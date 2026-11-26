<?php

declare(strict_types=1);

namespace Hospital\Operations\Application;

use Hospital\Operations\Domain\OperationsRepository;

final readonly class OperationsService
{
    public function __construct(private OperationsRepository $operations) {}

    public function overview(): array
    {
        $finance = $this->operations->finance();
        $rooms = $this->operations->rooms();

        return [
            'finance' => $finance + ['balance' => $finance['income'] - $finance['expenses']],
            'rooms' => $rooms,
            'occupied' => array_sum(array_column($rooms, 'occupied')),
            'capacity' => array_sum(array_column($rooms, 'capacity')),
            'reports' => $this->operations->reports(),
        ];
    }
}
