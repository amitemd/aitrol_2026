<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\EstadoCivil;
use Sis_medico\v3\contable\Rh_Configuraciones;
use Sis_medico\v3\contable\RolesNomina;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Nomina;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\NivelAcademico;
use Sis_medico\v3\contable\TipoUsuario;
use Sis_medico\v3\contable\Area;
use Sis_medico\v3\contable\PagoBeneficio;
use Sis_medico\v3\contable\Horario;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\Bancos;
class RecursosHumanosController extends Controller
{
    private $controlador = 'recursoshumanos';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        if ($r->submodulo == 'configuraciones') {
            $data['tipos'] = Utilidades::traerCatalogo('Configuracion nomina');
            $data['table'] = true;
            $data['titulo'] = trans('v3/new_titulos.configuracionesderecursoshumanos');
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_configuraciones';
            $data['obseraudi'] = 'Ingreso al Configuraciones de Recursos Humanos';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_configuraciones');
        } elseif ($r->submodulo == 'configuracionesjs') {
            $data = Rh_Configuraciones::getConfiguraciones();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'saveConfiguracion') {
            return Rh_Configuraciones::saveConfiguracion($r);
        } elseif ($r->submodulo == 'getConfiguracion') {
            return Rh_Configuraciones::getConfiguraciones($r->id);
        } elseif ($r->submodulo == 'estadoConfigNomina') {
            return Rh_Configuraciones::cambiarEstado($r);
        } elseif ($r->submodulo == 'listadeempleados') {
            $data['table'] = true;
            $data['titulo'] = 'Lista de empleados';
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_empleados';
            $data['obseraudi'] = 'Ingreso a la lista de empleados';
            $data['paises'] = Paises::getPaises();
            $data['genero'] = Utilidades::traerCatalogo("Genero");//dd($data);
            $data['nivel_academico'] = NivelAcademico::getNivelAcademico();//dd($data);
            $data['estado_civil'] = EstadoCivil::getEstadoCivil();//dd($data);
            $data['tipo_usuario'] = TipoUsuario::getTipos('19');//dd($data);
            $data['area'] = Area::getArea();//dd($data);
            $data['pago_beneficio'] = PagoBeneficio::getPagoBeneficio();//dd($data);
            $data['horario'] = Horario::getHorario();//dd($data);
            $data['sucursal'] = Sucursales::getSucursalNomina();//dd($data);
            $data['banco'] = Bancos::getBancos();//dd($data);
            $data['aporte_personal'] = Rh_Configuraciones::getAportesPersonal();//dd($data);
            $data['jornada_laboral'] = Utilidades::traerCatalogo('Tipo Jornada Nomina');
            //dd($data['tipo_usuario']);
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_empleados');
        } elseif ($r->submodulo == 'listadeempleadosjs') {
            $data = Nomina::getNomina($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        }elseif ($r->submodulo == 'getEmpleados') {
            return Nomina::getEmpleados($r->id);
        } elseif ($r->submodulo == 'listaderoles') {
            $data['table'] = true;
            $data['titulo'] = 'Lista de roles';
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_roles';
            $data['obseraudi'] = 'Ingreso a la lista de roles';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_roles');
        } elseif ($r->submodulo == 'listaderolesjs') {
            $data = RolesNomina::getRoles($r);
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
