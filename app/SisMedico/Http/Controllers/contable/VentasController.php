<?php

namespace Sis_medico\Http\Controllers\contable;

use App\Modules\Billing\Application\Queries\FindSalesInvoiceForCompany;
use DateTime;
use Excel;
use Exception;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use PHPExcel_Worksheet_Drawing;
use Response;
use Session;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Contable;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Caja;
use Sis_medico\CtCajaCobro;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_Detalle_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Detalle_Venta_Conglomerada;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Factura_Omni;
use Sis_medico\Ct_Factura_Procedimiento;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Ct_Porcentajes_Retencion_Fuente;
use Sis_medico\Ct_Porcentajes_Retencion_Iva;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Ct_Retencion_Fventas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Ventas_Recibo_Cobro;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Empresa;
use Sis_medico\EntregaMercaderia;
use Sis_medico\EntregaMercaderiaTrazabilidad;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario;
use Sis_medico\Log_usuario;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\ParametersConglomerada;
use Sis_medico\Planilla;
use Sis_medico\Plan_Cuentas;
use Sis_medico\PrecioProducto;
use Sis_medico\Procedimiento;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Validate_Decimals;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Comision_Detalle;
use Sis_medico\Ct_Comision_Cabecera;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\De_Empresa;
use Sis_medico\De_Info_Tributaria;
use Sis_medico\Http\Controllers\contable\VentasController as ContableVentasController;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Log_movimiento;
use Sis_medico\LogAsiento;
use Sis_medico\Pais;
use Sis_medico\Proforma_Venta_Cab;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Empresa_Relacionada;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\InvContableCab;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Log_Venta_Costo;
use Sis_medico\LogReciboDeCobro;
use Sis_medico\Venta_Kardex;
use Sis_medico\SeguroTipos;
use Sis_medico\Utilidades;
use function Illuminate\Support\defer;

class VentasController extends Controller
{
    private function calcularTotalesConglomerada(array $data)
    {
        $subtotal0 = 0.0;
        $subtotalIva = 0.0;
        $descuentoTotal = 0.0;
        $baseIva = 0.0;
        $base0 = 0.0;
        $copagoTotal = 0.0;
        $descuentos = isset($data['desc']) && is_array($data['desc']) ? $data['desc'] : [];

        foreach ((array) ($data['codigo'] ?? []) as $i => $codigo) {
            $cantidad = (float) ($data['cantidad'][$i] ?? 0);
            $precio = (float) ($data['precio'][$i] ?? 0);
            $copago = round((float) ($data['copago'][$i] ?? 0), 2, PHP_ROUND_HALF_UP);
            $descuento = round((float) ($descuentos[$i] ?? 0), 2, PHP_ROUND_HALF_UP);
            $subtotalLinea = round(($cantidad * $precio) - $copago, 2, PHP_ROUND_HALF_UP);
            $netoLinea = round($subtotalLinea - $descuento, 2, PHP_ROUND_HALF_UP);
            $gravaIva = (float) ($data['iva'][$i] ?? 0) > 0;

            $data['copago'][$i] = $copago;
            $data['desc'][$i] = $descuento;
            $data['precioneto'][$i] = $netoLinea;

            $copagoTotal += $copago;
            $descuentoTotal += $descuento;
            if ($gravaIva) {
                $subtotalIva += $subtotalLinea;
                $baseIva += $netoLinea;
            } else {
                $subtotal0 += $subtotalLinea;
                $base0 += $netoLinea;
            }
        }

        $data['subtotal_121'] = round($subtotalIva, 2, PHP_ROUND_HALF_UP);
        $data['subtotal_01'] = round($subtotal0, 2, PHP_ROUND_HALF_UP);
        $data['descuento1'] = round($descuentoTotal, 2, PHP_ROUND_HALF_UP);
        $data['totalc'] = round($copagoTotal, 2, PHP_ROUND_HALF_UP);
        $data['base1'] = round($baseIva + $base0, 2, PHP_ROUND_HALF_UP);
        $data['base_iva'] = round($baseIva, 2, PHP_ROUND_HALF_UP);
        $data['tarifa_iva1'] = round($baseIva * (float) $this->porIva->iva, 2, PHP_ROUND_HALF_UP);
        $data['total1'] = round($data['base1'] + $data['tarifa_iva1'], 2, PHP_ROUND_HALF_UP);

        return $data;
    }

    private $controlador = 'ventas';
    private $porIva = '';
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function (Request $request, \Closure $next): mixed {
            $this->porIva = Utilidades::porcentajeIva();

            if ($this->porIva === null) {
                return response()->view('errors.204', [
                    'mensajes' => 'No se ha configurado el porcentaje del iva',
                ]);
            }

            return $next($request);
        })->except('index');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    /*******************************
     ***LISTADO FACTURAS DE VENTA ***
    /******************************/
    public function index(Request $request)
    {
        $this->validateSalesListingRequest($request);

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $usuario_actual = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find(session('id_empresa'));
        $permitidos = ParametersConglomerada::pdf_permitidos($id_empresa);
        //echo '<pre>';print_r($request->opcion);exit;
        if ($request->opcion == '') {
            $dato = Ct_ventas::getListadoVentas($request);
            $this->deferElectronicInvoiceStatusRefresh($id_empresa);

            return view('contable/ventas/index', ['empresa' => $empresa, 'dato' => $dato, 'permitidos' => $permitidos, "usuario_actual" => $usuario_actual]);
        } elseif ($request->opcion == 'logFactura') {
            $datoFact = DB::table('de_log_facturacion')
                ->join('de_pasos as p', 'de_log_facturacion.id_pasos', '=', 'p.id')
                ->join('ct_ventas as v', 'de_log_facturacion.id_documento', '=', 'v.id')
                ->join('ct_clientes as c', 'v.id_cliente', '=', 'c.identificacion')
                ->where('id_documento', $request->id)
                ->where('tipo_documento', $request->tipo)
                ->orderBy('id_pasos', 'desc')
                ->get([
                    'p.nombre',
                    'de_log_facturacion.created_at',
                    'de_log_facturacion.informacion',
                    'c.nombre as cliente',
                    'nro_comprobante'
                ]);
            $tabla = '<table style="width:100%;cc">
            <tr>
                <td align="center" style="width:100%;background:gray;color:white;font-size:20px;">
                    <table align="center">
                        <tr>
                            <td><b>Cliente: </b></td>
                            <td>' . $datoFact[0]->cliente . '</td>
                        </tr>
                        <tr>
                            <td><b>Factura No.: &nbsp;</b></td>
                            <td>' . $datoFact[0]->nro_comprobante . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>
            <table style="width:100%;font-size:18px;">
                <tr style="background:gray;color:white;">
                <td align="center"><b>Fecha</b></td>
                <td align="center"><b>Proceso</b></td>
                <td align="center"><b>Mensaje</b></td>
                </tr>
                <tr style="background:gray;color:white;"><td colspan="4">&nbsp;</td></tr>';
            if (count($datoFact) > 0) {
                foreach ($datoFact as $row) {
                    $tabla .= '<tr>
                    <td align="center" style="border-bottom:1px gray solid;">' . $row->created_at . '</td>
                    <td style="border-bottom:1px gray solid;">' . $row->nombre . '</td>
                    <td style="border-bottom:1px gray solid;">' . $row->informacion . '</td>
                    </tr>';
                }
            }
            $tabla .= '<tr><td>&nbsp;</td></tr></table>';
            echo $tabla;
        } elseif ($request->opcion == 'editarCabeceraFactura') {
            $id_empresa = $request->session()->get('id_empresa');
            $data['controlador'] = $this->controlador;
            $data['id_empresa'] = $request->session()->get('id_empresa');
            $data['empresa'] = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
            $data['pais'] = pais::where('estado', '1')->get();
            $data['venta'] = Ct_ventas::where('id', $request->id)->first();
            $data['cliente'] = Ct_Clientes::where('identificacion', $data['venta']->id_cliente)->first();
            $data['forma_pago'] = Ct_Tipo_Pago::where('estado', '1')->get();
            config(['data' => $data]);
            return view('contable/ventas/editarCabeceraFactura', $data);
        } elseif ($request->opcion == 'updateClienteFactura') {
            $arrayCliente = [
                'identificacion' => $request->identificacion,
                'nombre' => $request->nombre,
                'tipo' => $request->tipo_identificacion,
            ];
            Ct_Clientes::where('identificacion', $request->identificacion)->update($arrayCliente);
            $arrayFact = [
                'doc_electronico' => 0
            ];
            Ct_ventas::where('id', $request->id_venta)->update($arrayFact);
            return redirect('contable/ventas');
        } elseif ($request->opcion == 'descargarnotaentrega') {
            $tipoText = 'Nota de entrega de la factura-';
            $data['venta'] = Ct_ventas::find($request->id);
            $data['empresa'] = $empresa;
            $data['title'] = $tipoText . $data['venta']->nro_comprobante;
            $pdf_ = 'sri_electronico.documentosgenerados.view_notaentregapdf';
            $view = \Illuminate\Support\Facades\View::make($pdf_, $data)->render();
            $pdf = \Illuminate\Support\Facades\App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');
            return $pdf->download($tipoText . $data['venta']->nro_comprobante . '.pdf');
        } elseif ($request->opcion == 'guardarFacturaSeguros') {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $fecha_as = $request['fecha_asiento'];
            $id_empresa = $request->session()->get('id_empresa');
            $llevaOrden = false;
            $numero = $request['numero'];
            $numero1 = null;
            $nfactura = 0;
            $num_comprobante = 0;
            $empresa = Empresa::find($id_empresa);
            $msjk = ", Store: no tiene inventario";
            if ($empresa->inventario == 1) {
                $msjk = ", Store: tiene inventario";
            }
            $cliente = Ct_Clientes::where('identificacion', '=', $request['identificacion_cliente'])->count();
            $id_proforma = $request->proforma;
            DB::beginTransaction();
            $msj = "no";
            try {
                $verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_asiento);
                if ($verifica_cierre['status'] == 'error') {
                    return ['status' => $verifica_cierre['status'], 'error' => $verifica_cierre['msj']];
                }
                if ($cliente == 0) {
                    Ct_Clientes::create([
                        'nombre' => strtoupper($request['nombre_cliente']),
                        'tipo' => '5',
                        'identificacion' => $request['identificacion_cliente'],
                        'clase' => '1',
                        'nombre_representante' => $request['nombre_cliente'],
                        'cedula_representante' => $request['identificacion_cliente'],
                        'ciudad_representante' => $request['ciudad_cliente'],
                        'direccion_representante' => $request['direccion_cliente'],
                        'telefono1_representante' => $request['telefono_cliente'],
                        'email_representante' => $request['mail_cliente'],
                        'estado' => '1',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                if ($request['sucursal'] != 0) {
                    $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
                    $c_sucursal = $cod_sucurs->codigo_sucursal;
                    $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                    $c_caja = $cod_caj->codigo_caja;
                    $proced = $request['procedimiento'];
                    if ($request['electronica'] == 1) {
                        $nfactura = "";
                        $num_comprobante = "";
                    } else if (!is_null($numero)) {
                        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                        $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                            ->where('nro_comprobante', $num_comprobante)
                            ->where('tipo', '<>', 'VEN-FACT')
                            ->where('estado', '!=', '0')->first();
                        if (!is_null($verifica_num_factura)) {
                            return ["respuesta" => "existe", 'mensaje' => "Ya existe un n�mero de factura"];
                        } else {
                            $nfactura = $numero;
                        }
                    } else {
                        $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                    }
                } else {
                    $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('codigo_sucursal', $empresa->establecimiento)->first();
                    $c_sucursal = $cod_sucurs->codigo_sucursal;
                    $cod_caj = Ct_Caja::where('estado', '1')->where('codigo_caja', $empresa->punto_emision)->first();
                    $c_caja = $cod_caj->codigo_caja;
                    $proced = $request['procedimiento'];
                    if ($request['electronica'] == 1) {
                        $nfactura = "";
                        $num_comprobante = "";
                    } else if (!is_null($numero)) {
                        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                        $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                            ->where('nro_comprobante', $num_comprobante)
                            ->where('tipo', '<>', 'VEN-FACT')
                            ->where('estado', '!=', '0')->first();
                        if (!is_null($verifica_num_factura)) {
                            return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                        } else {
                            $nfactura = $numero;
                        }
                    } else {
                        $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                    }
                }
                $pac = "";
                if ($request['nombre_paciente'] != "") {
                    $pac = " | " . $request['nombre_paciente'];
                }
                $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
                $id_paciente = $request['identificacion_paciente'];
                $patient = Paciente::find($id_paciente);
                $pc = "9999999999";
                if (is_null($patient)) {
                    $pc = "9999999999";
                } else {
                    $pc = $patient->id;
                }
                $input_cabecera = [
                    'sucursal' => $cod_sucurs->codigo_sucursal,
                    'punto_emision' => $c_caja,
                    'fecha_asiento' => $fecha_as,
                    'fact_numero' => $nfactura,
                    'id_empresa' => $id_empresa,
                    'observacion' => $text . " " . $request['concepto'],
                    'valor' => $request['total1'],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $diasPlazo = "0";
                $nota_electronica = "";
                if ($request['electronica'] == 1) {
                    $diasPlazo = $request->diasPlazo;
                    $nota_electronica = [
                        $request['concepto'],
                        $request['procedimiento']
                    ];
                }
                if ($num_comprobante == '') {
                    $datos = [
                        'sucursal' => $c_sucursal,
                        'punto_emision' => $c_caja
                    ];
                    $num_comprobante = VentasController::generarNumeroFactura($datos);
                }
                $secuencialserie = explode('-', $num_comprobante);
                $factura_venta = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja,
                    'numero' => isset($secuencialserie[2]) ? $secuencialserie[2] : '',
                    'nro_comprobante' => $num_comprobante,
                    'id_asiento' => $id_asiento_cabecera,
                    'id_empresa' => $id_empresa,
                    'tipo' => $request['tipo'],
                    'fecha' => $request['fecha_asiento'],
                    'divisas' => $request['divisas'],
                    'nombre_cliente' => $request['nombre_cliente'],
                    'tipo_consulta' => $request['tipo_consulta'],
                    'id_cliente' => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente' => $request['direccion_cliente'],
                    'ruc_id_cliente' => $request['identificacion_cliente'],
                    'telefono_cliente' => $request['telefono_cliente'],
                    'email_cliente' => $request['mail_cliente'],
                    'orden_venta' => $request['orden_venta'],
                    'nro_autorizacion' => $request['numero_autorizacion'],
                    'id_paciente' => $pc,
                    'nombres_paciente' => $request['nombre_paciente'],
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente' => $request['id_seguro'],
                    'procedimientos' => $request['procedimiento'],
                    'fecha_procedimiento' => $request['fecha_proced'],
                    'concepto' => $request['concepto'],
                    'copago' => $request['totalc'] != '' ? $request['totalc'] : 0,
                    'id_recaudador' => $request['cedula_recaudador'],
                    'ci_vendedor' => $request['cedula_vendedor'],
                    'vendedor' => $request['vendedor'],
                    'electronica' => 1,
                    'subtotal_0' => $request['subtotal_01'],
                    'subtotal_12' => $request['subtotal_121'],
                    'descuento' => $request['descuento1'],
                    'base_imponible' => $request['subtotal_121'],
                    'impuesto' => $request['tarifa_iva1'],
                    'total_final' => $request['total1'],
                    'valor_contable' => $request['total1'],
                    'dias_plazo' => $diasPlazo,
                    'nota_electronica' => $nota_electronica != "" ? json_encode($nota_electronica) : json_encode([]),
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'mensaje_kardex' => $request["kardex_descripcion"] . "" . $msjk,
                    'nota_array' => $nota_electronica != "" ? json_encode($nota_electronica) : json_encode([]),
                    'doc_electronico' => $c_caja == 100 ? 0 : null,
                    'emisor' => 'MD'
                ];
                if (is_null($id_paciente)) {
                    $factura_venta = [
                        'sucursal' => $c_sucursal,
                        'punto_emision' => $c_caja,
                        'numero' => $nfactura,
                        'nro_comprobante' => $num_comprobante,
                        'id_asiento' => $id_asiento_cabecera,
                        'id_empresa' => $id_empresa,
                        'tipo' => $request['tipo'],
                        'fecha' => $request['fecha_asiento'],
                        'divisas' => $request['divisas'],
                        'nombre_cliente' => $request['nombre_cliente'],
                        'tipo_consulta' => $request['tipo_consulta'],
                        'id_cliente' => $request['identificacion_cliente'], //nombre_cliente
                        'direccion_cliente' => $request['direccion_cliente'],
                        'ruc_id_cliente' => $request['identificacion_cliente'],
                        'telefono_cliente' => $request['telefono_cliente'],
                        'email_cliente' => $request['mail_cliente'],
                        'orden_venta' => $request['orden_venta'],
                        'nro_autorizacion' => $request['numero_autorizacion'],
                        'nombres_paciente' => $request['nombre_paciente'],
                        'id_hc_procedimiento' => $request['mov_paciente'],
                        'seguro_paciente' => $request['id_seguro'],
                        'procedimientos' => $request['procedimiento'],
                        'fecha_procedimiento' => $request['fecha_proced'],
                        'concepto' => $request['concepto'],
                        'copago' => $request['totalc'] != '' ? $request['totalc'] : 0,
                        'id_recaudador' => $request['cedula_recaudador'],
                        'ci_vendedor' => $request['cedula_vendedor'],
                        'vendedor' => $request['vendedor'],
                        'electronica' => 1,
                        'subtotal_0' => $request['subtotal_01'],
                        'subtotal_12' => $request['subtotal_121'],
                        'descuento' => $request['descuento1'],
                        'base_imponible' => $request['subtotal_121'],
                        'impuesto' => $request['tarifa_iva1'],
                        'total_final' => $request['total1'],
                        'valor_contable' => $request['total1'],
                        'dias_plazo' => $diasPlazo,
                        'nota_electronica' => $nota_electronica != "" ? json_encode($nota_electronica) : json_encode([]),
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'mensaje_kardex' => $request["kardex_descripcion"] . "" . $msjk,
                        'nota_array' => $nota_electronica != "" ? json_encode($nota_electronica) : json_encode([]),
                        'doc_electronico' => $c_caja == 100 ? 0 : null,
                        'emisor' => 'MD'
                    ];
                }
                $id_venta = Ct_ventas::insertGetId($factura_venta);
                $arr_total = [];
                $total_iva = 0;
                $total_impuesto = 0;
                $total_0 = 0;
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $dato = $request->input("descrip_prod")[$i];
                        $cadena = $dato;
                        $buscar = "|";
                        $resultado = strpos($cadena, $buscar);
                        if ($resultado !== FALSE) {
                            $dato = json_encode(explode('|', $dato));
                        }
                        $arr = [
                            'nombre' => $request->input("nombre")[$i],
                            'cantidad' => $request->input("cantidad")[$i],
                            'bodega' => $request->input("bodega")[$i],
                            'codigo' => $request->input("codigo")[$i],
                            'precio' => $request->input("precio")[$i],
                            'descpor' => $request->input("descpor")[$i],
                            'copago' => $request->input("copago")[$i],
                            'descuento' => $request->input("desc")[$i],
                            'precioneto' => $request->input("precioneto")[$i],
                            'detalle' => $dato,
                            'iva' => $request->input("iva")[$i],
                            'bodega' => $request->input("bodega")[$i],
                            'id_inv_serie' => $request->input("id_inv_serie")[$i],
                        ];
                        array_push($arr_total, $arr);
                    }
                }
                $cont = 0;
                foreach ($arr_total as $valor) {
                    if ($valor['copago'] > 0) {
                        $llevaOrden = true;
                    }
                    $otros = [];
                    if ($request->codigo_publico != '' || $request->descripcion_publico != '') {
                        $otros = [
                            'codigo_publico' => $request->codigo_publico[$cont],
                            'descripcion_publico' => $request->descripcion_publico[$cont]
                        ];
                    }
                    $detalle = [
                        'id_ct_ventas' => $id_venta,
                        'id_ct_productos' => $valor['codigo'],
                        'nombre' => $valor['nombre'],
                        'descripcion_producto' => $valor['nombre'],
                        'bodega' => $valor['bodega'],
                        'cantidad' => $valor['cantidad'],
                        'precio' => $valor['precio'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'descuento' => $valor['descuento'],
                        'extendido' => $valor['precioneto'],
                        'detalle' => $valor['detalle'],
                        'copago' => $valor['copago'],
                        'check_iva' => $valor['iva'],
                        'porcentaje' => $request['ivareal'],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'id_inv_serie' => $valor["id_inv_serie"],
                        'otros' => json_encode($otros)
                    ];
                    Ct_detalle_venta::create($detalle);
                    $cont++;
                }
                $val_tol = $request['total1'];
                if ($val_tol > 0) {
                    $id_plan = LogConfig::busqueda('1.01.02.05.01');
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $plan_cuentas->id,
                        'descripcion' => $plan_cuentas->nombre,
                        'fecha' => $fecha_as,
                        'debe' => $request['total1'],
                        'haber' => '0',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                if ($request['tarifa_iva1'] > 0) {
                    $id_plan = LogConfig::busqueda('2.01.07.01.01');
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $plan_cuentas->id,
                        'descripcion' => $plan_cuentas->nombre,
                        'fecha' => $fecha_as,
                        'debe' => '0',
                        'haber' => $request['tarifa_iva1'],
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                if ($request['subtotal_121'] > 0) {
                    $id_plan = LogConfig::busqueda('4.1.01.02');
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $plan_cuentas->id,
                        'descripcion' => $plan_cuentas->nombre,
                        'fecha' => $fecha_as,
                        'debe' => '0',
                        'haber' => $request['subtotal_121'],
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                if ($request['subtotal_01'] > 0) {
                    $id_plan = LogConfig::busqueda('4.1.01.01');
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $plan_cuentas->id,
                        'descripcion' => $plan_cuentas->nombre,
                        'fecha' => $fecha_as,
                        'debe' => '0',
                        'haber' => $request['subtotal_01'],
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                $valor_descuento = $request['descuento1'];
                if ($valor_descuento > 0) {
                    $id_plan = LogConfig::busqueda('4.1.06.01');
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $plan_cuentas->id,
                        'descripcion' => $plan_cuentas->nombre,
                        'fecha' => $fecha_as,
                        'haber' => '0',
                        'debe' => $request['descuento1'],
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                $variable = $request['contador_pago'];
                for ($i = 0; $i < $variable; $i++) {
                    $visibilidad_p = $request['visibilidad_pago' . $i];
                    if ($visibilidad_p == 1) {
                        Ct_Forma_Pago::create([
                            'id_ct_ventas' => $id_venta,
                            'tipo' => $request['id_tip_pago' . $i],
                            'fecha' => $request['fecha_pago' . $i],
                            'tipo_tarjeta' => $request['tipo_tarjeta' . $i],
                            'numero' => $request['numero_pago' . $i],
                            'banco' => $request['id_banco_pago' . $i],
                            'cuenta' => $request['id_cuenta_pago' . $i],
                            'giradoa' => $request['giradoa' . $i],
                            'valor' => $request['valor' . $i],
                            'valor_base' => $request['valor_base' . $i],
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ]);
                    }
                }
                $arr_p = [];
                $validates = false;
                for ($i = 0; $i < $variable; $i++) {
                    $visibilidad_pa = $request['visibilidad_pago' . $i];
                    if ($visibilidad_pa == 1) {
                        if ($request['id_tip_pago'][$i] == '7') {
                            $validates = true;
                        }
                        $arr_pagos = [
                            'id_tip_pago' => $request['id_tip_pago' . $i],
                            'fecha_pago' => $request['fecha_pago' . $i],
                            'tipo_tarjeta' => $request['tipo_tarjeta' . $i],
                            'numero_pago' => $request['numero_pago' . $i],
                            'id_banco_pago' => $request['id_banco_pago' . $i],
                            'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                            'giradoa' => $request['giradoa' . $i],
                            'valor' => $request['valor' . $i],
                            'valor_base' => $request['valor_base' . $i],
                        ];
                        array_push($arr_p, $arr_pagos);
                    }
                }
                $erf = [];
                if ($request['id_venta'] != "") {
                    $nuevo_copago = [
                        'copago' => '0',
                    ];
                }
                if ($llevaOrden) {
                    $orden_id = $this->ordenVenta($request['id_venta'], $request);
                    $id_ct_ventas = [
                        'id_ct_venta' => $id_venta,
                    ];
                    Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                } else {
                    $orden_id = 0;
                }
                $data['id'] = $id_venta;
                $data['tipo'] = 'VEN-FA';
                $asiento_costo_kardex = "";
                $kardex["asiento_costo"] = "";
                if ($empresa->egreso_kardex == 1 and $empresa->inventario == 1) {
                    $kardex = NuevoKardexContableController::generarKardex($id_venta, $request);
                    if ($kardex["status"] == "error") {
                        DB::rollBack();
                        return $kardex;
                    }
                    $kardex["asiento_costo"] = $kardex["asiento_costo"];
                }
                $num_sec_vent = Ct_ventas::find($id_venta);
                $num_vent = $num_sec_vent->numero;
                $asiento_kardex = Contable::kardex_asiento($id_venta);
                $getSri = "No";
                $id_comprobante_ingreso = "";
                if ($c_caja != 100) {
                    if ($request['electronica'] == '1') {
                        if ($empresa->electronica == 1) {
                            $getSri = $this->getSRI($id_venta);
                            if (isset($getSri["comprobante"])) {
                                $nfactura = $getSri["comprobante"];
                                $getSri = $getSri["status"];
                            } else {
                                $getSri = $getSri;
                            }
                        }
                    }
                } else {
                    $num_sec_vent = Ct_ventas::find($id_venta);
                    $num_vent = $num_sec_vent->numero;
                    $nfactura = $num_vent;
                }

                if (!$validates) {
                    $erf = VentasController::newCrearComprobante($nfactura, $request, $arr_p, $id_venta);
                    if ($erf["status"] != "success") {
                        DB::rollBack();
                        return $erf;
                    }
                    $id_comprobante_ingreso = $erf["id_ingreso"];
                }
                $inventario = null;
                if ($id_empresa == "0993069299001") {
                    $info = VentasController::darBajaProducto($request, $id_venta);
                }
                DB::commit();
                if (!is_null($id_proforma) and trim($id_proforma) != "") {
                    $proforma = Proforma_Venta_Cab::find($id_proforma);
                    $proforma->id_venta = $id_venta;
                    $proforma->id_usuariomod = $idusuario;
                    $proforma->ip_modificacion = $ip_cliente;
                    $proforma->save();
                }
                $inventario = Inventario::build_process('V', $id_venta, $id_empresa);
                return [
                    'status' => "success",
                    'idasiento' => $id_asiento_cabecera,
                    'idventa' => $id_venta,
                    'orden_id' => $orden_id,
                    'arr_p' => $arr_p,
                    'erf' => $erf,
                    'getSri' => $getSri,
                    'num_vent' => $num_vent,
                    'error' => 'no',
                    "respuesta" => "si",
                    'mensaje' => "exito",
                    'msj' => "Guardado Correctamente",
                    'inventario' => $inventario,
                    "asiento_costo" => $kardex["asiento_costo"],
                    "id_comprobante_ingreso" => $id_comprobante_ingreso
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return ['status' => 'error', 'error' => $e->getMessage(), 'linea' => $e->getLine()];
            }
        } elseif ($request->opcion == 'informeNeto') {
            ini_set('max_execution_time', 300);
            $sin = $request['sin'];
            $id_empresa = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->first();
            $fecha_desde = $request['fecha_desde'] == '' ? '2010-01-01' : $request['fecha_desde'];
            $gastos = $request['esfac_contable'];
            $variable = 0;
            if ($gastos == null) {
                $gastos = 0;
            }
            $tipo = $request['tipo'];
            if ($tipo == null) {
                $tipo = 0;
            }
            $fecha_hasta = $request['fecha_hasta'];
            $proveedor = $request['id_cliente'];
            $vendedor = $request['id_vendedor'];
            $estado = $request['estado'];
            if (!is_null($fecha_hasta)) {
                $fecha_actual = date("Y-m-d", strtotime($fecha_hasta . "+ 1 days"));
                $data['fecha_desde'] = $fecha_desde;
                $data['fecha_hasta'] = $fecha_hasta;
                $data['empresa'] = $empresa;
                $data['id_cliente'] = $proveedor;
                $data['id_vendedor'] = $vendedor;
                $data['estado'] = $request['estado'];
                //DB::enableQueryLog();
                $data['ventas'] = Ct_ventas::select(
                    'ct_ventas.fecha',
                    'ct_ventas.nro_comprobante',
                    'ct_ventas.id_cliente',
                    'ct_ventas.nombre_cliente',
                    'ct_ventas.vendedor',
                    'ct_ventas.subtotal_0',
                    'ct_ventas.subtotal_12',
                    'ct_ventas.descuento',
                    DB::raw("(SELECT SUM(nc0.subtotal0) FROM ct_nota_credito_clientes as nc0 WHERE nc0.id_factura = ct_ventas.id AND nc0.estado != 0) as subtotal0"),
                    DB::raw("(SELECT SUM(nc12.subtotal12) FROM ct_nota_credito_clientes as nc12 WHERE nc12.id_factura = ct_ventas.id AND nc12.estado != 0) as subtotal12"),
                    'ct_ventas.estado',
                    'uc.nombre1 as creado_por',
                    'um.nombre1 as modificado_por'
                )->join('users as uc', 'uc.id', 'ct_ventas.id_usuariocrea')
                    ->join('users as um', 'um.id', 'ct_ventas.id_usuariomod')
                    ->where('ct_ventas.id_empresa', $id_empresa)
                    ->where('ct_ventas.fecha', '>=', $fecha_desde)
                    ->where('ct_ventas.fecha', '<', $fecha_actual)
                    ->where(function ($st) use ($estado) {
                        if ($estado != '') {
                            $st->where('ct_ventas.estado', $estado);
                        }
                    })
                    ->where(function ($cl) use ($proveedor) {
                        if ($proveedor != '') {
                            $cl->where('ct_ventas.id_cliente', $proveedor);
                        }
                    })
                    ->where(function ($ve) use ($vendedor) {
                        if ($vendedor != '') {
                            $ve->where('ct_ventas.ci_vendedor', $vendedor);
                        }
                    })
                    ->orderBy('ct_ventas.id_cliente')
                    ->get();
                /*if (Auth::user()->id == '0921605895') {
                    echo '<pre>';
                    print_r(DB::getQuerylog());
                    exit;
                }*/
                if ($request->app == 1) {
                    if (count($data['ventas']) > 0) {
                        $sumVenta0 = 0;
                        $sumVenta12 = 0;
                        $sumDescuento = 0;
                        $sumNotaC0 = 0;
                        $sumNotaC12 = 0;
                        $sumVentaNeta = 0;
                        foreach ($data['ventas'] as $row) {
                            if ($row->estado != 0) {
                                $sumVenta0 += $row->subtotal_0;
                                $sumVenta12 += $row->subtotal_12;
                                $sumDescuento += $row->descuento;
                                $sumNotaC0 += $row->subtotal0;
                                $sumNotaC12 += $row->subtotal12;
                            }
                        }
                        $arrayDatos['ventaNeta'] = $sumVenta0 + $sumVenta12 - $sumDescuento - ($sumNotaC0 + $sumNotaC12);
                        $arrayDatos['numFacturas'] = count($data['ventas']);
                    } else {
                        $arrayDatos['ventaNeta'] = 0;
                        $arrayDatos['numFacturas'] = 0;
                    }
                    return json_encode($arrayDatos);
                }
            }
            try {
                $usuariosVendedores = DB::table('roles_usuario')->get();
                if (count($usuariosVendedores) > 0) {
                    $count = 0;
                    foreach ($usuariosVendedores as $row) {
                        if (in_array('17', json_decode($row->tipo_usuario), true)) {
                            $idVendedores[$count] = $row->id_usuario;
                        }
                        $count++;
                    }
                }
            } catch (Exception $e) {
                $usuariosVendedores = [];
            }
            if (count($usuariosVendedores) > 0) {
                $user_vendedor = User::whereIn('id', $idVendedores)
                    ->where('estado', 1)
                    ->get();
            } else {
                $user_vendedor = User::where('id_tipo_usuario', 17)
                    ->where('estado', 1)
                    ->get();
            }
            $data['empresa'] = $empresa;
            $data['user_vendedor'] = $user_vendedor;

            if ($request->excel == 1) {
                return Excel::download(Ct_ventas::excelInformeNeto($data));
            }

            return view('contable/ventas/view_informe_neto', $data);
        } elseif ($request->opcion == 'getCliente') {
            $data = Ct_Clientes::selectRaw('identificacion as id, CONCAT(identificacion," | ",nombre) as text')
                ->where('identificacion', 'like', '%' . $request->term . '%')
                ->orWhere('nombre', 'like', '%' . $request->term . '%')
                ->get();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($request->opcion == 'cobrar') {
            return view('contable/ventas/view_cobrar');
        } elseif ($request->opcion == 'confirmpago') {
            echo '<pre>';
            print_r($request->input());
            exit;
        } elseif ($request->opcion == 'ventasExamenes') {
            $ventas = Ct_ventas::getVentasExamenes(2023);
            $html = '';
            if (count($ventas) > 0) {
                $html .= '<button onclick="exportTableToExcel(\'Reporte\', \'Reporte Ventas y ordenes examenes' . date('Y-m-d H:i:s') . '\')">Exportar archivo en excel</button><br/><table id="Reporte" style="width:100%;font-size:10px;">
                <tr>
                    <td>Fecha</td>
                    <td>Sucursal</td>
                    <td>Orden</td>
                    <td>Documento</td>
                    <td>Paciente</td>
                    <td>Cliente</td>
                    <td>Identificación</td>
                    <td>Seguro</td>
                    <td>Valor</td>
                    <td>Descuento</td>
                    <td>Total</td>
                    <td>Código</td>
                    <td>Producto</td>
                </tr>';
                foreach ($ventas as $row) {
                    $orden = '';
                    $array_2 = '';
                    if ($row->nota_array != null) {
                        $orden = json_decode($row->nota_array, true);
                        if (isset($orden[0]) && $orden[0] != '') {
                            $validacion = array_search("ORDEN", array_column($orden, 'nombre'));
                            if ($validacion !== false) {
                                $array_2 = isset($orden[$validacion]["valor"]) ? $orden[$validacion]["valor"] : '';
                            }
                        }
                    }
                    $valor = ($row->precio - $row->descuento);
                    $html .= '<tr>
                    <td>' . $row->fecha . '</td>
                    <td>' . $row->sucursal . '</td>
                    <td>' . $array_2 . '</td>
                    <td>' . $row->nro_comprobante . '</td>
                    <td>' . $row->nombres_paciente . '</td>
                    <td>' . $row->nombre_cliente . '</td>
                    <td>' . $row->id_cliente . '</td>
                    <td>' . $row->seguro . '</td>
                    <td>' . $row->precio . '</td>
                    <td>' . $row->descuento . '</td>
                    <td>' . $valor . '</td>
                    <td>' . $row->id_ct_productos . '</td>
                    <td>' . $row->producto . '</td>
                    </tr>';
                }
                $html .= '</table>
                <script>
                function exportTableToExcel(tableID, filename = \'\'){
                    var downloadLink;
                    var dataType = \'application/vnd.ms-excel\';
                    var tableSelect = document.getElementById(tableID);
                    var tableHTML = tableSelect.outerHTML.replace(/ /g, \'%20\');                    
                    // Specify file name
                    filename = filename?filename+\'.xls\':\'excel_data.xls\';                    
                    // Create download link element
                    downloadLink = document.createElement("a");                    
                    document.body.appendChild(downloadLink);                    
                    if(navigator.msSaveOrOpenBlob){
                        var blob = new Blob([\'ufeff\', tableHTML], {
                            type: dataType
                        });
                        navigator.msSaveOrOpenBlob( blob, filename);
                    }else{
                        // Create a link to the file
                        downloadLink.href = \'data:\' + dataType + \', \' + tableHTML;                    
                        // Setting the file name
                        downloadLink.download = filename;                        
                        //triggering the function
                        downloadLink.click();
                    }
                }
                </script>';
            }
            echo $html;
        } elseif ($request->opcion == 'generarConglomerada') {
            DB::beginTransaction();
            try {
                $ordenVenta = Ct_Orden_Venta::find($request->id);
                $agenda = $ordenVenta->agenda;
                $paciente = $agenda->paciente;
                $cliente = $ordenVenta->cliente;
                $procedimiento = $agenda->procedimiento;
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $arrayOrden = [
                    'sucursal' => 0,
                    'punto_emision' => 0,
                    'numero' => 0,
                    'nro_comprobante' => 0,
                    'nro_autorizacion' => 0,
                    'id_asiento' => 0,
                    'tipo' => 'VEN-FA',
                    'fecha' => $ordenVenta->fecha_emision,
                    'divisas' => 1,
                    'id_cliente' => $cliente->identificacion,
                    'id_paciente' => $agenda->id_paciente,
                    'direccion_cliente' => $cliente->direccion_representante,
                    'ruc_id_cliente' => $cliente->identificacion,
                    'telefono_cliente' => $cliente->telefono_representante,
                    'email_cliente' => $cliente->email_representante,
                    'nombres_paciente' => $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2,
                    'seguro_paciente' => $ordenVenta->id_seguro,
                    'procedimientos' => $procedimiento->nombre,
                    'fecha_procedimiento' => $agenda->fechaini,
                    'subtotal_0' => $ordenVenta->valor_oda,
                    'subtotal_12' => 0,
                    'descuento' => 0,
                    'base_imponible' => 0,
                    'impuesto' => 0,
                    'total_final' => $ordenVenta->valor_oda,
                    'estado' => 1,
                    'tipo_consulta' => 1,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'id_empresa' => session('id_empresa'),
                    'orden_venta' => $request->id,
                    'nombre_cliente' => $cliente->nombre,
                    'valor_contable' => $ordenVenta->valor_oda,
                    'copago' => $ordenVenta->valor_oda
                ];
                $idVenOrden = Ct_Ven_Orden::insertGetId($arrayOrden);
                foreach ($ordenVenta->detalles as $detalle) {
                    $arrayDetalle = [
                        'id_ct_ven_orden' => $idVenOrden,
                        'id_ct_productos' => $detalle->cod_prod,
                        'nombre' => $detalle->nombre_producto,
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio,
                        'descuento_porcentaje' => $detalle->p_desc,
                        'descuento' => $detalle->descuento,
                        'extendido' => $detalle->cantidad * $detalle->precio,
                        'check_iva' => 0,
                        'estado' => 1,
                        'id_usuariocrea' => Auth::user()->id,
                        'id_usuariomod' => Auth::user()->id,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'detalle' => $detalle->descripcion,
                        'copago' => $detalle->cobrar_seguro,
                        'id_paciente' => $agenda->id_paciente
                    ];
                    Ct_Ven_Orden_Detalle::insert($arrayDetalle);
                }
                $ordenVenta->nro_comprobante = '000-000-000000000';
                $ordenVenta->fecha_envio = date('Y-m-d H:i:s');
                $ordenVenta->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ];
                echo '<pre>';
                print_r($arrayRespuesta);
            }
        } elseif ($request->opcion == 'generarFacturaRecibo') {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            $arrayRespuesta = [];
            $erf = [];
            $id_venta = null;
            $id_asiento_cabecera = 0;
            $arr_p = [];
            $num_vent = 0;
            $id_comprobante_ingreso = 0;
            DB::beginTransaction();
            try {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                $fecha_as = date('Y-m-d H:i:s');
                $id_empresa = $request->session()->get('id_empresa');
                $orden = Ct_Orden_Venta::find($request->id_orden);
                $llevaOrden = false;
                $cliente = Ct_Clientes::where('identificacion', $request->cedula)->count();
                if ($cliente == 0) {
                    if (strlen($request['cedula']) == 10) {
                        $tipo = 5;
                    } elseif (strlen($request['cedula']) == 13) {
                        $tipo = 4;
                    } else {
                        $tipo = 5;
                    }
                    $arrayCliente = [
                        'nombre' => strtoupper($request['razon_social']),
                        'tipo' => $tipo,
                        'identificacion' => $request['cedula'],
                        'clase' => '1',
                        'nombre_representante' => $request['razon_social'],
                        'cedula_representante' => $request['cedula'],
                        'ciudad_representante' => $request['ciudad'],
                        'direccion_representante' => $request['direccion'],
                        'telefono1_representante' => $request['telefono'],
                        'email_representante' => $request['email'],
                        'estado' => '1',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_cliente = Ct_Clientes::insertGetId($arrayCliente);
                }
                $cliente = Ct_Clientes::where('identificacion', $request->cedula)->first();
                if ($request->sucursal == '') {
                    return [
                        'status' => 'info',
                        'code' => 500,
                        'message' => 'Debe seleccionar una sucursal'
                    ];
                }

                $venOrden = Ct_Ven_Orden::where('orden_venta', $orden->id)->where('estado', 1)->first(['id']);
                if ($venOrden != '') {
                    return [
                        'status' => 'info',
                        'code' => 500,
                        'message' => 'la orden ya ha sido facturada'
                    ];
                }

                $venOrden = Ct_ventas::where('orden_venta', $orden->id)->where('estado', 1)->first(['id']);
                if ($venOrden != '') {
                    return [
                        'status' => 'info',
                        'code' => 500,
                        'message' => 'la orden ya ha sido facturada'
                    ];
                }
                $establecimiento = Ct_Sucursales::find(explode('-', $request->sucursal)[0]);
                $emision = Ct_Caja::find(explode('-', $request->sucursal)[1]);
                $nfactura = $this->obtener_numero_factura($id_empresa, $establecimiento->codigo_sucursal, $emision->codigo_caja);
                $num_comprobante = $establecimiento->codigo_sucursal . '-' . $emision->codigo_caja . '-' . $nfactura;
                $paciente = Paciente::find($orden->agenda->id_paciente);
                $text = 'Fact #' . ':' . $num_comprobante . '-' . $request->observacion . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;

                foreach ($orden->detalles as $detalle) {
                    $producto = Ct_productos::where('codigo', $detalle->cod_prod)->where('id_empresa', session('id_empresa'))->first();
                    if ($detalle->valor_oda > 0) {
                        $llevaOrden = true;
                    }
                }

                $factura_venta = [
                    'sucursal' => $establecimiento->codigo_sucursal,
                    'punto_emision' => $emision->codigo_caja,
                    'nro_comprobante' => $num_comprobante,
                    'id_asiento' => 0,
                    'id_empresa' => $id_empresa,
                    'tipo' => 'VEN-FA',
                    'fecha' => $fecha_as,
                    'divisas' => 1,
                    'nombre_cliente' => $cliente->nombre,
                    'tipo_consulta' => $orden->agenda->proc_consul == 0 ? 1 : 2,
                    'id_cliente' => $cliente->identificacion,
                    'direccion_cliente' => $cliente->direccion_representante,
                    'ruc_id_cliente' => $cliente->identificacion,
                    'telefono_cliente' => $cliente->telefono1_representante,
                    'email_cliente' => $cliente->email_representante,
                    'orden_venta' => $orden->id,
                    'id_paciente' => $paciente->id,
                    'nombres_paciente' => $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2,
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente' => $orden->agenda->id_seguro,
                    'procedimientos' => $request['procedimiento'],
                    'fecha_procedimiento' => $orden->agenda->fechaini,
                    'concepto' => $request['observacion'],
                    'copago' => $orden->valor_oda,
                    'subtotal_0' => $orden->subtotal_0,
                    'subtotal_12' => $orden->subtotal_12,
                    'descuento' => $orden->descuento,
                    'base_imponible' => $orden->total,
                    'impuesto' => $orden->iva,
                    'total_final' => $orden->total,
                    'valor_contable' => $orden->total,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'nota_array' => $request['nota_electronica'] != "" ?  json_encode($request['nota_electronica']) : json_encode([]),
                    'doc_electronico' => $emision->codigo_caja == 100 ? 0 : null,
                    'emisor' => 'MD',
                    'contraentrega' => $request['contraentrega'] != '' ? $request['contraentrega'] : 0,
                ];

                if ($orden->total > 0) {
                    $input_cabecera = [
                        'sucursal' => $establecimiento->codigo_sucursal,
                        'punto_emision' => $emision->codigo_caja,
                        'fecha_asiento' => $fecha_as,
                        'fact_numero' => $nfactura,
                        'id_empresa' => $id_empresa,
                        'observacion' => $text,
                        'valor' => $orden->total,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    $diasPlazo = 0;
                    $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
                    $electronica = 0;
                    if ($deEmpresa != '') {
                        $electronica = 1;
                    }
                    $factura_venta = [
                        'sucursal' => $establecimiento->codigo_sucursal,
                        'punto_emision' => $emision->codigo_caja,
                        'nro_comprobante' => $num_comprobante,
                        'id_asiento' => $id_asiento_cabecera,
                        'id_empresa' => $id_empresa,
                        'tipo' => 'VEN-FA',
                        'fecha' => $fecha_as,
                        'divisas' => 1,
                        'nombre_cliente' => $cliente->nombre,
                        'tipo_consulta' => $orden->agenda->proc_consul == 0 ? 1 : 2,
                        'id_cliente' => $cliente->identificacion,
                        'direccion_cliente' => $cliente->direccion_representante,
                        'ruc_id_cliente' => $cliente->identificacion,
                        'telefono_cliente' => $cliente->telefono1_representante,
                        'email_cliente' => $cliente->email_representante,
                        'orden_venta' => $orden->id,
                        'id_paciente' => $paciente->id,
                        'nombres_paciente' => $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2,
                        'id_hc_procedimiento' => $request['mov_paciente'],
                        'seguro_paciente' => $orden->agenda->id_seguro,
                        'procedimientos' => $request['procedimiento'],
                        'fecha_procedimiento' => $orden->agenda->fechaini,
                        'concepto' => $request['observacion'],
                        'copago' => $orden->valor_oda,
                        'electronica' => $electronica,
                        'subtotal_0' => $orden->subtotal_0,
                        'subtotal_12' => $orden->subtotal_12,
                        'descuento' => $orden->descuento,
                        'base_imponible' => $orden->total,
                        'impuesto' => $orden->iva,
                        'total_final' => $orden->total,
                        'valor_contable' => $orden->total,
                        'dias_plazo' => $diasPlazo,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'nota_array' => $request['nota_electronica'] != "" ?  json_encode($request['nota_electronica']) : json_encode([]),
                        'doc_electronico' => $emision->codigo_caja == 100 ? 0 : null,
                        'emisor' => 'MD',
                        'contraentrega' => $request['contraentrega'] != '' ? $request['contraentrega'] : 0,
                    ];

                    $id_venta = Ct_ventas::insertGetId($factura_venta);

                    $arr_total = [];
                    $total_iva = 0;
                    $total_impuesto = 0;
                    $total_0 = 0;
                    if (count($orden->detalles) > 0) {
                        foreach ($orden->detalles as $detalle) {
                            $producto = Ct_productos::where('codigo', $detalle->cod_prod)->where('id_empresa', session('id_empresa'))->first();
                            $arr = [
                                'nombre' => $detalle->nombre_producto,
                                'cantidad' => $detalle->cantidad,
                                'codigo' => $detalle->cod_prod,
                                'precio' => $detalle->precio,
                                'descpor' => $detalle->p_dcto,
                                'copago' => $detalle->valor_oda,
                                'descuento' => $detalle->descuento,
                                'precioneto' => $detalle->total,
                                'detalle' => $detalle->descripcion,
                                'iva' => $producto->iva,
                            ];
                            array_push($arr_total, $arr);
                        }
                        $cont = 0;
                        foreach ($arr_total as $valor) {
                            if ($valor['copago'] > 0) {
                                $llevaOrden = true;
                            }
                            $detalle = [
                                'id_ct_ventas' => $id_venta,
                                'id_ct_productos' => $valor['codigo'],
                                'nombre' => $valor['nombre'],
                                'descripcion_producto' => $valor['nombre'],
                                'cantidad' => $valor['cantidad'],
                                'precio' => $valor['precio'],
                                'descuento_porcentaje' => $valor['descpor'],
                                'descuento' => $valor['descuento'],
                                'extendido' => $valor['precioneto'],
                                'detalle' => $valor['detalle'],
                                'copago' => $valor['copago'],
                                'check_iva' => $valor['iva'],
                                'porcentaje' => $this->porIva->iva,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                            ];
                            Ct_detalle_venta::create($detalle);

                            $cont++;
                        }
                    }
                    $val_tol = $orden->total;
                    if ($val_tol > 0) {
                        $id_plan = LogConfig::busqueda('1.01.02.05.01');
                        $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                        $arrayDetalleAsiento = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plan_cuentas->id,
                            'descripcion'         => $plan_cuentas->nombre,
                            'fecha'               => $fecha_as,
                            'debe'                => $orden->total,
                            'haber'               => '0',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        Ct_Asientos_Detalle::create($arrayDetalleAsiento);
                    }
                    if ($orden->iva > 0) {
                        $id_plan = LogConfig::busqueda('2.01.07.01.01');
                        $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                        $arrayDetalleAsiento = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plan_cuentas->id,
                            'descripcion'         => $plan_cuentas->nombre,
                            'fecha'               => $fecha_as,
                            'debe'                => '0',
                            'haber'               => $orden->iva,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        Ct_Asientos_Detalle::create($arrayDetalleAsiento);
                    }
                    if ($orden->subtotal_12 > 0) {
                        $id_plan = LogConfig::busqueda('4.1.01.02');
                        $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                        $arrayDetalleAsiento = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plan_cuentas->id,
                            'descripcion'         => $plan_cuentas->nombre,
                            'fecha'               => $fecha_as,
                            'debe'                => '0',
                            'haber'               => $orden->subtotal_12,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        Ct_Asientos_Detalle::create($arrayDetalleAsiento);
                    }
                    if ($orden->subtotal_0 > 0) {
                        $id_plan = LogConfig::busqueda('4.1.01.01');
                        $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                        $arrayDetalleAsiento = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plan_cuentas->id,
                            'descripcion'         => $plan_cuentas->nombre,
                            'fecha'               => $fecha_as,
                            'debe'                => '0',
                            'haber'               => $orden->subtotal_0,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        Ct_Asientos_Detalle::create($arrayDetalleAsiento);
                    }
                    if ($orden->descuento > 0) {
                        $id_plan = LogConfig::busqueda('4.1.06.01');
                        $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $plan_cuentas->id,
                            'descripcion'         => $plan_cuentas->nombre,
                            'fecha'               => $fecha_as,
                            'haber'               => '0',
                            'debe'                => $orden->descuento,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                    $formasPago = Ct_Orden_Venta_Pago::where('id_orden', $orden->id)->get();
                    $arr_p = [];
                    if (count($formasPago) > 0) {
                        foreach ($formasPago as $formaPago) {
                            $arrayFormaPago = [
                                'id_ct_ventas' => $id_venta,
                                'tipo' => $formaPago->tipo,
                                'fecha' => $formaPago->fecha,
                                'tipo_tarjeta' => $formaPago->tipo_tarjeta,
                                'numero' => $formaPago->numero,
                                'banco' => $formaPago->banco,
                                'cuenta' => $formaPago->cuenta,
                                'valor' => $formaPago->valor,
                                'valor_base' => $formaPago->valor_base,
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ];
                            Ct_Forma_Pago::insert($arrayFormaPago);
                            if ($formaPago->tipo != '7') {
                                $arr_pagos = [
                                    'id_tip_pago' => $formaPago->tipo,
                                    'fecha_pago' => $formaPago->fecha,
                                    'tipo_tarjeta' =>  $formaPago->tipo_tarjeta,
                                    'numero_pago' => $formaPago->numero,
                                    'id_banco_pago' =>  $formaPago->banco,
                                    'id_cuenta_pago' => $formaPago->cuenta,
                                    'valor' => $formaPago->valor,
                                    'valor_base' => $formaPago->valor_base,
                                    'giradoa' => ''
                                ];
                                array_push($arr_p, $arr_pagos);
                            }
                        }
                    }
                }
                $validates = false;
                if ($llevaOrden) {
                    $orden_id = $this->ordenVentaFC($id_venta, $orden, $factura_venta);
                    $id_ct_ventas = [
                        'id_ct_venta' => $id_venta,
                    ];
                    Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                } else {
                    $orden_id = 0;
                }
                $data['id'] = $id_venta;
                $data['tipo']   = 'VEN-FA';
                if ($orden->total > 0) {
                    $msj = Ct_Kardex::generar_kardex($data);
                }
                /*$num_sec_vent = Ct_ventas::find($id_venta);
                $num_vent = $num_sec_vent->numero;
                $nfactura = $num_sec_vent->nro_comprobante;*/
                //$orden->nro_comprobante = $nfactura;
                if ($orden->total > 0) {
                    $orden->nro_comprobante = '000-000-000000000';
                    $orden->save();
                }
                if ($orden->total > 0) {
                    $asiento_kardex = Contable::kardex_asiento($id_venta);
                }
                $kardex["asiento_costo"] = "";
                $id_proforma = null;
                if ($orden->total > 0) {
                    if ($empresa->egreso_kardex == 1 and $empresa->inventario == 1 or !is_null($id_proforma)) {
                        $kardex = NuevoKardexContableController::newgenerarKardex($id_venta);
                        if ($kardex["status"] ==  "error" or !$kardex["status"]) {
                            DB::rollBack();
                            return $kardex;
                        }
                        if (isset($kardex["generate"]) and $kardex["generate"]) {
                            $kardex["asiento_costo"] = $kardex["asiento_costo"];
                            $logCosto = LogAsiento::guardarAsientoCostoVenta($id_venta, $kardex["asiento_costo"]);
                            if ($logCosto["status"] == "error") {
                                DB::rollBack();
                                return $logCosto;
                            }
                        }
                    }
                }
                $getSri = "No";
                $id_comprobante_ingreso = "";
                if ($orden->total > 0) {
                    $num_sec_vent = Ct_ventas::find($id_venta);
                    $num_vent = $num_sec_vent->numero;
                    $nfactura = $num_sec_vent->nro_comprobante;
                }
                if (!$validates) {
                    $request['fecha_asiento'] = date('Y-m-d H:i:s');
                    $request['identificacion_cliente'] = $cliente->identificacion;
                    $request['valor_totalPagos'] = $orden->total;
                    $request['total1'] = $orden->total;
                    if ($orden->total > 0) {
                        if (count($arr_p) > 0) {
                            $erf = VentasController::newCrearComprobante($nfactura, $request, $arr_p, $id_venta);
                            if ($erf["status"] != "success") {
                                DB::rollBack();
                                return $erf;
                            }
                            $id_comprobante_ingreso = $erf["id_ingreso"];
                        }
                    }
                }
                $arr_log_recibo = [
                    'id_ct_orden_venta' => $orden->id,
                    'descripcion'       => 'FACTURADA EL: ' . date('Y-m-d H:i:s'),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                LogReciboDeCobro::create($arr_log_recibo);
                DB::commit();
                return [
                    'status' => "success",
                    'code' => 200,
                    'message' => 'Orden facturada correctamente',
                    'idasiento' => $id_asiento_cabecera,
                    'idventa' => $id_venta,
                    'orden_id' => $orden->id,
                    'arr_p' => $arr_p,
                    'erf' => $erf,
                    'getSri' => $getSri,
                    'num_vent' => $num_vent,
                    'error' => 'no',
                    "respuesta" => "si",
                    'mensaje' => "exito",
                    'msj' => "Guardado Correctamente",
                    'inventario' => null,
                    "asiento_costo" => $kardex["asiento_costo"],
                    "id_comprobante_ingreso" => $id_comprobante_ingreso
                ];
            } catch (Exception $e) {
                DB::rollback();
                return $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'codigo' => $e->getCode()
                ];
            }
        }
    }
    public function ordenVentaFC($id_v, $orden, $data)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        $factura_venta = [
            'sucursal' => $data['sucursal'],
            'punto_emision' => $data['punto_emision'],
            'nro_comprobante' => '000-000-000000000',
            'id_asiento' => 0,
            'id_empresa' => $id_empresa,
            'tipo' => $data['tipo'],
            'fecha' => $data['fecha'],
            'divisas' => $data['divisas'],
            'nombre_cliente' => $data['nombre_cliente'],
            'tipo_consulta' => $data['tipo_consulta'],
            'id_cliente' => $data['id_cliente'],
            'direccion_cliente' => $data['direccion_cliente'],
            'ruc_id_cliente' => $data['ruc_id_cliente'],
            'telefono_cliente' => $data['telefono_cliente'],
            'email_cliente' => $data['email_cliente'],
            'orden_venta' => $data['orden_venta'],
            'estado_pago' => '0',
            'id_paciente' => $data['id_paciente'],
            'nombres_paciente' => $data['nombres_paciente'],
            'id_hc_procedimiento' => $data['id_hc_procedimiento'],
            'seguro_paciente' => $data['seguro_paciente'],
            'procedimientos' => $data['procedimientos'],
            'fecha_procedimiento' => $data['fecha_procedimiento'],
            'copago' => $data['copago'],
            'subtotal_0' => $data['subtotal_0'],
            'subtotal_12' => $data['subtotal_12'],
            'descuento' => $data['descuento'],
            'base_imponible' => $data['base_imponible'],
            'impuesto' => $data['impuesto'],
            'total_final' => $data['copago'],
            'valor_contable' => $data['copago'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'id_ct_venta' => $id_v,
        ];

        $id_venta = Ct_ven_orden::insertGetId($factura_venta);
        $arr_total = [];
        $subtotal0 = 0;
        $subtotal_impuesto = 0;
        foreach ($orden->detalles as $detalle) {
            $producto = Ct_productos::where('codigo', $detalle->cod_prod)->first();
            $arr = [
                'nombre' => $detalle->nombre_producto,
                'cantidad' => $detalle->cantidad,
                'codigo' => $detalle->cod_prod,
                'precio' =>  $detalle->valor_oda,
                'descpor' => $detalle->p_dcto,
                'copago' => $detalle->valor_oda,
                'descuento' => $detalle->descuento,
                'precioneto' => $detalle->total,
                'detalle' => $detalle->descripcion,
                'iva' => $producto->iva,
            ];
            if ($producto->iva == 0) {
                $subtotal0 += $detalle->cobrar_seguro;
            } else {
                $subtotal_impuesto += $detalle->cobrar_seguro;
            }
            array_push($arr_total, $arr);
        }
        $ventaFactura = Ct_Ven_Orden::find($id_venta);
        $ventaFactura->subtotal_0 = $subtotal0;
        $ventaFactura->subtotal_12 = $subtotal_impuesto;
        $ventaFactura->base_imponible = $subtotal0 + $subtotal_impuesto;
        $ventaFactura->total_final = $subtotal0 + $subtotal_impuesto;
        $ventaFactura->valor_contable = $subtotal0 + $subtotal_impuesto;

        $ventaFactura->save();

        foreach ($arr_total as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['copago'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];
            Ct_ven_orden_detalle::create($detalle);
        }
        $arr_log_recibo = [
            'id_ct_orden_venta' => $orden->id,
            'descripcion'       => 'EN CONGLOMERADA EL: ' . date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        LogReciboDeCobro::create($arr_log_recibo);
        return ['id' => $id_venta];
    }
    public function ventascrm(Request $r)
    {
        $data['porIva'] = $this->porIva;
        $data['empresa'] = Empresa::find(session('id_empresa'));
        return view('contable/ventas/view_ventascrm', $data);
    }
    public function ventascrearcrm()
    {
        $id_empresa = session('id_empresa');
        $data['tipo_pago'] = Ct_Tipo_Pago::where('estado', '1')->get();
        $data['tipo_tarjeta'] = Ct_Tipo_Tarjeta::all();
        $data['empresa'] = Empresa::find($id_empresa);
        $data['lista_banco'] = Ct_Bancos::where('estado', '1')->get();
        $data['divisas'] = Ct_Divisas::where('estado', '1')->get();
        $data['sucursales'] = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $data['user_vendedor'] = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $data['seguros'] = Seguro::all();
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $data['iva'] = Ct_Configuraciones::where('id_plan', $id_plan_confg)->where('estado', '1')->first();
        $data['porIva'] = $this->porIva;
        return view('contable/ventas/view_ventascrearcrm', $data);
    }
    public function verificarplanillas(Request $r)
    {
        $id_empresa = session('id_empresa');
        $data['empresa'] = Empresa::find($id_empresa);
        if ($r->desde != '' && $r->hasta != '') {
            $data['datos'] = Agenda::getProcedimientosInsumos($r, 0);
        }
        return view('contable/ventas/view_verificarplanillas', $data);
    }
    public function detalleplanilla(Request $r)
    {
        return Agenda::detalleplanilla($r->id);
    }
    public function informe_ventas_mensual(Request $r)
    {
        $data['subtotal12'] = 0;
        $data['subtotal0'] = 0;
        $data['subtotal'] = 0;
        $data['descuento'] = 0;
        $data['impuesto'] = 0;
        $data['totales'] = 0;
        $data['empresa'] = Empresa::find(session('id_empresa'));
        $data['desde'] = null;
        $data['hasta'] = null;
        $data['tipo'] = $r->tipo;
        $data['clientes'] = Ct_Clientes::where('estado', '<>', '0')->get();
        $data['id_cliente'] = $r->id_cliente;
        $data['secuencia'] = $r->secuencia;
        if (count($r->input()) > 0) {
            $desde = date('Y-m-d', strtotime($r->fecha_desde));
            $hasta = date('Y-m-d', strtotime($r->fecha_hasta));
            $data['informe'] = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('fecha', '>=', $desde)
                ->where('fecha', '<=', $hasta)
                ->whereIn('tipo', ['VEN-FA', 'VENFA-CO'])
                ->orderBy('fecha')
                ->get();
            $data['periodo'] = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('fecha', '>', $desde)
                ->where('fecha', '<=', $hasta)
                ->orderBy('fecha')
                ->groupBy(DB::raw('YEAR(fecha),MONTH(fecha)'))
                ->get(['fecha']);
            $data['activo'] = $r->activo;
            $data['desde'] = $desde;
            $data['hasta'] = $hasta;
        }
        if ($r->excelF == 0) {
            return view('contable/ventas/informe_mensual', $data);
        } else {
            $this->exportar_informe_ventas_mensual($r);
        }
    }
    public function exportar_informe_ventas_mensual(Request $r)
    {
        try {
            if (count($r->input()) > 0) {
                ini_set('max_execution_time', 300);
                $desde = date('Y-m-d', strtotime($r->fecha_desde));
                $hasta = date('Y-m-d', strtotime($r->fecha_hasta));
                $empresa = empresa::find(session('id_empresa'));
                $informe = Ct_ventas::where('id_empresa', $empresa->id)
                    ->where('fecha', '>', $desde)
                    ->where('fecha', '<=', $hasta)
                    ->orderBy('fecha')
                    ->get();
                $periodo = Ct_ventas::where('id_empresa', session('id_empresa'))
                    ->where('fecha', '>', $desde)
                    ->where('fecha', '<=', $hasta)
                    ->orderBy('fecha')
                    ->groupBy(DB::raw('YEAR(fecha),MONTH(fecha)'))
                    ->get(['fecha']);

                Excel::create('Informe Ventas Mensual', function ($excel) use ($desde, $hasta, $informe, $periodo, $empresa) {
                    $excel->sheet('Factura por confirmar ', function ($sheet) use ($informe, $desde, $hasta, $periodo, $empresa) {
                        if ($empresa->logo != '') {
                            if (file_exists($empresa->logo)) {
                                $objDrawing = new PHPExcel_Worksheet_Drawing;
                                $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                                $objDrawing->setCoordinates('N2');
                                $objDrawing->setHeight(70);
                                $objDrawing->setWorksheet($sheet);
                            }
                        }
                        $sheet->mergeCells('D1:D2');
                        $sheet->cell('D1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('14');
                        });
                        $sheet->mergeCells('E1:O2');
                        $sheet->cell('E1', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Informe de Ventas Mensual');
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('D3:D3');
                        $sheet->cell('D3', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->id);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('E3:O3');
                        $sheet->cell('E3', function ($cell) use ($desde, $hasta) {
                            // manipulate the cel
                            $cell->setValue('Desde' . $desde . ' - Hasta' . $hasta);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                        });
                        //tabla faltante
                        $sheet->cell('A4', function ($cell) {
                            $cell->setValue('Mes');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B4', function ($cell) {

                            $cell->setValue('Subtotal0');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C4', function ($cell) {

                            $cell->setValue('Subtotal 12');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('D4', function ($cell) {

                            $cell->setValue('Subtotal');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('E4', function ($cell) {

                            $cell->setValue('Descuento');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('F4', function ($cell) {

                            $cell->setValue('Impuesto');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G4', function ($cell) {

                            $cell->setValue('Ventas Brutas');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#929899');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i = 5;
                        //Inicio omar
                        foreach ($periodo as $periodoInicial) {
                            $fecha_periodo = explode('-', $periodoInicial->fecha);
                            $anio = $fecha_periodo[0] . '-' . $fecha_periodo[1];
                            $n_credito = 0;
                            $final_credito = 0;
                            $total_mes = 0;
                            $totales = 0;
                            $subtotal = 0;
                            $subtotal0 = 0;
                            $subtotal12 = 0;
                            $subtotalTotal = 0;
                            $descuento = 0;
                            $impuesto = 0;
                            $valor_nota_credito = 0;
                            $final_con_credito = 0;
                            foreach ($informe as $infoInicial) {
                                $fecha_venta = explode('-', $infoInicial->fecha);
                                $anio_venta = $fecha_venta[0] . '-' . $fecha_venta[1];
                                if (isset($informe) && count($informe) > 0) {
                                    if ($anio == $anio_venta) {
                                        if ((int) $infoInicial->estado !== 1) {
                                            continue;
                                        }

                                        $nota_credito = DB::table('ct_detalle_credito_clientes')
                                            ->where('id_factura', $infoInicial->id)
                                            ->first();
                                        if (!is_null($nota_credito)) {
                                            $valor_nota_credito = (float) $nota_credito->abono;
                                            $final_con_credito = (($infoInicial->subtotal_0 + $infoInicial->subtotal_12) - $infoInicial->descuento) - (float) $valor_nota_credito;
                                            $n_credito += $valor_nota_credito;
                                            $final_credito += $final_con_credito;
                                        } else {
                                            $final_con_credito = $infoInicial->total_final;
                                            $final_credito += ($infoInicial->subtotal_0 + $infoInicial->subtotal_12) - $infoInicial->descuento;
                                        }

                                        //$subtotalTotal = $subtotalTotal + $infoInicial->subtotal_total == '' ? 0.00 : $infoInicial->subtotal_total;
                                        $subtotalTotal += empty($infoInicial->subtotal_total)? 0.00: (float) $infoInicial->subtotal_total;
                                        $subtotal = $subtotal + $infoInicial->subtotal_total;
                                        $subtotal0 = $subtotal0 + $infoInicial->subtotal_0;
                                        $subtotal12 = $subtotal12 + $infoInicial->subtotal_12;
                                        $totales = $totales + $infoInicial->total_final;
                                        $total_mes = $total_mes + $infoInicial->total_final;
                                        $descuento = $descuento + $infoInicial->descuento;
                                        $impuesto = $impuesto + $infoInicial->impuesto;
                                        $subt = $infoInicial->base_imponible;

                                        $mesTabla = '';
                                        switch ($fecha_periodo[1]) {
                                            case '01':
                                                $mesTabla = 'Enero - ' . $fecha_periodo[0];
                                                break;
                                            case '02':
                                                $mesTabla = 'Febrero - ' . $fecha_periodo[0];
                                                break;
                                            case '03':
                                                $mesTabla = 'Marzo - ' . $fecha_periodo[0];
                                                break;
                                            case '04':
                                                $mesTabla = 'Abril - ' . $fecha_periodo[0];
                                                break;
                                            case '05':
                                                $mesTabla = 'Mayo - ' . $fecha_periodo[0];
                                                break;
                                            case '06':
                                                $mesTabla = 'Junio - ' . $fecha_periodo[0];
                                                break;
                                            case '07':
                                                $mesTabla = 'Julio - ' . $fecha_periodo[0];
                                                break;
                                            case '08':
                                                $mesTabla = 'Agosto - ' . $fecha_periodo[0];
                                                break;
                                            case '09':
                                                $mesTabla = 'Septiembre - ' . $fecha_periodo[0];
                                                break;
                                            case '10':
                                                $mesTabla = 'Octubre - ' . $fecha_periodo[0];
                                                break;
                                            case '11':
                                                $mesTabla = 'Noviembre - ' . $fecha_periodo[0];
                                                break;
                                            default:

                                                $mesTabla = 'Diciembre - ' . $fecha_periodo[0];
                                                break;
                                        }
                                    }

                                    $sheet->setColumnFormat(array(
                                        'B' => '$ 0.00',
                                        'C' => '$ 0.00',
                                        'D' => '$ 0.00',
                                        'E' => '$ 0.00',
                                        'F' => '$ 0.00',
                                        'G' => '$ 0.00',
                                    ));

                                    $sheet->cell('A' . $i, function ($cell) use ($mesTabla) {
                                        // manipulate the cel
                                        $cell->setValue($mesTabla);
                                        $cell->setFontWeight('bold');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($subtotal0) {
                                        // manipulate the cel
                                        $cell->setValue(number_format($subtotal0, 2, '.', ','));
                                        $cell->setFontWeight('bold');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('C' . $i, function ($cell) use ($subtotal12) {
                                        // manipulate the cel
                                        $cell->setValue(number_format($subtotal12, 2, '.', ','));
                                        $cell->setFontWeight('bold');

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('D' . $i, function ($cell) use ($subtotal) {
                                        // manipulate the cel
                                        $cell->setValue(number_format($subtotal, 2, '.', ','));
                                        $cell->setFontWeight('bold');

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });


                                    $sheet->cell('E' . $i, function ($cell) use ($descuento) {
                                        // manipulate the cel
                                        $cell->setValue(number_format($descuento, 2, '.', ','));
                                        $cell->setFontWeight('bold');

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('F' . $i, function ($cell) use ($impuesto) {
                                        // manipulate the cel
                                        $cell->setValue(number_format($impuesto, 2, '.', ','));
                                        $cell->setFontWeight('bold');

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });


                                    $sheet->cell('G' . $i, function ($cell) use ($totales) {
                                        // manipulate the cel
                                        if ($totales >= 1000) {
                                            $cell->setValue(number_format($totales, 2, ',', ','));
                                        } else {
                                            $cell->setValue(number_format($totales, 2, '.', ','));
                                        }
                                        $cell->setFontWeight('bold');

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                }
                            }
                            $i++;
                        }

                        //fin omar
                        $i += 2;
                        $total_anio = 0;

                        foreach ($periodo as $peri) {
                            $fecha_periodo = explode('-', $peri->fecha);
                            $anio = $fecha_periodo[0] . '-' . $fecha_periodo[1];
                            $subtotalIndividual = 0.00;
                            $n_credito = 0.00;
                            $sheet->cell('A' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Fecha');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('B' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Número');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('C' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('CI');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('D' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Cliente');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Tipo');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Detalle');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('G' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('SubTotal0');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('H' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('SubTotal12');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Descuento');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('J' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Subtotal');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Impuesto');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Ventas Brutas');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Total NCA');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Ventas Netas');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('Estado');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#929899');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $final_credito = 0;
                            $periodo = '';
                            $total_mes = 0;
                            $totales = 0;
                            $subtotal = 0;
                            $subtotal0 = 0;
                            $subtotal12 = 0;
                            $subtotalTotal = 0;
                            $descuento = 0;
                            $impuesto = 0;
                            $total_ventas_netas = 0;
                            $i++;
                            $sheet->setColumnFormat(array(
                                'G' => '$ 0.00',
                                'H' => '$ 0.00',
                                'I' => '$ 0.00',
                                'J' => '$ 0.00',
                                'K' => '$ 0.00',
                                'L' => '$ 0.00',
                                'M' => '$ 0.00',
                                'N' => '$ 0.00',
                            ));
                            foreach ($informe as $info) {
                                $fecha_venta = explode('-', $info->fecha);
                                $anio_venta = $fecha_venta[0] . '-' . $fecha_venta[1];
                                if (isset($informe) && count($informe) > 0) {
                                    if ($anio == $anio_venta) {
                                        $notaCredito = Ct_Nota_Credito_Clientes::where(
                                            'id_factura',
                                            $info->id
                                        )->first();

                                        $subtotalIndividual =
                                            (float) $info->subtotal_0 +
                                            (float) $info->subtotal_12 -
                                            (float) $info->descuento;

                                        $n_creditoFactura = 0.00;

                                        if (!is_null($notaCredito)) {
                                            $n_creditoFactura = (float) $notaCredito->total_credito;
                                        }
                                        $ventas_netas = $subtotalIndividual - $n_creditoFactura;
                                        if ((int) $info->estado === 1) {
                                            $subtotal += (float) $info->subtotal_total;
                                            $subtotal0 += (float) $info->subtotal_0;
                                            $subtotal12 += (float) $info->subtotal_12;
                                            $descuento += (float) $info->descuento;
                                            $impuesto += (float) $info->impuesto;
                                            $totales += (float) $info->total_final;
                                            $total_mes += (float) $info->total_final;
                                            $subtotalTotal += $subtotalIndividual;
                                            $final_credito += $n_creditoFactura;
                                            $total_ventas_netas += $ventas_netas;
                                        }

                                        $sheet->cell('A' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->fecha);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('B' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->nro_comprobante);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('C' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->ruc_id_cliente);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('D' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->nombre_cliente);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('E' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->tipo);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('F' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue($info->concepto);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('G' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($info->subtotal_0, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('H' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($info->subtotal_12, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('I' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($info->descuento, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('J' . $i, function ($cell) use ($subtotalIndividual) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($subtotalIndividual, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('K' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($info->impuesto, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('L' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($info->total_final, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('M' . $i, function ($cell) use ($n_creditoFactura) {
                                            $cell->setValue(
                                                number_format($n_creditoFactura, 2, '.', ',')
                                            );

                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('N' . $i, function ($cell) use ($ventas_netas) {
                                            // manipulate the cel
                                            $cell->setValue(number_format($ventas_netas, 2, '.', ','));
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $sheet->cell('O' . $i, function ($cell) use ($info) {
                                            // manipulate the cel
                                            if ($info->estado == 1) {
                                                $cell->setValue('Activo');
                                            } else {
                                                $cell->setValue('Inactivo');
                                            }
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $i++;
                                    }
                                }
                            }
                            $total_anio = $total_anio + $total_mes;

                            $sheet->mergeCells('A' . $i . ':' . 'F' . $i);
                            $sheet->cell('A' . $i, function ($cell) use ($fecha_periodo, $total_mes) {
                                // manipulate the cel
                                $mes = '';
                                switch ($fecha_periodo[1]) {
                                    case '01':
                                        $mes = 'Total Enero: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '02':
                                        $mes = 'Total Febrero: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '03':
                                        $mes = 'Total Marzo: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '04':
                                        $mes = 'Total Abril: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '05':
                                        $mes = 'Total Mayo: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '06':
                                        $mes = 'Total Junio: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '07':
                                        $mes = 'Total Julio: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '08':
                                        $mes = 'Total Agosto: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '09':
                                        $mes = 'Total Septiembre: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '10':
                                        $mes = 'Total Octubre: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    case '11':
                                        $mes = 'Total Noviembre: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                    default:
                                        $mes = 'Total Diciembre: $ ' . number_format($total_mes, 2, '.', ',');
                                        break;
                                }
                                $cell->setValue($mes);
                                $cell->setAlignment('left');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('G' . $i, function ($cell) use ($subtotal0) {
                                // manipulate the cel
                                $cell->setValue(number_format($subtotal0, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('H' . $i, function ($cell) use ($subtotal12) {
                                // manipulate the cel
                                $cell->setValue(number_format($subtotal12, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('I' . $i, function ($cell) use ($descuento) {
                                // manipulate the cel
                                $cell->setValue(number_format($descuento, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('J' . $i, function ($cell) use ($subtotalTotal) {
                                // manipulate the cel
                                $cell->setValue(number_format($subtotalTotal, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('K' . $i, function ($cell) use ($impuesto) {
                                // manipulate the cel
                                $cell->setValue(number_format($impuesto, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('L' . $i, function ($cell) use ($totales) {
                                // manipulate the cel
                                $cell->setValue(number_format($totales, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('M' . $i, function ($cell) use ($final_credito) {
                                // manipulate the cel
                                $cell->setValue(number_format($final_credito, 2, '.', ','));
                                $cell->setAlignment('right');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('N' . $i, function ($cell) use ($total_ventas_netas) {
                                // manipulate the cel
                                $cell->setValue(number_format($total_ventas_netas, 2, '.', ','));
                                $cell->setAlignment('rigth');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setAlignment('rigth');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $i++;
                        }
                        $sheet->mergeCells('A' . $i . ':' . 'O' . $i);
                        $sheet->cell('A' . $i, function ($cell) use ($total_anio) {
                            // manipulate the cel
                            $cell->setValue('Total : $' . number_format($total_anio, 2, '.', ','));
                            $cell->setAlignment('left');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    });
                })->export('xlsx');
            }
        } catch (\Exception $ex) {
            echo '<pre>' . $ex->getMessage() . ' Line: ' . $ex->getLine();
        }
    }
    public function index2(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $ventas = Ct_ventas::where('estado', '<', 2)
            ->whereNotNull('nro_comprobante')
            ->where('id_empresa', $id_empresa)
            ->where('tipo', "VENFA-CO")
            ->orderby('id', 'desc')->paginate(10);
        return view('contable/ventas/index2', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    public function index2_buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id' => $request['id'],
            'numero' => $request['numero'],
            'id_asiento' => $request['asiento'],
            'fecha' => $request['fecha'],
            'nombre_cliente' => $request['nombre_cliente'],
            'nombres_paciente' => $request['nombres_paciente'],
        ];
        $fields = array_keys($constraints);
        $ventas = array();
        for ($i = 0; $i < count($constraints); $i++) {
            if (!is_null($constraints[$fields[$i]])) {
                $ventas = Ct_ventas::where($fields[$i], "LIKE", "%" . $constraints[$fields[$i]] . "%")->where('id_empresa', $id_empresa)->where('tipo', "VENFA-CO")->paginate(10);
                break;
            }
        }
        if (count($ventas) <= 0) {
            $ventas = Ct_ventas::where('estado', '<', 2)
                ->where('id_empresa', $id_empresa)
                ->where('tipo', "VENFA-CO")
                ->orderby('id', 'desc')->paginate(10);
        }

        return view('contable/ventas/index2', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    /*******************************************
     **********ANULAMOS LA FACTURA VENTA*********
    /*******************************************/
    public function anular_factura($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        DB::beginTransaction();
        try {
            //Obtenemos la fecha de Hoy
            $fechahoy = Date('Y-m-d H:i:s');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $concepto = $request['concepto'];
            $estado_ventas = Ct_ventas::where('id', $id)->where('estado', '<>', 0)->first();
            if (!empty($estado_ventas)) {
                $act_estado = [
                    'estado' => '0',
                    'id_usuariomod' => $idusuario,
                ];
                $id_empresa = $request->session()->get('id_empresa');
                $registro_venta = Ct_ventas::findorfail($id);
                $data['id'] = $id;
                $data['tipo'] = 'VEN-FA';
                $msj = Ct_Kardex::anular_kardex($data);
                Ct_ventas::where('id', $id)->update($act_estado);
                $ordenes = Ct_Ven_Orden::where('id_referencia', $id)->get();
                if (!is_null($ordenes)) {
                    foreach ($ordenes as $v) {
                        $vs = Ct_Ven_Orden::find($v->id);
                        $act_estadox = [
                            'estado_pago' => '0',
                            'id_usuariomod' => $idusuario,
                        ];
                        $vs->update($act_estadox);
                    }
                }
                //INVERSO
                //Lo que esta en el Haber va al Debe
                //Lo que esta en el Debe va al Haber

                $consulta_cabecera_ctventas = Ct_Asientos_Cabecera::where('estado', '1')
                    ->where('id', $registro_venta->id_asiento)->first();

                $text = 'Fact #' . ':' . $registro_venta->nro_comprobante . '-' . $registro_venta->procedimientos . '-' . 'ANULACION FACTURA';

                $input_cabecera = [

                    'fecha_asiento' => $consulta_cabecera_ctventas->fecha_asiento,
                    'fact_numero' => $consulta_cabecera_ctventas->fact_numero,
                    'id_empresa' => $registro_venta->id_empresa,
                    'observacion' => $concepto . " " . $text,
                    'valor' => $consulta_cabecera_ctventas->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_ctventas->id)->get();

                if ($consulta_detalle != '[]') {

                    foreach ($consulta_detalle as $value) {

                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cab,
                            'id_plan_cuenta' => $value->id_plan_cuenta,
                            'descripcion' => $value->descripcion,
                            'fecha' => $consulta_cabecera_ctventas->fecha_asiento,
                            'haber' => $value->debe,
                            'debe' => $value->haber,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,

                        ]);
                    }
                }
                LogAsiento::anulacion("VEN-FA", $id_asiento_cab, $consulta_cabecera_ctventas->id);
                // $kardex = Venta_Kardex::where('id_venta', $id)->where('estado', 1)->first();
                // if (!is_null($kardex)) {
                //     InvCabMovimientos::anularTransaccionBodega($kardex->id_cab_moviento);
                //     $kardex->estado = 0;
                //     $kardex->id_usuariomod = $idusuario;
                //     $kardex->ip_modificacion = $ip_cliente;
                //     $kardex->save();
                // }
                $kardex = Venta_Kardex::where('id_venta', $id)->where('estado', 1)->groupBy('id_cab_moviento')->select(DB::raw('id_cab_moviento'))->get();
                if (count($kardex) > 0) {
                    foreach ($kardex as $item) {
                        InvCabMovimientos::anularTransaccionBodega($item->id_cab_moviento);
                        $actualizar = Venta_Kardex::where('estado', 1)->where('id_cab_moviento', $item->id_cab_moviento)->update(['estado' => 0, 'id_usuariomod' => $idusuario, 'ip_modificacion' => $ip_cliente]);
                    }
                    $asientoCosto = Log_Venta_Costo::where("id_venta", $id)->where("estado", 1)->first();
                    if (!is_null($asientoCosto)) {
                        $asientoCostoAnt = Ct_Asientos_Cabecera::find($asientoCosto->id_asiento);
                        $anuladoAsCosto = LogAsiento::anularAsiento($asientoCosto->id_asiento, "", "");
                        if ($anuladoAsCosto["status"] == "error") {
                            DB::rollback();
                            return $anuladoAsCosto;
                        }
                        $asientoCosto->estado = 0;
                        $asientoCosto->id_usuariomod = $idusuario;
                        $asientoCosto->ip_modificacion = $ip_cliente;
                        $asientoCosto->save();
                    }
                }
                DB::commit();
                return ["status" => "success", "msj" => "Registro Anulado"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine()];
        }
    }
    public function crearComprobante($nfactura, Request $request, $array_pagos, $factura_id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $contador_ctv = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {
                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
                //dd($max_id);
                $secuencia = intval($max_id->secuencia);
                //dd($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu = $secuencia + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {
            $total_pagos = $request['valor_totalPagos'];
            $input_cabecera = [
                'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura,
                'fecha_asiento' => $request['fecha_asiento'],
                'fact_numero' => $nfactura,
                'valor' => $objeto_validar->set_round($total_pagos),
                'id_empresa' => $id_empresa,
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta' => $confg,
                'descripcion' => $desc_cuenta->nombre,
                'fecha' => $request['fecha_asiento'],
                'haber' => $objeto_validar->set_round($total_pagos),
                'debe' => '0',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones' => 'COMPROBANTE DE INGRESO FACT: ' . $nfactura,
                'estado' => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha' => $request['fecha_asiento'],
                'secuencia' => $nfactura,
                'divisas' => '1',
                'id_empresa' => $id_empresa,
                'total_ingreso' => $objeto_validar->set_round($total_pagos),
                'id_cliente' => $request['identificacion_cliente'],
                'autollenar' => "Cancela FV : " . $nfactura,
                'id_vendedor' => $request['cedula_vendedor'],
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                if ($valor['fecha_pago'] != "") {
                }
                $val = 0;
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : $request['fecha_asiento'];
                $arraydetallepago = [
                    'id_comprobante' => $id_comprobante,
                    'fecha' => $fecha_pago,
                    'numero' => $valor['numero_pago'],
                    'id_banco' => $valor['id_banco_pago'],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'],
                    'id_tipo' => $valor['id_tip_pago'],
                    'total' => $val,
                    'cuenta' => $valor['id_cuenta_pago'],
                    'girador' => $valor['giradoa'],
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                //echo '<pre>';print_r($arraydetallepago);exit;
                Ct_Detalle_Pago_Ingreso::insert($arraydetallepago);
            }

            $confg = LogConfig::busqueda('1.01.01.1.01');

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta' => $confg,
                'descripcion' => $desc_cuenta->nombre,
                'fecha' => $request['fecha_asiento'],
                'debe' => $total_pagos, //$nuevo_saldof,
                'haber' => '0',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            $consulta_venta = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante' => $id_comprobante,
                    'fecha' => $request['fecha_asiento'],
                    'observaciones' => "Cancela FV : " . $nfactura,
                    'id_factura' => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura' => $request['total1'], //$request['saldo_a'.$i],
                    'total' => $total_pagos, //$request['abono_a'.$i],
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
            if ($total_pagos < $request['total1']) {
                //aqui creo una orden de venta nueva

            }
            $valor_actual = Ct_ventas::where('id', $factura_id)->first();
            $id__ventas = [

                'valor_contable' => floatVal($valor_actual->valor_contable) - $total_pagos,
                'estado_pago' => 2,
            ];

            Ct_ventas::where('id', $factura_id)->update($id__ventas);
            $consulta_venta = null;
            $input_actualiza = null;

            return $id__ventas;
        } else {
            return 0;
        }
    }
    /**********************************************
     ****CREAR FACTURAS DE VENTA DESDE LA AGENDA****
    /**********************************************/
    public function crear($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id);
        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
        $agenda = Agenda::findorfail($procedimiento->historia->id_agenda);
        $paciente = Paciente::findorfail($agenda->id_paciente);
        $ct_cliente = ct_clientes::where('identificacion', $paciente->id_usuario)->first();
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $bodega = bodega::where('estado', '1')->get();
        //$tipo_pago = Ct_Forma_Pago::where('estado', '1')->get();
        $seguros = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empresa_general = Empresa::all();
        $empresa_sucurs = Empresa::findorfail($agenda->id_empresa);
        //Obtenemos los % de Retenciones al Iva y la Fuente
        $rete_iva = Ct_Porcentajes_Retencion_Iva::where('estado', '1')->get();
        $rete_fuente = Ct_Porcentajes_Retencion_Fuente::where('estado', '1')->get();
        //Obtenemos el Tipo de Pago
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        //Obtenemos el Listado de bancos
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva = Ct_Configuraciones::where('id_plan', $id_plan_confg)->where('estado', '1')->first();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        return view('contable/ventas/create', ['clientes' => $clientes, 'bodega' => $bodega, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'divisas' => $divisas, 'procedimiento' => $procedimiento, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'id_cliente' => $paciente->id_usuario, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'empresa_general' => $empresa_general, 'empresa_sucurs' => $empresa_sucurs, 'rete_iva' => $rete_iva, 'rete_fuente' => $rete_fuente, 'cuentas' => $cuentas, 'sucursales' => $sucursales, 'productos' => $productos, 'iva' => $iva]);
    }
    /*************************************
     ****CREAR FACTURAS DE VENTA MANUAL****
    /*************************************/
    public function crear_factura(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $idusuario = Auth::user()->id;
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        //$seguros         = Seguro::all();
        //Obtenemos los seguros Validos
        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $usuariosVendedores = '';
        $idVendedores = [];
        try {
            $usuariosVendedores = DB::table('roles_usuario')->get();
            if (count($usuariosVendedores) > 0) {
                $count = 0;
                foreach ($usuariosVendedores as $row) {
                    if (in_array('17', json_decode($row->tipo_usuario), true)) {
                        $idVendedores[$count] = $row->id_usuario;
                    }
                    $count++;
                }
            }
        } catch (Exception $e) {
            $usuariosVendedores = [];
        }
        if (count($usuariosVendedores) > 0) {
            $user_vendedor = User::whereIn('id', $idVendedores)
                ->where('estado', 1)
                ->get();
        } else
            $user_vendedor = User::where('id_tipo_usuario', 17)
                ->where('estado', 1)
                ->get();
        $id_empresa = $request->session()->get('id_empresa');
        $bodega = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        $confg = LogConfig::busqueda('4.1.01.02');
        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        //$iva = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        /********************PROFORMasAS**********************/
        $proformas = Proforma_Venta_Cab::where('estado', 1)->where('id_empresa', $id_empresa)->where('tipo', 1)->where('id_venta', null)->get();
        /********************FIN PROFORMAS**********************/
        $entregaMercaderia = null;
        if ($request->has('entrega_mercaderia') && trim((string) $request->input('entrega_mercaderia')) !== '') {
            $entregaMercaderia = EntregaMercaderia::with(['cliente', 'detalles'])
                ->where('id_empresa', $id_empresa)
                ->whereNull('id_venta')
                ->where('estado', 'ENTREGADA')
                ->findOrFail($request->entrega_mercaderia);
        }
        return view('contable/ventas/create_factura', ['porIva' => $this->porIva, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas, 'tipo_tarjeta' => $tipo_tarjeta,  "proformas" => $proformas, 'entregaMercaderia' => $entregaMercaderia]);
    }
    /*************************************
     ****CREAR Orden DE VENTA MANUAL****
    /*************************************/
    public function crear_orden(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $bodega = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //dd($bodega);
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $confg = LogConfig::busqueda('4.1.01.02');
        //$iva       = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        return view('contable/ventas/create_orden', ['porIva' => $this->porIva, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas]);
    }
    public function precios(Request $request)
    {

        $producto_id = $request->id;
        $id_seguro   = $request->id_seguro;
        $id_nivel    = $request->id_nivel;

        $precios = PrecioProducto::where('codigo_producto', $producto_id)
            ->where('estado', 1)->get();

        return $precios;

        $inf_prod_tar = Ct_Productos_Tarifario::where('id_producto', $producto_id)
            ->where('id_seguro', $id_seguro)
            ->where('nivel', $id_nivel)
            ->where('estado', 1)->first();

        return $inf_prod_tar;
    }
    /*************************************
     ****CREAR FACTURAS DE VENTA MANUAL****
    /*************************************/
    public function insumos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros = Seguro::where('inactivo', '1')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $confg = LogConfig::busqueda('4.1.01.02');
        //$iva = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        //$searchinsumos= $this->searchInsumos($request);
        $primeros_datos = "";
        $segundos_datos = "";
        $fecha_desde = "";
        $tipox = 0;
        $fecha_hasta = "";
        //dd($searchinsumos);
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $bodegas      = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        return view('contable/ventas/insumos', ['porIva' => $this->porIva, 'bodegas' => $bodegas, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas, 'primeros_datos' => $primeros_datos, 'segundos_datos' => $segundos_datos, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipo_tarjeta' => $tipo_tarjeta, 'tipox' => $tipox]);
    }
    public function searchInsumos(Request $request)
    {
        //$finicio, $ffin, $seguro){ ahora tb el tipo
        $fecha = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $personal = [];
        $total = [];
        $id_empresa = $request->session()->get('id_empresa');
        $paciente = $request->identificacion_paciente;
        $ordenes = "";
        $secure = $request->id_seguro;
        $sss = [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'];
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $tipox = 1;
        if ($request->tipo_servicio == 2) {
            $ordenes = Ct_Ven_Orden::where('estado', '1')
                ->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '2');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
            //dd($ordenes);
            $tipox = 2;
        } else if ($request->tipo_servicio == 1) {
            $tipox = 1;
            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '1');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($request->tipo_servicio == 3) {
            $tipox = 3;
            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('id_empresa', $id_empresa)->where('estado_pago', '<>', '1');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])->orderBy('fecha_procedimiento', 'desc')->get();
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        // $iva            = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $confg = LogConfig::busqueda('4.1.01.02');
        //$iva = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        $searchinsumos = "";
        $primeros_datos = "";
        $segundos_datos = "";
        //dd($productos);
        $fecha_desde = $fecha;
        $fecha_hasta = $request->fecha_hasta;
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $secure = $request->id_seguro;
        $bodegas = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        return view('contable/ventas/insumos', ['porIva' => $this->porIva, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'tipo_tarjeta' => $tipo_tarjeta, 'cuentas' => $cuentas, 'searchinsumos' => $searchinsumos, 'ordenes' => $ordenes, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipox' => $tipox, 'secure' => $secure, 'bodegas' => $bodegas]);
    }
    public function modalDetalle($id, Request $request)
    {
        //dd($request->all());
        $ven_orden = Ct_Ven_Orden::find($request->id);

        $venta_orden = Ct_Orden_Venta::find($ven_orden->orden_venta);
        $agenda = $venta_orden->agenda;
        $ex_orden_agenda = Examen_Orden_Agenda::where('id_agenda', $agenda->id)->first();
        $ex_orden = Examen_Orden::find($ex_orden_agenda->id_orden);
        $ex_detalle = $ex_orden->detalles;
        // dd($ex_detalle);

        return view('contable/ventas/modal_detalles', ['examen_detalle' => $ex_detalle, 'id' => $request->id]);
    }
    public function selectsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos = [];
        if ($request['search'] != null) {
            $productos = Ct_productos::where('id_empresa', $id_empresa)->where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('ct_productos.codigo as id', 'ct_productos.nombre as text')->get();
        }

        return response()->json($productos);
    }
    public function clientesearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $clientes  = [];
        if ($request['search'] != null) {
            if ($request['type'] == 0) {
                $clientes = Ct_Clientes::where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.nombre', 'ct_clientes.telefono1_representante', 'ct_clientes.email_representante', 'ct_clientes.ciudad_representante', 'ct_clientes.direccion_representante', 'ct_clientes.identificacion as id', 'ct_clientes.nombre as text')->get();
            } else
                $clientes = Ct_Clientes::where('identificacion', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.nombre', 'ct_clientes.telefono1_representante', 'ct_clientes.email_representante', 'ct_clientes.ciudad_representante', 'ct_clientes.direccion_representante', 'ct_clientes.identificacion as id', 'ct_clientes.identificacion as text')->get();
        }
        return response()->json($clientes);
    }
    public function cedulasearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cedula_cliente  = [];
        if ($request['search'] != null) {
            $cedula_cliente = Ct_Clientes::where('identificacion', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.identificacion as id', 'ct_clientes.identificacion as text')->get();
        }

        return response()->json($cedula_cliente);
    }
    public function productosearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos = [];
        if ($request['search'] != null) {
            $productos = Ct_productos::where('nombre', 'LIKE', '%' . $request['search'] . '%')
                ->where('estado_tabla', '1')
                ->where('id_empresa', $id_empresa)
                ->select('ct_productos.codigo as id', 'ct_productos.nombre as text')->get();
        }

        return response()->json($productos);
    }
    public function buscar_producto_codigo(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        //$buscador = $request['search'];
        $buscador = trim($request->q);
        $productos  = [];

        if ($buscador != null) {
            $productos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', '1');

            $productos = $productos->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('codigo', "LIKE", "%{$buscador}%")
                    ->orwhere('nombre', 'LIKE', "%{$buscador}%");
            });

            $productos = $productos->select('codigo as id', DB::raw('CONCAT(codigo," | ",nombre) as text'))->get();
        }

        //dd($productos);

        return response()->json($productos);
    }
    public function paginate($items, $perPage = 5, $page = null, $baseUrl = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $lap = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
    public function previewData(Request $request)
    {
        //$finicio, $ffin, $seguro){

        $fecha       = date("Y/m/d", strtotime($request->finicio));
        $fecha_hasta = date("Y/m/d", strtotime($request->ffin));

        $consultas = DB::table('hc_evolucion as he')
            ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
            ->join('users as hd', 'hd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as p', 'p.id', 'h.id_paciente')
            ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'a.id')
            ->leftjoin('ct_ventas as v', 'v.orden_venta', 'ov.id')
            ->leftjoin('ct_ven_orden as vo', 'vo.id_ct_venta', 'v.id')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
            ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
            ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')
            ->select('he.*', 'ov.*', 'v.id as asd', 'v.seguro_paciente', 'vo.total_final as valor_final', 'vo.id as idventa', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');

        if ($fecha != null) {
            $consultas = $consultas->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $consultas = $consultas->orderBy('a.fechaini', 'desc');
        }
        $consultas = $consultas->where('a.espid', '<>', '10');
        $consultas = $consultas->where('v.seguro_paciente', $request->seguro);

        $consultas = $consultas->whereNotIn('he.hc_id_procedimiento', function ($q) {
            $q->select('id_hc_procedimientos')->from('ct_factura_procedimientos');
        })->get();

        return $consultas;
    }
    public function excelPreview($fecha, $fecha_hasta, $seguro, $tipo, $id_empresa, $secuencia = "001-00022-002")
    {

        $date  = new DateTime();
        $fecha = date('Y-m-d', ($fecha / 1000));

        $fecha_hasta = date('Y-m-d', ($fecha_hasta / 1000));
        $ordenes     = "";
        if ($tipo == 2) {
            $ordenes = Ct_Ven_Orden::where('estado', '1')
                ->where('seguro_paciente', $seguro)
                ->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '2')
                ->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 23:59:59')])
                ->orderBy('fecha', 'desc')
                ->get();
            $tipox = 2;
        } else if ($tipo == 1) {
            $tipox = 1;

            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('seguro_paciente', $seguro)->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '1')
                ->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 00:00:00')])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($tipo == 3) {
            $tipox   = 3;
            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('seguro_paciente', $seguro)->where('id_empresa', $id_empresa)->where('estado_pago', '<>', '1')->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 00:00:00')])->orderBy('fecha_procedimiento', 'desc')->get();
        }

        //array_push($personal, $consultas);
        //dd($ordenes);

        Excel::create('Consultas Pre-Factura Conglomerada', function ($excel) use ($fecha, $fecha_hasta, $ordenes, $secuencia) {
            $excel->sheet('Factura por confirmar ', function ($sheet) use ($ordenes, $fecha, $fecha_hasta, $secuencia) {
                $sheet->mergeCells('A1' . ':D1');
                $contador = 1;
                $sheet->cell('A1', function ($cell) use ($fecha_hasta, $fecha, $secuencia) {
                    // manipulate the cel
                    $cell->setValue('FACTURA ' . $secuencia);
                    $cell->setFontWeight('bold');
                });
                $contador++;
                $sheet->cell('A' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TITULAR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLAN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $contador = 3;
                $sumatot  = 0;
                foreach ($ordenes as $x) {

                    $sumatot += $x->copago;
                    if (!is_null($x)) {
                        $sheet->cell('A' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            /*$id_x= $x->id_ct_venta;
                            $consult= Ct_ventas::where('id',$id_x)->where('estado','!=','0')->first();
                            if(!is_null($consult)){
                            if(!is_null($consult->orden_venta)){
                            $cell->setValue($consult->orden_venta->numero_oda);
                            }
                            }
                             */
                            $cell->setValue($x->id);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue(substr($x->fecha_procedimiento, 0, 10));

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->nombres_paciente);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->nombre_cliente);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->estado_pago);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $total = $x->copago;
                            $cell->setValue(number_format($total, 2, '.', ','));

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $contador++;
                }
                $contador++;
                $sheet->cell('D' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F' . $contador, function ($cell) use ($sumatot) {
                    // manipulate the cel
                    $cell->setValue(number_format($sumatot, 2, '.', ','));
                });
            });
        })->export('xlsx');
        //return $request;

    }
    public function validarStock(Request $request)
    {
        $producto = $request->producto;
        $bodega   = $request->bodega;

        //$query = Ct_Inventario::where('bodega_id',$bodega)->where('producto_id', $producto)->first();
        $query = DB::table('ct_productos as p')
            ->join('ct_inventario as i', 'i.producto_id', 'p.id')
            ->where('i.bodega_id', $bodega)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'p.id')->where('p.codigo', $producto)
            ->select('*')
            ->get();
        return $query;
    }
    public function esInventariable(Request $request)
    {
        $producto = $request->producto;

        //$query = Ct_Productos_Insumos::where('id_producto',$producto)->count();

        $query = DB::table('ct_productos as p')
            ->where('p.codigo', $producto)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'p.id')
            ->count();
        return $query;
    }
    public function excel($id, Request $request)
    {
        $factura   = $id;
        $consultas = DB::table('ct_factura_procedimientos as fp')
            ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'fp.id_hc_procedimientos')
            ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
            ->join('users as hd', 'hd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as p', 'p.id', 'h.id_paciente')
            ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', '=', 'h.id_agenda')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
            ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
            ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')

            //        ->select('fp.*','he.*','ov.*','p.apellido1','p.apellido2','p.nombre1','p.nombre2','p.id as id_paciente','pc.nombre_general','a.fechaini','p.id as id_paciente','hp.id_seguro','hp.id_doctor_examinador','hs.nombre as hsnombre','hu.apellido1 as huapellido','hu.nombre1 as hunombre','pc.nombre_general','hd.apellido1 as hdapellido','hd.nombre1 as hdnombre','a.proc_consul')->where('pc.id','40')->get();
            ->select('ov.*', 'a.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->get();

        $consultas = $consultas->where('a.espid', '<>', '10');
        // dd($consultas);
        Excel::create('Reporte Consultas Factura -' . $factura, function ($excel) use ($factura) {

            $consultas = DB::table('ct_factura_procedimientos as fp')
                ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'fp.id_hc_procedimientos')
                ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
                ->join('users as hd', 'hd.id', 'h.id_doctor1')
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->join('paciente as p', 'p.id', 'h.id_paciente')
                ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
                ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'a.id')
                ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
                ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')
                ->select('fp.*', 'he.*', 'ov.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');
            $consultas = $consultas->where('fp.id_ct_ventas', $factura);
            $consultas = $consultas->where('a.espid', '<>', '10')->get();

            $excel->sheet('Llamadas por confirmar', function ($sheet) use ($consultas) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                });
                $contador = 2;
                foreach ($consultas as $value) {
                    $sheet->cell('A' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->numero_oda);
                    });
                    $sheet->cell('B' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fechaini);
                    });
                    $sheet->cell('C' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $nombre = "$value->apellido1 $value->apellido2 $value->nombre1 $value->apellido2";
                        $cell->setValue($value->id_paciente);
                    });
                    $sheet->cell('D' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $nombre = "$value->apellido1 $value->apellido2 $value->nombre1 $value->apellido2";
                        $cell->setValue($nombre);
                    });
                    $sheet->cell('E' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total);
                    });
                    $contador++;
                }
            });
        })->export('xlsx');
    }
    /*************************************
     ****CARGA PRODUCTOS DESDE LA AGENDA***
    /*************************************/
    public function buscar_insumos_mov(Request $request)
    {

        $id_proced = $request['id_hc_proced'];
        $data      = null;

        if (!is_null($id_proced)) {

            $insumos = DB::table('movimiento_paciente as mp')
                ->where('mp.id_hc_procedimientos', $id_proced)
                ->join('movimiento as m', 'm.id', 'mp.id_movimiento')
                ->join('producto as p', 'p.id', 'm.id_producto')
                ->groupBy('m.serie')
                ->select('p.codigo', DB::raw('count(*) as  total'), 'p.nombre', 'm.id_bodega', 'p.iva', 'm.precio as cost_vent')
                ->get();
        }

        if ($insumos != '[]') {

            $data = [$insumos];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    /*******************************************
     ***OBTENEMOS SECUENCIA DE LA FACTURA VENTA**
    /*******************************************/
    public function obtener_numero_factura($idempresa, $sucursal, $punto_emision)
    {
        $contador_ctv   = Ct_ventas::where('id_empresa', $idempresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->get()->count();
        $numero_factura = 0;
        if ($contador_ctv == 0) {
            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {
            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = Ct_ventas::where('id_empresa', $idempresa)
                ->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)
                ->where('estado', '!=', 0)
                ->latest()
                ->first();
            $max_id = intval($max_id->numero);
            //dd(strlen($max_id));
            if (strlen($max_id) < 9) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
    private static function generarNumeroFactura($data)
    {
        $num = De_Info_Tributaria::where('id_empresa', session('id_empresa'))
            ->where('cod_sucursal', $data['sucursal'])
            ->where('cod_caja', $data['punto_emision'])
            ->where('id_maestro_documentos', 1)
            ->first(['secuencial_nro']);
        if ($num == '') {
            $num = Ct_ventas::where('sucursal', $data['sucursal'])
                ->where('id_empresa', session('id_empresa'))
                ->where('punto_emision', $data['punto_emision'])
                ->where('tipo', 'VEN-FA')
                ->max('numero');
        } else {
            $num = $num->secuencial_nro;
        }
        if ($num == '')
            $num = $data['sucursal'] . '-' . $data['punto_emision'] . '-' . '000000001';
        else {
            $num = $num + 1;
            $num = $data['sucursal'] . '-' . $data['punto_emision'] . '-' . str_pad($num, 9, 0, STR_PAD_LEFT);
        }
        return $num;
    }
    public static function ventasStore($request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $ct_clientes = Ct_Clientes::find($request->identificacion_cliente);
        $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $c_sucursal = $cod_sucurs->codigo_sucursal;
        $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $empresa = Empresa::find($request['id_empresa']);
        $c_caja = $cod_caj->codigo_caja;
        $factura_venta = [
            'sucursal' => $c_sucursal,
            'punto_emision' => $c_caja,
            'nro_comprobante' => "",
            'id_empresa' => $request['id_empresa'],
            'tipo' => $request['tipo'],
            'fecha' => $request['fecha_asiento'],
            'divisas' => $request['divisas'],
            'nombre_cliente' => $request['razon_social_edit'],
            'tipo_consulta' => $request['tipo_consulta'],
            'id_cliente' => $request['identificacion_cliente'],
            'direccion_cliente' => $request['direccion_cliente'],
            'ruc_id_cliente' => $request['identificacion_cliente'],
            'telefono_cliente' => $request['telefono_cliente'],
            'email_cliente' => $request['mail_cliente'],
            'orden_venta' => $request['orden_venta'],
            'nro_autorizacion' => $request['numero_autorizacion'],
            'id_paciente' => $request['identificacion_paciente'],
            'nombres_paciente' => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente' => $request['id_seguro'],
            'procedimientos' => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],
            'concepto' => $request['concepto'],
            'copago' => $request['totalc'],
            'id_recaudador' => $request['cedula_recaudador'],
            'ci_vendedor' => $request['cedula_vendedor'],
            'vendedor' => $request['vendedor'],
            'electronica' => $request['electronica'],
            'subtotal_0' => $request['subtotal_01'],
            'subtotal_12' => $request['subtotal_121'],
            'descuento' => $request['descuento1'],
            'base_imponible' => $request['subtotal_121'],
            'impuesto' => $request['tarifa_iva1'],
            'total_final' => $request['total1'],
            'valor_contable' => $request['total1'],
            'dias_plazo' => $request->diasPlazo,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'nota_array' => $request['nota_electronica'] != "" ?  json_encode($request['nota_electronica']) : json_encode([]),
            'doc_electronico' => 0,
            'emisor' => 'MD',
            'contraentrega' => $request['contraentrega'] != '' ? $request['contraentrega'] : 0,
        ];
        /*if(Auth::user()->id=='0921605895')
        {
            echo '<pre>',print_r($factura_venta);DB::rollBack();exit;
        }*/
        $data['empresa'] = $request->empresa ?? $request->id_empresa;
        $getType = "5";
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $cliente['cedula']   = $ct_clientes->identificacion;
        $cliente['tipo']     = $getType;
        $cliente['nombre']   = $ct_clientes->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $ct_clientes->nombre);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = trim($cliente['apellido'] . ' ' . $explode[$i]);
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        $cliente['email']     = $factura_venta["email_cliente"];
        $cliente['telefono']  = $factura_venta["telefono_cliente"];
        $direccion['calle']   = $factura_venta["direccion_cliente"];
        $direccion['ciudad']  = $ct_clientes->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        $otros = [];
        if ($request->codigo_publico != '' || $request->descripcion_publico != '') {
            $otros = [
                'codigo_publico' => $request->codigo_publico[$cont],
                'descripcion_publico' => $request->descripcion_publico[$cont]
            ];
        }
        $agenda = null;
        $orden = Ct_Orden_Venta::find($request['orden_venta']);
        if (!is_null($orden)) {
            $agenda = Agenda::find($orden->id_agenda);
        }
        for ($i = 0; $i < count($request->nombre); $i++) {
            $detalle = [
                'id_ct_productos' => $request['codigo'][$i],
                'nombre' => $request['nombre'][$i],
                'descripcion_producto' => $request['nombre'][$i],
                'bodega' => $request['bodega'][$i],
                'cantidad' => $request['cantidad'][$i],
                'precio' => $request['precio'][$i],
                'descuento_porcentaje' => $request['descpor'][$i],
                'descuento' => $request['c'][$i],
                'extendido' => $request['precioneto'][$i],
                'detalle' => $request['detalle'][$i],
                'copago' => $request['copago'][$i],
                'check_iva' => $request['iva'][$i],
                'porcentaje' => $request['ivareal'],
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                //'id_inv_serie'         => $valor["id_inv_serie"],
                'otros' => json_encode($otros)
            ];
            $detalle = trim(preg_replace("/\s+/", " ", $detalle['detalle']));
            $producto['sku'] = $request['codigo'][$i]; //ID EXAMEN
            $producto['nombre'] = $request['nombre'][$i] . ' ' . $request['descrip_prod'][$i]; // NOMBRE DEL EXAMEN
            $producto['cantidad'] = $request['cantidad'][$i];
            $producto['precio'] = $request['precio'][$i]; //DETALLE
            if ($request['copago'][$i] > 0) {
                $producto['precio'] = $request['precioneto'][$i] / $request['cantidad'][$i];
            }
            $pricetot = ($request['cantidad'][$i] * $request['precio'][$i]) - $request['copago'][$i];
            $producto['descuento'] = $request['desc'][$i];
            $producto['subtotal']  = $pricetot - $request['desc'][$i]; //precio-descuento
            $tax = "0";
            if ($request['iva'][$i] == 1) {
                $tax = ($pricetot - $request['desc'][$i]) * $request['ivareal'];
            }
            $producto['tax']    = $tax;
            $producto['total']  = $pricetot - $request['desc'][$i]; //SUBTOTAL
            $producto['copago'] = $request['totalc'];
            $productos[$i]   = $producto;
        }
        $data['productos'] = $productos;
        if ($request['identificacion_paciente'] != null) {
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $request['identificacion_paciente'] . ' ' . $request['nombre_paciente'];
            $info[0] = $info_adicional;
        }

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $request['mail_cliente']; //EMAIL
        $info[2] = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $request->ciudad_cliente; //EMAIL
        $info[3] = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $request->direccion_cliente; //EMAIL
        $info[4] = $info_adicional;
        if (!is_null($orden)) {
            if ($orden->seguro != null) {
                $info_adicional['nombre'] = "SEGURO";
                $info_adicional['valor'] = $orden->seguro->nombre; //SEGURO
                $info[5] = $info_adicional;
            }
        }
        $tipo = $request['id_tip_pago'][0];
        if ($tipo == '1') {
            $pago['forma_pago'] = '01';
        } elseif ($tipo == '2') {
            $pago['forma_pago'] = '20';
        } elseif ($tipo == '3') {
            $pago['forma_pago'] = '20';
        } elseif ($tipo == '4') {
            $pago['forma_pago'] = '19';
        } elseif ($tipo == '5') {
            $pago['forma_pago'] = '20';
        } else {
            $pago['forma_pago'] = '16';
        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo'] = "{$request['diasPlazo']}";
        $data['pago'] = $pago;
        $data['contable'] = 0; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio'] = 0;
        if ($request['identificacion_paciente'] != null) {
            $data['paciente'] = $request['identificacion_paciente'];
        }
        $arr_p = [];
        for ($i = 0; $i < $request['contador_pago']; $i++) {
            $visibilidad_pa = $request['visibilidad_pago' . $i];
            if ($visibilidad_pa == 1) {
                $arr_pagos = [
                    'id_tipo' => $request['id_tip_pago' . $i],
                    'fecha' => $request['fecha_pago' . $i],
                    'tipo_tarjeta' => $request['tipo_tarjeta' . $i],
                    'numero_transaccion' => $request['numero_pago' . $i],
                    'id_banco' => $request['id_banco_pago' . $i],
                    'cuenta' => $request['id_cuenta_pago' . $i],
                    'giradoa' => $request['giradoa' . $i],
                    'valor' => $request['valor' . $i],
                    'valor_base' => $request['valor_base' . $i],
                    'info_banco' => Ct_Bancos::find($request['id_banco_pago' . $i])
                ];
                array_push($arr_p, $arr_pagos);
            }
        }
        $data['formas_pago'] = $arr_p;
        $data['procedimiento'] = $request['procedimiento'];
        $data['fecha_procedimiento'] = $request['fecha_proced'];
        $data['concepto'] = 'Factura Electronica -' . $request['procedimiento'];
        $data['copago_'] = $request['totalc'];
        $data['id_seguro'] = $request['id_seguro']; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura'] = $request['valor_totalPagos'];
        $data['sucursal'] = $c_sucursal;
        $data['punto_emision'] = $c_caja;
        $data['emisor'] = $factura_venta['emisor'];
        $data['orden'] = $orden;
        if (!is_null($agenda)) {
            $data['agenda'] = $agenda;
        }
        $data['idUsuario'] = $idusuario;
        $data['datosPaciente'] = Paciente::find($request['identificacion_paciente']);
        $data['datosUsuario'] = User::find($request['identificacion_paciente']);
        $data['dataCompleta'] = $request->input();
        $data['llevaOrden'] = 'no';
        $envio = ApiFacturacionController::envio_factura($data);
        if (Auth::user()->id == "0952510444") {
            dd($envio);
        }
        /*if (Auth::user()->id == '0921605895') {
            echo '<pre>';
            print_r($envio);
            exit;
        }*/
        if (!is_null($orden)) {
            $orden->update([
                'nro_comprobante' => $envio->comprobante,
                'fecha_envio'     => date('Y-m-d H:i:s'),
            ]);
        }
        //$manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => $empresa->razonsocial,
            //'dato_ant1'   => $orden->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            //'dato_ant4'   => $manage,
        ]);
        // return "ok";
        return ["status" => "success", "comprobante" => $envio->comprobante];
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $fecha_as = $request['fecha_asiento'];
        $id_empresa = $request->id_empresa !== null ? $request->id_empresa : $request->session()->get('id_empresa');
        $llevaOrden = false;
        $numero = $request['numero'];
        $numero1 = null;
        $nfactura = 0;
        $num_comprobante = 0;
        $empresa = Empresa::find($id_empresa);
        //if ($id_empresa !== '0992704152001') {
        //return self::ventasStore($request);
        //}
        $msjk = ", Store: no tiene inventario";
        if ($empresa->inventario == 1) {
            $msjk = ", Store: tiene inventario";
        }
        $cliente = Ct_Clientes::where('identificacion', $request['identificacion_cliente'])->count();
        $id_proforma = $request->proforma;
        DB::beginTransaction();
        $msj = "no";
        try {
            $entregaMercaderia = $this->validarEntregaMercaderia($request, $id_empresa);
            if ($cliente == 0) {
                // cliente
                Ct_Clientes::create([
                    'nombre' => strtoupper($request['nombre_cliente']),
                    'tipo' => '5',
                    'identificacion' => $request['identificacion_cliente'],
                    'clase' => '1',
                    'nombre_representante' => $request['nombre_cliente'],
                    'cedula_representante' => $request['identificacion_cliente'],
                    'ciudad_representante' => $request['ciudad_cliente'],
                    'direccion_representante' => $request['direccion_cliente'],
                    'telefono1_representante' => $request['telefono_cliente'],
                    'email_representante' => $request['mail_cliente'],
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
            if ($request['sucursal'] != 0) {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                $c_caja = $cod_caj->codigo_caja;
                $proced = $request['procedimiento'];
                //Aqui verificar si es electronica
                if ($request['electronica'] == 1) {
                    $nfactura = "";
                    $num_comprobante = "";
                } else if (!is_null($numero)) {
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            } else {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('codigo_sucursal', $empresa->establecimiento)->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj = Ct_Caja::where('estado', '1')->where('codigo_caja', $empresa->punto_emision)->first();
                $c_caja = $cod_caj->codigo_caja;
                $proced = $request['procedimiento'];
                //Aqui verificar si es electronica
                if ($request['electronica'] == 1) {
                    $nfactura = "";
                    $num_comprobante = "";
                } else if (!is_null($numero)) {
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            }
            $pac = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
            $id_paciente = $request['identificacion_paciente'];
            $patient = Paciente::find($id_paciente);
            $pc = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
            //7******GUARDAdo TABLA ASIENTO CABECERA********
            $input_cabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $fecha_as,
                'fact_numero' => $nfactura,
                'id_empresa' => $id_empresa,
                'observacion' => $text . " " . $request['concepto'],
                'valor' => $request['total1'],
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $diasPlazo = "0";
            $nota_electronica = "";
            if ($request->electronica == 1) {
                $diasPlazo = $request->diasPlazo;
                $nota_electronica = isset($request['nota_electronica'][0]) ? $request['nota_electronica'][0] : '';
            }
            if ($num_comprobante == '') {
                $datos = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja
                ];
                $num_comprobante = VentasController::generarNumeroFactura($datos);
            }
            $cliente = Ct_Clientes::where('identificacion', '=', $request['identificacion_cliente'])->first();
            $secuencialserie = explode('-', $num_comprobante);
            $factura_venta = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                //'numero' => isset($secuencialserie[2]) ? $secuencialserie[2] : '',
                'nro_comprobante' => $num_comprobante,
                'id_asiento' => $id_asiento_cabecera,
                'id_empresa' => $id_empresa,
                'tipo' => $request['tipo'],
                'fecha' => $request['fecha_asiento'],
                'divisas' => $request['divisas'],
                'nombre_cliente' => $cliente->nombre,
                'tipo_consulta' => $request['tipo_consulta'],
                'id_cliente' => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente' => $request['direccion_cliente'],
                'ruc_id_cliente' => $request['identificacion_cliente'],
                'telefono_cliente' => $request['telefono_cliente'],
                'email_cliente' => $request['mail_cliente'],
                'orden_venta' => $request['orden_venta'],
                'nro_autorizacion' => $request['numero_autorizacion'],
                'id_paciente' => $pc,
                'nombres_paciente' => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente' => $request['id_seguro'],
                //'id_nivel' => $request['id_nivel'],
                'procedimientos' => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'concepto' => $request['concepto'],
                'copago' => $request['totalc'],
                'id_recaudador' => $request['cedula_recaudador'],
                'ci_vendedor' => $request['cedula_vendedor'],
                'vendedor' => $request['vendedor'],
                'electronica' => $request['electronica'],
                //'nota' => $request['nota'],
                'subtotal_0' => $request['subtotal_01'],
                'subtotal_12' => $request['subtotal_121'],
                //'subtotal' => $request['subtotal1'],
                'descuento' => $request['descuento1'],
                'base_imponible' => $request['subtotal_121'],
                'impuesto' => $request['tarifa_iva1'],
                // 'transporte' => $request['transporte'],
                'total_final' => $request['total1'],
                'valor_contable' => $request['total1'],
                'dias_plazo' => $diasPlazo,
                'nota_electronica' => $nota_electronica,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'nota_array' => $request['nota_electronica'] != "" ?  json_encode($request['nota_electronica']) : json_encode([]),
                'doc_electronico' => $request['electronica'] == 1  ? 0 : null,
                //'doc_electronico' => $c_caja == 100 ? 0 : null,
                'emisor' => 'MD',
                'contraentrega' => $request['contraentrega'] != '' ? $request['contraentrega'] : 0,
            ];
            if (is_null($id_paciente)) {
                $factura_venta = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja,
                    //'numero' => $nfactura,
                    'nro_comprobante' => $num_comprobante,
                    'id_asiento' => $id_asiento_cabecera,
                    'id_empresa' => $id_empresa,
                    'tipo' => $request['tipo'],
                    'fecha' => $request['fecha_asiento'],
                    'divisas' => $request['divisas'],
                    'nombre_cliente' => $cliente->nombre,
                    'tipo_consulta' => $request['tipo_consulta'],
                    'id_cliente' => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente' => $request['direccion_cliente'],
                    'ruc_id_cliente' => $request['identificacion_cliente'],
                    'telefono_cliente' => $request['telefono_cliente'],
                    'email_cliente' => $request['mail_cliente'],
                    'orden_venta' => $request['orden_venta'],
                    'nro_autorizacion' => $request['numero_autorizacion'],
                    'nombres_paciente' => $request['nombre_paciente'],
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente' => $request['id_seguro'],
                    'procedimientos' => $request['procedimiento'],
                    'fecha_procedimiento' => $request['fecha_proced'],
                    'concepto' => $request['concepto'],
                    'copago' => $request['totalc'],
                    'id_recaudador' => $request['cedula_recaudador'],
                    'ci_vendedor' => $request['cedula_vendedor'],
                    'vendedor' => $request['vendedor'],
                    'electronica' => $request['electronica'],
                    'subtotal_0' => $request['subtotal_01'],
                    'subtotal_12' => $request['subtotal_121'],
                    'descuento' => $request['descuento1'],
                    'base_imponible' => $request['subtotal_121'],
                    'impuesto' => $request['tarifa_iva1'],
                    'total_final' => $request['total1'],
                    'valor_contable' => $request['total1'],
                    'dias_plazo' => $diasPlazo,
                    'nota_electronica' => $nota_electronica,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'nota_array' => $request['nota_electronica'] != "" ?  json_encode($request['nota_electronica']) : json_encode([]),
                    'doc_electronico' => $request['electronica'] == 1  ? 0 : null,
                    //'doc_electronico' => $c_caja == 100 ? 0 : null,
                    'emisor' => 'MD',
                    'contraentrega' => $request['contraentrega'] != '' ? $request['contraentrega'] : 0,
                ];
            }
            $id_venta = Ct_ventas::insertGetId($factura_venta);
            //$id_venta = 0;
            $arr_total = [];
            $total_iva = 0;
            $total_impuesto = 0;
            $total_0 = 0;
            for ($i = 0; $i < count($request->input("nombre")); $i++) {
                if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                    // if(Auth::user()->id =="0955728803"){
                    //         dd($request->input("nombre"),$request->input("cantidad"),$request->input("bodega"),$request->input("codigo"),$request->input("precio"),$request->input("descpor"),$request->input("copago"),$request->input("desc"),
                    //         $request->input("precioneto"),$request->input("descrip_prod"),$request->input("iva"),$request->input("bodega"));
                    // }
                    $arr = [
                        'nombre'        => $request->input("nombre")[$i],
                        'cantidad'      => $request->input("cantidad")[$i],
                        'bodega'        => isset($request["bodega"][$i]) ? $request->input("bodega")[$i] : null,
                        'codigo'        => $request->input("codigo")[$i],
                        'precio'        => $request->input("precio")[$i],
                        'descpor'       => $request->input("descpor")[$i],
                        'copago'        => $request->input("copago")[$i],
                        'descuento'     => $request->input("desc")[$i],
                        'precioneto'    => $request->input("precioneto")[$i],
                        'detalle'       => $request->input("descrip_prod")[$i],
                        'iva'           => $request->input("iva")[$i],
                        'id_inv_serie'  => isset($request["id_inv_serie"][$i]) ? $request->input("id_inv_serie")[$i] : null,
                    ];
                    array_push($arr_total, $arr);
                }
            }
            $cont = 0;
            foreach ($arr_total as $valor) {
                if ($valor['copago'] > 0) {
                    //registra orden de venta
                    $llevaOrden = true;
                }
                $otros = [];
                /*if (session('id_empresa') == '0993069299001') {
                    if ($cont == 1) {
                        echo '<pre>';
                        print_r($request->codigo_publico[$cont]);
                        print_r($request->descripcion_publico[$cont]);
                        DB::rollBack();
                        exit;
                    }
                }*/
                if (isset($request->codigo_publico[$cont]) && isset($request->descripcion_publico[$cont])) {
                    $otros = [
                        'codigo_publico' => $request->codigo_publico[$cont],
                        'descripcion_publico' => $request->descripcion_publico[$cont]
                    ];
                }
                /*if (Auth::user()->id == "0921605895") {
                    echo '<pre>';
                    print_r($otros);
                    DB::rollBack();
                    exit;
                }*/
                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'descripcion_producto' => $valor['nombre'],
                    'bodega'               => $valor['bodega'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['precioneto'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['copago'],
                    'check_iva'            => $valor['iva'],
                    'porcentaje'           => $request['ivareal'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'id_inv_serie'         => $valor["id_inv_serie"],
                    'otros' => json_encode($otros)
                ];
                Ct_detalle_venta::create($detalle);
                $cont++;
            }
            //***MODULO CUENTA POR COBRAR***
            //cUENTAS X COBRAR CLIENTES
            $val_tol = $request['total1'];
            if ($val_tol > 0) {
                $id_plan = LogConfig::busqueda('1.01.02.05.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan = LogConfig::busqueda('2.01.07.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan = LogConfig::busqueda('4.1.01.02');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan = LogConfig::busqueda('4.1.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $valor_descuento = $request['descuento1'];
            if ($valor_descuento > 0) {
                $id_plan = LogConfig::busqueda('4.1.06.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'haber'               => '0',
                    'debe'                => $request['descuento1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $variable = $request['contador_pago'];
            //dd($variable);
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad_p = $request['visibilidad_pago' . $i];
                if ($visibilidad_p == 1) {
                    Ct_Forma_Pago::create([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => $request['id_tip_pago' . $i],
                        'fecha'           => $request['fecha_pago' . $i],
                        'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                        'numero'          => $request['numero_pago' . $i],
                        'banco'           => $request['id_banco_pago' . $i],
                        'cuenta'          => $request['id_cuenta_pago' . $i],
                        'giradoa'         => $request['giradoa' . $i],
                        'valor'           => $request['valor' . $i],
                        'valor_base'      => $request['valor_base' . $i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
                // if(Auth::user()->id == '0951561075'){
                //     $prueba_fp = Ct_Forma_Pago::where('id_ct_ventas', $id_venta)->first();
                //     echo "<pre>";
                //     print_r ($prueba_fp);
                //     DB::rollBack();
                //     exit;                   
                // }
            }
            $arr_p     = [];
            $validates = false;
            //dd($variable);
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad_pa = $request['visibilidad_pago' . $i];
                if ($visibilidad_pa == 1) {
                    if ($request['id_tip_pago' . $i] == '7') {
                        $validates = true;
                    }
                    $arr_pagos = [
                        'id_tip_pago'    => $request['id_tip_pago' . $i],
                        'fecha_pago'     => $request['fecha_pago' . $i],
                        'tipo_tarjeta'   => $request['tipo_tarjeta' . $i],
                        'numero_pago'    => $request['numero_pago' . $i],
                        'id_banco_pago'  => $request['id_banco_pago' . $i],
                        'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                        'giradoa'        => $request['giradoa' . $i],
                        'valor'          => $request['valor' . $i],
                        'valor_base'     => $request['valor_base' . $i],
                    ];
                    array_push($arr_p, $arr_pagos);
                }
            }
            //agregar comprobantes de ingreso
            $erf = "";
            // if ($validates == true) {
            // } else {
            //     //$erf = $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            // }
            if ($request['id_venta'] != "") {
                $nuevo_copago = [
                    'copago' => '0',
                ];
                //Ct_ventas::where('id', $request['id_venta'])->update($nuevo_copago);
            }
            if ($llevaOrden) {
                $orden_id     = $this->ordenVenta($request['id_venta'], $request);
                $id_ct_ventas = [
                    'id_ct_venta' => $id_venta,
                ];
                Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
            } else {
                $orden_id = 0;
            }
            $data['id']     = $id_venta;
            $data['tipo']   = 'VEN-FA';
            //2022-09-08
            $msj = $entregaMercaderia ? 'Inventario descargado en la entrega ' . $entregaMercaderia->numero : Ct_Kardex::generar_kardex($data);
            $num_sec_vent   = Ct_ventas::find($id_venta);
            $num_vent       = $num_sec_vent->numero;
            $nfactura       = $num_sec_vent->nro_comprobante;
            $asiento_kardex = $entregaMercaderia
                ? $entregaMercaderia->id_asiento_costo
                : Contable::kardex_asiento($id_venta);
            $kardex["asiento_costo"] = "";
            if (!$entregaMercaderia && ($empresa->egreso_kardex == 1 and $empresa->inventario == 1 or !is_null($id_proforma))) {
                $kardex = NuevoKardexContableController::newgenerarKardex($id_venta);
                if ($kardex["status"] ==  "error" or !$kardex["status"]) {
                    DB::rollBack();
                    return $kardex;
                }

                if (isset($kardex["generate"]) and $kardex["generate"]) {
                    $kardex["asiento_costo"] = $kardex["asiento_costo"];
                    $logCosto = LogAsiento::guardarAsientoCostoVenta($id_venta, $kardex["asiento_costo"]);
                    if ($logCosto["status"] == "error") {
                        DB::rollBack();
                        return $logCosto;
                    }
                }
            }
            $getSri         = "No";
            $id_comprobante_ingreso = "";
            if ($c_caja != 100) {
                if ($request['electronica'] == '1') {
                    if ($empresa->electronica == 1) {
                        $getSri = $this->getSRI($id_venta);

                        if (isset($getSri["comprobante"])) {
                            $nfactura = $getSri["comprobante"];
                            $getSri = $getSri["status"];
                        } else {
                            $getSri = $getSri;
                        }
                    }
                }
            } else {
                $num_sec_vent = Ct_ventas::find($id_venta);
                $num_vent = $num_sec_vent->numero;
                $nfactura = $num_sec_vent->nro_comprobante;
            }
            //dd($nfactura);
            if (!$validates) {
                $erf = VentasController::newCrearComprobante($nfactura, $request, $arr_p, $id_venta);
                if ($erf["status"] != "success") {
                    DB::rollBack();
                    return $erf;
                }
                $id_comprobante_ingreso = $erf["id_ingreso"];
            }
            $inventario = null;
            // if (Auth::user()->id == "0957258056") {
            //     dd($nfactura);
            //     DB::rollBack();
            // }
            if ($entregaMercaderia) {
                $entregaMercaderia->id_venta = $id_venta;
                $entregaMercaderia->estado = 'FACTURADA';
                $entregaMercaderia->id_usuariomod = $idusuario;
                $entregaMercaderia->ip_modificacion = $ip_cliente;
                $entregaMercaderia->save();
                EntregaMercaderiaTrazabilidad::create([
                    'id_entrega' => $entregaMercaderia->id,
                    'evento' => 'FACTURA_CREADA',
                    'documento_tipo' => 'CT_VENTAS',
                    'documento_id' => $id_venta,
                    'detalle' => 'Factura comercial generada sin nuevo movimiento de inventario.',
                    'id_usuario' => $idusuario,
                    'ip' => $ip_cliente,
                ]);
            }
            DB::commit();
            if (!is_null($id_proforma) and trim($id_proforma) != "") {
                $proforma = Proforma_Venta_Cab::find($id_proforma);
                $proforma->id_venta         = $id_venta;
                $proforma->id_usuariomod    = $idusuario;
                $proforma->ip_modificacion  = $ip_cliente;
                $proforma->save();
            }
            // $arrayProforma = [
            //     'id_venta' => $id_venta
            // ];
            $inventario = $entregaMercaderia ? 'Inventario descargado previamente en ' . $entregaMercaderia->numero : Inventario::build_process('V', $id_venta, $id_empresa);
            return [
                'status' => "success",
                'idasiento' => $id_asiento_cabecera,
                'idventa' => $id_venta,
                'orden_id' => $orden_id,
                'arr_p' => $arr_p,
                'erf' => $erf,
                'getSri' => $getSri,
                'num_vent' => $num_vent,
                'error' => 'no',
                "respuesta" => "si",
                'mensaje' => "exito",
                'msj' => "Guardado Correctamente",
                'inventario' => $inventario,
                "asiento_costo" => $kardex["asiento_costo"],
                "id_comprobante_ingreso" => $id_comprobante_ingreso
            ];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['msj' => $e->getMessage(), "status" => "error", 'Linea' => $e->getLine()];
        }
    }

    /**
     * Valida el vínculo logístico y bloquea la entrega para impedir doble factura.
     * Al existir este vínculo el store conserva toda la contabilización comercial,
     * pero omite kardex e inventario porque fueron reconocidos al entregar.
     */
    private function validarEntregaMercaderia(Request $request, $idEmpresa)
    {
        if (!$request->has('entrega_mercaderia_id') || trim((string) $request->input('entrega_mercaderia_id')) === '') {
            return null;
        }

        $entrega = EntregaMercaderia::with('detalles')
            ->where('id_empresa', $idEmpresa)
            ->where('id_cliente', $request->identificacion_cliente)
            ->where('estado', 'ENTREGADA')
            ->whereNull('id_venta')
            ->lockForUpdate()
            ->find($request->entrega_mercaderia_id);
        if (!$entrega) {
            throw new Exception('La entrega no está disponible, no pertenece al cliente o ya fue facturada.');
        }
        if (!$entrega->id_movimiento_inventario || !$entrega->id_asiento_costo || !$entrega->id_guia_remision) {
            throw new Exception('La entrega no tiene completos el egreso, el asiento de costo y la guía.');
        }

        $esperado = [];
        foreach ($entrega->detalles as $detalle) {
            $esperado[$detalle->codigo_producto] = isset($esperado[$detalle->codigo_producto])
                ? $esperado[$detalle->codigo_producto] + (float) $detalle->cantidad
                : (float) $detalle->cantidad;
        }
        $recibido = [];
        foreach ((array) $request->input('codigo', []) as $indice => $codigo) {
            $cantidad = (float) $request->input('cantidad.' . $indice, 0);
            $recibido[$codigo] = isset($recibido[$codigo]) ? $recibido[$codigo] + $cantidad : $cantidad;
        }
        ksort($esperado);
        ksort($recibido);
        if (array_keys($esperado) !== array_keys($recibido)) {
            throw new Exception('La factura debe contener exactamente los productos de la entrega.');
        }
        foreach ($esperado as $codigo => $cantidad) {
            if (abs($cantidad - $recibido[$codigo]) > 0.0001) {
                throw new Exception('La cantidad facturada de ' . $codigo . ' no coincide con la cantidad entregada.');
            }
        }
        return $entrega;
    }

    public static function newCrearComprobante($nfactura, Request $request, $array_pagos, $factura_id)
    {
        try {
            $id_empresa     = $request->session()->get('id_empresa');
            $ip_cliente     = $_SERVER["REMOTE_ADDR"];
            $idusuario      = Auth::user()->id;
            $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
            $numero_factura = "";

            $secuencia_ing = "";
            // if (is_null($nfactura)) {
            //     $nfactura = $contador_ctv;
            //     if ($contador_ctv == 0) {
            //         $num            = '1';
            //         $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            //     } else {
            //         //Obtener Ultimo Registro de la Tabla ct_compras
            //         $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
            //         $secuencia = intval($max_id->secuencia);
            //         if (strlen($secuencia) < 10) {
            //             $nu             = $secuencia + 1;
            //             $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            //         }
            //     }
            //     $nfactura = $numero_factura;
            // }

            // if(Auth::user()->id == "0957258056"){
            //     dd("Holaa", $nfactura, $numero_factura);
            // }

            $arrFactura = explode("-", $nfactura);
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
                $secuencia = intval($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu             = $secuencia + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $secuencia_ing = $numero_factura;
            if (is_null($nfactura) or trim($nfactura) == "") {
                $nfactura = $secuencia_ing;
            }
            $objeto_validar = new Validate_Decimals();
            $id_comprobante = 0;
            if (sizeOf($array_pagos) == 0) {
                return ["status" => "error", "msj" => "No se creo el comprobante de ingreso"];
            }
            //if (sizeOf($array_pagos) > 0) {
            $total_pagos = $request['valor_totalPagos'];
            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura,
                'fecha_asiento'   => $request['fecha_asiento'],
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();
            if (is_null($desc_cuenta)) {
                return ["status" => "error", "msj" => "No tiene configurado cuentas para crear el comprobante de ingreso"];
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO FACT: ' . $nfactura,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $request['fecha_asiento'],
                'secuencia'           => $secuencia_ing,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $request['identificacion_cliente'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => $request['cedula_vendedor'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                if ($valor['fecha_pago'] != "") {
                }
                $val = "";
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : $request['fecha_asiento'];
                Ct_Detalle_Pago_Ingreso::create([
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $fecha_pago,
                    'numero'          => $valor['numero_pago'],
                    'id_banco'        => $valor['id_banco_pago'],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'],
                    'id_tipo'         => $valor['id_tip_pago'],
                    'total'           => $val,
                    'cuenta'          => $valor['id_cuenta_pago'],
                    'girador'         => $valor['giradoa'],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }

            $confg = LogConfig::busqueda('1.01.01.1.01');
            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();
            if (is_null($desc_cuenta)) {
                return ["status" => "error", "Falta configurar cuenta para el Comprobante de Ingreso", "exp" => ""];
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta'      => $confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $request['fecha_asiento'],
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $request['fecha_asiento'],
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $request['total1'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $request['total1']) {
                //aqui creo una orden de venta nueva
            }
            //$orden_id = $this->ordenVenta($request['id_venta'], $request);
            $valor_actual = Ct_ventas::find($factura_id);
            $valor_actual->valor_contable = $valor_actual->valor_contable - $total_pagos;
            $valor_actual->estado_pago    = 2;
            $valor_actual->save();
            // $id__ventas   = [

            //     'valor_contable' => floatVal($valor_actual->valor_contable) - $total_pagos,
            //     'estado_pago'    => 2,
            // ];

            // Ct_ventas::where('id', $factura_id)->update($id__ventas);
            // $consulta_venta  = null;
            // $input_actualiza = null;

            return ["status" => "success", "msj" => "Se creo el <b>Comprobante Ingreso</b> correctamente", "id_ingreso" => $id_comprobante];
            //} else {
            //     return 0;
            // }
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => "No se creo el <b>Comprobante de ingreso</b>", "exp" => $e->getMessage()];
        }
    }
    public function update($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_as   = date('Y-m-d', strtotime($request['fecha_asiento']));
        //dd($fecha_as);
        $ventas = Ct_ventas::find($id);
        if ($ventas->electronica != 1) {
            $asiento_cabecera = Ct_Asientos_Cabecera::find($ventas->id_asiento);
            $input_cabecera   = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $ventas->nro_comprobante,
                'id_empresa'      => $id_empresa,
                'observacion'     => $request['concepto'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            //dd($request->all());
            $id_asiento_cabecera = $asiento_cabecera->update($input_cabecera);
            foreach ($asiento_cabecera->detalles as $value) {
                $detalis                = Ct_Asientos_Detalle::find($value->id);
                $detalis->fecha         = $fecha_as;
                $detalis->id_usuariomod = $idusuario;
                $detalis->save();
            }
            $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal      = $cod_sucurs->codigo_sucursal;
            $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja          = $cod_caj->codigo_caja;
            $num_comprobante = $c_caja . '-' . $c_sucursal . '-' . $request['numero'];
            $id_paciente     = $request['identificacion_paciente'];
            $patient         = Paciente::find($id_paciente);
            $pc              = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            $factura_venta = [
                'numero'              => $request['numero'],
                'nro_comprobante'     => $num_comprobante,
                'fecha'               => $fecha_as,
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $pc,
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                //'id_nivel'            => $request['id_nivel'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_procedimiento'],
                'concepto'            => $request['concepto'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'ci_vendedor'         => $request['cedula_vendedor'],
                'vendedor'            => $request['vendedor'],
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
            ];
            $ventas->update($factura_venta);
        }
        return redirect()->back()->withErrors(['msg', 'The Message']);
    }
    public function getSRI($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden           = Ct_ventas::find($id);
        $data['empresa'] = $orden->id_empresa; // DR CARLOS ROBLES
        //$data['empresa']      = '0992704152001';   //gastroclinica
        $getType = $orden->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $cliente['cedula']   = $orden->ruc_id_cliente;
        $cliente['tipo']     = $getType; //eduardo dice q el lo calcula
        $cliente['nombre']   = $orden->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->cliente->nombre);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        //dd($cliente);
        $cliente['email']     = $orden->email_cliente;
        $cliente['telefono']  = $orden->telefono_cliente;
        $direccion['calle']   = $orden->direccion_cliente;
        $direccion['ciudad']  = $orden->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($direccion);
        $msn_error  = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en cedula';
        }
        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }

        $cant          = 0;
        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
        $setProducto   = "EXAMEN LABS";
        //dd($venta_detalle);

        foreach ($venta_detalle as $value) {
            $detalle               = trim(preg_replace("/\s+/", " ", $value->detalle));
            $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
            $producto['nombre']    = $value->nombre . ' ' . $detalle; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = $value->cantidad;
            $producto['precio']    = $value->precio; //DETALLE
            if ($value->copago > 0) {
                $producto['precio']    = $value->extendido / $value->cantidad;
            }
            $pricetot              = $value->cantidad * $value->precio;
            $producto['descuento'] = $value->descuento;
            $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
            $tax                   = "0";
            if ($value->check_iva == 1) {
                $tax = ($pricetot - $value->descuento) * $value->porcentaje;
            }
            $producto['tax']    = $tax;
            $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
            $producto['copago'] = "0";
            $productos[$cant]   = $producto;
            $cant++;
        }
        $data['productos'] = $productos;
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */
        /*
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;
         */
        if ($orden->id_paciente != null) {
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $info[0]                  = $info_adicional;
        }

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_cliente; //EMAIL
        $info[2]                  = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->cliente->ciudad_representante; //EMAIL
        $info[3]                  = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_cliente; //EMAIL
        $info[4]                  = $info_adicional;
        /*
        $info_adicional['nombre']      = "ORDEN";
        $info_adicional['valor']       = ''.$orden->id.'';//EMAIL
        $info[5]                       = $info_adicional;
         */
        if ($orden->seguro != null) {
            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
            $info[5]                  = $info_adicional;
        }
        $sform = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id)->get();

        $forma_pago = array();
        if (count($sform) > 1) {
            $sformid                  = $sform[0]->id_comprobante;
            $forma_pago               = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto                    = '';

            foreach ($forma_pago as $fp) {
                $total = $fp->total;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[6]                 = $info_adicional;
        } else {
            //dd("hi");
            if ($sform == '[]') {
                $pago['forma_pago']       = '20';
                $texto                    = "OTROS UTILIZANDO EL SISTEMA FINANCIERO";
                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = $texto;
                $info[6]                  = $info_adicional;
            } else {
                $sformid = $sform[0]->id_comprobante;

                $forma_pago = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
                //$forma_pago = $orden->detalle_forma_pago->first();
                //dd($forma_pago);
                $tipo = $forma_pago[0]->id_tipo;
                if ($tipo == '1') {
                    $pago['forma_pago'] = '01';
                } elseif ($tipo == '2') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '3') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '4') {
                    $pago['forma_pago'] = '19';
                } elseif ($tipo == '5') {
                    $pago['forma_pago'] = '20';
                } else {
                    $pago['forma_pago'] = '16';
                }
            }
        }

        $pago['informacion_adicional'] = $info;
        //  $pago['dias_plazo']            = '30';
        // if(Auth::user()->id == "0957258056"){
        $pago['dias_plazo']            = "{$orden->dias_plazo}";
        // }

        $data['pago']                  = $pago;
        $data['contable']              = 0; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 0;
        if ($orden->id_paciente != null) {
            $data['paciente'] = $orden->id_paciente;
        }
        $data['concepto']      = 'Factura Electronica -' . $orden->concepto;
        $data['copago']        = 0;
        $data['id_seguro']     = '0'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura'] = $orden->total_final;
        //dd($data);
        $fp_cant = 0;
        //dd($forma_pago);
        if (count($forma_pago) > 0) {
            foreach ($forma_pago as $fp) {
                $tipos_pago['id_tipo']            = $fp->id_tipo; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($fp->fecha, 0, 10);
                $tipos_pago['tipo_tarjeta']       = $fp->id_tipo_tarjeta; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                $tipos_pago['id_banco']           = $fp->id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                $tipos_pago['giradoa']            = $fp->girador; //si es efectivo no se envia
                $tipos_pago['valor']              = $fp->total; //valor a pagar de total
                $tipos_pago['valor_base']         = $fp->total; //valor a pagar de base

                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            }
        } else {
            $tipos_pago['id_tipo']            = "7"; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha, 0, 10);
            $tipos_pago['tipo_tarjeta']       = ""; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = ""; //si es efectivo no se envia
            $tipos_pago['id_banco']           = ""; //si es efectivo no se envia
            $tipos_pago['cuenta']             = ""; //si es efectivo no se envia
            $tipos_pago['giradoa']            = ""; //si es efectivo no se envia
            $tipos_pago['valor']              = "0"; //valor a pagar de total
            $tipos_pago['valor_base']         = "0"; //valor a pagar de base
            $pagos[0]                         = $tipos_pago;
        }

        //dd($forma_pago);
        //dd($pagos);
        $data['formas_pago'] = $pagos;

        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error  = 'Ya enviado al SRI';
        }
        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CARLOS ROBLES",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }

        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        $envio = ApiFacturacionController::envio_factura($data);
        $orden->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);
        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "CARLOS ROBLES FAC VENTA",
            'dato_ant1'   => $orden->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);
        // return "ok";
        return ["status" => "ok", "comprobante" => $envio->comprobante];
    }
    public function getSRI1($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden = Ct_ventas::find($id);
        $data['empresa'] = $orden->id_empresa; // DR CARLOS ROBLES
        //$data['empresa']      = '0992704152001';   //gastroclinica
        $getType = $orden->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $cliente['cedula']   = $orden->ruc_id_cliente;
        $cliente['tipo']     = $getType; //eduardo dice q el lo calcula
        $cliente['nombre']   = $orden->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->cliente->nombre);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        //dd($cliente);
        $cliente['email']     = $orden->email_cliente;
        $cliente['telefono']  = $orden->telefono_cliente;
        $direccion['calle']   = $orden->direccion_cliente;
        $direccion['ciudad']  = $orden->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($direccion);
        $msn_error  = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en cedula';
        }
        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }
        $cant          = 0;
        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
        $setProducto   = "EXAMEN LABS";
        //dd($venta_detalle);
        // if (count($venta_detalle) > 0) {
        //     if ($orden->id_empresa == '0993075000001') {
        //         $prices  = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('precio');
        //         $discont = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('descuento');
        //         if (is_null($discont)) {
        //             $discont = 0;
        //         }
        //         $cants                 = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('cantidad');
        //         $detailsp              = Ct_detalle_venta::where('id_ct_ventas', $id)->whereNotNull('detalle')->first();
        //         $pricetots             = $cants * $prices;
        //         $cantidad              = '1';
        //         $producto['sku']       = $setProducto;
        //         $producto['nombre']    = 'EXAMENES DE LABORATORIO ' . $orden->concepto; // NOMBRE DEL EXAMEN
        //         $producto['cantidad']  = '1';
        //         $producto['precio']    = $prices;
        //         $producto['descuento'] = $discont;
        //         $producto['subtotal']  = $orden->total_final; //precio-descuento
        //         $tax                   = "0";
        //         $producto['tax']       = $tax;
        //         $producto['total']     = $orden->total_final; //SUBTOTAL
        //         $producto['copago']    = "0";
        //         $productos[0]          = $producto;
        //     }

        // } else {
        //     if (Auth::user()->id == '0951561075') {
        //         dd("entra walter");
        //     }
        //     foreach ($venta_detalle as $value) {
        //         $detalle = trim(preg_replace("/\s+/", " ", $value->detalle));
        //         //se envian los productos

        //         $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
        //         $producto['nombre']    = $value->nombre . ' ' . $detalle; // NOMBRE DEL EXAMEN
        //         $producto['cantidad']  = $value->cantidad;
        //         $producto['precio']    = $value->precio; //DETALLE
        //         if ($value->copago > 0) {
        //             $producto['precio']    = $value->extendido / $value->cantidad;
        //         }
        //         $pricetot              = $value->cantidad * $value->precio;
        //         $producto['descuento'] = $value->descuento;
        //         $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
        //         $tax                   = "0";
        //         if ($value->check_iva == 1) {
        //             $tax = ($pricetot - $value->descuento) * $value->porcentaje;
        //         }
        //         $producto['tax']    = $tax;
        //         $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
        //         $producto['copago'] = "0";
        //         $productos[$cant]   = $producto;
        //         $cant++;
        //     }
        // }
        if (count($venta_detalle) > 0) {
            foreach ($venta_detalle as $value) {
                $detalle = trim(preg_replace("/\s+/", " ", $value->detalle));
                //se envian los productos
                $ct_producto_nombre = Ct_productos::where('codigo', $value->id_ct_productos)->where('id_empresa', $data['empresa'])->first();
                $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
                $producto['nombre']    = $ct_producto_nombre->nombre . ' ' . $detalle; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = $value->cantidad;
                $producto['precio']    = $value->precio; //DETALLE
                if ($value->copago > 0) {
                    $producto['precio']    = $value->extendido / $value->cantidad;
                }
                $pricetot              = $value->cantidad * $value->precio;
                $producto['descuento'] = $value->descuento;
                $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
                $tax                   = "0";
                if ($value->check_iva == 1) {
                    $tax = ($pricetot - $value->descuento) * $value->porcentaje;
                }
                $producto['tax']    = $tax;
                $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
                $producto['copago'] = "0";
                $productos[$cant]   = $producto;
                $cant++;
            }
        }
        $data['productos'] = $productos;
        //dd($data);
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */
        /*
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;
         */
        if ($orden->id_paciente != null) {
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $info[0]                  = $info_adicional;
        }

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_cliente; //EMAIL
        $info[2]                  = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->cliente->ciudad_representante; //EMAIL
        $info[3]                  = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_cliente; //EMAIL
        $info[4]                  = $info_adicional;
        if ($orden->seguro != null) {
            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
            $info[5]                  = $info_adicional;
        }
        $sform = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id)->get();
        $forma_pago = array();
        if (count($sform) > 1) {
            $sformid                  = $sform[0]->id_comprobante;
            $forma_pago               = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto                    = '';
            foreach ($forma_pago as $fp) {
                $total = $fp->total;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[6]                 = $info_adicional;
        } else {
            //dd("hi");
            if ($sform == '[]') {
                $pago['forma_pago']       = '20';
                $texto                    = "OTROS UTILIZANDO EL SISTEMA FINANCIERO";
                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = $texto;
                $info[6]                  = $info_adicional;
            } else {
                $sformid = $sform[0]->id_comprobante;
                $forma_pago = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
                //$forma_pago = $orden->detalle_forma_pago->first();
                //dd($forma_pago);
                $tipo = $forma_pago[0]->id_tipo;
                if ($tipo == '1') {
                    $pago['forma_pago'] = '01';
                } elseif ($tipo == '2') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '3') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '4') {
                    $pago['forma_pago'] = '19';
                } elseif ($tipo == '5') {
                    $pago['forma_pago'] = '20';
                } else {
                    $pago['forma_pago'] = '16';
                }
            }
        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;
        $data['contable']              = 0; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 0;
        if ($orden->id_paciente != null) {
            $data['paciente'] = $orden->id_paciente;
        }
        $data['concepto']      = 'Factura Electronica -' . $orden->concepto;
        $data['copago']        = 0;
        $data['id_seguro']     = '0'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura'] = $orden->total_final;
        //dd($data);
        $fp_cant = 0;
        //dd($forma_pago);
        if (count($forma_pago) > 0) {
            foreach ($forma_pago as $fp) {
                $tipos_pago['id_tipo']            = $fp->id_tipo; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($fp->fecha, 0, 10);
                $tipos_pago['tipo_tarjeta']       = $fp->id_tipo_tarjeta; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                $tipos_pago['id_banco']           = $fp->id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                $tipos_pago['giradoa']            = $fp->girador; //si es efectivo no se envia
                $tipos_pago['valor']              = $fp->total; //valor a pagar de total
                $tipos_pago['valor_base']         = $fp->total; //valor a pagar de base
                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            }
        } else {
            $tipos_pago['id_tipo']            = "7"; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha, 0, 10);
            $tipos_pago['tipo_tarjeta']       = ""; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = ""; //si es efectivo no se envia
            $tipos_pago['id_banco']           = ""; //si es efectivo no se envia
            $tipos_pago['cuenta']             = ""; //si es efectivo no se envia
            $tipos_pago['giradoa']            = ""; //si es efectivo no se envia
            $tipos_pago['valor']              = "0"; //valor a pagar de total
            $tipos_pago['valor_base']         = "0"; //valor a pagar de base
            $pagos[0]                         = $tipos_pago;
        }
        //dd($forma_pago);
        //dd($pagos);
        $data['formas_pago'] = $pagos;
        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error  = 'Ya enviado al SRI';
        }
        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CARLOS ROBLES",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }
        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        /*try {
            //$envio = ApiFacturacionController::envio_factura($data);
            //2022-09-08
            $envio = EmisionDocumentosController::getFactura($data);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage(), 'line' => $e->getLine(), 'controller' => $this->controlador]);
        }


        //dd($envio);

        $orden->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);

        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "CARLOS ROBLES FAC VENTA",
            'dato_ant1'   => $orden->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);*/

        return "ok";
    }
    public function store_ordenes(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'estado_pago'         => '0',
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],
            'copago'              => $request['total1'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            //'nota'                          => $request['nota'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            //'subtotal'                      => $request['subtotal1'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            // 'transporte'                    => $request['transporte'],
            'total_final'         => $request['totalc'],
            'valor_contable'      => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];
        // return $factura_venta;
        $id_venta = Ct_ven_orden::insertGetId($factura_venta);
        //$id_venta = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;
        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
                    'codigo'     => $request->input("codigo")[$i],
                    'precio'     => $request->input("precio")[$i],
                    'descpor'    => $request->input("descpor")[$i],
                    'copago'     => $request->input("copago")[$i],
                    'descuento'  => $request->input("desc")[$i],
                    'precioneto' => $request->input("precioneto")[$i],
                    'detalle'    => $request->input("descrip_prod")[$i],
                    'iva'        => $request->input("iva")[$i],
                ];
                array_push($arr_total, $arr);
            }
        }
        foreach ($arr_total as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['copago'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['precioneto'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_ven_orden_detalle::create($detalle);
        }
        return ['id' => $id_venta];
    }
    public function valida_numero_factura($idempresa, $sucursal, $punto_emision, $numero)
    {
        $numeroconcadenado    = $sucursal . '-' . $punto_emision . '-' . $numero;
        $verifica_num_factura = null;
        $validate             = true;
        $nfactura             = '';

        // do{
        $verifica_num_factura = Ct_ventas::where('id_empresa', $idempresa)
            ->where('nro_comprobante', $numeroconcadenado)
            ->where('tipo', '<>', 'VEN-FACT')
            ->where('estado', '!=', '0')->first();
        // if(!is_null($verifica_num_factura)){
        //  //   $nfactura        = $this->obtener_numero_factura($idempresa,$sucursal, $numero);
        //  //   $numeroconcadenado = $sucursal . '-' . $numero . '-' . $nfactura;
        //     //echo $numero;
        //     $numero ++;
        //     $nfactura = str_pad($numero, 10, "0", STR_PAD_LEFT);
        //     $numeroconcadenado = "{$sucursal}-{$punto_emision}-{$nfactura}";
        // }else{
        //     $validate = false;
        //     //dd ("sii entra");
        //     break;
        // }
        //}while(true);
        if (is_null($verifica_num_factura)) {
            //   dd("Numero");
            return $numero;
        } else {
            //  dd("existe");
            return "existe";
        }
        // dd("si {$numeroconcadenado}");

    }
    public function ordenVenta($id_v, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');
        if (Auth::user()->id == '0951561075') {
            //$id_empresa = '0916293723001';
            //dd($request->all());
        }
        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'estado_pago'         => '0',
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],
            'copago'              => $request['total1'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            'total_final'         => $request['totalc'],
            'valor_contable'      => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'id_ct_venta'         => $id_v,
        ];
        $id_venta  = Ct_ven_orden::insertGetId($factura_venta);
        $arr_total = [];
        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
                    'codigo'     => $request->input("codigo")[$i],
                    'precio'     => $request->input("precio")[$i],
                    'descpor'    => $request->input("descpor")[$i],
                    'copago'     => $request->input("copago")[$i],
                    'descuento'  => $request->input("desc")[$i],
                    'precioneto' => $request->input("precioneto")[$i],
                    'detalle'    => $request->input("descrip_prod")[$i],
                    'iva'        => $request->input("iva")[$i],

                ];
                array_push($arr_total, $arr);
            }
        }
        foreach ($arr_total as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['precioneto'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];
            Ct_ven_orden_detalle::create($detalle);
        }
        return ['id' => $id_venta];
    }
    public function updateorden(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'nro_autorizacion'    => $request['numero_autorizacion'],
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],

            'copago'              => $request['totalc'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            //'nota'                          => $request['nota'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            //'subtotal'                      => $request['subtotal1'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            // 'transporte'                    => $request['transporte'],
            'total_final'         => $request['total1'],
            'valor_contable'      => $request['total1'],
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,

        ];

        // return $factura_venta;

        Ct_ven_orden::where('id', $request['id'])->update($factura_venta);

        //$id_venta = Ct_ven_orden::insertGetId($factura_venta);
        $id_venta       = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;

        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'id_detalle' => $request->input("id_detalle")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
                    'codigo'     => $request->input("codigo")[$i],
                    'precio'     => $request->input("precio")[$i],
                    'descpor'    => $request->input("descpor")[$i],
                    'copago'     => $request->input("copago")[$i],
                    'descuento'  => $request->input("desc")[$i],
                    'precioneto' => $request->input("precioneto")[$i],
                    'detalle'    => $request->input("descrip_prod")[$i],
                    'iva'        => $request->input("iva")[$i],

                ];
                array_push($arr_total, $arr);
            }
        }
        foreach ($arr_total as $valor) {
            $detalle = [
                //'id_ct_ven_orden'                  => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['precioneto'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];

            // Ct_ven_orden_detalle::create($detalle);
            Ct_ven_orden_detalle::where('id', $valor['id_detalle'])->update($detalle);
        }

        //$request['id'];
        //return ['fact' => $factura_venta, 'det' => $detalle];
        return ['id' => $request['id']];
    }
    public function index_cierre(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            //$id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $caja = $request['caja'];
        $doctor = $request['doctor'];
        $id_paciente = $request['id_paciente'];
        $usuario_paciente = null;
        $tipo = $request['tipo'];
        /*if (is_null($tipo)) {
        $tipo = '0';
        }*/
        // $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', 'orden.id_agenda', 'agenda.id')
        //                         ->join('paciente as p', 'p.id', 'agenda.id_paciente')
        //                         ->join('users as u', 'agenda.id_usuariocrea', 'u.id')
        //                         ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
        //                         ->where('p.id_seguro', '<>', '3')
        //                         ->where('p.id_seguro', '<>', '6')
        //                         ->whereNull('orden.id');
        //$facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', 'orden.id_agenda', 'agenda.id')
        $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', function ($join) {
            $join->on(function ($query) {
                $query->on('orden.id_agenda', 'agenda.id')
                    ->where('orden.estado', '=', '1');
            });
        })
            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
            ->join('users as u', 'agenda.id_usuariomod', 'u.id')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->where('s.tipo', '<>', '0')->where('agenda.proc_consul', '<', '2')
            ->whereRaw('(agenda.omni = "%NO%" OR agenda.omni IS NULL)')
            ->where('agenda.estado', '<>', '0')
            ->whereNotNull('agenda.id_doctor1')
            ->whereNull('orden.id')
            ->where('agenda.estado_cita', '4')
            ->where('agenda.post_proc', '0')
            ->where('agenda.id_doctor1', '<>', '4444444444');
        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            //->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');
        if (!is_null($id_empresa)) {
            $ordenes  = $ordenes->where('ct_orden_venta.id_empresa', $id_empresa);
        }
        if (!is_null($tipo)) {
            $ordenes             = $ordenes->where('a.proc_consul', $tipo);
            $facturas_pendientes = $facturas_pendientes->where('agenda.proc_consul', $tipo);
        }
        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
            if ($caja == 'LABORATORIO') {
                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
            }
        } else {
            $ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
        }
        if (!is_null($doctor)) {
            $ordenes             = $ordenes->where('a.id_doctor1', $doctor);
            $facturas_pendientes = $facturas_pendientes->where('agenda.id_doctor1', $doctor);
        }
        if (!is_null($id_paciente)) {
            $ordenes  = $ordenes->where('a.id_paciente', $id_paciente);
            $usuario_paciente = Paciente::find($id_paciente);
        }
        $ordenes = $ordenes->get();
        $doctores = User::where('id_tipo_usuario', '3')->where('training', '0')->where('uso_sistema', '0')->orderby('apellido1')->get();
        $cajas      = CtCajaCobro::where('estado', 1)->get();
        $empresas            = Empresa::where('prioridad', '1')->where('estado', 1)->get();
        $facturas_pendientes = $facturas_pendientes->select('agenda.*', 'p.nombre1 as nombre1', 'p.apellido1 as apellido1', 'p.apellido2 as apellido2', 'u.nombre1 as unombre1', 'u.apellido1 as uapellido1', 'u.apellido2 as uapellido2')->get();
        return view('contable/ventas/index_cierre', ['empresas' => $empresas, 'facturas_pendientes' => $facturas_pendientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ordenes' => $ordenes, 'request' => $request, 'doctores' => $doctores, 'usuario_paciente' => $usuario_paciente, 'cajas' => $cajas]);
    }
    public function facturas_omni(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consultar     = $request->all();
        $fecha         = $request['fecha'];
        $fechafin      = $request['fecha_hasta'];
        $id_empresa    = $request->session()->get('id_empresa'); //$request['id_empresa'];
        $empresas      = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $procedimiento = Procedimiento::all();
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }
        $nombres        = $request['nombres'];
        $cedula         = $request['cedula'];
        $seguros        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();
        $procedimientos = [];
        if ($request->id_agenda != null) {
            $procedimientos = Agenda::whereIn('id', $request->id_agenda)->get();
        } else {
            if ($request->tipo != null) {
                $procedimientos = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', $request->tipo);
            } else {
                $procedimientos = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', '<>', 2);
            }

            $procedimientos = $procedimientos->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59']);
            $procedimientos = $procedimientos->join('historiaclinica as h', 'h.id_agenda', 'agenda.id');
            if (!is_null($request['id_seguro'])) {

                $procedimientos = $procedimientos->whereIn('h.id_seguro', $request['id_seguro']);
            }
            if (!is_null($request['procedimientos'])) {
                $procedimientos = $procedimientos->whereIn('agenda.id_procedimiento', $request['procedimientos']);
            }
            if (!is_null($request['omni'])) {
                if ($request['omni'] == "SI") {
                    $procedimientos = $procedimientos->whereRaw('(agenda.omni LIKE "%OM%" OR agenda.omni LIKE "%SI%")');
                } else {
                    $procedimientos = $procedimientos->where('agenda.omni', $request['omni']);
                }
            }
            $procedimientos = $procedimientos->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
            if (!is_null($cedula)) {
                $procedimientos->where('id_paciente', $cedula);
            }

            if ($nombres != null) {
                //dd("dada");
                $nombres2 = explode(" ", $nombres);
                $cantidad = count($nombres2);

                $nombres_sql = '';

                foreach ($nombres2 as $n) {
                    $nombres_sql = $nombres_sql . '%' . $n;
                }
                $nombres_sql = $nombres_sql . '%';

                if ($cantidad > 1) {
                    $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                        $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                    });
                } else {

                    $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                        $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                    });
                }
            }
            //dd($procedimientos);
            $procedimientos = $procedimientos->select('agenda.*', 'h.id_seguro as seguro_final')->get();
        }

        //dd($request->all());
        return view('contable/ventas/facturas_omni', ['procedimientos' => $procedimientos, 'procedimiento' => $procedimiento, 'empresas' => $empresas, 'empresa' => $empresas, 'seguros' => $seguros, 'fecha' => $fecha, 'fecha_hasta' => $fechafin, 'request' => $request]);
    }
    //crea la factura desde el recibo/reporte de caja
    public function factura_caja($id_orden, Request $request)
    {
        $porIva = $this->porIva;
        $fact_venta = Ct_Orden_Venta::findorfail($id_orden);
        $request['id_agenda'] = $fact_venta->id_agenda;
        $getReload = $this->getReloadRecibo($request);
        $ct_for_pag = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();
        $vistaurl = "contable.facturacion.pdf_comprobante_tributario";
        $view = \View::make($vistaurl, compact('fact_venta', 'ct_for_pag', 'porIva'))->render();
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros = Seguro::where('inactivo', '1')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        $id_plan = LogConfig::busqueda('4.1.01.02');
        //$iva = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        //$iva = Ct_Configuraciones::where('id_plan', $id_plan)->where('estado', '1')->first();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $fact_venta_detalle = Ct_Orden_Venta_Detalle::where('id_orden', $id_orden)->get();
        $bodega_in = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        return view('contable/ventas/create_facturaRecibo', ['porIva' => $this->porIva, 'bodega_in' => $bodega_in, 'fact_venta' => $fact_venta, 'tipo_tarjeta' => $tipo_tarjeta, 'fact_venta_detalle' => $fact_venta_detalle, 'ct_for_pag' => $ct_for_pag, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas]);
    }
    public function store_varios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        foreach (['total1', 'totalc', 'subtotal_01', 'subtotal_121', 'descuento1', 'base1', 'tarifa_iva1'] as $campo) {
            if ($request->has($campo) && is_numeric($request->input($campo))) {
                $request->merge([
                    $campo => round((float) $request->input($campo), 2, PHP_ROUND_HALF_UP)
                ]);
            }
        }
        foreach (['copago', 'desc', 'precioneto'] as $campo) {
            if (is_array($request->input($campo))) {
                $request->merge([
                    $campo => array_map(function ($valor) {
                        return is_numeric($valor)
                            ? round((float) $valor, 2, PHP_ROUND_HALF_UP)
                            : $valor;
                    }, $request->input($campo))
                ]);
            }
        }
        $request->merge($this->calcularTotalesConglomerada($request->all()));
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $llevaOrden = false;
        $idusuario  = Auth::user()->id;
        $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $numero     = $request['numero'];
        $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $proced = $request['procedimiento'];
        if ($request['sucursal'] != 0) {
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja = $cod_caj->codigo_caja;
            $proced = $request['procedimiento'];
            if (!is_null($numero)) {
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')
                    ->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
        } else {
            $c_sucursal = 0;
            $c_caja = 0;
            $num_comprobante = 0;
            $nfactura = 0;
            $proced = $request['procedimiento'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        DB::beginTransaction();
        try {
            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $arr_total2          = [];
            if ($id_empresa == '0993075000001')
                $id_asiento_cabecera = 0;
            else
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
            $factura_venta = [
                'sucursal' => $cod_sucurs->codigo_sucursal,
                'punto_emision' => $cod_caj->codigo_caja,
                'establecimiento' => $cod_sucurs->codigo_sucursal,
                'emision' => $cod_caj->codigo_caja,
                'numero' => $nfactura,
                'nro_comprobante' => $num_comprobante,
                'id_asiento' => $id_asiento_cabecera,
                'id_empresa' => $id_empresa,
                'tipo' => 'VENFA-CO',
                'ambulatorio' => $request['amBu'],
                'fecha' => $request['fecha_asiento'],
                'divisas' => $request['divisas'],
                'nombre_cliente' => $request['nombre_cliente'],
                'tipo_consulta' => $request['tipo_consulta'],
                'id_cliente' => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente' => $request['direccion_cliente'],
                'ruc_id_cliente' => $request['identificacion_cliente'],
                'telefono_cliente' => $request['telefono_cliente'],
                'email_cliente' => $request['mail_cliente'],
                'orden_venta' => $request['orden_venta'],
                'nro_autorizacion' => $request['numero_autorizacion'],
                'id_paciente' => $request['identificacion_paciente'],
                'nombres_paciente' => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'concepto' => $request['concepto'],
                'seguro_paciente' => $request['id_seguro'],
                'electronica' =>  $c_caja == 100 ? 1 : $request['electronica'],
                'procedimientos' => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago' => $request['totalc'],
                'id_recaudador' => $request['cedula_recaudador'],
                'subtotal_0' => $request['subtotal_01'],
                'subtotal_12' => $request['subtotal_121'],
                'descuento' => $request['descuento1'],
                'base_imponible' => $request['base_iva'],
                'impuesto' => $request['tarifa_iva1'],
                'total_final' => $request['total1'],
                'valor_contable' => $request['total1'],
                'ip_creacion' => "conglomerada",
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'nota_electronica' => $request['concepto'],
                'doc_electronico' => $c_caja == 100 ? 0 : null,
                'emisor' => $c_caja == 100 ? 'MD' : null,
            ];
            if ($id_empresa == '0993075000001')
                $id_venta = 0;
            else
                $id_venta = Ct_ventas::insertGetId($factura_venta);
            for ($i = 0; $i < count($request->codigo); $i++) {
                $upd = [
                    'estado_pago'     => '1',
                    'id_referencia'   => $id_venta,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $nombre = "";
                if (isset($request->input("nombre_producto")[$i])) {
                    $nombre = $request->input("nombre_producto")[$i];
                }
                $codigo = "";
                if (isset($request->input("codigo")[$i])) {
                    $codigo = $request->input("codigo")[$i];
                }
                $cantidad = 0;
                if (isset($request->input("cantidad")[$i])) {
                    $cantidad = $request->input("cantidad")[$i];
                }
                $paciente = "";
                if (isset($request->input("paciente")[$i])) {
                    $paciente = $request->input("paciente")[$i];
                }
                $precio = 0;
                if (isset($request->input("precio")[$i])) {
                    $precio = $request->input("precio")[$i];
                }
                $descuentopor = 0;
                if (isset($request->input("descpor")[$i])) {
                    $descuentopor = $request->input("descpor")[$i];
                }
                $descuento = 0;
                if (isset($request->input("desc")[$i])) {
                    $descuento = $request->input("desc")[$i];
                }
                $copago = 0;
                if (isset($request->input("copago")[$i])) {
                    $copago = $request->input("copago")[$i];
                }
                $precio_neto = 0;
                if (isset($request->input("precioneto")[$i])) {
                    $precio_neto = $request->input("precioneto")[$i];
                }
                $detalle = "";
                $detalle = $request->input("descrip_prod")[$i];
                $iva = 0;
                if (isset($request->input("iva")[$i])) {
                    $iva = $request->input("iva")[$i];
                }
                $id_orden = 1;
                if (isset($request->input("id_orden")[$i])) {
                    $id_orden = $request->input("id_orden")[$i];
                    $ventax = Ct_ven_orden::where('id', $request->input("id_orden")[$i])->first();
                    if ($ventax != '') {
                        Ct_ven_orden::where('id', $request->input("id_orden")[$i])->update($upd);
                    }
                }
                $bodega_in = "";
                if (isset($request->input("bodega")[$i])) {
                    $bodega_in = $request->input("bodega")[$i];
                }
                $arr = [
                    'nombre'      => $nombre,
                    'cantidad'    => $cantidad,
                    'codigo'      => $codigo,
                    'id_paciente' => $paciente,
                    'precio'      => $precio,
                    'id_orden'    => $id_orden,
                    'descpor'     => $descuentopor,
                    'copago'      => $copago,
                    'descuento'   => $descuento,
                    'precioneto'  => $precio_neto,
                    'detalle'     => $detalle,
                    'iva'         => $iva,
                    'id_hc_proc'  => $request->input('id_hc_proc')[$i],
                    'obs_pac'     => $text,
                    'bodega'      => $bodega_in,
                ];
                array_push($arr_total2, $arr);
            }
            $detalleConglomerada = [];
            foreach ($arr_total2 as $valor) {
                //dd($valor);
                $paciente = "";
                if (isset($valor['paciente'])) {
                    $paciente = $valor['paciente'];
                }
                $precio_neto = "";
                if (isset($valor['precioneto'])) {
                    $precio_neto = $valor['precioneto'];
                }
                $nombre = "";
                if (isset($valor['nombre'])) {
                    $nombre = $valor['nombre'];
                }
                $cantidad = 0;
                if (isset($valor['cantidad'])) {
                    $cantidad = $valor['cantidad'];
                }
                $codigo = "";
                if (isset($valor['codigo'])) {
                    $codigo = $valor['codigo'];
                }
                $nombre_procedimiento = "";
                if (isset($valor['nombre_procedimiento'])) {
                    $nombre_procedimiento = $valor['nombre_procedimiento'];
                }
                $fecha_procedimiento = "";
                if (isset($valor['fecha_procedimiento'])) {
                    $fecha_procedimiento = $valor['fecha_procedimiento'];
                }
                $precio = 0;
                if (isset($valor['precio'])) {
                    $precio = $valor['precio'];
                }
                $paciente = "";
                if (isset($valor['paciente'])) {
                    $paciente = $valor['paciente'];
                }
                $iva = 0;
                if (isset($valor['iva'])) {
                    $iva = $valor['iva'];
                }
                $descuento = 0;
                if (isset($valor['descuento'])) {
                    $descuento = $valor['descuento'];
                }
                $descrip_prod = "";
                if (isset($valor['detalle'])) {
                    $descrip_prod = $valor['detalle'];
                }
                $id_hc_proc = "";
                if (isset($valor['id_hc_proc'])) {
                    $id_hc_proc = $valor['id_hc_proc'];
                }
                $id_orden = 1;
                if (isset($valor['id_orden'])) {
                    $id_orden = $valor['id_orden'];
                }
                $bodega_in = "";
                if (isset($valor['bodega'])) {
                    $bodega_in = $valor['bodega'];
                }
                $copago = round((float) ($valor['copago'] ?? 0), 2, PHP_ROUND_HALF_UP);
                $precio_neto = round((floatval($cantidad) * floatval($precio)) - $descuento - $copago, 2, PHP_ROUND_HALF_UP);
                $desPro = Ct_productos::where('codigo', $codigo)->first();
                $detalle = [
                    'id_ct_ventas' => $id_venta,
                    'id_ct_productos' => $codigo,
                    'nombre' => $codigo,
                    'descripcion_producto' => $desPro != '' ? $desPro->nombre : $codigo,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'descuento_porcentaje' => $descuento,
                    'descuento' => $descuento,
                    'extendido' => $precio_neto,
                    'detalle' => $descrip_prod,
                    'copago' => $copago,
                    'check_iva' => $iva,
                    'codigo' => $codigo,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'bodega' => $bodega_in,
                ];
                if ($id_empresa != '0993075000001')
                    Ct_detalle_venta::create($detalle); //
                $detalles = [
                    'id_ct_ventas' => $id_venta,
                    'id_ct_productos' => $codigo,
                    'nombre' => $codigo,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'descuento_porcentaje' => $descuento,
                    'descuento' => $descuento,
                    'extendido' => $precio_neto,
                    'id_paciente' => $valor['id_paciente'],
                    'detalle' => $descrip_prod,
                    'copago' => $copago,
                    'check_iva' => $iva,
                    'id_orden' => $valor['id_orden'],
                    'codigo' => $codigo,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                array_push($detalleConglomerada, $detalles);
                if ($id_empresa != '0993075000001')
                    Ct_Detalle_Venta_Conglomerada::create($detalles);
            }
            //*
            //***MODULO CUENTA POR COBRAR***
            //
            //cUENTAS X COBRAR CLIENTES
            // --activo
            $val_tol = $request['total1'];
            $data['id'] = $id_venta;
            $data['tipo'] = 'VEN-FA';
            $getSri = "No";
            $empresa = Empresa::find($id_empresa);

            $arr_p = [];
            for ($i = 0; $i < $request['contador_pago']; $i++) {
                $visibilidad_pa = $request['visibilidad_pago' . $i];
                if ($visibilidad_pa == 1) {
                    $arr_pagos = [
                        'id_tipo'    => $request['id_tip_pago' . $i],
                        'fecha'     => $request['fecha_pago' . $i],
                        'tipo_tarjeta'   => $request['tipo_tarjeta' . $i],
                        'numero_transaccion'    => $request['numero_pago' . $i],
                        'id_banco'  => $request['id_banco_pago' . $i],
                        'cuenta' => $request['id_cuenta_pago' . $i],
                        'giradoa'        => $request['giradoa' . $i],
                        'valor'          => $request['valor' . $i],
                        'valor_base'     => $request['valor_base' . $i],
                        'info_banco' => Ct_Bancos::find($request['id_banco_pago' . $i])
                    ];
                    array_push($arr_p, $arr_pagos);
                }
            }
            $arrayDatos = [
                'input_cabecera' => $input_cabecera,
                'factura_venta' => $factura_venta,
                'arr_total2' => $arr_total2,
                'forma_pago' => $arr_p,
                'seguro' => Seguro::find($request['id_seguro']),
                'idUsuario' => $idusuario,
                'detalleConglomerada' => $detalleConglomerada,
                'formas_pago' => $arr_p
            ];
            if ($id_empresa == '0993075000001') {
                $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $nonce = EmisionDocumentosController::getNonce(15);
                $appId     = date('YmdHis') . '-Humanlabs';
                $appSecret = 'jdjdjKJOS(/68759856498465';
                $date      = date('c');
                $Token     = base64_encode(sha1($nonce . $date . $appSecret));
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                        'method'  => 'GET',
                        'content' => json_encode($arrayDatos),
                    ),
                    "ssl"  => array(
                        "verify_peer"      => false,
                        "verify_peer_name" => false,
                    ),
                );
                //echo '<pre>';print_r($arrayDatos);DB::rollBack();exit;
                $urllink = 'https://humanlabs.aitrol.com';
                $url = $urllink . '/apifacturaopciones/construirConglomerada';
                $context = stream_context_create($options);
                try {
                    $orden = file_get_contents($url, false, $context);
                    if ($orden != '') {
                    }
                } catch (Exception $e) {
                    $orden = '';
                }
                echo '<pre>';
                print_r($orden);
                //DB::rollBack();
                exit;
            }

            //dd("hola");
            if ($request['electronica'] == '1') {
                if ($empresa->electronica == 1) {
                    $getSri = $this->getSRI1($id_venta);
                }
            }
            $msj = Ct_Kardex::generar_kardex($data);
            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta' => $plan_cuentas->id,
                    'descripcion' => $plan_cuentas->nombre,
                    'fecha' => $fecha_as,
                    'debe' => $request['total1'],
                    'haber' => '0',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.02');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta' => $plan_cuentas->id,
                    'descripcion' => $plan_cuentas->nombre,
                    'fecha' => $fecha_as,
                    'debe' => '0',
                    'haber' => $request['subtotal_121'],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            for ($i = 0; $i < $request['contador_pago']; $i++) {
                $visibilidad_pa = $request['visibilidad_pago' . $i];
                if ($visibilidad_pa == 1) {
                    $arr_pagos = [
                        'id_tip_pago' => $request['id_tip_pago' . $i],
                        'fecha_pago' => $request['fecha_pago' . $i],
                        'tipo_tarjeta' => $request['tipo_tarjeta' . $i],
                        'numero_pago' => $request['numero_pago' . $i],
                        'id_banco_pago' => $request['id_banco_pago' . $i],
                        'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                        'giradoa' => $request['giradoa' . $i],
                        'valor' => $request['valor' . $i],
                        'valor_base' => $request['valor_base' . $i],
                    ];
                    array_push($arr_p, $arr_pagos);
                }
            }
            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            DB::commit();
            return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'kardex' => $msj, 'prd_val' => $request->input("prd_val"), 'llevaOrden' => $llevaOrden, 'mis inputs' => $request->all(), 'getSri' => $getSri, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return 'Error: ' . $e->getMessage() . ' Linea: ' . $e->getLine() . ' Archivo:' . $e->getFile();
        }
    }
    public function view_omni(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $validate = $request['validate'];
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //dd($request->all());
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        //$iva = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        /*
        if (!empty($request->input("id_agenda"))) {
        for ($i = 0; $i < count($request->input("id_agenda")); $i++) {
        $consulta_agenda =  DB::select('SELECT
        a.id AS id_agenda,
        ct.codigo AS codigo_producto,
        ct.nombre AS nombre_producto,
        h.fecha_atencion AS fecha_procedimiento,
        pc.id AS id_paciente,
        hc.id AS id_hc_procedimientos,
        pc.nombre1 AS nombre1,
        pc.nombre2 AS nombre2,
        pc.apellido1 AS apellido1,
        pc.apellido2 AS apellido2,

        pr.nombre AS nombre_completo,
        COUNT(DISTINCT(m.id)) AS cantidad_total,
        SUM(m.precio) AS precio_final
        FROM agenda AS a
        JOIN historiaclinica AS h
        ON h.id_agenda = a.id
        JOIN hc_procedimientos AS hc
        ON h.hcid = hc.id_hc
        JOIN hc_procedimiento_final as hcfinal
        ON hcfinal.id_hc_procedimientos = hc.id
        LEFT JOIN procedimiento as pr
        ON pr.id= hcfinal.id_procedimiento
        LEFT JOIN procedimiento_completo AS pcompleto
        ON pcompleto.id = hc.id_procedimiento_completo
        LEFT JOIN movimiento_paciente AS mp
        ON mp.id_hc_procedimientos = hc.id
        JOIN movimiento AS m
        ON m.id = mp.id_movimiento
        JOIN ct_productos_insumos AS proct
        ON proct.id_insumo = m.id_producto
        LEFT JOIN ct_productos AS ct
        ON ct.id = proct.id_producto
        JOIN paciente AS pc
        ON a.id_paciente = pc.id

        WHERE a.id =' . $request->input("id_agenda")[$i] . '
        GROUP BY m.id_producto  ORDER BY h.fecha_atencion DESC');
        //dd($consulta_agenda);
        //$segundo = Agenda::find($request->input("id_agenda")[$i]);
        if (count($consulta_agenda) > 0) {
        array_push($arr_todas, $consulta_agenda);
        }

        }
        }
        $group_procedures=array();
        foreach($arr_todas as $value){
        //dd($value);

        foreach($value as $x){
        //$group_store[$value->id_hc_procedimientos][] = $value;
        //dd($x);

        //$hc=hc_procedimientos::find($x->id_hc_procedimientos);
        $procedimiento_final= Hc_Procedimiento_Final::where('id_hc_procedimientos',$x->id_hc_procedimientos)->get();
        foreach($procedimiento_final as $final){
        if(!is_null($final)){
        //dd($value); $group[$x->id_paciente][] = $value;

        $array_groups=['paciente_principal'=>$x->id_paciente,'procedimiento_principal'=>$final->procedimiento->nombre."/".$x->id_paciente."/".$x->id_hc_procedimientos,'valores'=>$x];
        array_push($group_procedures,$array_groups);
        }
        }
        }
        }
        //dd($arr_todas);
        $finaly_array=array();
        $ter_A=array();
        foreach($group_procedures as $j=>$ter){
        if(!is_null($ter)){
        //dd($ter);
        //dd($ter['procedimiento_principal']);
        $ter_A[$ter['procedimiento_principal']][] = $ter;
        //array_push($finaly_array,$terA);
        }
        }
        //creo que debo partir desde paciente para agrupar los procedimientos por pacientes y dentro por procedimientos
        //dd($ter_A);
        //dd($ter_A);
        //dd($request->all());
        $group = array();
        $group2 = array();
        //group by procedures
        foreach ($arr_todas as $value) {
        foreach ($value as $x) {
        //dd($x);
        $group[$x->id_paciente][] = $value;
        array_push($pacientes, $x->id_paciente);
        }
        }
        if(count($group)>0){
        }else{
        //dd(count($request->input("paciente")));
        for ($i = 0; $i < count($request->input("paciente")); $i++) {
        if(!is_null($request->input("paciente")[$i])){
        //dd($request->input("paciente")[$i]);
        array_push($pacientes,$request->input("paciente")[$i]);
        }
        }
        }
        //dd($pacientes);
        //procedures group by
        $group_procedures=array();
        foreach($arr_todas as $z){
        foreach($z as $a){
        $group2[$a->id_hc_procedimientos][] = $z;
        array_push($group_procedures, $a->id_hc_procedimientos);
        }

        }*/
        $final = $this->getReloadOmni($request);
        //dd($final);
        //dd($pacientes);
        //dd(array_unique($group_procedures));
        //dd($request->all());
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $sec_part     = array();
        $sec_private  = array();
        $last_public  = array();
        //$array_todos = array();
        $t_factura = $request['tipo_fact'];
        //solo equipos
        if ($t_factura == 2) {
            $sec_part    = ParametersConglomerada::particulares($seguros);
            $sec_private = ParametersConglomerada::privados($seguros);
            $last_public = ParametersConglomerada::publicos($seguros);
        }
        $bodegas_hosp = Ct_Bodegas::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        if ($t_factura == 3) {
            return view('contable/ventas/createHonorarios', ['porIva' => $this->porIva, 'finalArray' => $final, 'tipo_tarjeta' => $tipo_tarjeta, 't_factura' => $t_factura, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'sec_part' => $sec_part, 'sec_private' => $sec_private, 'last_public' => $last_public, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas]);
        }
        return view('contable/ventas/create_facturaOmni', ['porIva' => $this->porIva, 'bodegas_hosp' => $bodegas_hosp, 'finalArray' => $final, 'tipo_tarjeta' => $tipo_tarjeta, 't_factura' => $t_factura, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'sec_part' => $sec_part, 'sec_private' => $sec_private, 'last_public' => $last_public, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'cuentas' => $cuentas]);
    }
    public function getReloadOmni(Request $request)
    {
        if (!is_null($request)) {
            $array_Pusher = array();
            $group2       = array();

            if (!empty($request->input("id_agenda"))) {
                for ($i = 0; $i < count($request->input("id_agenda")); $i++) {
                    $agenda = Agenda::find($request->input("id_agenda")[$i]);
                    //$patient= Paciente::find($request->input("paciente")[$i]);
                    $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
                        ->where('hc.id_agenda', $request->input("id_agenda")[$i])->get();
                    foreach ($procedimientos as $value) {
                        $texto = "";
                        if ($value->nombre_general == null) {
                            $adicionales = Hc_procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                            $mas         = true;
                            $texto       = "";
                            foreach ($adicionales as $value2) {
                                if ($mas == true) {
                                    $texto = $texto . $value2->procedimiento->nombre;
                                    $mas   = false;
                                } else {
                                    $texto = $texto . ' + ' . $value2->procedimiento->nombre;
                                }
                            }
                        }
                        $arr_AC = [];

                        //$productos =Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();
                        $productos                    = DB::table('movimiento_paciente as mp')->where('mp.id_hc_procedimientos', $value->id)->join('movimiento as m', 'm.id', 'mp.id_movimiento')->join('ct_productos_insumos as pro', 'pro.id_insumo', 'm.id_producto')->join('ct_productos as productos', 'productos.id', 'pro.id_producto')->groupBy('m.id_producto')->select(DB::raw('COUNT(DISTINCT(m.id)) AS cantidad'), 'productos.codigo as codigo', 'productos.nombre as nombre')->get();
                        $arr_AC['paciente_principal'] = $request->input("paciente")[$i];
                        $arr_AC['nombre_principal']   = $texto;
                        $arr_AC['hc_procedimiento']   = $value->id;
                        $arr_AC['fecha']              = $agenda->fechaini;
                        $arr_AC['agenda']             = $agenda->id;
                        $productosAC                  = [];
                        foreach ($productos as $xsa) {
                            //$arr_AC['productos']=$xsa;
                            //dd($xsa);
                            array_push($productosAC, $xsa);
                        }
                        $arr_AC['productos'] = $productosAC;
                        //update date 19 January 2021
                        //update date 16 March
                        //dd($value,$agenda->seguro);

                        $seguro            = DB::table('seguros')->where('id', $value->id_seguro)->first();
                        $arr_AC['seguros'] = $seguro->nombre;
                        array_push($array_Pusher, $arr_AC);
                    }
                }
                //dd($array_Pusher);
                foreach ($array_Pusher as $array_Pusher) {
                    if (!empty($array_Pusher)) {
                        //dd($array_Pusher);
                        $group2[$array_Pusher['paciente_principal']][] = $array_Pusher;
                    }
                }
            }
            //dd($group2);
            return $group2;
        } else {
            return response()->json("error no llega el request");
        }
    }
    public function getReloadRecibo(Request $request)
    {
        if (!is_null($request)) {
            $array_Pusher = array();
            $group2       = array();

            if (!empty($request->input("id_agenda"))) {
                $agenda         = Agenda::find($request->input("id_agenda"));
                $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
                    ->where('hc.id_agenda', $request->input("id_agenda"))->get();
                foreach ($procedimientos as $value) {
                    $texto = "";
                    if ($value->nombre_general == null) {
                        $adicionales = Hc_procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                        $mas         = true;
                        $texto       = "";
                        foreach ($adicionales as $value2) {
                            if ($mas == true) {
                                $texto = $texto . $value2->procedimiento->nombre;
                                $mas   = false;
                            } else {
                                $texto = $texto . ' + ' . $value2->procedimiento->nombre;
                            }
                        }
                    }
                    $arr_AC = [];

                    //$productos =Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();
                    $productos = DB::table('movimiento_paciente as mp')->where('mp.id_hc_procedimientos', $value->id)->join('movimiento as m', 'm.id', 'mp.id_movimiento')->join('ct_productos_insumos as pro', 'pro.id_insumo', 'm.id_producto')->join('ct_productos as productos', 'productos.id', 'pro.id_producto')->groupBy('m.id_producto')->select(DB::raw('COUNT(DISTINCT(m.id)) AS cantidad'), 'productos.codigo as codigo', 'productos.nombre as nombre')->get();

                    $arr_AC['paciente_principal'] = $agenda->paciente->id;
                    $arr_AC['nombre_principal']   = $texto;
                    $arr_AC['hc_procedimiento']   = $value->id;
                    $arr_AC['fecha']              = $agenda->fechaini;
                    $arr_AC['agenda']             = $agenda->id;
                    $productosAC                  = [];
                    foreach ($productos as $xsa) {
                        //$arr_AC['productos']=$xsa;
                        //dd($xsa);
                        array_push($productosAC, $xsa);
                    }
                    $arr_AC['productos'] = $productosAC;
                    //update date 19 January 2021
                    $arr_AC['seguros'] = $agenda->seguro->nombre;
                    array_push($array_Pusher, $arr_AC);
                }
                //dd($array_Pusher);
                foreach ($array_Pusher as $array_Pusher) {
                    if (!empty($array_Pusher)) {
                        //dd($array_Pusher);
                        $group2[$array_Pusher['paciente_principal']][] = $array_Pusher;
                    }
                }
            }
            //dd($group2);
            return $group2;
        } else {
            return response()->json("error no llega el request");
        }
    }
    public function store_omni(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //aqui ando
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $llevaOrden = false;
        $validate   = $request['validate'];
        $numero     = $request->numero;
        //$idusuario  = Auth::user()->id;
        $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $nfactura        = 0;
        $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $num_comprobante = 0;
        //dd($request->all());
        $proced = $request['procedimiento'];
        if ($request['sucursal'] != 0) {
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;
            $proced     = $request['procedimiento'];
            //if($idusuario != "0957258056"){
            if (!is_null($numero)) {
                $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
            /*}else{
        if (!is_null($numero)) {

        $numero1 = $this->valida_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja, $numero);

        if (is_null($numero1)) {
        $nfactura = $numero;
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }else {
        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }
        } else {

        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }
        }*/
        } else {
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = $request['procedimiento'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        //**
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //*
        //*
        //******GUARDAdo TABLA ASIENTO CABECERA********
        //*
        if ($request['tipo_factura'] == 3) {
            //dd("sa");
            $get = $this->getHonorarios($request);
        } else {
            DB::beginTransaction();
            $msj = "no    o";
            try {
                $input_cabecera = [
                    'fecha_asiento'   => $fecha_as,
                    'fact_numero'     => $nfactura,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => $text,
                    'valor'           => $request['total1'],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
                //$id_asiento_cabecera = 0;
                //GUARDAdo TABLA CT_VENTA.
                $factura_venta = [
                    'sucursal'            => $cod_sucurs->codigo_sucursal,
                    'punto_emision'       => $cod_caj->codigo_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => $id_empresa,
                    'tipo'                => $request['tipo'],
                    'fecha'               => $request['fecha_asiento'],
                    'divisas'             => $request['divisas'],
                    'nombre_cliente'      => $request['nombre_cliente'],
                    'tipo_consulta'       => $request['tipo_consulta'],
                    'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente'   => $request['direccion_cliente'],
                    'ruc_id_cliente'      => $request['identificacion_cliente'],
                    'telefono_cliente'    => $request['telefono_cliente'],
                    'email_cliente'       => $request['mail_cliente'],
                    'orden_venta'         => $request['orden_venta'],
                    'nro_autorizacion'    => $request['numero_autorizacion'],
                    'id_paciente'         => $request['identificacion_paciente'],
                    'nombres_paciente'    => $request['pacienteinfo'],
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente'     => $request['segurosinfo'],
                    'procedimientos'      => $request['procedimientoinfo'],
                    'fecha_procedimiento' => $request['fecha_proced'],
                    'copago'              => $request['totalc'],
                    'id_recaudador'       => $request['cedula_recaudador'],
                    'subtotal_0'          => $request['subtotal_01'],
                    'subtotal_12'         => $request['subtotal_121'],
                    'descuento'           => $request['descuento1'],
                    'base_imponible'      => $request['subtotal_121'],
                    'valor_contable'      => $request['total1'],
                    'impuesto'            => $request['tarifa_iva1'],
                    'total_final'         => $request['total1'],
                    'ip_creacion'         => "OMNI",
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];

                $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
                $arr_total      = [];
                $total_iva      = 0;
                $total_impuesto = 0;
                $total_0        = 0;
                $arr_id_hc      = [];
                $arr_activos    = [];
                $cont           = 0;
                $arr_obs        = [];
                if (!empty($request->input("veractivo"))) {
                    for ($i = 0; $i < count($request->input("veractivo")); $i++) {
                        if ($request->input("veractivo")[$i] == 1) {
                            $ar = $request->input("hc_procedimiento")[$i];
                            array_push($arr_activos, $ar);
                            $aobs = [
                                'procedimiento' => $request->input("hc_procedimiento")[$i],
                                'obs'           => $request->input("nom_paciente")[$i] . " - " . $request->input("obs_paciente")[$i],
                            ];
                            array_push($arr_obs, $aobs);
                        }
                    }
                }

                $observacion = "";
                if (!empty($request->input("nombre"))) {
                    for ($i = 0; $i < count($request->input("nombre")); $i++) {
                        if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                            for ($k = 0; $k < sizeof($arr_obs); $k++) {
                                if ($arr_obs[$k]['procedimiento'] == $request->input('id_hc_proc')[$i]) {
                                    $observacion = $arr_obs[$k]['obs'];
                                }
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $precio_neto = "";
                            if (isset($request->input("precioneto")[$i])) {
                                $precio_neto = $request->input("precioneto")[$i];
                            }
                            $nombre = "";
                            if (isset($request->input("nombre")[$i])) {
                                $nombre = $request->input("nombre")[$i];
                            }
                            $cantidad = 0;
                            if (isset($request->input("cantidad")[$i])) {
                                $cantidad = $request->input("cantidad")[$i];
                            }
                            $codigo = "";
                            if (isset($request->input("codigo")[$i])) {
                                $codigo = $request->input("codigo")[$i];
                            }
                            $nombre_procedimiento = "";
                            if (isset($request->input("id_principal")[$i])) {
                                $nombre_procedimiento = $request->input("id_principal")[$i];
                            }
                            $fecha_procedimiento = "";
                            if (isset($request->input("fecha_procedimiento")[$i])) {
                                $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                            }
                            $precio = 0;
                            if (isset($request->input("precio")[$i])) {
                                $precio = $request->input("precio")[$i];
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $iva = "";
                            if (isset($request->input("iva")[$i])) {
                                $iva = $request->input("iva")[$i];
                            }
                            $descuento = "";
                            if (isset($request->input("desc")[$i])) {
                                $descuento = $request->input("desc")[$i];
                            }
                            $descrip_prod = "";
                            if (isset($request->input("descrip_prod")[$i])) {
                                $descrip_prod = $request->input("descrip_prod")[$i];
                            }
                            $id_hc_proc = "";
                            if (isset($request->input("id_hc_proc")[$i])) {
                                $id_hc_proc = $request->input("id_hc_proc")[$i];
                            }
                            $id_agenda = "";
                            if (isset($request->input("id_agenda")[$i])) {
                                $id_agenda = $request->input("id_agenda")[$i];
                            }
                            $hc_procedimiento = "";
                            if (isset($request->input("hc_procedimiento")[$i])) {
                                $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                            }

                            $bodega_in = "";
                            if (isset($request->input("bodega")[$i])) {
                                $bodega_in = $request->input("bodega")[$i];
                            }

                            $precioneto = $cantidad * $precio;
                            $arr        = [
                                'nombre'     => $nombre,
                                'cantidad'   => $cantidad,
                                'codigo'     => $codigo,
                                'precio'     => $precio,
                                'descpor'    => "0.12",
                                'copago'     => "",
                                'descuento'  => $descuento,
                                'precioneto' => $precio_neto,
                                'detalle'    => $descrip_prod,
                                'iva'        => $iva,
                                'id_hc_proc' => $hc_procedimiento,
                                'obs_pac'    => $observacion,
                                'bodega_in'  => $bodega_in,
                            ];
                            if ($request["tipo_servicio"] == "2") {
                                $id_hc = ['id' => $request->input('id_hc_proc')[$i]];
                                if (in_array($request->input('id_hc_proc')[$i], $arr_activos)) {
                                    // array_push($arr, [);
                                    array_push($arr_total, $arr);
                                    // $cont++;
                                    array_push($arr_id_hc, $id_hc);
                                }
                            } elseif ($request["tipo_servicio"] == "1") {
                                array_push($arr_total, $arr);
                            }
                        }
                    }
                }

                $arr_consultas = [];
                if (!empty($request->input("prd_id"))) {
                    for ($i = 0; $i < count($request->input("prd_id")); $i++) {
                        if ($request->input("prd_id")[$i] != "" || $request->input("prd_id")[$i] != null) {
                            $arr_con = [
                                'value'      => $request->input("prd_val")[$i],
                                'prd_id'     => $request->input("prd_id")[$i],
                                'prd_activo' => $request->input("prd_activo")[$i],
                            ];
                            array_push($arr_consultas, $arr_con);
                        }
                    }
                }
                // $ids_consultas = json_encode($request->input('id_hc_consulta'));

                foreach ($arr_total as $valor) {
                    //dd($valor);
                    if ($valor['copago'] > 0) {
                        //registra orden de venta
                        $llevaOrden = true;
                    }
                    $detalle = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'descuento'            => $valor['descuento'],
                        'extendido'            => $valor['precioneto'],
                        'detalle'              => $valor['detalle'],
                        'copago'               => $valor['copago'],
                        'check_iva'            => $valor['iva'],
                        'codigo'               => $valor['obs_pac'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'bodega'               => $valor['bodega'],

                    ];

                    Ct_detalle_venta::create($detalle); //activo
                }

                //*
                //***MODULO CUENTA POR COBRAR***
                //
                //cUENTAS X COBRAR CLIENTES
                // --activo
                $val_tol = $request['total1'];

                if ($val_tol > 0) {
                    $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

                    //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => $request['total1'],
                        'haber'               => '0',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }

                //    2.01.07.01.01 iva sobre ventas
                if ($request['tarifa_iva1'] > 0) {
                    $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');

                    //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['tarifa_iva1'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
                // 4.1.01.02    Ventas Mercaderia Tarifa 12%
                if ($request['subtotal_121'] > 0) {
                    $id_plan_confg = LogConfig::busqueda('4.1.01.02');

                    //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['subtotal_121'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }

                // 4.1.01.01    Ventas Mercaderia Tarifa 0%
                if ($request['subtotal_01'] > 0) {

                    $id_plan_confg = LogConfig::busqueda('4.1.01.01');

                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['subtotal_01'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }

                //****REGISTRO FORMAS DE PAGO*** --activo falta****
                $arr_p = [];
                if (!empty($request->input("id_tip_pago"))) {
                    for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                        if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                            $arr_pagos = [
                                'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                                'fecha_pago'     => $request->input("fecha_pago")[$i],
                                'numero_pago'    => $request->input("numero_pago")[$i],
                                'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                                'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                                'giradoa'        => $request->input("giradoa")[$i],
                                'valor'          => $request->input("valor")[$i],
                                'valor_base'     => $request->input("valor_base")[$i],
                            ];
                            array_push($arr_p, $arr_pagos);
                        }
                    }
                }

                foreach ($arr_p as $valor) {
                    Ct_Forma_Pago::create([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                        'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                        'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                        'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                        'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                        'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                        'valor'           => $valor['valor'], //$request['valor'.$i],
                        'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }

                //agregar comprobantes de ingreso
                $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
                $arr_agendas = [];
                if ($request->tipo_factura == 1) {
                    foreach ($arr_consultas as $valor) {
                        if ($valor['prd_activo'] == "1") {
                            Ct_factura_procedimiento::create([
                                'id_ct_ventas'         => $id_venta,
                                'id_hc_procedimientos' => $valor['prd_id'],
                                'estado'               => "1",
                                'id_usuariocrea'       => $idusuario,
                                'id_usuariomod'        => $idusuario,
                                'ip_creacion'          => $ip_cliente,
                                'ip_modificacion'      => $ip_cliente,
                            ]);
                        }
                    }

                    if ($llevaOrden) {
                        $orden_id     = $this->ordenVenta($id_venta, $request);
                        $id_ct_ventas = [

                            'id_ct_venta' => $id_venta,

                        ];

                        Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                    } else {
                        $orden_id = 0;
                    }
                    $arr_agendas = [];
                    if (!empty($request->input("hc_procedimiento"))) {

                        for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {

                            if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $precio_neto = "";
                                if (isset($request->input("precioneto")[$i])) {
                                    $precio_neto = $request->input("precioneto")[$i];
                                }
                                $nombre = "";
                                if (isset($request->input("nombre")[$i])) {
                                    $nombre = $request->input("nombre")[$i];
                                }
                                $cantidad = 0;
                                if (isset($request->input("cantidad")[$i])) {
                                    $cantidad = $request->input("cantidad")[$i];
                                }
                                $codigo = "";
                                if (isset($request->input("codigo")[$i])) {
                                    $codigo = $request->input("codigo")[$i];
                                }
                                $nombre_procedimiento = "";
                                if (isset($request->input("id_principal")[$i])) {
                                    $nombre_procedimiento = $request->input("id_principal")[$i];
                                }
                                $fecha_procedimiento = "";
                                if (isset($request->input("fecha_procedimiento")[$i])) {
                                    $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                                }
                                $precio = 0;
                                if (isset($request->input("precio")[$i])) {
                                    $precio = $request->input("precio")[$i];
                                }
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $iva = "";
                                if (isset($request->input("iva")[$i])) {
                                    $iva = $request->input("iva")[$i];
                                }
                                $descuento = "";
                                if (isset($request->input("desc")[$i])) {
                                    $descuento = $request->input("desc")[$i];
                                }
                                $descrip_prod = "";
                                if (isset($request->input("descrip_prod")[$i])) {
                                    $descrip_prod = $request->input("descrip_prod")[$i];
                                }
                                $id_hc_proc = "";
                                if (isset($request->input("id_hc_proc")[$i])) {
                                    $id_hc_proc = $request->input("id_hc_proc")[$i];
                                }
                                $id_agenda = "";
                                if (isset($request->input("id_agenda")[$i])) {
                                    $id_agenda = $request->input("id_agenda")[$i];
                                }
                                $hc_procedimiento = "";
                                if (isset($request->input("hc_procedimiento")[$i])) {
                                    $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                                }
                                $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre];
                                array_push($arr_agendas, $arr_ags);
                            }
                        }
                    }
                    $contador = 1;
                    foreach ($arr_agendas as $agenda) {
                        $id_omni = Ct_Factura_Omni::insertGetId([
                            'id_ct_ventas'    => $id_venta,
                            'id_agenda'       => $agenda["id_agenda"],
                            'id_paciente'     => $agenda["id_paciente"],
                            'tipo_factura'    => $request['tipo_factura'],
                            'fecha'           => $agenda['fecha_procedimiento'],
                            'estado'          => "1",
                            'id_empresa'      => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ]);
                        $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                        $serie       = "";

                        if ($agenda['hc_procedimiento'] != null) {

                            $sx = Movimiento_Paciente::where('id_hc_procedimientos', $agenda['hc_procedimiento'])->get();

                            if (!is_null($sx)) {
                                foreach ($sx as $p) {

                                    $movimiento = Movimiento::find($p->id_movimiento);

                                    if (!is_null($movimiento)) {

                                        $find = Ct_Detalle_Venta_Omni::where('serie', $movimiento->serie)->where('id_hc_procedimiento', $agenda['hc_procedimiento'])->first();
                                        if (is_null($find)) {
                                            $serie = $movimiento->serie;
                                        } else {
                                            //$serie= $p->serie;
                                        }
                                    }
                                }
                            }
                        }
                        Ct_Detalle_Venta_Omni::create([
                            'id_ct_ventas'         => $id_venta,
                            'id_ct_productos'      => $agenda['producto'],
                            'nombre'               => $agenda['producto'],
                            'id_omni'              => $id_omni,
                            'cantidad'             => $agenda['cantidad'],
                            'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                            'id_agenda'            => $agenda['id_agenda'],
                            'nombre_principal'     => $agenda['nombre_principal'],
                            'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                            'precio'               => $agenda['precio'],
                            'serie'                => $serie, //serie
                            'descuento_porcentaje' => $request['iva_real'],
                            'extendido'            => $precio_neto,
                            'id_paciente'          => $agenda['id_paciente'],
                            'detalle'              => $agenda['descripcion'],
                            'check_iva'            => $agenda['check_iva'],
                            'ip_creacion'          => $ip_cliente,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                        ]);
                    }
                    $data['id']   = $id_venta;
                    $data['tipo'] = 'VEN-FA';
                    $data['omni'] = '1';
                    $msj          = Ct_Kardex::generar_kardex($data);
                } else {
                    //dd("dsadsa");
                    $arr_agendas = [];
                    if (!empty($request->input("codigo"))) {
                        for ($i = 0; $i < count($request->input("codigo")); $i++) {
                            if ($request->input("codigo")[$i] != "" || $request->input("codigo")[$i] != null) {
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $precio_neto = "";
                                if (isset($request->input("precioneto")[$i])) {
                                    $precio_neto = $request->input("precioneto")[$i];
                                }
                                $nombre = "";
                                if (isset($request->input("nombre")[$i])) {
                                    $nombre = $request->input("nombre")[$i];
                                }
                                $cantidad = 0;
                                if (isset($request->input("cantidad")[$i])) {
                                    $cantidad = $request->input("cantidad")[$i];
                                }
                                $codigo = "";
                                if (isset($request->input("codigo")[$i])) {
                                    $codigo = $request->input("codigo")[$i];
                                }
                                $nombre_procedimiento = "";
                                if (isset($request->input("id_principal")[$i])) {
                                    $nombre_procedimiento = $request->input("id_principal")[$i];
                                }
                                $fecha_procedimiento = "";
                                if (isset($request->input("fecha_procedimiento")[$i])) {
                                    $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                                }
                                $precio = 0;
                                if (isset($request->input("precio")[$i])) {
                                    $precio = $request->input("precio")[$i];
                                }
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $iva = "";
                                if (isset($request->input("iva")[$i])) {
                                    $iva = $request->input("iva")[$i];
                                }
                                $descuento = "";
                                if (isset($request->input("desc")[$i])) {
                                    $descuento = $request->input("desc")[$i];
                                }
                                $descrip_prod = "";
                                if (isset($request->input("descrip_prod")[$i])) {
                                    $descrip_prod = $request->input("descrip_prod")[$i];
                                }
                                $id_agenda = "";
                                if (isset($request->input("id_agenda")[$i])) {
                                    $id_agenda = $request->input("id_agenda")[$i];
                                }
                                $hc_procedimiento = "";
                                if (isset($request->input("hc_procedimiento")[$i])) {
                                    $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                                }
                                $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento];
                                array_push($arr_agendas, $arr_ags);
                            }
                        }
                    }
                    foreach ($arr_agendas as $agenda) {

                        $id_omni = Ct_Factura_Omni::insertGetId([
                            'id_ct_ventas'    => $id_venta,
                            'id_agenda'       => $agenda['id_agenda'],
                            'fecha'           => $agenda['fecha_procedimiento'],
                            'id_paciente'     => $agenda["id_paciente"],
                            'tipo_factura'    => $request['tipo_factura'],
                            'estado'          => "1",
                            'id_empresa'      => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ]);
                        $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                        Ct_Detalle_Venta_Omni::create([
                            'id_ct_ventas'         => $id_venta,
                            'id_omni'              => $id_omni,
                            'id_ct_productos'      => $agenda['producto'],
                            'nombre'               => $agenda['producto'],
                            'cantidad'             => $agenda['cantidad'],
                            'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                            'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                            'nombre_principal'     => $agenda['nombre_principal'],
                            'precio'               => $agenda['precio'],
                            'descuento_porcentaje' => $request['iva_real'],
                            'extendido'            => $precio_neto,
                            'id_paciente'          => $agenda['id_paciente'],
                            'detalle'              => $agenda['descripcion'],
                            'check_iva'            => $agenda['check_iva'],
                            'ip_creacion'          => $ip_cliente,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                        ]);
                    }
                }
                $data['id']   = $id_venta;
                $data['tipo'] = 'VEN-FA';
                $msj          = Ct_Kardex::generar_kardex($data);
                DB::commit();
                return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'arr_consultas' => $arr_consultas, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'llevaOrden' => $llevaOrden, 'arr_agendas' => $arr_agendas, 'kardex' => $msj, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
            } catch (\Exception $e) {

                DB::rollBack();
                return $e->getMessage();
            }
        }

        return $request;
    }
    public function search(Request $request)
    {
        $this->validateSalesListingRequest($request, true);

        $usuario_actual    = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $permitidos = ParametersConglomerada::pdf_permitidos($id_empresa);
        $dato = Ct_ventas::getBusqueda($request);
        $this->deferElectronicInvoiceStatusRefresh($id_empresa);

        return view('contable/ventas/index', ['empresa' => $empresa, 'dato' => $dato, 'permitidos' => $permitidos, "usuario_actual" => $usuario_actual]);
    }

    private function deferElectronicInvoiceStatusRefresh(int|string $idEmpresa): void
    {
        defer(function () use ($idEmpresa): void {
            $pendingInvoices = Ct_ventas::query()
                ->where('estado', 1)
                ->where('id_empresa', $idEmpresa)
                ->where('tipo', 'VEN-FA')
                ->where('electronica', 1)
                ->where('punto_emision', '!=', 100)
                ->where('estado_electronica', '<>', 3)
                ->whereYear('fecha', '>', 2021)
                ->where('created_at', '<=', now()->subMinutes(5))
                ->orderByDesc('id')
                ->limit(10)
                ->get(['id', 'nro_comprobante']);

            foreach ($pendingInvoices as $invoice) {
                try {
                    $response = ApiFacturacionController::estado_comprobante([
                        'empresa' => $idEmpresa,
                        'comprobante' => $invoice->nro_comprobante,
                        'tipo' => 'comprobante',
                    ]);
                } catch (\Throwable) {
                    continue;
                }

                $process = data_get($response, 'details.log.0.proceso');
                $message = data_get($response, 'details.log.0.mensaje');

                if ($process === null && $message === null) {
                    continue;
                }

                $electronicStatus = match (true) {
                    $process === 'Notificación', $message === 'Autorizado' => 3,
                    $message === 'Documento recibido' => 2,
                    $process === 'Firma' => 1,
                    default => 0,
                };

                $attributes = ['estado_electronica' => $electronicStatus];
                $authorization = data_get($response, 'details.autorizacion');

                if ($electronicStatus === 3 && is_scalar($authorization)) {
                    $attributes['nro_autorizacion'] = (string) $authorization;
                }

                Ct_ventas::whereKey($invoice->id)->update($attributes);
            }
        }, 'ventas.refresh-electronic-status.'.$idEmpresa);
    }

    private function validateSalesListingRequest(Request $request, bool $includeAdvancedFilters = false): void
    {
        $rules = [
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];

        if ($includeAdvancedFilters) {
            $rules += [
                'id' => ['nullable', 'string', 'max:30'],
                'numero' => ['nullable', 'string', 'max:50'],
                'id_asiento' => ['nullable', 'string', 'max:30'],
                'fecha' => ['nullable', 'date_format:Y-m-d'],
                'nombre_cliente' => ['nullable', 'string', 'max:150'],
                'nombres_paciente' => ['nullable', 'string', 'max:150'],
            ];
        }

        $request->validate($rules);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {
        $query = Ct_ventas::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                if ($fields[$index] == "id" || $fields[$index] == "id_asiento") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(20);
    }

    private function groupedSalesDetails(int|string $saleId): Collection
    {
        return DB::table('ct_detalle_venta as ct_detalle')
            ->where('ct_detalle.id_ct_ventas', $saleId)
            ->where('ct_detalle.estado', 1)
            ->select('ct_detalle.id_ct_productos')
            ->selectRaw('MAX(ct_detalle.nombre) as nombre')
            ->selectRaw('SUM(ct_detalle.precio) as precio')
            ->selectRaw('SUM(ct_detalle.cantidad) as cantidad')
            ->selectRaw('SUM(ct_detalle.descuento) as descuento')
            ->groupBy('ct_detalle.id_ct_productos')
            ->get();
    }

    public function editar($id, Request $request, FindSalesInvoiceForCompany $findSalesInvoice)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $salesInvoice = $findSalesInvoice->handle((int) $id, (string) $id_empresa);
        $ventas = $salesInvoice?->toLegacyRecord();

        if ($ventas === null) {
            abort(404);
        }

        $detalle_venta = DB::table('ct_detalle_venta as ct_det_vent')
            ->where('ct_det_vent.id_ct_ventas', $id)
            ->where('ct_det_vent.estado', '1')
            ->select('ct_det_vent.*')
            ->get();
        $detalle_venfaco = $this->groupedSalesDetails($id);
        $clientes   = Ct_Clientes::where('estado', '1')->get();
        $forma_pago = DB::table('ct_forma_pago')
            ->where('id_ct_ventas', $id)
            ->where('estado', '1')
            ->get();

        if (Auth::user()->id == '0951561075') {
            //  dd($forma_pago);
        }
        $tip_tarjeta     = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $banco           = Ct_Bancos::where('estado', '1')->get();
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $empresa         = Empresa::where('id', $id_empresa)->first();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $tipo_pago   = Ct_Tipo_Pago::where('estado', '1')->get();
        $cuentas     = Plan_Cuentas::where('estado', '2')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        return view('contable/ventas/edit', ['porIva' => $this->porIva, 'ventas' => $ventas, 'sucursales' => $sucursales, 'clientes' => $clientes, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'detalle_venta' => $detalle_venta, 'forma_pago' => $forma_pago, 'tip_tarjeta' => $tip_tarjeta, 'seguros' => $seguros, 'banco' => $banco, 'divisas' => $divisas, 'empresa' => $empresa, 'tipo_pago' => $tipo_pago, 'cuentas' => $cuentas, 'detalle_venfaco' => $detalle_venfaco, 'lista_banco' => $lista_banco]);
    }
    public function excelConglomerada($id_venta)
    {
        $venta = Ct_ventas::find($id_venta);
        $detalle = [];
        if ($venta->tipo == 'VENFA-CO') {
            $detalle = Ct_Detalle_Venta_Conglomerada::where('id_ct_ventas', $venta->id)->get();
        } else if ($venta->omni == 1) {
            $detalle = Ct_Detalle_Venta_Omni::where('id_ct_ventas', $venta->id)->get()->toArray();
        }
        // dd($venta, $detalle);
        Excel::create('Fact. Conglomerada', function ($excel) use ($venta, $detalle) {
            $excel->sheet('Fact. Conglomerada', function ($sheet) use ($venta, $detalle) {
                $forma_pago = DB::table('ct_forma_pago')
                    ->where('id_ct_ventas', $venta->id)
                    ->where('estado', '1')
                    ->get();
                $comienzo = 1;
                $titulo = $venta->tipo == 'VENFA-CO' ? 'FACTURA CONGLOMERADA' : '';
                $title["data"] = [$titulo];
                $title["direccion"] = "normal";
                $title["letra_inicial"] = "A";
                $title["letra_final"] = "F";
                $title["num_inicial"] = $comienzo;
                $title["num_final"]  = $comienzo;
                // $tile['background-color'] = "#FFFFFF";
                excelCreate::mergeCells($sheet, $title);
                $comienzo++;

                $titles = ["Cod principal", "Descripción", "Cantidad", "P. unitario", "Descuento", "P. total"];

                foreach ($detalle as $value) {
                    $nombre_paciente = "";
                    if (isset($value->paciente)) {
                        $nombre_paciente = "{$value->paciente->nombre1} {$value->paciente->apellido1} {$value->paciente->apellido2}";
                    } else if (isset($value->cliente)) {
                        $nombre_paciente = "{$value->cliente->nombre}";
                    }
                    $title["data"] = ["CLIENTE:  {$nombre_paciente}"];
                    $title["num_inicial"] = $comienzo;
                    $title["num_final"]  = $comienzo;
                    $title['background-color'] = "#B8B9FD";

                    excelCreate::mergeCells($sheet, $title);
                    $comienzo++;

                    $titulos_detalle["data"] = $titles;
                    $titulos_detalle["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $titulos_detalle);
                    $comienzo++;

                    $producto = Ct_productos::where('codigo', $value->id_ct_productos)->where('id_empresa', Session::get('id_empresa'))->first();
                    // dd($value, $producto);

                    $detalle["data"] = [$producto->codigo, $producto->nombre, $value->cantidad, $value->precio, $value->descuento, $value->extendido];
                    $detalle["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $detalle);
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }
    public function buscarCliente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();
        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $clientes = Ct_Clientes::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->get();
        foreach ($clientes as $cliente) {
            $data[] = array('value' => $cliente->nombre, 'id' => $cliente->identificacion, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarClientexId(Request $request)
    {
        //dd($request->valor);
        $busqueda = 'identificacion';
        $nombre   = $request['term'];
        $data     = array();
        if ($request->busq == "identificacion2") {
            $busqueda = "identificacion";
        }
        // dd($request->all());
        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $clientes = Ct_Clientes::where($busqueda, $request->valor)->where('estado', '1')->first();
        //dd($clientes);
        // foreach ($clientes as $cliente) {
        //     $data[] = array('value' => $cliente->identificacion, 'nombre' => $cliente->nombre, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        // }
        // // print_r($clientes);
        // if (count($data) > 0) {
        //     return $data;
        // } else {
        //     return ['value' => 'No se encontraron resultados', 'id' => ''];
        // }
        return $clientes;
    }
    public function buscarPaciente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();

        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $paciente = Paciente::where('id', 'like', $nombre . '%')->get();
        foreach ($paciente as $value) {
            $nom_completo = "$value->nombre1 $value->nombre2 $value->apellido1 $value->apellido2";
            $data[]       = array('value' => $value->id, 'nombre' => $nom_completo, 'seguro' => $value->id_seguro);
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarPaciente_nombre(Request $request)
    {
        $nombres  = $request['term'];
        $data     = array();
        $paciente = Paciente::where('id', '<>', '7777773333');
        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            if ($cantidad == '2' || $cantidad == '3') {
                $paciente = $paciente->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {
                $paciente = $paciente->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        $paciente = $paciente->get();
        foreach ($paciente as $value) {
            $nom_completo = "$value->nombre1 $value->nombre2 $value->apellido1 $value->apellido2";
            $data[]       = array('value' => $nom_completo, 'nombre' => $value->id, 'seguro' => $value->id_seguro);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarProducto(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('ct_productos')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            $data = $productos->nombre;
            return $data;
        } else {
            //return ['value' => 'No se encontraron resultados'];
            return "error";
        }
    }
    public function buscar_identificacion(Request $request)
    {

        $id_cliente = $request['cliente'];
        $clientes   = DB::table('ct_clientes')->where('identificacion', $id_cliente)->first();
        if (!is_null($clientes)) {
            $client_cedula         = $clientes->cedula_representante;
            $client_direccion      = $clientes->direccion_representante;
            $client_identificacion = $clientes->identificacion;
            $client_telefono       = $clientes->telefono1_representante;
            $client_email          = $clientes->email_representante;
            $client_ciudad         = $clientes->ciudad_representante;
            return ['client_cedula' => $client_cedula, 'client_direccion' => $client_direccion, 'client_identificacion' => $client_identificacion, 'client_telefono' => $client_telefono, 'client_email' => $client_email, 'client_ciudad' => $client_ciudad];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscador_paciente(Request $request)
    {
        $paciente = [];
        if ($request['search'] != null) {
            $paciente = Paciente::whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '%" . $request['search'] . "%' ")->select(DB::raw('CONCAT_WS(" ", apellido1, " " , apellido2, " ", nombre1," ", nombre2) as text'), 'id as id')->get();
        }
        return response()->json($paciente);
    }
    //Vendedor
    public function buscar_identificacion_vendedor(Request $request)
    {
        $id_vendedor   = $request['vendedor'];
        $user_vendedor = User::where('id', $id_vendedor)
            ->where('estado', 1)->first();
        if (!is_null($user_vendedor)) {
            $vendedor_cedula = $user_vendedor->id;
            return ['vendedor_cedula' => $vendedor_cedula];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_identificacion_recaudador(Request $request)
    {
        $id_recaudador   = $request['recaudador'];
        $user_recaudador = User::where('id', $id_recaudador)
            ->where('estado', 1)->first();
        if (!is_null($user_recaudador)) {
            $recaudador_cedula = $user_recaudador->id;
            return ['recaudador_cedula' => $recaudador_cedula];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_nombre(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();
        $productos = DB::table('ct_productos')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->codigo);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscar_codigo(Request $request)
    {
        //return  $request->all();
        $nombre = $request['nombre'];
        $data = null;
        $productos = DB::table('ct_productos')->where('nombre', $nombre)->first();
        if (!is_null($productos)) {
            $prod_insum = DB::table('producto as prd')->where('codigo', $productos->codigo)
                ->select('prd.iva as iva_prod')
                ->first();
            $iva_prod = $prod_insum->iva_prod;
            $cod_product    = $productos->codigo;
            $prec_uno       = $productos->precio1;
            $prec_dos       = $productos->precio2;
            $prec_tres      = $productos->precio3;
            $prec_cuatr     = $productos->precio4;
            $prec_promocion = $productos->promocion;
            $cost_vent      = $productos->ultima_compra;

            return ['cod_product' => $cod_product, 'prec_uno' => $prec_uno, 'prec_dos' => $prec_dos, 'prec_tres' => $prec_tres, 'prec_cuatr' => $prec_cuatr, 'prec_promocion' => $prec_promocion, 'prec_promocion' => $prec_promocion, 'cost_vent' => $cost_vent, 'iva_prod' => $iva_prod];
            //return $productos;

        } else {

            return "error";
        }
    }
    public function buscar_codigo2(Request $request)
    {
        $codigo = $request['term'];
        $data = array();
        $productos = DB::table('ct_productos')->where('codigo', 'like', '%' . $codigo . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_nombre2(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('ct_productos')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            $data = $productos->nombre;
            return $data;
        } else {
            //return ['value' => 'No se encontraron resultados'];
            return "error";
        }
    }
    public function update_direccion(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $iden_cliente = $request['ident_cliente'];
        $direccion    = $request['direc_cliente'];

        if (!is_null($iden_cliente)) {

            $input_direccion = [

                'direccion_representante' => $direccion,

            ];

            Ct_Clientes::where('identificacion', $iden_cliente)->update($input_direccion);
        }
    }
    public function completa_cedula_pacie(Request $request)
    {
        $id_pacient = $request['term'];
        $data       = array();

        $paciente = DB::table('paciente')->where('id', 'like', '%' . $id_pacient . '%')->get();

        foreach ($paciente as $pac) {
            $data[] = array('value' => $pac->id);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_paciente(Request $request)
    {
        $ced_pacient = $request['ced_paciente'];

        $paciente = Paciente::find($ced_pacient);

        //return $paciente;

        if (!is_null($paciente)) {

            //$texto = $paciente->nombre1. ' ' . $paciente->nombre2. ' ' . $paciente->apellido1. ' ' .$paciente->apellido2;

            $texto = $paciente->nombre1 . ' ' . $paciente->apellido1;

            $seguro = Seguro::find($paciente->id_seguro);

            if (!is_null($seguro)) {

                $id_seg = $seguro->id;
            }

            return ['texto' => $texto, 'id_seg' => $id_seg];
        } else {

            return "error";
        }
    }
    /************************************************************
     ***************OBTENER SUCURSAL DE CADA EMPRESA**************
    /************************************************************/
    public function obtener_sucursal_empresa(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empre = $request['id_emp'];

        if (!is_null($id_empre)) {

            $suc_caja = DB::table('ct_sucursales as ct_s')
                ->where('ct_s.estado', 1)
                ->where('ct_s.id_empresa', $id_empre)
                ->get();

            return $suc_caja;
        }

        return 'no';
    }
    /************************************************************
     *************OBTENER LAS CAJAS DE CADA SUCURSAL**************
    /************************************************************/
    public function obtener_caja_sucursal(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_sucur = $request['id_sucur'];
        if (!is_null($id_sucur)) {
            $caja = DB::table('ct_sucursales as ct_s')
                ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
                ->where('ct_c.estado', 1)
                ->where('ct_s.id', $id_sucur)
                ->get();
            return $caja;
        }
        return 'no';
    }
    /************************************************************
     *************IMPRIMIR COMPROBANTE FACTURA DE VENTA***********
    /************************************************************/
    public function imprimir_comprobante_factura($id)
    {

        $fact_venta = Ct_ventas::findorfail($id);
        $ct_for_pag = Ct_Forma_Pago::where('id_ct_ventas', $id)->get();
        $ct_val_ret = Ct_Retencion_Fventas::where('id_ct_ventas', $id)->first();

        if ($fact_venta != null) {

            $emp             = Empresa::find($fact_venta->id_empresa);
            $recaud          = User::find($fact_venta->id_recaudador);
            $cliente         = Ct_Clientes::where('identificacion', $fact_venta->id_cliente)->first();
            $pacient         = Paciente::find($fact_venta->id_paciente);
            $deta_vent       = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
            $detalle_venfaco = $this->groupedSalesDetails($id);
        }

        $vistaurl = "contable.ventas.pdf_comprobante_tributario";
        $view     = \View::make($vistaurl, compact('fact_venta', 'emp', 'recaud', 'cliente', 'pacient', 'deta_vent', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function ordenes(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguro     = Seguro::where('inactivo', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        if ($request->fecha_hasta == null) {
            $request->fecha_hasta = date('Y-m-d');
        }
        $ventas = Ct_ven_orden::where('estado', '!=', null)->where('id_empresa', $id_empresa);
        if ($request['id'] != null) {
            $ventas = $ventas->where('id', $request->id);
        } else {
            if ($request->fecha_desde != null && $request->fecha_hasta != null) {
                $ventas = $ventas->whereBetween('fecha', [$request->fecha_desde . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
            }
            if ($request->fecha_desde == null) {
                $ventas = $ventas->where('fecha', '<', $request->fecha_hasta . ' 23:59:59');
            }
            if ($request->nombres_paciente != null) {
                $ventas = $ventas->where('nombres_paciente', 'LIKE', '%' . $request->nombres_paciente . '%');
            }
            if ($request->nombre_cliente != null) {
                $ventas = $ventas->where('nombre_cliente', 'LIKE', '%' . $request->nombre_cliente . '%');
            }
            if ($request->nro_comprobante != null) {
                $ventas = $ventas->where('nro_comprobante', 'LIKE', '%' . $request->nro_comprobante . '%');
            }
        }
        $ventas = $ventas->orderby('id', 'desc')->paginate(10);

        return view('contable/ventas/ordenes', ['ventas' => $ventas, 'empresa' => $empresa, 'request' => $request, 'seguro' => $seguro]);
    }
    public function ordenes_crear_nuevo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_orden = $request['id_orden'];
        $orden_venta = Ct_Orden_Venta::find($request['id_orden']);
        $paciente = Paciente::find($orden_venta->agenda->id_paciente);
        $proc_consulta = Agenda::where('id', $orden_venta->id_agenda)->first();
        $tipo_proc_con = "CONSULTA";
        $tipo_consulta = '1';
        if (!is_null($proc_consulta) && $proc_consulta->proc_consul == 1) {
            $tipo_proc_con = "PROCEDIMIENTO";
            $tipo_consulta = '2';
        }
        $request['tipo'] = 'VEN-OV';
        $request['fecha_asiento']   = $orden_venta->fecha_emision;
        $request['orden_venta']     = $orden_venta->id;
        $request['divisas']         = 1;
        $request['identificacion_cliente']   = $orden_venta->identificacion;
        $request['identificacion_paciente']   = $paciente->id;
        $request['nombre_cliente']   =   $orden_venta->razon_social;
        $request['nombre_paciente']   =   $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;;
        $request['direccion_cliente']   =  $orden_venta->direccion;
        $request['mail_cliente']   =  $orden_venta->email;
        $request['telefono_cliente']   =  $orden_venta->telefono;
        $request['id_seguro']  =  $orden_venta->id_seguro;
        $request['procedimiento']  = $tipo_proc_con;
        $request['fecha_proced']   =  $orden_venta->nueva_fecha;
        $request['tipo_consulta']  =  $tipo_consulta;
        $request['subtotal_121']   =  $orden_venta->subtotal_12;
        $request['subtotal_01']   =  $orden_venta->subtotal_0;
        $request['descuento1']   =  $orden_venta->descuento;
        $request['tarifa_iva1']  =  $orden_venta->iva;
        $request['total1']   =   $orden_venta->total;
        $request['totalc']   =   $orden_venta->valor_oda;
        $cont_codigo = 0;
        $orden_venta_detalle = Ct_Orden_Venta_Detalle::where('id_orden', $id_orden)->get();
        foreach ($orden_venta_detalle as $ov) {
            $id_codigo[$cont_codigo] = $ov->cod_prod;
            $nombre[$cont_codigo] = $ov->nombre_producto;
            $descrip_prod[$cont_codigo] = $ov->descripcion;
            $iva[$cont_codigo] = $ov->iva;
            $cantidad[$cont_codigo] = $ov->cantidad;
            $precio[$cont_codigo] = $ov->precio;
            $copago[$cont_codigo] = $ov->cobrar_paciente;
            $descpor[$cont_codigo] = $ov->p_dcto;
            $desc[$cont_codigo] = $ov->descuento;
            $precioneto[$cont_codigo] = $ov->precio;
            $cont_codigo++;
        }
        $request['codigo'] = $id_codigo;
        $request['nombre'] = $nombre;
        $request['descrip_prod'] = $descrip_prod;
        $request['iva'] = $iva;
        $request['cantidad'] = $cantidad;
        $request['precio'] = $precio;
        $request['copago'] = $copago > 0 ? $copago : $orden_venta->valor_oda;
        $request['descpor'] = $copago;
        $request['desc'] = $desc;
        $request['precioneto'] = $precioneto;
        $id_orden = null;
        $orden_id     = $this->ordenVenta($id_orden, $request);
        if (!is_null($orden_id)) {
            return ['msj' => 'si'];
        } else {
            return ['msj' => 'no'];
        }
    }
    public function searchOrdenes(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $ventas      = array();
        $constraints = [
            'id'               => $request['id'],
            'fecha_desde'      => $request['fecha_desde'],
            'fecha_hasta'      => $request['fecha_hasta'],
            'nombre_cliente'   => $request['nombre_cliente'],
            'nombres_paciente' => $request['nombres_paciente'],
            'nro_comprobante'  => $request['nro_comprobante'],
        ];
        if ($request['id'] != null || $request['numero'] != null || $request['fecha'] != null || $request['nombre_cliente'] != null || $request['nombres_paciente'] != null) {
            $constraints = [
                'id'               => $request['id'],
                'fecha_desde'      => $request['fecha_desde'],
                'fecha_hasta'      => $request['fecha_hasta'],
                'nombre_cliente'   => $request['nombre_cliente'],
                'nombres_paciente' => $request['nombres_paciente'],
                'nro_comprobante'  => $request['nro_comprobante'],
            ];

            $ventas = $this->doSearchingQuery2($constraints, $id_empresa);
        } else {
            $ventas = Ct_Ven_Orden::where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
        }
        $idusuario = Auth::user()->id;
        if ($idusuario == "0957258056") {
            // dd($constraints);
        }

        return view('contable/ventas/ordenes', ['ventas' => $ventas, 'empresa' => $empresa, 'searchingVals' => $constraints]);
    }
    private function doSearchingQuery2($constraints, $id_empresa)
    {
        $query = Ct_Ven_Orden::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }
        return $query->where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
    }
    /*************************************
     **CREAR FACTURAS DE VENTA DESDE ORDEN**
    /*************************************/
    public function crear_facturaOrden($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $clientes    = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago   = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros     = Seguro::all();
        $idusuario   = Auth::user()->id;
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $bodega = bodega::where('estado', '1')->get();
        //$venta  = Ct_ventas::findorfail($id); //::where("id", $id)->first();
        if ($idusuario == '0957258056') {
            // dd($venta);
        }
        // print_r($venta['id_empresa']);
        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();

        $id_plan_confg = LogConfig::busqueda('4.1.01.02');

        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $iva       = Ct_Configuraciones::where('id_plan', $id_plan_confg)->where('estado', '1')->first();

        $detalle = Ct_ven_orden_detalle::where("id_ct_ven_orden", $id)->get();

        $orden_venta = Ct_ven_orden::findorfail($id); //::where("id",$id)->get();//::where("id", $id)->first();
        //return $id;

        return view('contable/ventas/create_facturaOrden', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, "venta" => $orden_venta, "detalle" => $detalle]);
    }
    public function eliminar($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $ventas     = Ct_ven_orden::where('id', $id)->first();
        $inp        = [
            'estado'          => '0',
            'estado_pago'     => '0',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $ventas->update($inp);
        return redirect()->route('orden_venta');
    }
    //function to upload more data with differents values and move to process contable
    public function masivo_ventas(Request $request)
    {
        $ventas = Ct_ventas::where('id_empresa', '0993075000001')->get();
        //$facturas= $this->vf($book,$idusuario,$ip_cliente);
        //we can return excel invoker to load big data
        //$verd= $this->cliente_retencion();
        //$ahor = $this->egresoMasivo();
        //$kardex= $this->updateKardex();
        Excel::filter('chunk')->load('vfactura.xlsx')->chunk(250, function ($reader) {
            /*
            "fecha_de_emision" => "12/01/2021"
            "autorizacion" => "1201202101130718914000120010020000002781234567815"
            "numero_de_factura" => "001-002-000000278"
            "ruc" => "0991353119001"
            "cliente" => "INTERNATIONAL LABORATORIES SERVICES"
            "seguro" => "0000 General"
            "procedimiento" => "HONORARIOS MEDICOS "
            "paciente" => "11AG-10 OCT 2020"
            "fecha_de_procedimeinto" => "12/01/2021"
            "subtotal_12" => 0.0
            "subtotal_0" => 361.45
            "subtotal" => 361.45
            "descuento" => 0.0
            "iva" => 0.0
            "total" => 361.45
            "estado" => null*/
            $idusuario  = "1316262193";
            $ip_cliente = "subidach";
            foreach ($reader as $book) {
                //dd($book);
                $cliente = Ct_Clientes::where('identificacion', $book->ruc)->first();
                if (is_null($cliente)) {
                    Ct_Clientes::create([
                        'identificacion'          => $book->ruc,
                        'nombre'                  => $book->cliente,
                        'tipo'                    => '5',
                        'clase'                   => 'normal',
                        'nombre_representante'    => $book->cliente,
                        'cedula_representante'    => $book->ruc,
                        'ciudad_representante'    => 'GUAYAQUIL',
                        'telefono1_representante' => '11111',
                        'pais'                    => 'Ecuador',
                        'estado'                  => '1',
                        'id_usuariocrea'          => $idusuario,
                        'id_usuariomod'           => $idusuario,
                        'ip_creacion'             => $ip_cliente,
                        'ip_modificacion'         => $ip_cliente,
                    ]);
                }
                $input_cabecera = [
                    'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'fact_numero'     => $book->numero_de_factura,
                    'id_empresa'      => '1307189140001',
                    'observacion'     => 'Factura #' . $book->numero_de_factura,
                    'valor'           => $book->total,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                if (isset($book->estado)) {
                    if ($book->estado == "ANULADA") {
                        $input_cabecera = [
                            'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha_de_emision)),
                            'fact_numero'     => $book->factura,
                            'id_empresa'      => '0993075000001',
                            'estado'          => '0',
                            'observacion'     => 'Factura #' . $book->factura,
                            'valor'           => $book->total,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                    }
                }
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $punto               = $book->numero_de_factura;
                $sucursal            = substr($punto, 0, 3);
                $punto_emision       = substr($punto, 4, 3);
                $nfactura            = substr($punto, 8);
                $factura_venta       = [
                    'sucursal'            => $sucursal,
                    'punto_emision'       => $punto_emision,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $book->numero_de_factura,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => '1307189140001',
                    'tipo'                => 'VEN-FA',
                    'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'divisas'             => '1',
                    'nombre_cliente'      => $book->cliente,
                    'tipo_consulta'       => '1',
                    'id_cliente'          => $book->ruc, //nombre_cliente
                    'direccion_cliente'   => 'DIRECCION',
                    'ruc_id_cliente'      => $book->ruc,
                    'telefono_cliente'    => '1111',
                    'email_cliente'       => '',
                    'orden_venta'         => '',
                    'nro_autorizacion'    => $book->autorizacion, //autorizacion de humanlabs
                    'id_paciente'         => '9999999999',
                    'nombres_paciente'    => $book->paciente,
                    'id_hc_procedimiento' => '',
                    'seguro_paciente'     => $book->seguro,
                    'procedimientos'      => $book->procedimiento,
                    'fecha_procedimiento' => date('Y-m-d', strtotime($book->fecha_de_procedimeinto)),
                    'concepto'            => '',
                    'copago'              => '',
                    'ci_vendedor'         => '',
                    'vendedor'            => '',
                    //'nota'                          => $request['nota'],
                    'subtotal_0'          => $book->subtotal,
                    'subtotal_12'         => '0',
                    //'subtotal'                      => $request['subtotal1'],
                    'descuento'           => $book->descuento,
                    'base_imponible'      => $book->descuento,
                    'impuesto'            => '0',
                    // 'transporte'                    => $request['transporte'],
                    'total_final'         => $book->total,
                    'valor_contable'      => $book->total,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $id_venta = Ct_ventas::insertGetId($factura_venta);
                $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '1.01.02.05.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'debe'                => $book->total,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                // 4.1.01.01    Ventas Mercaderia Tarifa 0%
                $precio = 0;
                $iva    = 0;
                if ($book->subtotal_12 > 0) {
                    $precio       = $book->subtotal_12;
                    $iva          = 1;
                    $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '2.01.07.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                        'debe'                => '0',
                        'haber'               => $book->subtotal_12,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    $precio       = $book->subtotal_0;
                    $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '4.1.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                        'debe'                => '0',
                        'haber'               => $book->subtotal_0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => 'HON-MED',
                    'nombre'               => 'HON-MED',
                    'cantidad'             => '1',
                    'precio'               => $precio,
                    'descuento_porcentaje' => '',
                    'descuento'            => $book->descuento,
                    'extendido'            => $book->total,
                    'detalle'              => 'HON-MED',
                    'copago'               => '',
                    'check_iva'            => $iva,
                    'porcentaje'           => $this->porIva->iva,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];
                Ct_detalle_venta::create($detalle);
            }
        });
        return response()->json(['mensaje' => 'disculpe se guardo correctamente']);
    }
    public function cliente_retencion()
    {
        $cliente_retencion = Ct_Cliente_Retencion::all();
        foreach ($cliente_retencion as $retencion) {
            $fecha = "";
            foreach ($retencion->detalle_retencion as $value) {
                if ($value->fechaauto != null) {
                    $fecha = $value->fechaauto;
                }
            }
            $update = Ct_Cliente_Retencion::find($retencion->id);
            if ($update != null) {
                $update->fecha = $fecha;
                $update->save();
            }
        }
    }
    public function debitoBancario()
    { //No la hice porque no vi que se usa
        $default        = "";
        $debitoBancario = Ct_Debito_Bancario::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as           = Ct_Caja_Banco::find($values->id_banco);
            $cuenta_mayor = $as->cuenta_mayor;
            if (!is_null($values)) {
                $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento)->get();
                foreach ($asiento as $asi) {

                    if ($asi->id_plan_cuenta != "2.01.03.01.01" && $asi->id_plan_cuenta != "2.01.03.01.02") {
                        $asi = Ct_Asientos_Detalle::find($asi->id);
                        //dd($asi);
                        if (!is_null($asi)) {
                            $asi->id_plan_cuenta = $cuenta_mayor;
                            $asi->save();
                        }
                    }
                }
            }
        }
    }
    public function egresoVarios()
    {
        $default        = "";
        $debitoBancario = Ct_Comprobante_Egreso_Varios::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as           = Ct_Caja_Banco::find($values->id_caja_banco);
            $cuenta_mayor = $as->cuenta_mayor;
            if (!is_null($values)) {
                $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento_cabecera)->get();
                foreach ($asiento as $asi) {

                    if ($asi->debe == 0) {
                        $asi = Ct_Asientos_Detalle::find($asi->id);
                        //dd($asi,$cuenta_mayor);
                        if (!is_null($asi)) {
                            $asi->id_plan_cuenta  = $cuenta_mayor;
                            $asi->ip_modificacion = "sistemas";
                            $asi->save();
                        }
                    }
                }
            }
        }
    }
    public function egresoMasivo()
    {
        $default        = "";
        $debitoBancario = Ct_Comprobante_Egreso::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as = Ct_Caja_Banco::find($values->id_caja_banco);
            if (!is_null($as)) {
                $cuenta_mayor = $as->cuenta_mayor;
                if (!is_null($values)) {
                    $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento_cabecera)->get();
                    foreach ($asiento as $asi) {

                        if ($asi->debe == 0) {
                            $asi = Ct_Asientos_Detalle::find($asi->id);
                            //dd($asi,$cuenta_mayor);
                            if (!is_null($asi)) {
                                $asi->id_plan_cuenta  = $cuenta_mayor;
                                $asi->ip_modificacion = "sistemas";
                                $asi->save();
                            }
                        }
                    }
                }
            }
        }
    }
    public function identificar_pagos($pago)
    {
        $id_ = "";
        if ($pago->tipo == 'CREDITO') {
            switch ($pago) {
                case 'MASTERCARD':
                    $id_ = 2;
                    break;
                case 'VISA':
                    $id_ = 1;
                    break;
                case 'PACIFICARD':
                    $id_ = 3;
                    break;
                case 'DINNERS':
                    $id_ = 4;
                    break;
                case 'BANCO PICHINCHA':
                    $id_ = 5;
                    break;
                case 'AMERICAN':
                    $id_ = 6;
                    break;
                case 'MAESTRO':
                    $id_ = 8;
                    break;
                case 'DISCOVER':
                    $id_ = 7;
                    break;
                case 'JCB':
                    $id_ = 9;
                    break;
                case 'ALIPAY':
                    $id_ = 10;
                    break;
                case 'PAYPAL':
                    $id_ = 11;
                    break;
                case 'ELECTRON':
                    $id_ = 12;
                    break;
            }
        } else {
            //banco
            switch ($pago) {
                case 'PICHINCHA':
                    $id_ = 1;
                    break;
                case 'PACIFICO':
                    $id_ = 2;
                    break;
                case 'GUAYAQUIL':
                    $id_ = 3;
                    break;
                case 'INTERNACIONAL':
                    $id_ = 4;
                    break;
                case 'BOLIVARIANO':
                    $id_ = 5;
                    break;
                case 'PRODUBANCO':
                    $id_ = 6;
                    break;
                case 'AUSTRO':
                    $id_ = 7;
                    break;
                case 'SOLIDARIO':
                    $id_ = 8;
                    break;
                case 'GENERAL':
                    $id_ = 9;
                    break;
                case 'LOJA':
                    $id_ = 10;
                    break;
                case 'MACHALA':
                    $id_ = 11;
                    break;
                case 'PROCREDIT':
                    $id_ = 12;
                    break;
                case 'AMERICA':
                    $id_ = 13;
                    break;
                case 'CHASE':
                    $id_ = 14;
                    break;
            }
        }
        return $id_;
    }
    //details master invoice, method paid, etc.
    //important more square values with covid at Monday
    //UPDATE: success function at 7 December 2020
    //NO UPDATE: no work at day 28 December 2020
    public function vf($book, $idusuario, $ip_cliente)
    {
        $cliente = Ct_Clientes::where('identificacion', $book->cedula)->first();
        if (!is_null($cliente)) {
            $input_cabecera = [
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $book->factura,
                'id_empresa'      => '0993075000001',
                'observacion'     => 'Factura #' . $book->factura,
                'valor'           => $book->total,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $input_cabecera = [
                        'fecha_asiento'   => $book->fecha,
                        'fact_numero'     => $book->factura,
                        'id_empresa'      => '0993075000001',
                        'estado'          => '0',
                        'observacion'     => 'Factura #' . $book->factura,
                        'valor'           => $book->total,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                }
            }
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $punto               = $book->factura;
            $sucursal            = substr($punto, 0, 3);
            $punto_emision       = substr($punto, 4, 3);
            $nfactura            = substr($punto, 8);
            $factura_venta       = [
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'numero'              => $nfactura,
                'nro_comprobante'     => $book->factura,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => '0993075000001',
                'tipo'                => 'VEN-FA',
                'fecha'               => $book->fecha,
                'divisas'             => '1',
                'nombre_cliente'      => $book->paciente,
                'tipo_consulta'       => '1',
                'id_cliente'          => $book->cedula, //nombre_cliente
                'direccion_cliente'   => 'DIRECCION',
                'ruc_id_cliente'      => $book->cedula,
                'telefono_cliente'    => '1111',
                'email_cliente'       => '',
                'orden_venta'         => '',
                'nro_autorizacion'    => '1127272146', //autorizacion de humanlabs
                'id_paciente'         => $book->cedula,
                'nombres_paciente'    => $book->paciente,
                'id_hc_procedimiento' => '',
                'seguro_paciente' => 'PRIVADO',
                'procedimientos' => '',
                'fecha_procedimiento' => $book->fecha,
                'concepto' => '',
                'copago' => '',
                'ci_vendedor' => '',
                'vendedor' => '',
                //'nota'                          => $request['nota'],
                'subtotal_0' => $book->subtotal,
                'subtotal_12' => '0',
                //'subtotal'                      => $request['subtotal1'],
                'descuento' => $book->descuento,
                'base_imponible' => $book->descuento,
                'impuesto' => '0',
                // 'transporte'                    => $request['transporte'],
                'total_final' => $book->total,
                'valor_contable' => $book->total,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            $id_venta = Ct_ventas::insertGetId($factura_venta);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta' => $plan_cuentas->id,
                'descripcion' => $plan_cuentas->nombre,
                'fecha' => $book->fecha,
                'debe' => $book->total,
                'haber' => '0',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%

            $id_plan_confg = LogConfig::busqueda('4.1.01.01');

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta' => $plan_cuentas->id,
                'descripcion' => $plan_cuentas->nombre,
                'fecha' => $book->fecha,
                'debe' => '0',
                'haber' => $book->total,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            $detalle = [
                'id_ct_ventas' => $id_venta,
                'id_ct_productos' => 'EXAMEN LABS',
                'nombre' => 'EXAMEN LABS',
                'cantidad' => '1',
                'precio' => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento' => $book->descuento,
                'extendido' => $total_final['total'],
                'detalle' => 'EXAMEN LABS',
                'copago' => '',
                'check_iva' => '0',
                'porcentaje' => $this->porIva->iva,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
            $detail = $this->vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta);
        } else {

            Ct_Clientes::create([
                'identificacion' => $book->cedula,
                'nombre' => $book->paciente,
                'tipo' => '5',
                'clase' => 'normal',
                'nombre_representante' => $book->paciente,
                'cedula_representante' => $book->cedula,
                'ciudad_representante' => 'GUAYAQUIL',
                'telefono1_representante' => '11111',
                'pais' => 'Ecuador',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            $input_cabecera = [
                'fecha_asiento' => $book->fecha,
                'fact_numero' => $book->factura,
                'id_empresa' => '0993075000001',
                'observacion' => 'Factura #' . $book->factura,
                'valor' => $book->total,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $input_cabecera = [
                        'fecha_asiento' => $book->fecha,
                        'fact_numero' => $book->factura,
                        'id_empresa' => '0993075000001',
                        'estado' => '0',
                        'observacion' => 'Factura #' . $book->factura,
                        'valor' => $book->total,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                }
            }
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $punto = $book->factura;
            $sucursal = substr($punto, 0, 3);
            $punto_emision = substr($punto, 4, 3);
            $nfactura = substr($punto, 8);
            $factura_venta = [
                'sucursal' => $sucursal,
                'punto_emision' => $punto_emision,
                'numero' => $nfactura,
                'nro_comprobante' => $book->factura,
                'id_asiento' => $id_asiento_cabecera,
                'id_empresa' => '0993075000001',
                'tipo' => 'VEN-FA',
                'fecha' => $book->fecha,
                'divisas' => '1',
                'nombre_cliente' => $book->paciente,
                'tipo_consulta' => '1',
                'id_cliente' => $book->cedula, //nombre_cliente
                'direccion_cliente' => 'DIRECCION',
                'ruc_id_cliente' => $book->cedula,
                'telefono_cliente' => '1111',
                'email_cliente' => '',
                'orden_venta' => '',
                'nro_autorizacion' => '1127272146', //autorizacion de humanlabs
                'id_paciente' => $book->cedula,
                'nombres_paciente' => $book->paciente,
                'id_hc_procedimiento' => '',
                'seguro_paciente' => 'PRIVADO',
                'procedimientos' => '',
                'fecha_procedimiento' => $book->fecha,
                'concepto' => '',
                'copago' => '',
                'ci_vendedor' => '',
                'vendedor' => '',
                //'nota'                          => $request['nota'],
                'subtotal_0' => $book->subtotal,
                'subtotal_12' => '0',
                //'subtotal'                      => $request['subtotal1'],
                'descuento' => $book->descuento,
                'base_imponible' => $book->descuento,
                'impuesto' => '0',
                // 'transporte'                    => $request['transporte'],
                'total_final' => $book->total,
                'valor_contable' => $book->total,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $factura_venta = [
                        'sucursal' => $sucursal,
                        'punto_emision' => $punto_emision,
                        'numero' => $nfactura,
                        'nro_comprobante' => $book->factura,
                        'id_asiento' => $id_asiento_cabecera,
                        'id_empresa' => '0993075000001',
                        'tipo' => 'VEN-FA',
                        'fecha' => $book->fecha,
                        'divisas' => '1',
                        'estado' => '0',
                        'nombre_cliente' => $book->paciente,
                        'tipo_consulta' => '1',
                        'id_cliente' => $book->cedula, //nombre_cliente
                        'direccion_cliente' => 'DIRECCION',
                        'ruc_id_cliente' => $book->cedula,
                        'telefono_cliente' => '1111',
                        'email_cliente' => '',
                        'orden_venta' => '',
                        'nro_autorizacion' => '1127272146', //autorizacion de humanlabs
                        'id_paciente' => $book->cedula,
                        'nombres_paciente' => $book->paciente,
                        'id_hc_procedimiento' => '',
                        'seguro_paciente' => 'PRIVADO',
                        'procedimientos' => '',
                        'fecha_procedimiento' => $book->fecha,
                        'concepto' => '',
                        'copago' => '',
                        'ci_vendedor' => '',
                        'vendedor' => '',
                        //'nota'                          => $request['nota'],
                        'subtotal_0' => $book->subtotal,
                        'subtotal_12' => '0',
                        //'subtotal'                      => $request['subtotal1'],
                        'descuento' => $book->descuento,
                        'base_imponible' => $book->descuento,
                        'impuesto' => '0',
                        // 'transporte'                    => $request['transporte'],
                        'total_final' => $book->total,
                        'valor_contable' => $book->total,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ];
                }
            }
            $id_venta = Ct_ventas::insertGetId($factura_venta);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

            //            $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta' => $plan_cuentas->id,
                'descripcion' => $plan_cuentas->nombre,
                'fecha' => $book->fecha,
                'debe' => $book->total,
                'haber' => '0',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            $id_plan_confg = LogConfig::busqueda('4.1.01.01');
            $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
            //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '4.1.01.01',
                'id_plan_cuenta' => $plan_cuentas->id,
                'descripcion' => $plan_cuentas->nombre,
                'fecha' => $book->fecha,
                'debe' => '0',
                'haber' => $book->total,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            //master detail
            $detail = $this->vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta);
        }
    }
    public function vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta)
    {
        //would change with other name
        //had left subtotal_0
        $numero_factura = "";
        if (!is_null($book->examen_laboratorio)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $total_final = $this->indentify_valor($book, 1);
            $detalle = [
                'id_ct_ventas' => $id_venta,
                'id_ct_productos' => 'EXAMEN LABS',
                'nombre' => 'EXAMEN LABS',
                'cantidad' => '1',
                'precio' => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento' => $book->descuento,
                'extendido' => $total_final['total'],
                'detalle' => 'EXAMEN LABS',
                'copago' => '',
                'check_iva' => '0',
                'porcentaje' => $this->porIva->iva,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }
        if (!is_null($book->prueba_covid)) {
            $total_final = $this->indentify_valor($book, 1);
            $detalle = [
                'id_ct_ventas' => $id_venta,
                'id_ct_productos' => 'COVID',
                'nombre' => 'COVID',
                'cantidad' => '1',
                'precio' => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento' => $book->descuento,
                'extendido' => $total_final['total'],
                'detalle' => 'COVID',
                'copago' => '',
                'check_iva' => '0',
                'porcentaje' => $this->porIva->iva,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }
        if (!is_null($book->fee)) {
            $total_final = $this->indentify_valor($book, 1);
            $detalle = [
                'id_ct_ventas' => $id_venta,
                'id_ct_productos' => 'COMISION',
                'nombre' => 'COMISION',
                'cantidad' => '1',
                'precio' => $total_final['comision'],
                'descuento_porcentaje' => '',
                'descuento' => $book->descuento,
                'extendido' => $total_final['total'],
                'detalle' => 'COMISION',
                'copago' => '',
                'check_iva' => '0',
                'porcentaje' => $this->porIva->iva,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            Ct_detalle_venta::create($detalle);
        }
        //retenciones
        /*
        if(!is_null($book->retencion)){
        $rete= $this->retencionv($book,$idusuario,$ip_cliente,$numero_factura,$id_venta);
        }*/
        //comprobante de ingreso and values with paid method
        if (!is_null($book->efectivo)) {
            $numero_factura = str_pad($nfactura, 9, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->efectivo,
                'fecha_asiento' => $book->fecha,
                'fact_numero' => $numero_factura,
                'valor' => $book->efectivo,
                'id_empresa' => '0993075000001',
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            //1.01.02.05.01
            //TRAER PAGOS
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta' => $desc_cuenta->id,
                'descripcion' => $desc_cuenta->nombre,
                'fecha' => $book->fecha,
                'haber' => $book->efectivo,
                'debe' => '0',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones' => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->efectivo,
                'estado' => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha' => $book->fecha,
                'secuencia' => $nfactura,
                'divisas' => '1',
                'id_empresa' => '0993075000001',
                'total_ingreso' => $book->efectivo,
                'id_cliente' => $book->cedula,
                'autollenar' => "Cancela FV : " . $nfactura,
                'id_vendedor' => '',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante' => $id_comprobante,
                'fecha' => $book->fecha,
                'numero' => '',
                'id_tipo_tarjeta' => '',
                'id_tipo' => '1',
                'total' => $book->efectivo,
                'cuenta' => '',
                'girador' => $book->paciente,
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ]);
            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta' => $desc_cuenta->id,
                'descripcion' => $desc_cuenta->nombre,
                'fecha' => $book->fecha,
                'debe' => $book->efectivo, //$nuevo_saldof,
                'haber' => '0',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante' => $id_comprobante,
                'fecha' => $book->fecha,
                'observaciones' => "Cancela FV : " . $nfactura,
                'id_factura' => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura' => $book->total, //$request['saldo_a'.$i],
                'total' => $book->efectivo, //$request['abono_a'.$i],
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ]);
        } elseif (!is_null($book->tcredito)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots = $this->indentify_valor($book, 2);
            if (!is_null($book->comision)) {
                $tedua = $book->tcredito + $book->comision;
                $numero_factura = str_pad($nfactura, 9, "0", STR_PAD_LEFT);
                $input_cabecera = [
                    'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'fecha_asiento' => $book->fecha,
                    'fact_numero' => $numero_factura,
                    'valor' => $tedua,
                    'id_empresa' => '0993075000001',
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                //1.01.02.05.01
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta' => $desc_cuenta->id,
                    'descripcion' => $desc_cuenta->nombre,
                    'fecha' => $book->fecha,
                    'haber' => $tedua,
                    'debe' => '0',
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);

                $input_comprobante = [
                    'observaciones' => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'estado' => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $book->fecha,
                    'secuencia' => $nfactura,
                    'divisas' => '1',
                    'id_empresa' => '0993075000001',
                    'total_ingreso' => $tedua,
                    'id_cliente' => $book->cedula,
                    'autollenar' => "Cancela FV : " . $nfactura,
                    'id_vendedor' => '',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);

                $identificar_pagos = $this->identificar_pagos($book);
                //type with decide the moment to save paid method
                if (!is_null($book->tipo)) {
                    if ($book->tipo == 'CREDITO') {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante' => $id_comprobante,
                            'fecha' => $book->fecha,
                            'numero' => $book->referencia2,
                            'id_tipo_tarjeta' => $identificar_pagos,
                            'id_tipo' => '4',
                            'total' => $tedua,
                            'cuenta' => $book->lote,
                            'girador' => $book->paciente,
                            'estado' => '1',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ]);
                    } else {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante' => $id_comprobante,
                            'fecha' => $book->fecha,
                            'numero' => $book->referencia2,
                            'id_tipo_tarjeta' => $identificar_pagos,
                            'id_tipo' => '6',
                            'total' => $tedua,
                            'cuenta' => $book->lote,
                            'girador' => $book->paciente,
                            'estado' => '1',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ]);
                    }
                }

                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante' => $id_comprobante,
                    'fecha' => $book->fecha,
                    'observaciones' => "Cancela FV : " . $nfactura,
                    'id_factura' => $id_venta, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura' => $book->total, //$request['saldo_a'.$i],
                    'total' => $tedua, //$request['abono_a'.$i],
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);

                $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta' => $desc_cuenta->id,
                    'descripcion' => $desc_cuenta->nombre,
                    'fecha' => $book->fecha,
                    'debe' => $tedua, //$nuevo_saldof,
                    'haber' => '0',
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            } else {
                $tedua = $book->tcredito;
                $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
                $input_cabecera = [
                    'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'fecha_asiento' => $book->fecha,
                    'fact_numero' => $numero_factura,
                    'valor' => $book->efectivo,
                    'id_empresa' => '0993075000001',
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                //1.01.02.05.01
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta' => $desc_cuenta->id,
                    'descripcion' => $desc_cuenta->nombre,
                    'fecha' => $book->fecha,
                    'haber' => $tedua,
                    'debe' => '0',
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);

                $input_comprobante = [
                    'observaciones' => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'estado' => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $book->fecha,
                    'secuencia' => $nfactura,
                    'divisas' => '1',
                    'id_empresa' => '0993075000001',
                    'total_ingreso' => $tedua,
                    'id_cliente' => $book->cedula,
                    'autollenar' => "Cancela FV : " . $nfactura,
                    'id_vendedor' => '',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
                $tedua = $book->tcredito + $book->comision;
                $identificar_pagos = $this->identificar_pagos($book);
                //identify paid method
                if ($book->tipo == 'CREDITO') {
                    Ct_Detalle_Pago_Ingreso::create([
                        'id_comprobante' => $id_comprobante,
                        'fecha' => $book->fecha,
                        'numero' => $book->referencia2,
                        'id_banco' => $identificar_pagos,
                        'id_tipo' => '4',
                        'total' => $tedua,
                        'cuenta' => $book->lote,
                        'girador' => $book->paciente,
                        'estado' => '1',
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ]);
                } else if ($book->tipo == 'DEBITO') {
                    Ct_Detalle_Pago_Ingreso::create([
                        'id_comprobante' => $id_comprobante,
                        'fecha' => $book->fecha,
                        'numero' => $book->referencia2,
                        'id_tipo_tarjeta' => $identificar_pagos,
                        'id_tipo' => '6',
                        'total' => $tedua,
                        'cuenta' => $book->lote,
                        'girador' => $book->paciente,
                        'estado' => '1',
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ]);
                }

                $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

                //aqui me quede
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.01.1.01',
                    'id_plan_cuenta' => $desc_cuenta->id,
                    'descripcion' => $desc_cuenta->nombre,
                    'fecha' => $book->fecha,
                    'debe' => $tedua, //$nuevo_saldof,
                    'haber' => '0',
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante' => $id_comprobante,
                    'fecha' => $book->fecha,
                    'observaciones' => "Cancela FV : " . $nfactura,
                    'id_factura' => $id_venta, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura' => $book->total, //$request['saldo_a'.$i],
                    'total' => $tedua, //$request['abono_a'.$i],
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
        } elseif (!is_null($book->transfdep)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots = $this->indentify_valor($book, 4);
            $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->transfdep,
                'fecha_asiento' => $book->fecha,
                'fact_numero' => $numero_factura,
                'valor' => $book->transfdep,
                'id_empresa' => '0993075000001',
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');

            //$desc_cuenta         = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta' => $desc_cuenta->id,
                'descripcion' => $desc_cuenta->nombre,
                'fecha' => $book->fecha,
                'haber' => $book->transfdep,
                'debe' => '0',
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            $input_comprobante = [
                'observaciones' => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->transfdep,
                'estado' => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha' => $book->fecha,
                'secuencia' => $nfactura,
                'divisas' => '1',
                'id_empresa' => '0993075000001',
                'total_ingreso' => $book->transfdep,
                'id_cliente' => $book->cedula,
                'autollenar' => "Cancela FV : " . $nfactura,
                'id_vendedor' => '',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante' => $id_comprobante,
                'fecha' => $book->fecha,
                'numero' => '',
                'id_tipo_tarjeta' => '',
                'id_tipo' => '5',
                'total' => $book->transfdep,
                'cuenta' => '',
                'girador' => $book->paciente,
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ]);

            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->transfdep, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'    => $id_comprobante,
                'fecha'             => $book->fecha,
                'observaciones'     => "Cancela FV : " . $nfactura,
                'id_factura'        => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura'     => $book->total, //$request['saldo_a'.$i],
                'total'             => $book->transfdep, //$request['abono_a'.$i],
                'estado'            => '1',
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
        } elseif (!is_null($book->cheque)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots           = $this->indentify_valor($book, 3);
            $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->cheque,
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $numero_factura,
                'valor'           => $book->cheque,
                'id_empresa'      => '0993075000001',
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'haber'               => $book->cheque,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->cheque,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $book->fecha,
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => '0993075000001',
                'total_ingreso'       => $book->transfdep,
                'id_cliente'          => $book->cedula,
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => '',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante    = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante'  => $id_comprobante,
                'fecha'           => $book->fecha,
                'numero'          => '',
                'id_tipo_tarjeta' => '',
                'id_tipo'         => '2',
                'total'           => $book->cheque,
                'cuenta'          => '',
                'girador'         => $book->paciente,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->cheque, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'    => $id_comprobante,
                'fecha'             => $book->fecha,
                'observaciones'     => "Cancela FV : " . $nfactura,
                'id_factura'        => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura'     => $book->total, //$request['saldo_a'.$i],
                'total'             => $book->cheque, //$request['abono_a'.$i],
                'estado'            => '1',
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
        }
    }
    public function retencionv($book, $idusuario, $ip_cliente, $numero_factura, $id_venta)
    {
        //function retencion
        $cabeceraa = [
            'observacion'     => 'Retencion: ' . $numero_factura . " ",
            'fecha_asiento'   => $book->fecha,
            'fact_numero'     => $numero_factura,
            'valor'           => $book->retencion,
            'estado'          => '2',
            'id_empresa'      => '0993075000001',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
        // $id_asiento_cabecera      = 1;
        $input = [
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_cliente'          => $book->cedula,
            'autorizacion'        => '',
            'id_factura'          => $id_venta,
            'nro_comprobante'     => $numero_factura,
            'valor_fuente'        => $book->retencion,
            'valor_iva'           => '0',
            'id_empresa'          => '0993075000001',
            'tipo'                => '1',
            'id_tipo'             => '15',
            'descripcion'         => 'retencion ventas',
            'estado'              => '1',
            'total'               => $book->total,
            'secuencia'           => $numero_factura,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];
        $id_retenciones = Ct_Cliente_Retencion::insertGetId($input);
        Ct_Detalle_Cliente_Retencion::create([
            'id_cliente_retencion' => $id_retenciones,
            'observacion'          => 'RETENCION FUENTE',
            'numerorefs'           => $numero_factura,
            'fechaauto'            => $book->fecha,
            'id_tipo'              => '15',
            'tipo'                 => 'RENTA',
            'id_porcentaje'        => '15',
            'codigo'               => '3440',
            'base_imponible'       => $book->total,
            'estado'               => '1',
            'totales'              => $book->retencion,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
        ]);
        //query ventas
        $ventas = Ct_ventas::find($id_venta);
        if (!is_null($ventas)) {
            $totalf                 = $ventas->valor_contable - $book->retencion;
            $ventas->estado_pago    = 2;
            $ventas->valor_contable = $totalf;
            $ventas->save();
        }

        $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
        //En guayaquil esta 1.01.02.05.01 cuenta x cobrar clientes comerciales
        $consulta_plan = Ct_Configuraciones::where('id', 5)->first();

        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $consulta_plan->id_plan,
            'descripcion'         => $consulta_plan->cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
            'fecha'               => $book->fecha,
            'debe'                => '0',
            'haber'               => $book->retencion,
            'estado'              => '1',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $idusuario,
            'ip_modificacion'     => $idusuario,
        ]);

        $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
        //$consulta_plan_debe = Plan_Cuentas::find('1.01.05.02.12');
        $consulta_plan_debe = Plan_Cuentas::find($id_plan_confg);

        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
            'descripcion'         => $consulta_plan_debe->nombre,
            'fecha'               => $book->fecha,
            'debe'                => $book->retencion,
            'haber'               => '0',
            'estado'              => '1',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $idusuario,
            'ip_modificacion'     => $idusuario,
        ]);
    }
    public function indentify_valor($book, $referencia)
    {
        $data                          = [];
        $data['total_efectivo']        = 0;
        $data['total_tarjeta_credito'] = 0;
        $data['total_cheque']          = 0;
        $data['total_transferencia']   = 0;
        $data['comision']              = 0;
        if ($referencia == 1) {
            $data['total_efectivo'] = floatval($book->efectivo);
        } else if ($referencia == 2) {
            $data['total_tarjeta_credito'] = floatval($book->tcredito);
        } else if ($referencia == 3) {
            $data['total_cheque'] = floatval($book->cheque);
        } else if ($referencia == 4) {
            $data['total_transferencia'] = floatval($book->transfdep);
        }
        $data['total'] = floatval($book->tcredito) + floatval($book->transfdep) + floatval($book->cheque);
        if ($book->comision != null) {
            $data['comision'] = floatval($book->comision);
        }
        return $data;
    }
    //use this for the finally way to process fact_omni and conglomerada when the products have the same code, build to the array.
    public function getArrayPreview(Request $request, $tipo = "")
    {
        $idusuario = Auth::user()->id;
        if (!is_null($request)) {
            //dd($request->all());
            $group_productos = [];
            $empresa         = Empresa::find($request->session()->get('id_empresa'));
            $arr             = [];
            $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal      = $cod_sucurs->codigo_sucursal;
            $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja          = $cod_caj->codigo_caja;
            $proced          = $request['procedimiento'];
            $numero          = $request['numero'];
            $num_comprobante = "";

            //if($idusuario != "0957258056"){
            if (!is_null($numero)) {
                $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $empresa->id)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura        = $this->obtener_numero_factura($empresa->id, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
            $cabecera                           = [];
            $cabecera['nro_comprobante']        = $num_comprobante;
            $cabecera['direccion']              = $empresa->direccion;
            $cabecera['nombre']                 = $empresa->nombrecomercial;
            $cabecera['razonsocial']            = $empresa->razonsocial;
            $cabecera['direccion']              = $empresa->direccion;
            $cabecera['logo']                   = $empresa->logo;
            $cabecera['cliente']                = $request->identificacion_cliente;
            $cabecera['nombre_cliente']         = $request->nombre_cliente;
            $cabecera['mail_cliente']           = $request->mail_cliente;
            $cabecera['fecha']                  = $request->fecha_asiento;
            $cabecera['autorizacion']           = $request->numero_autorizacion;
            $cabecera['emailinfo']              = $request->emailinfo;
            $cabecera['procedimientoinfo']      = $request->procedimientoinfo;
            $cabecera['pacienteinfo']           = $request->pacienteinfo;
            $cabecera['seguroinfo']             = $request->segurosinfo;
            $cabecera['direccioninfo']          = $request->direccioninfo;
            $cabecera['fechaprocedimientoinfo'] = $request->fechaprocedimientoinfo;
            $cabecera['id']                     = $empresa->id;
            $cabecera['total']                  = $request->total1;
            $cabecera['tipo_factura']           = $request->tipo_factura;
            $cabecera['subtotal_0']             = $request->subtotal_01;
            $cabecera['subtotal_12']            = $request->subtotal_121;
            $cabecera['impuesto']               = $request->tarifa_iva1;
            $cabecera['descuento']              = $request->descuento1;
            $cabecera['subtotal']               = number_format($request->subtotal_01 + $request->subtotal12, 2, '.', '');
            $err                                = [];
            $arr_total                          = [];
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }
                        $nombre_procedimiento = "";
                        if (isset($request->input("id_principal")[$i])) {
                            $nombre_procedimiento = $request->input("id_principal")[$i];
                        }
                        $fecha_procedimiento = "";
                        if (isset($request->input("fecha_procedimiento")[$i])) {
                            $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                        }
                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $iva = 0;
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = 0;
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $id_hc_proc = "";
                        if (isset($request->input("hc_procedimiento")[$i])) {
                            $id_hc_proc = $request->input("hc_procedimiento")[$i];
                        }
                        $precio_neto = floatval($cantidad) * floatval($precio);
                        $arr         = array(
                            'nombre'               => $nombre,
                            'cantidad'             => $cantidad,
                            'codigo'               => $codigo,
                            'nombre_procedimiento' => $nombre_procedimiento,
                            'fecha_procedimiento'  => $fecha_procedimiento,
                            'precio'               => $precio,
                            'id_paciente'          => $paciente,
                            'descpor'              => "0.12",
                            'copago'               => "",
                            'descuento'            => $descuento,
                            'precioneto'           => $precio_neto,
                            'detalle'              => $descrip_prod,
                            'iva'                  => $iva,
                            'id_hc_proc'           => $id_hc_proc,
                            'obs_pac'              => $request['ss'],
                        );
                        //$arrayFinally= $this->getProductPrices($arr);
                        array_push($arr_total, $arr);
                    }
                }
            }
            $id_seguro = $request->id_seguro;
            $ambulatorio = $request->amBu;
            $data = $this->getProductPrices($arr_total, $tipo, $id_seguro, $ambulatorio);
            $group_productos = $this->group_by("id_paciente", $data);
            if ($request->tipo_factura == 1) {
                $group_productos = $this->group_by("id_hc_proc", $arr_total);
            }
            if ($request->tipo_factura == 3) {
                $group_productos = $this->group_by("id_paciente", $arr_total);
            }
            if ($ambulatorio == 1) {
                $group_productos = $this->group_by("id_paciente", $arr_total);
            }
            return [$group_productos, $cabecera];
        } else {
            $ds = [];
            return $ds;
        }
    }
    //created by A. Chilan at 20 January 2021
    //use this array function to send invoice with process Faustos's API
    //only work with use
    public function getProductPrices($data, $tipo = "", $id_seguro = "", $ambulatorio = "")
    {
        //if u want sum values and group by values
        $groups = array();
        foreach ($data as $ks => $item) {
            //dd($item);
            $key = $item['codigo'];
            //only when key is distinc to null
            if (!is_null($key)) {
                if (!is_null($item['nombre'])) {
                    if ($tipo == "conglomerada") {
                        //esta son las combinaciones para facturacion humana  Procedimientos Ambulatorios de Gastroenterologia
                        if ($id_seguro == 4) {
                            if ($ambulatorio == 1) {
                                $key = $item['codigo'];
                            } else {
                                $getParameters = ParametersConglomerada::getHumana($key);
                                if ($getParameters != false) {
                                    $key = $getParameters;
                                }
                            }
                        } else {
                            if ($ambulatorio == 1) {

                                $key = $item['codigo'];
                            }
                        }
                    }
                    if (!array_key_exists($key, $groups)) {
                        $codigo = $item['codigo'];
                        $nombre = $item['nombre'];
                        if ($tipo == "conglomerada") {
                            //esta son las combinaciones para facturacion humana  Procedimientos Ambulatorios de Gastroenterologia
                            if ($id_seguro == 4) {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                } else {
                                    $getParameters = ParametersConglomerada::getHumana($codigo);
                                    //dd($getParameters);
                                    if ($getParameters != false) {
                                        $codigo = $getParameters;
                                    }
                                }
                            } else {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                }
                            }
                        }
                        $nombre       = Ct_productos::where('codigo', $codigo)->first();
                        $nombre       = $nombre->descripcion; //no hay valicacion aqui
                        $groups[$key] = array(
                            'codigo'               => $codigo,
                            'nombre'               => $nombre,
                            'fecha_procedimiento'  => $item['fecha_procedimiento'],
                            'nombre_procedimiento' => $item['nombre_procedimiento'],
                            'id_paciente'          => $item['id_paciente'],
                            'precio'               => $item['precio'],
                            'precioneto'           => $item['precioneto'],
                            'cantidad'             => $item['cantidad'],
                            'descuento'            => $item['descuento'],
                            'detalle'              => $item['detalle'],
                        );
                    } else {
                        if ($tipo == "conglomerada") {
                            /*need to group by procedures endoscopia+colono+cpre+eco, en humana es  41253 Procedimientos Ambulatorios de Gastroenterologia - ecografias ,en humana es  46877Ultrasonografia Diagnostica*/
                            //dd($groups[$key]['precio'],$item['precio']);
                            //no group procedures
                            $groups[$key]['precio'] = $groups[$key]['precio'] + $item['precio']; //no working date 26 January 2021
                        } else {
                            $groups[$key]['cantidad'] = $groups[$key]['cantidad'] + $item['cantidad'];
                        }
                        $groups[$key]['detalle']    = $groups[$key]['detalle'];
                        $groups[$key]['precioneto'] = $groups[$key]['precioneto'] + $item['precioneto'];
                        $groups[$key]['descuento']  = $groups[$key]['descuento'] + $item['descuento'];
                    }
                }
            }
        }
        //dd($groups);
        return $groups;
    }
    public function getLoader(Request $request)
    {
        $arPor = $request['getLoader'];
        if (!is_null($arPor)) {
            $arPor = json_decode($request['getLoader']);
            if (count($arPor) > 0) {
                $fecha = "";
                if ($arPor->fecha != "") {
                    $fecha = $arPor->fecha;
                }
                $comprobante = "";
                if ($arPor->comprobante != "") {
                    $comprobante = $arPor->comprobante;
                }
                $eq = "";
                if ($arPor->eqs != "") {
                    $eq = $arPor->eqs;
                }
                $cs = "";
                if ($arPor->cs != "") {
                    $cs = $arPor->cs;
                }
                $ep = "";
                if ($arPor->ep != "") {
                    $ep = $arPor->ep;
                }
                $px = "";
                if ($arPor->px != "") {
                    $px = $arPor->px;
                }
                $fff = "";
                if ($arPor->fff != null) {
                    $fff = $arPor->fff;
                }
            }
            return response()->json("ok");
        }
        return response()->json("error");
    }
    public function group_by($key, $data)
    {
        $result = array();
        //result data or group by
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
    public function modal_preview(Request $request)
    {
        $arrayGroup = $this->getArrayPreview($request, "");
        return view('contable/ventas/modal_preview', ['arrayGroup' => $arrayGroup]);
    }
    public function modal_preview_c(Request $request)
    {
        $tipo_servicio = $request->tipo_servicio;
        $arrayGroup    = $this->getArrayPreview($request, "conglomerada");
        return view('contable/ventas/modal_previewc', ['arrayGroup' => $arrayGroup]);
    }
    public function pdf_nuevo($id = "1", Request $request)
    {
        $vistaurl = "contable.ventas.pdf_nuevo";

        if (is_null($request->getCabecera)) {
            $id       = $request['id'];
            $detalles = DB::table('ct_detalle_venta_c as c')->join('ct_productos as productos', 'productos.codigo', 'c.id_ct_productos')->join('ct_ventas as v', 'v.id', 'c.id_ct_ventas')->join('paciente as p', 'c.id_paciente', 'p.id')->where('v.id', $id)->select(DB::raw('SUM(c.extendido) as sumatoria'), 'productos.nombre as nombre_producto', 'productos.codigo as codigo_producto', 'p.nombre1 as nombre1', 'p.apellido1 as apellido')->get();
            $valid    = 0;
            $ventas   = Ct_ventas::find($id);
            $view     = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();
            //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('PreImpresoVentas-' . '.pdf');
        } else {
            //dd("a");
            //dd($request->all());
            $detalles = json_decode($request['arrayPreview']);
            $ventas   = json_decode($request['getCabecera']);
            //dd($ventas->descuento);
            $valid = 1;
            $view  = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();

            //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('PreImpresoVentas-' . '.pdf');
        }
    }
    public function pdf_nuevo2($id = "1", Request $request)
    {
        $vistaurl = "contable.ventas.pdf_nuevo2";
        $detalles = json_decode($request['arrayPreview']);
        $ventas   = json_decode($request['getCabecera']);
        $valid    = 1;
        $view     = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('PreImpresoVentas-' . '.pdf');
    }
    public function getPrices(Request $request)
    {
        $codigo_producto = $request->codigo_producto;
        $id_cliente      = $request->id_cliente;
        if (!is_null($codigo_producto) && !is_null($id_cliente)) {
            $cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();
            $clase   = $cliente->clase;
            if ($clase != null) {
                $producto = PrecioProducto::where('codigo_producto', $codigo_producto)->where('nivel', $clase)->first();
                if (!is_null($producto)) {
                    return response()->json($producto->precio);
                }
                return response()->json("vacio");
            } else {
                return response()->json("no tiene clase");
            }
        }
        return response()->json("error");
    }
    public function getReportUses(Request $request)
    {
        //usar fecha desde y fecha hasta
        $fecha_desde     = date('Y-m-d', strtotime($request->fecha));
        $fecha_hasta     = date('Y-m-d', strtotime($request->fecha_hasta));
        $nombre_paciente = $request->nombres;
        //dd($request->all());
        //dd($fecha_desde);
        $group_productos = array();
        $id_empresa      = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $detalles = Ct_Detalle_Venta_Omni::whereBetween('fecha_procedimiento', [$fecha_desde, $fecha_hasta])->join('ct_factura_omni as ctx', 'ctx.id', 'ct_detalle_venta_omni.id_omni')->where('ctx.tipo_factura', 2)->where('ctx.id_empresa', $id_empresa);
        $detalles = $detalles->get()->toArray();
        //dd($detalles);
        $group_productos = $this->group_by("id_ct_productos", $detalles);
        $vistaurl        = "contable.ventas.pdf_usos_equipos";
        $view            = \View::make($vistaurl, compact('detalles', 'omni', 'group_productos', 'fecha_desde'))->render();
        $pdf             = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('usos-' . '.pdf');
    }
    public function getReportUsesExcel(Request $request)
    {
        $fecha_desde = $request->fecha;
        $id_empresa  = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $fecha_hasta = $request->fecha_hasta;
        $detalles    = Ct_Detalle_Venta_Omni::whereBetween('fecha_procedimiento', [$fecha_desde, $fecha_hasta])->join('ct_factura_omni as ctx', 'ctx.id', 'ct_detalle_venta_omni.id_omni')->where('ctx.tipo_factura', 2)->where('ctx.id_empresa', $id_empresa);

        $detalles = $detalles->get()->toArray();

        $group_productos = $this->group_by("id_ct_productos", $detalles);
        $empresa         = Empresa::where('prioridad', '1')->get();
        $omni            = array();
        Excel::create('Informe usos ' . $fecha_desde . ' a ' . $fecha_hasta, function ($excel) use ($detalles, $group_productos, $empresa, $omni, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe usos', function ($sheet) use ($detalles, $empresa, $group_productos, $omni, $fecha_desde, $fecha_hasta) {
                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('A5:I5');

                $sheet->cell('A5', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue($empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $i = 6;

                foreach ($group_productos as $key => $values) {
                    //dd($values);
                    $countValues = count($values);
                    $conter      = 0;
                    if ($conter <= $countValues) {
                        $mes = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"][date('m', strtotime($values[$conter]['fecha_procedimiento'])) - 1];
                        //only u can change this method similar downside code beacuse only show in a one case

                        $contador = 1;
                        foreach ($values as $value) {
                            $verified = Ct_Factura_Omni::find($value['id_omni']);
                            if ($verified->tipo_factura == 2) {
                                if ($contador == 1) {
                                    $sheet->cell('D' . $i, function ($cell) use ($mes) {
                                        // manipulate the cel
                                        // $this->setSangria($cont, $cell);
                                        $cell->setFontWeight('bold');
                                        $cell->setFontSize(16);
                                        $cell->setFontColor("#FF4F29");
                                        $cell->setValue($mes);
                                        $cell->setValignment('center');

                                        //   $cell->setBorder('', '', 'thin','');
                                    });
                                    $i++;
                                    $nombre = Ct_productos::where('codigo', $key)->first();
                                    $sheet->cell('D' . $i, function ($cell) use ($nombre) {
                                        // manipulate the cel
                                        // $this->setSangria($cont, $cell);
                                        $cell->setFontWeight('bold');
                                        $cell->setFontSize(16);
                                        $cell->setFontColor("#FF4F29");
                                        $cell->setValue($nombre->nombre);
                                        $cell->setValignment('center');

                                        //   $cell->setBorder('', '', 'thin','');
                                    });
                                    $i++;
                                }
                                $paciente = Paciente::find($value['id_paciente']);
                                $sheet->cell('A' . $i, function ($cell) use ($contador) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($contador);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('B' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue("");
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('C' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($value['fecha_procedimiento']);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($paciente) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('E' . $i, function ($cell) use ($paciente) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($paciente->seguro->nombre);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($value['nombre_principal']);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('G' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue("");
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $i++;
                                $contador++;
                            }
                        }
                    }
                    $conter++;
                }
            });
        })->export('xlsx');
    }
    public function getHonorarios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $fecha_as        = Date('Y-m-d H:i:s');
        $id_empresa      = $request->session()->get('id_empresa');
        $llevaOrden      = false;
        $validate        = $request['validate'];
        $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $proced          = $request['procedimiento'];
        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        $text            = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        $msj             = "no    o";

        $id_paciente = $request['identificacion_paciente'];
        if (is_null($request['identificacion_paciente'])) {
            $id_paciente = '9999999999';
        }
        //fix contranstrain
        $pacis = Paciente::find($id_paciente);
        if (is_null($pacis)) {
            $id_paciente = '9999999999';
        }
        DB::beginTransaction();
        $msj = "no    o";
        try {
            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $factura_venta       = [
                'sucursal'            => $cod_sucurs->codigo_sucursal,
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'],
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $id_paciente,
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['total1'],
                'ip_creacion'         => "OMNI",
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            $arr_id_hc      = [];
            $arr_activos    = [];
            $cont           = 0;
            $arr_obs        = [];
            if (!empty($request->input("veractivo"))) {
                for ($i = 0; $i < count($request->input("veractivo")); $i++) {
                    if ($request->input("veractivo")[$i] == 1) {
                        $ar = $request->input("hc_procedimiento")[$i];
                        array_push($arr_activos, $ar);
                        $aobs = [
                            'procedimiento' => $request->input("hc_procedimiento")[$i],
                            'obs'           => $request->input("nom_paciente")[$i] . " - " . $request->input("obs_paciente")[$i],
                        ];
                        array_push($arr_obs, $aobs);
                    }
                }
            }

            $observacion = "";
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }
                        $nombre_procedimiento = "";
                        if (isset($request->input("id_principal")[$i])) {
                            $nombre_procedimiento = $request->input("id_principal")[$i];
                        }
                        $fecha_procedimiento = "";
                        if (isset($request->input("fecha_procedimiento")[$i])) {
                            $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                        }
                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $iva = "";
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = "";
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $copago = "";
                        if (isset($request->input("copago")[$i])) {
                            $copago = $request->input("copago")[$i];
                        }
                        $id_hc_proc = "";
                        if (isset($request->input("id_hc_proc")[$i])) {
                            $id_hc_proc = $request->input("id_hc_proc")[$i];
                        }
                        $id_agenda = "";
                        if (isset($request->input("id_agenda")[$i])) {
                            $id_agenda = $request->input("id_agenda")[$i];
                        }
                        $hc_procedimiento = "";
                        if (isset($request->input("hc_procedimiento")[$i])) {
                            $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                        }
                        $precioneto = $cantidad * $precio;
                        $arr        = [
                            'nombre'     => $nombre,
                            'cantidad'   => $cantidad,
                            'codigo'     => $codigo,
                            'precio'     => $precio,
                            'descpor'    => "0.12",
                            'copago'     => $copago,
                            'descuento'  => $descuento,
                            'precioneto' => $precio_neto,
                            'detalle'    => $descrip_prod,
                            'iva'        => $iva,
                            'id_hc_proc' => $hc_procedimiento,
                            'obs_pac'    => $observacion,
                        ];

                        array_push($arr_total, $arr);
                    }
                }
            }

            // $ids_consultas = json_encode($request->input('id_hc_consulta'));

            foreach ($arr_total as $valor) {
                if ($valor['copago'] > 0) {
                    //registra orden de venta
                    $llevaOrden = true;
                }
                //dd($valor);

            }

            $val_tol = $request['total1'];

            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan_confg  = LogConfig::busqueda('4.1.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            if (!empty($request->input("id_tip_pago"))) {
                for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                    if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                        $arr_pagos = [
                            'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                            'fecha_pago'     => $request->input("fecha_pago")[$i],
                            'numero_pago'    => $request->input("numero_pago")[$i],
                            'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                            'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                            'giradoa'        => $request->input("giradoa")[$i],
                            'valor'          => $request->input("valor")[$i],
                            'valor_base'     => $request->input("valor_base")[$i],
                        ];
                        array_push($arr_p, $arr_pagos);
                    }
                }
            }

            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            $arr_agendas = [];
            if ($request->tipo_factura == 3) {
                if ($llevaOrden) {
                    //dd("a");
                    $orden_id = $this->ordenVentaConglomerada($id_venta, $request);
                    //dd($orden_id);
                    $id_ct_ventas = [

                        'id_ct_venta' => $id_venta,

                    ];

                    Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                } else {
                    $orden_id = 0;
                }
                $arr_agendas = [];
                if (!empty($request->input("hc_procedimiento"))) {
                    for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {
                        if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $precio_neto = "";
                            if (isset($request->input("precioneto")[$i])) {
                                $precio_neto = $request->input("precioneto")[$i];
                            }
                            $nombre = "";
                            if (isset($request->input("nombre")[$i])) {
                                $nombre = $request->input("nombre")[$i];
                            }
                            $cantidad = 0;
                            if (isset($request->input("cantidad")[$i])) {
                                $cantidad = $request->input("cantidad")[$i];
                            }
                            $codigo = "";
                            if (isset($request->input("codigo")[$i])) {
                                $codigo = $request->input("codigo")[$i];
                            }
                            $nombre_procedimiento = "";
                            if (isset($request->input("id_principal")[$i])) {
                                $nombre_procedimiento = $request->input("id_principal")[$i];
                            }
                            $fecha_procedimiento = "";
                            if (isset($request->input("fecha_procedimiento")[$i])) {
                                $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                            }
                            $precio = 0;
                            if (isset($request->input("precio")[$i])) {
                                $precio = $request->input("precio")[$i];
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $iva = "";
                            if (isset($request->input("iva")[$i])) {
                                $iva = $request->input("iva")[$i];
                            }
                            $descuento = "";
                            if (isset($request->input("desc")[$i])) {
                                $descuento = $request->input("desc")[$i];
                            }
                            $descrip_prod = "";
                            if (isset($request->input("descrip_prod")[$i])) {
                                $descrip_prod = $request->input("descrip_prod")[$i];
                            }
                            $id_hc_proc = "";
                            if (isset($request->input("id_hc_proc")[$i])) {
                                $id_hc_proc = $request->input("id_hc_proc")[$i];
                            }
                            $id_agenda = "";
                            if (isset($request->input("id_agenda")[$i])) {
                                $id_agenda = $request->input("id_agenda")[$i];
                            }
                            $hc_procedimiento = "";
                            if (isset($request->input("hc_procedimiento")[$i])) {
                                $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                            }
                            $copago = 0;
                            if (isset($request->input("copago")[$i])) {
                                $copago = $request->input("copago")[$i];
                            }
                            $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre, 'copago' => $copago];
                            array_push($arr_agendas, $arr_ags);
                        }
                    }
                }
                foreach ($arr_agendas as $agenda) {
                    $id_omni = Ct_Factura_Omni::insertGetId([
                        'id_ct_ventas'    => $id_venta,
                        'id_agenda'       => $agenda["id_agenda"],
                        'id_paciente'     => $agenda["id_paciente"],
                        'tipo_factura'    => $request['tipo_factura'],
                        'fecha'           => $agenda['fecha_procedimiento'],
                        'estado'          => "1",
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                    $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                    Ct_Detalle_Venta_Omni::create([
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $agenda['producto'],
                        'nombre'               => $agenda['producto'],
                        'id_omni'              => $id_omni,
                        'cantidad'             => $agenda['cantidad'],
                        'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                        'id_agenda'            => $agenda['id_agenda'],
                        'nombre_principal'     => $agenda['nombre_principal'],
                        'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                        'precio'               => $agenda['precio'],
                        'descuento_porcentaje' => $request['iva_real'],
                        'extendido'            => $precio_neto,
                        'id_paciente'          => $agenda['id_paciente'],
                        'detalle'              => $agenda['descripcion'],
                        'check_iva'            => $agenda['check_iva'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ]);
                    $detalle = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $agenda['producto'],
                        'nombre'               => $agenda['producto'],
                        'cantidad'             => $agenda['cantidad'],
                        'precio'               => $agenda['precio'],
                        'descuento_porcentaje' => "0.12",
                        'descuento'            => "0",
                        'estado'               => '1',
                        'extendido'            => $precio_neto,
                        'detalle'              => $agenda['descripcion'],
                        'copago'               => $agenda['copago'],
                        'check_iva'            => $agenda['check_iva'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,

                    ];
                    Ct_detalle_venta::create($detalle);
                }
                $msj = "";
            } else {
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'llevaOrden' => $llevaOrden, 'arr_agendas' => $arr_agendas, 'kardex' => $msj];

        return $request;
    }
    public function ordenVentaConglomerada($id_v, Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.arr_agendas
        $arr_agendas = [];
        if (!empty($request->input("hc_procedimiento"))) {
            for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {
                if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                    $paciente = "";
                    if (isset($request->input("paciente")[$i])) {
                        $paciente = $request->input("paciente")[$i];
                    }
                    $precio_neto = "";
                    if (isset($request->input("precioneto")[$i])) {
                        $precio_neto = $request->input("precioneto")[$i];
                    }
                    $copago = 0;
                    if (isset($request->input("copago")[$i])) {
                        $copago = $request->input("copago")[$i];
                    }

                    $nombre = "";
                    if (isset($request->input("nombre")[$i])) {
                        $nombre = $request->input("nombre")[$i];
                    }
                    $cantidad = 0;
                    if (isset($request->input("cantidad")[$i])) {
                        $cantidad = $request->input("cantidad")[$i];
                    }
                    $codigo = "";
                    if (isset($request->input("codigo")[$i])) {
                        $codigo = $request->input("codigo")[$i];
                    }
                    $nombre_procedimiento = "";
                    if (isset($request->input("id_principal")[$i])) {
                        $nombre_procedimiento = $request->input("id_principal")[$i];
                    }
                    $fecha_procedimiento = "";
                    if (isset($request->input("fecha_procedimiento")[$i])) {
                        $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                    }
                    $precio = 0;
                    if (isset($request->input("precio")[$i])) {
                        $precio = $request->input("precio")[$i];
                    }
                    $paciente = "";
                    if (isset($request->input("paciente")[$i])) {
                        $paciente = $request->input("paciente")[$i];
                    }
                    $iva = "";
                    if (isset($request->input("iva")[$i])) {
                        $iva = $request->input("iva")[$i];
                    }
                    $descuento = "";
                    if (isset($request->input("desc")[$i])) {
                        $descuento = $request->input("desc")[$i];
                    }
                    $descrip_prod = "";
                    if (isset($request->input("descrip_prod")[$i])) {
                        $descrip_prod = $request->input("descrip_prod")[$i];
                    }
                    $id_hc_proc = "";
                    if (isset($request->input("id_hc_proc")[$i])) {
                        $id_hc_proc = $request->input("id_hc_proc")[$i];
                    }
                    $id_agenda = "";
                    if (isset($request->input("id_agenda")[$i])) {
                        $id_agenda = $request->input("id_agenda")[$i];
                    }
                    $hc_procedimiento = "";
                    if (isset($request->input("hc_procedimiento")[$i])) {
                        $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                    }
                    $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'copago' => $copago, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre];
                    array_push($arr_agendas, $arr_ags);
                }
            }
        }
        $agrupproducto = $this->group_by("id_paciente", $arr_agendas);
        foreach ($agrupproducto as $key => $value) {

            if (!is_null($value)) {
                $contador            = 0;
                $paciente            = Paciente::find($key);
                $fecha_procedimiento = $request['fecha_asiento'];
                if (isset($value[$contador])) {
                    $fecha_procedimiento = $value[$contador]['fecha_procedimiento'];
                }
                $factura_venta = [
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => $id_empresa,
                    'tipo'                => $request['tipo'],
                    'fecha'               => $request['fecha_asiento'],
                    'divisas'             => $request['divisas'],
                    'nombre_cliente'      => $request['nombre_cliente'],
                    'tipo_consulta'       => $request['tipo_consulta'],
                    'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente'   => $request['direccion_cliente'],
                    'ruc_id_cliente'      => $request['identificacion_cliente'],
                    'telefono_cliente'    => $request['telefono_cliente'],
                    'email_cliente'       => $request['mail_cliente'],
                    'orden_venta'         => $request['orden_venta'],
                    'estado_pago'         => '0',
                    'honorarios'          => '1',
                    'id_paciente'         => $key,
                    'nombres_paciente'    => $paciente->nombre1 . ' ' . $paciente->apellido1,
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente'     => $paciente->id_seguro,
                    'procedimientos'      => $request['procedimiento'],
                    'fecha_procedimiento' => $fecha_procedimiento,
                    'copago'              => "0",
                    'id_recaudador'       => $request['cedula_recaudador'],
                    'ci_vendedor'         => $request['cedula_vendedor'],
                    'vendedor'            => $request['vendedor'],
                    //'nota'                          => $request['nota'],
                    'subtotal_0'          => "0",
                    'subtotal_12'         => "0",
                    //'subtotal'                      => $request['subtotal1'],
                    'descuento'           => "0",
                    'base_imponible'      => "0",
                    'impuesto'            => "0",
                    // 'transporte'                    => $request['transporte'],
                    'total_final'         => "0",
                    'valor_contable'      => "0",
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_ct_venta'         => $id_v,
                ];

                // return $factura_venta;
                $id_venta = Ct_ven_orden::insertGetId($factura_venta);
                foreach ($value as $x) {
                    $detalle = [
                        'id_ct_ven_orden' => $id_venta,
                        'id_ct_productos' => $x['producto'],
                        'nombre'          => $x['descripcion'],
                        'cantidad'        => $x['cantidad'],
                        'precio'          => $x['copago'],
                        'extendido'       => $x['precio'],
                        'detalle'         => $x['descripcion'],
                        'check_iva'       => $x['check_iva'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Ct_ven_orden_detalle::create($detalle);
                }
            }
        }
        return ['id' => $id_venta];
    }
    public function infome_labs(Request $request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        $variable2   = 0;
        $totales     = 0;
        $subtotal12  = 0;
        $subtotal0   = 0;
        $subtotal    = 0;
        $descuento   = 0;
        $impuesto    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas($request);
        }

        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);
            //dd($deudas);
            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);
            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->total_final;
                        $subtotal12 += $value->subtotal_12;
                        $subtotal0 += $value->subtotal_0;
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                    }
                }
            }
        }
        return view('contable/ventas/informe_labs', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo]);
    }
    public function pdf_omni($id, Request $request)
    {
        //dd("a");
        //dd($request->all());
        //$detalles = json_decode($request['arrayPreview']);
        $ventas   = Ct_ventas::find($id);
        $detalles = Ct_Detalle_Venta_Omni::where('id_ct_ventas', $id)->get();
        //dd($ventas->descuento);
        $vistaurl = "contable.ventas.pdf_omni";
        $valid    = 1;
        $view     = \View::make($vistaurl, compact('detalles', 'ventas'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Factura de Venta -' . $id . '.pdf');
    }
    public function pdf_conglomerada($id, Request $request) {}
    public function informe_nca(Request $request)
    {       //5000 id_venta revisar OJOOOOOOO
        $sin = $request['sin'];
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::where('id', $id_empresa)->first();
        $fecha_desde   = $request['fecha_desde'];
        $gastos        = $request['esfac_contable'];
        $variable      = 0;
        $variable2     = 0;
        $totales       = 0;
        $subtotal12    = 0;
        $subtotal0     = 0;
        $subtotal      = 0;
        $descuento     = 0;
        $impuesto      = 0;
        $n_credito     = 0;
        $final_credito = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas_nca($request);
        }
        $proveedores = [];
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia, $request['estado'], '', '', '', '', '', $sin);
            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);
            $proveedores = Ct_Clientes::where('estado', '<>', '0')
                ->where('identificacion', $proveedor)
                ->get();
            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->subtotal_0 + $value->subtotal_12;
                        $subtotal12 += $value->subtotal_12;
                        $subtotal0 += $value->subtotal_0;
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                    }
                }
            }
        }
        return view('contable/ventas/informe_ventas_nca', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo, 'request' => $request]);
    }
    public function excel_ventas_nca(Request $request)
    {
        //dd("Ingreso");
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $proveedor   = $request['id_cliente'];
        $fecha_hasta = $request['fecha_hasta'];
        $concepto    = $request['secuencia'];
        $gastos      = $request['tipo'];
        $variable    = 0;
        $fech        = 0;
        $empresa     = Empresa::where('id', $id_empresa)->first();
        // $nota_credito= DB::table('ct_detalle_credito_clientes')->where('id_factura',$id)->first();
        $consulta = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, 0, $concepto);
        //dd($consulta);
        Excel::create('Informe Factura de Ventas Netas ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Factura de Ventas Netas', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null && $empresa->logo != '(N/A)') {
                    if (file_exists(base_path() . '/storage/app/logo/' . $empresa->logo)) {
                        $fech = 1;
                        $sheet->mergeCells('A1:H1');
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setHeight(70);
                        $objDrawing->setWorksheet($sheet);
                    }
                }
                $sheet->mergeCells('C2:U2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:U3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME FACTURA DE VENTAS NETAS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:T4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H5:I5');
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENDEDOR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENTAS BRUTAS');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL NCA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENTAS NETAS');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // DETALLES
                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',

                ));
                $i = $this->setDetalleInforme_nca($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL
                $sheet->cells('C3:T3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:T2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:U5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalleInforme_nca($consulta, $sheet, $i, $variable)
    {
        $x                  = 0;
        $valor              = 0;
        $resta              = 0;
        $totales            = 0;
        $subtotal12         = 0;
        $subtotal0          = 0;
        $subtotal           = 0;
        $descuento          = 0;
        $impuesto           = 0;
        $finalsub           = 0;
        $valor_nota_credito = 0;
        $final_con_credito  = 0;
        $n_credito          = 0;
        $ventaBruta         = 0;
        $final_credito      = 0;

        foreach ($consulta as $value) {
            $nota_credito = Ct_Nota_Credito_Clientes::where('id_factura', $value->id)
                ->where('estado', '!=', '0')
                ->first();
            $valor_nota_credito = 0;

            if ($value != null) {
                //$nota_credito = DB::table('ct_detalle_credito_clientes')->where('id_factura', $value->id)->first();
                if ($value->estado != 0) {
                    if ($value->electronica == 1) {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12 - $value->descuento;
                    } else {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                    }
                    $subtotal12 += $value->subtotal_12;

                    $subtotal0 += $value->subtotal_0;

                    $descuento += $value->descuento;
                    $impuesto += $value->impuesto;
                    $finalsub = $value->subtotal_12 + $value->subtotal_0;
                    $subtotalf = $value->subtotal_12 + $value->subtotal_0 - $value->descuento;
                    $ventaBruta = $subtotalf + $value->impuesto;
                    //$ventaBruta = $value->total_final;

                    if (!is_null($nota_credito)) {
                        $valor_nota_credito = $nota_credito->total_credito;
                        //$final_con_credito = (($value->subtotal_0 + $value->subtotal_12) - $value->descuento) - $valor_nota_credito;
                        $final_con_credito = $ventaBruta - $valor_nota_credito; //$value->total_final
                        //$n_credito += $valor_nota_credito;
                        $n_credito += $valor_nota_credito;
                        $final_credito += $final_con_credito;
                    } else {
                        $final_con_credito = $ventaBruta;
                        $final_credito += $final_con_credito;
                    }

                    // if (!is_null($nota_credito)) {
                    //     $valor_nota_credito = $nota_credito->total_credito; // el valor de las notas de credito
                    //     $final_con_credito  = $value->total_final - $nota_credito->total_credito; // valor final si existe nota de credito
                    //     $n_credito += $nota_credito->total_credito; // suma de las notas de credito
                    //     $final_credito += $final_con_credito; //suma de totales con nota de credito
                    // } else {
                    //     $final_con_credito = $value->total_final; // si no tiene el valor la nca va a ser el final de la factura
                    //     $final_credito += $value->total_final; // suma final
                    // }
                    $totales += $value->subtotal_0 + $value->subtotal_12 + $value->impuesto - $value->descuento;;
                } else {
                    $final_con_credito = 0;
                }
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                    $cell->setFontWeight('bold');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nro_comprobante);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue(' ' . $value->id_cliente);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->cliente != null) {
                        $cell->setValue($value->cliente->nombre);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->paciente != null && $value->id_paciente != "9999999999") {
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->tipo);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->nombre);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H' . $i . ':I' . $i);
                $sheet->cell('H' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->concepto . "# Asiento " . $value->id_asiento);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $vendedor = User::find($value->ci_vendedor);
                    if (!is_null($vendedor)) {
                        $cell->setValue($vendedor->nombre1 . ' ' . $vendedor->apellido1);
                    } else {
                        $cell->setValue($value->vendedor);
                    }
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->subtotal_12);

                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->subtotal_0);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->descuento);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($finalsub, $value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($finalsub - $value->descuento);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->impuesto);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($value, $ventaBruta) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($ventaBruta);
                    // if ($value->electronica == 1) {
                    //     $get = $value->total_final + $value->descuento;
                    //     $cell->setValue($get);
                    // } else {
                    //     $cell->setValue($value->total_final);
                    // }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value, $valor_nota_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    /*if ($value->electronica == 1) {
                        $get = $value->total_credito;
                        if (!is_null($nota_credito)) {
                            $get = $nota_credito->total_credito;
                        }

                        $cell->setValue($get);
                    } else {
                        if (!is_null($nota_credito)) {
                            $cell->setValue($nota_credito->total_credito); // correcion

                        } else {
                            $cell->setValue('0.00'); // correcion

                        }
                    }*/
                    // if (!is_null($nota_credito)) {
                    //     $cell->setValue($nota_credito->total_credito);
                    // } else {
                    //     $cell->setValue('0.00');
                    // }
                    $cell->setValue(number_format($valor_nota_credito, 2, '.', ''));
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R' . $i, function ($cell) use ($value, $final_con_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($final_con_credito);
                    // if ($value->electronica == 1) {
                    //     $get = $ventaBruta + $valor_nota_credito; // se suma si es electronica
                    //     $cell->setValue($get);
                    // } else {
                    //     $cell->setValue($final_con_credito); //ya hicimos la resta arriba
                    // }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S' . $i, function ($cell) use ($value, $nota_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue('ANULADA');
                    } elseif (!is_null($nota_credito)) {

                        $cell->setValue('NCA');
                    } else {

                        $cell->setValue('ACTIVO');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            } else {
                $subtotal12 += $value->subtotal_12;

                $subtotal0 += $value->subtotal_0;

                $descuento += $value->descuento;
                $impuesto += $value->impuesto;
                $finalsub = $value->subtotal_12 + $value->subtotal_0;
                $subtotalf = $value->subtotal_12 + $value->subtotal_0 - $value->descuento;
                $ventaBruta = $subtotalf + $value->impuesto;
            }
        }
        $sheet->cell('G' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($descuento) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('O' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('P' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('Q' . $i, function ($cell) use ($n_credito) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($n_credito);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('R' . $i, function ($cell) use ($final_credito) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($final_credito);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function estadistico(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio = $request['anio'];
        //dd($request->all());
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        //0  es publico
        //1  es privado
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = '0' . $i;
            if (strlen($i) < 1) {
                $mes = $i;
            }
            $ventas_mes        = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereMonth('ct_ventas.fecha', $mes)
                ->whereYear('ct_ventas.fecha', $anio)
                ->where('ct_ventas.estado', '<>', '0')
                ->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            $estructure['mes'] = $mes;
            foreach ($ventas_mes as $value) {
                $estructure['privado']   = $value->privado;
                $estructure['publico']   = $value->publico;
                $estructure['cpublico']  = $value->cpublico;
                $estructure['cprivado']  = $value->cprivado;
                $estructure['privadosc'] = $value->privadosc;
                $estructure['publicosc'] = $value->publicosc;
                array_push($array_agrupado, $estructure);
            }
        }
        $array_agrupado_anio = array();
        $anios               = [$request->anio];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->whereYear('ct_ventas.fecha', $z)->where('ct_ventas.estado', '<>', '0')->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        $empresa = Empresa::all();

        return view('contable/ventas/estadisticos', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'empresas' => $empresa, 'id_empresa' => $id_empresa, 'request' => $request]);
        //dd($array_agrupado_anio,$array_agrupado);p
    }
    public function estadisticoshc4(Request $request)
    {
        $empresa    = Empresa::all();
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        return view('contable/ventas/estadisticohc4', ['empresas' => $empresa, 'id_empresa' => $id_empresa]);
    }
    public function graphics(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio     = $request['anio'];
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = '0' . $i;
            if (strlen($i) < 1) {
                $mes = $i;
            }
            $ventas_mes        = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->whereMonth('ct_ventas.fecha', $mes)->whereYear('ct_ventas.fecha', date('Y'))->where('ct_ventas.estado', '<>', '0')->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            $estructure['mes'] = $mes;
            foreach ($ventas_mes as $value) {
                $estructure['privado']   = $value->privado;
                $estructure['publico']   = $value->publico;
                $estructure['cpublico']  = $value->cpublico;
                $estructure['cprivado']  = $value->cprivado;
                $estructure['privadosc'] = $value->privadosc;
                $estructure['publicosc'] = $value->publicosc;
                array_push($array_agrupado, $estructure);
            }
        }
        $array_agrupado_anio = array();
        $anios               = ['2021'];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->where('ct_ventas.estado', '<>', '0')->whereYear('ct_ventas.fecha', $z)->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        return view('contable/ventas/graphics', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin]);
    }
    public function informe_ordenes_pendientes(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['id_seguro'];

        $seguros     = Seguro::all();
        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $ordenes = '[]';

        $ordenes = Ct_Ven_Orden::where('estado', '!=', '0')->where('id_empresa', $id_empresa)
            ->where('estado_pago', '=', '0')
            ->where('tipo', '=', 'VEN-OV')
            ->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->where('valor_contable', '!=', 0);

        if (!is_null($seguro)) {
            $ordenes  = $ordenes->where('seguro_paciente', $seguro);
        }
        $ordenes  = $ordenes->orderBy('id', 'desc')->get();


        return view('contable/ventas/informe_ordenes_pendientes', ['informe' => $ordenes, 'id_empresa' => $id_empresa, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'seguros' => $seguros]);
    }
    public function guardar_comision(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_plantilla = $request['id_plantilla'];
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $total_final = $request['subtotal1'];
        $porcentaje = $request['porcentaje1'];
        $total_comision = $request['total1'];
        $check_liq = $request['check_liq'];

        if (is_null($check_liq)) {
            return ['respuesta' => 'error', 'msj' => 'Seleccione un item', 'titulos' => 'Error'];
        }

        if (is_null($id_plantilla)) {
            return ['respuesta' => 'error', 'msj' => 'Seleccione la plantilla', 'titulos' => 'Error'];
        }

        DB::beginTransaction();
        try {
            $comision_cabecera = [
                'id_plantilla'    => $id_plantilla,
                'fecha_inicio'    => $fecha_desde,
                'fecha_fin'       => $fecha_hasta,
                'estado'          => '1',
                'id_empresa'      => $id_empresa,
                'total'           => $total_final,
                'porcentaje'      => $porcentaje,
                'total_comision'  => $total_comision,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $ct_comision_cabecera = Ct_Comision_Cabecera::insertGetId($comision_cabecera);

            foreach ($check_liq as $value) {
                $detalle_ventas = Ct_detalle_venta::where('estado', 1)->where('id', $value)->first();
                if (!is_null($detalle_ventas)) {
                    $ventas = Ct_ventas::where('estado', 1)->where('id_empresa', $id_empresa)->where('id', $detalle_ventas->id_ct_ventas)->first();
                }
                Ct_Comision_Detalle::create([
                    'id_comision_cabecera' => $ct_comision_cabecera,
                    'id_detalle_venta'     => $detalle_ventas->id,
                    'codigo_producto'      => $detalle_ventas->id_ct_productos,
                    'id_paciente'          => $ventas->id_paciente,
                    'nro_comprobante'      => $ventas->nro_comprobante,
                    'fecha_procedimiento'  => $ventas->fecha_procedimiento,
                    'seguro'               => $ventas->seguro_paciente,
                    'valor_total'          => $detalle_ventas->extendido,
                    'estado'               => '1',
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ]);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function buscar_precio($id, Request $request)
    {

        $valor_total = 0;

        $orden_venta = Ct_Detalle_Venta::find($id);

        $valor_total = $orden_venta->extendido;

        return ['valor_total' => $valor_total];
    }
    public function informe_liquidaciones_comisiones(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $productos_buscados   = $request['productos'];
        $id_seguro = $request['id_seguro'];
        $id_plantilla = $request['id_plantilla'];
        $seguros = Seguro::where('inactivo', '1')->Orderby('nombre', 'asc')->get();

        if (!is_null($id_plantilla)) {
            if ($id_plantilla == '1') {
                $productos_buscados = ['COLO-PENTAX', 'EDA-PENTAX'];
                $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->whereIn('codigo', ['COLO-PENTAX', 'EDA-PENTAX'])->get();
            } elseif ($id_plantilla == '2') {
                $productos_buscados = [];
                $productos_det_venta = Ct_Detalle_Venta::distinct()->where('estado', '1')->wherebetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get(['id_ct_productos']);
                //dd($productos_det_venta);
                if (!is_null($productos_det_venta)) {
                    $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->whereNotIn('codigo', ['COLO-PENTAX', 'EDA-PENTAX']);
                    $todos = $todos->where(function ($jq1) use ($productos_det_venta) {
                        foreach ($productos_det_venta as $prod_buscados) {
                            $jq1 = $jq1->orwhere('codigo', $prod_buscados->id_ct_productos);
                        }
                    });
                }

                $todos = $todos->get();
                if (!is_null($todos)) {
                    $i = 0;
                    foreach ($todos as $value) {
                        $productos_buscados[$i] = $value->codigo;
                        $i++;
                    }
                }
            }
        } elseif (!is_null($productos_buscados)) {
            $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1);
            $todos = $todos->where(function ($jq1) use ($productos_buscados) {
                foreach ($productos_buscados as $prod_buscados) {
                    $jq1 = $jq1->orwhere('codigo', $prod_buscados);
                }
            });

            $todos = $todos->get();
        } else {
            $productos_buscados = ['COLO-PENTAX', 'EDA-PENTAX'];
            $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1);

            $todos = $todos->where(function ($jq1) {
                $jq1->orwhere('codigo', 'COLO-PENTAX')
                    ->orwhere('codigo', 'EDA-PENTAX');
            });

            $todos = $todos->get();
        }

        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }

        $ventas = '[]';
        $ventas = Ct_Ventas::where('estado', '!=', '0')->where('id_empresa', $id_empresa)
            ->where('tipo', '=', 'VEN-FA')
            ->wherebetween('fecha_procedimiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        if (!is_null($id_seguro)) {
            $ventas = $ventas->where(function ($jq2) use ($id_seguro) {
                foreach ($id_seguro as $seguros_todos) {
                    $jq2 = $jq2->orwhere('seguro_paciente', $seguros_todos);
                }
            });
        }

        $ventas  = $ventas->orderBy('fecha_procedimiento', 'desc')->get();

        $comision_cabecera = Ct_Comision_Cabecera::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();

        return view('contable/ventas/informe_liquidaciones_comisiones', ['ventas' => $ventas, 'id_empresa' => $id_empresa, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'productos_todos' => $todos, 'productos_buscados' => $productos_buscados, 'id_plantilla' => $id_plantilla, 'comision_cabecera' => $comision_cabecera]);
    }
    public function reporte_cardiologia(Request $request)
    {
        //dd("hola");
        config(['data' => []]);
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $cardiologias = Hc_Evolucion::join('historiaclinica as h', 'h.hcid', 'hc_evolucion.hcid')
            ->leftjoin('hc_procedimientos as hcpro', 'hcpro.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as pa', 'pa.id', 'h.id_paciente')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('users as us', 'us.id', 'hcpro.id_doctor_examinador')
            ->where('a.espid', '8')
            ->where('a.estado', '1')
            ->where('a.fechaini', '>=',  $fecha_desde . ' 00:00:00')->where('a.fechafin', '<=', $fecha_hasta . ' 23:59:59')
            ->select('h.id_agenda', 'a.fechaini', 'h.id_paciente', 'pa.fecha_nacimiento', 'us.id as cedula_dr', 'us.nombre1', 'us.apellido1', 'us.apellido2', 's.nombre as seguro', 'a.estado_cita', 'hc_evolucion.motivo', 'hc_evolucion.cuadro_clinico')
            ->orderBy('a.fechaini', 'desc')->paginate(10);
        //dd($cardiologias);
        $data['controlador'] = $this->controlador;
        config(['data' => $data]);
        return view('contable/ventas/reporte_cardiologia', ['empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'cardiologias' => $cardiologias]);
    }
    public function excel_reporte_cardiologia(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['desdeFecha'];
        $fecha_hasta = $request['hastaFecha'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date("Y-m-d");
        }
        $cardiologias = Hc_Evolucion::join('historiaclinica as h', 'h.hcid', 'hc_evolucion.hcid')
            ->leftjoin('hc_procedimientos as hcpro', 'hcpro.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as pa', 'pa.id', 'h.id_paciente')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('users as us', 'us.id', 'hcpro.id_doctor_examinador')
            ->where('a.espid', '8')->where('a.estado', '1')
            ->where('a.fechaini', '>=',  $fecha_desde . ' 00:00:00')
            ->where('a.fechafin', '<=', $fecha_hasta . ' 23:59:59')
            ->select('h.id_agenda', 'a.fechaini', 'h.id_paciente', 'pa.nombre1 as nombre1paciente', 'pa.apellido1 as apellidopaciente', 'pa.apellido2 as apellidopaciente2', 'pa.fecha_nacimiento', 'us.id as cedula_dr', 'us.nombre1', 'us.apellido1', 'us.apellido2', 's.nombre as seguro', 'a.estado_cita', 'hc_evolucion.motivo', 'hc_evolucion.cuadro_clinico')
            ->orderBy('a.fechaini', 'desc')->get();
        //Titulos excel
        $titulos = array("No. Agenda", "Fecha", "Cedula", "Paciente", "Fecha de Nacimiento", "Doctor", "Estado cita", "Motivo", "Seguro", "Cuadro Clinico");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('REPORTE DE CARGIOLOGIA', function ($excel) use ($titulos, $posicion, $cardiologias) {
            $excel->sheet('REPORTE DE CARDIOLOGIA', function ($sheet) use ($titulos, $posicion, $cardiologias) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE CARDIOLOGIA');
                    $cell->setFontSize(18);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/
                foreach ($cardiologias as $soporte) {
                    $datos_excel = array();
                    $est_cita = ["0" => "POR CONFIRMAR", "1" => "CONFIRMADA", "2" => "REAGENDADO", "3" => "SUSPENDIDO", "4" => "ADMISIONADO"];
                    $texto = strip_tags($soporte->cuadro_clinico);
                    $new = str_replace(array('&ntilde;', '&Ntilde;', '&aacute;', '&eacute;', '&iacute;', "&oacute;", "&uacute;", '&Aacute;', '&Eacute;', '&Iacute;', "&Oacute;", "&Uacute;", "&nbsp;"), array("ñ", "Ñ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", " "), $texto);
                    array_push($datos_excel, $soporte->id_agenda, $soporte->fechaini, $soporte->id_paciente, $soporte->nombre1paciente . ' ' . $soporte->apellidopaciente . ' ' . $soporte->apellidopaciente2, $soporte->fecha_nacimiento, "{$soporte->nombre1} {$soporte->apellido1} {$soporte->apellido2}", $est_cita["{$soporte->estado_cita}"], $soporte->motivo, $soporte->seguro, $new);
                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(50)->setAutosize(false);
        })->export('xlsx');
    }
    public function pdf_liquidacion_comision($id, Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();

        $cabecera = Ct_Comision_Cabecera::where('id', $id)->where('estado', 1)->where('id_empresa', $id_empresa)->first();
        if (!is_null($cabecera)) {
            $detalle = Ct_Comision_Detalle::where('id_comision_cabecera', $id)->where('estado', 1)->get();
        }

        $vistaurl = "contable.ventas.pdf_liquidacion_comisiones";
        $view     = \View::make($vistaurl, compact('cabecera', 'detalle', 'empresa'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Liquidacion de comisiones' . '.pdf');
    }
    public function eliminar_liquidacion_comision($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cabecera_comision = Ct_Comision_Cabecera::find($id);

        $arreglo = [
            'estado'            => 0,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $cabecera_comision->update($arreglo);

        $detalle_comision = Ct_Comision_Detalle::where('id_comision_cabecera', $id)->where('estado', '1')->get();

        foreach ($detalle_comision as $value) {
            $value->estado = 0;
            $value->id_usuariomod = $id_usuario;
            $value->ip_modificacion = $ip_cliente;
            $value->save();
        }
    }
    public function informe_ventas(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $sucursales  = Ct_caja::where('id_empresa', $id_empresa)->get();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        $variable2   = 0;
        $totales     = 0;
        $subtotal12  = 0;
        $subtotal0   = 0;
        $subtotal    = 0;
        $descuento   = 0;
        $totaldetotal = 0;
        $impuesto    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas($request);
        }
        $activo      = $request->activo;
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $sucursal   = $request['sucursal'];
        $id_seguro   = $request['id_seguro'];
        $id_tipo   = $request['id_tipo'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();
        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        $tipos_seguros = SeguroTipos::where('estado', '>', '1')->get();
        // dd($seguro);

        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia, $activo, $sucursal, $seguros, $tipos_seguros, $id_seguro, $id_tipo);
            if (Auth::user()->id == '0953905999') {
                //dd($deudas);
            }
        }
        if (count($deudas) > 0) {
            if (is_null($activo) or $activo == 1) {
                $activo = 1;
            } else {
                $activo = 0;
            }
            $totales    = $deudas->where('estado', $activo)->sum('total_final');
            $subtotal12 = $deudas->where('estado', $activo)->sum('subtotal_12');
            //$subtotal = $deudas->where('estado', $activo)->sum('subtotal');
            $subtotal0 = $deudas->where('estado', $activo)->sum('subtotal_0');
            $descuento = $deudas->where('estado', $activo)->sum('descuento');
            $impuesto  = $deudas->where('estado', $activo)->sum('impuesto');

            if (Auth::user()->id == "0957258056") {
                //dd($descuento);
            }
            //*******************ELECTRONICA*********************//
            //$sumElectronica =  $deudas->where('estado', $activo)->sum('subtotal_0');
            $descuentoElectronica = $deudas->where('estado', $activo)->where('electronica', 1)->sum('descuento');
            //*******************NO ELECTRONICA*********************//


            // if($activo == 2){
            //     $totales = $deudas->sum('total_final');
            //     $subtotal12 = $deudas->sum('subtotal_12');
            //     $subtotal = $deudas->sum('subtotal');
            //     $subtotal0 = $deudas->sum('subtotal_0');
            //     $descuento = $deudas->sum('descuento');
            //     $impuesto = $deudas->sum('iva_total');
            // }else{
            //     $totales = $deudas->where('estado', '<>', '0')->sum('total_final');
            //     $subtotal12 = $deudas->where('estado', '<>', '0')->sum('subtotal_12');
            //     $subtotal = $deudas->where('estado', '<>', '0')->sum('subtotal');
            //     $subtotal0 = $deudas->where('estado', '<>', '0')->sum('subtotal_0');
            //     $descuento = $deudas->where('estado', '<>', '0')->sum('descuento');
            //     $impuesto = $deudas->where('estado', '<>', '0')->sum('iva_total');
            // }
            //  dd($sumElectronica);
            $subtotal = $subtotal0 + $subtotal12;
            $totales  = $subtotal  + $impuesto - $descuento;
            // dd($descuentoElectronica);
            //$totales = $totales + $descuentoElectronica;

            if (Auth::user()->id == "0957258056") {
                //dd($totales);
            }
        }


        return view('contable/ventas/informe', ['informe' => $deudas, 'secuencia' => $secuencia, 'sucursales' => $sucursales, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'activo' => $activo, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo, 'sucursal' => $sucursal, 'seguros' => $seguros, 'seguros' => $seguros, 'tipos_seguros' => $tipos_seguros, 'request' => $request, 'seguro_sel' => $id_seguro, 'tipo_seguro_sel' => $id_tipo]);
    }
    public function informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $referencia, $r, $secuencia, $activo = "", $sucursal = null, $seguros = null, $tipos_seguros = null, $id_seguro = '', $id_tipo = '', $sin = '')
    {
        try {
            $deudas    = null;
            $condicion = ['VEN-FA', 'VENFA-CO'];
            $deudas    = Ct_ventas::select(
                'ct_ventas.*',
                'seg.id as seguro_id',
                'seg.nombre',
                'seg.descripcion',
                'seg.color',
                'seg.inactivo',
                'seg.codigo_validacion',
                'seg.url_validacion',
                'seg.clase',
                'seg.promo_seguro',
                'seg.visualizar',
                'seg.email as email_seguro',
                'seg.ruc_cliente',
                'seg.id_seguro_tipos'
            )
                ->where(function ($q) use ($sin) {
                    if ($sin != '')
                        $q->where('ct_ventas.id_cliente', '!=', $sin);
                })

                ->whereIn('ct_ventas.tipo', $condicion)
                ->leftjoin('seguros as seg', 'seg.id', 'ct_ventas.seguro_paciente')
                ->where('ct_ventas.id_empresa', $id_empresa);
            // $seguros = Seguro::where('seguros.inactivo', '1')
            // ->where('promo_seguro', '<>', 1)
            // ->orderBy('nombre', 'asc')->first();
            // $tipos_seguros = SeguroTipos::where('estado', '>', '1')->first();
            if ($activo != null or $activo != "") {
                if ($activo == 2) {
                    $activo = 0;
                }
                $deudas = Ct_ventas::select(
                    'ct_ventas.*',
                    'seg.id as seguro_id',
                    'seg.nombre',
                    'seg.descripcion',
                    'seg.color',
                    'seg.inactivo',
                    'seg.codigo_validacion',
                    'seg.url_validacion',
                    'seg.clase',
                    'seg.promo_seguro',
                    'seg.visualizar',
                    'seg.email as email_seguro',
                    'seg.ruc_cliente',
                    'seg.id_seguro_tipos'
                )->whereIn('ct_ventas.tipo', $condicion)->where('ct_ventas.estado', $activo)
                    ->leftjoin('seguros as seg', 'seg.id', 'ct_ventas.seguro_paciente')
                    ->where('ct_ventas.id_empresa', $id_empresa);
                //dd('aqui');
            }
            if (!is_null($fecha_desde)) {
                $deudas = $deudas->whereBetween('ct_ventas.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $deudas = $deudas->where('ct_ventas.fecha', '<=', $fecha_hasta);
            }
            if (!is_null($secuencia)) {
                $deudas = $deudas->where('ct_ventas.nro_comprobante', 'like', '%' . $secuencia . '%');
            }
            if (!is_null($proveedor)) {
                $deudas = $deudas->where('ct_ventas.id_cliente', $proveedor);
            }
            if (!is_null($sucursal) && $sucursal != "") {
                $deudas = $deudas->where('ct_ventas.nro_comprobante', 'like', $sucursal . '%');
            }
            if ($id_tipo != '' && $id_seguro != '') {
                $deudas = $deudas->where('ct_ventas.seguro_paciente', $id_seguro);
                //$deudas = $deudas->where('seg.id_seguro_tipos', $id_tipo);
            } elseif ($id_tipo != '') {
                $deudas = $deudas->where('seg.id_seguro_tipos', $id_tipo);
            }
            if (($variable) == 1) {
                $deudas = $deudas->paginate(20);
            } else {
                $deudas = $deudas->orderBy('ct_ventas.fecha', 'ASC')->get();
            }
            return $deudas;
        } catch (Exception $e) {
            $arrayError = [
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ];
            echo '<pre>';
            print_r($arrayError);
            exit;
        }
    }
    public function excel_ventas(Request $request)
    {
        //dd("Ingreso");
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $proveedor   = $request['id_cliente'];
        $fecha_hasta = $request['fecha_hasta'];
        $concepto    = $request['secuencia'];
        $gastos      = $request['tipo'];
        $variable    = 0;
        $fech        = 0;
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $consulta    = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, 0, $concepto);
        //dd($consulta);
        Excel::create('Informe Factura de Ventas ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Factura de Ventas', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null && $empresa->logo != '(N/A)') {
                    $fech = 1;
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:Q2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:Q3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME FACTURA DE VENTAS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:Q4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AUTORIZACION');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });;
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',

                ));
                $i = $this->setDetalleInforme($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL
                $sheet->cells('C3:Q3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:Q2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:Q5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(24)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalleInforme($consulta, $sheet, $i, $variable)
    {
        $x          = 0;
        $valor      = 0;
        $resta      = 0;
        $totales    = 0;
        $subtotal12 = 0;
        $subtotal0  = 0;
        $subtotal   = 0;
        $descuento  = 0;
        $impuesto   = 0;
        $finalsub   = 0;
        foreach ($consulta as $value) {
            if ($value != null) {
                if ($value->estado != 0) {
                    if ($value->electronica == 1) {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12 + $value->descuento;
                    } else {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                    }
                    $subtotal12 += $value->subtotal_12;
                    $subtotal0 += $value->subtotal_0;
                    $descuento += $value->descuento;
                    $impuesto += $value->impuesto;
                    $finalsub = $value->subtotal_12 + $value->subtotal_0;
                    if ($value->electronica == 1) {
                        $finalsub = $value->subtotal_12 + $value->subtotal_0 + $value->descuento;
                    }
                    $totales += $value->total_final;
                }

                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                    $cell->setFontWeight('bold');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nro_comprobante);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->nro_autorizacion);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue(' ' . $value->id_cliente);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->cliente != null) {
                        $cell->setValue($value->cliente->nombre);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->paciente != null && $value->id_paciente != "9999999999") {
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->tipo);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->concepto . "# Asiento " . $value->id_asiento);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->subtotal_12);

                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->subtotal_0);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($finalsub, $value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($finalsub);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->descuento);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->impuesto);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_final;
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($get);
                    } else {
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($value->total_final);
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue('ANULADA');
                    } else {
                        $cell->setValue('ACTIVO');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue($value->usuariomod->nombre1 . " " . $value->usuariomod->apellido1);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            }
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($descuento) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function imprimirPlanillaDetalle($id, $id_hc_procedimiento) //id_procedimiento//
    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $empresa    = Empresa::find($agenda->id_empresa);
        $planilla   = Planilla::where('id_hc_procedimiento', $id_hc_procedimiento)
            ->where('estado', '!=', 0)
            ->where('aprobado', '!=', 0)
            ->orderBy('id', 'desc')
            ->first();
        // dd($planilla);
        if (!isset($planilla->id)) {
            $detalles = '[]';
        } else {
            $detalles = $planilla->detalles_validos;
        }

        $vistaurl = "contable.ventas.pdf_detalle_planilla";
        $view     = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detalles', 'orden', 'empresa', 'paciente'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    //LZ
    //Modal Planillas Agenda
    public function obtenerPlanillasAgenda($id)
    {
        $id_usuario = Auth::user()->id;
        $hcp        = hc_procedimientos::find($id);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $orden      = array();

        //HONORARIOS MEDICOS Y ANASTESIOLOGICOS 18/FEB/2022 VICTOR
        /*$seguro = $hc->seguro;
        if( $hcp->id_seguro != null ){
        $seguro = $hcp->seguro;
        }

        $id_empresa = $agenda->id_empresa;
        if($hcp->id_empresa != null){
        $id_empresa = $hcp->id_empresa;
        }

        $incluir_convenio = false; $convenio = null;

        if($seguro->tipo == 0){ //PUBLICO
        $convenio = Convenio::where('id_empresa',$id_empresa)->where('id_seguro',$seguro->id)->first();
        }

        if($seguro->tipo == 1){ //PRIVADO
        $convenios = Convenio::where('id_seguro',$seguro->id)->get();
        if($convenios->count() > 1){
        $incluir_convenio = true;
        }
        if($convenios->count() == 1){
        $incluir_convenio = false;
        $convenio = $convenios->first();
        }
        }*/

        //dd($seguro,$convenio);
        //
        return view('contable/ventas/modal_detalle_planillas', ['id' => $id, 'paciente' => $paciente, 'orden' => $orden, 'hc' => $hc, 'id_usuario' => $id_usuario, 'agenda' => $agenda]);
    }
    public function create_recibo(Request $request)
    {
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();
        $cuentas      = Plan_Cuentas::where('estado', '2')->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        //$iva = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        return view('contable/ventas/create_recibo', ['porIva' => $this->porIva, 'divisas' => $divisas, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'sucursales' => $sucursales, 'punto' => $punto, 'cuentas' => $cuentas, 'productos' => $productos, 't_factura' => '1', 'tipo_tarjeta' => $tipo_tarjeta]);
    }
    public function htmlrecibo(Request $request)
    {
        $id         = $request->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $recibo     = Ct_Orden_Venta::find($id);
        $productos  = Ct_productos::where('estado_tabla', '1')->get();
        return view('contable/ventas/detalles', ['id' => $id, 'recibo' => $recibo, 'productos' => $productos, 'empresa' => $empresa]);
    }
    public function index_recibo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ventas     = Ct_ventas::where('tipo', 'VEN-R')->where('id_empresa', $id_empresa);
        if ($request->id != null) {
            $ventas = $ventas->where('id', $request->id);
        } else {
            if ($request->id_asiento != null) {
                $ventas = $ventas->where('id_asiento', $request->id_asiento);
            }
            if ($request->fecha != null) {
                $ventas = $ventas->where('fecha', $request->fecha);
            }
            if ($request->nombre_cliente != null) {
                $ventas = $ventas->where('nombre_cliente', 'LIKE', '%' . $request->nombre_cliente . '%');
            }
            if ($request->nombre_paciente != null) {
                $ventas = $ventas->where('nombres_paciente', 'LIKE', '%' . $request->nombre_paciente . '%');
            }
        }
        $empresa = Empresa::find($id_empresa);
        $ventas  = $ventas->orderBy('fecha', 'DESC')->paginate(20);
        return view('contable.ventas.index_recibo', ['ventas' => $ventas, 'request' => $request, 'empresa' => $empresa]);
    }
    public function store_recibo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request->fecha_asiento;
        DB::beginTransaction();
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $msj        = "no    o";
        $numero     = $request['numero'];
        $cliente    = Ct_Clientes::where('identificacion', '=', $request['identificacion_cliente'])->count();
        try {
            if ($cliente == 0) {
                // cliente
                Ct_Clientes::create([
                    'nombre'                  => strtoupper($request['nombre_cliente']),
                    'tipo'                    => '5',
                    'identificacion'          => $request['identificacion_cliente'],
                    'clase'                   => '1',
                    'nombre_representante'    => $request['nombre_cliente'],
                    'cedula_representante'    => $request['identificacion_cliente'],
                    'ciudad_representante'    => $request['ciudad_cliente'],
                    'direccion_representante' => $request['direccion_cliente'],
                    'telefono1_representante' => $request['telefono_cliente'],
                    'email_representante'     => $request['mail_cliente'],
                    'estado'                  => '1',
                    'id_usuariocrea'          => $idusuario,
                    'id_usuariomod'           => $idusuario,
                    'ip_creacion'             => $ip_cliente,
                    'ip_modificacion'         => $ip_cliente,

                ]);
            }

            if ($request['sucursal'] != 0) {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();

                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                $c_caja     = $cod_caj->codigo_caja;
                $proced     = $request['procedimiento'];
                // echo($numero);

                if (!is_null($numero)) {
                    $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            } else {
                $c_sucursal      = 0;
                $c_caja          = 0;
                $num_comprobante = 0;
                $nfactura        = 0;
                $proced          = $request['procedimiento'];
            }
            $pac = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
            $id_paciente = $request['identificacion_paciente'];
            $patient     = Paciente::find($id_paciente);
            $pc          = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            $input_cabecera = [
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $factura_venta = [
                'sucursal'            => $cod_sucurs->codigo_sucursal,
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $request['identificacion_paciente'],
                'nombres_paciente'    => $request['pacienteinfo'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['segurosinfo'],
                'procedimientos'      => $request['procedimientoinfo'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'valor_contable'      => $request['total1'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['total1'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            $arr_id_hc      = [];
            $arr_activos    = [];
            $cont           = 0;
            $arr_obs        = [];

            $observacion = "";
            //dd(count($request->input("recibo")));
            for ($p = 0; $p < count($request->input("recibo")); $p++) {
                Ct_Ventas_Recibo_Cobro::create([
                    'id_venta'        => $id_venta,
                    'id_recibo'       => $request->input("recibo")[$p],
                    'id_empresa'      => $id_empresa,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {

                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }

                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }

                        $iva = "";
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = "";
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $pdesc = "";
                        if (isset($request->input("descpor")[$i])) {
                            $pdesc = $request->input("descpor")[$i];
                        }
                        //$precioneto = $cantidad * $precio;
                        $arr = [
                            'nombre'     => $nombre,
                            'cantidad'   => $cantidad,
                            'codigo'     => $codigo,
                            'precio'     => $precio,
                            'descpor'    => $pdesc,
                            'copago'     => "",
                            'descuento'  => $descuento,
                            'precioneto' => $precio_neto,
                            'detalle'    => $descrip_prod,
                            'iva'        => $iva,
                            'obs_pac'    => $observacion,
                        ];
                        array_push($arr_total, $arr);
                    }
                }
            }
            foreach ($arr_total as $valor) {
                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['precioneto'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['copago'],
                    'check_iva'            => $valor['iva'],
                    'codigo'               => $valor['obs_pac'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,

                ];

                Ct_detalle_venta::create($detalle); //activo
            }
            $val_tol = $request['total1'];

            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.02');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.01.02',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.01');

                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.01.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            if (!empty($request->input("id_tip_pago"))) {
                for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                    if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                        $arr_pagos = [
                            'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                            'fecha_pago'     => $request->input("fecha_pago")[$i],
                            'numero_pago'    => $request->input("numero_pago")[$i],
                            'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                            'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                            'giradoa'        => $request->input("giradoa")[$i],
                            'valor'          => $request->input("valor")[$i],
                            'valor_base'     => $request->input("valor_base")[$i],
                        ];
                        array_push($arr_p, $arr_pagos);
                    }
                }
            }

            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            $arr_agendas  = [];
            $data['id']   = $id_venta;
            $data['tipo'] = 'VEN-R';
            $msj          = Ct_Kardex::generar_kardex($data);
            DB::commit();
            return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'arr_agendas' => $arr_agendas, 'kardex' => $msj, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
        } catch (\Exception $e) {

            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function pdf_ieced($id)
    {
        $ventas   = Ct_ventas::findorfail($id);
        $vistaurl = "contable.ventas.pdf_ieced";
        $view     = \View::make($vistaurl, compact('ventas'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function visualizador_pdf($id, Request $request)
    {
        $ventas  = Ct_ventas::find($id);
        $empresa = Empresa::find($ventas->id_empresa);
        return view('contable.ventas.html_visualizador', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    public function envio_correo($id, Request $request)
    {
        $rol     = Ct_ventas::find($id);
        $usuario = $rol->cliente;
        $correo  = $rol->cliente->email_representante;
        $mes     = "";
        $roldate = date('m', strtotime($rol->fecha));
        if ($roldate == 1) {
            $mes = 'Enero';
        } elseif ($roldate == 2) {
            $mes = 'Febrero';
        } elseif ($roldate == 3) {
            $mes = 'Marzo';
        } elseif ($roldate == 4) {
            $mes = 'Abril';
        } elseif ($roldate == 5) {
            $mes = 'Mayo';
        } elseif ($roldate == 6) {
            $mes = 'Junio';
        } elseif ($roldate == 7) {
            $mes = 'Julio';
        } elseif ($roldate == 8) {
            $mes = 'Agosto';
        } elseif ($roldate == 9) {
            $mes = 'Septiembre';
        } elseif ($roldate == 10) {
            $mes = 'Octubre';
        } elseif ($roldate == 11) {
            $mes = 'Noviembre';
        } elseif ($roldate == 12) {
            $mes = 'Diciembre';
        }
        $rol_2 = $this->pdf_ieced($id);

        $asunto = "Comprobante " . $rol->fecha;
        $titulo = "Comprobante  " . $rol->fecha . '.pdf';
        Mail::send('mails.clientes', ['cliente' => $usuario], function ($msj) use ($correo, $asunto, $rol_2, $titulo) {
            $msj->subject($asunto);
            $msj->from('rol@mdconsgroup.com', 'Sistema de Comprobantes SIAAM');
            $msj->to($correo);
            $msj->attachData($rol_2, $titulo, [
                'mime' => 'application/pdf',
            ]);
        });
        return 'ok';
    }
    public function guardarCiudad(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $value = [
            'ciudad_representante'      => $request->ciudad,
            'direccion_representante'   => $request->direccion,
            'id_usuariomod'             => $idusuario,
            'ip_modificacion'           => $ip_cliente,
        ];
        Ct_Clientes::where('identificacion', $request->cedula)->update($value);

        return "ok";
    }
    public function asignar_egreso($id_venta)
    {

        $egresos = InvCabMovimientos::join('inv_documentos_bodegas', 'inv_cab_movimientos.id_documento_bodega', '=', 'inv_documentos_bodegas.id')
            ->leftjoin('venta_kardex', 'inv_cab_movimientos.id', '=', 'venta_kardex.id_cab_moviento')
            ->where('inv_cab_movimientos.estado', '=', 1)
            ->where('inv_documentos_bodegas.tipo', '=', 'E')
            ->whereNull('venta_kardex.id_cab_moviento')
            ->select('inv_cab_movimientos.*')
            ->get();
        //dd($egresos);

        // SELECT icm.*
        // FROM inv_cab_movimientos icm
        // INNER JOIN inv_documentos_bodegas idb on icm.id_documento_bodega = idb.id
        // left JOIN venta_kardex vk ON vk.id_cab_moviento = icm.id
        // where icm.estado like 1 AND idb.tipo like 'E' AND vk.id_cab_moviento is null;

        return view('contable.ventas.asignar_egreso', ['id_venta' => $id_venta, 'egresos' => $egresos]);
    }
    public function guardar_asignar_egreso(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $id_venta = $request['id_venta'];
            // $venta = Ct_ventas::find($id_venta);
            $venta_kardex = Venta_Kardex::where('id_venta', $id_venta)->first();

            if (!is_null($venta_kardex)) {
                DB::rollBack();
                return ['status' => 'error', 'msj' => 'Esta venta ya fue asignada a un egreso'];
            }

            for ($i = 0; $i < count($request['id_egreso']); $i++) {
                $arr = [
                    'id_venta'           => $id_venta,
                    'id_cab_moviento'    => $request['id_egreso'][$i],
                    'id_usuariocrea'     => Auth::user()->id,
                    'id_usuariomod'      => Auth::user()->id,
                    'ip_modificacion'    => $_SERVER["REMOTE_ADDR"],
                    'ip_creacion'        => $_SERVER["REMOTE_ADDR"],

                ];
                Venta_Kardex::insert($arr);
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }
    public function graphics2(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio = $request['anio'];
        //dd($request->all());
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        //0  es publico
        //1  es privado
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = $i;
            if ($i < 10) {
                $mes = '0' . $i;
            }

            $empresas_relacionadas = Ct_Empresa_Relacionada::where('estado', 1)->get();
            $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

            $arr_e = [];
            foreach ($empresas_relacionadas as $relacionada) {
                array_push($arr_e, $relacionada->id_cliente);
            }

            $ventas_mes = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereMonth('ct_ventas.fecha', $mes)
                ->whereYear('ct_ventas.fecha', $anio)
                ->where('ct_ventas.estado', '<>', '0');

            if (isset($arr_e)) {
                $ventas_mes = $ventas_mes->whereNotIn('id_cliente', $arr_e);
                $venta_clienter = Ct_ventas::where('id_empresa', $empresa->id)
                    ->where('estado', 1)
                    ->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO'])
                    ->whereIn('id_cliente', $arr_e)
                    ->whereMonth('ct_ventas.fecha', $mes)
                    ->whereYear('ct_ventas.fecha', $anio)
                    ->select(
                        DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                        DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                        DB::raw('SUM(ct_ventas.descuento) as descuento'),
                        DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                        DB::raw('SUM(ct_ventas.impuesto) as impuesto')
                    )->get();
            } else {
                $venta_clienter = [];
            }

            $ventas_mes = $ventas_mes->groupBy('s.tipo')->select(
                's.tipo',
                DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                DB::raw('SUM(ct_ventas.descuento) as descuento'),
                DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                DB::raw('SUM(ct_ventas.impuesto) as impuesto')
            )->get();



            $estructure['mes'] = $mes;
            $total0 = [
                "subtotal0" => 0,
                "subtotal12" => 0,
                "descuento" => 0,
                "impuesto"  => 0,
                "cantidad"  => 0
            ];
            $estructure['privado']   = $total0;
            $estructure['publico']   = $total0;
            $estructure['particular']   = $total0;
            $estructure['particular_relacionado'] = $total0;


            foreach ($ventas_mes as $value) {
                $total = [
                    "subtotal0" => is_null($value->subtotal0) ? 0 : $value->subtotal0,
                    "subtotal12" => is_null($value->subtotal12) ? 0 : $value->subtotal12,
                    "descuento" => is_null($value->descuento) ? 0 : $value->descuento,
                    "impuesto"  => is_null($value->impuesto) ? 0 : $value->impuesto,
                    "cantidad"  => is_null($value->cantidadf) ? 0 : $value->cantidadf
                ];
                if ($value->tipo == 0) {
                    $estructure['publico'] = $total;
                }
                if ($value->tipo == 1) {
                    $estructure['privado'] = $total;
                }
                if ($value->tipo == 2) {
                    $estructure['particular'] = $total;
                }
            }

            foreach ($venta_clienter as $vr) {
                $total = [
                    "subtotal0" => is_null($vr->subtotal0) ? 0 : $vr->subtotal0,
                    "subtotal12" => is_null($vr->subtotal12) ? 0 : $vr->subtotal12,
                    "descuento" => is_null($vr->descuento) ? 0 : $vr->descuento,
                    "impuesto"  => is_null($vr->impuesto) ? 0 : $vr->impuesto,
                    "cantidad"  => is_null($vr->cantidadf) ? 0 : $vr->cantidadf
                ];

                if (!isset($data["particular"])) {
                    $data["particular"] = $vr;
                }

                if (!isset($data["particular_sin_seguro"])) {
                    $data["particular_sin_seguro"] = $vr;
                }

                if (!isset($data["particular_relacionado"])) {
                    $data["particular_relacionado"] = $vr;
                }

                if (!isset($data["privado"])) {
                    $data["privado"] = $vr;
                }

                if (!isset($data["publico"])) {
                    $data["publico"] = $vr;
                }


                $estructure['particular_relacionado'] = $total;

                array_push($array_agrupado, $estructure);
            }
        }

        //dd($array_agrupado);

        $array_agrupado_anio = array();
        $anios               = [$request->anio];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')

                ->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereYear('ct_ventas.fecha', $z)
                ->where('ct_ventas.estado', '<>', '0')
                ->select(
                    DB::raw('SUM(ct_ventas.total_final) as total_final'),
                    DB::raw('SUM(ingreso.total) as ingresado'),
                    DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'),
                    DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'),
                    DB::raw('SUM(CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'),
                    DB::raw('SUM( CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'),
                    DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'),
                    DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc')
                )->get();

            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        $empresa = Empresa::all();

        return view('contable/ventas/graphics2', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'empresas' => $empresa, 'id_empresa' => $id_empresa, 'request' => $request]);
    }
    public function indexFcVendedor(Request $request)
    {
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $vendedor = $request->id_vendedor;
        $nombreV = '';
        if ($vendedor != null) {
            $nombreV = User::find($vendedor);
        }
        $rolUser = Auth::user()->id_tipo_usuario;
        $idUser = Auth::user()->id;
        $ventas = Ct_ventas::where('id_empresa', Session::get('id_empresa'))->where('estado', 1)->where('tipo', "VEN-FA")->whereNotNull('ci_vendedor');
        if (!is_null($fecha_desde)) {
            $ventas = $ventas->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
        } else {
            $ventas = $ventas->where('fecha', '<=', $fecha_hasta);
        }
        if ($rolUser == 17) {
            $ventas =  $ventas->where('ci_vendedor', $idUser);
        } else {
            if ($vendedor != null) {
                $ventas =  $ventas->where('ci_vendedor', $vendedor);
            }
        }
        $ventas = $ventas->get();
        return view('contable.ventas.reporte.index_fc_vendedor', ['nombreV' => $nombreV, 'ventas' => $ventas, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'vendedor' => $vendedor, 'rolUser' => $rolUser]);
    }
    public function indexFcVendedor_pdf(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $vendedor = $request->id_vendedor;
        $nombreV = '';
        if ($vendedor != null) {
            $nombreV = User::find($vendedor);
        }
        $rolUser = Auth::user()->id_tipo_usuario;
        $idUser = Auth::user()->id;
        $ventas = Ct_ventas::where('id_empresa', Session::get('id_empresa'))->where('estado', 1)->where('tipo', "VEN-FA")->whereNotNull('ci_vendedor');
        if (!is_null($fecha_desde)) {
            $ventas = $ventas->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
        } else {
            $ventas = $ventas->where('fecha', '<=', $fecha_hasta);
        }
        if ($rolUser == 17) {
            $ventas =  $ventas->where('ci_vendedor', $idUser);
        } else {
            if ($vendedor != null) {
                $ventas =  $ventas->where('ci_vendedor', $vendedor);
            }
        }
        $ventas = $ventas->get();
        //dd($ventas);
        $view = \View::make('contable.ventas.reporte.index_fc_vendedor_pdf', compact('ventas', 'fecha_desde', 'fecha_hasta', 'empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('contable.ventas.reporte.index_fc_vendedor_pdf');
    }

    // Método para verificar ventas sin egreso asignado
    public function ventas_sin_egreso(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        if ($empresa->inventario != 1 || $empresa->egreso_kardex != 1) {
            return redirect()->back()->with('error', 'La empresa no maneja inventario con egresos.');
        }
        $ventas_sin_egreso = Ct_ventas::leftJoin('venta_kardex', 'ct_ventas.id', '=', 'venta_kardex.id_venta')
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->where('ct_ventas.estado', '!=', '0')
            ->whereNull('venta_kardex.id_venta')
            ->select('ct_ventas.*')
            ->orderBy('ct_ventas.fecha', 'desc')
            ->paginate(20);
        return view('contable.ventas.ventas_sin_egreso', compact('ventas_sin_egreso', 'empresa'));
    }

    // Método para asignar egresos masivamente
    public function asignar_egresos_masivo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if ($empresa->inventario != 1 || $empresa->egreso_kardex != 1) {
            return response()->json(['status' => 'error', 'msj' => 'La empresa no maneja inventario con egresos.']);
        }
        DB::beginTransaction();
        try {
            // Obtener ventas sin egreso
            $ventas_sin_egreso = Ct_ventas::leftJoin('venta_kardex', 'ct_ventas.id', '=', 'venta_kardex.id_venta')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->where('ct_ventas.estado', '!=', '0')
                ->whereNull('venta_kardex.id_venta')
                ->select('ct_ventas.id', 'ct_ventas.fecha', 'ct_ventas.nro_comprobante', 'ct_ventas.numero')
                ->orderBy('ct_ventas.fecha', 'asc')
                ->limit(20)
                ->get();
            // Obtener egresos disponibles (no asignados)
            $egresos_disponibles = InvCabMovimientos::join('inv_documentos_bodegas', 'inv_cab_movimientos.id_documento_bodega', '=', 'inv_documentos_bodegas.id')
                ->leftJoin('venta_kardex', 'inv_cab_movimientos.id', '=', 'venta_kardex.id_cab_moviento')
                ->where('inv_cab_movimientos.estado', '=', 1)
                ->where('inv_documentos_bodegas.tipo', '=', 'E')
                ->whereNull('venta_kardex.id_cab_moviento')
                ->select('inv_cab_movimientos.id', 'inv_cab_movimientos.fecha', 'inv_cab_movimientos.num_doc_cont')
                ->orderBy('inv_cab_movimientos.fecha', 'asc')
                ->get();
            $asignaciones = 0;
            $asignaciones_exactas = 0;
            $asignaciones_fallback = 0;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            foreach ($ventas_sin_egreso as $venta) {
                $ventaClave = $this->normalizarDocumento($venta->nro_comprobante ?: $venta->numero);
                $egreso_asignar = null;
                if ($ventaClave !== '') {
                    $egreso_asignar = $egresos_disponibles->first(function ($egreso) use ($ventaClave) {
                        return $this->normalizarDocumento($egreso->num_doc_cont) === $ventaClave;
                    });
                }
                if (!$egreso_asignar) {
                    $egreso_asignar = $egresos_disponibles->where('fecha', '>=', $venta->fecha)->first();
                }
                if (!$egreso_asignar) {
                    $egreso_asignar = $egresos_disponibles->first();
                }
                if (!$egreso_asignar) {
                    // Crear nuevo egreso para la venta
                    $egreso_asignar = $this->crearEgresoParaVenta($venta, $id_empresa, $idusuario, $ip_cliente);
                }
                if ($egreso_asignar) {
                    Venta_Kardex::create([
                        'id_venta' => $venta->id,
                        'id_cab_moviento' => $egreso_asignar->id,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                    if ($this->normalizarDocumento($egreso_asignar->num_doc_cont) === $ventaClave && $ventaClave !== '') {
                        $asignaciones_exactas++;
                    } else {
                        $asignaciones_fallback++;
                    }
                    $egresos_disponibles = $egresos_disponibles->reject(function ($item) use ($egreso_asignar) {
                        return $item->id === $egreso_asignar->id;
                    });
                    $asignaciones++;
                }
            }
            DB::commit();
            $mensaje = "Se asignaron {$asignaciones} egresos a ventas sin egreso.";
            if ($asignaciones_exactas > 0) {
                $mensaje .= " {$asignaciones_exactas} coincidencias exactas por número de factura.";
            }
            if ($asignaciones_fallback > 0) {
                $mensaje .= " {$asignaciones_fallback} asignaciones por fecha cuando no se encontró egreso exacto.";
            }
            return response()->json([
                'status' => 'success',
                'msj' => trim($mensaje)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msj' => $e->getMessage()]);
        }
    }

    private function normalizarDocumento($valor)
    {
        return preg_replace('/[^0-9]/', '', trim((string) $valor));
    }

    private function crearEgresoParaVenta($venta, $id_empresa, $idusuario, $ip_cliente)
    {
        // Obtener bodega principal
        $id_bodega = env('BODEGA_PRINCIPAL', 1);
        // Documento EGP
        $documento = InvDocumentosBodegas::where('abreviatura_documento', 'EGP')->first();
        $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, 'EGP');
        if ($secuencia == 0) {
            return null; // No se pudo obtener secuencia
        }
        $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
            ->where('id_bodega', $id_bodega)
            ->first();
        if (!$transaccion) {
            return null;
        }
        // Crear cabecera
        $cab_mov_inv = new InvCabMovimientos;
        $cab_mov_inv->id_documento_bodega = $documento->id;
        $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
        $cab_mov_inv->id_bodega_origen = $id_bodega;
        $cab_mov_inv->numero_documento = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
        $cab_mov_inv->observacion = "EGRESO POR VENTA AUTOMATICA" . $venta->nro_comprobante;
        $cab_mov_inv->fecha = $venta->fecha;
        $cab_mov_inv->num_doc_cont = $venta->nro_comprobante;
        $cab_mov_inv->id_empresa = $id_empresa;
        $cab_mov_inv->ip_creacion = $ip_cliente;
        $cab_mov_inv->ip_modificacion = $ip_cliente;
        $cab_mov_inv->id_usuariocrea = $idusuario;
        $cab_mov_inv->id_usuariomod = $idusuario;
        $cab_mov_inv->save();
        // Obtener detalles de venta
        $detalles_venta = Ct_detalle_venta::where('id_ct_ventas', $venta->id)->get();
        foreach ($detalles_venta as $detalle) {
            // Verificar si es producto
            $producto = Ct_productos::where('codigo', $detalle->id_ct_productos)->first();
            if (!$producto) continue;
            $insumo = Ct_productos_insumos::where('id_producto', $producto->id)->first();
            if (!$insumo) continue;
            $prod = Producto::find($insumo->id_insumo);
            if (!$prod) continue;
            // Buscar inventario
            $inventario = InvInventario::where('id_producto', $prod->id)->where('id_bodega', $id_bodega)->first();
            if (!$inventario) continue;
            // Buscar serie disponible
            $serie = InvInventarioSerie::where('id_inv_inventario', $inventario->id)
                ->where('cantidad_usos_kardex', '>', 0)
                ->orderBy('fecha_vence', 'asc')
                ->first();
            if (!$serie) continue;
            // Calcular cantidad a egresar (asumir 1 por ahora, o detalle->cantidad)
            $cantidad = $detalle->cantidad; // Asumir que cantidad es la cantidad vendida
            $cant_uso = 1; // Asumir
            $valor_precio = $inventario->costo_promedio;
            $iva = 0; // Calcular si necesario
            // Crear detalle
            $det_mov_inv = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto = $prod->id;
            $det_mov_inv->id_inv_inventario = $inventario->id;
            $det_mov_inv->cantidad = $cantidad;
            $det_mov_inv->cant_uso = $cantidad * $cant_uso;
            $det_mov_inv->serie = $serie->serie;
            $det_mov_inv->lote = $serie->lote;
            $det_mov_inv->fecha_vence = $serie->fecha_vence;
            $det_mov_inv->valor_unitario = $valor_precio;
            $det_mov_inv->subtotal = $valor_precio * $cantidad;
            $det_mov_inv->descuento = 0;
            $det_mov_inv->iva = $iva;
            $det_mov_inv->total = $det_mov_inv->subtotal + $iva;
            $det_mov_inv->motivo = $cab_mov_inv->observacion;
            $det_mov_inv->ip_creacion = $ip_cliente;
            $det_mov_inv->ip_modificacion = $ip_cliente;
            $det_mov_inv->id_usuariocrea = $idusuario;
            $det_mov_inv->id_usuariomod = $idusuario;
            $det_mov_inv->save();
        }
        // Calcular totales
        InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
        // Kardex
        InvKardex::setKardex($cab_mov_inv->id);
        // Asiento
        InvContableCab::setAsientoContablePedido($cab_mov_inv->id);
        return $cab_mov_inv;
    }
}
