<?php

namespace Sis_medico\Http\Controllers\enfermeria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Exception;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Camilla;
use Sis_medico\Hospital;
use Sis_medico\Sala;
use Sis_medico\Pentax;
use Sis_medico\Agenda;
use Sis_medico\Paciente;
use Carbon\Carbon;
use Sis_medico\Camilla_Gestion;
use Sis_medico\Log_Camilla;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Expr\Cast\String_;
use Sis_medico\Camilla_Gestion_Detalle;
use Sis_medico\Camilla_Gestion_Tipos;
use Sis_medico\User;

use function PHPSTORM_META\type;

class Control_CaidaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(11, 6, 1, 4,7)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hospital = Hospital::where('id', 2)->get();
        $cuadro = Camilla::where('id_hospital', 2)->get();
        $camilla = Camilla::where('id_hospital', 2)->paginate(6);
        return view('riesgo_caida.index', ['hospital' => $hospital, 'camilla' => $camilla, 'request' => $request, 'cuadro' => $cuadro]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo = $request['hospital_tipo'];
        $fecha = $request['fecha'];
        $camillas = $this->doSearchingQuery($tipo, $fecha);
        $hospital = Hospital::all();
        $hospital_tipo = $request['hospital_tipo'];
        $camilla = Camilla::all();
        //dd($tipo);
        return view('riesgo_caida.index', ['request' => $request, 'camillas' => $camillas, 'hospital' => $hospital, 'camilla' => $camillas]);
    }

    private function doSearchingQuery($tipo, $fecha)
    {
        $query = Camilla::where('estado', 1);
        if (!is_null($fecha)) {
            $query = $query->whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59']);
        }
        if (!is_null($tipo)) {
            $query = $query->where('id_hospital', $tipo);
        }
        return $query->paginate(5);
    }

    public function modal_riesgo_caida($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $camilla = Camilla::where('id', $id)->first();
        //dd($camilla);
        $hospi = Hospital::where('id', $camilla->id_hospital)->first();
        $operacion = Sala::where('id_hospital', $hospi->id)->get();
        $fecha =  date("Y-m-d");
        //dd($fecha);
        $db = DB::table('sala as s')
            ->join('pentax as p', 'p.id_sala', 's.id')
            ->join('agenda as a', 'a.id', 'p.id_agenda')
            ->where('s.id_hospital', $hospi->id)
            ->whereBetween('p.created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
            ->select('a.*')
            ->distinct()
            ->paginate(3);
        //dd($db);
        return view('riesgo_caida.modal_caida', ['operacion' => $operacion, 'camilla' => $camilla, 'hospi' => $hospi, 'db' => $db]);
    }
    public function buscar_estado(Request $request)
    {
        //dd($request['hospital_id'])

        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $camilla = $request['camilla_id'];
        $hospi = $request['hospital_id'];
        $db1 = DB::table('sala as s')
            ->join('pentax as p', 'p.id_sala', 's.id')
            ->join('agenda as a', 'a.id', 'p.id_agenda')
            ->where('s.id_hospital', $hospi)
            ->where("a.estado","<>",0)
            ->whereBetween('a.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select('a.*')
            ->distinct()
            ->get();
        $blocks = array();
        $paciente_agenda = Camilla_Gestion::whereIn('estado', ['0', '1'])->get();
        foreach ($paciente_agenda as $val) {
            if ($val->alta == 0  && $val->sala != 1 && $val->estado == 0) {
                array_push($blocks, $val->id_agenda);
            }
        }
        $db = DB::table('sala as s')
            ->join('pentax as p', 'p.id_sala', 's.id')
            ->join('agenda as a', 'a.id', 'p.id_agenda')
            ->where('s.id_hospital', $hospi)
            ->where("a.estado","<>",0)
            ->whereNotIn('a.id', $blocks)
            ->whereBetween('a.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select('a.*')
            ->distinct()
            ->get();
        //dd($db);
        return  view('riesgo_caida.tabla', ['db1' => $db1, 'camilla' => $camilla, 'hospi' => $hospi, 'paciente_agenda' => $paciente_agenda, 'db' => $db]);
    }
    public function tabla(Request $request)
    {
        $desde = $request['desde'];
        $hasta = $request['hasta'];


        $registro = DB::table('log_camilla as lc')
            ->join('camilla_gestion as cg', 'cg.id', 'lc.id_camagestion')
            ->where('cg.camilla', '<>', null)
            ->where('cg.estado', 0)
            ->select('lc.*', 'cg.*')
            ->groupBy('cg.id_paciente')
            ->whereBetween('cg.fecha_cambio', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderBy('cg.created_at', 'asc')
            ->get();
            //dd($registro);
        return  view('riesgo_caida.tabla_nueva', ['registro' => $registro]);
    }
    public function verificar(Request $request)
    {
        $array = [];
        $fecha =  date("Y-m-d");
        $nombre = $request['term'];
        $retorno = Pentax::where('id_sala', $nombre)->whereDate('created_at', date('Y-m-d'))->get();
        foreach ($retorno as $x) {


            $data = array();
            $data['id'] = $x->agenda->paciente->id;
            $data['paciente'] = $x->agenda->paciente->nombre1 . '' . $x->agenda->paciente->nombre2;
            $array[] = $data;
        }
        return response()->json($array);
    }
    public function calc_edad(Request $request)
    {
        $nombre = $request['term'];
        $paciente = Paciente::where('id', $nombre)->first();
        $fecha_actual = date('Y-m-d');
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
        return  response()->json($edad);
    }
    public function form_mayor(Request $request, $id, $id_camilla, $id_agenda)
    {
        $id_agenda = Agenda::where('id', $id_agenda)->first();
        $camilla = Camilla::where('id', $id_camilla)->first();
        $paciente = Paciente::where('id', $id)->first();
        return view('riesgo_caida.mayor_edad', ['camilla' => $camilla, 'paciente' => $paciente, 'id_agenda' => $id_agenda]);
    }

    public function form_menor(Request $request, $id, $id_camilla, $id_agenda)
    {
        $id_agenda = Agenda::where('id', $id_agenda)->first();
        $camilla = Camilla::where('id', $id_camilla)->first();
        $paciente = Paciente::where('id', $id)->first();
        return view('riesgo_caida.menor_edad', ['camilla' => $camilla, 'paciente' => $paciente, 'id_agenda' => $id_agenda]);
    }
    public function guardar_datos(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_paciente = $request['id_paciente'];
        $id_camilla = $request['id_camilla'];
        $id_total = $request['punt_final'];
        $id_agenda = $request['id_agenda'];
        $caida_previa = $request['caida_previa'];
        $comorbilidades = $request['comorbilidades'];
        $deambular = $request['deambular'];
        $venoclisis = $request['venoclisis'];
        $marcha = $request['marcha'];
        $estado_mental = $request['estado_mental'];
        $date = date("Y-m-d");
        $pacienteObeservacionAnt = Camilla_Gestion::where('camilla', $id_camilla)->where('estado', 0)->latest()->first();
        //Edad
        $paciente = Paciente::where('id', $id_paciente)->first();
        $fecha_actual = date('Y-m-d');
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
        if ($pacienteObeservacionAnt != null) {
            $log = Log_Camilla::where('id_camagestion', $pacienteObeservacionAnt->id)->where('estado', 0)->orderBy('created_at', 'desc')->first();
        }
        $idCama = 0;
        if (!is_null($id_camilla)) {
            $idCama = Camilla_Gestion::create([
                'id_paciente'           => $id_paciente,
                'id_agenda'             => $id_agenda,
                'camilla'               => $id_camilla,
                'nivel_riesgo'          => $id_total,
                'estado_uso'            => 2, //Ocupado
                'fecha_cambio'          => $date,
                'sala'                  => '',
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'estado'                => 1,
                'num_atencion'          => 1,
                'caida_previa'          => $caida_previa,
                'comorbilidades'        => $comorbilidades,
                'deambular'             => $deambular,
                'venoclisis'            => $venoclisis,
                'marcha'                => $marcha,
                'estado_mental'         => $estado_mental,
                'edad'                  => $edad,

            ]);
        } else {
            $idCama = Camilla_Gestion::create([
                'sala'                  => 1,
                'id_paciente'           => $id_paciente,
                'id_agenda'             => $id_agenda,
                'camilla'               => $id_camilla,
                'nivel_riesgo'          => $id_total,
                'fecha_cambio'          => $date,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'estado'                => 1,
                'num_atencion'          => 1,
                'caida_previa'          => $caida_previa,
                'comorbilidades'        => $comorbilidades,
                'deambular'             => $deambular,
                'venoclisis'            => $venoclisis,
                'marcha'                => $marcha,
                'estado_mental'         => $estado_mental,
                'edad'                  => $edad,

            ]);
        }

        if ($pacienteObeservacionAnt == null) {
            Log_Camilla::create([
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => '',
                'observacion_ante'      => '',
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        } else {
            Log_Camilla::create([
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => $pacienteObeservacionAnt->id_paciente,
                'observacion_ante'      => '',
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        }

        return response()->json($id_paciente);
    }
    public function modal_cambio($id)
    {
        $edit = Camilla_Gestion::where('camilla', $id)->where("estado", 1)->where('num_atencion', 1)->first();

        $paciente = Paciente::where('id', $edit->id_paciente)->first();
        return view('riesgo_caida.modal_cambio', ['edit' => $edit, 'paciente' => $paciente, 'id' => $id]);
    }
    public function cambio_estado(Request $request)
    {
        $nombre = $request['id_paciente'];
        $cambio = $request['observacion'];
        $estado = $request['estado'];
        $id_camilla = $request['id_camilla'];
        $id_agenda = $request['id_agenda'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cambio_estado = Camilla_Gestion::where('id_paciente', $nombre)->where('id_agenda', $id_agenda)->where('camilla', $id_camilla)->where('estado', 1)->where("num_atencion", 1)->first();
        $estadia = 0;
        $observacion = Log_Camilla::where('id_camagestion', $cambio_estado->id)->first();
        if ($estado == 3 || 4) {
            $estadia = 1;
        }
        if ($estado == 1) {
            $estadia = 0;
        }
        $input = [
            'estado_uso'           => $estado, //Libre
            'num_atencion'         => $estadia,

        ];
        $input1 = [
            'observacion'          => $cambio,
        ];
        $cambio_estado->update($input);
        $observacion->update($input1);
        return  response()->json($nombre);
    }

    public function guardar_datos_menor(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_paciente = $request['id_paciente'];
        $id_camilla = $request['id_camilla'];
        $id_total = $request['punt_final'];
        $id_agenda = $request['id_agenda'];
        $edad = $request['edad'];
        $ant_caida = $request['ant_caida'];
        $compro_conci = $request['compro_conci'];
        $antecedentes = $request['antecedentes'];
        $date = date("Y-m-d");
        $pacienteObeservacionAnt = Camilla_Gestion::where('camilla', $id_camilla)->where('estado', 0)->orderBy('created_at', 'desc')->first();
        if ($pacienteObeservacionAnt != null) {
            $log = Log_Camilla::where('id_camagestion', $pacienteObeservacionAnt->id)->where('estado', 0)->orderBy('created_at', 'desc')->first();
        }
        $idCama = 0;
        if (!is_null($id_camilla)) {
            $idCama =  Camilla_Gestion::create([
                'id_paciente'           => $id_paciente,
                'camilla'               => $id_camilla,
                'nivel_riesgo'          => $id_total,
                'estado_uso'            => 2, //Ocupado
                'id_agenda'             => $id_agenda,
                'fecha_cambio'          => $date,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'estado'                => 1,
                'num_atencion'          => 1,
                'edad'                  => $edad,
                'ant_caida_previa'      => $ant_caida,
                'compromiso_conciencia' => $compro_conci,
                'antecedentes'          => $antecedentes,
                'sala'                  => '',
            ]);
        } else {
            $idCama =  Camilla_Gestion::create([
                'sala'                  => 1,
                'id_paciente'           => $id_paciente,
                'nivel_riesgo'          => $id_total,
                'id_agenda'             => $id_agenda,
                'fecha_cambio'          => $date,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'estado'                => 1,
                'num_atencion'          => 1,
                'edad'                  => $edad,
                'ant_caida_previa'      => $ant_caida,
                'compromiso_conciencia' => $compro_conci,
                'antecedentes'          => $antecedentes,
            ]);
        }

        if ($pacienteObeservacionAnt == null) {
            Log_Camilla::create([

                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => '',
                'observacion_ante'      => '',
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        } else {
            Log_Camilla::create([
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => $pacienteObeservacionAnt->id_paciente,
                'observacion_ante'      => $log->observacion,
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        }

        return response()->json($id_paciente);
    }
    public function modal_estado(Request $request, $id)
    {
        $edit = Camilla_Gestion::where('camilla', $id)->orderBy('created_at', 'desc')->where('estado', 1)->first();
        $paciente = Paciente::where('id', $edit->id_paciente)->first();
        return view('riesgo_caida.modal_estado', ['edit' => $edit, 'paciente' => $paciente]);
    }
    public function cambio_estado_uno(Request $request)
    {

        $estado = $request['estado'];
        $id_camilla = $request['id_camilla'];
        $check = $request['check'];

        if (is_null($check)) {
            $check = false;
        } else {
            $check = true;
        }
        $cambio_estado = Camilla_Gestion::where('camilla', $id_camilla)->where('num_atencion', 1)->where('estado', 1)->orderBy('created_at', 'desc')->first();
        $log = Log_Camilla::where('id_camagestion', $cambio_estado->id)->first();
        if ($estado == 1) {
            $input1 = [
                'estado_uso'           => $estado,
                'num_atencion'         => 0,
                'estado'               => 0,
                'alta'                => $check,
            ];
            $log->estado  = 0;
            if ($check == false) {
                $cambio_estado->alta = 0;
                $cambio_estado->save();
            }
            $log->save();
        } else {
            $input1 = [
                'estado_uso'          => $estado,
                'num_atencion'         => 1,
            ];
        }
        $cambio_estado->update($input1);
        return response()->json($estado);
    }
    public function camas_estado(Request $request)
    {
        $tipo = $request['hospital_tipo'];
        $cuadro = Camilla::where('id_hospital', $tipo)->get();
        //dd($cuadro);
        return  view('riesgo_caida.mostrar_res', ['cuadro' => $cuadro]);
    }
    public function camas_estado_hab(Request $request)
    {
        $tipo = $request['hospital_tipo'];
        $camilla = Camilla::where('id_hospital', $tipo)->get();

        return  view('riesgo_caida.mostrar_uno_res', ['camilla' => $camilla]);
    }
    public function background_estado($id_cama)
    {
        $cama_id = Camilla_Gestion::where('camilla', $id_cama)->where('num_atencion', 1)->where('estado', 1)->first();
        //dd($cama_id);
        return view('riesgo_caida.background', ['id_cama' => $id_cama, 'cama_id' => $cama_id]);
    }
    public function actualizar_estado(Request $request)
    {

        $estado = $request['data'];
        $estado_libre = $request['estado'];
        $camilla = $request['camilla'];
        if (empty($estado) && empty($estado_libre)) {
            $comprobar = Camilla_Gestion::where('camilla', $camilla)->where('num_atencion', 1)->where('estado', 1)->orderBy('created_at', 'desc')->first();
            if (!is_null($comprobar)) {
                return response()->json("Listo");
            } elseif (is_null($comprobar)) {
                return response()->json("NoListo");
            }
        } elseif (!empty($estado) && !empty($estado_libre)) {
            $comprobar = Camilla_Gestion::where('camilla', $estado)->where('estado_uso', $estado_libre)->orderBy('created_at', 'desc')->first();
            if (!is_null($comprobar)) {
                return response()->json("Si");
            } elseif (is_null($comprobar)) {
                return response()->json("No");
            }
        }
    }
    public function registro()
    {
        $registro = DB::table('log_camilla as lc')
            ->join('camilla_gestion as cg', 'cg.id', 'lc.id_camagestion')
            ->where('cg.camilla', '<>', null)
            ->where('cg.estado', 0)
            ->select('lc.*', 'cg.*')
            ->groupBy('cg.id_paciente')
            ->whereBetween('cg.created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])
            ->paginate(10);
        //dd($registro);  
        return view('riesgo_caida.registro_camas', ['registro' => $registro]);
    }
    public function pdf_mayor_edad(Request $request, $id)
    {
        //dd($id);
        $camilla_gestion = Camilla_Gestion::where('id_agenda', $id)->first();

        $datosUsuarios = Paciente::where('id', $camilla_gestion->id_paciente)->first();
        $vistaurl = "riesgo_caida.pdf_mayor_edad";
        $view     = \View::make($vistaurl, compact('camilla_gestion', 'datosUsuarios'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Riesgo de Caída de Morse ' . '.pdf');
    }
    public function pdf_menor_edad(Request $request, $id)
    {

        $camilla_gestion = Camilla_Gestion::where('id_agenda', $id)->first();
        $datosUsuarios = Paciente::where('id', $camilla_gestion->id_paciente)->first();
        $vistaurl = "riesgo_caida.pdf_menor_edad";
        $view     = \View::make($vistaurl, compact('camilla_gestion', 'datosUsuarios'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Riesgo de Caída de Morse ' . '.pdf');
    }

    public function guardar_sinriesgo($id_paciente, $id_camilla, $id_agenda)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $date = date("Y-m-d");
        $sinSala = [];
        $cambio_estado = Camilla_Gestion::where('id_paciente', $id_paciente)->where('id_agenda', $id_agenda)->where('alta', 1)->first();
        if (is_null($cambio_estado)) {
            $sinSala = Camilla_Gestion::where('id_paciente', $id_paciente)->where('id_agenda', $id_agenda)->where('sala', 1)->first();
        }
        $pacienteObeservacionAnt = Camilla_Gestion::where('camilla', $id_camilla)->where('estado', 0)->latest()->first();
        if ($pacienteObeservacionAnt != null) {
            $log = Log_Camilla::where('id_camagestion', $pacienteObeservacionAnt->id)->where('estado', 0)->orderBy('created_at', 'desc')->first();
        }
        $idCama = 0;
        if (!is_null($cambio_estado)) {
            if ($cambio_estado->edad > 13) {
                $idCama = Camilla_Gestion::create([
                    'id_paciente'           => $id_paciente,
                    'id_agenda'             => $id_agenda,
                    'camilla'               => $id_camilla,
                    'nivel_riesgo'          => $cambio_estado->nivel_riesgo,
                    'estado_uso'            => 2, //Ocupado
                    'fecha_cambio'          => $date,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'num_atencion'          => 1,
                    'caida_previa'          => $cambio_estado->caida_previa,
                    'comorbilidades'        => $cambio_estado->comorbilidades,
                    'deambular'             => $cambio_estado->deambular,
                    'venoclisis'            => $cambio_estado->venoclisis,
                    'marcha'                => $cambio_estado->marcha,
                    'estado_mental'         => $cambio_estado->estado_mental,
                    'edad'                  => $cambio_estado->edad

                ]);
            } else {
                $idCama =  Camilla_Gestion::create([
                    'id_paciente'           => $id_paciente,
                    'camilla'               => $id_camilla,
                    'nivel_riesgo'          => $cambio_estado->nivel_riesgo,
                    'estado_uso'            => 2, //Ocupado
                    'id_agenda'             => $id_agenda,
                    'fecha_cambio'          => $date,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'num_atencion'          => 1,
                    'edad'                  => $cambio_estado->edad,
                    'ant_caida_previa'      => $cambio_estado->ant_caida_previa,
                    'compromiso_conciencia' => $cambio_estado->compromiso_conciencia,
                    'antecedentes'          => $cambio_estado->antecedentes,
                    'sala'                  => '',
                ]);
            }
        } else {
            if ($sinSala->edad >= 13) {
                $idCama = Camilla_Gestion::create([
                    'id_paciente'           => $id_paciente,
                    'id_agenda'             => $id_agenda,
                    'camilla'               => $id_camilla,
                    'nivel_riesgo'          => $sinSala->nivel_riesgo,
                    'estado_uso'            => 2, //Ocupado
                    'fecha_cambio'          => $date,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'num_atencion'          => 1,
                    'caida_previa'          => $sinSala->caida_previa,
                    'comorbilidades'        => $sinSala->comorbilidades,
                    'deambular'             => $sinSala->deambular,
                    'venoclisis'            => $sinSala->venoclisis,
                    'marcha'                => $sinSala->marcha,
                    'estado_mental'         => $sinSala->estado_mental,
                    'edad'                  => $sinSala->edad

                ]);
            } else {

                $idCama =  Camilla_Gestion::create([
                    'id_paciente'           => $id_paciente,
                    'camilla'               => $id_camilla,
                    'nivel_riesgo'          => $sinSala->nivel_riesgo,
                    'estado_uso'            => 2, //Ocupado
                    'id_agenda'             => $id_agenda,
                    'fecha_cambio'          => $date,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'num_atencion'          => 1,
                    'edad'                  => $sinSala->edad,
                    'ant_caida_previa'      => $sinSala->ant_caida_previa,
                    'compromiso_conciencia' => $sinSala->compromiso_conciencia,
                    'antecedentes'          => $sinSala->antecedentes,
                    'sala'                  => '',
                ]);
            }
        }
        if ($pacienteObeservacionAnt == null) {
            Log_Camilla::create([

                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => '',
                'observacion_ante'      => '',
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        } else {
            Log_Camilla::create([
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_paciente_ant'       => $pacienteObeservacionAnt->id_paciente,
                'observacion_ante'      => $log->observacion,
                'estado'                => 1,
                'id_camagestion'        => $idCama->id,
            ]);
        }

        return back();
    }
    //entrar sin camilla
    public function ocupar_por_sala()
    {
        $sala = Sala::where('estado', 1)->where('id_hospital', 2)->get();
        return view('riesgo_caida.ocupar_sala', ['sala' => $sala]);
    }
    public function buscar_estado_sin_cama(Request $request)
    {
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $db1 = DB::table('sala as s')
            ->join('pentax as p', 'p.id_sala', 's.id')
            ->join('agenda as a', 'a.id', 'p.id_agenda')
            ->where("a.estado","<>",0)
            ->where('s.id_hospital', 2)
            ->whereBetween('a.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select('a.*')
            ->distinct()
            ->get();
        $blocks = array();
        $paciente_agenda = Camilla_Gestion::whereIn('estado', ['0', '1'])->get();
        foreach ($paciente_agenda as $val) {
            if ($val->alta == 0) {
                array_push($blocks, $val->id_agenda);
            }
        }
        $db = DB::table('sala as s')
            ->join('pentax as p', 'p.id_sala', 's.id')
            ->join('agenda as a', 'a.id', 'p.id_agenda')
            ->where("a.estado","<>",0)
            ->where('s.id_hospital', 2)
            ->whereNotIn('a.id', $blocks)
            ->whereBetween('a.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select('a.*')
            ->distinct()
            ->get();

        return view('riesgo_caida.tabla_sin_cama', ['db1' => $db1, 'paciente_agenda' => $paciente_agenda, 'db' => $db]);
    }

    public function mayor_sincama($id_paciente, $id_agenda)
    {
        $id_agenda = Agenda::where('id', $id_agenda)->first();
        $paciente = Paciente::where('id', $id_paciente)->first();
        return view('riesgo_caida.mayor_edadsala', ['paciente' => $paciente, 'id_agenda' => $id_agenda]);
    }
    public function menor_sincama($id_paciente, $id_agenda)
    {
        $id_agenda = Agenda::where('id', $id_agenda)->first();
        $paciente = Paciente::where('id', $id_paciente)->first();
        return view('riesgo_caida.menor_edadsala', ['paciente' => $paciente, 'id_agenda' => $id_agenda]);
    }
    //vaciar camas
    public function actualizar_masivo()
    {
        $msj = '';
        try {
            Camilla_Gestion::whereIn('estado', ['0', '1'])->whereIn('num_atencion', ['0', '1'])->update(array('estado' => 0, 'num_atencion' => 0, 'alta' => 0, 'estado_uso' => 1));
            Log_Camilla::where('estado', 1)->update(array('estado' => 0));
        } catch (Exception $e) {

            return $msj = 'error';
        }
        return json_encode('ok');
    }
    //comprobar si hay session
    public function comprobar_sesion(Request $request)
    {

        $mensaje = '';
        if ($request->session()) {
            return $mensaje = 'ok';
        } else {
            return $mensaje = 'no';
        }
    }
    public function ocupacionCamilla(Request $request)
    {
        $verificar = Camilla_Gestion::where("camilla", $request->id_camilla)->where("estado", 1)->where("num_atencion", 1)->whereBetween("created_at", [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->first();
        if (!is_null($verificar)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }
    public function ocupacionEstadoCamilla(Request $request)
    {

        $verificar = Camilla_Gestion::where("camilla", $request->id_camilla)->latest('created_at')->first();
        if (isset($verificar)) {
            if ($verificar->num_atencion == 0 && $verificar->estado == 0 && $verificar->estado_uso == (int)$request->arr) {
                return json_encode(false);
            } elseif ($verificar->estado_uso == 1) {
                return json_encode(true);
            } elseif ($verificar->estado_uso !== (int)$request->arr) {
                return json_encode(true);
            } elseif ($verificar->estado_uso == 2 || $verificar->estado_uso ==  3 ||  $verificar->estado_uso == 4) {
                return json_encode(false);
            } elseif ($request->arr == null) {
                return json_encode(false);
            }
        }
    }
    
    public function update_camilla_detalle($id_camilla_gestion,$valor){
        $detalle = Camilla_Gestion_Detalle::find($id_camilla_gestion);
        if(!is_null($detalle)){
            $array_det = [
                'valor' => $valor,
            ];
            $detalle->update($array_det);
        }
        return ['status' => 'success', 'msj' => 'Guardo con exito'];
    }
}
