<?php

namespace Sis_medico\v3\contable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\ComprobanteSecuencia;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\LogContable;


class LogAsiento extends Model
{
    public static function secuenciaEmpresa($id_empresa, $tipo = "", $tipo_comprobante = "1")
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        if ($tipo == 1) {

            $array = [
                [
                    'tipo'           => '1',
                    'secuencia'      => '0000000001',
                    'tipo_comprobante' => $tipo_comprobante,
                    'empresa'        => $id_empresa,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                    'ip_creacion'    => $ip_cliente,
                    'ip_modificacion' => $ip_cliente
                ]
            ];



            ComprobanteSecuencia::insert($array);
        }

        if ($tipo == 2) {
            ComprobanteSecuencia::insert([
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

        if ($tipo >= 5) {
            ComprobanteSecuencia::insert([
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
        $id_empresa = session("id_empresa");
        $idusuario  = Auth::user()->id;
        $ct_secuencia = ComprobanteSecuencia::where('empresa', $id_empresa)->where('tipo', $tipo)->where("tipo_comprobante", $tipo_comprobante)->first();

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

    public static function anulacion($tipo = "", $id_referencia = "", $id_ant = "")
    {
        try {
            $idusuario  = Auth::user()->id;
            $asiento = AsientosCabecera::find($id_ant);
            $log_contable = [
                'tipo'           => $tipo,
                'valor_ant'      => $asiento->valor,
                'valor'          => $asiento->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'observacion'    => "Anulación -- {$asiento->observacion}",
                'id_ant'         => $asiento->id,
                'id_referencia'  => $id_referencia,
            ];

            $id_anulacion = LogContable::insertGetId($log_contable);

            return ["status"=>true,"code"=>$id_anulacion];
        } catch (Exception $e) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
            ];
        }
    }

    public static function anularAsiento($id, $tipo, $msj = "")
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        try {
            $cab = AsientosCabecera::find($id);
            if ($msj == "") {
                $msj = "{$cab->observacion} / Asiento {$id}";
            }
            $id_asiento = AsientosCabecera::insertGetId([
                'observacion'     => $msj,
                'fecha_asiento'   => $cab->fecha_asiento,
                'id_empresa'      => $cab->id_empresa,
                'fact_numero'     => $cab->fact_numero,
                'valor'           => $cab->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'sucursal'        => $cab->sucursal,
            ]);

            foreach ($cab->detalles as $value) {
                AsientosDetalles::create([
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
            return ["status" => true, 'msj' => "Exito", "mod" => 'Log Asiento', "asiento" => $id_asiento];
        } catch (\Exception $e) {
            return ["status" => false, 'msj' => "No se completo la operacion", "exp" => $e->getMessage(), "mod" => 'Log Asiento'];
        }
    }

    public static function guardarAsientoCostoVenta($idVenta,$idAsiento){
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        DB::beginTransaction();
        try {
            LogVentaCosto::insert([
                "id_venta" => $idVenta,
                "id_asiento" => $idAsiento,
                "id_usuariocrea" => $id_usuario,
                "id_usuariomod" => $id_usuario,
                "ip_creacion" => $ip_cliente,
                "ip_modificacion" => $ip_cliente
            ]);
            DB::commit();
            return ['status' => true, 'msj' => 'Listo'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'msj' => "Error al guardar", "exp" => $e->getMessage(),'line'=>$e->getLine()];
        }
    }
}
