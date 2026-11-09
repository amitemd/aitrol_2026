<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class MultiTenantIntegrityAuditService
{
    public function inspect()
    {
        $database = DB::connection()->getDatabaseName();
        $columns = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', $database)
            ->where('COLUMN_NAME', 'id_empresa')
            ->orderBy('TABLE_NAME')
            ->get(array('TABLE_NAME', 'IS_NULLABLE'));
        $globalTables = array_fill_keys((array) config('tenancy.global_tables', array()), true);
        $hybridTables = array_fill_keys((array) config('tenancy.hybrid_company_tables', array()), true);
        $columns = $columns->reject(function ($column) use ($globalTables, $hybridTables) {
            return isset($globalTables[(string) $column->TABLE_NAME])
                || isset($hybridTables[(string) $column->TABLE_NAME]);
        })->values();
        $indexed = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('COLUMN_NAME', 'id_empresa')
            ->pluck('TABLE_NAME')->map(function ($table) { return (string) $table; })->unique()->flip();

        $tables = array();
        $totals = array('rows' => 0, 'missing_company' => 0, 'invalid_company' => 0,
            'nullable_tables' => 0, 'unindexed_tables' => 0, 'query_errors' => 0);

        foreach ($columns as $column) {
            $table = (string) $column->TABLE_NAME;
            try {
                $globalIdentifiers = (array) config('tenancy.global_company_identifiers.'.$table, array());
                $invalidSql = "SUM(CASE WHEN tenant_row.id_empresa IS NOT NULL AND TRIM(tenant_row.id_empresa) <> '' AND tenant_company.id IS NULL";
                $bindings = array();
                if ($globalIdentifiers) {
                    $invalidSql .= ' AND tenant_row.id_empresa NOT IN ('.implode(',', array_fill(0, count($globalIdentifiers), '?')).')';
                    $bindings = array_values($globalIdentifiers);
                }
                $invalidSql .= ' THEN 1 ELSE 0 END) as invalid_company';
                $counts = DB::table($table.' as tenant_row')
                    ->leftJoin('empresa as tenant_company', function ($join) {
                        $join->on('tenant_company.id', '=', 'tenant_row.id_empresa');
                    })
                    ->selectRaw("COUNT(*) as row_count,
                        SUM(CASE WHEN tenant_row.id_empresa IS NULL OR TRIM(tenant_row.id_empresa) = '' THEN 1 ELSE 0 END) as missing_company,
                        ".$invalidSql, $bindings)
                    ->first();
                $item = array(
                    'table' => $table,
                    'rows' => (int) $counts->row_count,
                    'missing_company' => (int) $counts->missing_company,
                    'invalid_company' => (int) $counts->invalid_company,
                    'nullable' => strtoupper((string) $column->IS_NULLABLE) === 'YES',
                    'indexed' => isset($indexed[$table]),
                );
            } catch (\Exception $exception) {
                $item = array('table' => $table, 'rows' => 0, 'missing_company' => 0,
                    'invalid_company' => 0, 'nullable' => true, 'indexed' => false,
                    'error' => 'No se pudo inspeccionar la tabla.');
                $totals['query_errors']++;
            }
            foreach (array('rows', 'missing_company', 'invalid_company') as $metric) $totals[$metric] += $item[$metric];
            if ($item['nullable']) $totals['nullable_tables']++;
            if (!$item['indexed']) $totals['unindexed_tables']++;
            if ($item['missing_company'] || $item['invalid_company'] || !$item['indexed'] || isset($item['error'])) $tables[] = $item;
        }

        $memberships = $this->membershipIntegrity();
        $patients = $this->patientIntegrity();
        $people = $this->personIdentityIntegrity();
        $controls = $this->codeControls();
        $classification = $this->classifyTables($database, $columns);
        $dataPass = $totals['missing_company'] === 0 && $totals['invalid_company'] === 0
            && $totals['query_errors'] === 0 && $memberships['decision'] === 'GO'
            && $patients['decision'] === 'GO' && $people['decision'] === 'GO';
        $architecturePass = $totals['nullable_tables'] === 0 && $totals['unindexed_tables'] === 0
            && $classification['unclassified_count'] === 0 && !in_array(false, $controls, true);

        return array(
            'decision' => $dataPass && $architecturePass ? 'CERTIFICADO' : 'NO-CERTIFICADO',
            'data_decision' => $dataPass ? 'GO' : 'NO-GO',
            'architecture_decision' => $architecturePass ? 'GO' : 'NO-GO',
            'scope' => array(
                'database_tables' => (int) DB::table('information_schema.TABLES')
                    ->where('TABLE_SCHEMA', $database)->where('TABLE_TYPE', 'BASE TABLE')->count(),
                'tables_with_id_empresa' => count($columns),
                'declared_global_tables' => $classification['declared_global_count'],
                'membership_tables' => $classification['membership_count'],
                'hybrid_company_tables' => $classification['hybrid_count'],
                'inherited_by_foreign_key' => $classification['foreign_key_inherited_count'],
                'declared_inherited_tables' => $classification['declared_inherited_count'],
                'unclassified_tables' => $classification['unclassified_count'],
                'unclassified_table_names' => $classification['unclassified_tables'],
            ),
            'tenant_data' => $totals,
            'table_findings' => $tables,
            'user_company_memberships' => $memberships,
            'patient_company_memberships' => $patients,
            'person_identity_registry' => $people,
            'code_controls' => $controls,
            'limitations' => array(
                'Las tablas sin id_empresa deben estar declaradas como catálogo global o heredar empresa mediante una relación padre antes de una certificación total.',
                'Las consultas DB::table y SQL crudo no reciben automáticamente el scope Eloquent y requieren revisión específica.',
                'La auditoría comprueba integridad referencial, pero no puede deducir si una asignación histórica corresponde semánticamente a la empresa correcta.',
            ),
        );
    }

    private function membershipIntegrity()
    {
        if (!$this->hasTables(array('users', 'empresa', 'usuario_empresa'))) {
            return array('decision' => 'NO-GO', 'error' => 'Faltan tablas de asignación de usuarios.');
        }
        $withoutCompany = DB::table('users as tenant_user')
            ->leftJoin('usuario_empresa as membership', 'membership.id_usuario', '=', 'tenant_user.id')
            ->leftJoin('empresa as company', function ($join) {
                $join->on('company.id', '=', 'membership.id_empresa')->where('company.estado', '=', 1);
            })->where('tenant_user.estado', 1)->groupBy('tenant_user.id')
            ->havingRaw('COUNT(company.id) = 0')->get(array('tenant_user.id'))->count();
        $orphan = DB::table('usuario_empresa as membership')
            ->leftJoin('users as tenant_user', 'tenant_user.id', '=', 'membership.id_usuario')
            ->leftJoin('empresa as company', 'company.id', '=', 'membership.id_empresa')
            ->where(function ($query) { $query->whereNull('tenant_user.id')->orWhereNull('company.id'); })->count();
        $duplicates = DB::table('usuario_empresa')->groupBy('id_usuario', 'id_empresa')
            ->havingRaw('COUNT(*) > 1')->get(array('id_usuario'))->count();
        return array('decision' => $withoutCompany || $orphan || $duplicates ? 'NO-GO' : 'GO',
            'active_users_without_company' => $withoutCompany, 'orphan_memberships' => $orphan,
            'duplicate_memberships' => $duplicates);
    }

    private function patientIntegrity()
    {
        if (!$this->hasTables(array('paciente', 'empresa', 'paciente_empresa'))) {
            return array('decision' => 'NO-GO', 'error' => 'Falta paciente_empresa o una tabla relacionada.');
        }
        $withoutCompany = DB::table('paciente as patient')->leftJoin('paciente_empresa as membership',
            'membership.id_paciente', '=', 'patient.id')->whereNull('membership.id_paciente')->count();
        $orphan = DB::table('paciente_empresa as membership')
            ->leftJoin('paciente as patient', 'patient.id', '=', 'membership.id_paciente')
            ->leftJoin('empresa as company', 'company.id', '=', 'membership.id_empresa')
            ->where(function ($query) { $query->whereNull('patient.id')->orWhereNull('company.id'); })->count();
        $duplicates = DB::table('paciente_empresa')->groupBy('id_paciente', 'id_empresa')
            ->havingRaw('COUNT(*) > 1')->get(array('id_paciente'))->count();
        return array('decision' => $withoutCompany || $orphan || $duplicates ? 'NO-GO' : 'GO',
            'patients_without_company' => $withoutCompany, 'orphan_memberships' => $orphan,
            'duplicate_memberships' => $duplicates);
    }

    private function personIdentityIntegrity()
    {
        if (!$this->hasTables(array('personas', 'persona_empresa', 'empresa'))) {
            return array('decision' => 'NO-GO', 'error' => 'Falta el registro canónico de personas.');
        }
        $duplicateIdentities = DB::table('personas')->groupBy('identificacion')
            ->havingRaw('COUNT(*) > 1')->get(array('identificacion'))->count();
        $orphanRoles = DB::table('persona_empresa as membership')
            ->leftJoin('personas as person', 'person.id', '=', 'membership.persona_id')
            ->leftJoin('empresa as company', 'company.id', '=', 'membership.id_empresa')
            ->where(function ($query) { $query->whereNull('person.id')->orWhereNull('company.id'); })->count();
        $unlinked = 0;
        $orphanLinks = 0;
        $syncTriggers = DB::connection()->getDriverName() === 'mysql'
            ? DB::table('information_schema.TRIGGERS')->where('TRIGGER_SCHEMA', DB::connection()->getDatabaseName())
                ->where('TRIGGER_NAME', 'like', 'sync_person_%')->count()
            : 0;
        foreach (array('paciente', 'users', 'proveedor', 'ct_clientes') as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'persona_id')) {
                $unlinked++;
                continue;
            }
            $unlinked += DB::table($table)->whereNull('persona_id')->count();
            $orphanLinks += DB::table($table.' as source')->leftJoin('personas as person',
                'person.id', '=', 'source.persona_id')->whereNotNull('source.persona_id')->whereNull('person.id')->count();
        }
        return array(
            'decision' => $duplicateIdentities || $orphanRoles || $unlinked || $orphanLinks || $syncTriggers < 8 ? 'NO-GO' : 'GO',
            'duplicate_identifications' => $duplicateIdentities,
            'orphan_company_roles' => $orphanRoles,
            'unlinked_domain_records' => $unlinked,
            'orphan_domain_links' => $orphanLinks,
            'database_sync_triggers' => $syncTriggers,
        );
    }

    private function codeControls()
    {
        $scope = file_get_contents(app_path('Services/AutomaticTenantScope.php'));
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
        $context = file_get_contents(app_path('Services/TenantContext.php'));
        return array(
            'automatic_eloquent_scope' => strpos($provider, "Event::listen('eloquent.booted: *'") !== false,
            'authenticated_requests_fail_closed' => strpos($scope, "whereRaw('1 = 0')") !== false,
            'cross_company_writes_rejected' => strpos($scope, 'El registro pertenece a otra empresa') !== false,
            'company_selection_uses_memberships' => strpos($context, "join('usuario_empresa'") !== false,
        );
    }

    private function classifyTables($database, $tenantColumns)
    {
        $tenantTables = array();
        foreach ($tenantColumns as $column) $tenantTables[(string) $column->TABLE_NAME] = true;
        $global = array_fill_keys((array) config('tenancy.global_tables', array()), true);
        $memberships = array_fill_keys((array) config('tenancy.membership_tables', array()), true);
        $hybrid = array_fill_keys((array) config('tenancy.hybrid_company_tables', array()), true);
        $declaredInherited = array_fill_keys((array) config('tenancy.inherited_tables', array()), true);
        $foreignKeyInherited = array();
        $relations = DB::table('information_schema.KEY_COLUMN_USAGE')->where('TABLE_SCHEMA', $database)
            ->whereNotNull('REFERENCED_TABLE_NAME')->get(array('TABLE_NAME', 'REFERENCED_TABLE_NAME'));
        $owned = $tenantTables;
        do {
            $changed = false;
            foreach ($relations as $relation) {
                $table = (string) $relation->TABLE_NAME;
                $parent = (string) $relation->REFERENCED_TABLE_NAME;
                if (!isset($owned[$table]) && isset($owned[$parent])) {
                    $owned[$table] = true;
                    $foreignKeyInherited[$table] = true;
                    $changed = true;
                }
            }
        } while ($changed);
        $all = DB::table('information_schema.TABLES')->where('TABLE_SCHEMA', $database)
            ->where('TABLE_TYPE', 'BASE TABLE')->pluck('TABLE_NAME');
        $unclassified = array();
        foreach ($all as $table) {
            $table = (string) $table;
            if (!isset($tenantTables[$table]) && !isset($global[$table]) && !isset($memberships[$table])
                && !isset($hybrid[$table]) && !isset($declaredInherited[$table])
                && !isset($foreignKeyInherited[$table])) $unclassified[] = $table;
        }
        sort($unclassified);
        return array('declared_global_count' => count($global), 'membership_count' => count($memberships),
            'hybrid_count' => count($hybrid),
            'foreign_key_inherited_count' => count($foreignKeyInherited),
            'declared_inherited_count' => count($declaredInherited), 'unclassified_count' => count($unclassified),
            'unclassified_tables' => $unclassified);
    }

    private function hasTables(array $tables)
    {
        foreach ($tables as $table) if (!Schema::hasTable($table)) return false;
        return true;
    }
}
