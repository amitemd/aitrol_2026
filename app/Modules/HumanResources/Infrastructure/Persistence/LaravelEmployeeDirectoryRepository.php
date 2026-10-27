<?php

namespace App\Modules\HumanResources\Infrastructure\Persistence;

use App\Modules\HumanResources\Application\Contracts\EmployeeDirectoryRepository;
use App\Modules\HumanResources\Application\Data\EmployeeDirectoryFilters;
use App\Modules\HumanResources\Application\Data\EmployeeSummary;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class LaravelEmployeeDirectoryRepository implements EmployeeDirectoryRepository
{
    public function paginate(EmployeeDirectoryFilters $filters, int $page, int $perPage): LengthAwarePaginator
    {
        $query = DB::table('empleado')
            ->where('estado', '<>', 0)
            ->select([
                'id',
                'nombre1',
                'nombre2',
                'apellido1',
                'apellido2',
                'telefono1',
                'telefono2',
            ]);

        $this->applyFilters($query, $filters);

        return $query
            ->orderBy('apellido1')
            ->orderBy('nombre1')
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn (object $record): EmployeeSummary => new EmployeeSummary(
                employeeId: (string) $record->id,
                firstName: (string) $record->nombre1,
                middleName: $this->nullableString($record->nombre2),
                lastName: (string) $record->apellido1,
                secondLastName: $this->nullableString($record->apellido2),
                primaryPhone: $this->nullableString($record->telefono1),
                secondaryPhone: $this->nullableString($record->telefono2),
            ));
    }

    private function applyFilters(Builder $query, EmployeeDirectoryFilters $filters): void
    {
        if ($filters->employeeId !== null) {
            $query->where('id', 'like', $this->pattern($filters->employeeId));
        }

        if ($filters->firstNames !== null) {
            $pattern = $this->pattern($filters->firstNames);
            $query->where(function (Builder $names) use ($pattern): void {
                $names->where('nombre1', 'like', $pattern)
                    ->orWhere('nombre2', 'like', $pattern);
            });
        }

        if ($filters->lastNames !== null) {
            $pattern = $this->pattern($filters->lastNames);
            $query->where(function (Builder $names) use ($pattern): void {
                $names->where('apellido1', 'like', $pattern)
                    ->orWhere('apellido2', 'like', $pattern);
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
