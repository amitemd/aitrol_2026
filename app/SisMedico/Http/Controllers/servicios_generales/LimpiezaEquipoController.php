<?php

namespace Sis_medico\Http\Controllers\servicios_generales;
 
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Agenda;
use Sis_medico\Equipo_Historia;
use Sis_medico\Control_Equipo;
use Sis_medico\Detalle_Equipo;
use Sis_medico\Empresa;
use Sis_medico\Equipo;
use Sis_medico\EquipoUsado;
use Sis_medico\Http\Controllers\Controller;
use  Sis_medico\Http\Controllers\servicios\ServiciosIecedController;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\Examen;
use Sis_medico\Pentax;
use Sis_medico\Apps_Agenda;
class LimpiezaEquipoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $sala = Sala::where('id_hospital', 2)
            ->where('estado', 1)
            ->where('proc_consul_sala', 1)
            ->get();
        $control     = DB::table('control_equipo')->paginate(10);
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');
        return view('servicios_generales.limpieza_equipos.index', ['sala' => $sala, 'control' => $control, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function registro($id, $id_sala,$id_pentax)
    {
        $paciente = Paciente::all();
        $equipo   = Equipo::all();
        $pro      = Procedimiento::all();

        $pentaxId = Pentax::find($id_pentax);
        $equiposHistoria = Equipo_Historia::where("hcid",$pentaxId->hcid)->get();
        
      
      
        return view('servicios_generales.limpieza_equipos.registro', ['equiposHistoria'=>$equiposHistoria,'id_pentax'=>$id_pentax,'id_sala' => $id_sala, 'paciente' => $paciente, 'equipo' => $equipo, 'pro' => $pro, 'id' => $id]);
    }

    public function autocomplete(Request $request)
    {

        $nombre       = $request['term'];
        $data         = array();
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', paciente.nombre1, paciente.nombre2, paciente.apellido1, paciente.apellido2) as completo, id
        FROM `paciente`
        WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'
        ";
        $paciente = DB::select($query);
        foreach ($paciente as $cliente) {
            $data[] = array('value' => $cliente->completo, 'id' => $cliente->id);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $control    = Control_Equipo::create([
            'id_paciente'         => $request['paciente'],
            'fecha_antes'         => $request['fecha'],
            'hora'                => $request['hora'],
            'prueba_despues'      => $request['prueba_desp'],
            'estado_equipo'       => $request['estado_equipo'],
            'hora_esterilizacion' => $request['hora_ester'],
            'prueba_antes'        => $request['pruebas'],
            'observaciones'       => $request['obs'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'sala'                => $request['sala_id'],
            'estado'              => 1,
            'id_pentax'           => $request['id_pentax']
        ]);
        for ($i = 0; $i < count($request['states']); $i++) {

            Detalle_Equipo::create([
                'id_control_equipo' => $control->id,
                'id_equipo'         => $request['states'][$i],
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ]);
        }

        for ($i = 0; $i < count($request['proc']); $i++) {
            EquipoUsado::create([
                'id_control_equipo' => $control->id,
                'id_procedimiento'  => $request['proc'][$i],
            ]);
        }
        return json_encode('ok');
    }
    public function marca(Request $request)
    {
        //dd($request->states);
        $global = [];
        if(!is_null($request->states)){
            foreach($request->states as $id){
                // dd($value);
                 $equipo = Equipo::where('id', $id)->first();
                 $serie  =  $equipo->serie;
                 $marca  = $equipo->marca;
                 $modelo = $equipo->modelo;
                 $conj   = $marca . ' ' . $modelo;
                 $data = [
                     "serie" => $serie,
                     "marca" => $marca,
                     "modelo" => $modelo,
                     "conj" => $conj
                 ];
                 array_push($global, $data);
             }
        }
        
        //dd($global);
        
        return json_encode($global);
    }

    public function editar($id)
    {
        $result  = Control_Equipo::where('id', $id)->first();
        $pro     = Procedimiento::where('estado', '1')->get();
        $equipo  = Equipo::where('estado', '1')->get();
        $datalle = Detalle_Equipo::where('id_control_equipo', $result->id)->get();
        $proTodo = EquipoUsado::where('id_control_equipo', $result->id)->get();
        return view('servicios_generales.limpieza_equipos.editar', ['proTodo' => $proTodo, 'detalle' => $datalle, 'result' => $result, 'pro' => $pro, 'equipo' => $equipo, 'id' => $id]);
    }

    public function actualizar(Request $request)
    {
        //dd($request->all());
        $ip_cliente                   = $_SERVER["REMOTE_ADDR"];
        $idusuario                    = Auth::user()->id;
        $control                      = Control_Equipo::where('id', $request['id'])->first();
        $control->id_paciente         = $request['paciente'];
        $control->fecha_antes         = $request['fecha'];
        $control->hora                = $request['hora'];
        $control->prueba_despues      = $request['prueba_desp'];
        $control->estado_equipo       = $request['estado_equipo'];
        $control->hora_esterilizacion = $request['hora_ester'];
        $control->prueba_antes        = $request['pruebas'];
        $control->observaciones       = $request['obs'];
        $control->ip_creacion         = $ip_cliente;
        $control->ip_modificacion     = $ip_cliente;
        $control->id_usuariocrea      = $idusuario;
        $control->id_usuariomod       = $idusuario;
        $control->save();

        if ($request->has("equipo")) {
            $variable = Detalle_Equipo::where('id_control_equipo', $control->id);
            //dd($eliminar);
            if (!is_null($variable)) {
                $variable->delete();
            }

            if (count($request['equipo']) > 0) {
                foreach ($request['equipo'] as $i => $v) {
                    // dd($request);
                    $data2 = array(
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_control_equipo' => $control->id,
                        'id_equipo'         => $request['equipo'][$i],

                    );
                    //dd($data2);

                    Detalle_Equipo::insert($data2);
                }
            }
        }
        if ($request->has("proc")) {
            $variable = EquipoUsado::where('id_control_equipo', $control->id);
            if (!is_null($variable)) {
                $variable->delete();
            }
            if (count($request['proc']) > 0) {
                foreach ($request['proc'] as $i => $v) {
                    // dd($request);
                    $data2 = array(
                        'id_control_equipo' => $control->id,
                        'id_procedimiento'  => $request['proc'][$i],

                    );
                    //dd($data2);

                    EquipoUsado::insert($data2);
                }
            }
        }

        return json_encode('ok');
    }

    public function buscar_fecha(Request $request)
    {

        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];
        $control     = Control_Equipo::whereBetween('created_at', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59'])->paginate(10);
        return view('servicios_generales.limpieza_equipos.index', ['control' => $control, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function excel(Request $request)
    {
        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];
        $id_empresa  = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = '0992704152001';
        }
        $tipo    = $request['tipo'];
        $empresa = Empresa::where('id', $id_empresa)->first();
        $control = array();
        $area    = Sala::where('id', $request['area'])->first();
        if (!is_null($request['area'])) {
            $control = Control_Equipo::whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('sala', $request['area'])->get();
            //dd($control);
        } else {
            $control = Control_Equipo::whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();
        }

        Excel::create('Limpieza y Desinfección de Equipos Medicos Vers. 0.1', function ($excel) use ($control, $empresa, $tipo, $area) {

            $excel->sheet('REPORTE LIMPIEZA', function ($sheet) use ($empresa, $control, $tipo, $area) {

                $sheet->mergeCells('C1:N1');
                $sheet->cell('C1', function ($cell) use ($empresa, $tipo) {
                    if (!is_null($empresa)) {
                        $cell->setValue($tipo == 1 ? $empresa->nombrecomercial . ':' . $empresa->id : 'CARLOS ROBLES MEDRANDA');
                    }
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:B1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(220);
                    $objDrawing->setWidth(120);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:N2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    if (!is_null($empresa)) {
                        $cell->setValue('CONTROL DE DESINFECCIÒN DE EQUIPOS MEDICOS');
                    }
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O1:O2');
                $sheet->cell('O1', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O3:O3');
                $sheet->cell('O3', function ($cell) use ($area) {
                    $cell->setValue($area->nombre_sala !== null ? $area->nombre_sala : '' );
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:k3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue('Antes de usar el equipo medico');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L3:N3');
                $sheet->cell('L3', function ($cell) {
                    $cell->setValue('Despues de usar el equipo');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O4:O6');
                $sheet->cell('O4', function ($cell) {
                    $cell->setValue('OBSERVACIONES/REPORTADO A');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A4:A6');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B4:B6');
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C4:C6');
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO MEDICO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D4:D6');
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA PROCEDIMIENTO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E4:E6');
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#F79646');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F4:F6');
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EQUIPO MEDICO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G4:G6');
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MODELO / MARCA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H4:H6');
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERIE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I4:I6');
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SE REALIZA PRUEBA DE FUGA DESPUES DE ESTERILIZAR EL EQUIPO? ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J4:J6');
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA RESPONSABLE QUIEN REALIZA PRUEBA FUGA DESPUES ESTERILIZAR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K4:K6');

                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO DEL EQUIPO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#F79646');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('L4:L6');

                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA ESTERILIZACION EQUIPO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#008080');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('M4:M6');

                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SE REALIZA PRUEBA DE FUGA ANTES DE ESTERILIZAR EL EQUIPO? ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#008080');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('N4:N6');

                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA RESPONSABLE ESTERILIZACION Y PRUEBA FUGA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#008080');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i        = 7;
                $contador = 1;
                foreach ($control as $value) {
                    $paciente = Paciente::where('id', $value->id_paciente)->first();
                    $user     = User::where('id', $value->id_paciente)->first();
                    $equipo   = Detalle_Equipo::where('id_control_equipo', $value->id)->get();
                    $procedi  = EquipoUsado::where('id_control_equipo', $value->id)->get();

                    $sheet->cell('A' . $i, function ($cell) use ($value, $contador) {
                        $cell->setValue($contador);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        $cell->setValue(substr($value->created_at, 0, 11));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($procedi) {
                        $arr = array();

                        foreach ($procedi as $val) {

                            array_push($arr, $val->nombre->nombre);
                        }
                        $separated = implode(",", $arr);
                        $cell->setValue($separated);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->hora);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($user) {
                        if (!is_null($user)) {
                            $cell->setValue($user->nombre1 . ' ' . $user->apellido1 . ' ' . $user->apellido2);
                        } else {
                            $cell->setValue('');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($equipo) {
                        $array  = array();
                        $array1 = array();
                        foreach ($equipo as $val) {
                            if (substr($val->usado->nombre, 0, 8) == 'EQUIPO :') {
                                array_push($array, substr($val->usado->nombre, 9, 35));
                            } elseif (substr($val->usado->nombre, 0, 9) == 'EQUIPO : ') {

                                array_push($array, substr($val->usado->nombre, 10, 35));
                            } elseif (substr($val->usado->nombre, 0, 7) == 'EQUIPO:') {

                                array_push($array, substr($val->usado->nombre, 8, 35));
                            }
                        }
                        for ($i = 0; $i < count($array); $i++) {
                            $ok = rtrim($array[$i]);
                            array_push($array1, $ok);
                        }
                        $separated = implode(",", $array);
                        $cell->setValue($separated);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($equipo) {
                        $arr = array();
                        foreach ($equipo as $val) {
                            array_push($arr, $val->usado->modelo);
                        }
                        $separated = implode(",", $arr);
                        $cell->setValue($separated);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($equipo) {
                        $arr = array();
                        foreach ($equipo as $val) {
                            array_push($arr, $val->usado->serie);
                        }
                        $separated = implode(",", $arr);
                        $cell->setValue($separated);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        $t = "";
                        if ($value->prueba_despues == 1) {
                            $t = "Si";
                        } else {
                            $t = "No";
                        }
                        $cell->setValue($t);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        if (!is_null($value)) {
                            $cell->setValue($value->usuario->nombre1 . ' ' . $value->usuario->apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {

                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->estado_equipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->hora_esterilizacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {

                        $g = '';
                        if ($value == '0') {
                            $g = 'no';
                        } else {
                            $g = 'si';
                        }
                        $cell->setValue($g);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->usuario->nombre1 . ' ' . $value->usuario->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $contador++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getStyle('A4:A60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B4:B60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C4:C60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D4:D60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E4:E6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F4:F60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G4:G60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H4:H6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I4:I6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('J4:J60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('K4:K60')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L4:L6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O4:O6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('M4:M6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N4:N6')->getAlignment()->setWrapText(true);

            $excel->getActiveSheet()->getStyle('C7')->getFont()->setSize(10);
        })->export('xlsx');
    }

    public function buscar_sala(Request $request)
    {

        //dd($request->all());

        $pacientes = Agenda::where("agenda.estado_cita", "4")
            ->where('agenda.proc_consul', '1')
            ->whereBetween('agenda.fechaini', [$request['fecha'] . ' 00:00', $request['fecha'] . ' 23:59'])
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('pentax as px', 'agenda.id', 'px.id_agenda')
            ->where('px.id_sala', $request['sala_id'])
            ->select('agenda.*', 'px.id as id_pentax', 'px.id_agenda as agenda_pentax', 'px.id_sala as pentax_sala')->get();

        return view('servicios_generales.limpieza_equipos.vista_nueva', ['pacientes' => $pacientes, 'sala_id' => $request['sala_id']]);
    }

    public function vistaexcel()
    {
        $sala = Sala::where('id_hospital', 2)
            ->where('estado', 1)
            ->where('proc_consul_sala', 1)
            ->get();

        return view('servicios_generales.limpieza_equipos.reportevista', ['sala' => $sala]);
    }

    public function excel_index(){

      $excel = Examen::paginate(10);

      return view('servicios_generales.mantenimiento_excel.excel_index', ['excel' => $excel]);
    }

    public function excel_actualizar(Request $request){

      $excel = Examen::find($request['id']);

      return view('servicios_generales.mantenimiento_excel.excel_actualizar', ['examen' => $excel]);
    }

    public function excel_update (Request $request){

      try {
          $excel = Examen::find($request['id_examen']);
          $excel->nombre_largo = $request['nombre_largo'];
          $excel->sugerencia = $request['sugerencia'];
          $excel->save();
          return json_encode('ok');

      } catch (\Exception $e) {
           return json_encode('error');
      }
    }


    public function arreglar_masivo(){


    Excel::filter('chunk')->load('public/nuevoexcel.xlsx')->chunk(250, function ($results) {

            foreach($results as $row)
            {
                $examen = Examen::where('id',intval($row['id']))->first();
                if(!is_null($examen)){
                     $examen->nombre_largo = $row['sugerencia_de_otros_nombres'];
                     $examen->sugerencia = $row['sugerencia'];
                     $examen->save();
                }
            }
    });


    }

    public function buscar (Request $request){

      $excel = Examen::where('nombre','like', '%' . $request['nombre'] . '%')->paginate(10);
      return view('servicios_generales.mantenimiento_excel.excel_index', ['excel' => $excel]);
    }

    public function reporte_apps(Request $request){
        $empresa = $request->session()->get('id_empresa');
        $request['empresa'] = $empresa;
      //dd($request->all());
      $titulos = array("FECHA","CEDULA","PACIENTE","COMPROBANTE ELECTRÓNICO","TIPO","CANTIDAD","PORTAL","ID","REFERIDO");
      $titulos1 = array("FECHA COMPRA","FECHA CONSULTA","HORA","CEDULA","PACIENTE","TIPO","PAYMENT","NUMERO DE FACTURA");
      $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
      $apps = DB::table('user_membresia')
      ->join('membresia','membresia.id','user_membresia.membresia_id')
      ->where('membresia.empresa_id','0993075000001')
      ->where('user_membresia.estado','1')->select('*')->whereBetween('user_membresia.fecha_compra', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:00'])->get();

      $consultas = DB::table('apps_agenda')->join('agenda','agenda.id','apps_agenda.id_agenda')
                      ->select('agenda.*','apps_agenda.request_id','apps_agenda.fecha','apps_agenda.ride','apps_agenda.tipo','apps_agenda.payment')->whereBetween('apps_agenda.fecha', [$request['fecha'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:00'])->where('agenda.estado',1)->get();

      Excel::create('REPORTE APLICACIÓN', function ($excel) use ($titulos, $posicion, $apps, $request,$consultas,$titulos1) {
                $excel->sheet('Reporte de Aplicación', function ($sheet) use ($titulos, $posicion, $apps, $request,$consultas,$titulos1) {
                    $sheet->mergeCells('A1:J1');
                    $sheet->cell('A1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('COMPRAS DE MEMBRESIA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $comienzo = 2; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/
                    $service = new ServiciosIecedController;

                    foreach ($apps as $soporte) {
                          $request->request->add(['requestId'=> $soporte->requestId]);
                          $numeroFact = 0 ;
                          $datos_excel = array();
                          $nombrePaciente = User::where('id',$soporte->user_id)->first();
                          $nombreReferido = User::where('id',$soporte->referido)->first();
                          $nombres = '';
                          if(!is_null($nombreReferido)){
                            $nombres = $nombreReferido->nombre1.' '.$nombreReferido->apellido1;
                          }
                          if(!is_null($soporte->requestId)){
                            $numeroFact = $service->getPaymentInformationRequest($request);
                              $numeroComprobante = '';
                              $details = '';
                              $subtotal = '';
                            if($numeroFact['comprobante'] != ''){
                                $numeroComprobante = $numeroFact['comprobante'];
                                $details = $numeroFact['details']['paymentMethod'];
                                $subtotal = $numeroFact['details']['subtotal'];
                            }
                              array_push($datos_excel,substr($soporte->fecha_compra, 0, 11),$nombrePaciente->id,$nombrePaciente->nombre1.' '.$nombrePaciente->nombre2.' '.$nombrePaciente->apellido1.' '.$nombrePaciente->apellido2,$numeroComprobante,$details,number_format(intval($subtotal), 2, '.', ' '),"APP",$soporte->requestId,$nombres);

                          }else{
                              array_push($datos_excel,substr($soporte->fecha_compra, 0, 11),$nombrePaciente->id,$nombrePaciente->nombre1.' '.$nombrePaciente->nombre2.' '.$nombrePaciente->apellido1.' '.$nombrePaciente->apellido2,'','','',"RECEPCION",$soporte->id_orden,$nombres);
                          }

                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                    $comienzo +=2;
                    $sheet->mergeCells('A'.$comienzo.':'.'J'.$comienzo);
                    $sheet->cell('A'.$comienzo, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('CONSULTAS AGENDADAS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                      $comienzo ++;
                    for ($i = 0; $i < count($titulos1); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos1, $i) {
                            $cell->setValue($titulos1[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    foreach ($consultas as $soporte) {
                        //$request->merge(['requestId' => 10126]);
                        $request->request->add(['requestId'=> $soporte->request_id]);
                        $request['requestId'] = $soporte->request_id;
                        $numeroFact = $service->getPaymentInformationRequest($request);
                        //dd($numeroFact);
                        $datos_excel = array();
                        $nombrePaciente = User::where('id',$soporte->id_paciente)->first();
                        $agenda = Agenda::where('id',$soporte->id)->first();

                        array_push($datos_excel,substr($agenda->created_at, 0, 11),substr($agenda->fechaini,0,11),substr($agenda->fechaini, 11, 18),$nombrePaciente->id,$nombrePaciente->nombre1.' '.$nombrePaciente->nombre2.' '.$nombrePaciente->apellido1.' '.$nombrePaciente->apellido2,$soporte->tipo,$soporte->payment,$numeroFact['comprobante']);


                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                });
            })->export('xlsx');

    }


}
