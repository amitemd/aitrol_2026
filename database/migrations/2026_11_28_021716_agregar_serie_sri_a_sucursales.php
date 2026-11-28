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
        Schema::table('erp_sucursales', function (Blueprint $tabla): void {
            $tabla->string('establecimiento_sri', 3)->nullable();
            $tabla->string('punto_emision_sri', 3)->nullable();
            $tabla->unique(['id_empresa', 'establecimiento_sri', 'punto_emision_sri'], 'erp_sucursales_serie_sri_unique');
        });

        $punto = (string) config('comercial.ventas.facturacion_electronica.punto_emision', '001');
        if (! preg_match('/\A(?!000)[0-9]{3}\z/', $punto)) {
            return;
        }

        DB::table('erp_sucursales')->select('id', 'codigo')->chunkById(200, function ($sucursales) use ($punto): void {
            foreach ($sucursales as $sucursal) {
                if (preg_match('/\A(?!000)[0-9]{3}\z/', $sucursal->codigo)) {
                    DB::table('erp_sucursales')->where('id', $sucursal->id)->update([
                        'establecimiento_sri' => $sucursal->codigo,
                        'punto_emision_sri' => $punto,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_sucursales', function (Blueprint $tabla): void {
            $tabla->dropUnique('erp_sucursales_serie_sri_unique');
            $tabla->dropColumn(['establecimiento_sri', 'punto_emision_sri']);
        });
    }
};
