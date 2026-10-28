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
    public function login(Request $request, CompanyAccess $companyAccess): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ], [
            'password.max' => __('new_system.auth.password_too_long'),
        ]);

        $email = Str::lower(trim($credentials['email']));
        $limiters = $this->loginLimiters($request, $email);

        if ($blockedLimiter = $this->blockedLimiter($limiters)) {
            $this->rememberLockout($request, $blockedLimiter, $email);

            throw ValidationException::withMessages([
                'email' => $this->lockoutMessage($blockedLimiter),
            ]);
        }

        Auth::guard()->setRememberDuration(
            (int) config('auth.login_security.remember_duration_minutes', 43200),
        );

        if (! Auth::attempt([
            'email' => $email,
            'password' => $credentials['password'],
            'estado' => 1,
        ], (bool) ($credentials['remember'] ?? false))) {
            foreach ($limiters as $name => $limiter) {
                $limiters[$name]['attempts'] = RateLimiter::hit($limiter['key'], $limiter['decay']);
            }

            $blockedLimiter = $this->blockedLimiter($limiters);

            if ($blockedLimiter !== null) {
                $this->rememberLockout($request, $blockedLimiter, $email);
            }

            throw ValidationException::withMessages([
                'email' => $blockedLimiter !== null
                    ? $this->lockoutMessage($blockedLimiter)
                    : __('new_system.auth.invalid_credentials'),
            ]);
        }

        RateLimiter::clear($limiters['pair']['key']);
        RateLimiter::clear($limiters['account']['key']);
        $request->session()->forget([
            'new_system.login_locked_until',
            'new_system.login_locked_email_hash',
        ]);
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $request->session()->put([
            'lang' => config('localization.default_locale', 'es'),
            'new_system.locale_explicitly_selected' => false,
        ]);

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

    /**
     * @return array<string, array{key: string, max: int, decay: int, attempts?: int}>
     */
    private function loginLimiters(Request $request, string $email): array
    {
        $accountHash = hash('sha256', $email);
        $ipHash = hash('sha256', (string) $request->ip());

        return [
            'pair' => [
                'key' => "new-system-login:pair:{$accountHash}:{$ipHash}",
                'max' => (int) config('auth.login_security.pair_max_attempts', 5),
                'decay' => (int) config('auth.login_security.pair_decay_seconds', 300),
            ],
            'account' => [
                'key' => "new-system-login:account:{$accountHash}",
                'max' => (int) config('auth.login_security.account_max_attempts', 10),
                'decay' => (int) config('auth.login_security.account_decay_seconds', 900),
            ],
            'ip' => [
                'key' => "new-system-login:ip:{$ipHash}",
                'max' => (int) config('auth.login_security.ip_max_attempts', 50),
                'decay' => (int) config('auth.login_security.ip_decay_seconds', 300),
            ],
        ];
    }

    /**
     * @param  array<string, array{key: string, max: int, decay: int, attempts?: int}>  $limiters
     * @return array{key: string, max: int, decay: int, attempts?: int}|null
     */
    private function blockedLimiter(array $limiters): ?array
    {
        foreach ($limiters as $limiter) {
            if (($limiter['attempts'] ?? 0) >= $limiter['max']
                || RateLimiter::tooManyAttempts($limiter['key'], $limiter['max'])) {
                return $limiter;
            }
        }

        return null;
    }

    /**
     * @param  array{key: string, max: int, decay: int, attempts?: int}  $limiter
     */
    private function lockoutMessage(array $limiter): string
    {
        if (str_contains($limiter['key'], ':pair:')) {
            $minutes = max(1, (int) ceil(RateLimiter::availableIn($limiter['key']) / 60));

            return __('new_system.auth.max_attempts', ['minutes' => $minutes]);
        }

        return __('new_system.auth.blocked_security');
    }

    /**
     * @param  array{key: string, max: int, decay: int, attempts?: int}  $limiter
     */
    private function rememberLockout(Request $request, array $limiter, string $email): void
    {
        $retryAfter = max(1, RateLimiter::availableIn($limiter['key']));

        $request->session()->flash(
            'new_system.login_locked_until',
            now()->addSeconds($retryAfter)->getTimestamp(),
        );
        $request->session()->flash(
            'new_system.login_locked_email_hash',
            hash('sha256', $email),
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('new-system.dashboard');
    }
}
