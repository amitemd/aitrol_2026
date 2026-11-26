<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\UseCases\ClientesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final readonly class ClienteApiController
{
    public function __construct(private ClientesService $clientes) {}

    public function index(ListarClientesRequest $request): AnonymousResourceCollection
    {
        return ClienteResource::collection($request->paginar($this->clientes));
    }

    public function show(int $cliente): ClienteResource
    {
        return new ClienteResource($this->clientes->consultar($cliente));
    }

    public function store(GuardarClienteRequest $request): JsonResponse
    {
        $cliente = $this->clientes->registrar($request->datos());

        return (new ClienteResource($cliente))->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.clientes.show', $cliente->id));
    }

    public function update(GuardarClienteRequest $request, int $cliente): ClienteResource
    {
        return new ClienteResource($this->clientes->actualizar($cliente, $request->datos()));
    }
}
