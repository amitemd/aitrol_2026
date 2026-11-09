<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeDocuments extends Migration
{
    public function up()
    {
        Schema::create('office_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_id', 32);
            $table->string('user_id', 32);
            $table->string('title', 180);
            $table->longText('content');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'user_id', 'deleted_at'], 'office_owner_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_documents');
    }
}
