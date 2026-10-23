<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;

use Sis_medico\Examen_Resultado;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Forma_de_pago;
use Sis_medico\Convenio;
use Sis_medico\Examen_Sub_Resultado;
use Sis_medico\Examen_Detalle_Costo;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Labs_doc_externos;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;
use Carbon\Carbon;




class EstadisticoController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion){ //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          
          return true;
        
        }

    }

    public function anio_mes() //ESTADISTICO AÑO MES
    { 
        $opcion = '2'; //AÑO MES
        
        /*if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }*/
        
        //ordenes agrupadas por año
        $or_anio = DB::table('examen_orden')
                    ->select('anio')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(valor) as valor')
                    ->orderBy('anio')
                    ->groupBy('anio')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->get();

        //dd($or_anio);            

        //ordenes agrupadas por año/doctor
        $anio = Date('Y');
        //dd($año);            
        $or_anio_doctor = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->select('eo.anio','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->get();
        
        //dd($or_anio_doctor);  

        $or_am_tipo = DB::table('examen_orden as eo')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','s.tipo')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','s.tipo')
                    ->groupBy('eo.anio','s.tipo')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->get();
        
        //dd($or_am_tipo);  

        return view('laboratorio/estadistico/anio_mes',['or_anio' => $or_anio, 'anio' => $anio, 'or_anio_doctor' => $or_anio_doctor]);          
        
        
        
        
        
    }

    public function estad_doctor_mes($anio, $mes) //ESTADISTICO AÑO MES
    { 
        $opcion = '2'; //AÑO MES
        
        /*if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }*/
        
    
                 

        //ordenes agrupadas por añomes/doctor
             
        $or_aniomes_doctor = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->whereNull('eo.codigo')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->get();

        $or_aniomes_doctor_codigo = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->whereNotNull('eo.codigo')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->get();            

        $or_aniomes_doctor_publico = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','0')
                    ->get();

        /*$or_aniomes_doctor_privado = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','1')
                    ->get(); */

        $or_aniomes_doctor_privado = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','eo.mes','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','1')
                    ->whereNull('eo.codigo')
                    ->get();

         $or_aniomes_doctor_privado_not = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('labs_doc_externos as de','de.id','eo.codigo') 
                    ->select('eo.anio','eo.mes','eo.codigo','de.apellido1','de.apellido2','de.nombre1','de.nombre2')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','eo.mes','eo.codigo','de.apellido1','de.apellido2','de.nombre1','de.nombre2')
                    ->groupBy('eo.anio','eo.mes','eo.codigo','de.apellido1','de.apellido2','de.nombre1','de.nombre2')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','1')
                    ->whereNotNull('eo.codigo')
                    ->get(); 
                    //dd($or_aniomes_doctor_privado_not);

        $or_aniomes_doctor_privado_hl = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
                    ->join('examen as e','e.id','ed.id_examen')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('sum(ed.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('e.humanlabs','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','1')
                    ->get();              

        /*$or_aniomes_doctor_particular = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->get();*/

        $or_aniomes_doctor_particular = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNull('eo.codigo')
                    ->get();

        $or_aniomes_doctor_particular_not = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes', 'eo.codigo')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','eo.mes','eo.codigo')
                    ->groupBy('eo.anio','eo.mes','eo.codigo')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNotNull('eo.codigo')
                    ->get();

        $or_aniomes_doctor_particular_not1 = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    //->selectRaw('sum(eo.valor) as valor')
                    ->selectRaw('sum(eo.total_valor - eo.recargo_valor) as valor') 
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNotNull('eo.codigo')
                    ->get();            

        $or_aniomes_doctor_particular_hl = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
                    ->join('examen as e','e.id','ed.id_examen')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('sum(ed.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('e.humanlabs','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNull('eo.codigo') //CORRECCION
                    ->get();

         $or_aniomes_doctor_particular_hl3 = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
                    ->join('examen as e','e.id','ed.id_examen')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('sum(ed.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->groupBy('eo.anio','eo.mes','d.apellido1','d.apellido2','d.nombre1','eo.id_doctor_ieced','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('e.humanlabs','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNotNull('eo.codigo')
                    ->get();            

        $or_aniomes_doctor_particular_hl2 = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
                    ->join('examen as e','e.id','ed.id_examen')
                    ->select('eo.anio','eo.mes','eo.codigo')
                    ->selectRaw('sum(ed.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','eo.codigo')
                    ->groupBy('eo.anio','eo.mes','eo.codigo')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('e.humanlabs','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->get(); 

        $Labs_doc_externos = Labs_doc_externos::where('estado','1')->get();                                             

 

        return view('laboratorio/estadistico/anio_mes_doctor',['anio' => $anio, 'mes' => $mes , 'or_aniomes_doctor' => $or_aniomes_doctor, 'or_aniomes_doctor_publico' => $or_aniomes_doctor_publico, 'or_aniomes_doctor_privado' => $or_aniomes_doctor_privado, 'or_aniomes_doctor_particular' => $or_aniomes_doctor_particular, 'or_aniomes_doctor_particular_hl' => $or_aniomes_doctor_particular_hl, 'or_aniomes_doctor_privado_hl' => $or_aniomes_doctor_privado_hl,'or_aniomes_doctor_privado_not' =>$or_aniomes_doctor_privado_not, 'or_aniomes_doctor_particular_not' =>$or_aniomes_doctor_particular_not,'or_aniomes_doctor_particular_hl2' => $or_aniomes_doctor_particular_hl2, 'or_aniomes_doctor_codigo' => $or_aniomes_doctor_codigo, 'or_aniomes_doctor_particular_not1' => $or_aniomes_doctor_particular_not1, 'or_aniomes_doctor_particular_hl3' => $or_aniomes_doctor_particular_hl3, 'Labs_doc_externos' => $Labs_doc_externos ]);          
        
        
        
        
        
    }

    public function estadistico_covid($anio, $mes) //ESTADISTICO AÑO MES
    { 
        //$opcion = '2'; //AÑO MES
        
        /*if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }*/
        
    
                 

        //ordenes agrupadas por añomes/doctor
        $arr = [ ['inicio' => 1, 'fin' => 5], ['inicio' => 6, 'fin' => 12], ['inicio' => 13, 'fin' => 19], ['inicio' => 20, 'fin' => 26], ['inicio' => 27, 'fin' => 30] ];

        $seguros = Seguro::where('inactivo','1')->get();
        
        $arr_orden=[]; $x=0; ;
        foreach($seguros as $seguro){ 
            if($seguro->id>='30' && $seguro->id<='32'){   
                $i = 0;
                foreach ($arr as $key) {

                    $orden = DB::table('examen_orden as eo')
                        ->where('eo.estado','1')
                        ->where('eo.realizado','1')
                        ->where('eo.anio',$anio)
                        ->where('eo.mes',$mes)
                        ->where('eo.id_seguro',$seguro->id)
                        ->whereBetween('eo.fecha_orden',[$anio.'-'.$mes.'-'.$key['inicio'].' 00:00:00', $anio.'-'.$mes.'-'.$key['fin'].' 23:59:59'])
                        ->select('eo.id_seguro')
                        ->selectRaw('count(*) as cantidad')
                        ->selectRaw('sum(eo.valor) as valor')
                        ->first();

                    $orden2 = DB::table('examen_orden as eo')
                        ->where('eo.estado','1')
                        ->where('eo.realizado','1')
                        ->where('eo.anio',$anio)
                        ->where('eo.mes',$mes)
                        ->where('eo.id_seguro',$seguro->id)
                        ->where('eo.pres_dom','1')
                        ->whereBetween('eo.fecha_orden',[$anio.'-'.$mes.'-'.$key['inicio'].' 00:00:00', $anio.'-'.$mes.'-'.$key['fin'].' 23:59:59'])
                        ->select('eo.id_seguro')
                        ->selectRaw('count(*) as cantidad')
                        ->selectRaw('sum(eo.valor) as valor')
                        ->first();    
  
                    
                    $arr_cant[$i] =  ['cantidad' => $orden->cantidad, 'valor' => $orden->valor, 'cantidad_dom' => $orden2->cantidad, 'valor_dom' => $orden2->valor]; 


                   
                    $i++; 
                }
                $arr_orden[$seguro->id] =  ['nombre' => $seguro->nombre, 'arr' => $arr_cant];  
            }
        }
        $i = 0;
        /*foreach ($arr as $key) {
            $cantidad_otros=0;$valor_otros=0;
            foreach($seguros as $seguro){ 
                if($seguro->id<'30' || $seguro->id>'32'){ 
                    
                
                    $orden = DB::table('examen_orden as eo')
                        ->where('eo.estado','1')
                        ->where('eo.realizado','1')
                        ->where('eo.anio',$anio)
                        ->where('eo.mes',$mes)
                        ->where('eo.id_seguro',$seguro->id)
                        ->whereBetween('eo.fecha_orden',[$anio.'-'.$mes.'-'.$key['inicio'].' 00:00:00', $anio.'-'.$mes.'-'.$key['fin'].' 23:59:59'])
                        ->select('eo.id_seguro')
                        ->selectRaw('count(*) as cantidad')
                        ->selectRaw('sum(eo.valor) as valor')
                        ->first();   
        
                    
                    $cantidad_otros+=$orden->cantidad;
                    $valor_otros+=$orden->valor;    
                   
                       
                     
                }

            } 

            $arr_cant2[$i] =  ['cantidad' => $cantidad_otros, 'valor' => $valor_otros];
           
            
            $i++;  
        }  
        $arr_orden['999'] =  ['nombre' => 'Otros', 'arr' => $arr_cant2];    */


        //dd($arr_orden);                                         

 

        return view('laboratorio/estadistico/covid',['anio' => $anio, 'mes' => $mes , 'arr_orden' => $arr_orden, 'arr' => $arr ]);          
        
        
        
        
        
    }

    public function semanasMes($year,$month)
    {
        # Obtenemos el ultimo dia del mes
        $ultimoDiaMes=date("t",mktime(0,0,0,$month,1,$year));
     
        # Obtenemos la semana del primer dia del mes
        $primeraSemana=date("W",mktime(0,0,0,$month,1,$year));
     
        # Obtenemos la semana del ultimo dia del mes
        $ultimaSemana=date("W",mktime(0,0,0,$month,$ultimoDiaMes,$year));
     
        # Devolvemos en un array los dos valores
        return array($primeraSemana,$ultimaSemana);
    }

    /*
     
    $year=2013;
    for($i=1;$i<=12;$i++)
    {
        list($primeraSemana,$ultimaSemana)=semanasMes($year,$i);
     
        echo "<br>Mes: ".$i."/".$year." - Primera semana: ".$primeraSemana." - Ultima semana: ".$ultimaSemana;
    }*/

    public function produccion_vs_facturas_vs_pagos($anio){

        $or_anio_mes = DB::table('examen_orden')
            ->select('anio', 'mes')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(total_valor - recargo_valor) as valor')
            ->selectRaw('sum(total_valor) as valor_total')
            ->orderBy('anio', 'mes')
            ->groupBy('anio', 'mes')
            ->where('estado', '1')
            ->where('realizado', '1')
            ->where('anio', $anio)
            ->get();

        $or_anio_mes_fact = DB::table('examen_orden as eo')
            ->join('ct_ventas as cv', 'cv.nro_comprobante', 'eo.comprobante')
            ->select('anio', 'mes')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(eo.total_valor) as valor')
            ->orderBy('eo.anio', 'mes')
            ->groupBy('eo.anio', 'mes')
            ->where('eo.estado', '1')
            ->where('eo.realizado', '1')
            ->where('cv.id_empresa', '0993075000001')
            ->where('anio', $anio)
            ->get();    

        $or_anio_mes_fact2 = DB::table('examen_orden as eo')
            ->join('ct_ventas as cv', 'cv.nro_comprobante', 'eo.comprobante')
            ->select('eo.anio', 'eo.mes', 'cv.*')
            ->orderBy('eo.anio', 'mes')
            ->where('eo.estado', '1')
            ->where('eo.realizado', '1')
            ->where('cv.id_empresa', '0993075000001')
            ->where('anio', $anio)
            ->get(); 
            
        $arr_fact  = [];
        $arr_fact2 = [];

        foreach ($or_anio_mes_fact2 as $value) {
            $ingreso = DB::table('ct_detalle_comprobante_ingreso as ci')
            ->where('ci.id_factura', $value->id)->sum('total');
            $arr_fact[$value->anio . '-' . $value->mes][$value->id] = [$value->total_final, $ingreso];
        }
        foreach ($arr_fact as $key => $value) {
            $sum_pag  = 0;
            $sum_fact = 0;
            foreach ($value as $subvalue) {
                $sum_fact += $subvalue[0];
                $sum_pag += $subvalue[1];

            }
            $arr_fact2[$key] = [$sum_fact, $sum_pag];
        } 

        foreach ($or_anio_mes_fact as $value) {
            $arr_aniomes_fact[$value->anio . '-' . $value->mes] = [$value->cantidad, $value->valor];
        }

        return view('laboratorio/estadistico/index_prodvsfactvspag', ['arr_aniomes_fact' => $arr_aniomes_fact, 'arr_fact2' => $arr_fact2, 'anio' => $anio, 'or_anio_mes' => $or_anio_mes]);  

    }

    public function estadisticos_examenes($anio){

        $or_anio_mes = DB::table('examen_orden as eo')
            ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
            ->join('examen as e','e.id','ed.id_examen')
            ->select('e.nombre', 'eo.mes')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(ed.valor - ed.valor_descuento) as valor')
            ->orderBy('e.nombre', 'eo.mes')
            ->groupBy('e.nombre', 'eo.mes')
            ->where('eo.estado', '1')
            ->where('eo.realizado', '1')
            ->where('eo.anio', $anio);
            //->get();

        //dd($or_anio_mes->get());    

        $or_anio = DB::table('examen_orden as eo')
            ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
            ->join('examen as e','e.id','ed.id_examen')
            ->select('e.nombre')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(ed.valor - ed.valor_descuento) as valor')
            ->orderBy('e.nombre')
            ->groupBy('e.nombre')
            ->where('eo.estado', '1')
            ->where('eo.realizado', '1')
            ->where('eo.anio', $anio)
            ->get(); 

        return view('laboratorio/estadistico/estadisticos_examenes', ['or_anio_mes' => $or_anio_mes, 'or_anio' => $or_anio, 'anio' => $anio ]);       

    }

    

    

    
   

    


}