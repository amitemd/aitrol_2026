<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente_Observaciones extends Model
{
    //
    protected $table = 'paciente_observaciones';

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
}
