<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_clientes', function (Blueprint $table): void {
            $table->id();
            $table->string('tipo_identificacion', 15);
            $table->string('identificacion', 30);
            $table->string('nombre', 160);
            $table->string('email', 254)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo_identificacion', 'identificacion'], 'erp_clientes_identificacion_unique');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_clientes');
    }
};
