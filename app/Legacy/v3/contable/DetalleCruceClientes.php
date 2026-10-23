<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
class DetalleCruceClientes extends Model
{
    private static $modelo = 'ct_detalle_cruce_clientes';
    protected $table = 'ct_detalle_cruce_clientes';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';
    
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Ct_Cruce_Valores_Cliente','id_comprobante');
    }

    public static function getDetalleClienteValores($id)
    {
        return DetalleCruceClientes::where('ct_detalle_cruce_clientes.id_comprobante', $id)
        ->get(['ct_detalle_cruce_clientes.fecha', 'ct_detalle_cruce_clientes.secuencia_factura', 'ct_detalle_cruce_clientes.observaciones', 'ct_detalle_cruce_clientes.total_factura', 'ct_detalle_cruce_clientes.total']);
    }

}
