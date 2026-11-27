<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLAS = ['af_activos', 'cb_cuentas', 'cb_operaciones'];

    public function up(): void
    {
        if (! Schema::hasTable('erp_sucursales')) {
            $migracionInicial = require __DIR__.'/2026_11_27_130000_crear_sucursales_operativas.php';
            $migracionInicial->up();
        }

        foreach (self::TABLAS as $tabla) {
            $columnas = ['id_empresa', 'id_sucursal'];
            $crearColumna = ! Schema::hasColumn($tabla, 'id_sucursal');
            $crearIndice = ! Schema::hasIndex($tabla, $tabla.'_sucursal_idx');
            $crearForanea = ! collect(Schema::getForeignKeys($tabla))->contains(
                fn (array $foranea): bool => $foranea['columns'] === $columnas && $foranea['foreign_table'] === 'erp_sucursales',
            );
            if ($crearColumna || $crearIndice || $crearForanea) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla, $columnas, $crearColumna, $crearIndice, $crearForanea): void {
                    if ($crearColumna) {
                        $table->unsignedBigInteger('id_sucursal')->nullable();
                    }
                    if ($crearIndice) {
                        $table->index($columnas, $tabla.'_sucursal_idx');
                    }
                    if ($crearForanea) {
                        $table->foreign($columnas, $tabla.'_sucursal_fk')
                            ->references(['id_empresa', 'id'])->on('erp_sucursales')->restrictOnDelete();
                    }
                });
            }

            DB::table('erp_sucursales')->where('codigo', 'PRINCIPAL')->chunkById(200, function ($sucursales) use ($tabla): void {
                foreach ($sucursales as $sucursal) {
                    DB::table($tabla)->where('id_empresa', $sucursal->id_empresa)->whereNull('id_sucursal')
                        ->update(['id_sucursal' => $sucursal->id]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TABLAS) as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla): void {
                $table->dropForeign($tabla.'_sucursal_fk');
                $table->dropIndex($tabla.'_sucursal_idx');
                $table->dropColumn('id_sucursal');
            });
        }
    }
};
