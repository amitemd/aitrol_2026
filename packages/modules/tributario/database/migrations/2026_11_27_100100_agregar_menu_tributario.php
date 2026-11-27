<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $padre = DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', 'Tributario')->value('id')
                ?? DB::table('menu')->insertGetId(['nombre' => 'Tributario', 'icono' => 'bi-file-earmark-check', 'orden' => 33, 'created_at' => now(), 'updated_at' => now()]);
            $opciones = ['tributario'];
            $orden = 10;
            foreach (config('tributario.secciones') as $seccion => [$nombre, $icono]) {
                $ruta = 'tributario.'.$seccion.'.index';
                if (! DB::table('menu')->where('ruta', $ruta)->exists()) {
                    DB::table('menu')->insert([
                        'padre_id' => $padre, 'nombre' => $nombre, 'icono' => $icono, 'ruta' => $ruta,
                        'orden' => $orden, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
                $opciones[] = $ruta;
                $orden += 10;
            }
            DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas) use ($opciones): void {
                foreach ($empresas as $empresa) {
                    $filas = [];
                    foreach ($opciones as $opcion) {
                        foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                            $filas[] = ['id_empresa' => $empresa->id, 'opcion' => $opcion, 'accion' => $accion];
                        }
                    }
                    DB::table('permisos')->insertOrIgnore($filas);
                }
            });
        });
    }

    public function down(): void
    {
        // Se conservan el menú y las autorizaciones personalizadas.
    }
};
