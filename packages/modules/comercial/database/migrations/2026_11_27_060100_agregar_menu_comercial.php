<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $orden = 28;
            foreach (config('comercial') as $modulo => $datos) {
                $padre = DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', $datos['titulo'])->value('id')
                    ?? DB::table('menu')->insertGetId([
                        'nombre' => $datos['titulo'], 'icono' => $datos['icono'], 'orden' => $orden,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                $posicion = 10;
                $opciones = [$modulo];
                foreach ($datos['secciones'] as $seccion => [$titulo, $icono]) {
                    $ruta = $modulo.'.'.$seccion.'.index';
                    if (! DB::table('menu')->where('ruta', $ruta)->exists()) {
                        DB::table('menu')->insert([
                            'padre_id' => $padre, 'nombre' => $titulo, 'ruta' => $ruta, 'icono' => $icono,
                            'orden' => $posicion, 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                    $opciones[] = $ruta;
                    $posicion += 10;
                }
                DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas) use ($opciones): void {
                    foreach ($empresas as $empresa) {
                        $permisos = [];
                        foreach ($opciones as $opcion) {
                            foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                                $permisos[] = ['id_empresa' => $empresa->id, 'opcion' => $opcion, 'accion' => $accion];
                            }
                        }
                        DB::table('permisos')->insertOrIgnore($permisos);
                    }
                });
                $orden++;
            }
        });
    }

    public function down(): void
    {
        // Las configuraciones y los permisos del menú se conservan.
    }
};
