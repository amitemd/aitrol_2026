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
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Producto;
use Sis_medico\Ct_Sucursales;
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
use Sis_medico\Contable;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Cruce_Cuentas;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Detalle_Cruce_Cuentas;
use Sis_medico\Ct_Detalle_Pago_Cruce_Prov;
use Sis_medico\Ct_Detalle_Pago_Cruce;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Comprobante_Secuencia;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas_Empresa;
class AnticipoProveedorController extends Controller
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
    public function relacionar_campos()
    {
        $variable = Ct_productos::where('codigo', '!=', null)->get();
        $variable1 = Producto::where('codigo', '!=', null)->get();
        foreach ($variable1 as $val) {
            foreach ($variable as $value) {
                if ($val->codigo == $value->codigo) {
                    //dd($val->codigo);
                    $input = [
                        'codigo_producto'        => $value->codigo,
                        'id_insumo'              => $val->id,
                        'id_producto'            => $value->id,
                        'id_usuariocrea'         => '0928572205',
                        'ip_creacion'            => '192.168.76.88',
                        'ip_modificacion'        => '192.168.76.88',
                        'id_usuariomod'          => '0928572205',
                    ];
                    Ct_productos_insumos::create($input);
                }
            }
        }
        return "ok gracias amigo";
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $anticipo = Ct_Cruce_Valores::where('id_empresa', $id_empresa);
        if ($request->id != null) {
            $anticipo = $anticipo->where('id', $request->id);
        } else {
            if ($request->id_proveedor != null) {
                $anticipo = $anticipo->where('id_proveedor', $request->id_proveedor);
            }
            if ($request->detalle != null) {
                $anticipo = $anticipo->where('detalle', 'LIKE', '%' . $request->detalle . '%');
            }
            if ($request->id_asiento_cabecera != null) {
                $anticipo = $anticipo->where('id_asiento_cabecera', $request->id_asiento_cabecera);
            }
        }
        $anticipo = $anticipo->orderBy('id', 'desc')->paginate(20);
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($proveedores);
        //dd($empresa);
        //dd($anticipo);
        return view('contable/cruce_valores/index', ['anticipo' => $anticipo, 'proveedores' => $proveedores, 'empresa' => $empresa, 'anticipo' => $anticipo, 'request' => $request]);
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
        $proveedores = Proveedor::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //dd($tipo_pago);

        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/cruce_valores/create', ['min_date' => $min_date, 'tipo_pago' => $tipo_pago, 'sucursales' => $sucursales, 'proveedores' => $proveedores, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }
    public function bancos(Request $request)
    {
        if ($request['opciones'] != '1') {
            $banco = DB::table('plan_cuentas')->where('estado', '!=', '0')->where('id_padre', '1.01.01.2')->get();
            return $banco;
        } else {
            $caja = DB::table('plan_cuentas')->where('id_padre', '1.01.01.1')->get();
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
        $fechahoy =  $request['fecha'];
        $objeto_validar = new Validate_Decimals();
        $consulta_facturas = 0;
        $total = 0;
        $input_actualiza = null;
        $contador_ctv = DB::table('ct_cruce_valores')->where('id_empresa', $id_empresa)->get();
        $numero_factura = 0;
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha']));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            // $empresas = Empresa::all();
            // foreach($empresas as $emp){
            //     $aux = '0000000000';

            //     $ct_secuencia = Ct_Comprobante_Secuencia::where('tipo', 6)->where('empresa', $emp->id)->first();
            //     if(is_null($ct_secuencia)){
            //         $cruce = Ct_Cruce_Valores::where('id_empresa', $emp->id)->get();
            //         foreach($cruce as $cr){
            //             if(intval($cr->secuencia) > $aux){
            //                 $aux = $cr->secuencia;
            //             }
            //         }
            //         Ct_Comprobante_Secuencia::create([
            //             'tipo'           => 6,
            //             'secuencia'      => $aux,
            //             'tipo_comprobante' =>2,
            //             'empresa'        => $emp->id,
            //             'id_usuariocrea' => $idusuario,
            //             'id_usuariomod'  => $idusuario,
            //             'ip_creacion'    => $ip_cliente,
            //             'ip_modificacion'=> $ip_cliente
            //         ]);
            //     }
            // }

            $numero_factura = LogAsiento::getSecuencia(6, 2);

            // if ($contador_ctv == 0) {
            //     $num = '1';
            //     $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            // } else {

            //     //Obtener Ultimo Registro de la Tabla ct_compras
            //     $max_id = DB::table('ct_cruce_valores')->max('id');

            //     if (($max_id >= 1) && ($max_id <= 10)) {
            //         $nu = $max_id + 1;
            //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            //     }

            //     if (($max_id >= 10) && ($max_id < 99)) {
            //         $nu = $max_id + 1;
            //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            //     }

            //     if (($max_id >= 100) && ($max_id < 1000)) {
            //         $nu = $max_id + 1;
            //         $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            //     }

            //     if ($max_id == 1000) {
            //         $numero_factura = $max_id;
            //     }
            // }

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
            $input_cruce = [
                'detalle'                       => $request['concepto'],
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'fecha_pago'                    => $fechahoy,
                'id_proveedor'                  => $request['id_proveedor'],
                'secuencia'                     => $numero_factura,
                'total_disponible'              => $objeto_validar->set_round($request['total_anticipos']),
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            $id_comprobante = Ct_Cruce_Valores::insertGetId($input_cruce);
            if ($request['contador_a'] != null && $request['contador'] != null) {
                for ($i = 0; $i <= $request['contador_a']; $i++) {
                    if (!is_null($request['numero_a' . $i])) {
                        $consulta_anticipo = Ct_Comprobante_Egreso::where('id', $request['id_act' . $i])->where('id_empresa', $id_empresa)->first();
                        if ($consulta_anticipo != '[]' && $consulta_anticipo != null) {
                            $saldo_redondeado1 = $objeto_validar->set_round($request['abono_a' . $i]);
                            $saldo_redondeado2 = $objeto_validar->set_round($request['saldo_a' . $i]);
                            $saldo_redondeado = $saldo_redondeado2 - $saldo_redondeado1;
                            if (($request['abono_a' . $i]) > 0) {
                                if ($saldo_redondeado >= 0) {
                                    $input_actualiza = [
                                        'valor_pago' => $saldo_redondeado,
                                        'estado' => '1',
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                } else {
                                    $input_actualiza = [
                                        'valor_pago' => '0.00',
                                        'estado' => '1',
                                        'id_usuariomod' => $idusuario,
                                        'ip_modificacion' => $ip_cliente
                                    ];
                                }
                                $consulta_anticipo->update($input_actualiza);
                                Ct_Detalle_Pago_Cruce_Prov::create([
                                    'id_comprobante' => $id_comprobante,
                                    'id_comp_ingreso' => $consulta_anticipo->id,
                                    'numero' => $request['numero_a' . $i],
                                    'fecha' =>  $request['fecha'],
                                    'valor_ant' => $request['saldo_a' . $i],
                                    'valor' => $request['abono_a' . $i],
                                    'concepto' => $request['concepto' . $i],
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
                for ($i = 0; $i <= $request['contador']; $i++) {
                    if (!is_null($request['abono' . $i])) {
                        if (($request['abono' . $i]) > 0) {

                            if (!is_null($request['id_actualiza' . $i])) {
                                $consulta_facturas = Ct_compras::where('id', $request['id_actualiza' . $i])->where('estado', '<>', '0')->first();
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
                                    } else {
                                        $input_actualiza = [
                                            'estado' => '3', // este estado es paara que ya no salga en la consulta solo si cumple el total de la factura pagada
                                            'valor_contable' => '0.00',
                                            'id_usuariomod' => $idusuario,
                                            'ip_modificacion' => $ip_cliente
                                        ];
                                    }
                                    $consulta_facturas->update($input_actualiza);
                                }
                                Ct_Detalle_Cruce::create([
                                    'id_comprobante'                 => $id_comprobante,
                                    'fecha'                          => $request['fecha'],
                                    'tipo'                           => $request['tipo' . $i],
                                    'observaciones'                  => $request['concepto_a' . $i],
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
                                // Contable::pagofactura($consulta_facturas->id,$id_comprobante,'CRUCEV');
                            }
                        }
                    }
                }
                //cambio 17 de mayo del 2020

                $cuenta_proveedor = $request['id_proveedor'];
                if (!is_null($cuenta_proveedor)) {
                    $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                    if (($consulta_en_proveedor) != null) {
                        $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();

                        // $cuenta_ant_prov = "1.01.04.03";
                        // $plan_empresa = Plan_Cuentas_Empresa::where('plan', $cuenta_ant_prov)->where('id_empresa', $id_empresa)->first();

                        // $cuenta_ant_prov = $plan_empresa->id_plan;
                        
                        // if ($id_empresa == "1793135579001") {
                        //     $cuenta_ant_prov = "1.01.04.03.01";
                        // }

                        //$cuenta_ant_prov = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ANTICIPOPROV_ANT_PROV');
                        //$plan_c = Plan_Cuentas::where('id', $cuenta_ant_prov)->first();

                        
                        $id_plan_config = LogConfig::busqueda('1.01.04.03.01');
                        // if(Auth::user()->id == "0957258056"){
                        //     $id_plan_config  = LogConfig::busqueda('ANTICIPOPROV_ANT_PROV');
                        // }
                        $cuenta_ant_prov = Plan_Cuentas::where('id', $id_plan_config)->first();
                        if (!is_null($cuenta_ant_prov)) {
                            if (!is_null($desc_cuenta) && $desc_cuenta != '[]') {
                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $cuenta_ant_prov->id,
                                    'descripcion'                   => $cuenta_ant_prov->nombre,
                                    'fecha'                         => $fechahoy,
                                    'haber'                         => $objeto_validar->set_round($request['total_anticipos']),
                                    'debe'                          => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $consulta_en_proveedor->id_cuentas,
                                    'descripcion'                   => $desc_cuenta->nombre,
                                    'fecha'                         => $fechahoy,
                                    'debe'                          => $objeto_validar->set_round($request['total_anticipos']),
                                    'haber'                         => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                            } else {
                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $cuenta_ant_prov->id,
                                    'descripcion'                   => $cuenta_ant_prov->nombre,
                                    'fecha'                         => $fechahoy,
                                    'haber'                         => $objeto_validar->set_round($request['total_anticipos']),
                                    'debe'                          => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $desc_cuenta->id,
                                    'descripcion'                   => $desc_cuenta->nombre,
                                    'fecha'                         => $fechahoy,
                                    'debe'                          => $objeto_validar->set_round($request['total_anticipos']),
                                    'haber'                         => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                    => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                            }
                        } else {
                            DB::rollBack();
                            return ['status' => 'error', 'msj' => trans('contableM.configure_cuenta') . ' 1.01.04.03.01'];
                        }
                    }
                }
                DB::commit();
                return ['status' => 'success', 'msj' => trans('contableM.operacion_exito'), 'id' => $id_comprobante];
            } else {
                return ['status' => 'error', 'msj' => trans('contableM.no_detalle_cruce')];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine()];
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
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }


    public function template()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('contable.plantilla.app-template');
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //izquierda campos de la tabla y derecha lo que esta en la vista index en el buscador
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id'                  => $request['id'],
            'id_proveedor'        => $request['id_proveedor'],
            'detalle'             => $request['detalle'],
            'fecha'               => $request['fecha'],
            'id_asiento_cabecera' => $request['id_asiento_cabecera'],
        ];
        $compras = $this->doSearchingQuery($constraints, $request);
        $proveedores = Proveedor::where('estado', '1')->get();
        return view('contable/cruce_valores/index', ['anticipo' => $compras, 'proveedores' => $proveedores, 'searchingVals' => $constraints, 'empresa' => $empresa, 'request' => $request]);
    }
    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = Ct_Cruce_Valores::where('id_empresa', $id_empresa)->where('estado', '1');
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
        $deudas = null;
        $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_comprobante_egreso as co', 'co.id_asiento_cabecera', 'c.id')
            ->where('co.id_proveedor', $id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->where('co.tipo', '2')
            ->where('co.valor_pago', '>', '0')
            ->where('co.estado', '1')
            ->select('co.secuencia', 'c.observacion', 'c.fecha_asiento', 'co.id_referencia', 'co.id_proveedor', 'co.valor_pago as valor_abono', 'co.id')
            ->orderby('co.id', 'desc')
            ->get();
        //dd($deudas);  
        if (!empty($deudas)) {
            return $deudas;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $comprobante_ingreso = Ct_Cruce_Valores::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $detalle_ingreso = Ct_Detalle_Cruce::where('id_comprobante', $comprobante_ingreso->id)->get();
        $detalle_anticipo = Ct_Detalle_Pago_Cruce_Prov::where('id_comprobante', $comprobante_ingreso->id)->where('estado', '1')->get();
        $empresa = Empresa::find($id_empresa);
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($comprobante_ingreso->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        return view('contable/cruce_valores/edit', ['id_empresa' => $id_empresa, 'empresa' => $empresa, 'detalle_pago' => $detalle_anticipo, 'detalle_cruce' => $detalle_ingreso, 'cruce_valores' => $comprobante_ingreso, 'sucursal' => $sucursal]);
    }

    public function anular_anticipo($id, Request $request)
    {
        //dd("aqi");

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $concepto = $request['concepto'];
        $idusuario  = Auth::user()->id;
        $comprobante_ingreso = Ct_Cruce_Valores::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $detalle_ingreso = Ct_Detalle_Cruce::where('id_comprobante', $id)->where('estado', '1')->get();
        $pago_cruce = Ct_Detalle_Pago_Cruce_Prov::where('id_comprobante', $id)->where('estado', '1')->get();
        $estado_ingreso = Ct_Cruce_Valores::where('id', $id)->where('estado', '<>', 0)->where('id_empresa', $id_empresa)->first();

        //dd($estado_ingreso); 
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comprobante_ingreso['fecha_pago']));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            if (!empty($estado_ingreso)) {

                $inp = [
                    'estado'             => '0',
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariomod'      => $idusuario,

                ];

                $comprobante_ingreso->update($inp);
                // dd($detalle_ingreso); 
                if (!is_null($detalle_ingreso)) {
                    //dd($detalle_ingreso);
                    foreach ($detalle_ingreso as $det_ing) {

                        $detalle_ing = Ct_Detalle_Cruce::where('id', $det_ing->id)->where('estado', '1')->first();
                        $compras = Ct_compras::where('id', $det_ing->id_factura)->where('estado', '>', '0')->where('id_empresa', $id_empresa)->first();
                        //dd($compras);
                        if (!is_null($compras)) {
                            $valor = $compras->valor_contable;

                            $suma = ($det_ing->total) + $valor;
                            //dd($suma);
                            $input_actualiza = [
                                'valor_contable'                => $suma,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $compras->update($input_actualiza);
                        }
                        $input = [
                            'estado'             => '0',
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                            'id_usuariocrea'     => $idusuario,
                            'id_usuariomod'      => $idusuario,
                        ];
                        $detalle_ing->update($input);
                    }
                }
                if (!is_null($pago_cruce)) {
                    foreach ($pago_cruce as $pag_cruce) {
                        $comprobante_egreso = Ct_Comprobante_Egreso::where('id', $pag_cruce->id_comp_ingreso)->first();
                        $val_ant = $pag_cruce->valor_ant;
                        //dd($val_ant);
                        if (!is_null($comprobante_egreso) && $comprobante_egreso != '[]') {
                            $i_actualiza = [
                                'valor_pago'         => $val_ant,
                                'ip_modificacion'    => $ip_cliente,
                                'id_usuariomod'      => $idusuario,
                            ];
                            $comprobante_egreso->update($i_actualiza);
                        }
                    }
                }

                $asiento = Ct_Asientos_Cabecera::find($comprobante_ingreso->id_asiento_cabecera);
                //dd($asiento);
                if (!is_null($asiento)) {
                    $asiento->estado = 1;
                    $asiento->id_usuariomod =  $idusuario;
                    $asiento->save();

                    $detalles = $asiento->detalles;
                    $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                        'observacion'     => strtoupper($concepto),
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comprobante_ingreso->secuencia,
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
                    LogAsiento::anulacion("AC-CV", $id_asiento, $asiento->id);
                    // Log_Contable::create([
                    //     'tipo'           => 'CV',
                    //     'valor_ant'      => $asiento->valor,
                    //     'valor'          => $asiento->valor,
                    //     'id_usuariocrea' => $idusuario,
                    //     'id_usuariomod'  => $idusuario,
                    //     'observacion'    => $asiento->concepto,
                    //     'id_ant'         => $asiento->id,
                    //     'id_referencia'  => $id_asiento,
                    // ]);
                }
                DB::commit();
                return ['status' => 'success', 'msj' => 'Operacion realizada Correctamente'];
                //return 'ok';
                //return redirect()->route('cruce.index');
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['status' => 'error', 'msj' =>  $e->getMessage()];
        }
        //dd("no entro ");
    }

    public function cruce_cuentas(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $anticipo = Ct_Cruce_Cuentas::where('id_empresa', $id_empresa)->orderBy('secuencia', 'DESC')->paginate(20);
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($proveedores);
        //dd($empresa);
        // dd($anticipo);
        return view('contable/cruce_cuentas/index', ['anticipo' => $anticipo, 'proveedores' => $proveedores, 'empresa' => $empresa]);
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
        $facturas = Ct_compras::where('id_empresa', $id_empresa)->where('estado', '<>', '0')->where('valor_contable', '>', '0')->get();
        $empresa_general = Empresa::all();
        $proveedores = Proveedor::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->select('plan_cuentas.*', 'pe.plan as id_plan_empresa', 'pe.nombre as nombre_plan')->get();
        //dd($tipo_pago);
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/cruce_cuentas/create', ['min_date' => $min_date, 'tipo_pago' => $tipo_pago, 'cuentas' => $cuentas, 'facturas' => $facturas, 'sucursales' => $sucursales, 'proveedores' => $proveedores, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa]);
    }
    public function traervalores(Request $request)
    {
        $compras = Ct_compras::find($request['codigo']);

        return [$compras->id, $compras->concepto, $compras->valor_contable];
    }
    public function cruce_cuentas_edit($id, Request $request)
    {
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodega = bodega::where('estado', '1')->get();
        $empresa_sucurs  = Empresa::findorfail($id_empresa);
        $facturas = Ct_compras::where('id_empresa', $id_empresa)->get();
        $empresa_general = Empresa::all();
        $proveedores = Proveedor::where('estado', '1')->get();
        $cruce = Ct_Cruce_Cuentas::find($id);
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $cuentas = Plan_Cuentas::all();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($cruce->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        //dd($tipo_pago);
        return view('contable/cruce_cuentas/edit', ['tipo_pago' => $tipo_pago, 'cruce' => $cruce, 'cuentas' => $cuentas, 'facturas' => $facturas, 'sucursales' => $sucursales, 'proveedores' => $proveedores, 'empresa' => $empresa, 'bodega' => $bodega, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'empresa' => $empresa, 'sucursal' => $sucursal]);
    }
    public function cruce_cuentas_store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fechahoy = $request['fecha_hoy'];
        $id_factura = $request['id_facturas'];
        $verificar = Ct_Cruce_Cuentas::where('id_factura', $id_factura)->first();
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_hoy']));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            if (!is_null($request['contador'])) {
                /*$verifica_cierre = LogAsiento::verificar_cierre_anio($fechahoy);
                if ($verifica_cierre['status'] == 'error') {
                    return ['status' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
                }*/
                $id_empresa = $request->session()->get('id_empresa');
                $contador_ctv = DB::table('ct_cruce_cuentas')->where('id_empresa', $id_empresa)->get()->count();
                $numero_factura = 0;
                if ($contador_ctv == 0) {
                    $num = '1';
                    $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
                } else {
                    //Obtener Ultimo Registro de la Tabla ct_compras
                    $max_id = DB::table('ct_cruce_cuentas')->where('id_empresa', $id_empresa)->orderBy('id', 'DESC')->first();
                    $max_id = intval($max_id->secuencia);
                    if (strlen($max_id) < 10) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                }
                $cabeceraa = [
                    'observacion' => "Fact # " . $request['id_facturas'] . " " . $request['concepto'],
                    'fecha_asiento' => $fechahoy,
                    'fact_numero' => $numero_factura,
                    'valor' => $request['total'],
                    'id_empresa' => $id_empresa,
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'sucursal' => $request['sucursal'],
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                //QUERY COMPRAS
                $consulta_compra = Ct_compras::where('id', $request['id_facturas'])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();

                if (!is_null($consulta_compra)) {
                    $valor = $consulta_compra->valor_contable - $request['total'];
                    $input_actualiza = [
                        'valor_contable'                => $valor,
                        'estado'                        => '2',
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $consulta_compra->update($input_actualiza);

                    $input = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'fecha' => $request['fecha_hoy'],
                        'detalle' => $request['concepto'],
                        'total' => $request['total'],
                        'id_factura' => $request['id_facturas'],
                        'id_proveedor' => $consulta_compra->proveedor,
                        'secuencia' => $numero_factura,
                        'estado' => '1',
                        'id_empresa' => $id_empresa,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ];
                    $id_debito = Ct_Cruce_Cuentas::insertGetId($input);
                    // Contable::pagofactura($consulta_compra->id,$id_debito,'CRUCEC');
                    $valor = 0;
                    $primerarray = array();
                    for ($i = 0; $i <= $request['contador']; $i++) {
                        if ($request['visibilidad' . $i] == '1') {
                            if (!is_null($request['codigo' . $i])) {
                                Ct_Detalle_Cruce_Cuentas::create([
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
                                        'haber'                         => $request['valor' . $i],
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
                    }

                    // $cuenta_prov = "2.01.03.01.01";
                    // $plan_empresa = Plan_Cuentas_Empresa::where('plan',$cuenta_prov)->where('id_empresa',$id_empresa)->first();
                    // $cuenta_prov = $plan_empresa->id_plan;
                    // if($id_empresa == "1793135579001"){
                    //     $confgcuenta_prov = "2.02.01.01.01";
                    // }

                    // $desc_cuenta = Plan_Cuentas::find($confgcuenta_prov);

                    //$cuenta_ant_prov = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ANTICIPOPROV_PROV_LOC');
                    $cuenta_ant_prov = LogConfig::busqueda('2.01.03.01.01');
                    //    $desc_cuenta = Plan_Cuentas::join('plan_cuentas_empresa as pe','pe.id_plan','plan_cuentas.id')->where('pe.plan',$cuenta_ant_prov)
                    //    ->where('pe.estado','!=','0')->where('pe.id_empresa',$id_empresa)->select('plan_cuentas.*')->first();
                    $desc_cuenta = Plan_Cuentas::find($cuenta_ant_prov);

                    if (is_null($desc_cuenta)) {
                        DB::rollback();
                        return ['status' => 'error', 'msj' => trans('contableM.configure_cuenta') . ' ' . trans('contableM.proveedores') . '...'];
                    }

                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $desc_cuenta->id,
                        'descripcion'                   => $desc_cuenta->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'debe'                          => $request['total'],
                        'haber'                         => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                    DB::commit();
                    return ['status' => 'success', 'msj' => 'Guardado con exito', 'id_debito' => $id_debito, 'id_asiento_cabecera' => $id_asiento_cabecera, 'id_numero_fact' => $numero_factura];
                }
            } else {
                DB::rollback();
                return ['status' => 'error', 'msj' => trans('contableM.error_campos_vacios')];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine()];

            //return 'error no guardo nada';
        }
        return 'error no guardo nada';
    }
    public function anular_cruce_cuentas($id, Request $request)
    {
        DB::beginTransaction();
        try {
            if (!is_null($id)) {
                $comp_ingreso = Ct_Cruce_Cuentas::where('estado', '1')->where('id', $id)->first();
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $concepto = $request['concepto'];
                $id_empresa = $request->session()->get('id_empresa');
                $idusuario  = Auth::user()->id;
                if (!is_null($comp_ingreso)) {
                    $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comp_ingreso->fecha));
                    if ($verifica_cierre->status == 'error') {
                        return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                    }
                    $consulta_venta = Ct_compras::where('id', $comp_ingreso->id_factura)->where('estado', '>', '0')->first();
                    if (!is_null($consulta_venta)) {
                        $valor = $consulta_venta->valor_contable + $comp_ingreso->total;
                        $input_actualiza = [
                            'valor_contable'                => $valor,
                            'estado'                        => '2',
                            'ip_modificacion'               => $ip_cliente,
                            'id_usuariomod'                 => $idusuario,
                        ];
                        $consulta_venta->update($input_actualiza);
                    } else {
                        DB::rollBack();
                        return ['status' => 'error', 'msj' => trans('contableM.no_compra')];
                    }
                    $input = [
                        'estado' => '0',
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $comp_ingreso->update($input);
                    Contable::recovery_price($comp_ingreso->id_factura, 'C');
                    $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
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
                    LogAsiento::anulacion("CC-A", $id_asiento, $asiento->id);
                    DB::commit();
                    return ['status' => 'success', 'msj' => trans('contableM.operacion_exito')];
                } else {
                    DB::rollBack();
                    return ['status' => 'error', 'msj' => trans('contableM.ComprobantedeIngreso') . ' null'];
                }
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
        $proveedores = Proveedor::where('estado', '1')->get();
        $constraints = [
            'id'                  => $request['id'],
            'id_proveedor'        => $request['id_proveedor'],
            'secuencia'           => $request['secuencia'],
            'detalle'             => $request['detalle'],
            'id_asiento_cabecera' => $request['id_asiento_cabecera'],
            'fecha_cheque'        => $request['fecha'],

        ];

        $comp_egreso = $this->doSearchingQuery2($constraints, $id_empresa);
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/cruce_cuentas/index', ['anticipo' => $comp_egreso, 'searchingVals' => $constraints, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }

    public function proveedorsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('razonsocial', 'LIKE', '%' . $request['search'] . '%')->where('estado', '1')->select('proveedor.id as id', 'proveedor.razonsocial as text')->get();
        }

        return response()->json($proveedor);
    }

    private function doSearchingQuery2($constraints, $id_empresa)
    {

        $query  = Ct_Cruce_Cuentas::query();
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

    public function pdf_cruces(Request $request, $id)
    {
        $id_empresa =  $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $datos = DB::table('ct_cruce_valores as crv')
            ->leftjoin('ct_detalle_cruce as cdc', 'cdc.id_comprobante', 'crv.id')
            ->where('crv.id', $id)
            ->select('crv.*', 'cdc.*')
            ->first();
        //dd($id); 
        $nombre = Proveedor::where('id', $datos->id_proveedor)->first();
        $datos2 = Ct_Detalle_Cruce::where('id_comprobante', $id)->get();
        //dd($datos2);
        $details = Ct_Detalle_Pago_Cruce_Prov::where('id_comprobante', $id)->get();
        $view =  \View::make('contable.cruce_valores.pdf_crucesdevalores', compact('datos', 'empresa', 'nombre', 'datos2', 'details'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('ph_esofafica  ph_esofafica.pdf');
    }
    public function fixegresos() {}
    public function pdf_cruce_cuentas(Request $request, $id)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $datos =  DB::table('ct_cruce_cuentas as crc')
            ->leftjoin('ct_detalle_cruce_cuentas as cdcc', 'cdcc.id_comprobante', 'crc.id')
            ->where('crc.id', $id)
            ->select('crc.*', 'cdcc.*')
            ->first();
        $cruces = Ct_Cruce_Cuentas::find($id);
        //dd($id)
        // 1: COM-FA 2: COM-FACT
        $detalles  = Ct_Asientos_Detalle::where('id_asiento_cabecera', $datos->id_asiento_cabecera)
            ->groupBy('id_plan_cuenta')
            ->select('id_plan_cuenta', 'descripcion')
            ->select(DB::raw('id_plan_cuenta, descripcion, SUM(debe) as debe, SUM(haber) as haber'))
            ->get();
        $compras = Ct_Compras::find($cruces->id_factura);

        $nombre = Proveedor::where('id', $datos->id_proveedor)->first();
        $datos2 = Ct_Detalle_Cruce_Cuentas::where('id_comprobante', $id)->get();
        $details = Ct_Detalle_Pago_Cruce_Prov::where('id_comprobante', $id)->get();
        $cruce = Ct_Cruce_Cuentas::find($id);
        //dd($cruce);
        $view =  \View::make('contable.cruce_cuentas.pdf_cruce_cuentas', compact('detalles', 'datos2', 'datos', 'empresa', 'nombre', 'details', 'cruce', 'compras'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('ph_esofafica  ph_esofafica.pdf');
    }
}
