<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreMasterDocumentTypes extends Migration
{
    /**
     * Restaura el catalogo que consumen Compras V2 y V3.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('ct_master_tipos')) {
            return;
        }

        $tipos = [
            '01' => 'Factura',
            '03' => 'Liquidación de compra de bienes o prestación de servicios',
            '04' => 'Nota de crédito',
            '05' => 'Nota de débito',
            '06' => 'Guía de remisión',
            '07' => 'Comprobante de retención',
        ];

        foreach ($tipos as $codigo => $nombre) {
            $existe = DB::table('ct_master_tipos')
                ->where('codigo', $codigo)
                ->where('tipo', 1)
                ->exists();

            if ($existe) {
                continue;
            }

            DB::table('ct_master_tipos')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo' => 1,
                'estado' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * No elimina datos maestros que ya pueden estar en uso.
     *
     * @return void
     */
    public function down()
    {
        // Intencionalmente conservador.
    }
}
