<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalDocumentCompleteness extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_document_completeness')) {
            Schema::create('clinical_document_completeness', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('clinical_document_id')->unique('cl_cmp_doc_uq');
                $table->string('encounter_id', 36)->index('cl_cmp_enc_idx');
                $table->string('document_type', 50)->index('cl_cmp_type_idx');
                $table->string('status', 20)->index('cl_cmp_status_idx');
                $table->text('required_fields')->nullable();
                $table->text('missing_fields')->nullable();
                $table->unsignedSmallInteger('completion_percent')->default(0);
                $table->dateTime('assessed_at');
                $table->string('assessed_by', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_document_completeness');
    }
}
