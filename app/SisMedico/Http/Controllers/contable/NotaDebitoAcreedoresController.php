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
use Sis_medico\Ct_Detalle_Debito_Acreedores;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_rubros;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Detalle_Anticipo_Proveedores;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;
use Exception;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Debito_Acreedores;
use Sis_medico\Ct_Detalle_Rubro_Acreedores;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;

class NotaDebitoAcreedoresController extends Controller
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
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor = Proveedor::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $anticipo = Ct_Debito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa)->paginate(20);
        //dd($anticipo);
        return view('contable/debito_acreedores/index', ['nota_debito' => $anticipo, 'proveedor' => $proveedor, 'empresa' => $empresa]);
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $constraints = [
            'id'                     => $request['id'],
            'id_proveedor'           => $request['id_proveedor'],
            'concepto'               => $request['concepto'],
            'serie'                  => $request['serie'],
            'secuencia'              => $request['secuencia'],
            'fecha_factura'          => $request['fecha_debito'],
            'id_asiento_cabecera'    => $request['id_asiento_cabecera']
        ];
        $compras = $this->doSearchingQuery($constraints, $request);
        //dd($compras);
        $proveedor = Proveedor::where('estado', '1')->get();
        //dd($constraints);
        return view('contable/debito_acreedores/index', ['nota_debito' => $compras, 'searchingVals' => $constraints, 'proveedor' => $proveedor, 'tipo_comprobante' => $tipo_comprobante, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = Ct_Debito_Acreedores::where('id_empresa', $id_empresa)->where('estado', '1');
        //dd($query->get());
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

        return $query->paginate(20);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $proveedores = Proveedor::all();
        $rubros = Ct_rubros::where('estado', '1')->get();
        $bodega = bodega::where('estado', '1')->get();
        $cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');
        $iva_param = Ct_Configuraciones::where('id_plan', $cuentaiva->cuenta_guardar)->first();
        $idusuario  = Auth::user()->id;
        if ($idusuario == '0922290697') {
            //dd($iva_param);
        }
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //dd($tipo_pago);
        /*$cierreCabecera = Ct_Cierre_Cabecera::where('id_empresa',$id_empresa)->where('estado','1')->orderBy('id','desc')->first();
        $mindate = null;
        if(!is_null($cierreCabecera)){
            $mindate = date('Y-m-d',strtotime($cierreCabecera->fecha_cierre));
        }*/
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/debito_acreedores/create', ['tipo_pago' => $tipo_pago, 'rubros' => $rubros, 'proveedores' => $proveedores, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'min_date' => $min_date, 'sucursales' => $sucursales]);
    }
    public function bancos(Request $request)
    {
         $id_empresa = $request->session()->get('id_empresa');
        if ($request['opciones'] != '1') {
            //$banco  = DB::table('plan_cuentas')->where('estado', '!=', '0')->where('id_padre', '1.01.01.2')->get();//1.01.01.02 BANCOS
            $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_CAJA');
            $banco   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();
            
            return $banco;
        } else {
            //$caja = DB::table('plan_cuentas')->where('id_padre', '1.01.01.1')->get();//1.01.01.01 CAJA
            $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_BANCOS');
            $caja   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();
            
            return $caja;
        }
        return ['value' => 'error'];
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        //$iva_param = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        $iva_param = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $comprobante = Ct_Debito_Acreedores::where('id_empresa', $id_empresa)->where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalles_comprobante = Ct_Detalle_Debito_Acreedores::where('id_debito_acreedores', $comprobante->id)->get();
        $detalles2 = Ct_Detalle_Rubro_Acreedores::where('id_debito', $comprobante->id)->get();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($comprobante->id_asiento_cabecera);
        if(!is_null($asientoCabecera)){
            $sucursal = $asientoCabecera->sucursal;
        }
        //dd($tipo_pago);
        return view('contable/debito_acreedores/edit', ['tipo_pago' => $tipo_pago, 'comprobante' => $comprobante, 'detalle' => $detalles_comprobante, 'detalles2' => $detalles2, 'iva_param' => $iva_param, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa,'sucursal' => $sucursal]);
    }
    public function store(Request $request)
    {
      
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $consulta_anticipo = null;
            $saldo_redondeado = 0;
            $objeto_validar = new Validate_Decimals();
            $consulta_facturas = 0;
            $fechahoy = $request['fecha_hoy'];
            $total = 0;
        DB::beginTransaction();
        try {
            /*$verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
            if ($verifica_cierre['status'] == 'error') {
                return ['status' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
            }*/
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
            //dd($verifica_cierre);
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'error' => $verifica_cierre->message];
            }
            $total_final = $objeto_validar->set_round($request['total_suma']);
            $input_actualiza = null;
            if ($request['contadore'] != null && $request['contador'] != null) {
                $id_empresa = $request->session()->get('id_empresa');
                $contador_ctv = DB::table('ct_debito_acreedores')->where('id_empresa', $id_empresa)->get()->count();
                $numero_factura = 0;
                if ($contador_ctv == 0) {

                    //return 'No Retorno nada';
                    $num = '1';
                    $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                } else {

                    //Obtener Ultimo Registro de la Tabla ct_compras
                    $max_id = DB::table('ct_debito_acreedores')->max('id');
                    if (strlen($max_id) < 10) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                    }
                }
                $nuevo_saldo = 0;
                $cabeceraa = [
                    'observacion'                   => 'Nota de Débito ' . $request['observacion'],
                    'fecha_asiento'                 => $fechahoy,
                    'fact_numero'                   => $numero_factura,
                    'valor'                         => $total_final,
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'sucursal'                      => $request['sucursal'],
                    //'estado_manual'                 => 3,
                ];

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                $input = [
                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'fechand'                       => $request['fechand'],
                    'concepto'                      => $request['concepto'],
                    'valor_contable'                => $request['total'],
                    'id_proveedor'                  => $request['id_proveedor'],
                    'estado'                        => '1',
                    'autorizacion'                  => $request['autorizacion'],
                    'f_autorizacion'                => $request['fecha'],
                    'serie_factura'                 => $request['serie_factura'],
                    'fecha_factura'                 => $request['fecha_factura'],
                    'fecha_caducidad'               => $request['fecha_caducidad'],
                    'autorizacion_factura'          => $request['autorizacion'],
                    'serie'                         => $request['serie'],
                    'secuencia'                     => $request['secuencia'],
                    'id_empresa'                    => $id_empresa,
                    'credito_tributario'            => $request['credito_tributario'],
                    'tipo_comprobante'              => $request['tipo_comprobante'],
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,

                ];
                $id_debito = Ct_Debito_Acreedores::insertGetId($input);
                $resultado = json_decode($request['listInvoice']);
                //echo "resultados" . $request['listInvoice'];

                foreach ($resultado as $value) {
                    $consulta_facturas = Ct_Compras::find($value->id);
                    //dd($consulta_facturas);
                    if ($consulta_facturas != '[]') {
                        $nuevo_saldo = $consulta_facturas->valor_contable - $value->abono;
                        $saldo_redondeado = $objeto_validar->set_round($nuevo_saldo);
                        $input_actualiza = null;

                        if ($saldo_redondeado >= 0) {
                            Ct_Detalle_Debito_Acreedores::create([
                                'id_debito_acreedores'           => $id_debito,
                                'vence'                          => $consulta_facturas->f_caducidad,
                                'concepto'                       => $consulta_facturas->observacion,
                                'id_factura'                     => $consulta_facturas->id,
                                'secuencia_factura'              => $consulta_facturas->secuencia,
                                'total_factura'                  => $consulta_facturas->valor_contable,
                                'total'                          => $value->abono,
                                'estado'                         => '1',
                                'ip_creacion'                    => $ip_cliente,
                                'ip_modificacion'                => $ip_cliente,
                                'id_usuariocrea'                 => $idusuario,
                                'id_usuariomod'                  => $idusuario,
                            ]);
                            $input_actualiza = [
                                //no actualizao el estado de la factura porque tiene que segui saliendo en las deudas del proveedor
                                'valor_contable' => $saldo_redondeado,
                                'id_usuariomod' => $idusuario,
                                'ip_modificacion' => $ip_cliente
                            ];
                            $consulta_facturas->update($input_actualiza);
                        }
                    }
                }

                for ($i = 0; $i < $request['contador']; $i++) {
                    if ($request['visibilidad' . $i] == 1) {
                        $consulta_rubro = Ct_rubros::where('codigo', $request['id_codigo' . $i])->first();
                        if ($consulta_rubro != '[]' || $consulta_rubro != null) {
                            Ct_Detalle_Rubro_Acreedores::create([
                                'id_debito' => $id_debito,
                                'codigo' => $consulta_rubro->codigo,
                                'nombre' => $consulta_rubro->nombre,
                                'concepto' => $request['detalle_rubro' . $i],
                                'valor' => $request['valor' . $i],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                            $cuenta_prov_loc = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOTADEBITOACREE_PROV_LOC');
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $id_asiento_cabecera,
                                'id_plan_cuenta' => $cuenta_prov_loc->cuenta_guardar,
                                'descripcion' => $cuenta_prov_loc->nombre_mostrar,
                                'fecha' => $request['fecha_hoy'],
                                'debe' => $request['total_base' . $i],
                                'haber' => '0',
                                'estado' => '1',
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);

                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera'           => $id_asiento_cabecera,
                                'id_plan_cuenta'                => $consulta_rubro->haber,
                                'descripcion'                   => $consulta_rubro->nombre,
                                'fecha'                         => $request['fecha_hoy'],
                                'haber'                         => $request['total_base' . $i],
                                'debe'                          => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                            ]);
                        }
                    }
                }
                DB::commit();
                return ['status' => 'success', 'msj' => trans('contableM.operacion_exito'), 'id' => $id_debito, 'id_asiento' => $id_asiento_cabecera];
            } else {
                DB::rollBack();
                return ['status' => 'error', 'msj' => trans('contableM.error_vacio')];
            }
        } catch(Exception $ex){
            DB::rollBack();
            return ['status' => 'error', 'msj' => $ex->getMessage()];
        }
        return ['status' => 'error', 'msj' => trans('contableM.no_guardo')];
    }
    public function anular($id, Request $request)
    {
        try {
            if (!is_null($id)) {
                $comp_ingreso = Ct_Debito_Acreedores::where('estado', '1')->where('id', $id)->first();
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $id_empresa = $request->session()->get('id_empresa');
                $idusuario  = Auth::user()->id;
                if (!is_null($comp_ingreso)) {
                    DB::beginTransaction();
                    // ahora actualizo el valor y le sumo lo que ya le había restado
                    //dd($comp_ingreso->detalle);  219.12
    
                    /*$verifica_cierre = LogAsiento::verificar_cierre_anio($comp_ingreso->fecha_factura);
                    if ($verifica_cierre['status'] == 'error') {
                        return ['status' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
                    }*/
                    $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comp_ingreso->fecha_factura));
                    if ($verifica_cierre->status == 'error') {
                        return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                    }
                    if (!is_null($comp_ingreso->detalle)) {
                        foreach ($comp_ingreso->detalle as $value) {
                            $consulta_venta = Ct_compras::where('id', $value->id_factura)->where('estado', '>', '0')->first();
    
                            if (!is_null($consulta_venta)) {
                                $valor = $consulta_venta->valor_contable;
                                $suma = ($value->total) + $valor;
                                $input_actualiza = [
                                    'valor_contable'                => $suma,
                                    'ip_modificacion'               => $ip_cliente,
                                    'id_usuariomod'                 => $idusuario,
                                ];
                                $consulta_venta->update($input_actualiza);
                            }
                        }
                    }
                    $input = [
                        'estado' => '0',
    
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $comp_ingreso->update($input);
                    $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                    $asiento->estado = 1;
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
                        'sucursal'        => $asiento->sucursal
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
                    LogAsiento::anulacion("ND-A", $id_asiento, $asiento->id);
                    // Log_Contable::create([
                    //     'tipo'           => 'NDA',
                    //     'valor_ant'      => $asiento->valor,
                    //     'valor'          => $asiento->valor,
                    //     'id_usuariocrea' => $idusuario,
                    //     'id_usuariomod'  => $idusuario,
                    //     'observacion'    => $asiento->concepto,
                    //     'id_ant'         => $comp_ingreso->id_asiento_cabecera,
                    //     'id_referencia'  => $id_asiento,
                    // ]);
                    DB::commit();
                    return ['status' => 'success', 'msj' => trans('contableM.anulacioncorrecta')];
                    //return redirect()->route('debitoacreedores.index');
                } else {
                    return ['status' => 'error', 'msj' => 'ID null'];
                }
            } else {
                return ['status' => 'error', 'msj' => 'ID null'];
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'line' => $ex->getLine()];
        }
        
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
    public function obtener_anticipos(Request $request)
    {
        $id_proveedor = $request['proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $data = 0;
        $tipo = $request['tipo'];
        $deudas = null;
        $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_comprobante_egreso as co', 'co.id_asiento_cabecera', 'c.id')
            ->where('co.id_proveedor', $id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->where('co.tipo', '2')
            ->where('co.estado', '1')
            ->select('co.secuencia', 'c.observacion', 'c.fecha_asiento', 'co.id_proveedor', 'co.valor_abono')
            ->orderby('co.id', 'desc')
            ->get();

        //dd($facturas);        
        if ($deudas != '[]') {
            return $deudas;
        } else {
            return ['value' => 'no resultados'];
        }
    }

    public function pdf_debito_acreedores($id, Request $request)
    {

        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');
        $iva_param = Ct_Configuraciones::where('id_plan', $cuentaiva->cuenta_guardar)->first();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $empresa_general = Empresa::all();
        $comprobante = Ct_Debito_Acreedores::where('id_empresa', $id_empresa)->where('id_empresa', $id_empresa)->where('id', $id)->first();
        //dd($comprobante);
        $detalles  = Ct_Asientos_Detalle::where('id_asiento_cabecera', $comprobante->id_asiento_cabecera)
            ->groupBy('id_plan_cuenta')
            ->select('id_plan_cuenta', 'descripcion')
            ->select(DB::raw('id_plan_cuenta, descripcion, SUM(debe) as debe, SUM(haber) as haber'))
            ->get();
        $detalles_comprobante = Ct_Detalle_Debito_Acreedores::where('id_debito_acreedores', $comprobante->id)->get();
        $detalles2 = Ct_Detalle_Rubro_Acreedores::where('id_debito', $comprobante->id)->get();
        //dd($tipo_pago);
        $view =  \View::make('contable.debito_acreedores.pdf_debito_acreedores', compact('empresa', 'tipo_pago', 'bodega', 'iva_param', 'c_tributario', 't_comprobante', 'comprobante', 'detalles_comprobante', 'detalles2', 'detalles'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('ph_esofafica  ph_esofafica.pdf');
    }
}
