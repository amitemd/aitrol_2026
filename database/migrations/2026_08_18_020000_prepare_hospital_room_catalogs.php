<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrepareHospitalRoomCatalogs extends Migration
{
    public function up()
    {
        if (Schema::hasTable('piso') && ! Schema::hasColumn('piso', 'estado')) {
            Schema::table('piso', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1);
            });
        }

        if (Schema::hasTable('tipo_habitacion')) {
            if (! Schema::hasColumn('tipo_habitacion', 'estado')) {
                Schema::table('tipo_habitacion', function (Blueprint $table) {
                    $table->tinyInteger('estado')->default(1);
                });
            }
            if (! Schema::hasColumn('tipo_habitacion', 'cantidad_camas')) {
                Schema::table('tipo_habitacion', function (Blueprint $table) {
                    $table->tinyInteger('cantidad_camas')->default(1);
                });
            }

            // Conserva el comportamiento histórico de los tipos existentes.
            DB::table('tipo_habitacion')->where('id', 2)->update(['cantidad_camas' => 2]);
            DB::table('tipo_habitacion')->where('id', 4)->update(['cantidad_camas' => 3]);
        }
    }

    public function down()
    {
        // Se conservan los catálogos para no afectar habitaciones existentes.
    }
}
