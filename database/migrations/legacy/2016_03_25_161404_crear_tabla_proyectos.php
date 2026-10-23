<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\UserForeignKey;

class CrearTablaProyectos extends Migration
{
    public function up()
    {
        $reference = new UserForeignKey();
        if (!Schema::hasTable('proyectos')) {
            Schema::create('proyectos', function (Blueprint $table) use ($reference) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $reference->addColumn($table);
                $table->index('idUsuario');
                $table->string('titulo', 150);
                $table->string('integrantes', 100);
                $table->string('descripcion', 150);
                $table->string('objetivo', 150);
                $table->string('estado', 20);
                $table->date('fecha');
                $table->string('pais', 20);
                $table->string('financiamiento', 50);
                $table->string('pclave', 100);
                $table->string('ruta', 150);
                $table->timestamps();
            });
        }
        $reference->ensure('proyectos');
    }

    public function down()
    {
        // Se conservan la tabla y sus registros.
    }
}
