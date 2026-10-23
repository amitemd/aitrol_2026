<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Session;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDocumentosBodegas;

class InvContableCab extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_contable_cab';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    public function detalles()
    {
        return $this->hasMany('Sis_medico\InvContableDet', 'id_contable_cab', 'id');
    }

    public static function setAsientoContablePedido($id_movimiento = null)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cab_mov  = InvCabMovimientos::find($id_movimiento);
        $contable = InvContableCab::where('id_documento_bodega', $cab_mov->id_documento_bodega)
            ->where('estado', 1)
            ->where("id_empresa", Session::get('id_empresa'))
            ->first();
        //dd($contable->detalles);
        $documento = InvDocumentosBodegas::find($cab_mov->id_documento_bodega);        
        if ($contable != null && $documento->tipo != 'F' or $documento == 'IVR') {
            $cuerpo    = $contable->detalles;
            $documento = $cab_mov->documento_bodega->abreviatura_documento;
            if (isset($cab_mov->id)) {
                $pedido = $cab_mov->pedido;
                $glosa  = "";
                $glosa  = $documento;
                if (isset($pedido->id)) {
                    $glosa .= " | " . $pedido->pedido . " | " . $pedido->proveedor->nombrecomercial;
                }
                $t_suma                       = $cab_mov->subtotal + $cab_mov->subtotal_0;
                $cab_asiento                  = new Ct_Asientos_Cabecera;
                $cab_asiento->observacion     = $glosa;
                $cab_asiento->fecha_asiento   = date('Y-m-d H:i:s');
                $cab_asiento->fact_numero     = $cab_mov->num_doc_cont;
                $cab_asiento->valor           = $t_suma;
                $cab_asiento->estado          = 1;
                $cab_asiento->id_empresa      = Session::get('id_empresa');
                $cab_asiento->modulo          = $documento;
                $cab_asiento->ip_creacion     = $ip_cliente;
                $cab_asiento->ip_modificacion = $ip_cliente;
                $cab_asiento->id_usuariocrea  = $idusuario;
                $cab_asiento->id_usuariomod   = $idusuario;
                $cab_asiento->save();
                if ($documento == 'IGR' or $documento == 'IGR' or $documento == 'IFC') {
                    foreach ($cuerpo as $value) {
                        #   DETALLE DEL ASIENTO  #
                        $plan                             = Plan_Cuentas::where('id', $value->cuenta)->first();
                        $det_asiento                      = new Ct_Asientos_Detalle;
                        $det_asiento->id_asiento_cabecera = $cab_asiento->id;
                        $det_asiento->fecha               = date('Y-m-d H:i:s');
                        $det_asiento->descripcion         = $glosa;
                        $det_asiento->id_plan_cuenta      = $value->cuenta;
                        $det_asiento->descripcion         = $plan->nombre;
                        if ($value->tipo == 'D') {
                            $det_asiento->debe  = $t_suma;
                            $det_asiento->haber = 0;
                        } else {
                            $det_asiento->debe  = 0;
                            $det_asiento->haber = $t_suma;
                        }
                        $det_asiento->estado          = 1;
                        $det_asiento->ip_creacion     = $ip_cliente;
                        $det_asiento->ip_modificacion = $ip_cliente;
                        $det_asiento->id_usuariocrea  = $idusuario;
                        $det_asiento->id_usuariomod   = $idusuario;
                        $det_asiento->save();
                    }
                } elseif ($documento == 'IFA' ) {
                    foreach ($cuerpo as $value) {
                        #   DETALLE DEL ASIENTO  #
                        $plan                             = Plan_Cuentas::where('id', $value->cuenta)->first();
                        $det_asiento                      = new Ct_Asientos_Detalle;
                        $det_asiento->id_asiento_cabecera = $cab_asiento->id;
                        $det_asiento->fecha               = date('Y-m-d H:i:s');
                        $det_asiento->descripcion         = $glosa;
                        $det_asiento->id_plan_cuenta      = $value->cuenta;
                        $det_asiento->descripcion         = $plan->nombre;
                        $det_asiento->estado              = 1;
                        $det_asiento->ip_creacion         = $ip_cliente;
                        $det_asiento->ip_modificacion     = $ip_cliente;
                        $det_asiento->id_usuariocrea      = $idusuario;
                        $det_asiento->id_usuariomod       = $idusuario;
                        if ($value->tipo == 'D' and $value->secuencia == 1) {
                            // PRODUCTO TERMINADO
                            $det_asiento->debe  = $cab_mov->total;
                            $det_asiento->haber = 0;
                            $det_asiento->save();
                        }
                        if ($value->tipo == 'D' and $value->secuencia == 2 and $cab_mov->iva > 0) {
                            // IVA COMPRA BIENES
                            $det_asiento->debe  = $cab_mov->iva;
                            $det_asiento->haber = 0;
                            $det_asiento->save();
                        }
                        if ($value->tipo == 'H' and $value->secuencia == 3 and $cab_mov->iva > 0) {
                            // RETENCIONES DE IVA COMPRA BIENES
                            $det_asiento->debe  = 0;
                            $det_asiento->haber = $cab_mov->iva;
                            $det_asiento->save();
                        }
                        if ($value->tipo == 'H' and $value->secuencia == 4) {
                            // CXP PROVEEDORES LOCALES
                            $det_asiento->debe  = 0;
                            $det_asiento->haber = $cab_mov->total;
                            $det_asiento->save();
                        }
                    }
                } else {
                    foreach ($cuerpo as $value) {
                        #   DETALLE DEL ASIENTO  #
                        $plan                             = Plan_Cuentas::where('id', $value->cuenta)->first();
                        $det_asiento                      = new Ct_Asientos_Detalle;
                        $det_asiento->id_asiento_cabecera = $cab_asiento->id;
                        $det_asiento->fecha               = date('Y-m-d H:i:s');
                        $det_asiento->descripcion         = $glosa;
                        $det_asiento->id_plan_cuenta      = $value->cuenta;
                        $det_asiento->descripcion         = $plan->nombre;
                        if ($value->tipo == 'D') {
                            $det_asiento->debe  = $t_suma;
                            $det_asiento->haber = 0;
                        } else {
                            $det_asiento->debe  = 0;
                            $det_asiento->haber = $t_suma;
                        }
                        $det_asiento->estado          = 1;
                        $det_asiento->ip_creacion     = $ip_cliente;
                        $det_asiento->ip_modificacion = $ip_cliente;
                        $det_asiento->id_usuariocrea  = $idusuario;
                        $det_asiento->id_usuariomod   = $idusuario;
                        $det_asiento->save();
                    }
                }

                return $cab_asiento->id;
            }
        }

        

    }
    public static function anularAsiento($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $asiento    = Ct_Asientos_Cabecera::findorfail($id);
        //dd($asiento);
        $asiento->tipo          = 0;
        $asiento->estado        = 1;
        $asiento->id_usuariomod = $idusuario;
        $asiento->save();
        $detalles       = $asiento->detalles;
        $concepto       = 'ANULACION DE TRANSACCION DE INVENTARIO ';
        $estado_compras = Ct_Asientos_Cabecera::where('id', $id)->first();
        if (!is_null($estado_compras)) {
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => 'ANULACION ' . $asiento->observacion,
                'fecha_asiento'   => $asiento->fecha_asiento,
                'valor'           => $asiento->valor,
                'tipo'            => '2',
                'id_empresa'      => $asiento->id_empresa,
                'aparece_sri'     => $asiento->aparece_sri,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            foreach ($detalles as $value) {
                $value->estado = 1;
                $value->save();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $asiento->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }
            Log_Contable::create([
                'tipo'           => 'L',
                'valor_ant'      => $asiento->valor,
                'valor'          => $asiento->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'observacion'    => $asiento->concepto,
                'id_ant'         => $id,
                'id_referencia'  => $id_asiento,
            ]);
        }
        return "ok";
    }

    public static function setAsientoContableComprarativo($id_movimiento = null)
    {
        //  proceso comparativo inventario //
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cab_mov  = InvCabMovimientos::find($id_movimiento);
        $contable = InvContableCab::where('id_documento_bodega', $cab_mov->id_documento_bodega)
            ->where('estado', 1)
            ->first();
        $cuerpo    = $contable->detalles;
        $documento = $cab_mov->documento_bodega->abreviatura_documento;

        if (isset($cab_mov->id) and ($documento == 'EGP')) {
            $cab_asiento                  = new Ct_Asientos_Cabecera;
            $cab_asiento->observacion     = $documento . " | " . $cab_mov->observacion;
            $cab_asiento->fecha_asiento   = date('Y-m-d H:i:s');
            $cab_asiento->fact_numero     = $cab_mov->num_doc_cont;
            $cab_asiento->valor           = $cab_mov->total;
            $cab_asiento->estado          = 1;
            $cab_asiento->id_empresa      = Session::get('id_empresa');
            $cab_asiento->modulo          = $documento;
            $cab_asiento->ip_creacion     = $ip_cliente;
            $cab_asiento->ip_modificacion = $ip_cliente;
            $cab_asiento->id_usuariocrea  = $idusuario;
            $cab_asiento->id_usuariomod   = $idusuario;
            $cab_asiento->save();
            foreach ($cuerpo as $value) {
                #   DETALLE DEL ASIENTO  #
                $plan                             = Plan_Cuentas::where('id', $value->cuenta)->first();
                $det_asiento                      = new Ct_Asientos_Detalle;
                $det_asiento->id_asiento_cabecera = $cab_asiento->id;
                $det_asiento->fecha               = date('Y-m-d H:i:s');
                $det_asiento->descripcion         = $documento . ": " . $cab_mov->observacion;
                $det_asiento->id_plan_cuenta      = $value->cuenta;
                $det_asiento->descripcion         = $plan->nombre;
                if ($value->tipo == 'D') {
                    $det_asiento->debe  = $cab_mov->total;
                    $det_asiento->haber = 0;
                } else {
                    $det_asiento->debe  = 0;
                    $det_asiento->haber = $cab_mov->total;
                }
                $det_asiento->estado          = 1;
                $det_asiento->ip_creacion     = $ip_cliente;
                $det_asiento->ip_modificacion = $ip_cliente;
                $det_asiento->id_usuariocrea  = $idusuario;
                $det_asiento->id_usuariomod   = $idusuario;
                $det_asiento->save();
            }
            return $cab_asiento->id;
        }
    }
}
