<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IndexAllTenantCompanyColumns extends Migration
{
    public function up(): void
    {
        foreach (Schema::getTables() as $tableData) {
            $tableName = $tableData['name'];

            if (! Schema::hasColumn($tableName, 'id_empresa') || $this->hasCompanyIndex($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $definition): void {
                $definition->index('id_empresa', 'mt_id_empresa_idx');
            });
        }
    }

    public function down(): void
    {
        foreach (Schema::getTables() as $tableData) {
            $tableName = $tableData['name'];

            if (! Schema::hasIndex($tableName, 'mt_id_empresa_idx')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $definition): void {
                $definition->dropIndex('mt_id_empresa_idx');
            });
        }
    }

    private function hasCompanyIndex(string $tableName): bool
    {
        foreach (Schema::getIndexes($tableName) as $index) {
            if (in_array('id_empresa', $index['columns'], true)) {
                return true;
            }
        }

        return false;
    }
}
