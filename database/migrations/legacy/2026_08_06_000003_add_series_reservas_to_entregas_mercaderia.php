<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\ChecksExistingIndexes;

class AddSeriesReservasToEntregasMercaderia extends Migration
{
    use ChecksExistingIndexes;

    public function up()
    {
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->after('numero');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia', 'uq_entrega_idempotency_key')) {
                $table->unique('idempotency_key', 'uq_entrega_idempotency_key');
            }
        });

        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'id_inv_inventario_serie')) {
                $table->bigInteger('id_inv_inventario_serie')->unsigned()->nullable()->after('id_producto');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'serie')) {
                $table->string('serie', 255)->nullable()->after('id_inv_inventario_serie');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'lote')) {
                $table->string('lote', 255)->nullable()->after('serie');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'fecha_vence')) {
                $table->date('fecha_vence')->nullable()->after('lote');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'existencia_antes')) {
                $table->decimal('existencia_antes', 14, 4)->default(0)->after('fecha_vence');
            }
            if (!$this->hasIndex('ct_entregas_mercaderia_detalle', 'idx_entrega_det_inv_serie')) {
                $table->index('id_inv_inventario_serie', 'idx_entrega_det_inv_serie');
            }
        });

        if (!Schema::hasTable('ct_entregas_mercaderia_reservas')) {
            Schema::create('ct_entregas_mercaderia_reservas', function (Blueprint $table) {
                $table->increments('id');
                $table->string('token', 64);
                $table->string('id_empresa', 20);
                $table->integer('id_bodega_contable');
                $table->integer('id_producto');
                $table->bigInteger('id_inv_inventario_serie')->unsigned();
                $table->decimal('cantidad', 14, 4);
                $table->dateTime('expires_at');
                $table->timestamps();
                $table->unique(['token', 'id_inv_inventario_serie'], 'uq_entrega_reserva_token_serie');
                $table->index(['id_inv_inventario_serie', 'expires_at'], 'idx_entrega_reserva_serie_expira');
                $table->index(['id_empresa', 'token'], 'idx_entrega_reserva_empresa_token');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ct_entregas_mercaderia_reservas');

        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_entrega_det_inv_serie');
            $table->dropColumn([
                'id_inv_inventario_serie',
                'serie',
                'lote',
                'fecha_vence',
                'existencia_antes',
            ]);
        });

        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            $table->dropUnique('uq_entrega_idempotency_key');
            $table->dropColumn('idempotency_key');
        });
    }
}
