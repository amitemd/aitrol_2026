<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSealToFirmaUsuario extends Migration
{
    public function up()
    {
        if (Schema::hasTable('firma_usuario') && ! Schema::hasColumn('firma_usuario', 'sello')) {
            Schema::table('firma_usuario', function (Blueprint $table) {
                $table->string('sello', 255)->nullable()->after('firma');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('firma_usuario') && Schema::hasColumn('firma_usuario', 'sello')) {
            Schema::table('firma_usuario', function (Blueprint $table) {
                $table->dropColumn('sello');
            });
        }
    }
}
