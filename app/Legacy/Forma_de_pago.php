<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Forma_de_pago extends model
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
