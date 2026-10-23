<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Clientes;
use Sis_medico\User;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Deposito_Bancario;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Configuraciones;

class Depo_Banca_Fact_VentasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index(){
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        

        $ct_deposito = DB::table('ct_deposito_bancario as ct_dep')
                            ->where('ct_dep.estado', '1')
                           ->select('ct_dep.*')
                           ->paginate(10);

       

        
        return view('contable/deposito_banca_fact_ventas/index',['ct_deposito' => $ct_deposito]);
   

    }

    public function crear(){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $cuentas = plan_cuentas::where('estado', '2')->get();

        return view('contable/deposito_banca_fact_ventas/create',['cuentas' => $cuentas]);

    }

    public function obtener_numero_deposito_bancario(){


        //Obtener el Total de Registros de la Tabla ct_ventas
        $contador_ctv = DB::table('ct_deposito_bancario')->get()->count();

  

        if($contador_ctv == 0){
       
            //return 'No Retorno nada';
            $num = '1';
            $numero_deposito = str_pad($num, 8, "0", STR_PAD_LEFT);
            return  $numero_deposito;


        }else{

            //Obtener Ultimo Registro de la Tabla ct_ventas
            $max_id = DB::table('ct_deposito_bancario')->max('id');

            if(($max_id>=1)&&($max_id<10)){
               $nu = $max_id+1;
               $numero_deposito = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $numero_deposito;
            }

            if(($max_id>=10)&&($max_id<99)){
               $nu = $max_id+1;
               $numero_deposito = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $numero_deposito;
            }

            if(($max_id>=100)&&($max_id<1000)){
               $nu = $max_id+1;
               $numero_deposito = str_pad($nu, 8, "0", STR_PAD_LEFT);
               return  $numero_deposito;
            }

            if($max_id == 1000){
               $numero_deposito = $max_id;
               return  $numero_deposito;
            }
        
        } 

    }

    public function store(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }

        $fecha_actual = Date('Y-m-d H:i:s');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $valor_efectivo = $request['total_efectivo'];
        $valor_pa_deposito = $request['totalpap_deposito'];
        $valor_cheque = $request['total_cheques'];
        $valor_tarjeta = $request['total_tarjetas'];
        $valor_deposito = $request['total_deposito'];

         $input = [
           
            'numero'                    => $request['ndeposito'],
            'tipo'                      => $request['tipo'],
            'fecha'                     => $request['fecha'],
            'concepto'                  => $request['concepto'],
            'caja_origen'               => $request['caja_origen'],
            'cuenta_destino'            => $request['cta_destino'],
            'nota'                      => $request['nota'],
            'total_efectivo'            => $request['total_efectivo'],
            'totalpa_deposito'          => $request['totalpap_deposito'],
            'total_cheques'             => $request['total_cheques'],
            'total_tarjetas'            => $request['total_tarjetas'],
            'total_deposito'            => $request['total_deposito'],
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,
        ];

        $id_deposito_bancario = Ct_Deposito_Bancario::insertGetId($input);

        //REGISTRO DE ASIENTO DIARIO.
        //SE GUARDA EN  LA TABLA CABECERA DEL ASIENTO.
            
        $input_cabecera = [
                'id_deposito_bancario'   => $id_deposito_bancario,
                'observacion'            => $request['concepto'],
                'fecha_asiento'          => $fecha_actual,
                'num_deposito_bancario'  => $request['ndeposito'],
                'valor'                  => $request['total_deposito'],
                'id_usuariocrea'         => $idusuario,
                'id_usuariomod'          => $idusuario,
                'ip_creacion'            => $ip_cliente,
                'ip_modificacion'        => $ip_cliente,
        ];

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            //CAJA DE ORIGEN
            $caja_origen = $request['caja_origen'];
            
            $plan_cuentas = Plan_Cuentas::where('id',$caja_origen)->first();
            Ct_Asientos_Detalle::create([
                                        
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $caja_origen,
                'descripcion'                   => $plan_cuentas->nombre,
                'fecha'                         => $fecha_actual,
                'debe'                          => '0',
                'haber'                         => $valor_deposito,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]);

            //CUENTA DE DESTINO
            $cuenta_destino = $request['cta_destino'];
           
            $plan_cuentas = Plan_Cuentas::where('id',$cuenta_destino)->first();
            Ct_Asientos_Detalle::create([
                                        
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_destino,
                'descripcion'                   => $plan_cuentas->nombre,
                'fecha'                         => $fecha_actual,
                'debe'                          => $valor_deposito,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]);

    }

    public function completar_numero(Request $request)
    {
        $numero = $request['term'];        
        $data      = array();
        $numeros_fact_ventas = DB::table('ct_ventas')->where('numero', 'like', '%' . $numero . '%')->where('estado','1')->get();

        foreach ($numeros_fact_ventas as $num_fact_vent) {
            $data[] = array('value' => $num_fact_vent->numero);
        }
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    
    }


    public function buscar_factura_numero(Request $request){

      $num_fact_venta = $request['num_factura'];



        $obt_datos_factura = DB::table('ct_ventas as ct_vent')
                              ->join('ct_asientos_cabecera as ct_ac','ct_ac.id_ct_ventas','ct_vent.id')
                              ->where('ct_vent.numero',$num_fact_venta)
                              ->select('ct_vent.id','ct_vent.numero','ct_ac.id as numero_asiento','ct_vent.nro_comprobante as numero_comprobante','ct_vent.fecha as fecha_factura')
                              ->where('ct_vent.estado','1')->first();




                              //->join('ct_comprobante_ingreso as ct_comp_ing','ct_comp_ing.id_fact_vent','ct_vent.id')
                              //->join('ct_comp_ing_det_valores_recibidos as ct_com_det','ct_com_det.id_comp_ing','ct_comp_ing.id')

        if ($obt_datos_factura != '[]') {


            $val_recib_depositar = DB::table('ct_comprobante_ingreso as ct_comp_ing')
                                      ->join('ct_comp_ing_det_valores_recibidos as ct_com_det','ct_com_det.id_comp_ing','ct_comp_ing.id')
                                      ->where('ct_comp_ing.id_fact_vent',$obt_datos_factura->id)
                                      ->where('ct_com_det.estado','1')
                                      ->select('ct_com_det.id_tipo_pago as tipo_pago','ct_com_det.fecha as fecha_recib',
                                        'ct_com_det.id_banco as banco_recib','ct_com_det.id_cuenta as cuenta_recib','ct_com_det.identificacion_cliente as girad_recib','ct_com_det.valor as valor_recib')
                                      ->get();

            //return $val_recib_depositar;


            
            $data = [$obt_datos_factura->id, 
                     $obt_datos_factura->numero,
                     $obt_datos_factura->numero_asiento,
                     $obt_datos_factura->numero_comprobante,
                     $obt_datos_factura->fecha_factura,
                     $val_recib_depositar,

                     //$obt_datos_factura->tipo_comprobante,
                     //$obt_datos_factura->detalle_procedimiento,
                     //$obt_datos_factura->valor_cobrar,
                   ];
            
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }

    }

    public function editar(){


    }
    
   
}