<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyFlowTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_emergency_locations')) {
            Schema::create('clinical_emergency_locations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('hospital_id')->nullable()->index();
                $table->string('code', 40);
                $table->string('name', 100);
                $table->string('type', 30)->index();
                $table->string('color', 10)->default('#64748b');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->unique(['hospital_id', 'code'], 'ce_locations_hospital_code_uq');
            });

            $now = date('Y-m-d H:i:s');
            DB::table('clinical_emergency_locations')->insert([
                ['code' => 'TRIAGE', 'name' => 'Triaje', 'type' => 'TRIAGE', 'color' => '#475569', 'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'WAITING', 'name' => 'Sala de espera clínica', 'type' => 'WAITING', 'color' => '#2563eb', 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'RESUSCITATION', 'name' => 'Reanimación', 'type' => 'RESUSCITATION', 'color' => '#dc2626', 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'CRITICAL_CARE', 'name' => 'Cubículo crítico', 'type' => 'CARE', 'color' => '#ea580c', 'sort_order' => 40, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'EMERGENCY_BOX', 'name' => 'Cubículo de emergencia', 'type' => 'CARE', 'color' => '#ca8a04', 'sort_order' => 50, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'FAST_TRACK', 'name' => 'Consulta rápida', 'type' => 'CARE', 'color' => '#16a34a', 'sort_order' => 60, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'OBSERVATION', 'name' => 'Observación de emergencia', 'type' => 'OBSERVATION', 'color' => '#7c3aed', 'sort_order' => 70, 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'ISOLATION', 'name' => 'Aislamiento', 'type' => 'ISOLATION', 'color' => '#0f766e', 'sort_order' => 80, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (! Schema::hasTable('clinical_emergency_flows')) {
            Schema::create('clinical_emergency_flows', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('solicitud_id')->unique();
                $table->unsignedInteger('hospital_id')->nullable()->index();
                $table->string('status', 35)->default('PENDING_TRIAGE')->index();
                $table->unsignedBigInteger('current_location_id')->nullable()->index();
                $table->dateTime('arrived_at');
                $table->dateTime('triaged_at')->nullable();
                $table->dateTime('attention_started_at')->nullable();
                $table->dateTime('last_reassessment_at')->nullable();
                $table->dateTime('decision_at')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->boolean('critical_arrival')->default(false);
                $table->string('arrival_mode', 30)->default('WALK_IN');
                $table->string('disposition', 35)->nullable()->index();
                $table->string('bed_request_status', 25)->nullable()->index();
                $table->text('decision_note')->nullable();
                $table->string('assigned_to', 30)->nullable();
                $table->string('created_by', 30)->nullable();
                $table->string('updated_by', 30)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_emergency_triage_assessments')) {
            Schema::create('clinical_emergency_triage_assessments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('solicitud_id')->index();
                $table->unsignedInteger('source_triage_id')->nullable()->index();
                $table->unsignedSmallInteger('sequence');
                $table->unsignedInteger('priority_id')->index();
                $table->unsignedTinyInteger('priority_level')->index();
                $table->string('priority_name', 100);
                $table->string('priority_color', 10);
                $table->string('discriminator', 191)->nullable();
                $table->text('clinical_reason');
                $table->decimal('systolic', 6, 2)->nullable();
                $table->decimal('diastolic', 6, 2)->nullable();
                $table->decimal('heart_rate', 6, 2)->nullable();
                $table->decimal('respiratory_rate', 6, 2)->nullable();
                $table->decimal('temperature', 5, 2)->nullable();
                $table->decimal('oxygen_saturation', 5, 2)->nullable();
                $table->unsignedTinyInteger('pain_score')->nullable();
                $table->boolean('high_risk')->default(false)->index();
                $table->dateTime('assessed_at');
                $table->string('assessed_by', 30);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->unique(['solicitud_id', 'sequence'], 'ce_triage_request_sequence_uq');
            });
        }

        if (! Schema::hasTable('clinical_emergency_location_movements')) {
            Schema::create('clinical_emergency_location_movements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('solicitud_id')->index();
                $table->unsignedBigInteger('from_location_id')->nullable();
                $table->unsignedBigInteger('to_location_id')->index();
                $table->string('reason', 191);
                $table->dateTime('moved_at');
                $table->string('moved_by', 30);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_emergency_location_movements');
        Schema::dropIfExists('clinical_emergency_triage_assessments');
        Schema::dropIfExists('clinical_emergency_flows');
        Schema::dropIfExists('clinical_emergency_locations');
    }
}
