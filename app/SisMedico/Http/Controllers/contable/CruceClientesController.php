<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_acreedores;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Cruce_Valores_Cliente;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Detalle_Cruce_Cuentas_Clientes;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;
use Exception;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Cruce_Cuentas_Clientes;
use Sis_medico\Ct_Detalle_Cruce_Clientes;
use Sis_medico\Ct_Detalle_Pago_Cruce;
use Sis_medico\Contable;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Sucursales;

class CruceClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public static function confgClientesComerciales()
    {
        $id_empresa = Session::get("id_empresa");
        $cuenta = "1.01.02.05.01";
        if ($id_empresa == "1793135579001") {
            $cuenta = "1.01.02.01.01";
        }
        return $cuenta;
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $anticipo = Ct_Cruce_Valores_Cliente::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $cliente = Ct_Clientes::where('estado', '1')->get();
        //dd($anticipo);
        return view('contable/cruce_valores_cliente/index', ['anticipo' => $anticipo, 'empresa' => $empresa, 'cliente' => $cliente]);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        //dd($tipo_pago);

        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable/cruce_valores_cliente/create', ['sucursales' => $sucursales, 'tipo_pago' => $tipo_pago, 'empresa' => $empresa, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'min_date' => $min_date]);
    }
    public function bancos(Request $request)
    {

        if ($request['opciones'] != '1') {
            $cuenta_banco = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CRUCECLIENTES_BANCOS');
            $banco = DB::table('plan_cuentas')->where('estado', '!=', '0')->where('id_padre', $cuenta_banco->id_padre)->get();
            return $banco;
        } else {
            $cuenta_caja = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CRUCECLIENTES_CAJA');
            $caja = DB::table('plan_cuentas')->where('id_padre', $cuenta_caja->id_padre)->get();
            return $caja;
        }
        return ['value' => 'error'];
    }
    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $consulta_anticipo = null;
        $saldo_redondeado = 0;
        $id_empresa = $request->session()->get('id_empresa');
        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->fecha);
        $verifica_cierre = json_decode($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            $fechahoy = $request['fecha'];
            $objeto_validar = new Validate_Decimals();
            $consulta_facturas = 0;
            $total = 0;
            $input_actualiza = null;
            $contador_ctv = DB::table('ct_cruce_valores_cliente')->get()->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_cruce_valores_cliente')->max('id');

                if (($max_id >= 1) && ($max_id <= 10)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if (($max_id >= 10) && ($max_id < 99)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if (($max_id >= 100) && ($max_id < 1000)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }

                if ($max_id == 1000) {
                    $numero_factura = $max_id;
                }
            }
            $cabeceraa = [
                'observacion'                   => $request['concepto'],
                'fecha_asiento'                 => $fechahoy,
                'fact_numero'                   => $numero_factura,
                'valor'                         => $objeto_validar->set_round($request['total_anticipos']),
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'sucursal'                      => $request['sucursal'],
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
            //cruce de clientes
            $input_cruce = [
                'detalle'                       => $request['concepto'],
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'fecha_pago'                    => $fechahoy,
                'id_cliente'                    => $request['id_cliente'],
                'secuencia'                     => $numero_factura,
                'total_disponible'              => $objeto_validar->set_round($request['total_anticipos']),
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            $id_comprobante = Ct_Cruce_Valores_Cliente::insertGetId($input_cruce);

            if ($request['contador_a'] == null && $request['contador'] == null) {
                DB::rollBack();
                return ['status' => 'error', 'msj' => 'Sin datos', 'titulos' => 'Error'];
            }
            for ($i = 0; $i <= $request['contador_a']; $i++) {
                if ($request['numero_a' . $i] != null) {
                    $consulta_anticipo = Ct_Comprobante_Ingreso::where('id', $request['idac' . $i])->where('id_empresa', $id_empresa)->first();
                    if (!is_null($consulta_anticipo)) {
                        if (!is_null($request['abono_a' . $i])) {
                            $saldo_redondeado1 = $objeto_validar->set_round($request['abono_a' . $i]);
                            $saldo_redondeado2 = $objeto_validar->set_round($request['saldo_a' . $i]);
                            $saldo_redondeado = $saldo_redondeado2 - $saldo_redondeado1;
                            if (($request['abono_a' . $i]) > 0) {
                                if ($saldo_redondeado > 0) {
                                    $input_actualiza = [
                                        'deficit_ingreso' => $saldo_redondeado,
                                        'estado' => '1',
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                    $consulta_anticipo->update($input_actualiza);
                                } else {
                                    $input_actualiza = [
                                        'deficit_ingreso' => '0.00',
                                        'estado' => '1',
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                    $consulta_anticipo->update($input_actualiza);
                                }
                                Ct_Detalle_Pago_Cruce::create([
                                    'id_comprobante' => $id_comprobante,
                                    'id_comp_ingreso' => $consulta_anticipo->id,
                                    'numero' => $request['numero_a' . $i],
                                    'fecha' => $request['fecha'],
                                    'valor_ant' => $request['saldo_a' . $i],
                                    'valor' => $request['abono_a' . $i],
                                    'concepto' => $request['concepto_a' . $i],
                                    'estado'                         => '1',
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'                => $ip_cliente,
                                    'id_usuariocrea'                 => $idusuario,
                                    'id_usuariomod'                  => $idusuario,
                                ]);
                            }
                        }
                    }
                }
            }
            $saldo_redondeado = 0;
            for ($i = 0; $i <= $request['contador']; $i++) {
                if (!is_null($request['abono' . $i])) {
                    if (($request['abono' . $i]) > 0) {
                        if (!is_null($request['numero' . $i])) {
                            $consulta_facturas = Ct_Ventas::where('id', $request['id_fact' . $i])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
                            if ($consulta_facturas != '[]') {
                                if ($request['abono' . $i] > $consulta_facturas->valor_contable) {
                                    $total = $request['abono' . $i] - $consulta_facturas->valor_contable;
                                    $saldo_redondeado = $objeto_validar->set_round($total);
                                } else {
                                    $total = $consulta_facturas->valor_contable - $request['abono' . $i];
                                    $saldo_redondeado = $objeto_validar->set_round($total);
                                }
                                if ($total > 0) {
                                    $input_actualiza = [
                                        //no actualizao el estado de la factura porque tiene que segui saliendo en las deudas del proveedor
                                        'valor_contable' => $saldo_redondeado,
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                    $consulta_facturas->update($input_actualiza);
                                } else {
                                    $input_actualiza = [
                                        'estado_pago' => '3', // este estado es paara que ya no salga en la consulta solo si cumple el total de la factura pagada
                                        'valor_contable' => '0.00',
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                    $consulta_facturas->update($input_actualiza);
                                }
                                Ct_Detalle_Cruce_Clientes::create([
                                    'id_comprobante'                 => $id_comprobante,
                                    'fecha'                          => $request['fecha'],
                                    'observaciones'                  => $request['concepto' . $i],
                                    'id_factura'                     => $consulta_facturas->id,
                                    'secuencia_factura'              => $request['numero' . $i],
                                    'total_factura'                  => $request['saldo' . $i],
                                    'total'                          => $request['abono' . $i],
                                    'estado'                         => '1',
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'                => $ip_cliente,
                                    'id_usuariocrea'                 => $idusuario,
                                    'id_usuariomod'                  => $idusuario,
                                ]);
                            }
                        }
                    }
                }
            }
            //cambio 17 de mayo del 2020 1.01.02.01.01
            //$conste = Plan_Cuentas::where('id', CruceClientesController::confgClientesComerciales())->first();
            $cuentacxccliente_com = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CRUCECLIENTES_CXCCLI_COME');
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuentacxccliente_com->cuenta_guardar,
                'descripcion'                   => $cuentacxccliente_com->nombre_mostrar,
                'fecha'                         => $fechahoy,
                'haber'                         => $objeto_validar->set_round($request['total_anticipos']),
                'debe'                          => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                    => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ]);
            $cuenta_ant_cliente = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CRUCECLIENTES_ANT_CLIENTE');
            //$conste2 = Plan_Cuentas::where('id', '2.01.10.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_ant_cliente->cuenta_guardar,
                'descripcion'                   => $cuenta_ant_cliente->nombre_mostrar,
                'fecha'                         => $fechahoy,
                'debe'                          => $objeto_validar->set_round($request['total_anticipos']),
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ]);
            //     return $id_comprobante;
            //} 
            //else {
            //     return 'error vacios';
            // }

            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id' => $id_comprobante, 'id_asiento' => $id_asiento_cabecera];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['status' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }

        return 'error no guardo nada';
    }
    public function pdf_comprobante($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $anticipo    = Ct_Cruce_Valores::find($id);
        $tipo_pago = Ct_Tipo_Pago::where('id', $anticipo->id_tipo_pago)->first();
        $empresa = Empresa::where('id', $anticipo->id_empresa)->first();
        $proveedor = Proveedor::where('id', $anticipo->id_proveedor)->first();
        $vistaurl = "contable.anticipos.pdf_comprobante_anticipo";
        $usuario_crea = DB::table('users')->where('id', $anticipo->id_usuariocrea)->first();
        $view     = \View::make($vistaurl, compact('emp', 'proveedor', 'empresa', 'anticipo', 'tipo_pago', 'usuario_crea'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //izquierda campos de la tabla y derecha lo que esta en la vista index en el buscador
        $constraints = [
            'detalle'             => $request['detalle'],
            'id_cliente'          => $request['id_cliente'],
            'fecha_pago'          => $request['fecha_pago'],
            'id_asiento_cabecera' => $request['id_asiento_cabecera'],
        ];
        $compras = $this->doSearchingQuery($constraints, $request);
        $cliente = Ct_Clientes::where('estado', '1')->get();
        return view('contable/cruce_valores_cliente/index', ['anticipo' => $compras, 'cliente' => $cliente, 'searchingVals' => $constraints]);
    }
    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = Ct_Cruce_Valores_Cliente::where('id_empresa', $id_empresa);

        $fields = array_keys($constraints);
        $index = 0;
        if ($request->id != null) {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], $constraint);
                }
                $index++;
            }
        } else {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                $index++;
            }
        }

        return $query->orderBy('id', 'desc')->paginate(20);
    }
    /* public function obtener_secuencia(){
        $contador_ctv = DB::table('ct_cruce_valores')->get()->count();
        if($contador_ctv == 0){
       
            //return 'No Retorno nada';
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            return  $numero_factura;
        }else{
            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = DB::table('ct_cruce_valores')->max('id');
            if(($max_id>=1)&&($max_id<10)){
               $nu = $max_id+1;
               $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);   
               return  $numero_factura;         
            }
            if(($max_id>=10)&&($max_id<99)){
               $nu = $max_id+1;
               $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT); 
               return  $numero_factura;
            }

            if(($max_id>=100)&&($max_id<1000)){
               $nu = $max_id+1;
               $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
               return  $numero_factura;
            }

            if($max_id == 1000){
               $numero_factura = $max_id;
               return  $numero_factura;
            }
        
        }
    }*/
    public function obtener_anticipos(Request $request)
    {
        $id_proveedor = $request['proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $data = 0;
        $tipo = $request['tipo'];
        $facturas = '[]';
        $deudas = null;
        $deudas = Ct_Comprobante_Ingreso::where('estado', '1')->where('tipo', '2')->where('id_empresa', $id_empresa)->where('id_cliente', $id_proveedor)->where('deficit_ingreso', '>', '0')->get();
        if ($deudas != '[]') {
            return $deudas;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function anular($id, Request $request)
    {
        //funcion anular + suma de valores
        if (!is_null($id)) {
            $comp_ingreso = Ct_Cruce_Valores_Cliente::where('estado', '1')->where('id', $id)->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            $sumars = 0;
            if (!is_null($comp_ingreso)) {
                if (!is_null($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_Ventas::where('id', $value->id_factura)->where('estado', '1')->first();
                        if (!is_null($consulta_venta)) {
                            $valor = $consulta_venta->valor_contable;
                            $suma = ($value->total) + $valor;
                            $sumars += $value->total;
                            $input_actualiza = [
                                'valor_contable'                => $suma,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                        }
                    }
                    foreach ($comp_ingreso->detalle2 as $x) {
                        $ps = Ct_Comprobante_Ingreso::find($x->id_comprobante_ingreso);
                        if (!is_null($ps)) {
                            //$ingreso= $ps->deficit_ingreso;
                            $ps->deficit_ingreso = $x->valor;
                            $ps->ip_modificacion = $ip_cliente;
                            $ps->id_usuariomod = $idusuario;
                            $ps->save();
                        }
                    }
                }
                $input = [
                    'total_disponible'              => $sumars,
                    'estado'                        => '0',
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
                foreach ($detalles as $value) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }

                LogAsiento::anulacion("CVC", $id_asiento, $asiento->id);

                return redirect()->route('cruce_clientes.index');
            }
        } else {
            return 'error';
        }
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $comprobante_ingreso = Ct_Cruce_Valores_Cliente::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_ingreso = Ct_Detalle_Cruce_Clientes::where('id_comprobante', $comprobante_ingreso->id)->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $detalle_anticipo = Ct_Detalle_Pago_Cruce::where('id_comprobante', $comprobante_ingreso->id)->where('estado', '1')->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)->where('estado', 1)->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($comprobante_ingreso->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        return view('contable/cruce_valores_cliente/edit', ['id_empresa' => $id_empresa, 'empresa' => $empresa, 'detalle_pago' => $detalle_anticipo, 'detalle_cruce' => $detalle_ingreso, 'cruce_valores' => $comprobante_ingreso, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'user_vendedor' => $user_vendedor, 'sucursal' => $sucursal]);
    }

    public function anular_anticipo_cliente($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $concepto = $request['concepto'];
        $idusuario  = Auth::user()->id;

        DB::beginTransaction();
        try {
            $cruce_cliente = Ct_Cruce_Valores_Cliente::find($id);
            $cab_asiento = Ct_Asientos_Cabecera::find($cruce_cliente->id_asiento_cabecera);
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            /*$verifica_cierre = LogAsiento::verificar_cierre_anio($cab_asiento->fecha_asiento);
            if ($verifica_cierre['status'] == 'error') {
                return $verifica_cierre;
            }*/

            if ($cruce_cliente->estado == 0) {
                DB::rollback();
                return ["status" => "error", "msj" => "Ya se encuentra anulado este comprobante"];
            }


            $detalle_cliente = Ct_Detalle_Cruce_Clientes::where('id_comprobante', $id)->where('estado', '1')->get();
            $pago_cruce = Ct_Detalle_Pago_Cruce::where('id_comprobante', $id)->where('estado', '1')->get();

            if ($detalle_cliente != []) {
                foreach ($detalle_cliente as $det_ing) {
                    $ventas = Ct_ventas::find($det_ing->id_factura);

                    //dd($ventas);
                    if (!is_null($ventas)) {
                        $suma = $det_ing->total + $ventas->valor_contable;
                        $ventas->valor_contable     = $suma;
                        $ventas->ip_creacion        = $ip_cliente;
                        $ventas->ip_modificacion    = $ip_cliente;
                        $ventas->id_usuariocrea     = $idusuario;
                        $ventas->id_usuariomod      = $idusuario;
                        $ventas->save();
                    }
                }
            }

            if ($pago_cruce != []) {
                foreach ($pago_cruce as $pag_cruce) {
                    $comprobante_ingreso = Ct_Comprobante_Ingreso::find($pag_cruce->id_comp_ingreso);
                    if (!is_null($comprobante_ingreso)) {
                        $comprobante_ingreso->deficit_ingreso    = $comprobante_ingreso->total;
                        $comprobante_ingreso->estado             = '1';
                        $comprobante_ingreso->ip_modificacion    = $ip_cliente;
                        $comprobante_ingreso->id_usuariomod      = $idusuario;
                        $comprobante_ingreso->save();
                    }
                }
            }



            $asiento = Ct_Asientos_Cabecera::findorfail($cruce_cliente->id_asiento_cabecera);
            $asiento->estado = 1;
            $asiento->id_usuariomod = $idusuario;
            $asiento->save();
            $detalles = $asiento->detalles;
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => strtoupper($concepto),
                'fecha_asiento'   => $asiento->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $cruce_cliente->secuencia,
                'valor'           => $asiento->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal'        => $asiento->sucursal,
            ]);
            foreach ($detalles as $value) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $asiento->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }

            $cruce_cliente->estado             = '0';
            $cruce_cliente->ip_modificacion    = $ip_cliente;
            $cruce_cliente->id_usuariomod      = $idusuario;
            $cruce_cliente->save();

            LogAsiento::anulacion("CL-CC", $id_asiento, $asiento->id);
            DB::commit();
            return ["status" => "success", "msj" => "Eliminado con exito", "id_asiento" => $id_asiento];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "ocurrio un error", "exp" => $e->getMessage()];
        }
    }

    public function anular_anticipo_cliente_no_sirve($id, Request $request)
    {


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $concepto = $request['concepto'];
        $idusuario  = Auth::user()->id;
        $cruce_cliente = Ct_Cruce_Valores_Cliente::find($id);
        $detalle_cliente = Ct_Detalle_Cruce_Clientes::where('id_comprobante', $id)->where('estado', '1')->get();
        $pago_cruce = Ct_Detalle_Pago_Cruce::where('id_comprobante', $id)->where('estado', '1')->get();
        $inp = [
            'estado'             => '0',
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,

        ];
        $cruce_cliente->update($inp);
        if (!is_null($detalle_cliente)) {
            //dd($detalle_cliente);
            foreach ($detalle_cliente as $det_ing) {

                $detalle_cli = Ct_Detalle_Cruce_Clientes::where('id', $det_ing->id)->where('estado', '1')->first();
                $ventas = Ct_ventas::where('id', $det_ing->id_factura)->where('estado', '>', '0')->where('valor_contable', '<>', '0')->first();
                //dd($ventas);
                if (!is_null($ventas)) {
                    $valor = $ventas->valor_contable;

                    $suma = ($det_ing->total) + $valor;
                    //dd($suma);
                    $input_actualiza = [
                        'valor_contable'                => $suma,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $ventas->update($input_actualiza);
                }
                /*     $input = [
                'estado'             => '0',
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
                ];
                $detalle_cli->update($input); */
            }
        }

        if (!is_null($pago_cruce)) {
            foreach ($pago_cruce as $pag_cruce) {
                $comprobante_ingreso = Ct_Comprobante_Ingreso::find($pag_cruce->id_comp_ingreso);
                $val_ant = $pag_cruce->valor_ant;
                //dd($comprobante_ingreso);
                if (!is_null($comprobante_ingreso) && $comprobante_ingreso != '[]') {
                    $i_actualiza = [
                        'deficit_ingreso'      => $val_ant,
                        'estado'             => '1',
                        'ip_modificacion'    => $ip_cliente,
                        'id_usuariomod'      => $idusuario,
                    ];
                    $comprobante_ingreso->update($i_actualiza);
                }
            }
        }



        $asiento = Ct_Asientos_Cabecera::findorfail($cruce_cliente->id_asiento_cabecera);
        //  dd($asiento);
        $asiento->estado = 1;
        $asiento->id_usuariomod = $idusuario;
        $asiento->save();
        $detalles = $asiento->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => strtoupper($concepto),
            'fecha_asiento'   => $asiento->fecha_asiento,
            'id_empresa'      => $id_empresa,
            'fact_numero'     => $cruce_cliente->secuencia,
            'valor'           => $asiento->valor,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        foreach ($detalles as $value) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $value->id_plan_cuenta,
                'debe'                => $value->haber,
                'haber'               => $value->debe,
                'descripcion'         => $value->descripcion,
                'fecha'               => $asiento->fecha_asiento,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        LogAsiento::anulacion("CL-CC", $id_asiento, $asiento->id);
        return 'ok';
        DB::commit();
        return ["status" => "success", "msj" => "Eliminado con exito", "id_asiento" => $id_asiento];



        return 'ok';
        //return redirect()->route('cruce.index');
    }
    public function cruce_cuentas(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $anticipo = Ct_Cruce_Cuentas_Clientes::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $clientes = Ct_Clientes::where('estado', '1')->get();
        //dd($proveedores);
        //dd($empresa);
        return view('contable/cruce_cuentas_ventas/index', ['anticipo' => $anticipo, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    public function cruce_cuentas_create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $facturas = Ct_ventas::where('id_empresa', $id_empresa)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->get();
        $empresa_general = Empresa::all();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        //$cuentas = Plan_Cuentas::all();
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pc', 'plan_cuentas.id', '=', 'pc.id_plan')
            ->where('pc.id_empresa', session('id_empresa'))
            ->get([
                'plan_cuentas.id',
                'plan_cuentas.nombre',
                'plan_cuentas.descripcion',
                'plan_cuentas.estado',
                'plan_cuentas.id_padre',
                'plan_cuentas.id_usuariocrea',
                'plan_cuentas.id_usuariomod',
                'plan_cuentas.ip_modificacion',
                'plan_cuentas.created_at',
                'plan_cuentas.updated_at',
                'plan_cuentas.naturaleza',
                'plan_cuentas.naturaleza_2',
                'pc.plan',
            ]);
        /*if (Auth::user()->id == '0921605895') {
            $cuentas1 = Plan_Cuentas::join('plan_cuentas_empresa as pc','plan_cuentas.id','=','pc.id_plan')
            ->get([
                'plan_cuentas.id',
                'plan_cuentas.nombre',
                'plan_cuentas.descripcion',
                'plan_cuentas.estado',
                'plan_cuentas.id_padre',
                'plan_cuentas.id_usuariocrea',
                'plan_cuentas.id_usuariomod',
                'plan_cuentas.ip_modificacion',
                'plan_cuentas.created_at',
                'plan_cuentas.updated_at',
                'plan_cuentas.naturaleza',
                'plan_cuentas.naturaleza_2',
                'pc.plan',
            ]);
        }*/
    
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        /*$cierreCabecera = Ct_Cierre_Cabecera::where('id_empresa',$id_empresa)->where('estado','1')->orderBy('id','desc')->first();
        $mindate = null;
        if(!is_null($cierreCabecera)){
            $mindate = date('Y-m-d',strtotime($cierreCabecera->fecha_cierre));
        }*/
        $sucursales = Ct_Sucursales::where('estado', 1)
        ->where('id_empresa', $id_empresa)
        ->get();
        return view('contable/cruce_cuentas_ventas/create', ['sucursales'=>$sucursales,'tipo_pago' => $tipo_pago, 'cuentas' => $cuentas, 'facturas' => $facturas, 'clientes' => $clientes, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'min_date' => $min_date]);
    }
    public function traervalores(Request $request)
    {
        $compras = Ct_ventas::find($request['codigo']);

        return [$compras->id, $compras->concepto, $compras->valor_contable];
    }
    public function cruce_cuentas_edit($id, Request $request)
    {
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $facturas = Ct_ventas::where('id_empresa', $id_empresa)->where('estado', '<>', '0')->get();
        $empresa_general = Empresa::all();
        $proveedores = Proveedor::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $cruce = Ct_Cruce_Cuentas_Clientes::find($id);
        $cuentas = Plan_Cuentas::all();
        //dd($tipo_pago);
        return view('contable/cruce_cuentas_ventas/edit', ['tipo_pago' => $tipo_pago, 'cruce' => $cruce, 'cuentas' => $cuentas, 'clientes' => $clientes, 'facturas' => $facturas, 'proveedores' => $proveedores, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }
    public function cruce_cuentas_store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fechahoy = $request['fecha_hoy'];
        $id_factura = $request['id_facturas'];

        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            $verificar = Ct_Cruce_Cuentas_Clientes::where('id_factura', $id_factura)->where('estado', 1)->first();
            //if (is_null($verificar)) {
                if (!is_null($request['contador'])) {
                    
                    $id_empresa = $request->session()->get('id_empresa');
                    $contador_ctv = DB::table('ct_cruce_cuentas_clientes')->where('id_empresa', $id_empresa)->get()->count();
                    $numero_factura = 0;
                    if ($contador_ctv == 0) {
                        $num = '1';
                        $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                    } else {
                        //Obtener Ultimo Registro de la Tabla ct_compras
                        $max_id = DB::table('ct_cruce_cuentas_clientes')->where('id_empresa', $id_empresa)->max('id');
                        if (strlen($max_id) < 10) {
                            $nu = $max_id + 1;
                            $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                        }
                    }
                    $cabeceraa = [
                        'observacion'                   => "Fact # " . $request['id_facturas'] . " " . $request['concepto'],
                        'fecha_asiento'                 => $fechahoy,
                        'fact_numero'                   => $numero_factura,
                        'valor'                         => $request['total'],
                        'id_empresa'                    => $id_empresa,
                        'estado'                        => '1',
                        'sucursal'                      => $request['sucursal'],
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                    //QUERY COMPRAS
                    $consulta_compra = Ct_ventas::where('id', $request['id_facturas'])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();

                    if (!is_null($consulta_compra)) {
                        $valor = $consulta_compra->valor_contable - $request['total'];
                        $input_actualiza = [
                            'valor_contable'                => $valor,
                            'estado_pago'                   => '2',
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariomod'                 => $idusuario,
                        ];
                        $consulta_compra->update($input_actualiza);
                        $input = [
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'fecha'                         => $request['fecha_hoy'],
                            'detalle'                       => $request['concepto'],
                            'total'                         => $request['total'],
                            'id_factura'                    => $request['id_facturas'],
                            'id_cliente'                    => $consulta_compra->id_cliente,
                            'secuencia'                     => $numero_factura,
                            'estado'                        => '1',
                            'id_empresa'                    => $id_empresa,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                        ];
                        $id_debito = Ct_Cruce_Cuentas_Clientes::insertGetId($input);

                        $valor = 0;
                        $primerarray = array();
                        for ($i = 0; $i <= $request['contador']; $i++) {
                            if ($request['visibilidad' . $i] == '1') {
                                if (!is_null($request['codigo' . $i])) {
                                    Ct_Detalle_Cruce_Cuentas_Clientes::create([
                                        'codigo' => $request['codigo' . $i],
                                        'id_comprobante' => $id_debito,
                                        'secuencia_factura' => $consulta_compra->numero,
                                        'fecha' => $request['fecha_hoy'],
                                        'estado' => '1',
                                        'total_factura' => '0',
                                        'total' => $request['valor' . $i],
                                        'observaciones' => $request['detalle' . $i],
                                        'ip_creacion'                   => $ip_cliente,
                                        'ip_modificacion'               => $ip_cliente,
                                        'id_usuariocrea'                => $idusuario,
                                        'id_usuariomod'                 => $idusuario,
                                    ]);
                                    $plan_cuentasx = Plan_Cuentas::find($request['codigo' . $i]);
                                    if (!is_null($plan_cuentasx)) {
                                        Ct_Asientos_Detalle::create([
                                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                                            'id_plan_cuenta'                => $request['codigo' . $i],
                                            'descripcion'                   => $plan_cuentasx->nombre,
                                            'fecha'                         => $request['fecha_hoy'],
                                            'debe'                         => $request['valor' . $i],
                                            'haber'                          => '0',
                                            'estado'                        => '1',
                                            'id_usuariocrea'                => $idusuario,
                                            'id_usuariomod'                 => $idusuario,
                                            'ip_creacion'                   => $ip_cliente,
                                            'ip_modificacion'               => $ip_cliente,
                                        ]);
                                    } else {
                                        DB::rollBack();
                                        return ['status' => 'error', 'msj' => trans('contableM.configure_cuenta') . ' ' . $request['codigo' . $i]];
                                    }
                                }
                            }
                        }
                        //$desc_cuenta = Plan_Cuentas::find(CruceClientesController::confgClientesComerciales());

                        $cuentacxccliente_com = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CRUCECLIENTES_CXCCLI_COME');
                        if ($cuentacxccliente_com) {
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera'           => $id_asiento_cabecera,
                                'id_plan_cuenta'                => $cuentacxccliente_com->cuenta_guardar,
                                'descripcion'                   => $cuentacxccliente_com->nombre_mostrar,
                                'fecha'                         => $request['fecha_hoy'],
                                'haber'                          => $request['total'],
                                'debe'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        } else {
                            DB::rollBack();
                            return ['status' => 'error', 'msj' => trans('contableM.configure_cuenta') . ' CRUCECLIENTES_CXCCLI_COME'];
                        }
                        DB::commit();
                        return ['status' => 'success', 'msj' => trans('contableM.operacion_exito'), 'id' => $id_debito, 'id_asiento' => $id_asiento_cabecera, 'numero_factura' => $numero_factura];
                    }
                } else {
                    DB::rollBack();
                    return ['status' => 'error', 'msj' => trans('contableM.error_campos_vacios')];
                }
            // } else {
            //     DB::rollBack();
            //     return ['status' => 'error', 'msj' => trans('contableM.cruce_cliente_null')];
            // }
        } catch (Exception $ex) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'line' => $ex->getLine()];
        }


        //return 'error no guardo nada';
    }
    public function anular_cruce_cuentas($id, Request $request)
    {
        DB::beginTransaction();
        try {
            if (!is_null($id)) {
                $comp_ingreso = Ct_Cruce_Cuentas_Clientes::where('estado', '1')->where('id', $id)->first();
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $concepto = $request['concepto'];
                $id_empresa = $request->session()->get('id_empresa');
                $idusuario  = Auth::user()->id;
                if (!is_null($comp_ingreso)) {
                    $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comp_ingreso->fecha));
                    if ($verifica_cierre->status == 'error') {
                        return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                    }
                    // ahora actualizo el valor y le sumo lo que ya le había restado
                    //dd($comp_ingreso->detalle);  219.12

                    $consulta_venta = Ct_ventas::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->first();

                    if (!is_null($consulta_venta)) {
                        $valor = $consulta_venta->valor_contable + $comp_ingreso->total;
                        $input_actualiza = [
                            'valor_contable'                => $valor,
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariomod'                 => $idusuario,
                        ];
                        $consulta_venta->update($input_actualiza);
                    }

                    $input = [
                        'estado' => '0',
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $comp_ingreso->update($input);
                    $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                    if (!is_null($asiento)) {
                        $asiento->estado = 1;
                        $asiento->id_usuariomod = $idusuario;
                        $asiento->save();
                        $detalles = $asiento->detalles;
                        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                            'observacion'     => strtoupper($concepto),
                            'fecha_asiento'   => $asiento->fecha_asiento,
                            'id_empresa'      => $id_empresa,
                            'fact_numero'     => $comp_ingreso->secuencia,
                            'valor'           => $asiento->valor,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'sucursal'        => $asiento->sucursal,
                        ]);
                        foreach ($detalles as $value) {
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $id_asiento,
                                'id_plan_cuenta'      => $value->id_plan_cuenta,
                                'debe'                => $value->haber,
                                'haber'               => $value->debe,
                                'descripcion'         => $value->descripcion,
                                'fecha'               => $asiento->fecha_asiento,
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                            ]);
                        }
                        LogAsiento::anulacion("CL-CC", $id_asiento, $comp_ingreso->id_asiento_cabecera);
                        DB::commit();
                        return ['status' => 'success', 'msj' => trans('contableM.operacion_exito')];
                        //return redirect()->route('acreedores_cegreso');
                    } else {
                        DB::rollBack();
                        return ['status' => 'error', 'msj' => trans('contableM.anulacioncorrecta')];
                    }
                } else {
                    DB::rollBack();
                    return ['status' => 'error', 'msj' => trans('contableM.ComprobantedeIngreso') . ' null'];
                }
            } else {
                return ['status' => 'error', 'msj' => 'ID null'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msj' => 'Ocurrio un error...', 'exp' => $e->getMessage(),  'line' => $e->getLine()];
        }
    }
    public function buscar_cruce(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $constraints = [
            'id'                  => $request['id'],
            'id_cliente'           => $request['id_cliente'],
            'secuencia'            => $request['secuencia'],
            'detalle'             => $request['detalle'],
            'fecha'               => $request['fecha'],

        ];

        $comp_egreso = $this->doSearchingQuery2($constraints, $id_empresa);
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/cruce_cuentas_ventas/index', ['anticipo' => $comp_egreso, 'searchingVals' => $constraints, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    private function doSearchingQuery2($constraints, $id_empresa)
    {

        $query  = Ct_Cruce_Cuentas_Clientes::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }


        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }
}
