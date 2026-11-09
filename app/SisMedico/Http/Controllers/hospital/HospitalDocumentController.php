<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\HospitalDocumentService;
use Sis_medico\Exceptions\ClinicalDocumentIncompleteException;
use Sis_medico\Exceptions\ClinicalProcedureBlockedException;

class HospitalDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id_solicitud)
    {
        return view('hospital.documentos.index', app(HospitalDocumentService::class)->index($id_solicitud));
    }

    public function document($first, $second)
    {
        list($id_solicitud, $document) = $this->normalizeEpisodeRoute($first, $second);
        return view('hospital.documentos.documento', app(HospitalDocumentService::class)->readDocument($id_solicitud, $document));
    }

    public function preview($first, $second)
    {
        list($id_solicitud, $document) = $this->normalizeEpisodeRoute($first, $second);
        return view('hospital.documentos.preview', app(HospitalDocumentService::class)->readDocument($id_solicitud, $document));
    }

    public function link(Request $request, $first, $second)
    {
        list($id_solicitud, $key) = $this->normalizeLinkRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($request, $id_solicitud, $key) {
            return redirect()->to(app(HospitalDocumentService::class)->externalLink($id_solicitud, $key, $request->input('procedure_id')));
        });
    }

    public function bind(Request $request, $id_solicitud)
    {
        $data = $request->validate(['procedure_id' => 'required|integer|min:1']);
        return $this->execute($id_solicitud, function () use ($id_solicitud, $data) {
            $service = app(HospitalDocumentService::class);
            $service->bindProcedure($service->obtain($id_solicitud), $data['procedure_id']);
        });
    }

    public function createProcedure(Request $request, $id_solicitud)
    {
        $request->merge(['id_solicitud' => $id_solicitud]);
        return $this->execute($id_solicitud, function () use ($request) {
            app(\Sis_medico\Services\ClinicalWardProcedureService::class)->crear_procedimiento($request);
        });
    }

    public function prepare($first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->prepareAnesthesia($id_solicitud, $procedure);
        });
    }

    public function sign(Request $request, $first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        $data = $request->validate(['kind' => 'required|in:preop,protocol', 'amendment_reason' => 'nullable|string|min:5|max:2000']);
        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure, $data) {
            app(HospitalDocumentService::class)->signSource($id_solicitud, $procedure, $data['kind'],
                isset($data['amendment_reason']) ? $data['amendment_reason'] : '');
        });
    }

    public function start(Request $request, $first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($request, $id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->start($id_solicitud, $procedure, $request->only([
                'procedure_indication', 'procedure_identity_verified', 'procedure_checklist_complete',
                'procedure_emergency_exception', 'procedure_emergency_reason', 'consent_template_id',
                'consent_version', 'consent_signed_at', 'consent_signed_by', 'consent_signer_role',
                'consent_representative_relationship', 'consent_expires_at', 'consent_evidence_reference',
            ]));
        });
    }

    public function finish($first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->finish($id_solicitud, $procedure);
        });
    }

    public function recovery($first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            $data = app(HospitalDocumentService::class)->recovery($id_solicitud, $procedure);
            $data['labels'] = HospitalDocumentService::recoveryFields();
            $data['options'] = HospitalDocumentService::recoveryOptions();
            $encounter = \Sis_medico\ClinicalEncounter::find($data['solicitud']->encounter_id);
            $data['readOnly'] = !HospitalDocumentService::writable($data['solicitud'], $encounter, true)
                || !auth()->user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);
            return view('hospital.documentos.recuperacion', $data);
        });
    }

    public function saveRecovery(Request $request, $first, $second)
    {
        list($id_solicitud, $procedure) = $this->normalizeEpisodeRoute($first, $second);
        return $this->execute($id_solicitud, function () use ($request, $id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->saveRecovery($id_solicitud, $procedure, $request->all());
        });
    }

    /**
     * Accept both the canonical ID-last routes and the legacy ID-first URLs.
     */
    private function normalizeEpisodeRoute($first, $second)
    {
        $route = request()->route();
        $routeId = $route ? $route->parameter('id_solicitud') : null;
        $routeValue = $route ? ($route->parameter('procedure') ?: $route->parameter('document')) : null;

        if ($routeId !== null && $routeValue !== null) {
            return [$routeId, $routeValue];
        }

        return [$first, $second];
    }

    private function normalizeLinkRoute($first, $second)
    {
        $route = request()->route();
        $routeId = $route ? $route->parameter('id_solicitud') : null;
        $routeKey = $route ? $route->parameter('key') : null;

        if ($routeId !== null && $routeKey !== null) {
            return [$routeId, $routeKey];
        }

        return [$first, $second];
    }

    private function execute($id, $callback)
    {
        try {
            $response = $callback();
            return $response ?: redirect()->route('hospital.documents.index', ['id_solicitud' => $id])->with('status', 'Registro actualizado.');
        } catch (\InvalidArgumentException $exception) {
            return $this->blocked($id, $exception);
        } catch (ClinicalDocumentIncompleteException $exception) {
            return $this->blocked($id, $exception);
        } catch (ClinicalProcedureBlockedException $exception) {
            return $this->blocked($id, $exception);
        }
    }

    private function blocked($id, $exception)
    {
        if (request()->expectsJson()) return response()->json(['message' => $exception->getMessage()], 422);
        return redirect()->route('hospital.documents.index', ['id_solicitud' => $id])->withErrors(['documentos' => $exception->getMessage()]);
    }
}
