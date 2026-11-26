<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmbarazoToOrdenTable extends Migration
{
    public function up()
    {
        Schema::table('orden', function (Blueprint $table) {
            $table->boolean('embarazo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orden', function (Blueprint $table) {
            $table->dropColumn('embarazo');
        });
    }
}
