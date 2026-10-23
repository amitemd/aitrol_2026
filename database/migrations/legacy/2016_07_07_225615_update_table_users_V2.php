<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableUsersV2 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'tipoUsuario')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('tipoUsuario');
            });
        }
    }

    public function down()
    {
        // Se conserva el campo y sus datos, como en la migración original.
    }
}
