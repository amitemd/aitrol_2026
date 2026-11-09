<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3ParkingCollectionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_parking_collections')) {
            return;
        }

        Schema::create('v3_parking_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_empresa', 20);
            $table->string('id_caja', 50);
            $table->unsignedInteger('id_tipo_pago');
            $table->dateTime('hora_ingreso');
            $table->dateTime('hora_salida');
            $table->string('nombre', 255);
            $table->decimal('valor', 12, 2);
            $table->string('numero_comprobante', 80)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->string('id_usuariocrea', 13);
            $table->string('id_usuariomod', 13);
            $table->string('ip_creacion', 45);
            $table->string('ip_modificacion', 45);
            $table->timestamps();

            $table->index(['id_empresa', 'hora_salida'], 'parking_company_exit_idx');
            $table->index(['id_empresa', 'id_caja', 'estado'], 'parking_company_cash_status_idx');
            $table->index(['id_empresa', 'id_usuariocrea', 'hora_salida'], 'parking_company_user_exit_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_parking_collections');
    }
}
