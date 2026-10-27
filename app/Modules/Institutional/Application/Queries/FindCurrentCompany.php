<?php

namespace App\Modules\Institutional\Application\Queries;

use App\Modules\Institutional\Application\Data\CompanyView;

final readonly class FindCurrentCompany
{
    public function __construct(private ListAccessibleCompanies $accessibleCompanies) {}

    public function handle(string $userId, bool $isAdministrator, ?string $companyId): ?CompanyView
    {
        if ($companyId === null || $companyId === '') {
            return null;
        }

        return $this->accessibleCompanies
            ->handle($userId, $isAdministrator)
            ->first(fn (CompanyView $company): bool => $company->id === $companyId);
    }
}
