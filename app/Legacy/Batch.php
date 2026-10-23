<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batch';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
