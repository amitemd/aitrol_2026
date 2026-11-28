<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('co_centros_costos', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $tabla->string('codigo', 40);
            $tabla->string('nombre', 160);
            $tabla->string('descripcion', 500)->nullable();
            $tabla->boolean('activo')->default(true);
            $tabla->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $tabla->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
            $tabla->timestamps();
            $tabla->unique(['id_empresa', 'id']);
            $tabla->unique(['id_empresa', 'codigo']);
        });
        Schema::table('co_lineas', function (Blueprint $tabla): void {
            $tabla->unsignedBigInteger('id_centro_costo')->nullable();
            $tabla->foreign(['id_empresa', 'id_centro_costo'])->references(['id_empresa', 'id'])
                ->on('co_centros_costos')->restrictOnDelete();
            $tabla->index(['id_empresa', 'id_centro_costo', 'id_asiento'], 'co_lineas_centro_asiento_index');
        });

        $raiz = DB::table('menu')->whereNull('padre_id')->whereNull('ruta')->where('nombre', 'Contabilidad')->value('id');
        $reportes = DB::table('menu')->where('padre_id', $raiz)->whereNull('ruta')->where('nombre', 'Reportes')->value('id');
        foreach ($this->opciones() as $ruta => [$nombre, $icono, $orden]) {
            $padre = str_starts_with($ruta, 'contabilidad.reportes.') ? $reportes : $raiz;
            if ($padre !== null && ! DB::table('menu')->where('ruta', $ruta)->exists()) {
                DB::table('menu')->insert(['padre_id' => $padre, 'ruta' => $ruta, 'nombre' => $nombre, 'icono' => $icono,
                    'orden' => $orden, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas): void {
            $permisos = [];
            foreach ($empresas as $empresa) {
                foreach (array_keys($this->opciones()) as $opcion) {
                    foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                        $permisos[] = ['id_empresa' => $empresa->id, 'opcion' => $opcion, 'accion' => $accion];
                    }
                }
            }
            DB::table('permisos')->insertOrIgnore($permisos);
        });
    }

    public function down(): void
    {
        Schema::table('co_lineas', function (Blueprint $tabla): void {
            $tabla->dropForeign(['id_empresa', 'id_centro_costo']);
            $tabla->dropIndex('co_lineas_centro_asiento_index');
            $tabla->dropColumn('id_centro_costo');
        });
        Schema::dropIfExists('co_centros_costos');
        DB::table('menu')->whereIn('ruta', array_keys($this->opciones()))->delete();
        DB::table('permisos')->whereIn('opcion', array_keys($this->opciones()))->delete();
    }

    /** @return array<string, array{string, string, int}> */
    private function opciones(): array
    {
        return [
            'contabilidad.centros-costos.index' => ['Centros de costos', 'bi-bullseye', 50],
            'contabilidad.reportes.resultados-centros-costos' => ['Resultados por centro de costos', 'bi-pie-chart', 80],
            'contabilidad.reportes.movimientos-centros-costos' => ['Movimientos por centro de costos', 'bi-list-ul', 90],
        ];
    }
};
