<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;
use Sis_medico\v3\contable\LogAsiento;
use Sis_medico\v3\contable\Utilidades;

class V3SupplierAccountCrossingService
{
    private $posting;

    public function __construct(V3AccountingPostingService $posting)
    {
        $this->posting = $posting;
    }

    public function save($request)
    {
        $this->assertSchema();
        $companyId = session('id_empresa');
        $userId = Auth::user()->id;
        $ip = Utilidades::getRealIP();
        $purchaseId = trim((string) $request->input('comprobante_compra'));
        $date = trim((string) $request->input('fechacc'));
        $concept = trim((string) $request->input('conceptop'));
        $branchCode = trim((string) $request->input('sucursalcc'));
        $token = trim((string) $request->input('crossing_token'));
        $total = $this->money($request->input('totalp'));
        $credits = $this->creditLines($request);

        if ($purchaseId === '' || $date === '' || $concept === '' || $branchCode === '') {
            throw new InvalidArgumentException('Complete la factura, fecha, establecimiento y concepto del cruce.');
        }
        if ($token === '' || strlen($token) > 100) throw new InvalidArgumentException('No se recibió la identidad segura del cruce. Recargue la pantalla.');
        if ($total <= 0) throw new InvalidArgumentException('El total del cruce debe ser mayor a cero.');
        if (strlen($concept) > 500) throw new InvalidArgumentException('El concepto no puede superar 500 caracteres.');
        $creditTotal = round(array_sum(array_column($credits, 'amount')), 2);
        if (abs($creditTotal - $total) > 0.01) {
            throw new InvalidArgumentException('El detalle de cuentas debe sumar exactamente '.number_format($total, 2).'.');
        }

        return DB::transaction(function () use ($companyId, $userId, $ip, $purchaseId, $date, $concept, $branchCode, $token, $total, $credits) {
            $completed = DB::table('ct_cruce_cuentas')->where('id_empresa', $companyId)
                ->where('crossing_token', $token)->lockForUpdate()->first();
            if ($completed) return $this->completedCrossing($completed, $purchaseId, $date, $concept, $branchCode, $total, $credits);

            $branch = DB::table('ct_sucursales')->where('id_empresa', $companyId)
                ->where('codigo_sucursal', $branchCode)->where('estado', 1)->first();
            if (!$branch) throw new InvalidArgumentException('El establecimiento no pertenece a la empresa o está inactivo.');

            $purchase = DB::table('ct_compras')->where('id', $purchaseId)
                ->where('id_empresa', $companyId)->where('estado', '<>', 0)->lockForUpdate()->first();
            if (!$purchase) throw new InvalidArgumentException('La factura no existe, está anulada o pertenece a otra empresa.');
            $provider = DB::table('proveedor')->where('id', $purchase->proveedor)->first();
            if (!$provider) throw new RuntimeException('No se encontró el proveedor asociado a la factura.');
            $payableAccount = !empty($provider->id_cuentas) ? $provider->id_cuentas : null;
            $this->assertCompanyAccount($payableAccount, $companyId, 'cuentas por pagar del proveedor');

            $balance = $this->money(isset($purchase->saldo_pendiente) ? $purchase->saldo_pendiente : 0);
            if ($balance <= 0) $balance = $this->money($purchase->valor_contable);
            if ($total - $balance > 0.01) {
                throw new InvalidArgumentException('El cruce de '.number_format($total, 2).' supera el saldo pendiente de '.number_format($balance, 2).'.');
            }

            foreach ($credits as $credit) $this->assertCompanyAccount($credit['account'], $companyId, 'el detalle del cruce');
            $sequence = LogAsiento::getSecuencia(1, 1);
            $now = date('Y-m-d H:i:s');
            $crossingId = DB::table('ct_cruce_cuentas')->insertGetId(array(
                'id_asiento_cabecera' => null, 'fecha' => $date, 'detalle' => $concept,
                'total' => $total, 'id_factura' => $purchase->id, 'id_proveedor' => $purchase->proveedor,
                'secuencia' => $sequence, 'estado' => 1, 'id_empresa' => $companyId,
                'crossing_token' => $token, 'saldo_base' => $balance,
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
            ));

            $detailRows = array();
            foreach ($credits as $credit) {
                $detailRows[] = array(
                    'codigo' => $credit['account'], 'id_comprobante' => $crossingId,
                    'secuencia_factura' => $purchase->numero, 'fecha' => $date, 'estado' => 1,
                    'total_factura' => 0, 'total' => $credit['amount'], 'observaciones' => $credit['description'],
                    'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                    'created_at' => $now, 'updated_at' => $now,
                );
            }
            DB::table('ct_detalle_cruce_cuentas')->insert($detailRows);

            $header = array(
                'sucursal' => $branchCode, 'punto_emision' => '', 'fecha_asiento' => $date,
                'fact_numero' => $sequence, 'id_empresa' => $companyId,
                'observacion' => 'CRUCE DE CUENTAS PROVEEDOR '.$sequence.': '.$concept,
                'valor' => $total, 'modulo' => 'CRUCE_CUENTAS_PROVEEDORES',
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
            );
            $lineBase = array('fecha' => $date, 'establecimiento' => $branchCode,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip);
            $lines = array(array_merge($lineBase, array(
                'id_plan_cuenta' => $payableAccount,
                'descripcion' => 'DISMINUCIÓN CUENTA POR PAGAR - '.$concept,
                'debe' => $total, 'haber' => 0,
            )));
            foreach ($credits as $credit) {
                $lines[] = array_merge($lineBase, array(
                    'id_plan_cuenta' => $credit['account'], 'descripcion' => $credit['description'],
                    'debe' => 0, 'haber' => $credit['amount'],
                ));
            }
            $entryId = $this->posting->post($header, $lines, array(
                'modulo' => 'CRUCE_CUENTAS_PROVEEDORES',
                'tipo_origen' => 'CRUCE_CUENTAS_PROVEEDOR', 'id_origen' => $token,
            ));

            $newBalance = max(0, round($balance - $total, 2));
            DB::table('ct_compras')->where('id', $purchase->id)->where('id_empresa', $companyId)->update(array(
                'saldo_pendiente' => $newBalance, 'valor_contable' => $newBalance,
                'estado_pago' => $newBalance <= 0.01 ? 'PAGADA' : 'PARCIAL',
                'estado' => $newBalance <= 0.01 ? 3 : 2,
                'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now,
            ));
            DB::table('ct_cruce_cuentas')->where('id', $crossingId)->where('id_empresa', $companyId)
                ->update(array('id_asiento_cabecera' => $entryId, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip));

            return array('crossing_id' => (int) $crossingId, 'entry_id' => (int) $entryId,
                'sequence' => $sequence, 'new_balance' => $newBalance);
        }, 3);
    }

    public function cancel($request)
    {
        $this->assertSchema();
        $companyId = session('id_empresa');
        $userId = Auth::user()->id;
        $ip = Utilidades::getRealIP();
        $crossingId = trim((string) $request->input('id'));
        $reason = trim((string) $request->input('motivo'));
        if ($crossingId === '') throw new InvalidArgumentException('No se identificó el cruce que desea anular.');
        if (strlen($reason) < 10 || strlen($reason) > 500) {
            throw new InvalidArgumentException('Ingrese un motivo de anulación entre 10 y 500 caracteres.');
        }

        return DB::transaction(function () use ($companyId, $userId, $ip, $crossingId, $reason) {
            $crossing = DB::table('ct_cruce_cuentas')->where('id', $crossingId)
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$crossing) throw new InvalidArgumentException('El cruce no existe o pertenece a otra empresa.');
            if ((int) $crossing->estado === 0) {
                if (!empty($crossing->id_asiento_reverso)) return array('crossing_id' => (int) $crossing->id,
                    'reversal_entry_id' => (int) $crossing->id_asiento_reverso, 'idempotent' => true);
                throw new RuntimeException('El cruce está anulado pero no tiene asiento de reverso; requiere revisión contable.');
            }
            if (empty($crossing->id_asiento_cabecera)) throw new RuntimeException('El cruce no posee asiento original y no puede anularse automáticamente.');

            $entry = DB::table('ct_asientos_cabecera')->where('id', $crossing->id_asiento_cabecera)
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$entry) throw new RuntimeException('No se encontró el asiento contable original del cruce.');
            $entryLines = DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $entry->id)
                ->where('estado', '<>', 0)->orderBy('id')->get();
            if ($entryLines->isEmpty()) throw new RuntimeException('El asiento original no contiene líneas para generar el reverso.');

            $today = date('Y-m-d');
            $header = array(
                'sucursal' => $entry->sucursal, 'punto_emision' => isset($entry->punto_emision) ? $entry->punto_emision : '',
                'fecha_asiento' => $today, 'fact_numero' => $entry->fact_numero, 'id_empresa' => $companyId,
                'observacion' => 'ANULACIÓN CRUCE DE CUENTAS '.$crossing->secuencia.': '.$reason,
                'valor' => $entry->valor, 'modulo' => 'CRUCE_CUENTAS_PROVEEDORES',
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
            );
            $reversalLines = array();
            foreach ($entryLines as $line) {
                $reversalLines[] = array(
                    'id_plan_cuenta' => $line->id_plan_cuenta, 'descripcion' => 'REVERSO: '.$line->descripcion,
                    'fecha' => $today, 'debe' => $this->money($line->haber), 'haber' => $this->money($line->debe),
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                );
            }
            $reversalEntryId = $this->posting->post($header, $reversalLines, array(
                'modulo' => 'CRUCE_CUENTAS_PROVEEDORES',
                'tipo_origen' => 'ANULACION_CRUCE_CUENTAS_PROVEEDOR', 'id_origen' => (string) $crossing->id,
            ));

            $purchase = DB::table('ct_compras')->where('id', $crossing->id_factura)
                ->where('id_empresa', $companyId)->where('proveedor', $crossing->id_proveedor)->lockForUpdate()->first();
            if (!$purchase) throw new RuntimeException('La factura del cruce ya no pertenece al proveedor o a la empresa.');
            $pending = $this->money(isset($purchase->saldo_pendiente) ? $purchase->saldo_pendiente : 0);
            if (abs($pending - $this->money($purchase->valor_contable)) > 0.01) $pending = $this->money($purchase->valor_contable);
            $restored = round($pending + $this->money($crossing->total), 2);
            $maximum = $this->money($crossing->saldo_base);
            if ($maximum <= 0) $maximum = $this->money($purchase->total_final);
            if ($maximum > 0 && $restored - $maximum > 0.01) {
                throw new RuntimeException('La anulación excedería el saldo que tenía la factura antes del cruce. Revise movimientos posteriores.');
            }
            $isPending = $maximum <= 0 || $restored >= $maximum - 0.01;
            $now = date('Y-m-d H:i:s');
            DB::table('ct_compras')->where('id', $purchase->id)->where('id_empresa', $companyId)->update(array(
                'saldo_pendiente' => $restored, 'valor_contable' => $restored,
                'estado_pago' => $isPending ? 'PENDIENTE' : 'PARCIAL', 'estado' => $isPending ? 1 : 2,
                'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now,
            ));
            DB::table('ct_detalle_cruce_cuentas')->where('id_comprobante', $crossing->id)->where('estado', 1)
                ->update(array('estado' => 0, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now));
            DB::table('ct_cruce_cuentas')->where('id', $crossing->id)->where('id_empresa', $companyId)->update(array(
                'estado' => 0, 'motivo_anulacion' => $reason, 'id_asiento_reverso' => $reversalEntryId,
                'fecha_anulacion' => $now, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip,
            ));

            return array('crossing_id' => (int) $crossing->id,
                'reversal_entry_id' => (int) $reversalEntryId, 'idempotent' => false);
        }, 3);
    }

    private function creditLines($request)
    {
        $accounts = (array) $request->input('cuentap', array());
        $descriptions = (array) $request->input('detallep', array());
        $amounts = (array) $request->input('valorp', array());
        if (!$accounts || count($accounts) !== count($amounts)) throw new InvalidArgumentException('Ingrese al menos una cuenta para el cruce.');
        $lines = array();
        foreach ($accounts as $index => $account) {
            $account = trim((string) $account);
            $amount = $this->money(isset($amounts[$index]) ? $amounts[$index] : 0);
            $description = trim((string) (isset($descriptions[$index]) ? $descriptions[$index] : ''));
            if ($account === '' || $description === '' || $amount <= 0) throw new InvalidArgumentException('Cada línea debe tener cuenta, detalle y valor mayor a cero.');
            if (strlen($description) > 500) throw new InvalidArgumentException('El detalle de una cuenta no puede superar 500 caracteres.');
            $lines[] = array('account' => $account, 'description' => $description, 'amount' => $amount);
        }
        return $lines;
    }

    private function completedCrossing($crossing, $purchaseId, $date, $concept, $branchCode, $total, array $credits)
    {
        if (empty($crossing->id_asiento_cabecera)) throw new RuntimeException('El cruce anterior con esta identidad todavía se encuentra en proceso.');
        $entry = DB::table('ct_asientos_cabecera')->where('id', $crossing->id_asiento_cabecera)
            ->where('id_empresa', $crossing->id_empresa)->first();
        $stored = DB::table('ct_detalle_cruce_cuentas')->where('id_comprobante', $crossing->id)
            ->where('estado', 1)->orderBy('id')->get();
        $same = (string) $crossing->id_factura === (string) $purchaseId
            && (string) $crossing->fecha === (string) $date
            && trim((string) $crossing->detalle) === $concept
            && abs($this->money($crossing->total) - $total) <= 0.01
            && $entry && (string) $entry->sucursal === (string) $branchCode
            && count($stored) === count($credits);
        if ($same) {
            foreach ($credits as $index => $credit) {
                $row = $stored[$index];
                if ((string) $row->codigo !== (string) $credit['account']
                    || trim((string) $row->observaciones) !== $credit['description']
                    || abs($this->money($row->total) - $credit['amount']) > 0.01) {
                    $same = false;
                    break;
                }
            }
        }
        if (!$same) throw new RuntimeException('La identidad segura del cruce ya fue utilizada con información diferente. Recargue la pantalla.');

        $purchase = DB::table('ct_compras')->where('id', $crossing->id_factura)
            ->where('id_empresa', $crossing->id_empresa)->first();
        return array('crossing_id' => (int) $crossing->id, 'entry_id' => (int) $crossing->id_asiento_cabecera,
            'sequence' => $crossing->secuencia,
            'new_balance' => $purchase ? $this->money($purchase->valor_contable) : 0, 'idempotent' => true);
    }

    private function assertCompanyAccount($account, $companyId, $label)
    {
        if (!$account || !DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('id_plan', $account)->exists()) {
            throw new RuntimeException('Configure una cuenta contable válida para '.$label.' antes de guardar.');
        }
    }

    private function assertSchema()
    {
        foreach (array('estado_pago', 'saldo_pendiente') as $column) {
            if (!Schema::hasColumn('ct_compras', $column)) throw new RuntimeException('Falta aplicar la migración del ciclo de compras y pagos. Ejecute php artisan migrate.');
        }
        foreach (array('crossing_token', 'saldo_base', 'motivo_anulacion', 'id_asiento_reverso', 'fecha_anulacion') as $column) {
            if (!Schema::hasColumn('ct_cruce_cuentas', $column)) throw new RuntimeException('Falta aplicar la migración de cruces de cuentas de proveedores. Ejecute php artisan migrate.');
        }
        if (!Schema::hasTable('v3_accounting_posting_sources')) throw new RuntimeException('Falta aplicar la migración de gobierno contable. Ejecute php artisan migrate.');
    }

    private function money($value)
    {
        return round((float) str_replace(',', '.', (string) $value), 2);
    }
}
