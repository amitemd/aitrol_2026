<?php

namespace Sis_medico\v3\contable;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Http\Controllers\ImportacionesController;
use Sis_medico\v3\contable\Configuraciones;
use Sis_medico\Ct_Cierre_Cabecera;

class CajaBanco extends Model
{
    private static $modelo = 'CajaBanco';
    protected $table = 'ct_caja_banco';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function cambiarEstadoConciliacion($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (ConciliacionMes::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
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
    public static function getNumero()
    {
        $num = TransferenciasBancarias::whereRaw('numero = (select ifnull(max(numero),0) from ct_transferencia_bancaria)')->get();
        return $num;
    }
    public static function saveTransferenciaBancaria($r)
    {
        DB::beginTransaction();
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($r->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'message' => $verifica_cierre->message];
            }
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $num = 1;
            $numero = self::getNumero();
            foreach ($numero as $row) {
                $num = $row->numero + 1;
            }
            $id_empresa = session('id_empresa');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $sucursal = $r->sucursal;
            $arrayTransferencia = [
                'fecha_asiento'       => Utilidades::cambiarformatofecha($r['fecha_asiento']),
                'empresa'             => $id_empresa,
                'numero'              => $num,
                'tipo'                => $r['tipo'],
                'proyecto'            => $r['proyecto'],
                'concepto'            => $r['concepto'],
                'numcheque'           => $r['numcheque'],
                'fecha_cheque'        => Utilidades::cambiarformatofecha($r['fecha_cheque']),
                'beneficiario'        => $r['beneficiario'],
                'no_increm_cheque'    => $r['no_increm_cheque'],
                'ruc'                 => $r['ruc'],
                'direccion'           => $r['direccion'],
                'glosa'               => $r['glosa'],
                'estado'              => 1,
                'id_cuenta_origen'    => $r['id_cuenta_origen'],
                'id_divisa_origen'    => $r['id_divisa_origen'],
                'id_cambio_origen'    => $r['id_cambio_origen'],
                'valor_origen'        => $r['valor_origen'],
                'id_cuenta_destino'   => $r['id_cuenta_destino'],
                'id_divisa_destino'   => $r['id_divisa_destino'],
                'id_cambio_destino'   => $r['id_cambio_destino'],
                'valor_destino'       => $r['valor_destino'],
                'id_comision'         => $r['id_comision'],
                'valor_comision'      => $r['valor_comision'],
                'comision_tarjeta'    => $r['comision_tarjeta'],
                'base_iva'            => $r['base_iva'],
                'id_base_iva'         => $r['id_base_iva'],
                'iva_retenido'        => $r['iva_retenido'],
                'base_ret_fuente'     => $r['base_ret_fuente'],
                'id_base_ret_fuente'  => $r['id_base_ret_fuente'],
                'retencion_fuente'    => $r['retencion_fuente'],
                'valores_adicionales' => $r['valores_adicionales'],
                'total'               => $r['total'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_proveedor'        => $r['id_proveedor'],
                'autorizacion'        => $r['autorizacion'],
            ];
            $id_asiento = Contabilidad::asiento($arrayTransferencia, $sucursal);
            if ($id_asiento["status"] == "error") {
                DB::rollback();
                return $id_asiento;
            }
            $input['id_asiento'] = $id_asiento['id_asiento'];
            $id_transferencia = TransferenciasBancarias::insertGetId($arrayTransferencia);
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
            $r->c = 'Caja Bancos';
            $r->s = 'saveConciliacion';
            $r->d =  $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
    }
    public static function traerDatosGenera($r)
    {
        $autorizacion = AutorizacionesPendientes::find($r->id);
        return ['dato' => $autorizacion];
    }
    public static function verificarAutorizacion($r)
    {
        $dato = AutorizacionesPendientes::where('autorizacion', $r->autorizacion)->first();
        $arrayRespuesta = [
            'autorizacion' => $r->autorizacion,
            'status' => ($dato != null) ? '1' : '0'
        ];
        return json_encode($arrayRespuesta);
    }
    public static function saveRentencion($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip_creacion = $_SERVER["REMOTE_ADDR"];
            $arrayAutorizacionPendiente = [
                'id_proveedor' => $r->id_proveedor_modal,
                'autorizacion' => $r->autorizacion_modal,
                'base_iva' => $r->base_iva_modal,
                'porcentaje_iva' => $r->porcentaje_iva_modal,
                'base_retencion' => $r->base_fuente_modal,
                'porcentaje_fuente' => $r->porcentaje_fuente_modal,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip_creacion,
                'ip_modificacion' => $ip_creacion,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            AutorizacionesPendientes::insert($arrayAutorizacionPendiente);
            $cont++;
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
            $r->c = 'Caja Bancos';
            $r->s = 'saveConciliacion';
            $r->d =  $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => ''
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function traerAutorizaciones($r)
    {
        $autorizacion = AutorizacionesPendientes::where('id_proveedor', $r->id)->get();
        return ['autorizaciones' => $autorizacion];
    }
    public static function pdfDatosConciliacion($r)
    {
        $id_empresa = $r->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $x = ($empresa->nombrecomercial . ' - ' . $id_empresa);
        $y = ('CONCILIACION BANCARIA');
        try {
            $periodo = explode('-', $r->periodo);
            $periodoCompleto = $r->periodo;
            $periodoCompleto = explode('-', $periodoCompleto);
            $idBanco = CajaBanco::where('id', $r->banco)->first();
            if ($idBanco == '') {
                $data['message'] = 'No existe el banco seleccionado';
                return view('v3/errors/view_422', $data);
            }
            $banco = $r->banco;
            $saldoAnteriorLibros = 0;
            $saldoAnteriorBancos = 0;
            setlocale(LC_TIME, 'es_ES');
            $mes = (int)$periodoCompleto[1];
            $fecha = new DateTime($periodoCompleto[0] . '-' . $mes);
            $nombreMes = mb_strtoupper(strftime('%B', $fecha->getTimestamp()), 'UTF-8');
            $pendientes = ConciliacionPendientes::where('id_empresa', $id_empresa)
                ->where('id_caja_banco', $banco)
                ->where('anio', '>=', 2025)
                ->where(function ($q) use ($periodo, $mes) {
                    $q->where('anio', $periodo[0])
                        ->where('mes', '<=', $mes);
                })
                ->get();
            $pendientes = self::pedientes_caja_banco($periodo[0], $mes, $id_empresa, $banco);
            $saldoAnteriorLibros = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('tipo', 1)->where('estado', 1)->first(['saldo_actual']);
            $saldoAnteriorBancos = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('tipo', 2)->where('estado', 1)->first(['saldo_actual']);
            $consulta_mes = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 2)->where('id_caja_banco', $banco)->get();
            $libro_mes = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 1)->where('id_caja_banco', $banco)->get();
            $array2 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Pagado', '(-) Valores Debitados', 'Saldo Según Bancos'); //
            $array3 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Girados', '(-) Valores Debitados', 'Saldo Según Libros'); //
            $z = ('LISTADO DE DOCUMENTOS PENDIENTES');
            $datos["data"] = ["Fecha", "Tipo", "Id Asiento", "Detalle", "Valor", "Numero", "Cheque", "Beneficiario"];
            $view = \View::make('v3.contable.cajabancos.pdf_pendientes', compact('nombreMes', 'empresa',  'mes', 'x', 'libro_mes', 'periodoCompleto', 'pendientes', 'array2', 'consulta_mes', 'banco'))->render();
            $pdf  = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');
            return $pdf->stream('ConciliacionBancaria.pdf');
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return view('v3/errors/view_422', $data);
        }
    }
    public static function dateDiffInDays($date1, $date2)
    {
        $diff = strtotime($date2) - strtotime($date1);
        return abs(round($diff / 86400));
    }
    public static function pedientes_caja_banco($anio, $mes, $id_empresa, $banco)
    {
        $all = DB::table("ct_conciliacion_pendientes")
            ->where('id_empresa', $id_empresa)
            ->where('id_caja_banco', $banco)
            ->whereYear('fecha', '>=', 2025)
            ->whereYear('fecha', '<=', $anio)
            ->get();

        $res = [];
        $res1 = [];
        $nulos = [];

        foreach ($all as $val) {
            if (is_null($val->mes)) {
                if (date("Y-m", strtotime($val->fecha)) <= date("Y-m", strtotime($anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT)))) {
                    $nulos[] = $val;
                }
            }

            if (date("Y-m", strtotime($val->fecha)) <= date("Y-m", strtotime($anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT)))) {
                $res[] = $val;
            }
        }

        foreach ($res as $value) {
            if (!is_null($value->mes) && $value->mes > $mes) {
                $res1[] = $value;
            }
        }

        $data = array_merge($nulos, $res1);
        return $data;
    }
    public static function excelDatosConciliacion($r)
    {
        Excel::create('Conciliacion Bancaria', function ($excel) use ($r) {
            $excel->sheet('Conciliacion', function ($sheet) use ($r) {
                $id_empresa = $r->session()->get('id_empresa');
                $empresa = Empresa::find($id_empresa);
                $x = ($empresa->nombrecomercial . ' - ' . $id_empresa);
                $y = ('CONCILIACION BANCARIA');
                $periodoCompleto = $r->periodo;
                $fecha = new DateTime($periodoCompleto);

                $periodoCompleto = $fecha->format('Y-m');
                $periodoCompleto = explode('-', $periodoCompleto);
                $banco = $r->banco;

                setlocale(LC_TIME, 'es_ES');
                $mes = (int)$periodoCompleto[1];

                $fecha = new DateTime($periodoCompleto[0] . '-' . $mes);

                $nombreMes = mb_strtoupper(strftime('%B', $fecha->getTimestamp()), 'UTF-8');
                $consulta = ConciliacionMes::where('mes', $mes)->where('anio', $periodoCompleto[0])->where('id_empresa', $id_empresa)->where('estado', '1')->where('id_caja_banco', $r['banco'])->get();
                $pendientes = ConciliacionPendientes::where('estado', '-1')->where('id_empresa', $id_empresa)->where('id_caja_banco', $banco)->get();

                if (!is_null($empresa) && file_exists(storage_path("app/logo/{$empresa->logo}"))) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath(storage_path("app/logo/{$empresa->logo}"));
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWidth(150);
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(10);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('B3:H3');
                $sheet->cell('B3', function ($cell) use ($empresa, $id_empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' - ' . $id_empresa);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $nombre_banco = isset($consulta->caja_banco) ? $consulta->caja_banco->nombre : '';
                $sheet->mergeCells('A4:H4');
                $sheet->cell('A4', function ($cell) use ($nombre_banco) {
                    // manipulate the cel
                    $cell->setValue('CONCILIACION BANCARIA ' . $nombre_banco);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $sheet->mergeCells('A5:H5');
                $sheet->cell('A5', function ($cell) use ($periodoCompleto, $nombreMes) {
                    // manipulate the cel
                    $cell->setValue($periodoCompleto[0] . ' ' . $nombreMes);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $sheet->mergeCells('A6:B6');
                $sheet->cell('A6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO EN BANCOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $sheet->mergeCells('G6:H6');
                $sheet->cell('G6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO EN LIBROS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $array2 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Pagado', '(-) Valores Debitados', 'Saldo Según Bancos');
                $array3 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Girados', '(-) Valores Debitados', 'Saldo Según Libros');
                $datos["comienzo"] = 7;
                for ($i = 0; $i < count($array2); $i++) {
                    ImportacionesController::excelDetalles($sheet, $datos["comienzo"]++, ["A"], [$array2[$i]]);
                }
                $y = 7;
                for ($i = 0; $i < count($array3); $i++) {
                    ImportacionesController::excelDetalles($sheet, $y++, ["G"], [$array3[$i]]);
                }
                $consulta_mes = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 2)->where('id_caja_banco', $banco)->first();
                $libro_mes = ConciliacionMes::where('anio', $periodoCompleto[0])->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 1)->where('id_caja_banco', $banco)->first();
                if (!is_null($consulta_mes)) {
                    $arr_d = array('$' . str_repeat(' ', 5) . $consulta_mes->saldo_anterior, '$' . str_repeat(' ', 5) . $consulta_mes->valor_depositos, '$' . str_repeat(' ', 5) . $consulta_mes->valor_acreditado, '$' . str_repeat(' ', 5) . $consulta_mes->valor_cheques, '$' . str_repeat(' ', 5) . $consulta_mes->valor_debitado, '$' . str_repeat(' ', 5) . $consulta_mes->saldo_actual);
                    $x = 7;
                    for ($i = 0; $i < count($arr_d); $i++) {

                        ImportacionesController::excelDetalles($sheet, $x++, ["B"], [$arr_d[$i]]);
                    }
                }
                if (!is_null($libro_mes)) {
                    $arr_lib = array('$' . str_repeat(' ', 5) . $libro_mes->saldo_anterior, '$' . str_repeat(' ', 5) . $libro_mes->valor_depositos, '$' . str_repeat(' ', 5) . $libro_mes->valor_acreditado, '$' . str_repeat(' ', 5) . $libro_mes->valor_cheques, '$' . str_repeat(' ', 5) . $libro_mes->valor_debitado, '$' . str_repeat(' ', 5) . $libro_mes->saldo_actual);
                    $a = 7;
                    for ($i = 0; $i < count($arr_lib); $i++) {

                        ImportacionesController::excelDetalles($sheet, $a++, ["H"], [$arr_lib[$i]]);
                    }
                }
                $datos["comienzo"] = 16;
                $sheet->mergeCells('A' . $datos["comienzo"] . ':H' . $datos["comienzo"]);
                $sheet->cell('A' . $datos["comienzo"], function ($cell) {
                    // manipulate the cell
                    $cell->setValue('CHEQUES GIRADOS Y NO COBRADOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $datos["comienzo"]++;
                $datos["background-color"] = "#CDE4F1";
                $datos["data"] = ["Fecha", "Tipo", "Id Asiento", "Detalle", "Valor", "Numero de comprobante", "Cheque", "Beneficiario"];
                excelCreate::details($sheet, $datos);
                $datos["comienzo"]++;
                foreach ($pendientes as $p) {
                    $datos["data"] = [$p->fecha, $p->tipo, $p->id_asiento, $p->detalle, $p->valor, $p->secuencia, $p->cheque, $p->beneficiario];
                    excelCreate::details($sheet, $datos);
                    $datos["comienzo"]++;
                }
                $datos["comienzo"]++;
                $datos["comienzo"]++;
                $datos["comienzo"]++;
                $datos["comienzo"]++;
                $sheet->cell('D' . $datos["comienzo"], function ($cell) {
                    $cell->setValue('ELABORADO POR:');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', null, null, null);
                });
            });
        })->export('xlsx');
    }
    public static function verConciliacion($id)
    {
        return ConciliacionMes::join('ct_caja_banco as b', 'ct_conciliacion_mes.id_caja_banco', 'b.id')
            ->where('ct_conciliacion_mes.id', $id)->first([
                'ct_conciliacion_mes.anio',
                'ct_conciliacion_mes.mes',
                'ct_conciliacion_mes.id_caja_banco',
                'b.nombre',
                'b.numero_cuenta'
            ]);
    }
    public static function getConciliacionMes()
    {
        return ConciliacionMes::join('ct_caja_banco as b', 'ct_conciliacion_mes.id_caja_banco', 'b.id')
            ->join('users as uc', 'ct_conciliacion_mes.id_usuariocrea', '=', 'uc.id')
            ->leftJoin('users as um', 'ct_conciliacion_mes.id_usuariomod', '=', 'um.id')
            ->where('b.id_empresa', session('id_empresa'))
            ->where('ct_conciliacion_mes.estado', 1)
            ->groupBy('ct_conciliacion_mes.anio', 'ct_conciliacion_mes.mes', 'ct_conciliacion_mes.id_caja_banco')
            ->orderBy('anio')
            ->orderBy('mes', 'desc')
            ->orderBy('tipo')
            ->get([
                'ct_conciliacion_mes.id',
                'ct_conciliacion_mes.id_caja_banco',
                'b.nombre',
                'ct_conciliacion_mes.estado',
                'ct_conciliacion_mes.anio',
                'ct_conciliacion_mes.mes',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                DB::raw('IF(ct_conciliacion_mes.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                DB::raw('GROUP_CONCAT(saldo_actual SEPARATOR \'|\') as valores'),
                'ct_conciliacion_mes.created_at'
            ]);
    }
    public static function saveConciliacion($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $periodo = explode('-', $r->fecha_periodo);
            $validacion = ConciliacionMes::where('anio', $periodo[0])
                ->where('mes', $periodo[1])
                ->where('estado', 1)
                ->where('id_caja_banco', $r->instituciones)
                ->get();
            $banco = CajaBanco::find($r->instituciones);
            if (count($validacion) > 1) {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'El mes ' . $periodo[1] . ' del periodo ' . $periodo[0] . ', para el banco: ' . $banco->nombre . ' ya se encuentra conciliado',
                    'data' => $r->input(),
                ];
                return json_encode($arrayRespuesta);
            }
            $conciliacion = ConciliacionBancaria::where('empresa', session('id_empresa'))
                ->where('anio', $periodo[0])
                ->where('mes', $periodo[1])
                ->where('id_caja_banco', $r->instituciones)
                ->where('estado', 0)
                ->get();
            $ip = Utilidades::getRealIP();
            if ($conciliacion->count() > 0) {
                foreach ($conciliacion as $row) {
                    $arrayPendientes = [];
                    $id_asiento = '';
                    $fecha_documento = '';
                    $secuencia = '';
                    $beneficiario = '';
                    $cheque = '';
                    foreach ($r->input() as $key => $rows) {
                        $cadena = $key;
                        $buscar = "datoEgresoMasivo_";
                        $resultado = strpos($cadena, $buscar);
                        if ($resultado !== FALSE) {
                            $dat = explode('|', $key);
                            for ($i = 0; $i < count($dat); $i++) {
                                if ($row->id_tipo == $dat[1]) {
                                    $id_asiento = $dat[2];
                                    $fecha_documento = $dat[3];
                                    $secuencia = $dat[4];
                                    $beneficiario = $dat[5];
                                    $cheque = $dat[6];
                                }
                            }
                        }
                    }
                    $egreso = ComprobanteEgresoVarios::find($row->id_tipo);
                    if ($egreso == '') {
                        $egreso = ComprobanteEgreso::find($row->id_tipo);
                    }
                    if ($row->tipo == 'EG' || $row->tipo == 'EGV') {
                        $arrayPendientes = [
                            'id_caja_banco' => $row->id_caja_banco,
                            'id_empresa' => session('id_empresa'),
                            'id_asiento' => $id_asiento != '' ? $id_asiento : $egreso->id_asiento_cabecera,
                            'fecha' => $fecha_documento != '' ? $fecha_documento : $egreso->fecha_comprobante,
                            'tipo' => $row->tipo,
                            'id_tipo' => $row->id_tipo,
                            'detalle' => $row->detalle,
                            'valor' => $row->valor,
                            'secuencia' => $secuencia,
                            'cheque' => $cheque != '' ? $cheque : $egreso->nro_cheque,
                            'beneficiario' => $beneficiario != '' ? $beneficiario : $egreso->beneficiario,
                            'estado' => -1,
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => $ip,
                            'ip_modificacion' => $ip,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'id_concilia' => $row->tipo . '-:-' . $row->id_tipo
                        ];
                        $chequePendiente = ConciliacionPendientes::where('id_tipo', $row->id_tipo)->first();
                        if ($chequePendiente != '') {
                            $chequePendiente->estado = -1;
                            $chequePendiente->save();
                        } else {
                            ConciliacionPendientes::insert($arrayPendientes);
                        }
                    }
                }
            }
            $arrayConciliacionMesBancos = [
                'id_caja_banco' => $r->instituciones,
                'mes' => $periodo[1],
                'anio' => $periodo[0],
                'id_empresa' => session('id_empresa'),
                'saldo_anterior' => $r->saldoAnteriorResumenBancos,
                'valor_depositos' => $r->depositoResumenBancos,
                'valor_acreditado' => $r->acreditadosResumenBancos,
                'valor_cheques' => $r->chequesGiradosResumenBancos,
                'valor_debitado' => $r->debitadosResumenBancos,
                'saldo_actual' => $r->saldoActualResumenBancos,
                'fecha' => date('Y-m-t', strtotime($r->fecha_periodo)),
                'tipo' => 2,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod'  => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'conciliado' => 1,
            ];
            ConciliacionMes::insert($arrayConciliacionMesBancos);
            $arrayConciliacionMesLibros = [
                'id_caja_banco' => $r->instituciones,
                'mes' => $periodo[1],
                'anio' => $periodo[0],
                'id_empresa' => session('id_empresa'),
                'saldo_anterior' => $r->saldoAnteriorResumenLibros,
                'valor_depositos' => $r->depositoResumenLibros,
                'valor_acreditado' => $r->acreditadosResumenLibros,
                'valor_cheques' => $r->chequesGiradosResumenLibros,
                'valor_debitado' => $r->debitadosResumenLibros,
                'saldo_actual' => $r->saldoActualResumenLibros,
                'fecha' => date('Y-m-t', strtotime($r->fecha_periodo)),
                'tipo' => 1,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'conciliado' => 1,
            ];
            ConciliacionMes::insert($arrayConciliacionMesLibros);
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            $cont++;
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
            $r->c = 'Caja Bancos';
            $r->s = 'saveConciliacion';
            $r->d =  $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/cajabancos/conciliacion'
            ];
            return $arrayRespuesta;
        }
    }
    public static function verDocumentosPendientesAgregados($r)
    {
        $periodo = explode('-', $r->periodo);
        $html = '<table class="table table-bordered table-striped" style="width: 100%;"><thead><tr><th>...</th><th>Fecha</th><th>Tipo</th><th>Asiento</th><th>Referencia</th><th>Monto</th></tr></thead><tbody>';
        $pendientes = ConciliacionPendientes::where('id_empresa', session('id_empresa'))
            ->where('id_caja_banco', $r->banco)
            ->where('estado', 1)
            ->where('anio', $periodo[0])
            ->where('mes', $periodo[1])
            ->get([
                'id',
                'fecha',
                'tipo',
                'id_asiento',
                'detalle',
                'valor',
                DB::Raw('IF(IFNULL(estado,0)=1,"checked","") as check_')
            ]);
        if ($pendientes->count() > 0) {
            foreach ($pendientes as $row) {
                $html .= '<tr>';
                $html .= ' <td style="text-align:center;">
                <div class="icheck-primary d-inline">
                    <input type="checkbox" class="chePendientesAgregados" onchange="calcularPendientesAgregados(\'' . $row->id . '\',\'' . $row->valor . '\')" id="chePendientesAgregados_' . $row->id . '" ' . $row->check_ . '>
                    <label for="chePendientesAgregados_' . $row->id . '"></label>
                </div>
            </td>';
                $html .= '<td>' . date('Y-m-d', strtotime($row->fecha)) . '</td>';
                $html .= '<td>' . $row->tipo . '</td>';
                $html .= '<td>' . $row->id_asiento . '</td>';
                $html .= '<td>' . $row->detalle . '</td>';
                $html .= '<td style="text-align:right;">' . $row->valor . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalPendientesAgregados">' . number_format($pendientes->sum('valor'), 2) . '</td></tr></tfoot></table>';
        return $html;
    }
    public static function verDocumentosPendientes($r)
    {
        $html = '<table class="table table-bordered table-striped" style="width: 100%;"><thead><tr><th>...</th><th>Fecha</th><th>Tipo</th><th>Asiento</th><th>Referencia</th><th>Monto</th></tr></thead><tbody>';
        $fec = $r->anio . '-' . $r->mes . '-01';
        $fec_ = date('Y-m-d', strtotime('+1 month', strtotime($fec)));
        $pendientes = ConciliacionPendientes::where('id_empresa', session('id_empresa'))
            ->where('id_caja_banco', $r->id_banco)
            ->where('estado', -1)
            ->where('fecha', '>=', $fec)
            ->where('fecha', '<=', $fec)
            //->where('fecha', '<', $fec_)
            ->whereNull('anio')
            ->whereNull('mes')
            ->get();
        if ($pendientes->count() > 0) {
            foreach ($pendientes as $row) {
                $html .= '<tr>';
                $html .= ' <td style="text-align:center;">
                <div class="icheck-primary d-inline">
                    <input type="checkbox" class="chePendientes" onchange="calcularPendientes(\'' . $row->id . '\',\'' . $row->valor . '\')" id="chePendientes_' . $row->id . '" ' . $row->check_ . '>
                    <label for="chePendientes_' . $row->id . '"></label>
                </div>
            </td>';
                $html .= '<td>' . date('Y-m-d', strtotime($row->fecha)) . '</td>';
                $html .= '<td>' . $row->tipo . '</td>';
                $html .= '<td>' . $row->id_asiento . '</td>';
                $html .= '<td>' . $row->detalle . '</td>';
                $html .= '<td style="text-align:right;">' . $row->valor . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalPendientes">0.00</td></tr></tfoot></table>';
        return $html;
    }
    public static function verificarConciliacionPendientes($r)
    {
        DB::beginTransaction();
        try {
            $ip = Utilidades::getRealIP();
            $pendientes = ConciliacionPendientes::find($r->id);
            if ($pendientes != '') {
                $dato = explode('-:-', $pendientes->id_concilia);
                if ($pendientes->tipo == 'EGV') {
                    $egresosVarios = EgresosVarios::find($dato[1]);
                    if ($egresosVarios == '') {
                        $concil = ConciliacionBancaria::find($dato[1]);
                        if ($concil != '') {
                            $egresosVarios = EgresosVarios::find($concil->id_tipo);
                        }
                    }
                    if ($egresosVarios->estado != 0) {
                        $peri = explode('-', $r->fecha_periodo);
                        $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
                        $arrayEgresosVarios = [
                            'id_caja_banco' => $r->id_banco,
                            'anio' => $peri[0],
                            'mes' => $peri[1],
                            'tipo' => 'EGV',
                            'id_tipo' => $egresosVarios->id,
                            'empresa' => session('id_empresa'),
                            'fecha_conciliacion' => $fecha_conciliacion,
                            'valor' => $egresosVarios->valor,
                            'detalle' => $egresosVarios->descripcion,
                            'estado' => 1,
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => $ip,
                            'ip_modificacion' => $ip,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        if (CajaBanco::verificarConciliacion($arrayEgresosVarios)) {
                            $pendientes->estado = $pendientes->estado == -1 ? 1 : -1;
                            if ($pendientes->estado == -1) {
                                $pendientes->anio = null;
                                $pendientes->mes = null;
                            } else {
                                $pendientes->anio = $peri[0];
                                $pendientes->mes = $peri[1];
                            }
                            $pendientes->save();
                        }
                    }
                } else {
                    $egresosPendiente = Egresos::find($dato[1]);
                    if ($egresosPendiente != '' && $egresosPendiente->estado != 0) {
                        $peri = explode('-', $r->fecha_periodo);
                        $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
                        $arrayEgresosVarios = [
                            'id_caja_banco' => $r->id_banco,
                            'anio' => $peri[0],
                            'mes' => $peri[1],
                            'tipo' => 'EG',
                            'id_tipo' => $egresosPendiente->id,
                            'empresa' => session('id_empresa'),
                            'fecha_conciliacion' => $fecha_conciliacion,
                            'valor' => $egresosPendiente->valor,
                            'detalle' => $egresosPendiente->descripcion,
                            'estado' => 1,
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => $ip,
                            'ip_modificacion' => $ip,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        if (CajaBanco::verificarConciliacion($arrayEgresosVarios)) {
                            $pendientes->estado = $pendientes->estado == -1 ? 1 : -1;
                            if ($pendientes->estado == -1) {
                                $pendientes->anio = null;
                                $pendientes->mes = null;
                            } else {
                                $pendientes->anio = $peri[0];
                                $pendientes->mes = $peri[1];
                            }
                            $pendientes->save();
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            echo '<pre>';
            print_r($arrayRespuesta);
            exit;
        }
    }
    public static function verificarConciliacionEgresosMasivos($r)
    {
        $ip = Utilidades::getRealIP();
        $egresosMasivos = EgresosMasivos::find($r->id);
        if ($egresosMasivos != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayEgresosMasivos = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => 'EGM',
                'id_tipo' => $egresosMasivos->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $egresosMasivos->valor_pago,
                'detalle' => $egresosMasivos->descripcion,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayEgresosMasivos);
        }
    }
    public static function verificarConciliacionEgresosVarios($r)
    {
        $ip = Utilidades::getRealIP();
        $egresosVarios = EgresosVarios::find($r->id);
        if ($egresosVarios != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayEgresosVarios = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => 'EGV',
                'id_tipo' => $egresosVarios->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $egresosVarios->valor,
                'detalle' => $egresosVarios->descripcion,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayEgresosVarios);
        }
    }
    public static function verificarConciliacionEgresos($r)
    {
        $ip = Utilidades::getRealIP();
        $egresos = Egresos::find($r->id);
        if ($egresos != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayEgresos = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => 'EG',
                'id_tipo' => $egresos->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $egresos->valor,
                'detalle' => $egresos->descripcion,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            /*if (Auth::user()->id == '0921605895') {
                echo '<pre>';
                print_r($arrayEgresos);
                exit;
            }*/
            CajaBanco::verificarConciliacion($arrayEgresos);
        }
    }
    public static function verificarConciliacionDebitoBancario($r)
    {
        $ip = Utilidades::getRealIP();
        $notarDebitoBancario = NotaDebitoBancaria::find($r->id);
        if ($notarDebitoBancario != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayNotarDebitoBancario = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => $notarDebitoBancario->tipo,
                'id_tipo' => $notarDebitoBancario->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $notarDebitoBancario->valor,
                'detalle' => $notarDebitoBancario->concepto,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayNotarDebitoBancario);
        }
    }
    public static function verificarConciliacionNotaCredito($r)
    {
        $ip = Utilidades::getRealIP();
        $notaCredito = NotadeCredito::find($r->id);
        if ($notaCredito != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayNotaCredito = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => $notaCredito->tipo,
                'id_tipo' => $notaCredito->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $notaCredito->valor,
                'detalle' => $notaCredito->concepto,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayNotaCredito);
        }
    }
    public static function verificarConciliacionNotaDebito($r)
    {
        $ip = Utilidades::getRealIP();
        $notaDebito = NotadeDebito::find($r->id);
        if ($notaDebito != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayNotaDebito = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => $notaDebito->tipo,
                'id_tipo' => $notaDebito->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $notaDebito->valor,
                'detalle' => $notaDebito->concepto,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayNotaDebito);
        }
    }
    public static function verificarConciliacionTransferencia($r)
    {
        $ip = Utilidades::getRealIP();
        $transferencia = TransferenciasBancarias::find($r->id);
        if ($transferencia != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayTransferencia = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => $r->tipo == '' ? $transferencia->tipo : $r->tipo,
                'id_tipo' => $transferencia->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $transferencia->valor_destino,
                'detalle' => $transferencia->concepto,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayTransferencia);
        }
    }
    public static function verificarConciliacionDeposito($r)
    {
        $ip = Utilidades::getRealIP();
        $deposito = DepositoBancario::find($r->id);
        if ($deposito != '') {
            $peri = explode('-', $r->fecha_periodo);
            $fecha_conciliacion = $r->fecha_periodo . '-' . cal_days_in_month(CAL_GREGORIAN, $peri[1], $peri[0]);
            $arrayDeposito = [
                'id_caja_banco' => $r->id_banco,
                'anio' => $peri[0],
                'mes' => $peri[1],
                'tipo' => $deposito->tipo,
                'id_tipo' => $deposito->id,
                'empresa' => session('id_empresa'),
                'fecha_conciliacion' => $fecha_conciliacion,
                'valor' => $deposito->valor_destino,
                'detalle' => $deposito->concepto,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            CajaBanco::verificarConciliacion($arrayDeposito);
        }
    }
    public static function verificarConciliacion($arrayDatos)
    {
        DB::beginTransaction();
        try {
            /*
             [id_caja_banco] => 18
             [anio] => 2025
             [mes] => 02
             [tipo] => EG
             [id_tipo] => 7777
             [empresa] => 0993075000001
             [fecha_conciliacion] => 2025-02-28
             [valor] => 68.75
             [detalle] => DEBITO BANCARIO: EMPRESA PÚBLICA ESTRTÉGICA MACIONAL DE ELECTRICIDAD CNEL EP - 148-999 - 061495030- CODIGO UNICO ELECTRICO 0410021131 TORRE MEDICA II OF 906
             [estado] => 1
             [id_usuariocrea] => 0921605895
             [id_usuariomod] => 0921605895
             [ip_creacion] => 186.3.217.71
             [ip_modificacion] => 186.3.217.71
             [created_at] => 2026-03-16 09:49:19
             [updated_at] => 2026-03-16 09:49:19
            */
            $ip = Utilidades::getRealIP();
            $conciliacion = ConciliacionBancaria::where('id_tipo', $arrayDatos['id_tipo'])
                ->where('tipo', $arrayDatos['tipo'])
                ->where('anio', $arrayDatos['anio'])
                ->where('mes', $arrayDatos['mes'])
                ->where('empresa', session('id_empresa'))
                ->first();
            /*if (Auth::user()->id == '0921605895') {
                echo '<pre>';
                print_r($conciliacion);
            }*/
            if ($conciliacion != '') {
                $conciliacion->estado = $conciliacion->estado == 0 ? 1 : 0;
                $conciliacion->id_usuariomod = Auth::user()->id;
                $conciliacion->ip_modificacion = $ip;
                $conciliacion->updated_at = date('Y-m-d H:i:s');
                $conciliacion->save();
            } else {
                ConciliacionBancaria::insert($arrayDatos);
            }
            /*if (Auth::user()->id == '0921605895') {
                echo '<pre>';
                print_r($conciliacion);
                exit;
            }*/
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo '<pre>';
            print_r($e->getMessage());
            return false;
        }
    }
    public static function getAllBank()
    {
        return  CajaBanco::where('estado', '1')->where('id_empresa', session("id_empresa"))->get();
    }
    public static function traerConciliacionPeriodo($r)
    {
        $periodo = explode('-', $r->periodo);
        $periodoCompleto = $r->periodo;
        $fecha = new DateTime($periodoCompleto . '-01');
        $fecha->modify('-1 month');
        $periodoCompleto = $fecha->format('Y-m');
        $periodoCompleto = explode('-', $periodoCompleto);
        $depositosHtml = '';
        $transferenciaCreditoHtml = '';
        $transferenciaDebitoHtml = '';
        $notasDebitoHtml = '';
        $notasCreditoHtml = '';
        $notaDebitoBancarioHtml = '';
        $egresosHtml = '';
        $egresosVariosHtml = '';
        $egresosMasivosHtml = '';
        $saldoAnteriorLibros = 0;
        $saldoAnteriorBancos = 0;
        $mesAnterior = (int)$periodoCompleto[1];
        $mesAnterior = str_pad($mesAnterior, 2, 0, STR_PAD_LEFT);
        $conciliado = 0;
        $conciliado = ConciliacionMes::where('anio', $periodo[0])
            ->where('mes', $periodo[1])
            ->where('id_caja_banco', $r->banco)
            ->where('estado', 1)
            ->first([
                'anio',
                'mes',
                'conciliado'
            ]);
        if ($conciliado != '') {
            $conciliado = $conciliado->conciliado;
        }
        $s = [
            'id_banco' => $r->banco,
            'anio' => $periodo[0],
            'mes' => $periodo[1]
        ];
        $pendientes = CajaBanco::verDocumentosPendientes((object)$s);
        $pendientesAgregados = CajaBanco::verDocumentosPendientesAgregados($r);
        $saldoAnteriorLibros = ConciliacionMes::where('anio', $periodoCompleto[0])->where('id_caja_banco', $r->banco)->where('mes', $mesAnterior)->where('tipo', 1)->where('estado', 1)->first(['saldo_actual']);
        $saldoAnteriorBancos = ConciliacionMes::where('anio', $periodoCompleto[0])->where('id_caja_banco', $r->banco)->where('mes', $mesAnterior)->where('tipo', 2)->where('estado', 1)->first(['saldo_actual']);
        /*$depositos = DepositoBancario::leftjoin('ct_caja_banco as cb', 'ct_deposito_bancario.id_cuenta_destino', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_deposito_bancario.id');
                $q->on('con.tipo', 'ct_deposito_bancario.tipo')
                    ->where('con.empresa', session('id_empresa'));
            })->where('ct_deposito_bancario.estado', '!=', 0)
            ->whereYear('ct_deposito_bancario.fecha_asiento', $periodo[0])
            ->whereMonth('ct_deposito_bancario.fecha_asiento', $periodo[1])
            ->where('ct_deposito_bancario.empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0) {
                    $q->where('cb.id', $r->banco);
                }
            })->orderBy('ct_deposito_bancario.fecha_asiento', 'asc')
            ->get([
                'ct_deposito_bancario.id',
                'ct_deposito_bancario.tipo',
                'ct_deposito_bancario.id_cuenta_destino',
                'ct_deposito_bancario.fecha_asiento',
                'ct_deposito_bancario.id_asiento',
                'ct_deposito_bancario.concepto',
                'ct_deposito_bancario.valor_destino',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);*/
        $empresa = session('id_empresa');
        $depositos = DepositoBancario::query()
            ->leftJoin('ct_caja_banco as cb', 'ct_deposito_bancario.id_cuenta_destino', '=', 'cb.cuenta_mayor')
            ->where('ct_deposito_bancario.estado', '!=', 0)
            ->whereYear('ct_deposito_bancario.fecha_asiento', $periodo[0])
            ->whereMonth('ct_deposito_bancario.fecha_asiento', $periodo[1])
            ->where('ct_deposito_bancario.empresa', session('id_empresa'))
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('cb.id', $r->banco);
            })
            ->select([
                'ct_deposito_bancario.id',
                'ct_deposito_bancario.tipo',
                'ct_deposito_bancario.id_cuenta_destino',
                'ct_deposito_bancario.fecha_asiento',
                'ct_deposito_bancario.id_asiento',
                'ct_deposito_bancario.concepto',
                'ct_deposito_bancario.valor_destino',
            ])
            ->selectSub(function ($q) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('IF(IFNULL(MAX(con.estado),0)=1,"checked","")')
                    ->whereColumn('con.id_tipo', 'ct_deposito_bancario.id')
                    ->whereColumn('con.tipo', 'ct_deposito_bancario.tipo')
                    ->where('con.empresa', session('id_empresa'));
            }, 'check_')
            ->orderBy('ct_deposito_bancario.fecha_asiento', 'asc')
            ->get();
        $depositosHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion" id="tableDepositos">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody>';
        $depositoTotal = 0;
        if ($depositos->count() > 0) {
            foreach ($depositos as $d) {
                if ($d->check_ == 'checked')
                    $depositoTotal += $d->valor_destino;
                $depositosHtml .= '<tr>
                    <td style="text-align:center;">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" class="cheDepositos" onchange="calcularDepositos(\'' . $d->id . '\',\'' . $d->valor_destino . '\')" id="chedeposito_' . $d->id . '" ' . $d->check_ . '>
                            <label for="chedeposito_' . $d->id . '"></label>
                        </div>
                    </td>
                    <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_asiento)) . '</td>
                    <td style="text-align:center;">' . $d->tipo . '</td>
                    <td style="text-align:center;">' . $d->id_asiento . '</td>
                    <td>' . $d->concepto . '</td>
                    <td style="text-align:right;">' . $d->valor_destino . '</td>
                </tr>';
            }
        }
        $depositosHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalDepositos">' . $depositoTotal . '</td></tr></tfoot></table>¬';
        $transferenciasAcreditadas = TransferenciasBancarias::leftjoin('ct_caja_banco as cb', 'ct_transferencia_bancaria.id_cuenta_destino', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_transferencia_bancaria.id');
                $q->on('con.tipo', 'ct_transferencia_bancaria.tipo');
            })
            ->whereYear('ct_transferencia_bancaria.fecha_asiento', $periodo[0])
            ->whereMonth('ct_transferencia_bancaria.fecha_asiento', $periodo[1])
            ->where('ct_transferencia_bancaria.empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('cb.id', $r->banco);
            })
            ->orderBy('ct_transferencia_bancaria.fecha_asiento', 'asc')
            ->where('ct_transferencia_bancaria.estado', '!=', 0)
            ->get([
                'ct_transferencia_bancaria.id',
                'ct_transferencia_bancaria.tipo',
                'ct_transferencia_bancaria.id_cuenta_destino',
                'ct_transferencia_bancaria.fecha_asiento',
                'ct_transferencia_bancaria.id_asiento',
                'ct_transferencia_bancaria.concepto',
                'ct_transferencia_bancaria.valor_destino',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);
        $transferenciaCreditoHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableTransferencia">';
        $transferenciaTotal = 0;
        if ($transferenciasAcreditadas->count() > 0) {
            foreach ($transferenciasAcreditadas as $d) {
                if ($d->check_ == 'checked')
                    $transferenciaTotal += $d->valor_destino;
                $transferenciaCreditoHtml .= '<tr>
                    <td style="text-align:center;">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" class="cheTransferenciasAcreditadas" onchange="calcularTransferenciasAcreditadas(\'' . $d->id . '\',\'' . $d->valor_destino . '\')" id="chetransferencia_' . $d->id . '" ' . $d->check_ . '>
                            <label for="chetransferencia_' . $d->id . '"></label>
                        </div>
                    </td>
                    <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_asiento)) . '</td>
                    <td style="text-align:center;">' . $d->tipo . '</td>
                    <td style="text-align:center;">' . $d->id_asiento . '</td>
                    <td>' . $d->concepto . '</td>
                    <td style="text-align:right;">' . $d->valor_destino . '</td>
                </tr>';
            }
        }
        $transferenciaCreditoHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalTransferenciasAcreditadas">' . $transferenciaTotal . '</td></tr></tfoot></table>¬';
        $transferenciasDebitadas = TransferenciasBancarias::leftjoin('ct_caja_banco as cb', 'ct_transferencia_bancaria.id_cuenta_origen', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_transferencia_bancaria.id')->where('con.tipo', 'BAN-TR-DB');
                //$q->on('con.tipo', 'ct_transferencia_bancaria.tipo');
            })
            ->whereYear('ct_transferencia_bancaria.fecha_asiento', $periodo[0])
            ->whereMonth('ct_transferencia_bancaria.fecha_asiento', $periodo[1])
            ->where('ct_transferencia_bancaria.empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('cb.id', $r->banco);
            })
            ->orderBy('ct_transferencia_bancaria.fecha_asiento', 'asc')
            ->where('ct_transferencia_bancaria.estado', '!=', 0)
            ->get([
                'ct_transferencia_bancaria.id',
                DB::raw('IF(ct_transferencia_bancaria.tipo="BAN-TR","BAN-TR-DB","") as tipo'),
                'ct_transferencia_bancaria.id_cuenta_origen',
                'ct_transferencia_bancaria.fecha_asiento',
                'ct_transferencia_bancaria.id_asiento',
                'ct_transferencia_bancaria.concepto',
                'ct_transferencia_bancaria.valor_origen as valor_destino',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);
        $transferenciaDebitoHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableTransferencia">';
        $transferenciaTotal = 0;
        if ($transferenciasDebitadas->count() > 0) {
            foreach ($transferenciasDebitadas as $d) {
                if ($d->check_ == 'checked')
                    $transferenciaTotal += $d->valor_destino;
                $transferenciaDebitoHtml .= '<tr>
                    <td style="text-align:center;">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" class="cheTransferenciasDebitadas" onchange="calcularTransferenciasDebitadas(\'' . $d->id . '\',\'' . $d->valor_destino . '\',\'' . $d->tipo . '\')" id="chetransferencia_' . $d->id . '" ' . $d->check_ . '>
                            <label for="chetransferencia_' . $d->id . '"></label>
                        </div>
                    </td>
                    <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_asiento)) . '</td>
                    <td style="text-align:center;">' . $d->tipo . '</td>
                    <td style="text-align:center;">' . $d->id_asiento . '</td>
                    <td>' . $d->concepto . '</td>
                    <td style="text-align:right;">' . $d->valor_destino . '</td>
                </tr>';
            }
        }
        $transferenciaDebitoHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalTransferenciasDebitadas">' . $transferenciaTotal . '</td></tr></tfoot></table>¬';
        /*$notasDebito = NotadeDebito::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_nota_debito_cabecera.id');
            $q->on('con.tipo', 'ct_nota_debito_cabecera.tipo');
        })
            ->whereYear('ct_nota_debito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_debito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_debito_cabecera.empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_nota_debito_cabecera.id_banco', $r->banco);
            })
            ->orderBy('ct_nota_debito_cabecera.fecha', 'asc')
            ->where('ct_nota_debito_cabecera.estado', '!=', 0)
            ->get([
                'ct_nota_debito_cabecera.id',
                'ct_nota_debito_cabecera.tipo',
                'ct_nota_debito_cabecera.fecha',
                'ct_nota_debito_cabecera.id_asiento',
                'ct_nota_debito_cabecera.concepto',
                'ct_nota_debito_cabecera.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);*/
        $notasDebito = NotadeDebito::query()
            ->whereYear('ct_nota_debito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_debito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_debito_cabecera.empresa', session('id_empresa'))
            ->where('ct_nota_debito_cabecera.estado', '!=', 0)
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('ct_nota_debito_cabecera.id_banco', $r->banco);
            })
            ->select([
                'ct_nota_debito_cabecera.id',
                'ct_nota_debito_cabecera.tipo',
                'ct_nota_debito_cabecera.fecha',
                'ct_nota_debito_cabecera.id_asiento',
                'ct_nota_debito_cabecera.concepto',
                'ct_nota_debito_cabecera.valor',
            ])
            ->selectSub(function ($q) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('IF(IFNULL(MAX(con.estado),0)=1,"checked","")')
                    ->whereColumn('con.id_tipo', 'ct_nota_debito_cabecera.id')
                    ->whereColumn('con.tipo', 'ct_nota_debito_cabecera.tipo')
                    ->where('con.empresa', session('id_empresa'));
            }, 'check_')
            ->orderBy('ct_nota_debito_cabecera.fecha', 'asc')
            ->get();
        $notasDebitoHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableNotaDebito">';
        $notaDebitoTotal = 0;
        if ($notasDebito->count() > 0) {
            foreach ($notasDebito as $d) {
                if ($d->check_ == 'checked')
                    $notaDebitoTotal += $d->valor;
                $notasDebitoHtml .= '<tr>
                        <td style="text-align:center;">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" class="cheNotaDebito" onchange="calcularNotaDebito(\'' . $d->id . '\',\'' . $d->valor . '\')" id="chenotaDebito_' . $d->id . '" ' . $d->check_ . '>
                                <label for="chenotaDebito_' . $d->id . '"></label>
                            </div>
                        </td>
                        <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha)) . '</td>
                        <td style="text-align:center;">' . $d->tipo . '</td>
                        <td style="text-align:center;">' . $d->id_asiento . '</td>
                        <td>' . $d->concepto . '</td>
                        <td style="text-align:right;">' . $d->valor . '</td>
                    </tr>';
            }
        }
        $notasDebitoHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalNotaDebito">' . $notaDebitoTotal . '</td></tr></tfoot></table>¬';
        /*$notaCredito = NotadeCredito::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_nota_credito_cabecera.id');
            $q->on('con.tipo', 'ct_nota_credito_cabecera.tipo');
        })
            ->whereYear('ct_nota_credito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_credito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_credito_cabecera.empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_nota_credito_cabecera.id_banco', $r->banco);
            })
            ->orderBy('ct_nota_credito_cabecera.fecha', 'asc')
            ->where('ct_nota_credito_cabecera.estado', '!=', 0)
            ->get([
                'ct_nota_credito_cabecera.id',
                'ct_nota_credito_cabecera.tipo',
                'ct_nota_credito_cabecera.fecha',
                'ct_nota_credito_cabecera.id_asiento',
                'ct_nota_credito_cabecera.concepto',
                'ct_nota_credito_cabecera.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);*/
        $notaCredito = NotadeCredito::query()
            ->whereYear('ct_nota_credito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_credito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_credito_cabecera.empresa', session('id_empresa'))
            ->where('ct_nota_credito_cabecera.estado', '!=', 0)
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('ct_nota_credito_cabecera.id_banco', $r->banco);
            })
            ->select([
                'ct_nota_credito_cabecera.id',
                'ct_nota_credito_cabecera.tipo',
                'ct_nota_credito_cabecera.fecha',
                'ct_nota_credito_cabecera.id_asiento',
                'ct_nota_credito_cabecera.concepto',
                'ct_nota_credito_cabecera.valor',
            ])
            ->selectSub(function ($q) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('IF(IFNULL(MAX(con.estado),0)=1,"checked","")')
                    ->whereColumn('con.id_tipo', 'ct_nota_credito_cabecera.id')
                    ->whereColumn('con.tipo', 'ct_nota_credito_cabecera.tipo')
                    ->where('con.empresa', session('id_empresa'));
            }, 'check_')
            ->orderBy('ct_nota_credito_cabecera.fecha', 'asc')
            ->get();
        $notasCreditoHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableNotaCredito">';
        $notaCreditoTotal = 0;
        if ($notaCredito->count() > 0) {
            foreach ($notaCredito as $d) {
                if ($d->check_ == 'checked')
                    $notaCreditoTotal += $d->valor;
                $notasCreditoHtml .= '<tr>
                            <td style="text-align:center;">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" class="cheNotasCredito" onchange="calcularNotaCredito(\'' . $d->id . '\',\'' . $d->valor . '\')" id="chenotaCredito_' . $d->id . '" ' . $d->check_ . '>
                                    <label for="chenotaCredito_' . $d->id . '"></label>
                                </div>
                            </td>
                            <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha)) . '</td>
                            <td style="text-align:center;">' . $d->tipo . '</td>
                            <td style="text-align:center;">' . $d->id_asiento . '</td>
                            <td>' . $d->concepto . '</td>
                            <td style="text-align:right;">' . $d->valor . '</td>
                        </tr>';
            }
            //holi
        }
        $notasCreditoHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalNotaCredito">' . $notaCreditoTotal . '</td></tr></tfoot></table>¬';
        $notasDebitoBancaria = NotaDebitoBancaria::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_debito_bancario.id');
            $q->on('con.tipo', 'ct_debito_bancario.tipo');
        })
            ->whereYear('ct_debito_bancario.fecha', $periodo[0])
            ->whereMonth('ct_debito_bancario.fecha', $periodo[1])
            ->where('ct_debito_bancario.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_debito_bancario.id_banco', $r->banco);
            })
            ->orderBy('ct_debito_bancario.fecha', 'asc')
            ->where('ct_debito_bancario.estado', 1)
            ->get([
                'ct_debito_bancario.id',
                'ct_debito_bancario.tipo',
                'ct_debito_bancario.fecha',
                'ct_debito_bancario.id_asiento',
                'ct_debito_bancario.concepto',
                'ct_debito_bancario.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);
        $notaDebitoBancarioHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableNotaDebito">';
        $notaDebitoBancarioTotal = 0;
        if ($notasDebitoBancaria->count() > 0) {
            foreach ($notasDebitoBancaria as $d) {
                if ($d->check_ == 'checked')
                    $notaDebitoBancarioTotal += $d->valor;
                $notaDebitoBancarioHtml .= '<tr>
                        <td style="text-align:center;">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" class="cheNotaDebitoBancaria" onchange="calcularDebitoBancario(\'' . $d->id . '\',\'' . $d->valor . '\')" id="chedebitobancario_' . $d->id . '" ' . $d->check_ . '>
                                <label for="chedebitobancario_' . $d->id . '"></label>
                            </div>
                        </td>
                        <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha)) . '</td>
                        <td style="text-align:center;">' . $d->tipo . '</td>
                        <td style="text-align:center;">' . $d->id_asiento . '</td>
                        <td>' . $d->concepto . '</td>
                        <td style="text-align:right;">' . $d->valor . '</td>
                    </tr>';
            }
        }
        $notaDebitoBancarioHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalDebitoBancario">' . $notaDebitoBancarioTotal . '</td></tr></tfoot></table>¬';
        //Anterior
        /*$egresos = Egresos::leftjoin('ct_conciliacion_bancaria as con', function ($q) use ($r) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso.id')
                ->where('con.tipo', 'EG');
        })
            ->where(function ($q) use ($periodo) {
                if (session('id_empresa') != '0993075000001') {
                    $q->whereYear('ct_comprobante_egreso.fecha_comprobante', $periodo[0]);
                    $q->whereMonth('ct_comprobante_egreso.fecha_comprobante', $periodo[1]);
                }
            })
            ->where('ct_comprobante_egreso.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_comprobante_egreso.id_caja_banco', $r->banco);
            })
            ->whereMonth("ct_comprobante_egreso.fecha_comprobante", "<=", explode("-", $r->periodo)[1])
            ->whereYear("ct_comprobante_egreso.fecha_comprobante", "<=", explode("-", $r->periodo)[0])
            ->where('ct_comprobante_egreso.estado', 1)
            ->orderBy('ct_comprobante_egreso.fecha_comprobante', 'asc')
            ->get([
                'ct_comprobante_egreso.id',
                'con.tipo',
                'con.mes',
                'con.anio',
                'ct_comprobante_egreso.fecha_comprobante',
                'ct_comprobante_egreso.id_asiento_cabecera',
                'ct_comprobante_egreso.descripcion',
                'ct_comprobante_egreso.valor',
                'ct_comprobante_egreso.id_caja_banco',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
            ]);*/
        $empresa = session('id_empresa');
        $anioFiltro = explode("-", $r->periodo)[0];
        $mesFiltro  = explode("-", $r->periodo)[1];
        DB::enableQueryLog();
        $egresos = Egresos::query()
            ->where('ct_comprobante_egreso.id_empresa', $empresa)
            ->where('ct_comprobante_egreso.estado', 1)
            ->when($empresa != '0993075000001', function ($q) use ($periodo) {
                $q->whereYear('ct_comprobante_egreso.fecha_comprobante', $periodo[0])
                    ->whereMonth('ct_comprobante_egreso.fecha_comprobante', $periodo[1]);
            })
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('ct_comprobante_egreso.id_caja_banco', $r->banco);
            })
            ->whereYear('ct_comprobante_egreso.fecha_comprobante', '>=', 2025)
            ->whereYear('ct_comprobante_egreso.fecha_comprobante', '<=', $anioFiltro)
            ->whereMonth('ct_comprobante_egreso.fecha_comprobante', '>=', $mesFiltro)
            ->whereMonth('ct_comprobante_egreso.fecha_comprobante', '<=', $mesFiltro)
            ->select([
                'ct_comprobante_egreso.id',
                DB::raw("'EG' as tipo"),
                'ct_comprobante_egreso.fecha_comprobante',
                'ct_comprobante_egreso.id_asiento_cabecera',
                'ct_comprobante_egreso.descripcion',
                'ct_comprobante_egreso.valor',
                'ct_comprobante_egreso.id_caja_banco',
            ])
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('MAX(con.mes)')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso.id')
                    ->where('con.tipo', 'EG')
                    ->where('con.empresa', $empresa);
            }, 'mes')
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('MAX(con.anio)')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso.id')
                    ->where('con.tipo', 'EG')
                    ->where('con.empresa', $empresa);
            }, 'anio')
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('IF(IFNULL(MAX(con.estado),0)=1,"checked","")')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso.id')
                    ->where('con.tipo', 'EG')
                    ->where('con.empresa', $empresa);
            }, 'check_')
            ->orderBy('ct_comprobante_egreso.fecha_comprobante', 'asc')
            ->get();
        //Nuevo
        /*$periodoSplit = explode("-", $r->periodo);
        $egresos = Egresos::leftJoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo', 'ct_comprobante_egreso.id')
                ->where('con.tipo', 'EG');
        })
            ->where(function ($q) use ($periodo) {
                if (session('id_empresa') != '0993075000001') {
                    $q->whereYear('ct_comprobante_egreso.fecha_comprobante', $periodo[0])
                        ->whereMonth('ct_comprobante_egreso.fecha_comprobante', $periodo[1]);
                }
            })
            ->where('ct_comprobante_egreso.id_empresa', session('id_empresa'))
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('ct_comprobante_egreso.id_caja_banco', $r->banco);
            })
            ->whereMonth('ct_comprobante_egreso.fecha_comprobante', '<=', $periodoSplit[1])
            ->whereYear('ct_comprobante_egreso.fecha_comprobante', '<=', $periodoSplit[0])
            ->where('ct_comprobante_egreso.estado', 1)
            ->orderBy('ct_comprobante_egreso.fecha_comprobante', 'asc')
            ->distinct()
            ->get([
                'ct_comprobante_egreso.id',
                'con.tipo',
                'con.mes',
                'con.anio',
                'ct_comprobante_egreso.fecha_comprobante',
                'ct_comprobante_egreso.id_asiento_cabecera',
                'ct_comprobante_egreso.descripcion',
                'ct_comprobante_egreso.valor',
                'ct_comprobante_egreso.id_caja_banco',
                DB::raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
            ]);*/
        /*if (Auth::user()->id == '0921605895') {
            echo '<pre>';
            print_r(DB::getQueryLog());
            exit;
        }*/
        $egresosHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableEgresos">';
        $egresoTotal = 0;
        if ($egresos->count() > 0) {
            foreach ($egresos as $d) {
                $verificar = ConciliacionBancaria::where("id_tipo", $d->id)->first();
                if (is_null($verificar)) {
                    $month = date("m", strtotime($d->fecha_comprobante));
                    $year = date("Y", strtotime($d->fecha_comprobante));
                    $idusuario = Auth::user()->id;
                    $ip_cliente = $_SERVER["REMOTE_ADDR"];

                    $arrayConciliacion = [
                        'id_caja_banco' => $d->id_caja_banco,
                        'anio' => $year,
                        'mes' => $month,
                        'tipo' => "EG",
                        'id_tipo' => $d->id,
                        'empresa' => $d->id_empresa == '' ? session('id_empresa') : $d->id_empresa,
                        'fecha_conciliacion' => date("Y-m-d"),
                        'valor' => $d->valor_pago == '' ? $d->valor : $d->valor_pago,
                        'detalle' => $d->descripcion,
                        'estado' => 1,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    ConciliacionBancaria::insert($arrayConciliacion);
                }
                if ($d->check_ == 'checked')
                    $egresoTotal += $d->valor;
                $egresosHtml .= '<tr>
                        <td style="text-align:center;">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" class="cheEgresos" onchange="calcularEgresos(\'' . $d->id . '\',\'' . $d->valor . '\')" id="cheEgresos_' . $d->id . '" ' . $d->check_ . '>
                                <label for="cheEgresos_' . $d->id . '"></label>
                            </div>
                        </td>
                        <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_comprobante)) . '</td>
                        <td style="text-align:center;">' . $d->tipo . '</td>
                        <td style="text-align:center;">' . $d->id_asiento_cabecera . '</td>
                        <td>' . $d->descripcion . '</td>
                        <td style="text-align:right;">' . $d->valor . '</td>
                    </tr>';
            }
        }
        $egresosHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalEgresos">' . $egresoTotal . '</td></tr></tfoot></table>¬';
        /*$egresosVarios = EgresosVarios::leftjoin('ct_conciliacion_bancaria as con', function ($q)  use ($r) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso_varios.id')
                ->where('con.tipo', 'EGV');
        })
            ->where(function ($q) use ($periodo) {
                if (session('id_empresa') != '0993075000001') {
                    $q->whereYear('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[0]);
                    $q->whereMonth('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[1]);
                }
            })
            ->where('ct_comprobante_egreso_varios.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_comprobante_egreso_varios.id_caja_banco', $r->banco);
            })
            ->whereMonth("ct_comprobante_egreso_varios.fecha_comprobante", "<=", explode("-", $r->periodo)[1])
            ->whereYear("ct_comprobante_egreso_varios.fecha_comprobante", "<=", explode("-", $r->periodo)[0])
            ->where('ct_comprobante_egreso_varios.estado', 1)
            ->orderBy('ct_comprobante_egreso_varios.fecha_comprobante', 'asc')

            ->get([
                'ct_comprobante_egreso_varios.id',
                'con.tipo',
                'ct_comprobante_egreso_varios.fecha_comprobante',
                'ct_comprobante_egreso_varios.id_asiento_cabecera',
                'ct_comprobante_egreso_varios.descripcion',
                'ct_comprobante_egreso_varios.valor',
                'ct_comprobante_egreso_varios.id_caja_banco',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_')
            ]);*/
        $empresa   = session('id_empresa');
        $anioFiltro = explode("-", $r->periodo)[0];
        $mesFiltro  = explode("-", $r->periodo)[1];
        $egresosVarios = EgresosVarios::query()
            ->where('ct_comprobante_egreso_varios.id_empresa', $empresa)
            ->where('ct_comprobante_egreso_varios.estado', 1)
            ->when($empresa != '0993075000001', function ($q) use ($periodo) {
                $q->whereYear('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[0])
                    ->whereMonth('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[1]);
            })
            ->when($r->banco != 0, function ($q) use ($r) {
                $q->where('ct_comprobante_egreso_varios.id_caja_banco', $r->banco);
            })
            ->whereYear('ct_comprobante_egreso_varios.fecha_comprobante', '>=', 2025)
            ->whereYear('ct_comprobante_egreso_varios.fecha_comprobante', '<=', $anioFiltro)
            ->whereMonth('ct_comprobante_egreso_varios.fecha_comprobante', '>=', $mesFiltro)
            ->whereMonth('ct_comprobante_egreso_varios.fecha_comprobante', '<=', $mesFiltro)
            ->select([
                'ct_comprobante_egreso_varios.id',
                DB::raw("'EGV' as tipo"),
                'ct_comprobante_egreso_varios.fecha_comprobante',
                'ct_comprobante_egreso_varios.id_asiento_cabecera',
                'ct_comprobante_egreso_varios.descripcion',
                'ct_comprobante_egreso_varios.valor',
                'ct_comprobante_egreso_varios.id_caja_banco',
            ])
            // Mes conciliado (último)
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('MAX(con.mes)')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso_varios.id')
                    ->where('con.tipo', 'EGV')
                    ->where('con.empresa', $empresa);
            }, 'mes')
            // Año conciliado (último)
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('MAX(con.anio)')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso_varios.id')
                    ->where('con.tipo', 'EGV')
                    ->where('con.empresa', $empresa);
            }, 'anio')
            // Check conciliado (clave para tu UI)
            ->selectSub(function ($q) use ($empresa) {
                $q->from('ct_conciliacion_bancaria as con')
                    ->selectRaw('IF(IFNULL(MAX(con.estado),0)=1,"checked","")')
                    ->whereColumn('con.id_tipo', 'ct_comprobante_egreso_varios.id')
                    ->where('con.tipo', 'EGV')
                    ->where('con.empresa', $empresa);
            }, 'check_')
            ->orderBy('ct_comprobante_egreso_varios.fecha_comprobante', 'asc')
            ->get();
        $egresosVariosHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableNotaDebito">';
        $egresosVariosTotal = 0;
        if ($egresosVarios->count() > 0) {
            foreach ($egresosVarios as $d) {
                $verificar = ConciliacionBancaria::where("id_tipo", $d->id)->first();
                if (is_null($verificar)) {
                    $month = date("m", strtotime($d->fecha_comprobante));
                    $year = date("Y", strtotime($d->fecha_comprobante));
                    $idusuario = Auth::user()->id;
                    $ip_cliente = $_SERVER["REMOTE_ADDR"];
                    $arrayConciliacion = [
                        'id_caja_banco' => $d->id_caja_banco,
                        'anio' => $year,
                        'mes' => $month,
                        'tipo' => "EGV",
                        'id_tipo' => $d->id,
                        'empresa' => $d->id_empresa == '' ? session('id_empresa') : $d->id_empresa,
                        'fecha_conciliacion' => date("Y-m-d"),
                        'valor' => $d->valor_pago == '' ? $d->valor : $d->valor_pago,
                        'detalle' => $d->descripcion,
                        'estado' => 1,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    ConciliacionBancaria::insert($arrayConciliacion);
                }
                if ($d->check_ == 'checked')
                    $egresosVariosTotal += $d->valor;
                $egresosVariosHtml .= '<tr>
                            <td style="text-align:center;">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" class="cheEgresosVarios" onchange="calcularEgresosVarios(\'' . $d->id . '\',\'' . $d->valor . '\')" id="cheEgresosVarios_' . $d->id . '" ' . $d->check_ . '>
                                    <label for="cheEgresosVarios_' . $d->id . '"></label>
                                </div>
                            </td>
                            <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_comprobante)) . '</td>
                            <td style="text-align:center;">' . $d->tipo . '</td>
                            <td style="text-align:center;">' . $d->id_asiento_cabecera . '</td>
                            <td>' . $d->descripcion . '</td>
                            <td style="text-align:right;">' . $d->valor . '</td>
                        </tr>';
            }
        }
        $egresosVariosHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalEgresosVarios">' . $egresosVariosTotal . '</td></tr></tfoot></table>¬';
        $egresosMasivos = EgresosMasivos::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso_masivo.id')
                ->where('con.tipo', 'EGM');
        })
            ->whereYear('ct_comprobante_egreso_masivo.fecha_comprobante', $periodo[0])
            ->whereMonth('ct_comprobante_egreso_masivo.fecha_comprobante', $periodo[1])
            ->where('ct_comprobante_egreso_masivo.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->banco != 0)
                    $q->where('ct_comprobante_egreso_masivo.id_caja_banco', $r->banco);
            })
            ->orderBy('ct_comprobante_egreso_masivo.fecha_comprobante', 'asc')
            ->where('ct_comprobante_egreso_masivo.estado', '!=', 0)
            ->get([
                'ct_comprobante_egreso_masivo.beneficiario',
                'ct_comprobante_egreso_masivo.secuencia',
                'ct_comprobante_egreso_masivo.no_cheque',
                'ct_comprobante_egreso_masivo.id',
                'con.tipo',
                'ct_comprobante_egreso_masivo.fecha_comprobante',
                'ct_comprobante_egreso_masivo.id_asiento_cabecera',
                'ct_comprobante_egreso_masivo.descripcion',
                'ct_comprobante_egreso_masivo.valor_pago',
                'ct_comprobante_egreso_masivo.id_caja_banco',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),

            ]);
        $egresosMasivosHtml .= '<table style="width:100%;" class="table table-bordered table-striped tableConciliacion">
            <thead>
                <tr>
                    <td style="text-align:center;">...</td>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Asiento</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                </tr>
            </thead><tbody id="tableNotaDebito">';
        $egresosMasivosTotal = 0;
        if ($egresosMasivos->count() > 0) {
            foreach ($egresosMasivos as $d) {
                if ($d->check_ == 'checked') {
                    $egresoMasivoDato = '';
                    $egresosMasivosTotal += $d->valor_pago;
                } else {
                    $egresoMasivoDato = '<input type="hidden" id="datoEgresoMasivo_|' . $d->id . '|' . $d->id_asiento_cabecera . '|' . $d->fecha_comprobante . '|' . $d->secuencia . '|' . $d->beneficiario . '|' . $d->no_cheque . '" name="datoEgresoMasivo_|' . $d->id . '|' . $d->id_asiento_cabecera . '|' . $d->fecha_comprobante . '|' . $d->secuencia . '|' . $d->beneficiario . '|' . $d->no_cheque . '" />';
                }
                $egresosMasivosHtml .= '<tr>
                            <td style="text-align:center;">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" class="cheEgresosMasivos" onchange="calcularEgresosMasivos(\'' . $d->id . '\',\'' . $d->valor_pago . '\',\'' . $d->id_asiento_cabecera . '\',\'' . $d->fecha_comprobante . '\',\'' . $d->secuencia . '\',\'' . $d->beneficiario . '\',\'' . $d->no_cheque . '\')" id="cheEgresosMasivos_' . $d->id . '" ' . $d->check_ . '>
                                    <label for="cheEgresosMasivos_' . $d->id . '">' . $egresoMasivoDato . '</label>
                                </div>
                                <div id="datoEgresoMasivo_' . $d->id . '"></div>
                            </td>
                            <td style="text-align:center;">' . date('Y-m-d', strtotime($d->fecha_comprobante)) . '</td>
                            <td style="text-align:center;">' . $d->tipo . '</td>
                            <td style="text-align:center;">' . $d->id_asiento_cabecera . '</td>
                            <td>' . $d->descripcion . '</td>
                            <td style="text-align:right;">' . $d->valor_pago . '</td>
                        </tr>';
            }
        }
        $egresosMasivosHtml .= '</tbody><tfoot><tr><td colspan="6" style="text-align:right;" id="totalEgresosMasivos">' . $egresosMasivosTotal . '</td></tr></tfoot></table>¬';
        $script = '<script>$(document).ready(function(){$(\'.tableConciliacion\').DataTable();})</script>¬';
        $salLi = isset($saldoAnteriorLibros->saldo_actual) ? $saldoAnteriorLibros->saldo_actual : 0;
        $salBa = isset($saldoAnteriorBancos->saldo_actual) ? $saldoAnteriorBancos->saldo_actual : 0;
        $saldoAnterior = $salLi . '¬' . $salBa . '¬' . $depositos->sum('valor_destino') . '|' . $transferenciasAcreditadas->sum('valor_destino') . '|' . $notasDebito->sum('valor') . '|' . $notaCredito->sum('valor') . '|' . $notasDebitoBancaria->sum('valor') . '|' . $egresos->sum('valor') . '|' . $egresosVarios->sum('valor') . '|' . $egresosMasivos->sum('valor_pago') . '|' . $conciliado . '|' . $transferenciasDebitadas->sum('valor_destino');
        return $depositosHtml . $transferenciaCreditoHtml . $notasDebitoHtml . $notasCreditoHtml . $notaDebitoBancarioHtml . $egresosHtml . $egresosVariosHtml . $egresosMasivosHtml . $script . $saldoAnterior . '¬' . $pendientes . '¬' . $pendientesAgregados . '¬' . $transferenciaDebitoHtml;
    }
    public static function buscarBancos($term)
    {
        return CajaBanco::selectRaw('id,concat(coalesce(numero_cuenta,"")," | ",coalesce(nombre,"")) as text')
            ->where(function ($q) use ($term) {
                $q->where('nombre', 'like', '%' . $term . '%')
                    ->orWhere('numero_cuenta', 'like', '%' . $term . '%');
            })
            ->where('estado', '1')
            ->where('clase', 1)
            ->where('id_empresa', session('id_empresa'))
            ->get();
    }
    public static function getListaCajas($id = '')
    {
        if ($id == '') {
            return CajaBanco::join('plan_cuentas as pc', 'pc.id', 'ct_caja_banco.cuenta_mayor')
                ->leftjoin('plan_cuentas_empresa as pce', 'pce.id_plan', 'pc.id')
                ->join('plan_cuentas AS pc1', 'pc1.id', '=', 'ct_caja_banco.grupo')
                ->leftjoin('ct_sucursales as s', 's.id', 'ct_caja_banco.sucursal')
                ->leftjoin('ct_tipo_pago as tp', 'tp.id', 'ct_caja_banco.formas_pago')
                ->where('pce.id_empresa', session('id_empresa'))
                ->where('ct_caja_banco.id_empresa', session('id_empresa'))
                ->where('ct_caja_banco.estado', 1)
                ->get([
                    'ct_caja_banco.id',
                    'ct_caja_banco.codigo',
                    'ct_caja_banco.nombre',
                    'ct_caja_banco.clase',
                    'ct_caja_banco.divisa',
                    'ct_caja_banco.sucursal',
                    'pc1.nombre as grupo',
                    'pce.nombre as nombre_mayor',
                    's.nombre_sucursal',
                    'tp.nombre as formas_pago',
                    'ct_caja_banco.estado',
                    'ct_caja_banco.cuenta_mayor'
                ]);
        } else {
            return CajaBanco::join('plan_cuentas as pc', 'pc.id', 'ct_caja_banco.cuenta_mayor')
                ->leftjoin('plan_cuentas_empresa as pce', 'pce.id_plan', 'pc.id')
                ->leftjoin('ct_sucursales as s', 's.id', 'ct_caja_banco.sucursal')
                ->leftjoin('ct_tipo_pago as tp', 'tp.id', 'ct_caja_banco.formas_pago')
                ->where('pce.id_empresa', session('id_empresa'))
                ->where('ct_caja_banco.id', $id)
                ->first([
                    'ct_caja_banco.id',
                    'ct_caja_banco.codigo',
                    'ct_caja_banco.nombre',
                    'ct_caja_banco.numero_cuenta',
                    'ct_caja_banco.clase',
                    'ct_caja_banco.divisa',
                    'ct_caja_banco.sucursal',
                    'ct_caja_banco.grupo',
                    'pce.nombre as nombre_mayor',
                    's.nombre_sucursal',
                    'ct_caja_banco.formas_pago',
                    'ct_caja_banco.ultimo_cheque',
                    'ct_caja_banco.comentarios',
                    'ct_caja_banco.estado',
                    'ct_caja_banco.cuenta_mayor'
                ]);
        }
    }
    public static function saveBancos($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                $data['id_empresa'] = session('id_empresa');
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = CajaBanco::insertGetId($data);
                $r->c = 'Caja Bancos';
                $r->s = 'saveCajaBancos';
                $r->d = $origin['d'];
                $r->m = CajaBanco::$modelo;
                $r->o = 'Se agrego la nueva caja/banco.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_empresa'] = session('id_empresa');
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                CajaBanco::where('id', $id)->update($data);
                $r->c = 'Caja Bancos';
                $r->s = 'saveCajaBancos';
                $r->d = $origin['d'];
                $r->m = CajaBanco::$modelo;
                $r->o = 'Se actualizo la nueva caja/banco.: ' . $id;
                $cont++;
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
            $r->c = 'Caja Bancos';
            $r->s = 'saveCajaBancos';
            $r->d =  $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => ''
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (CajaBanco::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
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
    public static function buscarCuenta($term)
    {
        return PlanCuentas::selectRaw('plan,CONCAT(plan, " | ",nombre) as text')
            ->where(function ($q) use ($term) {
                $q->where('plan', 'like', '%' . $term . '%')
                    ->orWhere('nombre', 'like', '%' . $term . '%')
                    ->orWhere('descripcion', 'like', '%' . $term . '%');
            })
            ->where('id_empresa', session('id_empresa'))
            ->get();
    }
    public static function getGrupo()
    {
        $cuenta_caja_cj = Configuraciones::obtener_cuenta('CAJA_BAN_CAJA');
        $cuenta_banco_cj = Configuraciones::obtener_cuenta('CAJA_BAN_BANCOS');
        $cuenta_obfinlo = Configuraciones::obtener_cuenta('CAJA_BAN_BANCOSLOCALES');

        $plan_cuenta = PlanCuentas::where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
            ->where(function ($query1) use ($cuenta_caja_cj, $cuenta_banco_cj, $cuenta_obfinlo) {
                $query1->where('plan_cuentas_empresa.plan', $cuenta_caja_cj->cuenta_guardar)
                    ->orWhere('plan_cuentas_empresa.plan', $cuenta_banco_cj->cuenta_guardar)
                    ->orWhere('plan_cuentas_empresa.plan', $cuenta_obfinlo->cuenta_guardar);
            })->get();
        return $plan_cuenta;
    }
    public static function getCuentaMayor($r)
    {
        $result = '<option value="">--' . trans('v3/new_varios.seleccionar') . '--</option>';
        $cuenta_mayor = PlanCuentas::join('plan_cuentas as pc', 'pc.id', 'plan_cuentas_empresa.plan')
            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
            ->where('plan_cuentas_empresa.id_padre', $r->id_grupo)
            ->select('plan_cuentas_empresa.plan as id', 'plan_cuentas_empresa.nombre as nombre')->get();
        if (count($cuenta_mayor) > 0) {
            foreach ($cuenta_mayor as $p) {
                $select = '';
                if ($r->id_grupo != 0) {
                    if ($r->id_grupo == $p->id_padre) {
                        $select = 'selected';
                    }
                }
                $result .= '<option value="' . $p->id . '" ' . $select . '>' . ucwords(strtolower(Utilidades::sanear_string_tildes($p->nombre))) . '</option>';
            }
        }
        return $result;
    }
    public static function traerctaGrupo($r)
    {
        $combo = '';
        $cuenta = PlanCuentas::selectRaw('plan, nombre as text')
            ->where('plan',  $r->id)
            ->first();
        if ($cuenta == '') {
            $cuenta = PlanCuentas::selectRaw('plan, nombre as text')
                ->where('id_plan',  $r->id)
                ->first();
        }
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->plan . '" selected>' . $cuenta->text . '</option>';
        }
        return $combo;
    }
    public static function tablaDepositoBancario($r)
    {
        $id_empresa  = session('id_empresa');
        $fecha_desde = $r->fecha_desde;
        $fecha_hasta = $r->fecha_hasta;
        $todo = $r->id_forma_pago;
        $letra = "";
        for ($i = 0; $i < count($todo); $i++) {
            if ($todo[$i] == "T") {
                $letra = "T";
            }
        }
        if ($letra == "T") {
            $detalle     = DetallePagoIngreso::where('id_tipo', '!=', '0')->where('estado_deposito', '<>', '1');
        } else {
            $detalle     = DetallePagoIngreso::whereIn('id_tipo', $r['id_forma_pago'])->where('estado_deposito', '<>', '1');
        }
        if (!empty($fecha_desde) || !empty($fecha_hasta)) {
            if (!empty($fecha_desde)) {
                $detalle = $detalle->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $detalle = $detalle->where('fecha', '<=', $fecha_hasta);
            }
        }
        $detalle = $detalle->orderBy('fecha', 'desc')->get();
        $html = "";
        $i = 0;
        foreach ($detalle as $value) {
            if ($value != null) {
                if ($value->new == 0) {
                    if ($value->cabecera_ingreso != null) {
                        if ($value->cabecera_ingreso->estado == 1 && $value->cabecera_ingreso->id_empresa == $id_empresa) {
                            if ($value->tipo_pago != null) {
                                $html .= "<tr>";
                                $html .= "<td style='text-align:center;'>";
                                $html .= "<input type='hidden' name='tipo_c[]' value='CI'>";
                                $html .= "<input type='hidden' id='{$value->id}' name='id_comp_n[]' >";
                                $html .= "<input  type='checkbox' id='id_comp" . $i . "' class='verify relactivo' name='id_comp[]' onchange='calcular_todo(); verificarChecks({$value->id}, {$value->cabecera_ingreso->id},id_comp{$i});' value='$value->id'> <input type='hidden' name='veractivo[]' class='veractivo' value='0'> <input class='totax' type='hidden' name='totales' value='$value->total'> <input type='hidden'class='tipox' name='tipx[]' value='" . $value->tipo_pago->nombre . "'>";
                                $html .= "</td>";
                                $html .= "<td style='text-align:center;'>" . $value->cabecera_ingreso->id . "</td>";
                                $html .= "<td>" . $value->tipo_c . "</td>";
                                $html .= "<td>" . $value->tipo_pago->nombre . "</td>";
                                $html .= "<td style='text-align:center;'>" . date('Y-m-d', strtotime($value->fecha)) . "</td>";
                                $html .= "<td>";
                                if (isset($value->cabecera_ingreso->detalle)) {
                                    foreach ($value->cabecera_ingreso->detalle as $det) {
                                        if (isset($det->ventas) > 0) {
                                            $html .= "<small> {$det->ventas->nro_comprobante} </small>";
                                        }
                                    }
                                }
                                $html .= "</td>";
                                $html .= "<td>" . $value->numero . "</td>";
                                if ($r->id_forma_pago == 999 || $r->id_forma_pago == "-1") {

                                    $traer = TipoTarjeta::where('id', $value->id_tipo_tarjeta)->first();
                                    if (($traer) != null) {
                                        $html .= "<td>" . $traer->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                } else {
                                    if (isset($value->banco)) {
                                        $html .= "<td>" . $value->banco->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                }
                                $html .= "<td>" . $value->cuenta . "</td>";
                                $html .= "<td>" . $value->girador . "</td>";
                                $html .= "<td style='text-align:center;'>$</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "</tr>";
                            }
                        }
                    }
                } else {
                    if ($value->cabecera_ingresov != null) {
                        if ($value->cabecera_ingresov->estado == 1 && $value->cabecera_ingresov->id_empresa == $id_empresa) {
                            if ($value->tipo_pago != null) {
                                $html .= "<tr>";
                                $html .= "<td>";
                                $html .= "<input type='hidden' name='tipo_c[]' value='CIV'>";
                                $html .= "<input type='hidden' name='id_comp_v[]'  value='{$value->cabecera_ingresov->id}'>";
                                $html .= "<input type='checkbox' id='id_comp" . $i . "' class='verify relactivo' name='id_comp[]' onchange='calcular_todo();' value='$value->id'> <input type='hidden' name='veractivo[]' class='veractivo' value='0'> <input class='totax' type='hidden' name='totales' value='$value->total'> <input type='hidden'class='tipox' name='tipx[]' value='" . $value->tipo_pago->nombre . "'>";
                                $html .= "</td>";
                                $html .= "<td>" . $value->cabecera_ingresov->id . "</td>";
                                $html .= "<td>" . $value->tipo_c . "</td>";
                                $html .= "<td>" . $value->tipo_pago->nombre . "</td>";
                                $html .= "<td>" . date('d/m/Y', strtotime($value->fecha)) . "</td>";
                                $html .= "<td>" . $value->cabecera_ingresov->secuencia . "</td>";
                                $html .= "<td>" . $value->numero . "</td>";
                                if ($r->id_forma_pago == 999) {

                                    $traer = TipoTarjeta::where('id', $value->id_tipo_tarjeta)->first();
                                    if (isset($traer)) {
                                        $html .= "<td>" . $traer->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                } else {
                                    if (isset($value->banco)) {
                                        $html .= "<td>" . $value->banco->nombre . "</td>";
                                    } else {
                                        $html .= "<td></td>";
                                    }
                                }
                                $html .= "<td>" . $value->cuenta . "</td>";
                                $html .= "<td>" . $value->girador . "</td>";
                                $html .= "<td>$</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "<td style='text-align: right;'>" . number_format($value->total, 2, '.', '') . "</td>";
                                $html .= "</tr>";
                            }
                        }
                    }
                }
            }
            $i++;
        }
        return $html;
    }
    public static function guardarDepositoBancario($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($r->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'message' => $verifica_cierre->message];
            }
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                unset($data['id']);
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $cod_sucurs = PuntoEmision::where('estado', '1')
                    ->where('id', $r->sucursal_empresa)->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj = Caja::where('estado', '1')->where('id', $r->punto_emision)->first();
                $c_caja = $cod_caj->codigo_caja;
                $datos = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja,
                    'tipo' => 1
                ];
                //$num_comprobante = Ventas::callPrivateStisGenerarNumero($datos);
                $total_final = 0;
                if (!is_null($data['total_efectivo'])) {
                    $total_final = $data['total_efectivo'];
                }
                if (!is_null($data['total_pap_deposito'])) {
                    $total_final = $data['total_pap_deposito'];
                }
                if (!is_null($data['total_cheques'])) {
                    $total_final = $data['total_cheques'];
                }
                if (!is_null($data['total_tarjetas'])) {
                    $total_final = $data['total_tarjetas'];
                }
                if (!is_null($data['total_datalink'])) {
                    $total_final = $data['total_datalink'];
                }
                if (!is_null($data['total_crucecuentas'])) {
                    $total_final = $data['total_crucecuentas'];
                }
                if (!is_null($data['total_deposito'])) {
                    $total_final = $data['total_deposito'];
                };
                $numero = DepositoBancario::getNumeroSecuencial();
                $arrayAsientoCabecera = [
                    'sucursal' => $c_sucursal,
                    'punto_emision' => $c_caja,
                    'fecha_asiento' => $r->fecha_asiento,
                    'fact_numero' => $numero,
                    'id_empresa' => session('id_empresa'),
                    'observacion' => $r->concepto,
                    'valor' => $total_final,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ];
                $id_asiento_cabecera = Contabilidad::crearAsiento('cajaBanco', 'deposito', $arrayAsientoCabecera,  $r, "cajaBanco");
                if ($id_asiento_cabecera['status'] == 'error') {
                    throw new Exception($id_asiento_cabecera['message']);
                }
                $input = [
                    'id_asiento'        => $id_asiento_cabecera,
                    'fecha_asiento'      => $data['fecha_asiento'],
                    'empresa'            => session("id_empresa"),
                    'numero'             => $numero,
                    'tipo'               => "BAN-DP",
                    'concepto'           => $data['concepto'],
                    'nota'               => $data['nota'],
                    'estado'             => 1,
                    'id_cuenta_origen'   => $data["id_cuenta_origen"],
                    'valor_origen'       => $data['total_efectivo'],
                    'id_cuenta_destino'  => $data['id_cuenta_destino'],
                    'valor_destino'      => $data['valor1'],
                    'total_efectivo'     => $data['total_efectivo'],
                    'total_pap_deposito' => $data['total_pap_deposito'],
                    'total_cheque'       => $data['total_cheques'],
                    'total_tarjeta'      => $data['total_tarjetas'],
                    'total_deposito'     => $data['total_deposito'],
                    'id_usuariocrea'     => Auth::user()->id,
                    'id_usuariomod'      => Auth::user()->id,
                    'ip_creacion'        => $ip,
                    'ip_modificacion'    => $ip,
                ];
                $registro =  DepositoBancario::insertGetId($input);
                $detalle = $data['id_comp'];
                foreach ($detalle as $det) {
                    $facturas = "";
                    $data = DetallePagoIngreso::where('id', $det)->first();
                    $data->estado_deposito = 1;
                    $data->save();
                    if (isset($data->cabecera_ingreso->detalle)) {
                        foreach ($data->cabecera_ingreso->detalle as $detdeldet) {
                            $facturas .= "$detdeldet->secuencia_factura,";
                        }
                    }
                    $inputdetalle = [
                        'deposito_bancario_id' => $registro,
                        'numero'               => $data->numero,
                        'tipo'                 => $data->id_tipo,
                        'id_ingreso'           => $det,
                        'fecha'                => $data->fecha,
                        'facturas'             => $facturas,
                        'banco'                => $data->id_banco,
                        'cuenta'               => $data->cuenta,
                        'girador'              => $data->girador,
                        'id_divisa'            => 1,
                        'importe'              => $data->total,
                        'valor'                => $data->total,
                        'valor_base'           => $data->total,
                        'id_usuariocrea'       => Auth::user()->id,
                        'id_usuariomod'        => Auth::user()->id,
                        'ip_creacion'          => $ip,
                        'ip_modificacion'      => $ip,
                        'tipo_comprobante'     => is_null($data->tipo_c) ? 'CI' : $data->tipo_c
                    ];
                    DetalleDepositoBancario::insert($inputdetalle);
                }
                $cont++;
                $r->c = 'DepositoBancario';
                $r->s = 'saveDepositoBancario';
                $r->d = $origin['d'];
                $r->m = CajaBanco::$modelo;
                $r->o = 'Se agrego el nuevo transportista con id.: ';
                $cont++;
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
            $r->c = 'Deposito Bancario';
            $r->s = 'saveDepositoBancario';
            $r->d =  $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/cajabancos/depositobancario'
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function allDataDepositoBancario($r)
    {
        if (isset($r->desde)) {
            return  DepositoBancario::orderby('id', 'asc')->where('empresa', session("id_empresa"))->whereBetween("created_at", [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])->get();
        } else {
            return  DepositoBancario::find($r);
        }
    }
    public static function allDataNotaCredito($r)
    {
        if (isset($r->desde)) {

            return NotadeCredito::leftJoin("users", "users.id", "ct_nota_credito_cabecera.id_usuariocrea")
                ->orderBy('ct_nota_credito_cabecera.id', 'asc')
                ->where('ct_nota_credito_cabecera.empresa', session("id_empresa"))
                ->whereBetween("ct_nota_credito_cabecera.created_at", [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])
                ->select("ct_nota_credito_cabecera.*", DB::raw("CONCAT(users.nombre1, ' ', users.apellido1) AS nombre_completo"))
                ->get();
        } else {
            return  NotadeCredito::find($r);
        }
    }
    public static function anular_asiento($asiento, $registro, $observacion)
    {
        $cabasiento = AsientosCabecera::find($registro);
        $cabasiento->estado = 1;
        $ip = Utilidades::getRealIP();
        $cabasiento->id_usuariomod = Auth::user()->id;
        $cabasiento->save();
        $estado_asiento = AsientosCabecera::where('id', $asiento)->where('estado', '<>', 0)->first();
        if (!empty($estado_asiento)) {
            $input_cabecera = [
                'observacion' => $observacion,
                'fecha_asiento' => $cabasiento->fecha_asiento,
                'id_empresa' => $cabasiento->id_empresa,
                'valor' => $cabasiento->valor,
                'id_usuariocrea' =>  Auth::user()->id,
                'id_usuariomod' =>  Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'sucursal' => $cabasiento->sucursal,
            ];
            $id_asiento_cabecera = AsientosCabecera::insertGetId($input_cabecera);
            $detasiento = AsientosDetalles::where('id_asiento_cabecera', $asiento)->orderBy('id', 'desc')->get();
            foreach ($detasiento as $row) {
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta' => $row->id_plan_cuenta,
                    'descripcion' => $row->descripcion,
                    'fecha' => $cabasiento->fecha_asiento,
                    'debe' => $row->haber,
                    'haber' => $row->debe,
                    'estado' => '1',
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ];
                AsientosDetalles::create($asiento_detalle);
            }
        }
    }
    public static function anularDeposito($r)
    {
        try {
            $registro = DepositoBancario::find($r["id"]);
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($registro->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $fechahoy   = Date('Y-m-d H:i:s');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $act_estado = [
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $detalles = DetalleDepositoBancario::where('deposito_bancario_id', $registro->id)->get();
            if (!is_null($detalles)) {
                foreach ($detalles as $z) {
                    $x = [
                        'estado'          => '0',
                        'estado_deposito' => '0',
                        'ip_modificacion' => $ip_cliente,
                    ];
                    DetallePagoIngreso::where('id', $z->id_ingreso)->update($x);
                }
            }
            DepositoBancario::where('id', $r["id"])->update($act_estado);
            DetalleDepositoBancario::where('deposito_bancario_id', $registro->id)->update($act_estado);
            self::anular_asiento($r, $registro->id_asiento, $r->text);
            DB::commit();
            return ['status' => 'success', 'msj' => 'Anulado Correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }
    public static function menuArmar()
    {
        $html = '';
        $results = Menu::join('new_db_menu as m2', 'new_db_menu.id_menu', 'm2.id_pertenece_menu')
            ->join('new_db_menu as m3', 'm2.id_menu', 'm3.id_pertenece_menu')
            ->where('new_db_menu.estado_menu', 1)
            ->whereNull('new_db_menu.id_pertenece_menu')
            ->get([
                'new_db_menu.id_menu as id_menu',
                'm2.id_pertenece_menu as id_submenu',
                'm3.id_pertenece_menu as id_submenu_',
                'new_db_menu.nombre_menu AS menu',
                'new_db_menu.icon_menu',
                'new_db_menu.controlador_menu',
                'm2.nombre_menu AS submenu',
                'm2.icon_menu AS icon_submenu',
                'm2.funcion_menu AS funcion',
                'm3.nombre_menu AS submenu_1',
                'm3.icon_menu AS icon_submenu_1',
                'm3.funcion_menu AS funcion_1'
            ]);
        $html .= '<select name="menu">';
        $menuHierarchy = [];
        foreach ($results as $result) {
            $menu = [
                'nombre_menu' => $result->menu,
                'submenus' => []
            ];
            if (!empty($result->submenu)) {
                $submenu = [
                    'nombre_menu' => $result->submenu,
                    'submenus' => []
                ];
                if (!empty($result->submenu_1)) {
                    $submenu_1 = [
                        'nombre_menu' => $result->submenu_1,
                        'submenus' => []
                    ];
                    $submenu['submenus'][] = $submenu_1;
                }
                $menu['submenus'][] = $submenu;
            }
            if (isset($menuHierarchy[$result->menu])) {

                $menuHierarchy[$result->menu]['submenus'] = array_merge($menuHierarchy[$result->menu]['submenus'], $menu['submenus']);
            } else {

                $menuHierarchy[$result->menu] = $menu;
            }
        }

        return $menuHierarchy;
    }
    public static function verificarNombrecajabanco($r)
    {
        $arrayRespuesta = [];
        $NombCajaBanco = CajaBanco::where(function ($query) use ($r) {
            $query->where('nombre', $r->nombre)
                ->orWhere('codigo', $r->codigo);
        })
            ->where('id_empresa', session('id_empresa'))
            ->first(['nombre']);

        if ($NombCajaBanco != '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'El nombre o codigo ya se encuentra registrado...',
                'data' => $r->input(),
            ];
        } else {
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => '',
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function anularTrasferencia($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $estado_transferencia = TransferenciasBancarias::where('id', $r->id)->where('estado', '<>', 0)->first();
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($estado_transferencia->fecha_asiento));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            if (!empty($estado_transferencia)) {
                $act_estado = [
                    'estado'          => '0',
                    'ip_modificacion' => $ip,
                ];
                $registro = TransferenciasBancarias::find($r->id);
                TransferenciasBancarias::where('id', $r->id)->update($act_estado);
                $cabasiento           = AsientosCabecera::find($registro->id_asiento);
                $estado_transferencia = AsientosCabecera::where('id', $r->id)->where('estado', '<>', 0)->first();
                if (!empty($estado_transferencia)) {
                    $input_cabecera = [
                        'observacion'     => $r->text,
                        'fecha_asiento'   => date("Y-m-d"),
                        'id_empresa'      => $cabasiento->id_empresa,
                        'valor'           => $cabasiento->valor,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip,
                        'ip_modificacion' => $ip,
                        'sucursal'        => $cabasiento->sucursal,
                    ];
                    $id_asiento_cabecera = AsientosCabecera::insertGetId($input_cabecera);
                    $detasiento = AsientosDetalles::where('id_asiento_cabecera', $r->id)->orderBy('id', 'desc')->get();
                    foreach ($detasiento as $row) {
                        $asiento_detalle = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $row->id_plan_cuenta,
                            'descripcion'         => $row->descripcion,
                            'fecha'               => date("Y-m-d"),
                            'debe'                => $row->haber,
                            'haber'               => $row->debe,
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip,
                            'ip_modificacion'     => $ip,
                        ];
                        AsientosDetalles::insert($asiento_detalle);
                    }
                }
                LogAsiento::anulacion('TB', $id_asiento_cabecera, $r->id);
            }
            $r->c = 'CajaBancos';
            $r->s = 'anularTrasferenciaBancaria';
            //$r->d = $origin['d'];
            $r->m = CajaBanco::$modelo;
            $r->o = 'Se anulo la nota de debito con id.: ' . $r->id;
            $cont++;
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
            $r->s = 'anula transferencia bancaria';
            //$r->d =  $origin['id'];
            $r->m = CajaBanco::$modelo;
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
                'reload' => "",
            ];
            return $arrayRespuesta;
        }
    }
    public static function anularNotaCredito($r)
    {
        try {
            $registro = NotaCredito::find($r->id);
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($registro->fecha));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            if (!empty($registro)) {
                $registro->estado = 0;
                $registro->save();
                $consulta_cabecera_nc = AsientosCabecera::where('estado', '1')
                    ->where('id', $registro['id_asiento'])->first();
                $input_cabecera = [
                    'observacion'     => 'ANULACIÓN NOTA DE CRÉDITO',
                    'fecha_asiento'   => $consulta_cabecera_nc->fecha_asiento,
                    'id_empresa'      => $consulta_cabecera_nc->id_empresa,
                    'valor'           => $consulta_cabecera_nc->valor,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip,
                    'ip_modificacion' => $ip,
                    'sucursal'        => $consulta_cabecera_nc->sucursal,
                ];
                $id_asiento_cab = AsientosCabecera::insertGetId($input_cabecera);
                $consulta_detalle = AsientosDetalles::where('id_asiento_cabecera', $consulta_cabecera_nc->id)->get();
                if (!empty($consulta_detalle)) {
                    foreach ($consulta_detalle as $value) {
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $id_asiento_cab,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'descripcion'         => 'ANULACION DE NOTA DE DEBITO',
                            'fecha'               => $consulta_cabecera_nc->fecha_asiento,
                            'haber'               => $value->debe,
                            'debe'                => $value->haber,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip,
                            'ip_modificacion'     => $ip,
                        ]);
                    }
                }
                LogContable::insert([
                    'tipo'           => 'NC',
                    'valor_ant'      => $consulta_cabecera_nc->valor,
                    'valor'          => $consulta_cabecera_nc->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                    'observacion'    => $consulta_cabecera_nc->concepto,
                    'id_ant'         => $consulta_cabecera_nc->id,
                    'id_referencia'  => $id_asiento_cab,
                ]);
            }
            return ['status' => 'success', 'msj' => 'Anulado Correctamente'];
        } catch (Exception $e) {
            dd($e->getMessage());
            return ['status' => 'error', 'msj' => 'Ocurrio un error',];
        }
    }
}
