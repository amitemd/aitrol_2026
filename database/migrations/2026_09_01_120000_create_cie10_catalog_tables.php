<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCie10CatalogTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('cie_10_3')) {
            Schema::create('cie_10_3', function (Blueprint $table) {
                $table->string('id', 10)->primary();
                $table->string('descripcion', 255);
                $table->tinyInteger('estado')->default(1);
                $table->string('id_usuariocrea', 13);
                $table->string('id_usuariomod', 13);
                $table->string('ip_creacion', 40)->default('127.0.0.1');
                $table->string('ip_modificacion', 40)->default('127.0.0.1');
                $table->timestamps();
                $table->index('estado', 'cie_10_3_estado_index');
            });
        }

        if (! Schema::hasTable('cie_10_4')) {
            Schema::create('cie_10_4', function (Blueprint $table) {
                $table->string('id', 10)->primary();
                $table->string('descripcion', 255);
                $table->string('id_cie_10_3', 10);
                $table->tinyInteger('estado')->default(1);
                $table->string('id_usuariocrea', 13);
                $table->string('id_usuariomod', 13);
                $table->string('ip_creacion', 40)->default('127.0.0.1');
                $table->string('ip_modificacion', 40)->default('127.0.0.1');
                $table->timestamps();
                $table->index('estado', 'cie_10_4_estado_index');
                $table->foreign('id_cie_10_3', 'cie_10_4_parent_foreign')
                    ->references('id')->on('cie_10_3')->onUpdate('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cie_10_4');
        Schema::dropIfExists('cie_10_3');
    }
}
