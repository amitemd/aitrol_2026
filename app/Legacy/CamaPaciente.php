<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CamaPaciente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cama_paciente';

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
        return $this->belongsTo('Sis_medico\Cama','id_cama');
    }
    public function paciente(){
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

}
 