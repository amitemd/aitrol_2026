<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Bancos;
use Sis_medico\v3\contable\Bodegas;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\Divisas;
use Sis_medico\v3\contable\InfoTributaria;
use Sis_medico\v3\contable\OrdenVenta;
use Sis_medico\v3\contable\OrdenVentaPago;
use Sis_medico\v3\contable\Pacientes;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\Productos;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\RolUsuarioTipo;
use Sis_medico\v3\contable\Seguros;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\TipoIdentificacion;
use Sis_medico\v3\contable\TipoPago;
use Sis_medico\v3\contable\TipoTarjeta;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\Usuarios;

class VentasController extends Controller
{
    private $controlador = 'ventas';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        $data['extra_'] = $r->extra;
        switch ($r->submodulo) {
            case 'buscarRecaudador':
                return RolUsuarioTipo::buscarRecaudador($r->term, $r->tipo);
                break;
            case 'buscarVentas':
                $data = Ventas::buscarVentas($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'estadoFactura':
                return Ventas::cambiarEstado($r);
                break;
            case 'traerPrecio':
                return Productos::getPrecios($r);
                break;
            case 'listadeventas':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventas';
                $data['obseraudi'] = 'Ingreso a ventas';
                $data['titulo'] = 'listadeventas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventas');
                break;
            case 'listadeventasjs':
                $data = Ventas::getVentas();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'ventasVerificar':
                return Ventas::verificar($r);
                break;
            case 'anularVenta':
                return Ventas::anularVentas($r);
                break;
            case 'facturar':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    if ($r->extra == 'facturaconglomerada') {
                        $data['table'] = true;
                        $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventasConglomerada';
                        $data['obseraudi'] = 'Ingreso a ventas conglomerada';
                        $data['titulo'] = 'listadeventas';
                        $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventasConglomerada');
                    } elseif ($r->extra == 'crearFacturaConglomerada') {
                        $data["sucursales"] = Sucursales::getSucursales();
                        $data["proveedores"] = Proveedor::getAllProveedores();
                        $data["recaudador"] = Usuarios::usuarioRecaudador();
                        $data["vendedor"] = Usuarios::usuarioVendedor();
                        $data['divisas'] = Divisas::traerDivisas();
                        $data["metodopago"] = TipoPago::getTipoPago();
                        $data["tiposTarjeta"] = TipoTarjeta::getTiposTarjeta();
                        $data["tiposBancos"] = Bancos::getBancos();
                        $data["seguros"] = Seguros::getSeguros();
                        $data["productos"] = Productos::getProductos();
                        $data["listasBancos"] = Bancos::getBancos();
                        $data["conglomeradaRegistro"] = Ventas::getOnlyRow($r["id"]);
                        $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_conglomerada';
                        $data['obseraudi'] = 'Crear factura conglomerada';
                        $data['titulo'] = "Crear factura conglomerada";
                        $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_conglomerada');
                    } elseif ($r->extra == 'facturaventa') {
                        $data['table'] = true;
                        $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura';
                        $data['obseraudi'] = 'Ingreso a ventas';
                        $data['titulo'] = trans('v3/new_titulos.ventas');
                        $data['tipoDoc'] = 'VEN-FA';
                        $data['formasDePago'] = Ventas::metodosDePagos();
                        $data["bodegas"] = Bodegas::traerBodegas();
                        $data['seguros'] = Seguros::getSeguros();
                        $data['sucursales'] = Sucursales::getSucursales();
                        $data['divisas'] = Divisas::getDivisas();
                        $data['impuestos'] = Utilidades::traerCatalogo('impuesto');
                        $data["clase"] = Utilidades::traerCatalogo('Clase');
                        $data['tipoIdentificacion'] = TipoIdentificacion::getTipoIdentificacion();
                        $data['paises'] = Paises::getPaises();
                        $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura');
                    } elseif ($r->extra == 'conglomeradajs') {
                        $data = Ventas::getFacturasConglomerada($r);
                        $results = array(
                            "sEcho" => 1,
                            "iTotalRecords" => count($data),
                            "iTotalDisplayRecords" => count($data),
                            "aaData" => $data
                        );
                        return json_encode($results);
                    } elseif ($r->extra == 'facturadecaja') {
                        $data['table'] = true;
                        $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja';
                        $data['obseraudi'] = 'Ingreso a Factura de caja';
                        $data['titulo'] = 'Factura de caja';
                        $data['tipo_pago'] = TipoPago::getTipoPago('facturacaja');
                        $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja');
                    } elseif ($r->extra == 'facturadecajajs') {
                        $data =  Ventas::facturadecajajs($r);
                        $results = array(
                            "sEcho" => 1,
                            "iTotalRecords" => count($data),
                            "iTotalDisplayRecords" => count($data),
                            "aaData" => $data
                        );
                        return json_encode($results);
                    }
                }
                break;
            case 'getVentaPrint':
                return Ventas::getVentaPrint($r->id);
                break;
            case 'buscarCliente':
                return Clientes::getClienteCombo($r->term);
                break;
            case 'buscarPaciente':
                return Pacientes::getPacienteCombo($r->term);
                break;
            case 'buscarDoctores':
                return Usuarios::buscarUsuarioDoctor($r->term);
                break;
            case 'buscarSeguro':
                return Seguros::getSeguroCombo($r->id);
                break;
            case 'buscarProducto':
                return Productos::getProductoCombo($r->term);
                break;
            case 'descargarComprobanteVenta':
                return Ventas::descargarComprobanteVenta($r);
            break;
            case 'preciosNivel':
                return Productos::preciosNivel($r);
                break;
            case 'obtenerPuntoEmision':
                return PuntoEmision::obtenerPuntoEmision($r->id);
                break;
            case 'getMetodoPagoFactura':
                return Utilidades::getMetodoPagoFactura();
                break;
            case 'saveVenta':
                return Ventas::saveVenta($r);
                break;
            case 'guardarCruceValores':
                return Ventas::saveConglomerada($r);
                break;
            case 'modalCliente':
                return view($data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_modal_clientes');
                break;
            case 'printFactura':
                $data['html'] = Ventas::getVentaPrint($r->id);
                return view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_print_factura', $data);
                break;
            case 'crearInfoTributaria':
                $data['resultado'] = InfoTributaria::crearInfoTributaria($r);
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_documentos_electronicos';
                $data['obseraudi'] = 'Ingreso a ventas';
                $data['titulo'] = 'listadeventas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_documentos_electronicos');
                break;
            case 'buscarInsumos':
                return Ventas::buscarFacturas($r);
                break;
            case 'anularVentaVerificar':
                return Ventas::anularVentaVerificar($r);
                break;
            case 'anularVentaD':
                return Ventas::anularVentaD($r);
                break;
            case 'verificarStock':
                return Ventas::verificarStock($r);
                break;
            case 'ventaOrden':
                $data["tiposBancos"] = Bancos::getBancos();
                $data["sucursales"] = Sucursales::getSucursales();
                $data['fact_venta'] = OrdenVenta::searchRow($r->id);
                $data['bodega_in'] = Bodegas::traerBodegas();
                $data["ct_for_pag"] = OrdenVentaPago::getOnlyRow($r->id);
                $data["metodopago"] = TipoPago::getTipoPago();
                $data["consulta"] = Productos::getProductos("CONS-STAFF");
                $data["tiposTarjeta"] = TipoTarjeta::getTiposTarjeta();
                $data["seguros"] = Seguros::getSeguros();
                $data["listasBancos"] = Bancos::getBancos();
                $data['tipoIdentificacion'] = TipoIdentificacion::getTipoIdentificacion();
                $data["clase"] = Utilidades::traerCatalogo('Clase');
                $data['divisas'] = Divisas::traerDivisas();
                $data['paises'] = Paises::getPaises();
                $data["id_agenda"] = $data["fact_venta"]->id;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventas_orden';
                $data['obseraudi'] = 'Ingreso a ventas';
                $data['titulo'] = 'Registro de Factura de Ventas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ventas_orden');
                break;
            case 'facturadecaja':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja';
                $data['obseraudi'] = 'Ingreso a Factura de caja';
                $data['titulo'] = 'Factura de caja';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja');
                break;
            case 'guardarReciboCobro':
                return Ventas::guardarReciboCobro($r);
            case 'traerDatosClientes':
                return Clientes::getCliente($r);
                break;
            case 'facturadecajajs':
                $data =  Ventas::facturadecajajs($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'eliminarFacturaCaja';
                return Ventas::eliminarFacturaCaja($r);
                break;
            case 'pdfFacturaCaja':
                return Ventas::pdfFacturaCaja($r);
                break;
            case 'EditarFacturaCaja':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja_editar';
                $data['obseraudi'] = 'Ingreso a Factura de caja editar';
                $data['titulo'] = 'Factura de caja editar';
                $data['paciente'] = Ventas::EditarFacturaCajaget($r);
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_factura_de_caja_editar');
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
