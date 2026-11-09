<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmergencyFlowTransitionContext extends Migration
{
    public function up()
    {
        Schema::table('clinical_emergency_flows', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(0);
            $table->unsignedInteger('requested_transfer_id')->nullable()->index();
            $table->text('critical_reason')->nullable();
            $table->string('critical_started_by', 30)->nullable();
            $table->dateTime('critical_started_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('clinical_emergency_flows', function (Blueprint $table) {
            $table->dropColumn(['version', 'requested_transfer_id', 'critical_reason', 'critical_started_by', 'critical_started_at']);
        });
    }
}
