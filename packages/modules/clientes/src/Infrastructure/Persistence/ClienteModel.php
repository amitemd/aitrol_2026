<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class ClienteModel extends Model
{
    protected $table = 'erp_clientes';

    protected $fillable = [
        'tipo_identificacion', 'identificacion', 'nombre', 'email', 'telefono', 'direccion', 'activo',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
