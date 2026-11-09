<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_encounter_closures', function (Blueprint $table) {
            $table->dateTime('physical_exit_at')->nullable();
            $table->string('physical_exit_by', 45)->nullable();
            $table->string('physical_exit_ip', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clinical_encounter_closures', function (Blueprint $table) {
            $table->dropColumn(['physical_exit_at', 'physical_exit_by', 'physical_exit_ip']);
        });
    }
};
