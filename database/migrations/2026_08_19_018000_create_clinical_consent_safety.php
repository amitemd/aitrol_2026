<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalConsentSafety extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_consents')) {
            Schema::create('clinical_consents', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index('cl_cons_enc_idx');
                $table->string('patient_id', 20)->index('cl_cons_pat_idx');
                $table->unsignedInteger('template_id');
                $table->string('template_version', 30);
                $table->string('procedure_key', 100)->index('cl_cons_proc_idx');
                $table->string('status', 20)->default('SIGNED')->index('cl_cons_status_idx');
                $table->dateTime('signed_at');
                $table->string('signed_by', 191);
                $table->string('signer_role', 30);
                $table->string('representative_relationship', 100)->nullable();
                $table->boolean('identity_verified');
                $table->dateTime('expires_at')->nullable();
                $table->string('content_hash', 64);
                $table->text('evidence_reference')->nullable();
                $table->string('recorded_by', 191);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_procedure_authorizations')) {
            Schema::create('clinical_procedure_authorizations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index('cl_pa_enc_idx');
                $table->string('patient_id', 20)->index('cl_pa_pat_idx');
                $table->string('procedure_key', 100)->index('cl_pa_proc_idx');
                $table->text('indication');
                $table->unsignedBigInteger('consent_id')->nullable()->index('cl_pa_cons_idx');
                $table->boolean('identity_verified');
                $table->boolean('checklist_complete');
                $table->text('checklist_snapshot');
                $table->boolean('emergency_exception')->default(false);
                $table->text('emergency_reason')->nullable();
                $table->string('exception_authorized_by', 191)->nullable();
                $table->string('status', 20)->default('AUTHORIZED')->index('cl_pa_status_idx');
                $table->string('authorized_by', 191);
                $table->dateTime('authorized_at');
                $table->string('used_source_type', 50)->nullable();
                $table->string('used_source_id', 100)->nullable();
                $table->dateTime('used_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->unique(['procedure_key', 'status'], 'cl_pa_proc_status_uq');
            });
        }
        if (! Schema::hasTable('clinical_consent_events')) {
            Schema::create('clinical_consent_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index('cl_ce_enc_idx');
                $table->unsignedBigInteger('consent_id')->nullable()->index('cl_ce_cons_idx');
                $table->unsignedBigInteger('authorization_id')->nullable()->index('cl_ce_auth_idx');
                $table->string('event_type', 50)->index('cl_ce_type_idx');
                $table->text('detail')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_consent_events');
        Schema::dropIfExists('clinical_procedure_authorizations');
        Schema::dropIfExists('clinical_consents');
    }
}
