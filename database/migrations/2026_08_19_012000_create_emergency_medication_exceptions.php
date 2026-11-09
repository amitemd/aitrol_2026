<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyMedicationExceptions extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clinical_emergency_medication_exceptions')) {
            return;
        }

        Schema::create('clinical_emergency_medication_exceptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('encounter_id', 36)->index('cem_exc_enc_idx');
            $table->unsignedInteger('solicitud_id')->index('cem_exc_sol_idx');
            $table->string('patient_id', 20)->index('cem_exc_pat_idx');
            $table->unsignedInteger('medication_id')->index('cem_exc_med_idx');
            $table->unsignedBigInteger('provisional_order_id')->index('cem_exc_prov_idx');
            $table->unsignedBigInteger('administration_id')->index('cem_exc_admin_idx');
            $table->unsignedBigInteger('regularized_order_id')->nullable()->index('cem_exc_reg_order_idx');
            $table->string('protocol_code', 100);
            $table->text('justification');
            $table->string('status', 30)->default('PENDING')->index('cem_exc_status_idx');
            $table->dateTime('regularization_due_at')->index('cem_exc_due_idx');
            $table->dateTime('regularized_at')->nullable();
            $table->string('created_by', 191);
            $table->string('regularized_by', 191)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_emergency_medication_exceptions');
    }
}
