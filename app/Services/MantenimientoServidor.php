<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Process;
use LogicException;
use Throwable;

final class MantenimientoServidor
{
    public const ACCIONES = [
        'git' => ['Desplegar desde Git', 'Actualiza desde la rama configurada, limpia las cachés de Laravel y aplica las migraciones pendientes.', 'bi-git'],
        'migrar' => ['Aplicar migraciones', 'Ejecuta las migraciones pendientes en la base configurada.', 'bi-database-up'],
        'cache' => ['Vaciar caché de aplicación', 'Elimina los valores de la caché predeterminada.', 'bi-eraser'],
        'config' => ['Limpiar configuración', 'Elimina la configuración compilada.', 'bi-sliders'],
        'rutas' => ['Limpiar rutas', 'Elimina las rutas compiladas.', 'bi-signpost-split'],
        'vistas' => ['Limpiar vistas', 'Elimina las vistas Blade compiladas.', 'bi-layout-text-window'],
        'limpiar' => ['Limpiar cachés de Laravel', 'Limpia configuración, rutas, eventos y vistas.', 'bi-arrow-repeat'],
        'optimizar' => ['Optimizar Laravel', 'Compila configuración, rutas, eventos y vistas.', 'bi-lightning-charge'],
    ];

    public function disponible(string $accion): bool
    {
        return (bool) config('mantenimiento.habilitado') && ($accion !== 'git' || config('mantenimiento.git'));
    }

    public function estado(): array
    {
        $this->expirar();
        $tabla = $this->tabla();
        $ultimas = $this->tabla()->selectRaw('max(id)')->groupBy('accion');

        return [
            'ocupado' => (clone $tabla)->where('bloqueo', 1)->exists(),
            'estados' => $tabla->whereIn('id', $ultimas)->get([
                'accion', 'estado', 'detalle', 'iniciado_at', 'finalizado_at',
            ])->keyBy('accion')->all(),
        ];
    }

    public function registrar(string $accion, User $usuario): int
    {
        abort_unless(isset(self::ACCIONES[$accion]), 404);
        abort_unless($this->disponible($accion), 403, 'Esta acción está deshabilitada en el servidor.');
        $this->expirar();

        try {
            return (int) $this->tabla()->insertGetId([
                'accion' => $accion, 'id_usuario' => $usuario->id, 'estado' => 'pendiente', 'bloqueo' => 1,
                'detalle' => 'En espera del servicio de mantenimiento del servidor.', 'created_at' => now(), 'updated_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            abort(409, 'Ya hay una acción de mantenimiento en curso.');
        }
    }

    public function procesarPendiente(): ?int
    {
        $this->expirar();
        $id = $this->tabla()->where('bloqueo', 1)->where('estado', 'pendiente')->value('id');
        if ($id === null || ! $this->ejecutar((int) $id)) {
            return null;
        }

        return (int) $id;
    }

    private function ejecutar(int $id): bool
    {
        $ejecucion = $this->tabla()->where('id', $id);
        if ((clone $ejecucion)->where('estado', 'pendiente')->update([
            'estado' => 'ejecutando', 'iniciado_at' => now(), 'updated_at' => now(),
        ]) !== 1) {
            return false;
        }

        try {
            $registro = $ejecucion->first();
            $usuario = User::query()->where('activo', true)->find($registro->id_usuario);
            $detalle = match (true) {
                $usuario === null || ! Gate::forUser($usuario)->allows('administrar-servidor') => 'El usuario ya no tiene autorización para ejecutar esta acción.',
                ! $this->disponible($registro->accion) => 'Esta acción fue deshabilitada antes de ejecutarse.',
                $registro->accion === 'git' => $this->actualizarGit($id),
                default => $this->artisan($id, $this->comandos($registro->accion)),
            };
        } catch (Throwable $error) {
            rescue(fn () => logger()->warning('La ejecución de mantenimiento se interrumpió.', [
                'ejecucion' => $id, 'tipo' => $error::class,
            ]), report: false);
            $detalle = function_exists('proc_open')
                ? 'El proceso se interrumpió o superó el tiempo de espera. Revisa el servidor antes de reintentar.'
                : 'El PHP CLI tiene proc_open deshabilitado y no puede iniciar comandos.';
        }

        $detalleExito = ($registro->accion ?? null) === 'git' ? 'Despliegue completado. Cachés de Laravel limpias y migraciones aplicadas.' : 'Acción completada.';
        $ejecucion->where('estado', 'ejecutando')->update([
            'estado' => $detalle === null ? 'correcto' : 'fallido', 'bloqueo' => null,
            'detalle' => $detalle ?? $detalleExito, 'finalizado_at' => now(), 'updated_at' => now(),
        ]);

        return true;
    }

    private function actualizarGit(int $id): ?string
    {
        $remoto = (string) config('mantenimiento.remoto');
        $rama = (string) config('mantenimiento.rama');
        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/D', $remoto)
            || ! preg_match('|^[a-zA-Z0-9][a-zA-Z0-9._/-]*$|D', $rama)) {
            return 'La rama o el remoto configurados para el despliegue no son válidos.';
        }

        $this->paso($id, 'Comprobando el repositorio y la rama.');
        $raiz = $this->git(['rev-parse', '--show-toplevel']);
        if ($raiz->failed()) {
            $salida = $this->salidaProceso($raiz);

            return match (true) {
                str_contains($salida, 'dubious ownership') => 'Git bloqueó el repositorio por su propietario. Comprueba la cuenta del servicio de mantenimiento.',
                str_contains($salida, 'not a git repository') => 'Git no encontró el repositorio. Comprueba la ruta y la lectura de .git con la cuenta de despliegue.',
                $this->permisoDenegado($salida) => 'La cuenta de despliegue no tiene permiso para abrir el repositorio Git.',
                $raiz->exitCode() === 127, str_contains($salida, 'command not found') => 'Git no está disponible en el PATH del servicio de mantenimiento.',
                default => 'Git no pudo determinar la raíz del repositorio desde el servicio de mantenimiento.',
            };
        }
        $directorio = realpath(trim($raiz->output()));
        if ($directorio === false || $directorio !== realpath(base_path())) {
            return 'La carpeta de la aplicación debe ser la raíz del repositorio Git.';
        }
        $actual = $this->git(['symbolic-ref', '--quiet', '--short', 'HEAD']);
        if (! $actual->successful() || trim($actual->output()) !== $rama) {
            return 'La rama activa del servidor debe coincidir con la rama de despliegue.';
        }
        if (! $this->git(['remote', 'get-url', $remoto])->successful()) {
            return 'El remoto configurado no existe en este repositorio.';
        }
        $cambios = $this->git(['status', '--porcelain', '--untracked-files=normal']);
        if (! $cambios->successful() || trim($cambios->output()) !== '') {
            return 'El repositorio tiene cambios locales o Git no pudo comprobarlos.';
        }

        $this->paso($id, 'Descargando la revisión remota.');
        $fetch = $this->git(['fetch', '--no-tags', $remoto, 'refs/heads/'.$rama], 90);
        if (! $fetch->successful()) {
            return 'Git no pudo descargar la rama. Revisa la conectividad y la credencial de lectura del servidor.';
        }
        $revision = trim($this->git(['rev-parse', '--verify', 'FETCH_HEAD^{commit}'])->output());
        if (! preg_match('/\A(?:[0-9a-f]{40}|[0-9a-f]{64})\z/', $revision)
            || ! $this->git(['merge-base', '--is-ancestor', 'HEAD', $revision])->successful()) {
            return 'La actualización no es de avance rápido. Revisa las revisiones de la rama local y remota.';
        }
        $dependencias = $this->git(['diff', '--name-only', 'HEAD', $revision, '--', 'composer.json', 'composer.lock', 'package.json', 'package-lock.json']);
        if (! $dependencias->successful() || trim($dependencias->output()) !== '') {
            return 'La revisión cambia las dependencias o no se pudo comprobarlas. Usa el procedimiento de despliegue que instala dependencias y genera los recursos.';
        }

        $this->paso($id, 'Aplicando la actualización de avance rápido.');
        if (! $this->git(['merge', '--ff-only', '--no-edit', $revision], 90)->successful()) {
            return 'Git descargó la revisión, pero no pudo aplicarla. Revisa permisos y el estado del repositorio.';
        }

        return $this->artisan($id, [...$this->comandos('limpiar'), ...$this->comandos('migrar')], 'El código se actualizó. ');
    }

    private function git(array $argumentos, int $tiempo = 15): ProcessResult
    {
        return Process::path(base_path())->env([
            'GIT_TERMINAL_PROMPT' => '0', 'GCM_INTERACTIVE' => 'Never',
        ])->timeout($tiempo)->run(['git', ...$argumentos]);
    }

    private function artisan(int $id, array $comandos, string $prefijo = ''): ?string
    {
        foreach ($comandos as $comando) {
            $this->paso($id, 'Ejecutando '.$comando.'.');
            $argumentos = [(string) config('mantenimiento.php'), base_path('artisan'), $comando, '--no-interaction', '--no-ansi'];
            if ($comando === 'migrate') {
                $argumentos[] = '--force';
            }
            $resultado = Process::path(base_path())->timeout($comando === 'migrate' ? 420 : 35)->run($argumentos);
            if ($resultado->failed()) {
                $salida = $this->salidaProceso($resultado);
                $causa = match (true) {
                    $comando === 'migrate' && str_contains($salida, 'aitrol_empresa_inicial') => 'La migración requiere configurar AITROL_EMPRESA_INICIAL con la empresa que recibirá los registros anteriores.',
                    str_contains($salida, 'could not find driver') => 'Al PHP CLI le falta el controlador de la base de datos.',
                    str_contains($salida, 'readonly database'), str_contains($salida, 'read-only database') => 'La base SQLite está en modo de solo lectura para el servicio de mantenimiento.',
                    str_contains($salida, 'sqlstate') && str_contains($salida, 'access denied') => 'La base de datos rechazó la conexión o los permisos del usuario configurado.',
                    str_contains($salida, 'bootstrap/cache') && (str_contains($salida, 'must be present and writable') || $this->permisoDenegado($salida)) => 'La cuenta de mantenimiento necesita escritura en bootstrap/cache.',
                    str_contains($salida, 'storage/framework') && $this->permisoDenegado($salida) => 'La cuenta de mantenimiento necesita escritura en storage/framework.',
                    str_contains($salida, 'storage/logs') && $this->permisoDenegado($salida) => 'La cuenta de mantenimiento necesita escritura en storage/logs.',
                    str_contains($salida, 'could not open input file') => 'El PHP CLI no pudo abrir el archivo artisan desde la carpeta de la aplicación.',
                    $resultado->exitCode() === 127, str_contains($salida, 'command not found') => 'PHP CLI no está disponible. Comprueba AITROL_PHP_CLI.',
                    str_contains($salida, 'sqlstate') => 'La base de datos devolvió un error. Comprueba el estado de las migraciones con el PHP CLI del servidor.',
                    $this->permisoDenegado($salida) => 'El servicio recibió un error de permisos. Comprueba el ejecutable, los archivos de Laravel y el contexto de SELinux.',
                    default => 'Reproduce el comando con la cuenta del servicio de mantenimiento.',
                };

                return $prefijo.'Falló '.$comando.' con código '.$resultado->exitCode().'. '.$causa;
            }
        }

        return null;
    }

    private function salidaProceso(ProcessResult $resultado): string
    {
        return strtolower(str_replace('\\', '/', $resultado->errorOutput()."\n".$resultado->output()));
    }

    private function permisoDenegado(string $salida): bool
    {
        return str_contains($salida, 'permission denied') || str_contains($salida, 'operation not permitted');
    }

    private function comandos(string $accion): array
    {
        return match ($accion) {
            'migrar' => ['migrate'],
            'cache' => ['cache:clear'],
            'config' => ['config:clear'],
            'rutas' => ['route:clear'],
            'vistas' => ['view:clear'],
            'limpiar' => ['config:clear', 'route:clear', 'event:clear', 'view:clear'],
            'optimizar' => ['config:cache', 'event:cache', 'route:cache', 'view:cache'],
            default => throw new LogicException('Acción de mantenimiento desconocida.'),
        };
    }

    private function paso(int $id, string $detalle): void
    {
        $this->tabla()->where('id', $id)->where('estado', 'ejecutando')->update(['detalle' => $detalle, 'updated_at' => now()]);
    }

    private function expirar(): void
    {
        foreach ([
            ['pendiente', 'created_at', 2, 'El servicio de mantenimiento no recogió la solicitud. Comprueba que aitrol-mantenimiento.timer esté activo.'],
            ['ejecutando', 'iniciado_at', 20, 'El servicio de mantenimiento no terminó a tiempo. Revisa su registro antes de reintentar.'],
        ] as [$estado, $columna, $minutos, $detalle]) {
            $this->tabla()->where('bloqueo', 1)->where('estado', $estado)->where($columna, '<', now()->subMinutes($minutos))->update([
                'estado' => 'fallido', 'bloqueo' => null, 'detalle' => $detalle, 'finalizado_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function tabla(): Builder
    {
        return DB::table('ejecuciones_mantenimiento');
    }
}
