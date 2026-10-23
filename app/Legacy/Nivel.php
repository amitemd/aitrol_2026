<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nivel extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nivel';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
