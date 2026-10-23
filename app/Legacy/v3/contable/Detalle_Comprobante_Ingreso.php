<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Detalle_Comprobante_Ingreso extends Model
{
    private static $modelo = 'Detalle Comprobante Ingreso';
    protected $table = 'ct_detalle_comprobante_ingreso';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
