<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Pacientes_ext;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Hosp_atencion_fomulario008;
use Sis_medico\Hosp_revision_formulario008;
use Sis_medico\Hosp_accidente_formulario008;
use Sis_medico\Hosp_antecedentes_formulario008;
use Sis_medico\Hosp_signos_vitales_formulario008;
use Sis_medico\Hosp_obstetrica_formulario008;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hosp_tratamiento_formulario008;
use Sis_medico\Hosp_formulario008_alta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Hosp_Triaje_Manchester;
use Sis_medico\Hosp_Triaje_Emergencia;
use Sis_medico\Ho_Tipo_Emergencia;
use Sis_medico\Ho_Prioridad_Emergencia;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Ho_Glasgow;
use Sis_medico\HoEstadoPaso;
use Sis_medico\User;
use Sis_medico\ClinicalEmergencyFlow;
use Sis_medico\Services\ClinicalEmergencyFlowService;
use Sis_medico\Services\EmergencyPatientRegistrationService;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;


class FormularioManchesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        //dd($rolUsuario,$opcion);
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    public function emergencialista()
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $paciente_emegerncia = Ingreso_emer_008::all();

        return view('hospital/emergencia/emergencia')->with('paciente_emegerncia', $paciente_emegerncia);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formulariomanchester()
    {
        app(ClinicalEmergencyFlowService::class)->context(request(), ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']);

        $tipos_emergencia = Ho_Tipo_Emergencia::where('estado', '1')->get();
        $prioridad = Ho_Prioridad_Emergencia::where('estado', '1')->orderBy('prioridad')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();

        // dd($prioridad);

        return view('hospital/emergencia/formulariomanchester', ['tipos_emergencia' => $tipos_emergencia, 'prioridad' => $prioridad, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }
    public function resultadomanchester(Request $request, $id)
    {
        $source = Ho_Triaje_Manchester::findOrFail($id);
        $solicitud = Ho_Solicitud::findOrFail($source->id_ho_solicitud);
        try {
            app(ClinicalEmergencyFlowService::class)->assertAccess($solicitud, $request, 'EMERGENCY_VIEW', [3, 6]);
        } catch (ClinicalAccessDeniedException $e) {
            abort(403, $e->getMessage());
        }
        $flow = ClinicalEmergencyFlow::where('solicitud_id', $solicitud->id)->first();
        $version = $flow ? (int) $flow->version : 0;

        $tipos_emergencia = Ho_Tipo_Emergencia::where('estado', '1')->get();
        $prioridad = Ho_Prioridad_Emergencia::where('estado', '1')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();

        $resultados = Ho_Triaje_Manchester::where('ho_triaje_manchester.id', $id)
            ->join('ho_solicitud as ho_s', 'ho_s.id', 'ho_triaje_manchester.id_ho_solicitud')
            ->join('paciente as p', 'p.id', 'ho_s.id_paciente')
            ->select('ho_triaje_manchester.*', 'ho_s.id_paciente', 'ho_s.id_seguro', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2')
            ->first();

        return view('hospital/emergencia/resultadomanchester', ['version' => $version, 'datos' => $resultados, 'tipos_emergencia' => $tipos_emergencia, 'prioridad' => $prioridad, 'id' => $id, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }


    public function buscar_paciente(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $productos;
    }

    public function obtener_informacion(Request $request)
    {


        $nombre_encargado   = $request['nombre'];
        $data               = null;
        $nuevo_nombre       = explode(' ', $nombre_encargado);
        $seteo              = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) 
                  as completo, telefono1, telefono2,
                     id_seguro, id_pais, sexo, id, estadocivil,
                     cedulafamiliar, religion, fecha_nacimiento,
                     trabajo, lugar_nacimiento, alergias, ciudad,
                     gruposanguineo, direccion, antecedentes_pat,
                     antecedentes_fam, ocupacion, telefono_llamar
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";


        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $jquery = \Sis_medico\Seguro::query()->where('id', $nombre->id_seguro)->first();
            $pais = DB::table('pais')->where('id', $nombre->id_pais)->first();
            $data[] = array(
                'value' =>  $nombre->completo,
                'fecha'             =>  $nombre->fecha_nacimiento,
                'telefono1'         =>  $nombre->telefono1,
                'telefono2'         =>  $nombre->telefono2,
                'id_pais'           =>  $nombre->id_pais,
                'ocupacion'         =>  $nombre->ocupacion,
                'seguro'            =>  $nombre->id_seguro,
                'sexo'              =>  $nombre->sexo,
                'id'                =>  $nombre->id,
                'estadoc'           =>  $nombre->estadocivil,
                'cedula'            =>  $nombre->cedulafamiliar,
                'religion'          =>  $nombre->religion,
                'alergia'           =>  $nombre->alergias,
                'lugar_nacimiento'  =>  $nombre->lugar_nacimiento,
                'ciudad'            =>  $nombre->ciudad,
                'grupos'            =>  $nombre->gruposanguineo,
                'direccion'         =>  $nombre->direccion,
                'antp'              =>  $nombre->antecedentes_pat,
                'antf'              =>  $nombre->antecedentes_fam,
                'tipo_seguro'       =>  $jquery->nombre,
                'id_pais'           =>  $pais->nombre,
                'telefono_llamar'   =>  $nombre->telefono_llamar
            );
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function guardar_manchester2(Request $request)
    {
        return $this->guardar_manchester($request);
    }

    public function guardar_manchester(Request $request)
    {
        return $this->saveResponse($request, function () use ($request) {
            $data = array_merge($request->only(['id_paciente', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'sexo']),
                $this->triageData($request));
            $this->validate($request, ['intake_mode'=>'nullable|in:MINIMAL,FULL', 'critical_arrival'=>'nullable|boolean']);
            $minimal = $request->input('intake_mode') === 'MINIMAL';
            $service = app(ClinicalEmergencyFlowService::class);
            $solicitud = $minimal ? $service->registerMinimalArrival($data + ['critical_arrival'=>$request->input('critical_arrival')], $request)
                : $service->registerArrival($data, $request);
            return ['respuesta' => 'success', 'msj' => $minimal ? 'Ingreso guardado. Puede completar los datos desde el episodio.' : 'Ingreso y triaje registrados.',
                'paso' => 2, 'solicitud_id' => $solicitud->id];
        });
    }

    public function update_manchester(Request $request)
    {
        return $this->saveResponse($request, function () use ($request) {
            $this->validate($request, ['id_manchester' => 'required|integer|min:1']);
            $source = Ho_Triaje_Manchester::findOrFail($request->id_manchester);
            $solicitud = Ho_Solicitud::findOrFail($source->id_ho_solicitud);
            $data = $this->triageData($request);
            $data['source_triage_id'] = $source->id;
            $flow = app(ClinicalEmergencyFlowService::class)->recordTriage($solicitud, $data, $request);
            return ['respuesta' => 'success', 'msj' => 'Nueva valoracion registrada. Se conserva la anterior.',
                'paso' => 2, 'version' => (int) $flow->version, 'solicitud_id' => $solicitud->id];
        });
    }

    private function triageData(Request $request)
    {
        $fields = ['priority_id' => 'prioridad', 'emergency_type_id' => 'tipos_emergencia',
            'clinical_reason' => 'mot_consulta', 'pregnancy' => 'embarazo_p',
            'classification_method' => 'classification_method', 'classification_confirmed' => 'classification_confirmed',
            'manchester_flowchart' => 'manchester_flowchart', 'discriminator' => 'discriminator',
            'glasgow_note' => 'glasgow_note', 'pain_score' => 'pain_score',
            'systolic' => 'presion_art_sis', 'diastolic' => 'presion_art_dias',
            'heart_rate' => 'frec_cardiaca', 'respiratory_rate' => 'frec_resp',
            'temperature' => 'temperatura', 'height' => 'talla', 'weight' => 'peso',
            'eye_response' => 'resp_ocular', 'verbal_response' => 'resp_verbal', 'motor_response' => 'resp_motora',
            'pupillary_response' => 'reac_pupilar', 'capillary_refill' => 'total_capilar',
            'oxygen_saturation' => 'satura_oxigeno', 'conscious' => 'est_conciencia', 'version' => 'version'];
        $data = [];
        foreach ($fields as $key => $field) {
            $value = $request->input($field);
            $data[$key] = is_string($value) ? trim($value) : $value;
            if ($data[$key] === '') $data[$key] = null;
        }
        $ranges = [
            'pain_score' => [0, 10], 'systolic' => [20, 300], 'diastolic' => [10, 200],
            'heart_rate' => [1, 300], 'respiratory_rate' => [1, 100],
            'temperature' => [25, 45], 'oxygen_saturation' => [0, 100],
            'height' => [0, null], 'weight' => [0, null], 'capillary_refill' => [0, null],
        ];
        foreach ($ranges as $field => [$minimum, $maximum]) {
            if ($data[$field] === null || !is_numeric($data[$field])) continue;
            $value = (float) $data[$field];
            if ($value < $minimum) $value = $minimum;
            if ($maximum !== null && $value > $maximum) $value = $maximum;
            $data[$field] = $value;
        }
        return $data;
    }

    public function registrar_paciente(Request $request)
    {
        return $this->saveResponse($request, function () use ($request) {
            $context = app(ClinicalEmergencyFlowService::class)->context($request);
            $patient = app(EmergencyPatientRegistrationService::class)->registerMinimal(
                $request->only([
                    'id_paciente', 'tipo_documento', 'nombre1', 'nombre2', 'apellido1', 'apellido2',
                    'sexo', 'without_document', 'registration_token',
                ]), $request, $context
            );
            return ['respuesta' => 'success', 'msj' => $patient->wasRecentlyCreated
                ? 'Paciente registrado. Puede continuar con el ingreso de emergencia.'
                : 'El paciente ya estaba registrado y fue seleccionado.',
                'paciente' => $this->patientSummary($patient)];
        });
    }

    public function pacientes(Request $request)
    {
        app(ClinicalEmergencyFlowService::class)->context($request);
        $this->validate($request, ['id' => 'nullable|string|max:13', 'term' => 'nullable|string|max:120']);
        if ($request->filled('id')) {
            $person = app(EmergencyPatientRegistrationService::class)->findPerson($request->input('id'));
            if ($person) {
                $patient = Paciente::where('persona_id', $person->id)->first();
                return response()->json($patient ? $this->patientSummary($patient, $person) : $this->personSummary($person));
            }
            $patient = Paciente::find(mb_strtoupper(trim($request->input('id')), 'UTF-8'));
            return response()->json($patient ? $this->patientSummary($patient) : false);
        }
        $term = trim((string) $request->input('term'));
        if (mb_strlen($term) < 3) return response()->json([]);

        $people = DB::table('personas')->where('tipo_persona', 'NATURAL');
        foreach (preg_split('/\s+/', $term) as $word) {
            $people->where(function ($names) use ($word) {
                foreach (['nombre1', 'nombre2', 'apellido1', 'apellido2'] as $column) {
                    $names->orWhere($column, 'like', '%'.addcslashes($word, '\\%_').'%');
                }
            });
        }
        $people = $people->orderBy('apellido1')->orderBy('nombre1')->limit(20)->get();
        $patients = Paciente::whereIn('persona_id', $people->pluck('id')->all())->get()->keyBy('persona_id');
        $results = $people->map(function ($person) use ($patients) {
            $patient = $patients->get($person->id);
            return $patient ? $this->patientSummary($patient, $person) : $this->personSummary($person);
        })->values();

        // Fallback para fichas antiguas que aún no hayan sido sincronizadas con Personas.
        if ($results->count() < 20) {
            $legacy = Paciente::whereNull('persona_id');
            foreach (preg_split('/\s+/', $term) as $word) {
                $legacy->where(function ($names) use ($word) {
                    foreach (['nombre1', 'nombre2', 'apellido1', 'apellido2'] as $column) {
                        $names->orWhere($column, 'like', '%'.addcslashes($word, '\\%_').'%');
                    }
                });
            }
            $results = $results->concat($legacy->orderBy('apellido1')->limit(20 - $results->count())->get()
                ->map(function ($patient) { return $this->patientSummary($patient); }));
        }
        return response()->json($results->values());
    }

    private function personSummary($person)
    {
        $value = trim(preg_replace('/\s+/', ' ', $person->apellido1.' '.$person->apellido2.' '.$person->nombre1.' '.$person->nombre2));
        return ['id' => $person->identificacion, 'persona_id' => $person->id,
            'es_paciente' => false, 'value' => $value,
            'nombre1' => $person->nombre1, 'nombre2' => $person->nombre2,
            'apellido1' => $person->apellido1, 'apellido2' => $person->apellido2,
            'sexo' => $person->sexo, 'tipo_documento' => $person->tipo_documento];
    }

    private function patientSummary(Paciente $patient, $person = null)
    {
        $summary = $patient->only(['id', 'persona_id', 'tipo_documento', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'sexo', 'registro_emergencia_pendiente']);
        if ($person) {
            foreach (['tipo_documento', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'sexo'] as $field) {
                if ($person->$field !== null && $person->$field !== '') $summary[$field] = $person->$field;
            }
        }
        return $summary + ['es_paciente' => true,
            'value' => trim(preg_replace('/\s+/', ' ', $summary['apellido1'].' '.$summary['apellido2'].' '.$summary['nombre1'].' '.$summary['nombre2'])),
            'editar_url' => route('v3.patients.edit', ['patient' => $patient->id])];
    }

    private function saveResponse(Request $request, callable $callback)
    {
        try {
            $result = $callback();
            if ($request->expectsJson()) return response()->json($result);
            return redirect()->route('hospital.emergencia')->with('success', $result['msj']);
        } catch (ClinicalAccessDeniedException $e) {
            return $this->saveFailure($request, $e->getMessage(), 403);
        } catch (\DomainException $e) {
            return $this->saveFailure($request, $e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return $this->saveFailure($request, $e->getMessage(), 422);
        }
    }

    private function saveFailure(Request $request, $message, $status)
    {
        if ($request->expectsJson()) return response()->json(['respuesta' => 'error', 'msj' => $message, 'message' => $message], $status);
        if ($status === 403) abort(403, $message);
        return back()->withErrors(['manchester' => $message])->withInput();
    }
}
