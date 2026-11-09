<?php

namespace Sis_medico\Services;

class ClinicalTransportAuditService
{
    public function inspect()
    {
        $issues = array();
        if (!config('clinical.force_https')) $issues[] = 'CLINICAL_FORCE_HTTPS está desactivado.';
        if ((int) config('clinical.hsts_max_age', 0) <= 0) $issues[] = 'CLINICAL_HSTS_MAX_AGE debe ser mayor que cero.';
        if (!config('session.secure')) $issues[] = 'La cookie de sesión no exige HTTPS.';
        if (!config('session.http_only')) $issues[] = 'La cookie de sesión no es HttpOnly.';
        if (config('app.debug')) $issues[] = 'APP_DEBUG debe estar desactivado en el entorno clínico.';

        $sameSite = strtolower((string) config('session.same_site'));
        if (!in_array($sameSite, array('lax', 'strict'), true)) {
            $issues[] = 'SESSION_SAME_SITE debe ser lax o strict para el sistema clínico.';
        }

        $url = (string) config('app.url');
        $parts = parse_url($url);
        if (stripos($url, 'https://') !== 0) $issues[] = 'APP_URL no utiliza https://.';
        if (!$parts || empty($parts['host'])) $issues[] = 'APP_URL no contiene un dominio válido.';
        $proxies = config('trustedproxy.proxies');
        if ($proxies === '*') $issues[] = 'TRUSTED_PROXIES no puede aceptar cualquier proxy en producción.';
        // Un Apache que termina TLS y ejecuta PHP directamente no necesita
        // confiar en encabezados reenviados. Solo auditamos X-Forwarded-Proto
        // cuando la instalación declara proxies distintos del loopback.
        $proxyList = is_array($proxies) ? $proxies : array_filter(array_map('trim', explode(',', (string) $proxies)));
        $externalProxies = array_diff($proxyList, array('127.0.0.1', '::1'));
        if (!empty($externalProxies)) {
            $headers = (array) config('trustedproxy.headers', array());
            if (!in_array('X_FORWARDED_PROTO', $headers, true)) {
                $issues[] = 'El proxy configurado no declara X_FORWARDED_PROTO; Laravel puede confundir HTTPS con HTTP.';
            }
        }

        $middleware = @file_get_contents(app_path('Http/Kernel.php'));
        $secureMiddleware = @file_get_contents(app_path('Http/Middleware/EnforceSecureClinicalTransport.php'));
        if (strpos((string) $middleware, 'EnforceSecureClinicalTransport::class') === false) {
            $issues[] = 'El middleware de transporte seguro no está registrado.';
        }
        foreach (array('Strict-Transport-Security', 'X-Content-Type-Options', 'Referrer-Policy', 'Content-Security-Policy', 'Cache-Control') as $header) {
            if (strpos((string) $secureMiddleware, $header) === false) $issues[] = 'Falta la cabecera segura ' . $header . '.';
        }
        if (strpos((string) $secureMiddleware, "config('app.url')") === false || strpos((string) $secureMiddleware, 'getHttpHost()') !== false) {
            $issues[] = 'La redirección HTTPS no utiliza exclusivamente el dominio canónico configurado.';
        }

        return array('pending' => count($issues), 'issues' => $issues);
    }

    /**
     * Verifica el certificado que realmente entrega el dominio público. Esta
     * comprobación queda fuera de inspect() para no añadir red a cada carga de
     * la matriz; el comando clínico sí la ejecuta expresamente.
     */
    public function inspectLiveCertificate()
    {
        $issues = array();
        $details = array();
        $url = (string) config('app.url');
        $host = parse_url($url, PHP_URL_HOST);
        $port = (int) (parse_url($url, PHP_URL_PORT) ?: 443);

        if (!$host || stripos($url, 'https://') !== 0) {
            return array('pending' => 1, 'issues' => array('No existe un dominio HTTPS válido para inspeccionar.'), 'details' => $details);
        }

        $cryptoMethod = defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')
            ? constant('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')
            : STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
            $cryptoMethod |= constant('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT');
        }
        $context = stream_context_create(array('ssl' => array(
            'capture_peer_cert' => true,
            'verify_peer' => true,
            'verify_peer_name' => true,
            'peer_name' => $host,
            'SNI_enabled' => true,
            'disable_compression' => true,
            'crypto_method' => $cryptoMethod,
        )));
        $errorNumber = 0;
        $errorMessage = '';
        $socket = @stream_socket_client(
            'ssl://' . $host . ':' . $port,
            $errorNumber,
            $errorMessage,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $issues[] = 'No fue posible validar el certificado servido por ' . $host . ': ' . trim($errorMessage);
            return array('pending' => count($issues), 'issues' => $issues, 'details' => $details);
        }

        $parameters = stream_context_get_params($socket);
        $metadata = stream_get_meta_data($socket);
        fclose($socket);
        $certificate = isset($parameters['options']['ssl']['peer_certificate'])
            ? $parameters['options']['ssl']['peer_certificate']
            : null;
        $parsed = $certificate ? @openssl_x509_parse($certificate) : false;

        if (!$parsed || empty($parsed['validFrom_time_t']) || empty($parsed['validTo_time_t'])) {
            $issues[] = 'El servidor no entregó un certificado X.509 interpretable.';
            return array('pending' => count($issues), 'issues' => $issues, 'details' => $details);
        }

        $now = time();
        $remainingDays = (int) floor(($parsed['validTo_time_t'] - $now) / 86400);
        $warningDays = (int) config('clinical.certificate_warning_days', 21);
        if ($parsed['validFrom_time_t'] > $now) $issues[] = 'El certificado todavía no es válido.';
        if ($parsed['validTo_time_t'] <= $now) $issues[] = 'El certificado público está vencido.';
        elseif ($remainingDays < $warningDays) $issues[] = 'El certificado vence en ' . $remainingDays . ' día(s), por debajo del umbral de ' . $warningDays . '.';

        $protocol = isset($metadata['crypto']['protocol']) ? (string) $metadata['crypto']['protocol'] : 'no informado';
        if ($protocol !== 'no informado' && stripos($protocol, 'TLSv1.2') === false && stripos($protocol, 'TLSv1.3') === false) {
            $issues[] = 'El servidor negoció un protocolo no permitido: ' . $protocol . '.';
        }

        $details = array(
            'domain' => $host,
            'protocol' => $protocol,
            'issuer' => isset($parsed['issuer']['CN']) ? $parsed['issuer']['CN'] : 'no informado',
            'valid_from' => date('c', $parsed['validFrom_time_t']),
            'expires_at' => date('c', $parsed['validTo_time_t']),
            'remaining_days' => $remainingDays,
            'fingerprint_sha256' => $certificate ? openssl_x509_fingerprint($certificate, 'sha256') : null,
        );

        return array('pending' => count($issues), 'issues' => $issues, 'details' => $details);
    }

    public function storeLiveSnapshot(array $result)
    {
        $directory = storage_path('app/clinical');
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new \RuntimeException('No fue posible crear el directorio de evidencia de transporte.');
        }
        $payload = array(
            'checked_at' => date('c'),
            'pending' => (int) (isset($result['pending']) ? $result['pending'] : 1),
            'issues' => isset($result['issues']) ? array_values($result['issues']) : array(),
            'details' => isset($result['details']) ? $result['details'] : array(),
        );
        $canonical = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($canonical === false) throw new \RuntimeException('No fue posible serializar la evidencia de transporte.');
        $payload['integrity_hash'] = hash('sha256', $canonical);
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $path = $directory . DIRECTORY_SEPARATOR . 'transport-audit.json';
        $temporary = $path . '.' . getmypid() . '.tmp';
        if (file_put_contents($temporary, $json . PHP_EOL, LOCK_EX) === false || !rename($temporary, $path)) {
            @unlink($temporary);
            throw new \RuntimeException('No fue posible guardar atómicamente la evidencia de transporte.');
        }

        return $payload;
    }

    public function latestLiveSnapshot($freshHours = 24)
    {
        $path = storage_path('app/clinical/transport-audit.json');
        if (!is_file($path) || !is_readable($path)) return null;
        $payload = json_decode((string) file_get_contents($path), true);
        if (!is_array($payload) || empty($payload['integrity_hash'])) return null;
        $hash = $payload['integrity_hash'];
        unset($payload['integrity_hash']);
        $canonical = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($canonical === false || !hash_equals($hash, hash('sha256', $canonical))) return null;
        $checkedAt = isset($payload['checked_at']) ? strtotime($payload['checked_at']) : false;
        $payload['integrity_hash'] = $hash;
        $payload['fresh'] = $checkedAt && (time() - $checkedAt) <= ((int) $freshHours * 3600);

        return $payload;
    }
}
