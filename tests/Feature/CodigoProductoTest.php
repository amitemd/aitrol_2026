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

    public function test_guarda_marca_unidad_modelo_y_cuatro_precios_con_costo_unitario(): void
    {
        $usuario = $this->administrador('1000000000001');
        $empresa = $usuario->id_empresa;
        $this->actingAs($usuario);
        $this->postJson(route('inventarios.marcas.store'), ['codigo' => 'ACME', 'nombre' => 'Acme'])->assertCreated();
        $this->postJson(route('inventarios.unidades.store'), ['codigo' => 'CJ', 'nombre' => 'Caja'])->assertCreated();
        $marca = DB::table('inv_marcas')->where('id_empresa', $empresa)->value('id');
        $unidad = DB::table('inv_unidades')->where('id_empresa', $empresa)->value('id');

        $this->get(route('inventarios.productos.create'))->assertOk()
            ->assertSeeInOrder(['Sin marca', 'Acme', 'Modelo', 'Unidad de medida', 'Caja'])
            ->assertSee('Precio 4')->assertSee('Costo unitario normal');
        $producto = ['codigo' => 'CAJA-01', 'nombre' => 'Caja de insumos', 'id_unidad' => $unidad, 'id_marca' => $marca,
            'modelo' => 'X-12', 'precio_venta' => '25.00', 'precio_venta_2' => '22.50', 'precio_venta_3' => '20.00',
            'precio_venta_4' => '18.75', 'costo_unitario_referencia' => '12.3456', 'activo' => true];
        $this->postJson(route('inventarios.productos.store'), $producto)->assertCreated();
        $id = DB::table('inv_productos')->where('id_empresa', $empresa)->where('codigo', 'CAJA-01')->value('id');
        $this->assertDatabaseHas('inv_productos', $producto + ['id' => $id, 'marca' => 'Acme', 'unidad' => 'Caja']);

        $this->putJson(route('inventarios.productos.update', $id), array_replace($producto, ['id_marca' => null, 'precio_venta_4' => null]))->assertOk();
        $this->assertDatabaseHas('inv_productos', ['id' => $id, 'marca' => null, 'id_marca' => null, 'precio_venta_4' => null]);
    }

    public function test_rechaza_catalogos_ajenos_y_los_servicios_no_manejan_existencias(): void
    {
        $usuario = $this->administrador('1000000000001');
        $otraEmpresa = $this->administrador('1000000000002')->id_empresa;
        $unidadAjena = DB::table('inv_unidades')->insertGetId(['id_empresa' => $otraEmpresa, 'codigo' => 'CX', 'nombre' => 'Caja ajena']);
        $unidad = DB::table('inv_unidades')->insertGetId(['id_empresa' => $usuario->id_empresa, 'codigo' => 'UND', 'nombre' => 'Unidad']);
        $datos = ['codigo' => 'SERV-01', 'nombre' => 'Instalación', 'tipo_producto' => 'servicio', 'stock_minimo' => 5];
        $alta = route('inventarios.productos.store');

        $this->actingAs($usuario)->postJson($alta, $datos + ['id_unidad' => $unidadAjena])
            ->assertUnprocessable()->assertJsonValidationErrors('id_unidad');
        $this->postJson($alta, $datos + ['id_unidad' => $unidad, 'precio_venta_4' => '-1'])
            ->assertUnprocessable()->assertJsonValidationErrors('precio_venta_4');
        $this->postJson($alta, $datos + ['id_unidad' => $unidad])->assertCreated();
        $id = DB::table('inv_productos')->where('id_empresa', $usuario->id_empresa)->where('codigo', 'SERV-01')->value('id');
        $this->assertDatabaseHas('inv_productos', ['id' => $id, 'tipo_producto' => 'servicio', 'stock_minimo' => 0]);
        $this->getJson(route('inventarios.movimientos.opciones', ['catalogo' => 'productos', 'q' => 'SERV-01']))
            ->assertOk()->assertJsonCount(0, 'results');
        $this->postJson(route('inventarios.movimientos.store'), ['id_producto' => $id, 'id_bodega' => 1,
            'tipo' => 'entrada', 'cantidad' => 1, 'costo_unitario' => 10, 'motivo' => 'Prueba de servicio'])
            ->assertUnprocessable()->assertJsonValidationErrors('id_producto');
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
