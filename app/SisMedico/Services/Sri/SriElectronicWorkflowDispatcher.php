<?php

namespace Sis_medico\Services\Sri;

use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Jobs\ProcessSriElectronicDocument;
use Sis_medico\SriElectronicWorkflow;

class SriElectronicWorkflowDispatcher
{
    public function dispatch($document, $documentType, $companyId, $status = 'queued')
    {
        if (!$document) throw new \RuntimeException('No existe el documento electrónico que se debe encolar.');
        if (!Schema::hasTable('sri_electronic_workflows')) {
            throw new \RuntimeException('No existe la tabla sri_electronic_workflows. Ejecute las migraciones SRI.');
        }

        $definition = config('sri.schemas.'.str_pad((string) $documentType, 2, '0', STR_PAD_LEFT));
        if (!$definition) throw new \RuntimeException('No existe el esquema SRI para el tipo de documento '.str_pad((string) $documentType, 2, '0', STR_PAD_LEFT).'.');

        $workflow = SriElectronicWorkflow::firstOrCreate(
            array('document_id' => $document->id),
            array(
                'company_id' => (string) $companyId,
                'document_type' => str_pad((string) $documentType, 2, '0', STR_PAD_LEFT),
                'schema_version' => $definition['version'],
                'status' => 'pending',
            )
        );

        if (in_array($workflow->status, array('authorized', 'emailed'), true)) {
            return $workflow;
        }

        $workflow->update(array(
            'status' => $status,
            'attempts' => 0,
            'last_error' => null,
            'next_attempt_at' => null,
        ));
        ProcessSriElectronicDocument::dispatch($workflow->id);

        return $workflow->fresh();
    }
}
