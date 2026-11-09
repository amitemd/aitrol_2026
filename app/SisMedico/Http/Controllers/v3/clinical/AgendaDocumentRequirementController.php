<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\AgendaDocumentoRequisito;
use Sis_medico\Documento;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro;

class AgendaDocumentRequirementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorizeMaintenance();

        $insuranceId = (int) $request->input('seguro');
        $scope = $this->scope($request->input('proc_consul', 1));
        $insurances = Seguro::orderBy('nombre')->get();
        $insurance = $insurances->first(function ($item) use ($insuranceId) {
            return (int) $item->id === $insuranceId;
        });
        if (!$insurance) {
            $insurance = $insurances->first();
            $insuranceId = $insurance ? (int) $insurance->id : 0;
        }

        $companyId = trim((string) $request->session()->get('id_empresa'));
        $returnDoctor = trim((string) $request->input('doctor'));
        $returnAppointment = (int) $request->input('appointment');
        $navigationQuery = array();
        $backUrl = route('v3.clinical-agenda.index');
        if ($returnDoctor !== '' && ctype_digit($returnDoctor) && $returnAppointment > 0) {
            $navigationQuery = array('doctor' => $returnDoctor, 'appointment' => $returnAppointment);
            $backUrl = route('v3.agenda-documents.index', $navigationQuery);
        }
        $requirements = collect();
        if ($insuranceId > 0) {
            $requirements = AgendaDocumentoRequisito::where('id_seguro', $insuranceId)
                ->where('proc_consul', $scope)
                ->where(function ($query) use ($companyId) {
                    $query->whereNull('id_empresa');
                    if ($companyId !== '') $query->orWhere('id_empresa', $companyId);
                })
                ->orderByRaw('id_empresa is null desc')
                ->orderBy('secuencia')
                ->get()
                ->keyBy('id_documento');
        }

        $documents = Documento::where('estado', 1)
            ->where('est_doc_tarea', 0)
            ->where(function ($query) use ($scope) {
                if ($scope === 2) $query->where('proc_consul', 2);
                else $query->whereIn('proc_consul', array($scope, 2));
            })
            ->orderBy('secuencia')
            ->orderBy('nombre')
            ->get();

        $payload = array(
            'insurances' => $insurances,
            'insurance' => $insurance,
            'insuranceId' => $insuranceId,
            'scope' => $scope,
            'documents' => $documents,
            'requirements' => $requirements,
            'navigationQuery' => $navigationQuery,
            'backUrl' => $backUrl,
        );
        if ($request->ajax() || $request->input('partial') === '1') {
            return view('v3.clinical.agenda_documents.requirements_content', $payload);
        }
        return $this->page($payload);
    }

    public function update(Request $request)
    {
        $this->authorizeMaintenance();
        $this->validate($request, array(
            'seguro' => array('required', 'integer', app(\Sis_medico\Services\CompanyCatalogService::class)->existsRule('seguros')),
            'proc_consul' => 'required|in:0,1,2',
            'documentos' => 'array',
            'documentos.*.id' => 'nullable|integer',
            'documentos.*.obligatorio' => 'nullable|boolean',
        ));

        $insuranceId = (int) $request->input('seguro');
        $insurance = Seguro::findOrFail($insuranceId);
        $scope = $this->scope($request->input('proc_consul'));
        $companyId = trim((string) $request->session()->get('id_empresa'));
        $selected = array_filter((array) $request->input('documentos', array()), function ($item) {
            return !empty($item['id']);
        });
        $documentIds = array_values(array_unique(array_map(function ($item) {
            return (int) $item['id'];
        }, $selected)));
        $validIds = Documento::where('estado', 1)->where('est_doc_tarea', 0)
            ->whereIn('id', $documentIds)->pluck('id')->map(function ($id) {
                return (int) $id;
            })->toArray();
        if (count($validIds) !== count($documentIds)) {
            return back()->withInput()->withErrors(array('documentos' => 'Uno de los documentos seleccionados ya no está disponible.'));
        }

        DB::transaction(function () use ($insuranceId, $scope, $companyId, $selected, $validIds, $request) {
            AgendaDocumentoRequisito::where('id_seguro', $insuranceId)
                ->where('proc_consul', $scope)
                ->where(function ($query) use ($companyId) {
                    $query->whereNull('id_empresa');
                    if ($companyId !== '') $query->orWhere('id_empresa', $companyId);
                })->delete();

            $companyScope = $companyId === '' ? null : $companyId;
            foreach ($selected as $position => $item) {
                $documentId = (int) $item['id'];
                if (!in_array($documentId, $validIds, true)) continue;
                AgendaDocumentoRequisito::create(array(
                    'id_seguro' => $insuranceId,
                    'id_documento' => $documentId,
                    'id_empresa' => $companyScope,
                    'proc_consul' => $scope,
                    'obligatorio' => !empty($item['obligatorio']) ? 1 : 0,
                    'secuencia' => $position + 1,
                    'estado' => 1,
                    'id_usuariocrea' => Auth::id(),
                    'id_usuariomod' => Auth::id(),
                    'ip_creacion' => $request->ip(),
                    'ip_modificacion' => $request->ip(),
                ));
            }
        });

        $message = count($validIds) . ' documento(s) requerido(s) actualizado(s) correctamente.';
        if ($request->expectsJson()) {
            return response()->json(array('ok' => true, 'message' => $message, 'saved_count' => count($validIds)));
        }
        $redirectParameters = array(
            'seguro' => $insurance->id,
            'proc_consul' => $scope,
        );
        $returnDoctor = trim((string) $request->input('doctor'));
        $returnAppointment = (int) $request->input('appointment');
        if ($returnDoctor !== '' && ctype_digit($returnDoctor) && $returnAppointment > 0) {
            $redirectParameters['doctor'] = $returnDoctor;
            $redirectParameters['appointment'] = $returnAppointment;
        }
        return redirect()->route('v3.agenda-document-requirements.index', $redirectParameters)->with('success', $message);
    }

    private function scope($value)
    {
        $value = (int) $value;
        return in_array($value, array(0, 1, 2), true) ? $value : 1;
    }

    private function authorizeMaintenance()
    {
        $user = Auth::user();
        if (!$user || (!method_exists($user, 'isSystemUser') || !$user->isSystemUser())
            && !in_array((int) $user->id_tipo_usuario, array(1, 4), true)) {
            abort(403);
        }
    }

    private function page(array $payload)
    {
        $title = 'Documentos requeridos por seguro';
        $data = array(
            'controlador' => 'documentos-requeridos', 'carpeta' => 'clinical',
            'submodulo' => 'documentos-requeridos', 'submodulo_' => 'documentos-requeridos', 'menu_id' => 741,
            'titulo' => $title, 'title' => $title . ' | Aitrol Hospitalario',
            'vista' => 'v3.clinical.agenda_documents.requirements',
            'obseraudi' => 'Ingreso a ' . strtolower($title),
            'contenido' => view('v3.clinical.agenda_documents.requirements', $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
