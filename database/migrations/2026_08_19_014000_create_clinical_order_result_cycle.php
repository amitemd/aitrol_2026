<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalOrderResultCycle extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_orders')) {
            Schema::create('clinical_orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index('cl_ord_enc_idx');
                $table->string('patient_id', 20)->index('cl_ord_pat_idx');
                $table->string('category', 30)->index('cl_ord_cat_idx');
                $table->string('description', 255);
                $table->string('priority', 20)->default('ROUTINE')->index('cl_ord_prio_idx');
                $table->string('status', 30)->default('ORDERED')->index('cl_ord_status_idx');
                $table->string('source_type', 80);
                $table->string('source_id', 80);
                $table->string('ordered_by', 191)->nullable();
                $table->dateTime('ordered_at')->nullable();
                $table->string('responsible_id', 191)->nullable();
                $table->dateTime('due_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->text('cancel_reason')->nullable();
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'cl_ord_source_uq');
            });
        }

        if (! Schema::hasTable('clinical_order_events')) {
            Schema::create('clinical_order_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_order_id')->index('cl_ord_evt_order_idx');
                $table->string('event_type', 50)->index('cl_ord_evt_type_idx');
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();
                $table->text('reason')->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_results')) {
            Schema::create('clinical_results', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_order_id')->index('cl_res_order_idx');
                $table->string('source_type', 80);
                $table->string('source_id', 80);
                $table->string('status', 30)->default('PRELIMINARY')->index('cl_res_status_idx');
                $table->text('summary')->nullable();
                $table->longText('payload_json')->nullable();
                $table->boolean('is_critical')->default(false)->index('cl_res_critical_idx');
                $table->text('critical_reason')->nullable();
                $table->string('verified_by', 191)->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->dateTime('occurred_at')->nullable();
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'cl_res_source_uq');
            });
        }

        if (! Schema::hasTable('clinical_critical_result_notifications')) {
            Schema::create('clinical_critical_result_notifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_result_id')->index('cl_crit_res_idx');
                $table->string('status', 30)->default('PENDING')->index('cl_crit_status_idx');
                $table->string('recipient_id', 191)->nullable();
                $table->string('recipient_name', 191)->nullable();
                $table->string('recipient_role', 100)->nullable();
                $table->dateTime('due_at')->index('cl_crit_due_idx');
                $table->dateTime('notified_at')->nullable();
                $table->string('notified_by', 191)->nullable();
                $table->dateTime('acknowledged_at')->nullable();
                $table->string('acknowledged_by', 191)->nullable();
                $table->dateTime('escalated_at')->nullable();
                $table->string('escalated_to', 191)->nullable();
                $table->unsignedInteger('attempts')->default(0);
                $table->text('notes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_critical_result_notifications');
        Schema::dropIfExists('clinical_results');
        Schema::dropIfExists('clinical_order_events');
        Schema::dropIfExists('clinical_orders');
    }
}
