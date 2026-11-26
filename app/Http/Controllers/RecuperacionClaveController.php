<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as ReglaClave;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class RecuperacionClaveController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password', ['correoDisponible' => $this->correoDisponible()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'string', 'email:filter', 'max:255'],
        ]);

        if (! $this->correoDisponible()) {
            return back()->withInput()->withErrors([
                'email' => 'El servicio de recuperación por correo aún no está disponible.',
            ]);
        }

        try {
            Password::sendResetLink($datos);
        } catch (TransportExceptionInterface $exception) {
            report($exception);
        }

        return back()->with('status', 'Si existe una cuenta con ese correo, recibirás un enlace para cambiar la contraseña.');
    }

    public function edit(Request $request, string $token): Response
    {
        $email = $request->query('email');

        return response()->view('auth.reset-password', [
            'token' => $token,
            'email' => is_string($email) ? $email : '',
        ])->header('Cache-Control', 'no-store');
    }

    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255'],
            'password' => ['required', 'string', 'confirmed', ReglaClave::min(12)->max(72), function (string $attribute, mixed $value, Closure $fail): void {
                if (is_string($value) && (strlen($value) > 72 || str_contains($value, "\0"))) {
                    $fail('Utiliza una contraseña de hasta 72 bytes y sin caracteres nulos.');
                }
            }],
        ], [
            'password.min' => 'La nueva contraseña debe tener al menos 12 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $resultado = Password::reset($datos, function (User $usuario, string $clave): void {
            $usuario->password = $clave;
            $usuario->setRememberToken(Str::random(60));
            $usuario->save();

            event(new PasswordReset($usuario));
        });

        if ($resultado !== Password::PasswordReset) {
            return to_route('password.request')->withErrors([
                'email' => 'El enlace es inválido o ha vencido. Solicita uno nuevo.',
            ]);
        }

        return to_route('dashboard')->with('status', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }

    private function correoDisponible(): bool
    {
        $mailer = config('mail.default');
        $transporte = config("mail.mailers.{$mailer}.transport");

        return is_string($transporte)
            && $transporte !== 'log'
            && ($transporte !== 'array' || app()->runningUnitTests())
            && ! in_array('log', config("mail.mailers.{$mailer}.mailers", []), true);
    }
}
