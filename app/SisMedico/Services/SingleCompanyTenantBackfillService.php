<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class SingleCompanyTenantBackfillService
{
    public function run()
    {
        if (!Schema::hasTable('empresa')) {
            throw new \RuntimeException('No existe la tabla empresa.');
        }
        $companies = DB::table('empresa')->where('estado', 1)->orderBy('prioridad')->get(array('id'));
        if ($companies->count() !== 1) {
            throw new \RuntimeException('El backfill automático requiere exactamente una empresa activa; no se asignarán datos ambiguos.');
        }
        $companyId = (string) $companies->first()->id;
        $actorId = $this->actorId();

        return DB::transaction(function () use ($companyId, $actorId) {
            $updated = array();
            $database = DB::connection()->getDatabaseName();
            $tables = DB::table('information_schema.COLUMNS')->where('TABLE_SCHEMA', $database)
                ->where('COLUMN_NAME', 'id_empresa')->orderBy('TABLE_NAME')->pluck('TABLE_NAME');
            foreach ($tables as $table) {
                $table = (string) $table;
                if ($table === 'usuario_empresa' || $table === 'paciente_empresa') continue;
                $count = DB::table($table)->where(function ($query) {
                    $query->whereNull('id_empresa')->orWhereRaw("TRIM(id_empresa) = ''");
                })->update(array('id_empresa' => $companyId));
                if ($count) $updated[$table] = $count;
            }

            $memberships = $this->backfillUsers($companyId, $actorId);
            return array('company_id' => $companyId, 'updated_rows' => array_sum($updated),
                'updated_tables' => $updated, 'created_user_memberships' => $memberships);
        }, 3);
    }

    private function backfillUsers($companyId, $actorId)
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('usuario_empresa')) return 0;
        $users = DB::table('users as tenant_user')
            ->leftJoin('usuario_empresa as membership', function ($join) use ($companyId) {
                $join->on('membership.id_usuario', '=', 'tenant_user.id')
                    ->where('membership.id_empresa', '=', $companyId);
            })->where('tenant_user.estado', 1)->whereNull('membership.id')->pluck('tenant_user.id');
        $now = date('Y-m-d H:i:s');
        foreach ($users as $userId) {
            DB::table('usuario_empresa')->insert(array('id_empresa' => $companyId, 'id_usuario' => $userId,
                'id_usuariocrea' => $actorId, 'id_usuariomod' => $actorId, 'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now));
        }
        return count($users);
    }

    private function actorId()
    {
        if (!Schema::hasTable('users')) throw new \RuntimeException('No existe la tabla users.');
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)->orderBy('id')->value('id');
        if (!$actor) $actor = DB::table('users')->where('estado', 1)->orderBy('id')->value('id');
        if (!$actor) throw new \RuntimeException('No existe un usuario activo para auditar el backfill.');
        return (string) $actor;
    }
}
