<?php

namespace App\Modules\HumanResources\Application\Contracts;

use App\Modules\HumanResources\Application\Data\EmployeeDirectoryFilters;
use App\Modules\HumanResources\Application\Data\EmployeeSummary;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeDirectoryRepository
{
    /** @return LengthAwarePaginator<int, EmployeeSummary> */
    public function paginate(EmployeeDirectoryFilters $filters, int $page, int $perPage): LengthAwarePaginator;
}
