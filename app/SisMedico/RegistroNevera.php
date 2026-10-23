<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class RegistroNevera extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registro_nevera';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */


    protected $guarded = [];

    
    public function temperatura_uno(){
        
        return $this->hasOne('Sis_medico\TemperaturaNevera','id','temp_primerhorario');
    }
    public function temperatura_dos(){
        
        return $this->hasOne('Sis_medico\TemperaturaNevera','id','temp_seghorario');
    }
    public function nombre_nevera(){
        return $this->hasOne('Sis_medico\Bodegas_Nevera','id','id_nevera');
    }
    
}