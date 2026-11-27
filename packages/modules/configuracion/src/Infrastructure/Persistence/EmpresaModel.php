<?php

namespace Aitrol\Configuracion\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class EmpresaModel extends Model
{
    use SoftDeletes;

    protected $table = 'erp_empresas';

    protected $fillable = ['ruc', 'razon_social', 'nombre_comercial', 'email', 'telefono', 'direccion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
