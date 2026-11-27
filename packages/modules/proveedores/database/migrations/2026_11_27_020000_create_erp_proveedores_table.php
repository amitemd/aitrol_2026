<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_proveedores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_persona')->constrained('personas')->restrictOnDelete();
            $table->string('tipo_identificacion', 15);
            $table->string('identificacion', 30);
            $table->string('nombre', 160)->index();
            $table->string('email', 254)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->json('perfil')->nullable();
            $table->string('foto')->nullable();
            $table->json('documentos')->nullable();
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tipo_identificacion', 'identificacion'], 'erp_proveedores_identificacion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_proveedores');
    }
};
