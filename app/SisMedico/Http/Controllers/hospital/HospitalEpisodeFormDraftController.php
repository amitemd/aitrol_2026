<?php

namespace Sis_medico\Http\Controllers\hospital;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Sis_medico\ApProcedimiento;
use Sis_medico\ClinicalDocument;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Empresa;
use Sis_medico\Exceptions\ClinicalDocumentIncompleteException;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_protocolo;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HospitalEpisodeFormDraft;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Procedimiento;
use Sis_medico\Services\ClinicalRecordService;
use Sis_medico\Services\ClinicalRecordSignatureService;
use Sis_medico\Services\ClinicalWardProcedureService;
use Sis_medico\Services\CompanyPdfLogoService;
use Sis_medico\Services\DoctorSignatureService;
use Sis_medico\Services\HospitalDocumentService;
use Sis_medico\Services\HospitalEpisodeBodyDiagram;
use Sis_medico\Services\HospitalEpisodeEmergencySource;
use Sis_medico\Services\HospitalEpisodeForm020;
use Sis_medico\Services\HospitalEpisodeForm022;
use Sis_medico\Services\HospitalEpisodeForm024;
use Sis_medico\Services\HospitalEpisodeFormCatalog;
use Sis_medico\Services\HospitalEpisodeMedicationPrescriptionService;
use Sis_medico\Services\HospitalEpisodePhysicalExam;
use Sis_medico\Services\HospitalEpisodeProfessionalBlockService;
use Sis_medico\Services\HospitalSurgeryChecklistContext;
use Sis_medico\Services\HospitalSurgeryChecklistPdf;
use Sis_medico\Services\TransanestheticCanvasImage;
use Sis_medico\Services\UserElectronicCertificateService;
use Sis_medico\Support\RequestSchema;
use Sis_medico\Support\RequestSchema as Schema;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HospitalEpisodeFormDraftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, $code, $id_solicitud)
    {
        $form = HospitalEpisodeFormCatalog::find($code);
        abort_unless($form, 404);
        $this->assertStorageAvailable();

        $documentService = app(HospitalDocumentService::class);
        $solicitud = $documentService->obtain($id_solicitud);
        $solicitud->loadMissing('paciente');
        $encounter = ClinicalEncounter::findOrFail($solicitud->encounter_id);
        $canWrite = HospitalDocumentService::writable($solicitud, $encounter, true)
            && $this->canAuthor($code);
        if ($canWrite) {
            try {
                $documentService->authorize($solicitud, 'hospital.documents.write');
            } catch (HttpExceptionInterface $exception) {
                if ($exception->getStatusCode() !== 403) {
                    throw $exception;
                }
                $canWrite = false;
            }
        }
        $entries = HospitalEpisodeFormDraft::where('encounter_id', $encounter->id)
            ->where('solicitud_id', $solicitud->id)
            ->where('form_code', $code)->orderByDesc('id')->get();
        $entry = $request->query('new') !== null ? null : $entries->first();
        if ($request->query('entry') !== null) {
            $entry = $entries->firstWhere('id', $request->query('entry'));
            abort_unless($entry, 404);
        }
        $officialConsultations = collect();
        if (in_array($code, ['007', '007B'], true)
            && Schema::hasTable('ho_ordenes') && Schema::hasTable('interconsulta')) {
            $officialConsultations = DB::table('ho_ordenes as order_link')
                ->join('interconsulta as consult', 'consult.id', '=', 'order_link.clave_tipo')
                ->where('order_link.id_ho_solicitud', $solicitud->id)
                ->where('order_link.tipo', 'INTERCONSULTA')
                ->where('consult.id_paciente', $solicitud->id_paciente)
                ->where('consult.estado', 1)
                ->select('consult.id', 'consult.fecha', 'consult.especialidad')
                ->distinct()->orderByDesc('consult.id')->get();
        }
        $emergencySource = app(HospitalEpisodeEmergencySource::class)->forForm($solicitud->id, $code);
        $prescribedMedications022 = $code === '022'
            ? app(HospitalEpisodeMedicationPrescriptionService::class)->forEpisode($solicitud)
            : [];
        $form022Payload = null;
        $medicationAdministrationUrl = null;
        if ($code === '022') {
            $form022Payload = HospitalEpisodeForm022::reconcilePrescriptions(
                $entry ? $entry->payload : [],
                $prescribedMedications022
            );
            $recipeId = collect($prescribedMedications022)->pluck('receta_id')->filter()->first();
            if ($recipeId && \Route::has('hospitalizacion.descargo_enfermeria_detalle')) {
                $medicationAdministrationUrl = route('hospitalizacion.descargo_enfermeria_detalle', [
                    'id' => $recipeId, 'id_solicitud' => $solicitud->id, 'standalone' => 1,
                ]);
            }
        }

        $entryDocument = $entry ? $this->draftDocument($entry) : null;
        $entryProof = $entry && $entryDocument && $entryDocument->signed_at
            ? app(ClinicalRecordSignatureService::class)->issueForm017Draft($entry) : null;

        return view('hospital.documentos.formulario-borrador', [
            'form' => $form, 'fields' => HospitalEpisodeFormCatalog::fields($code),
            'solicitud' => $solicitud, 'entries' => $entries, 'entry' => $entry,
            'canWrite' => $canWrite, 'officialConsultations' => $officialConsultations,
            'emergencySource' => $emergencySource,
            'prescribedMedications022' => $prescribedMedications022,
            'form022Payload' => $form022Payload,
            'medicationAdministrationUrl' => $medicationAdministrationUrl,
            'form024Images' => $code === '024' && $entry ? $this->form024ImageMetadata($entry) : [],
            'entrySigned' => (bool) ($entryDocument && $entryDocument->signed_at),
            'entryProof' => $entryProof,
        ]);
    }

    public function pdf(Request $request, $code, $id_solicitud)
    {
        $form = HospitalEpisodeFormCatalog::find($code);
        abort_unless($form, 404);

        // obtain() applies the same episode, patient and company access checks as the catalog.
        $solicitud = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $solicitud->loadMissing('paciente');
        $entry = null;
        $templateOnly = $request->boolean('template');
        if (Schema::hasTable('hospital_episode_form_drafts')) {
            $entries = HospitalEpisodeFormDraft::where('encounter_id', $solicitud->encounter_id)
                ->where('solicitud_id', $solicitud->id)->where('form_code', $code);
            $entry = $request->query('entry') !== null
                ? (clone $entries)->where('id', $request->query('entry'))->first()
                : $entries->orderByDesc('id')->first();
            abort_if($request->query('entry') !== null && ! $entry, 404);
        } else {
            abort_if($request->query('entry') !== null, 404);
        }

        $pdf = app('dompdf.wrapper');
        $pdf->setOptions(['isRemoteEnabled' => false, 'defaultMediaType' => 'print']);
        $emergencySource = app(HospitalEpisodeEmergencySource::class)->forForm($solicitud->id, $code);
        if ($templateOnly) {
            $entry = null;
            $emergencySource = ['suggestions' => [], 'records' => []];
        }
        $form022Payload = null;
        if ($code === '022' && ! $templateOnly) {
            $prescriptions022 = app(HospitalEpisodeMedicationPrescriptionService::class)->forEpisode($solicitud);
            $form022Payload = HospitalEpisodeForm022::reconcilePrescriptions(
                $entry ? $entry->payload : [],
                $prescriptions022
            );
        }
        $encounter = ClinicalEncounter::find($solicitud->encounter_id);
        $companyId = $encounter && $encounter->id_empresa
            ? (string) $encounter->id_empresa
            : (string) $request->session()->get('id_empresa');
        $empresa = Schema::hasTable('empresa') && $companyId !== '' ? Empresa::find($companyId) : null;
        if ($code === 'LVCS') {
            $notes = [];
            $payload = $entry ? (array) $entry->payload : [];
            foreach (HospitalEpisodeFormCatalog::fields($code) as $key => $label) {
                $value = $payload[$key] ?? null;
                if (is_scalar($value) && trim((string) $value) !== '') {
                    $notes[$label] = trim((string) $value);
                }
            }
            $context = app(HospitalSurgeryChecklistContext::class)->forEpisode($solicitud, $empresa);
            $logo = $empresa ? app(CompanyPdfLogoService::class)->path($empresa) : null;
            $content = app(HospitalSurgeryChecklistPdf::class)->render($context, $notes, $logo);

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="formulario-LVCS-episodio-'.$solicitud->id.'.pdf"',
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            ]);
        }
        $electronicProof = null;
        if ($code === '017' && ! $templateOnly) {
            $electronicProof = $entry
                ? app(ClinicalRecordSignatureService::class)->issueForm017Draft($entry)
                : $this->signedProtocolProofForEpisode($solicitud);
        }
        $professionalBlocks = app(HospitalEpisodeProfessionalBlockService::class);
        $entryDocument = $entry ? $this->draftDocument($entry) : null;
        $professionalBlock = $electronicProof
            ? $professionalBlocks->forUser(
                $electronicProof['author_id'],
                $electronicProof['signed_at'],
                $electronicProof,
                $companyId
            )
            : $professionalBlocks->forDraft($entry);
        if ($entry && ! $electronicProof && (! $entryDocument || ! $entryDocument->signed_at)) {
            $professionalBlock['electronic'] = false;
            $professionalBlock['qr_source'] = null;
            $professionalBlock['signature_image'] = null;
            $professionalBlock['seal_image'] = null;
        }
        $transanestesicoImage = $code === '018A'
            ? $this->transanestheticImageForEpisode($solicitud)
            : null;
        $form017DiagramImages = $code === '017' && $entry
            ? $this->form017DiagramDataUris($entry) : [];
        $form024ProcedureImages = $code === '024' && $entry
            ? $this->form024ImageDataUris($entry) : [];
        $pdf->loadView('hospital.documentos.formulario-borrador-pdf', [
            'form' => $form, 'fields' => HospitalEpisodeFormCatalog::fields($code),
            'solicitud' => $solicitud, 'entry' => $entry, 'emergencySource' => $emergencySource,
            'empresa' => $empresa, 'form022Payload' => $form022Payload,
            'transanestesicoImage' => $transanestesicoImage,
            'form017DiagramImages' => $form017DiagramImages,
            'form024ProcedureImages' => $form024ProcedureImages,
            'professionalBlock' => $professionalBlock,
            'entrySigned' => (bool) ($electronicProof || ($entryDocument && $entryDocument->signed_at)),
            '_pdf_branding_company_id' => $companyId,
            '_pdf_branding_override' => [
                'enabled' => true, 'position' => 'top-left', 'width_mm' => 40, 'height_mm' => 14,
                'offset_x_mm' => 0, 'offset_y_mm' => 0, 'opacity_percent' => 100,
                'rotation_deg' => 0, 'keep_aspect_ratio' => true, 'lock_content_position' => true,
            ],
        ])->setPaper('a4', in_array($code, ['018A', '019'], true) ? 'landscape' : 'portrait');

        $response = $pdf->stream('formulario-'.$code.'-episodio-'.$solicitud->id.'.pdf');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function diagramImage($id_solicitud, $entry, $position)
    {
        $solicitud = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $draft = HospitalEpisodeFormDraft::where('id', $entry)
            ->where('encounter_id', $solicitud->encounter_id)
            ->where('solicitud_id', $solicitud->id)->where('form_code', '017')->firstOrFail();
        $images = $this->form017DiagramMetadata($draft);
        $image = $images[(int) $position] ?? null;
        abort_unless($image && Storage::disk('local')->exists($image['path']), 404);

        return response()->file(Storage::disk('local')->path($image['path']), [
            'Content-Type' => $image['mime'],
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function form024Image($id_solicitud, $entry, $position)
    {
        $solicitud = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $draft = HospitalEpisodeFormDraft::where('id', $entry)
            ->where('encounter_id', $solicitud->encounter_id)
            ->where('solicitud_id', $solicitud->id)->where('form_code', '024')->firstOrFail();
        $images = $this->form024ImageMetadata($draft);
        $image = $images[(int) $position] ?? null;
        abort_unless($image && Storage::disk('local')->exists($image['path']), 404);

        return response()->file(Storage::disk('local')->path($image['path']), [
            'Content-Type' => $image['mime'],
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function signedProtocolProofForEpisode(Ho_Solicitud $solicitud)
    {
        foreach (['clinical_documents', 'hc_protocolo', 'hc_procedimientos', 'historiaclinica'] as $table) {
            if (! Schema::hasTable($table)) {
                return null;
            }
        }

        $protocolId = DB::table('clinical_documents as document')
            ->join('hc_protocolo as protocol', function ($join) {
                $join->on('document.source_id', '=', DB::raw('CAST(protocol.id AS CHAR)'))
                    ->where('document.source_type', '=', 'hc_protocolo');
            })
            ->join('hc_procedimientos as procedure', 'procedure.id', '=', 'protocol.id_hc_procedimientos')
            ->join('historiaclinica as history', 'history.hcid', '=', 'procedure.id_hc')
            ->where('history.encounter_id', $solicitud->encounter_id)
            ->whereNotNull('document.signed_at')
            ->whereNotNull('document.signed_by')
            ->orderByDesc('document.signed_at')
            ->value('protocol.id');
        if (! $protocolId) {
            return null;
        }

        $protocol = hc_protocolo::find($protocolId);

        return $protocol ? app(ClinicalRecordSignatureService::class)->issueForm017($protocol) : null;
    }

    /**
     * Recupera el último lienzo 018A que realmente pertenece al episodio.
     * El formulario genérico también debe mostrar el gráfico clínico guardado,
     * aunque todavía no exista un borrador textual del 018A.
     */
    private function transanestheticImageForEpisode(Ho_Solicitud $solicitud)
    {
        if (! Schema::hasTable('historiaclinica') || ! Schema::hasTable('hc_anestesiologia')) {
            return null;
        }

        $historyQuery = DB::table('historiaclinica')
            ->where('id_paciente', $solicitud->id_paciente);
        if (Schema::hasColumn('historiaclinica', 'encounter_id') && $solicitud->encounter_id) {
            $historyQuery->where('encounter_id', $solicitud->encounter_id);
        } elseif ($solicitud->id_agenda) {
            $historyQuery->where('id_agenda', $solicitud->id_agenda);
        } else {
            // Sin un vínculo inequívoco no se debe reutilizar el gráfico de
            // otro episodio del mismo paciente.
            return null;
        }

        $historyIds = $historyQuery->pluck('hcid');
        if ($historyIds->isEmpty()) {
            return null;
        }

        $baseQuery = Hc_Anestesiologia::whereIn('id_hc', $historyIds);
        $record = null;
        if ($solicitud->id_hcproc) {
            $record = (clone $baseQuery)
                ->where('id_hc_procedimientos', $solicitud->id_hcproc)
                ->orderByDesc('id')
                ->get()
                ->first(function ($anesthesia) {
                    return trim((string) $anesthesia->tran_anastesico_path) !== ''
                        || trim((string) $anesthesia->url_imagen) !== '';
                });
        }
        if (! $record) {
            $record = $baseQuery->orderByDesc('id')->get()->first(function ($anesthesia) {
                return trim((string) $anesthesia->tran_anastesico_path) !== ''
                    || trim((string) $anesthesia->url_imagen) !== '';
            });
        }
        if (! $record) {
            return null;
        }

        return TransanestheticCanvasImage::dataUri($record->tran_anastesico_path)
            ?: TransanestheticCanvasImage::dataUri($record->url_imagen);
    }

    public function store(Request $request, $code, $id_solicitud)
    {
        abort_unless(HospitalEpisodeFormCatalog::find($code), 404);
        $this->assertStorageAvailable();
        abort_unless($this->canAuthor($code), 403, 'La edición requiere un profesional clínico o un usuario de Sistemas autorizado.');

        $fields = HospitalEpisodeFormCatalog::fields($code);
        $rules = [
            'entry_id' => 'nullable|integer|min:1',
            'preserve_missing' => 'nullable|boolean',
            'data' => 'required|array',
        ];
        foreach ($fields as $key => $label) {
            $maximum = $key === 'body_marks' ? 20000 : (in_array($code, ['020', '022'], true) ? 60000 : 10000);
            $rules['data.'.$key] = 'nullable|string|max:'.$maximum;
        }
        if ($code === '003') {
            $rules['data.examen_fisico_marcas'] = 'nullable|array|max:25';
        }
        if ($code === '017' && $request->boolean('prepare_surgery')) {
            $rules = array_merge($rules, [
                'prepare_surgery' => 'required|boolean',
                'diagnoses' => 'required|array',
                'diagnoses.pre' => 'required|array|min:1|max:10',
                'diagnoses.pre.*' => 'required|string|max:16|distinct',
                'diagnoses.post' => 'nullable|array|max:10',
                'diagnoses.post.*' => 'required|string|max:16|distinct',
                'surgery' => 'required|array',
                'surgery.procedure_codes' => 'required|array|min:1|max:10',
                'surgery.procedure_codes.*' => 'required|string|max:30|distinct',
                'surgery.performed_procedure_codes' => 'nullable|array|max:10',
                'surgery.performed_procedure_codes.*' => 'required|string|max:30|distinct',
                'surgery.procedure_id' => 'nullable|integer|min:1',
                'surgery.doctor_id' => 'required|string|max:20',
                'surgery.room_id' => 'required|integer|min:1',
                'surgery.start' => 'required|date',
                'surgery.end' => 'required|date|after:surgery.start',
                'diagram_images' => 'nullable|array|max:3',
                'diagram_images.*' => 'image|mimes:jpeg,jpg,png|max:4096',
                'diagram_remove' => 'nullable|array|max:3',
                'diagram_remove.*' => 'integer|min:0|max:2|distinct',
            ]);
        }
        if ($code === '024') {
            $rules = array_merge($rules, [
                'procedure_code' => 'nullable|string|max:30',
                'procedure_images' => 'nullable|array|max:3',
                'procedure_images.*' => 'image|mimes:jpeg,jpg,png|max:4096',
                'procedure_image_remove' => 'nullable|array|max:3',
                'procedure_image_remove.*' => 'integer|min:0|max:2|distinct',
            ]);
        }
        $validated = $request->validate($rules);
        $payload = [];
        foreach ($fields as $key => $label) {
            if ($key === 'examen_fisico_marcas') {
                try {
                    $payload[$key] = HospitalEpisodePhysicalExam::normalize($validated['data'][$key] ?? null);
                } catch (\InvalidArgumentException $exception) {
                    throw ValidationException::withMessages(['data.'.$key => $exception->getMessage()]);
                }
            } else {
                $payload[$key] = trim((string) ($validated['data'][$key] ?? ''));
            }
        }
        $catalogProcedure = null;
        $form017Selections = null;
        if ($code === '017' && $request->boolean('prepare_surgery')) {
            $preDiagnoses = $this->resolveDiagnosisSelections($validated['diagnoses']['pre']);
            $postDiagnoses = $this->resolveDiagnosisSelections($validated['diagnoses']['post'] ?? []);
            $projectedProcedures = $this->resolveProcedureSelections($validated['surgery']['procedure_codes']);
            $performedProcedures = $this->resolveProcedureSelections($validated['surgery']['performed_procedure_codes'] ?? []);
            $catalogProcedure = $projectedProcedures[0];
            $payload['cie_preoperatorio'] = implode(' / ', array_column($preDiagnoses, 'code'));
            $payload['diagnosticos_preoperatorios'] = implode("\n", array_column($preDiagnoses, 'description'));
            $payload['cie_postoperatorio'] = implode(' / ', array_column($postDiagnoses, 'code'));
            $payload['diagnosticos_postoperatorios'] = implode("\n", array_column($postDiagnoses, 'description'));
            $payload['procedimiento'] = implode("\n", array_column($projectedProcedures, 'text'));
            $payload['_procedimiento_proyectado_codigo'] = json_encode(array_column($projectedProcedures, 'code'));
            $payload['indicacion'] = implode("\n", array_column($performedProcedures, 'text'));
            $payload['_procedimiento_realizado_codigos'] = json_encode(array_column($performedProcedures, 'code'));
            $plannedStart = Carbon::parse($validated['surgery']['start']);
            $plannedEnd = Carbon::parse($validated['surgery']['end']);
            $payload['_inicio_previsto'] = $plannedStart->format('Y-m-d\TH:i');
            $payload['_fin_previsto'] = $plannedEnd->format('Y-m-d\TH:i');
            $form017Selections = [
                'pre_diagnoses' => $preDiagnoses,
                'post_diagnoses' => $postDiagnoses,
                'projected_procedures' => $projectedProcedures,
                'performed_procedures' => $performedProcedures,
            ];
        }
        if ($code === '017') {
            foreach ($payload as $key => $value) {
                if (! is_string($value) || in_array($key, [
                    'firma_profesional', 'sello_identificacion', '_procedimiento_proyectado_codigo',
                    '_procedimiento_realizado_codigos',
                ], true)) {
                    continue;
                }
                $payload[$key] = mb_strtoupper($value, 'UTF-8');
            }
        }
        if ($code === '003') {
            try {
                $payload['body_marks'] = HospitalEpisodeBodyDiagram::normalize($payload['body_marks']);
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['data.body_marks' => $exception->getMessage()]);
            }
        } elseif ($code === '020') {
            try {
                $payload = HospitalEpisodeForm020::normalizePayload($payload);
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['data' => $exception->getMessage()]);
            }
        } elseif ($code === '022') {
            try {
                $payload = HospitalEpisodeForm022::normalizePayload($payload);
                $solicitud022 = app(HospitalDocumentService::class)->obtain($id_solicitud);
                $payload = HospitalEpisodeForm022::reconcilePrescriptions(
                    $payload,
                    app(HospitalEpisodeMedicationPrescriptionService::class)->forEpisode($solicitud022)
                );
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['data' => $exception->getMessage()]);
            }
        } elseif ($code === '024') {
            try {
                $payload = HospitalEpisodeForm024::normalizePayload($payload);
                $procedureCode = trim((string) ($validated['procedure_code'] ?? ''));
                if ($procedureCode !== '') {
                    $procedure = ApProcedimiento::where('codigo', $procedureCode)
                        ->where('estado', 1)->first(['codigo', 'descripcion']);
                    if (! $procedure) {
                        throw ValidationException::withMessages([
                            'procedure_code' => 'El procedimiento seleccionado no existe o no está vigente.',
                        ]);
                    }
                    $payload['_procedure_code'] = (string) $procedure->codigo;
                    $presetProcedure = (string) (HospitalEpisodeForm024::presets()[$payload['tipo_consentimiento']]['procedimiento'] ?? '');
                    if ($payload['procedimiento'] === '' || $payload['procedimiento'] === $presetProcedure) {
                        $payload['procedimiento'] = (string) $procedure->codigo.' - '
                            .mb_strtoupper(trim((string) $procedure->descripcion), 'UTF-8');
                    }
                }
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['data.tipo_consentimiento' => $exception->getMessage()]);
            }
        }
        if (($code === '020' && ! HospitalEpisodeForm020::hasData($payload))
            || ($code === '022' && ! HospitalEpisodeForm022::hasData($payload))
            || ($code === '024' && ! HospitalEpisodeForm024::hasData($payload))
            || (! in_array($code, ['020', '022', '024'], true) && ! count(array_filter($payload, function ($value) {
                return $value !== '';
            })))) {
            return back()->withErrors(['data' => 'Escriba al menos un dato antes de guardar.'])->withInput();
        }

        $submittedData = (array) $request->input('data', []);
        $preserveMissing = $request->boolean('preserve_missing');
        $surgeryPlan = $code === '017' && $request->boolean('prepare_surgery')
            ? (array) $validated['surgery'] : null;
        $entry = DB::transaction(function () use ($request, $id_solicitud, $code, $validated, $payload, $submittedData, $preserveMissing, $surgeryPlan, $catalogProcedure, $form017Selections) {
            $solicitud = Ho_Solicitud::where('id', $id_solicitud)->lockForUpdate()->firstOrFail();
            $encounter = app(HospitalDocumentService::class)->authorize($solicitud, 'hospital.documents.write');
            abort_unless(HospitalDocumentService::writable($solicitud, $encounter, true), 403,
                'El episodio ya no admite documentación.');

            $entry = null;
            if (! empty($validated['entry_id'])) {
                $entry = HospitalEpisodeFormDraft::where('id', $validated['entry_id'])
                    ->where('encounter_id', $encounter->id)
                    ->where('solicitud_id', $solicitud->id)->where('form_code', $code)
                    ->lockForUpdate()->first();
                abort_unless($entry, 404);
                $signedDocument = $this->draftDocument($entry);
                abort_if($signedDocument && $signedDocument->signed_at, 409,
                    'Este Formulario 017 ya fue firmado y es inmutable. Cree un nuevo registro para corregirlo.');
                if ($preserveMissing) {
                    $storedPayload = (array) $entry->payload;
                    foreach (array_keys($submittedData) as $submittedKey) {
                        if (array_key_exists($submittedKey, $payload)) {
                            $storedPayload[$submittedKey] = $payload[$submittedKey];
                        }
                    }
                    if ($code === '017' && isset($payload['_procedimiento_proyectado_codigo'])) {
                        $storedPayload['_procedimiento_proyectado_codigo'] = $payload['_procedimiento_proyectado_codigo'];
                        $storedPayload['procedimiento'] = $payload['procedimiento'];
                        $storedPayload['_procedimiento_realizado_codigos'] = $payload['_procedimiento_realizado_codigos'];
                        $storedPayload['indicacion'] = $payload['indicacion'];
                        $storedPayload['cie_preoperatorio'] = $payload['cie_preoperatorio'];
                        $storedPayload['diagnosticos_preoperatorios'] = $payload['diagnosticos_preoperatorios'];
                        $storedPayload['cie_postoperatorio'] = $payload['cie_postoperatorio'];
                        $storedPayload['diagnosticos_postoperatorios'] = $payload['diagnosticos_postoperatorios'];
                        $storedPayload['_inicio_previsto'] = $payload['_inicio_previsto'];
                        $storedPayload['_fin_previsto'] = $payload['_fin_previsto'];
                    }
                    $entry->payload = $storedPayload;
                } else {
                    $storedPayload = (array) $entry->payload;
                    if ($code === '017' && ! isset($payload['_procedimiento_proyectado_codigo'])
                        && ! empty($storedPayload['_procedimiento_proyectado_codigo'])) {
                        $payload['_procedimiento_proyectado_codigo'] = $storedPayload['_procedimiento_proyectado_codigo'];
                    }
                    if ($code === '024' && ! empty($storedPayload['_procedure_images'])) {
                        $payload['_procedure_images'] = $storedPayload['_procedure_images'];
                    }
                    $entry->payload = $payload;
                }
                $entry->updated_by = (string) Auth::id();
                $entry->save();
            } else {
                $entry = HospitalEpisodeFormDraft::create([
                    'encounter_id' => $encounter->id,
                    'solicitud_id' => $solicitud->id,
                    'form_code' => $code,
                    'payload' => $payload,
                    'created_by' => (string) Auth::id(),
                    'updated_by' => (string) Auth::id(),
                ]);
            }

            if ($code === '017' && $form017Selections) {
                $this->syncForm017Selections($entry, $form017Selections);
                $this->syncForm017DiagramImages($request, $entry);
            }
            if ($code === '024') {
                $this->syncForm024Images($request, $entry);
            }

            if ($surgeryPlan) {
                $clinicalProcedureId = ! empty($surgeryPlan['procedure_id'])
                    ? (int) $surgeryPlan['procedure_id'] : null;
                if (! $clinicalProcedureId && $catalogProcedure) {
                    $hasClinicalProcedureCode = RequestSchema::hasColumn('procedimiento', 'codigo');
                    $clinicalProcedure = Procedimiento::where('estado', 1)
                        ->where(function ($procedure) use ($catalogProcedure, $hasClinicalProcedureCode) {
                            $procedure->where('nombre', trim((string) $catalogProcedure['description']));
                            if ($hasClinicalProcedureCode) {
                                $procedure->orWhere('codigo', (string) $catalogProcedure['code']);
                            }
                        })->first();
                    $clinicalProcedureId = $clinicalProcedure ? (int) $clinicalProcedure->id : null;
                }
                if ($clinicalProcedureId) {
                    $planningRequest = new Request([
                        'id_solicitud' => $solicitud->id,
                        'procedimiento' => [$clinicalProcedureId],
                        'doctor' => (string) $surgeryPlan['doctor_id'],
                        'sala' => (int) $surgeryPlan['room_id'],
                        'fecha' => $surgeryPlan['start'],
                        'fechafin' => $surgeryPlan['end'],
                    ]);
                    app(ClinicalWardProcedureService::class)->crear_procedimiento($planningRequest);
                }
            }

            return $entry;
        }, 3);

        $redirectUrl = route('hospital.documents.form.show', [
            'code' => $code, 'id_solicitud' => $id_solicitud, 'entry' => $entry->id,
        ] + ($request->boolean('embedded') ? ['embedded' => 1] : []));
        if ($code === '017' && $request->input('continue_to') === 'preoperative') {
            return redirect()->to(app(HospitalDocumentService::class)->currentPreoperativeUrl($id_solicitud))
                ->with('status', 'Planificación guardada. Continúe con la valoración preanestésica.');
        }
        if ($code === '017' && $request->input('continue_to') === 'postoperative') {
            $redirectUrl .= '#field_diagnosticos_postoperatorios';
        }
        if ($code === '017' && $request->input('continue_to') === 'surgery') {
            return redirect()->back()->with('status', 'Formulario 017 guardado. Puede continuar completándolo desde Cirugía.');
        }

        return redirect()->to($redirectUrl)
            ->with('status', 'Borrador guardado en este episodio.');
    }

    public function sign(Request $request, $code, $id_solicitud)
    {
        abort_unless($code === '017', 404);
        $this->assertStorageAvailable();
        abort_unless($this->canAuthor($code), 403, 'La firma requiere un profesional clínico autorizado.');
        $validated = $request->validate(['entry_id' => 'required|integer|min:1']);

        try {
            $entry = DB::transaction(function () use ($id_solicitud, $validated) {
                $solicitud = Ho_Solicitud::where('id', $id_solicitud)->lockForUpdate()->firstOrFail();
                $encounter = app(HospitalDocumentService::class)->authorize($solicitud, 'hospital.documents.sign');
                abort_unless(HospitalDocumentService::writable($solicitud, $encounter, true), 403,
                    'El episodio ya no admite documentación.');
                $entry = HospitalEpisodeFormDraft::where('id', $validated['entry_id'])
                    ->where('encounter_id', $encounter->id)->where('solicitud_id', $solicitud->id)
                    ->where('form_code', '017')->lockForUpdate()->firstOrFail();

                $actorId = (string) Auth::id();
                $hasCertificate = app(UserElectronicCertificateService::class)
                    ->isActive($actorId, $encounter->id_empresa);
                $visualAssets = app(DoctorSignatureService::class)->visualAssetsForDoctor($actorId);
                abort_unless($hasCertificate || ! empty($visualAssets['signature_image']), 422,
                    'El profesional no tiene certificado electrónico vigente ni rúbrica digitalizada.');

                $records = app(ClinicalRecordService::class);
                $document = $records->registerDocument(
                    $encounter->id,
                    $solicitud->id_paciente,
                    'HOSPITAL_FORM_017',
                    'hospital_episode_form_draft',
                    $entry->id,
                    [
                        'title' => 'Formulario 017 - Protocolo quirúrgico',
                        'fields' => (array) $entry->payload,
                        'authored_by' => (string) ($entry->updated_by ?: $entry->created_by),
                        'authored_at' => $entry->updated_at,
                    ],
                    ['actor_id' => $actorId, 'ip' => request()->ip(), 'hospital_document' => true]
                );
                abort_unless($document, 503, 'No se pudo registrar el Formulario 017 en el expediente clínico.');
                $records->signDocument('hospital_episode_form_draft', $entry->id, $actorId,
                    ['actor_id' => $actorId, 'ip' => request()->ip(), 'hospital_document' => true]);

                return $entry;
            }, 3);
        } catch (ClinicalDocumentIncompleteException $exception) {
            return back()->withErrors(['documentos' => $exception->getMessage()]);
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['documentos' => $exception->getMessage()]);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['documentos' => $exception->getMessage()]);
        }

        return redirect()->route('hospital.documents.form.show', [
            'code' => '017', 'id_solicitud' => $id_solicitud, 'entry' => $entry->id,
        ])->with('status', 'Formulario 017 firmado. El PDF ya incluye la evidencia disponible del profesional.');
    }

    private function resolveDiagnosisSelections(array $codes)
    {
        $codes = array_values(array_unique(array_map(function ($code) {
            return mb_strtoupper(trim((string) $code), 'UTF-8');
        }, array_filter($codes, function ($code) {
            return trim((string) $code) !== '';
        }))));
        if (! $codes) {
            return [];
        }

        $descriptions = [];
        foreach (['cie_10_4', 'cie_10_3'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach (DB::table($table)->whereIn('id', $codes)->get(['id', 'descripcion']) as $item) {
                $code = mb_strtoupper(trim((string) $item->id), 'UTF-8');
                if (! isset($descriptions[$code])) {
                    $descriptions[$code] = trim((string) $item->descripcion);
                }
            }
        }
        $missing = array_values(array_diff($codes, array_keys($descriptions)));
        if ($missing) {
            throw ValidationException::withMessages([
                'diagnoses' => 'Uno o más diagnósticos CIE-10 no existen: '.implode(', ', $missing).'.',
            ]);
        }

        return array_map(function ($code) use ($descriptions) {
            $description = mb_strtoupper($descriptions[$code], 'UTF-8');

            return ['code' => $code, 'description' => $description, 'text' => $code.' - '.$description];
        }, $codes);
    }

    private function resolveProcedureSelections(array $codes)
    {
        $codes = array_values(array_unique(array_map(function ($code) {
            return trim((string) $code);
        }, array_filter($codes, function ($code) {
            return trim((string) $code) !== '';
        }))));
        if (! $codes) {
            return [];
        }

        $catalog = ApProcedimiento::whereIn('codigo', $codes)->where('estado', 1)
            ->get(['codigo', 'descripcion'])->keyBy(function ($item) {
                return (string) $item->codigo;
            });
        $missing = array_values(array_filter($codes, function ($code) use ($catalog) {
            return ! $catalog->has((string) $code);
        }));
        if ($missing) {
            throw ValidationException::withMessages([
                'surgery.procedure_codes' => 'Uno o más procedimientos no están vigentes en ap_procedimiento: '
                    .implode(', ', $missing).'.',
            ]);
        }

        return array_map(function ($code) use ($catalog) {
            $item = $catalog->get((string) $code);
            $description = mb_strtoupper(trim((string) $item->descripcion), 'UTF-8');

            return ['code' => (string) $code, 'description' => $description,
                'text' => (string) $code.' - '.$description];
        }, $codes);
    }

    private function syncForm017Selections(HospitalEpisodeFormDraft $entry, array $selections)
    {
        if (! Schema::hasTable('hospital_episode_form_draft_items')) {
            return;
        }
        DB::table('hospital_episode_form_draft_items')->where('draft_id', $entry->id)->delete();
        $groups = [
            ['key' => 'pre_diagnoses', 'type' => 'DIAGNOSIS', 'phase' => 'PREOPERATIVE'],
            ['key' => 'projected_procedures', 'type' => 'PROCEDURE', 'phase' => 'PREOPERATIVE'],
            ['key' => 'post_diagnoses', 'type' => 'DIAGNOSIS', 'phase' => 'POSTOPERATIVE'],
            ['key' => 'performed_procedures', 'type' => 'PROCEDURE', 'phase' => 'POSTOPERATIVE'],
        ];
        $now = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($groups as $group) {
            foreach (($selections[$group['key']] ?? []) as $position => $item) {
                $rows[] = [
                    'draft_id' => $entry->id,
                    'item_type' => $group['type'],
                    'phase' => $group['phase'],
                    'code' => (string) $item['code'],
                    'description' => mb_substr((string) $item['description'], 0, 500, 'UTF-8'),
                    'position' => $position + 1,
                    'created_by' => (string) Auth::id(),
                    'updated_by' => (string) Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        if ($rows) {
            DB::table('hospital_episode_form_draft_items')->insert($rows);
        }
    }

    private function syncForm017DiagramImages(Request $request, HospitalEpisodeFormDraft $entry)
    {
        $images = $this->form017DiagramMetadata($entry);
        $remove = array_map('intval', (array) $request->input('diagram_remove', []));
        foreach ($remove as $position) {
            if (! isset($images[$position])) {
                continue;
            }
            $pathToDelete = $images[$position]['path'];
            DB::afterCommit(function () use ($pathToDelete) {
                Storage::disk('local')->delete($pathToDelete);
            });
            unset($images[$position]);
        }
        $images = array_values($images);
        $uploads = array_values(array_filter((array) $request->file('diagram_images', [])));
        if (count($images) + count($uploads) > 3) {
            throw ValidationException::withMessages([
                'diagram_images' => 'El Formulario 017 admite un máximo de tres imágenes del procedimiento.',
            ]);
        }
        foreach ($uploads as $upload) {
            $mime = (string) $upload->getMimeType();
            $extension = $mime === 'image/png' ? 'png' : 'jpg';
            $name = bin2hex(random_bytes(16)).'.'.$extension;
            $directory = 'private/hospital/form017/'.$entry->id.'/diagramas';
            if (! Storage::disk('local')->putFileAs($directory, $upload, $name)) {
                throw new \RuntimeException('No fue posible guardar la imagen del diagrama quirúrgico.');
            }
            $images[] = [
                'path' => $directory.'/'.$name,
                'name' => mb_substr(basename((string) $upload->getClientOriginalName()), 0, 180, 'UTF-8'),
                'mime' => $mime,
            ];
        }
        $payload = (array) $entry->payload;
        $payload['_diagram_images'] = $images;
        $entry->payload = $payload;
        $entry->updated_by = (string) Auth::id();
        $entry->save();
    }

    private function form017DiagramMetadata(HospitalEpisodeFormDraft $entry)
    {
        $prefix = 'private/hospital/form017/'.$entry->id.'/diagramas/';
        $payload = (array) $entry->payload;
        $storedImages = isset($payload['_diagram_images']) ? (array) $payload['_diagram_images'] : [];

        return array_values(array_filter($storedImages, function ($image) use ($prefix) {
            return is_array($image) && isset($image['path'], $image['mime'])
                && strpos((string) $image['path'], $prefix) === 0
                && in_array($image['mime'], ['image/jpeg', 'image/png'], true);
        }));
    }

    private function form017DiagramDataUris(HospitalEpisodeFormDraft $entry)
    {
        $result = [];
        foreach ($this->form017DiagramMetadata($entry) as $image) {
            if (! Storage::disk('local')->exists($image['path'])) {
                continue;
            }
            $result[] = 'data:'.$image['mime'].';base64,'.base64_encode(Storage::disk('local')->get($image['path']));
        }

        return $result;
    }

    private function syncForm024Images(Request $request, HospitalEpisodeFormDraft $entry)
    {
        $images = $this->form024ImageMetadata($entry);
        $remove = array_map('intval', (array) $request->input('procedure_image_remove', []));
        foreach ($remove as $position) {
            if (! isset($images[$position])) {
                continue;
            }
            $pathToDelete = $images[$position]['path'];
            DB::afterCommit(function () use ($pathToDelete) {
                Storage::disk('local')->delete($pathToDelete);
            });
            unset($images[$position]);
        }
        $images = array_values($images);
        $uploads = array_values(array_filter((array) $request->file('procedure_images', [])));
        if (count($images) + count($uploads) > 3) {
            throw ValidationException::withMessages([
                'procedure_images' => 'El Formulario 024 admite un máximo de tres imágenes.',
            ]);
        }
        foreach ($uploads as $upload) {
            $mime = (string) $upload->getMimeType();
            $extension = $mime === 'image/png' ? 'png' : 'jpg';
            $name = bin2hex(random_bytes(16)).'.'.$extension;
            $directory = 'private/hospital/form024/'.$entry->id.'/procedimiento';
            if (! Storage::disk('local')->putFileAs($directory, $upload, $name)) {
                throw new \RuntimeException('No fue posible guardar la imagen del consentimiento informado.');
            }
            $images[] = [
                'path' => $directory.'/'.$name,
                'name' => mb_substr(basename((string) $upload->getClientOriginalName()), 0, 180, 'UTF-8'),
                'mime' => $mime,
            ];
        }
        $payload = (array) $entry->payload;
        $payload['_procedure_images'] = $images;
        $entry->payload = $payload;
        $entry->updated_by = (string) Auth::id();
        $entry->save();
    }

    private function form024ImageMetadata(HospitalEpisodeFormDraft $entry)
    {
        $prefix = 'private/hospital/form024/'.$entry->id.'/procedimiento/';
        $payload = (array) $entry->payload;
        $storedImages = isset($payload['_procedure_images']) ? (array) $payload['_procedure_images'] : [];

        return array_values(array_filter($storedImages, function ($image) use ($prefix) {
            return is_array($image) && isset($image['path'], $image['mime'])
                && strpos((string) $image['path'], $prefix) === 0
                && in_array($image['mime'], ['image/jpeg', 'image/png'], true);
        }));
    }

    private function form024ImageDataUris(HospitalEpisodeFormDraft $entry)
    {
        $result = [];
        foreach ($this->form024ImageMetadata($entry) as $image) {
            if (! Storage::disk('local')->exists($image['path'])) {
                continue;
            }
            $result[] = 'data:'.$image['mime'].';base64,'.base64_encode(Storage::disk('local')->get($image['path']));
        }

        return $result;
    }

    private function draftDocument(HospitalEpisodeFormDraft $entry)
    {
        if (! Schema::hasTable('clinical_documents')) {
            return null;
        }

        return ClinicalDocument::where('source_type', 'hospital_episode_form_draft')
            ->where('source_id', (string) $entry->id)->first();
    }

    private function canAuthor($code)
    {
        return HospitalEpisodeFormCatalog::canAuthor(Auth::user(), $code);
    }

    private function assertStorageAvailable()
    {
        abort_unless(Schema::hasTable('hospital_episode_form_drafts'), 503,
            'Falta aplicar la migración de borradores de formularios del episodio.');
    }
}
