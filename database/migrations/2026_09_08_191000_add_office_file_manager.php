<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeFileManager extends Migration
{
    public function up()
    {
        Schema::create('office_folders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_id', 32);
            $table->string('user_id', 32);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 180);
            $table->timestamps();
            $table->index(['company_id', 'user_id', 'parent_id'], 'office_folder_owner');
        });
        Schema::table('office_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable()->index();
            $table->string('kind', 16)->default('document');
            $table->string('original_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
        });
    }

    public function down()
    {
        Schema::table('office_documents', function (Blueprint $table) {
            $table->dropColumn(['folder_id', 'kind', 'original_path', 'original_name', 'mime_type', 'file_size']);
        });
        Schema::dropIfExists('office_folders');
    }
}
