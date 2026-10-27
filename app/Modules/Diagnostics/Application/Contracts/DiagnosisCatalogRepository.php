<?php

namespace App\Modules\Diagnostics\Application\Contracts;

use App\Modules\Diagnostics\Application\Data\DiagnosisCode;
use Illuminate\Support\Collection;

interface DiagnosisCatalogRepository
{
    /** @return Collection<int, DiagnosisCode> */
    public function search(string $term, int $limitPerCatalog): Collection;

    public function findByCode(string $code): ?DiagnosisCode;
}
