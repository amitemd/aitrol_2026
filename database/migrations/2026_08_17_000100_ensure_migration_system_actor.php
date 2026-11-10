<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $actorId = (string) env('BOOTSTRAP_SYSTEMS_ID', '0000000001');

        $now = now()->format('Y-m-d H:i:s');
        if (! DB::table('users')->where('id', $actorId)->exists()) {
            $values = [
                'id' => $actorId,
                'nombre1' => 'AITROL',
                'nombre2' => null,
                'apellido1' => 'MIGRACIONES',
                'apellido2' => 'SISTEMA',
                'nombres' => 'AITROL',
                'apellidos' => 'MIGRACIONES SISTEMA',
                'tipo_documento' => 1,
                'pais' => 1,
                'institucion' => 'AITROL',
                'tipoUsuario' => 1,
                'estado' => 0,
                'password' => Hash::make(Str::random(64)),
                'imagen_url' => ' ',
                'id_tipo_usuario' => 1,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            DB::table('users')->insert(array_intersect_key(
                $values,
                array_flip(Schema::getColumnListing('users'))
            ));
        }

        if (Schema::hasTable('tipousuario') && ! DB::table('tipousuario')->where('id', 1)->exists()) {
            DB::table('tipousuario')->insert([
                'id' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador del sistema',
                'estado' => 1,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }
    }

    public function down(): void
    {
        // El actor puede estar referenciado por registros históricos de migración.
    }
};
