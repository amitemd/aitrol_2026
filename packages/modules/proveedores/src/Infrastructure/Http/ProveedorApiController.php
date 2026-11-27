<?php

namespace Aitrol\Proveedores\Infrastructure\Http;

use Aitrol\Proveedores\Application\UseCases\ProveedoresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final readonly class ProveedorApiController
{
    public function __construct(private ProveedoresService $proveedores) {}

    public function index(ListarProveedoresRequest $request): AnonymousResourceCollection
    {
        return ProveedorResource::collection($request->paginar($this->proveedores));
    }

    public function show(int $proveedor): ProveedorResource
    {
        return new ProveedorResource($this->proveedores->consultar($proveedor));
    }

    public function store(GuardarProveedorRequest $request): JsonResponse
    {
        $proveedor = $this->proveedores->registrar($request->datos());

        return (new ProveedorResource($proveedor))->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.proveedores.show', $proveedor->id));
    }

    public function update(GuardarProveedorRequest $request, int $proveedor): ProveedorResource
    {
        return new ProveedorResource($this->proveedores->actualizar($proveedor, $request->datos()));
    }

    public function destroy(int $proveedor): Response
    {
        $this->proveedores->eliminar($proveedor);

        return response()->noContent();
    }
}
