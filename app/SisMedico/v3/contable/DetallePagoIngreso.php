<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetallePagoIngreso extends Model
{
    private static $modelo = 'DetallePagoIngreso';
    protected $table = 'ct_detalle_pago_ingreso';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function cabecera_ingreso(){
        
        return $this->belongsTo('Sis_medico\v3\contable\ComprobanteIngreso','id_comprobante');
    }

    public function cabecera_ingresov(){
        return $this->belongsTo('Sis_medico\v3\contable\ComprobanteIngresoVarios','id_comprobante');
    }

    public function tipo_pago(){
        
        return $this->belongsTo('Sis_medico\v3\contable\TipoPago','id_tipo', 'id');
    }

    public static function getDetalleValorRecibido($id)
    {
        return DetallePagoIngreso::where('ct_detalle_pago_ingreso.id_comprobante', $id)
            ->leftjoin('ct_tipo_pago as tp', 'tp.id', 'ct_detalle_pago_ingreso.id_tipo')
            ->leftjoin('ct_tipo_tarjeta as tt', 'tt.id', 'ct_detalle_pago_ingreso.id_tipo_tarjeta')
            ->leftjoin('ct_bancos as b', 'b.id', 'ct_detalle_pago_ingreso.id_banco')
            ->get(['tp.nombre as tipo_pago', 'ct_detalle_pago_ingreso.fecha', 'ct_detalle_pago_ingreso.numero', 'b.nombre as banco', 'tt.nombre as tarjeta',
             'ct_detalle_pago_ingreso.cuenta', 'ct_detalle_pago_ingreso.girador', 'ct_detalle_pago_ingreso.total']);
    }
}
