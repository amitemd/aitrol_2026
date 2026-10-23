<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotadeDebito extends Model
{
    private static $modelo = 'NotadeDebito';
    protected $table = 'ct_nota_debito_cabecera';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNotaDebito($id = '', $r)
    {
        if ($id == '') {
            return NotadeDebito::join('users as uc', 'ct_nota_debito_cabecera.id_usuariocrea', 'uc.id')
                ->where(function ($q) use ($r) {
                    if ($r->desde != '')
                        $q->whereBetween("ct_nota_debito_cabecera.fecha", [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59']);
                })
                ->get([
                    'ct_nota_debito_cabecera.id',
                    'ct_nota_debito_cabecera.fecha',
                    'ct_nota_debito_cabecera.id_asiento',
                    'ct_nota_debito_cabecera.tipo',
                    'ct_nota_debito_cabecera.concepto',
                    'ct_nota_debito_cabecera.valor',
                    'ct_nota_debito_cabecera.estado',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),

                ]);
        } else {
            return NotadeDebito::join('users as uc', 'ct_nota_debito_cabecera.id_usuariocrea', 'uc.id')
                ->where('ct_nota_debito_cabecera.id', $id)
                ->first([
                    'ct_nota_debito_cabecera.id',
                    'ct_nota_debito_cabecera.fecha',
                    'ct_nota_debito_cabecera.id_asiento',
                    'ct_nota_debito_cabecera.tipo',
                    'ct_nota_debito_cabecera.concepto',
                    'ct_nota_debito_cabecera.valor',
                    'ct_nota_debito_cabecera.estado',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                ]);
        }
    }
    public static function getNotadeDebitoCajaBancoPrint($id)
    {
        $id_empresa = session('id_empresa');
        $contenidoHtml = '';
        //$contenidoHtml = 'Busqueda sin resultados';
        $notadedebito = NotadeDebito::join('users as uc', 'ct_nota_debito_cabecera.id_usuariocrea', 'uc.id')
            ->join('ct_caja_banco as cb', 'ct_nota_debito_cabecera.id_banco', 'cb.id')
            ->where('ct_nota_debito_cabecera.id', $id)
            ->get([
                'ct_nota_debito_cabecera.id',
                'ct_nota_debito_cabecera.fecha',
                'ct_nota_debito_cabecera.id_usuariocrea',
                'ct_nota_debito_cabecera.concepto',
                'ct_nota_debito_cabecera.beneficiario',
                'ct_nota_debito_cabecera.valor',
                'ct_nota_debito_cabecera.observacion',
                'uc.nombre1 as ucnombre',
                'uc.apellido1 as ucapellido',
                'cb.nombre as nombre_banco',
            ]);
        //dd($notadedebito);
        if (count($notadedebito) > 0) {

            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($notadedebito[0]->fecha)) . '</small>
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
                    <div class="col-sm-12 invoice-col">
                    <b> Concepto: </b> ' . $notadedebito[0]->concepto  . '<br>
                    </div>
                    <div class="col-sm-12 invoice-col">
                    <b> Beneficiario: </b> ' . $notadedebito[0]->beneficiario  . '<br>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                          <tr>
                            <th>Banco</th>  
                            <th>Divisa</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>' . $notadedebito[0]->nombre_banco . '</td>
                    <td> DOLARES </td>
                    <td align="right">' . number_format($notadedebito[0]->valor, 4) . '</td>
                    </tr>
                    </tbody>
                    <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="5" style="text-align: center;">DETALLE</th>
                        </tr>
                        <tr>
                            <th>Num. Cuenta</th>  
                            <th>Nombre Cuenta</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>';
            $total_final = 0;

            if (count($notadedebito) > 0) {
                $detnotadedebito = NotaDebitoDetalle::where('id_nota_debito', $id)->get();
                //dd($detnotadedebito);
                foreach ($detnotadedebito as $row) {
                    $id_plan = PlanCuentas::where('id_plan', $row->codigo)->where('id_empresa', $id_empresa)->first();
                    $total_final += $row->debe;
                    $contenidoHtml .= '<tr>
                            <td>' . $id_plan->plan . '</td>
                            <td>' . $detnotadedebito[0]->cuenta . '</td>
                            <td align="right">' . number_format($row->debe, 4) . '</td>
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
                                    <th style="width:50%">Total:</th>
                                    <td align="right"> ' . number_format($total_final, 4) . '</td>
                                  
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>';

            $contenidoHtml .= '
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: black;" for="nota">Observaciòn: </label>
                            <textarea class="col-md-12 validar" name="nota" id="nota" cols="100" rows="3" disabled> ' . $notadedebito[0]->observacion  . '</textarea>
                        </div>
                    </div><br>';
        } else {
            $contenidoHtml = 'Buesqueda sin Resultados';
        }
        return $contenidoHtml . '|' . $id;
    }
    public static function saveNotaDebitoCajaBanco($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $cont = 0;
            $cod_sucurs = PuntoEmision::where('estado', '1')->where('id', $r->sucursal_empresa)->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = DB::table('ct_caja')->where('estado', '1')->where('id', $r->punto_emision)->first();
            $c_caja = $cod_caj->codigo_caja;
            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => "",
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->observacion,
                'valor' => $r->valor_total,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('cajaBanco', 'notadebito', $arrayAsientoCabecera,  $r);
            if ($r->total_detalle != 0) {
                unset($data['id']);
                $nd_cab['id_usuariocrea'] = Auth::user()->id;
                $nd_cab['id_usuariomod'] = Auth::user()->id;
                $nd_cab['ip_creacion'] = $ip;
                $nd_cab['ip_modificacion'] = $ip;
                $nd_cab['id_asiento'] = $id_asiento_cabecera;
                $nd_cab['estado'] = 1;
                $nd_cab['fecha'] = $data['fecha'];
                $nd_cab['concepto'] = $data['concepto'];
                $nd_cab['empresa'] = session('id_empresa');
                $nd_cab['beneficiario'] = $data['beneficiario'];
                $nd_cab['id_banco'] = $data['id_banco'];
                $nd_cab['valor'] = $data['valor_total'];
                $nd_cab['tipo'] = 'BAN-ND';
                $nd_cab['observacion'] = $data['observacion'];
                $id = NotadeDebito::insertGetId($nd_cab);
                $arr_nombres = $r->id_det_nd;
                $arr_valores = $r->debe;
                $cont1 = 0;
                foreach ($arr_nombres as $nom) {
                    $valor = $arr_valores[$cont1];
                    $arr_cuenta = explode("|", $nom);
                    $codigo = trim($arr_cuenta[0]);
                    $nombre_cta = trim($arr_cuenta[1]);
                    $arr_det_nd = [
                        'id_nota_debito'    => $id,
                        'codigo'            => $codigo,
                        'cuenta'            => $nombre_cta,
                        'debe'             => $valor,
                        'haber'            => '0',
                        'valor_base'       => $valor,
                        'id_usuariocrea'    => Auth::user()->id,
                        'id_usuariomod'     => Auth::user()->id,
                        'ip_creacion'       => $ip,
                        'ip_modificacion'   => $ip,
                    ];
                    $cont1++;
                    NotaDebitoDetalle::insert($arr_det_nd);
                }
                $r->c = 'CajaBancos';
                $r->s = 'saveNotaDebitoCajaBanco';
                $r->d = $origin['d'];
                $r->m = NotadeDebito::$modelo;
                $r->o = 'Se agrego nueva nota de debito id.: ' . $id;
                $cont++;
            } else {
                DB::rollBack();
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => "Agregue al menos un detalle para guardar la nota de débito caja banco",
                ];
                $r->c = 'CajaBancos';
                $r->s = 'saveNotaDebitoCajaBanco';
                $r->d =  $origin['d'];
                $r->m = NotadeDebito::$modelo;
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
            $r->c = 'CajaBancos';
            $r->s = 'saveNotaDebitoCajaBanco';
            $r->d =  $origin['d'];
            $r->m = NotadeDebito::$modelo;
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
    public static function anularNotaDebitoCajaBanco($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $nota_debito_cj = NotadeDebito::where('estado', '1')->where('id', $r->id)->first();
            if ($nota_debito_cj != '') {

                $input = [
                    'observacion_anulacion'         => $r->observacion,
                    'estado'                        => '0',
                    'ip_modificacion'               => $ip,
                    'id_usuariomod'                 => $idusuario,
                ];
                NotadeDebito::where('estado', '1')->where('id', $r->id)->update($input);
                $consultaCabecerctventas = AsientosCabecera::where('estado', '1')
                    ->where('id', $nota_debito_cj->id_asiento)->first();
                $arrayAsientoCabecera = [
                    'sucursal' => $consultaCabecerctventas["sucursal"],
                    'punto_emision' => $consultaCabecerctventas["punto_emision"],
                    'fecha_asiento' => date("Y-m-d"),
                    'fact_numero' => "",
                    'id_empresa' => session('id_empresa'),
                    'observacion' => $r->observacion,
                    'valor' => $consultaCabecerctventas->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,

                ];
                $id_asiento_cabecera = Contabilidad::crearAsiento('cajaBanco', 'anularanotadebito', $arrayAsientoCabecera,  $r);
                //dd(AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
                if ($id_asiento_cabecera["status"] == "error") {
                    throw new Exception("Ocurrio un error en la anulación");
                }

                LogContable::insert([
                    'tipo'           => 'ND',
                    'valor_ant'      => $nota_debito_cj->valor,
                    'valor'          => $nota_debito_cj->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                    'observacion'    => $nota_debito_cj->concepto,
                    'id_ant'         => $nota_debito_cj->id,
                    'id_referencia'  => $id_asiento_cabecera,
                ]);



                $r->c = 'CajaBancos';
                $r->s = 'anularNotaDebitoCajaBanco';
                //$r->d = $origin['d'];
                $r->m = NotadeDebito::$modelo;
                $r->o = 'Se anulo la nota de debito con id.: ' . $r->id;
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
            $r->c = 'CajaBancos';
            $r->s = 'anularNotaDebitoCajaBanco';
            //$r->d =  $origin['id'];
            $r->m = NotadeDebito::$modelo;
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
    public static function pdf_nota_debito($r)
    {
        //dd($r->all());
        //dd($r);
        $registro = NotadeDebito::where('id', $r)->first();
        //dd($registro);
        //$registro = NotadeDebito::all();

        $detalle  = NotaDebitoDetalle::where('id_nota_debito', $r)->get();

        //$fact_venta = Ct_Orden_Venta::findorfail($id);
        //$ct_for_pag = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();

        $vistaurl = "v3.contable.cajabancos.pdf_nota_debito";
        $view     = \View::make($vistaurl, compact('registro', 'detalle'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        //echo convertir(12312);
        return $pdf->stream('Nota de debito -' . $r . '.pdf');
    }
    public function empresas()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'empresa');
    }
    public function banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_banco');
    }
}
