<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Orden;
use Sis_medico\Empresa;
use Sis_medico\Nivel;
use Sis_medico\Labs_doc_externos;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Examen_Comision;
use Sis_medico\Examen_Detalle;

class LabsComisionesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 10, 11, 12)) == false && $id_auth != '1307189140') {
            return true;
        }
    }


    public function comisiones(Request $request)
    {

        /*if($this->rol()){
            return response()->view('errors.404');
        }*/

        $año = $request->anio;
        $mes = $request->mes;

        $año_hasta =  $año;
        $mes_hasta =  $request->mes_hasta;

        if ($año == null) {
            $año = date('Y');
            $mes = date('m');
            $mes = $mes - 2;
            if ($mes < 0) {
                $mes = 1;
            }
            $año_hasta = $año;
            $mes_hasta = $mes + 1;
        }

        if ($mes_hasta < $mes) {
            $mes_hasta = $mes + 1;
        }

        //PROCESO INICIAL ACTUALIZA DETALLE DE COMISIONES 0.08 PARA HUMANLABS

        /*$ordenes = Examen_Orden::where('examen_orden.estado','1')
            ->select('examen_orden.*')
            ->join('seguros as s','s.id','examen_orden.id_seguro')
            ->where('examen_orden.estado_comision','0')
            ->where('examen_orden.fecha_orden','>','2021-01-01 00:00:00')
            ->where('examen_orden.anio',$año)
            ->where('examen_orden.mes',$mes) 
            ->where('s.tipo','<>',0)
            ->get(); */


        $ordenes = Examen_Orden::where('examen_orden.estado', '1')
            ->select('examen_orden.*')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('examen_orden.estado_comision', '0')
            ->where('examen_orden.fecha_orden', '>', '2020-01-01 00:00:00')
            ->where('examen_orden.anio', '>=', $año)
            ->where('examen_orden.anio', '<=', $año_hasta)
            ->where('examen_orden.mes', '>=', $mes)
            ->where('examen_orden.mes', '<=', $mes_hasta)
            ->where('s.tipo', '<>', 0)
            ->get();



        foreach ($ordenes as $orden) {
            $detalles = $orden->detalles;
            foreach ($detalles as $detalle) {
                $comision = 0;
                $hl = 0;
                if ($detalle->examen->humanlabs) {
                    $comision = 0.08;
                    $hl = 1;
                    $detalle->update([
                        'p_comision' => $comision,
                        'human_labs' => $hl,
                    ]);
                }
            }
            $orden->update([
                'estado_comision' => '1',
            ]);
        }

        //dd($ordenes);


        $ordenes = Examen_Orden::where('examen_orden.estado_comision', '1')
	    ->where('examen_orden.estado','1')
            ->select('examen_orden.id_doctor_ieced', 'examen_orden.anio', 'examen_orden.mes', DB::raw('CASE WHEN ed.valor_descuento IS NULL THEN SUM( ( ed.valor ) * ed.p_comision ) ELSE SUM( ( ed.valor - ed.valor_descuento) * ed.p_comision ) END as val'),DB::raw('CASE WHEN ed.valor_descuento IS NULL THEN SUM( ( ed.valor ) * ed.p_comision ) ELSE SUM( ( ed.valor - ed.valor_descuento) * ed.p_comision ) END as val2'))
            ->where('examen_orden.anio', '>=', $año)
            ->where('examen_orden.anio', '<=', $año_hasta)
            ->where('examen_orden.mes', '>=', $mes)
            ->where('examen_orden.mes', '<=', $mes_hasta)
            ->where('examen_orden.id_doctor_ieced', '<>', 'GASTRO')
            ->where('examen_orden.id_doctor_ieced', '<>', '1234517896')
            ->where('examen_orden.id_doctor_ieced', '<>', 'DREX000001')
            ->join('examen_detalle as ed', 'ed.id_examen_orden', 'examen_orden.id')
            ->where('ed.human_labs', '1')
            ->groupBy('examen_orden.id_doctor_ieced', 'examen_orden.anio', 'examen_orden.mes')
            ->orderBy('examen_orden.id_doctor_ieced', 'examen_orden.anio', 'examen_orden.mes asc')
            ->get();




        $arr_com = [];
        $cambio_doc = '0';
        $arr_mes = [];
        $cont = 0;

        foreach ($ordenes as $orden) {
            if ($cambio_doc == '0') {
                $cambio_doc = $orden->id_doctor_ieced;
            }

            if ($cambio_doc !== $orden->id_doctor_ieced) {
                $cambio_doc = $orden->id_doctor_ieced;
                $arr_mes = [];
                $arr_staff = [];
            }

            $arr_mes[$orden->anio . '-' . $orden->mes] = $orden->val;
            $arr_mes['staff-' . $orden->anio . '-' . $orden->mes] = $orden->val2;//define el valor de comision del mes
            $arr_com[$orden->id_doctor_ieced] = $arr_mes;



            if ($cont == 3) {
                //dd($arr_com,$cambio_doc);
            }

            $cont++;
        }

        $arr_tmp = [];
        $i = 0;
        $x = $año;
        $y = $mes;
        for ($x; $x <= $año_hasta; $x++) {
            for ($y; $y <= $mes_hasta; $y++) {
                $arr_tmp[$i] = $x . '-' . $y;
                $i++;
            }
        }

        $users = User::all();

        $ordenes_externos = Examen_Orden::where('estado', '1')
            ->select('codigo', 'anio', 'mes', DB::raw('SUM( (valor - descuento_valor) * 0.10 )as val'))
            ->where('anio', '>=', $año)
            ->where('anio', '<=', $año_hasta)
            ->where('mes', '>=', $mes)
            ->where('mes', '<=', $mes_hasta)
            ->whereNotNull('codigo')
            //->where('id_doctor_ieced','1234517896')
            ->groupBy('codigo', 'anio', 'mes')
            ->orderBy('codigo', 'anio', 'mes asc')
            ->get();

        $arr_com2 = [];
        $cambio_doc2 = '0';
        $arr_mes2 = [];
        $cont2 = 0;

        foreach ($ordenes_externos as $oe) {
            if ($cambio_doc2 == '0') {
                $cambio_doc2 = $oe->codigo;
            }

            if ($cambio_doc2 !== $oe->codigo) {
                $cambio_doc2 = $oe->codigo;
                $arr_mes2 = [];
            }

            $arr_mes2[$oe->anio . '-' . $oe->mes] = $oe->val;
            $arr_com2[$oe->codigo] = $arr_mes2;



            if ($cont2 == 3) {
                //dd($arr_com,$cambio_doc);
            }

            $cont2++;
        }


        return view('laboratorio/labscomisiones/index', ['arr_com' => $arr_com, 'arr_tmp' => $arr_tmp, 'año' => $año, 'mes' => $mes, 'users' => $users, 'mes_hasta' => $mes_hasta, 'arr_com2' => $arr_com2,]);
    }

    function detalle_comisiones($ames, $id_doctor)
    {

        //dd(substr($ames,0,4),substr($ames,5),$id_doctor);
        $año = substr($ames, 0, 4);
        $mes = substr($ames, 5);
        $doctor = User::find($id_doctor);

        $ordenes = Examen_Orden::where('examen_orden.estado_comision', '1')
            ->select('examen_orden.id_doctor_ieced', 'examen_orden.anio', 'examen_orden.mes', 'examen_orden.id', 'e.nombre', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ed.valor', 'ed.valor_descuento', 'examen_orden.fecha_orden', DB::raw('CASE WHEN ed.valor_descuento IS NULL THEN ( ed.valor * ed.p_comision ) ELSE ( ( ed.valor - ed.valor_descuento) * ed.p_comision ) END as val'))
            ->where('examen_orden.anio', $año)
            ->where('examen_orden.mes', $mes)
            ->where('examen_orden.id_doctor_ieced', $id_doctor)
            ->join('examen_detalle as ed', 'ed.id_examen_orden', 'examen_orden.id')
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('examen as e', 'e.id', 'ed.id_examen')
            ->where('ed.human_labs', '1')
            ->get();
        //dd($ordenes->first());    

        Excel::create('Detalle-' . $doctor->apellido1 . '-' . $año . '-' . $mes, function ($excel) use ($ordenes, $doctor, $año, $mes) {

            $excel->sheet('Detalle', function ($sheet) use ($ordenes, $doctor, $año, $mes) {

                $letras = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];

                $fecha_d = date('Y/m/d');
                $i       = 3;
                $sheet->mergeCells('A1:G1');

                //$mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($doctor, $año, $mes) {
                    // manipulate the cel
                    $cell->setValue('DETALLE DE COMISIONES ' . $doctor->apellido1 . ' ' . $doctor->apellido2 . ' ' . $doctor->nombre1 . ' ' . $año . '-' . $mes);
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMISION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $total_valor = 0;
                $total_descuento = 0;
                $total_comision = 0;

                foreach ($ordenes as $orden) {

                    $total_valor += $orden->valor;
                    $total_descuento += $orden->valor_descuento;
                    $total_comision += $orden->val;

                    $sheet->cell('A' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id);

                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->fecha_orden);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->apellido1 . ' ' . $orden->apellido2 . ' ' . $orden->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor_descuento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $i++;
                }

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) use ($total_valor) {
                    // manipulate the cel
                    $cell->setValue($total_valor);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('F' . $i, function ($cell) use ($total_descuento) {
                    // manipulate the cel
                    $cell->setValue($total_descuento);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('G' . $i, function ($cell) use ($total_comision) {
                    // manipulate the cel
                    $cell->setValue($total_comision);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }


    function detalle_comisiones_externos($ames, $codigo){
        $año = substr($ames,0,4);
        $mes = substr($ames,5);
        $doctor_externo = Labs_doc_externos::find($codigo);

        $ordenes_externos = Examen_Orden::where('examen_orden.estado','1')
            ->select('examen_orden.codigo','examen_orden.anio','examen_orden.mes','examen_orden.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'examen_orden.valor', 'examen_orden.descuento_valor', 'examen_orden.fecha_orden', DB::raw('( ( examen_orden.valor - examen_orden.descuento_valor) * 0.1 ) as val'))
            ->join('labs_doc_externos as le','le.id','examen_orden.codigo')
            ->join('paciente as p','p.id','examen_orden.id_paciente')
            ->where('examen_orden.anio',$año)
            ->where('examen_orden.mes',$mes)
            ->where('examen_orden.codigo',$codigo)
            ->whereNotNull('examen_orden.codigo')
            ->get();
        Excel::create('Detalle-'.$doctor_externo->apellido1.'-'.$año.'-'.$mes, function ($excel) use ($ordenes_externos, $doctor_externo, $año, $mes) {

            $excel->sheet('Detalle', function ($sheet) use ($ordenes_externos, $doctor_externo, $año, $mes) {

                $letras = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];

                $fecha_d = date('Y/m/d');
                $i       = 3;
                $sheet->mergeCells('A1:H1');

                //$mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($doctor_externo, $año, $mes) {
                    // manipulate the cel
                    $cell->setValue('DETALLE DE COMISIONES '.$doctor_externo->apellido1.' '.$doctor_externo->nombre1.' '.$doctor_externo->nombre1.' '.$año.'-'.$mes );
                    $cell->setFontSize(16);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMISION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $total_valor = 0;
                $total_descuento = 0;
                $total_comision = 0;
                foreach($ordenes_externos as $orden){
                    $xorden = Examen_Orden::find($orden->id);
                    $total_valor += $orden->valor;
                    $total_descuento += $orden->descuento_valor;
                    $total_comision += $orden->val;
                    //$total_comision += $orden->val;
                    $sheet->cell('A'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id );
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->fecha_orden);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C'.$i, function ($cell) use ($xorden) {
                        // manipulate the cel
                        $cell->setValue($xorden->doctor->apellido1.' '.$xorden->doctor->apellido2.' '.$xorden->doctor->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function ($cell) use ($xorden) {
                        // manipulate the cel
                        $cell->setValue($xorden->seguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->apellido1.' '.$orden->apellido2.' '.$orden->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $i++;

                }
                /* fin for */
                $sheet->cell('E'.$i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F'.$i, function ($cell) use ($total_valor) {
                    // manipulate the cel
                    $cell->setValue($total_valor);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('G'.$i, function ($cell) use ($total_descuento) {
                    // manipulate the cel
                    $cell->setValue($total_descuento);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('H'.$i, function ($cell) use ($total_comision) {
                    // manipulate the cel
                    $cell->setValue($total_comision);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                

            });
        })->export('xlsx');    


    }

    public function masivo_empresa_nomina(){
        try {
            DB::beginTransaction();
            $empresas = Empresa::where('estado',1)->get();
            foreach ($empresas as $empresa) {
                $sucursal = Ct_Sucursales::where('id_empresa',$empresa->id)->orderBy('id','asc')->first();
                if(!is_null($sucursal)){
                    $array_sucur = [
                        'uso_nomina' => 1,
                    ];
                    $sucursal->update($array_sucur);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function masivo_valor_comision($ames,$codigo){
        $año = substr($ames,0,4);
        $mes = substr($ames,5);
        //dd($codigo,$año,$mes);
        try {
            DB::beginTransaction();
            $año = substr($ames,0,4);
            $mes = substr($ames,5);
            $actualizado =false;
            $doctor_externo = Labs_doc_externos::find($codigo);
            $ordenes_externos = Examen_Orden::where('examen_orden.estado','1')
                ->select('examen_orden.codigo','examen_orden.anio','examen_orden.mes','examen_orden.id','le.id as id_doctor', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'examen_orden.valor', 'examen_orden.descuento_valor', 'examen_orden.fecha_orden')
                ->join('labs_doc_externos as le','le.id','examen_orden.codigo')
                ->join('paciente as p','p.id','examen_orden.id_paciente')
                ->where('examen_orden.anio',$año)
                ->where('examen_orden.mes',$mes)
                ->where('examen_orden.codigo',$codigo)
                ->whereNotNull('examen_orden.codigo')
                ->where('examen_orden.estado_comision2',0)
                ->get();
            foreach ($ordenes_externos as $orden) {
                $detalles = Examen_Detalle::where('id_examen_orden', $orden->id)->get();
                $actualizado = false;
                if (count($detalles) > 0) {
                    $total_comision = 0;
                    foreach ($detalles as $detalle) {
                        $comision = Examen_Comision::where('id_examen', $detalle->id_examen)
                            ->where('id_doctor_externo', $orden->codigo)->first();

                        if (!is_null($comision)) {
                            $actualizado = true;

                            $valor_comision = $detalle->valor * ($comision->valor_comision / 100); //$comision->valor_comision es el porcentaje de la comision
                            $array_detalle = [
                                'valor_comision' => $valor_comision,
                            ];
                            $total_comision += $valor_comision;
                            $detalle->update($array_detalle);
                        }
                    }

                    if ($actualizado) {
                        $array_orden = [
                            'estado_comision2' => 1,
                            'total_comision' => $total_comision,
                        ];
                        $ordenM = Examen_Orden::find($orden->id);
                        if (!is_null($ordenM)) {
                            $ordenM->update($array_orden);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }


    function detalle_comisiones_externos_nuevo($ames, $codigo)
    {
        $año = substr($ames, 0, 4);
        $mes = substr($ames, 5);
        $doctor_externo = Labs_doc_externos::find($codigo);

        $ordenes_externos = Examen_Orden::where('examen_orden.estado', '1')
            ->select('examen_orden.codigo', 'examen_orden.anio', 'examen_orden.mes', 'examen_orden.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'examen_orden.valor', 'examen_orden.descuento_valor', 'examen_orden.fecha_orden', DB::raw('( ( examen_orden.valor - examen_orden.descuento_valor) * 0.1 ) as val'))
            ->join('labs_doc_externos as le', 'le.id', 'examen_orden.codigo')
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->where('examen_orden.anio', $año)
            ->where('examen_orden.mes', $mes)
            ->where('examen_orden.codigo', $codigo)
            ->whereNotNull('examen_orden.codigo')
            ->get();
        /*cambio jorge*/
        $this->masivo_valor_comision($ames, $codigo);
        /*fin cambio*/
        Excel::create('Detalle-' . $doctor_externo->apellido1 . '-' . $año . '-' . $mes, function ($excel) use ($ordenes_externos, $doctor_externo, $año, $mes) {

            $excel->sheet('Detalle', function ($sheet) use ($ordenes_externos, $doctor_externo, $año, $mes) {

                $letras = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];

                $fecha_d = date('Y/m/d');
                $i       = 3;
                $sheet->mergeCells('A1:H1');

                //$mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($doctor_externo, $año, $mes) {
                    // manipulate the cel
                    $cell->setValue('DETALLE DE COMISIONES '.$doctor_externo->apellido1.' '.$doctor_externo->nombre1.' '.$doctor_externo->nombre1.' '.$año.'-'.$mes );
                    $cell->setFontSize(16);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMISION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $total_valor = 0;
                $total_descuento = 0;
                $total_comision = 0;
                foreach($ordenes_externos as $orden){
                    $xorden = Examen_Orden::find($orden->id);
                    $total_valor += $orden->valor;
                    $total_descuento += $orden->descuento_valor;
                    $total = 0;
                    $detalles = Examen_Detalle::where('id_examen_orden',$orden->id)->get();
                    foreach ($detalles as $detalle) {
                        $total += $detalle->valor_comision;
                    }
                    $total_comision += $total;
                    //$total_comision += $orden->val;
                    $sheet->cell('A'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id );
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->fecha_orden);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C'.$i, function ($cell) use ($xorden) {
                        // manipulate the cel
                        $cell->setValue($xorden->doctor->apellido1.' '.$xorden->doctor->apellido2.' '.$xorden->doctor->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function ($cell) use ($xorden) {
                        // manipulate the cel
                        $cell->setValue($xorden->seguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->apellido1.' '.$orden->apellido2.' '.$orden->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G'.$i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H'.$i, function ($cell) use ($detalles) {
                        // manipulate the cel
                        //$cell->setValue($orden->val);
                        $total = 0;
                        foreach ($detalles as $detalle) {
                            $total += $detalle->valor_comision;
                        }
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $i++;

                }
                /* fin for */
                $sheet->cell('E'.$i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F'.$i, function ($cell) use ($total_valor) {
                    // manipulate the cel
                    $cell->setValue($total_valor);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('G'.$i, function ($cell) use ($total_descuento) {
                    // manipulate the cel
                    $cell->setValue($total_descuento);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('H'.$i, function ($cell) use ($total_comision) {
                    // manipulate the cel
                    $cell->setValue($total_comision);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }

    function excel_examen_comision()
    {
        ini_set('max_execution_time', 100);
        //$doctor_externo = Labs_doc_externos::find($codigo);
        $examenes = Examen::where('examen.estado', '1')->where('humanlabs','1')->where('nombre','not like', 'TOMA A DOMICILIO%')
            ->where('valor','>','0')
            ->select('examen.id', 'examen.nombre', 'examen.descripcion', 'examen.valor')
            // ->join('labs_doc_externos as le', 'le.id', 'examen_orden.codigo')
            // ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            // ->where('examen_orden.codigo', $codigo)
            // ->whereNotNull('examen_orden.codigo')
            ->get();
        
        /*cambio jorge*/
        //$this->masivo_valor_comision($ames, $codigo);
        /*fin cambio*/
        Excel::create('Lista_Examenes', function ($excel) use ($examenes) {
            $excel->sheet('Examenes', function ($sheet) use ($examenes) {
                // $letras = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];
                // $fecha_d = date('Y/m/d');
                $i       = 2;
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PVP PARTICULAR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HUMANLABS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PORCENTAJE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO DE COMISION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($examenes as $examen) {
                    // $id_examen         = $examen->id;
                    // $pvp         = $examen->valor;
                    // if($pvp>0){
                    //     $examen_comision = Examen_Comision::where('id_examen',$id_examen)->where('id_doctor',$codigo)->first();
                    //     if (!is_null($examen_comision)) {
                    //         $input = [
                    //             'valor_comision'  => 8,
                    //             'id_usuariomod'   => '0950457978',
                    //             'ip_modificacion' => 'CARGA_MASIVA',
                    //             'updated_at' => date('Y-m-d H:i:s'),
                    //         ];
                    //         $examen_comision->update($input);
                    //     } else {
                    //         $input = [
                    //             'id_examen' => $id_examen,
                    //             'id_doctor' => $codigo,
                    //             'valor_comision'  => 8,
                    //             'estado'          => 1,
                    //             'id_usuariocrea'  => '0950457978',
                    //             'id_usuariomod'   => '0950457978',
                    //             'ip_creacion'     => 'CARGA_MASIVA',
                    //             'ip_modificacion' => 'CARGA_MASIVA',
                    //         ];
                    //         Examen_Comision::create($input);
                    //     }
                    // }

                    //$total_comision += $orden->val;
                    $sheet->cell('A' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue($examen->id);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue($examen->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue($examen->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue('HUMANLABS');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue(0.08);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($examen) {
                        // manipulate the cel
                        $cell->setValue($examen->valor * 0.08);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('###%');
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $i++;
                }
            });
        })->export('xlsx');
    }

    function detalle_comisiones_staff($ames, $id_doctor)
    {
        $periodo =explode('-',$ames);
        $año = $periodo[1];
        $mes = $periodo[2];

        $doctor = User::find($id_doctor);

        $ordenes = Examen_Orden::where('examen_orden.estado_comision', '1')
            ->select('examen_orden.id_doctor_ieced', 'examen_orden.anio', 'examen_orden.mes', 'examen_orden.id', 'e.nombre', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ed.valor', 'ed.valor_descuento', 'examen_orden.fecha_orden', DB::raw('CASE WHEN ed.valor_descuento IS NULL THEN ( ed.valor * ed.p_comision ) ELSE ( ( ed.valor - ed.valor_descuento) * ed.p_comision ) END as val'))
            ->where('examen_orden.anio', $año)
            ->where('examen_orden.mes', $mes)
            ->where('examen_orden.id_doctor_ieced', $id_doctor)
            ->join('examen_detalle as ed', 'ed.id_examen_orden', 'examen_orden.id')
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('examen as e', 'e.id', 'ed.id_examen')
            ->where('ed.human_labs', '1')
            ->get();
        //dd($ordenes->first());    
        //$this->masivo_valor_comision_staff($ames, $id_doctor);
        Excel::create('Detalle-Staff-' . $doctor->apellido1 . '-' . $año . '-' . $mes, function ($excel) use ($ordenes, $doctor, $año, $mes) {

            $excel->sheet('Detalle', function ($sheet) use ($ordenes, $doctor, $año, $mes) {

                $letras = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];

                $fecha_d = date('Y/m/d');
                $i       = 3;
                $sheet->mergeCells('A1:G1');

                //$mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($doctor, $año, $mes) {
                    // manipulate the cel
                    $cell->setValue('DETALLE DE COMISIONES ' . $doctor->apellido1 . ' ' . $doctor->apellido2 . ' ' . $doctor->nombre1 . ' ' . $año . '-' . $mes);
                    $cell->setFontSize(16);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMISION');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $total_valor = 0;
                $total_descuento = 0;
                $total_comision = 0;

                foreach ($ordenes as $orden) {

                    $total_valor += $orden->valor;
                    $total_descuento += $orden->valor_descuento;
                    $total_comision += $orden->val;

                    $sheet->cell('A' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->fecha_orden);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->apellido1 . ' ' . $orden->apellido2 . ' ' . $orden->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->valor_descuento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $i++;
                }

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) use ($total_valor) {
                    // manipulate the cel
                    $cell->setValue($total_valor);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('F' . $i, function ($cell) use ($total_descuento) {
                    // manipulate the cel
                    $cell->setValue($total_descuento);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                $sheet->cell('G' . $i, function ($cell) use ($total_comision) {
                    // manipulate the cel
                    $cell->setValue($total_comision);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#FFF000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }

    function masivo_valor_comision_staff($ames, $codigo)
    {
        try {
            DB::beginTransaction();
            $periodo =explode('-',$ames);
            $año = $periodo[1];
            $mes = $periodo[2];
            $actualizado = false;
            $doctor = User::find($codigo);
            $ordenes_externos = Examen_Orden::where('examen_orden.estado', '1')
                ->select('examen_orden.codigo', 'examen_orden.anio', 'examen_orden.mes', 'examen_orden.id', 'u.id as id_doctor', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'examen_orden.valor', 'examen_orden.descuento_valor', 'examen_orden.fecha_orden')
                ->join('users as u', 'u.id', 'examen_orden.id_doctor_ieced')
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->where('examen_orden.anio', $año)
                ->where('examen_orden.mes', $mes)
                ->where('examen_orden.estado_comision2', 0)
                ->get();
            //dd($ordenes_externos);
            foreach ($ordenes_externos as $orden) {
                $detalles = Examen_Detalle::where('id_examen_orden', $orden->id)->get();
                $actualizado = false;
                if (count($detalles) > 0) {
                    $total_comision = 0;
                    foreach ($detalles as $detalle) {
                        $comision = Examen_Comision::where('id_examen', $detalle->id_examen)
                            ->where('id_doctor', $orden->id_doctor)->first();

                        if (!is_null($comision)) {
                            $actualizado = true;

                            $valor_comision = $detalle->valor * ($comision->valor_comision / 100); //$comision->valor_comision es el porcentaje de la comision
                            $array_detalle = [
                                'valor_comision' => $valor_comision,
                            ];
                            $total_comision += $valor_comision;
                            $detalle->update($array_detalle);
                        }
                    }

                    if ($actualizado) {
                        $array_orden = [
                            'estado_comision2' => 1,
                            'total_comision' => $total_comision,
                        ];
                        $ordenM = Examen_Orden::find($orden->id);
                        if (!is_null($ordenM)) {
                            $ordenM->update($array_orden);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
