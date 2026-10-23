<?php

namespace Sis_medico\Http\Controllers\hospital_iess;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;

class HospitalizadosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11)) == false) {
            return true;
        }
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hospitalizados = DB::table('agenda as a')
            ->where('a.proc_consul', '3')
            ->where('a.estado', '1')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('seguros as s', 's.id', 'a.id_seguro')
            ->leftjoin('users as d', 'd.id', 'a.id_doctor1')
            ->select('a.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as snombre', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->paginate(30);
        return view('hospital_iess/hospitalizados/index', ['hospitalizados' => $hospitalizados, 'paciente' => null]);
    }

    public function altas()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hospitalizados = DB::table('agenda as a')->where('a.proc_consul', '3')->where('a.estado', '2')->join('paciente as p', 'p.id', 'a.id_paciente')->join('seguros as s', 's.id', 'a.id_seguro')->leftjoin('users as d', 'd.id', 'a.id_doctor1')->select('a.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as snombre', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->orderBy('a.fechafin', 'desc')->paginate(30);
        //dd($hospitalizados);
        return view('hospital_iess/hospitalizados/altas', ['hospitalizados' => $hospitalizados, 'paciente' => null]);
    }

    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $seguros = Seguro::all();

        $usuarios = User::where('estado', 1)->where('id_tipo_usuario', '3')->get();

        return view('hospital_iess/hospitalizados/create', ['seguros' => $seguros, 'usuarios' => $usuarios]);

    }

    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput2($request);

        //CREAR USUARIO
        $input_usu_c = [

            'id'              => $request['id'],
            'nombre1'         => strtoupper($request['nombre1']),
            'nombre2'         => strtoupper($request['nombre2']),
            'apellido1'       => strtoupper($request['apellido1']),
            'apellido2'       => strtoupper($request['apellido2']),
            'telefono1'       => '1',
            'telefono2'       => '1',
            'id_tipo_usuario' => 2,
            'email'           => $request['id'] . '@mail.com',
            'password'        => bcrypt($request['id']),
            'tipo_documento'  => 1,
            'estado'          => 1,
            'imagen_url'      => ' ',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];

        $user = User::find($request['id']);

        if (!is_null($user)) {
            //$user->update($input_usu_a);
        } else {
            User::create($input_usu_c);
        }

        $input_pac = [

            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'telefono1'          => '1',
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,

            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,

        ];

        $paciente = Paciente::find($request['id']);

        if (is_null($paciente)) {
            paciente::create($input_pac);

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant2'   => " PARENTESCO: Principal",
                'dato2'       => 'HOSPITALIZADO',
            ];

            Log_usuario::create($input_log);
        }

        $input_historia = [
            'fechaini'        => $request['fechaini'],
            'fechafin'        => $request['fechaini'],
            'id_paciente'     => $request['id'],
            'id_doctor1'      => $request['id_doctor1'],
            'procedencia'     => $request['procedencia'],
            'sala_hospital'   => $request['sala_hospital'],
            'proc_consul'     => 3,
            'estado_cita'     => 0,
            'observaciones'   => $request['observaciones'],
            'id_seguro'       => $request['id_seguro'],
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'cortesia'        => 'NO',
        ];

        $seguro = Seguro::find($request['id_seguro']);

        $id_agenda = agenda::insertGetId($input_historia);

        Log_agenda::create([
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => 0,

            'estado_cita'     => 0,
            'fechaini'        => $request['fechaini'],
            'fechafin'        => $request['fechaini'],
            'estado'          => 1,
            'cortesia'        => 'NO',
            'observaciones'   => $request['observaciones'],
            'id_doctor1'      => $request['id_doctor1'],

            'descripcion'     => 'HOSPITALIZADO',
            'descripcion2'    => 'INGRESO',
            'descripcion3'    => '',

            'campos'          => "UBICACION:" . $request['procedencia'] . " SALA:" . $request['sala_hospital'] . " SEGURO:" . $request['id_seguro'] . "-" . $seguro->nombre,

            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ]);

        return redirect()->intended('/hospitalizados');
    }

    private function validateInput2($request)
    {

        $rules = [

            'id_seguro'     => 'required|exists:seguros,id',
            'id'            => 'required|max:10',
            'nombre1'       => 'required|max:60',
            'nombre2'       => 'required|max:60',
            'apellido1'     => 'required|max:60',
            'apellido2'     => 'required|max:60',
            'procedencia'   => 'required|max:100',
            'sala_hospital' => 'required|max:100',
            //'id_doctor1' =>  'exists:users,id',
            'observaciones' => 'max:255',

        ];

        $messages = [

            'id_seguro.required'     => 'Selecciona el seguro.',
            'id_seguro.exists'       => 'Seguro no existe.',
            'id.required'            => 'Agrega la cédula.',
            'id.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'nombre1.required'       => 'Agrega el primer nombre.',
            'nombre2.required'       => 'Agrega el segundo nombre.',
            'nombre1.max'            => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'            => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'     => 'Agrega el primer apellido.',
            'apellido1.max'          => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'     => 'Agrega el segundo apellido.',
            'apellido2.max'          => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'procedencia.required'   => 'Agrega la ubicación.',
            'procedencia.max'        => 'La ubicación no puede ser mayor a :max caracteres.',
            'sala_hospital.required' => 'Agrega la sala.',
            'sala_hospital.max'      => 'La sala no puede ser mayor a :max caracteres.',
            'id_doctor1.exists'      => 'Doctor no existe.',
            'observaciones.max'      => 'La observación no puede ser mayor a :max caracteres.',
        ];

        $this->validate($request, $rules, $messages);
    }

    public function inactivar($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $hospitalizado = Agenda::find($id);
        $seguro        = Seguro::find($hospitalizado->id_seguro);
        if (!is_null($hospitalizado)) {

            Log_agenda::create([
                'id_agenda'          => $id,
                'estado_cita_ant'    => 0,
                'fechaini_ant'       => $hospitalizado->fechaini,
                'fechafin_ant'       => $hospitalizado->fechafin,
                'estado_ant'         => $hospitalizado->estado,
                'cortesia_ant'       => $hospitalizado->cortesia,
                'observaciones_ant'  => $hospitalizado->observaciones,
                'id_doctor1_ant'     => $hospitalizado->id_doctor1,
                'id_doctor2_ant'     => $hospitalizado->id_doctor2,
                'id_doctor3_ant'     => $hospitalizado->id_doctor3,
                'id_sala_ant'        => $hospitalizado->id_sala,

                'estado_cita'        => 0,
                'fechaini'           => $hospitalizado->fechaini,
                'fechafin'           => $hospitalizado->fechafin,
                'estado'             => $hospitalizado->estado,
                'cortesia'           => $hospitalizado->cortesia,
                'observaciones'      => 'ELIMINADO',
                'id_doctor1'         => $hospitalizado->id_doctor1,
                'id_doctor2'         => $hospitalizado->id_doctor2,
                'id_doctor3'         => $hospitalizado->id_doctor3,
                'id_sala'            => $hospitalizado->id_sala,

                'descripcion'        => 'HOSPITALIZADO',
                'descripcion2'       => 'ELIMINADO',
                'descripcion3'       => '',
                'campos_ant'         => "UBICACION:" . $hospitalizado->procedencia . " SALA:" . $hospitalizado->sala_hospital . " SEGURO:" . $hospitalizado->id_seguro . "-" . $seguro->nombre,
                'campos'             => "UBICACION:" . $hospitalizado->procedencia . " SALA:" . $hospitalizado->sala_hospital . " SEGURO:" . $hospitalizado->id_seguro . "-" . $seguro->nombre,
                'id_usuarioconfirma' => $hospitalizado->id_usuarioconfirma,

                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);

            $input = [

                'estado'          => 0,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $hospitalizado->update($input);

        }

        return redirect()->intended('/hospitalizados');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hospitalizado = Agenda::find($id);
        if (!is_null($hospitalizado)) {

            $paciente = Paciente::find($hospitalizado->id_paciente);

            $usuarios = User::where('estado', 1)->where('id_tipo_usuario', '3')->get();

            $seguros = Seguro::all();

            return view('hospital_iess/hospitalizados/edit', ['seguros' => $seguros, 'usuarios' => $usuarios, 'paciente' => $paciente, 'hospitalizado' => $hospitalizado]);

        }

    }

    private function validateedit($request)
    {

        $rules = [

            'procedencia'   => 'required|max:60',
            'sala_hospital' => 'required|max:60',
            //'id_doctor1' =>  'exists:users,id',
            'id_seguro'     => 'required|exists:seguros,id',
            'fechaini'      => 'required',
            'estado'        => 'required',
            'observaciones' => 'max:255',

        ];

        $messages = [
            'procedencia.required'   => 'Agrega la ubicación.',
            'procedencia.max'        => 'La ubicación no puede ser mayor a :max caracteres.',
            'sala_hospital.required' => 'Agrega la sala.',
            'sala_hospital.max'      => 'La sala no puede ser mayor a :max caracteres.',
            'id_seguro.required'     => 'Selecciona el seguro.',
            'id_seguro.exists'       => 'Seguro no existe.',
            'fechaini.required'      => 'Agrega la fecha de  ingreso.',
            'estado.required'        => 'Agrega el estado.',
            'observaciones.max'      => 'La observación no puede ser mayor a :max caracteres.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validatealta($request)
    {

        $rules = [

            'fechafin' => 'required|after_or_equal:' . $request['fechaini'],

        ];

        $messages = [

            'fechafin.required'       => 'Ingrese la fecha de alta.',
            'fechafin.after_or_equal' => 'La fecha de alta debe ser mayor a la fecha de ingreso.',
        ];

        $this->validate($request, $rules, $messages);
    }

    public function update2(Request $request, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $hospitalizado = Agenda::find($id);
        $seguro_ant    = Seguro::find($hospitalizado->id_seguro);
        $seguro        = Seguro::find($request['id_seguro']);
        $fechafin      = $request['fechaini'];
        $this->validateedit($request);
        if ($request['estado'] == '2') {
            $this->validatealta($request);
            $fechafin = $request['fechafin'];
        }
        if (!is_null($hospitalizado)) {

            $descripcion2 = "";
            if ($request['estado'] == '1') {
                $descripcion2 = 'ACTUALIZA';
            } else {
                $descripcion2 = 'DADO DE ALTA';
            }
            Log_agenda::create([
                'id_agenda'          => $id,
                'estado_cita_ant'    => 0,
                'fechaini_ant'       => $hospitalizado->fechaini,
                'fechafin_ant'       => $hospitalizado->fechafin,
                'estado_ant'         => $hospitalizado->estado,
                'cortesia_ant'       => $hospitalizado->cortesia,
                'observaciones_ant'  => $hospitalizado->observaciones,
                'id_doctor1_ant'     => $hospitalizado->id_doctor1,
                'id_doctor2_ant'     => $hospitalizado->id_doctor2,
                'id_doctor3_ant'     => $hospitalizado->id_doctor3,
                'id_sala_ant'        => $hospitalizado->id_sala,

                'estado_cita'        => 0,
                'fechaini'           => $request['fechaini'],
                'fechafin'           => $fechafin,
                'estado'             => $request['estado'],
                'cortesia'           => $hospitalizado->cortesia,
                'observaciones'      => $request['observaciones'],
                'id_doctor1'         => $request['id_doctor1'],
                'id_doctor2'         => $hospitalizado->id_doctor2,
                'id_doctor3'         => $hospitalizado->id_doctor3,
                'id_sala'            => $hospitalizado->id_sala,

                'descripcion'        => 'HOSPITALIZADO',
                'descripcion2'       => $descripcion2,
                'descripcion3'       => '',
                'campos_ant'         => "UBICACION:" . $hospitalizado->procedencia . " SALA:" . $hospitalizado->sala_hospital . " SEGURO:" . $hospitalizado->id_seguro . "-" . $seguro_ant->nombre,
                'campos'             => "UBICACION:" . $request['procedencia'] . " SALA:" . $request['sala_hospital'] . " SEGURO:" . $request['id_seguro'] . "-" . $seguro->nombre,
                'id_usuarioconfirma' => $hospitalizado->id_usuarioconfirma,

                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);

            $input = [
                'fechaini'        => $request['fechaini'],
                'fechafin'        => $fechafin,
                'id_doctor1'      => $request['id_doctor1'],
                'procedencia'     => $request['procedencia'],
                'sala_hospital'   => $request['sala_hospital'],
                'observaciones'   => $request['observaciones'],
                'id_seguro'       => $request['id_seguro'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'estado'          => $request['estado'],
            ];

            $hospitalizado->update($input);

        }
    }

    public function alta($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $hospitalizado = Agenda::find($id);
        $seguro        = Seguro::find($hospitalizado->id_seguro);
        if (!is_null($hospitalizado)) {

            Log_agenda::create([
                'id_agenda'          => $id,
                'estado_cita_ant'    => 0,
                'fechaini_ant'       => $hospitalizado->fechaini,
                'fechafin_ant'       => $hospitalizado->fechafin,
                'estado_ant'         => $hospitalizado->estado,
                'cortesia_ant'       => $hospitalizado->cortesia,
                'observaciones_ant'  => $hospitalizado->observaciones,
                'id_doctor1_ant'     => $hospitalizado->id_doctor1,
                'id_doctor2_ant'     => $hospitalizado->id_doctor2,
                'id_doctor3_ant'     => $hospitalizado->id_doctor3,
                'id_sala_ant'        => $hospitalizado->id_sala,

                'estado_cita'        => 0,
                'fechaini'           => $hospitalizado->fechaini,
                'fechafin'           => date('Y-m-d'),
                'estado'             => $hospitalizado->estado,
                'cortesia'           => $hospitalizado->cortesia,
                'observaciones'      => 'DADO DE ALTA',
                'id_doctor1'         => $hospitalizado->id_doctor1,
                'id_doctor2'         => $hospitalizado->id_doctor2,
                'id_doctor3'         => $hospitalizado->id_doctor3,
                'id_sala'            => $hospitalizado->id_sala,

                'descripcion'        => 'HOSPITALIZADO',
                'descripcion2'       => 'DADO DE ALTA',
                'descripcion3'       => '',
                'campos_ant'         => "UBICACION:" . $hospitalizado->procedencia . " SALA:" . $hospitalizado->sala_hospital . " SEGURO:" . $hospitalizado->id_seguro . "-" . $seguro->nombre,
                'campos'             => "UBICACION:" . $hospitalizado->procedencia . " SALA:" . $hospitalizado->sala_hospital . " SEGURO:" . $hospitalizado->id_seguro . "-" . $seguro->nombre,
                'id_usuarioconfirma' => $hospitalizado->id_usuarioconfirma,

                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);

            $input = [

                'estado'          => 2,
                'fechafin'        => date('Y-m-d'),
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $hospitalizado->update($input);

        }

        return redirect()->intended('/hospitalizados');

    }

    public function buscapaciente($id)
    {
        $paciente = Paciente::find($id);
        if (!is_null($paciente)) {
            return $paciente;
        } else {
            return 'no';
        }

    }

    public function buscar(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nombre_encargado = $request['paciente'];
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $hospitalizados = DB::table('agenda as a')->where('a.proc_consul', '3')->where('a.estado', '1')->join('paciente as p', 'p.id', 'a.id_paciente')->join('seguros as s', 's.id', 'a.id_seguro')->leftjoin('users as d', 'd.id', 'a.id_doctor1')->select('a.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as snombre', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->selectRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as completo")->whereRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) like '" . $seteo . "'")->paginate(30);

        return view('hospital_iess/hospitalizados/index', ['hospitalizados' => $hospitalizados, 'paciente' => $request['paciente']]);

    }

    public function buscar2(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nombre_encargado = $request['paciente'];
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $hospitalizados = DB::table('agenda as a')->where('a.proc_consul', '3')->where('a.estado', '2')->join('paciente as p', 'p.id', 'a.id_paciente')->join('seguros as s', 's.id', 'a.id_seguro')->leftjoin('users as d', 'd.id', 'a.id_doctor1')->select('a.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as snombre', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->selectRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as completo")->whereRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) like '" . $seteo . "'")->orderBy('a.fechafin', 'desc')->paginate(30);

        return view('hospital_iess/hospitalizados/altas', ['hospitalizados' => $hospitalizados, 'paciente' => $request['paciente']]);

    }

    public function log($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hosp     = Agenda::find($id);
        $paciente = Paciente::find($hosp->id_paciente);
        $logs     = DB::table('log_agenda as l')->where('l.id_agenda', $id)->get();

        return view('hospital_iess/hospitalizados/log', ['logs' => $logs, 'paciente' => $paciente]);

    }

    //reporte agenda
    public function reporte(Request $request)
    {

        setlocale(LC_ALL, 'Spanish_Ecuador');
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->paginate(5); //3=DOCTORES

        $this->rol();
        $seguros = seguro::all();
        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        if ($request['fecha_hasta'] == '') {
            $fecha_hasta = date('Y/m/d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }

        $hospitalizados = DB::select("SELECT l.*, p.id as pid, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color, a.procedencia, a.sala_hospital, a.estado, s.nombre as nombre_seguro
            FROM log_agenda l
              JOIN agenda a ON l.id_agenda = a.id
              JOIN paciente p ON p.id = a.id_paciente
              LEFT JOIN users d1 ON l.id_doctor1 = d1.id
              LEFT JOIN seguros s ON a.id_seguro = s.id
            WHERE l.descripcion = 'HOSPITALIZADO' AND
                a.omni like 'OM' AND
            l.created_at BETWEEN '" . $fecha . " 00:00' AND '" . $fecha_hasta . " 23:59'
            ORDER BY l.id_agenda, l.id DESC, l.fechaini ASC");

        return view('reportes/hospitalizados/index', ['hospitalizados' => $hospitalizados, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'seguros' => $seguros]);
    }
    public function excel(Request $request)
    {

        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        if ($request['fecha_hasta'] == '') {
            $fecha_hasta = date('Y/m/d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }

        Excel::create('Hospitalizados-' . $fecha, function ($excel) use ($fecha, $fecha_hasta) {

            $agenda_hosp = Agenda::where('proc_consul', '3')->where('estado', '1')->get();

            $hospitalizados = DB::select("SELECT l.*, p.id as pid, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color, a.procedencia, a.sala_hospital, a.estado, s.nombre as nombre_seguro
            FROM log_agenda l
              JOIN agenda a ON l.id_agenda = a.id
              JOIN paciente p ON p.id = a.id_paciente
              LEFT JOIN users d1 ON l.id_doctor1 = d1.id
              LEFT JOIN seguros s ON a.id_seguro = s.id
            WHERE l.descripcion = 'HOSPITALIZADO' AND
            l.created_at BETWEEN '" . $fecha . " 00:00' AND '" . $fecha_hasta . " 23:59'
            ORDER BY l.id_agenda, l.id DESC, l.fechaini ASC");

            $excel->sheet('Reporte Diario Hospitalizados', function ($sheet) use ($hospitalizados, $fecha, $fecha_hasta) {
                $i = 5;
                $sheet->mergeCells('A2:I2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTES HOSPITALIZADOS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:G3');
                $mes = substr($fecha, 5, 2);
                if ($mes == 01) {$mes_letra = "ENERO";}
                if ($mes == 02) {$mes_letra = "FEBRERO";}
                if ($mes == 03) {$mes_letra = "MARZO";}
                if ($mes == 04) {$mes_letra = "ABRIL";}
                if ($mes == 05) {$mes_letra = "MAYO";}
                if ($mes == 06) {$mes_letra = "JUNIO";}
                if ($mes == 07) {$mes_letra = "JULIO";}
                if ($mes == '08') {$mes_letra = "AGOSTO";}
                if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                if ($mes == '10') {$mes_letra = "OCTUBRE";}
                if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);
                $sheet->cell('A3', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue($fecha2);
                    $cell->setBackground('#c2f0f0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cells('A5:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO  ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ALTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $vid = "";
                foreach ($hospitalizados as $value) {
                    $texto = explode(" ", $value->campos);

                    if ($vid != $value->id_agenda) {
                        $vid = $value->id_agenda;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 0, 10));

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->papellido2 != "(N/A)") {
                                $cell->setValue($value->papellido1 . ' ' . $value->papellido2);
                            } else {
                                $cell->setValue($value->papellido1);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->pnombre2 != "(N/A)") {
                                $cell->setValue($value->pnombre1 . ' ' . $value->pnombre2);
                            } else {
                                $cell->setValue($value->pnombre1);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->pid);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->d1nombre1 . " " . $value->d1apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('F' . $i, function ($cell) use ($value, $texto) {
                            // manipulate the cel
                            if (count($texto) > 2) {
                                $cell->setValue(substr($texto[2], 9, 20));
                            } else {
                                $cell->setValue($value->nombre_seguro);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->estado == '2') {
                                $cell->setValue(substr($value->fechafin, 0, 10));
                            } else {
                                $cell->setValue('');
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $i = $i + 1;

                    }

                }

            }

            );
        })->export('xlsx');
    }

}
