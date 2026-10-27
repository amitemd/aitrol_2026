<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
            'Si el correo pertenece a una cuenta activa, recibirás un enlace para restablecer la contraseña.',
        );
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
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
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

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PasswordReset) {
            return redirect()
                ->route('new-system.dashboard')
                ->with('status', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $status === Password::InvalidUser
                    ? 'No se encontró una cuenta activa con ese correo electrónico.'
                    : 'El enlace de recuperación no es válido o ya expiró.',
            ]);
    }
}
