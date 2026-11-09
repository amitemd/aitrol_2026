<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtProductosProcedimientosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_productos_procedimientos')) {
            return;
        }
        Schema::create('ct_productos_procedimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_producto')->index();
            $table->string('id_procedimiento', 255)->nullable();
            $table->string('id_seguro', 255)->nullable();
            $table->string('nombre')->nullable();
            $table->decimal('precio', 14, 2)->default(0);
            $table->string('id_usuariocrea', 255)->nullable();
            $table->string('id_usuariomod', 255)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->string('ip_modificacion', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ct_productos_procedimientos');
    }
}
