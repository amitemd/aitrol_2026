<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Excel;


class ReporteTributario extends Model
{


    public static function reportesExcel($r)
    {


        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $empresa = Empresas::find(session("id_empresa"));
        $name = substr($meses[$r->mes + 1], 0, 3);
        Excel::create('GASTRI-TECH S.A._' . $name . $r->anio, function ($excel) use ($empresa, $meses, $r) {
            $excel->sheet('Solicitud de Permiso', function ($sheet) use ($empresa, $meses, $r) {
                $sheet->mergeCells('A2:I2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A3:I3');
                $sheet->cell('A3', function ($cell) use ($empresa) {
                    $cell->setValue('R.U.C.::' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('D5:I5');
                $sheet->cell('D5', function ($cell) use ($empresa) {
                    $cell->setValue('Reporte Para Formulario IVA - 104 y Formulario Rte. IR 103');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });


                $sheet->cell('E7', function ($cell) {
                    $cell->setValue('MES');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });


                $sheet->cell('E7', function ($cell) {
                    $cell->setValue('MES');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });

                $sheet->cell('F7', function ($cell) use ($meses, $r) {
                    $cell->setValue($meses[$r->mes + 1]);
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });


                $sheet->cell('H7', function ($cell) {
                    $cell->setValue('AÑO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });


                $sheet->cell('F7', function ($cell) use ($r) {
                    $cell->setValue($r->anio);
                    $cell->setAlignment('center');
                    $cell->setFontColor('#4F81BD');
                });


                $sheet->mergeCells('A9:I9');
                $sheet->cell('A9', function ($cell) {
                    $cell->setValue('RESUMEN DE VENTAS Y OTRAS OPERACIONES DEL PERÍODO QUE DECLARA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });



                $sheet->mergeCells('C11:D11');
                $sheet->cell('C11', function ($cell) {
                    $cell->setValue('Valor Bruto');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });
                $sheet->mergeCells('E11:F11');
                $sheet->cell('E11', function ($cell) {
                    $cell->setValue('NOTAS DE CREDITO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });
                $sheet->mergeCells('G11:H11');
                $sheet->cell('G11', function ($cell) {
                    $cell->setValue('Valor Neto');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });





                $sheet->cell('C12', function ($cell) {
                    $cell->setValue('FACTURAS 0%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->cell('D12', function ($cell) {
                    $cell->setValue('"FACTURAS 12%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->cell('E12', function ($cell) {
                    $cell->setValue('0%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->cell('F12', function ($cell) {
                    $cell->setValue('12%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });
                $sheet->cell('G12', function ($cell) {
                    $cell->setValue('FACTURAS 0%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->cell('H12', function ($cell) {
                    $cell->setValue('FACTURAS 12%');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->mergeCells('I11:I12');
                $sheet->cell('I11', function ($cell) {
                    $cell->setValue('IVA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#4F81BD');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                });

                $sheet->getColumnDimension('E')->setWidth(20);
            });
        })->export('xlsx');
    }
}
