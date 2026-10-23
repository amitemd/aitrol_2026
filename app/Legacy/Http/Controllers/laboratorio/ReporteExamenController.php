<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Carbon\Carbon;
use Cookie;
use DateInterval;
use DateTime;
use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Mail;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_Labs;
use Sis_medico\Examen_Agrupador_Sabana;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Resultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

class ReporteExamenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexReporte(Request $request)
    {
        ini_set('max_execution_time',300);
        $pacientes   = Paciente::all();
        $paciente    = Paciente::find($request['paciente']);
        $parametros   = Examen_Parametro::where('estado',1)->get();
        $parametro    = Examen_Parametro::find($request['parametro']);
        if(is_null($request['fecha'])){
            $request['fecha'] = date('Y/m/d');
        }
        
        if(is_null($request['fecha_hasta'])){
            $request['fecha_hasta'] = date('Y/m/d');
        }
        $datos = $this->searchReporte($request);
        $datos = $datos->paginate(10);
        return view('laboratorio/reportes/view_report_certificacion', ['fecha'=> $request['fecha'], 'fecha_hasta'=> $request['fecha_hasta'], 'pacientes'=>$pacientes,'paciente'=>$paciente,'parametros'=>$parametros,'parametro_obj'=>$parametro,'datos'=> $datos]);
    }

    public function searchReporte(Request $request)
    {
        $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $datos = DB::table('examen_resultado as er')
            ->join('examen_parametro as ep', 'ep.id', 'er.id_parametro')
            ->join('examen as e', 'e.id', 'ep.id_examen')
            ->join('examen_orden as eo', 'eo.id', 'er.id_orden')
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->where('eo.estado', '1')
            ->where('ep.estado', '1')
            ->where('er.certificado',1)
            ->select('eo.id as id_orden', 'e.nombre as examen', 'ep.nombre as parametro_nombre', 'eo.fecha_orden as fecha_registro', 'er.updated_at as fecha_lectura', 'er.fecha_anterior as fecha_certificacion', 'p.nombre1', 'p.apellido1', 'e.tiempos');

        if (count($request->all()) > 0) {
            if (!is_null($request->fecha)) {
                $datos = $datos->where('eo.fecha_orden', ">=", "{$fecha_desde} 00:00:00");
            }

            if (!is_null($request->fecha_hasta)) {
                $datos = $datos->where('eo.fecha_orden', "<=", "{$fecha_hasta} 23:59:59");
            }

            if (!is_null($request->paciente)) {
                $datos = $datos->where('eo.id_paciente',$request->paciente);
            }

            if (!is_null($request->parametro)) {
                $datos = $datos->where('ep.id',$request->parametro);
            }
        }
        return $datos;
    }

    public function reporteTiempoCertificacion(Request $request)
    {
        $datos = $this->searchReporte($request);
        $datos = $datos->get();
        Excel::create('Reporte_Tiempo_Certificacion', function ($excel) use ($datos) {
            $excel->sheet('Reporte Tiempo Certificacion', function ($sheet) use ($datos) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
                $sheet->mergeCells('A1:K2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Tiempo Certificacion');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = 3;
                $arrTitulos1 = ["#orden", "Paciente", "Examen", "Parametro", "Fecha registro", "Fecha entrega", "Fecha certificacion", "Diff Real", "Tiempo Estimado", "Diff Estimado", "Cumple"];
                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos1); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos1, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos1[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $contSi = 0;
                $contNo = 0;
                foreach ($datos as $value) {
                    $diff_tiempo = 0;
                    $ini = new DateTime($value->fecha_registro);
                    if(!is_null($value->fecha_certificacion)){
                        $fin = new DateTime($value->fecha_certificacion);
                    }
                    else{
                        $fin = new DateTime($value->fecha_lectura);
                    }
                    $diff_tiempo = $ini->diff($fin);
                    $diferencia = "";
                    $meses = $diff_tiempo->m;
                    $dias = $diff_tiempo->d;
                    $horas = sprintf("%02d", $diff_tiempo->h);
                    $minutos = sprintf("%02d", $diff_tiempo->i);
                    $segundos = sprintf("%02d", $diff_tiempo->s);
                    if($meses>0){
                        $diferencia = $meses . 'm' . $dias . 'd' . $horas . ':'. $minutos . ':' . $segundos;
                    }
                    else if($dias>0){
                        $diferencia = $dias . 'd'. $horas . ':' . $minutos . ':' . $segundos;
                    }
                        
                    else if($horas>0){
                        $diferencia = $horas . ':' . $minutos . ':' . $segundos;
                    }
                    else if($minutos>0){
                        $diferencia = $minutos . ':' . $segundos;
                    }
                    else{
                        $diferencia = $segundos . 'seg';
                    }
                    $tiempo = trim($value->tiempos);
                    if(str_contains($value->tiempos,'-')){
                        $tiempo = trim(explode('-',$value->tiempos)[1]);
                    }
                    preg_match('/(\d+)\s*(\w+)/', $tiempo, $matches);
                    $num_dias = 0;
                    if(count($matches)==3){
                        $cantidad = intval($matches[1]);
                        $unidad = strtolower($matches[2]);
                        // Mapear las unidades a los intervalos de fecha de PHP
                        switch ($unidad) {
                            case 'dia':
                            case 'dias':
                                $num_dias = $cantidad;
                                break;
                            case 'semana':
                            case 'semanas':
                                $num_dias = $cantidad*7;
                                break;
                            case 'mes':
                            case 'meses':
                                $num_dias = $cantidad*30;
                                break;
                            case 'año':
                            case 'años':
                                $num_dias = $cantidad*365;
                                break;
                            default:
                                // Si la unidad no es reconocida, se asume que es en días
                                $num_dias = $cantidad;
                                break;
                        }
                    }

                    $fin_estimado = new DateTime($value->fecha_registro);
                    $fin_estimado->modify("+$num_dias days");

                    // Calcular la diferencia entre las fechas
                    $diff_real = $ini->diff($fin);
                    $diff_estimado = $ini->diff($fin_estimado);
                    
                    // Convertir las diferencias a segundos
                    $segundos_real = $diff_real->days * 24 * 3600 + $diff_real->h * 3600 + $diff_real->i * 60 + $diff_real->s;
                    $segundos_estimado = $diff_estimado->days * 24 * 3600 + $diff_estimado->h * 3600 + $diff_estimado->i * 60 + $diff_estimado->s;
                    $diferenciaTiempo = abs($segundos_real - $segundos_estimado);
                    // Convertir la diferencia en segundos a días, horas, minutos y segundos
                    $dias_diferencia = floor($diferenciaTiempo / (3600 * 24));
                    $horas_diferencia = floor(($diferenciaTiempo % (3600 * 24)) / 3600);
                    $minutos_diferencia = floor(($diferenciaTiempo % 3600) / 60);
                    $segundos_diferencia = $diferenciaTiempo % 60;
                    // Comparar las diferencias y mostrar el resultado adecuado
                    if ($dias_diferencia > 0) {
                        $diferenciaIntervalos = "$dias_diferencia días";
                    } elseif ($horas_diferencia > 0) {
                        $diferenciaIntervalos = "$horas_diferencia horas";
                    } elseif ($minutos_diferencia > 0) {
                        $diferenciaIntervalos = "$minutos_diferencia minutos";
                    } else {
                        $diferenciaIntervalos = "$segundos_diferencia segundos";
                    }

                    $condicion = '';
                    
                    if($diff_tiempo->d <= $diff_estimado->d){
                        $condicion = 'SI';
                        $contSi++;
                    }
                    else{
                        $condicion = 'No';
                        $contNo++;
                    }  

                    $arrValue = [];
                    $arrValue = [$value->id_orden, $value->nombre1.' '.$value->apellido1, $value->examen, $value->parametro_nombre, $value->fecha_registro,$value->fecha_lectura, $value->fecha_certificacion, $diferencia,$value->tiempos,$diferenciaIntervalos,$condicion];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                $total = count($datos);
                $row = $total + 4;
                $sheet->cell('J'."{$row}", function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cumplen tiempo:');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K'."{$row}", function ($cell) use ($contSi) {
                    // manipulate the cel
                    $cell->setValue($contSi);
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L'."{$row}", function ($cell) use ($contSi,$total) {
                    // manipulate the cel
                    $porcentSi = $contSi * 100 / $total;
                    $cell->setValue($porcentSi . '%');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $row++;
                $sheet->cell('J'."{$row}", function ($cell){
                    // manipulate the cel
                    $cell->setValue('No cumplen tiempo: ');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K'."{$row}", function ($cell) use ($contNo) {
                    // manipulate the cel
                    $cell->setValue($contNo);
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L'."{$row}", function ($cell) use ($contNo,$total) {
                    // manipulate the cel
                    $porcentNo = $contNo * 100 / $total;
                    $cell->setValue($porcentNo . '%');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $row++;
                $sheet->cell('J'."{$row}", function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total: ');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K'."{$row}", function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L'."{$row}", function ($cell) use ($total) {
                    // manipulate the cel
                    $porcentTotal = $total * 100 / $total;
                    $cell->setValue($porcentTotal . '%');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public function buscar_parametro(Request $request)
    {
        $nombre = $request['term'];
        $datos = [];
        $datos = Examen_Parametro::where('estado',1);
        if (count($request->all()) > 0) {
            if (!is_null($request->term)) {
                $datos = $datos->where('nombre', "like", '%' . $nombre . '%');
            }
        }
        
        $datos = $datos->get();
        $data      = [];
        foreach ($datos as $dato) {
            $data[] = array('text' => $dato->nombre, 'id' => $dato->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['text' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_empresa(Request $request)
    {
        $nombre = $request['term'];
        $datos = [];
        $datos = Empresa::where('estado',1);
        if (count($request->all()) > 0) {
            if (!is_null($request->term)) {
                $datos = $datos->where(function ($jq1) use ($nombre) {
                    $jq1->orwhereraw('razonsocial LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('nombrecomercial LIKE ?', ['%' . $nombre . '%']);
                });
            }
        }

        $datos = $datos->get();

        $data      = array();

        foreach ($datos as $dato) {
            $data[] = array('text' => $dato->razonsocial, 'id' => $dato->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['text' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_paciente(Request $request)
    {
        $nombre_pac   = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        
        $pacientes = Paciente::where(DB::raw('CONCAT_WS(" ", nombre1, nombre2, apellido1, apellido2)'), 'like', "%$seteo%")
        ->select(DB::raw('CONCAT_WS(" ", nombre1, nombre2, apellido1, apellido2) as completo,id'))
        ->get();

        foreach ($pacientes as $paciente) {
            $data[] = array('text' => $paciente->completo, 'id' => $paciente->id, 'nombres' => $paciente->completo);
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['text' => 'No se encontraron resultados', 'id' => ''];
        }
    }
}
