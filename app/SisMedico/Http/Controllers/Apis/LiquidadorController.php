<?php

namespace Sis_medico\Http\Controllers\Apis;

use Carbon\Carbon;
use Excel;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Archivo_historico;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Documento;
use Sis_medico\hc_procedimientos;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Epicrisis;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_Labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_anestesiologia_csv;
use Sis_medico\Hc_Cie10;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\HcFormulario013;
use Sis_medico\Historiaclinica;
use Sis_medico\Interconsulta;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Protocolo;
use Sis_medico\record_tecnicas_anestesicas;
use Sis_medico\Seguro;
use Sis_medico\User;
use Response;
use Sis_medico\Agenda_archivo;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\AgendaScan;
use Sis_medico\Anamnesis;
use Sis_medico\Ap_Orden;
use Sis_medico\ApArchivoFactura;
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Cardiologia;
use Sis_medico\Convenio;
use Sis_medico\Especialidad;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\LogDocumentoFirmado;
use Sis_medico\Medicina;
use Sis_medico\Nota_entrega;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\procedimiento_completo;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Orden;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Utilidades;

class LiquidadorController extends Controller
{
    // planilla individual
    public function planilla_detalle_pdf($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($archivo_plano)){
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if(!is_null($hc_procedimiento)){
                $txt_cie10 = null;
                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();

                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva','apd.iva_unitario','apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
                
                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

                $view = View::make('archivo_plano.planilla.planilla_pdf', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos'))->render();

                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->loadHTML($view);
                return $pdf->download('planilla_pdf_'.$hc_procedimiento->historia->paciente->apellido1.'_'.$hc_procedimiento->historia->paciente->nombre1.'_.pdf');
            }
            else{
                return 'No se encontro el procedimiento';
            }
        }
        else{
            return 'No se encontraron resultados';
        }
    }
    //acta entrega
    public function imprimirpdfActaEntrega($empresa,$mes_plano,$atencion,$seguro='2')//$ahid,
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $idhc = $plano_cabecera->id_hc_procedimimentos;
                $archivo_historico = Archivo_historico::where('id_historia',$idhc)->where('tipo_documento', 'ACT')->first();
                if(!is_null($archivo_historico)){
                    $documento         = Documento::find($archivo_historico->id_documento);
                    if(!is_null($documento)){
                        if(strtoupper($documento->tipo_documento) == 'ACT'){
                            $empresaxdoc       = Empresa::find($documento->id_empresa);
                            $historia          = $this->carga_hc($idhc);//($archivo_historico->id_historia);
                            $agenda            = Agenda::find($historia->id_agenda);
                            $seguro            = Seguro::find($historia->id_seguro);
                            $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
                            $paciente          = Paciente::find($historia->id_paciente);
                            $doctor            = User::find($historia->id_doctor1);
                            $procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento', $agenda->id_procedimiento)->first();
                            $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                            $paper_size = array(0, 0, 595, 920);
                            $data       = $historia;
                            //$date = $historia->created_at;
                            $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historia->hcid)
                            ->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')
                            ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
                            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                            ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();
                            
                            if (is_null($protocolo)) {
                                $date = $agenda->fechaini;
                            } elseif (!is_null($protocolo)) {
                                if (!is_null($protocolo->fecha)) {
                                    $date = $protocolo->fecha;
                                } else {
                                    $date = $agenda->fechaini;
                                }
                            }
                            //dd($documento);
                            //hc_admision.formato.actaentrega_v2023
                            //$view = View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            if ($documento->formato == 'actaentrega' && date("Y-m-d", strtotime($agenda->fechaini)) > date("Y-m-d", strtotime("2023-06-01"))) {
                                $view = View::make('hc_admision.formato.actaentrega_v2023', compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            }else{
                                $view = View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            }
                            $pdf  = App::make('dompdf.wrapper');
                    
                            if ($documento->formato == 'contrareferencia') {
                    
                                $pdf->setOptions(['dpi' => 96]);
                                $paper_size = array(0, 0, 1100, 1650);
                                $pdf->setpaper($paper_size);
                                $pdf->loadHTML($view);
                            } else {
                                $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
                            }
                    
                            return $pdf->stream($historia->id_paciente . '_' . $documento->tipo_documento . '_' . $archivo_historico->id_historia . '_' . $archivo_historico->id . '.pdf');
                        }
                        else{
                            // return response()->view('errors.404');
                            return 'el documento no es una acta de entrega';
                        }
                    }
                    else{
                        // return response()->view('errors.404');
                        return 'no se encontro el documento';
                    }
                }
                else{
                    // return response()->view('errors.404');
                    return ' no se encontro el archivo historico';
                }
            }
            else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }        
    }

    private function carga_hc($hcid)
    {
        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)
        ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
        ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
        ->join('users', 'users.id', 'paciente.id_usuario')
        ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
        ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
        ->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 
        'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 
        'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 
        'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 
        'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 
        'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 
        'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 
        'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 
        'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 
        'agenda.id_sala as idsala')->first();

        return $historiaclinica;
    }

    //epicrisis (006)
    public function imprimir_epicrisis($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento',$plano_cabecera->id_hc_procedimimentos)->first();
            if(!is_null($epicrisis)) {
                $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
                $historiaclinica = DB::table('historiaclinica')
                ->where('historiaclinica.hcid', $epicrisis->hcid)
                ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
                ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
                ->join('users', 'users.id', 'paciente.id_usuario')
                ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
                ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
                ->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')
                ->first();
        
                $cie10_in_pre = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();
        
                $cie10_in_def = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();
        
                $cie10_eg_pre = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();
        
                $cie10_eg_def = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();
                
                $cie10_3 = Cie_10_3::all();
                $cie10_4 = Cie_10_4::all();
                if (!is_null($protocolo)) {            
                    $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
        
                    if (!is_null($procedimiento)) {
                        $id_seguro  = $procedimiento->id_seguro;
                        $id_empresa = $procedimiento->id_empresa;
                        if ($procedimiento->id_seguro == null) {
                            $procedimiento->update(['id_seguro' => $historiaclinica->id_seguro]);
                            $id_seguro = $historiaclinica->id_seguro;
                        }
                        if ($procedimiento->id_empresa == null) {
                            $procedimiento->update(['id_empresa' => $historiaclinica->id_empresa]);
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
                    }

                    $tiene_receta = 'NO';
                    $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
            
                    if (!is_null($receta)) {
                        $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                        if ($receta_det > 0 || $receta->prescripcion != null) {
                            $tiene_receta = 'SI';
                        }
                    }
            
                    $tiene_receta = $epicrisis->receta;
            
                    $nombre_doc = "";
                    if (!is_null($firma)) {
                        $nombre_doc = User::find($firma->id_usuario);
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
            
                    $data = $historiaclinica;
                    $receta = hc_receta::where('id_hc',$data->hcid)->first();
                    
                    $view = View::make('auditoria_hc_admision.formato.epicrisis', compact('receta','data', 'evolucion_1', 'epicrisis', 'cie10_in_pre', 'cie10_in_def', 'cie10_3', 'cie10_4', 'cie10_eg_pre', 'cie10_eg_def', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa'))->render();
                    $pdf  = App::make('dompdf.wrapper');
            
                    $pdf->loadHTML($view);
                    return $pdf->stream('epicrisis-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $procedimiento = "";
                    $firma         = "";
                }
            }
            else{
                return 'Epicrisis no encontrado';
            }
        }
        else{
            return 'No se encontraron resultados';
        }
    }

    //interconsulta (007)
    public function imprimir_interconsulta($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $interconsulta = Interconsulta::where('id_hc',$plano_cabecera->id_hc)->first();
            if(!is_null($interconsulta)){
                $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $interconsulta->id)->get();
                $paciente       = $interconsulta->paciente;
                $age            = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                
                $empresa        = Empresa::where('prioridad', 1)->get()->first();

                $vistaurl = "hc4.consulta.formulario_007";
                $view     = View::make($vistaurl, compact('interconsulta', 'age', 'empresa' , 'paciente', 'cie10'))->render();

                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('007-' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
            }
            else{
                return 'No se encontro interconsulta';
            }            
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function buscar_orden($empresa,$mes_plano,$atencion,$seguro='2'){
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        $id_ordenes = [];
        if(!is_null($plano_cabecera)){
            $plano_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera',$plano_cabecera->id)->groupBy('id_orden_labs')->get();
            $cont = 0;
            foreach ($plano_detalle as $detalle) {
                $id_orden= $detalle->id_orden_labs;
                if(!is_null($id_orden)){
                    $id_ordenes[$cont] = $id_orden;
                    $cont++;
                }
            }
        }
        return $id_ordenes;
    }
    
    //orden laboratorio (010)
    public function descargar_orden_laboratorio($id_orden)
    {
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $examenes      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '24')->orderBy('a10.id_agrupador')->get();

        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        $orden         = Examen_Orden::find($id_orden);
        $detalles      = $orden->detalles;
        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();
        $empresa = Empresa::find($orden->id_empresa);

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
        
        $evolucion = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            ->where('hc_evo.created_at', '<=', $orden->fecha_orden)
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = DB::table('hc_cie10')
                ->where('hc_cie10.hc_id_procedimiento', $evolucion->hc_id_procedimiento)
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

        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_010A.pdf');
    }
    
    //resultados
    public function imprimir_resultado_back($id_orden)
    {
        ini_set('max_execution_time',300);
        $orden    = Examen_Orden::find($id_orden);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados = $orden->resultados;
        $parametros = Examen_Parametro::orderBy('orden')->get();

        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count(); //OK
                } else {
                    $cant_par = $cant_par + 10; // PENDIENTE
                }
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') { // PENDIENTE
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
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_Labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $age      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $vistaurl = "laboratorio.orden.resultados_pdf";
        $view     = View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id_orden . '.pdf', array("Attachment" => false));
    }

    public function imprimir_resultado($id_orden)
    {
        $orden    = Examen_Orden::find($id_orden);
        $empresa = Empresa::find($orden->id_empresa);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
        
        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
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
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }
        
        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $edad      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
        
        $diaNacimiento = $fechaNacimiento->format('d');
        $fecha=date('Y-m-d');
        $hora = date('H:i:s');
        $mesNacimiento = $fechaNacimiento->format('m');
        //nuevo
        $evolucion = Historiaclinica::where('historiaclinica.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'historiaclinica.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            ->where('hc_evo.created_at','<=',$orden->fecha_orden)
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i=0;
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

        $vistaurl = "laboratorio.orden.resultados_iess_pdf";
        $view     = View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'edad', 'ucreador', 'user', 'empresa', 'diaNacimiento','mesNacimiento','fecha','hora','diagnosticos'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('C_RESULTADOS.pdf');
    }
    //imagenologia (012)
    public function imprimir_formulario012($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        $orden_012   = Orden_012::where('id_orden',$id_orden)->first();
        
        if (!is_null($orden_012)) {
            $paciente    = Paciente::find($orden_012->id_paciente);
            $id_empresa  = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
            $inf_empresa = Empresa::where('id', $orden_012->id_empresa)->OrderBy('estado', '1')->first();

            $id_seguro = $orden_012->evolucion->procedimiento->id_seguro;
            if ($id_seguro != null) {
                $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
            }
            $empresa = Empresa::find($id_empresa);
            $seguro  = Seguro::find($id_seguro);
            $doctor  = User::find($orden_012->id_doctor_firma);
            $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();
            $age   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();

            $fecha_d = date('Y/m/d');
            $vistaurl = "hc4.ordenes.orden_ingresada_formato012.formato012_pdf";
            $view     = View::make($vistaurl, compact('orden_012', 'paciente', 'seguro', 'inf_empresa', 'empresa', 'age', 'doctor', 'cie10'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->setPaper('A4','portairt');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'chroot'  => base_path('/')]);
            return $pdf->stream('orden-de-laboratorio-' . $id_orden . '.pdf');
        }
        else{
            return 'No existe la orden';
        }
    }

    //protocolo (017)
    public function imprimir_protocolo($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
            if(!is_null($protocolo)){
                $id_agenda = $protocolo->historiaclinica->id_agenda;
                $agenda    = Agenda::find($id_agenda);
        
                $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador2;
                if ($protocolo->auditoria_procedimiento->id_doctor_responsable != null) {
                    $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_responsable;
                }
                if ($protocolo->auditoria_procedimiento->id_seguro != null) {
                    $seguro = Seguro::find($protocolo->auditoria_procedimiento->id_seguro);
                    if ($seguro->tipo == 0) {
                        if ($protocolo->auditoria_procedimiento->id_empresa == '1307189140001') {
                            $id_doctor_firma = '1307189140';
        
                        }
                        if ($protocolo->auditoria_procedimiento->id_empresa == '0992704152001') {
                            if ($id_doctor_firma == '0924611882') {
                                $id_doctor_firma = '094346835';
                            }
                        }
        
                    }
                }
        
                if ($id_doctor_firma == null) {
                    $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador;
                }
        
                if ($id_doctor_firma == null) {
                    $id_doctor_firma = $protocolo->historiaclinica->doctor_1->id;
                }
    
                $firma = Firma_Usuario::where('id_usuario', $id_doctor_firma)->first();
                $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;
                $view = View::make('auditoria_hc_admision.protocolo.prot_operatico', compact('protocolo', 'age', 'agenda', 'firma'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
    
                return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');
            }
            else{
                return 'No se encontro protocolo';
            }
            
        }
        else{
            return 'No se encontro registro';
        }
    }

    //anestesia 
    public function imprimir_anestesia($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            if(!is_null($id_procedimiento)){
                $procedimientos_hc = hc_procedimientos::find($id_procedimiento);
                $id                = $procedimientos_hc->id_hc;
                $historia          = Historiaclinica::find($id);
                if(!is_null($historia)){
                    $agenda            = Agenda::find($historia->id_agenda);
                    if(is_null($agenda)){
                        return 'No se encontro la agenda';
                    }
                    $seguro            = Seguro::find($historia->id_seguro);
                    $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
                    $paciente          = Paciente::find($historia->id_paciente);
                    if(is_null($paciente)){
                        return 'No se encontro el paciente';
                    }
                    $doctor            = User::find($historia->id_doctor1);
                    $record            = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id_procedimiento)->first();
                    if(!is_null($record)){
                        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                        $paper_size = array(0, 0, 595, 920);
                        $data       = $historia;
                        $date       = $historia->created_at;
                        $csv        = hc_anestesiologia_csv::where('id_hc_anestesiologia', '=', $record->id)->get();
                        $pentax     = Pentax::where('hcid', '=', $id)->first();
                        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $pentax->id)->get();
                        $record_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', '=', $record->id)->get();
                        $view = View::make('hc_admision.formato.record-anestesico', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'record', 'historia', 'record_tecnicas', 'csv', 'procedimientos_pentax', 'procedimientos_hc'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->loadHTML($view);
                        return $pdf->stream($historia->id_paciente . '_RECORDANESTESICO_' . $id . '.pdf');
                    }
                    else{
                        return 'No se encontro el record de anestesiologia';
                    }
                }
                else{
                    return 'No se encontro la historia clinica';
                }
            }
            else{
                return 'No se encontro procedimiento';
            }
        }
        else{
            return 'No se encontro registro';
        }
    }

    //histopatologia (013)
    public function imprimir_histopatologia($empresa,$mes_plano,$atencion,$seguro='2'){
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $formulario013 = HcFormulario013::where('id_hc_procedimiento',$id_procedimiento)->first();
            if(!is_null($formulario013)){
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

                $biopsias = Hc4_Biopsias::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();
                $procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
                $fecha_toma = date_format(new DateTime($formulario013->fecha_toma), 'd/m/Y'); 
                $fecha = date_format(new DateTime($procedimiento->created_at), 'Y-m-d'); 
                $hora = date_format(new DateTime($procedimiento->created_at), 'H:i:s');
                $doctor = User::find($procedimiento->id_doctor_examinador);
                $view = View::make('hc_admision.formulario013.formulario013_pdf',compact('age','diagnosticos' ,'fecha_toma', 'value_descripcion','cie10','formulario013', 'paciente', 'empresa', 'seguro_nombre', 'biopsias', 'procedimiento', 'fecha','hora','doctor'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('formulario_013' . '.pdf');
            }
            else{
                return 'No se encontro formulario 013';
            }
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function factura_descarga($empresa,$mes_plano,$atencion)
    {
        $archivo = ApArchivoFactura::where('id_empresa',$empresa)->where('mes_plano',$mes_plano)->where('estado',1)->first();
        $nombre_archivo = "";
        if(!is_null($archivo)){
            $nombre_archivo = $archivo->nombre;
            $path            = url('storage/app/facturas_auditoria/' . $nombre_archivo);
            return $path;
        }
        else{
            return 'no se encontro el archivo';
        }
    }
    
    //funcion de prueba
    public function pruebaDemonio(Request $request){
        ini_set('max_execution_time',300);
        //$text = explode('|',$request->parametro);
        $trama = $request->parametro;
        $enviar     = explode("torbi10", $trama);
        
        $contNum = 0;
        $contLet = 0;
        for ($i=0; $i < count($enviar); $i++) { 
            $valores = explode("|",$enviar[$i]);
            
        }
        sleep(10);

        
        return 'Total lineas: ' . count($enviar) . ', Tiene letras: ' . $contLet . ', Tiene numeros: ' . $contNum;
    }

    public function imprimir_estudio($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $tipo = '1';
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
                if(!is_null($aud_protocolo)){
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if(!is_null($protocolo)) {
                        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('seleccionado', '1')->where('estado', '1')->orderBy('id', 'desc')->get();
                        $paciente = paciente::find($protocolo->historiaclinica->id_paciente);
                        $seguro   = Seguro::find($protocolo->procedimiento->id_seguro);
                    
                        if (!is_null($protocolo->procedimiento->id_doctor_examinador2)) {
                            $firma = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->get();
                            if (!is_null($seguro)) {
                                if ($seguro->tipo == '0') {
                                    if ($protocolo->procedimiento->id_empresa == '0992704152001') {
                                        if ($protocolo->procedimiento->id_doctor_examinador2 == '0924611882') {
                                            $firma = Firma_Usuario::where('id_usuario', '094346835')->get();
                                        }
                                    }
                                }
                            }
                        } else {
                            $firma = null;
                        }
                        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                        $procedimiento_completo = procedimiento_completo::find($protocolo->procedimiento->id_procedimiento_completo);
                        $historia               = Historiaclinica::find($protocolo->hcid);
                        $elasto = Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->procedimiento->id)->where('id_procedimiento', '26')->first();
            
                        if (!is_null($elasto)) {
                            $view = View::make('hc_admision.formato.resumen_elasto', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'elasto'))->render();
                        } else {
                            if ($tipo == 0) {
                                $view = View::make('hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 1) {
                                $view = View::make('hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 2) {
                                $view = View::make('hc_admision.formato.resumen_2_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 3) {
                                $view = View::make('hc_admision.formato.resumen_3_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 4) {
                                $view = View::make('hc_admision.formato.resumen_4_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 5) {
                                $view = View::make('hc_admision.formato.resumen_5_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 6) {
                                $view = View::make('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 7) {
                                $view = View::make('hc_admision.formato.resumen_7_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 8) {
                                $view = View::make('hc_admision.formato.resumen_8_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 9) {
                                $view = View::make('hc_admision.formato.resumen_9_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 10) {
                                $view = View::make('hc_admision.formato.resumen_10_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            }
                        }
                        $pdf = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        $agenda = $historia->agenda;
                        $txt_fecha = substr($agenda->fechaini,8,2).'_'.substr($agenda->fechaini,5,2).'_'.substr($agenda->fechaini,0,4);
                        return $pdf->stream('Estudio_' . $paciente->id . '_' . $paciente->apellido1 . '_' . $paciente->nombre1 .'_' . $txt_fecha . '.pdf');
                    }
                    else{
                        return 'No se encontrolo el protocolo';
                    }
                }
                else{
                    return 'No se encontro el protocolo de auditoria';
                }
            }
            else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    public function bitacora($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $this->recalcular_valor_ap_cab($plano_cabecera->id);
                $archivo_plano = Archivo_Plano_Cabecera::find($plano_cabecera->id);
                $empresa = Empresa::find($archivo_plano->id_empresa);
                $procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
                $convenio = Convenio::where('id_seguro',$archivo_plano->id_seguro)->where('id_empresa',$archivo_plano->id_empresa)->where('estado','1')->first();
                $view = View::make('archivo_plano.reportes.bitacora', compact('archivo_plano','procedimiento','empresa','convenio'))->render();
                $pdf  = App::make('dompdf.wrapper');
                // $pdf->setPaper('A4');
                $pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('bitacora.pdf');
            }else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    public function recalcular_valor_ap_cab($id){

        $archivo_plano = Archivo_Plano_Cabecera::find($id);
        
        $honor_medicos = Db::table('archivo_plano_detalle as apd')
        ->where('id_ap_cabecera', $archivo_plano->id)
        ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
        ->orderby('apd.porcentaje_honorario', 'desc')
        ->orderby('apt.secuencia', 'asc')
        ->where('apt.tipo_ex', 'HME')
        //->orWhere('apt.tipo_ex','P')
        ->where('apd.estado', '1')
        ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
        ->get();

        $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

        $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

        $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

        $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

        $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

        $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
        
        $liquidacion_total=0;
        $total_hm = 0;
        foreach ($honor_medicos as $value1){
            $total_hm += $value1->total;
        }
        $tota_med = 0;
        foreach ($medicinas as $value2){
            $tota_med += $value2->total;
        }
        $totalins = 0; 
        foreach ($insumos as $value3){
            $total_item = round($value3->subtotal, 4) + round($value3->porcentaje10, 4) + ($value3->subtotal * $value3->porcentaje_iva);
            $totalins += $total_item;        
        }
        $total_lab = 0; 
        foreach ($laboratorio as $value4){
            $total_lab += $value4->total;
        }
        $total_imagen = 0; 
        foreach ($imagen as $value5){
            $total_imagen += $value5->total; 
        }       
        $total_serv = 0; 
        foreach ($servicios_ins as $value6){
            $total_serv += $value6->total; 
        }
        $total_equi = 0;
        foreach ($equipos as $value7){
            $total_equi += $value7->total; 
        }              

        $liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $tota_med + $total_hm;	
        //dd($liquidacion_total);
        if(!is_null($archivo_plano)){
            $archivo_plano->update([
                'valor'  => $liquidacion_total,
                'total_hm' => $total_hm,
                'total_ins' => $totalins,
                'total_lab' => $total_lab,
                'total_imagen' => $total_imagen,
                'total_serv' => $total_serv,
                'total_equi' => $total_equi,
                'leido'      => 1,
            ]);
        }
        
        return "elt";
    }

    public function imprimir_nuevo_epicrisis($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $procedimiento = "";
        $firma         = "";
        $xtipo      = 'auditoria';

        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $aud_epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento',$plano_cabecera->id_hc_procedimimentos)->first();
            if(!is_null($aud_epicrisis)) {
                $epicrisis = Hc_Epicrisis::find($aud_epicrisis->id_hc_procedimiento);
                $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
                $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first();
                $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
                $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
                $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
                $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();
                if (!is_null($protocolo)) {
                    $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    if (!is_null($procedimiento)) {
                        $id_seguro  = $procedimiento->id_seguro;
                        $id_empresa = $procedimiento->id_empresa;
                        if ($procedimiento->id_seguro == null) {
                            $id_seguro = $historiaclinica->id_seguro;
                        }

                        if ($procedimiento->id_empresa == null) {
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
                    }
                }
                
                $tiene_receta = 'NO';
                $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
                if (!is_null($receta)) {
                    $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                    if ($receta_det > 0 || $receta->prescripcion != null) {
                        $tiene_receta = 'SI';
                    }
                }
                $tiene_receta = $epicrisis->receta;
                $nombre_doc = "";
                if (!is_null($firma)) {
                    $nombre_doc = User::find($firma->id_usuario);
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


                $data = $historiaclinica;
                $receta = hc_receta::where('id_hc', $data->hcid)->first();
                $view = View::make('pdf_hospital.formulario006', compact('receta', 'cie10ca', 'cie10', 'cie10e', 'data', 'epicrisis', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Epicrisis' . '.pdf');
            }
            else {
                return 'No se encontro epicrisis de auditoria';
            }
        }
        else {
            return 'No se encontro registro';
        }
        
        //dd($epicrisis->id);
        
    }

    public function imprimir_evolucion($id_empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$id_empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
            if(!is_null($protocolo)){
                $empresa    = Empresa::find($id_empresa);
                $tipo = 'auditoria';
                $protocolo_original = null;
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first();
                $pxs = $hc_procedimiento->hc_procedimiento_f;
                $proc = null;
                $imagen = null;
                foreach($pxs as $px){
                    if(!is_null($px->procedimiento->id_grupo_procedimiento)){
                        $proc = $px->procedimiento;
                    }
                }
                if($proc != null){
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }        
            
                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();
                if ($protocolo_original->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                }
                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso','INGRESO')->get();
                $firma = Firma_Usuario::where('id_usuario', $protocolo_original->procedimiento->id_doctor_ayudante_con)->first();
                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
                $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo','cie10','firma','orden_biopsias','imagen', 'empresa', 'protocolo_original'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('formulario017.pdf');
            }
        }
        
    }
    
    public function imprimir_anemnesis($id_empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$id_empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        $anamnesis = null;
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            if(!is_null($id_procedimiento)){
                $procedimientos_hc = hc_procedimientos::find($id_procedimiento);
                $id                = $procedimientos_hc->id_hc;
                $historia          = Historiaclinica::find($id);
                $anamnesis = Anamnesis::where('hcid',$id)->first();
                
            }
        }
        else{
            return 'No se encontro registro';
        }
        //dd($anamnesis->historiaclinica->evoluciones[0]->indicaciones);
        //dd($anamnesis);
        
        //$cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anamnesis->hcid)->get();
        if(is_null($anamnesis)){
            return 'No se encontro anamesis';
        }
        $cie10 = [];
        $code_cie10 = $plano_cabecera->cie10;
        $vt_c10 = Cie_10_3::find($code_cie10);
        //dd($vt_c10);
        if (is_null($vt_c10)) {
            $vt_c10 = Cie_10_4::find($code_cie10);
        }
        $vt_pre_def = $plano_cabecera->presuntivo_def;
        $procedimiento = hc_procedimientos::where('id_hc', $anamnesis->hcid)->first();
        //dd($procedimiento);
        $doctor = "";
        if (!is_null($procedimiento)) {

            if ($procedimiento->id_doctor_responsable == null) {
                $doctor = User::find($procedimiento->id_doctor_examinador);

            } else {
                $doctor = User::find($procedimiento->id_doctor_responsable);

            }
            //dd($doctor);
        }
        $view = View::make('pdf_hospital.formulario002',compact('anamnesis','cie10','doctor','vt_c10','vt_pre_def'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario002.pdf');
        //return view('pdf_hospital.formulario001');
    }
    public function formulario010_unificado($id_orden)
    {
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
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
        $tipo_usuario  = 11;
        $orden         = Examen_Orden::find($id_orden);
        $detalles      = $orden->detalles;
        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();
        $empresa = Empresa::find($orden->id_empresa);

        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

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
        if($orden->fecha_convenios != null){
            $fecha_evolucion = $orden->fecha_convenios;
        }
        if(!is_null($orden->protocolo)){
            if($orden->protocolo->pre_post == 'POST'){
                $fecha_evolucion = date('Y-m-d', strtotime('-1 day', strtotime($fecha_evolucion)));
            }
            
        }

        $evolucion = Historiaclinica::where('historiaclinica.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'historiaclinica.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            //->where('hc_evo.created_at','<=',$orden->fecha_orden)
            ->whereRaw("DATE(hc_evo.created_at) <= ?", [$fecha_evolucion])
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i=0;
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

        //parte2
        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
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
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $edad      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
        
        $diaNacimiento = $fechaNacimiento->format('d');
        $fecha=date('Y-m-d');
        $hora = date('H:i:s');
        $mesNacimiento = $fechaNacimiento->format('m');

        $view = View::make('pdf_hospital.formulario010', compact(
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
            'diagnosticos',
            'pct', 
            'detalle', 
            'resultados', 
            'agrupador', 
            'parametros', 
            'edad', 
            'ucreador', 
            'user', 
            'empresa', 
            'diaNacimiento',
            'mesNacimiento',
            'fecha',
            'hora'
        ))->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        $vt_name = '';
        if ($tipo_usuario == 11) {
            $vt_name = 'HCU_010.pdf';
            return $pdf->stream($vt_name);
        }
    }

    public function getCabeceraPlano($empresa,$mes_plano,$atencion,$seguro='2'){
        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        $paciente = null;
        $procConsul = null;
        $laboratorio = null;
        if(!is_null($archivo_plano)){
            $paciente = Paciente::where('id', $archivo_plano->id_paciente)
            ->select(DB::raw("CONCAT(nombre1, IF(nombre2 != '(N/A)', CONCAT(' ', nombre2), ''), ' ', apellido1, IF(apellido2 != '(N/A)', CONCAT(' ', apellido2), '')) as nombre_paciente"))
            ->first();
            if(!is_null($paciente)){
                $paciente = $paciente->nombre_paciente;
            }
            
            $historiaClinica = Historiaclinica::find($archivo_plano->id_hc);
            if(!is_null($historiaClinica)){
                $agenda = Agenda::find($historiaClinica->id_agenda);
                if(!is_null($agenda)){
                    $procConsul = $agenda->proc_consul;
                    $fecha_ini = date('Y/m/d', strtotime($agenda->fechaini));
                    $orden = Examen_Orden::join('seguros as s','s.id', 'examen_orden.id_seguro')
                    ->whereRaw("DATE(examen_orden.created_at) = ?", [$fecha_ini])
                    ->where('examen_orden.id_paciente', $agenda->id_paciente)
                    ->where('s.tipo', 0)
                    ->get();
                    if(count($orden)>0){
                        $laboratorio = 1;
                    }
                    else{
                        $laboratorio = 0;
                    }
                    //dd($agenda, $fecha_ini,$orden, $laboratorio);
                }
            }
        }
        return response()->json(['paciente' => $paciente, 'procConsul' => $procConsul, 'laboratorio' => $laboratorio]);
    }

    //FORMULARIO002
    public function descargar_anamnesis($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($archivo_plano)){
            $this->crear_anamnesis($archivo_plano->id_hc);
            $anamnesis = Anamnesis::where('hcid',$archivo_plano->id_hc)->first();
            if(!is_null($anamnesis)){
                $cie10 = Aud_Hc_Cie10::where('aud_hc_cie10.hcid',  $anamnesis->hcid)->get();
                $procedimiento = hc_procedimientos::where('id_hc', $anamnesis->hcid)->first();
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
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_002.pdf');
            }
            else{
                return 'No se encontro anamnesis';
            }
        }
        else{
            return 'No se encontro archivo plano';
        }
    }
    
    public function descargar_cedula($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $paciente = Paciente::find($plano_cabecera->id_paciente);
                $copia_ced = Paciente_Biopsia::where('id_paciente', $paciente->id)->where('estado', 3)->first();
                if (!is_null($copia_ced)) {
                    $pathtoFile = storage_path() . '/app/avatars/' . $copia_ced->nombre;
                    return response()->file($pathtoFile);
                }
                else{
                    return 'No se encontro el archivo';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    public function descargar_cobertura($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento','COB')->first();
                    if(!is_null($archivo_historico)){
                        $name = storage_path('app/').$archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) { 
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        }
                        else{
                            return 'No se encontro el archivo';
                        }
                    }
                    else{
                        return 'No se encontro el archivo historico';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
        
    }

    public function guardarLogDescarga(Request $request){
        try {
            $arr_log= [
                'documento' => $request['fileName'],
                'ruta_firma' => $request['ruta_token'],
                'pos_sign' => $request['signPos'],
                'razon' => $request['reason'],
                'ubicacion' => $request['location'],
                'algoritmo' => $request['algoritmo'],
                'descripcion' => $request['descripcion'],
                'id_usuario' => $request['id_usuario'],
                'ip_usuario' => $request['ip_usuario'],
            ];
            LogDocumentoFirmado::create($arr_log);
            return ['status' => 'success', 'msj' => 'Guardado con exito'];
        } catch (Exception $ex) {
            return ['status' => 'error', 'msj' => $ex->getMessage()];
        }
    }

    public function getEmpresaPlanillaje(Request $request){
        $empresa = Empresa::find($request->id_empresa,[
            'id',
            'razonsocial',
            'nombrecomercial',
            'clave_firma_ofi',
            'clave_firma_sop',
        ]);
        if(!is_null($empresa)){
            return json_encode(['status' => 'success', 'msj' => 'Ejecutado con exito', 'data' => $empresa]);
        }
        else{
            return json_encode(['status' => 'error', 'msj' => 'Empresa no encontrada']);
        }
    }
    public function indexPasswordSign(){
        return view('archivo_plano.factura_mesplano.index_save_password');
    }

    public function storePasswordSign(Request $request){
        try {
            
            $id_empresa = session('id_empresa');
            $empresa = Empresa::find($id_empresa);
            $tipo = $request->tipo;
            $clave = $request->clave;//;bcrypt($request->clave);
            // $key = substr(hash('sha256', 'mi_clave_segura', true), 0, 32);
            // $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));

            // $encryptedPassword = encrypt($clave, $key, $iv);
            // $ivBase64 = base64_encode($iv);
            if(!is_null($empresa)){
                if($tipo == 0){
                    $arr_empresa = [
                        'clave_firma_ofi' => $clave,
                    ];
                }
                else{
                    $arr_empresa = [
                        'clave_firma_sop' => $clave,
                    ];
                }
                $empresa->update($arr_empresa);
                return ["status" => "success", "msj" => 'Actualizado con exito'];
            }
            else{
                return ["status" => "error", "msj" => 'Verifique la sesion'];
            }
        } catch (Exception $ex) {
            return ["status" => "error", "msj" => $ex->getMessage()];
        }
    }

    function encrypt($plaintext, $key, $iv) {
        $cipher = "aes-256-cbc";
        $encrypted = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
        return base64_encode($encrypted);
    }
    public function crear_anamnesis($hcid)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = '9999966654';
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
        return $anamnesis->id;
    }

    public function cotizador_imprimir_2($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalles   = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        //$membresia_controller = new MembresiasLabsController;
        //$res = $membresia_controller->buscar_membresia($orden->id_paciente);
        $res = null;
        $view = \View::make('laboratorio.orden.cotizacion_pdf2', compact('orden', 'detalles', 'age', 'forma_pago', 'res'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        //$pdf->setOptions(['isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('cotizador-' . $id . '.pdf');
        /*return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cotizador-' . $id . '.pdf', ['Content-Type' => 'application/pdf']);*/
    }

    //ORDEN012
    public function descargar_012($empresa,$mes_plano,$atencion)
    {
        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',2)->where('estado',1)->first();
        if(!is_null($archivo_plano)){

            if(str_contains($archivo_plano->nom_procedimiento, 'ECOGRA')){ //SOLO PARA ECOGRAFIAS

                $fecha_desde = $archivo_plano->fecha_ing;
                $fecha_desde = substr($fecha_desde,0,10);
                //BUSCAR ORDEN012
                $orden = Orden::where('id_paciente',$archivo_plano->id_paciente)->where('estado','1')->where('fecha_orden','<=',$fecha_desde.' 23:59:59')->where('tipo_procedimiento','2')->orderBy('fecha_orden','DESC')->first();
                if(!is_null($orden)){
                    $orden_012 = Orden_012::where('estado', '1')->where('id_orden',$orden->id)->first();
                   
                    if(!is_null($orden_012)){
                        
                    }else{
                        $orden_012 = $this->crea_orden012($orden->id);
                        if($archivo_plano->id == '79192'){
                            //dd($orden_012, $orden);
                        }
                        
                    }
                    return $this->imprime_orden012($orden_012->id);
                }else{
                    return "error, sin orden generada";
                }
            }else{
                return "NO ES ECOGRAFIA";
            }

            return "ok";
        }
    }

    public function imprime_orden012($id){

        $orden012 = Orden_012::find($id);
        $variables = $orden012;
        $cie10   = Orden_012_Cie10::where('id_orden_012', $id)->get();
        $firma = Firma_Usuario::where('id_usuario', $orden012->id_doctor_firma)->first();
        $view = View::make('pdf_hospital.formulario12A', compact("variables", "cie10", "firma"))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
       
        $pdf->loadHTML($view);
        return $pdf->stream('formulario12A.pdf');
        
    }

    public function crea_orden012($id_orden){

        $ordenp = Orden::find($id_orden);
        if(is_null($ordenp)){
            return null;
        }

        $ip_cliente = '1';
        $idusuario  = '9999966654';
        $id_evolucion = $ordenp->id_evolucion;
        if($id_evolucion == null){
            $fecha_desde = substr($ordenp->fecha_orden,0,10);

            $evolucion =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $ordenp->id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->where('hc_evo.created_at', '<=', $fecha_desde. ' 23:59:59')
                        ->select('hc_evo.*')
                        ->first();

            if($id_orden == '38291'){
                //dd($evolucion);
            }           

            if(!is_null($evolucion)){
                $ordenp->update([
                    'id_evolucion' => $evolucion->id,
                    'motivo_consulta' => $evolucion->motivo,
                    'resumen_clinico' => $evolucion->cuadro_clinico,
                ]);

                $id_evolucion = $evolucion->id;
            }            
        }
        
        if(!is_null($id_evolucion)){

            $evolucion = Hc_Evolucion::find($id_evolucion);
            $hcid            = $evolucion->hcid;
            $historiaclinica = Historiaclinica::find($hcid);

            //Obtenemos datos del Paciente
            $paciente = $historiaclinica->paciente;
            //Obtenemos datos de la Agenda
            $agenda = $historiaclinica->agenda;
            //Obtenemos una parte de la Fecha de la Agenda
            $fecha_agenda = substr($agenda->fechaini, 0, 10);

            $orden_012 = Orden_012::where('id_hc_evolucion', $id_evolucion)
                ->where('estado', '1')
                ->where('id_orden',$id_orden)
                ->first();

            if(is_null($orden_012)) {
    
                $arr = [
                    'id_paciente'        => $paciente->id,
                    'id_doctor_solicita' => $ordenp->id_doctor,
                    'id_doctor_firma'    => $ordenp->id_doctor,
                    'fecha_orden'        => $agenda->fechaini,
                    'id_hc_evolucion'    => $id_evolucion,
                    'id_orden'           => $id_orden,
                    'estado'             => '1',
                    'rutina'             => '1',
                    'puede_mover'        => '1',
                    //'descripcion' => $ordenes->descripcion,  Esta mal la Definicion
                    'motivo'             => $evolucion->motivo, //Evolucion Motivo
                    'id_empresa'         => $agenda->id_empresa,
                    'cuadro_clinico'     => $evolucion->cuadro_clinico, //Evolucion Cuadro Clinico
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,

                ];

                //Insertamos en la Tabla Orden_012 y Obtenemos el id de la Tabla
                $orden_012 = Orden_012::create($arr);

                //Una vez Insertado Consultamos la Tabla orden_012
                $id_012 = $orden_012->id;

                $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)
                    ->groupBy('cie10')
                    ->get();

                if (!is_null($cie10)) {

                    foreach ($cie10 as $value) {

                        $arr_cie10 = [
                            'id_orden_012'          => $id_012,
                            'cie10'                 => $value->cie10,
                            'presuntivo_definitivo' => $value->presuntivo_definitivo,
                            'id_usuariocrea'        => $idusuario,
                            'id_usuariomod'         => $idusuario,
                            'ip_creacion'           => $ip_cliente,
                            'ip_modificacion'       => $ip_cliente,

                        ];

                        Orden_012_Cie10::create($arr_cie10);

                    }

                }

                //Consultamos si existe el ID de la Tbl orden_012
                $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)
                    ->get();

                return $orden_012;    
    
            }

        }    


    }
    public function descargar_receta($empresa,$mes_plano,$atencion)
    {
        $tipo = 5;
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',2)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historia = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historia)){
                    $receta   = hc_receta::where('id_hc',$historia->hcid)->first();
                    if(!is_null($receta)){
                        $paciente = paciente::find($historia->id_paciente);
                        if(!is_null($paciente)){
                            $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
                            $logo_emp = Empresa::find($empresa);
                            $principioActivo = "";
                            if (Count($pacienteAlergia) > 0) {
                                foreach ($pacienteAlergia as $value) {
                                    if ($principioActivo == "") {
                                        $principioActivo = $value->principio_activo->nombre;
                                    } else {
                                        $principioActivo = $principioActivo . ", " . $value->principio_activo->nombre;
                                    }
                                }
                            } else {
                                $principioActivo = "NO TIENE";
                            }
                            $edad = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
                            $detalles  = hc_receta_detalle::where('id_hc_receta', $receta->id)->get();
                            if ($tipo == 5) {
                                $detalles  = hc_receta_detalle::where('id_hc_receta', $receta->id)->join('medicina_tarifario as mt', 'mt.id_medicina', 'hc_receta_detalle.id_medicina')->get();
                            }
                            if ($tipo == 6) {
                                $detalles = hc_receta_detalle::where('id_hc_receta', $receta->id)
                                    ->whereNotIn('hc_receta_detalle.id_medicina', function ($query) {
                                        $query->select('id_medicina')->from('medicina_tarifario');
                                    })->get();
                            }
                            $medicinas = Medicina::all();
                            $cie10     = hc_cie10::where('hcid', $historia->hcid)->get();
                            $firma     = null;
                            if (!is_null($receta) && is_null($firma)) {
                                $id_doctor = $receta->id_doctor_examinador;
                                $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
                            }
                    
                            if (!is_null($historia->hc_procedimientos) && is_null($firma)) {
                                $id_doctor = $historia->hc_procedimientos->id_doctor_examinador;
                                $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
                            }
                            if (is_null($firma)) {
                                $firma = Firma_Usuario::where('id_usuario', $historia->id_doctor1)->first();
                            }
                    
                            if ($tipo == 2) {
                                $view = View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'logo_emp'))->render();
                            }
                            if ($tipo == 1) {
                                $view = View::make('hc_admision.receta.sinmenbrete_nuevo_preimpreso', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();
                            }
                            if ($tipo == 3) {
                                $view = View::make('hc_admision.receta.menbretada_crm_auditoria', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }
                            if ($tipo == 5) {
                                $view = View::make('hc_admision.receta.receta_iess', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }
                    
                            if ($tipo == 6) {
                                $view = View::make('hc_admision.receta.receta_privada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }
                    
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view)->setPaper('a4', 'landscape');
                    
                            return $pdf->stream($historia->id_paciente . '_Receta_' . $receta->id . '.pdf');
                        }
                        else{
                            return 'No se encontro el paciente';
                        }
                    }
                    else{
                        return 'No se encontro la receta';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
        
    }

    public function imprimir_nota_entrega($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $hc = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($hc)){
                    $porIva = Utilidades::porcentajeIva();
                    $nota_entrega    = Nota_entrega::where('id_hc',$hc->hcid)->first();
                    if(!is_null($nota_entrega)){
                        $nota_entrega_detalle   = $nota_entrega->detalles;
                        $agenda = Agenda::findOrFail($hc->id_agenda);
                        $vistaurl = "hc_admision.receta.pdf_nota_entrega";
                        $view     = View::make($vistaurl, compact('nota_entrega', 'nota_entrega_detalle', 'hc', 'agenda', 'porIva'))->render();
                        $pdf = App::make('dompdf.wrapper');
                        $pdf->loadHTML($view);
                        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                        $pdf->setPaper('A4', 'portraint');
                        return $pdf->stream('Nota de Entrega - ' . $nota_entrega->numero . '.pdf');
                    }
                    else{
                        return 'No se encontro nota de entrega';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    public function descargar_protocolo_quirurgico($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
            if(!is_null($protocolo)){
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first(); //dd($protocolo_original, $hc_procedimiento);
                $empresa = $protocolo->procedimiento->empresa;
                $pxs = $hc_procedimiento->hc_procedimiento_f; //dd($pxs);
                $proc = null;
                $imagen = null;
                foreach ($pxs as $px) {
                    if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                        $proc = $px->procedimiento;
                    }
                }
                if ($proc != null) {
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }

                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                }

                $input = [
                    'fecha_operacion'     => $protocolo->fecha_operacion,
                    'hora_inicio'         => $protocolo->hora_inicio,
                    'hora_fin'            => $hora_fin,
                    'tipo_anestesia'      => $protocolo->tipo_anestesia,
                    'intervalo_anestesia' => $duracion,
                    'id_anestesiologo'    => $protocolo->id_anestesiologo,
                ];

                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
                $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
                $protocolo->update($input);

                $input2 = [
                    'id_doctor_examinador2'  =>  $protocolo->procedimiento->id_doctor_examinador,
                    'id_doctor_ayudante_con' => $protocolo->procedimiento->id_doctor_ayudante_con,
                ];
                $protocolo->procedimiento->update($input2);

                $firma = Firma_Usuario::where('id_usuario',  $protocolo->procedimiento->id_doctor_examinador)->first();

                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
                //dd($orden_biopsias);

                $xtipo = 'auditoria';
                $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'cie10_eg', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original', 'xtipo'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_017.pdf');
            }
            else{
                return 'No se encontro protocolo';
            }
            
        }
        else{
            return 'No se encontro registro';
        }
        
    }

    public function descargar_epicrisis_nuevo($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $procedimiento = "";
        $firma         = "";
        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento',$plano_cabecera->id_hc_procedimimentos)->first();
            if(!is_null($epicrisis)) {
                $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
                $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first();
                $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
                $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
                $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
                $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();
                $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();
                $cie10e = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();
                
                $doctor = null;
        
                if (!is_null($protocolo)) {
                    $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
                    if (!is_null($procedimiento)) {
                        $id_seguro  = $procedimiento->id_seguro;
                        $id_empresa = $procedimiento->id_empresa;
                        if ($procedimiento->id_seguro == null) {
                            $id_seguro = $historiaclinica->id_seguro;
                        }
                        if ($procedimiento->id_empresa == null) {
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
                
                $tiene_receta = 'NO';
                $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
                if (!is_null($receta)) {
                    $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                    if ($receta_det > 0 || $receta->prescripcion != null) {
                        $tiene_receta = 'SI';
                    }
                }
                $tiene_receta = $epicrisis->receta;
                $nombre_doc = "";
                if (!is_null($firma)) {
                    $nombre_doc = User::find($firma->id_usuario);
                }
                $data = $historiaclinica;
                $receta = hc_receta::where('id_hc', $data->hcid)->first();
                $view = View::make('pdf_hospital.formulario006', compact('receta', 'cie10ca', 'cie10', 'cie10e', 'data', 'epicrisis', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento', 'epicrisis_original'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Epicrisis' . '.pdf');
            }
            else{
                return 'Epicrisis no encontrado';
            }
        }
        else{
            return 'No se encontraron resultados';
        }
    }

    public function descargar_validacion($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento','LA_AS400')->first();
                    if(!is_null($archivo_historico)){
                        $name = storage_path('app/').$archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) { 
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        }
                        else{
                            return 'No se encontro el archivo';
                        }
                    }
                    else{
                        return 'No se encontro el archivo historico';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
        
    }

    public function descargar_hc_as400($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento','HC_AS400')->first();
                    if(!is_null($archivo_historico)){
                        $name = storage_path('app/').$archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) { 
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        }
                        else{
                            return 'No se encontro el archivo';
                        }
                    }
                    else{
                        return 'No se encontro el archivo historico';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
        
    }

    public function descargar_scan($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $agenda = Agenda::find($historiaClinica->id_agenda);
                    if(!is_null($agenda)){
                        $agenda = AgendaScan::where('id_agenda', $agenda->id)->first();
                        $name   = $agenda->archivo;
                        $path   = storage_path() . '/app/agenda_scan/' . $name;
                        if (file_exists($path)) {
                            return Response::download($path);
                        }
                        else{
                            return 'No se encontro el archivo';
                        }
                    }
                    else{
                        return 'No se encontro la agenda';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }  
        
    }
    public function descargar_cobertura_privada($empresa,$mes_plano,$atencion,$seguro='2')
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento','CBRPRV')->first();
                    if(!is_null($archivo_historico)){
                        $name = storage_path('app/').$archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) { 
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        }
                        else{
                            return 'No se encontro el archivo';
                        }
                    }
                    else{
                        return 'No se encontro el archivo historico';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
        
    }

    public function buscar_agenda_as400($empresa,$mes_plano,$atencion,$seguro='2'){
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
            if(!is_null($plano_cabecera)){
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if(!is_null($historiaClinica)){
                    $agenda    = Agenda::find($historiaClinica->id_agenda);
                    $id_archivos = [];
                    if(!is_null($agenda)){
                        $archivos = Agenda_archivo::where('id_agenda', $agenda->id)->get();
                        $cont = 0;
                        foreach ($archivos as $value) {
                            $id_archivo = $value->id;
                            if(!is_null($id_archivos)){
                                $id_archivos[$cont] = $id_archivo;
                                $cont++;
                            }
                        }
                        return $id_archivos;
                    }
                    else{
                        return 'No se encontro el archivo historico';
                    }
                }
                else{
                    return 'No se encontro historia';
                }
            } else{
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    public function descargar_archivo_as400($id){
        $archivo = Agenda_archivo::find($id);
        if(!is_null($archivo)){
            $name = storage_path('app/').$archivo->ruta . $archivo->archivo;
            if (file_exists($name)) { 
                return Response::make(file_get_contents($name), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $archivo->archivo . '"',
                ]);
            }
            else{
                return 'No se encontro el archivo';
            }
        }
        else{
            return 'No se encontro agenda archivo';
        }
    }

    public function imprimir_formulario012_unificado($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $procedimiento = "";
        $firma         = "";
        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $ap_orden = Ap_Orden::where('id_ap_cabecera',$plano_cabecera->id)->first();
            if(!is_null($ap_orden)){
                //A
                $orden012 = Orden_012::find($ap_orden->id_orden_012);
                $cie10   = Orden_012_Cie10::where('id_orden_012', $orden012->id)->get();
                $id_procedimiento = $ap_orden->id_hc_procedimientos;
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
                if(!is_null($aud_protocolo)){
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if(!is_null($protocolo)) {
                        //B
                        $especialidad = Especialidad::find($protocolo->historiaclinica->agenda->espid);
                        $id_empresa = $plano_cabecera->id_empresa;
                        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
                        $firma = null;
                        $ruta_firma = "";
                        if(!is_null($protocolo->procedimiento->doctor_firma)){
                            $firma = Firma_Usuario::where('id_usuario',$protocolo->procedimiento->doctor_firma->id)->first();
                            if(!is_null($firma)){
                                $ruta_firma = base_path().'/storage/app/avatars/' . $firma->nombre;
                            }
                        }
                        $view = View::make('pdf_hospital.formulario12_unificado', compact("orden012", "cie10",'protocolo','especialidad', 'empresa', 'ruta_firma'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        //$pdf->setPaper('A4', 'landscape');
                        $pdf->loadHTML($view);
                        return $pdf->stream('formulario012.pdf');
                    }
                    else{
                        return 'No se encontrolo el protocolo';
                    }
                }
                else{
                    return 'No se encontro el protocolo de auditoria';
                }    
            }
            else{
                return 'No se encontro ap_orden';
            }          
        }
        else{
            return 'No se encontraron cabecera plano';
        }
        
    }

    public function descargar_interconsulta_unificado($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $mes_plano = $plano_cabecera->mes_plano;
            $mes = substr($mes_plano, 0, 2);
            $anio = substr($mes_plano, 2, 4);
            $fecha_desde = Carbon::createFromFormat('Y-m-d H:i:s', "$anio-$mes-01 00:00:00");
            $fecha_hasta = $fecha_desde->copy()->endOfMonth()->setTime(23, 59, 59);
            $id_empresa = $plano_cabecera->id_empresa;
            $id_seguro = $plano_cabecera->id_seguro;
            $id_paciente = $plano_cabecera->id_paciente;
            $espid = 8;
            
            $historia_clinica = Historiaclinica::join('agenda as a', 'a.id','historiaclinica.id_agenda')
                ->whereBetween('a.fechaini', [$fecha_desde,$fecha_hasta])
                ->where('a.id_empresa',$id_empresa)
                ->where('historiaclinica.id_seguro', $id_seguro)
                ->where('historiaclinica.id_paciente',$id_paciente)
                ->where('a.espid', $espid)
                ->select('historiaclinica.*')
                ->first();

            if(!is_null($historia_clinica)){
                $empresa = Empresa::find($id_empresa);
                $paciente = $historia_clinica->paciente;
                $cardio = $historia_clinica->cardio;
                $evolucion_orden = $cardio->evolucion_orden;
                $cie10_orden = Hc_cardio_cie10::where('id_cardio', $cardio->id)->where('estado', '1')->get();
                $interconsulta = Interconsulta::where('id_hc', $historia_clinica->hcid)->where('estado', '1')->first();
                if (is_null($interconsulta)) {
                    $interconsulta = $this->crear_interconsulta($historia_clinica, $cardio);
                    $this->crear_diagnosticos_interconsulta($historia_clinica, $interconsulta);
                    $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                } else {
                    $interconsulta = $this->actualiza_interconsulta($historia_clinica, $cardio, $interconsulta);
                    $this->crear_diagnosticos_interconsulta($historia_clinica, $interconsulta);
                    $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                }
                $cie10 = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
                $vistaurl = "pdf_hospital.formulario007_unificado";
                $view = View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10', 'evolucion_orden', 'cardio', 'cie10_orden'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_007_' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
            }
            else{
                return 'No se encontro historia clinica';
            }
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function formulario12B($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();
            if(!is_null($aud_protocolo)){
                $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                if(!is_null($protocolo)) {
                    $id_empresa = $plano_cabecera->id_empresa;
                    $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
                    $firma = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->first();
                    $view = View::make('pdf_hospital.formulario12B', compact('protocolo', 'empresa', 'firma'))->render();
                    $pdf  = App::make('dompdf.wrapper');
                    $pdf->setPaper('A4');
                    //$pdf->setPaper('A4', 'landscape');
                    $pdf->loadHTML($view);
                    return $pdf->stream('formulario12B.pdf');
                }
                else{
                    return 'No se encontrolo el protocolo';
                }
            }
            else{
                return 'No se encontro el protocolo de auditoria';
            } 
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function descargar_protocolo($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_empresa = $plano_cabecera->id_empresa;
            $empresa    = Empresa::find($id_empresa);
            $xtipo      = 'auditoria';
            $protocolo_original = null;
            $hc_procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$hc_procedimiento->id)->first();
            if(!is_null($protocolo)){
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $pxs = $hc_procedimiento->hc_procedimiento_f;
                $proc = null;
                $imagen = null;
                foreach ($pxs as $px) {
                    if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                        $proc = $px->procedimiento;
                    }
                }
                if ($proc != null) {
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }

                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                }

                if ($xtipo == 'auditoria') {
                    $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
                    $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
                } else {
                    $cie10 = Hc_Cie10::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
                    $cie10_eg = Hc_Cie10::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
                }

                $firma = Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->id_doctor_examinador2)->first();

                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
                $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'cie10_eg', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original', 'xtipo'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_017.pdf');
            }
            else{
                return 'No se encontro protocolo de auditoria';
            }
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function formulario013B($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $id_empresa = $plano_cabecera->id_empresa;
            $empresa    = Empresa::find($id_empresa);
            $hc_procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
            if(!is_null($hc_procedimiento)){
                $biopsias =  Hc_informe_biopsia::where('id_hc_procedimiento', $hc_procedimiento->id)->get();
                if($biopsias =="[]"){
                    abort(404, 'Acceso no autorizado.');
                }
                
                $view = View::make('pdf_hospital.formulario13B', compact('biopsias'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('formulario13B.pdf');
            }
            else{
                return 'No se encontro procedimiento';
            }
        }
        else{
            return 'No se encontro registro';
        }
    }

    public function descargar_pre_anestesiologo($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($archivo_plano)){
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if(!is_null($hc_procedimiento)){
                $id_empresa = $archivo_plano->id_empresa;
                $empresa    = Empresa::find($id_empresa);

                $record = Hc_Anestesiologia::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                if(!is_null($record)){
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if(!is_null($pre_anestesico)){
                        $historia_clinica = Historiaclinica::where('hcid', $record->id_hc)->first();
                        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org',$hc_procedimiento->id)->first();
                        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos',$hc_procedimiento->id)->first();
                        //$cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $record->historia->hcid)->limit(2)->get();
                        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();
                        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
                        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
                        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
                        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
                        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
                        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
                        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();
                        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $record->id)->get();
                        $agrupados = array();
                        foreach ($examen as $exam){
                            if(!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)){
                                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
                            }
                        }
                        $agrupados1 = $agrupados;
                        $agrupados2 = [];
                        $totalAgrupadores = count($agrupados);
                        if($totalAgrupadores > 4){
                            // Calcular el tamaño de cada parte
                            $tamanoParte = ceil($totalAgrupadores / 2);
                            // Dividir el array en dos partes iguales
                            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true); // 'true' para mantener las claves asociativas
                            // Obtener la primera parte
                            $agrupados1 = $agrupadosDivididos[0];
                            // Obtener la segunda parte
                            $agrupados2 = $agrupadosDivididos[1];
                        }
                        $view = View::make('pdf_hospital.formulario018A', compact('cie10_eg','aud_protocolo','aud_hc_procedimiento','hc_procedimiento', 'historia_clinica', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'sistemas', 'aparatos', 'manejos', 'intubuaciones', 'tipos', 'rc_tecnicas', 'inducciones', 'mantenimientos', 'pre_anestesico','agrupados1','agrupados2'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        //$pdf->setPaper('A4');
                        $pdf->setPaper('A4', 'landscape');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_018A.pdf');
                    }
                    else{
                        return 'No se encontro pre_anestesico';
                    }
                }
                else{
                    return 'No se encontro record anestesico';
                }
            }
            else{
                return 'No se encontro procedimiento';
            }
        }
        else{
            return 'No se encontraron resultados';
        }        
    }

    public function nueva_evolucion_x_paciente($empresa,$mes_plano,$atencion,$seguro='2')
    {
        $plano_cabecera = Archivo_Plano_Cabecera::where('mes_plano',$mes_plano)->where('id_empresa',$empresa)->where('atencion',$atencion)->where('id_seguro',$seguro)->where('estado',1)->first();
        if(!is_null($plano_cabecera)){
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $plano_cabecera->id_hc_procedimimentos)->first();
            if(!is_null($protocolo)){
                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
    
                } elseif ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                }
               
                $procedimiento = $protocolo->auditoria_procedimiento;
    
                $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
            
                $id = $procedimiento->id_procedimientos_org;// ID PROC ORI
            
                $id_proc = $protocolo->auditoria_procedimiento->id;// ID PROC AUD
    
                $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();
            
                $indicaciones = [];
                foreach ($evolucion as $value) {
                    $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();
            
                }
    
                $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_procedimientos_org)->get();
        
                $id_principal  = '';
    
                foreach ($proc_finales as $px) {
                    if ($px->procedimiento->id_grupo_procedimiento != null) {
                        $id_principal = $px->id_procedimiento;
                        break;
                    }
                }
    
    
                $historiaclinica = DB::table('historiaclinica')
                ->where('historiaclinica.hcid', $procedimiento->id_hc)
                ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
                ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
                ->join('users', 'users.id', 'paciente.id_usuario')
                ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
                ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
                ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
                ->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'especialidad.nombre as enombre', 'historiaclinica.id_seguro as id_seguro')
                ->first();
                
                $data            = $historiaclinica;
                $fecha_operacion = $protocolo->fecha_operacion;
                $hora_ini        = $protocolo->hora_inicio;
                $cardiologia = null;
            
                if (count($evolucion) <= '1') {
                    $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
                    $pdf  = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }

                if ($id_principal != '') {
                    if ($protocolo->tipo_anestesia == '8' || $protocolo->tipo_anestesia == '9' || $protocolo->tipo_anestesia == '10' || $protocolo->tipo_anestesia == '11' || $protocolo->tipo_anestesia == '12') {
                        $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $protocolo->tipo_anestesia)->where('estado', '1')->first();
                        $minutos = $hor_f->minutos;                    
    
                        if ($minutos >= 1440) {
                            $fecha_final = strtotime("+$minutos minute", strtotime($protocolo->fecha_operacion));
                            $fecha_final = date('d/m/Y', $fecha_final);
                            $hora_fin    = strtotime("+$minutos minute", strtotime($protocolo->hora_inicio));
                            $hora_fin    = date('H:i', $hora_fin);
                            $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        } else {
                            if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2'){
                                $hora_ini = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                                $hora_ini = date('H:i', $hora_ini);
                            }
                            $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                            $hora_media = date('H:i', $hora_media);
                            $hora_fin   = strtotime("+$minutos minute", strtotime($protocolo->hora_inicio));
                            $hora_fin   = date('H:i', $hora_fin);
                            $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }
                    } else {
                        if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {
                            $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                            $hora_media = date('H:i', $hora_media);
                            $hora_fin   = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                            $hora_fin   = date('H:i', $hora_fin);
                            $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }
                        
                        if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                            $fecha_final = strtotime('+1440 minute', strtotime($protocolo->fecha_operacion));
                            $fecha_final = date('d/m/Y', $fecha_final);
                            $hora_fin    = strtotime('+1440 minute', strtotime($protocolo->hora_inicio));
                            $hora_fin    = date('H:i', $hora_fin);
                            $view        = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }
                    }
                }

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                
                    $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
    
                    $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                
                if ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $hora_media = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
             
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                if ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $hora_media = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                if ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $hora_media = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                if ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $hora_media = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+240 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                if ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $hora_media = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+270 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
    
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
                if ($protocolo->tipo_anestesia == 'SEDACION') {
                    $hora_media = strtotime('+30 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    
                }
              
                $pdf = App::make('dompdf.wrapper');
    
                $pdf->loadHTML($view);
    
    
                return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
            }
            else{
                return 'No se encontro protocolo';
            }

        }
        
    }

    public function getPacienteNombre($atencion,$id_empresa,$seguro,$mes_plano)
    {
      
        $cabeceras = Archivo_Plano_Cabecera::where('atencion', $atencion)->where('id_empresa',$id_empresa)->where('id_seguro',$seguro)->where('mes_plano',$mes_plano)->where('estado','1')->orderBy('fecha_ing','asc')->get();
        if(count($cabeceras) > 0){
            $cabecera = $cabeceras[0];
            $paciente = Paciente::where('id', $cabecera->id_paciente)
            ->select(DB::raw("CONCAT(apellido1, IF(apellido2 != '(N/A)', CONCAT(' ', apellido2), ''),' ',nombre1, IF(nombre2 != '(N/A)', CONCAT(' ', nombre2), '') ) as nombre_paciente"))
            ->first();
            if(!is_null($paciente)){
                $paciente = $paciente->nombre_paciente;
                $tildes = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ','N/A'];
                $sin_tildes = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'n', 'N',''];
                $paciente = str_replace($tildes, $sin_tildes, $paciente);
                return response()->json(['paciente' => $paciente]);
            } 
        }
        return response()->json(['paciente' => null]);
    }

    public function planilla_descargo_pdf($atencion,$id_empresa,$mes_plano,$seguro)
    {     
        $archivo_plano = Archivo_Plano_Cabecera::where('atencion', $atencion)->where('id_empresa',$id_empresa)->where('id_seguro',$seguro)->where('mes_plano',$mes_plano)->where('estado','1')->first();
        if(!is_null($archivo_plano)){
            $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get(); 

            $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva','apd.iva_unitario','apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

            $view = View::make('archivo_plano.planilla.informe_descargo', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos'))->render();

            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->download('informe_descargo.pdf');
        }
        return "no existe";
    }
}
