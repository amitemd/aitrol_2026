<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\DocumentosComerciales;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use App\Services\FacturaVenta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ComercialController
{
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

        return view('comercial::formulario', compact('modulo', 'seccion', 'periodo', 'fecha'));
    }

    public function show(Request $request)
    {
        [$modulo, $seccion] = $this->seccion($request);
        $documento = $this->decimales($this->documentos->consultar($modulo, $seccion, (int) $request->route('registro')));

        return $request->routeIs('api.*') ? response()->json(['data' => $documento])
            : view('comercial::detalle', compact('modulo', 'seccion', 'documento'));
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
            unset($reglas['lineas.*.tasa']);
            $reglas = array_replace($reglas, [
                'numero' => ['required', 'regex:/\A(?!000)\d{3}-(?!000)\d{3}-(?!000000000)\d{9}\z/'],
                'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guayaquil')->toDateString()],
                'id_bodega' => ['prohibited'],
                'lineas.*' => ['required', 'array:id_producto,id_bodega,descripcion,cantidad,precio,iva_codigo,descuento'],
                'lineas.*.id_bodega' => ['nullable', 'integer', 'min:1'],
                'lineas.*.iva_codigo' => ['nullable', 'required_without:lineas.*.id_producto', Rule::in(array_keys(FacturaVenta::IVA))],
                'lineas.*.descuento' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,100000000000'],
                'forma_pago' => ['required', Rule::in(array_keys(FacturaVenta::PAGOS))],
                'plazo_dias' => ['nullable', 'integer', 'between:0,3650'],
                'aplicar_propina' => ['sometimes', 'boolean'],
                'borrador' => ['nullable', 'integer', 'min:1'],
                'frecuencia' => ['nullable', Rule::prohibitedIf($request->filled('borrador')), Rule::in(array_keys(FacturaVenta::FRECUENCIAS))],
                'primera_fecha' => ['nullable', 'required_with:frecuencia', 'exclude_without:frecuencia', 'date_format:Y-m-d', 'after:fecha', 'after_or_equal:'.now('America/Guayaquil')->toDateString()],
            ]);
        }

        $datos = $request->validate($reglas, [
            'numero.regex' => 'Ingresa establecimiento, punto de emisión y secuencial, por ejemplo 001-001-000000001.',
            'forma_pago.required' => 'Selecciona una forma de pago.',
            'lineas.*.iva_codigo.required' => 'Selecciona el tratamiento de IVA de cada línea.',
            'lineas.*.iva_codigo.required_without' => 'Selecciona el tratamiento de IVA del servicio manual.',
        ]);
        foreach ($datos['lineas'] as $posicion => $linea) {
            if (! isset($linea['id_producto']) && ! isset($linea['descripcion'])) {
                throw ValidationException::withMessages(['lineas.'.$posicion.'.descripcion' => 'Indica un concepto o selecciona un producto.']);
            }
        }
        $documento = $factura ? $facturas->registrar($datos) : $this->documentos->registrar($modulo, $seccion, $datos);

        if ($factura && ! $request->expectsJson()) {
            return to_route('ventas.documentos.index')->with('status', 'Factura registrada en Aitrol.');
        }

        return $this->responder($request, $modulo, $seccion, $documento, 201, 'Documento registrado.');
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
