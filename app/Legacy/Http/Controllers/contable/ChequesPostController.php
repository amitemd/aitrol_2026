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
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Detalle_Pago_Post;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Comprobante_Ingreso_Varios;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Empresa;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Cheques_Post;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Detalle_Cheque_Post;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso_Varios;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\EstadoResultado;
use Sis_medico\Financery;
use Sis_medico\Validate_Decimals;
use Sis_medico\Numeros_Letras;
use Sis_medico\ProyeccionFinanciera2;
use Sis_medico\Log_Contable;

class ChequesPostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $bancos= Ct_Bancos::where('estado','1')->get();
        $empresa= Empresa::where('id',$id_empresa)->first();
        $cheques = Ct_Cheques_Post::where('id_empresa', $id_empresa)->orderBy('id','desc')->paginate(20);
        //dd($acreedores);
        $cliente= Ct_Clientes::where('estado','1')->get();

        return view('contable/chequespost/index', ['cheques' => $cheques,'bancos'=>$bancos,'empresa'=>$empresa,'cliente'=>$cliente]);
    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $banco = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->get();
        $caja = Ct_Caja_Banco::where('estado', '1')->where('clase', '2')->get();
        $cliente= Ct_Clientes::where('estado','1')->get();
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $tipos = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $empresa= Empresa::where('id',$id_empresa)->first();
        return view('contable/chequespost/create', ['tipos_pagos' => $tipo_pago,'tipos'=>$tipos,'banco' => $banco,'cliente'=>$cliente,'divisas' => $divisas,'caja'=>$caja, 'bancos' => $lista_banco,'empresa'=>$empresa]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("sds");
        $constraints = [
            'id'                => $request['id'],
            'id_banco'              => $request['id_banco'],
            'id_cliente'            => $request['id_cliente'],
            'secuencia'             => $request['secuencia'],
            'concepto'              => $request['concepto'],
            'fecha'                 => $request['fecha'],
            'id_asiento_cabecera'   => $request['id_asiento_cabecera'],
        ];

        //dd($constraints);
        $acreedores = $this->doSearchingQuery($constraints);
        $bancos= Ct_Bancos::where('estado','1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::find($id_empresa);
        $cliente= Ct_Clientes::where('estado','1')->get();
        return view('contable/chequespost/index', ['cheques' => $acreedores,'cliente'=>$cliente, 'searchingVals' => $constraints,'bancos'=>$bancos,'empresa'=>$empresa]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Cheques_Post::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('estado', '1')->paginate(10);
    }

    public function store(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $contador_ctv = DB::table('ct_cheques_post')->get()->count();
        $objeto_validar = new Validate_Decimals();
        $numero_factura=0;
        $cuenta_chequepost = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CHPOST_CUENTACLIENTESCOM');
        $cuenta_chepostcg = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CHPOST_CAJAGENERAL');
        
        if ($request['contador'] != null) {
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_cheques_post')->max('id');
                if(strlen($max_id)<10){
                    $nu = $max_id+1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $input_cabecera = [
                'observacion' => $request['autollenar'].' Cheque postfechado:' . $numero_factura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($request['valor_total']),
                'fecha_asiento' => date('Y-m-d H:i:s'),
                'fact_numero' => $numero_factura,
                'valor' => $objeto_validar->set_round($request['valor_total']),
                'id_empresa' => $id_empresa,
                'estado' => '1',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //cuenta Cuentas por cobrar clientes comerciales

            $desc_cuenta = Plan_Cuentas::where('id', $cuenta_chequepost->cuenta_guardar)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_chequepost->cuenta_guardar,
                'descripcion'                   => $cuenta_chequepost->nombre_mostrar,
                'fecha'                         => $request['fecha'],
                'haber'                          => $objeto_validar->set_round($request['valor_total']),
                'debe'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'   =>  $request['autollenar'] . ' POR LA CANTIDAD DE ' . $request['valor_total'],
                'estado'          => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'           => $request['fecha'],
                'secuencia'       => $numero_factura,
                'divisas'         => '1',
                'id_empresa'      => $id_empresa,
                'total_ingreso'   => $objeto_validar->set_round($request['valor_total']),
                'id_cliente'      =>   $request['id_cliente'],
                'autollenar'      => $request['autollenar'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Cheques_Post::insertGetId($input_comprobante);
            for ($i = 0; $i < $request['contador']; $i++) {
                if($request['visibilidad'.$i]==1){
                    if($request['tipo'.$i]=='4'){
                        Ct_Detalle_Pago_Post::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }else if($request['tipo'.$i]=='6'){ 
                        Ct_Detalle_Pago_Post::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                       => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }else if($request['tipo'.$i]=='1'){
                        Ct_Detalle_Pago_Post::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }else{
                        Ct_Detalle_Pago_Post::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_banco'                       => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }
                }
               
               
            }
            for ($i = 0; $i <=$request['contador_a']; $i++) {
                if (!is_null($request['abono_a' . $i]) && $request['abono_a'.$i]>0) {
                    if ($request['id_cliente'] != null) {
                        $nuevo_saldof = $objeto_validar->set_round($request['abono_a' . $i]);
                        // cuenta: Caja General
                        $desc_cuenta = Plan_Cuentas::where('id', $cuenta_chepostcg->cuenta_guardar)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $cuenta_chepostcg->cuenta_guardar,
                            'descripcion'                   => $cuenta_chepostcg->nombre_mostrar,
                            'fecha'                         => $request['fecha'],
                            'debe'                          => $nuevo_saldof,
                            'haber'                         => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    $consulta_venta = null;
                    $input_comprobante = null;
                    if (!is_null($request['numero' . $i])) {
                        if (!is_null($request['contador'])) {
                            $consulta_venta = Ct_Ventas::where('numero', $request['numero' . $i])->first();
                            Ct_Detalle_Cheque_Post::create([
                                'id_comprobante'                 => $id_comprobante,
                                'fecha'                          => $request['fecha'],
                                'observaciones'                  => $request['autollenar'],
                                'id_factura'                     => $consulta_venta->id,
                                'secuencia_factura'              => $request['numero' . $i],
                                'total_factura'                  => $request['saldo_a' . $i],
                                'total'                          => $request['abono_a' . $i],
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
            $consulta_venta = null;
            $input_actualiza = null;

            /*************************************
             ****ACTUALIZO LA VENTA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE INGRESO***
                /*************************************/

            for ($i = 0; $i <= $request['contador_a']; $i++) {
                if (!is_null($request['abono_a' . $i])) {
                    $nuevo_saldo = 0;
                    //actualizar valor contable de cada tabla
                    $consulta_venta = Ct_Ventas::where('numero', $request['numero' . $i])->where('id_empresa',$id_empresa)->first();
                    if (!is_null($consulta_venta)) {
                        if ($request['abono_a' . $i] > ($consulta_venta->valor_contable)) {
                            $nuevo_saldo = $request['abono_a' . $i] - $consulta_venta->valor_contable;
                        } else {
                            $nuevo_saldo = $consulta_venta->valor_contable - $request['abono_a' . $i];
                        }
                        $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                        $input_actualiza = null;
                        if ($nuevo_saldof != 0) {
                            $input_actualiza = [
                                'estado_pago'                   => '2', //poner otro estado para que no salga en las consultas
                                'valor_contable'                => $nuevo_saldof,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                            ];
                        } else {
                            $input_actualiza = [
                                'estado_pago'                   => '3', //poner otro estado para que no salga en las consultas
                                'valor_contable'                => $nuevo_saldof,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                            ];
                        }
                        $consulta_venta->update($input_actualiza);
                    }
                }
            }
        }

        return $id_comprobante;
    }
    public function edit($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $comprobante_ingreso= Ct_Cheques_Post::where('id',$id)->where('id_empresa',$id_empresa)->first();
        $detalle_ingreso= Ct_Detalle_Cheque_Post::where('id_comprobante',$comprobante_ingreso->id)->get();
        $clientes= Ct_Clientes::where('estado','1')->get();
        $caja = Ct_Caja_Banco::where('estado', '1')->where('clase', '2')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $tipos = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        return view('contable/chequespost/edit',['id_empresa'=>$id_empresa,'tipos'=>$tipos,'caja'=>$caja,'detalle_cheques'=>$detalle_ingreso,'cheques'=>$comprobante_ingreso,'tipo_pago'=>$tipo_pago,'lista_banco'=>$lista_banco]);
    }


    public function update(Request $request, $id)
    {


        return redirect()->intended('/contable/acreedor');
    }
    public function banco(Request $request)
    {
        if (!is_null($request['validacion'])) {
            $banco = Ct_Caja_Banco::where('estado', '1')->where('id', $request['validacion'])->first();
            if (!is_null($banco)) {
                return $banco;
            } else {
                return 'error banco';
            }
        } else {
            return 'error vacio';
        }
    }
    public function pdf_comprobante(Request $request, $id)
    {
        //cheque post fechado en pdf necesito pedir la captura
        $comp_ingreso = Ct_Comprobante_Ingreso_Varios::where('estado', '1')->where('id', $id)->first();
        $empresa = Empresa::where('id', $comp_ingreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        $detalle_ingreso = Ct_Detalle_Comprobante_Ingreso::where('id_comprobante', $comp_ingreso->id)->first();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_ingreso->total_ingreso, 2, '.', ''), "DOLARES", "CTVS");
        //dd($factura_contable);
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_ingreso->id_asiento_cabecera)->first();
        $asiento_detalle = null;
        if ($asiento_cabecera != null) {
            $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        }

        if ($comp_ingreso != '[]') {

            $vistaurl = "contable.chequespost.pdf_chequepostfechado";
            $view     = \View::make($vistaurl, compact('comp_ingreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('Cheque Post Fechado' . $id . '.pdf');
        }

        return 'error';
    }
    public function anular($id, Request $request){
        if (!is_null($id)) {
            $comp_ingreso = Ct_Cheques_Post::where('id', $id)->where('estado', '1')->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {
                $input = [
                    'estado' => '0',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                // ahora actualizo el valor y le sumo lo que ya le había restado
                if(!is_null($comp_ingreso->detalles)){
                    foreach($comp_ingreso->detalles as $value){
                        $consulta_venta= Ct_Ventas::where('id',$value->id_factura)->where('estado','1')->first();
                        if(!is_null($consulta_venta)){
                            $valor= $consulta_venta->valor_contable;
                            $suma= ($value->total)+$valor;
                            $input_actualiza=[
                                'valor_contable'                => $suma,
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                        }
                    }
                }
                $asiento= Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   =>  $asiento->fecha_asiento,
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
                Log_Contable::create([
                    'tipo'           => 'Cheques Post Fechados',
                    'valor_ant'      => $asiento->valor,
                    'valor'          => $asiento->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                    'observacion'    => $asiento->concepto,
                    'id_ant'         => $id,
                    'id_referencia'  => $id_asiento,
                ]);
                return redirect()->route('cruce_clientes.index');
            }
        } else {
            return 'error';
        }    
    }
}