<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class  LogMensajes extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_mensajes';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
