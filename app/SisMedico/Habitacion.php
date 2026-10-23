<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'habitacion';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
    public function agenda()   
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }
    public function callcenter()   
    {
        return $this->belongsTo('Sis_medico\CallCenter', 'id_callcenter');
    }**/
    public function cama()
    {
        return $this->hasMany('Sis_medico\Cama','id_habitacion');
    }
    public function tipo(){

        return $this->belongsTo('Sis_medico\Tipo_Habitacion','id_tipo');
    }
    
   
}
 