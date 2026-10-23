<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Cortesia_paciente extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cortesia_paciente';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
