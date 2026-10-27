<?php

namespace App\Modules\Institutional\Application\Data;

final readonly class CompanyView
{
    public function __construct(
        public string $id,
        public ?string $razonsocial,
        public ?string $nombrecomercial,
        public ?string $nombre_corto,
        public ?string $ciudad,
        public int $estado,
        public int $prioridad,
    ) {}

    public static function fromRecord(object $record): self
    {
        return new self(
            id: (string) $record->id,
            razonsocial: self::nullableString($record->razonsocial ?? null),
            nombrecomercial: self::nullableString($record->nombrecomercial ?? null),
            nombre_corto: self::nullableString($record->nombre_corto ?? null),
            ciudad: self::nullableString($record->ciudad ?? null),
            estado: (int) ($record->estado ?? 0),
            prioridad: (int) ($record->prioridad ?? 0),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        return $value === null ? null : (string) $value;
    }
}
