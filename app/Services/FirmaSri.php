<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class FirmaSri
{
    private const DS = 'http://www.w3.org/2000/09/xmldsig#';
    private const XADES = 'http://uri.etsi.org/01903/v1.3.2#';

    public function __construct(private EmpresaActiva $empresa) {}

    public function firmar(string $xml): string
    {
        $cifrado = DB::table('erp_identidad_empresas')->where('id_empresa', $this->empresa->id())->value('certificado_cifrado');
        if (! $cifrado) {
            $this->rechazar('Configura el certificado PKCS#12 vigente en la empresa antes de firmar.');
        }
        try {
            $credencial = json_decode(Crypt::decryptString($cifrado), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            $this->rechazar('Vuelve a configurar el certificado de la empresa. No se pudo abrir.');
        }
        if (! @openssl_pkcs12_read((string) base64_decode($credencial['pkcs12'] ?? '', true), $llaves, $credencial['clave'] ?? '')) {
            $this->rechazar('Vuelve a configurar el certificado de la empresa. No se pudo abrir.');
        }
        $certificado = openssl_x509_parse($llaves['cert']);
        $llavePublica = openssl_pkey_get_public($llaves['cert']);
        $rsa = $llavePublica === false ? null : (openssl_pkey_get_details($llavePublica)['rsa'] ?? null);
        if ($certificado === false || ! $rsa || $certificado['validFrom_time_t'] > now()->timestamp
            || $certificado['validTo_time_t'] <= now()->timestamp || ! ctype_digit((string) $certificado['serialNumber'])) {
            $this->rechazar('La firma requiere un certificado RSA vigente con número de serie válido.');
        }
        $der = preg_replace('/-----[^-]+-----|\s+/', '', $llaves['cert']);
        $doc = new DOMDocument('1.0', 'UTF-8');
        if (AutorizacionSri::leerXml($xml)?->getName() !== 'comprobanteRetencion' || ! $doc->loadXML($xml, LIBXML_NONET)) {
            $this->rechazar('El XML de la retención no es válido.');
        }
        $raiz = $doc->documentElement;
        $digestDocumento = $this->digest($raiz->C14N());
        $firma = $this->nodo($raiz, self::DS, 'ds:Signature');
        $firma->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:etsi', self::XADES);
        $firma->setAttribute('Id', 'FirmaComprobante');
        $info = $this->nodo($firma, self::DS, 'ds:SignedInfo');
        $this->nodo($info, self::DS, 'ds:CanonicalizationMethod')->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $this->nodo($info, self::DS, 'ds:SignatureMethod')->setAttribute('Algorithm', self::DS.'rsa-sha1');
        $valorFirma = $this->nodo($firma, self::DS, 'ds:SignatureValue');
        $infoClave = $this->nodo($firma, self::DS, 'ds:KeyInfo');
        $infoClave->setAttribute('Id', 'CertificadoComprobante');
        $this->nodo($this->nodo($infoClave, self::DS, 'ds:X509Data'), self::DS, 'ds:X509Certificate', $der);
        $rsaNodo = $this->nodo($this->nodo($infoClave, self::DS, 'ds:KeyValue'), self::DS, 'ds:RSAKeyValue');
        $this->nodo($rsaNodo, self::DS, 'ds:Modulus', base64_encode($rsa['n']));
        $this->nodo($rsaNodo, self::DS, 'ds:Exponent', base64_encode($rsa['e']));

        $cualificadas = $this->nodo($this->nodo($firma, self::DS, 'ds:Object'), self::XADES, 'etsi:QualifyingProperties');
        $cualificadas->setAttribute('Target', '#FirmaComprobante');
        $propiedades = $this->nodo($cualificadas, self::XADES, 'etsi:SignedProperties');
        $propiedades->setAttribute('Id', 'PropiedadesComprobante');
        $firmaProp = $this->nodo($propiedades, self::XADES, 'etsi:SignedSignatureProperties');
        $this->nodo($firmaProp, self::XADES, 'etsi:SigningTime', now('America/Guayaquil')->toIso8601String());
        $cert = $this->nodo($this->nodo($firmaProp, self::XADES, 'etsi:SigningCertificate'), self::XADES, 'etsi:Cert');
        $this->agregarDigest($this->nodo($cert, self::XADES, 'etsi:CertDigest'), $this->digest((string) base64_decode($der, true)));
        $emisor = $this->nodo($cert, self::XADES, 'etsi:IssuerSerial');
        $atributos = [];
        foreach (array_reverse($certificado['issuer'], preserve_keys: true) as $clave => $valores) {
            foreach ((array) $valores as $valor) {
                $atributos[] = $clave.'='.addcslashes($valor, ',+"\\<>;');
            }
        }
        $this->nodo($emisor, self::DS, 'ds:X509IssuerName', implode(',', $atributos));
        $this->nodo($emisor, self::DS, 'ds:X509SerialNumber', (string) $certificado['serialNumber']);
        $formato = $this->nodo($this->nodo($propiedades, self::XADES, 'etsi:SignedDataObjectProperties'), self::XADES, 'etsi:DataObjectFormat');
        $formato->setAttribute('ObjectReference', '#ReferenciaComprobante');
        $this->nodo($formato, self::XADES, 'etsi:MimeType', 'text/xml');

        $refProp = $this->nodo($info, self::DS, 'ds:Reference');
        $refProp->setAttribute('Type', 'http://uri.etsi.org/01903#SignedProperties');
        $refProp->setAttribute('URI', '#PropiedadesComprobante');
        $this->agregarDigest($refProp, $this->digest($propiedades->C14N()));
        $refCert = $this->nodo($info, self::DS, 'ds:Reference');
        $refCert->setAttribute('URI', '#CertificadoComprobante');
        $this->agregarDigest($refCert, $this->digest($infoClave->C14N()));
        $refDoc = $this->nodo($info, self::DS, 'ds:Reference');
        $refDoc->setAttribute('Id', 'ReferenciaComprobante');
        $refDoc->setAttribute('URI', '#comprobante');
        $transformaciones = $this->nodo($refDoc, self::DS, 'ds:Transforms');
        $this->nodo($transformaciones, self::DS, 'ds:Transform')->setAttribute('Algorithm', self::DS.'enveloped-signature');
        $this->agregarDigest($refDoc, $digestDocumento);

        if (! openssl_sign($info->C14N(), $firmaBinaria, $llaves['pkey'], OPENSSL_ALGO_SHA1)) {
            $this->rechazar('No se pudo firmar el XML con el certificado de la empresa.');
        }
        $valorFirma->textContent = base64_encode($firmaBinaria);

        return $doc->saveXML();
    }

    private function nodo(DOMElement $padre, string $ns, string $nombre, ?string $valor = null): DOMElement
    {
        $doc = $padre->ownerDocument;
        $nodo = $doc->createElementNS($ns, $nombre);
        if ($valor !== null) {
            $nodo->appendChild($doc->createTextNode($valor));
        }
        $padre->appendChild($nodo);

        return $nodo;
    }

    private function digest(string $contenido): string
    {
        return base64_encode(sha1($contenido, true));
    }

    private function agregarDigest(DOMElement $nodo, string $digest): void
    {
        $this->nodo($nodo, self::DS, 'ds:DigestMethod')->setAttribute('Algorithm', self::DS.'sha1');
        $this->nodo($nodo, self::DS, 'ds:DigestValue', $digest);
    }

    private function rechazar(string $mensaje): never
    {
        throw ValidationException::withMessages(['firma' => $mensaje]);
    }
}
