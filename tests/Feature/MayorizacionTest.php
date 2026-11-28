<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class MayorizacionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_el_mayor_web_abre_con_todas_las_cuentas_y_mantiene_saldos_separados(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 28));
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de prueba']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => '001', 'nombre' => 'Matriz']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $periodo = DB::table('co_periodos')->insertGetId([
            'id_empresa' => $empresa->id, 'nombre' => '2026', 'desde' => '2026-01-01', 'hasta' => '2026-12-31',
        ]);
        $caja = DB::table('co_cuentas')->insertGetId([
            'id_empresa' => $empresa->id, 'codigo' => '110', 'nombre' => 'Caja Mayor', 'clase' => 'activo',
        ]);
        $venta = DB::table('co_cuentas')->insertGetId([
            'id_empresa' => $empresa->id, 'codigo' => '410', 'nombre' => 'Venta Mayor', 'clase' => 'ingreso', 'activo' => false,
        ]);
        foreach ([1 => ['2026-10-30', 'Venta de octubre', 1500], 2 => ['2026-11-27', 'Venta de noviembre', 5000]] as $numero => [$fecha, $concepto, $total]) {
            $asiento = DB::table('co_asientos')->insertGetId([
                'id_empresa' => $empresa->id, 'id_periodo' => $periodo, 'numero' => $numero, 'fecha' => $fecha,
                'concepto' => $concepto, 'total' => $total,
            ]);
            DB::table('co_lineas')->insert([
                ['id_empresa' => $empresa->id, 'id_asiento' => $asiento, 'id_cuenta' => $caja, 'debe' => $total, 'haber' => 0],
                ['id_empresa' => $empresa->id, 'id_asiento' => $asiento, 'id_cuenta' => $venta, 'debe' => 0, 'haber' => $total],
            ]);
        }

        $ruta = 'contabilidad.reportes.mayorizacion';
        $this->actingAs($usuario)->get(route($ruta))->assertOk()
            ->assertSee('Todas las cuentas')->assertSee('Caja Mayor')->assertSee('Venta Mayor');

        $filtros = ['desde' => '2026-11-01', 'hasta' => '2026-11-28'];
        $this->get(route($ruta, $filtros + ['per_page' => 1]))->assertOk()
            ->assertSee('class="pagination', false)->assertDontSee('Venta de octubre')
            ->assertViewHas('registros', fn ($filas): bool => $filas->total() === 2 && $filas->first()['saldo'] === '65.00');
        $this->get(route($ruta, $filtros + ['id_cuenta' => $venta]))->assertOk()
            ->assertViewHas('totales', ['apertura' => '-15.00', 'debe' => '0.00', 'haber' => '50.00', 'cierre' => '-65.00'])
            ->assertViewHas('registros', fn ($filas): bool => $filas->total() === 1 && $filas->first()['saldo'] === '-65.00');

        $this->get(route($ruta.'.exportar', $filtros))->assertDownload('mayorizacion.csv');
    }
}
