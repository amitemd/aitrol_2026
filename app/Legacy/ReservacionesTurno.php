<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ReservacionesTurno extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reservaciones_turno';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function sala(){
        
        return $this->belongsTo('Sis_medico\Sala','id_sala');
    }
    public function hospital(){
        
        return $this->belongsTo('Sis_medico\Hospital','id_hospital');
    }
    public function paciente(){
        
        return $this->belongsTo('Sis_medico\User','cedula');
    }

    
}
