<?php

namespace Sis_medico\Services;

use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\NotaCreditoClientes;
use Sis_medico\v3\contable\Retenciones;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\NotadeCreditoProveedores;

/** Read-only working papers. Never changes documents or submits a declaration. */
class TaxDeclarationPrefill
{
    public function sales($year, $month, $company): array
    {
        $invoices = Ventas::where('id_empresa', $company)->where('estado', '!=', 0)
            ->whereYear('fecha', $year)->whereMonth('fecha', $month)->get();
        $notes = NotaCreditoClientes::where('id_empresa', $company)->where('estado', '!=', 0)
            ->whereYear('fecha', $year)->whereMonth('fecha', $month)->get();
        $connection = (new Ventas)->getConnection();
        $details = $notes->isEmpty() ? collect() : $connection->table('ct_devolucion_productos')
            ->whereIn('id_nota_credito', $notes->pluck('id'))->where('estado', 1)->get()->groupBy('id_nota_credito');
        $sources = $notes->isEmpty() ? collect() : Ventas::where('id_empresa', $company)
            ->whereIn('id', $notes->pluck('id_factura'))->get()->keyBy('id');
        $warnings = [];
        $credits = [];
        $audit = [];
        foreach ($invoices as $invoice) {
            $audit[] = ['documento' => 'Venta #'.$invoice->id, 'fecha' => $invoice->fecha,
                'base0' => (float) $invoice->subtotal_0, 'gravada' => (float) $invoice->subtotal_12,
                'iva' => (float) $invoice->iva_total];
        }
        foreach ($notes as $note) {
            $rows = $details->get($note->id, collect());
            if (!$sources->has($note->id_factura) || $rows->isEmpty()) {
                $warnings[] = "Nota de crédito #{$note->id}: falta factura de la empresa o detalle activo; no se incluyó. Revise antes de declarar.";
                continue;
            }
            $credit = ['subtotal_0' => 0, 'subtotal_12' => 0, 'iva_total' => 0];
            foreach ($rows as $row) {
                $base = round((float) $row->cantidad * (float) $row->precio - (float) $row->descuento, 2);
                $tax = round((float) $row->valor_iva, 2);
                if ($base < 0 || $tax < 0) {
                    $warnings[] = "Nota de crédito #{$note->id}: detalle negativo; no se incluyó.";
                    continue 2;
                }
                $credit[$tax > 0 ? 'subtotal_12' : 'subtotal_0'] += $base;
                $credit['iva_total'] += $tax;
            }
            if (abs($credit['subtotal_0'] + $credit['subtotal_12'] - (float) $note->subtotal) > 0.02) {
                $warnings[] = "Nota de crédito #{$note->id}: cabecera y detalle no coinciden; no se incluyó.";
                continue;
            }
            $credits[] = $credit;
            $audit[] = ['documento' => 'Nota de crédito #'.$note->id, 'fecha' => $note->fecha,
                'base0' => -$credit['subtotal_0'], 'gravada' => -$credit['subtotal_12'], 'iva' => -$credit['iva_total']];
        }
        $purchases = Compras::where('id_empresa', $company)->where('estado', '!=', 0)
            ->whereYear('fecha', $year)->whereMonth('fecha', $month)->get();
        $purchaseNotes = NotadeCreditoProveedores::where('id_empresa', $company)->where('estado', '!=', 0)
            ->whereYear('fecha', $year)->whereMonth('fecha', $month)->get();
        $purchaseResult = self::purchaseFields($purchases, $purchaseNotes);
        return ['ventas' => self::salesFields($invoices, $credits), 'compras' => $purchaseResult['fields'],
            'warnings' => array_merge($warnings, $purchaseResult['warnings']), 'audit' => $audit];
    }

    /** Local ordinary sales; special classifications require accountant review. */
    public static function salesFields($invoices, $credits): array
    {
        $sum = function ($rows, $key) {
            $value = 0;
            foreach ($rows as $row) $value += (float) (is_array($row) ? ($row[$key] ?? 0) : ($row->$key ?? 0));
            return round($value, 2);
        };
        $taxable = $sum($invoices, 'subtotal_12');
        $zero = $sum($invoices, 'subtotal_0');
        $netTaxable = round($taxable - $sum($credits, 'subtotal_12'), 2);
        $netZero = round($zero - $sum($credits, 'subtotal_0'), 2);
        $tax = round($sum($invoices, 'iva_total') - $sum($credits, 'iva_total'), 2);
        return [401 => $taxable, 403 => $zero, 411 => max(0, $netTaxable),
            413 => max(0, $netZero), 421 => max(0, $tax),
            442 => max(0, -$netZero), 443 => max(0, -$netTaxable), 453 => max(0, -$tax),
            409 => $taxable + $zero, 419 => max(0, $netTaxable) + max(0, $netZero),
            429 => max(0, $tax), 482 => max(0, $tax)];
    }

    public function withholdings($year, $month, $company): array
    {
        $rows = Retenciones::join('ct_detalle_retenciones as d', 'd.id_retenciones', '=', 'ct_retenciones.id')
            ->leftJoin('ct_porcentaje_retenciones as p', 'p.id', '=', 'd.id_porcentaje')
            ->where('ct_retenciones.id_empresa', $company)->where('ct_retenciones.estado', '!=', 0)
            ->where('d.estado', 1)->whereIn('d.tipo', ['2', 'RENTA'])
            ->whereYear('ct_retenciones.fecha', $year)->whereMonth('ct_retenciones.fecha', $month)
            ->get(['d.*', 'p.codigo_nombre as catalog_code']);
        return self::withholdingFields($rows);
    }

    public function vatWithholdings($year, $month, $company): array
    {
        $rows = Retenciones::join('ct_detalle_retenciones as d', 'd.id_retenciones', '=', 'ct_retenciones.id')
            ->where('ct_retenciones.id_empresa', $company)->where('ct_retenciones.estado', 1)
            ->where('d.estado', 1)->whereIn('d.tipo', ['1', 'IVA'])
            ->whereYear('ct_retenciones.fecha', $year)->whereMonth('ct_retenciones.fecha', $month)
            ->get(['d.porcentaje', 'd.totales']);
        $result = self::vatWithholdingFields($rows);
        $result['fields'][609] = round((float) \Sis_medico\v3\contable\Retenciones_Clientes::join(
            'ct_detalle_cliente_retencion as d', 'd.id_cliente_retencion', '=', 'ct_cliente_retencion.id')
            ->where('ct_cliente_retencion.id_empresa', $company)->where('ct_cliente_retencion.estado', 1)
            ->where('d.estado', 1)->whereIn('d.tipo', ['1', 'IVA'])
            ->whereYear('ct_cliente_retencion.fecha', $year)->whereMonth('ct_cliente_retencion.fecha', $month)
            ->sum('d.totales'), 2);
        return $result;
    }

    public static function vatWithholdingFields($rows): array
    {
        $mapping = [10=>721, 20=>723, 30=>725, 50=>727, 70=>729, 100=>731];
        $fields = array_fill_keys(array_values($mapping), 0);
        $warnings = [];
        foreach ($rows as $row) {
            $rate = (float) $row->porcentaje;
            if ($rate === 0.0) continue;
            if (!isset($mapping[(int) $rate]) || $rate !== (float) (int) $rate) {
                $warnings[] = "Retención IVA {$rate}%: no se pudo asignar un casillero del 104; revise el detalle.";
                continue;
            }
            $fields[$mapping[(int) $rate]] += (float) $row->totales;
        }
        $fields = array_map(function ($value) { return round($value, 2); }, $fields);
        $fields[799] = round(array_sum($fields), 2);
        // 800 y 802 requieren revisión; no se inventan compensaciones.
        $fields[801] = $fields[799];
        return ['fields'=>$fields, 'warnings'=>array_values(array_unique($warnings))];
    }

    public static function purchaseFields($purchases, $notes): array
    {
        $gross = []; $credit = []; $taxes = []; $warnings = [];
        foreach ([$purchases, $notes] as $kind => $rows) {
            foreach ($rows as $row) {
                $code = str_pad((string) ($kind ? $row->id_credito_tributario : $row->credito_tributario), 2, '0', STR_PAD_LEFT);
                $document = (int) ($kind ? $row->id_tipo_comprobante : $row->tipo_comprobante);
                // Imported goods/services, reimbursements and mixed-use sustento need separate review.
                $bucket = ['01' => 500, '03' => 501, '06' => 500, '02' => 502, '04' => 502, '07' => 502][$code] ?? null;
                if (!$bucket || !in_array($document, [1, 2, 3, 4], true)) {
                    $warnings[] = "Compra/NC proveedor #{$row->id}: sustento {$code} o tipo {$document} requiere clasificación; no se incluyó.";
                    continue;
                }
                $values = [$bucket => (float) $row->subtotal_12, 507 => (float) $row->subtotal_0];
                $tax = (float) ($kind ? $row->impuesto : $row->iva_total);
                if ($document === 2) $values = [508 => (float) $row->subtotal_12 + (float) $row->subtotal_0 + $tax];
                foreach ($values as $field => $value) {
                    if ($kind) $credit[$field] = ($credit[$field] ?? 0) + $value;
                    else $gross[$field] = ($gross[$field] ?? 0) + $value;
                }
                if ($document !== 2) $taxes[$bucket + 20] = ($taxes[$bucket + 20] ?? 0) + ($kind ? -$tax : $tax);
            }
        }
        $fields = []; $fields[543] = 0; $fields[544] = 0; $fields[554] = 0;
        foreach ([500, 501, 502, 507, 508] as $field) {
            $fields[$field] = round($gross[$field] ?? 0, 2);
            $net = round(($gross[$field] ?? 0) - ($credit[$field] ?? 0), 2);
            $fields[$field + 10] = max(0, $net);
            if ($net < 0) {
                $fields[$field >= 507 ? 543 : 544] += -$net;
                $warnings[] = 'Existen notas de crédito de proveedores por compensar; revise los saldos antes de declarar.';
            }
        }
        foreach ($taxes as $field => $tax) {
            $fields[$field] = max(0, round($tax, 2));
            $fields[554] += max(0, -round($tax, 2));
        }
        return ['fields' => $fields, 'warnings' => array_values(array_unique($warnings))];
    }

    public static function withholdingFields($rows): array
    {
        // Explicit SRI 2026 code mapping, not a percentage-based classification.
        $groups = [
            303 => [353, ['303']], 3030 => [3530, ['303A']],
            304 => [354, ['304','304A','304B','304C','304D','304E']],
            307 => [357, ['307']], 308 => [358, ['308']], 309 => [359, ['309']],
            310 => [360, ['310','332E']], 311 => [361, ['311']], 312 => [362, ['312']],
            3120 => [3620, ['312A']], 3121 => [3621, ['312C']],
            314 => [364, ['314A','314B','314C','314D']], 3140 => [3640, ['3482']],
            319 => [369, ['319']], 320 => [370, ['320']], 322 => [372, ['322']],
            323 => [373, ['323','323A','323B1','323E','323F','323G','323H','323I','323M','323P','323Q','323S']],
            324 => [374, ['324A','324B','324C']],
            343 => [393, ['343','343A']], 344 => [394, ['344A','344B','344C']],
            346 => [396, ['346B']],
        ];
        $fields = []; $warnings = []; $audit = [];
        foreach ($groups as $base => [$tax, $codes]) { $fields[$base] = 0; $fields[$tax] = 0; }
        foreach ($rows as $row) {
            $code = strtoupper(trim((string) ($row->codigo ?: $row->catalog_code)));
            $pair = null;
            foreach ($groups as $base => [$tax, $codes]) {
                if (in_array($code, $codes, true)) { $pair = [$base, $tax]; break; }
            }
            $audit[] = ['codigo' => $code, 'base' => (float) $row->base_imponible,
                'retenido' => (float) $row->totales, 'casillero' => $pair ? $pair[0] : null];
            if (!$pair) {
                $warnings[] = "Retención código {$code}: requiere clasificación manual; no se incluyó en los casilleros automáticos.";
                continue;
            }
            $fields[$pair[0]] = ($fields[$pair[0]] ?? 0) + (float) $row->base_imponible;
            $fields[$pair[1]] = ($fields[$pair[1]] ?? 0) + (float) $row->totales;
        }
        $fields[349] = 0; $fields[399] = 0;
        foreach ($audit as $item) {
            if ($item['casillero'] !== null) {
                $fields[349] += $item['base']; $fields[399] += $item['retenido'];
            }
        }
        return ['fields' => array_map(function ($v) { return round($v, 2); }, $fields),
            'warnings' => array_values(array_unique($warnings)), 'audit' => $audit];
    }
}
