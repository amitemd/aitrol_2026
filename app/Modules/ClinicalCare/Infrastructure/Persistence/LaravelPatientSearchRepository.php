<?php

namespace App\Modules\ClinicalCare\Infrastructure\Persistence;

use App\Modules\ClinicalCare\Application\Contracts\PatientSearchRepository;
use App\Modules\ClinicalCare\Application\Data\PatientSearchResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelPatientSearchRepository implements PatientSearchRepository
{
    /** @return Collection<int, PatientSearchResult> */
    public function searchByName(string $term, int $limit): Collection
    {
        $escapedTerm = addcslashes($term, '\\%_');
        $pattern = '%'.(preg_replace('/\s+/u', '%', $escapedTerm) ?? $escapedTerm).'%';

        return DB::table('paciente')
            ->select('id')
            ->selectRaw("TRIM(CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2)) as display_name")
            ->where('id', '!=', '9999999999')
            ->whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) LIKE ?", [$pattern])
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre1')
            ->orderBy('nombre2')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (object $record): PatientSearchResult => new PatientSearchResult(
                id: (string) $record->id,
                displayName: (string) $record->display_name,
            ))
            ->values();
    }
}
