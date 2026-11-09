<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sala') && Schema::hasColumn('sala', 'nombre_sala')) {
            Schema::table('sala', function (Blueprint $table) {
                $table->string('nombre_sala', 80)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sala') && Schema::hasColumn('sala', 'nombre_sala')) {
            Schema::table('sala', function (Blueprint $table) {
                $table->string('nombre_sala', 30)->change();
            });
        }
    }
};
