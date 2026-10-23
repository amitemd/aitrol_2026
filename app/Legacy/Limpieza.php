<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Limpieza extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'limpieza';


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

    public function user()
    {
        return $this->belongsTo('Sis_medico\User', 'responsable_anest');
    }

}