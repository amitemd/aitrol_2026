<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class De_Establecimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_establecimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
