<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Persona extends Model
{
    public const COMPONENTES_NOMBRE = [
        'primer_nombre' => ['Primer nombre', 80],
        'segundo_nombre' => ['Segundo nombre', 80],
        'primer_apellido' => ['Primer apellido', 50],
        'segundo_apellido' => ['Segundo apellido', 50],
    ];

    protected $table = 'personas';

    protected $fillable = [
        'id_tipo_identificacion', 'identificacion', 'nombre', 'apellido', 'telefono', 'celular',
        'direccion', 'id_ciudad', 'email', 'observacion', 'fecha_nacimiento',
        'urbanizacion', 'etapa', 'manzana', 'villa', 'referencia',
        'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido',
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
            $campos = array_keys(self::COMPONENTES_NOMBRE);
            if ($persona->isDirty($campos) && filled($persona->primer_nombre)) {
                $nombre = self::normalizarNombre($persona->only($campos));
                $persona->nombre = $nombre['nombres'];
                $persona->apellido = $nombre['apellidos'];
            } elseif ($persona->isDirty(['nombre', 'apellido']) && filled($persona->primer_nombre)) {
                foreach ($campos as $campo) {
                    $persona->{$campo} = null;
                }
            }

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

    public static function normalizarNombre(array $datos): array
    {
        if (blank($datos['primer_nombre'] ?? null)) {
            return array_diff_key($datos, self::COMPONENTES_NOMBRE);
        }
        $datos += array_fill_keys(array_keys(self::COMPONENTES_NOMBRE), null);
        $datos['nombres'] = trim($datos['primer_nombre'].' '.$datos['segundo_nombre']);
        $datos['apellidos'] = trim($datos['primer_apellido'].' '.$datos['segundo_apellido']) ?: null;

        return $datos;
    }

    public static function reglasPerfil(): array
    {
        return [
            'primer_nombre' => ['nullable', 'required_with:segundo_nombre,primer_apellido,segundo_apellido', 'string', 'max:80'],
            'segundo_nombre' => ['nullable', 'string', 'max:80'],
            'primer_apellido' => ['nullable', 'string', 'max:50'],
            'segundo_apellido' => ['nullable', 'string', 'max:50'],
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
