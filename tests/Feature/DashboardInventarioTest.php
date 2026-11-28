<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Aitrol\Inventarios\Application\RepositorioInventario;
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

    public function test_guarda_orden_y_grafico_por_empresa_y_usuario(): void
    {
        $usuario = $this->administrador();
        $otraEmpresa = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Otra empresa']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $otraEmpresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);
        $colega = User::factory()->create(['id_empresa' => $usuario->id_empresa, 'is_admin' => true]);
        $preferencias = [
            'distribucion' => [
                'indicadores' => ['bancos', 'compras', 'ventas', 'cobrar', 'pagar'],
                'paneles' => ['productos', 'alertas', 'accesos', 'tendencia'],
            ],
            'grafico' => 'scatter',
        ];

        $this->actingAs($usuario)->putJson(route('dashboard.preferencias'), $preferencias + [
            'id_empresa' => $otraEmpresa->id, 'id_usuario' => $colega->id,
        ])->assertNoContent();
        $this->get('/')->assertOk()->assertViewHas('preferencias', $preferencias)
            ->assertSee('data-dashboard-chart-type data-select2-nativo', false)
            ->assertSeeInOrder(['data-dashboard-card="productos"', 'data-dashboard-card="tendencia"'], false);
        $this->actingAs($colega)->get('/')->assertViewHas('preferencias.grafico', 'line');

        $usuario->forceFill(['id_empresa' => $otraEmpresa->id])->save();
        $this->actingAs($usuario)->get('/')->assertViewHas('preferencias.grafico', 'line');
        $this->assertDatabaseCount('dashboard_preferencias', 1);
    }

    public function test_rechaza_orden_duplicado_y_grafico_desconocido_con_422(): void
    {
        $this->actingAs($this->administrador())->putJson(route('dashboard.preferencias'), [
            'distribucion' => [
                'indicadores' => ['ventas', 'ventas', 'bancos', 'cobrar', 'pagar'],
                'paneles' => ['tendencia', 'alertas', 'accesos', 'productos'],
            ],
            'grafico' => 'radar',
        ])->assertUnprocessable()->assertJsonValidationErrors(['distribucion.indicadores.1', 'grafico']);

        $this->assertDatabaseCount('dashboard_preferencias', 0);
    }

    public function test_la_evolucion_neta_respeta_la_sucursal_y_el_periodo_activos(): void
    {
        $usuario = $this->administrador();
        $matriz = DB::table('erp_sucursales')->where('id_empresa', $usuario->id_empresa)->value('id');
        $otra = DB::table('erp_sucursales')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'NORTE', 'nombre' => 'Norte',
        ]);
        foreach ([
            [$matriz, '2025-03-01', 'documentos', 10000, null],
            [$matriz, '2025-03-02', 'notas-credito', 2500, null],
            [$matriz, '2025-03-03', 'documentos', 5000, '2025-03-04 00:00:00'],
            [$matriz, '2024-03-01', 'documentos', 30000, null],
            [$otra, '2025-03-05', 'documentos', 9000, null],
        ] as $numero => [$sucursal, $fecha, $tipo, $total, $anulado]) {
            DB::table('com_documentos')->insert([
                'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'modulo' => 'ventas',
                'tipo' => $tipo, 'numero' => "F-$numero", 'fecha' => $fecha, 'tercero' => 'Cliente',
                'subtotal' => $total, 'impuesto' => 0, 'total' => $total, 'anulado_at' => $anulado,
            ]);
        }

        $this->actingAs($usuario)->withSession([
            "contexto_operativo.{$usuario->id}.{$usuario->id_empresa}" => ['id_sucursal' => $matriz, 'periodo' => 2025],
        ])->get('/')->assertOk()->assertViewHas('series', fn (array $series): bool => count($series) === 1
            && $series[0]['id'] === 'ventas' && $series[0]['valores'][2] === 75.0);
    }

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

    public function test_el_panel_abre_sin_el_modulo_de_inventarios_cargado(): void
    {
        $usuario = $this->administrador();
        $rutas = Route::getRoutes();
        foreach (['inventarios.productos.index', 'inventarios.existencias.index'] as $ruta) {
            $rutas->getByName($ruta)->name('.deshabilitada');
        }
        $rutas->refreshNameLookups();
        unset($this->app[RepositorioInventario::class]);

        $this->actingAs($usuario)->get('/')->assertOk()->assertDontSee('Existencias en mínimo o por debajo');
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
