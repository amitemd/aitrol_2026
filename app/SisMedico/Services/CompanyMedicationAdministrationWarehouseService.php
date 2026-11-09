<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Support\RequestSchema as Schema;

/**
 * Mantiene una bodega de dispensacion/administracion independiente por empresa.
 * La bodega principal recibe compras y ajustes; esta bodega solo conserva el
 * stock que ya fue trasladado para entregar y administrar a pacientes.
 */
class CompanyMedicationAdministrationWarehouseService
{
    const PARAMETER = 'V3_BODEGA_ADMINISTRACION_MEDICAMENTOS';
    const NAME = 'BODEGA ADMINISTRACION DE MEDICAMENTOS';

    private $primaryWarehouse;

    public function __construct(CompanyPrimaryInventoryWarehouseService $primaryWarehouse)
    {
        $this->primaryWarehouse = $primaryWarehouse;
    }

    /**
     * @throws \DomainException
     */
    public function resolveOrProvision($companyId, $actorId, $ip, $hospitalId = null)
    {
        $companyId = trim((string) $companyId);
        if ($companyId === '') {
            throw new \DomainException('Seleccione una empresa activa antes de administrar medicamentos.');
        }

        return DB::transaction(function () use ($companyId, $actorId, $ip, $hospitalId) {
            if (!DB::table('empresa')->where('id', $companyId)->lockForUpdate()->first()) {
                throw new \DomainException('La empresa activa no existe.');
            }

            $configuredId = (int) DB::table('de_parametros')
                ->where('id_empresa', $companyId)->where('nombre', self::PARAMETER)
                ->where('estado', 1)->value('valor');
            if ($configuredId > 0) {
                $resolved = $this->resolveAccountingWarehouse($companyId, $configuredId);
                if ($resolved) return $resolved;
            }

            $primary = $this->primaryWarehouse->resolveOrProvision(
                $companyId, $actorId, $ip, $hospitalId
            );
            $primaryAccounting = $this->primaryWarehouse->principal($companyId);
            $hospitalId = $hospitalId ?: $primaryAccounting->hospital_id;
            if (!$hospitalId) {
                $hospitalId = DB::table('bodega')->where('id', $primary->inventory_id)->value('id_hospital');
            }
            if (!$hospitalId) {
                throw new \DomainException('No se pudo determinar la sede para crear la bodega de administracion de medicamentos.');
            }

            $accounting = DB::table('ct_bodegas')->where('id_empresa', $companyId)
                ->whereRaw('UPPER(nombre) = ?', array(self::NAME))->where('estado', 1)
                ->lockForUpdate()->first();
            if (!$accounting) {
                $accountingData = array(
                    'id_hospital' => $hospitalId,
                    'nombre' => self::NAME,
                    'color' => '#14b8a6',
                    'departamento' => null,
                    'id_empresa' => $companyId,
                    'estado' => 1,
                    'encargado' => $actorId,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                if (Schema::hasColumn('ct_bodegas', 'principal')) $accountingData['principal'] = 0;
                $accountingId = DB::table('ct_bodegas')->insertGetId($accountingData);
            } else {
                $accountingId = (int) $accounting->id;
            }

            $physical = Bodega::where('id_empresa', $companyId)
                ->whereRaw('UPPER(nombre) = ?', array(self::NAME))->where('estado', 1)
                ->lockForUpdate()->first();
            if (!$physical) {
                $physical = Bodega::create(array(
                    'id_hospital' => $hospitalId,
                    'nombre' => self::NAME,
                    'color' => '#14b8a6',
                    'estado' => 1,
                    'prioridad' => 0,
                    'encargado' => $actorId,
                    'id_empresa' => $companyId,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'tipo' => 1,
                    'mostrar' => 1,
                    'prioridad_farmacia' => 1,
                ));
            }

            DB::table('union_bodega')->updateOrInsert(
                array('id_empresa' => $companyId, 'id_bodega_cont' => $accountingId),
                array(
                    'id_bodega_compra' => $physical->id,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                )
            );
            $this->saveParameter($companyId, $accountingId, $actorId, $ip);

            return $this->result($companyId, $accountingId, $physical->id);
        }, 3);
    }

    private function resolveAccountingWarehouse($companyId, $accountingId)
    {
        $accounting = DB::table('ct_bodegas')->where('id', $accountingId)
            ->where('id_empresa', $companyId)->where('estado', 1)->lockForUpdate()->first();
        if (!$accounting) return null;

        $physicalIds = DB::table('union_bodega')->where('id_empresa', $companyId)
            ->where('id_bodega_cont', $accountingId)->whereNotNull('id_bodega_compra')
            ->distinct()->pluck('id_bodega_compra');
        if ($physicalIds->count() !== 1) return null;

        $physical = Bodega::where('id', $physicalIds->first())->where('id_empresa', $companyId)
            ->where('estado', 1)->lockForUpdate()->first();
        if (!$physical) return null;

        return $this->result($companyId, $accountingId, $physical->id);
    }

    private function result($companyId, $accountingId, $inventoryId)
    {
        return (object) array(
            'company_id' => (string) $companyId,
            'accounting_id' => (int) $accountingId,
            'inventory_id' => (int) $inventoryId,
            'name' => self::NAME,
            'inventory_name' => self::NAME,
        );
    }

    private function saveParameter($companyId, $accountingId, $actorId, $ip)
    {
        DB::table('de_parametros')->updateOrInsert(
            array('id_empresa' => $companyId, 'nombre' => self::PARAMETER),
            array(
                'valor' => (string) $accountingId,
                'estado' => 1,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            )
        );
    }
}
