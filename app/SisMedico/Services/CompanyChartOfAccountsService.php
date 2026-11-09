<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class CompanyChartOfAccountsService
{
    /**
     * Copy the master chart installed by the reference-data migration to one company.
     */
    public function copyMigrationChart($companyId, $actorId, $ip)
    {
        $companyId = trim((string) $companyId);
        $actorId = trim((string) $actorId);

        if ($companyId === '' || $actorId === '') {
            throw new \RuntimeException('No se pudo determinar la empresa o el usuario activo.');
        }
        if (!Schema::hasTable('empresa') || !Schema::hasTable('plan_cuentas') || !Schema::hasTable('plan_cuentas_empresa')) {
            throw new \RuntimeException('Las migraciones del plan de cuentas aún no están instaladas.');
        }

        return DB::transaction(function () use ($companyId, $actorId, $ip) {
            $company = DB::table('empresa')->where('id', $companyId)->lockForUpdate()->first();
            if (!$company) {
                throw new \RuntimeException('La empresa activa ya no se encuentra disponible.');
            }
            if (DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->exists()) {
                throw new \RuntimeException('La empresa ya tiene un plan de cuentas. Actualiza la página para visualizarlo.');
            }

            $accounts = DB::table('plan_cuentas')
                ->where('estado', '>', 0)
                ->orderByRaw('LENGTH(id) ASC')
                ->orderBy('id')
                ->get(array('id', 'nombre', 'descripcion', 'estado', 'id_padre', 'naturaleza', 'naturaleza_2'));
            if ($accounts->isEmpty()) {
                throw new \RuntimeException('La plantilla contable de las migraciones está vacía. Contacte a Sistemas.');
            }

            $now = date('Y-m-d H:i:s');
            $hasClosing = Schema::hasColumn('plan_cuentas_empresa', 'estado_cierre_ano');
            $hasCategory = Schema::hasColumn('plan_cuentas_empresa', 'categoria_niif');
            $hasCashFlow = Schema::hasColumn('plan_cuentas_empresa', 'flujo_efectivo_categoria');
            $hasCash = Schema::hasColumn('plan_cuentas_empresa', 'es_efectivo');
            $rows = array();

            foreach ($accounts as $account) {
                $code = (string) $account->id;
                $first = substr($code, 0, 1);
                $row = array(
                    'id_empresa' => $companyId,
                    'id_plan' => $code,
                    'plan' => $code,
                    'nombre' => $account->nombre,
                    'descripcion' => $account->descripcion,
                    'estado' => $account->estado,
                    'id_padre' => $account->id_padre,
                    'naturaleza' => $account->naturaleza,
                    'naturaleza_2' => $account->naturaleza_2,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now,
                );
                if ($hasClosing) $row['estado_cierre_ano'] = in_array($first, array('4', '5', '6'), true) ? 1 : 0;
                if ($hasCategory) $row['categoria_niif'] = $this->category($first);
                if ($hasCashFlow) $row['flujo_efectivo_categoria'] = in_array($first, array('4', '5', '6'), true) ? 'OPERACION' : null;
                if ($hasCash) $row['es_efectivo'] = strpos($code, '1.01.01') === 0 ? 1 : 0;
                $rows[] = $row;
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                DB::table('plan_cuentas_empresa')->insert($chunk);
            }

            return count($rows);
        });
    }

    private function category($first)
    {
        $categories = array('1' => 'ACTIVO', '2' => 'PASIVO', '3' => 'PATRIMONIO',
            '4' => 'INGRESO', '5' => 'COSTO', '6' => 'GASTO');
        return isset($categories[$first]) ? $categories[$first] : null;
    }
}
