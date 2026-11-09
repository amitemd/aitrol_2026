<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalBedTransfers extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clinical_bed_transfers')) {
            return;
        }

        Schema::create('clinical_bed_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('hospitalization_id')->index('cbt_hosp_idx');
            $table->string('patient_id', 45)->index('cbt_patient_idx');
            $table->unsignedInteger('from_room_id')->nullable();
            $table->unsignedInteger('from_bed_id')->nullable();
            $table->unsignedInteger('to_room_id');
            $table->unsignedInteger('to_bed_id');
            $table->string('reason', 255);
            $table->string('actor_id', 45)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('transferred_at')->index('cbt_date_idx');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_bed_transfers');
    }
}
