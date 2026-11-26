<?php

namespace Sis_medico\Services;

class ClinicalDocumentGuard
{
    public static function assertMutation($document)
    {
        if (! $document->exists || empty($document->getOriginal('signed_at'))) {
            return;
        }
        foreach (['clinical_record_id', 'encounter_id', 'document_type', 'source_type', 'source_id', 'status',
            'content_hash', 'content_snapshot', 'signed_at', 'signed_by', 'electronic_signature',
            'electronic_certificate_pem', 'electronic_certificate_fingerprint', 'electronic_certificate_id',
            'electronic_signature_algorithm'] as $field) {
            if ($document->isDirty($field)) {
                throw new \RuntimeException('Un documento firmado es inmutable; registre una adenda.');
            }
        }
    }
}
