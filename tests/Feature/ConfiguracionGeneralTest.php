<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ConfiguracionGeneralTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_open_and_modify_general_settings(): void
    {
        $usuario = User::factory()->create();
        $this->get(route('configuracion.usuarios.index'))->assertRedirect(route('dashboard'));

        $this->actingAs($usuario);
        $this->get(route('configuracion.usuarios.index'))->assertForbidden();
        $this->get(route('configuracion.usuarios.edit', $usuario))->assertForbidden();
        $this->put(route('configuracion.usuarios.update', $usuario), ['name' => 'Otro'])->assertForbidden();
        $this->get(route('configuracion.empresas.index'))->assertForbidden();
        $this->post(route('configuracion.empresas.store'), [])->assertForbidden();
        $this->get(route('dashboard'))->assertOk()->assertDontSee('/configuraciones/usuarios')->assertDontSee('/configuraciones/empresas');
    }

    public function test_an_administrator_can_update_an_existing_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $usuario = User::factory()->create();
        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertOk()->assertSee('/configuraciones/usuarios')->assertSee('/configuraciones/empresas');
        $this->get(route('configuracion.usuarios.index'))->assertOk()->assertSee($usuario->email);
        $edicion = route('configuracion.usuarios.edit', $usuario);
        $actualizar = route('configuracion.usuarios.update', $usuario);
        $this->get($edicion)->assertOk()->assertSee($usuario->name);
        $this->from($edicion)->put($actualizar, ['name' => 'Nombre corregido', 'email' => $admin->email])
            ->assertRedirect($edicion)->assertSessionHasErrors('email');

        $this->put($actualizar, ['name' => 'Nombre corregido', 'email' => 'corregido@example.test', 'is_admin' => true])
            ->assertRedirect(route('configuracion.usuarios.index'));
        $usuario->refresh();
        $this->assertSame('Nombre corregido', $usuario->name);
        $this->assertSame('corregido@example.test', $usuario->email);
        $this->assertNull($usuario->email_verified_at);
        $this->assertFalse($usuario->is_admin);
        $this->assertTrue(Hash::check('password', $usuario->password));
    }

    public function test_an_administrator_can_register_a_company_with_a_unique_ruc(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $listado = route('configuracion.empresas.index');
        $crear = route('configuracion.empresas.store');
        $datos = [
            'razon_social' => 'Empresa de prueba',
            'ruc' => '0991234567001',
            'email' => 'contacto@example.test',
        ];

        $this->from($listado)->post($crear, [...$datos, 'ruc' => 'ABC'])
            ->assertRedirect($listado)->assertSessionHasErrors('ruc');
        $this->post($crear, $datos)->assertRedirect($listado);
        $this->assertDatabaseHas('erp_empresas', $datos);
        $this->get($listado)->assertOk()->assertSee('Empresa de prueba');
        $this->from($listado)->post($crear, $datos)->assertRedirect($listado)->assertSessionHasErrors('ruc');
        $this->assertDatabaseCount('erp_empresas', 1);
    }

    public function test_administrative_lists_provide_modal_forms_and_ajax_responses(): void
    {
        $administrador = User::factory()->create(['is_admin' => true]);
        $usuario = User::factory()->create(['name' => 'Cuenta para buscar']);
        $this->actingAs($administrador);

        $this->get(route('configuracion.usuarios.index', ['q' => 'para buscar']))
            ->assertOk()->assertSee('data-ajax-list', false)->assertSee($usuario->email)->assertDontSee($administrador->email);
        $this->get(route('configuracion.usuarios.edit', $usuario))->assertOk()->assertSee('data-ajax-form', false);
        $this->putJson(route('configuracion.usuarios.update', $usuario), [
            'name' => $usuario->name,
            'email' => 'actualizado@example.test',
        ])->assertOk()->assertJsonPath('message', 'Usuario actualizado.');

        $this->get(route('configuracion.empresas.index', ['crear' => 1]))->assertOk()->assertSee('data-ajax-form', false);
        $this->postJson(route('configuracion.empresas.store'), [
            'razon_social' => 'Empresa para buscar',
            'ruc' => '0991234567001',
        ])->assertCreated();
        $this->get(route('configuracion.empresas.index', ['q' => 'para buscar']))
            ->assertOk()->assertSee('Empresa para buscar');

        $this->get(route('configuracion.menu.index', ['crear' => 1]))->assertOk()->assertSee('data-ajax-form', false);
        $this->postJson(route('configuracion.menu.store'), [
            'nombre' => 'Consulta para buscar', 'activo' => true, 'solo_administrador' => true, 'icono' => 'bi-folder', 'orden' => 100,
        ])->assertOk()->assertJsonPath('menuChanged', true);
        $this->get(route('configuracion.menu.index', ['q' => 'para buscar']))
            ->assertOk()->assertSee('Consulta para buscar');
    }

    public function test_user_api_requires_a_scoped_admin_token(): void
    {
        $usuario = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $id = $usuario->id;
        $this->getJson('/api/v1/usuarios')->assertUnauthorized();

        $token = $usuario->createToken('Usuario normal', ['usuarios:leer'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/usuarios')->assertForbidden();

        $this->app['auth']->forgetGuards();
        $token = $admin->createToken('Solo lectura', ['usuarios:leer'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/usuarios?q='.urlencode($usuario->email))
            ->assertOk()->assertJsonPath('meta.total', 1)->assertJsonPath('data.0.email', $usuario->email)
            ->assertJsonMissingPath('data.0.password')->assertJsonMissingPath('data.0.remember_token');
        $this->getJson("/api/v1/usuarios/{$id}")->assertOk()->assertJsonPath('data.id', $id);
        $this->putJson("/api/v1/usuarios/{$id}", [])->assertForbidden();

        $this->app['auth']->forgetGuards();
        $token = $admin->createToken('Edición', ['usuarios:escribir'])->plainTextToken;
        $this->withToken($token)->putJson("/api/v1/usuarios/{$id}", [
            'name' => 'Editado por API', 'email' => 'api@example.test', 'is_admin' => true,
        ])->assertOk()->assertJsonPath('data.is_admin', false)->assertJsonPath('data.email', 'api@example.test');
        $this->putJson("/api/v1/usuarios/{$admin->id}", [
            'name' => 'Editado por API', 'email' => 'admin-api@example.test',
        ])->assertForbidden();
    }
}
