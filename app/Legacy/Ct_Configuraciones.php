<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Ct_Configuraciones extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_configuraciones';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cuenta()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'id_plan');
    }

    public static function obtener_cuenta($tipo)
    {
        $id_empresa = Session::get('id_empresa');
        $cuenta = Ct_configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
            ->leftjoin('plan_cuentas', 'plan_cuentas.id', 'plan_cuentas_empresa.id_plan')
            ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
            ->where('ct_configuraciones.id_empresa', $id_empresa)
            ->where('tipo', "like",  "%{$tipo}%")
            ->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar', 'plan_cuentas.id_padre as id_padre')
            ->first();
        return $cuenta;
    }
}
