<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\ApProcedimiento;
use Sis_medico\Ap_Nivel_Convenio;
use Sis_medico\Ap_Tipo_Procedimiento;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Tipo_Examen;
use Response;

class ApPlantillaItemMspController extends Controller
{
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4, 5, 11, 22)) == false){
          return true;
        }
    }

    public function crear_item_msp($idcabecera){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nivel_convenio = Ap_Nivel_Convenio::all();
        $tipo_procedimiento = Ap_Tipo_Procedimiento::all();
        $archivo_plano_cab = Archivo_Plano_Cabecera::find($idcabecera);

        return view('archivo_plano/planilla/item_msp',['nivel_convenio' =>$nivel_convenio, 'tipo_procedimiento' =>$tipo_procedimiento,'idcabecera' =>$idcabecera,'archivo_plano_cab' =>$archivo_plano_cab]);
    }

    public function buscar_descripcion_msp(Request $request)
    {
        $descrip = $request['term'];

        $data =  array();

        $cadena = ApProcedimiento::where('descripcion', 'like', '%' . $descrip . '%')->get();

        foreach ($cadena as $value) {
            $data[] = array('value' => $value->descripcion,'tipo' => $value->tipo,'codigo' => $value->codigo,'cantidad' => $value->cantidad, 'precio' => $value->valor,'iva' => $value->iva,'porcent10' => $value->porcentaje10,'id_ap_proced' => $value->id,'clasificado' => $value->clasificado,'porcent_clasificado' => $value->porcentaje_clasificado);
        }
       
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    public function buscar_codigo_msp(Request $request)
    {

       $codigo = $request['term'];

       $nivel = $request['conv'];

       $data =  array();

        $cadena = ApProcedimiento::where('codigo', 'like', '%' . $codigo . '%')->get();

        foreach ($cadena as $value) {

            $data[] = array('value' => $value->codigo,'tipo' => $value->tipo,'descripcion' => $value->descripcion,'cantidad' => $value->cantidad, 'precio' => $value->valor,'iva' => $value->iva,'porcent10' => $value->porcentaje10,'id_ap_proced' => $value->id,'clasificado' => $value->clasificado,'porcent_clasificado' => $value->porcentaje_clasificado);
        }
       
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }
    
    /*public function store_item_msp(Request $request)
    {
       
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $variable = $request['contador_item_msp'];

        for($i = 0; $i < $variable; $i++){

            $visibilidad = $request['visibilidad_item_msp'. $i];
            
            if ($visibilidad == 1){
                
                $clas_porcent = '';
                $val_clasif = 0;
                $total_sol = 0;
                $val_unit = 0;
                $valor10 = 0;
                $valor_iva = 0;

                $clasif= $request['clasificador'.$i];
                     
                if($request['tipo'.$i] == 'S'){
                    $subtotal = $request['cantidad'.$i]*$request['precio'.$i];
                    $porcent_clasif = Ap_Tipo_Examen::where('tipo',$request['tipo'.$i])->where('estado', '1')->first();
                    $val = $request['precio'.$i];
                    $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
                    $total_sol = $subtotal+$val_clasif;
                    $total = $subtotal;
                }else{
                    $tipo = $request['tipo'.$i];
                    $tipo_ex = "NPI";
                    $tex = Ap_Tipo_Examen::where('tipo',$tipo)->first();
                    if(!is_null($tex)){
                        $tipo_ex = $tex->tipo_ex;
                    }
                    
                    if($tipo_ex == 'IMM'){
                        $val =$request['precio'.$i];
                        $val_unit= $val/(1+$request['porce_10'.$i]);
                        $subtotal = $request['cantidad'.$i]*$val_unit;
                        $valor_iva =$subtotal*$request['iva'.$i];
                        $valor10 =$subtotal*$request['porce_10'.$i];
                        $total= $subtotal+$valor10+$valor_iva;;
                        $total_sol = $total;

                    }else{

                        $subtotal = $request['cantidad'.$i]*$request['precio'.$i];
                        $val = $request['precio'.$i];
                        $total = $subtotal;
                        $total_sol = $subtotal + ($subtotal*$request['iva'.$i]);

                    }
                    
                }

                if($request['clasificador'.$i] == 'SA07-50'){

                    $clasif_porcent = 50;

                }else{
                    $clasif_porcent = 100;
                }

                $fech  = substr($request['fecha'.$i], 6, 4).'-'.substr($request['fecha'.$i], 3, 2).'-'.substr($request['fecha'.$i], 0, 2);
                
                $input_item = [
                    'id_ap_cabecera'         => $request['idcabecera'.$i],
                    'fecha'                  => $fech,
                    'id_nivel'               => $request['niv_convenio'.$i],
                    'tipo'                   => $request['tipo'.$i],
                    'codigo'                 => $request['codigo'.$i],
                    'clasificador'           => $request['clasificador'.$i],
                    'descripcion'            => $request['descripcion'.$i],
                    'cantidad'               => $request['cantidad'.$i],
                    'valor'                  => round(($val),2),   
                    'subtotal'               => round(($subtotal),2), 
                    'porcent_10'             => $request['porce_10'.$i],                       
                    'porcentaje10'           => round(($valor10),2),
                    'porcentaje_iva'         => $request['iva'.$i],
                    'valor_unitario'         => round(($val_unit ),2),
                    'iva'                    => round(($valor_iva),2),
                    'clasif_porcentaje_msp'  => $clasif_porcent,
                    'valor_porcent_clasifi'  => round(($val_clasif),2),
                    'total'                  => round(($total),2),
                    'total_solicitado_usd'   => round(($total_sol),2),
                    'id_usuariocrea'         => $id_usuario,
                    'id_usuariomod'          => $id_usuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente
                ];

                Archivo_Plano_Detalle::insert($input_item);

            }

        }

        return "ok";


    }*/

    public function store_item_msp(Request $request)
    {
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $variable = $request['contador_item_msp'];

        for($i = 0; $i < $variable; $i++){

            $visibilidad = $request['visibilidad_item_msp'. $i];
            
            if ($visibilidad == 1){
                
                $clas_porcent = '';
                $val_clasif = 0;
                $total_sol = 0;
                $val_unit = 0;
                $valor10 = 0;
                $valor_iva = 0;

                $clasif= $request['clasificador'.$i];

                if($request['clasificador'.$i] == 'SA07-50'){

                    $clasif_porcent = 50;

                }else{
                    $clasif_porcent = 100;
                }


                $val =$request['precio'.$i];
                $val_unit= $val/(1+$request['porce_10'.$i]);
                $subtotal = $request['cantidad'.$i]*$val_unit;
                $valor10 =$subtotal*$request['porce_10'.$i];
                $valor_iva =$subtotal*$request['iva'.$i];

                $porcent_clasif = Ap_Tipo_Examen::where('tipo',$request['tipo'.$i])->where('estado', '1')->first();
                if(!is_null($porcent_clasif)){
                    $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
                }
                
                //$total= $subtotal+$valor10+$valor_iva;

                $valor_unitario = round(($val_unit ),4);
                $iva_unitario = $valor_unitario * $request['iva'.$i];
                $iva_unitario = round($iva_unitario,4);
                $p10_unitario = $valor_unitario * $request['porce_10'.$i];
                $p10_unitario = round($p10_unitario,4);
                $total = round($valor_unitario+ $iva_unitario + $p10_unitario,2);
                $total = $total * $request['cantidad'.$i];
                $total_sol = $total + $val_clasif;

                $fech  = substr($request['fecha'.$i], 6, 4).'-'.substr($request['fecha'.$i], 3, 2).'-'.substr($request['fecha'.$i], 0, 2);
                
                $input_item = [
                    'id_ap_cabecera'         => $request['idcabecera'.$i],
                    'fecha'                  => $fech,
                    'id_nivel'               => $request['niv_convenio'.$i],
                    'tipo'                   => $request['tipo'.$i],
                    'codigo'                 => $request['codigo'.$i],
                    'clasificador'           => $request['clasificador'.$i],
                    'descripcion'            => $request['descripcion'.$i],
                    'cantidad'               => $request['cantidad'.$i],
                    'valor'                  => round(($val),2),   
                    'subtotal'               => round(($subtotal),2), 
                    'porcent_10'             => $request['porce_10'.$i],                       
                    'porcentaje10'           => round(($valor10),2),
                    'porcentaje_iva'         => $request['iva'.$i],
                    'valor_unitario'         => $valor_unitario,
                    'iva'                    => round(($valor_iva),2),
                    'clasif_porcentaje_msp'  => $clasif_porcent,
                    'valor_porcent_clasifi'  => round(($val_clasif),2),
                    'total'                  => round(($total),2),
                    'total_solicitado_usd'   => round(($total_sol),2),
                    'id_usuariocrea'         => $id_usuario,
                    'id_usuariomod'          => $id_usuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                    'iva_unitario'           => $iva_unitario,
                    'p10_unitario'           => $p10_unitario,
                ];

                Archivo_Plano_Detalle::insert($input_item);

            }

        }

        return "ok";


    }


    public function lista_item_msp($id_plan_cab){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$lista = ApPlantilla::orderBy('descripcion','ASC')->get();
        $lista = ApPlantilla::orderBy('desc_comp','ASC')->where("estado",1)->get();
        
        $archivo_plano_cab = Archivo_Plano_Cabecera::find($id_plan_cab);

        return view('archivo_plano/procedimientos/lista_items_msp',['lista' => $lista,'id_plan_cab' => $id_plan_cab,'archivo_plano_cab' => $archivo_plano_cab]);

    }
    
    
    /*public function store_item_msp(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id; 

       
       $variable = $request['contador_item_msp'];

       for($i = 0; $i < $variable; $i++){

         $visibilidad = $request['visibilidad_item_msp'. $i];

         if ($visibilidad == 1){

            ApProcedimiento::create([
                'tipo'                  => $request['tipo'.$i],
                'codigo'                => $request['codigo'.$i],
                'descripcion'           => $request['descripcion'.$i],
                'cantidad'              => $request['cantidad'.$i],
                'valor'                 => $request['precio'.$i],
                //'clasificador'        => $request['clasificador'.$i],
                //'porcentaje10'        => $request['tipo_mov'.$i],
                'iva'                   => $request['iva'.$i],
                //'estado'                => 1,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente
                
            ]);

         }


       }


    }*/

}
