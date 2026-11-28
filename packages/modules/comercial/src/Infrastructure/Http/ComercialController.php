<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Contabilidad\Infrastructure\ContabilizacionOperativa;
use Aitrol\Inventarios\Application\RepositorioInventario;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use Aitrol\Proveedores\Infrastructure\Persistence\EloquentProveedorRepository;
use App\Services\AutorizacionSri;
use App\Services\CatalogoRetenciones;
use App\Services\CobroFacturaVenta;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use App\Services\FacturaVenta;
use App\Services\FirmaSri;
use App\Services\PdfFacturaAutorizada;
use App\Services\RetencionElectronica;
use App\Services\XmlCompra;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final readonly class ComercialController
{
    public const SECCIONES_ARCHIVO = [
        'facturas' => 'documentos',
        'notas-de-credito' => 'notas-credito',
        'notas-de-debito' => 'notas-debito',
        'retenciones' => 'retenciones',
    ];

    public function __construct(private DocumentosComerciales $documentos) {}

    public function index(Request $request, CobroFacturaVenta $cobros)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $filtros = $this->filtros($request, conPaginacion: $seccion !== 'reportes');
        if ($seccion === 'reportes') {
            $reporte = $this->reporte($modulo, $filtros);

            return $request->routeIs('api.*') ? response()->json(['data' => $reporte])
                : view('comercial::index', compact('modulo', 'seccion', 'filtros', 'reporte'));
        }

        $api = $request->routeIs('api.*');
        $listaCompras = $modulo === 'compras' && $seccion === 'documentos';
        $registros = $this->documentos->listar($modulo, $seccion, $filtros, incluirSri: ! $api);
        $saldos = ! $api && $modulo === 'ventas' && $seccion === 'documentos' ? $cobros->saldos($registros->getCollection()) : collect();
        $registros->through($api ? $this->decimales(...) : fn (stdClass $registro): array => $this->detalleDocumento($registro, datosRespaldo: $listaCompras));

        return $api ? response()->json($registros)
            : view('comercial::index', compact('modulo', 'seccion', 'filtros', 'registros', 'saldos'));
    }

    public function exportar(Request $request)
    {
        $modulo = $request->route('modulo');
        $reporte = $this->reporte($modulo, $this->filtros($request));

        return response()->streamDownload(function () use ($modulo, $reporte): void {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, ['Tipo', 'Estado', 'Cantidad', 'Subtotal', 'Impuesto', 'Total'], ';', '"', '');
            foreach ($reporte['filas'] as $fila) {
                fputcsv($archivo, [
                    config('comercial.'.$modulo.'.secciones.'.$fila['tipo'].'.0'),
                    $fila['anulado'] ? 'Anulado' : 'Vigente',
                    $fila['cantidad'], $fila['subtotal'], $fila['impuesto'], $fila['total'],
                ], ';', '"', '');
            }
            fputcsv($archivo, []);
            foreach (['neto' => 'Importe neto', 'retenciones' => 'Retenciones vigentes', 'neto_menos_retenciones' => 'Neto menos retenciones'] as $campo => $etiqueta) {
                fputcsv($archivo, [$etiqueta, '', '', '', '', $reporte[$campo]], ';', '"', '');
            }
            fclose($archivo);
        }, 'reporte-'.$modulo.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function crear(Request $request, ContextoOperativo $contexto, FacturaVenta $facturas, EmpresaActiva $empresa, RepositorioInventario $inventario)
    {
        [$modulo, $seccion] = $this->seccion($request);

        $periodo = $contexto->periodo();
        $fecha = $periodo === $contexto->anioActual() ? now('America/Guayaquil')->toDateString() : $periodo.'-01-01';

        if ($modulo === 'ventas' && $seccion === 'documentos') {
            $borrador = $request->filled('borrador')
                ? ($facturas->pendientes()->where('p.id', $request->integer('borrador'))->first(['p.id', 'p.fecha', 'r.datos']) ?? abort(404))
                : null;
            $datos = $borrador ? json_decode($borrador->datos, true, flags: JSON_THROW_ON_ERROR) : [];
            $fecha = $borrador && (int) substr($borrador->fecha, 0, 4) === $periodo ? $borrador->fecha : $fecha;
            $cliente = $facturas->cliente((int) $request->old('id_tercero', $datos['id_tercero'] ?? 0));
            $lineas = array_values((array) $request->old('lineas', $datos['lineas'] ?? [[]]));
            $productos = DB::table('inv_productos')->where('id_empresa', $empresa->id())->where('activo', true)
                ->whereIn('id', array_filter(array_column($lineas, 'id_producto')))->get(['id', 'nombre', 'tipo_producto', 'iva_codigo'])->keyBy('id');
            $bodegas = DB::table('inv_bodegas')->where('id_empresa', $empresa->id())->where('id_sucursal', $contexto->sucursal()->id)
                ->where('activo', true)->orderBy('nombre')->pluck('nombre', 'id');

            return view('comercial::factura', compact('periodo', 'fecha', 'borrador', 'datos', 'cliente', 'lineas', 'productos', 'bodegas'));
        }

        if ($modulo === 'compras' && $seccion === 'documentos') {
            $bodegaPrincipal = $inventario->bodegaPrincipal();
            $token = $request->old('importacion_compra', $request->query('importacion'));
            $importacion = $this->importacionCompra($request, $token);
            $proveedor = DB::table('erp_proveedores as proveedor')->join('personas as persona', 'persona.id', '=', 'proveedor.id_persona')
                ->where('proveedor.id_empresa', $empresa->id())->where('proveedor.activo', true)
                ->whereNull('proveedor.deleted_at')->when($importacion !== null,
                    fn ($q) => $q->where('proveedor.identificacion', $importacion['ruc_emisor'])->where('proveedor.tipo_identificacion', 'ruc'),
                    fn ($q) => $q->where('proveedor.id', (int) $request->old('id_tercero')))
                ->select('proveedor.id', 'proveedor.identificacion', 'proveedor.nombre')
                ->selectRaw('COALESCE(persona.direccion, proveedor.direccion) as direccion, COALESCE(persona.email, proveedor.email) as email, COALESCE(persona.telefono, proveedor.telefono) as telefono, persona.celular')
                ->first();
            $lineas = array_map(fn ($linea): array => is_array($linea) ? array_filter($linea, 'is_scalar') : [],
                array_values((array) $request->old('lineas', $importacion['lineas'] ?? []))) ?: [[]];
            if ($importacion !== null) {
                foreach ($importacion['lineas'] as $indice => $linea) {
                    $lineas[$indice] = $linea + ['id_producto' => $lineas[$indice]['id_producto'] ?? null];
                }
                $lineas = array_slice($lineas, 0, count($importacion['lineas']));
            }
            $productos = DB::table('inv_productos')->where('id_empresa', $empresa->id())->where('activo', true)
                ->whereIn('id', array_filter(array_column($lineas, 'id_producto'), 'is_scalar'))->get(['id', 'codigo', 'nombre', 'tipo_producto'])->keyBy('id');

            $puedeCrearProducto = $importacion !== null && Gate::allows('permiso', ['inventarios.productos.index', 'escribir']);
            $unidadesProducto = $categoriasProducto = collect();
            if ($puedeCrearProducto) {
                $unidadesProducto = DB::table('inv_unidades')->where('id_empresa', $empresa->id())->where('activo', true)
                    ->orderBy('nombre')->get(['id', 'codigo', 'nombre']);
                $categoriasProducto = DB::table('inv_categorias')->where('id_empresa', $empresa->id())->where('activo', true)
                    ->orderBy('nombre')->pluck('nombre', 'id');
            }

            return view('comercial::compra', compact('periodo', 'fecha', 'bodegaPrincipal', 'proveedor', 'lineas', 'productos', 'importacion', 'token',
                'puedeCrearProducto', 'unidadesProducto', 'categoriasProducto'));
        }

        $idOrigen = $request->validate(['origen' => ['nullable', 'integer', 'min:1']])['origen'] ?? null;
        $origen = $idOrigen && in_array($seccion, DocumentosComerciales::VINCULADOS, true)
            ? $this->documentos->consultar($modulo, 'documentos', (int) $idOrigen) : null;
        $creditoTotal = $origen !== null && $modulo === 'ventas' && $seccion === 'notas-credito' && $request->boolean('total');
        if ($creditoTotal) {
            Gate::authorize('permiso', ['ventas.documentos.index', 'actualizar']);
        }
        abort_if($origen?->anulado_at !== null, 409, 'La factura de origen ya está anulada.');
        return view('comercial::formulario', compact('modulo', 'seccion', 'periodo', 'fecha', 'origen', 'creditoTotal'));
    }

    public function formularioRetencionRecibida(Request $request): View
    {
        $datos = $request->validate(['origen' => ['nullable', 'integer', 'min:1']]);
        $origen = isset($datos['origen']) ? $this->documentos->consultar('ventas', 'documentos', (int) $datos['origen']) : null;
        abort_if($origen?->anulado_at !== null, 409, 'La factura de origen ya está anulada.');

        return view('comercial::retencion-recibida', compact('origen'));
    }

    public function catalogoRetenciones(Request $request, CatalogoRetenciones $catalogo): JsonResponse
    {
        $filtros = $request->validate([
            'tipo' => ['required', 'in:renta,iva'], 'fecha' => ['required', 'date_format:Y-m-d'],
            'q' => ['nullable', 'string', 'max:80'], 'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $termino = trim($filtros['q'] ?? '');
        if ($termino === '') {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }
        $registros = $catalogo->vigentes($filtros['tipo'], $filtros['fecha'])
            ->where(fn ($q) => $q->whereLike('codigo', $termino.'%')->orWhereLike('descripcion', '%'.$termino.'%'))
            ->orderBy('codigo')->paginate(15);

        return response()->json([
            'results' => $registros->getCollection()->map(function (stdClass $fila): array {
                $tasa = $fila->porcentaje === null ? null : (string) (float) $fila->porcentaje;

                return [
                    'id' => $fila->codigo,
                    'text' => $fila->codigo.' · '.$fila->descripcion.' · '.($tasa === null ? 'Tarifa variable' : $tasa.' %'),
                    'porcentaje' => $tasa,
                ];
            }),
            'pagination' => ['more' => $registros->hasMorePages()],
        ]);
    }

    public function importarRetencion(Request $request, AutorizacionSri $sri, RetencionElectronica $retenciones): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_documento_origen' => ['nullable', 'integer', 'min:1'],
            'clave_acceso' => ['nullable', 'required_without:xml_retencion', 'prohibits:xml_retencion', 'regex:/\A[0-9]{8}07[0-9]{39}\z/'],
            'xml_retencion' => ['nullable', 'required_without:clave_acceso', 'file', 'extensions:xml', 'max:1900'],
        ], ['clave_acceso.regex' => 'Ingresa los 49 dígitos de la clave de una retención.']);
        $clave = $datos['clave_acceso'] ?? null;
        if ($clave !== null) {
            $resultado = $sri->buscar($clave);
            if ($resultado['estado'] !== 'AUTORIZADO' || empty($resultado['xml'])) {
                throw ValidationException::withMessages(['clave_acceso' => $resultado['mensajes'] ?: ['El SRI aún no devuelve una retención autorizada.']]);
            }
            $contenido = $resultado['xml'];
        } else {
            $contenido = $datos['xml_retencion']->get();
        }
        $retenciones->importar($contenido, isset($datos['id_documento_origen']) ? (int) $datos['id_documento_origen'] : null, $clave);

        return $request->expectsJson() ? response()->json(['message' => 'Retención recibida y vinculada a la venta.'])
            : to_route('ventas.retenciones.index')->with('status', 'Retención recibida y vinculada a la venta.');
    }

    public function descargarRetencion(Request $request, int $registro, AutorizacionSri $sri): StreamedResponse
    {
        $retencion = $this->documentos->consultar($request->route('modulo'), 'retenciones', $registro);
        $fiscal = json_decode($retencion->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
        $xml = $sri->autorizado($registro)?->xml_autorizado ?? $fiscal['xml_firmado'] ?? $fiscal['xml_retencion'] ?? abort(404);

        return response()->streamDownload(fn () => print($xml), 'retencion-'.$retencion->numero.'.xml', ['Content-Type' => 'application/xml']);
    }

    public function emitirRetencion(Request $request, int $registro, RetencionElectronica $retenciones, FirmaSri $firma, AutorizacionSri $sri): JsonResponse|RedirectResponse
    {
        $mensaje = $retenciones->emitir($registro, $firma, $sri);

        return $request->expectsJson() ? response()->json(['message' => $mensaje])
            : to_route('compras.retenciones.show', $registro)->with('status', $mensaje);
    }

    public function importarCompra(Request $request, EmpresaActiva $empresa, AutorizacionSri $sri, XmlCompra $lector, EloquentProveedorRepository $proveedores): RedirectResponse
    {
        $datos = $request->validate([
            'xml_compra' => ['required_without:clave_compra', 'prohibits:clave_compra', 'nullable', 'file', 'max:1024'],
            'clave_compra' => ['required_without:xml_compra', 'nullable', 'string', 'regex:/\A[0-9]{49}\z/'],
        ]);
        $desdeSri = isset($datos['clave_compra']);
        if ($desdeSri) {
            $clave = $datos['clave_compra'];
            $numero = substr($clave, 24, 3).'-'.substr($clave, 27, 3).'-'.substr($clave, 30, 9);
            if (substr($clave, 8, 2) === '01' && $this->documentos->compraRegistrada(substr($clave, 10, 13), $numero)) {
                return $this->compraDuplicada($numero);
            }
            $resultado = $sri->buscar($clave);
            if ($resultado['estado'] !== 'AUTORIZADO' || empty($resultado['xml'])) {
                throw ValidationException::withMessages([
                    'clave_compra' => 'No se obtuvo un XML autorizado del SRI. '.implode(' ', array_slice(
                        $resultado['mensajes'] ?: [AutorizacionSri::ESTADOS[$resultado['estado']].'.'], 0, 2,
                    )),
                ]);
            }
            $xml = $resultado['xml'];
        } else {
            $xml = $datos['xml_compra']->get();
        }
        $ruc = DB::table('erp_empresas')->where('id', $empresa->id())->value('ruc');
        $importacion = $lector->leer($xml, (string) $ruc);
        if ($this->documentos->compraRegistrada($importacion['ruc_emisor'], $importacion['numero'])) {
            return $this->compraDuplicada($importacion['numero']);
        }
        $proveedores->registrarDesdeFactura($importacion['ruc_emisor'], $importacion['proveedor'], $importacion['direccion_proveedor']);
        $importacion['fiscal']['fuente_compra'] = $desdeSri ? 'sri' : 'archivo';
        $token = (string) Str::uuid();
        $request->session()->put('xml_compra', [
            'token' => $token, 'empresa' => $empresa->id(), 'usuario' => $request->user()->id, 'vence' => now()->addMinutes(30)->timestamp,
            'datos' => $importacion,
        ]);

        return to_route('compras.documentos.create', ['importacion' => $token]);
    }

    private function compraDuplicada(string $numero): RedirectResponse
    {
        return to_route('compras.documentos.create')->withErrors([
            'compra_duplicada' => 'El comprobante '.$numero.' ya está registrado como compra de este proveedor.',
        ]);
    }

    public function descargarXmlCompra(int $registro): StreamedResponse
    {
        abort_unless(Gate::allows('permiso', ['compras.documentos.index', 'leer'])
            || Gate::allows('permiso', ['proveedores.documentos.facturas', 'leer']), 403);
        $compra = $this->documentos->consultar('compras', 'documentos', $registro);
        $fiscal = json_decode($compra->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
        $xml = $fiscal['xml_recibido'] ?? abort(404, 'Esta compra no tiene un XML adjunto.');

        return response()->streamDownload(fn () => print($xml), 'compra-'.$compra->id.'.xml', ['Content-Type' => 'application/xml']);
    }

    public function formularioRespaldoCompra(int $registro): View
    {
        $compra = $this->documentos->consultar('compras', 'documentos', $registro);
        $documento = $this->detalleDocumento($compra, datosRespaldo: true);
        abort_if($documento['tiene_respaldo'], 409, 'Esta compra ya tiene un archivo de respaldo.');

        return view('comercial::respaldo-compra', compact('documento'));
    }

    public function guardarRespaldoCompra(Request $request, int $registro, EmpresaActiva $empresa): JsonResponse|RedirectResponse
    {
        $this->documentos->consultar('compras', 'documentos', $registro);
        $archivo = $request->validate([
            'archivo' => ['required', 'file', 'mimetypes:application/pdf,image/jpeg,image/png,image/webp', 'max:10240'],
        ], [
            'archivo.mimetypes' => 'Elige un archivo PDF, JPG, PNG o WebP.',
            'archivo.max' => 'El archivo puede pesar hasta 10 MB.',
        ])['archivo'];
        $ruta = $archivo->store('compras/respaldo/'.$empresa->id(), 'local');
        abort_if($ruta === false, 500, 'No se pudo guardar el archivo de respaldo.');
        try {
            $this->documentos->adjuntarRespaldoCompra($registro, [
                'ruta' => $ruta, 'mime' => $archivo->getMimeType(),
            ]);
        } catch (Throwable $error) {
            Storage::disk('local')->delete($ruta);
            throw $error;
        }

        $mensaje = 'Archivo de respaldo guardado.';

        return $request->expectsJson() ? response()->json(['message' => $mensaje])
            : to_route('compras.documentos.index')->with('status', $mensaje);
    }

    public function archivoRespaldoCompra(Request $request, int $registro): StreamedResponse
    {
        $compra = $this->documentos->consultar('compras', 'documentos', $registro);
        $datos = json_decode($compra->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);
        $respaldo = $datos['respaldo_compra'] ?? abort(404);
        $disco = Storage::disk('local');
        abort_unless($disco->exists($respaldo['ruta']), 404, 'El archivo de respaldo no está disponible.');
        $nombre = 'compra-'.$registro.'.'.pathinfo($respaldo['ruta'], PATHINFO_EXTENSION);

        return $disco->response($respaldo['ruta'], $nombre, ['Content-Type' => $respaldo['mime']], $request->boolean('descargar') ? 'attachment' : 'inline');
    }

    private function importacionCompra(Request $request, mixed $token): ?array
    {
        $guardada = $request->session()->get('xml_compra');
        if (! is_string($token) || $guardada === null || $guardada['token'] !== $token
            || $guardada['empresa'] !== (int) $request->user()->id_empresa || $guardada['usuario'] !== $request->user()->id
            || $guardada['vence'] < now()->timestamp) {
            return null;
        }

        return $guardada['datos'];
    }

    public function show(Request $request, ContextoOperativo $contexto, CobroFacturaVenta $cobros)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $registro = $this->documentos->consultar($modulo, $seccion, (int) $request->route('registro'));
        if ($request->routeIs('api.*')) {
            return response()->json(['data' => $this->detalleDocumento($registro)]);
        }
        $visorFactura = $seccion === 'documentos';
        $documento = $this->detalleDocumento($registro, $visorFactura, $visorFactura && $modulo === 'compras');
        $datosVisor = $visorFactura ? $this->datosVisorFacturas($modulo, $contexto->sucursal(), collect([$registro])) : [];
        if ($visorFactura && $modulo === 'ventas' && ! empty($documento['clave_acceso'])) {
            $documento['estado_sri'] = DB::table('com_sri_consultas')->where('id_empresa', $registro->id_empresa)
                ->where('id_documento', $registro->id)->latest('id')->value('estado');
        }
        if ($visorFactura && $modulo === 'ventas') {
            $documento['saldo_cobro'] = $cobros->saldo($registro);
        }
        $verRespaldo = $visorFactura && $modulo === 'compras' && $request->boolean('respaldo');
        abort_if($verRespaldo && ! $documento['tiene_respaldo'], 404, 'Esta compra todavía no tiene un PDF o una imagen de respaldo.');

        return view('comercial::detalle', compact('modulo', 'seccion', 'documento', 'visorFactura', 'verRespaldo') + $datosVisor);
    }

    public function formularioCobro(int $registro, CobroFacturaVenta $cobros): View
    {
        $factura = $this->documentos->consultar('ventas', 'documentos', $registro);
        abort_if($factura->anulado_at !== null, 409, 'La factura está anulada.');
        $saldo = $cobros->saldo($factura);

        return view('comercial::cobro-factura', compact('factura', 'saldo'));
    }

    public function registrarCobro(Request $request, int $registro, CobroFacturaVenta $cobros): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_cuenta' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guayaquil')->toDateString()],
            'importe' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:100000000'],
            'referencia' => ['required', 'string', 'max:120'], 'clave_operacion' => ['required', 'uuid'],
        ]);
        $cobros->registrar($registro, $datos);
        $mensaje = 'Cobro registrado en Caja y bancos.';

        return $request->expectsJson() ? response()->json(['message' => $mensaje])
            : to_route('ventas.documentos.index')->with('status', $mensaje);
    }

    public function archivo(Request $request, ContextoOperativo $contexto): View|RedirectResponse
    {
        $sucursal = $contexto->resolverSucursalWeb();
        if ($sucursal === null) {
            return to_route('configuracion.contexto.index');
        }
        [$modulo, $seccion] = $this->seccion($request);
        $ruta = $request->route()->getName();
        [$directorio] = explode('.', $ruta);
        $titulo = config($directorio.'.secciones')[$ruta];
        $tipoDocumento = match ($seccion) {
            'documentos' => $modulo === 'ventas' ? 'Factura' : 'Factura de compra',
            'notas-credito' => 'Nota de crédito',
            'notas-debito' => 'Nota de débito',
            'retenciones' => 'Retención',
        };
        $filtros = $this->filtros($request, conPaginacion: true);
        $documentos = $this->documentos->listarConDetalle($modulo, $seccion, $filtros);
        $visorFactura = $seccion === 'documentos';
        $datosVisor = $visorFactura ? $this->datosVisorFacturas($modulo, $sucursal, $documentos->getCollection()) : [];
        $documentos->through(fn (stdClass $registro): array => $this->detalleDocumento($registro, $visorFactura, $visorFactura && $modulo === 'compras'));

        return view('comercial::archivo-documentos', compact(
            'documentos', 'modulo', 'directorio', 'ruta', 'titulo', 'tipoDocumento', 'filtros', 'visorFactura',
        ) + $datosVisor);
    }

    private function datosVisorFacturas(string $modulo, stdClass $sucursal, Collection $registros): array
    {
        $empresaFactura = null;
        $logoFactura = null;
        $codigosProductos = collect();
        $proveedoresFactura = collect();
        if ($registros->isNotEmpty()) {
            $empresaId = $sucursal->id_empresa;
            $empresaFactura = DB::table('erp_empresas as empresa')
                ->leftJoin('erp_identidad_empresas as identidad', 'identidad.id_empresa', '=', 'empresa.id')
                ->where('empresa.id', $empresaId)
                ->first(['empresa.razon_social', 'empresa.ruc', 'empresa.direccion', 'empresa.telefono', 'identidad.logo_mime', 'identidad.logo']);
            if ($modulo === 'ventas' && $empresaFactura?->logo && in_array($empresaFactura->logo_mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                $logoFactura = 'data:'.$empresaFactura->logo_mime.';base64,'.$empresaFactura->logo;
            }
            $idsProductos = $registros->flatMap(fn (stdClass $factura) => $factura->lineas->pluck('id_producto'))
                ->filter()->unique();
            if ($idsProductos->isNotEmpty()) {
                $codigosProductos = DB::table('inv_productos')->where('id_empresa', $empresaId)
                    ->whereIn('id', $idsProductos)->pluck('codigo', 'id');
            }
            if ($modulo === 'compras') {
                $proveedoresFactura = DB::table('erp_proveedores')->where('id_empresa', $empresaId)
                    ->whereIn('id', $registros->pluck('emisor')->filter()->unique())
                    ->get(['id', 'identificacion', 'nombre', 'direccion', 'telefono'])->keyBy('id');
            }
        }

        return compact('empresaFactura', 'sucursal', 'logoFactura', 'codigosProductos', 'proveedoresFactura');
    }

    private function detalleDocumento(stdClass $registro, bool $visorFactura = false, bool $datosRespaldo = false): array
    {
        $fiscal = json_decode($registro->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);

        $campos = [
            'clave_acceso' => true, 'ambiente' => true, 'forma_pago' => true, 'plazo_dias' => true, 'informacion_adicional' => true,
            'pagos' => true, 'fuente_compra' => true, 'estado_retencion' => true, 'recibida_sri' => true,
        ];
        if ($visorFactura) {
            $campos += ['emisor' => true, 'cliente' => true];
        }

        $documento = $this->decimales($registro) + array_intersect_key($fiscal, $campos);
        if ($datosRespaldo) {
            $documento['tiene_xml'] = ! empty($fiscal['xml_recibido']);
            $documento['tiene_respaldo'] = ! empty($fiscal['respaldo_compra']);
            $documento['mime_respaldo'] = $fiscal['respaldo_compra']['mime'] ?? null;
            if ($visorFactura) {
                $documento['id_proveedor'] = $registro->emisor;
                if ($documento['tiene_xml']) {
                    $xml = AutorizacionSri::leerXml($fiscal['xml_recibido']);
                    if ($xml?->getName() === 'autorizacion') {
                        $xml = AutorizacionSri::leerXml((string) $xml->comprobante);
                    }
                    if ($xml?->getName() === 'factura') {
                        $emisor = $xml->infoTributaria;
                        $documento['emisor'] = [
                            'razon_social' => (string) $emisor->razonSocial,
                            'ruc' => (string) $emisor->ruc,
                            'direccion' => (string) $emisor->dirMatriz,
                            'sucursal' => (string) $xml->infoFactura->dirEstablecimiento,
                        ];
                        foreach ($xml->infoAdicional->campoAdicional ?? [] as $campo) {
                            $documento['informacion_adicional'][] = [
                                'nombre' => (string) $campo['nombre'], 'valor' => (string) $campo,
                            ];
                        }
                    }
                }
            }
        }

        return $documento;
    }

    public function logSri(int $registro, AutorizacionSri $sri): View
    {
        $factura = $this->facturaSri($registro);
        $identidad = $sri->identidad($factura->datos_factura) ?? abort(404);
        $consultas = $sri->consultas($registro)->latest('id')->paginate(20, ['estado', 'mensajes', 'created_at']);
        $estado = $consultas->currentPage() === 1 ? $consultas->first()?->estado : $sri->consultas($registro)->latest('id')->value('estado');

        return view('comercial::log-sri', compact('factura', 'identidad', 'consultas', 'estado'));
    }

    public function consultarSri(int $registro, AutorizacionSri $sri): RedirectResponse
    {
        $factura = $this->facturaSri($registro);
        $identidad = $sri->identidad($factura->datos_factura) ?? abort(404);
        $estado = $sri->consultar($registro, $identidad['clave']);

        return to_route('ventas.documentos.sri.log', $registro)->with('status', AutorizacionSri::ESTADOS[$estado]);
    }

    public function descargarSri(int $registro, string $formato, AutorizacionSri $sri, PdfFacturaAutorizada $pdf): StreamedResponse
    {
        $factura = $this->facturaSri($registro);
        $identidad = $sri->identidad($factura->datos_factura) ?? abort(404);
        $autorizacion = $sri->autorizado($registro) ?? abort(404, 'El comprobante aún no tiene autorización confirmada.');
        $contenido = $formato === 'pdf' ? $pdf->generar($autorizacion->xml_autorizado) : $autorizacion->xml_autorizado;

        return response()->streamDownload(fn () => print($contenido), 'factura-'.$identidad['clave'].'.'.$formato, [
            'Content-Type' => $formato === 'pdf' ? 'application/pdf' : 'application/xml',
        ]);
    }

    private function facturaSri(int $id): stdClass
    {
        abort_unless(Gate::allows('permiso', ['ventas.documentos.index', 'leer'])
            || Gate::allows('permiso', ['clientes.documentos.facturas', 'leer']), 403);

        return $this->documentos->consultar('ventas', 'documentos', $id);
    }

    public function contactoFactura(int $cliente, FacturaVenta $facturas): JsonResponse
    {
        $registro = $facturas->cliente($cliente) ?? abort(404);

        return response()->json([
            'direccion' => $registro->direccion, 'telefono' => $registro->telefono, 'email' => $registro->email,
        ])->header('Cache-Control', 'no-store');
    }

    public function actualizarContactoFactura(Request $request, int $cliente, FacturaVenta $facturas): JsonResponse
    {
        $contacto = $request->validate([
            'direccion' => ['present', 'nullable', 'string', 'max:255'],
            'telefono' => ['present', 'nullable', 'string', 'max:30'],
            'email' => ['present', 'nullable', 'string', 'email:filter', 'max:254'],
        ], ['email.email' => 'Escribe un correo electrónico válido.']);
        $facturas->actualizarContacto($cliente, $contacto);

        return response()->json(['message' => 'Datos de contacto actualizados.']);
    }

    public function recurrentes(FacturaVenta $facturas): View
    {
        $pendientes = $facturas->pendientes()->orderBy('p.fecha')
            ->paginate(20, ['p.id', 'p.fecha', 'r.frecuencia'], 'borradores');
        $recurrencias = $facturas->recurrencias()
            ->join('com_documentos as origen', 'origen.id', '=', 'com_recurrencias.id_documento')
            ->orderByDesc('com_recurrencias.activa')->orderBy('com_recurrencias.proxima_fecha')
            ->paginate(20, ['com_recurrencias.*', 'origen.numero'], 'series');

        return view('comercial::recurrentes', compact('pendientes', 'recurrencias'));
    }

    public function estadoRecurrencia(Request $request, FacturaVenta $facturas, int $recurrencia): RedirectResponse
    {
        $request->validate(['activa' => ['required', 'boolean']]);
        $serie = $facturas->recurrencias()->where('id', $recurrencia);
        abort_unless((clone $serie)->exists(), 404);
        $serie->update(['activa' => $request->boolean('activa'), 'updated_at' => now()]);

        return to_route('ventas.facturas-recurrentes.index')->with('status', 'Recurrencia actualizada.');
    }

    public function opciones(Request $request)
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'between:1,100000'], 'factura' => ['nullable', 'boolean']]);
        $opciones = $this->documentos->opciones($request->route('modulo'), $request->route('catalogo'), $datos['q'] ?? null, (bool) ($datos['factura'] ?? false));

        return response()->json([
            'results' => $opciones->getCollection()->map(fn (stdClass $fila) => ['id' => $fila->id, 'text' => $fila->codigo.' · '.$fila->texto]
                + (isset($fila->tipo_producto) ? ['tipo_producto' => $fila->tipo_producto] : [])
                + (property_exists($fila, 'precio_venta') ? [
                    'precio' => $fila->precio_venta === null ? null : (string) $fila->precio_venta,
                    'iva_codigo' => $fila->iva_codigo,
                ] : [])),
            'pagination' => ['more' => $opciones->hasMorePages()],
        ]);
    }

    public function store(Request $request, FacturaVenta $facturas, RetencionElectronica $retenciones, CatalogoRetenciones $catalogo)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $vinculado = in_array($seccion, DocumentosComerciales::VINCULADOS, true);
        $retencion = $seccion === 'retenciones';
        $retencionElectronica = $retencion && $modulo === 'compras' && ! $request->routeIs('api.*');
        $compra = $modulo === 'compras' && $seccion === 'documentos';
        $compraWeb = $compra && ! $request->routeIs('api.*');
        $tokenCompra = $compraWeb ? $request->input('importacion_compra') : null;
        $importacion = $compraWeb ? $this->importacionCompra($request, $tokenCompra) : null;
        if ($tokenCompra !== null && $importacion === null) {
            throw ValidationException::withMessages(['importacion_compra' => 'La importación venció. Vuelve a cargar el XML o consulta el SRI.']);
        }
        $creditoTotal = $modulo === 'ventas' && $seccion === 'notas-credito' && ! $request->routeIs('api.*') && $request->boolean('credito_total');
        if ($creditoTotal) {
            Gate::authorize('permiso', ['ventas.documentos.index', 'actualizar']);
        }
        $reglas = [
            'numero' => ['required', 'string', 'max:60'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'observacion' => ['nullable', 'string', 'max:500'],
            'lineas' => ['required', 'list', 'min:1', 'max:100'],
            'lineas.*' => ['required', 'array:id_producto,descripcion,cantidad,precio,tasa'],
            'lineas.*.cantidad' => ['required', 'numeric', 'decimal:0,3', 'min:0.001', 'max:1000000'],
            'lineas.*.precio' => ['required', 'numeric', 'decimal:0,4', 'min:0.0001', 'max:100000'],
            'lineas.*.tasa' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:100'],
            'lineas.*.descripcion' => ['nullable', 'string', 'max:255'],
        ];
        if ($vinculado) {
            $reglas['id_documento_origen'] = ['required', 'integer', 'min:1'];
            $reglas['lineas.*.id_producto'] = ['prohibited'];
        } else {
            $reglas['id_tercero'] = ['required', 'integer', 'min:1'];
            $reglas['lineas.*.id_producto'] = ['nullable', 'integer', 'min:1'];
        }
        if ($seccion === 'documentos') {
            $reglas['id_bodega'] = ['nullable', 'integer', 'min:1'];
        }
        if ($compra) {
            $reglas['forma_pago'] = [$compraWeb && empty($importacion['fiscal']['pagos']) ? 'required' : 'nullable', Rule::in(array_keys(FacturaVenta::PAGOS))];
            $reglas['lineas.*'] = ['required', 'array:id_producto,descripcion,cantidad,precio,tasa,descuento'];
            $reglas['lineas.*.descuento'] = ['nullable', 'numeric', 'decimal:0,2', 'between:0,100000000000'];
        }
        if ($retencion) {
            $reglas = array_replace(array_diff_key($reglas, array_flip(['lineas.*.cantidad', 'lineas.*.precio', 'lineas.*.tasa', 'lineas.*.id_producto'])), [
                'lineas.*' => ['required', 'array:descripcion,tipo_retencion,codigo_retencion,base_retencion,porcentaje_retencion'],
                'lineas.*.descripcion' => ['required', 'string', 'max:255'],
                'lineas.*.tipo_retencion' => ['required', 'in:renta,iva,otra'],
                'lineas.*.codigo_retencion' => ['required', 'string', 'max:32'],
                'lineas.*.base_retencion' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:100000000'],
                'lineas.*.porcentaje_retencion' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:100'],
            ]);
        }
        if ($retencionElectronica) {
            unset($reglas['numero'], $reglas['lineas.*.descripcion']);
            $reglas['fecha'][] = 'before_or_equal:'.now('America/Guayaquil')->toDateString();
            $reglas['lineas.*'] = ['required', 'array:tipo_retencion,codigo_retencion,base_retencion,porcentaje_retencion'];
            $reglas['lineas.*.tipo_retencion'] = ['required', 'in:renta,iva'];
            $reglas['lineas.*.codigo_retencion'] = ['required', 'regex:/\A[0-9A-Z]{1,5}\z/'];
            $reglas['lineas.*.porcentaje_retencion'] = ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100'];
            $reglas['codigo_sustento'] = ['required', Rule::in(array_keys(RetencionElectronica::SUSTENTOS))];
            $reglas['parte_relacionada'] = ['required', 'in:SI,NO'];
            $reglas['iva_cero'] = ['nullable', 'in:0,6,7'];
            $reglas['forma_pago'] = ['nullable', Rule::in(array_keys(FacturaVenta::PAGOS))];
        }

        $factura = $modulo === 'ventas' && $seccion === 'documentos' && ! $request->routeIs('api.*');
        if ($factura) {
            unset($reglas['numero'], $reglas['lineas.*.tasa']);
            $reglas = array_replace($reglas, [
                'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guayaquil')->toDateString()],
                'id_bodega' => ['prohibited'],
                'lineas.*' => ['required', 'array:id_producto,id_bodega,descripcion,cantidad,precio,iva_codigo,descuento'],
                'lineas.*.id_bodega' => ['nullable', 'integer', 'min:1'],
                'lineas.*.iva_codigo' => ['nullable', 'required_without:lineas.*.id_producto', Rule::in(array_keys(FacturaVenta::IVA))],
                'lineas.*.descuento' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,100000000000'],
                'forma_pago' => ['required', Rule::in(array_keys(FacturaVenta::PAGOS))],
                'plazo_dias' => ['nullable', 'integer', 'between:0,3650'],
                'aplicar_propina' => ['sometimes', 'boolean'],
                'informacion_adicional' => ['sometimes', 'list', 'max:5'],
                'informacion_adicional.*' => ['array:nombre,valor'],
                'informacion_adicional.*.nombre' => ['required_with:informacion_adicional.*.valor', 'nullable', 'string', 'max:299'],
                'informacion_adicional.*.valor' => ['required_with:informacion_adicional.*.nombre', 'nullable', 'string', 'max:299'],
                'borrador' => ['nullable', 'integer', 'min:1'],
                'frecuencia' => ['nullable', Rule::prohibitedIf($request->filled('borrador')), Rule::in(array_keys(FacturaVenta::FRECUENCIAS))],
                'primera_fecha' => ['nullable', 'required_with:frecuencia', 'exclude_without:frecuencia', 'date_format:Y-m-d', 'after:fecha', 'after_or_equal:'.now('America/Guayaquil')->toDateString()],
            ]);
        }
        if ($creditoTotal) {
            $reglas = array_filter($reglas, fn (string $campo): bool => ! str_starts_with($campo, 'lineas'), ARRAY_FILTER_USE_KEY);
            $reglas['observacion'] = ['required', 'string', 'max:500'];
            $reglas['fecha'][] = 'before_or_equal:'.now('America/Guayaquil')->toDateString();
        }

        $datos = $request->validate($reglas, [
            'forma_pago.required' => 'Selecciona una forma de pago.',
            'lineas.*.iva_codigo.required' => 'Selecciona el tratamiento de IVA de cada línea.',
            'lineas.*.iva_codigo.required_without' => 'Selecciona el tratamiento de IVA del servicio manual.',
        ]);
        if ($retencionElectronica) {
            $catalogo->verificar($datos['lineas'], $datos['fecha']);
            $retenciones->generar($datos);
            $mensaje = 'Retención generada. El XML está pendiente de firma y envío al SRI.';

            return $request->expectsJson() ? response()->json(['message' => $mensaje])
                : to_route('compras.retenciones.index')->with('status', $mensaje);
        }
        if ($compra) {
            if ($importacion !== null) {
                $proveedor = DB::table('erp_proveedores')->where('id_empresa', $request->user()->id_empresa)->where('id', $datos['id_tercero'])
                    ->where('activo', true)->whereNull('deleted_at')->where('tipo_identificacion', 'ruc')
                    ->where('identificacion', $importacion['ruc_emisor'])->exists();
                if (! $proveedor) {
                    throw ValidationException::withMessages(['id_tercero' => 'Selecciona el proveedor que corresponde al RUC del XML.']);
                }
                if (count($datos['lineas']) !== count($importacion['lineas'])) {
                    throw ValidationException::withMessages(['lineas' => 'Conserva todas las líneas de la factura importada.']);
                }
                foreach ($importacion['lineas'] as $indice => $linea) {
                    $datos['lineas'][$indice] = $linea + ['id_producto' => $datos['lineas'][$indice]['id_producto'] ?? null];
                }
                $datos['numero'] = $importacion['numero'];
                $datos['fecha'] = $importacion['fecha'];
            }
            $datos['datos_compra'] = $importacion['fiscal'] ?? [];
            if (empty($datos['datos_compra']['pagos']) && isset($datos['forma_pago'])) {
                $datos['datos_compra']['forma_pago'] = (string) $datos['forma_pago'];
            }
        }
        if (isset($datos['informacion_adicional'])) {
            $datos['informacion_adicional'] = array_values(array_filter($datos['informacion_adicional'], fn (array $fila): bool => isset($fila['nombre'], $fila['valor'])));
        }
        foreach ($datos['lineas'] ?? [] as $posicion => $linea) {
            if (! isset($linea['id_producto']) && ! isset($linea['descripcion'])) {
                throw ValidationException::withMessages(['lineas.'.$posicion.'.descripcion' => 'Indica un concepto o selecciona un producto.']);
            }
        }
        try {
            $documento = $factura ? $facturas->registrar($datos) : $this->documentos->registrar($modulo, $seccion, $datos, creditoTotal: $creditoTotal);
        } catch (HttpException $error) {
            if ($error->getStatusCode() !== 409 || $importacion === null
                || ! $this->documentos->compraRegistrada($importacion['ruc_emisor'], $importacion['numero'])) {
                throw $error;
            }

            $request->session()->forget('xml_compra');

            return $this->compraDuplicada($importacion['numero']);
        }
        if ($importacion !== null) {
            $request->session()->forget('xml_compra');
        }

        if ($factura && ! $request->expectsJson()) {
            return to_route('ventas.documentos.index')->with('status', 'Factura '.$documento->numero.' registrada en Aitrol.');
        }

        return $this->responder($request, $modulo, $seccion, $documento, 201, 'Documento registrado.');
    }

    public function trazabilidad(Request $request, ContextoOperativo $contexto, CobroFacturaVenta $cobros, int $registro): View
    {
        $modulo = $request->route('modulo');
        $factura = $this->documentos->consultar($modulo, 'documentos', $registro);
        $vinculados = $this->documentos->vinculados($modulo, $registro);
        $partidasInventario = $this->documentos->partidasDeInventario(collect([$factura])->concat($vinculados));
        $productosTrazabilidad = $this->documentos->tiposProductosTrazabilidad($factura->lineas);
        $periodo = $contexto->periodo();
        $movimientosCobro = $modulo === 'ventas' ? $cobros->movimientos($factura->id) : collect();
        $saldoCobro = $modulo === 'ventas' ? $cobros->saldo($factura) : null;

        return view('comercial::trazabilidad-venta', compact(
            'factura', 'vinculados', 'partidasInventario', 'productosTrazabilidad', 'modulo', 'periodo', 'movimientosCobro', 'saldoCobro',
        ));
    }

    public function contabilizarVenta(int $registro, ContabilizacionOperativa $contabilidad): JsonResponse|RedirectResponse
    {
        $this->documentos->contabilizarVenta($registro, $contabilidad);

        return request()->expectsJson() ? response()->json(['message' => 'Asiento de la factura registrado.'])
            : to_route('ventas.documentos.index')->with('status', 'Asiento de la factura registrado.');
    }

    public function formularioAnulacion(Request $request)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $documento = $this->documentos->consultar($modulo, $seccion, (int) $request->route('registro'));
        abort_if($documento->anulado_at !== null, 409, 'El documento ya está anulado.');

        return view('comercial::anular', compact('modulo', 'seccion', 'documento'));
    }

    public function anular(Request $request)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:250']]);
        $documento = $this->documentos->anular($modulo, $seccion, (int) $request->route('registro'), $datos['motivo']);

        return $this->responder($request, $modulo, $seccion, $documento, 200, 'Documento anulado.');
    }

    private function seccion(Request $request): array
    {
        return [$request->route('modulo'), $request->route('seccion')];
    }

    private function filtros(Request $request, bool $conPaginacion = false): array
    {
        $reglas = ['q' => ['nullable', 'string', 'max:120'], 'desde' => ['nullable', 'date_format:Y-m-d'], 'hasta' => ['nullable', 'date_format:Y-m-d']];
        if ($request->filled('desde')) $reglas['hasta'][] = 'after_or_equal:desde';
        if ($conPaginacion) {
            $reglas['per_page'] = ['nullable', 'integer', 'between:1,100'];
            $reglas['page'] = ['nullable', 'integer', 'between:1,100000'];
        }
        $filtros = array_filter($request->validate($reglas), fn ($valor) => $valor !== null);
        unset($filtros['page']);

        return $filtros;
    }

    private function reporte(string $modulo, array $filtros): array
    {
        $reporte = $this->documentos->reporte($modulo, $filtros);
        $reporte['filas'] = $reporte['filas']->map($this->decimales(...));
        foreach (['neto', 'retenciones', 'neto_menos_retenciones'] as $campo) {
            $reporte[$campo] = Cantidad::decimal($reporte[$campo], 2);
        }

        return $reporte;
    }

    private function decimales(stdClass $registro): array
    {
        $datos = (array) $registro;
        unset($datos['emisor'], $datos['datos_factura']);
        foreach (['subtotal' => 2, 'impuesto' => 2, 'propina' => 2, 'total' => 2, 'precio' => 4, 'tasa' => 2, 'base_retencion' => 2, 'porcentaje_retencion' => 2] as $campo => $decimales) {
            if (isset($datos[$campo])) $datos[$campo] = Cantidad::decimal((int) $datos[$campo], $decimales);
        }
        if (isset($datos['id_documento'])) $datos['cantidad'] = Cantidad::decimal((int) $datos['cantidad'], 3);
        if (isset($datos['anulado'])) $datos['anulado'] = (bool) $datos['anulado'];
        if (isset($datos['lineas'])) $datos['lineas'] = $datos['lineas']->map($this->decimales(...));

        return $datos;
    }

    private function responder(Request $request, string $modulo, string $seccion, stdClass $documento, int $estado, string $mensaje)
    {
        if ($request->routeIs('api.*')) {
            $cabeceras = $estado === 201 ? ['Location' => route('api.v1.'.$modulo.'.'.$seccion.'.show', $documento->id)] : [];

            return response()->json(['data' => $this->decimales($documento)], $estado, $cabeceras);
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensaje], $estado);
        }

        return redirect()->route($modulo.'.'.$seccion.'.index')->with('status', $mensaje);
    }
}
