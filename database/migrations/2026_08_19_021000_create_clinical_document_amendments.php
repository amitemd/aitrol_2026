<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalDocumentAmendments extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clinical_documents') && ! Schema::hasColumn('clinical_documents', 'content_snapshot')) {
            Schema::table('clinical_documents', function (Blueprint $t) {
                $t->longText('content_snapshot')->nullable()->after('content_hash');
            });
        }if (! Schema::hasTable('clinical_document_amendments')) {
            Schema::create('clinical_document_amendments', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->unsignedBigInteger('clinical_document_id')->index('cl_da_doc_idx');
                $t->unsignedInteger('version');
                $t->string('previous_hash', 64)->nullable();
                $t->string('new_hash', 64);
                $t->longText('previous_content')->nullable();
                $t->longText('amended_content');
                $t->text('reason');
                $t->string('authored_by', 191);
                $t->dateTime('authored_at');
                $t->string('ip_address', 45)->nullable();
                $t->timestamps();
                $t->unique(['clinical_document_id', 'version'], 'cl_da_doc_ver_uq');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_document_amendments');
        if (Schema::hasTable('clinical_documents') && Schema::hasColumn('clinical_documents', 'content_snapshot')) {
            Schema::table('clinical_documents', function (Blueprint $t) {
                $t->dropColumn('content_snapshot');
            });
        }
    }
}
