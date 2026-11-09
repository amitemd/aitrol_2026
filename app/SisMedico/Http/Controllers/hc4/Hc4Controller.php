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
use Sis_medico\Ciudad;
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
use Sis_medico\Provincia;
use Sis_medico\Seguro;
use Sis_medico\Services\PatientTenantService;
use Sis_medico\TipoUsuario;
use Sis_medico\Empresa;
use Sis_medico\User;
use Svg\Tag\Rect;

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
            return response()->view('errors.403', [
                'message' => 'El perfil Doctor todavía no tiene habilitado el acceso al módulo clínico.',
            ], 403);
        }

        $usuario        = Auth::user();
        $id_usuario     = $usuario->id;
        $doctores       = User::where('id_tipo_usuario', 3)
            ->where('estado', 1)
            ->when((int) $usuario->id_tipo_usuario === 3, function ($query) use ($id_usuario) {
                $query->where('id', $id_usuario);
            })
            ->orderby('apellido1')
            ->get();
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
            ->where('a.estado', 1)->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 0)
            ->where(function ($query) use ($id_usuario) {
                $query->where('a.id_doctor1', $id_usuario)
                    ->orWhere('a.id_doctor2', $id_usuario)
                    ->orWhere('a.id_doctor3', $id_usuario);
            })->count('a.id');
        //dd($agenda_consultas);
        //dd($agenda_consultas);
        /*$procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1'
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $procedimiento_consultas = DB::table('agenda as a')
            ->where('a.estado', 1)->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 1)
            ->where(function ($query) use ($id_usuario) {
                $query->where('a.id_doctor1', $id_usuario)
                    ->orWhere('a.id_doctor2', $id_usuario)
                    ->orWhere('a.id_doctor3', $id_usuario);
            })->count('a.id');

        //dd($procedimiento_consultas);
        /*$procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $procedimiento_todas = DB::table('agenda as a')
            ->where('a.estado', 1)->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 1)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->count('a.id');

        // dd($procedimiento_todas);
        /*$consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0'
        AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");*/

        $consultas_todas = DB::table('agenda as a')
            ->where('a.estado', 1)->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->where('a.proc_consul', 0)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->count('a.id');

        $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->where('estado', '1')->count();
        //dd($ordenes_laboratorio);
        $pacientes = [];
        $nombres   = null;
        $apellidos = null;

        //dd($request);

        $id_doctor1 = (int) $usuario->id_tipo_usuario === 3 ? $id_usuario : null;

        return view('hc4/inicio', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas, 'ordenes_laboratorio' => $ordenes_laboratorio, 'pacientes' => $pacientes, 'nombres' => $nombres, 'apellidos' => $apellidos, 'request' => $request, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => null, 'especialidades' => $especialidades, 'id_especialidad' => null, 'proc_consul' => '2']);

    }

    /**
     * Búsqueda paginada para los filtros Select2 de la pantalla ambulatoria.
     */
    public function catalogosSelect2(Request $request)
    {
        if ($this->rol_new('2')) {
            return response()->json(['results' => []], 403);
        }

        $catalogo = (string) $request->query('catalogo', '');
        $termino = trim((string) $request->query('q', ''));
        $pagina = max(1, (int) $request->query('page', 1));
        $limite = 20;

        if (!in_array($catalogo, ['especialidades', 'doctores', 'seguros'], true) || $termino === '') {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
            ]);
        }

        $patron = '%' . addcslashes($termino, '\\%_') . '%';

        if ($catalogo === 'especialidades') {
            $consulta = Especialidad::where('estado', '1')
                ->where('nombre', 'like', $patron)
                ->orderBy('nombre')
                ->select('id', 'nombre');
        } elseif ($catalogo === 'seguros') {
            $consulta = Seguro::where('inactivo', '1')
                ->where('nombre', 'like', $patron)
                ->orderBy('nombre')
                ->select('id', 'nombre');
        } else {
            $consulta = User::where('id_tipo_usuario', 3)
                ->where('estado', 1)
                ->where(function ($query) use ($patron) {
                    $query->where('id', 'like', $patron)
                        ->orWhere('apellido1', 'like', $patron)
                        ->orWhere('apellido2', 'like', $patron)
                        ->orWhere('nombre1', 'like', $patron)
                        ->orWhere('nombre2', 'like', $patron)
                        ->orWhereRaw("CONCAT_WS(' ', apellido1, apellido2, nombre1, nombre2) LIKE ?", [$patron]);
                })
                ->orderBy('apellido1')
                ->orderBy('nombre1')
                ->select('id', 'apellido1', 'apellido2', 'nombre1', 'nombre2');
        }

        $registros = $consulta->skip(($pagina - 1) * $limite)->take($limite + 1)->get();
        $hayMas = $registros->count() > $limite;
        $registros = $registros->take($limite);

        $resultados = $registros->map(function ($registro) use ($catalogo) {
            $texto = $catalogo === 'doctores'
                ? trim($registro->apellido1 . ' ' . $registro->apellido2 . ' ' . $registro->nombre1 . ' ' . $registro->nombre2)
                : $registro->nombre;

            return [
                'id' => (string) $registro->id,
                'text' => $texto,
            ];
        })->values();

        return response()->json([
            'results' => $resultados,
            'pagination' => ['more' => $hayMas],
        ]);
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
        $usuario     = Auth::user();
        $id_usuario  = $usuario->id;

        $proc_consul = $request['proc_consul'];
        // Un médico solamente puede consultar sus propias atenciones desde
        // esta pantalla. El filtro recibido sigue disponible para perfiles
        // administrativos autorizados, pero nunca puede ampliar el alcance
        // de una sesión médica.
        $id_doctor1 = (int) $usuario->id_tipo_usuario === 3
            ? $id_usuario
            : $request['id_doctor1'];
        $id_seguro = $request['id_seguro'];
        $espid = $request['espid'];
        $cedula = $request['cedula'];
        $id_empresa = trim((string) session('id_empresa'));
        if ($id_empresa === '') {
            abort(403, 'Seleccione una empresa antes de buscar pacientes.');
        }

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
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento');
            app(PatientTenantService::class)->constrain($pacientes, 'p.id', $id_empresa);

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
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento');
            app(PatientTenantService::class)->constrain($pacientes, 'p.id', $id_empresa);

            $pacientes = $pacientes->where('p.id', $cedula)->limit('1000')->get();
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
                ->where('a.id_empresa', $id_empresa)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
                ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
                ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
                ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->whereNull('h.hcid')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as nombre'), 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', DB::raw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) as seguro_nom'), 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');
            //dd($pacientes1->get());

            $pacientes2 = DB::table('agenda as a')->where('a.estado', 1)
                ->where('a.id_empresa', $id_empresa)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
                ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
                ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
                ->leftJoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')
                ->whereNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as nombre'), 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', DB::raw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) as seguro_nom'), 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

            $pacientes2_0 = DB::table('agenda as a')->where('a.estado', 1)
                ->where('a.id_empresa', $id_empresa)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
                ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
                ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')
                ->whereNotNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as nombre'), 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', DB::raw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) as seguro_nom'), 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

            $pacientes3 = DB::table('agenda as a')->where('a.estado', 1)
                ->where('a.id_empresa', $id_empresa)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
                ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
                ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
                ->where('a.proc_consul', '1')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as nombre'), 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', DB::raw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) as seguro_nom'), 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');
            //dd($pacientes3->get());

            $pacientes4 = DB::table('agenda as a')->where('a.estado', 4)
                ->where('a.id_empresa', $id_empresa)
                ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
                ->whereBetween('fechaini', [$fecha1 . ' 00:00', $fecha2 . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
                ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
                ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '4')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as nombre'), 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', DB::raw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) as seguro_nom'), 'a.observaciones', 'a.id_procedimiento', 'a.omni', 'a.tc', 'a.teleconsulta');

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
                $pacientes1   = $pacientes1->whereRaw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) = ?', [$id_seguro]);
                $pacientes2   = $pacientes2->whereRaw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) = ?', [$id_seguro]);
                $pacientes2_0 = $pacientes2_0->whereRaw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) = ?', [$id_seguro]);
                $pacientes3   = $pacientes3->whereRaw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) = ?', [$id_seguro]);
                $pacientes4   = $pacientes4->whereRaw('COALESCE(h.id_seguro, a.id_seguro, p.id_seguro) = ?', [$id_seguro]);
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

            // El resultado representa atenciones. Un paciente puede tener
            // consultas de distintas especialidades dentro del mismo rango;
            // se elimina solamente la duplicidad producida por los UNION y
            // se conserva una fila por agenda.
            $pacientes = $pacientes1
                ->sortByDesc('fechaini')
                ->groupBy('id_agenda')
                ->map(function ($atenciones) {
                    return $atenciones->first();
                })
                ->values();
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
            ->leftJoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', 'a.id_doctor1')
            ->leftJoin('empresa as em', 'em.id', 'a.id_empresa')
            ->leftJoin('seguros as se_historia', 'se_historia.id', 'h.id_seguro')
            ->leftJoin('seguros as se_agenda', 'se_agenda.id', 'a.id_seguro')
            ->leftJoin('seguros as se_paciente', 'se_paciente.id', 'p.id_seguro')
            ->where('a.espid', '<>', '10')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', DB::raw('COALESCE(se_historia.nombre, se_agenda.nombre, se_paciente.nombre) as seguro_nombre'), 'em.nombre_corto as empresa_nombre', 'h.hcid', 'a.omni', 'a.estado_cita', 'a.tc', 'a.teleconsulta')->Orderby('a.fechaini', 'asc')->get();

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

        $diagnosticos_por_hcid = collect();
        $hcids = $agendas_pac->pluck('hcid')->filter()->unique()->values();
        if ($hcids->count() > 0) {
            $diagnosticos_por_hcid = DB::table('hc_cie10')
                ->whereIn('hcid', $hcids->all())
                ->select('hcid', DB::raw('COUNT(*) as total'))
                ->groupBy('hcid')
                ->pluck('total', 'hcid');
        }

        $fecha       = Date('Y-m-d');
        $fecha_hasta = Date('Y-m-d');

        /*return view('hc4/inicio_busqueda', ['agenda_consultas' => $agenda_consultas, 'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas,'ordenes_laboratorio'=>$ordenes_laboratorio,'agendas_pac'=>$agendas_pac]);*/

        return view('hc4/inicio_busqueda', ['agendas_pac' => $agendas_pac, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'agendas_proc' => $agendas_proc, 'diagnosticos_por_hcid' => $diagnosticos_por_hcid]);
    }

    //AGENDA DEL DIA
    //BUSCA CONSULTAS Y PROCEDIMIENTOS AGENDADOS DEL DIA DE HOY DEL DOCTOR QUE INGRESA SESION
    //SOLO PARA DOCTORES
    public function buscar_pacientes_doctor(Request $request)
    {
        if ($this->rol_new('2')) {
            return redirect('/');
        }

        $id_usuario = Auth::id();
        $fecha = $request->input('fecha') ?: date('Y-m-d');
        $fecha1 = $fecha . ' 00:00:00';
        $fecha2 = $fecha . ' 23:59:59';

        // Consultas y procedimientos comparten filtros y joins: una sola lectura.
        $agendas_dia = DB::table('agenda as a')
            ->where('a.estado', 1)
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->whereBetween('a.fechaini', [$fecha1, $fecha2])
            ->leftJoin('historiaclinica as h', 'h.id_agenda', '=', 'a.id')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->join('users as d', 'd.id', '=', 'a.id_doctor1')
            ->where(function ($query) use ($id_usuario) {
                $query->where('a.id_doctor1', $id_usuario)
                    ->orWhere(function ($procedimientos) use ($id_usuario) {
                        $procedimientos->where('a.proc_consul', '1')
                            ->where(function ($colaboradores) use ($id_usuario) {
                                $colaboradores->where('a.id_doctor2', $id_usuario)
                                    ->orWhere('a.id_doctor3', $id_usuario);
                            });
                    });
            })
            ->where('a.espid', '<>', '10')
            ->whereIn('a.proc_consul', ['0', '1'])
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2',
                'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'a.proc_consul',
                'a.id as id_agenda', 'a.cortesia', 'd.nombre1 as dnombre1',
                'd.apellido1 as dapellido1', 'h.hcid', 'a.omni', 'a.estado_cita',
                'a.tc', 'a.teleconsulta', 'a.id_procedimiento')
            ->orderBy('a.fechaini')->get();

        $agendas_pac = $agendas_dia->where('proc_consul', '0')->values();
        $agendas_pac_procedimientos = $agendas_dia->where('proc_consul', '1')->values();
        $agendas_reuniones = Agenda::where('estado', 1)
            ->whereBetween('fechaini', [$fecha1, $fecha2])
            ->where(function ($query) use ($id_usuario) {
                $query->where('id_doctor1', $id_usuario)
                    ->orWhere('id_doctor2', $id_usuario)
                    ->orWhere('id_doctor3', $id_usuario);
            })
            ->where('proc_consul', '2')->orderBy('fechaini')->get();

        $summary = app(\Sis_medico\Services\DoctorAgendaSummaryService::class)
            ->load($agendas_dia, $agendas_reuniones);

        return view('hc4/inicio_busqueda_doctor', array_merge($summary, compact(
            'agendas_pac', 'agendas_pac_procedimientos', 'agendas_reuniones'
        )));
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

    /**
     * Devuelve las agendas del médico autenticado para el rango visible del
     * calendario de horario laboral.
     */
    public function eventos_horario_doctor(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return response()->json(array(), 403);
        }

        $inicio = strtotime((string) $request->input('start'));
        $fin = strtotime((string) $request->input('end'));
        if ($inicio === false || $fin === false || $fin <= $inicio || ($fin - $inicio) > (62 * 86400)) {
            return response()->json(array('message' => 'El rango de fechas del calendario no es válido.'), 422);
        }

        $idDoctor = Auth::id();
        $idEmpresa = trim((string) session('id_empresa'));
        $agendas = DB::table('agenda as a')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->where('a.estado', 1)
            ->whereIn('a.proc_consul', array('0', '1'))
            ->where('a.fechaini', '<', date('Y-m-d H:i:s', $fin))
            ->where('a.fechafin', '>', date('Y-m-d H:i:s', $inicio))
            ->where(function ($query) use ($idDoctor) {
                $query->where('a.id_doctor1', $idDoctor)
                    ->orWhere('a.id_doctor2', $idDoctor)
                    ->orWhere('a.id_doctor3', $idDoctor);
            });

        if ($idEmpresa !== '') {
            $agendas->where('a.id_empresa', $idEmpresa);
        }

        $agendas = $agendas->select(
            'a.id', 'a.id_paciente', 'a.fechaini', 'a.fechafin', 'a.proc_consul',
            'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2'
        )->orderBy('a.fechaini')->get();

        $eventos = array();
        foreach ($agendas as $agenda) {
            $paciente = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter(array(
                $agenda->apellido1, $agenda->apellido2, $agenda->nombre1, $agenda->nombre2
            )))));
            $esProcedimiento = (int) $agenda->proc_consul === 1;
            $eventos[] = array(
                'id' => 'agenda-' . $agenda->id,
                'title' => ($esProcedimiento ? 'Procedimiento: ' : 'Consulta: ') . $paciente,
                'start' => $agenda->fechaini,
                'end' => $agenda->fechafin,
                'color' => $esProcedimiento ? '#17a090' : '#087ca7',
                'textColor' => '#ffffff',
                'editable' => false,
                'overlap' => true,
                'className' => 'hc4-patient-event',
                'patient' => $paciente,
                'patient_id' => $agenda->id_paciente,
                'appointment_type' => $esProcedimiento ? 'Procedimiento' : 'Consulta',
            );
        }

        return response()->json($eventos);
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

    //MUESTRA BARRA DE PROGRESO EN LAS ORDENES DE LABORATORIO
    public function carga_barra_progress($id)
    {
        return app(\Sis_medico\Services\ClinicalHc4SupportQueryService::class)->carga_barra_progress($id);
    }
    public function agregar_paciente()
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hc4/nuevo_paciente');
    }

    public function cargar_log_hc(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $from = $request->input('from');
        $to = $request->input('to');

        $query = Log_usuario::join('users as u', 'u.id', 'log_usuario.id_usuario')->where('log_usuario.descripcion', 'DESCARGO HISTORIA CLINICA');

        // Si hay ambas fechas, filtramos entre fechas (inclusive)
        if ($from && $to) {
            // Aseguramos formato correcto y rango inclusive
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate = Carbon::parse($to)->endOfDay();
            $query->whereBetween('log_usuario.created_at', [$fromDate, $toDate]);
        } elseif ($from) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('log_usuario.created_at', '>=', $fromDate);
        } elseif ($to) {
            $toDate = Carbon::parse($to)->endOfDay();
            $query->where('log_usuario.created_at', '<=', $toDate);
        }

        // Orden ejemplo: más recientes arriba
        $items = $query->select('u.nombre1', 'u.apellido1', 'u.apellido2', 'log_usuario.descripcion', 'log_usuario.dato_ant2', 'log_usuario.created_at')->orderBy('log_usuario.created_at', 'desc')->get();
        return view('hc4/log_hc', compact('items', 'from', 'to'));
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
                ->leftJoin('seguros', 'paciente.id_seguro', '=', 'seguros.id')
                ->leftJoin('users as uc', 'uc.id', 'agenda.id_usuariocrea')
                ->leftJoin('users as um', 'um.id', 'agenda.id_usuariomod')
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

            if (!is_null($agenda)) {
                if (!empty($agenda->fecha_nacimiento)) {
                    try {
                        $edad = Carbon::parse($agenda->fecha_nacimiento)->age;
                    } catch (\Exception $exception) {
                        $edad = 0;
                    }
                }

                $usuarioCorreo = $agenda->pparentesco == 'Principal'
                    ? User::find($agenda->id_paciente)
                    : User::find($agenda->id_usuario);
                $mail = is_null($usuarioCorreo) ? null : $usuarioCorreo->email;

                $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
            }

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

        // Clínica Borja utiliza un único catálogo de procedimientos activos.
        $px = Procedimiento::with('grupo_procedimiento')
            ->where('procedimiento.estado', '1')
            ->orderBy('procedimiento.nombre')
            ->get();

        $paciente = Paciente::find($paciente);

        //dd($px);
        return view('hc4.selecciona', ['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo]);

    }
    //cuando tienen una agenda primero debe validar

    public function selecciona_procedimiento2($tipo, $paciente, $hcid)
    {

        // Clínica Borja utiliza un único catálogo de procedimientos activos.
        $px = Procedimiento::with('grupo_procedimiento')
            ->where('procedimiento.estado', '1')
            ->orderBy('procedimiento.nombre')
            ->get();

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
        return app(\Sis_medico\Services\ClinicalHc4ProcedureService::class)->crear_procedimiento($request);
    }

    public function crear_paciente(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = trim((string) $request->input('cedula'));
        $request->merge(array('cedula' => $id));

        $birthDate = trim((string) $request->input('fecha_nacimiento'));
        if ($birthDate !== '' && strtotime(str_replace('/', '-', $birthDate)) !== false) {
            $request->merge(array('fecha_nacimiento' => date('Y-m-d', strtotime(str_replace('/', '-', $birthDate)))));
        }

        // La identidad del usuario es global. El alcance por empresa puede
        // ocultar una cuenta existente y provocar un intento de duplicar su PK.
        $user = User::withoutGlobalScope('active_company')->find($id);

        DB::transaction(function () use ($request, $user, $id, $ip_cliente, $idusuario) {
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
        });

        $paciente = Paciente::find($id);

        if (is_null($paciente)) {
            throw new \RuntimeException('El paciente no quedó asociado a la empresa activa.');
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
            'fecha_nacimiento' => 'required|date',

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
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [

            'cedula'           => 'required|max:10|unique:users,id|unique:paciente,id',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'email'            => 'required|email|max:191|unique:users',
            'fecha_nacimiento' => 'required|date',

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
        return app(\Sis_medico\Services\ClinicalHc4SupportQueryService::class)->calendario($request);
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
        return app(\Sis_medico\Services\ClinicalProtocolAttachmentService::class)
            ->store($request, \Sis_medico\Services\ClinicalProtocolAttachmentService::DOCUMENT);
    }

    public function guardado_foto2_estudios(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalProtocolAttachmentService::class)
            ->store($request, \Sis_medico\Services\ClinicalProtocolAttachmentService::STUDY);
    }

    public function perfil()
    {
        return app(\Sis_medico\Services\ClinicalHc4SupportQueryService::class)->perfil();
    }
    public function pasteles_hc4(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4AnalyticsService::class)->pasteles_hc4($request);
    }

    public function ganancia_hc4(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4AnalyticsService::class)->ganancia_hc4($request);
    }

    public function ganancia_efectiva(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4AnalyticsService::class)->ganancia_efectiva($request);
    }

    public function formulario_procs(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->formulario_procs($request);
    }

    public function carga_revision($id_agenda, $val)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->carga_revision($id_agenda, $val);
    }

    public function revision_formulario(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->revision_formulario($request);
    }

    public function cambiar_supervision($cambio, $id_agenda)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->cambiar_supervision($cambio, $id_agenda);
    }

    public function cambiar_crm($cambio, $id_agenda)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->cambiar_crm($cambio, $id_agenda);
    }

    public function exportar_revision(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalHc4ReviewService::class)->exportar_revision($request);
    }

    public function agenda_log($id)
    {
        return app(\Sis_medico\Services\ClinicalUpcomingProcedureQueryService::class)->reviewLog($id);
    }
    public function busca_log_agenda($id_agenda)
    {
        return app(\Sis_medico\Services\ClinicalUpcomingProcedureQueryService::class)->agendaLog($id_agenda);
    }
    public function modalimagen($id)
    {
        return app(\Sis_medico\Services\ClinicalUpcomingProcedureQueryService::class)->imageModal($id);
    }
    public function saveimage(Request $request)
    {
        //dd($request->all());
        $id= $request['id'];
        $imagenes = hc_imagenes_protocolo::where('id', $id)->where('estado','1')->OrderBy('created_at', 'desc')->first();
        $extension = 'hc_doc_'.$id.'_'.date('Ymd').'_'.$request['croppedImage']->getClientOriginalName();
        $imagenes->nombre= $extension;
        $imagenes->save();
        $fileName =  $extension;
        //ingresar la foto
        Storage::disk('hc_ima')->put($fileName, \File::get($request['croppedImage']));
        return response()->json(['success'=>'1']);
    }
    public function index_proximos_proc($id_paciente)
    {
        return app(\Sis_medico\Services\ClinicalUpcomingProcedureQueryService::class)->index_proximos_proc($id_paciente);
    }
}
