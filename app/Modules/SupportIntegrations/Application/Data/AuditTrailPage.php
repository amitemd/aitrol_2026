<?php

namespace App\Modules\SupportIntegrations\Application\Data;

use Illuminate\Pagination\LengthAwarePaginator;

final readonly class AuditTrailPage
{
    /**
     * @param  LengthAwarePaginator<int, object>  $activities
     * @param  LengthAwarePaginator<int, object>  $technicalEvents
     * @param  array{activities_today: int, technical_today: int, users_today: int}  $summary
     * @param  list<string>  $warnings
     */
    public function __construct(
        public LengthAwarePaginator $activities,
        public LengthAwarePaginator $technicalEvents,
        public array $summary,
        public array $warnings = [],
    ) {}
}
