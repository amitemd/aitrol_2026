<?php

namespace Aitrol\Configuracion\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class MenuModel extends Model
{
    protected $table = 'menu';

    protected $fillable = ['padre_id', 'nombre', 'ruta', 'icono', 'orden', 'activo', 'solo_administrador'];

    protected function casts(): array
    {
        return [
            'padre_id' => 'integer',
            'orden' => 'integer',
            'activo' => 'boolean',
            'solo_administrador' => 'boolean',
            'protegido' => 'boolean',
        ];
    }
}
