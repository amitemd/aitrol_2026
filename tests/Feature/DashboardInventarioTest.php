<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class DashboardInventarioTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_el_panel_muestra_inventario_en_el_idioma_elegido(): void
    {
        $usuario = $this->administrador();
        $this->actingAs($usuario)->withSession(['idioma' => 'en'])
            ->get('/')->assertOk()->assertSee('Recently added products');
    }

    public function test_el_panel_abre_cuando_falta_la_ruta_de_productos(): void
    {
        $usuario = $this->administrador();
        $rutas = Route::getRoutes();
        $rutas->getByName('inventarios.productos.index')->name('.deshabilitada');
        $rutas->refreshNameLookups();

        $this->actingAs($usuario)->get('/')->assertOk()->assertDontSee('Últimos productos registrados');
    }

    public function test_el_panel_abre_antes_de_completar_la_migracion_de_productos(): void
    {
        $usuario = $this->administrador();
        Schema::table('inv_productos', fn (Blueprint $tabla) => $tabla->dropColumn('precio_venta'));

        $this->actingAs($usuario)->get('/')->assertOk()->assertDontSee('Últimos productos registrados');
    }

    private function administrador(): User
    {
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de prueba']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);

        return User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
    }
}
