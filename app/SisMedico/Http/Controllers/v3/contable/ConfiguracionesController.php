<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Auditorias;
use Sis_medico\v3\contable\BodegasContable;
use Sis_medico\v3\contable\Catalogos;
use Sis_medico\v3\contable\Empresas;
use Sis_medico\v3\contable\Menu;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\PermisosUsuario;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\RolesUsuarios;
use Sis_medico\v3\contable\SubMenu;
use Sis_medico\v3\contable\TipoIdentificacion;
use Sis_medico\v3\contable\TipoUsuario;
use Sis_medico\v3\contable\Usuarios;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Especialidad;
use Sis_medico\v3\contable\Planes;
use Sis_medico\v3\contable\PorcentajeRenta;
use Sis_medico\v3\contable\SeleccionPlanes;

class ConfiguracionesController extends Controller
{
    private $controlador = 'configuraciones';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'menu':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'listadeopcionesdelmenu';
                    $data['vista'] = 'v3.'  . $this->carpeta . '.' . $this->controlador . '.view_menu';
                    $data['obseraudi'] = 'Ingreso al menú';
                    $data['contenido'] = view('v3.'  . $this->carpeta . '.' . $this->controlador . '.view_menu');
                }
                break;
            case 'menujs':
                $data = Menu::getMenuTotal();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'subMenujs':
                $data = SubMenu::getSubMenus($r->id);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getMenu':
                return Menu::getMenu($r->id);
                break;
            case 'saveMenu':
                return Menu::saveMenu($r);
                break;
            case 'subMenu':
                $data['table'] = true;
                $data['titulo'] = 'Listado de opciones del submenú';
                $data['id_menu'] = $r->id;
                $data['menu_'] = Menu::getMenus();
                $data['vista'] = 'v3.'  . $this->carpeta . '.' . $this->controlador . '.view_submenu';
                $data['obseraudi'] = 'Ingreso al submenú';
                $data['contenido'] = view('v3.'  . $this->carpeta . '.' . $this->controlador . '.view_submenu');
                break;
            case 'getSubMenu':
                return SubMenu::getSubMenu($r->id);
                break;
            case 'estadoMenu':
                return Menu::estadoMenu($r);
                break;
            case 'estadoEmpresa':
                return Empresas::estadoEmpresa($r);
                break;
            case 'estadoUsuario':
                return Usuarios::estadoUsuario($r);
                break;
            case 'permisos':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['titulo'] = trans('v3/new_titulos.selecciondepermisos');
                    $data['vista'] = 'v3.'  . $this->carpeta . '.' . $this->controlador . '.view_permisos';
                    $data['obseraudi'] = 'Ingreso a permisos';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_permisos');
                }
                break;
            case 'buscarUsuario':
                return Usuarios::buscarUsuario($r->term, 'todo');
                break;
            case 'getPermisos':
                return PermisosUsuario::getPermisos($r->id);
                break;
            case 'savePermisos':
                return PermisosUsuario::savePermisos($r);
                break;
            case 'saveSubMenu':
                return SubMenu::saveSubMenu($r);
                break;
            case 'traerModaIcon':
                return view('v3.'  . $this->carpeta . '.' . $this->controlador . '.view_iconos');
                break;
            case 'getAllMenus':
                return Menu::getMenus($r->term);
                break;
            case 'menuCombo':
                return Menu::menuCombo($r->id);
                break;
            case 'verificarCorreo':
                return Usuarios::verificarCorreo($r);
                break;
            case 'usuarios':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['paises'] = Paises::getPaises();
                    $data['tipousuario'] = TipoUsuario::getTipos();
                    $data['especialidad'] = Especialidad::getEspecialidad();
                    $data['titulo'] = 'Listado de usuarios';
                    $data['rolesUsuario'] = RolesUsuarios::getRolesUsuarios();
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_usuarios';
                    $data['obseraudi'] = 'Ingreso a usuarios';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_usuarios');
                }
                break;
            case 'usuariosjs':
                return Usuarios::getUsuarios('', $r);
                /*$data = Usuarios::getUsuarios();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);*/
                break;
            case 'perfil':
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_perfil';
                $data['obseraudi'] = 'Ingreso a usuarios';
                $data['paises'] = Paises::getPaises();
                $data['tipousuario'] = TipoUsuario::getTipos();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_perfil');
                break;
            case 'empresas':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'Listado de empresas';
                    $data['vista'] = 'v3.view_empresas';
                    $data['obseraudi'] = 'Ingreso a empresas';
                    $data['tipoIdentificacion'] = TipoIdentificacion::getTipoIdentificacion();
                    $data['paises'] = Paises::getPaises();
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_empresas');
                }
                break;
            case 'empresasjs':
                $data = Empresas::getEmpresa();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getEmpresa':
                return Empresas::getEmpresa($r->id);
                break;
            case 'getRepresentante':
                return Usuarios::buscarUsuario($r->term, 'todo');
                break;
            case 'saveEmpresa':
                return Empresas::saveEmpresa($r);
                break;
            case 'getIdRepresentante':
                return Empresas::getRepresentante($r->id);
                break;
            case 'perfilUsuario':
                $data['vista'] = 'v3.view_perfilUsuario';
                $data['obseraudi'] = 'Ingreso a perfil de usuarios';
                $data['paises'] = Paises::getPaises();
                $data['tipousuario'] = TipoUsuario::getTipos();
                $data['perfilUsuario'] = Usuarios::getUsuarios($r->id, $r);
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_perfilUsuario');
                break;
            case 'saveUsuario':
                return Usuarios::saveUsuario($r);
                break;
            case 'buscarCiudad':
                return Usuarios::buscarCiudad($r->id);
                break;
            case 'buscarProvincia':
                return Usuarios::buscarProvincia($r->id);
                break;
            case 'roles':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'rolesdeusuarios';
                    $data['rolesUsuarios'] = RolesUsuarios::getRolesUsuarios();
                    $data['vista'] = 'v3.view_roles_usuarios';
                    $data['obseraudi'] = 'Ingreso a roles de usuarios';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_roles_usuarios');
                }
                break;
            case 'establecimientos':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'establecimientos';
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_establecimientos';
                    $data['obseraudi'] = 'Ingreso a establecimientos';
                    $data['paises'] = Paises::getPaises();
                    $data['establecimientos'] = PuntoEmision::getPuntosEmision();
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_establecimientos');
                }
                break;
            case 'rolesUsuariosjs':
                $data = Menu::getMenuRoles($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveRolUsuarios':
                return RolesUsuarios::saveRolUsuarios($r);
                break;
            case 'saveEstablecimiento':
                return PuntoEmision::saveEstablecimiento($r);
                break;
            case 'catalogo':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'catalogo';
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_catalogo';
                    $data['obseraudi'] = 'Ingreso a catalogo';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_catalogo');
                }
                break;
            case 'catalogojs':
                $data = Catalogos::getCatalogos();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveCatalogo':
                return Catalogos::saveCatalogo($r);
                break;
            case 'estadoCatalogo':
                return Catalogos::estadoCatalogo($r);
                break;
            case 'getCatalogo':
                return Catalogos::getCatalogos($r->id);
                break;
            case 'subCatalogo':
                $data['table'] = true;
                $data['id'] = $r->id;
                $data['titulo'] = 'subcatalogo';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_catalogo';
                $data['obseraudi'] = 'Ingreso a catalogo';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_catalogo');
                break;
            case 'subCatalogojs':
                $data = Catalogos::getSubCatalogos($r->id);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'auditoria':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.auditoria');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_auditoria';
                $data['obseraudi'] = 'Ingreso a auditoria';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_auditoria');
                break;
            case 'auditoriajs':
                $data = Auditorias::getAuditorias();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getAuditoria':
                return Auditorias::getAuditorias($r->id);
                break;
            case 'validarNombreRol':
                return RolesUsuarios::validarNombreRol($r);
                break;
            case 'getUsuarios':
                return Usuarios::getUsuarios($r->id, $r);
                break;
            case 'bodegas':
                $data['table'] = true;
                $data['titulo'] = 'Bodegas';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_bodegas';
                $data['obseraudi'] = 'Ingreso a bodegas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_bodegas');
                break;
            case 'bodegasjs':
                $data = BodegasContable::getBodegas();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveBodega':
                return BodegasContable::saveBodega($r);
                break;
            case 'infoTributarioEmpresa':
                $data = PorcentajeRenta::infoTributarioEmpresa($r->id);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'planes':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = 'planes';
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_planes';
                    $data['obseraudi'] = 'Ingreso a planes';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_planes');
                }
                break;
            case 'planesjs':
                $data = Planes::getPlanes();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'getPlan':
                return Planes::getPlanes($r->id);
                break;
            case 'savePlan':
                return planes::savePlan($r);
                break;
            case 'validarnombreplan':
                $variable = planes::validarnombreplan($r);
                return $variable;
                break;
            case 'estadoPlan':
                return planes::cambiarEstadoPlan($r);
                break;
            case 'selecionarplan':
                $data['table'] = true;
                $data['titulo'] = 'selecionar Plan';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_selecionPlan';
                $data['obseraudi'] = 'Ingreso a selecionar Plan';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_selecionPlan');
                $data['seleccion'] = Planes::getSelectPlan();
                break;
            case 'selecionarplanjs':
                $data = SeleccionPlanes::getSeleccionPlanes();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveselecionarPlan':
                return SeleccionPlanes::saveSeleccionPlan($r);
                break;
            default:
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
                break;
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
