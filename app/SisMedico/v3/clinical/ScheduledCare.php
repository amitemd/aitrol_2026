<?php

namespace Sis_medico\v3\clinical;

use Illuminate\Database\Eloquent\Model;

class ScheduledCare extends Model
{
    protected $table = 'agenda';
    protected $guarded = array();
}
