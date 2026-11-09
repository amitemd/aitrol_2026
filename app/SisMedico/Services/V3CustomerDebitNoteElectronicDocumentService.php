<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Services\Sri\ElectronicDocumentPayload;
use Sis_medico\Services\Sri\ElectronicDocumentStateRecorder;
use Sis_medico\Services\Sri\SriDocumentBuilder;
use Sis_medico\Services\Sri\SriElectronicWorkflowDispatcher;
use Sis_medico\Utilidades;
use Sis_medico\v3\contable\MaestroDocumentos;

/** Registra notas de débito de clientes en el flujo electrónico SRI tipo 05. */
class V3CustomerDebitNoteElectronicDocumentService
{
    public function register(Ct_Nota_Debito_Cliente $note)
    {
        if (!(int) $note->electronica || (int) $note->estado === 0) return null;

        $company = DB::table('empresa')->where('id', $note->id_empresa)->where('estado', 1)->first();
        $electronicCompany = DB::table('de_empresa')->where('id_empresa', $note->id_empresa)->where('estado', 1)->first();
        if (!$company || !$electronicCompany) {
            throw new RuntimeException('Falta la configuración electrónica de la empresa para la nota de débito.');
        }

        $branch = DB::table('ct_sucursales')->where('id_empresa', $note->id_empresa)
            ->where('codigo_sucursal', $note->sucursal)->first();
        $customer = DB::table('ct_clientes as clientes')
            ->leftJoin('personas', 'personas.id', '=', 'clientes.persona_id')
            ->where('clientes.identificacion', $note->id_cliente)
            ->select('clientes.*', 'personas.email as email_representante',
                'personas.direccion as direccion_representante')->first();
        $invoice = $note->id_factura
            ? DB::table('ct_ventas')->where('id', $note->id_factura)
                ->where('id_empresa', $note->id_empresa)->where('estado', '<>', 0)->first()
            : null;
        if (!$invoice) throw new RuntimeException('La nota de débito no tiene una factura de sustento válida.');

        $details = DB::table('ct_detalle_debito_clientes')->where('id_debito', $note->id)
            ->orderBy('id')->get();
        if ($details->isEmpty()) throw new RuntimeException('La nota de débito no contiene motivos para generar el XML.');

        $establishment = str_pad(substr(preg_replace('/\D+/', '', (string) $note->sucursal), -3), 3, '0', STR_PAD_LEFT);
        $emissionPoint = str_pad(substr(preg_replace('/\D+/', '', (string) $note->punto_emision), -3), 3, '0', STR_PAD_LEFT);
        $digits = preg_replace('/\D+/', '', (string) $note->secuencia);
        $sequential = str_pad(substr($digits, -9), 9, '0', STR_PAD_LEFT);
        $noteDate = $this->sriDate($note->fecha, 'fecha de emisión de la nota de débito');
        $invoiceDate = $this->sriDate($invoice->fecha, 'fecha de emisión de la factura de sustento');
        $sourceNumber = $this->sourceDocumentNumber($invoice->nro_comprobante, $invoice);
        $key = $this->accessKey($noteDate, $electronicCompany, $establishment, $emissionPoint, $sequential);

        $vatConfiguration = Utilidades::porcentajeIva($note->id_empresa);
        $vatRate = $vatConfiguration ? round((float) $vatConfiguration->iva * 100, 2) : 0;
        $taxes = array();
        $subtotal = 0;
        $taxTotal = 0;
        foreach ($details as $detail) {
            $base = round(max(0, (float) $detail->valor), 2);
            $rate = (int) $detail->iva === 1 ? $vatRate : 0;
            if ((int) $detail->iva === 1 && $rate <= 0) {
                throw new RuntimeException('La nota aplica IVA, pero la empresa no tiene una tarifa vigente configurada.');
            }
            $tax = round($base * $rate / 100, 2);
            $code = $this->vatCode($rate);
            $group = $code.'|'.$rate;
            if (!isset($taxes[$group])) {
                $taxes[$group] = array('codigo' => '2', 'codigoPorcentaje' => $code,
                    'tarifa' => $this->money($rate), 'baseImponible' => 0, 'valor' => 0);
            }
            $taxes[$group]['baseImponible'] += $base;
            $taxes[$group]['valor'] += $tax;
            $subtotal += $base;
            $taxTotal += $tax;
        }
        $subtotal = round($subtotal, 2);
        $taxTotal = round($taxTotal, 2);
        $expectedTotal = round($subtotal + $taxTotal, 2);
        if (abs($expectedTotal - round((float) $note->valor_contable, 2)) > V3SalesDocumentBalanceService::TOLERANCIA) {
            throw new RuntimeException('El total de la nota de débito no coincide con sus motivos e impuestos.');
        }
        $xmlTaxes = array_values(array_map(function ($tax) {
            $tax['baseImponible'] = $this->money($tax['baseImponible']);
            $tax['valor'] = $this->money($tax['valor']);
            return $tax;
        }, $taxes));

        $identification = preg_replace('/[^0-9A-Za-z]+/', '', (string) $note->id_cliente);
        $customerName = trim((string) ($customer->nombre ?? $identification));
        if ($identification === '' || $customerName === '') {
            throw new RuntimeException('La nota de débito no tiene comprador válido.');
        }
        $issuerRuc = (string) $electronicCompany->id_empresa;
        if (!preg_match('/^\d{13}$/', $issuerRuc)) {
            throw new RuntimeException('La configuración electrónica no tiene un RUC emisor válido de 13 dígitos.');
        }

        $infoTributaria = array(
            'ambiente' => (string) $electronicCompany->ambiente,
            'tipoEmision' => '1',
            'razonSocial' => (string) $company->razonsocial,
            'nombreComercial' => (string) ($company->nombrecomercial ?: $company->razonsocial),
            'ruc' => $issuerRuc,
            'claveAcceso' => $key,
            'codDoc' => '05',
            'estab' => $establishment,
            'ptoEmi' => $emissionPoint,
            'secuencial' => $sequential,
            'dirMatriz' => (string) $company->direccion,
        );
        $infoNote = array(
            'fechaEmision' => $noteDate,
            'dirEstablecimiento' => (string) (($branch && $branch->direccion_sucursal)
                ? $branch->direccion_sucursal : $company->direccion),
            'tipoIdentificacionComprador' => $this->identificationType($identification, $customer),
            'razonSocialComprador' => $customerName,
            'identificacionComprador' => $identification,
            'obligadoContabilidad' => !empty($electronicCompany->contabilidad) ? 'SI' : 'NO',
            'codDocModificado' => '01',
            'numDocModificado' => $sourceNumber,
            'fechaEmisionDocSustento' => $invoiceDate,
            'totalSinImpuestos' => $this->money($subtotal),
            'impuestos' => $xmlTaxes,
            'valorTotal' => $this->money($expectedTotal),
            'pagos' => array(array('formaPago' => '20', 'total' => $this->money($expectedTotal))),
        );
        if (!empty($electronicCompany->contribuyente_especial)) {
            $infoNote = array_slice($infoNote, 0, 5, true)
                + array('contribuyenteEspecial' => (string) $electronicCompany->contribuyente_especial)
                + array_slice($infoNote, 5, null, true);
        }
        $reason = trim((string) $note->concepto);
        if ($reason === '') $reason = trim((string) $details->first()->concepto);
        $reasons = array(array('razon' => substr($reason ?: 'Nota de débito', 0, 300),
            'valor' => $this->money($expectedTotal)));
        $additional = array_values(array_filter(array(
            array('nombre' => 'Correo', 'valor' => (string) ($customer->email_representante ?? '')),
            array('nombre' => 'Dirección', 'valor' => (string) ($customer->direccion_representante ?? '')),
        ), function ($item) { return trim($item['valor']) !== ''; }));

        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('05');
        if (!$masterId) throw new RuntimeException('No existe el maestro activo para notas de débito SRI (05).');
        $this->clearPreviousArtifacts($note, $issuerRuc, $masterId);
        $existing = De_Documentos_Electronicos::where('clave_acceso', $key)->first();
        if ($existing && ((string) $existing->id_documento !== (string) $note->id
            || (string) $existing->ruc_emisor !== $issuerRuc)) {
            throw new RuntimeException('La clave de acceso ya está registrada para otro documento; requiere revisión manual.');
        }
        $document = De_Documentos_Electronicos::updateOrCreate(array('clave_acceso' => $key), array(
            'id_de_pasos' => 1,
            'id_maestro_documento' => $masterId,
            'id_documento' => $note->id,
            'ruc_receptor' => $identification,
            'ruc_emisor' => $issuerRuc,
            'establecimiento' => $establishment,
            'emision' => $emissionPoint,
            'secuencial' => $sequential,
            'subtotal_0' => $taxTotal > 0 ? 0 : $subtotal,
            'subtotal_12' => $taxTotal > 0 ? $subtotal : 0,
            'subtotal_total' => $subtotal,
            'total' => $expectedTotal,
            'infoTributaria' => json_encode($infoTributaria, JSON_UNESCAPED_UNICODE),
            'infoNotaDebito' => json_encode($infoNote, JSON_UNESCAPED_UNICODE),
            'motivos' => json_encode($reasons, JSON_UNESCAPED_UNICODE),
            'infoAdicional' => json_encode($additional, JSON_UNESCAPED_UNICODE),
            'estado' => 1,
            'id_usuariocrea' => $note->id_usuariocrea,
            'id_usuariomod' => $note->id_usuariomod,
            'ip_creacion' => $note->ip_creacion ?: '127.0.0.1',
            'ip_modificacion' => $note->ip_modificacion ?: '127.0.0.1',
        ));

        // Detecta datos inválidos antes de dejar el documento en cola.
        $payload = app(ElectronicDocumentPayload::class)->from($document);
        app(SriDocumentBuilder::class)->build('05', $payload);

        $note->clave_acceso = $key;
        $note->nro_autorizacion = $key;
        $note->secuencia = $sequential;
        $note->doc_electronico = 1;
        $note->save();
        app(ElectronicDocumentStateRecorder::class)->record($document, '05',
            ElectronicDocumentStateRecorder::GENERATED,
            'Nota de débito preparada y XML pendiente de firma y envío.');
        $workflow = app(SriElectronicWorkflowDispatcher::class)->dispatch($document, '05', $note->id_empresa);
        if (!$workflow) throw new RuntimeException('La nota de débito fue registrada, pero no se pudo crear su flujo SRI.');
        return $workflow;
    }

    private function clearPreviousArtifacts($note, $companyId, $masterId)
    {
        if (!\Schema::hasTable('sri_electronic_workflows')) return;
        $disk = Storage::disk(config('sri.storage_disk'));
        $documents = De_Documentos_Electronicos::where('id_documento', $note->id)
            ->where('ruc_emisor', $companyId)->where('id_maestro_documento', $masterId)->get();
        foreach ($documents as $document) {
            $workflows = \Sis_medico\SriElectronicWorkflow::where('document_id', $document->id)->get();
            foreach ($workflows as $workflow) {
                foreach (array($workflow->unsigned_path, $workflow->signed_path,
                    $workflow->authorized_path, $workflow->ride_path) as $path) {
                    if ($path && $disk->exists($path)) $disk->delete($path);
                }
                $workflow->update(array('status' => 'cancelled', 'unsigned_path' => null,
                    'signed_path' => null, 'authorized_path' => null, 'next_attempt_at' => null,
                    'last_error' => 'Reemplazado por una regeneración completa del comprobante.'));
            }
        }
    }

    private function accessKey($date, $electronicCompany, $establishment, $emissionPoint, $sequential)
    {
        $dateObject = \DateTimeImmutable::createFromFormat('!d/m/Y', $date);
        $key = new ClaveAcceso();
        $key->llenarDatos(array('fecha_emision' => $dateObject->format('Y-m-d'),
            'tipo_comprobante' => '05', 'ruc' => $electronicCompany->id_empresa,
            'tipo_ambiente' => $electronicCompany->ambiente,
            'punto_establecimiento' => $establishment, 'punto_emision' => $emissionPoint,
            'numero_comprobante' => $sequential, 'tipo_emision' => '1'));
        if (!$key->clave_acceso || strlen((string) $key->clave_acceso) !== 49
            || !ctype_digit((string) $key->clave_acceso)
            || substr((string) $key->clave_acceso, 8, 2) !== '05') {
            throw new RuntimeException('No se pudo generar una clave de acceso tipo 05 válida para la nota de débito.');
        }
        return $key->clave_acceso;
    }

    private function sourceDocumentNumber($number, $invoice)
    {
        $parts = preg_split('/\D+/', trim((string) $number), -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) >= 3) {
            return str_pad(substr($parts[0], -3), 3, '0', STR_PAD_LEFT).'-'
                .str_pad(substr($parts[1], -3), 3, '0', STR_PAD_LEFT).'-'
                .str_pad(substr(end($parts), -9), 9, '0', STR_PAD_LEFT);
        }
        $sequence = preg_replace('/\D+/', '', (string) ($invoice->numero ?? $number));
        return str_pad(substr((string) ($invoice->sucursal ?? '1'), -3), 3, '0', STR_PAD_LEFT).'-'
            .str_pad(substr((string) ($invoice->punto_emision ?? '1'), -3), 3, '0', STR_PAD_LEFT).'-'
            .str_pad(substr($sequence, -9), 9, '0', STR_PAD_LEFT);
    }

    private function sriDate($value, $label)
    {
        $raw = trim((string) $value);
        $date = null;
        foreach (array('Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd-m-Y') as $format) {
            $candidate = \DateTimeImmutable::createFromFormat('!'.$format, $raw);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($candidate && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $candidate->format($format) === $raw) {
                $date = $candidate;
                break;
            }
        }
        if (!$date || (int) $date->format('Y') < 2000 || (int) $date->format('Y') > ((int) date('Y') + 1)) {
            throw new RuntimeException('La '.$label.' es nula o inválida.');
        }
        return $date->format('d/m/Y');
    }

    private function vatCode($rate)
    {
        $key = (string) (int) round($rate);
        $codes = array('0' => '0', '5' => '5', '12' => '2', '13' => '10', '14' => '3', '15' => '4');
        if (!isset($codes[$key]) || abs((float) $rate - (float) $key) > 0.01) {
            throw new RuntimeException('La tarifa IVA '.$rate.'% no tiene código SRI configurado.');
        }
        return $codes[$key];
    }

    private function identificationType($identification, $customer)
    {
        $configured = $customer ? str_pad((string) $customer->tipo, 2, '0', STR_PAD_LEFT) : '';
        if (in_array($configured, array('04', '05', '06', '07', '08'), true)) return $configured;
        return strlen($identification) === 13 ? '04' : '05';
    }

    private function money($value) { return number_format((float) $value, 2, '.', ''); }
}
