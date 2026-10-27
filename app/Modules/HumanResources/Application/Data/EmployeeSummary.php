<?php

namespace App\Modules\HumanResources\Application\Data;

use Illuminate\Support\Str;

final readonly class EmployeeSummary
{
    public function __construct(
        public string $employeeId,
        public string $firstName,
        public ?string $middleName,
        public string $lastName,
        public ?string $secondLastName,
        public ?string $primaryPhone,
        public ?string $secondaryPhone,
    ) {}

    public function fullName(): string
    {
        return Str::of(implode(' ', array_filter([
            $this->firstName,
            $this->visibleOptionalName($this->middleName),
            $this->lastName,
            $this->visibleOptionalName($this->secondLastName),
        ])))->squish()->toString();
    }

    public function phones(): string
    {
        return implode(' / ', array_filter([$this->primaryPhone, $this->secondaryPhone]));
    }

    private function visibleOptionalName(?string $name): ?string
    {
        return $name !== null && $name !== '(N/A)' ? $name : null;
    }
}
