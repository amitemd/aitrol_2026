<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HardenV3AuditTrail extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_auditorias')) {
            return;
        }
        Schema::table('new_db_auditorias', function (Blueprint $table) {
            if (! Schema::hasColumn('new_db_auditorias', 'correlation_id')) {
                $table->string('correlation_id', 64)->nullable()->index('v3_audit_corr_idx');
            }
            if (! Schema::hasColumn('new_db_auditorias', 'action_auditoria')) {
                $table->string('action_auditoria', 20)->nullable();
            }
            if (! Schema::hasColumn('new_db_auditorias', 'before_auditoria')) {
                $table->longText('before_auditoria')->nullable();
            }
            if (! Schema::hasColumn('new_db_auditorias', 'after_auditoria')) {
                $table->longText('after_auditoria')->nullable();
            }
            if (! Schema::hasColumn('new_db_auditorias', 'previous_hash')) {
                $table->string('previous_hash', 64)->nullable();
            }
            if (! Schema::hasColumn('new_db_auditorias', 'event_hash')) {
                $table->string('event_hash', 64)->nullable()->index('v3_audit_hash_idx');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_auditorias')) {
            return;
        }
        Schema::table('new_db_auditorias', function (Blueprint $table) {
            $columns = ['correlation_id', 'action_auditoria', 'before_auditoria', 'after_auditoria', 'previous_hash', 'event_hash'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('new_db_auditorias', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
