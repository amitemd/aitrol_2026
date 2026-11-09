<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalRecordSignatureService;
use Sis_medico\User;

class ClinicalSignatureController extends Controller
{
    public function verify(Request $request, ClinicalRecordSignatureService $signatures)
    {
        $verification = $signatures->verify($request->query('p'), $request->query('s'));
        $doctor = $verification['author_id']
            ? User::withoutGlobalScopes()->find($verification['author_id'])
            : null;
        $name = $doctor ? trim(implode(' ', array_filter([
            $doctor->nombre1, $doctor->nombre2, $doctor->apellido1, $doctor->apellido2,
        ], function ($part) {
            $value = strtoupper(trim((string) $part));
            return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
        }))) : 'Profesional no identificado';
        $specialties = $doctor
            ? DB::table('user_espe as ue')->join('especialidad as e', 'e.id', '=', 'ue.espid')
                ->where('ue.usuid', $doctor->id)->orderBy('ue.id')->pluck('e.nombre')->unique()->implode(', ')
            : '';
        $recordLabels = array(
            ClinicalRecordSignatureService::FORM_006 => 'Formulario 006 - Epicrisis',
            ClinicalRecordSignatureService::FORM_008 => 'Formulario 008 - Emergencia',
            ClinicalRecordSignatureService::EVOLUTION => 'Formulario 005 - Evolución y prescripciones',
            ClinicalRecordSignatureService::EMERGENCY => 'Formulario 005 - Evolución de emergencia',
        );

        return response()->view('hospital.signatures.verify', [
            'verification' => $verification,
            'doctorName' => $name,
            'doctorSpecialties' => $specialties ?: 'Especialidad no registrada',
            'recordLabel' => $recordLabels[$verification['record_type']] ?? 'Registro clínico',
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
            'Referrer-Policy' => 'no-referrer',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; base-uri 'none'; form-action 'none'",
        ]);
    }
}
