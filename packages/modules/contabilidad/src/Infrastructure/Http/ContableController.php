<?php

namespace Aitrol\Contabilidad\Infrastructure\Http;

use Aitrol\Contabilidad\Application\LibroContable;
use Aitrol\Contabilidad\Infrastructure\Persistence\DatosContables;
use Aitrol\Contabilidad\Infrastructure\Queries\ReportesContables;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use App\Services\ContextoOperativo;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ContableController
{
    public function __construct(private DatosContables $datos, private LibroContable $libro, private ReportesContables $reportes) {}

    public function index(Request $request, ContextoOperativo $contexto)
    {
        $seccion = $request->route('seccion');
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'between:1,100000'], 'per_page' => ['nullable', 'integer', 'between:1,100']]);
        unset($filtros['page']);
        [$consulta, $columnas] = match ($seccion) {
            'asientos' => [$this->datos->asientos()->orderByDesc('co_asientos.fecha')->orderByDesc('co_asientos.numero'), ['co_asientos.concepto', 'co_asientos.referencia']],
            'cuentas' => [$this->datos->cuentas(), ['co_cuentas.codigo', 'co_cuentas.nombre']],
            'periodos' => [$this->datos->tabla('co_periodos')->orderByDesc('desde'), ['nombre']],
        };
        if ($seccion === 'asientos') {
            $contexto->aplicarPeriodo($consulta, 'co_asientos.fecha');
        }
        if ($seccion === 'cuentas' && ! $request->routeIs('api.*')) {
            $buscando = filled($filtros['q'] ?? null);
            $coincidencias = $buscando
                ? $this->datos->buscar(clone $consulta, $filtros['q'], $columnas)->pluck('co_cuentas.id')
                : null;
            $registros = $this->arbolCuentas($consulta->get(), $coincidencias);

            return view('contabilidad::index', compact('seccion', 'registros', 'filtros', 'buscando', 'coincidencias'));
        }
        $registros = $this->datos->buscar($consulta, $filtros['q'] ?? null, $columnas)->paginate($filtros['per_page'] ?? 20)
            ->appends($filtros)->through($this->presentar(...));

        return $request->routeIs('api.*') ? response()->json($registros) : view('contabilidad::index', compact('seccion', 'registros', 'filtros'));
    }

    private function arbolCuentas(Collection $cuentas, ?Collection $coincidencias): Collection
    {
        $visibles = [];
        if ($coincidencias !== null) {
            $porId = $cuentas->keyBy('id');
            foreach ($coincidencias as $id) {
                while ($id !== null && $porId->has($id) && ! isset($visibles[$id])) {
                    $visibles[$id] = true;
                    $id = $porId[$id]->id_padre;
                }
            }
        }

        $hijas = $cuentas->groupBy('id_padre');
        $filas = collect();
        $agregarRama = function (Collection $rama, int $nivel) use (&$agregarRama, $hijas, $filas, $coincidencias, $visibles): void {
            foreach ($rama as $cuenta) {
                $subcuentas = $hijas->get($cuenta->id, collect());
                $filas->push($this->presentar($cuenta) + [
                    'nivel_arbol' => $nivel,
                    'tiene_hijas' => $subcuentas->isNotEmpty(),
                    'visible_plan' => $coincidencias === null || isset($visibles[$cuenta->id]),
                    'expandida_plan' => $coincidencias !== null && $subcuentas->contains(fn (stdClass $hija): bool => isset($visibles[$hija->id])),
                ]);
                $agregarRama($subcuentas, $nivel + 1);
            }
        };
        $agregarRama($hijas->get('', collect()), 0);

        return $filas;
    }

    public function show(Request $request)
    {
        $seccion = $request->route('seccion');
        $registro = $this->presentar($this->registro($seccion, (int) $request->route('registro')));

        return $request->routeIs('api.*') ? response()->json(['data' => $registro]) : view('contabilidad::detalle', compact('seccion', 'registro'));
    }

    public function formulario(Request $request)
    {
        $seccion = $request->route('seccion');
        $accion = $request->route('accion');
        $registro = $accion ? $this->presentar($this->registro($seccion, (int) $request->route('registro'))) : null;
        if ($seccion === 'cuentas' && $accion === 'editar') {
            abort_unless($registro['auxiliar'], 403, 'Solo se pueden editar cuentas auxiliares.');
        }
        $padre = null;
        $codigoSugerido = null;
        $mensajeCodigo = 'Selecciona el grupo para ver el primer código libre.';
        if ($seccion === 'cuentas' && $accion === null) {
            $idPadre = filter_var($request->old('id_padre', $request->input('id_padre')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $padre = $idPadre ? $this->datos->cuenta($idPadre) : null;
        }
        abort_if($padre !== null && ($padre->auxiliar || ! $padre->activo), 404);
        if ($padre !== null) {
            try {
                $codigoSugerido = $this->libro->codigoAuxiliar($padre);
                $mensajeCodigo = 'El sistema asignará el primer código libre al guardar.';
            } catch (ValidationException $error) {
                $mensajeCodigo = $error->errors()['id_padre'][0];
            }
        }

        return view('contabilidad::formulario', compact('seccion', 'accion', 'registro', 'padre', 'codigoSugerido', 'mensajeCodigo'));
    }

    public function codigoAuxiliar(Request $request): JsonResponse
    {
        $datos = $request->validate(['id_padre' => ['required', 'integer', 'min:1']]);
        $padre = $this->datos->cuenta((int) $datos['id_padre']);

        return response()->json(['codigo' => $this->libro->codigoAuxiliar($padre)])->header('Cache-Control', 'no-store');
    }

    public function opciones(Request $request)
    {
        $datos = $request->validate([
            'q' => ['nullable', 'string', 'max:120'], 'page' => ['nullable', 'integer', 'between:1,100000'],
            'clase' => ['nullable', Rule::in(array_keys(config('contabilidad.clases')))], 'efectivo' => ['nullable', 'boolean'],
        ]);
        $modo = $request->route('clase_opcion');
        $consulta = $this->datos->tabla('co_cuentas')->where('auxiliar', $modo !== 'grupos');
        if ($modo !== 'reportes') $consulta->where('activo', true);
        if (isset($datos['clase'])) $consulta->where('clase', $datos['clase']);
        if (isset($datos['efectivo'])) $consulta->where('efectivo', $datos['efectivo']);
        $filas = $this->datos->buscar($consulta, $datos['q'] ?? null, ['codigo', 'nombre'])->orderBy('codigo')->orderBy('id')->paginate(20);

        return response()->json([
            'results' => $filas->getCollection()->map(fn ($fila) => ['id' => $fila->id, 'text' => $fila->codigo.' · '.$fila->nombre]),
            'pagination' => ['more' => $filas->hasMorePages()],
        ]);
    }

    public function store(Request $request)
    {
        $seccion = $request->route('seccion');
        $repetida = false;
        $mensaje = 'Registro guardado.';
        if ($seccion === 'asientos') {
            [$registro, $repetida] = $this->libro->registrar($request->validate($this->reglasAsiento()));
        } elseif ($seccion === 'cuentas') {
            $entrada = $this->camposCuenta($request, false);
            $registro = $this->libro->cuenta($entrada, null);
            if (! isset($entrada['codigo'])) {
                $mensaje = 'Cuenta auxiliar '.$registro->codigo.' guardada.';
            }
        } else {
            $registro = $this->libro->abrirPeriodo($request->validate([
                'nombre' => ['required', 'string', 'max:120'], 'desde' => ['required', 'date_format:Y-m-d'], 'hasta' => ['required', 'date_format:Y-m-d', 'after_or_equal:desde'],
            ]));
        }

        return $this->responder($request, $seccion, $registro, $repetida ? 200 : 201, $repetida ? 'El asiento ya estaba registrado.' : $mensaje);
    }

    public function accion(Request $request)
    {
        $seccion = $request->route('seccion');
        $id = (int) $request->route('registro');
        $registro = match ($request->route('accion')) {
            'editar' => $this->libro->cuenta($this->camposCuenta($request, true), $id),
            'cerrar' => $this->libro->cerrarPeriodo($id, $request->validate(['motivo' => ['required', 'string', 'max:250']])['motivo']),
            'revertir' => $this->libro->revertir($id, $request->validate(['fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'], 'motivo' => ['required', 'string', 'max:250']])),
        };

        return $this->responder($request, $seccion, $registro, 200, 'Cambio registrado.');
    }

    public function reporte(Request $request)
    {
        $reporte = $request->route('reporte');
        $filtros = $this->filtros($request);
        $datos = $this->reportes->consulta($reporte, $filtros);
        $columnas = $this->columnas($reporte);
        $registros = $datos['filas'] instanceof Builder
            ? $datos['filas']->paginate($filtros['per_page'] ?? 30)->appends($filtros)->through($this->presentar(...))
            : ($datos['filas'] ?? collect())->map($this->presentar(...));
        $totales = $this->presentar($datos['totales']);
        $cuenta = $reporte === 'mayorizacion' && isset($filtros['id_cuenta']) ? $this->datos->cuenta((int) $filtros['id_cuenta']) : null;
        $requiereCuenta = $reporte === 'mayorizacion' && $cuenta === null;
        if ($request->routeIs('api.*')) {
            return response()->json(['moneda' => 'USD', 'desde' => $reporte === 'balance-financiero' ? null : $filtros['desde'], 'hasta' => $filtros['hasta'],
                'totales' => (object) $totales, 'requiere_cuenta' => $requiereCuenta] + ($registros instanceof \Illuminate\Pagination\LengthAwarePaginator ? $registros->toArray() : ['data' => $registros]));
        }

        return view('contabilidad::reporte', compact('reporte', 'filtros', 'columnas', 'registros', 'totales', 'cuenta', 'requiereCuenta'));
    }

    public function exportar(Request $request)
    {
        $reporte = $request->route('reporte');
        $filtros = $this->filtros($request);
        $datos = $this->reportes->consulta($reporte, $filtros);
        $columnas = $this->columnas($reporte);
        if ($datos['filas'] === null) throw ValidationException::withMessages(['id_cuenta' => 'Selecciona una cuenta contable para exportar el mayor.']);

        return response()->streamDownload(function () use ($datos, $columnas): void {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, array_values($columnas), ';', '"', '');
            $filas = $datos['filas'] instanceof Builder ? $datos['filas']->cursor() : $datos['filas'];
            foreach ($filas as $fila) {
                $fila = $this->presentar($fila);
                $salida = [];
                foreach ($columnas as $campo => $etiqueta) {
                    $valor = $fila[$campo] ?? '';
                    $salida[] = in_array($campo, ['codigo', 'cuenta', 'concepto', 'detalle', 'clase', 'flujo'], true)
                        && preg_match('/^[\x00-\x20]*[=+@-]|^[\t\r\n]/', (string) $valor) ? "'".$valor : $valor;
                }
                fputcsv($archivo, $salida, ';', '"', '');
            }
            fclose($archivo);
        }, $reporte.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function integracion(Request $request)
    {
        $config = $this->configIntegracion();
        if ($request->routeIs('api.*')) return response()->json(['data' => $config]);

        $cuentas = $this->datos->tabla('co_cuentas')->whereIn('id', [...array_values($config['reglas']), ...array_values($config['tesoreria'])])
            ->get(['id', 'codigo', 'nombre'])->keyBy('id');
        $cajas = $this->datos->tabla('cb_cuentas')->where('moneda', 'USD')->orderBy('codigo')->get(['id', 'codigo', 'nombre']);

        return view('contabilidad::integraciones', compact('config', 'cuentas', 'cajas'));
    }

    public function actualizarIntegracion(Request $request)
    {
        $reglas = array_keys(config('contabilidad.reglas'));
        $entrada = $request->validate([
            'activa' => ['required', 'boolean'], 'fecha_inicio' => ['required_if:activa,1,true', 'nullable', 'date_format:Y-m-d'],
            'reglas' => ['nullable', 'array:'.implode(',', $reglas)], 'reglas.*' => ['nullable', 'integer', 'min:1'],
            'tesoreria' => ['nullable', 'array'], 'tesoreria.*' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($entrada['activa'] && empty($entrada['fecha_inicio'])) {
            throw ValidationException::withMessages(['fecha_inicio' => 'Indica desde cuándo se contabilizarán las operaciones.']);
        }
        foreach (array_keys($entrada['tesoreria'] ?? []) as $clave) {
            if (! preg_match('/^[1-9][0-9]{0,17}$/', (string) $clave)) throw ValidationException::withMessages(['tesoreria' => 'Usa como clave el ID de la caja o banco.']);
        }
        $this->libro->guardarIntegracion($entrada);

        return $request->routeIs('api.*') ? response()->json(['data' => $this->configIntegracion()])
            : redirect()->route('contabilidad.integraciones.index')->with('status', 'Configuración contable guardada.');
    }

    private function configIntegracion(): array
    {
        $fila = $this->datos->tabla('co_integraciones')->first();

        return [
            'activa' => (bool) ($fila->activa ?? false), 'fecha_inicio' => $fila->fecha_inicio ?? null,
            'reglas' => $fila === null ? [] : json_decode($fila->reglas, true, flags: JSON_THROW_ON_ERROR),
            'tesoreria' => $fila === null ? [] : json_decode($fila->tesoreria, true, flags: JSON_THROW_ON_ERROR),
        ];
    }

    private function registro(string $seccion, int $id): stdClass
    {
        return match ($seccion) {
            'asientos' => $this->datos->asiento($id), 'cuentas' => $this->datos->cuenta($id), 'periodos' => $this->datos->periodo($id),
        };
    }

    private function reglasAsiento(): array
    {
        $importe = ['nullable', 'numeric', 'decimal:0,2', 'between:0,1000000000'];

        return [
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'], 'concepto' => ['required', 'string', 'max:250'],
            'referencia' => ['nullable', 'string', 'max:120'], 'flujo' => ['nullable', Rule::in(array_keys(config('contabilidad.flujos')))],
            'clave_operacion' => ['nullable', 'uuid'], 'lineas' => ['required', 'list', 'min:2', 'max:100'],
            'lineas.*' => ['required', 'array:id_cuenta,debe,haber,detalle,movimiento_patrimonio'], 'lineas.*.id_cuenta' => ['required', 'integer', 'min:1'],
            'lineas.*.debe' => $importe, 'lineas.*.haber' => $importe, 'lineas.*.detalle' => ['nullable', 'string', 'max:250'],
            'lineas.*.movimiento_patrimonio' => ['nullable', Rule::in(array_keys(config('contabilidad.patrimonio')))],
        ];
    }

    private function camposCuenta(Request $request, bool $editar): array
    {
        $reglas = ['codigo' => [$editar || $request->routeIs('api.*') ? 'required' : 'sometimes', 'string', 'max:40'],
            'nombre' => ['required', 'string', 'max:160'], 'activo' => [$editar ? 'required' : 'sometimes', 'boolean']];
        if (! $editar) $reglas += [
            'clase' => ['sometimes', 'required', Rule::in(array_keys(config('contabilidad.clases')))], 'id_padre' => ['required', 'integer', 'min:1'],
            'auxiliar' => ['sometimes', 'accepted'], 'efectivo' => ['sometimes', 'boolean'],
        ];
        $datos = $request->validate($reglas) + ['activo' => true];
        if (isset($datos['codigo'])) {
            $datos['codigo'] = Str::upper($datos['codigo']);
            if (Str::length($datos['codigo']) > 40) {
                throw ValidationException::withMessages(['codigo' => 'El código debe tener hasta 40 caracteres en mayúsculas.']);
            }
        }

        return $editar ? $datos : $datos + ['auxiliar' => true, 'efectivo' => false];
    }

    private function filtros(Request $request): array
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'], 'hasta' => ['nullable', 'date_format:Y-m-d'],
            'id_cuenta' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'between:1,100000'], 'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);
        unset($filtros['page']);
        $filtros['hasta'] ??= now()->toDateString();
        $filtros['desde'] ??= substr($filtros['hasta'], 0, 4).'-01-01';
        if ($filtros['desde'] > $filtros['hasta']) throw ValidationException::withMessages(['hasta' => 'La fecha final debe ser igual o posterior a la inicial.']);

        return $filtros;
    }

    private function columnas(string $reporte): array
    {
        return match ($reporte) {
            'libro-diario', 'mayorizacion' => ['fecha' => 'Fecha', 'numero' => 'Asiento', 'codigo' => 'Código', 'cuenta' => 'Cuenta', 'concepto' => 'Concepto',
                'debe' => 'Debe', 'haber' => 'Haber'] + ($reporte === 'mayorizacion' ? ['saldo' => 'Saldo deudor / acreedor'] : []),
            'balance-comprobacion' => ['codigo' => 'Código', 'cuenta' => 'Cuenta', 'clase' => 'Clase', 'apertura' => 'Saldo inicial', 'debe' => 'Debe del período',
                'haber' => 'Haber del período', 'cierre' => 'Saldo final'],
            'estado-resultados', 'balance-financiero' => ['codigo' => 'Código', 'cuenta' => 'Cuenta', 'clase' => 'Clase', 'importe' => 'Importe'],
            'flujo-caja' => ['flujo' => 'Actividad', 'ingresos' => 'Entradas', 'egresos' => 'Salidas', 'neto' => 'Flujo neto'],
            'cambios-patrimonio' => ['codigo' => 'Código', 'cuenta' => 'Componente', 'apertura' => 'Saldo inicial']
                + array_diff_key(config('contabilidad.patrimonio'), ['apertura' => true])
                + ['registro_inicial' => 'Aperturas registradas', 'resultado' => 'Variación del resultado acumulado', 'cierre' => 'Saldo final'],
        };
    }

    private function presentar(stdClass|array $registro): array
    {
        $fila = (array) $registro;
        unset($fila['huella']);
        foreach (['total', 'debe', 'haber', 'saldo', 'apertura', 'cierre', 'importe', 'ingresos', 'gastos', 'resultado', 'activos', 'pasivos',
            'patrimonio_registrado', 'resultado_acumulado', 'patrimonio', 'pasivo_patrimonio', 'diferencia', 'egresos', 'neto', 'aporte', 'retiro', 'distribucion', 'traspaso_resultado', 'otro', 'registro_inicial'] as $campo) {
            if (isset($fila[$campo])) $fila[$campo] = Decimal::decimal((int) $fila[$campo], 2);
        }
        foreach (['auxiliar', 'efectivo', 'activo'] as $campo) {
            if (isset($fila[$campo])) $fila[$campo] = (bool) $fila[$campo];
        }
        if (isset($fila['lineas'])) $fila['lineas'] = $fila['lineas']->map($this->presentar(...));

        return $fila;
    }

    private function responder(Request $request, string $seccion, stdClass $registro, int $estado, string $mensaje)
    {
        if ($request->routeIs('api.*')) {
            $cabeceras = $estado === 201 ? ['Location' => route('api.v1.contabilidad.'.$seccion.'.show', $registro->id)] : [];

            return response()->json(['data' => $this->presentar($registro)], $estado, $cabeceras);
        }
        if ($request->expectsJson()) return response()->json(['message' => $mensaje], $estado);

        return redirect()->route('contabilidad.'.$seccion.'.index')->with('status', $mensaje);
    }
}
