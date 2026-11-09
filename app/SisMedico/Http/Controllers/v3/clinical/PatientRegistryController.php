<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3PatientRegistryService;
use Sis_medico\Services\PatientTenantService;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Familia;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Pais;

class PatientRegistryController extends Controller
{
    private $patients;

    public function __construct(V3PatientRegistryService $patients)
    {
        $this->middleware('auth');
        $this->patients = $patients;
    }

    public function index(Request $request)
    {
        $filters = $this->patients->filters($request);
        return $this->page('v3.clinical.patients.index', array(
            'filters' => $filters,
            'patients' => $this->patients->paginate($filters, $this->companyId($request)),
        ), 'Pacientes');
    }

    public function create(Request $request)
    {
        $companyId = $this->companyId($request);
        $insurers = \Sis_medico\Seguro::query();
        if (Schema::hasColumn('seguros', 'inactivo')) {
            $insurers->where('inactivo', 1);
        } elseif (Schema::hasColumn('seguros', 'estado')) {
            $insurers->where('estado', 1);
        }
        if (Schema::hasColumn('seguros', 'id_empresa')) {
            $insurers->where('id_empresa', $companyId);
        }
        return $this->page('v3.clinical.patients.create', array(
            'patientTypeProblem' => app(\Sis_medico\Services\v3\PatientUserType::class)->problem(),
            'countries' => DB::table('pais')->where('estado', 1)->orderBy('nombre')->get(),
            'insurers' => $insurers->orderBy('nombre')->get(),
            'agendaContext' => $this->agendaContext($request),
        ), 'Registrar paciente');
    }

    public function edit(Request $request, $patient)
    {
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patient);
        if (!$patient) abort(404, 'Paciente no encontrado.');

        $data = array(
            'paciente' => $patient,
            'alergiasxpac' => Paciente_Alergia::where('id_paciente', $patient->id)->get(),
            'paises' => Pais::all(),
            'seguros' => Seguro::orderBy('nombre')->get(),
            'user_aso' => User::find($patient->id_usuario),
            'repre_opc' => Paciente_Familia::where('id_paciente', $patient->id)->first(),
            'copia_cedula' => Paciente_Biopsia::where('id_paciente', $patient->id)->where('estado', '3')
                ->where('id_empresa', $companyId)
                ->orderBy('id', 'desc')->first(),
            'isV3Page' => true,
        );
        config(array('data' => array_merge(config('data') ?: array(), array(
            'controlador' => 'pacientes', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => 'Editar paciente', 'title' => 'Editar paciente | Aitrol Hospitalario',
            'obseraudi' => 'Ingreso a edición de paciente V3',
        ))));

        return view('paciente.edit', $data);
    }

    public function searchAllergies(Request $request)
    {
        $term = trim((string) $request->input('q'));
        if ($term === '') return response()->json(array());

        $allergies = DB::table('principio_activo')
            ->where('estado', 1)
            ->where('nombre', 'like', '%' . $term . '%')
            ->orderBy('nombre')
            ->limit(20)
            ->get(array('id', 'nombre'));

        return response()->json($allergies->map(function ($allergy) {
            return array('id' => $allergy->id, 'text' => $allergy->nombre);
        })->values());
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);
        app(\Sis_medico\Services\v3\PatientUserType::class)->id();
        $this->validate($request, array(
            'id' => 'required|string|min:3|max:13',
            'tipo_documento' => 'required|integer|between:1,3',
            'nombre1' => 'required|string|max:60', 'nombre2' => 'nullable|string|max:60',
            'apellido1' => 'required|string|max:60', 'apellido2' => 'required|string|max:60',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|integer|in:1,2', 'id_pais' => 'required|integer|exists:pais,id',
            'id_seguro' => array('required', 'integer', app(\Sis_medico\Services\CompanyCatalogService::class)->existsRule('seguros')),
            'telefono1' => 'required|string|max:60', 'telefono2' => 'nullable|string|max:60',
            'patient_email' => 'required_if:parentesco,Principal|nullable|email|max:191', 'ciudad' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255', 'ocupacion' => 'nullable|string|max:60',
            'alergias' => 'nullable|string|max:255',
            'father_id' => 'nullable|digits:10|different:id',
            'mother_id' => 'nullable|digits:10|different:id|different:father_id',
            'parentesco' => 'required|in:Principal,Padre/Madre,Conyugue,Hijo(a),Hermano(a),Sobrino(a),Nieto(a),Primo(a),Familiar',
            'responsible_id' => 'nullable|string|min:3|max:13',
            'responsible_nombre1' => 'nullable|string|max:60', 'responsible_nombre2' => 'nullable|string|max:60',
            'responsible_apellido1' => 'nullable|string|max:60', 'responsible_apellido2' => 'nullable|string|max:60',
            'responsible_fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'responsible_email' => 'nullable|email|max:191',
            'responsible_telefono1' => 'nullable|string|max:60', 'responsible_telefono2' => 'nullable|string|max:60',
            'responsible_id_pais' => 'nullable|integer|exists:pais,id',
            'origen' => 'required|in:MEDIO IMPRESO,MEDIO DIGITAL,REFERIDO,OTRO',
            'origen_detalle' => 'nullable|string|max:255', 'referido' => 'nullable|string|max:255',
            'agenda_doctor' => 'nullable|string|max:13',
            'agenda_inicio' => 'nullable|date_format:Y-m-d\TH:i',
            'agenda_fin' => 'nullable|date_format:Y-m-d\TH:i|after:agenda_inicio',
            'agenda_tipo' => 'nullable|integer|between:0,2',
        ));

        $patientId = trim($request->input('id'));
        $existingPatient = DB::table('paciente')->where('id', $patientId)->first();
        if ($existingPatient) {
            $patientRecord = Paciente::withoutGlobalScope('active_company')->find($patientId);
            app(\Sis_medico\Services\PersonIdentityService::class)->sync(
                $patientRecord, 'PACIENTE', 'id', 'NATURAL', $companyId
            );
            app(PatientTenantService::class)->associate($patientId, $companyId, Auth::id(), $request->ip());
            return redirect()->route('v3.patients.index')->with(
                'patient_success',
                'El paciente existente fue vinculado correctamente a la empresa activa.'
            );
        }

        $isPrincipal = $request->input('parentesco') === 'Principal';
        $isMinor = $this->isMinor($request->input('fecha_nacimiento'));
        if ($isMinor && trim((string) $request->input('father_id')) === '' && trim((string) $request->input('mother_id')) === '') {
            return back()->withInput()->withErrors(array(
                'father_id' => 'Para un paciente menor de edad debe ingresar la cédula del padre o de la madre.',
            ));
        }
        if (!$isPrincipal) {
            foreach (array('responsible_id', 'responsible_nombre1', 'responsible_apellido1', 'responsible_apellido2', 'responsible_fecha_nacimiento', 'responsible_email', 'responsible_telefono1', 'responsible_id_pais') as $field) {
                if (trim((string) $request->input($field)) === '') {
                    return back()->withInput()->withErrors(array($field => 'Complete todos los datos obligatorios del responsable.'));
                }
            }
            if (trim($request->input('responsible_id')) === $patientId) {
                return back()->withInput()->withErrors(array('responsible_id' => 'La identificación del responsable debe ser diferente a la del paciente.'));
            }
        }

        $responsible = $this->responsibleData($request, $isPrincipal);
        $existingUser = DB::table('users')->where('id', $responsible['id'])->first();
        if (!$existingUser && $responsible['email'] !== '') {
            $owner = DB::table('users')->where('email', $responsible['email'])->first();
            if ($owner && (string) $owner->id !== $responsible['id']) {
                return back()->withInput()->withErrors(array('responsible_email' => 'El correo electrónico ya pertenece a otro usuario.'));
            }
        }

        $actor = Auth::id(); $ip = $request->ip(); $now = date('Y-m-d H:i:s');
        DB::transaction(function () use ($request, $patientId, $companyId, $responsible, $existingUser, $isMinor, $actor, $ip, $now) {
            $patientTypeId = app(\Sis_medico\Services\v3\PatientUserType::class)->id();
            $this->saveUser($responsible, $existingUser, $actor, $ip, $now, $patientTypeId);
            $email = trim((string) $request->input('patient_email'));
            if ($request->input('parentesco') === 'Principal' && $email === '') $email = $responsible['email'];
            DB::table('paciente')->insert(array(
                'id' => $patientId, 'nombre1' => $this->upper($request->input('nombre1')),
                'nombre2' => $this->optionalName($request->input('nombre2')),
                'apellido1' => $this->upper($request->input('apellido1')), 'apellido2' => $this->upper($request->input('apellido2')),
                'tipo_documento' => (int) $request->input('tipo_documento'), 'id_pais' => $request->input('id_pais'),
                'ciudad' => $this->upper($request->input('ciudad')), 'direccion' => $this->upper($request->input('direccion')),
                'telefono1' => $request->input('telefono1'), 'telefono2' => $request->input('telefono2'),
                'telefono3' => $responsible['telefono2'], 'id_seguro' => $request->input('id_seguro'), 'imagen_url' => ' ',
                'fecha_nacimiento' => $request->input('fecha_nacimiento'), 'sexo' => $request->input('sexo'),
                'id_usuario' => $responsible['id'], 'menoredad' => $isMinor ? 1 : 0,
                'cedulafamiliar' => $isMinor ? (trim((string) $request->input('father_id')) !== '' ? trim($request->input('father_id')) : trim($request->input('mother_id'))) : null,
                'papa_mama' => $isMinor ? (trim((string) $request->input('father_id')) !== '' ? 'PADRE' : 'MADRE') : null,
                'alergias' => $this->upper($request->input('alergias')), 'ocupacion' => $this->upper($request->input('ocupacion')),
                'nombre1familiar' => $responsible['nombre1'], 'nombre2familiar' => $responsible['nombre2'],
                'apellido1familiar' => $responsible['apellido1'], 'apellido2familiar' => $responsible['apellido2'],
                'parentesco' => $request->input('parentesco'), 'parentescofamiliar' => $request->input('parentesco'),
                'origen' => $request->input('origen'), 'origen2' => $this->upper($request->input('origen_detalle')),
                'otro' => $request->input('origen') === 'OTRO' ? $this->upper($request->input('origen_detalle')) : null,
                'referido' => $this->upper($request->input('referido')), 'mail_opcional' => $email !== '' ? $email : null,
                'id_usuariocrea' => $actor, 'id_usuariomod' => $actor, 'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                'created_at' => $now, 'updated_at' => $now,
            ));
            app(PatientTenantService::class)->associate($patientId, $companyId, $actor, $ip);
            $patientRecord = Paciente::withoutGlobalScope('active_company')->find($patientId);
            app(\Sis_medico\Services\PersonIdentityService::class)->sync(
                $patientRecord, 'PACIENTE', 'id', 'NATURAL', $companyId
            );
            foreach (array('father_id' => 'PADRE', 'mother_id' => 'MADRE') as $field => $relationship) {
                $familyId = trim((string) $request->input($field));
                if (!$isMinor || $familyId === '') continue;
                DB::table('paciente_familia')->insert(array(
                    'id_paciente' => $patientId, 'cedula_fam' => $familyId, 'papa_mama' => $relationship,
                    'estado' => 1, 'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip, 'created_at' => $now, 'updated_at' => $now,
                ));
            }
            DB::table('log_usuario')->insert(array(
                'id_usuario' => $actor, 'ip_usuario' => $ip, 'descripcion' => 'REGISTRA PACIENTE V3',
                'dato_ant1' => $patientId, 'dato1' => trim($request->input('apellido1').' '.$request->input('apellido2').' '.$request->input('nombre1').' '.$request->input('nombre2')),
                'created_at' => $now, 'updated_at' => $now,
            ));
        });
        $agendaContext = $this->agendaContext($request);
        if (!empty($agendaContext['doctor'])) {
            return redirect()->route('v3.doctor-agenda.create', array_filter(array(
                'doctor' => $agendaContext['doctor'],
                'inicio' => $agendaContext['inicio'],
                'fin' => $agendaContext['fin'],
                'tipo' => $agendaContext['tipo'],
                'paciente' => $patientId,
                'id_paciente' => $patientId,
            ), function ($value) { return $value !== null && $value !== ''; }))->with('agenda_success', 'Paciente registrado. Complete los datos de la nueva agenda.');
        }

        return redirect()->route('v3.patients.index')->with('patient_success', 'El paciente fue registrado correctamente.');
    }

    public function update(Request $request, $patient)
    {
        $companyId = $this->companyId($request);
        // El correo de contacto pertenece al paciente; el correo de acceso de users no se modifica aquí.
        if (trim((string) $request->input('email_opc')) === '@') $request->merge(array('email_opc' => null));
        if (trim((string) $request->input('parentesco')) === '') $request->merge(array('parentesco' => 'Principal'));
        foreach (array('fecha_val', 'cod_val', 'validacion_cv_msp', 'validacion_nc_msp', 'validacion_sec_msp') as $optionalField) {
            if (trim((string) $request->input($optionalField)) === '') $request->merge(array($optionalField => null));
        }
        $patient = trim((string) $patient);
        app(PatientTenantService::class)->requireAccessible($patient, $companyId);
        $current = DB::table('paciente')->where('id', $patient)->first();
        if (!$current) abort(404, 'Paciente no encontrado.');
        if (trim((string) $request->input('id')) !== $patient) {
            return response()->json(array('id' => array('La cédula del paciente no se puede modificar.')), 422);
        }
        $this->validate($request, array(
            'id' => 'required|string|max:13', 'nombre1' => 'required|string|max:60',
            'nombre2' => 'nullable|string|max:60', 'apellido1' => 'required|string|max:60',
            'apellido2' => 'nullable|string|max:60', 'id_pais' => 'required|integer|exists:pais,id',
            'id_seguro' => array('required', 'integer', app(\Sis_medico\Services\CompanyCatalogService::class)->existsRule('seguros')), 'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|integer|in:1,2', 'telefono1' => 'required|string|max:60',
            'telefono2' => 'nullable|string|max:60',
            'ciudad' => 'nullable|string|max:255', 'direccion' => 'nullable|string|max:255',
            'parentesco' => 'required|string|max:255', 'referido' => 'nullable|string|max:255',
            'ocupacion' => 'nullable|string|max:60', 'lugar_nacimiento' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:60', 'email_opc' => 'nullable|email|max:255',
            'estadocivil' => 'nullable|integer|between:1,5', 'fecha_val' => 'nullable|date',
            'cod_val' => 'nullable|string|max:255', 'validacion_cv_msp' => 'nullable|string|max:255',
            'validacion_nc_msp' => 'nullable|string|max:255', 'validacion_sec_msp' => 'nullable|string|max:255',
            'ale_list' => 'nullable|array', 'ale_list.*' => 'integer|exists:principio_activo,id',
        ));
        $actor = Auth::id(); $ip = $request->ip(); $now = date('Y-m-d H:i:s');
        DB::transaction(function () use ($request, $patient, $current, $actor, $ip, $now) {
            $allergyIds = array_values(array_unique($request->input('ale_list', array())));
            $allergyNames = count($allergyIds) > 0
                ? DB::table('principio_activo')->whereIn('id', $allergyIds)->pluck('nombre')->all()
                : array();
            $validationParts = array(
                trim((string) $request->input('validacion_cv_msp')),
                trim((string) $request->input('validacion_nc_msp')),
                trim((string) $request->input('validacion_sec_msp')),
            );
            DB::table('paciente')->where('id', $patient)->update(array(
                'registro_emergencia_pendiente' => 0,
                'nombre1' => $this->upper($request->input('nombre1')), 'nombre2' => $this->optionalName($request->input('nombre2')),
                'apellido1' => $this->upper($request->input('apellido1')), 'apellido2' => $this->upper($request->input('apellido2')),
                'id_pais' => $request->input('id_pais'), 'id_seguro' => $request->input('id_seguro'),
                'ciudad' => $this->upper($request->input('ciudad')), 'direccion' => $this->upper($request->input('direccion')),
                'telefono1' => $request->input('telefono1'), 'telefono2' => $request->input('telefono2'),
                'fecha_nacimiento' => $request->input('fecha_nacimiento'), 'menoredad' => $this->isMinor($request->input('fecha_nacimiento')) ? 1 : 0,
                'sexo' => $request->input('sexo'), 'parentesco' => $request->input('parentesco'),
                'referido' => $this->upper($request->input('referido')), 'ocupacion' => $this->upper($request->input('ocupacion')),
                'lugar_nacimiento' => $this->upper($request->input('lugar_nacimiento')), 'religion' => $this->upper($request->input('religion')),
                'estadocivil' => $request->input('estadocivil'), 'fecha_val' => $request->input('fecha_val'),
                'cod_val' => trim((string) $request->input('cod_val')),
                'validacion_cv_msp' => $validationParts[0], 'validacion_nc_msp' => $validationParts[1],
                'validacion_sec_msp' => $validationParts[2], 'codigo_validacion_msp' => implode('-', $validationParts),
                'alergias' => implode('+', $allergyNames),
                'mail_opcional' => trim((string) $request->input('email_opc')) === '@' ? null : trim((string) $request->input('email_opc')), 'id_usuariomod' => $actor,
                'ip_modificacion' => $ip, 'updated_at' => $now,
            ));
            $patientRecord = Paciente::withoutGlobalScope('active_company')->find($patient);
            app(\Sis_medico\Services\PersonIdentityService::class)->sync(
                $patientRecord, 'PACIENTE', 'id', 'NATURAL', session('id_empresa')
            );
            DB::table('paciente_alergia')->where('id_paciente', $patient)->delete();
            foreach ($allergyIds as $allergyId) {
                DB::table('paciente_alergia')->insert(array(
                    'id_paciente' => $patient, 'id_principio_activo' => $allergyId,
                    'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                    'created_at' => $now, 'updated_at' => $now,
                ));
            }
            DB::table('log_usuario')->insert(array(
                'id_usuario' => $actor, 'ip_usuario' => $ip, 'descripcion' => 'ACTUALIZA DATOS DE PACIENTE V3',
                'dato_ant1' => $patient, 'dato1' => $patient,
                'dato_ant2' => trim($current->nombre1.' '.$current->nombre2.' '.$current->apellido1.' '.$current->apellido2),
                'dato2' => trim($request->input('nombre1').' '.$request->input('nombre2').' '.$request->input('apellido1').' '.$request->input('apellido2')),
                'created_at' => $now, 'updated_at' => $now,
            ));
        });
        return response()->json(array(
            'status' => 'success',
            'message' => 'Los cambios del paciente se guardaron correctamente.',
        ));
    }

    public function identificationDocument(Request $request, $patient)
    {
        $companyId = $this->companyId($request);
        app(PatientTenantService::class)->requireAccessible($patient, $companyId);
        if (!DB::table('paciente')->where('id', $patient)->exists()) {
            abort(404, 'Paciente no encontrado.');
        }

        $query = DB::table('paciente_biopsia')
            ->where('id_paciente', $patient)
            ->where('estado', 3);
        if (Schema::hasColumn('paciente_biopsia', 'eliminado')) {
            $query->where('eliminado', 0);
        }
        if (Schema::hasColumn('paciente_biopsia', 'id_empresa')) {
            $query->where('id_empresa', $companyId);
        }

        $document = $query->orderBy('id', 'desc')->first();
        if (!$document || trim((string) $document->nombre) === '') {
            abort(404, 'El paciente no tiene documento de identificación.');
        }

        $directory = realpath(storage_path('app/avatars'));
        $path = realpath(storage_path('app/avatars/'.basename($document->nombre)));
        if (!$directory || !$path || strpos($path, $directory.DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
            abort(404, 'El archivo del documento no está disponible.');
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/pdf';
        return response()->file($path, array(
            'Content-Type' => $mime ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($document->nombre).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ));
    }

    public function updateIdentificationDocument(Request $request, $patient)
    {
        $companyId = $this->companyId($request);
        $patient = trim((string) $patient);
        app(PatientTenantService::class)->requireAccessible($patient, $companyId);
        if (!DB::table('paciente')->where('id', $patient)->exists()) abort(404, 'Paciente no encontrado.');
        $this->validate($request, array('archivo' => 'required|file|mimes:jpeg,jpg,pdf|max:900'), array(
            'archivo.required' => 'Selecciona el documento de identificación que deseas guardar.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'El documento debe estar en formato JPG o PDF.',
            'archivo.max' => 'El documento no puede superar los 900 KB.',
        ));

        $file = $request->file('archivo');
        $extension = strtolower($file->getClientOriginalExtension());
        $name = 'copia_'.$patient.'_'.date('YmdHis').'_'.substr(uniqid(), -6).'.'.$extension;
        if (!Storage::disk('public')->put($name, file_get_contents($file->getRealPath()))) {
            return response()->json(array('message' => 'No se pudo almacenar el documento. Inténtalo nuevamente.'), 500);
        }

        $query = Paciente_Biopsia::where('id_paciente', $patient)->where('estado', 3)
            ->where('id_empresa', $companyId);
        $document = $query->orderBy('id', 'desc')->first();
        $previousName = $document ? $document->nombre : null;
        if (!$document) {
            $document = new Paciente_Biopsia();
            $document->id_paciente = $patient; $document->estado = 3;
            $document->id_usuariocrea = Auth::id(); $document->ip_creacion = $request->ip();
        }
        $document->id_empresa = $companyId; $document->nombre = $name;
        $document->id_usuariomod = Auth::id(); $document->ip_modificacion = $request->ip();
        $document->save();
        if ($previousName && $previousName !== $name) Storage::disk('public')->delete(basename($previousName));

        DB::table('log_usuario')->insert(array(
            'id_usuario'=>Auth::id(), 'ip_usuario'=>$request->ip(), 'descripcion'=>'ACTUALIZA DOCUMENTO IDENTIFICACION V3',
            'dato_ant1'=>$patient, 'dato1'=>$name, 'dato_ant2'=>$previousName, 'dato2'=>$file->getClientOriginalName(),
            'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s'),
        ));
        return response()->json(array(
            'ok'=>true, 'message'=>'El documento de identificación se actualizó correctamente.',
            'name'=>$file->getClientOriginalName(),
            'preview_url'=>route('v3.patients.identification-document', array('patient'=>$patient)),
        ));
    }

    public function legacyIndex(Request $request) { return redirect()->route('v3.patients.index', $this->legacyFilters($request)); }
    public function legacySearch(Request $request) { return redirect()->route('v3.patients.index', $this->legacyFilters($request)); }
    public function legacyCreate() { return redirect()->route('v3.patients.create'); }

    private function responsibleData(Request $request, $principal)
    {
        $prefix = $principal ? '' : 'responsible_';
        return array(
            'id' => trim($request->input($principal ? 'id' : 'responsible_id')),
            'nombre1' => $this->upper($request->input($prefix.'nombre1')),
            'nombre2' => $this->optionalName($request->input($prefix.'nombre2')),
            'apellido1' => $this->upper($request->input($prefix.'apellido1')),
            'apellido2' => $this->upper($request->input($prefix.'apellido2')),
            'fecha_nacimiento' => $request->input($prefix.'fecha_nacimiento'),
            'email' => trim((string) $request->input($principal ? 'patient_email' : 'responsible_email')),
            'telefono1' => $request->input($prefix.'telefono1'), 'telefono2' => $request->input($prefix.'telefono2'),
            'id_pais' => $request->input($prefix.'id_pais'),
        );
    }

    private function saveUser(array $data, $existing, $actor, $ip, $now, $patientTypeId)
    {
        // Vincular una cuenta existente no autoriza a reemplazar sus datos de acceso o personales.
        if ($existing) return;
        $values = array(
            'nombre1' => $data['nombre1'], 'nombre2' => $data['nombre2'], 'apellido1' => $data['apellido1'], 'apellido2' => $data['apellido2'],
            'tipo_documento' => 1, 'id_pais' => $data['id_pais'], 'email' => $data['email'] !== '' ? $data['email'] : null,
            'telefono1' => $data['telefono1'], 'telefono2' => $data['telefono2'], 'estado' => 1,
            'fecha_nacimiento' => $data['fecha_nacimiento'], 'id_usuariomod' => $actor, 'ip_modificacion' => $ip, 'updated_at' => $now,
        );
        DB::table('users')->insert(array_merge($values, array(
            'id' => $data['id'], 'password' => Hash::make(Str::random(40)), 'imagen_url' => ' ',
            'id_tipo_usuario' => $patientTypeId, 'uso_sistema' => 0, 'id_usuariocrea' => $actor, 'ip_creacion' => $ip, 'created_at' => $now,
        )));
    }

    private function legacyFilters(Request $request)
    {
        return array_filter(array('id' => $request->input('id'), 'nombres' => $request->input('nombres'), 'apellidos' => $request->input('apellidos'), 'correo' => $request->input('correo')), function ($value) { return $value !== null && $value !== ''; });
    }
    private function companyId(Request $request) { $id = trim((string) $request->session()->get('id_empresa')); if ($id === '') abort(403, 'Seleccione una empresa activa.'); return $id; }
    private function upper($value) { return mb_strtoupper(trim((string) $value), 'UTF-8'); }
    private function optionalName($value) { $value = $this->upper($value); return $value !== '' ? $value : '(N/A)'; }
    private function isMinor($birth) { return strtotime($birth.' +18 years') > time(); }

    private function agendaContext(Request $request)
    {
        $doctor = trim((string) $request->input('agenda_doctor'));
        if ($doctor === '') return array();

        $type = $request->input('agenda_tipo');
        return array(
            'doctor' => $doctor,
            'inicio' => trim((string) $request->input('agenda_inicio')),
            'fin' => trim((string) $request->input('agenda_fin')),
            'tipo' => $type === null || $type === '' ? 0 : (int) $type,
        );
    }

    private function page($view, array $payload, $title)
    {
        $data = array('controlador' => 'pacientes', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
