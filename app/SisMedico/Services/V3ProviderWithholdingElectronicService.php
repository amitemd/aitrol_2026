<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\SriElectronicWorkflow;
use Sis_medico\Services\Sri\ElectronicDocumentStateRecorder;
use Sis_medico\Services\Sri\SriElectronicWorkflowDispatcher;
use Sis_medico\v3\contable\MaestroDocumentos;
use Sis_medico\v3\contable\Retenciones;

class V3ProviderWithholdingElectronicService
{
    /**
     * Registra el comprobante de retencion 2.0.0 para que el proceso PHP lo
     * genere, valide, firme, envie al SRI y remita por correo.
     */
    public function register(Retenciones $withholding)
    {
        $document = DB::transaction(function () use ($withholding) {
            $locked = Retenciones::where('id', $withholding->id)
                ->where('id_empresa', $withholding->id_empresa)->lockForUpdate()->firstOrFail();
            return $this->registerDocument($locked);
        });
        if ($document) {
            DB::afterCommit(function () use ($document, $withholding) {
                app(SriElectronicWorkflowDispatcher::class)->dispatch($document, '07', $withholding->id_empresa);
            });
        }
        return $document;
    }

    private function registerDocument(Retenciones $withholding)
    {
        if (!(int) $withholding->electronica || !$withholding->clave_acceso) {
            return null;
        }

        if ((int) $withholding->doc_electronico === 5) {
            throw new RuntimeException('La retención ya está autorizada; no se puede regenerar su clave.');
        }
        $purchase = DB::table('ct_compras')->where('id', $withholding->id_compra)
            ->where('id_empresa', $withholding->id_empresa)->first();
        $supplier = DB::table('proveedor')->where('id', $withholding->id_proveedor)->first();
        $company = DB::table('empresa')->where('id', $withholding->id_empresa)->first();
        $electronicCompany = DB::table('de_empresa')->where('id_empresa', $withholding->id_empresa)->where('estado', 1)->first();
        $branch = DB::table('ct_sucursales')->where('id_empresa', $withholding->id_empresa)
            ->where('codigo_sucursal', $withholding->sucursal)->first();

        if (!$purchase || !$supplier || !$company || !$electronicCompany) {
            throw new RuntimeException('No se puede preparar el comprobante SRI: faltan datos de compra, proveedor o empresa.');
        }

        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('07');
        if (!$masterId) throw new RuntimeException('No existe el maestro SRI de retenciones (07).');
        $previous = De_Documentos_Electronicos::where('id_documento', $withholding->id)
            ->where('ruc_emisor', $withholding->id_empresa)
            ->where('id_maestro_documento', $masterId)->get();
        $key = $this->accessKey($withholding, $electronicCompany->ambiente);
        foreach ($previous as $oldDocument) {
            $oldWorkflow = SriElectronicWorkflow::where('document_id', $oldDocument->id)->lockForUpdate()->first();
            if ($oldWorkflow && (in_array($oldWorkflow->status, ['authorized', 'emailed', 'annulment_required'], true)
                || $oldWorkflow->authorization_number || $oldWorkflow->authorized_at)) {
                throw new RuntimeException('La retención tiene una autorización registrada; no se puede regenerar.');
            }
            if ($oldWorkflow && in_array($oldWorkflow->status, ['generating', 'signing', 'sending', 'authorizing'], true)) {
                throw new RuntimeException('La retención está en procesamiento; espere antes de regenerarla.');
            }
            if ($oldDocument->clave_acceso !== $key && $oldWorkflow) {
                // Conservar el XML anterior como evidencia, pero impedir su reenvío.
                $oldWorkflow->update(['status' => 'cancelled', 'next_attempt_at' => null,
                    'last_error' => 'Clave reemplazada al regenerar la retención con tipo 07.']);
            }
        }
        $withholding->clave_acceso = $key;
        $withholding->autorizacion = $key;

        $details = DB::table('ct_detalle_retenciones as detail')
            ->leftJoin('ct_porcentaje_retenciones as percentage', 'percentage.id', '=', 'detail.id_porcentaje')
            ->where('detail.id_retenciones', $withholding->id)
            ->where('detail.estado', 1)
            ->orderBy('detail.id')
            ->get(array('detail.*', 'percentage.codigo_nombre', 'percentage.tipo as tax_type'));
        if ($details->isEmpty()) {
            throw new RuntimeException('La retencion no contiene impuestos para generar el XML.');
        }

        $taxes = array();
        foreach ($details as $detail) {
            $taxes[] = app(\Sis_medico\Services\Sri\WithholdingTaxValidator::class)->fromDetail($detail);
        }

        $supportTaxes = $this->supportTaxes($purchase);
        $zeroRate = (float) $purchase->subtotal_0;
        $taxable = (float) $purchase->subtotal_12;

        $identification = preg_replace('/\D+/', '', (string) $supplier->id);
        $email = isset($supplier->email) ? trim((string) $supplier->email) : '';
        $address = $branch && isset($branch->direccion_sucursal) && $branch->direccion_sucursal
            ? $branch->direccion_sucursal : $company->direccion;
        $infoTributaria = array(
            'ambiente' => (string) $electronicCompany->ambiente,
            'tipoEmision' => '1',
            'razonSocial' => (string) $company->razonsocial,
            'nombreComercial' => (string) ($company->nombrecomercial ?: $company->razonsocial),
            'ruc' => (string) $company->id,
            'claveAcceso' => (string) $withholding->clave_acceso,
            'codDoc' => '07',
            'estab' => str_pad($withholding->sucursal, 3, '0', STR_PAD_LEFT),
            'ptoEmi' => str_pad($withholding->punto_emision, 3, '0', STR_PAD_LEFT),
            'secuencial' => str_pad($withholding->secuencia, 9, '0', STR_PAD_LEFT),
            'dirMatriz' => (string) $company->direccion,
        );
        $infoCompRetencion = array(
            'fechaEmision' => date('d/m/Y', strtotime($withholding->fecha)),
            'dirEstablecimiento' => (string) $address,
            'obligadoContabilidad' => !empty($electronicCompany->contabilidad) ? 'SI' : 'NO',
            'tipoIdentificacionSujetoRetenido' => $this->identificationType($identification),
            'parteRel' => 'NO',
            'razonSocialSujetoRetenido' => (string) $supplier->razonsocial,
            'identificacionSujetoRetenido' => $identification,
            'periodoFiscal' => date('m/Y', strtotime($withholding->fecha)),
        );
        if (!empty($electronicCompany->contribuyente_especial)) {
            $infoCompRetencion = array_slice($infoCompRetencion, 0, 2, true)
                + array('contribuyenteEspecial' => (string) $electronicCompany->contribuyente_especial)
                + array_slice($infoCompRetencion, 2, null, true);
        }

        $supportNumber = preg_replace('/\D+/', '', (string) ($purchase->numero ?: $purchase->secuencia_f));
        $supportCode = str_pad(trim((string) ($purchase->credito_tributario ?? '')), 2, '0', STR_PAD_LEFT);
        if (!preg_match('/^\d{2}$/D', $supportCode) || $supportCode === '00') {
            throw new RuntimeException('Configure el código de sustento tributario de la compra antes de generar la retención ATS.');
        }
        if ($supportCode === '10' || (string) $purchase->tipo_comprobante === '41') {
            throw new RuntimeException('Esta retención requiere datos adicionales de dividendos o reembolsos que este generador no captura.');
        }
        $docsSustento = array(array(
            'codSustento' => $supportCode,
            'codDocSustento' => str_pad((string) $purchase->tipo_comprobante, 2, '0', STR_PAD_LEFT),
            'numDocSustento' => str_pad($supportNumber, 15, '0', STR_PAD_LEFT),
            'fechaEmisionDocSustento' => date('d/m/Y', strtotime($purchase->fecha)),
            'fechaRegistroContable' => date('d/m/Y', strtotime($purchase->fecha)),
            'numAutDocSustento' => preg_replace('/\s+/', '', (string) $purchase->autorizacion),
            'pagoLocExt' => '01',
            'totalSinImpuestos' => $this->money($zeroRate + $taxable),
            'importeTotal' => $this->money($purchase->total_final),
            'impuestosDocSustento' => $supportTaxes,
            'retenciones' => $taxes,
            'pagos' => array(array('formaPago' => '01', 'total' => $this->money($purchase->total_final))),
        ));

        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('07');
        if (!$masterId) {
            throw new RuntimeException('No existe el maestro activo para comprobantes de retencion SRI (07).');
        }
        $existing = De_Documentos_Electronicos::where('clave_acceso', $withholding->clave_acceso)->first();
        if ($existing && ((string) $existing->id_documento !== (string) $withholding->id
            || (string) $existing->ruc_emisor !== (string) $company->id
            || (string) $existing->id_maestro_documento !== (string) $masterId)) {
            throw new RuntimeException('La clave de acceso ya está registrada para otra retención; requiere revisión manual para evitar duplicados.');
        }

        $audit = array(
            'id_usuariocrea' => $withholding->id_usuariocrea,
            'id_usuariomod' => $withholding->id_usuariomod,
            'ip_creacion' => $withholding->ip_creacion,
            'ip_modificacion' => $withholding->ip_modificacion,
        );
        $document = De_Documentos_Electronicos::updateOrCreate(
            array('clave_acceso' => $withholding->clave_acceso),
            $audit + array(
                'id_de_pasos' => 1, 'id_maestro_documento' => $masterId,
                'id_documento' => $withholding->id, 'ruc_receptor' => $identification,
                'ruc_emisor' => $company->id, 'establecimiento' => $withholding->sucursal,
                'emision' => $withholding->punto_emision, 'secuencial' => str_pad($withholding->secuencia, 9, '0', STR_PAD_LEFT),
                'total' => (float) $withholding->valor_fuente + (float) $withholding->valor_iva,
                'infoTributaria' => json_encode($infoTributaria, JSON_UNESCAPED_UNICODE),
                'infoCompRetencion' => json_encode($infoCompRetencion, JSON_UNESCAPED_UNICODE),
                'docsSustento' => json_encode($docsSustento, JSON_UNESCAPED_UNICODE),
                'infoAdicional' => $email
                    ? json_encode(array(array('nombre' => 'Email', 'valor' => $email)), JSON_UNESCAPED_UNICODE)
                    : null,
                'estado' => 1,
            )
        );
        $withholding->doc_electronico = 1;
        $withholding->save();
        app(ElectronicDocumentStateRecorder::class)->record(
            $document, '07', ElectronicDocumentStateRecorder::GENERATED, 'Documento electrónico preparado y XML pendiente de validación.'
        );
        return $document;
    }

    public function accessKey(Retenciones $withholding, $environment)
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', substr((string) $withholding->fecha, 0, 10));
        if (!$date || $date->format('Y-m-d') !== substr((string) $withholding->fecha, 0, 10)) {
            throw new RuntimeException('La fecha de emisión de la retención no es válida.');
        }
        $generator = new \Sis_medico\Http\Controllers\v3\contable\ClaveAcceso();
        $generator->llenarDatos([
            'fecha_emision' => $date->format('d-m-Y'), 'tipo_comprobante' => '07',
            'ruc' => (string) $withholding->id_empresa, 'tipo_ambiente' => (string) $environment,
            'punto_establecimiento' => str_pad($withholding->sucursal, 3, '0', STR_PAD_LEFT),
            'punto_emision' => str_pad($withholding->punto_emision, 3, '0', STR_PAD_LEFT),
            'numero_comprobante' => (string) $withholding->secuencia, 'tipo_emision' => '1',
        ]);
        if ($generator->isError || !preg_match('/^\d{49}$/D', (string) $generator->clave_acceso)) {
            throw new RuntimeException('No se pudo generar una clave válida para la retención. Revise su numeración y empresa.');
        }
        return $generator->clave_acceso;
    }

    public function cancel(Retenciones $withholding)
    {
        $document = De_Documentos_Electronicos::where('id_documento', $withholding->id)
            ->where('clave_acceso', $withholding->clave_acceso)->first();
        if (!$document) return;
        $document->estado = 0;
        $document->save();
        $workflow = SriElectronicWorkflow::where('document_id', $document->id)->first();
        if ($workflow) {
            $workflow->status = in_array($workflow->status, array('authorized', 'emailed'), true)
                ? 'annulment_required' : 'cancelled';
            $workflow->next_attempt_at = null;
            $workflow->save();
        }
    }

    private function money($value)
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function vatCode($rate)
    {
        $codes = array('0' => '0', '5' => '5', '12' => '2', '13' => '10', '14' => '3', '15' => '4');
        $key = (string) (int) round($rate);
        if (!isset($codes[$key]) || abs((float) $rate - (float) $key) > 0.01) {
            throw new RuntimeException(
                'La factura de compra tiene una tarifa IVA calculada de '.$this->money($rate).
                '%. Revise la tarifa y los valores IVA de sus detalles antes de generar el XML SRI.'
            );
        }
        return $codes[$key];
    }

    /**
     * Obtiene la tarifa desde los detalles de la compra. El cociente entre los
     * totales de cabecera se conserva solo para compras historicas sin detalle.
     */
    private function supportTaxes($purchase)
    {
        $supportTaxes = array();
        $taxableDetails = DB::table('ct_detalle_compra')
            ->where('id_ct_compras', $purchase->id)
            ->where('estado', 1)
            ->where('iva', 1)
            ->get(array('total', 'cantidad', 'precio', 'descuento', 'porcentaje'));

        $groups = array();
        foreach ($taxableDetails as $detail) {
            $storedRate = (float) $detail->porcentaje;
            $rate = $storedRate > 1 ? $storedRate : $storedRate * 100;
            if ($rate <= 0) continue;
            $rate = round($rate, 2);
            $key = number_format($rate, 2, '.', '');
            $base = (float) $detail->total;
            if ($base <= 0) {
                $base = ((float) $detail->cantidad * (float) $detail->precio) - (float) $detail->descuento;
            }
            if (!isset($groups[$key])) $groups[$key] = 0;
            $groups[$key] += max(0, $base);
        }

        foreach ($groups as $rate => $base) {
            $supportTaxes[] = array(
                'codImpuestoDocSustento' => '2',
                'codigoPorcentaje' => $this->vatCode((float) $rate),
                'baseImponible' => $this->money($base),
                'tarifa' => $this->money($rate),
                'valorImpuesto' => $this->money($base * ((float) $rate / 100)),
            );
        }

        $taxable = (float) $purchase->subtotal_12;
        $vat = (float) $purchase->iva_total;
        if (!$groups && ($taxable > 0 || $vat > 0)) {
            if ($taxable <= 0) {
                throw new RuntimeException('La factura registra IVA, pero no tiene una base gravada valida para generar el XML SRI.');
            }
            $rate = round(($vat / $taxable) * 100, 2);
            $supportTaxes[] = array(
                'codImpuestoDocSustento' => '2', 'codigoPorcentaje' => $this->vatCode($rate),
                'baseImponible' => $this->money($taxable), 'tarifa' => $this->money($rate),
                'valorImpuesto' => $this->money($vat),
            );
        }

        $zeroRate = (float) $purchase->subtotal_0;
        if ($zeroRate > 0 || !$supportTaxes) {
            $supportTaxes[] = array(
                'codImpuestoDocSustento' => '2', 'codigoPorcentaje' => '0',
                'baseImponible' => $this->money($zeroRate), 'tarifa' => '0.00', 'valorImpuesto' => '0.00',
            );
        }
        return $supportTaxes;
    }

    private function identificationType($identification)
    {
        if (strlen($identification) === 13) return '04';
        if (strlen($identification) === 10) return '05';
        return '06';
    }
}
