<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregasMercaderiaTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ct_entregas_mercaderia')) {
            Schema::create('ct_entregas_mercaderia', function (Blueprint $table) {
                $table->increments('id');
                $table->string('numero', 20);
                $table->string('id_empresa', 20);
                $table->string('id_cliente', 20);
                $table->integer('id_bodega_contable');
                $table->integer('id_guia_remision')->nullable();
                $table->integer('id_venta')->nullable();
                $table->bigInteger('id_movimiento_inventario')->unsigned()->nullable();
                $table->integer('id_asiento_costo')->nullable();
                $table->date('fecha_entrega');
                $table->date('fecha_fin_traslado');
                $table->string('transportista_identificacion', 20);
                $table->string('transportista_nombre', 255);
                $table->string('transportista_email', 255)->nullable();
                $table->string('placa', 20);
                $table->string('direccion_partida', 255);
                $table->string('direccion_destino', 255);
                $table->string('ruta', 255)->nullable();
                $table->string('motivo_traslado', 255);
                $table->text('observacion')->nullable();
                $table->string('estado', 20)->default('ENTREGADA');
                $table->string('id_usuariocrea', 50);
                $table->string('id_usuariomod', 50);
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'numero'], 'uq_entrega_empresa_numero');
                $table->index(['id_empresa', 'estado']);
            });
        }

        if (!Schema::hasTable('ct_entregas_mercaderia_detalle')) {
            Schema::create('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_entrega');
                $table->integer('id_producto');
                $table->string('codigo_producto', 100);
                $table->string('nombre_producto', 255);
                $table->decimal('cantidad', 14, 4);
                $table->decimal('costo_unitario', 14, 6)->default(0);
                $table->decimal('costo_total', 14, 6)->default(0);
                $table->text('descripcion')->nullable();
                $table->string('id_usuariocrea', 50);
                $table->string('id_usuariomod', 50);
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->foreign('id_entrega')->references('id')->on('ct_entregas_mercaderia');
                $table->index(['id_entrega', 'id_producto']);
            });
        }

        if (!Schema::hasTable('ct_entregas_mercaderia_trazabilidad')) {
            Schema::create('ct_entregas_mercaderia_trazabilidad', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_entrega');
                $table->string('evento', 50);
                $table->string('documento_tipo', 50)->nullable();
                $table->string('documento_id', 100)->nullable();
                $table->text('detalle')->nullable();
                $table->string('id_usuario', 50);
                $table->string('ip', 45)->nullable();
                $table->timestamps();
                $table->foreign('id_entrega')->references('id')->on('ct_entregas_mercaderia');
                $table->index(['id_entrega', 'evento']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ct_entregas_mercaderia_trazabilidad');
        Schema::dropIfExists('ct_entregas_mercaderia_detalle');
        Schema::dropIfExists('ct_entregas_mercaderia');
    }
}
