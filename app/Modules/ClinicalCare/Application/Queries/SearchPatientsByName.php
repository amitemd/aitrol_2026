<?php

namespace App\Modules\ClinicalCare\Application\Queries;

use App\Modules\ClinicalCare\Application\Contracts\PatientSearchRepository;
use App\Modules\ClinicalCare\Application\Data\PatientSearchResult;
use Illuminate\Support\Collection;

final readonly class SearchPatientsByName
{
    public function __construct(private PatientSearchRepository $patients) {}

    /** @return Collection<int, PatientSearchResult> */
    public function handle(string $term, int $limit = 100): Collection
    {
        $normalizedTerm = trim($term);

        if ($normalizedTerm === '') {
            return collect();
        }

        return $this->patients->searchByName(
            $normalizedTerm,
            min(max($limit, 1), 100),
        );
    }
}
