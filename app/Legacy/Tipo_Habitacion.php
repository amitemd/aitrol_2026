<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo_Habitacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_habitacion';

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

   
}
 