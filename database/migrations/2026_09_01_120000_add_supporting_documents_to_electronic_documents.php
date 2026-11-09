<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportingDocumentsToElectronicDocuments extends Migration
{
    public function up()
    {
        if (Schema::hasTable('de_documentos_electronicos') && ! Schema::hasColumn('de_documentos_electronicos', 'docsSustento')) {
            Schema::table('de_documentos_electronicos', function (Blueprint $table) {
                $table->longText('docsSustento')->nullable()->after('infoCompRetencion');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('de_documentos_electronicos') && Schema::hasColumn('de_documentos_electronicos', 'docsSustento')) {
            Schema::table('de_documentos_electronicos', function (Blueprint $table) {
                $table->dropColumn('docsSustento');
            });
        }
    }
}
