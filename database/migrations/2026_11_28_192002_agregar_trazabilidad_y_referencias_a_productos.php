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
        Schema::table('inv_productos', function (Blueprint $table): void {
            $table->date('fecha_vencimiento')->nullable();
            $table->string('codigo_externo', 120)->nullable();
            $table->string('id_producto_externo', 120)->nullable();
            $table->string('sistema_externo', 100)->nullable();
            $table->string('registro_sanitario', 120)->nullable();
            $table->string('codigo_qr', 1024)->nullable();
            $table->string('fabricante', 160)->nullable();
            $table->index(['id_empresa', 'fecha_vencimiento'], 'inv_productos_empresa_vencimiento_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_productos', function (Blueprint $table): void {
            $table->dropIndex('inv_productos_empresa_vencimiento_idx');
            $table->dropColumn(['fecha_vencimiento', 'codigo_externo', 'id_producto_externo',
                'sistema_externo', 'registro_sanitario', 'codigo_qr', 'fabricante']);
        });
    }
};
