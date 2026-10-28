<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Sis_medico\User;

class PasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('new-system.auth.forgot-password');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:191'],
        ]);

        Password::sendResetLink([
            'email' => Str::lower(trim($credentials['email'])),
            'estado' => 1,
        ]);

        return back()->with(
            'status',
            __('new_system.auth.reset_link_sent'),
        );
    }

    public function legacyRecoveryDisabled(): RedirectResponse
    {
        return redirect()
            ->route('new-system.password.request')
            ->withErrors([
                'email' => __('new_system.auth.registered_email_required'),
            ]);
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('new-system.auth.reset-password', [
            'email' => $request->string('email')->toString(),
            'token' => $token,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:191'],
            'password' => [
                'required',
                'confirmed',
                'max:255',
                PasswordRule::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);
        $credentials['email'] = Str::lower(trim($credentials['email']));
        $credentials['estado'] = 1;

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                $this->revokeDatabaseSessions($user);

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PasswordReset) {
            return redirect()
                ->route('new-system.dashboard')
                ->with('status', __('new_system.auth.password_updated'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => __('new_system.auth.invalid_reset_link'),
            ]);
    }

    private function revokeDatabaseSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $connection = config('session.connection');
        $table = (string) config('session.table', 'sessions');
        $schema = Schema::connection($connection);

        if (! $schema->hasTable($table)) {
            return;
        }

        DB::connection($connection)
            ->table($table)
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->delete();
    }
}
