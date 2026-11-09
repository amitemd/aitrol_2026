<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3HrOrganizationCatalogs extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_hr_departments')) {
            Schema::create('v3_hr_departments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->string('codigo', 30);
                $table->string('nombre', 120);
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['id_empresa', 'codigo'], 'v3_hr_department_company_code_uq');
                $table->unique(['id_empresa', 'nombre'], 'v3_hr_department_company_name_uq');
                $table->index(['id_empresa', 'estado'], 'v3_hr_department_company_status_idx');
            });
        }

        if (! Schema::hasTable('v3_hr_areas')) {
            Schema::create('v3_hr_areas', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('department_id');
                $table->string('codigo', 30);
                $table->string('nombre', 120);
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['id_empresa', 'codigo'], 'v3_hr_area_company_code_uq');
                $table->unique(['id_empresa', 'department_id', 'nombre'], 'v3_hr_area_department_name_uq');
                $table->index(['id_empresa', 'department_id', 'estado'], 'v3_hr_area_department_status_idx');
            });
        }

        if (Schema::hasTable('v3_hr_positions')) {
            Schema::table('v3_hr_positions', function (Blueprint $table) {
                if (! Schema::hasColumn('v3_hr_positions', 'department_id')) {
                    $table->unsignedInteger('department_id')->nullable();
                }
                if (! Schema::hasColumn('v3_hr_positions', 'area_id')) {
                    $table->unsignedInteger('area_id')->nullable();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('v3_hr_positions')) {
            Schema::table('v3_hr_positions', function (Blueprint $table) {
                if (Schema::hasColumn('v3_hr_positions', 'area_id')) {
                    $table->dropColumn('area_id');
                }
                if (Schema::hasColumn('v3_hr_positions', 'department_id')) {
                    $table->dropColumn('department_id');
                }
            });
        }
        Schema::dropIfExists('v3_hr_areas');
        Schema::dropIfExists('v3_hr_departments');
    }
}
