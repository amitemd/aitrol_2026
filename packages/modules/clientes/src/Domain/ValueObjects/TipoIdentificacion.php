<?php

namespace Aitrol\Clientes\Domain\ValueObjects;

enum TipoIdentificacion: string
{
    case Cedula = 'cedula';
    case Ruc = 'ruc';
    case Pasaporte = 'pasaporte';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Cedula => 'Cédula',
            self::Ruc => 'RUC',
            self::Pasaporte => 'Pasaporte',
        };
    }
}
