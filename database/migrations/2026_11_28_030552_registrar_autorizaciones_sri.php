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
        Schema::create('com_sri_consultas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_documento');
            $table->foreign(['id_empresa', 'id_documento'])->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado', 24);
            $table->json('mensajes');
            $table->longText('xml_autorizado')->nullable();
            $table->timestamp('created_at');
            $table->index(['id_empresa', 'id_documento', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_sri_consultas');
    }
};
