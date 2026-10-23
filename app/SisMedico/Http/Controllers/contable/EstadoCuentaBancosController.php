<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Http\Controllers\Controller;
use Excel;
use Sis_medico\Empresa;

class EstadoCuentaBancosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $bancos     = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->where('clase', 1)->get();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }
        if (!is_null($request['banco'])) {
            $banco = $request['banco'];
        } else {
            $banco = "";
        }
        if (!is_null($request['asiento_id'])) {
            $asiento_id = $request['asiento_id'];
        } else {
            $asiento_id = "";
        }
        $registros     = array();
        $saldoanterior = 0;
        if (isset($request['buscarAsiento'])) {
            $registros     = $this->getRegistros($fecha_desde, $fecha_hasta, $banco, $asiento_id, $id_empresa);
            $saldoanterior = $this->getSaldoAnterior($fecha_desde, $banco, $asiento_id, $id_empresa);
        }

        $variable = 0;
        if (isset($request['tipo'])) {
            $variable = $request->tipo;
        }
        $tipo = $request['tipo'];
        $empresa = Empresa::find($id_empresa);
        $fecha2 = $request['fecha_hasta'];
        return view('contable/estado_cuenta_bancos/index', [
            'registros' => $registros,
            'fecha2' => $fecha2,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'bancos'  => $bancos,
            'empresa' => $empresa,
            'banco' => $banco,
            'saldoanterior' => $saldoanterior,
            'tipos' => $tipo,
            'variable' => $variable
        ]);
    }

    public function getRegistros($fecha_desde, $fecha_hasta, $banco = "", $asiento_id = "", $id_empresa)
    {
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        //dd($fecha_desde);
        $timestamp   = \Carbon\Carbon::parse($fecha_desde)->timestamp;
        $fecha_desde = date('Y-m-d', $timestamp);
        //dd($fecha_desde);
        $fecha_hasta = str_replace('/', '-', $fecha_hasta);
        $timestamp   = \Carbon\Carbon::parse($fecha_hasta)->timestamp;
        $fecha_hasta = date('Y-m-d', $timestamp);
        //dd($fecha_hasta);
        $detalles = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
            ->where('pe.id_empresa', $id_empresa)
            ->where('c.id_empresa', $id_empresa)
            ->whereBetween('c.fecha_asiento', [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])
            ->where('c.fecha_asiento', '>', '1999-01-01 00:00:00')
            ->where('c.estado', '<>', 0);
        //dd($detalles);
        if ($asiento_id != "") {
            $detalles = $detalles->where('c.id', '=', $asiento_id);
        }
        //dd($detalles);
        if ($banco != "") {
            $ban = Ct_Caja_Banco::where('id', $banco)->first();
            //dd($ban);
            if (isset($ban->cuenta_mayor)) {
                // $detalles = $detalles->where('id_plan_cuenta', '=', $ban->cuenta_mayor);
                $detalles = $detalles->where('p.id', '=', $ban->cuenta_mayor);
            }
        } else {
            $bancos    = Ct_Caja_Banco::where('estado', '1')->where('clase', 1)->get();
            $condicion = "";
            foreach ($bancos as $row) {
                $condicion .= "$row->cuenta_mayor,";
            }
            if ($condicion != "") {
                $cuentasbancos = explode(',', $condicion);
                $detalles      = $detalles->whereIn('id_plan_cuenta', $cuentasbancos);
            }
        }
        $detalles = $detalles->orderBy('fecha_asiento', 'asc')->get();
        //dd($detalles);
        return $detalles;
    }

    public function getSaldoAnterior($fecha_desde, $banco = "", $asiento_id, $id_empresa)
    {
        //asientos 34658 - 34652
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        $timestamp   = \Carbon\Carbon::parse($fecha_desde)->timestamp;
        $fecha_desde = date('Y-m-d', $timestamp);

        $detalles = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
            ->where('pe.id_empresa', $id_empresa)
            ->where('c.fecha_asiento', '<', $fecha_desde . ' 00:00:00')
            ->where('c.fecha_asiento', '>', '1999-01-01 00:00:00')
            ->where('c.id_empresa', $id_empresa);
        if ($asiento_id != "") {
            $detalles = $detalles->where('c.id', '=', $asiento_id);
        }
        if ($banco != "") {
            $ban = Ct_Caja_Banco::where('id', $banco)->first();
            if (isset($ban->cuenta_mayor)) {
                //$detalles = $detalles->where('ct_asientos_detalle.id_plan_cuenta', '=', $ban->cuenta_mayor);
                $detalles = $detalles->where('p.id', '=', $ban->cuenta_mayor);
            }
        } else {
            $bancos    = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->where('clase', 1)->get();
            $condicion = "";
            foreach ($bancos as $row) {
                $condicion .= "$row->cuenta_mayor,";
            }
            if ($condicion != "") {
                $cuentasbancos = explode(',', $condicion);
                $detalles      = $detalles->whereIn('ct_asientos_detalle.id_plan_cuenta', $cuentasbancos);
            }
        }
        $detalles = $detalles->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))->first();
        return $detalles->saldo;
    }
    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd("entra");
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $bancos     = Ct_Caja_Banco::where('estado', '1')->get();
        $fecha_desde = null;
        $fecha_hasta = null;
        if (!is_null($request['fecha_desde2']) && !is_null($request['fecha_hasta2'])) {
            $fecha_desde = $request['fecha_desde2'];
            $fecha_hasta = $request['fecha_hasta2'];
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }
        if (!is_null($request['banco2'])) {
            $banco = $request['banco2'];
        } else {
            $banco = "";
        }
        if (!is_null($request['asiento_id'])) {
            $asiento_id = $request['asiento_id'];
        } else {
            $asiento_id = "";
        }
        $registros     = array();
        $saldoanterior = 0;
        //dd($fecha_desde,$fecha_hasta);
        // $fech= date("d-m-Y", strtotime($fecha_desde));
        //$fech2=  date("d-m-Y", strtotime($fecha_hasta));
        if (isset($request['buscarAsiento0'])) {
            //dd($banco);

        }
        $tipo = $request['tipo2'];

        $registros     = $this->getRegistros($fecha_desde, $fecha_hasta, $banco, $asiento_id, $id_empresa);
        $saldoanterior = $this->getSaldoAnterior($fecha_desde, $banco, $asiento_id, $id_empresa);
        //dd($registros);
        Excel::create('EstadoCuentaBancos-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $registros, $fecha_hasta, $tipo, $saldoanterior) {
            $excel->sheet('EstadoCuentaBancos', function ($sheet) use ($empresa,  $registros, $fecha_hasta, $saldoanterior, $tipo) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:A2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Banco ');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ref');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cheque');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G2:G2');
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H2:H2');
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Debe');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I2:I2');
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Haber');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J2:J2');
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:A3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B3:B3');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:C3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D3:D3');
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E3:E3');
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F3:F3');
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Anterior');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('G3:G3');
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('H3:H3');
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I3:I3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J3:J3');
                $sheet->cell('J3', function ($cell) use ($saldoanterior) {
                    // manipulate the cel
                    $cell->setValue($saldoanterior);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function ($cell)  use ($fecha_hasta) {
                    // manipulate the cel
                    $cell->setValue('-' . $fecha_hasta);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = 4;
                //dd($registros);
                $saldo = $saldoanterior;
                $saldodebe = 0;
                $saldohaber = 0;
                $totaldeb = 0;
                $totalcre = 0;

                $total = 0;
                $numero_factura = 0;
                foreach ($registros as $value) {

                    if ($tipo == "") {
                        if (isset($value->cabecera->egresos)) {
                            if ($value->cabecera->egresos->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('ACR-EG');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos->secuencia);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos->no_cheque);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos->proveedor->nombrecomercial);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos->descripcion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->egresos_varios)) {
                            if ($value->cabecera->egresos_varios->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('ACR-EGV');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel

                                    $cell->setValue($value->cabecera->egresos_varios->secuencia);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos_varios->nro_cheque);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos_varios->beneficiario);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->egresos_varios->descripcion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->debito)) {
                            if ($value->cabecera->debito->estado == 1) {

                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('BAN-ND');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $max_id = intval($value->cabecera->debito->id);
                                    $numero_factura = 0;
                                    if (strlen($max_id) < 10) {

                                        $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                    }
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->debito->concepto);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->depositos)) {
                            if ($value->cabecera->depositos->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value->cabecera->depositos->fecha_asiento)));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value->cabecera->depositos->fecha_asiento)));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('BAN-DEP');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $max_id = intval($value->cabecera->depositos->id);
                                    $numero_factura = 0;
                                    if (strlen($max_id) < 10) {

                                        $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                    }
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->depositos->concepto);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->baneg)) {
                            if ($value->cabecera->baneg->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('BAN-EG');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->baneg->secuencia);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    if (isset($value->cabecera->baneg->acreedor)) {
                                        $cell->setValue($value->cabecera->baneg->acreedor->nombrecomercial);
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->baneg->concepto);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->nota_credito)) {
                            if ($value->cabecera->nota_credito->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('BAN-NC');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    $max_id = intval($value->cabecera->nota_credito->id);
                                    $numero_factura = 0;
                                    if (strlen($max_id) < 10) {

                                        $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                    }
                                    // manipulate the cel
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('E' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->nota_credito->descripcion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->deposito)) {
                            if ($value->cabecera->deposito->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('DP');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    $max_id = intval($value->cabecera->id);
                                    $numero_factura = 0;
                                    if (strlen($max_id) < 10) {

                                        $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                    }
                                    // manipulate the cel
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->deposito->concepto);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->transferencia)) {
                            if ($value->cabecera->transferencia->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('BAN-TR');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    $max_id = intval($value->cabecera->id);
                                    $numero_factura = 0;
                                    if (strlen($max_id) < 10) {

                                        $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                    }
                                    // manipulate the cel
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->transferencia->concepto);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        } elseif (isset($value->cabecera->masivo)) {
                            if ($value->cabecera->masivo->estado == 1) {
                                $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe  += $value['debe'];
                                $saldohaber += $value['haber'];
                                $datos       = $value->cabecera->egresos;
                                $totaldeb  += $value['debe'];
                                $totalcre  += $value['haber'];
                                $total = $saldo;
                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('B' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });


                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('CAM');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    $numero_factura = ($value->cabecera->masivo->secuencia);
                                    // manipulate the cel
                                    $cell->setValue($numero_factura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });



                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->masivo->no_cheque);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->masivo->girado_a);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                # code...
                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cabecera->masivo->descripcion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('H' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['debe']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value['haber']);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });
                                $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                    // manipulate the cel
                                    $cell->setValue($saldo);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    // $this->setSangria($cont, $cell);
                                });

                                $i++;
                            }
                        }
                    } else {
                        if ($tipo == 1) {
                            if (isset($value->cabecera->egresos)) {
                                if ($value->cabecera->egresos->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('ACR-EG');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos->secuencia);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos->no_cheque);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos->proveedor->nombrecomercial);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos->descripcion);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 2) {
                            //dd("hola");
                            if (isset($value->cabecera->egresos_varios)) {
                                if ($value->cabecera->egresos_varios->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('ACR-EGV');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        // manipulate the cel

                                        $cell->setValue($value->cabecera->egresos_varios->secuencia);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos_varios->nro_cheque);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos_varios->beneficiario);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->egresos_varios->descripcion);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 3) {
                            if (isset($value->cabecera->baneg)) {
                                if ($value->cabecera->baneg->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('BAN-EG');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->baneg->secuencia);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        if (isset($value->cabecera->baneg->acreedor)) {
                                            $cell->setValue($value->cabecera->baneg->acreedor->nombrecomercial);
                                        }

                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->baneg->concepto);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 4) {
                            if (isset($value->cabecera->debito)) {
                                if ($value->cabecera->debito->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('BAN-ND');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $max_id = intval($value->cabecera->debito->id);
                                        $numero_factura = 0;
                                        if (strlen($max_id) < 10) {

                                            $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                        }
                                        $cell->setValue($numero_factura);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('F' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->debito->concepto);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 5) {
                            if (isset($value->cabecera->depositos)) {
                                if ($value->cabecera->depositos->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value->cabecera->depositos->fecha_asiento)));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value->cabecera->depositos->fecha_asiento)));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('BAN-DEP');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $max_id = intval($value->cabecera->depositos->id);
                                        $numero_factura = 0;
                                        if (strlen($max_id) < 10) {

                                            $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                        }
                                        $cell->setValue($numero_factura);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('F' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->depositos->concepto);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 6) {
                            if (isset($value->cabecera->nota_credito)) {
                                if ($value->cabecera->nota_credito->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('BAN-NC');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        $max_id = intval($value->cabecera->nota_credito->id);
                                        $numero_factura = 0;
                                        if (strlen($max_id) < 10) {

                                            $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                        }
                                        // manipulate the cel
                                        $cell->setValue($numero_factura);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('E' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('F' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->nota_credito->descripcion);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 7) {
                            if (isset($value->cabecera->transferencia)) {
                                if ($value->cabecera->transferencia->estado == 1) {
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('BAN-TR');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        $max_id = intval($value->cabecera->id);
                                        $numero_factura = 0;
                                        if (strlen($max_id) < 10) {

                                            $numero_factura = str_pad($max_id, 10, "0", STR_PAD_LEFT);
                                        }
                                        // manipulate the cel
                                        $cell->setValue($numero_factura);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('F' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->transferencia->concepto);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        } elseif ($tipo == 8) {
                            if (isset($value->cabecera->masivo)) {
                                if ($value->cabecera->masivo->estado == 1) {
                                    $saldo      = $saldo + ($value['debe'] - $value['haber']);
                                    $saldodebe  += $value['debe'];
                                    $saldohaber += $value['haber'];
                                    $datos       = $value->cabecera->egresos;
                                    $totaldeb  += $value['debe'];
                                    $totalcre  += $value['haber'];
                                    $total = $saldo;
                                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });


                                    $sheet->cell('C' . $i, function ($cell) {
                                        // manipulate the cel
                                        $cell->setValue('CAM');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                                        $numero_factura = ($value->cabecera->masivo->secuencia);
                                        // manipulate the cel
                                        $cell->setValue($numero_factura);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });



                                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->masivo->nro_cheque);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->masivo->girado_a);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    # code...
                                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value->cabecera->masivo->descr);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['debe']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue($value['haber']);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });
                                    $sheet->cell('J' . $i, function ($cell) use ($saldo) {
                                        // manipulate the cel
                                        $cell->setValue($saldo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        // $this->setSangria($cont, $cell);
                                    });

                                    $i++;
                                }
                            }
                        }
                    }
                }




                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Debitos');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('C' . $i, function ($cell) use ($totaldeb) {
                    // manipulate the cel
                    $cell->setValue($totaldeb);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Creditos');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('E' . $i, function ($cell) use ($totalcre) {
                    // manipulate the cel
                    $cell->setValue($totalcre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Final');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('G' . $i, function ($cell) use ($saldo) {
                    // manipulate the cel
                    $cell->setValue($saldo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                ));
            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H2:H2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I2:I2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('J2:J2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function setDetalles($consulta, $sheet, $i)
    {

        foreach ($consulta as $value) {

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->numero2);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });


            return $i;
        }
    }
}
