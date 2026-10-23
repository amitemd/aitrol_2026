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
use Sis_medico\UserMembresia;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Membresia;
use Sis_medico\Convenio;
use Sis_medico\Examen_Detalle_Costo;
use Sis_medico\Apps_Plan_Miembros;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;
use Carbon\Carbon;
use Exception;

class MembresiasLabsController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 16, 20)) == false) {
            return true;
        }

    }

    public function buscar_membresia_IECED($id_paciente){  
        $empresa = Empresa::where('prioridad','1')->get()->first();//dd($empresa);
        $paciente = Paciente::find($id_paciente);//dd($paciente);
        if(!is_null($paciente)){
            $usuario = User::find($id_paciente);
            if(!is_null($usuario)){
                //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
                $membresia = UserMembresia::where('user_membresia.user_id',$id_paciente)->where('user_membresia.estado',1)->join('membresia as m','m.id','user_membresia.membresia_id')->where('m.empresa_id',$empresa->id)->select('user_membresia.*')->first();//dd($id_paciente,$membresia,$empresa->id);
                if(!is_null($membresia)){
                    return ['estado' => 'ok', 'id' => $membresia->membresia->id, 'nombre' => $membresia->membresia->nombre];
                }else{
                    $miembro_plan = Apps_Plan_Miembros::where('cedula',$id_paciente)->where('id_empresa',$empresa->id)->where('estado',1)->first();
                    if(!is_null($miembro_plan)){
                        return ['estado' => 'ok', 'id' => $miembro_plan->membresia->id, 'nombre' => $miembro_plan->membresia->nombre];    
                    }else{
                        return ['estado' => 'error', 'mensaje' => "Sin Membresia Activa"];    
                    }
                }
            }else{
                return ['estado' => 'error', 'mensaje' => "No existe usuario"];
            }
        }else{
            return ['estado' => 'error', 'mensaje' => "No existe Paciente"];
        }
    }
    public function buscar_membresia($id_paciente){  
        try{

        
        $empresa = Empresa::where('prioridad_labs','1')->get()->first();
        
        $paciente = Paciente::find($id_paciente);
        if(!is_null($paciente)){
                
            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $elemento = json_encode([ 
                "paciente" => $id_paciente,
                "empresa"  => $empresa->id,
                "token"    => "8c0a00ec19933215dc29225e645ea714",
            ]);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,
                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $url     = "http://bd.aitrol.com/app/membresia/general";
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $respuesta = json_decode($response, true);
            //dd($respuesta);
                
            //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
            /*$membresia = UserMembresia::where('user_membresia.user_id',$id_paciente)->where('user_membresia.estado',1)->join('membresia as m','m.id','user_membresia.membresia_id')->where('m.empresa_id',$laboratorio->id)->select('user_membresia.*')->first();*/
            if($respuesta['result'] == 'ok'){
                if(isset($respuesta['resultado'])){
                    if(isset($respuesta['resultado']['membresia'])){
                        $detalles = [];
                        if( isset( $respuesta['resultado']['detalles'] )){
                            $detalles = $respuesta['resultado']['detalles'];     
                        }
                        return [
                            'estado'        => 'ok', 
                            'id'            => $respuesta['resultado']['membresia']['membresia_id'], 
                            'nombre'        => $respuesta['resultado']['membresia']['nombre'], 
                            'puntos'        => $respuesta['resultado']['membresia']['puntos'], 
                            'id_user_mem'   => $respuesta['resultado']['membresia']['id'],
                            'detalles'      => $detalles,
                            'tipo'          => $respuesta['resultado']['tipo'],
                        ];    
                    }
                }
            
            } 

        }    
                  
        return ['estado' => 'error', 'mensaje' => "Sin Membresia Activa"];            
    } catch(Exception $e){
        return ['estado' => 'error', 'mensaje' => $e->getMessage()]; 
    }
    }

    public function buscar_membresia_find_IECED($id_membresia){
        
        //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
        $membresia = Membresia::find($id_membresia);

        $detalles = [];
        foreach ($membresia->detalles as $detalle) {
            $detalles[$detalle->aplica_proc_cons] = [ 'descuento' => $detalle->porcentaje_descuento ];
        }

        return ['membresia' => $membresia, 'detalles' => $detalles];
    }

    public function crear_membresia($array_membresia){

        //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
        UserMembresia::create([
            'user_id'           => $array_membresia['user_id'],
            'membresia_id'      => $array_membresia['membresia_id'],
            'fecha_compra'      => $array_membresia['fecha_compra'],
            'meses'             => $array_membresia['meses'],
            'valor_pagado'      => $array_membresia['valor_pagado'],
            'meses_contratados' => $array_membresia['meses_contratados'],
            'id_usuariocrea'    => $array_membresia['id_usuariocrea'],
            'id_usuariomod'     => $array_membresia['id_usuariocrea'],
            'ip_creacion'       => $array_membresia['ip_creacion'],
            'ip_modificacion'   => $array_membresia['ip_modificacion'],
            'estado'            => $array_membresia['estado'],
            'id_orden'          => $array_membresia['id_orden'],
            'referido'          => $array_membresia['referido'],
        ]);

    }

    public function crear_membresia_labs($array_membresia){

        $empresa = Empresa::where('prioridad_labs','1')->get()->first();
        $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
        $elemento = json_encode([ 
            "token"             => "8c0a00ec19933215dc29225e645ea714",
            'id_usuario'        => $array_membresia['user_id'],
            'empresa'           => $empresa->id,    
            'membresia_id'      => $array_membresia['membresia_id']      ,
            'fecha_compra'      => $array_membresia['fecha_compra']     ,
            'meses'             => $array_membresia['meses']             ,
            'valor_pagado'      => $array_membresia['valor_pagado']      ,
            'meses_contratado'  => $array_membresia['meses_contratados'],
            'id_usuariocrea'    => $array_membresia['id_usuariocrea']    ,
            'requestId'         => null,
            'referido'          => $array_membresia['referido'],
            'id_orden'          => $array_membresia['id_orden'],
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $url     = "http://bd.aitrol.com/app/crea/membresia";
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        //dd($respuesta,$elemento);
        //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
        /*UserMembresia::create([
            'user_id'           => $array_membresia['user_id'],
            'membresia_id'      => $array_membresia['membresia_id'],
            'fecha_compra'      => $array_membresia['fecha_compra'],
            'meses'             => $array_membresia['meses'],
            'valor_pagado'      => $array_membresia['valor_pagado'],
            'meses_contratados' => $array_membresia['meses_contratados'],
            'id_usuariocrea'    => $array_membresia['id_usuariocrea'],
            'id_usuariomod'     => $array_membresia['id_usuariocrea'],
            'ip_creacion'       => $array_membresia['ip_creacion'],
            'ip_modificacion'   => $array_membresia['ip_modificacion'],
            'estado'            => $array_membresia['estado'],
            'id_orden'          => $array_membresia['id_orden'],
            'referido'          => $array_membresia['referido'],
        ]);*/

        return ['estado' => 'ok', 'mensaje' => "Membresia Creada"]; 

    }

    public function agenda_IECED(Request $request){


        $consultas = DB::table('apps_agenda')->join('agenda','agenda.id','apps_agenda.id_agenda')
                        ->select('agenda.*')->where('agenda.estado_cita','0')->where('agenda.estado','1')->where('fechaini','>','2022-07-18 00:00:00')->get();
                        
        return view('agenda/modal_Ieced', [ 'consultas' => $consultas ]);               

    }

    public function actualizar_puntos($data){
        
        //AQUI DEBERIA APUNTAR A LA NUEVA BASE EXTERNA
        //dd($data);
        $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
        $elemento = json_encode([ 
            "token"             => "8c0a00ec19933215dc29225e645ea714",
            "id_membresia"      => $data['id'],
            "tipo"              => "3", 
            "valor"             => $data['puntos'], 
            "motivo"            => $data['motivo'],
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $url     = "http://bd.aitrol.com/app/actualiza/puntos";
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        /*$membresia = UserMembresia::find($data['id']);//dd($data,$membresia);

        $membresia->update(['puntos' => $data['puntos']]);*/

        return ['id' => $data['id']];
    }

    public function validar_puntos($id_orden){

        $orden = Examen_Orden::find($id_orden);

        if(is_null($orden)){
            return['estado' => 'err', 'msn' => 'No existe Orden'];
        }

        $res = $this->buscar_membresia($orden->id_paciente);
        if($res['estado'] == 'error'){
            return['estado' => 'err', 'msn' => 'No existe Membresia'];        
        }

        if($res['puntos'] == 0){
            return['estado' => 'err', 'msn' => 'Sin Puntos Por Aplicar'];        
        }   

        if($orden->total_valor <= 0){
            return['estado' => 'err', 'msn' => 'Orden Sin Valor a Aplicar'];    
        } 

        $puntos_aplicar = $res['puntos'];
        if($orden->total_valor <= $res['puntos']){
                $puntos_aplicar = intval($orden->total_valor);
        }

        if($puntos_aplicar <= 0){
            return['estado' => 'err', 'msn' => 'Orden Sin Valor a Aplicar' ];    
        } 

        return['estado' => 'ok', 'msn' => $puntos_aplicar ]; 

    }

    public function aplicar_puntos($id_orden){

        $orden = Examen_Orden::find($id_orden);
        $empresa = Empresa::where('prioridad_labs',1)->first();

        if(is_null($orden)){
            return['estado' => 'err', 'msn' => 'No existe Orden'];
        }
            
        $res = $this->buscar_membresia($orden->id_paciente);
        if($res['estado'] == 'error'){
            return['estado' => 'err', 'msn' => 'No existe Membresia'];        
        }

        if($res['puntos'] == 0){
            return['estado' => 'err', 'msn' => 'Sin Puntos Por Aplicar'];        
        }   

        if($orden->total_valor <= 0){
            return['estado' => 'err', 'msn' => 'Orden Sin Valor a Aplicar'];    
        } 

        $puntos_aplicar = $res['puntos'];
        if($orden->total_valor <= $res['puntos']){
                $puntos_aplicar = intval($orden->total_valor);
        }

        if($puntos_aplicar <= 0){
            return['estado' => 'err', 'msn' => 'Orden Sin Valor a Aplicar' ];    
        } 

        $puntos_actual = $res['puntos'];
        $data['id']    = $res['id_user_mem'];
        if($puntos_aplicar > $puntos_actual){
            return['estado' => 'err', 'msn' => 'Puntos a aplicar no puede ser mayor a puntos actual' ];
        }

        $n_pct_dcto = ( $orden->descuento_valor + $puntos_aplicar ) / $orden->valor;
        $n_pct_dcto = $n_pct_dcto * 100;
        $n_pct_dcto = round($n_pct_dcto, 2);

        $orden->update(['puntos_aplicados' => $puntos_aplicar, 'descuento_p' => $n_pct_dcto]);

        $data['puntos'] = $puntos_actual - $puntos_aplicar;

        $data['motivo'] = 'EMPRESA: '.$empresa->id.'-'.$empresa->establecimiento.'-'.$empresa->punto_emision.'+ ORDEN:'.$orden->id;

        $this->actualizar_puntos($data);


        return['estado' => 'ok', 'puntos_aplicar' => $puntos_aplicar, 'descuento_p' => $n_pct_dcto]; 

    }


}