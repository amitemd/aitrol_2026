<?php

declare(strict_types=1);

namespace Hospital\Scheduling\Infrastructure;

use DateTimeImmutable;
use Hospital\Scheduling\Domain\ScheduleRepository;
use PDO;

final readonly class SqliteScheduleRepository implements ScheduleRepository
{
    public function __construct(private PDO $database) {}

    public function on(DateTimeImmutable $day): array
    {
        $statement = $this->database->prepare('SELECT starts_at, ends_at, title, kind FROM schedule WHERE day = ? ORDER BY starts_at');
        $statement->execute([$day->format('Y-m-d')]);

        return $statement->fetchAll();
    }

    public function counts(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        $statement = $this->database->prepare('SELECT kind, COUNT(*) FROM schedule WHERE day >= ? AND day < ? GROUP BY kind');
        $statement->execute([$start->format('Y-m-d'), $end->format('Y-m-d')]);

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR) + ['appointment' => 0, 'consultation' => 0, 'surgery' => 0];
    }
}
