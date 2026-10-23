<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ProductosInsumos extends Model
{
    protected $table = 'ct_productos_insumos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    protected $keyType = 'string';

}
