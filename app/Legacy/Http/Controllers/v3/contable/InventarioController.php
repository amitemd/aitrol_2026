<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Catalogos;
use Sis_medico\v3\contable\Equipo;
use Sis_medico\v3\contable\Insumo;
use Sis_medico\v3\contable\Marca;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Procedimiento;
use Sis_medico\v3\contable\Productos;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Seguros;

class InventarioController extends Controller
{
    private $controlador = 'inventario';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        if ($r->submodulo == 'listadeproductos') {
            $data['table'] = true;
            $data['marcas'] = Marca::getMarcas();
            $data['seguros'] = Seguros::getSeguros();
            $data['proveedores'] = Proveedor::getProveedores();
            $data['grupo'] = Catalogos::getSubCatalogos(34);
            $data['cuentas'] = PlanCuentas::getAllCuentas();
            $data['insumos'] = Insumo::getInsumos();
            $data['equipo'] = Equipo::getEquipos();
            $data['porcentajes_iva'] = Catalogos::getSubCatalogos(42);
            $data['impuestos'] = PlanCuentas::getAllCuentas();
            $data['procedimientos'] = Procedimiento::getProcedimientos();
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_productos';
            $data['obseraudi'] = 'Ingreso a productos';
            $data['titulo'] = 'listadeproductos';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_productos');
        } elseif ($r->submodulo == 'listadeproductosjs') {
            $data['table'] = true;
            $data = Productos::getProductos();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'getAllCuentas') {
            return PlanCuentas::getAllCuentas($r->term);
        } elseif ($r->submodulo == 'getAllInsumos') {
            return Insumo::getInsumos();
        } elseif ($r->submodulo == 'getAllEquipos') {
            return Equipo::getEquipos();
        } elseif ($r->submodulo == 'getAllProcedimiento') {
            return Procedimiento::getProcedimientos();
        } elseif ($r->submodulo == 'getAllSeguros') {
            return Seguros::getSeguros();
        } elseif ($r->submodulo == 'buscarProveedor') {
            return Proveedor::buscarProveedor($r->term);
        } elseif ($r->submodulo == 'getSegurosCombo') {
            return Seguros::getSegurosCombo($r->term);
        } elseif ($r->submodulo == 'getProcedimientoCombo') {
            return Procedimiento::getProcedimientoCombo($r->term);
        } elseif ($r->submodulo == 'getEquipoCombo') {
            return Equipo::getEquipoCombo($r->term);
        } elseif ($r->submodulo == 'getInsumoCombo') {
            return Insumo::getInsumoCombo($r->term);
        } elseif ($r->submodulo == 'getPaquetesCombo') {
            return Productos::getPaqueteCombo($r->term);
        } elseif ($r->submodulo == 'getMovimientos') {
            $html = "";
            $data['table'] = true;
            //dd($r->all());
            $movimientos = Productos::getMovimientos($r->id);
            for ($i = 0; $i < count($movimientos); $i++) {
                $html .= '<tr id="tr_' . $i . '">';
                $html .='<td><a id="codigo_' . $i . '" name="codigo[]" >' . $movimientos[$i]->codigo . '</a></td>';
                $html .='<td><a id="nombre_' . $i . '" name="nombre[]" >' . $movimientos[$i]->nombre_contable . '</a></td>';
                $html .='<td><a id="saldo_' . $i . '" name="saldo[]" >' . $movimientos[$i]->cantidad . '</a></td>';
                $html .='<td></td>';
                $html .= '</tr>';
            }
            $data['movimientos'] = $movimientos;
            $data['html'] = $html;
            return $data;
        } elseif ($r->submodulo == 'getProductos') {
            return Productos::getProductos($r->id);
        } elseif ($r->submodulo == 'saveProducto') {
            return Productos::saveProducto($r);
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
