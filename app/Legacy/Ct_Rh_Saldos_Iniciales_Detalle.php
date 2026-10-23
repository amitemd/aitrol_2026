<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Saldos_Iniciales_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_saldos_iniciales_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function saldos(){
        
        return $this->belongsTo('Sis_medico\Ct_Rh_Saldos_Iniciales','id_ct_rh_saldos_iniciales');
    
    }


}
