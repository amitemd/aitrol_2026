<?php
namespace Sis_medico\Http\Controllers\hc4;
/*use Sis_medico\Bodega;
use Sis_medico\Hc_Procedimientos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Agenda;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Hc_Receta;
use Sis_medico\Cortesia_Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Principio_Activo;
use Sis_medico\Examen;*/

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Protocolo;
use Sis_medico\Empresa;
use Sis_medico\Seguro;
use Sis_medico\Paciente;
use Sis_medico\User; 
use Carbon\Carbon;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Resultado;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Labs_doc_externos;
use Response;

class LaboratorioController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

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

    /*public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $paciente = Paciente::find($id_paciente);
       	return view('hc4/laboratorio/index');
    }*/

  
  //MUESTRA EL HISTORIA DE LAS ORDENES DE LABORATORIO
  //SOLO PARA DOCTORES
  public function search($id_paciente){

    $opcion = '2';
    if($this->rol_new($opcion)){
      return redirect('/');
    }

    
    $paciente = Paciente::find($id_paciente);


    $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
    //$seguros = Seguro::where('inactivo','1')->get();
    //$protocolos = Protocolo::where('estado','1')->get();
    //$empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();

    $ordenes = DB::table('examen_orden as eo')
           ->where('eo.id_paciente',$id_paciente)
           ->join('paciente as p','p.id','eo.id_paciente')
           ->join('seguros as s','s.id','eo.id_seguro')
           ->leftjoin('empresa as em','em.id','eo.id_empresa')
           ->leftjoin('nivel as n','n.id','eo.id_nivel')
           ->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')
           ->leftjoin('users as d','d.id','eo.id_doctor_ieced')
           ->join('users as cu','cu.id','eo.id_usuariocrea')
           ->join('users as mu','mu.id','eo.id_usuariomod')
           ->where('eo.estado','1')
           ->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo')
           ->OrderBy('eo.fecha_orden', 'desc')
           ->get();

      /*$id_usuarioprb = Auth::user()->id;
      if($id_usuarioprb == '9666666666'){
        return view('hc4/laboratorio/index_new', ['ordenes' => $ordenes, 'usuarios' => $usuarios,'paciente' => $paciente]);
      }

      return view('hc4/laboratorio/index', ['ordenes' => $ordenes, 'usuarios' => $usuarios,'paciente' => $paciente]);*/

      return view('hc4/laboratorio/index_new', ['ordenes' => $ordenes, 'usuarios' => $usuarios,'paciente' => $paciente]);

     /*return view('hc4/laboratorio/index', ['ordenes' => $ordenes, 'usuarios' => $usuarios,'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/

  }


    /*public function descargar($id){
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.fecha_nacimiento as pfecha_nacimiento','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();

        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seleccionados = [];
        foreach($detalles as $detalle){
            $seleccionados[]=$detalle->id_examen;
        }
        
        $seguro = Seguro::find($orden->id_seguro);
        $protocolos = Protocolo::where('estado','1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        $arreglo=[];
        $arreglo1=[];
        $nro=0;
        $agrupa_ant=0;

        foreach ($examenes as $examen) {
            if($agrupa_ant!=$examen->id_agrupador){
                $nro=0;
                $agrupa_ant=$examen->id_agrupador;
                $arreglo1=[];
            }
            $arreglo1[$nro]=$examen->id;
            $arreglo[$examen->id_agrupador]=$arreglo1;
            $nro++;   
        }

        if(!is_null($orden)){
            $tipo_usuario = Auth::user()->id_tipo_usuario;
            //$vistaurl="laboratorio.orden.orden";
            $vistaurl="hc4.laboratorio.orden";
            $view =  \View::make($vistaurl, compact('orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo','seleccionados', 'tipo_usuario'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150]);
            return $pdf->download('orden-de-laboratorio-'.$id.'.pdf'); 
        }
    }*/

    
    //FUNCION IMPRIMIR RESULTADOS
    //IMPRIME LOS RESULTADOS DE CADA ORDEN DE LABORATORIO
    //SOLO PARA DOCTORES
    public function imprimir_resultado($id){
        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }

        $orden = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user = User::find($paciente->id_usuario);
        
        $detalle = Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();


        //$detalle = $orden->detalles;
        $resultados =  $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
        //$agrupador = Examen_Agrupador::all();

        //Recalcula Porcentaje 
        $cant_par = 0;
        foreach($detalle as $d){
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if($d->id_examen=='639'){
                $xpar = $resultados->where('id_examen','639')->where('valor','<>','0');
                if($xpar->count()>0){
                    $cant_par = $cant_par + $xpar->count();
                }else{
                    $cant_par = $cant_par + 10;    
                }
                //$cant_par++;
            }else{
                if($d->examen->no_resultado=='0'){

                    if(count($d->parametros)=='0'){
                        $cant_par ++;  
                    } 
                    if($d->examen->sexo_n_s=='0'){
                      $parametro_nuevo = $d->parametros->where('sexo','3'); 
                      
                    }else{
                      $parametro_nuevo = $d->parametros->where('sexo',$orden->paciente->sexo);

                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par ++;    
                    }
                }     
            }
                          
            
        }

        $certificados = 0;
        $cantidad = 0;
        foreach($resultados as $r){
            $cantidad ++;
            if($r->certificado=='1'){
                $certificados ++;
                
            }
        }
        if($certificados>$cant_par){
            $certificados = $cant_par;
        }

        
        if($cant_par=='0'){
          $pct=0;
        }else{
          $pct = $certificados/$cant_par*100;
        }

       
        // Fin recalcula Porcentaje


        if($orden->seguro->tipo=='0'){
            $agrupador = Examen_Agrupador::all();  
            
        }else{
            $agrupador = Examen_Agrupador_labs::all();
            
        }
        
        $ucreador = $orden->crea;

        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        
        //$vistaurl="hc4.laboratorio.resultados_pdf";
        $vistaurl="laboratorio.orden.resultados_pdf";
        $view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador','user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-'.$id.'.pdf');
      }
    

    
    //MUESTRA BARRA DE PROGRESO
    public function puede_imprimir($id){

      $orden = Examen_Orden::find($id);
      $detalle = $orden->detalles;
      $resultados =  $orden->resultados;
      
      $cant_par = 0;
      foreach($detalle as $d){
        
        if($d->id_examen=='639'){
            $xpar = $resultados->where('id_examen','639')->where('valor','<>','0');
                if($xpar->count()>0){
                    $cant_par = $cant_par + $xpar->count();
                }else{
                    $cant_par = $cant_par + 10;    
                }
                //$cant_par++;
        } else {
            if ($d->examen->no_resultado == '0') {

                if (count($d->parametros->where('estado', '1')) == '0') {
                    $cant_par++;
                }
                if ($d->examen->sexo_n_s == '0') {
                    $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                } else {
                    $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                }
                foreach ($parametro_nuevo as $p) {
                    $cant_par++;
                }
            }
        }
                      
      }

      $certificados = 0;
      $cantidad = 0;
      
      foreach($resultados as $r){
        $cantidad ++;
        if($r->certificado=='1'){
          $certificados ++;
          }
      }

      if($certificados>$cant_par){
            $certificados = $cant_par;
      }
      
      return [ 'cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par ];

    }


      public function grafico(Request $request)
    {
      
        
        $estadistico_total=[];
        
        if($request->anio==null){
            $anio = Date('Y');
        }else{
            $anio = $request->anio;
        }

        $anio_desde = $anio - 6;
        

        $convenios = DB::table('convenio as c')->join('seguros as s','s.id','c.id_seguro')->join('empresa as e','e.id','c.id_empresa')->select('c.*','s.nombre','e.nombre_corto')->orderBy('c.id')->get();
        
        for($i=1;$i<=12;$i++){

            $ordenes_mes = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->select('anio','mes')->groupBy('anio','mes');
            if($ordenes_mes->count()>0){
                
                $estadistico_conv = [];
                foreach($convenios as $convenio){

                    $ordenes_mes_convenio = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->where('id_seguro',$convenio->id_seguro)->where('id_empresa',$convenio->id_empresa)->select('anio','mes','id_seguro','id_empresa')->groupBy('anio','mes','id_seguro','id_empresa');

                    

                        $estadistico_conv[$convenio->id] = ['ordenes' => $ordenes_mes_convenio->count(), 'cantidad' => $ordenes_mes_convenio->sum('cantidad'), 'valor' => $ordenes_mes_convenio->sum('valor')];
                        
                    
                    

                }

                $estadistico_total[$i] = ['mes' => $i, 'ordenes' => $ordenes_mes->count(), 'examenes' => $ordenes_mes->sum('cantidad'), 'valor' =>$ordenes_mes->sum('valor'), 'convenios' => $estadistico_conv];
    
            }
            $ordenes_part = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->select('anio','mes')->groupBy('anio','mes')->where('id_seguro','1');
            $estad_part[$i] = ['cantidad' => $ordenes_part->sum('cantidad'), 'valor' => $ordenes_part->sum('valor'), 'ordenes' => $ordenes_part->count()]; 
            
        }

        $or_anio = DB::table('examen_orden')
                    ->select('anio')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(total_valor - recargo_valor) as valor')
                    ->orderBy('anio')
                    ->groupBy('anio')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio','>=',$anio_desde)
                    ->get();

        $or_anio_tipo = DB::table('examen_orden')
                    ->join('seguros as s','s.id','id_seguro') 
                    ->select('anio','s.tipo')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(total_valor - recargo_valor) as valor')
                    ->orderBy('anio','s.tipo')
                    ->groupBy('anio','s.tipo')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio','>=',$anio_desde)
                    ->get();            

        //dd($or_anio_tipo);
        $arr_anio_tipo = null;
        foreach ($or_anio_tipo as $value) {
            $arr_anio_tipo[$value->anio.'-'.$value->tipo] = [$value->cantidad, $value->valor];
        }  
        //dd($arr_anio_tipo);          
                   

        $or_anio_mes = DB::table('examen_orden')
                    ->select('anio', 'mes')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(total_valor - recargo_valor) as valor')
                    ->orderBy('anio', 'mes')
                    ->groupBy('anio', 'mes')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio',$anio)
                    ->get(); 

        $or_anio_mes_tipo = DB::table('examen_orden')
                    ->join('seguros as s','s.id','id_seguro') 
                    ->select('anio', 'mes', 's.tipo')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(total_valor - recargo_valor) as valor')
                    ->orderBy('anio', 'mes' ,'s.tipo')
                    ->groupBy('anio', 'mes' ,'s.tipo')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio',$anio)
                    ->get(); 

        $arr_aniomes_tipo = null;
        foreach ($or_anio_mes_tipo as $value) {
            $arr_aniomes_tipo[$value->anio.'-'.$value->mes.'-'.$value->tipo] = [$value->cantidad, $value->valor];
        }                                
//dd($arr_anio_tipo);

        return view('hc4/laboratorio/grafico',['estadistico_total' => $estadistico_total, 'convenios' => $convenios, 'anio' => $anio, 'estad_part' => $estad_part, 'or_anio' => $or_anio, 'or_anio_mes' => $or_anio_mes, 'arr_anio_tipo' => $arr_anio_tipo, 'arr_aniomes_tipo' => $arr_aniomes_tipo]);    
       
        
    }

    public function cargar_anio_mes($anio, $mes){
       
       $or_aniomes_doctor = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
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
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
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
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->selectRaw('sum(eo.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','eo.codigo','de.apellido1','de.apellido2','de.nombre1','de.nombre2')
                    ->groupBy('eo.anio','eo.mes','eo.codigo','de.apellido1','de.apellido2','de.nombre1','de.nombre2')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','1')
                    ->whereNotNull('eo.codigo')
                    ->get(); 

        $or_aniomes_doctor_privado_hl = DB::table('examen_orden as eo')
                    ->join('users as d','d.id','eo.id_doctor_ieced')
                    ->join('seguros as s','s.id','eo.id_seguro')
                    ->join('examen_detalle as ed','ed.id_examen_orden','eo.id')
                    ->join('examen as e','e.id','ed.id_examen')
                    ->select('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->selectRaw('sum(ed.valor) as valor')
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
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
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
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
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->selectRaw('sum(eo.valor) as valor')
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
                    ->orderBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->groupBy('eo.anio','eo.mes','eo.id_doctor_ieced','d.apellido1','d.apellido2','d.nombre1','d.color')
                    ->where('eo.estado','1')
                    ->where('eo.realizado','1')
                    ->where('e.humanlabs','1')
                    ->where('eo.anio',$anio)
                    ->where('eo.mes',$mes)
                    ->where('s.tipo','2')
                    ->whereNull('eo.codigo')
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
            

                    //return "aqui";
      return view('hc4/laboratorio/anio_mes_doctor',['anio' => $anio, 'mes' => $mes , 'or_aniomes_doctor' => $or_aniomes_doctor, 'or_aniomes_doctor_publico' => $or_aniomes_doctor_publico, 'or_aniomes_doctor_privado_hl' => $or_aniomes_doctor_privado_hl, 'or_aniomes_doctor_particular_hl' => $or_aniomes_doctor_particular_hl, 'or_aniomes_doctor_privado' => $or_aniomes_doctor_privado, 'or_aniomes_doctor_particular' => $or_aniomes_doctor_particular,'or_aniomes_doctor_particular_not' =>$or_aniomes_doctor_particular_not,'or_aniomes_doctor_particular_hl2' => $or_aniomes_doctor_particular_hl2, 'or_aniomes_doctor_codigo' => $or_aniomes_doctor_codigo, 'or_aniomes_doctor_privado_not' =>$or_aniomes_doctor_privado_not, 'or_aniomes_doctor_particular_not1' =>$or_aniomes_doctor_particular_not1, 'or_aniomes_doctor_particular_hl3' =>$or_aniomes_doctor_particular_hl3, 'Labs_doc_externos' =>$Labs_doc_externos]);              

    }

    public function imprimir_resultado_x_orden($id){

      $orden = Examen_Orden::find($id);

      return view('hc4/laboratorio/resultado_x_orden',['orden' => $orden]);              


    }

}
