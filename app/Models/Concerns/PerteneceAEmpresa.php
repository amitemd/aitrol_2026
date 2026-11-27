<?php

namespace App\Models\Concerns;

use App\Services\EmpresaActiva;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait PerteneceAEmpresa
{
    protected static function bootPerteneceAEmpresa(): void
    {
        static::addGlobalScope('empresa', function (Builder $consulta): void {
            $id = auth()->user()?->id_empresa;
            if ($id === null) {
                $consulta->whereRaw('1 = 0');
            } else {
                $consulta->where($consulta->getModel()->qualifyColumn('id_empresa'), $id);
            }
        });
        static::creating(function (Model $registro): void {
            $registro->id_empresa = app(EmpresaActiva::class)->id();
        });
    }
}
