<?php

declare(strict_types=1);

namespace Hospital\Operations\Infrastructure;

use Hospital\Operations\Domain\OperationsRepository;
use PDO;

final readonly class SqliteOperationsRepository implements OperationsRepository
{
    public function __construct(private PDO $database) {}

    public function finance(): array
    {
        return $this->database->query('SELECT COALESCE(SUM(income), 0) AS income, COALESCE(SUM(expenses), 0) AS expenses FROM ledger')->fetch();
    }

    public function rooms(): array
    {
        return $this->database->query('SELECT label, occupied, capacity FROM rooms ORDER BY id')->fetchAll();
    }

    public function reports(): array
    {
        return $this->database->query('SELECT title, detail, area, status FROM reports ORDER BY id DESC')->fetchAll();
    }
}
