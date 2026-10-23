<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'piso';

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

    public function habitacion()
    {
        return $this->hasMany('Sis_medico\Habitacion','id_piso');
    }

}
 