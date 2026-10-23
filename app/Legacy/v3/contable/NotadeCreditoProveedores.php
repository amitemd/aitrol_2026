<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotadeCreditoProveedores extends Model
{
    private static $modelo = 'NotadeCreditoProveedores';
    protected $table = 'ct_credito_acreedores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNotasdeCredito($r)
    {
        DB::enableQueryLog();
        /*(select sum(subtotal_0) from `ct_credito_acreedores` where `ct_credito_acreedores`.`fecha` between '2021-01-01 00:00:00' and '2023-12-20 23:59:59' 
         and (`ct_credito_acreedores`.`id_proveedor` = '0991420460001')) as sumSubtotal0,*/
        if ($r->proveedor == '') {
            return NotadeCreditoProveedores::selectRaw(
                'ct_credito_acreedores.id_proveedor,
                p.nombrecomercial,
                ct_credito_acreedores.id,
                ct_credito_acreedores.fecha,
                ct_credito_acreedores.secuencia_factura,
                c.numero,
                ct_credito_acreedores.id_asiento_cabecera,
                ct_credito_acreedores.concepto,
                ct_credito_acreedores.subtotal_0,
                ct_credito_acreedores.subtotal_12,
                ct_credito_acreedores.subtotal,
                ct_credito_acreedores.impuesto,
                ct_credito_acreedores.valor_contable,
                (select sum(subtotal_0) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as sumSubtotal0,
                (select sum(subtotal_12) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as sumSubtotal12,
                (select sum(subtotal) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as sumSubtotal,
                (select sum(impuesto) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as sumImpuesto,
                (select sum(valor_contable) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as sumTotal'
            )
                ->join('ct_compras as c', 'ct_credito_acreedores.id_compra', 'c.id')
                ->join('proveedor as p', 'ct_credito_acreedores.id_proveedor', 'p.id')
                ->whereBetween('ct_credito_acreedores.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get();
        } else {
            return NotadeCreditoProveedores::selectRaw(
                'ct_credito_acreedores.id_proveedor,
                p.nombrecomercial,
                ct_credito_acreedores.id,
                ct_credito_acreedores.fecha,
                ct_credito_acreedores.secuencia_factura,
                c.numero,
                ct_credito_acreedores.id_asiento_cabecera,
                ct_credito_acreedores.concepto,
                ct_credito_acreedores.subtotal_0,
                ct_credito_acreedores.subtotal_12,
                ct_credito_acreedores.subtotal,
                ct_credito_acreedores.impuesto,
                ct_credito_acreedores.valor_contable,
                (select sum(subtotal_0) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" AND (ct_credito_acreedores.id_proveedor= ' . $r->proveedor . ')) as sumSubtotal0,
                (select sum(subtotal_12) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" AND (ct_credito_acreedores.id_proveedor= ' . $r->proveedor . ')) as sumSubtotal12,
                (select sum(subtotal) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" AND (ct_credito_acreedores.id_proveedor= ' . $r->proveedor . ')) as sumSubtotal,
                (select sum(impuesto) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" AND (ct_credito_acreedores.id_proveedor= ' . $r->proveedor . ')) as sumImpuesto,
                (select sum(valor_contable) from ct_credito_acreedores where ct_credito_acreedores.fecha between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" AND (ct_credito_acreedores.id_proveedor= ' . $r->proveedor . ')) as sumTotal'
            )
                ->join('ct_compras as c', 'ct_credito_acreedores.id_compra', 'c.id')
                ->join('proveedor as p', 'ct_credito_acreedores.id_proveedor', 'p.id')
                ->whereBetween('ct_credito_acreedores.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->where('ct_credito_acreedores.id_proveedor', $r->proveedor)
                ->get();
        }
    }
    public static function allNotasdeCredito($id = '')
    {
        DB::enableQueryLog();
        if ($id == '') {
            return NotadeCreditoProveedores::join('ct_compras as c', 'ct_credito_acreedores.id_compra', 'c.id')
                ->join('proveedor as p', 'ct_credito_acreedores.id_proveedor', 'p.id')
                ->join('users as uc', 'ct_credito_acreedores.id_usuariocrea', 'uc.id')
                ->leftJoin('users as um', 'ct_credito_acreedores.id_usuariomod', '=', 'um.id')
                ->orderBy('ct_credito_acreedores.id', 'desc')
                //->whereBetween('ct_credito_acreedores.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get([
                    'ct_credito_acreedores.id_proveedor',
                    'p.nombrecomercial',
                    'ct_credito_acreedores.id',
                    'ct_credito_acreedores.fecha',
                    'ct_credito_acreedores.secuencia_factura',
                    'c.numero',
                    'ct_credito_acreedores.id_asiento_cabecera',
                    'ct_credito_acreedores.concepto',
                    'ct_credito_acreedores.subtotal_0',
                    'ct_credito_acreedores.subtotal_12',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_credito_acreedores.subtotal',
                    'ct_credito_acreedores.impuesto',
                    'ct_credito_acreedores.valor_contable',
                    'ct_credito_acreedores.estado',
                    DB::raw('CONCAT(ct_credito_acreedores.serie, " - ",ct_credito_acreedores.secuencia) AS nro_comprobante'),
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_credito_acreedores.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } else {
            return NotadeCreditoProveedores::join('ct_compras as c', 'ct_credito_acreedores.id_compra', 'c.id')
                ->join('proveedor as p', 'ct_credito_acreedores.id_proveedor', 'p.id')
                ->join('users as uc', 'ct_credito_acreedores.id_usuariocrea', 'uc.id')
                ->where('ct_credito_acreedores.id', $id)
                ->orderBy('ct_credito_acreedores.id', 'desc')
                ->get([
                    'ct_credito_acreedores.id_proveedor',
                    'p.nombrecomercial',
                    'ct_credito_acreedores.id',
                    'ct_credito_acreedores.fecha',
                    'ct_credito_acreedores.secuencia_factura',
                    'c.numero',
                    'ct_credito_acreedores.id_asiento_cabecera',
                    'ct_credito_acreedores.concepto',
                    'ct_credito_acreedores.subtotal_0',
                    'ct_credito_acreedores.subtotal_12',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_credito_acreedores.subtotal',
                    'ct_credito_acreedores.impuesto',
                    'ct_credito_acreedores.valor_contable',
                    'ct_credito_acreedores.estado'
                ]);
        }
    }
    public static function saveNuevaNotaCredito($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        $tipo_nota = 0;
        try {
            //dd($r->all());
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            //dd($data);
            if ($data['cantidad_detalles'] != $data['cantidad_ok']) {
                $tipo_nota = 1;
            }
            //dd($data,$tipo_nota);
            //$punto_emision = PuntoEmision::getPuntoEmision($r->emision);
           //$sucursal = Sucursales::getSucursal($r->establecimiento);
            //dd($sucursal->codigo_sucursal);
            $numero_factura =  LogAsiento::getSecuencia(7, 1);
            $tdetalle = $r->a_detalles;
            $adetalle = explode(',', $tdetalle);
            //dd(($r->total));
            if($r->total != 0){
                if ($data['total'] < $data['valor_contable']) {
                    unset($data['id']);
                    $nc_cab['id_usuariocrea'] = Auth::user()->id;
                    $nc_cab['id_usuariomod'] = Auth::user()->id;
                    $nc_cab['ip_creacion'] = $ip;
                    $nc_cab['ip_modificacion'] = $ip;
                    //$nc_cab['id_asiento_cabecera'] = $id_asiento_cabecera;
                    $nc_cab['id_empresa'] = session('id_empresa');
                    $nc_cab['id_compra'] = $data['id_factura'];
                    $nc_cab['estado'] = 1;
                    $nc_cab['fecha'] = $data['fecha_nota'];
                    $nc_cab['fecha_caducidad'] = $data['fecha_caducidad'];
                    $nc_cab['id_proveedor'] = $data['id_proveedor_'];
                    $nc_cab['autorizacion'] = $data['autorizacion_nota'];
                    $nc_cab['serie'] = $r->establecimiento . '-' . $r->emision;
                    $nc_cab['secuencia'] = $data['secuencia'];
                    $nc_cab['secuencia_factura'] = $data['numero'];
                    $nc_cab['fechand'] = $data['fecha_nota'];
                    $nc_cab['concepto'] = $data['descripcion'];
                    $nc_cab['id_credito_tributario'] = $r->credito_tributario;
                    $nc_cab['id_tipo_comprobante'] = $r->tipo_comprobante;
                    $nc_cab['fecha_factura'] = $data['fecha'];
                    $nc_cab['serie_factura'] = $data['serie'];
                    $nc_cab['autorizacion_factura'] = $data['autorizacion'];
                    $nc_cab['subtotal'] = $data['subtotal_total'];
                    $nc_cab['impuesto'] = $data['impuesto'];
                    $nc_cab['valor_contable'] = $data['total'];
                    $nc_cab['nro_comprobante'] = $numero_factura;
                    $nc_cab['subtotal_12'] = $data['subtotal12'];
                    $nc_cab['subtotal_0'] = $data['subtotal0'];
                    $nc_cab['saldo_ant'] = $data['valor_contable'];
                    $nc_cab['nota'] = $data['nota'];
                    $nc_cab['tipo_nota'] = $tipo_nota;
                    //dd($nc_cab);
                    $id = NotadeCreditoProveedores::insertGetId($nc_cab);
                    $compra = Compras::where('id', $r->id_factura);
                    //dd($compra);
                    $v_contable = $r->valor_contable - floatval($r->total);
                    //dd($v_contable);
                    //dd("aqui");
                    $arr_comp = [
                        'valor_contable'  => $v_contable,
                    ];
                    //dd("aquiaa");
                    $compra->update($arr_comp);
                    

                    $trow = $r->a_row;
                    $arow = explode(',', $trow);
                    //dd($arow);
                    //dd($adetalle);
                    $cont1 = 0;
                    $valor_iva = 0;
                    foreach ($adetalle as $det) {
                        $detalle_compra = CtDetalleCompra::find($det);
                        $indice = $arow[$cont1];
                        $abono = $r->abono[$indice];
                        if($detalle_compra->iva == 1){
                            $valor_iva = $abono * $detalle_compra->porcentaje;
                        }
                        //dd($abono);
                        $arr_det_nd = [
                            'id_debito'         => $id,
                            'codigo'            => $detalle_compra->codigo,
                            'nombre'            => $detalle_compra->nombre,
                            'fecha'             => $r->fecha,
                            'vencimiento'       => $r->fecha,
                            'valor'             => $abono,
                            //'cantidad'          => $detalle_compra->cantidad,
                            'iva'               => $detalle_compra->iva,
                            'id_usuariocrea'    => Auth::user()->id,
                            'id_usuariomod'     => Auth::user()->id,
                            'ip_creacion'       => $ip,
                            'ip_modificacion'   => $ip,
                            'valor_iva'         => $valor_iva,
                            'total'             => $abono + $valor_iva,
                            'id_det_compra'     => $det,
                        ];
                        $cont1++;
                        //dd($arr_det_nd);
                        //dd($arr_det_nd);
                        NotadeCreditoDetalleProveedores::insert($arr_det_nd);
                    }
                    /// /// FATLTA EL ASIENTO ///////.
                    //dd($nc_cab,$id);
                    /*DB::rollBack();
                        $arrayAsientoCabecera = [
                            'sucursal' => $sucursal->codigo_sucursal,
                            'punto_emision' => $punto_emision->codigo_sucursal,
                            'fecha_asiento' => $r->fecha,
                            'fact_numero' => $numero_factura,
                            'id_empresa' => session('id_empresa'),
                            'observacion' => 'NOTA DE CREDITO PROVEEDORES:' . $numero_factura,
                            'total' => $r->total,
                            'subtotal' => $r->subtotal,
                            'impuesto' => $r->impuesto,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'tipo_nota' => $tipo_nota, // 0 = Total --- 1 = Parcial
                        ];
                        $id_asiento_cabecera = Contabilidad::crearAsiento('compras', 'nota_credito', $arrayAsientoCabecera,  $r); 
                        if($r->tipo == '1') //// Es una factura de producto //////
                        { 
                            /////////// CREA SOLO DETALLES DEL ASIENTO ///////// 
                        }else if ($r->tipo == 2) {
                            /// PREGUNTA SI EXISTE UNA FACTURA DE ACTIVO FIJO POR EL ASIENTO DE LA COMPRA ///
                            /// SI existe se hace un asiento detalle por 
                            /// Caso contrario hace un asiento por factura de compra
                        }
                        $nota_credito = NotadeCreditoProveedores::find($id);
                        $arr=[
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                        ];
                        $nota_credito->update($arr);*/
                    $r->c = 'Proveedores';
                    $r->s = 'saveNotaCreditoProveedores';
                    $r->d = $origin['d'];
                    $r->m = NotadeCreditoProveedores::$modelo;
                    $r->o = 'Se agrego nueva nota de credito id.: ' . $id;
                    $cont++;
                } else {
                    DB::rollBack();
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => "El total de la nota de crédito" . ' ' . $data['total'] . '  ' . "supera el valor contable" . ' ' . $data['valor_contable'] . ' ' . "de la factura",
                    ];
                    $r->c = 'Proveedores';
                    $r->s = 'saveNotaCreditoProveedores';
                    $r->d =  $origin['d'];
                    $r->m = NotadeCreditoProveedores::$modelo;
                    //$r->o = 'Ocurrio un error: ' . $e->getMessage();
                    Auditorias::saveAuditoria($r, DB::getQueryLog());
                    return json_encode($arrayRespuesta);
                }
                Auditorias::saveAuditoria($r, DB::getQueryLog());
            }
            else {
                DB::rollBack();
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => "Seleccione al menos un detalle de compra para guardar la nota de crédito",
                ];
                $r->c = 'Proveedores';
                $r->s = 'saveNotaCreditoProveedores';
                $r->d =  $origin['d'];
                $r->m = NotadeCreditoProveedores::$modelo;
                //$r->o = 'Ocurrio un error: ' . $e->getMessage();
                Auditorias::saveAuditoria($r, DB::getQueryLog());
                return json_encode($arrayRespuesta);
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());

        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Proveedores';
            $r->s = 'saveNotaCreditoProveedores';
            $r->d =  $origin['d'];
            $r->m = NotadeCreditoProveedores::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                //'tipo' => 'notadecreditoproveedores',
                'status' => 'success',
                'code' => 200,
                'reload' => '',
                'message' => 'Registro guardado correctamente', 
            ];
            return $arrayRespuesta;
        }
    }
    public static function anularNotaCredito($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $origin = $r->input();
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $nota_credito = NotadeCreditoProveedores::where('estado', '1')->where('id', $r->id)->first();
            if ($nota_credito != '') {
                $consulta_compra = Compras::where('id', $nota_credito->id_compra)->where('estado', '>', '0')->first();
                //dd($consulta_compra);
                if (!is_null($consulta_compra)) {
                    $valor = $consulta_compra->valor_contable + $nota_credito->valor_contable;
                    //dd($valor, $consulta_compra->valor_contable,$nota_credito->valor_contable);
                    $input_actualiza = [
                        'valor_contable'                => $valor,
                        'ip_modificacion'               => $ip,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    Compras::where('id', $nota_credito->id_compra)->where('estado', '>', '0')->update($input_actualiza);
                }
                $input = [
                    'observacion_anulacion'         => $r->observacion,
                    'estado'                        => '0',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                NotadeCreditoProveedores::where('estado', '1')->where('id', $r->id)->update($input);

                //$id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'cruce_cuenta', $cabecera,  $r);
                //aqui va reverso de asientos
                //LogAsiento::anulacion("CL-CC", $id_asiento, $comp_ingreso->id_asiento_cabecera);

                $r->c = 'Proveedores';
                $r->s = 'anularNotaCredito';
                //$r->d = $origin['d'];
                $r->m = NotadeCreditoProveedores::$modelo;
                $r->o = 'Se anulo la nota de credito con id.: ' . $r->id;
                $cont++;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Proveedores';
            $r->s = 'anularNotaCredito';
            //$r->d =  $origin['id'];
            $r->m = NotadeCreditoProveedores::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }

        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro anulado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }
    public static function getNotadeCreditoPrint($id)
    {
        dd("adadas");
        $contenidoHtml = '';
        //$contenidoHtml = 'Busqueda sin resultados';
        $notadecredito = NotadeCreditoProveedores::join('users as uc', 'ct_credito_acreedores.id_usuariocrea', 'uc.id')
            ->join('proveedor as pv', 'pv.id', 'ct_credito_acreedores.id_proveedor')
            ->join('ct_compras as c', 'c.id', 'ct_credito_acreedores.id_compra')
            ->where('ct_credito_acreedores.id', $id)
            ->get([
                'ct_credito_acreedores.id',
                'ct_credito_acreedores.fecha',
                'ct_credito_acreedores.id_proveedor',
                'pv.razonsocial',
                'pv.direccion',
                'pv.telefono1',
                'pv.telefono2',
                'pv.email',
                'ct_credito_acreedores.tipo_nota',
                'ct_credito_acreedores.id_usuariocrea',
                'ct_credito_acreedores.concepto',
                'ct_credito_acreedores.nota',
                'c.numero as id_compra',
                'ct_credito_acreedores.nro_comprobante',
                'ct_credito_acreedores.serie',
                'ct_credito_acreedores.subtotal',
                'ct_credito_acreedores.subtotal_0',
                'ct_credito_acreedores.subtotal_12',
                'ct_credito_acreedores.impuesto',
                'ct_credito_acreedores.valor_contable',
                'uc.nombre1 as ucnombre',
                'uc.apellido1 as ucapellido',
                'ct_credito_acreedores.estado',
                'ct_credito_acreedores.serie',
                'ct_credito_acreedores.autorizacion_factura',
                'ct_credito_acreedores.autorizacion'
            ]);
        if (count($notadecredito) > 0) {
            $fechaMax = date("d/m/Y", strtotime($notadecredito[0]->fecha_caducidad));
            $tipo_nota = "";
            if($notadecredito[0]->tipo_nota == 0){
                $tipo_nota = "Total";
            }
            if($notadecredito[0]->tipo_nota == 1){
                $tipo_nota = "Parcial";
            }

            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($notadecredito[0]->fecha)) . '</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . config('data.empresa')->razonsocial . '</strong><br>
                            ' . config('data.empresa')->id . '<br>
                            ' . config('data.empresa')->direccion . '<br>
                            Teléfono: ' . config('data.empresa')->telefono1 . ' ' . config('data.empresa')->telefono2 . '<br>
                            E-mail: <a href="mailto:' . config('data.empresa')->email . '">' . config('data.empresa')->email . '</a> <br>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . $notadecredito[0]->id_proveedor . '</strong><br>
                            ' . $notadecredito[0]->razonsocial . '<br>
                            ' . $notadecredito[0]->direccion . '<br>
                            Teléfono: ' . $notadecredito[0]->telefono1 . ' ' . $notadecredito[0]->telefono2 . '<br>
                            E-mail: <a href="mailto:' . $notadecredito[0]->email . '">' . $notadecredito[0]->email . '</a>
                        </address>
                    </div> 
                    <div class="col-sm-4 invoice-col">
                        <b>Nota de credito No.:' . $notadecredito[0]->serie . '-' . $notadecredito[0]->nro_comprobante . '</b><br>
                        <b>Tipo Nota: </b>' . $tipo_nota .'<br>
                        <b>Autorización:</b> <br> <span style="font-size:13px;">' . $notadecredito[0]->autorizacion . '</span>
                    </div>
                    <div class="col-sm-12 invoice-col">
                    <b> Concepto: </b> ' . $notadecredito[0]->concepto  . '<br>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>  
                            <th>Abono</th>
                        </tr>
                    </thead>
                    <tbody>';
            $total_final = 0;

            if (count($notadecredito) > 0) {
                $det_notadecredito = NotadeCreditoDetalleProveedores::where('id_debito', $id)->get();
                //dd($det_notadecredito);
                foreach ($det_notadecredito as $row) {
                    $total_final += $row->total;
                    $contenidoHtml .= '<tr>
                    <td>' . $det_notadecredito[0]->codigo . ' ' . $det_notadecredito[0]->nombre . ' </td>
                    <td align="right">' . number_format($row->total, 4) . '</td>
                    </tr>';
                }
            }

            $contenidoHtml .= '</tbody>
                </table></div></div>
                    <div class="row">
                        <div class="col-6">
                        </div>
                        <div class="col-6" align="right">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal 0:</th>
                                        <td align="right">$ ' . number_format($notadecredito[0]->subtotal_0, 4) . '</td>
                                      
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Subtotal 12:</th>
                                        <td align="right">$ ' . number_format($notadecredito[0]->subtotal_12, 4) . '</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Impuesto:</th>
                                        <td align="right">$ ' . number_format($notadecredito[0]->impuesto, 4) . '</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Total:</th>
                                        <td align="right">$ ' . number_format($total_final, 4) . '</td>
                                    </tr>
                                    </table>
                                
                                
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: black;" for="nota">Nota: </label>
                            <textarea class="col-md-12 validar" name="nota" id="nota" cols="100" rows="3" disabled> ' . $notadecredito[0]->nota  . '</textarea>
                        </div>
                    </div><br>


                    <div class="row no-print">
                        <div class="col-12">
                            <a href="' . url('v3/') . '/proveedores/printFactura?id=' . $notadecredito[0]->id . '" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            <a class="btn btn-success float-right" href="' . url('v3/') . '/opcionesdocumentos/descargarXML?clave=' . $notadecredito[0]->nro_autorizacion . '" ><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></a>
                            <a class="btn btn-primary float-right" href="' . url('v3/') . '/opcionesdocumentos/descargarPDF?clave=' . $notadecredito[0]->nro_autorizacion . '" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></a>
                    </div>
                    </div>|' . $notadecredito[0]->serie . '-' . $notadecredito[0]->nro_comprobante;
        } else {
            $contenidoHtml = 'Buesqueda sin Resultados';
        }
        return $contenidoHtml;
    }
}
