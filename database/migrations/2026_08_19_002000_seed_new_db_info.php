<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedNewDbInfo extends Migration
{
    /**
     * Registra la informacion base que utiliza la version 3 de Aitrol.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('new_db_info')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $systemsId = $this->systemsUserId();
        $values = $this->existingColumns([
            'nombre_info' => 'Aitrol',
            'version_info' => '3',
            'mayor_info' => '1',
            'menor_info' => '01',
            'revision_info' => '20231018',
            'id_usuario_creacion_info' => $systemsId,
            'id_usuario_modificacion_info' => $systemsId,
            'ip_creacion_info' => '::1',
            'ip_modificacion_info' => '::1',
            'created_at_info' => $now,
            'updated_at_info' => $now,
        ]);

        $info = DB::table('new_db_info')
            ->whereRaw('LOWER(nombre_info) = ?', ['aitrol'])
            ->orderBy('id_info')
            ->first();

        // V3 consulta el primer registro. Si ya hay uno, se reutiliza para no
        // dejar dos configuraciones compitiendo por el nombre y la version.
        if (! $info) {
            $info = DB::table('new_db_info')->orderBy('id_info')->first();
        }

        if ($info) {
            unset($values['created_at_info']);

            // No se reemplaza una auditoria existente por NULL cuando las
            // migraciones se ejecutan antes de crear los usuarios iniciales.
            if (! $systemsId) {
                unset(
                    $values['id_usuario_creacion_info'],
                    $values['id_usuario_modificacion_info']
                );
            }

            DB::table('new_db_info')->where('id_info', $info->id_info)->update($values);

            return;
        }

        DB::table('new_db_info')->insert($values);
    }

    /**
     * La informacion del producto es un catalogo base y no se elimina al
     * revertir para evitar dejar V3 sin nombre o version.
     *
     * @return void
     */
    public function down()
    {
        // No destructivo de forma intencional.
    }

    private function systemsUserId()
    {
        if (! Schema::hasTable('users')) {
            return null;
        }

        $configuredId = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $user = DB::table('users')->where('id', $configuredId)->first();

        if (! $user && Schema::hasColumn('users', 'id_tipo_usuario')) {
            $user = DB::table('users')->where('id_tipo_usuario', 1)->orderBy('id')->first();
        }

        return $user ? $user->id : null;
    }

    private function existingColumns(array $values)
    {
        return array_intersect_key(
            $values,
            array_flip(Schema::getColumnListing('new_db_info'))
        );
    }
}
