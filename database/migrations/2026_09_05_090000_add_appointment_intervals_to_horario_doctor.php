<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentIntervalsToHorarioDoctor extends Migration
{
    public function up()
    {
        Schema::table('horario_doctor', function (Blueprint $table) {
            if (! Schema::hasColumn('horario_doctor', 'intervalo_consulta')) {
                $table->unsignedSmallInteger('intervalo_consulta')->default(30);
            }
            if (! Schema::hasColumn('horario_doctor', 'intervalo_procedimiento')) {
                $table->unsignedSmallInteger('intervalo_procedimiento')->default(30);
            }
        });
    }

    public function down()
    {
        Schema::table('horario_doctor', function (Blueprint $table) {
            if (Schema::hasColumn('horario_doctor', 'intervalo_consulta')) {
                $table->dropColumn('intervalo_consulta');
            }
            if (Schema::hasColumn('horario_doctor', 'intervalo_procedimiento')) {
                $table->dropColumn('intervalo_procedimiento');
            }
        });
    }
}
