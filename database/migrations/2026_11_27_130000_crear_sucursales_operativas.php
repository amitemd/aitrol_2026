<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('erp_sucursales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->string('codigo', 30);
            $table->string('nombre', 120);
            $table->string('direccion', 255)->nullable();
            $table->timestamps();
            $table->unique(['id_empresa', 'codigo']);
            $table->unique(['id_empresa', 'id']);
        });

        $empresasConHistorial = DB::table('erp_empresas')->select('id')->where(function ($consulta): void {
            foreach (['com_documentos', 'inv_bodegas', 'af_activos', 'cb_cuentas', 'cb_operaciones'] as $tabla) {
                $consulta->orWhereExists(DB::table($tabla)->select('id')->whereColumn('id_empresa', 'erp_empresas.id'));
            }
        });
        $empresasConHistorial->chunkById(200, function ($empresas): void {
            DB::table('erp_sucursales')->insert($empresas->map(fn ($empresa): array => [
                'id_empresa' => $empresa->id, 'codigo' => 'PRINCIPAL', 'nombre' => 'Principal',
                'created_at' => now(), 'updated_at' => now(),
            ])->all());
        });

        foreach (['inv_bodegas', 'com_documentos'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla): void {
                $table->unsignedBigInteger('id_sucursal')->nullable();
                $table->index(
                    $tabla === 'com_documentos' ? ['id_empresa', 'id_sucursal', 'modulo', 'fecha'] : ['id_empresa', 'id_sucursal'],
                    $tabla.'_sucursal_idx',
                );
                $table->foreign(['id_empresa', 'id_sucursal'], $tabla.'_sucursal_fk')
                    ->references(['id_empresa', 'id'])->on('erp_sucursales')->restrictOnDelete();
            });
            DB::table('erp_sucursales')->where('codigo', 'PRINCIPAL')->chunkById(200, function ($sucursales) use ($tabla): void {
                foreach ($sucursales as $sucursal) {
                    DB::table($tabla)->where('id_empresa', $sucursal->id_empresa)->whereNull('id_sucursal')
                        ->update(['id_sucursal' => $sucursal->id]);
                }
            });
        }

        $padre = DB::table('menu')->where('ruta', 'configuracion.menu.index')->where('protegido', true)->value('padre_id');
        DB::table('menu')->insert([
            'padre_id' => $padre, 'nombre' => 'Sucursales', 'ruta' => 'configuracion.sucursales.index',
            'icono' => 'bi-diagram-3', 'orden' => 35, 'solo_administrador' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menu')->where('ruta', 'configuracion.sucursales.index')->delete();
        foreach (['com_documentos', 'inv_bodegas'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla): void {
                $table->dropForeign($tabla.'_sucursal_fk');
                $table->dropIndex($tabla.'_sucursal_idx');
                $table->dropColumn('id_sucursal');
            });
        }
        Schema::dropIfExists('erp_sucursales');
    }
};
