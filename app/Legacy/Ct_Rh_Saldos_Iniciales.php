<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Saldos_Iniciales extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_saldos_iniciales';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_empl');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle','id_ct_rh_saldos_iniciales');   
    }

}
