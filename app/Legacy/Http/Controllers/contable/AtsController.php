<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Exception;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Ats;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_DetalleAts;
use Sis_medico\Ct_Detalle_Cliente_Retencion;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retencion_Fventas;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Rh_Tipo_Cuenta;
use Sis_medico\Ct_rubros;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Maestro_Documentos;
use Sis_medico\Empresa;
use Sis_medico\Factura_Cabecera;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Proveedor;
use Sis_medico\LogConfig;
use SimpleXMLElement;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\Ct_Autorizaciones_Pendientes;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Formulario107;
use Sis_medico\Ct_Transferencia_Bancaria;
use Sis_medico\Plan_Cuentas;
use Sis_medico\TipoProveedor;
use Sis_medico\Utilidades;

class AtsController extends Controller
{
    private $conError = 0;
    private $mns = '';
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 26)) == false) {
            return true;
        }
    }
    public function descargar_xml(Request $r)
    {
        $comprobante =  Ct_Formulario107::generarXML($r);
        echo $comprobante;
        exit();
    }
    public function docelectronicos(Request $r)
    {
        $data['controlador'] = 'tributacion';
        $data['empresa'] = Empresa::where('id', session('id_empresa'))->where('estado', '1')->first();
        $data['tipoComprobante'] = De_Maestro_Documentos::orderBy('codigo')->get();
        return view('tributacion/view_gestor', $data);
    }
    public function traeDocumento(Request $r)
    {
        $data['comprobante'] = EmisionDocumentosController::obtenercomprobanteSRI($r->clave);
        $docCod = substr($r->clave, 8, 2);
        $esta = substr($r->clave, 24, 3);
        $emis = substr($r->clave, 27, 3);
        $num = substr($r->clave, 30, 9);
        $ruc = '';
        $data['empresa'] = Empresa::where('id', session('id_empresa'))->first();
        $xml = $data['comprobante']->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante;
        $xml = simplexml_load_string($xml);
        $detalles = $xml->detalles->detalle;
        $proovedor = Proveedor::find($xml->infoTributaria->ruc[0]);
        if (is_null($proovedor)) {
            $razonSocial = $xml->infoTributaria->razonSocial;
            $ruc = $xml->infoTributaria->ruc[0];
            $tipos = TipoProveedor::all();
            $retenciones = DB::table('ct_porcentaje_retenciones')
                ->where('tipo', '1')
                ->get();
            $retencioner = DB::table('ct_porcentaje_retenciones')
                ->where('tipo', '2')
                ->get();
            $id_configuracion = DB::table('ct_configuracion_bancos as ctcb')
                ->select('ctcb.nombre', 'ctcb.id')
                ->get();
            $tipo_cuenta = Ct_Rh_Tipo_Cuenta::where('estado', '1')->get();
            $id_padre = Plan_Cuentas::where('id_padre', '2.01.03')->get();
            return view("tributacion/modal_proveedor", ['razonSocial' => $razonSocial, 'ruc' => $ruc, 'tipos' => $tipos, 'retenciones' => $retenciones, 'id_configuracion' => $id_configuracion, 'tipo_cuenta' => $tipo_cuenta, 'id_padre' => $id_padre, 'retencioner' => $retencioner]);
        }
        if ($docCod == '01') {
            $divisas = Ct_Divisas::where('estado', '1')->get();
            $id_empresa     = $r->session()->get('id_empresa');
            $proveedor      = Proveedor::where('estado', '1')->get();
            $bodega         = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $c_tributario   = Ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
            $t_comprobante  = Ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
            $tipo_pago      = Ct_Forma_Pago::where('estado', '1')->get();
            $tipo_tarjeta   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
            $lista_banco    = Ct_Bancos::where('estado', '1')->get();
            $cuentaiva = LogConfig::busqueda('4.1.01.02');
            $iva_param      = Ct_Configuraciones::where('id_plan', $cuentaiva)->first();
            $rubros         = Ct_rubros::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $termino        = Ct_Termino::where('estado', '1')->get();
            $empresa        = Empresa::where('id', $id_empresa)->first();
            $empresa_sucurs = Empresa::findorfail($id_empresa);
            $empresa_general = Empresa::all();
            $sucursales  = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
            $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
            $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('p.plan as id', 'p.nombre as nombre')->get();
            $cierreCabecera = Ct_Cierre_Cabecera::where('id_empresa', $id_empresa)->where('estado', '1')->orderBy('id', 'desc')->first();
            return view('tributacion/modal_compra', ['xml' => $xml, 'pro' => $proovedor, 'detalles' => $detalles, 'divisas' => $divisas, 'empresa' => $empresa, 'rubros' => $rubros, 'iva_param' => $iva_param, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'tipo_pago' => $tipo_pago, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'termino' => $termino, 'sucursales' => $sucursales, 'cuentas' => $cuentas]);
        } elseif ($docCod == '04') {
            return view('tributacion/view_nota_credito', $data)->render();
        } elseif ($docCod == '07') {
            $data['sucursales'] = Ct_Sucursales::where('estado', 1)
                ->where('id_empresa', $data['empresa']->id_empresa)
                ->get();
            $data['ventas'] = Ct_ventas::where('estado', '<>', 0)->where('tipo', '<>', 'VEN-FACT')->where('id_empresa', $data['empresa']->id_empresa)->where('valor_contable', '>', 0)->get();
            return view('tributacion/view_retencion', $data)->render();
        }
    }
    public function importarArchivoXls(Request $r)
    {
        ini_set('max_execution_time', '300');
        $datosImportar = [];
        try {
            $path = $r->file($r->tipo_archivo)->getRealPath();
            $datos = Excel::load($path)->get();
            if (!empty($datos)) {
                foreach ($datos as $hoja) {
                    $datoExcel = [
                        'codigo' => $hoja->codigo,
                        'descripcion' => $hoja->descripcion,
                        'cantidad' => $hoja->cantidad,
                        'p_unitario' => $hoja->costo,
                        'subtotal' => $hoja->subtotal,
                        'subtotal_25' => $hoja->subtotal_25,
                        'tarifa_0' => $hoja->tarifa_0,
                        'tarifa_12' => $hoja->tarifa_12,
                        'iva' => $hoja->iva,
                        'total' => $hoja->total,
                    ];
                    array_push($datosImportar, $datoExcel);
                }
            }
        } catch (Exception $e) {
            $datosImportar = [
                'msj' => 'Un producto no tiene codigo asigando en su archivo',
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
            ];
        }
        return json_encode($datosImportar);
    }
    public function guardarRegistros(Request $r)
    {
        $html = [];
        $data = $r->array;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['estado'] = $this->estadoDocumentoSRI(trim($data[$i]['clave_acceso']));
            array_push($html, $data[$i]);
        }
        return $html;
    }
    public function guardarArchivo(Request $r)
    {
        ini_set('max_execution_time', '300');
        $arrayDatos = [];
        $guardado = $_FILES[$r->tipo_archivo]['tmp_name'];
        $cont = 0;
        $contFila = 0;
        DB::table('log_importacion_sri')
            ->where('id_empresa', session('id_empresa'))
            ->delete();
        if ($da = fopen($guardado, "r")) {
            while ($aux = fgets($da, 1024)) {
                if ($cont > 0) {
                    $lineaArchivo = explode('	', $aux);
                    if (isset($lineaArchivo[4])) {
                        $rucReceptor = strlen($lineaArchivo[7]) == 10 ? $lineaArchivo[7] . '001' : $lineaArchivo[7];
                        if ($rucReceptor == session('id_empresa')) {
                            $contFila++;
                            $tipo = '';
                            $docCod = substr($lineaArchivo[4], 8, 2);
                            if ($docCod == '01') {
                                $tipo = 'Factura';
                            } elseif ($docCod == '03') {
                                $tipo = 'Liquidación de compra';
                            } elseif ($docCod == '04') {
                                $tipo = 'Nota de crédito';
                            } elseif ($docCod == '05') {
                                $tipo = 'Nota de débito';
                            } elseif ($docCod == '06') {
                                $tipo = 'Guia de remisión';
                            } elseif ($docCod == '07') {
                                $tipo = 'Comprobante de retención';
                            }
                            $ip_cliente = $_SERVER["REMOTE_ADDR"];
                            $arrayComprobante = [
                                'no' => $contFila,
                                //'estado' => $this->estadoDocumentoSRI(trim($lineaArchivo[10])),
                                'estado' => '',
                                'tipo' => $tipo,
                                'num_comprobante' => $lineaArchivo[3], //1
                                'ruc_emisior' => $lineaArchivo[0], //2
                                'razon_social' => utf8_encode($lineaArchivo[1]), //3
                                'fecha_emision' => $lineaArchivo[6], //4
                                'fecha_autorizacion' => $lineaArchivo[5], //5
                                'clave_acceso' => $lineaArchivo[4], //9
                                'num_autorizacion' => $lineaArchivo[4], //10
                                'total' => $lineaArchivo[10], //11
                                'id_empresa' => session('id_empresa'),
                                'id_usuariocrea' => Auth::user()->id,
                                'id_usuariomod' => Auth::user()->id,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente
                            ];
                            //DB::table('log_importacion_sri')->insert($arrayComprobante);
                            array_push($arrayDatos, $arrayComprobante);
                        }
                    }
                }
                $cont++;
            }
            fclose($da);
            return json_encode($arrayDatos);
        } else {
            echo "Error: no se ha podido leer el archivo <strong>" . $_FILES['docXML']['name'] . "</strong>";
        }
    }
    public function estadoDocumentoSRI($clave)
    {
        try {
            $resultado = '';
            if (strlen($clave) > 49) {
                $clave = substr($clave, 0, 49);
            }
            $docCod = substr($clave, 8, 2);
            $esta = substr($clave, 24, 3);
            $emis = substr($clave, 27, 3);
            $num = substr($clave, 30, 9);
            $ruc = substr($clave, 10, 13);
            $title = '';
            $color = '';
            $tipo = '';
            $datoDoc = '';
            if ($docCod == '01') {
                $tipo = 'Factura';
                $compra = Ct_compras::select('id', 'numero', 'proveedor', 'tipo')
                    ->where('autorizacion', $clave)
                    ->where('estado', '<>', 0);
                $dato = AfFacturaActivoCabecera::selectRaw('id,CONCAT(serie,"-",secuencia) as numero,proveedor,tipo')
                    ->where('nro_autorizacion', $clave)
                    ->where('estado', '<>', 0)
                    ->union($compra)
                    ->first();
                if ($dato != '') {
                    $title = '';
                    $color = '';
                    $datoDoc = explode('-', $dato->numero);
                    if ($esta . '-' . $emis . '-' . $num != $datoDoc[0] . '-' . $datoDoc[1] . '-' . str_pad((int)$datoDoc[2], 9, 0, STR_PAD_LEFT)) {
                        $title = 'El numero de comprobante tiene errores';
                        $color = 'red';
                    } elseif ($ruc != $dato->proveedor) {
                        $title .= 'El numero de proveedor tiene errores';
                        $color = 'red';
                    } else {
                        $title = '';
                        $color = 'green';
                    }
                    $url = '<a href="javascript:;">';
                    if ($dato->tipo == 1) {
                        $url = '<a href="' . url('/') . '/contable/compras/editar/' . $dato->id . '" style="color:' . $color . '" target="_blank">';
                    } elseif ($dato->tipo == 2) {
                        $url = '<a href="' . url('/') . '/contable/fact_contable/editar/' . $dato->id . '" style="color:' . $color . '" target="_blank">';
                    } elseif ($dato->tipo == 'ACT-FA') {
                        $url = '<a href="' . url('/') . '/af/nueva/edit_new_factura/' . $dato->id . '" style="color:' . $color . '" target="_blank">';
                    }
                    $resultado = '<span style="color:' . $color . ';" title="' . $title . '">' . $url . 'Ingresado</a></span>';
                } else
                    $resultado = '<a href="Javascript:;" style="color:white;background:red;">Sin ingresar</a>';
            } elseif ($docCod == '03') {
                $resultado = '';
            } elseif ($docCod == '04') {
                $resultado = '';
                $dato = Ct_Credito_Acreedores::where('autorizacion', $clave)
                    ->where('estado', '<>', 0)
                    ->first();
                if ($dato != '') {
                    $title = '';
                    $color = '';
                    $datoDoc = explode('-', $dato->serie . '-' . $dato->secuencia);
                    if ($esta . '-' . $emis . '-' . $num != $datoDoc[0] . '-' . $datoDoc[1] . '-' . str_pad((int)$datoDoc[2], 9, 0, STR_PAD_LEFT)) {
                        $title = 'El numero de comprobante tiene errores';
                        $color = 'red';
                    } elseif ($ruc != $dato->id_proveedor) {
                        $title = 'El numero de proveedor tiene errores';
                        $color = 'red';
                    } else {
                        $title = '';
                        $color = 'green';
                    }
                    $url = '<a href="' . url('/') . '/contable/acreedores/nota/credito/editar2/' . $dato->id . '" style="color:' . $color . '" target="_new">';
                    $resultado = '<span style="color:' . $color . ';" title="' . $title . '">' . $url . 'Ingresado</a></span>';
                } else
                    $resultado = '<a href="Javascript:;" style="color:white;background:red;">Sin ingresar</a>';
            } elseif ($docCod == '05') {
                $resultado = '';
            } elseif ($docCod == '06') {
                $resultado = '';
            } elseif ($docCod == '07') {
                $dato = Ct_Cliente_Retencion::where('autorizacion', $clave)
                    ->where('estado', '<>', 0)
                    ->first();
                if ($dato != '') {
                    $title = '';
                    $color = '';
                    $datoDoc = explode('-', $dato->nro_comprobante);
                    if ($esta . '-' . $emis . '-' . $num != $datoDoc[0] . '-' . $datoDoc[1] . '-' . str_pad((int)$datoDoc[2], 9, 0, STR_PAD_LEFT)) {
                        $title = 'El numero de comprobante tiene errores';
                        $color = 'red';
                    } elseif ($ruc != $dato->id_cliente) {
                        $title = 'La identificacion del Proveedor tiene errores';
                        $color = 'red';
                    } else {
                        $title = '';
                        $color = 'green';
                    }
                    $url = '<a href="' . url('/') . '/contable/clientes/retenciones/edit/' . $dato->id . '" style="color:' . $color . '" target="_new">';
                    $resultado = '<span style="color:' . $color . ';" title="' . $title . '">' . $url . 'Ingresado</a></span>';
                } else {
                    $dato = Ct_Transferencia_Bancaria::where('autorizacion', $clave)
                        ->where('estado', '<>', 0)
                        ->first();
                    if ($dato != '') {
                        $title = '';
                        $color = '';
                        $datoDoc = explode('-', $dato->nro_comprobante);
                        if ($ruc != $dato->id_proveedor) {
                            $title = 'La identificacion del Proveedor tiene errores';
                            $color = 'red';
                        } else {
                            $title = '';
                            $color = 'green';
                        }
                        $url = '<a href="' . url('/') . '/contable/clientes/retenciones/edit/' . $dato->id . '" style="color:' . $color . '" target="_new">';
                        $resultado = '<span style="color:' . $color . ';" title="' . $title . '">' . $url . 'Ingresado</a></span>';
                    } else {
                        $dato = Ct_Autorizaciones_Pendientes::where('autorizacion', $clave)->first();
                        if ($dato != '') {
                            $url = '<a href="javascript:;" style="color:' . $color . '" target="_new">';
                            $title = 'La retencion esta ingresada, pero no esta ligada a una transferencia bancaria';
                            $color = '#F97808';
                            $resultado = '<span style="color: #F97808;" title="' . $title . '">' . $url . '<label style:"color:' . $color . '">Ingresado</label></a></span>';
                        } else {
                            $resultado = '<a href="Javascript:;" style="color:white;background:red;">Sin ingresar</a>';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            /*$arrayRespuesta = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $e->getTraceAsString()
            ];
            echo '<pre>';
            print_r($arrayRespuesta);
            exit;*/
        }
        return $resultado;
    }
    public function TildesHtml($cadena)
    {
        return str_replace(array("á", "é", "í", "ó", "ú", "ñ", "Á", "É", "Í", "Ó", "Ú", "Ñ"), array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&ntilde;", "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&Ntilde;"), $cadena);
    }
    public function index(Request $request)
    {
        ini_set('max_execution_time', 300);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['periodo']) && !is_null($request['periodo'])) {
            $periodo = $request['periodo'];
        } else {
            $periodo = date('Y-m');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $registros  = [];
        $ventas     = $this->dataVentas($periodo, '', $id_empresa);
        $compras    = $this->getCompras($periodo, '', $id_empresa);
        $anulados   = $this->getAnulados($periodo, $id_empresa);
        $generados  = $this->getGenerados($periodo, $id_empresa);
        $retf       = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
        $reti       = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();
        $retfcompra = $this->getRetCompras($compras);
        $retfventa  = $this->getRetFVentasTotales($ventas);
        return view('contable/ats/index', [
            'registros'  => $registros,
            'empresa'    => $empresa,
            'periodo'     => $periodo,
            'ventas'     => $ventas,
            'compras'       => $compras,
            'retf'        => $retf,
            'reti' => $reti,
            'retfcompra' => $retfcompra,
            'retfventa' => $retfventa,
            'generados' => $generados,
            'generados'  => $generados,
            'anulados'   => $anulados,
        ]);
    }
    public function getVentas($periodo)
    {
        $facturas = [];
        $periodo  = str_replace('/', '-', $periodo);
        if ($periodo != null) {
            list($anio, $mes) = explode("-", $periodo);
            $id_empresa = session('id_empresa');
            $facturas = Factura_Cabecera::whereYear('fecha_emision', '=', $anio)
                ->whereMonth('fecha_emision', '=', $mes)
                ->where('tipo', 'VEN-FA')
                ->where('id_empresa', $id_empresa)
                ->get();
        }
        return $facturas;
    }
    public function dataVentas($periodo, $condicion = "", $id_empresa = "", $estado = 1)
    {
        $facturas = [];
        // if($condicion==""){     $condicion="[]";    }
        $periodo = str_replace('/', '-', $periodo);
        if ($periodo != null) {
            list($anio, $mes) = explode("-", $periodo);
            $facturas         = Ct_ventas::whereYear('fecha', '=', $anio)
                ->where('estado', $estado)
                ->where('id_empresa', $id_empresa)
                ->whereMonth('fecha', '=', $mes);
            // if ($condicion != "") { $facturas = $facturas->whereIn('id', $condicion);   }
            if ($condicion != "") {
                $facturas = $facturas->where('id', $condicion);
            }
            $facturas = $facturas->get();
        }
        // dd($facturas);
        return $facturas;
    }
    public function getCompras($periodo, $condicion = "", $id_empresa = "", $estado = 0)
    {
        $compras = [];
        // if($condicion==""){     $condicion="[]";    }

        $periodo = str_replace('/', '-', $periodo);
        if ($periodo != null) {
            list($anio, $mes) = explode("-", $periodo);
            $compras = Ct_compras::whereYear('fecha', '=', $anio)
                ->where('estado', '<>', $estado)
                ->where('tipo', '<>', '3')
                ->where('id_empresa', $id_empresa)
                ->whereMonth('fecha', '=', $mes);
            // if ($condicion != "") { $compras = $compras->whereIn('id', $condicion); }
            if ($condicion != "") {
                $compras = $compras->where('id', $condicion);
            }
            $compras = $compras->get();
        }
        // dd($facturas);
        return $compras;
    }
    public function getAnulados($periodo, $id_empresa)
    {
        $anulvent = $this->dataVentas($periodo, '', $id_empresa, 0);
        $anulcomp = $this->getCompras($periodo, '', $id_empresa, 0);
        $ventas   = array();
        $anulados = array();
        $i        = 1;
        //  VENTAS ANULADAS
        if (Auth::user()->id == '0922729587') {
            //dd($anulvent);
        }
        $estab = "";
        $emision = "";
        $secuencia = "";
        foreach ($anulvent as $value) {
            /* list($estab, $emision, $secuencia) = explode('-', $value->nro_comprobante); */
            $comprobante = explode('-', $value->nro_comprobante);

            if (isset($comprobante[0])) {
                $estab = $comprobante[0];
            }
            if (isset($comprobante[1])) {
                $emision = $comprobante[1];
            }
            if (isset($comprobante[2])) {
                $secuencia = $comprobante[2];
            }
            $ventas['id']                      = $value->id;
            $ventas['numero']                  = $value->nro_comprobante;
            $ventas['tipo']                    = $value->tipo;
            $ventas['nombre']                  = $value->cliente->nombre;
            $ventas['tipo_comp']               = "";
            $ventas['establecimiento']         = $estab;
            $ventas['emision']                 = $emision;
            $ventas['secuenciad']              = $secuencia;
            $ventas['secuenciah']              = $secuencia;
            $ventas['autorizacion']            = $value->nro_autorizacion;
            $ventas['fecha_autorizacion']      = date('d/m/Y', strtotime($value->updated_at));
            $i++;
            $anulados[] = $ventas;
        }
        //  COMPRAS ANULADASP:
        foreach ($anulcomp as $value) {
            if ($estab != '' && $emision != '' && $secuencia != '') { //Agrege validacion 100223 AOMS
                if (isset($value->numero) && $value->numero != '-') {
                    try {
                        list($estab, $emision, $secuencia) = explode('-', $value->numero);
                    } catch (Exception $e) {
                        /*$arrayError = [
                            'error' => $e->getMessage(),
                            'linea' => $e->getLine(),
                            'serie' => $value->serie,
                            'secuencia_factura' => $value->secuencia_factura,
                            'numero' => $value->numero,
                            'id'=>$value->id
                        ];
                        echo '<pre>';
                        print_r($arrayError);
                        exit;
                        */
                        if (strlen($value->serie) == 1) {
                            $esta = substr($value->serie, 0, 3);
                            $emis = substr($value->serie, 3, 3);
                            if ($emis == '')
                                $emis = 1;
                            $numero = str_pad((int)$esta, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$emis, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$value->secuencia_factura, 9, 0, STR_PAD_LEFT);
                            $arrayCompra = [
                                'serie' => str_pad((int)$esta, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$emis, 3, 0, STR_PAD_LEFT),
                                'numero' => str_pad((int)$esta, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$emis, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$value->secuencia_factura, 9, 0, STR_PAD_LEFT),
                            ];
                            Ct_compras::where('id', $value->id)->update($arrayCompra);
                        } elseif (strlen($value->serie) == 6) {
                            $esta = substr($value->serie, 0, 3);
                            $emis = substr($value->serie, 3, 3);
                            $numero = $esta . '-' . $emis . '-' . str_pad((int)$value->secuencia_factura, 9, 0, STR_PAD_LEFT);
                            $arrayCompra = [
                                'serie' => str_pad((int)$esta, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$emis, 3, 0, STR_PAD_LEFT),
                                'numero' => str_pad((int)$esta, 3, 0, STR_PAD_LEFT) . '-' . str_pad((int)$emis, 3, 0, STR_PAD_LEFT) . '-' .  str_pad((int)$value->secuencia_factura, 9, 0, STR_PAD_LEFT),
                            ];
                            Ct_compras::where('id', $value->id)->update($arrayCompra);
                        } else
                            $numero = $value->serie . '-' . str_pad((int)$value->secuencia_factura, 9, 0, STR_PAD_LEFT);
                        list($estab, $emision, $secuencia) = explode('-', $numero);
                    }
                    $ventas['id']                      = $value->id;
                    $compras['numero']                 = $value->numero;
                    $compras['tipo']                   = $value->tipo;
                    $compras['nombre']                 = $value->proveedorf->razonsocial;
                    $compras['tipo_comp']              = $value->tipo_comprobante;
                    $compras['establecimiento']        = $estab;
                    $compras['emision']                = $emision;
                    $compras['secuenciad']             = $secuencia;
                    $compras['secuenciah']             = $secuencia;
                    $compras['autorizacion']           = $value->autorizacion;
                    $compras['fecha_autorizacion']     = date('d/m/Y', strtotime($value->updated_at));
                    $i++;
                    $anulados[] = $compras;
                }
            }
        }
        return $anulados;
    }
    public function getAtsxTipo($ats_id, $tipo)
    {
        $compras = [];
        if ($ats_id != null) {
            $compras = Ct_ats::where('ct_ats.id', '=', $ats_id)
                ->join('ct_detalle_ats as de', 'ct_ats.id', 'de.ats_id')
                ->where('de.tipo_mov', $tipo)
                ->get();
        }
        return $compras;
    }
    public function getAtsxTipo_2($ats_id, $tipo)
    {
        $compras = [];
        if ($ats_id != null) {
            $compras = Ct_ats::where('ct_ats.id', '=', $ats_id)
                ->join('ct_detalle_ats as de', 'ct_ats.id', 'de.ats_id')
                ->join('ct_ventas as ven', 'de.mov_id', 'ven.id')
                ->where('de.tipo_mov', $tipo)
                ->groupBy('ven.id_cliente')
                ->select('de.*', 'ven.id_cliente as cedula_cliente')
                ->get();
        }
        // dd($facturas);
        return $compras;
    }
    public function getRetCompras($compras)
    {
        $retf = array();
        foreach ($compras as $value) {
            $detalles = [];
            $detalles = Ct_Retenciones::where('id_compra', $value->id)
                ->join('ct_detalle_retenciones as d', 'ct_retenciones.id', 'd.id_retenciones')
                ->where('ct_retenciones.estado', 1)
                ->select('d.*')
                ->get();
            // dd($detalles);
            if ($detalles != []) {
                foreach ($detalles as $detalle) {
                    // $retf[$value->id][$detalle->id_porcentaje]['porcentaje'] = $detalle->id_porcentaje;
                    $retf[$value->id][$detalle->id_porcentaje]['codigo'] = $detalle->codigo;
                    $retf[$value->id][$detalle->id_porcentaje]['base']   = $detalle->base_imponible;
                    $retf[$value->id][$detalle->id_porcentaje]['valor']  = $detalle->totales;
                }
            }
        }
        return $retf;
    }
    public function getTotalRetFVentas($ventas)
    {
        $reti          = array();
        $reti['IVA']   = 0;
        $reti['RENTA'] = 0;
        foreach ($ventas as $value) {
            $retenciones = $this->getRetencionesVentas($value->id);
            if ($retenciones != null) {
                $detalles = $retenciones->detalle_retencion;
                if ($detalles != []) {
                    foreach ($detalles as $row) {

                        if ($row->tipo == 'IVA') {
                            $reti['IVA'] += $row->totales;
                        }
                        if ($row->tipo == 'RENTA') {
                            $reti['RENTA'] += $row->totales;
                        }
                    }
                }
            }
        }

        return $reti;
    }
    public function getRetFVentas($ventas)
    {
        $reti = array();
        foreach ($ventas as $value) {
            $retenciones = $this->getRetencionesVentas($value->id);
            if ($retenciones != null) {
                $detalles = $retenciones->detalle_retencion;
                if ($detalles != []) {
                    // dd($detalles);
                    foreach ($detalles as $row) {
                        $reti[$value->id][$row->id_porcentaje]['codigo'] = $row->codigo;
                        $reti[$value->id][$row->id_porcentaje]['base']   = $row->codigo;
                        $reti[$value->id][$row->id_porcentaje]['valor']  = $row->codigo;
                    }
                }
            }
        }

        return $reti;
    }
    public function getRetFVentasTotales($ventas)
    {
        $reti = array();
        foreach ($ventas as $value) {
            $retenciones = $this->getRetencionesVentas($value->id);
            if ($retenciones != null) {
                $detalles = $retenciones->detalle_retencion;
                if ($detalles != []) {
                    // dd($detalles);
                    foreach ($detalles as $row) {
                        // $reti[$value->id][$row->id_porcentaje]['porcentaje'] = $row->id_porcentaje;
                        if ($row->tipo == "RENTA") {
                            $reti[$value->id]['RENTA']['valor'] = $row->valor;
                        }
                        if ($row->tipo == "IVA") {
                            $reti[$value->id]['IVA']['valor'] = $row->valor;
                        }
                    }
                }
            }
        }

        return $reti;
    }
    public function getRetencionesV($ventas)
    {
        $reti = array();
        foreach ($ventas as $value) {
            //$retenciones = $this->getRetencionesVentas($value->mov_id);
            $valor_iva                     = Ct_Cliente_Retencion::where('id_cliente', $value->id_cliente)->where('estado', 1)->sum('valor_iva');
            $valor_fuente                  = Ct_Cliente_Retencion::where('id_cliente', $value->id_cliente)->where('estado', 1)->sum('valor_fuente');
            $reti[$value->mov_id]['RENTA'] = $valor_fuente;
            $reti[$value->mov_id]['IVA']   = $valor_iva;
            /*  if ($retenciones != null) {

        $detalles = $retenciones->detalle_retencion;
        if ($detalles != []) {
        // dd($detalles);
        $total1=0;
        $total2=0;
        foreach ($detalles as $row) {
        // $reti[$value->id][$row->id_porcentaje]['porcentaje'] = $row->id_porcentaje;

        if ($row->tipo == "RENTA") {
        // $total1+=$row->totales;
        $reti[$row->id_cliente]['RENTA'] = $detalles->valor_fuente;

        }
        if ($row->tipo == "IVA") {
        $total2=$row->totales;

        }
        }
        $reti[$value->mov_id]['RENTA'] = $total1;
        $reti[$value->mov_id]['IVA'] = $total2;
        }
        } */
        }

        return $reti;
    }
    public function getNCProveedores($compras, $mes, $anio, $id_empresa)
    {
        $monto = 0;
        $monto = Ct_Credito_Acreedores::where('id', $compras)
            ->where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->sum('subtotal');

        return $monto;
    }
    public function getGenerados($periodo, $id_empresa)
    {
        $datos = [];
        if ($periodo != null) {
            $datos = Ct_ats::where('periodo', $periodo)
                ->where('estado', 1)
                ->where('id_empresa', $id_empresa)
                ->get();
        }
        // dd($facturas);
        return $datos;
    }
    public function store(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        if (!is_null($request['filperiodo'])) {
            $periodo = $request['filperiodo'];
        } else {
            $periodo = date('Y-m');
        }
        $request['periodo'] = $request['filperiodo'];
        $this->guardarAts($request);
        return redirect()->route('ats.index', ['periodo' => $request['periodo']]);
    }
    public function _guardarAts($data)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id_empresa = $data->session()->get('id_empresa');

        //  TODO: GUARDAR UN TMP DE COMPAS
        $id_vts = "";
        if (isset($data['id_factura'])) {
            //  TODO: CABECERA ATS
            $cabecera = [
                'periodo'         => $data['filperiodo'],
                'id_empresa'      => $id_empresa,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $ats_id = Ct_Ats::insertGetId($cabecera);

            $idventas = $data['id_factura'];
            // dd($data);
            foreach ($idventas as $value) {
                $id_vts .= "$value,";
            }
            $id_vts = explode(",", $id_vts);
            $ventas = $this->dataVentas($data['filperiodo'], $id_vts, $id_empresa);
            // dd($ventas);
            $detalles = "[]";
            foreach ($ventas as $value) {
                Ct_DetalleAts::create([
                    'tipo_mov'               => 1,
                    'mov_id'                 => $value->id,
                    'ats_id'                 => $ats_id,
                    'identificacion'         => $value->id_cliente,
                    'nombre'                 => $value->cliente->nombre,
                    'emision'                => $value->fecha,
                    'tipo_comprobante'       => $value->tipo,
                    'num_comprobante'        => $value->nro_comprobante,
                    'comp_vent_autorizacion' => $value->nro_autorizacion,
                    'comp_diario'            => $value->id_asiento,
                    'id_empresa'             => $id_empresa,
                    'id_usuariomod'          => $idusuario,
                    'id_usuariocrea'         => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                ]);
            }
        }
        //  TODO: GUARDAR UN TMP DE VENTAS
        $id_comp = "";
        if (isset($data['id_compra'])) {
            $idcomps = $data['id_compra'];
            // dd($idventas);
            foreach ($idcomps as $value) {
                $id_comp .= "$value,";
            }
            $id_comp = explode(",", $id_comp);
            $compras = $this->getCompras($data['filperiodo'], $id_comp, $id_empresa);
            // dd($compras);
            foreach ($compras as $value) {
                //$retenciones = $this->getRetencionesCompras($value->id);
                Ct_DetalleAts::create([
                    'tipo_mov'               => 2,
                    'mov_id'                 => $value->id,
                    'ats_id'                 => $ats_id,
                    'identificacion'         => $value->proveedor,
                    'nombre'                 => $value->proveedor,
                    'emision'                => $value->fecha,
                    'tipo_comprobante'       => $value->tipo_comprobante,
                    'num_comprobante'        => $value->numero,
                    'comp_vent_autorizacion' => $value->autorizacion,
                    'comp_diario'            => $value->id_asiento_cabecera,
                    // 'comp_diario'               => $data->id_asiento,
                    'id_empresa'             => $id_empresa,
                    'id_usuariomod'          => $idusuario,
                    'id_usuariocrea'         => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                ]);
            }
        }

        //dd($compras);
    }
    public function guardarAts($data)
    {
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $id_empresa = $data->session()->get('id_empresa');
            //  TODO: GUARDAR UN TMP DE COMPAS
            $id_vts = "";
            //dd($data);
            if (isset($data['id_factura']) or isset($data['id_compra'])) {
                //  TODO: CABECERA ATS
                $cabecera = [
                    'periodo'         => $data['filperiodo'],
                    'id_empresa'      => $id_empresa,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $ats_id = Ct_Ats::insertGetId($cabecera);
            }
            if (isset($data['id_factura'])) {
                $idventas = $data['id_factura'];
                //dd($data);
                $detalles = "[]";
                foreach ($idventas as $value) {
                    // $id_vts .= "$value,";
                    $ventas = $this->dataVentas($data['filperiodo'], $value, $id_empresa);
                    Ct_DetalleAts::create([
                        'tipo_mov'               => 1,
                        'mov_id'                 => $ventas[0]->id,
                        'ats_id'                 => $ats_id,
                        'identificacion'         => $ventas[0]->id_cliente,
                        'nombre'                 => $ventas[0]->cliente->nombre,
                        'emision'                => $ventas[0]->fecha,
                        'tipo_comprobante'       => $ventas[0]->tipo,
                        'num_comprobante'        => $ventas[0]->nro_comprobante,
                        'comp_vent_autorizacion' => $ventas[0]->nro_autorizacion,
                        'comp_diario'            => $ventas[0]->id_asiento,
                        'id_empresa'             => $id_empresa,
                        'id_usuariomod'          => $idusuario,
                        'id_usuariocrea'         => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                    ]);
                }
            }

            //  TODO: GUARDAR UN TMP DE VENTAS
            $id_comp = "";
            if (isset($data['id_compra'])) {
                //dd($data);
                $idcomps = $data['id_compra'];
                $tipo_comprobante = $data['tipo_comprobante'];
                $autorizacion = $data['autorizacion'];
                $x = 0;
                //dd($tipo_comprobante);
                foreach ($idcomps as $value) {
                    // $id_comp .= "$value,";
                    //dd($value);
                    if (count($tipo_comprobante) > 0) {
                        $compras = $this->getCompras($data['filperiodo'], $value, $id_empresa);
                        if (($compras) != [] || $compras != null) {
                            Ct_DetalleAts::create([
                                'tipo_mov' => 2,
                                'mov_id' => $compras[0]->id,
                                'ats_id' => $ats_id,
                                'identificacion' => $compras[0]->proveedor,
                                'nombre' => $compras[0]->proveedor,
                                'emision' => $compras[0]->fecha,
                                // 'tipo_comprobante' => $compras[0]->tipo_comprobante,
                                'tipo_comprobante' => $tipo_comprobante[$value],
                                'num_comprobante' => $compras[0]->numero,
                                // 'comp_vent_autorizacion' => $compras[0]->autorizacion,
                                'comp_vent_autorizacion' => $autorizacion[$value],
                                'comp_diario' => $compras[0]->id_asiento_cabecera,
                                // 'comp_diario' => $data->id_asiento,
                                'id_empresa' => $id_empresa,
                                'id_usuariomod' => $idusuario,
                                'id_usuariocrea' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }
                    }

                    $x++;
                }
            }
            //  TODO: GUARDAR UN TMP DE ANULADOS
            if (isset($data['id_anulado'])) {
                $idanuls = $data['id_anulado'];
                $x       = 0;
                foreach ($idanuls as $value) {
                    Ct_DetalleAts::create([
                        'tipo_mov'               => 3,
                        'mov_id'                 => $value,
                        'ats_id'                 => $ats_id,
                        'nombre'                 => $data['anu_nombre'][$x],
                        'tipo_comprobante'       => $data['anu_tipo_comp'][$x],
                        'num_comprobante'        => $data['anu_numero'][$x],
                        'comp_vent_autorizacion' => $data['anu_autorizacion'][$x],
                        'id_empresa'             => $id_empresa,
                        'id_usuariomod'          => $idusuario,
                        'id_usuariocrea'         => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                    ]);
                    $x++;
                }
            }
            //DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return ['Error: ' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()];
        }
    }
    public function getRetencionesCompras($id)
    {
        $retenciones = Ct_Retenciones::where('id_compra', $id)->where('estado', 1)->first();
        return $retenciones;
    }
    public function getRetencionesVentas($id)
    {
        $retenciones = Ct_Cliente_Retencion::where('id_factura', $id)->where('estado', 1)->first();
        return $retenciones;
    }
    public function generar($empresa, $periodo)
    {
        $registros = [];
        $ventas = $this->getVentas($periodo);
        $compras = $this->getCompras($periodo);
        // dd($periodo);
        return view('contable/ats/index', [
            'registros' => $registros,
            'empresa' => $empresa,
            'periodo' => $periodo,
            'ventas'    => $ventas,
            'compras'    => $compras,
        ]);
    }
    public function show(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')
            ->first();
        if (!is_null($request['filperiodo'])) {
            $periodo = $request['filperiodo'];
        } else if (!is_null($request['filtroperiodo'])) {
            $periodo = $request['filtroperiodo'];
        } elseif (!is_null($request['periodo'])) {
            $periodo = $request['periodo'];
        } else {
            $periodo = date('Y-m');
        }
        if (!is_null($request['id_ats'])) {
            $ats_id = $request['id_ats'];
            if (isset($request['xls']) and ($request['xls'] == 1)) {
                $this->exportXls($empresa, $ats_id, $periodo);
            } elseif (isset($request['xml']) and ($request['xml'] == 1)) {
                $this->exportXml($empresa, $ats_id, $periodo);
            } else {
                $this->generar($empresa, $periodo);
            }
        }
    }
    public function numberToColumnName($number)
    {
        $abc     = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $abc_len = strlen($abc);

        $result_len = 1; // how much characters the column's name will have
        $pow        = 0;
        while (($pow += pow($abc_len, $result_len)) < $number) {
            $result_len++;
        }

        $result = "";
        $next   = false;
        // add each character to the result...
        for ($i = 1; $i <= $result_len; $i++) {
            $index = ($number % $abc_len) - 1; // calculate the module

            // sometimes the index should be decreased by 1
            if ($next || $next = false) {
                $index--;
            }

            // this is the point that will be calculated in the next iteration
            $number = floor($number / strlen($abc));

            // if the index is negative, convert it to positive
            if ($next = ($index < 0)) {
                $index = $abc_len + $index;
            }

            $result = $abc[$index] . $result; // concatenate the letter
        }
        return $result;
    }
    public function exportXls($empresa, $ats_id, $periodo)
    {
        Excel::create('SustentoAts-' . $periodo, function ($excel) use ($ats_id, $periodo, $empresa) {
            $excel->sheet('SustentoAts', function ($sheet) use ($ats_id, $periodo, $empresa) {
                $sheet->mergeCells('A2:AB2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue(strtoupper($empresa->nombrecomercial));
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('11');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A3:AB3');
                $sheet->cell('A3', function ($cell) use ($periodo) {
                    // manipulate the cel
                    $cell->setValue("SUSTENTO ATS");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('8');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A4:AB4');
                $sheet->cell('A4', function ($cell) use ($periodo) {
                    // manipulate the cel
                    $cell->setValue("$periodo");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('8');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A5:AB5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                });
                $sheet->mergeCells('A6:A7');
                $sheet->cell('A6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IDENTIF.');
                });
                $sheet->mergeCells('B6:B7');
                $sheet->cell('B6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                });
                $sheet->mergeCells('C6:C7');
                $sheet->cell('C6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EMISION');
                });

                $sheet->mergeCells('D6:E7');
                $sheet->cell('D6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMP. VENTA');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('F6:F7');
                $sheet->cell('F6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AUTORIZACIÓN');
                });
                $sheet->mergeCells('G6:H7');
                $sheet->cell('G6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMP. DIARIO');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('I6:J7');
                $sheet->cell('I6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMP. RETENCION');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('K6:K7');
                $sheet->cell('K6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AUTORIZACIÓN');
                });
                $sheet->mergeCells('L6:L7');
                $sheet->cell('L6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUSTENTO');
                });
                $sheet->mergeCells('M6:M7');
                $sheet->cell('M6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                });
                $sheet->mergeCells('N6:N7');
                $sheet->cell('N6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APLICA S/N');
                });
                $sheet->mergeCells('O6:O7');
                $sheet->cell('O6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCEPTO');
                });
                $sheet->mergeCells('P6:P7');
                $sheet->cell('P6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                });
                $sheet->mergeCells('Q6:Q7');
                $sheet->cell('Q6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 0');
                });
                $sheet->mergeCells('R6:R7');
                $sheet->cell('R6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 12');
                });
                $sheet->mergeCells('S6:S7');
                $sheet->cell('S6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MONTO IVA');
                });
                $numcolum = 20;
                $retf     = [];
                $reti     = [];
                $retf     = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
                $reti     = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();

                foreach ($retf as $ret) {
                    $namecolumn  = $this->numberToColumnName($numcolum);
                    $namecolumn2 = $this->numberToColumnName($numcolum + 2);
                    // dd($namecolumn); letra T
                    $sheet->mergeCells($namecolumn . '6:' . $namecolumn2 . '6');
                    $sheet->cell($namecolumn . '6', function ($cell) use ($ret) {
                        $cell->setValue(strtoupper(str_replace('Retenciones Realizadas del Impuesto a la ', '', $ret->nombre)));
                        $cell->setAlignment('center');
                    });
                    $sheet->cell($namecolumn . '7', function ($cell) {
                        $cell->setValue('CODIGO');
                    });
                    $numcolum++;
                    $namecolumn = $this->numberToColumnName($numcolum);
                    $sheet->cell($namecolumn . '7', function ($cell) {
                        $cell->setValue('BASE IMP');
                    });
                    $numcolum++;
                    $namecolumn = $this->numberToColumnName($numcolum);
                    $sheet->cell($namecolumn . '7', function ($cell) {
                        $cell->setValue('VALOR');
                    });

                    $numcolum++;
                }

                foreach ($reti as $ret) {
                    $namecolumn = $this->numberToColumnName($numcolum);
                    // dd($namecolumn . '6:' . $namecolumn . '7');
                    $sheet->mergeCells($namecolumn . '6:' . $namecolumn . '7');
                    $sheet->cell($namecolumn . '6', function ($cell) use ($ret) {
                        // manipulate the cel
                        $cell->setValue(strtoupper(str_replace('es Realizadas del', '', $ret->nombre)));
                        $cell->setAlignment('center');
                    });

                    $numcolum++;
                }

                $sheet->setColumnFormat(array(
                    'P'  => '0.00',
                    'Q'  => '0.00',
                    'R'  => '0.00',
                    'S'  => '0.00',
                    'T'  => '0.00',
                    'U'  => '0.00',
                    'W'  => '0.00',
                    'X'  => '0.00',
                    'Y'  => '0.00',
                    'Z'  => '0.00',
                    'AA' => '0.00',
                    'AB' => '0.00',
                    'AC' => '0.00',
                    'AD' => '0.00',
                    'AE' => '0.00',
                    'AF' => '0.00',
                    'AG' => '0.00',
                    'AH' => '0.00',
                    'AI' => '0.00',
                    'AJ' => '0.00',
                ));

                // DETALLES COMPRAS
                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPRAS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('8');
                });

                $i = 11;

                $i = $this->setDetalleCompras($sheet, $ats_id, 2, $i);
                $i++;

                // DETALLES VENTAS
                $sheet->mergeCells("A$i:B$i");
                $sheet->cell("A$i", function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENTAS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('8');
                });
                $i++;
                $i = $this->setDetalleVentas($sheet, $ats_id, 1, $i);

                //  CONFIGURACION FINAL
                $sheet->cells('A6:' . $namecolumn . '6', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#1B6DD7');
                    $cells->setFontSize('8');
                    $cells->setFontWeight('bold');
                    // $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    // $cells->setValignment('center');
                });
                $sheet->cells('A7:' . $namecolumn . '7', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#1B6DD7');
                    $cells->setFontSize('8');
                    $cells->setFontWeight('bold');
                    // $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    // $cells->setValignment('center');
                });

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 40,
                    'C' => 12,
                    'D' => 6,
                    'E' => 12,
                    'F' => 12,
                    'G' => 6,
                    'H' => 12,
                    'I' => 6,
                    'J' => 12,
                    'K' => 12,
                    'L' => 12,
                    'M' => 12,
                    'N' => 12,
                    'O' => 12,
                    'P' => 12,
                    'Q' => 12,
                    'R' => 12,
                    'S' => 12,
                    'T' => 12,
                    'U' => 12,
                    'V' => 12,
                    'W' => 12,
                ));

                $sheet->setHeight(array(
                    9  => 12,
                    11 => 12,
                ));
            });
        })->export('xlsx');
    }
    public function setDetalleCompras($sheet, $ats_id, $tipo, $i)
    {
        $data = $this->getAtsxTipo($ats_id, $tipo);
        // dd($data);
        foreach ($data as $value) {
            $sheet->cell('A' . $i, function ($cell) use ($value) {
                $cell->setValue(" " . $value['identificacion'] . " ");
            });
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                $cell->setValue($value['nombre']);
            });
            $sheet->cell('C' . $i, function ($cell) use ($value) {
                $cell->setValue(date('d/m/Y', strtotime($value['emision'])));
            });
            // $sheet->mergeCells('D'.$i.':E'.$i);
            $sheet->cell('D' . $i, function ($cell) use ($value) {
                $cell->setValue($value['tipo_comprobante']); //CONFIRMAR
            });
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                $cell->setValue($value['num_comprobante']);
            });
            $sheet->mergeCells('F' . $i . ':G' . $i);
            $sheet->cell('F' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_vent_autorizacion']);
            });
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_diario']);
            });
            $sheet->mergeCells('I' . $i . ':J' . $i);
            $sheet->cell('I' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_retencion']);
            });
            // $sheet->mergeCells('I'.$i.':J'.$i);
            $sheet->cell('K' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_rete_autorizacion']);
            });
            $sheet->cell('L' . $i, function ($cell) use ($value) {
                $cell->setValue($value['sustento']);
            });
            $sheet->cell('M' . $i, function ($cell) use ($value) {
                $cell->setValue($value['tipo']);
            });
            $sheet->cell('N' . $i, function ($cell) use ($value) {
                $cell->setValue($value['aplica_sn']);
            });
            $sheet->cell('O' . $i, function ($cell) use ($value) {
                $cell->setValue($value['concepto']);
            });
            $sheet->cell('P' . $i, function ($cell) use ($value) {
                $cell->setValue($value['subtotal']);
            });
            $sheet->cell('Q' . $i, function ($cell) use ($value) {
                $cell->setValue($value['base_0']);
            });
            $sheet->cell('R' . $i, function ($cell) use ($value) {
                $cell->setValue($value['base_12']);
            });
            $sheet->cell('S' . $i, function ($cell) use ($value) {
                $cell->setValue($value['monto_iva']);
            });
            // $sheet->cell('S'.$i, function($cell) use($value) {
            //     // manipulate the cel
            //     $cell->setValue($value['subtotal']);
            // });
            // dd($value['mov_id']);
            $compra  = Ct_compras::where('id', $value['mov_id'])->get();
            $retcomp = $this->getRetCompras($compra);
            //dd($retcomp);
            $numcolum = 20;
            $retf     = "[]";
            $retf     = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
            $reti     = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();

            foreach ($retf as $ret) {
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retcomp) {
                    $cell->setValue((isset($retcomp[$value->mov_id][$ret->id])) ? $retcomp[$value->mov_id][$ret->id]['codigo'] : "");
                });
                $numcolum++;
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retcomp) {
                    $cell->setValue((isset($retcomp[$value->mov_id][$ret->id])) ? $retcomp[$value->mov_id][$ret->id]['base'] : "");
                });
                $numcolum++;
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retcomp) {
                    $cell->setValue((isset($retcomp[$value->mov_id][$ret->id])) ? $retcomp[$value->mov_id][$ret->id]['valor'] : "");
                });
                $numcolum++;
            }

            foreach ($reti as $ret) {
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retcomp) {
                    $cell->setValue((isset($retcomp[$value->mov_id][$ret->id])) ? $retcomp[$value->mov_id][$ret->id]['codigo'] : "");
                });
                $numcolum++;
            }

            $i++;
        }
        return $i;
    }
    public function setDetalleVentas($sheet, $ats_id, $tipo, $i)
    {
        $data = $this->getAtsxTipo($ats_id, $tipo);
        foreach ($data as $value) {

            $ventas = Ct_compras::where('id', $value->mov_id)->get();
            if ($ventas != "[]") {
                $retenciones = $this->getRetVentas($ventas);
            } else {
                $retenciones = "[]";
            }

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                $cell->setValue(" " . $value['identificacion'] . " ");
            });
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                $cell->setValue($value['nombre']);
            });
            $sheet->cell('C' . $i, function ($cell) use ($value) {
                $cell->setValue(date('d/m/Y', strtotime($value['emision'])));
            });
            // $sheet->mergeCells('D'.$i.':E'.$i);
            $sheet->cell('D' . $i, function ($cell) use ($value) {
                $cell->setValue("COM"); //CONFIRMAR
            });
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                $cell->setValue($value['num_comprobante']);
            });
            $sheet->mergeCells('F' . $i . ':G' . $i);
            $sheet->cell('F' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_vent_autorizacion']);
            });
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_diario']);
            });
            $sheet->mergeCells('I' . $i . ':J' . $i);
            $sheet->cell('I' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_retencion']);
            });
            // $sheet->mergeCells('I'.$i.':J'.$i);
            $sheet->cell('K' . $i, function ($cell) use ($value) {
                $cell->setValue($value['comp_rete_autorizacion']);
            });
            $sheet->cell('L' . $i, function ($cell) use ($value) {
                $cell->setValue($value['sustento']);
            });
            $sheet->cell('M' . $i, function ($cell) use ($value) {
                $cell->setValue($value['tipo']);
            });
            $sheet->cell('N' . $i, function ($cell) use ($value) {
                $cell->setValue($value['aplica_sn']);
            });
            $sheet->cell('O' . $i, function ($cell) use ($value) {
                $cell->setValue($value['concepto']);
            });
            $sheet->cell('P' . $i, function ($cell) use ($value) {
                $cell->setValue($value['subtotal']);
            });
            $sheet->cell('Q' . $i, function ($cell) use ($value) {
                $cell->setValue($value['base_0']);
            });
            $sheet->cell('R' . $i, function ($cell) use ($value) {
                $cell->setValue($value['base_12']);
            });
            $sheet->cell('S' . $i, function ($cell) use ($value) {
                $cell->setValue($value['monto_iva']);
            });

            $venta   = Ct_ventas::where('id', $value['mov_id'])->get();
            $retvent = [];
            $retvent = $this->getRetCompras($venta);
            // dd($retcomp);
            $numcolum = 20;
            $retf     = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
            $reti     = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();

            foreach ($retf as $ret) {
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retvent) {
                    $cell->setValue((isset($retvent[$value->mov_id][$ret->id])) ? $retvent[$value->mov_id][$ret->id]['codigo'] : "");
                });
                $numcolum++;
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retvent) {
                    $cell->setValue((isset($retvent[$value->mov_id][$ret->id])) ? $retvent[$value->mov_id][$ret->id]['base'] : "");
                });
                $numcolum++;
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retvent) {
                    $cell->setValue((isset($retvent[$value->mov_id][$ret->id])) ? $retvent[$value->mov_id][$ret->id]['valor'] : "");
                });
                $numcolum++;
            }

            foreach ($reti as $ret) {
                $namecolumn = $this->numberToColumnName($numcolum);
                $sheet->cell($namecolumn . "$i", function ($cell) use ($ret, $value, $retvent) {
                    $cell->setValue((isset($retvent[$value->mov_id][$ret->id])) ? $retvent[$value->mov_id][$ret->id]['codigo'] : "");
                });
                $numcolum++;
            }

            $i++;
        }
        return $i;
    }
    public function exportXml($empresa, $ats_id, $periodo)
    {

        $tipoID = "";
        if (strlen($empresa->id) == 13) {
            $tipoID = "R"; // para ruc
        } elseif (strlen($empresa->id) == 10) {
            $tipoID = "C"; // para cedula
        } elseif ($empresa->id == "9999999999") {
            $tipoID = "F"; // para consumidor final
        } else {
            $tipoID = "P";
        }
        $datos_ats = Ct_ats::find($ats_id);
        $separador = substr($datos_ats->periodo, 4, -2);
        list($anio, $mes) = explode($separador, $datos_ats->periodo);
        $cab = "<?xml version='1.0' encoding='ISO-8859-1'?>
        <iva>
        <TipoIDInformante>$tipoID</TipoIDInformante>
        <IdInformante>" . $empresa->id . "</IdInformante>
        <razonSocial>" . str_replace('.', '', str_replace('&', 'Y', $empresa->nombrecomercial)) . "</razonSocial>
        <Anio>" . $anio . "</Anio>
        <Mes>" . $mes . "</Mes>
        <numEstabRuc>001</numEstabRuc>
        <totalVentas>[totalVentas]</totalVentas>
        <codigoOperativo>IVA</codigoOperativo>";
        $data = $this->getAtsxTipo($ats_id, 2);
        $xmlcompras = "<compras>";
        $idsCompra = [];
        $count = 0;
        foreach ($data as $key => $value) {
            if ($value['num_comprobante'] != 'IMPORTACION') {
                list($establecimiento, $puntoemision, $secuencial) = explode('-', $value['num_comprobante']);
                $findme = '0';
                while (strpos($secuencial, $findme) === 0) {
                    $secuencial = substr($secuencial, 1);
                }

                if (Auth::user()->id == '0922729587') {
                    if ($key == 9) {
                        dd($secuencial);
                    }
                }
                $a = "";
                $compra = Ct_compras::find($value->mov_id);
                if (!is_null($compra)) {
                    $a = $this->loadNC($compra->id, $value, $empresa->id, $periodo);
                }
                $descuento0 = 0;
                $descuento12 = 0;
                if (!is_null($compra)) {
                    $detalleCompra = Ct_detalle_compra::where('id_ct_compras', $compra->id)->get();
                    if (count($detalleCompra) > 0) {
                        foreach ($detalleCompra as $row) {
                            if ($row->iva == 1) {
                                $descuento12 += $row->descuento;
                            } else {
                                $descuento0 += $row->descuento;
                            }
                        }
                    }
                }
                $xmlcompras .= $a;
                $cont = 0;
                if (Auth::user()->id == '0922729587') {
                    if ($secuencial == "6371") {
                        dd("holaaaa caranbola");
                    }
                }
                if ($compra != '' && (!is_null($compra->autorizacion) && ($compra->credito_tributario != "00"))) {
                    $ice_total = '0.00';
                    if (!is_null($compra->ice_total)) {
                        $ice_total = $compra->ice_total;
                    }
                    $iva_total = '0.00';
                    if (!is_null($compra->iva_total)) {
                        $iva_total = $compra->iva_total;
                    }
                    if ($compra->subtotal_0 == null) {
                        $compra->subtotal_0 = "0.00";
                    }
                    if ($compra->subtotal_12 == null) {
                        $compra->subtotal_12 = "0.00";
                    }
                    $xmlcompras .= "<detalleCompras>";
                    if ($compra->tipo_comprobante == '03') {
                        $xmlcompras .= "<codSustento>02</codSustento>";
                    } else {
                        $xmlcompras .= "<codSustento>" . str_pad($compra->credito_tributario, 2, "0", STR_PAD_LEFT) . "</codSustento>";
                    }
                    if ($compra->tipo_comprobante == '03') {
                        $xmlcompras .= "<tpIdProv>02</tpIdProv>";
                    } else {
                        $xmlcompras .= "<tpIdProv>01</tpIdProv>";
                    }
                    $xmlcompras .= "<idProv>$value->identificacion</idProv>
                    <tipoComprobante>" . str_pad($compra->tipo_comprobante, 2, "0", STR_PAD_LEFT) . "</tipoComprobante>";
                    $baseImponible = $compra->subtotal_0 - $descuento0;
                    $baseImponible = number_format($baseImponible, 2, ',', '');
                    $baseImponible = str_replace(',', '.', $baseImponible);
                    $baseGrava = $compra->subtotal_12 - $descuento12;
                    $baseGrava = number_format($baseGrava, 2, ',', '');
                    $baseGrava = str_replace(',', '.', $baseGrava);
                    $xmlcompras .= "<parteRel>NO</parteRel>
                    <fechaRegistro>" . date('d/m/Y', strtotime($value->emision)) . "</fechaRegistro>
                    <establecimiento>" . str_pad($establecimiento, 2, "0", STR_PAD_LEFT) . "</establecimiento>
                    <puntoEmision>" . str_pad($puntoemision, 2, "0", STR_PAD_LEFT) . "</puntoEmision>
                    <secuencial>" . $secuencial . "</secuencial>
                    <fechaEmision>" . date('d/m/Y', strtotime($value->emision)) . "</fechaEmision>
                    <autorizacion>$value->comp_vent_autorizacion</autorizacion>
                    <baseNoGraIva>0.00</baseNoGraIva>
                    <baseImponible>$baseImponible</baseImponible>
                    <baseImpGrav>$baseGrava</baseImpGrav>
                    <baseImpExe>0.00</baseImpExe>
                    <montoIce>" . number_format($ice_total, 2, '.', '') . "</montoIce>
                    <montoIva>" . number_format($iva_total, 2, '.', '') . "</montoIva>
                    <valRetBien10>[valRetBien10]</valRetBien10>
                    <valRetServ20>[valRetServ20]</valRetServ20>
                    <valorRetBienes>[valorRetBienes]</valorRetBienes>
                    <valRetServ50>0.00</valRetServ50>
                    <valorRetServicios>[valorRetServicios]</valorRetServicios>
                    <valRetServ100>[valRetServ100]</valRetServ100>
                    <totbasesImpReemb>[totbasesImpReemb]</totbasesImpReemb>
                    <pagoExterior>
                        <pagoLocExt>01</pagoLocExt>
                        <paisEfecPago>NA</paisEfecPago>
                        <aplicConvDobTrib>NA</aplicConvDobTrib>
                        <pagExtSujRetNorLeg>NA</pagExtSujRetNorLeg>
                    </pagoExterior>";
                    $montoImpuestos = $baseGrava + $baseImponible + $ice_total + $iva_total;
                    if ($montoImpuestos > 500) {
                        $xmlcompras .= "<formasDePago>
                            <formaPago>20</formaPago>
                        </formasDePago>";
                    }

                    $compra = Ct_compras::where('id', $value->mov_id)->get();
                    //dd($compra);
                    $retcomp = $this->getRetCompras($compra);
                    // dd($retcomp);
                    $retf = Ct_Porcentaje_Retenciones::where('tipo', 2)->get();
                    $reti = Ct_Porcentaje_Retenciones::where('tipo', 1)->get();
                    $nbsp = "";
                    $xmlcompras .= "<air>";
                    foreach ($retf as $ret) {
                        /*if (Auth::user()->id == '0921605895') {
                            if (isset($retcomp[$value->mov_id][$ret->id])) {
                                echo '<pre>';
                                print_r($retcomp[$value->mov_id][$ret->id]['codigo']);
                                exit;
                            }
                        }*/
                        if (isset($retcomp[$value->mov_id][$ret->id])) {
                            $codretair  = $retcomp[$value->mov_id][$ret->id]['codigo'] == 3030 ? '303A' : $retcomp[$value->mov_id][$ret->id]['codigo'];
                            $baseimpair = $retcomp[$value->mov_id][$ret->id]['base'];
                            $valretair  = $retcomp[$value->mov_id][$ret->id]['valor'];
                            $porcentajeAir = 0;
                            if ($anio >= 2026 && $mes >= 3) {
                                $porcentajeAir = $ret->valor;
                            } else {
                                //$codigo, $anio, $mes, $base, $valor
                                $porcentajeAir = round(($valretair / $baseimpair) * 100, 2);
                                $porcentajeAir = $this->aproximarPorcentaje($porcentajeAir);
                            }
                            $xmlcompras .= "
                                        <detalleAir>
                                            <codRetAir>" . $codretair . "</codRetAir>
                                            <baseImpAir>" . $baseimpair . "</baseImpAir>
                                            <porcentajeAir>$porcentajeAir</porcentajeAir>
                                            <valRetAir>" . $valretair . "</valRetAir>
                                        </detalleAir>
                                   ";
                        }
                    }
                    $xmlcompras .= "</air>";
                    $acumret  = 0;
                    $base_imp = $compra[0]->subtotal;
                    $iva_rem  = $compra[0]->iva_total;
                    foreach ($reti as $ret) {
                        if (isset($retcomp[$value->mov_id][$ret->id])) {
                            $codretair  = $retcomp[$value->mov_id][$ret->id]['codigo'] == 3030 ? '303A' : $retcomp[$value->mov_id][$ret->id]['codigo'];
                            $baseimpair = $retcomp[$value->mov_id][$ret->id]['base'];
                            $valretair  = $retcomp[$value->mov_id][$ret->id]['valor'];
                            if ($ret->id == 1) {
                                $xmlcompras = str_replace("[valRetBien10]", $valretair, $xmlcompras);
                                $acumret += $valretair;
                                $base_imp += $baseimpair;
                                $iva_rem = $iva_rem - $valretair;
                            }
                            if ($ret->id == 2) {
                                $xmlcompras = str_replace("[valRetServ20]", $valretair, $xmlcompras);
                                $acumret += $valretair;
                                $base_imp += $baseimpair;
                                $iva_rem = $iva_rem - $valretair;
                            }
                            if ($ret->id == 3) {
                                $xmlcompras = str_replace("[valorRetBienes]", $valretair, $xmlcompras);
                                $acumret += $valretair;
                                $base_imp += $baseimpair;
                                $iva_rem = $iva_rem - $valretair;
                            }
                            if ($ret->id == 4) {
                                $xmlcompras = str_replace("[valorRetServicios]", $valretair, $xmlcompras);
                                $acumret += $valretair;
                                $base_imp += $baseimpair;
                                $iva_rem = $iva_rem - $valretair;
                            }
                            if ($ret->id == 5) {
                                $xmlcompras = str_replace("[valRetServ100]", $valretair, $xmlcompras);
                                $acumret += $valretair;
                                $base_imp += $baseimpair;
                                $iva_rem = $iva_rem - $valretair;
                            }
                        }
                    }
                    $xmlcompras = str_replace("[valRetBien10]", "0.00", $xmlcompras);
                    $xmlcompras = str_replace("[valRetServ20]", "0.00", $xmlcompras);
                    $xmlcompras = str_replace("[valorRetBienes]", "0.00", $xmlcompras);
                    $xmlcompras = str_replace("[valorRetServicios]", "0.00", $xmlcompras);
                    $xmlcompras = str_replace("[valRetServ100]", "0.00", $xmlcompras);
                    //$totbasesimpreemb = number_format($this->getNCProveedores($value->mov_id, $mes, $anio, $empresa->id), 2, '.', '');
                    $totbasesimpreemb = 0;
                    $xmlcompras = str_replace("[totbasesImpReemb]", $totbasesimpreemb, $xmlcompras);
                    $cabret = Ct_Retenciones::where('id_compra', $value->mov_id)->where('estado', 1)->first();
                    //dd($cabret);
                    // dd($cabret);
                    if (!is_null($cabret) and isset($cabret->nro_comprobante)) {
                        list($estab, $ptoemision, $secuencia) = explode('-', $cabret->nro_comprobante);
                        $fechaemiret1 = date('d/m/Y', strtotime($cabret->created_at));
                    } else {
                        //dd($ptoemision);
                        $estab        = "001";
                        $ptoemision   = "001";
                        $secuencia    = "";
                        $fechaemiret1 = "";
                    }
                    if (!is_null($cabret) and isset($cabret->nro_comprobante) && $cabret->autorizacion != null) {
                        $xmlcompras .= "<estabRetencion1>$estab</estabRetencion1>
                    <ptoEmiRetencion1>$ptoemision</ptoEmiRetencion1>
                    <secRetencion1>" . $secuencia . "</secRetencion1>
                    <autRetencion1>$cabret->autorizacion</autRetencion1>
                    <fechaEmiRet1>$fechaemiret1</fechaEmiRet1>";
                    }
                    $xmlcompras .= "</detalleCompras>";
                }
                $count++;
                $idsCompra[$count] = $value->mov_id;
            }
        }
        $docMismoPeriodo = '';
        $peNC = explode('/', $periodo);
        $docMismoPeriodo = Ct_Credito_Acreedores::select('id_compra as mov_id', 'id')
            ->whereYear('fecha', $peNC[0])
            ->whereMonth('fecha', $peNC[1])
            ->whereNotIn('id_compra', $idsCompra)
            ->get();
        if (count($docMismoPeriodo) > 0) {
            foreach ($docMismoPeriodo as $row) {
                if ($cont == 0) {
                    $compra = Ct_compras::find($row->mov_id);
                    if ($compra != '') {
                        $xmlcompras .= $this->loadNCRe($compra->id, $row, $empresa->id, $periodo);
                    }
                }
            }
        }
        $xmlcompras .= "</compras>";
        // TODO: VENTAS
        $valorretiva   = 0;
        $valorretrenta = 0;
        $data          = $this->getAtsxTipo_2($ats_id, 1);
        $totales       = $this->getRetencionesV($data);
        $xmlventas              = "";
        $ventas_establecimiento = "";
        if (count($data) > 0) {
            $xmlventas = "<ventas>";
            $acum_ventas = array();
            $acum_iva = array();
            $datos_ventas = array();
            $total_ventas_final = 0;
            foreach ($data as $value) {
                $salta = 0;
                $venta = Ct_ventas::find($value->mov_id);
                if (strlen($venta->id_cliente) == 13 and $venta->id_cliente != '9999999999999') {
                    $tpidcliente = "04";
                } elseif (strlen($venta->id_cliente) == 10) {
                    $tpidcliente = "05";
                    $cliente = Ct_Clientes::find($venta->id_cliente);
                    if ($cliente->tipo !== '') {
                        if ($cliente->tipo == 8) {
                            $tpidcliente = "06";
                        } else {
                            $tpidcliente = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                        }
                    }
                } elseif ($venta->id_cliente == "9999999999999") {
                    $tpidcliente = "07";
                } else {
                    $tpidcliente = "06";
                    $salta       = 0;
                }
                if ($salta == 0) {
                    $ventas_generales = Ct_ats::where('ct_ats.id', '=', $ats_id)
                        ->join('ct_detalle_ats as de', 'ct_ats.id', 'de.ats_id')
                        ->join('ct_ventas as ven', 'de.mov_id', 'ven.id')
                        ->where('de.tipo_mov', 1)
                        ->where('ven.tipo', 'VEN-FA')
                        ->where('ven.estado', '1')
                        ->where('ven.id_cliente', $value->cedula_cliente)
                        ->get();
                    $valoretiva     = "0.00";
                    $valoretrenta   = "0.00";
                    $venta_base_12  = 0;
                    $venta_base_0   = 0;
                    $venta_impuesto = 0;
                    $cantidad_fact  = 0;
                    foreach ($ventas_generales as $key => $value_ventasg) {
                        if (session('id_empresa') == '0993366635001' && session('id_empresa') == '0993075000001') {
                            if (isset($totales[$value_ventasg->mov_id]['IVA'])) {
                                $valoretiva += $totales[$venta->id]['IVA'];
                            }
                            if (isset($totales[$value_ventasg->mov_id]['RENTA'])) {
                                $valoretrenta = $totales[$venta->id]['RENTA'];
                            }
                        }
                        $venta_2 = Ct_ventas::find($value_ventasg->mov_id);
                        if ($venta_2->subtotal_12 != null) {
                            $venta_base_12 += $venta_2->subtotal_12;
                        }
                        if ($venta_2->subtotal_0 != null) {
                            $venta_base_0 += $venta_2->subtotal_0;
                        }
                        if ($venta_2->impuesto != null) {
                            $venta_impuesto += $venta_2->impuesto;
                        }
                        $cantidad_fact++;
                    }
                    $lk = $this->loadRete($value->cedula_cliente, $mes, $anio, $empresa->id);
                    //d($lk);
                    $valoretiva   = $lk['valor_iva'];
                    $valoretrenta = $lk['valor_fuente'];
                    $total_ventas_final += $venta_base_0 + $venta_base_12;
                    if (isset($puntoemision) and isset($serie) and isset($secuencia)) {
                        list($puntoemision, $serie, $secuencia) = explode('-', $value->num_comprobante);
                    }
                    if (isset($acum_ventas[$puntoemision])) {
                        $acum_ventas[$puntoemision] += $value->subtotal;
                        //dd("aqui");
                    } else {
                        $acum_ventas[$puntoemision] = $value->subtotal;
                        // dd($acum_ventas);
                    }
                    //dd($value->subtotal);
                    if (isset($acum_iva[$puntoemision])) {
                        $acum_iva[$puntoemision] += $value->monto_iva;
                    } else {
                        $acum_iva[$puntoemision] = $value->monto_iva;
                    }
                    $datos_ventas[$puntoemision][0] = $venta->sucursal;
                    $datos_ventas[$puntoemision][1] = $venta->numero;
                    //dd($venta);
                    $xmlventas .= "<detalleVentas>
                        <tpIdCliente>$tpidcliente</tpIdCliente>
                        <idCliente>$venta->id_cliente</idCliente>";
                    if ($tpidcliente != '07') {
                        $xmlventas .= "
                        <parteRelVtas>NO</parteRelVtas>";
                    }

                    if ($tpidcliente == '06') {
                        $xmlventas .= "
                        <tipoCliente>01</tipoCliente>
                        <denoCli>" . str_replace("(N/A)", "", Utilidades::sanear_string($venta->cliente->nombre)) . "</denoCli>";
                    }
                    $xmlventas .= "
                        <tipoComprobante>18</tipoComprobante>
                        <tipoEmision>F</tipoEmision>
                        <numeroComprobantes>" . $cantidad_fact . "</numeroComprobantes>
                        <baseNoGraIva>0.00</baseNoGraIva>
                        <baseImponible>" . number_format($venta_base_0, 2, '.', '') . "</baseImponible>
                        <baseImpGrav>" . number_format($venta_base_12, 2, '.', '') . "</baseImpGrav>
                        <montoIva>" . number_format($venta_impuesto, 2, '.', '') . "</montoIva>
                        <montoIce>0.00</montoIce>
                        <valorRetIva>" . number_format($valoretiva, 2, '.', '') . "</valorRetIva>
                        <valorRetRenta>" . number_format($valoretrenta, 2, '.', '') . "</valorRetRenta>
                        <formasDePago>
                            <formaPago>20</formaPago>
                        </formasDePago>
                    </detalleVentas>";
                }
            }
            $xmlventas .= "</ventas>";
            // TODO: AQUI VA EL RESUMEN DE LAS VENTAS X ESTABLECIMIENTO

            $ventas_establecimiento = "
                <ventasEstablecimiento>";
            //dd($datos_ventas);
            foreach ($acum_ventas as $key => $value) {
                if (!isset($acum_iva[$key])) {
                    $acum_iva[$key] = 0;
                }
                $ventas_establecimiento .= "
                    <ventaEst>
                        <codEstab>$key</codEstab>
                        <ventasEstab>" . number_format($total_ventas_final, 2, '.', '') . "</ventasEstab>
                        <ivaComp>" . $acum_iva[$key] . "</ivaComp>
                    </ventaEst>";
            }
            $cab = str_replace("[totalVentas]", number_format($total_ventas_final, 2, '.', ''), $cab);
            //dd($total_ventas_final);
            $ventas_establecimiento .= "
                </ventasEstablecimiento>";
        } else {
            $cab = str_replace("[totalVentas]", '0.00', $cab);
            /*$retencionesBancarias = Ct_Transferencia_Bancaria::selectRaw(
                'GROUP_CONCAT(id_proveedor LIMIT 1) as id_proveedor,
                COUNT(id) as num,
                SUM(iva_retenido) as iva_retenido,
                SUM(retencion_fuente) as retencion_fuente'
            )
                ->where('fecha_asiento', 'like', '%' . $anio . '-' . $mes . '%')
                ->whereNotNull('id_proveedor')
                ->groupBy('id_proveedor')
                ->get();
            if (count($retencionesBancarias) > 0) {
                $xmlventas = '<ventas>';
                foreach ($retencionesBancarias as $row) {
                    $xmlventas .= '<detalleVentas>
                        <tpIdCliente>04</tpIdCliente>
                        <idCliente>' . $row->id_proveedor . '</idCliente>
                        <parteRelVtas>NO</parteRelVtas>
                        <tipoComprobante>18</tipoComprobante>
                        <tipoEmision>F</tipoEmision>
                        <numeroComprobantes>' . $row->num . '</numeroComprobantes>
                        <baseNoGraIva>0.00</baseNoGraIva>
                        <baseImponible>0.00</baseImponible>
                        <baseImpGrav>0.00</baseImpGrav>
                        <montoIva>0.00</montoIva>
                        <montoIce>0.00</montoIce>
                        <valorRetIva>' . $row->iva_retenido . '</valorRetIva>
                        <valorRetRenta>' . $row->retencion_fuente . '</valorRetRenta>
                        <formasDePago>
                            <formaPago>01</formaPago>
                        </formasDePago>
                    </detalleVentas>';
                }
                $xmlventas .= '</ventas>
                <ventasEstablecimiento>
                        <ventaEst>
                            <codEstab>001</codEstab>
                            <ventasEstab>0.00</ventasEstab>
                            <ivaComp>0.00</ivaComp>
                        </ventaEst>
                    </ventasEstablecimiento>';
            }*/
        }
        $anulados = "";
        $data     = $this->getAtsxTipo($ats_id, 3);
        $anulados = "<anulados>";
        foreach ($data as $value) {
            $anulado = Ct_ventas::find($value->mov_id);
            if ($anulado != '') {
                try {
                    list($estab, $emision, $secuencia) = explode('-', $anulado->nro_comprobante);
                    $anulados .= "
                <detalleAnulados>
                    <tipoComprobante>18</tipoComprobante>
                    <establecimiento>" . $datos_ventas[$key][0] . "</establecimiento>
                    <puntoEmision>" . $emision . "</puntoEmision>
                    <secuencialInicio>" . round($secuencia) . "</secuencialInicio>
                    <secuencialFin>" . round($secuencia) . "</secuencialFin>
                    <autorizacion>" . $anulado->nro_autorizacion . "</autorizacion>
                </detalleAnulados>
            ";
                } catch (Exception $e) {
                    $this->conError++;
                    $this->mns .= 'Error: ' . $e->getMessage() . ' Motivo: La factura de venta con el ID: ' . $value->mov_id . ' no tiene la serie correctamente ingresada<br/>';
                }
            }
        }
        $anulados .= "</anulados>";
        $pie = "</iva>";
        /*if (Auth::user()->id != '0921605895') {
            header('Content-type: text/xml');
            header("Content-Disposition: attachment; filename=$datos_ats->periodo.xml");
            header("Pragma: no-cache");
            header("Expires: 0");
        }*/
        if ($this->conError == 0) {
            header('Content-type: text/xml');
            header("Content-Disposition: attachment; filename=$datos_ats->periodo.xml");
            header("Pragma: no-cache");
            header("Expires: 0");
        } else {
            $arrayErrores = [
                'num_errores' => $this->conError,
                'mns' => $this->mns
            ];
            echo '<pre>';
            print_r($arrayErrores);
            exit;
        }
        echo "$cab$xmlcompras$xmlventas$ventas_establecimiento$anulados$pie";
    }
    private function aproximarPorcentaje($porcentaje)
    {
        $valoresValidos = [1, 1.75, 2, 2.75, 3, 5, 8, 10];
        $closest = $valoresValidos[0];
        foreach ($valoresValidos as $v) {
            if (abs($porcentaje - $v) < abs($porcentaje - $closest)) {
                $closest = $v;
            }
        }
        return $closest;
    }
    public function loadRete($id, $mes, $anio, $id_empresa)
    {
        $valorCliente = array();
        $valor_iva = Ct_Cliente_Retencion::where('id_cliente', $id)->where('estado', '<>', '0')->whereMonth('fecha', $mes)->whereYear('fecha', $anio)->where('id_empresa', $id_empresa)->sum('valor_iva');
        $valor_fuente = Ct_Cliente_Retencion::where('id_cliente', $id)->where('estado', '<>', '0')->whereMonth('fecha', $mes)->whereYear('fecha', $anio)->where('id_empresa', $id_empresa)->sum('valor_fuente');
        $base_imponible = Ct_Detalle_Cliente_Retencion::join('ct_cliente_retencion', 'ct_detalle_cliente_retencion.id_cliente_retencion', '=', 'ct_cliente_retencion.id')
            ->where('ct_cliente_retencion.estado', '<>', 0)
            ->where('ct_cliente_retencion.id_cliente', $id)
            ->where('ct_cliente_retencion.id_empresa', $id_empresa)
            ->whereMonth('ct_cliente_retencion.fecha', $mes)
            ->whereYear('ct_cliente_retencion.fecha', $anio)
            ->sum('ct_detalle_cliente_retencion.base_imponible');
        $valorCliente['valor_iva'] = $valor_iva;
        $valorCliente['valor_fuente'] = $valor_fuente;
        $valorCliente['base_imponible'] = $base_imponible;
        return $valorCliente;
    }
    public function loadNC($id, $value, $empresa, $periodo, $id_tipo_comprobante = '')
    {
        $xmlcompras = "";
        $compra = Ct_Credito_Acreedores::where('id_compra', $value->mov_id)
            ->where('estado', '>', 0)
            //->where('fecha', 'like', '%' . $periodo . '%')
            ->first();
        if (!is_null($compra)) {
            if ($id_tipo_comprobante == '') {
                $periodoNC = explode('-', $compra->fecha);
                $fechaPeriodoNC = $periodoNC[0] . '/' . $periodoNC[1];
                if ($fechaPeriodoNC == $periodo) {
                    // dd('aa');
                    //$secuencial = $compra->nro_comprobante;
                    $secuencial = $compra->secuencia;
                    list($establecimiento, $puntoemision) = explode('-', $compra->serie);
                    $findme = '0';
                    while (strpos($secuencial, $findme) === 0) {
                        $secuencial = substr($secuencial, 1);
                    }
                    if (!is_null($compra->autorizacion) && ($compra->id_credito_tributario != "00")) {
                        //dd($compra);
                        $ice_total = '0.00';
                        $iva_total = '0.00';
                        if ($compra->subtotal_0 == null) {
                            $compra->subtotal_0 = "0.00";
                        }
                        if ($compra->subtotal_12 == null) {
                            $compra->subtotal_12 = "0.00";
                        } else
                            $iva_total = $compra->impuesto;
                        //dd($compra);
                        $xmlcompras .= "<detalleCompras>";
                        if ($compra->id_tipo_comprobante == '03') {
                            $xmlcompras .= "
                    <codSustento>02</codSustento>";
                        } else {
                            $xmlcompras .= "
                    <codSustento>" . str_pad($compra->id_credito_tributario, 2, "0", STR_PAD_LEFT) . "</codSustento>";
                        }
                        if ($compra->id_tipo_comprobante == '03') {
                            $xmlcompras .= "
                    <tpIdProv>02</tpIdProv>";
                        } else {
                            $xmlcompras .= "
                    <tpIdProv>01</tpIdProv>";
                        }
                        $idx = $compra->compra->proveedor;
                        $xmlcompras .= "
                    <idProv>$idx</idProv>
                    <tipoComprobante>04</tipoComprobante>";
                        $xmlcompras .= "
                    <parteRel>NO</parteRel>
                    <fechaRegistro>" . date('d/m/Y', strtotime($compra->fecha)) . "</fechaRegistro>
                    <establecimiento>" . str_pad($establecimiento, 2, "0", STR_PAD_LEFT) . "</establecimiento>
                    <puntoEmision>" . str_pad($puntoemision, 2, "0", STR_PAD_LEFT) . "</puntoEmision>
                    <secuencial>" . $secuencial . "</secuencial>
                    <fechaEmision>" . date('d/m/Y', strtotime($compra->fecha)) . "</fechaEmision>
                    <autorizacion>$compra->autorizacion</autorizacion>
                    <baseNoGraIva>0.00</baseNoGraIva>
                    <baseImponible>$compra->subtotal_0</baseImponible>
                    <baseImpGrav>$compra->subtotal_12</baseImpGrav>
                    <baseImpExe>0.00</baseImpExe>
                    <montoIce>" . number_format($ice_total, 2, '.', '') . "</montoIce>
                    <montoIva>" . number_format($iva_total, 2, '.', '') . "</montoIva>
                    <valRetBien10>[valRetBien10]</valRetBien10>
                    <valRetServ20>[valRetServ20]</valRetServ20>
                    <valorRetBienes>[valorRetBienes]</valorRetBienes>
                    <valRetServ50>0.00</valRetServ50>
                    <valorRetServicios>[valorRetServicios]</valorRetServicios>
                    <valRetServ100>[valRetServ100]</valRetServ100>
                    <totbasesImpReemb>[totbasesImpReemb]</totbasesImpReemb>
                    <pagoExterior>
                        <pagoLocExt>01</pagoLocExt>
                        <paisEfecPago>NA</paisEfecPago>
                        <aplicConvDobTrib>NA</aplicConvDobTrib>
                        <pagExtSujRetNorLeg>NA</pagExtSujRetNorLeg>
                    </pagoExterior>
                    <formasDePago>
                        <formaPago>20</formaPago>
                    </formasDePago>";
                        $compras = Ct_compras::find($value->mov_id);
                        //dd($compra);
                        $xmlcompras = str_replace("[valRetBien10]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valRetServ20]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valorRetBienes]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valorRetServicios]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valRetServ100]", "0.00", $xmlcompras);
                        //$totbasesimpreemb = number_format($this->getNCProveedores($value->mov_id, $mes, $anio, $empresa->id), 2, '.', '');
                        $totbasesimpreemb = 0;
                        $xmlcompras       = str_replace("[totbasesImpReemb]", $totbasesimpreemb, $xmlcompras);
                        //dd($ptoemision);
                        $estab        = "001";
                        $ptoemision   = "001";
                        $secuencia    = "";
                        $fechaemiret1 = "";
                        //document for modify 
                        $secuencs = explode('-', $compras->numero);
                        $sucursals = $secuencs[0];
                        $puntoemisions = $secuencs[1];
                        $secuencial = $secuencs[2];
                        $xmlcompras .= "<docModificado>" . $compras->tipo_comprobante . "</docModificado>
                <estabModificado>" . $sucursals . "</estabModificado>
                <ptoEmiModificado>" . $puntoemisions . "</ptoEmiModificado>
                <secModificado>" . $secuencial . "</secModificado>
                <autModificado>" . $compras->autorizacion . "</autModificado>";
                        $xmlcompras .= "</detalleCompras>";
                    }
                }
            }
        }
        return $xmlcompras;
    }
    public function loadNCRe($id, $value, $empresa, $periodo, $id_tipo_comprobante = '')
    {
        $xmlcompras = "";
        $compra = Ct_Credito_Acreedores::where('id', $value->id)
            ->where('estado', '>', 0)
            //->where('fecha', 'like', '%' . $periodo . '%')
            ->first();
        if (!is_null($compra)) {
            if ($id_tipo_comprobante == '') {
                $periodoNC = explode('-', $compra->fecha);
                $fechaPeriodoNC = $periodoNC[0] . '/' . $periodoNC[1];
                if ($fechaPeriodoNC == $periodo) {
                    // dd('aa');
                    //$secuencial = $compra->nro_comprobante;
                    $secuencial = $compra->secuencia;
                    list($establecimiento, $puntoemision) = explode('-', $compra->serie);
                    $findme = '0';
                    while (strpos($secuencial, $findme) === 0) {
                        $secuencial = substr($secuencial, 1);
                    }
                    if (!is_null($compra->autorizacion) && ($compra->id_credito_tributario != "00")) {
                        //dd($compra);
                        $ice_total = '0.00';
                        $iva_total = '0.00';
                        if ($compra->subtotal_0 == null) {
                            $compra->subtotal_0 = "0.00";
                        }
                        if ($compra->subtotal_12 == null) {
                            $compra->subtotal_12 = "0.00";
                        } else
                            $iva_total = $compra->impuesto;
                        //dd($compra);

                        $xmlcompras .= "<detalleCompras>";
                        if ($compra->id_tipo_comprobante == '03') {
                            $xmlcompras .= "
                    <codSustento>02</codSustento>";
                        } else {
                            $xmlcompras .= "
                    <codSustento>" . str_pad($compra->id_credito_tributario, 2, "0", STR_PAD_LEFT) . "</codSustento>";
                        }
                        if ($compra->id_tipo_comprobante == '03') {
                            $xmlcompras .= "
                    <tpIdProv>02</tpIdProv>";
                        } else {
                            $xmlcompras .= "
                    <tpIdProv>01</tpIdProv>";
                        }
                        $idx = $compra->compra->proveedor;
                        $xmlcompras .= "
                    <idProv>$idx</idProv>
                    <tipoComprobante>04</tipoComprobante>";
                        $xmlcompras .= "
                    <parteRel>NO</parteRel>
                    <fechaRegistro>" . date('d/m/Y', strtotime($compra->fecha)) . "</fechaRegistro>
                    <establecimiento>" . str_pad($establecimiento, 2, "0", STR_PAD_LEFT) . "</establecimiento>
                    <puntoEmision>" . str_pad($puntoemision, 2, "0", STR_PAD_LEFT) . "</puntoEmision>
                    <secuencial>" . str_pad($secuencial, 9, 0, STR_PAD_LEFT) . "</secuencial>
                    <fechaEmision>" . date('d/m/Y', strtotime($compra->fecha)) . "</fechaEmision>
                    <autorizacion>$compra->autorizacion</autorizacion>
                    <baseNoGraIva>0.00</baseNoGraIva>
                    <baseImponible>$compra->subtotal_0</baseImponible>
                    <baseImpGrav>$compra->subtotal_12</baseImpGrav>
                    <baseImpExe>0.00</baseImpExe>
                    <montoIce>" . number_format($ice_total, 2, '.', '') . "</montoIce>
                    <montoIva>" . number_format($iva_total, 2, '.', '') . "</montoIva>
                    <valRetBien10>[valRetBien10]</valRetBien10>
                    <valRetServ20>[valRetServ20]</valRetServ20>
                    <valorRetBienes>[valorRetBienes]</valorRetBienes>
                    <valRetServ50>0.00</valRetServ50>
                    <valorRetServicios>[valorRetServicios]</valorRetServicios>
                    <valRetServ100>[valRetServ100]</valRetServ100>
                    <totbasesImpReemb>[totbasesImpReemb]</totbasesImpReemb>
                    <pagoExterior>
                        <pagoLocExt>01</pagoLocExt>
                        <paisEfecPago>NA</paisEfecPago>
                        <aplicConvDobTrib>NA</aplicConvDobTrib>
                        <pagExtSujRetNorLeg>NA</pagExtSujRetNorLeg>
                    </pagoExterior>
                    <formasDePago>
                        <formaPago>20</formaPago>
                    </formasDePago>";
                        $compras = Ct_compras::find($value->mov_id);
                        //dd($compra);
                        $xmlcompras = str_replace("[valRetBien10]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valRetServ20]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valorRetBienes]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valorRetServicios]", "0.00", $xmlcompras);
                        $xmlcompras = str_replace("[valRetServ100]", "0.00", $xmlcompras);

                        //$totbasesimpreemb = number_format($this->getNCProveedores($value->mov_id, $mes, $anio, $empresa->id), 2, '.', '');
                        $totbasesimpreemb = 0;
                        $xmlcompras       = str_replace("[totbasesImpReemb]", $totbasesimpreemb, $xmlcompras);
                        //dd($ptoemision);
                        $estab        = "001";
                        $ptoemision   = "001";
                        $secuencia    = "";
                        $fechaemiret1 = "";
                        //document for modify 
                        $secuencs = explode('-', $compras->numero);
                        $sucursals = $secuencs[0];
                        $puntoemisions = $secuencs[1];
                        $secuencial = $secuencs[2];
                        $xmlcompras .= "<docModificado>" . $compras->tipo_comprobante . "</docModificado>
                <estabModificado>" . $sucursals . "</estabModificado>
                <ptoEmiModificado>" . $puntoemisions . "</ptoEmiModificado>
                <secModificado>" . $secuencial . "</secModificado>
                <autModificado>" . $compras->autorizacion . "</autModificado>";
                        $xmlcompras .= "</detalleCompras>";
                    }
                }
            }
        }
        return $xmlcompras;
    }
}
