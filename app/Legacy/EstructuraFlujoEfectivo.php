<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class EstructuraFlujoEfectivo extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estructura_flujo_efectivo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function plan()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas','id_plan', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo('Sis_medico\GrupoEstructuraFlujoEfectivo','id_grupo', 'id');
    }

}
