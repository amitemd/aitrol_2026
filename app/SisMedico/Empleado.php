<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empleado';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
