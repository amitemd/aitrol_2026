<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Pentax_log extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pentax_log';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
