<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            foreach (array_keys(config('accesos')) as $opcion) {
                if ($opcion !== 'proveedores' && ! str_starts_with($opcion, 'proveedores.')) {
                    continue;
                }

                DB::table('permisos')->insertOrIgnore(array_map(
                    fn (string $accion): array => ['opcion' => $opcion, 'accion' => $accion],
                    ['leer', 'escribir', 'actualizar', 'eliminar'],
                ));
            }

            $proveedores = $this->opcion(null, 'Proveedores', null, 'bi-truck', 25);
            $this->opcion($proveedores, 'Lista de proveedores', 'proveedores.index', 'bi-list-ul', 10);
            $documentos = $this->opcion($proveedores, 'Documentos', null, 'bi-folder2-open', 20);
            $reportes = $this->opcion($proveedores, 'Reportes', null, 'bi-bar-chart', 30);

            $orden = 10;
            foreach (config('proveedores.secciones') as $ruta => $nombre) {
                $esDocumento = str_starts_with($ruta, 'proveedores.documentos.');
                $this->opcion($esDocumento ? $documentos : $reportes, $nombre, $ruta, $esDocumento ? 'bi-file-earmark-text' : 'bi-graph-up', $orden);
                $orden += 10;
            }
        });
    }

    public function down(): void
    {
        // Se conservan las opciones editadas y los permisos asignados durante la operación.
    }

    private function opcion(?int $padre, string $nombre, ?string $ruta, string $icono, int $orden): int
    {
        $existente = $ruta === null
            ? DB::table('menu')->where('padre_id', $padre)->whereNull('ruta')->where('nombre', $nombre)->value('id')
            : DB::table('menu')->where('ruta', $ruta)->value('id');

        return $existente ?? DB::table('menu')->insertGetId([
            'padre_id' => $padre,
            'nombre' => $nombre,
            'ruta' => $ruta,
            'icono' => $icono,
            'orden' => $orden,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
