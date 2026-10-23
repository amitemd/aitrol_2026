<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class FormaDePago extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    
    protected $table = 'forma_de_pago';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
