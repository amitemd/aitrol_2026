<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\AfActivo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\ActivosDepreciacion_Cabecera;
use Sis_medico\v3\contable\ActivosDepreciacion_Detalle;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\AF_Tipo;
use Sis_medico\v3\contable\AF_Grupo;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\AF_Subtipo;
use Sis_medico\v3\contable\GuiaRemision;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\AF_BodegaSerieColor;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Marca;
use Sis_medico\v3\contable\AF_CodigoSecuencia;
use Sis_medico\v3\contable\AfActivo_Accesorios;

class ActivosFijosController extends Controller
{
    private $controlador = 'activosfijos';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'eliminarAccesorio':
                return AfActivo_Accesorios::eliminarAccesorio($r->id);
                break;
            case 'saveAccesorios':
                return ActivosFijos::saveAccesorios($r);
                break;
            case 'traerAccesorio':
                return  AfActivo_Accesorios::getAccesorios($r->id);
                break;
            case 'traerAccesorios':
                $data = AfActivo_Accesorios::getAccesorios('', $r->id);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
                break;
            case 'grupos':
                $data['table'] = true;
                $data['grupos'] = AF_Grupo::getAF_Grupo();
                $data['titulo'] = trans('v3/new_titulos.mantenimientodegrupodeactivosfijos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_grupos_activos_fijos';
                $data['obseraudi'] = 'Ingreso al Grupo de Activos Fijos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_grupos_activos_fijos');
                break;
            case 'gruposjs':
                $data = AF_Tipo::getAF_Tipo();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveGrupoActivosfijos':
                return AF_Tipo::saveTipoAf($r);
                break;
            case 'getAF_Tipo':
                return AF_Tipo::getAF_Tipo($r->id);
                break;
            case 'estadoGrupoActivosFijos':
                return AF_Tipo::cambiarEstado($r);
                break;
            case 'CodigoAf':
                return AF_CodigoSecuencia::CodigoAf($r);
                break;
            case 'listadeactivos':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.listadodeactivosfijos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_de_activos_fijos';
                $data['obseraudi'] = 'Ingreso al Listado de Activos Fijos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_de_activos_fijos');
                break;
            case 'listadeactivosjs':
                $data = ActivosFijos::getActivosFijos();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'estadoActivosFijos':
                return ActivosFijos::cambiarEstado($r);
                break;
            case 'getActivoFijo':
                return ActivosFijos::getActivosFijos($r->id);
                break;
            case 'buscarCuenta':
                return ActivosFijos::buscarCuenta($r->term);
                break;
            case 'traerCuentaGeneral':
                return PlanCuentas::traerCuentaGeneral($r);
                break;
            case 'subtipos':
                $data['table'] = true;
                $data['tipos'] = AF_Tipo::getTipo();
                $data['titulo'] = trans('v3/new_titulos.listadodesubtiposdeactivosfijos');
                $data['id_tipo'] = $r->id;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_subtipos_activos_fijos';
                $data['obseraudi'] = 'Ingreso al Subtipos de Activos Fijos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_subtipos_activos_fijos');
                break;
            case 'subtiposjs':
                $data = AF_Subtipo::getAF_Subtipos($r->id);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getSubtipos':
                return AF_Subtipo::getSubtipos($r->id);
                break;
            case 'getAF_Subtipos':
                return AF_Subtipo::getAF_Subtipos($r->id);
                break;
            case 'estadoSubtipoAf':
                return AF_Subtipo::estadoSubtipoAf($r);
                break;
            case 'saveSubtipoAf':
                return AF_Subtipo::saveSubtipoAf($r);
                break;
            case 'verificarCodigoSubtipo':
                return AF_Subtipo::verificarCodigoSubtipo($r);
                break;
            case 'depreciaciones':
                $data['table'] = true;
                $data['titulo'] = 'Lista de Depreciaciones';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_depreciaciones';
                $data['obseraudi'] = 'Ingreso al listado de Depreciaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_depreciaciones');
                break;
            case 'depreciacionesjs':
                $data = ActivosDepreciacion_Cabecera::getDepreciaciones();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getDepreciacionDetalle':
                return ActivosDepreciacion_Detalle::getDepreciacionDetalle($r->id);
                break;
            case 'historialDepreciacion':
                $data['table'] = true;
                $data['titulo'] = 'Historial Depreciaciones';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_historial_depreciaciones';
                $data['obseraudi'] = 'Ingreso al Listado de Depreciaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_historial_depreciaciones');
                break;
            case 'historialDepreciacionjs':
                $data = ActivosDepreciacion_Cabecera::getHistorialDepreciaciones();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'geDetHistorialDep':
                return ActivosDepreciacion_Detalle::geDetHistorialDep($r->id);
                break;
            case 'crearDepreciacion':
                $data['table'] = true;
                $data['sucursales'] = Sucursales::getSucursales();
                $data['titulo'] = 'Depreciación de Activos Fijos';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_depreciaciones';
                $data['obseraudi'] = 'Ingreso al Listado de Depreciaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_depreciaciones');
                break;
            case 'crearDepreciacionjs':
                $data = ActivosDepreciacion_Cabecera::getActivosDepreciacion($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'buscarTipos':
                return AF_Tipo::buscarTipos($r->term);
                break;
            case 'buscarSubTipos':
                return AF_Subtipo::buscarSubTipos($r->term);
                break;
            case 'saveDepreciacion':
                return ActivosDepreciacion_Cabecera::saveDepreciacion($r);
                break;
            case 'estadoDepeciacion':
                return ActivosDepreciacion_Cabecera::cambiarEstado($r);
                break;
            case 'getPuntoEmision':
                return GuiaRemision::obtenerPuntoEmision($r);
                break;
            case 'saveActivoFijo':
                return ActivosFijos::saveActivoFijo($r);
                break;
            case 'traerSubtipo':
                return ActivosFijos::traerSubtipo($r);
                break;
            case 'buscarResponsables':
                return AF_BodegaSerieColor::buscarResponsables($r->term);
                break;
            case 'getProveedorCombo':
                return Proveedor::getProveedorCombo($r->term);
                break;
            case 'getMarcaComboNuevo':
                return Marca::getMarcaComboNuevo($r->term);
                break;
            case 'getColor':
                return AF_BodegaSerieColor::getColor($r->term);
                break;
            case 'getSerie':
                return AF_BodegaSerieColor::getSerie($r->term);
                break;
            default:
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
                break;
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
