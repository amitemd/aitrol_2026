<?php

namespace Sis_medico\Http\Controllers\hc_admision;


use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Orden_Doctor;
use Sis_medico\Log_usuario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Tipo_Procedimiento;
use Sis_medico\Tipo_Detalle_Orden;
use Sis_medico\Cardiologia;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Orden_Doctor_Detalle;
use Sis_medico\Firma_Usuario;
use Excel;
use PHPExcel_Style_Alignment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;
use Sis_medico\Orden;
use Sis_medico\Orden_012;
use Sis_medico\Hc_Cie10;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc4_Biopsias_Ptv;


class Orden_ProcController extends Controller
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
        if(in_array($rolUsuario, array(1, 3, 6,11,7, 20)) == false){
          return true;
        }
    }

    private function rol_paciente(){
        $rolUsuario = Auth::user()->id_tipo_usuario;

        if(in_array($rolUsuario, array(2)) == false){
          return true;
        }
    }

    public function crear_editar($hcid){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if($this->rol()){
            return response()->view('errors.404');
        }

        $historiaclinica = Historiaclinica::find($hcid);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid','paciente.referido','paciente.id_usuario','paciente.trabajo','paciente.observacion','paciente.alcohol','hs.nombre as hsnombre','h.presion','h.pulso','h.temperatura','h.o2','h.altura','h.peso','h.perimetro','h.examenes_realizar','h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = Orden_Doctor::where('hcid',$hcid)->first();

        $seguros = Seguro::where('inactivo','1')->get();

        $tipos = Tipo_Procedimiento::where('estado','1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado','1')->orderBy('orden', 'asc')->get();

        $detalle_orden = null;
        if(!is_null($orden)){

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor',$orden->id)->get();

        }else{
            if($agenda->proc_consul == 0){
                $evolucion = Hc_Evolucion::where('hcid',$hcid)->first();
                $input = [

                        'hcid' => $hcid,
                        'id_paciente' => $historiaclinica->id_paciente,
                        'motivo' => $evolucion->motivo,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,

                    ];
                    Orden_Doctor::create($input);
            }else{
                $evolucion = hc_protocolo::where('hcid',$hcid)->first();
                $input = [

                        'hcid' => $hcid,
                        'id_paciente' => $historiaclinica->id_paciente,
                        'motivo' => $evolucion->motivo,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,

                    ];
                    Orden_Doctor::create($input);
            }


        }



        //dd(Date('d/m/Y',strtotime($orden->fecha_examen)));
        return view('hc_admision.orden_proc.orden_proc',['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'historiaclinica' => $historiaclinica]);
    }

    public function imprimir_orden($hcid)
    {

        $historiaclinica = Historiaclinica::find($hcid);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid','paciente.referido','paciente.id_usuario','paciente.trabajo','paciente.observacion','paciente.alcohol','hs.nombre as hsnombre','h.presion','h.pulso','h.temperatura','h.o2','h.altura','h.peso','h.perimetro','h.examenes_realizar','h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = DB::table('orden_doctor as od')->where('od.hcid',$hcid)->join('users as u','u.id','od.id_usuariocrea')->select('od.*','u.nombre1','u.apellido1')->first();

        $seguros = Seguro::where('inactivo','1')->get();

        $tipos = Tipo_Procedimiento::where('estado','1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado','1')->orderBy('orden', 'asc')->get();

        $detalle_orden = null;
        if(!is_null($orden)){

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor',$orden->id)->get();

        }
        $age = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;

        //return view('hc_admision.orden_proc.orden_pdf', ['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'age' => $age]);
        $view =  \View::make('hc_admision.orden_proc.orden_pdf', compact('agenda', 'seguros', 'orden', 'tipos', 'detalles', 'detalle_orden', 'age'))->render();
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);


        return $pdf->download('orden_doctor_'.$agenda->id_paciente.'.pdf');


/*
       $agenda = Agenda::find($id);
        $historia = Historiaclinica::where('id_agenda', $id)->first();

        //$historia = Historiaclinica::find($historia[0]->hcid);
        if(!is_null($historia)){
            $seguro = Seguro::find($historia->id_seguro);
            $paciente = Paciente::find($historia->id_paciente);
            $doctor = User::find($historia->id_doctor1);
            $responsable = User::find($historia->id_usuariocrea);
            if($agenda->proc_consul=='1'){
                $pentax = Pentax::where('id_agenda',$agenda->id)->first();
                $procedimientos = PentaxProc::where('id_pentax',$pentax->id)->get();
                $procedimientos_txt = '';
                foreach ($procedimientos as $value) {
                    if($procedimientos_txt==''){
                        $procedimientos_txt = procedimiento::find($value->id_procedimiento)->nombre;
                    }else{
                        $procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;
                    }
                }
            }else{
                $procedimientos_txt = 'CONSULTA';
            }
            $ControlDocController = new ControlDocController;
            $documentos = $ControlDocController->carga_documentos_union($historia->hcid, $agenda->proc_consul, $seguro->tipo);

            $data = $historia;
            $date = $historia->created_at;


        }else{
            $seguro = Seguro::find($agenda->id_seguro);
            $paciente = Paciente::find($agenda->id_paciente);
            $doctor = User::find($agenda->id_doctor1);
            $responsable = User::find($agenda->id_usuariocrea);
            if($agenda->proc_consul=='1'){
                $procedimientos_txt = procedimiento::find($agenda->id_procedimiento)->nombre;
                $procedimientos = AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
                foreach ($procedimientos as $value) {

                    $procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;

                }
            }else{
                $procedimientos_txt = 'CONSULTA';
            }

            $documentos = null;

            $data = $agenda;
            $date = $agenda->created_at;

        }



        $empresa = Empresa::where('id',$agenda->id_empresa)->first();
        if(is_null($empresa)){
            $empresa = Empresa::find('1391707460001');
        }

        //$empresaxdoc = Empresa::find($agenda->id_empresa);


        //nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
        //$procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento',$agenda->id_procedimiento)->first();



        //dd($empresa);


        $paper_size = array(0,0,595,920);

        //return view('hc_admision/formato/'.$documento->formato);
        $view =  \View::make('hc_admision.formato.resumen', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'seguro', 'responsable','procedimientos_txt','documentos'))->render();
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view)/*->setPaper($paper_size, 'portrait');

        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        if(!is_null($historia)){
            return $pdf->download($historia->id_paciente.'_resumen_'.$historia->hcid.'.pdf');
        }else{
            return $pdf->download($agenda->id_paciente.'_resumenAG_'.$agenda->id.'.pdf');
        } */
    }

    public function guardar(Request $request, $hcid){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        //return $request->all();

        $historiaclinica = DB::table('historiaclinica')->where('hcid',$hcid)->first();

        //return $historiaclinica->id_paciente;

        //return $request['hcid'];

        $orden_d = Orden_Doctor::where('hcid',$hcid)->first();



        if(!is_null($orden_d)){


            $input = [
                    'id_seguro' => $request['id_seguro'],
                    'motivo' => $request['motivo'],
                    'fecha_examen' => $request['fecha_examen'],
                    'observacion' => $request['observacion'],
                    'endoscopia_urgencia' => $request['endoscopia_urgencia'],
                    'endoscopia_terapeutica' => $request['endoscopia_terapeutica'],
                    'eco_doppler' => $request['eco_doppler'],
                    'ecografia' => $request['ecografia'],
                    'prueba_func' => $request['prueba_func'],
                    'campo1' => $request['campo1'],
                    'campo2' => $request['campo2'],
                    'campo3' => $request['campo3'],
                    'campo4' => $request['campo4'],
                    'campo5' => $request['campo5'],
                    'campo6' => $request['campo6'],
                    'campo7' => $request['campo7'],
                    'campo8' => $request['campo8'],

                    'id_usuariomod' => $idusuario,

                    'ip_modificacion' => $ip_cliente,

                ];

                $orden_d->update($input);

            return "ok";
        }

    }

    public function existe($hcid, $id){

        $orden = Orden_Doctor::where('hcid',$hcid)->first();
        //return $orden->id;
        if(!is_null($orden)){
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor',$orden->id)->where('id_tipo_detalle_orden',$id)->first();
            //return $detalle
            if(is_null($detalle)){
                return '0';
            }
        }
        return '1';
    }

    public function crear_detalle($hcid, $id){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid',$hcid)->first();
        //return $orden->id;
        if(!is_null($orden)){
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor',$orden->id)->where('id_tipo_detalle_orden',$id)->first();
            //return $detalle
            if(is_null($detalle)){
                $input = [
                    'id_tipo_detalle_orden' => $id,
                    'id_orden_doctor' => $orden->id,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    ];

                Orden_Doctor_Detalle::create($input);

            }
        }
        return 'ok';
    }

    public function eliminar($hcid, $id){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid',$hcid)->first();
        //return $orden->id;
        if(!is_null($orden)){
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor',$orden->id)->where('id_tipo_detalle_orden',$id)->first();
            //return $detalle
            if(!is_null($detalle)){
                $detalle->delete();

            }
        }
        return 'ok';
    }

    //Nueva Funcionalidad Historial de Ordenes de Procedimiento: Endoscopico, Funcional, Imagenes
    public function historial_ordenes($id_paciente){
        $paciente = Paciente::findOrFail($id_paciente);
        $listado_ordenes = Orden::with(array(
                                'doctor',
                                'orden_tipo.orden_procedimiento.procedimiento',
                            ))
                            ->where('id_paciente', $id_paciente)
                            ->where('estado', 1)
                            ->orderBy('id', 'desc')
                            ->get();
        $seguro = Seguro::find($paciente->id_seguro);
        $edad = $paciente->fecha_nacimiento
            ? Carbon::parse($paciente->fecha_nacimiento)->age
            : null;
        $view = 'v3.clinical.patient_orders.history';
        $payload = compact('listado_ordenes', 'paciente', 'seguro', 'edad');
        $data = array(
            'controlador' => 'historial-ordenes',
            'carpeta' => 'clinical',
            'submodulo' => 'index',
            'submodulo_' => 'index',
            'menu_id' => 688,
            'titulo' => 'Historial de órdenes de procedimientos',
            'title' => 'Historial de órdenes de procedimientos | Aitrol Hospitalario',
            'vista' => $view,
            'obseraudi' => 'Consulta V3 del historial de órdenes de procedimientos del paciente',
            'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge((array) config('data'), $data)));

        return view('v3.layouts.view_child');
    }

    //FUNCION IMPRIMIR ORDEN DE PROCEDIMIENTO
    //SOLO PARA DOCTORES
    public function imprimir_orden_hc3($id){


        $orden_proc = Orden::findOrFail($id);

        // Las órdenes creadas desde la opción del médico tienen un PDF
        // específico según su tipo. Conservamos esta ruta heredada, pero la
        // dirigimos al mismo documento que imprime el médico para evitar que
        // existan dos formatos distintos para una misma orden.
        $rutasPdfMedico = array(
            0 => 'imprimir.ordenhc4_endoscopica',
            1 => 'imprimir.ordenhc4_funcional',
            2 => 'imprimir.ordenhc4_imagenes',
        );
        $tipoProcedimiento = (int) $orden_proc->tipo_procedimiento;
        if (isset($rutasPdfMedico[$tipoProcedimiento])) {
            return redirect()->route($rutasPdfMedico[$tipoProcedimiento], array('id_orden' => $orden_proc->id));
        }

        if((is_null($orden_proc->check_doctor))&&(is_null($orden_proc->id_doctor_firma))){

          $doctor_firma = $orden_proc->id_doctor;

        }else{

          $doctor_firma = $orden_proc->id_doctor_firma;

        }

        if (!is_null($orden_proc)) {
                $firma = Firma_Usuario::where('id_usuario',$doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc->id_paciente);

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        //$id_doctor = Auth::user()->id;
        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$orden_proc->id_doctor)
                              ->first();

        $vistaurl="hc_admision.orden_proc.ordenes_proced_pdf";
        $view =  \View::make($vistaurl, compact('orden_proc','paciente','edad','doctor_solicitante','firma'))->render();

        /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-'.$id.'.pdf');
      }


    //HISTORIAL ORDENES DE LABORATORIO
    public function historial_ordenes_Laboratorio($id_paciente){

        /*if($this->rol()){
            return response()->view('errors.404');
        }*/

        $paciente = Paciente::find($id_paciente);

        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();

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
                   ->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.id as id_paciente','p.parentesco as parentesco','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo')
                   ->OrderBy('created_at', 'desc')
                   ->get();

        return view('hc_admision/orden_proc/listado_ordenes_laboratorio',['ordenes' => $ordenes, 'usuarios' => $usuarios,'paciente' => $paciente]);

    }

    public function historial_ordenes_paciente(){
        /*if($this->rol_paciente()){
            return response()->view('errors.404');
        }*/
        $id_paciente = Auth::user()->id;

        $paciente = Paciente::find($id_paciente);

        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();

        $ordenes = DB::table('examen_orden as eo')
                   ->join('paciente as p','p.id','eo.id_paciente')
                   ->join('users as usuario','usuario.id','p.id_usuario')
                   ->where('usuario.id',$id_paciente)
                   ->join('seguros as s','s.id','eo.id_seguro')
                   ->leftjoin('empresa as em','em.id','eo.id_empresa')
                   ->leftjoin('nivel as n','n.id','eo.id_nivel')
                   ->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')
                   ->leftjoin('users as d','d.id','eo.id_doctor_ieced')
                   ->join('users as cu','cu.id','eo.id_usuariocrea')
                   ->join('users as mu','mu.id','eo.id_usuariomod')
                   ->where('eo.estado','1')
                   ->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.id as id_paciente','p.parentesco as parentesco','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo')
                   ->OrderBy('created_at', 'desc')
                   ->get();

        return view('hc_admision/orden_proc/listado_ordenes_laboratorio',['ordenes' => $ordenes, 'usuarios' => $usuarios,'paciente' => $paciente]);
    }

    public function carga_012($hcid){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $evolucion = Hc_Evolucion::where('hcid',$hcid)->where('secuencia','0')->first();
        $historiaclinica = Historiaclinica::find($hcid);
        $agenda = Agenda::find($historiaclinica->id_agenda);
        $fecha_agenda = substr($agenda->fechaini,0,10);
        $doctores = User::where('estado','1')->where('id_tipo_usuario','3')->OrderBy('apellido1','asc')->get();
        $seguros = Seguro::where('inactivo','1')->OrderBy('nombre','asc')->get();
        $empresas = Empresa::where('estado','1')->where('id','<>','9999999999')->OrderBy('nombrecomercial','asc')->get();
        //dd($seguros);

        if(!is_null($historiaclinica)){
            $paciente = Paciente::find($historiaclinica->id_paciente);
            if(!is_null($evolucion)){
                $ordenes = Orden::where('id_paciente',$paciente->id)->whereBetween('fecha_orden',[$fecha_agenda.' 0:00:00', $fecha_agenda.' 23:59:59'])->where('estado','1')->get();
                if($ordenes->count()>0){
                    $descripcion = '';
                    $cont = '0';
                    foreach ($ordenes as $orden) {
                        $ordenes_tipo = $orden->orden_tipo;
                        foreach ($ordenes_tipo as $ot) {
                            foreach ($ot->orden_procedimiento as $op) {
                                if($cont=='0'){
                                    $descripcion = $op->procedimiento->nombre;
                                    $cont ++;
                                }else{
                                    $descripcion = $descripcion.' + '.$op->procedimiento->nombre;
                                }


                            }

                        }

                    }
                    //return $descripcion;
                    //dd($ordenes[0]);
                    $orden_012 = Orden_012::whereBetween('fecha_orden',[$fecha_agenda.' 0:00:00', $fecha_agenda.' 23:59:59'])->where('estado','1')->where('id_paciente',$historiaclinica->id_paciente)->first();
                    if(is_null($orden_012)){
                        $arr = [
                            'id_paciente' => $paciente->id,
                            //'referido' => $paciente->referido,
                            'id_doctor_solicita' => $ordenes[0]->id_doctor,
                            'id_doctor_firma' => $ordenes[0]->id_doctor,
                            'fecha_orden' => substr($agenda->fechaini,0,10),
                            'id_hc_evolucion' => $evolucion->id,
                            'estado' => '1',
                            'descripcion' => $descripcion,
                            'motivo' => $ordenes[0]->motivo_consulta,
                            'cuadro_clinico' => $ordenes[0]->resumen_clinico,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,

                        ];
                        //return $arr;
                        $id_012 = Orden_012::insertGetId($arr);
                        $orden_012 = Orden_012::find($id_012);

                        $cie10 = Hc_Cie10::where('hcid',$historiaclinica->hcid)->groupBy('cie10')->get();
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

                    $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012',$orden_012->id)->get();

                    return view('hc_admision/orden_012/ingreso_012',['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10]);
                }
                return "vacio";
            }
        }

        return "no encontro";
    }

    public function actualizar(Request $request ){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $orden_012 = Orden_012::find($request->id_orden);
        if(!is_null($orden_012)){
            $arr = [
                'id_seguro' => $request->id_seguro,
                'id_empresa' => $request->id_empresa,
                'id_doctor_firma' => $request->id_doctor_examinador,
                'descripcion' => $request->descripcion,
                'motivo' => $request->motivo,
                'cuadro_clinico' => $request->historia_clinica,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];

            $orden_012->update($arr);

        }

        return 'ok';

    }

    public function carga_012_c10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $input2 = [
            'id_orden_012'          => $request['id_orden'],
            'cie10'                 => $request['codigo'],
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Orden_012_Cie10::insertGetId($input2);

        $count = Orden_012_Cie10::where('id_orden_012', $request['id_orden'])->get()->count();

        $cie10 = Orden_012_Cie10::find($id);

        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }


    public function c012_c10eli($id)
    {

        $c10 = Orden_012_Cie10::find($id);
        $c10->delete();
        return 'ok';
    }

    public function imprimir_012($id)
    {

        $orden_012 = Orden_012::find($id);
        $paciente = Paciente::find($orden_012->id_paciente);
        $id_empresa = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
        $id_seguro = $orden_012->evolucion->procedimiento->id_seguro;
        if($id_seguro!=null){
            $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
        }
        $empresa = Empresa::find($id_empresa);
        $seguro = Seguro::find($id_seguro);
        $doctor = User::find($orden_012->id_doctor_firma);
        $cie10 = Orden_012_Cie10::where('id_orden_012',$orden_012->id)->get();


        $age = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();


        $view =  \View::make('hc_admision.orden_012.orden012_pdf', compact('orden_012', 'age', 'paciente', 'empresa', 'seguro', 'doctor', 'cie10', 'firma'))->render();
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);


        return $pdf->stream('orden_doctor_'.$paciente->id_paciente.'.pdf');



    }

    public function imprimir_012_excel($id)
    {
        return app(\Sis_medico\Services\ClinicalLegacyOrder012ExportService::class)->export($id);
    }

    public function excel_053_nuevo($id)
    {
        return app(\Sis_medico\Services\ClinicalLegacyOrder053ExportService::class)->export($id);
    }

    public function pdf_053_nuevo($id)
    {
        return app(\Sis_medico\Services\ClinicalLegacyOrder053ExportService::class)->exportPdf($id);
    }

    //HISTORIAL ORDENES DE LABORATORIO
    public function historial_ordenes_biopsias($id_paciente){

        //dd($id_paciente);
        $biopsias = Hc4_Biopsias::where('id_paciente',$id_paciente)->groupBy("hc_id_procedimiento")->get();

        $paciente = Paciente::find($id_paciente);

        /*if($biopsias->count()>0){

            foreach ($biopsias as $biop){

                $orden_biopsia = Biopsias::where('hc_id_procedimiento',$biop->hc_id_procedimiento)->get();
                //dd($orden_biopsia);


            }
        }*/

        return view('hc_admision/orden_proc/historial_ordenes_biopsias',['biopsias' => $biopsias,'paciente' => $paciente]);

    }

    //Imprimir Orden de Biopsias Recepcion
    public function imprime_orden_biopsia($id, $id_hcid, $id_doct)
    {


        $hc_proced = hc_procedimientos::find($id); 

        $count_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get()->count();

        if (($count_biospsias > 0) && (!is_null($hc_proced))) {

            $historia = historiaclinica::find($id_hcid);
            $paciente = paciente::find($historia->id_paciente);

            if ($paciente->fecha_nacimiento != null) {
                $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            }

            $biop_first = Hc4_Biopsias::where('hc_id_procedimiento', $id)->first();

            $impr_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get();

            $doctor_solicitante = DB::table('users as us')
                ->where('us.id', $id_doct)
                ->first();

            $vistaurl = "hc4.procedimiento_endoscopico.pdf_orden_biopsias";
            $view     = \View::make($vistaurl, compact('historia', 'paciente', 'edad', 'impr_biospsias', 'doctor_solicitante', 'biop_first','hc_proced'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream($historia->id_paciente . '_Biopsias_' . $id . '.pdf');

        }


    }


    public function historial_biopsias_nueva($id_paciente){
        $paciente = Paciente::find($id_paciente);
        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '0')
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'h.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos','hc_proto.motivo as mprotocolo')->OrderBy('a.fechaini', 'desc')->get();

            //nuevo
            $arrayBiopsias = [];
            foreach ($pro_final_0 as $key => $value) {
              $hc_procedimiento   = hc_procedimientos::find($value->id_procedimiento);
              $ptv_biopsias       = $hc_procedimiento->ptv_biopsias->where('estado','1');
              if(count($ptv_biopsias)){
                array_push($arrayBiopsias,$ptv_biopsias);
              }
            }
        return view('hc4/procedimiento_endoscopico/nueva_biopsia',['paciente'=>$paciente,"biopsias"=>$arrayBiopsias,'pro_final'=>$pro_final_0]);
    }

    public function historial_biopsias_impresion(Request $request){
        $biopsia_ptv = Hc4_Biopsias_Ptv::find($request['id_procedimiento']);
        try {
          $biopsia_ptv->fecha_imprimible = $request['fecha'];
          $biopsia_ptv->save();
          return ["status"=>"true", "msj"=>"La fecha imprimible se edito correctamente"];
        } catch (\Exception $e) {
            return ["status"=>"error", "msj"=>"Error al editar", "error"=>$e->getMessage()];
        }
    }
}
