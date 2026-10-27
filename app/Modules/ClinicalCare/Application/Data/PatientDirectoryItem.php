<?php

namespace App\Modules\ClinicalCare\Application\Data;

use Illuminate\Support\Str;

final readonly class PatientDirectoryItem
{
    public function __construct(
        public string $patientId,
        public string $firstName,
        public ?string $middleName,
        public string $lastName,
        public ?string $secondLastName,
        public ?string $primaryPhone,
        public ?string $secondaryPhone,
        public ?string $thirdPhone,
        public ?string $registeredAt,
        public ?string $creatorFirstName,
        public ?string $creatorLastName,
        public ?string $email,
    ) {}

    public function fullName(): string
    {
        return $this->joinVisibleValues([
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->secondLastName,
        ]);
    }

    public function phones(): string
    {
        return implode(' / ', array_values(array_filter([
            $this->primaryPhone,
            $this->secondaryPhone,
            $this->thirdPhone,
        ], $this->isVisibleValue(...))));
    }

    public function registeredDate(): string
    {
        return $this->registeredAt === null ? '' : mb_substr($this->registeredAt, 0, 10);
    }

    public function createdBy(): string
    {
        return $this->joinVisibleValues([$this->creatorFirstName, $this->creatorLastName]);
    }

    /** @param list<string|null> $values */
    private function joinVisibleValues(array $values): string
    {
        return Str::of(implode(' ', array_values(array_filter($values, $this->isVisibleValue(...)))))
            ->squish()
            ->toString();
    }

    private function isVisibleValue(?string $value): bool
    {
        return $value !== null && trim($value) !== '' && $value !== '(N/A)';
    }
}
