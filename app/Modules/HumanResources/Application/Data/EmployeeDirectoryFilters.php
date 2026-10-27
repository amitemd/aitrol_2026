<?php

namespace App\Modules\HumanResources\Application\Data;

final readonly class EmployeeDirectoryFilters
{
    public function __construct(
        public ?string $employeeId = null,
        public ?string $firstNames = null,
        public ?string $lastNames = null,
    ) {}

    /** @param array{employee_id?: string|null, first_names?: string|null, last_names?: string|null} $filters */
    public static function fromArray(array $filters): self
    {
        return new self(
            employeeId: self::normalize($filters['employee_id'] ?? null),
            firstNames: self::normalize($filters['first_names'] ?? null),
            lastNames: self::normalize($filters['last_names'] ?? null),
        );
    }

    private static function normalize(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
