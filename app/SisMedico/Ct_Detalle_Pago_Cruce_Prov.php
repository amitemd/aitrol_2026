<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Pago_Cruce_Prov extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_pago_cruce_pro';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function tras(){
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Egreso','id_comp_ingreso');
    }
  
    
}
