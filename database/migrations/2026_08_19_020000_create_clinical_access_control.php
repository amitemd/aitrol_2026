<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalAccessControl extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_access_scopes')) {
            Schema::create('clinical_access_scopes', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('user_id', 191)->nullable()->index('cl_as_user_idx');
                $t->unsignedInteger('role_id')->nullable()->index('cl_as_role_idx');
                $t->string('encounter_id', 36)->nullable()->index('cl_as_enc_idx');
                $t->string('care_setting', 30)->nullable();
                $t->string('service_code', 50)->nullable();
                $t->string('action', 60)->index('cl_as_action_idx');
                $t->dateTime('valid_from')->nullable();
                $t->dateTime('valid_until')->nullable();
                $t->boolean('active')->default(true);
                $t->string('granted_by', 191);
                $t->text('reason');
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_break_glass_accesses')) {
            Schema::create('clinical_break_glass_accesses', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('encounter_id', 36)->index('cl_bg_enc_idx');
                $t->string('user_id', 191)->index('cl_bg_user_idx');
                $t->string('action', 60)->index('cl_bg_action_idx');
                $t->text('reason');
                $t->string('status', 20)->default('ACTIVE')->index('cl_bg_status_idx');
                $t->dateTime('activated_at');
                $t->dateTime('expires_at');
                $t->dateTime('review_due_at')->index('cl_bg_due_idx');
                $t->string('reviewed_by', 191)->nullable();
                $t->dateTime('reviewed_at')->nullable();
                $t->string('review_decision', 20)->nullable();
                $t->text('review_notes')->nullable();
                $t->string('ip_address', 45)->nullable();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_access_events')) {
            Schema::create('clinical_access_events', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('encounter_id', 36)->nullable()->index('cl_ae_enc_idx');
                $t->string('user_id', 191)->nullable()->index('cl_ae_user_idx');
                $t->string('action', 60);
                $t->string('decision', 30)->index('cl_ae_dec_idx');
                $t->string('source', 30);
                $t->text('detail')->nullable();
                $t->string('ip_address', 45)->nullable();
                $t->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_access_events');
        Schema::dropIfExists('clinical_break_glass_accesses');
        Schema::dropIfExists('clinical_access_scopes');
    }
}
