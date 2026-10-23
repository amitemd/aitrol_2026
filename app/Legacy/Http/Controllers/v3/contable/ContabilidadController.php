<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Contabilidad;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\LogContable;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\AsientosDetalles;


class ContabilidadController extends Controller
{
    private $controlador = 'contabilidad';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        if ($r->submodulo == 'plandecuentas') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = 'Plan de cuentas';
                $data['plan'] = PlanCuentas::getPlanCuentaTree();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_plan_cuenta_tree';
                $data['obseraudi'] = 'Ingreso al plan de cuentas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_plan_cuenta_tree');
            }
        } elseif ($r->submodulo == 'plandecuentasjs') {
            $data = PlanCuentas::getPlanCuenta();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'traerCuentasPlan') {
            return PlanCuentas::traerCuentasPlan($r->plan);
        } elseif ($r->submodulo == 'detalleCuenta') {
            return PlanCuentas::detalleCuenta($r->plan);
        } elseif ($r->submodulo == 'secuenciaCuenta') {
            return PlanCuentas::secuenciaCuenta($r->plan);
        } elseif ($r->submodulo == 'savePlanCuenta') {
            return PlanCuentas::savePlanCuenta($r);
        } elseif ($r->submodulo == 'modificarPlanCuenta') {
            return PlanCuentas::modificarPlanCuenta($r);
        } elseif ($r->submodulo == 'exportarPlan') {
            return PlanCuentas::exportarPlan($r);
        } elseif ($r->submodulo == 'buscarCuenta') {
            return ActivosFijos::buscarCuenta($r->term);
        } elseif ($r->submodulo == 'getAsientoCabecera') {
            return AsientosCabecera::getAsientoCabecera($r);
        } elseif ($r->submodulo == 'DetalleLogContable') {
            return LogContable::DetalleLogContable($r);
        } elseif ($r->submodulo == 'getdetalleAsiento') {
            return AsientosDetalles::getdetalleAsiento($r);
        } elseif ($r->submodulo == 'updateAsientoDescuadrado') {
            return AsientosDetalles::updateAsientoDescuadrado($r);
        } elseif ($r->submodulo == 'checkpass') {
            return AsientosDetalles::checkpass($r);
        } elseif ($r->submodulo == 'descuadrados') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = 'Asientos descuadrados';
                //$data["detalleAsiento"] = AsientosDetalles::getdetalleAsiento($r);
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_asientos_descuadrados';
                $data['obseraudi'] = 'Ingreso a asientos descuadrados';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_asientos_descuadrados');
            }
        } elseif ($r->submodulo == 'descuadradosjs') {
            $data = Contabilidad::buscarDescuadrados($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'anulados') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = 'Asientos anulados';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_asientos_anulados';
                $data['obseraudi'] = 'Ingreso a asientos anulados';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_asientos_anulados');
            }
        } elseif ($r->submodulo == 'anuladosjs') {
            $data = LogContable::getAsientosAnulados();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else {
            $data['vista'] = 'v3.errors.view_404';
            $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
            $data['contenido'] = view('v3.errors.view_404');
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
