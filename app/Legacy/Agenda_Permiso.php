<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Agenda_Permiso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agenda_permiso';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}