<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalCompanyServicesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospital_company_services')) return;

        Schema::create('hospital_company_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_empresa', 13);
            $table->string('service_key', 60);
            $table->boolean('enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('id_usuariocrea', 13)->nullable();
            $table->string('id_usuariomod', 13)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->string('ip_modificacion', 45)->nullable();
            $table->timestamps();

            $table->unique(array('id_empresa', 'service_key'), 'hospital_company_service_unique');
            $table->index(array('id_empresa', 'enabled', 'sort_order'), 'hospital_company_service_visible');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_company_services');
    }
}
