<?php

namespace Sis_medico\Http\Controllers;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Examen_Obligatorio;
use Sis_medico\Examen_pendiente;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Observacion_General;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\User;
use Storage;

class PentaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20, 6)) == false) {
            return true;
        }
    }

    private function rol2()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(7)) == false) {
            return true;
        }
    }

    public function pentax()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('pentax/pentax', ['fecha' => '0']);
        
    }

    public function procedimientos_dr()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('procedimientos_dr/procedimientos_dr', ['fecha' => '0']);
    }
    public function pentax2($fecha)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('pentax/pentax', ['fecha' => $fecha]);
    }
    public function procedimientos_dr2($fecha)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('procedimientos_dr/procedimientos_dr', ['fecha' => $fecha]);
    }

    public function pentaxtv_dr()
    {

        if ($this->rol() && $this->rol2()) {
            return response()->view('errors.404');
        }

        return view('pentax/pentaxtv_dr', ['fecha' => '0']);
    }

    public function procedimientostv_dr()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('procedimientos_dr/procedimientostv_dr', ['fecha' => '0']);
    }

//FUNCION ORIGINAL
    public function pentaxtv()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('pentax/pentaxtv', ['fecha' => '0']);
  
    }


    public function log($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pentax      = DB::table('pentax')->where('pentax.id', $id)->join('agenda', 'agenda.id', '=', 'pentax.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->select('pentax.*', 'agenda.id_paciente', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')->first();
        $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $id)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();

        //dd($pentax_logs);

        return view('pentax/log', ['pentax_logs' => $pentax_logs, 'pentax' => $pentax]);

    }

    public function index($hora)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            $fecha = date('Y/m/d', $fecha);
        }

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.apellido2 as dapellido2', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '2')->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $anestesiologos = User::Where('id_tipo_usuario', '9')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();

        return view('pentax/index', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros, 'reuniones' => $reuniones, 'anestesiologos' => $anestesiologos]);
    }

    public function index_dr($hora)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            $fecha = date('Y/m/d', $fecha);
        }

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->whereNotIn('agenda.id_doctor1',['4444444444'])->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '<>', '2')->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();

        return view('procedimientos_dr/index', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros, 'reuniones' => $reuniones]);
    }

    public function indextv_dr($hora)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol() && $this->rol2()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            $fecha = date('Y/m/d', $fecha);
        }

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '2')->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $anestesiologos = User::Where('id_tipo_usuario', '9')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        return view('pentax/indextv_dr', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros, 'anestesiologos' => $anestesiologos]);
    }

    public function indextv_dr_109($hora)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol() && $this->rol2()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            $fecha = date('Y/m/d', $fecha);
        }

        //$pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->where('agenda.id_sala', '14')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->orderBy('agenda.fechaini')->get();
        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->whereNotIn('agenda.id_doctor1',['4444444444'])->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '<>', '2')->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $anestesiologos = User::Where('id_tipo_usuario', '9')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        return view('procedimientos_dr/indextv_dr', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros, 'anestesiologos' => $anestesiologos]);
    }

    public function indextv_drH($hora)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            //$fecha = date('Y/m/d', $fecha);
            $fecha = date('Y/m/d', $hora);
        }

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->whereNotIn('agenda.id_doctor1',['4444444444'])->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '<>', '2')->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        return view('procedimientos_dr/indextv_dr', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros]);
    }

//ESTA FUNCION MUESTRA LA INFORMACION DE LOS ESTADOS DE LOS PROCEDIMIENTOS
    public function indextv($hora)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = substr($hora, 0, 10);
            $fecha = date('Y/m/d', $fecha);
        }

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->whereNotIn('agenda.id_doctor1',['4444444444'])->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'agenda.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'agenda.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'agenda.id_doctor3')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.nombre as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->orderBy('agenda.fechaini')->get();

        $doctores       = User::Where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $salas          = Sala::where('estado', '1')->get();
        $seguros        = Seguro::all();

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = '0';
        }

        return view('pentax/indextv', ['pentax' => $pentax, 'fecha' => $hora, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'procedimientos' => $procedimientos, 'salas' => $salas, 'seguros' => $seguros]);
    }

    public function actualiza(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pentax       = Pentax::find($request['id']);
        $agenda       = Agenda::where('id', $pentax->id_agenda)->first();
        $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();

        $req_proc    = $request['proc'];
        $bandera     = '0';
        $bandera1    = '0';
        $descripcion = "ACTUALIZA ";

        //detecta cambios
        $flagcambio = '0';
        $flagestado = '0';

        

        if ($request['estado'] != $pentax->estado_pentax) {
            $flagestado = '1';

            if ($request['estado'] == '4') {

                $xtipo = Seguro::find($request['id_seguro'])->tipo;

                $pre_post = '0';
                $ex_pre   = null;
                $ex_post  = null;

                if ($xtipo == '0') {

                    /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
                    $bandera = true;
                    $agi     = '0';
                    if (count($req_proc) > 0) {
                        while ($bandera) {

                            $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $req_proc[$agi])->first();

                            $pre_post = '0';
                            if (!is_null($obligatorio)) {
                                $pre_post = $obligatorio->pre_post; //2 prey post

                            }
                            $agi++;
                            if ($pre_post != '0') {
                                $bandera = false;
                            }
                            if ($agi >= count($req_proc)) {
                                $bandera = false;
                            }
                        }
                    }
                    /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
                    if ($pre_post == '0') {
                        $bandera = true;
                        $agi     = '0';
                        if (count($req_proc) > 0) {
                            while ($bandera) {

                                $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $req_proc[$agi])->first();
                                $pre_post  = '0';
                                if (is_null($excepcion)) {
                                    $pre_post = '1'; //2 prey post

                                }
                                $agi++;
                                if ($pre_post != '0') {
                                    $bandera = false;
                                }
                                if ($agi >= count($req_proc)) {
                                    $bandera = false;
                                }
                            }
                        }

                    }

                    //ordenes del paciente de la ultima semana, pre y post
                    //$hoy = Date('Y-m-d');
                    $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($agenda->fechaini)));
                    $fecha_despues = Date('Y-m-d', strtotime('+5 day', strtotime($agenda->fechaini)));

                    $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $agenda->id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

                    if (is_null($ex_pre)) {
                        $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
                    }

                    $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $agenda->id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

                    if (is_null($ex_post)) {
                        $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
                    }

                    $semaforo = false;
                    if ($pre_post == '2') {
                        if (!is_null($ex_pre) && !is_null($ex_post)) {
                            if ($ex_pre->realizado == '0' || $ex_post->realizado == '0') {
                                $semaforo = true;
                            }
                        } else {
                            $semaforo = true;
                        }

                    }

                    if ($pre_post == '1') {
                        if (!is_null($ex_pre)) {
                            if ($ex_pre->realizado == '0') {
                                $semaforo = true;
                            }
                        } else {
                            $semaforo = true;
                        }

                    }

                    if ($semaforo) {

                        $this->Validate($request, ['observacion' => 'required|min:10'], ['observacion.required' => '', 'observacion.min' => 'Ingrese mínimo 10 caracteres...']);
                        $examen_pendiente = [
                            'id_agenda'       => $agenda->id,
                            'observacion'     => $request['observacion'],
                            'id_usuariocrea'  => $idusuario,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,

                        ];
                        Examen_pendiente::create($examen_pendiente);

                    }

                }
            }
        }

        if ($request['id_doctor1'] != $pentax->id_doctor1) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "DOCTOR ";
        }
        if ($request['id_anestesiologo'] != $pentax->id_anestesiologo) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ANESTESIOLOGO ";
        }
        if ($request['id_doctor2'] != $pentax->id_doctor2) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ASISTENTE1 ";
        }
        if ($request['id_doctor3'] != $pentax->id_doctor3) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ASISTENTE2 ";
        }
        if ($request['id_doctor4'] != $pentax->id_doctor4) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ENFERMERO ";
        }
        if ($request['id_sala'] != $pentax->id_sala) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "SALA ";
        }
        if ($request['id_seguro'] != $pentax->id_seguro) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "SEGURO ";
        }
        if ($request['id_subseguro'] != $pentax->id_subseguro) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "SUB-SEGURO ";
        }
        if ($request['observacion'] != '') {
            $flagcambio  = '1';
            $descripcion = $descripcion . "OBSERVACION ";
        }

        $this->validate_pentax($request, $flagcambio);

        foreach ($req_proc as $val_proc) {

            if ($bandera == '0') {
                $list_proc = $val_proc;
                $bandera   = '1';
            } else {
                $list_proc = $list_proc . "+" . $val_proc;
            }
        }

        $list_pentax_proc = "";
        foreach ($pentax_procs as $pentax_proc) {

            if ($bandera1 == '0') {
                $list_pentax_proc = "" . $pentax_proc->id_procedimiento . "";
                $bandera1         = '1';
            } else {
                $list_pentax_proc = $list_pentax_proc . "+" . $pentax_proc->id_procedimiento;
            }
        }

        if ($list_pentax_proc !== $list_proc) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "PROCEDIMIENTOS ";
            $this->validate_pentax($request, $flagcambio);
            foreach ($pentax_procs as $pentax_proc) {

                $pentax_proc->Delete();

            }
            foreach ($req_proc as $val_proc) {

                $input_pentax_pro = [
                    'id_pentax'        => $pentax->id,
                    'id_procedimiento' => $val_proc,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro);

            }
        }

//return $flagestado;
        //return $request['estado'];
        if ($flagcambio == '1' || $flagestado == '1') {
            //return "lamilchu";
            if ($flagcambio == '1') {
                $tipo_cambio = "ACTUALIZA";
            }

            $ingresa_prepa = $pentax->ingresa_prepa;
            $ingresa_proc  = $pentax->ingresa_proc;
            $ingresa_rec   = $pentax->ingresa_rec;
            $ingresa_alt   = $pentax->ingresa_alt;

            if ($flagestado == '1') {
                if ($request['estado'] == '-1') {$descripcion = "PRE - ADMISIONADO";}
                if ($request['estado'] == '0') {$descripcion = "EN ESPERA";}
                if ($request['estado'] == '1') {
                    $descripcion   = "PREPARACIÓN";
                    $ingresa_prepa = date('Y/m/d H:i:s');}
                if ($request['estado'] == '2') {
                    $descripcion  = "EN PROCEDIMIENTO";
                    $ingresa_proc = date('Y/m/d H:i:s');}
                if ($request['estado'] == '3') {
                    $descripcion = "RECUPERACIÓN";
                    $ingresa_rec = date('Y/m/d H:i:s');}
                if ($request['estado'] == '4') {
                    $descripcion = "ALTA";
                    $ingresa_alt = date('Y/m/d H:i:s');}
                if ($request['estado'] == '5') {$descripcion = "SUSPENDIDO";}
                $tipo_cambio = "ESTADO";
            }

            $input_log = [
                'id_pentax'        => $request['id'],
                'tipo_cambio'      => $tipo_cambio,
                'descripcion'      => $descripcion,
                'estado_pentax'    => $request['estado'],
                'id_seguro'        => $request['id_seguro'],
                'id_subseguro'     => $request['id_subseguro'],
                'procedimientos'   => $list_proc,
                'id_doctor1'       => $request['id_doctor1'],
                'id_doctor2'       => $request['id_doctor2'],
                'id_doctor3'       => $request['id_doctor3'],
                'id_doctor4'       => $request['id_doctor4'],
                'id_sala'          => $request['id_sala'],
                'observacion'      => $request['observacion'],
                'id_anestesiologo' => $request['id_anestesiologo'],
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
            ];

            Pentax_log::create($input_log);

            $input = ['estado_pentax' => $request['estado'],
                'id_sala'                 => $request['id_sala'],
                'id_seguro'               => $request['id_seguro'],
                'id_subseguro'            => $request['id_subseguro'],
                'id_doctor1'              => $request['id_doctor1'],
                'id_doctor2'              => $request['id_doctor2'],
                'id_doctor3'              => $request['id_doctor3'],
                'id_doctor4'              => $request['id_doctor4'],
                'id_anestesiologo'        => $request['id_anestesiologo'],
                'observacion'             => $request['observacion'],
                'ingresa_prepa'           => $ingresa_prepa,
                'ingresa_proc'            => $ingresa_proc,
                'ingresa_rec'             => $ingresa_rec,
                'ingresa_alt'             => $ingresa_alt,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariomod'           => $idusuario,
            ];

            $pentax->update($input);

            $paciente = Paciente::find($agenda->id_paciente);
            $paciente->update(['id_seguro' => $request['id_seguro'], 'id_subseguro' => $request['id_subseguro']]);

            $historia       = Historiaclinica::find($pentax->hcid);
            $fecha_atencion = $historia->fecha_atencion;
            
            if ($request['estado'] == '0') {
                $variable_creacion = 0;
                $fecha_atencion    = date('Y/m/d');
                $protocolo         = hc_protocolo::where('hcid', $historia->hcid)->first();
                //return "laex deed";

                if (!is_null($protocolo)) {
                    $protocolo->update(['fecha' => date('Y/m/d')]);
                }

                $id_historia = $pentax->hcid;
                //return $id_historia;
                $procedimientos_historia_anterior = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '0')
                    ->count();
                $procedimientos_nuevos = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->where('hc_proto.tipo_procedimiento', '0')
                    ->count();
                    
                    
                $bandera_procedimiento = 0;
                if (($procedimientos_historia_anterior == 0)) {
                    //return "tumanga";
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', 0)
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->get();

                        
                    if (count($procedimientos_pentax) > 0) {
                        $id_historia = $id_historia;
                        foreach ($procedimientos_pentax as $key => $vpro_pe) {
                            if ($key == 0) {
                                if (is_null($protocolo)) {
                                    $input_hc_procedimiento = [
                                        'id_hc'                 => $id_historia,
                                        'id_seguro'             => $paciente->id_seguro,
                                        'id_doctor_examinador'  => $request['id_doctor1'],
                                        'id_doctor_examinador2' => $request['id_doctor1'],
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                    ];

                                    $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                                    $input_hc_protocolo = [
                                        'fecha'                => date('Y-m-d'),
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
                                        'tipo_procedimiento'   => 0,
                                    ];
                                    hc_protocolo::insert($input_hc_protocolo);
                                } else {
                                    $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                                    $protocolo->update(['tipo_procedimiento' => 0]);
                                    $bandera_procedimiento = 1;
                                    $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                                    $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);
                                }
                                $input_pro_final = [
                                    'id_hc_procedimientos' => $id_hc_procedimiento,
                                    'id_procedimiento'     => $vpro_pe->id_procedimiento,
                                    'id_usuariocrea'       => $idusuario,
                                    'ip_modificacion'      => $ip_cliente,
                                    'id_usuariomod'        => $idusuario,
                                    'ip_creacion'          => $ip_cliente,
                                ];

                                Hc_Procedimiento_Final::create($input_pro_final);
                                $variable_creacion = 1;
                            } else {
                                $input_hc_procedimiento = [
                                    'id_hc'                 => $id_historia,
                                    'id_seguro'             => $paciente->id_seguro,
                                    'id_doctor_examinador'  => $request['id_doctor1'],
                                    'id_doctor_examinador2' => $request['id_doctor1'],
                                    'ip_modificacion'       => $ip_cliente,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                ];

                                $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                                $input_hc_protocolo = [
                                    'fecha'                => date('Y-m-d'),
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
                                    'tipo_procedimiento'   => 0,
                                ];
                                hc_protocolo::insert($input_hc_protocolo);
                                $input_pro_final = [
                                    'id_hc_procedimientos' => $id_hc_procedimiento,
                                    'id_procedimiento'     => $vpro_pe->id_procedimiento,
                                    'id_usuariocrea'       => $idusuario,
                                    'ip_modificacion'      => $ip_cliente,
                                    'id_usuariomod'        => $idusuario,
                                    'ip_creacion'          => $ip_cliente,
                                ];

                                Hc_Procedimiento_Final::create($input_pro_final);
                                $variable_creacion = 1;

                            }
                        }

                    }
                }

                $procedimientos_historia_anterior2 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '1')
                    ->count();
                $procedimientos_nuevos2 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->where('hc_proto.tipo_procedimiento', '1')
                    ->count();
                if (($procedimientos_historia_anterior2 == 0)) {
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', 1)
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->get();
                    if (!is_null($procedimientos_pentax)) {
                        $id_historia          = $id_historia;
                        $procedimiento        = hc_procedimientos::where('id_hc', $id_historia)->first();
                        $procedimientos_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->first();
                        foreach ($procedimientos_pentax as $key => $vpro_pe) {
                            if ((is_null($protocolo) && ($bandera_procedimiento == 0)) || ($variable_creacion == 1)) {
                                $input_hc_procedimiento = [
                                    'id_hc'                 => $id_historia,
                                    'id_seguro'             => $paciente->id_seguro,
                                    'id_doctor_examinador'  => $request['id_doctor1'],
                                    'id_doctor_examinador2' => $request['id_doctor1'],
                                    'ip_modificacion'       => $ip_cliente,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                ];

                                $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                                $input_hc_protocolo = [
                                    'fecha'                => date('Y-m-d'),
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
                                    'tipo_procedimiento'   => 1,
                                ];
                                hc_protocolo::insert($input_hc_protocolo);
                            } else {
                                $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                                $protocolo->update(['tipo_procedimiento' => 1]);
                                $bandera_procedimiento = 1;
                                $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                                $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);
                            }
                            $input_pro_final = [
                                'id_hc_procedimientos' => $id_hc_procedimiento,
                                'id_procedimiento'     => $vpro_pe->id_procedimiento,
                                'id_usuariocrea'       => $idusuario,
                                'ip_modificacion'      => $ip_cliente,
                                'id_usuariomod'        => $idusuario,
                                'ip_creacion'          => $ip_cliente,
                            ];
                            Hc_Procedimiento_Final::create($input_pro_final);
                            $variable_creacion = 1;
                        }
                    }
                }

                $procedimientos_historia_anterior3 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '2')
                    ->count();
                $procedimientos_nuevos3 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->where('hc_proto.tipo_procedimiento', '2')
                    ->count();
                if (($procedimientos_historia_anterior3 == 0)) {
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', '2')
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->first();
                    if (!is_null($procedimientos_pentax)) {
                        $id_historia          = $id_historia;
                        $procedimiento        = hc_procedimientos::where('id_hc', $id_historia)->first();
                        $procedimientos_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->first();

                        if ((is_null($protocolo) && ($bandera_procedimiento == 0)) || ($variable_creacion == 1)) {
                            $input_hc_procedimiento = [
                                'id_hc'                 => $id_historia,
                                'id_seguro'             => $paciente->id_seguro,
                                'id_doctor_examinador'  => $request['id_doctor1'],
                                'id_doctor_examinador2' => $request['id_doctor1'],
                                'ip_modificacion'       => $ip_cliente,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                            ];

                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                            $input_hc_protocolo = [
                                'fecha'                => date('Y-m-d'),
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
                                'tipo_procedimiento'   => '2',
                            ];
                            hc_protocolo::insert($input_hc_protocolo);
                        } else {
                            $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                            $protocolo->update(['tipo_procedimiento' => '2']);
                            $bandera_procedimiento = 1;
                            $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                            $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);
                        }
                        $input_pro_final = [
                            'id_hc_procedimientos' => $id_hc_procedimiento,
                            'id_procedimiento'     => $procedimientos_pentax->id_procedimiento,
                            'id_usuariocrea'       => $idusuario,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                        ];

                        Hc_Procedimiento_Final::create($input_pro_final);
                    }
                }

            }
            $historia->update(['id_seguro' => $request['id_seguro'], 'id_subseguro' => $request['id_subseguro'], 'id_doctor1' => $request['id_doctor1'], 'id_doctor2' => $request['id_doctor2'], 'id_doctor3' => $request['id_doctor3'], 'fecha_atencion' => $fecha_atencion]);
        }
        return 'ok';
        //return redirect()->route('pentax.pentax',['fecha' => $request['hora']] );

    }

    public function actualiza_dr(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pentax       = Pentax::find($request['id']);
        $agenda       = Agenda::where('id', $pentax->id_agenda)->first();
        $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();

        $req_proc    = $request['proc'];
        $bandera     = '0';
        $bandera1    = '0';
        $descripcion = "ACTUALIZA ";

        //detecta cambios
        $flagcambio = '0';
        $flagestado = '0';

        if ($request['estado'] != $pentax->estado_pentax) {
            $flagestado = '1';
            if ($request['estado'] == '4') {

                $xtipo = Seguro::find($request['id_seguro'])->tipo;

                $pre_post = '0';
                $ex_pre   = null;
                $ex_post  = null;

                if ($xtipo == '0') {

                    /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
                    $bandera = true;
                    $agi     = '0';
                    if (count($req_proc) > 0) {
                        while ($bandera) {

                            $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $req_proc[$agi])->first();

                            $pre_post = '0';
                            if (!is_null($obligatorio)) {
                                $pre_post = $obligatorio->pre_post; //2 prey post

                            }
                            $agi++;
                            if ($pre_post != '0') {
                                $bandera = false;
                            }
                            if ($agi >= count($req_proc)) {
                                $bandera = false;
                            }
                        }
                    }
                    /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
                    if ($pre_post == '0') {
                        $bandera = true;
                        $agi     = '0';
                        if (count($req_proc) > 0) {
                            while ($bandera) {

                                $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $req_proc[$agi])->first();
                                $pre_post  = '0';
                                if (is_null($excepcion)) {
                                    $pre_post = '1'; //2 prey post

                                }
                                $agi++;
                                if ($pre_post != '0') {
                                    $bandera = false;
                                }
                                if ($agi >= count($req_proc)) {
                                    $bandera = false;
                                }
                            }
                        }

                    }

                    //ordenes del paciente de la ultima semana, pre y post
                    $hoy        = Date('Y-m-d');
                    $nuevafecha = Date('Y-m-d', strtotime('- 1 month', strtotime($hoy)));

                    $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $agenda->id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

                    if (is_null($ex_pre)) {
                        $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.created_at', '>', $nuevafecha)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
                    }

                    $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $agenda->id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

                    if (is_null($ex_post)) {
                        $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.created_at', '>', $nuevafecha)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
                    }

                    if ($pre_post == '2') {
                        if (!is_null($ex_pre) && !is_null($ex_post)) {
                            if ($ex_pre->realizado == '0' || $ex_post->realizado == '0') {
                                return 'error';
                            }
                        } else {
                            return 'error';
                        }

                    }

                    if ($pre_post == '1') {
                        if (!is_null($ex_pre)) {
                            if ($ex_pre->realizado == '0') {
                                return 'error';
                            }
                        } else {
                            return 'error';
                        }

                    }
                }
            }
        }

        if ($request['id_doctor1'] != $pentax->id_doctor1) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "DOCTOR ";
        }
        if ($request['id_doctor2'] != $pentax->id_doctor2) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ASISTENTE1 ";
        }
        if ($request['id_doctor3'] != $pentax->id_doctor3) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "ASISTENTE2 ";
        }

        if ($request['id_seguro'] != $pentax->id_seguro) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "SEGURO ";
        }
        if ($request['id_subseguro'] != $pentax->id_subseguro) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "SUB-SEGURO ";
        }

        $this->validate_pentax($request, $flagcambio);

        foreach ($req_proc as $val_proc) {

            if ($bandera == '0') {
                $list_proc = $val_proc;
                $bandera   = '1';
            } else {
                $list_proc = $list_proc . "+" . $val_proc;
            }

        }

        $list_pentax_proc = "";
        foreach ($pentax_procs as $pentax_proc) {

            if ($bandera1 == '0') {
                $list_pentax_proc = "" . $pentax_proc->id_procedimiento . "";
                $bandera1         = '1';
            } else {
                $list_pentax_proc = $list_pentax_proc . "+" . $pentax_proc->id_procedimiento;
            }

        }

        if ($list_pentax_proc !== $list_proc) {
            $flagcambio  = '1';
            $descripcion = $descripcion . "PROCEDIMIENTOS ";
            $this->validate_pentax($request, $flagcambio);
            foreach ($pentax_procs as $pentax_proc) {

                $pentax_proc->Delete();

            }
            foreach ($req_proc as $val_proc) {

                $input_pentax_pro = [
                    'id_pentax'        => $pentax->id,
                    'id_procedimiento' => $val_proc,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro);

            }
        }

        if ($flagcambio == '1' || $flagestado == '1') {

            if ($flagcambio == '1') {
                $tipo_cambio = "ACTUALIZA";
            }

            $ingresa_prepa = $pentax->ingresa_prepa;
            $ingresa_proc  = $pentax->ingresa_proc;
            $ingresa_rec   = $pentax->ingresa_rec;
            $ingresa_alt   = $pentax->ingresa_alt;

            if ($flagestado == '1') {
                if ($request['estado'] == '0') {$descripcion = "EN ESPERA";}
                if ($request['estado'] == '1') {
                    $descripcion   = "PREPARACIÓN";
                    $ingresa_prepa = date('Y/m/d H:i:s');}
                if ($request['estado'] == '2') {
                    $descripcion  = "EN PROCEDIMIENTO";
                    $ingresa_proc = date('Y/m/d H:i:s');}
                if ($request['estado'] == '3') {
                    $descripcion = "RECUPERACIÓN";
                    $ingresa_rec = date('Y/m/d H:i:s');}
                if ($request['estado'] == '4') {
                    $descripcion = "ALTA";
                    $ingresa_alt = date('Y/m/d H:i:s');}
                if ($request['estado'] == '5') {$descripcion = "SUSPENDIDO";}
                $tipo_cambio = "ESTADO";
            }

            $input_log = [
                'id_pentax'       => $request['id'],
                'tipo_cambio'     => $tipo_cambio,
                'descripcion'     => $descripcion,
                'estado_pentax'   => $request['estado'],
                'id_seguro'       => $request['id_seguro'],
                'id_subseguro'    => $request['id_subseguro'],
                'procedimientos'  => $list_proc,
                'id_doctor1'      => $request['id_doctor1'],
                'id_doctor2'      => $request['id_doctor2'],
                'id_doctor3'      => $request['id_doctor3'],
                'id_sala'         => $pentax->id_sala,

                'observacion'     => $request['observacion'],
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log);

            $input = ['estado_pentax' => $request['estado'],
                'id_seguro'               => $request['id_seguro'],
                'id_subseguro'            => $request['id_subseguro'],
                'id_doctor1'              => $request['id_doctor1'],
                'id_doctor2'              => $request['id_doctor2'],
                'id_doctor3'              => $request['id_doctor3'],
                'observacion'             => $request['observacion'],
                'ingresa_prepa'           => $ingresa_prepa,
                'ingresa_proc'            => $ingresa_proc,
                'ingresa_rec'             => $ingresa_rec,
                'ingresa_alt'             => $ingresa_alt,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariomod'           => $idusuario,
            ];

            $pentax->update($input);

            $paciente = Paciente::find($agenda->id_paciente);
            $paciente->update(['id_seguro' => $request['id_seguro'], 'id_subseguro' => $request['id_subseguro']]);

            $historia       = Historiaclinica::find($pentax->hcid);
            $fecha_atencion = $historia->fecha_atencion;
            if ($request['estado'] == '0') {

                $fecha_atencion = date('Y/m/d');
                $protocolo      = hc_protocolo::where('hcid', $historia->hcid)->first();
                //return $protocolo;

                if (!is_null($protocolo)) {
                    $protocolo->update(['fecha' => date('Y/m/d')]);
                }

                $id_historia = $pentax->hcid;
                //return $id_historia;
                $procedimientos_historia_anterior = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '0')
                    ->count();
                
                $bandera_procedimiento = 0;
                if (($procedimientos_historia_anterior == 0)) {
                    //return "tumanga";
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', 0)
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->first();
                    if (!is_null($procedimientos_pentax)) {
                        //return $procedimientos_pentax;
                        $id_historia = $id_historia;
                        if (is_null($protocolo)) {
                            $input_hc_procedimiento = [
                                'id_hc'                 => $id_historia,
                                'id_seguro'             => $paciente->id_seguro,
                                'id_doctor_examinador'  => $request['id_doctor1'],
                                'id_doctor_examinador2' => $request['id_doctor1'],
                                'ip_modificacion'       => $ip_cliente,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                            ];

                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                            $input_hc_protocolo = [
                                'fecha'                => date('Y-m-d'),
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
                                'tipo_procedimiento'   => 0,
                            ];
                            hc_protocolo::insert($input_hc_protocolo);
                        } else {
                            $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                            $protocolo->update(['tipo_procedimiento' => 0]);
                            $bandera_procedimiento = 1;
                            $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                            $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);

                        }
                        $input_pro_final = [
                            'id_hc_procedimientos' => $id_hc_procedimiento,
                            'id_procedimiento'     => $procedimientos_pentax->id_procedimiento,
                            'id_usuariocrea'       => $idusuario,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                        ];

                        Hc_Procedimiento_Final::create($input_pro_final);
                    }
                }

                $procedimientos_historia_anterior2 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '1')
                    ->count();
               
                if (($procedimientos_historia_anterior2 == 0)) {
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', 1)
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->first();
                    if (!is_null($procedimientos_pentax)) {
                        $id_historia          = $id_historia;
                        $procedimiento        = hc_procedimientos::where('id_hc', $id_historia)->first();
                        $procedimientos_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->first();

                        if (is_null($protocolo) && ($bandera_procedimiento == 0)) {
                            $input_hc_procedimiento = [
                                'id_hc'                 => $id_historia,
                                'id_seguro'             => $paciente->id_seguro,
                                'id_doctor_examinador'  => $request['id_doctor1'],
                                'id_doctor_examinador2' => $request['id_doctor1'],
                                'ip_modificacion'       => $ip_cliente,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                            ];

                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                            $input_hc_protocolo = [
                                'fecha'                => date('Y-m-d'),
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
                                'tipo_procedimiento'   => 1,
                            ];
                            hc_protocolo::insert($input_hc_protocolo);
                        } else {
                            $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                            $protocolo->update(['tipo_procedimiento' => 1]);
                            $bandera_procedimiento = 1;
                            $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                            $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);
                        }
                        $input_pro_final = [
                            'id_hc_procedimientos' => $id_hc_procedimiento,
                            'id_procedimiento'     => $procedimientos_pentax->id_procedimiento,
                            'id_usuariocrea'       => $idusuario,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                        ];

                        Hc_Procedimiento_Final::create($input_pro_final);
                    }

                }

                $procedimientos_historia_anterior3 = DB::table('historiaclinica as h')
                    ->where('h.hcid', $id_historia)
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                    ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                    ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                    ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                    ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                    ->where('gp.tipo_procedimiento', '2')
                    ->count();
               
                if (($procedimientos_historia_anterior3 == 0)) {
                    $procedimientos_pentax = PentaxProc::where('id_pentax', $pentax->id)
                        ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
                        ->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
                        ->where('grupo_procedimiento.tipo_procedimiento', 2)
                        ->select('pentax_procedimiento.id_procedimiento')
                        ->first();
                    if (!is_null($procedimientos_pentax)) {
                        $id_historia          = $id_historia;
                        $procedimiento        = hc_procedimientos::where('id_hc', $id_historia)->first();
                        $procedimientos_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->first();

                        if (is_null($protocolo) && ($bandera_procedimiento == 0)) {
                            $input_hc_procedimiento = [
                                'id_hc'                 => $id_historia,
                                'id_seguro'             => $paciente->id_seguro,
                                'id_doctor_examinador'  => $request['id_doctor1'],
                                'id_doctor_examinador2' => $request['id_doctor1'],
                                'ip_modificacion'       => $ip_cliente,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                            ];

                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

                            $input_hc_protocolo = [
                                'fecha'                => date('Y-m-d'),
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
                                'tipo_procedimiento'   => 2,
                            ];
                            hc_protocolo::insert($input_hc_protocolo);
                        } else {
                            $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
                            $protocolo->update(['tipo_procedimiento' => 2]);
                            $bandera_procedimiento = 1;
                            $hc_procedimientos     = hc_procedimientos::find($id_hc_procedimiento);
                            $hc_procedimientos->update(['id_doctor_examinador' => $request['id_doctor1'], 'id_doctor_examinador2' => $request['id_doctor1']]);
                        }
                        $input_pro_final = [
                            'id_hc_procedimientos' => $id_hc_procedimiento,
                            'id_procedimiento'     => $procedimientos_pentax->id_procedimiento,
                            'id_usuariocrea'       => $idusuario,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                        ];

                        Hc_Procedimiento_Final::create($input_pro_final);
                    }

                }
            }
            $historia->update(['id_seguro' => $request['id_seguro'], 'id_subseguro' => $request['id_subseguro'], 'id_doctor1' => $request['id_doctor1'], 'id_doctor2' => $request['id_doctor2'], 'id_doctor3' => $request['id_doctor3'], 'fecha_atencion' => $fecha_atencion]);
        }
        return $request['hora'];
        //return redirect()->route('pentax.pentax',['fecha' => $request['hora']] );

    }

    //AQUI EMPIEZA OTRA FUNCION 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validate_pentax($request, $flagcambio)
    {
        $fecha = substr($request['hora'], 0, 10);
        $fecha = date('Y/m/d', $fecha);

        $fecha2 = date('Y/m/d H:i:s');

        //$cant_pentax = Pentax::where('id_doctor1',$request['id_doctor1'])->where('estado_pentax','2')->where('id','!=',$request['id'])->whereBetween('created_at', [$fecha.' 00:00', $fecha.' 23:59'])->count();

        $rules = [
            //'id_doctor1' => 'unique_doctor:'.$cant_pentax,
            'proc' => 'required',
        ];

        $nsubseg = Subseguro::where('id_seguro', $request['id_seguro'])->count();
        if ($nsubseg > 0) {
            $rules = [
                'id_subseguro' => 'required',
                'proc'         => 'required',
            ];
        }

        $mensaje = [
            'id_doctor1.unique_doctor' => 'El Doctor se encuentra ocupado',
            'proc.required'            => 'Seleccione por lo menos un procedimiento',
            'id_subseguro.required'    => 'Seleccione el Subseguro' . $nsubseg,
        ];

        $this->Validate($request, $rules, $mensaje);

        if ($flagcambio == '1') {
            $rules = [
                'observacion' => 'required',
            ];
            $mensaje = [
                'observacion.required' => 'Ingrese una observación',
            ];

            $this->Validate($request, $rules, $mensaje);
        }

        $pentax = Pentax::find($request['id']);
        $agenda = Agenda::find($pentax->id_agenda);

        if ($request['estado'] == '1' && strtotime($fecha2) > strtotime($agenda->fechaini) && $pentax->estado_pentax == '0') {

            $rules = [
                'observacion' => 'required',
            ];
            $mensaje = [
                'observacion.required' => 'Indique el motivo de la DEMORA en el inicio del procedimiento.',
            ];

            $this->Validate($request, $rules, $mensaje);
        }

        if ($request['estado'] == '5') {

            $rules = [
                'observacion' => 'required',
            ];
            $mensaje = [
                'observacion.required' => 'Indique el motivo de la SUSPENSIÓN del procedimiento.',
            ];

            $this->Validate($request, $rules, $mensaje);
        }

        /*if(!is_null($pentax)){
    $doctor = User::find($id);
    return "El Dr. ".$doctor->nombre1." ".$doctor->apellido1." se encuentra en procedimiento";
    }
    return "";*/

    }

    public function actualiza_sala($id, $id_sala, $hora)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pentax = Pentax::find($id);

        $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();

        $bandera  = '0';
        $bandera1 = '0';

        foreach ($pentax_procs as $pentax_proc) {

            if ($bandera1 == '0') {
                $list_pentax_proc = $pentax_proc->id_procedimiento;
                $bandera1         = '1';
            } else {
                $list_pentax_proc = $list_pentax_proc . "+" . $pentax_proc->id_procedimiento;
            }

        }

        $input_log = [
            'id_pentax'       => $pentax->id,
            'tipo_cambio'     => "ACTUALIZA",
            'descripcion'     => "ACTUALIZA SALA",
            'estado_pentax'   => $pentax->estado_pentax,
            'id_seguro'       => $pentax->id_seguro,
            'id_subseguro'    => $pentax->id_subseguro,
            'procedimientos'  => $list_pentax_proc,
            'id_doctor1'      => $pentax->id_doctor1,
            'id_doctor2'      => $pentax->id_doctor2,
            'id_doctor3'      => $pentax->id_doctor3,
            'id_sala'         => $id_sala,
            'observacion'     => "",
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
        ];

        Pentax_log::create($input_log);

        $input = [
            'id_sala'         => $id_sala,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $pentax->update($input);

        return redirect()->route('pentax.pentax2', ['fecha' => $hora]);

    }

    public function create()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('paciente/create');
    }

    public function search(Request $request)
    {
        $nombre2   = "";
        $apellido2 = "";
        $apellidos = explode(" ", $request['apellidos']);
        $nombres   = explode(" ", $request['nombres']);
        $apellido1 = $apellidos[0];
        $nombre1   = $nombres[0];
        if (count($nombres) > 1) {
            $nombre2 = $nombres[1];
        }
        if (count($apellidos) > 1) {
            $apellido2 = $apellidos[1];
        }
        if ($nombre1 == "") {
            $nombre1 = "";
        }
        if ($apellido1 == "") {
            $apellido1 = "";
        }
        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellidos'],
            'nombre1'   => $request['nombres'],
        ];

        $paciente = DB::table('paciente')->where('id', '!=', '9999999999');

        if (!is_null($request['id'])) {
            $paciente = $paciente->where('id', 'LIKE', '%' . $request['id'] . '%');
        }

        if (count($apellidos) > 1) {
            $paciente = $paciente->Where('apellido1', 'like', $apellido1 . '%')->Where('apellido2', 'like', $apellido2 . '%');
        } else {
            $paciente = $paciente->where(function ($query) use ($request, $apellido1) {
                $query->Where('apellido1', 'like', '%' . $apellido1 . '%')
                    ->orWhere('apellido2', 'like', '%' . $apellido1 . '%');
            });
        }

        if (count($nombres) > 1) {
            $paciente = $paciente->Where('nombre1', 'like', $nombre1 . '%')->Where('nombre2', 'like', $nombre2 . '%');
        } else {
            $paciente = $paciente->where(function ($query) use ($request, $nombre1) {
                $query->where('nombre1', 'like', "'%" . $nombre1 . "%'")
                    ->orWhere('nombre2', 'like', '%' . $nombre1 . '%');
            });
        }

        $paciente = $paciente->paginate(10);

        return view('paciente/index', ['paciente' => $paciente, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query  = Paciente::query();
        $fields = array_keys($constraints);
        $index  = 0;
        $index2 = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->orwhere($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
            }

            $index2++;
        }
        return $query->where('id', '!=', '9999999999')->paginate(10);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        seguro::create([
            'nombre'          => $request['nombre'],
            'descripcion'     => $request['descripcion'],
            'tipo'            => $request['tipo'],
            'color'           => $request['color'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->intended('/form_enviar_seguro');
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'nombre'      => 'required|max:60|unique:seguros',
            'descripcion' => 'required',
            'tipo'        => 'required',
            'color'       => 'required|unique:seguros',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $estado, $hora)
    {

        $semaforo   = false;
        $rolusuario = Auth::user()->id_tipo_usuario;



        $pentax = DB::table('pentax')->where('pentax.id', $id)
            ->join('agenda', 'agenda.id', '=', 'pentax.id_agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'pentax.hcid')
            ->select('pentax.*', 'paciente.id as cedula', 'paciente.nombre1', 'paciente.apellido1', 'paciente.nombre2', 'paciente.apellido2', 'agenda.fechaini', 'hc.parentesco')
            ->first();
            
        //dd($pentax);

        // Redirect to user list if updating user wasn't existed
        if ($pentax == null || count($pentax) == 0) {
            return redirect()->intended('/pentax');
        }

        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $pentax_procs   = DB::table('pentax_procedimiento')->where('pentax_procedimiento.id_pentax', $id)
            ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
            ->select('pentax_procedimiento.*', 'procedimiento.nombre', 'procedimiento.observacion')->get();
        $doctores       = User::Where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->get();
        $enfermeros     = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $anestesiologos = User::Where('id_tipo_usuario', '9')->get();
        $seguros        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();
        $subseguros     = Subseguro::all();

        $xtipo    = $seguros->find($pentax->id_seguro)->tipo;
        $pre_post = '0';
        $ex_pre   = null;
        $ex_post  = null;
        if ($xtipo == '0') {

            /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
            $bandera = true;
            $agi     = '0';
            if ($pentax_procs->count() > 0) {
                while ($bandera) {

                    $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $pentax_procs[$agi]->id_procedimiento)->first();

                    $pre_post = '0';
                    if (!is_null($obligatorio)) {
                        $pre_post = $obligatorio->pre_post; //2 prey post

                    }
                    $agi++;
                    if ($pre_post != '0') {
                        $bandera = false;
                    }
                    if ($agi >= $pentax_procs->count()) {
                        $bandera = false;
                    }
                }
            }
            /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($pentax_procs->count() > 0) {
                    while ($bandera) {

                        $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $pentax_procs[$agi]->id_procedimiento)->first();
                        $pre_post  = '0';
                        if (is_null($excepcion)) {
                            $pre_post = '1'; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $pentax_procs->count()) {
                            $bandera = false;
                        }
                    }
                }

            }

            //ordenes del paciente de la ultima semana, pre y post
            //$hoy = Date('Y-m-d');
            $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($pentax->fechaini)));
            $fecha_despues = Date('Y-m-d', strtotime('+5 day', strtotime($pentax->fechaini)));

            $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.id_agenda', $pentax->id_agenda)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

            if (is_null($ex_pre)) {
                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
            }

            $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.id_agenda', $pentax->id_agenda)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

            if (is_null($ex_post)) {
                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
            }

            $semaforo = false;
            if ($pre_post == '2') {
                if (!is_null($ex_pre) && !is_null($ex_post)) {
                    if ($ex_pre->realizado == '0' || $ex_post->realizado == '0') {
                        $semaforo = true;
                    }
                } else {
                    $semaforo = true;
                }

            }

            if ($pre_post == '1') {
                if (!is_null($ex_pre)) {
                    if ($ex_pre->realizado == '0') {
                        $semaforo = true;
                    }
                } else {
                    $semaforo = true;
                }

            }

            if ($estado == '4') {
                if ($semaforo) {
                    //dd($anestesiologos);
                    return view('pentax/edit', ['id' => $id, 'estado' => $estado, 'hora' => $hora, 'pentax' => $pentax, 'salas' => $salas, 'procedimientos' => $procedimientos, 'pentax_procs' => $pentax_procs, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'subseguros' => $subseguros, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post, 'anestesiologos' => $anestesiologos])->withErrors(['observacion' => ['Ingrese el motivo por el cual no se realiza el examen']]);
                }
            }

        }
        //dd($anestesiologos);
        return view('pentax/edit', ['id' => $id, 'estado' => $estado, 'hora' => $hora, 'pentax' => $pentax, 'salas' => $salas, 'procedimientos' => $procedimientos, 'pentax_procs' => $pentax_procs, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'subseguros' => $subseguros, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post, 'anestesiologos' => $anestesiologos]);
    }
    public function edit_dr($id, $estado, $hora)
    {
        $rolusuario = Auth::user()->id_tipo_usuario;

        $pentax = DB::table('pentax')->where('pentax.id', $id)
            ->join('agenda', 'agenda.id', '=', 'pentax.id_agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'pentax.hcid')
            ->select('pentax.*', 'paciente.id as cedula', 'paciente.nombre1', 'paciente.apellido1', 'paciente.nombre2', 'paciente.apellido2', 'agenda.fechaini', 'hc.parentesco')
            ->first();

        // Redirect to user list if updating user wasn't existed
        if ($pentax == null || count($pentax) == 0) {
            return redirect()->intended('/pentax');
        }

        $salas          = Sala::Where('id_hospital', '2')->where('estado', '1')->get();
        $procedimientos = Procedimiento::all();
        $pentax_procs   = DB::table('pentax_procedimiento')->where('pentax_procedimiento.id_pentax', $id)
            ->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
            ->select('pentax_procedimiento.*', 'procedimiento.nombre', 'procedimiento.observacion')->get();
        $doctores   = User::Where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $enfermeros = User::Where('id_tipo_usuario', '6')->where('estado', '1')->get();
        $seguros    = Seguro::all();
        $subseguros = Subseguro::all();

        $xtipo    = $seguros->find($pentax->id_seguro)->tipo;
        $pre_post = '0';
        $ex_pre   = null;
        $ex_post  = null;
        if ($xtipo == '0') {

            /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
            $bandera = true;
            $agi     = '0';
            if ($pentax_procs->count() > 0) {
                while ($bandera) {

                    $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $pentax_procs[$agi]->id_procedimiento)->first();

                    $pre_post = '0';
                    if (!is_null($obligatorio)) {
                        $pre_post = $obligatorio->pre_post; //2 prey post

                    }
                    $agi++;
                    if ($pre_post != '0') {
                        $bandera = false;
                    }
                    if ($agi >= $pentax_procs->count()) {
                        $bandera = false;
                    }
                }
            }
            /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($pentax_procs->count() > 0) {
                    while ($bandera) {

                        $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $pentax_procs[$agi]->id_procedimiento)->first();
                        $pre_post  = '0';
                        if (is_null($excepcion)) {
                            $pre_post = '1'; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $pentax_procs->count()) {
                            $bandera = false;
                        }
                    }
                }

            }

            //ordenes del paciente de la ultima semana, pre y post
            $hoy        = Date('Y-m-d');
            $nuevafecha = Date('Y-m-d', strtotime('- 1 month', strtotime($hoy)));

            $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.id_agenda', $pentax->id_agenda)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

            if (is_null($ex_pre)) {
                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.created_at', '>', $nuevafecha)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
            }

            $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.id_agenda', $pentax->id_agenda)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

            if (is_null($ex_post)) {
                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $pentax->cedula)->where('eo.created_at', '>', $nuevafecha)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
            }
        }

        return view('procedimientos_dr/edit', ['id' => $id, 'estado' => $estado, 'hora' => $hora, 'pentax' => $pentax, 'salas' => $salas, 'procedimientos' => $procedimientos, 'pentax_procs' => $pentax_procs, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'subseguros' => $subseguros, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    public function reporte_pentax($hora)
    {

        if ($hora == '0') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = date('Y/m/d', $hora);
        }

        Excel::create('Reporte Pentax-' . $fecha, function ($excel) use ($fecha) {

            $pentax = DB::table('pentax')->join('agenda', 'agenda.id', '=', 'pentax.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('users as au1', 'au1.id', '=', 'agenda.id_doctor1')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users as u1', 'u1.id', '=', 'pentax.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'pentax.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'pentax.id_doctor3')->join('seguros', 'seguros.id', '=', 'pentax.id_seguro')->join('sala', 'sala.id', '=', 'pentax.id_sala')->select('pentax.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'au1.nombre1 as adnombre1', 'au1.apellido1 as adapellido1', 'u1.apellido1 as dapellido1', 'u1.nombre1 as dnombre1', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala', 'procedimiento.observacion as probservacion', 'agenda.fechaini', 'agenda.est_amb_hos')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '2')->orderBy('agenda.fechaini')->get();

            $total_consulta = count($pentax);

            $observaciones = Observacion_General::where('estado', '1')->whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->OrderBy('created_at', 'desc')->get();

            $excel->sheet('Reporte Diario', function ($sheet) use ($pentax, $total_consulta, $fecha, $observaciones) {

                $sheet->mergeCells('A2:P2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE PROCEDIMIENTOS EN PENTAX');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:P3');
                $mes = substr($fecha, 5, 2);
                if ($mes == '01') {$mes_letra = "ENERO";}
                if ($mes == '02') {$mes_letra = "FEBRERO";}
                if ($mes == '03') {$mes_letra = "MARZO";}
                if ($mes == '04') {$mes_letra = "ABRIL";}
                if ($mes == '05') {$mes_letra = "MAYO";}
                if ($mes == '06') {$mes_letra = "JUNIO";}
                if ($mes == '07') {$mes_letra = "JULIO";}
                if ($mes == '08') {$mes_letra = "AGOSTO";}
                if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                if ($mes == '10') {$mes_letra = "OCTUBRE";}
                if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);
                $sheet->cell('A3', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue($fecha2);
                    $cell->setBackground('#FFFF00');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:P3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS AGENDA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('SALA PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA PROC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AMB/HOSP.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO ASIGNADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ENFERMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANESTESIOLOGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESO PREPARACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESO PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESO RECUPERACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIEMPO PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A4:U4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                    $cells->setFontWeight('bold');
                });
                $i = 5;
                foreach ($pentax as $value) {
                    //varios procedmientos agenda
                    $masproc          = $value->probservacion;
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id_agenda)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $masproc = $masproc . "+" . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }
                    //varios procedmientos pentax
                    $pentax_procs = PentaxProc::where('id_pentax', $value->id)->get();
                    if (!$pentax_procs->isEmpty()) {
                        $bandera_proc = '0';
                        foreach ($pentax_procs as $pentax_proc) {
                            if ($bandera_proc == '0') {
                                $pentax_probsr = Procedimiento::find($pentax_proc->id_procedimiento)->observacion;
                                $bandera_proc  = '1';
                            } else {
                                $pentax_probsr = $pentax_probsr . "+" . Procedimiento::find($pentax_proc->id_procedimiento)->observacion;
                            }

                        }
                    }
                    //apellidos
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        if ($value->papellido2 != "(N/A)") {
                            $cell->setValue($value->papellido1 . ' ' . $value->papellido2);
                        } else {
                            $cell->setValue($value->papellido1);
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //nombres
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        if ($value->pnombre2 != "(N/A)") {
                            $cell->setValue($value->pnombre1 . ' ' . $value->pnombre2);
                        } else {
                            $cell->setValue($value->pnombre1);
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //procedimientos agenda
                    $sheet->cell('C' . $i, function ($cell) use ($value, $masproc) {
                        $cell->setValue($masproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //procedimientos pentax
                    $sheet->cell('D' . $i, function ($cell) use ($value, $pentax_probsr) {
                        $cell->setValue($pentax_probsr);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //SALA
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->nombre_sala);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //hora
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //AMB/HOSP
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        if ($value->est_amb_hos == 0) {
                            $est_amb_hos = 'AMBULATORIO';
                        } else {
                            $est_amb_hos = 'HOSPITALIZADO';
                        }
                        $cell->setValue($est_amb_hos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //Doctor Ag
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->adnombre1 . " " . $value->adapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //Doctor1 px
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        $cell->setValue('Dr(a). ' . $value->dnombre1 . " " . $value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //Doctor2
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->d3nombre1 . " " . $value->d3apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //enfermero
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        $doctores       = User::Where('id_tipo_usuario', '6')->orWhere('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->get();
                        $pantallapentax =  Pentax::where('id_agenda',$value->id)->first();
                        if(!is_null($pantallapentax)){
                            $doctor4=$doctores->find($pantallapentax->id_doctor4);
                            if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5'){
                                if(!is_null($doctor4)){
                                    if($doctor4->id_tipo_usuario=='3'){
                                        $cell->setValue('pentax.dr_(a)'. $value->apellido1 . " " . $doctor4->nombre1);
                                    }
                                    else{
                                        $cell->setValue('pentax.dr_(a)'. $doctor4->apellido1 . " " . $doctor4->nombre1);
                                    }
                                }
                                else{
                                    $cell->setValue('pentax.dr_(a)');
                                }
                            }
                            else{
                                $cell->setValue('pentax.dr_(a)');
                            }
                        }
                        else{
                            $cell->setValue('pentax.dr_(a)');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //anestesiologo
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //CONVENIO
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        $empresa = '';
                        if(isset($value->id_empresa)){
                            $empresa = DB::table('empresa')->where('id',$value->id_empresa)->first()->nombre_corto;
                        }
                        $pantallapentax = Pentax::where('id_agenda',$value->id)->first();
                        if(!is_null($pantallapentax)){
                            $ptx_seg = Seguro::find($pantallapentax->id_seguro);
                            if(!is_null($ptx_seg)){
                                $cell->setValue($ptx_seg->nombre .'/' . $empresa);
                            }
                            else{
                                $cell->setValue($empresa);
                            }
                        }
                        else{
                            $cell->setValue($empresa);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //hora prepa
                    $sheet->cell('N' . $i, function ($cell) use ($value) {

                        $hora_prepa = "";
                        if ($value->ingresa_prepa != null) {
                            $hora_prepa = substr($value->ingresa_prepa, 11, 8);
                        }
                        $cell->setValue($hora_prepa);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //hora proc
                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        $hora_proc = "";
                        if ($value->ingresa_proc != null) {
                            $hora_proc = substr($value->ingresa_proc, 11, 8);
                        }
                        $cell->setValue($hora_proc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //hora recu
                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        $hora_rec = "";
                        if ($value->ingresa_rec != null) {
                            $hora_rec = substr($value->ingresa_rec, 11, 8);
                        }
                        $cell->setValue($hora_rec);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //duración proc
                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        $duracion = "";

                        if ($value->ingresa_rec != null && $value->ingresa_proc != null) {
                            $duracion = strtotime($value->ingresa_rec) - strtotime($value->ingresa_proc);
                            $duracion = round($duracion * 100 / 60) / 100;
                            $duracion = $duracion . " min";
                        }
                        $cell->setValue($duracion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //asistente1
                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->d2nombre1 . " " . $value->d2apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //asistente2
                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->d3nombre1 . " " . $value->d3apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //seguro
                    $sheet->cell('T' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //estado
                    $sheet->cell('U' . $i, function ($cell) use ($value) {
                        if ($value->estado_pentax == '0') {$cell->setValue('EN ESPERA');}
                        if ($value->estado_pentax == '1') {$cell->setValue('PREPARACIÓN');}
                        if ($value->estado_pentax == '2') {$cell->setValue('EN PROCEDIMIENTO');}
                        if ($value->estado_pentax == '3') {$cell->setValue('RECUPERACION');}
                        if ($value->estado_pentax == '4') {$cell->setValue('ALTA');}
                        if ($value->estado_pentax == '5') {$cell->setValue('SUSPENDER');}

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //aumento las filas
                    $i = $i + 1;
                    //OBSERVACION
                    $sheet->cell('A' . $i, function ($cell) use ($value) {

                        $cell->setValue("OBSERVACION");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('B' . $i . ':P' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        $observacion = "";
                        $logs        = Pentax_log::where('id_pentax', $value->id)->get();
                        if (!is_null($logs)) {
                            foreach ($logs as $log) {
                                if ($log->observacion != null) {
                                    $observacion = $observacion . "->" . $log->observacion;
                                }
                            }
                        }
                        $cell->setValue($observacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //aumento las filas
                    $i = $i + 1;
                }
                $i = $i + 2;
                $sheet->mergeCells('A' . $i . ':H' . $i);

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setAlignment('center');
                    $cell->setValue('REPORTE DE OBSERVACIONES GENERALES');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setFontWeight('bold');
                    $cell->setValue('USUARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B' . $i . ':H' . $i);
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel

                    $cell->setValue('OBSERVACIONES');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;

                if (!is_null($observaciones)) {

                    foreach ($observaciones as $observacion) {

                        $sheet->cell('A' . $i, function ($cell) use ($observacion) {
                            // manipulate the cel
                            $cell->setValue(User::find($observacion->id_usuariocrea)->nombre1 . ' ' . User::find($observacion->id_usuariocrea)->apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B' . $i . ':H' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($observacion) {
                            // manipulate the cel
                            $cell->setValue($observacion->observacion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i = $i + 1;

                    }

                }

            });
        })->export('xlsx');
    }

    public function show($id)
    {
        //
    }
//reporte agenda
    public function reporteagenda(Request $request)
    {
        //return date_default_timezone_get();
        setlocale(LC_ALL, 'Spanish_Ecuador');
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->paginate(5); //3=DOCTORES

        $this->rol();
        $seguro = seguro::all();
        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.telefono1, p.telefono2, p.telefono3, us.nombre1 as usnombre1, us.apellido1 as usapellido1
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users us ON us.id = a.id_usuariomod
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            sa.id_hospital = 2 AND
            a.proc_consul = 1 AND
            a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59'
            ORDER BY a.fechaini ASC");

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        foreach ($agenda2 as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
            if (!is_null($historia)) {

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }
        }

        return view('reportes/agenda-diario/indexpentax', ['procedimientos' => $agenda2, 'fecha' => $fecha, 'seguros' => $seguro, 'dp_proc' => $dp_proc]);
    }
    public function excel(Request $request)
    {

        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        Excel::create('Reporte Pentax-' . $fecha, function ($excel) use ($fecha) {

            $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.telefono1, p.telefono2, p.telefono3, us.nombre1 as usnombre1, us.apellido1 as usapellido1
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users us ON us.id = a.id_usuariomod
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            sa.id_hospital = 2 AND
            a.proc_consul = 1 AND
            a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59'
            ORDER BY a.fechaini ASC");

            $dp_proc              = [];
            $ControlDocController = new hc_admision\ControlDocController;
            foreach ($agenda2 as $a2) {

                $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
                if (!is_null($historia)) {

                    $hSeguro      = Seguro::find($historia->id_seguro);
                    $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                    $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                    $cant_pend    = $cantidad_doc - $cant_ok;

                    $dp_proc += [
                        $a2->id => $cant_pend,
                    ];
                }
            }

            $excel->sheet('Reporte Diario Pentax', function ($sheet) use ($agenda2, $fecha, $dp_proc) {
                $i = 6;
                $sheet->mergeCells('A2:R2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AGENDAMIENTO DE PROCEDIMIENTOS PENTAX');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:R3');
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
                    $cell->setBackground('#FFFF00');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:R4');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS MÉDICOS');
                    $cell->setBackground('#FFE4E1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1:R4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cells('A5:R5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONFIRMACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MÉDICO P.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MÉDICO S.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LOCAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CORTESIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DTOS. PEND.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELÉFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUSPENDE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                foreach ($agenda2 as $value) {
                    if ($value->estado_cita != 3) {
                        //varios procedmientos
                        $masproc          = $value->probservacion;
                        $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                        if (!$agprocedimientos->isEmpty()) {
                            foreach ($agprocedimientos as $agendaproc) {
                                $masproc = $masproc . "+" . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                            }
                        }
                        //varios procedmientos
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->papellido2 != "(N/A)") {
                                $cell->setValue($value->papellido1 . ' ' . $value->papellido2);
                            } else {
                                $cell->setValue($value->papellido1);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->pnombre2 != "(N/A)") {
                                $cell->setValue($value->pnombre1 . ' ' . $value->pnombre2);
                            } else {
                                $cell->setValue($value->pnombre1);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });

                        //calcular edad

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $fecha           = $value->pfecha_nacimiento;
                            list($Y, $m, $d) = explode("-", $fecha);
                            $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
                            $cell->setValue($edad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {

                            if ($value->id_usuarioconfirma != null) {

                                // manipulate the cel
                                $cell->setValue($value->unombre1 . ' ' . $value->uapellido1);
                            } else {
                                if ($value->estado_cita == '0') {
                                    $cell->setValue('POR CONFIRMAR');
                                } elseif ($value->estado_cita == '1') {
                                    $cell->setValue('CONFIRMADO');
                                } elseif ($value->estado_cita == '2') {
                                    $cell->setValue('REAGENDADO');
                                } elseif ($value->estado_cita == '3') {
                                    $cell->setValue('SUSPENDIDO');
                                } elseif ($value->estado_cita == '4') {
                                    $cell->setValue('ADMISIONADO');
                                }
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }

                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value, $masproc) {
                            // manipulate the cel
                            $cell->setValue($masproc);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 11, 5));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nombre_sala);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        if ($value->est_amb_hos == 0) {
                            $sheet->cell('H' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue('AMBULATORIO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        } else {
                            $sheet->cell('H' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $j_txt = 'HOSPITALIZADO';
                                if ($value->omni == 'SI') {
                                    $j_txt = 'HOSPITALIZADO/OMNI';
                                }
                                $cell->setValue($j_txt);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        }

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_doctor1 != "") {
                                $cell->setValue('Dr. ' . $value->d1nombre1 . ' ' . $value->d1apellido1);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });

                        if ($value->id_doctor2 != "") {
                            $sheet->cell('J' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->d2nombre1 . ' ' . $value->d2apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        } else {
                            $sheet->cell('J' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }

                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nombre_seguro);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nombre_hospital);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cortesia);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->procedencia);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value, $dp_proc) {
                            // manipulate the cel
                            if (array_has($dp_proc, $value->id)) {
                                $cell->setValue($dp_proc[$value->id]);
                            } else {
                                $cell->setValue(0);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            $telefonos = "";
                            if (!is_null($value->telefono1)) {
                                $telefonos = $value->telefono1 . "-";
                            }
                            if (!is_null($value->telefono2)) {
                                $telefonos = $telefonos . $value->telefono2 . "-";
                            }
                            if (!is_null($value->telefono3)) {
                                $telefonos = $telefonos . $value->telefono3;
                            }

                            $cell->setValue($telefonos);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            if ($value->estado_cita == '0') {
                                $cell->setValue('POR CONFIRMAR');
                            } elseif ($value->estado_cita == '1') {
                                $cell->setValue('CONFIRMADO');
                            } elseif ($value->estado_cita == '2') {
                                $cell->setValue('REAGENDADO');
                            } elseif ($value->estado_cita == '3') {
                                $cell->setValue('SUSPENDIDO');
                            } elseif ($value->estado_cita == '4') {
                                $cell->setValue('ADMISIONADO');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            if ($value->estado_cita == '3') {
                                $cell->setValue(substr($value->usnombre1, 0, 1) . $value->usapellido1);
                            } else {
                                $cell->setValue('');
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $i = $i + 1;
                    }
                }

            }

            );
        })->export('xlsx');
    }


    //PANTALLAS CONSULTORIOS PGMR. LOPEZ
    //FUNCION COPIADA
    
    public function consultatv(Request $request)
    {
       
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_doctor = $request->id_doctor;    
        $fecha = $request->fecha;
      

        if($fecha == null){
            $fecha = date('Y-m-d');
        }

        $doctores = Agenda::where('agenda.estado', '1')
                ->where('agenda.proc_consul', '0') 
                ->select('agenda.id_doctor1')
                ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])
                ->whereNotIn('agenda.id_doctor1',['4444444444','1307189140','1314490929'])
                ->groupBy('agenda.id_doctor1')
                ->orderBy('agenda.id_doctor1')
                ->get();

        if($id_doctor == null){
            $id_doctor = $doctores->first()->id_doctor1;
        }

        $consultas = Agenda::where('agenda.estado', '1')
                ->where('agenda.proc_consul', '0')
                ->join('users', 'users.id', '=', 'agenda.id_doctor1')
                ->join('sala', 'sala.id', '=', 'agenda.id_sala')
                ->join('paciente as p','p.id','agenda.id_paciente' ) 
                ->join('seguros','seguros.id','agenda.id_seguro' )    
                ->select('agenda.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido1 as papellido1', 'p.apellido2 as papellido2','users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'seguros.nombre as snombre', 'sala.nombre_sala')
                ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])
                ->where('agenda.id_doctor1',$id_doctor)
                ->orderBy('agenda.fechaini')
                ->get();
    

    return view('consulta_tv/indextv', ['consultas' => $consultas, 'fecha' => $fecha, 'doctores' => $doctores, 'id_doctor' => $id_doctor]);
    }

}
