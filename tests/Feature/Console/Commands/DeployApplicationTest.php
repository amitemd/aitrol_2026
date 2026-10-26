<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DeployApplicationTest extends TestCase
{
    public function test_deployment_stops_when_the_vite_manifest_is_missing(): void
    {
        config()->set('deployment.asset_manifest', base_path('tests/fixtures/missing-manifest.json'));

        $this->artisan('app:deploy', [
            '--no-maintenance' => true,
            '--no-migrate' => true,
            '--no-optimize' => true,
            '--no-queue-restart' => true,
        ])
            ->expectsOutputToContain('No existe public/build/manifest.json')
            ->assertFailed();
    }

    public function test_asset_manifest_check_can_only_be_skipped_explicitly(): void
    {
        config()->set('deployment.asset_manifest', base_path('tests/fixtures/missing-manifest.json'));

        $this->artisan('app:deploy', [
            '--no-asset-check' => true,
            '--no-maintenance' => true,
            '--no-migrate' => true,
            '--no-optimize' => true,
            '--no-queue-restart' => true,
        ])
            ->expectsOutputToContain('Despliegue finalizado correctamente.')
            ->assertSuccessful();
    }

    public function test_production_deployment_requires_the_force_option(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->artisan('app:deploy', [
            '--no-asset-check' => true,
            '--no-maintenance' => true,
            '--no-migrate' => true,
            '--no-optimize' => true,
            '--no-queue-restart' => true,
        ])
            ->expectsOutputToContain('En producción debe ejecutar el comando con --force.')
            ->assertFailed();
    }

    public function test_a_second_deployment_cannot_start_while_the_lock_is_held(): void
    {
        $lock = Cache::lock('aitrol:deployment', 3600);
        $this->assertTrue($lock->get());

        try {
            $this->artisan('app:deploy', [
                '--no-asset-check' => true,
                '--no-maintenance' => true,
                '--no-migrate' => true,
                '--no-optimize' => true,
                '--no-queue-restart' => true,
            ])
                ->expectsOutputToContain('Ya existe otro despliegue de AITROL en ejecución.')
                ->assertFailed();
        } finally {
            $lock->release();
        }
    }

    public function test_dry_run_displays_the_complete_deployment_plan_without_enabling_maintenance(): void
    {
        $this->artisan('app:deploy', ['--dry-run' => true])
            ->expectsOutputToContain('public/build/manifest.json')
            ->expectsOutputToContain('php artisan optimize:clear')
            ->expectsOutputToContain('php artisan migrate --force --isolated')
            ->expectsOutputToContain('php artisan optimize')
            ->expectsOutputToContain('php artisan queue:restart')
            ->assertSuccessful();

        $this->assertFalse(app()->isDownForMaintenance());
    }

    public function test_dry_run_omits_disabled_deployment_steps(): void
    {
        $this->artisan('app:deploy', [
            '--dry-run' => true,
            '--no-migrate' => true,
            '--no-optimize' => true,
            '--no-queue-restart' => true,
        ])
            ->expectsOutputToContain('php artisan optimize:clear')
            ->doesntExpectOutputToContain('php artisan migrate')
            ->doesntExpectOutputToContain('Generar cachés de producción')
            ->doesntExpectOutputToContain('php artisan queue:restart')
            ->assertSuccessful();
    }
}
