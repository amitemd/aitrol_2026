<?php

namespace App\Modules\Diagnostics\Application\Queries;

use App\Modules\Diagnostics\Application\Contracts\DiagnosisCatalogRepository;
use App\Modules\Diagnostics\Application\Data\DiagnosisCode;
use Illuminate\Support\Collection;

final readonly class SearchDiagnosisCodes
{
    public function __construct(private DiagnosisCatalogRepository $diagnoses) {}

    /** @return Collection<int, DiagnosisCode> */
    public function handle(string $term, int $limitPerCatalog = 100): Collection
    {
        $normalizedTerm = trim($term);

        if ($normalizedTerm === '') {
            return collect();
        }

        return $this->diagnoses->search(
            $normalizedTerm,
            min(max($limitPerCatalog, 1), 100),
        );
    }
}
