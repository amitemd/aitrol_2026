<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Exceptions\ClinicalMedicationBlockedException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalMedicationAdministrationService;

class ClinicalMedicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $administration = app(ClinicalMedicationAdministrationService::class)
                ->registerLegacyRecipeDetail($request->id, $request->all(), Auth::user()->id, $request->ip());

            return array(
                'estado' => 'O',
                'msn' => $administration->status === ClinicalMedicationAdministrationService::OMITTED
                    ? 'Omisión registrada con trazabilidad.'
                    : 'Administración registrada en el eMAR.',
                'administration_id' => $administration->id,
            );
        } catch (ClinicalMedicationBlockedException $exception) {
            return response()->json(array(
                'estado' => 'E',
                'msn' => implode("\n", $exception->errors()),
                'errors' => $exception->errors(),
            ), 422);
        }
    }

    public function emergencyException(Request $request)
    {
        try {
            $result = app(ClinicalMedicationAdministrationService::class)
                ->registerEmergencyException($request->solicitud_id, $request->all(), Auth::user()->id, $request->ip());

            return array(
                'estado' => 'O',
                'msn' => 'Administración urgente registrada. La orden debe regularizarse antes del plazo indicado.',
                'exception_id' => $result['exception']->id,
                'administration_id' => $result['administration']->id,
                'regularization_due_at' => $result['exception']->regularization_due_at->format('Y-m-d H:i:s'),
            );
        } catch (ClinicalMedicationBlockedException $exception) {
            return $this->blocked($exception);
        }
    }

    public function regularizeEmergency(Request $request, $exceptionId)
    {
        try {
            $exception = app(ClinicalMedicationAdministrationService::class)
                ->regularizeEmergencyException($exceptionId, $request->recipe_detail_id, Auth::user()->id);

            return array('estado' => 'O', 'msn' => 'Excepción regularizada con la orden médica.', 'exception_id' => $exception->id);
        } catch (ClinicalMedicationBlockedException $exception) {
            return $this->blocked($exception);
        }
    }

    private function blocked(ClinicalMedicationBlockedException $exception)
    {
        return response()->json(array(
            'estado' => 'E',
            'msn' => implode("\n", $exception->errors()),
            'errors' => $exception->errors(),
        ), 422);
    }
}
