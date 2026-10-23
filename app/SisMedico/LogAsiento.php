<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Comprobante_Secuencia;
use Sis_medico\Empresa;
use Sis_medico\LogAnularDeposito;
use Illuminate\Support\Facades\DB;
use Sis_medico\LogCierreAnio;

class LogAsiento
{
    public static function anulacion($tipo = "", $id_referencia = "", $id_ant = "")
    {
        $idusuario  = Auth::user()->id;
        $asiento = Ct_Asientos_Cabecera::find($id_ant);
        $id_anulacion = Log_Contable::insertGetId([
            'tipo'           => $tipo,
            'valor_ant'      => $asiento->valor,
            'valor'          => $asiento->valor,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
            'observacion'    => "Anulación -- {$asiento->observacion}",
            'id_ant'         => $asiento->id,
            'id_referencia'  => $id_referencia,
        ]);

        return $id_anulacion;
    }

    public static function secuenciaEmpresa($id_empresa, $tipo = "", $tipo_comprobante = "1")
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        if ($tipo == 1 or $tipo == "") {
            Ct_Comprobante_Secuencia::create([
                'tipo'           => '1',
                'secuencia'      => '0000000001',
                'tipo_comprobante' => $tipo_comprobante,
                'empresa'        => $id_empresa,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'ip_creacion'    => $ip_cliente,
                'ip_modificacion' => $ip_cliente
            ]);
        }

        if ($tipo == 2 or $tipo == "") {
            Ct_Comprobante_Secuencia::create([
                'tipo'           => '2',
                'secuencia'      => '0000000001',
                'tipo_comprobante' => $tipo_comprobante,
                'empresa'        => $id_empresa,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'ip_creacion'    => $ip_cliente,
                'ip_modificacion' => $ip_cliente
            ]);
        }

        if ($tipo >= 5 or $tipo == "") {
            Ct_Comprobante_Secuencia::create([
                'tipo'           => $tipo,
                'secuencia'      => '0000000001',
                'tipo_comprobante' => $tipo_comprobante,
                'empresa'        => $id_empresa,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'ip_creacion'    => $ip_cliente,
                'ip_modificacion' => $ip_cliente
            ]);
        }
    }

    public static function getSecuencia($tipo, $tipo_comprobante = "1")
    {
        $id_empresa = Session::get('id_empresa');
        $idusuario  = Auth::user()->id;

        $ct_secuencia = Ct_Comprobante_Secuencia::where('empresa', $id_empresa)->where('tipo', $tipo)->where("tipo_comprobante", $tipo_comprobante)->first();

        if (is_null($ct_secuencia)) {
            LogAsiento::secuenciaEmpresa($id_empresa, $tipo, $tipo_comprobante);
            return "0000000001";
        } else {
            $nro_secuencia = intval($ct_secuencia->secuencia);
            $nro_secuencia++;
            $nro_secuencia = str_pad($nro_secuencia, 10, "0", STR_PAD_LEFT);
            $ct_secuencia->secuencia = $nro_secuencia;
            $ct_secuencia->id_usuariomod = $idusuario;
            $ct_secuencia->save();
            return $nro_secuencia;
        }
    }

    public static function depositoBancario($id_comp_ingreso = "", $id_deposito = "", $detalle, $tipo = "")
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        if ($tipo == "I") {
            DB::beginTransaction();
            try {
                LogAnularDeposito::insertGetId([
                    'id_comp_ingreso' => $id_comp_ingreso,
                    'id_deposito' => $id_deposito,
                    'estado' => 1,
                    'detalle' => $detalle,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
                DB::commit();
                return ['status' => 'success', 'msj' => 'Guardado correctamente', 'id' => $id_deposito];
            } catch (\Exception $e) {
                DB::rollback();
                return ['status' => 'error', 'msj' => $e->getMessage()];
            }
        } else if ($tipo == "B") {
            $busqueda = LogAnularDeposito::where('id_comp_ingreso', $id_comp_ingreso)->first();
            if ($busqueda->estado_deposito == 1) {
                return ['estatus' => 'error', 'msj' => 'El comprobante cuenta ya con un deposito', 'id_deposito' => $busqueda->id_deposito];
            } else {
                return ['estatus' => 'success', 'msj' => 'El comprobante no cuenta con un deposito'];
            }
        }
    }

    public static function secuenciCompra($id_empresa, $sucursal, $puntoEmision, $secuencia = "")
    {
        $secuencia = Ct_compras::where("sucursal", $sucursal)->where("punto_emision", $puntoEmision);
        if ($secuencia == "") {
            $nro_secuencia = intval($secuencia->secuencia);
            $nro_secuencia++;
            $nro_secuencia = str_pad($nro_secuencia, 10, "0", STR_PAD_LEFT);
            $secuencia->secuencia = $nro_secuencia;
            $secuencia->save();
            return $nro_secuencia;
        } else {
        }
    }
    public function verificar($anio, $mes, $id_empresa)
    {

        $consulta = LogCierreAnio::whereYear('anio', $anio)->where('id_empresa', $id_empresa)->first();
        if (is_null($consulta)) {
            return ['status' => 'success', 'msj' => 'Si se puede'];
        } else {
            return ['status' => 'error', 'msj' => 'Esta caducado'];
        }
    }

    public function secuenciaAsiento()
    {
        $id_empresa = Session::get('id_empresa');
        $asientos = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)->get();
        $secuencia = 1;
        foreach ($asientos as $value) {
            $value->secuencia = $secuencia;
            $value->save();
            $secuencia++;
        }
    }


    public static function faltaAsiento($id_empresa, $id_asiento = "")
    {
        //return($id_empresa);
        $asiento = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)->get();

        $cuentas = array();
        foreach ($asiento as $cab) {

            $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $cab->id)->get();

            foreach ($detalle as $det) {

                $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $det->id_plan_cuenta)->where('id_empresa', $id_empresa)->first();
                if (is_null($plan_empresa)) {
                    // dd("aquii");
                    $data['id_asiento'] = $cab->id;
                    $data['cuenta'] = $det->id_plan_cuenta;
                    $data['detalle']   = "No Existe";
                    $data['estado']    = 0;
                    array_push($cuentas, $data);
                } else if ($plan_empresa->estado == 1) {
                    $data['id_asiento'] = $cab->id;
                    $data['cuenta'] = $det->id_plan_cuenta;
                    $data['detalle']   = "Cuenta Grupo";
                    $data['estado']     = 2;
                    array_push($cuentas, $data);
                }
            }
        }
        return $cuentas;
    }

    public static function anularAsiento($id, $tipo, $msj = "")
    {
        //dd($id, $tipo, $msj);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        try {
            $cab = Ct_Asientos_Cabecera::find($id);
            if ($msj == "") {
                $msj = "{$cab->observacion} / Asiento {$id}";
            }
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => $msj,
                'fecha_asiento'   => $cab->fecha_asiento,
                'id_empresa'      => $cab->id_empresa,
                'fact_numero'     => $cab->fact_numero,
                'valor'           => $cab->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ]);

            foreach ($cab->detalles as $value) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $cab->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                ]);
            }

            LogAsiento::anulacion($tipo, $id_asiento, $cab->id);

            $url_asiento = url('contable/contabilidad/libro/edit') . "/" . $id_asiento;

            return ["status" => 'success', 'msj' => "Exito", "mod" => 'Log Asiento', "asiento" => $id_asiento, 'url_asiento' => $url_asiento];
        } catch (\Exception $e) {
            return ["status" => 'error', 'msj' => "No se completo la operacion", "exp" => $e->getMessage(), "mod" => 'Log Asiento'];
        }
    }

    public static function buscarModuloAsiento($id){
        $log_contable = Log_Contable::where("id_ant", $id)->first();
        if(!is_null($log_contable)){
            return ['status'=> 'error', 'msj'=>'Ya se encuentra anulado este asiento'];
        }

        $comp_egreso = Ct_Comprobante_Egreso::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_egreso)){
            return ['status'=> 'error', 'msj'=>'Comprobante de Egreso', 'id'=>$comp_egreso->id];
        }

        $comp_egreso_varios = Ct_Comprobante_Egreso_Varios::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_egreso_varios)){
            return ['status'=> 'error', 'msj'=> 'Comprobante de Egreso Varios', 'id'=>$comp_egreso_varios->id];
        }

        $comp_egreso_masivo = Ct_Comprobante_Egreso_Masivo::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_egreso_masivo)){
            return ['status'=> 'error', 'msj'=> 'Comprobante de egreso masivo', 'id'=>$comp_egreso_masivo->id];
        }

        $comp_retenciones = Ct_Retenciones::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_retenciones)){
            return ['status'=> 'error', 'msj'=> 'Existe un comprobante de retenciones de acreedores', 'id'=>$comp_retenciones->id];
        }

        $comp_cruce_valores = Ct_Cruce_Valores::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_cruce_valores)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de cruce de valores de acreedores', 'id' => $comp_cruce_valores->id];
        }

        $comp_cruce_cuentas = Ct_Cruce_Cuentas::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_cruce_cuentas)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de comprobante de cruce de cuentas de acreedores', 'id' => $comp_cruce_cuentas->id];
        }

        $comp_comprobante_egreso = Ct_Comprobante_Egreso::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_comprobante_egreso)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de egreso de acreedores', 'id' => $comp_comprobante_egreso->id];
        }

        $comp_nota_credito = Ct_Credito_Acreedores::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_nota_credito)){
            return ['status' => 'error','msj'=>'Existe un comprobante de nota de credito de acreedores','id'=>$comp_nota_credito->id];
        }

        $comp_nota_debito = Ct_Debito_Acreedores::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_nota_debito)){
            return ['status' => 'error','msj'=>'Existe un comprobante de nota de debito de acreedores','id'=>$comp_nota_debito->id];
        }

        $comp_retenciones_clientes = Ct_Cliente_Retencion::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_retenciones_clientes)){
            return ['status'=> 'error', 'msj' => 'Existe un comprobante de retenciones de clientes','id'=>$comp_retenciones_clientes->id]; 
        }

        $comp_cheques_post = Ct_Cheques_Post::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_cheques_post)){
            return ['status' => 'error', 'msj' => 'Existe un comprante de cheque post de clientes','id'=>$comp_cheques_post->id];
        }

        $comp_cruce_cuentas_clientes = Ct_Cruce_Cuentas_Clientes::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_cruce_cuentas_clientes)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de cruce de cuentas de clientes', 'id' =>$comp_cruce_cuentas_clientes->id];
        }

        $comp_cruce_valores_clientes = Ct_Cruce_Valores_Cliente::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_cruce_valores_clientes)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de cruce de valores de cliente', 'id' => $comp_cruce_valores_clientes->id];
        }

        $comp_ingreso = Ct_Comprobante_Ingreso::where('id_asiento_cabecera', $id)->first();
        if(!is_null($comp_ingreso)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de ingresos de clientes', 'id' => $comp_ingreso->id];
        }

        $comp_ingreso_varios = Ct_Comprobante_Ingreso_Varios::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_ingreso_varios)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de ingresos varios de clientes', 'id' => $comp_ingreso_varios->id];
        }
        
        $comp_nota_credito_clientes = Ct_Nota_Credito_Clientes::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_nota_credito_clientes)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de nota de credito de clientes','id' => $comp_nota_credito_clientes->id];
        }

        $comp_nota_debito_clientes = Ct_Nota_Debito_Cliente::where('id_asiento_cabecera',$id)->first();
        if(!is_null($comp_nota_debito_clientes)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de nota de debito de clientes', 'id' => $comp_nota_debito_clientes -> id];
        }

        $comp_ven_orden = Ct_Ven_Orden::where('id_asiento',$id)->first();
        if(!is_null($comp_ven_orden)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de orden de venta', 'id' => $comp_ven_orden -> id];
        }

        $comp_ventas = Ct_ventas::where('id_asiento', $id)->first();
        if(!is_null($comp_ventas)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de ventas', 'id' => $comp_ventas -> id];
        }

        $comp_nota_debito_bancos = Nota_Debito::where('id_asiento',$id)->first();
        if(!is_null($comp_nota_debito_bancos)){
            return ['status'=>'error', 'msj' => 'Existe un comprobante de nota de debito de bancos','id' => $comp_nota_debito_bancos -> id];
        }

        $comp_factura_activo_cabecera = AfFacturaActivoCabecera::where('id_asiento',$id)->first();
        if(!is_null($comp_factura_activo_cabecera)){
            return ['status'=>'error','msj' => 'Existe un comprobante de factura activo cabecera','id' => $comp_factura_activo_cabecera -> id];
        }

        $comp_nota_credito_banco = Ct_Nota_Credito::where('id_asiento',$id)->first();
        if(!is_null($comp_nota_credito_banco)){
            return ['status' => 'error', 'msj' => 'Existe un compobante de nota de credito de banco', 'id' => $comp_nota_credito_banco -> id ];
        }

        $comp_debito_bancario = Ct_Debito_Bancario::where('id_asiento',$id)->first();
        if(!is_null($comp_debito_bancario)){
            //https://humanlabs.aitrol.com/contable/Banco/debitobancario/revisar/3499
            return ['status' => 'error', 'msj' => 'Existe un comprobante de debito bancario', 'id' => $comp_debito_bancario -> id];
        }
        
        $comp_deposito_bancario = Ct_Deposito_Bancario::where('id_asiento',$id)->first();
        if(!is_null($comp_deposito_bancario)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de deposito bancario', 'id' => $comp_deposito_bancario -> id];
        }
        
        $comp_transferencia_bancaria = Ct_Transferencia_Bancaria::where('id_asiento',$id)->first();
        if(!is_null($comp_transferencia_bancaria)){
            return ['status' => 'error', 'msj' => 'Existe un comprobante de transferencia bancaria', 'id' => $comp_transferencia_bancaria];
        }

        
    }

    public static function verificar_cierre_anio($fecha){

        $id_empresa = Session::get('id_empresa');
        $cierre_cab = Ct_Cierre_Cabecera::where('estado', '1')->where('id_empresa', $id_empresa)->where('fecha_cierre','>=', $fecha)->first();

        if (!is_null($cierre_cab)) {
            return ['status' => 'error', 'msj' => 'Ya existe un cierre contable, no se puede ingresar el documento', 'observacion' => "<b> Fecha:{$cierre_cab->fecha_cierre} <br> Observacion:{$cierre_cab->observacion} </b>"];
        }else {
            return ['status' => 'success', 'msj' => 'Listo'];
        }

    }

    public static function guardarAsientoCostoVenta($id_venta, $id_asiento)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        DB::beginTransaction();
        try {
            Log_Venta_Costo::create([
                "id_venta" => $id_venta,
                "id_asiento" => $id_asiento,
                "id_usuariocrea" => $id_usuario,
                "id_usuariomod" => $id_usuario,
                "ip_creacion" => $ip_cliente,
                "ip_modificacion" => $ip_cliente
            ]);

            DB::commit();

            return ['status' => 'success', 'msj' => 'Listo'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => "Error al guardar", "exp" => $e->getMessage()];
        }
    }

    public static function buscarAsientoAnulado($id_asiento){
        $asiento = Log_Contable::where("id_ant", $id_asiento)->first();

        return $asiento;
    }
}
