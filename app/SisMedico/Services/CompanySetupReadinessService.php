<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\DeEmpresas;

class CompanySetupReadinessService
{
    public function inspect($companyId)
    {
        $companyId = trim((string) $companyId);
        $items = array();
        $this->item($items, 'establecimiento', 'Establecimiento',
            'Registre al menos un establecimiento activo para emitir documentos.',
            'v3/configuraciones/establecimientos', $this->exists('ct_sucursales', $companyId), true, 'fa-store');

        $hasEmission = false;
        if ($companyId !== '' && Schema::hasTable('ct_caja') && Schema::hasTable('ct_sucursales')) {
            $hasEmission = DB::table('ct_caja as emission')
                ->join('ct_sucursales as branch', 'branch.id', '=', 'emission.id_sucursal')
                ->where('emission.id_empresa', $companyId)->where('branch.id_empresa', $companyId)
                ->where('emission.estado', 1)->where('branch.estado', 1)->exists();
        }
        $this->item($items, 'punto_emision', 'Punto de emisión',
            'Configure un punto de emisión activo dentro del establecimiento.',
            'v3/configuraciones/establecimientos', $hasEmission, true, 'fa-cash-register');

        $tax = $companyId !== '' ? DeEmpresas::estadoTributario($companyId) : array('bloqueada' => true);
        $this->item($items, 'tributaria', 'Configuración tributaria',
            isset($tax['mensaje']) ? $tax['mensaje'] : 'Complete la configuración tributaria de la empresa.',
            'v3/configuraciones/empresas', empty($tax['bloqueada']), true, 'fa-file-invoice');

        $this->item($items, 'plan_cuentas', 'Plan de cuentas',
            'Importe o copie el plan de cuentas para registrar la contabilidad de las facturas.',
            'v3/contabilidad/plandecuentas', $this->exists('plan_cuentas_empresa', $companyId), true, 'fa-book');

        $this->item($items, 'bodega', 'Bodega principal',
            'Agregue una bodega activa si facturará productos con control de inventario.',
            'v3/configuraciones/bodegas', $this->exists('ct_bodegas', $companyId), false, 'fa-warehouse');

        $pending = array_values(array_filter($items, function ($item) { return !$item['complete']; }));
        $blocking = array_values(array_filter($pending, function ($item) { return $item['blocking']; }));
        $complete = count($items) - count($pending);
        return array(
            'items' => $items, 'pending' => $pending, 'blocking' => $blocking,
            'can_invoice' => count($blocking) === 0,
            'completed' => $complete, 'total' => count($items),
            'percent' => count($items) ? (int) round(($complete / count($items)) * 100) : 100,
        );
    }

    private function exists($table, $companyId)
    {
        return $companyId !== '' && Schema::hasTable($table)
            && Schema::hasColumn($table, 'id_empresa')
            && DB::table($table)->where('id_empresa', $companyId)->where('estado', 1)->exists();
    }

    private function item(array &$items, $key, $title, $description, $path, $complete, $blocking, $icon)
    {
        $items[] = array(
            'key' => $key, 'title' => $title, 'description' => $description,
            'url' => url($path), 'complete' => (bool) $complete,
            'blocking' => (bool) $blocking, 'icon' => $icon,
        );
    }
}
