<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaPais extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pais')) {
            Schema::create('pais', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nombre', 20);
                $table->timestamps();
            });

            return;
        }

        Schema::table('pais', function (Blueprint $table) {
            if (!Schema::hasColumn('pais', 'id')) {
                $table->increments('id');
            }
            if (!Schema::hasColumn('pais', 'nombre')) {
                // No se inventan nombres para los registros existentes.
                $table->string('nombre', 20)->nullable();
            }
            if (!Schema::hasColumn('pais', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('pais', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down()
    {
        // La tabla y sus campos pueden existir desde antes de esta migración.
    }
}
