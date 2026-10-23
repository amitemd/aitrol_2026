<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AgendaScan extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agenda_scan';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
