<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Sis_medico\Support\RequestSchema as Schema;

class V3ManualJournalEntryService
{
    private $posting;

    public function __construct(V3AccountingPostingService $posting)
    {
        $this->posting = $posting;
    }

    public function create($companyId, $userId, array $payload, $ipAddress)
    {
        $type = strtoupper(trim((string) (isset($payload['tipo_asiento']) ? $payload['tipo_asiento'] : '')));
        if (!in_array($type, array('APERTURA', 'AJUSTE', 'CIERRE'), true)) {
            throw new InvalidArgumentException('Seleccione un tipo de asiento válido.');
        }
        $date = $this->date(isset($payload['fecha_asiento']) ? $payload['fecha_asiento'] : null);
        $concept = trim((string) (isset($payload['observacion']) ? $payload['observacion'] : ''));
        if ($concept === '' || mb_strlen($concept) < 5 || mb_strlen($concept) > 220) {
            throw new InvalidArgumentException('La descripción debe contener entre 5 y 220 caracteres.');
        }
        $reference = trim((string) (isset($payload['referencia']) ? $payload['referencia'] : ''));
        if (mb_strlen($reference) > 50) throw new InvalidArgumentException('La referencia no puede superar 50 caracteres.');
        $token = trim((string) (isset($payload['operation_token']) ? $payload['operation_token'] : ''));
        if (!preg_match('/^[a-f0-9]{32,64}$/', $token)) {
            throw new InvalidArgumentException('La identidad de la operación no es válida. Recargue el formulario.');
        }

        $accountIds = isset($payload['cuenta']) && is_array($payload['cuenta']) ? $payload['cuenta'] : array();
        $descriptions = isset($payload['descripcion']) && is_array($payload['descripcion']) ? $payload['descripcion'] : array();
        $debits = isset($payload['debe']) && is_array($payload['debe']) ? $payload['debe'] : array();
        $credits = isset($payload['haber']) && is_array($payload['haber']) ? $payload['haber'] : array();
        $costCenterIds = isset($payload['centro_costo']) && is_array($payload['centro_costo']) ? $payload['centro_costo'] : array();
        $lineCount = max(count($accountIds), count($debits), count($credits));
        if ($lineCount > 200) throw new InvalidArgumentException('El asiento no puede contener más de 200 líneas.');
        $normalized = array();
        $requestedAccounts = array();
        $requestedCostCenters = array();
        $totalDebitCents = 0;
        $totalCreditCents = 0;
        for ($index = 0; $index < $lineCount; $index++) {
            $accountId = trim((string) (isset($accountIds[$index]) ? $accountIds[$index] : ''));
            $debitCents = $this->cents(isset($debits[$index]) ? $debits[$index] : 0);
            $creditCents = $this->cents(isset($credits[$index]) ? $credits[$index] : 0);
            if ($accountId === '' && $debitCents === 0 && $creditCents === 0) continue;
            if ($accountId === '') throw new InvalidArgumentException('Seleccione la cuenta de todas las líneas con valor.');
            if (($debitCents > 0 && $creditCents > 0) || ($debitCents === 0 && $creditCents === 0)) {
                throw new InvalidArgumentException('Cada línea debe tener un valor únicamente en Debe o en Haber.');
            }
            $description = trim((string) (isset($descriptions[$index]) ? $descriptions[$index] : ''));
            $costCenterId = trim((string) (isset($costCenterIds[$index]) ? $costCenterIds[$index] : ''));
            if (mb_strlen($description) > 220) throw new InvalidArgumentException('La descripción de una línea supera 220 caracteres.');
            $requestedAccounts[$accountId] = true;
            if ($costCenterId !== '') $requestedCostCenters[$costCenterId] = true;
            $normalized[] = array('account' => $accountId, 'description' => $description,
                'cost_center' => $costCenterId, 'debit_cents' => $debitCents, 'credit_cents' => $creditCents);
            $totalDebitCents += $debitCents;
            $totalCreditCents += $creditCents;
        }
        if (count($normalized) < 2) throw new InvalidArgumentException('El asiento debe contener al menos dos líneas con valor.');
        if ($totalDebitCents <= 0 || $totalDebitCents !== $totalCreditCents) {
            throw new InvalidArgumentException('El asiento no cuadra. Debe y Haber deben ser iguales y mayores a cero.');
        }

        $validAccounts = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)
            ->where('estado', 2)->whereIn('id_plan', array_keys($requestedAccounts))
            ->get(array('id_plan'))->keyBy(function ($account) { return (string) $account->id_plan; });
        if ($validAccounts->count() !== count($requestedAccounts)) {
            throw new InvalidArgumentException('Una o más cuentas no pertenecen al plan de detalle de la empresa.');
        }
        if ($requestedCostCenters) {
            if (!Schema::hasTable('v3_cost_centers') || !Schema::hasColumn('ct_asientos_detalle', 'id_centro_costo')) {
                throw new InvalidArgumentException('Falta ejecutar la migración del módulo de centros de costos.');
            }
            $validCostCenters = DB::table('v3_cost_centers')->where('id_empresa', $companyId)->where('estado', 1)
                ->whereIn('id', array_keys($requestedCostCenters))->count();
            if ($validCostCenters !== count($requestedCostCenters)) {
                throw new InvalidArgumentException('Uno o más centros de costos no pertenecen a la empresa activa.');
            }
        }

        $operationTime = trim((string) (isset($payload['operation_time']) ? $payload['operation_time'] : ''));
        if ($type === 'AJUSTE' && !$this->validTime($operationTime)) {
            throw new InvalidArgumentException('La hora de la operación no es válida. Recargue el formulario.');
        }
        $time = $type === 'APERTURA' ? '00:00:00' : ($type === 'CIERRE' ? '23:59:59' : $operationTime);
        $timestamp = $date.' '.$time;
        $lines = array();
        foreach ($normalized as $line) {
            $lines[] = array(
                'id_plan_cuenta' => $line['account'],
                'descripcion' => $line['description'] !== '' ? $line['description'] : $concept,
                'debe' => round($line['debit_cents'] / 100, 2),
                'haber' => round($line['credit_cents'] / 100, 2),
                'fecha' => $timestamp,
                'ip_creacion' => $ipAddress,
                'ip_modificacion' => $ipAddress,
            );
            if ($line['cost_center'] !== '') $lines[count($lines) - 1]['id_centro_costo'] = (int) $line['cost_center'];
        }
        $sourceId = $type === 'AJUSTE' ? 'AJUSTE:'.$token : $type.':'.substr($date, 0, 4);
        return $this->posting->post(array(
            'id_empresa' => $companyId,
            'fecha_asiento' => $timestamp,
            'observacion' => '[ASIENTO DE '.$type.'] '.$concept,
            'fact_numero' => $reference,
            'valor' => round($totalDebitCents / 100, 2),
            'modulo' => 'ASIENTO_'.$type,
            'aparece_sri' => 0,
            'ip_creacion' => $ipAddress,
            'ip_modificacion' => $ipAddress,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
        ), $lines, array(
            'modulo' => 'CONTABILIDAD_MANUAL',
            'tipo_origen' => 'ASIENTO_'.$type,
            'id_origen' => $sourceId,
        ));
    }

    private function date($value)
    {
        $value = trim((string) $value);
        $date = Carbon::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new InvalidArgumentException('La fecha del asiento no es válida.');
        return $value;
    }

    private function cents($value)
    {
        $value = str_replace(',', '.', trim((string) $value));
        if ($value === '') return 0;
        if (!is_numeric($value) || (float) $value < 0) throw new InvalidArgumentException('Los valores del asiento deben ser números positivos.');
        if ((float) $value > 999999999.99) throw new InvalidArgumentException('Uno de los valores supera el máximo permitido.');
        return (int) round(((float) $value) * 100);
    }

    private function validTime($value)
    {
        if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $value, $parts)) return false;
        return (int) $parts[1] <= 23 && (int) $parts[2] <= 59 && (int) $parts[3] <= 59;
    }
}
