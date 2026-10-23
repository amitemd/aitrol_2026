<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Detalle_Comprobante_Egreso extends Model
{
    private static $modelo = 'Detalle Comprobante Egreso';
    protected $table = 'ct_detalle_comprobante_egreso';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
