<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_DetalleAts extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_ats';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
