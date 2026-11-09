<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DetalleComprobanteEgresoMasivo extends Model
{
    private static $modelo = 'Detalle comprobante egreso masivo';
    protected $table = 'ct_detalle_comprobante_egreso_masivo';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    
}
  