<?php

declare(strict_types=1);

namespace Hospital\Patients\Domain;

final readonly class Patient
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = trim($name);
        if (strlen($this->name) < 3 || strlen($this->name) > 120 || preg_match('/\p{L}/u', $this->name) !== 1) {
            throw new \InvalidArgumentException('Escribe un nombre válido de entre 3 y 120 caracteres.');
        }
    }
}
