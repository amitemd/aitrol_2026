<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Agenda_consentimiento extends Model
{
    protected $table = 'agenda_consentimiento';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
