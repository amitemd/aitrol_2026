<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Titulo_Profesional extends Model
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