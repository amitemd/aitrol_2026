<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_productos', function (Blueprint $table): void {
            $table->unsignedTinyInteger('iva_codigo')->nullable();
        });
        Schema::table('com_documentos', function (Blueprint $table): void {
            $table->unsignedBigInteger('propina')->default(0);
        });
        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->foreign(['id_empresa', 'id_bodega'], 'com_lineas_bodega_fk')
                ->references(['id_empresa', 'id'])->on('inv_bodegas')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->dropForeign('com_lineas_bodega_fk');
            $table->dropColumn('id_bodega');
        });
        Schema::table('com_documentos', fn (Blueprint $table) => $table->dropColumn('propina'));
        Schema::table('inv_productos', fn (Blueprint $table) => $table->dropColumn('iva_codigo'));
    }
};
