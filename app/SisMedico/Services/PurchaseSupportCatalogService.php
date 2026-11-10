<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Support\RequestSchema as Schema;

class PurchaseSupportCatalogService
{
    /**
     * Restaura los catálogos mínimos que Compras V2 y V3 comparten.
     * La operación es idempotente: conserva los datos existentes.
     *
     * @param  string|null  $userId
     * @return void
     */
    public function ensure($userId = null)
    {
        $userId = $this->validUserId($userId);

        $this->ensureTaxCreditTypes($userId);
        $this->ensurePaymentTerms($userId);

        if ($userId !== null) {
            $this->ensurePaymentMethods($userId);
            $this->ensureEmissionPoints($userId);
        }
    }

    private function ensureTaxCreditTypes($userId)
    {
        if (! Schema::hasTable('ct_master_tipos')) {
            return;
        }

        $types = [
            '00' => 'Casos especiales cuyo sustento no aplica en las opciones anteriores',
            '01' => 'Crédito tributario para IVA: servicios y bienes distintos de inventarios y activos fijos',
            '02' => 'Costo o gasto para Impuesto a la Renta: servicios y bienes distintos de inventarios y activos fijos',
            '03' => 'Activo fijo: crédito tributario para IVA',
            '04' => 'Activo fijo: costo o gasto para Impuesto a la Renta',
            '05' => 'Gastos de viaje, hospedaje y alimentación a nombre de empleados',
            '06' => 'Inventario: crédito tributario para IVA',
            '07' => 'Inventario: costo o gasto para Impuesto a la Renta',
            '08' => 'Valor pagado para solicitar reembolso de gasto',
            '09' => 'Reembolso por siniestros',
            '10' => 'Distribución de dividendos, beneficios o utilidades',
            '11' => 'Convenios de débito o recaudación',
            '12' => 'Retenciones presuntivas y actividades reguladas',
            '13' => 'Valores reconocidos por entidades del sector público',
        ];

        foreach ($types as $code => $name) {
            $query = DB::table('ct_master_tipos')->where('codigo', $code)->where('tipo', 2);
            $existing = $query->first();
            if ($existing) {
                if ((int) $existing->estado !== 1) {
                    $query->update([
                        'estado' => 1,
                        'id_usuariomod' => $userId,
                        'ip_modificacion' => '127.0.0.1',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                continue;
            }

            DB::table('ct_master_tipos')->insert($this->auditValues([
                'codigo' => $code,
                'nombre' => $name,
                'tipo' => 2,
                'estado' => 1,
            ], $userId));
        }
    }

    private function ensurePaymentTerms($userId)
    {
        if (! Schema::hasTable('ct_termino')) {
            return;
        }

        // Se preserva el ID 6 = contado porque módulos V2 lo identifican expresamente.
        $terms = [
            1 => [7, 'Crédito 7 días'],
            2 => [15, 'Crédito 15 días'],
            3 => [30, 'Crédito 30 días'],
            4 => [45, 'Crédito 45 días'],
            5 => [60, 'Crédito 60 días'],
            6 => [0, 'Contado'],
            7 => [90, 'Crédito 90 días'],
        ];

        foreach ($terms as $id => $term) {
            $existing = DB::table('ct_termino')
                ->where(function ($query) use ($id, $term) {
                    $query->where('id', $id)->orWhere('dias', $term[0]);
                })
                ->first();
            if ($existing) {
                if ((int) $existing->estado !== 1) {
                    DB::table('ct_termino')->where('id', $existing->id)->update([
                        'estado' => 1,
                        'id_usuariomod' => $userId,
                        'ip_modificacion' => '127.0.0.1',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                continue;
            }

            DB::table('ct_termino')->insert($this->auditValues([
                'id' => $id,
                'dias' => $term[0],
                'nombre' => $term[1],
                'estado' => 1,
            ], $userId));
        }
    }

    private function ensurePaymentMethods($userId)
    {
        if (! Schema::hasTable('ct_tipo_pago')) {
            return;
        }

        $hasCompanyScope = Schema::hasColumn('ct_tipo_pago', 'id_empresa');
        $companyId = $hasCompanyScope
            ? app(CompanyCatalogService::class)->companyId(true)
            : null;

        // Mantiene los IDs históricos de Aitrol y les asigna su código SRI.
        $methods = [
            1 => ['EFEC', '01', 'Efectivo'],
            2 => ['CHEQ', '20', 'Cheque'],
            3 => ['DEPO', '20', 'Depósito'],
            4 => ['TCRE', '19', 'Tarjeta de crédito'],
            5 => ['TRAN', '20', 'Transferencia bancaria'],
            6 => ['TDEB', '16', 'Tarjeta de débito'],
            7 => ['PEND', '20', 'Pendiente de pago'],
            8 => ['COMP', '15', 'Compensación de deudas'],
            9 => ['DELE', '17', 'Dinero electrónico'],
            10 => ['TPRE', '18', 'Tarjeta prepago'],
            11 => ['ENDO', '21', 'Endoso de títulos'],
        ];

        foreach ($methods as $id => $method) {
            $companyQuery = Ct_Tipo_Pago::query();
            if ($hasCompanyScope) {
                $companyQuery->where('id_empresa', $companyId);
            }

            $existing = (clone $companyQuery)->where('codigo', $method[0])->first();
            if (! $existing) {
                $legacyCandidate = (clone $companyQuery)->where('id', $id)->first();
                if ($legacyCandidate && trim((string) $legacyCandidate->codigo) === '') {
                    $existing = $legacyCandidate;
                }
            }

            if ($existing) {
                $updates = [
                    'estado' => 1,
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => '127.0.0.1',
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                if (! $existing->codigo) {
                    $updates['codigo'] = $method[0];
                }
                if (! $existing->codigo_sri) {
                    $updates['codigo_sri'] = $method[1];
                }
                if ($hasCompanyScope) {
                    app(CompanyCatalogService::class)->updateForCompany($existing, $updates);
                } else {
                    $existing->update($updates);
                }

                continue;
            }

            $values = [
                'codigo' => $method[0],
                'codigo_sri' => $method[1],
                'nombre' => $method[2],
                'porcentaje' => 0,
                'estado' => 1,
                'app' => 0,
                'descripcion_app' => $method[2],
            ];
            if ($hasCompanyScope) {
                $values['id_empresa'] = $companyId;
            }
            if (! DB::table('ct_tipo_pago')->where('id', $id)->exists()) {
                $values['id'] = $id;
            }

            Ct_Tipo_Pago::create($this->auditValues($values, $userId));
        }
    }

    private function ensureEmissionPoints($fallbackUserId)
    {
        if (! Schema::hasTable('ct_sucursales') || ! Schema::hasTable('ct_caja')) {
            return;
        }

        $branches = DB::table('ct_sucursales')->where('estado', 1)->get();
        foreach ($branches as $branch) {
            $activePoint = DB::table('ct_caja')
                ->where('id_sucursal', $branch->id)
                ->where('estado', 1)
                ->first();
            if ($activePoint) {
                continue;
            }

            $userId = $this->validUserId($branch->id_usuariocrea);
            $userId = $userId !== null ? $userId : $fallbackUserId;
            $inactivePoint = DB::table('ct_caja')
                ->where('id_sucursal', $branch->id)
                ->orderBy('id')
                ->first();
            if ($inactivePoint) {
                DB::table('ct_caja')->where('id', $inactivePoint->id)->update([
                    'estado' => 1,
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => '127.0.0.1',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                continue;
            }

            DB::table('ct_caja')->insert($this->auditValues([
                'id_sucursal' => $branch->id,
                'id_empresa' => $branch->id_empresa,
                'nombre_caja' => $branch->nombre_sucursal ?: 'Punto de emisión 001',
                'codigo_caja' => '001',
                'estado' => 1,
            ], $userId));
        }
    }

    private function validUserId($candidate)
    {
        if (! Schema::hasTable('users')) {
            return null;
        }

        if ($candidate && DB::table('users')->where('id', $candidate)->exists()) {
            return (string) $candidate;
        }

        $configured = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        if (DB::table('users')->where('id', $configured)->exists()) {
            return (string) $configured;
        }

        $user = DB::table('users')->where('estado', 1)->orderBy('id')->first();

        return $user ? (string) $user->id : null;
    }

    private function auditValues(array $values, $userId)
    {
        $now = date('Y-m-d H:i:s');
        $values['created_at'] = $now;
        $values['updated_at'] = $now;
        $values['ip_creacion'] = '127.0.0.1';
        $values['ip_modificacion'] = '127.0.0.1';

        if ($userId !== null) {
            $values['id_usuariocrea'] = $userId;
            $values['id_usuariomod'] = $userId;
        }

        return $values;
    }
}
