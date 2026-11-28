<?php

namespace Aitrol\Proveedores\Infrastructure\Http;

use Aitrol\Proveedores\Domain\Entities\Proveedor;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProveedorResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var Proveedor $proveedor */
        $proveedor = $this->resource;

        return [
            'id' => $proveedor->id,
            'id_persona' => $proveedor->idPersona,
            'tipo_identificacion' => $proveedor->identificacion->tipo->value,
            'identificacion' => $proveedor->identificacion->numero,
            'nombre' => $proveedor->nombre,
            'email' => $proveedor->email,
            'telefono' => $proveedor->telefono,
            'direccion' => $proveedor->direccion,
            'activo' => $proveedor->activo,
            'nombres' => $proveedor->perfil['nombres'] ?? $proveedor->nombre,
            'apellidos' => $proveedor->perfil['apellidos'] ?? null,
            'ciudad' => $proveedor->perfil['ciudad'] ?? null,
            'provincia' => $proveedor->perfil['provincia'] ?? null,
            'contactos' => $proveedor->perfil['contactos'] ?? [],
            ...array_intersect_key($proveedor->perfil, Persona::reglasPerfil()),
        ];
    }
}
