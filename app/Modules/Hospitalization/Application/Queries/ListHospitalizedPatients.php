<?php

namespace App\Modules\Hospitalization\Application\Queries;

use App\Modules\Hospitalization\Application\Contracts\HospitalizedPatientRepository;
use App\Modules\Hospitalization\Application\Data\HospitalizedPatientFilters;
use App\Modules\Hospitalization\Application\Data\HospitalizedPatientResult;
use Illuminate\Support\Collection;

final readonly class ListHospitalizedPatients
{
    public function __construct(private HospitalizedPatientRepository $patients) {}

    /** @return Collection<int, HospitalizedPatientResult> */
    public function handle(HospitalizedPatientFilters $filters): Collection
    {
        return $this->patients->search(new HospitalizedPatientFilters(
            fromDate: $this->normalize($filters->fromDate),
            toDate: $this->normalize($filters->toDate),
            roomId: $filters->roomId,
            patientId: $this->normalize($filters->patientId),
            patientName: $this->normalize($filters->patientName),
        ));
    }

    private function normalize(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
