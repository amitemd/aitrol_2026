<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableUsersV3 extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cedula')) {
                // NULL evita inventar identificaciones o duplicar un valor vacío.
                $table->string('cedula', 13)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'telefono3')) {
                $table->string('telefono3', 15)->nullable();
            }
            if (!Schema::hasColumn('users', 'telefono2')) {
                $table->string('telefono2', 15)->nullable();
            }
            if (!Schema::hasColumn('users', 'fechanacimiento')) {
                $table->date('fechanacimiento')->nullable();
            }
            if (!Schema::hasColumn('users', 'estado')) {
                $table->integer('estado')->nullable();
            }
        });
    }

    public function down()
    {
        // No eliminar campos que pueden contener datos anteriores a esta migración.
    }
}
