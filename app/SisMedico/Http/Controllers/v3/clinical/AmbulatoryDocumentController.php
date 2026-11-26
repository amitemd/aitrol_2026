<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3AmbulatoryDocumentService;

class AmbulatoryDocumentController extends Controller
{
    private $documents;

    public function __construct(V3AmbulatoryDocumentService $documents)
    {
        $this->middleware('auth');
        $this->documents = $documents;
    }

    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        return $this->page('v3.clinical.ambulatory_documents.index', [
            'term' => $term,
            'patients' => $this->documents->patients($term, $this->companyId($request)),
        ], 'Documentos ambulatorios');
    }

    public function patient(Request $request, $patient)
    {
        $companyId = $this->companyId($request);

        return $this->page('v3.clinical.ambulatory_documents.patient', [
            'patient' => $this->documents->patient($patient, $companyId),
            'consultations' => $this->documents->consultations($patient, $companyId),
        ], 'Consultas del paciente');
    }

    public function consultation(Request $request, $appointment)
    {
        $consultation = $this->documents->consultation($appointment, $this->companyId($request));

        return $this->page('v3.clinical.ambulatory_documents.consultation', [
            'consultation' => $consultation,
            'documents' => $this->documents->documents($consultation),
        ], 'Documentos de la consulta');
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') {
            abort(403, 'Seleccione una empresa activa.');
        }

        return $companyId;
    }

    private function page(string $view, array $payload, string $title): View
    {
        return view('new-system.ambulatory-documents', [
            'title' => $title,
            'documentView' => $view,
            'documentData' => $payload,
        ]);
    }
}
