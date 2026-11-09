<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserElectronicCertificatesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_electronic_certificates')) return;

        Schema::create('user_electronic_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_empresa', 20);
            $table->string('user_id', 32);
            $table->string('certificate_path', 255);
            $table->string('original_name', 255);
            $table->text('password_encrypted');
            $table->string('subject_name', 255)->nullable();
            $table->string('issuer_name', 255)->nullable();
            $table->string('serial_number', 191)->nullable();
            $table->string('fingerprint_sha256', 64);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('uploaded_by', 32);
            $table->string('upload_ip', 64)->nullable();
            $table->timestamps();

            $table->unique(array('id_empresa', 'user_id'), 'user_electronic_certificate_company_user_uq');
            $table->index(array('id_empresa', 'estado', 'valid_to'), 'user_electronic_certificate_status_idx');
            $table->index('fingerprint_sha256', 'user_electronic_certificate_fingerprint_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_electronic_certificates');
    }
}
