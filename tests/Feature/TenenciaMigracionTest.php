<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class TenenciaMigracionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reanuda_una_migracion_parcial_y_conserva_las_asignaciones_existentes(): void
    {
        $inicial = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa inicial']);
        $otra = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Otra empresa']);
        $sinEmpresa = User::factory()->create();
        $asignado = User::factory()->create(['id_empresa' => $otra->id]);
        config(['tenencia.empresa_legacy' => $inicial->id]);

        Schema::table('personal_access_tokens', function (Blueprint $table): void {
            $table->dropForeign(['id_empresa']);
            $table->dropIndex('personal_access_tokens_id_empresa_index');
        });
        $migracion = require database_path('migrations/2026_11_27_030000_separar_datos_por_empresa.php');
        $migracion->up();
        $migracion->up();

        $this->assertDatabaseHas('users', ['id' => $sinEmpresa->id, 'id_empresa' => $inicial->id]);
        $this->assertDatabaseHas('users', ['id' => $asignado->id, 'id_empresa' => $otra->id]);
        $this->assertTrue(Schema::hasIndex('personal_access_tokens', 'personal_access_tokens_id_empresa_index'));
        $this->assertTrue(collect(Schema::getForeignKeys('personal_access_tokens'))->contains(
            fn (array $clave): bool => $clave['columns'] === ['id_empresa'] && $clave['foreign_table'] === 'erp_empresas',
        ));
        $this->assertTrue(Schema::hasIndex('erp_proveedores', ['id_empresa', 'tipo_identificacion', 'identificacion'], 'unique'));
    }
}
