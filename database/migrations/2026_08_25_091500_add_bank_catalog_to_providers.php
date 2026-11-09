<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankCatalogToProviders extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('proveedor') || ! Schema::hasTable('ct_bancos') || Schema::hasColumn('proveedor', 'id_banco')) {
            return;
        }

        Schema::table('proveedor', function (Blueprint $table) {
            $table->integer('id_banco')->nullable()->after('id_configuracion');
            $table->index('id_banco', 'IDX_proveedor_id_banco');
            $table->foreign('id_banco', 'FK_proveedor_id_banco')
                ->references('id')
                ->on('ct_bancos')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        if (! Schema::hasTable('proveedor') || ! Schema::hasColumn('proveedor', 'id_banco')) {
            return;
        }

        Schema::table('proveedor', function (Blueprint $table) {
            $table->dropForeign('FK_proveedor_id_banco');
            $table->dropIndex('IDX_proveedor_id_banco');
            $table->dropColumn('id_banco');
        });
    }
}
