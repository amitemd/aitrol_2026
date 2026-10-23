<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\AF_BodegaSerieColor;
use Sis_medico\v3\contable\AF_Tipo;
use Sis_medico\v3\contable\AfFacturaActivoDetalle;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\ConfigBancos;
use Sis_medico\v3\contable\Configuraciones;
use Sis_medico\v3\contable\CtBodega;
use Sis_medico\v3\contable\Divisas;
use Sis_medico\v3\contable\FormaPago;
use Sis_medico\v3\contable\Marca;
use Sis_medico\v3\contable\MasterTipos;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\Pedido;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Productos;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\Termino;
use Sis_medico\v3\contable\TipoCuenta;
use Sis_medico\v3\contable\TipoIdentificacion;
use Sis_medico\v3\contable\TipoTarjeta;
use Sis_medico\v3\contable\Utilidades;

class ComprasController extends Controller
{
    private $controlador = 'compras';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'traerDias':
                return Termino::traerDias($r->id);
                break;
            case 'buscarCompras':
                $data = Compras::buscarCompras($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'listadecompras':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_compras';
                $data['obseraudi'] = 'Ingreso a lista de compras';
                $data['titulo'] = 'listadecompras';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_compras');
                break;
            case 'listadecomprasjs':
                $data = Compras::getCompras('', $r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'addActivos':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingresoactivos';
                $data['obseraudi'] = 'Ingreso de activos';
                $data['titulo'] = 'listadeactivos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingresoactivos');
                break;

            case 'preOrden':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_preorden';
                $data['obseraudi'] = 'Pre Orden Activo Fijo';
                $data['titulo'] = 'listadeordendeactivosfijos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_preorden');
                break;
            case 'saveOrdenActivos':
                return Compras::saveOrdenActivos($r);
                break;
            case 'nuevaFacturaOrden':
                $data["iva"] = Configuraciones::getImpuestoIva();
                $data["tipos"] =  AF_Tipo::getAF_Tipo();
                $data["marcas"]  = Marca::getMarcas();
                $data["af_colores"] = AF_BodegaSerieColor::af_colores();
                $data["af_responsables"] = AF_BodegaSerieColor::afResponsables();
                $data["af_series"] = AF_BodegaSerieColor::af_series();
                $data["registroActive"] = Compras::getPreOrden($r);
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearorden';
                $data['obseraudi'] = 'Ingreso de Pre-Factura';
                $data['titulo'] = 'Ingreso de Pre-Factura';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearorden');
                break;
            case 'addActivosjs':
                $data = Compras::getActivos();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'preOrdenjs':
                $data = Compras::getPreOrden($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'buscarProveedorCombo':
                return Proveedor::getProveedorCombo($r->term);
                break;
            case 'buscarProveedor':
                return Proveedor::buscarProveedor($r->term);
                break;
            case 'getAsiento':
                return Compras::getAsiento($r->id);
                break;
            case 'guardar_marca':
                return Compras::guardar_marca($r);
                break;
            case 'traerDatosProveedor':
                return Compras::traerDatosProveedor($r);
                break;
            case 'saveOrdenActivos':
                return Compras::saveOrdenActivos($r);
                break;
            case 'getCompra':
                return Compras::getCompra($r->id);
                break;
            case 'documentoFacturaBuscarOrden':
                return Compras::documentoFacturaBuscarOrden($r);
                break;
            case  'nuevaFacturaActivos':
                $data['table'] = true;
                $data["termino"] = Termino::getTerminos();
                $data["creditoTributario"] = MasterTipos::getTiposComprobantes(2);
                $data["tipos_comp"] = MasterTipos::getTiposComprobantes(1);
                $data['divisas'] = Divisas::traerDivisas();
                $data["iva"] = Configuraciones::getImpuestoIva();
                $data["sucursales"] = Sucursales::getSucursales();
                $data["tipos"] =  AF_Tipo::getAF_Tipo();
                $data["af_responsables"] = AF_BodegaSerieColor::afResponsables();
                $data["af_colores"] = AF_BodegaSerieColor::af_colores();
                $data["activoExiste"] = Compras::getOnlyOneRow($r);
                $data["af_series"] = AF_BodegaSerieColor::af_series();
                $data["marcas"]  = Marca::getMarcas();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearfacturasactivos';
                $data['obseraudi'] = 'Ingreso de facturas de activos';
                $data['titulo'] = 'Ingreso de facturas de activos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearfacturasactivos');
                break;
            case 'buscarPlandeCuenta':
                return PlanCuentas::buscarCuentaPlan($r->term);
                break;
            case 'getOnlyItemActive':
                return Compras::getOnlyItemActive($r);
                break;
            case 'verificarActivo':
                return Compras::verificarActivo($r);
                break;
            case 'anularActivo':
                return Compras::anularActivo($r);
                break;
            case  'getOrdenesActivoFijo':
                return Compras::obtenerDatosFacturasActivoFijo($r);
                break;
            case 'documentofactura_saveSerie':
                return Compras::documentofactura_saveSerie($r);
                break;
            case 'getDataCategoria':
                return Compras::buscarCategoria($r);
                break;
            case 'guardar_responsable':
                return Compras::guardar_responsable($r);
                break;
            case 'buscar_codigo':
                return Compras::buscar_codigo($r);
                break;
            case 'guardar_marca':
                return Compras::buscar_codigo($r);
                break;
            case 'saveFacturaActivos':
                return Compras::saveFacturaActivos($r);
                break;
            case 'buscar_categoria':
                return Compras::buscar_Categoria($r);
                break;
            case 'guardar_color':
                return Compras::guardar_color($r);
                break;
            case 'obtenerPuntoEmision':
                return PuntoEmision::obtenerPuntoEmision($r->id);
                break;
            case 'getAllTipoComprobantes':
                return MasterTipos::getAllTiposComprobantes($r->term, '1');
                break;
            case 'getAllCreditoTributario':
                return MasterTipos::getAllTiposComprobantes($r->term, '2');
                break;
            case 'nuevaFacturaActivos':
                return Compras::nuevaFacturaActivos($r);
                break;
            case 'getAllTerminos':
                return Termino::getAllTerminos($r->term);
                break;
            case 'getAllCuentas':
                return PlanCuentas::getAllCuentas($r->term);
                break;
            case 'getAllBancos':
                return ConfigBancos::getAllBancos($r->term);
                break;
            case 'buscarProducto':
                return Productos::getProductoCombo($r->term);
                break;
            case 'buscarGasto':
                return Productos::getGastoCombo($r->term);
                break;
            case 'getAllBodegas':
                return CtBodega::getBodega();
                break;
            case 'buscarBodega':
                return CtBodega::getBodegaCombo($r->term);
                break;
            case 'modalProveedor':
                return view($data['vista'] = 'v3.' . $this->controlador . '.view_modal_proveedores');
                break;
            case 'verificarCompra':
                return Compras::verificarCompra($r);
                break;
            case 'anularCompra':
                return Compras::anularCompra($r);
                break;
            case 'modalPedido':
                $data = Pedido::getPedidoModal();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'agregarPedido':
                $data['table'] = true;
                $pedido = Pedido::getPedido($r->id);
                $cabecera['fecha'] = $pedido[0]->fecha;
                $cabecera['id_proveedor'] = $pedido[0]->id_proveedor;
                $cabecera['pedido'] = $pedido[0]->pedido;
                $cabecera['tipo_comprobante'] = $pedido[0]->tipo;
                $subtotal_12 = 0.00;
                $subtotal_0 = 0.00;
                $iva_total = 0.00;
                $descuento_total = 0.00;
                $subtotal = 0.00;
                $valor_total = 0.00;
                $html = "";
                $bodegas = CtBodega::getBodega();
                $bode = '';
                foreach ($bodegas as $bodega) {
                    $bode .= '<option value="' . $bodega->id . '" >' . $bodega->nombre . '</option>';
                }
                for ($i = 0; $i < count($pedido); $i++) {
                    $pDescuento = $pedido[$i]->descuento * 100 / $pedido[$i]->precio;
                    $total = $pedido[$i]->precio * $pedido[$i]->cantidad_total + $pedido[$i]->iva - $pedido[$i]->descuento;
                    $descuento_total += $pedido[$i]->descuento;
                    $iva_total += $pedido[$i]->iva;
                    if ($pedido[$i]->iva > 0) {
                        $subtotal_12 += ($pedido[$i]->precio * $pedido[$i]->cantidad_total);
                    } else {
                        $subtotal_0 += $pedido[$i]->precio * $pedido[$i]->cantidad_total;
                    }
                    $html .= '<tr id="tr_' . $i . '">';
                    $html .=
                        '<td style="width: 200px;"><input type="hidden" class="form-control" id="baseImponible_' . $i . '" name="baseImponible[]" readonly />'
                        . '<input type="hidden" class="form-control" id="iva_fila_sin_' . $i . '" name="iva_fila_sin[]" readonly />'
                        . '<input type="hidden" class="form-control" id="iva_fila_' . $i . '" name="iva_fila[]" readonly />'
                        . '<input type="text" class="form-control" id="codigo_' . $i . '" name="codigo[]" readonly value="' . $pedido[$i]->codigo . '"/></td>';
                    $html .=
                        '<td style="width: 200px;"><select class="form-control select2_producto" id="nombre_' . $i . '" onchange="asignarProducto(\'' . $i . '\')" name="nombre[]" value="' . $pedido[$i]->producto_id . '|' . $pedido[$i]->codigo . '|' . $pedido[$i]->nombre . '|' . $pedido[$i]->is_iva . '">'
                        . '<option value="">--Seleccione--</option>
                            <option value="' . $pedido[$i]->codigo . "|" . $pedido[$i]->iva . '" selected>' . $pedido[$i]->codigo . ' | ' . $pedido[$i]->nombre . '</option>
                        </select></td>';
                    $html .=
                        '<td style="width: 200px;"><TextArea type="text" class="form-control text-right" id="detalle_' . $i . '" name="detalle[]" value="" /></textarea></td>';
                    $html .=
                        '<td style="width: 100px;">
                            <input type="text" class="form-control text-right" id="cantidad_' . $i . '" name="cantidad[]" onblur="calcular();" value="' . $pedido[$i]->cantidad_total . '"/>
                            <select id="bodega" name="bodega[]" class="form-control select2_bodega bodega" style="width: 100%;margin-top: 5px;" required>
                                <option value="">--Seleccione--</option>';
                    $html .= $bode;
                    $html .= '</select>
                        </td>';
                    $html .=
                        '<td><input type="text" class="form-control text-right" id="precio_' . $i . '" name="precio[]" onblur="calcular();" value="' . $pedido[$i]->precio . '"/></td>';
                    $html .=
                        '<td><input type="text" class="form-control text-right" id="descuento_' . $i . '" name="descuento[]" onblur="getPorcent(' . $i . ');" value="' . $pedido[$i]->descuento . '"/></td>';
                    $html .=
                        '<td><input type="text" class="form-control text-right" id="pdescuento_' . $i . '" name="pdescuento[]" onblur="getValue(' . $i . ');" value="' . $pDescuento . '"/></td>';
                    $html .=
                        '<td><input type="text" class="form-control text-right" id="total_' . $i . '" name="total[]" onblur="calcular();" value="' . $total . '"/></td>';
                    $html .=
                        '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(' . $i . ')"></i></td>';
                    $html .= '</tr>
                    <script>
                        $(".select2_bodega").select2();
                    $(".select2_producto").select2({
                        width: "element",
                        noResults: function () {
                            return "No hay resultado";
                        },
                        searching: function () {
                            return "Buscando..";
                        },
                        placeholder: "--Seleccione--",
                        minimumInputLength: 3,
                        ajax: {
                            url: "v3/" + $("#controlador").val() + "/buscarProducto",
                            type: "get",
                            dataType: "json",
                            delay: 250,
                            data: function (params) {
                                return { term: params.term };
                            },
                            processResults: function (response) {
                                return { results: response };
                            },
                            error(error) {
                                console.log(error);
                            },
                            cache: true,
                        },
                    });
                    $(".select2_bodega").val(\'' . $pedido[$i]->id_bodega . '\'\).trigger(\'change\');
                    </script>';
                }
                $html .= '';
                $subtotal = $subtotal_12 + $subtotal_0 - $descuento_total;
                $valor_total = $subtotal + $iva_total;
                $cabecera['subtotal12'] = $subtotal_12;
                $cabecera['subtotal0'] = $subtotal_0;
                $cabecera['iva'] = $iva_total;
                $cabecera['descuento'] = $descuento_total;
                $cabecera['subtotal'] = $subtotal;
                $cabecera['total'] = $valor_total;
                $cabecera['fila'] = count($pedido) - 1;
                $data['cabecera'] = $cabecera;
                $data['html'] = $html;
                return $data;
                break;
            case 'saveCompra':
                return Compras::saveCompra($r);
                break;
            case 'addcompra':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_compra';
                $data['obseraudi'] = 'Ingreso a ingreso de compra';
                $data['titulo'] = trans('v3/new_titulos.nuevacompra');
                $data['sucursales'] = Sucursales::getSucursales();
                $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
                $data['formaPago'] = FormaPago::getFormasPago();
                $data['tipoTarjeta'] = TipoTarjeta::getTiposTarjeta();
                $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
                $data['terminos'] = Termino::getTerminos();
                $data['paises'] = Paises::getPaises();
                $data['tipo_identificacion'] = TipoIdentificacion::getTipoIdentificacion();
                $data['cuenta'] = PlanCuentas::getAllCuentas();
                $data['bancos'] = ConfigBancos::getAllBancos();
                $data['tipo_cuenta'] = TipoCuenta::getAllTiposCuentas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_compra');
                break;
            case 'addgasto':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_gasto';
                $data['obseraudi'] = 'Ingreso a ingreso de gasto y liquidaciones';
                $data['titulo'] = trans('v3/new_titulos.nuevogasto');
                $data['sucursales'] = Sucursales::getSucursales();
                $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
                $data['formaPago'] = FormaPago::getFormasPago();
                $data['tipoTarjeta'] = TipoTarjeta::getTiposTarjeta();
                $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
                $data['terminos'] = Termino::getTerminos();
                $data['paises'] = Paises::getPaises();
                $data['tipo_identificacion'] = TipoIdentificacion::getTipoIdentificacion();
                $data['cuenta'] = PlanCuentas::getAllCuentas();
                $data['bancos'] = ConfigBancos::getAllBancos();
                $data['tipo_cuenta'] = TipoCuenta::getAllTiposCuentas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_gasto');
                break;
            case 'printFactura':
                $data['html'] = Compras::getCompraPrint($r->id);
                return view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_print_factura', $data);
                break;
            case 'verificarproveedor':
                return Compras::verificarProveedor($r);
                break;
            case 'descargarComprobanteCompra':
                return Compras::descargarComprobanteCompra($r);
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
