<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\TipoIdentificacion;
use Sis_medico\v3\contable\CruceCuentasClientes;
use Sis_medico\v3\contable\ComprobanteIngreso;
use Sis_medico\v3\contable\ComprobanteIngresoVarios;
use Sis_medico\v3\contable\Retenciones_Clientes;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\CodigoImpuestos;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Rubros_Clientes;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\Usuarios;
use Sis_medico\v3\contable\DetallePagoIngreso;
use Sis_medico\v3\contable\DetalleComprobanteIngreso;
use Sis_medico\v3\contable\CruceValoresClientes;
use Sis_medico\v3\contable\DetallePagoCruce;
use Sis_medico\v3\contable\DetalleCruceClientes;
use Sis_medico\v3\contable\Divisas;
use Sis_medico\v3\contable\CajaBanco;
use Sis_medico\v3\contable\DetallePagoIngresoVarios;
use Sis_medico\v3\contable\DetalleComprobanteIngresoVarios;
use Sis_medico\v3\contable\MasterTipos;
use Sis_medico\v3\contable\NotaCreditoClientes;
use Sis_medico\v3\contable\NotadeCredito;
use Sis_medico\v3\contable\PuntoEmision;

class ClientesController extends Controller
{
    private $controlador = 'clientes';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'nuevanotadecredito':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_credito';
                $data['obseraudi'] = 'Ingreso a nueva nota de credito';
                $data['titulo'] = "Nueva nota de credito";
                $data['facturas'] = Ventas::getVentasSinRetenciones();
                $data['sucursales'] = Sucursales::getSucursales();
                $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
                $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_credito');
                break;
            case 'notadecreditojs':
                return NotaCreditoClientes::getNotasCredito('', $r);
                break;
            case 'notadecredito':
                $data['table'] = true;
                $data['titulo'] = 'Nota de crédito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_de_credito');
                break;
            case 'listadeclientes':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_submodulo.listadeclientes');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_clientes';
                $data['obseraudi'] = 'Ingreso a lista de clientes';
                $data["clase"] = Utilidades::traerCatalogo('Clase');
                $data['tipoIdentificacion'] = TipoIdentificacion::getTipoIdentificacion();
                $data['paises'] = Paises::getPaises();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_clientes');
                break;
            case 'listadeclientesjs':
                return Clientes::getClientes('', $r);
                /* $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);*/
                break;
            case 'crucedecuentas':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = trans('v3/new_submodulo.crucecuentasclientes');
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucecuentas';
                    $data['obseraudi'] = 'Ingreso a cruce de cuentas';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucecuentas');
                }
                break;
            case 'crucedecuentasjs':
                $data = CruceCuentasClientes::getCruceCuentas();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'crucedevalores':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = trans('v3/new_submodulo.crucedevalores');
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucevalores';
                    $data['obseraudi'] = 'Ingreso a cruce de valores';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucevalores');
                }
                break;
            case 'crucedevaloresjs':
                $data = CruceValoresClientes::getCruceValores();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'comprobantedeingreso':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = trans('v3/new_submodulo.comprobantedeingreso');
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeingreso';
                    $data['obseraudi'] = 'Ingreso a comprobante de ingreso';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeingreso');
                }
                break;
            case 'comprobantedeingresojs':
                $data = ComprobanteIngreso::getCompIngreso();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'comprobantedeingresovarios':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = trans('v3/new_submodulo.comprobantedeingresovarios');
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeingresovarios';
                    $data['obseraudi'] = 'Ingreso a comprobante de ingreso varios';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeingresovarios');
                }
                break;
            case 'comprobantedeingresovariosjs':
                $data = ComprobanteIngresoVarios::getCompIngresoVarios();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'buscarCliente':
                return Clientes::getClienteCombo($r->term);
                break;
            case 'buscarVendedor':
                return Usuarios::getVendedor($r->term);
                break;
            case 'getCliente':
                return  Clientes::getClientes($r->id, $r);
                break;
            case 'getCruceCuentas':
                return  CruceCuentasClientes::getCruceCuentas($r->id);
                break;
            case 'getCruceValores':
                return  CruceValoresClientes::getCruceValores($r->id);
                break;
            case 'getCompIngreso':
                return  ComprobanteIngreso::getCompIngreso($r->id);
                break;
            case 'getCompIngresoVarios':
                return  ComprobanteIngresoVarios::getCompIngresoVarios($r->id);
                break;
            case 'getDetalleCruceCuentas':
                return  CruceCuentasClientes::getDetalleCruceCuentas($r->id);
                break;
            case 'getDetalleValorRecibido':
                return  DetallePagoIngreso::getDetalleValorRecibido($r->id);
                break;
            case 'getDetalleValorRecibidoCompVarios':
                return  DetallePagoIngresoVarios::getDetalleValorRecibidoCompVarios($r->id);
                break;
            case 'getDetalleValoraFavor':
                return  DetallePagoCruce::getDetalleValoraFavor($r->id);
                break;
            case 'getObtenerAnticipos':
                return  ComprobanteIngreso::getObtenerAnticipos($r);
                break;
            case 'comprobanteCombo':
                return Ventas::comprobanteCombo($r->id);
                break;
            case 'saveCliente':
                return  Clientes::saveCliente($r);
                break;
            case 'estadoCliente':
                return Clientes::estadoCliente($r);
                break;
            case 'estadoCruce':
                return CruceCuentasClientes::estadoCruceCuentas($r);
                break;
            case 'retenciones':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_retenciones';
                    $data['obseraudi'] = 'Ingreso a retenciones';
                    $data['titulo'] = trans('v3/new_titulos.listaderetenciones');
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_retenciones');
                }
                break;
            case 'retencionesjs':
                $data = Retenciones_Clientes::getRetenciones($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'cambiarEstadoRetencion':
                return Retenciones_Clientes::cambiarEstadoRetencion($r);
                break;
            case 'estadoCompIngreso':
                return ComprobanteIngreso::estadoCompIngreso($r);
                break;
            case 'getRetencionPrint':
                return Retenciones_Clientes::getRetencionPrint($r->id);
                break;
            case 'nuevaretencion':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_retencion';
                $data['obseraudi'] = 'Ingreso a nueva retencion';
                $data['titulo'] = trans('v3/new_titulos.nuevaretencioncliente');
                $data['facturas'] = Ventas::getVentasSinRetenciones();
                $data['sucursales'] = Sucursales::getSucursales();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_retencion');
                break;
            case 'buscarClienteF':
                return Ventas::buscarClienteF($r->term);
                break;
            case 'obtenerFacturaxCliente':
                return Ventas::obtenerFacturaxCliente($r->id);
                break;
            case 'obtenerFactura':
                return Ventas::getVentas($r->id);
                break;
            case 'obtenerPorcentajeRetenciones':
                return CodigoImpuestos::getCodigoRetenciones($r->tipo);
                break;
            case 'saveRetencionCliente':
                return Retenciones_Clientes::saveRetencionCliente($r);
                break;
            case 'buscarValor':
                return CruceCuentasClientes::buscarVentas($r->id);
                break;
            case 'buscarCuenta':
                return ActivosFijos::buscarCuenta($r->term);
                break;
            case 'buscarFactVenta':
                return CruceCuentasClientes::getFactVenta($r->term);
                break;
            case 'nuevo_cruce_cuentas':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_cuentas';
                $data['obseraudi'] = 'Ingreso a cruce de cuentas clientes';
                $data['titulo'] = trans('v3/new_titulos.crucedecuentasclientes');
                $data['sucursales'] = Sucursales::getSucursales();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_cuentas');
                break;
            case 'nuevo_cruce_valores':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_valores';
                $data['obseraudi'] = 'Ingreso a cruce de valores clientes';
                $data['titulo'] = trans('v3/new_titulos.crucedevalores');
                $data['ventas'] = CruceCuentasClientes::buscarVentas();
                $data['sucursales'] = Sucursales::getSucursales();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_valores');
                break;
            case 'saveCruceCuentas':
                return CruceCuentasClientes::saveCruceCuentas($r);
                break;
            case 'saveCruceValores':
                return CruceValoresClientes::saveCruceValores($r);
                break;
            case 'saveCompIngreso':
                return ComprobanteIngreso::saveCompIngreso($r);
                break;
            case 'saveCompIngresovarios':
                return ComprobanteIngresoVarios::saveCompIngresovarios($r);
                break;
            case 'saveSuperavit':
                return ComprobanteIngreso::saveSuperavit($r);
                break;
            case 'getnombrecliente':
                return Clientes::getnombrecliente($r->term);
                break;
            case 'getComprobanteCliente':
                return Clientes::getComprobanteCliente($r);
                break;

            case 'newData2Cliente':
                return Clientes::newData2Cliente($r);
                break;
            case 'obtenerPuntoEmision':
                return PuntoEmision::obtenerPuntoEmision($r->id); 
                break;
            case 'guardarNotaCreditoCliente':
                return Clientes::guardarNotaCreditoCliente($r);
                break;
            case 'rubros':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_rubros';
                    $data['obseraudi'] = 'Ingreso a rubros';
                    $data['titulo'] = trans('v3/new_titulos.rubros');
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_rubros');
                }
                break;
            case 'rubrosjs':
                $data = Rubros_Clientes::getRubros();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'nuevo_compIngreso':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_compIngreso';
                $data['obseraudi'] = 'Ingreso a comprobante de ingreso';
                $data['titulo'] = trans('v3/new_titulos.comprobantedeingresoclientes');
                $data['sucursales'] = Sucursales::getSucursales();
                $data['ventas'] = CruceCuentasClientes::buscarVentas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_compIngreso');
                break;
            case 'nuevo_compIngresoVarios':
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_compIngresovarios';
                $data['obseraudi'] = 'Ingreso a comprobante de ingreso varios';
                $data['titulo'] = trans('v3/new_titulos.comprobantedeingresoclientes');
                $data['sucursales'] = Sucursales::getSucursales();
                $data['divisas'] = Divisas::traerDivisas();
                $data['cajaBanco'] = CajaBanco::getAllBank();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_compIngresovarios');
                break;
            case 'estadoRubros':
                return Rubros_Clientes::cambiarEstado($r);
                break;
            case 'saveRubros':
                return Rubros_Clientes::saveRubros($r);
                break;
            case 'buscarCuenta2':
                return ActivosFijos::buscarCuenta($r->term);
                break;
            case 'getConfiguracionRubros':
                return Rubros_Clientes::getRubros($r->codigo);
                break;
            case 'traerCuentaGeneral':
                return PlanCuentas::traerCuentaGeneral($r);
                break;
            case 'verificarRubro':
                return Rubros_Clientes::verificarRubro($r);
                break;
            case 'getMetodoPagoFactura':
                return Utilidades::getMetodoPagoFactura();
                break;
            case 'getDetalleDeudaCliente':
                return  ComprobanteIngreso::getDetalleDeudaCliente($r);
                break;
            case 'getDetalleDCliente':
                return  DetalleComprobanteIngreso::getDetalleDCliente($r->id);
                break;
            case 'getDetalleCompVarios':
                return  DetalleComprobanteIngresoVarios::getDetalleCompVarios($r->id);
                break;
            case 'getDetalleClienteValores':
                return  DetalleCruceClientes::getDetalleClienteValores($r->id);
                break;
            case 'anularCruce':
                return  CruceCuentasClientes::anularCruce($r);
                break;
            case 'anularCruceValores':
                return  CruceValoresClientes::anularCruceValores($r);
                break;
            case 'anularCompIngreso':
                return  ComprobanteIngreso::anularCompIngreso($r);
                break;
            case 'anularCompIngresoVarios':
                return  ComprobanteIngresoVarios::anularCompIngresoVarios($r);
                break;
            case 'mostrarPdfCompIngreso':
                $data = ComprobanteIngreso::datosPdf($r->id);
                $variables = [
                    'comp_ingreso' => $data["comp_ingreso"],
                    'empresa' => $data["empresa"],
                    'detalle_pago_ingreso' => $data["detalle_pago_ingreso"],
                    'cliente' => $data["cliente"],
                    'detalle_deudas_cliente' => $data["detalle_deudas_cliente"]
                ];
                $view = \View::make('v3.contable.clientes.pdf_compIngreso', $variables)->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('Comprobante.pdf');
                break;
            case 'mostrarPdfCompIngresoVarios':
                $data = ComprobanteIngresoVarios::datosPdf($r->id);
                $variables = [
                    'comp_ingreso_varios'    => $data["comp_ingreso_varios"],
                    'empresa'                => $data["empresa"],
                    'asiento_cabecera'       => $data["asiento_cabecera"],
                    'asiento_detalles'       => $data["asiento_detalles"],
                ];
                $view = \View::make('v3.contable.clientes.pdf_compIngresoVarios', $variables)->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('Comprobante_ingreso_varios.pdf');
                break;
            case 'verificarCliente':
                return Clientes::verificarCliente($r->id);
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
