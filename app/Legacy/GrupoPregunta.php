<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class GrupoPregunta extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grupo_pregunta';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
