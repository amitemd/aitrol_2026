<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla_Control_Labs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plantilla_control_labs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
   
}