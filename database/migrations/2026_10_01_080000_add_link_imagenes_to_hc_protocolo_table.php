<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkImagenesToHcProtocoloTable extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hc_protocolo') || Schema::hasColumn('hc_protocolo', 'link_imagenes')) {
            return;
        }

        Schema::table('hc_protocolo', function (Blueprint $table) {
            $table->text('link_imagenes')->nullable()->after('conclusion');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('hc_protocolo') || ! Schema::hasColumn('hc_protocolo', 'link_imagenes')) {
            return;
        }

        Schema::table('hc_protocolo', function (Blueprint $table) {
            $table->dropColumn('link_imagenes');
        });
    }
}
