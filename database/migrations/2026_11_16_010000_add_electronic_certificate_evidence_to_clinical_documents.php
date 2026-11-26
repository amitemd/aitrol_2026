<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElectronicCertificateEvidenceToClinicalDocuments extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_documents')) {
            return;
        }

        Schema::table('clinical_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('clinical_documents', 'electronic_certificate_id')) {
                $table->unsignedBigInteger('electronic_certificate_id')->nullable()->after('signed_by');
            }
            if (! Schema::hasColumn('clinical_documents', 'electronic_certificate_fingerprint')) {
                $table->string('electronic_certificate_fingerprint', 64)->nullable()->after('electronic_certificate_id');
            }
            if (! Schema::hasColumn('clinical_documents', 'electronic_signature_algorithm')) {
                $table->string('electronic_signature_algorithm', 32)->nullable()->after('electronic_certificate_fingerprint');
            }
            if (! Schema::hasColumn('clinical_documents', 'electronic_signature')) {
                $table->text('electronic_signature')->nullable()->after('electronic_signature_algorithm');
            }
            if (! Schema::hasColumn('clinical_documents', 'electronic_certificate_pem')) {
                $table->text('electronic_certificate_pem')->nullable()->after('electronic_signature');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('clinical_documents')) {
            return;
        }
        Schema::table('clinical_documents', function (Blueprint $table) {
            foreach ([
                'electronic_certificate_pem', 'electronic_signature', 'electronic_signature_algorithm',
                'electronic_certificate_fingerprint', 'electronic_certificate_id',
            ] as $column) {
                if (Schema::hasColumn('clinical_documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
