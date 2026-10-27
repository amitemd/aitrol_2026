<?php

namespace App\Modules\Institutional\Application\Queries;

use App\Modules\Institutional\Application\Contracts\CompanyAccessRepository;
use App\Modules\Institutional\Application\Data\CompanyView;
use Illuminate\Support\Collection;

final readonly class ListAccessibleCompanies
{
    public function __construct(private CompanyAccessRepository $companies) {}

    /** @return Collection<int, CompanyView> */
    public function handle(string $userId, bool $isAdministrator): Collection
    {
        return $this->companies->accessibleFor($userId, $isAdministrator);
    }
}
