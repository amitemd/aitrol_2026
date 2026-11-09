<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmergencyPatientRegistrationStatus extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('paciente', 'registro_emergencia_pendiente')) {
            Schema::table('paciente', function (Blueprint $table) {
                $table->boolean('registro_emergencia_pendiente')->default(false)->index();
            });
        }
    }

    public function down()
    {
        Schema::table('paciente', function (Blueprint $table) {
            $table->dropIndex(['registro_emergencia_pendiente']);
            $table->dropColumn('registro_emergencia_pendiente');
        });
    }
}
