<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Rc_pre_anestesica;
use Sis_medico\User;

/** Construye el bloque visible y verificable de los formularios 018, 018A y 019. */
class AnesthesiaPdfSignatureService
{
    public function form018(Rc_pre_anestesica $record)
    {
        $anesthesia = Hc_Anestesiologia::find($record->id_hc_anestesiologia);
        $authorId = $record->id_usuariocrea ?: optional($anesthesia)->id_anestesiologo;

        return $this->build($authorId, app(ClinicalRecordSignatureService::class)->issueForm018($record), $record->updated_at);
    }

    public function form018A(Hc_Anestesiologia $record)
    {
        $authorId = $record->id_anestesiologo ?: $record->id_usuariocrea;

        return $this->build($authorId, app(ClinicalRecordSignatureService::class)->issueForm018A($record), $record->updated_at);
    }

    public function form019(Hc_Evolucion_Anestesiologo $record)
    {
        $anesthesia = $record->id_hc_anestesiologia
            ? Hc_Anestesiologia::find($record->id_hc_anestesiologia)
            : Hc_Anestesiologia::where('id_hc_procedimientos', $record->hc_id_procedimiento)->first();
        $authorId = $record->id_usuariocrea ?: optional($anesthesia)->id_anestesiologo;

        return $this->build($authorId, app(ClinicalRecordSignatureService::class)->issueForm019($record), $record->updated_at);
    }

    private function build($authorId, array $proof, $signedAt)
    {
        $authorId = trim((string) $authorId);
        $author = $authorId !== '' ? User::withoutGlobalScopes()->find($authorId) : null;
        $hasElectronicCertificate = app(UserElectronicCertificateService::class)
            ->isActive($authorId, session('id_empresa'));
        $images = app(DoctorSignatureService::class);
        $visualAssets = $images->visualAssetsForDoctor($authorId);
        $specialty = $author ? DB::table('user_espe as ue')->join('especialidad as e', 'e.id', '=', 'ue.espid')
            ->where('ue.usuid', $author->id)->orderBy('ue.id')->pluck('e.nombre')->unique()->implode(', ') : '';
        $name = $author ? trim(implode(' ', array_filter([
            $author->nombre1, $author->nombre2, $author->apellido1, $author->apellido2,
        ], function ($part) {
            $value = strtoupper(trim((string) $part));

            return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
        }))) : '';
        if ($signedAt instanceof \DateTimeInterface) {
            $signedAt = $signedAt->format('Y-m-d H:i:s');
        }

        return [
            'electronic' => $author && $hasElectronicCertificate && ! empty($proof['validation_url']),
            'professional' => $author,
            'identification' => $author ? trim((string) $author->id) : $authorId,
            'name' => $name !== '' ? $name : 'PROFESIONAL NO IDENTIFICADO',
            'specialty' => $specialty !== '' ? $specialty : 'Especialidad no registrada',
            'register' => $author ? trim((string) $author->registro_doctor) : '',
            'signed_at' => trim((string) $signedAt),
            'status' => 'VALIDADO POR AITROL',
            'fingerprint' => $proof['fingerprint'] ?? null,
            'validation_url' => $hasElectronicCertificate ? ($proof['validation_url'] ?? null) : null,
            'qr_source' => $hasElectronicCertificate && ! empty($proof['validation_url'])
                ? app(ClinicalElectronicSignatureQr::class)->dataUri($proof['validation_url']) : null,
            'signature_image' => $visualAssets['signature_image'],
            'seal_image' => $visualAssets['seal_image'],
        ];
    }
}
