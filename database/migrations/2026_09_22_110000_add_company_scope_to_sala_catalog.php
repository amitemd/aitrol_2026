<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sala') || Schema::hasColumn('sala', 'id_empresa')) {
            return;
        }

        Schema::table('sala', function (Blueprint $table) {
            $table->string('id_empresa', 20)->nullable()->after('id');
            $table->index('id_empresa', 'sala_empresa_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sala') || ! Schema::hasColumn('sala', 'id_empresa')) {
            return;
        }

        Schema::table('sala', function (Blueprint $table) {
            $table->dropIndex('sala_empresa_idx');
            $table->dropColumn('id_empresa');
        });
    }
};
