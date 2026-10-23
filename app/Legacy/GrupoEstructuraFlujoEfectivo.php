<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class GrupoEstructuraFlujoEfectivo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grupo_flujo_efectivo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $keyType = 'string';
}
