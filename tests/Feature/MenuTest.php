<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_manage_the_menu(): void
    {
        $this->get(route('configuracion.menu.index'))->assertRedirect(route('dashboard'));

        $this->actingAs(User::factory()->create());
        $this->get(route('configuracion.menu.index'))->assertForbidden();
        $this->post(route('configuracion.menu.store'))->assertForbidden();
        $this->get(route('dashboard'))->assertOk()
            ->assertSee('Dashboard v2')->assertSee('sales-chart')
            ->assertSee('Clientes')->assertDontSee('Configuraciones generales');
    }

    public function test_changes_to_the_menu_appear_on_the_next_page_load(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $index = route('configuracion.menu.index');

        $this->get($index)->assertOk()->assertSee('Administrar menú')->assertSee('Configuraciones generales');
        $this->post(route('configuracion.menu.store'), $this->datos('Gestión comercial', null))->assertRedirect($index);
        $this->get(route('dashboard'))->assertOk()->assertSee('Gestión comercial');
        $grupo = DB::table('menu')->where('nombre', 'Gestión comercial')->value('id');

        $this->post(route('configuracion.menu.store'), $this->datos('Alta comercial', 'clientes.create', $grupo))
            ->assertRedirect($index);
        $id = DB::table('menu')->where('nombre', 'Alta comercial')->value('id');

        $this->get(route('dashboard'))->assertOk()
            ->assertSeeInOrder(['Gestión comercial', route('clientes.create'), 'Alta comercial']);

        $this->put(route('configuracion.menu.update', $grupo), [
            ...$this->datos('Gestión comercial', null), 'activo' => false,
        ])->assertRedirect($index);
        $this->get(route('dashboard'))->assertOk()->assertDontSee('Alta comercial');

        $this->delete(route('configuracion.menu.destroy', $grupo))
            ->assertRedirect($index)->assertSessionHasErrors('menu');

        $this->put(route('configuracion.menu.update', $id), $this->datos('Registrar cliente', 'clientes.create'))
            ->assertRedirect($index);
        $this->get(route('dashboard'))->assertOk()->assertSee('Registrar cliente')->assertDontSee('Alta comercial');

        $this->delete(route('configuracion.menu.destroy', $id))->assertRedirect($index);
        $this->delete(route('configuracion.menu.destroy', $grupo))->assertRedirect($index);
        $this->get(route('dashboard'))->assertOk()->assertDontSee('Registrar cliente');
    }

    public function test_it_rejects_invalid_destinations_and_protects_access_to_configuration(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $index = route('configuracion.menu.index');
        $protegido = DB::table('menu')->where('ruta', 'configuracion.menu.index')->value('id');
        $clientes = DB::table('menu')->where('ruta', 'clientes.index')->value('id');

        $this->from($index)->post(route('configuracion.menu.store'), $this->datos('Enlace externo', 'https://example.test'))
            ->assertRedirect($index)->assertSessionHasErrors('ruta');
        $this->from($index)->post(route('configuracion.menu.store'), $this->datos('Opción', 'dashboard', $clientes))
            ->assertRedirect($index)->assertSessionHasErrors('menu');
        $this->delete(route('configuracion.menu.destroy', $protegido))
            ->assertRedirect($index)->assertSessionHasErrors('menu');
        $this->get($index)->assertOk()->assertSee('Administrar menú');
    }

    public function test_it_can_move_a_submenu_without_creating_cycles_or_losing_children(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $index = route('configuracion.menu.index');
        $store = route('configuracion.menu.store');

        $this->post($store, $this->datos('Área A', null))->assertRedirect($index);
        $grupo = DB::table('menu')->where('nombre', 'Área A')->value('id');
        $this->post($store, $this->datos('Área B', null, $grupo))->assertRedirect($index);
        $subgrupo = DB::table('menu')->where('nombre', 'Área B')->value('id');
        $this->post($store, $this->datos('Registro interno', 'clientes.create', $subgrupo))->assertRedirect($index);

        $this->get(route('dashboard'))->assertOk()->assertSeeInOrder(['Área A', 'Área B', 'Registro interno']);
        $this->from($index)->put(route('configuracion.menu.update', $grupo), $this->datos('Área A', null, $subgrupo))
            ->assertRedirect($index)->assertSessionHasErrors('menu');
        $this->from($index)->put(route('configuracion.menu.update', $subgrupo), $this->datos('Área B', 'dashboard', $grupo))
            ->assertRedirect($index)->assertSessionHasErrors('menu');
        $this->put(route('configuracion.menu.update', $subgrupo), $this->datos('Área B', null))->assertRedirect($index);
        $this->assertDatabaseHas('menu', ['id' => $subgrupo, 'padre_id' => null]);
        $this->assertDatabaseHas('menu', ['nombre' => 'Registro interno', 'padre_id' => $subgrupo]);
        $this->assertDatabaseHas('menu', ['ruta' => 'clientes.index', 'padre_id' => null]);
    }

    private function datos(string $nombre, ?string $ruta, ?int $padre = null): array
    {
        return [
            'nombre' => $nombre,
            'ruta' => $ruta,
            'padre_id' => $padre,
            'icono' => 'bi-people',
            'orden' => 50,
            'activo' => true,
            'solo_administrador' => false,
        ];
    }
}
