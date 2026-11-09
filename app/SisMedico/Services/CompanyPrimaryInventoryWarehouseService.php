<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\BodegasContable;

class CompanyPrimaryInventoryWarehouseService
{
    /**
     * Resuelve la bodega principal V3 y su bodega equivalente en el inventario legado.
     * La correspondencia es empresarial; nunca se infiere por existencias ni por usuario.
     *
     * @throws \DomainException
     */
    public function resolve($companyId)
    {
        return $this->resolveInventoryForPrincipal($this->principal($companyId));
    }

    /**
     * Obtiene la bodega principal visible en Configuraciones > Bodegas.
     *
     * @throws \DomainException
     */
    public function principal($companyId)
    {
        $companyId = trim((string) $companyId);
        if ($companyId === '') {
            throw new \DomainException('Seleccione una empresa activa antes de realizar el descargo.');
        }

        $accountingWarehouseId = BodegasContable::principalIdParaEmpresa($companyId);
        if ($accountingWarehouseId < 1) {
            throw new \DomainException('La empresa actual no tiene una bodega principal activa configurada.');
        }

        $accountingWarehouse = BodegasContable::where('id', $accountingWarehouseId)
            ->where('id_empresa', $companyId)
            ->where('estado', 1)
            ->first(array('id', 'nombre', 'id_hospital', 'color'));
        if (!$accountingWarehouse) {
            throw new \DomainException('La bodega principal configurada no está activa o no pertenece a la empresa actual.');
        }

        return (object) array(
            'company_id' => $companyId,
            'accounting_id' => (int) $accountingWarehouse->id,
            'name' => $accountingWarehouse->nombre,
            'hospital_id' => $accountingWarehouse->id_hospital ? (int) $accountingWarehouse->id_hospital : null,
            'color' => $accountingWarehouse->color,
        );
    }

    /**
     * Repara instalaciones antiguas creando la contraparte de inventario y su
     * unión cuando la empresa todavía no posee ninguna bodega física.
     */
    public function resolveOrProvision($companyId, $actorId, $ip, $hospitalId = null)
    {
        $principal = $this->principal($companyId);
        try {
            $resolved = $this->resolveInventoryForPrincipal($principal);
            if ($resolved->mapping_inferred) {
                $this->persistMapping($principal, $resolved->inventory_id, $actorId, $ip);
                $resolved->mapping_inferred = false;
            }
            return $resolved;
        } catch (\DomainException $exception) {
            $hasMapping = DB::table('union_bodega')
                ->where('id_empresa', $principal->company_id)
                ->where('id_bodega_cont', $principal->accounting_id)
                ->whereNotNull('id_bodega_compra')->exists();
            $hasCompanyWarehouse = Bodega::where('estado', 1)
                ->where('id_empresa', $principal->company_id)->exists();
            $hasCompanyInventory = Schema::hasTable('inv_inventario_serie')
                && DB::table('inv_inventario_serie')->where('id_empresa', $principal->company_id)
                    ->where('estado', '<>', 0)->exists();
            if ($hasMapping || $hasCompanyWarehouse || $hasCompanyInventory) throw $exception;

            $hospitalId = $hospitalId ?: $principal->hospital_id;
            if (!$hospitalId && Schema::hasTable('company_hospital_contexts')) {
                $hospitalId = DB::table('company_hospital_contexts')
                    ->where('company_id', $principal->company_id)->where('active', 1)
                    ->orderBy('id')->value('hospital_id');
            }
            if (!$hospitalId && Schema::hasTable('hospital') && Schema::hasColumn('hospital', 'id_empresa')) {
                $hospitalId = DB::table('hospital')->where('id_empresa', $principal->company_id)
                    ->where('estado', 1)->orderBy('id')->value('id');
            }
            if (!$hospitalId) {
                throw new \DomainException('La bodega principal no tiene una sede empresarial asociada para crear su contraparte de inventario.');
            }

            return DB::transaction(function () use ($principal, $actorId, $ip, $hospitalId) {
                $warehouse = Bodega::create(array(
                    'id_hospital' => $hospitalId,
                    'nombre' => $principal->name,
                    'color' => $principal->color ?: '#0ea5a8',
                    'estado' => 1,
                    'encargado' => $actorId,
                    'id_empresa' => $principal->company_id,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ));
                $this->persistMapping($principal, $warehouse->id, $actorId, $ip);
                return $this->result($principal, $warehouse, false);
            });
        }
    }

    /**
     * @throws \DomainException
     */
    public function resolveInventoryForPrincipal($principal)
    {
        $companyId = (string) $principal->company_id;

        $inventoryWarehouseIds = DB::table('union_bodega')
            ->where('id_empresa', $companyId)
            ->where('id_bodega_cont', $principal->accounting_id)
            ->whereNotNull('id_bodega_compra')
            ->distinct()
            ->pluck('id_bodega_compra');

        if ($inventoryWarehouseIds->count() !== 1) {
            if ($inventoryWarehouseIds->isEmpty()) {
                return $this->inferInventoryWarehouse($principal);
            }
            throw new \DomainException('La bodega principal debe tener una sola correspondencia con la bodega de inventario.');
        }

        $inventoryWarehouse = Bodega::where('id', $inventoryWarehouseIds->first())
            ->where('estado', 1)
            ->first(array('id', 'nombre', 'id_empresa'));
        if (!$inventoryWarehouse) {
            throw new \DomainException('La bodega de inventario vinculada a la bodega principal no está activa.');
        }

        // La tabla de unión es la fuente empresarial de la correspondencia. Además,
        // rechazamos una vinculación que señale expresamente a otra empresa.
        $inventoryCompanyId = trim((string) $inventoryWarehouse->id_empresa);
        if ($inventoryCompanyId !== '' && $inventoryCompanyId !== $companyId) {
            throw new \DomainException('La bodega de inventario vinculada no pertenece a la empresa actual.');
        }

        return (object) array(
            'company_id' => $companyId,
            'accounting_id' => (int) $principal->accounting_id,
            'inventory_id' => (int) $inventoryWarehouse->id,
            'name' => $principal->name,
            'inventory_name' => $inventoryWarehouse->nombre,
            'mapping_inferred' => false,
        );
    }

    /**
     * Compatibilidad con empresas creadas antes de que existiera union_bodega.
     * Solo se acepta una correspondencia inequívoca y siempre dentro de la empresa.
     */
    private function inferInventoryWarehouse($principal)
    {
        $companyId = (string) $principal->company_id;
        $companyWarehouses = Bodega::where('estado', 1)
            ->where('id_empresa', $companyId)
            ->get(array('id', 'nombre', 'id_empresa'));

        $principalName = $this->normalizeName($principal->name);
        $sameName = $companyWarehouses->filter(function ($warehouse) use ($principalName) {
            return $this->normalizeName($warehouse->nombre) === $principalName;
        })->values();
        if ($sameName->count() === 1) {
            return $this->result($principal, $sameName->first(), true);
        }
        if ($sameName->count() > 1) {
            throw new \DomainException('Existen varias bodegas de inventario con el nombre de la bodega principal; configure una sola correspondencia.');
        }

        if (Schema::hasTable('inv_inventario_serie')) {
            $inventoryWarehouseIds = DB::table('inv_inventario_serie')
                ->where('id_empresa', $companyId)
                ->where('estado', '<>', 0)
                ->whereNotNull('id_bodega')
                ->distinct()
                ->pluck('id_bodega');
            $warehousesWithInventory = Bodega::where('estado', 1)
                ->whereIn('id', $inventoryWarehouseIds)
                ->get(array('id', 'nombre', 'id_empresa'))
                ->filter(function ($warehouse) use ($companyId) {
                    $warehouseCompany = trim((string) $warehouse->id_empresa);
                    return $warehouseCompany === '' || $warehouseCompany === $companyId;
                })->values();
            if ($warehousesWithInventory->count() === 1) {
                return $this->result($principal, $warehousesWithInventory->first(), true);
            }
        }

        if ($companyWarehouses->count() === 1) {
            return $this->result($principal, $companyWarehouses->first(), true);
        }

        throw new \DomainException('La bodega principal de la empresa no está vinculada a una bodega de inventario y no existe una correspondencia única que pueda usarse automáticamente.');
    }

    private function result($principal, $inventoryWarehouse, $inferred)
    {
        return (object) array(
            'company_id' => (string) $principal->company_id,
            'accounting_id' => (int) $principal->accounting_id,
            'inventory_id' => (int) $inventoryWarehouse->id,
            'name' => $principal->name,
            'inventory_name' => $inventoryWarehouse->nombre,
            'mapping_inferred' => (bool) $inferred,
        );
    }

    private function persistMapping($principal, $inventoryWarehouseId, $actorId, $ip)
    {
        $now = date('Y-m-d H:i:s');
        DB::table('union_bodega')->updateOrInsert(
            array(
                'id_empresa' => $principal->company_id,
                'id_bodega_cont' => $principal->accounting_id,
            ),
            array(
                'id_bodega_compra' => $inventoryWarehouseId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            )
        );
    }

    private function normalizeName($name)
    {
        $name = preg_replace('/\s+/u', ' ', trim((string) $name));
        return function_exists('mb_strtoupper') ? mb_strtoupper($name, 'UTF-8') : strtoupper($name);
    }
}
