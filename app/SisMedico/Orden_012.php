<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden_012 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_012';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function evolucion(){
        return $this->belongsTo('Sis_medico\Hc_Evolucion','id_hc_evolucion');    
    }

    public function paciente(){
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');    
    }

    public function doctor(){
        return $this->belongsTo('Sis_medico\User','id_doctor_firma');    
    }

    public function doctor1(){
        return $this->belongsTo('Sis_medico\User','id_doctor_solicita');    
    }

    public function observacion(){
        return $this->belongsTo('Sis_medico\Hc_Evolucion','id_hc_evolucion');    
    }
    public function empresa(){
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');    
    }
    public function orden(){
        return $this->belongsTo('Sis_medico\Orden','id_orden');    
    }
}
