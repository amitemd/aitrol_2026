<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;

class DeployApplicationTest extends TestCase
{
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
