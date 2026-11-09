<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalDeathWorkflow extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_death_cases')) {
            Schema::create('clinical_death_cases', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->unique('cl_death_enc_uq');
                $table->unsignedBigInteger('closure_id')->nullable()->index('cl_death_close_idx');
                $table->string('patient_id', 20)->index('cl_death_pat_idx');
                $table->string('status', 30)->default('DRAFT')->index('cl_death_status_idx');
                $table->dateTime('occurred_at');
                $table->string('pronounced_by', 191);
                $table->dateTime('pronounced_at');
                $table->string('death_location', 191);
                $table->text('probable_cause');
                $table->string('death_manner', 30);
                $table->string('certificate_number', 100)->unique('cl_death_cert_uq');
                $table->boolean('identity_verified')->default(false);
                $table->boolean('body_identified')->default(false);
                $table->string('family_contact_name', 191);
                $table->dateTime('family_notified_at');
                $table->string('family_notified_by', 191);
                $table->dateTime('morgue_notified_at');
                $table->string('morgue_notified_by', 191);
                $table->text('notes')->nullable();
                $table->string('completed_by', 191)->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_death_events')) {
            Schema::create('clinical_death_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_death_case_id')->index('cl_death_evt_case_idx');
                $table->string('event_type', 50)->index('cl_death_evt_type_idx');
                $table->text('detail')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_death_events');
        Schema::dropIfExists('clinical_death_cases');
    }
}
