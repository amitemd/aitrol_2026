<?php

namespace App\Modules\ClinicalCare\Application\Queries;

use App\Modules\ClinicalCare\Application\Contracts\PatientDirectoryRepository;
use App\Modules\ClinicalCare\Application\Data\PatientDirectoryFilters;
use App\Modules\ClinicalCare\Application\Data\PatientDirectoryItem;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListPatients
{
    public function __construct(private PatientDirectoryRepository $patients) {}

    /** @return LengthAwarePaginator<int, PatientDirectoryItem> */
    public function handle(PatientDirectoryFilters $filters, int $page = 1, int $perPage = 25): LengthAwarePaginator
    {
        return $this->patients->paginate($filters, max(1, $page), min(max(1, $perPage), 100));
    }
}
