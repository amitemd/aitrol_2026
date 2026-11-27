<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $padre = DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', 'Inventarios')->value('id')
                ?? DB::table('menu')->insertGetId([
                    'nombre' => 'Inventarios', 'icono' => 'bi-boxes', 'orden' => 27, 'created_at' => now(), 'updated_at' => now(),
                ]);
            $orden = 10;
            foreach (config('inventarios.secciones') as $seccion => [$nombre, $icono]) {
                $ruta = "inventarios.{$seccion}.index";
                if (! DB::table('menu')->where('ruta', $ruta)->exists()) {
                    DB::table('menu')->insert([
                        'padre_id' => $padre, 'nombre' => $nombre, 'ruta' => $ruta, 'icono' => $icono,
                        'orden' => $orden, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
                $orden += 10;
            }

            $opciones = array_filter(array_keys(config('accesos')), fn (string $opcion): bool => $opcion === 'inventarios' || str_starts_with($opcion, 'inventarios.'));
            $separada = \Illuminate\Support\Facades\Schema::hasColumn('permisos', 'id_empresa');
            $empresas = $separada ? DB::table('erp_empresas')->orderBy('id')->pluck('id') : collect([null]);
            foreach ($empresas as $empresa) {
                $filas = [];
                foreach ($opciones as $opcion) {
                    foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                        $filas[] = ['opcion' => $opcion, 'accion' => $accion] + ($separada ? ['id_empresa' => $empresa] : []);
                    }
                }
                DB::table('permisos')->insertOrIgnore($filas);
            }
        });
    }

    public function down(): void
    {
        // Se conservan las personalizaciones del menú y las asignaciones de permisos.
    }
};
