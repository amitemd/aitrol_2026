<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Contabilidad\Infrastructure\ContabilizacionOperativa;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use App\Services\FacturaVenta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ComercialController
{
    public const SECCIONES_ARCHIVO = [
        'facturas' => 'documentos',
        'notas-de-credito' => 'notas-credito',
        'notas-de-debito' => 'notas-debito',
        'retenciones' => 'retenciones',
    ];

    public function __construct(private DocumentosComerciales $documentos) {}

    public function index(Request $request)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $filtros = $this->filtros($request, conPaginacion: $seccion !== 'reportes');
        if ($seccion === 'reportes') {
            $reporte = $this->reporte($modulo, $filtros);

            return $request->routeIs('api.*') ? response()->json(['data' => $reporte])
                : view('comercial::index', compact('modulo', 'seccion', 'filtros', 'reporte'));
        }

        $registros = $this->documentos->listar($modulo, $seccion, $filtros)->through($this->decimales(...));

        return $request->routeIs('api.*') ? response()->json($registros)
            : view('comercial::index', compact('modulo', 'seccion', 'filtros', 'registros'));
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

    public function crear(Request $request, ContextoOperativo $contexto, FacturaVenta $facturas, EmpresaActiva $empresa)
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

    public function show(Request $request)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $registro = $this->documentos->consultar($modulo, $seccion, (int) $request->route('registro'));
        $documento = $this->detalleDocumento($registro);

        return $request->routeIs('api.*') ? response()->json(['data' => $documento])
            : view('comercial::detalle', compact('modulo', 'seccion', 'documento'));
    }

    public function archivo(Request $request, ContextoOperativo $contexto): View|RedirectResponse
    {
        if ($contexto->resolverSucursalWeb() === null) {
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
        $documentos = $this->documentos->listarConDetalle($modulo, $seccion, $filtros)->through($this->detalleDocumento(...));

        return view('comercial::archivo-documentos', compact('documentos', 'modulo', 'directorio', 'ruta', 'titulo', 'tipoDocumento', 'filtros'));
    }

    private function detalleDocumento(stdClass $registro): array
    {
        $fiscal = json_decode($registro->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR);

        return $this->decimales($registro) + array_intersect_key($fiscal, [
            'clave_acceso' => true, 'ambiente' => true, 'forma_pago' => true, 'plazo_dias' => true, 'informacion_adicional' => true,
        ]);
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
                + (isset($fila->tipo_producto) ? [
                    'precio' => $fila->precio_venta === null ? null : (string) $fila->precio_venta,
                    'iva_codigo' => $fila->iva_codigo, 'tipo_producto' => $fila->tipo_producto,
                ] : [])),
            'pagination' => ['more' => $opciones->hasMorePages()],
        ]);
    }

    public function store(Request $request, FacturaVenta $facturas)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $vinculado = in_array($seccion, DocumentosComerciales::VINCULADOS, true);
        $retencion = $seccion === 'retenciones';
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
        if (isset($datos['informacion_adicional'])) {
            $datos['informacion_adicional'] = array_values(array_filter($datos['informacion_adicional'], fn (array $fila): bool => isset($fila['nombre'], $fila['valor'])));
        }
        foreach ($datos['lineas'] ?? [] as $posicion => $linea) {
            if (! isset($linea['id_producto']) && ! isset($linea['descripcion'])) {
                throw ValidationException::withMessages(['lineas.'.$posicion.'.descripcion' => 'Indica un concepto o selecciona un producto.']);
            }
        }
        $documento = $factura ? $facturas->registrar($datos) : $this->documentos->registrar($modulo, $seccion, $datos, creditoTotal: $creditoTotal);

        if ($factura && ! $request->expectsJson()) {
            return to_route('ventas.documentos.index')->with('status', 'Factura '.$documento->numero.' registrada en Aitrol.');
        }

        return $this->responder($request, $modulo, $seccion, $documento, 201, 'Documento registrado.');
    }

    public function trazabilidadVenta(int $registro): View
    {
        $factura = $this->documentos->consultar('ventas', 'documentos', $registro);
        $vinculados = $this->documentos->vinculadosVenta($registro);

        return view('comercial::trazabilidad-venta', compact('factura', 'vinculados'));
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
