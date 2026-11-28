<?php

namespace Aitrol\Contabilidad\Application\Puertos;

use Closure;
use stdClass;

interface RepositorioContable
{
    public function transaccion(Closure $trabajo): mixed;

    public function cuenta(int $id): stdClass;

    public function codigosConPrefijo(string $prefijo): iterable;

    public function nombresDeCuentas(?int $excepto): iterable;

    public function auxiliares(array $ids): array;

    public function tieneSubcuentasActivas(int $id): bool;

    public function periodoEnFecha(string $fecha): ?stdClass;

    public function periodosSolapados(string $desde, string $hasta): bool;

    public function periodo(int $id): stdClass;

    public function asiento(int $id): stdClass;

    public function asientoConClave(string $clave): ?stdClass;

    public function asientoDeOrigen(string $origen, int $id): ?int;

    public function siguienteNumero(): int;

    public function guardarCuenta(array $datos, ?int $id): int;

    public function guardarPeriodo(array $datos, ?int $id = null): int;

    public function guardarAsiento(array $cabecera, array $lineas): int;

    public function cajaEnDolares(int $id): bool;

    public function guardarIntegracion(array $datos): void;
}
