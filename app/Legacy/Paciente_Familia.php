<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente_Familia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paciente_familia';

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


}