<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Encuesta_Complemento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encuesta_complemento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
     public function pregunta()
        {
            return $this->belongsTo('Sis_medico\Pregunta','id_pregunta', 'id');
        }

}