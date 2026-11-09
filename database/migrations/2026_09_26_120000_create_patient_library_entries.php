<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_library_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_id', 45);
            $table->string('patient_id', 45);
            $table->string('title', 180);
            $table->text('notes')->nullable();
            $table->dateTime('occurred_at');
            $table->string('file_path')->nullable();
            $table->string('file_name', 180)->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('created_by', 191);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'patient_id', 'occurred_at'], 'patient_library_chronology');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_library_entries');
    }
};
