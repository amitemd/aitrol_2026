<?php

namespace Aitrol\CajaBancos\Infrastructure\Http;

use Aitrol\CajaBancos\Application\ConciliacionBancaria;
use Aitrol\CajaBancos\Application\DatosTesoreria;
use Aitrol\CajaBancos\Application\LibroCaja;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class CajaBancosController
{
    public function __construct(private DatosTesoreria $datos, private LibroCaja $libro, private ConciliacionBancaria $banco) {}

    public function index(Request $request)
    {
        $seccion = $request->route('seccion');
        $filtros = $this->filtros($request);
        $registros = $this->datos->listado($seccion, $filtros)->paginate($filtros['per_page'] ?? 20)->appends($filtros)->through($this->presentar(...));
        $resumen = null;
        if ($seccion === 'reportes') {
            $resumen = $this->datos->listado('reportes', $filtros)->reorder()->select('op.moneda')
                ->selectRaw('sum(case when cb_asientos.variacion > 0 then cb_asientos.variacion else 0 end) as ingresos')
                ->selectRaw('sum(case when cb_asientos.variacion < 0 then -cb_asientos.variacion else 0 end) as egresos')
                ->groupBy('op.moneda')->orderBy('op.moneda')->get()->map($this->presentar(...));
        }
        if ($request->routeIs('api.*')) {
            return response()->json($resumen === null ? $registros : ['resumen' => $resumen] + $registros->toArray());
        }

        $cuentaFiltro = isset($filtros['id_cuenta']) ? $this->datos->tabla('cb_cuentas')->where('id', $filtros['id_cuenta'])->first() : null;

        return view('caja-bancos::index', compact('seccion', 'filtros', 'registros', 'resumen', 'cuentaFiltro'));
    }

    public function show(Request $request)
    {
        $seccion = $request->route('seccion');
        $registro = $this->presentar($this->datos->detalle($seccion, (int) $request->route('registro')));

        return $request->routeIs('api.*') ? response()->json(['data' => $registro]) : view('caja-bancos::detalle', compact('seccion', 'registro'));
    }

    public function crear(Request $request)
    {
        return view('caja-bancos::formulario', ['seccion' => $request->route('seccion'), 'registro' => null, 'accion' => null]);
    }

    public function formularioAccion(Request $request)
    {
        $seccion = $request->route('seccion');
        $registro = $this->presentar($this->datos->detalle($seccion, (int) $request->route('registro')));
        $accion = $request->route('accion');

        return view('caja-bancos::formulario', compact('seccion', 'registro', 'accion'));
    }

    public function opciones(Request $request)
    {
        $filtros = $this->filtros($request);
        $tipo = $request->route('tipo');
        $cuentas = $this->datos->listado('cuentas', $request->route('filtrar') ? $filtros : ['activo' => true] + $filtros);
        if ($tipo !== 'todas') $cuentas->where('tipo', $tipo);
        $resultados = $cuentas->paginate(20);

        return response()->json([
            'results' => $resultados->getCollection()->map(fn ($cuenta) => ['id' => $cuenta->id, 'text' => $cuenta->codigo.' · '.$cuenta->nombre.' ('.$cuenta->moneda.')']),
            'pagination' => ['more' => $resultados->hasMorePages()],
        ]);
    }

    public function candidatos(Request $request)
    {
        $filtros = $this->filtros($request);
        $resultados = $this->banco->candidatos((int) $request->route('registro'), $filtros['q'] ?? null)->paginate(20);
        if ($request->routeIs('api.*')) {
            return response()->json($resultados->through($this->presentar(...)));
        }

        return response()->json([
            'results' => $resultados->getCollection()->map(fn ($fila) => ['id' => $fila->id, 'text' => $fila->fecha.' · '.$fila->concepto.($fila->referencia ? ' · '.$fila->referencia : '')]),
            'pagination' => ['more' => $resultados->hasMorePages()],
        ]);
    }

    public function store(Request $request)
    {
        $seccion = $request->route('seccion');
        $repetida = false;
        if ($seccion === 'cuentas') {
            $registro = $this->libro->cuenta($this->camposCuenta($request), null);
        } elseif ($seccion === 'cierres') {
            $datos = $request->validate(['id_cuenta' => ['required', 'integer', 'min:1']]);
            $registro = $this->libro->abrir((int) $datos['id_cuenta']);
        } elseif ($seccion === 'extractos') {
            $registro = $this->banco->importar($this->camposExtracto($request));
        } else {
            $datos = $this->camposOperacion($request, $seccion);
            $tipo = $seccion === 'movimientos' ? $datos['tipo'] : config('caja-bancos.operaciones.'.$seccion)[0];
            [$registro, $repetida] = $this->libro->registrar($tipo, $datos);
        }

        return $this->responder($request, $seccion, $registro, $repetida ? 200 : 201, $repetida ? 'La operación ya estaba registrada.' : 'Registro guardado.');
    }

    public function accion(Request $request)
    {
        $seccion = $request->route('seccion');
        $id = (int) $request->route('registro');
        $accion = $request->route('accion');
        $registro = match ($accion) {
            'editar' => $this->libro->cuenta($this->camposCuenta($request, $id), $id),
            'cerrar' => $this->libro->cerrar($id, $request->validate([
                'efectivo_contado' => ['required', 'numeric', 'decimal:0,2', 'between:0,1000000000'], 'observacion' => ['nullable', 'string', 'max:500'],
            ])),
            'conciliar' => $this->banco->conciliar($id, (int) $request->validate(['id_asiento' => ['required', 'integer', 'min:1']])['id_asiento']),
            'desconciliar' => $this->banco->desconciliar($id, $request->validate(['motivo' => ['required', 'string', 'max:250']])['motivo']),
            'revertir' => $this->libro->revertir($seccion, $id, $request->validate(['motivo' => ['required', 'string', 'max:250']])['motivo']),
        };

        return $this->responder($request, $seccion, $registro, 200, 'Cambio registrado.');
    }

    public function exportar(Request $request)
    {
        $filtros = $this->filtros($request);

        return response()->streamDownload(function () use ($filtros): void {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, ['Fecha', 'Cuenta', 'Tipo', 'Concepto', 'Referencia', 'Moneda', 'Variación', 'Saldo anterior', 'Saldo'], ';', '"', '');
            foreach ($this->datos->listado('reportes', $filtros)->cursor() as $registro) {
                $fila = $this->presentar($registro);
                $texto = [];
                foreach (['cuenta', 'concepto', 'referencia'] as $campo) {
                    $valor = $fila[$campo] ?? '';
                    $texto[$campo] = preg_match('/^[\x00-\x20]*[=+@-]|^[\t\r\n]/', $valor) ? "'".$valor : $valor;
                }
                fputcsv($archivo, [$fila['fecha'], $texto['cuenta'], config('caja-bancos.tipos.'.$fila['tipo']), $texto['concepto'], $texto['referencia'],
                    $fila['moneda'], $fila['variacion'], $fila['saldo_anterior'], $fila['saldo']], ';', '"', '');
            }
            fclose($archivo);
        }, 'libro-caja-bancos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function camposCuenta(Request $request, ?int $id = null): array
    {
        $tipo = $id === null ? $request->input('tipo') : $this->datos->fila('cb_cuentas', $id)->tipo;
        $reglas = ['codigo' => ['required', 'string', 'max:40'], 'nombre' => ['required', 'string', 'max:160'],
            'activo' => [$id === null ? 'sometimes' : 'required', 'boolean']];
        if ($tipo === 'banco') {
            $reglas += ['banco' => ['required', 'string', 'max:120'], 'ultimos_digitos' => ['nullable', 'digits:4']];
        }
        if ($id === null) {
            $reglas += ['tipo' => ['required', 'in:banco,caja'], 'moneda' => ['required', Rule::in(config('caja-bancos.monedas'))]];
        }
        $datos = $request->validate($reglas);
        $datos['codigo'] = Str::upper($datos['codigo']);
        if (Str::length($datos['codigo']) > 40) {
            throw ValidationException::withMessages(['codigo' => 'El código debe tener hasta 40 caracteres en mayúsculas.']);
        }
        $datos['activo'] ??= true;

        return $datos;
    }

    private function camposExtracto(Request $request): array
    {
        if ($request->hasFile('archivo')) {
            $request->validate(['archivo' => ['required', 'file', 'extensions:csv', 'max:2048']]);
            $request->merge(['lineas' => (new ExtractoCsv)->leer($request->file('archivo'))]);
        }

        return $request->validate([
            'id_cuenta' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:160'],
            'lineas' => ['required', 'list', 'min:1', 'max:1000'],
            'lineas.*' => ['required', 'array:fecha,referencia,descripcion,importe'],
            'lineas.*.fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'lineas.*.referencia' => ['required', 'string', 'max:120'],
            'lineas.*.descripcion' => ['required', 'string', 'max:250'],
            'lineas.*.importe' => ['required', 'numeric', 'decimal:0,2', 'between:-1000000000,1000000000'],
        ]);
    }

    private function camposOperacion(Request $request, string $seccion): array
    {
        $reglas = [
            'id_cuenta' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'importe' => ['required', 'numeric', 'decimal:0,2', 'between:0.01,1000000000'],
            'concepto' => ['required', 'string', 'max:250'], 'referencia' => ['nullable', 'string', 'max:120'],
            'clave_operacion' => ['nullable', 'uuid'],
        ];
        if (in_array($seccion, ['transferencias', 'depositos'], true)) {
            $reglas['id_destino'] = ['required', 'integer', 'min:1', 'different:id_cuenta'];
        }
        if ($seccion === 'movimientos') $reglas['tipo'] = ['required', 'in:ingreso,egreso'];

        return $request->validate($reglas);
    }

    private function filtros(Request $request): array
    {
        $reglas = [
            'q' => ['nullable', 'string', 'max:120'], 'per_page' => ['nullable', 'integer', 'between:1,100'], 'page' => ['nullable', 'integer', 'between:1,100000'],
            'id_cuenta' => ['nullable', 'integer', 'min:1'], 'desde' => ['nullable', 'date_format:Y-m-d'], 'hasta' => ['nullable', 'date_format:Y-m-d'],
            'activo' => ['nullable', 'boolean'], 'conciliado' => ['nullable', 'boolean'], 'id_extracto' => ['nullable', 'integer', 'min:1'],
        ];
        if ($request->filled('desde')) $reglas['hasta'][] = 'after_or_equal:desde';
        $filtros = array_filter($request->validate($reglas), fn ($valor) => $valor !== null);
        unset($filtros['page']);

        return $filtros;
    }

    private function presentar(stdClass $registro): array
    {
        $datos = (array) $registro;
        unset($datos['huella']);
        foreach (['importe', 'saldo', 'saldo_actual', 'variacion', 'saldo_anterior', 'saldo_apertura', 'saldo_cierre', 'efectivo_contado', 'diferencia', 'ingresos', 'egresos'] as $campo) {
            if (isset($datos[$campo])) $datos[$campo] = Decimal::decimal((int) $datos[$campo], 2);
        }
        if (isset($datos['activo'])) $datos['activo'] = (bool) $datos['activo'];
        if (isset($datos['asientos'])) $datos['asientos'] = $datos['asientos']->map($this->presentar(...));

        return $datos;
    }

    private function responder(Request $request, string $seccion, stdClass $registro, int $estado, string $mensaje)
    {
        if ($request->routeIs('api.*')) {
            $cabeceras = $estado === 201 ? ['Location' => route('api.v1.caja-bancos.'.$seccion.'.show', $registro->id)] : [];

            return response()->json(['data' => $this->presentar($registro)], $estado, $cabeceras);
        }
        if ($request->expectsJson()) return response()->json(['message' => $mensaje], $estado);

        return redirect()->route('caja-bancos.'.$seccion.'.index')->with('status', $mensaje);
    }
}
