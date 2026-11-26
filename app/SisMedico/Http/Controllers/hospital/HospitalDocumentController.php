<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Exceptions\ClinicalDocumentIncompleteException;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
use Sis_medico\Exceptions\ClinicalProcedureBlockedException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalWardProcedureService;
use Sis_medico\Services\HospitalDocumentService;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HospitalDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, $id_solicitud)
    {
        $context = $request->query('context') === 'ambulatory' ? 'ambulatory' : null;
        $service = app(HospitalDocumentService::class);
        $destination = $service->scheduledDocumentsDestination($id_solicitud);
        if ($destination && (string) $destination['solicitud']->id !== (string) $id_solicitud) {
            $parameters = ['id_solicitud' => $destination['solicitud']->id];
            if ($context) {
                $parameters['context'] = $context;
            }

            return redirect()->route('hospital.documents.index', $parameters);
        }
        if ($destination && $destination['repairable']) {
            return response()->view('hospital.documentos.vincular-ingreso', ['idSolicitud' => $id_solicitud], 409);
        }

        return view('hospital.documentos.index', $service->index($id_solicitud, $context));
    }

    public function linkScheduledAdmission(Request $request, $id_solicitud)
    {
        $data = $request->validate(['agenda_id' => 'nullable|integer|min:1']);
        try {
            $solicitud = app(HospitalDocumentService::class)->linkScheduledDocuments($id_solicitud, $data['agenda_id'] ?? null);

            return redirect()->route('hospital.documents.index', ['id_solicitud' => $solicitud->id]);
        } catch (\InvalidArgumentException|ClinicalEncounterConflictException $exception) {
            $message = $exception->getMessage();
        } catch (ModelNotFoundException $exception) {
            if ($request->expectsJson()) {
                throw $exception;
            }
            $message = 'No se encontró una solicitud hospitalaria disponible para esta cirugía.';
        } catch (HttpExceptionInterface $exception) {
            if (! in_array($exception->getStatusCode(), [403, 404], true) || $request->expectsJson()) {
                throw $exception;
            }
            $message = $exception->getStatusCode() === 403 && $exception->getMessage() !== ''
                ? $exception->getMessage() : 'No se encontró una solicitud hospitalaria disponible para esta cirugía.';
        }

        if ($request->boolean('desde_cirugia')) {
            return redirect()->route('hospital.index_modulos', ['id_paso' => 5])->withErrors(['documentos_cirugia' => $message]);
        }

        return $this->blocked($id_solicitud, new \InvalidArgumentException($message));
    }

    public function document($first, $second)
    {
        [$id_solicitud, $document] = $this->normalizeEpisodeRoute($first, $second);

        return view('hospital.documentos.documento', app(HospitalDocumentService::class)->readDocument($id_solicitud, $document));
    }

    public function preview($first, $second)
    {
        [$id_solicitud, $document] = $this->normalizeEpisodeRoute($first, $second);

        return view('hospital.documentos.preview', app(HospitalDocumentService::class)->readDocument($id_solicitud, $document));
    }

    public function link(Request $request, $first, $second)
    {
        [$id_solicitud, $key] = $this->normalizeLinkRoute($first, $second);

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
            app(ClinicalWardProcedureService::class)->crear_procedimiento($request);
            if ($request->input('modal')) {
                return redirect()->route('hospital.documents.form.show', [
                    'code' => '017',
                    'id_solicitud' => $request->input('id_solicitud'),
                ])->with('status', 'Planificación guardada. Complete los datos restantes del Formulario 017.');
            }
        });
    }

    public function prepare($first, $second)
    {
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);

        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->prepareAnesthesia($id_solicitud, $procedure);
        });
    }

    public function preoperative($id_solicitud)
    {
        return $this->execute($id_solicitud, function () use ($id_solicitud) {
            $data = request()->validate(['agenda_id' => 'nullable|integer|min:1']);
            $url = app(HospitalDocumentService::class)->currentPreoperativeUrl($id_solicitud, $data['agenda_id'] ?? null);

            return redirect()->to($url);
        });
    }

    public function surgeryCatalog(Request $request, $type, $id_solicitud)
    {
        abort_unless(in_array($type, ['procedures', 'doctors', 'cie10'], true), 404);
        $data = $request->validate(['q' => 'nullable|string|max:100']);

        return response()->json(app(HospitalDocumentService::class)->searchSurgeryCatalog(
            $id_solicitud,
            $type,
            isset($data['q']) ? $data['q'] : ''
        ));
    }

    public function sign(Request $request, $first, $second)
    {
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);
        $data = $request->validate(['kind' => 'required|in:preop,protocol', 'amendment_reason' => 'nullable|string|min:5|max:2000']);

        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure, $data) {
            app(HospitalDocumentService::class)->signSource($id_solicitud, $procedure, $data['kind'],
                isset($data['amendment_reason']) ? $data['amendment_reason'] : '');
        });
    }

    public function start(Request $request, $first, $second)
    {
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);

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
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);

        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            app(HospitalDocumentService::class)->finish($id_solicitud, $procedure);
        });
    }

    public function recovery($first, $second)
    {
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);

        return $this->execute($id_solicitud, function () use ($id_solicitud, $procedure) {
            $data = app(HospitalDocumentService::class)->recovery($id_solicitud, $procedure);
            $data['labels'] = HospitalDocumentService::recoveryFields();
            $data['options'] = HospitalDocumentService::recoveryOptions();
            $encounter = ClinicalEncounter::find($data['solicitud']->encounter_id);
            $data['readOnly'] = ! HospitalDocumentService::writable($data['solicitud'], $encounter, true)
                || ! auth()->user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);

            return view('hospital.documentos.recuperacion', $data);
        });
    }

    public function saveRecovery(Request $request, $first, $second)
    {
        [$id_solicitud, $procedure] = $this->normalizeEpisodeRoute($first, $second);

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
        } catch (ClinicalEncounterConflictException $exception) {
            return $this->blocked($id, $exception);
        }
    }

    private function blocked($id, $exception)
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return redirect()->route('hospital.documents.index', ['id_solicitud' => $id])->withErrors(['documentos' => $exception->getMessage()]);
    }
}
