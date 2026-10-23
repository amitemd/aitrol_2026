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
use Sis_medico\ApProcedimientoNivel;
use Sis_medico\ApPlantilla;
use Sis_medico\ApPlantillaItem;
use Sis_medico\Convenio;
use Sis_medico\Ap_Tipo_Examen;
use Response;
use Sis_medico\LogAuditoriaPublica;
use Sis_medico\ApPermisoLiquidacion;

class ApPlantillaItemIessController extends Controller
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

    public function crear_item_iess($idcabecera){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nivel_convenio = Ap_Nivel_Convenio::all();
        $tipo_procedimiento = Ap_Tipo_Procedimiento::all();
        $archivo_plano_cab = Archivo_Plano_Cabecera::find($idcabecera);

        if($archivo_plano_cab->cierre == '1'){
            return view('archivo_plano/procedimientos/modal_liquidacion_cerrada');
        }
        
        return view('archivo_plano/planilla/item_iess',['nivel_convenio' =>$nivel_convenio, 'tipo_procedimiento' =>$tipo_procedimiento,'idcabecera' =>$idcabecera,'archivo_plano_cab' =>$archivo_plano_cab]);
    }

    public function store_item_iess(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id; 

       
       $variable = $request['contador_item'];

        for($i = 0; $i < $variable; $i++){

           $visibilidad = $request['visibilidad_item'. $i];

            if ($visibilidad == 1){

                $val       = $request['precio'.$i];
                $val_unit  = $val/(1+$request['porce_10'.$i]);
                
                $subtotal  = $request['cantidad'.$i] * $val_unit;
                //$valor_iva = $subtotal * $request['iva'.$i];
                //$valor10   = $subtotal * $request['porce_10'.$i];
                //$total     = $subtotal + $valor10 + $valor_iva;

                $val_unit  = round(($val_unit ),4);
                $iva_unitario = $val_unit * $request['iva'.$i];
                $p10_unitario = $val_unit * $request['porce_10'.$i];

                $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
                $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);

                $valor_iva    = $request['cantidad'.$i] * $iva_unitario;
                $valor10      = $request['cantidad'.$i] * $p10_unitario;
                $total        = $subtotal + $valor10 + $valor_iva;

                $fech  = substr($request['fecha'.$i], 6, 4).'-'.substr($request['fecha'.$i], 3, 2).'-'.substr($request['fecha'.$i], 0, 2);
                
                $input_item = [
                    'id_ap_cabecera'   => $request['idcabecera'.$i], 
                    'fecha'            => $fech,
                    'id_nivel'         => $request['niv_convenio'.$i],
                    'tipo'             => $request['tipo'.$i], 
                    'codigo'           => $request['codigo'.$i], 
                    'descripcion'      => $request['descripcion'.$i], 
                    'cantidad'         => $request['cantidad'.$i], 
                    'valor'            => round(($val),4),   
                    'subtotal'         => round(($subtotal),4),  
                    'porcent_10'       => $request['porce_10'.$i],                       
                    'porcentaje10'     => round(($valor10),4),
                    'valor_unitario'   => $val_unit,
                    'porcentaje_iva'   => $request['iva'.$i],
                    'iva'              => $valor_iva,
                    'porcentaje_honorario' => $request['porcent_hon'.$i],
                    'total'            => round(($total),4),
                    'id_usuariocrea'   => $id_usuario,
                    'id_usuariomod'    => $id_usuario,
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'iva_unitario'     => $iva_unitario,
                    'p10_unitario'     => $p10_unitario,
                ];
                /*if($id_usuario == '0922290697'){
                    dd($input_item);
                }*/
                Archivo_Plano_Detalle::insert($input_item);

                

            }


        }
        LogAuditoriaPublica::guardarLog_agrega_item($request['idcabecera0'],'FUNCION AGREGA POR ITEM');

        return "ok";

    }


    public function crea_upd_item_iess($id,$indice){

        $det_plano = Archivo_Plano_Detalle::find($id);

        $cabecera = $det_plano->cabecera;
        //$permiso = ApPermisoLiquidacion::valida_usuario_modal($cabecera->id);
        if($cabecera->cierre == '1'){
            return view('archivo_plano/procedimientos/modal_liquidacion_cerrada');
        }

        return view('archivo_plano/procedimientos/edit_items_iess',['det_plano' => $det_plano,'indice' => $indice]);


    }

    public function store_upd_item_iess(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $id_plan_det = $request['idplanodetalle'];

        $archivo_plano_det = Archivo_Plano_Detalle::find($id_plan_det);
        $cabecera = $archivo_plano_det->cabecera;

        $fech  = substr($request['fecha'], 6, 4).'-'.substr($request['fecha'], 3, 2).'-'.substr($request['fecha'], 0, 2);
        
        $val_clasif = 0;
        $k_valor = $request['precio'];
        $cantidad =$request['cantidad'];
        $valor10 = 0;
        $val_unit = 0;
        $valor_iva = 0;
        $total_sol = 0;
        $redo = '4';
        
        /*$valor_nivel = ApProcedimientoNivel::where('codigo',$request['codigo'])->where('cod_conv',$request['nivel'])->first();
        if(!is_null($valor_nivel)){
            $k_valor =round(($valor_nivel->uvr1*$valor_nivel->prc1),2);
        }*/
        
        /*if($request['orden_plantilla'] == '3'){
            $k_valor =$k_valor/2;
        }*/

        /*if($request['tipo'] == 'S'){
            $subtotal = $k_valor*$k_cantidad ;
            $porcent_clasif = Ap_Tipo_Examen::where('tipo',$request['tipo'])->where('estado', '1')->first();
            $val = $k_valor;
            $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
            $total_sol = $subtotal+$val_clasif;
            $total = $subtotal;
        }else if($request['orden_plantilla'] == '1'){

            if(!is_null($valor_nivel)){
                $valor_an = ($valor_nivel->uvr1a)*($valor_nivel->prc1a);
            }
            $val = $valor_an;
            $subtotal = $k_cantidad*$val;
            $total = $subtotal;

        }else{
            $val =$k_valor;
            $val_unit = $val/(1+$request['porcent_10']);
            $subtotal = $k_cantidad*$val_unit;
            $valor10 = $subtotal*$request['porcent_10'];
            $valor_iva =$subtotal*$request['iva'];
            $total = $subtotal+$valor10+$valor_iva;
            $total_sol = $total;
        }*/
        $iva_unitario = $archivo_plano_det->valor_unitario * $request['iva'];
        $p10_unitario = $archivo_plano_det->valor_unitario * $archivo_plano_det->porcent_10;
        if($idusuario == '0922290697'){
            //dd($archivo_plano_det->valor_unitario,$archivo_plano_det->porcentaje_iva,$archivo_plano_det->porcent_10,$iva_unitario,$p10_unitario);
        }
        if($cabecera->id_seguro == '5'){
            $iva_unitario = round($iva_unitario,4);
            $p10_unitario = round($p10_unitario,4);
        }else{
            $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
            $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);
        }

        //dd($iva_unitario,$p10_unitario);
        $val_unit = $k_valor / (1+$request['porcent_10']); //280
        $val_unit = round($val_unit,4);
        $subtotal = $cantidad * $archivo_plano_det->valor_unitario;
        $valor10  = $p10_unitario * $cantidad;
        //$valor_iva =$subtotal*$request['iva'];
        $nuevo_iva = $iva_unitario * $cantidad;
        $total = $subtotal+$valor10+$nuevo_iva;
        //MSP
        $porcent_clasif = null;$val_clasif = 0;
        if($cabecera->id_seguro == '5'){
            $redo = '2';
            //$val_unit = round($val_unit,2);
            //$iva_unitario = round($iva_unitario,2);
            //$p10_unitario = round($p10_unitario,2);
            $total = round($val_unit+ $iva_unitario + $p10_unitario,2);
            $total = $total * $cantidad;
            $porcent_clasif = Ap_Tipo_Examen::where('tipo',$request['tipo'])->where('estado', '1')->first();
            if(!is_null($porcent_clasif)){
                $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
            }
        }
        //$total_sol = $total;
        $total_sol = $total + $val_clasif;

        
        $input_item = [
            
            'fecha'                  => $fech,
            'tipo'                   => $request['tipo'],
            'codigo'                 => $request['codigo'],
            'descripcion'            => $request['descripcion1'],
            'cantidad'               => $request['cantidad'],
            'valor'                  => round(($k_valor),$redo),
            'subtotal'               => round(($subtotal),$redo),
            'porcent_10'             => $request['porcent_10'],
            'porcentaje10'           => round(($valor10),$redo),
            'porcentaje_iva'         => $request['iva'],
            'valor_unitario'         => $val_unit,
            'iva'                    => round(($nuevo_iva),$redo),
            'valor_porcent_clasifi'  => round(($val_clasif),$redo),
            'total'                  => round(($total),$redo),
            'total_solicitado_usd'   => round(($total_sol),$redo),
            'ip_modificacion'        => $ip_cliente,
            'id_usuariomod'          => $idusuario,
            'iva_unitario'           => $iva_unitario,
            'p10_unitario'           => $p10_unitario,
        
        ];
        LogAuditoriaPublica::guardarLog_edita_detalle($archivo_plano_det->id_ap_cabecera,'EDITA DETALLE',$request['cantidad'],$fech,$val_unit);
        $archivo_plano_det->update($input_item);

    }

    public function lista_item_iess($id_plan_cab){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $lista = ApPlantilla::where('estado','1')->orderBy('descripcion','ASC')->get();
        //$lista = ApPlantilla::orderBy('desc_comp','ASC')->get();
        
        $archivo_plano_cab = Archivo_Plano_Cabecera::find($id_plan_cab);
        if($archivo_plano_cab->cierre == '1'){
            return view('archivo_plano/procedimientos/modal_liquidacion_cerrada');
        }

        return view('archivo_plano/procedimientos/lista_items_iess',['lista' => $lista,'id_plan_cab' => $id_plan_cab,'archivo_plano_cab' => $archivo_plano_cab]);

    }

    public function buscar_plant_procedimiento(Request $request){

        $rolUsuario = Auth::user()->id_tipo_usuario; 
        if($rolUsuario == '1'){
            //dd($request-all());
        }

        $id_plan_cab = $request['plan_cabecera'];
        $cod_plantilla = $request['id_procedimiento'];
        $id_nivel = $request['nivel_convenio'];
        $j_seguro = $request['j_seguro'];
        $fecha = $request['fecha'];

        //return $cod_plantilla;

        $procedimientos = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad, b.valor, b.iva from ap_plantilla_items a, ap_procedimiento b
        where cod_plantilla='$cod_plantilla' and a.id_procedimiento=b.codigo order by b.descripcion ASC";  
        $procedimientos = DB::select($procedimientos);

        //dd($procedimientos);

        /*if($cod_plantilla == 39){

            $proc = ApPlantillaItem::where('cod_plantilla',$cod_plantilla)->where('ap_plantilla_items.estado','1')
            ->join('ap_procedimiento as ap_proc','ap_proc.id','ap_plantilla_items.procedimiento')
            ->select('ap_proc.descripcion','ap_proc.codigo','ap_plantilla_items.cantidad','ap_proc.iva','ap_proc.tipo','ap_proc.valor','ap_plantilla_items.orden','ap_proc.id')->get();

            
        }else{

            $proc = ApPlantillaItem::where('cod_plantilla',$cod_plantilla)->where('ap_plantilla_items.estado','1')
            ->join('ap_procedimiento as ap_proc','ap_proc.codigo','ap_plantilla_items.id_procedimiento')
            ->select('ap_proc.descripcion','ap_proc.codigo','ap_plantilla_items.cantidad','ap_proc.iva','ap_proc.tipo','ap_proc.valor','ap_plantilla_items.orden','ap_proc.id')->get();
        }*/

        $proc = ApPlantillaItem::where('cod_plantilla',$cod_plantilla)->where('ap_plantilla_items.estado','1')
            ->join('ap_procedimiento as ap_proc','ap_proc.id','ap_plantilla_items.procedimiento')
            ->select('ap_proc.descripcion','ap_proc.codigo','ap_plantilla_items.cantidad','ap_proc.iva','ap_proc.tipo','ap_proc.valor','ap_plantilla_items.orden','ap_proc.id','ap_proc.iva_nuevo')->get();
            //dd($proc);

        $fecha = date("Y-m-d", strtotime(str_replace('/', '-', $fecha))); 
           
        return view('archivo_plano/procedimientos/carga_list_proced',['procedimientos' => $procedimientos, 'proc' =>$proc,'id_nivel'=>$id_nivel,'j_seguro'=>$j_seguro, 'fecha'=>$fecha]);

    }

    public function buscar_descripcion(Request $request)
    {
        $descrip = $request['term'];
        $fecha   = $request['fecha'];

        $rolUsuario = Auth::user()->id_tipo_usuario; 
       

        $data =  array();

        $cadena = ApProcedimiento::where('descripcion', 'like', '%' . $descrip . '%')->get();

        foreach ($cadena as $value) {
            $iva = $value->iva;
            if($fecha == null){
                $iva = $value->iva;
            }

            $fecha = date("Y-m-d", strtotime(str_replace('/', '-', $fecha)));
        
            if($fecha >= '2024-04-01'){

                $iva = $value->iva_nuevo;    
            }
            
          
            
            
            $data[] = array('value' => $value->descripcion,'tipo' => $value->tipo,'codigo' => $value->codigo,'cantidad' => $value->cantidad, 'precio' => $value->valor,'iva' => $iva,'porcent10' => $value->porcentaje10,'id_ap_proced' => $value->id, 'valor_crm' => $value->valor_crm);
        }
       
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }


    public function buscar_codigo(Request $request)
    {

       $codigo = $request['term'];

       $nivel = $request['conv'];

       $data =  array();

        $cadena = ApProcedimiento::where('codigo', 'like', '%' . $codigo . '%')->get();

        foreach ($cadena as $value) {

            $data[] = array('value' => $value->codigo,'tipo' => $value->tipo,'descripcion' => $value->descripcion,'cantidad' => $value->cantidad, 'precio' => $value->valor,'iva' => $value->iva,'porcent10' => $value->porcentaje10,'id_ap_proced' => $value->id);
        }
       
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    public function buscar_codigo_2(Request $request)
    {
        $codigo = $request['term'];
        $nivel = $request['conv'];
        $data =  array();
        $cadena = ApProcedimiento::where('codigo', 'like', '%' . $codigo . '%')->orwhere('descripcion','like', '%' . $codigo . '%')->get();
        foreach ($cadena as $value) {
            $data[] = array('value' => $value->descripcion. ' ( ' .$value->codigo.' ) ','tipo' => $value->tipo,'descripcion' => $value->descripcion,'cantidad' => $value->cantidad, 'precio' => $value->valor,'iva' => $value->iva,'porcent10' => $value->porcentaje10,'id_ap_proced' => $value->id);
        }
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function recalculo_valor_items($idcabecera,$idseguro,$idempresa)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $plano_cab = Archivo_Plano_Cabecera::find($idcabecera);
        

        $nivel_empr_seg = Convenio::where('convenio.estado','1')
                                  ->where('convenio.id_empresa',$idempresa)
                                  ->where('convenio.id_seguro',$idseguro)
                                  ->select('convenio.id_nivel')
                                  ->first();

       
        
        $niv_convenio = $nivel_empr_seg->id_nivel;
        
        
        $cont_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera', $idcabecera)
        ->where('estado', '1')->get()->count();
        
        $plan_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera', $idcabecera)
                                              ->where('estado', '1')->get();
         
        if($plano_cab->id_empresa != $idempresa){

            //Recorremos los detalles de la Cabecera 
            foreach ($plan_detalle as $value){
                //////CORRECCION VH
                $valor10 = 0;
                $valor_iva = 0;
                $val_clasif = 0;
                
                $clasif = DB::table('ap_tipo_examen')->where('ap_tipo_examen.tipo',$value->tipo)->first();

                $val_clasif = 0;
                $k_valor =$value->valor;
                $k_cantidad =$value->cantidad;

                if(($value->id_orden_labs != null)||($value->hc_cardio != null)){
                    $val_temp = 1;
                }else{
                    $valor_nivel = ApProcedimientoNivel::where('codigo',$value->codigo)->where('cod_conv',$niv_convenio)->first();
                    if(!is_null($valor_nivel)){
                        $k_valor =round(($valor_nivel->uvr1*$valor_nivel->prc1),2);
                    }

                    if($value->orden_plantilla_item == '3'){
                        $k_valor =$k_valor/2;
                    }

                    if($value->tipo == 'S'){
                        $subtotal = $k_valor*$k_cantidad ;
                        $porcent_clasif = Ap_Tipo_Examen::where('tipo',$value->tipo)->where('estado', '1')->first();
                        $val = $k_valor;
                        $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
                        $total_sol = $subtotal+$val_clasif;
                        $total = $subtotal;
                        ///////CORRECCION VH
                        $val_unit = $val;
                        
                    }else if($value->orden_plantilla_item == '1'){

                        if(!is_null($valor_nivel)){
                            $valor_an = ($valor_nivel->uvr1a)*($valor_nivel->prc1a);
                        }
                        $val = $valor_an;
                        $subtotal = $k_cantidad*$val;
                        $total = $subtotal;

                    }else{
                        $val =$k_valor;
                        $val_unit = $val/(1+$value->porcent_10);
                        $subtotal = $value->cantidad*$val_unit;
                        $valor10 =$subtotal*$value->porcent_10;
                        $valor_iva =$subtotal*$value->porcentaje_iva;
                        $total = $subtotal+$valor10+$valor_iva;
                        $total_sol = $total;
                    }

                    //Comparacion de Porcentaje Clasificador
                    if($clasif->clasificado == 'SA07-50'){

                        $clasif_porcent = 50;

                    }else{
                        $clasif_porcent = 100;
                    }

                        //Nuevo Ingreso Detalle Cabecera
                        $arr = [
                            'id_ap_cabecera'          => $value->id_ap_cabecera,
                            'fecha'                   => $value->fecha,
                            'id_nivel'                => $niv_convenio,
                            'tipo'                    => $value->tipo,
                            'codigo'                  => $value->codigo,
                            'clasificador'            => $value->clasificador,
                            'descripcion'             => $value->descripcion,
                            'cantidad'                => $value->cantidad,
                            'valor'                   => round(($val),2),   
                            'subtotal'                => round(($subtotal),2), 
                            'porcent_10'              => $value->porcent_10,                       
                            'porcentaje10'            => round(($valor10),2),
                            'porcentaje_iva'          => $value->porcentaje_iva,
                            'valor_unitario'          => round(($val_unit),2),
                            'iva'                     => round(($valor_iva),2),
                            'clasif_porcentaje_msp'   => $clasif_porcent,
                            'valor_porcent_clasifi'   => round(($val_clasif),2),
                            'total'                   => round(($total),2),
                            'total_solicitado_usd'    => round(($total_sol),2),
                            'orden_plantilla_item'    => $value->orden_plantilla_item,
                            'id_usuariocrea'          => $idusuario,
                            'id_usuariomod'           => $idusuario,
                            'ip_creacion'             => $ip_cliente,
                            'ip_modificacion'         => $ip_cliente,
                        ];

                        Archivo_Plano_Detalle::where('id', $value->id)
                        ->update($arr);
                }
            }

            LogAuditoriaPublica::guardarLog_actualizar2($idcabecera, 'ACTUALIZA EMPRESA Y SEGURO', 'EMPRESA ANTERIOR: '.$plano_cab->id_empresa.' - NUEVA '.$idempresa);

            //Actualizamos la Empresa
            $input = [
                'id_empresa'=> $idempresa,
                'id_nivel'=> $niv_convenio,
                
            ];

            Archivo_Plano_Cabecera::where('id', $idcabecera)
            ->update($input);

            

        }

        

        //return "ok";

        $msj =  "ok";
        return ['msj' => $msj,'cont_detalle' => $cont_detalle];
        
    }

    /*public function crear_detalle_item(Request $request)
    {   
        $id_proc = $request['id_proc'];
        $niv_conv = $request['nivel_convenio'];
        $tipo = $request['tipo'];
        $cod_proc = $request['codigo'];
        $descrip = $request['descripcion'];
        $cant = $request['cantidad'];
        $val = $request['precio'];
        $iva = $request['iva'];
        $hon_anest = $request['hono_Anest'];

        if($tipo == 'P'){

            //Busqueda Id_procedimiento TA cuando es de Tipo P
            $ap_proc = ApProcedimiento::where('tipo','TA')->first();
           
            //Obtenemos el valor de tiempo de Anestesia
            $inf_proc_nivel = ApProcedimientoNivel::where('id_procedimiento',$ap_proc->id;)
                                                ->where('cod_conv',$niv_conv)->first();
            
            $val_tiemp_anest = round(($inf_proc_nivel->uvr1)*($inf_proc_nivel->prc1),2);

            //return ['precio' => $precio,'hono_anast' => $hono_anast,'separ' => $separ];


        }

    }*/

    //Actualizacion data

    /*function obtener_id_proced(){

        $recorre_proc_nivel = ApProcedimientoNivel::all();

        foreach ($recorre_proc_nivel as $value){

            $archivo_plano = ApProcedimiento::where('codigo',$value->codigo)->first();

            $input = [
            
               'id_procedimiento' => $archivo_plano->id,
                
            ];

            $archivo_plan_nivel = ApProcedimientoNivel::find($value->id);
    
            $archivo_plan_nivel->update($input);

        }

        return "ok";

    }*/

    //Actualiza Columna Procedimiento ap_plantilla_items
    function obtener_id_proced_codig(){

        $recorre_applantilla_item = ApPlantillaItem::all();

        foreach ($recorre_applantilla_item as $value){

            $archivo_procedimiento = ApProcedimiento::where('codigo',$value->id_procedimiento)->first();

            $input_act_prod = [
            
                'procedimiento' => $archivo_procedimiento->id,
                 
            ];

            //$act_plan_proced = ApPlantillaItem::find($value->id);

            //$act_plan_proced>update($input_act_prod);

            $value->update($input_act_prod);

        }

        return "ok";

    }

    private function redondea_menos_4_dec($numero){
        
        if( $this->contarDecimales($numero) > 4 ){
            $numero = floor($numero * 10000) / 10000;
        }

        return $numero;

    }

    private function contarDecimales($numero) {
        $partes = explode('.', strval($numero));
        if (count($partes) < 2) {
            return 0; // Si no hay punto decimal, no hay decimales
        } else {
            return strlen($partes[1]); // La longitud de la parte después del punto decimal es el número de decimales
        }
    }
    



}
