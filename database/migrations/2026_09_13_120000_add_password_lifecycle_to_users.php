<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPasswordLifecycleToUsers extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'password_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('password_changed_at')->nullable();
            });
        }

        // La política comienza en el despliegue para no bloquear de golpe a
        // todas las cuentas existentes. Desde aquí vencerán cada 72 días.
        DB::table('users')->whereNull('password_changed_at')->update([
            'password_changed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'password_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('password_changed_at');
            });
        }
    }
}
