<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Api_App extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_app';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}