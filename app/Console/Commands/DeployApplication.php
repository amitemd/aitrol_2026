<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class DeployApplication extends Command
{
    protected $signature = 'app:deploy
        {--no-migrate : No ejecutar migraciones pendientes}
        {--no-optimize : No generar las cachés optimizadas de producción}
        {--no-maintenance : No activar el modo mantenimiento desde este comando}
        {--no-queue-restart : No reiniciar los workers de colas}
        {--no-asset-check : No comprobar el manifiesto generado por Vite}
        {--dry-run : Mostrar el plan sin modificar la aplicación}
        {--force : Confirmar la ejecución en producción}';

    protected $description = 'Finaliza un despliegue limpiando cachés, migrando y optimizando la aplicación';

    public function handle(): int
    {
        if (app()->isProduction() && ! $this->option('force') && ! $this->option('dry-run')) {
            $this->error('En producción debe ejecutar el comando con --force.');

            return self::FAILURE;
        }

        $steps = $this->steps();

        if ($this->option('dry-run')) {
            $this->info('Plan de despliegue:');

            if (! $this->option('no-asset-check')) {
                $this->line('  - Verificar frontend: public/build/manifest.json');
            }

            foreach ($steps as $step) {
                $this->line('  - '.$step['display']);
            }

            return self::SUCCESS;
        }

        $assetManifest = (string) config('deployment.asset_manifest');

        if (! $this->option('no-asset-check') && ! is_file($assetManifest)) {
            $this->error('No existe public/build/manifest.json. Ejecute npm ci y npm run build antes del despliegue.');

            return self::FAILURE;
        }

        $lock = Cache::lock('aitrol:deployment', 3600);

        if (! $lock->get()) {
            $this->error('Ya existe otro despliegue de AITROL en ejecución.');

            return self::FAILURE;
        }

        $maintenanceActivated = false;
        $wasAlreadyDown = app()->isDownForMaintenance();

        try {
            if (! $this->option('no-maintenance') && ! $wasAlreadyDown) {
                $this->runArtisan('down', [
                    '--retry' => 60,
                    '--refresh' => 15,
                ]);
                $maintenanceActivated = true;
            }

            foreach ($steps as $step) {
                $this->runArtisan($step['command'], $step['arguments']);
            }

            $this->newLine();
            $this->info('Despliegue finalizado correctamente.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->newLine();
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if ($maintenanceActivated) {
                $this->call('up');
            }

            $lock->release();
        }
    }

    /**
     * @return list<array{display: string, command: string, arguments: array<string, bool>}>
     */
    private function steps(): array
    {
        $steps = [
            [
                'display' => 'Limpiar cachés: php artisan optimize:clear',
                'command' => 'optimize:clear',
                'arguments' => [],
            ],
        ];

        if (! $this->option('no-migrate')) {
            $steps[] = [
                'display' => 'Migrar base: php artisan migrate --force --isolated',
                'command' => 'migrate',
                'arguments' => ['--force' => true, '--isolated' => true],
            ];
        }

        if (! $this->option('no-optimize')) {
            $steps[] = [
                'display' => 'Generar cachés de producción: php artisan optimize',
                'command' => 'optimize',
                'arguments' => [],
            ];
        }

        if (! $this->option('no-queue-restart')) {
            $steps[] = [
                'display' => 'Reiniciar colas: php artisan queue:restart',
                'command' => 'queue:restart',
                'arguments' => [],
            ];
        }

        return $steps;
    }

    /** @param array<string, bool|int> $arguments */
    private function runArtisan(string $command, array $arguments = []): void
    {
        $this->newLine();
        $this->components->info('Ejecutando: php artisan '.$command);

        $process = new Process([
            PHP_BINARY,
            base_path('artisan'),
            $command,
            ...$this->formatArguments($arguments),
        ], base_path());
        $process->setTimeout(null);

        $exitCode = $process->run(
            fn (string $type, string $output) => $this->output->write($output),
        );

        if ($exitCode !== self::SUCCESS) {
            throw new RuntimeException("Falló el comando php artisan {$command}.");
        }
    }

    /**
     * @param  array<string, bool|int>  $arguments
     * @return list<string>
     */
    private function formatArguments(array $arguments): array
    {
        $formatted = [];

        foreach ($arguments as $name => $value) {
            if ($value === false) {
                continue;
            }

            $formatted[] = $value === true ? $name : "{$name}={$value}";
        }

        return $formatted;
    }
}
