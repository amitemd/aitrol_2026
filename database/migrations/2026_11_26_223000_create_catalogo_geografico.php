<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paises', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 2)->unique();
            $table->string('nombre', 120)->unique();
        });

        Schema::create('provincias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_pais')->constrained('paises')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->unique(['id_pais', 'nombre']);
        });

        Schema::create('ciudades', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_provincia')->constrained('provincias')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->unique(['id_provincia', 'nombre']);
        });

        Schema::table('personas', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_ciudad_anterior')->nullable();
        });
        DB::table('personas')->whereNotNull('id_ciudad')->update([
            'id_ciudad_anterior' => DB::raw('id_ciudad'),
            'id_ciudad' => null,
        ]);
        Schema::table('personas', function (Blueprint $table): void {
            $table->foreign('id_ciudad')->references('id')->on('ciudades')->restrictOnDelete();
        });

        $pais = DB::table('paises')->insertGetId(['codigo' => 'EC', 'nombre' => 'Ecuador']);
        foreach ([
            'Azuay' => 'Cuenca', 'Bolívar' => 'Guaranda', 'Cañar' => 'Azogues', 'Carchi' => 'Tulcán',
            'Chimborazo' => 'Riobamba', 'Cotopaxi' => 'Latacunga', 'El Oro' => 'Machala',
            'Esmeraldas' => 'Esmeraldas', 'Galápagos' => 'Puerto Baquerizo Moreno', 'Guayas' => 'Guayaquil',
            'Imbabura' => 'Ibarra', 'Loja' => 'Loja', 'Los Ríos' => 'Babahoyo', 'Manabí' => 'Portoviejo',
            'Morona Santiago' => 'Macas', 'Napo' => 'Tena', 'Orellana' => 'Puerto Francisco de Orellana',
            'Pastaza' => 'Puyo', 'Pichincha' => 'Quito', 'Santa Elena' => 'Santa Elena',
            'Santo Domingo de los Tsáchilas' => 'Santo Domingo', 'Sucumbíos' => 'Nueva Loja',
            'Tungurahua' => 'Ambato', 'Zamora Chinchipe' => 'Zamora',
        ] as $provincia => $ciudad) {
            $id = DB::table('provincias')->insertGetId(['id_pais' => $pais, 'nombre' => $provincia]);
            DB::table('ciudades')->insert(['id_provincia' => $id, 'nombre' => $ciudad]);
        }
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table): void {
            $table->dropForeign(['id_ciudad']);
        });
        DB::table('personas')->update(['id_ciudad' => DB::raw('id_ciudad_anterior')]);
        Schema::table('personas', function (Blueprint $table): void {
            $table->dropColumn('id_ciudad_anterior');
        });
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('paises');
    }
};