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
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->dropForeign(['id_usuario_creacion']);
            $table->dropForeign(['id_usuario_modificacion']);
            $table->dropColumn(['id_usuario_creacion', 'id_usuario_modificacion']);
        });
    }
};
