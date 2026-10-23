<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Interconsulta extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interconsulta';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }
    
    
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor');
    }

    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','id_hc');
    }

}
