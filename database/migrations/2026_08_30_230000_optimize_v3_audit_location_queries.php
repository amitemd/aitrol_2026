<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizeV3AuditLocationQueries extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_auditorias')) {
            return;
        }

        Schema::table('new_db_auditorias', function (Blueprint $table) {
            if (! $this->hasIndex('v3_audit_company_order_idx')) {
                $table->index(['id_empresa_auditoria', 'id_auditoria'], 'v3_audit_company_order_idx');
            }
            if (! $this->hasIndex('v3_audit_user_company_order_idx')) {
                $table->index(['id_usuario_auditoria', 'id_empresa_auditoria', 'id_auditoria'], 'v3_audit_user_company_order_idx');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_auditorias')) {
            return;
        }

        Schema::table('new_db_auditorias', function (Blueprint $table) {
            if ($this->hasIndex('v3_audit_company_order_idx')) {
                $table->dropIndex('v3_audit_company_order_idx');
            }
            if ($this->hasIndex('v3_audit_user_company_order_idx')) {
                $table->dropIndex('v3_audit_user_company_order_idx');
            }
        });
    }

    private function hasIndex($name)
    {
        $rows = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            ['new_db_auditorias', $name]
        );

        return count($rows) > 0;
    }
}
