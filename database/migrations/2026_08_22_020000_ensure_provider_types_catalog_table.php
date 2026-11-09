<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureProviderTypesCatalogTable extends Migration
{
    /**
     * Garantiza el catalogo en bases parciales o anteriores. En una
     * instalacion completa la tabla ya viene en el esquema heredado.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tipoproveedor')) {
            return;
        }

        Schema::create('tipoproveedor', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nombre', 30);
            $table->string('descripcion', 255);
            $table->tinyInteger('visualizar')->default(1);
            $table->tinyInteger('estado')->default(1);
            $table->string('id_usuariocrea', 13)->nullable();
            $table->string('id_usuariomod', 13)->nullable();
            $table->string('ip_creacion', 40)->nullable();
            $table->string('ip_modificacion', 40)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_usuariocrea', 'FK_tipoproveedor_id_usuariocrea');
            $table->index('id_usuariomod', 'FK_tipoproveedor_id_usuariomod');
        });

        if (Schema::hasTable('users')) {
            Schema::table('tipoproveedor', function (Blueprint $table) {
                $table->foreign('id_usuariocrea', 'FK_tipoproveedor_id_usuariocrea_fk')
                    ->references('id')->on('users')->onUpdate('cascade');
                $table->foreign('id_usuariomod', 'FK_tipoproveedor_id_usuariomod_fk')
                    ->references('id')->on('users')->onUpdate('cascade');
            });
        }
    }

    /**
     * El rollback no elimina catalogos operativos ni referencias historicas.
     *
     * @return void
     */
    public function down()
    {
        // Intencionalmente conservador: no se elimina una tabla de negocio.
    }
}
