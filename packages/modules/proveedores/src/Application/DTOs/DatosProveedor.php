<?php

namespace Aitrol\Proveedores\Application\DTOs;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;

final readonly class DatosProveedor
{
    public function __construct(
        public IdentificacionFiscal $identificacion,
        public string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $direccion,
        public bool $activo,
        public array $perfil = [],
    ) {}
}
