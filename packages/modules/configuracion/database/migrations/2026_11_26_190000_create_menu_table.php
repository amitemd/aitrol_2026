<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('padre_id')->nullable()->constrained('menu')->restrictOnDelete();
            $table->string('nombre', 80);
            $table->string('ruta', 100)->nullable();
            $table->string('icono', 50);
            $table->unsignedSmallInteger('orden')->default(10);
            $table->boolean('activo')->default(true);
            $table->boolean('solo_administrador')->default(false);
            $table->boolean('protegido')->default(false);
            $table->timestamps();
        });

        $ahora = now();
        foreach ([
            ['Panel general', 'dashboard', 'bi-speedometer2', 10],
            ['Clientes', 'clientes.index', 'bi-people', 20],
            ['Documentación API', 'swagger.index', 'bi-braces', 30],
        ] as [$nombre, $ruta, $icono, $orden]) {
            DB::table('menu')->insert([
                'nombre' => $nombre, 'ruta' => $ruta, 'icono' => $icono,
                'orden' => $orden, 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }

        $configuracion = DB::table('menu')->insertGetId([
            'nombre' => 'Configuraciones generales', 'icono' => 'bi-gear', 'orden' => 40,
            'solo_administrador' => true, 'protegido' => true,
            'created_at' => $ahora, 'updated_at' => $ahora,
        ]);

        DB::table('menu')->insert([
            'padre_id' => $configuracion, 'nombre' => 'Administrar menú', 'ruta' => 'configuracion.menu.index',
            'icono' => 'bi-list-ul', 'solo_administrador' => true, 'protegido' => true,
            'created_at' => $ahora, 'updated_at' => $ahora,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
