<?php

namespace Sis_medico\Services;

use Illuminate\Support\Str;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Medicina;
use Sis_medico\Producto;
use Sis_medico\Producto_Medicina;

class MedicationInventoryProductService
{
    /**
     * Relaciona el medicamento prescrito con inventario solo cuando la
     * correspondencia es inequívoca (código operacional o nombre completo).
     */
    public function resolve($medicineId, $companyId = null, $warehouseId = null, $persist = false, $actorId = null, $ip = null)
    {
        $link = Producto_Medicina::where('id_medicina', $medicineId)->first();
        if ($link) {
            $linkedProduct = Producto::where('id', $link->id_producto)->where('estado', 1)->first();
            if ($linkedProduct) return $linkedProduct;
        }

        $medicine = Medicina::where('id', $medicineId)->where('estado', 1)->first();
        if (!$medicine) return null;

        $product = null;
        $operationalCode = trim((string) $medicine->consep);
        if ($operationalCode !== '') {
            $product = Producto::where('codigo', $operationalCode)->where('estado', 1)->first();
        }

        if (!$product) {
            $query = Producto::where('estado', 1);
            if ($companyId !== null && $companyId !== '') {
                $productIds = InvInventarioSerie::where('id_empresa', $companyId)
                    ->where('estado', '<>', 0)
                    ->when($warehouseId, function ($inventory) use ($warehouseId) {
                        return $inventory->where('id_bodega', $warehouseId);
                    })->pluck('id_producto')->filter()->unique();
                if ($productIds->isNotEmpty()) $query->whereIn('id', $productIds);
            }

            $medicineTokens = $this->tokens(implode(' ', array(
                $medicine->nombre, $medicine->concentracion, $medicine->presentacion,
            )));
            $matches = $query->get()->filter(function ($candidate) use ($medicineTokens) {
                $productTokens = $this->tokens($candidate->nombre.' '.$candidate->descripcion.' '.$candidate->codigo);
                return !empty($medicineTokens) && empty(array_diff($medicineTokens, $productTokens));
            })->values();
            if ($matches->count() === 1) $product = $matches->first();
        }

        if ($product && $persist) {
            $audit = array(
                    'id_producto' => $product->id,
                    'id_usuariomod' => $actorId,
                    'ip_modificacion' => $ip,
                );
            if ($link) {
                $link->update($audit);
            } else {
                Producto_Medicina::create(array_merge($audit, array(
                    'id_medicina' => $medicine->id,
                    'id_usuariocrea' => $actorId,
                    'ip_creacion' => $ip,
                )));
            }
        }

        return $product;
    }

    private function tokens($value)
    {
        $normalized = strtoupper(Str::ascii((string) $value));
        $tokens = preg_split('/[^A-Z0-9]+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $ignored = array('DE', 'DEL', 'LA', 'EL', 'EN', 'Y', 'TABLETA', 'TABLETAS', 'CAPSULA', 'CAPSULAS');
        return array_values(array_unique(array_filter($tokens, function ($token) use ($ignored) {
            return strlen($token) > 1 && !in_array($token, $ignored, true);
        })));
    }
}
