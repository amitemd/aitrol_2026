<?php

namespace Aitrol\Proveedores\Domain\Repositories;

use Aitrol\Proveedores\Domain\Entities\Proveedor;

interface ProveedorRepository
{
    public function eliminar(int $id): void;

    public function buscar(int $id): ?Proveedor;

    /** @return array{items: list<Proveedor>, total: int} */
    public function listar(?string $busqueda, int $pagina, int $porPagina): array;

    public function guardar(Proveedor $proveedor): Proveedor;
}
