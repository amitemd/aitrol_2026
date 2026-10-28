<?php

namespace App\Modules\SupportIntegrations\Application\Contracts;

use App\Modules\SupportIntegrations\Application\Data\HttpRequestAuditEntry;

interface HttpRequestAuditRecorder
{
    public function record(HttpRequestAuditEntry $entry): void;
}
