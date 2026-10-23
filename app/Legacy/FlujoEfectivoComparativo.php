<?php

namespace Sis_medico;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\EstructuraFlujoEfectivo; 
use Sis_medico\Plan_cuentas;
use Session;

class FlujoEfectivoComparativo 
{

    public static function flujoEfectivoComparativo($desde, $hasta, $grupos="[]")
    {
        $data = array();
        
        $f = new flujoEfectivoComparativo();
        $est = $f->getEstructura($grupos);
        foreach($est as $e){
            $saldo = 0;
            $resp = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
            $det['padre'] = $f->getCuentaPadre($e->id_plan);     
            $det['grupo'] = $e->grupo;
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $resp['saldo'];     $det['mes'] = $resp['mes']; 
            $data[] = $det;
        }
        return $data;
    }

    public static function getEstructura($grupos="")
    { 
        if($grupos != '[]'){
            $estructura = EstructuraFlujoEfectivo::where('estructura_flujo_efectivo.estado', '!=', 0)  
                ->join('plan_cuentas as p', 'id_plan', 'p.id')  
                ->join('grupo_flujo_efectivo as g', 'id_grupo', 'g.id')  
                ->whereIn('id_grupo', $grupos)
                ->select('id_plan', 'p.nombre',  'p.estado', 'signo', 'g.nombre as grupo')
                ->orderBy('signo') 
                ->orderBy('id_plan') 
                // ->groupBy('signo', 'id_plan') 
                ->get();  
        }else{
            $estructura = EstructuraFlujoEfectivo::where('estructura_flujo_efectivo.estado', '!=', 0)  
                ->join('plan_cuentas as p', 'id_plan', 'p.id')  
                ->select('id_plan', 'p.nombre',  'p.estado', 'signo')
                ->orderBy('signo') 
                ->orderBy('id_plan') 
                // ->groupBy('signo', 'id_plan') 
                ->get();
        }
        
        
        return $estructura;
    }

    
    public function getCuentaPadre($cuenta)
    {
        $id = substr($cuenta, 0, 1);
        $plan = Plan_cuentas::where('id', $id)->first();
        return "$plan->nombre";
    }

    public static function getAsientos($cuenta, $detalle, $desde, $hasta)
    {
        $id_empresa = Session::get('id_empresa');
        $data = array();    $saldo = 0;
        if($detalle==1){ 
            $operador = '='; $valor = $cuenta;
        }else{
            $operador = 'like'; $valor = $cuenta.'.%';
        }
        $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', $operador, $cuenta)
                ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->where('ct_asientos_detalle.estado', '<>', 0) 
                ->where('c.id_empresa', $id_empresa)
                ->whereBetween('fecha', ["$desde 00:00:00", "$hasta 23:59:59"])
                ->select(DB::raw('MONTH(ct_asientos_detalle.fecha) as mes'), DB::raw('ifnull(SUM(debe - haber),0) as saldo')) 
                ->groupBy('mes')
                ->get();
        //dd($asiento);
        foreach ($asiento as $row) { 
            $data['saldo'] = $row->saldo;
            $data['mes'] = $row->mes;
        }
        
        return $saldo;
    }

}