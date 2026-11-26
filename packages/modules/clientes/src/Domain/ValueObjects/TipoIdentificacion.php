<?php

namespace Aitrol\Clientes\Domain\ValueObjects;

enum TipoIdentificacion: string
{
    case Cedula = 'cedula';
    case Ruc = 'ruc';
    case Pasaporte = 'pasaporte';

    public function idCatalogo(): int
    {
        return match ($this) {
            self::Cedula => 1,
            self::Ruc => 2,
            self::Pasaporte => 3,
        };
    }

    public static function desdeCatalogo(int $id): self
    {
        return match ($id) {
            1 => self::Cedula,
            2 => self::Ruc,
            3 => self::Pasaporte,
        };
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::Cedula => 'Cédula',
            self::Ruc => 'RUC',
            self::Pasaporte => 'Pasaporte',
        };
    }
}
