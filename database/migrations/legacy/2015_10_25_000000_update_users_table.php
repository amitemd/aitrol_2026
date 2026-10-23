<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nombres')) {
                $table->string('nombres', 60);
            }
            if (!Schema::hasColumn('users', 'apellidos')) {
                $table->string('apellidos', 60);
            }
            if (!Schema::hasColumn('users', 'pais')) {
                $table->integer('pais');
            }
            if (!Schema::hasColumn('users', 'ciudad')) {
                $table->string('ciudad', 60);
            }
            if (!Schema::hasColumn('users', 'institucion')) {
                $table->string('institucion', 100);
            }
            if (!Schema::hasColumn('users', 'ocupacion')) {
                $table->string('ocupacion', 60);
            }
        });
    }

    public function down()
    {
        // Los campos pueden ser anteriores a esta migración; se conservan sus datos.
    }
}
