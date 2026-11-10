<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePacienteEmpresaTable extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('paciente_empresa')) {
            Schema::create('paciente_empresa', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('id_paciente', 13);
                $table->string('id_empresa', 32);
                $table->string('id_usuariocrea', 32)->nullable();
                $table->string('id_usuariomod', 32)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_paciente', 'id_empresa'], 'paciente_empresa_unique');
                $table->index(['id_empresa', 'id_paciente'], 'paciente_empresa_lookup');
            });
        }

        $this->backfillFromCompanyActivity();
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_empresa');
    }

    private function backfillFromCompanyActivity(): void
    {
        if (! Schema::hasTable('paciente') || ! Schema::hasTable('paciente_empresa')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($this->companyActivityTables() as $sourceTable) {
            $memberships = DB::table($sourceTable.' as source')
                ->join('paciente as patient', 'patient.id', '=', 'source.id_paciente')
                ->join('empresa as company', 'company.id', '=', 'source.id_empresa')
                ->whereNotNull('source.id_paciente')
                ->where('source.id_paciente', '<>', '')
                ->whereNotNull('source.id_empresa')
                ->where('source.id_empresa', '<>', '')
                ->distinct()
                ->selectRaw(
                    "source.id_paciente, source.id_empresa, '127.0.0.1' AS ip_creacion, "
                    ."'127.0.0.1' AS ip_modificacion, ? AS created_at, ? AS updated_at",
                    [$now, $now],
                );

            DB::table('paciente_empresa')->insertOrIgnoreUsing([
                'id_paciente',
                'id_empresa',
                'ip_creacion',
                'ip_modificacion',
                'created_at',
                'updated_at',
            ], $memberships);
        }
    }

    /**
     * @return list<string>
     */
    private function companyActivityTables(): array
    {
        return array_values(array_filter(
            Schema::getTableListing(null, false),
            fn (string $tableName): bool => $tableName !== 'paciente_empresa'
                && preg_match('/^[A-Za-z0-9_]+$/', $tableName) === 1
                && Schema::hasColumns($tableName, ['id_paciente', 'id_empresa']),
        ));
    }
}
