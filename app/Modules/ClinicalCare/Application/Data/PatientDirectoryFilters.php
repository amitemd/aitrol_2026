<?php

namespace App\Modules\ClinicalCare\Application\Data;

final readonly class PatientDirectoryFilters
{
    public function __construct(
        public ?string $patientId = null,
        public ?string $firstNames = null,
        public ?string $lastNames = null,
        public ?string $email = null,
    ) {}

    /** @param array{patient_id?: string|null, first_names?: string|null, last_names?: string|null, email?: string|null} $filters */
    public static function fromArray(array $filters): self
    {
        return new self(
            patientId: self::normalize($filters['patient_id'] ?? null),
            firstNames: self::normalize($filters['first_names'] ?? null),
            lastNames: self::normalize($filters['last_names'] ?? null),
            email: self::normalize($filters['email'] ?? null),
        );
    }

    private static function normalize(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
