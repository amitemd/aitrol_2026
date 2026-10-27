<?php

namespace App\Modules\Billing\Application\Data;

final readonly class SalesInvoiceDirectoryFilters
{
    public function __construct(
        public string $companyId,
        public ?string $globalSearch = null,
        public ?string $invoiceId = null,
        public ?string $number = null,
        public ?string $journalEntryId = null,
        public ?string $date = null,
        public ?string $clientName = null,
        public ?string $patientNames = null,
        public int $page = 1,
        public int $perPage = 25,
    ) {}

    /** @param array{q?: mixed, id?: mixed, numero?: mixed, id_asiento?: mixed, fecha?: mixed, nombre_cliente?: mixed, nombres_paciente?: mixed, page?: mixed, per_page?: mixed} $filters */
    public static function fromArray(string $companyId, array $filters): self
    {
        $perPage = (int) ($filters['per_page'] ?? 25);

        return new self(
            companyId: $companyId,
            globalSearch: self::normalize($filters['q'] ?? null, 100),
            invoiceId: self::normalize($filters['id'] ?? null, 30),
            number: self::normalize($filters['numero'] ?? null, 50),
            journalEntryId: self::normalize($filters['id_asiento'] ?? null, 30),
            date: self::normalize($filters['fecha'] ?? null, 10),
            clientName: self::normalize($filters['nombre_cliente'] ?? null, 150),
            patientNames: self::normalize($filters['nombres_paciente'] ?? null, 150),
            page: max(1, (int) ($filters['page'] ?? 1)),
            perPage: in_array($perPage, [10, 25, 50], true) ? $perPage : 25,
        );
    }

    private static function normalize(mixed $value, int $limit): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $normalized = mb_substr(trim((string) $value), 0, $limit);

        return $normalized !== '' ? $normalized : null;
    }
}
