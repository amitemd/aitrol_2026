<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class record_tecnicas_anestesicas extends Model
{
    //record_tecnicas_anestesicas

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'record_tecnicas_anestesicas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
