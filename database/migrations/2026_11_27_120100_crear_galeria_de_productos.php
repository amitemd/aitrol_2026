<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inv_producto_imagenes', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $tabla->unsignedBigInteger('id_producto');
            $tabla->foreign(['id_empresa', 'id_producto'], 'inv_imagen_producto_fk')
                ->references(['id_empresa', 'id'])->on('inv_productos')->restrictOnDelete();
            $tabla->unsignedSmallInteger('orden');
            $tabla->string('ruta', 255);
            $tabla->timestamps();
            $tabla->index(['id_empresa', 'id_producto', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_producto_imagenes');
    }
};
