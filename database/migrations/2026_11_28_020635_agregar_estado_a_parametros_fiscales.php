<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('erp_parametros_fiscales', fn (Blueprint $tabla) => $tabla->boolean('activo')->default(true));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_parametros_fiscales', fn (Blueprint $tabla) => $tabla->dropColumn('activo'));
    }
};
