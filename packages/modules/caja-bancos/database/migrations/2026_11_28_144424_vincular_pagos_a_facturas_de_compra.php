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
        Schema::table('cb_operaciones', function (Blueprint $table) {
            $table->foreignId('id_factura_compra')->nullable()->constrained('com_documentos')->restrictOnDelete();
            $table->index(['id_empresa', 'id_sucursal', 'id_factura_compra'], 'cb_operaciones_factura_compra_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cb_operaciones', function (Blueprint $table) {
            $table->dropIndex('cb_operaciones_factura_compra_idx');
            $table->dropConstrainedForeignId('id_factura_compra');
        });
    }
};
