<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CentrosCostosTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_mantenimiento_y_reportes_web_del_centro(): void
    {
        $usuario = $this->administrador('1000000000001');
        $this->actingAs($usuario);
        $datos = ['codigo' => 'adm', 'nombre' => 'Administración', 'descripcion' => 'Gestión general', 'activo' => true];

        $this->get(route('contabilidad.centros-costos.create'))->assertOk()->assertSee('Nuevo centro de costos');
        $this->postJson(route('contabilidad.centros-costos.store'), $datos)->assertCreated();
        $this->postJson(route('contabilidad.centros-costos.store'), $datos)->assertUnprocessable()->assertJsonValidationErrors('codigo');
        $id = DB::table('co_centros_costos')->where('id_empresa', $usuario->id_empresa)->where('codigo', 'ADM')->value('id');
        $this->get(route('contabilidad.centros-costos.index', ['q' => 'ADM']))->assertOk()->assertSee('Administración');
        $this->get(route('contabilidad.centros-costos.editar.formulario', $id))->assertOk()->assertSee('Editar centro de costos');
        $this->get(route('contabilidad.asientos.create'))->assertOk()->assertSee('ADM · Administración');
        $this->putJson(route('contabilidad.centros-costos.editar', $id), array_replace($datos, ['activo' => false]))->assertOk();
        $this->get(route('contabilidad.centros-costos.show', $id))->assertOk()->assertSee('Inactivo')->assertSee('Auditoría');
        $this->get(route('contabilidad.asientos.create'))->assertOk()->assertDontSee('ADM · Administración');
        $this->get(route('contabilidad.reportes.resultados-centros-costos'))->assertOk()
            ->assertSee('Sin centro asignado')->assertSee('ADM · Administración (inactivo)');
        $this->get(route('contabilidad.reportes.movimientos-centros-costos'))->assertOk()->assertSee('Movimientos por centro de costos');
    }

    public function test_resultados_y_movimientos_por_centro_preservan_reversiones_y_aislamiento(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 27));
        $usuario = $this->administrador('1000000000001');
        $otraEmpresa = $this->administrador('1000000000002')->id_empresa;
        $ajeno = DB::table('co_centros_costos')->insertGetId(['id_empresa' => $otraEmpresa, 'codigo' => 'EXT', 'nombre' => 'Centro externo']);
        $this->withToken($usuario->createToken('Prueba contable', ['*'])->plainTextToken);
        $datosCentro = ['codigo' => 'OP', 'nombre' => 'Operaciones', 'activo' => true];
        $centro = $this->postJson(route('api.v1.contabilidad.centros-costos.store'), $datosCentro)->assertCreated()->json('data.id');
        $this->postJson(route('api.v1.contabilidad.periodos.store'), [
            'nombre' => 'Prueba', 'desde' => '2026-01-01', 'hasta' => '2026-12-31',
        ])->assertCreated();
        $cuentas = [];
        foreach (['activo' => '110', 'ingreso' => '410', 'gasto' => '510'] as $clase => $codigo) {
            $cuentas[$clase] = DB::table('co_cuentas')->insertGetId([
                'id_empresa' => $usuario->id_empresa, 'codigo' => $codigo, 'nombre' => ucfirst($clase), 'clase' => $clase,
            ]);
        }
        $asientos = route('api.v1.contabilidad.asientos.store');
        $partida = ['fecha' => '2026-11-06', 'concepto' => 'Venta'];
        $this->postJson($asientos, $partida + ['lineas' => [
            ['id_cuenta' => $cuentas['activo'], 'debe' => 100],
            ['id_cuenta' => $cuentas['ingreso'], 'haber' => 100, 'id_centro_costo' => $centro],
        ]])->assertCreated()->assertJsonPath('data.lineas.1.id_centro_costo', $centro);
        $gasto = $this->postJson($asientos, $partida + ['lineas' => [
            ['id_cuenta' => $cuentas['gasto'], 'debe' => 40, 'id_centro_costo' => $centro],
            ['id_cuenta' => $cuentas['gasto'], 'debe' => 5],
            ['id_cuenta' => $cuentas['activo'], 'haber' => 45],
        ]])->assertCreated()->json('data.id');

        $filtros = ['desde' => '2026-11-01', 'hasta' => '2026-11-27'];
        $resultados = 'api.v1.contabilidad.reportes.resultados-centros-costos';
        $movimientos = 'api.v1.contabilidad.reportes.movimientos-centros-costos';
        $this->getJson(route($resultados, $filtros))->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('totales.resultado', '55.00')->assertJsonPath('data.0.resultado', '60.00');
        $this->getJson(route($resultados, $filtros + ['id_centro_costo' => 0]))->assertOk()->assertJsonPath('totales.gastos', '5.00');
        $this->getJson(route($movimientos, $filtros + ['id_centro_costo' => $centro]))->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('totales.haber', '100.00')->assertJsonPath('totales.debe', '40.00');
        $this->get(route($movimientos.'.exportar', $filtros + ['id_centro_costo' => $centro]))->assertDownload('movimientos-centros-costos.csv');
        $this->getJson(route($resultados, $filtros + ['id_centro_costo' => $ajeno]))->assertNotFound();
        $this->putJson(route('api.v1.contabilidad.centros-costos.editar', $centro), array_replace($datosCentro, ['activo' => false]))->assertOk();

        foreach ([$centro, $ajeno] as $invalido) {
            $this->postJson($asientos, $partida + ['lineas' => [
                ['id_cuenta' => $cuentas['gasto'], 'debe' => 20, 'id_centro_costo' => $invalido],
                ['id_cuenta' => $cuentas['activo'], 'haber' => 20],
            ]])->assertUnprocessable()->assertJsonValidationErrors('lineas.0.id_centro_costo');
        }
        $this->postJson(route('api.v1.contabilidad.asientos.revertir', $gasto), ['fecha' => '2026-11-09', 'motivo' => 'Anulación'])
            ->assertOk()->assertJsonPath('data.lineas.0.id_centro_costo', $centro);
        $this->getJson(route($resultados, $filtros + ['id_centro_costo' => $centro]))->assertOk()->assertJsonPath('totales.resultado', '100.00');
    }

    private function administrador(string $ruc): User
    {
        $empresa = EmpresaModel::create(['ruc' => $ruc, 'razon_social' => 'Empresa de pruebas']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => '001', 'nombre' => 'Matriz']);

        return User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
    }
}
