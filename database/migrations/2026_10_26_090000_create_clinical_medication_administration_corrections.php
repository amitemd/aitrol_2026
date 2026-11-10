<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalMedicationAdministrationCorrections extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_medication_administration_corrections')) {
            Schema::create('clinical_medication_administration_corrections', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('administration_id');
                $table->dateTime('previous_administered_at');
                $table->dateTime('new_administered_at');
                $table->string('reason', 500);
                $table->string('corrected_by', 191);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->index('administration_id', 'cmac_administration_idx');
            });

            return;
        }

        if (! Schema::hasIndex('clinical_medication_administration_corrections', 'cmac_administration_idx')) {
            Schema::table('clinical_medication_administration_corrections', function (Blueprint $table) {
                $table->index('administration_id', 'cmac_administration_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_medication_administration_corrections');
    }
}
