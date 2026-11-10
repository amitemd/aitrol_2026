<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaQ;
use Sis_medico\Cama;
use Sis_medico\CamaPaciente;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Especialidad;
use Sis_medico\Evolucion_005;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
// use Sis_medico\Hospital;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Ho_Archivos_Solicitud;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_estado_paso;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\HoEstadoPaso;
use Sis_medico\Hospital;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hospital_Producto;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Medicina;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\Plan_005;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Procedimiento;
use Sis_medico\Quirofano;
use Sis_medico\Seguro;
use Sis_medico\Services\AmbulatoryAdmissionService;
use Sis_medico\Services\ClinicalEmergencyFormService;
use Sis_medico\Services\ClinicalEmergencyFormUpdateService;
use Sis_medico\Services\ClinicalHospitalDestinationService;
use Sis_medico\Services\ClinicalHospitalFormAvailabilityService;
use Sis_medico\Services\ClinicalHospitalFormService;
use Sis_medico\Services\ClinicalHospitalModuleService;
use Sis_medico\Services\ClinicalHospitalPatientRegistrationService;
use Sis_medico\Services\ClinicalHospitalQueryService;
use Sis_medico\Services\ClinicalHospitalTransferService;
use Sis_medico\Services\ClinicalTransferReconciliationService;
use Sis_medico\Services\HospitalServiceMenuService;
use Sis_medico\Subseguro;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\User;

class HospitalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_if($this->rol_new('1'), 403);

            return $next($request);
        });
    }

    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Catalogo minimo requerido por el tablero hospitalario.
     *
     * La base historica incluye la estructura de ho_estado_paso, pero algunas
     * instalaciones no contienen sus registros iniciales. Mantener este
     * catalogo permite recuperar la configuracion sin mostrar un error 500.
     *
     * @return array
     */
    public function index(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            // return redirect('/');
        }
        $fechahoy = date('Y-m-d');
        // $otrointento = AgendaQ::where("estado","1")->whereBetween('fechaini', [$fechahoy.' 00:00:00', $fechahoy.' 23:59:59'])->get();
        /*$otrointento = AgendaQ::where("estado","1")->where('fechaini',$fechahoy)->get();
        $totales=0;
        if($otrointento=="[]"){
            $otrointento= 0;
        }else {
            $totales= $otrointento->count();
        }*/

        /*$agendaprogramadas= AgendaQ::where('estado','=','1')->get();

        $query = "SELECT SUBSTRING(fechaini,1,9) as fecha
                 FROM `agenda_quirofano` WHERE fecha.fecha ";

        $datesubstring = DB::select($query);
        $totales= count($);*/
        $nombres = DB::table('cama_paciente')
            ->join('paciente', 'paciente.id', '=', 'cama_paciente.id_paciente')
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'cama_paciente.id_paciente', 'cama_paciente.created_at', 'cama_paciente.id_cama')
            ->get();
        // dd($nombres);
        // $inicio = CamaPaciente::all();
        $totales = AgendaQ::where('estado', '=', '1')->where('fecha_total', '=', $fechahoy)->count();

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        $hospitalServices = app(HospitalServiceMenuService::class)
            ->visibleForCompany(session('id_empresa'));

        return view('hospital/index', ['totales' => $totales, 'nombres' => $nombres, 'doctores' => $doctores, 'seguros' => $seguros, 'especialidades' => $especialidades, 'id_especialidad' => null, 'id_doctor1' => null, 'id_seguro' => null, 'request' => $request, 'hospitalServices' => $hospitalServices, 'canConfigureHospitalServices' => Auth::user()->isSystemUser()]);
    }

    public function farmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $farmacia = Hospital_Producto::paginate(5);

        return view('hospital/farmacia', ['farmacia' => $farmacia]);
    }

    /*public function iniciobusca(Request $request){
    $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $nombres= CamaPaciente::where("id_paciente","like",$request->nombre1."%")->get();
        }
        elseif(($request->apellido1)!=""){
            $nombres= CamaPaciente::where("id_cama","like",$request->apellido1."%")->get();
        }
    return view('hospital/iniciobusca',['nombres'=>$nombres]);
    }*/
    public function buscadorfarmacia(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if (($request->codigo) != '') {
            $farmacia = Hospital_Producto::where('codigo', 'like', $request->codigo.'%')->get();
        } elseif (($request->nombre) != '') {
            $farmacia = Hospital_Producto::where('nombre', 'like', $request->nombre.'%')->get();
        }

        return view('hospital/buscadorfarmacia', ['farmacia' => $farmacia]);
    }

    public function agregarp()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/agregarp');
    }

    public function quirofano()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $agendados = DB::table('agenda_quirofano')
            ->join('users', 'users.id', '=', 'agenda_quirofano.id_doctor')
            ->join('paciente', 'paciente.id', '=', 'agenda_quirofano.id_paciente')
            ->select('users.nombre1', 'users.apellido1', 'agenda_quirofano.fechaini', 'agenda_quirofano.fechafin', 'agenda_quirofano.observaciones', 'paciente.nombre2', 'paciente.apellido2')
            ->paginate(5);
        // dd($agendados);
        // $paciente=hospital::with('nombre')->get();
        // $paciente=Paciente::find(1)->paciente:
        $quirofano = Quirofano::all();
        $agenda = AgendaQ::paginate(5);

        // dd($quirofano);
        return view('hospital/quirofano/quirofano', ['quirofano' => $quirofano, 'agenda' => $agenda, 'agendados' => $agendados]);
    }

    public function modalq()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/quirofano/modalq');
    }

    public function resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        $medico = $request->get('medico');
        $codigo = $request->get('codigo');
        $nota_evolucion = $request->get('nota_evolucion');

        $prescri = Evolucion_005::where('id_paciente', '=', $id_paciente)
            ->medico($medico)
            ->codigo($codigo)
            ->nota_evolucion($nota_evolucion)
            ->paginate(5);

        return view('hospital/emergencia/resultado', ['prescri' => $prescri, 'id_paciente' => $id_paciente, 'users' => $users]);
    }

    public function modaleditar($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = Evolucion_005::where('id', '=', $id)->paginate(5);

        return view('hospital/emergencia/modaleditar', ['editar' => $editar, 'id' => $id, 'campo' => $campo]);
    }

    public function registro()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/emergencia/registro');
    }

    public function vistac()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $id_tipo = 1;
        $muestra = Hospital::where('id_tipo', $id);

        return view('hospital/vistac', ['muestra' => $muestra]);
    }

    public function questionario($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $formnularioid = Paciente::find($id);

        // dd($formnularioid);
        return view('hospital/emergencia/questionario', ['formnularioid' => $formnularioid]);
    }

    public function modalprescripcion()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/modalprescripcion');
    }

    public function modalplan()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $plan = Plan_005::paginate(5);

        return view('hospital/emergencia/modalplan', ['plan' => $plan]);
    }

    public function emergencia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $firma = Firma_Usuario::all();
        $cama1 = Cama::all();
        $seguro = Seguro::all();
        $examenes = Examen::all();
        $paciente = Paciente::paginate(20);
        $user = User::all();
        // dd($paciente);
        $hospital_emergencia = DB::table('hospital_emergencia as a')
            // ->where('a.tipo', '2')
            // ->join('examen','hospital_emergencia.id_examen','=','id')
            ->join('examen as b', 'b.id', 'a.id_examen')
            ->select('a.id_examen')
            ->get();
        $milo = DB::table('hospital_emergencia as a')
            ->join('firma_usuario as b', 'b.id', 'a.id_nombre')
            ->select('a.id_nombre')
            ->get();

        return view('hospital/emergencia/emergencia', ['paciente' => $paciente, 'firma' => $firma, 'examenes' => $examenes, 'user' => $user, 'cama1' => $cama1, 'milo' => $milo, 'seguro' => $seguro]);
    }

    /*public function buscadore(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $paciente= paciente::where("nombre1","like",$request->nombre1."%")->get();
        }

        elseif(($request->apellido1)!=""){
            $paciente= paciente::where("apellido1","like",$request->apellido1."%")->get();
        }

        return view('hospital/emergencia/buscadore',['paciente'=>$paciente]);
    }*/
    public function autocompletar(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = '%';
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo.$value.'%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '".$seteo."'
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = ['value' => $product->completo, 'id' => $product->id];
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $productos;
    }

    public function autocompletar2(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = '%';
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo.$value.'%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, fecha_nacimiento 
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '".$seteo."'";
        $nombres = DB::select($query);
        if ($nombres != []) {
            $data = $nombres[0]->fecha_nacimiento;

            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }

    public function autocompletar3(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = '%';
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo.$value.'%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, historia_clinica
                    FROM paciente
                    WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '".$seteo."'";
        $nombres = DB::select($query);
        if ($nombres != []) {
            $data = $nombres[0]->historia_clinica;

            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }

    public function autocompletar4(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = '%';
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo.$value.'%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id_seguro,observacion,gruposanguineo,referido,antecedentes_fam,antecedentes_pat,id
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '".$seteo."'";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = ['value' => $product->completo, 'seguro' => $product->id_seguro, 'observacion' => $product->observacion, 'gruposanguineo' => $product->gruposanguineo, 'referido' => $product->referido, 'antecedentes_fam' => $product->antecedentes_fam, 'antecedentes_pat' => $product->antecedentes_pat, 'id' => $product->id];
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $productos;
    }

    public function registrome(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER['REMOTE_ADDR'];
        $idusuario1 = Auth::user()->id;
        $hola = Auth::user()->cedula;
        Hospital_Emergencia::create([
            'apellidos' => $request['apellido'],
            'anotaciones' => $request['anotaciones'],
            'id_examen' => $request['nameid'],
            'id_nombre' => $request['id_tipo'],
            'apellidos' => $request['apellido'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);

        return back();
    }

    public function agregarpa()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        // $paises = Pais::all();
        // $seguros = Seguro::all();

        // return view('hospital/emergencia/agregarpa', ['paises'=>$paises, 'seguros'=>$seguros]);
        return view('hospital/emergencia/agregarpa');
    }

    // public function registropac(Request $request){
    //     $opcion = '1';
    //     if ($this->rol_new($opcion)) {
    //         return redirect('/');
    //     }
    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;

    //     Paciente::create([
    //         'id' => $request['id_usuario'],
    //         'id_seguro' => $request['id_seguro'],
    //         'nombre1' => $request['nombre1'],
    //         'nombre2' => $request['nombre2'],
    //         'apellido1' => $request['apellido1'],
    //         'apellido2' => $request['apellido2'],
    //         'telefono1' => $request['telefono1'],
    //         'telefono2' => $request['telefono2'],
    //         'id_pais' => $request['id_pais'],
    //         'fecha_nacimiento' => $request['fecha_nacimiento'],
    //         'email' => $request['email'],
    //     ]);
    //     return back();
    // }

    public function registropac(Request $request)
    {
        return app(ClinicalHospitalPatientRegistrationService::class)->registropac($request);
    }

    public function formulario05(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $cie = Hc_Cie10::all();
        // dd($cie);
        $servicio = Evolucion_005::where('id_paciente', '=', 'id_paciente')->get();
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.fecha_nacimiento', 'paciente.sexo', 'ingreso_emer_008.id')
            ->get();

        // dd($users);
        return view('hospital/emergencia/formulario05', ['id_paciente' => $id_paciente, 'servicio' => $servicio, 'users' => $users, 'cie' => $cie]);
    }

    public function medicamentos(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->medicamentos($request);
    }

    public function formuarioevolucion(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->formuarioevolucion($request);
    }

    public function diagnostico005(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->diagnostico005($request);
    }

    public function medidas_generales(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->medidas_generales($request);
    }

    public function salas(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->salas($request);
    }

    public function tratamiento(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->tratamiento($request);
    }

    public function plan(Request $request)
    {
        return app(ClinicalHospitalFormService::class)->plan($request);
    }

    public function editarevolucion(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editarevolucion($request, $id);
    }

    public function resultado_diagnostico(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->resultado_diagnostico($request, $id_paciente);
    }

    public function modaleditar_diagnostico(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->modaleditar_diagnostico($request, $id);
    }

    public function editardiagnostico(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editardiagnostico($request, $id);
    }

    public function resultado_generales(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->resultado_generales($request, $id_paciente);
    }

    public function editar_generales(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_generales($request, $id);
    }

    public function editar_gene(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_gene($request, $id);
    }

    public function mostrar_resultadotratamiento(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->mostrar_resultadotratamiento($request, $id_paciente);
    }

    public function modal_tratamiento(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->modal_tratamiento($request, $id);
    }

    public function editar_tratamiento(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_tratamiento($request, $id);
    }

    public function resultado_plan(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->resultado_plan($request, $id_paciente);
    }

    public function modal_editarplan(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->modal_editarplan($request, $id);
    }

    public function editar_plan(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_plan($request, $id);
    }

    public function medicamentos_resultado(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->medicamentos_resultado($request, $id_paciente);
    }

    public function modal_medicamentos(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->modal_medicamentos($request, $id);
    }

    public function editar_medi(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_medi($request, $id);
    }

    public function salas_resultado(Request $request, $id_paciente)
    {
        return app(ClinicalHospitalFormService::class)->salas_resultado($request, $id_paciente);
    }

    public function editar_salas(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_salas($request, $id);
    }

    public function editar_modal(Request $request, $id)
    {
        return app(ClinicalHospitalFormService::class)->editar_modal($request, $id);
    }

    public function autocompletarcie(Request $request)
    {
        return app(ClinicalHospitalQueryService::class)->autocompletarcie($request);
    }

    public function detalles($id, Request $request)
    {
        return app(ClinicalHospitalQueryService::class)->detalles($id, $request);
    }

    public function primer_paso(Request $request)
    {

        return view('hospital.primerpaso');
    }

    public function segundo_paso(Request $request)
    {
        return view('hospital.segundopaso');
    }

    public function tercer_paso(Request $request)
    {

        return view('hospital.tercerpaso');
    }

    public function cuarto_paso(Request $request)
    {
        return view('hospital.cuartopaso');
    }

    public function quinto_paso(Request $request)
    {
        return view('hospital.quintopaso');
    }

    public function genericos(Request $request)
    {
        $genericos = [];
        if ($request['search'] != null) {
            $genericos = Medicina::where('medicina.nombre', 'LIKE', $request['search'].'%')->where('estado', '1')->select('medicina.nombre as text', 'medicina.id as id')->get();
        }

        return response()->json($genericos);
    }

    public function getResponse($id)
    {
        $porSi = '';

        return $id;
    }

    public function admision($id_paso)
    {
        abort_unless(in_array((int) $id_paso, [2, 3], true), 404);

        $fecha = date('Y-m-d');
        $companyId = app(ClinicalHospitalDestinationService::class)->companyId();
        $seguros = Seguro::where('id_empresa', $companyId)->where('inactivo', '1')->orderBy('nombre')->get();
        $procedimiento = Procedimiento::where('estado', 1)->whereNotNull('id_grupo_procedimiento')->orderBy('nombre')->get();
        $doctorRoleId = User::roleIdForName(['DOCTORES', 'DOCTOR']);
        $user = DB::table('users')->join('usuario_empresa as doctor_company',
            'doctor_company.id_usuario', '=', 'users.id')
            ->where('doctor_company.id_empresa', $companyId)
            ->where('users.id_tipo_usuario', $doctorRoleId)->where('users.estado', 1)
            ->select('users.*')->distinct()->orderBy('users.apellido1')->get();
        $salas = app(ClinicalHospitalDestinationService::class)->salas([(int) $id_paso]);
        $areaIngreso = (int) $id_paso === 2
            ? 'Emergencia'
            : ((int) $id_paso === 3 ? 'Hospitalizacion' : 'Atencion hospitalaria');

        $subseguros = Subseguro::join('seguros as s', 's.id', 'subseguro.id_seguro')
            ->where('s.id_empresa', $companyId)->where('subseguro.estado', 1)
            ->select('subseguro.nombre', 's.nombre as seguro')->orderBy('subseguro.nombre')->get();

        return view('hospital/admision', ['seguros' => $seguros, 'fecha' => $fecha, 'id_paso' => $id_paso, 'procedimiento' => $procedimiento, 'user' => $user, 'salas' => $salas, 'areaIngreso' => $areaIngreso, 'procedimiento2' => $procedimiento, 'subseguros' => $subseguros]);
    }

    public function ingreso_modulos(Request $request)
    {
        return app(ClinicalHospitalModuleService::class)->ingreso_modulos($request);
    }

    public function index_modulos(Request $request, $id_paso)
    {
        if ((int) $id_paso === 2) {
            return redirect()->route('hospital.emergencia');
        }
        abort_unless(in_array((int) $id_paso, [3, 4, 5, 6, 7], true), 404);
        $this->validate($request, ['fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d'.($request->filled('fecha_desde') ? '|after_or_equal:fecha_desde' : '')]);
        $companyId = app(ClinicalHospitalDestinationService::class)->companyId();
        $desde = trim((string) $request->input('fecha_desde', ''));
        $hasta = trim((string) $request->input('fecha_hasta', ''));
        $cedula = trim((string) $request->input('cedula', ''));
        $paciente = trim((string) $request->input('paciente', ''));
        $ho_estado_paso = app(ClinicalHospitalModuleService::class)->resolveHospitalModule($id_paso);
        abort_unless($ho_estado_paso, 404);
        $salas = app(ClinicalHospitalDestinationService::class)->salas([(int) $id_paso]);
        $encounterColumnAvailable = Schema::hasColumn('ho_solicitud', 'encounter_id');
        $encounterFoundationAvailable = $encounterColumnAvailable && Schema::hasTable('clinical_encounters');
        $solicitudes = Ho_Solicitud::with(['paciente', 'agenda.paciente', 'agenda.doctor1', 'manchester.ho_prioridad'])
            ->leftJoin('agenda as a', 'a.id', '=', 'ho_solicitud.id_agenda')
            ->join('paciente as p', 'p.id', '=', 'ho_solicitud.id_paciente');
        if ($encounterFoundationAvailable) {
            $solicitudes->leftJoin('clinical_encounters as ce', 'ce.id', '=', 'ho_solicitud.encounter_id')
                ->where(function ($tenant) use ($companyId) {
                    $tenant->where('ce.id_empresa', $companyId)->orWhere('a.id_empresa', $companyId);
                });
        } else {
            $solicitudes->where('a.id_empresa', $companyId);
        }
        $solicitudes->where('ho_solicitud.estado', 1)
            ->where(function ($query) use ($id_paso) {
                $query->where('ho_solicitud.estado_paso', $id_paso);
                if (in_array((int) $id_paso, [3, 6, 7], true)) {
                    $query->orWhereExists(function ($stay) use ($id_paso) {
                        $stay->select(DB::raw(1))->from('ho_hospitalizacion as h')
                            ->join('ho_traspaso_sala008 as t', 't.id', '=', 'h.id_traspaso')
                            ->whereColumn('t.id_solicitud', 'ho_solicitud.id')->where('h.estado', 1)->where('t.paso', $id_paso)
                            ->where('ho_solicitud.estado_paso', '<>', 5);
                    });
                }
            })->select('ho_solicitud.*', DB::raw('COALESCE(a.fechaini, ho_solicitud.fecha_ingreso) as fechaini'));
        if ($desde !== '') {
            $solicitudes->where('ho_solicitud.fecha_ingreso', '>=', $desde.' 00:00:00');
        }
        if ($hasta !== '') {
            $solicitudes->where('ho_solicitud.fecha_ingreso', '<=', $hasta.' 23:59:59');
        }
        if ($cedula !== '') {
            $solicitudes->where('ho_solicitud.id_paciente', $cedula);
        }
        if ($paciente !== '') {
            $solicitudes->where(function ($query) use ($paciente) {
                $like = '%'.$paciente.'%';
                $query->whereRaw('CONCAT_WS(" ", p.nombre1, p.nombre2, p.apellido1, p.apellido2) LIKE ?', [$like])
                    ->orWhereRaw('CONCAT_WS(" ", p.apellido1, p.apellido2, p.nombre1, p.nombre2) LIKE ?', [$like]);
            });
        }
        $solicitudes = $solicitudes->orderBy('ho_solicitud.fecha_ingreso', 'asc')->paginate(50);
        $formulariosDisponibles = app(ClinicalHospitalFormAvailabilityService::class)
            ->forSolicitudes($solicitudes->getCollection()->pluck('id')->all());
        $surgeryTransfers = collect();
        $surgeryStates = [];
        $surgeryAgendas = collect();
        if ((int) $id_paso === 5) {
            $solicitudIds = $solicitudes->getCollection()->pluck('id')->all();
            $surgeryTransfers = Ho_Traspaso_Sala008::with('sala')->whereIn('id_solicitud', $solicitudIds)
                ->where('paso', 5)->whereIn('estado', [1, 2])->orderByDesc('id')->get()
                ->groupBy('id_solicitud')->map(function ($items) {
                    return $items->first();
                });
            $encounterIds = $encounterColumnAvailable
                ? $solicitudes->getCollection()->pluck('encounter_id')->filter()->all()
                : [];
            $surgeryEvents = $encounterFoundationAvailable && Schema::hasTable('clinical_encounter_events') && $encounterIds !== []
                ? ClinicalEncounterEvent::whereIn('encounter_id', $encounterIds)
                    ->whereIn('event_type', ['HOSPITAL_SURGERY_BOUND', 'HOSPITAL_SURGERY_STARTED',
                        'HOSPITAL_SURGERY_FINISHED', 'HOSPITAL_SURGERY_RECOVERED'])->get()->groupBy('encounter_id')
                : collect();
            foreach ($solicitudes as $surgerySolicitud) {
                $events = $surgeryEvents->get($surgerySolicitud->encounter_id, collect());
                $types = $events->pluck('event_type')->all();
                $surgeryStates[$surgerySolicitud->id] = in_array('HOSPITAL_SURGERY_RECOVERED', $types, true)
                    ? 'RECUPERACION COMPLETADA'
                    : (in_array('HOSPITAL_SURGERY_FINISHED', $types, true)
                        ? 'CIRUGIA FINALIZADA / RECUPERACION PENDIENTE'
                        : (in_array('HOSPITAL_SURGERY_STARTED', $types, true)
                            ? 'EN QUIROFANO / OPERACION EN CURSO'
                            : (in_array('HOSPITAL_SURGERY_BOUND', $types, true)
                                ? 'PENDIENTE DE INICIO' : 'RECEPCION QUIRURGICA')));
            }
            if ($encounterIds !== [] && Schema::hasColumn('agenda', 'encounter_id')) {
                $surgeryAgendas = Agenda::with('doctor1')->whereIn('encounter_id', $encounterIds)
                    ->where('ho_tipo', 5)->where('estado', 1)->orderByDesc('id')->get()
                    ->unique('encounter_id')->keyBy('encounter_id');
            }
        }
        $pendingHospitalTransfers = Ho_Traspaso_Sala008::with(['paciente', 'sala'])
            ->join('ho_solicitud as s', 's.id', '=', 'ho_traspaso_sala008.id_solicitud');
        if ($encounterFoundationAvailable) {
            $pendingHospitalTransfers->join('clinical_encounters as e', 'e.id', '=', 's.encounter_id')
                ->where('e.id_empresa', $companyId)->whereNull('e.ended_at');
        } else {
            $pendingHospitalTransfers->join('agenda as transfer_agenda', 'transfer_agenda.id', '=', 's.id_agenda')
                ->where('transfer_agenda.id_empresa', $companyId);
        }
        $pendingHospitalTransfers = $pendingHospitalTransfers->where('s.estado', 1)
            ->where('ho_traspaso_sala008.estado', 1)->where('ho_traspaso_sala008.paso', $id_paso)
            ->orderBy('ho_traspaso_sala008.fecha')->get(['ho_traspaso_sala008.*']);

        return view('hospital/modulos/index', compact('solicitudes', 'formulariosDisponibles', 'salas',
            'ho_estado_paso', 'desde', 'hasta', 'cedula', 'paciente', 'pendingHospitalTransfers',
            'surgeryTransfers', 'surgeryStates', 'surgeryAgendas'));
    }

    public function buscador_vista(Request $request)
    {
        return app(ClinicalHospitalModuleService::class)->buscador_vista($request);
    }

    public function traspaso_modulo($id)
    {
        $solicitud = Ho_Solicitud::findOrFail($id);
        app(ClinicalHospitalTransferService::class)->authorize($solicitud, 'hospital.transfer.request', ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
        if (in_array((int) $solicitud->estado_paso, [1, 2], true)) {
            return response('<a class="btn btn-primary" href="'.e(route('hospital.emergencia', ['solicitud' => $id, 'accion' => 'destino'])).'">Decision medica de Emergencia</a>');
        }
        $pasos = HoEstadoPaso::whereIn('id', [3, 5, 6, 7])->where('estado', 1)->get();
        $salas = app(ClinicalHospitalDestinationService::class)->salas([3, 5, 6, 7]);
        $activeOrders = app(ClinicalTransferReconciliationService::class)->previewActiveOrders($id);

        return view('hospital/modulos/traspaso', compact('solicitud', 'pasos', 'salas', 'activeOrders'));
    }

    public function guardar_traspaso(Request $request)
    {
        return app(ClinicalHospitalModuleService::class)->guardar_traspaso($request);
    }

    public function subir_formularios($id)
    {
        $solicitud = $this->hospitalRequestForActiveCompany($id);

        $ho_archivos = Ho_Archivos_Solicitud::where('id_solicitud', $solicitud->id)->get();

        return view('hospital/modulos/archivos', ['solicitud' => $solicitud, 'ho_archivos' => $ho_archivos]);
    }

    public function guardar_formularios(Request $request)
    {

        $this->validate($request, [
            'id_solicitud' => 'required',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
        ]);

        DB::beginTransaction();
        try {

            $id_solicitud = $request->id_solicitud;
            $this->hospitalRequestForActiveCompany($id_solicitud);
            $nombre_original = $request['archivo']->getClientOriginalName();

            $extension = $request['archivo']->getClientOriginalExtension();
            // dd($extension);

            $nuevo_nombre = 'formularios'.date('YmdHis').'.'.$extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = base_path().'/storage/app/avatars/'.$nuevo_nombre;

            if ($r1) {
                $arr_archivo = [
                    'id_solicitud' => $id_solicitud,
                    'nombre_archivo' => $nuevo_nombre,
                ];

                Ho_Archivos_Solicitud::create($arr_archivo);
            }

            DB::commit();

            return ['respuesta' => 'success', 'msj' => 'success'];
        } catch (\Exception $e) {
            DB::rollback();

            return ['respuesta' => 'error', 'msj' => 'error', 'exp' => $e->getMessage()];
        }
    }

    public function detalle_formularios($id_sol)
    {

        $solicitud = $this->hospitalRequestForActiveCompany($id_sol);
        $ho_archivos = Ho_Archivos_Solicitud::where('id_solicitud', $solicitud->id)->where('estado', '1')->get();

        return view('hospital/modulos/detalle_archivos', ['ho_archivos' => $ho_archivos, 'solicitud' => $solicitud]);
    }

    public function descarga_formulario($name)
    {
        $imagen = $this->hospitalFileForActiveCompany($name);
        // $paciente        = paciente::find($imagen->id_paciente);
        $nombre_archivo = null;
        $path = $this->hospitalFilePath($imagen->nombre_archivo);
        $nombre_archivo = $nombre_archivo.'_'.$imagen->nombre_archivo;

        if ($nombre_archivo == null) {
            $nombre_archivo = $imagen->nombre_archivo;
        } else {
            $nombre_temporal = $imagen->nombre_archivo;
            $datos = explode('.', $nombre_temporal);
            if (count($datos) == 2) {
                $extension = $datos[1];
                $nombre_archivo = $nombre_archivo.'.'.$extension;
            } else {
                $nombre_archivo = $imagen->nombre_archivo;
            }
        }
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function eliminar_formulario($id)
    {

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        $imagen = $this->hospitalFileForActiveCompany($id);

        $arr = [
            'estado' => 0,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ];

        $imagen->update($arr);
    }

    public function ver_formulario($id_archivo)
    {

        $nombreArchivo = $this->hospitalFileForActiveCompany($id_archivo);
        if (! is_null($nombreArchivo)) {
            $pathtoFile = $this->hospitalFilePath($nombreArchivo->nombre_archivo);

            // dd($pathtoFile);
            return response()->file($pathtoFile);
        }

        return 'no cargado';
    }

    public function listado_paciente($id_paciente)
    {
        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('id_empresa', session('id_empresa'))
            ->where('estado', 1)->select('fecha_orden', 'id')->first();
        if (! is_null($examen)) {
            $arr_listado[0] = [$examen->fecha_orden, 'LABORATORIO', $examen->id];
        }
        $procedimiento = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_protocolo as proto', 'proto.hcid', '=', 'hc.hcid')->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', '=', 'hc.hcid')->join('hc_procedimiento_final as hc_procf', 'hc_procf.id_hc_procedimientos', '=', 'hc_proc.id')->join('procedimiento as proc', 'proc.id', '=', 'hc_procf.id_procedimiento')->where('agenda.id_paciente', $id_paciente)->where('agenda.id_empresa', session('id_empresa'))->where('agenda.estado', 1)->where('agenda.proc_consul', 1)->wherenotNull('proto.hallazgos')->wherenotNull('proc.id_grupo_procedimiento')->select('agenda.id as id_agenda', 'agenda.fechaini', 'proto.id_hc_procedimientos', 'proc.nombre', 'proto.id as id_protocolo')->first();
        if (! is_null($procedimiento)) {
            $arr_listado[1] = [$procedimiento->fechaini, $procedimiento->nombre, $procedimiento->id_protocolo];
        }
        $consulta = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_evolucion as evo', 'evo.hcid', '=', 'hc.hcid')->join('especialidad as esp', 'esp.id', '=', 'agenda.espid')->where('agenda.id_paciente', $id_paciente)->where('agenda.id_empresa', session('id_empresa'))->where('agenda.estado', 1)->where('agenda.proc_consul', 0)->where('evo.secuencia', '=', '0')->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini', 'desc')->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion', 'esp.nombre')->first();
        if (! is_null($consulta)) {
            $arr_listado[2] = [$consulta->fechaini, $consulta->nombre, $consulta->id_hc_evolucion];
        }
    }

    public function modal_laboratorio($id)
    {
        return view('hospital/laboratorio/modal_resultados', ['id' => $id]);
    }

    public function modal_consultas($id)
    {
        $evolucion = Hc_Evolucion::find($id);

        $hc_cie10 = Hc_Cie10::where('hcid', $evolucion->hcid)->get();

        $c10_arr = [];
        if (count($hc_cie10) > 0) {
            foreach ($hc_cie10 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr[$c10->id] = $c3->descripcion;
                }
                if ($c4 != null) {
                    $c10_arr[$c10->id] = $c4->descripcion;
                }
            }
        }

        return view('hospital/consulta/modal_consulta', ['id' => $id, 'evolucion' => $evolucion, 'hc_cie10' => $hc_cie10, 'c10_arr' => $c10_arr]);
    }

    public function modal_estudio($id)
    {
        return view('hospital/imagenes/modal_estudio_vt', ['id' => $id]);
    }

    public function planillar($id_solicitud)
    {

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solicitud);
        if (is_null($solicitud)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN SOLICITUD', 'linea' => ''];
        }

        $planilla = Planilla::where('estado', 1)->where('id_solicitud', $solicitud)->first();

        $agenda = $solicitud->agenda;
        if (is_null($agenda)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN AGENDA', 'linea' => ''];
        }

        $hc = $agenda->historia_clinica;
        if (is_null($hc)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN HISTORIA CLINICA', 'linea' => ''];
        }

        $hc_proc = $hc->procedimiento->first();
        if (is_null($hc_proc)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN PROCEDIMIENTO', 'linea' => ''];
        }

        $plantilla = Insumo_Plantilla_Control::where('estado', 1)->where('codigo', 'HOSPITAL')->first();
        if (is_null($plantilla)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN PLANTILLA', 'linea' => ''];
        }

        if (! is_null($planilla)) {

            $id_planilla = $planilla->id;
        } else {

            $paciente = $hc->paciente;

            $id_planilla = Planilla::insertGetId([
                'codigo' => 'HOSPITAL',
                'id_agenda' => $solicitud->id_agenda,
                'fecha' => date('Y-m-d H:i:s'),
                'id_hc_procedimiento' => $hc_proc->id,
                'id_planilla' => $plantilla->id,
                'observacion' => 'Paciente '.$paciente->apellido1.' '.$paciente->apellido2.' | '.$paciente->nombre1.' '.$paciente->nombre2,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_solicitud' => $id_solicitud,

            ]);
        }

        return ['respuesta' => 'ok', 'msj' => $id_planilla, 'linea' => ''];
    }

    public function planillar_detalle($id_solicitud)
    {

        $solicitud = Ho_Solicitud::find($id_solicitud);

        $logs = $solicitud->log;

        $planilla = Planilla::where('id_solicitud', $id_solicitud)->first();
        $detalles = [];
        if (! is_null($planilla)) {
            $detalles = $planilla->detalles_validos;
        }

        return view('hospital/planilla/editar', ['logs' => $logs, 'detalles' => $detalles, 'id_solicitud' => $id_solicitud]);
    }

    public function planillar_detalle_aplicar($id_log)
    {

        $log = Ho_Log_Solicitud::find($id_log);
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        // dd($log->paso);
        $estado_insumo = $log->paso->paso_insumos->first();
        // dd($estado_insumo);
        $plantilla = Insumo_Plantilla_Control::where('estado', 1)->where('codigo', 'HOSPITAL')->first();
        if (is_null($plantilla)) {
            return ['respuesta' => 'error', 'msj' => 'ERROR EN PLANTILLA', 'linea' => ''];
        }
        $insumo = null;
        $precio = 0;
        if (! is_null($estado_insumo)) {
            $insumo = $estado_insumo->insumo; // dd($insumo);
            if (! is_null($insumo)) {
                $codigo = $insumo->codigo;
                $producto_insumo = Ct_productos_insumos::where('id_insumo', $insumo->id)->first(); // dd($producto_insumo);
                if (! is_null($producto_insumo)) {
                    $producto = $producto_insumo->ct_producto;
                    if (! is_null($producto)) {
                        $precio = $producto->valor_total_paq;
                    }
                }
            }
        }
        $idhc_proc = null;
        $namepaciente = null;
        $agend = $log->agenda;
        if (isset($agend)) {
            $history = $agend->historia_clinica;
            $namepaciente = $agend->paciente->apellido1.' '.$agend->paciente->apellido2.' '.$agend->paciente->nombre1;
            if (isset($history)) {
                $hc_proc = $history->hc_procedimientos;
                if (isset($hc_proc)) {
                    $idhc_proc = $hc_proc->first()->id;
                }
            }
        }

        $cabecera = Planilla::where('id_solicitud', $log->id_ho_solicitud)->first();
        // dd($cabecera,$log);

        if (is_null($cabecera)) {
            $id_cab = Planilla::insertGetId([
                'codigo' => 'HOSPITAL',
                'id_agenda' => $log->id_agenda,
                'fecha' => date('Y-m-d H:i:s'),
                'id_hc_procedimiento' => $idhc_proc,
                'id_planilla' => $plantilla->id,
                'observacion' => 'Paciente '.$namepaciente,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_solicitud' => $log->id_ho_solicitud,
            ]);
        } else {
            $id_cab = $cabecera->id;
        }

        Planilla_Detalle::create([
            // 'id_planilla_cabecera' => $cabecera->id,
            'id_planilla_cabecera' => $id_cab,
            // 'movimiento'           => $request['movimiento'][$i],
            'cantidad' => '1',
            // 'serie'                => $request['serie'][$i],
            // 'lote'                 => $request['lote'][$i],
            'check' => '1',
            // 'fecha_vencimiento'    => $request['fecha_vencimiento'][$i],
            'codigo' => $codigo,
            'precio' => $precio,
            'estado' => 1,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'observacion' => 'HOSPITAL: PASO '.$log->paso->nombre_paso,
            // 'tipo_plantilla'         => $tipo_plantilla,
            // 'id_movimiento_paciente' => $id_movimiento_paciente,
            // 'id_bodega'              => $id_bodega
            'id_log_solic' => $id_log,

        ]);

        return 'ok';
    }

    public function aplicar_todos($id_solic)
    {

        $solicitud = Ho_Solicitud::find($id_solic);

        $logs = $solicitud->log;

        foreach ($logs as $log) {

            $this->planillar_detalle_aplicar($log->id);
        }

        return 'ok';
    }

    public function crea_solic_ambulatoria($id_agenda)
    {
        try {
            return app(AmbulatoryAdmissionService::class)->admit(
                $id_agenda,
                Auth::user()->id,
                request()->ip(),
                [
                    'code' => request()->input('clinical_override'),
                    'reason' => request()->input('clinical_override_reason'),
                ]
            );
        } catch (ClinicalEncounterConflictException $exception) {
            return [
                'estado' => 'conflicto_clinico',
                'codigo' => 'R-01',
                'msn' => $exception->getMessage(),
                'episodio_activo' => $exception->conflictingEncounterId(),
                'requiere_autorizacion' => true,
                'puede_autorizar' => Auth::user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR']),
            ];
        }
    }

    private function hospitalRequestForActiveCompany($id)
    {
        return Ho_Solicitud::join('agenda as tenant_agenda', 'tenant_agenda.id', '=', 'ho_solicitud.id_agenda')
            ->where('ho_solicitud.id', $id)
            ->where('tenant_agenda.id_empresa', session('id_empresa'))
            ->select('ho_solicitud.*')
            ->firstOrFail();
    }

    private function hospitalFileForActiveCompany($id)
    {
        return Ho_Archivos_Solicitud::join('ho_solicitud as tenant_request', 'tenant_request.id', '=', 'ho_archivos_solicitud.id_solicitud')
            ->join('agenda as tenant_agenda', 'tenant_agenda.id', '=', 'tenant_request.id_agenda')
            ->where('ho_archivos_solicitud.id', $id)
            ->where('tenant_agenda.id_empresa', session('id_empresa'))
            ->select('ho_archivos_solicitud.*')
            ->firstOrFail();
    }

    private function hospitalFilePath($filename)
    {
        $filename = trim((string) $filename);
        abort_if($filename === '' || $filename !== basename($filename), 404);

        $root = storage_path('app/avatars');
        $rootPath = realpath($root);
        $filePath = realpath($root.DIRECTORY_SEPARATOR.$filename);
        abort_if($rootPath === false || $filePath === false, 404);
        abort_unless(strpos($filePath, $rootPath.DIRECTORY_SEPARATOR) === 0, 404);

        return $filePath;
    }

    public function crea_form_008($id_solic)
    {

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solic);
        if (! is_null($solicitud)) {
            $form008 = app(ClinicalEmergencyFormService::class)->ensure($solicitud, [
                'fecha_creacion' => $solicitud->fecha_ingreso,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_agenda' => $solicitud->id_agenda,
            ]);

            return ['estado' => 'ok', 'id_form' => $form008->id];
        } else {

            return ['estado' => 'err', 'msn' => 'Sin Solicitud'];
        }
    }

    public function edit_solic_ambulatoria($id)
    {

        // dd($id);

        $solicitud = Ho_Solicitud::find($id); // dd($solicitud);
        if (! is_null($solicitud)) {

            $procedimientos = $this->obtener_procedimientos_realizados($solicitud->id_agenda);
            $form008 = $solicitud->form008->first();
            // dd("entro");

            return view('hospital/ambulatorio/solic', ['solicitud' => $solicitud, 'procedimientos' => $procedimientos, 'form008' => $form008]);
        } else {

            return ['estado' => 'err', 'msn' => 'Sin Agenda'];
        }
    }

    public function edit_solic_ambulatoria_admision($id)
    {

        // dd($id);

        $solicitud = Ho_Solicitud::find($id); // dd($solicitud);
        if (! is_null($solicitud)) {

            // $procedimientos = $this->obtener_procedimientos_realizados($solicitud->id_agenda);
            $procedimientos = null;
            $form008 = $solicitud->form008->first();
            // dd("entro");

            return view('hospital/ambulatorio/solic_admin', ['solicitud' => $solicitud, 'procedimientos' => $procedimientos, 'form008' => $form008]);
        } else {

            return ['estado' => 'err', 'msn' => 'Sin Agenda'];
        }
    }

    public function crea_edita_solicitud($id_agenda)
    {

        // dd($id_agenda);
        $solicitud = Ho_Solicitud::where('id_agenda', $id_agenda)->where('estado', '1')->first();
        if (! is_null($solicitud)) {

            return redirect()->route('hospital.edit_solic_ambulatoria', ['id' => $solicitud->id]);
        } else {

            $this->crea_solic_ambulatoria($id_agenda);
            $solicitud = Ho_Solicitud::where('id_agenda', $id_agenda)->where('estado', '1')->first();
            if (! is_null($solicitud)) {

                return redirect()->route('hospital.edit_solic_ambulatoria', ['id' => $solicitud->id]);
            } else {

                return redirect()->back();
            }
        }
    }

    public function crea_edita_solicitud_admision($id_agenda)
    {

        // dd($id_agenda);
        $solicitud = Ho_Solicitud::where('id_agenda', $id_agenda)->where('estado', '1')->first();
        if (! is_null($solicitud)) {

            return redirect()->route('hospital.edit_solic_ambulatoria_admision', ['id' => $solicitud->id]);
        } else {

            $this->crea_solic_ambulatoria($id_agenda);
            $solicitud = Ho_Solicitud::where('id_agenda', $id_agenda)->where('estado', '1')->first();
            if (! is_null($solicitud)) {

                return redirect()->route('hospital.edit_solic_ambulatoria_admision', ['id' => $solicitud->id]);
            } else {

                return redirect()->back();
            }
        }
    }

    public function crea_datos_paciente($id_paciente)
    {

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;

        $paciente = Paciente::find($id_paciente);
        if (! is_null($paciente)) {
            $datos_pac = Ho_Datos_Paciente::where('id_paciente', $id_paciente)->first();
            if (is_null($datos_pac)) {
                $arr_pac = [
                    'id_paciente' => $id_paciente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];

                $id_datos_pac = Ho_Datos_Paciente::insertGetId($arr_pac);

                return ['estado' => 'ok', 'id_datos_pac' => $id_datos_pac];
            } else {

                return ['estado' => 'ok', 'id_datos_pac' => $datos_pac->id];
            }
        } else {

            return ['estado' => 'err', 'msn' => 'Sin Solicitud'];
        }
    }

    public function obtener_procedimientos_realizados($id_agenda)
    {

        $agenda = Agenda::find($id_agenda);
        $historia = $agenda->historia_clinica;

        $procs = $historia->procedimiento;
        $mas = true;
        $texto = '';
        // dd($procs);
        foreach ($procs as $proc) {
            // dd($proc);

            $finales = $proc->hc_procedimiento_f;
            foreach ($finales as $value2) {
                if ($mas == true) {
                    $texto = $texto.$value2->procedimiento->nombre;
                    $mas = false;
                } else {
                    $texto = $texto.' + '.$value2->procedimiento->nombre;
                }
            }
        }

        return $texto;
    }

    public function guardar_f008(Request $request)
    {
        return app(ClinicalEmergencyFormUpdateService::class)->update($request, true);
    }

    public function guardar_f008_admision(Request $request)
    {
        return app(ClinicalEmergencyFormUpdateService::class)->update($request, false);
    }
}
