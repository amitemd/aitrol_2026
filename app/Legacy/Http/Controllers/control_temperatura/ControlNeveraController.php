<?php

namespace Sis_medico\Http\Controllers\control_temperatura;

use Sis_medico\Http\Controllers\Controller;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_acreedores;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Forma_de_pago;
use Sis_medico\Pais;
use Sis_medico\Hospital;
use Sis_medico\Sala;
use Sis_medico\AgendaQ;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\ControlTemperatura;
use Sis_medico\ControlNevera;
use Sis_medico\ControlAmbiente;
use Sis_medico\Empresa;
use Sis_medico\Agenda_Permiso;
use Sis_medico\ControlTemparea;
use Sis_medico\TemperaturaNevera;
use Sis_medico\RegistroNevera;
use Sis_medico\Ct_Nomina;
use Sis_medico\Bodegas_Nevera;
use Sis_medico\Ambiente_Nevera;

class ControlNeveraController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24, 4, 6,7,6,10)) == false) 
        {
            return true;
        }
    }

    public function index(Request $request)
    {
        //sacar datos hacer
        $datos = RegistroNevera::find($request->id);
        $fecha_desde  = date('Y-m-d');
        $fecha_hasta  = date('Y-m-d');
        $empresa      = Empresa::where('id', $datos->id_empresa)->first();
        $controlarea = ControlTemparea::find($datos->id_control_temp);
        $temperatura_neveras = TemperaturaNevera::all();
        $registro_nevera = RegistroNevera::whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('id_control_temp', $datos->id_control_temp)->where("id_control_temperatura", $datos->id_control_temperatura)->paginate(20);
        return view('control_temperatura/control_nevera/index', ['id_control_temperatura' => $datos->id_control_temperatura, 'temperatura_neveras' => $temperatura_neveras, 'controlarea' => $controlarea, 'id_lugar' => $datos->id_control_temp, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'registro_nevera' => $registro_nevera]);
    }

    public function index_registro(Request $request)
    {


        $fecha_desde  = date('Y-m-d');
        $fecha_hasta  = date('Y-m-d');
        $empresa      = Empresa::where('id', $request->id_empresa)->first();
        $controlarea = ControlTemparea::find($request->tipo);
        $temperatura_neveras = TemperaturaNevera::all();
        $registro_nevera = RegistroNevera::whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('id_control_temp', $request->tipo)->where("id_control_temperatura", $request->id)->paginate(20);

        return view('control_temperatura/control_nevera/index', ['id_control_temperatura' => $request->id, 'temperatura_neveras' => $temperatura_neveras, 'controlarea' => $controlarea, 'id_lugar' => $request->tipo, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'registro_nevera' => $registro_nevera]);
    }


    public function eleccion_tipo_control(Request $request)
    {

        return  $this->create($request);
    }

    public function eleccion_tipo2_control(Request $request)
    {
        $nevera = new ControlNevera;

        return  $nevera->index($request);
    }

    public function create(Request $request)
    {
        // el id me permite saber si es nevera o ambiente
        $controlugar = ControlTemparea::find($request['tipo']);
        $temperatura_neveras = TemperaturaNevera::all();
        $empresa      = Empresa::where('id', $request->empresa)->first();
        $bodegas_nevera = Bodegas_Nevera::where("id_empresa", $request->empresa)->where("id_temparea",$request['tipo'])->where("id_control_temperatura",$request['id'])->get();
        $ambiente = Ambiente_Nevera::where("estado", 1)->get();
        return view('control_temperatura/control_nevera/create', ['id_n_b' => $request->id, 'ambiente' => $ambiente, 'bodegas_nevera' => $bodegas_nevera, 'controlugar' => $controlugar, 'temperatura_neveras' => $temperatura_neveras, 'empresa' => $empresa]);
    }
    public function areas()
    {
        $areas = ControlTemperatura::all();
        return view('control_temperatura/control_nevera/areas', ['areas' => $areas]);
    }

    public function guardar(Request $request)
    {


        $primer_horario = date("h:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $registro = RegistroNevera::create([
            'id_control_temp'    => $request['id_area'],
            'primer_horario'         => date('H:i:s'),
            'temp_primerhorario' => $request['temp_primerhorario'],
            'observacion'     => $request['observaciones'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'responsable'     => $idusuario,
            'id_empresa'      => $request['id_empresa'],
            'id_nevera'      => $request['nevera'],
            'id_control_temperatura' => $request['id_bo_ne'],
        ]);

        return json_encode(['status' => true, 'registro' => $registro]);
    }

    public function update_nevera(Request $request)
    {

        try {

            $update = RegistroNevera::find($request->id);

            $update->temp_seghorario = $request->temperatura_se;
            $update->segundo_horario = date('H:i:s');
            $update->check_s = 1;
            $update->save();

            return ['status' => true, 'msj' => 'Actualización Correcta'];
        } catch (\Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public function buscador(Request $request)
    {
        
        $fecha_desde  = $request->desde;
        $fecha_hasta  = $request->hasta;
        $empresa      = Empresa::where('id', $request->id_empresa)->first();
        $registro_nevera = RegistroNevera::whereBetween('created_at', [$request->desde . ' 00:00:00', $request->hasta . ' 23:59:59'])->where('id_control_temp', $request['id_lugar'])->paginate(10);
        $controlarea = ControlTemparea::find($request->id_lugar);
        $temperatura_neveras = TemperaturaNevera::all();
        return view('control_temperatura/control_nevera/index', ['id_control_temperatura'=>$request['id_control_temperatura'],'id_lugar' => $request->id_lugar,'empresa'=>$empresa,'controlarea'=>$controlarea,'registro_nevera' => $registro_nevera, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'temperatura_neveras' => $temperatura_neveras]);
    }

    public function excel_temperatura(Request $request)
    {


        $posicion = array(
            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
            "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
            "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR"
        );
        $anio_semana = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];


        // cambiar con el request 
        $empresa      = Empresa::where('id', $request->id_empresa)->first();
        $temperaturas  =  TemperaturaNevera::where("estado", 1)->where("tipo", $request->tipo)->get();
        $area = ControlTemparea::find($request->id_lugar);

        //cambiar fecha con el request['fecha']

        $rows = RegistroNevera::whereBetween('created_at', [$request->desde . ' 00:00:00', $request->hasta . ' 23:59:59'])->where("id_control_temp", $request->id_lugar)->where("id_control_temperatura", $request->id_control_temperatura)->where("estado", 1)->where("id_nevera", $request->id_nevera)->get();
        $fecha = date("Y-m-d");
        $tipor = $request->tipo;
        Excel::create('CONTROL DE TEMPERATURA', function ($excel) use ($tipor, $area, $posicion, $empresa, $temperaturas, $anio_semana, $fecha, $rows,$request) {
            $excel->sheet('PONDERACIÓN', function ($sheet) use ($tipor, $area, $posicion, $empresa, $temperaturas, $anio_semana, $fecha, $rows,$request) {

                $logo = base_path() . '/storage/app/logo/' . $empresa->logo;
                $sheet->mergeCells('E1:G1');
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                $objDrawing->setCoordinates('E1');
                $objDrawing->setHeight(180);
                $objDrawing->setWidth(160);
                $objDrawing->setWorksheet($sheet);

                $tit = $tipor === '1' ? 'Nevera' : 'Ambiente';
                $sheet->mergeCells('I4:O4');
                $sheet->cell('I4', function ($cell) use($tit){
                    $cell->setValue('CONTROL DE TEMPERATURA - '.$tit);
                    $cell->setFontColor('#010101');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('R3:T3');
                $sheet->cell('R3', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontColor('#010101');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });


                $sheet->mergeCells('A9:D9');
                $sheet->cell('A9', function ($cell) use ($area) {
                    $cell->setValue($area->nombre);
                    $cell->setBackground('#99CCFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('AI9:AT10');
                $sheet->cell('B9', function ($cell) {
                    $cell->setValue('ÁREA: COMPRAS');
                    $cell->setBackground('#99CCFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });



                $fechaDay = date('m', strtotime($fecha));
                $newL = '';
                if ($fechaDay[0] === '0') {
                    $newL = str_replace('0', '', $fechaDay);
                }
                $anio = intval(date("m",strtotime(($request->desde))));
                $anio1 = intval(date("m",strtotime(($request->hasta))));
                $nt  = strtolower(substr($anio_semana[$anio], 0, 3));
                $nt1  = strtolower(substr($anio_semana[$anio1], 0, 3));
                $day = date("d",strtotime($request->desde));
                $day1 = date("d",strtotime($request->hasta));
                $fecha_completa = "$nt -  $day hasta $nt1 - $day1";
                $sheet->mergeCells('I9:O9');
                $sheet->cell('I9', function ($cell) use ($fecha_completa) {
                    $cell->setValue("$fecha_completa");
                    $cell->setBackground('#99CCFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //ASDADSASDAS
                $sheet->mergeCells('A12:S12');
                $sheet->cell('A12', function ($cell) {
                    $cell->setBackground('#99CCFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //variable
                $tias = $tipor  === '1' ? 'A13:A32' : 'A13:A30';
                $sheet->mergeCells($tias);
                $sheet->cell('A13', function ($cell) {
                    $cell->setValue('Escala de temperatura');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $arrayMod = [];

                for ($m = 0; $m < count($temperaturas); $m++) {
                    array_push($arrayMod, $temperaturas[$m]['temperatura']);
                }


                array_splice($arrayMod, 0, 0, '→');

                array_splice($arrayMod, count($arrayMod), 0, array('→', 'HORA:'));



                $contador = 13;
                for ($i = 0; $i < count($arrayMod); $i++) {
                    $sheet->cell("B$contador", function ($cell) use ($arrayMod, $i) {
                        $cell->setValue($arrayMod[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('12');
                    });
                    for ($j = 0; $j < $contador; $j++) {
                        for ($k = 13; $k <= 31; $k++) {
                            $ite = 2 + $j;
                            if ($ite <= 18) {
                                $sheet->cell($posicion[2 + $j] . $k, function ($cell) {
                                    $cell->setValue('');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setFontSize('12');
                                });
                            }
                        }
                    }
                    $contador++;
                }

                $arrayP = [];
                for ($g = 0; $g < count($rows); $g++) {
                    array_push($arrayP, [$rows[$g]['primer_horario'] != null ? date("H:i", strtotime($rows[$g]['primer_horario'])) : '', $rows[$g]['segundo_horario'] !== null ?  date("H:i", strtotime($rows[$g]['segundo_horario'])) : '', date("Y-m-d", strtotime($rows[$g]['created_at'])), $rows[$g]->temperatura_uno !== null ? $rows[$g]->temperatura_uno->temperatura : '', $rows[$g]->temperatura_dos !== null ? $rows[$g]->temperatura_dos->temperatura : '']);
                }
                //cambiar variable
                $comienzo =  $tipor  === '1' ? 32 : 30;
                $fechas_comienzo =  $tipor  === '1' ?  33 : 31;
                // 0   1  2 3  
                // "A", "B", "C", "D", "E", "F", "G", "H"

                $contador = 0;
                $contador1 = 0;
                for ($i = 0; $i < count($arrayP); $i++) {
                    $this->x_check($arrayP[$i], $posicion, $contador + 2, $sheet, $temperaturas);
                    for ($j = 0; $j < count($arrayP[$i]); $j++) {

                        if ($j <= 1) {
                            $sheet->cell($posicion[$contador + 2] . $comienzo, function ($cell) use ($i, $j, $arrayP) {
                                $cell->setValue($arrayP[$i][$j]);
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle($posicion[$contador + 2] . $comienzo)->getAlignment()->setTextRotation(90);
                            $sheet->getStyle($posicion[$contador + 2] . $comienzo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                            $contador++;
                        } elseif ($j === 2) {
                            $contp = $contador1 + 3;
                            $sheet->mergeCells($posicion[$contador1 + 2] . $fechas_comienzo . ':' . $posicion[$contp] . $fechas_comienzo);
                            $sheet->cell($posicion[$contador1 + 2] . $fechas_comienzo, function ($cell) use ($i, $j, $arrayP) {
                                $cell->setValue($arrayP[$i][$j]);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $contador1++;
                        }
                    }
                    $contador1++;
                }
                $sheet->getStyle("A13:A32")->getAlignment()->setTextRotation(90);
                $sheet->getStyle("A13:A32")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            });



            $arrayExcluidos = [];
            for ($i = 0; $i < count($posicion); $i++) {
                $excel->getActiveSheet()->setShowGridlines(true)->getColumnDimension($posicion[$i])->setWidth(8)->setAutosize(false);
            }
        })->export('xlsx');
    }

    public function x_check($array, $posicion, $contador, $sheet, $temperaturas)
    {

        unset($array[0]);
        unset($array[1]);
        unset($array[2]);

        $arr = array_values($array);

        $art = [];
        $it = 14;
        for ($i = 0; $i < count($temperaturas); $i++) {
            $art[$it] = $temperaturas[$i]->temperatura;
            $it++;
        }
        $contadorS = 0;

       
        for ($p = 0; $p < count($arr); $p++) {

            $rt = '';
            if($arr[$p] !== ''){

                $rt = array_search($arr[$p], $art);
            }
            



            if ($rt !== false && $rt !== '') {
                
                $sheet->cell($posicion[$contador+$contadorS] . $rt, function ($cell) use ($i) {
                    $cell->setValue("X");
                    
                    $cell->setFontSize('12');
                });
                $contadorS ++;
            }
        }
    }


    public function index_contemp(Request $request)
    {


        $empresa = Empresa::where('prioridad', 1)->first();
        $lugar_control = ControlTemperatura::where("id_empresa", $empresa->id)->get();
        return view('control_temperatura/control_nevera/index_contemp', ['lugar_control' => $lugar_control, 'empresa' => $empresa]);
    }



    public function index_labs()
    {

        $empresa = Empresa::where("prioridad_labs", 1)->first();
        $control = ControlTemperatura::where("estado", 1)->get();
        $lugar_control = ControlTemperatura::where("id_empresa", $empresa->id)->get();
        $ambiente = Ambiente_Nevera::where("estado", 1)->get();
        return view("control_temperatura/control_nevera/index_labs", ['ambiente' => $ambiente, 'empresa' => $empresa, 'control' => $control, 'lugar_control' => $lugar_control]);
    }

    public function reporte_excel(Request $request)
    {

        $nevera = Bodegas_Nevera::all();
        return view("control_temperatura/control_nevera/reporte", ['nevera' => $nevera, 'id_lugar' => $request->id_lugar, 'id_control_temperatura' => $request->id_control_temperatura, 'empresa' => $request->empresa]);
    }
    public function excel_buscador(Request $request)
    {

        $this->excel_temperatura($request);
    }
}
