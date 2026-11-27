<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejecuciones_mantenimiento', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $tabla->string('accion', 30)->index();
            $tabla->string('estado', 15);
            $tabla->unsignedTinyInteger('bloqueo')->nullable()->unique();
            $tabla->string('detalle', 250);
            $tabla->timestamp('iniciado_at')->nullable();
            $tabla->timestamp('finalizado_at')->nullable();
            $tabla->timestamps();
        });
        $padre = DB::table('menu')->where('ruta', 'configuracion.menu.index')->value('padre_id');
        DB::table('menu')->insert([
            'padre_id' => $padre, 'nombre' => 'Mantenimiento del servidor', 'icono' => 'bi-tools',
            'ruta' => 'configuracion.mantenimiento.index', 'orden' => 95, 'solo_administrador' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('menu')->where('ruta', 'configuracion.mantenimiento.index')->delete();
        Schema::dropIfExists('ejecuciones_mantenimiento');
    }
};
