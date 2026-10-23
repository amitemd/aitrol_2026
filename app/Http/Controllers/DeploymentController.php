<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class DeploymentController extends Controller
{
    /**
     * @var array<string, array{
     *     label: string,
     *     description: string,
     *     icon: string,
     *     button: string,
     *     confirmation: string|null,
     *     requires_manifest: bool,
     *     commands: list<array{command: string, arguments: array<string, bool>}>
     * }>
     */
    private const OPERATIONS = [
        'deploy' => [
            'label' => 'Finalizar despliegue',
            'description' => 'Limpia cachés, ejecuta migraciones pendientes, optimiza Laravel y reinicia las colas. No ejecuta Git, Composer ni npm.',
            'icon' => 'fa-rocket',
            'button' => 'btn-danger',
            'confirmation' => 'DESPLEGAR',
            'requires_manifest' => true,
            'commands' => [
                ['command' => 'optimize:clear', 'arguments' => []],
                ['command' => 'migrate', 'arguments' => ['--force' => true, '--isolated' => true]],
                ['command' => 'optimize', 'arguments' => []],
                ['command' => 'queue:restart', 'arguments' => []],
            ],
        ],
        'migrate' => [
            'label' => 'Ejecutar migraciones',
            'description' => 'Aplica solamente las migraciones pendientes mediante migrate --force --isolated.',
            'icon' => 'fa-database',
            'button' => 'btn-warning',
            'confirmation' => 'MIGRAR',
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'migrate', 'arguments' => ['--force' => true, '--isolated' => true]],
            ],
        ],
        'optimize-clear' => [
            'label' => 'Limpiar todas las cachés',
            'description' => 'Ejecuta optimize:clear para limpiar configuración, rutas, vistas, eventos y caché de la aplicación.',
            'icon' => 'fa-eraser',
            'button' => 'btn-primary',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'optimize:clear', 'arguments' => []],
            ],
        ],
        'view-clear' => [
            'label' => 'Limpiar vistas',
            'description' => 'Elimina las vistas Blade compiladas con view:clear.',
            'icon' => 'fa-eye',
            'button' => 'btn-info',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'view:clear', 'arguments' => []],
            ],
        ],
        'cache-clear' => [
            'label' => 'Limpiar caché',
            'description' => 'Vacía el almacén de caché configurado mediante cache:clear.',
            'icon' => 'fa-trash',
            'button' => 'btn-info',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'cache:clear', 'arguments' => []],
            ],
        ],
        'config-clear' => [
            'label' => 'Limpiar configuración',
            'description' => 'Elimina la caché de configuración mediante config:clear.',
            'icon' => 'fa-sliders',
            'button' => 'btn-default',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'config:clear', 'arguments' => []],
            ],
        ],
        'route-clear' => [
            'label' => 'Limpiar rutas',
            'description' => 'Elimina la caché de rutas mediante route:clear.',
            'icon' => 'fa-random',
            'button' => 'btn-default',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'route:clear', 'arguments' => []],
            ],
        ],
        'optimize' => [
            'label' => 'Regenerar optimizaciones',
            'description' => 'Reconstruye las cachés de producción mediante optimize.',
            'icon' => 'fa-bolt',
            'button' => 'btn-success',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'optimize', 'arguments' => []],
            ],
        ],
        'queue-restart' => [
            'label' => 'Reiniciar colas',
            'description' => 'Solicita el reinicio seguro de los workers con queue:restart.',
            'icon' => 'fa-refresh',
            'button' => 'btn-default',
            'confirmation' => null,
            'requires_manifest' => false,
            'commands' => [
                ['command' => 'queue:restart', 'arguments' => []],
            ],
        ],
    ];

    public function index(): View
    {
        $this->authorizeAdministrator();

        return view('deployment.index', [
            'operations' => self::OPERATIONS,
            'status' => [
                'environment' => app()->environment(),
                'laravel' => app()->version(),
                'php' => PHP_VERSION,
                'maintenance' => app()->isDownForMaintenance(),
                'debug' => (bool) config('app.debug'),
                'manifest' => is_file((string) config('deployment.asset_manifest')),
                'database' => (string) config('database.default'),
                'cache' => (string) config('cache.default'),
            ],
        ]);
    }

    public function execute(Request $request): RedirectResponse
    {
        $this->authorizeAdministrator();

        $validated = $request->validate([
            'operation' => ['required', 'string', Rule::in(array_keys(self::OPERATIONS))],
            'confirmation' => ['nullable', 'string', 'max:30'],
        ]);

        $operationKey = $validated['operation'];
        $operation = self::OPERATIONS[$operationKey];

        if ($operation['confirmation'] !== null
            && ($validated['confirmation'] ?? '') !== $operation['confirmation']) {
            return back()
                ->withErrors(['confirmation' => "Escriba {$operation['confirmation']} para confirmar la operación."])
                ->withInput();
        }

        if ($operation['requires_manifest'] && ! is_file((string) config('deployment.asset_manifest'))) {
            return back()->with('deployment_result', [
                'successful' => false,
                'title' => $operation['label'],
                'output' => 'No existe public/build/manifest.json. Compile los recursos antes de finalizar el despliegue.',
            ]);
        }

        $lock = Cache::lock('aitrol:deployment', 900);

        if (! $lock->get()) {
            return back()->with('deployment_result', [
                'successful' => false,
                'title' => $operation['label'],
                'output' => 'Ya existe otra operación de despliegue o mantenimiento en ejecución.',
            ]);
        }

        $outputs = [];
        $successful = true;

        try {
            set_time_limit(0);

            foreach ($operation['commands'] as $step) {
                $outputs[] = '$ php artisan '.$step['command'];
                $exitCode = Artisan::call($step['command'], $step['arguments']);
                $commandOutput = trim(Artisan::output());

                if ($commandOutput !== '') {
                    $outputs[] = $commandOutput;
                }

                if ($exitCode !== 0) {
                    $successful = false;
                    $outputs[] = "El comando terminó con código {$exitCode}.";
                    break;
                }
            }

            Log::notice('Operación administrativa ejecutada desde el panel de despliegue.', [
                'operation' => $operationKey,
                'successful' => $successful,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);
        } catch (Throwable $exception) {
            $successful = false;
            report($exception);
            $outputs[] = 'La operación no pudo completarse: '.$exception->getMessage();
        } finally {
            $lock->release();
        }

        return back()->with('deployment_result', [
            'successful' => $successful,
            'title' => $operation['label'],
            'output' => Str::limit(implode(PHP_EOL.PHP_EOL, $outputs), 30000),
        ]);
    }

    private function authorizeAdministrator(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->id_tipo_usuario === 1,
            403,
            'No tiene permisos para administrar despliegues.',
        );
    }
}
