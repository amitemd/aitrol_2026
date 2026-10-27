<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use App\Services\NewSystem\CompanyAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sis_medico\User;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_LOCKOUT_SECONDS = 300;

    public function login(Request $request, CompanyAccess $companyAccess): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = Str::lower(trim($credentials['email']));
        $throttleKey = $this->throttleKey($request, $email);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'email' => $this->lockoutMessage($throttleKey),
            ]);
        }

        if (! Auth::attempt([
            'email' => $email,
            'password' => $credentials['password'],
            'estado' => 1,
        ], (bool) ($credentials['remember'] ?? false))) {
            $attempts = RateLimiter::hit($throttleKey, self::LOGIN_LOCKOUT_SECONDS);

            throw ValidationException::withMessages([
                'email' => $attempts >= self::MAX_LOGIN_ATTEMPTS
                    ? $this->lockoutMessage($throttleKey)
                    : 'Las credenciales proporcionadas no son válidas o el usuario está inactivo.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();
        $companies = $companyAccess->accessibleFor($user);
        $defaultCompany = $companies->first();

        if ($defaultCompany !== null) {
            $request->session()->put('id_empresa', $defaultCompany->id);
        } else {
            $request->session()->forget('id_empresa');
        }

        return redirect()->route('new-system.dashboard');
    }

    private function throttleKey(Request $request, string $email): string
    {
        return 'new-system-login:'.Str::transliterate($email.'|'.$request->ip());
    }

    private function lockoutMessage(string $throttleKey): string
    {
        $minutes = max(1, (int) ceil(RateLimiter::availableIn($throttleKey) / 60));

        return "Has alcanzado el máximo de intentos. Inténtalo nuevamente en {$minutes} minuto(s).";
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('new-system.dashboard');
    }
}
