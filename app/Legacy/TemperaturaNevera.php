<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TemperaturaNevera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temperatura_nevera';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}