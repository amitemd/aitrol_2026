<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ComprobanteEgreso extends Model
{

    public static $modelo = 'comprobante_egreso';
    protected $table = 'ct_comprobante_egreso';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';


    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Proveedor', 'id_proveedor', 'id');
        //return $this->belongsTo('Sis_medico\Ct_Acreedores', 'id');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleComprobanteEgreso', 'id_comprobante');
    }
}
