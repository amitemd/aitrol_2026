<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Route;

class SecurityArchitectureAuditService
{
    public function inspect()
    {
        $checks = array(
            $this->check('runtime', 'Runtime soportado',
                version_compare(PHP_VERSION, '8.3.0', '>=') && version_compare(app()->version(), '13.0.0', '>='),
                'PHP '.PHP_VERSION.' · Laravel '.app()->version(), 'fail'),
            $this->check('debug', 'Depuración desactivada',
                !app()->environment('production', 'produccion') || !config('app.debug'),
                config('app.debug') ? 'APP_DEBUG está activo.' : 'APP_DEBUG está desactivado.', 'fail'),
            $this->check('session_secure', 'Cookie de sesión segura',
                !app()->environment('production', 'produccion') || (bool) config('session.secure'),
                config('session.secure') ? 'La cookie exige HTTPS.' : 'SESSION_SECURE_COOKIE no está activo.', 'fail'),
            $this->check('session_http_only', 'Cookie inaccesible para JavaScript',
                (bool) config('session.http_only'),
                config('session.http_only') ? 'HttpOnly está activo.' : 'La cookie de sesión no tiene HttpOnly.', 'fail'),
            $this->check('session_same_site', 'Protección SameSite',
                in_array(strtolower((string) config('session.same_site')), array('lax', 'strict'), true),
                'SameSite='.(string) config('session.same_site'), 'fail'),
            $this->check('trusted_proxies', 'Proxies explícitos',
                $this->trustedProxiesAreExplicit(),
                'No se debe confiar globalmente en encabezados reenviados.', 'fail'),
            $this->check('csrf', 'CSRF sin excepciones heredadas',
                $this->csrfExceptionsAreEmpty(),
                'Las integraciones de máquina deben usar autenticación API, no exclusiones CSRF.', 'fail'),
            $this->check('outbound_allowlist', 'Destinos externos controlados',
                $this->outboundAllowlistIsSecure(),
                'Las salidas modernas exigen HTTPS y una lista explícita de servidores autorizados.', 'fail'),
            $this->check('outbound_tls', 'TLS externo obligatorio',
                !$this->sourceDisablesTlsVerification(),
                'No se permite desactivar la verificación TLS en el código de aplicación.', 'fail'),
            $this->check('api_tokens', 'Autenticación API moderna',
                config('auth.guards.api.driver') !== 'token',
                config('auth.guards.api.driver') === 'token'
                    ? 'El guard API heredado todavía utiliza tokens persistidos; migrar cada integración a tokens hash con capacidades.'
                    : 'El guard API no utiliza el driver token heredado.', 'warn'),
        );

        if (config('ip_restriction.enabled', false)) {
            $checks[] = $this->check('ip_allowlist', 'Lista global de IP cerrada',
                $this->globalIpAllowlistIsRestricted(),
                'IPS_PERMITIDAS debe contener solo IP, CIDR o rangos acotados; nunca redes universales ni loopback en producción.', 'fail');
        }

        $mutableGetRoutes = $this->mutableGetRoutes();
        $checks[] = $this->check('mutable_get', 'GET reservado para lectura',
            count($mutableGetRoutes) === 0,
            count($mutableGetRoutes).' rutas GET con escritura detectada; deben migrarse por módulo a POST, PUT, PATCH o DELETE.',
            'warn');

        $summary = array('pass' => 0, 'warn' => 0, 'fail' => 0);
        foreach ($checks as $check) {
            $summary[$check['status']]++;
        }

        return array(
            'generated_at' => date(DATE_ATOM),
            'summary' => $summary,
            'checks' => $checks,
            'mutable_get_routes' => $mutableGetRoutes,
        );
    }

    private function check($id, $name, $passed, $detail, $failureStatus)
    {
        return array(
            'id' => $id,
            'name' => $name,
            'status' => $passed ? 'pass' : $failureStatus,
            'detail' => $detail,
        );
    }

    private function csrfExceptionsAreEmpty()
    {
        try {
            $middleware = app(\Sis_medico\Http\Middleware\VerifyCsrfToken::class);
            $property = new \ReflectionProperty($middleware, 'except');
            $property->setAccessible(true);
            return count(array_filter((array) $property->getValue($middleware))) === 0;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function trustedProxiesAreExplicit()
    {
        $proxies = config('trustedproxy.proxies', array());
        if (is_string($proxies)) {
            $proxies = array_filter(array_map('trim', explode(',', $proxies)));
        }
        if (!is_array($proxies)) return false;

        foreach ($proxies as $proxy) {
            $proxy = trim((string) $proxy);
            if ($proxy === '*' || $proxy === '**' || $proxy === '') return false;
            if (filter_var($proxy, FILTER_VALIDATE_IP)) continue;
            if (strpos($proxy, '/') === false) return false;
            list($network, $prefix) = array_pad(explode('/', $proxy, 2), 2, null);
            if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit((string) $prefix)) return false;
            $maximumBits = strpos($network, ':') !== false ? 128 : 32;
            if ((int) $prefix < 0 || (int) $prefix > $maximumBits) return false;
        }
        return true;
    }

    private function globalIpAllowlistIsRestricted()
    {
        $rules = config('ip_restriction.allowed_ips', array());
        if (!is_array($rules) || count($rules) === 0) return false;
        $allowLoopback = (bool) config(
            'ip_restriction.allow_loopback',
            !app()->environment('production')
        );
        $networkMatcher = new UserNetworkAccessService;

        foreach ($rules as $rule) {
            $rule = trim((string) $rule);
            if ($rule === '' || in_array($rule, array('*', '**', '0.0.0.0/0', '::/0'), true)) return false;
            if (!$allowLoopback && (
                $networkMatcher->matchesIp('127.0.0.1', $rule)
                || $networkMatcher->matchesIp('::1', $rule)
            )) return false;
            if (filter_var($rule, FILTER_VALIDATE_IP)) continue;

            if (strpos($rule, '/') !== false) {
                list($network, $prefix) = array_pad(explode('/', $rule, 2), 2, null);
                if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit((string) $prefix)
                    || (int) $prefix <= 0 || (int) $prefix > (strpos($network, ':') !== false ? 128 : 32)) return false;
                continue;
            }

            if (strpos($rule, '-') !== false) {
                list($first, $last) = array_map('trim', explode('-', $rule, 2));
                $first = @inet_pton($first); $last = @inet_pton($last);
                if ($first === false || $last === false || strlen($first) !== strlen($last) || strcmp($first, $last) > 0) return false;
                continue;
            }

            return false;
        }
        return true;
    }

    private function outboundAllowlistIsSecure()
    {
        $hosts = (array) config('outbound.allowed_hosts', array());
        if (empty($hosts)) return false;

        foreach ($hosts as $host) {
            $host = trim((string) $host);
            if ($host === '' || filter_var($host, FILTER_VALIDATE_IP)
                || preg_match('/^[a-z0-9.-]+$/i', $host) !== 1) {
                return false;
            }
        }
        return true;
    }

    private function sourceDisablesTlsVerification()
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path()));
        foreach ($iterator as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') continue;
            $source = file_get_contents($file->getPathname());
            if (preg_match('/CURLOPT_SSL_VERIFY(?:PEER|HOST)\s*=>\s*(?:false|0)|[\'\"]verify(?:_peer|_peer_name)[\'\"]\s*=>\s*false/i', $source) === 1) {
                return true;
            }
        }
        return false;
    }

    private function mutableGetRoutes()
    {
        $items = array();
        foreach (Route::getRoutes() as $route) {
            if (!in_array('GET', $route->methods(), true)) {
                continue;
            }

            $action = $route->getActionName();
            $label = $route->uri().' '.$action;
            if (preg_match('/(guardar|save|store|update|actualiz|delete|destroy|elimin|anular|borrar|quitar|toggle|cambiar|liberar|registrar|procesar|confirmar)/i', $label) !== 1) {
                continue;
            }
            if (!$this->actionWrites($action)) {
                continue;
            }

            $items[] = array(
                'method' => 'GET',
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $action,
            );
        }

        usort($items, function ($left, $right) {
            return strcmp($left['uri'], $right['uri']);
        });
        return $items;
    }

    private function actionWrites($action)
    {
        if (strpos($action, '@') === false) {
            return false;
        }

        list($class, $method) = explode('@', $action, 2);
        try {
            $reflection = new \ReflectionMethod($class, $method);
            $lines = file($reflection->getFileName());
            $source = implode('', array_slice($lines, $reflection->getStartLine() - 1,
                $reflection->getEndLine() - $reflection->getStartLine() + 1));
            return preg_match('/(::create\s*\(|::insert|firstOrCreate|updateOrCreate|insertGetId|->save\s*\(|->update\s*\(|->delete\s*\(|DB::(?:insert|update|delete|statement)|Storage::(?:put|delete|move)|->store\s*\(|->move\s*\(|unlink\s*\()/i', $source) === 1;
        } catch (\Throwable $exception) {
            return preg_match('/(guardar|actualiz|update|delete|elimin|anular|crear|store|save)/i', $action) === 1;
        }
    }
}
