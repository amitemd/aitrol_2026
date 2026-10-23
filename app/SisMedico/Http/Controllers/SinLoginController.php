<?php

namespace Sis_medico\Http\Controllers;

use Auth;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mail;
use Sis_medico\Agenda;
use Sis_medico\Area;
use Sis_medico\Contable;
use Sis_medico\Ct_Clientes;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Complemento;
use Sis_medico\Encuesta_Complementolabs;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_Resultado;
use Sis_medico\Form_Preadmision_Lista;
use Sis_medico\Forma_de_pago;
use Sis_medico\GrupoPregunta;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Log_Api;
use Sis_medico\Log_usuario;
use Sis_medico\Master_encuesta;
use Sis_medico\Paciente;
use Sis_medico\Pregunta;
use Sis_medico\Preguntas_Labs;
use Sis_medico\Protocolo;
use Sis_medico\Sugerencia;
use Sis_medico\TipoSugerencia;
use Sis_medico\User;
use Sis_medico\Historiaclinica;
use Sis_medico\Master_Labs_Encuesta;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Form_Preadmision_Cabecera;
use Sis_medico\Form_Preadmision_Detalle;
use Sis_medico\Hc_Log;
use Sis_medico\Paciente_Antecedente;
use Sis_medico\Principio_Activo;
use Sis_medico\Paciente_Habito;
use Sis_medico\Paciente_Medicamento;
use Sis_medico\Examen_Parametro_Referencia_Anterior;
use Sis_medico\InvCabMovimientos;
use Sis_medico\Seguro;
use Sis_medico\Examen_Agrupador_010;
use Sis_medico\Examen_Agrupador_Sabana;


class SinLoginController extends Controller
{
    public function sugerencia()
    {
        $areas          = Area::where("estado", '=', 1)->get();
        $tiposugerencia = TipoSugerencia::where('estado', '1')->get();
        return view('sinlogin/sugerencias', ['areas' => $areas, 'tiposugerencia' => $tiposugerencia]);
    }

    public function sugerenciaguardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        Sugerencia::create([
            'id_tiposugerencia' => $request['tiposugerencia'],
            'id_area'           => $request['area'],
            'observacion'       => $request['mensaje'],
        ]);
        $areas          = Area::where("estado", '=', 1)->get();
        $tiposugerencia = TipoSugerencia::where('estado', '1')->get();
        return view('sinlogin/general');
        //return redirect()->intended('/sala-management');
    }

    public function encuestas()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function encuestas2()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta2', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function encuestaguardar(Request $request)
    {
        //return $request->all();
        $id_master  = $request->id_master;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        /*$id = DB::table('encuesta_1')->insertGetId(
        ['ip_ingreso' => $ip_cliente, 'id_area' => $request['area']]
        );*/

        if ($request['cedula'] == null) {
            return "1";
        }

        $fecha_hoy = date('Y-m-d');
        $master    = $request['id_master'];

        if ($master == 1) {
            $master = 0;
        }

        if ($master == 2) {
            $master = 1;
        }

        if ($request['cedula'] != null) {
            $paciente = Paciente::find($request['cedula']);

            if ($paciente == null) {
                return "5";
            }
        }

        //dd($master);
        $numero      = 1;
        $no_agendado = Agenda::where('id_paciente', $request['cedula'])->whereBetween('fechaini', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where('estado', '1')->get();
        // dd($no_agendado);

        if (count($no_agendado) <= 0) {
            return "2";
        }


        if (count($no_agendado) == 1) {
            if ($no_agendado[0]->proc_consul != $master) {

                return "4";
            }
        }

        if (count($no_agendado) > 1) {

            for ($i = 0; $i < count($no_agendado); $i++) {

                if ($no_agendado[$i]->proc_consul != $master) {

                    $numero++;
                }
            }

            if ($numero == 0) {

                return "2";
            }
        }

        $grupopregunta = GrupoPregunta::all();
        //return $grupopregunta;
        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            if ($value->id != 4) {
                //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
                $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();
                //return $dato;
                foreach ($dato as $value2) {
                    $flag = false;
                    if ($request[$value2->id] == null) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    return "3";
                }
            }
        }

        $id = DB::table('encuesta_1')->insertGetId(
            ['ip_ingreso' => $ip_cliente, 'id_area' => $id_master, 'anio' => substr(date('Y-m-d'), 0, 4), 'mes' => substr(date('Y-m-d'), 5, 2), 'id_paciente' => $request['cedula']]
        );
        //return $id;

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();

            //return $dato;
            foreach ($dato as $value2) {

                DB::table('encuesta_complemento')->insert(
                    ['id_encuesta_1' => $id, 'id_pregunta' => $value2->id, 'valor' => $request[$value2->id], 'id_grupo' => $value2->id_grupopregunta, 'valor2' => $request['val2' . $value2->id]]
                );
            }
        }

        return "ok";
    }

    public function encuesta_sugencia()
    {
        return view('sinlogin/general');
    }

    public function maquina1(Request $request)
    {
        $trama      = $request->parametro;
        $enviar     = explode("torbi10", $trama);
        $valor_id   = "dato";
        $idusuario  = '9999999999';
        $ipcreacion = "maquina";
        $n_examen   = 101;
        foreach ($enviar as $value) {
            $descriptando = explode("|", $value);
            if ($descriptando[0] == "OBR") {
                $valor_id = $descriptando[3];
                $examen   = Examen_Detalle::where('id_examen', '101')->where('id_examen_orden', $valor_id)->first();
                if (!is_null($examen)) {
                    $n_examen = 101;
                } else {
                    $n_examen = 1184;
                }
            }
            if ($valor_id != "dato") {
                if (($descriptando[0] == "OBX") && ($descriptando[1] < 36)) {

                    $buscar_tipo = explode("^", $descriptando[3]);

                    $examen_parametro = examen_parametro::where('nombre', $buscar_tipo[1])->where('id_examen', $n_examen)->first();

                    if (!is_null($examen_parametro)) {

                        $id_parametro     = $examen_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => $descriptando[5],
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => $descriptando[5],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }
                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
        }

        return "MSH|^~\&|LIS||||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\nMSA|AA|1";
    }

    public function maquina2(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '6';
        //return "hola";
        if (count($enviar) == 4) {
            //ENVIAR EXAMENWS
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QRD") {
                    $valor_id = $descriptando[8];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo  = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||DSR^Q03|0|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                if ($value->id_examen == "1179") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                if ($value->id_examen == "1181") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                //examen especial proteinas y fracciones
                                if ($value->examen->nombre_equipo != "") {
                                    if ($contador == 29) {
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                    $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                    $contador = $contador + 1;
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        }
        if (count($enviar) == 5) {
            //RECIBIR RESULTADOS
            $codigo_maquina = 1;
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                }
                if ($descriptando[0] == "OBX") {

                    $examen = examen::where('nombre_equipo', $descriptando[3])->first();

                    $examen_parametro = examen_parametro::where('id_examen', $examen->id)->get();

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => round($descriptando[5], 2),
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => round($descriptando[5], 2),
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function bha200(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '69F2746D24014F21AD7139756F64CAD8';
        //return "hola";
        if (count($enviar) == 3) {
            //ENVIAR EXAMENWS
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "bha200") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QPD") {
                    $valor_id = $descriptando[3];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\&|Modulab|Systelab|bha200|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
3|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo = chr(0x0B) . "MSH|^~\\&|Modulab|Systelab|bha200|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
3|69F2746D24014F21AD7139756F64CAD8|P|2.5.1|||ER|AL||UNICODE UTF-8|||LAB-28^IHE\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                if ($value->id_examen == "1179") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                if ($value->id_examen == "1181") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                //examen especial proteinas y fracciones
                                if ($value->examen->nombre_equipo != "") {
                                    if ($contador == 29) {
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                    $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                    $contador = $contador + 1;
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        }
        if (count($enviar) == 5) {
            //RECIBIR RESULTADOS
            $codigo_maquina = '69F2746D24014F21AD7139756F64CAD8';
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|Modulab|Systelab|BA400|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
3|" . $codigo_maquina . "|P|2.5.1|||ER|AL||UNICODE UTF-8|||LAB-28^IHE\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                }
                if ($descriptando[0] == "OBX") {

                    $examen = examen::where('nombre_equipo', $descriptando[3])->first();

                    $examen_parametro = examen_parametro::where('id_examen', $examen->id)->get();

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => round($descriptando[5], 2),
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => round($descriptando[5], 2),
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function maquina3(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '6';
        $ultimo         = end($enviar);
        if (substr($ultimo, 0, 3) != "OBX") {
            //ENVIAR EXAMENWS
            //return "entrada";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QRD") {
                    $valor_id = $descriptando[8];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo  = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||DSR^Q03|0|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                $esp_examen_parametro = Examen_Parametro::where('id_examen', $value->id_examen)
                                    ->whereNotNull('nombre_equipo')
                                    ->where('maquina', 3)
                                    ->get();
                                foreach ($esp_examen_parametro as $esp_value) {
                                    if ($esp_value->nombre_equipo != "") {
                                        if ($contador == 29) {
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        } else {
            //return "recibir resultados";
            //RECIBIR RESULTADOS
            $codigo_maquina = 1;
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                return $descriptando[0];
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                    //return $valor_id;
                }
                if ($descriptando[0] == "OBX") {
                    //return $examen->id;
                    //return "OBX";

                    $examen_parametro = examen_parametro::where('nombre_equipo', $descriptando[3])->where('maquina', 3)->get();
                    //return $examen_parametro;

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            //return $examen_resultado->id;
                            //return "entra actual";
                            $input = [
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            //return "no tiene";
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function rrhh_encuesta($id)
    {
        //dd($id);

        $encuestas = Master_encuesta::where('estado', 1)->where('id', $id)->first();
        //dd($encuestas);
        if (is_null($encuestas)) {
            // dd("esta vacio");
            $encuestas = Master_encuesta::where('estado', 1)->where('id', 1)->first();
            return view('rrhh.encuesta', ['encuestas' => $encuestas]);
        } else {
            return view('rrhh.encuesta', ['encuestas' => $encuestas]);
        }
    }

    public function formato_encuesta($id)
    {

        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        $encuesta      = Master_encuesta::find($id);
        // dd($encuesta->id);
        $tiempos = explode('+', $encuesta->tiempos);

        //dd($tiempos);

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id)->get();
            //dd($dato);
            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        //dd($id);
        return view('rrhh/formato_encuesta', ['arreglo' => $arreglo, 'areas' => $areas, 'tiempos' => $tiempos, 'encuesta' => $encuesta, 'id' => $encuesta->id]);
    }

    public function externo_web()
    {
        $covid_local     = Examen::find('1191');
        $covid_domicilio = Examen::find('1195');

        return view('laboratorio/externo/web', ['covid_local' => $covid_local, 'covid_domicilio' => $covid_domicilio]);
    }

    public function promo($id)
    {
        return view('laboratorio/externo/ingresar_orden', ['id' => $id]);
    }
    //MODIFICADA PARA PAGO EN LINEA
    public function externo_guardar(Request $request)
    {
        $_pasarela_pagos_detalle  = "";
        $_pasarela_pagos_subtotal = 0;
        $_pasarela_pagos_iva      = 0;
        $_pasarela_pagos_total    = 0;
        //return "guardar";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $id_seguro = null;
        if ($request->promo == '1') {
            $id_seguro    = '30';
            $id_protocolo = '10';
            $id_nivel     = '29';
        } elseif ($request->promo == '2') {
            $id_seguro    = '31';
            $id_protocolo = '11';
            $id_nivel     = '30';
        } elseif ($request->promo == '3') {
            $id_seguro    = '32';
            $id_protocolo = '12';
            $id_nivel     = '31';
        } elseif ($request->promo == '4' || $request->promo == '5') {
            $id_seguro    = '1';
            $id_protocolo = null;
            $id_nivel     = null;
        }
        if ($id_seguro != null) {
            $reglas = [
                'email' => 'email',
            ];
            $mensaje = [
                'email.email'  => 'El Email tiene error en el formato.',
                'email.unique' => 'El Email ya se encuentra registrado.',
            ];
            $this->validate($request, $reglas, $mensaje);
            $paciente = Paciente::find($request->id);
            if (!is_null($paciente)) {
                $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
            } else {
                $usuario_mail = User::where('email', $request->email)->first();
            }

            if (!is_null($usuario_mail)) {
                $xusuario = $usuario_mail;
                //Si encuentra el correo, crea o actualiza el grupo familiar
                $email = $request->id . '@correo.dum';
                $user  = User::find($request->id);
                if (is_null($user)) {
                    $input_us = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'telefono1'        => $request['celular'],
                        'telefono2'        => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'id_tipo_usuario'  => 2,
                        'email'            => $email,
                        'password'         => bcrypt($request['id']),
                        'tipo_documento'   => 1,
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => '1234517896',
                        'id_usuariomod'    => '1234517896',
                    ];
                    User::create($input_us);
                } else {
                    $input_us = [
                        'email'           => $email,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ];
                    $user->update($input_us);
                }
                $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
                if (is_null($grupo_familiar)) {
                    Labs_Grupo_Familiar::create([
                        'id'              => $request->id,
                        'id_usuario'      => $usuario_mail->id,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                        'ip_creacion'     => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                    ]);
                } else {
                    $grupo_familiar->update([
                        'id_usuario'      => $usuario_mail->id,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ]);
                }
            } else {
                $email = $request->email;
                $user  = User::find($request->id);
                if (is_null($user)) {
                    $input_us = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'telefono1'        => $request['celular'],
                        'telefono2'        => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'id_tipo_usuario'  => 2,
                        'email'            => $email,
                        'password'         => bcrypt($request['id']),
                        'tipo_documento'   => 1,
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => '1234517896',
                        'id_usuariomod'    => '1234517896',
                    ];
                    User::create($input_us);
                } else {
                    $input_us = [
                        'email'           => $email,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ];
                    $user->update($input_us);
                }
                $xusuario = User::find($request->id);
            }

            if (is_null($paciente)) {
                //CREA EL PACIENTE
                //crear paciente
                $input_pac = [
                    'id'                 => $request['id'],
                    'id_usuario'         => $request['id'],
                    'nombre1'            => strtoupper($request['nombre1']),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($request['apellido1']),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $request['fecha_nacimiento'],
                    'sexo'               => $request['sexo'],
                    'telefono1'          => $request['celular'],
                    'telefono2'          => '1',
                    'nombre1familiar'    => strtoupper($request['nombre1']),
                    'nombre2familiar'    => strtoupper($request['nombre2']),
                    'apellido1familiar'  => strtoupper($request['apellido1']),
                    'apellido2familiar'  => strtoupper($request['apellido2']),
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => $id_seguro,
                    'imagen_url'         => ' ',
                    'menoredad'          => 0,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariocrea'     => '1234517896',
                    'id_usuariomod'      => '1234517896',
                ];
                Paciente::create($input_pac);
                $paciente = Paciente::find($request->id);
            }
            $input_ex = [
                'id_paciente'      => $paciente->id,
                'anio'             => 0,
                'mes'              => 0,
                'id_protocolo'     => $id_protocolo,
                'id_seguro'        => $id_seguro,
                'id_nivel'         => $id_nivel,
                'est_amb_hos'      => '0',
                'id_doctor_ieced'  => '1234517896',
                'doctor_txt'       => 'PAGADO EN LA WEB',
                'observacion'      => '',
                'id_empresa'       => '0992704152001',
                'cantidad'         => '0',
                'estado'           => '1',
                'realizado'        => '1',
                'valor'            => '0',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => '1234517896',
                'id_usuariomod'    => '1234517896',
                'motivo_descuento' => '',
                //'fecha_orden'      => date('Y-m-d h:i:s'),
                'codigo'           => '0001',
            ];
            $id_orden  = Examen_Orden::insertGetId($input_ex);
            $pexamenes = Examen_Protocolo::where('id_protocolo', $id_protocolo)->get();
            $total     = 0;
            $contador  = 0;
            if ($request->promo == '4' || $request->promo == '5') {
                if ($request->promo == '4') {
                    $examen    = Examen::find('1191');
                    $input_det = [
                        'id_examen_orden' => $id_orden,
                        'id_examen'       => $examen->id,
                        'valor'           => $examen->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                        'id_usuariomod'   => '1234517896',
                    ];
                    Examen_detalle::create($input_det);
                    $total = $examen->valor;
                    $contador++;
                } else {
                    $examen    = Examen::find('1195');
                    $input_det = [
                        'id_examen_orden' => $id_orden,
                        'id_examen'       => $examen->id,
                        'valor'           => $examen->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                        'id_usuariomod'   => '1234517896',
                    ];
                    Examen_detalle::create($input_det);
                    $total = $examen->valor;
                    $contador++;
                }
            } else {
                foreach ($pexamenes as $pexamen) {
                    //CREA DETALLE
                    $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $pexamen->id_examen)->first();
                    if (is_null($detalle)) {
                        $contador++;
                        $examen = Examen::find($pexamen->id_examen);
                        //return $examen;
                        $valor    = $examen->valor;
                        $cubre    = 'NO';
                        $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $id_nivel)->first();
                        if (!is_null($ex_nivel)) {
                            if ($ex_nivel->valor1 != 0) {
                                $valor = $ex_nivel->valor1;
                                $cubre = 'SI';
                            }
                        }
                        $input_det = [
                            'id_examen_orden' => $id_orden,
                            'id_examen'       => $examen->id,
                            'valor'           => $valor,
                            'cubre'           => $cubre,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                        ];
                        Examen_detalle::create($input_det);
                        /// orden
                        $total += $valor;
                    }
                }
            }
            //ACTUALIZA ORDEN
            $total = round($total, 2);
            /*$descuento_p = $orden->descuento_p;
            if ($orden->descuento_p == null) {
            $descuento_valor = 0;
            $descuento_p     = 0;
            }*/
            /*if ($orden->descuento_p > 100) {
            $descuento_valor = $total;
            } else {
            $descuento_valor = $orden->descuento_p * $total / 100;
            $descuento_valor = round($descuento_valor, 2);
            }*/
            //$subtotal_pagar = $total - $descuento_valor;
            $subtotal_pagar = $total;
            $forma_pago     = Forma_de_pago::find('1');
            $recargo_p      = $forma_pago->recargo_p;
            $recargo_valor  = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor  = round($recargo_valor, 2);
            $valor_total    = $subtotal_pagar + $recargo_valor;
            $valor_total    = round($valor_total, 2);
            $orden          = Examen_Orden::find($id_orden);
            $input_ex2      = [
                'descuento_valor' => '0',
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                'cantidad'        => $contador,
                'valor'           => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
                'pago_online'     => '1', //indica que es un pago en linea
                'estado_pago'     => '0', //se confirmo el pago o no
            ];
            $orden->update($input_ex2);
            Log_usuario::create([
                'id_usuario'  => '1234517896',
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GENERA COTIZACION WEB",
                'dato_ant1'   => $paciente->id,
                'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
            ]);
            /*$nombre_paciente = $paciente->apellido1;
        if($paciente->apellido2!='(N/A)' || $paciente->apellido2!='N/A'){
        $nombre_paciente = $nombre_paciente.' '.$paciente->apellido2;
        }
        $nombre_paciente = $nombre_paciente.' '.$paciente->apellido1;
        if($paciente->nombre1!='(N/A)' || $paciente->nombre1!='N/A'){
        $nombre_paciente = $nombre_paciente.' '.$paciente->nombre1;
        }

        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $xusuario, "paciente" => $paciente);
        $correo = $xusuario->email;
        Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
        $msj->from("noreply@labs.ec", "HUMANLABS");
        $msj->subject('Resultados de Exámenes de Laboratorio');
        $msj->to($correo);
        $msj->bcc('torbi10@hotmail.com');

        });*/
        } else {
            return "Seleccione Paquete Preventivo";
        }

        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_iva      = 0;
        $_pasarela_pagos_total    = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)
        $pyf_details = array();
        array_push($pyf_details, array(
            "sku"      => '' . $id_orden . '',
            "name"     => 'LABS.EC Orden de laboratorio #' . $id_orden,
            "qty"      => 1,
            "price"    => $_pasarela_pagos_subtotal,
            "tax"      => 0.00,
            "discount" => 0.00, //falta revisar
            "total"    => $_pasarela_pagos_subtotal,
        ));
        $celular = $request['celular'];
        if (strlen($request['celular']) < 10) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        //json de invocación
        $data_array = array(
            "company"        => $RUC_LABS,
            "person"         => array(
                "document"     => $request['id'],
                "documentType" => $this->getDocumentType($request['id']),
                "name"         => $this->cleanNames(strtoupper($request['nombre1']) . ' ' . strtoupper($request['nombre2'])),
                "surname"      => $this->cleanNames(strtoupper($request['apellido1']) . ' ' . strtoupper($request['apellido2'])),
                "email"        => $email,
                "mobile"       => $celular,
            ),
            "paymentRequest" => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(

                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            "base"   => $_pasarela_pagos_subtotal,
                        ),
                    ),

                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "returnUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=" . $id_orden, //URL DE RETORNO
            "cancelUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=" . $id_orden, //URL DE CANCELACION
            "userAgent"      => "labs_ec/1",
        );

        $manage    = json_encode($data_array);
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'usuario' => $xusuario->id, 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
    }

    public function mail_externo_web($id_paciente, $id_usuario)
    {
        $paciente        = Paciente::find($id_paciente);
        $usuario         = User::find($id_usuario);
        $nombre_paciente = $paciente->apellido1;
        if ($paciente->apellido2 != '(N/A)' || $paciente->apellido2 != 'N/A') {
            $nombre_paciente = $nombre_paciente . ' ' . $paciente->apellido2;
        }
        $nombre_paciente = $nombre_paciente . ' ' . $paciente->apellido1;
        if ($paciente->nombre1 != '(N/A)' || $paciente->nombre1 != 'N/A') {
            $nombre_paciente = $nombre_paciente . ' ' . $paciente->nombre1;
        }
        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $usuario, "paciente" => $paciente);
        $correo   = $usuario->email;
        Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');
        });

        return "ok";
    }

    public function buscapaciente($id)
    {
        $paciente = Paciente::find($id);
        if (!is_null($paciente)) {
            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email = $gf_user->email;
                }
            } else {
                $email = $paciente->usuario->email;
            }
            return ['id' => $paciente->id, 'nombre1' => $paciente->nombre1, 'nombre2' => $paciente->nombre2, 'apellido1' => $paciente->apellido1, 'apellido2' => $paciente->apellido2, 'telefono1' => $paciente->telefono1, 'direccion' => $paciente->direccion, 'email' => $email, 'sexo' => $paciente->sexo, 'fecha_nacimiento' => $paciente->fecha_nacimiento, 'resultado' => 'ok'];
        } else {
            return ['resultado' => 'no'];
        }
    }

    public function buscar_orden($id_orden)
    {

        $orden = Examen_Orden::find($id_orden);
        if (!is_null($orden)) {
            $paciente = $orden->paciente;

            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($paciente->id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email = $gf_user->email;
                }
            } else {
                $email = $paciente->usuario->email;
            }
            $subtotal = 0;
            foreach ($orden->detalles as $value) {
                $subtotal += $value->valor;
            }

            $seguro = $orden->seguro->tipo;
            if ($seguro == '0') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " Pertenece a un Seguro Público y No se puede pagar por este medio";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($seguro == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " Pertenece a un Seguro Privado y No se puede pagar por este medio";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->pago_online == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " ya se encuentra pagada, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->estado_pago == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " ya se encuentra pagada, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->total_valor == '0') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " no tiene valor a pagar, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->cantidad != $orden->detalles->count()) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($subtotal - $orden->valor > 1) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración.";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($subtotal - $orden->valor < -1) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración..";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }

            $detalles = $orden->detalles;
            $age      = 0;
            if ($orden->paciente->fecha_nacimiento != null) {
                $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
            }
            return view('laboratorio.externo.cotizacion_externa', ['orden' => $orden, 'detalles' => $detalles, 'age' => $age, 'email' => $email]);
        }

        $mensaje = "El número de cotización " . $id_orden . " No se encuentra registrada en el sistema, consultar con la administración";
        return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
    }

    public function pagar_orden(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $reglas     = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email' => 'El Email tiene error en el formato.',
            //'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);

        $orden = Examen_Orden::find($request->id_orden);
        if (!is_null($orden)) {
            $paciente = $orden->paciente;

            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($paciente->id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email    = $gf_user->email;
                    $xusuario = $gf_user;
                }
            } else {
                $email    = $paciente->usuario->email;
                $xusuario = $paciente->usuario;
            }
            $cambio = false;
            if ($email != $request->email) {
                $cambio = true;
            }
            if ($cambio) {
                $usuario = User::where('email', $request->email)->first();
                if (!is_null($usuario)) {
                    //Si encuentra el correo, crea o actualiza el grupo familiar
                    $xusuario = $usuario;
                    if (!is_null($gf_user)) {
                        $gf_user->update([
                            'id_usuario'      => $usuario->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $usuario->id,
                        ]);
                    } else {
                        Labs_Grupo_Familiar::create([
                            'id'              => $paciente->id,
                            'id_usuario'      => $usuario->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $usuario->id,
                            'ip_creacion'     => $ip_cliente,
                            'id_usuariocrea'  => $usuario->id,
                        ]);
                    }
                } else {
                    $paciente->usuario->update([
                        'email' => $request->email,
                    ]);
                    $xusuario = $paciente->usuario;
                }
                $input_log = [
                    'id_usuario'  => $paciente->id,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "ACTUALIZA PACIENTE MAIL PAGO LABS",
                    'dato_ant1'   => $email,
                    'dato1'       => $request->email,
                    'dato_ant4'   => "COTIZACION",
                    'dato2'       => 'COTIZACION',
                ];

                Log_usuario::create($input_log);
            }
            //ACTUALIZA LA ORDEN
            $orden->update([
                'fecha_orden' => null,
                'anio'        => '0',
                'mes'         => '0',
                'estado_pago' => '1',
                'pago_online' => '1',
            ]);
            $input_log = [
                'id_usuario'  => $paciente->id,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "PAGO ONLINE LABS",
                'dato_ant1'   => 'ORDEN : ' . $orden->id,
                'dato1'       => 'PACIENTE : ' . $paciente->id,
                'dato_ant4'   => "COTIZACION",
                'dato2'       => 'COTIZACION',
            ];

            Log_usuario::create($input_log);

            /*$nombre_paciente = $paciente->apellido1;
            if($paciente->apellido2!='(N/A)' || $paciente->apellido2!='N/A'){
            $nombre_paciente = $nombre_paciente.' '.$paciente->apellido2;
            }
            $nombre_paciente = $nombre_paciente.' '.$paciente->apellido1;
            if($paciente->nombre1!='(N/A)' || $paciente->nombre1!='N/A'){
            $nombre_paciente = $nombre_paciente.' '.$paciente->nombre1;
            }

            $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $xusuario, "paciente" => $paciente);
            $correo = $xusuario->email;
            Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');

            });*/

            return ['estado' => 'ok', 'usuario' => $xusuario->id, 'paciente' => $paciente->id];
        }

        return "error";
    }

    public function carrito_paciente()
    {
        $covid_local     = Examen::find('1191');
        $covid_domicilio = Examen::find('1195');

        return view('laboratorio/externo/carrito_paciente', ['covid_local' => $covid_local, 'covid_domicilio' => $covid_domicilio]);
    }

    public function carrito_guardar_orden(Request $request)
    {
        if ($request['token'] != '26ae11be61ffd5cda37477a54f6d1355') {
            return "credenciales invalidas";
        }

        $arr_examen = $request['examenes']; //RECIBO ARREGLO DE LOS EXAMENES DEL COTIZADOR
        //dd($arr_examen,$request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $id_seguro = '1'; //PARTICULAR
        $reglas    = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email'  => 'El Email tiene error en el formato.',
            'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);
        $paciente = Paciente::find($request->id);
        if (!is_null($paciente)) {
            //NO EXISTE PACIENTE
            $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
        } else {
            //EXISTE PACIENTE
            $usuario_mail = User::where('email', $request->email)->first();
        }

        if (!is_null($usuario_mail)) {
            $xusuario = $usuario_mail;
            //Si encuentra el correo, crea o actualiza el grupo familiar
            $email = $request->id . '@correo.dum';
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
            if (is_null($grupo_familiar)) {
                Labs_Grupo_Familiar::create([
                    'id'              => $request->id,
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                ]);
            } else {
                $grupo_familiar->update([
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ]);
            }
        } else {
            $email = $request->email;
            $user  = User::find($request->id);
            if (is_null($user)) {

                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $xusuario = User::find($request->id);
        }

        //dd($input_us,$request->all());

        if (is_null($paciente)) {
            //CREA EL PACIENTE
            //crear paciente
            $input_pac = [
                'id'                 => $request['id'],
                'id_usuario'         => $request['id'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
                'sexo'               => $request['sexo'],
                'telefono1'          => $request['celular'],
                'direccion'          => $request['direccion'],
                'telefono2'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'tipo_documento'     => 1,
                'id_seguro'          => $id_seguro,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => '1234517896',
                'id_usuariomod'      => '1234517896',
            ];
            Paciente::create($input_pac);
            $input_log = [
                'id_usuario'  => $request['id'],
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "COTIZADOR",
                'dato2'       => 'PAGON EN LINEA LABS',
            ];

            Log_usuario::create($input_log);
            $paciente = Paciente::find($request->id);
        } else {
            $input_pac2 = [
                'telefono1'       => $request['celular'],
                'direccion'       => $request['direccion'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
            ];
            $paciente->update($input_pac2);
        }

        $input_ex = [
            'id_paciente'       => $paciente->id,
            'anio'              => 0,
            'mes'               => 0,
            'id_seguro'         => $id_seguro,
            'est_amb_hos'       => '0',
            'id_doctor_ieced'   => '1234517896',
            'doctor_txt'        => 'INGRESADO EN LA WEB',
            'observacion'       => '',
            'id_empresa'        => '0992704152001',
            'cantidad'          => '0',
            'estado'            => '0',
            'realizado'         => '0',
            'valor'             => '0',
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => '1234517896',
            'id_usuariomod'     => '1234517896',
            'motivo_descuento'  => '',
            'fecha_tentativa'   => $request['fecha_tentativa'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            //'fecha_orden'     => date('Y-m-d h:i:s'),
        ];

        //return ["Holaaaa" , $request->all()];
        /*$orden = Examen_Orden::where('id_paciente',$paciente->id)->where('estado','0')->where('pago_online','1')->first();
        if(!is_null($orden)){
        $orden->update($input_ex);
        $detalles = $orden->detalles;
        foreach ($detalles as $d) {
        $d->delete();
        }
        $id_orden = $orden->id;
        }else{
        $id_orden = Examen_Orden::insertGetId($input_ex);
        }*/

        $id_orden = Examen_Orden::insertGetId($input_ex);

        $input_cli = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => '5',
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => '1234517896',
            'id_usuariomod'           => '1234517896',
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();
        //return "holsaaa";
        //return [$paciente->id, $cliente];
        //echo $input_cli;
        //return "<b> .'$input_cli'.</b>";

        if (!is_null($cliente)) {

            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli);
            //return [$cliente];
            //$cliente->update($input_cli);
        } else {

            Ct_Clientes::create($input_cli);
        }

        $total    = 0;
        $contador = 0;
        $txt_ex   = '';
        //$pct_dcto          = 0.20;
        $pct_dcto          = 0;
        $descuento_detalle = 0;
        foreach ($arr_examen as $value) {

            $examen = Examen::find($value);
            //dd($value,$examen);
            if (!is_null($examen)) {
                $txt_ex          = $txt_ex . '+' . $examen->nombre;
                $p_descuento     = 0;
                $valor_descuento = 0;
                /*if ($examen->humanlabs == '1') {*/
                $p_descuento     = $pct_dcto;
                $valor_descuento = $examen->valor * $p_descuento;

                $valor_descuento = round($valor_descuento, 2);
                //$valor_descuento = floor($valor_descuento * 100)/100;
                $descuento_detalle += $valor_descuento;
                /*}*/
                $input_det = [
                    'id_examen_orden' => $id_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $examen->valor,
                    'human_labs'      => $examen->humanlabs,
                    'p_descuento'     => $p_descuento,
                    'valor_descuento' => $valor_descuento,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                    'id_usuariomod'   => '1234517896',
                ];
                Examen_detalle::create($input_det);
                $total = $total + $examen->valor;
                $contador++;
            }
        }
        /*if ($request['pres_dom'] == '1') {
        $examen = Examen::find('1203');
        //dd($value,$examen);
        if (!is_null($examen)) {
        $input_det = [
        'id_examen_orden' => $id_orden,
        'id_examen'       => $examen->id,
        'valor'           => $examen->valor,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => '1234517896',
        'id_usuariomod'   => '1234517896',
        'human_labs'      => 0,
        'p_descuento'     => 0,
        'valor_descuento' => 0,
        ];
        Examen_detalle::create($input_det);
        $total = $total + $examen->valor;
        $contador++;
        }
        }*/

        //ACTUALIZA ORDEN
        $total             = round($total, 2);
        $recargo_p         = 0;
        $descuento_detalle = round($descuento_detalle, 2);
        $subtotal_pagar    = $total - $descuento_detalle;

        $recargo_valor = $subtotal_pagar * $recargo_p / 100;
        $recargo_valor = round($recargo_valor, 2);
        $valor_total   = $subtotal_pagar + $recargo_valor;
        $valor_total   = round($valor_total, 2);
        $orden         = Examen_Orden::find($id_orden);
        $input_ex2     = [
            'recargo_valor'   => $recargo_valor,
            'total_valor'     => $valor_total,
            'cantidad'        => $contador,
            'valor'           => $total,
            'descuento_valor' => $descuento_detalle,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => '1234517896',
            'pago_online'     => '1',
            'estado_pago'     => '0',
            'pres_dom'        => $request->pres_dom,
        ];
        $orden->update($input_ex2);
        /*Log_usuario::create([
        'id_usuario'  => '1234517896',
        'ip_usuario'  => $ip_cliente,
        'descripcion' => "GENERA COTIZACION WEB",
        'dato_ant1'   => $paciente->id,
        'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
        ]);*/
        Log_usuario::create([
            'id_usuario'  => $paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "GENERA COTIZACION WEB",
            'dato_ant4'   => $txt_ex,
        ]);
        //return "ok";
        return view('laboratorio.externo.lista_confirmar', ['orden' => $orden, 'detalles' => $orden->detalles]);
    }

    public function paquete_guardar_orden(Request $request)
    {
        if ($request['token'] != '26ae11be61ffd5cda37477a54f6d1355') {
            return "credenciales invalidas";
        }

        $paquete = [
            '27' => '40',
            '28' => '41',
            '29' => '42',
            '30' => '43',
            '31' => '44',
            '32' => '45',
            '33' => '46',
            '93' => '68',
            '94' => '77',
            '95' => '81'
        ];

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $nivel              = $paquete[$request->id_protocolo]; //PARTICULAR
        $id_seguro          = '35';
        $protocolo          = Protocolo::find($request->id_protocolo);
        $examenes_protocolo = $protocolo->examenes;
        //dd($protocolo,$examenes_protocolo);
        $reglas = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email'  => 'El Email tiene error en el formato.',
            'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);
        $paciente = Paciente::find($request->id);
        if (!is_null($paciente)) {
            //BUSCA SI EL CORREO EXISTE EN OTRO USUARIO
            $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
        } else {
            //BUSCA SI EXISTE EL CORREO
            $usuario_mail = User::where('email', $request->email)->first();
        }

        if (!is_null($usuario_mail)) {
            //SI ENCUENTRA EL CORREO
            $xusuario = $usuario_mail;
            //Si encuentra el correo, crea o actualiza el grupo familiar
            $email = $request->id . '@correo.dum';
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
            if (is_null($grupo_familiar)) {
                Labs_Grupo_Familiar::create([
                    'id'              => $request->id,
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                ]);
            } else {
                $grupo_familiar->update([
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ]);
            }
        } else {
            $email = $request->email;
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'direccion'        => $request['direccion'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $xusuario = User::find($request->id);
        }

        //dd($input_us,$request->all());

        if (is_null($paciente)) {
            //CREA EL PACIENTE
            //crear paciente
            $input_pac = [
                'id'                 => $request['id'],
                'id_usuario'         => $request['id'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
                'sexo'               => $request['sexo'],
                'telefono1'          => $request['celular'],
                'direccion'          => $request['direccion'],
                'telefono2'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'tipo_documento'     => 1,
                'id_seguro'          => $id_seguro,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => '1234517896',
                'id_usuariomod'      => '1234517896',
            ];
            Paciente::create($input_pac);
            $input_log = [
                'id_usuario'  => $request['id'],
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "PROMOS",
                'dato2'       => 'PAGON EN LINEA LABS',
            ];

            Log_usuario::create($input_log);
            $paciente = Paciente::find($request->id);
        } else {
            $input_pac2 = [
                'telefono1'       => $request['celular'],
                'direccion'       => $request['direccion'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
            ];
            $paciente->update($input_pac2);
        }

        $input_ex = [
            'id_paciente'       => $paciente->id,
            'anio'              => 0,
            'mes'               => 0,
            'id_seguro'         => $id_seguro,
            'id_nivel'          => $nivel,
            'est_amb_hos'       => '0',
            'id_doctor_ieced'   => '1234517896',
            'doctor_txt'        => 'INGRESADO EN LA WEB',
            'observacion'       => '',
            'id_empresa'        => '0992704152001',
            'cantidad'          => '0',
            'estado'            => '0',
            'realizado'         => '0',
            'valor'             => '0',
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => '1234517896',
            'id_usuariomod'     => '1234517896',
            'motivo_descuento'  => '',
            'fecha_tentativa'   => $request['fecha_tentativa'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            //'fecha_orden'      => date('Y-m-d h:i:s'),
        ];

        /*$orden = Examen_Orden::where('id_paciente',$paciente->id)->where('estado','0')->where('pago_online','1')->first();
        if(!is_null($orden)){
        $orden->update($input_ex);
        $detalles = $orden->detalles;
        foreach ($detalles as $d) {
        $d->delete();
        }
        $id_orden = $orden->id;
        }else{
        $id_orden = Examen_Orden::insertGetId($input_ex);
        }*/

        $id_orden = Examen_Orden::insertGetId($input_ex);

        $input_cli = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => '5',
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => '1234517896',
            'id_usuariomod'           => '1234517896',
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();
        //return "holsaaa";
        //return [$paciente->id, $cliente];
        //echo $input_cli;
        //return "<b> .'$input_cli'.</b>";

        if (!is_null($cliente)) {

            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli);
            //return [$cliente];
            //$cliente->update($input_cli);
        } else {

            Ct_Clientes::create($input_cli);
        }

        $pct_dcto          = 0.20;
        $sin_desc = ['94', '95'];
        if (in_array($request->id_protocolo, $sin_desc)) {
            $pct_dcto          = 0.00;
        }

        $total             = 0;
        $descuento_detalle = 0;
        $contador          = 0;

        foreach ($examenes_protocolo as $value) {

            $examen = Examen::find($value->id_examen);
            //dd($value,$examen);
            if (!is_null($examen)) {
                $valor    = $examen->valor;
                $cubre    = 'NO';
                $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                if (!is_null($ex_nivel)) {
                    if ($ex_nivel->valor1 != 0) {

                        $valor = $ex_nivel->valor1;
                        $cubre = 'SI';
                    }
                }

                $p_descuento     = 0;
                $valor_descuento = 0;
                if ($examen->humanlabs == '1') {
                    $p_descuento     = $pct_dcto;
                    $valor_descuento = $valor * $p_descuento;

                    $valor_descuento = round($valor_descuento, 2);
                    //$valor_descuento = floor($valor_descuento * 100)/100;
                    $descuento_detalle += $valor_descuento;
                }

                $input_det = [
                    'id_examen_orden' => $id_orden,
                    'id_examen'       => $examen->id,
                    'cubre'           => $cubre,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                    'id_usuariomod'   => '1234517896',
                    'human_labs'      => $examen->humanlabs,
                    'p_descuento'     => $p_descuento,
                    'valor_descuento' => $valor_descuento,

                ];
                Examen_detalle::create($input_det);
                $total = $total + $valor;
                $contador++;
            }
        }
        if ($request['pres_dom'] == '1') {
            $examen = Examen::find('1203');
            //dd($value,$examen);
            if (!is_null($examen)) {
                $input_det = [
                    'id_examen_orden' => $id_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $examen->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                    'id_usuariomod'   => '1234517896',
                    'human_labs'      => 0,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];
                Examen_detalle::create($input_det);
                $total = $total + $examen->valor;
                $contador++;
            }
        }

        //ACTUALIZA ORDEN
        $total             = round($total, 2);
        $recargo_p         = 0;
        $descuento_detalle = round($descuento_detalle, 2);
        $subtotal_pagar    = $total - $descuento_detalle;

        $recargo_valor = $subtotal_pagar * $recargo_p / 100;
        $recargo_valor = round($recargo_valor, 2);
        $valor_total   = $subtotal_pagar + $recargo_valor;
        $valor_total   = round($valor_total, 2);
        $orden         = Examen_Orden::find($id_orden);
        $input_ex2     = [
            'recargo_valor'   => $recargo_valor,
            'total_valor'     => $valor_total,
            'cantidad'        => $contador,
            'valor'           => $total,
            'descuento_valor' => $descuento_detalle,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => '1234517896',
            'pago_online'     => '1',
            'estado_pago'     => '0',
            'pres_dom'        => $request->pres_dom,
        ];
        $orden->update($input_ex2);
        /*Log_usuario::create([
        'id_usuario'  => '1234517896',
        'ip_usuario'  => $ip_cliente,
        'descripcion' => "GENERA COTIZACION WEB",
        'dato_ant1'   => $paciente->id,
        'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
        ]);*/
        Log_usuario::create([
            'id_usuario'  => $paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "GENERA COTIZACION WEB",
            'dato2'       => $protocolo->nombre,
        ]);
        //return "ok";
        return view('laboratorio.externo.lista_confirmar', ['orden' => $orden, 'detalles' => $orden->detalles]);
    }

    /*
    FUNCIONES PARA EL API DEL BOTON DE PAGOS
     */
    /* POST PROCESO ACTUAL
    public function web_postproceso_pago(Request $request)
    {

    $res        = "";
    $ip_cliente = $_SERVER["REMOTE_ADDR"];

    $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
    $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';

    Log_Api::create([
    'descripcion' => 'llego',
    'dato1'       => $request,
    ]);
    if ($request['xmlreq'] != null) {
    $xmlreq             = base64_decode($request['xmlreq']);
    $status             = explode("=", explode("&", $xmlreq)[0])[1];
    $requestId          = explode("=", explode("&", $xmlreq)[1])[1];
    $refRequestId       = explode("=", explode("&", $xmlreq)[2])[1];
    $orderId            = explode("=", explode("&", $xmlreq)[3])[1];
    $date               = explode("=", explode("&", $xmlreq)[4])[1];
    $signature          = explode("=", explode("&", $xmlreq)[5])[1];
    $signature_to_check = sha1($status . $date . $PAGOSYFACTURAS_APPSECRET);
    if ($signature_to_check == $signature) {
    //
    date_default_timezone_set('America/Guayaquil');
    $pagoenlinea = DB::table('pagosenlinea')->where('request_id', $requestId)->first();

    if (!is_null($pagoenlinea)) {
    $fecha_hoy = date('Y-m-d');
    $id        = $pagoenlinea->id;
    //ACTUALIZA EL REGISTRO DE PAGO
    DB::table('pagosenlinea')
    ->where('request_id', $requestId)
    ->update(['clave' => $orderId, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]);
    } else {
    //AGREGA EL REGISTRO DE PAGO
    $fecha_hoy = date('Y-m-d');

    $id = DB::table('pagosenlinea')->insertGetId(
    ['tipo' => 'examen_orden', 'clave' => $orderId, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]
    );
    }
    //ACTUALIZA LA ORDEN DE EXAMEN SI EL PAGO FUE APROVADO
    if ($status == "APPROVED" || $status == "APPROVED_PARTIAL") {
    //ACTUALIZA EL ESTADO DE LA ORDEN DE EXAMEN A PAGADO 1
    $orden              = examen_orden::find($orderId);
    $orden->estado_pago = "1"; //aqui se actualiza el estado
    $orden->estado      = "1";
    $orden->realizado   = "1";
    $orden->save();
    Log_usuario::create([
    'id_usuario'  => $orden->paciente->id_usuario,
    'ip_usuario'  => $ip_cliente,
    'descripcion' => "LABORATORIO",
    'dato_ant1'   => $orderId,
    'dato1'       => $orden->id_paciente,
    'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN",
    'dato2'       => $requestId,
    ]);
    }
    //ACTUALIZA LOS DETALLES DEL PAGO
    $this->getPaymentInformation($requestId);
    $res = $res . "Id=" . $id; //RETORNA EL ID DEL REGISTRO QUE SE INGRESO
    return $res;
    //}

    } else {
    echo "ERROR";
    }
    }
    } */

    /* NUEVO POST PROCESO */
    public function web_postproceso_pago(Request $request)
    {

        $res        = "";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';

        Log_Api::create([
            'descripcion' => 'llego',
            'dato1'       => $request,
        ]);
        if ($request['xmlreq'] != null) {
            $xmlreq             = base64_decode($request['xmlreq']);
            $status             = explode("=", explode("&", $xmlreq)[0])[1];
            $requestId          = explode("=", explode("&", $xmlreq)[1])[1];
            $refRequestId       = explode("=", explode("&", $xmlreq)[2])[1];
            $orderId            = explode("=", explode("&", $xmlreq)[3])[1];
            $date               = explode("=", explode("&", $xmlreq)[4])[1];
            $signature          = explode("=", explode("&", $xmlreq)[5])[1];
            $signature_to_check = sha1($status . $date . $PAGOSYFACTURAS_APPSECRET);
            if ($signature_to_check == $signature) {
                //
                date_default_timezone_set('America/Guayaquil');
                $pagoenlinea = DB::table('pagosenlinea')->where('request_id', $requestId)->first();

                $SUFIJO_ID_ORDEN = substr($orderId, 0, 3);
                $ID_ORDEN        = substr($orderId, 3);

                if ($SUFIJO_ID_ORDEN != 'ORD' && $SUFIJO_ID_ORDEN != 'MEM') {
                    //SACAR ESTO CUANDO YA NO ESTE EN LA WEB
                    $ID_ORDEN = $orderId;
                }

                if (!is_null($pagoenlinea)) {

                    $fecha_hoy = date('Y-m-d');
                    $id        = $pagoenlinea->id;
                    //ACTUALIZA EL REGISTRO DE PAGO
                    DB::table('pagosenlinea')
                        ->where('request_id', $requestId)
                        ->update(['clave' => $ID_ORDEN, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]);
                } else {
                    //AGREGA EL REGISTRO DE PAGO
                    $fecha_hoy = date('Y-m-d');

                    $tipo_pago = '';
                    if ($SUFIJO_ID_ORDEN == 'MEM') {
                        //PAGO DE MEMBRESIA
                        $tipo_pago = 'pago_membresia';
                    } else {
                        //PAGO DE ORDEN
                        $tipo_pago = 'examen_orden';
                    }

                    $id = DB::table('pagosenlinea')->insertGetId(
                        ['tipo' => $tipo_pago, 'clave' => $ID_ORDEN, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]
                    );
                }

                if ($status == "APPROVED" || $status == "APPROVED_PARTIAL") {
                    if ($SUFIJO_ID_ORDEN == 'MEM') {
                        //PAGO DE MEMBRESIA
                        //use Sis_medico\Contable;
                        $data_array = array(
                            "id_orden" => $ID_ORDEN, //tipo de de orden de venta
                            //"id_usuario"  => $cedula_usuario, //fecha de orden
                        );
                        $response = Contable::update_data($data_array);
                    } else {
                        //ORDEN
                        //ACTUALIZA LA ORDEN DE EXAMEN SI EL PAGO FUE APROVADO
                        //ACTUALIZA EL ESTADO DE LA ORDEN DE EXAMEN A PAGADO 1
                        $orden              = examen_orden::find($ID_ORDEN);
                        $orden->estado_pago = "1"; //aqui se actualiza el estado
                        $orden->estado      = "1";
                        $orden->realizado   = "1";
                        $orden->save();
                        Log_usuario::create([
                            'id_usuario'  => $orden->paciente->id_usuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "LABORATORIO",
                            'dato_ant1'   => $ID_ORDEN,
                            'dato1'       => $orden->id_paciente,
                            'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN",
                            'dato2'       => $requestId,
                        ]);
                    }
                }

                //ACTUALIZA LOS DETALLES DEL PAGO
                $this->getPaymentInformation($requestId);
                $res = $res . "Id=" . $id; //RETORNA EL ID DEL REGISTRO QUE SE INGRESO
                return $res;
                //}

            } else {
                echo "ERROR";
            }
        }
    }

    public function web_retorno_pago(Request $request)
    {
        /*return "<html><head></head><body><script>window.parent.location.href='http://186.70.157.2/sis_medico/public/laboratorio/externo/web/'</script></body></html>";*/
        return "<html><head></head><body><script>location.href='https://labs.ec'</script></body></html>";
    }

    public function web_cancelacion_pago(Request $request)
    {
        /*return "<html><head></head><body><script>window.parent.location.href='http://186.70.157.2/sis_medico/public/laboratorio/externo/web/'</script></body></html>";*/
        return "<html><head></head><body><script>location.href='https://labs.ec'</script></body></html>";
    }

    public function getPaymentInformation($requestId)
    {
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);
        Log_usuario::create([
            'id_usuario'  => '0922290697',
            'ip_usuario'  => 'post_proc',
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => '1',
            'dato1'       => '1',
            'dato_ant2'   => "RESPUESTA DEL POST",
            'dato2'       => "prb",
            'dato_ant4'   => $make_call,
        ]);
        if ($response['status'] != null) {

            //ge payment information
            $payment_details         = $response['details'];
            $payment_nro_comprobante = $response['comprobante'];

            DB::table('pagosenlinea')
                ->where('request_id', $requestId)
                ->update([
                    'estado'          => $response['status']['status'],
                    'interes'         => $response['details']['interest'],
                    'credittype'      => $response['details']['creditType'], //00 corriente - 02 o 03 diferido -
                    'issuer'          => $response['details']['isssuerName'],
                    'paymentmethod'   => $response['details']['paymentMethodName'], //tarjetamarca
                    'lastdigits'      => $response['details']['lastDigits'],
                    'expiration'      => $response['details']['expiration'],
                    'pago_auth'       => $response['details']['authorization'],
                    'nro_comprobante' => $payment_nro_comprobante,
                ]);
        }
    }

    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        //dd($context, $url);
        return file_get_contents($url, false, $context);
    }

    public function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function getDocumentType($document)
    {
        if (strlen($document) == 10) {
            return '05'; //CEDULA
        } else if (strlen($document) == 13) {
            return '04'; //RUC
        } else {
            return '06'; //PASAPORTE
        }
    }

    public function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }

    /*public function carrito_pago($id_orden)//FUNCION QUE INVOCA LO DE JUANK
    {
    $orden = Examen_Orden::find($id_orden);
    $paciente = $orden->paciente;
    $id_paciente = $paciente->id;
    $grupo_fam = Labs_Grupo_Familiar::find($id_paciente);
    if (!is_null($grupo_fam)) {
    $user_aso = User::find($grupo_fam->id_usuario);
    } else {
    $user_aso = User::find($paciente->id_usuario);
    }
    $email = $user_aso->email;
    if(is_null($orden)){
    return ['estado' => "Error"];
    }
    $valor_total = $orden->total_valor;
    /*
    INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
     */
    /*$RUC_LABS='0993075000001';
    $PAGOSYFACTURAS_APPID='V1oW1RHpw8GtxwGoIkuq';
    $PAGOSYFACTURAS_APPSECRET='SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
    $_pasarela_pagos_subtotal = $valor_total;
    $_pasarela_pagos_iva = 0;
    $_pasarela_pagos_total = $valor_total;
    $pyf_checkout_url = 'https://vpos.accroachcode.com/';
    //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
    //detalle(s)
    $pyf_details=array();
    array_push($pyf_details,array(
    "sku"    =>  ''.$id_orden.'',
    "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
    "qty"    =>  1,
    "price"   =>  $_pasarela_pagos_subtotal,
    "tax"   =>  0.00,
    "discount"   =>  0.00,  //falta revisar
    "total"   =>  $_pasarela_pagos_subtotal
    ));
    $celular=$paciente->telefono2;
    if($celular==null){
    $celular='0900000001';
    }
    if(strlen($celular)<10){
    $celular='0900000001';
    }
    if(!is_numeric($celular)){
    $celular='0900000001';
    }
    // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
    $nombres=$paciente->nombre1;
    if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
    $nombres = $nombres.' '.$paciente->nombre2;
    }
    $apellidos=$paciente->apellido2;
    if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
    $apellidos = $apellidos.' '.$paciente->apellido2;
    }
    //json de invocación
    $data_array =  array(
    "company"        => $RUC_LABS,
    "person"         => array(
    "document"      => $paciente->id,
    "documentType"  => $this->getDocumentType($paciente->id),
    "name"          => $this->cleanNames(strtoupper($nombres)),
    "surname"       => $this->cleanNames(strtoupper($apellidos)),
    "email"         => $email,
    "mobile"        => $celular
    ),
    "paymentRequest"  => array(
    "orderId"       => ''.$id_orden.'',
    "description"   => "Compra en linea labs",  //PONER EN CONFIGURACION
    "items"         => array(
    "item"        => $pyf_details //pending
    ),
    "amount"    =>  array(

    "taxes"     =>  array(
    array(
    "kind"   =>  "Iva",
    "amount"   =>  0.00,
    "base"   =>  round($_pasarela_pagos_subtotal,2,PHP_ROUND_HALF_UP )
    )
    ),

    "currency" => "USD",
    "total" => round($_pasarela_pagos_total,2,PHP_ROUND_HALF_UP )
    )
    ),
    "returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
    "cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
    "userAgent" =>  "labs_ec/1"
    );

    $manage = json_encode($data_array);
    $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID,$PAGOSYFACTURAS_APPSECRET);
    $response = json_decode($make_call, true);
    if($response['status']!=null){
    if($response['status']['status']=='success'){
    $pyf_checkout_url = $response['processUrl'];
    }
    else{
    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status='.$response['status']['status'];
    }
    }
    else{
    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
    }
    /*
    FIN DE INVOCACION A API DE BOTON DE PAGOS
     */

    /*
    return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
    //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }*/

    public function carrito_pago($id_orden) //FUNCION QUE INVOCA LO DE JUANK

    {
        //return "hola";
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $orden       = Examen_Orden::find($id_orden);
        $paciente    = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam   = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $email = $orden->email_factura;
        if (is_null($orden)) {
            return ['estado' => "Error"];
        }
        $valor_total = $orden->total_valor;
        $valor       = $orden->valor;
        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_subtotal = round($_pasarela_pagos_subtotal * 100) / 100;
        //$_pasarela_pagos_subtotal = number_format($_pasarela_pagos_subtotal,2,'.','');
        $_pasarela_pagos_iva   = 0;
        $_pasarela_pagos_total = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_total = round($_pasarela_pagos_total * 100) / 100;
        $pyf_checkout_url      = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)

        $datosAdicionales = array();
        array_push($datosAdicionales, array(
            "key"   => "Agentes_Retencion", //los subgiones _ son espacios
            "value" => "Resolucion 1",
        ));
        array_push($datosAdicionales, array(
            "key"   => "Paciente",
            "value" => $orden->id_paciente . " " . $this->cleanNames($orden->paciente->apellido1 . " " . $orden->paciente->nombre1),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Mail",
            "value" => $email,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Ciudad",
            "value" => $this->cleanNames($orden->ciudad_factura),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Direccion",
            "value" => $orden->direccion_factura,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Orden",
            "value" => '' . $orden->id . '',
        ));

        $pyf_details = array();
        /*array_push($pyf_details,array( //VERSION SIN DESCUENTO
        "sku"    =>  ''.$id_orden.'',
        "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
        "qty"    =>  1,
        "price"   =>  $_pasarela_pagos_subtotal,
        "tax"   =>  0.00,
        "discount"   =>  0.00,  //falta revisar
        "total"   =>  $_pasarela_pagos_subtotal,
        ));*/
        /*foreach ($orden->detalles as $value) { //VERSION DESCUENTO 1
        array_push($pyf_details, array(
        "sku"      => '' . $id_orden . '-' . $value->id_examen,
        "name"     => $value->examen->nombre,
        "qty"      => 1,
        "price"    => $value->valor,
        "tax"      => 0.00,
        "discount" => $value->valor_descuento, //falta revisar
        "total"    => ($value->valor * 1) - $value->valor_descuento,
        ));
        }*/
        foreach ($orden->detalles as $value) {
            array_push($pyf_details, array(
                "sku"      => '' . $id_orden . '-' . $value->id_examen,
                "name"     => $value->examen->nombre,
                "qty"      => 1,
                "price"    => floatval($value->valor),
                "discount" => floatval($value->valor_descuento), //falta revisar
                "subtotal" => floatval($value->valor - $value->valor_descuento), //falta revisar
                "tax"      => 0.00,
                "total"    => floatval($value->valor - $value->valor_descuento),
            ));
        }

        $celular = $orden->telefono_factura;
        if ($celular == null) {
            $celular = '0900000001';
        }
        if (strlen($celular) < 10) {
            $celular = '0900000001';
        }
        if (!is_numeric($celular)) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        $nombres  = $orden->nombre_factura;
        $nom      = explode(" ", $nombres);
        $nom_cant = count($nom);
        if ($nom_cant == 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[2] . ' ' . $nom[3];
        } elseif ($nom_cant == 3) {
            $xnombres = $nom[0];
            $xsurname = $nom[1] . ' ' . $nom[2];
        } elseif ($nom_cant == 2) {
            $xnombres = $nom[0];
            $xsurname = $nom[1];
        } elseif ($nom_cant == 1) {
            $xnombres = $nom[0];
            $xsurname = ' ';
        } elseif ($nom_cant > 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[1] . ' ' . $nom[2] . ' ' . $nom_cant[4];
        }

        /*if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
        $nombres = $nombres.' '.$paciente->nombre2;
        }
        $apellidos=$paciente->apellido2;
        if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
        $apellidos = $apellidos.' '.$paciente->apellido2;
        }*/
        //json de invocación

        $data_array = array(
            "company"           => $RUC_LABS,
            "person"            => array(
                "document"     => $orden->cedula_factura,
                "documentType" => $this->getDocumentType($orden->cedula_factura),
                "name"         => $this->cleanNames(strtoupper($xnombres)),
                "surname"      => $this->cleanNames(strtoupper($xsurname)),
                "email"        => $email,
                "mobile"       => $celular,
                /*"address"       => array(
            "street"    =>  $orden->direccion_factura,
            "city"      =>  $orden->ciudad_factura,
            "country"   =>  "EC"
            ), */
            ),
            "paymentRequest"    => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(
                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            //"base"   => $_pasarela_pagos_subtotal,
                            "base"   => 0.00,
                        ),
                    ),
                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "billingParameters" => array(
                "establecimiento" => "001",
                "ptoEmision"      => "002",
                "infoAdicional"   => $datosAdicionales,
                "formaPago"       => "19",
                "plazoDias"       => "10",
            ),
            //"returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
            //"cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
            "returnUrl"         => "https://labs.ec/regresar.php", //URL DE RETORNO
            "cancelUrl"         => "https://labs.ec", //URL DE CANCELACION

            "userAgent"         => "labs_ec/1",
        );

        $manage = json_encode($data_array);
        Log_usuario::create([
            'id_usuario'  => $orden->paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "LLAMADA API",
            'dato2'       => "",
            'dato4'       => $manage,
        ]);
        //return $manage;
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
        //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }

    public function app_carrito_pago($id_orden) //FUNCION QUE INVOCA LO DE JUANK PARA LAS APPS

    {

        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $orden       = Examen_Orden::find($id_orden);
        $paciente    = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam   = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $email = $orden->email_factura;
        if (is_null($orden)) {
            return ['estado' => "Error"];
        }
        $valor_total = $orden->total_valor;
        $valor       = $orden->valor;
        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_subtotal = round($_pasarela_pagos_subtotal * 100) / 100;
        //$_pasarela_pagos_subtotal = number_format($_pasarela_pagos_subtotal,2,'.','');
        $_pasarela_pagos_iva   = 0;
        $_pasarela_pagos_total = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_total = round($_pasarela_pagos_total * 100) / 100;
        $pyf_checkout_url      = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)

        $datosAdicionales = array();
        array_push($datosAdicionales, array(
            "key"   => "Agentes_Retencion", //los subgiones _ son espacios
            "value" => "Resolucion 1",
        ));
        array_push($datosAdicionales, array(
            "key"   => "Paciente",
            "value" => $orden->id_paciente . " " . $this->cleanNames($orden->paciente->apellido1 . " " . $orden->paciente->nombre1),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Mail",
            "value" => $email,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Ciudad",
            "value" => $this->cleanNames($orden->ciudad_factura),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Direccion",
            "value" => $orden->direccion_factura,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Orden",
            "value" => '' . $orden->id . '',
        ));

        $pyf_details = array();
        /*array_push($pyf_details,array( //VERSION SIN DESCUENTO
        "sku"    =>  ''.$id_orden.'',
        "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
        "qty"    =>  1,
        "price"   =>  $_pasarela_pagos_subtotal,
        "tax"   =>  0.00,
        "discount"   =>  0.00,  //falta revisar
        "total"   =>  $_pasarela_pagos_subtotal,
        ));*/
        /*foreach ($orden->detalles as $value) { //VERSION DESCUENTO 1
        array_push($pyf_details, array(
        "sku"      => '' . $id_orden . '-' . $value->id_examen,
        "name"     => $value->examen->nombre,
        "qty"      => 1,
        "price"    => $value->valor,
        "tax"      => 0.00,
        "discount" => $value->valor_descuento, //falta revisar
        "total"    => ($value->valor * 1) - $value->valor_descuento,
        ));
        }*/
        foreach ($orden->detalles as $value) {
            array_push($pyf_details, array(
                "sku"      => '' . $id_orden . '-' . $value->id_examen,
                "name"     => $value->examen->nombre,
                "qty"      => 1,
                "price"    => floatval($value->valor),
                "discount" => floatval($value->valor_descuento), //falta revisar
                "subtotal" => floatval($value->valor - $value->valor_descuento), //falta revisar
                "tax"      => 0.00,
                "total"    => floatval($value->valor - $value->valor_descuento),
            ));
        }

        $celular = $orden->telefono_factura;
        if ($celular == null) {
            $celular = '0900000001';
        }
        if (strlen($celular) < 10) {
            $celular = '0900000001';
        }
        if (!is_numeric($celular)) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        $nombres  = $orden->nombre_factura;
        $nom      = explode(" ", $nombres);
        $nom_cant = count($nom);
        if ($nom_cant == 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[2] . ' ' . $nom[3];
        } elseif ($nom_cant == 3) {
            $xnombres = $nom[0];
            $xsurname = $nom[1] . ' ' . $nom[2];
        } elseif ($nom_cant == 2) {
            $xnombres = $nom[0];
            $xsurname = $nom[1];
        } elseif ($nom_cant == 1) {
            $xnombres = $nom[0];
            $xsurname = ' ';
        } elseif ($nom_cant > 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[1] . ' ' . $nom[2] . ' ' . $nom_cant[4];
        }

        /*if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
        $nombres = $nombres.' '.$paciente->nombre2;
        }
        $apellidos=$paciente->apellido2;
        if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
        $apellidos = $apellidos.' '.$paciente->apellido2;
        }*/
        //json de invocación

        $data_array = array(
            "company"           => $RUC_LABS,
            "person"            => array(
                "document"     => $orden->cedula_factura,
                "documentType" => $this->getDocumentType($orden->cedula_factura),
                "name"         => $this->cleanNames(strtoupper($xnombres)),
                "surname"      => $this->cleanNames(strtoupper($xsurname)),
                "email"        => $email,
                "mobile"       => $celular,
                /*"address"       => array(
            "street"    =>  $orden->direccion_factura,
            "city"      =>  $orden->ciudad_factura,
            "country"   =>  "EC"
            ), */
            ),
            "paymentRequest"    => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(
                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            //"base"   => $_pasarela_pagos_subtotal,
                            "base"   => 0.00,
                        ),
                    ),
                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "billingParameters" => array(
                "establecimiento" => "001",
                "ptoEmision"      => "002",
                "infoAdicional"   => $datosAdicionales,
                "formaPago"       => "19",
                "plazoDias"       => "10",
            ),
            //"returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
            //"cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
            "returnUrl"         => "https://labs.ec/regresar.php", //URL DE RETORNO
            "cancelUrl"         => "https://labs.ec", //URL DE CANCELACION

            "userAgent"         => "labs_ec/1",
        );

        $manage = json_encode($data_array);
        Log_usuario::create([
            'id_usuario'  => $orden->paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "LLAMADA API",
            'dato2'       => "",
            'dato4'       => $manage,
        ]);
        //return $manage;
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
        //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }

    public function buscar_clientes($id)
    {

        $cliente = Ct_Clientes::where('identificacion', $id)->first();
        if (!is_null($cliente)) {
            return ['id' => $cliente->identificacion, 'nombre' => $cliente->nombre, 'telefono' => $cliente->telefono1_representante, 'direccion' => $cliente->direccion_representante, 'email' => $cliente->email_representante, 'ciudad' => $cliente->ciudad_representante, 'resultado' => 'ok'];
        } else {
            return 'no';
        }
    }

    public function vivokey($id_paciente)
    {

        $nuevo = base64_decode(base64_decode(($id_paciente)));
        if (Auth::guest()) {
            $paciente = paciente::find($nuevo);
            return view('paciente_vivokey', ['paciente' => $paciente]);
        } else {
            $rolUsuario = Auth::user()->id_tipo_usuario;
            if ($rolUsuario == 3) {
                return redirect()->route('nd.buscador', ['id_paciente' => $nuevo]);
            }
        }
        $paciente = paciente::find($nuevo);
        return response()->json($paciente);
        //dd($paciente);
        if (is_null($paciente)) {
            return "no exite paciente";
        }

        return redirect()->route('paciente.historia', ['id_paciente' => $nuevo]);
    }
    public function formato_encuestalabs(Request $request)
    {

        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        //$areas         = Area::where("estado", '=', 1)->get();

        // dd($encuesta->id);

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato     = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->where("tipo_encuesta", 1)->get();
            //dd($dato);
            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        $id_master_encuesta = Master_Labs_Encuesta::where("tipo", 1)->first();
        return view('laboratorio/encuesta_laboratorio/formato_encuestalabs', ['id_master_tipo' => $id_master_encuesta, 'arreglo' => $arreglo]);
    }

    public function encuestalabsguardar(Request $request)
    {

        try {
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $fecha_hoy =  date('Y-m-d');
            $verificar = Encuesta_Labs::where('id_paciente', $request['cedula'])->whereBetween('created_at', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where("tipo_master", 1)->first();
            if (!is_null($verificar)) {
                return ['status' => false, 'message' => 'Ya hay un encuesta el dia de hoy'];
            }
            $grupopregunta = DB::table('grupo_pregunta as gp')
                ->join('pregunta_labs as pl', 'gp.id', 'pl.id_grupopregunta')
                ->where('pl.estado', 1)
                ->where('pl.id_grupopregunta', '!=', 4)->get();

            if ($request['cedula'] == null) {
                return ['status' => false, 'message' => 'La cedula es vacia'];
            }
            $valexamen = Examen_Orden::where('id_paciente', $request['cedula'])->get();

            if (is_null($valexamen)) {
                return ['status' => false, 'message' => 'No cuenta con una orden'];
            }
            $date = Carbon::now();

            $encuesta = Encuesta_Labs::create([
                'id_paciente' => $request['cedula'],
                'mes'         => $date->month,
                'anio'        => $date->year,
                'ip_ingreso'  => $ip_cliente,
                'id_orden'    => $valexamen->last()->id,
                'tipo_master' => $request['id_master'],
            ]);
            $i = 0;
            if (!is_null($encuesta)) {
                foreach ($grupopregunta as $value) {
                    $variable = $value->id;
                    $dato = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->where("tipo_encuesta", 1)->get();
                    foreach ($dato as $value2) {
                        if ($i == 4) {
                            DB::table('encuesta_complementolabs')->insert(
                                ['id_encuesta_labs' => $encuesta->id, 'id_pregunta_labs' => $value2->id, 'calificacion' => $request['calificacion'], 'id_grupo' => $value2->id_grupopregunta]
                            );
                        } else {
                            if ($variable == 4) {
                                if (!is_null($request[$value2->id])) {
                                    $respuesta = $request[$value2->id];
                                } else {
                                    $respuesta = 'N/R';
                                }
                            } else {
                                $respuesta = $request[$value2->id];
                            }

                            DB::table('encuesta_complementolabs')->insert(
                                ['id_encuesta_labs' => $encuesta->id, 'id_pregunta_labs' => $value2->id, 'valor' => $respuesta, 'id_grupo' => $value2->id_grupopregunta]
                            );
                        }
                        $i++;
                    }
                }
            }
            return ['status' => true, 'message' => 'Guardado con exito'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function nuevo_formato_guardar(Request $request)//labsencuestanew
    {



        try {
            $master = $request['id_master'];
            if ($request['cedula'] == null) {
                return ['status' => false, 'message' => 'La cédula es vacia'];
            }
            $valexamen = Examen_Orden::where('id_paciente', $request['cedula'])->get();
            if ($valexamen->isEmpty()) {
                return ['status' => false, 'message' => 'No cuenta con una orden'];
            }
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $fecha_hoy =  date('Y-m-d');
            $id_orden = null;
            

            if(isset($request["id_orden"])){
                $id_orden = $request["id_orden"];
                $ip_cliente = "Creado en App";
            }else{
                $id_orden = $valexamen->last()->id;
            }
            $verificar = null;
            if(isset($request["id_orden"])){
                $verificar = Encuesta_Labs::where('id_paciente', $request['cedula'])->where("id_orden", $request["id_orden"])->where("tipo_master", $master)->first();
                if (!is_null($verificar)) {
                    return ['status' => false, 'message' => 'Ya realizo la encuesta'];
                }
            }else{
                $verificar = Encuesta_Labs::where('id_paciente', $request['cedula'])->whereBetween('created_at', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where("tipo_master", $master)->first();
                if (!is_null($verificar)) {
                    return ['status' => false, 'message' => 'Ya hay un encuesta el dia de hoy'];
                }
            }

            
            $grupopregunta = DB::table('grupo_pregunta as gp')
                ->join('pregunta_labs as pl', 'gp.id', 'pl.id_grupopregunta')
                ->where('pl.estado', 1)
                ->where("pl.tipo_encuesta", $master)
                ->select("pl.id as id_labs_pregunta", "gp.id as id")
                ->get();
            
            $date = Carbon::now();
            

            $encuesta = Encuesta_Labs::create([
                'id_paciente' => $request['cedula'],
                'mes'         => $date->month,
                'anio'        => $date->year,
                'ip_ingreso'  => $ip_cliente,
                'id_orden'    => $id_orden,
                'tipo_master' => $master,
            ]);


            if (!is_null($encuesta)) {
                foreach ($grupopregunta as $value) {

                    $valor = '';

                    if (!is_null($request[$value->id_labs_pregunta])) {
                        $valor = $request[$value->id_labs_pregunta];
                    } else {
                        $valor = $request['14'];
                    }

                    DB::table('encuesta_complementolabs')
                        ->insert(['id_encuesta_labs' => $encuesta->id, 'id_pregunta_labs' => $value->id_labs_pregunta, 'valor' => $valor, 'id_grupo' => $value->id]);
                }
            }
            return ['status' => true, 'message' => 'Guardado con exito'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public static function sumarProducto($matrices, $dividendo)
    {


        if (isset($matrices) and $dividendo != 0) {
            $result = 0;
            $matricesCount = count($matrices);
            $matrixSize = count($matrices[0]);

            for ($i = 0; $i < $matrixSize; $i++) {
                $product = 1;
                for ($j = 0; $j < $matricesCount; $j++) {
                    $product *= $matrices[$j][$i];
                }
                $result += $product;
            }

            $res = ($result * 10) / $dividendo;

            return  $res;
        }
    }


    public static function nombreNumber($numero)
    {  
        $cadena = "";

        switch ($numero) {

            case 10:
                $cadena = 'Muy bueno';
                break;

            case 8:
                $cadena = 'Bueno';
                break;

            case 5:
                $cadena = 'Ni bueno ni malo';
                break;


            case 2:
                $cadena = 'Malo';
                break;

            case 0:
                $cadena = 'Muy malo';
                break;

            default:
                break;
        }
        return $cadena;
    }



    public static function excel_nuevo_formato($fecha_desde, $fecha_hasta)
    {

        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");

        $titulos_uno = ['¿Cómo ha sido la información y resolución de dudas que recibió por parte del personal administrativo?', '¿Cómo ha sido la explicación por parte del personal administrativo sobre los diversos medios para consultar los resultados?', '¿Cómo ha sido la amabilidad y respeto que recibió por parte del personal de toma de muestra?', '¿El personal de toma de muestra le consultó su nombre, antes de proceder a tomar las muestras?', '¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?', '¿Las instalaciones estaban limpias y ordenadas?'];


        $titulos_dos = ['No.', 'PACIENTE', 'Pregunta 1', 'Pregunta 2', 'Pregunta 3', 'Pregunta 4', 'Pregunta 5', 'Pregunta 6', 'Fecha Creacion','Comentario'];
        $titulos_tres = ['PREGUNTA', 'CRITERIOS DE EVALUACIÓN', 'MUY MALO', 'MALO', 'NI BUENO NI MALO ', 'BUENO', 'MUY BUENO', 'Total de Respuestas recibidas por pregunta', 'Indicador sin ponderar por pregunta'];
        $titulos_cuatro = ['PREGUNTA', 'CRITERIOS DE EVALUACIÓN', 'SI', 'NO', '', '', "", 'Total de Respuestas recibidas por pregunta', 'Indicador sin ponderar por pregunta'];
        //TRAER LOS 5 ULTIMAS ENCUESTAS

        $query_cinco_ultimos_pacientes = Encuesta_Labs::join("paciente", "paciente.id", "encuesta_labs.id_paciente")
            ->where("encuesta_labs.tipo_master", 2)
            ->select("paciente.nombre1", "paciente.nombre2", "paciente.apellido1", "paciente.apellido2", "encuesta_labs.*")
            ->whereBetween("encuesta_labs.created_at", [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])
            ->get();

        Excel::create('REPORTES DE ENCUESTAS LABS', function ($excel) use ($posicion, $titulos_uno, $titulos_dos, $query_cinco_ultimos_pacientes, $titulos_tres, $titulos_cuatro) {
            $excel->sheet('PONDERACIÓN', function ($sheet) use ($posicion, $titulos_uno, $titulos_dos, $query_cinco_ultimos_pacientes, $titulos_tres, $titulos_cuatro) {
                $sheet->mergeCells('A1:T1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Encuestas LABS');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('A2:T2');
                $sheet->cell('A2', function ($cell) {
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                });
                $comienzo = 3;

                $sheet->cell('D' . $comienzo, function ($cell) {
                    $cell->setValue("No.");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('E' . $comienzo . ':L' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) {
                    $cell->setValue("Pregunta");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                for ($i = 0; $i < count($titulos_uno); $i++) {
                    $sheet->cell('D' . $comienzo, function ($cell) use ($i) {
                        $cell->setValue($i + 1);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('E' . $comienzo . ':L' . $comienzo);
                    $sheet->cell('E' . $comienzo, function ($cell) use ($titulos_uno, $i) {
                        $cell->setValue($titulos_uno[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;
                }

                $comienzo++;

                for ($i = 0; $i < count($titulos_dos); $i++) {
                    $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $titulos_dos) {
                        $cell->setValue($titulos_dos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;


                $arrayPregunta = [
                    0 => [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    ],
                    1 => [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    ],
                    2 => [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    ],
                ];

                $arrayPregunta2 = [

                    0 => [
                        0 => 0,
                        1 => 0,

                    ],
                    1 => [
                        0 => 0,
                        1 => 0,

                    ],
                    2 => [
                        0 => 0,
                        1 => 0,

                    ],
                ];

                foreach ($query_cinco_ultimos_pacientes as $key => $val) {
                    $complemento = Encuesta_Complementolabs::where("id_encuesta_labs", $val->id)->get();
                    $array = [];
                    foreach ($complemento as $res) {
                        if (in_array($res->id_pregunta_labs, [7, 8, 9, 15, 10, 11])) {

                            switch ($res->valor) {
                                case 0:
                                    array_push($array, '0');
                                    break;

                                case 2:
                                    array_push($array, '2');
                                    break;

                                case 5:
                                    array_push($array, '5');
                                    break;

                                case 8:
                                    array_push($array, '8');
                                    break;
                                case 10:
                                    array_push($array, '10');
                                    break;
                                case '10':
                                    array_push($array, '10');
                                    break;
                                case '0':
                                    array_push($array, '0');
                                    break;
                                default:
                                    array_push($array, '');
                            }
                        }
                    }

                   
                    if ($array[0] == '0') {
                        $arrayPregunta[0][0] = $arrayPregunta[0][0] +  1;
                    } elseif ($array[0] == '2') {
                        $arrayPregunta[0][1] = $arrayPregunta[0][1] +  1;
                    } elseif ($array[0] == '5') {
                        $arrayPregunta[0][2] = $arrayPregunta[0][2] +  1;
                    } elseif ($array[0] == '8') {
                        $arrayPregunta[0][3] = $arrayPregunta[0][3] +  1;
                    } elseif ($array[0] == '10') {

                        $arrayPregunta[0][4] = $arrayPregunta[0][4] +  1;
                    }

                    if ($array[1] == '0') {
                        $arrayPregunta[1][0] = $arrayPregunta[1][0] +  1;
                    } elseif ($array[1] == '2') {
                        $arrayPregunta[1][1] = $arrayPregunta[1][1] +  1;
                    } elseif ($array[1] == '5') {
                        $arrayPregunta[1][2] = $arrayPregunta[1][2] +  1;
                    } elseif ($array[1] == '8') {
                        $arrayPregunta[1][3] = $arrayPregunta[1][3] +  1;
                    } elseif ($array[1] == '10') {
                        $arrayPregunta[1][4] = $arrayPregunta[1][4] +  1;
                    }


                    if ($array[2] == '0') {
                        $arrayPregunta[2][0] = $arrayPregunta[2][0] +  1;
                    } elseif ($array[2] == '2') {
                        $arrayPregunta[2][1] = $arrayPregunta[2][1] +  1;
                    } elseif ($array[2] == '5') {
                        $arrayPregunta[2][2] = $arrayPregunta[2][2] +  1;
                    } elseif ($array[2] == '8') {
                        $arrayPregunta[2][3] = $arrayPregunta[2][3] +  1;
                    } elseif ($array[2] == '10') {
                        $arrayPregunta[2][4] = $arrayPregunta[2][4] +  1;
                    }



                    if ($array[3] == '10') {
                        $arrayPregunta2[0][0] = $arrayPregunta2[0][0] +  1;
                    } elseif ($array[3] == '0') {

                        $arrayPregunta2[0][1] = $arrayPregunta2[0][1] +  1;
                    }



                    if ($array[4] == '10') {
                        $arrayPregunta2[1][0] = $arrayPregunta2[1][0] + 1;
                    } elseif ($array[4] == '0') {
                        $arrayPregunta2[1][1] = $arrayPregunta2[1][1] + 1;
                    }

                    if ($array[5] == '10') {

                        $arrayPregunta2[2][0] = $arrayPregunta2[2][0] + 1;
                    } elseif ($array[5] == '0') {

                        $arrayPregunta2[2][1] = $arrayPregunta2[2][1] + 1;
                    }
                    $arr = [$key + 1, 
                        $val->nombre1 . ' ' . $val->nombre2 . ' ' . $val->apellido1 . ' ' . $val->apellido2, 
                        SinLoginController::nombreNumber($array[0]), 
                        SinLoginController::nombreNumber($array[1]), 
                        SinLoginController::nombreNumber($array[2]), 
                        SinLoginController::nombreNumber($array[3]), 
                        SinLoginController::nombreNumber($array[4]), 
                        SinLoginController::nombreNumber($array[5]), 
                        date("Y-m-d h:i:s", strtotime(($val->created_at))), 
                        $complemento[3]->valor];
                    for ($i = 0; $i < count($arr); $i++) {
                        $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $arr) {
                            $cell->setValue($arr[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                
                
                //primera

                $suma1 = array_sum($arrayPregunta[0]);
                $suma2 = array_sum($arrayPregunta[1]);
                $suma3 = array_sum($arrayPregunta[2]);

                //segunda

                $suma5 = array_sum($arrayPregunta2[0]);
                $suma6 = array_sum($arrayPregunta2[1]);
                $suma7 = array_sum($arrayPregunta2[2]);
                
               

                $matriz1 = [$arrayPregunta[0],[0,2,5,8,10]];
                $matriz2 = [$arrayPregunta[1],[0,2,5,8,10]];
                $matriz3 = [$arrayPregunta[2],[0,2,5,8,10]];
               


                $matriz5 = [$arrayPregunta2[0],[10,0]];
                $matriz6 = [$arrayPregunta2[1],[10,0]];
                $matriz7 = [$arrayPregunta2[2],[10,0]];

                $function = new SinLoginController;
                $sumarProducto1 = SinLoginController::sumarProducto($matriz1,$suma1);

                $sumarProducto2 = SinLoginController::sumarProducto($matriz2,$suma2);

                $sumarProducto3 = SinLoginController::sumarProducto($matriz3,$suma3);

                $sumarProducto4 = SinLoginController::sumarProducto($matriz7,$suma7);

                $sumarProducto5 = SinLoginController::sumarProducto($matriz5,$suma5);

                $sumarProducto6 = SinLoginController::sumarProducto($matriz6,$suma6);
                
                $sumarTotal = ($sumarProducto1+ $sumarProducto2 +$sumarProducto3 +$sumarProducto4 +$sumarProducto5 + $sumarProducto6)/6;



                array_push($arrayPregunta[0],array_sum($arrayPregunta[0]),$sumarProducto1);
                array_push($arrayPregunta[1],array_sum($arrayPregunta[1]),$sumarProducto2);
                array_push($arrayPregunta[2],array_sum($arrayPregunta[2]),$sumarProducto3);


              
                
                


                $newArray = [
                    0=>['¿Cómo ha sido la información y resolución de dudas que recibió por parte del personal administrativo?' =>  $arrayPregunta[0]],
                    1=>['¿Cómo ha sido la explicación por parte del personal administrativo sobre los diversos medios para consultar los resultados?' => $arrayPregunta[1]],
                    2=>['¿Cómo ha sido la amabilidad y respeto que recibió por parte del personal de toma de muestra?' => $arrayPregunta[2]],
                    
                ];

                $newArray1 = [
                    0=>['¿El personal de toma de muestra le consultó su nombre, antes de proceder a tomar las muestras?'=>$arrayPregunta2[0]],
                    1=>['¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?'=>$arrayPregunta2[1]],
                    2=>['¿Las instalaciones estaban limpias y ordenadas?'=>$arrayPregunta2[2]],
                ];

                $comienzo++;

                for ($i = 0; $i < count($titulos_tres); $i++) {
                    $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $titulos_tres) {
                        $cell->setValue($titulos_tres[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#ACB9CA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
              
                foreach ($newArray as $pregunta=>$valores) {
                   
                    $sheet->cell($posicion[1] . $comienzo, function ($cell) use ($pregunta) {
                        $cell->setValue($pregunta+1);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    foreach($valores as $i=>$r){
                        $sheet->cell($posicion[2] . $comienzo, function ($cell) use ($i) {
                            $cell->setValue($i);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    foreach($r as $i=>$val){

                        $sheet->cell($posicion[$i+3] . $comienzo, function ($cell) use ($val) {
                            $cell->setValue(is_numeric($val) ? round($val,2) : $val);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }


                for ($i = 0; $i < count($titulos_cuatro); $i++) {
                    $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $titulos_cuatro) {
                        $cell->setValue($titulos_cuatro[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#ACB9CA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;
                //dd($newArray1);
                array_push($newArray1[0]['¿El personal de toma de muestra le consultó su nombre, antes de proceder a tomar las muestras?'],0,0,0);
                array_push($newArray1[1]['¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?'],0,0,0);
                array_push($newArray1[2]['¿Las instalaciones estaban limpias y ordenadas?'],0,0,0);


                
             

                array_push($newArray1[0]['¿El personal de toma de muestra le consultó su nombre, antes de proceder a tomar las muestras?'],$suma5,$sumarProducto5);

                array_push($newArray1[1]['¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?'],$suma6,$sumarProducto6);
                array_push($newArray1[2]['¿Las instalaciones estaban limpias y ordenadas?'],$suma7,$sumarProducto4);

               
                foreach ($newArray1 as $pregunta=>$valores) {
                   
                    $sheet->cell($posicion[1] . $comienzo, function ($cell) use ($pregunta) {
                        $cell->setValue($pregunta+1);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    foreach($valores as $i=>$r){
                        $sheet->cell($posicion[2] . $comienzo, function ($cell) use ($i) {
                            $cell->setValue($i);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    foreach($r as $i=>$val){

                        $sheet->cell($posicion[$i+3] . $comienzo, function ($cell) use ($val) {
                            $cell->setValue(is_numeric($val) ? round($val,2) : $val);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
               
                $sheet->cell('I'.$comienzo, function ($cell) {
                    $cell->setValue("Indicador mensual sin ponderación:");
                    $cell->setFontWeight('bold');
                    $cell->setBackground("FFFF00");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J'.$comienzo, function ($cell) use($sumarTotal){
                    $cell->setValue(round($sumarTotal,2));
                    $cell->setFontWeight('bold');
                    $cell->setBackground("FFFF00");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                


                $sheet->setWidth(array(

                    'E'     => 50,
                    'B'     => 15,
                    'C'     => 15,
                    'D'     => 15,
                    'E'     => 15,
                    'F'     => 15,
                    'G'     => 15,
                    'H'     => 15,
                    'I'     => 15,
                    'J'     => 15,
                    'K'     => 15,
                ));
            });


            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $excel->getActiveSheet()->getStyle('G4')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B18:H900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I5:I900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('K5:K900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('J5:J900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L5:L900')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N6')->getAlignment()->setWrapText(true);
            SinLoginController::informelabs($excel, $posicion, $titulos_uno, $titulos_dos, $query_cinco_ultimos_pacientes);
        })->export('xlsx');
    }

    public static function informelabs($excel, $posicion, $titulos_uno, $titulos_dos, $query_cinco_ultimos_pacientes)
    {
        $excel->sheet('INFORME', function ($sheet) use ($posicion, $titulos_uno, $titulos_dos, $query_cinco_ultimos_pacientes) {
            $sheet->mergeCells('A1:T1');
            $sheet->cell('A1', function ($cell) {
                $cell->setValue('Encuestas LABS');
                $cell->setFontWeight('bold');
            });
            $sheet->mergeCells('A2:T2');
            $sheet->cell('A2', function ($cell) {
                $cell->setValue('BASE DE PREGUNTAS');
                $cell->setFontWeight('bold');
            });
            $comienzo = 3;

            $sheet->cell('D' . $comienzo, function ($cell) {
                $cell->setValue("No.");
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->mergeCells('E' . $comienzo . ':L' . $comienzo);
            $sheet->cell('E' . $comienzo, function ($cell) {
                $cell->setValue("Pregunta");
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $comienzo++;
            for ($i = 0; $i < count($titulos_uno); $i++) {
                $sheet->cell('D' . $comienzo, function ($cell) use ($i) {
                    $cell->setValue($i + 1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E' . $comienzo . ':L' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) use ($titulos_uno, $i) {
                    $cell->setValue($titulos_uno[$i]);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
            }
            $comienzo++;
            $function = new SinLoginController;
            for ($i = 0; $i < count($titulos_dos); $i++) {
                $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $titulos_dos) {
                    $cell->setValue($titulos_dos[$i]);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            }
            $comienzo++;

            $array = [];

            $arrayPregunta = [
                0 => [
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                ],
                1 => [
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                ],
                2 => [
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                ],
            ];

                $arrayPregunta2 = [

                    0 => [
                        0 => 0,
                        1 => 0,

                    ],
                    1 => [
                        0 => 0,
                        1 => 0,

                    ],
                    2 => [
                        0 => 0,
                        1 => 0,

                    ],
                ];


            foreach ($query_cinco_ultimos_pacientes as $key => $val) {
                $complemento = Encuesta_Complementolabs::where("id_encuesta_labs", $val->id)->get();
                $array = [];
                foreach ($complemento as $res) {
                    if (in_array($res->id_pregunta_labs, [7, 8, 9, 15, 10, 11])) {
                        switch ($res->valor) {
                            case 0:
                                array_push($array, '0');
                                break;

                            case 2:
                                array_push($array, '2');
                                break;

                            case 5:
                                array_push($array, '5');
                                break;

                            case 8:
                                array_push($array, '8');
                                break;
                            case 10:
                                array_push($array, '10');
                                break;
                            case '10':
                                array_push($array, '10');
                                break;
                            case '0':
                                array_push($array, '0');
                                break;
                            default:
                                array_push($array, '');
                        }
                    }
                }

                if ($array[0] == '0') {
                    $arrayPregunta[0][0] = $arrayPregunta[0][0] +  1;
                } elseif ($array[0] == '2') {
                    $arrayPregunta[0][1] = $arrayPregunta[0][1] +  1;
                } elseif ($array[0] == '5') {
                    $arrayPregunta[0][2] = $arrayPregunta[0][2] +  1;
                } elseif ($array[0] == '8') {
                    $arrayPregunta[0][3] = $arrayPregunta[0][3] +  1;
                } elseif ($array[0] == '10') {

                    $arrayPregunta[0][4] = $arrayPregunta[0][4] +  1;
                }

                if ($array[1] == '0') {
                    $arrayPregunta[1][0] = $arrayPregunta[1][0] +  1;
                } elseif ($array[1] == '2') {
                    $arrayPregunta[1][1] = $arrayPregunta[1][1] +  1;
                } elseif ($array[1] == '5') {
                    $arrayPregunta[1][2] = $arrayPregunta[1][2] +  1;
                } elseif ($array[1] == '8') {
                    $arrayPregunta[1][3] = $arrayPregunta[1][3] +  1;
                } elseif ($array[1] == '10') {
                    $arrayPregunta[1][4] = $arrayPregunta[1][4] +  1;
                }


                if ($array[2] == '0') {
                    $arrayPregunta[2][0] = $arrayPregunta[2][0] +  1;
                } elseif ($array[2] == '2') {
                    $arrayPregunta[2][1] = $arrayPregunta[2][1] +  1;
                } elseif ($array[2] == '5') {
                    $arrayPregunta[2][2] = $arrayPregunta[2][2] +  1;
                } elseif ($array[2] == '8') {
                    $arrayPregunta[2][3] = $arrayPregunta[2][3] +  1;
                } elseif ($array[2] == '10') {
                    $arrayPregunta[2][4] = $arrayPregunta[2][4] +  1;
                }

               

                if ($array[3] == '10') {
                    $arrayPregunta2[0][0] = $arrayPregunta2[0][0] +  1;
                } elseif ($array[3] == '0') {

                    $arrayPregunta2[0][1] = $arrayPregunta2[0][1] +  1;
                }



                if ($array[4] == '10') {
                    $arrayPregunta2[1][0] = $arrayPregunta2[1][0] + 1;
                } elseif ($array[4] == '0') {
                    $arrayPregunta2[1][1] = $arrayPregunta2[1][1] + 1;
                }

                if ($array[5] == '10') {
                    $arrayPregunta2[2][0] = $arrayPregunta2[2][0] + 1;
                } elseif ($array[5] == '0') {

                    $arrayPregunta2[2][1] = $arrayPregunta2[2][1] + 1;
                }



                $arr = [
                    $key + 1,
                    $val->nombre1 . ' ' . $val->nombre2 . ' ' . $val->apellido1 . ' ' . $val->apellido2,
                    SinLoginController::nombreNumber($array[0]), 
                    SinLoginController::nombreNumber($array[1]), 
                    SinLoginController::nombreNumber($array[2]), 
                    SinLoginController::nombreNumber($array[3]), 
                    SinLoginController::nombreNumber($array[4]), 
                    SinLoginController::nombreNumber($array[5]), 
                    date("Y-m-d h:i:s", strtotime(($val->created_at))),
                    $complemento[3]->valor
                ];
                for ($i = 0; $i < count($arr); $i++) {

                    if ($i > 0) {


                        if ($arr[$i] === 10) {
                            $arr[$i] = 'Muy Bueno';
                        } elseif ($arr[$i] === 8) {
                            $arr[$i] = 'Bueno';
                        } elseif ($arr[$i] === 5) {
                            $arr[$i] = 'Ni Bueno ni Malo';
                        } elseif ($arr[$i] === 2) {
                            $arr[$i] = 'Malo';
                        } elseif ($arr[$i] === 0) {

                            $arr[$i] = 'Malo Malo';
                        }
                    }

                    $sheet->cell($posicion[$i + 1] . $comienzo, function ($cell) use ($i, $arr) {
                        $cell->setValue($arr[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
            }


            $sheet->setWidth(array(

                'E'     => 50,
                'B'     => 15,
                'C'     => 35,
                'D'     => 15,
                'E'     => 15,
                'F'     => 15,
                'G'     => 15,
                'H'     => 15,
                'I'     => 15,
                'J'     => 15,
                'K'     => 15,
            ));
        });
    }

    public function encuestas2_labs()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        //$areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta2', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function verificar_paciente(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = json_decode($request->getContent(), true);
                if (!isset($elemento['id_paciente'])) {
                    return response()->json([
                        'result'    => '12',
                        'status'    => '0',
                        'resultado' => '',
                        'message'   => 'Variables Incompletas.',
                    ]);
                }
                $id_paciente = $elemento['id_paciente'];
                $historia = Historiaclinica::where('id_paciente', $id_paciente)->count();

                if ($historia > 0) {
                    $respuesta = "Si";
                } else {
                    $respuesta = "No";
                }

                return response()->json([
                    'result'    => '1',
                    'status'    => '0',
                    'resultado' => '',
                    'message'   => $respuesta,
                ]);
                //return json_encode($ordenes_anio);
            } else {
                return response()->json([
                    'result'    => '10',
                    'status'    => '0',
                    'resultado' => '',
                    'message'   => 'Credenciales Invalidas.',
                ]);
            }
        } else {
            return response()->json([
                'result'    => '10',
                'status'    => '0',
                'resultado' => '',
                'message'   => 'Credenciales Invalidas.',
            ]);
        }
    }


    public function nuevo_formato($id)
    {

        $empresa = Empresa::where('prioridad', '1')->where('estado', '1')->first();
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $encuesta      = Master_encuesta::find($id);

        $tiempos = explode('+', $encuesta->tiempos);


        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato     = Pregunta::where('id_grupopregunta', '=', $variable)
                ->where('pregunta.estado', '=', '1')
                ->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')
                ->where('pe.id_masterencuesta', $id)
                ->select('pregunta.*')
                ->get();
            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        //    dd($arreglo);



        return view('nueva_encuesta/index_encuesta', ['empresa' => $empresa, 'encuesta' => $encuesta, 'arreglo' => $arreglo, 'tiempos' => $tiempos, 'id' => $id]);
    }

    public function nuevo_formato_encuesta()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato     = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->where("tipo_encuesta", 2)->get();
            if ($dato->isNotEmpty()) {
                $arreglo[$contador] = $dato;
                $contador           = $contador + 1;
            }
        }
        $id_master_tipo = Master_Labs_Encuesta::where("tipo", 2)->first();
        return view('laboratorio/nueva_encuesta_labs/index', ['arreglo' => $arreglo, 'id_master_tipo' => $id_master_tipo]);
    }
    public function encuesta_labs($id)//labsencuestanew
    {
        $grupopregunta = GrupoPregunta::whereNotNull('orden')->orderby('orden','ASC')->get();
        $contador      = 0;
        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato = Preguntas_Labs::where('id_grupopregunta', '=', $variable)
                ->where('pregunta_labs.estado', '=', '1')
                ->where("tipo_encuesta", $id)
                ->get();

            if ($dato->isNotEmpty()) {
                $arr_p = array();
                foreach ($dato as $d) {
                    $valores = $d->pregunta_valor;
                    $arr_valores = array();
                    foreach ($valores as $val) {
                        $arr_val = [
                            "nombre" => $val->nombre_opcion->nombre,
                            "valor" => $val->valor,
                        ];
                        array_push($arr_valores, $arr_val);
                    }
                    $arr = [
                        "id" => $d->id,
                        "pregunta" => $d->nombre,
                        "id_grupopregunta" => $d->id_grupopregunta,
                        "valores" =>  $arr_valores
                    ];
                    array_push($arr_p, $arr);
                }
                $arreglo[$contador] = $arr_p;
                $contador           = $contador + 1;
            }
        }
        //dd($arreglo);

        $id_master_tipo = Master_Labs_Encuesta::where("tipo", $id)->first();
        return view('laboratorio/nueva_encuesta_labs/index_encuesta', ['arreglo' => $arreglo, 'id_master_tipo' => $id_master_tipo]);
    }


    public function encuesta_labs_app($id, $cedula, $id_orden )//labsencuestanew
    {
        // $id_master_tipo = Master_Labs_Encuesta::where("tipo", $id)->first();
        return view('laboratorio/nueva_encuesta_labs/app/encuesta',["cedula" => $cedula, "id_orden" => $id_orden] );
    }

    public static function encuestas_nuevo_guardar(Request $request)
    {

        $id_master  = $request->id_master;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            if ($request['cedula'] == null) {
                //return "1";
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => "Ingrese su número de cédula"];
            }

            $fecha_hoy = date('Y-m-d');
            $master    = $request['id_master'];

            if ($master == 3) {
                $master = 0;
            }

            if ($master == 4) {
                $master = 1;
            }

            if ($request['cedula'] != null) {
                $paciente = Paciente::find($request['cedula']);

                if ($paciente == null) {
                    //return "5";
                    DB::rollBack();
                    return ['respuesta' => 'error', 'msj' => "Paciente no registrado"];
                }
            }

            //dd($master);
            $numero      = 1;
            $no_agendado = [];
            if ($request['id_master'] == 3) {
                $no_agendado = Agenda::where('id_paciente', $request['cedula'])->where('proc_consul', 0)->whereBetween('fechaini', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where('estado', '1')->whereNotIn('id_doctor1',['4444444444', '444444444A', '444444444B'])->get();
            }
            if ($request['id_master'] == 4) {
                $no_agendado = Agenda::where('id_paciente', $request['cedula'])->where('proc_consul', 1)->whereBetween('fechaini', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where('estado', '1')->whereNotIn('id_doctor1',['4444444444', '444444444A', '444444444B'])->get();
            }

            // dd($no_agendado);

            
            if (is_null($no_agendado) or empty($no_agendado) or $no_agendado == "" or count($no_agendado) == 0 or $no_agendado == null or !$no_agendado) {
                //return "2";
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => "No tiene cita agendada para hoy"];
            }


            if (count($no_agendado) == 1) {
                if ($no_agendado[0]->proc_consul != $master) {
                    // return "4";
                    DB::rollBack();
                    return ['respuesta' => 'error', 'msj' => "No se encuentra agendado para este tipo de atención"];
                }
            }

            if (count($no_agendado) > 1) {

                for ($i = 0; $i < count($no_agendado); $i++) {

                    if ($no_agendado[$i]->proc_consul != $master) {

                        $numero++;
                    }
                }

                if ($numero == 0) {

                    //return "2";
                    DB::rollBack();
                    return ['respuesta' => 'error', 'msj' => "No tiene cita agendada para hoy"];
                }
            }

            $grupopregunta = GrupoPregunta::all();
            //return $grupopregunta;
            foreach ($grupopregunta as $value) {
                $variable = $value->id;
                if ($value->id != 4) {
                    //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
                    $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();
                    //return $dato;
                    foreach ($dato as $value2) {
                        $flag = false;
                        if ($request[$value2->id] == null) {
                            $flag = true;
                            break;
                        }
                    }
                    if ($flag) {
                        //return "3";
                        DB::rollBack();
                        return ['respuesta' => 'error', 'msj' => "Complete todo el formulario"];
                    }
                }
            }

            $id = DB::table('encuesta_1')->insertGetId(
                ['id_agenda' => $no_agendado->last()->id, 'ip_ingreso' => $ip_cliente, 'id_area' => $id_master, 'anio' => substr(date('Y-m-d'), 0, 4), 'mes' => substr(date('Y-m-d'), 5, 2), 'id_paciente' => $request['cedula']]
            );
            //return $id;

            foreach ($grupopregunta as $value) {
                $variable = $value->id;
                //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
                $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();

                //return $dato;
                foreach ($dato as $value2) {

                    DB::table('encuesta_complemento')->insert(
                        ['id_encuesta_1' => $id, 'id_pregunta' => $value2->id, 'valor' => $request[$value2->id], 'id_grupo' => $value2->id_grupopregunta, 'valor2' => $request['val2' . $value2->id]]
                    );
                }
            }

            //return "ok";

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Gracias por su confianza y tiempo! Si tiene algún comentario, sugerencia o queja no dude en hacernos saber mediante el Buzón de Sugerencias.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function encuesta_boton($id)
    {
        //dd($id);

        $encuestas = Master_encuesta::where('estado', 1)->where('id', $id)->first();
        //dd($encuestas);
        if (is_null($encuestas)) {
            // dd("esta vacio");
            $encuestas = Master_encuesta::where('estado', 1)->where('id', 1)->first();
            return view('nueva_encuesta/encuesta_boton', ['encuestas' => $encuestas]);
        } else {
            return view('nueva_encuesta/encuesta_boton', ['encuestas' => $encuestas]);
        }
    }
    public function index_form_preadmision()
    {
        $empresa = Empresa::where('estado', 1)->where('prioridad', 1)->first();

        $lsexo = Form_Preadmision_Lista::where('id_grupo', 1)->where('estado', 1)->get();
        $lestado_civil = Form_Preadmision_Lista::where('id_grupo', 2)->where('estado', 1)->get();
        $lgrupo_sanguineo = Form_Preadmision_Lista::where('id_grupo', 3)->where('estado', 1)->get();
        $lrespuesta = Form_Preadmision_Lista::where('id_grupo', 4)->where('estado', 1)->get();
        $lfrecuencia1 = Form_Preadmision_Lista::where('id_grupo', 5)->where('estado', 1)->get();
        $lfrecuencia2 = Form_Preadmision_Lista::where('id_grupo', 6)->where('estado', 1)->get();
        $lfrecuencia3 = Form_Preadmision_Lista::where('id_grupo', 7)->where('estado', 1)->get();
        $lfrecuencia4 = Form_Preadmision_Lista::where('id_grupo', 8)->where('estado', 1)->get();
        $lantecedentes = Form_Preadmision_Lista::where('id_grupo', 9)->where('estado', 1)->get();
        $lparentesco = Form_Preadmision_Lista::where('id_grupo', 10)->where('estado', 1)->get();
        $lvacunas = Form_Preadmision_Lista::where('id_grupo', 11)->where('estado', 1)->get();
        $lsintomas = Form_Preadmision_Lista::where('id_grupo', 12)->where('estado', 1)->get();
        $ltiempo_sintoma = Form_Preadmision_Lista::where('id_grupo', 13)->where('estado', 1)->get();
        $lfrecuencia = Form_Preadmision_Lista::where('id_grupo', 14)->where('estado', 1)->get();
        //$alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();


        return view('form_preadmision/index', ['empresa' => $empresa, 'lsexo' => $lsexo, 'lestado_civil' => $lestado_civil, 'lgrupo_sanguineo' => $lgrupo_sanguineo, 'lrespuesta' => $lrespuesta, 'lfrecuencia1' => $lfrecuencia1, 'lfrecuencia2' => $lfrecuencia2, 'lfrecuencia3' => $lfrecuencia3, 'lfrecuencia4' => $lfrecuencia4, 'lantecedentes' => $lantecedentes, 'lparentesco' => $lparentesco, 'lvacunas' => $lvacunas, 'lsintomas' => $lsintomas, 'ltiempo_sintoma' => $ltiempo_sintoma, 'lfrecuencia' => $lfrecuencia]);
    }

    public function buscar_paciente($id_paciente)
    {
        $data = Paciente::find($id_paciente);

        if (!is_null($data)) {
            $usuario = User::find($id_paciente);
            $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();
            if (is_null($alergiasxpac)) {
                $arr_alergias = [];
            } else {
                $arr_alergias = [];

                foreach ($alergiasxpac as  $value) {
                    $arr = [
                        'id_principio_activo'  => $value->id_principio_activo,
                        'nombre'  => $value->principio_activo->nombre,
                    ];
                    array_push($arr_alergias, $arr);
                }
            }

            $habito = Paciente_Habito::where('id_paciente', $id_paciente)->where('estado', 1)->first();

            $ant_familiares = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('estado', 1)->where('tipo', 1)->get();
            $ant_patologicos = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('estado', 1)->where('tipo', 2)->get();
            $ant_quirurgicos = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('estado', 1)->where('tipo', 3)->get();
            $medicamentos = Paciente_Medicamento::where('id_paciente', $id_paciente)->where('estado', 1)->get();

            $arr = [
                "nombre1" => $data->nombre1,
                "nombre2" => $data->nombre2,
                "apellido1" => $data->apellido1,
                "apellido2" => $data->apellido2,
                "email" => $usuario->email,
                "telefono1" => $data->telefono1,
                "telefono2" => $data->telefono2,
                "id" => $data->id,
                "ocupacion" => $data->ocupacion,
                "sexo" => $data->sexo,
                "estado_civil" => $data->estadocivil,
                "fecha_nacimiento" => $data->fecha_nacimiento,
                "ciudad" => $data->ciudad,
                "direccion" => $data->direccion,
                "grupo_sanguineo" => $data->gruposanguineo,
                "transfusion" => $data->transfusion,
                "trabajo" => $data->trabajo,
                "alergias" => $arr_alergias,
                "fuma" => !is_null($habito) ? $habito->fuma : null,
                "frecuencia_fuma" => !is_null($habito) ? $habito->frecuencia_fuma : null,
                "alcohol" => !is_null($habito) ? $habito->alcohol : null,
                "frecuencia_alcohol" => !is_null($habito) ? $habito->frecuencia_alcohol : null,
                "drogas" => !is_null($habito) ? $habito->drogas : null,
                "frecuencia_drogas" => !is_null($habito) ? $habito->frecuencia_drogas : null,
                "actividad_fisica" => !is_null($habito) ? $habito->actividad_fisica : null,
                "frecuencia_actividad" => !is_null($habito) ? $habito->frecuencia_actividad : null,
                "alimentacion" => !is_null($habito) ? $habito->alimentacion : null,
                "frecuencia_alimentacion" => !is_null($habito) ? $habito->frecuencia_alimentacion : null,
                "habito_defecatorio" => !is_null($habito) ? $habito->habito_defecatorio : null,
                "frecuencia_defecatorio" => !is_null($habito) ? $habito->frecuencia_defecatorio : null,
                "familiares" => $ant_familiares,
                "patologicos" => $ant_patologicos,
                "quirurgicos" => $ant_quirurgicos,
                "medicamentos" => $medicamentos,
            ];

            return $arr;
        } else {
            return "sin datos";
        }
    }

    public function form_admision_guardar(Request $request)
    {
        //dd($request->all());
        $id_paciente = $request->cedula;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        try {
            if (is_null($id_paciente)) {
                return ["status" => "error", "msj" => "Ingrese su número de cedula"];
            }
            $paciente = Paciente::find($id_paciente);
            $usuario = User::find($id_paciente);
            if (is_null($usuario)) {
                $arr_us = [
                    'id'               => $id_paciente,
                    'email'            => $request->correo,
                    'nombre1'          => strtoupper($request->nombre1),
                    'id_tipo_usuario'  => 2,
                    'nombre2'          => strtoupper($request->nombre2),
                    'apellido1'        => strtoupper($request->apellido1),
                    'apellido2'        => strtoupper($request->apellido2),
                    'estado'           => '1',
                    'imagen_url'       => ' ',
                    'telefono1'        => $request->telefono1,
                    'telefono2'        => $request->telefono2,
                    'direccion'        => $request->direccion,
                    'tipo_documento'   => '1',
                    'id_pais'          => '1', //pendiente revisar
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'password'         => bcrypt($id_paciente),
                    'id_usuariocrea'   => 'CREAUSER',
                    'id_usuariomod'    => 'CREAUSER',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $id_usuario = User::insertGetId($arr_us);

                Paciente::create([
                    "id"                 => $id_paciente,
                    "nombre1"            => $request->nombre1,
                    "nombre2"            => $request->nombre2,
                    "apellido1"          => $request->apellido1,
                    "apellido2"          => $request->apellido2,
                    "telefono1"          => $request->telefono1,
                    "telefono2"          => $request->telefono2,
                    "ocupacion"          => $request->ocupacion,
                    "sexo"               => $request->sexo,
                    "estadocivil"       => $request->estado_civil,
                    "fecha_nacimiento"   => $request->fecha_nacimiento,
                    "ciudad"             => $request->ciudad_nacimiento,
                    "direccion"          => $request->direccion,
                    "ip_modificacion"    => $ip_cliente,
                    "gruposanguineo"    => $request->grupo_sanguineo,
                    "transfusion"        => $request->transfusiones,
                    "trabajo"            => $request->lugar_trabajo,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariocrea'     => 'CREAUSER',
                    'id_usuariomod'      => 'CREAUSER',
                    'id_usuario'         => $id_usuario,
                ]);

                $input_log = [
                    'id_usuario'  => 'CREAUSER',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CREA NUEVO PACIENTE APP",
                    'dato_ant1'   => $id_paciente,
                    'dato1'       => strtoupper($request->nombre1 . " " . $request->nombre2 . " " . $request->apellido1 . " " . $request->apellido2),
                    'dato_ant4'   => " PARENTESCO: Principal",
                    'dato2'       => 'APP',
                ];

                Log_usuario::create($input_log);
            } else {
                if (is_null($paciente)) {
                    Paciente::create([
                        "id"                 => $id_paciente,
                        "nombre1"            => $request->nombre1,
                        "nombre2"            => $request->nombre2,
                        "apellido1"          => $request->apellido1,
                        "apellido2"          => $request->apellido2,
                        "telefono1"          => $request->telefono1,
                        "telefono2"          => $request->telefono2,
                        "ocupacion"          => $request->ocupacion,
                        "sexo"               => $request->sexo,
                        "estadocivil"       => $request->estado_civil,
                        "fecha_nacimiento"   => $request->fecha_nacimiento,
                        "ciudad"             => $request->ciudad_nacimiento,
                        "direccion"          => $request->direccion,
                        "ip_modificacion"    => $ip_cliente,
                        "gruposanguineo"    => $request->grupo_sanguineo,
                        "transfusion"        => $request->transfusiones,
                        "trabajo"            => $request->lugar_trabajo,
                        'ip_creacion'        => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                        'id_usuariocrea'     => 'CREAUSER',
                        'id_usuariomod'      => 'CREAUSER',
                    ]);
                } else {
                    $arrpac = [
                        "id"                 => $id_paciente,
                        "nombre1"            => $request->nombre1,
                        "nombre2"            => $request->nombre2,
                        "apellido1"          => $request->apellido1,
                        "apellido2"          => $request->apellido2,
                        "telefono1"          => $request->telefono1,
                        "telefono2"          => $request->telefono2,
                        "ocupacion"          => $request->ocupacion,
                        "sexo"               => $request->sexo,
                        "estadocivil"       => $request->estado_civil,
                        "fecha_nacimiento"   => $request->fecha_nacimiento,
                        "ciudad"             => $request->ciudad_nacimiento,
                        "direccion"          => $request->direccion,
                        "ip_modificacion"    => $ip_cliente,
                        "gruposanguineo"    => $request->grupo_sanguineo,
                        "transfusion"        => $request->transfusiones,
                        "trabajo"            => $request->lugar_trabajo,
                        'ip_modificacion'    => $ip_cliente,
                        'id_usuariomod'      => 'CREAUSER',
                    ];

                    $paciente->update($arrpac);
                }
            }

            $valida_form = Form_Preadmision_Cabecera::where('id_paciente', $id_paciente)->where('estado', 1)->where('fecha', date('Y-m-d'))->first();
            if (!is_null($valida_form)) {
                return ["status" => "error", "msj" => "El paciente ya fue registrado"];
            } else {

                $arr_cab = [
                    'id_paciente' => $id_paciente,
                    'fecha' => date('Y-m-d'),
                    'alimentos_sintomas' => $request->alimentos_sintomas,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $id_cab = Form_Preadmision_Cabecera::insertGetId($arr_cab);

                for ($x = 0; $x < count($request->sintomas); $x++) {

                    $arr_det = [
                        'id_form_cab'       => $id_cab,
                        'id_sintoma'        => $request->sintomas[$x],
                        'id_tiempo_sintoma' => $request->tiempo_sintomas[$x],
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    Form_Preadmision_Detalle::create($arr_det);
                }

                if ($request->has("enfermedad_antcfam")) {
                    $variable = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('tipo', '1')->where('estado', '1');

                    if (!is_null($variable)) {
                        $variable->delete();
                    }
                    if ('enfermedad_antcfam' != $variable) {
                        if (count($request->enfermedad_antcfam) > 0) {
                            for ($y = 0; $y < count($request->enfermedad_antcfam); $y++) {
                                Paciente_Antecedente::create([
                                    'id_paciente'       => $id_paciente,
                                    'tipo'              => $request->tipof,
                                    'antecedente'       => $request->enfermedad_antcfam[$y],
                                    'parentesco'        => $request->parentesco_antcfam[$y],
                                    'ip_creacion'       => $ip_cliente,
                                    'ip_modificacion'   => $ip_cliente,
                                ]);
                            }
                        }
                    }
                }

                if ($request->has("antecedentes_patologicos")) {
                    $variablep = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('tipo', '2')->where('estado', '1');

                    if (!is_null($variablep)) {
                        $variablep->delete();
                    }
                    if (count($request->antecedentes_patologicos) > 0) {

                        for ($z = 0; $z < count($request->antecedentes_patologicos); $z++) {
                            Paciente_Antecedente::create([
                                'id_paciente'       => $id_paciente,
                                'tipo'              => $request->tipop,
                                'antecedente'       => $request->antecedentes_patologicos[$z],
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        }
                    }
                }

                if ($request->has("antecedentes_quirurgicos")) {
                    $variableq = Paciente_Antecedente::where('id_paciente', $id_paciente)->where('tipo', '3')->where('estado', '1');

                    if (!is_null($variableq)) {
                        $variableq->delete();
                    }
                    if (count($request->antecedentes_quirurgicos) > 0) {

                        for ($a = 0; $a < count($request->antecedentes_quirurgicos); $a++) {
                            Paciente_Antecedente::create([
                                'id_paciente'       => $id_paciente,
                                'tipo'              => $request->tipoq,
                                'antecedente'       => $request->antecedentes_quirurgicos[$a],
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        }
                    }
                }

                if ($request->has("medicamento")) {
                    $variablem = Paciente_Medicamento::where('id_paciente', $id_paciente)->where('estado', '1');
                    if (!is_null($variablem)) {
                        $variablem->delete();
                    }
                    if (count($request->medicamento) > 0) {
                        for ($b = 0; $b < count($request->medicamento); $b++) {
                            Paciente_Medicamento::create([
                                'id_paciente' => $id_paciente,
                                'medicamento' => $request->medicamento[$b],
                                'detalle' => $request->detalle_medicamento[$b],
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }
                    }
                }



                $habito = Paciente_Habito::where('id_paciente', $id_paciente)->where('estado', 1)->first();

                if (!is_null($habito)) {
                    $habito->update([
                        'fuma' => $request->fuma,
                        'frecuencia_fuma' => $request->frec_fuma,
                        'alcohol' => $request->alcohol,
                        'frecuencia_alcohol' => $request->frec_alcohol,
                        'drogas' => $request->drogas,
                        'frecuencia_drogas' => $request->frec_drogas,
                        'actividad_fisica' => $request->actividad_fisica,
                        'frecuencia_actividad' => $request->frec_actividad_fisica,
                        'alimentacion' => $request->alimentacion,
                        'frecuencia_alimentacion' => $request->frec_alimentacion,
                        'habito_defecatorio' => $request->habito_defecatorio,
                        'frecuencia_defecatorio' => $request->frec_habito_defecatorio,
                        'ip_modificacion'   => $ip_cliente,
                    ]);
                } else {
                    Paciente_Habito::create([
                        'id_paciente' => $id_paciente,
                        'fuma' => $request->fuma,
                        'frecuencia_fuma' => $request->frec_fuma,
                        'alcohol' => $request->alcohol,
                        'frecuencia_alcohol' => $request->frec_alcohol,
                        'drogas' => $request->drogas,
                        'frecuencia_drogas' => $request->frec_drogas,
                        'actividad_fisica' => $request->actividad_fisica,
                        'frecuencia_actividad' => $request->frec_actividad_fisica,
                        'alimentacion' => $request->alimentacion,
                        'frecuencia_alimentacion' => $request->frec_alimentacion,
                        'habito_defecatorio' => $request->habito_defecatorio,
                        'frecuencia_defecatorio' => $request->frec_habito_defecatorio,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);
                }

                return ["status" => "ok", "msj" => "Datos guardados"];
            }
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function find_medicamento_alergia(Request $request)
    {

        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $genericos = Principio_Activo::where('nombre', 'like', '%' . $term . '%')->limit(20)->get();
        $formatted_tags = [];

        foreach ($genericos as $generico) {
            $formatted_tags[] = ['id' => $generico->id, 'text' => $generico->nombre];
        }

        return \Response::json($formatted_tags);
    }

    public function guardar_alergia(Request $request)
    {
        //dd($request->all());
        $idusuario  = 'CREAUSER';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        try {

            $id = $request["id_paciente"];
            $paciente = Paciente::find($id);
            //dd($paciente);

            $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();

            $todo = null;
            if (!is_null($alergiasxpac)) {
                foreach ($alergiasxpac as $alergias) {
                    $nombre_alergia = Principio_Activo::find($alergias->id_principio_activo);
                    $todo .= $nombre_alergia->nombre . ', ';
                }
            }

            $todo_nuevo = null;
            if (!is_null($request["ale_list"])) {
                foreach ($request["ale_list"] as $alergias_nuevas) {
                    $todo_nuevo .= $alergias_nuevas . ', ';
                }
            }

            if ($todo != null && $todo_nuevo != null) {
                $alergias_new = [
                    'anterior'        => 'DATOS_PRINCIPALES-> Alergia: ' . $todo,
                    'nuevo'           => 'DATOS_PRINCIPALES-> Alergia: ' . $todo_nuevo,
                    'id_paciente'     => $paciente->id,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                Hc_Log::create($alergias_new);
            }

            foreach ($alergiasxpac as $apac) {
                $apac->delete();
            }

            $alergia_txt = "";
            $ale_flag    = true;
            //return $request->ale_list;
            if ($request->ale_list != null) {
                foreach ($request->ale_list as $ale) {
                    if (is_numeric($ale)) {
                        $generico = Principio_Activo::find($ale);
                    } else {
                        $generico = Principio_Activo::where('nombre', 'like', substr(strtoupper($ale), 0, -5))->first();
                        if (is_null($generico)) {
                            $input_principio = [
                                'nombre'          => substr(strtoupper($ale), 0, -5),
                                'descripcion'     => substr(strtoupper($ale), 0, -5),
                                'estado'          => '1',
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                            ];
                            $ale      = Principio_Activo::insertGetId($input_principio);
                            $generico = Principio_Activo::find($ale);
                        } else {
                            $ale = $generico->id;
                        }
                    }
                    $generico = Principio_Activo::find($ale);
                    $pac_ale  = [

                        'id_paciente'         => $id,
                        'id_principio_activo' => $ale,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariomod'       => $idusuario,
                        'id_usuariocrea'      => $idusuario,

                    ];
                    Paciente_Alergia::create($pac_ale);
                    if ($ale_flag) {
                        $alergia_txt = $generico->nombre;
                        $ale_flag    = false;
                    } else {
                        $alergia_txt = $alergia_txt . '+' . $generico->nombre;
                    }
                }
            }

            return ["respuesta" => "success", "msj" => "Datos guardados"];
        } catch (\Exception $e) {
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function nuevoMaquina1(Request $request)
    {
        ini_set('max_execution_time',300);
        try {
            $trama      = $request->parametro;
            $enviar     = explode("torbi10", $trama);
            $id_orden   = "dato";
            $idusuario  = '9999999999';
            $ipcreacion = "maquina";
            $n_examen   = 101;
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if ($descriptando[0] == "OBR") {
                    $id_orden = $descriptando[3];
                    $examen   = Examen_Detalle::where('id_examen', '101')->where('id_examen_orden', $id_orden)->first();
                    if (!is_null($examen)) {
                        $n_examen = 101;
                    } else {
                        $n_examen = 1184;
                    }
                }
                if ($id_orden != "dato") {
                    if (($descriptando[0] == "OBX") && ($descriptando[1] < 36)) {
                        $buscar_tipo = explode("^", $descriptando[3]);
                        $tipo = $buscar_tipo[1];
                        $examen = Examen::find($n_examen);
                        $examen_parametro = examen_parametro::where('nombre', $tipo)->where('id_examen', $n_examen)->first();
                        if (!is_null($examen_parametro)) {
                            $id_parametro     = $examen_parametro->id;
                            $orden            = examen_orden::find($id_orden);
                            if (!is_null($orden)) {
                                $fl_ref = 0;
                                $ref_ant = Examen_Parametro_Referencia_Anterior::where('id_parametro', $examen_parametro->id)->where('fecha_valida', '>', $orden->fecha_orden)->orderBy('fecha_valida', 'asc')->first();
                                if (!is_null($ref_ant)) {
                                    $fl_ref = 1;
                                }
                                $valor = $descriptando[5];
                                if ($fl_ref) {
                                    if ($ref_ant->texto_referencia == "") {
                                        if (is_numeric($ref_ant->valor1g)) {
                                            if ($valor > $ref_ant->valor1g) {
                                                $valor = '*'.$descriptando[5];
                                            }
                                        }
                            
                                        if (is_numeric($ref_ant->valor1)) {
                                            if ($valor < $ref_ant->valor1) {
                                                $valor = '*'.$descriptando[5];
                                            }
                                        }
                                    }
                                } else {
                                    if ($examen_parametro->texto_referencia == "") {
                                        if (is_numeric($examen_parametro->valor1g)) {
                                            if ($valor > $examen_parametro->valor1g) {
                                                $valor = '*'.$descriptando[5];
                                            }
                                        }
                            
                                        if (is_numeric($examen_parametro->valor1)) {
                                            if ($valor < $examen_parametro->valor1) {
                                                $valor = '*'.$descriptando[5];
                                            }
                                        }
                                    }
                                }

                                $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $id_orden)->first();
                                if (!is_null($examen_resultado)) {
                                    $input = [
                                        'valor'           => $valor,
                                        'ip_modificacion' => $ipcreacion, 
                                        'id_usuariomod'   => $idusuario,
                                    ];
                                    Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                                } else {
                                    Examen_Resultado::create([
                                        'id_orden'        => $id_orden,
                                        'id_parametro'    => $id_parametro,
                                        'valor'           => $valor,
                                        'id_usuariocrea'  => $idusuario,
                                        'id_usuariomod'   => $idusuario,
                                        'ip_creacion'     => $ipcreacion,
                                        'ip_modificacion' => $ipcreacion,
                                    ]);
                                }
                                $orden->realizado = "1";
                                $orden->save();
                            }
                        }
                    }
                }
            }
            return "MSH|^~\&|LIS||||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\nMSA|AA|1";
        } catch (Exception $ex) {
            return "MSH|^~\&|LIS|LIS|||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\n
            MSA|AE|1\n
            QAK|1|Linea: " .$ex->getLine() . "\n
            QAK|2|Mensaje: " .$ex->getMessage();
        }
    }

    public function maquilaElectrolitos(Request $request){
        try {
            $trama = $request->trama;
            $id_orden   = "dato";
            $idusuario  = '9999999999';
            $ipcreacion = "maquina";
            $error = "";
            //AR (Application Rejection); AE (Application Error)
            if($trama != ""){
                $fragmentos = explode("trol23", $trama);
                $fecha = $fragmentos[0];
                $serialNumber= $fragmentos[1];
                $id_orden = trim(str_replace('Sample Number:','',$fragmentos[2]));
                $orden = examen_orden::find($id_orden);
                $resultados = [];
                $nombre_parametro = "";
                $examen_parametro = null;
                for ($i = 3; $i < count($fragmentos); $i++) {
                    if(trim($fragmentos[$i]) != ""){
                        $valor = explode("=", trim(str_replace('mmol/L', '', $fragmentos[$i])));
                        if(count($valor) > 1){
                            $resultados[trim($valor[0])] = trim($valor[1]);
                        }
                    }
                }
                
                foreach ($resultados as $key => $value) {
                    $newValue = $value;
                    switch ($key) {
                        case 'K':
                            $newValue = round($value, 1);
                            $nombre_parametro = 'POTASIO';
                            break;
                        case 'Na':
                            $newValue = intval($value);
                            $nombre_parametro = 'SODIO';
                            break;
                        case 'Cl':
                            $newValue = intval($value);
                            $nombre_parametro = 'CLORO';
                            break;
                        case 'Ca':
                            $nombre_parametro = 'CALCIO IONICO DETERMINADO';
                            break;
                        case 'nCa':
                            // $examen_parametro = examen_parametro::where('nombre', 'CALCIO IONICO DETERMINADO')->where('id_examen', $id_examen)->first();
                            // break;
                        case 'tCa':
                            // $newValue = intval($value);
                            // $examen_parametro = examen_parametro::where('nombre', 'CLORO')->where('id_examen', $id_examen)->first();
                            break;
                        default:
                            $nombre_parametro = "";
                            break;
                    }

                    if($nombre_parametro!=""){
                        $examen_parametro = examen_parametro::join('examen_detalle as ed', 'ed.id_examen','examen_parametro.id_examen')
                        ->where('examen_parametro.nombre', '=', $nombre_parametro)
                        ->where('ed.id_examen_orden',$id_orden)
                        ->select('examen_parametro.*')->first();
                        
                        if(!is_null($examen_parametro)){
                            $fl_ref = 0;
                            $ref_ant = Examen_Parametro_Referencia_Anterior::where('id_parametro', $examen_parametro->id)->where('fecha_valida', '>', $orden->fecha_orden)->orderBy('fecha_valida', 'asc')->first();
                            if (!is_null($ref_ant)) {
                                $fl_ref = 1;
                            }
                            $valor = $newValue;
                            if ($fl_ref) {
                                if ($ref_ant->texto_referencia == "") {
                                    if (is_numeric($ref_ant->valor1g)) {
                                        if ($valor > $ref_ant->valor1g) {
                                            $valor = '*'.$newValue;
                                        }
                                    }
                        
                                    if (is_numeric($ref_ant->valor1)) {
                                        if ($valor < $ref_ant->valor1) {
                                            $valor = '*'.$newValue;
                                        }
                                    }
                                }
                            } else {
                                if (is_null($examen_parametro->texto_referencia)) {
                                    if (is_numeric($examen_parametro->valor1g)) {
                                        if ($valor > $examen_parametro->valor1g) {
                                            $valor = '*'.$newValue;
                                        }
                                    }
                        
                                    if (is_numeric($examen_parametro->valor1)) {
                                        if ($valor < $examen_parametro->valor1) {
                                            $valor = '*'.$newValue;
                                        }
                                    }
                                }
                            }
                            $examen_resultado = Examen_Resultado::where('id_parametro', $examen_parametro->id)->where('id_orden', $id_orden)->first();
                            if(!is_null($examen_resultado)){
                                $input = [
                                    'valor'           => $valor,
                                    'ip_modificacion' => $ipcreacion,
                                    'id_usuariomod'   => $idusuario,
                                ];
                                Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                            }else{
                                $input = [
                                    'id_orden'        => $id_orden,
                                    'id_parametro'    => $examen_parametro->id,
                                    'valor'           => $valor,
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                    'ip_creacion'     => $ipcreacion,
                                    'ip_modificacion' => $ipcreacion,
                                ];
                                Examen_Resultado::create($input);
                            }
                        }
                        else{
                            $error .= "No se encontro el examen con el parametro " . $nombre_parametro . ", ";
                        }
                    }
                }
                if($error==""){
                    return "MSH|^~\&|LIS||||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\nMSA|AA|1";
                }
                else{
                    return "MSH|^~\&|LIS|LIS|||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\n
                    MSA|AE|1\n
                    QAK|1|".$error;
                }
            }
            else{
                return "MSH|^~\&|LIS|LIS|||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\n
                MSA|AE|1\n
                QAK|1|Error no se recibieron datos";
            }
        } catch (Exception $ex) {
            return "MSH|^~\&|LIS|LIS|||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\n
            MSA|AE|1\n
            QAK|1|Linea: " .$ex->getLine() . "\n
            QAK|2|Mensaje: " .$ex->getMessage();
        }
    }


    public function arreglaCabMovimiento($id_empresa){
        $cab_movimiento = InvCabMovimientos::whereNull("total")->get();
        foreach ($cab_movimiento as $key => $value) {
            InvCabMovimientos::calcularTotalCabMovimiento($value->id);
        }

        dd("ok gracias amigo");
    }

    public static function crear_resultados($id_orden)
    {
        try {
            $ip_cliente = 'API';
            $idusuario  = 'CREAUSER';
            date_default_timezone_set('America/Guayaquil');
            $examen_orden = Examen_Orden::find($id_orden);
            $fecha_orden  = $examen_orden->fecha_orden;
            
            if(!is_null($examen_orden)){
                $paciente = $examen_orden->paciente;
                $seguro = Seguro::find($examen_orden->id_seguro);
                //Tipo seguro = 0:publico, 1:privado, 2:npi 
                $detalles = $examen_orden->detalles;
                foreach ($detalles as $detalle) {
                    $examen = $detalle->examen;
                    $id_agrupador_publico = $examen->id_agrupador;

                    $examen_agrupador_010 = Examen_Agrupador_010::where('id_examen', $examen->id)->where('estado',1)->first();
                    if(!is_null($examen_agrupador_010)){
                        $id_agrupador_publico = $examen_agrupador_010->id_agrupador;
                    }
                    
                    $eas = Examen_Agrupador_Sabana::where('id_examen',$examen->id)->first();
                    $id_agrupador_privado = null;
                    if(!is_null($eas)){
                        $id_agrupador_privado = $eas->id_examen_agrupador_labs;
                    }
                    if($examen->sexo_n_s==0){
                        $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',3)->where('estado','1')->get();
                    } else{
                        $parametros = Examen_Parametro::where('id_examen',$examen->id)->where('sexo',$paciente->sexo)->where('estado','1')->get();
                    }
                    foreach ($parametros as $parametro) {
                        $examen_resultado = Examen_Resultado::where('id_orden',$id_orden)->where('id_parametro', $parametro->id)->first();
                        //$examen_resultado =  $examen_orden->resultados->where('id_parametro', $parametro->id)->first();
                        
                        if(is_null($examen_resultado)){
                            if($fecha_orden > $parametro->created_at){ 
                                if($seguro->tipo==0){//publico
                                    if($examen->id != '639'){
                                        $input_resultado = [
                                            'id_orden'        => $examen_orden->id,
                                            'id_parametro'    => $parametro->id,
                                            'valor'           => null,
                                            'ip_creacion'     => 'CARGA_EXA',
                                            'ip_modificacion' => $ip_cliente,
                                            'id_usuariocrea'  => $idusuario,
                                            'id_usuariomod'   => $idusuario,
                                            'id_examen'       => $examen->id,
                                            'id_agrupador_publico'    => $id_agrupador_publico,
                                        ];
                                        Examen_Resultado::create($input_resultado);
                                    }

                                } else {
                                    if($examen->id != '639'){
                                        $input_resultado = [
                                            'id_orden'        => $examen_orden->id,
                                            'id_parametro'    => $parametro->id,
                                            'valor'           => null,
                                            'ip_creacion'     => 'CARGA_EXA',
                                            'ip_modificacion' => $ip_cliente,
                                            'id_usuariocrea'  => $idusuario,
                                            'id_usuariomod'   => $idusuario,
                                            'id_examen'       => $examen->id,
                                            'id_agrupador_privado'    => $id_agrupador_privado,
                                        ];
                                        Examen_Resultado::create($input_resultado);
                                    }

                                }
                            }    
                        } else{
                            if($seguro->tipo==0){//publico
                                
                                $input_resultado = [
                                    
                                    'id_examen'             => $examen->id,
                                    'id_agrupador_publico'  => $id_agrupador_publico,
                                    'fecha_anterior'        => $examen_resultado->updated_at,
                                ];
                                $examen_resultado->update($input_resultado);
                            } else {
                                
                                $input_resultado = [
                                    
                                    'id_examen'             => $examen->id,
                                    'id_agrupador_privado'  => $id_agrupador_privado,
                                    'fecha_anterior'        => $examen_resultado->updated_at,
                                ];
                                $examen_resultado->update($input_resultado);

                            }
                            //dd($examen_resultado);
                        }
                    }
                }
            }else{
                return ['status' => 'error', 'msj' => 'No se encontro la orden'];
            }
            
            return ['status' => 'success', 'msj' => 'Se creo resultados'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
}
