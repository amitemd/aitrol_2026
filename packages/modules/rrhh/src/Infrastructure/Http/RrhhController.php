<?php

namespace Aitrol\Rrhh\Infrastructure\Http;

use Aitrol\Rrhh\Application\DatosRrhh;
use Aitrol\Rrhh\Application\GestionPersonal;
use Aitrol\Rrhh\Application\Nomina;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class RrhhController
{
    public function __construct(private DatosRrhh $datos, private GestionPersonal $gestion, private Nomina $nominas) {}

    public function index(Request $request): View
    {
        $seccion = $request->route('seccion');
        $flujo = config('rrhh.procesos.'.$seccion, []);
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        if (in_array($seccion, ['panel', 'reportes'], true)) {
            return view('rrhh::panel', ['seccion' => $seccion, 'indicadores' => $this->indicadores()]);
        }

        [$consulta, $busqueda] = match ($seccion) {
            'nomina' => [$this->datos->tabla('rh_nominas'), ['periodo']],
            'roles' => [$this->datos->tabla('rh_roles')->join('rh_nominas as n', 'n.id', '=', 'rh_roles.id_nomina')
                ->select('rh_roles.*', 'n.periodo', 'n.estado'), ['rh_roles.empleado', 'n.periodo']],
            'contratos' => [$this->gestion->consulta($seccion)->join('rh_empleados as e', 'e.id', '=', 'rh_contratos.id_empleado')
                ->select('rh_contratos.*', 'e.nombre as empleado'), ['e.nombre', 'rh_contratos.tipo']],
            'empleados' => [$this->gestion->consulta($seccion), ['nombre', 'codigo']],
            default => [$this->gestion->consulta($seccion)->leftJoin('rh_empleados as e', 'e.id', '=', 'rh_procesos.id_empleado')
                ->select('rh_procesos.*', 'e.nombre as empleado'), ['rh_procesos.titulo', 'e.nombre']],
        };
        if ($seccion === 'prestamos') {
            $cuotas = $this->datos->procedimientos('novedades')->whereColumn('id_prestamo', 'prestamo.id')
                ->whereNotNull('id_nomina')->selectRaw('coalesce(sum(importe), 0)');
            $consulta->join('rh_procesos as prestamo', 'prestamo.id', '=', 'rh_procesos.id')->selectSub($cuotas, 'descontado');
        }
        if (isset($filtros['q'])) {
            $consulta->where(function (Builder $q) use ($busqueda, $filtros): void {
                foreach ($busqueda as $campo) {
                    $q->orWhereLike($campo, '%'.$filtros['q'].'%');
                }
            });
        }
        $registros = $consulta->orderByDesc($consulta->from.'.id')->paginate(25)->appends($filtros);

        return view('rrhh::index', compact('seccion', 'flujo', 'registros', 'filtros'));
    }

    public function formulario(Request $request): View
    {
        $seccion = $request->route('seccion');
        $id = $request->route('registro');
        $registro = $id === null ? null : $this->datos->registro($this->gestion->consulta($seccion), (int) $id);
        $soloEstado = $seccion === 'conceptos' && $registro?->estado === 'aprobado';
        if ($registro && isset(config('rrhh.procesos')[$seccion]) && $seccion !== 'organizacion' && ! $soloEstado) {
            abort_unless($registro->estado === 'pendiente', 409, 'El registro ya fue resuelto.');
        }
        $campos = $soloEstado ? [] : $this->campos($seccion, $registro);

        return view('rrhh::formulario', compact('seccion', 'registro', 'campos', 'soloEstado'));
    }

    public function detalle(Request $request): View
    {
        $seccion = $request->route('seccion');
        $registro = $this->datos->registro($this->gestion->consulta($seccion), (int) $request->route('registro'));
        $campos = [];
        foreach ($this->campos($seccion, $registro) as $clave => $campo) {
            if (($valor = $registro->{$clave} ?? null) === null) {
                continue;
            }
            $campos[$campo[0]] = match (true) {
                $campo[1] === 'select' => $campo[3][$valor] ?? $valor,
                in_array($clave, ['importe', 'sueldo'], true) => 'USD '.DatosRrhh::importe((int) $valor),
                default => $valor,
            };
        }
        if (isset($registro->estado) && $seccion !== 'organizacion') {
            $campos['Estado'] = config('rrhh.procesos.'.$seccion.'.estados.'.$registro->estado, ucfirst($registro->estado));
        }
        if (in_array($seccion, ['conceptos', 'organizacion'], true)) {
            $campos['Activación'] = $registro->activo ? 'Activo' : 'Inactivo';
        }

        return view('partials.detalle-ficha-simple', [
            'titulo' => config('rrhh.secciones.'.$seccion.'.0'), 'volver' => route('rrhh.'.$seccion.'.index'), 'campos' => $campos,
        ]);
    }

    public function guardar(Request $request): RedirectResponse|JsonResponse
    {
        $seccion = $request->route('seccion');
        if ($seccion === 'conceptos' && $request->route('registro') !== null && $request->boolean('solo_estado')) {
            $datos = $request->validate(['activo' => ['required', 'boolean']]);
            $this->gestion->cambiarActivoConcepto((int) $request->route('registro'), (bool) $datos['activo']);

            return $this->resultado($request, 'Estado actualizado.', 'rrhh.conceptos.index');
        }
        $flujo = config('rrhh.procesos.'.$seccion, []);
        $importe = ['required', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/', 'numeric', 'gt:0'];
        $fecha = ['required', 'date_format:Y-m-d'];
        $reglas = match ($seccion) {
            'empleados' => [
                'codigo' => ['required', 'regex:/^[A-Za-z0-9_-]{1,30}$/'], 'nombre' => ['required', 'string', 'max:160'],
                'identificacion' => ['nullable', 'string', 'max:40'], 'correo' => ['nullable', 'email', 'max:150'],
                'ingreso' => $fecha, 'salida' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:ingreso'],
                'id_departamento' => ['nullable', 'integer', 'min:1'], 'id_cargo' => ['nullable', 'integer', 'min:1'],
            ],
            'contratos' => [
                'id_empleado' => ['required', 'integer', 'min:1'], 'tipo' => ['required', 'string', 'max:40'],
                'inicio' => $fecha, 'fin' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:inicio'],
                'sueldo' => $importe, 'horas_semanales' => ['required', 'numeric', 'decimal:0,2', 'between:1,168'],
            ],
            'nomina' => ['periodo' => ['required', 'date_format:Y-m', 'regex:/^20\d{2}-/']],
            default => [
                'titulo' => ['required', 'string', 'max:160'], 'tipo' => ['required', Rule::in(array_keys($flujo['tipos']))],
                'detalle' => ['nullable', 'string', 'max:3000'],
                ...(! empty($flujo['empleado']) ? ['id_empleado' => ['required', 'integer', 'min:1']] : []),
                ...(isset($flujo['fecha']) ? ['inicio' => $fecha] : []),
                ...(isset($flujo['fin']) ? ['fin' => [$seccion === 'permisos' ? 'required' : 'nullable', 'date_format:Y-m-d', 'after_or_equal:inicio']] : []),
                ...(isset($flujo['importe']) ? ['importe' => $importe] : []),
                ...(isset($flujo['cantidad']) ? ['cantidad' => $seccion === 'saldos-vacaciones'
                    ? ['required', 'integer', 'between:1,100']
                    : ['required', 'numeric', 'decimal:0,2', 'between:0,100']] : []),
                ...($seccion === 'novedades' ? ['id_prestamo' => ['exclude_unless:tipo,cuota', 'required', 'integer', 'min:1']] : []),
            ],
        };
        if ($seccion === 'conceptos') {
            $reglas['importe'] = ['exclude_unless:tipo,ingreso-fijo,descuento-fijo', ...$importe];
            $reglas['cantidad'] = ['exclude_unless:tipo,ingreso-porcentual,descuento-porcentual', 'required', 'numeric', 'decimal:0,2', 'gt:0', 'max:100'];
        }
        if (in_array($seccion, ['conceptos', 'organizacion'], true)) {
            $reglas['activo'] = ['sometimes', 'boolean'];
        }
        $datos = $request->validate($reglas);
        if ($seccion === 'conceptos') {
            $datos += ['importe' => null, 'cantidad' => null];
        }
        if ($seccion === 'nomina') {
            $id = $this->nominas->abrir($datos['periodo']);

            return $this->resultado($request, 'Nómina preparada para revisión.', 'rrhh.nomina.show', [$id]);
        }
        $this->gestion->guardar($seccion, $request->route('registro') === null ? null : (int) $request->route('registro'), $datos);

        return $this->resultado($request, 'Registro guardado.', 'rrhh.'.$seccion.'.index');
    }

    public function estado(Request $request): RedirectResponse|JsonResponse
    {
        $seccion = $request->route('seccion');
        $id = (int) $request->route('registro');
        if ($seccion === 'nomina') {
            $datos = $request->validate([
                'estado' => ['required', 'in:cerrar,pagar'],
                'referencia' => ['exclude_unless:estado,pagar', 'required', 'string', 'max:80'],
            ]);
            $this->nominas->cambiarEstado($id, $datos['estado'], $datos['referencia'] ?? null);

            return $this->resultado($request, 'Estado de nómina actualizado.', 'rrhh.nomina.show', [$id]);
        }
        $datos = $request->validate(['estado' => ['required', 'in:aprobado,rechazado']]);
        $this->gestion->resolver($seccion, $id, $datos['estado']);

        return $this->resultado($request, 'Registro resuelto.', 'rrhh.'.$seccion.'.index');
    }

    private function resultado(Request $request, string $mensaje, string $ruta, array $parametros = []): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje])
            : to_route($ruta, $parametros)->with('status', $mensaje);
    }

    public function nomina(int $registro): View
    {
        $nomina = $this->datos->registro($this->datos->tabla('rh_nominas'), $registro);
        $roles = $this->datos->tabla('rh_roles')->where('id_nomina', $registro)->orderBy('empleado')->orderBy('id')->paginate(30);

        return view('rrhh::nomina', compact('nomina', 'roles'));
    }

    public function rol(int $registro): View
    {
        $rol = $this->datos->registro($this->datos->tabla('rh_roles'), $registro);
        $nomina = $this->datos->registro($this->datos->tabla('rh_nominas'), (int) $rol->id_nomina);
        $lineas = json_decode($rol->lineas, true, 512, JSON_THROW_ON_ERROR);

        return view('rrhh::rol', compact('rol', 'nomina', 'lineas'));
    }

    public function exportar(int $registro): StreamedResponse
    {
        $nomina = $this->datos->registro($this->datos->tabla('rh_nominas'), $registro);
        abort_if($nomina->estado === 'borrador', 409);
        $consulta = $this->datos->tabla('rh_roles')->where('id_nomina', $registro);

        return response()->streamDownload(function () use ($consulta): void {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Empleado', 'Ingresos', 'Descuentos', 'Neto'], ';', '"', '');
            foreach ($consulta->lazyById() as $rol) {
                $nombre = preg_match('/^\s*[=+\-@]|^[\t\r\n]/u', $rol->empleado) ? "'".$rol->empleado : $rol->empleado;
                fputcsv($salida, [$nombre, DatosRrhh::importe($rol->ingresos), DatosRrhh::importe($rol->descuentos), DatosRrhh::importe($rol->neto)], ';', '"', '');
            }
            fclose($salida);
        }, 'nomina-'.substr($nomina->periodo, 0, 7).'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function campos(string $seccion, ?stdClass $registro = null): array
    {
        $empleado = fn (): array => ['Empleado', 'select', true, $this->datos->tabla('rh_empleados')->orderBy('nombre')->pluck('nombre', 'id')];
        $organizacion = fn (string $tipo, ?int $asignado) => $this->datos->procedimientos('organizacion')->where('tipo', $tipo)
            ->where(fn (Builder $query) => $query->where('activo', true)->when($asignado !== null, fn (Builder $opciones) => $opciones->orWhere('id', $asignado)))
            ->orderBy('titulo')->pluck('titulo', 'id');
        $flujo = config('rrhh.procesos.'.$seccion, []);

        return match ($seccion) {
            'empleados' => [
                'codigo' => ['Código', 'text', true], 'nombre' => ['Nombre completo', 'text', true],
                'identificacion' => ['Identificación', 'text', false], 'correo' => ['Correo de trabajo', 'email', false],
                'ingreso' => ['Fecha de ingreso', 'date', true], 'salida' => ['Fecha de salida', 'date', false],
                'id_departamento' => ['Departamento', 'select', false, $organizacion('departamento', $registro?->id_departamento)],
                'id_cargo' => ['Cargo', 'select', false, $organizacion('cargo', $registro?->id_cargo)],
            ],
            'contratos' => [
                'id_empleado' => $empleado(), 'tipo' => ['Modalidad contractual', 'text', true],
                'inicio' => ['Inicio del contrato', 'date', true], 'fin' => ['Fin del contrato', 'date', false],
                'sueldo' => ['Sueldo bruto mensual (USD)', 'number', true], 'horas_semanales' => ['Horas semanales', 'number', true],
            ],
            'nomina' => ['periodo' => ['Mes de nómina', 'month', true]],
            default => [
                'titulo' => ['Descripción breve', 'text', true], 'tipo' => ['Tipo', 'select', true, $flujo['tipos']],
                ...(! empty($flujo['empleado']) ? ['id_empleado' => $empleado()] : []),
                ...(isset($flujo['fecha']) ? ['inicio' => [$flujo['fecha'], 'date', true]] : []),
                ...(isset($flujo['fin']) ? ['fin' => [$flujo['fin'], 'date', $seccion === 'permisos']] : []),
                ...(isset($flujo['cantidad']) ? ['cantidad' => [$flujo['cantidad'], 'number', $seccion !== 'conceptos']] : []),
                ...(isset($flujo['importe']) ? ['importe' => ['Importe (USD)', 'number', $seccion !== 'conceptos']] : []),
                ...($seccion === 'novedades' ? ['id_prestamo' => ['Préstamo o anticipo (solo para cuotas)', 'select', false,
                    $this->datos->procedimientos('prestamos')->where('estado', 'aprobado')->orderBy('titulo')->pluck('titulo', 'id')]] : []),
                'detalle' => ['Notas', 'textarea', false],
            ],
        };
    }

    private function indicadores(): array
    {
        $indicadores = [
            'empleados' => ['Empleados activos', fn (): int => $this->datos->tabla('rh_empleados')->where('ingreso', '<=', today()->toDateString())
                ->where(fn (Builder $q) => $q->whereNull('salida')->orWhere('salida', '>=', today()->toDateString()))->count()],
            'contratos' => ['Contratos que vencen en 30 días', fn (): int => $this->datos->tabla('rh_contratos')->whereBetween('fin', [today()->toDateString(), today()->addDays(30)->toDateString()])->count()],
            'permisos' => ['Permisos por aprobar', fn (): int => $this->datos->procedimientos('permisos')->where('estado', 'pendiente')->count()],
            'asistencia' => ['Registros de asistencia del mes', fn (): int => $this->datos->procedimientos('asistencia')->whereBetween('inicio', [today()->startOfMonth()->toDateString(), today()->endOfMonth()->toDateString()])->count()],
            'reclutamiento' => ['Vacantes abiertas', fn (): int => $this->datos->procedimientos('reclutamiento')->where('tipo', 'vacante')->where('estado', 'pendiente')->count()],
            'nomina' => ['Nóminas por cerrar', fn (): int => $this->datos->tabla('rh_nominas')->where('estado', 'borrador')->count()],
        ];

        return collect($indicadores)->filter(fn (array $indicador, string $seccion): bool => Gate::allows('permiso', ['rrhh.'.$seccion.'.index', 'leer']))
            ->map(fn (array $indicador): array => [$indicador[0], $indicador[1]()])->all();
    }
}
