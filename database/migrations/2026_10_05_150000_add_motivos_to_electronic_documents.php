<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivosToElectronicDocuments extends Migration
{
    public function up()
    {
        if (Schema::hasTable('de_documentos_electronicos')
            && ! Schema::hasColumn('de_documentos_electronicos', 'motivos')) {
            Schema::table('de_documentos_electronicos', function (Blueprint $table) {
                $table->text('motivos')->nullable()->after('detalles');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('de_documentos_electronicos')
            && Schema::hasColumn('de_documentos_electronicos', 'motivos')) {
            Schema::table('de_documentos_electronicos', function (Blueprint $table) {
                $table->dropColumn('motivos');
            });
        }
    }
}
