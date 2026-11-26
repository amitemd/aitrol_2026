<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class RecuperacionClaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_recovery_mail_uses_the_configured_url_and_does_not_reveal_accounts(): void
    {
        config(['app.url' => 'https://aitrol.example.test', 'mail.default' => 'array']);
        Notification::fake();
        $usuario = User::factory()->create();
        $mensaje = 'Si existe una cuenta con ese correo, recibirás un enlace para cambiar la contraseña.';

        $this->get('/')->assertOk()->assertSee(route('password.request'));
        $this->from('/forgot-password')
            ->post('/forgot-password', ['email' => 'desconocido@example.test'])
            ->assertSessionHas('status', $mensaje);
        Notification::assertNothingSent();

        $this->from('/forgot-password')
            ->withServerVariables(['HTTP_HOST' => 'otro.example.test'])
            ->post('/forgot-password', ['email' => $usuario->email])
            ->assertRedirect()
            ->assertSessionHas('status', $mensaje);

        Notification::assertSentTo($usuario, ResetPassword::class, function (ResetPassword $notification) use ($usuario): bool {
            $url = $notification->toMail($usuario)->actionUrl;

            return $url === 'https://aitrol.example.test'.route('password.reset', [
                'token' => $notification->token,
                'email' => $usuario->email,
            ], absolute: false);
        });
    }

    public function test_password_reset_updates_the_hash_and_consumes_the_token(): void
    {
        $usuario = User::factory()->create();
        $token = Password::createToken($usuario);
        $claveNueva = 'Clave-de-prueba-2026';

        $this->get(route('password.reset', ['token' => $token, 'email' => $usuario->email]))
            ->assertOk()->assertSee('Nueva contraseña');

        $datos = [
            'email' => $usuario->email,
            'token' => $token,
            'password' => $claveNueva,
            'password_confirmation' => $claveNueva,
        ];

        $this->post('/reset-password', $datos)->assertRedirect(route('dashboard'));
        $this->assertTrue(Hash::check($claveNueva, $usuario->fresh()->password));
        $this->assertFalse(Password::tokenExists($usuario, $token));
        $this->post('/reset-password', $datos)->assertRedirect(route('password.request'));
    }

    public function test_a_log_mailer_cannot_write_a_recovery_link_to_the_log(): void
    {
        config(['mail.default' => 'log']);
        Notification::fake();
        $usuario = User::factory()->create();

        $this->get('/forgot-password')->assertOk()->assertSee('Solicita al administrador')
            ->assertDontSee('Enviar enlace');
        $this->from('/forgot-password')->post('/forgot-password', ['email' => $usuario->email])
            ->assertRedirect('/forgot-password')->assertSessionHasErrors('email');

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_an_operator_can_generate_a_recovery_link_without_sending_mail(): void
    {
        config(['app.url' => 'https://aitrol.example.test', 'mail.default' => 'log']);
        Notification::fake();
        $usuario = User::factory()->create();
        $tokenAnterior = Password::createToken($usuario);

        $this->artisan('aitrol:enlace-recuperacion', ['email' => $usuario->email])
            ->expectsOutputToContain('https://aitrol.example.test/reset-password/')
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertFalse(Password::tokenExists($usuario, $tokenAnterior));
    }
}
