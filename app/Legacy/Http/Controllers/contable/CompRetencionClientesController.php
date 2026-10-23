<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Clientes;
use Sis_medico\User;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Porcentajes_Retencion_Iva;
use Sis_medico\Ct_Porcentajes_Retencion_Fuente;
use Sis_medico\Ct_Comprobante_Retencion_Clientes;
use Sis_medico\Ct_Comp_Ret_Clientes_Detalle_Retenciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Configuraciones;

class CompRetencionClientesController extends Controller
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

    public function index(){
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $ct_comp_retencion = DB::table('ct_comprobante_retencion_clientes as ct_comp_ret')
                                ->where('ct_comp_ret.estado', '1')
                                ->select('ct_comp_ret.*')
                                ->paginate(10);

        


      
        return view('contable/comp_retencion_clientes/index',['ct_comp_retencion'=>$ct_comp_retencion]);
   

    }

    public function crear(){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $divisas = Ct_Divisas::where('estado', '1')->get(); 
        $clientes = Ct_Clientes::where('estado', '1')->get();

        //Porcentaje de Retencion Iva, Porcentaje de Retencion Fuente
        $porce_rete_iva = Ct_Porcentajes_Retencion_Iva::where('estado', '1')->get(); 
        $porce_rete_fuente = Ct_Porcentajes_Retencion_Fuente::where('estado', '1')->get(); 

        return view('contable/comp_retencion_clientes/create',['clientes' => $clientes,'divisas' => $divisas,'porce_rete_iva' => $porce_rete_iva,'porce_rete_fuente' => $porce_rete_fuente]);
        
    }

    public function obtener_numero(){

        //Obtener el Total de Registros de la Tabla Ct_Comprobante_Retencion_Clientes
        $contador_comp_ret_client = DB::table('ct_comprobante_retencion_clientes')->get()->count();

  

        if($contador_comp_ret_client == 0){
       
            //return 'No Retorno nada';
            $num = '1';
            $num_comprobante_ret_client = str_pad($num, 8, "0", STR_PAD_LEFT);
            return  $num_comprobante_ret_client;


        }else{

            //Obtener Ultimo Registro de la Tabla ct_ingreso_clientes
            $max_id = DB::table('ct_comprobante_retencion_clientes')->max('id');

            if(($max_id>=1)&&($max_id<10)){
               $nu = $max_id+1;
               $num_comprobante_ret_client = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $num_comprobante_ret_client;
            }

            if(($max_id>=10)&&($max_id<99)){
               $nu = $max_id+1;
               $num_comprobante_ret_client = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $num_comprobante_ret_client;
            }

            if(($max_id>=100)&&($max_id<1000)){
               $nu = $max_id+1;
               $num_comprobante_ret_client = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $num_comprobante_ret_client;
            }

            if($max_id == 1000){
               $num_comprobante_ret_client = $max_id;
               return  $num_comprobante_ret_client;
            }
        
        } 

    }

    public function buscar_factura_numero(Request $request){

      $num_fact_venta = $request['num_factura'];



      $obt_datos_factura = DB::table('ct_ventas as ct_vent')
                              ->join('ct_asientos_cabecera as ct_ac','ct_ac.id_ct_ventas','ct_vent.id')
                              ->join('ct_clientes as ct_cli','ct_cli.identificacion','ct_vent.id_cliente')
                              ->where('ct_vent.numero',$num_fact_venta)
                              ->select('ct_vent.id',
                                       'ct_vent.numero',
                                       'ct_ac.id as numero_asiento',
                                       'ct_cli.identificacion as identificacion_cliente',
                                       'ct_vent.nro_comprobante as numero_comprobante',
                                       'ct_vent.fecha as fecha_factura',
                                       'ct_vent.tipo as tipo_comprobante',
                                       'ct_vent.procedimientos as detalle_procedimiento',
                                       'ct_vent.total_final as valor_cobrar',
                                       'ct_vent.subtotal_12 as sub_total12',
                                       'ct_vent.subtotal_0 as sub_total0',
                                       'ct_vent.impuesto as impuesto_cobrado')
                              ->where('ct_vent.estado','1')->first();

        if ($obt_datos_factura != '[]') {
            
            $data = [$obt_datos_factura->id, 
                     $obt_datos_factura->numero,
                     $obt_datos_factura->numero_asiento,
                     $obt_datos_factura->identificacion_cliente,
                     $obt_datos_factura->numero_comprobante,
                     $obt_datos_factura->fecha_factura,
                     $obt_datos_factura->tipo_comprobante,
                     $obt_datos_factura->detalle_procedimiento,
                     $obt_datos_factura->valor_cobrar,
                     $obt_datos_factura->sub_total12,
                     $obt_datos_factura->sub_total0,
                     $obt_datos_factura->impuesto_cobrado,
                   ];
            
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }

    }

    public function completar_numero(Request $request)
    {
        $numero = $request['term'];        
        $data      = array();
        $numeros_fact_ventas = DB::table('ct_ventas')->where('numero', 'like', '%' . $numero . '%')->where('estado','1')->get();
        //dd($productos);
        foreach ($numeros_fact_ventas as $num_fact_vent) {
            $data[] = array('value' => $num_fact_vent->numero);
        }
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    
    }

    public function buscar_identificacion(Request $request)
    {

        $id_cliente = $request['cliente'];
        $data_cliente = null;
        $clientes = DB::table('ct_clientes')->where('identificacion', $id_cliente)->first();
        if (!is_null($clientes)){
            
          $client_identificacion = $clientes->identificacion;
             
          return ['client_identificacion' => $client_identificacion];

        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function calculo_porcentaje_iva(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $id_identificador = $request['id_tip_ret_iva'];

        $valor_porcent = DB::table('ct_porcentajes_retencion_iva as ct_pr_iva')
                               ->where('ct_pr_iva.id',$id_identificador)
                               ->select('ct_pr_iva.valor as val_iva','ct_pr_iva.cuenta_clientes as cuent_client_iva')
                               ->where('ct_pr_iva.estado','1')->first();
        
        if($valor_porcent != '[]'){
            
            $data = [$valor_porcent->val_iva,
                     $valor_porcent->cuent_client_iva];
            return $data;
        }else {
            return ['value' => 'no resultados'];
        }               
        
    }

    public function calculo_porcentaje_retencion_fuente(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $id_identificador = $request['id_tip_ret_fuente'];

        
    
        $valor_porcent = DB::table('ct_porcentajes_retencion_fuente as ct_pr_f')
                               ->where('ct_pr_f.id',$id_identificador)
                               ->select('ct_pr_f.valor as val_fuente','ct_pr_f.cuenta_clientes as cuent_client_fuent')
                               ->where('ct_pr_f.estado','1')->first();
        
        if($valor_porcent != '[]'){
            
            $data = [$valor_porcent->val_fuente,
                     $valor_porcent->cuent_client_fuent];
            return $data;
        }else {
            return ['value' => 'no resultados'];
        }               
        
    
    }


    public function store(Request $request){

      if($this->rol()){
        return response()->view('errors.404');
      }

      $cuent_re_iva = $request['cuen_cl_iva'];
      $cuent_re_fuent = $request['cuen_cl_fuent'];


      $fecha_actual = Date('Y-m-d H:i:s');
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario  = Auth::user()->id;

      $variable = $request['contador'];

      $input = [

            'numero'                    => $request['num_com_retenc_cliente'],
            'fecha'                     => $request['fecha'],
            'tipo'                      => $request['tipo'],
            'id_caja'                   => $request['caja'],
            'concepto'                  => $request['concepto'],
            'id_cliente'                => $request['cliente'],
            'autorizacion'              => $request['autorizacion'],
            'ret_imp_renta'             => $request['ret_imp_renta'],
            'ret_iva'                   => $request['retencion_iva'],
            'total_ingresos'            => $request['total_ingresos'],
            'credito_aprobado'          => $request['credito_aprobado'],
            'total_deudas'              => $request['total_deudas'],
            'total_abonos'              => $request['total_abonos'],
            'nuevo_saldo'               => $request['nuevo_saldo'],
            'deficit'                   => $request['deficit'],
            'credito_favor'             => $request['credito_favor'],
            'observacion'               => $request['obs_doctor'],
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,

       ];

      $id_comp_ret_client = Ct_Comprobante_Retencion_Clientes::insertGetId($input);

      for ($i = 0; $i < $variable; $i++){

          Ct_Comp_Ret_Clientes_Detalle_Retenciones::create([
                    
                    'id_comp_ret_clientes'     => $id_comp_ret_client,
                    'numero_factura'           => $request['numero_fact' . $i],
                    'fecha'                    => $request['fecha_retencion' . $i],
                    'id_divisas'               => $request['id_divisa' . $i],
                    'base_fuente'              => $request['base_fuente' . $i],
                    'tipo_rfir'                => $request['id_tipo_rfir' . $i],
                    'total_rfir'               => $request['total_rfir' . $i],
                    'base_iva'                 => $request['base_iva' . $i],
                    'tipo_rfiva'               => $request['id_tipo_rfiva' . $i],
                    'total_rfiva'              => $request['total_rfiva' . $i],
                    'id_usuariocrea'           => $idusuario,
                    'id_usuariomod'            => $idusuario,
                    'ip_creacion'              => $ip_cliente,
                    'ip_modificacion'          => $ip_cliente,
          
          ]);

      }

      //REGISTRO DE ASIENTO DIARIO.
      //SE GUARDA EN  LA TABLA CABECERA DEL ASIENTO.
            
      $input_cabecera = [
              'id_comp_ret_client'     => $id_comp_ret_client,
              'observacion'            => $request['concepto'],
              'fecha_asiento'          => $fecha_actual,
              'num_comp_rete_client'   => $request['num_com_retenc_cliente'],
              'valor'                  => $request['total_deudas'],
              'id_usuariocrea'         => $idusuario,
              'id_usuariomod'          => $idusuario,
              'ip_creacion'            => $ip_cliente,
              'ip_modificacion'        => $ip_cliente,
      ];


      $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

      $total_deuda = $request['total_deudas'];
      $nuev_sal = $request['nuevo_saldo'];

      //CAJA GENERAL
      if ($nuev_sal>0){

        $valor_rfiva = $request['retencion_iva'];
          
          if ($valor_rfiva>0){

            $plan_cuentas= Plan_Cuentas::where('id',$cuent_re_iva)->first();

            Ct_Asientos_Detalle::create([
                                        
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $plan_cuentas->id,
                'descripcion'                   => $plan_cuentas->nombre,
                'fecha'                         => $fecha_actual,
                'debe'                          => $valor_rfiva,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
    
            ]); 
          
          }

        $valor_rf_renta = $request['ret_imp_renta'];
            
          if ($valor_rf_renta>0){
            
            $plan_cuentas= Plan_Cuentas::where('id',$cuent_re_fuent)->first();

              Ct_Asientos_Detalle::create([
                                          
                  'id_asiento_cabecera'           => $id_asiento_cabecera,
                  'id_plan_cuenta'                => $plan_cuentas->id,
                  'descripcion'                   => $plan_cuentas->nombre,
                  'fecha'                         => $fecha_actual,
                  'debe'                          => $valor_rf_renta,
                  'haber'                         => '0',
                  'id_usuariocrea'                => $idusuario,
                  'id_usuariomod'                 => $idusuario,
                  'ip_creacion'                   => $ip_cliente,
                  'ip_modificacion'               => $ip_cliente,
      
              ]); 

          }
         
          $cuenta_caja_general = Ct_Configuraciones::obtener_cuenta('COMPRETENCIONESCLIENTES_CAJA_GENERAL');
         // $plan_cuentas= Plan_Cuentas::where('id','1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                                        
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_caja_general->cuenta_guardar,
                'descripcion'                   => $cuenta_caja_general->nombre_mostrar,
                'fecha'                         => $fecha_actual,
                'debe'                          => $nuev_sal,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]); 
      
      }elseif($total_deuda>0){
        
            $cuenta_caja_general = Ct_Configuraciones::obtener_cuenta('COMPRETENCIONESCLIENTES_CAJA_GENERAL');
            //$plan_cuentas= Plan_Cuentas::where('id','1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                                        
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_caja_general->cuenta_guardar,
                'descripcion'                   => $cuenta_caja_general->nombre_mostrar,
                'fecha'                         => $fecha_actual,
                'debe'                          => $total_deuda,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]); 
      }

      //CUENTAS POR COBRAR
      if ($total_deuda>0){
       
          $cuenta_cobrar_cliente = Ct_Configuraciones::obtener_cuenta('COMPRETENCIONESCLIENTES_CUENTA_COBRAR_CLIENTES');        
          //$plan_cuentas= Plan_Cuentas::where('id','1.01.02.05.01')->first();
          Ct_Asientos_Detalle::create([
                                      
              'id_asiento_cabecera'           => $id_asiento_cabecera,
              'id_plan_cuenta'                => $cuenta_cobrar_cliente->cuenta_guardar,
              'descripcion'                   => $cuenta_cobrar_cliente->nombre_mostrar,
              'fecha'                         => $fecha_actual,
              'debe'                          => '0',
              'haber'                         => $total_deuda,
              'id_usuariocrea'                => $idusuario,
              'id_usuariomod'                 => $idusuario,
              'ip_creacion'                   => $ip_cliente,
              'ip_modificacion'               => $ip_cliente,
          
          ]); 

      }

    }


}
