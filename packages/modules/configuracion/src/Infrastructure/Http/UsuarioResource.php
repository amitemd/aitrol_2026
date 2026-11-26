<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_persona' => $this->id_persona,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => (bool) $this->is_admin,
            'nombres' => $this->persona?->nombre,
            'apellidos' => $this->persona?->apellido,
            'email_contacto' => $this->persona?->email,
            'telefono' => $this->persona?->telefono,
            'direccion' => $this->persona?->direccion,
            ...($this->persona?->perfil() ?? []),
        ];
    }
}
