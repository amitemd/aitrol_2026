<?php

declare(strict_types=1);

namespace Hospital\Dashboard\Application;

use DateTimeImmutable;
use Hospital\Operations\Application\OperationsService;
use Hospital\Patients\Application\PatientService;
use Hospital\Scheduling\Application\ScheduleService;

final readonly class DashboardService
{
    public function __construct(
        private PatientService $patients,
        private ScheduleService $schedule,
        private OperationsService $operations,
    ) {}

    public function overview(DateTimeImmutable $day, string $query, int $year): array
    {
        return [
            'patients' => $this->patients->overview($query, $year),
            'schedule' => $this->schedule->overview($day),
            'operations' => $this->operations->overview(),
        ];
    }
}
