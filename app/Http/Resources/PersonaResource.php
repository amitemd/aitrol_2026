<?php

namespace App\Http\Resources;

use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identificacion' => $this->identificacion,
            'tipo_identificacion' => TipoIdentificacion::desdeCatalogo((int) $this->id_tipo_identificacion)->value,
            'nombres' => $this->nombre,
            'apellidos' => $this->apellido,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'direccion' => $this->direccion,
            'fecha_nacimiento' => $this->fecha_nacimiento?->toDateString(),
            'email' => $this->email,
            'telefono' => $this->telefono,
            'celular' => $this->celular,
        ];
    }
}
