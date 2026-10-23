<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Transportistas;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\GuiaRemision;
use Sis_medico\v3\contable\Sucursales;

class GuiadeRemisionController extends Controller
{
    private $controlador = 'guiaderemision';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        if ($r->submodulo == 'transportista') {
            $data['table'] = true;
            $data['tipos'] = Utilidades::traerCatalogo('Tipo de identificacion');
            $data['titulo'] = trans('v3/new_titulos.mantenimientodetransportistas');
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_transportistas';
            $data['obseraudi'] = 'Ingreso al Mantenimiento de transportistas';
            $data['paises'] = Paises::getPaises();
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_transportistas');
        } elseif ($r->submodulo == 'transportistajs') {
            $data = Transportistas::getTransportistas();

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'saveTransportista') {
            return Transportistas::saveTransportista($r);
        } elseif ($r->submodulo == 'getTransportista') {
            return Transportistas::getTransportistas($r->id);
        } elseif ($r->submodulo == 'estadoTransportista') {
            return Transportistas::cambiarEstado($r);
        } elseif ($r->submodulo == 'listadeguias') {
            $data['table'] = true;
            $data['titulo'] = trans('v3/new_titulos.listadodeguiaderemision');
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_listadeguias';
            $data['obseraudi'] = 'Ingreso al Listado de Guia de Remisión';
            $data['paises'] = Paises::getPaises();
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_listadeguias');
        } elseif ($r->submodulo == 'listadeguiasjs') {
            $data = GuiaRemision::getGuiaRemision();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'estadoGuiaRemision') {
            return GuiaRemision::cambiarEstado($r);
        } elseif ($r->submodulo == 'crearGuia') {
            $data['titulo'] = trans('v3/new_titulos.guiaderemision');
            $data["sucursales"] = Sucursales::getSucursales();
            $data["productos"] = GuiaRemision::getProductos($r);
            $data["guiaExiste"] = GuiaRemision::getGuiaRemision($r["id"]);
            $data["tipoGuia"] = Utilidades::traerCatalogo("Tipo Guia");
            $data['documentoGuia'] = Utilidades::traerCatalogo("Documentos guia");
            $data["tipoDoc"] = Utilidades::traerCatalogo("Tipo Documento Guia");
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_creacionGuia';
            $data['obseraudi'] = 'Creacion de guia de remisión';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_creacionGuia');
        } elseif ($r->submodulo == 'getPuntoEmision') {
            return GuiaRemision::obtenerPuntoEmision($r);
        } elseif ($r->submodulo == 'getTransportistaDatosGuia') {
            return GuiaRemision::obtenerDatosGuia($r);
        } elseif ($r->submodulo == 'llenarCamposTransportista') {
            return GuiaRemision::obtenerDatosTransportista($r);
        } elseif ($r->submodulo == 'modalTransportista') {
        } elseif ($r->submodulo == 'validarCampos') {
            return GuiaRemision::validarCampos($r);
        } elseif ($r->submodulo == 'validarPlaca') {
            return GuiaRemision::validarPlaca($r);
        } elseif ($r->submodulo == 'consultarProdu') {
            return GuiaRemision::consultarProductos($r);
        } elseif ($r->submodulo == 'revisarCodigo') {
            return GuiaRemision::revisarCodigo($r);
        } elseif ($r->submodulo == 'guardarGuiaRemision') {
            return GuiaRemision::guardarGuiaRemision($r);
        } elseif ($r->submodulo == 'traerFacturas') {
            return GuiaRemision::traerFacturas($r);
        } elseif ($r->submodulo == 'crearTablaFactura') {
            return GuiaRemision::crearTabla($r);
        } elseif ($r->submodulo == 'getGuiaPrint') {
            return GuiaRemision::getVentaPrint($r);
        } elseif ($r->submodulo == 'traerDatosFactura') {
            return GuiaRemision::traerDatosFacturas($r);
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
