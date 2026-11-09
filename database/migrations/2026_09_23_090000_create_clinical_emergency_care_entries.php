<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalEmergencyCareEntries extends Migration
{
    public function up()
    {
        Schema::create('clinical_emergency_care_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->string('section', 40);
            $table->text('content')->nullable();
            $table->string('recorded_by', 30);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['solicitud_id', 'section', 'id'], 'emergency_care_section_revision');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_emergency_care_entries');
    }
}
