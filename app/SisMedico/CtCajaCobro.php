<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CtCajaCobro extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table     = 'ct_caja_cobro';
    protected $keyType   = 'string';
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


}
