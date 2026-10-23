<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebitoAcreedores extends Model
{
    private static $modelo = 'DebitoAcreedores';
    protected $table = 'ct_debito_acreedores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNotasdeDebitoAcreedores($r)
    {
        DB::enableQueryLog();
        if ($r->id_proveedor == '') {
            return DebitoAcreedores::selectRaw(
                'ct_debito_acreedores.secuencia,
                ct_debito_acreedores.id_asiento_cabecera,
                ct_debito_acreedores.concepto,
                ct_debito_acreedores.fecha_factura,
                p.razonsocial as proveedor,
                ct_debito_acreedores.valor_contable, 
                u.nombre1 as ucnombre,
                u.apellido1 as ucapellido,
                ct_debito_acreedores.estado,
                (select sum(valor_contable) from ct_debito_acreedores where ct_debito_acreedores.fecha_factura between "' . $r->d . '" and "' . $r->h . '" ) as total'
            )
                ->join('proveedor as p', 'ct_debito_acreedores.id_proveedor', 'p.id')
                ->join('users as u', 'u.id', 'ct_debito_acreedores.id_usuariocrea')
                ->where('ct_debito_acreedores.id_empresa', session('id_empresa'))
                ->where('ct_debito_acreedores.estado', '1')
                ->whereBetween('ct_debito_acreedores.fecha_factura', [$r->d, $r->h])
                ->get();
        } else {
            return DebitoAcreedores::selectRaw(
                'ct_debito_acreedores.secuencia,
                ct_debito_acreedores.id_asiento_cabecera,
                ct_debito_acreedores.concepto,
                ct_debito_acreedores.fecha_factura,
                p.razonsocial as proveedor,
                ct_debito_acreedores.valor_contable,
                u.nombre1 as ucnombre,
                u.apellido1 as ucapellido,
                ct_debito_acreedores.estado,
                (select sum(valor_contable) from ct_debito_acreedores where ct_debito_acreedores.fecha_factura between "' . $r->d . ' 00:00:00" and "' . $r->h . ' 23:59:59" ) as total'
            )
                ->join('proveedor as p', 'ct_debito_acreedores.id_proveedor', 'p.id')
                ->join('users as u', 'u.id', 'ct_debito_acreedores.id_usuariocrea')
                ->where('ct_debito_acreedores.id_empresa', session('id_empresa'))
                ->where('ct_debito_acreedores.estado', '1')
                ->whereBetween('ct_debito_acreedores.fecha_factura', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->where('ct_debito_acreedores.id_proveedor', $r->id_proveedor)
                ->get();
        }
    }
    public static function allNotasdeDebito($id = '')
    {
        DB::enableQueryLog();
        if ($id == '') {
            return DebitoAcreedores::join('proveedor as p', 'ct_debito_acreedores.id_proveedor', 'p.id')
                ->join('users as uc', 'ct_debito_acreedores.id_usuariocrea', 'uc.id')
                ->leftJoin('users as um', 'ct_debito_acreedores.id_usuariomod', '=', 'um.id')
                ->orderBy('ct_debito_acreedores.id', 'desc')
                //->whereBetween('ct_debito_acreedores.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get([
                    'ct_debito_acreedores.id_proveedor',
                    'p.nombrecomercial',
                    'ct_debito_acreedores.id',
                    'ct_debito_acreedores.fechand',
                    'ct_debito_acreedores.secuencia',
                    'ct_debito_acreedores.id_asiento_cabecera',
                    'ct_debito_acreedores.concepto',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_debito_acreedores.valor_contable',
                    'ct_debito_acreedores.estado',
                    DB::raw('CONCAT(ct_debito_acreedores.serie, " - ",ct_debito_acreedores.secuencia) AS nro_comprobante'),
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_debito_acreedores.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } else {
            return DebitoAcreedores::join('proveedor as p', 'ct_debito_acreedores.id_proveedor', 'p.id')
                ->join('users as uc', 'ct_debito_acreedores.id_usuariocrea', 'uc.id')
                ->where('ct_debito_acreedores.id', $id)
                ->orderBy('ct_debito_acreedores.id', 'desc')
                ->get([
                    'ct_debito_acreedores.id_proveedor',
                    'p.nombrecomercial',
                    'ct_debito_acreedores.id',
                    'ct_debito_acreedores.fechand',
                    'ct_debito_acreedores.secuencia',
                    'ct_debito_acreedores.id_asiento_cabecera',
                    'ct_debito_acreedores.concepto',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_debito_acreedores.valor_contable',
                    'ct_debito_acreedores.estado'
                ]);
        }
    }
    public static function getNotadeDebitoPrint($id)
    {
        $contenidoHtml = '';
        //$contenidoHtml = 'Busqueda sin resultados';
        $notadedebito = DebitoAcreedores::join('users as uc', 'ct_debito_acreedores.id_usuariocrea', 'uc.id')
            ->join('proveedor as pv', 'pv.id', 'ct_debito_acreedores.id_proveedor')
            ->leftjoin('ct_master_tipos as tipo_c', 'tipo_c.codigo', 'ct_debito_acreedores.tipo_comprobante')
            ->leftjoin('ct_master_tipos as tipo_c1', 'tipo_c1.codigo', 'ct_debito_acreedores.credito_tributario')
            ->where('ct_debito_acreedores.id', $id)
            ->get([
                'ct_debito_acreedores.id',
                'ct_debito_acreedores.fechand',
                'ct_debito_acreedores.id_proveedor',
                'pv.razonsocial',
                'pv.direccion',
                'pv.telefono1',
                'pv.telefono2',
                'pv.email',
                'ct_debito_acreedores.id_usuariocrea',
                'ct_debito_acreedores.concepto',
                'ct_debito_acreedores.nota',
                'ct_debito_acreedores.serie',
                'ct_debito_acreedores.valor_contable',
                'uc.nombre1 as ucnombre',
                'uc.apellido1 as ucapellido',
                'ct_debito_acreedores.estado',
                'ct_debito_acreedores.serie',
                'ct_debito_acreedores.autorizacion_factura',
                'ct_debito_acreedores.serie_factura',
                'ct_debito_acreedores.autorizacion',
                'tipo_c.nombre as tipo_comprobante',
                'tipo_c1.nombre as credito_tributario',

            ]);
        //dd($notadedebito);
        if (count($notadedebito) > 0) {

            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($notadedebito[0]->fechand)) . '</small>
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
                            <strong>' . $notadedebito[0]->id_proveedor . '</strong><br>
                            ' . $notadedebito[0]->razonsocial . '<br>
                            ' . $notadedebito[0]->direccion . '<br>
                            Teléfono: ' . $notadedebito[0]->telefono1 . ' ' . $notadedebito[0]->telefono2 . '<br>
                            E-mail: <a href="mailto:' . $notadedebito[0]->email . '">' . $notadedebito[0]->email . '</a>
                        </address>
                    </div> 
                    <div class="col-sm-4 invoice-col">
                        <b>Nota de credito No.:' . $notadedebito[0]->serie . '-' . $notadedebito[0]->nro_comprobante . '</b><br>
                        <b>Autorización:</b> <br> <span style="font-size:13px;">' . $notadedebito[0]->autorizacion . '</span>
                    </div>
                    <div class="col-sm-12 invoice-col">
                    <b> Concepto: </b> ' . $notadedebito[0]->concepto  . '<br>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="5" style="text-align: center;">DETALLE DE RUBROS</th>
                        </tr>
                        <tr>
                            <th>Rubro</th>  
                            <th>Detalle</th>
                            <th>Divisa</th>
                            <th>Valor</th>
                            <th>Total Base</th>
                        </tr>
                    </thead>
                    <tbody>';
            $total_final = 0;

            if (count($notadedebito) > 0) {
                $det_rubrodedebito = NotadeDebitoDetalleProveedores::where('id_debito_acreedores', $id)->get();
                $det_rubrodedebito = DetalleRubroProveedores::where('id_debito', $id)->get();
                //dd($det_rubrodedebito);
                foreach ($det_rubrodedebito as $row) {
                    $total_final += $row->valor;
                    $contenidoHtml .= '<tr>
                    <td>' . $det_rubrodedebito[0]->codigo . ' ' . $det_rubrodedebito[0]->nombre . '</td>
                    <td>' . $det_rubrodedebito[0]->concepto . '</td>
                    <td>' . 'Dolares' . '</td>
                    <td align="right">' . number_format($row->valor, 4) . '</td>
                    <td align="right">' . number_format($row->valor, 4) . '</td>
                    </tr>';
                }
            }
            //dd($det_rubrodedebito);

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
                                        <td align="right">$ ' . number_format($total_final, 4) . '</td>
                                      
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>';
                    
            $contenidoHtml .=' <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="6" style="text-align: center;">DETALLE DE DEUDAS CON EL PROVEEDOR</th>
                </tr>
                <tr>
                    <th>Vence</th>  
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Div</th>
                    <th>Saldo</th>
                    <th>Abono</th>
                </tr>
            </thead>
            <tbody>';

            if (count($notadedebito) > 0) {
                $det_debitoacreedor = NotadeDebitoDetalleProveedores::where('id_debito_acreedores', $id)->get();
                //dd($det_debitoacreedor);
                foreach ($det_debitoacreedor as $row) {
                    $contenidoHtml .= '<tr>
                    <td>' . $det_debitoacreedor[0]->vence . '</td>
                    <td>' . 'COM-FA' .'</td>
                    <td>' . $det_debitoacreedor[0]->concepto . '</td>
                    <td>' . '$' . '</td>
                    <td align="right">' . number_format($row->total_factura, 4) . '</td>
                    <td align="right">' . number_format($row->total, 4) . '</td>
                    </tr>';
                }
            }
            $contenidoHtml .= '</tbody>
            </table></div></div>
                    <div class="col-md-12" style="margin-top: 30px;">
                        <div class="input-group">
                            <label class="col-md-12 cabecera" style="color: black;" for="nota">Nota: </label>
                            <textarea class="col-md-12 validar" name="nota" id="nota" cols="100" rows="3" disabled> ' . $notadedebito[0]->nota  . '</textarea>
                        </div>
                    </div><br>


                    <div class="row no-print">
                        <div class="col-12">
                            <a href="' . url('v3/') . '/proveedores/printFactura?id=' . $notadedebito[0]->id . '" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            <a class="btn btn-success float-right" href="' . url('v3/') . '/opcionesdocumentos/descargarXML?clave=' . $notadedebito[0]->nro_autorizacion . '" ><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></a>
                            <a class="btn btn-primary float-right" href="' . url('v3/') . '/opcionesdocumentos/descargarPDF?clave=' . $notadedebito[0]->nro_autorizacion . '" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></a>
                    </div>
                    </div>|' . $notadedebito[0]->serie . '-' . $notadedebito[0]->nro_comprobante;
        } else {
            $contenidoHtml = 'Buesqueda sin Resultados';
        }
        return $contenidoHtml;
    }
}
