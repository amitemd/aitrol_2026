<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Jobs\EjecutarMantenimientoServidor;
use App\Models\User;
use App\Services\MantenimientoServidor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

final class MantenimientoServidorTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const RUTA = '/configuraciones/mantenimiento';

    public function test_restringe_acceso_y_solo_admite_acciones_habilitadas(): void
    {
        config(['mantenimiento.habilitado' => false]);
        $administrador = $this->administrador(false);
        $this->actingAs($administrador)->get(self::RUTA)->assertForbidden();
        $this->postJson(self::RUTA.'/migrar')->assertForbidden();

        $administrador->forceFill(['is_admin_global' => true])->save();
        $this->actingAs($administrador)->get(self::RUTA)->assertOk()->assertSee('Mantenimiento del servidor');
        $this->postJson(self::RUTA.'/migrar')->assertForbidden();
        config(['mantenimiento.habilitado' => true, 'mantenimiento.git' => false]);
        $this->postJson(self::RUTA.'/git')->assertForbidden();
        $this->postJson(self::RUTA.'/comando')->assertNotFound();
        $this->assertDatabaseCount('ejecuciones_mantenimiento', 0);
    }

    public function test_serializa_ejecuciones_y_muestra_estado_y_error_sin_exponer_la_salida(): void
    {
        config(['mantenimiento.habilitado' => true]);
        Bus::fake([EjecutarMantenimientoServidor::class]);
        Process::fake([
            '*migrate*' => Process::result(output: 'Define AITROL_EMPRESA_INICIAL. CLAVE_CONFIDENCIAL', exitCode: 1),
            '*route:clear*' => Process::result(),
        ])->preventStrayProcesses();
        $this->actingAs($this->administrador());
        $id = $this->postJson(self::RUTA.'/migrar')->assertAccepted()->assertJsonPath('estados.migrar.estado', 'pendiente')->json('id');
        Bus::assertDispatchedAfterResponse(EjecutarMantenimientoServidor::class, fn ($trabajo): bool => $trabajo->ejecucion === $id);
        $this->postJson(self::RUTA.'/rutas')->assertConflict();
        app(MantenimientoServidor::class)->ejecutar($id);
        $this->getJson(self::RUTA.'/estado')->assertOk()->assertJsonPath('ocupado', false)
            ->assertJsonPath('estados.migrar.estado', 'fallido')
            ->assertSee('AITROL_EMPRESA_INICIAL')->assertDontSee('CLAVE_CONFIDENCIAL');
        Process::assertRan(fn ($proceso): bool => in_array('--force', $proceso->command, true));

        $siguiente = $this->postJson(self::RUTA.'/rutas')->assertAccepted()->json('id');
        app(MantenimientoServidor::class)->ejecutar($siguiente);
        $this->getJson(self::RUTA.'/estado')->assertJsonPath('estados.rutas.estado', 'correcto');
    }

    public function test_git_se_detiene_si_hay_cambios_locales(): void
    {
        config(['mantenimiento.habilitado' => true, 'mantenimiento.git' => true, 'mantenimiento.rama' => 'main']);
        Process::fake([
            '*--show-toplevel*' => Process::result(output: base_path().PHP_EOL),
            '*symbolic-ref*' => Process::result(output: "main\n"),
            '*get-url*' => Process::result(),
            '*--porcelain*' => Process::result(output: " M routes/web.php\n"),
        ])->preventStrayProcesses();
        $usuario = $this->administrador();
        $this->actingAs($usuario);
        $servicio = app(MantenimientoServidor::class);

        $servicio->ejecutar($servicio->registrar('git', $usuario));

        $this->assertDatabaseHas('ejecuciones_mantenimiento', [
            'accion' => 'git', 'estado' => 'fallido', 'detalle' => 'El repositorio tiene cambios locales o Git no pudo comprobarlos.',
        ]);
        Process::assertDidntRun(fn ($proceso): bool => in_array('fetch', $proceso->command, true));
    }

    public function test_identifica_errores_de_permisos_web_sin_publicar_la_salida_del_proceso(): void
    {
        config(['mantenimiento.habilitado' => true, 'mantenimiento.git' => true]);
        Process::fake([
            '*--show-toplevel*' => Process::result(errorOutput: 'fatal: detected dubious ownership in repository at RUTA_PRIVADA', exitCode: 128),
            '*config:clear*' => Process::result(errorOutput: 'unlink(bootstrap/cache/config.php): Permission denied RECURSO_PRIVADO', exitCode: 1),
        ])->preventStrayProcesses();
        $usuario = $this->administrador();
        $servicio = app(MantenimientoServidor::class);
        $servicio->ejecutar($servicio->registrar('git', $usuario));
        $servicio->ejecutar($servicio->registrar('config', $usuario));

        $this->actingAs($usuario)->getJson(self::RUTA.'/estado')
            ->assertJsonPath('estados.git.estado', 'fallido')
            ->assertJsonPath('estados.config.estado', 'fallido')
            ->assertJsonPath('estados.git.detalle', fn (string $detalle): bool => str_contains($detalle, 'Git bloqueó el repositorio por su propietario'))
            ->assertJsonPath('estados.config.detalle', fn (string $detalle): bool => str_contains($detalle, 'escritura en bootstrap/cache'))
            ->assertDontSee('RUTA_PRIVADA')->assertDontSee('RECURSO_PRIVADO');
    }

    public function test_git_aplica_solo_la_revision_validada_y_limpia_el_arranque(): void
    {
        $revision = str_repeat('a', 40);
        config(['mantenimiento.habilitado' => true, 'mantenimiento.git' => true, 'mantenimiento.rama' => 'main', 'mantenimiento.remoto' => 'origin']);
        Process::fake([
            '*--show-toplevel*' => Process::result(output: base_path().PHP_EOL),
            '*symbolic-ref*' => Process::result(output: "main\n"),
            '*get-url*' => Process::result(),
            '*--porcelain*' => Process::result(),
            '*fetch*' => Process::result(),
            '*FETCH_HEAD*' => Process::result(output: $revision),
            '*merge-base*' => Process::result(),
            '*--name-only*' => Process::result(),
            '*--ff-only*' => Process::result(),
            '*:clear*' => Process::result(),
        ])->preventStrayProcesses();
        $usuario = $this->administrador();
        $servicio = app(MantenimientoServidor::class);

        $servicio->ejecutar($servicio->registrar('git', $usuario));

        $this->assertDatabaseHas('ejecuciones_mantenimiento', ['accion' => 'git', 'estado' => 'correcto']);
        Process::assertRan(fn ($proceso): bool => $proceso->command === ['git', 'fetch', '--no-tags', 'origin', 'refs/heads/main']);
        Process::assertRan(fn ($proceso): bool => $proceso->command === ['git', 'merge', '--ff-only', '--no-edit', $revision]);
        Process::assertRan(fn ($proceso): bool => in_array('route:clear', $proceso->command, true));
    }

    private function administrador(bool $global = true): User
    {
        $empresa = EmpresaModel::create(['ruc' => '1273300000001', 'razon_social' => 'Empresa de prueba']);

        return User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true, 'is_admin_global' => $global]);
    }
}
