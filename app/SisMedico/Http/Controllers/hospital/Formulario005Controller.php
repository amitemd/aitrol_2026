<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_solicitud;
use Sis_medico\User;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Paciente;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_child_pugh;
use Sis_medico\Medicina;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Ho_Evoluciones_Enfermeria;
use Sis_medico\Ho_Hc_Receta_Evolucion;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;

class Formulario005Controller extends Controller
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

    private function solicitudForActiveCompany($id)
    {
        $companyId = session('id_empresa');
        abort_if(empty($companyId), 403, 'No existe una empresa activa.');

        $solicitud = Ho_Solicitud::with(['paciente', 'encounter', 'agenda.historia_clinica'])
            ->leftJoin('agenda as tenant_agenda', 'tenant_agenda.id', '=', 'ho_solicitud.id_agenda')
            ->leftJoin('clinical_encounters as tenant_encounter', 'tenant_encounter.id', '=', 'ho_solicitud.encounter_id')
            ->where('ho_solicitud.id', $id)
            ->where(function ($tenant) use ($companyId) {
                $tenant->where('tenant_encounter.id_empresa', $companyId)
                    ->orWhere('tenant_agenda.id_empresa', $companyId);
            })
            ->select('ho_solicitud.*')
            ->first();

        abort_unless($solicitud, 404, 'No se encontró la solicitud hospitalaria en la empresa activa.');
        return $solicitud;
    }

    private function nursingEvolutionForActiveCompany($id)
    {
        $companyId = session('id_empresa');
        abort_if(empty($companyId), 403, 'No existe una empresa activa.');

        return Ho_Evoluciones_Enfermeria::join('ho_solicitud as tenant_request', 'tenant_request.id', '=', 'ho_evoluciones_enfermeria.id_solicitud')
            ->join('agenda as tenant_agenda', 'tenant_agenda.id', '=', 'tenant_request.id_agenda')
            ->where('ho_evoluciones_enfermeria.id', $id)
            ->where('tenant_agenda.id_empresa', $companyId)
            ->select('ho_evoluciones_enfermeria.*')
            ->firstOrFail();
    }

    private function recipeForSolicitation($id, Ho_Solicitud $solicitud)
    {
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'No se encontró la historia clínica del paciente.');

        return hc_receta::where('id', $id)
            ->where('id_hc', $historia->hcid)
            ->firstOrFail();
    }

    private function episodeHistory(Ho_Solicitud $solicitud)
    {
        $history = $solicitud->id_agenda
            ? Historiaclinica::where('id_agenda', $solicitud->id_agenda)
                ->where('id_paciente', $solicitud->id_paciente)
                ->when($solicitud->encounter_id, function ($query) use ($solicitud) {
                    $query->where('encounter_id', $solicitud->encounter_id);
                })->first()
            : null;
        if ($history || !$solicitud->encounter_id) return $history;
        return Historiaclinica::where('encounter_id', $solicitud->encounter_id)
            ->where('id_paciente', $solicitud->id_paciente)->orderByDesc('hcid')->first();
    }

    private function recipeDetailForActiveCompany($id)
    {
        return hc_receta_detalle::join('hc_receta as tenant_recipe', 'tenant_recipe.id', '=', 'hc_receta_detalle.id_hc_receta')
            ->join('historiaclinica as tenant_history', 'tenant_history.hcid', '=', 'tenant_recipe.id_hc')
            ->join('agenda as tenant_agenda', 'tenant_agenda.id', '=', 'tenant_history.id_agenda')
            ->where('hc_receta_detalle.id', $id)
            ->where('tenant_agenda.id_empresa', session('id_empresa'))
            ->select('hc_receta_detalle.*')
            ->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_005($id_solicitud)
    {
        // El F-005 solo puede usar una historia del episodio actual.
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $paciente = Paciente::find($solicitud->id_paciente);
        abort_unless($paciente, 404, 'No se encontró el paciente de la solicitud hospitalaria.');

        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        if (!$historia) {
            return view('hospital.formulario_005.sin_historia', [
                'solicitud' => $solicitud,
                'paciente' => $paciente,
            ]);
        }

        // Mantiene compatibles las vistas antiguas que leen la agenda desde la solicitud.
        if (!$agenda && $historia->agenda) {
            $solicitud->setRelation('agenda', $historia->agenda);
            $agenda = $solicitud->agenda;
        }

        $log = $solicitud->log()->latest('id')->first();

        $evolucion = $historia->evoluciones()->latest('id')->first();
        $evolucion_enfermeria = Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->latest()->first();
        $child_pugh = is_null($evolucion) ? null : $evolucion->child_pug()->first();
        $examenes = Examen_Orden::where('id_paciente', $solicitud->id_paciente)->latest('created_at')->first();
        
        //return view('hospital.formulario_005.index_005', ['examenes' => $examenes, 'paciente' => $paciente, 'solicitud' => $solicitud, 'log' => $log, 'evolucion' => $evolucion, 'child_pugh' => $child_pugh]);
        // FIN VERSION ANTERIOR        

        $id_paciente = $paciente->id;
        
        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('id_empresa', session('id_empresa'))
            ->where('estado',1)->select('fecha_orden','id')->first();
        if(!is_null($examen)){
            $tiene_resultados = DB::table('examen_resultado')
                ->where('id_orden', $examen->id)
                ->where('certificado', 1)
                ->exists();
            $arr_listado[0] = [$examen->fecha_orden, 'LABORATORIO', $examen->id, $tiene_resultados];
        }
        
        $procedimiento = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')
        ->join('hc_protocolo as proto', 'proto.hcid','=','hc.hcid')
        ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc','=','hc.hcid')
        ->join('hc_procedimiento_final as hc_procf','hc_procf.id_hc_procedimientos','=','hc_proc.id')
        ->join('procedimiento as proc','proc.id','=','hc_procf.id_procedimiento')
        ->where('agenda.id_paciente', $id_paciente)
        ->where('agenda.id_empresa', session('id_empresa'))
        ->where('agenda.estado',1)
        ->where('agenda.proc_consul',1)
        ->wherenotNull('proto.hallazgos')
        ->wherenotNull('proc.id_grupo_procedimiento')
        ->select('agenda.id as id_agenda','agenda.fechaini','proto.id_hc_procedimientos','proc.nombre', 'proto.id as id_protocolo')
        ->first();
        if(!is_null($procedimiento)){
            $arr_listado[1] = [$procedimiento->fechaini,$procedimiento->nombre, $procedimiento->id_protocolo];
        }
        
        $consulta = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')
        ->join('hc_evolucion as evo','evo.hcid','=','hc.hcid')
        ->join('especialidad as esp','esp.id','=','agenda.espid')
        ->where('agenda.id_paciente', $id_paciente)
        ->where('agenda.id_empresa', session('id_empresa'))
        ->where('agenda.estado',1)
        ->where('agenda.proc_consul',0)
        ->where('evo.secuencia','=','0')
        ->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini','desc')
        ->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion','esp.nombre')->first();
        if(!is_null($consulta)){
            $arr_listado[2] = [$consulta->fechaini,$consulta->nombre, $consulta->id_hc_evolucion];
        }
        //dd($examen,$procedimiento,$consulta);
        $notas = null;

        
        //dd($arr_listado);
        //nuevo cambio

        
        $sql = "SELECT sol.fecha_ingreso, a.id_paciente,p.religion,TIMESTAMPDIFF(DAY,sol.fecha_ingreso, CURDATE()) as dias_hospitalizados,
        CONCAT(p.apellido1, ' ', p.apellido2, ' ', p.nombre1, ' ', p.nombre2) as nombre_paciente,seg.nombre as seguro, 
        CONCAT(u.apellido1, ' ', u.apellido2, ' ', u.nombre1, ' ', u.nombre2) as nombre_doctor,
        p.fecha_nacimiento, YEAR(CURDATE())-YEAR(p.fecha_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.fecha_nacimiento,'%m-%d'), 0 , -1 ) as edad, 
        if(p.antecedentes_pat is null,'No tiene antecedentes patologicos',p.antecedentes_pat) as ante_patologicos, 
        if(p.antecedentes_fam is null,'No tiene antecedentes familiares',p.antecedentes_fam) as ante_familiares, 
        if(p.antecedentes_quir is null,'No tiene antecedentes quirurgicos',p.antecedentes_quir) as ante_quirurgicos from ho_solicitud sol
        join agenda a on a.id = sol.id_agenda
        join paciente p on p.id = a.id_paciente 
        join users u on u.id = a.id_doctor1
        join seguros seg on seg.id = a.id_seguro
        where sol.id = ? and a.id_empresa = ?";

        $data = DB::select($sql, array($id_solicitud, session('id_empresa')));
        $data = $data[0] ?? (object) [
            'fecha_ingreso' => $solicitud->fecha_ingreso,
            'id_paciente' => $solicitud->id_paciente,
            'religion' => $paciente->religion ?? null,
            'dias_hospitalizados' => null,
            'nombre_paciente' => trim(($paciente->apellido1 ?? '') . ' ' . ($paciente->apellido2 ?? '') . ' ' . ($paciente->nombre1 ?? '') . ' ' . ($paciente->nombre2 ?? '')),
            'seguro' => null,
            'nombre_doctor' => null,
            'fecha_nacimiento' => $paciente->fecha_nacimiento ?? null,
            'edad' => null,
            'ante_patologicos' => $paciente->antecedentes_pat ?? null,
            'ante_familiares' => $paciente->antecedentes_fam ?? null,
            'ante_quirurgicos' => $paciente->antecedentes_quir ?? null,
        ];

        
        
        $alergias = Paciente_Alergia::where('id_paciente',$paciente->id)
        ->join('principio_activo as pa','pa.id','paciente_alergia.id_principio_activo')
        ->where('paciente_alergia.id_paciente', $paciente->id)
        ->pluck('pa.nombre');
        $str_alergias = $alergias->implode(', ');
        $epicrisis_final = Hc_Epicrisis_Final::where('id_ho_solicitud', $solicitud->id)->first();

        return view('hospital.formulario_005.index_005', ['arr_listado'=>$arr_listado,'examenes' => $examenes, 'paciente' => $paciente, 'solicitud' => $solicitud, 'log' => $log, 'historia_hospital' => $historia, 'evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'evolucion_enfermeria' => $evolucion_enfermeria, 'notas' => $notas, 'data' => $data, 'str_alergias' => $str_alergias, 'epicrisis_final' => $epicrisis_final]);
    }

    public function f5_evolucion($id)
    {
        $solicitud = Ho_Solicitud::find($id);
        abort_if(is_null($solicitud), 404, 'No se encontró la solicitud hospitalaria.');

        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        $hcProc = $historia ? hc_procedimientos::where('id_hc', $historia->hcid)
            ->orderBy('id', 'desc')->first() : null;
        $solicitud->id_agenda = $agenda ? $agenda->id : null;
        $solicitud->hcid = $historia ? $historia->hcid : null;
        $solicitud->id_hcproc = $hcProc ? $hcProc->id : null;

        $evoluciones = $hcProc
            ? Hc_Evolucion::where('hc_id_procedimiento', $hcProc->id)->orderBy('created_at', 'DESC')->get()
            : collect();

        // Cada tarjeta debe identificar al profesional que realmente registró
        // esa evolución. La especialidad se obtiene de las asignaciones del
        // mismo usuario y no del médico que esté conectado actualmente.
        $authorIds = $evoluciones->pluck('id_usuariocrea')->filter()
            ->map(function ($id) { return (string) $id; })->unique()->values();
        $authors = User::withoutGlobalScopes()->whereIn('id', $authorIds)->get()->keyBy(function ($user) {
            return (string) $user->id;
        });
        $specialtiesByAuthor = DB::table('user_espe as author_specialty')
            ->join('especialidad as specialty', 'specialty.id', '=', 'author_specialty.espid')
            ->whereIn('author_specialty.usuid', $authorIds)
            ->orderBy('author_specialty.id')
            ->get(['author_specialty.usuid', 'specialty.nombre'])
            ->groupBy(function ($specialty) { return (string) $specialty->usuid; });

        $evoluciones->each(function ($evolution) use ($authors, $specialtiesByAuthor) {
            $authorId = (string) $evolution->id_usuariocrea;
            $author = $authors->get($authorId);
            $nameParts = $author ? [
                $author->nombre1, $author->nombre2, $author->apellido1, $author->apellido2,
            ] : [];
            $doctorName = trim(implode(' ', array_filter($nameParts, function ($part) {
                $value = strtoupper(trim((string) $part));
                return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
            })));
            $specialtyNames = $specialtiesByAuthor->get($authorId, collect())
                ->pluck('nombre')->filter()->unique()->implode(', ');

            $evolution->setAttribute('medico_nombre', $doctorName ?: 'Profesional no identificado');
            $evolution->setAttribute('medico_especialidad', $specialtyNames ?: 'Especialidad no registrada');
        });

        $receta = hc_receta::with('detalles')
            ->where('id_hc', $solicitud->hcid)
            ->latest('created_at')
            ->first();
        $detalles = is_null($receta) ? collect() : $receta->detalles;


        return view('hospital.formulario_005.evolucion', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones, 'receta' => $receta, 'detalles'=>$detalles]);
    }

    public function guardar_evolucion(Request $request, $id_evol)
    {

        //dd($request->all());

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        // El formulario antiguo envía id_solicitud y algunas pantallas envían
        // solicitud_id; aceptar ambos mantiene compatibilidad entre vistas.
        $id_solicitud = $request->input('solicitud_id', $request->input('id_solicitud'));
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $evolucion = Hc_Evolucion::find($id_evol);
        abort_if(is_null($evolucion), 404);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia && (string) $evolucion->hcid === (string) $historia->hcid, 404);

        $id_hc_rec_evo = Ho_Hc_Receta_Evolucion::where('id_evolucion', $id_evol)->first();
        $receta = null;
        $detalles = [];
        if(!is_null($id_hc_rec_evo)){
            $receta = hc_receta::with('detalles')->find($id_hc_rec_evo->id_receta);
            $detalles = is_null($receta) ? collect() : $receta->detalles;
        }

        if (!is_null($evolucion)) {
            $arr_evolucion = [
                'motivo'            => $request['motivo'],
                'cuadro_clinico'    => $request['n_evolucion'],
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $evolucion->update($arr_evolucion);

            $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

            if (!is_null($child_pugh)) {
                $arr_chill = [
                    'examen_fisico'     => $request['examen_fisico'],
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $child_pugh->update($arr_chill);
            }
        }

        return view('hospital.formulario_005.nueva_evolucion', ['evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'solicitud' => $solicitud, 'receta'=>$receta, 'id_hc_rec_evo'=>$id_hc_rec_evo, 'detalles'=>$detalles]);
    }

    public function evolucion_detalle($id, Request $request)
    {
        //dd($request->all());
        $id_solicitud = $request['id_solicitud'];
        $solicitudBase = $this->solicitudForActiveCompany($id_solicitud);
        $evolucion = Hc_Evolucion::find($id);
        abort_if(is_null($evolucion), 404, 'No se encontró la evolución solicitada.');
        $historiaBase = $this->episodeHistory($solicitudBase);
        abort_unless($historiaBase && (string) $evolucion->hcid === (string) $historiaBase->hcid, 404);
        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id_solicitud)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->where('ag.id_empresa', session('id_empresa'))
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
        ->first();
        if (!$solicitud) {
            $solicitud = $solicitudBase;
            $solicitud->setAttribute('id_agenda', $historiaBase->id_agenda);
            $solicitud->setAttribute('hcid', $historiaBase->hcid);
            $solicitud->setAttribute('id_hcproc', $evolucion->hc_id_procedimiento);
        }
        //dd($evolucion->id);
        $receta = null;
        $rec_evolu = Ho_Hc_Receta_Evolucion::where('id_evolucion',$evolucion->id)->first();
        if (!is_null($rec_evolu)) {
            $receta = hc_receta::with('detalles')->find($rec_evolu->id_receta);
        }
        // Compatibilidad con evoluciones antiguas que no tienen registro en
        // ho_hc_receta_evolucion, pero conservan la receta de la historia clínica.
        if (is_null($receta) && !is_null($solicitud)) {
            $receta = hc_receta::with('detalles')
                ->where('id_hc', $solicitud->hcid)
                ->latest('created_at')
                ->first();
        }
            //dd($receta);
        $detalles = is_null($receta) ? collect() : $receta->detalles;

        return view('hospital.formulario_005.nueva_evolucion', ['evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'solicitud' => $solicitud, 'receta'=>$receta, 'detalles'=>$detalles]);
    }

    public function crear_evolucion($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = $this->solicitudForActiveCompany($id);
        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 422, 'No se encontró una historia clínica para crear la evolución.');

        $hcProc = hc_procedimientos::where('id_hc', $historia->hcid)
            ->orderBy('id', 'desc')->first();
        abort_unless($hcProc, 422, 'El paciente aún no tiene un procedimiento clínico vinculado.');

        $solicitud->id_agenda = $agenda ? $agenda->id : $historia->id_agenda;
        $solicitud->hcid = $historia->hcid;
        $solicitud->id_hcproc = $hcProc->id;

        $registros = DB::transaction(function () use ($solicitud, $ip_cliente, $idusuario) {
            $evolucionId = Hc_Evolucion::insertGetId([
                'hc_id_procedimiento' => $solicitud->id_hcproc,
                'hcid'                => $solicitud->hcid,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);

            $childPugh = hc_child_pugh::create([
                'id_hc_evolucion' => $evolucionId,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'examen_fisico'   => "ESTADO CABEZA Y CUELLO:\nESTADO TÓRAX:\nESTADO ABDOMEN:\nESTADO MIEMBROS SUPERIORES:\nESTADO MIEMBROS INFERIORES:\nOTROS:",
            ]);

            $recetaId = hc_receta::insertGetId([
                'id_hc'           => $solicitud->hcid,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            Ho_Hc_Receta_Evolucion::create([
                'id_receta'       => $recetaId,
                'id_evolucion'    => $evolucionId,
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            return [
                'evolucion' => Hc_Evolucion::findOrFail($evolucionId),
                'child_pugh' => $childPugh,
                'receta' => hc_receta::findOrFail($recetaId),
            ];
        });

        return view('hospital.formulario_005.nueva_evolucion', [
            'solicitud' => $solicitud,
            'evolucion' => $registros['evolucion'],
            'child_pugh' => $registros['child_pugh'],
            'receta' => $registros['receta'],
            'detalles' => [],
        ]);

    }

    public function f5_diagnostico()
    {

        return view('hospital.formulario_005.diagnostico');
    }

    public function f5_medidas_generales()
    {

        return view('hospital.formulario_005.med_generales');
    }

    public function f5_tratamiento()
    {

        return view('hospital.formulario_005.tratamiento');
    }

    public function f5_plan()
    {

        return view('hospital.formulario_005.plan');
    }

    public function f5_medicamentos($id)
    {
        $solicitud = $this->solicitudForActiveCompany($id);
        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        $recetas = $historia
            ? hc_receta::where('id_hc', $historia->hcid)->orderBy('created_at', 'DESC')->get()
            : collect();

        //dd($receta);

        return view('hospital.formulario_005.medicamentos', ['solicitud' => $solicitud, 'recetas' => $recetas]);
    }

    public function receta_detalle($id, Request $request)
    {
        $id_solicitud = $request['id_solicitud'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $historia = $this->episodeHistory($solicitud);
        $receta = hc_receta::where('id', $id)
            ->where('id_hc', optional($historia)->hcid)
            ->firstOrFail();
        $detalles = $receta->detalles;
        //dd($detalles);
        return view('hospital.formulario_005.nueva_receta', ['solicitud' => $solicitud, 'detalles' => $detalles, 'receta' => $receta]);
    }

    public function crear_receta($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = $this->solicitudForActiveCompany($id);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'No se encontró la historia clínica del paciente.');

        // Reutiliza la receta vacía más reciente. Cada pulsación anterior del
        // botón "Agregar" creaba otra receta sin detalles y enfermería podía
        // terminar abriendo una orden vacía en lugar de la prescripción real.
        $receta = hc_receta::where('id_hc', $historia->hcid)
            ->where('id_usuariocrea', $idusuario)
            ->whereDoesntHave('detalles')->orderBy('id', 'desc')->first();
        if (!$receta) {
            $receta = hc_receta::create([
                'id_hc'                 => $historia->hcid,
                'id_doctor_examinador'  => $historia->id_doctor1 ?: $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ]);
        }

        $recetas = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.medicamentos', ['solicitud' => $solicitud, 'recetas' => $recetas]);
    }

    public function eliminar_medicina($id_detalle, Request $request)
    {
        $detalle_receta = $this->recipeDetailForActiveCompany($id_detalle);

        $idReceta = $detalle_receta->id_hc_receta;
        $id_solicitud = $request['id_solicitud'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $receta = $this->recipeForSolicitation($idReceta, $solicitud);
        $detalle_receta->delete();
        $detalles = $receta->detalles;

        return view('hospital.formulario_005.recetajs', compact('solicitud', 'detalles', 'receta'));
    }

    public function editar_medicina(Request $request, $id_detalle)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $detalle_receta = $this->recipeDetailForActiveCompany($id_detalle);

        $cantidad = max(1, (int) $request->input('cantidad' . $id_detalle, 1));
        $arr_med = [
            'dosis'             => trim((string) $request->input('dosis' . $id_detalle, '')),
            'cantidad'          => $cantidad,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $detalle_receta->update($arr_med);

        return "ok";
    }

    public function f05_medicina_guardar(Request $request, $id_rec)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request->input('solicitud_id', $request->input('id_solicitud'));
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);

        $id_generico = $request['id_generico'];

        $receta = $this->recipeForSolicitation($id_rec, $solicitud);

        $medicina = Medicina::find($id_generico);

        if (is_null($medicina)) {
            return response()->json(['message' => 'Seleccione una medicina válida antes de agregar.'], 422);
        }

        $receta_detalle = $receta->detalles->where('id_medicina', $medicina->id)->first();

        if (is_null($receta_detalle)) {

            hc_receta_detalle::create([
                'id_hc_receta'    => $receta->id,
                'id_medicina'     => $medicina->id,
                'nombre'          => $medicina->nombre,
                'cantidad'        => 1,
                'dosis'           => $medicina->dosis,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario

            ]);
        }
        $receta2 = hc_receta::find($receta->id);

        return view('hospital.formulario_005.recetajs', ['solicitud' => $solicitud, 'detalles' => $receta2->detalles, 'receta' => $receta]);
    }

    /**
     * Guarda diagnósticos del F-005 usando la historia real del episodio.
     * El endpoint de emergencias no sirve para solicitudes hospitalarias sin agenda.
     */
    public function agregar_cie10(Request $request)
    {
        $solicitud = $this->solicitudForActiveCompany($request->input('id_solicitud'));
        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 422, 'No se encontró la historia clínica del paciente.');

        $hcProc = $solicitud->id_hcproc
            ? hc_procedimientos::find($solicitud->id_hcproc)
            : hc_procedimientos::where('id_hc', $historia->hcid)->orderBy('id', 'desc')->first();
        abort_unless($hcProc, 422, 'No se encontró el procedimiento clínico del episodio.');

        $codigo = strtoupper(trim((string) $request->input('codigo')));
        $tipo = strtoupper(trim((string) $request->input('pre_def')));
        abort_if($codigo === '', 422, 'Seleccione un diagnóstico CIE-10.');
        abort_if(!in_array($tipo, ['PRESUNTIVO', 'DEFINITIVO'], true), 422, 'Seleccione la clasificación del diagnóstico.');

        $catalogo = Cie_10_4::find($codigo) ?: Cie_10_3::find($codigo);
        abort_unless($catalogo, 422, 'El diagnóstico CIE-10 seleccionado no existe.');

        $duplicado = Hc_Cie10::where('hc_id_procedimiento', $hcProc->id)
            ->where('ingreso_egreso', 'INGRESO')
            ->where('cie10', $codigo)->exists();
        if ($duplicado) {
            return response()->json(['estado' => 'err', 'msn' => 'El diagnóstico ya está registrado en este episodio.']);
        }

        $ip = request()->ip();
        $usuario = Auth::id();
        $id = Hc_Cie10::insertGetId([
            'hcid' => $historia->hcid,
            'cie10' => $codigo,
            'hc_id_procedimiento' => $hcProc->id,
            'ingreso_egreso' => 'INGRESO',
            'presuntivo_definitivo' => $tipo,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
        ]);

        return response()->json([
            'id' => $id,
            'cie10' => $codigo,
            'descripcion' => $catalogo->descripcion,
            'pre_def' => $tipo,
            'in_eg' => 'INGRESO',
            'id_hcproc' => $hcProc->id,
        ]);
    }

    public function f5_examenes()
    {

        return view('hospital.formulario_005.examenes');
    }

    public function f5_salas()
    {

        return view('hospital.formulario_005.salas');
    }

    public function pdf_formulario005()
    {


        $view = \View::make('hospital.formulario_005.formulario005_pdf')->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        return $pdf->stream('formulario005.pdf');
    }

    public function cargar_examenes($id)
    {

        $idPaciente = $this->solicitudForActiveCompany($id);
        $paciente = Paciente::find($idPaciente->id_paciente);
        $ordenes = DB::table('examen_orden as eo')
            ->where('eo.id_paciente', $idPaciente->id_paciente)
            ->where('eo.id_empresa', session('id_empresa'))
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            ->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')
            ->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')
            ->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')
            ->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')
            ->join('users as cu', 'cu.id', 'eo.id_usuariocrea')
            ->join('users as mu', 'mu.id', 'eo.id_usuariomod')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.nombre2 as dnombre2', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo')
            ->limit(3)
            ->OrderBy('eo.fecha_orden', 'desc')
            ->get();

        return view('hospital.formulario_005.mostrar_examenes', [
            'ordenes' => $ordenes,
            'paciente' => $paciente,
            'solicitud' => $idPaciente,
        ]);
    }

    public function imprimir_resultado($id)
    {
        $orden = Examen_Orden::where('id', $id)
            ->where('id_empresa', session('id_empresa'))
            ->firstOrFail();
        $paciente = Paciente::find($orden->id_paciente);
        $user = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //$detalle = $orden->detalles;
        $resultados =  $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
        //$agrupador = Examen_Agrupador::all();

        //Recalcula Porcentaje 
        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
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
        }

        $certificados = 0;
        $cantidad = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }


        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }


        // Fin recalcula Porcentaje


        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::all();
        }
        $ucreador = $orden->crea;
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //$vistaurl="hc4.laboratorio.resultados_pdf";
        $vistaurl = "hospital.formulario_005.pdf_resultados";
        $view =  \View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function puede_imprimir($id)
    {

        $orden = Examen_Orden::where('id', $id)
            ->where('id_empresa', session('id_empresa'))
            ->firstOrFail();
        $detalle = $orden->detalles;
        $resultados =  $orden->resultados;

        $cant_par = 0;
        foreach ($detalle as $d) {

            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
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
        }

        $certificados = 0;
        $cantidad = 0;

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


    public function evolucion_enfermeria($id)
    {

        $solicitud = $this->solicitudForActiveCompany($id);
        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);

        $evoluciones = $historia
            ? Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->orderBy('created_at', 'DESC')->get()
            : collect();


        return view('hospital.formulario_005.evolucion_enfermeria', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones]);
    }

    public function evol_detalle_enfermeria($id, Request $request)
    {
        $id_solicitud = $request['id_solicitud'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $evolucion = $this->nursingEvolutionForActiveCompany($id);
        abort_unless((string) $evolucion->id_solicitud === (string) $solicitud->id, 404);
        return view('hospital.formulario_005.nueva_evolucion_enfermeria', ['solicitud' => $solicitud, 'evolucion' => $evolucion]);
    }


    public function crear_evolucion_enfer($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = $this->solicitudForActiveCompany($id);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'Este episodio no tiene una historia clínica vinculada.');


        $arr_evolucion = [
            'id_solicitud'          => $solicitud->id,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'hcid'                  => $historia->hcid,
        ];

        $evolucion = Ho_Evoluciones_Enfermeria::create($arr_evolucion);


        $evoluciones = Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->Orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.evolucion_enfermeria', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones]);
    }

    public function guardar_evolucion_enfermeria(Request $request, $id_evol)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $evolucion = $this->nursingEvolutionForActiveCompany($id_evol);
        abort_unless((string) $evolucion->id_solicitud === (string) $solicitud->id, 404);

        if (!is_null($evolucion)) {
            $arr_evolucion = [
                'ip_modificacion'       => $ip_cliente,
                'id_usuariomod'         => $idusuario,
                'parenteral'            => $request['parenteral'],
                'via_oral'              => $request['via_oral'],
                'total_ingreso'         => $request['total_ingresos'],
                'orina'                 => $request['orina'],
                'drenaje'               => $request['drenaje'],
                'otros_elimina'         => $request['otros_elimina'],
                'total_elimina'         => $request['total_elimina'],
                'aseo_bano'             => $request['aseo_bano'],
                'peso'                  => $request['peso'],
                'dieta'                 => $request['dieta'],
                'num_comidas'           => $request['num_comidas'],
                'num_micciones'         => $request['num_micciones'],
                'num_deposiciones'      => $request['num_deposiciones'],
                'actividad_fisica'      => $request['actividad_fisica'],
                'cambio_sonda'          => $request['cambio_sonda'],
                'recanalizacion'        => $request['recanalizacion'],
                'responsable'           => $request['responsable'],
                'frec_respiratoria'     => $request['fRespiratori'],
                'presion_arterial'      => $request['pArterial'],
                'peso_neonato'          => $request['peso_neonato'],
                'peso_panal'            => $request['peso_panal'],
            ];

            $evolucion->update($arr_evolucion);
        }

        return view('hospital.formulario_005.nueva_evolucion_enfermeria', ['solicitud' => $solicitud, 'evolucion' => $evolucion]);
    }

    public function guardarimagen(Request $request, $id)
    {
        $this->validate($request, array(
            'my-file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ));

        $evolucion = $this->nursingEvolutionForActiveCompany($id);
        $originalName = basename((string) $request['my-file']->getClientOriginalName());
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $extension = 'ho_'.$id.'_'.date('YmdHis').'_'.$safeName;
        Storage::disk('hc_ima')->put($extension, \File::get($request['my-file']));
        $evolucion->imagen_signovitales = $extension;
        $evolucion->save();
    }
    public function verpdf($id)
    {
        $evolucion = $this->nursingEvolutionForActiveCompany($id);
        $datoPaciente = Paciente::where('id', $evolucion->dato->id_paciente)->first();
        // return view('hospital.formulario_005.verpdf005', ['evolucion' => $evolucion, 'datoPaciente' => $datoPaciente]);
        $view = \View::make('hospital.formulario_005.verpdf005', compact('evolucion', 'datoPaciente'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Evolució Enfermeria' . '.pdf');
    }

    public function notas_enfermeria($id)
    {
        $solicitud = $this->solicitudForActiveCompany($id);
        $agenda = $solicitud->agenda;
        $historia = $this->episodeHistory($solicitud);
        $notas_enfermeria = $historia
            ? Hc_Evolucion_Indicacion::where('id_hc', $historia->hcid)->orderBy('created_at', 'DESC')->get()
            : collect();

        return view('hospital.formulario_005.notas_enfermeria', ['solicitud' => $solicitud, 'notas_enfermeria' => $notas_enfermeria]);
    }

    public function crear_nota_enfermeria($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = $this->solicitudForActiveCompany($id);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'Este episodio no tiene una historia clínica vinculada.');

        $arr_evolucion = [
            'id_hc'                 => $historia->hcid,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        Hc_Evolucion_Indicacion::create($arr_evolucion);


        $notas_enfermeria = Hc_Evolucion_Indicacion::where('id_hc', $historia->hcid)->Orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.notas_enfermeria', ['solicitud' => $solicitud, 'notas_enfermeria' => $notas_enfermeria]);
    }



    public function nota_enfermeria_detalle($id, Request $request)
    {

        $id_solicitud = $request['id_solicitud'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'Este episodio no tiene una historia clínica vinculada.');
        $nota_enfermeria = Hc_Evolucion_Indicacion::where('id', $id)
            ->where('id_hc', $historia->hcid)
            ->firstOrFail();
        return view('hospital.formulario_005.nueva_nota_enfermeria', ['solicitud' => $solicitud, 'nota_enfermeria' => $nota_enfermeria]);
    }

    public function guardar_nota_enfermeria(Request $request, $id)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = $this->solicitudForActiveCompany($id_solicitud);
        $historia = $this->episodeHistory($solicitud);
        abort_unless($historia, 404, 'Este episodio no tiene una historia clínica vinculada.');
        $nota_enfermeria = Hc_Evolucion_Indicacion::where('id', $id)
            ->where('id_hc', $historia->hcid)
            ->firstOrFail();

        if (!is_null($nota_enfermeria)) {
            $arr_nota = [
                'ip_modificacion'       => $ip_cliente,
                'id_usuariomod'         => $idusuario,
                'descripcion'           => $request['observacion'],
            ];

            $nota_enfermeria->update($arr_nota);
        }

        return view('hospital.formulario_005.nueva_nota_enfermeria', ['solicitud' => $solicitud, 'nota_enfermeria' => $nota_enfermeria]);
    }
}
