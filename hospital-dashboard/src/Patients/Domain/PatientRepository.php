<?php

declare(strict_types=1);

namespace Hospital\Patients\Domain;

interface PatientRepository
{
    public function add(Patient $patient): void;

    public function count(): int;

    /** @return list<array{name: string, registered_on: string}> */
    public function search(string $query): array;

    /** @return list<array{month: int, total: int, admitted: int}> */
    public function monthlyActivity(int $year): array;
}
