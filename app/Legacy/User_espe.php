<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class user_espe extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_espe';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
