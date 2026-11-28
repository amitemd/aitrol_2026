<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\AprobacionesOrdenCompra;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final readonly class AprobacionOrdenCompraController
{
    public function __construct(private AprobacionesOrdenCompra $aprobaciones) {}

    public function index(Request $request): View|JsonResponse
    {
        $filtros = $request->validate([
            'estado' => ['sometimes', Rule::in(['pendiente', 'aprobada', 'rechazada', 'anulada'])],
            'q' => ['nullable', 'string', 'max:120'],
        ]);
        $estado = $filtros['estado'] ?? 'pendiente';
        $busqueda = $filtros['q'] ?? null;
        $ordenes = $this->aprobaciones->listar($estado, $busqueda);

        return $request->routeIs('api.*')
            ? response()->json(['data' => $ordenes])
            : view('comercial::aprobaciones-compra', compact('ordenes', 'estado', 'busqueda'));
    }

    public function show(Request $request, int $orden): View|JsonResponse
    {
        $detalle = $this->aprobaciones->detalle($orden);

        return $request->routeIs('api.*') ? response()->json(['data' => $detalle]) : view('comercial::aprobacion-compra', $detalle);
    }

    public function decidir(Request $request, int $orden): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'decision' => ['required', Rule::in(AprobacionesOrdenCompra::DECISIONES)],
            'motivo' => ['required_if:decision,rechazada', 'nullable', 'string', 'max:500'],
        ]);
        $detalle = $this->aprobaciones->decidir($orden, $datos['decision'], $datos['motivo'] ?? null);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $detalle])
            : to_route('compras.aprobaciones.show', $orden)->with('status', $datos['decision'] === 'aprobada' ? 'Orden aprobada.' : 'Orden rechazada.');
    }

    public function politica(Request $request): View|JsonResponse
    {
        $politica = $this->aprobaciones->politica();

        return $request->routeIs('api.*') ? response()->json(['data' => $politica]) : view('comercial::politica-aprobacion-compra', compact('politica'));
    }

    public function guardarPolitica(Request $request): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'activa' => ['required', 'boolean'],
            'monto_minimo' => ['required_if:activa,true,1', 'nullable', 'numeric', 'decimal:0,2', 'between:0,10000000000000'],
        ]);
        $politica = $this->aprobaciones->guardarPolitica((bool) $datos['activa'], $datos['monto_minimo'] ?? 0);

        return $request->routeIs('api.*') || $request->expectsJson() ? response()->json(['data' => $politica])
            : to_route('compras.aprobaciones.politica')->with('status', 'Política de aprobación guardada.');
    }
}
