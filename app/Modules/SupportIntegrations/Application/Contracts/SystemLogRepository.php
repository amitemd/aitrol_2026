<?php

namespace App\Modules\SupportIntegrations\Application\Contracts;

use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemLogPage;

interface SystemLogRepository
{
    public function read(SystemLogFilters $filters, int $page, int $perPage): SystemLogPage;
}
