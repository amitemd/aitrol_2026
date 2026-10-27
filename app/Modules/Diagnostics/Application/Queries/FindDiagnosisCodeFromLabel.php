<?php

namespace App\Modules\Diagnostics\Application\Queries;

use App\Modules\Diagnostics\Application\Contracts\DiagnosisCatalogRepository;
use App\Modules\Diagnostics\Application\Data\DiagnosisCode;

final readonly class FindDiagnosisCodeFromLabel
{
    public function __construct(private DiagnosisCatalogRepository $diagnoses) {}

    public function handle(string $label): ?DiagnosisCode
    {
        if (preg_match('/^\(([^)]+)\)/', trim($label), $matches) !== 1) {
            return null;
        }

        $code = trim($matches[1]);

        return $code === '' ? null : $this->diagnoses->findByCode($code);
    }
}
