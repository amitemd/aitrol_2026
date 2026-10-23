<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Agenda;
use Sis_medico\Empresa;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_Doctor;
use Sis_medico\HorarioDoctorApp;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Medicina;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\User;
use DateTime;
use DatePeriod;
use DateInterval;
use Mail;
use Sis_medico\Apps_Agenda;
use Sis_medico\Apps_Plan_Miembros;
use Sis_medico\Convenio;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Doctor_Tiempo;
use Sis_medico\Log_Apps;
use Sis_medico\Log_usuario;
use Sis_medico\LogPagoEnLinea;
use Sis_medico\LogReciboDeCobro;
use Sis_medico\Membresia;
use Sis_medico\TransaccionPago;
use Sis_medico\UserMembresia;
use Sis_medico\v3\contable\Productos;

class BdIecedController extends Controller
{
    public function listado_recetas(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_paciente = $request->id_paciente;
        $paciente = Paciente::find($id_paciente);

        $recetas1 = hc_receta::selectRaw("hc_receta.id, hc_receta.id_hc, hc_receta.prescripcion, hc_receta.rp, a.fechaini, s.nombre as seguro_nombre, IFNULL(hcp.id_doctor_examinador,h.id_doctor1) as id_doctor, h.fecha_atencion, h.id_paciente, if(hcp.id_doctor_examinador IS NOT NULL, CONCAT(u.nombre1, ' ',u.apellido1), IFNULL(hcp.id_doctor_examinador, CONCAT(us.nombre1, ' ', us.apellido1))) as nombre_doctor")
            ->join('historiaclinica as h', 'hc_receta.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('hc_procedimientos as hcp', 'h.hcid', 'hcp.id_hc')
            ->leftjoin('users as u', 'u.id', 'hcp.id_doctor_examinador')
            ->leftjoin('users as us', 'us.id', 'h.id_doctor1')
            ->where('h.id_paciente', $id_paciente)
            ->where('a.estado', 1)
            ->where('hcp.estado', 1)
            ->where('a.estado_cita', 4)
            ->orderBy('h.fecha_atencion', 'desc');

        $recetas2 = hc_receta::selectRaw("hc_receta.id, hc_receta.id_hc, hc_receta.prescripcion, hc_receta.rp, a.fechaini, s.nombre as seguro_nombre, h.id_doctor1 as id_doctor, h.fecha_atencion, h.id_paciente, IF(hc_receta.id_doctor_examinador IS NOT NULL, CONCAT(usr.nombre1, ' ', usr.apellido1), CONCAT(us.nombre1, ' ', us.apellido1)) as nombre_doctor")
            ->join('historiaclinica as h', 'hc_receta.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('users as us', 'us.id', 'h.id_doctor1')
            ->leftjoin('users as usr', 'usr.id', 'hc_receta.id_doctor_examinador')
            ->where('h.id_paciente', $id_paciente)
            ->where('a.estado', 4)
            ->where('a.estado_cita', 4)
            ->orderBy('h.fecha_atencion', 'desc');

        $recetas = $recetas1->union($recetas2)->orderBy('fecha_atencion', 'desc')->get();

        $arr = array();
        foreach ($recetas as $value) {
            $detalles = $value->detalles;
            if (count($detalles) > 0 or !is_null($value->rp) or !is_null($value->prescripcion)) {
                array_push($arr, $value);
            }
        }

        return ['recetas' => $arr, 'paciente' => $paciente];
    }

    public function pdf_receta($id, $tipo)
    {

        $receta   = hc_receta::find($id);
        $historia = Historiaclinica::find($receta->id_hc);
        $paciente = paciente::find($historia->id_paciente);
        $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
        $logo_emp = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        $id_empresa  = $logo_emp->id;

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
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::all();
        $cie10     = Hc_Cie10::where('hcid', $receta->id_hc)->get();
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

            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'id_empresa', 'logo_emp'))->render();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');

        // Headers para la respuesta PDF 
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $historia->id_paciente . '_Receta.pdf"',
            'Cache-Control' => 'public, must-revalidate, max-age=0',
            'Pragma' => 'public',
            'Expires' => '0'
        ];
        // return $pdf->stream($historia->id_paciente . '_Receta_' . $id . '.pdf', array('Attachment' => false, 'inline' => true));
        return response($pdf->output(), 200, $headers);
    }

    public function view_receta($id, $tipo)
    {
        $url = "";
        $format = "";
        $file = "";
        $new_view = false;

        if ($tipo == 1) { //listado de recetas
            $url = url('bd/pdf/receta') . "/" . $id . "/2";
        }

        if ($tipo == 2) { //resultados de examenes
            $url = url('pagina/resultados/externos/imprimir') . "/" . $id;
        }

        if ($tipo == 3 or $tipo == 4) { //listado de biopsias

            $documentos = null;
            if ($tipo == 3) {
                $documentos = paciente_biopsia::find($id);
            }
            if ($tipo == 4) {
                $documentos = hc_imagenes_protocolo::find($id);
            }
            $document = explode(".", $documentos->nombre);
            $format = $document[count($document) - 1];
            $file = 'https://ieced.siaam.ec/sis_medico/storage/app/hc_ima/' . $documentos->nombre;
            $new_view = true;
        }

        if ($tipo == 5) {
            $url = url('api/pdfEstudios') . "/" . $id . "/7";
        }

        if ($new_view and $format != "pdf") {
            return view('pdf_app.documents', ['id' => $id, "url" => $url, "format" => $format, "file" => $file]);
        }

        if ($tipo == 6) {
            $url = url('labs/cotizacion/externos/imprimir') . "/" . $id;
        }

        return view('pdf_app.index_receta', ['id' => $id, "url" => $url, "format" => $format, "file" => $file]);
        // return view('pdf_app.index_receta',['id'=> $id]);
    }

    public function download_receta($id, $tipo)
    {
        $receta   = hc_receta::find($id);
        $historia = Historiaclinica::find($receta->id_hc);
        $paciente = paciente::find($historia->id_paciente);
        $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
        $logo_emp = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        $id_empresa  = $logo_emp->id;

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
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::all();
        $cie10     = Hc_Cie10::where('hcid', $receta->id_hc)->get();
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

            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'logo_emp'))->render();
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete_nuevo_preimpreso', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();
        }

        if ($tipo == 3) {
            $view = \View::make('hc_admision.receta.menbretada_crm_auditoria', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');

        // return $pdf->stream($historia->id_paciente . '_Receta_' . $id . '.pdf');
        return $pdf->download('receta-' . $receta->id . '.pdf');
    }

    public function listado_biopsias(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_paciente = $request->id_paciente;
        $biopsias = Paciente_Biopsia::where('id_paciente', $id_paciente)
            ->join('paciente as p', 'p.id', 'paciente_biopsia.id_paciente')
            ->where('estado', '0')
            ->select('paciente_biopsia.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido1 as papellido1', 'p.apellido2 as papellido2')
            ->OrderBy('created_at', 'desc')->get();

        return $biopsias;
    }

    public function descarga_externo_biopsia($name)
    {
        $imagen          = paciente_biopsia::find($name);
        $paciente        = paciente::find($imagen->id_paciente);
        $nombre_paciente = null;
        $nombre_paciente = $paciente->apellido1;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre;
        $nombre_paciente = $nombre_paciente . '_' . $paciente->nombre1;
        //dd($path);

        if ($nombre_paciente == null) {
            $nombre_archivo = $imagen->nombre;
        } else {
            $nombre_temporal = $imagen->nombre;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_paciente . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre;
                }
            } else {
                $nombre_archivo = $imagen->nombre;
            }
        }
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function listado_proc_funcionales(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_paciente = $request->id_paciente;

        $pro_final_1 = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('paciente as p', 'p.id', 'historiaclinica.id_paciente')
            ->join('seguros as s', 's.id', 'hc_p.id_seguro')
            ->where('hc_proto.tipo_procedimiento', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.id as id_paciente', 'p.apellido1 as papellido1', 'p.apellido2 as papellido2', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'hc_p.id_seguro as hc_p_id_seguro', 's.nombre as nombre_seguro', 'a.fechaini as fecha_agenda')->OrderBy('historiaclinica.created_at', 'desc')->get();

        $arr_doc = [];
        foreach ($pro_final_1 as $value) {

            $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $value->id_protocolo)->orderBy('id', 'desc')->whereIn('estado', [2, 3])->get();
            $det_doc = [];
            foreach ($documentos as $doc) {
                $data['id'] = $doc->id;
                $data['id_hc_protocolo'] = $doc->id_hc_protocolo;
                $data['nombre'] = $doc->nombre;
                $data['ruta'] = 'storage/app/hc_ima/' . $doc->nombre;

                array_push($det_doc, $data);
            }

            $aux['id_paciente'] = $value->id_paciente;
            $aux['nombre_paciente'] =  $value->papellido1 . ' ' . $value->papellido2 . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
            $aux['id_agenda'] = $value->id_agenda;
            $aux['id_seguro'] = $value->hc_p_id_seguro;
            $aux['nombre_seguro'] = $value->nombre_seguro;
            $aux['fecha_operacion'] = $value->fecha_agenda;
            $aux['id_hc'] = $value->id_hc;
            $aux['id_procedimiento'] = $value->id_procedimiento;
            $aux['id_doctor_examinador'] = $value->id_doctor_examinador;
            $aux['nombre_doctor'] = $value->nombre1 . ' ' . $value->apellido1;
            $aux['detalles'] = $det_doc;

            array_push($arr_doc, $aux);
        }

        return $arr_doc;
    }

    public function lista_informes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $id_paciente = $request->id_paciente;
        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ['status' => 'error', 'msj' => 'Paciente no registrado'];
        }

        $historia = Historiaclinica::join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('users as u', 'u.id', 'historiaclinica.id_doctor1')
            ->where('historiaclinica.id_paciente', $id_paciente)
            ->where('a.proc_consul', '1')
            ->where('a.estado_cita', '4')
            ->where('a.estado', '1')
            ->select('historiaclinica.*', DB::raw('CONCAT(u.nombre1," ",u.apellido1) as doctor'))
            ->get();

        if (is_null($historia)) {
            return ['status' => 'error', 'msj' => 'No tiene historia clinica'];
        }
        $arr = array();
        foreach ($historia as $his) {
            $aux = array();
            $protocolos = hc_protocolo::where('hcid', $his->hcid)->get();
            foreach ($protocolos as $value) {
                $hc_procedimientos = hc_procedimientos::where('hc_procedimientos.id', $value->id_hc_procedimientos)->join('users as u', 'u.id', 'hc_procedimientos.id_doctor_examinador')->select('hc_procedimientos.id', DB::raw('CONCAT(u.nombre1," ",u.apellido1) as doctor'))->first();
                $data['url'] = url('bd/download/estudio/externo') . '/' . $value->id;
                $data['doctor'] = $hc_procedimientos->doctor;
                array_push($aux, $data);
            }

            $datos['hcid'] = $his->hcid;
            $datos['fecha_atencion'] = $his->fecha_atencion;
            $datos['urls'] = $aux;
            array_push($arr, $datos);
        }

        return ['status' => 'success', 'msj' => 'Estudio ok', 'data' => $arr];
    }
    //listado doctores para la app
    public function lista_doctor_app(Request $request)
    {
        if ($request->token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json(['result' => '0', 'message' => 'Token incorrecto', 'status' => 'error']);
        }
        $id_usuario = $request['id_usuario'];
        $list   = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('app', 1)->orderBy('nombre1', 'ASC')->get();
        $mebresia = UserMembresia::where('user_id', $id_usuario)->where('estado', 1)->where('membresia_id', '3')->first();
        $men = !is_null($mebresia) ? 1 : 0;
        $information = array();
        if ($request['day'] < 7) {
            foreach ($list as $l) {
                $valor_consulta = Doctor_Tiempo::where('id_doctor', $l->id)->first();

                $h = "";
                $price = !is_null($valor_consulta) ? $valor_consulta->precio : '0.00';
                $porcentaje = 25;
                $descuento = (($price * $porcentaje) / 100);
                $precio_mem = $price - $descuento;

                $data['fecha'] = $request['date'];
                $data['id_doctor'] = $l->id;
                $cant_agendas = BdIecedController::verificarAgenda($data);

                if ($cant_agendas['status'] == 'success') {

                    $shedule      = HorarioDoctorApp::where('id_doctor', $l->id)->where('tipo', 1)->where('ndia', $request['day'])->where('estado', 1)->first();
                    $caso_especial = Agenda::whereDate('fechaini', $request['date'])->where('estado', 1)->where('proc_consul', 2)->where('id_doctor1', $l->id)->first();
                    if (!is_null($shedule)) {
                        $h = '' . substr($shedule->hora_ini, 0, 5) . ' - ' . substr($shedule->hora_fin, 0, 5);
                    }

                    if ($h != "" && is_null($caso_especial)) {
                        $img = '';
                        if ($l->imagen_url != ' ' && !is_null($l->imagen_url)) {
                            $img = asset('') . '../storage/app/avatars/' . $l->imagen_url;
                        } else {
                            $img = asset('') . '../storage/app/avatars/avatar.jpg';
                        }
                        $pl = [
                            'name'      => $l->nombre1 . ' ' . $l->apellido1,
                            'id_doctor' => $l->id,
                            'price'     => $price,
                            'price_mem' => $precio_mem,
                            'porcentaje' => $porcentaje,
                            'valor_descuento' => $descuento,
                            'day'       => $h,
                            'height'    => $img
                        ];
                        array_push($information, $pl);
                    }
                }
            }
        }
        return ['status' => 'success', 'msj' => 'Correcto', "membresia" => $men, "membresia_global" => 1, 'list' => $information];
    }
    // funcion q carga la disponibilidad del doctor
    public function horario_doctor_app(Request $request)
    {
        $id = $request['id'];
        $id_usuario = $request['id_usuario'];
        $shedule      = HorarioDoctorApp::where('id_doctor', $id)->where('tipo', 1)->where('ndia', $request['day'])->where('estado', 1)->get();
        $valor_consulta = Doctor_Tiempo::where('id_doctor', $id)->first();
        $mebresia = UserMembresia::where('user_id', $id_usuario)->where('estado', 1)->where('membresia_id', '3')->first();
        $hoursfree = [];
        $xp = [];
        $contador = 0;
        foreach ($shedule as $sh) {
            $var1 = substr($sh->hora_ini, 0, 5);
            $var2 = substr($sh->hora_fin, 0, 5);
            $fechaInicio = new DateTime($var1);
            $fechaFin = new DateTime($var2);
            $fechaFin = $fechaFin->modify('+30 minutes');
            $rangoFechas = new DatePeriod($fechaInicio, new DateInterval('PT30M'), $fechaFin);
            $contador = 0;
            $maximo = iterator_count($rangoFechas) - 1;

            foreach ($rangoFechas as $fecha) {
                if ($contador < $maximo) {
                    $minutes_to_add = 30;
                    $time = new DateTime($request['date'] . ' ' . $fecha->format("H:i"));
                    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                    $stamp = $time->format('H:i');
                    $validAgenda = Agenda::whereBetween('fechaini', [$request['date'] . ' ' . $fecha->format("H:i") . ':00', $request['date'] . ' ' .  $stamp . ':00'])
                        ->where('id_doctor1', $id)
                        ->where('estado', 1)
                        ->where('observaciones', 'like', '%FUE PAGADO DESDE LA APP%')
                        ->where('proc_consul', 0)
                        ->count();

                    $p['inicio'] = $request['date'] . ' ' . $fecha->format("H:i") . ':00';
                    $p['id_doctor1'] = $id;
                    $validateMaximos = $this->validateMax1($p);

                    $data['fecha'] = $request['date'];
                    $data['id_doctor'] = $id;
                    $cant_agendas = BdIecedController::verificarAgenda($data);
                    if ($cant_agendas['status'] == 'error') {
                        return $cant_agendas['msj'];
                    }

                    if ($validAgenda == 0 && $validateMaximos > 0) {
                        if (!is_null($mebresia)) {
                            $porcentaje = 25;
                            $descuento = (($valor_consulta->precio * $porcentaje) / 100);
                            $precio_mem = $valor_consulta->precio - $descuento;
                            $price = $precio_mem;
                        } else {
                            $price = !is_null($valor_consulta) ? $valor_consulta->precio : '0.00';
                        }

                        $ph['hour'] = $fecha->format("H:i");
                        $ph['date'] = $request['date'] . ' ' . $fecha->format("H:i");
                        $ph['date2'] = $request['date'] . ' ' . $stamp;
                        $ph['id'] = $id;
                        $user = User::find($id);
                        $ph['name'] = $user->apellido1 . ' ' . $user->nombre1;
                        $ph['price'] = $price;
                        $ph['finally'] = $fecha->format("H:i") . ' - ' . $stamp;
                        array_push($hoursfree, $ph);
                    }
                }
                $contador++;
            }
        }

        return response()->json($hoursfree);
    }

    private function validateMax1($request)
    {
        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where('proc_consul', '=', 0)
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor = User::find($request['id_doctor1']);
        if ($doctor->max_consulta >= $cantidad) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function verificarAgenda($data)
    {
        try {
            $fecha = $data['fecha'];
            $id_doctor = $data['id_doctor'];
            $validAgenda = Agenda::whereBetween('fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
                ->where('id_doctor1', $id_doctor)
                ->where('estado', 1)
                ->where('proc_consul', 0)
                ->where('observaciones', 'like', '%FUE PAGADO DESDE LA APP%')
                ->count();

            if ($validAgenda >= 3) {
                return ['status' => 'error', 'msj' => 'No es posible agendar en la fecha seleccionada porque se ha llegado al límite establecido de agendas para este doctor'];
            }
            return ['status' => 'success', 'msj' => 'Agenda ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public static function VerificarDisponibilidad($data)
    {
        try {
            $fecha_ini = $data['fecha_ini'];
            $fecha_fin = $data['fecha_fin'];
            $id_doctor = $data['id_doctor'];

            $validar = Agenda::whereBetween('fechaini', [$fecha_ini, $fecha_fin])
                // ->join('seguros as s', 's.id', 'agenda.id_seguro')
                ->where('id_doctor1', $id_doctor)
                ->where('estado', 1)
                ->where('proc_consul', 0)
                // ->where('s.tipo', '!=', 0)
                ->first();
            if (!is_null($validar)) {
                return ['status' => 'error', 'msj' => 'Horario no disponible'];
            }
            return ['status' => 'success', 'msj' => 'Horario disponible'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function proxima_cita($data)
    {
        $id_doctor = $data['id_doctor'];
        $cita = Agenda::where('id_doctor1', $id_doctor)->where('estado', 1)->where('proc_consul', 0)->first();
    }

    public function beneficios_membresia(Request $request)
    {
        $empresa = Empresa::where('prioridad', 1)->first();
        $membresias = Membresia::where('tipo_app', 1)->where('empresa_id', $empresa->id)->first();
        $detalles = $membresias->detalles;
        $arr_det = array();
        foreach ($detalles as $det) {
            $aux['id'] = $det->id;
            $aux['nombre'] = $det->nombre;
            $aux['aplica'] = $det->aplica_proc_cons;
            $aux['porcentaje'] = round($det->porcentaje_descuento, 2);
            array_push($arr_det, $aux);
        }

        $arr['id'] = $membresias->id;
        $arr['nombre'] = $membresias->nombre;
        $arr['precio_anual'] = round($membresias->precio_anual, 2);
        $arr['detalles'] = $arr_det;
        return $arr;
    }

    public function envio_mail_agenda(Request $request)
    {
        try {
            $requestId = $request->requestId;
            $apps = Apps_Agenda::where("id_check_app", $requestId)->where('estado', 1)->first();
            $agenda = Agenda::find($apps->id_agenda);
            $paciente = User::find($agenda->id_paciente);
            $doctor = User::find($agenda->id_doctor1);
            $fecha = $agenda->fechaini;
            $asunto = "Cita Médica Confirmada";
            $titulo = "Agendamiento de Cita Médica";
            $url = $apps->url;

            if ($apps->tipo == 'PRESENCIAL') {
                Mail::send('iecedapps.cita_presencial', ['paciente' => $paciente, 'doctor' => $doctor, 'fecha' => $fecha, 'url' => $url], function ($msj) use ($paciente, $asunto, $titulo) {
                    $msj->subject($asunto);
                    $msj->from('rol@mdconsgroup.com', 'Sistema de Agendamiendo AITROL');
                    $msj->to($paciente->email);
                });
            } else {
                Mail::send('iecedapps.videoconsulta', ['paciente' => $paciente, 'doctor' => $doctor, 'fecha' => $fecha, 'url' => $url], function ($msj) use ($paciente, $asunto, $titulo) {
                    $msj->subject($asunto);
                    $msj->from('rol@mdconsgroup.com', 'Sistema de Agendamiendo AITROL');
                    $msj->to($paciente->email);
                });
            }
            return ['status' => 'success', 'msj' => 'Mail Enviado'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function crea_recibos(Request $request)
    {
        $recibo =  BdIecedController::creaReciboCobro($request['id_transaccion']);
        return $recibo;
    }

    public function crea_agendaMem(Request $request)
    {
        $data = $request['data'];
        $agenda = BdPagosController::agendaMembresia($data);
        return $agenda;
    }

    public static function creaReciboCobro($id_transaccion)
    {
        try {
            // $datos = json_decode($detalle, true);
            $empresa = Empresa::where('prioridad', 1)->first();
            $transaccion = TransaccionPago::find($id_transaccion);
            $log = LogPagoEnLinea::where('id_transaccion', $id_transaccion)->first();
            $agenda = Agenda::find($log->id_agenda);
            $convenio = Convenio::where('id_seguro', $agenda->id_seguro)->where('estado', '1')->first();
            $id_nivel = null;
            if (!is_null($convenio)) {
                $id_nivel = $convenio->id_nivel;
            }
            $datos = json_decode($transaccion->data_request, true);

            $cliente = Ct_Clientes::where('identificacion', $datos['orden_factura']['id_usuario'])->first();
            $idusuario = '0000000004';
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            if ($datos['datos_orden']['tipopago'] == 'ORD') {
                $fecha_emision = date('Y-m-d', strtotime($datos['datos_orden']['dateini']));
                $nueva_fecha = $datos['datos_orden']['dateini'];
            } else if ($datos['datos_orden']['tipopago'] == 'MEM') {
                $fecha_emision = date('Y-m-d', strtotime($transaccion->created_at));
                $nueva_fecha = $transaccion->created_at;
            }
            $arr_orden_venta = [
                'id_agenda'             => $log->id_agenda,
                'id_empresa'            => $empresa->id,
                'fecha_emision'         => $fecha_emision,
                'nueva_fecha'           => $nueva_fecha,
                'oda'                   => 0,
                'id_seguro'             => $agenda->id_seguro,
                'id_nivel'              => $id_nivel,
                'tipo_identificacion'   => $cliente->tipo,
                'identificacion'        => $agenda->id_paciente,
                'razon_social'          => $cliente->nombre,
                'ciudad'                => $datos['orden_factura']['ciudad'],
                'direccion'             => $datos['orden_factura']['direccion'],
                'telefono'              => $datos['orden_factura']['telefono_factura'],
                'email'                 => $datos['orden_factura']['email_factura'],
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'total_sin_tarjeta'     => $datos['total'],
                'estado'                => 1,
                'numero_oda'            => 0,
                'total'                 => $datos['total'],
                'iva'                   => $datos['valor_iva'],
                'subtotal_0'            => $datos['subtotal_0'],
                'subtotal_12'           => $datos['subtotal_12'],
                'valor_oda'             => 0,
                'total_cobrar_seguro'   => 0,
                'caja'                  => 'Torre 2'
            ];

            $id_orden = Ct_Orden_Venta::insertGetId($arr_orden_venta);

            for ($j = 0; $j < count($datos['cart_items']); $j++) {
                $ctProductos = Productos::where('codigo', $datos['cart_items'][$j]['name'])->first();
                if (is_null($ctProductos)) {
                    $arrayProducto = [
                        'codigo'                => str_replace(' ', '', strtoupper($datos['cart_items'][$j]['name'])),
                        'nombre'                => $datos['cart_items'][$j]['name'],
                        'descripcion'           => $datos['cart_items'][$j]['name'],
                        'grupo'                 => 3,
                        'cta_gastos'            => '1.01.03.01.03',
                        'cta_ventas'            => '4.1.01.01',
                        'cta_costos'            => '5.1.01.07',
                        'cta_devolucion'        => '4.1.07',
                        'reg_serie'             => 0,
                        'mod_precio'            => 0,
                        'mod_desc'              => 0,
                        'impuesto_iva_compras'  => '1.01.01.1.01',
                        'impuesto_iva_ventas'   => '1.01.01.1.01',
                        'impuesto_servicio'     => 1,
                        'impuesto_ice'          => 1,
                        'clasificacion_impuesto_ice' => 1,
                        'promedio'              => 0,
                        'reposicion'            => 0,
                        'lista'                 => 0,
                        'ultima_compra'         => 0,
                        'ident_paquete'         => 0,
                        'valor_total_paq'       => $datos['cart_items'][$j]['price'],
                        'estado_tabla'          => 1,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                        'ip_creacion'           => $ip_cliente,
                        'ip_modificacion'       => $ip_cliente,
                        'created_at'            => date('Y-m-d H:is'),
                        'updated_at'            => date('Y-m-d H:is'),
                        'iva'                   => 0,
                        'id_empresa'            => $empresa->id,
                        'tipo_objeto'           => 'P',
                        'tipo_producto'         => 0
                    ];
                    $idP = Productos::insertGetId($arrayProducto);
                    $ctProductos = Productos::find($idP);
                }

                $arr_producto = [
                    'id_orden'          => $id_orden,
                    'cod_prod'          => $ctProductos->codigo,
                    'nombre_producto'   => $ctProductos->nombre,
                    'id_producto'       => $ctProductos->id,
                    'precio'            => $datos['cart_items'][$j]['price'],
                    'cantidad'          => $datos['cart_items'][$j]['quantity'],
                    'total'             => $datos['cart_items'][$j]['price'],
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'p_oda'             => 0,
                    'valor_oda'         => 0,
                    'tipo_dcto'         => '%',
                    'p_dcto'            => 0,
                    'descuento'         => 0,
                    'iva'               => 0,
                    'valor_iva'         => 0,
                    'cobrar_paciente'   => $datos['cart_items'][$j]['price'],
                    'cobrar_seguro'     => 0,
                ];

                Ct_Orden_Venta_Detalle::create($arr_producto);
            }

            $tipo_pago = Ct_Tipo_Pago::where('nombre', 'LIKE', '%PAGO POR APP%')->first();

            $arr_forma_pago = [
                'id_orden'          => $id_orden,
                'tipo'              => $tipo_pago->id,
                'valor'             => $datos['total'],
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'posee_fi'          => 0,
                'p_fi'              => 0,
                'fecha'             => $fecha_emision,
            ];

            Ct_Orden_Venta_Pago::create($arr_forma_pago);

            $arr_log_recibo = [
                'id_ct_orden_venta' => $id_orden,
                'descripcion'       => 'CREADA EL: ' . date('Y-m-d H:i:s') . ' POR PAGO EN LINEA APP IECED',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
            LogReciboDeCobro::create($arr_log_recibo);

            $log->id_orden_venta = $id_orden;
            $log->save();


            return ['status' => 'success', 'msj' => 'Registro Correcto', 'id_orden_venta' => $id_orden];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function loginIecedApp(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $token = $request['token'];
        if ($token != '8c0a00ec19933215dc29225e645ea714') {
            return ['status' => 'error', 'msj' => 'Credenciales invalidas'];
        }
        $email    = $request['email'];
        $password = $request['pass'];

        if (is_null($email) || is_null(trim($password)) || !isset($request['pass'])) {
            return ['status' => 'error', 'msj' => 'Credenciales Invalidas.'];
        }

        $usuario = User::where('email', $email)->orwhere('id', $email)->first();
        if (is_null($usuario)) {
            return ['status' => 'error', 'msj' => 'Usuario no existe.'];
        }

        $passwordUser = $usuario->password;
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        if (Hash::check($password, $passwordUser)) {
            $valid = Apps_Plan_Miembros::where('id_usuario', $usuario->id)->where('estado', 1)->where('approved', 1)->first();
            if ($valid != null) {
                $ad = '1';
            }
            $imagen_url = $usuario->imagen_url;
            if (is_null($imagen_url) || $imagen_url == ' ') {
                $imagen_url = asset('') . '../storage/app/avatars/avatar.jpg';
            } else {
                $imagen_url = asset('') . '../storage/app/avatars/' . $usuario->imagen_url;
            }
            $paciente      = Paciente::find($usuario->id);
            $userMembresia = UserMembresia::where('user_id', $usuario->id)
                ->join('membresia as m', 'm.id', 'membresia_id')
                ->where('user_membresia.estado', '1')
                ->where('m.empresa_id', $empresa->id)
                ->select('user_membresia.*')
                ->first();

            if (is_null($userMembresia) && !is_null($valid)) {
                $userMembresia = UserMembresia::where('user_id', $valid->id_referencia)
                    ->join('membresia as m', 'm.id', 'membresia_id')
                    ->where('user_membresia.estado', '1')
                    ->where('m.empresa_id', $empresa->id)
                    ->select('user_membresia.*')
                    ->first();
            }

            if (is_null($paciente)) {
                Paciente::create([
                    'id'               => $usuario->id,
                    'nombre1'          => strtoupper($usuario->nombre1),
                    'nombre2'          => strtoupper($usuario->nombre2),
                    'apellido1'        => strtoupper($usuario->apellido1),
                    'apellido2'        => strtoupper($usuario->apellido2),
                    'tipo_documento'   => '1',
                    'parentesco'       => 'Principal',
                    'id_usuario'       => $usuario->id,
                    'id_pais'          => '1',
                    'direccion'        => $usuario->direccion,
                    'id_seguro'        => '1',
                    'imagen_url'       => 'avatar.jpg',
                    'fecha_nacimiento' => $usuario->fecha_nacimiento,
                    'telefono1'        => $usuario->telefono1,
                    'telefono2'        => $usuario->telefono2,
                    'ip_creacion'      => '::1',
                    'ip_modificacion'  => '::1',
                    'id_usuariocrea'   => 'CREAUSER',
                    'id_usuariomod'    => 'CREAUSER',
                    'otro'             => 'APP IECED',
                ]);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP IECED",
                    'dato_ant1'   => $usuario->id,
                    'dato1'       => strtoupper($usuario->nombre1 . " " . $usuario->nombre2 . " " . $usuario->apellido1 . " " . $usuario->apellido2),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP IECED',
                ];
                Log_usuario::create($input_log);
                $paciente = Paciente::find($usuario->id);
            }

            Log_Apps::create([
                'id_user' => $usuario->id,
                'id_empresa' => '0993075000001',
                'observacion' => 'Correcto',
                'estado' => 1
            ]);

            return [
                'id'              => $usuario->id,
                'email'           => $usuario->email,
                'nombre1'         => $usuario->nombre1,
                'nombre2'         => $usuario->nombre2,
                'apellido1'       => $usuario->apellido1,
                'apellido2'       => $usuario->apellido2,
                'sexo'            => $paciente->sexo,
                'telefono'        => $usuario->telefono1,
                'direccion'       => $usuario->direccion,
                'ciudad'          => $usuario->ciudad,
                'fecha_nacimiento' => $usuario->fecha_nacimiento,
                'photo'           => $imagen_url,
                'membresia'       => is_null($userMembresia) ? 0 : 1,
                'status'          => 'success',
            ];
        } else {
            Log_Apps::create([
                'id_user' => $usuario->id,
                'id_empresa' => $empresa->id,
                'observacion' => 'Credenciales Invalidas.',
                'estado' => 0
            ]);
            return ['status' => 'error', 'msj' => 'Credenciales Invalidas.'];
        }
    }

    public function registroUsuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            if (is_null($request['id_usuario'])) {
                return ["status" => "error", "msj" => "La cedula es requerida"];
            }
            if (strlen($request['id_usuario']) >= 13) {
                return ["status" => "error", "msj" => "La cedula es mayor a 13 digitos"];
            }
            $user_correo = User::where('email', $request['email'])->first();
            if (!is_null($user_correo)) {
                return ["status" => "error", "msj" => "El correo ya existe"];
            }
            $usuario = User::find($request['id_usuario']);
            if (!is_null($usuario)) {
                return ["status" => "error", "msj" => "El usuario ya existe"];
            }

            $nombre1 = $request['nombre1'];
            $apellido1 = $request['apellido1'];
            $email = $request['email'];
            $fecha_nacimiento = $request['fecha_nacimiento'];
            $sexo = $request['sexo'];
            if (is_null($nombre1)) {
                return ["status" => "error", "msj" => "Nombre 1 vacio"];
            }
            if (is_null($apellido1)) {
                return ["status" => "error", "msj" => "Apellido 1 vacio"];
            }
            if (is_null($email)) {
                return ["status" => "error", "msj" => "Email vacio"];
            }
            if (is_null($fecha_nacimiento)) {
                return ["status" => "error", "msj" => "Fecha de nacimiento vacio"];
            }
            if (is_null($sexo)) {
                return ["status" => "error", "msj" => "Sexo vacio"];
            }
            $paciente = Paciente::find($request['id_usuario']);

            if (is_null($usuario)) {
                $arr_us = [
                    'id'               => $request['id_usuario'],
                    'email'            => $email,
                    'nombre1'          => strtoupper($nombre1),
                    'id_tipo_usuario'  => 2,
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($apellido1),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'estado'           => '1',
                    'imagen_url'       => ' ',
                    'telefono1'        => $request['telefono1'],
                    'direccion'        => $request['direccion'],
                    'tipo_documento'   => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'password'         => !is_null($request['password']) ? bcrypt($request['password']) : bcrypt($request['id_usuario']),
                    'id_usuariocrea'   => 'CREAUSER',
                    'id_usuariomod'    => 'CREAUSER',
                    'ip_creacion'      => '::1',
                    'ip_modificacion'  => '::1',
                ];
                $id = User::insertGetId($arr_us);


                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO USUARIO APP IECED",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP IECED',
                ];
                Log_usuario::create($input_log);
            } else {
                $usUp = [
                    'email'            => is_null($request['email']) ? $usuario->email : $request['email'],
                    'nombre1'          => is_null($request['nombre1']) ?  strtoupper($usuario->nombre1) : strtoupper($request['nombre1']),
                    'nombre2'          => is_null($request['nombre2']) ? strtoupper($usuario->nombre2) : strtoupper($request['nombre2']),
                    'apellido1'        => is_null($request['apellido1']) ? strtoupper($usuario->apellido1) : strtoupper($request['apellido1']),
                    'apellido2'        => is_null($request['apellido2']) ? strtoupper($usuario->apellido2) : strtoupper($request['apellido2']),
                    'telefono1'        => is_null($request['telefono1']) ? $usuario->telefono1 : $request['telefono1'],
                    'direccion'        => is_null($request['direccion']) ? $usuario->direccion : $request['direccion'],
                    'fecha_nacimiento' => is_null($request['fecha_nacimiento']) ? $usuario->fecha_nacimiento : $request['fecha_nacimiento'],
                    'id_usuariomod'    => 'CREAUSER',
                    'ip_modificacion'  => '::1',
                ];
                $usuario->update($usUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZA USUARIO APP IECED",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP IECED',
                ];
                Log_usuario::create($input_log);
            }

            if (is_null($paciente)) {
                $input_pac = [
                    'id'                 => $request['id_usuario'],
                    'id_usuario'         => $request['id_usuario'],
                    'nombre1'            => strtoupper($nombre1),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($apellido1),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $fecha_nacimiento,
                    'sexo'               => $sexo,
                    'telefono1'          => $request['telefono1'],
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => 1,
                    'imagen_url'         => ' ',
                    'menoredad'          => 0,
                    'ip_creacion'        => '::1',
                    'ip_modificacion'    => '::1',
                    'id_usuariocrea'     => 'CREAUSER',
                    'id_usuariomod'      => 'CREAUSER',
                    'otro'               => 'APP IECED',

                ];
                Paciente::create($input_pac);
                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "CREA NUEVO PACIENTE APP IECED",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP IECED',
                ];
                Log_usuario::create($input_log);
            } else {
                $pacUp = [
                    'nombre1'            => is_null($request['nombre1']) ? strtoupper($paciente->nombre1) : strtoupper($request['nombre1']),
                    'nombre2'            => is_null($request['nombre2']) ? strtoupper($paciente->nombre2) : strtoupper($request['nombre2']),
                    'apellido1'          => is_null($request['apellido1']) ? strtoupper($paciente->apellido1) : strtoupper($request['apellido1']),
                    'apellido2'          => is_null($request['apellido2']) ? strtoupper($paciente->apellido2) : strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => is_null($request['fecha_nacimiento']) ? $paciente->fecha_nacimiento : $request['fecha_nacimiento'],
                    'sexo'               => is_null($request['sexo']) ? $paciente->sexo : $request['sexo'],
                    'telefono1'          => is_null($request['telefono1']) ? $paciente->telefono1 : $request['telefono1'],
                    'ip_modificacion'    => '::1',
                    'id_usuariomod'      => 'CREAUSER',
                ];
                $paciente->update($pacUp);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => '::1',
                    'descripcion' => "ACTUALIZAR NUEVO PACIENTE APP IECED",
                    'dato_ant1'   => $request['id_usuario'],
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'CREADO EN APP IECED',
                ];
                Log_usuario::create($input_log);
            }

            return ["status" => "success", "msj" => "Usuario creado"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }

    public function verificarUsuario(Request $request)
    {
        $usuario = User::find($request['id_usuario']);
        if (is_null($usuario)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        $paciente      = Paciente::find($usuario->id);
        $valid = Apps_Plan_Miembros::where('id_usuario', $usuario->id)->where('estado', 1)->where('approved', 1)->first();

        $userMembresia = UserMembresia::where('user_id', $usuario->id)
            ->join('membresia as m', 'm.id', 'membresia_id')
            ->where('user_membresia.estado', '1')
            ->where('m.empresa_id', $empresa->id)
            ->select('user_membresia.*')
            ->first();

        if (is_null($userMembresia) && !is_null($valid)) {
            $userMembresia = UserMembresia::where('user_id', $valid->id_referencia)
                ->join('membresia as m', 'm.id', 'membresia_id')
                ->where('user_membresia.estado', '1')
                ->where('m.empresa_id', $empresa->id)
                ->select('user_membresia.*')
                ->first();
        }
        $imagen_url = $usuario->imagen_url;
        if (is_null($imagen_url) || $imagen_url == ' ') {
            $imagen_url = asset('') . '../storage/app/avatars/avatar.jpg';
        } else {
            $imagen_url = asset('') . '../storage/app/avatars/' . $usuario->imagen_url;
        }

        return [
            'id'              => $usuario->id,
            'email'           => $usuario->email,
            'nombre1'         => $usuario->nombre1,
            'nombre2'         => $usuario->nombre2,
            'apellido1'       => $usuario->apellido1,
            'apellido2'       => $usuario->apellido2,
            'sexo'            => is_null($paciente) ? 0 : $paciente->sexo,
            'telefono'        => $usuario->telefono1,
            'direccion'       => $usuario->direccion,
            'ciudad'          => $usuario->ciudad,
            'fecha_nacimiento' => $usuario->fecha_nacimiento,
            'membresia'       => is_null($userMembresia) ? 0 : 1,
            'photo'           => $imagen_url,
            'status'          => 'success',
        ];
    }

    public function cambiarContrasena(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $id_usuario = $request['id_usuario'];
        $user = User::find($id_usuario);
        if (is_null($user)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }
        if (trim($request['password_1']) == '') {
            return ["status" => "error", "msj" => "Contrasena 1 vacia"];
        }
        if (trim($request['password_2']) == '') {
            return ["status" => "error", "msj" => "Contrasena 2 vacia"];
        }
        if (trim($request['password_1']) !=  trim($request['password_2'])) {
            return ["status" => "error", "msj" => "Las contraseñas no coinciden"];
        }
        try {
            $user->password = bcrypt($request["password_1"]);
            $user->id_usuariomod = $id_usuario;
            $user->ip_modificacion = $id_usuario;
            $user->save();

            return ["status" => "success", "msj" => "Contraseña Actualizada"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }

    public function updateUsuario(Request $request)
    {
        $id_usuario = $request['id_usuario'];
        $user = User::find($id_usuario);
        if (is_null($user)) {
            return ["status" => "error", "msj" => "El usuario no existe"];
        }
        $paciente = Paciente::find($id_usuario);

        try {
            $user->nombre1   = is_null($request['nombre1']) ? strtoupper($user->nombre1) : strtoupper($request['nombre1']);
            $user->nombre2   = is_null($request['nombre2']) ? strtoupper($user->nombre2) : strtoupper($request['nombre2']);
            $user->apellido1 = is_null($request['apellido1']) ? strtoupper($user->apellido1) : strtoupper($request['apellido1']);
            $user->apellido2 = is_null($request['apellido2']) ? strtoupper($user->apellido2) : strtoupper($request['apellido2']);
            $user->telefono1 = is_null($request['telefono1']) ? $user->telefono1 : $request['telefono1'];
            $user->direccion = is_null($request['direccion']) ? $user->direccion : $request['direccion'];
            $user->fecha_nacimiento = is_null($request['fecha_nacimiento']) ? $user->fecha_nacimiento : $request['fecha_nacimiento'];
            $user->email = is_null($request['email']) ? $user->email : $request['email'];
            $user->id_usuariomod = 'CREAUSER';
            $user->ip_modificacion = '::1';
            if (!is_null($request['password'])) {
                $user->password = bcrypt($request["password"]);
            }
            $user->save();

            if (!is_null($paciente)) {
                $paciente->nombre1   = is_null($request['nombre1']) ? strtoupper($paciente->nombre1) : strtoupper($request['nombre1']);
                $paciente->nombre2   = is_null($request['nombre2']) ? strtoupper($paciente->nombre2) : strtoupper($request['nombre2']);
                $paciente->apellido1 = is_null($request['apellido1']) ? strtoupper($paciente->apellido1) : strtoupper($request['apellido1']);
                $paciente->apellido2 = is_null($request['apellido2']) ? strtoupper($paciente->apellido2) : strtoupper($request['apellido2']);
                $paciente->telefono1 = is_null($request['telefono1']) ? $paciente->telefono1 : $request['telefono1'];
                $paciente->fecha_nacimiento = is_null($request['fecha_nacimiento']) ? $paciente->fecha_nacimiento : $request['fecha_nacimiento'];
                $paciente->direccion = is_null($request['direccion']) ? $paciente->direccion : $request['direccion'];
                $paciente->sexo = is_null($request['sexo']) ? $paciente->sexo : $request['sexo'];
                $paciente->id_usuariomod = 'CREAUSER';
                $paciente->ip_modificacion = '::1';
                $paciente->save();
            }


            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "ACTUALIZAR USUARIO Y PACIENTE APP IECED",
                'dato_ant1'   => $request['id_usuario'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato2'       => 'ACTUALIZADO EN APP IECED',
            ];
            Log_usuario::create($input_log);

            return ["status" => "success", "msj" => "Usuario Actualizado"];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage()];
        }
    }
}
