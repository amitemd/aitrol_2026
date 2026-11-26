<?php

namespace Aitrol\Clientes\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class IdentificacionFiscal
{
    public string $numero;

    public function __construct(public TipoIdentificacion $tipo, string $numero)
    {
        $this->numero = strtoupper(trim($numero));
        $patron = match ($tipo) {
            TipoIdentificacion::Cedula => '/\A[0-9]{10}\z/',
            TipoIdentificacion::Ruc => '/\A[0-9]{13}\z/',
            TipoIdentificacion::Pasaporte => '/\A[A-Z0-9][A-Z0-9-]{0,29}\z/',
        };

        if (preg_match($patron, $this->numero) !== 1) {
            throw new InvalidArgumentException('El número no tiene el formato del tipo de identificación seleccionado.');
        }
    }
}
