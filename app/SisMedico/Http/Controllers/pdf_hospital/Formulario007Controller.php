<?php

namespace Sis_medico\Http\Controllers\pdf_hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Schema;
use Sis_medico\Paciente;
use Sis_medico\Empresa;
use Carbon\Carbon;
use Sis_medico\Examen;
use Sis_medico\Protocolo;
use Sis_medico\Hosp_dato_paciente;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hosp_atencion_fomulario008;
use Sis_medico\Hosp_revision_formulario008;
use Sis_medico\Hosp_accidente_formulario008;
use Sis_medico\Hosp_antecedentes_formulario008;
use Sis_medico\Hosp_signos_vitales_formulario008;
use Sis_medico\Hosp_obstetrica_formulario008;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hosp_tratamiento_formulario008;
use Sis_medico\Hosp_formulario008_alta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Seguro;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\User;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Agenda;
use Sis_medico\Sala;
use Sis_medico\Hc_Log;
use Sis_medico\Log_Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta;
use Sis_medico\Ho_Form008;
use Sis_medico\hc_child_pugh;
use Sis_medico\Ho_Glasgow;
use Sis_medico\Ho_Lesiones008;
use Sis_medico\Ho_Traspaso_Sala008;
use PDF;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Hc_Cie10;
use Sis_medico\Orden_012;
use Sis_medico\Ho_form008_diagnostico;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Log_usuario;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Sis_medico\Archivo_historico;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Documento;
use Sis_medico\Examen_Orden;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Interconsulta;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_anestesiologia_csv;
use Sis_medico\hc_protocolo_cirugia;
use Sis_medico\Pentax;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Epicrisis;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Anamnesis;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\HcFormulario013;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Especialidad;
use Sis_medico\Hc_Evolucion_Anestesiologo;

class Formulario007Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function formulario007($id)
    {

        $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $id)->get();
        $interconsulta = Interconsulta::find($id);
        //dd($interconsulta->historia->evoluciones);

        $paciente = $interconsulta->paciente;
        $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;

        $empresa = Empresa::where('prioridad', 1)->get()->first();


        $vistaurl = "pdf_hospital.formulario007";
        $view = \View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario007.pdf' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
        exit(0);
        //return view('pdf_hospital.formulario001');
    }

    public function informe007($id)
    {

        $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $id)->get();
        $interconsulta = Interconsulta::find($id);

        $paciente = $interconsulta->paciente;
        $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;

        $empresa = Empresa::where('prioridad', 1)->get()->first();


        $vistaurl = "pdf_hospital.informe007";
        $view = \View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('informe.pdf' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
        exit(0);
        //return view('pdf_hospital.formulario001');
    }

    public function formulario005()
    {

        $view = View::make('pdf_hospital.formulario005')->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario005.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function formulario12B($id_protocolo, Request $request)
    {
        
        $xtipo      = $request->xtipo;

        $protocolo = hc_protocolo::find($id_protocolo);
        
        if($xtipo == 'auditoria'){

            $protocolo = Aud_Hc_Protocolo::find($id_protocolo);
        }

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $firma = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->first();
        //dd($firma);

        $view = View::make('pdf_hospital.formulario12B',compact('protocolo','empresa','firma'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario12B.pdf');
        exit(0);
        //return view('pdf_hospital.formulario001');
    }

    public function formulario12A(Request $request)
    {

        $variables = Orden_012::find($request->id);
        //dd($variables);
        $cie10   = Orden_012_Cie10::where('id_orden_012', $request->id)->get();
        $view = View::make('pdf_hospital.formulario12A', compact("variables", "cie10"))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario012.pdf');
        //return view('pdf_hospital.formulario001');
    }

    
    public function formulario006($id,Request $request){
        $procedimiento = null;
        $firma         = null;
        $xtipo      = $request->xtipo;

        $epicrisis_original = null;

        if($xtipo == 'auditoria'){

            $epicrisis = Aud_Hc_Epicrisis::find($id);//dd($epicrisis);
            $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
            $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first();//dd($protocolo_original, $hc_procedimiento);
        
        }else{
            
            $epicrisis  = Hc_Epicrisis::find($id);
            $hc_procedimiento = hc_procedimientos::where('id', $epicrisis->hc_id_procedimiento)->first();
        
        }
        //dd($xtipo,$epicrisis,$epicrisis_original);
        

        //$epicrisis = Hc_Epicrisis::find($id);
        //dd($epicrisis->id);
        $protocolo = hc_protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        
        if($xtipo == 'auditoria'){

            $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        }

        /*$historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();*/
        //dd($historiaclinica);
        $historiaclinica = Historiaclinica::where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
        //dd($cie10);
        $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();
      
       
        //$cie10_3 = Cie_10_3::all();
        //$cie10_4 = Cie_10_4::all();
        if (!is_null($protocolo)) {
            $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
            
        }else{
            $procedimiento = hc_procedimientos::where('id_hc',$historiaclinica->hcid)->first();    
        }

        if (!is_null($procedimiento)) {

            $id_seguro  = $procedimiento->id_seguro;
            $id_empresa = $procedimiento->id_empresa;
            if ($procedimiento->id_seguro == null) {
                //$procedimiento->update(['id_seguro' => $historiaclinica->id_seguro]);
                $id_seguro = $historiaclinica->id_seguro;
            }
            if ($procedimiento->id_empresa == null) {
                //$procedimiento->update(['id_empresa' => $historiaclinica->id_empresa]);
                $id_empresa = $historiaclinica->id_empresa;
            }
            $xseguro = Seguro::find($id_seguro);
            if ($procedimiento->id_doctor_responsable == null) {
                //$firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
            } else {
                //$firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_responsable)->first();
            }
            if ($xseguro->tipo == '0') {
                if ($id_empresa == '1307189140001') {
                    //$firma = Firma_Usuario::where('id_usuario', '1307189140')->first();
                }
                if ($id_empresa == '0992704152001') {
                    if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                        //$firma = Firma_Usuario::where('id_usuario', '094346835')->first();
                    }
                }
            }

        }
        //dd($procedimiento);
        $tiene_receta = 'NO';
        $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
        if (!is_null($receta)) {
            $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
            if ($receta_det > 0 || $receta->prescripcion != null) {
                $tiene_receta = 'SI';
            }
        }
        $tiene_receta = $epicrisis->receta;

        //dd($firma);
        $nombre_doc = "";
        if (!is_null($firma)) {
            //$nombre_doc = User::find($firma->id_usuario);
        }

        $doctor = "";
        if (!is_null($procedimiento)) {

            if ($procedimiento->id_doctor_responsable == null) {
                $doctor = User::find($procedimiento->id_doctor_examinador2);

            } else {
                $doctor = User::find($procedimiento->id_doctor_responsable);

            }
            if ($xseguro->tipo == '0') {
                if ($id_empresa == '1307189140001') {
                    $doctor = User::find('1307189140');
                }
                if ($id_empresa == '0992704152001') {
                    if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                        $doctor = User::find('094346835');
                    }
                }
            }

        }
        if($doctor == ""){
            $doctor = $historiaclinica->doctor_1;    
        }
        if($doctor != ""){
            $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
        }
         

        $data = $historiaclinica;
        $receta = hc_receta::where('id_hc', $data->hcid)->first();
        $view = View::make('pdf_hospital.formulario006', compact('receta', 'cie10ca', 'cie10', 'cie10e', 'data', 'epicrisis', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento', 'epicrisis_original'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('HCU_006' . '.pdf');
    }
    public function formulario10B()
    {
        $view = View::make('pdf_hospital.formulario10B')->render();
        $pdf  = App::make('dompdf.wrapper');
        //$pdf->setPaper('A4');
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario10B.pdf');
        //return view('pdf_hospital.formulario001');
    }
    public function formulario10A($id)
    {
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $examenes      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor= Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','24')->orderBy('a10.id_agrupador')->get();
        
        
        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        //$orden         = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'p.fecha_nacimiento as pfecha_nacimiento','p.sexo as psexo', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2')->first();
        $orden         = Examen_Orden::findOrFail($id);
        //$detalles      = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $detalles      = $orden->detalles;

        $seguro     = $orden->id_seguro ? Seguro::find($orden->id_seguro) : null;
        $protocolos = Protocolo::where('estado', '1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        //dd($orden);
        $arreglo    = [];
        $arreglo1   = [];
        $seleccionados = [];
        $nro        = 0;
        $agrupa_ant = 0;
        foreach ($examenes as $examen) {

            //dd($examen);
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;

            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }

        $view = View::make('pdf_hospital.formulario10A', compact( 'orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 
            'arreglo', 'seleccionados','hematologia','quimica','inmunologia','heces','tumorales','citologia','cardiacos','hormonas','farmacos','serologia','biologia',
            'coagulacion','orina','inmunosupresor','gases','trasfusional','vih'
            ))->render();
        //return view('pdf_hospital.formulario10A', compact('arrayTotal', 'orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo', 'seleccionados'));
        $pdf  = App::make('dompdf.wrapper');
        //$pdf->setPaper('A4');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHTML($view);
        
        return $pdf->stream('formulario10A.pdf');
        //return view('pdf_hospital.formulario001');
    }
    public function formulario017_hospital($id_hc_protocolo)
    {
        //dd($id_hc_protocolo);



        $hc_protocolo = hc_protocolo::with([
            'procedimiento.doctor',
            'historiaclinica.agenda.doctor1',
        ])->findOrFail($id_hc_protocolo);

        
        $cie10i = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $hc_protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
      

        $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $hc_protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();

        //dd($hc_protocolo);
        $cirugia = hc_protocolo_cirugia::where('id_hc_protocolo', $hc_protocolo->id)->first();

        $doctor = optional($hc_protocolo->procedimiento)->doctor;
        if (is_null($doctor)) {
            $doctor = optional(optional($hc_protocolo->historiaclinica)->agenda)->doctor1;
        }

       

        //dd($id_hc_protocolo->procedimiento);

        $view = View::make('pdf_hospital.formulario017_hospital', compact('hc_protocolo', 'cirugia', 'cie10i', 'cie10e', 'doctor'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario017.pdf');
        //return view('pdf_hospital.formulario001');
    }
    public function formulario017(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $xtipo      = $request->xtipo;
        
        $protocolo_original = null;

        $empresa = Empresa::where('prioridad', 1)->first();
        if($xtipo == 'auditoria'){

            $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
            $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
            $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first();//dd($protocolo_original, $hc_procedimiento);
        
        }else{
            
            $protocolo  = hc_protocolo::find($request->protocolo);
            $hc_procedimiento = hc_procedimientos::where('id', $protocolo->id_hc_procedimientos)->first();
        
        }
        
        

        $pxs = $hc_procedimiento->hc_procedimiento_f;//dd($pxs);
        $proc = null;$imagen = null;
        foreach($pxs as $px){
            //dd($px);
            if(!is_null($px->procedimiento->id_grupo_procedimiento)){
                $proc = $px->procedimiento;
            }
        }//dd($proc);
        if($proc != null){
            $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
        }        
       
        $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();

        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }

        if ($xtipo == 'auditoria') {

            $input = [
                'fecha_operacion'     => $request->fecha_operacion,
                'hora_inicio'         => $request->hora_ini,
                'hora_fin'            => $hora_fin,
                'tipo_anestesia'      => $request->tipo_anestesia,
                'intervalo_anestesia' => $duracion,
                'id_anestesiologo'    => $request->id_anestesiologo,

            ];

            $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
        } else {

            $input = [
                'fecha_operacion'     => $request->fecha_operacion,
                'hora_inicio'         => $request->hora_ini,
                'hora_fin'            => $hora_fin,
                'tipo_anestesia'      => $request->tipo_anestesia,
                'intervalo_anestesia' => $duracion,
                'id_anestesiologo'    => $request->id_anestesiologo,
                'descripcion'         => $request->descripcion,
                'muestra'             => $request->muestra,

            ];

            $cie10 = Hc_Cie10::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
        }

        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];
        $protocolo->procedimiento->update($input2);




        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
        //dd($orden_biopsias);


        $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_017.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function formulario018A($procedimiento_id)
    {
        $record = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $procedimiento_id)->first();

        $view = View::make('pdf_hospital.formulario018A', compact('record'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario018A.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function formulario018()
    {

        $view = View::make('pdf_hospital.formulario018')->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario018.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function formulario()
    {
        $view = View::make('pdf_hospital.formulario')->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario.pdf');
        //return view('pdf_hospital.formulario001');
    }


    public function bitacora($id)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id);
        $procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
        //dd($procedimiento->historia->agenda->paciente);

        $view = View::make('pdf_hospital.bitacora', compact('archivo_plano', 'procedimiento'))->render();
        $pdf  = App::make('dompdf.wrapper');
        // $pdf->setPaper('A4');
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('vitacora.pdf');
      
    }
    public function formulario002($id,Request $request)
    {
        //dd($id);
        $anamnesis = Anamnesis::find($id);
        $xtipo      = $request->xtipo;
        //dd($anamnesis->historiaclinica->evoluciones[0]->indicaciones);
        //dd($anamnesis);
        if($xtipo == 'auditoria'){
        
            $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hcid',  $anamnesis->hcid)->get();

        }    
        else {

            $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anamnesis->hcid)->get();

        }

        
        
        $procedimiento = hc_procedimientos::where('id_hc', $anamnesis->hcid)->first();
        //dd($procedimiento);
        $doctor = "";
        if (!is_null($procedimiento)) {

            if ($procedimiento->id_doctor_responsable == null) {
                $doctor = User::find($procedimiento->id_doctor_examinador2);

            } else {
                $doctor = User::find($procedimiento->id_doctor_responsable);

            }
        }
        if($doctor == ""){
            $doctor = $anamnesis->historiaclinica->doctor_1;
        }
        $view = View::make('pdf_hospital.formulario002',compact('anamnesis','cie10','doctor'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario002.pdf');
        //return view('pdf_hospital.formulario001');
    }
    public function crear_anamnesis($hcid)
    {
        //dd($hcid);



        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $anamnesis = Anamnesis::where('hcid', $hcid)->where('estado', '1')->first();//dd($anamnesis);

        $historiaclinica = Historiaclinica::find($hcid);
       
       
        if (is_null($anamnesis)) {

            $motivo_evolucion= '';
            $problema_actual= '';
            $indicacion_evolucion= '';
            
            if(count($historiaclinica->evoluciones) > 0){

                $evoluciones = $historiaclinica->evoluciones;
                $evolucion = $evoluciones[0];

                $motivo_evolucion= $evolucion->motivo;
                $problema_actual= $evolucion->cuadro_clinico;
                $indicacion_evolucion= $evolucion->indicacion;

            }

            $arr_anamnesis = [

                'hcid' => $hcid,
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'motivo_evolucion'      => $motivo_evolucion,
                'problema_actual'       => $problema_actual,
                'indicacion_evolucion'  => $indicacion_evolucion,
            ];
            $id_an = Anamnesis::insertGetId($arr_anamnesis);
            $anamnesis = Anamnesis::find($id_an);
        } 
        //$this->actualizar_evolucion_anamnesis($anamnesis->id);

        
        return redirect(route('formato.anamenesis_index', ['anamnesis' => $anamnesis ]));
        
       
    }

    public function anamenesis_index($id)
    {
        $anamnesis = Anamnesis::find($id);
        //dd($anamnesis);
        $anamnesis =$this->actualizar_evolucion_anamnesis($id);

        return view('anamenesis/anamenesis', compact('id','anamnesis'));
    }


    public function anamenesis_guardar(Request $request)
    {
        
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id = $request->input('id');
        $valores = $request->except('id'); 
    
        
        $anamnesis = Anamnesis::find($id);

    
        if ($anamnesis) {
          
            foreach ($valores as $nombre => $valor) {
                
                if (Schema::hasColumn('anamnesis', $nombre)) {
                    
                    $anamnesis->$nombre = $valor;
                } else {
                    
                    if (Schema::hasColumn('historiaclinica', $nombre)) {
                        
                        $historiaclinica = Historiaclinica::where('hcid', $anamnesis->hcid)->first();
                       
                        if ($historiaclinica) {
                            
                            $valorHistoriaclinica = $historiaclinica->$nombre;
                            
                            if ($valor != $valorHistoriaclinica) {
                                $historiaclinica->$nombre = $valor;
                                $historiaclinica->save();
                            }
                        }
                    }
                }
            }
            
            $anamnesis->id_usuariomod = $idusuario;
            $anamnesis->ip_modificacion = $ip_cliente;
            $anamnesis->save();
        } else {
           
            $arr_anamnesis = [
                'estado'            => 1,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
           
            $anamnesis = Anamnesis::create(array_merge($valores, $arr_anamnesis));
        }
    
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }
    public function anamenesis_guardar_evo(Request $request)
    {
        
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id = $request->input('id');
 
        
        $anamnesis = Anamnesis::find($id);
        //dd($anamnesis);
        $historiaclinica = Historiaclinica::where('hcid',$anamnesis->hcid)->first();

        if ($anamnesis) {
            $arr_anamnesis = [
                'motivo_evolucion'      => $request['motivo_evolucion'],
                'problema_actual'       => $request['problema_actual'],
                'indicacion_evolucion'  => $request['indicacion_evolucion'],

                'frecuencia_respiratoria'  => $request['frecuencia_respiratoria'],
                'imc_kg'  => $request['imc_kg'],
                'perimetro_abdominal'  => $request['perimetro_abdominal'],
                'hemoglobina_capilar'  => $request['hemoglobina_capilar'],
                'glucosa_capilar'  => $request['glucosa_capilar'],
                'pulsioximetri'  => $request['pulsioximetri'],
                'estado'            => 1,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
            $anamnesis->update($arr_anamnesis);
           
            $arr_historiac = [
                
                'fecha_atencion'  => $request['fecha_atencion'],
                'temperatura'  => $request['temperatura'],
                'presion'  => $request['presion'],
                'altura'  => $request['altura'],
                'peso'  => $request['peso'],
            ];
            $historiaclinica->update($arr_historiac);
        }
    
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }

    public function modal_formatos_24( $id, Request $request ){

        $tipo = $request->tipo;

        

        if($tipo == 'auditoria'){
            $protocolo = Aud_Hc_Protocolo::find($id);
            $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
            $anamesis   = null;
            if($protocolo != null){
                $anamesis  = Anamnesis::where('hcid',$protocolo_original->hcid)->first();
                $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento',$protocolo_original->id_hc_procedimientos)->first();
                
            }
            $biopsia = null;
            $infobiopsia = null;
            $agenda = null;
            $solicitud = null;
        }else{
            $protocolo  = hc_protocolo::find($id);
            $anamesis   = null;
            if($protocolo != null){
                $anamesis  = Anamnesis::where('hcid',$protocolo->hcid)->first();
                $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento',$protocolo->id_hc_procedimientos)->first();
                $biopsia   = HcFormulario013::where('id_hc_procedimiento',$protocolo->id_hc_procedimientos)->first();
                $infobiopsia    =  Hc_informe_biopsia::where('id_hc_procedimiento',$protocolo->id_hc_procedimientos)->first();
                $agenda = $protocolo->historiaclinica->agenda;
                $solicitud = Ho_Solicitud::where('id_agenda',$agenda->id)->where('estado','1')->first(); 
            }
            
        }
  
        return view('pdf_hospital.modal_formularios',['anamesis' => $anamesis, 'epicrisis' => $epicrisis, 'protocolo' => $protocolo, 'tipo' => $tipo, 'biopsia' => $biopsia, 'infobiopsia' => $infobiopsia, 'solicitud' => $solicitud]);

    }
    public function formulario013B($id)
    {

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        

       $bioxia =  Hc_informe_biopsia::where('id_hc_procedimiento',$id)->first();
       //dd($bioxia->historia->id_doctor1);

       $doctor = User::find($bioxia->historia->id_doctor1);
        
       $firma = Firma_Usuario::where('id_usuario', $doctor->id)->first();



        $view = View::make('pdf_hospital.formulario13B',compact('bioxia','empresa','firma'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario13B.pdf');
    }
    public function informe_bioxias_crear($hc_procedimiento_id)
    {
        //dd($hc_procedimiento);


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $bioxia = Hc_informe_biopsia::where('id_hc_procedimiento', $hc_procedimiento_id)->where('estado', '1')->first();

        $hc_procedimientos = hc_procedimientos::where('id', $hc_procedimiento_id)->where('estado', '1')->first();
        //dd($hc_procedimientos);

        $id_hc = Historiaclinica::find($hc_procedimientos->id_hc);

        
        //dd($id_hc->hcid);


        if (is_null($bioxia)) {

            $arr_infome_bioxia = [

                'id_hc_procedimiento' => $hc_procedimiento_id,
                'hcid'                => $id_hc->hcid,
                'id_paciente'         => $id_hc->paciente->id,
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $id_inf = Hc_informe_biopsia::insertGetId($arr_infome_bioxia);
            $bioxia = Hc_informe_biopsia::find($id_inf);
        }

        return redirect(route('pdf_hospital.informe_bioxias_edit', ['hc_procedimiento_id' => $hc_procedimiento_id, 'bioxia' => $bioxia]));
    }
    public function informe_bioxias_edit($id)

    {
        $texto = "";
        //dd($id);
        $infBioxia = Hc_informe_biopsia::where('id_hc_procedimiento', $id)->first();
        //dd($infBioxia);
        $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id)->get();
        $mas = true;
        foreach($adicionales as $value2)
        {
            if($mas == true){
                $texto = $texto.$value2->procedimiento->nombre  ;
                $mas = false;
                }
            else{
                $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                }
        }
        
        
        return view('pdf_hospital.informe_bioxia_edit', ['infBioxia' => $infBioxia, 'id' => $id, 'procedimiento' => $texto]);
    }


    public function informe_bioxias_guardar(Request $request)
    {

        //dd($request->all());
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $hcid = $request->input('hcid');
        //dd($id_hc);
        $hc_informe_biopsia = Hc_informe_biopsia::where('hcid', $hcid)->first();

        if ($hc_informe_biopsia) {
            $valores = $request->except(['hcid', '_token']);
            $updatedData = array_merge($valores, [
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($updatedData as $nombre => $valor) {
                if (Schema::hasColumn('hc_informe_biopsia', $nombre)) {
                    $hc_informe_biopsia->$nombre = $valor;
                }
            }

            $hc_informe_biopsia->save();
        } else {
            $valores = $request->except('_token');
            $arr_bioxia = [
                'hcid' => $hcid,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $hc_informe_biopsia = Hc_informe_biopsia::create(array_merge($valores, $arr_bioxia));
        }

        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }
    public function pdf_019 ($hc_id_procedimiento)
    {
        $hc_procedimiento = hc_procedimientos::find($hc_id_procedimiento);
        if(!is_null($hc_procedimiento)){
            $historia = Historiaclinica::find($hc_procedimiento->id_hc);
            $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::Where('hc_id_procedimiento',$hc_id_procedimiento)->first();
            if(!is_null($historia)){
                $paciente = Paciente::find($historia->id_paciente);
                $agenda = Agenda::find($historia->id_agenda);
                $especialidad = Especialidad::find($agenda->espid);
                $empresa = Empresa::find($agenda->id_empresa);
                $doctor = User::find($agenda->id_doctor1);
                $cirugia = '';
                $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos',$hc_id_procedimiento)->get();
                $anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos',$hc_id_procedimiento)->first();
                $postanestesicoImage = null;
                if ($anestesiologia && !empty($anestesiologia->post_anastesico_path)) {
                    $imageName = basename($anestesiologia->post_anastesico_path);
                    $imagePaths = [
                        public_path('assets/images/postanestesico/'.$imageName),
                        storage_path('app/hc_ima/trans/'.$imageName),
                    ];
                    foreach ($imagePaths as $imagePath) {
                        if (is_file($imagePath)) {
                            $postanestesicoImage = 'data:image/png;base64,'.base64_encode(file_get_contents($imagePath));
                            break;
                        }
                    }
                }
                //dd($anestesiologia);
                if(count($hc_procedimiento_final)>1){
                    $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                    for ($i=1; $i < count($hc_procedimiento_final); $i++) { 
                        $cirugia = $cirugia .' + '. $hc_procedimiento_final[$i]->procedimiento->nombre;
                    }
                }
                elseif(count($hc_procedimiento_final)==1){
                    $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                }
                $diagnostico = '';
                $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento',$hc_id_procedimiento)->get();
                foreach ($hc_cie10 as $value) {
                    $cie10 = Cie_10_3::find($value->cie10);
                    if(is_null($cie10)){
                        $cie10 = Cie_10_4::find($value->cie10);
                    }
                    $diagnostico = $diagnostico . '<br>' . $cie10->descripcion;
                }
                $firma = Firma_Usuario::where('id_usuario', $doctor->id)->first();
                // $hora = date('H:i', strtotime($fecha));
                // $fecha = date('Y-m-d', strtotime($fecha));
                $viewData = compact(
                    'evolucion_anestesiologo',
                    'agenda',
                    'historia',
                    'paciente',
                    'especialidad',
                    'empresa',
                    'doctor',
                    'cirugia',
                    'diagnostico',
                    'anestesiologia',
                    'firma',
                    'postanestesicoImage'
                );

                // Compatibilidad durante despliegues donde Composer todavía no
                // ha instalado FPDI: se conserva el PDF anterior en vez de responder 500.
                if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
                    $fallbackView = View::make('pdf_hospital.formulario019', array_merge($viewData, [
                        'pdfPage' => 'all',
                        'pdfPageNumber' => 1,
                    ]))->render();
                    $fallbackPdf = App::make('dompdf.wrapper');
                    $fallbackPdf->setOptions(['chroot' => base_path('/')]);
                    $fallbackPdf->setPaper('A4', 'landscape');
                    $fallbackPdf->loadHTML($fallbackView);

                    return $fallbackPdf->stream('formulario019.pdf');
                }

                $landscapeView = View::make('pdf_hospital.formulario019', array_merge($viewData, [
                    'pdfPage' => 'first',
                    'pdfPageNumber' => 1,
                ]))->render();
                $landscapePdf = App::make('dompdf.wrapper');
                $landscapePdf->setOptions(['chroot' => base_path('/')]);
                $landscapePdf->setPaper('A4', 'landscape');
                $landscapePdf->loadHTML($landscapeView);
                $landscapePdf->render();

                $portraitView = View::make('pdf_hospital.formulario019', array_merge($viewData, [
                    'pdfPage' => 'second',
                    'pdfPageNumber' => 2,
                ]))->render();
                $portraitPdf = App::make('dompdf.wrapper');
                $portraitPdf->setOptions(['chroot' => base_path('/')]);
                $portraitPdf->setPaper('A4', 'portrait');
                $portraitPdf->loadHTML($portraitView);
                $portraitPdf->render();

                $mergedPdf = new \setasign\Fpdi\Fpdi();
                $mergedPdf->SetAutoPageBreak(false);
                foreach ([$landscapePdf->output(), $portraitPdf->output()] as $pdfContent) {
                    $pageCount = $mergedPdf->setSourceFile(
                        \setasign\Fpdi\PdfParser\StreamReader::createByString($pdfContent)
                    );

                    for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                        $template = $mergedPdf->importPage($pageNumber);
                        $size = $mergedPdf->getTemplateSize($template);
                        $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                        $mergedPdf->AddPage($orientation, [$size['width'], $size['height']]);
                        $mergedPdf->useTemplate($template);
                    }
                }

                return response($mergedPdf->Output('S'), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="formulario019.pdf"',
                ]);
            }
        }
        //return view('pdf_hospital.formulario001');
    }
    public static function  actualizar_evolucion_anamnesis($id){
        $anamnesis = Anamnesis::find($id);//dd($anamnesis);

        $historiaclinica = $anamnesis->historiaclinica;
        
        $motivo_evolucion= '';
        $problema_actual= '';
        $indicacion_evolucion= '';
        
        if(count($historiaclinica->evoluciones) > 0){

            $evoluciones = $historiaclinica->evoluciones;
            $evolucion = $evoluciones[0];
            
            if(!is_null($evolucion)){
                $motivo_evolucion= $evolucion->motivo;
                $problema_actual= $evolucion->cuadro_clinico;
                $indicacion_evolucion= $evolucion->indicacion;
                $cont = 0;
                if($anamnesis->motivo_evolucion == null){
                    $cont ++;
                    $anamnesis->motivo_evolucion = $motivo_evolucion;    
                }
                if($anamnesis->problema_actual == null){
                    $cont ++;
                    $anamnesis->problema_actual = $problema_actual;    
                }
                if($anamnesis->indicacion_evolucion == null){
                    $cont ++;
                    $anamnesis->indicacion_evolucion = $indicacion_evolucion;    
                }
                if($cont > 0){
                    $anamnesis->save();
                }
            }

        }
        return $anamnesis;

    }
}
