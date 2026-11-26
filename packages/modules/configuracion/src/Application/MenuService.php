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

        $esGrupo = $datos['ruta'] === null;
        if ($opcion !== null && ($opcion->ruta === null) !== $esGrupo) {
            throw new DomainException('El tipo de una opción existente no se puede cambiar.');
        }

        if ($datos['padre_id'] !== null) {
            $padre = $this->listar()[$datos['padre_id']] ?? null;
            if ($esGrupo || $padre === null || $padre->ruta !== null || $padre->padreId !== null) {
                throw new DomainException('Selecciona un grupo principal para el enlace.');
            }
        }

        $this->repositorio->guardar($opcion?->id, $datos);
    }

    public function eliminar(OpcionMenu $opcion): void
    {
        if ($opcion->protegido) {
            throw new DomainException('Esta opción protege el acceso a la administración del menú.');
        }

        $this->repositorio->eliminar($opcion->id);
    }
}
