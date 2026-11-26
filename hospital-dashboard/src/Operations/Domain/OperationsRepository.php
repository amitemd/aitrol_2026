<?php

declare(strict_types=1);

namespace Hospital\Operations\Domain;

interface OperationsRepository
{
    /** @return array{income: int, expenses: int} */
    public function finance(): array;

    /** @return list<array{label: string, occupied: int, capacity: int}> */
    public function rooms(): array;

    /** @return list<array{title: string, detail: string, area: string, status: string}> */
    public function reports(): array;
}
