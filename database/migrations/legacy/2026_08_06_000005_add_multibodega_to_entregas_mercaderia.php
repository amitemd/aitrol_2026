<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\ChecksExistingIndexes;

class AddMultibodegaToEntregasMercaderia extends Migration
{
    use ChecksExistingIndexes;

    public function up()
    {
        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'id_bodega_contable')) {
                $table->integer('id_bodega_contable')->nullable()->after('id_producto');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia_detalle', 'idx_entrega_det_bodega')) {
                $table->index('id_bodega_contable', 'idx_entrega_det_bodega');
            }
        });

        if (!Schema::hasTable('ct_entregas_mercaderia_movimientos')) {
            Schema::create('ct_entregas_mercaderia_movimientos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_entrega');
                $table->integer('id_bodega_contable');
                $table->bigInteger('id_movimiento_inventario')->unsigned();
                $table->decimal('costo_total', 14, 6)->default(0);
                $table->timestamps();
                $table->unique(['id_entrega', 'id_movimiento_inventario'], 'uq_entrega_movimiento');
                $table->index(['id_entrega', 'id_bodega_contable'], 'idx_entrega_mov_bodega');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ct_entregas_mercaderia_movimientos');

        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_det_bodega');
            $table->dropColumn('id_bodega_contable');
        });
    }
}
