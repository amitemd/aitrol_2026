<?php

namespace Sis_medico\Services\Sri;

use Illuminate\Support\Facades\Log;
use Sis_medico\v3\contable\NotaCreditoClientes;
use Sis_medico\Services\V3CustomerCreditNoteElectronicDocumentService;
use Sis_medico\Services\V3CustomerDebitNoteElectronicDocumentService;
use Sis_medico\Services\V3PurchaseLiquidationElectronicDocumentService;
use Sis_medico\Services\V3ProviderWithholdingElectronicService;
use Sis_medico\Services\V3SalesElectronicDocumentService;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\Retenciones;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Throwable;

class ElectronicDocumentCollector
{
    public function collect(array $companyIds, $limit = 50)
    {
        $result = array('sales' => 0, 'credit_notes' => 0, 'debit_notes' => 0, 'liquidations' => 0,
            'withholdings' => 0, 'errors' => 0, 'failures' => array());

        Ventas::whereIn('id_empresa', $companyIds)
            ->where('electronica', 1)->where('estado', 1)
            ->where(function ($query) {
                $query->whereNull('tipo')->orWhere('tipo', '<>', 'N-D');
            })
            // Compatibilidad con facturas antiguas que fueron guardadas con
            // NULL. Las nuevas se crean en 0. La clave ya no es requisito de
            // selección porque V3SalesElectronicDocumentService la genera.
            ->where(function ($query) {
                $query->where('doc_electronico', 0)->orWhereNull('doc_electronico');
            })
            ->orderBy('id')->limit($limit)->get()->each(function ($sale) use (&$result) {
                try {
                    app(V3SalesElectronicDocumentService::class)->register($sale);
                    $result['sales']++;
                } catch (Throwable $exception) {
                    $result['errors']++;
                    $result['failures'][] = array(
                        'type' => 'factura', 'id' => $sale->id,
                        'company_id' => $sale->id_empresa, 'error' => $exception->getMessage(),
                    );
                    try {
                        app(ElectronicDocumentStateRecorder::class)->preparationFailure($sale, '01', $exception);
                    } catch (Throwable $logException) {
                        Log::error('No se pudo registrar el error de preparación de la factura.', array(
                            'sale_id' => $sale->id, 'error' => $logException->getMessage(),
                        ));
                    }
                    Log::error('No se pudo preparar la factura para el flujo SRI.', array(
                        'sale_id' => $sale->id, 'company_id' => $sale->id_empresa,
                        'error' => $exception->getMessage(),
                    ));
                }
            });

        $remaining = max(0, $limit - $result['sales']);
        if ($remaining > 0) {
            NotaCreditoClientes::whereIn('id_empresa', $companyIds)
                ->where('electronica', 1)->where('estado', 1)->where('doc_electronico', 0)
                ->orderBy('id')->limit($remaining)->get()->each(function ($note) use (&$result) {
                    try {
                        app(V3CustomerCreditNoteElectronicDocumentService::class)->register($note);
                        $result['credit_notes']++;
                    } catch (Throwable $exception) {
                        $result['errors']++;
                        $result['failures'][] = array('type' => 'nota de crédito', 'id' => $note->id,
                            'company_id' => $note->id_empresa, 'error' => $exception->getMessage());
                        try { app(ElectronicDocumentStateRecorder::class)->preparationFailure($note, '04', $exception); } catch (Throwable $logException) {
                            Log::error('No se pudo registrar el error de preparación de la nota de crédito.', array('note_id' => $note->id, 'error' => $logException->getMessage()));
                        }
                        Log::error('No se pudo preparar la nota de crédito para el flujo SRI.', array('note_id' => $note->id, 'company_id' => $note->id_empresa, 'error' => $exception->getMessage()));
                    }
                });
        }
        $remaining = max(0, $limit - $result['sales'] - $result['credit_notes']);
        if ($remaining > 0) {
            Ct_Nota_Debito_Cliente::whereIn('id_empresa', $companyIds)
                ->where('electronica', 1)->where('estado', 1)
                ->where('doc_electronico', 0)
                ->orderBy('id')->limit($remaining)->get()->each(function ($note) use (&$result) {
                    try {
                        app(V3CustomerDebitNoteElectronicDocumentService::class)->register($note);
                        $result['debit_notes']++;
                    } catch (Throwable $exception) {
                        $result['errors']++;
                        $result['failures'][] = array('type' => 'nota de débito', 'id' => $note->id,
                            'company_id' => $note->id_empresa, 'error' => $exception->getMessage());
                        try {
                            app(ElectronicDocumentStateRecorder::class)->preparationFailure($note, '05', $exception);
                        } catch (Throwable $logException) {
                            Log::error('No se pudo registrar el error de preparación de la nota de débito.',
                                array('note_id' => $note->id, 'error' => $logException->getMessage()));
                        }
                        Log::error('No se pudo preparar la nota de débito para el flujo SRI.',
                            array('note_id' => $note->id, 'company_id' => $note->id_empresa,
                                'error' => $exception->getMessage()));
                    }
                });
        }
        $remaining = max(0, $limit - $result['sales'] - $result['credit_notes'] - $result['debit_notes']);
        if ($remaining > 0) {
            Compras::whereIn('id_empresa', $companyIds)
                ->where('tipo_comprobante', '03')->where('electronica', 1)
                ->where('estado', 1)->where('doc_electronico', 0)
                ->orderBy('id')->limit($remaining)->get()->each(function ($purchase) use (&$result) {
                    try {
                        app(V3PurchaseLiquidationElectronicDocumentService::class)->register($purchase);
                        $result['liquidations']++;
                    } catch (Throwable $exception) {
                        $result['errors']++;
                        $result['failures'][] = array('type' => 'liquidación de compra', 'id' => $purchase->id,
                            'company_id' => $purchase->id_empresa, 'error' => $exception->getMessage());
                        try {
                            app(ElectronicDocumentStateRecorder::class)->preparationFailure($purchase, '03', $exception);
                        } catch (Throwable $logException) {
                            Log::error('No se pudo registrar el error de preparación de la liquidación.',
                                array('purchase_id' => $purchase->id, 'error' => $logException->getMessage()));
                        }
                        Log::error('No se pudo preparar la liquidación para el flujo SRI.', array(
                            'purchase_id' => $purchase->id, 'company_id' => $purchase->id_empresa,
                            'error' => $exception->getMessage(),
                        ));
                    }
                });
        }

        $remaining = max(0, $limit - $result['sales'] - $result['credit_notes'] - $result['debit_notes'] - $result['liquidations']);
        if ($remaining > 0) {
            Retenciones::whereIn('id_empresa', $companyIds)
                ->where('electronica', 1)->where('estado', 1)
                ->where('doc_electronico', 0)
                ->whereNotNull('clave_acceso')->where('clave_acceso', '<>', '')
                ->orderBy('id')->limit($remaining)->get()->each(function ($withholding) use (&$result) {
                    try {
                        app(V3ProviderWithholdingElectronicService::class)->register($withholding);
                        $result['withholdings']++;
                    } catch (Throwable $exception) {
                        $result['errors']++;
                        $result['failures'][] = array(
                            'type' => 'retencion', 'id' => $withholding->id,
                            'company_id' => $withholding->id_empresa, 'error' => $exception->getMessage(),
                        );
                        try {
                            app(ElectronicDocumentStateRecorder::class)->preparationFailure($withholding, '07', $exception);
                        } catch (Throwable $logException) {
                            Log::error('No se pudo registrar el error de preparación de la retención.', array(
                                'withholding_id' => $withholding->id, 'error' => $logException->getMessage(),
                            ));
                        }
                        Log::error('No se pudo preparar la retención para el flujo SRI.', array(
                            'withholding_id' => $withholding->id, 'company_id' => $withholding->id_empresa,
                            'error' => $exception->getMessage(),
                        ));
                    }
                });
        }

        return $result;
    }
}
