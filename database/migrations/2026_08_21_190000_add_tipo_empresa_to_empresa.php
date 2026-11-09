<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoEmpresaToEmpresa extends Migration
{
    public function up()
    {
        if (Schema::hasTable('empresa') && ! Schema::hasColumn('empresa', 'tipo_empresa')) {
            Schema::table('empresa', function (Blueprint $table) {
                $table->string('tipo_empresa', 40)->default('hospitalario')->after('nombrecomercial');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('empresa') && Schema::hasColumn('empresa', 'tipo_empresa')) {
            Schema::table('empresa', function (Blueprint $table) {
                $table->dropColumn('tipo_empresa');
            });
        }
    }
}
