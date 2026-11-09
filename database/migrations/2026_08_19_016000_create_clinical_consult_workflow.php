<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalConsultWorkflow extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_consult_requests')) {
            Schema::create('clinical_consult_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index('cl_con_enc_idx');
                $table->string('patient_id', 20)->index('cl_con_pat_idx');
                $table->unsignedBigInteger('interconsulta_id')->unique('cl_con_inter_uq');
                $table->string('priority', 20)->default('ROUTINE')->index('cl_con_prio_idx');
                $table->string('status', 30)->default('REQUESTED')->index('cl_con_status_idx');
                $table->string('target_service', 191)->nullable();
                $table->string('target_specialty', 191)->nullable();
                $table->string('requested_by', 191)->nullable();
                $table->dateTime('requested_at');
                $table->dateTime('due_at')->index('cl_con_due_idx');
                $table->string('assigned_to', 191)->nullable();
                $table->dateTime('accepted_at')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->unsignedInteger('escalation_level')->default(0);
                $table->dateTime('last_escalated_at')->nullable();
                $table->text('response_summary')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_consult_events')) {
            Schema::create('clinical_consult_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_consult_request_id')->index('cl_con_evt_req_idx');
                $table->string('event_type', 50)->index('cl_con_evt_type_idx');
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();
                $table->text('reason')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_consult_events');
        Schema::dropIfExists('clinical_consult_requests');
    }
}
