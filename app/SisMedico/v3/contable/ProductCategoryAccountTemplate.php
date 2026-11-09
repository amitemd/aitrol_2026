<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ProductCategoryAccountTemplate extends Model
{
    protected $table = 'v3_product_category_account_templates';
    protected $guarded = array();

    const INCOME_TAXED = 'cuenta_ingreso_gravado';
    const INCOME_ZERO = 'cuenta_ingreso_cero';
    const COST = 'cuenta_costo';
    const EXPENSE = 'cuenta_gasto';
    const INVENTORY = 'cuenta_inventario';
    const RETURNS = 'cuenta_devolucion';

    /**
     * Devuelve las cuentas de ingreso agrupadas por cuenta contable. Una
     * categoria sin plantilla conserva temporalmente la cuenta global.
     */
    public static function revenueLines(array $input, $companyId)
    {
        $encodedProducts = isset($input['producto']) ? (array) $input['producto'] : array();
        $codes = array();
        $parsed = array();
        foreach ($encodedProducts as $index => $encoded) {
            $parts = explode('|', (string) $encoded);
            $code = isset($parts[1]) ? trim($parts[1]) : '';
            if ($code !== '') $codes[] = $code;
            $parsed[$index] = array('code' => $code, 'taxed' => isset($parts[3]) && (int) $parts[3] === 1);
        }

        $products = DB::table('ct_productos')->where('id_empresa', $companyId)
            ->whereIn('codigo', array_values(array_unique($codes)))
            ->get(array('codigo', 'grupo'))->keyBy('codigo');
        $templates = Schema::hasTable('v3_product_category_account_templates')
            ? static::where('id_empresa', $companyId)->where('estado', 1)->get()->keyBy(function ($row) {
                return (string) $row->categoria;
            }) : collect();
        $accounts = array();

        foreach ($parsed as $index => $item) {
            $product = $products->get($item['code']);
            $category = $product ? trim((string) $product->grupo) : '';
            $field = $item['taxed'] ? self::INCOME_TAXED : self::INCOME_ZERO;
            $fallback = $item['taxed'] ? Globales::VENTA_INGRESOS_GRAVADOS : Globales::VENTA_INGRESOS_0;
            $template = $category !== '' ? $templates->get($category) : null;
            $accountId = $template ? trim((string) $template->{$field}) : '';
            $account = $accountId !== '' ? static::account($companyId, $accountId) : Globales::cuentaVenta($fallback, $companyId);
            if (!$account) {
                $label = static::categoryLabel($category);
                throw new \Exception('CONFIG_CONTABLE: Configure la cuenta de ingresos '.($item['taxed'] ? 'gravados' : 'tarifa 0%').' para la categoria '.$label.'.');
            }

            $net = isset($input['total'][$index]) ? (float) $input['total'][$index] : 0;
            $discount = isset($input['descuento'][$index]) ? (float) $input['descuento'][$index] : 0;
            $key = (string) $account->id_plan;
            if (!isset($accounts[$key])) $accounts[$key] = array('account' => $account, 'amount' => 0);
            $accounts[$key]['amount'] += $net + $discount;
        }

        foreach ($accounts as &$line) $line['amount'] = round($line['amount'], 4);
        unset($line);
        return array_values($accounts);
    }

    public static function account($companyId, $accountId)
    {
        return DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', $companyId)
            ->where(function ($query) use ($accountId) {
                $query->where('pe.id_plan', $accountId)->orWhere('pe.plan', $accountId);
            })
            ->where('pe.estado', 2)
            ->first(array('pe.id_plan', 'pe.plan', 'p.nombre'));
    }

    public static function costLines(array $reservation, $total, $companyId)
    {
        $templates = Schema::hasTable('v3_product_category_account_templates')
            ? static::where('id_empresa', $companyId)->where('estado', 1)->get()->keyBy(function ($row) { return (string) $row->categoria; })
            : collect();
        $categories = array(); $rawTotal = 0;
        foreach ($reservation as $group) {
            $category = trim((string) $group['product']->grupo);
            $amount = round((float) $group['legacy_unit_cost'] * (float) $group['quantity'], 4);
            if (!isset($categories[$category])) $categories[$category] = 0;
            $categories[$category] += $amount; $rawTotal += $amount;
        }
        if (!$categories) $categories[''] = (float) $total;

        $result = array(); $allocated = 0; $keys = array_keys($categories); $last = end($keys);
        foreach ($categories as $category => $rawAmount) {
            $amount = $category === $last ? round((float) $total - $allocated, 4)
                : round($rawTotal > 0 ? (float) $total * $rawAmount / $rawTotal : 0, 4);
            $allocated += $amount;
            $template = $category !== '' ? $templates->get($category) : null;
            foreach (array(self::COST => Globales::VENTA_COSTO, self::INVENTORY => Globales::VENTA_INVENTARIO) as $field => $fallback) {
                $accountId = $template ? trim((string) $template->{$field}) : '';
                $account = $accountId !== '' ? static::account($companyId, $accountId) : Globales::cuentaVenta($fallback, $companyId);
                if (!$account) throw new \Exception('CONFIG_CONTABLE: Configure la cuenta de '.($field === self::COST ? 'costo de ventas' : 'inventario').' para la categoria '.static::categoryLabel($category).'.');
                $side = $field === self::COST ? 'debe' : 'haber'; $key = $side.'|'.$account->id_plan;
                if (!isset($result[$key])) $result[$key] = array('account' => $account, 'debe' => 0, 'haber' => 0);
                $result[$key][$side] += $amount;
            }
        }
        foreach ($result as &$line) { $line['debe'] = round($line['debe'], 4); $line['haber'] = round($line['haber'], 4); } unset($line);
        return array_values($result);
    }

    public static function categoryLabel($category)
    {
        $legacy = array('1' => 'Insumos', '2' => 'Medicamentos', '3' => 'Servicios', '4' => 'Procedimientos',
            '5' => 'Otros', '6' => 'Honorarios medicos', '7' => 'Equipos', '8' => 'Honorarios de anestesiologia', '9' => 'Membresias');
        if (isset($legacy[(string) $category])) return $legacy[(string) $category];
        if (Schema::hasTable('new_db_catalogo')) {
            $row = DB::table('new_db_catalogo')->where(function ($query) use ($category) {
                $query->where('id_catalogo', $category)->orWhere('valor', $category);
            })->first(array('catalogo'));
            if ($row && trim((string) $row->catalogo) !== '') return $row->catalogo;
        }
        return trim((string) $category) !== '' ? (string) $category : 'Sin categoria';
    }
}
