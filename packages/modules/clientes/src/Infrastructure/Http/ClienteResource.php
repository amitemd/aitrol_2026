<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Domain\Entities\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ClienteResource extends JsonResource
{
    /** @return array<string, int|string|bool|null> */
    public function toArray(Request $request): array
    {
        /** @var Cliente $cliente */
        $cliente = $this->resource;

        return [
            'id' => $cliente->id,
            'tipo_identificacion' => $cliente->identificacion->tipo->value,
            'identificacion' => $cliente->identificacion->numero,
            'nombre' => $cliente->nombre,
            'email' => $cliente->email,
            'telefono' => $cliente->telefono,
            'direccion' => $cliente->direccion,
            'activo' => $cliente->activo,
        ];
    }
}
