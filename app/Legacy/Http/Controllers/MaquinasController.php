<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Resultado;

class MaquinasController extends Controller
{

    public function consultar_orden(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                //var_dump($elemento);exit();
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $variables = json_decode($elemento, true);
                $examenes_detalle = Examen_Detalle::where('id_examen_orden', $variables['orden'])->get();
                $examen_orden     = Examen_Orden::find($variables['orden']);
                if ($examen_orden->paciente->sexo == 1) {
                    $sexo = "M";
                } else {
                    $sexo = "F";
                }
                $data['id_orden']                      = $variables['orden'];
                $data['paciente']['cedula']            = $examen_orden->id_paciente;
                $data['paciente']['nombres']           = $examen_orden->paciente->nombre1 . ' ' . $examen_orden->paciente->apellido1 . ' ' . $examen_orden->paciente->apellido2;
                $data['paciente']['fecha_nacimiento']  = $examen_orden->paciente->fecha_nacimiento;
                $data['paciente']['sexo']  = $sexo;
                $data['paciente']['fecha_orden']       = $examen_orden->fecha_orden;
                $detalles = array();
                foreach ($examenes_detalle as $value) {
                    //examen especial proteinas y fracciones
                    if ($value->id_examen == "1179") {
                        $esp_examen_parametro = Examen_Parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
                        foreach ($esp_examen_parametro as $esp_value) {
                            $elemento = array($esp_value->nombre_equipo, $value->examen->id_maquina, $value->examen->tipo_muestra);
                            array_push($detalles, $elemento);
                        }
                    }
                    if ($value->id_examen == "1181") {
                        $esp_examen_parametro = Examen_Parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
                        foreach ($esp_examen_parametro as $esp_value) {
                            $elemento = array($esp_value->nombre_equipo, $value->examen->id_maquina, $value->examen->tipo_muestra);
                            array_push($detalles, $elemento);
                        }
                    }
                    //examen especial proteinas y fracciones
                    if ($value->examen->nombre_equipo != "") {
                        $elemento = array($value->examen->nombre_equipo, $value->examen->id_maquina, $value->examen->tipo_muestra);
                        array_push($detalles, $elemento);
                    }
                }

                $data['detalles'] = $detalles;
                return json_encode($data);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }

    public function resultado_orden(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                //var_dump($elemento);exit();
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $variables = json_decode($elemento, true);
                $orden = Examen_Orden::find($variables['orden']);
                $resultados = $variables['resultados'];
                foreach ($resultados as $resultado) {
                    $bun_ure = Examen_Detalle::where('id_examen_orden', $variables['orden'])->where('id_examen', '1181')->first();
                    if ($resultado['parametro'] == 'UREA' && !is_null($bun_ure)) {
                        $examen_r = Examen_Resultado::where('id_orden', $variables['orden'])->where('id_parametro', '409')->first();
                        if (is_null($examen_r)) {
                            Examen_Resultado::insert([
                                'id_orden'          => $variables['orden'],
                                'id_parametro'      => '409',
                                'valor'             => $resultado['resultado'],
                                'id_usuariocrea'    => '9999999999',
                                'id_usuariomod'     => '9999999999',
                                'ip_creacion'       => 'maquina',
                                'ip_modificacion'   => 'maquina',
                            ]);
                            continue;
                        } else {
                            $examen_r->valor           = $resultado['resultado'];
                            $examen_r->ip_modificacion = "MODIFICADO MAQUINA";
                            $examen_r->save();
                            continue;
                        }
                    }

                    $parametros = Examen_Parametro::where('nombre_equipo', $resultado['parametro'])
                        ->where('sexo', $orden->paciente->sexo)->where('id_examen', '<>', '1181')->first();
                    if (!is_null($parametros)) {
                        $examen_r = Examen_Resultado::where('id_orden', $variables['orden'])->where('id_parametro', $parametros->id)->first();
                        if (is_null($examen_r)) {
                            Examen_Resultado::insert([
                                'id_orden'          => $variables['orden'],
                                'id_parametro'      => $parametros->id,
                                'valor'             => $resultado['resultado'],
                                'id_usuariocrea'    => '9999999999',
                                'id_usuariomod'     => '9999999999',
                                'ip_creacion'       => 'maquina',
                                'ip_modificacion'   => 'maquina',
                            ]);
                            continue;
                        } else {
                            $examen_r->valor           = $resultado['resultado'];
                            $examen_r->ip_modificacion = "MODIFICADO MAQUINA";
                            $examen_r->save();
                            continue;
                        }
                    } else {
                        $parametros_2 = Examen_Parametro::where('nombre_equipo', $resultado['parametro'])
                            ->where('sexo', "3")->first();
                        if (!is_null($parametros_2)) {
                            $examen_r = Examen_Resultado::where('id_orden', $variables['orden'])->where('id_parametro', $parametros_2->id)->first();
                            if (is_null($examen_r)) {
                                Examen_Resultado::insert([
                                    'id_orden'          => $variables['orden'],
                                    'id_parametro'      => $parametros_2->id,
                                    'valor'             => $resultado['resultado'],
                                    'id_usuariocrea'    => '9999999999',
                                    'id_usuariomod'     => '9999999999',
                                    'ip_creacion'       => 'maquina',
                                    'ip_modificacion'   => 'maquina',
                                ]);
                                continue;
                            } else {
                                $examen_r->valor           = $resultado['resultado'];
                                $examen_r->ip_modificacion = "MODIFICADO MAQUINA";
                                $examen_r->save();
                                continue;
                            }
                        } else {
                            $examen_d = Examen::where('nombre_equipo', $resultado['parametro'])->first();
                            if (!is_null($examen_d)) {
                                $parametro_b = Examen_Parametro::where('id_examen', $examen_d->id)->first();
                                if (!is_null($parametro_b)) {
                                    $examen_r = Examen_Resultado::where('id_orden', $variables['orden'])->where('id_parametro', $parametro_b->id)->first();
                                    if (is_null($examen_r)) {
                                        Examen_Resultado::insert([
                                            'id_orden'          => $variables['orden'],
                                            'id_parametro'      => $parametro_b->id,
                                            'valor'             => $resultado['resultado'],
                                            'id_usuariocrea'    => '9999999999',
                                            'id_usuariomod'     => '9999999999',
                                            'ip_creacion'       => 'maquina',
                                            'ip_modificacion'   => 'maquina',
                                        ]);
                                        continue;
                                    } else {
                                        $examen_r->valor           = $resultado['resultado'];
                                        $examen_r->ip_modificacion = "MODIFICADO MAQUINA";
                                        $examen_r->save();
                                        continue;
                                    }
                                }
                                $data['id_orden']         = $variables['orden'];
                                $data['status']           = "error";
                                $data['status']           = "no existe parametro en el sistema";
                                return json_encode($data);
                            }
                        }
                    }
                }
                $data['id_orden']         = $variables['orden'];
                $data['status']           = "ok ";
                $data['value']           = "ok";
                return json_encode($data);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
}