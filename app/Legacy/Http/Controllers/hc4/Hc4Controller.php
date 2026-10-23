<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Especialidad;
use Sis_medico\EstimadoSeguros;
use Sis_medico\Examen_Orden;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_Doctor;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Pais;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\TipoUsuario;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\Paciente_biopsia;

class Hc4Controller extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
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

    //INICIO
    //SOLO PARA DOCTORES
    public function vista1(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $id_usuario     = Auth::user()->id;
        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->get();

        $fecha1 = date('Y/m/d ') . "00:00:00";
        $fecha2 = date('Y/m/d ') . "23:59:59";
        //dd($fecha1);
        /*$agenda_consultas = DB::select("select * from `agenda` where `proc_consul` = '0'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $agenda_consultas = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 0)
            ->where(function ($query) use ($id_usuario) {
                $query->where([['a.id_doctor1', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor2', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor3', '=', $id_usuario]]);
            })

            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->get();
        //dd($agenda_consultas);
        //dd($agenda_consultas);
        /*$procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $procedimiento_consultas = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 1)
            ->where(function ($query) use ($id_usuario) {
                $query->where([['a.id_doctor1', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor2', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor3', '=', $id_usuario]]);
            })

            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->get();

        //dd($procedimiento_consultas);
        /*$procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $procedimiento_todas = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 1)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'h.id_seguro')
            ->get();

        // dd($procedimiento_todas);
        /*$consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $consultas_todas = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 0)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'h.id_seguro')
            ->get();

        $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->where('estado', '1')->count();
        //dd($ordenes_laboratorio);
        $pacientes = [];
        $nombres   = null;
        $apellidos = null;

        //dd($request);

        return view('hc4/inicio', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas, 'ordenes_laboratorio' => $ordenes_laboratorio, 'pacientes' => $pacientes, 'nombres' => $nombres, 'apellidos' => $apellidos, 'request' => $request, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'doctores' => $doctores, 'id_doctor1' => null, 'seguros' => $seguros, 'id_seguro' => null, 'especialidades' => $especialidades, 'id_especialidad' => null, 'proc_consul' => '2']);

    }

    //BUSCAR PACIENTE INGRESADO POR APELLIDOS Y NOMBRES
    //SOLO PARA DOCTORES
    public function buscar_paciente(Request $request)
    {
        //return $request->all();
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request['nombres']);
        $nombres_sql = '';
        $nombres     = $request['nombres'];
        $apellidos   = $request['apellidos'];
        $pacientes   = [];
        $id_usuario  = Auth::user()->id;

        $proc_consul = $request['proc_consul'];
        $id_doctor1 = $request['id_doctor1'];
        $id_seguro = $request['id_seguro'];
        $espid = $request['espid'];
        $cedula = $request['cedula'];

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == null) {
            $proc_consul = '2';
        }

        $fecha1 = $request['desde_inicio'];
        $fecha2 = $request['hasta_fin'];

        if ($fecha1 == null) {
            $fecha1 = Date('Y-m-d');
        }

        if ($fecha2 == null) {
            $fecha2 = Date('Y-m-d');
        }

        //BUSCA POR NOMBRE Y APELLIDO DEL PACIENTE
        if ($nombres != null || $apellidos != null) {

            //dd($nombres);
            $pacientes = DB::table('paciente as p')
                ->leftjoin('historiaclinica as h', 'h.id_paciente', 'p.id')
                ->leftjoin('agenda as a', 'h.id_agenda', 'a.id')
                ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.estado_cita');

            //dd($pacientes);

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $apellidos2 = explode(" ", $apellidos);
            $cantidad   = $cantidad + count($nombres2);

            foreach ($apellidos2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > '1') {
                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

                // dd($pacientes);

            } else {

                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });

            }
            // dd($pacientes->get());

            $pacientes = $pacientes->limit('1000')->get();
            //dd($pacientes);
            $agendas_proc = null;
        } elseif($cedula != null){
            $pacientes = DB::table('paciente as p')
                ->leftjoin('historiaclinica as h', 'h.id_paciente', 'p.id')
                ->leftjoin('agenda as a', 'h.id_agenda', 'a.id')
                //->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.estado_cita', 'a.id as id_agenda');

            $pacientes = $pacientes->where('a.id_paciente', $cedula)->limit('1000')->get();
            $agendas_proc = null;
        } else {
            //BUSCA POR FECHAS
            //return "hola";
            /* $pacientes = DB::table('agenda as a')->where('a.estado',1)
            ->whereBetween('fechaini',[$fecha1.' 00:00', $fecha2.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->leftjoin('historiaclinica as h','h.id_agenda','a.id');  */

            $pacientes1 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->whereNull('h.hcid')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');
            //dd($pacientes1->get());

            $pacientes2 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')
                ->whereNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

            $pacientes2_0 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')
                ->whereNotNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

            $pacientes3 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->where('a.proc_consul', '1')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');
            //dd($pacientes3->get());

            $pacientes4 = DB::table('agenda as a')->where('a.estado', 4)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '4')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

            if ($proc_consul != 2) {
                //  $pacientes = $pacientes->where('a.proc_consul', $proc_consul);
                $pacientes1   = $pacientes1->where('a.proc_consul', $proc_consul);
                $pacientes2   = $pacientes2->where('a.proc_consul', $proc_consul);
                $pacientes2_0 = $pacientes2_0->where('a.proc_consul', $proc_consul);
                $pacientes3   = $pacientes3->where('a.proc_consul', $proc_consul);
                $pacientes4   = $pacientes4->where('a.proc_consul', $proc_consul);
            }

            if (!is_null($id_doctor1)) {
                //  $pacientes = $pacientes->where('a.id_doctor1', $id_doctor1);
                $pacientes1   = $pacientes1->where('a.id_doctor1', $id_doctor1);
                $pacientes2   = $pacientes2->where('h.id_doctor1', $id_doctor1);
                $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
                $pacientes3   = $pacientes3->where('h.id_doctor1', $id_doctor1);
                $pacientes4   = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);

            }

            //$pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);
            //dd($pacientes1->get());
            //dd($totalp->get());

            //dd($pacientes->get()->count(),$pacientes1->get()->count(),$pacientes2->get()->count(), $pacientes3->get()->count(), $pacientes4->get()->count());

            if (!is_null($id_seguro)) {
                //  $pacientes = $pacientes->where('a.id_seguro', $id_seguro);
                $pacientes1   = $pacientes1->where('a.id_seguro', $id_seguro);
                $pacientes2   = $pacientes2->where('h.id_seguro', $id_seguro);
                $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
                $pacientes3   = $pacientes3->where('h.id_seguro', $id_seguro);
                $pacientes4   = $pacientes4->where('h.id_seguro', $id_seguro);
            }

            if (!is_null($espid)) {
                //  $pacientes = $pacientes->where('a.espid', $espid);
                $pacientes1   = $pacientes1->where('a.espid', $espid);
                $pacientes2   = $pacientes2->where('a.espid', $espid);
                $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
                $pacientes3   = $pacientes3->where('a.espid', $espid);
                $pacientes4   = $pacientes4->where('a.espid', $espid);

            }

            /* $pacientes = $pacientes->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita')
            ->Orderby('a.fechaini','asc')
            ->limit('100')->get();  */
            $pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);

            $pacientes1 = $pacientes1->limit('1000')->get();
            $pacientes  = $pacientes1;
            // dd($pacientes);
            //dd($pacientes);
            $agendas_proc = null;
            foreach ($pacientes as $pac) {
                //dd($pac);
                if ($pac->proc_consul == '1') {
                    $pentax = Pentax::where('id_agenda', $pac->id_agenda)->first();
                    if (!is_null($pentax)) {
                        $txt_px = '';
                        foreach ($pentax->procedimientos as $p) {
                            if ($txt_px == '') {
                                $txt_px = $p->procedimiento->nombre;
                            } else {
                                $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                            }

                        }
                        //dd($txt_px);
                        $agendas_proc[$pac->id_agenda] = [$txt_px];
                    }
                    // dd($agendas_proc[$pac->id_agenda]);
                }

            }
        }

        /*if ($nombres!=null || $apellidos != null) {
        $pacientes = $pacientes->limit('100')->get();
        }*/

        //dd($pacientes->first());
        /*if($pacientes != array()){
        $pacientes = $pacientes->limit('100')->get();
        $agendas_proc=null;
        }*/

        //dd($pacientes);

        return view('hc4/buscador_paciente_fecha', ['pacientes' => $pacientes, 'nombres' => $nombres, 'apellidos' => $apellidos, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'agendas_proc' => $agendas_proc, 'request' => $request, 'cedula' => $cedula]);
    }

    //LISTADO DE PACIENTES DEL DIA
    //BUSCA CONSULTAS Y PROCEDIMIENTOS DEL DIA DE HOY DE TODO LOS DOCTORES
    //SOLO PARA DOCTORES
    public function buscar_paciente_fecha(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $id_usuario = Auth::user()->id;

        if ($request['fecha'] == null) {
            $fecha1 = date('Y/m/d ') . "00:00:00";
            $fecha2 = date('Y/m/d ') . "23:59:59";
        } else {
            $fecha1 = $request['fecha'] . " 00:00:00";
            $fecha2 = $request['fecha'] . " 23:59:59";
            //return $fecha1;
        }

        /*$agenda_consultas = DB::select("select * from `agenda` where `proc_consul` = '0'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->count();*/
        $agendas_pac = [];
        $agendas_pac = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'h.id_seguro')
            ->where('a.espid', '<>', '10')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'se.nombre as seguro_nombre', 'em.nombre_corto as empresa_nombre', 'h.hcid', 'a.omni', 'a.estado_cita', 'a.tc', 'a.teleconsulta')->Orderby('a.fechaini', 'asc')->get();

        //dd($agendas_pac->count());
        $agendas_proc = null;
        foreach ($agendas_pac as $pac) {
            if ($pac->proc_consul == '1') {
                $pentax = Pentax::where('id_agenda', $pac->id_agenda)->first();
                if (!is_null($pentax)) {
                    $txt_px = '';
                    foreach ($pentax->procedimientos as $p) {
                        if ($txt_px == '') {
                            $txt_px = $p->procedimiento->nombre;
                        } else {
                            $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                        }

                    }
                    //dd($txt_px);
                    $agendas_proc[$pac->id_agenda] = [$txt_px];
                    //dd($agendas_proc[$pac->id_agenda]);
                }

            }
        }

        $fecha       = Date('Y-m-d');
        $fecha_hasta = Date('Y-m-d');

        /*return view('hc4/inicio_busqueda', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas,'ordenes_laboratorio'=>$ordenes_laboratorio,'agendas_pac'=>$agendas_pac]);*/

        return view('hc4/inicio_busqueda', ['agendas_pac' => $agendas_pac, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'agendas_proc' => $agendas_proc, 'fecha1' => $fecha1, 'fecha2' => $fecha2]);
    }

    //AGENDA DEL DIA
    //BUSCA CONSULTAS Y PROCEDIMIENTOS AGENDADOS DEL DIA DE HOY DEL DOCTOR QUE INGRESA SESION
    //SOLO PARA DOCTORES
    public function buscar_pacientes_doctor(Request $request)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $id_usuario = Auth::user()->id;

        if ($request['fecha'] == null) {
            $fecha1 = date('Y/m/d ') . "00:00:00";
            $fecha2 = date('Y/m/d ') . "23:59:59";
        } else {
            $fecha1 = $request['fecha'] . " 00:00:00";
            $fecha2 = $request['fecha'] . " 23:59:59";
            //return $fecha1;
        }

        /*$agenda_consultas = DB::select("select * from `agenda` where `proc_consul` = '0'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");

        $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->count();*/
        $agendas_pac = [];

        $agendas_pac = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
        //->join('users as d','d.id','a.id_doctor1')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->leftjoin('seguros as se', 'se.id', 'a.id_seguro')
        //->where('a.id_doctor1',$id_usuario)
            ->where(function ($query) use ($id_usuario) {
                $query->where([['a.id_doctor1', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor2', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor3', '=', $id_usuario]]);
            })

            ->where('a.espid', '<>', '10')
            ->where('a.proc_consul', '0')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'h.hcid', 'a.omni', 'a.estado_cita', 'a.tc', 'a.teleconsulta')->Orderby('a.fechaini', 'asc')->get();

        $agendas_pac_procedimientos = [];

        $agendas_pac_procedimientos = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
        //->join('users as d','d.id','a.id_doctor1')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->leftjoin('seguros as se', 'se.id', 'a.id_seguro')
        //->where('a.id_doctor1',$id_usuario)
            ->where(function ($query) use ($id_usuario) {
                $query->where([['a.id_doctor1', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor2', '=', $id_usuario]])
                    ->orWhere([['a.id_doctor3', '=', $id_usuario]]);
            })

            ->where('a.espid', '<>', '10')
            ->where('a.proc_consul', '1')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'h.hcid', 'a.omni', 'a.estado_cita')->Orderby('a.fechaini', 'asc')->get();
        $agendas_reuniones = Agenda::where('estado', 1)
            ->whereBetween('fechaini', [$fecha1, $fecha2])
        //->where('a.id_doctor1',$id_usuario)
            ->where(function ($query) use ($id_usuario) {
                $query->where([['id_doctor1', '=', $id_usuario]])
                    ->orWhere([['id_doctor2', '=', $id_usuario]])
                    ->orWhere([['id_doctor3', '=', $id_usuario]]);
            })
            ->where('proc_consul', '2')
            ->Orderby('fechaini', 'asc')->get();
        // dd($agendas_pac);
        //dd($agendas_reuniones);

        //Nuevo campo procedimiento
        $agendas_proc = null;
        foreach ($agendas_pac_procedimientos as $pac) {

            if ($pac->proc_consul == '1') {
                $pentax = Pentax::where('id_agenda', $pac->id_agenda)->first();
                if (!is_null($pentax)) {
                    $txt_px = '';
                    foreach ($pentax->procedimientos as $p) {
                        if ($txt_px == '') {
                            $txt_px = $p->procedimiento->nombre;
                        } else {
                            $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                        }

                    }
                    //dd($txt_px);
                    $agendas_proc[$pac->id_agenda] = [$txt_px];
                    //dd($agendas_proc[$pac->id_agenda]);
                }

            }

        }

        /*return view('hc4/inicio_busqueda', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas,'ordenes_laboratorio'=>$ordenes_laboratorio,'agendas_pac'=>$agendas_pac]);*/
        return view('hc4/inicio_busqueda_doctor', ['agendas_pac' => $agendas_pac, 'agendas_proc' => $agendas_proc, 'agendas_pac_procedimientos' => $agendas_pac_procedimientos, 'agendas_reuniones' => $agendas_reuniones]);

    }

    //HORARIO LABORABLE
    //MUESTRA EL HORARIO LABORABLE DEL DOCTOR QUE INGRESA SESION
    //SOLO PARA DOCTORES
    public function cargar_hor_doctor()
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $id_usuario    = Auth::user()->id;
        $nombre_doctor = User::find($id_usuario);

        $horarios = Horario_Doctor::where('id_doctor', $id_usuario)->orderBy('ndia', 'asc')->get();

        $diaInicio = "Monday";
        $diaFin    = "Sunday";

        $fecha    = date('Y-m-d');
        $strFecha = strtotime($fecha);

        $fechaInicio = date('Y-m-d', strtotime('last ' . $diaInicio, $strFecha));
        $fechaFin    = date('Y-m-d', strtotime('next ' . $diaFin, $strFecha));

        if (date("l", $strFecha) == $diaInicio) {
            $fechaInicio = date("Y-m-d", $strFecha);
        }
        if (date("l", $strFecha) == $diaFin) {
            $fechaFin = date("Y-m-d", $strFecha);
        }

        $fechaInicio = $fechaInicio . ' 00:00';
        $fechaFin    = $fechaFin . ' 23:59';

        $extra = Excepcion_Horario::where('id_doctor1', $id_usuario)->whereBetween('inicio', [$fechaInicio, $fechaFin])->get();

        return view('hc4/horario_doctor/buscar_horario', ['nombre_doctor' => $nombre_doctor, 'extra' => $extra, 'horarios' => $horarios]);
    }

    //ORDENES DE LABORATORIO
    //MUESTRA LAS ORDENES DE LABORATORIO INGRESADAS EL DIA DE HOY
    //SOLO PARA DOCTORES
    public function cargar_ordenes_lab()
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $fecha1 = date('Y/m/d ') . "00:00:00";
        $fecha2 = date('Y/m/d ') . "23:59:59";

        $ordenes_lab = DB::table('examen_orden as eo')
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            ->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')
            ->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')
            ->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')
            ->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')
            ->join('users as cu', 'cu.id', 'eo.id_usuariocrea')
            ->join('users as mu', 'mu.id', 'eo.id_usuariomod')
            ->whereBetween('eo.fecha_orden', [$fecha1, $fecha2])
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo')->get();
        //dd($ordenes_lab);

        return view('hc4/ordenes_lab', ['ordenes_lab' => $ordenes_lab, 'fecha1' => $fecha1, 'fecha2' => $fecha2]);
    }
    public function cargar_ordenes_lab_nuevo(Request $request)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        if (is_null($fecha1)) {
            $fecha1 = date("Y-m-d");
        }
        if (is_null($fecha2)) {
            $fecha2 = date("Y-m-d");
        }

        $ordenes_lab = DB::table('examen_orden as eo')
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            ->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')
            ->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')
            ->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')
            ->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')
            ->join('users as cu', 'cu.id', 'eo.id_usuariocrea')
            ->join('users as mu', 'mu.id', 'eo.id_usuariomod');

        $ordenes_agrupadas = Examen_Orden::join('seguros as s', 's.id', 'examen_orden.id_seguro')->where('examen_orden.estado', '1')->groupBy('s.id');

        if (!is_null($fecha1)) {
            $ordenes_lab = $ordenes_lab->whereBetween('eo.fecha_orden', [$fecha1 . ' 00:00:00', $fecha2 . ' 23:59:59']);
            $ordenes_agrupadas = $ordenes_agrupadas->whereBetween('examen_orden.fecha_orden', [$fecha1 . ' 00:00:00', $fecha2 . ' 23:59:59']);

        } else {
            $ordenes_lab = $ordenes_lab->where('eo.fecha_orden', '<=', $fecha2);
            $ordenes_agrupadas = $ordenes_agrupadas->where('examen_orden.fecha_orden', '<=', $fecha2);
        }
        $ordenes_lab = $ordenes_lab->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo')->get();

        $ordenes_agrupadas = $ordenes_agrupadas->select('s.id', 's.nombre as snombre', 's.tipo as stipo', DB::raw('count(s.id) as cantidad'))->get();

        $agrupado = [];
        foreach ($ordenes_agrupadas as $value) {
            $data['id_seguro'] = $value->id;
            $data['nombre_seguro'] = $value->snombre;
            $data['tipo_seguro'] = $value->stipo;
            $data['cantidad'] = $value->cantidad;
            array_push($agrupado, $data);
        }
        return view('hc4/ordenes_lab', ['ordenes_lab' => $ordenes_lab, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'agrupado' => $agrupado]);
    }

    //ORDENES DE LABORATORIO BUSCADOR
    //MUESTRA LAS ORDENES DE LABORATORIO INGRESADAS EL DIA DE HOY
    //SOLO PARA DOCTORES
    /*public function  cargar_ordenes_lab_buscador(){

    $opcion = '2';
    if($this->rol_new($opcion)){
    return redirect('/');
    }

    $id_usuario = Auth::user()->id;
    $fecha1 =  date('Y/m/d ')."00:00:00";
    $fecha2 =  date('Y/m/d ')."23:59:59";

    $agenda_consultas = DB::select("select * from `agenda` where `proc_consul` = '0'
    and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
    `fechaini` between '".$fecha1."' and '".$fecha2."'");

    $procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1'
    and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
    `fechaini` between '".$fecha1."' and '".$fecha2."'");

    $procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1'
    AND estado = '1'  AND
    `fechaini` between '".$fecha1."' and '".$fecha2."'");

    $consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0'
    AND estado = '1'  AND
    `fechaini` between '".$fecha1."' and '".$fecha2."'");

    $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->count();

    $ordenes_lab = DB::table('examen_orden as eo')
    ->join('paciente as p','p.id','eo.id_paciente')
    ->join('seguros as s','s.id','eo.id_seguro')
    ->leftjoin('empresa as em','em.id','eo.id_empresa')
    ->leftjoin('nivel as n','n.id','eo.id_nivel')
    ->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')
    ->leftjoin('users as d','d.id','eo.id_doctor_ieced')
    ->join('users as cu','cu.id','eo.id_usuariocrea')
    ->join('users as mu','mu.id','eo.id_usuariomod')
    ->whereBetween('eo.fecha_orden', [$fecha1,$fecha2])
    ->where('eo.estado','<>','0')
    ->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo','p.sexo')->get();

    return view('hc4/inicio_lab', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas,'ordenes_laboratorio'=>$ordenes_laboratorio,'ordenes_lab' => $ordenes_lab]);

    }*/

    //MUESTRA BARRA DE PROGRESO EN LAS ORDENES DE LABORATORIO
    public function carga_barra_progress($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = $orden->detalles;
        $resultados = $orden->resultados;

        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->examen->no_resultado == '0') {

                if (count($d->parametros) == '0') {
                    $cant_par++;
                }
                if ($d->examen->sexo_n_s == '0') {
                    $parametro_nuevo = $d->parametros->where('sexo', '3');

                } else {
                    $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);

                }
                foreach ($parametro_nuevo as $p) {
                    $cant_par++;
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

        return ['cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par];

    }

    //AGREGAR NUEVO PACIENTE
    //SOLO PARA DOCTORES
    public function agregar_paciente()
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hc4/nuevo_paciente');
    }

    //BUSCA EL PACIENTE POR NOMBRES Y APELLIDOS, SI EXISTE MUESTRA MENSAJE DE ALERTA
    //SOLO PARA DOCTORES
    public function pacientexnombre(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado = $request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2;

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) = '" . $nombre_encargado . "'
                  ";

        $nombres = DB::select($query);
        if ($nombres != array()) {
            return $nombres[0]->id;
        } else {
            return '0';
        }

    }

    //MUESTRA DETALLES DE FILIACION DE UN PACIENTE
    //SOLO PARA DOCTORES
    public function search_detalle_filiacion($id_paciente)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $paciente = Paciente::find($id_paciente);

        $agenda_last = DB::table('agenda as a')
            ->where('a.id_paciente', $id_paciente)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->where('a.espid', '<>', '10')
            ->orderBy('a.fechaini', 'desc')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('empresa as em', 'em.id', 'a.id_empresa')
            ->select('h.*', 's.nombre', 's.id', 'a.fechaini', 'a.proc_consul', 'a.cortesia', 'em.nombre_corto')
            ->first();
        //dd($agenda_last);
        $agenda       = null;
        $edad         = 0;
        $mail         = '';
        $alergiasxpac = null;

        if ($agenda_last != null) {
            $agenda = DB::table('agenda')
                ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
                ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
                ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
                ->join('users as um', 'um.id', 'agenda.id_usuariomod')
                ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
                ->leftjoin('seguros as hs', 'hs.id', 'h.id_seguro')
                ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
                ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
                ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
                ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
                ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
                ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
                ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'paciente.religion as preligion', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'paciente.referido', 'paciente.id_usuario', 'paciente.trabajo', 'paciente.observacion', 'paciente.alcohol', 'hs.nombre as hsnombre', 'h.presion', 'h.pulso', 'h.temperatura', 'h.o2', 'h.altura', 'h.peso', 'h.perimetro', 'h.examenes_realizar', 'h.id_seguro as h_idseguro')
                ->where('agenda.id', '=', $agenda_last->id_agenda)
                ->first();

            //dd($agenda);

            if ($agenda->fecha_nacimiento != null) {
                $edad = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;
            }

            if ($agenda->pparentesco == 'Principal') {
                $mail = null;
                $u1   = User::find($agenda->id_paciente);
                if (!is_null($u1)) {
                    $mail = $u1->email;
                }
            } else {
                $mail = User::find($agenda->id_usuario)->email;
            }

            $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();

        }

        $seguro = DB::table('agenda as a')
            ->where('a.id_paciente', $id_paciente)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->orderBy('a.fechaini', 'desc')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->select('s.nombre', 'a.*')
            ->first();

        //dd($seguro);
        return view('hc4/filiacion/detalle_filiacion', ['agenda' => $agenda, 'edad' => $edad, 'mail' => $mail, 'alergiasxpac' => $alergiasxpac, 'paciente' => $paciente]);

    }

    //AGREGAR PROCEDIMIENTO
    //funcion solo para los doctores
    public function selecciona_procedimiento($tipo, $paciente)
    {

        //'0: endoscopico, 1: funcional, 2:imagen, 3:consulta', 4:broncoscopias
        $px = Procedimiento::where('procedimiento.estado', '1')->get();

        $paciente = Paciente::find($paciente);

        //dd($px);
        return view('hc4.selecciona', ['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo]);

    }
    //cuando tienen una agenda primero debe validar

    public function selecciona_procedimiento2($tipo, $paciente, $hcid)
    {

        //'0: endoscopico, 1: funcional, 2:imagen, 3:consulta', 4:broncoscopias
        $px = Procedimiento::where('procedimiento.estado', '1')->get();

        $paciente = Paciente::find($paciente);

        //dd($hcid);
        return view('hc4.selecciona_editar', ['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo, 'hcid' => $hcid]);

    }

    public function crear_procedimiento2(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_doctor      = Auth::user()->id;
        $idusuario      = $id_doctor;
        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->paciente);

        if ($procedimientos != null) {

            $id_historia          = $request['hcid'];
            $procedimiento        = hc_procedimientos::where('id_hc', $id_historia)->first();
            $procedimientos_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->first();
            //return $procedimiento;
            if (!is_null($procedimientos_final)) {
                //return "entra";
                $input_hc_procedimiento = [
                    'id_hc'                 => $id_historia,
                    'id_seguro'             => $paciente->id_seguro,
                    'id_doctor_examinador'  => $idusuario,
                    'id_doctor_examinador2' => $idusuario,
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
                    'tipo_procedimiento'   => $request->tipo_procedimiento,
                ];
                hc_protocolo::insert($input_hc_protocolo);
                $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->get();
                foreach ($evoluciones as $value) {
                    $input_evolucion = [
                        'hc_id_procedimiento' => $id_hc_procedimiento,
                        'hcid'                => $id_historia,
                        'secuencia'           => $value->secuencia,
                        'motivo'              => $value->motivo,
                        'cuadro_clinico'      => $value->cuadro_clinico,
                        'laboratorio'         => $value->laboratorio,
                        'fecha_ingreso'       => ' ',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ];
                    Hc_Evolucion::insert($input_evolucion);
                }
            } else {
                $id_hc_procedimiento = $procedimiento->id;
            }

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

        return "Ingrese el Procedimiento";

    }

    public function crear_procedimiento(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->paciente);
        $sala           = \Sis_medico\Sala::where('proc_consul_sala', 1)->first();

        $empresa = Empresa::where('prioridad','1')->first();
        //dd($empresa);

        $procedimiento_crear_new = [
            'anterior'        => 'PROC_ENDOSCOPICOS -> El Dr. creo nuevo procedimiento endoscopico',
            'nuevo'           => 'PROC_ENDOSCOPICOS -> El Dr. creo nuevo procedimiento endoscopico',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini'         => Date('Y-m-d H:i:s'),
                'fechafin'         => Date('Y-m-d H:i:s'),
                'id_paciente'      => $paciente->id,
                'id_doctor1'       => $id_doctor,
                'proc_consul'      => '4',
                'estado_cita'      => '4',
                'id_empresa'       => $empresa->id,
                'espid'            => '4',
                'observaciones'    => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_seguro'        => $paciente->id_seguro,
                'estado'           => '4',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $id_doctor,
                'id_usuariomod'    => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala'          => $sala->id,
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
                'id_sala'         => $sala->id,
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
                'id_sala'         => $sala->id,
                'id_seguro'       => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc'                 => $id_historia,
                'id_seguro'             => $paciente->id_seguro,
                'id_doctor_examinador'  => $idusuario,
                'id_doctor_examinador2' => $idusuario,
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
                'tipo_procedimiento'   => $request->tipo_procedimiento,
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

        return "Ingrese el Procedimiento";

    }

    //CREAR PACIENTE
    public function crear_paciente(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = $request['cedula'];

        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if (!is_null($user)) {

            $this->validateInput2($request);
            Paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);

            Cortesia_paciente::create([
                'id'              => $request['cedula'],
                'cortesia'        => $request['cortesia'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        } else {

            $this->validateInput($request);

            User::create([
                'id'               => $request['cedula'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => '1',
                'id_pais'          => '1',
                'id_tipo_usuario'  => 2,
                'email'            => $request['email'],
                'password'         => bcrypt($request['cedula']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'fecha_nacimiento' => $request['fecha_nacimiento'],
            ]);
            paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',

                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        }

        $paciente = Paciente::find($id);

        if ($request->cortesia == 'SI') {
            $cortesia_paciente = Cortesia_paciente::find($id);
            if (is_null($cortesia_paciente)) {
                Cortesia_paciente::create([
                    'id'              => $id,
                    'cortesia'        => 'SI',
                    'ilimitado'       => 'NO',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return $paciente->id;

    }

    private function validateInput2($request)
    {

        $rules = [

            'cedula'           => 'required|max:10|unique:paciente,id',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [

            'cedula'           => 'required|max:10|unique:users',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'email'            => 'required|email|max:191|unique:users',
            'cedula'           => 'required|max:10|unique:paciente,id',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    public function calendario(Request $request)
    {

        //dd($request->all());
        if ($request['fecha'] == null) {
            $fecha_hoy = Date('Y-m-d');
        } else {
            $fecha_hoy = Date('Y-m-d', strtotime($request['fecha']));
        }

        $fecha_desde = date('Y-m-d', strtotime($fecha_hoy . "- 90 days"));
        $fecha_hasta = date('Y-m-d', strtotime($fecha_hoy . "+ 90 days"));
        //dd($fecha_hoy);
        $tipo = Auth::user()->id_tipo_usuario;

        $id   = Auth::user()->id;
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento')->where('agenda.estado_cita', '<', '4')
            ->where('agenda.proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        $agenda_px = DB::table('agenda as a')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'a.id')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'h.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'p.id as pentax')
            ->where('a.proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['h.id_doctor1', '=', $id], ['a.estado', '=', '1']])
                    ->orWhere([['h.id_doctor2', '=', $id], ['a.estado', '=', '1']])
                    ->orWhere([['h.id_doctor3', '=', $id], ['a.estado', '=', '1']]);
            })
            ->where('a.created_at', '>', $fecha_desde)->where('a.estado_cita', '4')
            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        //dd($agenda,$agenda_px);
        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        //dd($agenda3);

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();
        //dd($fecha_desde);
        $doctores    = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get(); //3=DOCTORES;
        $enfermero   = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get();
        $doctor      = User::find($id);
        $nombres     = $request['nombres'];
        $nombres_sql = '';
        $agendas_pac = [];
        if ($nombres != null) {
            $agendas_pac = DB::table('paciente as p')->join('historiaclinica as h', 'h.id_paciente', 'p.id')->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento');

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas_pac = $agendas_pac->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                //$agendas_pac = $agendas_pac->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
                $agendas_pac = $agendas_pac->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

            $agendas_pac = $agendas_pac->get();
        }

        //dd($agendas_pac);
        //return "hola";

        return view('hc4/calendario', ['user' => $user, 'doctor' => $doctor, 'users' => $doctores, 'enfermero' => $enfermero, 'id' => $id, 'agenda' => $agenda, 'agenda2' => $agenda2, 'salas' => $salas, 'agenda3' => $agenda3, 'agenda_px' => $agenda_px, 'fecha_hoy' => $fecha_hoy, 'nombres' => $nombres, 'agendas_pac' => $agendas_pac]);
    }

    public function hc4_agendar_doctor($id_doctor, $i)
    {
        /*
        if($this->rol()){
        return response()->view('errors.404');
        }*/
        $doctor        = User::find($id_doctor);
        $procedimiento = Procedimiento::all();
        return view('hc4/agregar_dr', ['i' => $i, 'doctor' => $doctor, 'procedimiento' => $procedimiento, 'paciente' => null, 'cortesia_paciente' => null]);

    }

    public function hc4_agendar_reunion($id_doctor, $i)
    {
        /*
        if($this->rol()){
        return response()->view('errors.404');
        }*/
        $doctor = User::find($id_doctor);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();
        $procedimiento = Procedimiento::all();
        return view('hc4/ag_reunion', ['i' => $i, 'doctor' => $doctor, 'procedimiento' => $procedimiento, 'paciente' => null, 'cortesia_paciente' => null, 'salas' => $salas]);

    }

    public function guardado_foto2_documento(Request $request)
    {
        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 0;
        $data        = array();
        //return "entra";
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 2,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);

            array_push($data, $id_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_doc_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $nombre_anterior                    = $file->getClientOriginalName();
            $archivo_historico->nombre_anterior = $nombre_anterior;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
        return $data;
    }

    public function guardado_foto2_estudios(Request $request)
    {
        $path        = public_path() . '/app/hc/';
        $files       = $request->file('foto');
        $idhc        = $request['id_hc_protocolo'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $protocolo   = hc_protocolo::find($idhc);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $i           = 1;
        $data        = array();
        foreach ($files as $file) {

            $input_archivo = [
                'id_hc_protocolo' => $idhc,
                'estado'          => 3,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            //sacar la extension
            $extension = $file->getClientOriginalExtension();

            $id_archivo = hc_imagenes_protocolo::insertGetId($input_archivo);
            array_push($data, $id_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_estudios_' . $id_paciente . '_' . $idhc . '_' . date('Ymd') . '_' . $id_archivo . '.' . $extension;
            //ingresar la foto
            Storage::disk('hc_ima')->put($fileName, \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico = hc_imagenes_protocolo::find($id_archivo);

            $archivo_historico->nombre          = $fileName;
            $nombre_anterior                    = $file->getClientOriginalName();
            $archivo_historico->nombre_anterior = $nombre_anterior;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
            $i                                  = $i + 1;
        }
        return $data;
    }

    public function perfil()
    {
        $id         = Auth::user()->id;
        $rolusuario = Auth::user()->id_tipo_usuario;
        $user       = User::find($id);
        // Redirect to user list if updating user wasn't existed

        $especialidades = especialidad::all();
        $especialidad   = DB::table('user_espe')->where('usuid', '=', $id)->get();
        $paises         = pais::all();
        $tipousuarios   = tipousuario::all();
        $anio           = date('Y');
        $rol_pag        = Ct_Rol_Pagos::where('estado', '1')->where('id_user', $user->id)->where('certificado','1')->orderby('id', 'desc')->get();

        return view('hc4/perfil', ['user' => $user])->with('paises', $paises)->with('tipousuarios', $tipousuarios)->with('rolusuario', $rolusuario)->with('especialidad', $especialidad)->with('especialidades', $especialidades)->with('id', $id)->with('rol_pag', $rol_pag);
    }

    public function pasteles_hc4(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;
        $doctores    = User::where('id_tipo_usuario', '3')->get();
        //procedimientos agendados por doctores
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_ag[$i] = [$doctor, $ctaagenda];
                $i++;
            }

        }

        //CONSULTAS AGENDADAS por doctores
        $doctor_co = [];
        $k         = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_co[$k] = [$doctor, $ctaagenda];
                $k++;
            }

        }

        //CONSULTAS AGENDADAS REALIZADAS por doctores
        $doctor_co_ok = [];
        $k            = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('estado_cita', '4')->count();
            if ($ctaagenda != '0') {
                $doctor_co_ok[$k] = [$doctor, $ctaagenda];
                $k++;
            }

        }

        //PROCEDIMIENTOS REALIZADOS por doctor (pentax)
        $proc_doc = [];
        $i1       = 0;
        $doctores = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = DB::table('agenda as a')->where('a.proc_consul', '1')->where('a.estado', '1')->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('pentax as p', 'p.id_agenda', 'a.id')->where('p.id_doctor1', $doctor->id)->where('p.estado_pentax', '4')->count();

            if ($ctaagenda != '0') {
                $proc_doc[$i1] = [$doctor, $ctaagenda];
                $i1++;
            }

        }

        //procedimientos por seguros
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
        return view('hc4/pastel/procesos', ['doctor_ag' => $doctor_ag, 'doctor_co' => $doctor_co, 'proc_seg' => $proc_seg, 'co_seg' => $co_seg, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'proc_doc' => $proc_doc, 'doctor_co_ok' => $doctor_co_ok]);
    }

    public function ganancia_hc4(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;

        //consultas por doctores
        $doctores       = User::where('id_tipo_usuario', '3')->get();
        $doctor_ganacia = [];
        $k              = 0;
        foreach ($doctores as $doctor) {
            $ganancia        = 0;
            $agendas         = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->where('hp.id_doctor_examinador', $doctor->id)->get();
            $elementod       = 0;
            $doctor_consulta = [];
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia                       = $ganancia + 120;
                            $doctor_consulta[$elementod][0] = '120';
                            $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                            $doctor_consulta[$elementod][2] = $value->id;
                            $doctor_consulta[$elementod][3] = $value->fechaini;
                            $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1;
                            $elementod++;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia                       = $ganancia + 14;
                                $doctor_consulta[$elementod][0] = '14';
                                $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                                $doctor_consulta[$elementod][2] = $value->id;
                                $doctor_consulta[$elementod][3] = $value->fechaini;
                                $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1;
                                $elementod++;
                            } elseif ($value->id_empresa == '1307189140001') {

                                $doctor_consulta[$elementod][0] = '12';
                                $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                                $doctor_consulta[$elementod][2] = $value->id;
                                $doctor_consulta[$elementod][3] = $value->fechaini;
                                $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1s;
                                $elementod++;
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia                       = $ganancia + $estimado->costo;
                                $doctor_consulta[$elementod][0] = $estimado->costo;
                                $doctor_consulta[$elementod][1] = $estimado->seguro->nombre;
                                $elementod++;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;

                                    $doctor_consulta[$elementod][0] = $estimado2->costo;
                                    $doctor_consulta[$elementod][1] = $estimado2->seguro->nombre;
                                    $elementod++;
                                }

                            }
                        }

                    }
                    //dd($value);
                }
            }

            $agendas = Agenda::where('agenda.proc_consul', '0')->where('agenda.id_doctor1', $doctor->id)->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->whereNull('hp.id_doctor_examinador')->get();

            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            if ($ganancia > 0) {
                $doctor_ganacia[$k] = [$doctor, $ganancia];
                //dd($doctor_consulta);
                $k++;
            }

        }

        //consultas por seguros

        $seguros = Seguro::all();
        $co_seg  = [];
        $l       = 0;
        foreach ($seguros as $seguro) {
            $ganancia   = 0;
            $cta_seguro = Agenda::where('agenda.id_seguro', $seguro->id)->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();
            if ($cta_seguro->count() > '0') {
                foreach ($cta_seguro as $value) {
                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $value->id_doctor1)->first();
                    if ($value->vip != 1) {
                        if (!is_null($estimado)) {
                            $ganancia = $ganancia + $estimado->costo;
                        } else {
                            $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                            if (!is_null($estimado2)) {
                                $ganancia = $ganancia + $estimado2->costo;
                            }

                        }
                    }

                }
                if ($ganancia > 0) {
                    $co_seg[$l] = [$seguro, $ganancia];
                    $l++;
                }
            }

        }
        $ganancia   = 0;
        $cta_seguro = Agenda::where('agenda.id_seguro', '2')->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->select('agenda.*')->get();
        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                if ($value->vip != 1) {
                    if ($value->id_empresa == '0992704152001') {
                        $ganancia = $ganancia + 14;
                    } elseif ($value->id_empresa == '1307189140001') {
                        $ganancia = $ganancia + 12;
                    }
                }

            }
            $seguro = Seguro::find(2);
            if ($ganancia > 0) {
                $co_seg[$l] = [$seguro, $ganancia];
                $l++;
            }
        }

        $ganancia   = 0;
        $vip        = [];
        $cta_seguro = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.vip', 1)->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();

        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                $ganancia = $ganancia + 120;

            }

            if ($ganancia > 0) {
                $vip[0] = ['vip', $ganancia];
            }
        }
        //ESTADISTICO DE ORDENES DE VENTA POR AGENDA CONSULTA
        $orden_venta_seguro = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('co.estado', 1)
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();

        $orden_venta_seguro2 = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->whereNull('co.id')
            ->where('agenda.proc_consul', 0)
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', 4)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();
        //dd($orden_venta_seguro2);

        //dd($orden_venta_seguro);

        $orden_venta_tipo_total = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.id', 'agenda.id_paciente', 'agenda.cortesia', 'agenda.vip', 's.clase', 's.nombre', 'co.id as orden', 'co.estado', 'co.total_sin_tarjeta', 'co.valor_oda')
            ->get();

        $agenda_dia = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('date_format(agenda.fechaini,"%Y-%m-%d") as fecha')
            ->selectRaw('count(*) as cantidad')
            ->get();

        $cantidad_dias = $agenda_dia->count();

        //dd($orden_venta_tipo_total);

        $arr          = [];
        $cantidad_nt  = 0;
        $total_nt     = 0;
        $cantidad_vip = 0;
        $total_vip    = 0;
        $cantidad_top = 0;
        $total_top    = 0;
        $arr2         = [];
        $meta_valor   = 0;
        foreach ($orden_venta_tipo_total as $value) {
            if ($value->orden != null && $value->estado == '1') {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $total_vip = $total_vip + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[1]    = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => $cantidad_dias * 2, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $total_nt = $total_nt + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[0]   = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $total_top = $total_top + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[2]    = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }
            if ($value->orden == null) {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $arr[1] = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => 0, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $arr[2] = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }

        }

        //dd($arr[0]);

        return view('hc4/pastel/ganacia_estimada', ['doctor_ganacia' => $doctor_ganacia, 'co_seg' => $co_seg, 'vip' => $vip, 'orden_venta_seguro' => $orden_venta_seguro, 'request' => $request, 'arr' => $arr, 'orden_venta_tipo_total' => $orden_venta_tipo_total, 'orden_venta_seguro2' => $orden_venta_seguro2]);
    }

    public function ganancia_efectiva(Request $request)
    {
        //return "hola";
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;

        //consultas por doctores
        $doctores       = User::where('id_tipo_usuario', '3')->get();
        $doctor_ganacia = [];
        $k              = 0;
        foreach ($doctores as $doctor) {
            $ganancia = 0;
            $agendas  = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->where('hp.id_doctor_examinador', $doctor->id)->get();
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            $agendas = Agenda::where('agenda.proc_consul', '0')->where('agenda.id_doctor1', $doctor->id)->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->whereNull('hp.id_doctor_examinador')->get();
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            if ($ganancia > 0) {
                $doctor_ganacia[$k] = [$doctor, $ganancia];
                $k++;
            }

        }

        //consultas por seguros

        $seguros = Seguro::all();
        $co_seg  = [];
        $l       = 0;
        foreach ($seguros as $seguro) {
            $ganancia   = 0;
            $cta_seguro = Agenda::where('agenda.id_seguro', $seguro->id)->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();
            if ($cta_seguro->count() > '0') {
                foreach ($cta_seguro as $value) {
                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $value->id_doctor1)->first();
                    if ($value->vip != 1) {
                        if (!is_null($estimado)) {
                            $ganancia = $ganancia + $estimado->costo;
                        } else {
                            $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                            if (!is_null($estimado2)) {
                                $ganancia = $ganancia + $estimado2->costo;
                            }

                        }
                    }

                }
                if ($ganancia > 0) {
                    $co_seg[$l] = [$seguro, $ganancia];
                    $l++;
                }
            }

        }
        $ganancia   = 0;
        $cta_seguro = Agenda::where('agenda.id_seguro', '2')->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->select('agenda.*')->get();
        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                if ($value->vip != 1) {
                    if ($value->id_empresa == '0992704152001') {
                        $ganancia = $ganancia + 14;
                    } elseif ($value->id_empresa == '1307189140001') {
                        $ganancia = $ganancia + 12;
                    }
                }

            }
            $seguro = Seguro::find(2);
            if ($ganancia > 0) {
                $co_seg[$l] = [$seguro, $ganancia];
                $l++;
            }
        }

        $ganancia   = 0;
        $vip        = [];
        $cta_seguro = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.vip', 1)->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();

        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                $ganancia = $ganancia + 120;

            }

            if ($ganancia > 0) {
                $vip[0] = ['vip', $ganancia];
            }
        }
        //ESTADISTICO DE ORDENES DE VENTA POR AGENDA CONSULTA
        $orden_venta_seguro = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('co.estado', 1)
            ->where('agenda.proc_consul', 0)
            ->where('agenda.cortesia', 'NO')
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();

        $cortesia_t = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('co.estado', 1)
            ->where('agenda.proc_consul', 0)
            ->where('agenda.cortesia', 'SI')
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('agenda.cortesia')
            ->groupBy('agenda.cortesia')
            ->first();

        //dd($orden_venta_seguro);

        $orden_venta_seguro2 = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->whereNull('co.id')
            ->where('agenda.proc_consul', 0)
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', 4)
            ->where('agenda.cortesia', 'NO')
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();
        //dd($orden_venta_seguro2);

        //dd($orden_venta_seguro);

        $orden_venta_tipo_total = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.cortesia', 'NO')
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.id', 'agenda.id_paciente', 'agenda.cortesia', 'agenda.vip', 's.clase', 's.nombre', 'co.id as orden', 'co.estado', 'co.total_sin_tarjeta', 'co.valor_oda', 'co.total')
            ->get();

        $agenda_dia = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('date_format(agenda.fechaini,"%Y-%m-%d") as fecha')
            ->selectRaw('count(*) as cantidad')
            ->get();

        $cantidad_dias = $agenda_dia->count();

        //dd($orden_venta_tipo_total);

        $arr          = [];
        $cantidad_nt  = 0;
        $total_nt     = 0;
        $cantidad_vip = 0;
        $total_vip    = 0;
        $cantidad_top = 0;
        $total_top    = 0;
        $arr2         = [];
        $meta_valor   = 0;
        foreach ($orden_venta_tipo_total as $value) {
            if ($value->orden != null && $value->estado == '1') {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $total_vip = $total_vip + $value->total + $value->valor_oda;
                    $arr[1]    = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => $cantidad_dias * 2, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $total_nt = $total_nt + $value->total + $value->valor_oda;
                    $arr[0]   = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $total_top = $total_top + $value->total + $value->valor_oda;
                    $arr[2]    = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }
            if ($value->orden == null) {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $arr[1] = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => 0, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $arr[2] = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }

        }

        //dd($arr[0]);

        return view('hc4/pastel/ganancia_efectiva', ['doctor_ganacia' => $doctor_ganacia, 'co_seg' => $co_seg, 'vip' => $vip, 'orden_venta_seguro' => $orden_venta_seguro, 'request' => $request, 'arr' => $arr, 'orden_venta_tipo_total' => $orden_venta_tipo_total, 'orden_venta_seguro2' => $orden_venta_seguro2, 'cortesia_t' => $cortesia_t]);
    }

    public function revisar_procedimientos(Request $request)
    {
        $procedimientos = Agenda::where('estado', '<>', '0')->where('proc_consul', '1')->whereBetween('fechaini', [$request->desde_inicio . '  0:00:00', $request->fecha_hasta . ' 23:59:59'])->Orderby('fechaini')->get();

        $pxs = Procedimiento::where('estado', 1)->get();

        return view('hc4/revisar_procedimientos', ['desde' => $request->desde_inicio, 'hasta' => $request->fecha_hasta, 'procedimientos' => $procedimientos, 'pxs' => $pxs]);
    }

    public function formulario_procs(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $procedimientos = $request->procedimientos;
        $procedimientos = array_unique($procedimientos);

        if (count($procedimientos) != 0) {
            $pentax = Pentax::where('id_agenda', $request->id)->first();
            if (!is_null($pentax)) {
                $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();
                $name_procs   = '';
                foreach ($pentax_procs as $ad) {
                    $name_procs = Procedimiento::find($ad->id_procedimiento)->observacion . ' ' . $name_procs;
                    $ad->delete();
                }
                $name_procs2 = '';
                foreach ($procedimientos as $value) {
                    $name_procs2      = Procedimiento::find($value)->observacion . ' ' . $name_procs2;
                    $input_pentax_pro = [
                        'id_pentax'        => $pentax->id,
                        'id_procedimiento' => $value,
                        'id_usuariocrea'   => $idusuario,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                    ];
                    PentaxProc::create($input_pentax_pro);

                }
                $input_log = [
                    'id_pentax'       => $pentax->id,
                    'tipo_cambio'     => 'REVISA AGENDA',
                    'descripcion'     => 'CAMBIO PROCEDIMIENTOS',
                    'estado_pentax'   => $pentax->estado_pentax,
                    'id_seguro'       => $pentax->id_seguro,
                    'id_subseguro'    => $pentax->id_subseguro,
                    'procedimientos'  => $name_procs2,
                    'id_doctor1'      => $pentax->id_doctor1,
                    'id_doctor2'      => $pentax->id_doctor2,
                    'id_doctor3'      => $pentax->id_doctor3,
                    'id_sala'         => $pentax->id_sala,

                    'observacion'     => 'CAMBIA PROCEDIMIENTOS',
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ];

                Pentax_log::create($input_log);
            } else {
                $agenda     = Agenda::find($request->id);
                $agproc     = AgendaProcedimiento::where('id_agenda', $request->id)->get();
                $name_procs = Procedimiento::find($agenda->id_procedimiento)->observacion;
                foreach ($agproc as $ad) {
                    $name_procs = $name_procs . ' ' . Procedimiento::find($ad->id_procedimiento)->observacion;
                    $ad->delete();
                }
                $input_proc = [
                    'id_procedimiento' => $procedimientos[0],
                    'id_usuariomod'    => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                ];
                $agenda->update($input_proc);

                $name_procs2 = '';
                foreach ($procedimientos as $value) {
                    $name_procs2 = Procedimiento::find($value)->observacion . ' ' . $name_procs2;
                    if ($procedimientos[0] != $value) {
                        AgendaProcedimiento::create([
                            'id_agenda'        => $request->id,
                            'id_procedimiento' => $value,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                        ]);
                    }
                }
                Log_agenda::create([
                    'id_agenda'       => $request->id,
                    'descripcion'     => 'REVISA AGENDA',
                    'descripcion2'    => 'ACTUALIZA PROCEDIMIENTOS',
                    'observaciones'   => 'REVISA AGENDA: ' . $name_procs . '=>' . $name_procs2,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            }

        }

        return $procedimientos;

    }

    public function carga_revision($id_agenda, $val)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_agenda = substr($id_agenda, 8);
        $txt       = '';
        if ($val == '0') {
            $txt = 'NO';
        }if ($val == '1') {
            $txt = 'SI';
        }
        $agenda = Agenda::find($id_agenda);
        if (!is_null($agenda)) {
            $agenda->update(['revisado' => $val]);
            Log_agenda::create([
                'id_agenda'       => $agenda->id,
                'descripcion'     => 'REVISA AGENDA',
                'descripcion2'    => $txt,
                'observaciones'   => 'REVISA AGENDA: ' . $txt,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);
        }

        return [$id_agenda, $val];
    }

    public function revision_formulario(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($request->id);
        if (!is_null($agenda)) {
            $agenda->update([
                'observacion_proc' => $request->observacion,
                'diagnostico_proc' => $request->diagnostico,
            ]);
            Log_agenda::create([
                'id_agenda'       => $request->id,
                'descripcion'     => 'ACTUALIZA OBSERVACION/DIAGNOSTICO',
                'descripcion2'    => 'obs: ' . $request->observacion . ' diag:' . $request->diagnostico,
                'observaciones'   => 'obs: ' . $request->observacion . ' diag:' . $request->diagnostico,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }

    public function cambiar_supervision($cambio, $id_agenda)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($id_agenda);
        $texto      = 'SUPERVISADO POR EL DR. ROBLES';
        if (!is_null($agenda)) {
            if ($cambio != '1') {
                $cambio = null;
                $texto  = 'YA NO SERA SUPERVISADO POR EL DR. ROBLES';
            }
            $agenda->update([
                'supervisa_robles' => $cambio,
            ]);
            Log_agenda::create([
                'id_agenda'       => $id_agenda,
                'descripcion'     => 'REVISION DE PROCEDIMIENTOS',
                'descripcion2'    => $texto,
                'observaciones'   => '',
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }
    public function cambiar_crm($cambio, $id_agenda)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($id_agenda);
        $texto      = 'SOLO REALIZADO POR EL DR. ROBLES';
        if (!is_null($agenda)) {
            if ($cambio != '1') {
                $cambio = null;
                $texto  = 'PUEDE SER REALIZADO POR CUALQUIER DR';
            }
            $agenda->update([
                'solo_robles' => $cambio,
            ]);
            Log_agenda::create([
                'id_agenda'       => $id_agenda,
                'descripcion'     => 'REVISION DE PROCEDIMIENTOS',
                'descripcion2'    => $texto,
                'observaciones'   => '',
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }

    public function exportar_revision(Request $request)
    {
        //dd($request->all());
        $procedimientos = Agenda::where('estado', '<>', '0')->where('proc_consul', '1')->whereBetween('fechaini', [$request->desde_inicio . '  0:00:00', $request->fecha_hasta . ' 23:59:59'])->Orderby('fechaini')->get();
        //dd($procedimientos);
        Excel::create('exportar revision', function ($excel) use ($procedimientos) {
            $excel->sheet('exportar revision', function ($sheet) use ($procedimientos) {
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha / Hora');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cedula');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Apellidos');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombres');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Edad');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO ASIGNADO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Seguro');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Procedimiento');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Amb/Hosp');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Estado');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Revisado');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Diagnostico');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Observaciones');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = 3;
                foreach ($procedimientos as $procedimiento) {
                    $resto_procs  = AgendaProcedimiento::where('id_agenda', $procedimiento->id)->get();
                    $pentax       = Pentax::where('id_agenda', $procedimiento->id)->first();
                    $pcant        = 0;
                    $pentax_procs = null;
                    if (!is_null($pentax)) {
                        $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();
                        $pcant        = $pentax_procs->count();
                    }

                    $edad = 0;
                    if ($procedimiento->paciente->fecha_nacimiento != null) {
                        $edad = Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
                    }
                    $sheet->cell('B' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->fechaini);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->id_paciente);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->paciente->apellido1 . ' ' . $procedimiento->paciente->apellido2);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->paciente->nombre1 . ' ' . $procedimiento->paciente->nombre2);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($edad) {
                        // manipulate the cel
                        $cell->setValue($edad);
                    });
                    if (!is_null($procedimiento->id_doctor1)) {
                        $sheet->cell('G' . $i, function ($cell) use ($procedimiento) {
                            // manipulate the cel
                            $cell->setValue('Dr. ' . $procedimiento->doctor1->nombre1 . ' ' . $procedimiento->doctor1->apellido1);
                        });
                    }
                    $sheet->cell('H' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->seguro->nombre);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($procedimiento, $resto_procs, $pentax_procs, $pcant) {
                        // manipulate the cel
                        $texto = $procedimiento->procedimiento->nombre;
                        foreach ($resto_procs as $val) {
                            $texto = $texto . ' ' . Procedimiento::find($val->id_procedimiento)->observacion;
                        }
                        if ($pcant > 0) {
                            $texto = '';
                            foreach ($pentax_procs as $val) {
                                $texto = $texto . ' ' . Procedimiento::find($val->id_procedimiento)->observacion;
                            }
                        }
                        $cell->setValue($texto);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->est_amb_hos == 0) {
                            $cell->setValue("Ambulatorio");
                        } else {
                            $cell->setValue("Hospitalizado");
                        }
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->estado_cita == '0') {
                            $cell->setValue("Por Confirmar");
                        } elseif ($procedimiento->estado_cita == '1') {
                            $cell->setValue("Confirmado");
                        } elseif ($procedimiento->estado_cita == '2') {
                            $cell->setValue("Reagendar");
                        } elseif ($procedimiento->estado_cita == '3') {
                            $cell->setValue("Suspendido");
                        } elseif ($procedimiento->estado_cita == '4') {
                            $cell->setValue("Pre-Admisionado");
                        }

                    });
                    $sheet->cell('L' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->revisado == '0') {
                            $cell->setValue("No");
                        } elseif ($procedimiento->revisado == '1') {
                            $cell->setValue("Si");
                        }

                    });
                    $sheet->cell('M' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->diagnostico_proc);
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->observacion_proc);
                    });
                    $i++;
                }

            });
        })->export('xlsx');
    }

    public function agenda_log($id)
    {

        $logs   = Log_Agenda::where('id_agenda', $id)->get();
        $agenda = Agenda::find($id);

        return view('hc4/revisar_procedimientos/log', ['logs' => $logs, 'agenda' => $agenda]);

    }
    public function busca_log_agenda($id_agenda)
    {

        $logs = Log_Agenda::where('id_agenda', $id_agenda)->get();
        //dd($logs);

        return view('hc4/consulta/log_agenda', ['logs' => $logs]);
    }
    public function modalimagen($id)
    {
        $imagenes    = hc_imagenes_protocolo::where('id', $id)->where('estado', '1')->OrderBy('created_at', 'desc')->first();
        $public_path = public_path();
        $url         = 'http://192.168.59.38/sis_medico_prb/' . '/storage/app/hc_ima/' . $imagenes->nombre; // depende de root en el archivo filesystems.php.

        //verificamos si el archivo existe y lo retornamos
        return view('hc4/modaleditor', ['imagenf' => $imagenes, 'url' => $url, 'id' => $id]);
    }
    public function saveimage(Request $request)
    {
        //dd($request->all());
        $id               = $request['id'];
        $imagenes         = hc_imagenes_protocolo::where('id', $id)->where('estado', '1')->OrderBy('created_at', 'desc')->first();
        $extension        = 'hc_doc_' . $id . '_' . date('Ymd') . '_' . $request['croppedImage']->getClientOriginalName();
        $imagenes->nombre = $extension;
        $imagenes->save();
        $fileName = $extension;
        //ingresar la foto
        Storage::disk('hc_ima')->put($fileName, \File::get($request['croppedImage']));
        return response()->json(['success' => '1']);
    }
    public function index_proximos_proc($id_paciente)
    {
        $fecha = date('Y-m-d H:i:s');
        $fecha_2 = date('Y-m-d H:i:s', strtotime($fecha. "-3 month"));
        $prox_procedimientos = Agenda::where('agenda.id_paciente', $id_paciente)
        ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
        ->join('users as um', 'um.id', 'agenda.id_usuariomod')
        //->where('agenda.estado', '<>', '0')
        ->where('agenda.proc_consul', '1')
        ->where('agenda.fechaini', '>=', $fecha)
        ->select('agenda.*', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre')
        ->OrderBy('agenda.fechaini', 'asc')->get();
        $procedimientos = Procedimiento::all();
        $paciente = Paciente::find($id_paciente);
        return view('hc4/proximos_procedimientos/index', ['paciente' => $paciente, 'prox_procedimientos' => $prox_procedimientos, 'procedimientos' => $procedimientos]);
    }
}
