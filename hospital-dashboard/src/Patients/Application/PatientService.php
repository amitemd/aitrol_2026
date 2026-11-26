<?php

declare(strict_types=1);

namespace Hospital\Patients\Application;

use Hospital\Patients\Domain\Patient;
use Hospital\Patients\Domain\PatientRepository;

final readonly class PatientService
{
    public function __construct(private PatientRepository $patients) {}

    public function register(string $name): void
    {
        $this->patients->add(new Patient($name));
    }

    /** @return array{count: int, results: list<array{name: string, registered_on: string}>, activity: list<array{month: int, total: int, admitted: int}>} */
    public function overview(string $query, int $year): array
    {
        return [
            'count' => $this->patients->count(),
            'results' => $this->patients->search($query),
            'activity' => $this->patients->monthlyActivity($year),
        ];
    }
}
