<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente_Alergia extends Model
{
    //
    protected $table = 'paciente_alergia';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
    public function principio_activo()
    {
        return $this->belongsTo('Sis_medico\Principio_Activo', 'id_principio_activo');
    }
}
