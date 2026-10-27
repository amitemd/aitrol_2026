<?php

namespace App\Modules\ClinicalCare\Application\Contracts;

use App\Modules\ClinicalCare\Application\Data\PatientDirectoryFilters;
use App\Modules\ClinicalCare\Application\Data\PatientDirectoryItem;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientDirectoryRepository
{
    /** @return LengthAwarePaginator<int, PatientDirectoryItem> */
    public function paginate(PatientDirectoryFilters $filters, int $page, int $perPage): LengthAwarePaginator;
}
