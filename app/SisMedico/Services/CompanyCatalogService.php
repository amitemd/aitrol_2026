<?php

namespace Sis_medico\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class CompanyCatalogService
{
    private $catalogs = array('especialidad', 'seguros', 'ct_tipo_pago', 'ct_tipo_tarjeta');

    public function companyId($required = false)
    {
        $companyId = trim((string) session('id_empresa'));
        // Los procesos de instalacion y consola no tienen sesion; para ellos
        // se usa la primera empresa activa. Una sesion autenticada, en cambio,
        // debe seleccionar expresamente su empresa para evitar cruces.
        if ($companyId === '' && !auth()->check()) {
            $companyId = trim((string) DB::table('empresa')->where('estado', 1)
                ->orderBy('prioridad')->orderBy('id')->value('id'));
        }
        if ($required && $companyId === '') {
            throw new RuntimeException('No existe una empresa activa para guardar el catálogo.');
        }
        return $companyId;
    }

    public function updateForCompany(Model $record, array $attributes)
    {
        $companyId = $this->companyId(true);
        if (!in_array($record->getTable(), $this->catalogs, true)
            || trim((string) $record->getAttribute('id_empresa')) !== $companyId) {
            throw new RuntimeException('El registro pertenece a otra empresa.');
        }
        $record->update($attributes);
        return $record;
    }

    public function uniqueRule($table, $column, $ignoreId = null)
    {
        $rule = Rule::unique($table, $column)->where('id_empresa', $this->companyId(true));
        if ($ignoreId !== null) $rule->ignore($ignoreId);
        return $rule;
    }

    public function existsRule($table, $column = 'id')
    {
        return Rule::exists($table, $column)->where('id_empresa', $this->companyId(true));
    }
}
