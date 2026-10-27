<?php

namespace App\Modules\Hospitalization\Application\Contracts;

use App\Modules\Hospitalization\Application\Data\HospitalizedPatientFilters;
use App\Modules\Hospitalization\Application\Data\HospitalizedPatientResult;
use Illuminate\Support\Collection;

interface HospitalizedPatientRepository
{
    /** @return Collection<int, HospitalizedPatientResult> */
    public function search(HospitalizedPatientFilters $filters): Collection;
}
