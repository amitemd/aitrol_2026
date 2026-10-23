<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LogPagoEnLinea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_pagoenlinea';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
