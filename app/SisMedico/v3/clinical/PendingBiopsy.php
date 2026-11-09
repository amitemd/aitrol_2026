<?php

namespace Sis_medico\v3\clinical;

use Illuminate\Database\Eloquent\Model;

class PendingBiopsy extends Model
{
    protected $table = 'paciente_biopsia';
    protected $guarded = array();
}
