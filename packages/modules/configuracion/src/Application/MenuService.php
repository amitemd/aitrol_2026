<?php

namespace Aitrol\Configuracion\Application;

use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Domain\OpcionMenu;
use DomainException;

final class MenuService
{
    public function __construct(private readonly MenuRepository $repositorio) {}

    /** @return array<int, OpcionMenu> */
    public function listar(): array
    {
        return $this->repositorio->listar();
    }

    /** @return array<int, list<OpcionMenu>> */
    public function navegacion(bool $administrador): array
    {
        $grupos = [];
        foreach ($this->listar() as $opcion) {
            if ($opcion->activo && ($administrador || ! $opcion->soloAdministrador)) {
                $grupos[$opcion->padreId ?? 0][] = $opcion;
            }
        }

        return $grupos;
    }

    public function guardar(?OpcionMenu $opcion, array $datos): void
    {
        if ($opcion?->protegido) {
            throw new DomainException('Esta opción protege el acceso a la administración del menú.');
        }

        $this->repositorio->modificarJerarquia(function () use ($opcion, $datos): void {
            $opciones = $this->listar();
            $padreId = $datos['padre_id'];

            if ($padreId !== null) {
                $padre = $opciones[$padreId] ?? null;
                if ($padre === null || $padre->ruta !== null) {
                    throw new DomainException('Selecciona un grupo para ubicar esta opción.');
                }
                if ($opcion !== null && isset($this->jerarquia($opciones, $padre->id)[$opcion->id])) {
                    throw new DomainException('Un grupo no puede quedar dentro de sí mismo ni de uno de sus submenús.');
                }
            }

            if ($opcion !== null && $datos['ruta'] !== null) {
                foreach ($opciones as $item) {
                    if ($item->padreId === $opcion->id) {
                        throw new DomainException('Mueve las opciones de este grupo antes de convertirlo en enlace.');
                    }
                }
            }

            $this->repositorio->guardar($opcion?->id, $datos);
        });
    }

    /** @return array<int, string> */
    public function ubicaciones(array $opciones, ?OpcionMenu $opcion): array
    {
        $ubicaciones = [];
        foreach ($opciones as $grupo) {
            if ($grupo->ruta !== null) {
                continue;
            }

            $jerarquia = $this->jerarquia($opciones, $grupo->id);
            if ($opcion === null || ! isset($jerarquia[$opcion->id])) {
                $ubicaciones[$grupo->id] = implode(' › ', array_reverse($jerarquia));
            }
        }

        natcasesort($ubicaciones);

        return $ubicaciones;
    }

    /** @return array<int, string> */
    private function jerarquia(array $opciones, int $id): array
    {
        $jerarquia = [];
        $actual = $opciones[$id] ?? null;
        while ($actual !== null && ! isset($jerarquia[$actual->id])) {
            $jerarquia[$actual->id] = $actual->nombre;
            $actual = $opciones[$actual->padreId] ?? null;
        }

        return $jerarquia;
    }

    public function eliminar(OpcionMenu $opcion): void
    {
        if ($opcion->protegido) {
            throw new DomainException('Esta opción protege el acceso a la administración del menú.');
        }

        $this->repositorio->eliminar($opcion->id);
    }
}
