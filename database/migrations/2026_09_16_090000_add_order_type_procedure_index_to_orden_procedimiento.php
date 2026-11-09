<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTypeProcedureIndexToOrdenProcedimiento extends Migration
{
    public function up()
    {
        Schema::table('orden_procedimiento', function (Blueprint $table) {
            $table->index(
                ['id_orden_tipo', 'id_procedimiento'],
                'orden_procedimiento_tipo_procedimiento_idx'
            );
        });
    }

    public function down()
    {
        Schema::table('orden_procedimiento', function (Blueprint $table) {
            $table->dropIndex('orden_procedimiento_tipo_procedimiento_idx');
        });
    }
}
