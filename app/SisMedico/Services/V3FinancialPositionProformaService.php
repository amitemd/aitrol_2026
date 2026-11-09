<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\PorcentajeRenta;

class V3FinancialPositionProformaService
{
    private $reports;

    public function __construct(V3FinancialReportService $reports)
    {
        $this->reports = $reports;
    }

    /**
     * Construye el balance con ajustes de cierre exclusivamente proforma.
     * No inserta ni actualiza asientos, cuentas o saldos contables.
     */
    public function build($companyId, $from, $asOf)
    {
        $from = Carbon::parse($from)->toDateString();
        $asOf = Carbon::parse($asOf)->toDateString();
        $position = $this->reports->financialPosition($companyId, $asOf);
        $statement = $this->reports->incomeStatement($companyId, $from, $asOf);

        $recognizedParticipation = $this->matchingExpense($statement, array('PARTICIP', 'TRABAJ'));
        $recognizedTax = $this->matchingExpense($statement, array('IMPUESTO', 'RENTA'));
        $profitBeforeAdjustments = round((float) $statement['resultado'] + $recognizedParticipation + $recognizedTax, 2);
        $participation = $profitBeforeAdjustments > 0 ? round($profitBeforeAdjustments * 0.15, 2) : 0.0;
        $nonDeductible = $this->nonDeductibleExpenses($companyId, $from, $asOf);
        $taxableBase = max(round($profitBeforeAdjustments - $participation + $nonDeductible, 2), 0);
        $taxConfig = DB::table('ct_porcentaje_renta')->where('id_empresa', $companyId)
            ->where('anio', date('Y', strtotime($from)))->first();
        $incomeTax = $this->incomeTax($taxConfig, $from, $asOf, $profitBeforeAdjustments, $participation,
            (float) $statement['categorias']['INGRESO'], $taxableBase);

        $participationAdjustment = round($participation - $recognizedParticipation, 2);
        $taxAdjustment = round($incomeTax - $recognizedTax, 2);
        $warnings = array();
        if (!$taxConfig && $profitBeforeAdjustments > 0) {
            $warnings[] = 'No existe configuración de impuesto a la renta para '.date('Y', strtotime($from)).'; el impuesto proforma se presenta en cero.';
        }

        $adjustments = $this->closingEntries($companyId, $statement);
        $this->applyAdjustment($position, $companyId, 'Participación de trabajadores', $participationAdjustment,
            array('PARTICIP', 'TRABAJ'), array('PARTICIP', 'TRABAJ'), $adjustments, $warnings);
        $this->applyAdjustment($position, $companyId, 'Impuesto a la renta', $taxAdjustment,
            array('IMPUESTO', 'RENTA'), array('IMPUESTO', 'RENTA', 'PAGAR'), $adjustments, $warnings);

        $position['diferencia'] = round((float) $position['categorias']['ACTIVO']
            - (float) $position['categorias']['PASIVO'] - (float) $position['categorias']['PATRIMONIO'], 2);
        $position['cuadrado'] = abs($position['diferencia']) <= V3FinancialReportService::TOLERANCE;
        $position['proforma'] = array(
            'desde' => $from,
            'hasta' => $asOf,
            'utilidad_antes_ajustes' => $profitBeforeAdjustments,
            'participacion_trabajadores' => $participation,
            'participacion_reconocida' => $recognizedParticipation,
            'base_gravable' => $taxableBase,
            'gastos_no_deducibles' => $nonDeductible,
            'impuesto_renta' => $incomeTax,
            'impuesto_reconocido' => $recognizedTax,
            'resultado_neto' => round($profitBeforeAdjustments - $participation - $incomeTax, 2),
            'tarifa_renta' => $taxConfig ? (float) $taxConfig->porcentaje : null,
            'regimen' => $taxConfig ? (int) $taxConfig->regimen_especial : null,
            'ajustes' => $adjustments,
            'advertencias' => $warnings,
            'persistido' => false,
        );
        return $this->withAccountGroups($companyId, $position);
    }

    /**
     * Agrega únicamente la jerarquía visual del plan de cuentas a una
     * posición financiera canónica. No calcula ajustes ni modifica datos.
     */
    public function withAccountGroups($companyId, array $position)
    {
        $chart = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)
            ->orderBy('plan')->get(array('plan', 'nombre', 'id_padre', 'estado', 'categoria_niif'));
        $position['grupos'] = array();
        foreach (array('ACTIVO', 'PASIVO', 'PATRIMONIO') as $category) {
            $details = isset($position['detalle'][$category]) && is_array($position['detalle'][$category])
                ? $position['detalle'][$category] : array();
            $position['grupos'][$category] = $this->groupAccountDetails($chart, $details, $category);
        }

        return $position;
    }

    /**
     * Agrupa cada saldo una sola vez bajo su cuenta de grupo mas cercana.
     * El subtotal se calcula exclusivamente desde los detalles presentados.
     */
    public function groupAccountDetails($accounts, array $details, $category = null)
    {
        $byCode = array();
        $nodes = array();
        foreach ($accounts as $account) {
            $code = trim((string) $this->field($account, 'plan'));
            if ($code === '') {
                continue;
            }
            $byCode[$code] = array(
                'plan' => $code,
                'nombre' => (string) $this->field($account, 'nombre'),
                'id_padre' => trim((string) $this->field($account, 'id_padre')),
                'estado' => (int) $this->field($account, 'estado'),
                'categoria_niif' => strtoupper(trim((string) $this->field($account, 'categoria_niif'))),
            );
            if ($byCode[$code]['estado'] === 1) {
                $nodes[$code] = array(
                    'plan' => $code,
                    'nombre' => $byCode[$code]['nombre'],
                    'id_padre' => $byCode[$code]['id_padre'],
                    'detalles' => array(),
                );
            }
        }

        $rootCodes = array('ACTIVO' => '1', 'PASIVO' => '2', 'PATRIMONIO' => '3');
        $rootCode = isset($rootCodes[$category]) ? $rootCodes[$category] : null;
        $used = array();
        foreach ($details as $detail) {
            $code = trim((string) (isset($detail['plan']) ? $detail['plan'] : ''));
            $group = $this->nearestGroup($code, $byCode);
            if ($group) {
                $key = $group['plan'];
            } elseif ($rootCode !== null && isset($byCode[$rootCode])) {
                $key = $rootCode;
                $group = $byCode[$rootCode];
            } else {
                $key = '__OTRAS_CUENTAS__';
                $group = array('plan' => '', 'nombre' => 'Otras cuentas y resultados', 'id_padre' => '');
            }
            if (!isset($nodes[$key])) {
                $nodes[$key] = array(
                    'plan' => $group['plan'],
                    'nombre' => $group['nombre'],
                    'id_padre' => isset($group['id_padre']) ? $group['id_padre'] : '',
                    'detalles' => array(),
                );
            }

            $value = round((float) $detail['valor'], 2);
            $row = $detail;
            $row['valor'] = $value;
            $row['operacion'] = $value < 0 ? 'RESTA' : 'SUMA';
            if ($code !== '' && $code === $group['plan'] && isset($byCode[$code]) && $byCode[$code]['estado'] === 1) {
                $row['nombre'] = 'Movimiento directo · '.$row['nombre'];
            }
            $nodes[$key]['detalles'][] = $row;
            $used[$key] = true;
        }

        $relevant = $used;
        foreach (array_keys($used) as $key) {
            $cursor = $key;
            $visited = array();
            while (isset($nodes[$cursor]) && $nodes[$cursor]['id_padre'] !== '' && !isset($visited[$cursor])) {
                $visited[$cursor] = true;
                $cursor = $nodes[$cursor]['id_padre'];
                if (isset($nodes[$cursor])) {
                    $relevant[$cursor] = true;
                }
            }
        }
        // Las clasificaciones principales deben verse aunque no tengan saldo:
        // Activo/Pasivo corriente y no corriente, y los grupos principales de patrimonio.
        if ($rootCode !== null && isset($nodes[$rootCode])) {
            $relevant[$rootCode] = true;
            foreach ($nodes as $key => $node) {
                if ($node['id_padre'] === $rootCode) {
                    $relevant[$key] = true;
                }
            }
        }

        $children = array();
        $roots = array();
        foreach ($relevant as $key => $present) {
            if (!isset($nodes[$key])) {
                continue;
            }
            $parent = $nodes[$key]['id_padre'];
            if ($parent !== '' && isset($relevant[$parent]) && isset($nodes[$parent])) {
                if (!isset($children[$parent])) $children[$parent] = array();
                $children[$parent][] = $key;
            } else {
                $roots[] = $key;
            }
        }
        $sortCodes = function (&$codes) use ($nodes) {
            usort($codes, function ($left, $right) use ($nodes) {
                if ($nodes[$left]['plan'] === '') return 1;
                if ($nodes[$right]['plan'] === '') return -1;
                return strnatcasecmp($nodes[$left]['plan'], $nodes[$right]['plan']);
            });
        };
        $sortCodes($roots);
        foreach ($children as &$childCodes) $sortCodes($childCodes);
        unset($childCodes);

        $buildNode = function ($key, $level) use (&$buildNode, $nodes, $children) {
            $node = $nodes[$key];
            usort($node['detalles'], function ($left, $right) {
                if ((string) $left['plan'] === '') return 1;
                if ((string) $right['plan'] === '') return -1;
                return strnatcasecmp((string) $left['plan'], (string) $right['plan']);
            });
            $node['nivel'] = $level;
            $node['subgrupos'] = array();
            $node['valor'] = 0.0;
            $node['cantidad_detalles'] = count($node['detalles']);
            foreach ($node['detalles'] as $detail) {
                $node['valor'] = round($node['valor'] + (float) $detail['valor'], 2);
            }
            if (isset($children[$key])) {
                foreach ($children[$key] as $childKey) {
                    $child = $buildNode($childKey, $level + 1);
                    $node['subgrupos'][] = $child;
                    $node['valor'] = round($node['valor'] + (float) $child['valor'], 2);
                    $node['cantidad_detalles'] += (int) $child['cantidad_detalles'];
                }
            }
            $calculated = 0.0;
            foreach ($node['detalles'] as $detail) $calculated = round($calculated + (float) $detail['valor'], 2);
            foreach ($node['subgrupos'] as $child) $calculated = round($calculated + (float) $child['valor'], 2);
            $node['verificado'] = abs($calculated - (float) $node['valor']) <= V3FinancialReportService::TOLERANCE;
            return $node;
        };

        $groups = array();
        foreach ($roots as $key) $groups[] = $buildNode($key, 0);

        return $groups;
    }

    private function nearestGroup($code, array $byCode)
    {
        if ($code === '' || !isset($byCode[$code])) {
            return null;
        }
        $current = $byCode[$code];
        $visited = array();
        while ($current) {
            if (isset($visited[$current['plan']])) {
                return null;
            }
            $visited[$current['plan']] = true;
            if ((int) $current['estado'] === 1) {
                return $current;
            }
            $parent = $current['id_padre'];
            $current = $parent !== '' && isset($byCode[$parent]) ? $byCode[$parent] : null;
        }
        return null;
    }

    private function field($row, $field)
    {
        if (is_array($row)) {
            return isset($row[$field]) ? $row[$field] : null;
        }
        return isset($row->{$field}) ? $row->{$field} : null;
    }

    private function closingEntries($companyId, array $statement)
    {
        $entries = array();
        $resultAccount = $this->account($companyId, 'PATRIMONIO', array('RESULTADO', 'PERIODO'));
        if (!$resultAccount) $resultAccount = $this->account($companyId, 'PATRIMONIO', array('RESULTADO'));
        if (!$resultAccount) $resultAccount = $this->account($companyId, 'PATRIMONIO', array('UTILIDAD'));
        $resultLabel = $resultAccount ? $resultAccount->plan.' · '.$resultAccount->nombre : 'Resultado del periodo · proforma';
        foreach ($statement['detalle']['INGRESO'] as $line) {
            $this->appendClosingEntry($entries, 'Cierre virtual de ingresos', $line, $resultAccount, true, $resultLabel);
        }
        foreach (array('COSTO', 'GASTO') as $category) {
            foreach ($statement['detalle'][$category] as $line) {
                $this->appendClosingEntry($entries, 'Cierre virtual de costos y gastos', $line, $resultAccount, false, $resultLabel);
            }
        }
        return $entries;
    }

    private function appendClosingEntry(array &$entries, $concept, array $line, $resultAccount, $isIncome, $resultLabel)
    {
        $amount = round(abs((float) $line['valor']), 2);
        if ($amount <= V3FinancialReportService::TOLERANCE) return;
        $accountLabel = $line['plan'].' · '.$line['nombre'];
        $normalDirection = (float) $line['valor'] >= 0;
        $debitIsSource = $isIncome === $normalDirection;
        $entries[] = array(
            'concepto' => $concept,
            'debe' => $debitIsSource ? $accountLabel : $resultLabel,
            'haber' => $debitIsSource ? $resultLabel : $accountLabel,
            'debe_id_plan' => $debitIsSource ? $line['id_plan'] : ($resultAccount ? $resultAccount->id_plan : null),
            'haber_id_plan' => $debitIsSource ? ($resultAccount ? $resultAccount->id_plan : null) : $line['id_plan'],
            'valor' => $amount,
            'tipo' => 'Cierre proforma',
        );
    }

    private function matchingExpense(array $statement, array $terms)
    {
        $total = 0.0;
        foreach (array('COSTO', 'GASTO') as $category) {
            foreach ($statement['detalle'][$category] as $line) {
                if ($this->matches($line['nombre'], $terms)) {
                    $total += (float) $line['valor'];
                }
            }
        }
        return round($total, 2);
    }

    private function nonDeductibleExpenses($companyId, $from, $to)
    {
        $total = 0.0;
        foreach ($this->reports->ledger($companyId, $from, $to) as $line) {
            $category = strtoupper((string) $line->categoria_niif);
            if ($category === '') {
                $category = substr((string) $line->plan, 0, 1) === '6' ? 'GASTO' : '';
            }
            $isExpense = $category === 'GASTO' || ($category === 'COSTO' && strpos((string) $line->plan, '5.1') !== 0);
            if ($isExpense && (string) $line->aparece_sri === '0') {
                $total += (float) $line->debe - (float) $line->haber;
            }
        }
        return max(round($total, 2), 0);
    }

    private function incomeTax($config, $from, $to, $profit, $participation, $income, $taxableBase)
    {
        if (!$config || $profit <= 0) {
            return 0.0;
        }
        if ((int) $config->regimen_especial === 1) {
            return round(max($taxableBase, 0) * ((float) $config->porcentaje / 100), 2);
        }
        $calculated = PorcentajeRenta::impuestoCausado($config, $from, $to, $profit, $participation, $income, $taxableBase);
        return is_numeric($calculated) ? round(max((float) $calculated, 0), 2) : 0.0;
    }

    private function applyAdjustment(array &$position, $companyId, $concept, $amount, array $expenseTerms,
        array $liabilityTerms, array &$adjustments, array &$warnings)
    {
        if (abs($amount) <= V3FinancialReportService::TOLERANCE) {
            return;
        }
        $expense = $this->account($companyId, 'GASTO', $expenseTerms);
        $liability = $this->account($companyId, 'PASIVO', $liabilityTerms);
        if (!$liability && $concept === 'Impuesto a la renta') {
            $liability = $this->account($companyId, 'PASIVO', array('IMPUESTO', 'SRI', 'PAGAR'));
        }
        if (!$liability && $concept === 'Participación de trabajadores') {
            $liability = $this->account($companyId, 'PASIVO', array('PARTICIP', '15'));
        }
        if (!$expense) {
            $warnings[] = 'No se encontró una cuenta de gasto configurada para '.$concept.'.';
        }
        if (!$liability) {
            $warnings[] = 'No se encontró una cuenta por pagar configurada para '.$concept.'.';
        }

        $position['categorias']['PASIVO'] = round((float) $position['categorias']['PASIVO'] + $amount, 2);
        $position['categorias']['PATRIMONIO'] = round((float) $position['categorias']['PATRIMONIO'] - $amount, 2);
        $position['detalle']['PASIVO'][] = array(
            'plan' => $liability ? $liability->plan : 'PROFORMA',
            'nombre' => $concept.' por pagar · ajuste proforma',
            'valor' => $amount,
            'proforma' => true,
        );
        $this->adjustCurrentResult($position['detalle']['PATRIMONIO'], $amount);

        $debit = $expense ? $expense->plan.' · '.$expense->nombre : 'Cuenta de gasto pendiente de configurar';
        $credit = $liability ? $liability->plan.' · '.$liability->nombre : 'Cuenta por pagar pendiente de configurar';
        $debitId = $expense ? $expense->id_plan : null;
        $creditId = $liability ? $liability->id_plan : null;
        if ($amount < 0) {
            $swap = $debit;
            $debit = $credit;
            $credit = $swap;
            $swap = $debitId;
            $debitId = $creditId;
            $creditId = $swap;
        }
        $adjustments[] = array(
            'concepto' => $concept,
            'debe' => $debit,
            'haber' => $credit,
            'debe_id_plan' => $debitId,
            'haber_id_plan' => $creditId,
            'valor' => abs($amount),
            'tipo' => $amount < 0 ? 'Reversión proforma' : 'Ajuste proforma',
        );
    }

    private function adjustCurrentResult(array &$rows, $amount)
    {
        foreach ($rows as &$row) {
            if ($this->matches($row['nombre'], array('RESULTADO', 'FECHA'))) {
                $row['valor'] = round((float) $row['valor'] - $amount, 2);
                $row['proforma'] = true;
                return;
            }
        }
        $rows[] = array('plan' => '', 'nombre' => 'Resultado neto proforma del periodo', 'valor' => -$amount, 'proforma' => true);
    }

    private function account($companyId, $category, array $terms)
    {
        $query = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 2)
            ->where(function ($where) use ($category) {
                if ($category === 'PASIVO') {
                    $where->where('categoria_niif', 'PASIVO')->orWhereRaw('LEFT(TRIM(plan),1) = ?', array('2'));
                } elseif ($category === 'PATRIMONIO') {
                    $where->where('categoria_niif', 'PATRIMONIO')->orWhereRaw('LEFT(TRIM(plan),1) = ?', array('3'));
                } else {
                    $where->whereIn('categoria_niif', array('COSTO', 'GASTO'))
                        ->orWhereRaw('LEFT(TRIM(plan),1) IN (?,?)', array('5', '6'));
                }
            });
        foreach ($terms as $term) {
            $query->whereRaw('UPPER(COALESCE(nombre,?)) LIKE ?', array('', '%'.$this->normalize($term).'%'));
        }
        return $query->orderBy('plan')->first(array('id_plan', 'plan', 'nombre'));
    }

    private function matches($value, array $terms)
    {
        $value = $this->normalize($value);
        foreach ($terms as $term) {
            if (strpos($value, $this->normalize($term)) === false) {
                return false;
            }
        }
        return true;
    }

    private function normalize($value)
    {
        return strtoupper(strtr((string) $value, array('á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'ñ' => 'n', 'Ñ' => 'N')));
    }
}
