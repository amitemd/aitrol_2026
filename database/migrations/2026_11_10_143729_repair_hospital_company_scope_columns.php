<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schemaMigration = require database_path(
            'migrations/2026_11_10_111342_ensure_company_scope_schema_for_hospital_and_ambulatory.php'
        );
        $schemaMigration->up();

        $this->ensureCompanyIndex('hospital', 'hospital_empresa_idx');
        $this->ensureCompanyIndex('sala', 'sala_empresa_idx');
        $this->ensureCompanyIndex('agenda', 'agenda_empresa_idx');

        $backfillMigration = require database_path(
            'migrations/2026_11_10_111346_backfill_company_scope_for_hospital_and_ambulatory.php'
        );
        $backfillMigration->up();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La reparación adopta columnas y propietarios clínicos existentes.
        // Revertirla volvería a mezclar información de distintas empresas.
    }

    private function ensureCompanyIndex(string $tableName, string $indexName): void
    {
        if (! Schema::hasTable($tableName)
            || ! Schema::hasColumn($tableName, 'id_empresa')
            || $this->hasCompanyIndex($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
            $table->index('id_empresa', $indexName);
        });
    }

    private function hasCompanyIndex(string $tableName): bool
    {
        foreach (Schema::getIndexes($tableName) as $index) {
            $columns = array_map(
                static fn (string $column): string => strtolower($column),
                $index['columns'] ?? [],
            );

            if (($columns[0] ?? null) === 'id_empresa') {
                return true;
            }
        }

        return false;
    }
};
