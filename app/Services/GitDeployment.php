<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class GitDeployment
{
    public function __construct(private CommandRunner $commandRunner) {}

    /**
     * @return array{
     *     enabled: bool,
     *     available: bool,
     *     clean: bool,
     *     writable: bool,
     *     mode: string,
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
        $usesHelper = $this->usesHelper();
        $status = [
            'enabled' => (bool) config('deployment.git.enabled', true),
            'available' => false,
            'clean' => false,
            'writable' => $usesHelper ? false : is_writable(base_path()) && is_writable(base_path('.git')),
            'mode' => $usesHelper ? 'helper' : 'direct',
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

        if ($usesHelper && ($configurationError = $this->helperConfigurationError()) !== null) {
            $status['error'] = $configurationError;

            return $status;
        }

        try {
            $repositoryCheck = $this->process($this->gitCommand(['rev-parse', '--is-inside-work-tree']));
        } catch (Throwable $exception) {
            $status['error'] = 'No fue posible ejecutar Git: '.Str::limit($exception->getMessage(), 200);

            return $status;
        }

        if (! $repositoryCheck->isSuccessful() || trim($repositoryCheck->getOutput()) !== 'true') {
            $status['error'] = $this->processError($repositoryCheck, 'El directorio de la aplicación no es un repositorio Git.');

            return $status;
        }

        try {
            $branch = $this->process($this->gitCommand(['branch', '--show-current']));
            $commit = $this->process($this->gitCommand(['rev-parse', '--short', 'HEAD']));
            $changes = $this->process($this->gitCommand(['status', '--porcelain']));
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

        if ($usesHelper) {
            try {
                $helperCheck = $this->process($this->helperCommand(check: true));
            } catch (Throwable $exception) {
                $status['error'] = 'No fue posible verificar el ayudante privilegiado: '.Str::limit($exception->getMessage(), 200);

                return $status;
            }

            if (! $helperCheck->isSuccessful()) {
                $status['error'] = 'El ayudante privilegiado no está listo: '.$this->processError(
                    $helperCheck,
                    'verifique sudoers, el usuario de despliegue y el ejecutable configurado.',
                );

                return $status;
            }

            $status['writable'] = true;
        }

        if (! $status['writable']) {
            $status['error'] = 'El usuario de PHP no tiene permisos de escritura. Configure el ayudante privilegiado de despliegue; no use chmod 777.';
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

        $command = $this->usesHelper()
            ? $this->helperCommand()
            : $this->gitCommand(['pull', '--ff-only', $status['remote'], $status['target']]);
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

    private function usesHelper(): bool
    {
        return trim((string) config('deployment.git.helper')) !== '';
    }

    private function helperConfigurationError(): ?string
    {
        $helper = trim((string) config('deployment.git.helper'));
        $user = trim((string) config('deployment.git.user'));

        if (! str_starts_with($helper, '/')) {
            return 'DEPLOY_GIT_HELPER debe contener una ruta absoluta.';
        }

        if ($user === '') {
            return 'Defina DEPLOY_GIT_USER cuando configure DEPLOY_GIT_HELPER.';
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function helperCommand(bool $check = false): array
    {
        $helper = trim((string) config('deployment.git.helper'));
        $user = trim((string) config('deployment.git.user'));
        $command = ['sudo', '-n', '-H'];

        if ($user !== '') {
            $command[] = '-u';
            $command[] = $user;
        }

        $command[] = $helper;

        if ($check) {
            $command[] = '--check';
        }

        return $command;
    }

    /**
     * @param  list<string>  $arguments
     * @return list<string>
     */
    private function gitCommand(array $arguments): array
    {
        return [
            'git',
            '-c',
            'safe.directory='.base_path(),
            ...$arguments,
        ];
    }

    /**
     * @param  list<string>  $command
     */
    private function process(array $command, float $timeout = 10): Process
    {
        return $this->commandRunner->run($command, base_path(), $timeout);
    }

    private function processError(Process $process, string $fallback): string
    {
        $error = trim($process->getErrorOutput().PHP_EOL.$process->getOutput());

        return $error !== '' ? $error : $fallback;
    }
}
