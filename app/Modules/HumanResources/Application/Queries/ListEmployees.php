<?php

namespace App\Modules\HumanResources\Application\Queries;

use App\Modules\HumanResources\Application\Contracts\EmployeeDirectoryRepository;
use App\Modules\HumanResources\Application\Data\EmployeeDirectoryFilters;
use App\Modules\HumanResources\Application\Data\EmployeeSummary;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListEmployees
{
    public function __construct(private EmployeeDirectoryRepository $employees) {}

    /** @return LengthAwarePaginator<int, EmployeeSummary> */
    public function handle(EmployeeDirectoryFilters $filters, int $page = 1, int $perPage = 30): LengthAwarePaginator
    {
        return $this->employees->paginate($filters, max(1, $page), min(max(1, $perPage), 100));
    }
}
