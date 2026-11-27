<?php

namespace Aitrol\ActivosFijos\Infrastructure\Http;

use Aitrol\ActivosFijos\Application\RegistroActivos;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ActivosController
{
    public function __construct(private RegistroActivos $activos) {}

    public function index(Request $request)
    {
        $seccion = $request->route('seccion');
        $filtros = $this->filtros($request, $seccion);
        $registros = $this->activos->listado($seccion, $filtros)->paginate($filtros['per_page'] ?? 20)->appends($filtros)
            ->through($this->presentar(...));
        $resumen = $seccion === 'reportes' ? $this->presentar((object) $this->activos->resumen($filtros)) : null;
        if ($request->routeIs('api.*')) {
            return response()->json($resumen === null ? $registros : ['resumen' => $resumen] + $registros->toArray());
        }

        return view('activos-fijos::index', compact('seccion', 'filtros', 'registros', 'resumen'));
    }

    public function show(Request $request)
    {
        $seccion = $request->route('seccion');
        $registro = $this->presentar($this->activos->registro($seccion, (int) $request->route('registro')));

        return $request->routeIs('api.*') ? response()->json(['data' => $registro])
            : view('activos-fijos::detalle', compact('seccion', 'registro'));
    }

    public function formulario(Request $request)
    {
        $seccion = $request->route('seccion');
        $id = $request->route('registro');
        $registro = $id ? $this->presentar($this->activos->registro($seccion, (int) $id)) : null;
        $origenes = $this->origenes($seccion);

        return view('activos-fijos::formulario', compact('seccion', 'registro', 'origenes'));
    }

    public function opciones(Request $request)
    {
        $origen = $request->route('origen');
        abort_unless(in_array($origen, $this->origenes($request->route('seccion')), true), 404);
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'between:1,100000']]);
        $resultados = $this->activos->opciones($origen, $datos['q'] ?? null)->paginate(20);

        return response()->json([
            'results' => $resultados->getCollection()->map(fn (stdClass $fila) => ['id' => $fila->id, 'text' => $fila->codigo.' · '.$fila->nombre]),
            'pagination' => ['more' => $resultados->hasMorePages()],
        ]);
    }

    public function store(Request $request)
    {
        return $this->guardar($request, null);
    }

    public function update(Request $request)
    {
        return $this->guardar($request, (int) $request->route('registro'));
    }

    public function exportar(Request $request)
    {
        $filtros = $this->filtros($request, 'reportes');

        return response()->streamDownload(function () use ($filtros): void {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, ['Código', 'Activo', 'Grupo', 'Tipo', 'Subtipo', 'Ubicación', 'Estado', 'Costo', 'Depreciación acumulada', 'Valor en libros'], ';', '"', '');
            foreach ($this->activos->listado('reportes', $filtros)->cursor() as $registro) {
                $fila = $this->presentar($registro);
                $celdas = [];
                foreach (['codigo', 'nombre', 'grupo', 'tipo', 'subtipo', 'ubicacion'] as $campo) {
                    $valor = $fila[$campo];
                    $celdas[] = preg_match('/^[\x00-\x20]*[=+@-]|^[\t\r\n]/', $valor) ? "'".$valor : $valor;
                }
                fputcsv($archivo, [...$celdas, $fila['estado'], $fila['costo'], $fila['depreciado'], $fila['valor_libros']], ';', '"', '');
            }
            fclose($archivo);
        }, 'activos-fijos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function guardar(Request $request, ?int $id)
    {
        $seccion = $request->route('seccion');
        $catalogo = in_array($seccion, config('activos-fijos.catalogos'), true);
        $datos = $request->validate($this->reglas($seccion, $id !== null, $catalogo));
        if (isset($datos['codigo'])) {
            $datos['codigo'] = Str::upper($datos['codigo']);
            if (Str::length($datos['codigo']) > 40) {
                throw ValidationException::withMessages(['codigo' => 'El código debe tener hasta 40 caracteres en mayúsculas.']);
            }
        }
        if ($catalogo) {
            $datos['activo'] ??= true;
            $registro = $this->activos->guardarCatalogo($seccion, $datos, $id);
        } else {
            if ($seccion === 'depreciaciones' && $datos['periodo'] > now()->format('Y-m')) {
                throw ValidationException::withMessages(['periodo' => 'No se pueden registrar períodos futuros.']);
            }
            $registro = match ($seccion) {
                'activos' => $this->activos->guardarActivo($datos, $id),
                'movimientos' => $this->activos->mover($datos),
                'depreciaciones' => $this->activos->depreciar($datos),
                'ordenes' => $this->activos->guardarOrden($datos, $id),
            };
        }
        $estatus = $id === null ? 201 : 200;
        if ($request->routeIs('api.*')) {
            $cabeceras = $id === null ? ['Location' => route('api.v1.activos-fijos.'.$seccion.'.show', $registro->id)] : [];

            return response()->json(['data' => $this->presentar($registro)], $estatus, $cabeceras);
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Registro guardado.'], $estatus);
        }

        return redirect()->route('activos-fijos.'.$seccion.'.index')->with('status', 'Registro guardado.');
    }

    private function reglas(string $seccion, bool $edicion, bool $catalogo): array
    {
        $clave = ['integer', 'min:1'];
        $vida = ['nullable', 'integer', 'between:0,600'];
        $valor = ['numeric', 'decimal:0,2', 'between:0,1000000000'];
        $basicas = ['codigo' => ['required', 'string', 'max:40'], 'nombre' => ['required', 'string', 'max:160'], 'descripcion' => ['nullable', 'string', 'max:500']];
        if ($catalogo) {
            $reglas = $basicas + ['activo' => [$edicion ? 'required' : 'sometimes', 'boolean']];
            if (config('activos-fijos.padres.'.$seccion)) $reglas['id_padre'] = ['required', ...$clave];
            if ($seccion === 'subtipos') $reglas['vida_util_meses'] = $vida;

            return $reglas;
        }

        return match ($seccion) {
            'activos' => $basicas + [
                'marca' => ['nullable', 'string', 'max:80'], 'modelo' => ['nullable', 'string', 'max:80'], 'serie' => ['nullable', 'string', 'max:100'],
                'costo' => [$edicion ? 'sometimes' : 'required', ...$valor], 'valor_residual' => ['nullable', ...$valor],
                'vida_util_meses' => $vida, 'inicio_depreciacion' => ['nullable', 'date_format:Y-m-d'],
            ] + ($edicion ? [] : [
                'id_subtipo' => ['required', ...$clave], 'id_ubicacion' => ['required', ...$clave],
                'id_compra' => ['nullable', ...$clave], 'id_responsable' => ['nullable', ...$clave],
                'fecha_adquisicion' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            ]),
            'movimientos' => [
                'id_activo' => ['required', ...$clave], 'tipo' => ['required', 'in:traslado,asignacion,baja'],
                'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'], 'motivo' => ['required', 'string', 'max:250'],
                'id_ubicacion_destino' => ['exclude_unless:tipo,traslado', 'required', ...$clave],
                'id_responsable_destino' => ['exclude_unless:tipo,asignacion', 'nullable', ...$clave],
            ],
            'depreciaciones' => ['id_activo' => ['required', ...$clave], 'periodo' => ['required', 'date_format:Y-m']],
            'ordenes' => $edicion ? [
                'estado' => ['required', 'in:en_proceso,completada,cancelada'],
                'resultado' => ['required_if:estado,completada,cancelada', 'nullable', 'string', 'max:500'],
                'costo_mantenimiento' => ['exclude_unless:estado,completada', 'nullable', ...$valor],
            ] : [
                'id_activo' => ['required', ...$clave], 'tipo' => ['required', 'in:preventivo,correctivo'],
                'detalle' => ['required', 'string', 'max:500'], 'fecha_programada' => ['required', 'date_format:Y-m-d'],
            ],
        };
    }

    private function filtros(Request $request, string $seccion): array
    {
        $reglas = ['q' => ['nullable', 'string', 'max:120'], 'per_page' => ['nullable', 'integer', 'between:1,100'], 'page' => ['nullable', 'integer', 'between:1,100000']];
        if (in_array($seccion, ['activos', 'adquisiciones', 'reportes', ...config('activos-fijos.catalogos')], true)) {
            $reglas['activo'] = ['nullable', 'boolean'];
        }
        if (in_array($seccion, ['activos', 'adquisiciones', 'reportes'], true)) {
            $reglas['desde'] = $reglas['hasta'] = ['nullable', 'date_format:Y-m-d'];
            if ($request->filled('desde')) $reglas['hasta'][] = 'after_or_equal:desde';
        }
        if (in_array($seccion, ['movimientos', 'depreciaciones', 'ordenes'], true)) {
            $reglas['id_activo'] = ['nullable', 'integer', 'min:1'];
        }
        if ($seccion === 'ordenes') $reglas['estado'] = ['nullable', 'in:pendiente,en_proceso,completada,cancelada'];
        $filtros = array_filter($request->validate($reglas), fn ($valor) => $valor !== null);
        unset($filtros['page']);

        return $filtros;
    }

    private function origenes(string $seccion): array
    {
        return match ($seccion) {
            'tipos' => ['grupos'], 'subtipos' => ['tipos'],
            'activos' => ['subtipos', 'ubicaciones', 'responsables', 'compras'],
            'movimientos' => ['activos', 'ubicaciones', 'responsables'],
            'ordenes' => ['activos'], 'depreciaciones' => ['depreciables'],
            default => [],
        };
    }

    private function presentar(stdClass $registro): array
    {
        $datos = (array) $registro;
        if (array_key_exists('fecha_baja', $datos)) {
            $datos['valor_libros'] = $datos['costo'] - $datos['depreciado'];
            $datos['estado'] = $datos['fecha_baja'] === null ? 'activo' : 'baja';
        }
        if (isset($datos['clase'])) $datos['activo'] = (bool) $datos['activo'];
        foreach (['costo', 'valor_residual', 'depreciado', 'valor_libros', 'importe', 'acumulado', 'costo_mantenimiento'] as $campo) {
            if (isset($datos[$campo])) $datos[$campo] = Decimal::decimal((int) $datos[$campo], 2);
        }

        return $datos;
    }
}
