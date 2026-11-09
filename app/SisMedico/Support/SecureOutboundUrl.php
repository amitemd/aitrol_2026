<?php

namespace Sis_medico\Support;

use RuntimeException;

class SecureOutboundUrl
{
    /**
     * Rechaza integraciones no configuradas o que intenten transmitir por HTTP.
     */
    public static function requireHttps($url, $service)
    {
        $url = trim((string) $url);

        if ($url === '' || strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
            throw new RuntimeException(
                'La integración '.$service.' está deshabilitada: configure un endpoint HTTPS válido.'
            );
        }

        return $url;
    }
}
