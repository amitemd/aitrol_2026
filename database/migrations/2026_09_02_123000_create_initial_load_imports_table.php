<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInitialLoadImportsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('initial_load_imports')) {
            return;
        }
        Schema::create('initial_load_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('original_name', 255);
            $table->string('stored_path', 500);
            $table->string('file_hash', 64)->index();
            $table->string('status', 30)->default('validating')->index();
            $table->unsignedInteger('ready_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->unsignedInteger('manual_rows')->default(0);
            $table->longText('summary_json')->nullable();
            $table->longText('results_json')->nullable();
            $table->longText('errors_json')->nullable();
            $table->longText('warnings_json')->nullable();
            $table->string('created_by', 13)->index();
            $table->string('created_ip', 45)->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('initial_load_imports');
    }
}
