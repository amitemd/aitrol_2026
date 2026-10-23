<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\Bancos;
use Sis_medico\v3\contable\CajaBanco;
use Sis_medico\v3\contable\ConfigBancos;
use Sis_medico\v3\contable\DepositoBancario;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\Divisas;
use Sis_medico\v3\contable\GuiaRemision;
use Sis_medico\v3\contable\NotaDebitoBancaria;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\PorcentajeRetenciones;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\TipoPago;
use Sis_medico\v3\contable\TransferenciasBancarias;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\NotadeDebito;
use Sis_medico\Ct_Cierre_Cabecera;

class CajaBancosController extends Controller
{
    private $controlador = 'cajabancos';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'estadoConciliacionBancaria':
                return CajaBanco::cambiarEstadoConciliacion($r);
                break;
            case 'imprimirTransferencia':
                $registro = TransferenciasBancarias::findorfail($r->id);
                $detalle = AsientosCabecera::where('id', $registro->id_asiento)->first();
                $detalle_asiento = AsientosDetalles::where('id_asiento_cabecera', $detalle->id)->get();
                $vistaurl =  'v3.' . $this->carpeta . '.' . $this->controlador . '.pdf_transferencia';
                $view = \View::make($vistaurl, compact('registro', 'detalle', 'detalle_asiento'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->stream('Trasnferencia Bancaria-' . $r->id . '.pdf');
                break;
            case 'imprimirDeposito':
                $registro = DepositoBancario::findorfail($r->id);
                $vistaurl = 'v3.' . $this->carpeta . '.' . $this->controlador . '.pdf_deposito_bancario';
                $view = \View::make($vistaurl, compact('registro'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->stream('Trasnferencia Bancaria-' . $r->pid . '.pdf');
                break;
            case 'listadebancos':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.listadebancos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_bancos';
                $data['obseraudi'] = 'Ingreso al listado de bancos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_bancos');
                break;
            case 'verificarAutorizacion':
                return CajaBanco::verificarAutorizacion($r);
                break;
            case 'saveRentencion':
                return CajaBanco::saveRentencion($r);
                break;
            case 'listadebancosjs':
                $data = Bancos::getListabancos();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'crearcajabanco':
                $data['sucursales'] = Sucursales::getSucursales();
                $data['divisa'] = Divisas::traerDivisas();
                $data['formas_pago'] = TipoPago::getTipoPago();
                $data['grupo'] = CajaBanco::getGrupo();
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.crearcajaybancos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' .  $this->controlador . '.view_crear_caja_bancos';
                $data['obseraudi'] = 'Ingreso a crear caja y bancos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_caja_bancos');
                break;
            case 'crearcajabancojs':
                $data = CajaBanco::getListaCajas();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'saveBancos':
                return Bancos::saveBancos($r);
                break;
            case 'saveCajaBanco':
                return CajaBanco::saveBancos($r);
                break;
            case 'getConfiguracion':
                return Bancos::getListabancos($r->id);
                break;
            case 'estadoConfigNomina':
                return Bancos::cambiarEstado($r);
                break;
            case 'estadoConfigNomina2':
                return CajaBanco::cambiarEstado($r);
                break;
            case 'getConfiguracionCajaBanco':
                return CajaBanco::getListaCajas($r->id);
                break;
            case 'getCuentaMayor':
                return CajaBanco::getCuentaMayor($r);
                break;
            case 'traerctaGrupo':
                return CajaBanco::traerctaGrupo($r);
                break;
            case 'anularTrasferenciaBancaria':
                return CajaBanco::anularTrasferencia($r);
                break;
            case 'transferenciabancaria':
                if (!Utilidades::verificarPermisos($r, $this->controlador)) {
                    $data['vista'] = 'v3.errors.view_401';
                    $data['obseraudi'] = 'Sin autorizacion';
                    $data['contenido'] = view('v3.errors.view_401', $data);
                } else {
                    $data['table'] = true;
                    $data['titulo'] = trans('v3/new_titulos.transferenciabancaria');
                    $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_transferencias_bancarias';
                    $data['obseraudi'] = 'Ingreso al listado de bancos';
                    $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_transferencias_bancarias');
                }
                break;
            case 'transferenciabancariajs':
                $data = TransferenciasBancarias::getTransferenciasBancarias('', $r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'depositobancario':
                $data['table'] = true;
                $data['titulo'] = "Deposito Bancario";
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_deposito_bancario';
                $data['obseraudi'] = 'Ingreso al listado de depositos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_deposito_bancario');
                break;
            case 'crearDepositoBancario':
                $data['titulo'] = "Crear deposito bancario";
                $data["formaPago"] = TipoPago::getTipoPago();
                $data["sucursales"] = Sucursales::getSucursales();
                $data["registroGu"] = CajaBanco::allDataDepositoBancario($r->id);
                $data["cuentas"] = PlanCuentas::allCuentas();
                $data["cajaBanco"] = CajaBanco::getAllBank();
                $data['min_date'] = Ct_Cierre_Cabecera::ultima_fecha();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_creardeposito_bancario';
                $data['obseraudi'] = 'Crear deposito depositos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_creardeposito_bancario');
                break;
            case 'conciliacion':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.conciliaciones');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_conciliaciones';
                $data['obseraudi'] = 'Ingreso al listado de conciliaciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_conciliaciones');
                break;
            case 'getPlandeCuentas':
                return PlanCuentas::allPlanCuenta();
                break;
            case 'conciliacionjs':
                $data = CajaBanco::getConciliacionMes();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'agregarConciliacion':
                if ($r->i != '') {
                    $data['idMes'] = $r->i;
                    $data['parametros'] = CajaBanco::verConciliacion($r->i);
                }
                $data['titulo'] = trans('v3/new_titulos.nuevaconciliacion');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_conciliacion';
                $data['obseraudi'] = 'Ingreso al listado de conciliaciones';
                $data['table'] = true;
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_conciliacion');
                break;
            case 'buscarBancos':
                return CajaBanco::buscarBancos($r->term);
                break;
            case 'buscarformapago':
                return CajaBanco::tablaDepositoBancario($r);
                break;
            case 'taerConciliacionPeriodo':
                return CajaBanco::traerConciliacionPeriodo($r);
                break;
            case 'guardarDepositoBancario':
                return CajaBanco::guardarDepositoBancario($r);
                break;
            case 'getPuntoEmision':
                return GuiaRemision::obtenerPuntoEmision($r);
                break;
            case 'anularNotaCredito':
                return CajaBanco::anularNotaCredito($r);
                break;
            case 'notadecredito':
                $data['table'] = true;
                $data['titulo'] = "Listado de Notas de Crédito";
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_credito';
                $data['obseraudi'] = 'Notas de Crédito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nota_credito');
                break;
            case 'allNotaCreditojs':
                $data = CajaBanco::allDataNotaCredito($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'crearNotaCredito':
                $data['titulo'] = "Crear Nota de Crédito";
                $data["formaPago"] = TipoPago::getTipoPago();
                $data["sucursales"] = Sucursales::getSucursales();
                $data["registroGu"] = CajaBanco::allDataDepositoBancario($r->id);
                $data["cuentas"] = PlanCuentas::allCuentas();
                $data["banco"] = CajaBanco::getAllBank();
                $data["divisas"] = Divisas::getDivisas();
                $data["plan_cuentas"] = PlanCuentas::traerCuentas();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearnotacredito';
                $data['obseraudi'] = 'Crear Nota de Crédito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crearnotacredito');
                break;
            case 'allDataDepositoBacancariojs':
                $data = CajaBanco::allDataDepositoBancario($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;
            case 'verificarConciliacionDeposito':
                return CajaBanco::verificarConciliacionDeposito($r);
                break;
            case 'verificarConciliacionTransferencia':
                return CajaBanco::verificarConciliacionTransferencia($r);
                break;
            case 'verificarConciliacionNotaDebito':
                return CajaBanco::verificarConciliacionNotaDebito($r);
                break;
            case 'verificarConciliacionNotaCredito':
                return CajaBanco::verificarConciliacionNotaCredito($r);
                break;
            case 'verificarConciliacionDebitoBancario':
                return CajaBanco::verificarConciliacionDebitoBancario($r);
                break;
            case 'verificarConciliacionEgresos':
                return CajaBanco::verificarConciliacionEgresos($r);
                break;
            case 'verificarConciliacionEgresosVarios':
                return CajaBanco::verificarConciliacionEgresosVarios($r);
                break;
            case 'verificarConciliacionEgresosMasivos':
                return CajaBanco::verificarConciliacionEgresosMasivos($r);
                break;
            case 'verificarConciliacionPendientes':
                return CajaBanco::verificarConciliacionPendientes($r);
                break;
            case 'anularDepositoBnacario':
                return CajaBanco::anularDeposito($r);
                break;
            case 'verDocumentosPendientes':
                return  CajaBanco::verDocumentosPendientes($r);
                break;
            case 'crearTransferenciaBancaria':
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_transferencias_bancarias';
                $data['titulo'] = 'Nueva transferencia';
                $data['sucursales'] = Sucursales::getSucursales();
                $data['obseraudi'] = 'Ingreso a ingreso de nota debito bancario';
                $data['bancos'] = CajaBanco::getAllBank();
                $data['divisas'] = Divisas::traerDivisas();
                $data['retenciones'] = PorcentajeRetenciones::Geteverporcentajes(1);
                $data['retencionesFue'] = PorcentajeRetenciones::Geteverporcentajes(2);
                $data['min_date'] = Ct_Cierre_Cabecera::ultima_fecha();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_crear_transferencias_bancarias');
                break;
            case 'saveConciliacion':
                return CajaBanco::saveConciliacion($r);
                break;
            case 'getAsiento':
                return NotaDebitoBancaria::getAsiento($r->id);
                break;
            case 'getDebito':
                return NotaDebitoBancaria::getNotaDebito($r->id, $r);
                break;
            case 'obtenerPuntoEmision':
                return PuntoEmision::obtenerPuntoEmision($r->id);
                break;
            case 'getAllCuentas':
                return PlanCuentas::getAllCuentas($r->term);
                break;
            case 'getAllBancos':
                return ConfigBancos::getAllBancos($r->term);
                break;
            case 'saveNotaDebito':
                return NotaDebitoBancaria::saveDebito($r);
                break;
            case 'verificarbanco':
                return Bancos::verificarbanco($r);
                break;
            case 'verificarNombrecajabanco':
                return CajaBanco::verificarNombrecajabanco($r);
                break;
            case 'notadedebitobancaria':
                $data["sucursales"] = Sucursales::getSucursales();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_nota_debito';
                $data['obseraudi'] = 'Ingreso a ingreso de nota debito bancario';
                $data['titulo'] = trans('v3/new_titulos.nuevaNotaDebito');
                $data['divisas'] = Divisas::traerDivisas();
                $data['cuentas'] = PlanCuentas::getAllCuentas();
                $data['proveedores'] = Proveedor::getAllProveedores();
                $data['bancos'] = CajaBanco::getListaCajas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_nota_debito');
                break;
            case 'saveNotaDebito':
                return NotaDebitoBancaria::saveDebito($r);
                break;
            case 'menuArmar':
                return CajaBanco::menuArmar();
                break;
            case 'getFacturasProveedor':
                return AsientosDetalles::getFacturasProveedor($r->id_proveedor);
                break;
            case 'pdfDatosConciliacion':
                return CajaBanco::pdfDatosConciliacion($r);
                break;
            case 'excelDatosConciliacion':
                return CajaBanco::excelDatosConciliacion($r);
                break;
            case 'traerAutorizaciones':
                return CajaBanco::traerAutorizaciones($r);
                break;
            case 'traerDatosGenera':
                return CajaBanco::traerDatosGenera($r);
                break;
            case 'saveTransferenciaBancaria':
                return CajaBanco::saveTransferenciaBancaria($r);
                break;
            case 'addDebito':
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_nota_debito';
                $data['obseraudi'] = 'Ingreso a ingreso de nota debito bancario';
                $data['titulo'] = trans('v3/new_titulos.nuevaNotaDebito');
                $data['divisas'] = Divisas::traerDivisas();
                $data['cuentas'] = PlanCuentas::getAllCuentas();
                $data['proveedores'] = Proveedor::getAllProveedores();
                $data['bancos'] = CajaBanco::getListaCajas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ingreso_nota_debito');
                break;
            case 'notadedebito':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.NotaDebito');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_de_notas_debito';
                $data['obseraudi'] = 'Ingreso a Nota de dèbito Caja Banco';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_lista_de_notas_debito');
                break;
            case 'notadedebitojs':
                $data = NotadeDebito::getNotaDebito('', $r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
                break;

            case 'registraranticipo':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.registrarAnticipo');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_registro_anticipos';
                $data['obseraudi'] = 'Registrar Anticipo';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_registro_anticipos');
                break;

            case 'cajachica':
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.registrarReposicionCajaChica');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_registro_reposicion_caja_chica';
                $data['obseraudi'] = 'Registrar Anticipo';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_registro_reposicion_caja_chica');
                break;
            case 'getNotadeDebitoCajaBancoPrint':
                return NotadeDebito::getNotadeDebitoCajaBancoPrint($r->id);
                break;
            case 'nuevanotadedebitocajabanco':
                $data['table'] = true;
                $data["sucursales"] = Sucursales::getSucursales();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_debito_caja_banco';
                $data['obseraudi'] = 'Ingreso a nueva nota de debito Caja Banco';
                $data['titulo'] = "Nueva nota de debito Caja Banco";
                $data['bancos'] = CajaBanco::getListaCajas();
                $data['divisas'] = Divisas::traerDivisas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_nueva_nota_de_debito_caja_banco');
                break;
            case 'movimientosbancarios':
                $data['table'] = true;
                $data["sucursales"] = Sucursales::getSucursales();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_movimiento_bancario';
                $data['obseraudi'] = 'Ingreso a nueva nota de debito Caja Banco';
                $data['titulo'] = "Nueva nota de debito Caja Banco";
                $data['bancos'] = CajaBanco::getListaCajas();
                $data['divisas'] = Divisas::traerDivisas();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_movimiento_bancario');
                break;
            case 'saveNotaDebitoCajaBanco':
                return NotadeDebito::saveNotaDebitoCajaBanco($r);
                break;
            case 'anularNotaCredito':
                return  NotadeDebito::anularNotaDebitoCajaBanco($r);
                break;
            case 'pdf_nota_debito':
                return NotadeDebito::pdf_nota_debito($r->id);
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
