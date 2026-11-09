<?php

namespace Sis_medico\Services\Sri;

use DOMDocument;
use RuntimeException;

class XadesBesSigner
{
    const DS = 'http://www.w3.org/2000/09/xmldsig#';
    const XADES = 'http://uri.etsi.org/01903/v1.3.2#';

    public function sign($xml, $p12Path, $password)
    {
        if (!is_readable($p12Path)) throw new RuntimeException('No se puede leer la firma electrónica.');
        $certs = [];
        if (!openssl_pkcs12_read(file_get_contents($p12Path), $certs, $password)) {
            throw new RuntimeException('No se pudo abrir la firma electrónica; revise su contraseña.');
        }
        $certificate = openssl_x509_parse($certs['cert']);
        if (!$certificate) throw new RuntimeException('El certificado de firma no es válido.');
        if ($certificate['validFrom_time_t'] > time()) throw new RuntimeException('La firma electrónica todavía no está vigente.');
        if ($certificate['validTo_time_t'] < time()) throw new RuntimeException('La firma electrónica está vencida.');
        if (!openssl_x509_check_private_key($certs['cert'], $certs['pkey'])) {
            throw new RuntimeException('La clave privada no corresponde al certificado de firma.');
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        if (!$doc->loadXML($xml, LIBXML_NOBLANKS)) throw new RuntimeException('XML inválido para firmar.');
        if ($doc->documentElement->getAttribute('id') !== 'comprobante') {
            throw new RuntimeException('El XML no contiene el nodo id="comprobante" requerido por el SRI.');
        }
        $suffix = bin2hex(random_bytes(8));
        $signatureId = 'Signature-'.$suffix;
        $propertiesId = 'SignedProperties-'.$suffix;
        $keyInfoId = 'KeyInfo-'.$suffix;
        $documentReferenceId = 'Reference-'.$suffix;
        $doc->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', self::DS);
        $signature = $doc->createElementNS(self::DS, 'ds:Signature');
        $signature->setAttribute('Id', $signatureId);
        $signature->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xades', self::XADES);
        $signedInfo = $signature->appendChild($doc->createElementNS(self::DS, 'ds:SignedInfo'));
        $canonical = $signedInfo->appendChild($doc->createElementNS(self::DS, 'ds:CanonicalizationMethod'));
        $canonical->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $method = $signedInfo->appendChild($doc->createElementNS(self::DS, 'ds:SignatureMethod'));
        $method->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $object = $signature->appendChild($doc->createElementNS(self::DS, 'ds:Object'));
        $qualifying = $object->appendChild($doc->createElementNS(self::XADES, 'xades:QualifyingProperties'));
        // Declarar XAdES también en este nodo, igual que el XML aceptado por
        // el SRI; algunos conversores Java no resuelven bien solo la herencia
        // del namespace declarado en ds:Signature.
        $qualifying->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xades', self::XADES);
        $qualifying->setAttribute('Target', '#'.$signatureId);
        $properties = $qualifying->appendChild($doc->createElementNS(self::XADES, 'xades:SignedProperties'));
        $properties->setAttribute('Id', $propertiesId);
        $signatureProps = $properties->appendChild($doc->createElementNS(self::XADES, 'xades:SignedSignatureProperties'));
        $signatureProps->appendChild($doc->createElementNS(self::XADES, 'xades:SigningTime', gmdate('Y-m-d\TH:i:s\Z')));
        $signingCertificate = $signatureProps->appendChild($doc->createElementNS(self::XADES, 'xades:SigningCertificate'));
        $cert = $signingCertificate->appendChild($doc->createElementNS(self::XADES, 'xades:Cert'));
        $certDigest = $cert->appendChild($doc->createElementNS(self::XADES, 'xades:CertDigest'));
        $digestMethod = $certDigest->appendChild($doc->createElementNS(self::DS, 'ds:DigestMethod'));
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $rawCert = base64_decode($this->cleanCertificate($certs['cert']));
        $certDigest->appendChild($doc->createElementNS(self::DS, 'ds:DigestValue', base64_encode(sha1($rawCert, true))));
        $issuerSerial = $cert->appendChild($doc->createElementNS(self::XADES, 'xades:IssuerSerial'));
        // X509IssuerName es el emisor del certificado, no el titular. Usar
        // certificate['name'] aqui coloca al contribuyente como su propio
        // emisor y el conversor XAdES del SRI rechaza el documento.
        $issuerSerial->appendChild($doc->createElementNS(self::DS, 'ds:X509IssuerName', $this->certificateIssuerName($certs['cert'])));
        $issuerSerial->appendChild($doc->createElementNS(self::DS, 'ds:X509SerialNumber', $this->certificateSerialNumber($certificate)));
        $dataProperties = $properties->appendChild($doc->createElementNS(self::XADES, 'xades:SignedDataObjectProperties'));
        $dataFormat = $dataProperties->appendChild($doc->createElementNS(self::XADES, 'xades:DataObjectFormat'));
        $dataFormat->setAttribute('ObjectReference', '#'.$documentReferenceId);
        $dataFormat->appendChild($doc->createElementNS(self::XADES, 'xades:MimeType', 'text/xml'));

        $keyInfo = $doc->createElementNS(self::DS, 'ds:KeyInfo');
        $keyInfo->setAttribute('Id', $keyInfoId);
        $x509 = $keyInfo->appendChild($doc->createElementNS(self::DS, 'ds:X509Data'));
        $x509->appendChild($doc->createElementNS(self::DS, 'ds:X509Certificate', $this->cleanCertificate($certs['cert'])));

        // La firma debe estar insertada antes de calcular los digest. La
        // canonicalizacion inclusiva incorpora los namespaces heredados.
        $doc->documentElement->appendChild($signature);
        $signatureValue = $doc->createElementNS(self::DS, 'ds:SignatureValue');
        $signatureValue->setAttribute('Id', 'SignatureValue-'.$suffix);
        $signature->insertBefore($signatureValue, $object);
        $signature->insertBefore($keyInfo, $object);

        // XMLDSig referencia estos nodos por separado. Canonicalizarlos en
        // un DOM independiente hace que la firma y la verificacion usen el
        // mismo conjunto de namespaces, sin depender de sus ancestros.
        // El orden y las transformaciones siguen el XML XAdES-BES PHP
        // aceptado por el SRI: comprobante, certificado y SignedProperties.
        $this->reference($doc, $signedInfo, '#comprobante', $this->comprobanteDigest($doc), true, null, $documentReferenceId);
        $this->reference($doc, $signedInfo, '#'.$keyInfoId, $this->nodeDigest($keyInfo));
        $this->reference($doc, $signedInfo, '#'.$propertiesId, $this->nodeDigest($properties), false, 'http://uri.etsi.org/01903#SignedProperties');
        $signedCanonical = $signedInfo->C14N(false, false);
        if (!openssl_sign($signedCanonical, $rawSignature, $certs['pkey'], OPENSSL_ALGO_SHA1)) throw new RuntimeException('OpenSSL no pudo firmar el XML.');
        $signatureValue->nodeValue = base64_encode($rawSignature);
        $signedXml = $doc->saveXML();
        if (!$this->verify($signedXml)) throw new RuntimeException('La verificación criptográfica posterior al firmado falló.');
        return $signedXml;
    }

    public function verify($xml)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        if (!$doc->loadXML($xml, LIBXML_NOBLANKS)) return false;
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('ds', self::DS);
        $xpath->registerNamespace('xades', self::XADES);
        $signature = $xpath->query('//ds:Signature')->item(0);
        $signedInfo = $xpath->query('./ds:SignedInfo', $signature)->item(0);
        $signatureValue = $xpath->query('./ds:SignatureValue', $signature)->item(0);
        $certificate = $xpath->query('./ds:KeyInfo/ds:X509Data/ds:X509Certificate', $signature)->item(0);
        if (!$signature || !$signedInfo || !$signatureValue || !$certificate) return false;

        $pem = "-----BEGIN CERTIFICATE-----\n".chunk_split(trim($certificate->textContent), 64, "\n")."-----END CERTIFICATE-----\n";
        if (openssl_verify($signedInfo->C14N(false, false), base64_decode(trim($signatureValue->textContent)), $pem, OPENSSL_ALGO_SHA1) !== 1) return false;

        foreach ($xpath->query('./ds:Reference', $signedInfo) as $reference) {
            $uri = $reference->getAttribute('URI');
            $digest = $xpath->query('./ds:DigestValue', $reference)->item(0);
            if (!$digest || substr($uri, 0, 1) !== '#') return false;
            $id = substr($uri, 1);
            $target = $xpath->query('//*[@Id="'.$id.'" or @id="'.$id.'"]')->item(0);
            if (!$target) return false;
            if ($id === 'comprobante') {
                $copyDocument = clone $doc;
                $copyXPath = new \DOMXPath($copyDocument);
                $copyXPath->registerNamespace('ds', self::DS);
                foreach ($copyXPath->query('//ds:Signature') as $node) {
                    $node->parentNode->removeChild($node);
                }
                $copy = $copyDocument->documentElement;
            } else {
                $copy = $target;
            }
            if (!hash_equals(trim($digest->textContent), base64_encode(sha1($copy->C14N(false, false), true)))) return false;
        }
        return true;
    }

    private function reference($doc, $signedInfo, $uri, $digest, $enveloped = false, $type = null, $id = null)
    {
        $reference = $signedInfo->appendChild($doc->createElementNS(self::DS, 'ds:Reference'));
        $reference->setAttribute('URI', $uri);
        if ($id) $reference->setAttribute('Id', $id);
        if ($type) $reference->setAttribute('Type', $type);
        if ($enveloped) {
            $transforms = $reference->appendChild($doc->createElementNS(self::DS, 'ds:Transforms'));
            $transform = $transforms->appendChild($doc->createElementNS(self::DS, 'ds:Transform'));
            $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
            $canonicalTransform = $transforms->appendChild($doc->createElementNS(self::DS, 'ds:Transform'));
            $canonicalTransform->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        }
        $method = $reference->appendChild($doc->createElementNS(self::DS, 'ds:DigestMethod'));
        $method->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $reference->appendChild($doc->createElementNS(self::DS, 'ds:DigestValue', $digest));
    }

    private function cleanCertificate($certificate)
    {
        return preg_replace('/-----[^-]+-----|\s+/', '', $certificate);
    }

    private function comprobanteDigest(DOMDocument $doc)
    {
        $copyDocument = clone $doc;
        $copyXPath = new \DOMXPath($copyDocument);
        $copyXPath->registerNamespace('ds', self::DS);
        foreach ($copyXPath->query('//ds:Signature') as $node) {
            $node->parentNode->removeChild($node);
        }

        return base64_encode(sha1($copyDocument->documentElement->C14N(false, false), true));
    }

    private function nodeDigest($node)
    {
        return base64_encode(sha1($node->C14N(false, false), true));
    }

    private function certificateIssuerName($certificatePem)
    {
        $command = 'openssl x509 -noout -issuer -nameopt RFC2253';
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $certificatePem);
            fclose($pipes[0]);
            $output = trim(stream_get_contents($pipes[1]));
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
            if ($exitCode === 0 && preg_match('/^issuer\s*=\s*(.+)$/D', $output, $matches) === 1) {
                return trim($matches[1]);
            }
        }

        throw new RuntimeException('OpenSSL no pudo obtener el emisor RFC2253 del certificado de firma.');
    }

    private function certificateSerialNumber(array $certificate)
    {
        $hex = strtoupper(trim((string) ($certificate['serialNumberHex'] ?? '')));
        if ($hex === '' || preg_match('/^[0-9A-F]+$/D', $hex) !== 1) {
            throw new RuntimeException('El certificado no contiene un serial hexadecimal válido.');
        }
        $hex = ltrim($hex, '0');
        if ($hex === '') throw new RuntimeException('El serial X509 debe ser positivo.');
        $decimal = '0';
        foreach (str_split($hex) as $digit) {
            $carry = hexdec($digit);
            $next = '';
            for ($i = strlen($decimal) - 1; $i >= 0; $i--) {
                $value = ((int) $decimal[$i] * 16) + $carry;
                $next = ($value % 10).$next;
                $carry = intdiv($value, 10);
            }
            while ($carry > 0) {
                $next = ($carry % 10).$next;
                $carry = intdiv($carry, 10);
            }
            $decimal = ltrim($next, '0') ?: '0';
        }
        return $decimal;
    }

    private function certificateModulus($certificate)
    {
        $publicKey = openssl_pkey_get_public($certificate);
        $details = $publicKey ? openssl_pkey_get_details($publicKey) : false;
        if (!$details || empty($details['rsa']['n'])) {
            throw new RuntimeException('No se pudo obtener la clave RSA del certificado.');
        }
        return base64_encode($details['rsa']['n']);
    }
}
