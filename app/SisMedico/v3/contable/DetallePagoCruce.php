<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetallePagoCruce extends Model
{
    protected $table = 'ct_detalle_pago_cruce';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';  

    public static function getDetalleValoraFavor($id)
    {
        return  DetallePagoCruce::where('ct_detalle_pago_cruce.id_comprobante', $id)->get(['ct_detalle_pago_cruce.fecha', 'ct_detalle_pago_cruce.numero', 'ct_detalle_pago_cruce.concepto', 'ct_detalle_pago_cruce.valor_ant', 'ct_detalle_pago_cruce.valor']);
    }
 
}
