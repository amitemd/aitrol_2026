<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class RecursosHumanosTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cierra_la_nomina_con_contratos_novedades_y_cuotas_y_emite_roles(): void
    {
        [$usuario, $sucursal] = $this->empresa('1234567890001');
        $this->actingAs($usuario);
        $this->get('/rrhh/panel')->assertOk()->assertSee('Recursos humanos');
        $this->get('/rrhh/empleados')->assertOk()->assertSee('data-ajax-dialog');
        $this->get('/rrhh/empleados/crear')->assertOk()->assertSee('data-ajax-form')->assertSee('Información laboral');
        $this->post('/rrhh/empleados', ['codigo' => 'E01', 'nombre' => 'Ana Rivas', 'ingreso' => '2026-01-01'])->assertRedirect('/rrhh/empleados');
        $empleado = DB::table('rh_empleados')->where('id_sucursal', $sucursal)->value('id');
        $this->get('/rrhh/empleados/'.$empleado)->assertOk()->assertSee('data-ajax-form')->assertSee('Ana Rivas');
        $contrato = ['id_empleado' => $empleado, 'tipo' => 'Indefinido', 'inicio' => '2026-01-01', 'sueldo' => '1000.00', 'horas_semanales' => 40];
        $this->post('/rrhh/contratos', $contrato)->assertRedirect('/rrhh/contratos');
        $this->postJson('/rrhh/contratos', $contrato)->assertUnprocessable()->assertJsonValidationErrors('inicio');
        $this->postJson('/rrhh/conceptos', [
            'titulo' => 'Complemento interno', 'tipo' => 'ingreso-porcentual', 'id_empleado' => $empleado,
            'inicio' => '2026-11-01', 'cantidad' => '2.50',
        ])->assertOk();
        $concepto = DB::table('rh_procesos')->where('tipo', 'ingreso-porcentual')->value('id');
        $this->patchJson('/rrhh/conceptos/'.$concepto.'/estado', ['estado' => 'aprobado'])->assertOk();
        $this->post('/rrhh/prestamos', [
            'titulo' => 'Anticipo noviembre', 'tipo' => 'anticipo', 'id_empleado' => $empleado, 'inicio' => '2026-11-01', 'importe' => '80.00',
        ])->assertRedirect();
        $prestamo = DB::table('rh_procesos')->where('tipo', 'anticipo')->value('id');
        $this->patch('/rrhh/prestamos/'.$prestamo.'/estado', ['estado' => 'aprobado'])->assertRedirect();
        $cuota = ['titulo' => 'Cuota del anticipo', 'tipo' => 'cuota', 'id_empleado' => $empleado, 'inicio' => '2026-11-20', 'id_prestamo' => $prestamo, 'importe' => '40.00'];
        $this->post('/rrhh/novedades', $cuota)->assertRedirect();
        $this->post('/rrhh/nomina', ['periodo' => '2026-11'])->assertRedirect();
        $nomina = DB::table('rh_nominas')->value('id');
        $this->patchJson('/rrhh/nomina/'.$nomina.'/estado', ['estado' => 'cerrar'])->assertUnprocessable();
        $this->assertDatabaseCount('rh_roles', 0);
        $this->patch('/rrhh/novedades/'.DB::table('rh_procesos')->where('tipo', 'cuota')->value('id').'/estado', ['estado' => 'aprobado'])->assertRedirect();
        $this->patch('/rrhh/nomina/'.$nomina.'/estado', ['estado' => 'cerrar'])->assertRedirect();
        $this->patchJson('/rrhh/nomina/'.$nomina.'/estado', ['estado' => 'cerrar'])->assertConflict();
        $this->postJson('/rrhh/novedades', $cuota)->assertUnprocessable()->assertJsonValidationErrors('inicio');
        $this->assertDatabaseHas('rh_roles', ['id_nomina' => $nomina, 'ingresos' => 102500, 'descuentos' => 4000, 'neto' => 98500]);
        $this->get('/rrhh/nomina/'.$nomina)->assertOk()->assertSee('data-ajax-form')->assertSee('Ana Rivas');
        $this->get('/rrhh/prestamos')->assertOk()->assertSee('Saldo USD 40.00');
        $this->get('/rrhh/nomina/'.$nomina.'/exportar')->assertDownload('nomina-2026-11.csv');
        $this->get('/rrhh/roles/'.DB::table('rh_roles')->value('id'))->assertOk()->assertSee('Cuota del anticipo');
        $this->patch('/rrhh/nomina/'.$nomina.'/estado', ['estado' => 'pagar', 'referencia' => 'TR-001'])->assertRedirect();
        $this->assertDatabaseHas('rh_nominas', ['id' => $nomina, 'estado' => 'pagada', 'referencia_pago' => 'TR-001']);
    }

    public function test_vacaciones_requieren_saldo_y_no_permiten_cruce_de_fechas(): void
    {
        [$usuario, $sucursal] = $this->empresa('1234567890004');
        $this->actingAs($usuario);
        $empleado = DB::table('rh_empleados')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'codigo' => 'E04',
            'nombre' => 'Empleado', 'ingreso' => '2026-01-01',
        ]);
        $permiso = ['titulo' => 'Vacaciones noviembre', 'tipo' => 'vacaciones', 'id_empleado' => $empleado, 'inicio' => '2026-11-18', 'fin' => '2026-11-20'];
        $this->postJson('/rrhh/permisos', $permiso)->assertOk();
        $vacaciones = DB::table('rh_procesos')->where('tipo', 'vacaciones')->value('id');
        $this->patchJson('/rrhh/permisos/'.$vacaciones.'/estado', ['estado' => 'aprobado'])->assertUnprocessable();
        $this->postJson('/rrhh/saldos-vacaciones', [
            'titulo' => 'Saldo aprobado por talento humano', 'tipo' => 'credito', 'id_empleado' => $empleado,
            'inicio' => '2026-11-01', 'cantidad' => 3,
        ])->assertOk();
        $saldo = DB::table('rh_procesos')->where('tipo', 'credito')->value('id');
        $this->patchJson('/rrhh/saldos-vacaciones/'.$saldo.'/estado', ['estado' => 'aprobado'])->assertOk();
        $this->patchJson('/rrhh/permisos/'.$vacaciones.'/estado', ['estado' => 'aprobado'])->assertOk();
        $this->postJson('/rrhh/permisos', $permiso)->assertOk();
        $duplicado = DB::table('rh_procesos')->where('tipo', 'vacaciones')->max('id');
        $this->patchJson('/rrhh/permisos/'.$duplicado.'/estado', ['estado' => 'aprobado'])
            ->assertUnprocessable()->assertJsonPath('errors.estado.0', 'Ya existe un permiso aprobado que coincide con estas fechas.');
    }

    public function test_aisla_empleados_y_nomina_por_sucursal_y_permiso(): void
    {
        [$usuario, $sucursal] = $this->empresa('1234567890002');
        [$externo, $sucursalExterna] = $this->empresa('1234567890003');
        $empleadoExterno = DB::table('rh_empleados')->insertGetId([
            'id_empresa' => $externo->id_empresa, 'id_sucursal' => $sucursalExterna,
            'nombre' => 'Persona externa', 'codigo' => 'X001', 'ingreso' => '2026-01-01',
        ]);
        $segunda = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $usuario->id_empresa, 'codigo' => 'B', 'nombre' => 'Otra']);
        $empleadoOtraSucursal = DB::table('rh_empleados')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $segunda, 'nombre' => 'Otra ubicación',
            'codigo' => 'B001', 'ingreso' => '2026-01-01',
        ]);
        $this->actingAs($usuario)->post('/configuraciones/contexto-operativo', ['id_sucursal' => $sucursal, 'periodo' => 2026])->assertRedirect();

        $this->get('/rrhh/empleados')->assertOk()->assertDontSee('Persona externa')->assertDontSee('Otra ubicación');
        $this->get('/rrhh/empleados/'.$empleadoExterno.'/editar')->assertNotFound();
        $this->get('/rrhh/empleados/'.$empleadoOtraSucursal.'/editar')->assertNotFound();

        $normal = User::factory()->create(['id_empresa' => $usuario->id_empresa, 'is_admin' => false]);
        $this->actingAs($normal)->post('/configuraciones/contexto-operativo', ['id_sucursal' => $sucursal, 'periodo' => 2026])->assertRedirect();
        $this->get('/rrhh/nomina')->assertForbidden();
    }

    private function empresa(string $ruc): array
    {
        $empresa = EmpresaModel::create(['ruc' => $ruc, 'razon_social' => 'Empresa de prueba']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $sucursal = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $empresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);

        return [$usuario, $sucursal];
    }
}
