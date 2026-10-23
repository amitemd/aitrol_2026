<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetalleComprobanteIngreso extends Model
{
    private static $modelo = 'DetalleComprobanteIngreso';
    protected $table = 'ct_detalle_comprobante_ingreso';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getDetalleDCliente($id)
    {
        return DetalleComprobanteIngreso::where('ct_detalle_comprobante_ingreso.id_comprobante', $id)
            ->join('ct_ventas as v', 'v.id', 'ct_detalle_comprobante_ingreso.id_factura')
            ->get(
                    [
                            'ct_detalle_comprobante_ingreso.id_factura as factura', 
                            'ct_detalle_comprobante_ingreso.fecha', 
                            'v.tipo', 
                            'ct_detalle_comprobante_ingreso.secuencia_factura', 
                            'ct_detalle_comprobante_ingreso.observaciones',
                            'ct_detalle_comprobante_ingreso.total_factura', 
                            'ct_detalle_comprobante_ingreso.total', 
                            DB::raw('ct_detalle_comprobante_ingreso.total_factura - ct_detalle_comprobante_ingreso.total as saldo'),
                            DB::raw('DATE_ADD(v.fecha, INTERVAL IFNULL(v.dias_plazo,0) DAY) as fecha_vencimiento')
                    ]
            );
    }

    public function ventas()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Ventas', 'id_factura');
    }
    public function ingreso()
    {
        return $this->belongsTo('Sis_medico\v3\contable\ComprobanteIngreso','id_comprobante');
    }

    
}
 