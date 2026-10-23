<?php

namespace Sis_medico\Http\Controllers\Apis;

use Carbon\Carbon;
use Excel;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Archivo_historico;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Documento;
use Sis_medico\hc_procedimientos;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Epicrisis;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_Labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_anestesiologia_csv;
use Sis_medico\Hc_Cie10;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\HcFormulario013;
use Sis_medico\Historiaclinica;
use Sis_medico\Interconsulta;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Protocolo;
use Sis_medico\record_tecnicas_anestesicas;
use Sis_medico\Seguro;
use Sis_medico\User;
use Response;
use Sis_medico\agenda as Sis_medicoAgenda;
use Sis_medico\Agenda_archivo;
use Sis_medico\AgendaScan;
use Sis_medico\Anamnesis;
use Sis_medico\Ap_Orden;
use Sis_medico\ApArchivoFactura;
use Sis_medico\ApProcedimiento;
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Aud_Hc_Procedimiento_Final;
use Sis_medico\Cardiologia;
use Sis_medico\Convenio;
use Sis_medico\Drogas_administradas;
use Sis_medico\Especialidad;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Hc4_Auditoria_Biopsias;
use Sis_medico\Hc_cardio_cie10;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Informe_Tecnico;
use Sis_medico\Interconsulta_Diagnostico;
use Sis_medico\LogDocumentoFirmado;
use Sis_medico\Medicina;
use Sis_medico\Nota_entrega;
use Sis_medico\Nota_Entrega_Escaneados;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\procedimiento_completo;
use Sis_medico\Rc_antecedentes;
use Sis_medico\Rc_indicaciones;
use Sis_medico\Rc_pre_anestesica;
use Sis_medico\Rc_resultados;
use Sis_medico\TecnicasAnestesicas;
use Sis_medico\Utilidades;
use stdClass;

class LiquidadorNewController extends Controller
{
    public function buscar_cabeceras($empresa, $mes_plano, $numero_paciente, $seguro = '2')
    {
        $arr_cab = [];
        $cabeceras = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)->where('id_empresa', $empresa)->where('numero_paciente', $numero_paciente)->where('id_seguro', $seguro)->where('estado', 1)->get();

        foreach ($cabeceras as $cabecera) {
            array_push($arr_cab, $cabecera->id);
        }
        return $arr_cab;
    }
    public function getCabeceraPlano($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);

        $nombre = null;
        $identificacion = null;
        $identificacion_padre = null;
        $identificacion_madre = null;
        $menorEdad = false;
        $edad = null;

        $procConsul = null;
        $laboratorio = null;
        $fecha_ing = null;
        $fecha_proc = null;
        $tipo = 'e';

        if (!is_null($archivo_plano)) {

            $fecha_ing = $archivo_plano->fecha_ing;

            $paciente = Paciente::where('id', $archivo_plano->id_paciente)
                ->select(
                    'id',
                    'fecha_nacimiento',
                    'cedula_padre',
                    'cedula_madre',
                    DB::raw("CONCAT(
                        apellido1,
                        IF(TRIM(REPLACE(REPLACE(apellido2, '.', ''), '(N/A)', '')) != '', CONCAT(' ', apellido2), ''),
                        ' ',
                        nombre1,
                        IF(TRIM(REPLACE(REPLACE(nombre2, '.', ''), '(N/A)', '')) != '', CONCAT(' ', nombre2), '')
                    ) as nombre_paciente")
                )
                ->first();

            if (!is_null($paciente)) {

                $nombre = $paciente->nombre_paciente;
                $identificacion = $archivo_plano->id_paciente;

                $identificacion_madre = $paciente->cedula_madre;
                $identificacion_padre = $paciente->cedula_padre;

                if (
                    !empty($paciente->fecha_nacimiento) &&
                    $paciente->fecha_nacimiento != '0000-00-00'
                ) {
                    try {
                        $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
                        $menorEdad = $edad < 18;
                    } catch (\Exception $e) {
                        $edad = null;
                        $menorEdad = false;
                    }
                }
            }

            $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();

            if (!is_null($aud_protocolo)) {
                $fecha_proc = $aud_protocolo->fecha_operacion;
            } else {
                $protocolo = Hc_Protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();

                if (!is_null($protocolo)) {
                    $fecha_proc = $protocolo->fecha_operacion;
                } else {
                    $fecha_proc = $archivo_plano->fecha_ing;
                }
            }

            if (!is_null($fecha_proc)) {
                $fecha_proc = Carbon::parse($fecha_proc)->format('Y-m-d');
            }

            $historiaClinica = Historiaclinica::find($archivo_plano->id_hc);

            if (!is_null($historiaClinica)) {

                $agenda = Agenda::find($historiaClinica->id_agenda);

                if (!is_null($agenda)) {

                    $procConsul = $agenda->proc_consul;

                    if ($procConsul == '1') {

                        $procName = $archivo_plano->nom_procedimiento ?? '';
                        $procNorm = mb_strtolower($procName, 'UTF-8');

                        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $procNorm);

                        if ($ascii !== false) {
                            $procNorm = $ascii;
                        }

                        if (strpos($procNorm, 'ecografia') !== false) {
                            $procConsul = 5;
                        } elseif (
                            strpos($procNorm, 'espirometria') !== false ||
                            strpos($procNorm, 'test de ejercicio pulmonar') !== false ||
                            strpos($procNorm, 'test ejercicio pulmonar') !== false
                        ) {
                            $procConsul = 6;
                        } elseif (
                            strpos($procNorm, 'manometria') !== false ||
                            strpos($procNorm, 'ph metria') !== false ||
                            strpos($procNorm, 'capsula endoscopica') !== false
                        ) {
                            $procConsul = 7;
                            $tipo = 'f';
                        } else {
                            $procConsul = $agenda->proc_consul;
                        }
                    } elseif ($procConsul == '4') {
                        $this->updateHCCabeceraPlano($archivo_plano->id);
                    }

                    $fecha_ini = date('Y/m/d', strtotime($agenda->fechaini));

                    $orden = Examen_Orden::join('seguros as s', 's.id', 'examen_orden.id_seguro')
                        ->whereRaw("DATE(examen_orden.created_at) = ?", [$fecha_ini])
                        ->where('examen_orden.id_paciente', $agenda->id_paciente)
                        ->where('s.tipo', 0)
                        ->get();

                    if (count($orden) > 0) {
                        $laboratorio = 1;
                    } else {
                        $laboratorio = 0;
                    }
                }

                // $this->updateDoctorExaminador(
                //     $archivo_plano->id_hc_procedimimentos,
                //     $archivo_plano->id_empresa,
                //     $tipo
                // );
            }
        }

        return response()->json([
            'paciente' => $nombre,
            'identificacion' => $identificacion,
            'identificacion_padre' => $identificacion_padre,
            'identificacion_madre' => $identificacion_madre,
            'menorEdad' => $menorEdad,
            'edad' => $edad,
            'procConsul' => $procConsul,
            'laboratorio' => $laboratorio,
            'fechaIng' => $fecha_ing,
            'fechaProc' => $fecha_proc
        ]);
    }
    public function buscar_orden($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        $id_ordenes = [];
        if (!is_null($plano_cabecera)) {
            $plano_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera', $plano_cabecera->id)->groupBy('id_orden_labs')->get();
            $cont = 0;
            foreach ($plano_detalle as $detalle) {
                $id_orden = $detalle->id_orden_labs;
                if (!is_null($id_orden)) {
                    $id_ordenes[$cont] = $id_orden;
                    $cont++;
                }
            }
        }
        return $id_ordenes;
    }
    public function buscar_agenda_as400($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $agenda    = Agenda::find($historiaClinica->id_agenda);
                    $id_archivos = [];
                    if (!is_null($agenda)) {
                        $archivos = Agenda_archivo::where('id_agenda', $agenda->id)->get();
                        $cont = 0;
                        foreach ($archivos as $value) {
                            $id_archivo = $value->id;
                            if (!is_null($id_archivos)) {
                                $id_archivos[$cont] = $id_archivo;
                                $cont++;
                            }
                        }
                        return $id_archivos;
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function buscar_estudio_espirometria($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            $id_archivos = [];
            if (!is_null($plano_cabecera)) {
                $aud_protocolos = Aud_Hc_Protocolo::where('hcid', $plano_cabecera->id_hc)->get();
                foreach ($aud_protocolos as $key => $aud_protocolo) {
                    $protocolo_fallback = null;
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if (!is_null($protocolo)) {
                        $historia_actual = Historiaclinica::find($plano_cabecera->id_hc);
                        $agenda_actual   = $historia_actual ? Agenda::find($historia_actual->id_agenda) : null;
                        if ($agenda_actual) {
                            $inicio = Carbon::parse($agenda_actual->fechaini)->startOfDay();
                            $fin    = Carbon::parse($agenda_actual->fechaini)->endOfDay();
                            $agenda_alt = Agenda::where('id_paciente', $plano_cabecera->id_paciente)
                                ->where('id_seguro',   $plano_cabecera->id_seguro)
                                ->where('id_empresa',  $plano_cabecera->id_empresa)
                                ->whereBetween('fechaini', [$inicio, $fin])
                                ->where('id', '<>', $agenda_actual->id)
                                ->first();

                            if (!is_null($agenda_alt)) {
                                $historia_alt = Historiaclinica::where('id_agenda', $agenda_alt->id)->first();
                                if (!is_null($historia_alt)) {
                                    $protocolo_fallback = hc_protocolo::where('hcid', $historia_alt->hcid)->first();
                                }
                            }
                        }

                        if (!is_null($protocolo_fallback)) {
                            $protocolo = $protocolo_fallback;
                        }

                        if (!is_null($protocolo)) {
                            $cont = 0;
                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '2')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }

                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '3')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }

                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '4')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }
                        }
                    }
                }
            }
            return $id_archivos;
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function buscar_estudio_funcional($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            $id_archivos = [];
            if (!is_null($plano_cabecera)) {
                $aud_protocolos = Aud_Hc_Protocolo::where('hcid', $plano_cabecera->id_hc)->get();
                foreach ($aud_protocolos as $key => $aud_protocolo) {
                    $protocolo_fallback = null;
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if (!is_null($protocolo)) {
                        $historia_actual = Historiaclinica::find($plano_cabecera->id_hc);
                        $agenda_actual   = $historia_actual ? Agenda::find($historia_actual->id_agenda) : null;
                        if ($agenda_actual) {
                            $inicio = Carbon::parse($agenda_actual->fechaini)->startOfDay();
                            $fin    = Carbon::parse($agenda_actual->fechaini)->endOfDay();
                            $agenda_alt = Agenda::where('id_paciente', $plano_cabecera->id_paciente)
                                ->where('id_seguro',   $plano_cabecera->id_seguro)
                                ->where('id_empresa',  $plano_cabecera->id_empresa)
                                ->whereBetween('fechaini', [$inicio, $fin])
                                ->where('id', '<>', $agenda_actual->id)
                                ->first();

                            if (!is_null($agenda_alt)) {
                                $historia_alt = Historiaclinica::where('id_agenda', $agenda_alt->id)->first();
                                if (!is_null($historia_alt)) {
                                    $protocolo_fallback = hc_protocolo::where('hcid', $historia_alt->hcid)->first();
                                }
                            }
                        }

                        if (!is_null($protocolo_fallback)) {
                            $protocolo = $protocolo_fallback;
                        }

                        if (!is_null($protocolo)) {
                            $cont = 0;
                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '2')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }

                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '3')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }

                            $hc_imagenes_protocolo = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '4')->get();
                            if (count($hc_imagenes_protocolo) > 0) {
                                foreach ($hc_imagenes_protocolo as $value) {
                                    $id_archivo = $value->id;
                                    if (!is_null($id_archivos)) {
                                        $id_archivos[$cont] = $id_archivo;
                                        $cont++;
                                    }
                                }
                                return $id_archivos;
                            }
                        }
                    }
                }
            }
            return $id_archivos;
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function buscar_agenda_archivos($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        try {
            $agenda = Agenda::where('id_paciente', $archivo_plano->id_paciente)->first();

            if (is_null($agenda)) {
                return 'No se encontro agenda para el paciente';
            }
            $id_archivos = [];
            $cont = 0;
            $agendas = Agenda::join('agenda_archivo as ah', 'ah.id_agenda', '=', 'agenda.id')
                ->where('agenda.id_paciente', $archivo_plano->id_paciente)
                ->whereNotNull('ah.archivo')
                ->select(
                    'agenda.id as id',
                    'agenda.id_paciente',
                    'ah.id as ahid',
                    'ah.ruta',
                    'ah.archivo',
                    'ah.tipo_documento',
                    'ah.texto'
                )
                ->orderBy('agenda.created_at', 'desc')
                ->get();
            foreach ($agendas as $value) {
                $archivo = Agenda_archivo::where('id_agenda', $value->id)->first();
                $id_archivo = $archivo->id;
                if (!is_null($id_archivos)) {
                    $id_archivos[$cont] = $id_archivo;
                    $cont++;
                }
            }
            return $id_archivos;
        } catch (\Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function buscar_adicionales($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $id_archivos = [];
                $cont = 0;
                $adicionales = Paciente_biopsia::where('id_paciente', $plano_cabecera->id_paciente)->where('estado', '1')->get();
                foreach ($adicionales as $value) {
                    $id_archivo = $value->id;
                    if (!is_null($id_archivos)) {
                        $id_archivos[$cont] = $id_archivo;
                        $cont++;
                    }
                }
                return $id_archivos;
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function updateHcCabeceraPlano($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $historiaClinica = Historiaclinica::find($archivo_plano->id_hc);
        if (!is_null($historiaClinica)) {
            $agenda_ori = Agenda::find($historiaClinica->id_agenda);
            if (!is_null($agenda_ori)) {
                $fechaOri = Carbon::parse($agenda_ori->fechaini)->toDateString();
                $agenda_correcta = Agenda::where('id_paciente', $archivo_plano->id_paciente)
                    ->whereDate('fechaini', $fechaOri)
                    ->where('id', '<>', $agenda_ori->id)
                    ->where('proc_consul', 1)
                    ->first();

                if (!is_null($agenda_correcta)) {
                    $historia_correcta = Historiaclinica::where('id_agenda', $agenda_correcta->id)->first();
                    if (!is_null($historia_correcta)) {
                        $procedimiento_ori = hc_procedimientos::where('id_hc', $historiaClinica->hcid)->first();
                        $id_procedimiento_ori = $procedimiento_ori->id;
                        $aud_protocolo_ori = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento_ori)->first();
                        if (!is_null($aud_protocolo_ori)) {
                            $protocolo = hc_protocolo::find($aud_protocolo_ori->id_protocolo_org);
                            if (!is_null($protocolo)) {
                                if (is_null($aud_protocolo_ori->motivo) || is_null($aud_protocolo_ori->conclusion) || is_null($aud_protocolo_ori->hallazgos)) {
                                    $arr_protocolo = [
                                        'motivo'     => is_null($aud_protocolo_ori->motivo)     ? $protocolo->motivo     : $aud_protocolo_ori->motivo,
                                        'conclusion' => is_null($aud_protocolo_ori->conclusion) ? $protocolo->conclusion : $aud_protocolo_ori->conclusion,
                                        'hallazgos'  => is_null($aud_protocolo_ori->hallazgos)  ? $protocolo->hallazgos  : $aud_protocolo_ori->hallazgos,
                                    ];
                                    $aud_protocolo_ori->update($arr_protocolo);
                                    $aud_protocolo_ori = Aud_Hc_Protocolo::find($aud_protocolo_ori->id);
                                }
                            }
                        }

                        $procedimiento_correcto = hc_procedimientos::where('id_hc', $historia_correcta->hcid)->first();
                        $id_procedimiento = $procedimiento_correcto->id;
                        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
                        if (!is_null($aud_protocolo)) {
                            $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                            if (!is_null($protocolo)) {
                                if (is_null($aud_protocolo->motivo) || is_null($aud_protocolo->conclusion) || is_null($aud_protocolo->hallazgos)) {
                                    $arr_protocolo = [
                                        'motivo'     => is_null($aud_protocolo->motivo)     ? $protocolo->motivo     : $aud_protocolo->motivo,
                                        'conclusion' => is_null($aud_protocolo->conclusion) ? $protocolo->conclusion : $aud_protocolo->conclusion,
                                        'hallazgos'  => is_null($aud_protocolo->hallazgos)  ? $protocolo->hallazgos  : $aud_protocolo->hallazgos,
                                    ];
                                    $aud_protocolo->update($arr_protocolo);
                                    $aud_protocolo = Aud_Hc_Protocolo::find($aud_protocolo->id);
                                }
                            }
                        }
                        $arr = [
                            'id_hc' => $historia_correcta->hcid,
                            'id_hc_procedimimentos' => $procedimiento_correcto->id,
                            'ip_modificacion' => $historiaClinica->hcid . '_cambiohcid_' . $archivo_plano->id_hc_procedimimentos . '_'  . $archivo_plano->mes_plano,
                        ];
                        $archivo_plano->update($arr);
                        return ['status' => 'success', 'msj' => $historiaClinica->hcid . '_cambiohcid_' . $archivo_plano->id_hc_procedimimentos . '_'  . $archivo_plano->mes_plaano];
                    } else {
                        return ['status' => 'error', 'msj' => 'No se encontro historia clinica correcta'];
                    }
                } else {
                    return ['status' => 'error', 'msj' => 'No se encontro ningun agenda'];
                }
            } else {
                return ['status' => 'error', 'msj' => 'No existe la agenda original'];
            }
        } else {
            return ['status' => 'error', 'msj' => 'No existe la historia clinica original'];
        }
    }
    public function updateDoctorExaminador($id_hc_procedimiento, $id_empresa, $tipo = 'e')
    {
        $doctores_staff = [];
        if ($tipo == 'f') {
            $doctores_staff = ['1314490929'];
        } else {
            if ($id_empresa == '1307189140001') {
                $doctores_staff = ['1307189140'];
            } elseif ($id_empresa == '0992704152001') {
                $doctores_staff = ['1306579234', '0916461221', '1104260235', '1713796835', '1717930919'];
            }
        }

        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)->first();
        if (is_null($aud_hc_procedimiento)) {
            return;
        }

        $doctor_actual = $aud_hc_procedimiento->id_doctor_examinador2;

        // Si el doctor actual NO está en el staff, asignar uno aleatorio
        if (!in_array($doctor_actual, $doctores_staff)) {
            $doctor_seleccionado = $doctores_staff[array_rand($doctores_staff)];
            $aud_hc_procedimiento->update([
                'id_doctor_examinador2' => $doctor_seleccionado
            ]);
        }
    }
    // planilla individual
    public function descargar_planilla($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if (!is_null($hc_procedimiento)) {
                $txt_cie10 = null;
                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')
                    ->get();

                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina', 'apd.valor_unitario')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.porcent_10')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

                $view = View::make('archivo_plano.planilla.planilla_pdf', compact('archivo_plano', 'txt_cie10', 'medicinas', 'insumos', 'laboratorio', 'servicios_ins', 'imagen', 'equipos', 'honor_medicos'))->render();

                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->loadHTML($view);
                return $pdf->download('planilla_pdf_' . $hc_procedimiento->historia->paciente->apellido1 . '_' . $hc_procedimiento->historia->paciente->nombre1 . '_.pdf');
            } else {
                return 'No se encontro el procedimiento';
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_planilla_temp($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $txt_cie10 = null;
            $cie10 = Cie_10_3::find($archivo_plano->cie10);
            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($archivo_plano->cie10);
                if (!is_null($cie10)) {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }
            } else {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }

            $honor_medicos = Db::table('archivo_plano_detalle as apd')
                ->where('id_ap_cabecera', $archivo_plano->id)
                ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                ->orderby('apd.porcentaje_honorario', 'desc')
                ->orderby('apt.secuencia', 'asc')
                ->where('apt.tipo_ex', 'HME')
                ->where('apd.estado', '1')
                ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                ->get();

            $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
            $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.iva_unitario', 'apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
            $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();
            $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();
            $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
            $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
            $view = View::make('archivo_plano.planilla.planilla010iva_pdf', compact('archivo_plano', 'txt_cie10', 'medicinas', 'insumos', 'laboratorio', 'servicios_ins', 'imagen', 'equipos', 'honor_medicos'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->download('P_INDIVIDUAL.pdf');
        } else {
            return 'no se encontro cabecera';
        }
    }
    public function descargar_planilla_consolidada($num_paciente, $id_empresa, $mes_plano, $seguro)
    {
        $cabeceras = Archivo_Plano_Cabecera::where('numero_paciente', $num_paciente)->where('id_empresa', $id_empresa)->where('id_seguro', $seguro)->where('mes_plano', $mes_plano)->where('estado', '1')->orderBy('fecha_ing', 'asc')->get();
        $cont = count($cabeceras);
        if (count($cabeceras) > 0) {
            $fecha_ingreso = $cabeceras[0]->fecha_ing;
            $ult_cab = $cont - 1;
            $fecha_egreso = $cabeceras[$ult_cab]->fecha_alt;
            $txt_cie10 = null;

            $cie10 = Cie_10_3::find($cabeceras[0]->cie10);
            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($cabeceras[0]->cie10);
                if (!is_null($cie10)) {
                    $txt_cie10 = '(' . $cabeceras[0]->cie10 . ') ' . $cie10->descripcion;
                }
            } else {
                $txt_cie10 = '(' . $cabeceras[0]->cie10 . ') ' . $cie10->descripcion;
            }
            $nom_proc = "";
            $flag = false;
            $arr_hm = [];
            $arr_med = [];
            $arr_ins = [];
            $arr_lab = [];
            $arr_serv = [];
            $arr_im = [];
            $arr_eq = [];
            foreach ($cabeceras as $cab) {
                if ($flag == true) {
                    $nom_proc .= ', ' . $cab->nom_procedimiento;
                } else {
                    $nom_proc .= $cab->nom_procedimiento;
                }

                $flag = true;
                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $cab->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();
                if (count($honor_medicos) > 0) {
                    array_push($arr_hm, $honor_medicos);
                }
                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
                if (count($medicinas) > 0) {
                    array_push($arr_med, $medicinas);
                }
                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.iva_unitario', 'apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
                if (count($insumos) > 0) {
                    array_push($arr_ins, $insumos);
                }
                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();
                if (count($laboratorio) > 0) {
                    array_push($arr_lab, $laboratorio);
                }
                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();
                if (count($servicios_ins) > 0) {
                    array_push($arr_serv, $servicios_ins);
                }
                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
                if (count($imagen) > 0) {
                    array_push($arr_im, $imagen);
                }
                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
                if (count($equipos) > 0) {
                    array_push($arr_eq, $equipos);
                }
            }
            $view = View::make('archivo_plano.planilla.nuevo_planilla_pdf', compact('cabeceras', 'txt_cie10', 'arr_med', 'arr_ins', 'arr_lab', 'arr_serv', 'arr_im', 'arr_eq', 'arr_hm', 'fecha_ingreso', 'fecha_egreso', 'nom_proc'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('P_INDIVIDUAL.pdf');
        }
        return "no existe";
    }
    public function descargar_planilla_consolidada_temp($num_paciente, $id_empresa, $mes_plano, $seguro)
    {
        $cabeceras = Archivo_Plano_Cabecera::where('numero_paciente', $num_paciente)->where('id_empresa', $id_empresa)->where('id_seguro', $seguro)->where('mes_plano', $mes_plano)->where('estado', '1')->orderBy('fecha_ing', 'asc')->get();
        $cont = count($cabeceras);
        if (count($cabeceras) > 0) {
            $fecha_ingreso = $cabeceras[0]->fecha_ing;
            $ult_cab = $cont - 1;
            $fecha_egreso = $cabeceras[$ult_cab]->fecha_alt;
            $txt_cie10 = null;
            $cie10 = Cie_10_3::find($cabeceras[0]->cie10);
            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($cabeceras[0]->cie10);
                if (!is_null($cie10)) {
                    $txt_cie10 = '(' . $cabeceras[0]->cie10 . ') ' . $cie10->descripcion;
                }
            } else {
                $txt_cie10 = '(' . $cabeceras[0]->cie10 . ') ' . $cie10->descripcion;
            }
            $nom_proc = "";
            $flag = false;
            $arr_hm = [];
            $arr_med = [];
            $arr_ins = [];
            $arr_lab = [];
            $arr_serv = [];
            $arr_im = [];
            $arr_eq = [];
            foreach ($cabeceras as $cab) {
                if ($flag == true) {
                    $nom_proc .= ', ' . $cab->nom_procedimiento;
                } else {
                    $nom_proc .= $cab->nom_procedimiento;
                }
                $flag = true;

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $cab->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')
                    ->get();
                if (count($honor_medicos) > 0) {
                    array_push($arr_hm, $honor_medicos);
                }
                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina', 'apd.valor_unitario')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
                if (count($medicinas) > 0) {
                    array_push($arr_med, $medicinas);
                }
                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.iva_unitario', 'apd.p10_unitario', 'apd.porcent_10')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
                if (count($insumos) > 0) {
                    array_push($arr_ins, $insumos);
                }
                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();
                if (count($laboratorio) > 0) {
                    array_push($arr_lab, $laboratorio);
                }
                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();
                if (count($servicios_ins) > 0) {
                    array_push($arr_serv, $servicios_ins);
                }
                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
                if (count($imagen) > 0) {
                    array_push($arr_im, $imagen);
                }
                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $cab->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
                if (count($equipos) > 0) {
                    array_push($arr_eq, $equipos);
                }
            }
            $view = View::make('archivo_plano.planilla.iva10porcen_nuevo_planilla_pdf', compact('cabeceras', 'txt_cie10', 'arr_med', 'arr_ins', 'arr_lab', 'arr_serv', 'arr_im', 'arr_eq', 'arr_hm', 'fecha_ingreso', 'fecha_egreso', 'nom_proc'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->download('P_INDIVIDUAL.pdf');
        }
        return "no existe";
    }
    public function descargar_planilla_msp($id_plano)
    {
        try {
            $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);

            if (is_null($archivo_plano)) {
                return 'No se encontro cabecera';
            }

            $agend = Agenda::find($archivo_plano->id_agenda);
            $detalles = Archivo_Plano_Detalle::where('id_ap_cabecera', $archivo_plano->id)
                ->where('estado', 1)
                ->get();

            $fecha_elaboracion = date('d/m/Y');

            $fecha_desde_invert = '';
            $fecha_hasta_invert = '';

            $fecha_desde = substr($archivo_plano->fecha_alt, 0, 10);
            $invert_fech_desde = explode('-', $fecha_desde);
            $anio_servicio = $invert_fech_desde[0];


            //Detalle de Servicio Desde
            $fecha_desde = substr($archivo_plano->fecha_ing, 0, 10);
            $invert_fech_desde = explode('-', $fecha_desde);
            $fecha_desde_invert = $invert_fech_desde[2] . "/" . $invert_fech_desde[1] . "/" . $invert_fech_desde[0];

            //Detalle de Servicio Hasta
            $fecha_hasta = substr($archivo_plano->fecha_alt, 0, 10);
            $invert_fech_hasta = explode('-', $fecha_hasta);
            $fecha_hasta_invert = $invert_fech_hasta[2] . "/" . $invert_fech_hasta[1] . "/" . $invert_fech_hasta[0];

            //Mes Servicio
            $mes_servicio = $invert_fech_desde[1];

            $txt_mes = '';
            if ($mes_servicio == '12') {
                $txt_mes = 'DICIEMBRE';
            } elseif ($mes_servicio == '11') {
                $txt_mes = 'NOVIEMBRE';
            } elseif ($mes_servicio == '10') {
                $txt_mes = 'OCTUBRE';
            } elseif ($mes_servicio == '09') {
                $txt_mes = 'SEPTIEMBRE';
            } elseif ($mes_servicio == '08') {
                $txt_mes = 'AGOSTO';
            } elseif ($mes_servicio == '07') {
                $txt_mes = 'JULIO';
            } elseif ($mes_servicio == '06') {
                $txt_mes = 'JUNIO';
            } elseif ($mes_servicio == '05') {
                $txt_mes = 'MAYO';
            } elseif ($mes_servicio == '04') {
                $txt_mes = 'ABRIL';
            } elseif ($mes_servicio == '03') {
                $txt_mes = 'MARZO';
            } elseif ($mes_servicio == '02') {
                $txt_mes = 'FEBRERO';
            } elseif ($mes_servicio == '01') {
                $txt_mes = 'ENERO';
            }

            $mes_anio_servicio = $txt_mes . "-" . $anio_servicio;
            $txt_cie10 = null;
            $cie10 = Cie_10_3::find($archivo_plano->cie10);
            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($archivo_plano->cie10);
                if (!is_null($cie10)) {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }
            } else {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }

            $planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')
                ->where('apd.id_ap_cabecera', $archivo_plano->id)
                ->where('apd.estado', '1')
                ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                ->select(
                    'apd.fecha',
                    'apd.codigo',
                    'apd.descripcion',
                    'apt.clasificado',
                    'apd.cantidad',
                    'apd.valor',
                    'apd.subtotal',
                    'apd.clasif_porcentaje_msp',
                    'apd.valor_porcent_clasifi',
                    'apd.total_solicitado_usd',
                    'apd.tipo',
                    'apd.clasificador',
                    'apd.valor_unitario',
                    'apd.porcentaje_iva',
                    'apd.porcent_10',
                    'apd.iva_unitario',
                    'apd.p10_unitario',
                    'apd.total'
                )
                ->orderBy('apt.orden_msp', 'asc')
                ->orderBy('apd.id', 'asc')
                ->get();

            $total_inicio = 0;
            foreach ($planilla_msp_detalle as $value) {
                $total_inicio += (float) $value->total_solicitado_usd;
            }

            $empresa_nombre = '';
            if (!is_null($archivo_plano->empresa)) {
                $empresa_nombre = $archivo_plano->empresa->razonsocial;
            }

            $paciente_nombre = '';
            if (!is_null($archivo_plano->paciente)) {
                $paciente_nombre =
                    trim($archivo_plano->paciente->apellido1 . ' ' .
                        $archivo_plano->paciente->apellido2 . ' ' .
                        $archivo_plano->paciente->nombre1 . ' ' .
                        $archivo_plano->paciente->nombre2);
            }

            $paciente_identificacion = $archivo_plano->id_paciente;
            $tipo_servicio = 'AMBULATORIO';
            $total = 0;
            $detalles_pdf = [];

            foreach ($planilla_msp_detalle as $value) {
                $fecha_detalle = '';
                if (!empty($value->fecha)) {
                    $invert_fecha = substr($value->fecha, 0, 10);
                    $invert_fech_deta = explode('-', $invert_fecha);
                    $fecha_detalle = $invert_fech_deta[2] . "/" . $invert_fech_deta[1] . "/" . $invert_fech_deta[0];
                }
                $codigo = '';
                if (!in_array($value->tipo, ['IV', 'I', 'M', 'IVC'])) {
                    $codigo = $value->codigo;
                }

                if ($value->codigo == '16') {
                    $ap_procedimiento = ApProcedimiento::where('codigo', $value->codigo)
                        ->where('tipo', $value->tipo)
                        ->first();

                    if (!is_null($ap_procedimiento)) {
                        $codigo = $ap_procedimiento->cod_cb;
                    }
                }

                $valor_unitario = round(
                    (float) $value->valor_unitario +
                        (float) $value->p10_unitario +
                        (float) $value->iva_unitario,
                    2
                );

                if ($value->valor_porcent_clasifi > 0) {
                    $value->total_solicitado_usd += $value->valor_porcent_clasifi;
                }

                $clasificador_porcentaje = round((float) $value->clasif_porcentaje_msp, 2);
                $subtotal_usd = round((float) $value->total, 2);
                $valor_modificador = round((float) $value->valor_porcent_clasifi, 2);
                $total_solicitado = round((float) $value->total_solicitado_usd, 2);
                $total += (float) $value->total_solicitado_usd;
                $detalles_pdf[] = [
                    'fecha' => $fecha_detalle,
                    'codigo' => $codigo,
                    'tipo' => $value->tipo,
                    'clasificador' => $value->clasificador,
                    'descripcion' => $value->descripcion,
                    'cantidad' => $value->cantidad,
                    'valor_unitario' => $valor_unitario,
                    'clasificador_porcentaje' => $clasificador_porcentaje,
                    'subtotal_usd' => $subtotal_usd,
                    'valor_modificador' => $valor_modificador,
                    'total_solicitado' => $total_solicitado,
                ];
            }

            $data = [
                'empresa_nombre' => $empresa_nombre,
                'tipo_servicio' => $tipo_servicio,
                'fecha_elaboracion' => $fecha_elaboracion,
                'monto_solicitado' => $total_inicio,
                'mes_anio_servicio' => $mes_anio_servicio,
                'nro_cv' => $archivo_plano->cod_deriva_msp,
                'cie10' => $archivo_plano->cie10,
                'txt_cie10' => $txt_cie10,
                'paciente_identificacion' => $paciente_identificacion,
                'paciente_nombre' => $paciente_nombre,
                'fecha_desde' => $fecha_desde_invert,
                'fecha_hasta' => $fecha_hasta_invert,
                'detalles' => $detalles_pdf,
                'total' => round($total, 2),

                // firma igual al excel que subiste
                'firma_nombre' => 'DRA. AURA EDITH ZAMBRANO PAREDES',
                'firma_cc' => '0926862152',
                'firma_cargo' => 'COORDINADORA DE AUDITORIA MEDICA',
            ];

            $pdf = App::make('dompdf.wrapper');
            $pdf->setPaper('a4', 'portrait');
            $pdf->loadView('archivo_plano/planilla/planilla_msp_PDF', $data);

            return $pdf->stream('Planilla_MSP_' . $archivo_plano->id . '.pdf');
        } catch (\Exception $e) {
            return 'Eroor: ' . $e->getMessage();
        }
    }
    public function descargar_factura($id_plano)
    {
        $cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($cabecera)) {
            $archivo = ApArchivoFactura::where('id_empresa', $cabecera->id_empresa)->where('mes_plano', $cabecera->mes_plano)->where('estado', 1)->first();
            $nombre_archivo = "";
            if (!is_null($archivo)) {
                $nombre_archivo = $archivo->nombre;
                $path            = url('storage/app/facturas_auditoria/' . $nombre_archivo);
                return $path;
            } else {
                return 'no se encontro el archivo';
            }
        } else {
            return 'no se encontro registro';
        }
    }
    //acta entrega
    public function descargar_acta_entrega($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $idhc = $plano_cabecera->id_hc_procedimimentos;
                $archivo_historico = Archivo_historico::where('id_historia', $idhc)->where('tipo_documento', 'ACT')->first();
                if (!is_null($archivo_historico)) {
                    $documento         = Documento::find($archivo_historico->id_documento);
                    if (!is_null($documento)) {
                        if (strtoupper($documento->tipo_documento) == 'ACT') {
                            $empresaxdoc       = Empresa::find($documento->id_empresa);
                            $historia          = $this->carga_hc($idhc); //($archivo_historico->id_historia);
                            $agenda            = Agenda::find($historia->id_agenda);
                            $seguro            = Seguro::find($historia->id_seguro);
                            $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
                            $paciente          = Paciente::find($historia->id_paciente);
                            $doctor            = User::find($historia->id_doctor1);
                            $procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento', $agenda->id_procedimiento)->first();
                            $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                            $paper_size = array(0, 0, 595, 920);
                            $data       = $historia;
                            $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historia->hcid)
                                ->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')
                                ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
                                ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                                ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

                            if (is_null($protocolo)) {
                                $date = $agenda->fechaini;
                            } elseif (!is_null($protocolo)) {
                                if (!is_null($protocolo->fecha)) {
                                    $date = $protocolo->fecha;
                                } else {
                                    $date = $agenda->fechaini;
                                }
                            }
                            //dd($documento);
                            //hc_admision.formato.actaentrega_v2023
                            //$view = View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            if ($documento->formato == 'actaentrega' && date("Y-m-d", strtotime($agenda->fechaini)) > date("Y-m-d", strtotime("2023-06-01"))) {
                                $view = View::make('hc_admision.formato.actaentrega_v2023', compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            } else {
                                $view = View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor', 'procedimiento_empresa'))->render();
                            }
                            $pdf  = App::make('dompdf.wrapper');

                            if ($documento->formato == 'contrareferencia') {

                                $pdf->setOptions(['dpi' => 96]);
                                $paper_size = array(0, 0, 1100, 1650);
                                $pdf->setpaper($paper_size);
                                $pdf->loadHTML($view);
                            } else {
                                $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
                            }

                            return $pdf->stream($historia->id_paciente . '_' . $documento->tipo_documento . '_' . $archivo_historico->id_historia . '_' . $archivo_historico->id . '.pdf');
                        } else {
                            return 'el documento no es una acta de entrega';
                        }
                    } else {
                        return 'no se encontro el documento';
                    }
                } else {
                    return ' no se encontro el archivo historico';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    private function carga_hc($hcid)
    {
        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)
            ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
            ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
            ->join('users', 'users.id', 'paciente.id_usuario')
            ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
            ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
            ->select(
                'historiaclinica.*',
                'paciente.nombre1',
                'paciente.nombre2',
                'paciente.apellido1',
                'paciente.apellido2',
                'seguros.nombre',
                'agenda.fechaini',
                'paciente.fecha_nacimiento',
                'agenda.proc_consul',
                'agenda.id_procedimiento',
                'seguros.tipo',
                'subseguro.nombre as sbnombre',
                'paciente.telefono1',
                'paciente.telefono2',
                'paciente.telefono3',
                'users.nombre1 as unombre1',
                'users.nombre2 as unombre2',
                'users.apellido1 as uapellido1',
                'users.apellido2 as uapellido2',
                'paciente.parentesco',
                'users.telefono1 as utelefono1',
                'users.telefono2 as utelefono2',
                'users.id as uid',
                'paciente.parentescofamiliar as fparentesco',
                'paciente.nombre1familiar as fnombre1',
                'paciente.nombre2familiar as fnombre2',
                'paciente.apellido1familiar as fapellido1',
                'paciente.apellido2familiar as fapellido2',
                'paciente.id_usuario',
                'paciente.cedulafamiliar',
                'agenda.id_sala as idsala'
            )->first();

        return $historiaclinica;
    }
    //epicrisis (006)
    public function descargar_epicrisis_nuevo($id_plano)
    {

        $procedimiento = "";
        $firma         = "";
        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)->first();
            if (is_null($epicrisis)) {
                $this->crear_epicrisis($plano_cabecera->id_hc, $plano_cabecera->id_hc_procedimimentos);
                $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)->first();
            }
            $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
            $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first();
            $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
            $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
            $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
            $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();
            $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();
            $cie10e = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();

            $doctor = null;

            if (!is_null($protocolo)) {
                $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
                if (!is_null($procedimiento)) {
                    $id_seguro  = $procedimiento->id_seguro;
                    $id_empresa = $procedimiento->id_empresa;
                    if ($procedimiento->id_seguro == null) {
                        $id_seguro = $historiaclinica->id_seguro;
                    }
                    if ($procedimiento->id_empresa == null) {
                        $id_empresa = $historiaclinica->id_empresa;
                        $procedimiento->id_empresa = $id_empresa;
                    }
                    $xseguro = Seguro::find($id_seguro);

                    if ($procedimiento->id_doctor_responsable == null) {
                        $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
                        $doctor = User::find($procedimiento->id_doctor_examinador2);
                    } else {
                        $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_responsable)->first();
                        $doctor = User::find($procedimiento->id_doctor_responsable);
                    }
                    if ($xseguro->tipo == '0') {
                        if ($id_empresa == '1307189140001') {
                            $firma = Firma_Usuario::where('id_usuario', '1307189140')->first();
                            $doctor = User::find('1307189140');
                        }
                        if ($id_empresa == '0992704152001') {
                            if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                                $firma = Firma_Usuario::where('id_usuario', '094346835')->first();
                                $doctor = User::find('094346835');
                            }
                        }
                    }
                }
            }

            $tiene_receta = 'NO';
            $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
            if (!is_null($receta)) {
                $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                if ($receta_det > 0 || $receta->prescripcion != null) {
                    $tiene_receta = 'SI';
                }
            }

            $tiene_receta = $epicrisis->receta;
            $nombre_doc = "";
            if (!is_null($firma)) {
                $nombre_doc = User::find($firma->id_usuario);
            }
            $data = $historiaclinica;
            if (is_null($procedimiento->empresa)) {
                $procedimiento->id_empresa = $id_empresa;
            }
            $receta = hc_receta::where('id_hc', $data->hcid)->first();
            $hc_procedimiento_f = Hc_Procedimiento_Final::join('procedimiento', 'procedimiento.id', '=', 'hc_procedimiento_final.id_procedimiento')
                ->where('hc_procedimiento_final.id_hc_procedimientos', $plano_cabecera->id_hc_procedimimentos)
                ->whereNotNull('procedimiento.id_grupo_procedimiento')
                ->select('hc_procedimiento_final.*', 'procedimiento.id', 'procedimiento.nombre')
                ->first();

            //Fausto

            $texto = $epicrisis->cuadro_clinico;
            $tieneCamposVacios = false;


            if (preg_match('/APP:\s*<br\s*\/?>/i', $texto) && preg_match('/APF:\s*<br\s*\/?>/i', $texto)  && preg_match('/APQX:\s*<br\s*\/?>/i', $texto)) {
                $tieneCamposVacios = true;
            }

            if ($tieneCamposVacios) {
                $consulta = DB::table('ap_plantilla_procedimiento')
                    ->where("id_procedimiento", $hc_procedimiento_f->id_procedimiento)
                    ->where("estado", 1)
                    ->inRandomOrder()
                    ->first();
                if ($consulta) {
                    $epicrisis->cuadro_clinico = $consulta->plantilla;
                }
            }

            $defaultFavorableDes = 'PACIENTE EGRESA DE QUIROFANO ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS ENDOSCOPICOS. PCTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. ES DADA DE ALTA, SE EXPLICAN LOS RESULTADOS AL PACIENTE Y ACOMPAÑANTE. SE ENTREGAN INDICACIONES AL ALTA';

            if (!$epicrisis_original->favorable_des) {
                $epicrisis_original->favorable_des = $defaultFavorableDes;
            }

            if (!$epicrisis->favorable_des) {
                $epicrisis->favorable_des = $defaultFavorableDes;
            }
            $view = View::make('pdf_hospital.formulario006', compact('receta', 'cie10', 'cie10e', 'data', 'epicrisis', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento', 'epicrisis_original', 'hc_procedimiento_f'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setOptions(['chroot'  => base_path('/')]);
            $pdf->loadHTML($view)->setPaper('a4', 'portrait');
            return $pdf->stream('Epicrisis' . '.pdf');
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_epicrisis_antiguo($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)->first();
            if (!is_null($epicrisis)) {
                $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
                $historiaclinica = DB::table('historiaclinica')
                    ->where('historiaclinica.hcid', $epicrisis->hcid)
                    ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
                    ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
                    ->join('users', 'users.id', 'paciente.id_usuario')
                    ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
                    ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
                    ->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')
                    ->first();

                $cie10_in_pre = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();

                $cie10_in_def = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('aud_hc_cie10.ingreso_egreso', 'INGRESO')->get();

                $cie10_eg_pre = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();

                $cie10_eg_def = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('aud_hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('aud_hc_cie10.ingreso_egreso', 'EGRESO')->get();

                $cie10_3 = Cie_10_3::all();
                $cie10_4 = Cie_10_4::all();
                if (!is_null($protocolo)) {
                    $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();

                    if (!is_null($procedimiento)) {
                        $id_seguro  = $procedimiento->id_seguro;
                        $id_empresa = $procedimiento->id_empresa;
                        if ($procedimiento->id_seguro == null) {
                            $procedimiento->update(['id_seguro' => $historiaclinica->id_seguro]);
                            $id_seguro = $historiaclinica->id_seguro;
                        }
                        if ($procedimiento->id_empresa == null) {
                            $procedimiento->update(['id_empresa' => $historiaclinica->id_empresa]);
                            $id_empresa = $historiaclinica->id_empresa;
                        }
                        $xseguro = Seguro::find($id_seguro);
                        if ($procedimiento->id_doctor_responsable == null) {
                            $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
                        } else {
                            $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_responsable)->first();
                        }
                        if ($xseguro->tipo == '0') {
                            if ($id_empresa == '1307189140001') {
                                $firma = Firma_Usuario::where('id_usuario', '1307189140')->first();
                            }
                            if ($id_empresa == '0992704152001') {
                                if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                                    $firma = Firma_Usuario::where('id_usuario', '094346835')->first();
                                }
                            }
                        }
                    }

                    $tiene_receta = 'NO';
                    $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();

                    if (!is_null($receta)) {
                        $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                        if ($receta_det > 0 || $receta->prescripcion != null) {
                            $tiene_receta = 'SI';
                        }
                    }

                    $tiene_receta = $epicrisis->receta;

                    $nombre_doc = "";
                    if (!is_null($firma)) {
                        $nombre_doc = User::find($firma->id_usuario);
                    }

                    $doctor = "";
                    if (!is_null($procedimiento)) {

                        if ($procedimiento->id_doctor_responsable == null) {
                            $doctor = User::find($procedimiento->id_doctor_examinador2);
                        } else {
                            $doctor = User::find($procedimiento->id_doctor_responsable);
                        }
                        if ($xseguro->tipo == '0') {
                            if ($id_empresa == '1307189140001') {
                                $doctor = User::find('1307189140');
                            }
                            if ($id_empresa == '0992704152001') {
                                if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                                    $doctor = User::find('094346835');
                                }
                            }
                        }
                    }

                    $data = $historiaclinica;
                    $receta = hc_receta::where('id_hc', $data->hcid)->first();

                    $view = View::make('auditoria_hc_admision.formato.epicrisis', compact('receta', 'data', 'evolucion_1', 'epicrisis', 'cie10_in_pre', 'cie10_in_def', 'cie10_3', 'cie10_4', 'cie10_eg_pre', 'cie10_eg_def', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa'))->render();
                    $pdf  = App::make('dompdf.wrapper');

                    $pdf->loadHTML($view);
                    return $pdf->stream('epicrisis-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $procedimiento = "";
                    $firma         = "";
                }
            } else {
                return 'Epicrisis no encontrado';
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_epicrisis($id_plano)
    {
        $procedimiento = "";
        $firma         = "";
        $xtipo      = 'auditoria';

        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $aud_epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)->first();
            if (!is_null($aud_epicrisis)) {
                $epicrisis = Hc_Epicrisis::find($aud_epicrisis->id_hc_procedimiento);
                $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
                $hc_procedimiento = hc_procedimientos::where('id', $epicrisis_original->hc_id_procedimiento)->first();
                $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
                $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
                $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();
                $cie10e = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();
                if (!is_null($protocolo)) {
                    $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    if (!is_null($procedimiento)) {
                        $id_seguro  = $procedimiento->id_seguro;
                        $id_empresa = $procedimiento->id_empresa;
                        if ($procedimiento->id_seguro == null) {
                            $id_seguro = $historiaclinica->id_seguro;
                        }

                        if ($procedimiento->id_empresa == null) {
                            $id_empresa = $historiaclinica->id_empresa;
                        }

                        $xseguro = Seguro::find($id_seguro);
                        if ($procedimiento->id_doctor_responsable == null) {
                            $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
                        } else {
                            $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_responsable)->first();
                        }

                        if ($xseguro->tipo == '0') {
                            if ($id_empresa == '1307189140001') {
                                $firma = Firma_Usuario::where('id_usuario', '1307189140')->first();
                            }
                            if ($id_empresa == '0992704152001') {
                                if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                                    $firma = Firma_Usuario::where('id_usuario', '094346835')->first();
                                }
                            }
                        }
                    }
                }

                $tiene_receta = 'NO';
                $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
                if (!is_null($receta)) {
                    $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
                    if ($receta_det > 0 || $receta->prescripcion != null) {
                        $tiene_receta = 'SI';
                    }
                }
                $tiene_receta = $epicrisis->receta;
                $nombre_doc = "";
                if (!is_null($firma)) {
                    $nombre_doc = User::find($firma->id_usuario);
                }

                $doctor = "";
                if (!is_null($procedimiento)) {

                    if ($procedimiento->id_doctor_responsable == null) {
                        $doctor = User::find($procedimiento->id_doctor_examinador2);
                    } else {
                        $doctor = User::find($procedimiento->id_doctor_responsable);
                    }
                    if ($xseguro->tipo == '0') {
                        if ($id_empresa == '1307189140001') {
                            $doctor = User::find('1307189140');
                        }
                        if ($id_empresa == '0992704152001') {
                            if ($procedimiento->id_doctor_examinador2 == '0924611882') {
                                $doctor = User::find('094346835');
                            }
                        }
                    }
                }


                $data = $historiaclinica;
                $receta = hc_receta::where('id_hc', $data->hcid)->first();
                $view = View::make('pdf_hospital.formulario006', compact('receta', 'cie10ca', 'cie10', 'cie10e', 'data', 'epicrisis', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->stream('Epicrisis' . '.pdf');
            } else {
                return 'No se encontro epicrisis de auditoria';
            }
        } else {
            return 'No se encontro registro';
        }

        //dd($epicrisis->id);

    }
    //interconsulta (007)

    public function descargar_interconsulta_unificado_robot($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        $empresa = Empresa::where('id',$plano_cabecera->empresa->id)->first();
        $crm = User::where('id','1307189140')->first();
        
        if (!is_null($plano_cabecera)) {
            $historia_clinica = Historiaclinica::find($plano_cabecera->id_hc);
            $paciente = $historia_clinica->paciente;
            $cie10 = Hc_Cie10::where('hcid',$historia_clinica->hcid)->get();


            $procedimiento = hc_procedimientos::where('id_hc', $historia_clinica->hcid)->first(); 
            
           
            $doctor = "";
            if (!is_null($procedimiento)) {

                if ($procedimiento->id_doctor_responsable == null) {
                    $doctor = User::find($procedimiento->id_doctor_examinador2);
                } else {
                    $doctor = User::find($procedimiento->id_doctor_responsable);
                }
            }
            if ($doctor == "") {
                $doctor = $historia_clinica->doctor_1;
            }
            $vistaurl = "pdf_hospital.formulario007_unificado_robot";
            $view = View::make($vistaurl, compact('plano_cabecera','empresa','crm','doctor', 'cie10', 'historia_clinica', 'paciente'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('HCU_007.pdf', array("Attachment" => false));
        } else {
            return 'No se encontro historia clinica';
        }
    }



    public function descargar_interconsulta($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $interconsulta = Interconsulta::where('id_hc', $plano_cabecera->id_hc)->first();
            if (!is_null($interconsulta)) {
                $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $interconsulta->id)->get();
                $paciente       = $interconsulta->paciente;
                $age            = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                $empresa        = Empresa::where('prioridad', 1)->get()->first();
                $vistaurl = "hc4.consulta.formulario_007";
                $view     = View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10'))->render();

                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('007-' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
            } else {
                return 'No se encontro interconsulta';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_interconsulta_unificado($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $historia_clinica = Historiaclinica::find($plano_cabecera->id_hc);
            $empresa = Empresa::find($plano_cabecera->id_empresa);
            if (!is_null($historia_clinica)) {
                $paciente = $historia_clinica->paciente;
                $cardio = $historia_clinica->cardio;
                if (is_null($cardio)) {
                    $verificar = Archivo_Plano_Detalle::where('id_ap_cabecera', $id_plano)
                        ->whereIn('codigo', ['93000'])
                        ->where('estado', 1)
                        ->first();

                    if (!is_null($verificar)) {
                        $cardios = Cardiologia::join('historiaclinica as h', 'h.hcid', '=', 'hc_cardio.hcid')
                            ->where('h.id_paciente', $paciente->id)
                            ->select('hc_cardio.*')
                            ->get();

                        if ($cardios->isEmpty()) {
                            return 'no se encontro cardiologia';
                        }

                        $fechaHistoria = Carbon::parse($plano_cabecera->fecha_ing)->endOfDay();
                        $fechaMinima   = $fechaHistoria->copy()->subMonths(3)->startOfDay();
                        $cardio = Cardiologia::join('historiaclinica as h', 'h.hcid', '=', 'hc_cardio.hcid')
                            ->where('h.id_paciente', $paciente->id)
                            ->whereBetween('hc_cardio.fecha_formato', [
                                $fechaMinima->format('Y-m-d H:i:s'),
                                $fechaHistoria->format('Y-m-d H:i:s'),
                            ])
                            ->orderBy('hc_cardio.fecha_formato', 'desc')
                            ->select('hc_cardio.*')
                            ->first();

                        if (is_null($cardio)) {
                            return 'no existe cardiologia previa dentro de los 3 meses';
                        }
                    } else {
                        return 'Esta atencion no tiene cardiologia';
                    }
                }
                //$cardio->evolucion_orden;
                $evolucion_orden = Hc_Evolucion::where('hcid', $cardio->hcid)->first();
                $evolucion_orden->historiaclinica = $historia_clinica;
                $historia_cardio = Historiaclinica::find($cardio->hcid);
                $id_drsol = $historia_clinica->id_doctor1;
                $aud_hc = Aud_Hc_Procedimientos::where('id_hc', $historia_clinica->hcid)->first();
                if (!is_null($aud_hc)) {
                    $id_drsol = $aud_hc->id_doctor_examinador;
                }
                $cardio->id_drsol = $id_drsol;

                $cie10_orden = Hc_cardio_cie10::where('id_cardio', $cardio->id)->where('estado', '1')->get();
                $interconsulta = Interconsulta::where('id_hc', $historia_cardio->hcid)->where('estado', '1')->first();

                if (is_null($interconsulta)) {
                    $interconsulta = $this->crear_interconsulta($historia_cardio, $cardio);
                    $this->crear_diagnosticos_interconsulta($historia_cardio, $interconsulta);
                    $age = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                } else {
                    $interconsulta = $this->actualiza_interconsulta($historia_cardio, $cardio, $interconsulta);
                    $this->crear_diagnosticos_interconsulta($historia_cardio, $interconsulta);
                    $age = Carbon::createFromDate(substr($historia_cardio->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
                }
                $cie10 = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
                $vistaurl = "pdf_hospital.formulario007_unificado";
                $view = View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10', 'evolucion_orden', 'cardio', 'cie10_orden'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_007.pdf', array("Attachment" => false));
            } else {
                return 'No se encontro historia clinica';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function crear_interconsulta($historia_clinica, $cardio)
    {
        $idusuario = "9999966654";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $evolucion_orden = $cardio->evolucion_orden;
        $especialidad_res = $historia_clinica->agenda->especialidad->nombre;
        $especialidad = " ";
        $id_dr_inter = "";
        $aud_hc = Aud_Hc_Procedimientos::where('id_hc', $historia_clinica->hcid)->first();
        if (!is_null($evolucion_orden)) {
            $especialidad = $evolucion_orden->historiaclinica->agenda->especialidad->nombre;
        }
        if (!is_null($aud_hc)) {
            $id_dr_inter = $aud_hc->id_doctor_examinador;
        }
        $arr_inter_consulta = [
            'id_paciente'           => $historia_clinica->id_paciente,
            'id_doctor'             => $historia_clinica->id_doctor1,
            'id_empresa'            => $historia_clinica->agenda->id_empresa,
            'fecha'                 => $cardio->fecha_formato,
            'evolucion'             => $cardio->cuadro_clinico,
            'estado'                => '1',
            'resultados_exa'        => $cardio->resultados,
            'plan_terapeuticos'     => $cardio->plan_tratamiento,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_hc'                 => $historia_clinica->hcid,
            'resumen'               => $cardio->resumen,
            'cuadro_actual'         => $cardio->cuadro_actual,
            'especialidad'          => $especialidad,
            'especialidad_resultado'      => $especialidad_res,
            'id_doctor'             => $id_dr_inter,
        ];
        $interconsulta = Interconsulta::create($arr_inter_consulta);
        return $interconsulta;
    }
    public function actualiza_interconsulta($historia_clinica, $cardio, $interconsulta)
    {
        $interconsulta = Interconsulta::find($interconsulta->id);
        $idusuario = "9999966654";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $especialidad_res = $historia_clinica->agenda->especialidad->nombre;
        $evolucion_orden = $cardio->evolucion_orden;
        $id_dr_inter = "";
        $aud_hc = Aud_Hc_Procedimientos::where('id_hc', $historia_clinica->hcid)->first();
        $especialidad = " ";
        if (!is_null($evolucion_orden)) {
            $especialidad = $evolucion_orden->historiaclinica->agenda->especialidad->nombre;
        }
        if (!is_null($aud_hc)) {
            $id_dr_inter = $aud_hc->id_doctor_examinador;
        }
        $arr_inter_consulta = [
            'id_paciente'           => $historia_clinica->id_paciente,
            'id_doctor'             => $historia_clinica->id_doctor1,
            'id_empresa'            => $historia_clinica->agenda->id_empresa,
            'fecha'                 => $cardio->fecha_formato,
            //'evolucion'             => $cardio->cuadro_clinico,
            'estado'                => '1',
            'resultados_exa'        => $cardio->resultados,
            'plan_terapeuticos'     => $cardio->plan_tratamiento,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'id_hc'                 => $historia_clinica->hcid,
            'resumen'               => $cardio->resumen,
            'cuadro_actual'         => $cardio->cuadro_actual,
            'especialidad'          => $especialidad,
            'especialidad_resultado'      => $especialidad_res,
            'id_doctor'             => $id_dr_inter,
        ];
        $interconsulta->update($arr_inter_consulta);
        $interconsulta = Interconsulta::find($interconsulta->id);
        return $interconsulta;
    }
    public function crear_diagnosticos_interconsulta($historia_clinica, $interconsulta)
    {
        $idusuario = "9999966654";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cie10_cardio = Aud_Hc_Cie10::where('hcid', $historia_clinica->hcid)->get();
        $cie10_interconsulta = Interconsulta_Diagnostico::where('id_interconsulta', $interconsulta->id)->get();
        foreach ($cie10_interconsulta as $value) {
            $value->delete();
        }
        foreach ($cie10_cardio as $cie10_c) {
            $arr_cie_10 = [
                'id_interconsulta'      => $interconsulta->id,
                'cie10'                 => $cie10_c->cie10,
                'presuntivo_definitivo' => $cie10_c->presuntivo_definitivo,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
            ];
            Interconsulta_Diagnostico::create($arr_cie_10);
        }
    }
    //orden laboratorio (010)
    public function descargar_orden($id_orden)
    {
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        +$examenes      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('examen.publico_privado', '0')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '24')->orderBy('a10.id_agrupador')->get();

        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        $orden         = Examen_Orden::find($id_orden);
        $detalles      = $orden->detalles;
        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();
        $empresa = Empresa::find($orden->id_empresa);

        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        $texto       = '';
        foreach ($examenes as $examen) {
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;
            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }

        $evolucion = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            ->where('hc_evo.created_at', '<=', $orden->fecha_orden)
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = DB::table('hc_cie10')
                ->where('hc_cie10.hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i = 0;
            foreach ($hc_cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }
                $diagnosticos[$i]['cie10'] = $value->cie10;
                $diagnosticos[$i]['descripcion'] = $descripcion;
                $i++;
            }
        }

        $view = View::make('pdf_hospital.formulario10A', compact(
            'orden',
            'usuarios',
            'examenes',
            'agrupadores',
            'detalles',
            'empresa',
            'seguro',
            'protocolos',
            'arreglo',
            'seleccionados',
            'hematologia',
            'quimica',
            'inmunologia',
            'heces',
            'tumorales',
            'citologia',
            'cardiacos',
            'hormonas',
            'farmacos',
            'serologia',
            'biologia',
            'coagulacion',
            'orina',
            'inmunosupresor',
            'gases',
            'trasfusional',
            'vih',
            'diagnosticos'
        ))->render();

        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_010A.pdf');
    }
    //resultados
    public function descargar_resultado($id_orden)
    {
        $orden    = Examen_Orden::find($id_orden);
        $empresa = Empresa::find($orden->id_empresa);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $edad      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $fechaNacimiento = new DateTime($orden->pfecha_nacimiento);

        $diaNacimiento = $fechaNacimiento->format('d');
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $mesNacimiento = $fechaNacimiento->format('m');
        //nuevo
        $evolucion = Historiaclinica::where('historiaclinica.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'historiaclinica.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            ->where('hc_evo.created_at', '<=', $orden->fecha_orden)
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i = 0;
            foreach ($hc_cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                $diagnosticos[$i]['cie10'] = $value->cie10;
                $diagnosticos[$i]['descripcion'] = $descripcion;
                $i++;
            }
        }
        //fin nuevo

        $vistaurl = "laboratorio.orden.resultados_iess_pdf";
        $view     = View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'edad', 'ucreador', 'user', 'empresa', 'diaNacimiento', 'mesNacimiento', 'fecha', 'hora', 'diagnosticos'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('C_RESULTADOS.pdf');
    }
    //formulario010 Orden + Resultados
    public function dsecargar_orden_unificado($id_orden)
    {
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $examenes      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->where('examen.publico_privado', '0')->join('examen_agrupador_010 as a10', 'a10.id_examen', 'examen.id')->where('a10.estado', '1')->select('examen.*', 'a10.id_agrupador')->where('a10.id_agrupador', '24')->orderBy('a10.id_agrupador')->get();

        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        $tipo_usuario  = 11;
        $orden         = Examen_Orden::find($id_orden);
        $detalles      = $orden->detalles;
        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();
        $empresa = Empresa::find($orden->id_empresa);

        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        $texto       = '';
        foreach ($examenes as $examen) {
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;
            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }

        $fecha_evolucion = $orden->fecha_orden;
        if ($orden->fecha_convenios != null) {
            $fecha_evolucion = $orden->fecha_convenios;
        }

        $evolucion = Historiaclinica::where('historiaclinica.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'historiaclinica.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            //->where('hc_evo.created_at','<=',$orden->fecha_orden)
            ->whereRaw("DATE(hc_evo.created_at) <= ?", [$fecha_evolucion])
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i = 0;
            foreach ($hc_cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                $diagnosticos[$i]['cie10'] = $value->cie10;
                $diagnosticos[$i]['descripcion'] = $descripcion;
                $i++;
            }
        }

        //parte2
        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $edad      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $fechaNacimiento = new DateTime($orden->pfecha_nacimiento);

        $diaNacimiento = $fechaNacimiento->format('d');
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $mesNacimiento = $fechaNacimiento->format('m');

        $view = View::make('pdf_hospital.formulario010', compact(
            'orden',
            'usuarios',
            'examenes',
            'agrupadores',
            'detalles',
            'empresa',
            'seguro',
            'protocolos',
            'arreglo',
            'seleccionados',
            'hematologia',
            'quimica',
            'inmunologia',
            'heces',
            'tumorales',
            'citologia',
            'cardiacos',
            'hormonas',
            'farmacos',
            'serologia',
            'biologia',
            'coagulacion',
            'orina',
            'inmunosupresor',
            'gases',
            'trasfusional',
            'vih',
            'diagnosticos',
            'pct',
            'detalle',
            'resultados',
            'agrupador',
            'parametros',
            'edad',
            'ucreador',
            'user',
            'empresa',
            'diaNacimiento',
            'mesNacimiento',
            'fecha',
            'hora'
        ))->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        $vt_name = '';
        if ($tipo_usuario == 11) {
            $vt_name = 'HCU_010.pdf';
            return $pdf->stream($vt_name);
        }
    }
    //imagenologia (012)
    public function descargar_imageneologiaArobot($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (is_null($plano_cabecera)) {
                return 'No se encontro registro';
            }

            $idusuario = '9999966654';
            $ip_cliente = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
            $id_orden = null;
            $orden_012 = null;

            // 1) Camino principal: usa ap_ordenes generado por proceso de archivo plano.
            $ap_orden = Ap_Orden::where('id_ap_cabecera', $plano_cabecera->id)
                ->where('estado', '1')
                ->orderByRaw('CASE WHEN id_orden_012 IS NULL THEN 1 ELSE 0 END')
                ->orderByRaw('CASE WHEN id_orden IS NULL THEN 1 ELSE 0 END')
                ->orderBy('id', 'desc')
                ->first();

            if (!is_null($ap_orden)) {
                if (!is_null($ap_orden->id_orden_012)) {
                    $orden_012 = Orden_012::where('id', $ap_orden->id_orden_012)->where('estado', '1')->first();
                }
                if (is_null($orden_012) && !is_null($ap_orden->id_orden)) {
                    $id_orden = $ap_orden->id_orden;
                    $orden_012 = Orden_012::where('id_orden', $id_orden)->where('estado', '1')->orderBy('id', 'desc')->first();
                }
            }

            // 1.1) Fallback sin ap_orden: buscar orden por paciente y fecha_ing (-3 meses).
            if (is_null($orden_012)) {
                $fecha_ing = !is_null($plano_cabecera->fecha_ing) ? substr($plano_cabecera->fecha_ing, 0, 10) : null;
                if (!is_null($plano_cabecera->id_paciente) && !is_null($fecha_ing)) {
                    $fecha_desde = Carbon::parse($fecha_ing)->subMonths(3)->startOfDay()->toDateTimeString();
                    $fecha_hasta = Carbon::parse($fecha_ing)->endOfDay()->toDateTimeString();

                    $orden_en_rango = DB::table('orden as oo')
                        ->where('oo.id_paciente', $plano_cabecera->id_paciente)
                        ->whereBetween('oo.fecha_orden', [$fecha_desde, $fecha_hasta])
                        ->orderBy('oo.fecha_orden', 'desc')
                        ->orderBy('oo.id', 'desc')
                        ->select('oo.id', 'oo.fecha_orden')
                        ->first();

                    if (!is_null($orden_en_rango)) {
                        $id_orden = $orden_en_rango->id;
                        $orden_012 = Orden_012::where('id_orden', $id_orden)
                            ->where('estado', '1')
                            ->orderBy('id', 'desc')
                            ->first();
                    }
                }
            }

            $historiaclinica = Historiaclinica::find($plano_cabecera->id_hc);
            if (is_null($historiaclinica)) {
                return 'No se encontro historia';
            }

            $paciente = Paciente::find($historiaclinica->id_paciente);
            if (is_null($paciente)) {
                return 'No se encontro paciente';
            }

            $agenda = Agenda::find($historiaclinica->id_agenda);
            if (is_null($agenda)) {
                return 'No se encontro agenda';
            }

            // 2) Fallback: si no vino por ap_orden, ubicar orden_012 por evolucion/orden.
            $evolucion = null;
            if (!is_null($orden_012) && !is_null($orden_012->id_hc_evolucion)) {
                $evolucion = Hc_Evolucion::where('id', $orden_012->id_hc_evolucion)->where('secuencia', '0')->first();
            }
            if (is_null($evolucion)) {
                $evolucion = Hc_Evolucion::where('hcid', $historiaclinica->hcid)->where('secuencia', '0')->orderBy('id', 'desc')->first();
            }
            if (is_null($evolucion) && !is_null($plano_cabecera->id_hc_procedimimentos)) {
                $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)->where('secuencia', '0')->orderBy('id', 'desc')->first();
            }
            if (is_null($evolucion)) {
                return 'No se encontro evolucion';
            }

            if (is_null($id_orden) && !is_null($orden_012) && !is_null($orden_012->id_orden)) {
                $id_orden = $orden_012->id_orden;
            }

            if (is_null($orden_012) && !is_null($id_orden)) {
                $orden_012 = Orden_012::where('id_hc_evolucion', $evolucion->id)
                    ->where('estado', '1')
                    ->where('id_orden', $id_orden)
                    ->orderBy('id', 'desc')
                    ->first();
            }

            if (is_null($orden_012) && !is_null($id_orden)) {
                $orden_012 = Orden_012::where('id_orden', $id_orden)->where('estado', '1')->orderBy('id', 'desc')->first();
            }

            if (is_null($orden_012)) {
                if (is_null($id_orden)) {
                    return 'No se encontro orden para generar formulario 012';
                }

                $id_doctor = !is_null($historiaclinica->id_doctor1) ? $historiaclinica->id_doctor1 : $idusuario;
                $arr = [
                    'id_paciente'        => $paciente->id,
                    'id_doctor_solicita' => $id_doctor,
                    'id_doctor_firma'    => $id_doctor,
                    'fecha_orden'        => $agenda->fechaini,
                    'id_hc_evolucion'    => $evolucion->id,
                    'id_orden'           => $id_orden,
                    'estado'             => '1',
                    'rutina'             => '1',
                    'puede_mover'        => '1',
                    'motivo'             => $evolucion->motivo,
                    'id_empresa'         => $agenda->id_empresa,
                    'cuadro_clinico'     => $evolucion->cuadro_clinico,
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                ];

                $id_012 = Orden_012::insertGetId($arr);
                $orden_012 = Orden_012::find($id_012);

                if (!is_null($ap_orden)) {
                    $arr_ap = [
                        'id_orden_012' => $id_012,
                        'id_usuariomod' => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    if (is_null($ap_orden->id_orden)) {
                        $arr_ap['id_orden'] = $id_orden;
                    }
                    $ap_orden->update($arr_ap);
                }
            }

            // 3) Crear CIE10 solo si no existen para evitar duplicados por descargas repetidas.
            $total_cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->count();
            if ($total_cie10 == 0) {
                $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)->groupBy('cie10')->get();
                foreach ($cie10 as $value) {
                    $arr_cie10 = [
                        'id_orden_012'          => $orden_012->id,
                        'cie10'                 => $value->cie10,
                        'presuntivo_definitivo' => $value->presuntivo_definitivo,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                        'ip_creacion'           => $ip_cliente,
                        'ip_modificacion'       => $ip_cliente,
                    ];
                    Orden_012_Cie10::create($arr_cie10);
                }
            }

            $variables = $orden_012;
            $cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();
            $firma = Firma_Usuario::where('id_usuario', $variables->id_doctor_firma)->first();
            $view = View::make('pdf_hospital.formulario12A', compact("variables", "cie10", "firma"))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('formulario12A.pdf');
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }

    //imagenologia (012)
    public function descargar_imageneologiaA($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        $orden_012   = Orden_012::where('id_orden', $id_orden)->first();

        if (!is_null($orden_012)) {
            $paciente    = Paciente::find($orden_012->id_paciente);
            $id_empresa  = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
            $inf_empresa = Empresa::where('id', $orden_012->id_empresa)->OrderBy('estado', '1')->first();

            $id_seguro = $orden_012->evolucion->procedimiento->id_seguro;
            if ($id_seguro != null) {
                $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
            }
            $empresa = Empresa::find($id_empresa);
            $seguro  = Seguro::find($id_seguro);
            $doctor  = User::find($orden_012->id_doctor_firma);
            $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();
            $age   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();

            $fecha_d = date('Y/m/d');
            $vistaurl = "hc4.ordenes.orden_ingresada_formato012.formato012_pdf";
            $view     = View::make($vistaurl, compact('orden_012', 'paciente', 'seguro', 'inf_empresa', 'empresa', 'age', 'doctor', 'cie10'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->setPaper('A4', 'portairt');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'chroot'  => base_path('/')]);
            return $pdf->stream('orden-de-laboratorio-' . $id_orden . '.pdf');
        } else {
            return 'No existe la orden';
        }
    }
    public function descargar_imageneologiaB($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
            if (!is_null($protocolo)) {
                $cie_10eco = null;
                $nombre_procedimiento = "";
                $id_empresa = $plano_cabecera->id_empresa;
                $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

                $firma = Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->id_doctor_examinador2)->first();
                if ($protocolo->auditoria_procedimiento->seguro->id != '5') {
                    $firma = Firma_Usuario::where('id_usuario', '0961491354')->first();
                    if (!is_null($protocolo->procedimiento) && !is_null($protocolo->procedimiento->hc_procedimiento_final) && !is_null($protocolo->procedimiento->hc_procedimiento_final->procedimiento)) {
                        $nombre_procedimiento = $protocolo->procedimiento->hc_procedimiento_final->procedimiento->nombre;
                        if ($nombre_procedimiento !== null && preg_match('/ECOG/i', $nombre_procedimiento)) {
                            $cie_10eco = Cie_10_4::find('K760');
                        }
                    }
                }

                $view = View::make('pdf_hospital.formulario12B', compact('protocolo', 'empresa', 'firma', 'nombre_procedimiento', 'cie_10eco'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('formulario12B.pdf');
            } else {
                return 'No se encontro el protocolo de auditoria';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_imageneologia_unificado($id_plano)
    {
        $procedimiento = "";
        $firma         = "";
        $epicrisis_original = null;
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $ap_orden = Ap_Orden::where('id_ap_cabecera', $plano_cabecera->id)->first();
            if (!is_null($ap_orden)) {
                //A
                $orden012 = Orden_012::find($ap_orden->id_orden_012);
                $cie10   = Orden_012_Cie10::where('id_orden_012', $orden012->id)->get();
                $id_procedimiento = $ap_orden->id_hc_procedimientos;
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
                if (!is_null($aud_protocolo)) {
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if (!is_null($protocolo)) {
                        //B
                        $especialidad = Especialidad::find($protocolo->historiaclinica->agenda->espid);
                        $id_empresa = $plano_cabecera->id_empresa;
                        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
                        $firma = null;
                        $ruta_firma = "";
                        if (!is_null($protocolo->procedimiento->doctor_firma)) {
                            $firma = Firma_Usuario::where('id_usuario', '0961491354')->first();
                            if (!is_null($firma)) {
                                $ruta_firma = base_path() . '/storage/app/avatars/' . $firma->nombre;
                            }
                        }
                        $view = View::make('pdf_hospital.formulario12_unificado', compact("orden012", "cie10", 'protocolo', 'especialidad', 'empresa', 'ruta_firma'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        //$pdf->setPaper('A4', 'landscape');
                        $pdf->loadHTML($view);
                        return $pdf->stream('formulario012.pdf');
                    } else {
                        return 'No se encontrolo el protocolo';
                    }
                } else {
                    return 'No se encontro el protocolo de auditoria';
                }
            } else {
                return 'No se encontro ap_orden';
            }
        } else {
            return 'No se encontraron cabecera plano';
        }
    }
    //protocolo (017)
    public function descargar_protocolo_quirurgico($id_plano) //no usado
    {

        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
            //dd($protocolo);
            if (!is_null($protocolo)) {
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first(); //dd($protocolo_original, $hc_procedimiento);
                $empresa = $protocolo->procedimiento->empresa;
                $pxs = $hc_procedimiento->hc_procedimiento_f; //dd($pxs);
                $proc = null;
                $imagen = null;
                foreach ($pxs as $px) {
                    if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                        $proc = $px->procedimiento;
                    }
                }
                if ($proc != null) {
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }

                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                }

                $input = [
                    'fecha_operacion'     => $protocolo->fecha_operacion,
                    'hora_inicio'         => $protocolo->hora_inicio,
                    'hora_fin'            => $hora_fin,
                    'tipo_anestesia'      => $protocolo->tipo_anestesia,
                    'intervalo_anestesia' => $duracion,
                    'id_anestesiologo'    => $protocolo->id_anestesiologo,
                ];

                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
                $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'EGRESO')->get();
                $protocolo->update($input);

                $input2 = [
                    'id_doctor_examinador2'  =>  $protocolo->procedimiento->id_doctor_examinador,
                    'id_doctor_ayudante_con' => $protocolo->procedimiento->id_doctor_ayudante_con,
                ];
                $protocolo->procedimiento->update($input2);

                $firma = Firma_Usuario::where('id_usuario',  $protocolo->procedimiento->id_doctor_examinador)->first();

                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
                //dd($orden_biopsias);

                $xtipo = 'auditoria';
                $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'cie10_eg', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original', 'xtipo'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_017.pdf');
            } else {
                return 'No se encontro protocolo';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_protocolo_antiguo($id_plano) //antiguo
    {

        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
            if (!is_null($protocolo)) {
                $proyectado = $plano_cabecera->diagnostico;
                $realizado = $plano_cabecera->diagnostico;
                $id_agenda = $protocolo->historiaclinica->id_agenda;
                $agenda    = Agenda::find($id_agenda);

                $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador2;
                if ($protocolo->auditoria_procedimiento->id_doctor_responsable != null) {
                    $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_responsable;
                }
                if ($protocolo->auditoria_procedimiento->id_seguro != null) {
                    $seguro = Seguro::find($protocolo->auditoria_procedimiento->id_seguro);
                    if ($seguro->tipo == 0) {
                        if ($protocolo->auditoria_procedimiento->id_empresa == '1307189140001') {
                            $id_doctor_firma = '1307189140';
                        }
                        if ($protocolo->auditoria_procedimiento->id_empresa == '0992704152001') {
                            if ($id_doctor_firma == '0924611882') {
                                $id_doctor_firma = '094346835';
                            }
                        }
                    }
                }

                if ($id_doctor_firma == null) {
                    $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador;
                }

                if ($id_doctor_firma == null) {
                    $id_doctor_firma = $protocolo->historiaclinica->doctor_1->id;
                }

                $firma = Firma_Usuario::where('id_usuario', $id_doctor_firma)->first();
                $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;
                $view = View::make('auditoria_hc_admision.protocolo.prot_operatico', compact('protocolo', 'age', 'agenda', 'firma'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'portrait');

                return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');
            } else {
                return 'No se encontro protocolo';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_protocolo_nuevo($id_plano) //actual
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_empresa = $plano_cabecera->id_empresa;
            $empresa    = Empresa::find($id_empresa);
            $xtipo      = 'auditoria';
            $protocolo_original = null;
            $historia = Historiaclinica::find($plano_cabecera->id_hc);
            $hc_procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
            $hc_procedimientos = hc_procedimientos::where('id_hc', $plano_cabecera->id_hc)->get();

            if ($plano_cabecera->id_seguro == 5) {
                foreach ($hc_procedimientos as $value) {
                    $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $value->id)->first();
                    if ($protocolo->ntxt_procedimiento != null) {
                        break;
                    }
                }
            }

            if (!is_null($protocolo)) {
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $pxs = $hc_procedimiento->hc_procedimiento_f;
                $proc = null;
                $imagen = null;

                foreach ($pxs as $px) {
                    if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                        $proc = $px->procedimiento;
                    }
                }

                if ($proc != null) {
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }

                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();

                $id_agenda = $protocolo->historiaclinica->id_agenda;
                $agenda    = Agenda::find($id_agenda);
                $fecha_operacion = $protocolo->fecha_operacion;

                if ($fecha_operacion == null) {
                    $fecha_operacion = substr($agenda->fechaini, 0, 10);
                }

                $protocolo->hora_ini = substr($fecha_operacion, 11, 5);

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo->hora_ini));
                    $hora_fin = date('H:i', $hora_fin);
                }

                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)
                    ->where('ingreso_egreso', 'INGRESO')
                    ->get();

                $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)
                    ->where('ingreso_egreso', 'EGRESO')
                    ->get();

                $firma = Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->id_doctor_examinador2)->first();

                $cie10_eg1 = Hc_Cie10::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)
                    ->where('ingreso_egreso', 'EGRESO')
                    ->get();

                $cie10_in = Hc_Cie10::where('hc_id_procedimiento', $plano_cabecera->id_hc_procedimimentos)
                    ->where('ingreso_egreso', 'INGRESO')
                    ->get();

                $diagnostico_principal = '';
                $nombre_cie10 = $plano_cabecera->cie10 ?? '';

                if (!is_null($nombre_cie10) && trim($nombre_cie10) != '') {
                    $nombre_cie10 = trim($nombre_cie10);
                    $porciones = explode(")", $nombre_cie10);

                    if (count($porciones) > 1) {
                        // Cuando viene así: (K803) TEXTO
                        $codigo_cie10 = substr($porciones[0], 1);
                    } else {
                        // Cuando viene así: K803 o K803 TEXTO
                        $partes = explode(" ", $nombre_cie10);
                        $codigo_cie10 = trim($partes[0]);
                        $codigo_cie10 = str_replace(['(', ')'], '', $codigo_cie10);
                    }

                    $codigo_cie10 = trim($codigo_cie10);

                    $cie10_3 = Cie_10_3::where('id', $codigo_cie10)->first();
                    $cie10_4 = Cie_10_4::where('id', $codigo_cie10)->first();

                    if (!is_null($cie10_3)) {
                        $diagnostico_principal = '(' . $codigo_cie10 . ') ' . trim($cie10_3->descripcion);
                    } elseif (!is_null($cie10_4)) {
                        $diagnostico_principal = '(' . $codigo_cie10 . ') ' . trim($cie10_4->descripcion);
                    } else {
                        if (count($porciones) > 1) {
                            $descripcion = trim($porciones[1]);
                            $diagnostico_principal = '(' . $codigo_cie10 . ') ' . $descripcion;
                        } else {
                            $diagnostico_principal = $nombre_cie10;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Proyectado y realizado
                |--------------------------------------------------------------------------
                | Si protocolo tiene dato, se respeta.
                | Si protocolo está vacío, usa el diagnóstico principal de cabecera.
                | Solo se elimina el código cuando viene al inicio entre paréntesis:
                | (K803) CALCULO...
                |--------------------------------------------------------------------------
                */

                $diagnostico_principal_sin_codigo = trim($diagnostico_principal ?? '');

                if ($diagnostico_principal_sin_codigo != '') {
                    $diagnostico_principal_sin_codigo = preg_replace('/^\s*\([^)]*\)\s*/', '', $diagnostico_principal_sin_codigo);
                    $diagnostico_principal_sin_codigo = trim($diagnostico_principal_sin_codigo);
                }

                $proyectado = trim($protocolo->proyectado ?? '');

                if ($proyectado != '') {
                    $proyectado = preg_replace('/^\s*\([^)]*\)\s*/', '', $proyectado);
                    $proyectado = trim($proyectado);
                }

                $realizado = trim($protocolo->realizado ?? '');

                if ($realizado != '') {
                    $realizado = preg_replace('/^\s*\([^)]*\)\s*/', '', $realizado);
                    $realizado = trim($realizado);
                }

                if ($proyectado == '') {
                    $proyectado = $diagnostico_principal_sin_codigo;
                }

                if ($realizado == '') {
                    $realizado = $diagnostico_principal_sin_codigo;
                }

                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();

                $view = View::make('pdf_hospital.formulario017', compact(
                    'hc_procedimiento',
                    'pentax',
                    'protocolo',
                    'cie10',
                    'cie10_eg',
                    'cie10_eg1',
                    'firma',
                    'orden_biopsias',
                    'imagen',
                    'empresa',
                    'protocolo_original',
                    'xtipo',
                    'proyectado',
                    'realizado'
                ))->render();

                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view);

                return $pdf->stream('HCU_017.pdf');
            } else {
                return 'No se encontro protocolo de auditoria';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_cobertura_publica($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento', 'COB')->first();
                    if (!is_null($archivo_historico)) {
                        $name = storage_path('app/') . $archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) {
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        } else {
                            return 'No se encontro el archivo';
                        }
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_cobertura_privada($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento', 'CBRPRV')->first();
                    if (!is_null($archivo_historico)) {
                        $name = storage_path('app/') . $archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) {
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        } else {
                            return 'No se encontro el archivo';
                        }
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    //anestesia 
    public function descargar_anestesia($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            if (!is_null($id_procedimiento)) {
                $procedimientos_hc = hc_procedimientos::find($id_procedimiento);
                $id                = $procedimientos_hc->id_hc;
                $historia          = Historiaclinica::find($id);
                if (!is_null($historia)) {
                    $agenda            = Agenda::find($historia->id_agenda);
                    if (is_null($agenda)) {
                        return 'No se encontro la agenda';
                    }
                    $seguro            = Seguro::find($historia->id_seguro);
                    $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
                    $paciente          = Paciente::find($historia->id_paciente);
                    if (is_null($paciente)) {
                        return 'No se encontro el paciente';
                    }
                    $doctor            = User::find($historia->id_doctor1);
                    $record            = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id_procedimiento)->first();
                    if (!is_null($record)) {
                        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                        $paper_size = array(0, 0, 595, 920);
                        $data       = $historia;
                        $date       = $historia->created_at;
                        $csv        = hc_anestesiologia_csv::where('id_hc_anestesiologia', '=', $record->id)->get();
                        $pentax     = Pentax::where('hcid', '=', $id)->first();
                        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $pentax->id)->get();
                        $record_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', '=', $record->id)->get();
                        $view = View::make('hc_admision.formato.record-anestesico', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'record', 'historia', 'record_tecnicas', 'csv', 'procedimientos_pentax', 'procedimientos_hc'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->loadHTML($view);
                        return $pdf->stream($historia->id_paciente . '_RECORDANESTESICO_' . $id . '.pdf');
                    } else {
                        return 'No se encontro el record de anestesiologia';
                    }
                } else {
                    return 'No se encontro la historia clinica';
                }
            } else {
                return 'No se encontro procedimiento';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_pre_anestesiologo($id_plano)
    {

        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);

        if (!is_null($archivo_plano)) {
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if (!is_null($hc_procedimiento)) {
                $id_empresa = $archivo_plano->id_empresa;
                $empresa    = Empresa::find($id_empresa);
                $record = Hc_Anestesiologia::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                $anestesiologiamsp = User::where('id', 1759345422)->first();
                if (!is_null($record)) {
                    
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (is_null($pre_anestesico)) {
                        $arr_riesgo_preanestesico = [
                            'id_hc_anestesiologia' => $record->id,
                            'estado'               => 1,
                            'id_usuariocrea'       => '9999966654',
                            'id_usuariomod'        => '9999966654',
                            'ip_modificacion'      => '1',
                        ];
                        $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                        $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                    }
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (!is_null($pre_anestesico)) {
                        $this->actualiza_informacion($pre_anestesico->id);
                        $historia_clinica = Historiaclinica::where('hcid', $record->id_hc)->first();
                        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();
                        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();
                        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
                        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
                        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
                        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
                        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
                        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
                        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();
                        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $record->id)->get();
                        $agrupados = array();
                        foreach ($examen as $exam) {
                            if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
                            }
                        }
                        $agrupados1 = $agrupados;
                        $agrupados2 = [];
                        $totalAgrupadores = count($agrupados);
                        if ($totalAgrupadores > 4) {
                            $tamanoParte = ceil($totalAgrupadores / 2);
                            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true);
                            $agrupados1 = $agrupadosDivididos[0];
                            $agrupados2 = $agrupadosDivididos[1];
                        }
                        $hc_procedimiento_final = $historia_clinica->id_seguro == 5 ?  Hc_Procedimiento_Final::where("id_hc_procedimientos", $record->id_hc_procedimientos)->whereIn("id_procedimiento", [38, 24, 31, 92, 123, 132, 343])->exists() : null;
                        $hc_procedimiento_final_msp = Hc_Procedimiento_Final::where("id_hc_procedimientos", $hc_procedimiento->id)->first();
                        $isMayor = Carbon::parse($historia_clinica->agenda->paciente->fecha_nacimiento)->age >= 18;
                        $view = View::make('pdf_hospital.formulario018A', compact('anestesiologiamsp', 'hc_procedimiento_final_msp', 'isMayor', 'hc_procedimiento_final', 'cie10_eg', 'aud_protocolo', 'aud_hc_procedimiento', 'hc_procedimiento', 'historia_clinica', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'sistemas', 'aparatos', 'manejos', 'intubuaciones', 'tipos', 'rc_tecnicas', 'inducciones', 'mantenimientos', 'pre_anestesico', 'agrupados1', 'agrupados2'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_018A.pdf');
                    } else {
                        return 'No se encontro pre_anestesico';
                    }
                } else {
                    return 'No se encontro record anestesico';
                }
            } else {
                return 'No se encontro procedimiento';
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_pre_anestesiologo_horizontal($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if (!is_null($hc_procedimiento)) {
                $id_empresa = $archivo_plano->id_empresa;
                $empresa    = Empresa::find($id_empresa);
                $record = Hc_Anestesiologia::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                if (!is_null($record)) {
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (is_null($pre_anestesico)) {
                        $arr_riesgo_preanestesico = [
                            'id_hc_anestesiologia' => $record->id,
                            'estado'               => 1,
                            'id_usuariocrea'       => '9999966654',
                            'id_usuariomod'        => '9999966654',
                            'ip_creacion'          => '1',
                            'ip_modificacion'      => '1',
                        ];
                        $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                        $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                    }
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (!is_null($pre_anestesico)) {
                        $this->actualiza_informacion($pre_anestesico->id);
                        $historia_clinica = Historiaclinica::where('hcid', $record->id_hc)->first();
                        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();
                        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                        $userInstrumentista = User::where('id', '0959181454')->first();
                        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();
                        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
                        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
                        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
                        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
                        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
                        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
                        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();
                        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $record->id)->get();
                        $agrupados = array();
                        foreach ($examen as $exam) {
                            if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
                            }
                        }
                        $agrupados1 = $agrupados;
                        $agrupados2 = [];
                        $totalAgrupadores = count($agrupados);
                        if ($totalAgrupadores > 4) {
                            $tamanoParte = ceil($totalAgrupadores / 2);
                            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true);
                            $agrupados1 = $agrupadosDivididos[0];
                            $agrupados2 = $agrupadosDivididos[1];
                        }
                        $hc_procedimiento_final = $historia_clinica->id_seguro == 5 ?  Hc_Procedimiento_Final::where("id_hc_procedimientos", $record->id_hc_procedimientos)->whereIn("id_procedimiento", [38, 24, 31, 92, 123, 132, 343])->exists() : null;

                        $hc_procedimiento_final_msp = Hc_Procedimiento_Final::where("id_hc_procedimientos", $hc_procedimiento->id)->whereIn("id_procedimiento", [38, 24, 31, 92, 123, 132, 343])->exists();

                        $isMayor = Carbon::parse($historia_clinica->agenda->paciente->fecha_nacimiento)->age >= 18;
                        $view = View::make('pdf_hospital.formulario018A_horizontal', compact('hc_procedimiento_final_msp', 'userInstrumentista', 'isMayor', 'hc_procedimiento_final', 'cie10_eg', 'aud_protocolo', 'aud_hc_procedimiento', 'hc_procedimiento', 'historia_clinica', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'sistemas', 'aparatos', 'manejos', 'intubuaciones', 'tipos', 'rc_tecnicas', 'inducciones', 'mantenimientos', 'pre_anestesico', 'agrupados1', 'agrupados2'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_018A.pdf');
                    } else {
                        return 'No se encontro pre_anestesico';
                    }
                } else {
                    return 'No se encontro record anestesico';
                }
            } else {
                return 'No se encontro procedimiento';
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_pre_anestesiologo_vertical($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
            if (!is_null($hc_procedimiento)) {
                $id_empresa = $archivo_plano->id_empresa;
                $empresa    = Empresa::find($id_empresa);
                $record = Hc_Anestesiologia::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                $anestesiologiamsp = User::where('id', 1759345422)->first();

                if (!is_null($record)) {
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (is_null($pre_anestesico)) {
                        $arr_riesgo_preanestesico = [
                            'id_hc_anestesiologia' => $record->id,
                            'estado'               => 1,
                            'id_usuariocrea'       => '9999966654',
                            'id_usuariomod'        => '9999966654',
                            'ip_creacion'          => '1',
                            'ip_modificacion'      => '1',
                        ];
                        $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                        $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                    }
                    $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $record->id)->first();
                    if (!is_null($pre_anestesico)) {
                        $this->actualiza_informacion($pre_anestesico->id);
                        $historia_clinica = Historiaclinica::where('hcid', $record->id_hc)->first();
                        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();
                        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();
                        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();
                        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
                        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
                        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
                        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
                        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
                        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
                        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();
                        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $record->id)->get();
                        $agrupados = array();
                        foreach ($examen as $exam) {
                            if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
                            }
                        }
                        $agrupados1 = $agrupados;
                        $agrupados2 = [];
                        $totalAgrupadores = count($agrupados);
                        if ($totalAgrupadores > 4) {
                            $tamanoParte = ceil($totalAgrupadores / 2);
                            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true);
                            $agrupados1 = $agrupadosDivididos[0];
                            $agrupados2 = $agrupadosDivididos[1];
                        }
                        $firmamsp = null;
                        if (!is_null($anestesiologiamsp->id)) {
                            $firmamsp = Firma_Usuario::where('id_usuario', $anestesiologiamsp->id)->first();
                        }

                        $hc_procedimiento_final = $historia_clinica->id_seguro == 5 ?  Hc_Procedimiento_Final::where("id_hc_procedimientos", $record->id_hc_procedimientos)->whereIn("id_procedimiento", [38, 24, 31, 92, 123, 132, 343])->exists() : null;
                        $isMayor = Carbon::parse($historia_clinica->agenda->paciente->fecha_nacimiento)->age >= 18;
                        $view = View::make('pdf_hospital.formulario018A_vertical', compact('firmamsp', 'anestesiologiamsp', 'isMayor', 'hc_procedimiento_final', 'cie10_eg', 'aud_protocolo', 'aud_hc_procedimiento', 'hc_procedimiento', 'historia_clinica', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'sistemas', 'aparatos', 'manejos', 'intubuaciones', 'tipos', 'rc_tecnicas', 'inducciones', 'mantenimientos', 'pre_anestesico', 'agrupados1', 'agrupados2'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_018A.pdf');
                    } else {
                        return 'No se encontro pre_anestesico';
                    }
                } else {
                    return 'No se encontro record anestesico';
                }
            } else {
                return 'No se encontro procedimiento';
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_pre_anestesico_018($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $historiaClinica = Historiaclinica::find($archivo_plano->id_hc);
            $hc_anestesiologia = Hc_Anestesiologia::where('id_hc', $archivo_plano->id_hc)->where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
            if (!is_null($hc_anestesiologia)) {
                $hc_procedimiento = hc_procedimientos::find($hc_anestesiologia->id_hc_procedimientos);
                $agenda = Agenda::where('id', $hc_procedimiento->historia->agenda->id)->first();
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                $empresa    = Empresa::where('id', $archivo_plano->id_empresa)->where('estado', '1')->first();
                $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $hc_anestesiologia->id)->first();
                $anestesiologiamsp = User::where('id', 1759345422)->first();
                //dd($anestesiologiamsp);
                if (is_null($pre_anestesico)) {
                    $arr_riesgo_preanestesico = [
                        'id_hc_anestesiologia' => $hc_anestesiologia->id,
                        'estado'               => 1,
                        'id_usuariocrea'       => '9999966654',
                        'id_usuariomod'        => '9999966654',
                        'ip_creacion'          => '1',
                        'ip_modificacion'      => '1',
                    ];
                    $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                    $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                }

                if (!is_null($pre_anestesico)) {
                    $this->actualiza_informacion($pre_anestesico->id);
                }

                $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $pre_anestesico->id)->where('estado', 1)->get();
                $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $pre_anestesico->id)->where('estado', 1)->get();
                $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $pre_anestesico->id)->where('estado', 1)->get();
                $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_anestesiologia->id_hc_procedimientos)->get();
                $mas = true;
                $texto = "";
                foreach ($adicionales as $value2) {
                    if ($mas == true) {
                        $texto = $texto . $value2->procedimiento->nombre;
                        $mas = false;
                    } else {
                        $texto = $texto . ' + ' . $value2->procedimiento->nombre;
                    }
                }

                $agrupados = array();
                foreach ($rc_examen as $exam) {
                    if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                        $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
                    }
                }
                $agrupados1 = $agrupados;
                $agrupados2 = [];
                $totalAgrupadores = count($agrupados);
                if ($totalAgrupadores > 4) {
                    $tamanoParte = ceil($totalAgrupadores / 2);
                    $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true);
                    $agrupados1 = $agrupadosDivididos[0];
                    $agrupados2 = $agrupadosDivididos[1];
                }
                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
                $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();
                $seguro = $archivo_plano->seguro;
                $view = View::make('pdf_hospital.formulario018', compact('seguro','anestesiologiamsp', 'agenda', 'aud_protocolo', 'cie10', 'hc_procedimiento', 'pre_anestesico', 'rc_antecedentes', 'rc_examen', 'indicaciones', 'empresa', 'texto', 'agrupados1', 'agrupados2'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_018.pdf');
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_post_anestesico($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $hc_procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
            if (!is_null($hc_procedimiento)) {
                $historia = Historiaclinica::find($hc_procedimiento->id_hc);
                if (!is_null($historia)) {
                    $paciente = Paciente::find($historia->id_paciente);
                    $agenda = Agenda::find($historia->id_agenda);
                    $especialidad = Especialidad::find($agenda->espid);
                    $empresa = Empresa::find($agenda->id_empresa);
                    $doctor = User::find($agenda->id_doctor1);
                    $cirugia = '';
                    $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->get();
                    $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->first();
                    $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $aud_hc_procedimiento->id_procedimientos_org)->first();
                    $anestesiologia = Hc_Anestesiologia::where('id_hc', $archivo_plano->id_hc)->where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
                    $anestesiologiamsp = User::where('id', 1759345422)->first();
                    if (!is_null($anestesiologia)) {
                        $hc_procedimiento = hc_procedimientos::find($anestesiologia->id_hc_procedimientos);
                        $empresa    = Empresa::where('id', $archivo_plano->id_empresa)->where('estado', '1')->first();
                        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($pre_anestesico)) {
                            $arr_riesgo_preanestesico = [
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado'               => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];
                            $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                            $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                        }

                        if (!is_null($pre_anestesico)) {
                            $this->actualiza_informacion($pre_anestesico->id);
                        }


                        $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::Where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($evolucion_anestesiologo)) {

                            $arr_postanestesico = [
                                'id_hc' => $anestesiologia->id_hc,
                                'hc_id_procedimiento' => $anestesiologia->id_hc_procedimientos,
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado' => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];

                            $id_rc = Hc_Evolucion_Anestesiologo::insertGetId($arr_postanestesico);
                            $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::find($id_rc);
                        }


                        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anestesiologia->historia->hcid)->limit(2)->get();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->first();
                        if (count($hc_procedimiento_final) > 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                            for ($i = 1; $i < count($hc_procedimiento_final); $i++) {
                                $cirugia = $cirugia . ' + ' . $hc_procedimiento_final[$i]->procedimiento->nombre;
                            }
                        } elseif (count($hc_procedimiento_final) == 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                        }
                        $diagnostico = '';
                        $firma = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma = Firma_Usuario::where('id_usuario', $anestesiologia->id_anestesiologo)->first();
                        }
                        $firmamsp = null;
                        if (!is_null($anestesiologiamsp->id)) {
                            $firmamsp = Firma_Usuario::where('id_usuario', $anestesiologiamsp->id)->first();
                        }
                        $firma_doctor = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma_doctor = Firma_Usuario::where('id_usuario', $protocolo->historiaclinica->id_doctor1)->first();
                        }
                        $view = View::make('pdf_hospital.formulario019', compact('firmamsp', 'anestesiologiamsp', 'protocolo', 'firma_doctor', 'evolucion_anestesiologo', 'agenda', 'historia', 'paciente', 'especialidad', 'empresa', 'doctor', 'cirugia', 'diagnostico', 'anestesiologia', 'cie10', 'firma', 'cie10_eg', 'aud_hc_procedimiento'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setOptions(['chroot'  => base_path('/')]);
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_019.pdf');
                    } else {
                        return 'No se encontro anestesiologia';
                    }
                } else {
                    return 'No se encontro la historia clinica';
                }
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_post_anestesico_horizontal($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $hc_procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
            if (!is_null($hc_procedimiento)) {
                $historia = Historiaclinica::find($hc_procedimiento->id_hc);
                if (!is_null($historia)) {
                    $paciente = Paciente::find($historia->id_paciente);
                    $agenda = Agenda::find($historia->id_agenda);
                    $especialidad = Especialidad::find($agenda->espid);
                    $empresa = Empresa::find($agenda->id_empresa);
                    $doctor = User::find($agenda->id_doctor1);
                    $cirugia = '';
                    $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->get();
                    $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->first();
                    $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $aud_hc_procedimiento->id_procedimientos_org)->first();
                    $anestesiologia = Hc_Anestesiologia::where('id_hc', $archivo_plano->id_hc)->where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
                    $anestesiologiamsp = User::where('id', 1759345422)->first();

                    if (!is_null($anestesiologia)) {
                        $hc_procedimiento = hc_procedimientos::find($anestesiologia->id_hc_procedimientos);
                        $empresa    = Empresa::where('id', $archivo_plano->id_empresa)->where('estado', '1')->first();
                        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($pre_anestesico)) {
                            $arr_riesgo_preanestesico = [
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado'               => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];
                            $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                            $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                        }

                        if (!is_null($pre_anestesico)) {
                            $this->actualiza_informacion($pre_anestesico->id);
                        }


                        $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::Where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($evolucion_anestesiologo)) {

                            $arr_postanestesico = [
                                'id_hc' => $anestesiologia->id_hc,
                                'hc_id_procedimiento' => $anestesiologia->id_hc_procedimientos,
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado' => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];

                            $id_rc = Hc_Evolucion_Anestesiologo::insertGetId($arr_postanestesico);
                            $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::find($id_rc);
                        }


                        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anestesiologia->historia->hcid)->limit(2)->get();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->first();
                        if (count($hc_procedimiento_final) > 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                            for ($i = 1; $i < count($hc_procedimiento_final); $i++) {
                                $cirugia = $cirugia . ' + ' . $hc_procedimiento_final[$i]->procedimiento->nombre;
                            }
                        } elseif (count($hc_procedimiento_final) == 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                        }
                        $diagnostico = '';
                        $firma = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma = Firma_Usuario::where('id_usuario', $anestesiologia->id_anestesiologo)->first();
                        }
                        $firmamsp = null;
                        if (!is_null($anestesiologiamsp->id)) {
                            $firmamsp = Firma_Usuario::where('id_usuario', $anestesiologiamsp->id)->first();
                        }
                        $firma_doctor = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma_doctor = Firma_Usuario::where('id_usuario', $protocolo->historiaclinica->id_doctor1)->first();
                        }
                        $view = View::make('pdf_hospital.formulario019_horizontal', compact('firmamsp', 'anestesiologiamsp', 'protocolo', 'firma_doctor', 'evolucion_anestesiologo', 'agenda', 'historia', 'paciente', 'especialidad', 'empresa', 'doctor', 'cirugia', 'diagnostico', 'anestesiologia', 'cie10', 'firma', 'cie10_eg', 'aud_hc_procedimiento'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setOptions(['chroot'  => base_path('/')]);
                        $pdf->setPaper('A4', 'landscape');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_019.pdf');
                    } else {
                        return 'No se encontro anestesiologia';
                    }
                } else {
                    return 'No se encontro la historia clinica';
                }
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function descargar_post_anestesico_vertical($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $hc_procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
            if (!is_null($hc_procedimiento)) {
                $historia = Historiaclinica::find($hc_procedimiento->id_hc);
                if (!is_null($historia)) {
                    $paciente = Paciente::find($historia->id_paciente);
                    $agenda = Agenda::find($historia->id_agenda);
                    $especialidad = Especialidad::find($agenda->espid);
                    $empresa = Empresa::find($agenda->id_empresa);
                    $doctor = User::find($agenda->id_doctor1);
                    $cirugia = '';
                    $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->get();
                    $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->first();
                    $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $aud_hc_procedimiento->id_procedimientos_org)->first();
                    $anestesiologia = Hc_Anestesiologia::where('id_hc', $archivo_plano->id_hc)->where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
                    $anestesiologiamsp = User::where('id', 1759345422)->first();
                    if (!is_null($anestesiologia)) {
                        $hc_procedimiento = hc_procedimientos::find($anestesiologia->id_hc_procedimientos);
                        $empresa    = Empresa::where('id', $archivo_plano->id_empresa)->where('estado', '1')->first();
                        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($pre_anestesico)) {
                            $arr_riesgo_preanestesico = [
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado'               => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];
                            $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
                            $pre_anestesico = Rc_pre_anestesica::find($id_rc);
                        }

                        if (!is_null($pre_anestesico)) {
                            $this->actualiza_informacion($pre_anestesico->id);
                        }


                        $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::Where('id_hc_anestesiologia', $anestesiologia->id)->first();
                        if (is_null($evolucion_anestesiologo)) {

                            $arr_postanestesico = [
                                'id_hc' => $anestesiologia->id_hc,
                                'hc_id_procedimiento' => $anestesiologia->id_hc_procedimientos,
                                'id_hc_anestesiologia' => $anestesiologia->id,
                                'estado' => 1,
                                'id_usuariocrea'       => '9999966654',
                                'id_usuariomod'        => '9999966654',
                                'ip_creacion'          => '1',
                                'ip_modificacion'      => '1',
                            ];

                            $id_rc = Hc_Evolucion_Anestesiologo::insertGetId($arr_postanestesico);
                            $evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::find($id_rc);
                        }


                        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $anestesiologia->historia->hcid)->limit(2)->get();
                        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->first();
                        if (count($hc_procedimiento_final) > 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                            for ($i = 1; $i < count($hc_procedimiento_final); $i++) {
                                $cirugia = $cirugia . ' + ' . $hc_procedimiento_final[$i]->procedimiento->nombre;
                            }
                        } elseif (count($hc_procedimiento_final) == 1) {
                            $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                        }
                        $diagnostico = '';
                        $firma = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma = Firma_Usuario::where('id_usuario', $anestesiologia->id_anestesiologo)->first();
                        }
                        $firmamsp = null;
                        if (!is_null($anestesiologiamsp->id)) {
                            $firmamsp = Firma_Usuario::where('id_usuario', $anestesiologiamsp->id)->first();
                        }
                        $firma_doctor = null;
                        if (!is_null($anestesiologia->id_anestesiologo)) {
                            $firma_doctor = Firma_Usuario::where('id_usuario', $protocolo->historiaclinica->id_doctor1)->first();
                        }
                        $view = View::make('pdf_hospital.formulario019_vertical', compact('firmamsp', 'anestesiologiamsp', 'protocolo', 'firma_doctor', 'evolucion_anestesiologo', 'agenda', 'historia', 'paciente', 'especialidad', 'empresa', 'doctor', 'cirugia', 'diagnostico', 'anestesiologia', 'cie10', 'firma', 'cie10_eg', 'aud_hc_procedimiento'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setOptions(['chroot'  => base_path('/')]);
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_019.pdf');
                    } else {
                        return 'No se encontro anestesiologia';
                    }
                } else {
                    return 'No se encontro la historia clinica';
                }
            }
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function actualiza_informacion($id)
    {

        $pre_anestesico = Rc_pre_anestesica::find($id); //dd($id, $pre_anestesico);
        $csv_record = hc_anestesiologia_csv::where('id_hc_anestesiologia', $pre_anestesico->id_hc_anestesiologia)->first();
        if (!is_null($pre_anestesico)) {
            $cont = 0;
            $anestesiologia = $pre_anestesico->anestesiologia;
            $hc = $anestesiologia->historia;
            $paciente = $hc->paciente;
            //$post_anestesico = Hc_Evolucion_Anestesiologo::where('id_hc_anestesiologia',$anestesiologia->id)->where('estado','1')->first();

            if ($pre_anestesico->quirurgicos == null) {
                $cont++;
                $pre_anestesico->quirurgicos = $paciente->antecedentes_quir;
            }
            if ($pre_anestesico->alergicos == null) {
                $cont++;
                $alergiasxpac = $paciente->a_alergias;
                $txt_aler = '';
                foreach ($alergiasxpac as $ale_pac) {
                    $txt_aler .= ' + ' . $ale_pac->principio_activo->nombre;
                }
                $pre_anestesico->alergicos = $txt_aler;
            }
            if ($pre_anestesico->habitos == null) {
                $cont++;
                $pre_anestesico->habitos = $paciente->alcohol;
            }
            if ($pre_anestesico->lactato_ringer == null) {
                $cont++;
                $pre_anestesico->lactato_ringer = $anestesiologia->lactato_ringer;
            }
            if ($pre_anestesico->expansores == null) {
                $cont++;
                $pre_anestesico->expansores = $anestesiologia->expansores;
            }
            if ($pre_anestesico->sangre == null) {
                $cont++;
                $pre_anestesico->sangre = $anestesiologia->sangre_derivados;
            }
            if ($pre_anestesico->observaciones == null) {
                $cont++;
                $pre_anestesico->observaciones = $anestesiologia->observaciones;
            }
            if (!is_null($csv_record)) {
                if ($pre_anestesico->presion == null) {
                    $cont++;
                    //$pre_anestesico->presion = $hc->presion;
                    $pre_anestesico->presion = $csv_record->presion_arterial;
                }
                if ($pre_anestesico->pulso == null) {
                    $cont++;
                    //$pre_anestesico->pulso = $hc->pulso;
                    $pre_anestesico->pulso = $csv_record->pulso;
                }
                if ($pre_anestesico->o2 == null) {
                    $cont++;
                    //$pre_anestesico->o2 = $hc->o2;
                    $pre_anestesico->o2 = $csv_record->o2;
                }
                if ($pre_anestesico->temperatura == null) {
                    $cont++;
                    //$pre_anestesico->temperatura = $hc->temperatura;
                    $pre_anestesico->temperatura = $csv_record->temperatura;
                }
                if ($pre_anestesico->fecuencia_respiratoria == null) {
                    $cont++;
                    $pre_anestesico->fecuencia_respiratoria = $csv_record->respiracion;
                }
            }
            if ($pre_anestesico->sitio_1 == null) {
                $texto1 = "Miembro superior izquierdo";
                $texto2 = "Miembro superior derecho";
                $txt_rnd = '';
                $indiceAleatorio = random_int(0, 1);
                if ($indiceAleatorio === 0) {
                    $txt_rnd = $texto1;
                } else {
                    $txt_rnd = $texto2;
                }
                $pre_anestesico->sitio_1 = $txt_rnd;
            }
            if ($pre_anestesico->calibre_1 == null) {
                $pre_anestesico->calibre_1 = '20';
            }
            if ($pre_anestesico->observaciones == null) {
                $pre_anestesico->observaciones = 'PACIENTE PASA A SALA DE RECUPERACIÓN EN CONDICIONES ESTABLES';
            }
            if ($cont > 0) {
                $pre_anestesico->save();
            }
            for ($i = 1; $i <= 11; $i++) {
                $property = 'd' . $i;
                if (!is_null($anestesiologia->$property)) {
                    $droga = $anestesiologia->$property;
                    $droga_ad = Drogas_administradas::where('id_hc_anestesiologia', $anestesiologia->id)
                        ->where('drogas', $droga)
                        ->where('estado', '1')
                        ->first();
                    if (is_null($droga_ad)) {
                        $arr_dog_ad = [
                            'id_hc_anestesiologia' => $anestesiologia->id,
                            'estado'               => '1',
                            'drogas'               => $droga,
                            'id_usuariocrea'       => $anestesiologia->id_usuariocrea,
                            'id_usuariomod'        => $anestesiologia->id_usuariomod,
                            'ip_creacion'          => $anestesiologia->ip_creacion,
                            'ip_modificacion'      => $anestesiologia->ip_modificacion,
                        ];
                        Drogas_administradas::create($arr_dog_ad);
                    }
                }
            }
            $id_paciente = $hc->id_paciente;
            $fecha_orden = $anestesiologia->fecha;
            $orden = $this->getLastOrder($id_paciente, $fecha_orden);
            if (!is_null($orden)) {
                $resultados = $orden->resultados;
                if (count($resultados) > 0) {

                    foreach ($resultados as $resul) {
                        $examen = $resul->examen;
                        if (!is_null($examen)) {
                            $id_agrupador = $examen->id_agrupador;
                            if (!is_null($resul->valor)) {
                                if ($resul->certificado == '1') {
                                    if (!is_null($examen)) {
                                        $rc_resultado = Rc_resultados::where('id_rc_pre_anestesia', $pre_anestesico->id)->where('id_parametro', $resul->id_parametro)->where('id_orden', $resul->id_orden)->first();
                                        if (is_null($rc_resultado)) {
                                            if (!is_null($resul->id_agrupador_publico)) {
                                                $id_agrupador = $resul->id_agrupador_publico;
                                            }
                                            $arr_crea_ex_rc = [
                                                'id_examen'                 => $examen->id,
                                                'id_rc_pre_anestesia'       => $pre_anestesico->id,
                                                'id_hc_anestesiologia'      => $anestesiologia->id,
                                                'nombre'                    => $examen->nombre,
                                                'resultado'                 => $resul->valor,
                                                'estado'                    => '1',
                                                'id_parametro'              => $resul->id_parametro,
                                                'id_orden'                  => $resul->id_orden,
                                                'id_usuariocrea'            => $resul->id_usuariocrea,
                                                'id_usuariomod'             => $resul->id_usuariomod,
                                                'ip_creacion'               => $resul->ip_creacion,
                                                'ip_modificacion'           => $resul->ip_modificacion,
                                                'id_agrupador_publico'      => $id_agrupador,

                                            ];
                                            Rc_resultados::create($arr_crea_ex_rc);
                                        } else {
                                            $arr_upd = [
                                                'id_agrupador_publico'       => $id_agrupador,
                                            ];
                                            $rc_resultado->update($arr_upd);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function getLastOrder($id_paciente, $fecha_orden)
    {
        $fecha_orden = Carbon::parse($fecha_orden);
        $order = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('estado', '1')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, created_at, ?)) DESC', [$fecha_orden])
            ->first();
        return $order;
    }
    //histopatologia (013)
    public function descargar_histopatologiaA($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
            $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $procedimiento->id)->first();
            $epicrisis_original = Hc_Epicrisis::find($epicrisis->id_epricrisis_org);
            $formulario013 = HcFormulario013::where('id_hc_procedimiento', $procedimiento->id)->first();
            if (!is_null($formulario013)) {
                $historia = Historiaclinica::find($procedimiento->id_hc);
                if (!is_null($historia)) {
                    $agenda = Agenda::find($historia->id_agenda);
                    if (!is_null($agenda)) {
                        $especialidad = Especialidad::find($agenda->espid);
                        $paciente = Paciente::find($formulario013->id_paciente);
                        $empresa = Empresa::find($formulario013->id_empresa);
                        $seguro = Seguro::find($paciente->id_seguro);
                        $seguro_nombre = '';

                        if (!is_null($seguro)) {
                            $seguro_nombre = strtoupper($seguro->nombre);
                        }

                        $edad = null;
                        $fechaActual = new DateTime();
                        if (!is_null($paciente)) {
                            $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
                            $edad = $fechaNacimiento->diff($fechaActual);
                            $age = $edad->y;
                        }

                        $diagnosticos = Aud_Hc_Cie10::where('hc_id_procedimiento', $formulario013->id_hc_procedimiento)->limit(5)->get();

                        foreach ($diagnosticos as $value_descripcion) {
                            $cie10 = Cie_10_3::where('id', $value_descripcion->cie10)->first();
                        }

                        $biopsias = Hc4_Auditoria_Biopsias::where('id_hc_formulario_013', $formulario013->id)->where('estado', 1)->limit(5)->get();
                        $descripcion_frascos = $biopsias->pluck('descripcion_frasco')
                            ->filter()
                            ->unique()
                            ->implode(', ');

                        $fecha_toma = date_format(new DateTime($formulario013->fecha_toma), 'd/m/Y');
                        $hora_toma = date_format(new DateTime($formulario013->fecha_toma), 'H:i:s');
                        $fecha = date_format(new DateTime($procedimiento->created_at), 'Y-m-d');
                        $hora = date_format(new DateTime($procedimiento->created_at), 'H:i:s');
                        $doctor = User::find($procedimiento->id_doctor_examinador);
                        $firma = null;
                        $ruta_firma = "";
                        if (!is_null($doctor)) {
                            $firma = Firma_Usuario::where('id_usuario', $doctor->id)->first();
                            if (!is_null($firma)) {
                                $ruta_firma = base_path() . '/storage/app/avatars/' . $firma->firma;
                            }
                        }
                        $view = View::make('pdf_hospital.formulario13A', compact('epicrisis', 'epicrisis_original', 'descripcion_frascos', 'seguro', 'age', 'diagnosticos', 'fecha_toma', 'hora_toma', 'value_descripcion', 'cie10', 'formulario013', 'paciente', 'empresa', 'seguro_nombre', 'especialidad', 'biopsias', 'procedimiento', 'fecha', 'hora', 'doctor', 'ruta_firma'))->render();
                        $pdf  = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        return $pdf->stream('HCU_013_A.pdf');
                    } else {
                        return 'No se encontro agenda';
                    }
                } else {
                    return 'No se encontro historia clinica';
                }
            } else {
                return 'No se encontro formulario 013';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_histopatologiaB($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_empresa = $plano_cabecera->id_empresa;
            $empresa    = Empresa::find($id_empresa);
            $hc_procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
            $fecha_operacion = $hc_procedimiento->aud_hc_protocolo->fecha_operacion ?? null;
            if (!is_null($hc_procedimiento)) {
                $biopsias =  Hc_informe_biopsia::where('id_hc_procedimiento', $hc_procedimiento->id)->get();

                if (count($biopsias) > 0) {
                    $view = View::make('pdf_hospital.formulario13B', compact('biopsias', 'fecha_operacion'))->render();
                    $pdf  = App::make('dompdf.wrapper');
                    $pdf->setPaper('A4');
                    //$pdf->setPaper('A4', 'landscape');
                    $pdf->loadHTML($view);
                    return $pdf->stream('formulario13B.pdf');
                } else {
                    return 'No se encontraron biopsias';
                }
            } else {
                return 'No se encontro procedimiento';
            }
        } else {
            return 'No se encontro registro';
        }
    }
    public function descargar_estudio($id_plano)
    {
        $tipo = '1';
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
                if (!is_null($aud_protocolo)) {
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if (!is_null($protocolo)) {
                        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('seleccionado', '1')->where('estado', '1')->orderBy('id', 'desc')->get();
                        $paciente = paciente::find($aud_protocolo->historiaclinica->id_paciente);
                        $seguro   = Seguro::find($aud_protocolo->auditoria_procedimiento->id_seguro);
                        if (!is_null($aud_protocolo->auditoria_procedimiento->id_doctor_examinador2)) {
                            $firma = Firma_Usuario::where('id_usuario', $aud_protocolo->auditoria_procedimiento->doctor_firma->id)->get();
                            if (!is_null($seguro)) {
                                if ($seguro->tipo == '0') {
                                    if ($aud_protocolo->auditoria_procedimiento->id_empresa == '0992704152001') {
                                        if ($aud_protocolo->auditoria_procedimiento->id_doctor_examinador2 == '0924611882') {
                                            $firma = Firma_Usuario::where('id_usuario', '094346835')->get();
                                        }
                                    }
                                }
                            }
                        } else {
                            $firma = null;
                        }
                        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                        $procedimiento_completo = procedimiento_completo::find($aud_protocolo->auditoria_procedimiento->id_procedimiento_completo);
                        $historia               = Historiaclinica::find($aud_protocolo->hcid);
                        $elasto = Aud_Hc_Procedimiento_Final::where('id_hc_procedimientos', $aud_protocolo->auditoria_procedimiento->id_procedimientos_org)->where('id_procedimiento', '26')->first();
                        if (!is_null($elasto)) {
                            $view = View::make('auditoria_hc_admision.formato.resumen_elasto', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'elasto'))->render();
                        } else {
                            if ($tipo == 0) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 1) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 2) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_2_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 3) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_3_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 4) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_4_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 5) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_5_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 6) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 7) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_7_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 8) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_8_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 9) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_9_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            } elseif ($tipo == 10) {
                                $view = View::make('auditoria_hc_admision.formato.resumen_10_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
                            }
                        }
                        $pdf = App::make('dompdf.wrapper');
                        $pdf->setPaper('A4');
                        $pdf->loadHTML($view);
                        $agenda = $historia->agenda;
                        $txt_fecha = substr($agenda->fechaini, 8, 2) . '_' . substr($agenda->fechaini, 5, 2) . '_' . substr($agenda->fechaini, 0, 4);
                        return $pdf->stream('C_RESULTADOS.pdf');
                    } else {
                        return 'No se encontrolo el protocolo';
                    }
                } else {
                    return 'No se encontro el protocolo de auditoria';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_estudio_robot($id_plano)
    {
        try {
            $plano = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano)) {
                $id_procedimiento = $plano->id_hc_procedimimentos;
                $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
                if (!is_null($aud_protocolo)) {
                    $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                    if (!is_null($protocolo)) {
                        $historia_actual = Historiaclinica::find($plano->id_hc);
                        $historia = $historia_actual;
                        $agenda_actual   = $historia_actual ? Agenda::find($historia_actual->id_agenda) : null;
                        $aud_necesita = (
                            is_null($aud_protocolo->motivo) &&
                            is_null($aud_protocolo->conclusion) &&
                            is_null($aud_protocolo->hallazgos)
                        );
                        $protocolo_fallback = null;
                        if ($aud_necesita) {
                            $necesita_fallback = (
                                (is_null($aud_protocolo->motivo)     && is_null($protocolo->motivo)) ||
                                (is_null($aud_protocolo->conclusion) && is_null($protocolo->conclusion)) ||
                                (is_null($aud_protocolo->hallazgos)  && is_null($protocolo->hallazgos))
                            );
                            if ($necesita_fallback && $agenda_actual) {
                                $inicio = Carbon::parse($agenda_actual->fechaini)->startOfDay();
                                $fin    = Carbon::parse($agenda_actual->fechaini)->endOfDay();
                                $agenda_alt = Agenda::where('id_paciente', $plano->id_paciente)
                                    ->where('id_seguro',   $plano->id_seguro)
                                    ->where('id_empresa',  $plano->id_empresa)
                                    ->whereBetween('fechaini', [$inicio, $fin])
                                    ->where('id', '<>', $agenda_actual->id)
                                    ->first();

                                if (!is_null($agenda_alt)) {
                                    $historia_alt = Historiaclinica::where('id_agenda', $agenda_alt->id)->first();
                                    $historia = $historia_alt;
                                    if (!is_null($historia_alt)) {
                                        $protocolo_fallback = hc_protocolo::where('hcid', $historia_alt->hcid)->first();
                                    }
                                }
                            }

                            if (is_null($aud_protocolo->motivo)) {
                                if (!is_null($protocolo->motivo)) {
                                    $aud_protocolo->motivo = $protocolo->motivo;
                                } elseif (!is_null($protocolo_fallback) && !is_null($protocolo_fallback->motivo)) {
                                    $aud_protocolo->motivo = $protocolo_fallback->motivo;
                                }
                            }

                            if (is_null($aud_protocolo->conclusion)) {
                                if (!is_null($protocolo->conclusion)) {
                                    $aud_protocolo->conclusion = $protocolo->conclusion;
                                } elseif (!is_null($protocolo_fallback) && !is_null($protocolo_fallback->conclusion)) {
                                    $aud_protocolo->conclusion = $protocolo_fallback->conclusion;
                                }
                            }

                            if (is_null($aud_protocolo->hallazgos)) {
                                if (!is_null($protocolo->hallazgos)) {
                                    $aud_protocolo->hallazgos = $protocolo->hallazgos;
                                } elseif (!is_null($protocolo_fallback) && !is_null($protocolo_fallback->hallazgos)) {
                                    $aud_protocolo->hallazgos = $protocolo_fallback->hallazgos;
                                }
                            }

                            if ($necesita_fallback && !is_null($protocolo_fallback)) {
                                $protocolo = $protocolo_fallback;
                            }
                        }
                        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->orderBy('id', 'desc')->get();
                        if (count($imagenes) == 0) {
                            if (!is_null($protocolo_fallback)) {
                                $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo_fallback->id)->where('estado', '1')->orderBy('id', 'desc')->get();
                            }
                        }
                        $paciente = paciente::find($plano->id_paciente);
                        if (!is_null($paciente)) {
                            if (!is_null($aud_protocolo->auditoria_procedimiento)) {
                                $seguro   = Seguro::find($aud_protocolo->auditoria_procedimiento->id_seguro);
                                $firma = null;
                                $doctor_firma = null;
                                if (!is_null($aud_protocolo->auditoria_procedimiento->doctor_firma)) {
                                    $doctor_firma = $aud_protocolo->auditoria_procedimiento->doctor_firma ?? null;

                                    $verificar = Archivo_Plano_Detalle::where('id_ap_cabecera', $id_plano)
                                        ->whereIn('codigo', ['76700'])
                                        ->where('estado', 1)
                                        ->first();

                                    if (!is_null($verificar)) {
                                        $doctor_firma = User::find('0961491354');
                                    }

                                    if (!is_null($doctor_firma)) {
                                        $firma = Firma_Usuario::where('id_usuario', $doctor_firma->id)->first();
                                    }
                                }
                                $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                                $procedimiento_completo = procedimiento_completo::find($aud_protocolo->auditoria_procedimiento->id_procedimiento_completo);
                                $elasto = Aud_Hc_Procedimiento_Final::where('id_hc_procedimientos', $aud_protocolo->auditoria_procedimiento->id_procedimientos_org)->where('id_procedimiento', '26')->first();
                                $view = View::make('pdf_hospital.estudio_robot_auditoria', compact('aud_protocolo', 'protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'doctor_firma'))->render();
                                $pdf = App::make('dompdf.wrapper');
                                $pdf->setPaper('A4');
                                $pdf->loadHTML($view);
                                return $pdf->stream('C_RESULTADOS.pdf');
                            } else {
                                'No se encontro el protocolo de auditoria';
                            }
                        } else {
                            'No se encontro el paciente';
                        }
                    } else {
                        'No se encontro el protocolo';
                    }
                } else {
                    'No se encontro el procedimiento';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_bitacora($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $this->recalcular_valor_ap_cab($plano_cabecera->id);
                $archivo_plano = Archivo_Plano_Cabecera::find($plano_cabecera->id);
                $empresa = Empresa::find($archivo_plano->id_empresa);
                $procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
                $convenio = Convenio::where('id_seguro', $archivo_plano->id_seguro)->where('id_empresa', $archivo_plano->id_empresa)->where('estado', '1')->first();
                $view = View::make('archivo_plano.reportes.bitacora', compact('archivo_plano', 'procedimiento', 'empresa', 'convenio'))->render();
                $pdf  = App::make('dompdf.wrapper');
                // $pdf->setPaper('A4');
                $pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('bitacora.pdf');
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_evolucion($id_plano)
    {

        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $id_procedimiento = $plano_cabecera->id_hc_procedimimentos;
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
            if (!is_null($protocolo)) {
                $empresa    = Empresa::find($plano_cabecera->id_empresa);
                $tipo = 'auditoria';
                $protocolo_original = null;
                $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
                $hc_procedimiento = hc_procedimientos::where('id', $protocolo_original->id_hc_procedimientos)->first();
                $pxs = $hc_procedimiento->hc_procedimiento_f;
                $proc = null;
                $imagen = null;
                foreach ($pxs as $px) {
                    if (!is_null($px->procedimiento->id_grupo_procedimiento)) {
                        $proc = $px->procedimiento;
                    }
                }
                if ($proc != null) {
                    $imagen = Formato_consentimiento::where('id_procedimiento', $proc->id)->first();
                }

                $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();
                if ($protocolo_original->tipo_anestesia == 'GENERAL') {
                    $duracion = '60';
                    $hora_fin = strtotime('+60 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL90') {
                    $duracion = '90';
                    $hora_fin = strtotime('+90 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL120') {
                    $duracion = '120';
                    $hora_fin = strtotime('+120 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL150') {
                    $duracion = '150';
                    $hora_fin = strtotime('+150 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL180') {
                    $duracion = '180';
                    $hora_fin = strtotime('+180 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } elseif ($protocolo_original->tipo_anestesia == 'GENERAL210') {
                    $duracion = '210';
                    $hora_fin = strtotime('+210 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                } else {
                    $duracion = '30';
                    $hora_fin = strtotime('+30 minute', strtotime($protocolo_original->hora_inicio));
                    $hora_fin = date('H:i', $hora_fin);
                }
                $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $protocolo_original->id_hc_procedimientos)->where('ingreso_egreso', 'INGRESO')->get();
                $firma = Firma_Usuario::where('id_usuario', $protocolo_original->procedimiento->id_doctor_ayudante_con)->first();
                $orden_biopsias = Hc4_Biopsias::where('id_paciente', $hc_procedimiento->historia->paciente->id)->get();
                $view = View::make('pdf_hospital.formulario017', compact('hc_procedimiento', 'pentax', 'protocolo', 'cie10', 'firma', 'orden_biopsias', 'imagen', 'empresa', 'protocolo_original'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                //$pdf->setPaper('A4', 'landscape');
                $pdf->loadHTML($view);
                return $pdf->stream('formulario017.pdf');
            }
        }
    }
    //FORMULARIO002
    public function descargar_anamnesis($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $this->crear_anamnesis($archivo_plano->id_hc);
            $anamnesis = Anamnesis::where('hcid', $archivo_plano->id_hc)->first();
            if (!is_null($anamnesis)) {
                $cie10 = Aud_Hc_Cie10::where('aud_hc_cie10.hcid',  $anamnesis->hcid)->get();

                $cie10 = [];
                $code_cie10 = $archivo_plano->cie10;
                $vt_c10 = Cie_10_3::find($code_cie10);
                if (is_null($vt_c10)) {
                    $vt_c10 = Cie_10_4::find($code_cie10);
                }
                $vt_pre_def = $archivo_plano->presuntivo_def;

                $procedimiento = hc_procedimientos::where('id_hc', $anamnesis->hcid)->first();
                $doctor = "";
                if (!is_null($procedimiento)) {
                    if ($procedimiento->id_doctor_responsable == null) {
                        $doctor = User::find($procedimiento->id_doctor_examinador2);
                    } else {
                        $doctor = User::find($procedimiento->id_doctor_responsable);
                    }
                }
                if ($doctor == "") {
                    $doctor = $anamnesis->historiaclinica->doctor_1;
                }
                $view = View::make('pdf_hospital.formulario002', compact('anamnesis', 'cie10', 'doctor', 'vt_c10', 'vt_pre_def'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->setOptions(['chroot'  => base_path('/')]);
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_002.pdf');
            } else {
                return 'No se encontro anamnesis';
            }
        } else {
            return 'No se encontro archivo plano';
        }
    }
    public function crear_anamnesis($hcid)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = '9999966654';
        $anamnesis = Anamnesis::where('hcid', $hcid)->where('estado', '1')->first(); //dd($anamnesis);
        $historiaclinica = Historiaclinica::find($hcid);
        if (is_null($anamnesis)) {
            $motivo_evolucion = '';
            $problema_actual = '';
            $indicacion_evolucion = '';

            if (count($historiaclinica->evoluciones) > 0) {

                $evoluciones = $historiaclinica->evoluciones;
                $evolucion = $evoluciones[0];

                $motivo_evolucion = $evolucion->motivo;
                $problema_actual = $evolucion->cuadro_clinico;
                $indicacion_evolucion = $evolucion->indicacion;
            }

            $arr_anamnesis = [
                'hcid' => $hcid,
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'motivo_evolucion'      => $motivo_evolucion,
                'problema_actual'       => $problema_actual,
                'indicacion_evolucion'  => $indicacion_evolucion,
            ];
            $id_an = Anamnesis::insertGetId($arr_anamnesis);
            $anamnesis = Anamnesis::find($id_an);
        }
        return $anamnesis->id;
    }
    public function descargar_hc_as400($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento', 'HC_AS400')->first();
                    if (!is_null($archivo_historico)) {
                        $name = storage_path('app/') . $archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) {
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        } else {
                            return 'No se encontro el archivo';
                        }
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_archivo_as400($id)
    {
        $archivo = Agenda_archivo::find($id);
        if (!is_null($archivo)) {
            $name = storage_path('app/') . $archivo->ruta . $archivo->archivo;
            if (file_exists($name)) {
                return Response::make(file_get_contents($name), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $archivo->archivo . '"',
                ]);
            } else {
                return 'No se encontro el archivo';
            }
        } else {
            return 'No se encontro agenda archivo';
        }
    }
    public function descargar_cedula($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $paciente = Paciente::find($plano_cabecera->id_paciente);
                $copia_ced = Paciente_Biopsia::where('id_paciente', $paciente->id)->where('estado', 3)->first();
                if (!is_null($copia_ced)) {
                    $pathtoFile = storage_path() . '/app/avatars/' . $copia_ced->nombre;
                    return response()->file($pathtoFile);
                } else {
                    return 'No se encontro el archivo';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_receta($id_plano)
    {
        $tipo = 5;
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historia = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historia)) {
                    $receta   = hc_receta::where('id_hc', $historia->hcid)->first();
                    if (!is_null($receta)) {
                        $paciente = paciente::find($historia->id_paciente);
                        if (!is_null($paciente)) {
                            $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
                            $logo_emp = Empresa::find($plano_cabecera->id_empresa);
                            $principioActivo = "";
                            if (Count($pacienteAlergia) > 0) {
                                foreach ($pacienteAlergia as $value) {
                                    if ($principioActivo == "") {
                                        $principioActivo = $value->principio_activo->nombre;
                                    } else {
                                        $principioActivo = $principioActivo . ", " . $value->principio_activo->nombre;
                                    }
                                }
                            } else {
                                $principioActivo = "NO TIENE";
                            }
                            $edad = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
                            $detalles  = hc_receta_detalle::where('id_hc_receta', $receta->id)->get();
                            if ($tipo == 5) {
                                $detalles  = hc_receta_detalle::where('id_hc_receta', $receta->id)->join('medicina_tarifario as mt', 'mt.id_medicina', 'hc_receta_detalle.id_medicina')->get();
                            }
                            if ($tipo == 6) {
                                $detalles = hc_receta_detalle::where('id_hc_receta', $receta->id)
                                    ->whereNotIn('hc_receta_detalle.id_medicina', function ($query) {
                                        $query->select('id_medicina')->from('medicina_tarifario');
                                    })->get();
                            }
                            $medicinas = Medicina::all();
                            $cie10     = hc_cie10::where('hcid', $historia->hcid)->get();
                            $firma     = null;
                            if (!is_null($receta) && is_null($firma)) {
                                $id_doctor = $receta->id_doctor_examinador;
                                $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
                            }

                            if (!is_null($historia->hc_procedimientos) && is_null($firma)) {
                                $id_doctor = $historia->hc_procedimientos->id_doctor_examinador;
                                $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
                            }
                            if (is_null($firma)) {
                                $firma = Firma_Usuario::where('id_usuario', $historia->id_doctor1)->first();
                            }

                            if ($tipo == 2) {
                                $view = View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'logo_emp'))->render();
                            }
                            if ($tipo == 1) {
                                $view = View::make('hc_admision.receta.sinmenbrete_nuevo_preimpreso', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();
                            }
                            if ($tipo == 3) {
                                $view = View::make('hc_admision.receta.menbretada_crm_auditoria', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }
                            if ($tipo == 5) {
                                $view = View::make('hc_admision.receta.receta_iess', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }

                            if ($tipo == 6) {
                                $view = View::make('hc_admision.receta.receta_privada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
                            }

                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view)->setPaper('a4', 'landscape');

                            return $pdf->stream($historia->id_paciente . '_Receta_' . $receta->id . '.pdf');
                        } else {
                            return 'No se encontro el paciente';
                        }
                    } else {
                        return 'No se encontro la receta';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_nota_entrega($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $hc = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($hc)) {
                    $porIva = Utilidades::porcentajeIva();
                    $nota_entrega    = Nota_entrega::where('id_hc', $hc->hcid)->first();
                    if (!is_null($nota_entrega)) {
                        $nota_entrega_detalle   = $nota_entrega->detalles;
                        $agenda = Agenda::findOrFail($hc->id_agenda);
                        $vistaurl = "hc_admision.receta.pdf_nota_entrega";
                        $view     = View::make($vistaurl, compact('nota_entrega', 'nota_entrega_detalle', 'hc', 'agenda', 'porIva'))->render();
                        $pdf = App::make('dompdf.wrapper');
                        $pdf->loadHTML($view);
                        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                        $pdf->setPaper('A4', 'portraint');
                        return $pdf->stream('Nota de Entrega - ' . $nota_entrega->numero . '.pdf');
                    } else {
                        return 'No se encontro nota de entrega';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_nota_entrega_subida($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $procedimiento = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
                $fecha_hasta = $plano_cabecera->fecha_ing != null ? $plano_cabecera->fecha_ing : $procedimiento->created_at;
                $fecha_hasta = date('Y-m-d', strtotime($fecha_hasta));
                $fecha_hasta = $fecha_hasta . ' 23:59:59';
                $fecha = date('Y-m-d', strtotime('-3 month', strtotime($fecha_hasta)));

                $doc_scan = null;
                $medicinas = Historiaclinica::join('hc_receta as r', 'r.id_hc', 'historiaclinica.hcid')
                    ->join('seguros as s', 's.id', 'historiaclinica.id_seguro')
                    ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                    ->join('users as u', 'u.id', 'historiaclinica.id_doctor1')
                    ->where('historiaclinica.id_paciente', $plano_cabecera->id_paciente)
                    ->where('s.tipo', 0)
                    ->whereBetween('r.created_at', [$fecha, $fecha_hasta])
                    ->select(DB::raw('CONCAT(u.apellido1," ",u.nombre1) as doctor'), 'a.fechaini', 'r.*')
                    ->get();
                $nombre = "";
                //dd($fecha_hasta,$fecha,$plano_cabecera->id_paciente);
                foreach ($medicinas as $value) {
                    $receta = hc_receta::find($value->id);
                    $nota_entrega = Nota_entrega::where('id_hc_receta', $receta->id)->first();

                    if (!is_null($nota_entrega)) {
                        $doc_scan = Nota_Entrega_Escaneados::where('id_nota_entrega', $nota_entrega->id)->where('estado', '1')->first();
                        if (!is_null($doc_scan) && $nota_entrega->estado == 1) {
                            $nombre = $doc_scan->archivo;
                        }
                    }
                }

                $nombreArchivo = Nota_Entrega_Escaneados::where('archivo', $nombre)->first();
                $path1 = storage_path() . "/app/notas_entrega/" . $nombreArchivo->archivo;
                if (file_exists($path1)) {
                    return Response::make(file_get_contents($path1), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . basename($nombreArchivo->archivo) . '"',
                    ]);
                } else {
                    return 'No se encontro el archivo';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_scan($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $agenda = Agenda::find($historiaClinica->id_agenda);
                    if (!is_null($agenda)) {
                        $agenda = AgendaScan::where('id_agenda', $agenda->id)->first();
                        $name   = $agenda->archivo;
                        $path   = storage_path() . '/app/agenda_scan/' . $name;
                        if (file_exists($path)) {
                            return Response::download($path);
                        } else {
                            return 'No se encontro el archivo';
                        }
                    } else {
                        return 'No se encontro la agenda';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_validacion($id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento', 'LA_AS400')->first();
                    if (!is_null($archivo_historico)) {
                        $name = storage_path('app/') . $archivo_historico->ruta . $archivo_historico->archivo;
                        if (file_exists($name)) {
                            return Response::make(file_get_contents($name), 200, [
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                            ]);
                        } else {
                            return 'No se encontro el archivo';
                        }
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_evolucion_nuevo($id_plano)
    {
        $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($plano_cabecera)) {
            $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $plano_cabecera->id_hc_procedimimentos)->first();

            if (!is_null($protocolo)) {
                $procedimiento = $protocolo->auditoria_procedimiento;
                $firma = Firma_Usuario::where('id_usuario', $procedimiento->id_doctor_examinador2)->first();
                $id = $procedimiento->id_procedimientos_org; // ID PROC ORI
                $id_proc = $protocolo->auditoria_procedimiento->id; // ID PROC AUD
                $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

                if (count($evolucion) > 0) {
                    $defaultCuadroClinico = 'PACIENTE EGRESA DE QUIROFANO ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS ENDOSCOPICOS. 
                        PCTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. ES DADA DE ALTA, SE EXPLICAN LOS RESULTADOS AL PACIENTE Y ACOMPAÑANTE. SE ENTREGAN INDICACIONES AL ALTA';

                    if (!isset($evolucion[1])) {
                        $evolucion[1] = new stdClass();
                        $evolucion[1]->cuadro_clinico = $defaultCuadroClinico;
                    } else {
                        $tieneCamposVacios_1 = false;
                        if (
                            preg_match('/APP:\s*<br\s*\/?>/i', $evolucion[1]->cuadro_clinico) &&
                            preg_match('/APF:\s*<br\s*\/?>/i', $evolucion[1]->cuadro_clinico) &&
                            preg_match('/APQX:\s*<br\s*\/?>/i', $evolucion[1]->cuadro_clinico)
                        ) {
                            $tieneCamposVacios_1 = true;
                        }

                        if (trim($evolucion[1]->cuadro_clinico) == "" || $tieneCamposVacios_1) {
                            $evolucion[1]->cuadro_clinico = $defaultCuadroClinico;
                        }
                    }

                    $obtenerPlantilla = function () use ($plano_cabecera) {
                        $hc_procedimiento_f = Hc_Procedimiento_Final::join('procedimiento', 'procedimiento.id', '=', 'hc_procedimiento_final.id_procedimiento')
                            ->where('hc_procedimiento_final.id_hc_procedimientos', $plano_cabecera->id_hc_procedimimentos)
                            ->whereNotNull('procedimiento.id_grupo_procedimiento')
                            ->select('hc_procedimiento_final.*', 'procedimiento.id', 'procedimiento.nombre')
                            ->first();

                        if (!$hc_procedimiento_f) return null;

                        $consulta = DB::table('ap_plantilla_procedimiento')
                            ->where("id_procedimiento", $hc_procedimiento_f->id_procedimiento)
                            ->where("estado", 1)
                            ->inRandomOrder()
                            ->first();

                        return $consulta ? $consulta->plantilla : null;
                    };

                    if (!isset($evolucion[0])) {
                        $evolucion[0] = new stdClass();
                        $evolucion[0]->cuadro_clinico = $obtenerPlantilla() ?? '';
                    } else {
                        $tieneCamposVacios = false;
                        if (
                            preg_match('/APP:\s*<br\s*\/?>/i', $evolucion[0]->cuadro_clinico) &&
                            preg_match('/APF:\s*<br\s*\/?>/i', $evolucion[0]->cuadro_clinico) &&
                            preg_match('/APQX:\s*<br\s*\/?>/i', $evolucion[0]->cuadro_clinico)
                        ) {
                            $tieneCamposVacios = true;
                        }

                        if (trim($evolucion[0]->cuadro_clinico) == "" || $tieneCamposVacios) {
                            $plantilla = $obtenerPlantilla();
                            if ($plantilla) {
                                $evolucion[0]->cuadro_clinico = $plantilla;
                            }
                        }
                    }
                }


                $indicaciones = [];
                foreach ($evolucion as $value) {
                    if (isset($value->id)) {
                        $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();
                    }
                }
                $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_procedimientos_org)->get();
                $id_principal  = '';
                foreach ($proc_finales as $px) {
                    if ($px->procedimiento->id_grupo_procedimiento != null) {
                        $id_principal = $px->id_procedimiento;
                        break;
                    }
                }
                $historiaclinica = DB::table('historiaclinica')
                    ->where('historiaclinica.hcid', $procedimiento->id_hc)
                    ->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
                    ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
                    ->join('users', 'users.id', 'paciente.id_usuario')
                    ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
                    ->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')
                    ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
                    ->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'especialidad.nombre as enombre', 'historiaclinica.id_seguro as id_seguro')
                    ->first();
                $data            = $historiaclinica;
                $fecha_operacion = $protocolo->fecha_operacion;
                if (is_null($fecha_operacion)) {
                    $proc = hc_procedimientos::find($plano_cabecera->id_hc_procedimimentos);
                    $fecha_operacion = $proc->created_at;
                }
                $hora_ini        = $protocolo->hora_inicio;
                $cardiologia = null;

                if (count($evolucion) <= '1') {
                    $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
                    $pdf  = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($id_principal != '') {
                    if ($protocolo->tipo_anestesia == '8' || $protocolo->tipo_anestesia == '9' || $protocolo->tipo_anestesia == '10' || $protocolo->tipo_anestesia == '11' || $protocolo->tipo_anestesia == '12') {
                        $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $protocolo->tipo_anestesia)->where('estado', '1')->first();
                        $minutos = $hor_f->minutos;

                        if ($minutos >= 1440) {
                            $fecha_final = strtotime("+$minutos minute", strtotime($fecha_operacion));
                            $fecha_final = date('d/m/Y', $fecha_final);
                            $hora_fin    = strtotime("+$minutos minute", strtotime($protocolo->hora_inicio));
                            $hora_fin    = date('H:i', $hora_fin);
                            $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        } else {
                            if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {
                                $hora_ini = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                                $hora_ini = date('H:i', $hora_ini);
                            }
                            $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                            $hora_media = date('H:i', $hora_media);
                            $hora_fin   = strtotime("+$minutos minute", strtotime($protocolo->hora_inicio));
                            $hora_fin   = date('H:i', $hora_fin);
                            $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }
                    } else {
                        if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {
                            $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                            $hora_media = date('H:i', $hora_media);
                            $hora_fin   = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                            $hora_fin   = date('H:i', $hora_fin);
                            $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }

                        if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                            $fecha_final = strtotime('+1440 minute', strtotime($fecha_operacion));
                            $fecha_final = date('d/m/Y', $fecha_final);
                            $hora_fin    = strtotime('+1440 minute', strtotime($protocolo->hora_inicio));
                            $hora_fin    = date('H:i', $hora_fin);
                            $view        = View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();
                            $pdf = App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                        }
                    }
                }

                if ($protocolo->tipo_anestesia == 'GENERAL') {
                    $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }

                if ($protocolo->tipo_anestesia == 'GENERAL90') {
                    $hora_media = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($protocolo->tipo_anestesia == 'GENERAL120') {
                    $hora_media = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($protocolo->tipo_anestesia == 'GENERAL150') {
                    $hora_media = strtotime('+150 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($protocolo->tipo_anestesia == 'GENERAL180') {
                    $hora_media = strtotime('+180 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+240 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($protocolo->tipo_anestesia == 'GENERAL210') {
                    $hora_media = strtotime('+210 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+270 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if ($protocolo->tipo_anestesia == 'SEDACION') {
                    $hora_media = strtotime('+30 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+90 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                if (is_null($protocolo->tipo_anestesia)) {
                    $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view = View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
            } else {
                return 'No se encontro protocolo';
            }
        }
    }
    public function descargar_informe_descargo($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
            $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.iva_unitario', 'apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
            $view = View::make('archivo_plano.planilla.informe_descargo', compact('archivo_plano', 'txt_cie10', 'medicinas', 'insumos', 'laboratorio', 'servicios_ins', 'imagen', 'equipos', 'honor_medicos'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->download('informe_descargo.pdf');
        }
        return "no existe";
    }
    public function guardarLogDescarga(Request $request)
    {
        try {
            $arr_log = [
                'documento' => $request['fileName'],
                'ruta_firma' => $request['ruta_token'],
                'pos_sign' => $request['signPos'],
                'razon' => $request['reason'],
                'ubicacion' => $request['location'],
                'algoritmo' => $request['algoritmo'],
                'descripcion' => $request['descripcion'],
                'id_usuario' => $request['id_usuario'],
                'ip_usuario' => $request['ip_usuario'],
            ];
            LogDocumentoFirmado::create($arr_log);
            return ['status' => 'success', 'msj' => 'Guardado con exito'];
        } catch (Exception $ex) {
            return ['status' => 'error', 'msj' => $ex->getMessage()];
        }
    }
    public function descargar_informe_tecnico($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $hc_id_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)->where('estado', '1')->first();
        $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
        $cie10_in = Aud_Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first(); //dd($protocolo, $archivo_plano->id_hc_procedimimentos);

        if ($hc_id_procedimiento == null) {
            $hc_id_procedimiento = $archivo_plano->fx_hc_procedimiento;
            $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('secuencia', '0')->where('estado', '1')->first();
            $cie10_eg = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'EGRESO')->get();
            $cie10_in = Hc_Cie10::where('hc_id_procedimiento', $archivo_plano->id_hc_procedimimentos)->where('ingreso_egreso', 'INGRESO')->get();
            $protocolo = hc_protocolo::where('id_hc_procedimientos', $archivo_plano->id_hc_procedimimentos)->first();
        }
        $detalles = $archivo_plano->detalles->whereIn('tipo', ['P', 'PA'])->whereNotIn('codigo', ['93000', '99204', '99203'])->where("estado", 1);
        $tarifario = DB::table('ap_tarifarios_protocolo')
            ->join(
                'maestro_de_codigo',
                'ap_tarifarios_protocolo.id_maestro_codigo',
                '=',
                'maestro_de_codigo.id'
            )
            ->whereIn('tarifario', $detalles->pluck('codigo'))
            ->where('ap_tarifarios_protocolo.estado', 1)
            ->groupBy('ap_tarifarios_protocolo.id_maestro_codigo')
            ->select(
                'maestro_de_codigo.id',
                'maestro_de_codigo.protocolos',
                'maestro_de_codigo.observaciones',
                'maestro_de_codigo.nac',
                'maestro_de_codigo.intern',
                'maestro_de_codigo.mbe'
            )
            ->get();
        $protos = Informe_Tecnico::where('id_ap_cabecera', $archivo_plano->id)->get();

        $view = View::make('pdf_hospital.informe_tecnico', compact('id', 'hc_id_procedimiento', 'archivo_plano', 'evolucion', 'detalles', 'protos', 'cie10_eg', 'cie10_in', 'protocolo', 'tarifario'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('INFORME TECNICO.pdf');
    }
    public function descargar_formulario_externo($cod_doc, $id_plano)
    {
        try {
            $plano_cabecera = Archivo_Plano_Cabecera::find($id_plano);
            if (!is_null($plano_cabecera)) {
                $historiaClinica = Historiaclinica::find($plano_cabecera->id_hc);
                if (!is_null($historiaClinica)) {
                    $archivo_historico = Archivo_historico::where('id_historia', $historiaClinica->hcid)->where('tipo_documento', $cod_doc)->first();
                    if (!is_null($archivo_historico)) {
                        if (!empty($archivo_historico->archivo)) {
                            $name = storage_path('app/') . $archivo_historico->ruta . $archivo_historico->archivo;
                            if (file_exists($name)) {
                                return Response::make(file_get_contents($name), 200, [
                                    'Content-Type'        => 'application/pdf',
                                    'Content-Disposition' => 'inline; filename="' . $archivo_historico->archivo . '"',
                                ]);
                            } else {
                                return 'No se encontro el archivo';
                            }
                        } else {
                            return 'No se cargo el archivo';
                        }
                    } else {
                        return 'No se encontro el archivo historico';
                    }
                } else {
                    return 'No se encontro historia';
                }
            } else {
                return 'No se encontro registro';
            }
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_espirometria($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $id_paciente       = $archivo_plano->id_paciente;
            $fecha  = $archivo_plano->fecha_ing;
            $aud_hc = $archivo_plano->id_hc;
            $hc_proc = $archivo_plano->id_hc_procedimimentos;
            $historia_clinica = Historiaclinica::join('paciente', 'paciente.id', 'historiaclinica.id_paciente')
                ->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')
                ->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')
                ->where('historiaclinica.id_paciente', $id_paciente)
                ->where('agenda.fechaini', '<', $fecha)
                ->where('agenda.espid', '=', '5')
                ->where('historiaclinica.id_seguro', '=', $archivo_plano->id_seguro)
                ->select('historiaclinica.*')
                ->first();
            $idusuario = '9999966654';

            if (!is_null($historia_clinica)) {
                $paciente = $historia_clinica->paciente;
                $agenda = $historia_clinica->agenda;
                $evolucion_orden = Hc_Evolucion::where('hcid', $historia_clinica->hcid)->where('secuencia', '0')->first();
                $age = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                $cie10_orden = Hc_Cie10::where('hcid', $historia_clinica->hcid)->get();
                $vistaurl = "pdf_hospital.formulario007_espirometria";
                $view = View::make($vistaurl, compact('fecha', 'historia_clinica', 'age', 'paciente', 'evolucion_orden', 'cie10_orden', 'hc_proc', 'aud_hc'))->render();
                $pdf  = App::make('dompdf.wrapper');
                $pdf->setPaper('A4');
                $pdf->loadHTML($view);
                return $pdf->stream('HCU_007.pdf', array("Attachment" => false));
            } else {
                return "No se encontro consulta neumologica";
            }
        } else {
            return "No se encontro consulta neumologica";
        }
    }
    public function descargar_estudio_espirometria($id)
    {
        $archivo = hc_imagenes_protocolo::find($id);
        if (is_null($archivo)) {
            return 'No se encontro archivo de espirometria';
        }

        $filename = $archivo->nombre;
        if (!$filename) {
            return 'Nombre del archivo invalido';
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return 'No se descarga: el archivo no es PDF (ext=' . $ext . ')';
        }

        // 1) Buscar en storage/app/hc_ima/{nombre}
        $path1 = storage_path('app/hc_ima/' . $filename);
        if (file_exists($path1)) {
            return Response::make(file_get_contents($path1), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
            ]);
        }

        // 2) Si no está, buscar en la ruta del href asset('hc_ima_nombre')/{id}
        // asset('hc_ima_nombre') apunta a /public/hc_ima_nombre
        $path2 = public_path('hc_ima_nombre/' . $id);
        if (file_exists($path2)) {
            return Response::make(file_get_contents($path2), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
            ]);
        }

        // 3) No existe en ninguna
        return 'No se encontro el archivo PDF (id=' . $id . ', nombre=' . $filename . ')';
    }
    public function descargar_agenda_archivo($id)
    {
        try {
            $archivo = Agenda_archivo::find($id);

            if (is_null($archivo)) {
                return 'No se encontro el archivo';
            }

            $filename = $archivo->archivo;
            if (empty($filename)) {
                return 'Nombre del archivo invalido';
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Solo permitir PDF
            if ($ext !== 'pdf') {
                return 'No se descarga: el archivo no es PDF (ext=' . $ext . ')';
            }

            $rutas_posibles = [
                public_path('agenda/paciente/descarga/' . $filename),
                public_path('hc_agenda/' . $filename),
                storage_path('app/agenda/paciente/descarga/' . $filename),
                storage_path('app/hc_agenda/' . $filename),
            ];

            foreach ($rutas_posibles as $path) {
                if (file_exists($path)) {
                    return Response::make(file_get_contents($path), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
                    ]);
                }
            }
            return 'No se encontro el archivo PDF en las rutas configuradas. Archivo: ' . $filename;
        } catch (\Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function crear_epicrisis($hcid, $proc)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = '9999966654';
        date_default_timezone_set('America/Guayaquil');
        $hc_procedimiento = Aud_Hc_Procedimientos::find($proc);
        $historia = Historiaclinica::find($hcid);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);
        $seguro = Seguro::find($historia->id_seguro);
        $evolucion_p   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        $evolucion   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        $evolucion_1 = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $epicrisis_org = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $proc)->first();
        $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        $favorable_des = null;
        if (!is_null($evolucion_1)) {
            $favorable_des = $evolucion_1->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => '',
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            } else {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => $epicrisis_org->favorable_des,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            }
            $id_evolucion  = Hc_Evolucion::insertGetId($input);
            $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
            $favorable_des = $evolucion_1->cuadro_clinico;
        }

        if (!is_null($evolucion_1)) {
            $favorable_des = $evolucion_1->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => '',
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $id_evolucion,
                ];
            }
            $id_evolucion  = Aud_Hc_Evolucion::insertGetId($input);
            $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        }
        $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $c_clinico = null;
        if (!is_null($evolucion)) {
            $c_clinico = $evolucion->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $edad     = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                $alergias = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
                if ($alergias == "[]") {
                    $alergia = "No";
                } else {
                    $alergia = "";
                    foreach ($alergias as $value) {
                        if ($alergia == "") {
                            $alergia = $value->principio_activo->nombre;
                        } else {
                            $alergia = $alergia . ", " . $value->principio_activo->nombre;
                        }
                    }
                }
                if ($paciente->sexo == 1) {
                    $sexo = "MASCULINO";
                } else {
                    $sexo = "FEMENINO";
                }
                $procedimientos       = hc_procedimientos::find($proc);
                $nombre_procedimiento = "";
                if ($procedimientos->id_procedimiento != null) {
                    $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
                }
                $cuadro_clinico = "<p>PACIENTE " . $sexo . " DE " . $edad . " AÑOS DE EDAD ACUDE CON ORDEN DEL " . $seguro->nombre . " PARA LA REALIZACION DE " . $nombre_procedimiento . "<br> APP: " . $paciente->antecedentes_pat . " <br> APF: " . $paciente->antecedentes_fam . "<br> APQX: " . $paciente->antecedentes_quir . "<br> ALERGIAS: " . $alergia . "<br></p>";
                $input          = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 0,
                    'cuadro_clinico'      => $cuadro_clinico,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $evolucion_1->id,
                ];
            } else {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 0,
                    'cuadro_clinico'      => $epicrisis->cuadro_clinico,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $evolucion_1->id,
                ];
            }
            $id_evolucion = Aud_Hc_Evolucion::insertGetId($input);
            $evolucion    = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
            $c_clinico    = $evolucion->cuadro_clinico;
        }

        if (is_null($epicrisis_org)) {
            //CREAR EPICRISIS
            $input1 = [
                'cuadro_clinico'      => $c_clinico,
                'hc_id_procedimiento' => $proc,
                'hcid'                => $hcid,
                'favorable_des'       => $favorable_des,
                'complicacion'        => $protocolo->complicaciones,
                //'hallazgo' => $protocolo->hallazgos,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_epi = Hc_Epicrisis::insertGetId($input1);
            $epicrisis_org = Hc_Epicrisis::find($id_epi);
        } else {

            $input1a = [
                'cuadro_clinico'  => $c_clinico,
                //'hallazgo' => $protocolo->hallazgos,
                'favorable_des'   => $favorable_des,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $epicrisis_org->update($input1a);
        }
        $id_epicrisis_org = $epicrisis_org->id;

        if (is_null($epicrisis)) {
            //CREAR EPICRISIS
            $input1_a = [
                'cuadro_clinico'      => $c_clinico,
                'hc_id_procedimiento' => $proc,
                'hcid'                => $hcid,
                'favorable_des'       => $favorable_des,
                'complicacion'        => $protocolo->complicaciones,
                //'hallazgo' => $protocolo->hallazgos,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'id_epricrisis_org'   => $id_epicrisis_org,
            ];
            $id_epi_a = Aud_Hc_Epicrisis::insertGetId($input1_a);
            $epicrisis = Aud_Hc_Epicrisis::find($id_epi_a);
        } else {
            $input1a_aud = [
                //'cuadro_clinico'  => $c_clinico,
                'hallazgo' => $protocolo->hallazgos
            ];
            $epicrisis->update($input1a_aud);
        }
    }
    public function crear_evolucion($hcid, $id_aud_procedimiento)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = '9999966654';
        date_default_timezone_set('America/Guayaquil');
        $hc_procedimiento = Aud_Hc_Procedimientos::find($id_aud_procedimiento);
        $proc = $hc_procedimiento->id;
        $historia = Historiaclinica::find($hcid);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);
        $seguro = Seguro::find($historia->id_seguro);
        $evolucion_p   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        $evolucion   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        $evolucion_1 = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $epicrisis_org = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $proc)->first();
        $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        $favorable_des = null;
        if (!is_null($evolucion_1)) {
            $favorable_des = $evolucion_1->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => '',
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            } else {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => $epicrisis_org->favorable_des,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            }
            $id_evolucion  = Hc_Evolucion::insertGetId($input);
            $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
            $favorable_des = $evolucion_1->cuadro_clinico;
        }

        if (!is_null($evolucion_1)) {
            $favorable_des = $evolucion_1->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => '',
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $id_evolucion,
                ];
            }
            $id_evolucion  = Aud_Hc_Evolucion::insertGetId($input);
            $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        }
        $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $c_clinico = null;
        if (!is_null($evolucion)) {
            $c_clinico = $evolucion->cuadro_clinico;
        } else {
            if (is_null($epicrisis_org)) {
                $edad     = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                $alergias = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
                if ($alergias == "[]") {
                    $alergia = "No";
                } else {
                    $alergia = "";
                    foreach ($alergias as $value) {
                        if ($alergia == "") {
                            $alergia = $value->principio_activo->nombre;
                        } else {
                            $alergia = $alergia . ", " . $value->principio_activo->nombre;
                        }
                    }
                }
                if ($paciente->sexo == 1) {
                    $sexo = "MASCULINO";
                } else {
                    $sexo = "FEMENINO";
                }
                $procedimientos       = hc_procedimientos::find($proc);
                $nombre_procedimiento = "";
                if ($procedimientos->id_procedimiento != null) {
                    $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
                }
                $cuadro_clinico = "<p>PACIENTE " . $sexo . " DE " . $edad . " AÑOS DE EDAD ACUDE CON ORDEN DEL " . $seguro->nombre . " PARA LA REALIZACION DE " . $nombre_procedimiento . "<br> APP: " . $paciente->antecedentes_pat . " <br> APF: " . $paciente->antecedentes_fam . "<br> APQX: " . $paciente->antecedentes_quir . "<br> ALERGIAS: " . $alergia . "<br></p>";
                $input          = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 0,
                    'cuadro_clinico'      => $cuadro_clinico,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $evolucion_1->id,
                ];
            } else {
                $input = [
                    'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 0,
                    'cuadro_clinico'      => $epicrisis->cuadro_clinico,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_evolucion_org'    => $evolucion_1->id,
                ];
            }
            $id_evolucion = Aud_Hc_Evolucion::insertGetId($input);
            $evolucion    = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
            $c_clinico    = $evolucion->cuadro_clinico;
        }
    }
    public function descargar_sevoflurano($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $id_procedimiento = $archivo_plano->id_hc_procedimimentos;
            $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
            if (!is_null($aud_protocolo)) {
                $protocolo = hc_protocolo::find($aud_protocolo->id_protocolo_org);
                if (!is_null($protocolo)) {
                    $proce = Hc_Procedimiento_Final::where("id_hc_procedimientos", $protocolo->id_hc_procedimientos)->get();
                    $isCpre = $proce->contains('id_procedimiento', 38);
                    $id_agenda = $protocolo->historiaclinica->id_agenda;
                    $agenda    = Agenda::find($id_agenda);
                    $fecha     = $agenda->fechaini;
                    $hora_ini = substr($aud_protocolo->hora_inicio, 0, 5);
                    $hora_fin = substr($aud_protocolo->hora_fin, 0, 5);
                    $id_anestesiologo = null;
                    $fecha_operacion = $aud_protocolo->fecha_operacion;
                    if ($fecha_operacion == null) {
                        $fecha_operacion = substr($agenda->fechaini, 0, 10);
                    }
                    if (!is_null($aud_protocolo->auditoria_procedimiento)) {
                        $id_anestesiologo = $aud_protocolo->auditoria_procedimiento->id_doctor_examinador2;
                    } else {
                        $id_anestesiologo = $protocolo->procedimiento->id_doctor_examinador2;
                    }
                    $anestesiologo = User::find($id_anestesiologo);
                    $empresa       = Empresa::find($archivo_plano->id_empresa);
                    $porcentaje    = 0;
                    $horasSegundos = 0;
                    $fechaini = date('Y-m-d') . " " . $hora_ini;
                    $fechafin = date('Y-m-d') . " " . $hora_fin;
                    $horasDiff = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = ($horasDiff / 60) * (-1);
                    $horas    = ($horasDiff / 3600) * (-1);
                    $litros   = '5';
                    $totalLitros = $litros * $minutos;
                    $valorUnitario = 0.01;
                    $valorTotal  = $valorUnitario * $totalLitros;

                    $logoBase64 = null;
                    $logoPath = base_path() . '/storage/app/logo/' . $empresa->logo;
                    if (!empty($empresa->logo) && file_exists($logoPath)) {
                        $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                        $logoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                    $firmaBase64 = null;
                    $firma = Firma_Usuario::where('id_usuario', $agenda->id_seguro == 5 ? $aud_protocolo->id_anestesiologo : $anestesiologo)->first();

                    if ($firma && $firma->nombre) {
                        $firmaPath = base_path() . '/storage/app/avatars/' . $firma->nombre;
                        if (file_exists($firmaPath)) {
                            $ext = strtolower(pathinfo($firmaPath, PATHINFO_EXTENSION));
                            $firmaBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($firmaPath));
                        }
                    }
                    $cantidadSevo = null;
                    if ($anestesiologo && $porcentaje) {
                        $constante    = 3.33;
                        $cantidadSevo = floor($constante * $porcentaje * $litros);
                        $horasSegundos = $horasDiff * (-1); // en segundos
                    }

                    $view = View::make('hc_admision.historia.consumo_oxigeno_pdf', compact(
                        'empresa',
                        'agenda',
                        'fecha',
                        'hora_ini',
                        'hora_fin',
                        'litros',
                        'minutos',
                        'horas',
                        'totalLitros',
                        'valorUnitario',
                        'valorTotal',
                        'logoBase64',
                        'firmaBase64',
                        'anestesiologo',
                        'porcentaje',
                        'cantidadSevo',
                        'horasSegundos',
                        'isCpre',
                        'fecha_operacion',
                        'aud_protocolo'
                    ))->render();

                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    $pdf->setPaper('a4', 'landscape');

                    return $pdf->stream('Reporte_Consumo_Oxigeno_' . $fecha . '.pdf');
                }
            }
        }
    }
    public function descargar_adicionales($id)
    {
        try {
            $archivo = Paciente_Biopsia::find($id);

            if (is_null($archivo)) {
                return 'No se encontro el archivo';
            }

            $filename = $archivo->nombre;
            if (empty($filename)) {
                return 'Nombre del archivo invalido';
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Solo permitir PDF
            if ($ext !== 'pdf') {
                return 'No se descarga: el archivo no es PDF (ext=' . $ext . ')';
            }

            $rutas_posibles = [
                public_path('agenda/paciente/descarga/' . $filename),
                public_path('hc_agenda/' . $filename),
                storage_path('app/agenda/paciente/descarga/' . $filename),
                storage_path('app/hc_agenda/' . $filename),
                storage_path('/app/hc_ima/' . $filename),
            ];

            foreach ($rutas_posibles as $path) {
                if (file_exists($path)) {
                    return Response::make(file_get_contents($path), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
                    ]);
                }
            }
            return 'No se encontro el archivo PDF en las rutas configuradas. Archivo: ' . $filename;
        } catch (Exception $ex) {
            return 'Error: ' . $ex->getMessage() . '; Linea: ' . $ex->getLine();
        }
    }
    public function descargar_certificado_seg_priv($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $paciente = Paciente::find($archivo_plano->id_paciente);
            $seguro_privado = Seguro::find($archivo_plano->id_seguro_priv);
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            $fecha_derivacion = $archivo_plano->fecha_ing;
            $vistaurl = "pdf_hospital.consulta_financiamiento";
            $view = View::make($vistaurl, compact('paciente', 'seguro_privado', 'edad', 'fecha_derivacion'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('carta.pdf', array("Attachment" => false));
        }
    }
    public function descargar_lista_insumos($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
            $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.iva_unitario', 'apd.p10_unitario')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
            $ruta = 'archivo_plano.planilla.informe_descargo_msp';
            $view = View::make($ruta, compact('archivo_plano', 'medicinas', 'insumos'))->render();
            $pdf  = App::make('dompdf.wrapper');
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('lista_insumo.pdf', array("Attachment" => false));
        } else {
            return 'No se encontro cabecera';
        }
    }
    public function descargar_estudio_funcional($id)
    {
        $archivo = hc_imagenes_protocolo::find($id);

        if (is_null($archivo)) {
            return 'No se encontro archivo de estudio funcional';
        }

        $filename = $archivo->nombre;

        if (!$filename) {
            return 'Nombre del archivo invalido';
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $extensionesPermitidas = [
            'pdf',
            'doc',
            'docx',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'bmp'
        ];

        if (!in_array($ext, $extensionesPermitidas)) {
            return 'No se descarga: tipo de archivo no permitido (ext=' . $ext . ')';
        }

        $contentType = 'application/octet-stream';

        if ($ext === 'pdf') {
            $contentType = 'application/pdf';
        } elseif ($ext === 'doc') {
            $contentType = 'application/msword';
        } elseif ($ext === 'docx') {
            $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $contentType = 'image/jpeg';
        } elseif ($ext === 'png') {
            $contentType = 'image/png';
        } elseif ($ext === 'gif') {
            $contentType = 'image/gif';
        } elseif ($ext === 'webp') {
            $contentType = 'image/webp';
        } elseif ($ext === 'bmp') {
            $contentType = 'image/bmp';
        }

        $disposition = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']) ? 'inline' : 'attachment';
        $path1 = storage_path('app/hc_ima/' . $filename);

        if (file_exists($path1)) {
            return Response::make(file_get_contents($path1), 200, [
                'Content-Type'        => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . basename($filename) . '"',
            ]);
        }

        $path2 = public_path('hc_ima_nombre/' . $filename);

        if (file_exists($path2)) {
            return Response::make(file_get_contents($path2), 200, [
                'Content-Type'        => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . basename($filename) . '"',
            ]);
        }

        $path3 = public_path('hc_ima_nombre/' . $id);

        if (file_exists($path3)) {
            return Response::make(file_get_contents($path3), 200, [
                'Content-Type'        => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . basename($filename) . '"',
            ]);
        }

        return 'No se encontro el archivo (id=' . $id . ', nombre=' . $filename . ', ext=' . $ext . ')';
    }
    public function descargar_cedula_paciente($id_plano)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (!is_null($archivo_plano)) {
            $copia_ced = Paciente_Biopsia::where('id_paciente', $archivo_plano->id_paciente)->where('estado', 3)->first();
            if (!is_null($copia_ced)) {
                $pathtoFile = storage_path('/app/avatars/') . $copia_ced->nombre;
                return response()->file($pathtoFile, [
                    'Content-Disposition' => 'inline; filename="' . 'D_ANEXOS' . '"'
                ]);
            } else {
                return "no se encontro cedula paciente";
            }
        }
    }
    public function actualizarNombresArchivoPlano($id_empresa, $mes_plano, $id_seguro)
    {
        $cabeceras = Archivo_Plano_Cabecera::where('id_empresa', $id_empresa)
            ->where('mes_plano', $mes_plano)
            ->where('id_seguro', $id_seguro)
            ->where('estado', 1)
            ->get();

        try {
            foreach ($cabeceras as $cabecera) {

                $paciente = Paciente::find($cabecera->id_paciente);

                if (!$paciente) {
                    continue;
                }

                $apellido1 = trim($paciente->apellido1 ?? '');
                $apellido2 = trim($paciente->apellido2 ?? '');
                $nombre1   = trim($paciente->nombre1 ?? '');
                $nombre2   = trim($paciente->nombre2 ?? '');

                $apellido2Limpio = trim(str_replace(['.', '(N/A)'], '', $apellido2));
                $nombre2Limpio   = trim(str_replace(['.', '(N/A)'], '', $nombre2));

                $nombres = $apellido1;

                if ($apellido2Limpio != '') {
                    $nombres .= ' ' . $apellido2;
                }

                $nombres .= ' ' . $nombre1;

                if ($nombre2Limpio != '') {
                    $nombres .= ' ' . $nombre2;
                }
                //dd($cabecera->nombres);
                if ($cabecera->nombres != $nombres) {
                    $cabecera->nombres = trim($nombres);
                    $cabecera->save();
                }
            }
            return ['status' => 'success', 'msj' => 'realizado con exito'];
        } catch (Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }

    public function planilla_individual_msp_excel($id_cab)
    {
        $fecha_elaboracion = date('d/m/Y');
        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.id', $id_cab)
            ->where('archivo_plano_cabecera.estado', '1')
            ->first();
        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;
        $historia_clinica = Historiaclinica::find($archivo_plano->id_hc);
        $agend = agenda::find($historia_clinica->id_agenda);

        //Detalle de Servicio Desde
        $fecha_desde = substr($archivo_plano->fecha_ing, 0, 10);
        $invert_fech_desde = explode('-', $fecha_desde);
        $fecha_desde_invert = $invert_fech_desde[2] . "/" . $invert_fech_desde[1] . "/" . $invert_fech_desde[0];

        //Detalle de Servicio Hasta
        $fecha_hasta = substr($archivo_plano->fecha_alt, 0, 10);
        $invert_fech_hasta = explode('-', $fecha_hasta);
        $fecha_hasta_invert = $invert_fech_hasta[2] . "/" . $invert_fech_hasta[1] . "/" . $invert_fech_hasta[0];

        //Anio servicio
        $anio_servicio = $invert_fech_desde[0];

        //Mes Servicio
        $mes_servicio = $invert_fech_desde[1];

        //Obtenemos el mes
        $txt_mes = '';
        if ($mes_servicio == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes_servicio == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes_servicio == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes_servicio == '09') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes_servicio == '08') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes_servicio == '07') {
            $txt_mes = 'JULIO';
        } elseif ($mes_servicio == '06') {
            $txt_mes = 'JUNIO';
        } elseif ($mes_servicio == '05') {
            $txt_mes = 'MAYO';
        } elseif ($mes_servicio == '04') {
            $txt_mes = 'ABRIL';
        } elseif ($mes_servicio == '03') {
            $txt_mes = 'MARZO';
        } elseif ($mes_servicio == '02') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes_servicio == '01') {
            $txt_mes = 'ENERO';
        }

        $mes_anio_servicio = $txt_mes . "-" . $anio_servicio;
        $txt_cie10 = null;
        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        $planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')
            ->where('apd.id_ap_cabecera', $archivo_plano->id)
            ->where('apd.estado', '1')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->select('apd.fecha', 'apd.codigo', 'apd.descripcion', 'apt.clasificado', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.clasif_porcentaje_msp', 'apd.valor_porcent_clasifi', 'apd.total_solicitado_usd', 'apd.tipo', 'apd.clasificador', 'apd.clasif_porcentaje_msp', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.porcent_10', 'apd.iva_unitario', 'apd.p10_unitario', 'apd.total')
            ->orderBy('apt.orden_msp', 'asc')
            ->orderBy('apd.id', 'asc')
            ->get();

        $total_inicio = 0.00;
        foreach ($planilla_msp_detalle as $value) {
            $total_inicio += isset($value->total_solicitado_usd) ? (float) $value->total_solicitado_usd : 0.00;
        }

        Excel::create('Planilla MSP ', function ($excel) use ($agend, $archivo_plano, $detalles, $txt_cie10, $fecha_desde_invert, $fecha_hasta_invert, $fecha_elaboracion, $mes_anio_servicio, $planilla_msp_detalle, $total_inicio) {
            $excel->sheet('Planilla MSP', function ($sheet) use ($agend, $archivo_plano, $detalles, $txt_cie10, $fecha_desde_invert, $fecha_hasta_invert, $fecha_elaboracion, $mes_anio_servicio, $planilla_msp_detalle, $total_inicio) {

                $sheet->mergeCells('B2:Q2');
                $sheet->cell('B2', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->empresa->razonsocial);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                });

                $sheet->mergeCells('B3:Q3');
                $sheet->cell('B3', function ($cell) {
                    $cell->setValue('PLANILLA DE CARGOS INDIVIDUAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('Tipo Servicio:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', 'thin');
                });

                $sheet->mergeCells('F5:K5');
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', '', '', '');
                });

                $sheet->mergeCells('L5:N5');
                $sheet->cell('L5', function ($cell) {
                    $cell->setValue('Fecha de Elaboración:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', '');
                });

                $sheet->mergeCells('O5:Q5');
                $sheet->cell('O5', function ($cell) use ($fecha_elaboracion) {
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', '', '');
                });

                $sheet->mergeCells('B6:E6');
                $sheet->cell('B6', function ($cell) {
                    $cell->setValue('Monto Solicitado:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F6:K6');
                $sheet->getStyle('F6')->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('F6', function ($cell) use ($total_inicio) {

                    $valor_total = number_format((float) $total_inicio, 2, '.', '');
                    $cell->setValue($valor_total);
                    //dd($valor_total, gettype($valor_total));
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L6:N6');
                $sheet->cell('L6', function ($cell) {
                    $cell->setValue('Mes y Año de Servicio:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O6:Q6');
                $sheet->cell('O6', function ($cell) use ($mes_anio_servicio) {
                    $cell->setValue($mes_anio_servicio);
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B7:E7');
                $sheet->cell('B7', function ($cell) {
                    $cell->setValue('Nro. CV:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F7:K7');
                $sheet->cell('F7', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->cod_deriva_msp);
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L7:N7');
                $sheet->cell('L7', function ($cell) {
                    $cell->setValue('CIE10:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O7:Q7');
                $sheet->cell('O7', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->cie10);
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B8:E8');
                $sheet->cell('B8', function ($cell) {
                    $cell->setValue('CC No:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F8:K8');
                $sheet->cell('F8', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->id_paciente);
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L8:N8');
                $sheet->cell('L8', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O8:Q8');
                $sheet->cell('O8', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B9:E9');
                $sheet->cell('B9', function ($cell) {
                    $cell->setValue('Beneficiario:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F9:K9');
                $sheet->cell('F9', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2);
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L9:N9');
                $sheet->cell('L9', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O9:Q9');
                $sheet->cell('O9', function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B10:E10');
                $sheet->cell('B10', function ($cell) {
                    $cell->setValue('Detalle de Servicio Desde:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F10:K10');
                $sheet->cell('F10', function ($cell) use ($fecha_desde_invert) {
                    $cell->setValue($fecha_desde_invert);
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L10:N10');
                $sheet->cell('L10', function ($cell) {
                    $cell->setValue('Hasta:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O10:Q10');
                $sheet->cell('O10', function ($cell) use ($fecha_hasta_invert) {
                    $cell->setValue($fecha_hasta_invert);
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B11:C11');
                $sheet->cell('B11', function ($cell) {
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D11', function ($cell) {
                    $cell->setValue("Código\nTSNS");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E11', function ($cell) {
                    $cell->setValue("Clasificador");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('F11:K11');
                $sheet->cell('F11', function ($cell) {
                    $cell->setValue('Descripción');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L11', function ($cell) {
                    $cell->setValue("Cantidad\n#");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M11', function ($cell) {
                    $cell->setValue("Valor Unitario\nUSD");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N11', function ($cell) {
                    $cell->setValue("Clasificador\n%");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O11', function ($cell) {
                    $cell->setValue("Subtotal\nUSD");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P11', function ($cell) {
                    $cell->setValue("Valor por\nModificador USD");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Q11', function ($cell) {
                    $cell->setValue("Total Solicitado\nUSD");
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',
                    'Q' => '0.00',
                ));

                $i = 12;
                $j = 0;
                $k = 0;
                $l = 0;
                $m = 0;
                $n = 0;
                $p = 0;
                $q = 0;
                $r = 0;
                $s = 0;
                $t = 0;

                $total = 0;

                foreach ($planilla_msp_detalle as $key => $value) {
                    $invert_fecha = substr($value->fecha, 0, 10);
                    $invert_fech_deta = explode('-', $invert_fecha);
                    $invert_fech_detalle = $invert_fech_deta[2] . "/" . $invert_fech_deta[1] . "/" . $invert_fech_deta[0];

                    $sheet->mergeCells('B' . $i . ':C' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($invert_fech_detalle) {
                        $cell->setValue($invert_fech_detalle);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $tipos = ['P', 'AN', 'S'];
                    $codigos = ['16', '240159'];
                    $sheet->cell('D' . $i, function ($cell) use ($value, $tipos, $codigos) {
                        if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                            $cell->setValue(' ');
                        } else {
                            if (strlen($value->codigo) <= 10 && $value->clasificador != 'SA19-56') {
                                if (in_array($value->tipo, $tipos) || in_array($value->codigo, $codigos)) {
                                    if ($value->codigo == 16) {
                                        $cell->setValue(911111);
                                    } else {
                                        $cell->setValue($value->codigo);
                                    }
                                }
                            }
                        }
                        if ($value->codigo == '16') {
                            $ap_procedimiento = ApProcedimiento::where('codigo', $value->codigo)->where('tipo', $value->tipo)->first();
                            if (!is_null($ap_procedimiento)) {
                                if (strlen($ap_procedimiento->cod_cb) <= 10 && $value->clasificador != 'SA19-56') {
                                    if (in_array($value->tipo, $tipos) || in_array($value->codigo, $codigos)) {
                                        if ($value->codigo == 16) {
                                            $cell->setValue(911111);
                                        } else {
                                            $cell->setValue($value->codigo);
                                        }
                                    }
                                }
                            }
                        }

                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->clasificador);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $descripcion = trim($value->descripcion);

                    // Limpia espacios dobles, tabs, saltos raros
                    $descripcion = preg_replace('/\s+/', ' ', $descripcion);

                    // Para F:K, corta cada 32 caracteres aprox.
                    $descripcionAjustada = wordwrap($descripcion, 32, "\r\n", false);

                    // Cuenta líneas
                    $lineas = substr_count($descripcionAjustada, "\r\n") + 1;

                    // Altura segura.
                    // Si son 5 líneas: 5 * 18 + 25 = 115
                    $alturaFila = ($lineas * 18) + 25;

                    // Mínimo
                    if ($alturaFila < 60) {
                        $alturaFila = 60;
                    }

                    $sheet->mergeCells('F' . $i . ':K' . $i);

                    $sheet->cell('F' . $i, function ($cell) use ($descripcionAjustada) {
                        $cell->setValue($descripcionAjustada);
                        $cell->setAlignment('center');

                        // MUY IMPORTANTE: arriba, no centro
                        $cell->setValignment('top');

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // Ajustar texto
                    $sheet->getStyle('F' . $i . ':K' . $i)
                        ->getAlignment()
                        ->setWrapText(true);

                    // Alinear arriba desde PHPExcel
                    $sheet->getStyle('F' . $i . ':K' . $i)
                        ->getAlignment()
                        ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);

                    // IMPORTANTE: esto debe ir DESPUÉS de pintar la celda
                    $sheet->setHeight($i, $alturaFila);


                    //fin

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        $valor = number_format((float) $value->cantidad, 2, '.', '');
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        if ($value->clasif_porcentaje_msp == 50) {
                            $valor_real = (($value->valor_unitario + $value->p10_unitario + $value->iva_unitario) * (2));
                            $valor_unitario = round($valor_real, 2);
                            $valor = number_format((float) $valor_unitario, 2, '.', '');
                        } else {
                            $valor_real = $value->valor_unitario + $value->p10_unitario + $value->iva_unitario;
                            $valor_unitario = round($valor_real, 2);
                            $valor = number_format((float) $valor_unitario, 2, '.', '');
                        }
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        $valor = round($value->clasif_porcentaje_msp, 2);
                        $valor = number_format((float) $valor, 2, '.', '');
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('O' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        $valor = number_format((float) $value->total, 2, '.', '');
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        $valor = round($value->valor_porcent_clasifi, 2);
                        $valor = number_format((float) $valor, 2, '.', '');
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('Q' . $i, function ($cell) use ($value) {

                        if ($value->valor_porcent_clasifi > 0) {
                            $value->total_solicitado_usd += $value->valor_porcent_clasifi;
                        }

                        $valor = round($value->total_solicitado_usd, 2);
                        $valor = number_format((float) $valor, 2, '.', '');
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $total += $value->total_solicitado_usd;
                    $i++;
                }

                $sheet->getStyle('B11:Q' . $i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B11:Q' . $i)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $sheet->getRowDimension(11)->setRowHeight(45);

                for ($fila = 12; $fila <= $i; $fila++) {
                    $sheet->getRowDimension($fila)->setRowHeight(30);
                }

                $sheet->mergeCells('B' . $i . ':P' . $i);
                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('Valor Total Solicitado');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('Q' . $i, function ($cell) use ($total) {
                    $valor = number_format((float) $total, 2, '.', '');
                    $cell->setValue($valor);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $j = $i + 2;

                $sheet->mergeCells('B' . $j . ':Q' . $j);
                $sheet->cell('B' . $j, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $k = $j + 1;
                $sheet->mergeCells('B' . $k . ':Q' . $k);
                $sheet->cell('B' . $k, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $l = $k + 1;
                $sheet->mergeCells('B' . $l . ':Q' . $l);
                $sheet->cell('B' . $l, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $m = $l + 1;
                $sheet->mergeCells('B' . $m . ':Q' . $m);
                $sheet->cell('B' . $m, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $n = $m + 1;
                $sheet->mergeCells('B' . $n . ':Q' . $n);
                $sheet->cell('B' . $n, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $p = $n + 1;
                $sheet->mergeCells('B' . $p . ':Q' . $p);
                $sheet->cell('B' . $p, function ($cell) {
                    $cell->setValue('Firma:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $q = $p + 1;
                $sheet->cell('B' . $q, function ($cell) {
                    $cell->setValue('Nombre:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $q . ':G' . $q);
                $sheet->cell('C' . $q, function ($cell) {
                    $cell->setValue('DRA. AURA EDITH ZAMBRANO PAREDES');
                    $cell->setBorder('', '', 'hair', '');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H' . $q . ':Q' . $q);
                $sheet->cell('H' . $q, function ($cell) {
                    $cell->setBorder('', 'thin', '', '');
                });

                $r = $q + 1;
                $sheet->cell('B' . $r, function ($cell) {
                    $cell->setValue('N° CC:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $r . ':G' . $r);
                $sheet->cell('C' . $r, function ($cell) {
                    $cell->setValue('0926862152');
                    $cell->setBorder('', '', 'hair', '');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H' . $r . ':O' . $r);
                $sheet->cell('H' . $r, function ($cell) {
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $r . ':Q' . $r);
                $sheet->cell('P' . $r, function ($cell) {
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $s = $r + 1;
                $sheet->cell('B' . $s, function ($cell) {
                    $cell->setValue('Cargo:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $s . ':G' . $s);
                $sheet->cell('C' . $s, function ($cell) {
                    $cell->setValue('COORDINADORA DE AUDITORÍA MÉDICA');
                    $cell->setBorder('', '', 'hair', '');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H' . $s . ':O' . $s);
                $sheet->cell('H' . $s, function ($cell) {
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $s . ':Q' . $s);
                $sheet->cell('P' . $s, function ($cell) {
                    $cell->setValue('Sello');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', '', '');
                });

                $t = $s + 1;
                $sheet->mergeCells('B' . $t . ':Q' . $t);
                $sheet->cell('B' . $t, function ($cell) {
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(17)->setAutosize(false);

            $excel->getActiveSheet()->getStyle('A:Q')->getFont()->setName('Arial')->setSize(11);
            $excel->getActiveSheet()->getStyle('D12:D1000')->getNumberFormat()->setFormatCode('@');


            $excel->getActiveSheet()->getStyle('D11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('M11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('P11')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Q11')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }
}
