<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Domain\Entities\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ClienteResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var Cliente $cliente */
        $cliente = $this->resource;

        return [
            'id' => $cliente->id,
            'id_persona' => $cliente->idPersona,
            'tipo_identificacion' => $cliente->identificacion->tipo->value,
            'identificacion' => $cliente->identificacion->numero,
            'nombre' => $cliente->nombre,
            'email' => $cliente->email,
            'telefono' => $cliente->telefono,
            'direccion' => $cliente->direccion,
            'activo' => $cliente->activo,
            'nombres' => $cliente->perfil['nombres'],
            'apellidos' => $cliente->perfil['apellidos'],
            'ciudad' => $cliente->perfil['ciudad'] ?? null,
            'provincia' => $cliente->perfil['provincia'] ?? null,
            'zona' => $cliente->perfil['zona'] ?? null,
            'latitud' => isset($cliente->perfil['latitud']) ? (float) $cliente->perfil['latitud'] : null,
            'longitud' => isset($cliente->perfil['longitud']) ? (float) $cliente->perfil['longitud'] : null,
            'contactos' => $cliente->perfil['contactos'] ?? [],
            ...array_intersect_key($cliente->perfil, array_flip(array_keys(\App\Models\Persona::reglasPerfil()))),
        ];
    }
}
