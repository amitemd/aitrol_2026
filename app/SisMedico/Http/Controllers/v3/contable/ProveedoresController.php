<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\v3\contable\Bancos;
use Sis_medico\v3\contable\Retenciones;
use Sis_medico\v3\contable\TipoCuenta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\TipoProveedor;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Paises;
use Sis_medico\v3\contable\CajaBanco;
use Sis_medico\v3\contable\TipoIdentificacion;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\ComprobanteEgresoMasivo;
use Sis_medico\v3\contable\ComprobanteEgresoVario;
use Sis_medico\v3\contable\ComprobanteEgresoVarios;
use Sis_medico\v3\contable\ConfigBancos;
use Sis_medico\v3\contable\Divisas;
use Sis_medico\v3\contable\Rubros;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\TipoPago;
use Sis_medico\v3\contable\MasterTipos;
use Sis_medico\v3\contable\NotadeCreditoProveedores;
use Sis_medico\v3\contable\PorcentajeRetenciones;
use Sis_medico\v3\contable\CruceCuentas;
use Sis_medico\v3\contable\DebitoAcreedores;
use Sis_medico\v3\contable\GuiaRemision;
use Sis_medico\v3\contable\Plan_Cuentas;

class ProveedoresController extends Controller
{
    private $controlador = 'proveedores';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        if ($r->submodulo == 'listadeproveedores') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_proveedores';
            $data['obseraudi'] = 'Ingreso a la lista de proveedores';
            $data['titulo'] = trans('v3/new_tabla.listaProveedores');
            $data['paises'] = Paises::getPaises();
            $data['tipo_identificacion'] = TipoIdentificacion::getTipoIdentificacion();
            $data['cuenta'] = PlanCuentas::getAllCuentas();
            $data['bancos'] = Bancos::getBancos();
            $data['tipo_cuenta'] = TipoCuenta::getAllTiposCuentas();
            $data['tipo_proveedor'] = TipoProveedor::getTipoProveedor();
            $data['provisiones'] = Utilidades::traerCatalogo('provisiones');
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_proveedores');
        } elseif ($r->submodulo == 'listadeproveedoresjs') {
            $data = Proveedor::getProveedores();

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'getProveedor') {
            return Proveedor::getProveedores($r->id);
        } elseif ($r->submodulo == 'proveedor') {
            $data['table'] = true;
            $data['titulo'] = trans('v3/new_titulos.listadodeopcionesdelproveedor');
            $data['id'] = $r->id;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_proveedores';
            $data['obseraudi'] = 'Ingreso al proveedor';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_proveedores');
        } elseif ($r->submodulo == 'getAllTiposIdentificacion') {
            return TipoIdentificacion::getAllTiposIdentificacion($r->term);
        } elseif ($r->submodulo == 'getAllTipoSProveedor') {
            return TipoProveedor::getAllTiposProveedor($r->term);
        } elseif ($r->submodulo == 'getAllCuentas') {
            return PlanCuentas::getAllCuentas($r->term);
        } elseif ($r->submodulo == 'getAllBancos') {
            return Bancos::getAllBancos($r->term);
        } elseif ($r->submodulo == 'getAllTiposCuentas') {
            return TipoCuenta::getAllTiposCuentas($r->term);
        } elseif ($r->submodulo == 'saveProveedor') {
            return Proveedor::saveProveedor($r);
        } elseif ($r->submodulo == 'tipoIdentificacionCombo') {
            return TipoIdentificacion::tipoIdentificacionCombo($r->id);
        } elseif ($r->submodulo == 'cuentaContableCombo') {
            return PlanCuentas::cuentaContableCombo($r->id);
        } elseif ($r->submodulo == 'tipoCuentaCombo') {
            return TipoCuenta::tipoCuentaCombo($r->id);
        } elseif ($r->submodulo == 'bancoCombo') {
            return ConfigBancos::bancoCombo($r->id);
        } elseif ($r->submodulo == 'guardarArchivo') {
            return Utilidades::guardarArchivo($r);
        } elseif ($r->submodulo == 'getProveedorCombo') {
            return Proveedor::getProveedorCombo($r->term);
        } elseif ($r->submodulo == 'retenciones') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_retenciones';
                $data['obseraudi'] = 'Ingreso a retenciones';
                $data['titulo'] = "Lista de Retenciones";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_retenciones');
            }
        } elseif ($r->submodulo == 'retencionesjs') {
            $data = Retenciones::getRetenciones($r, 'todo');
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'verificarAnulacion') {
            return Retenciones::verificarAnulacion($r);
        } elseif ($r->submodulo == 'anularRetencion') {
            return Retenciones::anularRetencion($r);
        } elseif ($r->submodulo == 'saldosiniciales') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_saldos_iniciales';
            $data['obseraudi'] = 'Ingreso a saldos iniciales';
            $data['titulo'] = "Saldos Iniciales";
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_saldos_iniciales');
        } elseif ($r->submodulo == 'saldosinicialesjs') {
            $data = Compras::obtenerCompras();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'crucedecuentas') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_submodulo.crucedecuentas');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucecuentas';
                $data['obseraudi'] = 'Ingreso a cruce de cuentas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crucecuentas');
            }
        } elseif ($r->submodulo == 'crucedecuentasjs') {
            $data = CruceCuentas::getCruceCuentas();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else if ($r->submodulo == 'nuevo_cruce_cuentas') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_cuentas';
            $data['obseraudi'] = 'Ingreso a cruce de cuentas';
            $data['titulo'] = trans('v3/new_titulos.crucedecuentas');
            $data['sucursales'] = Sucursales::getSucursales();
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_cruce_cuentas');
        } elseif ($r->submodulo == 'saveCruceCuentasProveedor') {
            return CruceCuentas::saveCruceCuentasProveedor($r);
        } else if ($r->submodulo == 'nuevo_saldoinicial') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_saldoinicial';
            $data['obseraudi'] = 'Ingreso a saldos iniciales';
            $data['titulo'] = 'Saldos Iniciales';
            $data['formasDePago'] = Ventas::metodosDePagos();
            $data['sucursales'] = Sucursales::getSucursales();
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nuevo_saldoinicial');
        } elseif ($r->submodulo == 'obtenerPuntoEmision') {
            return PuntoEmision::obtenerPuntoEmision($r->id);
        } elseif ($r->submodulo == 'rubros') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_rubros';
            $data['obseraudi'] = 'Ingreso a rubros';
            $data['titulo'] = "Rubros";
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_rubros');
        } elseif ($r->submodulo == 'rubrosjs') {
            $data = Rubros::getRubros();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'estadoRubros') {
            return Rubros::cambiarEstado($r);
        } elseif ($r->submodulo == 'estadoProveedor') {
            return Proveedor::cambiarEstadoProveedor($r);
        } elseif ($r->submodulo == 'buscarValor') {
            return CruceCuentas::buscarCompras($r->id);
        } elseif ($r->submodulo == 'estadoRetenciones') {
            return Retenciones::cambiarEstado($r);
        } elseif ($r->submodulo == 'estadoSaldosIniciales') {
            return Compras::cambiarEstado($r);
        } elseif ($r->submodulo == 'buscarCuenta') {
            return ActivosFijos::buscarCuenta($r->term);
        } elseif ($r->submodulo == 'saveRubros') {
            return Rubros::saveRubros($r);
        } elseif ($r->submodulo == 'getConfiguracionRubros') {
            return Rubros::getRubros($r->codigo);
        } elseif ($r->submodulo == 'cuentaContable') {
            return Rubros::cuentaContable($r->id);
        } elseif ($r->submodulo == 'verificarRubro') {
            return Rubros::verificarRubro($r);
        } elseif ($r->submodulo == 'getRetencionPrint') {
            return Retenciones::getRetencionPrint($r);
        } elseif ($r->submodulo == 'printFactura') {
            $data['html'] = Retenciones::getRetencionPrint($r);
            return view('v3.contable.ventas.view_print_factura', $data);
        } elseif ($r->submodulo == 'nuevaretencion') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_retencion';
            $data['obseraudi'] = 'Ingreso a nueva retencion';
            $data['titulo'] = "Nueva retención";
            $data['facturas'] = Compras::getComprasSinRetenciones();
            $data['sucursales'] = Sucursales::getSucursales();
            $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
            $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
            $data['min_date'] = Ct_Cierre_Cabecera::ultima_fecha(); 
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_retencion');
        } elseif ($r->submodulo == 'obtenerFactura') {
            return Compras::getComprasAll($r->id, $r);
        } elseif ($r->submodulo == 'obtenerPorcentajeRetenciones') {
            return PorcentajeRetenciones::Geteverporcentajes($r->tipo);
        } elseif ($r->submodulo == 'saveRetencionProveedor') {
            return Retenciones::saveRetencionProveedor($r);
        } elseif ($r->submodulo == 'getTraerFacturas') {
            return Retenciones::traerRetenciones($r);
        } else if ($r->submodulo == 'comprobantedeegreso') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeegreso';
            $data['obseraudi'] = 'Ingreso a nuevo comprobante de egreso';
            $data['titulo'] = "Nueva comprobante de egreso";
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeegreso');
        } else if ($r->submodulo == 'crearComprobanteEgreso') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_comprobante_egreso';
            $data['bancos'] = CajaBanco::getListaCajas();
            $data["divisas"] = Divisas::traerDivisas();
            $data["formas_pago"] = TipoPago::getTipoPago();
            $data["comprobante"] = Proveedor::allComprobanteEgreso(["id" => $r->id]);
            $valor = null;
            $valor_banco = null;
            $detalle = null;
            if (!is_array($data["comprobante"]) && !($data["comprobante"] instanceof \Countable) && !is_null($data["comprobante"])) {
                $valor = Sucursales::getSucursales($data["comprobante"]->sucursal);
                $valor_banco = CajaBanco::getListaCajas($data["comprobante"]->banco);
                $detalle = $data["comprobante"]->detalles;
            }
            $data["detalle"] = $detalle;
            $data["edit_banco"] = $valor_banco;
            $data["edit_sucursal"] = $valor;
            $data['sucursales'] = Sucursales::getSucursales();
            $data['obseraudi'] = 'Crear nuevo comprobante de egreso';
            $data['titulo'] = "Crear comprobante de egreso";
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_comprobante_egreso');
        } else if ($r->submodulo == 'comprobantedeegresovario') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeegresov';
            $data['obseraudi'] = 'Ingreso a nuevo comprobante de egreso v';
            $data['titulo'] = "Nueva comprobante de egreso varios";
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_comprobantedeegresov');
        } else if ($r->submodulo == 'crearComprobanteEgresovarios') {
            $data['table'] = true;
            $data['titulo'] = "Crear comprobante de egreso";
            $data["formas_pago"] = TipoPago::getTipoPago();
            $data['bancos'] = CajaBanco::getListaCajas();
            $data["divisas"] = Divisas::traerDivisas();
            $data['cuentas'] = Plan_Cuentas::getCuentas($r);
            $data['sucursales'] = Sucursales::getSucursales();
            $data["comprobante"] = Proveedor::allComprobantesVarios(["id" => $r->id]);
            $valor = null;
            $valor_banco = null;
            $detalle = null;
            if (isset($r->id)) {
                $valor = Sucursales::getSucursales($data["comprobante"]->sucursal);
                $data["puntoEmision"] = ComprobanteEgresoMasivo::getPuntoEmision($data);
                $valor_banco = CajaBanco::getListaCajas($data["comprobante"]->banco);
                $detalle = $data["comprobante"]->detalles;
            }
            $data["detalle"] = $detalle;
            $data["edit_banco"] = $valor_banco;
            $data["edit_sucursal"] = $valor;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_comprobante_egreso_varios';
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_comprobante_egreso_varios');
        } else if ($r->submodulo == 'guardarComprobanteEgresoVario') {
            return ComprobanteEgresoMasivo::guardarComprobanteEgresoVario($r);
        } else if ($r->submodulo == 'buscarAcreedores') {
            return Proveedor::getProveedorCombo($r->term);
        } else if ($r->submodulo == 'buscarNombreProveedor') {
            return Proveedor::buscar_detalleAcreedores($r->id_empresa);
        } else if ($r->submodulo == "buscarProveedor") {
            return Proveedor::buscar_proveedor($r->id_proveedor);
        } else if ($r->submodulo == "getLastNumberChe") {
            return Proveedor::getLastNumberChe($r);
        } else if ($r->submodulo == "guardarComprobante") {
            return Proveedor::guardarComprobante($r);
        } else if ($r->submodulo == "buscarAntAnticipos") {
            return Proveedor::buscarAntAnticipos($r);
        } elseif ($r->submodulo == 'getPuntoEmision') {
            return GuiaRemision::obtenerPuntoEmision($r);
        } else if ($r->submodulo == "allComprobantes") {
            $data = Proveedor::allComprobanteEgreso($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else if ($r->submodulo == "allComprobantesVarios") {
            $data =   Proveedor::allComprobantesVarios($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else if ($r->submodulo == 'allCruce') {
            $data = Proveedor::allCruce($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else if ($r->submodulo == "estadoComprobanteEgreso") {
            return Proveedor::cambiarEstado($r);
        } else if ($r->submodulo == 'estadoCruceValor') {
            return Proveedor::cambiarEstadoCruce($r);
        } else if ($r->submodulo == 'mostrarPdf') {
            return Proveedor::mostrarPdf($r->id);
        } elseif ($r->submodulo == 'notadecredito') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.listadodenotasdecreditodelproveedor');
                $data['id'] = $r->id;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_de_credito';
                $data['obseraudi'] = 'Ingreso a Notas de credito proveedor';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_de_credito');
            }
        } elseif ($r->submodulo == 'notadecreditojs') {
            $data = NotadeCreditoProveedores::allNotasdeCredito();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } else if ($r->submodulo == "obtenerDetalleCompra") {
            return Compras::getDetalleCompra($r->id);
        } elseif ($r->submodulo == 'nuevanotadecredito') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_credito';
            $data['obseraudi'] = 'Ingreso a nueva nota de credito';
            $data['titulo'] = "Nueva nota de credito";
            $data['facturas'] = Compras::getComprasSinRetenciones();
            $data['sucursales'] = Sucursales::getSucursales();
            $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
            $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_credito');
        } elseif ($r->submodulo == 'anularNotaCredito') {
            return  NotadeCreditoProveedores::anularNotaCredito($r);
        } elseif ($r->submodulo == 'getNotadeCreditoPrint') {
            return NotadeCreditoProveedores::getNotadeCreditoPrint($r->id);
        } elseif ($r->submodulo == 'printFactura') {
            $data['html'] = NotadeCreditoProveedores::getNotadeCreditoPrint($r->id);
            return view('v3.ventas.view_print_factura', $data);
        } elseif ($r->submodulo == 'notadedebito') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.listadodenotasdedebitodelproveedor');
                $data['id'] = $r->id;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_de_debito';
                $data['obseraudi'] = 'Ingreso a Notas de debito proveedor';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_de_debito');
            }
        } elseif ($r->submodulo == 'notadedebitojs') {
            $data = DebitoAcreedores::allNotasdeDebito();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'nuevanotadedebito') {
            $data['table'] = true;
            $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_debito';
            $data['obseraudi'] = 'Ingreso a nueva nota de debito';
            $data['titulo'] = "Nueva nota de debito";
            $data['facturas'] = Compras::getComprasSinRetenciones();
            $data['sucursales'] = Sucursales::getSucursales();
            $data['tipoComprobante'] = MasterTipos::getTiposComprobantes('1');
            $data['c_tributario'] = MasterTipos::getTiposComprobantes('2');
            //$data['rubros'] = Rubros::getAllRubros();
            $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_debito');
        } elseif ($r->submodulo == 'getAllRubros') {
            return Rubros::getAllRubros();
        } elseif ($r->submodulo == 'obtenerFacturaProveedor') {
            return Compras::getComprasProveedor($r->id);
        } elseif ($r->submodulo == 'getNotadeDebitoPrint') {
            return DebitoAcreedores::getNotadeDebitoPrint($r->id);
        } else if ($r->submodulo == "crucedevalores") {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_registrocrucevalores';
                $data['obseraudi'] = 'Registro de Cruce de Valores';
                $data['titulo'] = "Nuevo cruce de valores";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_registrocrucevalores');
            }
        } else if ($r->submodulo == 'saveNotaCreditoProveedores') {
            return NotadeCreditoProveedores::saveNuevaNotaCredito($r);
        } else if ($r->submodulo == "compra_buscar_proveedornombre") {
            return Proveedor::compraBuscarProveedor($r);
        } else if ($r->submodulo == "obtener_anticipos") {
            return Proveedor::obtener_anticipos($r);
        } else if ($r->submodulo == 'crearCruceValor') {
            if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                $data['vista'] = 'v3.errors.view_401';
                $data['obseraudi'] = 'Sin autorizacion';
                $data['contenido'] = view('v3.errors.view_401', $data);
            } else {
                $data["sucursales"] = Sucursales::getSucursales();
                $data["proveedores"] = Proveedor::getAllProveedores();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_crucevalor';
                $data['obseraudi'] = 'Crear cruce valor';
            $data['min_date'] = Ct_Cierre_Cabecera::ultima_fecha();
                $data['titulo'] = "Crear cruce valor";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_crucevalor');
                $data["cruce"] = Proveedor::allCruce(["id" => $r->id]);
            }
        } else if ($r->submodulo == 'guardarCruceValores') {
            return Proveedor::saveCruceValores($r);
        } elseif ($r->submodulo == 'getCruceCuentas') {
            return  CruceCuentas::getCruceCuentas($r->id);
        } elseif ($r->submodulo == 'getDetalleCruceCuentas') {
            return  CruceCuentas::getDetalleCruceCuentas($r->id);
        } elseif ($r->submodulo == 'anularCruceProv') {
            return  CruceCuentas::anularCruceProv($r);
        } elseif ($r->submodulo == 'buscarFactCompra') {
            return CruceCuentas::getFactCompra($r->term);
        } else if ($r->submodulo == 'mostrarPddCruce') {
            $data = Proveedor::datosPdf($r->id);
            $variables = [
                'datos' => $data["datos"],
                'empresa' => $data["empresa"],
                'nombre' => $data["nombre"],
                'datos2' => $data["datos2"],
                'details' => $data["details"]
            ];
            $view = \View::make('contable.cruce_valores.pdf_crucesdevalores', $variables)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('Comprobante.pdf');
        } else if ($r->submodulo == 'mostrarPDFCruceCuentaProv') {
            $data = CruceCuentas::datosPdf($r->id);
            $variables = [
                'ccuentas' => $data["ccuentas"],
                'proveedor' => $data["proveedor"],
                'empresa' => $data["empresa"],
                'detalle_crucecuentasprov' => $data["detalle_crucecuentasprov"],
                'compras' => $data["compras"],
                'datos' => $data["datos"],
                'detalles' => $data["detalles"],
            ];
            $view = \View::make('v3.contable.proveedores.pdf_cruce_cuentas', $variables)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('Comprobante.pdf');
        } else if ($r->submodulo == 'anularComproEgresoVarios') {
            return ComprobanteEgresoMasivo::anularComprobanteMasivo($r);
        } else if ($r->submodulo == 'mostrarPdfIngreso') {
            $data = Proveedor::datosPdfingreso($r->id);
            $variables = [
                'letras' => $data["letras"],
                'empresa' => $data["empresa"],
                'comp_egreso' => $data["comp_egreso"],
                'asiento_cabecera' => $data["asiento_cabecera"],
                'total_str' => $data["total_str"],
                'asiento_detalle' => $data["asiento_detalle"],

            ];

            $view = \View::make('contable.comp_egreso_varios.pdf_comprobante_egreso_varios', $variables)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('resultado-' . $r->id . '.pdf');
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
