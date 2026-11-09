<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedUsdBaseCurrency extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_divisas') || ! Schema::hasTable('users')) {
            return;
        }

        $userId = DB::table('users')->where('estado', 1)->orderBy('id')->value('id');
        if (! $userId) {
            $userId = DB::table('users')->orderBy('id')->value('id');
        }

        if (! $userId) {
            throw new RuntimeException('No existe un usuario válido para registrar la divisa base USD.');
        }

        $now = date('Y-m-d H:i:s');
        $usd = DB::table('ct_divisas')->where('id', 1)->first();

        if ($usd) {
            DB::table('ct_divisas')->where('id', 1)->update([
                'descripcion' => 'Dólar estadounidense (USD)',
                'estado' => 1,
                'id_usuariomod' => $userId,
                'ip_modificacion' => '127.0.0.1',
                'updated_at' => $now,
            ]);

            return;
        }

        DB::table('ct_divisas')->insert([
            'id' => 1,
            'descripcion' => 'Dólar estadounidense (USD)',
            'estado' => 1,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        // Se conserva USD porque puede estar referenciado por documentos contables.
    }
}
