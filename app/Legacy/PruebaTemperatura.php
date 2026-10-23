<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class PruebaTemperatura extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prueba_temperatura';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    
}