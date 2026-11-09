<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AllowPendingPentaxWithoutHistory extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('pentax') || ! Schema::hasColumn('pentax', 'hcid')) {
            return;
        }

        Schema::table('pentax', function (Blueprint $table) {
            // La historia clinica nace en admision; una agenda pendiente aun no tiene hcid.
            $table->unsignedInteger('hcid')->nullable()->change();
        });
    }

    public function down()
    {
        if (! Schema::hasTable('pentax') || ! Schema::hasColumn('pentax', 'hcid')) {
            return;
        }

        // No se eliminan agendas pendientes para forzar un rollback estructural.
        if (DB::table('pentax')->whereNull('hcid')->exists()) {
            return;
        }

        Schema::table('pentax', function (Blueprint $table) {
            $table->unsignedInteger('hcid')->nullable(false)->change();
        });
    }
}
