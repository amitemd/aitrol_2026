<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePacienteEmpresaTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('paciente_empresa')) {
            Schema::create('paciente_empresa', function (Blueprint $table) {
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

    public function down()
    {
        Schema::dropIfExists('paciente_empresa');
    }

    private function backfillFromCompanyActivity()
    {
        if (! Schema::hasTable('paciente') || ! Schema::hasTable('paciente_empresa')) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $tables = DB::table('information_schema.columns as patient_column')
            ->join('information_schema.columns as company_column', function ($join) {
                $join->on('company_column.table_schema', '=', 'patient_column.table_schema')
                    ->on('company_column.table_name', '=', 'patient_column.table_name');
            })
            ->whereRaw('patient_column.table_schema = DATABASE()')
            ->where('patient_column.column_name', 'id_paciente')
            ->where('company_column.column_name', 'id_empresa')
            ->pluck('patient_column.table_name');

        $now = date('Y-m-d H:i:s');
        foreach ($tables as $source) {
            if ($source === 'paciente_empresa' || ! preg_match('/^[A-Za-z0-9_]+$/', $source)) {
                continue;
            }
            DB::statement("INSERT IGNORE INTO paciente_empresa
                (id_paciente, id_empresa, ip_creacion, ip_modificacion, created_at, updated_at)
                SELECT DISTINCT source.id_paciente, source.id_empresa, '127.0.0.1', '127.0.0.1', ?, ?
                FROM `{$source}` AS source
                INNER JOIN paciente AS patient ON patient.id = source.id_paciente
                INNER JOIN empresa AS company ON company.id = source.id_empresa
                WHERE source.id_paciente IS NOT NULL AND source.id_paciente <> ''
                  AND source.id_empresa IS NOT NULL AND source.id_empresa <> ''", [$now, $now]);
        }
    }
}
