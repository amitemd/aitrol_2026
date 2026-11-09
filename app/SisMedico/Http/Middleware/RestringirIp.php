<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RestringirIp
{
    /**
     * Restringe el acceso a las IP configuradas para toda la aplicacion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('ip_restriction.enabled', false)) {
            return $next($request);
        }

        $ipsAutorizadas = config('ip_restriction.allowed_ips', array());
        $clientIp = $request->ip();
        $loopbackDenied = !config(
            'ip_restriction.allow_loopback',
            !app()->environment('production')
        ) && $this->isLoopbackIp($clientIp);

        if (!is_array($ipsAutorizadas)
            || $loopbackDenied
            || !$this->isAllowedIp($clientIp, $ipsAutorizadas)
        ) {
            if (config('ip_restriction.audit_denied', true)) {
                Log::warning('Global IP allowlist denied request.', array(
                    'method' => substr((string) $request->method(), 0, 10),
                    'path' => substr('/'.ltrim((string) $request->path(), '/'), 0, 500),
                    'client_ip' => substr((string) $clientIp, 0, 45),
                    'remote_address' => substr((string) $request->server->get('REMOTE_ADDR'), 0, 45),
                    'trusted_proxy' => $request->isFromTrustedProxy(),
                    'forwarded_for' => substr((string) $request->header('X-Forwarded-For', ''), 0, 255),
                    'user_agent' => substr((string) $request->header('User-Agent', ''), 0, 255),
                    'reason' => $loopbackDenied
                        ? 'loopback_not_allowed_in_production'
                        : 'outside_global_allowlist',
                ));
            }

            // Usa exactamente el mismo flujo que cualquier ruta inexistente.
            // La respuesta no revela que existe una restriccion por IP.
            abort(404);
        }

        return $next($request);
    }

    private function isLoopbackIp($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $packed = @inet_pton($ip);
        if ($packed === false) {
            return false;
        }

        return strlen($packed) === 4
            ? ord($packed[0]) === 127
            : $packed === inet_pton('::1');
    }

    private function isAllowedIp($clientIp, array $rules)
    {
        if (!filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return false;
        }

        foreach ($rules as $rule) {
            $rule = trim((string) $rule);
            if ($rule === '') {
                continue;
            }

            if ($clientIp === $rule
                || (strpos($rule, '/') !== false && $this->matchesCidr($clientIp, $rule))
                || (strpos($rule, '-') !== false && $this->matchesRange($clientIp, $rule))
            ) {
                return true;
            }
        }

        return false;
    }

    private function matchesCidr($clientIp, $rule)
    {
        $parts = explode('/', $rule, 2);
        $network = isset($parts[0]) ? trim($parts[0]) : '';
        $prefix = isset($parts[1]) ? trim($parts[1]) : '';

        if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit($prefix)) {
            return false;
        }

        $packedIp = @inet_pton($clientIp);
        $packedNetwork = @inet_pton($network);
        if ($packedIp === false
            || $packedNetwork === false
            || strlen($packedIp) !== strlen($packedNetwork)
        ) {
            return false;
        }

        $prefix = (int) $prefix;
        $maximumBits = strlen($packedIp) * 8;
        if ($prefix < 0 || $prefix > $maximumBits) {
            return false;
        }

        $completeBytes = (int) floor($prefix / 8);
        if ($completeBytes > 0
            && substr($packedIp, 0, $completeBytes) !== substr($packedNetwork, 0, $completeBytes)
        ) {
            return false;
        }

        $remainingBits = $prefix % 8;
        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainingBits)) & 0xFF;
        return (ord($packedIp[$completeBytes]) & $mask)
            === (ord($packedNetwork[$completeBytes]) & $mask);
    }

    private function matchesRange($clientIp, $rule)
    {
        $parts = explode('-', $rule, 2);
        $start = isset($parts[0]) ? @inet_pton(trim($parts[0])) : false;
        $end = isset($parts[1]) ? @inet_pton(trim($parts[1])) : false;
        $packedIp = @inet_pton($clientIp);

        if ($start === false
            || $end === false
            || $packedIp === false
            || strlen($start) !== strlen($end)
            || strlen($start) !== strlen($packedIp)
            || strcmp($start, $end) > 0
        ) {
            return false;
        }

        return strcmp($packedIp, $start) >= 0 && strcmp($packedIp, $end) <= 0;
    }
}
