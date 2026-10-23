<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class TituloProfesional extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'titulo_profesional';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}