<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DevolucionProductos extends Model
{
    protected $table = 'ct_devolucion_productos';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    
}
