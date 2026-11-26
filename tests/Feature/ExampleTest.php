<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_requires_a_session_and_the_login_uses_adminlte(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/login')->assertOk()->assertSee('login-box')->assertDontSee('app-sidebar');

        $usuario = User::factory()->create();
        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->get('/')->assertOk()->assertSee($usuario->name);
        $this->get('/login')->assertRedirect(route('dashboard'));
        $this->post('/logout')->assertRedirect(route('login'));
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_the_admin_command_stores_a_hashed_initial_password(): void
    {
        $email = 'administrador@example.test';
        $clavePrueba = 'prueba-A';

        $this->artisan('aitrol:usuario-crear', [
            'email' => $email,
            'nombre' => 'Administrador',
            '--administrador' => true,
            '--clave-inicial' => true,
        ])
            ->expectsQuestion('Contraseña', $clavePrueba)
            ->expectsQuestion('Repita la contraseña', $clavePrueba)
            ->assertSuccessful();

        $usuario = User::query()->where('email', $email)->sole();
        $this->assertTrue($usuario->is_admin);
        $this->assertNotSame($clavePrueba, $usuario->password);
        $this->post('/login', ['email' => $email, 'password' => $clavePrueba])
            ->assertRedirect(route('dashboard'));
    }
}
