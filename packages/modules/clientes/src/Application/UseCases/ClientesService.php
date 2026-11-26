<?php

namespace Aitrol\Clientes\Application\UseCases;

use Aitrol\Clientes\Application\DTOs\DatosCliente;
use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Aitrol\Clientes\Domain\Entities\Cliente;
use Aitrol\Clientes\Domain\Repositories\ClienteRepository;

final readonly class ClientesService
{
    public function __construct(private ClienteRepository $clientes) {}

    public function consultar(int $id): Cliente
    {
        return $this->clientes->buscar($id) ?? throw new ClienteNoEncontrado('Cliente no encontrado.');
    }

    /** @return array{items: list<Cliente>, total: int} */
    public function listar(?string $busqueda, int $pagina, int $porPagina): array
    {
        return $this->clientes->listar($busqueda, $pagina, $porPagina);
    }

    public function registrar(DatosCliente $datos): Cliente
    {
        return $this->guardar(null, $datos);
    }

    public function actualizar(int $id, DatosCliente $datos): Cliente
    {
        return $this->guardar($id, $datos);
    }

    private function guardar(?int $id, DatosCliente $datos): Cliente
    {
        return $this->clientes->guardar(new Cliente(
            $id,
            $datos->identificacion,
            $datos->nombre,
            $datos->email,
            $datos->telefono,
            $datos->direccion,
            $datos->activo,
            perfil: $datos->perfil,
        ));
    }
}
