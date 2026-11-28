<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CategoriasInventarioTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_categorias_muestra_el_listado_y_la_creacion_directa_abre_el_modal_en_el_listado(): void
    {
        $usuario = $this->administrador('1000000000001');

        $this->actingAs($usuario)->postJson(route('inventarios.categorias.store'), [
            'codigo' => 'CAT-1', 'nombre' => 'Insumos de prueba', 'activo' => true,
        ])->assertCreated();

        $this->get(route('inventarios.categorias.index'))->assertOk()
            ->assertSee('Insumos de prueba')
            ->assertSee('data-ajax-open="Nueva categoría"', false)
            ->assertSee('data-ajax-dialog', false)
            ->assertDontSee('data-ajax-open-on-load', false);

        $listadoConModal = route('inventarios.categorias.index', ['nueva' => 1]);
        $this->get(route('inventarios.categorias.create'))->assertRedirect($listadoConModal);
        $this->get($listadoConModal)->assertOk()->assertSee('Insumos de prueba')
            ->assertSee('data-ajax-open-on-load="nueva"', false);

        $this->get(route('inventarios.categorias.create'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertSee('Nueva categoría')->assertSee('data-ajax-form', false)
            ->assertDontSee('data-ajax-list', false);
    }

    public function test_rechaza_nombres_y_codigos_repetidos_al_crear_y_editar_en_la_misma_empresa(): void
    {
        $usuario = $this->administrador('1000000000001');
        $alta = route('inventarios.categorias.store');
        $categoria = ['codigo' => 'SERV-001', 'nombre' => 'Atención especial', 'activo' => true];
        $otraCategoria = ['codigo' => 'SERV-002', 'nombre' => 'Servicios varios', 'activo' => true];

        $this->actingAs($usuario)->postJson($alta, $categoria)->assertCreated();
        $this->postJson($alta, $otraCategoria)->assertCreated();
        $this->postJson($alta, array_replace($categoria, ['codigo' => 'SERV-003', 'nombre' => ' ATENCIÓN  ESPECIAL ']))
            ->assertUnprocessable()->assertJsonValidationErrors('nombre')->assertJsonMissingValidationErrors('codigo');
        $this->postJson($alta, array_replace($categoria, ['codigo' => 'serv-001', 'nombre' => 'Nombre distinto']))
            ->assertUnprocessable()->assertJsonValidationErrors('codigo')->assertJsonMissingValidationErrors('nombre');

        $id = DB::table('inv_categorias')->where('id_empresa', $usuario->id_empresa)->where('codigo', 'SERV-002')->value('id');
        $edicion = route('inventarios.categorias.update', $id);
        $this->putJson($edicion, $categoria)->assertUnprocessable()->assertJsonValidationErrors(['codigo', 'nombre']);
        $this->putJson($edicion, $otraCategoria + ['descripcion' => 'Actualizada'])->assertOk();
        $this->assertDatabaseHas('inv_categorias', ['id' => $id, 'codigo' => 'SERV-002', 'nombre' => 'Servicios varios', 'descripcion' => 'Actualizada']);

        $this->actingAs($this->administrador('1000000000002'))->postJson($alta, $categoria)->assertCreated();
    }

    public function test_muestra_autores_y_fechas_sin_hora_y_permite_cambiar_el_estado_desde_el_listado(): void
    {
        $creador = $this->administrador('1000000000001');
        $creador->persona()->update(['nombre' => 'Alex Omar', 'apellido' => 'Mite']);
        $editor = User::factory()->create(['id_empresa' => $creador->id_empresa, 'is_admin' => true]);
        $editor->persona()->update(['nombre' => 'Elena', 'apellido' => 'Vera']);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-11-25 15:46:00', 'America/Guayaquil'));
        $this->actingAs($creador)->postJson(route('inventarios.categorias.store'), [
            'codigo' => 'CAT-1', 'nombre' => 'Servicios', 'activo' => true,
        ])->assertCreated();
        $id = DB::table('inv_categorias')->where('id_empresa', $creador->id_empresa)->where('codigo', 'CAT-1')->value('id');

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-11-26 13:52:00', 'America/Guayaquil'));
        $estado = route('inventarios.categorias.estado', $id);
        $this->actingAs($editor)->patchJson($estado, ['activo' => false])->assertOk();
        $this->assertDatabaseHas('inv_categorias', ['id' => $id, 'activo' => false, 'id_usuario_modificacion' => $editor->id]);
        $this->get(route('inventarios.categorias.index'))->assertOk()
            ->assertSee('bi-eye', false)->assertSee('bi-list-ul', false)
            ->assertSeeInOrder(['bi-pencil', 'dropdown-divider', 'Activar'], false)
            ->assertSee('accion-fila-activar', false)
            ->assertSeeInOrder(['A. Mite', '25/11/2026', 'E. Vera', '26/11/2026'])
            ->assertDontSee('15:46')->assertDontSee('13:52');
        $this->patchJson($estado, ['activo' => true])->assertOk();
        $this->assertDatabaseHas('inv_categorias', ['id' => $id, 'activo' => true]);
        $this->get(route('inventarios.categorias.index'))->assertOk()->assertSee('accion-fila-desactivar', false);
        $this->actingAs($this->administrador('1000000000002'))->patchJson($estado, ['activo' => false])->assertNotFound();
    }

    public function test_la_plantilla_asigna_varias_categorias_y_los_productos_heredan_las_cuentas(): void
    {
        $usuario = $this->administrador('1000000000001');
        $empresa = $usuario->id_empresa;
        $this->actingAs($usuario);
        $categorias = [
            DB::table('inv_categorias')->insertGetId(['id_empresa' => $empresa, 'codigo' => 'SERV', 'nombre' => 'Servicios']),
            DB::table('inv_categorias')->insertGetId(['id_empresa' => $empresa, 'codigo' => 'ACCE', 'nombre' => 'Accesorios']),
        ];
        $ventas = DB::table('co_cuentas')->insertGetId(['id_empresa' => $empresa, 'codigo' => '4.01.01', 'nombre' => 'Ventas de servicios', 'clase' => 'ingreso']);
        $plantilla = ['codigo' => 'TPL-SERV', 'nombre' => 'Plantilla de servicios', 'activo' => true, 'cuentas' => ['ventas' => $ventas]];

        $this->get(route('inventarios.plantillas-contables.create'))->assertOk()
            ->assertSeeInOrder(['Categorías que usarán esta plantilla', 'Accesorios', 'Servicios']);
        $this->postJson(route('inventarios.plantillas-contables.store'), $plantilla + ['id_categorias' => [null, ...$categorias]])->assertCreated();
        $idPlantilla = DB::table('inv_plantillas_contables')->where('id_empresa', $empresa)->where('codigo', 'TPL-SERV')->value('id');
        $this->assertSame(2, DB::table('inv_categorias')->where('id_plantilla_contable', $idPlantilla)->count());
        $this->get(route('inventarios.plantillas-contables.show', $idPlantilla))->assertOk()->assertSeeInOrder(['Categorías asignadas', 'Accesorios', 'Servicios']);
        $this->postJson(route('inventarios.productos.store'), [
            'codigo' => 'P-001', 'nombre' => 'Servicio contratado', 'unidad' => 'unidad', 'id_categoria' => $categorias[0],
        ])->assertCreated();
        $producto = DB::table('inv_productos')->where('id_empresa', $empresa)->where('codigo', 'P-001')->first();
        $this->assertNull($producto->cuentas);
        $this->get(route('inventarios.productos.show', $producto->id))->assertOk()
            ->assertSee('Plantilla de servicios')->assertSee('4.01.01 · Ventas de servicios');

        $editar = route('inventarios.plantillas-contables.update', $idPlantilla);
        $this->putJson($editar, $plantilla + ['id_categorias' => [null, $categorias[1]]])->assertOk();
        $this->assertDatabaseHas('inv_categorias', ['id' => $categorias[0], 'id_plantilla_contable' => null]);
        $this->assertDatabaseHas('inv_categorias', ['id' => $categorias[1], 'id_plantilla_contable' => $idPlantilla]);
    }

    public function test_una_plantilla_no_puede_tomar_categorias_ajenas_o_ya_asignadas(): void
    {
        $usuario = $this->administrador('1000000000001');
        $otraEmpresa = $this->administrador('1000000000002')->id_empresa;
        $empresa = $usuario->id_empresa;
        $this->actingAs($usuario);
        $ventas = DB::table('co_cuentas')->insertGetId(['id_empresa' => $empresa, 'codigo' => '4.01.01', 'nombre' => 'Ventas de servicios', 'clase' => 'ingreso']);
        $plantilla = ['codigo' => 'TPL-USO', 'nombre' => 'Plantilla de pruebas', 'cuentas' => ['ventas' => $ventas]];
        $plantillaActual = DB::table('inv_plantillas_contables')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'TPL-PREV', 'nombre' => 'Plantilla anterior', 'cuentas' => json_encode(['ventas' => $ventas]),
        ]);
        $asignada = DB::table('inv_categorias')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'CAT-PREV', 'nombre' => 'Ya asignada', 'id_plantilla_contable' => $plantillaActual,
        ]);
        $ajena = DB::table('inv_categorias')->insertGetId(['id_empresa' => $otraEmpresa, 'codigo' => 'CAT-OTRA', 'nombre' => 'Categoría ajena']);

        foreach ([$asignada, $ajena] as $id) {
            $this->postJson(route('inventarios.plantillas-contables.store'), $plantilla + ['id_categorias' => [$id]])
                ->assertUnprocessable()->assertJsonValidationErrors('id_categorias');
        }

        $this->assertDatabaseMissing('inv_plantillas_contables', ['id_empresa' => $empresa, 'codigo' => 'TPL-USO']);
        $this->assertDatabaseHas('inv_categorias', ['id' => $asignada, 'id_plantilla_contable' => $plantillaActual]);
        $this->assertDatabaseHas('inv_categorias', ['id' => $ajena, 'id_plantilla_contable' => null]);
    }

    private function administrador(string $ruc): User
    {
        $empresa = EmpresaModel::create(['ruc' => $ruc, 'razon_social' => 'Empresa de pruebas']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => '001', 'nombre' => 'Matriz']);

        return User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
    }
}
