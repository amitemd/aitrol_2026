<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Apps_Ratings extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps_ratings';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
