<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodontologiaPeriodontogramasTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('perio_periodontogramas')) {
            return;
        }

        Schema::create('perio_periodontogramas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_agenda');
            $table->unsignedInteger('hcid')->nullable();
            $table->string('id_paciente', 20);
            $table->string('id_doctor', 20)->nullable();
            $table->longText('payload_json');
            $table->tinyInteger('estado')->default(1);
            $table->string('id_usuariocrea', 20)->nullable();
            $table->string('id_usuariomod', 20)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->string('ip_modificacion', 45)->nullable();
            $table->timestamps();
            $table->unique('id_agenda', 'perio_periodontogramas_agenda_unique');
            $table->index('id_paciente', 'perio_periodontogramas_paciente_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perio_periodontogramas');
    }
}
