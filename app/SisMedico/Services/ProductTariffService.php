<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Convenio;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Ct_productos;
use Sis_medico\PrecioProducto;
use Sis_medico\Seguro;

class ProductTariffService
{
    /**
     * Resuelve el nivel exclusivamente dentro de la empresa y seguro indicados.
     * Si se proporciona un nivel, también confirma que forme parte del convenio.
     */
    public function resolveLevel($insuranceId, $companyId, $levelId = null)
    {
        if (!$insuranceId || !$companyId) return null;

        $insurance = Seguro::where('id', $insuranceId)
            ->where('id_empresa', $companyId)
            ->where('inactivo', 1)
            ->first();
        if (is_null($insurance) || (int) $insurance->id_seguro_tipos === 3) return null;

        $query = Convenio::where('id_empresa', $companyId)
            ->where('id_seguro', $insuranceId)
            ->where('estado', 1);
        if ($levelId !== null && $levelId !== '') $query->where('id_nivel', $levelId);

        $agreement = $query->orderBy('id')->first();
        return is_null($agreement) ? null : $agreement->id_nivel;
    }

    /**
     * Devuelve siempre un precio trazable. El tarifario gana únicamente cuando
     * coincide producto + seguro + nivel y el producto pertenece a la empresa.
     */
    public function resolve(Ct_productos $product, $insuranceId, $companyId, $levelId = null)
    {
        if ((string) $product->id_empresa !== (string) $companyId) {
            throw new \InvalidArgumentException('El producto no pertenece a la empresa activa.');
        }

        $base = (float) $product->valor_total_paq;
        $source = 'producto';
        if ($base <= 0) {
            $legacy = PrecioProducto::where('id_empresa', $companyId)
                ->where('codigo_producto', $product->codigo)
                ->where('estado', 1)
                ->orderBy('nivel')->orderBy('id')->first();
            $base = is_null($legacy) ? 0 : (float) $legacy->precio;
            $source = is_null($legacy) ? 'sin_precio' : 'precio_producto';
        }

        $insurance = Seguro::where('id', $insuranceId)
            ->where('id_empresa', $companyId)->where('inactivo', 1)->first();
        $resolvedLevel = $this->resolveLevel($insuranceId, $companyId, $levelId);
        $rate = null;
        // Un seguro sin niveles puede tener un precio propio con nivel NULL.
        // Los seguros particulares continúan usando siempre el precio base.
        if (!is_null($insurance) && (int) $insurance->id_seguro_tipos !== 3) {
            $rateQuery = Ct_Productos_Tarifario::where('id_producto', $product->id)
                ->where('id_seguro', $insuranceId)
                ->where('estado', 1);
            if (is_null($resolvedLevel)) {
                $rateQuery->whereNull('nivel');
            } else {
                $rateQuery->where('nivel', $resolvedLevel);
            }
            $rate = $rateQuery->orderByDesc('updated_at')->orderByDesc('id')->first();
        }

        return array(
            'price' => round(is_null($rate) ? $base : (float) $rate->precio_producto, 2),
            'base_price' => round($base, 2),
            'source' => is_null($rate) ? $source : 'tarifario',
            'in_agreement' => is_null($rate) ? 0 : 1,
            'insurance_id' => $insuranceId,
            'level_id' => $resolvedLevel,
            'tariff_id' => is_null($rate) ? null : $rate->id,
        );
    }

    public function levelsByInsurance($companyId)
    {
        $rows = DB::table('convenio as c')->join('nivel as n', 'n.id', '=', 'c.id_nivel')
            ->join('seguros as s', 's.id', '=', 'c.id_seguro')
            ->where('c.id_empresa', $companyId)->where('s.id_empresa', $companyId)
            ->where('c.estado', 1)->where('n.estado', 1)->where('s.inactivo', 1)
            ->select('c.id_seguro', 'n.id', 'n.nombre')->orderBy('n.nombre')->get();

        $result = array();
        foreach ($rows as $row) {
            if (!isset($result[$row->id_seguro])) $result[$row->id_seguro] = array();
            $result[$row->id_seguro][] = array('id' => $row->id, 'nombre' => $row->nombre);
        }
        return $result;
    }
}
