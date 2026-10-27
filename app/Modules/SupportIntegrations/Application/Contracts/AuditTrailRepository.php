<?php

namespace App\Modules\SupportIntegrations\Application\Contracts;

use App\Modules\SupportIntegrations\Application\Data\AuditFilters;
use App\Modules\SupportIntegrations\Application\Data\AuditTrailPage;

interface AuditTrailRepository
{
    public function read(AuditFilters $filters, int $activityPage, int $technicalPage, int $perPage): AuditTrailPage;
}
