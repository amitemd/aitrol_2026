<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendaCirugiasTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('agenda_cirugias')) {
            return;
        }

        Schema::create('agenda_cirugias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_agenda')->unique();
            $table->string('modalidad', 40)->default('HOSPITALIZACION_PROGRAMADA');
            $table->string('id_anestesiologo', 20)->nullable();
            $table->text('indicacion_quirurgica');
            $table->dateTime('hora_quirurgica');
            $table->dateTime('fin_quirurgico');
            $table->unsignedSmallInteger('duracion_minutos')->default(120);
            $table->unsignedSmallInteger('preparacion_minutos')->default(30);
            $table->unsignedSmallInteger('limpieza_minutos')->default(30);
            $table->string('estado_quirurgico', 30)->default('TENTATIVA');
            $table->string('id_usuariocrea', 20)->nullable();
            $table->string('id_usuariomod', 20)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->string('ip_modificacion', 45)->nullable();
            $table->timestamps();

            $table->index(['hora_quirurgica', 'fin_quirurgico'], 'agenda_cirugia_horario');
            $table->index(['id_anestesiologo', 'hora_quirurgica'], 'agenda_cirugia_anestesiologo');
            $table->index('estado_quirurgico');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agenda_cirugias');
    }
}
