<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetalleComprobanteIngresoVarios extends Model
{
    private static $modelo = 'DetalleComprobanteIngresoVarios';
    protected $table = 'ct_detalle_comprobante_ingreso_varios';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getDetalleCompVarios($id) 
    {
    return DetalleComprobanteIngresoVarios::where('ct_detalle_comprobante_ingreso_varios.id_comprobante_varios', $id)
        ->get(['ct_detalle_comprobante_ingreso_varios.codigo', 'ct_detalle_comprobante_ingreso_varios.nombre', 'ct_detalle_comprobante_ingreso_varios.haber']);
    }
    
}
  