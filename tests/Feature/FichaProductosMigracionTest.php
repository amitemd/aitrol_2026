<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class FichaProductosMigracionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reanuda_la_ficha_con_catalogos_creados_y_preserva_los_datos(): void
    {
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de prueba']);
        $categoria = DB::table('inv_categorias')->insertGetId([
            'id_empresa' => $empresa->id, 'codigo' => 'CAT1', 'nombre' => 'Categoría existente',
        ]);
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $empresa->id, 'id_categoria' => $categoria, 'codigo' => 'P1',
            'nombre' => 'Producto existente', 'unidad' => 'unidad', 'marca' => 'Marca original',
        ]);
        Schema::table('inv_productos', function (Blueprint $tabla): void {
            $tabla->dropForeign(['id_empresa', 'id_categoria']);
            $tabla->dropColumn('modelo');
        });

        $migracion = require database_path('migrations/2026_11_27_120000_agregar_ficha_y_plantillas_de_productos.php');
        $migracion->up();
        $migracion->up();

        $this->assertDatabaseHas('inv_categorias', ['id' => $categoria, 'nombre' => 'Categoría existente']);
        $this->assertDatabaseHas('inv_productos', ['id' => $producto, 'id_categoria' => $categoria, 'marca' => 'Marca original', 'modelo' => null]);
        $claves = collect(Schema::getForeignKeys('inv_productos'))->filter(
            fn (array $clave): bool => $clave['columns'] === ['id_empresa', 'id_categoria'] && $clave['foreign_table'] === 'inv_categorias',
        );
        $this->assertCount(1, $claves);
    }
}
