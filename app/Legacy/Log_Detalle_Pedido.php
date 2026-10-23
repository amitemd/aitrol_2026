<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_Detalle_Pedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_detalle_pedido';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}

