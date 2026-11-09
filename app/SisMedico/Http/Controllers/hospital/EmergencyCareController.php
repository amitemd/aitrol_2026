<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Sis_medico\ClinicalEmergencyTriageAssessment;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalEmergencyCareService;

class EmergencyCareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, $solicitud, ClinicalEmergencyCareService $care)
    {
        $source = Ho_Solicitud::with('paciente')->findOrFail($solicitud);
        $access = $care->access($source, $request);
        return view('hospital.emergencia.care', [
            'source' => $source,
            'flow' => $access['flow'],
            'canEdit' => $access['can_edit'],
            'sections' => ClinicalEmergencyCareService::sections(),
            'latest' => $care->latest($source->id),
            'triage' => ClinicalEmergencyTriageAssessment::where('solicitud_id', $source->id)
                ->orderByDesc('id')->first(),
        ]);
    }

    public function save(Request $request, $solicitud, ClinicalEmergencyCareService $care)
    {
        $data = $request->validate([
            'section' => 'required|string|max:40',
            'content' => 'nullable|string|max:4000',
            'base_entry_id' => 'required|integer|min:0',
        ]);
        try {
            $entry = $care->save(Ho_Solicitud::findOrFail($solicitud), $request, $data);
            return response()->json([
                'entry_id' => $entry ? (int) $entry->id : 0,
                'saved_at' => $entry ? $entry->created_at->format('d/m/Y H:i') : null,
                'recorded_by' => $entry ? $entry->recorded_by : null,
            ]);
        } catch (\DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }
}
