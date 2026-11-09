<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Sis_medico\v3\contable\LogAsiento;
use Sis_medico\v3\contable\Utilidades;

class V3SupplierPaymentService
{
    private $posting;

    public function __construct(V3AccountingPostingService $posting)
    {
        $this->posting = $posting;
    }

    public function save($request)
    {
        $companyId = session('id_empresa');
        $userId = Auth::user()->id;
        $ip = Utilidades::getRealIP();

        if ($request->input('id')) {
            return $this->updateNotes($request, $companyId, $userId, $ip);
        }

        $this->assertSchema();
        $providerId = trim((string) $request->input('nombre_acreedores'));
        $date = trim((string) $request->input('fecha'));
        $concept = trim((string) $request->input('concepto'));
        $amount = $this->money($request->input('valor'));
        $token = trim((string) $request->input('payment_token'));
        if ($providerId === '' || $date === '' || $concept === '') {
            throw new InvalidArgumentException('Complete proveedor, fecha y concepto del comprobante de egreso.');
        }
        if ($amount <= 0) throw new InvalidArgumentException('El valor del pago debe ser mayor a cero.');
        if ($token === '' || strlen($token) > 100) throw new InvalidArgumentException('No se recibió la identidad segura del pago. Recargue la pantalla.');

        return DB::transaction(function () use ($request, $companyId, $userId, $ip, $providerId, $date, $concept, $amount, $token) {
            $provider = DB::table('proveedor')->where('id', $providerId)->lockForUpdate()->first();
            if (!$provider) throw new InvalidArgumentException('El proveedor seleccionado no existe.');

            $branch = DB::table('ct_sucursales')->where('id', $request->input('sucursal'))
                ->where('id_empresa', $companyId)->where('estado', 1)->first();
            if (!$branch) throw new InvalidArgumentException('Seleccione una sucursal activa de la empresa.');

            $paymentMethod = \Sis_medico\Ct_Tipo_Pago::query()->where('id', $request->input('forma_pago'))->where('estado', 1)->first();
            if (!$paymentMethod) throw new InvalidArgumentException('Seleccione una forma de pago activa.');
            if (Str::contains(strtolower(Str::ascii($paymentMethod->nombre)), 'pendiente')) {
                throw new InvalidArgumentException('Un comprobante de egreso representa un pago realizado; no admite la forma Pendiente de pago.');
            }

            $cash = DB::table('ct_caja_banco')->where('id', $request->input('banco'))
                ->where('id_empresa', $companyId)->where('estado', 1)->lockForUpdate()->first();
            if (!$cash) throw new InvalidArgumentException('Seleccione la caja o banco desde donde sale el dinero.');
            $methodName = strtolower(Str::ascii($paymentMethod->nombre));
            $isCash = Str::contains($methodName, 'efectivo');
            $expectedClass = $isCash ? 2 : 1;
            if ((int) $cash->clase !== $expectedClass) {
                throw new InvalidArgumentException($isCash
                    ? 'El pago en efectivo debe salir de una cuenta configurada como Caja.'
                    : 'La forma de pago seleccionada debe salir de una cuenta configurada como Banco.');
            }
            $this->assertCompanyAccount($cash->cuenta_mayor, $companyId, 'la caja o banco de pago');

            $isCheque = Str::contains($methodName, 'cheque');
            $checkNumber = trim((string) $request->input('num_cheque'));
            $checkDate = trim((string) $request->input('fecha_cheque'));
            $delivered = $request->has('cheque_entregado') ? 1 : 0;
            if ($isCheque && ($checkNumber === '' || $checkDate === '')) {
                throw new InvalidArgumentException('Para pagar con cheque ingrese el número y la fecha del cheque.');
            }

            $allocations = $this->allocations($request);
            if (empty($allocations)) throw new InvalidArgumentException('Ingrese al menos un abono para una deuda del proveedor.');
            $allocated = round(array_sum(array_column($allocations, 'amount')), 2);
            if (abs($allocated - $amount) > 0.01) {
                throw new InvalidArgumentException('El total de abonos ('.number_format($allocated, 2).') debe coincidir con el valor pagado ('.number_format($amount, 2).').');
            }

            // El token protege la operacion completa, no solo el asiento. Un
            // reintento debe devolver el comprobante ya confirmado sin volver a
            // descontar las deudas del proveedor.
            $completed = $this->completedPayment($companyId, $token, $providerId, $date, $concept,
                $amount, $paymentMethod->id, $cash->id, $isCheque, $checkNumber, $checkDate,
                $isCheque ? $delivered : 0, $branch->codigo_sucursal, $allocations);
            if ($completed) return $completed;

            $payableAccount = !empty($provider->id_cuentas) ? $provider->id_cuentas : null;
            $this->assertCompanyAccount($payableAccount, $companyId, 'cuentas por pagar del proveedor');

            $sequence = LogAsiento::getSecuencia(1, 1);
            $now = date('Y-m-d H:i:s');
            $voucherId = DB::table('ct_comprobante_egreso')->insertGetId(array(
                'descripcion' => $concept, 'estado' => 1,
                'beneficiario' => !empty($provider->nombrecomercial) ? $provider->nombrecomercial : $provider->razonsocial,
                'id_asiento_cabecera' => null, 'check' => $isCheque ? $delivered : 0,
                'fecha_cheque' => $isCheque ? $checkDate : null, 'id_secuencia' => $sequence,
                'id_pago' => $paymentMethod->id, 'id_caja_banco' => $cash->id,
                'no_cheque' => $isCheque ? $checkNumber : null, 'fecha_comprobante' => $date,
                'secuencia' => $sequence, 'id_empresa' => $companyId, 'id_proveedor' => $providerId,
                'valor_pago' => $amount, 'valor' => $amount, 'comentarios' => trim((string) $request->input('nota')),
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                'fecha_entregado' => $isCheque && $delivered ? $date : null,
                'created_at' => $now, 'updated_at' => $now,
            ));

            $purchaseIds = array_column($allocations, 'purchase_id');
            sort($purchaseIds, SORT_STRING);
            $purchases = DB::table('ct_compras')->where('id_empresa', $companyId)
                ->where('proveedor', $providerId)->whereIn('id', $purchaseIds)
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $detailRows = array();
            foreach ($allocations as $allocation) {
                $purchase = $purchases->get($allocation['purchase_id']);
                if (!$purchase) throw new InvalidArgumentException('Una de las deudas seleccionadas no pertenece al proveedor.');
                $balance = $this->money($purchase->saldo_pendiente);
                if ($balance <= 0) $balance = $this->money($purchase->valor_contable);
                if ($allocation['amount'] - $balance > 0.01) {
                    throw new InvalidArgumentException('El abono de la compra '.$purchase->numero.' supera su saldo actual de '.number_format($balance, 2).'.');
                }
                $newBalance = max(0, round($balance - $allocation['amount'], 2));
                $detailRows[] = array(
                    'id_comprobante' => $voucherId, 'observacion' => $concept, 'id_compra' => $purchase->id,
                    'id_secuencia' => $purchase->numero, 'saldo_base' => $balance, 'abono' => $allocation['amount'],
                    'estado' => 1, 'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                    'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                    'created_at' => $now, 'updated_at' => $now,
                );
                DB::table('ct_compras')->where('id', $purchase->id)->update(array(
                    'saldo_pendiente' => $newBalance, 'valor_contable' => $newBalance,
                    'estado_pago' => $newBalance <= 0.01 ? 'PAGADA' : 'PARCIAL',
                    'estado' => $newBalance <= 0.01 ? 3 : 2,
                    'id_caja_banco_pago' => $cash->id, 'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip, 'updated_at' => $now,
                ));
            }
            DB::table('ct_detalle_comprobante_egreso')->insert($detailRows);

            $header = array(
                'sucursal' => $branch->codigo_sucursal, 'punto_emision' => '', 'fecha_asiento' => $date,
                'fact_numero' => $sequence, 'id_empresa' => $companyId, 'observacion' => $concept,
                'valor' => $amount, 'modulo' => 'PAGOS_PROVEEDORES',
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
            );
            $lineBase = array('fecha' => $date, 'establecimiento' => $branch->codigo_sucursal,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip);
            $entryId = $this->posting->post($header, array(
                array_merge($lineBase, array('id_plan_cuenta' => $payableAccount,
                    'descripcion' => 'Cancelación de cuenta por pagar a '.$provider->razonsocial, 'debe' => $amount, 'haber' => 0)),
                array_merge($lineBase, array('id_plan_cuenta' => $cash->cuenta_mayor,
                    'descripcion' => 'Salida de '.$cash->nombre.' por pago a proveedor', 'debe' => 0, 'haber' => $amount)),
            ), array('modulo' => 'PAGOS_PROVEEDORES', 'tipo_origen' => 'COMPROBANTE_EGRESO', 'id_origen' => $token));

            DB::table('ct_comprobante_egreso')->where('id', $voucherId)->update(array('id_asiento_cabecera' => $entryId, 'updated_at' => $now));
            DB::table('ct_compras')->whereIn('id', array_column($allocations, 'purchase_id'))
                ->where('estado_pago', 'PAGADA')->update(array('id_asiento_pago' => $entryId));

            return array('voucher_id' => $voucherId, 'entry_id' => $entryId, 'sequence' => $sequence,
                'idempotent' => false);
        }, 3);
    }

    public function cancel($request)
    {
        $this->assertCancellationSchema();
        $companyId = session('id_empresa');
        $userId = Auth::user()->id;
        $ip = Utilidades::getRealIP();
        $voucherId = trim((string) $request->input('id'));
        $reason = trim((string) $request->input('motivo'));
        if ($voucherId === '') throw new InvalidArgumentException('No se recibió el comprobante de egreso que desea anular.');
        if (strlen($reason) < 10) throw new InvalidArgumentException('Ingrese un motivo de anulación de al menos 10 caracteres.');
        if (strlen($reason) > 500) throw new InvalidArgumentException('El motivo de anulación no puede superar 500 caracteres.');

        return DB::transaction(function () use ($companyId, $userId, $ip, $voucherId, $reason) {
            $voucher = DB::table('ct_comprobante_egreso')->where('id', $voucherId)
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$voucher) throw new InvalidArgumentException('El comprobante de egreso no existe o pertenece a otra empresa.');
            if ((int) $voucher->estado === 0) {
                if (!empty($voucher->id_asiento_reverso)) {
                    return array('voucher_id' => (int) $voucher->id,
                        'reversal_entry_id' => (int) $voucher->id_asiento_reverso, 'idempotent' => true);
                }
                throw new RuntimeException('El comprobante ya está anulado, pero no tiene un asiento de reverso relacionado. Requiere revisión contable.');
            }
            if (empty($voucher->id_asiento_cabecera)) {
                throw new RuntimeException('El comprobante no posee asiento contable original; no puede anularse automáticamente.');
            }

            $entry = DB::table('ct_asientos_cabecera')->where('id', $voucher->id_asiento_cabecera)
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$entry) throw new RuntimeException('No se encontró el asiento contable original del comprobante.');
            $entryLines = DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $entry->id)
                ->where('estado', '<>', 0)->orderBy('id')->get();
            if ($entryLines->isEmpty()) throw new RuntimeException('El asiento original no tiene líneas contables para generar el reverso.');

            $today = date('Y-m-d');
            $header = array(
                'sucursal' => $entry->sucursal,
                'punto_emision' => isset($entry->punto_emision) ? $entry->punto_emision : '',
                'fecha_asiento' => $today, 'fact_numero' => $entry->fact_numero,
                'id_empresa' => $companyId,
                'observacion' => 'ANULACIÓN COMPROBANTE DE EGRESO '.$voucher->secuencia.': '.$reason,
                'valor' => $entry->valor, 'modulo' => 'PAGOS_PROVEEDORES',
                'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                'ip_creacion' => $ip, 'ip_modificacion' => $ip,
            );
            $reversalLines = array();
            foreach ($entryLines as $line) {
                $reversalLines[] = array(
                    'id_plan_cuenta' => $line->id_plan_cuenta,
                    'descripcion' => 'REVERSO: '.$line->descripcion,
                    'fecha' => $today, 'debe' => $this->money($line->haber),
                    'haber' => $this->money($line->debe),
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                );
            }
            $reversalEntryId = $this->posting->post($header, $reversalLines, array(
                'modulo' => 'PAGOS_PROVEEDORES',
                'tipo_origen' => 'ANULACION_COMPROBANTE_EGRESO',
                'id_origen' => (string) $voucher->id,
            ));

            $allocations = DB::table('ct_detalle_comprobante_egreso')
                ->where('id_comprobante', $voucher->id)->where('estado', 1)->whereNotNull('id_compra')
                ->select('id_compra', DB::raw('SUM(abono) AS abono'), DB::raw('MAX(saldo_base) AS saldo_base'))
                ->groupBy('id_compra')->orderBy('id_compra')->get();
            $purchaseIds = $allocations->pluck('id_compra')->filter()->values()->all();
            $purchases = empty($purchaseIds) ? collect() : DB::table('ct_compras')
                ->where('id_empresa', $companyId)->where('proveedor', $voucher->id_proveedor)
                ->whereIn('id', $purchaseIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $now = date('Y-m-d H:i:s');
            foreach ($allocations as $allocation) {
                $purchase = $purchases->get($allocation->id_compra);
                if (!$purchase) throw new RuntimeException('Una deuda relacionada con el comprobante ya no pertenece al proveedor o a la empresa.');
                $pending = $this->money($purchase->saldo_pendiente);
                $legacyPending = $this->money($purchase->valor_contable);
                if (abs($pending - $legacyPending) > 0.01) $pending = $legacyPending;
                $restored = round($pending + $this->money($allocation->abono), 2);
                // saldo_base representa la deuda exacta de esta compra justo antes
                // de aplicar el comprobante. Es más fiable que total_final cuando
                // existen retenciones o ajustes previos.
                $maximumDebt = $this->money($allocation->saldo_base);
                if ($maximumDebt <= 0) $maximumDebt = $this->money($purchase->total_final);
                if ($maximumDebt > 0 && $restored - $maximumDebt > 0.01) {
                    throw new RuntimeException('La anulación excedería la deuda original de la compra '.$purchase->numero.'. Revise sus movimientos posteriores.');
                }
                $isPending = $maximumDebt <= 0 || $restored >= $maximumDebt - 0.01;
                $changes = array(
                    'saldo_pendiente' => $restored, 'valor_contable' => $restored,
                    'estado_pago' => $isPending ? 'PENDIENTE' : 'PARCIAL',
                    'estado' => $isPending ? 1 : 2, 'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip, 'updated_at' => $now,
                );
                if ((string) $purchase->id_asiento_pago === (string) $voucher->id_asiento_cabecera) {
                    $changes['id_asiento_pago'] = null;
                    $changes['id_caja_banco_pago'] = null;
                }
                DB::table('ct_compras')->where('id', $purchase->id)->where('id_empresa', $companyId)->update($changes);
            }

            DB::table('ct_detalle_comprobante_egreso')->where('id_comprobante', $voucher->id)
                ->where('estado', 1)->update(array('estado' => 0, 'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip, 'updated_at' => $now));
            DB::table('ct_comprobante_egreso')->where('id', $voucher->id)->where('id_empresa', $companyId)
                ->update(array('estado' => 0, 'motivo_anulacion' => $reason,
                    'id_asiento_reverso' => $reversalEntryId, 'fecha_anulacion' => $now,
                    'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => $now));

            return array('voucher_id' => (int) $voucher->id,
                'reversal_entry_id' => (int) $reversalEntryId, 'idempotent' => false);
        }, 3);
    }

    private function allocations($request)
    {
        $amountsByPurchase = array();
        $count = max(0, (int) $request->input('contador_total'));
        for ($i = 0; $i < $count; $i++) {
            $amount = $this->money($request->input('abono'.$i));
            if ($amount <= 0) continue;
            $purchaseId = trim((string) $request->input('id_actualiza'.$i));
            if ($purchaseId === '') throw new InvalidArgumentException('No se pudo identificar una deuda seleccionada.');
            if ($this->money($request->input('valorasumir'.$i)) > 0) {
                throw new InvalidArgumentException('Las retenciones asumidas deben registrarse en su proceso específico antes de emitir el pago.');
            }
            if (!isset($amountsByPurchase[$purchaseId])) $amountsByPurchase[$purchaseId] = 0;
            $amountsByPurchase[$purchaseId] = round($amountsByPurchase[$purchaseId] + $amount, 2);
        }
        ksort($amountsByPurchase, SORT_STRING);
        $allocations = array();
        foreach ($amountsByPurchase as $purchaseId => $amount) {
            $allocations[] = array('purchase_id' => (string) $purchaseId, 'amount' => $amount);
        }
        return $allocations;
    }

    private function updateNotes($request, $companyId, $userId, $ip)
    {
        return DB::transaction(function () use ($request, $companyId, $userId, $ip) {
            $voucher = DB::table('ct_comprobante_egreso')->where('id', $request->input('id'))
                ->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$voucher) throw new InvalidArgumentException('No se encontró el comprobante de egreso que desea actualizar.');
            $changes = array('descripcion' => trim((string) $request->input('concepto')),
                'id_usuariomod' => $userId, 'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s'));
            if ($request->has('nota')) $changes['comentarios'] = trim((string) $request->input('nota'));
            DB::table('ct_comprobante_egreso')->where('id', $voucher->id)->update($changes);
            return array('voucher_id' => $voucher->id, 'entry_id' => null);
        });
    }

    private function completedPayment($companyId, $token, $providerId, $date, $concept, $amount,
        $paymentMethodId, $cashId, $isCheque, $checkNumber, $checkDate, $delivered, $branchCode,
        array $allocations)
    {
        $source = DB::table('v3_accounting_posting_sources')->where('id_empresa', $companyId)
            ->where('modulo', 'PAGOS_PROVEEDORES')->where('tipo_origen', 'COMPROBANTE_EGRESO')
            ->where('id_origen', $token)->lockForUpdate()->first();
        if (!$source) return null;
        if (!$source->id_asiento) throw new RuntimeException('El pago anterior con esta identidad todavía se encuentra en proceso.');

        $voucher = DB::table('ct_comprobante_egreso')->where('id_empresa', $companyId)
            ->where('id_asiento_cabecera', $source->id_asiento)->lockForUpdate()->first();
        if (!$voucher) throw new RuntimeException('La identidad del pago existe pero su comprobante no pudo ser localizado.');
        $entry = DB::table('ct_asientos_cabecera')->where('id', $source->id_asiento)
            ->where('id_empresa', $companyId)->first();
        $same = (string) $voucher->id_proveedor === (string) $providerId
            && (string) $voucher->fecha_comprobante === (string) $date
            && abs($this->money($voucher->valor_pago) - $amount) <= 0.01
            && trim((string) $voucher->descripcion) === $concept
            && (string) $voucher->id_pago === (string) $paymentMethodId
            && (string) $voucher->id_caja_banco === (string) $cashId
            && (!$isCheque || (trim((string) $voucher->no_cheque) === $checkNumber
                && (string) $voucher->fecha_cheque === (string) $checkDate
                && (int) $voucher->check === (int) $delivered))
            && $entry && (string) $entry->sucursal === (string) $branchCode;

        $stored = DB::table('ct_detalle_comprobante_egreso')->where('id_comprobante', $voucher->id)
            ->where('estado', 1)->select('id_compra', DB::raw('SUM(abono) AS amount'))
            ->groupBy('id_compra')->get();
        $storedAllocations = array();
        foreach ($stored as $row) $storedAllocations[(string) $row->id_compra] = $this->money($row->amount);
        $expectedAllocations = array();
        foreach ($allocations as $allocation) $expectedAllocations[(string) $allocation['purchase_id']] = $this->money($allocation['amount']);
        ksort($storedAllocations, SORT_STRING);
        ksort($expectedAllocations, SORT_STRING);
        if (!$same || $storedAllocations !== $expectedAllocations) {
            throw new RuntimeException('La identidad segura del pago ya fue utilizada con información diferente. Recargue la pantalla.');
        }

        return array('voucher_id' => (int) $voucher->id, 'entry_id' => (int) $source->id_asiento,
            'sequence' => $voucher->secuencia, 'idempotent' => true);
    }

    private function assertSchema()
    {
        foreach (array('estado_pago', 'saldo_pendiente', 'id_caja_banco_pago', 'id_asiento_pago') as $column) {
            if (!Schema::hasColumn('ct_compras', $column)) throw new RuntimeException('Falta aplicar la migración del ciclo de compras y pagos. Ejecute php artisan migrate.');
        }
        if (!Schema::hasTable('v3_accounting_posting_sources')) throw new RuntimeException('Falta aplicar la migración de gobierno contable. Ejecute php artisan migrate.');
    }

    private function assertCancellationSchema()
    {
        $this->assertSchema();
        foreach (array('motivo_anulacion', 'id_asiento_reverso', 'fecha_anulacion') as $column) {
            if (!Schema::hasColumn('ct_comprobante_egreso', $column)) {
                throw new RuntimeException('Falta aplicar la migración de anulación de comprobantes de egreso. Ejecute php artisan migrate.');
            }
        }
    }

    private function assertCompanyAccount($account, $companyId, $label)
    {
        if (!$account || !DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('id_plan', $account)->exists()) {
            throw new RuntimeException('Configure una cuenta contable válida para '.$label.' antes de guardar el comprobante.');
        }
    }

    private function money($value)
    {
        return round((float) str_replace(',', '.', (string) $value), 2);
    }
}
