<?php

namespace Sis_medico\reprocesos;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Movimiento;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Empresa;

class PlanillaReproceso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function reprocesarplanilla()
    {
        $planillas = Planilla::whereNotNull('id_asiento_cabecera')->whereIn('id', [402, 407, 408, 411, 412, 415, 416, 452, 706, 742])->get();
        //dd($planillas);
        foreach ($planillas as $planilla) {
            $valor = Planilla_Detalle::where('id_planilla_cabecera', $planilla->id)
                ->where('check', 1)
                ->where('estado', 1)
                ->get();

            $total_n = 0;
            $total_c = 0;
            foreach ($valor as $p) {
                $serie = $p->serie;
                $movimiento = InvDetMovimientos::where('serie', $serie)->first();
                if (is_null($movimiento)) {
                    return $serie;
                }
                if (is_null($movimiento->pedido)) {
                    $movimiento = Movimiento::where('serie', $serie)->first();
                }
                $documento = InvDocumentosBodegas::where('id', $movimiento->pedido->tipo)->whereIn('abreviatura_documento', ['ICG', 'IGR'])->first();
                if (!is_null($documento)) {
                    $total_c    += ($p->cantidad * $p->precio);
                } else {
                    $total_n    += ($p->cantidad * $p->precio);
                }
            }

            $deletedRows = Ct_Asientos_Detalle::where('id_asiento_cabecera', $planilla->id_asiento_cabecera)->delete();


            $empresa = \Sis_medico\Empresa::where('prioridad', 1)->first();
            $id_empresa = $empresa->id;
            $debe = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')->first();
            $debe_consigna = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO_CONSIGNA')->first();
            // $plan_debe =
            $haber = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')->first();
            if ($total_n > 0) {
                $plan                             = Plan_Cuentas::where('id', $debe->id_plan)->first();
                $det_asiento                      = new Ct_Asientos_Detalle;
                $det_asiento->id_asiento_cabecera = $planilla->id_asiento_cabecera;
                $det_asiento->fecha               = $planilla->updated_at;
                $det_asiento->id_plan_cuenta      = $debe->id_plan;
                $det_asiento->descripcion         = $plan->nombre;
                $det_asiento->haber  = $total_n;
                $det_asiento->debe = 0;
                $det_asiento->estado          = 1;
                $det_asiento->ip_creacion     = 'REPROCESO';
                $det_asiento->ip_modificacion = $planilla->ip_creacion;
                $det_asiento->id_usuariocrea  = $planilla->id_usuariomod;
                $det_asiento->id_usuariomod   = $planilla->id_usuariomod;
                $det_asiento->save();
            }
            if ($total_c > 0) {
                $plan                             = Plan_Cuentas::where('id', $debe_consigna->id_plan)->first();
                $det_asiento                      = new Ct_Asientos_Detalle;
                $det_asiento->id_asiento_cabecera = $planilla->id_asiento_cabecera;
                $det_asiento->fecha               = $planilla->updated_at;
                $det_asiento->id_plan_cuenta      = $debe_consigna->id_plan;
                $det_asiento->descripcion         = $plan->nombre;
                $det_asiento->haber  = $total_c;
                $det_asiento->debe = 0;
                $det_asiento->estado          = 1;
                $det_asiento->ip_creacion     = 'REPROCESO';
                $det_asiento->ip_modificacion = $planilla->ip_creacion;
                $det_asiento->id_usuariocrea  = $planilla->id_usuariomod;
                $det_asiento->id_usuariomod   = $planilla->id_usuariomod;
                $det_asiento->save();
            }
            //detalle de haber

            $plan                             = Plan_Cuentas::where('id', $haber->id_plan)->first();
            $det_asiento                      = new Ct_Asientos_Detalle;
            $det_asiento->id_asiento_cabecera = $planilla->id_asiento_cabecera;
            $det_asiento->fecha               = $planilla->updated_at;
            $det_asiento->id_plan_cuenta      = $haber->id_plan;
            $det_asiento->descripcion         = $plan->nombre;
            $det_asiento->debe  = $total_c + $total_n;
            $det_asiento->haber = 0;
            $det_asiento->estado          = 1;
            $det_asiento->ip_creacion     = 'REPROCESO';
            $det_asiento->ip_modificacion = $planilla->ip_creacion;
            $det_asiento->id_usuariocrea  = $planilla->id_usuariocrea;
            $det_asiento->id_usuariomod   = $planilla->id_usuariomod;
            $det_asiento->save();
        }
        return "okay gracias amigo";
    }

    public static function reprocesarKardexPlanilla()
    {
        $planillas = Planilla::whereNotNull('id_asiento_cabecera')->get();
        foreach ($planillas as $planilla) {
            $detalles = Planilla_Detalle::where('id_planilla_cabecera', $planilla->id)
                ->where('check', 1)
                ->where('estado', 1)
                ->get();

            foreach ($detalles as $detalle) {
                $contador = InvDetMovimientos::join('inv_kardex', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
                    ->where('id_movimiento_paciente', $detalle->id_movimiento_paciente)
                    ->count();
                if ($contador == 1) {
                    $verificar = InvDetMovimientos::join('inv_kardex', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
                        ->where('id_movimiento_paciente', $detalle->id_movimiento_paciente)
                        ->first();
                    if ($detalle->cantidad > 0) {
                        if ($detalle->precio != $verificar->valor_unitario) {
                            $detalle->precio = $verificar->valor_unitario;
                            $detalle->ip_creacion = 'reproceso_precio';
                            $detalle->save();
                        }
                    }
                }
            }
        }
        return "okay gracias amigo";
    }
}
