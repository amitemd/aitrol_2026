<?php

namespace App\Services\NewSystem;

use Illuminate\Support\Collection;
use Sis_medico\Empresa;
use Sis_medico\User;

class CompanyAccess
{
    /** @return Collection<int, Empresa> */
    public function accessibleFor(User $user): Collection
    {
        $query = Empresa::query()
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

        if ((int) $user->id_tipo_usuario !== 1) {
            $query->join('usuario_empresa as access', 'access.id_empresa', '=', 'empresa.id')
                ->where('access.id_usuario', $user->getKey());
        }

        return $query
            ->distinct()
            ->orderByDesc('empresa.prioridad')
            ->orderBy('empresa.nombrecomercial')
            ->orderBy('empresa.id')
            ->get();
    }

    public function currentFor(User $user): ?Empresa
    {
        $companyId = session('id_empresa');

        if ($companyId === null) {
            return null;
        }

        return $this->accessibleFor($user)->firstWhere('id', $companyId);
    }
}
