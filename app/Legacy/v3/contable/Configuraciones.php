<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Configuraciones extends Model
{
    private static $modelo = 'Configuraciones';
    protected $table = 'ct_configuraciones';
    protected $primaryKey  = 'id';

    public function cuentaM()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Plan_Cuentas', 'id_plan');
    }

    public static function getImpuestoIva()
    {
        return Configuraciones::where('id', 3)->first(['iva', 'ice']);
    }
    public static function obtener_cuenta($tipo)
    {
        $cuenta = '';
        $id_empresa = session('id_empresa');
        $endoscopy = 1;
        $nuevo = 0;
        if ($nuevo == 0 and $endoscopy == 0) {
            $cuenta = Configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
                ->leftjoin('plan_cuentas', 'plan_cuentas.id', 'plan_cuentas_empresa.id_plan')
                ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
                ->where('ct_configuraciones.id_empresa', $id_empresa)
                ->where('modulo', "{$tipo}")
                ->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar', 'plan_cuentas.id_padre as id_padre')
                ->first();
        } else {
            $cuenta = Configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
                ->leftjoin('plan_cuentas', 'plan_cuentas.id', 'plan_cuentas_empresa.id_plan')
                ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
                ->where('ct_configuraciones.id_empresa', $id_empresa)
                ->where('tipo', "like", "%{$tipo}%")
                ->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar', 'plan_cuentas.id_padre as id_padre')
                ->first();
        }
        return $cuenta;
    }
}
