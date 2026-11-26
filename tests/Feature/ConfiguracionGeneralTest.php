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
}