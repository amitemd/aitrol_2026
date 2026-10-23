<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class PreguntasValores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preguntas_valores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function nombre_opcion()
    {
        return $this->belongsTo('Sis_medico\PreguntasOpciones', 'id_opcion');
    }

    
}
