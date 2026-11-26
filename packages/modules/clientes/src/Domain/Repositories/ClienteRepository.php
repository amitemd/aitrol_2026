<?php

namespace Aitrol\Clientes\Domain\Repositories;

use Aitrol\Clientes\Domain\Entities\Cliente;

interface ClienteRepository
{
    public function buscar(int $id): ?Cliente;

    /** @return array{items: list<Cliente>, total: int} */
    public function listar(?string $busqueda, int $pagina, int $porPagina): array;

    public function guardar(Cliente $cliente): Cliente;
}
