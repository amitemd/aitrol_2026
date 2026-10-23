<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\Configuraciones;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\DetallePagoIngreso;
use Sis_medico\v3\contable\DetalleComprobanteIngreso;
use Sis_medico\v3\contable\Empresas;

class ComprobanteIngreso extends Model
{
    private static $modelo = 'ComprobanteIngreso';
    protected $table = 'ct_comprobante_ingreso';
    protected $primaryKey  = 'id';

    public static function ingresosOperativos($anio = '')
    {
        if ($anio == '') {
            $anio = date('Y');
            $anio_ = $anio - 1;
        } else {
            $anio_ = $anio - 1;
        }
        return DB::table('ct_comprobante_ingreso as ci')
            ->select(DB::raw('YEAR(ci.fecha) as anio'))
            ->addSelect(DB::raw("IF(s.tipo=0,'PUBLICO',IF(s.tipo=1,'PRIVADO',IF(s.tipo=2,'PARTICULAR',''))) as tipo"))
            ->addSelect('s.nombre')
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=1 THEN (dci.total) END),0) as ene'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=2 THEN (dci.total) END),0) as feb'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=3 THEN (dci.total) END),0) as mar'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=4 THEN (dci.total) END),0) as abr'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=5 THEN (dci.total) END),0) as may'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=6 THEN (dci.total) END),0) as jun'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=7 THEN (dci.total) END),0) as jul'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=8 THEN (dci.total) END),0) as ago'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=9 THEN (dci.total) END),0) as sep'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=10 THEN (dci.total) END),0) as oct'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=11 THEN (dci.total) END),0) as nov'))
            ->addSelect(DB::raw('IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=12 THEN (dci.total) END),0) as dic'))
            ->addSelect(DB::raw('(IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=1 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=2 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=3 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=4 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=5 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=6 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=7 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=8 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=9 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=10 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=11 THEN (dci.total) END),0) +
                        IFNULL(SUM(CASE WHEN MONTH(ci.fecha)=12 THEN (dci.total) END),0)) as total'))
            ->join('ct_detalle_comprobante_ingreso as dci', 'ci.id', '=', 'dci.id_comprobante')
            ->join('ct_ventas as v', 'dci.id_factura', '=', 'v.id')
            ->leftJoin('seguros as s', 's.id', '=', 'v.seguro_paciente')
            ->where('ci.id_empresa', session('id_empresa'))
            ->where('ci.estado', '!=', 0)
            ->whereIn('v.tipo', ['VEN-FA', 'VENFA-CO'])
            ->where(function ($query) use ($anio, $anio_) {
                $query->whereRaw('YEAR(ci.fecha) = ' . $anio_)
                    ->orWhereRaw('YEAR(ci.fecha) = ' . $anio);
            })
            ->groupBy('s.id', DB::raw('YEAR(ci.fecha)'))
            ->orderBy('s.tipo')
            ->get();
    }

    public static function getDetalleDeudaCliente($r)
    {
        $cliente = explode('|', $r->cliente);
        $id_proveedor = $cliente[0];
        $id_empresa   = session('id_empresa');
        $data         = 0;
        $facturas     = '[]';
        DB::enableQueryLog();
        return Clientes::join('ct_ventas as co', 'co.id_cliente', 'ct_clientes.identificacion')
            ->where('co.id_cliente', $id_proveedor)
            ->where('co.id_empresa', $id_empresa)
            ->where('co.valor_contable', '>', '0')
            ->where('co.estado', '<>', '0')
            ->orderBy('co.fecha', 'asc')
            ->select('co.valor_contable', 'co.numero', 'co.id', 'co.tipo', 'co.concepto', 'co.fecha', 'co.nro_comprobante', DB::raw('DATE_ADD(co.fecha, INTERVAL IFNULL(co.dias_plazo,0) DAY) as fecha_vencimiento'))
            ->get();
    }

    public static function getObtenerAnticipos($r)
    {
        $cliente = explode('|', $r->cliente);
        $id_proveedor = $cliente[0];
        $id_empresa   = session('id_empresa');
        $data         = 0;
        $facturas     = '[]';
        DB::enableQueryLog();
        return ComprobanteIngreso::where('id_cliente', $id_proveedor)
            ->where('id_empresa', $id_empresa)
            ->where('deficit_ingreso', '>', '0')
            ->where('estado', '1')
            ->where('tipo', '2')
            ->select('fecha', 'secuencia', 'observaciones', 'deficit_ingreso', 'id')
            ->get();
    }

    public static function getCompIngreso($id = '')
    {
        if ($id == '') {
            return ComprobanteIngreso::leftjoin('users as uc', 'ct_comprobante_ingreso.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_clientes as c', 'ct_comprobante_ingreso.id_cliente', 'c.identificacion')
                ->where('ct_comprobante_ingreso.id_empresa', session('id_empresa'))
                ->whereYear('ct_comprobante_ingreso.fecha', session('periodo'))
                ->orderBy('ct_comprobante_ingreso.id', 'desc')->get([
                    'ct_comprobante_ingreso.id',
                    'ct_comprobante_ingreso.secuencia',
                    'ct_comprobante_ingreso.id_asiento_cabecera',
                    'ct_comprobante_ingreso.id_cliente',
                    'ct_comprobante_ingreso.fecha',
                    'ct_comprobante_ingreso.observaciones',
                    'ct_comprobante_ingreso.total_ingreso',
                    'c.nombre',
                    'ct_comprobante_ingreso.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido'
                ]);
        } else {
            return ComprobanteIngreso::leftjoin('users as uc', 'ct_comprobante_ingreso.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_clientes as c', 'ct_comprobante_ingreso.id_cliente', 'c.identificacion')
                ->leftjoin('users as uv', 'ct_comprobante_ingreso.id_vendedor', 'uv.id')
                ->where('ct_comprobante_ingreso.id_empresa', session('id_empresa'))
                ->where('ct_comprobante_ingreso.id', $id)
                ->orderBy('ct_comprobante_ingreso.id', 'desc')->first([
                    'ct_comprobante_ingreso.id',
                    'ct_comprobante_ingreso.secuencia',
                    'ct_comprobante_ingreso.id_asiento_cabecera',
                    'ct_comprobante_ingreso.id_cliente',
                    'ct_comprobante_ingreso.id_vendedor',
                    'ct_comprobante_ingreso.fecha',
                    'ct_comprobante_ingreso.observaciones',
                    'ct_comprobante_ingreso.total_ingreso',
                    'ct_comprobante_ingreso.sc',
                    'ct_comprobante_ingreso.os',
                    'uv.apellido1 as v_ap1',
                    'uv.apellido2 as v_ap2',
                    'uv.nombre1 as v_nom1',
                    'uv.nombre2 as v_nom2',
                    'c.nombre as nombre_cliente'
                ]);
        }
    }

    public static function estadoCompIngreso($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (ComprobanteIngreso::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro editado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado de comprobante de ingreso',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }

    public static function SaveCompIngreso($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $id_cliente = explode('|', $r->id_cliente);
        $cont = 0;
        $data = $r->input();
        $origin = $r->input();
        unset($data['d']);
        unset($data['_token']);
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        $superavit = (int) $r['superavit'];
        $resultado = json_decode($r['listInvoice']);
        try {
            $cod_sucurs = PuntoEmision::where('estado', '1')
                ->where('id', $data["sucursal"])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = Caja::where('estado', '1')->where('id_sucursal', $cod_sucurs->id)->first();
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
            $numero_factura = ComprobanteIngreso::generarNumeroCompIngreso();
            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $numero_factura,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r["observacion"][0],
                'valor' => $r->valor_total,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'comprobante_ingreso', $arrayAsientoCabecera,  $r);
            //dd($id_asiento_cabecera,AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
            if (isset($id_asiento_cabecera["status"])) {
                throw new Exception($id_asiento_cabecera["message"]);
            }
            $input_comprobante = [
                'observaciones'       => $r->autollenar . ' la cantidad de ' . $r->valor_total,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $r->fecha,
                'id_vendedor'         => $r->id_vendedor,
                'secuencia'           => $numero_factura,
                'tipo'                => '0',
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $r->valor_total,
                'deficit_ingreso'     => $r->valor_total,
                'id_cliente'          => $id_cliente[0],
                'autollenar'          => $r->autollenar,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip,
                'ip_modificacion'     => $ip,
            ];
            $id_comprobante = ComprobanteIngreso::insertGetId($input_comprobante);
            //dd($id_comprobante, count($data["id_pago"]), $r['fecha_pago0'], $r['numero0']);
            for ($i = 0; $i < count($data["id_pago"]); $i++) {
                $pag_ing = [
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
                $det_pago = DetallePagoIngreso::insertGetId($pag_ing);
            }
            for ($i = 0; $i < count($data["id_fac"]); $i++) {
                if ($r['abono_a'][$i] > 0) {
                    $nuevo_saldo  = 0;
                    $nuevo_saldof = $r['abono_a'][$i];
                    $ventas = Ventas::find($r['id_fac'][$i]);
                    if (!is_null($ventas)) {
                        $detcompIngreso = [
                            'id_comprobante'    => $id_comprobante,
                            'fecha'             => $r->fecha,
                            'observaciones'     => $r->autollenar,
                            'id_factura'        => $ventas->id,
                            'secuencia_factura' => $r['numero_fact'][$i],
                            'total_factura'     => $r['saldo_a'][$i],
                            'total'             => $r['abono_a'][$i],
                            'estado'            => '1',
                            'ip_creacion'       => $ip,
                            'ip_modificacion'   => $ip,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                        ];
                        $detcompI = DetalleComprobanteIngreso::insertGetId($detcompIngreso);

                        if ($r['abono_a'][$i] > ($ventas->valor_contable)) {
                            $nuevo_saldo = $r['abono_a'][$i] - $ventas->valor_contable;
                        } else {
                            $nuevo_saldo = $ventas->valor_contable - $r['abono_a'][$i];
                        }
                        if ($nuevo_saldo != 0) {
                            $input_actualiza = [
                                'estado_pago'     => '2',
                                'valor_contable'  => $nuevo_saldo,
                                'ip_modificacion' => $ip,
                                'id_usuariomod'   => $idusuario,
                            ];
                        } else {
                            $input_actualiza = [
                                'estado_pago'     => '3',
                                'valor_contable'  => $nuevo_saldo,
                                'ip_modificacion' => $ip,
                                'id_usuariomod'   => $idusuario,
                            ];
                        }
                        Ventas::where('id', $r['id_fac'][$i])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->update($input_actualiza);
                    }
                }
            }
            $r->c = 'Clientes';
            $r->s = 'SaveCompIngreso';
            $r->d = $origin['d'];
            $r->m = ComprobanteIngreso::$modelo;
            $r->o = 'Se agrego el nuevo comprobante de ingreso con id.: ' . $id_comprobante;
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            //}
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
            $r->s = 'SaveCompIngreso';
            $r->d =  $origin['d'];
            $r->m = ComprobanteIngreso::$modelo;
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
                'tipo' => 'compIngreso',
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }

    public static function SaveSuperavit($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $id_cliente = explode('|', $r->id_cliente);
        $cont = 0;
        $data = $r->input();
        $origin = $r->input();
        unset($data['d']);
        unset($data['_token']);
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        try {
            $numero_factura = ComprobanteIngreso::generarNumeroCompIngreso();
            $cod_sucurs = PuntoEmision::where('estado', '1')
                ->where('id', $data["sucursal"])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = Caja::where('estado', '1')->where('id_sucursal', $cod_sucurs->id)->first();
            if ($cod_caj == '') {
                DB::rollBack();
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'reload' => '',
                    'message' => 'No existe punto de emision para esta sucursal.',
                ];
                return $arrayRespuesta;
            }
            $c_caja = $cod_caj->codigo_caja;
            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $numero_factura,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r["autollenar"],
                'valor' => $r->total_suma,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];

            $id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'comprobanteIngresoDeficit', $arrayAsientoCabecera,  $r);
         
            //dd(AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
 
            $input_comprobante = [
                'observaciones'       => $r->autollenar . ' anticipo de cliente ',
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $r->fecha,
                'secuencia'           => $numero_factura,
                'tipo'                => '2',
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $r->valor_total,
                'deficit_ingreso'     => $r->valor_total,
                'id_cliente'          => $id_cliente[0],
                'autollenar'          => $r->autollenar,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip,
                'ip_modificacion'     => $ip,
            ];
            $id_comprobante = ComprobanteIngreso::insertGetId($input_comprobante);
            for ($i = 0; $i < count($data["id_pago"]); $i++) {
                $pag_ing = [
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
                $det_pago = DetallePagoIngreso::insertGetId($pag_ing);
            }
            $r->c = 'Clientes';
            $r->s = 'SaveSuperavit';
            $r->d = $origin['d'];
            $r->m = ComprobanteIngreso::$modelo;
            $r->o = 'Se agrego el nuevo comprobante de ingreso con id.: ' . $id_comprobante;
            $cont++;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'reload' => '',
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'SaveSuperavit';
            $r->d =  $origin['d'];
            $r->m = ComprobanteIngreso::$modelo;
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
                'tipo' => 'compIngreso',
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }

    public static function anularCompIngreso($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $data = $r->input();
        $origin = $r->input();
        $cont = 0;
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        try {

            $comp_ingreso = ComprobanteIngreso::where('estado', '1')->where('id', $r->id)->first();
            $detallesa    = DetalleComprobanteIngreso::where('id_comprobante', $r->id)->get();
            if (!is_null($comp_ingreso)) {

                $comp_ingreso->estado =  0;
                $comp_ingreso->ip_creacion = $ip;
                $comp_ingreso->ip_modificacion =  $ip;
                $comp_ingreso->id_usuariomod = $idusuario;
                $comp_ingreso->save();
                foreach ($detallesa as $value) {
                    $consulta_venta = Ventas::where('id', $value->id_factura)->where('id_empresa', $id_empresa)->where('estado', '1')->first();
                    if (!is_null($consulta_venta)) {
                        $valor           = $consulta_venta->valor_contable;
                        $suma            = ($value->total) + $valor;
                        $consulta_venta->valor_contable  = $suma;
                        $consulta_venta->ip_modificacion = $ip;
                        $consulta_venta->id_usuariomod   = $idusuario;
                        $consulta_venta->save();
                    }
                }

                $asiento = AsientosCabecera::find($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->save();

                $detalles   = $asiento->detalles;
                $id_asiento = AsientosCabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   => date("Y-m-d"),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => Utilidades::getRealIP(),
                    'ip_modificacion' => Utilidades::getRealIP(),
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);

                foreach ($detalles as $value) {
                    AsientosDetalles::insert([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => Utilidades::getRealIP(),
                        'ip_modificacion'     => Utilidades::getRealIP(),
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
                $r->c = 'Clientes';
                $r->s = 'AnularCompIngreso';
                $r->m = ComprobanteIngreso::$modelo;
                $r->o = 'Se anulo el comprobante de ingreso con id.: ' . $r->id;
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
            $r->s = 'AnularCompIngreso';
            $r->m = ComprobanteIngreso::$modelo;
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

    private static function generarNumeroCompIngreso()
    {
        $num = ComprobanteIngreso::where('id_empresa', session('id_empresa'))->max('id');
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
        $comp_ingreso = ComprobanteIngreso::where('id', $id)->first();
        $cliente = Clientes::where('identificacion', $comp_ingreso->id_cliente)->first();
        $empresa = Empresas::where('id', $comp_ingreso->id_empresa)->first();
        $detalle_deudas_cliente = DetalleComprobanteIngreso::where('id_comprobante', $comp_ingreso->id)
            ->join('ct_ventas as v', 'v.id', 'ct_detalle_comprobante_ingreso.id_factura')
            ->get(['v.nro_comprobante', 'v.tipo', 'v.procedimientos', 'ct_detalle_comprobante_ingreso.fecha', 'ct_detalle_comprobante_ingreso.total_factura', 'ct_detalle_comprobante_ingreso.total', DB::raw('DATE_ADD(v.fecha, INTERVAL IFNULL(v.dias_plazo,0) DAY) as fecha_vencimiento')]);
        $detalle_pago_ingreso = DetallePagoIngreso::where('ct_detalle_pago_ingreso.id_comprobante', $id)
            ->leftjoin('ct_tipo_pago as tp', 'tp.id', 'ct_detalle_pago_ingreso.id_tipo')
            ->leftjoin('ct_tipo_tarjeta as tt', 'tt.id', 'ct_detalle_pago_ingreso.id_tipo_tarjeta')
            ->leftjoin('ct_bancos as b', 'b.id', 'ct_detalle_pago_ingreso.id_banco')
            ->get([
                'tp.nombre as tipo_pago', 'ct_detalle_pago_ingreso.fecha', 'ct_detalle_pago_ingreso.numero', 'b.nombre as banco', 'tt.nombre as tarjeta',
                'ct_detalle_pago_ingreso.cuenta', 'ct_detalle_pago_ingreso.girador', 'ct_detalle_pago_ingreso.total'
            ]);
        return compact("comp_ingreso", "empresa", "detalle_pago_ingreso", "cliente", "detalle_deudas_cliente");
    }
}
