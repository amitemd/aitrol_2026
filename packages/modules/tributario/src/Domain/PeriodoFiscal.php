<?php

namespace Aitrol\Tributario\Domain;

use Carbon\CarbonImmutable;

final readonly class PeriodoFiscal
{
    public string $desde;
    public string $hasta;

    public function __construct(public string $formato, int $ejercicio, ?int $mes, bool $semestral)
    {
        $inicio = CarbonImmutable::create($ejercicio, $formato === 'formulario-107' ? 1 : $mes, 1);
        $this->desde = $inicio->toDateString();
        $meses = $formato === 'formulario-107' ? 12 : ($semestral ? 6 : 1);
        $this->hasta = $inicio->addMonths($meses)->subDay()->toDateString();
    }
}
