<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\User;
use Sis_medico\UserNetworkAccessRule;

class UserNetworkAccessService
{
    public function evaluate(User $user, Request $request, $now = null)
    {
        if (!Schema::hasTable('user_network_access_rules')) {
            return array('allowed' => true, 'reason' => 'unrestricted', 'rule' => null);
        }

        $rules = UserNetworkAccessRule::where('user_id', $user->id)->where('active', 1)->get();
        if ($rules->isEmpty()) return array('allowed' => true, 'reason' => 'no_rules', 'rule' => null);

        $timestamp = $now ?: new \DateTimeImmutable('now', new \DateTimeZone(config('app.timezone')));
        $ip = (string) $request->ip();
        foreach ($rules as $rule) {
            if ($this->matches($rule, $ip, $timestamp)) {
                return array('allowed' => true, 'reason' => 'matched', 'rule' => $rule);
            }
        }

        return array('allowed' => false, 'reason' => 'outside_policy', 'rule' => null);
    }

    private function matches(UserNetworkAccessRule $rule, $ip, \DateTimeInterface $now)
    {
        if (config('ip_restriction.enabled', false) && !$this->matchesIp($ip, $rule->ip_rule)) return false;
        $date = $now->format('Y-m-d');
        if ($rule->valid_from && $date < $rule->valid_from) return false;
        if ($rule->valid_until && $date > $rule->valid_until) return false;
        if (!in_array($now->format('N'), explode(',', $rule->weekdays), true)) return false;

        $time = $now->format('H:i:s');
        $start = (string) $rule->starts_at;
        $end = (string) $rule->ends_at;
        // Permite turnos que cruzan medianoche, por ejemplo 22:00-06:00.
        return $start <= $end ? ($time >= $start && $time <= $end) : ($time >= $start || $time <= $end);
    }

    public function matchesIp($clientIp, $rule)
    {
        if (!filter_var($clientIp, FILTER_VALIDATE_IP)) return false;
        $rule = trim((string) $rule);
        if ($clientIp === $rule) return true;
        if (strpos($rule, '/') !== false) {
            list($network, $prefix) = array_pad(explode('/', $rule, 2), 2, null);
            if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit((string) $prefix)) return false;
            $ip = @inet_pton($clientIp); $net = @inet_pton($network);
            if ($ip === false || $net === false || strlen($ip) !== strlen($net)) return false;
            $prefix = (int) $prefix; $bits = strlen($ip) * 8;
            if ($prefix < 0 || $prefix > $bits) return false;
            $bytes = (int) floor($prefix / 8);
            if ($bytes && substr($ip, 0, $bytes) !== substr($net, 0, $bytes)) return false;
            $remaining = $prefix % 8;
            return !$remaining || ((ord($ip[$bytes]) & ((0xff << (8 - $remaining)) & 0xff)) === (ord($net[$bytes]) & ((0xff << (8 - $remaining)) & 0xff)));
        }
        if (strpos($rule, '-') !== false) {
            list($first, $last) = array_map('trim', explode('-', $rule, 2));
            $ip = @inet_pton($clientIp); $first = @inet_pton($first); $last = @inet_pton($last);
            return $ip !== false && $first !== false && $last !== false && strlen($ip) === strlen($first)
                && strlen($first) === strlen($last) && strcmp($first, $last) <= 0
                && strcmp($ip, $first) >= 0 && strcmp($ip, $last) <= 0;
        }
        return false;
    }

    public function isValidRule($rule)
    {
        $rule = trim((string) $rule);
        if (filter_var($rule, FILTER_VALIDATE_IP)) return true;
        if (strpos($rule, '/') !== false) {
            list($network, $prefix) = array_pad(explode('/', $rule, 2), 2, null);
            if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit((string) $prefix)) return false;
            return (int) $prefix >= 0 && (int) $prefix <= (strpos($network, ':') !== false ? 128 : 32);
        }
        if (strpos($rule, '-') !== false) {
            list($first, $last) = array_map('trim', explode('-', $rule, 2));
            $first = @inet_pton($first); $last = @inet_pton($last);
            return $first !== false && $last !== false && strlen($first) === strlen($last) && strcmp($first, $last) <= 0;
        }
        return false;
    }
}
