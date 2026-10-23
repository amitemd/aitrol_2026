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
use DateTime;
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
use Sis_medico\rc_antecedentes;
use Sis_medico\rc_resultados;
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
use Sis_medico\rc_pre_anestesica;
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
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Especialidad;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\HcFormulario013;
use Sis_medico\Hc_cardio_cie10;
use Sis_medico\Interconsulta_Diagnostico;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\Hc4_Auditoria_Biopsias;
use Sis_medico\MaestroDeCodigo;
use Sis_medico\Informe_Tecnico;

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

        $xtipo = $request->xtipo;
        $firma = null;

        $protocolo = hc_protocolo::find($id_protocolo);
        if ($protocolo->procedimiento->doctor_firma != null) {
            $firma = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->first();
        }

        //dd($xtipo);
        if ($xtipo == 'auditoria') {

            $protocolo = Aud_Hc_Protocolo::find($id_protocolo);
            //dd($protocolo->auditoria_procedimiento);
            if ($protocolo->auditoria_procedimiento->doctor_firma != null) {
                $firma = Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->doctor_firma->id)->first();
                //dd($firma);
            }
        }

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        if ($id_protocolo == '42765') {
            //dd($protocolo->auditoria_procedimiento);
        }

        //dd($firma);

        $view = View::make('pdf_hospital.formulario12B', compact('protocolo', 'empresa', 'firma'))->render();
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

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $variables->evolucion->hcid)->get();
    

        $firma = Firma_Usuario::where('id_usuario', $variables->id_doctor_firma)->first();
        $view = View::make('pdf_hospital.formulario12A', compact("variables", "cie10", "firma"))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario12A.pdf');
        //return view('pdf_hospital.formulario001');
    }


    public function formulario006($id, Request $request)
    {
        $procedimiento = ""; 
        $firma         = "";
        $xtipo      = $request->xtipo;

        $epicrisis_original = null;

        if ($xtipo == 'auditoria') {

            $epicrisis = Aud_Hc_Epicrisis::find($id); //dd($epicrisis);
            $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
            $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first(); //dd($protocolo_original, $hc_procedimiento);

        } else {

            $epicrisis  = Hc_Epicrisis::find($id);
            $hc_procedimiento = hc_procedimientos::where('id', $epicrisis->hc_id_procedimiento)->first();
        }
        //dd($xtipo,$epicrisis,$epicrisis_original);


        //$epicrisis = Hc_Epicrisis::find($id);
        //dd($epicrisis->id);
        $protocolo = hc_protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();

        if ($xtipo == 'auditoria') {

            $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        }

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
        //dd($historiaclinica);

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
        //dd($cie10);
        $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();

        if ($xtipo == 'auditoria') {

            $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();
            //dd($cie10);
            $cie10e = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();
        }

        $firma = null;
        $doctor = null;

        if (!is_null($protocolo)) {
            $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos); //dd($procedimiento);
            if ($xtipo == 'auditoria') {
                $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
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
                    $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
                } else {
                    $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_responsable)->first();
                }
                if ($xseguro->tipo == '0') {
                    if ($id_empresa == '1307189140001') {
                        $firma = Firma_Usuario::where('id_usuario', '1307189140')->first();
                    }
                    if ($id_empresa == '0992704152001') {
                        if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                            $firma = Firma_Usuario::where('id_usuario', '094346835')->first();
                        }
                    }
                }

                if (!is_null($firma)) {
                    $doctor = $firma->doctor;
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
            $nombre_doc = User::find($firma->id_usuario);
        }

        /*$doctor = "";
        if (!is_null($procedimiento)) {

            if ($procedimiento->id_doctor_responsable == null) {
                $doctor = User::find($procedimiento->id_doctor_examinador);
            } else {
                $doctor = User::find($procedimiento->id_doctor_responsable);
            }
            if ($xseguro->tipo == '0') {
                if ($id_empresa == '1307189140001') {
                    $doctor = User::find('1307189140');
                }
                if ($id_empresa == '0992704152001') {
                    if ($procedimiento->id_doctor_examinador == '0924611882') {
                        $doctor = User::find('094346835');
                    }
                }
            }

        }*/


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
        $examenes      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '24')->orderBy('a10.id_agrupador')->get();


        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        //$orden         = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'p.fecha_nacimiento as pfecha_nacimiento','p.sexo as psexo', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2')->first();
        $orden         = Examen_Orden::find($id);
        //$detalles      = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $detalles      = $orden->detalles;

        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        //dd($orden);
        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        $texto       = '';
        foreach ($examenes as $examen) {
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;
            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }



        $fecha_evolucion = $orden->fecha_orden;
        if ($orden->fecha_convenios != null) {
            $fecha_evolucion = $orden->fecha_convenios;
        }
        if(!is_null($orden->protocolo)){
            if($orden->protocolo->pre_post == 'POST'){
                $fecha_evolucion = date('Y-m-d', strtotime('-1 day', strtotime($fecha_evolucion)));
            }
            
        }
        
        //nuevo
        $evolucion = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            //->where('hc_evo.created_at','<=',$orden->fecha_orden)
            ->whereRaw("DATE(hc_evo.created_at) <= ?", [$fecha_evolucion])
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = DB::table('aud_hc_cie10')
                ->where('aud_hc_cie10.hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i = 0;
            foreach ($hc_cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                $diagnosticos[$i]['cie10'] = $value->cie10;
                $diagnosticos[$i]['descripcion'] = $descripcion;
                $i++;
            }
        }

        //fin nuevo
        $view = View::make('pdf_hospital.formulario10A', compact(
            'orden',
            'usuarios',
            'examenes',
            'agrupadores',
            'detalles',
            'empresa',
            'seguro',
            'protocolos',
            'arreglo',
            'seleccionados',
            'hematologia',
            'quimica',
            'inmunologia',
            'heces',
            'tumorales',
            'citologia',
            'cardiacos',
            'hormonas',
            'farmacos',
            'serologia',
            'biologia',
            'coagulacion',
            'orina',
            'inmunosupresor',
            'gases',
            'trasfusional',
            'vih',
            'diagnosticos'
        ))->render();
        //return view('pdf_hospital.formulario10A', compact('arrayTotal', 'orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo', 'seleccionados'));
        $pdf  = App::make('dompdf.wrapper');
        //$pdf->setPaper('A4');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario10A.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function index()
    {
        ini_set('max_execution_time', 300);
        $formato_consentimiento = Formato_consentimiento::where('estado', '1')->select('id', 'nombre', 'pdf', 'imagen_url')->paginate(10);
        return view('hospital/formato_consentimiento/index', ['formato_consentimiento' => $formato_consentimiento]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('hospital/formato_consentimiento/create');
    }

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $formato = formato_consentimiento::where('estado', 1)->get();
        $flag = false;
        foreach ($formato as $for) {
            if (strtoupper($request['nombre']) == strtoupper($for->nombre)) {
                $flag = true;

                break;
            }
        }
        if ($flag == false) {
            $arr_formato_consentimiento = [
                'nombre'               => $request['nombre'],
                'estado'               => 1,
                'pdf'                  => $request['nombre_vista'],
                'imagen_url'           => $request['ruta_imagen'],
                'consiste'             => $request['consiste'],
                'realiza'              => $request['realiza'],
                'duracion_estimada'    => $request['duracion_estimada'],
                'beneficios'           => $request['beneficios'],
                'riesgos_frecuentes'   => $request['riesgos_frecuentes'],
                'riesgos_infrecuentes' => $request['riesgos_infrecuentes'],
                'riesgos_adicionales'  => $request['riesgos_adicionales'],
                'alternativas'         => $request['alternativas'],
                'manejo'               => $request['manejo'],
                'consecuencias'        => $request['consecuencias'],
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            formato_consentimiento::create($arr_formato_consentimiento);
            return redirect(route('formatoconsent.index'));
        } else {

            echo "<script>$(function() { $('#modal_falla').modal('show'); });</script>";
            return redirect(route('formatoconsent.create'));
        }
    }

    /**
     * Display the specified resource.
     *

     * @param  \Sis_medico\formato_consentimiento  $formato_consentimiento
     * @return \Illuminate\Http\Response
     */


    public function edit($id)


    /*
     * @param  \Sis_medico\formato_consentimiento  $forma
     * @return \Illuminate\Http\Response
     */

    {
        $forma = formato_consentimiento::where('id', $id)->first();
        return view('hospital/formato_consentimiento/edit', ['forma' => $forma, 'id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     * @param  \Sis_medico\formatoconsentimiento  $formato_consentimiento

     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $forma = formato_consentimiento::where('id', $request->id)->first();
        $formatos = formato_consentimiento::where('estado', 1)->get(['nombre']);
        $flag = false;
        foreach ($formatos as $dm) {
            if (strtoupper($request['nombre'] != $forma->nombre)) {
                if (strtoupper($request['nombre']) == strtoupper($dm->nombre)) {
                    $flag = true;
                    break;
                }
            }
        }
        if ($flag == false) {
            $arr_formato_consentimiento = [
                'nombre'               => $request['nombre'],
                'estado'               => 1,
                'pdf'                  => $request['nombre_vista'],
                'imagen_url'           => $request['ruta_imagen'],
                'consiste'             => $request['consiste'],
                'realiza'              => $request['realiza'],
                'duracion_estimada'    => $request['duracion_estimada'],
                'beneficios'           => $request['beneficios'],
                'riesgos_frecuentes'   => $request['riesgos_frecuentes'],
                'riesgos_infrecuentes' => $request['riesgos_infrecuentes'],
                'riesgos_adicionales'  => $request['riesgos_adicionales'],
                'alternativas'         => $request['alternativas'],
                'manejo'               => $request['manejo'],
                'consecuencias'        => $request['consecuencias'],
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $forma->update($arr_formato_consentimiento);
            return redirect(route('formatoconsent.index'));
            //return json_encode(['result' => 2]);
        } else {
            return redirect(route('formatoconsent.edit', ['id' => $request['id']]));
            //return json_encode(['result' => 1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Sis_medico\formato_consentimiento  $formato_consentimiento
     * @return \Illuminate\Http\Response
     */

    public function delete($id)
    {
        $forma = Formato_consentimiento::find($id);

        $arr_formato_consentimiento = [
            'estado'          => 0,

        ];
        //dd($arr_formato_consentimiento,$forma);
        $forma->update($arr_formato_consentimiento);

        return redirect(route('formatoconsent.index'));
    }


    private function carga_hc($hcid)
    {

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)
            ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
            ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
            ->join('users', 'users.id', 'paciente.id_usuario')
            ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
            ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
            ->select(
                'historiaclinica.*',
                'paciente.nombre1',
                'paciente.nombre2',
                'paciente.apellido1',
                'paciente.apellido2',
                'seguros.nombre',
                'agenda.fechaini',
                'paciente.fecha_nacimiento',
                'agenda.proc_consul',
                'agenda.id_procedimiento',
                'seguros.tipo',
                'subseguro.nombre as sbnombre',
                'paciente.telefono1',
                'paciente.telefono2',
                'paciente.telefono3',
                'users.nombre1 as unombre1',
                'users.nombre2 as unombre2',
                'users.apellido1 as uapellido1',
                'users.apellido2 as uapellido2',
                'paciente.parentesco',
                'users.telefono1 as utelefono1',
                'users.telefono2 as utelefono2',
                'users.id as uid',
                'paciente.parentescofamiliar as fparentesco',
                'paciente.nombre1familiar as fnombre1',
                'paciente.nombre2familiar as fnombre2',
                'paciente.apellido1familiar as fapellido1',
                'paciente.apellido2familiar as fapellido2',
                'paciente.id_usuario',
                'paciente.cedulafamiliar',
                'agenda.id_sala as idsala'
            )->first();

        return $historiaclinica;
    }

    public function imprimirpdf($id)
    {

        $archivo_historico = Archivo_historico::find($id);
        $documento         = Documento::find($archivo_historico->id_documento);
        $historia          = $this->carga_hc($archivo_historico->id_historia);
        $agenda            = Agenda::find($historia->id_agenda);
        $seguro            = Seguro::find($historia->id_seguro);
        $empresa           = Empresa::where('id', $agenda->id_empresa)->first();

        $paciente          = Paciente::find($historia->id_paciente);
        $empresaxdoc       = Empresa::find($documento->id_empresa);
        $doctor            = User::find($historia->id_doctor1);

        //nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
        $procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento', $agenda->id_procedimiento)->first();

        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $paper_size = array(0, 0, 595, 920);
        $data       = $historia;
        //$date = $historia->created_at;
        $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historia->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

        if (is_null($protocolo)) {
            $date = $agenda->fechaini;
        } elseif (!is_null($protocolo)) {

            if (!is_null($protocolo->fecha)) {
                $date = $protocolo->fecha;
            } else {
                $date = $agenda->fechaini;
            }
        }
        $view = \View::make('hc_admision.formato.actaentrega_v2023', compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        if ($documento->formato == 'contrareferencia') {
            $pdf->setOptions(['dpi' => 96]);
            $paper_size = array(0, 0, 1100, 1650);
            $pdf->setpaper($paper_size);
            $pdf->loadHTML($view);
        } else {
            $pdf->loadHTML($view);
        }
        return $pdf->stream($historia->id_paciente . '_' . $documento->tipo_documento . '_' . $archivo_historico->id_historia . '_' . $archivo_historico->id . '.pdf');
    }

    public function formulario017(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $xtipo      = $request->xtipo;
        // dd($request->all());
        $protocolo_original = null;

        $empresa = Empresa::where('prioridad', 1)->first();
        if ($xtipo == 'auditoria') {

            $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
            $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
            $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first(); //dd($protocolo_original, $hc_procedimiento);

        } else {

            $protocolo  = hc_protocolo::find($request->protocolo);
            $hc_procedimiento = hc_procedimientos::where('id', $protocolo->id_hc_procedimientos)->first();
        }
        
        //dd($protocolo);

        $pxs = $hc_procedimiento->hc_procedimiento_f; //dd($pxs);
        $proc = null;
        $imagen = null;
        foreach ($pxs as $px) {
            //dd($px);
            if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                $proc = $px->procedimiento;
            }
        } //dd($proc);
        if ($proc != null) {
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
            $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
            //dd($cie10_eg);
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
            $cie10_eg = Hc_Cie10::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
        }

        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
            //'id_anestesiologo'       => $request->id_anestesiologo,
        ];


        if ($xtipo == 'auditoria') {
            $protocolo->auditoria_procedimiento->update($input2);
        } else {
            $protocolo->procedimiento->update($input2);
        }

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
        //dd($firma,$request->all());
        $idusuario = Auth::user()->id;
        if ($idusuario == '9999966654') {
            //dd($hc_procedimiento,$protocolo);
        }


        $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'cie10_eg', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original', 'xtipo'))->render();
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
    public function formulario002($id, Request $request)
    {
        //dd($id);
        $anamnesis = Anamnesis::find($id);
        $h_c = $anamnesis->historiaclinica->evoluciones->where("secuencia",0)->first();
        $xtipo      = $request->xtipo;
        //dd($anamnesis->historiaclinica->evoluciones[0]->indicaciones);
        //dd($anamnesis);
        if ($xtipo == 'auditoria') {

            $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hcid',  $anamnesis->hcid)->get();
        } else {

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
        if ($doctor == "") {
            $doctor = $anamnesis->historiaclinica->doctor_1;
        }
        $vt_c10 = null;$vt_pre_def = '';

        $view = View::make('pdf_hospital.formulario002', compact('h_c','anamnesis', 'cie10', 'doctor', 'vt_c10','vt_pre_def'))->render();
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

        $anamnesis = Anamnesis::where('hcid', $hcid)->where('estado', '1')->first(); //dd($anamnesis);

        $historiaclinica = Historiaclinica::find($hcid);


        if (is_null($anamnesis)) {

            $motivo_evolucion = '';
            $problema_actual = '';
            $indicacion_evolucion = '';

            if (count($historiaclinica->evoluciones) > 0) {

                $evoluciones = $historiaclinica->evoluciones;
                $evolucion = $evoluciones[0];

                $motivo_evolucion = $evolucion->motivo;
                $problema_actual = $evolucion->cuadro_clinico;
                $indicacion_evolucion = $evolucion->indicacion;
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


        return redirect(route('formato.anamenesis_index', ['anamnesis' => $anamnesis]));
    }

    public function anamenesis_index($id)
    {
        $anamnesis = Anamnesis::find($id);
        $h_c = $anamnesis->historiaclinica->evoluciones->where("secuencia",0)->first();
    
        //dd($anamnesis);

        return view('anamenesis/anamenesis', compact('id', 'anamnesis','h_c'));
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
        $historiaclinica = Historiaclinica::where('hcid', $anamnesis->hcid)->first();

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

    public function modal_formatos_24($id, Request $request)
    {

        $tipo = $request->tipo;

        $biopsia =  null;
        if ($tipo == 'auditoria') {
            $protocolo = Aud_Hc_Protocolo::find($id);
            $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
            $anamesis   = null;
            if ($protocolo != null) {
                $anamesis  = Anamnesis::where('hcid', $protocolo_original->hcid)->first();
                $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->first();
                $biopsia =  Hc_informe_biopsia::where('id_hc_procedimiento', $protocolo_original->id_hc_procedimientos)->first();
            }
        } else {
            $protocolo  = hc_protocolo::find($id);
            $anamesis   = null;
            if ($protocolo != null) {
                $anamesis  = Anamnesis::where('hcid', $protocolo->hcid)->first();
                $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->first();
                $biopsia =  Hc_informe_biopsia::where('id_hc_procedimiento', $protocolo->id_hc_procedimientos)->first();
            }
        }

        return view('pdf_hospital.modal_formularios', ['anamesis' => $anamesis, 'epicrisis' => $epicrisis, 'protocolo' => $protocolo, 'tipo' => $tipo, 'biopsia' => $biopsia]);
    }


    public function pdf_019($hc_id_procedimiento)
    {
        $hc_procedimiento = hc_procedimientos::find($hc_id_procedimiento);
        if (!is_null($hc_procedimiento)) {
            $historia = Historiaclinica::find($hc_procedimiento->id_hc);
            $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::Where('hc_id_procedimiento', $hc_id_procedimiento)->first();
            if (!is_null($historia)) {
                $paciente = Paciente::find($historia->id_paciente);
                $agenda = Agenda::find($historia->id_agenda);
                $especialidad = Especialidad::find($agenda->espid);
                $empresa = Empresa::find($agenda->id_empresa);
                $doctor = User::find($agenda->id_doctor1);
                $cirugia = '';
                $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_id_procedimiento)->get();
                $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org',$hc_id_procedimiento)->first();
                //dd($aud_hc_procedimiento);
                //$anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', $hc_id_procedimiento)->first();
                $anestesiologia = Hc_Anestesiologia::find($evolucion_anestesiologo->id_hc_anestesiologia);
                $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anestesiologia->historia->hcid)->limit(2)->get();
                $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_id_procedimiento)->where('ingreso_egreso', 'EGRESO')->first();
                //dd($cie10_eg);
                $idusuario = Auth::user()->id;
                if($idusuario == '9999966654'){
                    //dd($cie10_eg);
                }
                //dd($anestesiologia);
                if (count($hc_procedimiento_final) > 1) {
                    $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                    for ($i = 1; $i < count($hc_procedimiento_final); $i++) {
                        $cirugia = $cirugia . ' + ' . $hc_procedimiento_final[$i]->procedimiento->nombre;
                    }
                } elseif (count($hc_procedimiento_final) == 1) {
                    $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                }
                $diagnostico = '';
                /*$hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $hc_id_procedimiento)->get();
                foreach ($hc_cie10 as $value) {
                    $cie10 = Cie_10_3::find($value->cie10);
                    if (is_null($cie10)) {
                        $cie10 = Cie_10_4::find($value->cie10);
                    }
                    $diagnostico = $diagnostico . '<br>' . $cie10->descripcion;
                }*/
                //$firma = Firma_Usuario::where('id_usuario', $doctor->id)->first();
                // $hora = date('H:i', strtotime($fecha));
                // $fecha = date('Y-m-d', strtotime($fecha));
                $firma = null;
                if(!is_null($anestesiologia->id_anestesiologo)){
                $firma = Firma_Usuario::where('id_usuario', $anestesiologia->id_anestesiologo)->first();
                }
                $view = View::make('pdf_hospital.formulario019', compact('evolucion_anestesiologo', 'agenda', 'historia', 'paciente', 'especialidad', 'empresa', 'doctor', 'cirugia', 'diagnostico', 'anestesiologia','cie10','firma','cie10_eg','aud_hc_procedimiento'))->render();
                $pdf  = App::make('dompdf.wrapper');
                // $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);


                return $pdf->stream('HCU_019.pdf');
            }
        }
        //return view('pdf_hospital.formulario001');
    }

    
    public function informe_bioxias_crear($id)
    {

        $biopsia_hc4 = Hc4_Biopsias::find($id);
        $hc_procedimiento_id = $biopsia_hc4->hc_id_procedimiento;
        //dd($hc_procedimiento);


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $bioxia = Hc_informe_biopsia::where('id_hc_muestra', $id)->where('estado', '1')->first();
        $hc_procedimientos = hc_procedimientos::where('id', $hc_procedimiento_id)->where('estado', '1')->first();
        $id_hc = Historiaclinica::find($hc_procedimientos->id_hc);
        if(is_null($bioxia)) {     
            $arr_infome_bioxia = [
                'id_hc_procedimiento' => $hc_procedimiento_id,
                'hcid'                => $id_hc->hcid,
                'id_hc_muestra'                => $id,
                'id_paciente'         => $id_hc->paciente->id,
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $id_inf = Hc_informe_biopsia::insertGetId($arr_infome_bioxia);
        }

        return redirect(route('pdf_hospital.informe_bioxias_edit', ['id' => $id]));
    }

    public function informe_bioxias_edit($id)
    {
        $texto = "";
        //dd($id);
        $biopsia_hc4 = Hc4_Biopsias::find($id);
        $hc_procedimiento_id = $biopsia_hc4->id_hc_procedimiento;
        $infBioxia = Hc_informe_biopsia::where('id_hc_muestra', $id)->first();
        //dd($infBioxia);
        $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento_id)->get();
        $mas = true;

        $patologos = User::where('id_tipo_usuario', 31)->get();
        
        foreach ($adicionales as $value2) {
            if ($mas == true) {
                $texto = $texto . $value2->procedimiento->nombre;
                $mas = false;
            } else {
                $texto = $texto . ' + ' .  $value2->procedimiento->nombre;
            }
        }


        return view('pdf_hospital.informe_bioxia_edit', ['infBioxia' => $infBioxia, 'id' => $id, 'procedimiento' => $texto, 'patologos' => $patologos, 'biopsia_hc4' => $biopsia_hc4]);
    }

    public function informe_bioxias_guardar(Request $request)
    {

        //dd($request->all());
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $hcid = $request->input('hcid');

        $id = $request->input('id');
        //dd($id_hc);
        $hc_informe_biopsia = Hc_informe_biopsia::findorfail($id);

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

    public function formulario013A($id_orden)
    {
        $formulario013 = HcFormulario013::where('id_hc_procedimiento', $id_orden)->firstOrFail();
        if(!is_null($formulario013)){
            $hc_procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
            $historia = Historiaclinica::find($hc_procedimiento->id_hc);
            $agenda = Agenda::find($historia->id_agenda);
            $especialidad = Especialidad::find($agenda->espid);
            $paciente = Paciente::find($formulario013->id_paciente);
            $empresa = Empresa::find($formulario013->id_empresa);
            $seguro = Seguro::find($paciente->id_seguro);
    
            $seguro_nombre = '';
            if(!is_null($seguro)){
                $seguro_nombre = strtoupper($seguro->nombre);
            }
    
            $edad = null;
            $fechaActual = new DateTime();
            if(!is_null($paciente)){
                $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
                $edad = $fechaNacimiento->diff($fechaActual);
                $age = $edad->y;
            }

            $diagnosticos = Aud_Hc_Cie10::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();

            foreach($diagnosticos as $value_descripcion){
                $cie10 = Cie_10_3::where('id',$value_descripcion->cie10)->first();
            }

            $biopsias = Hc4_Auditoria_Biopsias::where('id_hc_formulario_013',$formulario013->id)->where('estado',1)->limit(5)->get();
            //dd($biopsias);
            $procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
            $fecha_toma = date_format(new DateTime($formulario013->fecha_toma), 'd/m/Y'); 
            $hora_toma = date_format(new DateTime($formulario013->fecha_toma), 'H:i:s');
            $fecha = date_format(new DateTime($procedimiento->created_at), 'Y-m-d'); 
            $hora = date_format(new DateTime($procedimiento->created_at), 'H:i:s');
            $doctor = User::find($procedimiento->id_doctor_examinador);
            $aud_proc = Aud_Hc_Procedimientos::where('id_procedimientos_org', $procedimiento->id)->where('estado','1')->first();
            if(!is_null($aud_proc)){
                $doctor = $aud_proc->doctor_firma;
            }
            $firma = null;
            $ruta_firma = "";
            if(!is_null($doctor)){
                $firma = Firma_Usuario::where('id_usuario',$doctor->id)->first();
                if(!is_null($firma)){
                    if(!is_null($firma->firma)){
                        $ruta_firma = base_path().'/storage/app/avatars/' . $firma->firma;
                    }
                }
            }
            $view = View::make('pdf_hospital.formulario13A',compact('age','diagnosticos' ,'fecha_toma', 'hora_toma', 'value_descripcion','cie10','formulario013', 'paciente', 'empresa', 'seguro_nombre', 'especialidad', 'biopsias', 'procedimiento', 'fecha','hora','doctor','ruta_firma'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('HCU_013_A.pdf');
        }
        else{
            return 'No se encontro formulario 013';
        }
    }
    public function formulario007_unificado($id)
    {

        $historia_clinica = Historiaclinica::find($id);

        //dd($cardio);
        $paciente = $historia_clinica->paciente;
        $cardio = $historia_clinica->cardio;
        $evolucion_orden = $cardio->evolucion_orden;
        //dd($cardio,$cardio->doctor);
        //dd($evolucion_orden->historiaclinica);
        $cie10_orden = Hc_cardio_cie10::where('id_cardio', $cardio->id)->where('estado', '1')->get();

        $interconsulta = Interconsulta::where('id_hc', $historia_clinica->hcid)->where('estado', '1')->first();


        if (is_null($interconsulta)) {
            //$this->crear_interconsulta($historia_clinica, $cardio);
            $interconsulta = $this->crear_interconsulta($historia_clinica, $cardio);
            $this->crear_diagnosticos_interconsulta($historia_clinica, $interconsulta);
            $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
        } else {
            //$this->actualiza_interconsulta($historia_clinica, $cardio, $interconsulta);
            $interconsulta = $this->actualiza_interconsulta($historia_clinica, $cardio, $interconsulta);
            $this->crear_diagnosticos_interconsulta($historia_clinica, $interconsulta);
            $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
        }
        $cie10 = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
        //dd($interconsulta,$cie10);
        $vistaurl = "pdf_hospital.formulario007_unificado";
        $view = \View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10', 'evolucion_orden', 'cardio', 'cie10_orden'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_007.pdf' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
        exit(0);
        //dd($historiaclinica);
        //return view('hc_admision/evolucion/cardiologia', ['evolucion' => $evolucion, 'historiaclinica' => $historiaclinica, 'cardio' => $cardio]);
    }
    public static function crear_interconsulta($historia_clinica, $cardio)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $evolucion_orden = $cardio->evolucion_orden;
        $especialidad_res = $historia_clinica->agenda->especialidad->nombre;
        $especialidad = " ";
        $id_dr_inter = "";
        $aud_hc = Aud_Hc_Procedimientos::where('id_hc', $historia_clinica->hcid)->first();
        if (!is_null($evolucion_orden)) {
            $especialidad = $evolucion_orden->historiaclinica->agenda->especialidad->nombre;
        }
        if (!is_null($aud_hc)) {
            $id_dr_inter = $aud_hc->id_doctor_examinador;
        }
        $arr_inter_consulta = [
            'id_paciente'           => $historia_clinica->id_paciente,
            'id_doctor'             => $historia_clinica->id_doctor1,
            'id_empresa'            => $historia_clinica->agenda->id_empresa,
            'fecha'                 => $cardio->fecha_formato,
            'evolucion'             => $cardio->cuadro_clinico,
            'estado'                => '1',
            'resultados_exa'        => $cardio->resultados,
            'plan_terapeuticos'     => $cardio->plan_tratamiento,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_hc'                 => $historia_clinica->hcid,
            'resumen'               => $cardio->resumen,
            'cuadro_actual'         => $cardio->cuadro_actual,
            'especialidad'          => $especialidad,
            'especialidad_resultado'      => $especialidad_res,
            'id_doctor'             => $id_dr_inter,
        ];
        $interconsulta = Interconsulta::create($arr_inter_consulta);
        return $interconsulta;
    }
    public static function actualiza_interconsulta($historia_clinica, $cardio, $interconsulta)
    {
        $interconsulta = Interconsulta::find($interconsulta->id);
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $especialidad_res = $historia_clinica->agenda->especialidad->nombre;
        $evolucion_orden = $cardio->evolucion_orden;
        //dd($cardio,$cardio->evolucion_orden);
        $id_dr_inter = "";
        $aud_hc = Aud_Hc_Procedimientos::where('id_hc', $historia_clinica->hcid)->first();
        $especialidad = " ";
        if (!is_null($evolucion_orden)) {
            $especialidad = $evolucion_orden->historiaclinica->agenda->especialidad->nombre;
            //dd($especialidad);
        }
        if (!is_null($aud_hc)) {
            $id_dr_inter = $aud_hc->id_doctor_examinador;
        }
        $arr_inter_consulta = [
            'id_paciente'           => $historia_clinica->id_paciente,
            'id_doctor'             => $historia_clinica->id_doctor1,
            'id_empresa'            => $historia_clinica->agenda->id_empresa,
            'fecha'                 => $cardio->fecha_formato,
            //'evolucion'             => $cardio->cuadro_clinico,
            'estado'                => '1',
            'resultados_exa'        => $cardio->resultados,
            'plan_terapeuticos'     => $cardio->plan_tratamiento,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'id_hc'                 => $historia_clinica->hcid,
            'resumen'               => $cardio->resumen,
            'cuadro_actual'         => $cardio->cuadro_actual,
            'especialidad'          => $especialidad,
            'especialidad_resultado'      => $especialidad_res,
            'id_doctor'             => $id_dr_inter,
        ];
        $interconsulta->update($arr_inter_consulta);
        $interconsulta = Interconsulta::find($interconsulta->id);
        return $interconsulta;
    }
    public static function crear_diagnosticos_interconsulta($historia_clinica, $interconsulta)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cie10_cardio = Aud_Hc_Cie10::where('hcid', $historia_clinica->hcid)->get();
        //dd($cie10_cardio);

        $cie10_interconsulta = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
        foreach ($cie10_interconsulta as $value) {
            $value->delete();
        }
        foreach ($cie10_cardio as $cie10_c) {
            $arr_cie_10 = [
                'id_interconsulta'      => $interconsulta->id,
                'cie10'                 => $cie10_c->cie10,
                'presuntivo_definitivo' => $cie10_c->presuntivo_definitivo,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
            ];
            Interconsulta_Diagnostico::create($arr_cie_10);
        }
    }
    public function formulario013B($id)
    {
        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //dd($id)
        $biopsias =  Hc_informe_biopsia::where('id_hc_procedimiento', $id)->get();
        if($biopsias =="[]"){
            abort(404, 'Acceso no autorizado.');
        }
        //dd($biopsia);
                
        $view = View::make('pdf_hospital.formulario13B', compact('biopsias'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
	$pdf->setOptions(['chroot'  => base_path('/')]);
        return $pdf->stream('formulario13B.pdf');
    }
    public function informe_tecnico($id)
    {
        
        //$hc_id_procedimiento = hc_procedimientos::where('id', $id)->first();

        $archivo_plano = Archivo_Plano_Cabecera::find($id);
       
        $hc_id_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->where('estado', '1')->first();
        $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
        $cie10_in = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first(); //dd($protocolo, $archivo_plano->id_hc_procedimimentos);
        
     

        if ($hc_id_procedimiento == null) {
            $hc_id_procedimiento = $archivo_plano->fx_hc_procedimiento;
            $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
            $cie10_eg = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
            $cie10_in = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
            $protocolo = hc_protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
        }
        $detalles = $archivo_plano->detalles->whereIn('tipo', ['P', 'PA'])->whereNotIn('codigo', ['99252','93000', '99204', '99203'])->where('estado', '1'); //dd($detalles);

        $protos = Informe_Tecnico::where('id_ap_cabecera', $archivo_plano->id)->get();


        $tarifario = DB::table('ap_tarifarios_protocolo')
            ->join(
                'maestro_de_codigo',
                'ap_tarifarios_protocolo.id_maestro_codigo',
                '=',
                'maestro_de_codigo.id'
            )
            ->whereIn('tarifario', $detalles->pluck('codigo'))
            ->where('ap_tarifarios_protocolo.estado', 1)
            ->groupBy(
                'ap_tarifarios_protocolo.id_maestro_codigo'
            )
            ->select(
                'maestro_de_codigo.id',
                'maestro_de_codigo.protocolos',
                'maestro_de_codigo.observaciones',
                'maestro_de_codigo.nac',
                'maestro_de_codigo.intern',
                'maestro_de_codigo.mbe'
            )
            ->get();


        $view = View::make('pdf_hospital.informe_tecnico', compact('id', 'hc_id_procedimiento', 'archivo_plano', 'evolucion', 'detalles', 'protos', 'cie10_eg', 'cie10_in', 'protocolo', 'tarifario'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('INFORME TECNICO.pdf');
    }


    //sucursales
    public function formato_tecnico($id)
    {
        $doctor = User::where("id_tipo_usuario", 3)->get();
        $especialidad = Especialidad::where("estado", 1)->get();
        //$hc_procedimiento = hc_procedimientos::where('id',$id)->first();
        $archivo_plano = Archivo_Plano_Cabecera::find($id);
        $hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->where('estado', '1')->first();
        $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
        $cie10_in = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();

        if ($hc_procedimiento == null) {
            $hc_procedimiento = $archivo_plano->fx_hc_procedimiento;
            $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
            $cie10_eg = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
            $cie10_in = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
            $protocolo = hc_protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
        }
        $detalles = $archivo_plano->detalles->whereIn('tipo', ['P', 'PA'])->whereNotIn('codigo', ['93000', '99204', '99203'])->where("estado", 1);
        //dd($archivo_plano);
        //dd($hc_procedimiento);
        $protos = Informe_Tecnico::where('id_ap_cabecera', $archivo_plano->id)->get();




        return view("informe_tecnico.index", ['archivo_plano' => $archivo_plano, "hc_procedimiento" => $hc_procedimiento, "doctor" => $doctor, "especialidad" => $especialidad, 'evolucion' => $evolucion, 'detalles' => $detalles, 'protos' => $protos, 'cie10_eg' => $cie10_eg, 'protocolo' => $protocolo]);
    }



    //sucursales

    public function guardar_auditoria(Request $request)
    {

        $id_maestro_codigo = $request->id_maestro_codigo;
        $maestro_codigo = MaestroDeCodigo::find($id_maestro_codigo);

        try {
            if (!is_null($maestro_codigo)) {
                $guardar = DB::table("informe_tecnico")->insert([
                    "id_ap_cabecera" => $request->id_ap_cabecera,
                    //"numero"=> $request["datos"]["numero"],
                    "id_maestro_codigo" => $id_maestro_codigo,
                    "nac" => $maestro_codigo->nac,
                    "intern" => $maestro_codigo->intern,
                    "mbe" => $maestro_codigo->mbe,
                    "observaciones" => $maestro_codigo->observaciones,
                    "id_usuariocrea" => Auth::user()->id,
                    "id_usuariomod" => Auth::user()->id,
                    "ip_creacion" => $_SERVER["REMOTE_ADDR"],
                    "ip_modificacion" => $_SERVER["REMOTE_ADDR"],
                ]);
                return ["status" => true, "msj" => "Guardado con exito"];
            }
        } catch (Exception $e) {
            return ["status" => false, "msj" => $e->getMessage(), "line" => $e->getLine()];
        }
    }



    //sucursales

    public function buscarProtocolo(Request $request)
    {
        $nombre = $request['search'];
        $data   = array();
        $protocolo = MaestroDeCodigo::where('protocolos', 'like', '%' . $nombre . '%')->get(); //dd($protocolo, $request->all());
        foreach ($protocolo as $value) {

            $nom_completo = "$value->protocolos";
            $data[]       = array(
                'id' => $value->id,
                //'numero' => $value->numero,
                'text' => $nom_completo,
                //'observaciones' => $value->observaciones,
                //'intern' => $value->intern,
                //'mbe' => $value->mbe
            );
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }


    //sucursales
    public function eliminar_protocolo($id)
    {

        $it = Informe_Tecnico::find($id);
        if (!is_null($it)) {
            $it->delete();
        }

        return "ok";
    }
}
