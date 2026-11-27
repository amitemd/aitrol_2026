<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $raiz = $this->grupo(null, 'Contabilidad', 'bi-calculator', 32);
            $reportes = $this->grupo($raiz, 'Reportes', 'bi-bar-chart', 40);
            $opciones = ['contabilidad', 'contabilidad.reportes'];
            $orden = 10;
            foreach (config('contabilidad.secciones') as $seccion => [$nombre, $icono]) {
                $ruta = 'contabilidad.'.$seccion.'.index';
                $this->opcion($raiz, $ruta, $nombre, $icono, $orden);
                $opciones[] = $ruta;
                $orden += 10;
            }
            $orden = 10;
            foreach (config('contabilidad.reportes') as $seccion => [$nombre, $icono]) {
                $ruta = 'contabilidad.reportes.'.$seccion;
                $this->opcion($reportes, $ruta, $nombre, $icono, $orden);
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

    private function grupo(?int $padre, string $nombre, string $icono, int $orden): int
    {
        return DB::table('menu')->where('padre_id', $padre)->whereNull('ruta')->where('nombre', $nombre)->value('id')
            ?? DB::table('menu')->insertGetId(['padre_id' => $padre, 'nombre' => $nombre, 'icono' => $icono, 'orden' => $orden, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function opcion(int $padre, string $ruta, string $nombre, string $icono, int $orden): void
    {
        if (! DB::table('menu')->where('ruta', $ruta)->exists()) {
            DB::table('menu')->insert([
                'padre_id' => $padre, 'nombre' => $nombre, 'ruta' => $ruta, 'icono' => $icono, 'orden' => $orden,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Se conservan las configuraciones y asignaciones personalizadas.
    }
};
