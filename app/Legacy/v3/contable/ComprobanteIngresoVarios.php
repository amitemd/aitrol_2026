<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\DetallePagoIngresoVarios;
use Sis_medico\v3\contable\Plan_Cuentas;
use Sis_medico\v3\contable\DetalleComprobanteIngresoVarios;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\AsientosCabecera;

class ComprobanteIngresoVarios extends Model
{
    private static $modelo = 'Comprobante Ingreso Varios';
    protected $table = 'ct_comprobante_ingreso_varios';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getCompIngresoVarios($id = '')
    {
        if ($id == '') {
            return ComprobanteIngresoVarios::leftjoin('users as uc', 'ct_comprobante_ingreso_varios.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_caja_banco as cb', 'ct_comprobante_ingreso_varios.id_banco', 'cb.id')
                ->where('ct_comprobante_ingreso_varios.id_empresa', session('id_empresa'))
                //->whereYear('ct_comprobante_ingreso_varios.fecha', session('periodo'))
                ->orderBy('ct_comprobante_ingreso_varios.id', 'desc')->get([
                    'ct_comprobante_ingreso_varios.id',
                    'ct_comprobante_ingreso_varios.secuencia',
                    'ct_comprobante_ingreso_varios.id_asiento_cabecera',
                    'ct_comprobante_ingreso_varios.fecha',
                    'ct_comprobante_ingreso_varios.id_banco',
                    'ct_comprobante_ingreso_varios.concepto',
                    'ct_comprobante_ingreso_varios.total_ingreso',
                    'ct_comprobante_ingreso_varios.estado',
                    'cb.nombre as nombre_caja',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                ]);
        } else {
            return ComprobanteIngresoVarios::leftjoin('users as uc', 'ct_comprobante_ingreso_varios.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_caja_banco as cb', 'ct_comprobante_ingreso_varios.id_banco', 'cb.id')
                ->leftjoin('ct_divisas as cd', 'ct_comprobante_ingreso_varios.divisas', 'cd.id')
                ->where('ct_comprobante_ingreso_varios.id_empresa', session('id_empresa'))
                ->where('ct_comprobante_ingreso_varios.id', $id)
                ->orderBy('ct_comprobante_ingreso_varios.id', 'desc')->first([
                    'ct_comprobante_ingreso_varios.id',
                    'ct_comprobante_ingreso_varios.secuencia',
                    'ct_comprobante_ingreso_varios.id_asiento_cabecera',
                    'ct_comprobante_ingreso_varios.fecha',
                    'ct_comprobante_ingreso_varios.observaciones',
                    'ct_comprobante_ingreso_varios.concepto',
                    'ct_comprobante_ingreso_varios.total_ingreso',
                    'cb.nombre as nombre_caja',
                    'cd.descripcion as divisas',
                    'ct_comprobante_ingreso_varios.comentarios'
                ]);
        }
    }

    public static function SaveCompIngresovarios($r)
    {
        //dd($r);
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        $data = $r->input();
        $origin = $r->input();
        unset($data['d']);
        unset($data['_token']);
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');

        try {
            $cod_sucurs = PuntoEmision::where('estado', '1')
                ->where('id', $data["sucursal"])->first();

            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = Caja::where('estado', '1')->where('id_sucursal', $cod_sucurs->id)->where('id_empresa', $id_empresa)->first();
            if ($cod_caj == '') {
                DB::rollBack();
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    //'reload' => '',
                    'message' => 'No existe punto de emision para esta sucursal.',
                ];
                return $arrayRespuesta;
            }
            $c_caja = $cod_caj->codigo_caja;
            $numero_factura = ComprobanteIngresoVarios::generarNumeroCompIngresoVarios();

            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $numero_factura,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->concepto . 'por la cantidad de ' . $r->total_ingresos,
                'valor' => $r->total_ingresos,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];
            $id_asiento_cabecera = AsientosCabecera::insertGetId($arrayAsientoCabecera);
            //$id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'comprobante_ingreso', $arrayAsientoCabecera,  $r);
            //dd($id_asiento_cabecera,AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
            if (isset($id_asiento_cabecera["status"])) {
                throw new Exception($id_asiento_cabecera["message"]);
            }

            $caja_banco = CajaBanco::where('estado', '1')->where('id', $r->id_banco)->first();
            if ($caja_banco == '') {
                DB::rollBack();
                $arrayRespuestab = [
                    'status' => 'error',
                    'code' => 500,
                    //'reload' => '',
                    'message' => 'El banco seleccionado no existe',
                ];
                return $arrayRespuestab;
            }
            $cuenta = Plan_Cuentas::where('id', $caja_banco->cuenta_mayor)->first();

            $Asientos_detalle = [
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $caja_banco->cuenta_mayor,
                'descripcion'                   => $cuenta->nombre,
                'fecha'                         => $r->fecha,
                'debe'                          => $r->total_ingresos,
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip,
                'ip_modificacion'               => $ip,
            ];
            AsientosDetalles::insertGetId($Asientos_detalle);

            $input_comprobante = [
                'observaciones'       => $r->concepto . ' por la cantidad de ' . $r->total_ingresos,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $r->fecha,
                'id_banco'            => $r->id_banco,
                'secuencia'           => $numero_factura,
                'divisas'             => $r->divisas,
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $r->total_ingresos,
                'concepto'            => $r->concepto,
                'comentarios'         => $r->nota,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip,
                'ip_modificacion'     => $ip,
            ];
            $id_comprobante = ComprobanteIngresoVarios::insertGetId($input_comprobante);
            for ($i = 0; $i < count($data["id_pago"]); $i++) {
                $pag_ing_varios = [
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $r['fecha_pago'][$i],
                    'numero'          => $r['numero'][$i],
                    'id_tipo_tarjeta' => $r['id_tarjetas'][$i],
                    'id_banco'        => $r['id_bancos'][$i],
                    'id_tipo'         => $r['id_pago'][$i],
                    'total'           => $r['valor'][$i],
                    'cuenta'          => $r['cuenta'][$i],
                    'girador'         => $r['giradoA'][$i],
                    'estado'          => '1',
                    'ip_creacion'     => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                $det_pago_varios = DetallePagoIngresoVarios::insertGetId($pag_ing_varios);

                $pag_ing = [
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $r['fecha_pago'][$i],
                    'numero'          => $r['numero'][$i],
                    'id_tipo_tarjeta' => $r['id_tarjetas'][$i],
                    'id_banco'        => $r['id_bancos'][$i],
                    'id_tipo'         => $r['id_pago'][$i],
                    'new'             => '1',
                    'total'           => $r['valor'][$i],
                    'cuenta'          => $r['cuenta'][$i],
                    'girador'         => $r['giradoA'][$i],
                    'estado'          => '1',
                    'ip_creacion'     => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'tipo_c'          => 'CIV',
                ];
                $det_pago = DetallePagoIngreso::insertGetId($pag_ing);
            }
            for ($i = 0; $i < count($data["cuenta_contable"]); $i++) {
                if ($r['haber'][$i] > 0) {
                    $nombre_cuenta = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->where('plan_cuentas.id', $r['cuenta_contable'][$i])->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->first();
                    $detcompIngreso = [
                        'id_comprobante_varios'          => $id_comprobante,
                        'fecha'                          => $r->fecha,
                        'codigo'                         => $r['cuenta_contable'][$i],
                        'nombre'                         => $nombre_cuenta->nombre,
                        'haber'                          => $r['haber'][$i],
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip,
                        'ip_modificacion'                => $ip,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ];
                    DetalleComprobanteIngresoVarios::insertGetId($detcompIngreso);

                    $ct_asiento_detalle = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $r['cuenta_contable'][$i],
                        'debe'                => '0',
                        'haber'               => $r['haber'][$i],
                        'descripcion'         => $nombre_cuenta->nombre,
                        'fecha'               => $r->fecha,
                        'ip_creacion'         => $ip,
                        'ip_modificacion'     => $ip,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ];
                    AsientosDetalles::insertGetId($ct_asiento_detalle);
                }
            }

            $r->c = 'Clientes';
            $r->s = 'SaveCompIngresoVarios';
            $r->d = $origin['d'];
            $r->m = ComprobanteIngresoVarios::$modelo;
            $r->o = 'Se agrego el nuevo comprobante de ingreso varios con id.: ' . $id_comprobante;
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                //'reload' => '',
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'SaveCompIngresoVarios';
            $r->d =  $origin['d'];
            $r->m = ComprobanteIngresoVarios::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'id_asiento' => $id_asiento_cabecera,
                'id' => $id_comprobante,
                'numero' => $numero_factura,
                'tipo' => 'compIngresoVarios',
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }

    public static function anularCompIngresoVarios($r)
    {
        DB::beginTransaction(); 
        DB::enableQueryLog();
        $cont = 0;
        $ip = Utilidades::getRealIP(); 
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        try {
            $comp_ingreso_varios = ComprobanteIngresoVarios::where('estado', '1')->where('id', $r->id)->first();
            if (!is_null($comp_ingreso_varios)) {
                $input = [
                    'estado'            => '0',
                    'nota'              => $r->observacion, 
                    'ip_modificacion'   => $ip,
                    'id_usuariomod'     => $idusuario,
                ];
                ComprobanteIngresoVarios::where('estado', '1')->where('id', $r->id)->update($input);
                
                $asiento = AsientosCabecera::where('estado', '1')->where('id', $comp_ingreso_varios->id_asiento_cabecera)->where('id_empresa', $id_empresa)->first();
                
                if (!is_null($asiento)) {
                    $asiento_detalles = $asiento->detalles;
                    $id_asiento = [
                        'observacion'     => 'ANULACIÓN ' .$r->observacion,
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comp_ingreso_varios->secuencia,
                        'valor'           => $asiento->valor,
                        'ip_creacion'     => $ip,
                        'ip_modificacion' => $ip,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $id_asiento_cabecera = AsientosCabecera::insertGetId($id_asiento);

                    foreach ($asiento_detalles as $value) {
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'debe'                => $value->haber,
                            'haber'               => $value->debe,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => $asiento->fecha_asiento,
                            'ip_creacion'         => $ip,
                            'ip_modificacion'     => $ip,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario
                        ]);
                    }
                    LogAsiento::anulacion('CIV', $id_asiento_cabecera, $asiento->id);
                }
                $r->c = 'Clientes';
                $r->s = 'AnularCompIngresoVarios';
                $r->m = ComprobanteIngresoVarios::$modelo;
                $r->o = 'Se anulo el comprobante de ingreso vario con id.: ' . $r->id;
                Auditorias::saveAuditoria($r, DB::getQueryLog());
                $cont++;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                //'reload' => '',
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'AnularCompIngresoVarios';
            $r->m = ComprobanteIngresoVarios::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro anulado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }

    private static function generarNumeroCompIngresoVarios()
    {
        $num = ComprobanteIngresoVarios::where('id_empresa', session('id_empresa'))->max('id');
        if ($num == '')
            $num = '0000000001';
        else {
            $num = $num + 1;
            $num = str_pad($num, 10, 0, STR_PAD_LEFT);
        }
        return $num;
    }

    public static function datosPdf($id)
    {
        $comp_ingreso_varios = ComprobanteIngresoVarios::where('id', $id)->first();
        $empresa = Empresas::where('id', $comp_ingreso_varios->id_empresa)->first();
        $asiento_cabecera = AsientosCabecera::where('id', $comp_ingreso_varios->id_asiento_cabecera)->first();
        $asiento_detalles = null;
        if (!is_null($asiento_cabecera)) {
            $asiento_detalles = $asiento_cabecera->detalles;
        }
       
        return compact("comp_ingreso_varios", "empresa","asiento_cabecera", "asiento_detalles");
    }

    public function usuario(){
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

}
