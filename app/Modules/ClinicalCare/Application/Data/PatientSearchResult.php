<?php

namespace App\Modules\ClinicalCare\Application\Data;

final readonly class PatientSearchResult
{
    public function __construct(
        public string $id,
        public string $displayName,
    ) {}

    /** @return array{value: string, id: string} */
    public function toLegacyOption(): array
    {
        return [
            'value' => $this->displayName,
            'id' => $this->id,
        ];
    }
}
