<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrepareGeographicCatalogTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('pais')) {
            Schema::create('pais', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->string('nombre', 60);
                $table->tinyInteger('estado')->default(1);
                $table->string('id_usuariocrea', 13)->nullable();
                $table->string('id_usuariomod', 13)->nullable();
                $table->string('ip_creacion', 40)->nullable();
                $table->string('ip_modificacion', 40)->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
            });
        } elseif (! Schema::hasColumn('pais', 'estado')) {
            Schema::table('pais', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1);
            });
        }

        if (! Schema::hasTable('new_db_provincias')) {
            Schema::create('new_db_provincias', function (Blueprint $table) {
                $table->increments('id_provincia');
                $table->string('nombre_provincia', 255);
                $table->integer('id_pais_provincia')->nullable();
                $table->string('cod_provincia', 255)->nullable();
                $table->tinyInteger('estado')->default(1);
                $table->dateTime('created_at_provincia')->nullable();
                $table->timestamp('updated_at_provincia')->nullable();
            });
        } elseif (! Schema::hasColumn('new_db_provincias', 'estado')) {
            Schema::table('new_db_provincias', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1);
            });
        }

        if (! Schema::hasTable('new_db_ciudades')) {
            Schema::create('new_db_ciudades', function (Blueprint $table) {
                $table->increments('id_ciudad');
                $table->integer('id_pais_ciudad')->nullable();
                $table->string('provincia_ciudad', 255)->nullable();
                $table->string('cod_area_provincia_ciudad', 255)->nullable();
                $table->string('nombre_ciudad', 255);
                $table->dateTime('fecha_ciudad')->nullable();
                $table->integer('id_provincia_ciudad')->nullable();
                $table->tinyInteger('estado')->default(1);
                $table->timestamp('created_at_ciudad')->nullable();
                $table->timestamp('updated_at_ciudad')->nullable();
            });
        } elseif (! Schema::hasColumn('new_db_ciudades', 'estado')) {
            Schema::table('new_db_ciudades', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1);
            });
        }
    }

    public function down()
    {
        // No se eliminan catálogos geográficos para proteger datos existentes.
    }
}
