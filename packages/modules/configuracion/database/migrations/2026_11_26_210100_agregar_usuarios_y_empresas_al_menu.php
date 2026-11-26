<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $padre = DB::table('menu')->where('ruta', 'configuracion.menu.index')
                ->where('protegido', true)->value('padre_id');

            foreach ([
                ['Usuarios', 'configuracion.usuarios.index', 'bi-person-gear', 20],
                ['Empresas', 'configuracion.empresas.index', 'bi-buildings', 30],
            ] as [$nombre, $ruta, $icono, $orden]) {
                if (! DB::table('menu')->where('ruta', $ruta)->exists()) {
                    DB::table('menu')->insert([
                        'padre_id' => $padre, 'nombre' => $nombre, 'ruta' => $ruta,
                        'icono' => $icono, 'orden' => $orden, 'solo_administrador' => true,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('menu')->whereIn('ruta', ['configuracion.usuarios.index', 'configuracion.empresas.index'])->delete();
    }
};