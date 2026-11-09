<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreElectronicDocumentCatalog extends Migration
{
    /**
     * Garantiza el catalogo utilizado por la numeracion y documentos SRI.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('de_maestro_documentos') || ! Schema::hasTable('users')) {
            return;
        }

        $usuario = DB::table('users')->orderBy('id')->first(['id']);
        if (! $usuario) {
            return;
        }

        $documentos = [
            '01' => 'Factura',
            '03' => 'Liquidacion de compra de bienes o prestacion de servicios',
            '04' => 'Nota de credito',
            '05' => 'Nota de debito',
            '06' => 'Guia de remision',
            '07' => 'Comprobante de retencion',
        ];
        $ahora = date('Y-m-d H:i:s');

        foreach ($documentos as $codigo => $nombre) {
            $existente = DB::table('de_maestro_documentos')
                ->where('codigo', $codigo)
                ->orderBy('id')
                ->first();

            if ($existente) {
                DB::table('de_maestro_documentos')->where('id', $existente->id)->update([
                    'nombre' => $nombre,
                    'estado' => 1,
                    'id_usuariomod' => $usuario->id,
                    'ip_modificacion' => '127.0.0.1',
                    'updated_at' => $ahora,
                ]);

                continue;
            }

            DB::table('de_maestro_documentos')->insert([
                'nombre' => $nombre,
                'codigo' => $codigo,
                'estado' => 1,
                'id_usuariocrea' => $usuario->id,
                'id_usuariomod' => $usuario->id,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    /**
     * No elimina documentos que pueden estar relacionados con comprobantes.
     *
     * @return void
     */
    public function down()
    {
        // Conservador para no romper claves foraneas ni historicos tributarios.
    }
}
