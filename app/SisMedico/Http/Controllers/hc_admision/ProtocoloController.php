<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\Empresa;
use Sis_medico\Excel_Valores;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_protocolo_training;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Orden;
use Sis_medico\Orden_Valores;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\Record;
use Sis_medico\Seguro;
use Sis_medico\Ct_Factura_Procedimiento;
use Sis_medico\Tipo_Anesteciologia;
use Sis_medico\User;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Http\Controllers\Insumos\PlantillaController;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Procedimiento_Detalle_Honorario;

class ProtocoloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 7)) == false) {
            return true;
        }
    }

    public function mostrar($id_procedimiento)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $hc_procedimientos = hc_procedimientos::find($id_procedimiento);
        $id                = $hc_procedimientos->historia->id_agenda;
        $usuarios          = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;
        $enfermeros        = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;
        $anestesiologos    = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado', '1')->get(); //9=ANESTESIOLOGO;
        $salas             = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $ced_paciente = $agenda->id_paciente;
        $paciente     = Paciente::find($ced_paciente);
        $hca          = DB::table('historiaclinica')
            ->where('id_agenda', '=', $id)
            ->get();
        $hca_seguro = $hca[0]->id_seguro;
        $hca_id     = $hca[0]->hcid;
        $seguro     = Seguro::find($hca_seguro);

        $hcp = DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = " . $ced_paciente . " AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

        $ag = Agenda::find($id);
        $pc = $ag->proc_consul;

        $records = Record::all();

        $archivo_vrf = array();
        if ($hca[0]->verificar == 1) {
            $archivo_historico = Archivo_historico::where('id_historia', $hca[0]->hcid)->where('tipo_documento', 'VRF')->get();
            $archivo_vrf       = $archivo_historico[0];
        }

        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();
        if ($pc == 1) {
            $fotos = DB::table('archivo_historico')
                ->where('id_historia', '=', $hca_id)
                ->where('tipo_documento', '<>', 'VRF')
                ->get();
            $procedimientos = DB::table('pentax_procedimiento')
                ->join('procedimiento', 'pentax_procedimiento.id_pentax', '=', 'procedimiento.id')
                ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                ->where('id_pentax', '=', $hca_id)
                ->get();
            $tipo_anesteciologia = Tipo_Anesteciologia::all();
            $protocolo           = hc_protocolo::where('id_hc_procedimientos', '=', $id_procedimiento)->first();

            if ($protocolo == "") {

                $hallazgos = $hc_procedimientos->procedimiento_completo->tecnica_quirurgica;

            } else {

                $hallazgos = $protocolo->hallazgos;
            }
            $anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id_procedimiento)->first();

            return view('hc_admision/protocolo/protocolo', ['agenda' => $agenda, 'paciente' => $paciente, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'hca' => $hca, 'hcp' => $hcp, 'seguro' => $seguro, 'fotos' => $fotos, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda, 'records' => $records, 'hc_procedimientos' => $hc_procedimientos, 'protocolo' => $protocolo, 'hallazgos' => $hallazgos, 'id' => $id_procedimiento, 'anestesiologia' => $anestesiologia]);
        }
    }

    public function crea_actualiza(Request $request)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $protocolo = hc_protocolo::where('id_hc_procedimientos', '=', $request["id_hc_procedimientos"])->first();

        if ($protocolo == '') {
            $input1 = [
                'hallazgos'            => $request["hallazgos"],
                'fecha'                => $request["fecha"],
                'id_hc_procedimientos' => $request["id_hc_procedimientos"],
                'hora_inicio'          => $request["hora_ini"],
                'hora_fin'             => $request["hora_fin"],
                'complicaciones'       => $request["complicaciones"],
                'estado_final'         => $request["estado_final"],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            hc_protocolo::insert($input1);
        } else {
            $id     = $protocolo->id;
            $input1 = [
                'hallazgos'            => $request["hallazgos"],
                'fecha'                => $request["fecha"],
                'id_hc_procedimientos' => $request["id_hc_procedimientos"],
                'hora_inicio'          => $request["hora_ini"],
                'hora_fin'             => $request["hora_fin"],
                'complicaciones'       => $request["complicaciones"],
                'estado_final'         => $request["estado_final"],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
            hc_protocolo::where('id', $id)
                ->update($input1);
        }
        return redirect()->intended('historiaclinica/protocolo/' . $request["id_hc_procedimientos"]);
    }

    public function ingreso_foto(Request $request)
    {

        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $idhc       = $request['id_hc_procedimiento'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $i          = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_procedimiento' => $idhc,
                'id_usuariocrea'      => $idusuario,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_ima_procedimiento_' . $idhc . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
    }
    public function imprime($id_hc_protocolo)
    {
        
        $protocolo         = hc_protocolo::find($id_hc_protocolo);
        $procedimiento_id  = $protocolo->id_hc_procedimientos;
        $anestesico        = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $procedimiento_id)->first();
        $procedimientos_hc = hc_procedimientos::find($procedimiento_id);
        $id                = $procedimientos_hc->id_hc;
        $historia          = Historiaclinica::find($id);
        $agenda            = Agenda::find($historia->id_agenda);
        $seguro            = Seguro::find($historia->id_seguro);
        $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
        $paciente          = Paciente::find($historia->id_paciente);
        $doctor            = User::find($historia->id_doctor1);

        $age = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;

        $data   = $historia;
        $date   = $historia->created_at;
        $pentax = Pentax::where('hcid', '=', $id)->first();

        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $pentax->id)->get();
        $duracion              = strtotime($protocolo->hora_fin) - strtotime($protocolo->hora_inicio);
        $duracion              = round($duracion * 100 / 60) / 100;
        $duracion              = $duracion . " min";

        //return view('hc_admision/protocolo/impresion',['data' => $data, 'empresa' => $empresa, 'paciente' => $paciente, 'age' => $age, 'protocolo' => $protocolo, 'procedimientos_hc' => $procedimientos_hc, 'anestesico' => $anestesico, 'duracion' => $duracion]);
        //return view('hc_admision/formato/'.$documento->formato);
        $paper_size = array(0, 0, 595, 920);
        $view       = \View::make('hc_admision.protocolo.impresion', compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'historia', 'procedimientos_pentax', 'procedimientos_hc', 'protocolo', 'anestesico', 'duracion'))->render();
        $pdf        = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        return $pdf->download($historia->id_paciente . '_PROTOCOLO_OPERATORIO_' . $id . '.pdf');
    }

    public function masterhc(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $nombres       = $request['nombres'];
        $procedimiento = $request['procedimiento'];

        if ($fecha == null) {
            $fecha = Date('Y-m-d');
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = Date('Y-m-d');
        }

        $procedimientos = DB::table('hc_protocolo as hpro')->join('historiaclinica as h', 'h.hcid', 'hpro.hcid')->join('users as hd', 'hd.id', 'h.id_doctor1')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->join('hc_procedimientos as hp', 'hp.id', 'hpro.id_hc_procedimientos')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')->leftjoin('pentax as px', 'px.id_agenda', 'a.id')->select('hpro.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'px.estado_pentax as pxestado', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre');

        $nombres_sql = '';
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            //dd($nombres_sql);
            if ($cantidad == '2' || $cantidad == '3') {
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

        }

        if ($fecha != null) {
            $procedimientos = $procedimientos->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $procedimientos = $procedimientos->orderBy('a.fechaini', 'desc');
        }

        $procedimientos = $procedimientos->where('a.espid', '<>', '10')->paginate(50);

        // dd($procedimientos);
        $consultas = DB::table('hc_evolucion as he')->join('historiaclinica as h', 'h.hcid', 'he.hcid')->join('users as hd', 'hd.id', 'h.id_doctor1')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')->select('he.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');

        $nombres_sql = '';
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            //dd($nombres_sql);
            if ($cantidad == '2' || $cantidad == '3') {
                $consultas = $consultas->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $consultas = $consultas->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

        }

        if ($fecha != null) {
            $consultas = $consultas->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $consultas = $consultas->orderBy('a.fechaini', 'desc');
        }

        $consultas = $consultas->where('a.espid', '<>', '10')->paginate(50);

        //dd($consultas);
        return view('hc_admision/historia/master', ['procedimientos' => $procedimientos, 'consultas' => $consultas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres]);

    }

    public function detallehc($id)
    {

        $protocolo     = hc_protocolo::find($id);
        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);

        $detalle_pdf = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->where('estado', '1')->get();

        //dd($procedimiento->id);
        $evoluciones_proc = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->where('e.hc_id_procedimiento', $procedimiento->id)->get();

        //dd($evoluciones_proc);

        //dd($id,$procedimiento,$protocolo);

        $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->orderBy('id', 'desc')->get();
        $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '2')->get();
        $estudios   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '3')->get();
        $biopsias   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '4')->get();

        $hc_receta    = hc_receta::where('id_hc', $procedimiento->historia->hcid)->first();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $procedimiento->historia->id_paciente)->get();
        $epicrisis    = Hc_Epicrisis::where('hc_id_procedimiento', $procedimiento->id)->first();
        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $procedimiento->historia->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre')
            ->get();

        $insumos = array();
        if (!is_null($procedimiento)) {
            $insumos = DB::table('movimiento_paciente as mp')
                ->where('mp.id_hc_procedimientos', $procedimiento->id)
                ->join('movimiento as m', 'm.id', 'mp.id_movimiento')
                ->join('producto as p', 'p.id', 'm.id_producto')
                ->get();
        }
        $equipos = array();
        if (!is_null($procedimiento)) {
            $equipos = DB::table('equipo_historia as eh')
                ->where('eh.hcid', $procedimiento->historia->hcid)
                ->join('equipo as e', 'e.id', 'eh.id_equipo')
                ->select('e.nombre', 'eh.created_at', 'e.serie')
                ->get();
        }

        $planilla   = PlantillaController::planillaProcedimiento($protocolo->id_hc_procedimientos);

        if (!isset($planilla->id)) {
            $detalles = '[]';
        } else {
            $detalles = $planilla->detalles_validos;
        }

        $equipos2 = DB::table('equipo_historia as eh')->join('equipo as e', 'e.id', 'eh.id_equipo')->where('hcid', $procedimiento->id_hc)->select('e.*', 'eh.*')->get();


        // facturado 
        $insufac = Ct_Detalle_Venta_Omni::where('id_hc_procedimiento', $protocolo->id_hc_procedimientos)->get();
        $planilla_cabecera = Planilla_Cabecera::where('id_hc_procedimiento',$procedimiento->id)->first();
        $detalles = array();
        if(!is_null($planilla_cabecera)){
            $detalles = Planilla_Detalle::where('id_planilla_cabecera',$planilla_cabecera->id)->where('check',1)->where('estado',1)->get();
        }
        return view('hc_admision/procedimientos/procedimiento_det', ['procedimiento' => $procedimiento, 'evoluciones_proc' => $evoluciones_proc, 
        'imagenes' => $imagenes, 'protocolo' => $protocolo, 'documentos' => $documentos, 'estudios' => $estudios, 'biopsias' => $biopsias, 
        'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'epicrisis' => $epicrisis, 'hist_recetas' => $hist_recetas, 'insumos' => $insumos, 
        'equipos' => $equipos, 'insufac' => $insufac, "detalle_pdf" => $detalle_pdf, 'detalles'=> $detalles,'equipos2'=> $equipos2,'detalles' => $detalles]);
    }

    public function detalle_consulta($id)
    {

        $evolucion = Hc_Evolucion::find($id);
        //dd($evolucion);

        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
        $hc_receta  = hc_receta::where('id_hc', $evolucion->hcid)->first();

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $evolucion->historiaclinica->id_paciente)->get();

        $hc_procedimiento = hc_procedimientos::find($evolucion->hc_id_procedimiento);
        //return $agenda->id_seguro;
        /*if($hc_procedimiento->id_seguro == null){
        $input_procedimiento = [
        'id_seguro' => $agenda->id_seguro,
        ];
        hc_procedimientos::where('id_hc', $agenda->hcid)->update($input_procedimiento);
        $hc_procedimiento =  hc_procedimientos::where('id_hc', $agenda->hcid)->first();
        }
        if($hc_procedimiento->id_doctor_examinador == null){
        $input_procedimiento = [
        'id_doctor_examinador' => $historia->id_doctor1,
        ];
        hc_procedimientos::where('id_hc', $agenda->hcid)->update($input_procedimiento);
        $hc_procedimiento =  hc_procedimientos::where('id_hc', $agenda->hcid)->first();
        }*/
        $seguros  = Seguro::where('inactivo', '1')->get();
        $doctores = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //dd($doctores);
        return view('hc_admision/visita/visita_det', ['evolucion' => $evolucion, 'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'child_pugh' => $child_pugh, 'hc_procedimiento' => $hc_procedimiento, 'seguros' => $seguros, 'doctores' => $doctores]);
    }

    public function search(Request $request)
    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $nombres       = $request['nombres'];
        $procedimiento = $request['procedimiento'];

        $procedimientos = DB::table('hc_protocolo as hpro')->join('historiaclinica as h', 'h.hcid', 'hpro.hcid')->join('users as hd', 'hd.id', 'h.id_doctor1')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->join('hc_procedimientos as hp', 'hp.id', 'hpro.id_hc_procedimientos')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')->leftjoin('pentax as px', 'px.id_agenda', 'a.id')->select('hpro.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'px.estado_pentax as pxestado', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre');

        $nombres_sql = '';
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            //dd($nombres_sql);
            if ($cantidad == '2' || $cantidad == '3') {
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

        }

        if ($fecha != null) {
            $procedimientos = $procedimientos->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $procedimientos = $procedimientos->orderBy('a.fechaini', 'desc');
        }

        $procedimientos = $procedimientos->where('a.espid', '<>', '10')->paginate(50);

        //dd($procedimientos);

        $consultas = DB::table('hc_evolucion as he')->join('historiaclinica as h', 'h.hcid', 'he.hcid')->join('users as hd', 'hd.id', 'h.id_doctor1')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')->select('he.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');

        $nombres_sql = '';
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            //dd($nombres_sql);
            if ($cantidad == '2' || $cantidad == '3') {
                $consultas = $consultas->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $consultas = $consultas->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

        }

        if ($fecha != null) {
            $consultas = $consultas->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $consultas = $consultas->orderBy('a.fechaini', 'desc');
        }

        $consultas = $consultas->where('a.espid', '<>', '10')->paginate(50);

        //dd($consultas);
        return view('hc_admision/historia/master', ['procedimientos' => $procedimientos, 'consultas' => $consultas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres]);

    }

    public function pr_modal($id)
    {

        $protocolo = hc_protocolo::find($id);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);

        //$hora_inicio = substr($agenda->fechaini,11,5);

        if ($protocolo->hora_inicio == null) {
            $hora_inicio = substr($agenda->fechaini, 11, 5);
        } else {
            $hora_inicio = substr($protocolo->hora_inicio, 0, 5);
        }

        $fecha_operacion = $protocolo->fecha_operacion;
        if ($fecha_operacion == null) {
            $fecha_operacion = substr($agenda->fechaini, 0, 10);
        }

        $id_doctor_firma = $protocolo->procedimiento->id_doctor_examinador2;
        if ($protocolo->procedimiento->id_doctor_responsable != null) {
            $id_doctor_firma = $protocolo->procedimiento->id_doctor_responsable;
        }
        if ($protocolo->procedimiento->id_seguro != null) {
            $seguro = Seguro::find($protocolo->procedimiento->id_seguro);
            if ($seguro->tipo == 0) {
                if ($protocolo->procedimiento->id_empresa == '1307189140001') {
                    $id_doctor_firma = '1307189140';

                }
                if ($protocolo->procedimiento->id_empresa == '0992704152001') {
                    if ($id_doctor_firma == '0924611882') {
                        $id_doctor_firma = '094346835';
                    }
                }

            }
        }

        if ($id_doctor_firma == null) {
            $id_doctor_firma = $protocolo->procedimiento->id_doctor_examinador;
        }

        if ($id_doctor_firma == null) {
            $id_doctor_firma = $protocolo->historiaclinica->doctor_1->id;
        }

        $id_doctor_ayudante_con = $protocolo->procedimiento->id_doctor_ayudante_con;

        if ($id_doctor_ayudante_con == null) {
            if ($protocolo->historiaclinica->doctor_2 != null) {
                $id_doctor_ayudante_con = $protocolo->historiaclinica->doctor_2->id;
            }

        }

        $cpre_eco = Hc_cpre_eco::where('hcid', $protocolo->hcid)->first();

        //dd($id_doctor_firma);
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        return view('hc_admision/protocolo/pr_modal', ['protocolo' => $protocolo, 'hora_inicio' => $hora_inicio, 'id_doctor_firma' => $id_doctor_firma, 'doctores' => $doctores, 'fecha_operacion' => $fecha_operacion, 'id_doctor_ayudante_con' => $id_doctor_ayudante_con, 'cpre_eco' => $cpre_eco,'anestesiologos' => $anestesiologos]);

    }

    public function guardar_op(Request $request)
    {

        $protocolo = hc_protocolo::find($request->protocolo);

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
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];
        $protocolo->procedimiento->update($input2);

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('hc_admision.protocolo.prot_operatorio', compact('protocolo', 'age', 'agenda', 'firma'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('HCU_017.pdf');
        //return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');

    }

    public function guardar_op_cpre_eco(Request $request)
    {
        //dd($request->all());
        $protocolo = hc_protocolo::find($request->protocolo);
        $cpre_eco  = Hc_cpre_eco::where('hcid', $protocolo->hcid)->first();

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
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
            'id_doctor1'          => $request->id_doctor_examinador2,
            'id_doctor2'          => $request->id_doctor_ayudante_con,
        ];

        if (!is_null($cpre_eco)) {
            $cpre_eco->update($input);
        }

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('hc_admision.protocolo.prot_operatorio_cpre_eco', compact('protocolo', 'age', 'agenda', 'firma', 'cpre_eco'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('HCU_017.pdf');
        //return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');
    }
    // 06/02/2019  modula de cpre y eco

    public function modal_cpre_eco($hcid)
    {

        $cpre_eco = Hc_cpre_eco::where('hcid', $hcid)->first();
        $proc     = hc_protocolo::where('hcid', $hcid)->get();
        $texto    = '';
        foreach ($proc as $p) {

            $texto = $texto . $p->hallazgos . '<br>';

        }
        //dd($texto);
        // dd($cpre_eco);

        return view('hc_admision/protocolo/cpre_eco_modal', ['cpre_eco' => $cpre_eco, 'hcid' => $hcid, 'texto' => $texto]);

    }

    // 07/02/2019

    public function modal_crear_editar(Request $request)
    {
        //dd($request -> all());
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $hcid       = $request->hcid;
        $cpre_eco   = Hc_cpre_eco::where('hcid', $hcid)->first();
        if (is_null($cpre_eco)) {
            $input = [

                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'hcid'            => $request["hcid"],
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Hc_cpre_eco::create($input);
            return "Procedimiento CPRE + ECO Guardado";
        } else {
            $input = [
                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $cpre_eco->update($input);
            return "Procedimiento CPRE + ECO Actualizado";
        }

    }

    public function crear_training($training, $protocolo, $n)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $training_proc = Hc_protocolo_training::where('id_training', $training)->where('id_hc_protocolo', $protocolo)->first();
        if (is_null($training_proc)) {
            $input = [

                'id_hc_protocolo' => $protocolo,
                'id_training'     => $training,
                'estado'          => $n,
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Hc_protocolo_training::create($input);
        } else {
            $input = [

                'estado'          => $n,

                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $training_proc->update($input);
        }

    }

    //funcion solo para los doctores
    public function selecciona_procedimiento($tipo, $paciente)
    {

        /*if($this->rol_doctor()){
        return response()->view('errors.404');
        }*/
        //'0: endoscopico, 1: funcional, 2:imagen, 3:consulta', 4:broncoscopias
        $px = Procedimiento::where('procedimiento.estado', '1')->get();

        $paciente = Paciente::find($paciente);

        //dd($paciente);
        return view('hc_admision.protocolo.selecciona', ['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo]);

    }

    public function crear_procedimiento(Request $request)
    {

        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];
        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->paciente);

        $input_agenda = [
            'fechaini'         => Date('Y-m-d H:i:s'),
            'fechafin'         => Date('Y-m-d H:i:s'),
            'id_paciente'      => $paciente->id,
            'id_doctor1'       => $id_doctor,
            'proc_consul'      => '4',
            'estado_cita'      => '4',
            'espid'            => '4',
            'observaciones'    => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
            'id_seguro'        => $paciente->id_seguro,
            'estado'           => '4',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $id_doctor,
            'id_usuariomod'    => $id_doctor,
            'id_procedimiento' => $procedimientop,
            'id_sala'          => '10',
        ];

        $id_agenda = agenda::insertGetId($input_agenda);
        //return $id_agenda;

        $txt_pro = '';
        foreach ($procedimientos as $value) {

            if ($procedimientop != $value) {
                $txt_pro = $txt_pro . '+' . $value;
                AgendaProcedimiento::create([
                    'id_agenda'        => $id_agenda,
                    'id_procedimiento' => $value,
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => $id_doctor,
                    'id_usuariomod'    => $id_doctor,
                ]);
            }

        }

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'estado'          => '4',
            'observaciones'   => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
            'campos_ant'      => 'PRO: ' . $procedimientop . $txt_pro,

            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];

        $idusuario = $id_doctor;

        Log_agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $paciente->id,
            'id_seguro'       => $paciente->id_seguro,

            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_pentax = [
            'id_agenda'       => $id_agenda,
            'hcid'            => $id_historia,
            'id_sala'         => '10',
            'id_doctor1'      => $idusuario,
            'id_seguro'       => $paciente->id_seguro,
            'observacion'     => "PROCEDIMIENTO CREADO POR EL DOCTOR",
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_pentax = Pentax::insertGetId($input_pentax);

        $list_proc = '';
        foreach ($procedimientos as $value) {
            $input_pentax_pro2 = [
                'id_pentax'        => $id_pentax,
                'id_procedimiento' => $value,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
            ];

            PentaxProc::create($input_pentax_pro2);
            $list_proc = $list_proc . "+" . $value;
        }

        $input_log_px = [
            'id_pentax'       => $id_pentax,
            'tipo_cambio'     => "CREADO POR EL DOCTOR",
            'descripcion'     => "EN ESPERA",
            'estado_pentax'   => '0',
            'procedimientos'  => $list_proc,
            'id_doctor1'      => $idusuario,
            'observacion'     => "CREADO POR EL DOCTOR",
            'id_sala'         => '10',
            'id_seguro'       => $paciente->id_seguro,

            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
        ];

        Pentax_log::create($input_log_px);

        $input_hc_procedimiento = [
            'id_hc'           => $id_historia,
            'id_seguro'       => $paciente->id_seguro,

            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_protocolo = [
            'id_hc_procedimientos' => $id_hc_procedimiento,
            'hora_inicio'          => date('H:i:s'),
            'hora_fin'             => date('H:i:s'),
            'estado_final'         => ' ',
            'ip_modificacion'      => $ip_cliente,
            'hcid'                 => $id_historia,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];
        hc_protocolo::insert($input_hc_protocolo);

        foreach ($procedimientos as $value) {
            $input_pro_final = [
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'id_procedimiento'     => $value,
                'id_usuariocrea'       => $idusuario,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
            ];

            Hc_Procedimiento_Final::create($input_pro_final);
        }

        return "ok";

    }

    public function produccion_mes()
    {

        $doctores       = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('nombre1')->get();
        $seguros        = DB::table('seguros as s')->where('s.inactivo', '1')->get();
        $procedimientos = DB::table('hc_procedimientos as hp')->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->wherenull('id_procedimiento_completo')->where('hp.estado', '1')->whereYear('a.fechaini', '2019')->whereMonth('a.fechaini', '09')->select(DB::raw('YEAR(a.fechaini) as year, MONTH(a.fechaini) as month'), 'a.fechaini', 'h.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'hp.id as hpid', 'hp.id_seguro', 'a.id_empresa as aempresa', 'hp.id_empresa as hpempresa', 'a.id as agenda', 'hp.estimado_minimo')->orderBy('a.id')->get();

        //MARCA QUIEN NO TIENE PROCEDIMIENTO FINAL
        /*foreach ($procedimientos as $val) {
        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()==0){
        $arr=[
        'estado' => '0',
        'ip_creacion' => 'SIN_PROC'
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        }*/
        //ACTUALIZA EMPRESA
        /*foreach ($procedimientos as $val) {
        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()>0){
        $arr=[
        'id_empresa' => $val->aempresa,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        } */
        //carga valores estimados
        /*foreach ($procedimientos as $val) {

        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()=='1'){
        if($val->id_seguro=='1'){

        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','1')->where('id_procedimiento',$px['0']->id_procedimiento)->first();//particulares

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        }else{
        $seguro = Seguro::find($val->id_seguro);
        if($seguro->tipo=='0'){
        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','2')->where('id_procedimiento',$px['0']->id_procedimiento)->first();

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }

        }
        }

        }
        if($px->count()>'1'){
        if($val->id_seguro=='1'){
        $arr=[
        'estimado_minimo' => '999',
        ];
        hc_procedimientos::find($val->hpid)->update($arr);

        }else{
        $seguro = Seguro::find($val->id_seguro);
        if($seguro->tipo=='0'){
        $txt_pro = '';$cont = 0;
        foreach ($px as $pt) {
        if($cont == 0){
        $txt_pro = $pt->id_procedimiento;
        $cont ++;
        }else{
        $txt_pro = $txt_pro.'+'.$pt->id_procedimiento;
        }
        }
        //dd($txt_pro);
        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','2')->where('id_procedimiento',$txt_pro)->first();

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }

        }
        }

        }
        else{

        }
        }*/

        /*
        foreach ($procedimientos as $val){//AGREGA PROCEDIMIENTOS SECUNDARIOS
        $pentax = Pentax::where('id_agenda', $val->agenda)->first();
        if(!is_null($pentax)){
        $id_principal=0;$id_secundario = 0;
        $pproc = PentaxProc::where('id_pentax',$pentax->id)->get();
        //dd($pproc);
        foreach($pproc as $p1){

        if($p1->procedimiento->id_grupo_procedimiento != null){
        $id_principal=$p1->id_procedimiento;
        }
        if($id_principal != 0){
        $id_secundario=$p1->id_procedimiento;
        if($id_secundario!=0 && $id_principal!=0){

        $px_sec = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->where('id_procedimiento',$id_secundario)->first();
        if(is_null($px_sec)){
        $px_pri = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->where('id_procedimiento',$id_principal)->first();
        if(!is_null($px_pri)){
        //dd($id_secundario,$id_principal);
        $input_pro_final = [
        'id_hc_procedimientos' => $val->hpid,
        'id_procedimiento' => $id_secundario,
        'id_usuariocrea' => '0922290697',
        'ip_modificacion' => 'SISTEMAS',
        'id_usuariomod' => '0922290697',
        'ip_creacion' => 'SISTEMAS',
        ];

        Hc_Procedimiento_Final::create($input_pro_final);

        }
        }
        }
        }

        }

        }
        } */

        return view('hc_admision.procedimientos.estadisticos', ['procedimientos' => $procedimientos, 'nombres' => null, 'doctores' => $doctores, 'seguros' => $seguros, 'id_doctor1' => null, 'id_seguro' => null]);
    }

    public function subir_excel()
    {

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('subevalor.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                //dd($book);
                //$anio = date("Y",strtotime($book->fecha_proced));
                //$mes = date("d",strtotime($book->fecha_proced));
                $anio = $book->anio;
                $mes  = $book->mes;

                $arrf = explode('/', $book->fecha_proced);

                $nombre = ltrim($book->paciente);
                $nombre = rtrim($nombre);
                $nombre = trim($nombre, " \t\n\r");
                $nombre = str_replace('_x000D_', '', $nombre);

                /*if($book->id == 8058){
                dd($book, $arrf);
                }*/
                $array = [
                    'anio'                => $anio,
                    'mes'                 => $mes,
                    'id_subida'           => $book->id,
                    'fecha_procedimiento' => $arrf[2] . '/' . $arrf[1] . '/' . $arrf[0],
                    'fecha'               => $arrf[2] . '/' . $arrf[1] . '/' . $arrf[0],
                    'procedimiento'       => $book->proced,
                    'tipo'                => $book->tipo,
                    'paciente'            => $nombre,
                    'seguro'              => $book->seguro,
                    'id_seguro'           => $book->id_seguro,
                    'valor'               => $book->valor,
                    'grupo'               => $book->proc,
                    'id_usuariocrea'      => '0922290697',
                    'id_usuariomod'       => '0922290697',
                    'ip_creacion'         => 'SISTEMAS',
                    'ip_modificacion'     => 'SISTEMAS',

                ];

                //dd($array);

                $excel = Excel_Valores::where('anio', $anio)->where('mes', $mes)->where('id_subida', $book->id)->first();
                //dd($excel);
                if (is_null($excel)) {

                    Excel_Valores::create($array);
                } else {

                    $nombre      = ltrim($book->paciente);
                    $nombre      = rtrim($nombre);
                    $nombre      = trim($nombre, " \t\n\r");
                    $nombre      = str_replace('_x000D_', '', $nombre);
                    $e_nombre    = explode(' ', $nombre);
                    $nombres_sql = '';
                    $cantidad    = count($e_nombre);
                    foreach ($e_nombre as $n) {
                        $nombres_sql = $nombres_sql . '%' . $n;
                    }
                    $nombres_sql = $nombres_sql . '%';
                    //dd($nombres_sql);

                    if ($cantidad == '2' || $cantidad == '3') {
                        $paciente = Paciente::where(function ($jq1) use ($nombres_sql) {
                            $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', [$nombres_sql]);
                        })->get();

                    } else {

                        $paciente = Paciente::where(function ($jq1) use ($nombres_sql) {
                            $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql]);
                        })->get();
                    }

                    if ($paciente->count() == 1) {
                        $id_cedula = $paciente[0]->id;

                        $arr2 = [

                            'paciente'    => $nombre,
                            'id_paciente' => $id_cedula,
                            'ip_creacion' => 'SISTEMAS2',
                        ];

                        $excel->update($arr2);
                    }

                }
            }
        });

        return "ok";
    }

    public function cargar_empresa($id_proc, $id_agenda, $id_seguro)
    {
        $agenda = Agenda::find($id_agenda);
        $seguro = Seguro::find($id_seguro);
        if ($seguro->tipo == 0) {
            $empresas = DB::table('convenio as c')->join('empresa as e', 'e.id', 'c.id_empresa')->select('e.*')->where('c.id_seguro', $seguro->id)->get();
        } else {
            $empresas = DB::table('empresa as e')->where('e.estado', '1')->where('e.id', '<>', '9999999999')->get();
        }
        $procedimiento = hc_procedimientos::find($id_proc);
        $id_empresa    = $procedimiento->id_empresa;

        if ($procedimiento->id_empresa == null) {
            $procedimiento->update(['id_empresa' => $agenda->id_empresa]);
            $id_empresa = $agenda->id_empresa;
        }

        return view('hc_admision.empresas', ['empresas' => $empresas, 'id_empresa' => $id_empresa]);
    }

    public function xcruzar_historiaclinica()
    {
        $excel = Excel_Valores::wherenull('hcid')->get();
        //dd($excel->count());
        foreach ($excel as $value) {

            //dd($value->fecha.' 23:59:59');
            $agenda = DB::table('agenda as a')->where('a.id_paciente', $value->id_paciente)->whereBetween('a.fechaini', [$value->fecha . '  0:00:00', $value->fecha . ' 23:59:59'])->where('a.proc_consul', '1')->where('a.estado_cita', '4')->join('historiaclinica as h', 'h.id_agenda', 'a.id')->select('a.*', 'h.hcid')->first();

            if (!is_null($agenda)) {

                $value->update(['hcid' => $agenda->hcid]);
            }
        }
        return "ok";

    }

    public function ordenes_master(Request $request)
    {
        $nombres   = null;
        $id_seguro = null;
        $empresa   = null;
        $facturada = null;

        $anio        = $request->anio;
        $mes         = $request->mes;
        $id_doctor1  = $request->id_doctor1;
        $id_paciente = $request->id_paciente;

        $doctores = User::where('id_tipo_usuario', '3')->orderBy('apellido1')->get();
        $seguros  = Seguro::orderBy('nombre')->get();
        $ordenes  = Orden::where('orden.estado', '1')->orderBy('orden.created_at')->leftjoin('orden_valores as ov', 'ov.id_orden', 'orden.id')->leftjoin('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('orden.*', 'ev.seguro', 'ev.id_seguro');

        $ordenes_valores = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor');

        $ordenes_valoresx = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->selectRaw('count(*) as cantidad')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->get();
        //dd($ordenes_valoresx->get());
        $ordenes_valores2  = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->selectRaw('count(*) as cantidad')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor');
        $ordenes_valores2x = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->selectRaw('count(*) as cantidad')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->get();

        if ($anio != null) {
            $ordenes          = $ordenes->where('orden.anio', $anio);
            $ordenes_valores  = $ordenes_valores->where('o.anio', $anio);
            $ordenes_valores2 = $ordenes_valores2->where('ev.anio', $anio);
        }

        if ($mes != null) {
            $ordenes          = $ordenes->where('orden.mes', $mes);
            $ordenes_valores  = $ordenes_valores->where('o.mes', $mes);
            $ordenes_valores2 = $ordenes_valores2->where('ev.mes', $mes);
        }

        if ($id_doctor1 != null) {
            $ordenes = $ordenes->where('orden.id_doctor', $id_doctor1);
        }

        if ($id_paciente != null) {
            $ordenes = $ordenes->where('orden.id_paciente', $id_paciente);
        }

        $ordenes          = $ordenes->get();
        $ordenes_valores  = $ordenes_valores->get();
        $ordenes_valores2 = $ordenes_valores2->get();

        return view('hc_admision.procedimientos.ordenes', ['ordenes' => $ordenes, 'nombres' => $nombres, 'doctores' => $doctores, 'seguros' => $seguros, 'id_doctor1' => $id_doctor1, 'id_seguro' => $id_seguro, 'anio' => $anio, 'mes' => $mes, 'id_paciente' => $id_paciente, 'ordenes_valores' => $ordenes_valores, 'ordenes_valores2' => $ordenes_valores2, 'ordenes_valoresx' => $ordenes_valoresx, 'ordenes_valores2x' => $ordenes_valores2x, 'facturada' => $facturada]);
    }

    public function ordenes_excel(Request $request)
    {
        $nombres   = null;
        $id_seguro = null;
        $empresa   = null;

        $anio        = $request->anio;
        $mes         = $request->mes;
        $id_doctor1  = $request->id_doctor1;
        $id_paciente = $request->id_paciente;

        $doctores = User::where('id_tipo_usuario', '3')->orderBy('apellido1')->get();
        $seguros  = Seguro::orderBy('nombre')->get();
        $ordenes  = Orden::where('orden.estado', '1')->orderBy('orden.created_at')->join('ordenes_valores as ov', 'ov.id_orden', 'orden.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('orden.*', 'ev.seguro', 'ev.id_seguro');

        $ordenes_valores = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor');

        $ordenes_valoresx = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->selectRaw('count(*) as cantidad')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->get();
        //dd($ordenes_valoresx->get());
        $ordenes_valores2  = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->selectRaw('count(*) as cantidad')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor');
        $ordenes_valores2x = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->selectRaw('count(*) as cantidad')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->get();

        if ($anio != null) {
            $ordenes          = $ordenes->where('orden.anio', $anio);
            $ordenes_valores  = $ordenes_valores->where('o.anio', $anio);
            $ordenes_valores2 = $ordenes_valores2->where('ev.anio', $anio);
        }

        if ($mes != null) {
            $ordenes          = $ordenes->where('orden.mes', $mes);
            $ordenes_valores  = $ordenes_valores->where('o.mes', $mes);
            $ordenes_valores2 = $ordenes_valores2->where('ev.mes', $mes);
        }

        if ($id_doctor1 != null) {
            $ordenes = $ordenes->where('orden.id_doctor', $id_doctor1);
        }

        if ($id_paciente != null) {
            $ordenes = $ordenes->where('orden.id_paciente', $id_paciente);
        }

        $ordenes          = $ordenes->get();
        $ordenes_valores  = $ordenes_valores->get();
        $ordenes_valores2 = $ordenes_valores2->get();

        $fecha_d = date('Y-m-d');

        Excel::create('Ordenes-' . $fecha_d, function ($excel) use ($fecha_d, $ordenes) {

            $excel->sheet('Historiaclinica', function ($sheet) use ($fecha_d, $ordenes) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');;
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 2;
                foreach ($ordenes as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->anio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        $cell->setValue($mes[$value->mes - 1]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->doctor->apellido1 . '' . $value->doctor->apellido2 . ' ' . $value->doctor->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente . '-' . $value->paciente->apellido1 . ' ' . $value->paciente->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $txt_pxs = '';
                        $tipos   = $value->orden_tipo;
                        foreach ($tipos as $tipo) {
                            $pxs = $tipo->orden_procedimiento;
                            foreach ($pxs as $px) {
                                $txt_pxs = $txt_pxs . '+' . $px->procedimiento->nombre;
                            }
                        }
                        $cell->setValue($txt_pxs);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

        })->export('xlsx');
    }

    public function valores_master(Request $request)
    {
        $nombres   = null;
        $id_seguro = null;
        $empresa   = null;

        $anio        = $request->anio;
        $mes         = $request->mes;
        $id_paciente = $request->id_paciente;
        $id_seguro   = $request->id_seguro;
        $id_empresa  = $request->id_empresa;
        $tipo_seguro = $request->tipo_seguro;

        $doctores = User::where('id_tipo_usuario', '3')->orderBy('apellido1')->get();
        $seguros  = Seguro::orderBy('nombre')->get();
        $empresas = Empresa::where('id', '<>', '9999999999')->get();
        $valores  = Excel_Valores::where('excel_valores.tipo', '1')->join('seguros as s', 's.id', 'excel_valores.id_seguro')->select('excel_valores.*', 's.tipo as tipo_seguro')->orderBy('excel_valores.fecha');

        if ($anio != null) {
            $valores = $valores->where('excel_valores.anio', $anio);
        }

        if ($mes != null) {
            $valores = $valores->where('excel_valores.mes', $mes);
        }

        if ($id_paciente != null) {
            $valores = $valores->where('excel_valores.id_paciente', $id_paciente);
        }

        if ($id_seguro != null) {
            $valores = $valores->where('excel_valores.id_seguro', $id_seguro);
        }

        if ($id_empresa != null) {
            $valores = $valores->where('excel_valores.id_empresa', $id_empresa);
        }

        if ($tipo_seguro != null) {
            $valores = $valores->where('s.tipo', $tipo_seguro);
        }

        $valores = $valores->get();

        //carga empresa
        /*foreach ($valores as $value) {

        if($value->seguro=='IESS-CRM'){
        $value->update(['id_empresa' => '1307189140001']);
        }else{
        $value->update(['id_empresa' => '0992704152001']);
        }
        }*/

        return view('hc_admision.procedimientos.valores', ['valores' => $valores, 'anio' => $anio, 'mes' => $mes, 'id_paciente' => $id_paciente, 'id_seguro' => $id_seguro, 'seguros' => $seguros, 'empresas' => $empresas, 'id_empresa' => $id_empresa, 'tipo_seguro' => $tipo_seguro]);
    }

    public function proceso_cuadre()
    {

        $valores = Excel_Valores::where('tipo', '1')->where('gestion', '0')->orderBy('fecha')->get();
        //dd($valores);
        $arreglo = [];
        $i       = 0;
        foreach ($valores as $value) {
            //dd($value);

            $agendas = DB::table('agenda as a')->where('a.id_paciente', $value->id_paciente)->whereBetween('a.fechaini', [$value->fecha . '  0:00:00', $value->fecha . ' 23:59:59'])->where('a.proc_consul', '>=', '1')->where('a.estado', '>=', '1')->where('a.estado_cita', '4')->join('historiaclinica as h', 'h.id_agenda', 'a.id')->select('a.*', 'h.hcid')->get();

            /*if($value->id=='1548'){
            dd($agendas);
            }*/
            //dd($agendas);
            foreach ($agendas as $a) {
                $hc_procedimientos = hc_procedimientos::where('id_hc', $a->hcid)->get();
                /*if($value->id=='1548'){
                dd($hc_procedimientos);
                } */
                //dd($hc_procedimientos);
                foreach ($hc_procedimientos as $hc_proc) {
                    $procedimientos = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_proc->id)->get();
                    /*if($value->id=='1548'){
                    dd($procedimientos);
                    }*/
                    //dd($procedimientos);
                    foreach ($procedimientos as $px) {
                        $grupo = $px->procedimiento->id_grupo_procedimiento;
                        if ($grupo != null) {
                            $tipo        = $px->procedimiento->grupo_procedimiento->tipo_procedimiento;
                            $arreglo[$i] = [
                                'id_paciente' => $value->id_paciente,
                                'fecha'       => $value->fecha,
                                'tipo'        => $tipo,
                                'id_xls'      => $value->id,
                                'hcid'        => $a->hcid,
                            ];
                            $i++;
                            //dd($px);
                        }

                    }

                }
            }
            //dd($value);

            $value->update(['gestion' => '1']);

        }

        foreach ($arreglo as $value) {

            $valores = Excel_Valores::where('fecha', $value['fecha'])->where('id_paciente', $value['id_paciente'])->where('grupo', $value['tipo'])->get();

            foreach ($valores as $val) {
                $val->update(['hcid' => $value['hcid']]);

            }
            //dd($val);
            $orden = Orden::where('id_paciente', $value['id_paciente'])->where('fecha_orden', '<=', $value['fecha'])->where('tipo_procedimiento', $value['tipo'])->first();
            if (!is_null($orden)) {
                $orden_valor = Orden_Valores::where('id_orden', $orden->id)->where('id_excel_valores', $value['id_xls'])->first();
                if (is_null($orden_valor)) {
                    Orden_Valores::create(['id_orden' => $orden->id, 'id_excel_valores' => $value['id_xls']]);
                }
            }
            //dd($val);

        }
        return 'ok';
    }

    public function produccion_estad()
    {

        return view('hc_admision.procedimientos.carga');
    }

    public function estad_index()
    {

        $anio                    = '2019';
        $ordenes_valores_totales = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->get();
        //dd($ordenes_valores_totales);
        $ordenes_valores_totales_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id')->where('ev.anio', $anio)->get();

        $ordenes_valores_publicos = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 0)->get();

        $ordenes_valores_publicos_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 0)->get();

        $ordenes_valores_privados = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 1)->get();

        $ordenes_valores_privados_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 1)->get();

        $ordenes_valores_particulares = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 2)->get();

        $ordenes_valores_particulares_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id')->orderBy('ev.anio', 'ev.mes')->where('ev.anio', $anio)->where('s.tipo', 2)->get();

        $ordenes_valores_totales2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'o.mes')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->get();
        //dd($ordenes_valores_totales);
        $ordenes_valores_totales_orden2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id')->where('o.anio', $anio)->get();

        $ordenes_valores_publicos2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 0)->get();

        $ordenes_valores_publicos_orden2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 0)->get();

        $ordenes_valores_privados2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 1)->get();

        $ordenes_valores_privados_orden2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 1)->get();

        $ordenes_valores_particulares2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 2)->get();

        $ordenes_valores_particulares_orden2 = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id')->orderBy('o.anio', 'o.mes')->where('o.anio', $anio)->where('s.tipo', 2)->get();

        $ordenes_anio = DB::table('orden')->select('anio')->selectRaw('count(*) as cantidad')->orderBy('anio')->groupBy('anio')->where('estado', '1')->get();

        $ordenes_anio_mes = DB::table('orden')->select('anio', 'mes')->selectRaw('count(*) as cantidad')->orderBy('anio', 'mes')->groupBy('anio', 'mes')->where('estado', '1')->where('anio', $anio)->get();

        return view('hc_admision.procedimientos.estad_index', ['ordenes_valores_totales' => $ordenes_valores_totales, 'ordenes_valores_totales_orden' => $ordenes_valores_totales_orden, 'ordenes_valores_publicos' => $ordenes_valores_publicos, 'ordenes_valores_privados' => $ordenes_valores_privados, 'ordenes_valores_particulares' => $ordenes_valores_particulares, 'anio' => $anio, 'ordenes_valores_publicos_orden' => $ordenes_valores_publicos_orden, 'ordenes_valores_privados_orden' => $ordenes_valores_privados_orden, 'ordenes_valores_particulares_orden' => $ordenes_valores_particulares_orden, 'ordenes_valores_totales2' => $ordenes_valores_totales2, 'ordenes_valores_totales_orden2' => $ordenes_valores_totales_orden2, 'ordenes_valores_publicos2' => $ordenes_valores_publicos2, 'ordenes_valores_privados2' => $ordenes_valores_privados2, 'ordenes_valores_particulares2' => $ordenes_valores_particulares2, 'ordenes_valores_publicos_orden2' => $ordenes_valores_publicos_orden2, 'ordenes_valores_privados_orden2' => $ordenes_valores_privados_orden2, 'ordenes_valores_particulares_orden2' => $ordenes_valores_particulares_orden2, 'ordenes_anio' => $ordenes_anio, 'ordenes_anio_mes' => $ordenes_anio_mes]);
    }

    public function estad_mes($anio, $mes)
    {

        $doctores = User::where('id_tipo_usuario', 3)->get();

        $ordenes_valores_totales = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->get();

        $ordenes_valores_totales_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->where('ev.anio', $anio)->where('ev.mes', $mes)->get();

        $ordenes_valores_publicos = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 0)->get();

        $ordenes_valores_publicos_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 0)->get();

        $ordenes_valores_privados = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 1)->get();

        $ordenes_valores_privados_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 1)->get();

        $ordenes_valores_particulares = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 2)->get();

        $ordenes_valores_particulares_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->groupBy('ev.anio', 'ev.mes', 'o.id_doctor', 'o.id')->orderBy('ev.anio', 'ev.mes', 'o.id_doctor')->where('ev.anio', $anio)->where('ev.mes', $mes)->where('s.tipo', 2)->get();

        return view('hc_admision.procedimientos.estad_mes', ['ordenes_valores_totales' => $ordenes_valores_totales, 'ordenes_valores_totales_orden' => $ordenes_valores_totales_orden, 'ordenes_valores_publicos' => $ordenes_valores_publicos, 'ordenes_valores_privados' => $ordenes_valores_privados, 'ordenes_valores_particulares' => $ordenes_valores_particulares, 'anio' => $anio, 'ordenes_valores_publicos_orden' => $ordenes_valores_publicos_orden, 'ordenes_valores_privados_orden' => $ordenes_valores_privados_orden, 'ordenes_valores_particulares_orden' => $ordenes_valores_particulares_orden, 'mes' => $mes, 'doctores' => $doctores]);
    }

    public function estad_mes_orden($anio, $mes)
    {

        $doctores = User::where('id_tipo_usuario', 3)->get();

        $ordenes_valores_totales = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'ev.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->get();

        $ordenes_valores_totales_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->where('o.anio', $anio)->where('o.mes', $mes)->get();

        $ordenes_valores_publicos = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 0)->get();

        $ordenes_valores_publicos_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 0)->get();

        $ordenes_valores_privados = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 1)->get();

        $ordenes_valores_privados_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 1)->get();

        $ordenes_valores_particulares = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor')->selectRaw('sum(ev.valor) as total')->groupBy('o.anio', 'o.mes', 'o.id_doctor')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 2)->get();

        $ordenes_valores_particulares_orden = DB::table('orden as o')->join('orden_valores as ov', 'ov.id_orden', 'o.id')->join('excel_valores as ev', 'ev.id', 'ov.id_excel_valores')->join('seguros as s', 's.id', 'ev.id_seguro')->select('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->groupBy('o.anio', 'o.mes', 'o.id_doctor', 'o.id')->orderBy('o.anio', 'o.mes', 'o.id_doctor')->where('o.anio', $anio)->where('o.mes', $mes)->where('s.tipo', 2)->get();

        return view('hc_admision.procedimientos.estad_mes', ['ordenes_valores_totales' => $ordenes_valores_totales, 'ordenes_valores_totales_orden' => $ordenes_valores_totales_orden, 'ordenes_valores_publicos' => $ordenes_valores_publicos, 'ordenes_valores_privados' => $ordenes_valores_privados, 'ordenes_valores_particulares' => $ordenes_valores_particulares, 'anio' => $anio, 'ordenes_valores_publicos_orden' => $ordenes_valores_publicos_orden, 'ordenes_valores_privados_orden' => $ordenes_valores_privados_orden, 'ordenes_valores_particulares_orden' => $ordenes_valores_particulares_orden, 'mes' => $mes, 'doctores' => $doctores]);
    }

    public function anio_mes_doc($anio, $mes)
    {
        $or_aniomes_doctor = DB::table('orden as o')
            ->join('users as d', 'd.id', 'o.id_doctor')
            ->select('o.anio', 'o.mes', 'o.id_doctor', 'd.apellido1', 'd.apellido2', 'd.nombre1', 'd.color')
            ->selectRaw('count(*) as cantidad')
            ->orderBy('o.anio', 'd.apellido1', 'd.apellido2', 'd.nombre1', 'o.mes', 'o.id_doctor', 'd.color')
            ->groupBy('o.anio', 'd.apellido1', 'd.apellido2', 'd.nombre1', 'o.mes', 'o.id_doctor', 'd.color')
            ->where('o.estado', '1')
            ->where('o.anio', $anio)
            ->where('o.mes', $mes)
            ->get();
        //dd($or_aniomes_doctor);
        return view('hc_admision.procedimientos.estad_anio_mes_doc', ['or_aniomes_doctor' => $or_aniomes_doctor, 'anio' => $anio, 'mes' => $mes]);
    }
    public function reporte_ordenes($id_doctor)
    {
       
        $ordenes  = Orden::where('orden.estado', '1')->where('id_doctor',$id_doctor)->orderBy('orden.fecha_orden')->get();

        $fecha_d = date('Y-m-d');

        Excel::create('Ordenes_doctor-' . $fecha_d, function ($excel) use ($fecha_d, $ordenes) {

            $excel->sheet('Historiaclinica', function ($sheet) use ($fecha_d, $ordenes) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');;
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 2;
                foreach ($ordenes as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->anio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        $cell->setValue($mes[$value->mes - 1]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $txt_pxs = '';
                        $tipos   = $value->orden_tipo;
                        foreach ($tipos as $tipo) {
                            $pxs = $tipo->orden_procedimiento;
                            foreach ($pxs as $px) {
                                $txt_pxs = $txt_pxs . '+' . $px->procedimiento->nombre;
                            }
                        }
                        $cell->setValue($txt_pxs);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

        })->export('xlsx');
    }

}
