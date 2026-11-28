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
        Schema::table('cb_operaciones', function (Blueprint $table): void {
            $table->foreignId('id_factura_venta')->nullable()->constrained('com_documentos')->restrictOnDelete();
            $table->index(['id_empresa', 'id_factura_venta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cb_operaciones', function (Blueprint $table): void {
            $table->dropIndex(['id_empresa', 'id_factura_venta']);
            $table->dropConstrainedForeignId('id_factura_venta');
        });
    }
};
