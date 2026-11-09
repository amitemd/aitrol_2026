<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBloodBankAndOncologyWorkflows extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('blood_bank_units')) {
            Schema::create('blood_bank_units', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20)->nullable()->index('bb_unit_company_idx');
                $table->string('code', 40);
                $table->string('blood_type', 3)->index('bb_unit_type_idx');
                $table->string('component', 30)->index('bb_unit_component_idx');
                $table->unsignedInteger('volume_ml')->default(450);
                $table->string('donor_code', 40)->nullable();
                $table->string('location', 120)->nullable();
                $table->dateTime('collected_at');
                $table->dateTime('expires_at')->index('bb_unit_expiry_idx');
                $table->string('status', 20)->default('AVAILABLE')->index('bb_unit_status_idx');
                $table->string('created_by', 191)->nullable();
                $table->string('updated_by', 191)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'code'], 'bb_unit_company_code_uq');
            });
        }

        if (! Schema::hasTable('blood_bank_requests')) {
            Schema::create('blood_bank_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20)->nullable()->index('bb_req_company_idx');
                $table->string('encounter_id', 36)->nullable()->index('bb_req_encounter_idx');
                $table->string('patient_id', 20)->index('bb_req_patient_idx');
                $table->string('patient_name', 191);
                $table->string('blood_type', 3)->index('bb_req_type_idx');
                $table->string('component', 30)->index('bb_req_component_idx');
                $table->unsignedInteger('units_required')->default(1);
                $table->string('priority', 20)->default('ROUTINE')->index('bb_req_priority_idx');
                $table->string('status', 30)->default('PENDING')->index('bb_req_status_idx');
                $table->text('indication');
                $table->text('notes')->nullable();
                $table->string('requested_by', 191)->nullable();
                $table->dateTime('requested_at');
                $table->dateTime('due_at')->nullable()->index('bb_req_due_idx');
                $table->string('crossmatch_result', 30)->nullable();
                $table->dateTime('delivered_at')->nullable();
                $table->string('created_by', 191)->nullable();
                $table->string('updated_by', 191)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blood_bank_request_units')) {
            Schema::create('blood_bank_request_units', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('blood_bank_request_id')->index('bb_link_request_idx');
                $table->unsignedBigInteger('blood_bank_unit_id')->index('bb_link_unit_idx');
                $table->string('status', 20)->default('RESERVED');
                $table->dateTime('reserved_at');
                $table->dateTime('delivered_at')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->timestamps();
                $table->unique('blood_bank_unit_id', 'bb_link_unit_uq');
            });
        }

        if (! Schema::hasTable('oncology_consults')) {
            Schema::create('oncology_consults', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20)->nullable()->index('onco_company_idx');
                $table->string('encounter_id', 36)->nullable()->index('onco_encounter_idx');
                $table->string('patient_id', 20)->index('onco_patient_idx');
                $table->string('patient_name', 191);
                $table->string('cancer_type', 191);
                $table->string('clinical_stage', 60)->nullable();
                $table->string('target_specialty', 100)->default('Oncología');
                $table->string('priority', 20)->default('ROUTINE')->index('onco_priority_idx');
                $table->string('status', 30)->default('REQUESTED')->index('onco_status_idx');
                $table->text('reason');
                $table->text('clinical_summary');
                $table->boolean('multidisciplinary')->default(false);
                $table->string('requested_by', 191)->nullable();
                $table->string('assigned_to', 191)->nullable();
                $table->dateTime('requested_at');
                $table->dateTime('due_at')->index('onco_due_idx');
                $table->dateTime('accepted_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->text('response_plan')->nullable();
                $table->dateTime('next_action_at')->nullable();
                $table->string('created_by', 191)->nullable();
                $table->string('updated_by', 191)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('oncology_consult_events')) {
            Schema::create('oncology_consult_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('oncology_consult_id')->index('onco_event_consult_idx');
                $table->string('event_type', 50);
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();
                $table->text('notes')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('oncology_consult_events');
        Schema::dropIfExists('oncology_consults');
        Schema::dropIfExists('blood_bank_request_units');
        Schema::dropIfExists('blood_bank_requests');
        Schema::dropIfExists('blood_bank_units');
    }
}
