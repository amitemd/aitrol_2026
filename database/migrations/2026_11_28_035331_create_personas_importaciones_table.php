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
        Schema::create('personas_importaciones', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->string('archivo');
            $table->string('estado', 20)->default('pendiente');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('procesadas')->default(0);
            $table->unsignedInteger('importadas')->default(0);
            $table->unsignedInteger('existentes')->default(0);
            $table->unsignedInteger('omitidas')->default(0);
            $table->unsignedInteger('procesadas_inicio')->default(0);
            $table->string('error')->nullable();
            $table->timestamp('inicio_progreso')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
        Schema::table('personas', function (Blueprint $table): void {
            $table->index('identificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table): void {
            $table->dropIndex(['identificacion']);
        });
        Schema::dropIfExists('personas_importaciones');
    }
};
