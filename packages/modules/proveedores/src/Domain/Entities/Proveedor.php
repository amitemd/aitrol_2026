<?php

namespace Aitrol\Proveedores\Domain\Entities;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use InvalidArgumentException;

final readonly class Proveedor
{
    public string $nombre;

    public function __construct(
        public ?int $id,
        public IdentificacionFiscal $identificacion,
        string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $direccion,
        public bool $activo,
        public ?int $idPersona = null,
        public array $perfil = [],
        public array $auditoria = [],
    ) {
        $this->nombre = trim($nombre);

        if ($this->nombre === '') {
            throw new InvalidArgumentException('El nombre del proveedor es obligatorio.');
        }
    }
}
