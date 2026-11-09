<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('v3_hr_employee_documents')) {
            return;
        }

        Schema::create('v3_hr_employee_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_empresa', 20);
            $table->unsignedInteger('employee_id');
            $table->string('tipo', 50);
            $table->string('titulo', 180);
            $table->string('persona_relacionada', 180)->nullable();
            $table->string('parentesco', 50)->nullable();
            $table->string('numero_documento', 80)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('file_path', 255);
            $table->string('file_name', 180);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('observacion')->nullable();
            $table->string('uploaded_by', 191);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['id_empresa', 'employee_id', 'tipo'], 'v3_hr_doc_employee_type_idx');
            $table->index(['id_empresa', 'fecha_vencimiento'], 'v3_hr_doc_expiration_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('v3_hr_employee_documents');
    }
};
