<?php

namespace Sis_medico\reprocesos;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Movimiento;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;
use Sis_medico\InvCabMovimientos;

class EgresoVarioReproceso extends Model
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

    public static function reprocesaregreso()
    {

        $empresa = \Sis_medico\Empresa::where('prioridad', 1)->first();
        $id_empresa = $empresa->id;
        $documentos = InvCabMovimientos::join('inv_documentos_bodegas', 'inv_documentos_bodegas.id', '=', 'inv_cab_movimientos.id_documento_bodega')
            ->where('inv_documentos_bodegas.abreviatura_documento', 'EVC')
            ->where('inv_cab_movimientos.id_empresa', $id_empresa)
            ->select('inv_cab_movimientos.*')
            ->get();
        //return $documentos;
        foreach ($documentos as $key => $cabecera) {
            $total_c = 0;
            $total_n = 0;
            foreach ($cabecera->detalles as $detalle) {
                $serie = $detalle->serie;
                $movimiento = Movimiento::where('serie', $serie)->first();
                if ($movimiento->pedido->tipo == 3) {
                    $total_n    += ($detalle->cantidad * $detalle->valor_unitario);
                } else {
                    $total_c    += ($detalle->cantidad * $detalle->valor_unitario);
                }
            }
            //return $cabecera;
            $deletedRows = Ct_Asientos_Detalle::where('id_asiento_cabecera', $cabecera->id_asiento)->delete();
            $id_empresa = $cabecera->id_empresa;
            $debe = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')->first();
            $debe_consigna = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO_CONSIGNA')->first();
            // $plan_debe =
            $haber = Ct_Configuraciones::where('id_empresa', $id_empresa)->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')->first();
            if ($total_n > 0) {
                $plan                             = Plan_Cuentas::where('id', $debe->id_plan)->first();
                $det_asiento                      = new Ct_Asientos_Detalle;
                $det_asiento->id_asiento_cabecera = $cabecera->id_asiento;
                $det_asiento->fecha               = $cabecera->updated_at;
                $det_asiento->id_plan_cuenta      = $debe->id_plan;
                $det_asiento->descripcion         = $plan->nombre;
                $det_asiento->haber  = $total_n;
                $det_asiento->debe = 0;
                $det_asiento->estado          = 1;
                $det_asiento->ip_creacion     = 'REPROCESO';
                $det_asiento->ip_modificacion = $cabecera->ip_creacion;
                $det_asiento->id_usuariocrea  = $cabecera->id_usuariomod;
                $det_asiento->id_usuariomod   = $cabecera->id_usuariomod;
                $det_asiento->save();
            }
            if ($total_c > 0) {
                $plan                             = Plan_Cuentas::where('id', $debe_consigna->id_plan)->first();
                $det_asiento                      = new Ct_Asientos_Detalle;
                $det_asiento->id_asiento_cabecera = $cabecera->id_asiento;
                $det_asiento->fecha               = $cabecera->updated_at;
                $det_asiento->id_plan_cuenta      = $debe_consigna->id_plan;
                $det_asiento->descripcion         = $plan->nombre;
                $det_asiento->haber  = $total_c;
                $det_asiento->debe = 0;
                $det_asiento->estado          = 1;
                $det_asiento->ip_creacion     = 'REPROCESO';
                $det_asiento->ip_modificacion = $cabecera->ip_creacion;
                $det_asiento->id_usuariocrea  = $cabecera->id_usuariomod;
                $det_asiento->id_usuariomod   = $cabecera->id_usuariomod;
                $det_asiento->save();
            }
            //detalle de haber

            $plan                             = Plan_Cuentas::where('id', $haber->id_plan)->first();
            $det_asiento                      = new Ct_Asientos_Detalle;
            $det_asiento->id_asiento_cabecera = $cabecera->id_asiento;
            $det_asiento->fecha               = $cabecera->updated_at;
            $det_asiento->id_plan_cuenta      = $haber->id_plan;
            $det_asiento->descripcion         = $plan->nombre;
            $det_asiento->debe  = $total_c + $total_n;
            $det_asiento->haber = 0;
            $det_asiento->estado          = 1;
            $det_asiento->ip_creacion     = 'REPROCESO';
            $det_asiento->ip_modificacion = $cabecera->ip_creacion;
            $det_asiento->id_usuariocrea  = $cabecera->id_usuariomod;
            $det_asiento->id_usuariomod   = $cabecera->id_usuariomod;
            $det_asiento->save();
        }
        return "okay gracias amigo";
    }
}
