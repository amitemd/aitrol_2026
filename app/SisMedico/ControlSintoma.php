<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ControlSintoma extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'control_sintoma';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','cedula');
    }
    protected $guarded = [];
}