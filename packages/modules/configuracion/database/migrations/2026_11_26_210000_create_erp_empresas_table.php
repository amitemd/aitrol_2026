<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_empresas', function (Blueprint $table): void {
            $table->id();
            $table->string('ruc', 13)->unique();
            $table->string('razon_social', 160);
            $table->string('nombre_comercial', 160)->nullable();
            $table->string('email', 254)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_empresas');
    }
};