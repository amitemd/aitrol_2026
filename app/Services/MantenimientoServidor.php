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
use RuntimeException;
use Throwable;

final class MantenimientoServidor
{
    public const ACCIONES = [
        'git' => ['Actualizar desde Git', 'Actualiza el código desde la rama configurada.', 'bi-git'],
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
                'detalle' => 'En espera de ejecución.', 'created_at' => now(), 'updated_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            abort(409, 'Ya hay una acción de mantenimiento en curso.');
        }
    }

    public function ejecutar(int $id): void
    {
        $ejecucion = $this->tabla()->where('id', $id);
        if ((clone $ejecucion)->where('estado', 'pendiente')->update([
            'estado' => 'ejecutando', 'iniciado_at' => now(), 'updated_at' => now(),
        ]) !== 1) {
            return;
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
            logger()->warning('La ejecución de mantenimiento se interrumpió.', [
                'ejecucion' => $id, 'tipo' => $error::class,
            ]);
            $detalle = 'El proceso se interrumpió o superó el tiempo de espera. Revisa el servidor antes de reintentar.';
        }

        $detalleExito = ($registro->accion ?? null) === 'git' ? 'Código actualizado. Revisa si hay migraciones pendientes.' : 'Acción completada.';
        $ejecucion->where('estado', 'ejecutando')->update([
            'estado' => $detalle === null ? 'correcto' : 'fallido', 'bloqueo' => null,
            'detalle' => $detalle ?? $detalleExito, 'finalizado_at' => now(), 'updated_at' => now(),
        ]);
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
        $directorio = $raiz->successful() ? realpath(trim($raiz->output())) : false;
        if ($directorio !== realpath(base_path())) {
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

        return $this->artisan($id, $this->comandos('limpiar'), 'El código se actualizó. ');
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
                if ($comando === 'migrate' && str_contains($resultado->output().$resultado->errorOutput(), 'AITROL_EMPRESA_INICIAL')) {
                    return 'La migración requiere configurar AITROL_EMPRESA_INICIAL con la empresa que recibirá los registros anteriores.';
                }

                return $prefijo.'Falló '.$comando.' con código '.$resultado->exitCode().'. Revisa el registro de Laravel.';
            }
        }

        return null;
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
        $vencidas = $this->tabla()->where('bloqueo', 1)->where(function (Builder $consulta): void {
            $consulta->where(fn (Builder $q) => $q->where('estado', 'pendiente')->where('created_at', '<', now()->subMinutes(2)))
                ->orWhere(fn (Builder $q) => $q->where('estado', 'ejecutando')->where('iniciado_at', '<', now()->subMinutes(20)));
        });
        if ($vencidas->exists()) {
            $vencidas->update([
                'estado' => 'fallido', 'bloqueo' => null, 'detalle' => 'La ejecución no respondió dentro del tiempo esperado.',
                'finalizado_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function tabla(): Builder
    {
        return DB::table('ejecuciones_mantenimiento');
    }
}
