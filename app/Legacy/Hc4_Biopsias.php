<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc4_Biopsias extends Model
{
    //
    protected $table = 'hc4_biopsias';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function pacientes()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
    
    public function seguros()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro'); 
    }

    public function paciente_omni()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'hcid');
    }

    public function paciente_omni2()
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_paciente', 'id_paciente');
    }


    public function historia_clinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'hcid');
    }

}
  