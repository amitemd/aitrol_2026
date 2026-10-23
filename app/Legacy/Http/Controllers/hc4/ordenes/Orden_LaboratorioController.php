<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\laboratorio\OrdenController;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Carbon\Carbon;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Examen_Orden;
use Sis_medico\Log_usuario;
use Sis_medico\Firma_Usuario;
use Sis_medico\Protocolo;
use Sis_medico\Examen_Protocolo;
use Response;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen;

class Orden_LaboratorioController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index($id_paciente){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      $paciente = Paciente::find($id_paciente);

      return view('hc4/ordenes/orden_labs/index', ['paciente' => $paciente]);

    }

    public function index2($id_paciente){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      $paciente = Paciente::find($id_paciente);

      $ordenes = Examen_Orden::where('id_paciente',$id_paciente)
        ->where('estado','<>','0')   
        ->OrderBy('created_at', 'desc')
        ->get();
  

      return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => '0']);

    }

    public function crear($id_paciente){
        
     
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }

      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id; 
      $paciente = Paciente::find($id_paciente);
      $id_protocolo = null;
      $protocolo = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->first(); 
      if(!is_null($protocolo)){
        $examen_proto = Examen_Protocolo::where('id_protocolo',$protocolo->id)->get();
        $id_protocolo = $protocolo->id; 
      }

      $input_ex = [
            'id_paciente' => $id_paciente,
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'id_protocolo' => $id_protocolo,
            'id_seguro' => '1',
            
            'est_amb_hos' => '0',
            'id_doctor_ieced' => $id_doctor,
            
            'cantidad' => '0',
            'estado' => '-1',
            'valor' => '0',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $id_doctor,
            'id_usuariomod' => $id_doctor,
            'fecha_orden' => date('Y-m-d h:i:s'),
                        
        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);    


        $ordenes = Examen_Orden::where('id_paciente',$id_paciente)
        ->where('estado','<>','0')   
        ->OrderBy('created_at', 'desc')
        ->get(); 

      $etxt = '';  
      if(!is_null($protocolo)){
        $total=0;
        $cantidad=0;

        foreach ($examen_proto as $examen_p) {
          $examen = Examen::find($examen_p->id_examen);
          //return $examen;
          $valor    = $examen->valor;
          $cubre    = 'NO';

          $input_det = [
              'id_examen_orden' => $id_examen_orden,
              'id_examen'       => $examen->id,
              'valor'           => $valor,
              'cubre'           => $cubre,
              'ip_creacion'     => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea'  => $id_doctor,
              'id_usuariomod'   => $id_doctor,
          ];
        
          Examen_detalle::create($input_det);
          /// orden
          $total       = $total+$valor;
          $total       = round($total, 2);
          $cantidad++;
          $etxt = $etxt.'-'.$examen->id;
              
        }

        $input_or=[
          'cantidad'   => $cantidad,
          'valor'      => $total,
          'total_valor'      => $total,
        ];
        $orden = Examen_Orden::find($id_examen_orden);
    
        $orden->update($input_or);
       
      }

      Log_usuario::create([
        'id_usuario' => $id_doctor,
        'ip_usuario' => $ip_cliente,
        'descripcion' => "GENERA ORDEN DOCTOR",
        'dato_ant1' => 'orden: '.$id_examen_orden.'- paciente: '.$paciente->id,
        'dato1' => strtoupper($paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2),
        'dato_ant2' => $etxt,
        
      ]); 
       
         
      return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => $id_examen_orden]);
      
    }

      
    

    public function editar($id_orden){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }

      $id_doctor = Auth::user()->id; 
      
      $orden = Examen_Orden::find($id_orden);

      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e','e.id','sa.id_examen')->where('e.estado','1')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
      

      $detalles_ch = [];
      $i=0;
       
      $nuevo_detalles = $orden->detalles;

      $cantidad=count($nuevo_detalles);
      
      if($cantidad > 0) {
         $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$id_orden)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
      }
      
      if(!is_null($nuevo_detalles)){
        foreach ($nuevo_detalles as $nuevo_detalle) {
          $detalles_ch[$i] = $nuevo_detalle->id_examen; 
          $i = $i + 1;       
        }  
       
      }

      $protocolos = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->get(); 
      
       
      return view('hc4/ordenes/orden_labs/editar', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden, 'protocolos' => $protocolos]);

    }

    public function buscar(Request $request, $id_orden){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::find($id_orden);

      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      
      $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e','e.id','sa.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->where('e.estado','1')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden');

      if($request->seleccionados=='1' && $request->buscador ==null && $request->buscador2==null ){

        $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$id_orden)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden');
      }

      if($request->buscador!=null){
        $examenes_labs = $examenes_labs->where('e.descripcion','like','%'.$request->buscador.'%');
      }

      if($request->buscador2!=null){
        $examenes_labs = $examenes_labs->where('l.nombre','like','%'.$request->buscador2.'%');
      }

      if($request->firma_dr==1){
        $orden->update(['doctor_firma' => '1307189140']);
      }else{
        $orden->update(['doctor_firma' => $orden->id_doctor_ieced]);  
      }

      $examenes_labs = $examenes_labs->get();

      $detalles_ch = [];
      $i=0;
       
      $nuevo_detalles = $orden->detalles;
      if(!is_null($nuevo_detalles)){
        foreach ($nuevo_detalles as $nuevo_detalle) {
          $detalles_ch[$i] = $nuevo_detalle->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }

    public function cambia_perfil(Request $request, $id_orden){
        
      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id; 
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::find($id_orden);

      $detalles = $orden->detalles;

      if($request->id_protocolo!=null){
        foreach ($detalles as $value) {
          $value->delete();
        } 
        $protocolo = Protocolo::find($request->id_protocolo);
        if(!is_null($protocolo)){
          $examen_proto = Examen_Protocolo::where('id_protocolo',$protocolo->id)->get();
          $id_protocolo = $protocolo->id; 
          $total=0;
          $cantidad=0;
          $etxt = '';

          foreach ($examen_proto as $examen_p) {
            $examen = Examen::find($examen_p->id_examen);
            //return $examen;
            $valor    = $examen->valor;
            $cubre    = 'NO';

            $input_det = [
                'id_examen_orden' => $orden->id,
                'id_examen'       => $examen->id,
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_doctor,
                'id_usuariomod'   => $id_doctor,
            ];
          
            Examen_detalle::create($input_det);
            /// orden
            $total       = $total+$valor;
            $total       = round($total, 2);
            $cantidad++;
            $etxt = $etxt.'-'.$examen->id;
                
          }

          $input_or=[
            'id_protocolo' => $id_protocolo,
            'cantidad'   => $cantidad,
            'valor'      => $total,
            'total_valor' => $total
          ];
      
          $orden->update($input_or);

          Log_usuario::create([
            'id_usuario' => $id_doctor,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN DOCTOR - PERFIL",
            'dato_ant1' => 'orden: '.$orden->id.'- paciente: '.$orden->id,
            'dato1' => strtoupper($orden->paciente->nombre1." ".$orden->paciente->nombre2." ".$orden->paciente->apellido1." ".$orden->paciente->apellido2),
            'dato_ant2' => $etxt,
            
          ]); 
        }

      }

      $agrupador_labs = DB::table('examen_agrupador_labs')->get();

      

        $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$id_orden)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
  

      $detalles_ch = [];
      $i=0;

      $orden = Examen_Orden::find($id_orden);
       
      $nuevo_detalles = $orden->detalles;
      if(!is_null($nuevo_detalles)){
        foreach ($nuevo_detalles as $nuevo_detalle) {
          $detalles_ch[$i] = $nuevo_detalle->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }

    public function deseleccionar_perfil($id_orden){
        
      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id; 
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::find($id_orden);

      $detalles = $orden->detalles;

      foreach ($detalles as $value) {
        $value->delete();
      } 
   
      $agrupador_labs = DB::table('examen_agrupador_labs')->get();

      $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden',$id_orden)->join('examen as e','e.id','ed.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ed.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
  

      $detalles_ch = [];
      $i=0;

      $orden->update([
        'valor'           => 0,
        'cantidad'        => 0,
        'descuento_p'     => 0,
        'descuento_valor' => 0,
        'recargo_p'       => 0,
        'recargo_valor'   => 0,
        'total_valor'     => 0
      ]);
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }



    public function examenes_favoritos(){

      $id_doctor = Auth::user()->id; 
      $protocolos = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->get();

      return view('hc4/ordenes/examenes_favoritos/index', ['protocolos' => $protocolos, 'nombre' => null]);

    }

    public function examenes_favoritos_buscar(Request $request){

      $id_doctor = Auth::user()->id; 
      $protocolos = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->where('nombre','like', '%'.$request->nombre.'%')->get();

      return view('hc4/ordenes/examenes_favoritos/index', ['protocolos' => $protocolos, 'nombre' => $request->nombre]);

    }

    public function examenes_favoritos_editar($id){

      $protocolo = Protocolo::find($id);
      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      $examenes_labs = DB::table('examen_protocolo as ep')->where('id_protocolo',$id)->join('examen as e','e.id','ep.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ep.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
      
      $detalles_ch = [];
      $i=0;
       
      $pexamenes = $protocolo->examenes;
      if(!is_null($pexamenes)){
        foreach ($pexamenes as $pexamen) {
          $detalles_ch[$i] = $pexamen->id_examen; 
          $i = $i + 1;       
        }  
      }

      return view('hc4/ordenes/examenes_favoritos/editar',['protocolo' => $protocolo, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'agrupador_labs' => $agrupador_labs ]);        
    
    }

    public function examenes_favoritos_ver($id){
      $protocolo = Protocolo::find($id);
      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      $examenes_labs = DB::table('examen_protocolo as ep')->where('id_protocolo',$id)->join('examen as e','e.id','ep.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ep.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
      
      $detalles_ch = [];
      $i=0;
       

      return view('hc4/ordenes/examenes_favoritos/ver',['protocolo' => $protocolo, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'agrupador_labs' => $agrupador_labs ]);   
    }

    public function examenes_favoritos_crear(){  
      
      return view('hc4/ordenes/examenes_favoritos/crear');

    }

    
    public function examenes_favoritos_guardar(Request $request){

      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 
      
      if($request->nombre==null){
        return [ 'estado' => 'err', 'respuesta' => 'INGRESE EL NOMBRE'];
      }
      
      $protocolo = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->where('nombre',$request->nombre)->first();
      if(!is_null($protocolo)){
        return [ 'estado' => 'err', 'respuesta' => 'NOMBRE DEL PERFIL YA SE ENCUENTRA INGRESADO'];
      }

      $arr = [
        'nombre' => $request->nombre,
        'est_amb_hos' => '0',
        'estado' => '2',
        'pre_post' => null,
        'id_usuariocrea' => $id_doctor,
        'id_usuariomod' => $id_doctor,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente
      ];

      $id_protocolo = Protocolo::insertGetId($arr);

      return [ 'estado' => 'ok', 'respuesta' => $id_protocolo];

    }

    public function examenes_favoritos_listado($id){

      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      
      $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e','e.id','sa.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->where('e.estado','1')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->where('sa.estado','1')->get();    
      
      return view('hc4/ordenes/examenes_favoritos/listado',['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'id' => $id]);

    }

    public function examenes_favoritos_actualizar(Request $request){

      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 

      $protocolo = Protocolo::find($request->xid);

      if(is_null($protocolo)){
        return [ 'estado' => 'err', 'respuesta' => 'NO SE ENCONTRO PERFIL'];
      }
      
      if($request->nombre==null){
        return [ 'estado' => 'err', 'respuesta' => 'INGRESE EL NOMBRE'];
      }
      
      $arr = [
        'nombre' => $request->nombre,
        'id_usuariomod' => $id_doctor,
        'ip_modificacion' => $ip_cliente
      ];

      $protocolo->update($arr);

      return [ 'estado' => 'ok', 'respuesta' => $protocolo->id];

    }

    public function examenes_favoritos_buscador(Request $request, $id){
        
      $protocolo = Protocolo::find($id);

      $agrupador_labs = DB::table('examen_agrupador_labs')->get();
      
      $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e','e.id','sa.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->where('e.estado','1')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->where('sa.estado','1');

      if($request->seleccionados=='1' && $request->buscador ==null && $request->buscador2==null ){

        $examenes_labs = DB::table('examen_protocolo as ep')->where('id_protocolo',$id)->join('examen as e','e.id','ep.id_examen')->join('examen_agrupador_sabana as sa','sa.id_examen','ep.id_examen')->join('examen_agrupador_labs as l','l.id','sa.id_examen_agrupador_labs')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id','l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden');
      }

      if($request->buscador!=null){
        $examenes_labs = $examenes_labs->where('e.descripcion','like','%'.$request->buscador.'%');
      }

      if($request->buscador2!=null){
        $examenes_labs = $examenes_labs->where('l.nombre','like','%'.$request->buscador2.'%');
      }

      $examenes_labs = $examenes_labs->get();

      $detalles_ch = [];
      $i=0;
       
      $pexamenes = $protocolo->examenes;
      if(!is_null($pexamenes)){
        foreach ($pexamenes as $pexamen) {
          $detalles_ch[$i] = $pexamen->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/examenes_favoritos/xlistado',['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'id' => $id, 'detalles_ch' => $detalles_ch]);

    }

    public function examenes_favoritos_seleccionar($protocolo, $id){
      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 

      $examen_protocolo = Examen_Protocolo::where('id_protocolo',$protocolo)->where('id_examen',$id)->first();
      if(is_null($examen_protocolo)){
        $arr = [
          'id_protocolo' => $protocolo,
          'id_examen' => $id,
          'ip_creacion' => $ip_cliente,
          'ip_modificacion' => $ip_cliente,
          'id_usuariocrea' => $id_doctor,
          'id_usuariomod' => $id_doctor 
        ];

        Examen_Protocolo::create($arr);
      }  

      return "ok";  
      
    }

    public function examenes_favoritos_eliminar($protocolo, $id){
      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 

      $examen_protocolo = Examen_Protocolo::where('id_protocolo',$protocolo)->where('id_examen',$id)->first();
      $examen_protocolo->delete();

      return "ok";  
      
    }
    
   
}


