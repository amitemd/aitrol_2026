<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressiveEmergencyIntake extends Migration
{
    public function up()
    {
        Schema::table('paciente', function (Blueprint $table) {
            $table->boolean('emergency_identity_pending')->default(false);
            $table->string('emergency_registration_token', 64)->nullable()->unique();
        });
        Schema::create('clinical_emergency_intake_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->string('stage', 20);
            $table->unsignedBigInteger('triage_assessment_id')->nullable();
            $table->json('clinical_data');
            $table->json('patient_changes');
            $table->string('recorded_by', 30);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_emergency_intake_updates');
        Schema::table('paciente', function (Blueprint $table) {
            $table->dropColumn(['emergency_identity_pending', 'emergency_registration_token']);
        });
    }
}
