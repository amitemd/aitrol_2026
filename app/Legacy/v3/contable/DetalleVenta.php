<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'ct_detalle_venta';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

}
