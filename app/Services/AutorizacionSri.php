<?php

namespace App\Services;

use DOMDocument;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use stdClass;

final readonly class AutorizacionSri
{
    private const SERVICIOS = ['1' => 'celcer.sri.gob.ec', '2' => 'cel.sri.gob.ec'];

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente de consulta',
        'SIN_RESPUESTA' => 'Sin registro en el SRI',
        'EN_PROCESO' => 'En proceso',
        'ERROR' => 'Consulta fallida',
        'NO_AUTORIZADO' => 'No autorizado',
        'AUTORIZADO' => 'Autorizado',
    ];

    public function __construct(private EmpresaActiva $empresa) {}

    /** @return array{clave: string, ambiente: string}|null */
    public function identidad(?string $datos): ?array
    {
        $fiscal = json_decode($datos ?? '{}', true);
        $clave = (string) ($fiscal['clave_acceso'] ?? '');
        $ambiente = (string) ($fiscal['ambiente'] ?? '');
        if (! isset(self::SERVICIOS[$ambiente]) || ! preg_match('/\A[0-9]{49}\z/', $clave) || $clave[23] !== $ambiente) {
            return null;
        }

        return ['clave' => $clave, 'ambiente' => $ambiente];
    }

    public function consultas(int $id): Builder
    {
        return DB::table('com_sri_consultas')->where('id_empresa', $this->empresa->id())->where('id_documento', $id);
    }

    public function autorizado(int $id): ?stdClass
    {
        return $this->consultas($id)->where('estado', 'AUTORIZADO')->whereNotNull('xml_autorizado')->latest('id')->first();
    }

    public function consultar(int $id, string $clave): string
    {
        if ($this->autorizado($id) !== null) {
            return 'AUTORIZADO';
        }

        $resultado = $this->buscar($clave);

        return $this->empresa->transaccion(function () use ($id, $resultado): string {
            if ($this->autorizado($id) !== null) {
                return 'AUTORIZADO';
            }
            DB::table('com_sri_consultas')->insert([
                'id_empresa' => $this->empresa->id(), 'id_documento' => $id, 'id_usuario' => auth()->id(),
                'estado' => $resultado['estado'], 'mensajes' => json_encode($resultado['mensajes'], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE),
                'xml_autorizado' => $resultado['xml'] ?? null, 'created_at' => now(),
            ]);

            return $resultado['estado'];
        });
    }

    /** @return array{estado: string, mensajes: list<string>, xml?: string} */
    public function buscar(string $clave): array
    {
        if (! preg_match('/\A[0-9]{49}\z/', $clave) || ! isset(self::SERVICIOS[$clave[23]])) {
            return ['estado' => 'ERROR', 'mensajes' => ['La clave de acceso debe tener 49 dígitos y un ambiente SRI válido.']];
        }
        $ambiente = $clave[23];
        $consulta = <<<XML
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sri="http://ec.gob.sri.ws.autorizacion">
              <soap:Body><sri:autorizacionComprobante><claveAccesoComprobante>{$clave}</claveAccesoComprobante></sri:autorizacionComprobante></soap:Body>
            </soap:Envelope>
            XML;
        $resultado = ['estado' => 'ERROR', 'mensajes' => ['No se pudo consultar el servicio de autorización del SRI.']];
        try {
            $respuesta = Http::connectTimeout(4)->timeout(12)->withoutRedirecting()
                ->withHeader('SOAPAction', '""')->withBody($consulta, 'text/xml; charset=UTF-8')
                ->post('https://'.self::SERVICIOS[$ambiente].'/comprobantes-electronicos-ws/AutorizacionComprobantesOffline');
            if ($respuesta->successful()) {
                $resultado = $this->interpretar($respuesta->body(), $clave, $ambiente);
            } else {
                $resultado['mensajes'] = ['El servicio de autorización del SRI devolvió HTTP '.$respuesta->status().'.'];
            }
        } catch (ConnectionException $error) {
            preg_match('/\AcURL error (\d+):/', $error->getMessage(), $detalle);
            $codigo = (int) ($detalle[1] ?? 0);
            $resultado['mensajes'] = [match ($codigo) {
                6 => 'El servidor de Aitrol no pudo resolver la dirección del SRI. Revisa la conexión DNS del servidor.',
                28 => 'El servicio de autorización del SRI tardó demasiado en responder. Intenta nuevamente.',
                default => 'No se pudo establecer conexión con el servicio de autorización del SRI.',
            }];
        }

        return $resultado;
    }

    public function recibir(string $xml, string $ambiente): void
    {
        $servidor = self::SERVICIOS[$ambiente] ?? throw ValidationException::withMessages(['sri' => 'El ambiente SRI del comprobante es inválido.']);
        $contenido = base64_encode($xml);
        $soap = <<<XML
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sri="http://ec.gob.sri.ws.recepcion">
              <soap:Body><sri:validarComprobante><xml>{$contenido}</xml></sri:validarComprobante></soap:Body>
            </soap:Envelope>
            XML;
        try {
            $respuesta = Http::connectTimeout(4)->timeout(12)->withoutRedirecting()
                ->withHeader('SOAPAction', '""')->withBody($soap, 'text/xml; charset=UTF-8')
                ->post('https://'.$servidor.'/comprobantes-electronicos-ws/RecepcionComprobantesOffline');
        } catch (ConnectionException) {
            throw ValidationException::withMessages(['sri' => 'El SRI no confirmó la recepción. Puedes volver a consultar con la misma clave.']);
        }
        $recepcion = $respuesta->successful() ? (self::leerXml($respuesta->body())?->xpath('//*[local-name()="RespuestaRecepcionComprobante"]')[0] ?? null) : null;
        if ((string) $recepcion?->estado === 'RECIBIDA') {
            return;
        }
        $mensajes = [];
        foreach ($recepcion?->xpath('.//mensajes/mensaje') ?? [] as $mensaje) {
            $mensajes[] = Str::limit(trim((string) $mensaje->identificador.' '.$mensaje->mensaje.' '.$mensaje->informacionAdicional), 400);
        }

        throw ValidationException::withMessages(['sri' => $mensajes ?: ['El SRI no confirmó la recepción del XML firmado.']]);
    }

    /** @return array{estado: string, mensajes: list<string>, xml?: string} */
    private function interpretar(string $cuerpo, string $clave, string $ambiente): array
    {
        $invalida = ['estado' => 'ERROR', 'mensajes' => ['La respuesta del SRI no contiene una autorización verificable para esta clave.']];
        $respuesta = self::leerXml($cuerpo)?->xpath('//*[local-name()="RespuestaAutorizacionComprobante"]')[0] ?? null;
        if ($respuesta === null || trim((string) $respuesta->claveAccesoConsultada) !== $clave) {
            return $invalida;
        }
        if (trim((string) $respuesta->numeroComprobantes) === '0') {
            return ['estado' => 'SIN_RESPUESTA', 'mensajes' => ['El SRI todavía no devuelve un resultado para la clave consultada.']];
        }

        $resultado = $invalida;
        foreach ($respuesta->autorizaciones->autorizacion ?? [] as $autorizacion) {
            $estado = str_replace(' ', '_', trim((string) $autorizacion->estado));
            if ($estado === 'RECHAZADO') {
                $estado = 'NO_AUTORIZADO';
            }
            $mensajes = [];
            foreach ($autorizacion->mensajes->mensaje ?? [] as $mensaje) {
                $texto = trim(implode(' ', [(string) $mensaje->identificador, (string) $mensaje->mensaje, (string) $mensaje->informacionAdicional]));
                $mensajes[] = Str::limit($texto, 600);
            }
            if ($estado === 'AUTORIZADO') {
                $documento = self::leerXml((string) $autorizacion->comprobante);
                $raiz = match (substr($clave, 8, 2)) {
                    '01' => 'factura',
                    '07' => 'comprobanteRetencion',
                    default => null,
                };
                if ($raiz === null || $documento?->getName() !== $raiz
                    || (string) $documento->infoTributaria->claveAcceso !== $clave
                    || (string) $documento->infoTributaria->ambiente !== $ambiente
                    || trim((string) $autorizacion->numeroAutorizacion) !== $clave
                    || trim((string) $autorizacion->fechaAutorizacion) === '') {
                    continue;
                }
                $xml = new DOMDocument('1.0', 'UTF-8');
                $xml->appendChild($xml->importNode(dom_import_simplexml($autorizacion), true));

                return ['estado' => $estado, 'mensajes' => $mensajes, 'xml' => $xml->saveXML()];
            }
            if (in_array($estado, ['NO_AUTORIZADO', 'EN_PROCESO'], true)) {
                $resultado = ['estado' => $estado, 'mensajes' => $mensajes];
            }
        }

        return $resultado;
    }

    public static function leerXml(string $xml): ?SimpleXMLElement
    {
        if ($xml === '' || strlen($xml) > 2_000_000 || stripos($xml, '<!DOCTYPE') !== false || str_contains($xml, "\0")) {
            return null;
        }

        $resultado = @simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET);

        return $resultado === false ? null : $resultado;
    }

    public static function digitoVerificador(string $base): int
    {
        $suma = 0;
        for ($i = 47; $i >= 0; $i--) {
            $suma += (int) $base[$i] * (2 + (47 - $i) % 6);
        }

        return match ($verificador = 11 - $suma % 11) {
            11 => 0,
            10 => 1,
            default => $verificador,
        };
    }
}
