<?php

namespace App\Modules\Hospitalization\Application\Data;

final readonly class HospitalizedPatientFilters
{
    public function __construct(
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?int $roomId = null,
        public ?string $patientId = null,
        public ?string $patientName = null,
    ) {}
}
