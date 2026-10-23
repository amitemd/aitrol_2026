<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ControlTemperatura extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'control_temperatura';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','responsable');
    }
    public function area_nevera(){
        
        return $this->belongsTo('Sis_medico\ControlTemparea','id');
    }

    protected $guarded = [];
}