<?php

namespace Sis_medico\Services;

use Sis_medico\ClinicalDocument;

class ClinicalSignedDocumentGuard
{
    public static function assertSourceMutation($sourceType, $sourceId, $model = null)
    {
        if (!\Schema::hasTable('clinical_documents')) return;
        $document = ClinicalDocument::where('source_type', $sourceType)->where('source_id', (string) $sourceId)
            ->whereNotNull('signed_at')->first();
        if (!$document) return;
        if ($model) {
            $dirty = array_keys($model->getDirty());
            $clinical = array_diff($dirty, array('updated_at','ip_modificacion','id_usuariomod'));
            if (!count($clinical)) return;
        }
        $reason = trim((string) request()->input('amendment_reason'));
        if (strlen($reason) < 5) {
            throw new \InvalidArgumentException('La corrección de un documento firmado requiere un motivo de adenda de al menos 5 caracteres.');
        }
    }
}
