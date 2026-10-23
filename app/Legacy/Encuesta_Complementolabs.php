<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Encuesta_Complementolabs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encuesta_complementolabs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
     public function pregunta()
        {
            return $this->belongsTo('Sis_medico\Preguntas_Labs','id_pregunta_labs', 'id');
        }
        public function grupo()
        {
            return $this->belongsTo('Sis_medico\Grupo_Pregunta','id_grupo', 'id');
        }

}