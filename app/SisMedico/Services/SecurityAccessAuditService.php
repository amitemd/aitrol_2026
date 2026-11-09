<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\SecurityAccessEvent;
use Sis_medico\User;

class SecurityAccessAuditService
{
    public function record($eventType, $result, Request $request, $user = null, array $extra = array())
    {
        try {
            if (!Schema::hasTable('security_access_events')) return null;

            $identifier = strtolower(trim((string) $request->input('email', '')));
            if (!$user && $identifier !== '' && Schema::hasTable('users')) {
                $user = User::where('email', $identifier)->first();
            }

            $userId = $user ? (string) $user->id : null;
            $ip = substr((string) $request->ip(), 0, 45);
            $agent = substr((string) $request->header('User-Agent', 'Desconocido'), 0, 1000);
            $device = $this->deviceMetadata($agent);
            $fingerprint = hash('sha256', $agent.'|'.strtolower((string) $request->header('Accept-Language', '')));
            $history = $this->history($userId, $ip, $fingerprint);
            $location = $this->location($request);
            $risk = $this->risk($eventType, $result, $request, $userId, $ip, $history, $location, $extra);
            $sessionId = $request->hasSession() ? (string) $request->session()->getId() : '';

            return SecurityAccessEvent::create(array(
                'user_id' => $userId,
                'identifier_hash' => $identifier !== '' ? hash('sha256', $identifier) : null,
                'event_type' => substr((string) $eventType, 0, 40),
                'result' => substr((string) $result, 0, 20),
                'risk_level' => $risk['level'],
                'risk_reasons' => count($risk['reasons']) ? json_encode($risk['reasons']) : null,
                'ip_address' => $ip ?: null,
                'is_new_ip' => $history['has_ip_history'] && !$history['known_ip'],
                'is_new_device' => $history['has_device_history'] && !$history['known_device'],
                'country_code' => $location['country'],
                'region' => $location['region'],
                'city' => $location['city'],
                'location_source' => $location['source'],
                'device_type' => $device['type'],
                'operating_system' => $device['os'],
                'browser' => $device['browser'],
                'device_fingerprint' => $fingerprint,
                'user_agent' => $agent,
                'http_method' => substr($request->method(), 0, 10),
                'path' => substr('/'.$request->path(), 0, 500),
                'session_hash' => $sessionId !== '' ? hash('sha256', $sessionId) : null,
                'occurred_at' => date('Y-m-d H:i:s'),
            ));
        } catch (\Exception $exception) {
            // La auditoria nunca debe impedir que la aplicacion cierre o valide una sesion.
            Log::error('No se pudo registrar la auditoria de acceso.', array(
                'event_type' => $eventType,
                'error' => $exception->getMessage(),
            ));
            return null;
        }
    }

    public function signature(Request $request)
    {
        return hash('sha256', (string) $request->ip().'|'.(string) $request->header('User-Agent', '')
            .'|'.(string) $request->header('Accept-Language', ''));
    }

    public function requestMetadata(Request $request)
    {
        $device = $this->deviceMetadata(substr((string) $request->header('User-Agent', 'Desconocido'), 0, 1000));
        return array_merge($device, $this->location($request));
    }

    private function history($userId, $ip, $fingerprint)
    {
        if (!$userId) return array('has_ip_history' => false, 'has_device_history' => false,
            'known_ip' => false, 'known_device' => false);
        $success = SecurityAccessEvent::where('user_id', $userId)->where('result', 'success');
        $hasSecurityHistory = (clone $success)->exists();
        $legacyHistory = Schema::hasTable('log_usuario')
            ? DB::table('log_usuario')->where('id_usuario', $userId)->where('descripcion', 'INICIO DE SESION')
            : null;
        return array(
            'has_ip_history' => $hasSecurityHistory || ($legacyHistory && (clone $legacyHistory)->exists()),
            'has_device_history' => $hasSecurityHistory,
            'known_ip' => $ip !== '' && ((clone $success)->where('ip_address', $ip)->exists()
                || ($legacyHistory && (clone $legacyHistory)->where('ip_usuario', $ip)->exists())),
            'known_device' => (clone $success)->where('device_fingerprint', $fingerprint)->exists(),
        );
    }

    private function risk($eventType, $result, Request $request, $userId, $ip, array $history, array $location, array $extra)
    {
        $score = 0;
        $reasons = array();
        if ($result !== 'success') { $score += 25; $reasons[] = 'Intento de acceso no exitoso'; }
        if ($history['has_ip_history'] && !$history['known_ip']) { $score += 30; $reasons[] = 'IP nueva para este usuario'; }
        if ($history['has_device_history'] && !$history['known_device']) { $score += 25; $reasons[] = 'Dispositivo o navegador nuevo'; }

        $hour = (int) date('G');
        $start = (int) config('security_audit.usual_start_hour', 5);
        $end = (int) config('security_audit.usual_end_hour', 23);
        if ($hour < $start || $hour >= $end) { $score += 15; $reasons[] = 'Acceso fuera del horario habitual configurado'; }

        if ($result !== 'success' && $ip !== '') {
            $minutes = max(1, (int) config('security_audit.failed_attempt_window_minutes', 15));
            $threshold = max(2, (int) config('security_audit.failed_attempt_threshold', 5));
            $failures = SecurityAccessEvent::where('ip_address', $ip)->where('result', '<>', 'success')
                ->where('occurred_at', '>=', date('Y-m-d H:i:s', time() - ($minutes * 60)))->count();
            if (($failures + 1) >= $threshold) { $score += 40; $reasons[] = 'Múltiples intentos fallidos desde la misma IP'; }
        }

        if (!empty($extra['reason'])) $reasons[] = substr((string) $extra['reason'], 0, 250);
        if (in_array($eventType, array('blocked_legacy_login', 'blocked_legacy_endpoint'), true)) {
            $score += 50;
            $reasons[] = 'Intento de usar una ruta antigua retirada por seguridad';
        }

        return array('level' => $score >= 70 ? 'critical' : ($score >= 40 ? 'high' : ($score >= 20 ? 'medium' : 'low')),
            'reasons' => array_values(array_unique($reasons)));
    }

    private function location(Request $request)
    {
        // Estos encabezados solo son evidencia cuando Symfony reconoce al
        // emisor como proxy confiable. Un cliente directo podría falsificarlos.
        if (!$request->isFromTrustedProxy()) {
            return array('country' => null, 'region' => null, 'city' => null, 'source' => null);
        }
        $country = $request->header('CF-IPCountry') ?: $request->header('X-Country-Code');
        $region = $request->header('CF-Region') ?: $request->header('X-Region');
        $city = $request->header('CF-IPCity') ?: $request->header('X-City');
        return array(
            'country' => $country ? substr(strtoupper((string) $country), 0, 8) : null,
            'region' => $region ? substr((string) $region, 0, 120) : null,
            'city' => $city ? substr((string) $city, 0, 120) : null,
            'source' => ($country || $region || $city) ? 'trusted-proxy-header' : null,
        );
    }

    private function deviceMetadata($agent)
    {
        $isBot = preg_match('/bot|crawler|spider|curl|wget|python|postman/i', $agent);
        $type = $isBot ? 'Automatizado' : (preg_match('/tablet|ipad/i', $agent) ? 'Tableta'
            : (preg_match('/mobile|android|iphone/i', $agent) ? 'Móvil' : 'Computador'));
        $os = 'Desconocido';
        foreach (array('Windows' => '/Windows/i', 'Android' => '/Android/i', 'iOS' => '/iPhone|iPad/i',
            'macOS' => '/Macintosh|Mac OS X/i', 'Linux' => '/Linux/i') as $name => $pattern) if (preg_match($pattern, $agent)) { $os = $name; break; }
        $browser = 'Desconocido';
        foreach (array('Edge' => '/Edg\//i', 'Chrome' => '/Chrome\//i', 'Firefox' => '/Firefox\//i',
            'Safari' => '/Safari\//i', 'Internet Explorer' => '/MSIE|Trident/i', 'cURL' => '/curl\//i') as $name => $pattern) {
            if (preg_match($pattern, $agent)) { $browser = $name; break; }
        }
        return array('type' => $type, 'os' => $os, 'browser' => $browser);
    }
}
