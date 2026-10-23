<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CamaPacienteLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cama_paciente_log';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cama()
    {
        return $this->belongsTo('Sis_medico\Cama','id_cama');
    }
    public function paciente(){
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

}
 