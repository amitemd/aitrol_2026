<?php

namespace Sis_medico\Services\Security;

use RuntimeException;

class SecureOutboundHttpClient
{
    public function request(string $method, string $url, array $headers = [], string $body = ''): array
    {
        $this->assertSafeUrl($url);
        if (!function_exists('curl_init')) {
            throw new RuntimeException('La extensión cURL es necesaria para la conexión externa.');
        }

        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            throw new RuntimeException('Método HTTP externo no permitido.');
        }

        $curlHeaders = ['Accept: application/json'];
        foreach ($headers as $name => $value) {
            if (!preg_match('/^[A-Za-z0-9-]+$/', (string) $name)
                || preg_match('/[\r\n]/', (string) $value)) {
                throw new RuntimeException('La solicitud externa contiene una cabecera inválida.');
            }
            $curlHeaders[] = $name.': '.$value;
        }

        $responseBody = '';
        $responseHeaders = [];
        $maxBytes = max(1024, (int) config('outbound.max_response_bytes', 5242880));
        $handle = curl_init($url);
        if ($handle === false) {
            throw new RuntimeException('No se pudo iniciar la conexión externa.');
        }

        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => max(1, (int) config('outbound.connect_timeout', 5)),
            CURLOPT_TIMEOUT => max(1, (int) config('outbound.timeout', 20)),
            CURLOPT_USERAGENT => 'Aitrol-Hospital/13 outbound-client',
            CURLOPT_HEADERFUNCTION => function ($curl, $line) use (&$responseHeaders) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return strlen($line);
            },
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use (&$responseBody, $maxBytes) {
                if (strlen($responseBody) + strlen($chunk) > $maxBytes) {
                    return 0;
                }
                $responseBody .= $chunk;
                return strlen($chunk);
            },
        ];
        if (defined('CURLOPT_SSLVERSION') && defined('CURL_SSLVERSION_TLSv1_2')) {
            $options[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;
        }
        if ($body !== '') {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        try {
            curl_setopt_array($handle, $options);
            if (curl_exec($handle) === false) {
                throw new RuntimeException('No se pudo completar la conexión externa segura.');
            }

            return [
                'status' => (int) curl_getinfo($handle, CURLINFO_HTTP_CODE),
                'body' => $responseBody,
                'headers' => $responseHeaders,
            ];
        } finally {
            curl_close($handle);
        }
    }

    public function assertSafeUrl(string $url): void
    {
        $parts = parse_url($url);
        $host = is_array($parts) ? strtolower((string) ($parts['host'] ?? '')) : '';
        $allowed = array_map('strtolower', (array) config('outbound.allowed_hosts', []));

        if (!is_array($parts)
            || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || $host === ''
            || filter_var($host, FILTER_VALIDATE_IP)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])
            || (isset($parts['port']) && (int) $parts['port'] !== 443)
            || !in_array($host, $allowed, true)) {
            throw new RuntimeException('El destino externo no está autorizado.');
        }
    }
}
