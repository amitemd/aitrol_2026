<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\HospitalEpisodeFormDraft;
use Sis_medico\User;

/** Prepara la identificación visual del profesional para los formularios del episodio. */
class HospitalEpisodeProfessionalBlockService
{
    public function forDraft(?HospitalEpisodeFormDraft $entry = null, ?array $electronicProof = null)
    {
        if (! $entry) {
            return $this->emptyBlock();
        }

        $authorId = trim((string) ($entry->updated_by ?: $entry->created_by));

        return $this->forUser($authorId, $entry->updated_at, $electronicProof);
    }

    public function forUser($authorId, $recordedAt = null, ?array $electronicProof = null, $companyId = null)
    {
        $authorId = trim((string) $authorId);
        $author = $authorId !== '' ? User::withoutGlobalScopes()->find($authorId) : null;
        $images = app(DoctorSignatureService::class);
        $visualAssets = $images->visualAssetsForDoctor($authorId);
        $proofUrl = trim((string) ($electronicProof['validation_url'] ?? ''));
        $companyId = trim((string) ($companyId ?: session('id_empresa')));
        $hasElectronicCertificate = ! empty($electronicProof['certificate_verified'])
            || app(UserElectronicCertificateService::class)->isActive($authorId, $companyId);
        if (! $hasElectronicCertificate) {
            $proofUrl = '';
        }
        $fullName = $author ? trim(implode(' ', array_filter([
            $author->nombre1, $author->nombre2, $author->apellido1, $author->apellido2,
        ], function ($part) {
            $value = strtoupper(trim((string) $part));

            return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
        }))) : '';
        $specialty = '';
        if ($author) {
            try {
                $specialty = DB::table('user_espe as ue')
                    ->join('especialidad as e', 'e.id', '=', 'ue.espid')
                    ->where('ue.usuid', $author->id)->orderBy('ue.id')
                    ->pluck('e.nombre')->unique()->implode(', ');
            } catch (\Throwable $exception) {
                $specialty = '';
            }
        }
        $updatedAt = $recordedAt;
        if ($updatedAt instanceof \DateTimeInterface) {
            $updatedAt = $updatedAt->format('Y-m-d H:i:s');
        }
        $updatedAt = trim((string) $updatedAt);

        return [
            'available' => (bool) $author,
            'electronic' => $proofUrl !== '',
            'identification' => $author ? trim((string) $author->id) : $authorId,
            'name' => $fullName,
            'specialty' => $specialty !== '' ? $specialty : 'Especialidad no registrada',
            'professional_register' => $author ? trim((string) $author->registro_doctor) : '',
            'signature_status' => 'VALIDADO POR AITROL',
            'fingerprint' => trim((string) ($electronicProof['fingerprint'] ?? '')),
            'first_name' => $author ? trim((string) $author->nombre1) : '',
            'last_name' => $author ? trim((string) $author->apellido1) : '',
            'second_last_name' => $author ? trim((string) $author->apellido2) : '',
            'recorded_at' => $updatedAt,
            'date' => $updatedAt !== '' ? substr($updatedAt, 0, 10) : '',
            'time' => $updatedAt !== '' ? substr($updatedAt, 11, 5) : '',
            'qr_source' => $proofUrl !== ''
                ? app(ClinicalElectronicSignatureQr::class)->dataUri($proofUrl)
                : null,
            'signature_image' => $visualAssets['signature_image'],
            'seal_image' => $visualAssets['seal_image'],
        ];
    }

    private function emptyBlock()
    {
        return [
            'available' => false, 'electronic' => false, 'identification' => '', 'name' => '',
            'specialty' => '', 'professional_register' => '', 'signature_status' => '', 'fingerprint' => '',
            'first_name' => '', 'last_name' => '', 'second_last_name' => '',
            'recorded_at' => '', 'date' => '', 'time' => '',
            'qr_source' => null, 'signature_image' => null, 'seal_image' => null,
        ];
    }
}
