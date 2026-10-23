<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\ChecksExistingIndexes;

class AddBodegaInventarioToEntregasMercaderia extends Migration
{
    use ChecksExistingIndexes;

    public function up()
    {
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'id_bodega_inventario')) {
                $table->unsignedInteger('id_bodega_inventario')->nullable()->after('id_bodega_contable');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia', 'idx_entrega_bodega_inv')) {
                $table->index('id_bodega_inventario', 'idx_entrega_bodega_inv');
            }
        });

        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'id_bodega_inventario')) {
                $table->unsignedInteger('id_bodega_inventario')->nullable()->after('id_bodega_contable');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia_detalle', 'idx_entrega_det_bodega_inv')) {
                $table->index('id_bodega_inventario', 'idx_entrega_det_bodega_inv');
            }
        });

        Schema::table('ct_entregas_mercaderia_reservas', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_reservas', 'id_bodega_inventario')) {
                $table->unsignedInteger('id_bodega_inventario')->nullable()->after('id_bodega_contable');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia_reservas', 'idx_entrega_res_bodega_inv')) {
                $table->index('id_bodega_inventario', 'idx_entrega_res_bodega_inv');
            }
        });

        Schema::table('ct_entregas_mercaderia_movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_movimientos', 'id_bodega_inventario')) {
                $table->unsignedInteger('id_bodega_inventario')->nullable()->after('id_bodega_contable');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia_movimientos', 'idx_entrega_mov_bodega_inv')) {
                $table->index(['id_entrega', 'id_bodega_inventario'], 'idx_entrega_mov_bodega_inv');
            }
        });
    }

    public function down()
    {
        Schema::table('ct_entregas_mercaderia_movimientos', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_mov_bodega_inv');
            $table->dropColumn('id_bodega_inventario');
        });

        Schema::table('ct_entregas_mercaderia_reservas', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_res_bodega_inv');
            $table->dropColumn('id_bodega_inventario');
        });

        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_det_bodega_inv');
            $table->dropColumn('id_bodega_inventario');
        });

        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_bodega_inv');
            $table->dropColumn('id_bodega_inventario');
        });
    }
}
