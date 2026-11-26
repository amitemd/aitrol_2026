<?php

namespace Aitrol\Configuracion\Domain;

interface MenuRepository
{
    /** @return array<int, OpcionMenu> */
    public function listar(): array;

    public function guardar(?int $id, array $datos): void;

    public function eliminar(int $id): void;
}
