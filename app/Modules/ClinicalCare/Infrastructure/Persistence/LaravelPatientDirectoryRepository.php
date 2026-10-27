<?php

namespace App\Modules\ClinicalCare\Infrastructure\Persistence;

use App\Modules\ClinicalCare\Application\Contracts\PatientDirectoryRepository;
use App\Modules\ClinicalCare\Application\Data\PatientDirectoryFilters;
use App\Modules\ClinicalCare\Application\Data\PatientDirectoryItem;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class LaravelPatientDirectoryRepository implements PatientDirectoryRepository
{
    public function paginate(PatientDirectoryFilters $filters, int $page, int $perPage): LengthAwarePaginator
    {
        $query = DB::table('paciente')
            ->leftJoin('users as creator', 'creator.id', '=', 'paciente.id_usuariocrea')
            ->leftJoin('users as account', 'account.id', '=', 'paciente.id')
            ->where('paciente.id', '<>', '9999999999')
            ->select([
                'paciente.id',
                'paciente.nombre1',
                'paciente.nombre2',
                'paciente.apellido1',
                'paciente.apellido2',
                'paciente.telefono1',
                'paciente.telefono2',
                'paciente.telefono3',
                'paciente.created_at',
                'creator.nombre1 as creator_first_name',
                'creator.apellido1 as creator_last_name',
                'account.email',
            ]);

        $this->applyFilters($query, $filters);

        return $query
            ->orderBy('paciente.apellido1')
            ->orderBy('paciente.apellido2')
            ->orderBy('paciente.nombre1')
            ->orderBy('paciente.id')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn (object $record): PatientDirectoryItem => new PatientDirectoryItem(
                patientId: (string) $record->id,
                firstName: (string) $record->nombre1,
                middleName: $this->nullableString($record->nombre2),
                lastName: (string) $record->apellido1,
                secondLastName: $this->nullableString($record->apellido2),
                primaryPhone: $this->nullableString($record->telefono1),
                secondaryPhone: $this->nullableString($record->telefono2),
                thirdPhone: $this->nullableString($record->telefono3),
                registeredAt: $this->nullableString($record->created_at),
                creatorFirstName: $this->nullableString($record->creator_first_name),
                creatorLastName: $this->nullableString($record->creator_last_name),
                email: $this->nullableString($record->email),
            ));
    }

    private function applyFilters(Builder $query, PatientDirectoryFilters $filters): void
    {
        if ($filters->patientId !== null) {
            $query->where('paciente.id', 'like', $this->pattern($filters->patientId));
        }

        $this->applyTokenFilter($query, $filters->firstNames, ['paciente.nombre1', 'paciente.nombre2']);
        $this->applyTokenFilter($query, $filters->lastNames, ['paciente.apellido1', 'paciente.apellido2']);

        if ($filters->email !== null) {
            $query->where('account.email', 'like', $this->pattern($filters->email));
        }
    }

    /** @param list<string> $columns */
    private function applyTokenFilter(Builder $query, ?string $value, array $columns): void
    {
        if ($value === null) {
            return;
        }

        foreach (preg_split('/\s+/u', $value, flags: PREG_SPLIT_NO_EMPTY) ?: [] as $token) {
            $pattern = $this->pattern($token);
            $query->where(function (Builder $tokenQuery) use ($columns, $pattern): void {
                foreach ($columns as $index => $column) {
                    $index === 0
                        ? $tokenQuery->where($column, 'like', $pattern)
                        : $tokenQuery->orWhere($column, 'like', $pattern);
                }
            });
        }
    }

    private function pattern(string $value): string
    {
        return '%'.addcslashes($value, '\\%_').'%';
    }

    private function nullableString(mixed $value): ?string
    {
        return $value !== null && $value !== '' ? (string) $value : null;
    }
}
