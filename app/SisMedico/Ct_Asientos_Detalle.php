<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Asientos_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_asientos_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cuenta()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'id_plan_cuenta');
    }
    public function cuenta_empresa()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas_Empresa','id_plan_cuenta', 'id_plan');
    }
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabecera', 'id_asiento_cabecera');
    }

}
