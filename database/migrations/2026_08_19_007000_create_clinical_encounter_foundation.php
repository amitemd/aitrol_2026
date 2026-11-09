<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalEncounterFoundation extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_encounters')) {
            Schema::create('clinical_encounters', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('id_paciente', 45)->index();
                $table->string('id_empresa', 45)->nullable()->index();
                $table->unsignedTinyInteger('id_hospital')->nullable()->index();
                $table->string('care_setting', 24)->index();
                $table->string('status', 30)->default('ACTIVE')->index();
                $table->dateTime('started_at');
                $table->dateTime('ended_at')->nullable();
                $table->string('close_reason', 80)->nullable();
                $table->string('id_usuariocrea', 45)->nullable();
                $table->string('id_usuariomod', 45)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->index(['id_paciente', 'status'], 'clinical_encounter_patient_status');
            });
        }

        if (! Schema::hasTable('clinical_encounter_links')) {
            Schema::create('clinical_encounter_links', function (Blueprint $table) {
                $table->increments('id');
                $table->string('encounter_id', 36)->index();
                $table->string('source_type', 40);
                $table->string('source_id', 80);
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'clinical_encounter_source_unique');
            });
        }

        if (! Schema::hasTable('clinical_encounter_events')) {
            Schema::create('clinical_encounter_events', function (Blueprint $table) {
                $table->increments('id');
                $table->string('encounter_id', 36)->index();
                $table->string('event_type', 40)->index();
                $table->string('from_value', 50)->nullable();
                $table->string('to_value', 50)->nullable();
                $table->text('reason')->nullable();
                $table->string('actor_id', 45)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_encounter_overrides')) {
            Schema::create('clinical_encounter_overrides', function (Blueprint $table) {
                $table->increments('id');
                $table->string('encounter_id', 36)->index();
                $table->string('conflicting_encounter_id', 36)->index();
                $table->string('requested_setting', 24);
                $table->text('reason');
                $table->string('authorized_by', 45);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_encounter_overrides');
        Schema::dropIfExists('clinical_encounter_events');
        Schema::dropIfExists('clinical_encounter_links');
        Schema::dropIfExists('clinical_encounters');
    }
}
