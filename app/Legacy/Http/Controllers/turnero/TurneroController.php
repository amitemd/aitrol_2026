<?php

namespace Sis_medico\Http\Controllers\turnero;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Sala;
use Sis_medico\Hospital;
use Sis_medico\Paciente;
use Sis_medico\Agenda;
use Sis_medico\User;
use Sis_medico\ReservacionesTurno;
use Carbon\Carbon;
use Sis_medico\Piso;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Expr\Empty_;
use PHPExcel_Worksheet_Drawing;
use Excel;

class TurneroController extends Controller
{

    public function index(Request $request)
    {
        return view('turnero/pantalla_principal', ['sala' => $request['sala_id'], 'hospital' => $request['hospital_id'], 'eleccion' => $request['eleccion']]);
    }
    public function index_2(Request $request)
    {
        return view('turnero/pantalla_principal_sin_identificacion', ['sala' => $request['sala_id_2'], 'hospital' => $request['hospital_id_2'], 'eleccion' => $request['eleccion_2']]);
    }

    public function tabla($id_hospital, $id_sala)
    {
        $hospital = Hospital::where('id', $id_hospital)->first();
        $sala = Sala::where('id', $id_sala)->first();
        $msj = "";
        if (empty($sala) && empty($hospital)) {
            $msj = "La sala y hospital no existen";
        }
        if (empty($hospital) && !empty($sala)) {
            $msj = 'El hospital no existe';
        }
        if (empty($sala)  && !empty($hospital)) {
            $msj = 'La sala no existe';
        }
        return view('turnero/teclado', ['msj' => $msj, 'hospital' => $hospital, 'sala' => $sala]);
    }
    public function imprimir(Request $request)
    {
        $confirm = Agenda::where('id_paciente', $request['paciente_id'])->whereBetween('fechaini', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->first();

        if (count($confirm) >= 1) {
            $turnoVerificacion = ReservacionesTurno::where('id_paciente', $request['paciente_id'])->whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('estado', '0')->where('modulo', null)->first();

            if (empty($turnoVerificacion)) {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $cedula = $request['paciente_id'];
                $tipo = $request['eleccion_ok'];
                $hospital = Hospital::where('id', $request['hospital_id'])->first();
                $sala = Sala::where('id', $request['sala_id'])->first();
                $turnero = ReservacionesTurno::all();
                $aumento = 0;
                $turno = $turnero->last();
                $date = Carbon::now();
                if (empty($turno)) {
                    $aumento = 1;
                }
                if (!empty($turno)) {
                    if ($turno->created_at->day < $date->day) {
                        $aumento = 1;
                    } else {
                        $aumento = +$turno->turno + 1;
                    }
                }
                $campo = ReservacionesTurno::create([
                    'id_paciente'          => $cedula,
                    'id_hospital'          => $hospital->id,
                    'id_sala'              => $sala->id,
                    'turno'                => $aumento,
                    'letraproc'            => $tipo,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'estado'               => 0,
                ]);

                $turnero =  $this->imprimirboleto($campo->id);
                return view('turnero/ticket', ['turnero' => $turnero, 'salita' => $request['sala_id'], 'hospital' => $request['hospital_id']]);
            } else {
                $campo = $turnoVerificacion;
                $turnero =  $this->imprimirboleto($campo->id);
                return view('turnero/ticket', ['turnero' => $turnero, 'salita' => $request['sala_id'], 'hospital' => $request['hospital_id']]);
            }
        } else {

            $turnoVerificacion = ReservacionesTurno::where('cedula', $request['paciente_id'])->whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('estado', '0')->where('modulo', null)->first();

            if (empty($turnoVerificacion)) {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $cedula = $request['paciente_id'];
                $tipo = $request['eleccion_ok'];
                $hospital = Hospital::where('id', $request['hospital_id'])->first();
                $sala = Sala::where('id', $request['sala_id'])->first();
                $turnero = ReservacionesTurno::all();
                $aumento = 0;
                $turno = $turnero->last();
                $date = Carbon::now();
                if (empty($turno)) {
                    $aumento = 1;
                }
                if (!empty($turno)) {
                    if ($turno->created_at->day < $date->day) {
                        $aumento = 1;
                    } else {
                        $aumento = +$turno->turno + 1;
                    }
                }

                $campo = ReservacionesTurno::create([
                    'cedula'               => $cedula,
                    'id_hospital'          => $hospital->id,
                    'id_sala'              => $sala->id,
                    'turno'                => $aumento,
                    'letraproc'            => $tipo,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'estado'               => 0,
                ]);

                $turnero =  $this->imprimirboleto($campo->id);
                return view('turnero/ticket', ['turnero' => $turnero, 'salita' => $request['sala_id'], 'hospital' => $request['hospital_id']]);
            } else {
                $campo = $turnoVerificacion;
                $turnero =  $this->imprimirboleto($campo->id);
                return view('turnero/ticket', ['turnero' => $turnero, 'salita' => $request['sala_id'], 'hospital' => $request['hospital_id']]);
            }
        }
    }
    public function imprimirboleto($id)
    {
        $turnero = ReservacionesTurno::find($id);
        $cantAntes = ReservacionesTurno::where('id', '<>', $id)->whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('estado', 0)->count();
        return array($turnero, $cantAntes);
    }
    public function documentos(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cedula = $request['paciente_id'];
        $cedula = $request['cedpas'];
        $turnero = ReservacionesTurno::all();
        $aumento = 0;
        $turno = $turnero->last(); //ultimo registro
        $date = Carbon::now();
        $tippo = '';
        //Numero del turno
        if (empty($turno)) {
            $aumento = 1;
        } else {
            if ($turno->created_at->day < $date->day) {
                $aumento = 1;
            } else {
                $aumento = +$turno->turno + 1;
            }
        }
        //final
        //tipo = 0 hay que verificar que este agendado ! no
        if ($request['tipo'] == 0) {
            $elec = 0;
            if ($request['eleccion'] == 'procedimiento') {
                $tippo = 'procedimiento';
                $elec = 1;
            } else {
                $tippo = 'consulta';
                $elec = 0; //consulta
            }
            $user = Agenda::where('id_paciente', $cedula)
                ->where('proc_consul', $elec)
                ->whereBetween('fechaini', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])
                ->first();

            if (!empty($user)) {
                //variables para definir el nuevo turno

                try {
                    $campo = ReservacionesTurno::create([
                        'cedula'               => $cedula,
                        'id_hospital'          => $request['hospital'],
                        'id_sala'              => $request['sala'],
                        'turno'                => $aumento,
                        'letraproc'            => $request['eleccion'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'estado'               => 0, //estado inicial
                    ]);

                    return json_encode(array(
                        'status' => true,
                        'body' => $campo
                    ));
                } catch (Exception $e) {
                    return json_encode(array(
                        'status' => false,
                        'msj' => 'Porfavor contactar con las administración'
                    ));
                }
            } else {
                return json_encode(array(
                    'status' => false,
                    'msj' => 'El paciente no esta agendado para '.$tippo,
                ));
            }
        } else {
            try {
                $campo = ReservacionesTurno::create([
                    'cedula'               => $cedula,
                    'id_hospital'          => $request['hospital'],
                    'id_sala'              => $request['sala'],
                    'turno'                => $aumento,
                    'letraproc'            => $request['eleccion'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'estado'               => 0, //estado inicial
                ]);

                return json_encode(array(
                    'status' => true,
                    'body' => $campo
                ));
            } catch (Exception $e) {
                return json_encode(array(
                    'status' => false,
                    'msj' => 'Porfavor contactar con las administración'
                ));
            }
        }
    }

    public function salaespera()
    {


        return view('turnero/sala_espera');
    }
    public function tabla_espera(Request $request)
    {
        $registro = [];
        if (empty($request['fecha'])) {
            $registro = ReservacionesTurno::where('estado', '0')->get();
        } else {

            $registro = ReservacionesTurno::whereBetween('created_at', [$request['fecha'] . ' 00:00:00', $request['fecha'] . ' 23:59:59'])->get();
        }
        return view("turnero/index_sala", ['registro' => $registro]);
    }

    public function administracion(Request $request)
    {
        $turnero = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->whereIn('estado', [0, 1])->orderBy('created_at', 'asc')->paginate(10);
        return view('turnero/administracion', ['turnero' => $turnero]);
    }
    public function cambio_estado(Request $request)
    {
        $paciente = ReservacionesTurno::where('id', $request['id'])->first();
        $value = $request->session()->get('session');
        $paciente->modulo = $value;
        $paciente->estado = 1;
        $paciente->save();
        return json_encode('ok');
    }
    public function finalizar(Request $request)
    {
        $id_auth    = Auth::user()->id;
        $paciente = ReservacionesTurno::where('id', $request['id'])->whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', '1')->first();
        $paciente->estado = 2;
        $paciente->id_usuarioupdated = $id_auth;
        $paciente->save();
        return json_encode('ok');
    }
    public function turnos()
    {
        $actual = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->whereIn('estado', ['0', '1'])->first();
        return view('turnero/tabla', ['actual' => $actual]);
    }

    public function guardar_cache(Request $request)
    {
        Session::put('session', $request['eleccion']);
        $value = $request->session()->get('session');
        return json_encode('ok');
    }
    public function sala_espera()
    {

        return view('turnero/pantalla_salaespera');
    }
    public function nuevo_turnos(Request $request)
    {
        //TURNOS SIN MODULO
        $actual = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', '1')->limit(3)->get();
        return view('turnero/span_tur1', ['actual' => $actual]);
    }
    public function turno_lista()
    {
        //MOSTRAR LOS PACIENTE ATENTIDOS
        $registro = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', '2')->orderBy('created_at', 'DESC')->limit(3)->get();
        return view('turnero/span_tur', ['registro' => $registro]);
    }

    public function verficacion_turno(Request $request)
    {
        $veri = ReservacionesTurno::where('id', $request['cedula'])->whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->first();
        if ($veri->modulo == null || $veri->modulo == '') {
            return json_encode('ok');
        } else {
            return json_encode('no');
        }
    }

    public function verficacion_boleto(Request $request)
    {
        $veri = ReservacionesTurno::where('cedula', $request['cedula'])->whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', 0)->first();
        if (count($veri) > 0) { //ya esta con turno no atentido
            return json_encode(array(
                'status' => false,
                'body' => $veri,
                'msj' => 'Paciente con turno , desea remprimir'
            ));
        } else {
            return json_encode(array(
                'status' => true,
            ));
        }
    }
    public function excel_buscar(Request $request)
    {
        $fechaInicio = $request['fechaInicio'];
        $fechaFin = $request['fechaFin'];
        $horarioCompleto = ReservacionesTurno::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->where('estado', '2')->get();
    }

    public function turno_pantalla()
    {
        return view('turnero/pantalla_salaespera_sonido');
    }

    public function nuevo_turnos_pantalla()
    {
        $actual = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', '1')->limit(3)->get();
        return view('turnero/span_tur1pantalla', ['actual' => $actual]);
    }

    public function turno_lista_pantalla()
    {
        $registro = ReservacionesTurno::whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->where('estado', '2')->orderBy('created_at', 'DESC')->limit(3)->get();
        return view('turnero/span_turpantalla', ['registro' => $registro]);
    }


    public function imprimir_boleto(Request $request)
    {
        $turneroTurno = ReservacionesTurno::find($request['id_registro']);
        $usuariosEspera = ReservacionesTurno::where('estado', 0)->where('id', '<>', $request['id_registro'])->where('turno','<',$turneroTurno->turno)->whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59'])->count();

        return view('turnero.ticket', ['turnero' => $turneroTurno, 'usuariosEspera' => $usuariosEspera]);
    }
}
