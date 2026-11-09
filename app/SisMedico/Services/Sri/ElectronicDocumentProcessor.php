<?php

namespace Sis_medico\Services\Sri;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sis_medico\De_Empresa;
use Sis_medico\SriElectronicWorkflow;

class ElectronicDocumentProcessor
{
    public function synchronizeAuthorization(SriElectronicWorkflow $workflow)
    {
        $workflow->load('document');
        $company = De_Empresa::where('id_empresa', $workflow->company_id)->where('estado', 1)->first();
        $this->assertProductionCompany($company);
        $directory = 'sri/' . $workflow->company_id . '/' . $workflow->document->clave_acceso;
        return $this->authorize($workflow, $directory);
    }

    public function resendAuthorizedEmail(SriElectronicWorkflow $workflow)
    {
        if (!in_array($workflow->status, ['authorized', 'emailed'], true)) {
            throw new RuntimeException('Sólo se puede reenviar un comprobante autorizado por el SRI.');
        }
        if (!config('sri.mail_enabled')) {
            throw new RuntimeException('El envío de correo electrónico está deshabilitado en la configuración SRI.');
        }
        $workflow->load('document');
        $xml = app(SriRidePdfGenerator::class)->authorizedXml($workflow);
        $recipient = $this->mail($workflow, $xml);
        if (!$recipient) {
            $tipoDestinatario = $workflow->document_type === '03' ? 'proveedor' : 'cliente';
            throw new RuntimeException('El '.$tipoDestinatario.' no tiene un correo electrónico válido configurado.');
        }
        return $recipient;
    }

    public function process(SriElectronicWorkflow $workflow)
    {
        $workflow->load('document');
        // Un job antiguo no puede autorizar ni reenviar una clave reemplazada
        // por una regeneración posterior de una nota de crédito o débito.
        if (in_array($workflow->document_type, array('04', '05'), true)
            && $workflow->document && $workflow->document->id_documento) {
            $sourceTable = $workflow->document_type === '05'
                ? 'ct_debito_clientes' : 'ct_nota_credito_clientes';
            $currentKey = DB::table($sourceTable)
                ->where('id', $workflow->document->id_documento)
                ->where('id_empresa', $workflow->company_id)
                ->value($workflow->document_type === '05' ? 'clave_acceso' : 'nro_autorizacion');
            if ($currentKey && (string) $currentKey !== (string) $workflow->document->clave_acceso) {
                $workflow->update(array(
                    'status' => 'cancelled', 'next_attempt_at' => null,
                    'last_error' => 'Workflow obsoleto: la nota fue regenerada con una nueva clave de acceso.'
                ));
                return;
            }
        }
        $company = De_Empresa::where('id_empresa', $workflow->company_id)->where('estado', 1)->first();
        $this->assertProductionCompany($company);
        $document = $workflow->document;
        $definition = config('sri.schemas.' . $workflow->document_type);
        if (substr((string) $document->clave_acceso, 8, 2) !== (string) $workflow->document_type) {
            throw new RuntimeException('El tipo de la clave de acceso no coincide con el comprobante. Regenere el documento antes de enviarlo al SRI.');
        }
        $directory = 'sri/' . $workflow->company_id . '/' . $document->clave_acceso;
        if ($workflow->status === 'retry' && $workflow->signed_path) {
            $workflow->update(['status' => 'authorization_queued']);
            if ($this->authorize($workflow, $directory, true)) return;
            $workflow->refresh();
        }
        if ($workflow->status === 'authorization_queued' && $workflow->signed_path) {
            return $this->authorize($workflow, $directory);
        }

        $workflow->update(['status' => 'generating', 'attempts' => $workflow->attempts + 1, 'last_error' => null]);
        $payload = app(ElectronicDocumentPayload::class)->from($document);
        $unsigned = app(SriDocumentBuilder::class)->build($workflow->document_type, $payload);
        $unsignedPath = $directory . '/unsigned.xml';
        Storage::disk(config('sri.storage_disk'))->put($unsignedPath, $unsigned);
        $workflow->update(['status' => 'signing', 'unsigned_path' => $unsignedPath]);
        $this->state($workflow, ElectronicDocumentStateRecorder::XSD_VALIDATED, 'XML generado y validado correctamente contra el XSD.');
        $signed = app(XadesBesSigner::class)->sign($unsigned, $this->signaturePath($company->ruta_firma, $company->id_empresa), $company->clave_firma);
        $this->validateSignedXml(
            $signed,
            $definition['file'],
            (string) $document->clave_acceso,
            (string) $workflow->document_type,
            (string) $definition['version'],
            (string) $company->id_empresa
        );
        $signedPath = $directory . '/signed.xml';
        Storage::disk(config('sri.storage_disk'))->put($signedPath, $signed);
        // El archivo que se envía debe ser exactamente el que acaba de quedar
        // guardado, nunca un XML anterior conservado en memoria o en caché.
        $signedToSend = Storage::disk(config('sri.storage_disk'))->get($signedPath);
        if ($signedToSend === false || hash('sha256', $signedToSend) !== hash('sha256', $signed)) {
            throw new RuntimeException('No se pudo verificar el XML firmado recién generado antes de enviarlo al SRI.');
        }
        $workflow->update(['status' => 'sending', 'signed_path' => $signedPath]);
        $this->state($workflow, ElectronicDocumentStateRecorder::SIGNED, 'XML firmado con PHP/OpenSSL mediante XAdES-BES.', [
            'signed_sha256' => hash('sha256', $signedToSend),
            'signed_bytes' => strlen($signedToSend),
            'signed_path' => $signedPath,
            'transport' => 'PHP SoapClient / WSDL base64Binary (XML sin precodificar)',
        ]);

        $reception = app(SriGateway::class)->receive($signedToSend);
        $document->respuestaSriRecepcion = json_encode($reception, JSON_UNESCAPED_UNICODE);
        $document->save();
        $state = strtoupper((string) data_get($reception, 'RespuestaRecepcionComprobante.estado', data_get($reception, 'estado', '')));
        if ($state !== 'RECIBIDA') {
            // Un SOAP Fault/caída interna del SRI no es un rechazo del XML.
            // No consultar autorización ni marcar manual: el job debe
            // reintentarlo conservando el XML firmado actual.
            if ($this->isTransientReceptionFailure($reception)) {
                throw new RuntimeException('Falla temporal del servicio de recepción del SRI: ' . json_encode($reception, JSON_UNESCAPED_UNICODE));
            }
            // Una respuesta de recepcion con error no garantiza que el
            // comprobante no haya sido registrado. Consultar autorizacion
            // evita dejar desactualizada una factura que el SRI ya autorizo.
            if ($this->authorize($workflow, $directory)) {
                return;
            }
            app(ElectronicDocumentStateRecorder::class)->record(
                $document,
                $workflow->document_type,
                ElectronicDocumentStateRecorder::RECEPTION_ERROR,
                'Documento devuelto por el servicio de recepción del SRI.',
                $reception,
                true
            );
            throw new RuntimeException('SRI devolvió el comprobante: ' . json_encode($reception, JSON_UNESCAPED_UNICODE));
        }
        $this->state($workflow, ElectronicDocumentStateRecorder::RECEIVED, 'Documento recibido correctamente por el SRI.', $reception);
        $workflow->update(['status' => 'waiting_authorization', 'last_response' => json_encode($reception, JSON_UNESCAPED_UNICODE), 'next_attempt_at' => Carbon::now()->addMinutes(config('sri.authorization_delay'))]);
    }

    private function authorize(SriElectronicWorkflow $workflow, $directory, $allowResend = false)
    {
        $document = $workflow->document;
        $workflow->update(['status' => 'authorizing', 'attempts' => $workflow->attempts + 1, 'next_attempt_at' => null]);
        $authorization = app(SriGateway::class)->authorize($document->clave_acceso);
        $document->respuestaSriAutorizacion = json_encode($authorization, JSON_UNESCAPED_UNICODE);
        $document->save();
        $authorizationData = data_get($authorization, 'RespuestaAutorizacionComprobante.autorizaciones.autorizacion');
        if (isset($authorizationData[0])) $authorizationData = $authorizationData[0];
        $authorizationState = strtoupper((string) data_get($authorizationData, 'estado', ''));
        if ($authorizationState !== 'AUTORIZADO') {
            if ($authorizationState === '' || $authorizationState === 'EN PROCESO') {
                if ($this->receptionHasError($document)) {
                    $receptionError = $this->receptionErrorMessage($document);
                    $message = 'El comprobante no está autorizado y la recepción falló: ' . $receptionError;
                    app(ElectronicDocumentStateRecorder::class)->record(
                        $document,
                        $workflow->document_type,
                        ElectronicDocumentStateRecorder::RECEPTION_ERROR,
                        'El comprobante no fue autorizado porque la recepción del SRI reportó un error.',
                        array('recepcion' => $receptionError, 'autorizacion' => $authorization),
                        true
                    );
                    $workflow->update(array(
                        'status' => 'failed',
                        'last_error' => $message,
                        'last_response' => json_encode($authorization, JSON_UNESCAPED_UNICODE),
                        'next_attempt_at' => null,
                    ));
                    return true;
                }
                $this->state($workflow, ElectronicDocumentStateRecorder::IN_PROCESS, 'El SRI mantiene el documento en proceso.', $authorization);
                $workflow->update(['status' => 'waiting_authorization', 'last_response' => json_encode($authorization, JSON_UNESCAPED_UNICODE), 'next_attempt_at' => Carbon::now()->addMinutes(config('sri.authorization_delay'))]);
                return true;
            }
            if ($allowResend) {
                $workflow->update(['status' => 'retry', 'next_attempt_at' => null]);
                return false;
            }
            app(ElectronicDocumentStateRecorder::class)->record(
                $document,
                $workflow->document_type,
                ElectronicDocumentStateRecorder::AUTHORIZATION_ERROR,
                'Documento no autorizado por el SRI.',
                $authorization,
                true
            );
            $workflow->update([
                'status' => 'failed',
                'last_error' => 'SRI respondió ' . $authorizationState . '.',
                'last_response' => json_encode($authorization, JSON_UNESCAPED_UNICODE),
                'next_attempt_at' => null
            ]);
            return;
        }
        $authorizedXml = (string) data_get($authorizationData, 'comprobante', '');
        if ($authorizedXml === '' && $workflow->signed_path) {
            $authorizedXml = Storage::disk(config('sri.storage_disk'))->get($workflow->signed_path);
        }
        $authorizedPath = $directory . '/authorized.xml';
        Storage::disk(config('sri.storage_disk'))->put($authorizedPath, $authorizedXml);
        $workflow->update([
            'status' => 'authorized',
            'authorized_path' => $authorizedPath,
            'authorization_number' => data_get($authorizationData, 'numeroAutorizacion'),
            'authorized_at' => $this->date(data_get($authorizationData, 'fechaAutorizacion')),
            'last_response' => json_encode($authorization, JSON_UNESCAPED_UNICODE),
            'next_attempt_at' => null,
        ]);
        if ($workflow->document_type === '01' && $document->id_documento) {
            DB::table('ct_ventas')->where('id', $document->id_documento)
                ->where('id_empresa', $workflow->company_id)->update(array(
                    'doc_electronico' => 5,
                    'nro_autorizacion' => data_get($authorizationData, 'numeroAutorizacion'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
        } elseif ($workflow->document_type === '07' && $document->id_documento) {
            DB::table('ct_retenciones')->where('id', $document->id_documento)
                ->where('id_empresa', $workflow->company_id)->update(array(
                    'doc_electronico' => 5,
                    'estado_electronica' => 5,
                    'autorizacion' => $document->clave_acceso,
                    'nro_autorizacion' => data_get($authorizationData, 'numeroAutorizacion'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
        } elseif ($workflow->document_type === '04' && $document->id_documento) {
            DB::table('ct_nota_credito_clientes')->where('id', $document->id_documento)
                ->where('id_empresa', $workflow->company_id)->update(array(
                    'doc_electronico' => 5,
                    'nro_autorizacion' => data_get($authorizationData, 'numeroAutorizacion'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
        } elseif ($workflow->document_type === '05' && $document->id_documento) {
            DB::table('ct_debito_clientes')->where('id', $document->id_documento)
                ->where('id_empresa', $workflow->company_id)->update(array(
                    'doc_electronico' => 5,
                    'clave_acceso' => $document->clave_acceso,
                    'nro_autorizacion' => data_get($authorizationData, 'numeroAutorizacion'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
        } elseif ($workflow->document_type === '03' && $document->id_documento) {
            DB::table('ct_compras')->where('id', $document->id_documento)
                ->where('id_empresa', $workflow->company_id)->update(array(
                    'doc_electronico' => 5,
                    'autorizacion' => $document->clave_acceso,
                    'nro_autorizacion' => data_get($authorizationData, 'numeroAutorizacion'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
        }
        $this->state($workflow, ElectronicDocumentStateRecorder::AUTHORIZED, 'Documento autorizado por el SRI.', $authorization);
        $this->mail($workflow->fresh('document'), $authorizedXml);
        return true;
    }

    private function assertProductionCompany($company)
    {
        if (!$company) throw new RuntimeException('La empresa no tiene configuración electrónica activa.');
        if ((int) $company->ambiente !== 2) throw new RuntimeException('El job SRI sólo procesa empresas configuradas en producción.');
        if (!$company->ruta_firma || !$company->clave_firma) throw new RuntimeException('La empresa no tiene firma electrónica configurada.');
        if (!is_readable($this->signaturePath($company->ruta_firma, $company->id_empresa))) throw new RuntimeException('El archivo de firma electrónica de la empresa no existe.');
    }

    private function validateSignedXml($xml, $schema, $expectedAccessKey, $expectedType, $expectedVersion, $expectedRuc)
    {
        libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        if (!$document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            $errors = array_map(function ($error) {
                return trim($error->message);
            }, libxml_get_errors());
            libxml_clear_errors();
            throw new RuntimeException('El XML firmado no es válido: ' . implode(' | ', array_unique($errors)));
        }
        $root = $document->documentElement;
        if (!$root || $root->getAttribute('id') !== 'comprobante') {
            throw new RuntimeException('El XML firmado no contiene el nodo id="comprobante".');
        }
        if ($root->getAttribute('version') !== $expectedVersion) {
            throw new RuntimeException('La versión del XML firmado no coincide con la versión configurada del XSD.');
        }
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('ds', XadesBesSigner::DS);
        if ($xpath->query('//ds:Signature')->length !== 1) {
            throw new RuntimeException('El XML firmado debe contener exactamente una firma XAdES-BES.');
        }
        $value = function ($query) use ($xpath) {
            $nodes = $xpath->query($query);
            return $nodes && $nodes->length ? trim($nodes->item(0)->textContent) : '';
        };
        if ($value('/*[1]/*[local-name()="infoTributaria"]/*[local-name()="claveAcceso"]') !== $expectedAccessKey) {
            throw new RuntimeException('La clave de acceso del XML firmado no coincide con la registrada en el documento.');
        }
        if ($value('/*[1]/*[local-name()="infoTributaria"]/*[local-name()="codDoc"]') !== $expectedType) {
            throw new RuntimeException('El tipo de documento del XML firmado no coincide con el workflow SRI.');
        }
        if ($value('/*[1]/*[local-name()="infoTributaria"]/*[local-name()="ruc"]') !== $expectedRuc) {
            throw new RuntimeException('El RUC del XML firmado no coincide con la empresa activa.');
        }
        $path = resource_path('sri/schemas/' . $schema);
        if (!$document->schemaValidate($path)) {
            $errors = array_map(function ($error) {
                return trim($error->message);
            }, libxml_get_errors());
            libxml_clear_errors();
            throw new RuntimeException('El XML firmado no cumple el XSD del SRI: ' . implode(' | ', array_unique($errors)));
        }
        libxml_clear_errors();
        if (!app(XadesBesSigner::class)->verify($xml)) {
            throw new RuntimeException('La verificación criptográfica del XML firmado falló antes del envío al SRI.');
        }
    }

    private function signaturePath($file, $companyId = null)
    {
        if (is_file($file)) return $file;
        $base = storage_path('app/facturaelectronica/p12');
        $candidates = array_filter([
            $companyId ? $base . '/' . $companyId . '/' . basename($file) : null,
            $base . '/' . basename($file),
        ]);
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) return $candidate;
        }
        return reset($candidates);
    }

    private function date($value)
    {
        try {
            return $value ? Carbon::parse($value) : Carbon::now();
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    private function receptionHasError($document)
    {
        $response = json_decode((string) $document->respuestaSriRecepcion, true);
        if (!is_array($response)) return false;
        $state = strtoupper((string) data_get($response, 'RespuestaRecepcionComprobante.estado', data_get($response, 'estado', '')));
        if ($state && $state !== 'RECIBIDA') return true;
        return data_get($response, 'RespuestaRecepcionComprobante.comprobantes.comprobante.mensajes.mensaje') !== null;
    }

    private function isTransientReceptionFailure($reception)
    {
        $text = strtolower(json_encode($reception, JSON_UNESCAPED_UNICODE));
        foreach (['could not open connection', 'genericjdbcexception', 'soap:fault', 'soap:server', 'temporarily unavailable', 'service unavailable'] as $term) {
            if (strpos($text, $term) !== false) return true;
        }
        return false;
    }

    private function receptionErrorMessage($document)
    {
        $response = json_decode((string) $document->respuestaSriRecepcion, true);
        $message = data_get($response, 'RespuestaRecepcionComprobante.comprobantes.comprobante.mensajes.mensaje');
        if (isset($message[0])) $message = $message[0];
        if (is_array($message)) {
            $id = (string) data_get($message, 'identificador', 'desconocido');
            $text = (string) data_get($message, 'mensaje', 'Error no especificado');
            $detail = (string) data_get($message, 'informacionAdicional', '');
            return 'Código ' . $id . ': ' . $text . ($detail ? ' - ' . $detail : '');
        }
        return 'Error de recepción no especificado.';
    }

    public function emailRecipient(SriElectronicWorkflow $workflow)
    {
        $workflow->loadMissing('document');
        if ((string) $workflow->document_type === '03') {
            if (!$workflow->document || (string) $workflow->document->ruc_emisor !== (string) $workflow->company_id) return null;
            $supplier = $workflow->getConnection()->table('ct_compras as compra')
                ->join('proveedor', 'proveedor.id', '=', 'compra.proveedor')
                ->leftJoin('personas as persona', 'persona.id', '=', 'proveedor.persona_id')
                ->where('compra.id', $workflow->document->id_documento)
                ->where('compra.id_empresa', $workflow->company_id)
                ->where('compra.tipo_comprobante', '03')
                ->where('compra.estado', 1)
                ->first(array('persona.email as persona_email', 'proveedor.email as proveedor_email'));
            $email = $supplier ? trim((string) $supplier->persona_email) : '';
            if ($email === '' && $supplier) $email = trim((string) $supplier->proveedor_email);
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }
        if ((string) $workflow->document_type === '07') {
            if (!$workflow->document || (string) $workflow->document->ruc_emisor !== (string) $workflow->company_id) return null;
            $email = $workflow->getConnection()->table('ct_retenciones as retencion')
                ->join('proveedor', 'proveedor.id', '=', 'retencion.id_proveedor')
                ->where('retencion.id', $workflow->document->id_documento)
                ->where('retencion.id_empresa', $workflow->company_id)->where('retencion.estado', 1)
                ->value('proveedor.email');
            $email = trim((string) $email);
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }
        if (in_array((string) $workflow->document_type, ['01', '04', '05'], true)) {
            // El XML conserva los datos históricos. El destinatario se consulta
            // en la ficha actual, también cuando el trabajador procesa la cola.
            if (!$workflow->document || (string) $workflow->document->ruc_emisor !== (string) $workflow->company_id) {
                return null;
            }
            $table = $workflow->document_type === '04' ? 'ct_nota_credito_clientes'
                : ($workflow->document_type === '05' ? 'ct_debito_clientes' : 'ct_ventas');
            $email = $workflow->getConnection()->table($table.' as origen')
                ->join('ct_clientes as cliente', 'cliente.identificacion', '=', 'origen.id_cliente')
                ->join('personas as persona', 'persona.id', '=', 'cliente.persona_id')
                ->where('origen.id', $workflow->document->id_documento)
                ->where('origen.id_empresa', $workflow->company_id)
                ->value('persona.email');
            $email = trim((string) $email);
            // No volver al correo antiguo si el actual está vacío o es inválido.
            return $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }
        $info = json_decode($workflow->document->infoAdicional, true);
        $email = data_get($info, 'email', data_get($info, 'Email'));
        if (!$email && is_array($info)) {
            foreach ($info as $field) {
                if (in_array(strtolower((string) data_get($field, 'nombre')), ['email', 'mail', 'correo'], true)) {
                    $email = data_get($field, 'valor');
                    break;
                }
            }
        }
        $email = trim((string) $email);
        return $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function mail(SriElectronicWorkflow $workflow, $xml)
    {
        if (!config('sri.mail_enabled')) return false;
        $email = $this->emailRecipient($workflow);
        if (!$email) return false;
        $ride = app(SriRidePdfGenerator::class)->generate($workflow, $xml);
        $company = De_Empresa::where('id_empresa', $workflow->company_id)->first();
        $senderName = $company && ($company->nombrecomercial || $company->razonsocial)
            ? ($company->nombrecomercial ?: $company->razonsocial) : 'MdConsgroup';
        Mail::send('sri_electronico.emails.authorized', ['workflow' => $workflow, 'company' => $company, 'senderName' => $senderName], function ($message) use ($email, $workflow, $xml, $ride, $senderName) {
            $titles = array(
                '01' => 'Factura electrónica autorizada',
                '03' => 'Liquidación de compra electrónica autorizada',
                '04' => 'Nota de crédito electrónica autorizada',
                '05' => 'Nota de débito electrónica autorizada',
                '07' => 'Comprobante de retención electrónico autorizado',
            );
            $title = isset($titles[$workflow->document_type])
                ? $titles[$workflow->document_type] : 'Comprobante electrónico autorizado';
            $message->to($email)->subject($title . ' ' . $workflow->document->clave_acceso);
            $message->from(config('mail.from.address'), $senderName);
            $message->attachData($xml, $workflow->document->clave_acceso . '.xml', ['mime' => 'application/xml']);
            $message->attachData($ride['bytes'], $workflow->document->clave_acceso . '.pdf', ['mime' => 'application/pdf']);
        });
        $workflow->update(['emailed_at' => Carbon::now(), 'status' => 'emailed']);
        $this->deleteTemporaryArtifacts($workflow);
        app(ElectronicDocumentStateRecorder::class)->record(
            $workflow->document,
            $workflow->document_type,
            ElectronicDocumentStateRecorder::EMAILED,
            'Documento autorizado enviado por correo a ' . $email . '.',
            null,
            false,
            false
        );
        return $email;
    }

    private function deleteTemporaryArtifacts(SriElectronicWorkflow $workflow)
    {
        $disk = Storage::disk(config('sri.storage_disk'));
        foreach ([$workflow->unsigned_path, $workflow->signed_path, $workflow->authorized_path] as $path) {
            if ($path && $disk->exists($path)) $disk->delete($path);
        }
        $workflow->update(['unsigned_path' => null, 'signed_path' => null, 'authorized_path' => null]);
    }

    private function state(SriElectronicWorkflow $workflow, $step, $information, $comment = null)
    {
        $workflow->load('document');
        app(ElectronicDocumentStateRecorder::class)->record(
            $workflow->document,
            $workflow->document_type,
            $step,
            $information,
            $comment
        );
    }
}
