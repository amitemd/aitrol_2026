<?php

namespace Aitrol\Clientes\Application\DTOs;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;

final readonly class DatosCliente
{
    public function __construct(
        public IdentificacionFiscal $identificacion,
        public string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $direccion,
        public bool $activo,
    ) {}
}
