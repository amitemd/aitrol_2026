<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $ahora = now();
            $auditoria = DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', 'Auditoría')->value('id');
            $auditoria ??= DB::table('menu')->insertGetId([
                'nombre' => 'Auditoría', 'icono' => 'bi-shield-check', 'orden' => 36,
                'created_at' => $ahora, 'updated_at' => $ahora,
            ]);

            foreach ([
                'auditoria.logs.index' => ['Bitácora', 'bi-list-check', 10],
                'swagger.index' => ['Documentación API', 'bi-braces', 20],
            ] as $ruta => [$nombre, $icono, $orden]) {
                $opcion = DB::table('menu')->where('ruta', $ruta);
                if ($opcion->exists()) {
                    $opcion->update(['padre_id' => $auditoria, 'orden' => $orden, 'updated_at' => $ahora]);
                    continue;
                }
                DB::table('menu')->insert([
                    'padre_id' => $auditoria, 'ruta' => $ruta, 'nombre' => $nombre, 'icono' => $icono,
                    'orden' => $orden, 'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }

            DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', 'Configuraciones generales')
                ->update(['nombre' => 'Configuración', 'updated_at' => $ahora]);

            DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas): void {
                DB::table('permisos')->insertOrIgnore($empresas->map(fn ($empresa) => [
                    'id_empresa' => $empresa->id, 'opcion' => 'auditoria.logs.index', 'accion' => 'leer',
                ])->all());
            });
        });
    }

    public function down(): void
    {
        // Se conservan las personalizaciones de menú y los permisos asignados.
    }
};
