<?php

declare(strict_types=1);

namespace Hospital\Scheduling\Domain;

use DateTimeImmutable;

interface ScheduleRepository
{
    /** @return list<array{starts_at: string, ends_at: string, title: string, kind: string}> */
    public function on(DateTimeImmutable $day): array;

    /** @return array{appointment: int, consultation: int, surgery: int} */
    public function counts(DateTimeImmutable $start, DateTimeImmutable $end): array;
}
