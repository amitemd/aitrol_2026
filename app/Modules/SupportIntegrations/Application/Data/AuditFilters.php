<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class AuditFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $userId = null,
        public ?string $companyId = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
    ) {}
}
