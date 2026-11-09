<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureDefaultFlatFileInsuranceType extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('tipo_seguro')) {
            return;
        }

        $existingId = DB::table('tipo_seguro')
            ->where(function ($query) {
                $query->whereRaw('UPPER(TRIM(nombre)) = ?', ['ACTIVO'])
                    ->orWhereRaw('UPPER(TRIM(tipo_principal)) = ?', ['ACTIVO']);
            })
            ->orderBy('id')
            ->value('id');

        if ($existingId) {
            DB::table('tipo_seguro')->where('id', $existingId)->update([
                'estado' => 1,
                'updated_at' => now(),
            ]);

            return;
        }

        $actor = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)
            ->orderBy('id')->value('id');
        if (! $actor) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }
        if (! $actor) {
            throw new RuntimeException('No se puede registrar el tipo de seguro ACTIVO porque no existe un usuario para la auditoría.');
        }

        DB::table('tipo_seguro')->insert([
            'nombre' => 'ACTIVO',
            'tipo' => 'SG',
            'tipo_principal' => 'ACTIVO',
            'estado' => 1,
            'id_usuariocrea' => (string) $actor,
            'id_usuariomod' => (string) $actor,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        // No se elimina porque puede quedar referenciado por cabeceras de archivo plano.
    }
}
