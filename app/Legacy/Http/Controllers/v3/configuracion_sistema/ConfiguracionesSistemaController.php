<?php

namespace Sis_medico\Http\Controllers\v3\configuracion_sistema;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\configuracion_sistema\Hospital;
use Sis_medico\v3\contable\Especialidad;

class ConfiguracionesSistemaController extends Controller
{
    private $controlador = 'configuracionessistema';
    private $carpeta = 'configuracion_sistema';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        if ($r->submodulo == 'ubicaciones') {
            $data['table'] = true;
            $data['titulo'] = 'Lista de Ubicaciones';
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_ubicaciones';
            $data['obseraudi'] = 'Ingreso al listado de ubicaciones';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_ubicaciones');
        
        }  elseif ($r->submodulo == 'ubicacionesjs') {
            $data = Hospital::getUbicaciones();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        }elseif ($r->submodulo == 'saveUbicacion') {
            return Hospital::saveUbicacion($r);
        }elseif ($r->submodulo == 'getUbicacion') {
            return Hospital::getUbicaciones($r->id);
        }elseif ($r->submodulo == 'estadoConfigUbicacion') {
            return Hospital::cambiarEstado($r);
        }elseif ($r->submodulo == 'especialidades') {
            $data['table'] = true;
            $data['titulo'] = 'Lista de Especialidades';
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_especialidades';
            $data['obseraudi'] = 'Ingreso al listado de Especialidades';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_especialidades');
        
        }elseif ($r->submodulo == 'especialidadesjs') {
            $data = Especialidad::getEspecialidad();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        }elseif ($r->submodulo == 'getEspecialidad') {
            return Especialidad::getEspecialidad($r->id);
        }elseif ($r->submodulo == 'saveEspecialidad') {
            return Especialidad::saveEspecialidad($r);
        }elseif ($r->submodulo == 'estadoConfigEspecialidad') {
            return Especialidad::cambiarEstado($r);
        }elseif ($r->submodulo == 'procedimientos') {
            $data['table'] = true;
            $data['titulo'] = 'Lista de Procedimientos';
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_procedimientos';
            $data['obseraudi'] = 'Ingreso al listado de Procedimientos';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_procedimientos');
        
        }
        else {
            $data['vista'] = 'v3.errors.view_404';
            $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
            $data['contenido'] = view('v3.errors.view_404');
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
