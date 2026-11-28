<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\PresupuestosCompra;
use Aitrol\Comercial\Application\SolicitudesCompra;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final readonly class SolicitudCompraController
{
    public function __construct(private PresupuestosCompra $presupuestos, private SolicitudesCompra $solicitudes) {}

    public function presupuestos(Request $request, ContextoOperativo $contexto): View|JsonResponse
    {
        $datos = $request->validate(['anio' => ['sometimes', ...$contexto->reglasPeriodo()]]);
        $anio = (int) ($datos['anio'] ?? $contexto->periodo() ?? $contexto->anioActual());
        $presupuestos = $this->presupuestos->listar($anio);
        if ($request->routeIs('api.*')) {
            return response()->json(['data' => $presupuestos]);
        }
        $centros = Gate::allows('administrar-configuracion') ? $this->presupuestos->centrosActivos() : collect();

        return view('comercial::presupuestos-compra', compact('presupuestos', 'centros', 'anio'));
    }

    public function guardarPresupuesto(Request $request, ContextoOperativo $contexto): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_centro_costo' => ['required', 'integer', 'min:1'],
            'anio' => $contexto->reglasPeriodo(),
            'monto' => ['required', 'numeric', 'decimal:0,2', 'between:0,10000000000000'],
        ]);
        $guardado = $this->presupuestos->guardar((int) $datos['id_centro_costo'], (int) $datos['anio'], $datos['monto']);

        return $request->routeIs('api.*')
            ? response()->json(['data' => $guardado]) : to_route('compras.presupuestos.index', ['anio' => $datos['anio']])->with('status', 'Presupuesto guardado.');
    }

    public function index(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['estado' => ['nullable', Rule::in(array_keys(SolicitudesCompra::ESTADOS))]]);
        $estado = $datos['estado'] ?? null;
        $solicitudes = $this->solicitudes->listar($estado);

        return $request->routeIs('api.*') ? response()->json(['data' => $solicitudes]) : view('comercial::solicitudes-compra', compact('solicitudes', 'estado'));
    }

    public function create(int $presupuesto, EmpresaActiva $empresa): View
    {
        $seleccion = $this->presupuestos->presentar($this->presupuestos->consultar($presupuesto));
        abort_unless($seleccion['centro_activo'], 409, 'El presupuesto pertenece a un centro de costo inactivo.');
        $lineas = collect(old('lineas', [[]]))->filter(fn ($linea): bool => is_array($linea))->values();
        $productos = DB::table('inv_productos')->where('id_empresa', $empresa->id())
            ->whereIn('id', $lineas->pluck('id_producto')->filter(fn ($id): bool => is_numeric($id)))->get(['id', 'codigo', 'nombre'])->keyBy('id');

        return view('comercial::solicitud-compra-formulario', ['presupuesto' => $seleccion, 'lineas' => $lineas, 'productos' => $productos]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_presupuesto' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'motivo' => ['required', 'string', 'max:500'],
            'lineas' => ['required', 'list', 'min:1', 'max:100'],
            'lineas.*' => ['required', 'array:descripcion,id_producto,cantidad,precio,tasa'],
            'lineas.*.id_producto' => ['nullable', 'integer', 'min:1'],
            'lineas.*.descripcion' => ['required_without:lineas.*.id_producto', 'nullable', 'string', 'max:255'],
            'lineas.*.cantidad' => ['required', 'numeric', 'decimal:0,3', 'between:0.001,1000000'],
            'lineas.*.precio' => ['required', 'numeric', 'decimal:0,4', 'between:0.0001,100000'],
            'lineas.*.tasa' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,100'],
        ]);
        $id = $this->solicitudes->crear($datos);

        return $this->resultado($request, $id, 'Solicitud registrada para revisión.', 201);
    }

    public function show(Request $request, int $solicitud): View|JsonResponse
    {
        $detalle = $this->solicitudes->detalle($solicitud);

        return $request->routeIs('api.*') ? response()->json(['data' => $detalle]) : view('comercial::solicitud-compra-detalle', $detalle);
    }

    public function decidir(Request $request, int $solicitud): JsonResponse|RedirectResponse
    {
        $datos = $request->validate(['decision' => ['required', 'in:reservada,rechazada'], 'motivo' => ['required_if:decision,rechazada', 'nullable', 'string', 'max:500']]);
        $this->solicitudes->decidir($solicitud, $datos['decision'], $datos['motivo'] ?? null);

        return $this->resultado($request, $solicitud, $datos['decision'] === 'reservada' ? 'Solicitud aprobada y presupuesto reservado.' : 'Solicitud rechazada.');
    }

    public function convertir(Request $request, int $solicitud): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'id_tercero' => ['required', 'integer', 'min:1'], 'numero' => ['required', 'string', 'max:60'], 'fecha' => ['required', 'date_format:Y-m-d'],
        ]);
        $this->solicitudes->convertir($solicitud, $datos);

        return $this->resultado($request, $solicitud, 'Orden generada y presupuesto comprometido.', 201);
    }

    public function cancelar(Request $request, int $solicitud): JsonResponse|RedirectResponse
    {
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:500']]);
        $this->solicitudes->cancelar($solicitud, $datos['motivo']);

        return $this->resultado($request, $solicitud, 'Solicitud cancelada.');
    }

    private function resultado(Request $request, int $id, string $mensaje, int $codigo = 200): JsonResponse|RedirectResponse
    {
        return $request->routeIs('api.*') ? response()->json(['data' => $this->solicitudes->detalle($id)], $codigo)
            : to_route('compras.solicitudes.show', $id)->with('status', $mensaje);
    }
}
