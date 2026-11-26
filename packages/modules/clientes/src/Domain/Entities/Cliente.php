<?php

namespace Aitrol\Clientes\Domain\Entities;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use InvalidArgumentException;

final readonly class Cliente
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
    ) {
        $this->nombre = trim($nombre);

        if ($this->nombre === '' || mb_strlen($this->nombre) > 160) {
            throw new InvalidArgumentException('El nombre debe tener entre 1 y 160 caracteres.');
        }
    }
}
