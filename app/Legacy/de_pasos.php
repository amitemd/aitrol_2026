<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class De_Pasos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_pasos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
