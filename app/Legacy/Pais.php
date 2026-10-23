<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class pais extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pais';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
