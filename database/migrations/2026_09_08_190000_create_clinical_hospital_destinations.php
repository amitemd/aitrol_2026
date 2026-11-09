<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_hospital_destinations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('company_id', 32);
            $table->unsignedInteger('sala_id');
            $table->unsignedInteger('hospital_id');
            $table->unsignedTinyInteger('step');
            $table->string('updated_by', 32);
            $table->timestamps();
            $table->unique(['company_id', 'sala_id'], 'clinical_hospital_destination_unique');
        });
        Schema::create('clinical_hospital_bed_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedInteger('habitacion_id')->unique();
            $table->string('company_id', 32);
            $table->unsignedInteger('sala_id');
            $table->string('updated_by', 32);
            $table->timestamps();
            $table->index(['company_id', 'sala_id'], 'clinical_hospital_bed_destination');
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Las ubicaciones hospitalarias requieren una reversa revisada para no perder la trazabilidad de camas.');
    }
};
