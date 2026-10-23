<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Comprobante_Secuencia;
use Sis_medico\Empresa;
use Sis_medico\LogAnularDeposito;
use Illuminate\Support\Facades\DB;
use Sis_medico\LogCierreAnio;
use Sis_medico\Ct_Configuraciones;

class LogConfig
{
    public static function busqueda($cuenta)
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        //$id_plan = $cuenta;
        $endoscopy = 1;
        if ($endoscopy == 1) {
            $id_plan = LogConfig::obtener_cuenta($cuenta);
        } else {
            $plan_empresa = Plan_Cuentas_Empresa::where('plan', $cuenta)->where('id_empresa', $id_empresa)->first();
            if (!is_null($plan_empresa)) {
                $id_plan = $plan_empresa->id_plan;
            } else {
                $id_plan = $cuenta;
            }
        }
        return $id_plan;
    }
    public static function obtener_cuenta($tipo)
    {
        $id_empresa = Session::get('id_empresa');
        $cuenta = Ct_Configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
            ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
            ->where('ct_configuraciones.id_empresa', $id_empresa)
            ->where('tipo', 'like', "%-{$tipo}-%")
            ->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar')
            ->first();
        //dd($cuenta, $tipo);
        if (!is_null($cuenta)) {
            return $cuenta->cuenta_guardar;
        } else {
            return $tipo;
        }
    }

    public static function masivoConfiguraciones()
    {
        $id_empresa = "1793135579001";
        $empresas = Empresa::all();
        $configuraciones = Ct_Configuraciones::where('id_empresa', '<>', "NULL")->get();
        dd($configuraciones);
        foreach ($empresas as $emp) {
            foreach ($configuraciones as $config) {
                if ($config->id_empresa != $emp->id) {
                    $data = $config["original"];
                    unset($data["id"]);
                    $data["id_empresa"] = $emp->id;
                    //dd($data);
                    Ct_Configuraciones::create($data);
                }
            }
        }

        //dd("ok gracias amigo");

        return $configuraciones;
    }
}
