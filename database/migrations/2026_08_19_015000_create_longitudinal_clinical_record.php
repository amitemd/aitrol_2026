<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLongitudinalClinicalRecord extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_records')) {
            Schema::create('clinical_records', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('patient_id', 20)->unique('cl_rec_patient_uq');
                $table->string('status', 20)->default('ACTIVE')->index('cl_rec_status_idx');
                $table->dateTime('opened_at');
                $table->string('created_by', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_documents')) {
            Schema::create('clinical_documents', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('clinical_record_id', 36)->index('cl_doc_record_idx');
                $table->string('encounter_id', 36)->index('cl_doc_enc_idx');
                $table->string('document_type', 50)->index('cl_doc_type_idx');
                $table->string('source_type', 80);
                $table->string('source_id', 80);
                $table->string('title', 255)->nullable();
                $table->string('status', 30)->default('CURRENT')->index('cl_doc_status_idx');
                $table->unsignedInteger('version')->default(1);
                $table->unsignedBigInteger('parent_document_id')->nullable()->index('cl_doc_parent_idx');
                $table->string('content_hash', 64)->nullable();
                $table->string('authored_by', 191)->nullable();
                $table->dateTime('authored_at')->nullable();
                $table->dateTime('signed_at')->nullable();
                $table->string('signed_by', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'cl_doc_source_uq');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_documents');
        Schema::dropIfExists('clinical_records');
    }
}
