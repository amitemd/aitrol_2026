<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class GitDeployment
{
    /**
     * @return array{
     *     enabled: bool,
     *     available: bool,
     *     clean: bool,
     *     writable: bool,
     *     branch: string,
     *     remote: string,
     *     target: string,
     *     commit: string,
     *     error: string|null
     * }
     */
    public function status(): array
    {
        $remote = (string) config('deployment.git.remote', 'origin');
        $target = (string) config('deployment.git.branch', 'main');
        $status = [
            'enabled' => (bool) config('deployment.git.enabled', true),
            'available' => false,
            'clean' => false,
            'writable' => is_writable(base_path()) && is_writable(base_path('.git')),
            'branch' => '',
            'remote' => $remote,
            'target' => $target,
            'commit' => '',
            'error' => null,
        ];

        if (! $status['enabled']) {
            $status['error'] = 'La actualización por Git está deshabilitada en la configuración.';

            return $status;
        }

        try {
            $repositoryCheck = $this->process(['git', 'rev-parse', '--is-inside-work-tree']);
        } catch (Throwable $exception) {
            $status['error'] = 'No fue posible ejecutar Git: '.Str::limit($exception->getMessage(), 200);

            return $status;
        }

        if (! $repositoryCheck->isSuccessful() || trim($repositoryCheck->getOutput()) !== 'true') {
            $status['error'] = $this->processError($repositoryCheck, 'El directorio de la aplicación no es un repositorio Git.');

            return $status;
        }

        try {
            $branch = $this->process(['git', 'branch', '--show-current']);
            $commit = $this->process(['git', 'rev-parse', '--short', 'HEAD']);
            $changes = $this->process(['git', 'status', '--porcelain']);
        } catch (Throwable $exception) {
            $status['error'] = 'No fue posible consultar Git: '.Str::limit($exception->getMessage(), 200);

            return $status;
        }

        if (! $branch->isSuccessful() || ! $commit->isSuccessful() || ! $changes->isSuccessful()) {
            $status['error'] = 'No fue posible consultar el estado actual del repositorio.';

            return $status;
        }

        $status['available'] = true;
        $status['branch'] = trim($branch->getOutput());
        $status['commit'] = trim($commit->getOutput());
        $status['clean'] = trim($changes->getOutput()) === '';

        if (! $status['writable']) {
            $status['error'] = 'El usuario de PHP no tiene permisos de escritura sobre el proyecto o la carpeta .git.';
        } elseif ($status['branch'] !== $target) {
            $status['error'] = "La rama activa es {$status['branch']}; el despliegue está configurado para {$target}.";
        } elseif (! $status['clean']) {
            $status['error'] = 'Existen cambios locales o archivos sin seguimiento. Resuélvalos antes de actualizar.';
        }

        return $status;
    }

    /**
     * @return array{successful: bool, output: string}
     */
    public function pull(): array
    {
        $status = $this->status();

        if (! $status['enabled'] || ! $status['available'] || ! $status['writable'] || ! $status['clean'] || $status['branch'] !== $status['target']) {
            return [
                'successful' => false,
                'output' => $status['error'] ?? 'El repositorio no está listo para actualizarse.',
            ];
        }

        $command = ['git', 'pull', '--ff-only', $status['remote'], $status['target']];
        try {
            $process = $this->process($command, (float) config('deployment.git.timeout', 300));
        } catch (Throwable $exception) {
            return [
                'successful' => false,
                'output' => 'No fue posible ejecutar Git: '.Str::limit($exception->getMessage(), 500),
            ];
        }
        $output = trim($process->getOutput().PHP_EOL.$process->getErrorOutput());

        return [
            'successful' => $process->isSuccessful(),
            'output' => implode(PHP_EOL, [
                '$ '.implode(' ', $command),
                $output !== '' ? $output : 'Git no devolvió salida.',
            ]),
        ];
    }

    /**
     * @param  list<string>  $command
     */
    private function process(array $command, float $timeout = 10): Process
    {
        $process = new Process(
            command: $command,
            cwd: base_path(),
            env: ['GIT_TERMINAL_PROMPT' => '0'],
            timeout: $timeout,
        );
        $process->run();

        return $process;
    }

    private function processError(Process $process, string $fallback): string
    {
        $error = trim($process->getErrorOutput().PHP_EOL.$process->getOutput());

        return $error !== '' ? $error : $fallback;
    }
}
