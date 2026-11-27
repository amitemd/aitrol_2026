<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_preferencias', function (Blueprint $table): void {
            $table->foreignId('id_empresa')->constrained('erp_empresas')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->json('distribucion');
            $table->string('grafico', 16);
            $table->primary(['id_empresa', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_preferencias');
    }
};
