<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'id_tipo_identificacion', 'identificacion', 'nombre', 'apellido', 'telefono', 'celular',
        'direccion', 'id_ciudad', 'email', 'observacion', 'fecha_nacimiento',
        'urbanizacion', 'etapa', 'manzana', 'villa', 'referencia',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $persona): void {
            $persona->id_usuario_creacion = auth()->id();
        });

        static::saving(function (self $persona): void {
            if (! $persona->exists || $persona->isDirty()) {
                $persona->id_usuario_modificacion = auth()->id();
            }
        });
    }

    public static function buscarOCrearConBloqueo(array $documento, array $datos): self
    {
        $ahora = now();
        // La clave única resuelve la creación simultánea entre empresas.
        (new self)->newModelQuery()->getQuery()->upsert($documento + $datos + [
            'id_usuario_creacion' => auth()->id(),
            'id_usuario_modificacion' => auth()->id(),
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ], ['id_tipo_identificacion', 'identificacion'], ['identificacion']);

        return self::query()->where($documento)->lockForUpdate()->firstOrFail();
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }

    public static function reglasPerfil(): array
    {
        return [
            'celular' => ['nullable', 'string', 'max:100'],
            'id_ciudad' => ['nullable', 'integer', 'exists:ciudades,id'],
            'fecha_nacimiento' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'observacion' => ['nullable', 'string', 'max:3000'],
            'urbanizacion' => ['nullable', 'string', 'max:100'],
            'etapa' => ['nullable', 'string', 'max:100'],
            'manzana' => ['nullable', 'string', 'max:100'],
            'villa' => ['nullable', 'string', 'max:100'],
            'referencia' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function perfil(): array
    {
        return [
            ...$this->only(array_keys(self::reglasPerfil())),
            'fecha_nacimiento' => $this->fecha_nacimiento?->toDateString(),
            'id_ciudad' => $this->id_ciudad === null ? null : (int) $this->id_ciudad,
        ];
    }
}
