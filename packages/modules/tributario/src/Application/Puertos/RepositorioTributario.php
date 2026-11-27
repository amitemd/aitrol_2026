<?php

namespace Aitrol\Tributario\Application\Puertos;

use Aitrol\Tributario\Domain\PeriodoFiscal;
use Closure;
use stdClass;

interface RepositorioTributario
{
    public function transaccion(Closure $trabajo): mixed;

    public function buscarPeriodo(PeriodoFiscal $periodo): ?stdClass;

    public function declaracion(int $id): stdClass;

    public function guardarDeclaracion(array $datos, ?int $id = null): int;

    public function preparar(PeriodoFiscal $periodo, array $entradas): array;

    public function tieneConstancia(int $id): bool;

    public function existeEntregaSolapada(stdClass $declaracion): bool;

    public function evento(int $id, string $accion, ?string $detalle = null): void;

    public function guardarNomina(array $datos): stdClass;
}
