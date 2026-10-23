<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Carbon\Carbon;
use Cookie;
use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Mail;
use Response;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Agrupador_Sabana;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Resultado;
use Sis_medico\Log_usuario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro;
use Sis_medico\Examen_Agrupador_010;
use Sis_medico\Examen_Detalle;

class NuevoResultadoController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function crear_resultados($id_orden){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $examen_orden = Examen_Orden::find($id_orden);

        $fecha_orden  = $examen_orden->fecha_orden;
        
        if(!is_null($examen_orden)){
            $paciente = $examen_orden->paciente;
            $seguro = Seguro::find($examen_orden->id_seguro);
            //Tipo seguro = 0:publico, 1:privado, 2:npi 
            $detalles = $examen_orden->detalles;
            foreach ($detalles as $detalle) {
                $examen = $detalle->examen;
                $id_agrupador_publico = $examen->id_agrupador;

                $examen_agrupador_010 = Examen_Agrupador_010::where('id_examen', $examen->id)->where('estado',1)->first();
                if(!is_null($examen_agrupador_010)){
                    $id_agrupador_publico = $examen_agrupador_010->id_agrupador;
                }
                
                $eas = Examen_Agrupador_Sabana::where('id_examen',$examen->id)->first();
                $id_agrupador_privado = null;
                if(!is_null($eas)){
                    $id_agrupador_privado = $eas->id_examen_agrupador_labs;
                }
                if($examen->sexo_n_s==0){
                    $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',3)->where('estado','1')->get();
                } else{
                    $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',$paciente->sexo)->where('estado','1')->get();
                }
                foreach ($parametros as $parametro) {
                    $examen_resultado = Examen_Resultado::where('id_orden',$id_orden)->where('id_parametro', $parametro->id)->first();
                    //$examen_resultado =  $examen_orden->resultados->where('id_parametro', $parametro->id)->first();
                    
                    if(is_null($examen_resultado)){
                        if($fecha_orden > $parametro->created_at){ 
                            if($seguro->tipo==0){//publico
                                if($examen->id != '639'){
                                    $input_resultado = [
                                        'id_orden'        => $examen_orden->id,
                                        'id_parametro'    => $parametro->id,
                                        'valor'           => null,
                                        'ip_creacion'     => 'CARGA_EXA',
                                        'ip_modificacion' => $ip_cliente,
                                        'id_usuariocrea'  => $idusuario,
                                        'id_usuariomod'   => $idusuario,
                                        'id_examen'       => $examen->id,
                                        'id_agrupador_publico'    => $id_agrupador_publico,
                                    ];
                                    Examen_Resultado::create($input_resultado);
                                }

                            } else {
                                if($examen->id != '639'){
                                    $input_resultado = [
                                        'id_orden'        => $examen_orden->id,
                                        'id_parametro'    => $parametro->id,
                                        'valor'           => null,
                                        'ip_creacion'     => 'CARGA_EXA',
                                        'ip_modificacion' => $ip_cliente,
                                        'id_usuariocrea'  => $idusuario,
                                        'id_usuariomod'   => $idusuario,
                                        'id_examen'       => $examen->id,
                                        'id_agrupador_privado'    => $id_agrupador_privado,
                                    ];
                                    Examen_Resultado::create($input_resultado);
                                }

                            }
                        }    
                    } else{
                        if($seguro->tipo==0){//publico
                            
                            $input_resultado = [
                                
                                'id_examen'             => $examen->id,
                                'id_agrupador_publico'  => $id_agrupador_publico,
                                'fecha_anterior'        => $examen_resultado->updated_at,
                            ];
                            $examen_resultado->update($input_resultado);
                        } else {
                            
                            $input_resultado = [
                                
                                'id_examen'             => $examen->id,
                                'id_agrupador_privado'  => $id_agrupador_privado,
                                'fecha_anterior'        => $examen_resultado->updated_at,
                            ];
                            $examen_resultado->update($input_resultado);

                        }
                        //dd($examen_resultado);
                        
                    }
                }
            }
        }
    }
 
    public function cargar_ordenes($mes,$anio){
        $ordenes = Examen_Orden::where('mes',$mes)->where('anio',$anio)->where('estado',1)->get();
        foreach ($ordenes as $orden) {
            $this->crear_resultados_masivo($orden->id);
        }
    }

    public static function crear_resultados_masivo($id_orden){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $examen_orden = Examen_Orden::find($id_orden);

        $fecha_orden  = $examen_orden->fecha_orden;
        
        if(!is_null($examen_orden)){
            $paciente = $examen_orden->paciente;
            $seguro = Seguro::find($examen_orden->id_seguro);
            //Tipo seguro = 0:publico, 1:privado, 2:npi 
            $detalles = $examen_orden->detalles;
            foreach ($detalles as $detalle) {
                $examen = $detalle->examen;
                $id_agrupador_publico = $examen->id_agrupador;

                $examen_agrupador_010 = Examen_Agrupador_010::where('id_examen', $examen->id)->where('estado',1)->first();
                if(!is_null($examen_agrupador_010)){
                    $id_agrupador_publico = $examen_agrupador_010->id_agrupador;
                }
                
                $eas = Examen_Agrupador_Sabana::where('id_examen',$examen->id)->first();
                $id_agrupador_privado = null;
                if(!is_null($eas)){
                    $id_agrupador_privado = $eas->id_examen_agrupador_labs;
                }
                if($examen->sexo_n_s==0){
                    $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',3)->where('estado','1')->get();
                } else{
                    $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',$paciente->sexo)->where('estado','1')->get();
                }
                foreach ($parametros as $parametro) {
                    $examen_resultado = Examen_Resultado::where('id_orden',$id_orden)->where('id_parametro', $parametro->id)->first();
                    //$examen_resultado =  $examen_orden->resultados->where('id_parametro', $parametro->id)->first();
                    
                    if(is_null($examen_resultado)){
                        if($fecha_orden > $parametro->created_at){
                            if($seguro->tipo==0){//publico
                                if($examen->id != '639' and $examen->id != '609'){
                                    $input_resultado = [
                                        'id_orden'        => $examen_orden->id,
                                        'id_parametro'    => $parametro->id,
                                        'valor'           => null,
                                        'ip_creacion'     => 'CARGA_EXA',
                                        'ip_modificacion' => $ip_cliente,
                                        'id_usuariocrea'  => $idusuario,
                                        'id_usuariomod'   => $idusuario,
                                        'id_examen'       => $examen->id,
                                        'id_agrupador_publico'    => $id_agrupador_publico,
                                    ];
                                    Examen_Resultado::create($input_resultado);
                                }

                            } else {
                                if($examen->id != '639' and $examen->id != '609'){
                                    $input_resultado = [
                                        'id_orden'        => $examen_orden->id,
                                        'id_parametro'    => $parametro->id,
                                        'valor'           => null,
                                        'ip_creacion'     => 'CARGA_EXA',
                                        'ip_modificacion' => $ip_cliente,
                                        'id_usuariocrea'  => $idusuario,
                                        'id_usuariomod'   => $idusuario,
                                        'id_examen'       => $examen->id,
                                        'id_agrupador_privado'    => $id_agrupador_privado,
                                    ];
                                    Examen_Resultado::create($input_resultado);
                                }

                            }
                        }    
                    } else{
                        if($seguro->tipo==0){//publico
                            
                            $input_resultado = [
                                
                                'id_examen'             => $examen->id,
                                'id_agrupador_publico'  => $id_agrupador_publico,
                                'fecha_anterior'        => $examen_resultado->updated_at,
                            ];
                            $examen_resultado->update($input_resultado);
                        } else {
                            
                            $input_resultado = [
                                
                                'id_examen'             => $examen->id,
                                'id_agrupador_privado'  => $id_agrupador_privado,
                                'fecha_anterior'        => $examen_resultado->updated_at,
                            ];
                            $examen_resultado->update($input_resultado);

                        }
                        //dd($examen_resultado);
                        
                    }
                }
            }
        }
    }

    public function realizar_new(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        
        $id = $request->id;
        $nombre_grupo = $request->nombre_grupo;
        $nombre_examen = $request->nombre_examen;
        $tipo = $request->tipo; //0:pendiente resultado , 1:pendiente certificar, 2:todo
       
        $orden      = Examen_Orden::find($id);

        $resultados = $this->buscar_laboratorio_orden($request);
        if($id == '148900'){
            //dd($resultados);
        }

        return view('laboratorio.orden.resultados_new', ['orden' => $orden, 'resultados' => $resultados, 'request' => $request]);
    }

    
    public function realizar_new_lista(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $nombre_grupo = $request->nombre_grupo;
        $nombre_examen = $request->nombre_examen;
        $tipo = $request->tipo; //0:pendiente resultado , 1:pendiente certificar, 2:todo
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        
        if($fecha_desde == null){
            $fecha_desde = date('Y-m-d');
        }

        if($fecha_hasta == null){
            $fecha_hasta = date('Y-m-d');
        }

        $resultados = $this->buscar_laboratorio($request);//dd($resultados);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $agrupadores_labs = Examen_Agrupador_labs::where('estado','1')->get();
        return view('laboratorio.orden.resultados_new_lista', ['resultados' => $resultados, 'request' => $request, 'agrupadores' => $agrupadores, 'agrupadores_labs' => $agrupadores_labs]);
    }

    public function buscar_laboratorio($request){
        $id = $request->id_orden;
        $nombre_grupo = $request->nombre_grupo;
        $nombre_examen = $request->nombre_examen;
        $tipo = $request->tipo; //0:pendiente resultado , 1:pendiente certificar, 2:todo
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $paciente = $request->paciente;
        if($fecha_desde == null){
            $fecha_desde = date('Y-m-d');
        }

        if($fecha_hasta == null){
            $fecha_hasta = date('Y-m-d');
        }

        $resultados = Examen_Resultado::join('examen as e','e.id','examen_resultado.id_examen')
            ->join('examen_orden as eo','eo.id','examen_resultado.id_orden')
            ->join('paciente as p','p.id','eo.id_paciente')
            ->leftjoin('examen_agrupador_labs as al','al.id','examen_resultado.id_agrupador_privado')
            ->leftjoin('examen_agrupador as ea','ea.id','examen_resultado.id_agrupador_publico')
            ->join('examen_parametro as ep','ep.id','examen_resultado.id_parametro')
            ->where('eo.fecha_orden','>=',$fecha_desde)->where('eo.fecha_orden','<=',$fecha_hasta.' 23:59:59');
        if($tipo == null){
            $tipo = 0;    
        }    
        if($id != null){
            $resultados = $resultados->where('examen_resultado.id_orden',$id);
        }    
        
        if($nombre_grupo != null){
            $resultados = $resultados
            ->Where(function ($query) use ($nombre_grupo){
                $query->orwhere('al.nombre','like','%'.$nombre_grupo.'%')
                      ->orwhere('ea.nombre','like','%'.$nombre_grupo.'%');
            });
        }    
        if($nombre_examen != null){
            $resultados = $resultados->where('e.nombre','like','%'.$nombre_examen.'%');
        }    
        if($tipo == 0){
            $resultados = $resultados->whereNull('examen_resultado.valor');
        }
        if($tipo == 1){
            $resultados = $resultados->where('examen_resultado.certificado',0);
        }

        if (!is_null($paciente)) {
            $nombres2    = explode(" ", $paciente);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $resultados = $resultados->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $resultados = $resultados->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }
        
        
        if(is_null($id)){
            $resultados = $resultados->select('eo.fecha_orden','eo.id')
            ->groupBy('eo.fecha_orden','eo.id')
            ->orderBy('eo.fecha_orden','eo.id','examen_resultado.id_examen')
            ->get();   
        }else{
            $resultados = $resultados->select('examen_resultado.id_orden', 'examen_resultado.id_parametro', 'examen_resultado.valor', 'examen_resultado.certificado', 'e.nombre as examen', 'al.nombre as grupo', 'e.id as id_examen','ep.nombre as parametro','ep.descripcion','ep.texto1','ep.valor1','ep.valor1g','ep.unidad1','ep.texto_referencia','e.tiene_detalle','e.texto','examen_resultado.id as res','ea.nombre as grupo2','e.resultado_especial','ep.id_parametro_ref','eo.id')
            ->orderBy('al.nombre','e.nombre','e.sexo_n_s','al.id as id_grupo','examen_resultado.id as result','e.resultado_especial','ep.id_parametro_ref')
            ->groupBy('eo.id')
            ->get();
        }
        return $resultados;

    }

    public function buscar_laboratorio_orden($request){

        $id = $request->id;
        $nombre_grupo = $request->nombre_grupo;
        $nombre_examen = $request->nombre_examen;
        $tipo = $request->tipo; //0:pendiente resultado , 1:pendiente certificar, 2:todo
        //$fecha_desde = $request->fecha_desde;
        //$fecha_hasta = $request->fecha_hasta;

        /*if($fecha_desde == null){
            $fecha_desde = date('Y-m-d');
        }

        if($fecha_hasta == null){
            $fecha_hasta = date('Y-m-d');
        }*/

        //$resultados = Examen_Resultado::join('examen as e','e.id','examen_resultado.id_examen')
        $resultados = Examen_Orden::find($id)->resultados()
            //->join('examen_orden as eo','eo.id','examen_resultado.id_orden')
            ->join('examen as e','e.id','examen_resultado.id_examen')
            ->leftjoin('examen_agrupador_labs as al','al.id','examen_resultado.id_agrupador_privado')
            ->leftjoin('examen_agrupador as ea','ea.id','examen_resultado.id_agrupador_publico')
            ->join('examen_parametro as ep','ep.id','examen_resultado.id_parametro');
            //->where('examen_resultado.id_orden',$id);
            //->where('eo.fecha_orden','>=',$fecha_desde)->where('eo.fecha_orden','<=',$fecha_hasta.' 23:59:59');
        if($tipo == null){
            $tipo = 0;    
        }      
        
        if($nombre_grupo != null){
            $resultados = $resultados
            ->Where(function ($query) use ($nombre_grupo){
                $query->orwhere('al.nombre','like','%'.$nombre_grupo.'%')
                      ->orwhere('ea.nombre','like','%'.$nombre_grupo.'%');
            });
        }    
        if($nombre_examen != null){
            $resultados = $resultados->where('e.nombre','like','%'.$nombre_examen.'%');
        }    
        if($tipo == 0){
            $resultados = $resultados->whereNull('examen_resultado.valor');
        }
        if($tipo == 1){
            $resultados = $resultados->where('examen_resultado.certificado',0);
        }
       
        $resultados = $resultados->select('examen_resultado.id_orden', 'examen_resultado.id_parametro', 'examen_resultado.valor', 'examen_resultado.certificado', 'e.nombre as examen', 'al.nombre as grupo', 'e.id as id_examen','ep.nombre as parametro','ep.descripcion','ep.texto1','ep.valor1','ep.valor1g','ep.unidad1','ep.texto_referencia','e.tiene_detalle','e.texto','examen_resultado.id as res','ea.nombre as grupo2','e.resultado_especial','ep.id_parametro_ref')
        ->orderBy('al.nombre','e.nombre','e.sexo_n_s','al.id as id_grupo','examen_resultado.id as result','e.resultado_especial','ep.id_parametro_ref')
        ->get();
       
        return $resultados;

    }

    public function nuevo_certificar($id_orden, $id, $n)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::find($id_orden);
        //MARCA COMO CERTIFICADO
        $resultado = $orden->resultados->where('id_parametro', $id)->first();
        if (!is_null($resultado)) {

            $input = [

                'certificado'     => $n,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,

            ];

            $resultado->update($input);
        }

        $this->marcar_maquina($id_orden);

        return 'ok';

    }

    public function marcar_maquina($id_orden)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $er_biometria   = '2'; $er_bioquimica = '2'; $er_manual = '2';
        //0 pendiente: tiene un examen sin ingresar 1:certificar: todos los exámenes ya ingresados y pendiente de certificar  2:listo

        $orden = Examen_Orden::find($id_orden);

        $resultados1 = Examen_Orden::find($id_orden)->resultados()
            ->join('examen as e','e.id','examen_resultado.id_examen')
            ->where('e.maquina','1')
            ->select('examen_resultado.id','examen_resultado.valor','examen_resultado.certificado')
            ->get();

        $resultados2 = Examen_Orden::find($id_orden)->resultados()
            ->join('examen as e','e.id','examen_resultado.id_examen')
            ->where('e.maquina','2')
            ->select('examen_resultado.id','examen_resultado.valor','examen_resultado.certificado')
            ->get();
            
        $resultados0 = Examen_Orden::find($id_orden)->resultados()
            ->join('examen as e','e.id','examen_resultado.id_examen')
            ->where('e.maquina','0')
            ->select('examen_resultado.id','examen_resultado.valor','examen_resultado.certificado')
            ->get();   
        
        foreach ($resultados1 as $resultado) {

            if($resultado->valor == null){

                $er_biometria = '0';
                break;

            }
            if ($resultado->certificado == '0') {
                
                $er_biometria = '1';
                break;

            }

            
        }

        foreach ($resultados2 as $resultado) {

            if($resultado->valor == null){

                $er_bioquimica = '0';
                break;

            }
            if ($resultado->certificado == '0') {
                
                $er_bioquimica = '1';
                break;

            }

            
        }

        foreach ($resultados0 as $resultado) {

            if($resultado->valor == null){

                $er_manual = '0';
                break;

            }
            if ($resultado->certificado == '0') {
                
                $er_manual = '1';
                break;

            }

            
        }

        $orden->update([
            'er_biometria'    => $er_biometria,
            'er_bioquimica'   => $er_bioquimica,
            'er_manual'       => $er_manual,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,    
        ]);

        

        if ($er_biometria == '0') {
            $descripcion = "ORDEN BIOMETRIA PENDIENTE DE INGRESAR EXAMEN";
        } elseif ($er_biometria == '1') {
            $descripcion = "ORDEN BIOMETRIA PENDIENTE DE CERTIFICACION";
        } else {
            $descripcion = "ORDEN BIOMETRIA CERTIFICADA";
        }

        if ($er_bioquimica == '0') {
            $descripcion = $descripcion. "-ORDEN BIOQUIMICA PENDIENTE DE INGRESAR EXAMEN";
        } elseif ($er_bioquimica == '1') {
            $descripcion = $descripcion. "-ORDEN BIOQUIMICA PENDIENTE DE CERTIFICACION";
        } else {
            $descripcion = $descripcion. "-ORDEN BIOQUIMICA CERTIFICADA";
        }

        if ($er_manual == '0') {
            $descripcion = $descripcion. "-ORDEN PENDIENTE DE INGRESAR EXAMEN";
        } elseif ($er_manual == '1') {
            $descripcion = $descripcion. "-ORDEN PENDIENTE DE CERTIFICACION";
        } else {
            $descripcion = $descripcion. "-ORDEN CERTIFICADA";
        }

        Log_usuario::create([

            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => $descripcion,
            'dato_ant1'   => "ORDEN: " . $orden->id,
            'dato1'       => "NUEVA OPCION: ",

        ]);

        return 'ok';
    }

    public function nuevo_certificar_grupo( Request $request )
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id = $request->id;
        $nombre_grupo = $request->nombre_grupo;
        $nombre_examen = $request->nombre_examen;
        $tipo = $request->tipo;

        $orden      = Examen_Orden::find($id);

        //$resultados = $this->buscar_laboratorio($request);
        $resultados = $this->buscar_laboratorio_orden($request);
        

        foreach ($resultados as $resultado){

            if($resultado->certificado == 0 and $resultado->valor != null){

                $res = Examen_Resultado::find($resultado->res);
                if(!is_null($res)){

                    $input = [

                        'certificado'     => '1',
                        'id_usuariomod'   => $idusuario,
                        'ip_modificacion' => $ip_cliente,
        
                    ];
        
                    $res->update($input); 
                    
                    
                }
                

            }

        }

        $this->marcar_maquina($id);

        return 'ok';

    }

    public function reporte_excel(Request $request)
    {
        $resultados = $this->buscar_laboratorio($request);
        
        
        $titulos = array("No", "Orden", "Paciente", "Fecha", "Seguro","Grupo","Examen","Parametro");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H");
        Excel::create('REPORTE EXAMENES PENDIENTES', function ($excel) use ($titulos, $posicion, $resultados,$request) {
            $excel->sheet('REPORTE EXAMENES PENDIENTES', function ($sheet) use ($titulos, $posicion, $resultados,$request) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('REPORTE DE EXAMENES PENDIENTES');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#DAF7A6');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#DAF7A6');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $cont = 1;
                foreach ($resultados as $val) {
                    $orden =Examen_Orden::find($val->id);
                    $request['id'] = $val->id;
                    //$resultadosx = $this->buscar_laboratorio($request);
                    $resultadosx = $this->buscar_laboratorio_orden($request);//dd($resultadosx);
                    foreach ($resultadosx as $value) {
                        $datos_excel = array();
                        array_push(
                            $datos_excel,
                            $cont,
                            $orden->id,
                            $orden->paciente->id .' - ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->apellido2 . ' ' . $orden->paciente->nombre1 . ' ' . $orden->paciente->nombre2,
                            $orden->fecha_orden,
                            $orden->seguro->nombre,
                            $value->grupo . ' ' . $value->grupo2,
                            $value->examen,
                            $value->parametro
                        );

                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                    $cont++;
                    
                }
                // $sheet->setWidth(array(
                //     'A' => 15,
                //     'B' => 50,
                //     'C' => 18,
                //     'D' => 10,
                //     'E' => 10,
                //     'F' => 10,
                //     'G' => 15,
                //     'H' => 15,
                //     'I' => 15,
                //     'J' => 15,
                //     'K' => 15,
                //     'L' => 15,
                //     'M' => 15,
                //     'N' => 15,
                //     'O' => 15,
                //     'P' => 15,
                //     'Q' => 15,
                //     'R' => 15,

                //     'S' => 15,
                //     'T' => 15,
                //     'U' => 15,

                //     'V' => 15,
                //     'W' => 15,
                //     'X' => 15,

                //     'Y' => 15,
                //     'Z' => 15,
                //     'AA' => 15,

                //     'AB' => 15,
                //     'AC' => 15,

                // ));
            });
            // $excel->getActiveSheet()->getStyle('F1:F' . $excel->getActiveSheet()->getHighestRow())
            //     ->getAlignment()->setWrapText(true);
            // $excel->getActiveSheet()->getStyle('D1:D' . $excel->getActiveSheet()->getHighestRow())
            //     ->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }
    public function cargar_log(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request->id;

        $log = Log_usuario::join('users as u','log_usuario.id_usuario','u.id')
        ->where('log_usuario.dato_ant1','like','%'.$id.'%')
        ->where('log_usuario.dato1','like','%maquina%')
        ->select(
            'log_usuario.id_usuario',
            DB::raw("CONCAT_WS('', u.nombre1, ' ', u.apellido1, ' ', u.apellido2) as usuario"),
            'log_usuario.dato_ant1',
            'log_usuario.descripcion',
            'log_usuario.created_at as fecha',
            'log_usuario.ip_usuario'
        )
        ->get();
        return view('laboratorio.orden.resultados_log', ['log' => $log]);
    }
}
