<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalMedicationAdministration extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_medication_orders')) {
            Schema::create('clinical_medication_orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index();
                $table->string('patient_id', 20)->index();
                $table->string('source_type', 80);
                $table->string('source_id', 80);
                $table->string('medication_name', 255);
                $table->string('ordered_dose', 120)->nullable();
                $table->string('ordered_route', 80)->nullable();
                $table->string('status', 30)->default('ACTIVE')->index();
                $table->string('ordered_by', 191)->nullable();
                $table->timestamp('ordered_at')->nullable();
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'clinical_med_order_source_unique');
            });
        }

        if (! Schema::hasTable('clinical_medication_administrations')) {
            Schema::create('clinical_medication_administrations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('encounter_id', 36)->index();
                $table->unsignedBigInteger('medication_order_id')->index();
                $table->string('patient_id', 20)->index();
                $table->string('medication_name', 255);
                $table->string('dose', 120);
                $table->string('route', 80);
                $table->dateTime('scheduled_at');
                $table->dateTime('administered_at');
                $table->string('status', 30)->index();
                $table->text('omission_reason')->nullable();
                $table->text('clinical_result')->nullable();
                $table->string('inventory_series', 191)->nullable();
                $table->boolean('patient_verified')->default(false);
                $table->boolean('medication_verified')->default(false);
                $table->boolean('dose_verified')->default(false);
                $table->boolean('route_verified')->default(false);
                $table->boolean('time_verified')->default(false);
                $table->string('administered_by', 191);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->unique(['medication_order_id', 'scheduled_at'], 'clinical_med_admin_schedule_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_medication_administrations');
        Schema::dropIfExists('clinical_medication_orders');
    }
}
