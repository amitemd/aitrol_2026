<?php

namespace Sis_medico\Services;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class AutomaticTenantScope
{
    private $tenantTables = array();

    /**
     * Tablas de contexto o relación que deben poder consultarse antes de que
     * exista una empresa activa o para construir el selector de empresas.
     */
    private $excluded = array(
        'empresa',
        'usuario_empresa',
        'paciente_empresa',
    );

    public function register(Model $model)
    {
        $table = $model->getTable();
        $globalTables = (array) config('tenancy.global_tables', array());
        $hybridTables = (array) config('tenancy.hybrid_company_tables', array());
        if (in_array($table, $this->excluded, true) || in_array($table, $globalTables, true)
            || in_array($table, $hybridTables, true)
            || !$this->hasTenantColumn($table)) {
            return;
        }

        $class = get_class($model);
        $class::addGlobalScope('active_company', function ($query) use ($table) {
            $companyId = trim((string) session('id_empresa'));
            if ($companyId !== '') {
                $from = (string) $query->getQuery()->from;
                $qualifier = $table;
                if (preg_match('/\s+as\s+([A-Za-z0-9_]+)$/i', $from, $matches)) {
                    $qualifier = $matches[1];
                }
                $query->where($qualifier . '.id_empresa', $companyId);
            } elseif (auth()->check()) {
                // Una sesión autenticada sin empresa nunca puede consultar
                // accidentalmente todas las filas de una tabla empresarial.
                $query->whereRaw('1 = 0');
            }
        });

        $class::saving(function ($record) {
            $companyId = trim((string) session('id_empresa'));
            $recordCompany = trim((string) $record->getAttribute('id_empresa'));
            if ($companyId === '' && auth()->check()) {
                throw new RuntimeException('Seleccione una empresa antes de guardar el registro.');
            }
            if ($companyId !== '' && $recordCompany !== '' && $recordCompany !== $companyId) {
                throw new RuntimeException('El registro pertenece a otra empresa y no puede guardarse desde la empresa activa.');
            }
            if ($companyId !== '' && $recordCompany === '') {
                $record->setAttribute('id_empresa', $companyId);
            }
        });
    }

    private function hasTenantColumn($table)
    {
        if (array_key_exists($table, $this->tenantTables)) {
            return $this->tenantTables[$table];
        }

        try {
            return $this->tenantTables[$table] = Schema::hasColumn($table, 'id_empresa');
        } catch (\Exception $exception) {
            return $this->tenantTables[$table] = false;
        }
    }
}
