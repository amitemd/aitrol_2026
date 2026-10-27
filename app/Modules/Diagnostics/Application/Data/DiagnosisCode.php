<?php

namespace App\Modules\Diagnostics\Application\Data;

final readonly class DiagnosisCode
{
    public function __construct(
        public string $code,
        public string $description,
    ) {}

    /** @return array{value: string, id: string} */
    public function toLegacySearchOption(): array
    {
        return [
            'value' => "({$this->code}) {$this->description}",
            'id' => $this->code,
        ];
    }

    /** @return array{value: string, id: string} */
    public function toLegacySelection(): array
    {
        return [
            'value' => $this->description,
            'id' => $this->code,
        ];
    }
}
