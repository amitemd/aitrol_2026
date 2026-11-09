<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HospitalEpisodeFormDraft;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\HospitalDocumentService;
use Sis_medico\Services\HospitalEpisodeBodyDiagram;
use Sis_medico\Services\HospitalEpisodeEmergencySource;
use Sis_medico\Services\HospitalEpisodeFormCatalog;
use Sis_medico\Services\HospitalEpisodeForm020;
use Sis_medico\Services\HospitalEpisodeForm022;
use Sis_medico\Services\HospitalEpisodeForm024;
use Sis_medico\Services\HospitalEpisodeMedicationPrescriptionService;
use Sis_medico\Services\HospitalEpisodePhysicalExam;
use Illuminate\Validation\ValidationException;

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
            } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception) {
                if ($exception->getStatusCode() !== 403) throw $exception;
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

        return view('hospital.documentos.formulario-borrador', [
            'form' => $form, 'fields' => HospitalEpisodeFormCatalog::fields($code),
            'solicitud' => $solicitud, 'entries' => $entries, 'entry' => $entry,
            'canWrite' => $canWrite, 'officialConsultations' => $officialConsultations,
            'emergencySource' => $emergencySource,
            'prescribedMedications022' => $prescribedMedications022,
            'form022Payload' => $form022Payload,
            'medicationAdministrationUrl' => $medicationAdministrationUrl,
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
            abort_if($request->query('entry') !== null && !$entry, 404);
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
        if ($code === '022' && !$templateOnly) {
            $prescriptions022 = app(HospitalEpisodeMedicationPrescriptionService::class)->forEpisode($solicitud);
            $form022Payload = HospitalEpisodeForm022::reconcilePrescriptions(
                $entry ? $entry->payload : [],
                $prescriptions022
            );
        }
        $empresa = null;
        if (Schema::hasTable('empresa') && $request->session()->get('id_empresa')) {
            $empresa = DB::table('empresa')->where('id', $request->session()->get('id_empresa'))->first();
        }
        $pdf->loadView('hospital.documentos.formulario-borrador-pdf', [
            'form' => $form, 'fields' => HospitalEpisodeFormCatalog::fields($code),
            'solicitud' => $solicitud, 'entry' => $entry, 'emergencySource' => $emergencySource,
            'empresa' => $empresa, 'form022Payload' => $form022Payload,
        ])->setPaper('a4', in_array($code, ['018A', '019'], true) ? 'landscape' : 'portrait');

        return $pdf->stream('formulario-'.$code.'-episodio-'.$solicitud->id.'.pdf');
    }

    public function store(Request $request, $code, $id_solicitud)
    {
        abort_unless(HospitalEpisodeFormCatalog::find($code), 404);
        $this->assertStorageAvailable();
        abort_unless($this->canAuthor($code), 403, 'La edición requiere un profesional clínico o un usuario de Sistemas autorizado.');

        $fields = HospitalEpisodeFormCatalog::fields($code);
        $rules = ['entry_id' => 'nullable|integer|min:1', 'data' => 'required|array'];
        foreach ($fields as $key => $label) {
            $maximum = $key === 'body_marks' ? 20000 : (in_array($code, ['020', '022'], true) ? 60000 : 10000);
            $rules['data.'.$key] = 'nullable|string|max:'.$maximum;
        }
        if ($code === '003') $rules['data.examen_fisico_marcas'] = 'nullable|array|max:25';
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
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['data.tipo_consentimiento' => $exception->getMessage()]);
            }
        }
        if (($code === '020' && !HospitalEpisodeForm020::hasData($payload))
            || ($code === '022' && !HospitalEpisodeForm022::hasData($payload))
            || ($code === '024' && !HospitalEpisodeForm024::hasData($payload))
            || (!in_array($code, ['020', '022', '024'], true) && !count(array_filter($payload, function ($value) { return $value !== ''; })))) {
            return back()->withErrors(['data' => 'Escriba al menos un dato antes de guardar.'])->withInput();
        }

        $entry = DB::transaction(function () use ($id_solicitud, $code, $validated, $payload) {
            $solicitud = Ho_Solicitud::where('id', $id_solicitud)->lockForUpdate()->firstOrFail();
            $encounter = app(HospitalDocumentService::class)->authorize($solicitud, 'hospital.documents.write');
            abort_unless(HospitalDocumentService::writable($solicitud, $encounter, true), 403,
                'El episodio ya no admite documentación.');

            if (!empty($validated['entry_id'])) {
                $entry = HospitalEpisodeFormDraft::where('id', $validated['entry_id'])
                    ->where('encounter_id', $encounter->id)
                    ->where('solicitud_id', $solicitud->id)->where('form_code', $code)
                    ->lockForUpdate()->first();
                abort_unless($entry, 404);
                $entry->payload = $payload;
                $entry->updated_by = (string) Auth::id();
                $entry->save();
                return $entry;
            }

            return HospitalEpisodeFormDraft::create([
                'encounter_id' => $encounter->id,
                'solicitud_id' => $solicitud->id,
                'form_code' => $code,
                'payload' => $payload,
                'created_by' => (string) Auth::id(),
                'updated_by' => (string) Auth::id(),
            ]);
        }, 3);

        return redirect()->route('hospital.documents.form.show', [
            'code' => $code, 'id_solicitud' => $id_solicitud, 'entry' => $entry->id,
        ] + ($request->boolean('embedded') ? ['embedded' => 1] : []))
            ->with('status', 'Borrador guardado en este episodio.');
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
