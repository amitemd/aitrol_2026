<?php

namespace Sis_medico\Http\Controllers;

use DateInterval;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\ApProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Ct_productos;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Orden;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_protocolo_training;
use Sis_medico\Historiaclinica;
use Sis_medico\Orden;
use Sis_medico\Paciente_Doctor;
use Sis_medico\Pentax;
use Sis_medico\Subseguro;
use Sis_medico\PentaxProc;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\User;
use Storage;

class ConsultaMController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22, 14)) == false) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->get();
        //dd($especialidades);

        $fecha       = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');
        /*$agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')->join('seguros','seguros.id','=','agenda.id_seguro')->whereBetween('agenda.fechaini', [$fecha.' 00:00', $fecha.' 23:59'])->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')->join('users as au','au.id','=','agenda.id_usuariomod')->join('sala','sala.id','=','agenda.id_sala')->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')->join('hospital','hospital.id','=','sala.id_hospital')->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor')->orderby('agenda.fechaini','desc')
        ->where('proc_consul','<','2')->paginate(30);*/

        $agendas = DB::table('agenda')
            ->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->where('agenda.ip_creacion', '<>', 'cobro_hc')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor')
            ->orderby('agenda.fechaini', 'desc')
            //->where('proc_consul','<','2')->paginate(30);
            //->where('proc_consul','=','4')->paginate(30);
            ->where(function ($query) {
                $query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');
            })->paginate(30);

        $agendas_hospital = DB::table('agenda')
            ->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor')
            ->orderby('agenda.fechaini', 'desc')
            ->where('proc_consul', '4')
            ->where('agenda.ip_creacion', '<>', 'cobro_hc')
            ->where('agenda.estado', '>', '0')
            ->paginate(30);


        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
            if (!is_null($historia)) {
                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                //dd($cant_ok);
                //$cant_pend = $cantidad_doc - $cant_ok;
                if ($cantidad_doc == 0) {
                    $porcentaje = 0;
                } else {
                    $porcentaje = ($cant_ok / $cantidad_doc) * 100;
                }
                $dp_proc += [
                    $a2->id => $porcentaje,
                ];
            }
        }

        return view('consultam/index', ['agendas' => $agendas, 'proc_consul' => '2', 'cedula' => '', 'nombres' => '', 'fecha' => $fecha, 'pentax' => '2', 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => null, 'seguros' => $seguros, 'id_seguro' => null, 'especialidades' => $especialidades, 'id_especialidad' => null, 'procedimientos' => $procedimientos, 'id_procedimiento' => null, 'agendas_hospital' => $agendas_hospital]);
    }

    public function pentax(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proc_consul = $request['proc_consul'];
        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $pentax      = $request['pentax'];
        $id_doctor1  = $request['id_doctor1'];
        $id_seguro   = $request['id_seguro'];

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        if ($proc_consul == '1' && $pentax == '2') {

            $ctr_pentax = DB::table('agenda')->leftjoin('pentax', 'agenda.id', 'pentax.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->where('agenda.proc_consul', '1')->where('hospital.id', '2')->leftjoin('users as da1', 'da1.id', '=', 'agenda.id_doctor1')->leftjoin('users as d1', 'd1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', '=', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', '=', 'pentax.id_doctor3')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->join('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('hospital', 'hospital.id', '=', 'sala.id_hospital')->select('pentax.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'da1.nombre1 as danombre1', 'da1.apellido1 as daapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'agenda.fechaini', 'agenda.estado_cita', 'agenda.paciente_dr', 'agenda.id as aid')->orderby('agenda.fechaini', 'desc');

            if ($fecha != null && $fecha_hasta != null) {
                $ctr_pentax = $ctr_pentax->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            }

            if ($cedula != null) {
                $ctr_pentax = $ctr_pentax->where('agenda.id_paciente', $cedula);
            }

            if ($id_doctor1 != null) {
                $ctr_pentax = $ctr_pentax->where('agenda.id_doctor1', $id_doctor1);
            }

            if ($id_seguro != null) {
                $ctr_pentax = $ctr_pentax->where('agenda.id_seguro', $id_seguro);
            }

            if ($nombres != null) {

                $nombres2 = explode(" ", $nombres);
                $cantidad = count($nombres2);

                if ($cantidad == '2' || $cantidad == '3') {
                    $ctr_pentax = $ctr_pentax->where(function ($jq1) use ($nombres) {
                        $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                    });
                } else {

                    $ctr_pentax = $ctr_pentax->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                }
            }

            $ctr_pentax = $ctr_pentax->paginate(30);
            //dd($ctr_pentax);

            $p_procs = [];
            foreach ($ctr_pentax as $px) {
                $p_procs[$px->id] = DB::table('pentax_procedimiento')->where('pentax_procedimiento.id_pentax', $px->id)->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')->select('procedimiento.id', 'procedimiento.nombre', 'procedimiento.observacion')->get();
            }

            //dd($p_procs['33']['0']->nombre);

            return view('consultam/indexpentax', ['ctr_pentax' => $ctr_pentax, 'proc_consul' => '1', 'cedula' => '', 'nombres' => '', 'fecha' => $fecha, 'pentax' => '2', 'p_procs' => $p_procs, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro]);
        } else {
            return $this->search($request);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proc_consul      = $request['proc_consul'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $pentax           = $request['pentax'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $espid            = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        /*$agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')->join('seguros','seguros.id','=','agenda.id_seguro')->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')->join('users as au','au.id','=','agenda.id_usuariomod')->leftjoin('sala','sala.id','=','agenda.id_sala')->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')->leftjoin('hospital','hospital.id','=','sala.id_hospital')->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor')->orderby('agenda.fechaini','desc')->where('proc_consul','<','2');*/

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->where('agenda.ip_creacion', '<>', 'cobro_hc')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor')->orderby('agenda.fechaini', 'desc')->where(function ($query) {
            $query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');
        });

        $agendas_hospital = DB::table('agenda')
            ->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->where('agenda.ip_creacion', '<>', 'cobro_hc')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor')
            ->orderby('agenda.fechaini', 'desc')
            ->where('proc_consul', '4')
            ->where('agenda.estado', '>', '0');

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);
        }

        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas = $agendas->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas = $agendas->where('hospital.id', '<>', '2');
            }
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $agendas_hospital = $agendas_hospital->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
            $agendas_hospital = $agendas_hospital->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
            $agendas_hospital = $agendas_hospital->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('agenda.id_seguro', $id_seguro);
            $agendas_hospital = $agendas_hospital->where('agenda.id_seguro', $id_seguro);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });

                $agendas_hospital = $agendas_hospital->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                $agendas_hospital = $agendas_hospital->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($espid != null) {
            $agendas = $agendas->where('agenda.espid', $espid);
            $agendas_hospital = $agendas_hospital->where('agenda.espid', $espid);
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            //dd($agendas->get());
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });

            $agendas_hospital = $agendas_hospital->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }
        //dd($id_procedimiento,$agendas->get());

        $agendas = $agendas->paginate(30);

        $agendas_hospital = $agendas_hospital->paginate(30);

        $dp_proc              = [];
        $arr_prb              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                //$cant_pend = $cantidad_doc - $cant_ok;
                if ($cantidad_doc == 0) {
                    $porcentaje = 0;
                } else {
                    $porcentaje = ($cant_ok / $cantidad_doc) * 100;
                }
                //$arr_prb += [$a2->id => [$cantidad_doc, $cant_ok, $cant_pend]];
                $dp_proc += [
                    $a2->id => $porcentaje,
                ];
            }
        }
        //dd($arr_prb);

        $especialidades = Especialidad::where('estado', '1')->get();
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->get();

        return view('consultam/index', ['agendas' => $agendas, 'proc_consul' => $proc_consul, 'cedula' => $cedula, 'nombres' => $nombres, 'fecha' => $fecha, 'pentax' => $pentax, 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'especialidades' => $especialidades, 'id_especialidad' => $espid, 'procedimientos' => $procedimientos, 'id_procedimiento' => $id_procedimiento, 'agendas_hospital' => $agendas_hospital]);
    }

    public function reporte(Request $request)
    {

        $proc_consul      = $request['proc_consul'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $pentax           = $request['pentax'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $espid            = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        /*$agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')
        ->join('seguros','seguros.id','=','agenda.id_seguro')
        ->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')
        ->join('users as uc','uc.id','=','agenda.id_usuariocrea')
        ->join('users as au','au.id','=','agenda.id_usuariomod')
        ->leftjoin('sala','sala.id','=','agenda.id_sala')
        ->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')
        ->leftjoin('hospital','hospital.id','=','sala.id_hospital')
        ->leftjoin('pentax','pentax.id_agenda','agenda.id')
        ->leftjoin('seguros as seguro_pentax','seguro_pentax.id','=','pentax.id_seguro')
        ->leftjoin('users as dp1','dp1.id','=','pentax.id_doctor1')
        ->leftjoin('users as d2','d2.id','pentax.id_doctor2')
        ->leftjoin('users as d3','d3.id','pentax.id_doctor3')
        ->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','uc.apellido1 as ucapellido','uc.nombre1 as ucnombre','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor','dp1.nombre1 as dp1nombre1','dp1.apellido1 as dp1apellido1','pentax.id as pxid','d2.apellido1 as d2apellido1','d3.apellido1 as d3apellido1','pentax.estado_pentax','pentax.ingresa_alt','paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax','paciente.direccion','paciente.telefono1','paciente.telefono2','paciente.telefono3','paciente.parentesco','paciente.id_usuario','paciente.referido')
        ->orderby('agenda.fechaini','desc')
        ->where('proc_consul','<','2');*/

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->leftjoin('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')
            ->leftjoin('subseguro as ss', 'ss.id', 'hc.id_subseguro')
            ->join('users as uc', 'uc.id', '=', 'agenda.id_usuariocrea')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')
            ->leftjoin('seguros as seguro_pentax', 'seguro_pentax.id', '=', 'pentax.id_seguro')
            ->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')
            ->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')
            ->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')
            ->where('agenda.ip_creacion', '<>', 'cobro_hc')
            ->select('ss.nombre as nombre_subseguro', 'hc.id_subseguro','agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.fecha_nacimiento as fech_nacimiento', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'uc.apellido1 as ucapellido', 'uc.nombre1 as ucnombre', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'paciente.parentesco', 'paciente.id_usuario', 'paciente.referido', 'pentax.id_anestesiologo', 'paciente.origen', 'paciente.origen2', 'paciente.otro', 'paciente.sexo')
            ->orderby('agenda.fechaini', 'desc')
            ->where(function ($query) {
                $query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');
            });

            

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);
        }

        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas = $agendas->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas = $agendas->where('hospital.id', '<>', '2');
            }
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('agenda.id_seguro', $id_seguro);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($espid != null) {
            $agendas = $agendas->where('agenda.espid', $espid);
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }

        $agendas = $agendas->get();

        if(Auth::user()->id == '0951561075'){
            //dd($agendas);
        }

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }
        }
       // dd($agendas);

        $fecha_d = date('Y/m/d');
        Excel::create('Agenda-' . $fecha_d, function ($excel) use ($agendas, $fecha) {

            $excel->sheet('Consulta Agendas', function ($sheet) use ($agendas, $fecha) {
                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A1:S1');

                $sheet->mergeCells('A2:P2');
                $sheet->mergeCells('Q2:V2');
                $sheet->mergeCells('W2:Z2');
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTAS Y PROCEDIMIENTOS AGENDADOS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS DE LA AGENDA');
                    $cell->setBackground('#80ccff');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS PENTAX');
                    $cell->setBackground('#c2f0f0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DIRECCIONES Y TELEFONOS');
                    $cell->setBackground('#c2f000');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:AA4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('VIP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('CORTESIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MODIFICA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSECUTIVO/PRIMERA VEZ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ULTIMA MODIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBSEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //DIRECCIONES Y TELEFONOS
                $sheet->cell('AB4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AI4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AMB/HOSP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AJ4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OMNI');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AK4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANESTESIOLOGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AL4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AM4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AN4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GENERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas as $value) {
                    $historiaclinica = Historiaclinica::where('id_agenda', $value->id)->first();
                    $txt_pro         = '';
                    if (!is_null($historiaclinica)) {
                        $evolucion = Hc_Evolucion::where('hcid', $historiaclinica->hcid)->where('secuencia', 0)->first();
                        if (!is_null($evolucion)) {
                            $orden = Orden::where('id_evolucion', $evolucion->id)->get();
                            if (!is_null($orden)) {
                                foreach ($orden as $o) {
                                    $orden_tipo = $o->orden_tipo;
                                    foreach ($orden_tipo as $ot) {
                                        $orden_proc = $ot->orden_procedimiento;
                                        foreach ($orden_proc as $op) {
                                            $procedimiento = $op->procedimiento;
                                            if (!is_null($procedimiento)) {
                                                if ($txt_pro == '') {
                                                    $txt_pro = $procedimiento->nombre;
                                                } else {
                                                    $txt_pro = $txt_pro . '+' . $procedimiento->nombre;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $empresa = null;
                    if ($value->id_empresa != null) {
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $txtcolor = '#000000';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->fech_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        if (!is_null($value->fech_nacimiento)) {
                            $fecha           = $value->fech_nacimiento;
                            list($Y, $m, $d) = explode("-", $fecha);
                            $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
                            $cell->setValue($edad);
                        } else {
                            $cell->setValue("");
                        }
                        //$cell->setValue($value->edad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $vip = '';
                        if ($value->vip == '1') {
                            $vip = 'VIP';
                        }
                        $cell->setValue($vip);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $part = '';
                        /*if ($value->paciente_dr == '1') {
                            $part = 'PART.';
                        }*/
                        if ($value->paciente_dr == '1') {
                            $pac_doc = Paciente_Doctor::where('id_paciente', $value->id_paciente)->first();
                            if (!is_null($pac_doc)) {

                                $xdoc = $pac_doc->doctor;
                                //dd($xdoc);
                                if (!is_null($xdoc)) {
                                    $part = $xdoc->apellido1 . ' ' . $xdoc->nombre1;
                                }
                            }
                        }
                        $cell->setValue($part);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel

                        $cell->setValue($value->cortesia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor, $historiaclinica, $empresa) {
                        // manipulate the cel
                        if ($value->omni == 'OM') {
                            if ($value->proc_consul == '4') {
                                $cell->setValue($value->sala_hospital);
                            }
                        } else {
                            $cell->setValue($value->snombre);
                        }
                        //$cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    /*if(!is_null($value->probservacion)){
                    $vproc = $value->probservacion;
                    }
                    else{
                    if($value->observaciones = 'EVOLUCION CREADA POR EL DOCTOR')
                    {
                    if($value->omni = 'OM'){
                    $vproc = 'VISITA OMNI';
                    }else{
                    $vproc = 'VISITA';
                    }
                    }
                    }

                    $agprocedimientos = AgendaProcedimiento::where('id_agenda',$value->id)->get();
                    if(!$agprocedimientos->isEmpty()){
                    foreach($agprocedimientos as $agendaproc){
                    $vproc = $vproc.' + '.Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                    }
                    } */

                    if ($value->proc_consul == '0') {
                        $vproc = 'CONSULTA';
                    } else {
                        if ($value->proc_consul == '1') {

                            if (!is_null($value->probservacion)) {
                                $vproc = $value->probservacion;
                            }

                            $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                            if (!$agprocedimientos->isEmpty()) {
                                foreach ($agprocedimientos as $agendaproc) {
                                    $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                                }
                            }
                        } else {

                            if ($value->observaciones == 'EVOLUCION CREADA POR EL DOCTOR') {
                                if ($value->omni == 'OM') {
                                    $vproc = 'VISITA OMNI';
                                } else {
                                    $vproc = 'VISITA';
                                }
                            }
                        }
                    }

                    $sheet->cell('L' . $i, function ($cell) use ($value, $vproc, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    /*$sheet->cell('K'.$i, function($cell) use($value, $txtcolor, $historiaclinica, $empresa) {
                    // manipulate the cel
                    $consultorio='';
                    if($value->consultorio!='0'){
                    $consultorio='/CONSULTORIO';
                    }

                    if(is_null($historiaclinica)){
                    $cell->setValue($value->senombre.$consultorio);
                    }else{
                    if($empresa!=null){
                    $cell->setValue($historiaclinica->seguro->nombre.$consultorio.'/'.$empresa->nombre_corto);
                    }else{
                    $cell->setValue($historiaclinica->seguro->nombre.$consultorio);
                    }

                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                    });*/

                    //$historiaclinica = Historiaclinica::where('id_agenda',$value->id)->first();

                    $hc_seguro = null;
                    $hc        = null;
                    $hc_proc   = null;

                    $hc = Historiaclinica::where('id_agenda', $value->id)->first();

                    if (!is_null($hc)) {

                        $hc_proc = hc_procedimientos::where('id_hc', $hc->hcid)->first();
                    }

                    if (!is_null($hc_proc)) {
                        if ($hc_proc->id_seguro != null) {
                            $hc_seguro = Seguro::find($hc_proc->id_seguro)->nombre;
                        }
                    }

                    $sheet->cell('M' . $i, function ($cell) use ($value, $txtcolor, $historiaclinica, $empresa, $hc_seguro) {
                        // manipulate the cel
                        $consultorio = '';
                        if ($value->consultorio != '0') {
                            $consultorio = '/CONSULTORIO';
                        }

                        if (is_null($historiaclinica)) {
                            $cell->setValue($value->senombre . $consultorio);
                        } else {

                            if ($value->omni == 'OM') {

                                $cell->setValue($hc_seguro);
                            } else {

                                $cell->setValue($value->senombre);
                            }
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ucnombre . ' ' . $value->ucapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->aunombre1 . ' ' . $value->auapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $vest = null;
                    if ($value->omni == 'OM') {
                        if ($value->estado_cita == '4') {
                            $vest = 'INGRESO';
                        } elseif ($value->estado_cita == '5') {
                            $vest = 'ALTA';
                        } elseif ($value->estado_cita == '6') {
                            $vest = 'EMERGENCIA';
                        }
                    } else {
                        if ($value->estado_cita == '0') {
                            $vest = 'Por Confirmar';
                        } elseif ($value->estado_cita == '1') {
                            $vest = 'Confirmado';
                        } elseif ($value->estado_cita == '3') {
                            $vest = 'Suspendido';
                        } elseif ($value->estado_cita == '-1') {
                            $vest = 'No Asiste';
                        } elseif ($value->estado_cita == '4') {
                            $vest = 'Asistió';
                        } elseif ($value->estado_cita == '2') {
                            if ($value->estado == '1') {
                                $vest = 'Completar Datos';
                            } else {
                                $vest = 'Reagendar';
                            }
                        }
                    }

                    $sheet->cell('P' . $i, function ($cell) use ($value, $vest, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $vcita = "";
                    if ($value->tipo_cita == '0') {
                        $vcita = 'PRIMERA VEZ';
                    } else {
                        $vcita = 'CONSECUTIVO';
                    }
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $vcita, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vcita);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->updated_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('U' . $i, function ($cell) use ($value, $txtcolor) {

                        if ($value->proc_consul == 0) {
                            $historia = historiaclinica::where('id_agenda', '=', $value->id)->first();
                            if (!is_null($historia)) {
                                $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
                                if (!is_null($procedimiento)) {
                                    // manipulate the cel
                                    if (!is_null($procedimiento->id_doctor_examinador)) {
                                        $cell->setValue($procedimiento->doctor->nombre1 . ' ' . $procedimiento->doctor->apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    } else {
                                        // manipulate the cel
                                        $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    }
                                } else {
                                    // manipulate the cel
                                    $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setFontColor($txtcolor);
                                }
                            } else {
                                // manipulate the cel
                                $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontColor($txtcolor);
                            }
                        } else {
                            // manipulate the cel
                            $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        }
                    });

                    $txtpproc = "";
                    if (!is_null($value->pxid)) {
                        $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                        //dd($pentaxprocedimientos);
                        if (!is_null($pentaxprocedimientos)) {
                            $ban = '0';
                            foreach ($pentaxprocedimientos as $proc) {
                                if ($ban == '0') {
                                    $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                    $ban      = '1';
                                } else {
                                    $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                }
                            }
                        }
                    }

                    $sheet->cell('V' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $txtasis = $value->d2apellido1;
                    if ($value->d3apellido1 != null) {
                        $txtasis = $txtasis . ' + ' . $value->d3apellido1;
                    }
                    $sheet->cell('W' . $i, function ($cell) use ($value, $txtcolor, $txtasis, $historiaclinica, $empresa) {
                        // manipulate the cel
                        if (is_null($historiaclinica)) {
                            $cell->setValue($value->senombre);
                            if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                $cell->setValue($value->senombre . " IESS CONSULTORIO ");
                            }
                        } else {
                            if ($empresa != null) {
                                $cell->setValue($historiaclinica->seguro->nombre . '/' . $empresa->nombre_corto);
                                if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                    $cell->setValue($historiaclinica->seguro->nombre . '/' . $empresa->nombre_corto . " IESS CONSULTORIO ");
                                }
                            } else {
                                $cell->setValue($historiaclinica->seguro->nombre);
                                if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                    $cell->setValue($historiaclinica->seguro->nombre . " IESS CONSULTORIO ");
                                }
                            }
                        }
                        //$cell->setValue($value->seguro_pentax);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value, $txtcolor, $txtasis) {
                        // manipulate the cel
                        if ($value->nombre_subseguro != null) {
                            $cell->setValue($value->nombre_subseguro);
                        } else {
                            $cell->setValue('');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('Y' . $i, function ($cell) use ($value, $txtcolor, $txtasis) {
                        // manipulate the cel
                        $cell->setValue($txtasis);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    if ($value->estado_pentax == '0') {
                        $txtest = 'EN ESPERA';
                    } elseif ($value->estado_pentax == '1') {
                        $txtest = 'PREPARACIÓN';
                    } elseif ($value->estado_pentax == '2') {
                        $txtest = 'EN PROCEDIMIENTO';
                    } elseif ($value->estado_pentax == '3') {
                        $txtest = 'RECUPERACION';
                    } elseif ($value->estado_pentax == '4') {
                        $txtest = 'ALTA';
                    } elseif ($value->estado_pentax == '5') {
                        $txtest = 'SUSPENDER';
                    } else {
                        $txtest = '';
                    }

                    $sheet->cell('Z' . $i, function ($cell) use ($value, $txtcolor, $txtest) {
                        // manipulate the cel
                        $cell->setValue($txtest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('AA' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ingresa_alt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    //DIRECCIONES Y TELEFONOS
                    $sheet->cell('AB' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->direccion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '-' . $value->telefono2 . '-' . $value->telefono3);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AE' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $usuario = null;
                        $usuario = User::find($value->id_usuario);
                        if ($usuario != null) {
                            $cell->setValue($usuario->email);
                        } else {
                            $cell->setValue('');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AF' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->origen);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AG' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        //dd($value->referido);
                        $cell->setValue($value->origen2);
                        $z_txt = '';
                        /*if($value->est_amb_hos=='0'){
                        $z_txt = 'AMBULATORIO';
                        }else{
                        $z_txt = 'HOSPITALIZADO';
                        }*/
                        if ($value->origen == 'REFERIDO') {
                            $cell->setValue($value->referido);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AH' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->otro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AI' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $z_txt = '';
                        /*if($value->est_amb_hos=='0'){
                        $z_txt = 'AMBULATORIO';
                        }else{
                        $z_txt = 'HOSPITALIZADO';
                        }*/
                        if ($value->est_amb_hos == '0') {
                            $z_txt = 'AMBULATORIO';
                        } else {
                            /*if($value->est_amb_hos == '1'){
                            if($value->omni=='OM'){
                            $z_txt = 'OMNI ';
                            }
                            }else{*/
                            $z_txt = 'HOSPITALIZADO';
                            //}
                        }
                        $cell->setValue($z_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AJ' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $aa_txt = '';
                        /*if($value->omni=='SI'){
                        $aa_txt = 'OMNI';
                        }*/
                        if ($value->omni == 'SI') {
                            $aa_txt = 'OMNI';
                        } elseif ($value->omni == 'OM') {
                            $aa_txt = 'SI';
                        }
                        $cell->setValue($aa_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AK' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt_anes = '';
                        if ($value->id_anestesiologo != null) {
                            $anest    = User::find($value->id_anestesiologo);
                            $txt_anes = $anest->apellido1 . ' ' . $anest->apellido2 . ' ' . $anest->nombre1;
                        }

                        $cell->setValue($txt_anes);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('AL' . $i, function ($cell) use ($value, $txtcolor, $txt_pro) {
                        // manipulate the cel

                        $cell->setValue($txt_pro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('AM'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->procedencia);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('AN'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $sexo = '';
                        if ($value->sexo == '1') {
                            $sexo = 'Masculino';
                        } elseif ($value->sexo == '2') {
                            $sexo = 'Femenino';
                        }
                        $cell->setValue($sexo);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i = $i + 1;
                }
            });
        })->export('xlsx');
    }

    public function reporte_paquetes(Request $request)
    {

        $proc_consul = '0';
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as uc', 'uc.id', '=', 'agenda.id_usuariocrea')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')
            ->leftjoin('seguros as seguro_pentax', 'seguro_pentax.id', '=', 'pentax.id_seguro')
            ->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')
            ->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')
            ->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')
            ->where('seguros.nombre', 'like', '%PROMO%')
            ->where('proc_consul', '0')
            ->where('agenda.estado', '1')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.fecha_nacimiento as fech_nacimiento', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'uc.apellido1 as ucapellido', 'uc.nombre1 as ucnombre', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'paciente.parentesco', 'paciente.id_usuario', 'paciente.referido', 'pentax.id_anestesiologo', 'paciente.origen', 'paciente.origen2', 'paciente.otro')
            ->orderby('agenda.fechaini', 'desc');

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $agendas = $agendas->get();

        $fecha_d = date('Y/m/d');
        Excel::create('Agenda-' . $fecha_d, function ($excel) use ($agendas, $fecha) {

            $excel->sheet('Consulta Agendas', function ($sheet) use ($agendas, $fecha) {
                $fecha_d = date('Y/m/d');
                $i       = 3;
                $sheet->mergeCells('A1:K1');

                /*$sheet->mergeCells('A2:P2');
                $sheet->mergeCells('Q2:V2');
                $sheet->mergeCells('W2:Z2');*/
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cell('A1', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTA DE PAQUETES PREVENTIVOS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A2:K2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GENERO ORDEN DE PROCEDIMIENTOS/IMAGENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GENERO ORDEN DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('FECHA PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('PROXIMA CONSULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('OBSERVACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GESTION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas as $value) {
                    $historiaclinica = Historiaclinica::where('id_agenda', $value->id)->first();
                    $txt_pro         = '';

                    $txtcolor = '#000000';

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $nombre_seguro = $value->senombre;
                    if (!is_null($historiaclinica)) {
                        $nombre_seguro = $historiaclinica->seguro->nombre;
                    }

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor, $nombre_seguro) {

                        $cell->setValue($nombre_seguro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $rfecha     = substr($value->fechaini, 0, 10);
                    $orden_proc = Orden::where('id_paciente', $value->id_paciente)->whereBetween('created_at', [$rfecha . ' 0:00:00', $rfecha . ' 23:59:59'])->first();
                    $tx1        = 'NO'; //REGRESA
                    if (!is_null($orden_proc)) {
                        $tx1 = 'SI';
                    }

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor, $tx1) {

                        $cell->setValue($tx1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $examen_lab = Examen_Orden::where('id_paciente', $value->id_paciente)->whereBetween('created_at', [$rfecha . ' 0:00:00', $rfecha . ' 23:59:59'])->first();
                    $tx2        = 'NO'; //REGRESA
                    if (!is_null($orden_proc)) {
                        $tx2 = 'SI';
                    }

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor, $tx2) {

                        $cell->setValue($tx2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $tx_pro   = '';
                    $fecha_pro   = '';
                    $pro_proc = Agenda::where('id_paciente', $value->id_paciente)->where('proc_consul', '1')->where('estado', '1')->whereBetween('created_at', [$rfecha . ' 0:00:00', $rfecha . ' 23:59:59'])->first();
                    if (!is_null($pro_proc)) {
                        $fecha_pro        = substr($pro_proc->fechaini, 0, 10);
                        $tx_pro           = $pro_proc->procedimiento->observacion;
                        $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                        if (!$agprocedimientos->isEmpty()) {
                            foreach ($agprocedimientos as $agendaproc) {
                                $tx_pro = $tx_pro . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                            }
                        }
                    }

                    $pentax = Pentax::where('id_agenda', $value->id)->first();
                    if (!is_null($pentax)) {
                        $tx_pro               = "";
                        $pentaxprocedimientos = PentaxProc::where('id_pentax', $pentax->id)->get();
                        //dd($pentaxprocedimientos);
                        if ($pentaxprocedimientos->count() > 0) {
                            $ban = '0';
                            foreach ($pentaxprocedimientos as $proc) {
                                if ($ban == '0') {
                                    $tx_pro = Procedimiento::find($proc->id_procedimiento)->observacion;
                                    $ban    = '1';
                                } else {
                                    $tx_pro = $tx_pro . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                }
                            }
                        }
                    }

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor, $tx_pro) {
                        // manipulate the cel
                        $cell->setValue($tx_pro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor, $fecha_pro) {
                        // manipulate the cel

                        $cell->setValue($fecha_pro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $fecha_con = '';
                    $pro_con   = Agenda::where('id_paciente', $value->id_paciente)->where('proc_consul', '0')->where('estado', '1')->whereBetween('created_at', [$rfecha . ' 0:00:00', $rfecha . ' 23:59:59'])->first();
                    if (!is_null($pro_con)) {
                        $fecha_con = substr($pro_con->fechaini, 0, 10);
                    }

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor, $fecha_con) {
                        // manipulate the cel

                        $cell->setValue($fecha_con);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(' ');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel

                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(' ');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i = $i + 1;
                }
            });
        })->export('xlsx');
    }

    /* Reporte de Ordenes de Biopsias*/
    public function reporte_biosias(Request $request)
    {

        $fecha = $request['fecha'];

        $fecha_hasta = $request['fecha_hasta'];

        $fecha1 = $fecha . ' ' . "00:00:00";
        $fecha2 = $fecha_hasta . ' ' . "23:59:59";

        if ($fecha != null && $fecha_hasta != null) {

            $orden_biopsias = DB::table('hc4_biopsias')->join('paciente', 'paciente.id', '=', 'hc4_biopsias.id_paciente')
                ->join('users', 'users.id', '=', 'hc4_biopsias.id_doctor')
                //->where('hc4_biopsias.estado', 1)
                ->whereBetween('hc4_biopsias.created_at', [$fecha1, $fecha2])
                ->select('hc4_biopsias.created_at as f_creacion', 'hc4_biopsias.numero_frasco as num_frasco', 'hc4_biopsias.descripcion_frasco as desc_frasco', 'hc4_biopsias.observacion as obs_frasco', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'users.apellido1 as uapellido1', 'hc4_biopsias.hcid as id_historiacli')
                ->get();
        }

        $fecha_d = date('Y/m/d');
        Excel::create('Reporte de Biopsias-' . $fecha_d, function ($excel) use ($orden_biopsias, $fecha) {

            $excel->sheet('Consulta Reporte Biopsias', function ($sheet) use ($orden_biopsias, $fecha) {
                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A1:S1');

                $sheet->mergeCells('A2:P2');
                $sheet->mergeCells('Q2:V2');
                $sheet->mergeCells('W2:Z2');
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE BIOPSIAS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:AA4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('NOMBRE2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('FRASCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DESCRIPCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('OBSERVACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($orden_biopsias as $value) {

                    $txtcolor = '#000000';

                    $histclini = Historiaclinica::where('hcid', $value->id_historiacli)->first();

                    if (!is_null($histclini)) {

                        $seg = Seguro::where('id', $histclini->id_seguro)->first();
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue(substr($value->f_creacion, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->papellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->papellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->pnombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->pnombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($seg, $txtcolor) {
                        //Seguro
                        $cell->setValue($seg->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->num_frasco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->desc_frasco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->obs_frasco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->uapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i = $i + 1;
                }
            });
        })->export('xlsx');
    }

    private function doSearchingQuery()
    {
    }

    public function store(Request $request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    public function detalle($id)
    {
        /*if($this->rol()){
        return response()->view('errors.404');
        }*/

        //$agenda = Agenda::find($id);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('sala', 'sala.id', 'agenda.id_sala')
            ->leftjoin('hospital', 'hospital.id', 'sala.id_hospital')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'paciente.ocupacion', 'paciente.estadocivil', 'paciente.parentesco', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.color as color', 'seguros.nombre as sanombre', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'paciente.fecha_nacimiento', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('users as d1', 'hc.id_doctor1', 'd1.id')
            ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
            ->leftjoin('pentax', 'pentax.hcid', 'hc.hcid')
            ->leftjoin('users as d2', 'hc.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'hc.id_doctor3', 'd3.id')
            ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
            ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
            ->select('hc.*', 'seguros.nombre as snombre', 'seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'pentax.id as id_pentax', 'up.nombre1 as upnombre1', 'up.apellido1 as upapellido1', 'up.nombre2 as upnombre2', 'up.apellido2 as upapellido2')
            ->where('hc.id_agenda', '=', $agenda->id)
            ->first();

        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/consultam');
        }

        //dd($historiaclinica);

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new hc_admision\ControlDocController;
            $cantidad_doc         = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

            if (!is_null($historiaclinica->id_pentax)) {
                $pentaxprocs = DB::table('pentax_procedimiento')
                    ->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')
                    ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                    ->where('id_pentax', $historiaclinica->id_pentax)->get();

                $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $historiaclinica->id_pentax)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
                //dd($pentaxprocs);
                //dd($pentax_logs);
            }
        }

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();
        //dd($agendaprocs);

        return view('consultam/detalle', ['agenda' => $agenda, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'agendaprocs' => $agendaprocs, 'cantidad_doc' => $cantidad_doc, 'pentax_logs' => $pentax_logs]);
    }

    public function detalle_ag($id, $unix)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$agenda = Agenda::find($id);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('sala', 'sala.id', 'agenda.id_sala')
            ->leftjoin('hospital', 'hospital.id', 'sala.id_hospital')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'paciente.ocupacion', 'paciente.estadocivil', 'paciente.parentesco', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.color as color', 'seguros.nombre as sanombre', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'paciente.fecha_nacimiento', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('users as d1', 'hc.id_doctor1', 'd1.id')
            ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
            ->leftjoin('pentax', 'pentax.hcid', 'hc.hcid')
            ->leftjoin('users as d2', 'hc.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'hc.id_doctor3', 'd3.id')
            ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
            ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
            ->select('hc.*', 'seguros.nombre as snombre', 'seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'pentax.id as id_pentax', 'up.nombre1 as upnombre1', 'up.apellido1 as upapellido1', 'up.nombre2 as upnombre2', 'up.apellido2 as upapellido2')
            ->where('hc.id_agenda', '=', $agenda->id)
            ->first();

        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/consultam');
        }

        //dd($historiaclinica);

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new hc_admision\ControlDocController;
            $cantidad_doc         = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

            if (!is_null($historiaclinica->id_pentax)) {
                $pentaxprocs = DB::table('pentax_procedimiento')
                    ->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')
                    ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                    ->where('id_pentax', $historiaclinica->id_pentax)->get();

                $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $historiaclinica->id_pentax)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
                //dd($pentaxprocs);
                //dd($pentax_logs);
            }
        }

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();
        //dd($agendaprocs);

        return view('consultam/detalle_ag', ['agenda' => $agenda, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'agendaprocs' => $agendaprocs, 'cantidad_doc' => $cantidad_doc, 'pentax_logs' => $pentax_logs, 'unix' => $unix]);
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

    public function show($id)
    {
        //
    }

    public function pastelpentax(Request $request)
    {

        $hoy         = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        //$hoy=Date('Y-m-d'); PROCEDIMIENTOS AGENDADOS
        $doctor_ag = [];
        $i         = 0;
        $doctores  = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '1')->where('estado', '1')->where('estado_cita', '!=', '3')->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_ag[$i] = [$doctor, $ctaagenda];
                $i++;
            }
        }
        //dd($doctor_ag);

        //PROCEDIMIENTOS REALIZADOS
        $proc_doc = [];
        $i1       = 0;
        $doctores = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = DB::table('agenda as a')
                ->where('a.proc_consul', '1')
                ->where('a.estado', '1')
                ->whereBetween('a.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])
                ->join('pentax as p', 'p.id_agenda', 'a.id')
                ->where('p.estado_pentax', '4')
                ->where('p.id_doctor1', $doctor->id)
                ->count();
            if ($ctaagenda != '0') {
                $proc_doc[$i1] = [$doctor, $ctaagenda];
                $i1++;
            }
        }

        //CONSULTAS AGENDADAS
        $doctor_co = [];
        $k         = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_co[$k] = [$doctor, $ctaagenda];
                $k++;
            }
        }

        //CONSULTAS AGENDADAS REALIZADAS
        $doctor_co_ok = array();
        $k            = 0;
        $doctores     = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->where('estado', '1')->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])->where('estado_cita', '4')->count();
            if ($ctaagenda != '0') {
                $doctor_co_ok[$k] = [$doctor, $ctaagenda];
                $k++;
            }
        }

        //CONSULTAS AGENDADAS REALIZADAS por seguros
        $se_consultas_seg = Agenda::where('agenda.proc_consul', '0')
            ->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])
            ->where('agenda.estado', '1')
            ->where('agenda.estado_cita', '4')
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->groupBy('agenda.id_seguro')
            ->select(DB::raw('COUNT(agenda.id_seguro) as cantidad, s.nombre as nombre_seguro, s.color'))
            //->orderBy('name', 'desc')
            ->get();

        $proc_seg = [];
        $j        = 0;
        $seguros  = Seguro::all();
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $proc_seg[$j] = [$seguro, $cta_seguro];
                $j++;
            }
        }

        $co_seg = [];
        $l      = 0;
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $co_seg[$l] = [$seguro, $cta_seguro];
                $l++;
            }
        }

        //PROCEDIMIENTOS REALIZADOS SEGUROS
        $se_procedimiento_seg = Agenda::where('agenda.proc_consul', '1')
            ->whereBetween('agenda.fechaini', [$hoy . ' 00:00', $fecha_hasta . ' 23:59'])
            ->where('agenda.estado', '1')
            ->join('pentax as p', 'p.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->where('p.estado_pentax', '4')
            ->groupBy('agenda.id_seguro')
            ->select(DB::raw('COUNT(agenda.id_seguro) as cantidad, s.nombre as nombre_seguro, s.color'))
            //->orderBy('name', 'desc')
            ->get();

        return view('consultam/pastelpentax', ['doctor_ag' => $doctor_ag, 'doctor_co' => $doctor_co, 'proc_seg' => $proc_seg, 'co_seg' => $co_seg, 'fecha' => $hoy, 'fecha_hasta' => $fecha_hasta, 'proc_doc' => $proc_doc, 'doctor_co_ok' => $doctor_co_ok, 'se_procedimiento_seg' => $se_procedimiento_seg, 'se_consultas_seg' => $se_consultas_seg]);
    }

    public function search2(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;
        $doctores    = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_ag[$i] = [$doctor, $ctaagenda];
                $i++;
            }
        }

        //CONSULTAS AGENDADAS
        $doctor_co = [];
        $k         = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_co[$k] = [$doctor, $ctaagenda];
                $k++;
            }
        }

        //CONSULTAS AGENDADAS REALIZADAS
        $doctor_co_ok = array();
        $k            = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)
                ->where('proc_consul', '0')
                ->where('estado', '1')
                ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
                ->where('estado_cita', '4')
                ->count();
            if ($ctaagenda != '0') {
                $doctor_co_ok[$k] = [$doctor, $ctaagenda];
                $k++;
            }
        }
        //dd($doctor_co_ok);

        //CONSULTAS AGENDADAS REALIZADAS por seguros
        $se_consultas_seg = Agenda::where('agenda.proc_consul', '0')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
            ->where('agenda.estado', '1')
            ->where('agenda.estado_cita', '4')
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->groupBy('agenda.id_seguro')
            ->select(DB::raw('COUNT(agenda.id_seguro) as cantidad, s.nombre as nombre_seguro, s.color'))
            //->orderBy('name', 'desc')
            ->get();

        //PROCEDIMIENTOS REALIZADOS
        $proc_doc = [];
        $i1       = 0;
        $doctores = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = DB::table('agenda as a')
                ->where('a.proc_consul', '1')
                ->where('a.estado', '1')
                ->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
                ->join('pentax as p', 'p.id_agenda', 'a.id')
                ->where('p.id_doctor1', $doctor->id)
                ->where('p.estado_pentax', '4')
                ->count();

            if ($ctaagenda != '0') {
                $proc_doc[$i1] = [$doctor, $ctaagenda];
                $i1++;
            }
        }
        //dd($proc_doc);

        //PROCEDIMIENTOS REALIZADOS SEGUROS
        $se_procedimiento_seg = Agenda::where('agenda.proc_consul', '1')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
            ->where('agenda.estado', '1')
            ->join('pentax as p', 'p.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->where('p.estado_pentax', '4')
            ->groupBy('agenda.id_seguro')
            ->select(DB::raw('COUNT(agenda.id_seguro) as cantidad, s.nombre as nombre_seguro, s.color'))
            //->orderBy('name', 'desc')
            ->get();

        /*foreach ($doctores as $doctor) {
        $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('estado_cita', '4')->count();
        if ($ctaagenda != '0') {
        $doctor_co_ok[$k] = [$doctor, $ctaagenda];
        $k++;
        }

        }*/

        $proc_seg = [];
        $j        = 0;
        $seguros  = Seguro::all();
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $proc_seg[$j] = [$seguro, $cta_seguro];
                $j++;
            }
        }

        $co_seg = [];
        $l      = 0;
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $co_seg[$l] = [$seguro, $cta_seguro];
                $l++;
            }
        }

        //dd($doctor_co);

        return view('consultam/pastelpentax', ['doctor_ag' => $doctor_ag, 'doctor_co' => $doctor_co, 'proc_seg' => $proc_seg, 'co_seg' => $co_seg, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'proc_doc' => $proc_doc, 'doctor_co_ok' => $doctor_co_ok, 'se_procedimiento_seg' => $se_procedimiento_seg, 'se_consultas_seg' => $se_consultas_seg]);
    }

    //reporte agenda
    public function reporteagenda(Request $request)
    {
        //return date_default_timezone_get();
        setlocale(LC_ALL, 'Spanish_Ecuador');
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->paginate(5); //3=DOCTORES

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguro = seguro::all();
        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            sa.id_hospital != 2 AND
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

        return view('reportes/agenda-diario/indexdrh', ['procedimientos' => $agenda2, 'fecha' => $fecha, 'seguros' => $seguro, 'dp_proc' => $dp_proc]);
    }
    public function excel(Request $request)
    {

        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        Excel::create('Reporte proc-otros-' . $fecha, function ($excel) use ($fecha) {

            $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2,d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.fecha_nacimiento as pfecha_nacimiento
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            sa.id_hospital != 2 AND
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

            $excel->sheet(
                'Reporte proc-otros',
                function ($sheet) use ($agenda2, $fecha, $dp_proc) {
                    $i = 6;
                    $sheet->mergeCells('A2:P2');
                    $sheet->cell('A2', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('AGENDAMIENTO DE PROCEDIMIENTOS');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:P3');
                    $mes = substr($fecha, 5, 2);
                    if ($mes == 01) {
                        $mes_letra = "ENERO";
                    }
                    if ($mes == 02) {
                        $mes_letra = "FEBRERO";
                    }
                    if ($mes == 03) {
                        $mes_letra = "MARZO";
                    }
                    if ($mes == 04) {
                        $mes_letra = "ABRIL";
                    }
                    if ($mes == 05) {
                        $mes_letra = "MAYO";
                    }
                    if ($mes == 06) {
                        $mes_letra = "JUNIO";
                    }
                    if ($mes == 07) {
                        $mes_letra = "JULIO";
                    }
                    if ($mes == '08') {
                        $mes_letra = "AGOSTO";
                    }
                    if ($mes == '09') {
                        $mes_letra = "SEPTIEMBRE";
                    }
                    if ($mes == '10') {
                        $mes_letra = "OCTUBRE";
                    }
                    if ($mes == '11') {
                        $mes_letra = "NOVIEMBRE";
                    }
                    if ($mes == '12') {
                        $mes_letra = "DICIEMBRE";
                    }
                    $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);
                    $sheet->cell('A3', function ($cell) use ($fecha2) {
                        // manipulate the cel
                        $cell->setValue($fecha2);
                        $cell->setBackground('#FFFF00');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A4:P4');
                    $sheet->cell('A4', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('PROCEDIMIENTOS MÉDICOS');
                        $cell->setBackground('#FFE4E1');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A1:P4', function ($cells) {
                        // manipulate the range of cells
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('A5:P5', function ($cells) {
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
                        $cell->setValue('DCTOS. PENDIENTES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('P5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('ESTADO');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    foreach ($agenda2 as $value) {
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
                            if ($value->id_usuarioconfirma != '') {
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
                                } elseif ($value->estado_cita == '-1') {
                                    $cell->setValue('NO ASISTE');
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
                                $cell->setValue('HOSPITALIZADO');
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
                            if ($value->estado_cita == '0') {
                                $cell->setValue('POR CONFIRMAR');
                            } elseif ($value->estado_cita == '1') {
                                $cell->setValue('CONFIRMADO');
                            } elseif ($value->estado_cita == '2') {
                                $cell->setValue('REAGENDADO');
                            } elseif ($value->estado_cita == '3') {
                                $cell->setValue('SUSPENDIDO');
                            } elseif ($value->estado_cita == '-1') {
                                $cell->setValue('NO ASISTE');
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
                        $i = $i + 1;
                    }
                }

            );
        })->export('xlsx');
    }

    public function consulta_documentos($hcid)
    {

        /*if($this->rol()){
        return response()->view('errors.404');
        }*/
        $historia = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario')->first();

        $procs = null;

        if ($historia->proc_consul == 1) {

            $procs = Procedimiento::find($historia->id_procedimiento)->observacion;

            $procs_ag = AgendaProcedimiento::where('id_agenda', $historia->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }
        }

        $ControlDocController = new hc_admision\ControlDocController;
        $documentos           = $ControlDocController->carga_documentos_union($hcid, $historia->proc_consul, $historia->tipo);

        return view('consultam.consulta_documentos', ['documentos' => $documentos, 'historia' => $historia, 'procs' => $procs, 'hcid' => $hcid]);
    }

    public function reporte2(Request $request)
    {

        $proc_consul = $request['proc_consul'];
        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $pentax      = $request['pentax'];
        $id_doctor1  = $request['id_doctor1'];
        $id_seguro   = $request['id_seguro'];
        $hSeguro = null;

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $agendas = DB::table('agenda')->join('historiaclinica as hi', 'hi.id_agenda', 'agenda.id')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hi.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '<', '2');

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);
        }

        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas = $agendas->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas = $agendas->where('hospital.id', '<>', '2');
            }
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('hi.id_seguro', $id_seguro);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        $agendas = $agendas->get();

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
            
            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }
        }
        if ($fecha == null) {
            $fecha = date('Y/m/d');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();

        Excel::create('Registro-Brazaletes-' . $fecha, function ($excel) use ($empresa,$agendas, $fecha, $fecha_hasta, $hSeguro) {

            $excel->sheet('Consulta Agenda', function ($sheet) use ($empresa,$agendas, $fecha, $fecha_hasta, $hSeguro) {

                $sheet->setStyle(array(
                    'font' => array(
                        'name' => 'Calibri',
                        'size' => 8,

                    ),
                ));

                $sheet->setHeight(array(
                    3 => 20,
                    4 => 20,
                    5 => 15,
                    6 => 15,
                    7 => 20,
                ));

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 5,
                    'C' => 15,
                    'D' => 40,
                    'E' => 10,
                    'F' => 20,
                    'G' => 15,
                    'H' => 15,
                    'I' => 15,
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path().'/imagenes/endoscopynet_gastroquito.png'); //your image path
                $objDrawing->setCoordinates('B3');
                $objDrawing->setWidth(105);
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetX(5);
 
                $sheet->mergeCells('B3:C4');
                $sheet->mergeCells('D3:H3');
                $sheet->mergeCells('D4:H4');

                $sheet->cell('B3', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('D3', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
                $sheet->cell('D4', function ($cell) {
                    $cell->setValue('FORMULARIO DE REGISTRO DE COLOCACIÓN DE PULSERAS DE IDENTIFICACIÓN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('I3', function ($cell) {
                    $cell->setValue('Ver 0.1');
                    $cell->setBorder('thin', 'thin', '', '');
                    $cell->setAlignment('center');
                });

                $sheet->cell('I4', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('B5:C5');
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('FECHA DE REGISTRO:');
                });

                $sheet->cell('D5', function ($cell) use ($fecha, $fecha_hasta) {
                    if ($fecha != $fecha_hasta) {
                        $cell->setValue(substr($fecha, 0, 10) . ' - ' . substr($fecha_hasta, 0, 10));
                    } else {
                        $cell->setValue(substr($fecha, 0, 10));
                    }

                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('B7', function ($cell) {
                    $cell->setValue('Nro.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C7', function ($cell) {
                    $cell->setValue('MOTIVOS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
                $sheet->cell('D7', function ($cell) {
                    $cell->setValue('NOMBRE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
                $sheet->cell('E7', function ($cell) {
                    $cell->setValue('No. HC');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('F7', function ($cell) {
                    $cell->setValue('FECHA DE INGRESO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('G7', function ($cell) {
                    $cell->setValue('RESPONSABLE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('H7', function ($cell) {
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $sheet->cell('I7', function ($cell) {
                    $cell->setValue('OBSERVACIÓN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });

                $nro = 0;
                $i   = 8;
                foreach ($agendas as $value) {

                    $historia = Historiaclinica::where('id_agenda', $value->id)->first();

                    if (!is_null($historia)) {

                        $hSeguro = Seguro::find($historia->id_seguro);
                    }

                    if ($value->estado_cita == '4') {

                        if ($value->proc_consul == '1') {
                            $nro = $nro + 1;

                            $sheet->setHeight(array(
                                $i => 15,
                            ));

                            $sheet->cell('B' . $i, function ($cell) use ($value, $nro) {
                                // manipulate the cel
                                $cell->setValue($nro);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('C' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue('PROCEDIMIENTO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('D' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                if ($value->papellido2 != "(N/A)") {
                                    $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                                } else {
                                    $vnombre = $value->papellido1;
                                }

                                if ($value->pnombre2 != "(N/A)") {
                                    $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                                } else {
                                    $vnombre = $vnombre . ' ' . $value->pnombre1;
                                }

                                $cell->setValue($vnombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('E' . $i, function ($cell) use ($value) {

                                $cell->setValue(substr($value->id_paciente, 5, 5));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('F' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue(substr($value->fechaini, 0, 10));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });

                            $sheet->cell('G' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->aunombre1 . ' ' . $value->auapellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('H' . $i, function ($cell) use ($value, $hSeguro) {
                                // manipulate the cel
                                if (!is_null($hSeguro)) {
                                    $cell->setValue($hSeguro->nombre);
                                } else {
                                    $cell->setValue($value->senombre);
                                }

                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('I' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->observaciones);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $i = $i + 1;
                        }
                    }
                }
            });
        })->export('xlsx');
    }

    //cambios 08052018
    public function reporteagenda2(Request $request)
    {
        //return date_default_timezone_get();
        setlocale(LC_ALL, 'Spanish_Ecuador');
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->get(); //3=DOCTORES

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguro = seguro::all();
        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        if ($request['doctor'] == '') {
            $doctor = '1314490929';
        } else {
            $doctor = $request['doctor'];
        }

        $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            a.id_doctor1 = '" . $doctor . "' AND
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

        return view('reportes/agenda-diario/indexdrh2', ['procedimientos' => $agenda2, 'fecha' => $fecha, 'seguros' => $seguro, 'dp_proc' => $dp_proc, 'doctor' => $doctor, 'users' => $users]);
    }

    public function excel2(Request $request)
    {

        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        if ($request['doctor'] == '') {
            $doctor = '1314490929';
        } else {
            $doctor = $request['doctor'];
        }

        Excel::create('Reporte Doctor-' . $fecha, function ($excel) use ($fecha, $doctor) {

            $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2,d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.fecha_nacimiento as pfecha_nacimiento
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            a.id_doctor1 = '" . $doctor . "' AND
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

            $excel->sheet(
                'Reporte proc-dr',
                function ($sheet) use ($agenda2, $fecha, $dp_proc) {
                    $i = 6;
                    $sheet->mergeCells('A2:P2');
                    $sheet->cell('A2', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('AGENDAMIENTO DE PROCEDIMIENTOS');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:P3');
                    $mes = substr($fecha, 5, 2);
                    if ($mes == 01) {
                        $mes_letra = "ENERO";
                    }
                    if ($mes == 02) {
                        $mes_letra = "FEBRERO";
                    }
                    if ($mes == 03) {
                        $mes_letra = "MARZO";
                    }
                    if ($mes == 04) {
                        $mes_letra = "ABRIL";
                    }
                    if ($mes == 05) {
                        $mes_letra = "MAYO";
                    }
                    if ($mes == 06) {
                        $mes_letra = "JUNIO";
                    }
                    if ($mes == 07) {
                        $mes_letra = "JULIO";
                    }
                    if ($mes == '08') {
                        $mes_letra = "AGOSTO";
                    }
                    if ($mes == '09') {
                        $mes_letra = "SEPTIEMBRE";
                    }
                    if ($mes == '10') {
                        $mes_letra = "OCTUBRE";
                    }
                    if ($mes == '11') {
                        $mes_letra = "NOVIEMBRE";
                    }
                    if ($mes == '12') {
                        $mes_letra = "DICIEMBRE";
                    }
                    $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);
                    $sheet->cell('A3', function ($cell) use ($fecha2) {
                        // manipulate the cel
                        $cell->setValue($fecha2);
                        $cell->setBackground('#FFFF00');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A4:P4');
                    $sheet->cell('A4', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('PROCEDIMIENTOS MÉDICOS');
                        $cell->setBackground('#FFE4E1');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A1:P4', function ($cells) {
                        // manipulate the range of cells
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('A5:P5', function ($cells) {
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
                        $cell->setValue('DCTOS. PENDIENTES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('P5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('ESTADO');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    foreach ($agenda2 as $value) {
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
                                } elseif ($value->estado_cita == '-1') {
                                    $cell->setValue('NO ASISTE');
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
                                $cell->setValue('HOSPITALIZADO');
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
                            if ($value->estado_cita == '0') {
                                $cell->setValue('POR CONFIRMAR');
                            } elseif ($value->estado_cita == '1') {
                                $cell->setValue('CONFIRMADO');
                            } elseif ($value->estado_cita == '-1') {
                                $cell->setValue('NO ASISTE');
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
                        $i = $i + 1;
                    }
                }

            );
        })->export('xlsx');
    }

    public function log_agenda($id)
    {

        /*if($this->rol()){
        return response()->view('errors.404');
        }*/

        $logs = DB::table('log_agenda as l')->where('l.id_agenda', $id)->join('users as u', 'u.id', 'l.id_usuariocrea')->select('l.*', 'u.nombre1', 'u.apellido1')->get();

        return view('consultam/log_agenda', ['logs' => $logs]);
    }
    public function control_consultas(Request $request)
    {

        //dd($request->all());
        $proc_consul      = $request['proc_consul'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $id_procedimiento = $request['id_procedimiento'];

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as uc', 'uc.id', '=', 'agenda.id_usuariocrea')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'uc.apellido1 as ucapellido', 'uc.nombre1 as ucnombre', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'paciente.id_usuario')
            ->orderby('agenda.fechaini', 'desc')
            ->where(function ($query) {
                $query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');
            });

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('agenda.id_seguro', $id_seguro);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }

        $agendas = $agendas->get();

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }
        }
        //dd($agendas);

        $fecha_d = date('Y/m/d');
        Excel::create('Control Consultas-' . $fecha_d, function ($excel) use ($agendas, $fecha) {

            $excel->sheet('Cpntrol Consultas', function ($sheet) use ($agendas, $fecha) {
                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONTROL CONSULTAS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS DE LA AGENDA');
                    $cell->setBackground('#80ccff');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A3', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A4:J4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N°');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA AGENDADA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LLEG./ADM');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ENT.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SAL.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $n = 1;
                $i = 5;

                foreach ($agendas as $value) {
                    $historiaclinica = Historiaclinica::where('id_agenda', $value->id)->first();
                    $txt_pro         = '';
                    if (!is_null($historiaclinica)) {
                        $evolucion = Hc_Evolucion::where('hcid', $historiaclinica->hcid)->where('secuencia', 0)->first();
                        if (!is_null($evolucion)) {
                            $orden = Orden::where('id_evolucion', $evolucion->id)->first();
                            if (!is_null($orden)) {
                                $orden_tipo = $orden->orden_tipo;
                                foreach ($orden_tipo as $ot) {
                                    $orden_proc = $ot->orden_procedimiento;
                                    foreach ($orden_proc as $op) {
                                        $procedimiento = $op->procedimiento;
                                        if (!is_null($procedimiento)) {
                                            if ($txt_pro == '') {
                                                $txt_pro = $procedimiento->nombre;
                                            } else {
                                                $txt_pro = $txt_pro . '+' . $procedimiento->nombre;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //dd($value);

                    $empresa = null;
                    if ($value->id_empresa != null) {
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $txtcolor = '#000000';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($n, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($n);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($value->proc_consul == '0') {
                        $vproc = 'CONSULTA';

                        $sheet->cell('E' . $i, function ($cell) use ($value, $vproc, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue($vproc);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue(substr($value->created_at, 11, 5));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $hc_seguro = null;
                        $hc        = null;
                        $hc_proc   = null;

                        $hc = Historiaclinica::where('id_agenda', $value->id)->first();

                        if (!is_null($hc)) {

                            $hc_proc = hc_procedimientos::where('id_hc', $hc->hcid)->first();
                        }

                        if (!is_null($hc_proc)) {
                            if ($hc_proc->id_seguro != null) {
                                $hc_seguro = Seguro::find($hc_proc->id_seguro)->nombre;
                            }
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 11, 5));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });
                        $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechafin, 11, 5));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor, $historiaclinica, $empresa, $hc_seguro) {
                            // manipulate the cel
                            $consultorio = '';
                            if ($value->consultorio != '0') {
                                $consultorio = '/CONSULTORIO';
                            }

                            if (is_null($historiaclinica)) {
                                $cell->setValue($value->senombre . $consultorio);
                            } else {

                                if ($value->omni == 'OM') {

                                    $cell->setValue($hc_seguro);
                                } else {

                                    $cell->setValue($value->senombre);
                                }
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });
                        $n++;
                        $i++;
                    }
                }
            });
        })->export('xlsx');
    }
    public function tiempoReporte(Request $request)
    {
        //consulta proncipal
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $analisis = $request['analisis'];

        $datos       = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->leftjoin('reservaciones_turno as rt', 'agenda.id', '=', 'rt.id_agenda')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*','hc.created_at as hora_admision','rt.created_at as hora_turno')
            ->get();

        //dd($datos);
        //  ->select('hc.id_paciente', 'p.nombre1' , 'p.nombre2' , 'p.apellido1' , 'p.apellido2', 'agenda.fechaini', 'hcp.hora_inicio')->get();

        //dd(count($datos));
        if (count($datos) > 0) {
            //  dd($datos);
            Excel::create('REPORTE DE TIEMPO' . ' ' . $fecha_desde . ' - ' . $fecha_hasta, function ($excel) use ($empresa, $datos, $fecha_desde, $fecha_hasta,$analisis) {

                $excel->sheet('REPORTE DE TIEMPO', function ($sheet) use ($empresa, $datos, $fecha_desde, $fecha_hasta,$analisis) {

                    $sheet->mergeCells('C1:F1');
                    $sheet->cell('C1', function ($cell) use ($empresa) {
                        $cell->setValue($empresa->razonsocial);
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#FFFFFF');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });
                    if ($empresa->logo != null) {
                        $sheet->mergeCells('A1:B1');
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        if(file_exists(base_path() . '/storage/app/logo/logo1391707460001.png')){
                            $objDrawing->setPath(base_path() . '/storage/app/logo/logo1391707460001.png');
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setHeight(220);
                            $objDrawing->setWidth(120);
                            $objDrawing->setWorksheet($sheet);
                        }
                    }

                    $sheet->mergeCells('C2:F2');
                    $sheet->cell('C2', function ($cell) use ($fecha_desde, $fecha_hasta) {
                        $cell->setValue('Fecha desde:' . ' ' . $fecha_desde . ' - ' . 'Fecha Hasta:' . $fecha_hasta);
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#FFFFFF');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });

                    $sheet->cell('A3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha Agenda');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cedula');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Paciente');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Doctor');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de toma de turno');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de admisión');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Análisis');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de Entrada a la Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fin Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Tiempo de Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Tiempos de Espera');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i     = 4;
                    $fecha = date("Y-m-d H:i:s");
                    $fechi = date("d H:i:s", strtotime("01 00:00:00"));

                    $contr = 0;                    
                    $total = 0;
                    foreach ($datos as $value) {
                        $horainiciomostrar = date_format(new DateTime($value->hora_inicio), 'H:i:s');
                        
                        if(!is_null($value->hora_turno)){
                            $hora_turno = date_format(new DateTime($value->hora_turno), 'H:i:s');
                        }
                        else{
                            $hora_turno = date_format(new DateTime('00:00'), 'H:i:s');
                        }

                        $fechaini = date_format(new DateTime($value->fechaini), 'Y-m-d');
                        $horaini = date_format(new DateTime($value->fechaini), 'H:i:s');
                        $fechainimostrar = $horaini;
                        if($analisis == 1){
                            $condicion = $hora_turno < $fechainimostrar;//horainiciomostrar
                        }
                        else if($analisis == 2){
                            $condicion = $hora_turno > $fechainimostrar;
                        }
                        else{
                            $condicion = $hora_turno != $fechainimostrar;
                        }
                        //dd($condicion);
                        if($condicion){
                            //$fechaini = explode(' ', $value->fechaini);
                            
                            //$duracion = strtotime($value->hora_inicio) - strtotime($fechaini[1]);
                            $duracion = strtotime($value->hora_inicio) - strtotime($horaini);
                            $duracion = round($duracion * 100 / 60) / 100;
                            $suma     = 0;
                            if ($duracion > 0) {
                                $total = $duracion + $total;
                                $suma  = $duracion;
                            }
                            
                            $dias_total = date("H:i", strtotime("00:00"));
                            $dias_total = date("H:i", strtotime("+" . intval($suma) . " minutes", strtotime($dias_total)));
                            $tiempo_espera = 0;
                            //$fechainimostrar = explode(':', $fechaini[1]);
                            
                            $tiempo_consulta = '';
                            if(!is_null($value->hora_fin)){
                                $fin = new DateTime($value->hora_fin);
                                $ini = new DateTime($value->hora_inicio);
                                $tiempo_consulta = $fin->diff($ini);
                                $tiempo_consulta = str_pad($tiempo_consulta->h,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->i,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->s,2,'0',STR_PAD_LEFT);
                            }
                            if (is_null($value->hora_inicio)) {

                            }
                            // $horainiciomostrar = explode(':', $value->hora_inicio);
                            // dd($horainiciomostrar);
                            // if (!is_null($value->hora_fin)) {
                            //     $horafinmostrar = explode(':', $value->hora_fin);
                            // }
                            //dd($horafinmostrar);

                            $sheet->cell('A' . $i, function ($cell) use ($fechaini) {
                                $cell->setValue($fechaini);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });

                            $sheet->cell('B' . $i, function ($cell) use ($value) {
                                $cell->setValue($value->id_paciente);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('C' . $i, function ($cell) use ($value) {
                                $cell->setValue($value->nombre1 . ' ' . $value->nombre2 . ' ' . $value->apellido1 . ' ' . $value->apellido2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('D' . $i, function ($cell) use ($value) {
                                $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('E' . $i, function ($cell) use ($fechainimostrar) {
                                //$cell->setValue($fechainimostrar[0] . ':' . $fechainimostrar[1]);
                                $cell->setValue($fechainimostrar);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            
                            $sheet->cell('F' . $i, function ($cell) use ($hora_turno) {
                                $cell->setValue($hora_turno);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            
                            $sheet->cell('G' . $i, function ($cell) use ($value) {
                                $hora_admision =  date_format(new DateTime($value->hora_admision), 'H:i:s');
                                $cell->setValue($hora_admision);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('H' . $i, function ($cell) use ($hora_turno,$fechainimostrar) {
                                if($hora_turno < $fechainimostrar){
                                    $cell->setValue('SI');
                                }
                                else{
                                    $cell->setValue('NO');
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            
                            $sheet->cell('I' . $i, function ($cell) use ($horainiciomostrar) {
                                //$cell->setValue($horainiciomostrar[0] . ':' . $horainiciomostrar[1]);
                                $cell->setValue($horainiciomostrar);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            if (!is_null($value->hora_fin)) {
                                $horafinmostrar = date_format(new DateTime($value->hora_fin), 'H:i:s');
                                $sheet->cell('J' . $i, function ($cell) use ($horafinmostrar) {
                                    //$cell->setValue($horafinmostrar[0] . ':' . $horafinmostrar[1]);
                                    $cell->setValue($horafinmostrar);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            } else {
                                $sheet->cell('J' . $i, function ($cell) {
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            }
                            $sheet->cell('K' . $i, function ($cell) use ($tiempo_consulta) {
                                $cell->setValue($tiempo_consulta);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $sheet->cell('L' . $i, function ($cell) use ($dias_total) {
                                $cell->setValue($dias_total);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                            $i++;
                            $contr++;
                        }
                    }
                    $i++;
                    //dd($duracion);

                    $promedio_min = date("H:i", strtotime("00:00"));

                    $promedio      = 0;
                    $promedio_dias = 0;
                    // dd($total);

                    if ($total > 0) {
                        $promedio      = $total / count($datos);
                        $promedio_dias = $promedio / 720;
                    }

                    $promedio_min = date("H:i", strtotime("+" . intval($promedio) . " minutes", strtotime($promedio_min)));
                    // dd($promedio_min);

                    $sheet->cell('A' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Promedio de Espera');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($promedio_min, $promedio_dias) {
                        $cell->setValue($promedio_min);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                });
            })->export('xlsx');
        } else {
            return "No existen datos por mostrar";
        }
    }

    public function excel_reporte(Request $request)
    {
        $agenda = Agenda::where('estado', '1')->$datos  = DB::table('historiaclinica as hc')
            ->join('agenda as ag', 'ag.id', 'hc.id_agenda')
            ->join('pentax as pe', 'pe.id_agenda', 'ag.id')
            ->join('orden as or', 'hc.id_paciente', 'or.id_paciente')
            ->join('seguros as se', 'hc.id_seguro', 'se.id')
            ->join('paciente as p', 'hc.id_paciente', 'p.id')
            ->join('pentax_procedimiento as pp', 'pp.id_pentax', 'pe.id')
            ->join('procedimiento as proc', 'pp.id_procedimiento', 'proc.id')
            ->whereBetween('ag.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            // ->groupBy('or.id')
            ->select(
                'hc.hcid as idhistoria',
                'ag.fechaini',
                'hc.id_paciente',
                'p.nombre1',
                'p.nombre2',
                'p.apellido1',
                'p.apellido2',
                'se.nombre',
                'proc.nombre as procedimiento',
                'or.id as nrorden',
                'se.nombre as nombre_seg',
                'se.tipo as tipo_seg'
            )->get();

        //dd($datos);

        $titulos = array(
            "FECHA", "NOMBRE DEL PACIENTE", "PROCEDIMIENTOS", "SEGURO", "ORIGEN", "COMENTARIO", "ORDEN", "C.I. PCTE", "C.I. AFIL",
            "C.I. FAMIL.", "CARNET", "APORT", "CARTA COMUNA", "FORMUL. 053", "FORML. 012", "COBERTURA", "SEGURO PRIVADO", "CADUCA ORDEN", "RECORD ANAST"
        );
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S");

        Excel::create('Reporte', function ($excel) use ($titulos, $posicion, $datos) {
            $excel->sheet('Reporte Master', function ($sheet) use ($titulos, $posicion, $datos) {
                $comienzo = 1; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                $seguro   = "X";

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                foreach ($datos as $value) {
                    $ci_paciente  = "";
                    $ci_afiliado  = "";
                    $aportaciones = "";
                    $datos_excel  = array();

                    $archivo = DB::table('archivo_historico as ah')
                        ->join('documento as dc', 'dc.id', 'ah.id_documento')
                        ->where('ah.id_historia', $value->idhistoria)
                        ->select('ah.descripcion', 'ah.estado as estado_doc', 'ah.id_documento as documento_id')
                        ->get();
                    //dd($archivo);

                    foreach ($archivo as $doc) {
                        if ($doc->documento_id == 1 && $doc->estado_doc == 1) {
                            $ci_paciente = "X";
                        }
                        if ($doc->documento_id == 2 && $doc->estado_doc == 1) {
                            $ci_afiliado = "X";
                        }
                        if ($doc->documento_id == 5 && $doc->estado_doc == 1) {
                            $aportaciones = "X";
                        }
                    }

                    if ($value->tipo_seg == 1) {
                        $seguro = $value->nombre_seg;
                    }
                    $nombre_pac = "";
                    $nombre_pac = $value->nombre1 . " " . $value->apellido1 . " " . $value->apellido2;
                    array_push(
                        $datos_excel,
                        $value->fechaini,
                        $nombre_pac,
                        $value->procedimiento,
                        $value->nombre,
                        "Origen",
                        "Comentario",
                        $value->nrorden,
                        $ci_paciente,
                        $ci_afiliado,
                        'CI FAMIL',
                        'CARNET',
                        $aportaciones,
                        'CARTA COMUNA',
                        'form 053',
                        'form 012',
                        'cobertur',
                        $seguro
                    );

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    public function reporte_fellows($id_fellow)
    {

        $procedimientos = Hc_protocolo_training::where('id_training', $id_fellow)->get();
        //dd($procedimientos);
        $user = User::find($id_fellow);

        $titulos = array("FECHA", "PACIENTE", "PROCEDIMIENTOS AGENDADOS", "PROCEDIMIENTOS DOCTOR", "CONCLUSION");

        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S");

        Excel::create('Reporte_' . $user->apellido1 . ' ' . $user->nombre1, function ($excel) use ($titulos, $posicion, $procedimientos) {
            $excel->sheet('Reporte Master', function ($sheet) use ($titulos, $posicion, $procedimientos) {
                $comienzo = 1; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                foreach ($procedimientos as $value) {
                    $protocolo        = hc_protocolo::find($value->id_hc_protocolo);
                    $f_procedimientos = Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->get();
                    $historia         = $protocolo->historiaclinica;
                    $pentax           = $historia->pentax;
                    $procedimientos   = $pentax->procedimientos;
                    $conclusion       = strip_tags($protocolo->conclusion);

                    $fecha                = $historia->agenda->fechaini;
                    $npaciente            = $historia->paciente->apellido1 . ' ' . $historia->paciente->apellido2 . ' ' . $historia->paciente->nombre1 . ' ' . $historia->paciente->nombre2;
                    $txtprocedimientos    = '';
                    $fn_txt_procedimiento = '';

                    foreach ($f_procedimientos as $procedimiento) {
                        $fn_txt_procedimiento = $fn_txt_procedimiento . '+' . $procedimiento->procedimiento->nombre;
                    }

                    foreach ($procedimientos as $procedimiento) {
                        $txtprocedimientos = $txtprocedimientos . '+' . $procedimiento->procedimiento->nombre;
                    }

                    $datos_excel = [];
                    array_push($datos_excel, $fecha, $npaciente, $txtprocedimientos, $fn_txt_procedimiento, $conclusion);

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    //INDEX REPORTE FELLOWS

    public function index_rfellows()
    {
        $rfellows = User::where('training', '1')->get();
        return view('consultam/reporte_fellows/index', ['rfellow' => $rfellows]);
    }
    public function descargar_rfellows(Request $request)
    {
        //dd($request->all());
        $id_fellow = $request['menu'];
        //dd($id_fellow);
        $procedimientos = Hc_protocolo_training::where('id_training', $id_fellow)->get();
        //dd($procedimientos);
        $user = User::find($id_fellow);
        // dd($user);

        $titulos = array("FECHA", "PACIENTE", "PROCEDIMIENTOS AGENDADOS", "PROCEDIMIENTOS DOCTOR", "CONCLUSION");

        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S");

        Excel::create('Reporte_' . $user->apellido1 . ' ' . $user->nombre1, function ($excel) use ($titulos, $posicion, $procedimientos) {
            $excel->sheet('Reporte Master', function ($sheet) use ($titulos, $posicion, $procedimientos) {
                $comienzo = 1; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                foreach ($procedimientos as $value) {
                    $protocolo        = hc_protocolo::find($value->id_hc_protocolo);
                    $f_procedimientos = Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->get(); //dd($f_procedimientos);
                    $historia         = $protocolo->historiaclinica;
                    $pentax           = $historia->pentax;
                    $procedimientos   = $pentax->procedimientos;
                    $conclusion       = strip_tags($protocolo->conclusion);

                    $fecha                = $historia->agenda->fechaini;
                    $npaciente            = $historia->paciente->apellido1 . ' ' . $historia->paciente->apellido2 . ' ' . $historia->paciente->nombre1 . ' ' . $historia->paciente->nombre2;
                    $txtprocedimientos    = '';
                    $fn_txt_procedimiento = '';

                    foreach ($f_procedimientos as $procedimiento) {
                        $fn_txt_procedimiento = $fn_txt_procedimiento . '+' . $procedimiento->procedimiento->nombre;
                    }

                    foreach ($procedimientos as $procedimiento) {
                        $txtprocedimientos = $txtprocedimientos . '+' . $procedimiento->procedimiento->nombre;
                    }

                    $datos_excel = [];
                    array_push($datos_excel, $fecha, $npaciente, $txtprocedimientos, $fn_txt_procedimiento, $conclusion);

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    public function reporte_estados(Request $request)
    {

        $proc_consul      = $request['proc_consul'];
        //$empresa          = $request['id_empresa'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $pentax           = $request['pentax'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $id_procedimiento = $request['id_procedimiento'];
        //$procedimiento_final 

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        /*$agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')
        ->join('seguros','seguros.id','=','agenda.id_seguro')
        ->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')
        ->join('users as uc','uc.id','=','agenda.id_usuariocrea')
        ->join('users as au','au.id','=','agenda.id_usuariomod')
        ->leftjoin('sala','sala.id','=','agenda.id_sala')
        ->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')
        ->leftjoin('hospital','hospital.id','=','sala.id_hospital')
        ->leftjoin('pentax','pentax.id_agenda','agenda.id')
        ->leftjoin('seguros as seguro_pentax','seguro_pentax.id','=','pentax.id_seguro')
        ->leftjoin('users as dp1','dp1.id','=','pentax.id_doctor1')
        ->leftjoin('users as d2','d2.id','pentax.id_doctor2')
        ->leftjoin('users as d3','d3.id','pentax.id_doctor3')
        ->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','uc.apellido1 as ucapellido','uc.nombre1 as ucnombre','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor','dp1.nombre1 as dp1nombre1','dp1.apellido1 as dp1apellido1','pentax.id as pxid','d2.apellido1 as d2apellido1','d3.apellido1 as d3apellido1','pentax.estado_pentax','pentax.ingresa_alt','paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax','paciente.direccion','paciente.telefono1','paciente.telefono2','paciente.telefono3','paciente.parentesco','paciente.id_usuario','paciente.referido')
        ->orderby('agenda.fechaini','desc')
        ->where('proc_consul','<','2');*/

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as uc', 'uc.id', '=', 'agenda.id_usuariocrea')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')
            ->leftjoin('seguros as seguro_pentax', 'seguro_pentax.id', '=', 'pentax.id_seguro')
            ->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')
            ->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')
            ->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')
            ->where('agenda.id_doctor1', '<>', '4444444444')
            ->where("agenda.estado_cita",3)
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.fecha_nacimiento as fech_nacimiento', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'uc.apellido1 as ucapellido', 'uc.nombre1 as ucnombre', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'paciente.parentesco', 'paciente.id_usuario', 'paciente.referido', 'pentax.id_anestesiologo', 'paciente.origen', 'paciente.origen2', 'paciente.otro')
            ->orderby('agenda.fechaini', 'desc')
            ->where(function ($query) {
                $query->where('proc_consul', '=', '1');
            });

        if ($proc_consul == '1') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }


        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }


        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }


        $agendas = $agendas->get();


        $cuenta_agenda = count($agendas);
        //dd($cuenta_agenda);

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        $cuenta_suspendido = 0;



        foreach ($agendas as $a2) {

            //  dd($a2);

            if ($a2->estado_cita == '3') {
                $cuenta_suspendido = $cuenta_suspendido + 1;
            }


            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }

            //dd($agendas);

            //dd($cuenta_suspendido);

        }


        $fecha_d = date('Y/m/d');
        Excel::create('Procedimientos_Suspendidos' . $fecha_d, function ($excel) use ($agendas, $fecha) {


            $excel->sheet('Procedimientos Suspendidos', function ($sheet) use ($agendas, $fecha) {

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:A3');
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/logo1793135579001.png');
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(100);
                $objDrawing->setWidth(120);
                $objDrawing->setWorksheet($sheet);


                $sheet->mergeCells('B1:J3');
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('REPORTE DE PROCEDIMIENTOS SUSPENDIDOS');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('23');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K1:L3');
                $sheet->cell('K1', function ($cell) {
                    $cell->setValue('Ver. 0.1');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('23');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i       = 5;

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);


                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DEL PROCEDIMIENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EMPRESA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO AGENDADO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO FINAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO PENTAX 1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO FIRMA ESTUDIO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO FINAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MODIFICA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function ($cell) {
                    $cell->setValue('CONSECUTIVO/PRIMERA VEZ');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function ($cell) {
                    $cell->setValue('ULTIMA OBSERVACION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });



                foreach ($agendas as $value) {


                    $historiaclinica = Historiaclinica::where('id_agenda', $value->id)->first();

                    $txt_pro         = '';
                    if (!is_null($historiaclinica)) {
                        $evolucion = Hc_Evolucion::where('hcid', $historiaclinica->hcid)->where('secuencia', 0)->first();
                        if (!is_null($evolucion)) {
                            $orden = Orden::where('id_evolucion', $evolucion->id)->first();
                            if (!is_null($orden)) {
                                $orden_tipo = $orden->orden_tipo;
                                foreach ($orden_tipo as $ot) {
                                    $orden_proc = $ot->orden_procedimiento;
                                    foreach ($orden_proc as $op) {
                                        $procedimiento = $op->procedimiento;
                                        if (!is_null($procedimiento)) {
                                            if ($txt_pro == '') {
                                                $txt_pro = $procedimiento->nombre;
                                            } else {
                                                $txt_pro = $txt_pro . '+' . $procedimiento->nombre;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $empresa = null;
                    if ($value->id_empresa != null) {
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $txtcolor = '#000000';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($value->proc_consul == '0') {
                        $vproc = 'CONSULTA';
                    } else {
                        if ($value->proc_consul == '1') {

                            if (!is_null($value->probservacion)) {
                                $vproc = $value->probservacion;
                            }

                            $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                            if (!$agprocedimientos->isEmpty()) {
                                foreach ($agprocedimientos as $agendaproc) {
                                    $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                                }
                            }
                        } else {

                            if ($value->observaciones == 'EVOLUCION CREADA POR EL DOCTOR') {
                                if ($value->omni == 'OM') {
                                    $vproc = 'VISITA OMNI';
                                } else {
                                    $vproc = 'VISITA';
                                }
                            }
                        }
                    }

                    $sheet->cell('D' . $i, function ($cell) use ($empresa) {
                        // manipulate the cel
                        if(!is_null($empresa)){
                            $cell->setValue($empresa->nombrecomercial);
                        }else{
                            $cell->setValue("");
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $vproc) {
                        // manipulate the cel
                        $cell->setValue($vproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $nombre_proc = '';
                    //dd($historiaclinica);
                    if (!is_null($historiaclinica)) {
                        $hc_procedimientos = hc_procedimientos::where('id_hc', $historiaclinica->hcid)->first();

                        //dd($hc_procedimientos);      

                        if (!is_null($hc_procedimientos)) {
                            $proc_fin = $hc_procedimientos->hc_procedimiento_f;

                            // dd($proc_fin, $nombre_proc);
                            if (count($proc_fin) > 0) {
                                //  dd("sffd");
                                foreach ($proc_fin as $pf) {
                                    $procedimiento = Procedimiento::where('id', $pf->id_procedimiento)->first();
                                    $nombre_proc = "{$procedimiento->nombre} + {$nombre_proc}";
                                }
                            }
                        }
                    }



                    $sheet->cell('F' . $i, function ($cell) use ($nombre_proc) {
                        // manipulate the cel
                        $cell->setValue($nombre_proc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {

                        if ($value->proc_consul == 0) {
                            $historia = historiaclinica::where('id_agenda', '=', $value->id)->first();
                            if (!is_null($historia)) {
                                $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
                                if (!is_null($procedimiento)) {
                                    // manipulate the cel
                                    if (!is_null($procedimiento->id_doctor_examinador)) {
                                        $cell->setValue($procedimiento->doctor->nombre1 . ' ' . $procedimiento->doctor->apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    } else {
                                        // manipulate the cel
                                        $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    }
                                } else {
                                    // manipulate the cel
                                    $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                }
                            } else {
                                // manipulate the cel
                                $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        } else {
                            // manipulate the cel
                            $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });

                    $vest = null;
                    if ($value->omni == 'OM') {
                        if ($value->estado_cita == '4') {
                            $vest = 'INGRESO';
                        } elseif ($value->estado_cita == '5') {
                            $vest = 'ALTA';
                        } elseif ($value->estado_cita == '6') {
                            $vest = 'EMERGENCIA';
                        }
                    } else {
                        if ($value->estado_cita == '0') {
                            $vest = 'Por Confirmar';
                        } elseif ($value->estado_cita == '1') {
                            $vest = 'Confirmado';
                        } elseif ($value->estado_cita == '3') {
                            $vest = 'Suspendido';
                        } elseif ($value->estado_cita == '-1') {
                            $vest = 'No Asiste';
                        } elseif ($value->estado_cita == '4') {
                            $vest = 'Asistió';
                        } elseif ($value->estado_cita == '2') {
                            if ($value->estado == '1') {
                                $vest = 'Completar Datos';
                            } else {
                                $vest = 'Reagendar';
                            }
                        }
                    }
                    $hc_seguro = null;
                    $hc        = null;
                    $hc_proc   = null;

                    $hc = Historiaclinica::where('id_agenda', $value->id)->first();

                    if (!is_null($hc)) {

                        $hc_proc = hc_procedimientos::where('id_hc', $hc->hcid)->first();
                    }

                    if (!is_null($hc_proc)) {
                        if ($hc_proc->id_seguro != null) {
                            $hc_seguro = Seguro::find($hc_proc->id_seguro)->nombre;
                        }
                    }

                    $sheet->cell('I' . $i, function ($cell) use ($value, $historiaclinica, $empresa, $hc_seguro) {
                        // manipulate the cel
                        $consultorio = '';
                        if ($value->consultorio != '0') {
                            $consultorio = '/CONSULTORIO';
                        }

                        if (is_null($historiaclinica)) {
                            $cell->setValue($value->senombre . $consultorio);
                        } else {

                            if ($value->omni == 'OM') {

                                $cell->setValue($hc_seguro);
                            } else {

                                $cell->setValue($value->senombre);
                            }
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });



                    $sheet->cell('J' . $i, function ($cell) use ($value, $vest) {
                        // manipulate the cel
                        $cell->setValue($vest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->aunombre1 . ' ' . $value->auapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $vcita = "";
                    if ($value->tipo_cita == '0') {
                        $vcita = 'PRIMERA VEZ';
                    } else {
                        $vcita = 'CONSECUTIVO';
                    }
                    $sheet->cell('L' . $i, function ($cell) use ($value, $vcita) {
                        // manipulate the cel
                        $cell->setValue($vcita);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });




                    $i = $i + 1;
                }

                $i = $i + 2;

                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('TOTAL PROCEDIMIENTOS AGENDADOS:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $cuenta_agenda = count($agendas);
                $sheet->cell('C' . $i, function ($cell) use ($cuenta_agenda) {
                    $cell->setValue($cuenta_agenda);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $i = $i + 1;

                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('TOTAL PROCEDIMIENTOS SUSPENDIDOS:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cuenta_suspendido = 0;
                foreach ($agendas as $a2) {
                    //  dd($a2);

                    if ($a2->estado_cita == '3') {
                        $cuenta_suspendido = $cuenta_suspendido + 1;
                    }
                }
                $sheet->cell('C' . $i, function ($cell) use ($cuenta_suspendido) {
                    $cell->setValue($cuenta_suspendido);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public function reporte_insumos_procedimientos($id)
    {
        $protocolo     = hc_protocolo::find($id);
        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);

        // facturado 
        $planilla_cabecera = Planilla_Cabecera::where('id_hc_procedimiento',$procedimiento->id)->first();
        $detalles = array();
        if(!is_null($planilla_cabecera)){
            $detalles = Planilla_Detalle::where('id_planilla_cabecera',$planilla_cabecera->id)->get();
        }

        Excel::create('Insumos Utilizados', function ($excel) use ($detalles, $procedimiento) {
            $excel->sheet('Proyeccion de Venta', function ($sheet) use ($detalles, $procedimiento) {

                $fecha_d = date('Y/m/d');
                 $sheet->mergeCells('A2:F2');
                    $sheet->cell('A2', function ($cell){
                        $cell->setValue('REPORTE DE PROYECCION DE VENTAS');
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#FFFFFF');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('23');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                $i = 5;

                $mes = substr($fecha_d, 5, 2);
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
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);


                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRODUCTO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PUBLICO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('f4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'E' => '$### ### ### ##0.00',
                    'F' => '$### ### ### ##0.00',
                ));

                foreach ($detalles as $value) {                    
                    //insumo
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        if(!is_null($value->producto)){
                            $cell->setValue($value->producto->codigo . ' / ' . $value->producto->nombre);
                        }else{
                            $cell->setValue('');
                        }
                        // manipulate the cel
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //producto
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        $ct_producto = Ct_productos::find($value->id_ct_producto);
                        // manipulate the cel
                        if(!is_null($ct_producto)){
                            $cell->setValue($ct_producto->codigo . ' / ' . $ct_producto->nombre);
                        }else {
                            $cell->setValue('');
                        }
                        
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Publico
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        $ap_proc = ApProcedimiento::find($value->id_ap_procedimiento);
                        // manipulate the cel
                        if(!is_null($ap_proc)){ 
                            $cell->setValue($ap_proc->descripcion);
                        }
                        else{
                            $cell->setValue('');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    //cantidad
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setAlignment('center');
                        $cell->setValue($value->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    //valor
                    $sheet->cell('E' . $i, function ($cell) use ( $value) {
                        // manipulate the cel
                        $cell->setValue(number_format($value->valor_ap_procedimiento,2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    //total
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(number_format($value->total_proyeccion,2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i = $i + 1;
                }
            });
        })->export('xlsx');
    }
    public function vistaReporte(Request $request)
    {
        
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $analisis = $request['analisis'];
        $agendas = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', '=', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.id as ag_id', 'hc.created_at as hora_admision', 'rt.created_at as hora_turno', 'rt.id_agenda')
            ->selectRaw("IF(rt.created_at IS NOT NULL, IF(TIME(rt.created_at) < TIME(agenda.fechaini), 'si', 'no'), 'no tiene') as analisis")
            ->leftJoin('reservaciones_turno as rt', 'agenda.id', '=', 'rt.id_agenda');
            if($analisis == 1){//si
                $agendas = $agendas->where(function ($query) {
                    $query->whereRaw("time(rt.created_at) < time(agenda.fechaini)")
                          ->orWhereNull('rt.created_at');
                });
            }
            else if($analisis == 2){//no
                $agendas = $agendas->whereRaw("TIME(rt.created_at) >= time(agenda.fechaini)");
            }
            $agendas = $agendas->paginate(30);
        return view('consultam/view_report_tiempo', ['fecha'=> $fecha_desde, 'fecha_hasta'=> $fecha_hasta, 'empresa'=> $empresa, 'agendas'=> $agendas, 'analisis' => $analisis]);
    }

    public function searchReporte(Request $request)
    {
        ini_set('max_execution_time',300);
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $analisis = $request['analisis'];
        $agendas = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', '=', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.id as ag_id', 'hc.created_at as hora_admision', 'rt.created_at as hora_turno', 'rt.id_agenda')
            ->selectRaw("IF(rt.created_at IS NOT NULL, IF(TIME(rt.created_at) < TIME(agenda.fechaini), 'si', 'no'), 'no tiene') as analisis")
            ->leftJoin('reservaciones_turno as rt', 'agenda.id', '=', 'rt.id_agenda');
            if($analisis == 1){//si
                $agendas = $agendas->where(function ($query) {
                    $query->whereRaw("time(rt.created_at) < time(agenda.fechaini)")
                          ->orWhereNull('rt.created_at');
                });
            }
            else if($analisis == 2){//no
                $agendas = $agendas->whereRaw("time(rt.created_at) >= time(agenda.fechaini)");
            }
            $agendas = $agendas->paginate(30);
        return view('consultam/view_report_tiempo', ['fecha'=> $fecha_desde, 'fecha_hasta'=> $fecha_hasta, 'empresa'=> $empresa, 'agendas'=> $agendas, 'analisis' => $analisis]);
    }

    public function vistaReporteSucursales(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $analisis = $request['analisis'];
        $agendas = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', '=', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.id as ag_id', 'hc.created_at as hora_admision')
            ->selectRaw("IF(TIME(hc.created_at) < TIME(agenda.fechaini), 'si', 'no') as analisis");
            if($analisis == 1){//si
                $agendas = $agendas->where(function ($query) {
                    $query->whereRaw("TIME(hc.created_at) < TIME(agenda.fechaini)");
                });
            }
            else if($analisis == 2){//no
                $agendas = $agendas->whereRaw("TIME(hc.created_at) >= TIME(agenda.fechaini)");
            }
            $agendas = $agendas->paginate(30);
        return view('consultam/view_report_tiempo_sucursales', ['fecha'=> $fecha_desde, 'fecha_hasta'=> $fecha_hasta, 'empresa'=> $empresa, 'agendas'=> $agendas, 'analisis' => $analisis]);
    }

    public function searchReporteSucursales(Request $request)
    {
        ini_set('max_execution_time',300);
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $analisis = $request['analisis'];
        $agendas = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', '=', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.id as ag_id', 'hc.created_at as hora_admision')
            ->selectRaw("IF(TIME(hc.created_at) < TIME(agenda.fechaini), 'si', 'no') as analisis");
            if($analisis == 1){//si
                $agendas = $agendas->where(function ($query) {
                    $query->whereRaw("TIME(hc.created_at) < TIME(agenda.fechaini)");
                });
            }
            else if($analisis == 2){//no
                $agendas = $agendas->whereRaw("TIME(hc.created_at) >= TIME(agenda.fechaini)");
            }
            $agendas = $agendas->paginate(30);
        return view('consultam/view_report_tiempo_sucursales', ['fecha'=> $fecha_desde, 'fecha_hasta'=> $fecha_hasta, 'empresa'=> $empresa, 'agendas'=> $agendas, 'analisis' => $analisis]);
    }
    public function tiempoReporteSucursales(Request $request)
    {
        //consulta proncipal
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $analisis = $request['analisis'];

        $datos = DB::table('historiaclinica as hc')
            ->join('agenda', 'agenda.id', '=', 'hc.id_agenda')
            ->join('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->join('hc_procedimientos as hcp', 'hcp.id_hc', '=', 'hc.hcid')
            ->join('paciente as p', 'hc.id_paciente', '=', 'p.id')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
            ->where('agenda.proc_consul', '0')
            ->where('espid', '!=', '8')
            ->where('agenda.id_doctor1', '!=', '1314490929')
            ->whereNotNull('hcp.hora_inicio')
            ->select('d1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'p.*', 'agenda.*', 'hcp.*', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.id as ag_id', 'hc.created_at as hora_admision')
            ->selectRaw("IF(TIME(hc.created_at) < TIME(agenda.fechaini), 'si', 'no') as analisis");
            if($analisis == 1){//si
                $datos = $datos->where(function ($query) {
                    $query->whereRaw("TIME(hc.created_at) < TIME(agenda.fechaini)");
                });
            }
            else if($analisis == 2){//no
                $datos = $datos->whereRaw("TIME(hc.created_at) >= TIME(agenda.fechaini)");
            }
            $datos = $datos->get();

        if (count($datos) > 0) {
            Excel::create('REPORTE DE TIEMPO' . ' ' . $fecha_desde . ' - ' . $fecha_hasta, function ($excel) use ($empresa, $datos, $fecha_desde, $fecha_hasta,$analisis) {

                $excel->sheet('REPORTE DE TIEMPO', function ($sheet) use ($empresa, $datos, $fecha_desde, $fecha_hasta,$analisis) {

                    $sheet->mergeCells('C1:F1');
                    $sheet->cell('C1', function ($cell) use ($empresa) {
                        $cell->setValue($empresa->razonsocial);
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#FFFFFF');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });
                    if ($empresa->logo != null) {
                        $sheet->mergeCells('A1:B1');
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        if(file_exists(base_path() . '/storage/app/logo/logo1391707460001.png')){
                            $objDrawing->setPath(base_path() . '/storage/app/logo/logo1391707460001.png');
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setHeight(220);
                            $objDrawing->setWidth(120);
                            $objDrawing->setWorksheet($sheet);
                        }
                    }

                    $sheet->mergeCells('C2:F2');
                    $sheet->cell('C2', function ($cell) use ($fecha_desde, $fecha_hasta) {
                        $cell->setValue('Fecha desde:' . ' ' . $fecha_desde . ' - ' . 'Fecha Hasta:' . $fecha_hasta);
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#FFFFFF');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });

                    $sheet->cell('A3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha Agenda');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cedula');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Paciente');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Doctor');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de admisión');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Hora de Entrada a la Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fin Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Tiempo de Consulta');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Análisis');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Tiempos de Espera');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i     = 4;

                    $contr = 0;                    
                    $total = 0;
                    $valor_analisis = 0;
                    $total_tiempo_espera = "00:00";
                    foreach ($datos as $value) {
                        $horainiciomostrar = date_format(new DateTime($value->hora_inicio), 'H:i:s');
                        $fechaini = date_format(new DateTime($value->fechaini), 'Y-m-d');
                        $horaini = date_format(new DateTime($value->fechaini), 'H:i:s');
                        $fechainimostrar = $horaini;
                        $hora_admision = date_format(new DateTime($value->hora_admision), 'H:i');
                        $hora_consulta = date_format(new DateTime($value->fechaini), 'H:i');
                        $hora_entrada_consulta = date_format(new DateTime($value->hora_inicio), 'H:i');
                        $duracion = strtotime($value->hora_inicio) - strtotime($horaini);
                        $duracion = round($duracion * 100 / 60) / 100;
                        $suma     = 0;
                        if ($duracion > 0) {
                            $total = $duracion + $total;
                            $suma  = $duracion;
                        }
                        
                        $dias_total = date("H:i", strtotime("00:00"));
                        $dias_total = date("H:i", strtotime("+" . intval($suma) . " minutes", strtotime($dias_total)));
                        $tiempo_espera = 0;
                        
                        if($hora_admision < $hora_consulta)
                        {
                            $tiempo_espera = strtotime($hora_entrada_consulta) - strtotime($hora_consulta);
                            if($tiempo_espera <= 0){
                                $tiempo_espera = 0;
                            }
                            $tiempo_espera = round($tiempo_espera * 100 / 60) / 100; 
                            $tiempo_espera = gmdate('H:i', $tiempo_espera * 60);
                        }
                        else{
                            $tiempo_espera = strtotime($hora_entrada_consulta) - strtotime($hora_admision); 
                            $tiempo_espera = round($tiempo_espera * 100 / 60) / 100;
                            $tiempo_espera = gmdate('H:i', $tiempo_espera * 60);
                        }
                        $dt1 = new DateTime($total_tiempo_espera);
                        $dt2 = new DateTime($tiempo_espera);
                        $dt0 = new DateTime('00:00');
                        $dt1_val = $dt1->diff($dt0);
                        $dt2_val = $dt2->diff($dt0);
                        $dias = $dt1_val->d + $dt2_val->d;
                        $horas = $dt1_val->h + $dt2_val->h;
                        $minutos = $dt1_val->i+ $dt2_val->i;
                        $segundos = $dt1_val->s + $dt2_val->s;
                        if($horas > 23){
                            $dias = $dias+1;
                            $horas = $horas - 60;
                        }
                        if($minutos > 59){
                            $horas = $horas+1;
                            $minutos = $minutos - 60;
                        }
                        if($segundos > 59){
                            $minutos = $minutos+1;
                            $segundos = $segundos - 60;
                        }
                        $total_tiempo_espera = sprintf("%02d:%02d", $horas, $minutos); // Formatear las horas y los minutos
                        $tiempo_consulta = '';
                        if(!is_null($value->hora_fin)){
                            $fin = new DateTime($value->hora_fin);
                            $ini = new DateTime($value->hora_inicio);
                            $tiempo_consulta = $fin->diff($ini);
                            $tiempo_consulta = str_pad($tiempo_consulta->h,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->i,2,'0',STR_PAD_LEFT) . ':' . str_pad($tiempo_consulta->s,2,'0',STR_PAD_LEFT);
                        }
                        $sheet->cell('A' . $i, function ($cell) use ($fechaini) {
                            $cell->setValue($fechaini);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->nombre1 . ' ' . $value->nombre2 . ' ' . $value->apellido1 . ' ' . $value->apellido2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($hora_consulta) {
                            $cell->setValue($hora_consulta);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($hora_admision) {
                            //$hora_admision =  date_format(new DateTime($hora_admision), 'H:i:s');
                            $cell->setValue($hora_admision);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($horainiciomostrar) {
                            $cell->setValue($horainiciomostrar);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        if (!is_null($value->hora_fin)) {
                            $horafinmostrar = date_format(new DateTime($value->hora_fin), 'H:i:s');
                            $sheet->cell('H' . $i, function ($cell) use ($horafinmostrar) {
                                $cell->setValue($horafinmostrar);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        } else {
                            $sheet->cell('H' . $i, function ($cell) {
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($tiempo_consulta) {
                            $cell->setValue($tiempo_consulta);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($hora_admision,$hora_consulta) {
                            if($hora_admision < $hora_consulta){
                                $cell->setValue('SI');
                            }
                                else{
                                $cell->setValue('NO');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('K' . $i, function ($cell) use ($tiempo_espera) {
                            
                            $cell->setValue($tiempo_espera);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        
                        $i++;
                        $contr++;
                        
                    }
                    $i++;


                    $promedioFinal = "";
                    if ($total_tiempo_espera != "") {
                        $partes_horas = explode(':', $total_tiempo_espera);
                        $horas=(int) $partes_horas[0];
                        $minutos=(int) $partes_horas[1];
                        $total_minutos = ($horas*60) + $minutos;
                        $total_segundos = $total_minutos * 60;
                        $promedioSegundos = $total_segundos / count($datos);
                        $h = intval($promedioSegundos / 3600);
                        $m = intval(($promedioSegundos % 3600) / 60);
                        $segundosRestantes = $promedioSegundos % 60;
                        if($h>0){
                            $promedioFinal = sprintf('%02d:%02d:%02d', $h, $m, $segundosRestantes);
                        }
                        else{
                            $promedioFinal = sprintf('%02d:%02d', $m, $segundosRestantes);
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Promedio de Espera');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setBackground('#ff8000');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($promedioFinal) {
                        $cell->setValue($promedioFinal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                });
            })->export('xlsx');
        } else {
            return "No existen datos por mostrar";
        }
    }
}
