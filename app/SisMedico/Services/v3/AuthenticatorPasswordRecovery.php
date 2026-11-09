<?php

namespace Sis_medico\Services\v3;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Sis_medico\Services\UserNetworkAccessService;
use Sis_medico\User;

class AuthenticatorPasswordRecovery
{
    const SESSION_KEY = 'v3_authenticator_password_recovery';
    const LIFETIME = 300;

    public function eligible($user)
    {
        return $user && (int) $user->estado === 1 && $user->hasTwoFactorEnabled() && !$user->isTwoFactorExempt();
    }

    public function grant(Request $request, User $user)
    {
        $request->session()->regenerate(true);
        $request->session()->forget('two_factor_login');
        // This is NOT an authenticated session or a token accepted by the email reset endpoint.
        $request->session()->put(self::SESSION_KEY, array(
            'user_id' => (string) $user->getKey(), 'expires_at' => time() + self::LIFETIME,
            'stamp' => $this->stamp($user),
        ));
    }

    public function pending(Request $request)
    {
        $proof = $request->session()->get(self::SESSION_KEY);
        if (!is_array($proof) || empty($proof['user_id']) || empty($proof['stamp'])
            || empty($proof['expires_at']) || (int) $proof['expires_at'] <= time()) return null;
        $user = User::find($proof['user_id']);
        return $this->matches($proof, $user) ? $user : null;
    }

    public function reset(Request $request, $password)
    {
        $proof = $request->session()->get(self::SESSION_KEY);
        if (!is_array($proof) || empty($proof['user_id'])) return null;
        $user = (new User)->getConnection()->transaction(function () use ($proof, $request, $password) {
            $user = User::whereKey($proof['user_id'])->lockForUpdate()->first();
            if (!$this->matches($proof, $user)
                || !app(UserNetworkAccessService::class)->evaluate($user, $request)['allowed']) return null;
            $hash = Hash::make($password);
            $remember = Str::random(60);
            // Compare-and-swap also protects against concurrent/stale recovery submissions.
            $updated = User::whereKey($user->getKey())->where('password', $user->password)
                ->where('two_factor_secret', $user->two_factor_secret)
                ->where('two_factor_confirmed_at', $user->getOriginal('two_factor_confirmed_at'))
                ->whereNull('two_factor_exempted_at')->where('estado', 1)
                ->update(array('password' => $hash, 'remember_token' => $remember,
                    'password_changed_at' => date('Y-m-d H:i:s')));
            if ($updated !== 1) return null;
            Password::broker()->deleteToken($user);
            $user->password = $hash;
            $user->setRememberToken($remember);
            return $user;
        });
        if ($user) {
            $request->session()->forget(self::SESSION_KEY);
            event(new PasswordReset($user));
        }
        return $user;
    }

    private function matches(array $proof, $user)
    {
        return $this->eligible($user) && isset($proof['expires_at'], $proof['stamp'])
            && (int) $proof['expires_at'] > time() && is_string($proof['stamp'])
            && hash_equals($this->stamp($user), $proof['stamp']);
    }

    private function stamp(User $user)
    {
        // No password hash or TOTP secret is exposed to the browser, URL, or logs.
        return hash_hmac('sha256', json_encode(array((string) $user->getKey(), $user->email,
            $user->password, $user->getRememberToken(), $user->two_factor_secret,
            $user->getOriginal('two_factor_confirmed_at'))), (string) config('app.key'));
    }
}
