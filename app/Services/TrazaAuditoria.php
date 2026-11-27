<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Throwable;

final class TrazaAuditoria
{
    private const LIMITE_CONSULTAS = 60;
    private const LIMITE_PASOS = 48;

    private int $inicio;
    private int $consultas = 0;
    private array $pasos = [];
    private bool $limitada = false;

    public function __construct()
    {
        $this->inicio = hrtime(true);
    }

    public static function actual(): ?self
    {
        return app()->bound('request') ? request()->attributes->get(self::class) : null;
    }

    public static function registrarConsulta(QueryExecuted $consulta): void
    {
        $traza = self::actual();
        if ($traza === null || ! request()->route()?->getName()) {
            return;
        }

        $traza->consultas++;
        if ($traza->consultas > self::LIMITE_CONSULTAS) {
            $traza->limitada = true;
            return;
        }

        $traza->capturar(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 80));
    }

    public static function modulo(string $ruta): string
    {
        $grupo = explode('.', preg_replace('/^api\.v\d+\./', '', $ruta), 2)[0];

        return match ($grupo) {
            'empresa', 'usuarios' => 'configuracion',
            'login', 'logout', 'password' => 'acceso',
            default => array_key_exists($grupo, config('auditoria.modulos')) ? $grupo : 'otros',
        };
    }

    public function registrarError(Throwable $error): void
    {
        $this->capturar($error->getTrace());
    }

    public function resumen(): array
    {
        return [
            'duracion_ms' => (int) ((hrtime(true) - $this->inicio) / 1_000_000),
            'traza' => json_encode([
                'consultas' => $this->consultas,
                'pasos' => array_values($this->pasos),
                'limitada' => $this->limitada,
            ], JSON_THROW_ON_ERROR),
        ];
    }

    private function capturar(array $marcos): void
    {
        foreach (array_reverse($marcos) as $marco) {
            $clase = $marco['class'] ?? '';
            $metodo = $marco['function'] ?? '';
            $objeto = $marco['object'] ?? null;
            if ($objeto instanceof Builder || $objeto instanceof Relation) {
                $modelo = $objeto instanceof Builder ? $objeto->getModel() : $objeto->getRelated();
                $this->agregar('Modelo Eloquent', $modelo::class);
            } elseif ($objeto instanceof Model) {
                $this->agregar('Modelo Eloquent', $objeto::class);
            } elseif ($clase === \Illuminate\Database\Query\Builder::class) {
                $this->agregar('Persistencia', 'Laravel Query Builder');
            }

            if (! str_starts_with($clase, 'Aitrol\\') && ! str_starts_with($clase, 'App\\')) {
                continue;
            }
            $tipo = match (true) {
                str_contains($clase, '\\Controllers\\') || str_contains($clase, '\\Infrastructure\\Http\\') => 'Controlador',
                str_contains($clase, '\\Application\\UseCases\\') => 'Caso de uso',
                str_contains($clase, '\\Application\\') => 'Servicio de aplicación',
                str_contains($clase, '\\Infrastructure\\Persistence\\') => 'Repositorio / persistencia',
                str_contains($clase, '\\Services\\') && $clase !== self::class => 'Servicio',
                default => null,
            };
            if ($tipo === null) {
                continue;
            }

            $concreta = is_object($objeto) ? $objeto::class : $clase;
            $this->agregar($tipo, $concreta.'::'.$metodo);
            foreach (class_implements($concreta, false) ?: [] as $contrato) {
                if (str_starts_with($contrato, 'Aitrol\\') && method_exists($contrato, $metodo)) {
                    $this->agregar('Interfaz implementada', $contrato.'::'.$metodo);
                }
            }
        }
    }

    private function agregar(string $tipo, string $nombre): void
    {
        $clave = $tipo.'|'.$nombre;
        if (! isset($this->pasos[$clave]) && count($this->pasos) >= self::LIMITE_PASOS) {
            $this->limitada = true;
            return;
        }

        $this->pasos[$clave] = ['tipo' => $tipo, 'nombre' => $nombre];
    }
}
