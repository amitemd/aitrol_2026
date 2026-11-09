<?php

namespace Sis_medico\Services\Sri;

use RuntimeException;
use SoapClient;

class SriGateway
{
    public function receive($signedXml)
    {
        $client = $this->client(config('sri.production.reception'));
        // El WSDL declara xml como xs:base64Binary: SoapClient ya lo codifica.
        // Pasar base64_encode($signedXml) lo codifica DOS veces y el SRI
        // recibe texto Base64 en vez del XML firmado (error 35, clave N/A).
        // No reserializar tampoco: deben conservarse los bytes de la firma.
        $response = $client->__soapCall('validarComprobante', [['xml' => $signedXml]]);
        return json_decode(json_encode($response), true);
    }

    public function authorize($accessKey)
    {
        $client = $this->client(config('sri.production.authorization'));
        $response = $client->__soapCall('autorizacionComprobante', [['claveAccesoComprobante' => $accessKey]]);
        return json_decode(json_encode($response), true);
    }

    protected function client($wsdl)
    {
        try {
            return new SoapClient($wsdl, ['exceptions' => true, 'trace' => false, 'connection_timeout' => 15, 'cache_wsdl' => WSDL_CACHE_BOTH]);
        } catch (\Exception $exception) {
            throw new RuntimeException('No fue posible conectar con el servicio web del SRI: '.$exception->getMessage(), 0, $exception);
        }
    }
}
