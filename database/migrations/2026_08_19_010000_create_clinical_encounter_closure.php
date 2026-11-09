<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateClinicalEncounterClosure extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_encounter_closures')) {
            Schema::create('clinical_encounter_closures', function (Blueprint $table) {
                $table->increments('id');
                $table->string('encounter_id', 36)->unique();
                $table->unsignedInteger('solicitud_id')->nullable()->index();
                $table->unsignedInteger('epicrisis_id')->nullable()->index();
                $table->string('closure_type', 24)->index();
                $table->text('medication_reconciliation');
                $table->string('pending_orders_decision', 24);
                $table->text('pending_orders_reason')->nullable();
                $table->string('follow_up_responsible_id', 45)->nullable();
                $table->text('checklist_snapshot');
                $table->string('status', 20)->default('PENDING')->index();
                $table->string('signed_by', 45);
                $table->dateTime('signed_at');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_encounter_closure_orders')) {
            Schema::create('clinical_encounter_closure_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('closure_id')->index();
                $table->string('order_type', 40);
                $table->string('order_id', 80);
                $table->string('previous_status', 40)->nullable();
                $table->string('disposition', 24);
                $table->text('reason')->nullable();
                $table->string('responsible_id', 45)->nullable();
                $table->timestamps();
                $table->unique(['closure_id', 'order_type', 'order_id'], 'clinical_closure_order_unique');
            });
        }

        if (Schema::hasTable('hc_epicrisis_final') && ! Schema::hasColumn('hc_epicrisis_final', 'encounter_id')) {
            Schema::table('hc_epicrisis_final', function (Blueprint $table) {
                $table->string('encounter_id', 36)->nullable()->index();
            });
        }
        if (Schema::hasTable('hc_epicrisis_final') && Schema::hasColumn('hc_epicrisis_final', 'encounter_id')
            && Schema::hasColumn('ho_solicitud', 'encounter_id')) {
            DB::table('hc_epicrisis_final')->whereNull('encounter_id')->orderBy('id')->chunk(250, function ($records) {
                foreach ($records as $record) {
                    $encounterId = DB::table('ho_solicitud')->where('id', $record->id_ho_solicitud)->value('encounter_id');
                    if ($encounterId) {
                        DB::table('hc_epicrisis_final')->where('id', $record->id)
                            ->update(['encounter_id' => $encounterId]);
                    }
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hc_epicrisis_final') && Schema::hasColumn('hc_epicrisis_final', 'encounter_id')) {
            Schema::table('hc_epicrisis_final', function (Blueprint $table) {
                $table->dropColumn('encounter_id');
            });
        }
        Schema::dropIfExists('clinical_encounter_closure_orders');
        Schema::dropIfExists('clinical_encounter_closures');
    }
}
