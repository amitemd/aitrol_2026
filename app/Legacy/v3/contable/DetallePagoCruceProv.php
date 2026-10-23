<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetallePagoCruceProv extends Model
{
    protected $table = 'ct_detalle_pago_cruce_pro';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';


    public function tras(){
        return $this->belongsTo('Sis_medico\v3\contable\ComprobanteEgreso','id_comp_ingreso');
    }

 
}
