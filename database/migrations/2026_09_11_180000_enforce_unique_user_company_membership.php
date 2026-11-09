<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnforceUniqueUserCompanyMembership extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('usuario_empresa')) {
            return;
        }

        // Las filas repetidas representan la misma membresía. Se conserva la
        // más antigua antes de impedir nuevas duplicaciones por concurrencia.
        DB::statement('DELETE duplicate_membership FROM usuario_empresa duplicate_membership
            INNER JOIN usuario_empresa original_membership
                ON original_membership.id_empresa = duplicate_membership.id_empresa
                AND original_membership.id_usuario = duplicate_membership.id_usuario
                AND original_membership.id < duplicate_membership.id');

        if (! $this->hasIndex('usuario_empresa', 'usuario_empresa_company_user_uq')) {
            Schema::table('usuario_empresa', function (Blueprint $table) {
                $table->unique(['id_empresa', 'id_usuario'], 'usuario_empresa_company_user_uq');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('usuario_empresa') && $this->hasIndex('usuario_empresa', 'usuario_empresa_company_user_uq')) {
            Schema::table('usuario_empresa', function (Blueprint $table) {
                $table->dropUnique('usuario_empresa_company_user_uq');
            });
        }
    }

    private function hasIndex($table, $name)
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)->where('INDEX_NAME', $name)->exists();
    }
}
