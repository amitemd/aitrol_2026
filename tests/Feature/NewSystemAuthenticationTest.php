<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Notifications\MyResetPassword;
use Sis_medico\User;
use Tests\TestCase;

class NewSystemAuthenticationTest extends TestCase
{
    public function test_login_styles_use_compact_desktop_dimensions(): void
    {
        $styles = file_get_contents(public_path('css/new-system-legacy.css'));

        $this->assertIsString($styles);
        $this->assertStringContainsString('max-width: 1080px;', $styles);
        $this->assertStringContainsString('min-height: min(760px, calc(100vh - 72px));', $styles);
        $this->assertStringContainsString('height: 250px;', $styles);
        $this->assertStringContainsString('max-width: 430px;', $styles);
        $this->assertStringContainsString('height: 46px;', $styles);
    }

    public function test_login_is_blocked_for_five_minutes_after_five_failed_attempts(): void
    {
        $this->createAuthenticationRecords();
        $this->travelTo('2026-10-27 08:00:00');

        foreach (range(1, 4) as $attempt) {
            $this->post(route('new-system.login.store'), [
                'email' => 'admin@example.com',
                'password' => 'incorrect-password-'.$attempt,
            ])->assertSessionHasErrors([
                'email' => 'Las credenciales proporcionadas no son válidas o el usuario está inactivo.',
            ]);
        }

        $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'incorrect-password-5',
        ])->assertSessionHasErrors([
            'email' => 'Has alcanzado el máximo de intentos. Inténtalo nuevamente en 5 minuto(s).',
        ]);

        $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'Has alcanzado el máximo de intentos. Inténtalo nuevamente en 5 minuto(s).',
        ]);
        $this->assertGuest();

        $this->travel(301)->seconds();

        $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirectToRoute('new-system.dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_is_blocked_when_an_account_is_attacked_from_multiple_addresses(): void
    {
        $this->createAuthenticationRecords();
        $this->travelTo('2026-10-27 08:00:00');

        foreach (range(1, 10) as $attempt) {
            $this->withServerVariables(['REMOTE_ADDR' => "192.0.2.{$attempt}"])
                ->post(route('new-system.login.store'), [
                    'email' => 'admin@example.com',
                    'password' => 'incorrect-password-'.$attempt,
                ]);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->post(route('new-system.login.store'), [
                'email' => 'admin@example.com',
                'password' => 'password',
            ])->assertSessionHasErrors([
                'email' => 'El acceso está temporalmente bloqueado por seguridad. Inténtalo nuevamente más tarde.',
            ]);
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_replace_their_identity_through_the_login_endpoint(): void
    {
        $authenticatedUser = $this->createAuthenticationRecords();
        DB::table('users')->insert([
            'id' => 'second-user',
            'nombre1' => 'Segundo',
            'apellido1' => 'Usuario',
            'email' => 'second@example.com',
            'password' => bcrypt('password'),
            'remember_token' => null,
            'id_tipo_usuario' => 1,
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($authenticatedUser)
            ->post(route('new-system.login.store'), [
                'email' => 'second@example.com',
                'password' => 'password',
            ])
            ->assertRedirect(route('new-system.dashboard'));

        $this->assertAuthenticatedAs($authenticatedUser);
    }

    public function test_login_rejects_an_excessively_large_password_before_hashing_it(): void
    {
        $this->createAuthenticationRecords();

        $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => str_repeat('a', 256),
        ])->assertSessionHasErrors([
            'password' => 'La contraseña no puede superar los 255 caracteres.',
        ]);

        $this->assertGuest();
    }

    public function test_authentication_pages_send_browser_security_headers_and_disable_caching(): void
    {
        $response = $this->get(route('new-system.dashboard'));

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $contentSecurityPolicy = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString("frame-ancestors 'none'", $contentSecurityPolicy);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9+\/=]+';/", $contentSecurityPolicy);
        $this->assertMatchesRegularExpression('/<script nonce="[A-Za-z0-9+\/=]+">/', $response->getContent());
    }

    public function test_https_authentication_pages_enable_strict_transport_security(): void
    {
        $this->get('https://localhost:8000/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_login_is_limited_per_address_across_different_accounts(): void
    {
        $this->createAuthenticationRecords();
        config()->set('auth.login_security.ip_max_attempts', 3);

        foreach (range(1, 3) as $attempt) {
            $this->post(route('new-system.login.store'), [
                'email' => "unknown-{$attempt}@example.com",
                'password' => 'incorrect-password',
            ]);
        }

        $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'El acceso está temporalmente bloqueado por seguridad. Inténtalo nuevamente más tarde.',
        ]);
        $this->assertGuest();
    }

    public function test_remember_me_creates_a_persistent_login_cookie(): void
    {
        $user = $this->createAuthenticationRecords();

        $response = $this->post(route('new-system.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response
            ->assertRedirectToRoute('new-system.dashboard')
            ->assertCookie(Auth::guard()->getRecallerName());
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()?->getRememberToken());
    }

    public function test_password_recovery_forms_are_available_from_the_new_login(): void
    {
        $this->get(route('new-system.dashboard'))
            ->assertOk()
            ->assertSee(route('new-system.password.request'), false);

        $this->get(route('new-system.password.request'))
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?')
            ->assertSee('action="'.route('new-system.password.email').'"', false);

        $this->get(route('new-system.password.reset', [
            'email' => 'admin@example.com',
            'token' => 'test-token',
        ]))
            ->assertOk()
            ->assertSee('Crea una nueva contraseña')
            ->assertSee('action="'.route('new-system.password.update').'"', false);
    }

    public function test_password_reset_request_sends_a_valid_notification(): void
    {
        $user = $this->createAuthenticationRecords();
        Notification::fake();

        $response = $this
            ->from(route('new-system.password.request'))
            ->post(route('new-system.password.email'), [
                'email' => 'ADMIN@example.com',
            ]);

        $response
            ->assertRedirectToRoute('new-system.password.request')
            ->assertSessionHas(
                'status',
                'Si el correo pertenece a una cuenta activa, recibirás un enlace para restablecer la contraseña.',
            );
        Notification::assertSentTo(
            $user,
            MyResetPassword::class,
            fn (MyResetPassword $notification): bool => Password::broker()->tokenExists($user, $notification->token)
                && $notification->toMail($user)->actionUrl === route('new-system.password.reset', [
                    'email' => $user->email,
                    'token' => $notification->token,
                ]),
        );
    }

    public function test_unknown_email_receives_the_same_password_recovery_response(): void
    {
        $this->createAuthenticationRecords();
        Notification::fake();

        $response = $this
            ->from(route('new-system.password.request'))
            ->post(route('new-system.password.email'), [
                'email' => 'unknown@example.com',
            ]);

        $response
            ->assertRedirectToRoute('new-system.password.request')
            ->assertSessionHas(
                'status',
                'Si el correo pertenece a una cuenta activa, recibirás un enlace para restablecer la contraseña.',
            );
        Notification::assertNothingSent();
    }

    public function test_password_recovery_requests_are_limited_for_five_minutes(): void
    {
        $this->createAuthenticationRecords();
        Notification::fake();

        foreach (range(1, 3) as $attempt) {
            $this
                ->from(route('new-system.password.request'))
                ->post(route('new-system.password.email'), [
                    'email' => 'unknown@example.com',
                ])
                ->assertSessionHas('status');
        }

        $this
            ->from(route('new-system.password.request'))
            ->post(route('new-system.password.email'), [
                'email' => 'unknown@example.com',
            ])
            ->assertRedirectToRoute('new-system.password.request')
            ->assertSessionHasErrors([
                'email' => 'Demasiadas solicitudes. Inténtalo nuevamente en cinco minutos.',
            ]);
        Notification::assertNothingSent();
    }

    public function test_password_recovery_is_limited_per_address_across_different_emails(): void
    {
        $this->createAuthenticationRecords();
        config()->set('auth.password_reset_security.ip_max_attempts', 3);
        Notification::fake();

        foreach (range(1, 3) as $attempt) {
            $this
                ->from(route('new-system.password.request'))
                ->post(route('new-system.password.email'), [
                    'email' => "unknown-{$attempt}@example.com",
                ])
                ->assertSessionHas('status');
        }

        $this
            ->from(route('new-system.password.request'))
            ->post(route('new-system.password.email'), [
                'email' => 'another-unknown@example.com',
            ])
            ->assertSessionHasErrors([
                'email' => 'Demasiadas solicitudes. Inténtalo nuevamente en cinco minutos.',
            ]);
        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = $this->createAuthenticationRecords();
        Event::fake([PasswordReset::class]);
        $token = Password::broker()->createToken($user);
        $newPassword = 'New-Secure-Password-2026!';

        $response = $this->post(route('new-system.password.update'), [
            'email' => 'admin@example.com',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ]);

        $response
            ->assertRedirectToRoute('new-system.dashboard')
            ->assertSessionHas('status', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
        $this->assertTrue(Hash::check($newPassword, (string) $user->fresh()?->password));
        $this->assertNotNull($user->fresh()?->getRememberToken());
        Event::assertDispatched(
            PasswordReset::class,
            fn (PasswordReset $event): bool => $event->user->is($user),
        );
    }

    public function test_password_reset_requires_a_strong_password(): void
    {
        $user = $this->createAuthenticationRecords();
        $token = Password::broker()->createToken($user);

        $this->post(route('new-system.password.update'), [
            'email' => 'admin@example.com',
            'password' => 'simplepassword',
            'password_confirmation' => 'simplepassword',
            'token' => $token,
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', (string) $user->fresh()?->password));
    }

    public function test_invalid_password_reset_does_not_reveal_whether_the_account_exists(): void
    {
        $this->createAuthenticationRecords();

        $response = $this->post(route('new-system.password.update'), [
            'email' => 'unknown@example.com',
            'password' => 'New-Secure-Password-2026!',
            'password_confirmation' => 'New-Secure-Password-2026!',
            'token' => 'invalid-token',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'El enlace de recuperación no es válido o ya expiró.',
        ]);
    }

    public function test_password_reset_link_expires_after_thirty_minutes(): void
    {
        $user = $this->createAuthenticationRecords();
        $this->travelTo('2026-10-27 08:00:00');
        $token = Password::broker()->createToken($user);
        $this->travel(31)->minutes();

        $this->post(route('new-system.password.update'), [
            'email' => 'admin@example.com',
            'password' => 'New-Secure-Password-2026!',
            'password_confirmation' => 'New-Secure-Password-2026!',
            'token' => $token,
        ])->assertSessionHasErrors([
            'email' => 'El enlace de recuperación no es válido o ya expiró.',
        ]);

        $this->assertTrue(Hash::check('password', (string) $user->fresh()?->password));
    }

    public function test_password_reset_revokes_existing_database_sessions(): void
    {
        $user = $this->createAuthenticationRecords();
        Schema::dropIfExists('sessions');
        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        DB::table('sessions')->insert([
            'id' => 'existing-authenticated-session',
            'user_id' => $user->getAuthIdentifier(),
            'ip_address' => '192.0.2.10',
            'user_agent' => 'Test browser',
            'payload' => base64_encode(serialize([])),
            'last_activity' => now()->getTimestamp(),
        ]);
        config()->set('session.driver', 'database');
        $token = Password::broker()->createToken($user);
        $newPassword = 'New-Secure-Password-2026!';

        $this->post(route('new-system.password.update'), [
            'email' => 'admin@example.com',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ])->assertRedirectToRoute('new-system.dashboard');

        $this->assertDatabaseMissing('sessions', [
            'id' => 'existing-authenticated-session',
        ]);
    }

    private function createAuthenticationRecords(): User
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuario_empresa');
        Schema::dropIfExists('empresa');
        Schema::dropIfExists('log_usuario');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('nombre1');
            $table->string('apellido1');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedInteger('id_tipo_usuario');
            $table->unsignedTinyInteger('estado');
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('empresa', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('razonsocial');
            $table->string('nombrecomercial');
            $table->string('nombre_corto')->nullable();
            $table->string('ciudad')->nullable();
            $table->unsignedTinyInteger('estado');
            $table->integer('prioridad')->default(0);
        });
        Schema::create('usuario_empresa', function (Blueprint $table): void {
            $table->string('id_usuario');
            $table->string('id_empresa');
        });
        Schema::create('log_usuario', function (Blueprint $table): void {
            $table->id();
            $table->string('id_usuario');
            $table->string('ip_usuario');
            $table->string('descripcion');
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id' => 'admin',
            'nombre1' => 'Administrador',
            'apellido1' => 'AITROL',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'remember_token' => null,
            'id_tipo_usuario' => 1,
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('empresa')->insert([
            'id' => 'empresa-1',
            'razonsocial' => 'AITROL S.A.',
            'nombrecomercial' => 'AITROL',
            'nombre_corto' => 'AITROL',
            'ciudad' => 'Guayaquil',
            'estado' => 1,
            'prioridad' => 1,
        ]);
        DB::table('usuario_empresa')->insert([
            'id_usuario' => 'admin',
            'id_empresa' => 'empresa-1',
        ]);

        return User::query()->findOrFail('admin');
    }
}
