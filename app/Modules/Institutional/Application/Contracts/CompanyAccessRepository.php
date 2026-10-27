<?php

namespace App\Modules\Institutional\Application\Contracts;

use App\Modules\Institutional\Application\Data\CompanyView;
use Illuminate\Support\Collection;

interface CompanyAccessRepository
{
    /** @return Collection<int, CompanyView> */
    public function accessibleFor(string $userId, bool $isAdministrator): Collection;
}
