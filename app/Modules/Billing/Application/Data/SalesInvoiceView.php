<?php

namespace App\Modules\Billing\Application\Data;

final readonly class SalesInvoiceView
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private array $attributes)
    {
    }

    public static function fromLegacyRecord(object $record): self
    {
        return new self((array) $record);
    }

    public function id(): int
    {
        return (int) ($this->attributes['id'] ?? 0);
    }

    public function companyId(): string
    {
        return (string) ($this->attributes['id_empresa'] ?? '');
    }

    public function number(): string
    {
        return (string) ($this->attributes['nro_comprobante'] ?? '');
    }

    public function toLegacyRecord(): object
    {
        return (object) $this->attributes;
    }
}
