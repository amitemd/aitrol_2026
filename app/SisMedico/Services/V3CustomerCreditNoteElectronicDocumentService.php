<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Services\Sri\ElectronicDocumentStateRecorder;
use Sis_medico\Services\Sri\SriElectronicWorkflowDispatcher;
use Sis_medico\v3\contable\MaestroDocumentos;
use Sis_medico\v3\contable\NotaCreditoClientes;

/** Registra notas de crédito de clientes en el flujo PHP nuevo del SRI. */
class V3CustomerCreditNoteElectronicDocumentService
{
    public function register(NotaCreditoClientes $note)
    {
        if (!(int) $note->electronica || (int) $note->estado === 0) return null;

        $company = DB::table('empresa')->where('id', $note->id_empresa)->where('estado', 1)->first();
        $electronicCompany = DB::table('de_empresa')->where('id_empresa', $note->id_empresa)->where('estado', 1)->first();
        if (!$company || !$electronicCompany) throw new RuntimeException('Falta la configuración electrónica de la empresa para la nota de crédito.');

        $branch = DB::table('ct_sucursales')->where('id_empresa', $note->id_empresa)->where('codigo_sucursal', $note->sucursal)->first();
        // El correo del cliente se encuentra en la persona vinculada al cliente;
        // ct_clientes no almacena necesariamente email_representante.
        $customer = DB::table('ct_clientes as clientes')
            ->leftJoin('personas', 'personas.id', '=', 'clientes.persona_id')
            ->where('clientes.identificacion', $note->id_cliente)
            ->select(
                'clientes.*',
                'personas.email as email_representante',
                'personas.direccion as direccion_representante'
            )->first();
        $invoice = $note->id_factura ? DB::table('ct_ventas')->where('id', $note->id_factura)->where('id_empresa', $note->id_empresa)->first() : null;
        if (!$invoice) throw new RuntimeException('La nota de crédito no tiene una factura de sustento válida.');

        $establishment = str_pad((string) $note->sucursal, 3, '0', STR_PAD_LEFT);
        $emissionPoint = str_pad((string) $note->punto_emision, 3, '0', STR_PAD_LEFT);
        $sequential = str_pad((string) $note->secuencia, 9, '0', STR_PAD_LEFT);
        $noteDate = $this->sriDate($note->fecha, 'fecha de emisión de la nota de crédito');
        $invoiceDate = $this->sriDate($invoice->fecha, 'fecha de emisión de la factura de sustento');
        $key = $this->accessKey($noteDate, $company, $electronicCompany, $establishment, $emissionPoint, $sequential);

        $details = DB::table('ct_devolucion_productos')->where('id_nota_credito', $note->id)->where('estado', 1)->orderBy('id')->get();
        if ($details->isEmpty()) throw new RuntimeException('La nota de crédito no contiene detalles para generar el XML.');

        $xmlDetails = array();
        $taxes = array();
        foreach ($details as $detail) {
            $base = round(max(0, ((float) $detail->cantidad * (float) $detail->precio) - (float) $detail->descuento), 2);
            $tax = round((float) $detail->valor_iva, 2);
            $rate = $tax > 0 && $base > 0 ? round($tax / $base * 100, 2) : 0;
            $code = $this->vatCode($rate);
            $group = $code.'|'.$rate;
            if (!isset($taxes[$group])) $taxes[$group] = array('codigo' => '2', 'codigoPorcentaje' => $code, 'baseImponible' => 0, 'valor' => 0);
            $taxes[$group]['baseImponible'] += $base;
            $taxes[$group]['valor'] += $tax;
            $xmlDetails[] = array(
                'codigoInterno' => substr((string) $detail->codigo, 0, 25),
                'codigoAdicional' => substr((string) $detail->codigo, 0, 25),
                'descripcion' => substr((string) ($detail->descripcion ?: $detail->nombre), 0, 300),
                'cantidad' => $this->decimal($detail->cantidad, 6),
                'precioUnitario' => $this->decimal($detail->precio, 6),
                'descuento' => $this->money($detail->descuento),
                'precioTotalSinImpuesto' => $this->money($base),
                'detallesAdicionales' => array(array('@attributes' => array('nombre' => 'Producto', 'valor' => substr((string) $detail->nombre, 0, 300)))),
                'impuestos' => array(array('codigo' => '2', 'codigoPorcentaje' => $code, 'tarifa' => $this->money($rate), 'baseImponible' => $this->money($base), 'valor' => $this->money($tax))),
            );
        }

        $totalTaxes = array_values(array_map(function ($tax) {
            $tax['baseImponible'] = $this->money($tax['baseImponible']);
            $tax['valor'] = $this->money($tax['valor']);
            return $tax;
        }, $taxes));
        $identification = preg_replace('/\D+/', '', (string) $note->id_cliente);
        $customerName = trim((string) ($customer->nombre ?? $identification));
        if ($identification === '' || $customerName === '') throw new RuntimeException('La nota de crédito no tiene comprador válido.');

        $issuerRuc = (string) $electronicCompany->id_empresa;
        if ($issuerRuc === '') throw new RuntimeException('La configuración electrónica no tiene RUC de empresa.');
        $infoTributaria = array('ambiente' => (string) $electronicCompany->ambiente, 'tipoEmision' => '1',
            'razonSocial' => (string) $company->razonsocial, 'nombreComercial' => (string) ($company->nombrecomercial ?: $company->razonsocial),
            'ruc' => $issuerRuc, 'claveAcceso' => $key, 'codDoc' => '04', 'estab' => $establishment,
            'ptoEmi' => $emissionPoint, 'secuencial' => $sequential, 'dirMatriz' => (string) $company->direccion);
        // La secuencia es la definida por notaCredito_V1.1.0.xsd del SRI.
        // Los campos opcionales deben omitirse, no enviarse vacíos.
        $infoNota = array('fechaEmision' => $noteDate,
            'dirEstablecimiento' => (string) (($branch && $branch->direccion_sucursal) ? $branch->direccion_sucursal : $company->direccion),
            'tipoIdentificacionComprador' => $this->identificationType($identification, $customer), 'razonSocialComprador' => $customerName,
            'identificacionComprador' => $identification,
            'obligadoContabilidad' => !empty($electronicCompany->contabilidad) ? 'SI' : 'NO',
            'codDocModificado' => '01', 'numDocModificado' => (string) $invoice->nro_comprobante,
            'fechaEmisionDocSustento' => $invoiceDate,
            'totalSinImpuestos' => $this->money($note->subtotal),
            'valorModificacion' => $this->money($note->total_credito), 'moneda' => 'DOLAR',
            'totalConImpuestos' => $totalTaxes,
            'motivo' => substr((string) ($note->concepto ?: $note->observacion), 0, 300));
        if (!empty($electronicCompany->contribuyente_especial)) {
            $infoNota = array_slice($infoNota, 0, 5, true)
                + array('contribuyenteEspecial' => (string) $electronicCompany->contribuyente_especial)
                + array_slice($infoNota, 5, null, true);
        }
        $additional = array_values(array_filter(array(
            array('nombre' => 'Correo', 'valor' => (string) ($customer->email_representante ?? '')),
            array('nombre' => 'Dirección', 'valor' => (string) ($customer->direccion_representante ?? '')),
        ), function ($item) { return trim($item['valor']) !== ''; }));

        $masterId = MaestroDocumentos::getIdMaestrosDocumentos('04');
        if (!$masterId) throw new RuntimeException('No existe el maestro activo para notas de crédito SRI (04).');
        $this->clearPreviousArtifacts($note, $issuerRuc, $masterId);
        $existing = De_Documentos_Electronicos::where('clave_acceso', $key)->first();
        if ($existing && ((string) $existing->id_documento !== (string) $note->id
            || (string) $existing->ruc_emisor !== $issuerRuc)) {
            throw new RuntimeException('La clave de acceso ya está registrada para otro documento; requiere revisión manual para evitar duplicados.');
        }
        $document = De_Documentos_Electronicos::updateOrCreate(array('clave_acceso' => $key), array(
            'id_de_pasos' => 1, 'id_maestro_documento' => $masterId, 'id_documento' => $note->id,
            'ruc_receptor' => $identification, 'ruc_emisor' => $issuerRuc, 'establecimiento' => $establishment,
            'emision' => $emissionPoint, 'secuencial' => $sequential, 'total' => (float) $note->total_credito,
            'infoTributaria' => json_encode($infoTributaria, JSON_UNESCAPED_UNICODE), 'infoNotaCredito' => json_encode($infoNota, JSON_UNESCAPED_UNICODE),
            'detalles' => json_encode($xmlDetails, JSON_UNESCAPED_UNICODE), 'infoAdicional' => json_encode($additional, JSON_UNESCAPED_UNICODE),
            'estado' => 1, 'id_usuariocrea' => $note->id_usuariocrea, 'id_usuariomod' => $note->id_usuariomod,
            'ip_creacion' => $note->ip_creacion ?: '127.0.0.1', 'ip_modificacion' => $note->ip_modificacion ?: '127.0.0.1'));
        $note->nro_autorizacion = $key;
        $note->doc_electronico = 1;
        $note->save();
        app(ElectronicDocumentStateRecorder::class)->record($document, '04', ElectronicDocumentStateRecorder::GENERATED, 'Nota de crédito preparada y XML pendiente de validación.');
        $workflow = app(SriElectronicWorkflowDispatcher::class)->dispatch($document, '04', $note->id_empresa);
        if (!$workflow) throw new RuntimeException('La nota de crédito fue registrada, pero no se pudo crear su flujo SRI.');
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
                foreach (array($workflow->unsigned_path, $workflow->signed_path, $workflow->authorized_path, $workflow->ride_path) as $path) {
                    if ($path && $disk->exists($path)) $disk->delete($path);
                }
                $workflow->update(array(
                    'status' => 'cancelled', 'unsigned_path' => null, 'signed_path' => null,
                    'authorized_path' => null, 'next_attempt_at' => null,
                    'last_error' => 'Reemplazado por una regeneración completa del comprobante.'
                ));
            }
        }
    }

    private function accessKey($date, $company, $electronicCompany, $establishment, $emissionPoint, $sequential)
    {
        $key = new ClaveAcceso();
        $dateObject = \DateTimeImmutable::createFromFormat('!d/m/Y', $date);
        if (!$dateObject || $dateObject->format('d/m/Y') !== $date) {
            throw new RuntimeException('No se pudo convertir la fecha de emisión para generar la clave de acceso.');
        }
        // Se entrega ISO al generador para evitar que strtotime interprete dd/mm/yyyy
        // como una fecha inválida según la configuración regional del servidor.
        $key->llenarDatos(array('fecha_emision' => $dateObject->format('Y-m-d'), 'tipo_comprobante' => '04', 'ruc' => $electronicCompany->id_empresa,
            'tipo_ambiente' => $electronicCompany->ambiente, 'punto_establecimiento' => $establishment, 'punto_emision' => $emissionPoint,
            'numero_comprobante' => $sequential, 'tipo_emision' => '1'));
        if (!$key->clave_acceso || strlen((string) $key->clave_acceso) !== 49 || !ctype_digit((string) $key->clave_acceso)) {
            throw new RuntimeException('No se pudo generar una clave de acceso válida de 49 dígitos para la nota de crédito.');
        }
        if (substr((string) $key->clave_acceso, 0, 8) !== $dateObject->format('dmY')) {
            throw new RuntimeException('La clave de acceso de la nota de crédito no corresponde a su fecha de emisión.');
        }
        return $key->clave_acceso;
    }

    private function sriDate($value, $label)
    {
        $date = null;
        if ($value instanceof \DateTimeInterface) {
            $date = \DateTimeImmutable::createFromInterface($value);
        } else {
            $raw = trim((string) $value);
            foreach (array('Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd-m-Y') as $format) {
                $candidate = \DateTimeImmutable::createFromFormat('!'.$format, $raw);
                $errors = \DateTimeImmutable::getLastErrors();
                if ($candidate && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                    && $candidate->format($format) === $raw) {
                    $date = $candidate;
                    break;
                }
            }
        }
        $year = $date ? (int) $date->format('Y') : 0;
        if (!$date || $year < 2000 || $year > ((int) date('Y') + 1)) {
            throw new RuntimeException('La '.$label.' es nula o inválida; no se generará una clave de acceso incorrecta.');
        }
        return $date->format('d/m/Y');
    }

    private function vatCode($rate)
    {
        $key = (string) (int) round($rate);
        $codes = array('0' => '0', '5' => '5', '12' => '2', '13' => '10', '14' => '3', '15' => '4');
        if (!isset($codes[$key]) || abs((float) $rate - (float) $key) > 0.01) throw new RuntimeException('La tarifa IVA '.$rate.'% no tiene código SRI configurado.');
        return $codes[$key];
    }

    private function identificationType($identification, $customer)
    {
        $configured = $customer ? str_pad((string) $customer->tipo, 2, '0', STR_PAD_LEFT) : '';
        if (in_array($configured, array('04', '05', '06', '07', '08'), true)) return $configured;
        return strlen($identification) === 13 ? '04' : '05';
    }

    private function money($value) { return number_format((float) $value, 2, '.', ''); }
    private function decimal($value, $places) { return number_format((float) $value, $places, '.', ''); }
}
