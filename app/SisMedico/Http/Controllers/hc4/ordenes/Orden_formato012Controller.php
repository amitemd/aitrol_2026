<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Paciente;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Orden;
use Sis_medico\Orden_012;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Carbon\Carbon;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Cie10;
use Sis_medico\Orden_012_Cie10; 
use Sis_medico\Cie_10_3; 
use Sis_medico\Cie_10_4;
use Sis_medico\Firma_Usuario;
use Excel;
use PHPExcel_Style_Alignment;
use Response;
 
class Orden_formato012Controller extends Controller
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

    //Muestra el historial de las ordenes de Imagenes
    //Solo para Doctores


    public function formato012($id, $id_orden)
    {

        //dd($id);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        $evolucion = Hc_Evolucion::where('id', $id)->where('secuencia','0')->first();
        
        //Obtenemos Historia Clinica a partir del hcid de la Tbl hc_evolucion
        $hcid = $evolucion->hcid;
        $historiaclinica = Historiaclinica::find($hcid);
        
        //Obtenemos datos del Paciente
        $paciente = Paciente::find($historiaclinica->id_paciente);
        
        //Obtenemos datos de la Agenda
        $agenda = Agenda::find($historiaclinica->id_agenda);
       
        //Obtenemos una parte de la Fecha de la Agenda
        $fecha_agenda = substr($agenda->fechaini,0,10);
       
        //Obtenemos el Listado de Usuario de Tipo Doctores
        $doctores = User::where('estado','1')
                          ->where('id_tipo_usuario','3')
                          ->OrderBy('apellido1','asc')
                          ->where('training','0')
                          ->where('uso_sistema','0')
                          ->get();
        
        //Obtenemos el Listado de Seguro
        $seguros = Seguro::where('inactivo','1')
                           ->OrderBy('nombre','asc')
                           ->get();
        
        
        //Obtenemos  el Listado de Empresas que tengan convenio con tipo de Seguro IESS
        $empresas = Empresa::where('empresa.estado','1')
                            ->join('convenio as c','c.id_empresa','empresa.id')
                            ->where('empresa.id','<>','9999999999')
                            ->OrderBy('empresa.nombrecomercial','asc')
                            ->where('c.id_seguro','2')
                            ->select('empresa.*')->get();

        
        //$orden_012 = Orden_012::where('id_hc_evolucion',$id)->first();
    

        //Si Existe el id evolucion en la tabla hc_evolucion
        if(!is_null($evolucion)) {


            //Luego Vericamos si existe el id evolucion en la tabla orden_012
            $orden_012 = Orden_012::where('id_hc_evolucion',$id)
                                    ->where('estado','1')
                ->where('id_orden', $id_orden)
                                    ->first();

            if(!is_null($orden_012)){

                $cie10 = Hc_Cie10::where('hcid',$historiaclinica->hcid)
                                   ->groupBy('cie10')
                                   ->get();

                if(!is_null($cie10)){

                    foreach ($cie10 as $value) {
                        
                        $arr_cie10 = [
                            'id_orden_012' => $orden_012->id,
                            'cie10' => $value->cie10,
                            'presuntivo_definitivo' => $value->presuntivo_definitivo,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,

                        ];
                        
                        Orden_012_Cie10::create($arr_cie10);
                        
                    }
            
                }
 
                //Consultamos si existe el ID de la Tbl orden_012
                $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012',$orden_012->id)
                                                    ->get();

                //Invocamos a la vista de Formulario 012
                return view('hc4.ordenes.orden_procedimiento_endoscopico.formato012',['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10, 'agenda' => $agenda]);


            }else{

                //Verificamos en la Tabla  Orden de procedimientos Endoscopicos,Funcionales,Imagenes
                /*$ordenes = Orden::where('id_paciente',$paciente->id)
                                  ->whereBetween('fecha_orden',[$fecha_agenda.' 0:00:00', $fecha_agenda.' 23:59:59'])->where('estado','1')
                                  ->get();*/

                $arr = [
                    'id_paciente' => $paciente->id,
                    'id_doctor_solicita' => $idusuario,
                    'id_doctor_firma' => $idusuario,
                    'fecha_orden' => $agenda->fechaini,
                    'id_hc_evolucion' => $id,
                    'id_orden'           => $id_orden,
                    'estado' => '1',
                    'rutina' => '1',
                    'puede_mover' => '1',
                    //'descripcion' => $ordenes->descripcion,  Esta mal la Definicion
                    'motivo' => $evolucion->motivo,//Evolucion Motivo
                    'id_empresa'         => $agenda->id_empresa,
                    'cuadro_clinico' => $evolucion->cuadro_clinico,//Evolucion Cuadro Clinico
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];

                //Insertamos en la Tabla Orden_012 y Obtenemos el id de la Tabla
                $id_012 = Orden_012::insertGetId($arr);

                //Una vez Insertado Consultamos la Tabla orden_012
                $orden_012 = orden_012::find($id_012);

                /*$cie10 = Hc_Cie10::where('id',$historiaclinica->hcid)
                                   ->groupBy('cie10')
                                   ->get();*/

                $cie10 = Hc_Cie10::where('hcid',$historiaclinica->hcid)
                                   ->groupBy('cie10')
                                   ->get();
                
                
                if(!is_null($cie10)){
                    
                    foreach ($cie10 as $value) {
                        
                        $arr_cie10 = [
                            'id_orden_012' => $id_012,
                            'cie10' => $value->cie10,
                            'presuntivo_definitivo' => $value->presuntivo_definitivo,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,

                        ];
                        
                        Orden_012_Cie10::create($arr_cie10);
                        
                    }
                
                }
            
                //Consultamos si existe el ID de la Tbl orden_012
                $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012',$orden_012->id)
                                                    ->get();

            
                return view('hc4.ordenes.orden_procedimiento_endoscopico.formato012',['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10, 'agenda' => $agenda]);
           
            }

        }
    
    }

    public function actualizar_formato012(Request $request){
        
        //return $request->id_empresa;

        //return $request->id_orden;
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $empresa = Empresa::where('prioridad', 1)->first();

        $orden_012 = Orden_012::find($request->id_orden);
        
        //return $orden_012;
        
        $evolucion = Hc_Evolucion::find($orden_012->id_hc_evolucion);
        $historiaclinica = $evolucion->historiaclinica;
        $agenda = $historiaclinica->agenda;
        $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();
        if(!is_null($orden_012)){
            $arr = [                
                'id_doctor_firma' => $request->id_doctor_examinador,
                'id_doctor_solicita' => $request->id_doctor_solicita,
                'fecha_orden'     => $request->fecha_orden,
                'referido'        => $request->referido,
                'descripcion'     => $request->descripcion,
                'diagnosticos'    => $request->diagnosticos,
                'motivo' => $request->motivo,
                'cuadro_clinico' => $request->historia_clinica,
                'urgente' => $request->urgente,
                'rutina' => $request->rutina,
                'control' => $request->control,
                'rx_convencional' => $request->rx_convencional,
                'rx_portatil'     => $request->rx_portatil,
                'tomografia' => $request->tomografia,
                'mamografia'      => $request->mamografia,
                'resonancia' => $request->resonancia,
                'ecografia' => $request->ecografia,
                'descripcion'     => $request->descripcion,
                'procedimiento' => $request->procedimiento,
                'otros' => $request->otros,
                'paciente_contaminado'           => $request->paciente_contaminado,
                'sedacion'           => $request->sedacion,
                'texto_otros' => $request->texto_otros,
                'puede_mover' => $request->puede_mover,
                'puede_retirar' => $request->puede_retirar,
                'medico_presente' => $request->medico_presente,
                'toma_radio' => $request->toma_radio,
                'servicio' => $request->servicio,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_empresa'      => $empresa->id,
            ];

            //return $arr;

            $orden_012->update($arr);
            // $agenda->update(['id_empresa' => $request->id_empresa]); cambio
           
        }
        return 'ok';
    }

    public function imprimir_012_excel($id)
    {
        return app(\Sis_medico\Services\ClinicalOrder012ExportService::class)->export($id);
    }
    public function orden_ingresada_formato012(){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
      
        $doctores = User::where('id_tipo_usuario',3)->where('estado',1)->orderby('apellido1')->get();

        $fecha       = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');

        $ordenes_012 = DB::table('orden_012 as o12')
                             ->join('paciente as p','p.id','=','o12.id_paciente')
                             ->join('empresa as e','e.id','=','o12.id_empresa')
                             ->whereBetween('o12.created_at', [$fecha.' 00:00', $fecha.' 23:59'])
                             ->leftjoin('users as d1','d1.id','=','o12.id_doctor_firma')
                             ->select('o12.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2',
                                            'p.apellido1 as papellido1','p.apellido2 as papellido2',
                                            'e.nombrecomercial as enombre','d1.nombre1 as dnombre1',
                                            'd1.apellido1 as dapellido1','d1.apellido2 as dapellido2',
                                            'd1.color as d1color')
                            ->orderby('o12.created_at','desc')
                            ->get();

        // dd($ordenes_012);
        // dd($fecha);

        return view('hc4.ordenes.orden_ingresada_formato012.index',['ordenes_012'=>$ordenes_012,'doctores' => $doctores, 'fecha' => $fecha, 'fecha_hasta'=>$fecha_hasta, 'id_doctor1' => null, 'nombres' => '', 'cedula'=> '']);
    }

    //ESTA FUNCTION BUSCA LAS ORDENES
    //EN SISTEMA MEDICO PRB
    public function search(Request $request) {
    
        $cedula         = $request['cedula'];
        $nombres        = $request['nombres'];
        $fecha          = $request['fecha'];
        $fecha_hasta    = $request['fecha_hasta'];
        $id_doctor1     = $request['id_doctor1'];

        $doctores = User::where('id_tipo_usuario',3)->where('estado',1)->orderBy('apellido1')->get();

        $ordenes_012 = DB::table('orden_012 as o12')
            ->join('paciente as p','p.id','=','o12.id_paciente')
            ->join('empresa as e','e.id','=','o12.id_empresa')
            ->leftjoin('users as d1','d1.id','=','o12.id_doctor_firma')
            ->select('o12.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2',
                            'p.apellido1 as papellido1','p.apellido2 as papellido2',
                            'e.nombrecomercial as enombre','d1.nombre1 as dnombre1',
                            'd1.apellido1 as dapellido1','d1.apellido2 as dapellido2',
                            'd1.color as d1color');
            // dd($fecha,$ordenes_012->get());

        if($fecha!=null && $fecha_hasta!=null){
            $ordenes_012 = $ordenes_012->whereBetween('o12.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }

        if($cedula!=null){
            $ordenes_012 = $ordenes_012->where('o12.id_paciente',$cedula);
        }

        if($id_doctor1!=null){
            $ordenes_012 = $ordenes_012->where('o12.id_doctor_firma',$id_doctor1);    
        }

        // $ordenes_012 = Orden_012::orderBy('created_at','desc')->get();
        // $ordenes_012 = $ordenes_012->get();
        $ordenes_012 = $ordenes_012->orderBy('o12.created_at','desc')->get();
        
        // dd($ordenes_012);
        // dd($fecha);

        return view('hc4.ordenes.orden_ingresada_formato012.index', ['ordenes_012' => $ordenes_012, 'cedula' => $cedula, 'nombres' => $nombres, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1]);
    
    }


    //ESTA FUNCTION OBTIENE LA INFOAMCION POR PACIENTE
    //MUESTRA LA INFORMACION EN UN FORMULARIO POR EL LADO DE SISTEMA MEDICO PRB
    public function Editar_formato012(Request $request, $id){

        //Obtenemos la Lista de Doctores
        $doctores = User::where('estado','1')
                        ->where('id_tipo_usuario','3')
                        ->OrderBy('apellido1','asc')
                        ->where('training','0')
                        ->where('uso_sistema','0')
                        ->get();

        //Obtenemos la Lista de Empresa
        $empresas = Empresa::where('empresa.estado','1')
                        ->join('convenio as c','c.id_empresa','empresa.id')
                        ->where('empresa.id','<>','9999999999')
                        ->OrderBy('empresa.nombrecomercial','asc')
                        ->where('c.id_seguro','2')
                        ->select('empresa.*')
                        ->get();
        
        $ordenes_012 = Orden_012::find($id);

        $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012',$ordenes_012->id)->get();
        //dd($id);

        return view('hc4.ordenes.orden_ingresada_formato012.editar_formato012',['ordenes_012'=>$ordenes_012,'doctores'=>$doctores, 'empresas'=>$empresas, 'orden_012_cie10'=>$orden_012_cie10]);
    }
    
    //ACTUALIZA LA ORDEN 012 POR PACIENTE
    //EN El SISTEMA MEDICO PRB
    //public function Actualizar_formato_012(Request $request){


        //$ip_cliente= $_SERVER["REMOTE_ADDR"];
        //$idusuario = Auth::user()->id;

       // $ordenes_012 = Orden_012::find($request->id_orden);
       // $evolucion = Hc_Evolucion::find($ordenes_012->id_hc_evolucion);
        //$historiaclinica = $evolucion->historiaclinica;
        //$agenda = $historiaclinica->agenda;
        
       // if(!is_null($ordenes_012)){
            //$arr = [                
               // 'id_doctor_firma' => $request->id_doctor_examinador,
               // 'fecha_orden' => $request->fecha_orden,
                //'referido' => $request->referido,
                //'descripcion' => $request->descripcion,
                //'motivo' => $request->motivo,
                //'cuadro_clinico' => $request->historia_clinica,
                //'urgente' => $request->urgente,
                //'rutina' => $request->rutina,
                //'control' => $request->control,
                //'rx_convencional' => $request->rx_convencional,
                //'tomografia' => $request->tomografia,
               // 'resonancia' => $request->resonancia,
                //'ecografia' => $request->ecografia,
                //'procedimiento' => $request->procedimiento,
                //'otros' => $request->otros,
                //'texto_otros' => $request->texto_otros,
                //'puede_mover' => $request->puede_mover,
                //'puede_retirar' => $request->puede_retirar,
                //'medico_presente' => $request->medico_presente,
                //'toma_radio' => $request->toma_radio,
                //'servicio' => $request->servicio,
                //'id_usuariomod' => $idusuario,
               // 'ip_modificacion' => $ip_cliente,
                //'id_empresa'=> $request->id_empresa,    //cambio
            //];

            //return $arr;

           // $ordenes_012->update($arr);
            // $agenda->update(['id_empresa' => $request->id_empresa]); cambio
           
       // }

        ////return 'ok';

    //}
    

    
}

