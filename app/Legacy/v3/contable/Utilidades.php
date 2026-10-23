<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Support\Facades\Storage;
use Sis_medico\v3\contable\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Image;
use Response;
use DateTime;
use Sis_medico\Ct_productos;
use Sis_medico\Log_usuario;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Empresa;
use SoapClient;

class Utilidades extends Model
{
    protected $keyType = 'string';

    public static function excelCarteraPagar($r)
    {
        //dd($r->all());
        $id_empresa = session("id_empresa");
        $deudas = DB::table('proveedor as p')
            ->leftJoin('ct_compras as c', 'p.id', '=', 'c.proveedor')
            ->leftJoin('ct_termino as t', 'c.termino', '=', 't.id')
            ->select(
                'c.id',
                'c.fecha',
                't.dias',
                'p.razonsocial',
                'p.id as proveedor_id',
                DB::raw('DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY) AS vencimiento'),
                DB::raw('DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) AS dias_transcurridos'),
                'c.observacion',
                DB::raw('ROUND(c.total_final, 2) AS saldo'),
                DB::raw('CASE WHEN DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) > t.dias THEN ROUND(c.total_final, 2) ELSE 0 END AS valor_vencido'),
                DB::raw('CASE WHEN DATE(NOW()) = DATE(DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) THEN ROUND(c.total_final, 2) ELSE 0 END AS periodo'),
                DB::raw('CASE WHEN DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) <= t.dias THEN ROUND(c.total_final, 2) ELSE 0 END AS valor_por_vencer')
            )
            ->where('c.estado', '<>', 0)
            ->where(function ($q) use ($r) {
                if ($r->proveedor != '') {
                    $q->where('p.id', $r->proveedor);
                }
            })
            ->where('c.id_empresa', $id_empresa)
            ->whereBetween('c.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->where('c.valor_contable', '>', 0)
            ->groupBy('p.id', 'c.id')
            ->get();


        $resultados = [];
        foreach ($deudas as $deuda) {
            $proveedorId = $deuda->proveedor_id;
            if (!isset($resultados[$proveedorId])) {
                $resultados[$proveedorId] = [
                    'codigo' => $deuda->proveedor_id,
                    'cuenta' => $deuda->razonsocial,
                    'valor_vencido_total' => 0,
                    'valor_periodo_total' => 0,
                    'valor_por_vencer_total' => 0,
                    'detalles' => []
                ];
            }
            $resultados[$proveedorId]['valor_vencido_total'] += $deuda->valor_vencido;
            $resultados[$proveedorId]['valor_periodo_total'] += $deuda->periodo;
            $resultados[$proveedorId]['valor_por_vencer_total'] += $deuda->valor_por_vencer;
            $resultados[$proveedorId]['detalles'][] = [
                'fecha' => $deuda->fecha,
                'vencimiento' => $deuda->vencimiento,
                'observacion' => $deuda->observacion,
                'saldo' => $deuda->saldo
            ];
        }

        $titulos = array("#", "Código", "Proveedor", "Vencido", "Periodo", "Por vencer");
        $titulos2 = array("#", "Fecha", "Fecha Vence", "Observaciones", "Saldo");
        $posicion = array("A", "B", "C", "D", "E", "F", "G");


        return Excel::create('Reporte Cartera Por Pagar', function ($excel) use ($posicion, $titulos, $resultados, $titulos2) {
            $excel->sheet('Reporte Cartera Por Pagar', function ($sheet) use ($posicion, $titulos, $resultados, $titulos2) {


                $sheet->mergeCells('A1:F1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Cartera Por Pagar');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#1870C8');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });

                $comienzo = 2;

                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $total_vencido = 0;
                $total_periodo = 0;
                $total_por_vencer = 0;


                foreach ($resultados as $proveedorId => $data) {

                    $sheet->cell('A' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue('+');
                        $cell->setAlignment('center');
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue($data['codigo']);
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue($data['cuenta']);
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue(round($data['valor_vencido_total'], 2));
                        $cell->setAlignment('right');
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue(round($data['valor_periodo_total'], 2));
                        $cell->setAlignment('right');
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $comienzo, function ($cell) use ($data) {
                        $cell->setValue(round($data['valor_por_vencer_total'], 2));
                        $cell->setAlignment('right');
                        $cell->setBackground('#CCE1A7');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $total_vencido += $data['valor_vencido_total'];
                    $total_periodo += $data['valor_periodo_total'];
                    $total_por_vencer += $data['valor_por_vencer_total'];

                    $comienzo++;

                    for ($i = 0; $i < count($titulos2); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos2, $i) {
                            $cell->setValue($titulos2[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $comienzo++;

                    foreach ($data['detalles'] as $index => $detalle) {
                        $sheet->cell('A' . $comienzo, function ($cell) use ($index) {
                            $cell->setValue($index + 1);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $comienzo, function ($cell) use ($detalle) {
                            $cell->setValue($detalle['fecha']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $comienzo, function ($cell) use ($detalle) {
                            $cell->setValue($detalle['vencimiento']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $comienzo, function ($cell) use ($detalle) {
                            $cell->setValue($detalle['observacion']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $comienzo, function ($cell) use ($detalle) {
                            $cell->setValue(round($detalle['saldo'], 2));
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $comienzo++;
                    }
                }

                $sheet->cell('C' . $comienzo, function ($cell) {
                    $cell->setValue('Totales:');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $comienzo, function ($cell) use ($total_vencido) {
                    $cell->setValue(round($total_vencido, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $comienzo, function ($cell) use ($total_periodo) {
                    $cell->setValue(round($total_periodo, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $comienzo, function ($cell) use ($total_por_vencer) {
                    $cell->setValue(round($total_por_vencer, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public static function excelInformeSaldo($r)
    {
        //dd($r->all());
        DB::enableQueryLog();
        $id_empresa = session('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $query = DB::table('proveedor as p')
            ->join('ct_compras as c', 'p.id', '=', 'c.proveedor')
            ->select(
                'p.id as proveedor_id',
                'p.razonsocial',
                DB::raw('ROUND(SUM(IFNULL(c.total_final, 0) - IFNULL((
                SELECT 
                    SUM(IFNULL(valor_fuente, 0) + IFNULL(valor_iva, 0)) 
                FROM 
                    ct_retenciones 
                WHERE 
                    id_compra = c.id AND estado != 0
            ), 0)), 2) AS deuda'),
                DB::raw('IFNULL(SUM((
                SELECT SUM(abono)
                FROM ct_detalle_comprobante_egreso ddce
                JOIN ct_comprobante_egreso cce ON ddce.id_comprobante = cce.id
                WHERE cce.id_proveedor = p.id
                AND ddce.id_compra = c.id
            )), 0) AS anticipos')
            )
            ->where('c.estado', '!=', 0)
            ->where(function ($q) use ($r) {
                if ($r->proveedor != null) {
                    $q->where('p.id', $r->proveedor);
                }
            })
            ->where('c.id_empresa', $id_empresa)
            ->wherebetween('c.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
        $result = $query->groupBy('p.id', 'p.razonsocial')
            ->get();

        //dd($result);


        $titulos = array("Código", "Cuenta", "Deudas", "Anticipos", "Saldo");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");

        return Excel::create('Informe Resultados', function ($excel) use ($posicion, $titulos, $empresa, $result, $r) {
            $excel->sheet('Informe Resultados', function ($sheet) use ($posicion, $titulos, $result, $empresa, $r) {

                $sheet->mergeCells('A1:E1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A2:E2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A3:E3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue('INFORME SALDO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#1870C8');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A4:E4');
                $sheet->cell('A4', function ($cell) use ($r) {
                    $cell->setValue('AL' . ' ' . $r->h);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });

                $comienzo = 4;

                $comienzo++;
                // Escribir los títulos de las columnas
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;


                $totalDeuda = 0;
                $totalAnticipos = 0;
                foreach ($result as $detalle) {

                    $anticipos = round($detalle->anticipos, 2);
                    $deuda = round($detalle->deuda, 2);
                    $saldo = round($detalle->deuda - $detalle->anticipos, 2);

                    $totalDeuda += $detalle->deuda;
                    $totalAnticipos += $detalle->anticipos;

                    $datos_excel = [];
                    array_push($datos_excel, $detalle->proveedor_id, $detalle->razonsocial, $deuda, $anticipos, $saldo);

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 2 || $i == 3 || $i == 4) {
                                $cell->setAlignment('right');
                            } elseif ($i == 1) {
                                $cell->setAlignment('left');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }

                $sheet->cell('B' . $comienzo, function ($cell) {
                    $cell->setValue('Totales');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize(18);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C' . $comienzo, function ($cell) use ($totalDeuda) {
                    $cell->setValue(round($totalDeuda, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(15);
                    $cell->setAlignment('right');
                });
                $sheet->cell('D' . $comienzo, function ($cell) use ($totalAnticipos) {
                    $cell->setValue(round($totalAnticipos, 2));
                    $cell->setFontSize(15);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                });
                $sheet->cell('E' . $comienzo, function ($cell) use ($totalDeuda, $totalAnticipos) {
                    $cell->setValue(round($totalDeuda - $totalAnticipos, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize(15);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                });
            });
        })->export('xlsx');
    }
    public static function exceDeudasP($r)
    {
        return self::excelDeudas($r);
    }
    public static function excelDeudas($r)
    {
        DB::enableQueryLog();
        $id_empresa = session('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $results = DB::table('ct_compras')
            ->select(
                DB::raw('0 AS orden'),
                DB::raw("'Factura' AS tipo"),
                'ct_compras.id',
                'ct_compras.serie',
                'ct_compras.secuencia_f',
                'ct_compras.fecha_termino',
                'ct_compras.f_caducidad',
                'ct_compras.fecha AS fecha_compra',
                DB::raw('NULL AS fecha_documento'),
                'proveedor.id AS identificacion',
                'proveedor.razonsocial',
                'ct_compras.subtotal_0',
                'ct_compras.subtotal_12',
                'ct_compras.subtotal',
                'ct_compras.iva_total',
                'ct_compras.total_final',
                'ct_compras.valor_contable',
                'ct_compras.id_empresa',
                'ct_compras.tipo',
                'ct_compras.numero',
                'ct_compras.tipo_comprobante',
                'ct_compras.observacion',
                DB::raw("CONCAT(proveedor.razonsocial, ' Fact: # ', ct_compras.numero, ' Ref: ', ct_compras.id, ' -- Asiento ', ct_compras.id_asiento_cabecera) AS detalle"),
                'ct_compras.id_asiento_cabecera AS id_asiento',
                DB::raw("CONCAT(ct_asientos_detalle.id_plan_cuenta, ' | ', ct_asientos_detalle.descripcion) AS cuenta"),
                DB::raw("CONCAT('COM-FA ', ct_compras.secuencia_f) AS comprobante"),
                DB::raw("(SELECT SUM(COALESCE(valor, 0)) FROM ct_comprobante_egreso c JOIN ct_detalle_comprobante_egreso d ON c.id = d.id_comprobante WHERE c.id_empresa = ct_compras.id_empresa AND c.estado != 0 AND d.id_compra = ct_compras.id) AS egreso"),
                DB::raw("(SELECT SUM(COALESCE(valor_fuente, 0) + COALESCE(valor_iva, 0)) FROM ct_retenciones WHERE id_empresa = ct_compras.id_empresa AND estado != 0 AND id_compra = ct_compras.id) AS retencion"),
                DB::raw("(SELECT SUM(COALESCE(total_disponible, 0)) FROM ct_cruce_valores JOIN ct_detalle_cruce ON ct_cruce_valores.id = ct_detalle_cruce.id_comprobante WHERE ct_cruce_valores.id_empresa = ct_compras.id_empresa AND ct_cruce_valores.estado != 0 AND ct_detalle_cruce.id_factura = ct_compras.id) AS cruce_valores"),
                DB::raw("(SELECT SUM(COALESCE(valor_contable, 0)) FROM ct_credito_acreedores JOIN ct_detalle_credito_acreedores ON ct_credito_acreedores.id = ct_detalle_credito_acreedores.id_debito WHERE ct_credito_acreedores.estado != 0  AND ct_credito_acreedores.id_compra = ct_compras.id ) AS credito_acreedores"),
                DB::raw("(SELECT SUM(COALESCE(valor, 0)) FROM ct_debito_bancario JOIN ct_debito_bancario_detalle ON ct_debito_bancario.id = ct_debito_bancario_detalle.id_debito WHERE ct_debito_bancario.estado != 0 AND ct_debito_bancario_detalle.id_compra = ct_compras.id) AS debito_bancario"),
                DB::raw("(SELECT SUM(COALESCE(valor_pago, 0)) FROM ct_comprobante_egreso_masivo JOIN ct_detalle_comprobante_egreso_masivo ON ct_comprobante_egreso_masivo.id = ct_detalle_comprobante_egreso_masivo.id_comprobante WHERE ct_comprobante_egreso_masivo.estado != 0 AND ct_detalle_comprobante_egreso_masivo.id_compra = ct_compras.id) AS comprobante_egreso_masivo"),
                DB::raw("(SELECT SUM(COALESCE(valor_contable, 0)) FROM ct_debito_acreedores JOIN ct_detalle_debito_acreedores ON ct_debito_acreedores.id = ct_detalle_debito_acreedores.id_debito_acreedores WHERE ct_debito_acreedores.estado <> 0 AND ct_detalle_debito_acreedores.id_factura = ct_compras.id) AS debito_acreedores"),
                DB::raw("(select CONCAT(pe.plan,' | ',IFNULL(p.nombre,pe.nombre)) from ct_asientos_detalle d join plan_cuentas p on d.id_plan_cuenta=p.id join plan_cuentas_empresa pe on p.id=pe.id_plan and pe.id_empresa='$id_empresa' where d.id_asiento_cabecera=ct_compras.id_asiento_cabecera and (pe.plan like '5%' OR (pe.plan like '1.01.%' AND pe.descripcion like '%INVEN%')) limit 1) AS cuenta_contable")
            )
            ->join('proveedor', 'proveedor.id', '=', 'ct_compras.proveedor')
            ->leftJoin('ct_asientos_detalle', function ($join) {
                $join->on('ct_compras.id_asiento_cabecera', '=', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->where('ct_asientos_detalle.descripcion', 'LIKE', '%PROVEE%');
            })
            ->leftJoin('plan_cuentas', 'ct_asientos_detalle.id_plan_cuenta', '=', 'plan_cuentas.id')
            ->leftJoin('plan_cuentas_empresa', function ($join) {
                $join->on('plan_cuentas.id', '=', 'plan_cuentas_empresa.plan')
                    ->on('plan_cuentas_empresa.id_empresa', '=', 'ct_compras.id_empresa');
            })
            ->where('ct_compras.numero', '!=', 'IMPORTACION')
            ->where('ct_compras.estado', '<>', 0)
            ->where('ct_compras.id_empresa', $id_empresa)
            ->where('ct_compras.valor_contable', '>', '0')
            ->where(function ($q) use ($r) {
                if ($r->id_proveedor !== null) {
                    $q->where("ct_compras.proveedor", $r->id_proveedor);
                }
            })
            ->whereBetween('ct_compras.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->get();
        $titulos = array("Proveedor", "Fecha", "Días", "Vencimiento", "Días Vencidos", "Tipo", "Número", "Cuenta", "Detalle", "Tipo", "Valor", "EG", "RT", "DB/TRANS", "CRUCE", "N/C", "Saldo");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");
        return Excel::create('Balance Financiero', function ($excel) use ($posicion, $titulos, $empresa, $r, $results) {
            $excel->sheet('Balance Financiero', function ($sheet) use ($posicion, $titulos, $empresa, $r, $results) {
                $sheet->mergeCells('A1:O1');
                $sheet->cell('A1', function ($cell) use ($r, $empresa) {
                    $cell->setValue(' Deudas Pendientes' . ' ' . $empresa->razonsocial . ' ' . $r->d . ' - ' . $r->h);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });
                $totalValor = 0;
                $comienzo = 3;
                // Escribir los títulos de las columnas
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                foreach ($results as $data) {
                    $days = "0 días";
                    $daysf = 0;
                    if (!is_null($data->fecha_termino)) {
                        $fec = new DateTime($data->f_caducidad);
                        $fec2 = new DateTime($data->fecha_termino);
                        $diff = $fec->diff($fec2);
                        $days = $diff->days . ' Días ';
                        $fech = new DateTime($data->fecha_termino);
                        $fech2 = new DateTime($r->h);
                        $diff2 = $fech2->diff($fech);
                        $daysf = $diff2->format("%r%a");
                    }
                    $tipo = ($data->tipo == 1) ? "COM-FA" : "COM-FACT";
                    $detalle = "Fact: " . $data->secuencia_f . " Ref: " . $data->numero . " " . $data->observacion . " Id. Asiento: " . $data->id_asiento . " ID: " . $data->id;
                    $masterTipo = MasterTipos::where("codigo", $data->tipo_comprobante)->first();
                    $masterTipoNombre = isset($masterTipo) ? $masterTipo->nombre : '';
                    $total_final = $data->total_final; // Este es el valor sin formatear
                    $total_final_formatted = number_format($total_final, 2, '.', '');
                    $egreso = number_format($data->egreso ?? 0, 2,'.', '');
                    $retencion = number_format($data->retencion ?? 0, 2,'.', '');
                    $debito_acreedores = number_format($data->debito_acreedores ?? 0, 2,'.', '');
                    $cruce_valores = number_format($data->cruce_valores ?? 0, 2,'.', '');
                    $credito_acreedores = number_format($data->credito_acreedores ?? 0, 2,'.', '');
                    $valor_contable = number_format($data->valor_contable ?? 0, 2,'.', '');
                    // Acumular el total del valor
                    $totalValor += $total_final;
                    // Escribir el nombre del proveedor en cada fila
                    $datos_excel = [
                        $data->razonsocial, // Mostrar el nombre en la columna A
                        $data->fecha_compra,
                        $days,
                        date("d-m-Y", strtotime($data->f_caducidad)),
                        $daysf,
                        $tipo,
                        $data->numero,
                        $data->cuenta_contable,
                        $detalle,
                        $masterTipoNombre,
                        $total_final_formatted,
                        $egreso,
                        $retencion,
                        $debito_acreedores,
                        $cruce_valores,
                        $credito_acreedores,
                        $valor_contable
                    ];
                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 10 || $i == 11 || $i == 12 || $i == 13 || $i == 14 || $i == 15 || $i == 16) {
                                $cell->setAlignment('right');
                            } elseif ($i == 0 || $i == 7 || $i == 8) {
                                $cell->setAlignment('left');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }
                $sheet->setWidth('A', 40);
                $sheet->setWidth('B', 15);
                $sheet->setWidth('C', 12);
                $sheet->setWidth('D', 21);
                $sheet->setWidth('E', 14);
                $sheet->setWidth('F', 12);
                $sheet->setWidth('G', 25);
                $sheet->setWidth('H', 50);
                $sheet->setWidth('I', 120);
                $sheet->setWidth('j', 23);
                $sheet->setWidth('k', 23);
                $sheet->setWidth('L', 12);
                $sheet->setWidth('M', 12);
            });
        })->export('xlsx');
    }
    // public static function excelBalanceFinanciero($r, $armar = '', $tipo = '')
    // {



    //     $hasta = str_replace('/', '-', $r->h);
    //     $desde = str_replace('/', '-', $r->d);
    //     $desde = date('Y-m-d', strtotime($desde));
    //     $hasta = date('Y-m-d', strtotime($hasta));
    //     $id_empresa = session('id_empresa');
    //     $empresa = Empresa::find($id_empresa);
    //     $htmlActivos = '';
    //     $htmlPasivos = '';
    //     $htmlPatrimonio = '';
    //     $planCuenta = '';
    //     $totalActivos = 0;
    //     $totalActivos_ = 0;
    //     $totalPasivos = 0;
    //     $totalPasivo_ = 0;
    //     $totalPatrimonio = 0;
    //     $totalPatrimonio_ = 0;
    //     $participacionTrabajadores = 0;
    //     $utilidadPerdida = 0;
    //     $totalPorcentajeActivo = 0;
    //     $totalPorcentajePasivo = 0;
    //     $totalPorcentajePatrimonio = 0;
    //     $datos = Contabilidad::getBalanceComprobacion($r, 'return');
    //     $totalActivos_ = $datos->where('tipo', 'ACTIVOS')->sum('saldo');
    //     $totalPasivo_ = $datos->where('tipo', 'PASIVOS')->sum('saldo');
    //     $totalPatrimonio_ = $datos->where('tipo', 'PATRIMONIO')->sum('saldo');
    //     $totalIngresos = $datos->where('tipo', 'INGRESOS')->sum('saldo');
    //     $totalCostos = $datos->where('tipo', 'COSTOS')->sum('saldo');
    //     $totalGastos = $datos->where('tipo', 'GASTOS')->sum('saldo');
    //     $total_diferencia = $totalPasivo_ + $totalPatrimonio_ + $totalActivos_;
    //     $datos_ = $datos;
    //     $index = [];
    //     foreach ($datos as $item) {
    //         $index[$item->id] = $item;
    //     }
    //     $datos = $index;
    //     $resultado = Contabilidad::getBalanceResultados($r, 'return');
    //     if ($armar != '') {
    //         if ($tipo == 'financiero')
    //             return $datos;
    //         elseif ($tipo == 'resultados')
    //             return $resultado;
    //     }
    //     $totalIngresos = $resultado->where('tipo', 'INGRESOS')->sum('saldo');
    //     $totalCostos = $resultado->where('tipo', 'COSTOS')->sum('saldo');
    //     $totalGastos = $resultado->where('tipo', 'GASTOS')->sum('saldo');
    //     $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
    //     if ($utilidadBruta > 0) {
    //         $participacionTrabajadores = $utilidadBruta * 0.15;
    //         $utilidadContable = $utilidadBruta - $participacionTrabajadores;
    //     }
    //     $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($r->d)));
    //     if ($rentaEmpresa != '') {
    //         if ($rentaEmpresa->regimen_especial == 1) {
    //             $htmlUtilidadGravable = '<td>Utilidad gravable</td><td style="text-align: right;">$ 0.00</td>';
    //         }
    //     } else {
    //         //Devolver que no hay renta configurada
    //     }
    //     $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $r->d, $r->h, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
    //     $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
    //     if (count($datos) > 0) {
    //         $htmlActivos .= '<h2>Activos</h2>
    //         <table style="border-collapse:collapse; width: 100%; align="center"">
    //             <tr class="td-border">
    //                 <th class="td-border">Cuenta</th>
    //                 <th class="td-border">Nombre</th>
    //                 <th class="td-border">Saldo</th>
    //                 <th class="td-border">%</th>
    //             </tr>';
    //         $htmlPasivos .= '<h2>Pasivos</h2>
    //             <table  style="border-collapse:collapse; width: 100%; align="center"">
    //                 <tr class="td-border">
    //                     <th class="td-border">Cuenta</th>
    //                     <th class="td-border">Nombre</th>
    //                     <th class="td-border">Saldo</th>
    //                     <th class="td-border">%</th>
    //                 </tr>';
    //         $htmlPatrimonio .= '<h2>Patrimonio</h2>
    //             <table  style="border-collapse:collapse; width: 100%;">
    //                 <tr class="td-border">
    //                     <th class="td-border">Cuenta</th>
    //                     <th class="td-border">Nombre</th>
    //                     <th class="td-border">Saldo</th>
    //                     <th class="td-border">%</th>
    //                 </tr>';
    //         foreach ($datos as $key => $row) {
    //             $porcentaje = 0;
    //             if ($row->tipo == 'ACTIVOS') {
    //                 $saldo = self::traerTodo1($datos_->where("tipo", "ACTIVOS"), $row->id);
    //                 if ($saldo != 0) {
    //                     $porcentaje = ($saldo / $totalActivos_) * 100;
    //                 }
    //                 $totalPorcentajeActivo += $porcentaje;
    //                 if ($saldo < 0) {
    //                     $saldo_ = '(' . number_format(($saldo * -1), 2, ',', '.') . ')';
    //                 } else {
    //                     $saldo_ =  number_format($saldo, 2, ',', '.');
    //                 }
    //                 $totalActivos += $saldo;
    //                 $cantidadP = "";
    //                 for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
    //                     $cantidadP .= "&nbsp;";
    //                 }
    //                 if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
    //                     $htmlActivos .= '<tr>
    //                 <td class="td-border">' . $cantidadP . $row->plan . '</td>
    //                 <td class="td-border">' . $row->nombre . '</td>
    //                 <td class="td-border" style="text-align: right;">$' . $saldo_ . '</td>
    //                 <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
    //                 </tr>';
    //                 }
    //                 $porcentaje = 0;

    //                 //dd($datos);
    //             }
    //             if ($row->tipo == 'PASIVOS') {
    //                 $saldo = self::traerTodo1($datos_->where("tipo", "PASIVOS"), $row->id);
    //                 if ($saldo != 0) {
    //                     $porcentaje = ($saldo / $totalPasivo_) * 100;
    //                 }
    //                 $totalPorcentajePasivo += $porcentaje;
    //                 if ($impuestoGenerado > 0) {
    //                     $arrayBusqueda = [
    //                         'IMPUESTO SRI',
    //                         'PAGAR'
    //                     ];
    //                     $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
    //                 }
    //                 if ($planCuenta != '') {

    //                     if ($planCuenta->plan == $row->plan) {
    //                         $saldo = $impuestoGenerado * -1;
    //                     }
    //                 }
    //                 if ($saldo < 0) {
    //                     $saldo_ = number_format(($saldo * -1), 2, ',', '.');
    //                 } else {
    //                     $saldo_ =  number_format($saldo, 2, ',', '.');
    //                 }
    //                 $totalPasivos += $saldo;
    //                 $cantidadP = "";

    //                 for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
    //                     $cantidadP .= "&nbsp;";
    //                 }
    //                 if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
    //                     $htmlPasivos .= '<tr>
    //                 <td class="td-border">' . $cantidadP . $row->plan . '</td>
    //                 <td class="td-border">' . $row->nombre . '</td>
    //                 <td class="td-border" style="text-align: right;">$' . $saldo_ . '</td>
    //                 <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
    //                 </tr>';
    //                 }
    //                 $porcentaje = 0;
    //             }
    //             if ($row->tipo == 'PATRIMONIO') {
    //                 $saldo = self::traerTodo1($datos_->where("tipo", "PATRIMONIO"), $row->id);
    //                 if ($saldo != 0) {
    //                     $porcentaje = ($saldo / $totalPatrimonio_) * 100;
    //                 }
    //                 $totalPorcentajePatrimonio += $porcentaje;
    //                 $arrayBusqueda = [
    //                     'PERDIDA',
    //                     'NETA',
    //                     'PERIODO'
    //                 ];
    //                 $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda, 'PERDIDA');
    //                 if ($planCuenta != '') {
    //                     if ($planCuenta->plan == $row->plan) {
    //                         $saldo = $utilidadPerdida * -1;
    //                     }
    //                 }
    //                 $cadena = strtoupper($row->nombre);
    //                 $buscar = "PERDIDA";
    //                 $resultado = strpos($cadena, $buscar);
    //                 if ($saldo < 0) {
    //                     $saldo_ = number_format(($saldo * -1), 2, ',', '.');
    //                 } else {
    //                     $saldo_ = '(' . number_format($saldo, 2, ',', '.') . ')';
    //                 }
    //                 $totalPatrimonio += $saldo;
    //                 $cantidadP = "";

    //                 for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
    //                     $cantidadP .= "&nbsp;";
    //                 }
    //                 if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
    //                     $htmlPatrimonio .= '<tr>
    //                 <td class="td-border">' . $cantidadP . $row->plan . '</td>
    //                 <td class="td-border">' . $row->nombre . '</td>
    //                 <td class="td-border" style="text-align: right;">$' . $saldo . '</td>
    //                 <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
    //                 </tr>';
    //                 }
    //                 $porcentaje = 0;
    //             }
    //         }
    //         $htmlActivos .= '<tr>
    //                 <td style="font-size: 10;">Total Activos</td>
    //                 <td >&nbsp;</td>
    //                 <td style="text-align: right;" id="totalActivos">$ ' . number_format($totalActivos_, 2, ',', '.') . '</td>
    //                 <td style="text-align: right;"></td>
    //             </tr>
    //         </table>';

    //         dd($htmlActivos);
    //         $htmlPasivos .= '<tr class="tr">
    //                 <td style="font-size: 10;">Total Pasivos</td>
    //                 <td>&nbsp;</td>
    //                 <td style="text-align: right;">$ ' . number_format($totalPasivo_ * -1, 2, ',', '.') . '</td>
    //                 <td style="text-align: right;"></td>
    //             </tr>
    //         </table>';
    //         $htmlPatrimonio .= '<tr class="tr">
    //                 <td style="font-size: 10;">Total Patrimonio</td>
    //                 <td>&nbsp;</td>
    //                 <td style="text-align: right;">$ ' . number_format($totalPatrimonio_ * -1, 2, ',', '.') . '</td>
    //                 <td style="text-align: right;"></td>
    //             </tr>
    //         </table>';
    //     }

    //     $totalPasivos = ($totalPasivos * -1);
    //     $totalPatrimonio = ($totalPatrimonio * -1);
    //     $diferencia = $totalActivos_ - (($totalPasivo_ * -1) + ($totalPatrimonio_ * -1));



    //     $posicion = array("A", "B", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


    //     Excel::create('Balance Financiero', function ($excel) use ($posicion, $datos, $empresa, $desde, $hasta, $totalActivos_, $totalPasivo_, $totalPatrimonio_, $total_diferencia, $htmlActivos) {
    //         $excel->sheet('Balance Financiero', function ($sheet) use ($posicion, $datos, $empresa, $desde, $hasta, $totalActivos_, $totalPasivo_, $totalPatrimonio_, $total_diferencia, $htmlActivos) {
    //             $sheet->setWidth('A', 40);
    //             $sheet->setWidth('B', 30);
    //             $sheet->setWidth('C', 30);
    //             $sheet->setWidth('D', 27);
    //             $objDrawing = new PHPExcel_Worksheet_Drawing;
    //             $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo); //your image path
    //             $objDrawing->setCoordinates('A2');
    //             $objDrawing->setWidth(200);
    //             $objDrawing->setWorksheet($sheet);
    //             $objDrawing->setOffsetX(5);

    //             $sheet->mergeCells('A1:B1');

    //             $sheet->mergeCells('A3:D3');
    //             $sheet->cell('A3', function ($cell) use ($empresa) {
    //                 $cell->setValue($empresa->razonsocial);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setBackground('#92CFEF');
    //                 $cell->setFontSize(20);
    //                 $cell->setAlignment('center');
    //             });


    //             $sheet->mergeCells('A4:D4');
    //             $sheet->cell('A4', function ($cell) {
    //                 $cell->setValue('Balance Financiero');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setBackground('#92CFEF');
    //                 $cell->setFontSize(20);
    //                 $cell->setAlignment('center');
    //             });




    //             $sheet->mergeCells('A5:D5');
    //             $sheet->cell('A5', function ($cell) use ($desde, $hasta) {
    //                 $cell->setBackground('#92CFEF');
    //                 $cell->setValue('DESDE:' . ' ' . $desde . ' ' . 'HASTA:' . ' ' . $hasta);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setFontSize(20);
    //                 $cell->setAlignment('center');
    //             });

    //             $sheet->mergeCells('A6:D6');
    //             $sheet->cell('A6', function ($cell) {
    //                 $cell->setFontSize(18);
    //                 $cell->setValue('Resumen');
    //                 $cell->setBackground('#92CFEF');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //             });


    //             $sheet->cell('B8', function ($cell) {
    //                 $cell->setValue('Descripción');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('C8', function ($cell) {
    //                 $cell->setValue('Valor');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('B9', function ($cell) {
    //                 $cell->setValue('Activos	');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('B10', function ($cell) {
    //                 $cell->setValue('Pasivos');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('B11', function ($cell) {
    //                 $cell->setValue('Patrimonio');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('B12', function ($cell) {
    //                 $cell->setValue('Diferencia');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('C12', function ($cell) use ($total_diferencia) {
    //                 $cell->setValue($total_diferencia);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });


    //             $sheet->cell('C11', function ($cell) use ($totalPatrimonio_) {
    //                 $cell->setValue($totalPatrimonio_);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->cell('C10', function ($cell) use ($totalPasivo_) {
    //                 $cell->setValue($totalPasivo_);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });


    //             $sheet->cell('C9', function ($cell) use ($totalActivos_) {
    //                 $cell->setValue($totalActivos_);
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });


    //             $sheet->mergeCells('A15:D15');
    //             $sheet->cell('A15', function ($cell) {
    //                 // manipulate the cel
    //                 $cell->setValue('ACTIVOS');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $sheet->mergeCells('E15:F15');
    //             $sheet->cell('E15', function ($cell) {
    //                 // manipulate the cel
    //                 $cell->setValue('Pasivos');
    //                 $cell->setFontWeight('bold');
    //                 $cell->setAlignment('center');
    //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //             });

    //             $comienzo = 16;

    //             $activos = [];
    //             foreach ($datos as $data) {
    //                 //dd($data->saldo);
    //                 if ($data->tipo == "ACTIVOS") {
    //                     $activos[] = $data;
    //                     $datos_excel = [];
    //                     array_push($datos_excel, $data->plan, $data->nombre);
    //                     for ($i = 0; $i < count($datos_excel); $i++) {
    //                         $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
    //                             $cell->setValue($datos_excel[$i]);
    //                             $cell->setBorder('thin', 'thin', 'thin', 'thin');
    //                             $cell->setAlignment('center');
    //                         });
    //                     }
    //                     $comienzo++;
    //                 }
    //             }

    //         });
    //     })->export('xlsx');
    // }
    public static function convertidorFecha($fecha)
    {
        $fechaParsear = str_replace('/', '-', $fecha);
        $timestamp = \Carbon\Carbon::parse($fechaParsear)->timestamp;
        $fecha_desde = date('Y-m-d', $timestamp);
        return $fecha_desde;
    }
    public static function pdfBalanceFinanciero($r, $armar = '', $tipo = '')
    {
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', trim(isset($r->d) ? $r->d : ''));
        if ($desde == '') {
            $desde = Contabilidad::getFechaInicioSistema();
        } else {
            $desde = date('Y-m-d', strtotime($desde));
        }
        $hasta = date('Y-m-d', strtotime($hasta));
        $id_empresa = session('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $htmlActivos = '';
        $htmlPasivos = '';
        $htmlPatrimonio = '';
        $planCuenta = '';
        $totalActivos = 0;
        $totalActivos_ = 0;
        $totalPasivos = 0;
        $totalPasivo_ = 0;
        $totalPatrimonio = 0;
        $totalPatrimonio_ = 0;
        $participacionTrabajadores = 0;
        $utilidadPerdida = 0;
        $totalPorcentajeActivo = 0;
        $totalPorcentajePasivo = 0;
        $totalPorcentajePatrimonio = 0;
        $datos = Contabilidad::getBalanceComprobacion($r, 'return');
        $totalActivos_ = $datos->where('tipo', 'ACTIVOS')->sum('saldo');
        $totalPasivo_ = $datos->where('tipo', 'PASIVOS')->sum('saldo');
        $totalPatrimonio_ = $datos->where('tipo', 'PATRIMONIO')->sum('saldo');
        $totalIngresos = $datos->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $datos->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $datos->where('tipo', 'GASTOS')->sum('saldo');
        $total_diferencia = $totalPasivo_ + $totalPatrimonio_ + $totalActivos_;
        $datos_ = $datos;
        $index = [];
        foreach ($datos as $item) {
            $index[$item->id] = $item;
        }
        $datos = $index;
        $resultado = Contabilidad::getBalanceResultados($r, 'return');
        if ($armar != '') {
            if ($tipo == 'financiero')
                return $datos;
            elseif ($tipo == 'resultados')
                return $resultado;
        }
        $totalIngresos = $resultado->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $resultado->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $resultado->where('tipo', 'GASTOS')->sum('saldo');
        $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
        if ($utilidadBruta > 0) {
            $participacionTrabajadores = $utilidadBruta * 0.15;
            $utilidadContable = $utilidadBruta - $participacionTrabajadores;
        }
        $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime(trim(isset($r->d) ? $r->d : '') == '' ? $r->h : $r->d)));
        if ($rentaEmpresa != '') {
            if ($rentaEmpresa->regimen_especial == 1) {
                $htmlUtilidadGravable = '<td>Utilidad gravable</td><td style="text-align: right;">$ 0.00</td>';
            }
        } else {
            //Devolver que no hay renta configurada
        }
        $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $r->d, $r->h, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
        $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
        if (count($datos) > 0) {
            $htmlActivos .= '<h2>Activos</h2>
            <table style="border-collapse:collapse; width: 100%; align="center"">
                <tr class="td-border">
                    <th class="td-border">Cuenta</th>
                    <th class="td-border">Nombre</th>
                    <th class="td-border">Saldo</th>
                    <th class="td-border">%</th>
                </tr>';
            $htmlPasivos .= '<h2>Pasivos</h2>
                <table  style="border-collapse:collapse; width: 100%; align="center"">
                    <tr class="td-border">
                        <th class="td-border">Cuenta</th>
                        <th class="td-border">Nombre</th>
                        <th class="td-border">Saldo</th>
                        <th class="td-border">%</th>
                    </tr>';
            $htmlPatrimonio .= '<h2>Patrimonio</h2>
                <table  style="border-collapse:collapse; width: 100%;">
                    <tr class="td-border">
                        <th class="td-border">Cuenta</th>
                        <th class="td-border">Nombre</th>
                        <th class="td-border">Saldo</th>
                        <th class="td-border">%</th>
                    </tr>';
            foreach ($datos as $key => $row) {
                $porcentaje = 0;
                if ($row->tipo == 'ACTIVOS') {
                    $saldo = self::traerTodo1($datos_->where("tipo", "ACTIVOS"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalActivos_) * 100;
                    }
                    $totalPorcentajeActivo += $porcentaje;
                    if ($saldo < 0) {
                        $saldo_ = '(' . number_format(($saldo * -1), 2, ',', '.') . ')';
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalActivos += $saldo;
                    $cantidadP = "";
                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlActivos .= '<tr>
                    <td class="td-border">' . $cantidadP . $row->plan . '</td>
                    <td class="td-border">' . $row->nombre . '</td>
                    <td class="td-border" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;

                    //dd($datos);
                }
                if ($row->tipo == 'PASIVOS') {
                    $saldo = self::traerTodo1($datos_->where("tipo", "PASIVOS"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPasivo_) * 100;
                    }
                    $totalPorcentajePasivo += $porcentaje;
                    if ($impuestoGenerado > 0) {
                        $arrayBusqueda = [
                            'IMPUESTO SRI',
                            'PAGAR'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
                    }
                    if ($planCuenta != '') {

                        if ($planCuenta->plan == $row->plan) {
                            $saldo = $impuestoGenerado * -1;
                        }
                    }
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalPasivos += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPasivos .= '<tr>
                    <td class="td-border">' . $cantidadP . $row->plan . '</td>
                    <td class="td-border">' . $row->nombre . '</td>
                    <td class="td-border" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
                if ($row->tipo == 'PATRIMONIO') {
                    $saldo = self::traerTodo1($datos_->where("tipo", "PATRIMONIO"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPatrimonio_) * 100;
                    }
                    $totalPorcentajePatrimonio += $porcentaje;
                    $arrayBusqueda = [
                        'PERDIDA',
                        'NETA',
                        'PERIODO'
                    ];
                    $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda, 'PERDIDA');
                    if ($planCuenta != '') {
                        if ($planCuenta->plan == $row->plan) {
                            $saldo = $utilidadPerdida * -1;
                        }
                    }
                    $cadena = strtoupper($row->nombre);
                    $buscar = "PERDIDA";
                    $resultado = strpos($cadena, $buscar);
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ = '(' . number_format($saldo, 2, ',', '.') . ')';
                    }
                    $totalPatrimonio += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPatrimonio .= '<tr>
                    <td class="td-border">' . $cantidadP . $row->plan . '</td>
                    <td class="td-border">' . $row->nombre . '</td>
                    <td class="td-border" style="text-align: right;">$' . $saldo . '</td>
                    <td class="td-border" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
            }
            $htmlActivos .= '<tr>
                    <td style="font-size: 10;">Total Activos</td>
                    <td >&nbsp;</td>
                    <td style="text-align: right;" id="totalActivos">$ ' . number_format($totalActivos_, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';
            //dd($htmlActivos);
            $htmlPasivos .= '<tr class="tr">
                    <td style="font-size: 10;">Total Pasivos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPasivo_ * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';
            $htmlPatrimonio .= '<tr class="tr">
                    <td style="font-size: 10;">Total Patrimonio</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPatrimonio_ * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';
        }
        $totalPasivos = ($totalPasivos * -1);
        $totalPatrimonio = ($totalPatrimonio * -1);
        $diferencia = $totalActivos_ - (($totalPasivo_ * -1) + ($totalPatrimonio_ * -1));
        $view = \View::make('v3/contable/utilidades/view_pdfDescargarArchivoFinanciero', compact('datos', 'empresa', 'desde', 'hasta', 'totalActivos_', 'totalPasivo_', 'totalPatrimonio_', 'total_diferencia', 'htmlActivos', 'htmlPasivos', 'htmlPatrimonio'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('view_pdfDescargarArchivoFinanciero.pdf');
    }
    public static function traerTodo1($array, $cuentaBase)
    {
        $acumulador = 0;
        $cuentaBaseLongitud = strlen($cuentaBase);
        foreach ($array as $val) {
            if ($val->plan === $cuentaBase || substr($val->plan, 0, $cuentaBaseLongitud) === $cuentaBase) {
                $acumulador += $val->saldo;
            }
        }
        return $acumulador;
    }
    public static function getDecimal($valor = 0, $digitos = 2, $formato = false)
    {
        if (trim($valor) == "") $valor = 0;
        $resultado = round($valor, $digitos, PHP_ROUND_HALF_UP);
        $resultado = number_format($resultado, $digitos, ".", $formato ? "," : "");
        return $resultado;
    }
    public static function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }
    public static function verArchivo($ruta)
    {
        $pathtoFile = storage_path('app/v3/compras/' . $ruta);
        if (file_exists($pathtoFile)) {
            ob_end_clean();
            return Response::download($pathtoFile);
        }
        /*if (!is_null($ruta)) {
            $pathtoFile = Storage::disk('archivos_compras')->path($ruta);
    
            if (file_exists($pathtoFile)) {
                return response()->file($pathtoFile, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($pathtoFile) . '"'
                ]);
            } else {
                return response()->json(['error' => 'File not found'], 404);
            }
        } else {
            return response()->json(['error' => 'No file specified'], 400);
        }*/
        if (!is_null($ruta)) {
            $pathtoFile = storage_path('app/v3/compras/' . $ruta);
            if (file_exists($pathtoFile)) {
                // Establecer cabeceras adecuadas para el tipo de archivo
                $mimeType = mime_content_type($pathtoFile);
                $headers = [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . basename($pathtoFile) . '"'
                ];
                return response()->file($pathtoFile, $headers);
            } else {
                return response()->json(['error' => 'File not found'], 404);
            }
        } else {
            return response()->json(['error' => 'No file specified'], 400);
        }
    }
    public static function dropzone($id)
    {
        $html = '<style>
            .dropzone .dz-preview .dz-details {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .dropzone .dz-preview .dz-image img {
                display: block;
                max-width: 100%;
                max-height: 100%;
            }
        </style>
                <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>' . trans('v3/new_varios.foto_activo') . '</label>
                    <div class="input-group mb-3">
                        <div id="foto_activo" name="foto_activo" class="dropzone form-control text-right" style="border: 2px dashed #d3d3d3; padding: 20px; height: auto;">
                            <div class="dz-message" data-dz-message>
                                <span>' . trans('v3/new_varios.foto_activo') . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>' . trans('v3/new_varios.foto_acc') . '</label>
                    <div class="input-group mb-3">
                        <div id="foto_acc" name="foto_acc" class="dropzone form-control text-right" style="border: 2px dashed #d3d3d3; padding: 20px; height: auto;">
                            <div class="dz-message" data-dz-message>
                                <span>' . trans('v3/new_varios.foto_activo') . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <script>
        var myDropzone = new Dropzone("#foto_activo", {
            url: "v3/utilidades/subirActivo",
            addRemoveLinks: true,
            acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif, .dcm",
            maxFilesize: 5,
            thumbnailWidth: 100,
            thumbnailHeight: 100,
            sending: function(file, xhr, formData) {
                formData.append("_token", $(\'[name=_token]\').val());
                formData.append("id", $(\'#id\').val());
                formData.append("tipo", 1); 
            },
            success: function(file, response) {
            file.newName = response.fileName; // response.fileName debería ser el nuevo nombre del archivo
            var mockFile = {
            name: response.fileName,
            size: file.size,
            serverId: response.id // response.id debería ser el ID de la base de datos
            };
        
            // Actualizar la vista previa con el nuevo nombre del archivo
            file.previewElement.querySelector("[data-dz-name]").innerHTML = response.fileName;
            },
            removedfile: function(file) {

                var fileName = file.newName !== undefined ? file.newName : file.name;
                var serverId = file.serverId;
                
                if (confirm("¿Estás seguro de que deseas eliminar esta imagen?")) {
                    fetch(`v3/utilidades/cambiarEstadoImagen?id=${$(\'#id\').val()}&fileName=${fileName}`, {
                        method: \'GET\',
                        headers: {
                            \'X-CSRF-TOKEN\': $(\'[name=_token]\').val(),
                            \'Content-Type\': \'application/json\'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(\'Error en la respuesta del servidor\');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === \'success\') {
                            file.previewElement.remove();
                            mensajes(data.status, data.message);
                        } else {
                            mensajes(data.status, data.message);
                        }
                    })
                    .catch(error => {
                        mensajes(\'error\', \'Hubo un error al intentar eliminar la imagen.\');
                        console.error(\'Error al eliminar la imagen:\', error);
                    });
                }
            },
            init: function() {
                var self = this;
                fetch(`v3/utilidades/obtenerImagenes?id=${$(\'#id\').val()}&t=1`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(imagen => {
                            console.log(\'Image data:\', imagen);
                            if (!imagen.name || !imagen.size || !imagen.id || !imagen.url) {
                                console.error(\'Invalid image data:\', imagen);
                                return;
                            }
                            var mockFile = {
                                name: imagen.name,
                                size: imagen.size,
                                serverId: imagen.id 
                            };
                            self.options.addedfile.call(self, mockFile);
                            self.options.thumbnail.call(self, mockFile, imagen.url);
                        });
                    })
                    .catch(error => {
                        console.error(\'Error al obtener las imágenes:\', error);
                    });
            }
        });
            $("#foto_acc").dropzone({

                url: "v3/utilidades/subirActivo",
                addRemoveLinks: true,
                acceptedFiles: ".jpg, .jpeg, .png, .bmp, .jpe, .jfif, .tiff, .tif, .dcm",
                maxFilesize: 3,
                sending: function(file, xhr, formData) {

                    formData.append("_token", $(\'[name=_token]\').val());
                formData.append("id", $(\'#id\').val());
                formData.append("tipo", 2); 
                },
                success: function(file, response) {
                    file.newName = response.fileName; // response.fileName debería ser el nuevo nombre del archivo
                    var mockFile = {
                    name: response.fileName,
                    size: file.size,
                    serverId: response.id // response.id debería ser el ID de la base de datos
                    };
                
                    // Actualizar la vista previa con el nuevo nombre del archivo
                    file.previewElement.querySelector("[data-dz-name]").innerHTML = response.fileName;
                    },
                removedfile: function(file) {

                    var fileName = file.newName !== undefined ? file.newName : file.name;
                    var serverId = file.serverId;
                    
                    if (confirm("¿Estás seguro de que deseas eliminar esta imagen?")) {
                        fetch(`v3/utilidades/cambiarEstadoImagen?id=${$(\'#id\').val()}&fileName=${fileName}`, {
                            method: \'GET\',
                            headers: {
                                \'X-CSRF-TOKEN\': $(\'[name=_token]\').val(),
                                \'Content-Type\': \'application/json\'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(\'Error en la respuesta del servidor\');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data.status)
                            if (data.status === \'success\') {
                                file.previewElement.remove();
                                mensajes(data.status, data.message);
                            } else {
                                mensajes(data.status, data.message);
                            }
                        })
                        .catch(error => {
                            mensajes(\'error\', \'Hubo un error al intentar eliminar la imagen.\');
                            console.error(\'Error al eliminar la imagen:\', error);
                        });
                    }
                },
                init: function() {
                    var self = this; 
                    fetch(\'v3/utilidades/obtenerImagenes?id=\' + $(\'#id\').val() + \'&t=2\')
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(imagen => {
                                console.log(\'Image data:\', imagen);
                                if (!imagen.name || !imagen.size || !imagen.id || !imagen.url) {
                                    console.error(\'Invalid image data:\', imagen);
                                    return;
                                }
                                var mockFile = {
                                    name: imagen.name,
                                    size: imagen.size,
                                    serverId: imagen.id 
                                };
                                self.options.addedfile.call(self, mockFile);
                                self.options.thumbnail.call(self, mockFile, imagen.url);
                            });
                        })
                        .catch(error => {
                            console.error(\'Error al obtener las imágenes:\', error);
                        });
                }
            });

           </script>';
        return $html;
    }
    public static function subirActivo($r)
    {
        $id = $r->input('id');
        $tipo = $r->input('tipo');
        // Generar un nombre único para el archivo
        $nuevo_nombre = date('YmdHis') . '_' . $r->id . '.' . $r['file']->getClientOriginalExtension();
        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($r['file']));
        $image = Image::make(Storage::disk('public')->get($nuevo_nombre));
        $image->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));
        $archivo = new ActivosFijosArchivos();
        $archivo->id_af_activo = $id;
        $archivo->tipo = $tipo;
        $archivo->imagen = $nuevo_nombre;
        $archivo->estado = 1;
        $archivo->save();
        return response()->json(['status' => 'success', 'fileName' => $nuevo_nombre, 'id' => $archivo->id]);
    }
    public static function cambiarEstadoImagen($r)
    {
        $fecha = date('YmdHis');
        $idusuario = Auth::user()->id;
        $estado = 0;
        $af_archivos = ActivosFijosArchivos::where('id_af_activo', $r->id)->where('imagen', $r->fileName)->first();
        $af_archivos->estado = $estado;
        $af_archivos->id_usuariomod = $idusuario;
        $af_archivos->deleted_at = $fecha;
        $af_archivos->save();
        return response()->json(['status' => 'success', 'message' => 'Imagen eliminada correctamente.']);
        return response()->json(['status' => 'error', 'message' => 'Imagen no encontrada.'], 404);
    }
    public static function getInfoPago()
    {
        return SeleccionPlanes::getPlanEmpresa();
    }
    public static function busqueda($cuenta)
    {
        $id_empresa = session('id_empresa');
        $plan_empresa = PlanCuentas::where('plan', $cuenta)->where('id_empresa', $id_empresa)->first();
        if (!is_null($plan_empresa)) {
            $id_plan = $plan_empresa->id_plan;
        } else {
            $id_plan = $cuenta;
        }
        return $id_plan;
    }
    public static function  descargarArchivoPdf($r, $tipo)
    {
        //dd($r->all(),$tipo);
        $periodo = date('Y', strtotime($r->d));
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', $r->d);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $logCierre = 1;
        if ($r->c == 1) {
            $logCierre = LogCierreAnio::where('anio', $periodo)->first();
        }
        if ($tipo == 'resultados') {
            $array = [
                'd' => $r['d'],
                'h' => $r['h'],
                'c' => $r['c'],
            ];
            $datos = Contabilidad::getBalanceResultados((object)$array, 'hola');
            $totalIngresos = $datos->where('tipo', 'INGRESOS')->sum('saldo');
            $totalCostos = $datos->where('tipo', 'COSTOS')->sum('saldo');
            $totalGastos = $datos->where('tipo', 'GASTOS')->sum('saldo');
            $id_empresa = session('id_empresa');
            $empresa = Empresa::find($id_empresa);
            $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($desde)));
            $utilidadContable = 0;
            $utilidadPerdida = 0;
            $participacionTrabajadores = 0;
            $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
            if ($utilidadBruta > 0) {
                $participacionTrabajadores = $utilidadBruta * 0.15;
                $utilidadContable = $utilidadBruta - $participacionTrabajadores;
            }
            if ($logCierre != '') {
                $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $desde, $hasta, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
            }
            if ($utilidadBruta < 0) {
                $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
                $utilidadPerdida = '$ (' . number_format(($utilidadPerdida * -1), 2, ', ', '.') . ')';
            } else {
                $utilidadPerdida = '$ ' . number_format($utilidadPerdida, 2, ', ', '.');
            }
            $participacionTrabajadores = '$ ' . number_format($participacionTrabajadores, 2, ',', '.');
            $utilidadContable = '$ ' . number_format($utilidadContable, 2, ',', '.');
            if ($utilidadBruta < 0) {
                $utilidadBruta = '(' . number_format(($utilidadBruta * -1), 2, ',', '.') . ')';
            }
            if ($totalCostos < 0) {
                $totalCostos = $totalCostos * -1;
            }
            if ($totalGastos < 0) {
                $totalGastos = $totalGastos * -1;
            }
            $utilidadBruta = '$ ' . $utilidadBruta;
            $impuestoGenerado = '$ ' . number_format($impuestoGenerado, 2, ',', '.');
        }
        $view = \View::make('v3/contable/utilidades/view_pdfDescargarArchivo', compact('datos', 'empresa', 'desde', 'hasta', 'totalIngresos', 'totalCostos', 'totalGastos', 'impuestoGenerado', 'utilidadContable', 'participacionTrabajadores', 'utilidadBruta', 'utilidadPerdida'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('view_pdfDescargarArchivo');
    }
    public static function descargarArchivo($r, $tipo)
    {
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', $r->d);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        if ($tipo == 'resultados') {
            $array = [
                'd' => $r['d'],
                'h' => $r['h'],
                'c' => $r['c'],
            ];
            $datos = Contabilidad::getBalanceResultados((object)$array, 'hola');
            $totalIngresos = $datos->where('tipo', 'INGRESOS')->sum('saldo');
            $totalCostos = $datos->where('tipo', 'COSTOS')->sum('saldo');
            $totalGastos = $datos->where('tipo', 'GASTOS')->sum('saldo');
            //echo $totalIngresos.' | '.$totalCostos.' | '.$totalGastos; exit;
            $id_empresa = session('id_empresa');
            $empresa = Empresa::find($id_empresa);
            $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($desde)));
            $utilidadContable = 0;
            $utilidadPerdida = 0;
            $participacionTrabajadores = 0;
            $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
            //dd($utilidadBruta);
            if ($utilidadBruta > 0) {
                $participacionTrabajadores = $utilidadBruta * 0.15;
                $utilidadContable = $utilidadBruta - $participacionTrabajadores;
            }
            $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $desde, $hasta, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
            if ($utilidadBruta < 0) {
                $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
                //dd($utilidadPerdida); 
                $utilidadPerdida = '$ (' . number_format(($utilidadPerdida * -1), 2, ', ', '.') . ')';
            } else {
                $utilidadPerdida = '$ ' . number_format($utilidadPerdida, 2, ', ', '.');
            }
            $participacionTrabajadores = '$ ' . number_format($participacionTrabajadores, 2, ',', '.');
            $utilidadContable = '$ ' . number_format($utilidadContable, 2, ',', '.');
            if ($utilidadBruta < 0) {
                $utilidadBruta = '(' . number_format(($utilidadBruta * -1), 2, ',', '.') . ')';
            }
            if ($totalCostos < 0) {
                $totalCostos = $totalCostos * -1;
            }
            if ($totalGastos < 0) {
                $totalGastos = $totalGastos * -1;
            }
            $utilidadBruta = '$ ' . $utilidadBruta;
            $impuestoGenerado = '$ ' . number_format($impuestoGenerado, 2, ',', '.');
            //dd($empresa);
        }
        $posicion = array("A", "B", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Balance de Resultados', function ($excel) use ($posicion, $datos, $empresa, $totalIngresos, $totalCostos, $totalGastos, $desde, $hasta, $impuestoGenerado, $utilidadContable, $participacionTrabajadores, $utilidadBruta, $utilidadPerdida) {
            $excel->sheet('Balance de Resultados', function ($sheet) use ($posicion, $datos, $empresa, $totalIngresos, $totalCostos, $totalGastos, $desde, $hasta, $impuestoGenerado, $utilidadContable, $participacionTrabajadores, $utilidadBruta, $utilidadPerdida) {
                $sheet->setWidth('A', 40);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 30);
                $sheet->setWidth('D', 27);
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo); //your image path
                $objDrawing->setCoordinates('A2');
                $objDrawing->setWidth(200);
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetX(5);
                $sheet->mergeCells('A1:B1');
                $sheet->mergeCells('A3:D3');
                $sheet->cell('A3', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setFontSize(20);
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A4:D4');
                $sheet->cell('A4', function ($cell) {
                    $cell->setValue('Balance de resultados');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setFontSize(20);
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A5:D5');
                $sheet->cell('A5', function ($cell) use ($desde, $hasta) {
                    $cell->setBackground('#92CFEF');
                    $cell->setValue('DESDE:' . ' ' . $desde . ' ' . 'HASTA:' . ' ' . $hasta);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(20);
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A6:D6');
                $sheet->cell('A6', function ($cell) {
                    $cell->setFontSize(18);
                    $cell->setValue('Resumen');
                    $cell->setBackground('#92CFEF');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B8', function ($cell) {
                    $cell->setValue('Descripción');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C8', function ($cell) {
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B9', function ($cell) {
                    $cell->setValue('Utilidad/Perdida del ejercicio	');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B10', function ($cell) {
                    $cell->setValue('Participación a trabajadores	');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B11', function ($cell) {
                    $cell->setValue('Utilidad contable');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B12', function ($cell) {
                    $cell->setValue('Impuesto generado');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B13', function ($cell) {
                    $cell->setValue('Utilidad/Perdida neta	');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C12', function ($cell) use ($impuestoGenerado) {
                    $cell->setValue($impuestoGenerado);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C11', function ($cell) use ($utilidadContable) {
                    $cell->setValue($utilidadContable);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C10', function ($cell) use ($participacionTrabajadores) {
                    $cell->setValue($participacionTrabajadores);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C9', function ($cell) use ($utilidadBruta) {
                    $cell->setValue($utilidadBruta);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C13', function ($cell) use ($utilidadPerdida) {
                    $cell->setValue($utilidadPerdida);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B16:C16');
                $sheet->cell('B16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Ingresos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('D9:D9');
                $sheet->cell('A17', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cuenta');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B17:C17');
                $sheet->cell('B17', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G9:G9');
                $sheet->cell('D17', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 18;
                $ingreso = [];
                foreach ($datos as $data) {
                    if ($data->tipo == "INGRESOS" && $data->saldo != 0) {
                        $ingreso[] = $data;
                        $datos_excel = [];
                        array_push($datos_excel, $data->plan, $data->nombre, $data->saldo);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            if ($posicion[$i] == 'B') {
                                $sheet->mergeCells($posicion[$i] . $comienzo . ':' . 'C' . $comienzo);
                            }
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                }
                //$sheet->mergeCells('D' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    $cell->setValue('Total ingresos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G' . $comienzo . ':' . 'G' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) use ($totalIngresos) {
                    $cell->setValue($totalIngresos);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $sheet->mergeCells('B' . $comienzo . ':' . 'C' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) {
                    $cell->setValue('COSTOS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                //$sheet->mergeCells('D' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cuenta');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B' . $comienzo . ':' . 'C' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('F' . $comienzo . ':' . 'F' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $ingreso = [];
                foreach ($datos as $data) {
                    if ($data->tipo == "COSTOS" && $data->saldo != 0) {
                        $ingreso[] = $data;
                        $datos_excel = [];
                        array_push($datos_excel, $data->plan, $data->nombre, $data->saldo);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            if ($posicion[$i] == 'B') {
                                $sheet->mergeCells($posicion[$i] . $comienzo . ':' . 'C' . $comienzo);
                            }
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                }
                //$sheet->mergeCells('D' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    $cell->setValue('Total costos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('F' . $comienzo . ':' . 'F' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) use ($totalCostos) {
                    $cell->setValue($totalCostos);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $sheet->mergeCells('B' . $comienzo . ':' . 'C' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) {
                    $cell->setValue('GASTOS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                //$sheet->mergeCells('D' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cuenta');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B' . $comienzo . ':' . 'C' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('F' . $comienzo . ':' . 'F' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $ingreso = [];
                foreach ($datos as $data) {
                    if ($data->tipo == "GASTOS" && $data->saldo != 0) {
                        $ingreso[] = $data;
                        $datos_excel = [];
                        array_push($datos_excel, $data->plan, $data->nombre, $data->saldo);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            if ($posicion[$i] == 'B') {
                                $sheet->mergeCells($posicion[$i] . $comienzo . ':' . 'C' . $comienzo);
                            }
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                }
                //$sheet->mergeCells('D' . $comienzo . ':' . 'D' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    $cell->setValue('Total gastos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G' . $comienzo . ':' . 'G' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) use ($totalGastos) {
                    $cell->setValue($totalGastos);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                if (!is_null($empresa->id_representante)) {
                    if ($empresa->persona_nat_jur == '2') {
                        if (is_null($empresa->tipo_representante)) {
                            $representante = $empresa->pref_repre->titulo_prefijo . ' ' .
                                $empresa->usuario_representante->nombre1 . ' ' .
                                $empresa->usuario_representante->nombre2 . ' ' .
                                $empresa->usuario_representante->apellido1 . ' ' .
                                $empresa->usuario_representante->apellido2 . "\n" .
                                "\C.I. " . $empresa->id_representante . "\n";
                        } else {
                            $representante = $empresa->pref_repre->titulo_prefijo . ' ' .
                                $empresa->usuario_representante->nombre1 . ' ' .
                                $empresa->usuario_representante->nombre2 . ' ' .
                                $empresa->usuario_representante->apellido1 . ' ' .
                                $empresa->usuario_representante->apellido2 . "\n" .
                                "\EN REPRESENTACION DE " . "\n" . $empresa->empresa_representante . "\n";
                        }
                        $sheet->cell('A' . $comienzo, function ($cell) use ($representante) {
                            $cell->setValue($representante);
                            $cell->setAlignment('left');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->getStyle('A' . $comienzo)->getAlignment()->setWrapText(true);
                    }
                }
                if (!is_null($empresa->id_contador)) {
                    $contador = $empresa->pref_cont->titulo_prefijo . ' ' .
                        $empresa->usuario_contador->nombre1 . ' ' .
                        $empresa->usuario_contador->nombre2 . ' ' .
                        $empresa->usuario_contador->apellido1 . ' ' .
                        $empresa->usuario_contador->apellido2 . "\n" .
                        "C.I. " . $empresa->id_contador . "\n" .
                        "Registro # " . $empresa->num_registro_contador . "\n";
                    $sheet->cell('D' . $comienzo, function ($cell) use ($contador) {
                        $cell->setValue($contador);
                        $cell->setAlignment('left');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('D' . $comienzo)->getAlignment()->setWrapText(true);
                }
            });
        })->export('xlsx');
    }
    public function set_round($paremeters)
    {
        return number_format(round($paremeters, 2), 2, '.', '');
    }
    public function set_round_decimals($parameters, $decimals = "")
    {
        if ($decimals == null) {
            $decimals = 2;
        }
        $numbr = number_format(round($parameters, $decimals), $decimals);
        return $numbr;
    }
    public static function eliminarArchivoCompra($r)
    {
        $arrayRespuesta = [];
        try {
            $compra = Compras::where('id', $r->id)->first(['rutapdf']);
            if ($compra != '') {
                $ruta = base_path() . '/storage/app/v3/compras/' . $compra->rutapdf;
                unlink($ruta);
                $array = [
                    'rutapdf' => null
                ];
                Compras::where('id', $r->id)->update($array);
                $compra->rutapdf = null;
                $compra->save();
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Archivo eliminado correctamente',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Error al eliminar el archivo',
                'data' => $r->input(),
                'error' => $e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile()
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function ultimaConexion()
    {
        return Log_usuario::where('id_usuario', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->first(['created_at']);
    }
    public static function leerArchivoXMLCompra($r)
    {
        $xmlFile = $r->file($r->tipo_archivo);
        if ($xmlFile && $xmlFile->getClientOriginalExtension() === 'xml') {
            $xmlContent = file_get_contents($xmlFile->path());
            $xml = simplexml_load_string($xmlContent);
            $datos = (simplexml_load_string($xml->comprobante));
            $array = json_encode($datos);
            return $array;
        } else {
            return ['status' => false, 'msj' => "No se subió un archivo XML válido."];
        }
    }
    public static function getarchivoView($id, $tipo = '')
    {
        $buscar = "entidad";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_empresa_archivo', $id)->where('tipo_archivo', $tipo)->first(['archivo']);
        }
        $buscar = "cliente";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_cliente_archivo', $id)->where('tipo_archivo', $tipo)->first(['archivo']);
        }
        $buscar = "proveedor";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_proveedor_archivo', $id)->where('tipo_archivo', $tipo)->first(['ruta_archivo']);
        }
        $buscar = "empleadoCedula";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_usuario_archivo', $id)->where('tipo_archivo', $tipo)->first(['ruta_archivo']);
        }
        $buscar = "empleadoFichaOcupacional";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_usuario_archivo', $id)->where('tipo_archivo', $tipo)->first(['ruta_archivo']);
        }
        $buscar = "empleadoFichaTecnica";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_usuario_archivo', $id)->where('tipo_archivo', $tipo)->first(['ruta_archivo']);
        }
        $buscar = "usuario";
        $resultado = strpos($tipo, $buscar);
        if ($resultado !== FALSE) {
            return Archivos::where('id_usuario_archivo', $id)->where('tipo_archivo', $tipo)->first(['ruta_archivo']);
        }
    }
    public static function getMetodoPagoFactura()
    {
        $arrayRespuesta = [
            'bancos' => DB::table('ct_bancos')->where('estado', 1)->get(),
            'tipoPago' => DB::table('ct_tipo_pago')->where('estado', 1)->get(),
            'tipoTarjeta' => DB::table('ct_tipo_tarjeta')->where('estado', 1)->get()
        ];
        return json_encode($arrayRespuesta);
    }
    public static function savePlanillaPrincipal($r)
    {
        $arrayRespuesta = [
            'status' => 'error',
            'message' => 'Error al guardar la planilla'
        ];
        DB::beginTransaction();
        try {
            ConfigSistema::where('id_usuario_config_sistema', Auth::user()->id)->delete();
            $arrayPlantilla = [
                'id_usuario_config_sistema' => Auth::user()->id,
                'plantilla_config_sistema' => $r->plantilla,
                'estado_config_sistema' => 1,
                'created_at_config_sistema' => date('Y-m-d H:m:s'),
                'updated_at_config_sistema' => date('Y-m-d H:m:s')
            ];
            ConfigSistema::insert($arrayPlantilla);
            $arrayRespuesta = [
                'status' => 'success',
                'message' => 'La plantilla ' . ucwords(strtolower($r->plantilla)) . ' ha sigo guardada con exito...'
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'message' => $e->getMessage() . ' - ' . $e->getLine() . ' - ' . $e->getFile()
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function leerTour($r)
    {
        try {
            $ruta_archivo = base_path() . '/storage/app/tour/' . $r->controlador . '.txt';
            $arrayTourCompleto = [];
            foreach ($r->elements as $element) {
                $arrayElement = [];
                $arrayTour = [];
                $archivo = fopen($ruta_archivo, 'r');
                /*while (!feof($archivo)) {
                    echo fgets($archivo) . '<br/>';
                }
                fclose($archivo);
                exit;*/
                while (!feof($archivo)) {
                    $linea = fgets($archivo);
                    $cadena = $linea;
                    $buscar = 'title-' . $element;
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $arrayElement = [
                            'title' => str_replace('title-' . $element . ':', '', $linea)
                        ];
                        array_push($arrayTour, $arrayElement);
                    }
                    $buscar = 'text-' . $element;
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $arrayElement = [
                            'text' => str_replace('text-' . $element . ':', '', $linea)
                        ];
                        array_push($arrayTour, $arrayElement);
                    }
                    $buscar = 'element-' . $element;
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $arrayElement = [
                            'element' => str_replace('element-' . $element . ':', '', $linea)
                        ];
                        array_push($arrayTour, $arrayElement);
                    }
                    $buscar = 'on-' . $element;
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $arrayElement = [
                            'on' => str_replace('on-' . $element . ':', '', $linea)
                        ];
                        array_push($arrayTour, $arrayElement);
                    }
                }
                fclose($archivo);
                array_push($arrayTourCompleto, $arrayTour);
            }
        } catch (Exception $e) {
            $arrayTourCompleto = [];
        }
        return json_encode($arrayTourCompleto);
    }
    public static function crearcarpeta($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 777, true);
        }
    }
    public static function validarFirma($r)
    {
        $arrayRespuesta = [];
        try {
            $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
            if ($deEmpresa != '') {
                $clave = $r->clave;
                $file = base_path() . '/storage/app/facturaelectronica/p12/' . $deEmpresa->id_empresa . '/' . $r->firma;
                $fd = fopen($file, 'r');
                $p12buf = fread($fd, filesize($file));
                fclose($fd);
                $resultado = [];
                openssl_pkcs12_read($p12buf, $resultado, $clave);
                if (count($resultado) > 0) {
                    $arrayRespuesta = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Clave y firma correctas',
                        'data' => $r->input(),
                    ];
                } else {
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'Clave y/o firma incorrectas',
                        'data' => $r->input(),
                    ];
                }
            } else {
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return $arrayRespuesta;
    }
    public static function guardarArchivo($r)
    {
        $resultado = '';
        $nombre_archivo = $_FILES[$r->tipo_archivo]['name'];
        $ext = explode('.', $_FILES[$r->tipo_archivo]['name']);
        $ip = Utilidades::getRealIP();
        if ($r->tipo_archivo == 'entidadFirma') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/facturaelectronica/p12/' . $r->id_empresa);
            $ruta = base_path() . '/storage/app/facturaelectronica/p12/' . $r->id_empresa . '/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $nombre_archivo)) {
                $resultado = 'ok|' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
        } else if ($r->tipo_archivo == 'entidadRuc') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/v3/entidad');
            $ruta = base_path() . '/storage/app/v3/entidad/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_empresa . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_empresa . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_empresa_archivo', session('id_empresa'))
                ->where('id_empresa_archivo', $r->id_empresa)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_empresa_archivo' => $r->id_empresa,
                    'ruta_archivo' => '/storage/app/v3/entidad/' . $r->id_empresa . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_empresa_archivo' => $r->id_empresa,
                    'ruta_archivo' => '/storage/app/v3/entidad/' . $r->id_empresa . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_empresa_archivo', $r->id_empresa)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
        } elseif ($r->tipo_archivo == 'proveedorRuc' || $r->tipo_archivo == 'proveedorCedula' || $r->tipo_archivo == 'proveedorVotacion') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/v3/proveedores');
            $ruta = base_path() . '/storage/app/v3/proveedores/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_proveedor . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_proveedor . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_empresa_archivo', session('id_empresa'))
                ->where('id_proveedor_archivo', $r->id_proveedor)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_proveedor_archivo' => $r->id_proveedor,
                    'ruta_archivo' => '/storage/app/v3/proveedores/' . $r->id_proveedor . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_proveedor_archivo' => $r->id_proveedor,
                    'ruta_archivo' => '/storage/app/v3/proveedores/' . $r->id_proveedor . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_proveedor_archivo', $r->id_proveedor)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
        } elseif ($r->tipo_archivo == 'usuarioFoto') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/v3/usuarios');
            $ruta = base_path() . '/storage/app/v3/usuarios/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . Auth::user()->id . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . Auth::user()->id . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_usuario_archivo', Auth::user()->id)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' =>  Auth::user()->id,
                    'ruta_archivo' => '/storage/app/v3/usuarios/' .  Auth::user()->id . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' =>  Auth::user()->id,
                    'ruta_archivo' => '/storage/app/v3/usuarios/' .  Auth::user()->id . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_usuario_archivo',  Auth::user()->id)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
            $url = url('/') . '/storage/app/v3/usuarios/' . Auth::user()->id . '_' . $nombre_archivo;
            $url = str_replace('public/', '', $url);
            $resultado = 'ok|<img class="profile-user-img img-fluid img-circle" src="' . $url . '" alt="User profile picture" style="width:89px !important;heigth:89px !important;">';
            //$resultado = 'ok|' . $url . '/storage/app/v3/usuarios/' . Auth::user()->id . '_' . $nombre_archivo;
        } elseif ($r->tipo_archivo == 'proveedorLogo') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/logo');
            $ruta = base_path() . '/storage/app/logo/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_proveedor . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_proveedor . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_proveedor_archivo', $r->id_proveedor)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            $proveedor = Proveedor::find($r->id_proveedor);

            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_proveedor_archivo' =>  $r->id_proveedor,
                    'ruta_archivo' => '/storage/app/logo/' .  Auth::user()->id . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_proveedor_archivo' =>  $r->id_proveedor,
                    'ruta_archivo' => '/storage/app/logo/' .  $r->id_proveedor . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_proveedor_archivo',  Auth::user()->id)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
            $proveedor->logo = $r->id_proveedor . '_' . $nombre_archivo;
            $proveedor->save();
            $url = url('/') . '/storage/app/logo/' . $r->id_proveedor . '_' . $nombre_archivo;
            $url = str_replace('public/', '', $url);
            $resultado = 'ok|<img src="' . $url . '" style="width:150px !important;heigth:180px !important;">';
        } elseif ($r->tipo_archivo == 'empleadoCedula' || $r->tipo_archivo == 'empleadoFichaOcupacional' || $r->tipo_archivo == 'empleadoFichaTecnica') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/v3/nomina/empleados');
            $ruta = base_path() . '/storage/app/v3/nomina/empleados/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_empleado . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_empleado . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_empresa_archivo', session('id_empresa'))
                ->where('id_usuario_archivo', $r->id_empleado)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' => $r->id_empleado,
                    'ruta_archivo' => '/storage/app/v3/nomina/empleados/' . $r->id_empleado . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' => $r->id_empleado,
                    'ruta_archivo' => '/storage/app/v3/nomina/empleados/' . $r->id_empleado . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                //dd($arrayArchivo);
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_usuario_archivo', $r->id_empleado)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
        } elseif ($r->tipo_archivo == 'usuarioCedula' || $r->tipo_archivo == 'usuarioVotacion') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/v3/usuarios');
            $ruta = base_path() . '/storage/app/v3/usuarios/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_usuario . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_usuario . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_empresa_archivo', session('id_empresa'))
                ->where('id_usuario_archivo', $r->id_usuario)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' => $r->id_usuario,
                    'ruta_archivo' => '/storage/app/v3/usuarios/' . $r->id_usuario . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' => $r->id_usuario,
                    'ruta_archivo' => '/storage/app/v3/usuarios/' . $r->id_usuario . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_usuario_archivo', $r->id_usuario)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
        } elseif ($r->tipo_archivo == 'usuarioLogo') {
            Utilidades::crearcarpeta(base_path() . '/storage/app/avatars');
            $ruta = base_path() . '/storage/app/avatars/';
            if (move_uploaded_file($_FILES[$r->tipo_archivo]['tmp_name'],  $ruta . $r->id_usuario . '_' . $nombre_archivo)) {
                $resultado = 'ok|' . $r->id_usuario . '_' . $nombre_archivo;
            } else {
                $resultado = $nombre_archivo . 'no|Ocurrió algún error al subir el fichero. No pudo guardarse.';
            }
            $archi = Archivos::where('id_usuario_archivo', $r->id_usuario)
                ->where('tipo_archivo', $r->tipo_archivo)
                ->first();
            $usuarios = Usuarios::find($r->id_usuario);

            if ($archi == '') {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' =>  $r->id_usuario,
                    'ruta_archivo' => '/storage/app/avatars/' .  Auth::user()->id . '_' . $nombre_archivo,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::insert($arrayArchivo);
            } else {
                $arrayArchivo = [
                    'id_empresa_archivo' => session('id_empresa'),
                    'tipo_archivo' => $r->tipo_archivo,
                    'extencion_archivo' => $ext[1],
                    'id_usuario_archivo' =>  $r->id_usuario,
                    'ruta_archivo' => '/storage/app/avatars/' .  $r->id_usuario . '_' . $nombre_archivo,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Archivos::where('id_empresa_archivo', session('id_empresa'))
                    ->where('id_usuario_archivo',  Auth::user()->id)
                    ->where('tipo_archivo', $r->tipo_archivo)
                    ->update($arrayArchivo);
            }
            $usuarios->imagen_url = $r->id_usuario . '_' . $nombre_archivo;
            $usuarios->save();
            $url = url('/') . '/storage/app/avatars/' . $r->id_usuario . '_' . $nombre_archivo;
            $url = str_replace('public/', '', $url);
            $resultado = 'ok|<img src="' . $url . '" style="width:150px !important;heigth:180px !important;">';
        }

        return $resultado;
    }
    public static function cambiarFormatoFecha($fecha)
    {
        $fecha = str_replace('/', '-', $fecha);
        $timestamp = \Carbon\Carbon::parse($fecha)->timestamp;
        $fecha = date('Y-m-d', $timestamp);
        return $fecha;
    }
    public static function guardarArchivoCompra($r)
    {
        $id = str_replace('archivoCompra_', '', $r->id);

        if (!is_null($r[$r->id])) {
            $nombre_original = $id . '_' . $_FILES[$r->id]['name'];
            $nombre_guardar = date('YmdHis') . str_replace(' ', '', $nombre_original);
            $filemd = $r->file($r->id);
            $extension = $filemd->getClientOriginalExtension();
            Storage::disk('archivos_compras')->put($nombre_guardar, File::get($filemd));
            $rutadelaimagen = $nombre_guardar;
            Utilidades::crearcarpeta($rutadelaimagen);
            DB::table('ct_compras')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]);
        }




        /*
        $id = str_replace('archivoCompra_', '', $r->id);
        $nombre_original = $id . '_' . $_FILES[$r->id]['name'];
        $nombre_guardar = date('YmdHis') . str_replace(' ', '', $nombre_original);
        $ruta = base_path() . '/storage/app/public/';
        if (move_uploaded_file($_FILES[$r->id]['tmp_name'],  $ruta . $nombre_guardar)) {
            $rutadelaimagen = $nombre_guardar;
            DB::table('ct_compras')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]);
        }
            */
    }
    public static function accesos()
    {
        $sql = "SELECT 
            a.controlador_auditoria as controlador_auditoria,
            a.submodulo_auditoria as submodulo_auditoria,
            COUNT(a.id_auditoria) as cantidad,
            (SELECT m.icon_menu FROM new_db_menu m WHERE m.controlador_menu = a.controlador_auditoria LIMIT 1) as icon_menu,
            (SELECT m.color_menu FROM new_db_menu m WHERE m.controlador_menu = a.controlador_auditoria LIMIT 1) as color_menu
        FROM 
            new_db_auditorias a
        left join `new_db_menu` as `m` on a.`controlador_auditoria` = `m`.`controlador_menu` and `m`.`id_pertenece_menu` is null 
        WHERE 
            a.id_usuario_auditoria='0921605895'
            AND a.submodulo_auditoria != 'index'
            AND a.submodulo_auditoria NOT LIKE '%combo'
            AND a.submodulo_auditoria NOT LIKE 'save%'
            AND a.submodulo_auditoria NOT LIKE 'perfil%'
            AND a.submodulo_auditoria NOT LIKE 'buscar%'
            AND a.submodulo_auditoria IS NOT NULL
            AND a.controlador_auditoria IS NOT NULL
        GROUP BY 
            a.controlador_auditoria, a.submodulo_auditoria
        ORDER BY 
            COUNT(a.id_auditoria) DESC;";
        return DB::select($sql);
    }
    public static function buscarTodo($text)
    {
        DB::enableQueryLog();
        $productos = Ct_productos::selectRaw("'productos' as modulo,codigo,nombre,'' as identificacion,descripcion,'' as numero_comprobante,ct_productos.created_at,'' as producto,'' as email,ct_productos.id")
            ->where('nombre', 'LIKE', '%' . trim($text, '.') . '%');
        //->where('codigo', 'LIKE', '%' . trim($text, '.') . '%');
        $clientes = Clientes::selectRaw("'clientes' as modulo,'' as codigo, nombre,identificacion,'' as descripcion,'' as numero_comprobante,ct_clientes.created_at,'' as producto,email_representante as email,ct_clientes.identificacion as id")
            ->where(function ($q) use ($text) {
                $q->orWhere('nombre', 'LIKE', '%' . $text . '%');
                $q->orWhere('identificacion', 'LIKE', '%' . $text . '%');
                $q->orWhere('nombre_representante', 'LIKE', '%' . $text . '%');
                $q->orWhere('cedula_representante', 'LIKE', '%' . $text . '%');
                $q->orWhere('telefono1_representante', 'LIKE', '%' . $text . '%');
                $q->orWhere('telefono2_representante', 'LIKE', '%' . $text . '%');
            });
        $ventas = Ventas::selectRaw("'ventas' as modulo,'' as codigo,c.nombre,c.identificacion,'' as descripcion,nro_comprobante as numero_comprobante,ct_ventas.created_at,p.nombre as producto,'' as email,ct_ventas.id")
            ->join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->leftJoin('ct_detalle_venta as dv', 'ct_ventas.id', 'dv.id_ct_ventas')
            ->leftjoin('ct_productos as p', 'dv.id_ct_productos', 'p.codigo')
            ->where(function ($q) use ($text) {
                $q->orWhere('c.identificacion', 'LIKE', '%' . $text . '%');
                $q->orWhere('id_asiento', 'LIKE', '%' . $text . '%');
                $q->orWhere('nro_comprobante', 'LIKE', '%' . $text . '%');
                $q->orWhere('concepto', 'LIKE', '%' . $text . '%');
                $q->orWhere('p.nombre', 'LIKE', '%' . trim($text, '.') . '%');
                $q->orWhere('c.nombre', 'LIKE', '%' . trim($text, '.') . '%');
            });
        $data = $clientes->union($productos)->union($ventas)->get();
        return $data;
    }
    public static function verificarPermisos($r, $cont)
    {
        $estado = true;
        $arrayPermisos = [];
        $permisos = PermisosUsuario::getPermisos(Auth::user()->id);
        if ($permisos != '') {
            $submodulos = Menu::where('funcion_menu', $r->submodulo)
                ->where('controlador_menu', $cont)
                ->whereNotNull('id_pertenece_menu')
                ->where('estado_menu', 1)
                ->first();
            if ($submodulos == '') {
                $estado = false;
            } else {
                $arrayPermisos = json_decode($permisos->id_submenu_permiso, true);
                if (!is_array($arrayPermisos) || !in_array($submodulos->id_menu, $arrayPermisos)) {
                    $estado = false;
                }
            }
        } else {
            $estado = false;
        }
        return $estado;
    }
    public static function getRealIP()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
                $entries = explode('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
                reset($entries);
                while (list(, $entry) = each($entries)) {
                    $entry = trim($entry);
                    if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                        $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/');
                        $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                        if ($client_ip != $found_ip) {
                            $client_ip = $found_ip;
                            break;
                        }
                    }
                }
            } else {
                $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
            }
        } else {
            $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
        }
        return $client_ip;
    }
    public static function detect()
    {
        $browser = array("IE", "OPERA", "MOZILLA", "NETSCAPE", "FIREFOX", "SAFARI", "CHROME", "IEMOBILE", "NINTENDOBROWSER", "MSIE", "TRIDENT", "EDGE", "EDG");
        $os = array("WIN", "MAC", "LINUX", "ANDROID", "IPHONE", "IPAD", "LUMIA", "CRKEY", "WIIU", "XBOX", "PLAYSTATION", "3DS", "SMART-TV", "SMARTTV", "ARMV7L", "X11");
        $info['browser'] = "OTHER";
        $info['os'] = "OTHER";
        $info['version'] = '';
        $info['agente'] = $_SERVER['HTTP_USER_AGENT'];
        foreach ($browser as $parent) {
            $s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent);
            $f = $s + strlen($parent);
            $version = substr($_SERVER['HTTP_USER_AGENT'], $f, 15);
            $version = preg_replace('/[^0-9,.]/', '', $version);
            if ($s) {
                $info['browser'] = $parent;
                if (isset($version)) $info['version'] = $version;
                else $info['version'] = '';
            }
        }
        foreach ($os as $val) {
            if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $val) !== false) $info['os'] = $val;
        }
        return $info;
    }
    public static function sanear_string_tildes($string)
    {
        $string = trim($string);
        $string = str_replace(array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'), array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'), $string);
        return $string;
    }
    public static function sanear_string_sin_tildes($string)
    {
        $string = trim($string);
        $string = str_replace(array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü', 'á', 'é', 'í', 'ó', 'ú', '�'), array('a', 'e', 'i', 'o', 'u', 'n', 'u', 'a', 'e', 'i', 'o', 'u', '?'), $string);
        return $string;
    }
    public static function sanear_string($string)
    {
        $string = trim($string);
        $string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string);
        $string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string);
        $string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string);
        $string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string);
        $string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string);
        $string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string);
        $string = str_replace(array("\\", "¨", "º", "-", "~", "#", "@", "|", "!", "\"", "·", "$", "%", "&", "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":", " "), '', $string);
        return $string;
    }
    public static function traerCatalogo($tipo)
    {
        $catalogo = Catalogos::where('catalogo', 'like', '%' . $tipo . '%')->whereNull('id_catalogo_pertenece')->first(['id_catalogo']);
        if (!is_null($catalogo)) {
            return Catalogos::where('id_catalogo_pertenece', $catalogo->id_catalogo)->get();
        } else {
            return [];
        }
    }
    public static function obtenercomprobanteSRI($clave = '', $para = '')
    {
        $html = '';
        $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl";
        $parametros = array();
        $parametros['claveAccesoComprobante'] = $clave;
        try {

            $client = new SoapClient($servicio, $parametros);
            $result = $client->autorizacionComprobante($parametros);
        } catch (Exception $e) {
            $client = new SoapClient($servicio, $parametros);
            $result = $client->autorizacionComprobante($parametros);
        }
        if ($para == '') {
            return $result;
        } else {
            try {
                $docCod = substr($clave, 8, 2);
                $esta = substr($clave, 24, 3);
                $emis = substr($clave, 27, 3);
                $num = substr($clave, 30, 9);
                $empresa = Empresa::where('id', session('id_empresa'))->first();
                if ($result->RespuestaAutorizacionComprobante->numeroComprobantes > 0) {
                    $cuerpo = '';
                    $comprobante = simplexml_load_string($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
                    /*echo '<pre>';
                    print_r($comprobante);*/
                    $tipoDoc = '';
                    if ($docCod == '01') {
                        $tipoDoc = 'Factura';
                        $totalPagar = 0;
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="98%">';
                        $cuerpo .= '<tr><td>Emisor: </td><td>' . $comprobante->infoTributaria->razonSocial . '</td></tr>
                        <tr><td>Ruc: </td><td colspan="6">' . $comprobante->infoTributaria->ruc . '</td></tr>
                        <tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoFactura->razonSocialComprador . '</td></tr>
                        <tr><td>Fecha Emision: </td><td>' . $comprobante->infoFactura->fechaEmision . '</td></tr>';
                        $cuerpo .= '<tr><td width="100%" colspan="2"><table width="100%"><tr><td>Codigo P</td>';
                        if (isset($comprobante->detalles->detalle[0]->codigoAuxiliar)) {
                            $cuerpo .= '<td>Codigo A</td>';
                        }
                        $cuerpo .= '<td>Descripción</td>
                        <td>Cantidad</td>
                        <td>Precio</td>
                        <td>Descuento</td>
                        <td>Total</td>';
                        $cuerpo .= '</tr>';
                        $subtotal0 = 0;
                        $subtotal12 = 0;
                        $iva = 0;
                        $codAux = '';
                        foreach ($comprobante->detalles->detalle as $row) {
                            $codAux = '';
                            if (isset($row->codigoAuxiliar)) {
                                $codAux = '<td style="border-bottom:1px solid gray;">' . $row->codigoAuxiliar . '</td>';
                            }
                            if ($row->impuestos->impuesto->tarifa > 0) {
                                $subtotal12 += (float)$row->impuestos->impuesto->baseImponible;
                                $iva += ((float)$row->impuestos->impuesto->baseImponible * (float)$row->impuestos->impuesto->tarifa) / 100;
                            } else {
                                $subtotal0 += $row->impuestos->impuesto->baseImponible;
                            }
                            $cuerpo .= '<tr>
                            <td style="border-bottom:1px solid gray;">' . $row->codigoPrincipal . '</td>
                            ' . $codAux . '
                            <td style="border-bottom:1px solid gray;">' . $row->descripcion . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">' . $row->cantidad . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->precioUnitario . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->descuento . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->precioTotalSinImpuesto . '</td>
                            </tr>';
                        }
                        $tr = '';
                        if ($codAux != '')
                            $tr = '<td></td>';
                        $apagar = (float)$subtotal12 + (float)$subtotal0 + (float)$comprobante->infoFactura->totalDescuento + (float)$iva;
                        $cuerpo .= '<tr>' . $tr . '<td colspan="4"></td><td>Tarifa 0%: </td><td style="text-align:right;">$ ' . (float)$subtotal0 . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Tarifa 12%: </td><td style="text-align:right;">$ ' . number_format((float)$subtotal12, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Subtotal: </td><td style="text-align:right;">$ ' . number_format(((float)$subtotal12 + (float)$subtotal0), 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Descuento: </td><td style="text-align:right;">$ ' . number_format((float)$comprobante->infoFactura->totalDescuento, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Impuesto: </td><td style="text-align:right;">$ ' . number_format($iva, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>A pagar: </td><td style="text-align:right;">$ ' . number_format($apagar, 2) . '</td></tr>';
                        $cuerpo .= '</table></td></tr>';
                        //$cuerpo .= '<tr><td colspan="">&nbsp;</td><td>Total a Pagar: </td><td style="text-align:right;">$ ' . number_format($totalPagar, 2) . '</td></tr></table></td></tr>';
                    }
                    if ($docCod == '03')
                        $tipoDoc = 'Liquidación de compra';
                    if ($docCod == '04') {
                        $tipoDoc = 'Nota de crédito';
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="98%">';
                        $cuerpo .= '<tr><td>Emisor: </td><td>' . $comprobante->infoTributaria->razonSocial . '</td></tr>
                        <tr><td>Ruc: </td><td colspan="6">' . $comprobante->infoTributaria->ruc . '</td></tr>
                        <tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoFactura->razonSocialComprador . '</td></tr>
                        <tr><td>Fecha Emision: </td><td>' . $comprobante->infoNotaCredito->fechaEmision . '</td></tr>';
                        $cuerpo .= '<tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoNotaCredito->razonSocialComprador . '</td></tr>
                        <tr><td>Documento mod: </td><td>' . $comprobante->infoNotaCredito->numDocModificado . '</td></tr>';
                    }
                    if ($docCod == '05')
                        $tipoDoc = 'Nota de débito';
                    if ($docCod == '06')
                        $tipoDoc = 'Guía de remisión';
                    if ($docCod == '07') {
                        $tipoDoc = 'Retención';
                        $totalRetenido = 0;
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="80%">';
                        try {
                            foreach ($comprobante->docsSustento->docSustento->retenciones->retencion as $row) {
                                $totalRetenido += (float)$row->valorRetenido;
                                $cuerpo .= '<tr><td>Codigo: </td><td style="text-align:center;">' . $row->codigoRetencion . '</td><td>Base Imponible: </td><td style="text-align:right;">$ ' . $row->baseImponible . '</td><td>Procentaje: </td><td style="text-align:center;">' . $row->porcentajeRetener . '</td><td>Retenido: </td><td style="text-align:right;">$ ' . $row->valorRetenido . '</td></tr>';
                            }
                        } catch (Exception $e) {
                            foreach ($comprobante->impuestos->impuesto as $row) {
                                $totalRetenido += (float)$row->valorRetenido;
                                $cuerpo .= '<tr><td>Codigo: </td><td style="text-align:center;">' . $row->codigoRetencion . '</td><td>Base Imponible: </td><td style="text-align:right;">$ ' . $row->baseImponible . '</td><td>Procentaje: </td><td style="text-align:center;">' . $row->porcentajeRetener . '</td><td>Retenido: </td><td style="text-align:right;">$ ' . $row->valorRetenido . '</td></tr>';
                            }
                        }
                        $cuerpo .= '<tr><td colspan="6">&nbsp;</td><td>Total retenido: </td><td style="text-align:right;">$ ' . number_format($totalRetenido, 2) . '</td></tr></table></td></tr>';
                    }
                    $html = '<table width="100%">
                <tr>
                <td>Documento:</td>
                <td>' . $tipoDoc . '</td>
                </tr>
                <tr>
                <td>Número comprobante:</td>
                <td>' . $esta . '-' . $emis . '-' . $num . '</td>
                </tr>                
                <tr>
                <td>Clave Consultada:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->claveAccesoConsultada . '</td>
                </tr>
                <tr>
                <td>Estado:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado . '</td>
                </tr>
                <tr>
                <td>Ambiente:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente . '</td>
                </tr>
                <tr>
                <td>Fecha autorización:</td>
                <td>' . date('Y-m-d H:i:s', strtotime($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)) . '</td>
                </tr>
                <tr>
                <td>Número de autorización:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion . '</td>
                </tr>
                <tr>
                <td>
                </td>
                </tr>
                ' . $cuerpo . '
                </table>';
                } else {
                    $html = '<table width="100%">
                    <tr>
                    <td colspan="2">Documento no autorizado por el SRI</td>
                    </tr>
                    <tr>
                    <td>Clave consultada:</td>
                    <td>' . $clave . '</td>
                    </tr>
                    </table>';
                }
            } catch (Exception $e) {
                $html = '<table width="100%">
                <tr>
                <td colspan="2">Documento no autorizado por el SRI</td>
                </tr>
                <tr>
                <td>Clave consultada:</td>
                <td>' . $clave . '</td>
                </tr>
                <tr><td>' . $e->getMessage() . '</td></tr>
                <tr><td>' . $e->getLine() . '</td></tr>
                </table>';
            }
            return $html;
        }
    }
    public static function getMensajesAutorizacion($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');
        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('Identificador',  $mensaje->identificador));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje->mensaje));
            if (isset($mensaje->informacionAdicional)) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje->informacionAdicional));
            }
            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje->tipo));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
    }
    public static function armarMenu()
    {
        $sql = " SELECT 
            m1.id_menu,
            m2.id_menu,
            m3.id_menu,
            m1.nombre_menu AS menu,
            m1.icon_menu,
            m1.controlador_menu,
            m2.nombre_menu AS submenu,
            m2.icon_menu AS icon_submenu,
            m2.funcion_menu AS funcion,
            m3.nombre_menu AS submenu_1,
            m3.icon_menu AS icon_submenu_1,
            m3.funcion_menu AS funcion_1 
        FROM 
            new_db_menu m1 
        JOIN 
            new_db_menu m2 ON m1.id_menu = m2.id_pertenece_menu
        LEFT JOIN 
            new_db_menu m3 ON m2.id_menu = m3.id_pertenece_menu
        WHERE 
            m1.estado_menu = 1
            AND m1.id_pertenece_menu IS NULL
        ORDER BY 
            m1.orden_menu, m1.nombre_menu;";
        $datos = array(
            array("nombre" => "Elemento 1", "tiene_hijos" => false),
            array("nombre" => "Elemento 2", "tiene_hijos" => true, "hijos" => array("Hijo 1", "Hijo 2", "Hijo 3")),
            array("nombre" => "Elemento 3", "tiene_hijos" => false),
            array("nombre" => "Elemento 4", "tiene_hijos" => true, "hijos" => array("Hijo 4", "Hijo 5")),
        );
        function buildMenuOptions($parentId, &$combo, $depth = 0, $nombre = '')
        {
            $submenu = Menu::where('id_pertenece_menu', $parentId)->get();
            if (count($submenu) > 0) {
                if ($depth > 0) {

                    $combo .= '<optgroup label="' . $nombre . '">';
                }
                foreach ($submenu as $row) {
                    $combo .= '<option value="' . $row->id_menu . '">' . $row->nombre_menu . '</option>';
                    buildMenuOptions($row->id_menu, $combo, $depth + 1, $row->nombre_menu);
                }
                if ($depth > 0) {
                    $combo .= '</optgroup>';
                }
            }
        }

        $combo = '';
        foreach (config('data.menu') as $rowm) {
            $combo .= '<optgroup label="' . $rowm->nombre_menu . '">';
            buildMenuOptions($rowm->id_menu, $combo);
            $combo .= '</optgroup>';
        }
        return $combo;
    }
    public static function guardarArchivoActivo($r)
    {

        $id = str_replace('archivoActivo_', '', $r->id);
        $idusuario  = Auth::user()->id;
        $nombre_original = $id . '_' . $_FILES[$r->id]['name'];
        $nombre_guardar = date('YmdHis') . str_replace(' ', '', $nombre_original);
        $filemd = $r->file($r->id);
        $carpeta = 'archivos_activos';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if (!Storage::disk($carpeta)->exists($carpeta)) {

            Storage::disk($carpeta)->makeDirectory($carpeta);
        }

        Storage::disk($carpeta)->put('archivos_activos/' . $nombre_guardar, File::get($filemd));

        Af_Activo_Archivos::where('id', $id)->insert([
            'id_factura'      => $id,
            'nombre_archivo'  => $nombre_guardar,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'tipo'            => 1 //preguntar
        ]);
    }
    public static function eliminarArchivoActivo($r)
    {
        $arrayRespuesta = [];
        try {
            $archivo = Af_Activo_Archivos::where('id_factura', $r->id)->where("estado", 1)->first();
            $archivo->estado = 0;
            $archivo->save();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Archivo eliminado correctamente',
                'data' => $r->input(),
            ];
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Error al eliminar el archivo',
                'data' => $r->input(),
                'error' => $e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile()
            ];
        }
        return json_encode($arrayRespuesta);
    }
}
