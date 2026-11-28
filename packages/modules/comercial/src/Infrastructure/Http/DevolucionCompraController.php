<?php

namespace Aitrol\Comercial\Infrastructure\Http;

use Aitrol\Comercial\Application\ComprasPendientes;
use Aitrol\Comercial\Application\DevolucionesCompra;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final readonly class DevolucionCompraController
{
    public function __construct(private DevolucionesCompra $devoluciones) {}

    public function index(Request $request, int $registro): JsonResponse|View
    {
        $datos = $this->devoluciones->seguimiento($registro);

        return $request->routeIs('api.*') ? response()->json(['data' => $datos]) : view('comercial::devoluciones-compra', $datos);
    }

    public function store(Request $request, int $registro): JsonResponse|RedirectResponse
    {
        if (! $request->routeIs('api.*') && is_array($request->input('lineas'))) {
            $request->merge(['lineas' => array_values(array_filter($request->input('lineas'),
                fn ($linea): bool => ! is_array($linea) || filled($linea['cantidad'] ?? null)))]);
        }
        $datos = $request->validate([
            'numero' => ['required', 'string', 'max:60'],
            'motivo' => ['required', 'string', 'max:250'],
            'lineas' => ['required', 'list', 'min:1', 'max:100'],
            'lineas.*' => ['required', 'array:id_linea_recepcion,cantidad'],
            'lineas.*.id_linea_recepcion' => ['required', 'integer', 'min:1', 'distinct'],
            'lineas.*.cantidad' => ['required', 'numeric', 'decimal:0,3', 'between:0.001,1000000'],
        ]);
        $nota = $this->devoluciones->registrar($registro, $datos);

        return $request->routeIs('api.*') || $request->expectsJson()
            ? response()->json(['data' => $nota], 201)
            : to_route('compras.documentos.devoluciones.index', $registro)->with('status', 'Devolución registrada. La nota de crédito se aplicó al saldo del proveedor.');
    }

    public function pendientes(Request $request, ComprasPendientes $compras): JsonResponse|View
    {
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'estado' => ['sometimes', Rule::in(['todos', 'por-recibir', 'por-facturar'])]]);
        $estado = $filtros['estado'] ?? 'todos';
        $busqueda = $filtros['q'] ?? null;
        $datos = $compras->reporte($busqueda, $estado);

        return $request->routeIs('api.*') ? response()->json(['data' => $datos]) : view('comercial::compras-pendientes', $datos + compact('estado', 'busqueda'));
    }
}
