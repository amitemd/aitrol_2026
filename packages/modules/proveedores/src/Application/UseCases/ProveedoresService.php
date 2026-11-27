<?php

namespace Aitrol\Proveedores\Application\UseCases;

use Aitrol\Proveedores\Application\DTOs\DatosProveedor;
use Aitrol\Proveedores\Domain\ProveedorNoEncontrado;
use Aitrol\Proveedores\Domain\Entities\Proveedor;
use Aitrol\Proveedores\Domain\Repositories\ProveedorRepository;

final readonly class ProveedoresService
{
    public function __construct(private ProveedorRepository $proveedores) {}

    public function consultar(int $id): Proveedor
    {
        return $this->proveedores->buscar($id) ?? throw new ProveedorNoEncontrado('Proveedor no encontrado.');
    }

    /** @return array{items: list<Proveedor>, total: int} */
    public function listar(?string $busqueda, int $pagina, int $porPagina): array
    {
        return $this->proveedores->listar($busqueda, $pagina, $porPagina);
    }

    public function registrar(DatosProveedor $datos): Proveedor
    {
        return $this->guardar(null, $datos);
    }

    public function actualizar(int $id, DatosProveedor $datos): Proveedor
    {
        return $this->guardar($id, $datos);
    }

    public function eliminar(int $id): void
    {
        $this->proveedores->eliminar($id);
    }

    private function guardar(?int $id, DatosProveedor $datos): Proveedor
    {
        return $this->proveedores->guardar(new Proveedor(
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
