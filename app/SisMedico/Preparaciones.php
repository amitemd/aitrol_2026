<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Preparaciones extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preparaciones';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}