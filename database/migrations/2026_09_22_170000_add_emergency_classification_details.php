<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmergencyClassificationDetails extends Migration
{
    public function up()
    {
        Schema::table('clinical_emergency_triage_assessments', function (Blueprint $table) {
            $table->json('assessment_details')->nullable();
        });
    }

    public function down()
    {
        Schema::table('clinical_emergency_triage_assessments', function (Blueprint $table) {
            $table->dropColumn('assessment_details');
        });
    }
}
