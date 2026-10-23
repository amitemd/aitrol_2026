<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaConglomerada extends Model
{
    protected $table = 'ct_detalle_venta_c';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

}
