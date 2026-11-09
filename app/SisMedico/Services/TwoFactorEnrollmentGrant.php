<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Sis_medico\User;

/**
 * One-browser, short-lived proof that the account owner completed a verified
 * password reset before being allowed to see a new Authenticator secret.
 */
class TwoFactorEnrollmentGrant
{
    const SESSION_KEY = 'two_factor_enrollment_grant';
    const EXPIRES_SECONDS = 600;

    public function issue(Request $request, User $user)
    {
        $request->session()->put(self::SESSION_KEY, array(
            'user_id' => (string) $user->getAuthIdentifier(),
            'created_at' => time(),
            'credential_stamp' => $this->credentialStamp($user),
        ));
    }

    public function allows(Request $request, User $user)
    {
        $grant = $request->session()->get(self::SESSION_KEY);

        if (!is_array($grant)
            || empty($grant['user_id'])
            || empty($grant['created_at'])
            || empty($grant['credential_stamp'])
            || !hash_equals((string) $grant['user_id'], (string) $user->getAuthIdentifier())
            || (time() - (int) $grant['created_at']) > self::EXPIRES_SECONDS
            || !hash_equals($this->credentialStamp($user), (string) $grant['credential_stamp'])) {
            $this->forget($request);
            return false;
        }

        return true;
    }

    public function forget(Request $request)
    {
        $request->session()->forget(array(self::SESSION_KEY, 'two_factor_setup_secret'));
    }

    private function credentialStamp(User $user)
    {
        return hash_hmac('sha256', implode('|', array(
            (string) $user->getAuthIdentifier(),
            (string) $user->password,
            (string) $user->getRememberToken(),
        )), (string) config('app.key'));
    }
}
