<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBootstrapUserTables extends Migration
{
    /**
     * Create only the authentication tables that are absent.
     *
     * The column definitions follow aitrol_db.sql. Foreign keys to users are
     * deliberately omitted here because tipousuario and users reference each
     * other through their audit columns in the original database.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tipousuario')) {
            Schema::create('tipousuario', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->string('nombre', 30)->unique();
                $table->string('descripcion', 255);
                $table->tinyInteger('estado')->default(1);
                $table->string('id_usuariocrea', 13)->nullable();
                $table->string('id_usuariomod', 13)->nullable();
                $table->string('ip_creacion', 40)->nullable();
                $table->string('ip_modificacion', 40)->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->string('id', 13)->primary();
                $table->string('nombre1', 60);
                $table->string('nombre2', 60)->nullable();
                $table->string('apellido1', 60);
                $table->string('apellido2', 60);
                $table->tinyInteger('tipo_documento')->default(1);
                $table->tinyInteger('id_pais')->nullable();
                $table->string('ciudad', 60)->nullable();
                $table->string('direccion', 255)->nullable();
                $table->string('email', 191)->nullable()->unique();
                $table->string('telefono1', 60)->nullable();
                $table->string('telefono2', 60)->nullable();
                $table->string('ocupacion', 60)->nullable();
                $table->tinyInteger('estado')->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->string('password', 191);
                $table->string('imagen_url', 100)->default(' ');
                $table->tinyInteger('id_tipo_usuario');
                $table->tinyInteger('max_procedimiento')->default(5);
                $table->tinyInteger('max_consulta')->default(5);
                $table->string('color', 40)->nullable();
                $table->string('registro_doctor', 60)->nullable();
                $table->tinyInteger('uso_sistema')->default(0);
                $table->tinyInteger('training')->default(0);
                $table->rememberToken();
                $table->string('id_usuariocrea', 13);
                $table->string('id_usuariomod', 13);
                $table->string('ip_creacion', 40);
                $table->string('ip_modificacion', 40);
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
                $table->tinyInteger('uso_laboratorio')->default(1);
                $table->string('departamento', 100)->nullable();
                $table->string('cargo', 100)->nullable();
                $table->string('servicios', 255)->nullable();
                $table->string('libro', 255)->nullable();
                $table->string('folio', 255)->nullable();
                $table->string('numero', 255)->nullable();
                $table->tinyInteger('app')->default(0);
                $table->tinyInteger('uso_pentax')->default(0);
                $table->integer('id_ciudad_usuario')->nullable();
                $table->string('urbanizacion', 255)->nullable();
                $table->string('etapa', 255)->nullable();
                $table->string('manzana', 255)->nullable();
                $table->string('villa', 255)->nullable();
                $table->text('referencia_direccion')->nullable();
                $table->string('area', 255)->nullable();
                $table->tinyInteger('new_system')->default(0);
            });
        }
    }

    /**
     * This bootstrap migration never removes authentication data on rollback.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left empty to protect pre-existing user tables.
    }
}
