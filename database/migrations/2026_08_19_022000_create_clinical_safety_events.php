<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalSafetyEvents extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_safety_events')) {
            Schema::create('clinical_safety_events', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('encounter_id', 36)->index('cl_se_enc_idx');
                $t->string('patient_id', 20)->index('cl_se_pat_idx');
                $t->string('category', 30)->index('cl_se_cat_idx');
                $t->string('subtype', 100)->nullable();
                $t->string('severity', 20)->index('cl_se_sev_idx');
                $t->dateTime('occurred_at');
                $t->dateTime('detected_at');
                $t->string('location', 191);
                $t->text('description');
                $t->text('immediate_actions');
                $t->string('isolation_type', 100)->nullable();
                $t->string('pathogen', 191)->nullable();
                $t->string('custody_reference', 191)->nullable();
                $t->string('status', 30)->default('OPEN')->index('cl_se_status_idx');
                $t->string('assigned_to', 191);
                $t->boolean('notification_required')->default(false);
                $t->dateTime('notified_at')->nullable();
                $t->string('notified_by', 191)->nullable();
                $t->string('notification_recipient', 191)->nullable();
                $t->dateTime('review_due_at')->nullable()->index('cl_se_due_idx');
                $t->text('root_cause')->nullable();
                $t->text('corrective_actions')->nullable();
                $t->dateTime('closed_at')->nullable();
                $t->string('closed_by', 191)->nullable();
                $t->string('created_by', 191);
                $t->string('ip_address', 45)->nullable();
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('clinical_safety_event_logs')) {
            Schema::create('clinical_safety_event_logs', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->unsignedBigInteger('clinical_safety_event_id')->index('cl_sel_event_idx');
                $t->string('event_type', 50)->index('cl_sel_type_idx');
                $t->string('from_status', 30)->nullable();
                $t->string('to_status', 30)->nullable();
                $t->text('detail')->nullable();
                $t->string('actor_id', 191)->nullable();
                $t->string('ip_address', 45)->nullable();
                $t->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_safety_event_logs');
        Schema::dropIfExists('clinical_safety_events');
    }
}
