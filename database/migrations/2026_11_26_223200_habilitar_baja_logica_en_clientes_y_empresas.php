<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['erp_clientes', 'erp_empresas'] as $tabla) {
            Schema::table($tabla, fn (Blueprint $table) => $table->softDeletes());
        }
    }

    public function down(): void
    {
        foreach (['erp_clientes', 'erp_empresas'] as $tabla) {
            Schema::table($tabla, fn (Blueprint $table) => $table->dropSoftDeletes());
        }
    }
};