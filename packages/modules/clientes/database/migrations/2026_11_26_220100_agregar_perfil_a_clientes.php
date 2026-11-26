<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->json('perfil')->nullable();
            $table->json('documentos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->dropColumn(['perfil', 'documentos']);
        });
    }
};