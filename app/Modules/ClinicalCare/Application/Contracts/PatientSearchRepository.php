<?php

namespace App\Modules\ClinicalCare\Application\Contracts;

use App\Modules\ClinicalCare\Application\Data\PatientSearchResult;
use Illuminate\Support\Collection;

interface PatientSearchRepository
{
    /** @return Collection<int, PatientSearchResult> */
    public function searchByName(string $term, int $limit): Collection;
}
