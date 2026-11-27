<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_cached_language_catalog_keeps_the_login_available_in_spanish(): void
    {
        config(['app.idiomas' => null]);

        $this->withSession(['idioma' => 'fr'])->get('/')->assertOk()->assertSee('lang="es"', false)->assertSee('Español');
    }

    public function test_it_saves_the_chosen_language_and_rejects_unsupported_values(): void
    {
        $this->get('/')->assertOk()->assertSee('lang="es"', false)->assertSee('Iniciar sesión');

        foreach (['en' => ['en', 'Sign in'], 'pt' => ['pt', 'Entrar'], 'it' => ['it', 'Accedi'], 'fr' => ['fr', 'Se connecter'], 'zh_CN' => ['zh-CN', '登录']] as $idioma => [$html, $etiqueta]) {
            $this->from('/')->post('/idioma', ['idioma' => $idioma])->assertRedirect('/');
            $this->get('/')->assertOk()->assertSee('lang="'.$html.'"', false)->assertSee($etiqueta);
        }

        $this->postJson('/idioma', ['idioma' => 'de'])->assertUnprocessable()->assertJsonValidationErrors('idioma');
    }

    public function test_dashboard_shows_the_latest_four_products_from_the_current_company(): void
    {
        $empresa = DB::table('erp_empresas')->insertGetId(['ruc' => '0990000000001', 'razon_social' => 'Empresa principal']);
        $externa = DB::table('erp_empresas')->insertGetId(['ruc' => '0990000000002', 'razon_social' => 'Empresa externa']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);
        $producto = ['id_empresa' => $empresa, 'unidad' => 'Unidad', 'precio_venta' => 1900, 'created_at' => '2026-11-27 12:00:00'];
        foreach (range(1, 5) as $numero) {
            DB::table('inv_productos')->insert($producto + ['codigo' => 'IT-'.$numero, 'nombre' => 'Producto prueba '.$numero]);
        }
        DB::table('inv_productos')->insert(array_replace($producto, ['id_empresa' => $externa, 'codigo' => 'OTRO', 'nombre' => 'Producto externo']));
        $this->actingAs(User::factory()->create(['id_empresa' => $empresa, 'is_admin' => true]));

        $this->get('/')->assertOk()->assertSeeInOrder(['Producto prueba 5', 'Producto prueba 4', 'Producto prueba 3', 'Producto prueba 2'])
            ->assertDontSee('Producto prueba 1')->assertDontSee('Producto externo')->assertDontSee('Samsung TV');
    }

    public function test_the_dashboard_requires_a_session_and_the_login_uses_adminlte(): void
    {
        $this->get('/')->assertOk()->assertSee('login-box')->assertDontSee('app-sidebar')
            ->assertSee(asset('adminlte/css/adminlte.min.css'))->assertSee(route('password.request'));
        $this->get('/login')->assertRedirect(route('dashboard'));

        $usuario = User::factory()->create();
        $this->post('/', ['email' => $usuario->email, 'password' => 'incorrecta'])
            ->assertRedirect(route('dashboard'))->assertSessionHasErrors('email');
        $this->post('/', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->get('/')->assertOk()->assertSee($usuario->name)->assertDontSee('login-box');
        $this->get('/login')->assertRedirect(route('dashboard'));
        $this->post('/logout')->assertRedirect(route('dashboard'));
        $this->get('/')->assertOk()->assertSee('login-box');
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
        $this->post('/', ['email' => $email, 'password' => $clavePrueba])
            ->assertRedirect(route('dashboard'));
    }
}
