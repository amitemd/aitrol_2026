<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;

class ReceiptInsuranceOrderService
{
    /**
     * Sincroniza la parte cubierta por el seguro de un recibo.
     *
     * La parte pagada por el paciente permanece en ct_orden_venta. La parte
     * pendiente del seguro se registra inmediatamente en ct_ven_orden y sus
     * detalles, aun cuando el recibo todavia no haya sido facturado.
     */
    public function sync(Ct_Orden_Venta $receipt, $saleId = null, array $context = array())
    {
        return DB::transaction(function () use ($receipt, $saleId, $context) {
            $receipt = Ct_Orden_Venta::with(array('detalles.producto', 'agenda.paciente'))
                ->where('id_empresa', $receipt->id_empresa)
                ->lockForUpdate()
                ->findOrFail($receipt->id);

            $details = $receipt->detalles->filter(function ($detail) {
                return round((float) $detail->valor_oda, 2) > 0;
            });
            $insuranceTotal = round((float) $details->sum('valor_oda'), 2);

            $insuranceOrder = Ct_Ven_Orden::where('id_empresa', $receipt->id_empresa)
                ->where('orden_venta', $receipt->id)
                ->where(function ($query) {
                    $query->whereNull('id_ct_venta')->orWhere('id_ct_venta', 0);
                })
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($insuranceTotal <= 0 || $details->isEmpty()) {
                if ($insuranceOrder) {
                    $insuranceOrder->update(array(
                        'estado' => 0,
                        'id_usuariomod' => Auth::id(),
                        'ip_modificacion' => request()->ip(),
                    ));
                }
                return null;
            }

            $agenda = $receipt->agenda;
            $patient = $agenda ? $agenda->paciente : null;
            $patientName = $patient ? trim(implode(' ', array_filter(array(
                $patient->nombre1,
                $patient->nombre2,
                $patient->apellido1,
                $patient->apellido2,
            )))) : '';
            $userId = Auth::id();
            $ip = request()->ip();
            $currencyId = isset($context['divisas']) ? $context['divisas'] : null;
            if (!$currencyId) {
                $currency = Ct_Divisas::where('estado', 1)->orderBy('id')->first();
                $currencyId = $currency ? $currency->id : 1;
            }

            if (!$insuranceOrder) {
                $insuranceOrder = new Ct_Ven_Orden();
                $insuranceOrder->id_usuariocrea = $userId;
                $insuranceOrder->ip_creacion = $ip;
            }

            $subtotalZero = round((float) $details->filter(function ($detail) {
                return (float) $detail->iva <= 0;
            })->sum('valor_oda'), 2);
            $subtotalTaxed = round($insuranceTotal - $subtotalZero, 2);
            $procedureNames = $details->map(function ($detail) {
                return $detail->nombre_producto ?: $detail->descripcion;
            })->filter()->implode(', ');

            $insuranceOrder->fill(array(
                'sucursal' => '0',
                'punto_emision' => '0',
                'numero' => '0',
                'nro_comprobante' => '0',
                'id_asiento' => 0,
                'id_empresa' => $receipt->id_empresa,
                'tipo' => isset($context['tipo']) ? $context['tipo'] : 'VEN-FA',
                'fecha' => isset($context['fecha']) ? $context['fecha'] : ($receipt->fecha_emision ?: date('Y-m-d')),
                'divisas' => $currencyId,
                'nombre_cliente' => $receipt->razon_social,
                'tipo_consulta' => $agenda ? $agenda->proc_consul : null,
                'id_cliente' => $receipt->identificacion,
                'direccion_cliente' => $receipt->direccion,
                'ruc_id_cliente' => $receipt->identificacion,
                'telefono_cliente' => $receipt->telefono,
                'email_cliente' => $receipt->email,
                'orden_venta' => $receipt->id,
                'estado' => 1,
                'estado_pago' => 0,
                'id_paciente' => $agenda ? $agenda->id_paciente : null,
                'nombres_paciente' => $patientName,
                'id_hc_procedimiento' => $agenda ? $agenda->id_procedimiento : null,
                'seguro_paciente' => $receipt->id_seguro,
                'procedimientos' => substr(isset($context['procedimiento']) ? $context['procedimiento'] : $procedureNames, 0, 255),
                'fecha_procedimiento' => $agenda ? $agenda->fechaini : null,
                'copago' => $insuranceTotal,
                'id_recaudador' => isset($context['id_recaudador']) ? $context['id_recaudador'] : $userId,
                'ci_vendedor' => isset($context['ci_vendedor']) ? $context['ci_vendedor'] : $userId,
                'vendedor' => isset($context['vendedor']) ? $context['vendedor'] : '',
                'subtotal_0' => $subtotalZero,
                'subtotal_12' => $subtotalTaxed,
                'descuento' => 0,
                'base_imponible' => $insuranceTotal,
                'impuesto' => 0,
                'total_final' => $insuranceTotal,
                'valor_contable' => $insuranceTotal,
                'id_ct_venta' => $saleId ?: $insuranceOrder->id_ct_venta,
                'id_usuariomod' => $userId,
                'ip_modificacion' => $ip,
            ));
            $insuranceOrder->save();

            Ct_Ven_Orden_Detalle::where('id_ct_ven_orden', $insuranceOrder->id)->delete();
            foreach ($details as $detail) {
                $amount = round((float) $detail->valor_oda, 2);
                $quantity = max(1, (float) $detail->cantidad);
                Ct_Ven_Orden_Detalle::create(array(
                    'id_ct_ven_orden' => $insuranceOrder->id,
                    'id_ct_productos' => $detail->cod_prod ?: (string) $detail->id_producto,
                    'nombre' => $detail->nombre_producto ?: ($detail->descripcion ?: 'Producto'),
                    'cantidad' => $quantity,
                    'precio' => round($amount / $quantity, 4),
                    'descuento_porcentaje' => 0,
                    'descuento' => 0,
                    'extendido' => $amount,
                    'detalle' => $detail->descripcion,
                    'copago' => $amount,
                    'check_iva' => (float) $detail->iva > 0 ? 1 : 0,
                    'id_paciente' => $agenda ? $agenda->id_paciente : null,
                    'estado' => 1,
                    'id_usuariocrea' => $userId,
                    'id_usuariomod' => $userId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ));
            }

            return $insuranceOrder;
        });
    }

    public function cancelPending(Ct_Orden_Venta $receipt)
    {
        return Ct_Ven_Orden::where('id_empresa', $receipt->id_empresa)
            ->where('orden_venta', $receipt->id)
            ->where(function ($query) {
                $query->whereNull('id_ct_venta')->orWhere('id_ct_venta', 0);
            })
            ->where('estado', '<>', 0)
            ->update(array(
                'estado' => 0,
                'id_usuariomod' => Auth::id(),
                'ip_modificacion' => request()->ip(),
            ));
    }
}
