<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\UserForeignKey;

class CrearTablaPublicaciones extends Migration
{
    public function up()
    {
        $reference = new UserForeignKey();
        if (!Schema::hasTable('publicaciones')) {
            Schema::create('publicaciones', function (Blueprint $table) use ($reference) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $reference->addColumn($table);
                $table->index('idUsuario');
                $table->integer('idTipopublicacion');
                $table->string('titulo', 150);
                $table->string('autores', 100);
                $table->string('universidad', 60);
                $table->string('anio', 5);
                $table->string('pais', 40);
                $table->string('revista', 100);
                $table->string('volumen', 10);
                $table->string('numero', 5);
                $table->string('pageI', 5);
                $table->string('pageF', 5);
                $table->string('volumenL', 10);
                $table->string('numeroL', 5);
                $table->string('ciudad', 40);
                $table->string('edicion', 40);
                $table->string('editorial', 40);
                $table->string('ruta', 150);
                $table->timestamps();
            });
        }
        $reference->ensure('publicaciones');
    }

    public function down()
    {
        // Se conservan la tabla y sus registros.
    }
}
