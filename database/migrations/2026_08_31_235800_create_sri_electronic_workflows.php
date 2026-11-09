<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSriElectronicWorkflows extends Migration
{
    public function up()
    {
        // MySQL commits CREATE TABLE implicitly. If a deployment was interrupted
        // after creating it but before Laravel recorded the migration, preserve
        // the existing workflows and let the migration be registered normally.
        if (Schema::hasTable('sri_electronic_workflows')) {
            return;
        }

        Schema::create('sri_electronic_workflows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('document_id')->unique();
            $table->string('company_id', 13)->index();
            $table->string('document_type', 2)->index();
            $table->string('schema_version', 10);
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('unsigned_path')->nullable();
            $table->string('signed_path')->nullable();
            $table->string('authorized_path')->nullable();
            $table->string('authorization_number', 60)->nullable();
            $table->dateTime('authorized_at')->nullable();
            $table->dateTime('emailed_at')->nullable();
            $table->dateTime('next_attempt_at')->nullable()->index();
            $table->text('last_error')->nullable();
            $table->longText('last_response')->nullable();
            $table->timestamps();
            $table->foreign('document_id')->references('id')->on('de_documentos_electronicos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sri_electronic_workflows');
    }
}
