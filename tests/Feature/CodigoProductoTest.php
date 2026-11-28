<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CodigoProductoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sugiere_un_consecutivo_por_empresa_y_permite_personalizarlo_al_crear(): void
    {
        $primero = $this->administrador('1000000000001');
        $segundo = $this->administrador('1000000000002');

        $this->actingAs($primero)->get(route('inventarios.productos.create'))
            ->assertOk()->assertViewHas('codigoSugerido', 'AC-000001')
            ->assertSee('value="AC-000001"', false);
        $this->postJson(route('inventarios.productos.store'), $this->producto('ac-000001'))->assertCreated();
        $this->postJson(route('inventarios.productos.store'), $this->producto('CAT-42'))->assertCreated();
        $this->get(route('inventarios.productos.create'))->assertOk()->assertViewHas('codigoSugerido', 'AC-000002');
        $this->postJson(route('inventarios.productos.store'), $this->producto('ac-000001'))
            ->assertUnprocessable()->assertJsonValidationErrors('codigo');

        $this->actingAs($segundo)->get(route('inventarios.productos.create'))
            ->assertOk()->assertViewHas('codigoSugerido', 'AC-000001');
        $this->postJson(route('inventarios.productos.store'), $this->producto('AC-000001'))->assertCreated();
    }

    public function test_bloquea_el_cambio_de_codigo_y_permite_editar_los_demas_datos(): void
    {
        $usuario = $this->administrador('1000000000001');
        $this->actingAs($usuario)
            ->postJson(route('inventarios.productos.store'), $this->producto('MANUAL-01'))
            ->assertCreated();
        $registro = DB::table('inv_productos')->where('id_empresa', $usuario->id_empresa)->where('codigo', 'MANUAL-01')->value('id');

        $this->get(route('inventarios.productos.edit', $registro))->assertOk()
            ->assertSee('Código fijo del producto.')->assertSee('readonly', false);
        $this->putJson(route('inventarios.productos.update', $registro), $this->producto('MANUAL-02'))
            ->assertUnprocessable()->assertJsonValidationErrors('codigo');
        $this->putJson(route('inventarios.productos.update', $registro), $this->producto('manual-01'))
            ->assertOk();
        $sinCodigo = array_diff_key($this->producto('MANUAL-01'), ['codigo' => true]);
        $sinCodigo['nombre'] = 'Producto actualizado';
        $this->putJson(route('inventarios.productos.update', $registro), $sinCodigo)->assertOk();
        $this->assertDatabaseHas('inv_productos', ['id' => $registro, 'codigo' => 'MANUAL-01', 'nombre' => 'Producto actualizado']);
    }

    private function administrador(string $ruc): User
    {
        $empresa = EmpresaModel::create(['ruc' => $ruc, 'razon_social' => 'Ágora Comercial']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => '001', 'nombre' => 'Matriz']);

        return User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
    }

    /** @return array{codigo: string, nombre: string, unidad: string, activo: bool} */
    private function producto(string $codigo): array
    {
        return ['codigo' => $codigo, 'nombre' => 'Producto de pruebas', 'unidad' => 'unidad', 'activo' => true];
    }
}
