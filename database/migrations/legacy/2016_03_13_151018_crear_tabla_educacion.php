<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Support\Migrations\UserForeignKey;

class CrearTablaEducacion extends Migration
{
    public function up()
    {
        $reference = new UserForeignKey();
        if (!Schema::hasTable('educacion')) {
            Schema::create('educacion', function (Blueprint $table) use ($reference) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $reference->addColumn($table);
                $table->index('idUsuario');
                $table->integer('idTipoeducacion');
                $table->string('titulo', 100);
                $table->string('institucion', 60);
                $table->string('anio', 5);
                $table->timestamps();
            });
        }
        $reference->ensure('educacion');
    }

    public function down()
    {
        // La tabla puede contener información anterior a esta migración.
    }
}
