<?php

namespace Aitrol\Tributario\Infrastructure\Http;

use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Aitrol\Tributario\Application\PreparacionTributaria;
use Aitrol\Tributario\Domain\PeriodoFiscal;
use Aitrol\Tributario\Infrastructure\Persistence\TributarioSql;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;
use Throwable;

final readonly class TributarioController
{
    public function __construct(private TributarioSql $datos, private PreparacionTributaria $preparacion, private EmpresaActiva $empresa) {}

    public function index(Request $request, ContextoOperativo $contexto)
    {
        $seccion = $request->route('seccion');
        $filtros = $this->filtros($request);
        $consulta = match ($seccion) {
            'documentos' => $this->dependientes('tr_documentos'), 'procesos' => $this->dependientes('tr_eventos'),
            default => $this->datos->tabla('tr_declaraciones')->select('id', 'id_empresa', 'formato', 'desde', 'hasta', 'estado',
                'referencia_externa', 'fecha_externa', 'created_at', 'updated_at')
                ->when(in_array($seccion, config('tributario.formatos'), true), fn ($q) => $q->where('formato', $seccion))
                ->when($filtros['estado'] ?? null, fn ($q, $estado) => $q->where('estado', $estado))->orderByDesc('desde'),
        };
        $tabla = ['documentos' => 'tr_documentos', 'procesos' => 'tr_eventos'][$seccion] ?? 'tr_declaraciones';
        $consulta->whereIn($tabla === 'tr_declaraciones' ? 'formato' : 'd.formato', $this->formatosPermitidos());
        $contexto->aplicarPeriodo($consulta, $tabla === 'tr_declaraciones' ? 'desde' : 'd.desde');
        $consulta->when($filtros['ejercicio'] ?? null, fn ($q, $anio) => $q->whereYear($tabla === 'tr_declaraciones' ? 'desde' : 'd.desde', $anio));
        $registros = $consulta->orderByDesc($tabla.'.id')->paginate($filtros['per_page'] ?? 20)->appends($filtros)->through($this->presentar(...));

        return $request->routeIs('api.*') ? response()->json($registros) : view('tributario::index', compact('seccion', 'filtros', 'registros'));
    }

    public function nomina(Request $request, ContextoOperativo $contexto)
    {
        $seccion = 'nomina';
        $filtros = $this->filtros($request);
        $registros = $this->datos->tabla('tr_nomina')
            ->when($contexto->periodo(), fn ($q, $anio) => $q->where('ejercicio', $anio))
            ->when($filtros['ejercicio'] ?? null, fn ($q, $anio) => $q->where('ejercicio', $anio))
            ->orderByDesc('ejercicio')->orderBy('nombre')->paginate($filtros['per_page'] ?? 20)->appends($filtros)->through($this->presentar(...));

        return $request->routeIs('api.*') ? response()->json($registros) : view('tributario::index', compact('seccion', 'filtros', 'registros'));
    }

    public function guardarNomina(Request $request)
    {
        $importe = ['required', 'numeric', 'decimal:0,2', 'between:0,1000000000'];
        $datos = $request->validate([
            'ejercicio' => ['required', 'integer', 'between:1900,'.now()->year],
            'identificacion' => ['required', 'string', 'max:30', 'regex:/^[\pL\d.-]+$/u'], 'nombre' => ['required', 'string', 'max:180'],
            'ingresos_gravados' => $importe, 'ingresos_exentos' => $importe, 'aporte_personal' => $importe, 'retencion' => $importe,
        ]);
        $datos['identificacion'] = Str::upper($datos['identificacion']);
        foreach (['ingresos_gravados', 'ingresos_exentos', 'aporte_personal', 'retencion'] as $campo) $datos[$campo] = Decimal::unidades($datos[$campo], 2);
        $fila = $this->preparacion->guardarNomina($datos);

        return $this->respuesta($request, 'nomina', $fila, 200, 'Datos de nómina guardados.');
    }

    public function opciones(Request $request)
    {
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'between:1,100000']]);
        $query = $this->datos->tabla('tr_declaraciones')->whereIn('formato', $this->formatosPermitidos());
        if (isset($filtros['q'])) {
            $q = '%'.$filtros['q'].'%';
            $query->where(fn (Builder $b) => $b->whereLike('formato', $q)->orWhereLike('desde', $q));
        }
        $filas = $query->orderByDesc('desde')->orderByDesc('id')->paginate(20);

        return response()->json([
            'results' => $filas->getCollection()->map(fn ($d) => ['id' => $d->id, 'text' => config('tributario.secciones.'.$d->formato.'.0').' · '.$d->desde.' a '.$d->hasta]),
            'pagination' => ['more' => $filas->hasMorePages()],
        ]);
    }

    public function formulario(Request $request)
    {
        $seccion = $request->route('seccion');
        $accion = $request->route('accion');
        $registro = $accion ? $this->registro($seccion, (int) $request->route('registro')) : null;

        return view('tributario::formulario', compact('seccion', 'accion', 'registro'));
    }

    public function guardar(Request $request)
    {
        $seccion = $request->route('seccion');
        if ($seccion === 'documentos') return $this->guardarArchivo($request);
        $datos = $request->validate([
            'ejercicio' => ['required', 'integer', 'between:1900,'.now()->year], 'mes' => [$seccion === 'formulario-107' ? 'nullable' : 'required', 'integer', 'between:1,12'],
            'semestral' => ['nullable', 'boolean'], 'base_nomina' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,1000000000'],
            'retencion_nomina' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,1000000000'],
        ]);
        $semestral = $seccion === 'formulario-104' && (bool) ($datos['semestral'] ?? false);
        $mes = isset($datos['mes']) ? (int) $datos['mes'] : null;
        if ($semestral && ! in_array($mes, [1, 7], true)) throw ValidationException::withMessages(['mes' => 'Para un semestre selecciona enero o julio.']);
        $periodo = new PeriodoFiscal($seccion, (int) $datos['ejercicio'], $mes, $semestral);
        if ($periodo->desde > now()->toDateString()) throw ValidationException::withMessages(['mes' => 'El período aún no inicia.']);
        foreach (['base_nomina', 'retencion_nomina'] as $campo) $datos[$campo] = Decimal::unidades($datos[$campo] ?? 0, 2);
        $fila = $this->preparacion->preparar($periodo, $datos);

        return $this->respuesta($request, $seccion, $fila, 200, 'Papel de trabajo preparado.');
    }

    public function show(Request $request)
    {
        $seccion = $request->route('seccion');
        $registro = $this->presentar($this->registro($seccion, (int) $request->route('registro')));
        $columnas = $this->columnas($registro['formato']);

        return $request->routeIs('api.*') ? response()->json(['data' => $registro])->header('Cache-Control', 'no-store')
            : view('tributario::detalle', compact('seccion', 'registro', 'columnas'));
    }

    public function transicion(Request $request)
    {
        $s = $request->route('seccion');
        $accion = $request->route('accion');
        $id = (int) $request->route('registro');
        $this->registro($s, $id);
        if ($s === 'declaraciones') {
            Gate::authorize('permiso', ['tributario.'.$this->datos->declaracion($id)->formato.'.index', 'actualizar']);
        }
        $reglas = $accion === 'registrar-entrega' ? [
            'referencia_externa' => ['required', 'string', 'max:100'], 'fecha_externa' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ] : ['motivo' => ['required', 'string', 'max:250']];
        $fila = $this->preparacion->transicion($id, $accion, $request->validate($reglas));

        return $this->respuesta($request, $s, $fila, 200, 'Cambio registrado.');
    }

    public function exportar(Request $request)
    {
        $registro = $this->presentar($this->registro($request->route('seccion'), (int) $request->route('registro')));
        $columnas = $this->columnas($registro['formato']);

        return response()->streamDownload(function () use ($registro, $columnas): void {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, array_values($columnas), ';', '"', '');
            foreach ($registro['detalle'] as $fila) {
                $salida = [];
                foreach ($columnas as $campo => $titulo) {
                    $valor = $fila[$campo] ?? '';
                    $texto = in_array($campo, ['codigo', 'numero', 'tercero', 'identificacion', 'tipo_identificacion', 'documento_origen', 'nombre'], true);
                    $salida[] = $texto && preg_match('/^[\x00-\x20]*[=+@-]|^[\t\r\n]/', (string) $valor) ? "'".$valor : $valor;
                }
                fputcsv($archivo, $salida, ';', '"', '');
            }
            fclose($archivo);
        }, 'papel-'.$registro['formato'].'-'.$registro['desde'].'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'no-store']);
    }

    public function archivo(Request $request)
    {
        $fila = $this->datos->tabla('tr_documentos')->where('id', (int) $request->route('registro'))->first() ?? abort(404);
        $this->registro('declaraciones', (int) $fila->id_declaracion);
        abort_unless(Storage::disk('local')->exists($fila->archivo), 404);

        return Storage::disk('local')->download($fila->archivo, 'respaldo-'.$fila->id.'.'.$fila->extension, [
            'Content-Type' => 'application/octet-stream', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'no-store',
        ]);
    }

    private function guardarArchivo(Request $request)
    {
        $datos = $request->validate([
            'id_declaracion' => ['required', 'integer', 'min:1'], 'tipo' => ['required', Rule::in(array_keys(config('tributario.archivos')))],
            'nombre' => ['required', 'string', 'max:180'], 'archivo' => ['required', 'file', 'extensions:pdf,xml,csv,xlsx', 'max:10240'],
        ]);
        $this->registro('declaraciones', (int) $datos['id_declaracion']);
        $archivo = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $sha256 = hash_file('sha256', $archivo->getPathname());
        $ruta = $archivo->storeAs('tributario/'.$this->empresa->id(), Str::uuid().'.'.$extension, 'local');
        abort_if(! $ruta, 500, 'No se pudo guardar el respaldo.');
        try {
            $fila = $this->datos->guardarDocumento(array_replace($datos, [
                'archivo' => $ruta, 'extension' => $extension, 'sha256' => $sha256, 'bytes' => $archivo->getSize(),
            ]));
        } catch (Throwable $error) {
            Storage::disk('local')->delete($ruta);
            throw $error;
        }

        return $this->respuesta($request, 'documentos', $fila, 201, 'Respaldo guardado.');
    }

    private function dependientes(string $tabla): Builder
    {
        $campos = $tabla === 'tr_documentos' ? ['nombre', 'tipo', 'bytes', 'extension', 'sha256'] : ['accion', 'detalle'];
        $campos = array_map(fn ($campo) => $tabla.'.'.$campo, ['id', 'id_declaracion', 'id_usuario_creacion', 'created_at', ...$campos]);

        return $this->datos->tabla($tabla)->join('tr_declaraciones as d', 'd.id', '=', $tabla.'.id_declaracion')
            ->select([...$campos, 'd.formato', 'd.desde', 'd.hasta']);
    }

    private function registro(string $seccion, int $id): stdClass
    {
        $fila = $this->datos->declaracion($id);
        abort_unless($seccion === 'declaraciones' || $fila->formato === $seccion, 404);
        abort_unless(in_array($fila->formato, $this->formatosPermitidos(), true), 404);

        return $fila;
    }

    private function formatosPermitidos(): array
    {
        return array_filter(config('tributario.formatos'), fn ($formato) => Gate::allows('permiso', ['tributario.'.$formato.'.index', 'leer']));
    }

    private function filtros(Request $request): array
    {
        $datos = $request->validate([
            'ejercicio' => ['nullable', 'integer', 'between:1900,2100'], 'estado' => ['nullable', Rule::in(array_keys(config('tributario.estados')))],
            'page' => ['nullable', 'integer', 'between:1,100000'], 'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);
        unset($datos['page']);

        return array_filter($datos, fn ($v) => $v !== null);
    }

    private function columnas(string $formato): array
    {
        return match ($formato) {
            'formulario-103' => ['codigo' => 'Código de retención', 'tasa' => 'Porcentaje', 'base' => 'Base', 'retenido' => 'Retenido'],
            'formulario-104' => ['modulo' => 'Origen', 'tasa' => 'Tarifa registrada (%)', 'base' => 'Base neta', 'impuesto' => 'IVA registrado'],
            'formulario-107' => ['identificacion' => 'Identificación', 'nombre' => 'Trabajador', 'ingresos_gravados' => 'Ingresos gravados',
                'ingresos_exentos' => 'Ingresos exentos', 'aporte_personal' => 'Aporte personal', 'retencion' => 'Retención'],
            'ats' => ['fecha' => 'Fecha', 'modulo' => 'Origen', 'tipo' => 'Tipo', 'numero' => 'Comprobante',
                'identificacion' => 'Identificación', 'tipo_identificacion' => 'Tipo de identificación', 'tercero' => 'Tercero',
                'documento_origen' => 'Documento de origen', 'base' => 'Base registrada', 'impuesto' => 'Impuesto registrado', 'total' => 'Total', 'anulado_at' => 'Anulado el'],
        };
    }

    private function presentar(stdClass|array $registro): array
    {
        $fila = (array) $registro;
        unset($fila['archivo']);
        foreach (['resumen', 'detalle', 'observaciones'] as $campo) {
            if (isset($fila[$campo]) && is_string($fila[$campo]) && isset($fila['formato']) && array_key_exists('resumen', $fila)) {
                $fila[$campo] = json_decode($fila[$campo], true, flags: JSON_THROW_ON_ERROR);
                if ($campo === 'detalle') $fila[$campo] = array_map($this->presentar(...), $fila[$campo]);
                if ($campo === 'resumen') $fila[$campo] = $this->presentar($fila[$campo]);
            }
        }
        foreach (['base', 'tasa', 'retenido', 'impuesto', 'total', 'ingresos_gravados', 'ingresos_exentos', 'aporte_personal', 'retencion',
            'base_registrada', 'renta_retenida', 'iva_ventas', 'iva_compras', 'iva_retenido_compras', 'iva_retenido_ventas'] as $campo) {
            if (isset($fila[$campo])) $fila[$campo] = Decimal::decimal((int) $fila[$campo], 2);
        }

        return $fila;
    }

    private function respuesta(Request $request, string $seccion, stdClass $fila, int $status, string $mensaje)
    {
        if ($request->routeIs('api.*')) return response()->json(['data' => $this->presentar($fila)], $status)->header('Cache-Control', 'no-store');
        if ($request->expectsJson()) return response()->json(['message' => $mensaje], $status);

        return redirect()->route('tributario.'.$seccion.'.index')->with('status', $mensaje);
    }
}
