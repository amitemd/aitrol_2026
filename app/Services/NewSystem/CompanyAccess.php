<?php

namespace App\Services\NewSystem;

use App\Modules\Institutional\Application\Data\CompanyView;
use App\Modules\Institutional\Application\Queries\FindCurrentCompany;
use App\Modules\Institutional\Application\Queries\ListAccessibleCompanies;
use Illuminate\Support\Collection;
use Sis_medico\User;

class CompanyAccess
{
    public function __construct(
        private readonly ListAccessibleCompanies $accessibleCompanies,
        private readonly FindCurrentCompany $currentCompany,
    ) {}

    /** @return Collection<int, CompanyView> */
    public function accessibleFor(User $user): Collection
    {
        return $this->accessibleCompanies->handle(
            (string) $user->getKey(),
            (int) $user->id_tipo_usuario === 1,
        );
    }

    public function currentFor(User $user): ?CompanyView
    {
        $companyId = session('id_empresa');

        return $this->currentCompany->handle(
            (string) $user->getKey(),
            (int) $user->id_tipo_usuario === 1,
            $companyId === null ? null : (string) $companyId,
        );
    }
}
