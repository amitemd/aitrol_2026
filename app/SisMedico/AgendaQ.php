<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AgendaQ extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agenda_quirofano';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /*public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    //Agregada 8/04/2019
    public function doctor1()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor1');
    }

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_procedimiento');
    }

    public function historia_clinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id', 'id_agenda');
    }

    public function historia_clinica_1()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id_paciente', 'id_paciente');
    }

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }*/
    public function doctor(){
        return $this->belongsTo('Sis_medico\User', 'id_doctor');
    }
    public function paciente(){
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }
    
}
 