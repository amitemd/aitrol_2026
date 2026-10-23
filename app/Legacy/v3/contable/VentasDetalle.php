<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class VentasDetalle extends Model
{
    private static $modelo = 'Ventas';
    protected $table = 'ct_detalle_venta';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';
}
