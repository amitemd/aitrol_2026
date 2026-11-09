<?php

namespace Sis_medico\Services;

use Excel;
use Illuminate\Http\Request;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Agenda;
use Sis_medico\Empresa;
use Sis_medico\FormatoDescargaInsumos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_protocolo;

class ClinicalConsumptionExportService
{
    public function oxygen(Request $request)
    {
        $fecha    = $request->fecha_oxigeno;
        $hora_ini = $request->hora_ini;
        $hora_fin = $request->hora_fin;
        $idagenda = $request->agenda;

        $agenda = Agenda::find($idagenda);

        $id_empresa = $agenda->id_empresa;
        $empresa    = Empresa::find($id_empresa);
        Excel::create('Reporte de consumo de oxigeno en pacientes ' . $fecha, function ($excel) use ($fecha, $hora_ini, $hora_fin, $empresa, $agenda, $request) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($fecha, $hora_ini, $hora_fin, $empresa, $agenda, $request) {

                if ($empresa->logo != null && $agenda->id_seguro != 5) {
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('A5:I5');

                $sheet->cell('A5', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue($empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });

                $sheet->mergeCells('A8:I8');

                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue('                                   REPORTE DE CONSUMO DE OXIGENO EN PACIENTES');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });

                $sheet->cell('A10', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');

                    $cell->setValue('HC: ');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('B10', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($agenda->paciente->id);
                    //   $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('A11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('PACIENTE: ');

                    //   $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('B11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($agenda->paciente->apellido1 . " " . $agenda->paciente->apellido2 . " " . $agenda->paciente->nombre1 . " " . $agenda->paciente->nombre2);
                    //   $cell->setBorder('thin', '', '', '');
                });

                $sheet->cell('A14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->mergeCells('B14:C14');
                $sheet->cell('B14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('HORAS');

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('CODIGO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('E14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('SERVICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('F14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('litros');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('G14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('minutos');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('H14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('horas');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('I14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('TOTAL/LITROS');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('J14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('K14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('VALOR TOTAL');
                    //$cell->setBackground('#FFFF00');
                    // arriba,derecha,abajo,izquierda
                    //$cell->setBorder('thin', 'thin', '', '');
                    $cell->setBorder('thin', 'thin', '', '');
                });

                $sheet->cell('A15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('INICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('FINAL');
                    $cell->setValignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('D15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', 'thin');

                });
                $sheet->cell('E15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');

                });
                $sheet->cell('F15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');

                    $cell->setBorder('', '', 'thin', '');

                });
                $sheet->cell('G15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('O');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('A15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('INICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('FINAL');
                    $cell->setValignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('D15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', 'thin');

                });
                $sheet->cell('E15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');

                });
                $sheet->cell('F15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');

                    $cell->setBorder('', '', 'thin', '');

                });
                $sheet->cell('G15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });

                //AAAAAAA

                $sheet->cell('A16', function ($cell) use ($fecha) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue($fecha);
                    $cell->setBorder('thin', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B16', function ($cell) use ($hora_ini) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($hora_ini);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C16', function ($cell) use ($hora_fin) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($hora_fin);
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cell('D16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    //$cell->setFontWeight('');
                    $cell->setValue('H900034');
                    $cell->setBorder('thin', '', 'thin', 'thin');

                });
                $sheet->cell('E16', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    //$cell->setFontWeight('bold');
                    $cell->setValue('OXIGENO (LITROS/MINUTO)');
                    $cell->setBorder('thin', '', 'thin', 'thin');

                });
                $sheet->cell('F16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue($request->litros);

                    $cell->setBorder('', '', 'thin', 'thin');

                });
                $sheet->cell('G16', function ($cell) use ($agenda, $request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60 * (-1);
                    $segundos = $minutos / 60;
                    //$hora_final= $horafin-$horaini;
                    $cell->setFontWeight('bold');
                    $cell->setValue($minutos);
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60;
                    $segundos = $minutos / 60;
                    //dd($seconds/60);
                    $horafinal = $minutos;
                    //$horafinal = strtotime ( '+'.$horafin.'minute' ,$fechaini) ;
                    //$horafinal=$horafin-$horaini;
                    $fina = 100 * $horafinal / 60;

                    $final = $fina / 100;
                    $cell->setFontWeight('bold');
                    $cell->setValue($segundos * (-1));
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $litros = $request->litros;

                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60;
                    $segundos = $minutos / 60;
                    //dd($seconds/60);
                    $horafinal = $minutos;
                    $total     = $litros * $horafinal;
                    $cell->setFontWeight('bold');
                    $cell->setValue($total * (-1));
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('0.01');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    $litros   = $request->litros;
                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60;
                    $segundos = $minutos / 60;
                    $time1    = new DateTime($fechaini);
                    $time2    = new DateTime($fechafin);
                    $interval = $time1->diff($time2);
                    //dd($interval);
                    $hora = $interval->format("%i min");
                    $hora = intval(substr($hora, 0, 2));
                    if ($hora == 0) {
                        $hora = 60;
                    }
                    $horafinal = $horafin - $horaini;
                    $total     = $litros * $minutos * (-1);
                    $tot       = 0.01 * $total;
                    $cell->setFontWeight('bold');
                    $cell->setValue($tot);
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                //$i = $this->setDetalles($consulta, $sheet, 6);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
        })->export('xlsx');
    }
    public function products(Request $request)
    {
        $protocolo = hc_protocolo::findOrFail($request->protocolo);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $formato   = FormatoDescargaInsumos::find($request->tipo_formato);
        $fecha     = $request->fecha_oxigeno;
        $agenda    = Agenda::find($id_agenda);
        $empresaf  = $agenda->id_empresa;
        $empresa   = Empresa::find($empresaf);
        //dd($empresa);
        Excel::create('Reporte de consumo de oxigeno en pacientes ' . $fecha, function ($excel) use ($fecha, $empresa, $agenda, $request, $formato) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($fecha, $empresa, $agenda, $request, $formato) {

                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('B1:F1');

                $sheet->cell('B1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(14);
                    $cell->setValue('                                 ' . $empresa->nombrecomercial . '/' . $empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A2:F2');

                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 ' . $empresa->direccion);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A3:F3');

                $sheet->cell('A3', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 Telefono:' . $empresa->telefono);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A4:F4');

                $sheet->cell('A4', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 GUAYAQUIL-ECUADOR');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A7:F7');

                $sheet->cell('A7', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(14);
                    $cell->setValue('                                     LISTA DE MEDICAMENTOS Y MATERIALES USADOS EN SALA DE ENDOSCOPIA    ');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A9:F9');

                $sheet->cell('A9', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('PACIENTE:  ' . $agenda->paciente->apellido1 . " " . $agenda->paciente->apellido2 . " " . $agenda->paciente->nombre1 . " " . $agenda->paciente->nombre2);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A10:C10');
                $sheet->cell('A10', function ($cell) use ($formato) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('PROCEDIMIENTO:  ' . $formato->descripcion);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('D10', function ($cell) use ($fecha) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('FECHA:   ' . $fecha);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A11:C11');
                $sheet->cell('A11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('SEGURO:  ' . $agenda->seguro->nombre);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('D11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('H.CLINICA:  ' . $agenda->paciente->id);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('A13', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('NOMBRE');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('B13', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('CANTIDAD');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //   $cell->setBorder('', '', 'thin','');
                });
                $acumulador  = 14;
                $contador    = 0;
                $cuentamitad = count($formato->detalles);
                $mitad       = count($formato->detalles) / 2;
                $mitadint    = intval($mitad);
                if ($cuentamitad > 1) {
                    $sheet->cell('D13', function ($cell) use ($agenda) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('NOMBRE');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        //   $cell->setBorder('', '', 'thin','');
                    });
                    $sheet->cell('E13', function ($cell) use ($agenda) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('CANTIDAD');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //   $cell->setBorder('', '', 'thin','');
                    });
                }

                $contadore   = 0;
                $acumuladore = 14;
                foreach ($formato->detalles as $val) {
                    //dd($val->hc_procedimiento_f);
                    //dd($val);
                    if ($cuentamitad == 1) {
                        $sheet->cell('A' . $acumulador, function ($cell) use ($val) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            //$cell->setFontWeight('bold');
                            $cell->setValue($val->nombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $acumulador, function ($cell) use ($val) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            //$cell->setFontWeight('bold');
                            $cell->setValue($val->cantidad);
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $acumulador++;

                    } else {
                        if ($contador < $mitadint) {
                            $sheet->cell('A' . $acumulador, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('B' . $acumulador, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->cantidad);
                                $cell->setValignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $acumulador++;
                        } else {
                            $sheet->cell('D' . $acumuladore, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('E' . $acumuladore, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->cantidad);
                                $cell->setValignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $acumuladore++;
                        }
                    }

                    $contador++;

                }

                foreach ($formato->detalles as $val) {
                    //dd($val->hc_procedimiento_f);
                    //dd($val);
                    if ($mitadint <= $cuentamitad) {

                        $acumuladore++;
                    }
                    $contador++;

                }

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                //$i = $this->setDetalles($consulta, $sheet, 6);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(27)->setAutosize(false);
        })->export('xlsx');
    }

    public function nueva_evolucion( $id_hc ){


        $historiaclinica = Historiaclinica::find($id_hc);
        //dd($historiaclinica);
        $evoluciones = Hc_Evolucion::where('hcid',$id_hc)->where('estado','1')->orderBy('secuencia')->get();

        $view = \View::make('hc_admision.formato.nuevo_evolucion_todo', compact('evoluciones','historiaclinica'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');


    }
}

