<?php

namespace Sis_medico\Http\Controllers\servicios;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Sis_medico\hc_imagenes_protocolo;
use Response;
use DatePeriod;
use Mail;
use DateInterval;
use Sis_medico\Agenda;
use Sis_medico\Api_App;
use Sis_medico\Apps_Agenda;
use Sis_medico\Apps_Banners;
use Sis_medico\Apps_Charlas;
use Sis_medico\Apps_Plan_Miembros;
use Sis_medico\Apps_Ratings;
use Sis_medico\Apps_Solicitudes;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\hc_protocolo;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_Doctor;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_Api;
use Sis_medico\Log_Apps;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Membresia;
use Sis_medico\MembresiaDetalle;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Procedimiento;
use Sis_medico\User;
use Sis_medico\UserMembresia;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;

class ServiciosIecedController extends Controller
{
    public function login(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result'     => '2',
                    'status'     => '',
                    'idPaciente' => '',
                    'idUsuario'  => '',
                    'message'    => 'No hay token.',
                ]);
            }
            $email    = $request['email'];
            $password = $request['pass'];
            if (is_null($email)) {
                return response()->json([
                    'result'     => '10',
                    'status'     => '0',
                    'idPaciente' => '',
                    'message'    => 'No hay email.',
                    'idUsuario'  => '',
                ]);
            }
            $usuario = User::where('email', $email)->first();
            if (is_null($usuario)) {
                $usuario = User::find($email);
            }
            if (!is_null($usuario) && $usuario != '[]') {
                $imagen_url = $usuario->imagen_url;
                if (is_null($imagen_url) || $imagen_url == ' ') {
                    $imagen_url = asset('') . '../storage/app/avatars/avatar.jpg';
                } else {
                    $imagen_url = asset('') . '../storage/app/avatars/' . $usuario->imagen_url;
                }
                $name2 = "";
                if ($usuario->nombre2 != '(N/A)') {
                    $name2 = $usuario->nombre2;
                }
                $surname2 = "";
                if ($usuario->apellido2 != '(N/A)') {
                    $surname2 = $usuario->apellido2;
                }

                if (!is_null($password)) {
                    if (!Hash::check($password, $usuario->password)) {
                        Log_Apps::create([
                            'id_user'     => $usuario->id,
                            'id_empresa'  => '0992704152001',
                            'observacion' => 'Contrasena Incorrecta',
                            'estado'      => 0,
                        ]);
                        return response()->json([
                            'result'     => '3', //contraseña incorrecta
                            'status'     => '',
                            'message'    => 'Contraseña Incorrecta.',
                            'idPaciente' => '',
                            'idUsuario'  => '',
                        ]);
                    }

                    $paciente      = Paciente::where('id', $usuario->id)->first();
                    $userMembresia = UserMembresia::join('membresia as m', 'm.id', 'user_membresia.membresia_id')->where('m.empresa_id', '0992704152001')->where('user_membresia.user_id', $usuario->id)->where('user_membresia.estado', '1')->first();
                    //dd($userMembresia);
                    if (!is_null($paciente)) {
                        if (!is_null($userMembresia)) {
                            //dd($userMembresia);
                            $userdetails = MembresiaDetalle::where('membresia_id', $userMembresia->membresia->id)->get();
                            //dd($userMembresia->id);
                            Log_Apps::create([
                                'id_user'     => $usuario->id,
                                'id_empresa'  => '0992704152001',
                                'observacion' => 'Correcto',
                                'estado'      => 1,
                            ]);
                            return response()->json([
                                'idPaciente'      => $paciente->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $usuario->apellido1,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'date'            => $usuario->fecha_nacimiento,
                                'telefono'        => $usuario->telefono1,
                                'photo'           => asset('') . '../storage/app/avatars/' . $usuario->imagen_url,
                                'user'            => $usuario,
                                'email'           => $usuario->email,
                                'membresia'       => $userMembresia->membresia,
                                'membresiaDetail' => $userdetails,
                                'checkIn'         => '1',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        } else {
                            Log_Apps::create([
                                'id_user'     => $usuario->id,
                                'observacion' => 'Correcto',
                                'id_empresa'  => '0992704152001',
                                'estado'      => 1,
                            ]);
                            return response()->json([
                                'idPaciente'      => $paciente->id,
                                'idUsuario'       => $usuario->id,
                                'email'           => $usuario->email,
                                'user'            => $usuario,
                                'photo'           => asset('') . '../storage/app/avatars/' . $usuario->imagen_url,
                                'name'            => $usuario->nombre1 . ' ' . $usuario->apellido1,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'membresia'       => '',
                                'membresiaDetail' => '',
                                'message'         => 'No tiene membresia.',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        }
                    } else {
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
                        ]);
                        if (!is_null($userMembresia)) {
                            $userdetails = MembresiaDetalle::where('membresia_id', $userMembresia->membresia->id)->get();
                            //dd($userMembresia->id);
                            Log_Apps::create([
                                'id_user'     => $usuario->id,
                                'observacion' => 'Correcto',
                                'id_empresa'  => '0992704152001',
                                'estado'      => 1,
                            ]);
                            return response()->json([
                                'idPaciente'      => $usuario->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $usuario->apellido1,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'email'           => $usuario->email,
                                'user'            => $usuario,
                                'photo'           => asset('') . '../storage/app/avatars/' . $usuario->imagen_url,
                                'membresia'       => $userMembresia->membresia,
                                'membresiaDetail' => $userdetails,
                                'checkIn'         => '1',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        } else {
                            Log_Apps::create([
                                'id_user'     => $usuario->id,
                                'observacion' => 'Correcto',
                                'id_empresa'  => '0992704152001',
                                'estado'      => 1,
                            ]);
                            return response()->json([
                                'idPaciente'      => $usuario->id,
                                'idUsuario'       => $usuario->id,
                                'name'            => $usuario->nombre1 . ' ' . $usuario->apellido1,
                                'surname'         => $usuario->apellido1 . ' ' . $surname2,
                                'email'           => $usuario->email,
                                'photo'           => asset('') . '../storage/app/avatars/' . $usuario->imagen_url,
                                'membresia'       => '',
                                'email'           => '',
                                'user'            => $usuario,
                                'membresiaDetail' => '',
                                'status'          => $usuario->estado,
                                'result'          => '1',
                            ]);
                        }
                        return response()->json([
                            'result'     => '5',
                            'status'     => '',
                            'idPaciente' => '',
                            'idUsuario'  => '',
                            'message'    => 'No existe como paciente',
                        ]);
                    }
                } else {
                    return response()->json([
                        'result'     => '2',
                        'status'     => '0',
                        'checkIn'    => '0',
                        'idPaciente' => '',
                        'idUsuario'  => '',
                        'message'    => 'No tiene password.',
                    ]);
                }
            } else {
                return response()->json([
                    'result'     => '4',
                    'status'     => '0',
                    'idPaciente' => '',
                    'message'    => 'No existe usuario.',
                    'idUsuario'  => '',
                ]);
            }
        } else {
            $post = [
                'token' => '8c0a00ec19933215dc29225e645ea714',
                'email' => $request['email'],
                'pass'  => $request['pass'],
            ];

            $ch = curl_init('http://siaam.ec/sis_medico/public/api/login');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
            // close the connection, release resources used

        }
    }
    public function drlist(Request $request)
    {
        $global = $request['city'];
        if ($request->token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json(['result' => '0', 'message' => 'Token incorrecto', 'status' => 'error']);
        }
        $apiApp = Api_App::where('city', $global)->where('estado', 1)->first();
        $list   = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('tipo_documento', 'asc')
            ->whereIn('id', ['1314490929', '1307189140', '1713796835', '0916461221', '1306579234', '9666666666'])->get();
        /* $list = Doctor_Tiempo::orderBy('ip_creacion', 'desc')->join('users as u', 'u.id', 'doctor_tiempo.id_doctor')->where('doctor_tiempo.id_doctor', '<>', 4444444444)->where('doctor_tiempo.id_doctor', '<>', 0)->where('doctor_tiempo.id_doctor', '<>', 3596988777)->select('u.*', 'doctor_tiempo.precio as precio')->get(); */
        $information = array();

        foreach ($list as $l) {
            $name2 = "";
            if ($l->nombre2 != '(N/A)') {
                $name2 = $l->nombre2;
            }
            $surname2 = "";
            if ($l->apellido2 != '(N/A)') {
                $surname2 = $l->apellido2;
            }
            $price = '65.00';
            if ($l->id == '1307189140') {
                $price = '80.00';
            }
            if ($l->id == '1314490929') {
                $price = '70.00';
            }
            if ($l->imagen_url != ' ' && !is_null($l->imagen_url)) {
                $pl = [
                    'idUsuario' => $l->id,
                    'name'      => $l->nombre1 . ' ' . $l->apellido1,
                    'surname'   => $l->apellido1 . ' ' . $surname2,
                    'email'     => $l->email,
                    'shedule'   => $this->shedule($l->id),
                    'prox'      => '1',
                    'price'     => $price,
                    'photo'     => asset('') . '../storage/app/avatars/' . $l->imagen_url,
                    'status'    => $l->estado,
                    'result'    => '1',
                ];
                array_push($information, $pl);
            } else {
                $pl = [
                    'idUsuario' => $l->id,
                    'name'      => $l->nombre1 . ' ' . $name2,
                    'surname'   => $l->apellido1 . ' ' . $surname2,
                    'email'     => $l->email,
                    'shedule'   => $this->shedule($l->id),
                    'prox'      => '1',
                    'price'     => $price,
                    'photo'     => asset('') . '../storage/app/avatars/avatar.jpg',
                    'status'    => $l->estado,
                    'result'    => '1',
                ];
                array_push($information, $pl);
            }
        }
        return response()->json([
            'result'  => '2',
            'message' => 'Correcto.',
            'status'  => 'ok',
            'list'    => $information,
        ]);
    }
    public function loadImage($url)
    {
        $file     = File::get($url);
        $type     = File::mimeType($url);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
    public function shedule($id)
    {
        $shedule      = Horario_Doctor::where('id_doctor', $id)->whereIn('tipo', [0, 3])->orderBy('ndia', 'ASC')->where('estado', 1)->get();
        $informationx = array();
        $days         = 8;
        $daysini      = 0;
        /*  foreach($shedule as $s){

        $pl['nDay']=$s->ndia;
        $pl['Day']=$s->dia;
        $pl['hourIni']=$s->hora_ini;
        $pl['hourFin']=$s->hora_fin;
        array_push($information,$pl);
        } */
        for ($i = 1; $i < $days; $i++) {
            $shedule = Horario_Doctor::where('id_doctor', $id)->where('ndia', $i)->where('estado', 1)->first();
            if (!is_null($shedule)) {
                $informationwow['dayHour'] = $i;
                $informationwow['dayName'] = $shedule->dia;
                $information               = array();
                $shedule                   = Horario_Doctor::where('id_doctor', $id)->where('ndia', $i)->whereIn('tipo', [0, 3])->where('estado', 1)->orderBy('hora_ini', 'ASC')->get();
                $firsthour                 = "No disponible";
                $lasthour                  = "";
                $count                     = 0;
                foreach ($shedule as $s) {
                    if ($count == 0) {
                        $firsthour = $s->hora_ini;
                    }

                    $pl['nDay']    = $s->ndia;
                    $pl['Day']     = $s->dia;
                    $pl['hourIni'] = $s->hora_ini;
                    $pl['hourFin'] = $s->hora_fin;
                    array_push($information, $pl);
                    $count++;
                    if ($count == count($shedule)) {
                        $lasthour = $s->hora_fin;
                    }
                }
                $informationwow['firshour'] = $firsthour;
                $informationwow['lasthour'] = $lasthour;
                $informationwow['shedule']  = $information;
                array_push($informationx, $informationwow);
            }
        }
        return $informationx;
    }
    public function proxAgend($id_doctor)
    {
        //date_default_timezone_set('Europe/London');

        $doctor      = User::find($id_doctor);
        $tiempo_cita = $doctor->tiempo_cita;

        $fechahoy  = Date('Y-m-d H:i:s');
        $var_fecha = $fechahoy;
        $loop      = 0;
        while ($loop <= 100) {
            $n_dia    = date('N', strtotime($var_fecha));
            $horarios = Horario_Doctor::where('id_doctor', $id_doctor)->where('ndia', $n_dia)->orderBy('ndia', 'asc')->get();
            foreach ($horarios as $value) {
                $fecha_dia = Date('Y-m-d', strtotime($var_fecha));
                $hora_ini  = $value->hora_ini;
                $i         = 1;
                $hora_fin  = $value->hora_fin;
                $rini      = $hora_ini;
                if ($tiempo_cita == null) {
                    $tiempo_cita = 30;
                }
                $rfin   = Date('H:i:s', strtotime("+" . $tiempo_cita . " minutes", strtotime($rini)));
                $inicio = $fecha_dia . " " . $rini;
                $fec_1h = Date('Y-m-d H:00:00', strtotime("+1 hours", strtotime($fechahoy)));
                $fin    = $fecha_dia . " " . $rfin;
                //dd($inicio,$fin,$rfin,$hora_fin);
                if ($fec_1h >= $inicio) {
                    $inicio = $fec_1h;
                    $rini   = Date('H:i:s', strtotime($inicio));
                    $rfin   = Date('H:i:s', strtotime("+" . $tiempo_cita . " minutes", strtotime($rini)));
                    $fin    = $fecha_dia . " " . $rfin;
                }
                //dd($inicio,$fin,$rfin,$hora_fin,$tiempo_cita);
                while ($rfin <= $hora_fin) {
                    //VALIDA AGENDA
                    $inicio2 = Date('Y-m-d H:i:s', strtotime("+1 seconds", strtotime($inicio)));
                    $fin2    = Date('Y-m-d H:i:s', strtotime("-1 seconds", strtotime($fin)));

                    $dato2 = DB::table('agenda')->where('id', '<>', $id_doctor)
                        ->where(function ($query) use ($inicio2, $fin2, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })->where(function ($query) use ($inicio2, $fin2, $id_doctor) {
                            return $query->whereRaw("(('" . $inicio2 . "' BETWEEN fechaini and fechafin)")
                                ->orWhere(
                                    function ($query) use ($inicio2, $fin2, $id_doctor) {
                                        $query->whereRaw("'" . $fin2 . "' BETWEEN fechaini and fechafin)");
                                    }
                                )
                                ->orWhere(function ($query) use ($inicio2, $fin2, $id_doctor) {
                                    $query->whereRaw("(fechaini BETWEEN '" . $inicio2 . "' and '" . $fin2 . "'");
                                })
                                ->orWhere(function ($query) use ($inicio2, $fin2, $id_doctor) {
                                    $query->whereRaw("fechafin BETWEEN '" . $inicio2 . "' and '" . $fin2 . "')");
                                });
                        })->where(function ($query) {
                            return $query->where('estado', 1);
                        })->get();

                    if ($i == 5) {
                        //dd($inicio,$fin,$dato2->count(),$i,$rfin,$hora_fin);
                    }
                    if ($dato2->count() == 0) {
                        date_default_timezone_set('UTC');
                        return $inicio;
                        /*  return redirect()->route('agenda.nuevo2', ['id' => $id_doctor, 'fecha' => strtotime($inicio), 'i' => 0]); */
                    }
                    //dd($dato2);

                    if ($i > 50) {
                        break;
                    }
                    $i++;
                    $rini = $rfin;
                    $rfin = Date('H:i:s', strtotime("+" . $tiempo_cita . " minutes", strtotime($rini)));
                    if ($rfin == "00:00:00") {
                        break;
                    }
                    $inicio = $fecha_dia . " " . $rini;
                    $fin    = $fecha_dia . " " . $rfin;
                    //dd($inicio,$fin);
                }
            }

            $loop++;
            $var_fecha = Date('Y-m-d H:i:s', strtotime("+1 days", strtotime($var_fecha)));
        }
    }
    public function generarOrdenExamen(Request $request)
    {
        $pl = Contable::generarOrden($request->all());
        return response()->json(['status' => 'ok', 'msj' => $pl, 'data' => $request->all()]);
    }
    public function buildPatient(Request $request)
    {

        $usuario = User::find($request->id);
        $name2   = "";
        if ($usuario->nombre2 != '(N/A)') {
            $name2 = $usuario->nombre2;
        }
        $surname2 = "";
        if ($usuario->apellido2 != '(N/A)') {
            $surname2 = $usuario->apellido2;
        }
        $paciente = Paciente::where('id', $usuario->id)->first();
        if (!is_null($paciente)) {

            return response()->json([
                'idPaciente' => $paciente->id,
                'idUsuario'  => $usuario->id,
                'email'      => $usuario->email,
                'user'       => $usuario,
                'name'       => $usuario->nombre1 . ' ' . $usuario->apellido1,
                'surname'    => $usuario->apellido1 . ' ' . $surname2,
                'status'     => $usuario->estado,
                'result'     => '1',
                'message'    => 'Correcto',
            ]);
        } else {
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
                'imagen_url'       => '',
                'fecha_nacimiento' => $usuario->fecha_nacimiento,
                'telefono1'        => $usuario->telefono1,
                'telefono2'        => $usuario->telefono2,
            ]);

            return response()->json([
                'idPaciente' => $usuario->id,
                'idUsuario'  => $usuario->id,
                'name'       => $usuario->nombre1 . ' ' . $usuario->apellido1,
                'surname'    => $usuario->apellido1 . ' ' . $surname2,
                'email'      => $usuario->email,
                'user'       => $usuario,
                'status'     => $usuario->estado,
                'result'     => '1',
                'message'    => 'Correcto',
            ]);
        }
    }
    //use this tomorrow
    public function agendPatient($request)
    {
        //dd($id);
        $id = $request['id_usuario'];
        $teleconsulta=0;
        if($request['mod']=='ONLINE'){
            $teleconsulta=1;
        }
        $paciente = Paciente::find($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = $request['id_doctor'];
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();
        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }
        $input_agenda = [
            'fechaini'        => $request['dateini'],
            'fechafin'         => $request['dateend'],
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '3',
            'id_empresa'      => $request['id_empresa'],
            'espid'           => $espid,
            'tipo_cita'       => 1,
            'tc'              => $teleconsulta,
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_seguro'       =>  1, //change this
            'estado'          => '0', //First state 0 when i confirmed i will change
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => '0000000004',
            'id_usuariomod'   => '0000000004',
        ];
        $id_agenda = Agenda::insertGetId($input_agenda);
        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => $request['dateini'],
            'fechafin'        => $request['dateend'],
            'estado'          => '1',
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'FUE PAGADO DESDE LA APP',

            'id_usuariomod'   => '0000000004',
            'id_usuariocrea'  => '0000000004',
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        $idusuario = $id_doctor;
        Log_Agenda::create($input_log);
        return $id_agenda;
    }
    public function getuserinfo(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }

        if ($global == '1') {
            $idusuario = $request['idUsuario'];
            if (!is_null($idusuario)) {
                $token = $request['token'];
                if ($token != '8c0a00ec19933215dc29225e645ea714') {
                    return response()->json([
                        'result'    => '2',
                        'names'     => '',
                        'surnames'  => '',
                        'email'     => '',
                        'telefono'  => '',
                        'direccion' => '',
                        'status'    => '',
                        'response'  => 'Token incorrecto',
                    ]);
                }
                $user = User::where('id', $idusuario)->first();

                if (!is_null($user) && $user != '[]') {
                    return response()->json([
                        'result'          => '1',
                        'names'           => $user->nombre1,
                        'surnames'        => $user->apellido1,
                        'email'           => $user->email,
                        'telefono'        => $user->telefono,
                        'direccion'       => $user->direccion,
                        'status'          => $user->estado,
                        'membresia'       => '',
                        'message'         => 'No tiene membresia',
                        'membresiaDetail' => '',
                    ]);
                } else {
                    return response()->json([
                        'result'    => '3',
                        'names'     => '',
                        'surnames'  => '',
                        'email'     => '',
                        'telefono'  => '',
                        'direccion' => '',
                        'message'   => 'No existe Usuario.',
                        'status'    => '',
                    ]);
                }
            } else {
                return response()->json([
                    'result'    => '4',
                    'names'     => '',
                    'surnames'  => '',
                    'email'     => '',
                    'telefono'  => '',
                    'direccion' => '',
                    'message'   => 'No hay id.',
                    'status'    => '',
                ]);
            }
        } else {
            $post = [
                'token'     => '8c0a00ec19933215dc29225e645ea714',
                'idUsuario' => $request['idUsuario'],
            ];
            return response()->json(['status' => 'ok']);

            /*  $ch = curl_init('http://siaam.ec/sis_medico/public/api/getuserinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return response()->json(json_decode($response)); */
        }
    }
    public function dashboard(Request $request)
    {
        $datos              = Log_Apps::join('users as u', 'u.id', 'log_apps.id_user')->where('log_apps.id_empresa', session()->get('id_empresa'))->where('log_apps.estado', 1)->groupBy('log_apps.id_user')->select(DB::raw('COUNT(log_apps.id) as total'), 'u.id as id', 'u.nombre1 as nombre1', 'u.apellido1 as apellido1', 'u.imagen_url as imagen_url')->paginate(10);
        $totales_conectados = Log_Apps::join('users as u', 'u.id', 'log_apps.id_user')->where('log_apps.id_empresa', session()->get('id_empresa'))->where('log_apps.estado', 1)->count();
        if (Auth::check()) {
        } else {
            return redirect()->route('login');
        }
        return view('dashboard_apps.index', ['datos' => $datos, 'totales_conectados' => $totales_conectados]);
    }
    public function banners(Request $request)
    {
        $url     = array();
        $banners = Apps_Banners::where('estado', 1)->where('id_empresa', '0992704152001')->get();
        foreach ($banners as $banners) {

            $urls1['url']         = asset('') . '../storage/app/avatars/' . $banners->url;
            $urls1['descripcion'] = $banners->descripcion;
            $urls1['id']          = $banners->id;
            $urls1['tipo']        = $banners->tipo;
            $urls1['link']        = $banners->link;
            if ($banners->tipo == 'Home') {
                $urls1['image']      = asset('') . '../storage/app/avatars/' . $banners->url;
                $urls1['background'] = $banners->color;
                $urls1['key']        = $banners->id;
                $urls1['text']       = $banners->descripcion;
                $urls1['tipo']       = $banners->tipo;
                $urls1['title']      = 'IECED';
            }
            array_push($url, $urls1);
        }
        $url = Contable::groupBy($url, 'tipo');
        return response()->json($url);
    }
    public function charlas(Request $request)
    {
        $url     = array();
        $charlas = Apps_Charlas::where('estado', 1)->where('id_empresa', '0992704152001')->get();
        foreach ($charlas as $charlas) {
            $anterior = 0;
            if ($charlas->fecha <= date('Y-m-d')) {
                $anterior = 1;
            }
            $user        = User::find($charlas->id_doctor);
            $c['nombre'] = $user->nombre1 . ' ' . $user->apellido1;
            if ($user->imagen_url != null && $user->imagen_url != ' ') {
                $c['imagen'] = asset('') . '../storage/app/avatars/' . $user->imagen_url;
            } else {
                $c['imagen'] = asset('') . '../storage/app/avatars/' . 'avatar.jpg';
            }
            $c['url']   = $charlas->url;
            $c['fecha'] = date('d/m/Y', strtotime($charlas->fecha));
            $c['hora']  = date('H:i', strtotime($charlas->fecha));
            $fec        = new DateTime($charlas->fecha);
            $fec2       = new DateTime(date('Y-m-d H:i:s'));
            $diff       = $fec2->diff($fec);
            /*  $days= $diff->days. ' Dias '; */
            $c['evento']    = $charlas->descripcion;
            $c['Fdias']     = $diff->format('%r%a');
            $c['pased']     = $anterior;
            $c['Fminutos']  = $diff->i . ' Minutos';
            $c['Fsegundos'] = $diff->s . ' Segundos';
            array_push($url, $c);
        }
        return response()->json($url);
    }
    public function charlasToday()
    {
        $url = array();
        $charlas = Apps_Charlas::where('estado', 1)->whereDate('fecha', date('Y-m-d'))->where('id_empresa', '0992704152001')->get();
        foreach ($charlas as $charlas) {
            $anterior = 0;
            if ($charlas->fecha <= date('Y-m-d')) {
                $anterior = 1;
            }
            $user        = User::find($charlas->id_doctor);
            $c['nombre'] = $user->nombre1 . ' ' . $user->apellido1;
            if ($user->imagen_url != null && $user->imagen_url != ' ') {
                $c['imagen'] = asset('') . '../storage/app/avatars/' . $user->imagen_url;
            } else {
                $c['imagen'] = asset('') . '../storage/app/avatars/' . 'avatar.jpg';
            }
            $c['url']   = $charlas->url;
            $c['fecha'] = date('d/m/Y', strtotime($charlas->fecha));
            $c['hora']  = date('H:i', strtotime($charlas->fecha));
            $fec        = new DateTime($charlas->fecha);
            $fec2       = new DateTime(date('Y-m-d H:i:s'));
            $diff       = $fec2->diff($fec);
            /*  $days= $diff->days. ' Dias '; */
            $c['evento']    = $charlas->descripcion;
            $c['Fdias']     = $diff->format('%r%a');
            $c['pased']     = $anterior;
            $c['Fminutos']  = $diff->i . ' Minutos';
            $c['Fsegundos'] = $diff->s . ' Segundos';
            array_push($url, $c);
        }
        return response()->json($url);
    }
    public function membresias(Request $request)
    {
        $url        = array();
        $membresias = Membresia::where('estado', 1)->where('empresa_id', '0992704152001')->get();
        $contador   = 0;
        foreach ($membresias as $membresias) {

            $mem['id']           = $membresias->id;
            $mem['nombre']       = $membresias->nombre;
            $mem['precio_anual'] = $membresias->precio_anual;
            $mem['url']          = asset('') . '../storage/app/avatars/' . $membresias->url;
            $mem['detalles']     = $membresias->detalles;
            $mem['index']        = $contador;
            array_push($url, $mem);
            $contador++;
        }
        return response()->json($url);
    }
    public function historialConsultas(Request $request)
    {
        $id_paciente = $request->id;
        $agendas     = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->select('agenda.*', 'agenda.id as id_ag', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'sala.nombre_sala as sala_nombre', 'd1.id as id_doctor')->orderby('agenda.fechaini', 'desc')->where('agenda.id_paciente', $id_paciente)->where(function ($query) {
            $query->where('proc_consul', '0')->orWhere('omni', '=', 'OM');
        });
        $agendas = $agendas->get();
        //dd($agendas);
        $xk = array();
        foreach ($agendas as $a) {
            $user2 = User::find($a->id_doctor);
            $p['id']          = $a->id_ag;
            $p['dnombre1']    = $a->dnombre1;
            $p['dapellido1']  = $a->dapellido1;
            $photo = asset('') . '../storage/app/avatars/' . $user2->imagen_url;
            if (is_null($user2->imagen_url) || $user2->imagen_url == ' ') {
                $photo = asset('') . '../storage/app/avatars/avatar.jpg';
            }
            $p['photo'] = $photo;
            $p['fecha']       = date('d/m/Y', strtotime($a->fechaini));
            $p['hora']        = date('H:i:s', strtotime($a->fechaini)) . ' - ' . date('H:i:s', strtotime($a->fechafin));
            $p['sala_nombre'] = $a->sala_nombre;
            array_push($xk, $p);
        }
        return response()->json($xk);
    }
    public function historialRecetas(Request $request)
    {
        $id_paciente  = $request->id;
        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('h.fecha_atencion', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.id_doctor1', 'h.fecha_atencion', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.id as id_doctor','r.id as idreceta')
            ->get();
        $historial = array();
        $empresa= DB::table('empresa')->where('apps',1)->first();
        foreach ($hist_recetas as $h) {
            $dia = Date('N', strtotime($h->fecha_atencion));
            $mes = Date('n', strtotime($h->fecha_atencion));
            if ($dia == '1') {
                $dia = 'Lunes';
            } elseif ($dia == '2') {
                $dia = 'Martes';
            } elseif ($dia == '3') {
                $dia = 'Miércoles';
            } elseif ($dia == '4') {
                $dia = 'Jueves';
            } elseif ($dia == '5') {
                $dia = 'Viernes';
            } elseif ($dia == '6') {
                $dia = 'Sábado';
            } elseif ($dia == '7') {
                $dia = 'Domingo';
            }
            if ($mes == '1') {
                $mes = 'Enero';
            } elseif ($mes == '2') {
                $mes = 'Febrero';
            } elseif ($mes == '3') {
                $mes = 'Marzo';
            } elseif ($mes == '4') {
                $mes = 'Abril';
            } elseif ($mes == '5') {
                $mes = 'Mayo';
            } elseif ($mes == '6') {
                $mes = 'Junio';
            } elseif ($mes == '7') {
                $mes = 'Julio';
            } elseif ($mes == '8') {
                $mes = 'Agosto';
            } elseif ($mes == '9') {
                $mes = 'Septiembre';
            } elseif ($mes == '10') {
                $mes = 'Octubre';
            } elseif ($mes == '11') {
                $mes = 'Noviembre';
            } elseif ($mes == '12') {
                $mes = 'Diciembre';
            }
            $a['fecha']      = date('d/m/Y', strtotime(substr($h->fecha_atencion, 0, 10)));
            $a['fechaTexto'] = $dia . ' ' . substr($h->fecha_atencion, 8, 2) . ' de ' . $mes . ' del ' . substr($h->fecha_atencion, 0, 4);
            $a['dr']         = $h->dnombre1 . ' ' . $h->dapellido1;
            $a['rp'] = strip_tags($h->rp);
            $a['prescripcion']= strip_tags($h->prescripcion);
            $a['url']        = $empresa->url_apps.'/api/recetaPdf/' . $h->id . '/2';
            array_push($historial, $a);
        }
        return response()->json($historial);
    }
    public function pdf(Request $request)
    {
        return view('sinlogin.iframe2pdf', ['id' => $request['id']]);
    }
    public function pdfReceta($id, $tipo)
    {
        
        $empresa= DB::table('empresa')->where('apps',1)->first();
        $receta   = hc_receta::find($id);
        $historia = Historiaclinica::find($receta->id_hc);
        $paciente = paciente::find($historia->id_paciente);
        //return $historia;
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
        //return view('hc_admision/receta/menbretada', ['paciente' => $paciente,'edad' => $edad, 'historia' => $historia,  'receta' => $receta,]);
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::where('estado', 1)->get();
        $cie10     = Hc_Cie10::where('hcid', $receta->id_hc)->get();
        $firma     = null;
        $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
        $logo_emp = Empresa::find($empresa->id);
        $principioActivo = "";
        $empresa= DB::table('empresa')->first();
        $id_empresa= $empresa->id;
        if (count($pacienteAlergia) > 0) {
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
            $view = \View::make('hc_admision.receta.menbretada', compact('receta','id_empresa', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'logo_emp'))->render();
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete', compact('receta','id_empresa', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'principioActivo'))->render();
        }
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        //return $pdf->download($historia->id_paciente.'_Receta_'.$id.'.pdf');

        return $pdf->download('receta-' . $id . '.pdf');
    }
    public function notificacionCharlas(Request $request)
    {
        $charlas = Apps_Charlas::where('estado', 1)->whereDate('fecha', date('Y-m-d'))->where('id_empresa', '0992704152001')->get();
        $agendas = Apps_Agenda::where('estado', 1)->whereDate('fecha', date('Y-m-d'))->where('id_usuariocrea', $request->id)->get();
        $aps     = [];
        foreach ($charlas as $charlas) {
            $users             = User::find($charlas->id_doctor);
            $ap['id']          = $charlas->id;
            $ap['fecha']       = date('d/m/Y H:i:s', strtotime($charlas->fecha));
            $ap['descripcion'] = $charlas->descripcion;
            $ap['type']        = "charlas";
            $ap['doctor']      = $users->nombre1 . ' ' . $users->apellido1;
            array_push($aps, $ap);
        }
        foreach ($agendas as $a) {
            //$ag= agenda::find($a->id_agenda);
            $users             = User::find($a->id_doctor);
            $ap['id']          = $a->id;
            $ap['fecha']       = date('d/m/Y H:i:s', strtotime($a->fecha));
            $ap['descripcion'] = 'Consulta Medica';
            $ap['type']        = "agend";
            $ap['doctor']      = $users->nombre1 . ' ' . $users->apellido1;
            array_push($aps, $ap);
        }
        return response()->json($aps);
    }
    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        //dd($appId);
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        //dd($context, $url);
        return file_get_contents($url, false, $context);
    }

    public function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function getDocumentType($document)
    {
        if (strlen($document) == 10) {
            return '05'; //CEDULA
        } else if (strlen($document) == 13) {
            return '04'; //RUC
        } else {
            return '06'; //PASAPORTE
        }
    }

    public function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }
    public function returnUrl(Request $request)
    {
        echo "<html><head></head><body><script type='text/javascript'>window.ReactNativeWebView?.postMessage('irAtras')</script></body></html>";
    }
    public function cancelUrl(Request $request)
    {
        echo "<html><head></head><body><script type='text/javascript'>window.ReactNativeWebView?.postMessage('irAtras')</script></body></html>";
    }
    public function createOrderGetPayment(Request $request)
    {

        $global = $request['city'];
        if (is_null($global)) {
            $global = '1'; //Guayaquil
        }
        $json_request = json_decode($request->getContent(), true);

            /*
        {
            "id": 1,
            "nombre": "Guayaquil"
        },
        {
            "id": 2,
            "nombre": "Quito"
        },
        {
            "id": 3,
            "nombre": "Santo Domingo"
        },
        {
            "id": 4,
            "nombre": "Manta"
        },
        {
            "id": 5,
            "nombre": "Milagro"
        },
        {
            "id": 6,
            "nombre": "Portoviejo"
        } */
        $arrayCiudades= [
            "3"=>"2390058384001",
            "4"=>"1391927177001",
            "2" => "1793135579001",
            "5" => "0993375469001",
        ];
        $ciudadBypass=$arrayCiudades[$json_request['city']];
            if (!isset($json_request['id_doctor'])) {
                $json_request['id_doctor'] = 1316262193;
            }
        $empresa= Empresa::find($ciudadBypass);
        $json_request['id_empresa']=$ciudadBypass;
            $id_doctor    = $json_request['id_doctor'];
            $_pasarela_pagos_detalle  = "";
            $_pasarela_pagos_subtotal = 0;
            $_pasarela_pagos_iva      = 0;
            $_pasarela_pagos_total    = 0;

            $tipo_pago            = $json_request['tipopago']; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
            $membresia_id         = $json_request['membresia_id']; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
            $membresia_detalle_id = $json_request['membresia_detalle_id'];
            $ip_cliente           = $_SERVER["REMOTE_ADDR"];

            $cedula_usuario = $json_request['id_usuario'];
            $email_usuario  = $json_request['email_usuario'];
            if ($membresia_id == 0) {
                $membresia_id = null;
            }

            //PACIENTE
            $cedula_paciente  = $json_request['id_paciente'];
            $celular_paciente = $json_request['celular_paciente'];

            //FACTURA
            $telefono_factura = $json_request['telefono_factura'];
            $email_factura    = $json_request['email_factura'];

            //VARIABLES
        $RUC_LABS          = $ciudadBypass; //0916293723001
            $ref_id            = "";
            $id_seguro         = '1';
            $id_protocolo      = null;
            $id_nivel          = null;
            $total             = $json_request['total'];
            $contador          = 0;
            $usuario_mail      = null;
            $paciente          = null;
            $user              = null;
            $id_orden          = 0;
            $membresia         = null;
            $descuento_detalle = 0;


            // VALIDACIONES DE USUARIO Y PACIENTE
            $pacientevalidate= Paciente::find($cedula_paciente);
            $usuvalidate= User::find($cedula_paciente);
            if(is_null($usuvalidate)){
                User::create([
                    'id'               => $cedula_paciente,
                    'nombre1'          => strtoupper($json_request['nombre1_paciente']),
                    'nombre2'          => strtoupper($json_request['nombre2_paciente']),
                    'apellido1'        => strtoupper($json_request['apellido1_paciente']),
                    'apellido2'        => strtoupper($json_request['apellido2_paciente']),
                    'direccion'        => '.',
                    'email'            => $cedula_paciente.'@mail.com',
                'id_tipo_usuario'  => '2',
                    'tipo_documento'   => '1',
                    'id_pais'          => '1',
                    'password'         => bcrypt($cedula_paciente),
                    'imagen_url'       => 'avatar.jpg',
                    'fecha_nacimiento' => $json_request['fecha_nacimiento_paciente'],
                    'telefono1'        => $json_request['telefono_factura'],
                    'telefono2'        => $json_request['telefono_factura'],
                ]);
            }
            if(is_null($pacientevalidate)){
                Paciente::create([
                    'id'               => $cedula_paciente,
                    'nombre1'          => strtoupper($json_request['nombre1_paciente']),
                    'nombre2'          => strtoupper($json_request['nombre2_paciente']),
                    'apellido1'        => strtoupper($json_request['apellido1_paciente']),
                    'apellido2'        => strtoupper($json_request['apellido2_paciente']),
                    'tipo_documento'   => '1',
                    'parentesco'       => 'Principal',
                    'id_usuario'       => $cedula_paciente,
                    'id_pais'          => '1',
                    'direccion'        => '.',
                    'id_seguro'        => $id_seguro,
                    'imagen_url'       => 'avatar.jpg',
                    'fecha_nacimiento' => $json_request['fecha_nacimiento_paciente'],
                    'telefono1'        => $json_request['telefono_factura'],
                    'telefono2'        => $json_request['telefono_factura'],
                ]);
            }
            $paciente       = Paciente::find($cedula_paciente);
            if ($tipo_pago == "ORD") {

            } else if ($tipo_pago == "MEM") {
                //Es un pago de membresia
                foreach ($json_request['detalles'] as $pexamen) {
                    $valor = $pexamen['total'];
                    //$total += $valor;
                }

                $membresia = Membresia::find($json_request['membresia']);

                if ($membresia != null) {
                    $data_array = array(
                        "tipo"                    => "MEM-VEN", //tipo de de orden de venta
                        "fecha"                   => date('Y-m-d h:i:s'), //fecha de orden
                        "id_empresa"              => $RUC_LABS, //empresa
                        "divisas"                 => "1", //cualquiera
                        "nombre_cliente"          => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], //nombre del cliente
                        "tipo_consulta"           => "1", //esto clavado
                        "identificacion_cliente"  => $cedula_paciente, // ci de cliente
                        "direccion_cliente"       => $paciente['direccion_paciente'], //direccion del cliente
                        "telefono_cliente"        => $paciente['celular_paciente'], // telefono de cliente
                        "mail_cliente"            => $email_usuario, //mail del cliente
                        "orden_venta"             => "0", //idde referencia
                        "identificacion_paciente" => $cedula_paciente, // ci de paciente
                        "nombre_paciente"         => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], // nonbre de paciente
                        "id_seguro"               => "1", //de la tabla seguros,
                        "subtotal_01"             => $total,
                        "subtotal_121"            => $total,
                        "descuento1"              => "0.00",
                        "tarifa_iva1"             => "0.00",
                        "base"                    => $total,
                        "totalc"                  => $total,
                        "valor_contable"          => $total,
                        "details"                 => array(
                            array(
                                "codigo"     => 1,
                                "nombre"     => $membresia['nombre'], ///<<<<<<<---------
                                "cantidad"   => "1",
                                "precio"     => $total,
                                "extendido"  => "0.00",
                                "iva"        => "1",
                                "descpor"    => "0",
                                "descuento"  => "0",
                                "copago"     => "0",
                                "detalle"    => $membresia['nombre'],
                                "precioneto" => $total,

                            ),
                        ),
                        "id_usuario"              => $paciente['id'],
                    );
                    $response = Contable::build_data($data_array);
                    $id_orden = $response['ven_orden'];
                    //ARMA EL REF_ID
                    $ref_id = "MEM" . $id_orden; //pivot para p&f

                }
            }

            //ACTUALIZA TOTALES
            $total             = round($total, 2);
            $recargo_p         = 0;
            $descuento_detalle = round($descuento_detalle, 2);
            $subtotal_pagar    = $total - $descuento_detalle;
            $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor     = round($recargo_valor, 2);
            $valor_total       = $subtotal_pagar + $recargo_valor;
            $valor_total       = round($valor_total, 2);
            $id_apps           = null;
            $id_mem            = null;
            if ($tipo_pago == "ORD") {
                $stringPlain = 'siam_2020_' . random_int(1, 1000);
                $encodeurl   = base64_encode($stringPlain);
                $url         = 'https://mdconsgroup.ec:8000/?cid=' . $encodeurl;
                if ($json_request['mod'] == "PRESENCIAL") {
                    $url = null;
                }
                $id_agenda = $this->agendPatient($json_request);
                $id_apps = Apps_Agenda::insertGetId([
                    //falta agenda esto va despues de todo
                    /* 'id_agenda'=>$id_agenda, */
                    'fecha'          => $json_request['dateini'],
                    'estado'         => 1,
                    'url'            => $url,
                    'id_agenda'      => $id_agenda,
                    'total'          => $json_request['total'],
                    'id_doctor'      => $json_request['id_doctor'],
                    'id_usuariocrea' => $json_request['id_usuario'],
                    'tipo'           => $json_request['mod'],
                    'online'         => 0,
                ]);
                $ref_id = "ORD" . $id_apps;
            }

            //LOG DEL SISTEMA DE QUIEN CRE LA ORDEN
            Log_usuario::create([
                'id_usuario'  => '1234517896',
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GENERA COTIZACION MOVIL",
                'dato_ant1'   => $cedula_paciente,
                'dato1'       => strtoupper($paciente['nombre1_paciente'] . " " . $paciente['nombre2_paciente'] . " " . $paciente['apellido1_paciente'] . " " . $paciente['apellido2_paciente']),
            ]);

            /*
            INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
             */
        $PAGOSYFACTURAS_APPID     = $empresa['appid'];
        $PAGOSYFACTURAS_APPSECRET = $empresa['appsecret'];
            $_pasarela_pagos_subtotal = round($valor_total, 2, PHP_ROUND_HALF_UP);
            $_pasarela_pagos_iva      = 0;
            $_pasarela_pagos_total    = round($valor_total, 2, PHP_ROUND_HALF_UP);
            $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
            $pyf_details              = array();

            if ($tipo_pago == "ORD") {
                array_push($pyf_details, array(
                    "sku"      => '' . $id_orden . '',
                    "name"     => 'Consultas médicas',
                    "qty"      => 1,
                    "price"    => $_pasarela_pagos_subtotal,
                    "tax"      => 0.00,
                    "discount" => 0.00, //falta revisar
                    "total"    => $_pasarela_pagos_subtotal,
                ));
            } else if ($tipo_pago == "MEM") {
                array_push($pyf_details, array(
                    "sku"      => '' . $id_orden . '',
                    "name"     => 'Servicios Médicos (MEMBRESIA PLAN PREMIUM)#' . $id_orden,
                    "qty"      => 1,
                    "price"    => $_pasarela_pagos_subtotal,
                    "tax"      => 0.00,
                    "discount" => 0.00, //falta revisar
                    "total"    => $_pasarela_pagos_subtotal,
                ));
            }

            if (strlen($telefono_factura) < 10) {
                $telefono_factura = '0900000001';
            }

            //CLEANING NAMES AND SURENAMES
            $nombres_factura            = '';
            $apellidos_factura          = '';
            $nombres_apellidos_facturas = explode(" ", $json_request['nombre_factura']);
            $nombres_factura            = $nombres_apellidos_facturas[0];
            for ($i = 1; $i < count($nombres_apellidos_facturas); $i++) {
                $apellidos_factura .= $nombres_apellidos_facturas[$i];
            }
            if (count($nombres_apellidos_facturas) == 4) {
                $nombres_factura   = $nombres_apellidos_facturas[0] . ' ' . $nombres_apellidos_facturas[1];
                $apellidos_factura = $nombres_apellidos_facturas[2] . ' ' . $nombres_apellidos_facturas[3];
            }
            $data_array = array(
                "company"        => $RUC_LABS,
                "person"         => array(
                    "document"     => $json_request['cedula_factura'],
                    "documentType" => $this->getDocumentType($json_request['cedula_factura']),
                    //"name"         => $this->cleanNames(strtoupper($request['nombre1']) . ' ' . strtoupper($request['nombre2'])),
                    //"surname"      => $this->cleanNames(strtoupper($request['apellido1']) . ' ' . strtoupper($request['apellido2'])),
                    "name"         => $this->cleanNames(strtoupper($paciente['nombre1'])),
                    "surname"      => $this->cleanNames(strtoupper($paciente['apellido1'])),
                    "email"        => $email_factura,
                    "mobile"       => $telefono_factura,
                ),
                "paymentRequest" => array(
                    "orderId"     => '' . $ref_id . '',
                "description" => "Compra en linea", //PONER EN CONFIGURACION
                    "items"       => array(
                        "item" => $pyf_details, //pending
                    ),
                    "amount"      => array(

                        "taxes"    => array(
                            array(
                                "kind"   => "Iva",
                                "amount" => 0.00,
                                "base"   => $_pasarela_pagos_subtotal,
                            ),
                        ),
                        "currency" => "USD",
                        "total"    => $_pasarela_pagos_total,
                    ),
                ),
                //"returnUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=" . $ref_id, //URL DE RETORNO
                //"cancelUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=" . $ref_id, //URL DE CANCELACION
                "returnUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/returnUrl?orderid=" . $ref_id, //URL DE RETORNO, orderid not used in link for now
                "cancelUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/cancelUrl?orderid=" . $ref_id, //URL DE CANCELACION, orderid not used in link for now
                "userAgent"      => "labs_ec/1",
            );
        //dd($data_array,$PAGOSYFACTURAS_APPID,$PAGOSYFACTURAS_APPSECRET);
            $requestId = '';
            $manage    = json_encode($data_array);
            $manage2   = json_encode($request->getContent());
            Log_Api::create([
                'id_usuario'  => '131626193',
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ENVIO API",
                'url'         => '13',
                'dato1'       => $manage,
                'dato2'       => $manage2,
            ]);
            $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
            $response  = json_decode($make_call, true);
        //dd($response);
            if ($response['status'] != null) {
                $requestId = $response['requestId'];
                if ($response['status']['status'] == 'success') {
                    $pyf_checkout_url = $response['processUrl'];
                } else {
                    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
                }
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
            }
            /*
            RESPUESTA P&F
            {
            "status": {
            "status": "success",
            "message": "payment request successfully created",
            "reason": "",
            "date": "2021-09-09T14:51:17-05:00"
            },
            "requestId": "7501",
            "processUrl": "https://vpos.accroachcode.com/sandbox/40GesdOhDl6Dg23mavMyHM"
            }
             */
            //RETURNS JSON RESPONSE
            //return ['estado' => 'ok', 'usuario' => $xusuario->id, 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
            $apsx             = Apps_Agenda::find($id_apps);
            if ($apsx != null) {
                $apsx->request_id = $requestId;
                $apsx->json_send  = $manage;
                $apsx->save();
                return response()->json([
                    'estado'          => 'ok',
                    'usuario'         => $request['id_usuario'],
                    'ref_id'          => $ref_id,
                    'total'           => $total,
                    'url_vpos'        => $pyf_checkout_url,
                    'request_id'      => $requestId,
                    'paciente_id'     => $paciente['id'],
                    'paciente_id_req' => $cedula_paciente,
                ]);
            }
            if ($id_mem != null) {
                $memx = UserMembresia::find($id_mem);
                $memx->requestId = $requestId;
                $memx->save();
            }
            return response()->json([
                'estado'          => 'ok',
                'usuario'         => $request['id_usuario'],
                'ref_id'          => $ref_id,
                'total'           => $total,
                'url_vpos'        => $pyf_checkout_url,
                'request_id'      => $requestId,
                'paciente_id'     => $paciente['id'],
                'paciente_id_req' => $cedula_paciente,
            ]);

    }
    public function asientos($json_request, $RUC_LABS, $ip_cliente)
    {
        $input_cabecera = [
            'fecha_asiento'   => date('Y-m-d'),
            'fact_numero'     => "obs",
            'id_empresa'      => $RUC_LABS,
            'observacion'     => "observar",
            'valor'           => $json_request['total'],
            'id_usuariocrea'  => $json_request['id_usuario'],
            'id_usuariomod'   => $json_request['id_usuario'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        $plan_cuentas        = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
        Ct_Asientos_Detalle::create([

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => '1.01.02.05.01',
            'descripcion'         => $plan_cuentas->nombre,
            'fecha'               => date('Y-m-d'),
            'debe'                => $json_request['total'],
            'haber'               => '0',
            'id_usuariocrea'      => $json_request['id_usuario'],
            'id_usuariomod'       => $json_request['id_usuario'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);
        $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
        Ct_Asientos_Detalle::create([

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => '4.1.01.02',
            'descripcion'         => $plan_cuentas->nombre,
            'fecha'               => date('Y-m-d'),
            'debe'                => '0',
            'haber'               => $json_request['total'],
            'id_usuariocrea'      => $json_request['id_usuario'],
            'id_usuariomod'       => $json_request['id_usuario'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);
        /*         $input_cabecera = [
        'observacion'     => "PAGO CONSULTA ICED",
        'fecha_asiento'   => date('Y-m-d'),
        'fact_numero'     => "observar",
        'valor'           => $json_request['total'],
        'id_empresa'      => $RUC_LABS,
        'estado'          => '1',
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $json_request['id_usuario'],
        'id_usuariomod'   => $json_request['id_usuario'],
        ];
        $idcabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $RUC_LABS)->latest()->first();
        //dd($max_id);
        $secuencia = intval($max_id->secuencia);
        $numero_factura="";
        //dd($max_id->secuencia);
        if (strlen($secuencia) < 10) {
        $nu             = $secuencia + 1;
        $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
        }
        $input_comprobante = [
        'observaciones'       => "PAGO CONSULTA ICED",
        'estado'              => '1',
        'id_asiento_cabecera' => $idcabecera,
        'fecha'               => date('Y-m-d'),
        'sc'                  => $numero_factura,
        'secuencia'           => $numero_factura,
        'tipo'                => '0',
        'divisas'             => '1',
        'id_empresa'          => $RUC_LABS,
        'total_ingreso'       => $json_request['total'],
        'deficit_ingreso'     =>  $json_request['total'],
        'id_cliente'          => $json_request['id_usuario'],
        'autollenar'          => 'PAGO EN LINEA IECED',
        'id_usuariocrea'      => $json_request['id_usuario'],
        'id_usuariomod'       => $json_request['id_usuario'],
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        ];
        $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
        $desc_cuenta         = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
        Ct_Asientos_Detalle::create([
        'id_asiento_cabecera' => $idcabecera,
        'id_plan_cuenta'      => '1.01.02.05.01',
        'descripcion'         => $desc_cuenta->nombre,
        'fecha'               => date('Y-m-d'),
        'haber'               => $json_request['total'],
        'debe'                => '0',
        'estado'              => '1',
        'id_usuariocrea'      => $json_request['id_usuario'],
        'id_usuariomod'       => $json_request['id_usuario'],
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        ]);
        Ct_Detalle_Pago_Ingreso::create([
        'id_comprobante'  => $id_comprobante,
        'fecha'           => date('Y-m-d'),
        'numero'          => $numero_factura,
        'id_tipo_tarjeta' => '',
        'id_tipo'         => $request['tipo' . $i],
        'total'           => $request['valor' . $i],
        'cuenta'          => $request['cuenta' . $i],
        'girador'         => $request['girador' . $i],
        'estado'          => '1',
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]); */
        return $id_asiento_cabecera;
    }
    public function getPaymentInformation(Request $request)
    {
        //GETS JSON BODY
        $json_request = json_decode($request->getContent(), true);
        $requestId    = $json_request['requestId'];
        $arrayCiudades= [
            "3"=>"2390058384001",
            "4"=>"1391927177001",
            "2"=>"1793135579001"
        ];
        $ciudadBypass=$arrayCiudades[$json_request['city']];
        $empresa= Empresa::find($ciudadBypass);
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = $empresa['appid'];
        $PAGOSYFACTURAS_APPSECRET = $empresa['appsecret'];
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);

        return $response; //en el status verificar que status.status=="APPROVED"
    }
    public function getMembresiaPayment(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        $requestId    = $json_request['requestId'];
        $request['requestId'] = null;
        $request['requestId'] = $requestId;
        $response             = $this->getPaymentInformationRequest($request);
        //dd($request);
        if ($response['status']['status'] == 'APPROVED') {
            $usuario = UserMembresia::where('estado', 0)->where('requestId', $requestId)->first();
            if (!is_null($usuario)) {
                $usuario->estado = 1;
                $usuario->save();
            }
            return response()->json('ok');
        }
        return response()->json('no');
    }
    public function getAgendaPayment(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        $requestId    = $json_request['requestId'];
        $arrayCiudades= [
            "3"=>"2390058384001",
            "4"=>"1391927177001",
            "2"=>"1793135579001"
        ];
        $ciudadBypass=$arrayCiudades[$json_request['city']];
        $empresa= Empresa::find($ciudadBypass);
        $request['requestId'] = null;
        $request['requestId'] = $requestId;
        $request['empresa']= $empresa['id'];
        $response             = $this->getPaymentInformationRequest($request);
        //dd($request);
        if ($response['status']['status'] == 'APPROVED') {

            $apps = Apps_Agenda::where("request_id", $requestId)->where('online', 0)->first();
            $apps->online = 1;
            $agenda = Agenda::find($apps->id_agenda);
            $apps_agenda = Apps_Agenda::where('id_agenda', $agenda->id)->first();
            $paciente = User::find($apps_agenda->id_usuariocrea);
            $doctor = User::find($apps_agenda->id_doctor);
            $fecha = $agenda->fechaini;
            $asunto = "Cita Médica Confirmada";
            $titulo = "Agendamiento de Cita Médica";
            $url = $apps_agenda->url;
            $agenda->estado = 1;
            $agenda->estado_cita=0;
            $agenda->proc_consul=0;
            $agenda->save();
            $apps->save();
            if ($apps_agenda->tipo == 'PRESENCIAL') {
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


            return response()->json('ok');
        }
        return response()->json(['status'=>$response['status']['status']]);
    }
    public function getAgendaPayment2($request)
    {
        
        $requestId    = $request['requestId'];
        $response             = $this->getPaymentInformationRequest($request);
        //dd($request);
        if ($response['status']['status'] == 'APPROVED') {

            $apps = Apps_Agenda::where("request_id", $requestId)->where('online', 0)->first();
            $apps->online = 1;
            $agenda = Agenda::find($apps->id_agenda);
            $apps_agenda = Apps_Agenda::where('id_agenda', $agenda->id)->first();
            $paciente = User::find($apps_agenda->id_usuariocrea);
            $doctor = User::find($apps_agenda->id_doctor);
            $fecha = $agenda->fechaini;
            $asunto = "Cita Médica Confirmada";
            $titulo = "Agendamiento de Cita Médica";
            $url = $apps_agenda->url;
            $agenda->estado = 1;
            $agenda->estado_cita=0;
            $agenda->proc_consul=0;
            $agenda->save();
            $apps->save();
            if ($apps_agenda->tipo == 'PRESENCIAL') {
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


            return response()->json('ok');
        }
        return response()->json('no');
    }
    public function registerUser(Request $request)
    {
        $global = $request['city'];
        if (is_null($global)) {
            $global = '1';
        }
        if ($global == '1') {
            $token = $request['token'];
            if ($token != '8c0a00ec19933215dc29225e645ea714') {
                return response()->json([
                    'result'    => '2',
                    'idUsuario' => '',
                ]);
            }
            $nombre           = $request['names'];
            $idusuario        = $request['id'];
            $apellido         = $request['surnames'];
            $email            = $request['email'];
            $telefono         = $request['telefono'];
            $seguros          = $request['seguros'];
            $fecha_nacimiento = $request['date'];
            $direccion        = $request['direccion'];
            if (!is_null($nombre) && !is_null($apellido) && !is_null($email) && !is_null($request['pass']) && !is_null($idusuario) && !is_null($fecha_nacimiento)) {
                $verificar  = User::where('email', $email)->first();
                $verificar2 = User::find($idusuario);
                if (is_null($seguros)) {
                    $seguros = "1";
                }
                if (!is_null($verificar) && $verificar != '[]' || !is_null($verificar2) && $verificar2 != '[]') {
                    return response()->json([
                        'result'    => '3', //email ya se encuentra registrado
                        'idUsuario' => '',
                        'dataLog'   => 'Ya existe usuario',
                    ]);
                } else {
                    list($nombre1, $nombre2)     = array_pad(explode(' ', $nombre), 30, null);
                    list($apellido1, $apellido2) = array_pad(explode(' ', $apellido), 30, null);

                    if (is_null($apellido2)) {
                        $apellido2 = "(N/A)";
                    }
                    if (is_null($nombre2)) {
                        $nombre2 = "(N/A)";
                    }
                    //'imagen_url'       => 'img000169423.png',
                    User::create([
                        'id'               => $idusuario,
                        'email'            => $email,
                        'nombre1'          => strtoupper($nombre1),
                        'id_tipo_usuario'  => '2',
                        'nombre2'          => strtoupper($nombre2),
                        'apellido1'        => strtoupper($apellido1),
                        'apellido2'        => strtoupper($apellido2),
                        'estado'           => '1',
                        'imagen_url'       => 'avatar.jpg',
                        'telefono1'        => $telefono,
                        'telefono2'        => $telefono,
                        'direccion'        => $direccion,
                        'tipo_documento'   => '1',
                        'id_pais'          => '1', //pendiente revisar
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'password'         => bcrypt($request['pass']),
                    ]);
                    Paciente::create([
                        'id'               => $idusuario,
                        'nombre1'          => strtoupper($nombre1),
                        'nombre2'          => strtoupper($nombre2),
                        'apellido1'        => strtoupper($apellido1),
                        'apellido2'        => strtoupper($apellido2),
                        'tipo_documento'   => '1',
                        'parentesco'       => 'Principal',
                        'id_usuario'       => $idusuario,
                        'id_pais'          => '1',
                        'direccion'        => $direccion,
                        'id_seguro'        => $seguros,
                        'imagen_url'       => 'avatar.jpg',
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'telefono1'        => $telefono,
                        'telefono2'        => $telefono,
                    ]);
                    $validate  = $this->findOtherBD($request);
                    $responses = "";
                    if ($validate->getData()->result == 1) {
                        $response = "ya existe en portoviejo";
                    } else {
                        if (is_null($request['seguros'])) {
                            $request['seguros'] = '1';
                        }
                        $response = "se crea en portoviejo";
                        $create   = $this->createOtherBD($request);
                        //dd($create);
                        if ($create->getData()->result == 1) {
                        } else {
                            $response = $create->getData();
                        }
                    }
                    return response()->json([
                        'result'     => '1',
                        'idUsuario'  => $idusuario,
                        'idPaciente' => $idusuario,
                        'response'   => $response,
                    ]);
                }
            } else {
                return response()->json([
                    'result'    => '4', //vacios parametros
                    'idUsuario' => '',
                    'dataLog'   => 'Vacios parametros',
                ]);
            }
        } else {
            $post = [
                'token'     => '8c0a00ec19933215dc29225e645ea714',
                'id'        => $request['id'],
                'names'     => $request['names'],
                'surnames'  => $request['surnames'],
                'email'     => $request['email'],
                'telefono'  => $request['telefono'],
                'seguros'   => $request['seguros'],
                'date'      => $request['date'],
                'direccion' => $request['direccion'],
                'pass'      => $request['pass'],
            ];
            $ch = curl_init('http://siaam.ec/sis_medico/public/api/registeruser');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);
            return response()->json(json_decode($response));
        }
    }
    public function validAgenda(Request $request)
    {
        //first validation
        $json_request = json_decode($request->getContent(), true);
        //dd($request->getContent());
        /*        Log_Apps::create([
        'observacion'=>$request,
        ]); */
        $validar_horario = $this->valida_horarioxdoctor_dia($json_request);
        $validar_maximos = $this->validateMax1($json_request);
        $validar         = $this->validateInput3($json_request);
        return response()->json(['status' => 1, 'horario' => 0, 'maximos' => 0, 'validar' => 0, 'msj' => 'When is zero, dont pass the function']); // i change this because i dont wanna buy yet 
    }
    public function saber_dia($fecha)
    {

        $dias = array('0', '1', '2', '3', '4', '5', '6', '7'); //12/1/2018

        $nombre_dia = $dias[date('N', strtotime($fecha))];

        return $nombre_dia;
    }
    public function valida_horarioxdoctor_dia($request)
    {

        $id_doctor = $request['id_doctor1'];
        $fechaini  = $request['inicio'];
        $fechafin  = $request['fin'];

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s', strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s', strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s', strtotime($fechaini));
        $final  = date('Y-m-d H:i:s', strtotime($fechafin));

        $cantidad_ini = Horario_Doctor::where('id_doctor', $id_doctor)->where('ndia', $ndiaini)->where('estado', '1')->where('hora_ini', '<=', $horaini)->where('hora_fin', '>=', $horaini)->count();

        $cantidad_fin = Horario_Doctor::where('id_doctor', $id_doctor)->where('ndia', $ndiafin)->where('estado', '1')->where('hora_ini', '<=', $horafin)->where('hora_fin', '>=', $horafin)->count();

        if ($cantidad_ini == 0) {
            $cantidad_ini = Excepcion_Horario::where('id_doctor1', $id_doctor)->where('inicio', '<=', $inicio)->where('fin', '>=', $inicio)->count();
        }

        if ($cantidad_fin == 0) {
            $cantidad_fin = Excepcion_Horario::where('id_doctor1', $id_doctor)->where('inicio', '<=', $final)->where('fin', '>=', $final)->count();
        }
        $cantidad = $cantidad_ini + $cantidad_fin;

        $reglas = [
            'inicio' => 'comparamayor:0,' . $cantidad_ini,
            'fin'    => 'comparamayor:0,' . $cantidad_fin,
        ];
        $mensajes = [
            'inicio.comparamayor' => 'fecha de inicio esta fuera del horario laborable del Doctor',
            'fin.comparamayor'    => 'fecha de fin esta fuera del horario laborable del Doctor',
        ];

        //$this->validate($request,$reglas,$mensajes);
        return $cantidad;
    }
    private function validateMax1($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);
        //return  9/10/2018 se habilita bloqueo

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
        //dd($cantidad);
        $doctor = User::find($request['id_doctor1']);
        if ($doctor->max_consulta >= $cantidad) {
            return 1;
        } else {
            return 0;
        }
        /*  if ($request['proc_consul'] == 0) {
        $rules = [
        'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
        ];
        } else if ($request['proc_consul'] == 1) {
        $rules = [
        'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
        ];
        }
        $mensajes = [
        'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
        'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ]; */
        return $cantidad;
        //$this->validate($request, $rules, $mensajes);

    }
    private function validateInput3($request)
    {
        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        //$this->validate($request, $rules, $mensajes);
        if ($cant_agenda > 0) {
            return 1;
        } else {
            return 0;
        }
        return $cant_agenda;
    }
    public function storeSolicitudes(Request $json_request)
    {
        //$json_request = json_decode($request->getContent(), true);
        $validar = Apps_Solicitudes::where('id_usuariocrea', $json_request['id'])->whereDate('created_at', date('Y-m-d'))->count();
        if ($validar >= 4) {
            return response()->json(['status' => 0, 'msj' => 'Muchos intentos de solicitud diaria.']);
        }
        $id_solicitud = null;

        if ($json_request['filename'] != 'no') {
            $extension = 'png';
            $imageName = date('YmdHis') . $json_request['id'];
            $user      = User::find($json_request['id']);

            /* \File::put(storage_path(). '/' . $imageName, base64_decode($image)); */
            $r1           = Storage::disk('public')->put($imageName . '.' . $extension, base64_decode($json_request['filename']));
            $id_solicitud = Apps_Solicitudes::create([
                'id_usuariocrea'   => $json_request['id'],
                'observaciones'    => $json_request['observaciones'],
                'telefono1'        => $json_request['telefono1'],
                'id_empresa'       => '0992704152001',
                'id_procedimiento' => $json_request['id_procedures'],
                'telefono2'        => $json_request['telefono2'],
                'url'              => $imageName . '.' . $extension,
                'tipo'             => $json_request['tipo'],
            ]);
        } else {
            $id_solicitud = Apps_Solicitudes::create([
                'id_usuariocrea'   => $json_request['id'],
                'observaciones'    => $json_request['observaciones'],
                'telefono1'        => $json_request['telefono1'],
                'id_empresa'       => '0992704152001',
                'id_procedimiento' => $json_request['id_procedures'],
                'telefono2'        => $json_request['telefono2'],
                'tipo'             => $json_request['tipo'],
            ]);
        }

        return response()->json(['id' => $id_solicitud, 'status' => 1, 'msj' => 'ok.', 'json_er' => 'ok']);
    }
    public function procedures(Request $request)
    {
        $procedimiento = Procedimiento::where('estado', 1)->select('id', 'nombre')->get();
        return response()->json(['status' => 1, 'msj' => 'ok', 'procedures' => $procedimiento]);
    }
    public function store_agenda(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        /*  $id_agenda= agenda::insertGetId([
        'fecha'=>$json_request['fecha'],
        ''
        ]); */
        $stringPlain = 'siam_2020_' . date('YdmHis') . random_int(1, 3000);
        $encodeurl   = base64_encode($stringPlain);
        $url         = 'https://mdconsgroup.ec:8000/?cid=' . $encodeurl;
        $id_apps     = Apps_Agenda::insertGetId([
            //falta agenda esto va despues de todo
            'fecha'          => $json_request['inicio'],
            'estado'         => 1,
            'url'            => $url,
            'id_doctor'      => $json_request['id_doctor'],
            'id_usuariocrea' => $json_request['id'],
            'tipo'           => $json_request['tipo'],
        ]);
        return response()->json(['msj' => 'ok', 'status' => 1, 'url' => $url, 'id_apps']);
    }
    public function list_online(Request $request)
    {
        $apps = Apps_Agenda::join('agenda as a','a.id','apps_agenda.id_agenda')->where('apps_agenda.estado', 1)
        ->where('apps_agenda.id_usuariocrea', $request->id)->where('apps_agenda.fecha', '>=', date('Y-m-d'))
        ->select('apps_agenda.url', 'apps_agenda.id as id', 'a.fechaini as fecha', 'apps_agenda.id_usuariocrea', 'apps_agenda.id_doctor','apps_agenda.online','a.estado as estado','apps_agenda.tipo')->get();
        $ko   = [];

        foreach ($apps as $p) {
            if($p->request_id!=null){
                if($p->online==0){
                    $json_request['requestId']=$p->request_id;
                    $this->getAgendaPayment2($json_request);
                }
            }
            if($p->estado==1){
                $user1 = User::find($p->id_usuariocrea);
                $user2 = User::find($p->id_doctor);
                $photo = asset('') . '../storage/app/avatars/' . $user2->imagen_url;
                if (is_null($user2->imagen_url) || $user2->imagen_url == ' ') {
                    $photo = asset('') . '../storage/app/avatars/avatar.jpg';
                }
                $fec             = new DateTime($p->fecha);
                $fec2            = new DateTime(date('Y-m-d H:i:s'));
                $diff            = $fec2->diff($fec);
                $l['id']         = $p->id;
                $l['url']        = $p->url;
                $l['date']       = $p->fecha;
                if($p->tipo=='ONLINE'){
                    $l['type']=1;
                }else{
                    $l['type'] = 0;
                }
                $l['hour']       = date('H', strtotime($p->fecha));
                $l['usercreate'] = $user1->apellido1 . ' ' . $user1->nombre1;
                $l['doctor']     = $user2->apellido1 . ' ' . $user2->nombre1;
                $l['photo']      = $photo;
                $l['Fdias']      = $diff->days . ' Dias';
                $l['Fminutos']   = $diff->i . ' Minutos';
                $l['Fsegundos']  = $diff->s . ' Segundos';
                array_push($ko, $l);
            }
           
        }
        return response()->json(['msj' => 'ok', 'status' => 1, 'online' => $ko]);
    }
    public function store_ratings(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        Apps_Ratings::create([
            'id_agenda'  => $json_request['id_agenda'],
            'value'      => $json_request['value'],
            'estado'     => 1,
            'id_usuario' => $json_request['id'],
            'id_empresa' => '0992704152001',
        ]);
        return response()->json(['status' => 1, 'msj' => 'ok']);
    }
    public function verify_rating(Request $request)
    {
        if (!is_null($request->id)) {
            $rating = Apps_Ratings::where('id_agenda', $request->id)->first();
            if (!is_null($rating)) {
                return response()->json(['status' => 0]);
            } else {
                return response()->json(['status' => 1]);
            }
        }
        return response()->json(['status' => 0]);
    }
    /*     public function pendient_agend(Request $request){
    $agendas= Apps_Agenda::where('estado',1)->where('online',0)->get();
    return view('');
    } */
    public function get_pay_app(Request $request)
    {
        $getaapp  = Apps_Agenda::where('estado', 1)->where('online', 0)->get();
        $contador = 0;
        foreach ($getaapp as $g) {
            if ($g->request_id != null) {
                $request['requestId'] = null;
                $request['requestId'] = $g->request_id;
                $response             = $this->getPaymentInformationRequest($request);
                //dd($request);
                if ($response['status']['status'] == 'APPROVED') {
                    $json_request['total']      = $g->total;
                    $json_request['id_doctor']  = $g->id_doctor;
                    $json_request['id_usuario'] = $g->id_usuariocrea;
                    $json_request['date']       = $g->fecha;
                    //$json_request['message']= $response['status']['message'];
                    //$json_request['status']= $response['status']['status'];
                    $id_asiento_cabecera     = $this->asientos($json_request, '0992704152001', '1a');
                    $id_agenda               = $this->agendPatient($json_request);
                    $xp                      = str_replace("ORD", "", $g->ref_id);
                    $id_orden                = $xp;
                    $ventas                  = Ct_ventas::find($id_orden);
                    $ventas->tipo            = "VEN-FA";
                    $ventas->nro_comprobante = $response['comprobante'];
                    $ventas->id_asiento      = $id_asiento_cabecera;
                    $ventas->save();
                    $g->online    = 1;
                    $g->id_agenda = $id_agenda;
                    $g->message   = $response['status']['message'];
                    $g->status    = $response['status']['status'];
                    $g->id_venta  = $ventas->id;
                    $g->save();
                }
            }
        }
        return response()->json(['contador' => $contador, 'status' => 1]);
    }
    public function getPaymentInformationRequest(Request $request)
    {
        //GETS JSON BODY

        $requestId = $request['requestId'];
        $idempresa= $request['empresa'];
        $empresa= Empresa::find($idempresa);
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = $empresa['appid'];
        $PAGOSYFACTURAS_APPSECRET = $empresa['appsecret'];
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);

        return $response; //en el status verificar que status.status=="APPROVED"
    }
    public function uploadPhoto(Request $request)
    {
        //$extension = explode('/', explode(':', substr($request->data, 0, strpos($request->data, ';')))[1])[1];   // .jpg .png .pdf
        //$image=imageCreateFromString($request->data);
        //return response()->json(['date'=>$request->all()]);
        $extension = 'png';
        $imageName = date('YmdHis') . $request->id;
        $user      = User::find($request->id);

        /* \File::put(storage_path(). '/' . $imageName, base64_decode($image)); */
        $r1 = Storage::disk('public')->put($imageName . '.' . $extension, base64_decode($request->filename));
        if ($r1) {
            $user->imagen_url = $imageName . '.' . $extension;
            $user->save();
        }
        return response()->json(['status' => 'ok', 'imagename' => asset('') . '../storage/app/avatars/' . $imageName . '.' . $extension]);
    }
    public function misConsultas(Request $request)
    {
        $agendas = Apps_Agenda::where('estado', 1)->where('id_usuariocrea', $request->id)->get();
        $data    = [];
        foreach ($agendas as $a) {
            $doctor      = User::find($a->id_doctor);
            $p['doctor'] = $doctor->nombre1 . ' ' . $doctor->apellido1;
            $p['date']   = date('d/m/Y H:i:s', strtotime($a->fecha));
            $p['type']   = $a->tipo;
            $p['obs']    = $a->observacion;
            array_push($data, $p);
        }
        return response()->json(['status' => 1, 'data' => $data]);
    }
    public function updateProfile(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        $user         = User::find($json_request['id']);
        if (!is_null($user)) {
            $user->fecha_nacimiento = $json_request['fecha'];
            $user->email            = $json_request['email'];
            $user->save();
            return response()->json(['msj' => 'ok', 'status' => 1]);
        }
        return response()->json(['msj' => 'no', 'status' => 0]);
    }
    public function newPassword(Request $request)
    {
        $json_request = json_decode($request->getContent(), true);
        $usuario      = User::find($json_request['id']);
        $password     = $json_request['password'];
        $newpassword  = $json_request['newpassword'];
        if (!Hash::check($password, $usuario->password)) {
            return response()->json(['status' => 0]);
        }
        $usuario->password = bcrypt($newpassword);
        $usuario->save();
        return response()->json(['status' => 1]);
    }
    public function privacidad(Request $request)
    {
        return view('iecedapps.privacidad');
    }
    public function updateFamiliar(Request $request)
    {
        $json_request           = json_decode($request->getContent(), true);
        $plan                   = Apps_Plan_Miembros::find($json_request['id']);
        $plan->telefono         = $json_request['phone'];
        $plan->fecha_nacimiento = $json_request['date'];
        $plan->direccion        = $json_request['direction'];
        $plan->save();
        return response()->json('ok');
    }
    public function downloadStudio($id_protocolo, $tipo)
    {
        //dd($tipo);
        $protocolo = hc_protocolo::find($id_protocolo);
        //dd($protocolo);
        //$protocolo->procedimiento->certificado =1 sale o si no no
        // 
        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('seleccionado', '1')->where('estado', '1')->orderBy('id', 'desc')->get();

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
        //dd($firma);
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);

        //$elasto = $protocolo->procedimiento->hc_procedimiento_final->where('id_procedimiento','26')->first();
        $elasto = Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->procedimiento->id)->where('id_procedimiento', '26')->first();

        if (!is_null($elasto)) {
            $view = \View::make('hc_admision.formato.resumen_elasto', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'elasto'))->render();
        } else {
            if ($tipo == 0) {
                $view = \View::make('hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 1) {
                //return "asdsad12312";
                $view = \View::make('hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 2) {
                $view = \View::make('hc_admision.formato.resumen_2_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 3) {
                $view = \View::make('hc_admision.formato.resumen_3_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 4) {
                $view = \View::make('hc_admision.formato.resumen_4_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 5) {
                $view = \View::make('hc_admision.formato.resumen_5_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 6) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 7) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_7_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 8) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_8_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 9) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_9_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 10) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_10_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            }
        }

        //return view('hc_admision.formato.resumen_procedimiento', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'edad'=> $edad, 'paciente' => $paciente, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');

        $pdf->loadHTML($view);
        $agenda = $historia->agenda;
        $txt_fecha = substr($agenda->fechaini, 8, 2) . '_' . substr($agenda->fechaini, 5, 2) . '_' . substr($agenda->fechaini, 0, 4); //dd($txt_fecha);

        return $pdf->stream('Estudio_' . $paciente->id . '_' . $paciente->apellido1 . '_' . $paciente->nombre1 . '_' . $txt_fecha . '.pdf');
    }
    public function loadStudios(Request $request)
    {
        $id_paciente = $request['id'];
        $paciente = Paciente::find($id_paciente);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')

            ->where(function ($query) {
                $query->where('gp.tipo_procedimiento', '0')
                    ->orwhere('gp.tipo_procedimiento', '2');
            })
            ->where('hc_p.estado', '1')
            ->select('u.apellido1 as apellido', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda');
        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')

            ->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })
            ->where('hc_p.estado', '1')
            ->select('u.apellido1 as apellido', 'u.nombre1', 'u.email as tipo_procedimiento', 'u.apellido1', 'h.hcid', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda');
        $sqlUnion = $pro_completo_0->union($pro_final_0);
        $querySql = $sqlUnion->toSql();
        $query = DB::table(DB::raw("($querySql order by created_at_proto desc) as a"))->mergeBindings($sqlUnion)->get();
        $getProtocol = [];
        $empresa= DB::table('empresa')->where('apps',1)->first();
        foreach ($query as $p) {
            $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $p->id_procedimiento)->get();
            $mas = true;
            $texto = "";
            foreach ($adicionales as $value2) {
                if ($mas == true) {
                    $texto = $texto . $value2->procedimiento->nombre;
                    $mas = false;
                } else {
                    $texto = $texto . ' + ' .  $value2->procedimiento->nombre;
                }
            }
            $agenda = Agenda::find($p->id_agenda);
            $x['procedureName'] = $texto;
            $x['name'] = $p->apellido . " " . $p->nombre1;
            $x['date'] = date('d/m/Y', strtotime($agenda->fechaini));
            //$x['url'] = route('api.downloadStudio', ['id' => $p->id_protocolo, 'tipo' => '7']);
            $x['url']= $empresa->url_apps.'/api/pdfEstudios/'.$p->id_protocolo.'/7';
            array_push($getProtocol, $x);
        }

        return response()->json($getProtocol);
    }
    public function todayAgend(Request $request)
    {
        if ($request->token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json(['result' => '0', 'message' => 'Token incorrecto', 'status' => 'error']);
        }
        //$list = Horario_Doctor::where('ndia', $request['day'])->whereIn('tipo', ['0', '3'])->get();
        $axz=['0912356870','1234517896','5786412222','0000000004','4444444444'];
        $list   = User::where('id_tipo_usuario', '=', 3)->where('uso_sistema',0)->whereNotIn('id',$axz)->where('estado', '=', 1)->orderBy('tipo_documento', 'asc')->get();
        $information = array();
        if ($request['day'] < 7) {
            foreach ($list as $l) {
                $price = '40.00';
                if($l->id=="1307189140"){
                    $price='80.00';
                }
                $h = "";
                $shedule      = Horario_Doctor::where('id_doctor', $l->id)->whereIn('tipo', [1, 3,0])->where('ndia', $request['day'])->where('estado', 1)->get();
                $caso_especial = Agenda::whereDate('fechaini', $request['date'])->where('estado', 1)->where('proc_consul', 2)->where('id_doctor1', $l->id)->where('observaciones', 'LIKE', '%NO AGENDAR%')->first();
                foreach ($shedule as $x) {
                    $h .= '' . substr($x->hora_ini, 0, 5) . ' - ' . substr($x->hora_fin, 0, 5) . ',';
                }
                //dd(is_null($caso_especial), $caso_especial);
                if ($h != "" && is_null($caso_especial)) {
                    $h = $h . $price;
                    if ($l->imagen_url != ' ' && !is_null($l->imagen_url)) {
                        $pl = [
                            'name'      => $l->nombre1 . ' ' . $l->apellido1,
                            'day' => $h . ',' . $l->id,
                            'height'     => asset('') . '../storage/app/avatars/' . $l->imagen_url,

                        ];
                        array_push($information, $pl);
                    } else {
                        $pl = [
                            'name'      => $l->nombre1 . ' ' . $l->apellido1,
                            'day'       => $h . ',' . $l->id,
                            'height'     => asset('') . '../storage/app/avatars/avatar.jpg'
                        ];
                        array_push($information, $pl);
                    }
                }
            }
        }

        return response()->json([
            'result'  => '2',
            'message' => 'Correcto.',
            'status'  => 'ok',
            'list'    => $information,
        ]);
    }
    public function getHorarios(Request $json_request)
    {
        //$json_request = json_decode($request->getContent(), true);
        $id = $json_request['id'];
        $shedule      = Horario_Doctor::where('id_doctor', $id)->where('ndia', $json_request['day'])->whereIn('tipo', [1, 3,0])->where('estado', 1)->orderBy('hora_ini', 'ASC')->get();
        $hoursfree = [];
        $xp = [];
        //dd($shedule);
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
                    $time = new DateTime($json_request['date'] . ' ' . $fecha->format("H:i"));
                    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                    $stamp = $time->format('H:i');
                    $validAgenda = Agenda::whereBetween('fechaini', [
                        $json_request['date'] . $fecha->format("H:i") . ':00',
                        $json_request['date'] . $stamp . ':00'
                    ])->where('id_doctor1', $id)->where('estado', 1)->count();
                    $p['inicio'] = $json_request['date'] . $fecha->format("H:i") . ':00';
                    $p['id_doctor1'] = $id;
                    $validateMaximos = $this->validateMax1($p);
                    if ($validAgenda == 0 && $validateMaximos > 0) {
                        $price = '34.00';
                        //dd($validAgenda,$json_request['date'].$fecha->format("H:i").':00');
                        $ph['hour'] = $fecha->format("H:i");
                        $ph['date'] = $json_request['date'] . ' ' . $fecha->format("H:i");
                        $ph['date2'] = $json_request['date'] . ' ' . $stamp;
                        $ph['id'] = $id;
                        $user = User::find($id);
                        $ph['name'] = $user->apellido1 . ' ' . $user->nombre1;
                        $ph['price'] = $price;
                        $ph['finally'] = $fecha->format("H:i") . ' - ' . $stamp;
                        //$ph['letter']=substr($fecha->format("H:i"),0,2);
                        array_push($hoursfree, $ph);
                    }
                }
                $contador++;
            }
            //$p= Contable::groupBy($hoursfree,"letter");
            //array_push($xp,$p);
            //dd($p);
        }
        return response()->json($hoursfree);
    }
    public function carga_factura(Request $request)
    {
        $empresa= Empresa::where ('prioridad','1')->first();
        $request['empresa'] = $empresa->id;
       /* $apps = UserMembresia::where() DB::table('user_membresia')
      ->join('membresia','membresia.id','user_membresia.membresia_id')
      ->where('membresia.empresa_id','0993075000001')
      ->where('user_membresia.estado','1')->select('*')->whereBetween('user_membresia.fecha_compra', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:00'])->get();*/
        $consultas =  Apps_Agenda::where('apps_agenda.estado',1)
            ->join('agenda as a','a.id','apps_agenda.id_agenda')
            ->where('a.estado',1)
            ->whereNull('nro_comprobante')
            ->get();
            //dd ($consultas); 
        foreach ($consultas as $consulta){
            $request->request->add(['requestId'=> $consulta->request_id]);
            
            $numeroFact = $this->getPaymentInformationRequest($request);
            $numeroComprobante = '';
            $details = '';
            $subtotal = '';
            if($numeroFact['comprobante'] != ''){
              $numeroComprobante = $numeroFact['comprobante'];
              //$details = $numeroFact['details']['paymentMethod'];
              //$subtotal = $numeroFact['details']['subtotal'];
              $consulta->update(['nro_comprobante'=>$numeroComprobante]);
            }
        }
        return'ok';
    }
}
