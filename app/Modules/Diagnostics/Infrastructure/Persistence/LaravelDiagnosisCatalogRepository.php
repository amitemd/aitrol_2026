<?php

namespace App\Modules\Diagnostics\Infrastructure\Persistence;

use App\Modules\Diagnostics\Application\Contracts\DiagnosisCatalogRepository;
use App\Modules\Diagnostics\Application\Data\DiagnosisCode;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelDiagnosisCatalogRepository implements DiagnosisCatalogRepository
{
    /** @var list<string> */
    private const TABLES = ['cie_10_3', 'cie_10_4'];

    /** @return Collection<int, DiagnosisCode> */
    public function search(string $term, int $limitPerCatalog): Collection
    {
        $escapedTerm = addcslashes($term, '\\%_');
        $pattern = "%{$escapedTerm}%";

        return collect(self::TABLES)
            ->flatMap(fn (string $table): Collection => $this->searchTable($table, $pattern, $limitPerCatalog))
            ->values();
    }

    public function findByCode(string $code): ?DiagnosisCode
    {
        foreach (self::TABLES as $table) {
            $record = DB::table($table)
                ->select(['id', 'descripcion'])
                ->where('id', $code)
                ->first();

            if ($record !== null) {
                return $this->toDiagnosisCode($record);
            }
        }

        return null;
    }

    /** @return Collection<int, DiagnosisCode> */
    private function searchTable(string $table, string $pattern, int $limit): Collection
    {
        return DB::table($table)
            ->select(['id', 'descripcion'])
            ->where(function (Builder $query) use ($pattern): void {
                $query
                    ->where('descripcion', 'like', $pattern)
                    ->orWhere('id', 'like', $pattern);
            })
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (object $record): DiagnosisCode => $this->toDiagnosisCode($record));
    }

    private function toDiagnosisCode(object $record): DiagnosisCode
    {
        return new DiagnosisCode(
            code: (string) $record->id,
            description: (string) $record->descripcion,
        );
    }
}
