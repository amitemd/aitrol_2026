<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddClinicalResourceCatalogToRoomTypes extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tipo_habitacion')) {
            Schema::table('tipo_habitacion', function (Blueprint $table) {
                if (! Schema::hasColumn('tipo_habitacion', 'cantidad_camas')) {
                    $table->tinyInteger('cantidad_camas')->default(1);
                }
                if (! Schema::hasColumn('tipo_habitacion', 'estado')) {
                    $table->tinyInteger('estado')->default(1);
                }
                if (! Schema::hasColumn('tipo_habitacion', 'categoria')) {
                    $table->string('categoria', 30)->default('habitacion');
                }
                if (! Schema::hasColumn('tipo_habitacion', 'icono')) {
                    $table->string('icono', 80)->nullable();
                }
                if (! Schema::hasColumn('tipo_habitacion', 'imagen')) {
                    $table->string('imagen', 120)->nullable();
                }
            });

            DB::table('tipo_habitacion')->whereNull('categoria')->update(['categoria' => 'habitacion']);

            $defaults = [
                [
                    'nombre' => 'CONSULTORIO',
                    'descripcion' => 'Atención ambulatoria',
                    'cantidad_camas' => 0,
                    'categoria' => 'consultorio',
                    'icono' => 'fa-stethoscope',
                    'imagen' => 'office.png',
                ],
                [
                    'nombre' => 'QUIRÓFANO',
                    'descripcion' => 'Sala de cirugía',
                    'cantidad_camas' => 0,
                    'categoria' => 'quirofano',
                    'icono' => 'fa-procedures',
                    'imagen' => 'quirofano1.png',
                ],
            ];

            foreach ($defaults as $default) {
                $exists = DB::table('tipo_habitacion')->where('nombre', $default['nombre'])->exists();
                if (! $exists) {
                    $data = $default + [
                        'estado' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('tipo_habitacion')->insert($data);
                } else {
                    DB::table('tipo_habitacion')->where('nombre', $default['nombre'])->update([
                        'categoria' => $default['categoria'],
                        'icono' => $default['icono'],
                        'imagen' => $default['imagen'],
                    ]);
                }
            }
        }
    }

    public function down()
    {
        if (! Schema::hasTable('tipo_habitacion')) {
            return;
        }

        DB::table('tipo_habitacion')->whereIn('nombre', ['CONSULTORIO', 'QUIRÓFANO'])->delete();

        Schema::table('tipo_habitacion', function (Blueprint $table) {
            foreach (['imagen', 'icono', 'categoria'] as $column) {
                if (Schema::hasColumn('tipo_habitacion', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
