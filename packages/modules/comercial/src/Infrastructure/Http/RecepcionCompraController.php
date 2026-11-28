<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\RecepcionesCompra;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use App\Services\FacturaVenta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final readonly class RecepcionCompraController
{
    public function __construct(private RecepcionesCompra $recepciones) {}

    public function index(Request $request, int $orden, EmpresaActiva $empresa, ContextoOperativo $contexto): JsonResponse|View
    {
        $seguimiento = $this->recepciones->seguimiento($orden);
        if ($request->routeIs('api.*')) {
            return response()->json(['data' => $seguimiento]);
        }

        $puedeRecibir = Gate::allows('permiso', ['compras.ordenes-compra.index', 'actualizar'])
            && Gate::allows('permiso', ['inventarios.movimientos.index', 'escribir']);
        $puedeFacturar = Gate::allows('permiso', ['compras.documentos.index', 'escribir']);
        $puedeAprobarDiferencias = Gate::allows('permiso', ['contabilidad.asientos.index', 'escribir']);
        $bodegas = $puedeRecibir ? DB::table('inv_bodegas')->where(['id_empresa' => $empresa->id(), 'id_sucursal' => $contexto->sucursal()->id, 'activo' => true])
            ->orderBy('nombre')->orderBy('id')->get(['id', 'codigo', 'nombre']) : collect();

        return view('comercial::recepciones-compra', $seguimiento + compact('puedeRecibir', 'puedeFacturar', 'puedeAprobarDiferencias', 'bodegas'));
    }

    public function store(Request $request, int $orden): JsonResponse|RedirectResponse
    {
        $this->quitarLineasVaciasDelFormulario($request);
        $datos = $request->validate([
            'id_bodega' => ['required', 'integer', 'min:1'],
            'clave_operacion' => ['required', 'uuid'],
            'observacion' => ['nullable', 'string', 'max:500'],
            'lineas' => ['required', 'list', 'min:1', 'max:100'],
            'lineas.*' => ['required', 'array:id_linea_orden,cantidad'],
            'lineas.*.id_linea_orden' => ['required', 'integer', 'min:1', 'distinct'],
            'lineas.*.cantidad' => ['required', 'numeric', 'decimal:0,3', 'between:0.001,1000000'],
        ]);
        [$recepcion, $repetida] = $this->recepciones->recibir($orden, $datos);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $recepcion], $repetida ? 200 : 201)
            : to_route('compras.ordenes-compra.recepciones.index', $orden)->with('status', 'Recepción contabilizada e ingresada a la bodega.');
    }

    public function facturar(Request $request, int $orden): JsonResponse|RedirectResponse
    {
        $this->quitarLineasVaciasDelFormulario($request);
        $datos = $request->validate([
            'recepciones' => ['required', 'list', 'min:1', 'max:25'],
            'recepciones.*' => ['required', 'integer', 'min:1', 'distinct'],
            'modo_cotejo' => ['sometimes', Rule::in(['completo', 'parcial'])],
            'motivo_diferencia' => ['prohibited_unless:modo_cotejo,parcial', 'nullable', 'string', 'max:500'],
            'numero' => ['required', 'string', 'max:60'],
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guayaquil')->toDateString()],
            'forma_pago' => ['required', Rule::in(array_keys(FacturaVenta::PAGOS))],
            'plazo_dias' => ['nullable', 'integer', 'between:0,3650'],
            'observacion' => ['nullable', 'string', 'max:500'],
            'subtotal' => ['required', 'numeric', 'decimal:0,2', 'between:0.01,10000000000000'],
            'impuesto' => ['required', 'numeric', 'decimal:0,2', 'between:0,10000000000000'],
            'lineas' => ['required', 'list', 'min:1', 'max:100'],
            'lineas.*' => ['required', 'array:id_linea_orden,cantidad,precio,tasa,descuento'],
            'lineas.*.id_linea_orden' => ['required', 'integer', 'min:1', 'distinct'],
            'lineas.*.cantidad' => ['required', 'numeric', 'decimal:0,3', 'between:0.001,1000000'],
            'lineas.*.precio' => ['required', 'numeric', 'decimal:0,4', 'between:0.0001,100000'],
            'lineas.*.tasa' => ['prohibited_unless:modo_cotejo,parcial', 'nullable', 'numeric', 'decimal:0,2', 'between:0,100'],
            'lineas.*.descuento' => ['prohibited_unless:modo_cotejo,parcial', 'nullable', 'numeric', 'decimal:0,2', 'between:0,100000000000'],
        ]);
        $factura = $this->recepciones->facturar($orden, $datos);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $factura], 201)
            : to_route('compras.ordenes-compra.recepciones.index', $orden)->with('status', 'Factura cotejada y contabilizada en cuentas por pagar.');
    }

    public function anular(Request $request, int $orden, int $recepcion): Response|RedirectResponse
    {
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:250']]);
        $this->recepciones->anular($orden, $recepcion, $datos['motivo']);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->noContent()
            : to_route('compras.ordenes-compra.recepciones.index', $orden)->with('status', 'Recepción revertida. La cantidad vuelve a quedar pendiente en la orden.');
    }

    private function quitarLineasVaciasDelFormulario(Request $request): void
    {
        if ($request->routeIs('api.*')) {
            return;
        }
        $lineas = $request->input('lineas');
        if (is_array($lineas)) {
            $request->merge(['lineas' => array_values(array_filter($lineas, fn ($fila): bool => ! is_array($fila)
                || filled($fila['cantidad'] ?? null) || filled($fila['precio'] ?? null)
                || filled($fila['tasa'] ?? null) || filled($fila['descuento'] ?? null)))]);
        }
    }
}
