<?php

namespace Sis_medico\Http\Controllers\Hospital;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Sis_medico\Agenda;
use Sis_medico\Agenda_consentimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Documento;
use Sis_medico\Empresa;
use Sis_medico\Examen_Orden;
use Sis_medico\Firma_Usuario;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Hc_Cie10;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_estado_paso;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HoEstadoPaso;
use Sis_medico\Paciente;
use Sis_medico\Planilla;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Formulario_053;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;

class FormulariosPDFController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7)) == false) {
            return true;
        }
    }

    public function formulario001_pdf($id_solicitud){
        $solicitud = Ho_Solicitud::find($id_solicitud);
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);;
        if(!is_null($solicitud)){
            $data['id_solicitud'] = $id_solicitud;
            $ho_log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud',$solicitud->id)->whereNotNull('id_agenda')->get();
            $logs_solicitud = [];
            $i = 0;
            foreach($ho_log_solicitud as $key => $value){
                $log_solicitud = [];
                $cie10_ingreso = [];
                $cie10_salida = [];
                $paciente = Paciente::find($solicitud->id_paciente);
                $log_solicitud['cont'] = $i+1;
                $log_solicitud['id_log'] =  $value->id;
                $agenda = Agenda::find($value->id_agenda);
                $dias = 0;
                $fecha_ingreso = new DateTime($value->fecha_ingreso);
                $fecha_salida = null;
                if(!is_null($value->fecha_salida)){
                    $fecha_salida = new DateTime($value->fecha_salida);
                    $diff = $fecha_ingreso->diff($fecha_salida);
                    $dias = $diff->days;
                    $fecha_salida = date_format($fecha_salida, 'Y-m-d');
                }
                $fecha_ingreso = date_format($fecha_ingreso, 'Y-m-d');
                $log_solicitud['fecha_ingreso'] = $fecha_ingreso;
                $log_solicitud['fecha_salida'] = $fecha_salida;
                $log_solicitud['dias'] = $dias;

                if(!is_null($agenda)){
                    $log_solicitud['id_agenda'] = $value->id_agenda;
                    $historia = Historiaclinica::where('id_agenda',$agenda->id)->first();
                    if(!is_null($historia)){
                        $log_solicitud['id_historia'] = $historia->hcid;
                        $paso = HoEstadoPaso::find($value->estado_paso);
                        $hc_cie10_ingreso = Hc_Cie10::where('hcid',$historia->hcid)->where('ingreso_egreso','INGRESO')->first();
                        
                        if(!is_null($hc_cie10_ingreso)){
                            $cie10 = Cie_10_3::find($hc_cie10_ingreso->cie10);
                            if(is_null($cie10)){
                                $cie10 = Cie_10_4::find($hc_cie10_ingreso->cie10);
                            }

                            if(!is_null($cie10)){                                
                                $cie10_ingreso['descripcion'] = $cie10->descripcion;
                                $cie10_ingreso['cie10'] = $hc_cie10_ingreso->cie10;
                                $cie10_ingreso['definitivo'] = $hc_cie10_ingreso->presuntivo_definitivo;
                                $cie10_ingreso['id_paso'] = $value->estado_paso;
                                $cie10_ingreso['nombre_paso'] = "";
                                if(!is_null($paso)){
                                    $cie10_ingreso['nombre_paso'] = $paso->nombre_paso;
                                }
                                $log_solicitud['cie10_ingreso'] = $cie10_ingreso;
                            }
                        }
                        $hc_cie10_salida = Hc_Cie10::where('hcid',$historia->hcid)->where('ingreso_egreso','EGRESO')->first();
                        if(!is_null($hc_cie10_salida)){
                            $cie10 = Cie_10_3::find($hc_cie10_salida->cie10);
                            if(is_null($cie10)){
                                $cie10 = Cie_10_4::find($hc_cie10_salida->cie10);
                            }

                            if(!is_null($cie10)){
                                $cie10_salida['descripcion'] = $cie10->descripcion;
                                $cie10_salida['cie10'] = $hc_cie10_salida->cie10;
                                $cie10_salida['definitivo'] = $hc_cie10_salida->presuntivo_definitivo;
                                $cie10_salida['id_paso'] = $value->estado_paso;
                                $cie10_salida['nombre_paso'] = "";
                                if(!is_null($paso)){
                                    $cie10_salida['nombre_paso'] = $paso->nombre_paso;
                                }
                                $log_solicitud['cie10_salida'] = $cie10_salida;
                            }
                        }
                        
                        
                        $ruta_firma = "";
                        $firma = Firma_Usuario::where('id_usuario',$agenda->id_doctor1)->first();
                        if(!is_null($firma)){
                            $ruta_firma = base_path().'/storage/app/logo/' . $firma->nombre;
                        }
                        else{
                            $firma = Firma_Usuario::where('id_usuario',$agenda->id_doctor2)->first();
                            if(!is_null($firma)){
                                $ruta_firma = base_path().'/storage/app/logo/' . $firma->nombre;
                            }
                            else{
                                $firma = Firma_Usuario::where('id_usuario',$agenda->id_doctor3)->first();
                                if(!is_null($firma)){
                                    $ruta_firma = base_path().'/storage/app/logo/' . $firma->nombre;
                                }
                            }
                        }
                        $log_solicitud['firma_doctor'] = $ruta_firma;
                        $logs_solicitud[$i] = $log_solicitud;
                        $i++;                   
                    }
                }
            }
            $data['logs_solicitud'] = $logs_solicitud;
            //dd($data);
            $diff = null;
            $age = null;
            $fechaActual = new DateTime();
            $seguro = null;
            if(!is_null($paciente)){
                $seguro = Seguro::find($paciente->id_seguro);
                $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
                $diff = $fechaNacimiento->diff($fechaActual);
                $datos_paciente = Ho_Datos_Paciente::where('id_paciente',$paciente->id)->first();
                $edad = $diff->y;
                
            }         
            $view = View::make('pdf_hospital.formulario001',compact('data','empresa','edad','datos_paciente','seguro','paciente'))->render();
            $pdf  = App::make('dompdf.wrapper');
            //$pdf->setPaper('A4');
            $pdf->setPaper('A4', 'landscape');
            $pdf->loadHTML($view);
            return $pdf->stream('formulario001.pdf');
        }
        else{
            return 'No se encontro la solicitud';
        }
    }

    public function formulario022_pdf(){
        $view = View::make('pdf_hospital.formulario022')->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario022.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function documentos_pdf(Request $request, $id_paciente, $id_empresa, $id_agenda)
    {
        $agenda = Agenda::find($id_agenda);
        //$id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $paciente = paciente::find($id_paciente);
        $consentimiento = Formato_consentimiento::where('estado', 1)->get();

        return view('pdf_hospital/consentimiento_modal', ['paciente' => $paciente, 'consentimiento' => $consentimiento, 'empresa' => $empresa, 'agenda' => $agenda]);
    }
    
    public function descargar_pdf_consentimiento(Request $request)
    {
        //dd($request->all());
        $id_paciente   = $request->id_paciente;
        $formatoid  = $request->id_procedimiento;
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $date       = Date('Y-m-d');
        $paciente   = Paciente::find($id_paciente);
        $agenda = Agenda::find($request->id_agenda);
        //$id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($agenda->id_empresa);
        $formato = Formato_consentimiento::find($request->id_procedimiento);
        $imagen = base_path('public/imagenes/'.$formato->imagen_url);//public_path('imagenes\\' . $formato->imagen_url;
        // dd($imagen);
        $seguro = Seguro::find($paciente->id_seguro);
        $seguro_nombre = '';
        if(!is_null($seguro)){
            $seguro_nombre = strtoupper($seguro->nombre);
        }

        $edad = null;
        $age = 0;
        $fecha = date('d-m-Y');
        $hora = date('H:i:s');
        $fechaActual = new DateTime();
        if(!is_null($paciente)){
            $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
            $edad = $fechaNacimiento->diff($fechaActual);
            $age = $edad->y;
        }

        $view = View::make('pdf_hospital.formulario024', compact('date', 'paciente', 'consentimiento', 'agenda', 'formato', 'empresa', 'seguro_nombre', 'age', 'imagen', 'fecha', 'hora'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('A4', 'portrait');
        return $pdf->stream('Consentimiento informado prueba de VIH Vers. 0.2' . '.pdf');
    }


    public function formulario024_pdf(){
        /*$archivo_historico = Archivo_historico::find($ahid);
        $documento         = Documento::find($archivo_historico->id_documento);
        $historia          = $this->carga_hc($archivo_historico->id_historia);
        $agenda            = agenda::find($historia->id_agenda);
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
        }*/
        $view = View::make('pdf_hospital.formulario024')->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario024.pdf');
        //return view('pdf_hospital.formulario001');
    }

    public function formulario053_pdf(){
        $view = View::make('pdf_hospital.formulario053')->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            //$pdf->setPaper('A4', 'landscape');
            $pdf->loadHTML($view);
            return $pdf->stream('formulario053.pdf');
            //return view('pdf_hospital.formulario001');
    }
}
