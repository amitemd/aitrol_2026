<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class As400_Observaciones extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'as400_observaciones';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
