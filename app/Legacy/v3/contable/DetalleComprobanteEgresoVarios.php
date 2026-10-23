<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DetalleComprobanteEgresoVarios extends Model
{
    private static $modelo = 'DetalleComprobanteEgresoVarios';
    protected $table = 'ct_detalle_comprobante_egreso_varios';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    
}
  