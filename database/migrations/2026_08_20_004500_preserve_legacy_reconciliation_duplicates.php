<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preserve every historical row while making the reconciliation scope unique
     * for new records.
     */
    public function up(): void
    {
        $this->prepareMonthlyClosures();
        $this->prepareBankLines();
    }

    /**
     * Keep financial provenance metadata when rolling back later migrations.
     */
    public function down(): void {}

    private function prepareMonthlyClosures(): void
    {
        if (! Schema::hasTable('ct_conciliacion_mes')) {
            return;
        }

        $this->addDuplicateMetadata('ct_conciliacion_mes');

        if (Schema::hasIndex('ct_conciliacion_mes', 'v3_con_mes_scope_uq')) {
            return;
        }

        $scope = ['id_empresa', 'id_caja_banco', 'anio', 'mes', 'tipo'];
        $this->preserveDuplicates('ct_conciliacion_mes', $scope);

        Schema::table('ct_conciliacion_mes', function (Blueprint $table) use ($scope): void {
            $table->unique([...$scope, 'scope_guard'], 'v3_con_mes_scope_uq');
        });
    }

    private function prepareBankLines(): void
    {
        if (! Schema::hasTable('ct_conciliacion_bancaria')) {
            return;
        }

        $this->addDuplicateMetadata('ct_conciliacion_bancaria');

        if (! Schema::hasColumn('ct_conciliacion_bancaria', 'naturaleza')) {
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table): void {
                $table->string('naturaleza', 1)->nullable();
            });
        }

        DB::table('ct_conciliacion_bancaria')
            ->whereNull('naturaleza')
            ->update([
                'naturaleza' => DB::raw("CASE WHEN UPPER(tipo) IN ('EG','EGV','EGM','ND','NDB','DB','DEBITO') THEN 'D' ELSE 'C' END"),
            ]);

        $scope = ['empresa', 'id_caja_banco', 'anio', 'mes', 'tipo', 'id_tipo'];
        $hasScopeIndex = Schema::hasIndex('ct_conciliacion_bancaria', 'v3_con_line_scope_uq');
        $hasLegIndex = Schema::hasIndex('ct_conciliacion_bancaria', 'v3_con_line_leg_uq');

        if (! $hasScopeIndex && ! $hasLegIndex) {
            $this->preserveDuplicates('ct_conciliacion_bancaria', $scope);

            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) use ($scope): void {
                $table->unique([...$scope, 'scope_guard'], 'v3_con_line_scope_uq');
            });

            $hasScopeIndex = true;
        }

        if (! $hasLegIndex) {
            if (! $hasScopeIndex) {
                $this->preserveDuplicates('ct_conciliacion_bancaria', $scope);
            }

            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) use ($scope): void {
                $table->unique([...$scope, 'naturaleza', 'scope_guard'], 'v3_con_line_leg_uq');
            });
        }
    }

    private function addDuplicateMetadata(string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'duplicado_de')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->unsignedInteger('duplicado_de')->nullable();
            });
        }

        if (! Schema::hasColumn($tableName, 'scope_guard')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->unsignedTinyInteger('scope_guard')->nullable()->default(1);
            });
        }
    }

    /**
     * @param  list<string>  $scope
     */
    private function preserveDuplicates(string $tableName, array $scope): void
    {
        $duplicateGroups = DB::table($tableName)
            ->select($scope)
            ->selectRaw('MIN(id) AS canonical_id, COUNT(*) AS total')
            ->groupBy($scope)
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $duplicateGroup) {
            $canonicalId = (int) $duplicateGroup->canonical_id;
            $scopeQuery = $this->scopeQuery($tableName, $scope, $duplicateGroup);

            (clone $scopeQuery)
                ->where('id', $canonicalId)
                ->update([
                    'duplicado_de' => null,
                    'scope_guard' => 1,
                ]);

            (clone $scopeQuery)
                ->where('id', '<>', $canonicalId)
                ->update([
                    'duplicado_de' => $canonicalId,
                    'scope_guard' => null,
                ]);
        }
    }

    /**
     * @param  list<string>  $scope
     */
    private function scopeQuery(string $tableName, array $scope, object $duplicateGroup): Builder
    {
        $query = DB::table($tableName);

        foreach ($scope as $column) {
            $value = $duplicateGroup->{$column};

            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }
};
