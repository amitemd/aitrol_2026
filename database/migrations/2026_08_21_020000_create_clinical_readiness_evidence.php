<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalReadinessEvidence extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clinical_readiness_evidence')) {
            return;
        }

        Schema::create('clinical_readiness_evidence', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('evidence_type', 30)->index('cre_type_idx');
            $table->string('status', 20)->index('cre_status_idx');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable()->index('cre_done_idx');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('metrics')->nullable();
            $table->string('evidence_path', 500)->nullable();
            $table->string('evidence_hash', 64)->nullable();
            $table->string('recorded_by', 100)->nullable();
            $table->string('approved_by', 100)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_readiness_evidence');
    }
}
