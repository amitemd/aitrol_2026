<?php

namespace App\Modules\Institutional\Infrastructure\Persistence;

use App\Modules\Institutional\Application\Contracts\CompanyAccessRepository;
use App\Modules\Institutional\Application\Data\CompanyView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelCompanyAccessRepository implements CompanyAccessRepository
{
    /** @return Collection<int, CompanyView> */
    public function accessibleFor(string $userId, bool $isAdministrator): Collection
    {
        $query = DB::table('empresa')
            ->select([
                'empresa.id',
                'empresa.razonsocial',
                'empresa.nombrecomercial',
                'empresa.nombre_corto',
                'empresa.ciudad',
                'empresa.estado',
                'empresa.prioridad',
            ])
            ->where('empresa.estado', 1);

        if (! $isAdministrator) {
            $query->join('usuario_empresa as access', 'access.id_empresa', '=', 'empresa.id')
                ->where('access.id_usuario', $userId);
        }

        return $query
            ->distinct()
            ->orderByDesc('empresa.prioridad')
            ->orderBy('empresa.nombrecomercial')
            ->orderBy('empresa.id')
            ->get()
            ->map(fn (object $record): CompanyView => CompanyView::fromRecord($record))
            ->values();
    }
}
