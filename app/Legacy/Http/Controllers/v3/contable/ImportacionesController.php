<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\CtBodega;
use Sis_medico\v3\contable\ImportacionesCabecera;
use Sis_medico\v3\contable\ImportacionesGastosCab;
use Sis_medico\v3\contable\MasterTipos;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\Termino;

class ImportacionesController extends Controller
{
    private $controlador = 'importaciones';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'listadeimportaciones':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.listadeimportaciones');
                $data['vista'] = 'v3.' .  $this->carpeta . '.' . $this->controlador . '.view_lista_importaciones';
                $data['obseraudi'] = 'Ingreso al listado de importaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.'  . $this->controlador . '.view_lista_importaciones');
                break;
            case 'listadeimportacionesjs':
                $data = ImportacionesCabecera::getImportaciones();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'ingresa_factura':
                $data['cab'] = ImportacionesCabecera::find($r->id);
                $data['sucursales'] = Sucursales::getSucursales();
                $data['terminos'] = Termino::getTerminos();
                $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
                $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
                $data['vista'] = 'v3.' .  $this->carpeta . '.' . $this->controlador . '.view_ingreso_compra_importacion';
                $data['obseraudi'] = 'Ingreso a crear una factura en importaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.'  . $this->controlador . '.view_ingreso_compra_importacion');
                break;
            case 'getAllBodegas':
                return CtBodega::getBodega();
                break;
            case 'saveFactura':
                return ImportacionesCabecera::saveFactura($r);
                break;
            case 'buscarGastosImportacion':
                return ImportacionesGastosCab::buscarGastosImportacion($r->term);
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
