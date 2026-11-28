<?php

namespace Aitrol\Inventarios\Domain;

final class CantidadInventario
{
    public const MAXIMO = 1_000_000_000;

    public static function unidades(string|int|float $valor, int $decimales): int
    {
        [$entero, $fraccion] = array_pad(explode('.', (string) $valor, 2), 2, '');

        return (int) ($entero.str_pad($fraccion, $decimales, '0'));
    }

    public static function proporcionar(int $importe, int $cantidad, int $total): int
    {
        return intdiv($importe, $total) * $cantidad + intdiv(($importe % $total) * $cantidad + intdiv($total, 2), $total);
    }

    public static function decimal(int $unidades, int $decimales): string
    {
        $valor = str_pad((string) abs($unidades), $decimales + 1, '0', STR_PAD_LEFT);

        return ($unidades < 0 ? '-' : '').substr($valor, 0, -$decimales).'.'.substr($valor, -$decimales);
    }
}
