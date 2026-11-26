<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\UseCases\ClientesService;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final readonly class ClienteWebController
{
    public function __construct(private ClientesService $clientes) {}

    public function index(ListarClientesRequest $request): View
    {
        return view('clientes::index', [
            'clientes' => $request->paginar($this->clientes),
            'tipos' => TipoIdentificacion::cases(),
        ]);
    }

    public function create(): View
    {
        return view('clientes::form', ['cliente' => null, 'tipos' => TipoIdentificacion::cases()]);
    }

    public function edit(int $cliente): View
    {
        return view('clientes::form', ['cliente' => $this->clientes->consultar($cliente), 'tipos' => TipoIdentificacion::cases()]);
    }

    public function store(GuardarClienteRequest $request): JsonResponse|RedirectResponse
    {
        $this->clientes->registrar($request->datos());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cliente registrado.'], 201);
        }

        return to_route('clientes.index')->with('status', 'Cliente registrado.');
    }

    public function update(GuardarClienteRequest $request, int $cliente): RedirectResponse
    {
        $this->clientes->actualizar($cliente, $request->datos());

        return to_route('clientes.index')->with('status', 'Cliente actualizado.');
    }
}
