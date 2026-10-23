<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Preguntas_Labs_Valores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preguntas_labs_valores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function nombre_opcion()
    {
        return $this->belongsTo('Sis_medico\Preguntas_Labs_Opciones', 'id_opcion');
    }

    
}
