<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TransaccionPago extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaccion_pago';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
