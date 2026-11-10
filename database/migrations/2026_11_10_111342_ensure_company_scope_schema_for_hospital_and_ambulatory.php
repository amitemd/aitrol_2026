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
        if (! Schema::hasTable('company_hospital_contexts')) {
            Schema::create('company_hospital_contexts', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('company_id', 20);
                $table->unsignedInteger('hospital_id');
                $table->boolean('is_default')->default(false);
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->unique(['company_id', 'hospital_id'], 'co_hosp_pair_uq');
                $table->index(['company_id', 'active', 'is_default'], 'co_hosp_company_active_idx');
                $table->index('hospital_id', 'co_hosp_hospital_idx');
            });
        }

        $this->addCompanyColumn('hospital', 'hospital_empresa_idx');
        $this->addCompanyColumn('sala', 'sala_empresa_idx');
        $this->addCompanyColumn('agenda', 'agenda_empresa_idx');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migracion adopta estructuras que pueden pertenecer al sistema
        // legado. Eliminarlas durante un rollback causaria perdida de datos.
    }

    private function addCompanyColumn(string $tableName, string $indexName): void
    {
        if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'id_empresa')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
            $table->string('id_empresa', 20)->nullable()->index($indexName);
        });
    }
};
