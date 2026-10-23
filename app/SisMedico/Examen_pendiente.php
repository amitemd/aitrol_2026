<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_pendiente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_pendiente';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
