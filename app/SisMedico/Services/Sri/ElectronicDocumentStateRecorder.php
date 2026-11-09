<?php

namespace Sis_medico\Services\Sri;

use Illuminate\Support\Facades\DB;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\SriElectronicWorkflow;
use Throwable;

class ElectronicDocumentStateRecorder
{
    const GENERATED = 1;
    const XSD_VALIDATED = 2;
    const SIGNED = 3;
    const RECEIVED = 4;
    const AUTHORIZED = 5;
    const SIGNATURE_ERROR = 6;
    const GENERATION_ERROR = 7;
    const XSD_ERROR = 8;
    const RECEPTION_ERROR = 9;
    const AUTHORIZATION_ERROR = 10;
    const EMAILED = 11;
    const IN_PROCESS = 12;

    public function record(
        De_Documentos_Electronicos $document,
        $documentType,
        $step,
        $information,
        $comment = null,
        $isError = false,
        $updateDocumentState = true
    ) {
        DB::transaction(function () use ($document, $documentType, $step, $information, $comment, $isError, $updateDocumentState) {
            if ($updateDocumentState) {
                $document->id_de_pasos = $step;
                $document->save();
                $this->updateSource($documentType, $document->id_documento, $document->ruc_emisor, $step);
            }

            DB::table('de_log_facturacion')->insert($this->audit($document) + array(
                'tipo_documento' => str_pad((string) $documentType, 2, '0', STR_PAD_LEFT),
                'id_pasos' => $step,
                'id_documento' => $document->id_documento,
                'clave_acceso' => $document->clave_acceso,
                'informacion' => $this->stringValue($information),
                'comentario' => $comment === null ? null : $this->stringValue($comment),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));

            if ($isError) {
                DB::table('de_log_error')->insert($this->audit($document) + array(
                    'id_de_documentos_electronicos' => $document->id,
                    'id_documento' => $document->id_documento,
                    'descripcion_error' => $this->stringValue($comment === null ? $information : $comment),
                    'estado' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
            }
        });
    }

    public function preparationFailure($model, $documentType, Throwable $exception)
    {
        $step = self::GENERATION_ERROR;
        $audit = array(
            'id_usuariocrea' => $model->id_usuariocrea,
            'id_usuariomod' => $model->id_usuariomod,
            'ip_creacion' => $model->ip_creacion ?: '127.0.0.1',
            'ip_modificacion' => $model->ip_modificacion ?: '127.0.0.1',
        );
        DB::transaction(function () use ($model, $documentType, $exception, $step, $audit) {
            $model->doc_electronico = $step;
            $model->save();
            $accessKey = trim((string) ($model->clave_acceso ?: $model->nro_autorizacion));
            DB::table('de_log_facturacion')->insert($audit + array(
                'tipo_documento' => str_pad((string) $documentType, 2, '0', STR_PAD_LEFT),
                'id_pasos' => $step, 'id_documento' => $model->id,
                'clave_acceso' => $accessKey ?: 'SIN-CLAVE-'.$model->id,
                'informacion' => 'No se pudo preparar el documento electrónico.',
                'comentario' => $exception->getMessage(),
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ));
            DB::table('de_log_error')->insert($audit + array(
                'id_de_documentos_electronicos' => null, 'id_documento' => $model->id,
                'descripcion_error' => $exception->getMessage(), 'estado' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ));
        });
    }

    public function failure(SriElectronicWorkflow $workflow, Throwable $exception)
    {
        $workflow->load('document');
        if (!$workflow->document) return;
        $map = array(
            'generating' => stripos($exception->getMessage(), 'XSD') !== false ? self::XSD_ERROR : self::GENERATION_ERROR,
            'signing' => self::SIGNATURE_ERROR,
            'sending' => self::RECEPTION_ERROR,
            'authorizing' => self::AUTHORIZATION_ERROR,
            'authorized' => self::EMAILED,
        );
        $step = isset($map[$workflow->status]) ? $map[$workflow->status] : self::GENERATION_ERROR;
        if ((int) $workflow->document->id_de_pasos === $step && $workflow->status !== 'authorized') return;
        $this->record(
            $workflow->document,
            $workflow->document_type,
            $step,
            'Error durante el proceso electrónico.',
            $exception->getMessage(),
            true,
            $workflow->status !== 'authorized'
        );
    }

    private function updateSource($documentType, $documentId, $companyId, $step)
    {
        if (!$documentId) return;
        $normalizedType = str_pad((string) $documentType, 2, '0', STR_PAD_LEFT);
        $table = $normalizedType === '07' ? 'ct_retenciones'
            : ($normalizedType === '05' ? 'ct_debito_clientes'
            : ($normalizedType === '04' ? 'ct_nota_credito_clientes'
            : ($normalizedType === '03' ? 'ct_compras' : 'ct_ventas')));
        DB::table($table)->where('id', $documentId)->where('id_empresa', $companyId)
            ->update(array('doc_electronico' => $step, 'updated_at' => date('Y-m-d H:i:s')));
    }

    private function audit(De_Documentos_Electronicos $document)
    {
        return array(
            'id_usuariocrea' => $document->id_usuariocrea,
            'id_usuariomod' => $document->id_usuariomod,
            'ip_creacion' => $document->ip_creacion ?: '127.0.0.1',
            'ip_modificacion' => $document->ip_modificacion ?: '127.0.0.1',
        );
    }

    private function stringValue($value)
    {
        if (is_string($value)) return $value;
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
