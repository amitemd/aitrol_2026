<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Caja;
use Sis_medico\Validate_Decimals;

class ComprobanteEgresoMasivo extends Model
{
    private static $modelo = 'ComprobanteEgresoMasivo';
    protected $table = 'ct_comprobante_egreso_masivo';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function buscarEgresoMasivo($r)
    {

        $egreso = ComprobanteEgresoMasivo::where('id_empresa', session('id_empresa'))
            ->leftJoin("users", "ct_comprobante_egreso_masivo.id_usuariocrea", "=", "users.id")
            ->where('ct_comprobante_egreso_masivo.id_empresa', session('id_empresa'))
            ->whereBetween('ct_comprobante_egreso_masivo.created_at', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->orderBy('id', 'DESC')
            ->get([
                "ct_comprobante_egreso_masivo.*",
                DB::raw("CONCAT(users.nombre1, ' ', users.nombre2) as nombreCrea"),
            ]);
    }


    public static function getEgresosMasivos($id = '')
    {
        if ($id == '') {
            return ComprobanteEgresoMasivo::where('id_empresa', session('id_empresa'))
                ->leftjoin("users", "ct_comprobante_egreso_masivo.id_usuariocrea", "users.id")
                ->orderBy('id', 'DESC')->get([
                    "ct_comprobante_egreso_masivo.*",
                    DB::raw("CONCAT(users.nombre1, ' ', users.nombre2) as nombreCrea"),
                ]);
        }
    }

    public static function guardarComprobanteEgresoVario($r)
    {

        $id_empresa = $r->session()->get('id_empresa');
        $punto_emision = $r['punto_emision'];
        $punto_emision = substr($punto_emision, 4);
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $objeto_validar = new Validate_Decimals();
        $numero_factura = 0;
        $idusuario  = Auth::user()->id;
        $id_empresa = $r->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            if (!isset($r->id)) {
                $numero_factura = LogAsiento::getSecuencia(2);
                $cod_sucurs = PuntoEmision::where('estado', '1')->where('id', $r->sucursal)->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj = DB::table('ct_caja')->where('estado', '1')->where('id', $r->punto_emision)->first();
                $c_caja = $cod_caj->codigo_caja;
                $arrayAsientoCabecera = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja,
                    'fecha_asiento' => $r->fecha_hoy,
                    'fact_numero' => $numero_factura,
                    'id_empresa' => session('id_empresa'),
                    'observacion' => $r->concepto,
                    'valor' => $r->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cabecera = Contabilidad::crearAsiento('proveedores', 'crearComprobanteEgresovarios', $arrayAsientoCabecera,  $r, "venta");
                if ($id_asiento_cabecera['status'] == 'error') {
                    throw new Exception($id_asiento_cabecera['message']);
                }
                $input_comprobante = [
                    'descripcion'     => $r['concepto'],
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    'nota'     => $r['nota'],
                    'fecha_comprobante' => $r['fecha_hoy'],
                    'id_divisa'         => $r["divisas"],
                    'id_banco'          => $r["banco"],
                    'direccion'         => $r["direccion"],
                    'beneficiario'    => strtoupper($r['beneficiario']),
                    'check'           => isset($r['cheque_entregado']) ? 1 : 0,
                    'girado'          => $r['girado'],
                    'id_caja_banco'   => $r['banco'],
                    'nro_cheque'      => $r['num_cheque'],
                    'valor'           => $objeto_validar->set_round($r['valor']),
                    'fecha_cheque'    => $r['fecha_cheque'],
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'fecha_entregado'  => isset($r['fecha_hoy']) ? date("Y-m-d") : NULL,
                ];
                $id_comprobante = EgresosVarios::insertGetId($input_comprobante);
                for ($i = 0; $i < count($r['debe']); $i++) {
                    $nuevo_saldof = $r['debe'][$i];
                    if (!is_null($r['cuenta'][$i])) {
                        $desc_cuenta = Plan_Cuentas::where('id', $r['cuenta'][$i])->first();
                        if ($desc_cuenta != null) {
                            if ($r['visibilidad'][$i] == 1 || $r['visibilidad'][$i] == '1') {
                                if (!is_null($r['debe'][$i]) && $r['debe'][$i] > 0) {
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => 111,
                                        'id_plan_cuenta'      => $r['cuenta'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $r['fecha_hoy'],
                                        'debe'                => $nuevo_saldof,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                                if (!is_null($r['haber'][$i]) && $r['haber'][$i] > 0) {
                                    $nuevo_saldof = $objeto_validar->set_round($r['haber'][$i]);

                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => 111,
                                        'id_plan_cuenta'      => $r['cuenta'][$i],
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $r['fecha_hoy'],
                                        'haber'               => $nuevo_saldof,
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                    ]);
                                }
                            }
                        }
                    }

                    if (!is_null($r['debe'][$i]) && $r['debe'][$i] > 0) {
                        $cons = Plan_Cuentas::find($r['cuenta'][$i]);
                        DetalleComprobanteEgresoVarios::insert([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $r['cuenta'][$i],
                            'cuenta'                         => $cons->nombre,
                            'descripcion'                    => $r['observacion'],
                            'debe'                           => $r['debe'][$i],
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }
                    if (!is_null($r['haber'][$i]) && $r['haber'][$i] > 0) {
                        $cons = Plan_Cuentas::find($r['cuenta'][$i]);
                        DetalleComprobanteEgresoVarios::insert([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $r['cuenta'][$i],
                            'cuenta'                         => $cons->nombre,
                            'descripcion'                    => $r['observacion'],
                            'debe'                           => $r['haber'][$i],
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                    }
                }
            }else{
                $registro = EgresosVarios::find($r->id);
                $registro->nota = $r->nota;
                $registro->descripcion = $r->concepto;
                $registro->nro_cheque = $r->num_cheque;
                $registro->save();
            }
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/proveedores/comprobantedeegresovario'
            ];
            return json_encode($arrayRespuesta);
        } catch (\Exception $e) {

            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
                'reload' => ''
            ];
            $r->c = 'Comprobante Egreso Masivo';
            $r->s = 'saveComprobanteEgresoMasivo';
            $r->m = self::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
    }

    public static function anularComprobanteMasivo($r)
    {

        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $concepto = $r->text;
            $comp_ingreso = ComprobanteEgresoVarios::where('estado', '1')->where('id', $r->id)->first();
            $comp_ingreso->estado = 0;
            $comp_ingreso->nota   = $concepto;
            $comp_ingreso->ip_creacion = $ip_cliente;
            $comp_ingreso->ip_modificacion = $ip_cliente;
            $comp_ingreso->id_usuariomod = $idusuario;
            $comp_ingreso->save();
            $asiento = AsientosCabecera::find($comp_ingreso->id_asiento_cabecera);
            $asiento->estado = 1;
            $asiento->id_usuariomod = $idusuario;
            $detalles = $asiento->detalles;

            $id_asiento = AsientosCabecera::insertGetId([
                'observacion'     => $concepto,
                'fecha_asiento'   => $asiento->fecha_asiento,
                'id_empresa'      => session("id_empresa"),
                'fact_numero'     => $comp_ingreso->secuencia,
                'valor'           => $asiento->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal'        => $asiento->sucursal,
            ]);
            foreach ($detalles as $value) {
                AsientosDetalles::insert([
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
            LogAsiento::anulacion("AC-EGV", $id_asiento, $asiento->id);
            DB::commit();
            return ["status" => "success", "msj" => "Registro Anulado", 'reload' => ''];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine()];
        }
    }

    public static function getPuntoEmision($r)
    {
        return Ct_Caja::where("id_sucursal", $r["comprobante"]->asiento->sucursal)->where("codigo_caja", $r["comprobante"]->asiento->punto_emision)->where("id_empresa", session("id_empresa"))->first();
    }
}
