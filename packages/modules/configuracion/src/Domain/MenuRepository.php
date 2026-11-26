<?php

namespace Aitrol\Configuracion\Domain;

use Closure;

interface MenuRepository
{
    /** @return array<int, OpcionMenu> */
    public function listar(): array;

    public function guardar(?int $id, array $datos): void;

    public function modificarJerarquia(Closure $cambio): void;

    public function eliminar(int $id): void;
}
