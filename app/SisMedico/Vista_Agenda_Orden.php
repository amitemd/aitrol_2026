<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Vista_Agenda_Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vista_agenda_orden';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}