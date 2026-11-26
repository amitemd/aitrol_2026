<?php

declare(strict_types=1);

namespace Hospital\Scheduling\Application;

use DateTimeImmutable;
use Hospital\Scheduling\Domain\ScheduleRepository;

final readonly class ScheduleService
{
    public function __construct(private ScheduleRepository $schedule) {}

    public function overview(DateTimeImmutable $day): array
    {
        $weekStart = $day->modify('monday this week');

        return [
            'events' => $this->schedule->on($day),
            'current' => $this->schedule->counts($weekStart, $weekStart->modify('+7 days')),
            'previous' => $this->schedule->counts($weekStart->modify('-7 days'), $weekStart),
        ];
    }
}
