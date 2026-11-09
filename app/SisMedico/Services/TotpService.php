<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Crypt;
use Sis_medico\User;

class TotpService
{
    const PERIOD = 30;
    const DIGITS = 6;

    /** Atomically consume a counter across login and password recovery requests. */
    public function consume(User $user, $code)
    {
        if (!$user->hasTwoFactorEnabled() || $user->isTwoFactorExempt() || (int) $user->estado !== 1
            || !preg_match('/^[0-9]{6}$/D', (string) $code)) return null;
        try {
            $counter = $this->verify(Crypt::decryptString($user->two_factor_secret), $code, 1, $user->two_factor_last_counter);
        } catch (\Exception $exception) {
            return null;
        }
        if ($counter === null) return null;
        $updated = $user->newQuery()->whereKey($user->getKey())->where('estado', 1)
            ->where('two_factor_secret', $user->two_factor_secret)
            ->where('two_factor_confirmed_at', $user->getOriginal('two_factor_confirmed_at'))
            ->whereNull('two_factor_exempted_at')
            ->where(function ($query) use ($counter) {
                $query->whereNull('two_factor_last_counter')->orWhere('two_factor_last_counter', '<', $counter);
            })->update(array('two_factor_last_counter' => $counter));
        if ($updated !== 1) return null;
        $user->two_factor_last_counter = $counter;
        return $counter;
    }

    public function generateSecret($bytes = 20)
    {
        return $this->base32Encode(random_bytes($bytes));
    }

    public function provisioningUri($issuer, $account, $secret)
    {
        $label = rawurlencode($issuer).':'.rawurlencode($account);
        return 'otpauth://totp/'.$label.'?secret='.$secret.'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits='.self::DIGITS.'&period='.self::PERIOD;
    }

    public function verify($secret, $code, $window = 1, $lastUsedCounter = null)
    {
        $code = preg_replace('/\D/', '', (string) $code);
        if (strlen($code) !== self::DIGITS) return null;

        $counter = (int) floor(time() / self::PERIOD);
        for ($offset = -$window; $offset <= $window; $offset++) {
            $candidate = $counter + $offset;
            if ($lastUsedCounter !== null && $candidate <= (int) $lastUsedCounter) continue;
            if (hash_equals($this->at($secret, $candidate), $code)) return $candidate;
        }

        return null;
    }

    public function at($secret, $counter)
    {
        $binary = $this->base32Decode($secret);
        $high = (int) floor($counter / 4294967296);
        $low = $counter % 4294967296;
        $hash = hash_hmac('sha1', pack('N2', $high, $low), $binary, true);
        $offset = ord(substr($hash, -1)) & 0x0f;
        $value = unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff;
        return str_pad((string) ($value % pow(10, self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Encode($value)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($value) as $character) $bits .= str_pad(decbin(ord($character)), 8, '0', STR_PAD_LEFT);
        $encoded = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $encoded .= $alphabet[bindec($chunk)];
        }
        return $encoded;
    }

    private function base32Decode($value)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split(strtoupper(preg_replace('/[^A-Z2-7]/i', '', $value))) as $character) {
            $position = strpos($alphabet, $character);
            if ($position === false) throw new \InvalidArgumentException('Secreto TOTP inválido.');
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }
        $decoded = '';
        foreach (str_split($bits, 8) as $chunk) if (strlen($chunk) === 8) $decoded .= chr(bindec($chunk));
        return $decoded;
    }
}
