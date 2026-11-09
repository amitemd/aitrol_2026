<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\AgendaDocumentoRequisito;
use Sis_medico\AgendaScan;
use Sis_medico\Archivo_historico;
use Sis_medico\Documento;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Services\PatientTenantService;
use Sis_medico\User;

class AgendaDocumentController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request, $doctor, $appointment)
    {
        $agenda = $this->agenda($request, $doctor, $appointment);
        $history = Historiaclinica::where('id_agenda', $agenda->id)->first();
        $documents = $this->documents($agenda);
        $archives = Archivo_historico::where(function ($query) use ($history, $agenda) {
            if ($history) $query->where('id_historia', $history->hcid);
            $query->orWhere('id_agenda', $agenda->id);
        })->get()->keyBy('id_documento');
        $patientIdCopy = $this->patientIdCopy($agenda);
        $uploaderIds = $archives->filter(function ($archive) { return !empty($archive->id_usuariomod); })->pluck('id_usuariomod')->unique()->values();
        $uploaders = $uploaderIds->isEmpty() ? collect() : User::whereIn('id', $uploaderIds)->get()->keyBy('id');
        $patient = $agenda->paciente;
        $insurance = $agenda->seguro;
        $company = $agenda->empresa;
        $procedures = $this->procedureText($agenda);
        $scan = AgendaScan::where('id_agenda', $agenda->id)->first();
        $consentCount = DB::table('formato_consentimiento')->where('estado', 1)
            ->where(function ($query) use ($agenda) { $query->whereNull('id_empresa')->orWhere('id_empresa', $agenda->id_empresa); })
            ->where(function ($query) use ($agenda) { $query->whereNull('id_procedimiento')->orWhere('id_procedimiento', $agenda->id_procedimiento); })->count();
        $user = Auth::user();
        $canManageRequirements = $user && ((method_exists($user, 'isSystemUser') && $user->isSystemUser())
            || in_array((int) $user->id_tipo_usuario, array(1, 4), true));
        $payload = compact('agenda', 'doctor', 'patient', 'insurance', 'company', 'procedures', 'documents', 'archives', 'uploaders', 'patientIdCopy', 'history', 'scan', 'consentCount', 'canManageRequirements');
        $data = array('controlador'=>'agenda-clinica','carpeta'=>'clinical','submodulo'=>'documentos','submodulo_'=>'documentos',
            'titulo'=>'Control de documentos','title'=>'Control de documentos | Aitrol Hospitalario','vista'=>'v3.clinical.agenda_documents.index',
            'obseraudi'=>'Ingreso a control documental de agenda','contenido'=>view('v3.clinical.agenda_documents.index', $payload));
        config(array('data'=>array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }

    public function toggle(Request $request, $doctor, $appointment, $document)
    {
        $agenda = $this->agenda($request, $doctor, $appointment);
        $doc = $this->document($agenda, $document);
        $archive = $this->archive($agenda, $doc);
        $archive->estado = $request->input('estado') ? 1 : 0;
        $archive->fecha_entrega = date('Y-m-d H:i:s');
        $archive->id_usuariomod = Auth::id();
        $archive->ip_modificacion = $request->ip();
        $archive->save();
        return response()->json(array('ok'=>true, 'checked'=>(bool)$archive->estado));
    }

    public function upload(Request $request, $doctor, $appointment, $document)
    {
        $this->validate($request, array('archivo'=>'required|file|mimes:pdf|max:10240'));
        $agenda = $this->agenda($request, $doctor, $appointment);
        $doc = $this->document($agenda, $document);
        $archive = $this->archive($agenda, $doc);
        $extension = strtolower($request->file('archivo')->getClientOriginalExtension());
        $name = 'agenda_'.$agenda->id.'_doc_'.$doc->id.'_'.date('YmdHis').'_'.substr(uniqid(), -6).'.'.$extension;
        $disk = Storage::disk('agenda_documents_private');
        $stored = $disk->put($name, file_get_contents($request->file('archivo')->getRealPath()));
        abort_unless($stored && $disk->exists($name), 500, 'No se pudo almacenar el documento. Inténtalo nuevamente.');
        $previousFile = $archive->archivo;
        $archive->archivo = $name;
        $archive->ruta = 'private/agenda_documentos/';
        $archive->estado = 1;
        $archive->descripcion = trim((string)$request->input('descripcion'));
        $archive->fecha_entrega = date('Y-m-d H:i:s'); $archive->id_usuariomod = Auth::id(); $archive->ip_modificacion = $request->ip();
        $archive->save();
        if ($previousFile && $previousFile !== $name) {
            $previousFile = basename($previousFile);
            if (Storage::disk('agenda_documents_private')->exists($previousFile)) {
                Storage::disk('agenda_documents_private')->delete($previousFile);
            } else {
                Storage::disk('hc')->delete($previousFile);
            }
        }
        if ($request->expectsJson()) {
            return response()->json(array(
                'ok'=>true,
                'message'=>'Documento cargado correctamente.',
                'checked'=>true,
                'fecha_entrega'=>$archive->fecha_entrega,
                'uploader_name'=>$this->personName(Auth::user()),
                'preview_url'=>route('v3.agenda-documents.file',array('doctor'=>$doctor,'appointment'=>$agenda->id,'document'=>$doc->id)),
                'download_url'=>route('v3.agenda-documents.file',array('doctor'=>$doctor,'appointment'=>$agenda->id,'document'=>$doc->id,'descargar'=>1)),
            ));
        }
        return back()->with('agenda_success', 'Documento cargado correctamente.');
    }

    public function file(Request $request, $doctor, $appointment, $document)
    {
        $agenda = $this->agenda($request, $doctor, $appointment); $doc = $this->document($agenda, $document);
        $archive = $this->existingArchive($agenda, $doc); abort_unless($archive && $archive->archivo, 404);
        $path = $this->archiveFilePath($archive);
        $downloadName = basename($archive->archivo);

        // Las versiones anteriores también podían guardar la identificación en
        // paciente_biopsia/avatars. Si el vínculo histórico quedó huérfano, se
        // recupera la copia vigente del paciente en vez de responder con 404.
        if (!$path && (int) $doc->msp === 4) {
            $copy = $this->patientIdCopy($agenda);
            if ($copy) {
                $path = $this->patientIdCopyPath($copy);
                $downloadName = basename($copy->nombre);
            }
        }

        abort_unless($path, 404, 'El archivo del documento no está disponible.');
        $headers = array('Content-Type'=>$this->mimeType($path));
        return $request->input('descargar') ? response()->download($path, $downloadName, $headers) : response()->file($path, $headers);
    }

    public function patientCopy(Request $request, $doctor, $appointment)
    {
        $agenda = $this->agenda($request, $doctor, $appointment);
        $copy = $this->patientIdCopy($agenda); abort_unless($copy, 404, 'El paciente no tiene un documento de identificación disponible.');
        $path = $this->patientIdCopyPath($copy); abort_unless($path, 404, 'El archivo del documento no está disponible.');
        $headers = array('Content-Type'=>$this->mimeType($path));
        return $request->input('descargar') ? response()->download($path, basename($copy->nombre), $headers) : response()->file($path, $headers);
    }

    public function generated(Request $request, $doctor, $appointment, $document)
    {
        $agenda = $this->agenda($request, $doctor, $appointment); $doc = $this->document($agenda, $document);
        $formats = array('acta_servicio','actaentrega','consentimiento_general','solicitud_atencion','entrega_informe','financiamiento_privado','ingreso_paciente');
        abort_unless($doc->sistema && in_array($doc->formato, $formats, true), 404);
        if ($doc->formato === 'ingreso_paciente') {
            return $this->patientAdmissionResponse($request, $agenda, (bool) $request->input('descargar'));
        }
        $patient=$agenda->paciente; $company=$agenda->empresa; $insurance=$agenda->seguro; $physician=$agenda->doctor1;
        $age=$patient->fecha_nacimiento ? Carbon::parse($patient->fecha_nacimiento)->age : null;
        $patientName=$this->personName($patient); $representativeName=trim(implode(' ',array_filter(array($patient->apellido1familiar,$patient->apellido2familiar,$patient->nombre1familiar,$patient->nombre2familiar))));
        $representativeId=$patient->cedulafamiliar; $representativeRelationship=$patient->parentescofamiliar?:$patient->parentesco;
        $validationCode=trim((string)($agenda->cod_val?:($agenda->codigo_validacion_msp?:($patient->codigo_validacion_msp?:($patient->cod_val?:$patient->codigo)))));
        $diagnosis=$agenda->cie10?:$agenda->diagnostico_proc; $procedure=$this->procedureText($agenda);
        $logoData=$this->companyLogoData($company);
        if ($doc->formato === 'actaentrega') {
            $attentionDate = Carbon::parse($agenda->fechaini);
            $months = array(1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE');
            $serviceMonth = $months[(int) $attentionDate->format('n')];
            $serviceYear = $attentionDate->format('Y');
            $historyNumber = trim((string)($patient->historia_clinica?:$patient->id));
            $physicianName = $physician ? $this->personName($physician) : '';
            $providerName = trim((string)($company->razonsocial?:($company->nombrecomercial?:$company->nombre_corto)));
            $providerContact = $physicianName !== '' ? 'DR(A). '.$physicianName : $providerName;
            $providerPhone = trim(implode(' / ', array_filter(array($company->telefono1, $company->telefono2))));
            $providerEmail = trim((string)$company->email);
            $providerDocument = trim((string)($physician ? $physician->id : $company->id_prestador));
            $city = trim((string)($company->ciudad?:$patient->ciudad));
            $attentionDay = $attentionDate->format('d');
            $serviceType = (int)$agenda->est_amb_hos === 1 ? 'HOSPITALARIO' : ((int)$agenda->est_amb_hos === 2 ? 'EMERGENCIA' : 'AMBULATORIO');
            $actaData = compact(
                'agenda','doc','patient','company','insurance','physician','age','patientName','representativeName',
                'representativeId','representativeRelationship','validationCode','diagnosis','procedure','logoData',
                'serviceMonth','serviceYear','historyNumber','providerName','providerContact','providerPhone',
                'providerEmail','providerDocument','city','attentionDay','serviceType'
            );
            // La plantilla ya incorpora el membrete y el logo de la empresa.
            $actaData['_pdf_branding_override'] = array('enabled' => false);
            $pdf=app('dompdf.wrapper');
            $pdf->loadView('v3.clinical.agenda_documents.acta_entrega', $actaData)->setPaper(array(0, 0, 612, 855));
            $filename=$patient->id.'_ACTA_ENTREGA_'.$agenda->id.'.pdf';
            $response=$request->input('descargar') ? $pdf->download($filename) : $pdf->stream($filename);
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return $response;
        }
        $pdf=app('dompdf.wrapper');$pdf->loadView('v3.clinical.agenda_documents.generated',compact('agenda','doc','patient','company','insurance','physician','age','patientName','representativeName','representativeId','representativeRelationship','validationCode','diagnosis','procedure','logoData'));
        $filename=$patient->id.'_'.$doc->tipo_documento.'_'.$agenda->id.'.pdf';
        return $request->input('descargar') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function patientAdmission(Request $request, $patient)
    {
        $company = trim((string) $request->session()->get('id_empresa'));
        abort_if($company === '', 403);
        app(PatientTenantService::class)->requireAccessible($patient, $company);

        $patientRecord = Paciente::with(array('usuario', 'seguro'))->findOrFail($patient);

        $query = Agenda::with(array('paciente', 'seguro', 'empresa', 'procedimiento'))
            ->where('id_empresa', $company)
            ->where('id_paciente', $patient);

        $user = Auth::user();
        if (!$user || !in_array((int) $user->id_tipo_usuario, array(1, 4, 5), true)) {
            $query->where('id_doctor1', Auth::id());
        }

        $agenda = $query->orderBy('fechaini', 'desc')->orderBy('id', 'desc')->first();
        if (!$agenda) {
            return $this->patientAdmissionResponse(
                $request,
                null,
                false,
                $patientRecord,
                $patientRecord->seguro
            );
        }

        return $this->patientAdmissionResponse($request, $agenda, false);
    }

    public function uploadScan(Request $request, $doctor, $appointment)
    {
        $this->validate($request, array('archivo'=>'required|file|mimes:pdf,jpeg,jpg,png|max:20480'));
        $agenda = $this->agenda($request, $doctor, $appointment); $extension = strtolower($request->file('archivo')->getClientOriginalExtension());
        $name = 'scan_'.$agenda->id.'_'.date('YmdHis').'.'.$extension;
        Storage::disk('agenda_scan')->put($name, file_get_contents($request->file('archivo')->getRealPath()));
        $scan = AgendaScan::firstOrNew(array('id_agenda'=>$agenda->id));
        if ($scan->archivo && $scan->archivo !== $name) Storage::disk('agenda_scan')->delete($scan->archivo);
        $scan->archivo=$name; $scan->id_usuariomod=Auth::id(); $scan->ip_modificacion=$request->ip();
        if (!$scan->exists) { $scan->id_usuariocrea=Auth::id(); $scan->ip_creacion=$request->ip(); }
        $scan->save(); return back()->with('agenda_success', 'Archivo escaneado guardado correctamente.');
    }

    public function scan(Request $request, $doctor, $appointment)
    {
        $agenda=$this->agenda($request,$doctor,$appointment); $scan=AgendaScan::where('id_agenda',$agenda->id)->firstOrFail();
        $path=storage_path('app/agenda_scan/'.basename($scan->archivo)); abort_unless(is_file($path),404);
        return $request->input('descargar') ? response()->download($path,basename($scan->archivo)) : response()->file($path);
    }

    private function agenda(Request $request, $doctor, $appointment)
    {
        $company=trim((string)$request->session()->get('id_empresa')); abort_if($company==='',403);
        if ((string)Auth::id()!==(string)$doctor && !in_array((int)Auth::user()->id_tipo_usuario,array(1,4,5),true)) abort(403);
        return Agenda::with(array('paciente','seguro','empresa','procedimiento'))->where('id',$appointment)->where('id_empresa',$company)->where('id_doctor1',$doctor)->firstOrFail();
    }

    private function documents($agenda)
    {
        $insuranceId = (int) $agenda->id_seguro;
        if ($insuranceId <= 0) return collect();

        $requirements = AgendaDocumentoRequisito::with('documento')
            ->where('id_seguro', $insuranceId)
            ->where('estado', 1)
            ->whereIn('proc_consul', array(2, $agenda->proc_consul))
            ->where(function ($query) use ($agenda) {
                $query->whereNull('id_empresa')->orWhere('id_empresa', $agenda->id_empresa);
            })
            ->orderByRaw('id_empresa is null desc')
            ->orderBy('secuencia')
            ->get()
            ->keyBy('id_documento');

        return $requirements->map(function ($requirement) {
            $document = $requirement->documento;
            if (!$document || !(int) $document->estado || (int) $document->est_doc_tarea !== 0) return null;
            if (strtoupper(trim((string) $document->tipo_documento)) === 'ING_PAC'
                || strtoupper(trim((string) $document->nombre_corto)) === 'INGRESO DE PACIENTES') {
                $document->sistema = 1;
                $document->formato = 'ingreso_paciente';
            }
            $document->agenda_documento_obligatorio = (int) $requirement->obligatorio;
            return $document;
        })->filter()->sortBy(function ($document) {
            return sprintf('%08d-%s', (int) $document->secuencia, $document->nombre);
        })->values();
    }

    private function patientAdmissionResponse(Request $request, $agenda, $download, $patient = null, $insurance = null)
    {
        $patient = $patient ?: $agenda->paciente;
        $insurance = $insurance ?: ($agenda ? $agenda->seguro : null);
        $age = $patient->fecha_nacimiento ? Carbon::parse($patient->fecha_nacimiento)->age : null;
        $patientName = $this->personName($patient);
        $representativeName = trim(implode(' ', array_filter(array(
            $patient->apellido1familiar,
            $patient->apellido2familiar,
            $patient->nombre1familiar,
            $patient->nombre2familiar,
        ))));
        $representativeId = $patient->cedulafamiliar;
        $templatePath = resource_path('pdf/clinical/ingreso-pacientes-borja.png');
        abort_unless(is_file($templatePath), 500, 'La plantilla de ingreso de pacientes no está disponible.');
        $templateData = 'data:image/png;base64,'.base64_encode(file_get_contents($templatePath));
        $historyNumber = $patient->id;
        $patient->load('usuario');
        $patientEmail = $patient->mail_opcional ?: optional($patient->usuario)->email;
        $patientPhones = trim(implode(' / ', array_filter(array($patient->telefono1, $patient->telefono2))));
        $patientSex = (int) $patient->sexo === 1 ? 'MASCULINO' : ((int) $patient->sexo === 2 ? 'FEMENINO' : 'NO REGISTRADO');
        $attentionDate = $agenda && $agenda->fechaini
            ? Carbon::parse($agenda->fechaini)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');
        $declarantName = $patientName;
        $declarantId = $patient->id;
        if ($age !== null && $age < 18 && $representativeName !== '') {
            $declarantName = $representativeName;
            $declarantId = $representativeId;
        }
        $isPrivatePatient = !$insurance || (int) $insurance->tipo === 2;
        $isPrivateInsurance = $insurance && (int) $insurance->tipo === 1;
        $pdf = app('dompdf.wrapper');
        $admissionData = compact(
            'agenda','patient','insurance','age','patientName','templateData','historyNumber','patientEmail',
            'patientPhones','patientSex','attentionDate','declarantName','declarantId','isPrivatePatient','isPrivateInsurance'
        );
        // El formulario ya contiene su membrete en la imagen base. Impide que
        // BrandedPdf agregue nuevamente el logo corporativo en la esquina.
        $admissionData['_pdf_branding_override'] = array('enabled' => false);
        $pdf->loadView('v3.clinical.agenda_documents.patient_admission', $admissionData)
            ->setPaper('A4', 'portrait');
        $filename = $patient->id.'_INGRESO_PACIENTE_'.($agenda ? $agenda->id : date('YmdHis')).'.pdf';
        $response = $download ? $pdf->download($filename) : $pdf->stream($filename);
        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }

    private function document($agenda,$id) { $doc=$this->documents($agenda)->first(function($item)use($id){return (string)$item->id===(string)$id;}); abort_unless($doc,404); return $doc; }
    private function existingArchive($agenda,$doc){$history=Historiaclinica::where('id_agenda',$agenda->id)->first();return Archivo_historico::where('id_documento',$doc->id)->where(function($q)use($history,$agenda){if($history)$q->where('id_historia',$history->hcid);$q->orWhere('id_agenda',$agenda->id);})->first();}
    private function archive($agenda,$doc){return DB::transaction(function()use($agenda,$doc){Agenda::where('id',$agenda->id)->where('id_empresa',$agenda->id_empresa)->lockForUpdate()->firstOrFail();$archive=$this->existingArchive($agenda,$doc);if($archive)return $archive;$history=Historiaclinica::where('id_agenda',$agenda->id)->first();return Archivo_historico::create(array('id_historia'=>$history?$history->hcid:null,'id_agenda'=>$agenda->id,'id_documento'=>$doc->id,'tipo_documento'=>$doc->tipo_documento,'descripcion'=>'','estado'=>0,'id_usuario_entrega'=>$agenda->id_paciente,'id_usuario_recibe'=>Auth::id(),'id_usuariocrea'=>Auth::id(),'id_usuariomod'=>Auth::id(),'ip_creacion'=>request()->ip(),'ip_modificacion'=>request()->ip(),'fecha_entrega'=>date('Y-m-d H:i:s')));},3);}

    private function archiveFilePath($archive)
    {
        if (!$archive || !trim((string) $archive->archivo)) return null;

        $filename = basename(str_replace('\\', '/', (string) $archive->archivo));
        $route = strtolower(trim(str_replace('\\', '/', (string) $archive->ruta), '/'));
        $directories = array();

        if (strpos($route, 'private/agenda_documentos') !== false || strpos($route, 'agenda_documentos') !== false) {
            $directories[] = storage_path('app/private/agenda_documentos');
        } elseif (strpos($route, 'avatars') !== false) {
            $directories[] = storage_path('app/avatars');
        } elseif (strpos($route, 'hc') !== false) {
            $directories[] = storage_path('app/hc');
        }

        $directories = array_unique(array_merge($directories, array(
            storage_path('app/private/agenda_documentos'),
            storage_path('app/hc'),
            storage_path('app/avatars'),
        )));

        foreach ($directories as $directory) {
            $path = $directory.DIRECTORY_SEPARATOR.$filename;
            if (is_file($path)) return $path;
        }

        return null;
    }

    private function patientIdCopy($agenda)
    {
        $copies = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)
            ->where('estado', 3)
            ->where('id_empresa', $agenda->id_empresa)
            ->orderBy('id', 'desc')
            ->get();

        return $copies->first(function ($copy) {
            return $this->patientIdCopyPath($copy) !== null;
        });
    }

    private function patientIdCopyPath($copy)
    {
        if (!$copy || !trim((string) $copy->nombre)) return null;
        $path = storage_path('app/avatars/'.basename(str_replace('\\', '/', (string) $copy->nombre)));
        return is_file($path) ? $path : null;
    }

    private function mimeType($path)
    {
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : false;
        return $mime ?: 'application/octet-stream';
    }

    private function procedureText($agenda)
    {
        $names = array();

        if ($agenda->procedimiento) {
            $names[] = $agenda->procedimiento->observacion ?: $agenda->procedimiento->nombre;
        }

        foreach (DB::table('agenda_procedimiento as ap')
            ->join('procedimiento as p', 'p.id', '=', 'ap.id_procedimiento')
            ->where('ap.id_agenda', $agenda->id)
            ->pluck('p.nombre') as $name) {
            $names[] = $name;
        }

        $names = array_values(array_unique(array_filter($names)));

        if ($names) {
            return implode(' + ', $names);
        }

        // Algunas agendas de procedimiento no tienen todavía un registro en
        // agenda_procedimiento. En ese caso se conserva el tipo real de la agenda.
        return (int) $agenda->proc_consul === 1 ? 'Procedimiento' : 'Consulta';
    }

    private function companyLogoData($company)
    {
        if (!$company) return null;

        $paths = array();
        foreach (array($company->logo, $company->logo_form) as $logo) {
            if (trim((string)$logo) !== '') {
                $paths[] = storage_path('app/logo/'.basename(str_replace('\\', '/', (string)$logo)));
            }
        }

        // Respaldo institucional para Clínica Borja cuando el logo histórico
        // de la empresa todavía no está disponible en storage/app/logo.
        if ((string)$company->id === '0992914246001') {
            $paths[] = public_path('assets/images/logo_certificado_borja.png');
        }

        foreach (array_unique($paths) as $path) {
            if (!is_file($path)) continue;
            $mime = function_exists('mime_content_type') ? mime_content_type($path) : false;
            return 'data:'.($mime ?: 'image/png').';base64,'.base64_encode(file_get_contents($path));
        }

        return null;
    }
    private function personName($person){return trim(implode(' ',array_filter(array($person->apellido1,$person->apellido2,$person->nombre1,$person->nombre2),function($value){return $value&&$value!=='N/A'&&$value!=='(N/A)';})));}
}
