<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetallePagoIngresoVarios extends Model
{
    private static $modelo = 'DetallePagoIngresoVarios';
    protected $table = 'ct_detalle_pago_ingreso_varios';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getDetalleValorRecibidoCompVarios($id)
    {
    return DetallePagoIngresoVarios::where('ct_detalle_pago_ingreso_varios.id_comprobante', $id)
        ->leftjoin('ct_tipo_pago as tp', 'tp.id', 'ct_detalle_pago_ingreso_varios.id_tipo')
        ->leftjoin('ct_tipo_tarjeta as tt', 'tt.id', 'ct_detalle_pago_ingreso_varios.id_tipo_tarjeta')
        ->leftjoin('ct_bancos as b', 'b.id', 'ct_detalle_pago_ingreso_varios.id_banco')
        ->get(['tp.nombre as tipo_pago', 'ct_detalle_pago_ingreso_varios.fecha', 'ct_detalle_pago_ingreso_varios.numero', 'b.nombre as banco', 'tt.nombre as tarjeta',
         'ct_detalle_pago_ingreso_varios.cuenta', 'ct_detalle_pago_ingreso_varios.girador', 'ct_detalle_pago_ingreso_varios.total']);
    }
}


