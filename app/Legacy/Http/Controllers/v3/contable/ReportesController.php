<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\ComprobanteEgresoMasivo;
use Sis_medico\v3\contable\Contabilidad;
use Sis_medico\v3\contable\NotadeCreditoProveedores;
use Sis_medico\v3\contable\Ventas;
use Excel;
use Exception;
use Sis_medico\v3\contable\NotadeDebitoClientes;
use Sis_medico\v3\contable\Retenciones;
use Sis_medico\v3\contable\DebitoAcreedores;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\ComprobanteIngreso;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\ComprobanteEgreso;
use Sis_medico\v3\contable\Empresas;

class ReportesController extends Controller
{
    private $controlador = 'reportes';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        $data['extra_'] = $r->extra;
        if ($r->submodulo == 'proveedores') {
            if ($r->extra == 'egresomasivo') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.egresosmasivo');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_egresomasivo';
                $data['obseraudi'] = 'Ingreso al reporte de egresos masivos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_egresomasivo');
            } elseif ($r->extra == 'egresomasivojs') {
                $data = ComprobanteEgresoMasivo::getEgresosMasivos();
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'deudasvspagos') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.deudasvspagos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudasvspagos';
                $data['obseraudi'] = 'Ingreso al reporte de deudasvs pagos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudasvspagos');
            } elseif ($r->extra == 'deudasvspagosproveedoresjs') {
                $data = Compras::getDeudasvsPagos($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'notadecredito') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.notasdecredito');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdecredito';
                $data['obseraudi'] = 'Ingreso al reporte de notas de crédito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdecredito');
            } elseif ($r->extra == 'notasdeCreditoProveedorjs') {
                $data = NotadeCreditoProveedores::getNotasdeCredito($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'notadedebito') {
                $data['table'] = true;
                $data['titulo'] =  trans('v3/new_titulos.notasdedebito');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdedebito';
                $data['obseraudi'] = 'Ingreso al reporte de notas de debito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdedebito');
            } elseif ($r->extra == 'notasdeDebitoProveedoresjs') {
                $data = DebitoAcreedores::getNotasdeDebitoAcreedores($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'retencionesproveedores') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.retencionesproveedores');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_retencionesproveedores';
                $data['obseraudi'] = 'Ingreso al reporte de retenciones proveedores';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_retencionesproveedores');
            } elseif ($r->extra == 'retencionesProveedorjs') {
                $data = Retenciones::getreporteRetenciones($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'deudaspendientes') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudas_pendientes';
                $data['obseraudi'] = 'Ingreso a deudas pendientes';
                $data['titulo'] = "Deudas Pendientes";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudas_pendientes');
            } elseif ($r->extra == 'DeudasPendientesjs') {
                return Proveedor::getreporteDeudasPendientes($r);
            } elseif ($r->extra == 'anticipos') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_anticipos';
                $data['obseraudi'] = 'Ingreso a deudas pendientes';
                $data['titulo'] = "Anticipo proveedores";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_anticipos');
            } elseif ($r->extra == 'informesaldos') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informe_saldos';
                $data['obseraudi'] = 'Ingreso a informe saldos';
                $data['titulo'] = "Informe saldos";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informe_saldos');
            } elseif ($r->extra == 'carteraporpagar') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cartera_por_pagar';
                $data['obseraudi'] = 'Cartera por Pagar';
                $data['option'] = '';
                if ($r->desde !== null || $r->hasta !== null) {
                    $data['input'] = $r->input();
                    $data['proveedor'] = Proveedor::CarteraPagar($r);
                    $data['option'] = Proveedor::armarProveedor($r);
                    //dd( $data['option']);
                }
                $data['titulo'] = "Cartera por Pagar";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cartera_por_pagar');
            } elseif ($r->extra == 'chequesgirados') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.chequesgirados');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cheques_girado';
                $data['obseraudi'] = 'Ingreso al reporte de cheques girados';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cheques_girado');
            } elseif ($r->extra == 'informeanticipos') {
                $data['table'] = true;
                if ($r->d != "" && $r->h != "") {
                    $data['d'] = $r->d;
                    $data['h'] = $r->h;
                }
                $data['titulo'] = trans('v3/new_titulos.informeanticipos');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informeanticipos';
                $data['obseraudi'] = 'Ingreso al reporte de informe anticipos proveedor';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informeanticipos');
            } elseif ($r->extra == 'InformeAnticiposProveedoresjs') { /* MIO CAMBIAR*/
                $data = ComprobanteEgreso::getreporteInformeAnticipo($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'chequesgiradosjs') {
                return Proveedor::getChequesGirados($r);
            } elseif ($r->extra == 'InformeSaldosjs') {
                return Proveedor::InformeSaldos($r);
            } elseif ($r->extra == 'carterapagarjs') {
                return Proveedor::CarteraPagar($r);
            } elseif ($r->extra == 'InformeTiempojs') {
                $data = Proveedor::getFacturas($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } else if ($r->extra == 'indexReporteTiempo') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informe_tiempo';
                $data['obseraudi'] = 'Ingreso al informe de tiempo';
                $data['titulo'] = "Informe de tiempo";
                $data['Proveedor'] = Proveedor::getAllProveedores();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_informe_tiempo');
            } else {
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
            }
        } elseif ($r->submodulo == 'excelCarteraPagar') {
            return Utilidades::excelCarteraPagar($r);
        } elseif ($r->submodulo == 'excelInformeSaldo') {
            return Utilidades::excelInformeSaldo($r);
        } elseif ($r->submodulo == 'excelDeudas') {
            return  Excel::download(Utilidades::exceDeudasP($r));
        } elseif ($r->submodulo == 'buscarEgresoMasivo') {
            $data = ComprobanteEgresoMasivo::buscarEgresoMasivo($r);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->submodulo == 'ventas') {
            if ($r->extra == 'ventasnetas') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.ventasnetas');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_ventasnetas';
                $data['obseraudi'] = 'Ingreso al reporte de ventas netas';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_ventasnetas');
            } elseif ($r->extra == 'ventasnetasjs') {
                $data = Ventas::getVentasNetas($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } else {
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
            }
        } elseif ($r->submodulo == 'contables') {
            if ($r->extra == 'balancederesultados') {
                $data['table'] = true;
                $data['titulo'] = 'Balance de resultados';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancederesultados';
                $data['obseraudi'] = 'Ingreso al reporte de balance de resultados';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancederesultados');
            } elseif ($r->extra == 'excelbalancedecomprobacion') {
                return Excel::download(Utilidades::excelBalanceComprobacion($r));
            } elseif ($r->extra == 'librodiario') {
                $data['table'] = true;
                $data['titulo'] = 'Libro diario';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_libro_diario';
                $data['obseraudi'] = 'Ingreso al reporte de libro diario';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_libro_diario');
            } elseif ($r->extra == 'detalleLibroDiario') {
                $data = AsientosCabecera::getLibroDiario($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'mayorizacion') {
                $data['table'] = true;
                $data['titulo'] = 'Mayorización';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_mayorizacion';
                $data['obseraudi'] = 'Ingreso al reporte de mayorización';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['cuentas'] = AsientosCabecera::getMayorizacion();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_mayorizacion');
            } elseif ($r->extra == 'flujodeefectivo') {
                $data['titulo'] = 'Flujo de efectivo';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_flujodeefectivo';
                $data['obseraudi'] = 'Ingreso al reporte de flujo de efectivo';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['ingresosOperativos'] = ComprobanteIngreso::ingresosOperativos();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_flujodeefectivo');
            } elseif ($r->extra == 'balancedecomprobacion') {
                $data['table'] = true;
                $data['titulo'] = 'Balance de comprobación';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancedecomprobacion';
                $data['obseraudi'] = 'Ingreso al reporte de balance de comprobacion';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancedecomprobacion');
            } elseif ($r->extra == 'detalleBalanceComprobacion') {
                return $data = Contabilidad::getBalanceComprobacion($r);
            } elseif ($r->extra == 'detalleBalanceComprobacionAnulacion') {
                return $data = Contabilidad::detalleBalanceComprobacionAnulacion($r);
            } elseif ($r->extra == 'detalleBalanceResultados') {
                return $data = Contabilidad::getBalanceResultados($r);
            } elseif ($r->extra == 'balancefinanciero') {
                $data['table'] = true;
                $data['img'] = true;
                $data['titulo'] = 'Balance financiero';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancefinanciero';
                $data['obseraudi'] = 'Ingreso al reporte de balance financiero';
                $data['desde'] = $r->desde != '' ? date('Y-m-d', strtotime(str_replace('/', '-', $r->desde))) : '';
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['contenido'] = view(
                    'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_balancefinanciero',
                    [
                        'desde' => $data['desde'],
                        'hasta' => $data['hasta'],
                    ]
                );
            } elseif ($r->extra == 'detalleBalanceFinanciero') {
                return $data = Contabilidad::detalleBalanceFinanciero($r);
            } elseif ($r->extra == 'libromayor') {
                $data['table'] = true;
                $data['titulo'] = 'Libro mayor';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_libro_mayor';
                $data['obseraudi'] = 'Ingreso al reporte de libro diario';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $cuenta = $r->cuentadesde;
                $data['cuentadesde'] = $cuenta;
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_libro_mayor');
            } elseif ($r->extra == 'libroMayorjs') {
                $data = Contabilidad::getLibroMayor($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            }
        } elseif ($r->submodulo == 'clientes') {
            if ($r->extra == 'notadedebito') {
                $data['table'] = true;
                $data['titulo'] = trans('v3/new_titulos.notasdedebito');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdedebito';
                $data['obseraudi'] = 'Ingreso al reporte de notas de debito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_notasdedebito');
            } elseif ($r->extra == 'notasdeDebitoClientesjs') {
                $data = NotadeDebitoClientes::getNotasdeDebitoClientes($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'retenciones') {
                $data['table'] = true;
                $data['titulo'] = 'Clientes Retención';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_clientesretenciones';
                $data['obseraudi'] = 'Ingreso al reporte de clientes retención';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_clientesretenciones');
            } elseif ($r->extra == 'retencionesFunctionjs') {
                $data = Clientes::retenciones($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'deudasvspagos') {
                $data['table'] = true;
                if ($r->d != "" && $r->h != "") {
                    $data['d'] = $r->d;
                    $data['h'] = $r->h;
                }
                $data['titulo'] = trans('v3/new_titulos.deudasvspagosclientes');
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudasvspagosclientes';
                $data['obseraudi'] = 'Ingreso al reporte de deudas vs pagos clientes';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudasvspagosclientes');
            } elseif ($r->extra == 'DeudasvsPagosClientesjs') {
                $data = Clientes::getreporteDeudasvsPagos($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'nota_credito') {
                //adesso
                $data['table'] = true;
                $data['titulo'] = 'Notas de Credito';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_nota_credito';
                $data['obseraudi'] = 'Notas de Credito';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_nota_credito');
            } elseif ($r->extra == 'notasdeCreditoClientesjs') {
                $data = Clientes::nota_credito($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            } elseif ($r->extra == 'anticipos') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_anticipos';
                $data['obseraudi'] = 'Ingreso a anticipo clientes';
                $data['titulo'] = "Anticipo clientes";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_anticipos');
            } elseif ($r->extra == 'deudaspendientes') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudas_pendientes';
                $data['obseraudi'] = 'Ingreso a deudas pendientes clientes';
                $data['titulo'] = "Deudas pendientes clientes";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_deudas_pendientes');
            } elseif ($r->extra == 'carteravencida') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cartera_vencida';
                $data['obseraudi'] = 'Ingreso a cartera vencida clientes';
                $data['titulo'] = "Cartera vencida clientes";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_cartera_vencida');
            } elseif ($r->extra == 'DeudasPendientesClientejs') {
                return Clientes::getreporteDeudasPendientes($r);
            } elseif ($r->extra == 'edaddecartera') {
                $data['table'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_edad_cartera';
                $data['obseraudi'] = 'Ingreso a edad de cartera';
                $data['titulo'] = "Anticipo clientes";
                $data['cuentascobrar'] = Clientes::cuentasPorCobrar();
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_edad_cartera');
            } else {
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
            }
        } elseif ($r->submodulo == 'financieros') {
            if ($r->extra == 'indicesfinancieros') {
                $data['titulo'] = 'Índices financieros';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_indice_financiero';
                $data['obseraudi'] = 'Ingreso los indices financieros';
                $desde = str_replace('/', '-', $r->desde != '' ? $r->desde : date('Y') . '-01-01');
                $desde = date('Y-m-d', strtotime($desde));
                $data['desde'] = $desde;
                $hasta = str_replace('/', '-', $r->hasta != '' ? $r->hasta : date('Y-m-d'));
                $hasta = date('Y-m-d', strtotime($hasta));
                $data['hasta'] = $hasta;
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_indice_financiero');
            } elseif ($r->extra == 'detalleRatiosFinanciero') {
                return Contabilidad::detalleRatiosFinanciero($r);
            }
        } elseif ($r->submodulo == 'pdfBalanceFinanciero') {
            return Utilidades::pdfBalanceFinanciero($r, 'resultados');
        } elseif ($r->submodulo == 'excelBalanceFinanciero') {
            return Utilidades::excelBalanceFinanciero($r, 'resultados');
        } elseif ($r->submodulo == 'excelResultados') {
            return Utilidades::descargarArchivo($r, 'resultados');
        } elseif ($r->submodulo == 'pdfResultados') {
            return Utilidades::descargarArchivoPdf($r, 'resultados');
        } elseif ($r->submodulo == 'excelDeudasPendientes') {
            return Utilidades::excelDeudasPendientes($r);
        } elseif ($r->submodulo == 'activosfijos') {
            if ($r->extra == 'porsucursales') {
                $data['table'] = true;
                $data['titulo'] = 'Lista de activos';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_lista_activos';
                $data['obseraudi'] = 'Lista de activos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.' . $r->submodulo . '.view_lista_activos');
            } elseif ($r->extra == 'activosFijosSucursaljs') {
                $data = ActivosFijos::getActivosFijosSucursales($r);
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                return json_encode($results);
            }
        } elseif ($r->submodulo == 'excelResultadoMensual') {
            return Contabilidad::excelResultadoMensual($r);
        } elseif ($r->submodulo == 'excelDeudasClientes') {
            return Contabilidad::excelDeudasClientes($r);
        } elseif ($r->submodulo == 'excelBalanceComprobacion') {
            $datos = Contabilidad::getBalanceComprobacionSolo($r, "return");
            $empresa = Empresas::find(session("id_empresa"));
            return Excel::create('Balance de Comprobación', function ($excel) use ($empresa, $r, $datos) {
                $excel->sheet('Reporte', function ($sheet)  use ($empresa, $r, $datos) {
                    $sheet->mergeCells('A1:H1');
                    $sheet->cell('A1', function ($cell) {
                        $cell->setValue('Balance de Comprobación');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(16);
                        
                        $cell->setAlignment('center');
                    });

                    $sheet->mergeCells('A2:H2');
                    $sheet->cell('A2', function ($cell) use ($empresa) {
                        $cell->setValue('Empresa ' . $empresa->razonsocial);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(14);
                        $cell->setAlignment('center');
                    });

                    $sheet->mergeCells('A3:H3');
                    $sheet->cell('A3', function ($cell) use ($r) {
                        $cell->setValue('Desde: ' . $r->d . ' al ' . $r->h);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(14);
                        $cell->setAlignment('center');
                    });


                    $sheet->cell('A5', function ($cell) {
                        $cell->setValue('Plan');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });

                    $sheet->cell('B5', function ($cell) {
                        $cell->setValue('Descripción');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });


                    $sheet->cell('C5', function ($cell) {
                        $cell->setValue('Inicial Deudor');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });


                    $sheet->cell('D5', function ($cell) {
                        $cell->setValue('Inicial Acreedor');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });

                    $sheet->cell('E5', function ($cell) {
                        $cell->setValue('Debe');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });


                    $sheet->cell('F5', function ($cell) {
                        $cell->setValue('Haber');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });

                    $sheet->cell('G5', function ($cell) {
                        $cell->setValue('Deudor');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });

                    $sheet->cell('H5', function ($cell) {
                        $cell->setValue('Acreedor');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setAlignment('center');
                    });

                    $comienzo = 6;
                    $totalDebe = 0;
                    $totalHaber = 0;
                    $totalAcreedor = 0;
                    $totalDeudor = 0;

                    $sumarTotalDeudor = 0;
                    $sumarTotalAcredor = 0;

                    $sumarTotalDeudorF = 0;
                    $sumarTotalAcredorF = 0;
                    $newArray = $datos;
                    $arrayNew = [];
                    foreach ($datos as $value) {
                        $indexEncotrados = [];
                        foreach ($newArray as $key => $index) {
                            if ($index->plan === $value->plan) {
                                array_push($indexEncotrados, $key);
                            }
                        }
                        $totalDebe += $value->debe;
                        $totalHaber += $value->haber;
                        $totalAcreedor += $value->saldo_acreedor;
                        $totalDeudor += $value->saldo_deudor;
                        $totalDeudor = 0;
                        $totalAcreedor = 0;
                        $deudorFinal = 0;
                        $acreedorFinal = 0;
                        if (count($indexEncotrados) > 1) {
                            try {
                                $firstValue = get_object_vars($datos[$indexEncotrados[0]]);
                                $secondValue = get_object_vars($datos[$indexEncotrados[1]]);
                                $array_merge = [$firstValue, $secondValue];
                                $searchPastYear = array_search(date("Y", strtotime("-1 year")), array_column($array_merge, "anio"));
                                $searchCurrentYear = array_search(date("Y"), array_column($array_merge, "anio"));
                                $totalDeudor =  $array_merge[$searchPastYear]["debe"];
                                $totalAcreedor = $array_merge[$searchPastYear]["haber"];


                                $sumarDebe =  (float)$array_merge[$searchPastYear]["debe"] + (float)$array_merge[$searchCurrentYear]["debe"];
                                $sumarHaber =  (float)$array_merge[$searchPastYear]["haber"] + (float)$array_merge[$searchCurrentYear]["haber"];



                                $calculo = (round($sumarDebe, 2) - round($sumarHaber, 2));


                                if ($calculo < 0) {
                                    $calculo  = $calculo *  (-1);
                                }

                                if ($sumarDebe > $sumarHaber) {
                                    $deudorFinal = $calculo;
                                    $acreedorFinal = 0;
                                } else {
                                    $acreedorFinal = $calculo;
                                    $deudorFinal = 0;
                                }

                                unset($datos[$indexEncotrados[1]]);
                            } catch (Exception $e) {
                                dd($indexEncotrados, "error", $e->getMessage());
                            }
                        } else {
                            $deudorFinal = $value->saldo_deudor;
                            $acreedorFinal = $value->saldo_acreedor;
                        }
                        $sumarTotalDeudor += $totalDeudor;
                        $sumarTotalAcredor += $totalAcreedor;


                     

                        array_push($arrayNew, ["plan" => $value->plan, "descripcion" => $value->nombre, "totalDeudor" => $totalDeudor, "totalAcreedor" => $totalAcreedor, "debe" => $value->debe, "haber" => $value->haber, "deudorFinal" => $deudorFinal, "acreedorFinal" => $acreedorFinal]);
                    }
                    $uniquePlans = [];
                    foreach ($arrayNew as $item) {
                        if (!in_array($item['plan'], array_column($uniquePlans, 'plan'))) {
                            $uniquePlans[] = $item;
                        }
                    }

                    foreach ($uniquePlans as $value) {
                        $sheet->cell('A' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["plan"]);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["descripcion"]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["totalDeudor"]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["totalAcreedor"]);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('E' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["debe"]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["haber"]);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        // verification


                        $sheet->cell('G' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["deudorFinal"]);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('H' . $comienzo, function ($cell) use ($value) {
                            $cell->setValue($value["acreedorFinal"]);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sumarTotalDeudorF += $value["deudorFinal"];
                        $sumarTotalAcredorF += $value["acreedorFinal"];

                        $comienzo++;
                    }






                    $sheet->cell('B' . $comienzo, function ($cell) {
                        $cell->setValue("Totales");
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $comienzo, function ($cell) use ($sumarTotalDeudor) {
                        $cell->setValue($sumarTotalDeudor);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $comienzo, function ($cell) use ($sumarTotalAcredor) {
                        $cell->setValue($sumarTotalAcredor);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('E' . $comienzo, function ($cell) use ($totalDebe) {
                        $cell->setValue($totalDebe);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $comienzo, function ($cell) use ($totalHaber) {
                        $cell->setValue($totalHaber);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $comienzo, function ($cell) use ($sumarTotalDeudorF) {
                        $cell->setValue($sumarTotalDeudorF);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $comienzo, function ($cell) use ($sumarTotalAcredorF) {
                        $cell->setValue($sumarTotalAcredorF);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                });
            })->export('xlsx');


            exit;
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
