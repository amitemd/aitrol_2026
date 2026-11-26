<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClinicalSettingsToEmpresa extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('empresa')) {
            return;
        }

        if (! Schema::hasColumn('empresa', 'nivel_precio_medicamentos')) {
            Schema::table('empresa', function (Blueprint $table) {
                $table->unsignedInteger('nivel_precio_medicamentos')->default(3)->after('tipo_empresa');
            });
        }
    }

    public function down()
    {
        if (! Schema::hasTable('empresa')) {
            return;
        }

        if (Schema::hasColumn('empresa', 'nivel_precio_medicamentos')) {
            Schema::table('empresa', function (Blueprint $table) {
                $table->dropColumn('nivel_precio_medicamentos');
            });
        }
    }
}
