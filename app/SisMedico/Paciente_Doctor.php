<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente_Doctor extends Model
{
    //
    protected $table = 'paciente_doctor';

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
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuario');
    }
}
