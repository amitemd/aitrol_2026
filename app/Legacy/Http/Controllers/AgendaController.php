<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Cookie;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Agenda_archivo;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Obligatorio;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Hospital;
use Sis_medico\Log_Agenda;
use Sis_medico\Max_Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Doctor;
use Sis_medico\Paciente_Familia;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\Pais;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\TipoUsuario;
use Sis_medico\User;
use Sis_medico\Triaje_Telefonico;
use Sis_medico\SegurosPaciente;

date_default_timezone_set('America/Guayaquil');

class AgendaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20, 22)) == false) {
            return true;
        }
    }

    private function rol_dr()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 13, 5, 7, 20, 9, 22)) == false) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users           = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('tipo_documento', 'asc')->where('uso_sistema', '0')->get(); //3=DOCTORES
        $tipousuarios    = tipousuario::all();
        $especialidades  = Especialidad::where('estado', '1')->get();
        $id_especialidad = '4';

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $salas_hospital = Sala::where('id_hospital', 8)->get();

        return view('agenda/index', ['users' => $users, 'tipousuarios' => $tipousuarios, 'especialidades' => $especialidades, 'id_especialidad' => $id_especialidad, 'salas_hospital' => $salas_hospital]);
    }

    public function subir_archivo_validacion(Request $request, $id_agenda, $id_archivo)
    {

        $extension    = strtolower($request['archivo']->getClientOriginalExtension());
        $nuevo_nombre = "hc_ESCPRO_" . $id_agenda . "_" . $id_archivo . "." . $extension;
        $r1           = Storage::disk('hc_agenda')->put($nuevo_nombre, \File::get($request['archivo']));
        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $archivo_historico = Agenda_archivo::find($id_archivo);

            $archivo_historico->archivo         = $nuevo_nombre;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
        }
    }

    private function validatereagendar($request, $agenda)
    {
        $cantidad = 0;
        if ($agenda->proc_consul == 1) {
            if ($request->id_sala == 10 || $request->id_sala == 13 || $request->id_sala == 16) {
                //dd($agenda);
                if (date('Y-m-d H:i:s', strtotime($request['inicio'])) != $agenda->fechaini) {
                    //dd(date('Y-m-d H:i:s', strtotime($request['inicio'])), $agenda->fechaini);
                    $cantidad = Agenda::join('sala as s', 'agenda.id_sala', '=', 's.id')->where('agenda.fechaini', '>', date('Y-m-d', strtotime($request['inicio'])) . '  0:00:00')->where('agenda.fechaini', '<', date('Y-m-d', strtotime($request['inicio'])) . ' 23:59:59')->where('agenda.proc_consul', '1')->where('agenda.estado', '<>', '0')->where('s.id_hospital', '2')->get()->count();
                    //dd($cantidad);
                }
            }
        }

        $maximo_procedimientos = Max_Procedimiento::find('1')->cantidad;

        $rules = [
            'inicio' => 'max_procedimiento:' . $cantidad . ',' . $maximo_procedimientos . ',',
        ];

        $mensajes = [
            'inicio.max_procedimiento' => 'La fecha seleccionada tiene: ' . $cantidad . ' procedimientos',
        ];

        $this->validate($request, $rules, $mensajes);
        //dd($cantidad,$maximo_procedimientos);

    }

    public function reportediario(Request $request)
    {

        //return date_default_timezone_get();
        setlocale(LC_ALL, 'Spanish_Ecuador');
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->paginate(5); //3=DOCTORES

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguro = seguro::all();
        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id
              LEFT JOIN sala sa ON a.id_sala = sa.id
              LEFT JOIN hospital h ON sa.id_hospital = h.id, paciente p, seguros s, procedimiento pr

            WHERE a.id_paciente = p.id AND
            a.estado = 1 AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.proc_consul = 1 AND
            a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:00'
            ORDER BY a.fechaini ASC");

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        foreach ($agenda2 as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
            if (!is_null($historia)) {

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];
            }
        }

        $agenda = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.telefono1, p.telefono2, p.telefono3 , p.apellido2 as papellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, p.menoredad as menoredad, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.fecha_nacimiento as pfecha_nacimiento
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN sala sa ON a.id_sala = sa.id
              LEFT JOIN hospital h ON sa.id_hospital = h.id
              LEFT JOIN paciente p ON a.id_paciente = p.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN seguros s ON a.id_seguro = s.id
            WHERE a.estado = 1 AND
            (a.proc_consul = 0 OR a.proc_consul = 2)
            AND (a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59' OR a.fechafin BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59')
            ORDER BY a.id_doctor1, a.fechaini ASC");

        $total_consulta = count($agenda);

        $dp_cons               = [];
        $ControlDocController1 = new hc_admision\ControlDocController;
        foreach ($agenda as $a1) {

            $historia1 = Historiaclinica::where('id_agenda', $a1->id)->first();
            if (!is_null($historia1)) {

                $hSeguro1      = Seguro::find($historia1->id_seguro);
                $cantidad_doc1 = $ControlDocController1->carga_documentos_union($historia1->hcid, '0', $hSeguro1->tipo)->count();
                $cant_ok1      = Archivo_historico::where('id_historia', $historia1->hcid)->where('estado', '1')->get()->count();
                $cant_pend1    = $cantidad_doc1 - $cant_ok1;

                $dp_cons += [
                    $a1->id => $cant_pend1,
                ];
            }
        }

        return view('reportes/agenda-diario/index', ['procedimientos' => $agenda2, 'total_consulta' => $total_consulta, 'consultas' => $agenda, 'fecha' => $fecha, 'seguros' => $seguro, 'dp_proc' => $dp_proc, 'dp_cons' => $dp_cons]);
    }

    public function excel(Request $request)
    {

        if ($request['fecha'] == '') {
            $fecha = date('Y/m/d');
        } else {
            $fecha = $request['fecha'];
        }

        Excel::create('Reporte Diario-' . $fecha, function ($excel) use ($fecha) {

            $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, p.fecha_nacimiento as pfecha_nacimiento, p.telefono1, p.telefono2, p.telefono3, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre, pr.observacion as probservacion, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id
              LEFT JOIN sala sa ON a.id_sala = sa.id
              LEFT JOIN hospital h ON sa.id_hospital = h.id, paciente p, seguros s, procedimiento pr

            WHERE a.id_paciente = p.id AND
            a.estado = 1 AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.proc_consul = 1 AND
            a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59'
            ORDER BY a.fechaini ASC");

            $dp_proc              = [];
            $ControlDocController = new hc_admision\ControlDocController;
            foreach ($agenda2 as $a2) {

                $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
                if (!is_null($historia)) {

                    $hSeguro      = Seguro::find($historia->id_seguro);
                    $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                    $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                    $cant_pend    = $cantidad_doc - $cant_ok;

                    $dp_proc += [
                        $a2->id => $cant_pend,
                    ];
                }
            }

            $agenda = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.telefono1, p.telefono2, p.telefono3 , p.apellido2 as papellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d1.color as d1color,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, p.menoredad as menoredad, u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2, p.fecha_nacimiento as pfecha_nacimiento
            FROM agenda a
              LEFT JOIN users u ON a.id_usuarioconfirma = u.id
              LEFT JOIN sala sa ON a.id_sala = sa.id
              LEFT JOIN hospital h ON sa.id_hospital = h.id
              LEFT JOIN paciente p ON a.id_paciente = p.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN seguros s ON a.id_seguro = s.id
            WHERE a.estado = 1 AND
            (a.proc_consul = 0 OR a.proc_consul = 2)
            AND (a.fechaini BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59' OR a.fechafin BETWEEN '" . $fecha . " 00:00' AND '" . $fecha . " 23:59')
            ORDER BY a.id_doctor1, a.fechaini ASC");

            //dd($agenda);

            $total_consulta = count($agenda);

            $dp_cons               = [];
            $ControlDocController1 = new hc_admision\ControlDocController;
            foreach ($agenda as $a1) {

                $historia1 = Historiaclinica::where('id_agenda', $a1->id)->first();
                if (!is_null($historia1)) {

                    $hSeguro1      = Seguro::find($historia1->id_seguro);
                    $cantidad_doc1 = $ControlDocController1->carga_documentos_union($historia1->hcid, '0', $hSeguro1->tipo)->count();
                    $cant_ok1      = Archivo_historico::where('id_historia', $historia1->hcid)->where('estado', '1')->get()->count();
                    $cant_pend1    = $cantidad_doc1 - $cant_ok1;

                    $dp_cons += [
                        $a1->id => $cant_pend1,
                    ];
                }
            }
            $excel->sheet('Reporte Diario', function ($sheet) use ($agenda2, $agenda, $total_consulta, $fecha, $dp_proc, $dp_cons) {
                $i = 6;
                $sheet->mergeCells('A2:S2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AGENDAMIENTO DE PROCEDIMIENTOS MÉDICOS AMBULATORIOS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:S3');
                $mes = substr($fecha, 5, 2);
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
                $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);
                $sheet->cell('A3', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue($fecha2);
                    $cell->setBackground('#FFFF00');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:S4');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS MÉDICOS');
                    $cell->setBackground('#FFE4E1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1:S4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONFIRMACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E5:G5');
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MÉDICO P.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MÉDICO S.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LOCAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CORTESIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DCTOS. PEND.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELÉFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                foreach ($agenda2 as $value) {
                    //varios procedmientos
                    $masproc          = $value->probservacion;
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $masproc = $masproc . "+" . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }
                    //varios procedmientos
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $cell->setValue($value->papellido1 . ' ' . $value->papellido2);
                        } else {
                            $cell->setValue($value->papellido1);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->pnombre2 != "(N/A)") {
                            $cell->setValue($value->pnombre1 . ' ' . $value->pnombre2);
                        } else {
                            $cell->setValue($value->pnombre1);
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });

                    //calcular edad

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (!is_null($value->pfecha_nacimiento)) {
                            $fecha           = $value->pfecha_nacimiento;
                            list($Y, $m, $d) = explode("-", $fecha);
                            $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
                            $cell->setValue($edad);
                        } else {
                            $cell->setValue("");
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        if ($value->id_usuarioconfirma != null) {
                            // manipulate the cel
                            $cell->setValue($value->unombre1 . ' ' . $value->uapellido1);
                        } else {
                            if ($value->estado_cita == '0') {
                                $cell->setValue('POR CONFIRMAR');
                            } elseif ($value->estado_cita == '1') {
                                $cell->setValue('CONFIRMADO');
                            } elseif ($value->estado_cita == '2') {
                                $cell->setValue('REAGENDADO');
                            } elseif ($value->estado_cita == '3') {
                                $cell->setValue('SUSPENDIDO');
                            } elseif ($value->estado_cita == '-1') {
                                $cell->setValue('NO ASISTE');
                            } elseif ($value->estado_cita == '4') {
                                $cell->setValue('ADMISIONADO');
                            }
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });

                    $sheet->mergeCells('E' . $i . ':G' . $i);
                    $sheet->cell('E' . $i, function ($cell) use ($value, $masproc) {
                        // manipulate the cel
                        $cell->setValue($masproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_sala);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    if ($value->est_amb_hos == 0) {
                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            // manipulate the cel
                            $cell->setValue('AMBULATORIO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                    } else {
                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            // manipulate the cel
                            $j_txt = 'HOSPITALIZADO';
                            if ($value->omni == 'SI') {
                                $j_txt = 'HOSPITALIZADO/OMNI';
                            }
                            $cell->setValue($j_txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                    }

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue('Dr. ' . $value->d1nombre1 . ' ' . $value->d1apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    if ($value->id_doctor2 != "") {
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('Dr. ' . $value->d2nombre1 . ' ' . $value->d2apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                    } else {
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_seguro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cortesia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->procedencia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $dp_proc) {
                        // manipulate the cel
                        if (array_has($dp_proc, $value->id)) {
                            $cell->setValue($dp_proc[$value->id]);
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        if ($value->estado_cita == '0') {
                            $cell->setValue('POR CONFIRMAR');
                        } elseif ($value->estado_cita == '1') {
                            $cell->setValue('CONFIRMADO');
                        } elseif ($value->estado_cita == '2') {
                            $cell->setValue('REAGENDADO');
                        } elseif ($value->estado_cita == '3') {
                            $cell->setValue('SUSPENDIDO');
                        } elseif ($value->estado_cita == '4') {
                            $cell->setValue('ASISTE');
                        } elseif ($value->estado_cita == '-1') {
                            $cell->setValue('NO ASISTE');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        $telefonos = "";
                        if (!is_null($value->telefono1)) {
                            $telefonos = $value->telefono1 . "-";
                        }
                        if (!is_null($value->telefono2)) {
                            $telefonos = $telefonos . $value->telefono2 . "-";
                        }
                        if (!is_null($value->telefono3)) {
                            $telefonos = $telefonos . $value->telefono3;
                        }

                        $cell->setValue($telefonos);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($value->estado_cita != 0) {
                            $cell->setFontColor($value->color);
                            if ($value->paciente_dr == 1) {
                                $cell->setFontColor($value->d1color);
                            }
                        }
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value) {
                        $part = '';
                        if ($value->paciente_dr == '1') {
                            $part = 'PART.';
                        }
                        $cell->setValue($part);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
                $j = 0;
                $c = 0;
                $i = $i + 2;
                $sheet->mergeCells('A' . $i . ':M' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSULTAS MÉDICAS ESPECIALIZADAS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A' . $i . ':L' . $i, function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = $i + 1;
                foreach ($agenda as $value) {

                    if ($c != $value->id_doctor1 && $j == 0) {

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('DR. ' . $value->d1nombre1 . ' ' . $value->d1apellido1);
                            $cell->setBackground('#FFFF00');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $i . ':M' . $i);
                        $sheet->cells('A' . $i . ':M' . $i, function ($cells) {
                            // manipulate the range of cells
                            $cells->setAlignment('center');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i = $i + 1;
                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('APELLIDOS');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('NOMBRES');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('EDAD');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONFIRMACIÓN');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SEGURO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('HORA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TIPO CITA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('H' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TCONS.');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TELECONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SALA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('UBICACIÓN');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('STATUS');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CORTESIA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('INGRESO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('O' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DTOS. PEND.');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('P' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('PARTICULAR.');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Q' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('VIP');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('R' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TELEFONO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('S' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ULTIMA OBSERVACION');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('S' . $i . ':U' . $i);

                        $i = $i + 1;
                    }
                    if ($value->proc_consul == '0') {

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->papellido2 != "(N/A)") {
                                $cell->setValue($value->papellido1 . ' ' . $value->papellido2);
                            } else {
                                $cell->setValue($value->papellido1);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->pnombre2 != "(N/A)") {
                                $cell->setValue($value->pnombre1 . ' ' . $value->pnombre2);
                            } else {
                                $cell->setValue($value->pnombre1);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if (!is_null($value->pfecha_nacimiento)) {
                                $fecha           = $value->pfecha_nacimiento;
                                list($Y, $m, $d) = explode("-", $fecha);
                                $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
                                $cell->setValue($edad);
                            } else {
                                $cell->setValue("");
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        if ($value->estado_cita == 0) {
                            $sheet->cell('D' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue('Por Confirmar');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        } else {
                            $sheet->cell('D' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue(substr($value->unombre1, 0, 1) . ' ' . $value->uapellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        }
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->consultorio == '1') {
                                $cell->setValue($value->nombre_seguro . ' CONSULTORIO');
                            } else {
                                $cell->setValue($value->nombre_seguro);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 11, 5));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        if ($value->tipo_cita == 0) {
                            $sheet->cell('G' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue('PRIMERA VEZ');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        } else {
                            $sheet->cell('G' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue('CONSECUTIVO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        }

                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue($value->telefono1);
                            $tcons = 'NO';
                            if ($value->tc) {
                                $tcons = 'SI';
                            }
                            $cell->setValue($tcons);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue($value->telefono1);
                            $cell->setValue($value->teleconsulta);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nombre_sala);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nombre_hospital);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            if ($value->estado_cita == '0') {
                                $cell->setValue('POR CONFIRMAR');
                            } elseif ($value->estado_cita == '1') {
                                $cell->setValue('CONFIRMADO');
                            } elseif ($value->estado_cita == '2') {
                                $cell->setValue('REAGENDADO');
                            } elseif ($value->estado_cita == '3') {
                                $cell->setValue('SUSPENDIDO');
                            } elseif ($value->estado_cita == '4') {
                                $cell->setValue('ASISTE');
                            } elseif ($value->estado_cita == '-1') {
                                $cell->setValue('NO ASISTE');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cortesia);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        if ($value->est_amb_hos == 0) {
                            $sheet->cell('N' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $cell->setValue('AMBULATORIO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        } else {
                            $sheet->cell('N' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $j_txt = 'HOSPITALIZADO';
                                if ($value->omni == 'SI') {
                                    $j_txt = 'HOSPITALIZADO/OMNI';
                                }
                                $cell->setValue($j_txt);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        }
                        $sheet->cell('O' . $i, function ($cell) use ($value, $dp_cons) {
                            // manipulate the cel
                            if (array_has($dp_cons, $value->id)) {
                                $cell->setValue($dp_cons[$value->id]);
                            } else {
                                $cell->setValue(0);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($value->estado_cita != 0) {
                                $cell->setFontColor($value->color);
                                if ($value->paciente_dr == 1) {
                                    $cell->setFontColor($value->d1color);
                                }
                            }
                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value, $dp_cons) {
                            // manipulate the cel
                            $vip = '';
                            if ($value->vip == '1') {
                                $vip = 'VIP';
                            }
                            $cell->setValue($vip);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value, $dp_cons) {
                            // manipulate the cel

                            $part = '';
                            if ($value->paciente_dr == '1') {
                                $part = 'PART.';
                            }
                            $cell->setValue($part);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        if ($value->menoredad == 1) {
                            $sheet->cell('R' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                //$cell->setValue($value->telefono1);
                                $telefonos = "";
                                if ($value->tc) {
                                    $telefonos = $value->teleconsulta . '-';
                                }
                                if (!is_null($value->telefono1)) {
                                    $telefonos = $value->telefono1 . "-";
                                }
                                if (!is_null($value->telefono2)) {
                                    $telefonos = $telefonos . $value->telefono2 . "-";
                                }
                                if (!is_null($value->telefono3)) {
                                    $telefonos = $telefonos . $value->telefono3;
                                }

                                $cell->setValue($telefonos);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        } else {
                            $sheet->cell('R' . $i, function ($cell) use ($value) {

                                // manipulate the cel
                                $telefonos = '';
                                $telefonos = $telefonos . $value->telefono1;
                                $cell->setValue($telefonos);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if ($value->estado_cita != 0) {
                                    $cell->setFontColor($value->color);
                                    if ($value->paciente_dr == 1) {
                                        $cell->setFontColor($value->d1color);
                                    }
                                }
                            });
                        }

                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->observaciones);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('S' . $i . ':U' . $i);
                    } else {
                        $sheet->mergeCells('A' . $i . ':M' . $i);
                        $sheet->cell('A' . $i, function ($cell) use ($value, $dp_cons) {
                            $procedencia = '';
                            if ($value->procedencia == '') {
                                $procedencia = 'Reuniones';
                            } else {
                                $procedencia = $value->procedencia;
                            }
                            $cell->setValue($procedencia . ":      Desde: " . $value->fechaini . "      hasta: " . $value->fechafin . "       Observaciones: " . $value->observaciones);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setBackground('#99bbff');
                        });
                    }
                    $i = $i + 1;
                    $j = $j + 1;
                    $c = $value->id_doctor1;
                    if (($j < $total_consulta) && ($agenda[$j]->id_doctor1 != $c)) {
                        $sheet->cell('A' . $i, function ($cell) use ($agenda, $j) {
                            // manipulate the cel
                            $cell->setValue('DR. ' . $agenda[$j]->d1nombre1 . ' ' . $agenda[$j]->d1apellido1);
                            $cell->setBackground('#FFFF00');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A' . $i . ':M' . $i);
                        $sheet->cells('A' . $i . ':M' . $i, function ($cells) {
                            // manipulate the range of cells
                            $cells->setAlignment('center');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i = $i + 1;
                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('APELLIDOS');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('NOMBRES');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('EDAD');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONFIRMACION');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SEGURO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('HORA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TIPO CITA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('H' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TCONS.');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TELECONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SALA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('UBICACION');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('STATUS');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CORTESIA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('INGRESO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('O' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DTOS. PEND.');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('P' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('PARTICULAR');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Q' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('VIP');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('R' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TELEFONO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('S' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ULTIMA OBSERVACION');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('S' . $i . ':U' . $i);

                        $i = $i + 1;
                    }
                }
                $sheet->cells('A1:S' . $i, function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
            });
        })->export('xlsx');
    }

    public function agenda($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = User::find($id);

        $fecha      = date('Y-m-j');
        $fecha_1    = date('Y-m-d');
        $nuevafecha = strtotime('-2 week', strtotime($fecha));
        $bfecha     = date('Y-m-j', $nuevafecha);
        $agenda     = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 1)
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();

        $agenda3 = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 0)
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();

        $agenda2 = Agenda::where('proc_consul', '=', 2)
            ->select('agenda.*')
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['agenda.id_doctor1', '=', $id]])
                    ->orWhere([['agenda.id_doctor2', '=', $id]])
                    ->orWhere([['agenda.id_doctor3', '=', $id]]);
            })
            ->get();

        $agenda_sus = null;
        $horario    = DB::table('horario_doctor')
            ->where('id_doctor', '=', $id)->orderBy('ndia')
            ->orderBy('hora_ini')
            ->get();


        //horas extras aceptadas
        $aceptadas_extra = Excepcion_Horario::where('id_doctor1', '=', $id)->where('inicio', '>=', $bfecha)->get();

        return view('agenda/calendario', ['users' => $user, 'id' => $id, 'doctor' => $doctor, 'agenda' => $agenda, 'agenda2' => $agenda2, 'agenda3' => $agenda3, 'versuspendidas' => '0', 'agenda_sus' => $agenda_sus, 'fecha' => '0', 'horario' => $horario, 'extra' => $aceptadas_extra]);
    }

    public function agenda2($id, $hora)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }
        $horario = DB::table('horario_doctor')
            ->where('id_doctor', '=', $id)
            ->orderBy('ndia')
            ->orderBy('hora_ini')
            ->get();
        $doctor = User::find($id);

        date_default_timezone_set('UTC');
        $fecha  = substr($hora, 0, 10);
        $fecha2 = date('Y/m/d H:i', $fecha);
        $fecha_1 = date('Y-m-d', $fecha);

        $nuevafecha = strtotime('-1 month', strtotime($fecha2));
        $bfecha     = date('Y-m-j', $nuevafecha);
        date_default_timezone_set('America/Guayaquil');

        $agenda = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 1)
            ->where('estado', 1)
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')

            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();
        $agenda3 = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 0)
            ->where('estado', 1)
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();
        $agenda2 = Agenda::where('proc_consul', '=', 2)
            ->select('agenda.*')
            ->where('estado', 1)
            ->where('fechaini', '>', $fecha_1 . ' 00:00:00')
            ->where('fechafin', '<', $fecha_1 . ' 23:59:59')
            ->where('fechaini', '>=', $bfecha)
            ->where(function ($query) use ($id) {
                $query->where([['agenda.id_doctor1', '=', $id]])
                    ->orWhere([['agenda.id_doctor2', '=', $id]])
                    ->orWhere([['agenda.id_doctor3', '=', $id]]);
            })
            ->get();

        $agenda_sus = null;

        if ($hora != "") {
            $hora = $hora;
        } else {
            $hora = 0;
        }
        //horas extras aceptadas
        $aceptadas_extra = Excepcion_Horario::where('id_doctor1', '=', $id)->get();

        return view('agenda/calendario', ['users' => $user, 'id' => $id, 'doctor' => $doctor, 'agenda' => $agenda, 'agenda2' => $agenda2, 'agenda3' => $agenda3, 'versuspendidas' => '0', 'agenda_sus' => $agenda_sus, 'fecha' => $hora, 'horario' => $horario, 'extra' => $aceptadas_extra]);
    }

    public function buscador_json(Request $request)
    {
        $fpermiso = '0';
        $id_user_ses = Auth::user()->id;
        $permiso = Agenda_Permiso::where('id_usuario', $id_user_ses)->where('estado', '1')->first();
        if (!is_null($permiso)) {
            $fpermiso = '1';
        }
        $id = $request['id_doctor'];
        $fecha = $request['fecha'];
        $semana = $request['semana'];


        if ($semana == 1) {
            $array = array();
            return  json_encode($array);
        }
        $diaSemana = date("w", strtotime($fecha)) - 1;

        # Calcular el tiempo (no la fecha) de cuándo fue el inicio de semana
        $tiempoDeInicioDeSemana = strtotime("-" . $diaSemana . " days", strtotime($fecha)); # Restamos -X days
        # Y formateamos ese tiempo
        $fechaInicioSemana = date("Y-m-d", $tiempoDeInicioDeSemana);
        # Ahora para el fin, sumamos
        $tiempoDeFinDeSemana = strtotime("+6 days", $tiempoDeInicioDeSemana); # Sumamos +X days, pero partiendo del tiempo de inicio
        # Y formateamos
        $fechaFinSemana = date("Y-m-d", $tiempoDeFinDeSemana);
        $fecha_d = date("Y-m-d", strtotime($fecha));


        $agenda     = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 1)
            ->where('fechaini', '>', $fechaInicioSemana . ' 00:00:00')
            ->where('fechafin', '<', $fechaFinSemana . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();

        $agenda3 = Agenda::select('agenda.*')
            ->where('proc_consul', '=', 0)
            ->where('fechaini', '>', $fechaInicioSemana . ' 00:00:00')
            ->where('fechafin', '<', $fechaFinSemana . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id]])
                    ->orWhere([['id_doctor2', '=', $id]])
                    ->orWhere([['id_doctor3', '=', $id]]);
            })
            ->get();

        $fecha_valida = date("Y-m-d H:i");
        $agenda2 = Agenda::where('proc_consul', '=', 2)
            ->select('agenda.*')
            ->where('fechaini', '>', $fechaInicioSemana . ' 00:00:00')
            ->where('fechafin', '<', $fechaFinSemana . ' 23:59:59')
            ->where('agenda.estado', '1')
            ->where(function ($query) use ($id) {
                $query->where([['agenda.id_doctor1', '=', $id]])
                    ->orWhere([['agenda.id_doctor2', '=', $id]])
                    ->orWhere([['agenda.id_doctor3', '=', $id]]);
            })
            ->get();
        $enviar = "[";
        $lastagenda3 = $agenda3->count();
        $lastagenda3--;
        $lastagenda2 = $agenda2->count();
        $lastagenda2--;
        $lastagenda = $agenda->count();
        $lastagenda--;
        $versuspendidas = 0;
        $age_1 = 0;
        $age_2 = 0;
        foreach ($agenda3  as $llave => $value) {
            $age_1 = 1;
            $atendido = 0;
            $receta_ok = 0;
            $hc_seguro_nombre = '';
            $hc_color = '';
            $hc_procedimientos = array();
            $hc = Historiaclinica::where('id_agenda', $value->id)->first();
            if (!is_null($hc)) {
                $hc_procedimientos = hc_procedimientos::where('id_hc', $hc->hcid)->first();
            }
            if (!is_null($hc)) {
                $cie10 = Hc_Cie10::where('hcid', $hc->hcid)->first();
                if (!is_null($cie10)) {
                    $atendido = 1;
                }
                $receta = hc_receta::where('id_hc', $hc->hcid)->first();
                if (!is_null($receta)) {
                    if ($receta->rp != null && $receta->prescripcion != null) {
                        $receta_ok = 1;
                    }
                }
                if (!is_null($hc->seguro)) {
                    $hc_seguro_nombre = $hc->seguro->nombre;
                    $hc_color = $hc->seguro->color;
                }
            }
            $enviar = $enviar . "{
                'id':" . $value->id . ",
                'className': 'a" . $value->id . "',";
            if ($id == '4444444444' || $id == '444444444A' || $id == '444444444B') {
                $enviar .= "
                'title': '" . $value->paciente->apellido1 . ' ';
                if ($value->paciente->apellido2 != '(N/A)') {
                    $enviar .= $value->paciente->apellido2 . ' ';
                }
                $enviar .= $value->paciente->nombre1 . ' ';
                if ($value->paciente->nombre2 != "(N/A)") {
                    $enviar .= $value->paciente->nombre2 . ' ';
                }
                $enviar .= ' | TOMA DE MUESTRA ';
                if ($value->estado_cita == 4) {
                    $enviar .= 'REALIZADO ';
                }
                $enviar .= " | Agenda: " . $value->user_crea->apellido1 . ' | Modif: ' . $value->user_mod->apellido1 . ' | Seguro: ' . $value->seguro->nombre . "',";
            } else {
                $enviar .= "'title' : '" . $value->paciente->apellido1 . ' ';
                if ($value->paciente->apellido2 != "(N/A)") {
                    $enviar .= $value->paciente->apellido2 . ' ';
                }
                $enviar .= $value->paciente->nombre1 . " ";
                if ($value->paciente->nombre2 != "(N/A)") {
                    $enviar .= $value->paciente->nombre2 . ' ';
                }
                if ($value->paciente_dr == "1") {
                    $enviar .= "/PART ";
                }
                $enviar .= "| CONSULTA ";
                if ($value->vip == "1") {
                    $enviar .= "/VIP";
                }
                $enviar .= " | ";
                if ($value->estado_cita == 0) {
                    $enviar .= "Por Confirmar";
                }
                if ($value->estado_cita == 1) {
                    $enviar .= "Confirmada";
                }
                if ($value->estado_cita == 2) {
                    if ($value->estado == 1) {
                        $enviar .= "Completar Datos";
                    } else {
                        $enviar .= "Por Reagendar";
                    }
                }
                if ($value->estado_cita == 4) {
                    $enviar .= "ASISTIÓ";
                }
                $enviar .= " | ";
                if ($value->tipo_cita == 0) {
                    $enviar .= "Primera vez";
                }
                if ($value->tipo_cita == 1) {
                    $enviar .= "Consecutivo";
                }
                $enviar .= " | Agenda: " . $value->user_crea->apellido1 . " | Modif: " . $value->user_mod->apellido1 . " | Seguro: ";
                if ($value->estado_cita != '4') {
                    $enviar .= $value->seguro->nombre;
                } else {
                    $enviar .= $hc_seguro_nombre;
                }
                $enviar .= " | Cort.: " . $value->cortesia;
                if ($value->omni == 'SI') {
                    $enviar .= " | OMNI ";
                }
                if ($value->ciudad != null) {
                    $enviar .= " | Ciu.Proc: " . $value->paciente->ciudad;
                }
                if (!empty($hc_procedimientos->hora_fin)) {
                    $enviar .= " |**ATENDIDO** | ";
                }
                if ($receta_ok) {
                    $enviar .= "|RECETA OK ";
                }
                $enviar .= " |Teleconsulta: ";
                if ($value->tc == '1') {
                    $enviar .= "SI";
                } else {
                    $enviar .= "NO";
                }
                $enviar .= "',";
            }
            $enviar .= "'start' : '" . $value->fechaini . "',
                'end' : '" . $value->fechafin . "',";
            if ($fpermiso) {
                $enviar .= "'editable': true,";
            } else {
                if ($value->espid == '8') {
                    $enviar .= "'editable': true,";
                } else {
                    $enviar .= "'editable': false,";
                }
            }
            $enviar .= "'url': '" . route('agenda.edit2', ['id' => $value->id, 'doctor' => $id]) . "',";
            if ($value->paciente_dr == 0) {
                if ($value->estado_cita == 0) {
                    $enviar .= "'color': 'black'";
                } else {
                    if ($hc_color != "") {
                        $enviar .= "'color': '" . $hc_color . "',";
                    } else {
                        $enviar .= "'color': '" . $value->seguro->color . "',";
                    }
                    $enviar .= "'textColor': 'black'";
                }
            }
            if ($value->paciente_dr == 1) {
                if ($value->estado_cita == 0) {
                    $enviar .= "'color': 'black'";
                } else {
                    $enviar .= "'color': 'red',
                    'textColor': 'black'";
                }
            }

            if ($value->vip == 1) {
                //$enviar .= "'color': '#ff3300'";
            }

            if ($llave == $lastagenda3) {
                $enviar .= "}";
            } else {
                $enviar .= "},";
            }
        }

        foreach ($agenda2 as $llave => $value) {
            $age_2 = 1;
            $sala = Sala::find($value->id_sala);
            if ($age_1 == 1 && $llave == 0) {
                $enviar .= ',';
            }
            $enviar .=   "{ 
                            'id'    : '" . $value->id . " idreunion',
                        'className': 'classreunion','title' : '" . $value->procedencia . " - " . str_replace(array("\r\n", "\r"), " ", $value->observaciones) . " | " . $sala->nombre_sala . " / " . Hospital::find($sala->id_hospital)->nombre_hospital . " | Agendado: " . substr($value->user_crea->nombre1, 0, 1) . $value->user_crea->apellido1 . " | Modificado: " . substr($value->user_mod->nombre1, 0, 1) . $value->user_mod->apellido1 . " ',";
            $enviar .=   "'start' : '" . $value->fechaini . "', 'end' : '" . $value->fechafin . "',";
            $enviar .=   "'color': '#023f84', 'url': '" . route('reunion.edit2', ['id' => $value->id]) . "'";

            if ($value->fechaini >= $fecha_valida) {
                $enviar .= ",'editable': false";
            }

            if ($llave == $lastagenda2) {
                $enviar .= "}";
            } else {
                $enviar .= "},";
            }
        }

        foreach ($agenda as $llave => $value) {
            $agendaprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
            $varhospital = Sala::find($value->id_sala)->hospital->id;
            $hc = Historiaclinica::where('id_agenda', $value->id)->first();
            $hc_seguro_nombre = '';
            if (!is_null($hc)) {
                if (!is_null($hc->seguro)) {
                    $hc_seguro_nombre = $hc->seguro->nombre;
                }
            }
            if ($hc_seguro_nombre == '') {
                $hc_seguro_nombre = $value->seguro->nombre;
            }
            if (($age_1 == 1 || $age_2 == 1) && $llave == 0) {
                $enviar .= ',';
            }

            $enviar .=   "{'id'    : '" . $value->id . "', 'className': 'a" . $value->id . "',";
            $enviar .=   "'title' : '" . $value->paciente->apellido1 . " ";
            if ($value->paciente->apellido2 != "(N/A)") {
                $enviar .= $value->paciente->apellido2 . " ";
            }
            $enviar .= $value->paciente->nombre1;
            if ($value->paciente->nombre2 != "(N/A)") {
                $enviar .= $value->paciente->nombre2;
            }
            if ($value->paciente_dr == "1") {
                $enviar .= " /PART";
            }
            $enviar .= " | PROC";
            if ($value->vip == "1") {
                $enviar .= " /VIP ";
            }
            $enviar .= ": " . $value->procedimiento->nombre;
            if (!$agendaprocedimientos->isEmpty()) {
                foreach ($agendaprocedimientos as $agendaproc) {
                    $enviar .= "+" . Procedimiento::find($agendaproc->id_procedimiento)->nombre;
                }
            }
            $enviar .= " | ";
            if ($versuspendidas == 0) {
                if ($value->estado_cita == 0) {
                    $enviar .= " Estado:  Por Confirmar ";
                } elseif ($value->estado_cita == 1) {
                    $enviar .= " Estado:  Confirmada ";
                } elseif ($value->estado_cita == 2) {
                    if ($value->estado == 1) {
                        $enviar .= " Estado:  Completar Datos ";
                    } else {
                        $enviar .= " Estado:  Por Reagendar ";
                    }
                } elseif ($value->estado_cita == 4) {
                    $enviar .= " Estado:  ASISTIÓ ";
                }
                $enviar .= " | ";
            }
            $enviar .= "Agendado: " . $value->user_crea->apellido1 . " | Modificado: " . $value->user_mod->apellido1 . " | Seguro: ";
            if ($value->estado_cita != 4) {
                $enviar .= $value->seguro->nombre;
            } else {
                $enviar .= $hc_seguro_nombre;
            }
            $enviar .= " | Cortesia: " . $value->cortesia;
            if ($value->omni == 'SI') {
                $enviar .= " | OMNI ";
            }
            if ($value->paciente->ciudad != null) {
                $enviar .= " |Ciudad Proc: " . $value->paciente->ciudad;
            }
            $enviar .= " |Teleconsulta: ";
            if ($value->tc == '1') {
                $enviar .= "SI";
            } else {
                $enviar .= "NO";
            }
            $enviar .= "', 'start' : '" . $value->fechaini . "', 'end' : '" . $value->fechafin . "',";
            if ($varhospital == 2) {
                $enviar .= "'editable': false,";
            } else {
                $enviar .= "'editable': true,";
            }
            $enviar .= "'url': '" . route('agenda.edit2', ['id' => $value->id, 'doctor' => $id]) . "',";
            if ($value->paciente_dr == 0) {
                if ($value->estado_cita == 0) {
                    $enviar .= "'color': 'black'";
                } else {
                    $enviar .= "'color': '" . $value->seguro->color . "', 'textColor': 'black'";
                }
            }
            if ($value->paciente_dr == 1) {
                if ($value->estado_cita == 0) {
                    $enviar .= "'color': 'black'";
                } else {
                    $enviar .= "'color': 'red'";
                }
            }
            if ($value->vip == 1) {
                //$enviar .= "'color': '#ff3300'";
            }
            if ($llave == $lastagenda) {
                $enviar .= "}";
            } else {
                $enviar .= "},";
            }
        }
        $enviar = $enviar . "]";
        return str_replace("'", '"', $enviar);
    }

    public function nuevo($id, $fecha, $i)
    {

        //dd('hola');
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = User::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')
            ->orderBy('hospital.nombre_hospital')
            ->orderBy('sala.nombre_sala')
            ->where('sala.estado', '1')
            ->get();

        $paciente = Paciente::find($i);

        //dd($paciente);

        //SI NO SE ENCUENTRA EL PACIENTE
        if ($paciente == array() && $i != '0') {

            return redirect()->route('agenda.paciente', ['id' => $id, 'i' => $i, 'fecha' => $fecha, 'sala' => '0']);
        }

        $cortesia_paciente = Cortesia_paciente::find($i);

        $user      = DB::table('users')->where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('nombre1')->get(); //3=DOCTORES;
        $enfermero = DB::table('users')->where('id_tipo_usuario', 6)->where('estado', 1)->orderBy('nombre1')->get(); //6=ENFERMEROS;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $procedimiento = Procedimiento::all();
        $empresa       = Empresa::where('estado', '1')->orderBy('nombrecomercial', 'Desc')->where('admision', '1')->get();
        $seguro        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();
        $especialidad  = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();

        date_default_timezone_set('UTC');

        $fecha  = substr($fecha, 0, 10);
        $fecha2 = date('Y/m/d H:i', $fecha);
        $n_dia  = date('N', $fecha);
        $hora   = date('H:i', $fecha);
        $hora   = date('H:i', strtotime('+1 minute', strtotime($hora)));

        $tipo_horario = DB::select("SELECT tipo
                                    FROM horario_doctor
                                    Where id_doctor = '" . $id . "' AND
                                    ndia = '" . $n_dia . "' AND
                                    '" . $hora . "' BETWEEN hora_ini AND hora_fin ; ");

        //dd($id,$n_dia,$hora,$tipo_horario);

        if ($tipo_horario != array()) {
            $tipo_horario = $tipo_horario[0]->tipo;
        } else {

            //$tipo_horario = 0;
            $tipo_horario2 = DB::select("SELECT tipo
                                    FROM excepcion_horario
                                    Where id_doctor1 = '" . $id . "' AND
                                    '" . $fecha2 . "' BETWEEN inicio AND fin ; ");

            if ($tipo_horario2 != array()) {

                $tipo_horario = $tipo_horario2[0]->tipo;
            } else {

                $tipo_horario = -1;
            }
        }

        $citas = array();
        if (!is_null($paciente)) {

            $citas = $this->busca_citasxpaciente_dia_mes($fecha2, $paciente->id);
        }

        $observacion_admin = Paciente_Observaciones::where('id_paciente', $i)->first();
        //$doctor_todo = Sis_medico\Doctor_Tiempo::where('id_doctor',$doctor->id)->first();
        //dd($observacion_admin);

        $observaciones_admin = "";
        // dd($observaciones_admin);
        if ($observacion_admin == null) {
            $observaciones_admin = "";
        } else {
            $observaciones_admin = $observacion_admin->observacion;
        }

        return view('agenda/agregar', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'procedimiento2' => $procedimiento, 'i' => $i, 'especialidad' => $especialidad, 'empresa' => $empresa, 'enfermero' => $enfermero, 'seguro' => $seguro, 'hora' => $fecha2, 'unix' => $fecha, 'cortesia_paciente' => $cortesia_paciente, 'citas' => $citas, 'tipo_horario' => $tipo_horario, 'observaciones_admin' => $observaciones_admin]);
    }

    public function agenda4()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $agenda2 = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d3.nombre1 as d3nombre1, d3.nombre2 as d3nombre2, d3.apellido1 as d3apellido1, d3.apellido2 as d3apellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, d2.nombre1 as d2nombre1, d2.nombre2 as d2nombre2, d2.apellido1 as d2apellido1, d2.apellido2 as d2apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, pr.nombre as prnombre,u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuariocrea = u.id
              LEFT JOIN users d1 ON a.id_doctor1 = d1.id
              LEFT JOIN users d2 ON a.id_doctor2 = d2.id
              LEFT JOIN users d3 ON a.id_doctor3 = d3.id, paciente p, seguros s, sala sa, hospital h, procedimiento pr
            WHERE a.id_paciente = p.id AND
            a.estado = 1 AND
            a.id_seguro = s.id AND
            a.id_procedimiento = pr.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            a.proc_consul = 1 AND
            a.id_usuariocrea = u.id
            ORDER BY a.fechaini ASC");

        $agenda = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2,
            s.color as color, s.nombre as nombre_seguro, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital, p.menoredad as menoredad, p.telefono1 as telefono1, p.telefono3 as telefono3,u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM agenda a
              LEFT JOIN users u ON a.id_usuariocrea = u.id, paciente p, users d1, seguros s, sala sa, hospital h
            WHERE a.id_paciente = p.id AND
            a.id_doctor1 = d1.id AND
            a.id_seguro = s.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            a.estado = 1 AND
            a.proc_consul = 0
            ORDER BY a.id_doctor1, a.fechaini ASC");

        $agenda3 = DB::select("SELECT a.*, d1.nombre1 as d1nombre1, d1.nombre2 as d1nombre2, d1.apellido1 as d1apellido1, d1.apellido2 as d1apellido2, sa.nombre_sala as nombre_sala, h.nombre_hospital as nombre_hospital,u.nombre1 as unombre1, u.nombre2 as unombre2, u.apellido1 as uapellido1, u.apellido2 as uapellido2
            FROM   agenda a
                LEFT JOIN users u ON a.id_usuariocrea = u.id, users d1, sala sa, hospital h
            WHERE a.id_doctor1 = d1.id AND
            a.id_sala = sa.id AND
            sa.id_hospital = h.id AND
            a.estado = 1 AND
            a.proc_consul = 2
            ORDER BY a.id_doctor1, a.fechaini ASC");

        //el anterior

        return view('agenda/todos', ['agenda' => $agenda, 'agenda2' => $agenda2, 'agenda3' => $agenda3]);
    }

    public function perfil()
    {
        $id         = Auth::user()->id;
        $rolusuario = Auth::user()->id_tipo_usuario;
        $user       = User::find($id);
        // Redirect to user list if updating user wasn't existed

        $especialidades = especialidad::all();
        $especialidad   = DB::table('user_espe')->where('usuid', '=', $id)->get();
        $paises         = pais::all();
        $tipousuarios   = tipousuario::all();

        if ($rolusuario == 2) {
            return view('users-mgmt/edit_paciente', ['user' => $user])->with('paises', $paises)->with('tipousuarios', $tipousuarios)->with('rolusuario', $rolusuario)->with('id', $id);
        }
        return view('users-mgmt/edit', ['user' => $user])->with('paises', $paises)->with('tipousuarios', $tipousuarios)->with('rolusuario', $rolusuario)->with('especialidad', $especialidad)->with('especialidades', $especialidades)->with('id', $id);
    }

    public function search(Request $request)
    {

        $id_especialidad = $request['id_especialidad'];

        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellido'],
        ];

        $constraints2 = [
            'apellido2' => $request['apellido'],
        ];

        $apellido2 = "";
        $apellidos = explode(" ", $request['apellido']);

        $apellido1 = $apellidos[0];

        if (count($apellidos) > 1) {
            $apellido2 = $apellidos[1];
        }
        if ($apellido1 == "") {
            $apellido1 = "";
        }

        $users = DB::table('users as u')->join('user_espe as ue', 'ue.usuid', 'u.id')->where(function ($query) use ($request, $apellido1) {
            $query->Where('u.apellido1', 'like', '%' . $apellido1 . '%')
                ->orWhere('u.apellido2', 'like', '%' . $apellido1 . '%');
        })
            ->where(function ($query) use ($request, $apellido2) {
                $query->Where('u.apellido1', 'like', '%' . $apellido2 . '%')
                    ->orWhere('u.apellido2', 'like', '%' . $apellido2 . '%');
            })
            ->where('u.id', 'LIKE', '%' . $request['id'] . '%');
        //->where('id', '!=', '9999999999');

        $users = $users->select('u.*', 'ue.espid')->where('u.id_tipo_usuario', '3');

        if ($id_especialidad != null) {
            $users = $users->where('ue.espid', $id_especialidad);
        }

        $users = $users->where('estado', '1')->paginate(15);

        $tipousuarios = tipousuario::all();

        $especialidades = Especialidad::where('estado', '1')->get();

        return view('agenda/index', ['users' => $users, 'searchingVals' => $constraints, 'tipousuarios' => $tipousuarios, 'especialidades' => $especialidades, 'id_especialidad' => $id_especialidad]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query   = User::query();
        $fields  = array_keys($constraints);
        $fields2 = array_keys($constraints2);
        $index   = 0;
        $index2  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                $query = $query->where('id_tipo_usuario', '=', '3');
                $index++;
            }
            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint2 != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
                $query = $query->where('id_tipo_usuario', '=', '3');
                $index++;
            }

            $index++;
        }
        $query = $query->where('id_tipo_usuario', '=', '3')->orderBy('tipo_documento', 'asc');
        return $query;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createconsulta($id, $i)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $doctor = User::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();
        $procedimiento = Procedimiento::all();

        $paciente = Paciente::find($i);

        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        return view('agenda/create', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'i' => $i]);
    }

    public function paciente($id, $i, $fecha, $sala)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguros        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();
        $user    = DB::table('users')->where([['id', '=', $i]])->get(); //3=DOCTORES;

        // Redirect to user list if updating user wasn't existed

        if ($user == null || count($user) == 0) {
            $user = array();
        }

        $pais = pais::all();

        return view('agenda/paciente', ['user' => $user, 'seguros' => $seguros, 'pais' => $pais, 'id' => $id, 'i' => $i, 'fecha' => $fecha, 'sala' => $sala]);
    }
    public function existe_usuario($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = User::find($id); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed

        if (!is_null($user)) {
            return $user;
        }

        return "no";
    }

    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = $request['id'];
        $user    = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if ($request['parentesco'] != "Principal") {

            $this->validateprincipal($request);
        }

        $cv                    = $request['validacion_cv_msp'];
        $nc                    = $request['validacion_nc_msp'];
        $sec                   = $request['validacion_sec_msp'];
        $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

        $origen2 = '';
        $otro    = '';
        if ($request['origen'] == "MEDIO IMPRESO") {
            $origen2 = $request['origen_impreso'];
            $otro    = $request['impreso_otros'];
        } else if ($request['origen'] == "MEDIO DIGITAL") {
            $origen2 = $request['origen_digital'];
            $otro    = $request['digital_otros'];
        }

        if (!is_null($user)) {

            $this->validateInput2($request);
            $user->update([
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_pais'          => $request['id_pais'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'email'            => $request['email'],
            ]);
        } else {
            $this->validateInput($request);
            User::create([
                'id'               => $request['id'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_pais'          => $request['id_pais'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'id_tipo_usuario'  => 2,
                'email'            => $request['email'],
                'password'         => bcrypt($request['id']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ]);
        }
        //return $request->all();

        paciente::create([
            'id'                    => $request['id2'],
            'nombre1'               => strtoupper($request['nombre12']),
            'nombre2'               => strtoupper($request['nombre22']),
            'apellido1'             => strtoupper($request['apellido12']),
            'apellido2'             => strtoupper($request['apellido22']),
            'telefono1'             => $request['telefono12'],
            'telefono2'             => $request['telefono22'],
            'nombre1familiar'       => strtoupper($request['nombre1']),
            'nombre2familiar'       => strtoupper($request['nombre2']),
            'apellido1familiar'     => strtoupper($request['apellido1']),
            'apellido2familiar'     => strtoupper($request['apellido2']),
            'parentesco'            => $request['parentesco'],
            'parentescofamiliar'    => $request['parentesco'],
            'id_pais'               => $request['id_pais2'],
            'fecha_val'             => $request['fecha_val'],
            'cod_val'               => $request['cod_val'],
            'validacion_cv_msp'     => $request['validacion_cv_msp'],
            'validacion_nc_msp'     => $request['validacion_nc_msp'],
            'validacion_sec_msp'    => $request['validacion_sec_msp'],
            'codigo_validacion_msp' => $codigo_validacion_msp,
            'fecha_nacimiento'      => $request['fecha_nacimiento2'],
            'telefono3'             => $request['telefono2'],
            'tipo_documento'        => 1,
            'imagen_url'            => ' ',
            'menoredad'             => $request['menoredad'],
            'id_seguro'             => $request['id_seguro'],
            'id_usuario'            => $request['id'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'origen'                => $request['origen'],
            'origen2'               => $origen2,
            'otro'                  => $otro,
            'referido'              => $request['referido'],
            'mail_opcional'         => $request['email2'],
            'papa_mama'             => $request['papa_mama'],
        ]);

        if ($request->id3 != null) {
            Paciente_Familia::create([
                'id_paciente'     => $request->id2,
                'cedula_fam'      => $request->id3,
                'papa_mama'       => $request->papa_mama2,
                'apellido1'       => strtoupper($request->apellido1_3),
                'apellido2'       => strtoupper($request->apellido2_3),
                'nombre1'         => strtoupper($request->nombre1_3),
                'nombre2'         => strtoupper($request->nombre2_3),
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        if ($request['doctor'] == "0") {
            $paciente = DB::table('paciente')->where('id', '!=', '9999999999')
                ->paginate(10);

            return view('paciente/index', ['paciente' => $paciente]);
        } elseif ($request['doctor'] == "1") {
            return redirect()->route('preagenda.nuevo2', ['fecha' => $request['fecha'], 'i' => $request['id2'], 'sala' => $request['sala']]);
        } else {
            return redirect()->route('agenda.nuevo2', ['id' => $request['doctor'], 'fecha' => $request['fecha'], 'i' => $request['id2']]);
        }
    }

    private function validateprincipal($request)
    {

        $rules = [
            'id2' => 'different:id',
        ];

        $messages = [
            'id2.different' => 'Cédula es la misma que la del principal.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [
            'parentesco'         => 'required',
            'id'                 => 'required|max:10|unique:users',
            'nombre1'            => 'required|max:60',
            'nombre2'            => 'nullable|max:60',
            'apellido1'          => 'required|max:60',
            'apellido2'          => 'nullable|max:60',
            'telefono1'          => 'required|max:50',
            'telefono2'          => 'required|max:50',
            'id_pais'            => 'required',
            'fecha_val.required' => 'Agrega la fecha de validación.',
            'cod_val.required'   => 'Agrega el código de validación.',
            'fecha_nacimiento'   => 'required|date|edad_fecha',
            'email'              => 'required|email|max:191|unique:users',
            'id_seguro'          => 'required',
            'id2'                => 'required|max:10|unique:paciente,id',
            'nombre12'           => 'required|max:60',
            'nombre22'           => 'nullable|max:60',
            'apellido12'         => 'required|max:60',
            'apellido22'         => 'nullable|max:60',
            'telefono12'         => 'required|max:50',
            'telefono22'         => 'required|max:50',
            'id_pais2'           => 'required',
            'fecha_nacimiento2'  => 'required|date',
        ];
        if ($request['parentesco'] == "Principal") {
            $rules = array_add($rules, 'menoredad', 'in:0');
        }
        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre22.required'          => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'id2.required'               => 'Agrega la cédula.',
            'id2.max'                    => 'La cédula no puede ser mayor a :max caracteres.',
            'id2.unique'                 => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre12.required'          => 'Agrega el primer nombre.',
            'nombre12.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre22.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido12.required'        => 'Agrega el primer apellido.',
            'apellido12.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido22.required'        => 'Agrega el segundo apellido.',
            'apellido22.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'Si el paciente es menor de edad, debe registrar el representante.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request)
    {

        $rules = [
            'parentesco'        => 'required',
            'id_seguro'         => 'required',
            'id2'               => 'required|max:10|unique:paciente,id',
            'nombre12'          => 'required|max:60',
            'nombre22'          => 'nullable|max:60',
            'apellido12'        => 'required|max:60',
            'apellido22'        => 'nullable|max:60',
            'telefono12'        => 'required|max:50',
            'telefono22'        => 'required|max:50',
            'id_pais2'          => 'required',
            'fecha_nacimiento2' => 'required|date',
            'email'             => 'unique:users,email,' . $request->id,
        ];
        if ($request['parentesco'] == "Principal") {
            $rules = array_add($rules, 'menoredad', 'in:0');
        }
        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'id2.required'               => 'Agrega la cédula.',
            'id2.max'                    => 'La cédula no puede ser mayor a :max caracteres.',
            'id2.unique'                 => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre12.required'          => 'Agrega el primer nombre.',
            'nombre22.required'          => 'Agrega el segundo nombre.',
            'nombre12.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre22.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido12.required'        => 'Agrega el primer apellido.',
            'apellido12.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido22.required'        => 'Agrega el segundo apellido.',
            'apellido22.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
            'email.unique'               => 'El correo se encuentra habilitado para otro usuario',
        ];

        $this->validate($request, $rules, $messages);
    }

    public function cuenta_cortesias($fechaini, $id_doctor)
    {

        $fecha_dia = date('Y-m-d', strtotime($fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = DB::table('agenda')
            ->join('cortesia_paciente', function ($join) use ($fecha_dia, $id_doctor, $nuevafecha) {
                $join->on('agenda.id_paciente', '=', 'cortesia_paciente.id')
                    ->where('agenda.id_doctor1', $id_doctor)->where('agenda.fechaini', '>', $fecha_dia)->where('agenda.fechaini', '<', $nuevafecha)->where('agenda.cortesia', 'SI')->where('cortesia_paciente.ilimitado', 'NO')->where('cortesia_paciente.cortesia', 'SI');
            })
            ->count();

        return $cant_cortesias;
    }

    public function busca_citasxpaciente_dia($fechaini, $id_paciente)
    {
        $fecha_dia = date('Y-m-d', strtotime($fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $citas = DB::table('agenda')->where('id_paciente', $id_paciente)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('estado', '!=', '0')->get();

        return $citas;
    }

    public function busca_citasxpaciente_dia_mes($fechaini, $id_paciente)
    {
        $fecha_dia = date('Y-m-d', strtotime($fechaini));

        $nuevafecha = strtotime('-1 month', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        //dd($fecha_dia,$nuevafecha);

        $citas = DB::table('agenda as a')->where('a.id_paciente', $id_paciente)->where('a.fechaini', '>', $nuevafecha)->leftjoin('users as u', 'u.id', 'a.id_doctor1')->leftjoin('users as uc', 'uc.id', 'a.id_usuariocrea')->leftjoin('users as um', 'um.id', 'a.id_usuariomod')->orderBy('a.fechaini')->select('a.*', 'u.nombre1', 'u.apellido1', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();

        return $citas;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // dd($request['id_paciente']);
        //dd($request['observaciones_admin']);

        //dd($request->all());
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $estado_cita = 0;
        $estado      = 1;
        $id_agenda  = null;

        //cortesia corregido
        $cortesia = "NO";
        if ($request['cortesia'] != null) {
            $cortesia = $request['cortesia'];
        }
        $cant_cort = 0;

        if ($request->est_amb_hos == '1') {
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        } else {
            $omni = null;
        }

        $cuenta = DB::table('agenda')
            ->where('id_paciente', '=', $request['id_paciente'])->count();
        if ($cuenta == '0') {
            $tipo_cita = 0;
        } else {
            $tipo_cita = 1;
        }

        $valor = $request['proc_consul'];

        $fecha          = date('Y-m-d H:i');
        $procedimientos = $request['procedimiento'];
        if (!is_null($procedimientos)) {
            $procedimientop = $procedimientos[0];
        } else {
            $procedimientop = null;
        }

        //12/1/2018 validacion de sala ocupada
        $idhospital = Sala::find($request['id_sala'])->id_hospital;
        if ($idhospital == '2') {
            //$this->valida_salaPentax($request,'0','0');
        } //--
        $post_procedimiento = 0;
        if ($request['post_procedimiento'] == "on") {
            $post_procedimiento = 1;
        }

        //solo valida si no es consulta y diferente de particular
        if ($request['id_seguro'] != 1 && $request['proc_consul'] != 0) {
            $this->validateInput3($request);
        }

        $this->validateInput4($request);
        //dd($request);
        //return "victor es marika22222";
        if ($valor != 2) {

            //dd($request['tipo_horario']);
            if ($request['tipo_horario'] != -1) {

                //valida horario del doctor
                $horariocontroller = new HorarioController();
                $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);
            } else {

                $rules_e    = ['observaciones' => 'required'];
                $mensajes_e = ['observaciones.required' => 'Debe ingresar una observación'];
                $this->validate($request, $rules_e, $mensajes_e);
            }

            $this->validateInput6($request);
            $this->validate_paciente($request);
            $this->validateMax1($request);
            $paciente = Paciente::find($request['id_paciente']);
            /* cortesias */
            if (!is_null($paciente)) {
                $cortesia_paciente = Cortesia_paciente::find($paciente->id);

                if (!is_null($cortesia_paciente)) {

                    if ($valor == '0') {
                        $cortesia = $cortesia_paciente->cortesia;

                        if ($cortesia_paciente->cortesia == "SI" && $cortesia_paciente->ilimitado == "NO") {

                            $cant_cort = $this->cuenta_cortesias($request['inicio'], $request['id_doctor1']);
                            $this->validateCortesias($request, $cant_cort);
                        }
                    }
                }
            }

            $usuario_prin = User::find($paciente->id_usuario);
            $correo       = $usuario_prin->email;

            if ($request->id_seguro == '2') {

                $val_arr = [
                    'id_empresa' => 'required',
                ];
                $msn_arr = [
                    'id_empresa.required' => 'Seleccione la Empresa',
                ];
                $this->validate($request, $val_arr, $msn_arr);
            }

            //paciente del  Doctor
            $paciente_dr = $request->paciente_dr;
            /*if($request->paciente_dr=='1'){
        if(is_null($paciente->paciente_doctor)){

        $arr_pac_doc = [
        'id_paciente' => $paciente->id,
        'id_usuario' => $request['id_doctor1'],
        'id_usuariocrea' => $idusuario,
        'ip_modificacion' => $ip_cliente,
        'id_usuariomod' => $idusuario,
        'ip_creacion' => $ip_cliente,

        ];
        Paciente_Doctor::create($arr_pac_doc);
        }else{

        if($paciente->paciente_doctor->id_usuario == $request['id_doctor1']){

        $paciente_dr = '1';
        }

        }

        }else{
        if(!is_null($paciente->paciente_doctor)){

        if($paciente->paciente_doctor->id_usuario == $request['id_doctor1']){

        $paciente_dr = '1';
        }

        }

        } */
        }
        $cv                    = $request['validacion_cv_msp'];
        $nc                    = $request['validacion_nc_msp'];
        $sec                   = $request['validacion_sec_msp'];
        $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

        if ($valor == 0) {
            //Consulta
            //dd($request['cod_val']);
            $input_historia = [
                'fechaini'              => $request['inicio'],
                'fechafin'              => $request['fin'],
                'id_paciente'           => $request['id_paciente'],
                'id_doctor1'            => $request['id_doctor1'],
                'proc_consul'           => $request['proc_consul'],
                'id_empresa'            => $request['id_empresa'],
                'id_sala'               => $request['id_sala'],
                'espid'                 => $request['espid'],
                'tipo_cita'             => $request['tipo_cita'],
                'estado_cita'           => $estado_cita,
                'observaciones'         => $request['observaciones'],
                'est_amb_hos'           => $request['est_amb_hos'],
                'id_seguro'             => $request['id_seguro'],
                'estado'                => 1,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'cortesia'              => $cortesia,
                'vip'                   => $request['vip'],
                'fecha_val'             => $request['fecha_val'],
                'cod_val'               => $request['cod_val'],
                'validacion_cv_msp'     => $request['validacion_cv_msp'],
                'validacion_nc_msp'     => $request['validacion_nc_msp'],
                'validacion_sec_msp'    => $request['validacion_sec_msp'],
                'codigo_validacion_msp' => $codigo_validacion_msp,
                'adelantado'            => $request['adelantado'],
                'procedencia'           => $request['procedencia'],
                'teleconsulta'          => $request['teleconsulta'],
                'tc'                    => $request['tc'],
                //'paciente_dr' => $request['paciente_dr'],
                'paciente_dr'           => $paciente_dr,
                'omni'                  => $omni,
                'post_proc'             => $post_procedimiento,
            ];
            if ($request['hc'] != null) {
                $paciente               = Paciente::find($request['id_paciente']);
                $historiaclinica_nueva  = $request['hc'] . $paciente->historia_clinica;
                $nuevo_historia_clinica = [
                    'historia_clinica' => $historiaclinica_nueva,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                Paciente::where('id', $paciente->id)->update($nuevo_historia_clinica);
            }

            $id_agenda = Agenda::insertGetId($input_historia);
            if ($request['hc'] != null) {
                Agenda_archivo::create([
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => 'txt',
                    'texto'           => $request['hc'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            if ($request['archivo'] != null) {
                $input_archivo = [
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => "HCAGENDA",
                    'descripcion'     => "Historia Clinica creada de la agenda",
                    'ruta'            => "/hc_agenda/",
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $id_archivo = Agenda_archivo::insertGetId($input_archivo);
                $this->subir_archivo_validacion($request, $id_agenda, $id_archivo);
            }
            /* validacion correo
            Mail::send('mails.consulta', $request->all(), function($msj)  use ($correo){
            $msj->subject('Reservacion de cita medica IECED');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');
            });*/
            $input = [
                'id_seguro'        => $request['id_seguro'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,

            ];
            if ($paciente->fecha_nacimiento != $request['fecha_nacimiento']) {
                //dd($request->all());
                Paciente::where('id', $request['id_paciente'])->update($input);
            }
        }
        if ($valor == 2) {
            //Reuniones

            Agenda::create([
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'procedencia'     => $request['clase'],
                'id_doctor1'      => $request['id_doctor1'],
                'proc_consul'     => $request['proc_consul'],
                'id_sala'         => $request['id_sala'],
                'estado_cita'     => 1,
                'observaciones'   => $request['observaciones'],
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'post_proc'       => $post_procedimiento,

            ]);
        }
        if ($valor == 1) {
            //ES PROCEDIMIENTO

            //UNIDAD PENTAX
            if ($idhospital == '2') {

                //return $idhospital;
                $user_agenda = $idusuario;
                $permiso     = Agenda_Permiso::where('id_usuario', $user_agenda)->where('proc_consul', '1')->where('estado', '2')->first();

                //USUARIO NO PERMITIDO
                //dd($permiso);
                if (is_null($permiso)) {

                    //VALIDACION MAX
                    $this->validateMaxproc($request);
                }

                //return "con permiso";
            }

            //VALIDA SI TIENE AGREGADO PROCEDIMIENTO 22012019
            $rules1 = [
                'procedimiento' => 'required',
            ];
            $mensajes1 = [
                'procedimiento.required' => 'Ingrese el Procedimiento',
            ];
            $this->validate($request, $rules1, $mensajes1);
            //
            //dd("paso");

            if ($request['id_doctor2'] != '' || $request['id_doctor3'] != '') {
                $this->validateInput5($request);
            }
            if ($request['id_doctor2'] != '' && $request['id_doctor3'] != '') {

                $this->validateDoctores($request);
            }
            if ($request['id_doctor2'] != '') {
                $this->validateMax2($request);
            }
            if ($request['id_doctor3'] != '') {
                $this->validateMax3($request);
            }

            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')
            //dd();

            if ($request['id_seguro'] == '3') {
                // dd($request['id_seguro']);
                if ($request['adelantado'] == 1) {
                    //dd($request['adelantado']);
                    $rules_observacionissfa = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionissfa = [
                        'observaciones.required' => 'Ingrese una observacion',
                    ];
                    $this->validate($request, $rules_observacionissfa, $mensajes_observacionissfa);
                }

                $rules_issfa = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];

                $mensajes_issfa = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',
                ];

                $this->validate($request, $rules_issfa, $mensajes_issfa);
            } elseif ($request['id_seguro'] == '5') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionmsp = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionmsp = [
                        'observaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacionmsp, $mensajes_observacionmsp);
                }

                $rules_msp = [
                    'fecha_val'          => 'required',
                    'validacion_cv_msp'  => 'required',
                    'validacion_nc_msp'  => 'required',
                    'validacion_sec_msp' => 'required',

                ];
                $mensajes_msp = [
                    'fecha_val.required'          => 'Ingrese la fecha de validación',
                    'validacion_cv_msp.required'  => 'codigo',
                    'validacion_nc_msp.required'  => 'numero',
                    'validacion_sec_msp.required' => 'secuencia',

                ];

                $this->validate($request, $rules_msp, $mensajes_msp);
            } elseif ($request['id_seguro'] == '6') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionisspol = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacionisspol = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacionisspol, $mensajes_observacionisspol);
                }

                $rules_isspol = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_isspol = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                $this->validate($request, $rules_isspol, $mensajes_isspol);
            } elseif ($request['id_seguro'] == '2') {

                if ($request['adelantado'] == 1) {
                    $rules_observacioniess = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacioniess = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacioniess, $mensajes_observacioniess);
                }

                $rules_iess = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_iess = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                //$this->validate($request, $rules_iess, $mensajes_iess);
            }

            $input_historia = [
                'fechaini'              => $request['inicio'],
                'fechafin'              => $request['fin'],
                'id_empresa'            => $request['id_empresa'],
                'id_paciente'           => $request['id_paciente'],
                'id_doctor1'            => $request['id_doctor1'],
                'id_doctor2'            => $request['id_doctor2'],
                'id_doctor3'            => $request['id_doctor3'],
                'id_procedimiento'      => $procedimientop,
                'proc_consul'           => $request['proc_consul'],
                'id_sala'               => $request['id_sala'],
                'vip'                   => $request['vip'],
                'fecha_val'             => $request['fecha_val'],
                'cod_val'               => $request['cod_val'],
                'validacion_cv_msp'     => $request['validacion_cv_msp'],
                'validacion_nc_msp'     => $request['validacion_nc_msp'],
                'validacion_sec_msp'    => $request['validacion_sec_msp'],
                'codigo_validacion_msp' => $codigo_validacion_msp,
                'adelantado'            => $request['adelantado'],
                'espid'                 => $request['espid'],
                'id_seguro'             => $request['id_seguro'],
                'tipo_cita'             => $request['tipo_cita'],
                'estado_cita'           => $estado_cita,
                'observaciones'         => $request['observaciones'],
                'est_amb_hos'           => $request['est_amb_hos'],
                'estado'                => 1,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                //CORRECCION CORTESIA
                'cortesia'              => $cortesia,
                'procedencia'           => $request['procedencia'],
                //'paciente_dr' => $request['paciente_dr'],
                'paciente_dr'           => $paciente_dr,
                'omni'                  => $omni,
                'post_proc'             => $post_procedimiento,
            ];

            $id_agenda            = agenda::insertGetId($input_historia);
            $procedimiento_enviar = null;
            foreach ($procedimientos as $value) {
                $procedimiento_a      = DB::table('procedimiento')->where('id', '=', $value)->get();
                $procedimiento_enviar = $procedimiento_a[0]->nombre . '+' . $procedimiento_enviar;

                if ($procedimientop != $value) {
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $idusuario,
                        'id_usuariomod'    => $idusuario,
                    ]);
                }
            }
            if ($request['hc'] != null) {
                $paciente               = Paciente::find($request['id_paciente']);
                $historiaclinica_nueva  = $request['hc'] . $paciente->historia_clinica;
                $nuevo_historia_clinica = [
                    'historia_clinica' => $historiaclinica_nueva,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                Paciente::where('id', $paciente->id)->update($nuevo_historia_clinica);
            }

            if ($request['hc'] != null) {
                Agenda_archivo::create([
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => 'txt',
                    'texto'           => $request['hc'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            if ($request['archivo'] != null) {
                $input_archivo = [
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => "HCAGENDA",
                    'descripcion'     => "Historia Clinica creada de la agenda",
                    'ruta'            => "/hc_agenda/",
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $id_archivo = Agenda_archivo::insertGetId($input_archivo);
                $this->subir_archivo_validacion($request, $id_agenda, $id_archivo);
            }
            $input = [
                'id_seguro'        => $request['id_seguro'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,

            ];
            if ($paciente->fecha_nacimiento != $request['fecha_nacimiento']) {
                //dd($request->all());
                Paciente::where('id', $request['id_paciente'])->update($input);
            }
            /* enviar correo procedimiento
        $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);
        $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $request['nombre_paciente'], "especialidad_nombre" => $request['especialidad_nombre'], "inicio" => $request['inicio'], "nombre_doctor" => $request['nombre_doctor'], "hospital_nombre" => $request['hospital_nombre'], "consultorio_nombre" => $request['consultorio_nombre'], "hospital_direccion" => $request['hospital_direccion']);
        if($paciente->fecha_nacimiento != $request['fecha_nacimiento']){
        Paciente::where('id', $request['id_paciente'])->update($input);
        }
        Mail::send('mails.procedimiento', $avanza, function($msj)  use ($correo){
        $msj->subject('Reservacion de procedimiento medico IECED');
        $msj->to($correo);
        $msj->bcc('torbi10@hotmail.com');
        });*/
        }
        if ($valor == 2) {
            $idPaciente = $request['id_paciente'];
            // $obser_admin="";
            if ($request['observaciones_admin'] != "" || $request['observaciones_admin'] != null) {
                $obser_admin = Paciente_Observaciones::where('id_paciente', $idPaciente)->first();
                $obser       = [
                    'id_paciente'     => $idPaciente,
                    'observacion'     => $request['observaciones_admin'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                if (count($obser_admin) > 0) {
                    //actualiza
                    Paciente_Observaciones::where('id_paciente', $idPaciente)->update($obser);
                } else {
                    //crea
                    Paciente_Observaciones::create($obser);
                }
            }
        }
        if(!is_null($id_agenda)){ 
            $agenda = Agenda::find($id_agenda);
            Log_agenda::create([
                'id_agenda'          => $agenda->id,
                'estado_cita_ant'    => $agenda->estado_cita,
                'fechaini_ant'       => $agenda->fechaini,
                'fechafin_ant'       => $agenda->fechafin,
                'estado_ant'         => $agenda->estado,
                'cortesia_ant'       => $agenda->cortesia,
                'id_doctor1_ant'     => $agenda->id_doctor1,
                'id_doctor2_ant'     => $agenda->id_doctor2,
                'id_doctor3_ant'     => $agenda->id_doctor3,
                'id_sala_ant'        => $agenda->id_sala,
                'fechaini'           => $agenda->fechaini,
                'fechafin'           => $agenda->fechafin,
                'estado'             => '1',
                'cortesia'           => $agenda->cortesia,
                'observaciones'      => $request['observaciones'],
                'id_doctor1'         => $agenda->id_doctor1,
                'id_doctor2'         => $agenda->id_doctor2,
                'id_doctor3'         => $agenda->id_doctor3,
                'id_sala'            => $agenda->id_sala,
                'descripcion'        => 'CREACION DE LA AGENDA',
                'id_usuarioconfirma' => $agenda->id_usuarioconfirma,
                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);
        }
        return redirect()->route('agenda.fecha', ['id' => $request['id_doctor1'], 'fecha' => $request['unix']]);
    }

    private function validate_paciente($request)
    {
        $id_paciente = $request['id_paciente'];
        $ini2        = date_create($request['inicio']);
        $fin2        = date_create($request['fin']);
        $inicio      = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin         = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio      = date_format($inicio, 'Y/m/d H:i:s');
        $fin         = date_format($fin, 'Y/m/d H:i:s');
        $dato2       = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_paciente', '=', $request['id_paciente']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        $rules       = [
            'id_paciente' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes = [
            'id_paciente.unique_doctor' => 'El paciente ya posee una cita a esta hora',
        ];
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput3($request)
    {
        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        if ($request->id_doctor1 == '9666666666') {
            $rules = [
                //'id_doctor1' =>  'unique_doctor:'.$cant_agenda,
                //'observaciones' => 'max:200',
                'inicio'  => 'required|date|before:fin',
                'fin'     => 'required|date|after:inicio',
                'id_sala' => 'required',
            ];
        } else {
            $rules = [
                'id_doctor1' => 'unique_doctor:' . $cant_agenda,
                //'observaciones' => 'max:200',
                'inicio'     => 'required|date|before:fin',
                'fin'        => 'required|date|after:inicio',
                'id_sala'    => 'required',
            ];
        }

        $mensajes = [
            'observaciones.max'         => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor'  => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'           => 'Agregue una fecha de Inicio.',
            'inicio.date'               => 'fecha mal agregada.',
            'inicio.before'             => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'              => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'              => 'Agregue una fecha de Inicio.',
            'fin.date'                  => 'fecha mal agregada.',
            'fin.before'                => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                 => 'la fecha de fin debe ser después que la de inicio',
            'procedencia.required'      => 'Agregue la procedencia.',
            'procedencia.max'           => 'La procedencia no puede ser mayor a :max caracteres',
            'fecha_nacimiento.required' => 'Agregue la fecha de nacimiento.',
        ];
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput3_2($request, $id)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        if ($request->id_doctor1 == '9666666666') {
            $rules = [
                //'id_doctor1' =>  'unique_doctor:'.$cant_agenda,
                //'observaciones' => 'max:200',
                'inicio'  => 'required|date|before:fin',
                'fin'     => 'required|date|after:inicio',
                'id_sala' => 'required',

            ];
        } else {
            $rules = [
                'id_doctor1' => 'unique_doctor:' . $cant_agenda,
                //'observaciones' => 'max:200',
                'inicio'     => 'required|date|before:fin',
                'fin'        => 'required|date|after:inicio',
                'id_sala'    => 'required',

            ];
        }

        $mensajes = [
            'observaciones.max'        => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor' => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'          => 'Agregue una fecha de Inicio.',
            'inicio.date'              => 'fecha mal agregada.',
            'inicio.before'            => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'             => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'             => 'Agregue una fecha de Inicio.',
            'fin.date'                 => 'fecha mal agregada.',
            'fin.before'               => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                => 'la fecha de fin debe ser después que la de inicio',
        ];

        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput3_3($request, $id, $ini2, $fin2)
    {

        $ini2   = date_create($ini2);
        $fin2   = date_create($fin2);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        if ($request->id_doctor1 == '9666666666') {
            $rules = [
                //'id_doctor1' =>  'unique_doctor:'.$cant_agenda,
                'observaciones' => 'max:2000',

            ];
        } else {

            $rules = [
                'id_doctor1' => 'unique_doctor:' . $cant_agenda,
                //'observaciones' => 'max:200',

            ];
        }

        $mensajes = [
            'observaciones.max'        => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor' => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'          => 'Agregue una fecha de Inicio.',
            'inicio.date'              => 'fecha mal agregada.',
            'inicio.before'            => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'             => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'             => 'Agregue una fecha de Inicio.',
            'fin.date'                 => 'fecha mal agregada.',
            'fin.before'               => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                => 'la fecha de fin debe ser después que la de inicio',
        ];

        $this->validate($request, $rules, $mensajes);
    }

    public function valida_salaPentax($request, $crea_act, $id)
    {

        $sala   = $request['id_sala'];
        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where(function ($query) use ($sala) {
            return $query->where('id_sala', '=', $sala);
        });

        $dato2 = $dato2->where(function ($query) use ($request, $inicio, $fin) {
            return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                ->orWhere(
                    function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                    }
                )
                ->orWhere(function ($query) use ($request, $inicio, $fin) {
                    $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                })
                ->orWhere(function ($query) use ($request, $inicio, $fin) {
                    $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                });
        })
            ->where(function ($query) {
                return $query->where('estado', '<>', '0');
            });

        if ($crea_act == '1') {
            $dato2 = $dato2->where('id', '<>', $id);
        }
        $dato2 == $dato2->get();

        $cant_agenda = $dato2->count();

        $rules = [
            'id_sala' => 'unique_doctor:' . $cant_agenda,
        ];

        $mensajes = [

            'id_sala.unique_doctor' => 'La sala seleccionada esta ocupada',
        ];

        $this->validate($request, $rules, $mensajes);
    }

    private function validateMax1($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);
        //return  9/10/2018 se habilita bloqueo

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        //dd($cantidad);
        $doctor = User::find($request['id_doctor1']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }
    private function validateMaxproc($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        //dd( $fecha_req);

        $dato2 = DB::table('agenda as a')

            ->join('sala as s', 's.id', 'a.id_sala')
            ->select('a.*')
            ->where('s.id_hospital', '2')
            ->where('proc_consul', '=', 1)
            ->where('a.estado', '!=', '0')
            ->whereBetween('fechaini', [$fecha_req . ' 00:00:00', $fecha_req . ' 23:59:59'])->get();

        $cantidad = $dato2->count();
        //dd($cantidad);
        $maximo_procedimientos = Max_Procedimiento::find('1')->cantidad;

        $rules = [
            'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $maximo_procedimientos . ',',
        ];

        $mensajes = [

            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $maximo_procedimientos,
        ];
        $this->validate($request, $rules, $mensajes);
    }
    private function validateMax1_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor1']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }

    private function validateEdit($request)
    {

        $mensajes = [
            'estado_cita.required' => 'Selecciona el Estado de la Cita.',
            'observaciones.max'                 => 'La observacion no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required'         => 'Ingresa la fecha de nacimiento.',
        ];
        $constraints = [
            'estado_cita' => 'required',

            //'observaciones' => 'max:200',

        ];

        $this->validate($request, $constraints, $mensajes);
    }

    private function validateDoctores($request)
    {
        $rules = [
            'id_doctor3' => 'different:id_doctor2',
            'id_doctor2'           => 'different:id_doctor3'
        ];
        $mensajes = [
            'id_doctor2.different' => 'Los Doctores asistentes no pueden ser la misma persona',
            'id_doctor3.different' => 'Los Doctores asistentes no pueden ser la misma persona'
        ];

        $this->validate($request, $rules, $mensajes);
    }

    private function validateDoctores2($request)
    {
        $rules = [
            'id_doctor1' => 'different:id_doctor2',
            'id_doctor1'           => 'different:id_doctor3',
            'id_doctor2'           => 'different:id_doctor1',
            'id_doctor3'           => 'different:id_doctor1',
        ];
        $mensajes = [
            'id_doctor1.different' => 'El Doctor principal no puede ser un asistente',
            'id_doctor2.different' => 'El Doctor asistente no pueden ser principal',
            'id_doctor3.different' => 'El Doctor asistente no pueden ser principal'
        ];

        $this->validate($request, $rules, $mensajes);
    }

    private function validateMax2($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor2']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor2' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor2' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor2.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor2.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }
    private function validateMax3($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();

        $doctor = User::find($request['id_doctor3']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor3' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor3' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor3.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor3.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }
    private function validateMax2_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor2']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor2' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor2' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor2.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor2.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }
    private function validateMax3_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();

        $doctor = User::find($request['id_doctor3']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor3' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor3' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor3.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor3.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput5($request)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        $dato3 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $request['fin'] . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda2 = $dato3->count();

        $rules3 = [
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $rules2 = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes2 = [
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];
        $mensajes3 = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] == "") {
            $this->validate($request, $rules2, $mensajes2);
        }

        if ($request['id_doctor2'] == "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules3, $mensajes3);
        }

        $rules = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $mensajes = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules, $mensajes);
        }
    }

    private function validateInput5_2($request, $id)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        $dato3 = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $request['fin'] . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda2 = $dato3->count();

        $rules3 = [
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $rules2 = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes2 = [
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];
        $mensajes3 = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] == "") {
            $this->validate($request, $rules2, $mensajes2);
        }

        if ($request['id_doctor2'] == "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules3, $mensajes3);
        }

        $rules = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $mensajes = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 2',
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 1',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules, $mensajes);
        }
    }

    public function validateCortesias($request, $cant_cort)
    {
        $reglas = [
            'id' => 'comparamayor:' . $cant_cort . ',2,',
        ];
        $mensajes = [
            'id.comparamayor' => 'Existen ' . $cant_cort . ' cortesias en el día, consulte con el Dr.',
        ];
        $this->validate($request, $reglas, $mensajes);
    }

    private function validateInput4($request)
    {
        $fecha  = date('Y-m-d H:i');
        $reglas = [
            'inicio' => 'date|after:' . $fecha,
            'fin'    => 'date|after:' . $fecha,
        ];
        $mensajes = [
            'inicio.after'       => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.after'          => 'la fecha de fin debe ser después que la fecha actual',
            'id_seguro.required' => 'se requiere ingresar el seguro',
        ];
        $this->validate($request, $reglas, $mensajes);
    }
    private function validateInput6($request)
    {
        $reglas = [
            'id'          => 'exists:paciente',
            'espid'       => 'required',
            'id_paciente' => 'required',
            'id_seguro'   => 'required',
            'est_amb_hos' => 'required',
            'tipo_cita'   => 'required',
        ];
        $mensajes = [
            'id.exists'            => 'Paciente ingresado no existe',
            'id_paciente.required' => 'se requiere numero de cedula del paciente',
            'id_seguro.required'   => 'se requiere ingresar el seguro',
            'est_amb_hos.required' => 'Seleccione el estado de ingreso del paciente',
            'tipo_cita.required'   => 'Seleccione es consecutivo o primera vez',
        ];
        $this->validate($request, $reglas, $mensajes);
    }

    public function edit2_pre($id, $url_doctor, Request $request)
    {
        $agenda = Agenda::find($id);

        if ($agenda->estado_cita != 2) {
            return "ya se gestionó";
        } else {
            return "ok";
        }
    }

    public function edit2($id, $url_doctor, Request $request)
    {
        $ruta = Cookie::get('ruta_p');

        $rolUsuario = Auth::user()->id_tipo_usuario;
        // $id = Auth::user()->id;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','=',1)->orderBy('apellido1', 'asc')->get(); //3=DOCTORES;
        $usuarios   = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get()->where('estado', '=', 1); //6=ENFERMEROS;
        $salas      = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')->orderBy('hospital.nombre_hospital')
            ->get();
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'paciente.ocupacion', 'paciente.referido')
            ->where('agenda.id', '=', $id)
            ->first();

        $logs = DB::table('log_agenda as l')->where('l.id_agenda', $agenda->id)->join('users as u', 'u.id', 'l.id_usuariocrea')->select('l.*', 'u.nombre1', 'u.apellido1')->orderBy('l.id', 'desc')->get();

        $historia = Historiaclinica::where('id_agenda', $id)->get();

        $ordenes = Examen_orden::where('id_paciente', $agenda->id_paciente)->where('estado', '<>', 0)->count();
        $orden_a = Examen_Orden_Agenda::where('id_agenda', $agenda->id)->first();
        //dd($orden_a);
        $cantidad_doc = 0;
        if (!is_null($historia->first())) {

            $ControlDocController = new hc_admision\ControlDocController;
            $hSeguro              = Seguro::find($historia['0']->id_seguro);

            $cantidad_doc = $ControlDocController->carga_documentos_union($historia['0']->hcid, $agenda->proc_consul, $hSeguro->tipo)->count();

            $ordenes = Examen_orden::where('hcid', $historia[0]->hcid)->count();
        }

        //cedula y nombre del paciente cambiar a produ 7/11/2017
        $especialidades = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $agenda->id_doctor1)->get();

        $seguros              = Seguro::all();
        $procedimientos       = Procedimiento::all();
        $empresas             = Empresa::all();
        $agendaprocedimientos = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();
        //cedula y nombre del paciente cambiar a produ 7/11/2017
        if ($request['id_consultadr'] != null) {
            $id_doc = $request['id_consultadr'];
        } else {
            if (old('id_consultadr') != '') {
                $id_doc = old('id_consultadr');
            } else {
                $id_doc = $agenda->id_doctor1;
            }
        }

        //agendas
        $cagenda  = [];
        $cagenda3 = [];
        $cagenda2 = [];

        $ar_historia    = DB::table('agenda_archivo')->where('id_agenda', $id)->where('tipo_documento', '=', 'HCAGENDA')->get();
        $ar_historiatxt = Agenda_archivo::where('id_agenda', $id)->where('tipo_documento', 'txt')->first();

        $sala     = null;
        $hospital = null;
        if (!is_null($agenda->id_sala)) {
            $sala     = Sala::find($agenda->id_sala);
            $hospital = Hospital::find($sala->id_hospital);
        }

        if (!is_null($historia->first())) {
            $xtipo = $hSeguro->tipo;
        } else {
            $xtipo = Seguro::find($agenda->id_seguro)->tipo;
        }

        $pre_post = '0';
        $ex_pre   = null;
        $ex_post  = null;
        if ($xtipo == '0') {
            /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
            $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $agenda->id_procedimiento)->first();

            $pre_post = '0';
            if (!is_null($obligatorio)) {
                $pre_post = $obligatorio->pre_post; //2 prey post

            }

            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($agendaprocedimientos->count() > 0) {
                    while ($bandera) {

                        $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post    = '0';
                        if (!is_null($obligatorio)) {
                            $pre_post = $obligatorio->pre_post; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $agendaprocedimientos->count()) {
                            $bandera = false;
                        }
                    }
                }
            }

            /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
            if ($pre_post == '0') {
                if ($agenda->id_procedimiento != null) {
                    $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $agenda->id_procedimiento)->first();
                    $pre_post  = '0';
                    if (is_null($excepcion)) {
                        $pre_post = '1'; //2 pre

                    }
                }
            }

            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($agendaprocedimientos->count() > 0) {
                    while ($bandera) {

                        $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post  = '0';
                        if (is_null($excepcion)) {
                            $pre_post = '1'; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $agendaprocedimientos->count()) {
                            $bandera = false;
                        }
                    }
                }
            }

            //ordenes del paciente de la ultima semana, pre y post
            //$hoy = Date('Y-m-d');
            $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($agenda->fechaini)));
            $fecha_despues = Date('Y-m-d', strtotime('+5 day', strtotime($agenda->fechaini)));
            //dd($fecha_antes,$agenda->fechaini,$fecha_despues);

            $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

            if (is_null($ex_pre)) {
                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
            }

            $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

            if (is_null($ex_post)) {
                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
            }
        }

        $observaciones_admin = "";
        $observaciones_admin = Paciente_Observaciones::where('id_paciente', $agenda->id_paciente)->first();
        if (!is_null($observaciones_admin)) {
            $observaciones_admin = $observaciones_admin->observacion;
        } else {
            $observaciones_admin = "";
        }
        //dd($observaciones_admin);
        //dd($ex_pre, $ex_post);
        $fecha_p = date("Y-m-d");
        $triaje_Check = Triaje_Telefonico::where("id_agenda", $id)->first();
	$seguros_pacientes = SegurosPaciente::join('seguros as s', 's.id', 'seguros_paciente.id_seguro')
            ->where('seguros_paciente.id_agenda', $agenda->id)
            ->where('seguros_paciente.estado', 1)
            ->get()->pluck('id')->toArray();
	    $agenda_firma = HistoriaClinica::join('hc_procedimientos', 'historiaclinica.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('historiaclinica.id_agenda', 'LIKE', $agenda->id)
            ->whereNotNull('hc_procedimientos.id_doctor_firma')
            ->select('historiaclinica.*')
            ->first();
        return view('agenda/edit', ['triaje_Check' => $triaje_Check, 'agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'historia' => $historia, 'especialidades' => $especialidades, 'seguros' => $seguros, 'procedimientos' => $procedimientos, 'empresas' => $empresas, 'agendaprocedimientos' => $agendaprocedimientos, 'cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3, 'url_doctor' => $url_doctor, 'ar_historia' => $ar_historia, 'ar_historiatxt' => $ar_historiatxt, 'cantidad_doc' => $cantidad_doc, 'id_doc' => $id_doc, 'sala' => $sala, 'hospital' => $hospital, 'ordenes' => $ordenes, 'orden_a' => $orden_a, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post, 'logs' => $logs, 'ruta' => $ruta, 'observaciones_admin' => $observaciones_admin, 'seguros_pacientes' => $seguros_pacientes, 'agenda_firma' => $agenda_firma]);
    }

    public function detalle($id)
    {
        //return $id;
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol_dr()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $seguros    = null;
        $subseguros = null;
        $seguros    = Seguro::all();
        //$subseguros = Subseguro::all();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('seguros as hs', 'hs.id', 'h.id_seguro')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'paciente.referido', 'paciente.id_usuario', 'paciente.trabajo', 'paciente.observacion', 'paciente.alcohol', 'hs.nombre as hsnombre', 'h.presion', 'h.pulso', 'h.temperatura', 'h.o2', 'h.altura', 'h.peso', 'h.perimetro', 'h.examenes_realizar', 'h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $id)
            ->first();

        $procedimientos_pentax = null;
        $no_admin              = false;
        $protocolo             = null;
        $evolucion             = null;
        $imagenes              = null;
        $documentos            = null;
        $biopsias              = null;
        $orden_laboratorio     = null;
        $estudios              = null;
        $hc_rec                = null;

        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->orderBy('e.id', 'desc')->where('a.estado_cita', '<>', '3')->get();

        $protocolos = DB::table('hc_protocolo as p')->join('historiaclinica as h', 'h.hcid', 'p.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('hc_procedimientos as hc', 'hc.id', 'p.id_hc_procedimientos')->join('agenda as a', 'a.id', 'h.id_agenda')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')->select('p.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'p.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')->orderBy('a.fechaini', 'desc')->orderBy('p.created_at', 'desc')->where('a.espid', '<>', '10')->get();

        $protocolos_dia   = [];
        $evoluciones_proc = [];
        $child_pugh       = null;
        $hc_proc          = null;

        if (!is_null($agenda->hcid)) {

            if ($agenda->proc_consul == '0' || $agenda->proc_consul == '4') {
                //CONSULTAS O EVOLUCIONES CREADAS POR EL DOCTOR
                $xseguro   = Seguro::find($agenda->h_idseguro);
                $evolucion = DB::table('hc_evolucion as e')->where('e.hcid', $agenda->hcid)->join('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();
                if (is_null($evolucion)) {

                    if ($agenda->estado_cita == '4') {

                        $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();

                        if (is_null($hc_proc)) {
                            $input_hc_procedimiento = [
                                'id_hc'                     => $agenda->hcid,
                                'id_seguro'                 => $agenda->h_idseguro,
                                'id_doctor_examinador'      => $agenda->id_doctor1,
                                'id_procedimiento_completo' => '40',
                                'ip_modificacion'           => $ip_cliente,
                                'id_usuariocrea'            => $idusuario,
                                'id_usuariomod'             => $idusuario,
                                'ip_creacion'               => $ip_cliente,
                                'created_at'                => date('Y-m-d H:i:s'),
                                'updated_at'                => date('Y-m-d H:i:s'),
                            ];
                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                        } else {
                            $id_hc_procedimiento    = $hc_proc->id;
                            $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                            if (is_null($procedimiento_generico->id_doctor_examinador)) {
                                $inputx2 = [
                                    'id_doctor_examinador' => $agenda->id_doctor1,
                                    'ip_modificacion'      => $ip_cliente,
                                    'id_usuariomod'        => $idusuario,
                                ];
                                //return $request->all();
                                $procedimiento_generico->update($inputx2);
                            }
                            $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                        }
                        $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();

                        $xcuadro_clinico = null;
                        $tsexo           = '';
                        if ($agenda->espid == '4') {
                            if (!is_null($xseguro)) {
                                if ($xseguro->tipo == '0') {
                                    if ($agenda->sexo == '1') {
                                        $tsexo = 'MASCULINO';
                                    } elseif ($agenda->sexo == '2') {
                                        $tsexo = 'FEMENINO';
                                    }

                                    $tedad = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;

                                    $xcuadro_clinico = 'PACIENTE DE SEXO ' . $tsexo . ' DE ' . $tedad . ' AÑOS DE EDAD CON CUADRO CLINICO DE  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MESES DE EVOLUCION CARACTERIZADO POR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; , &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,<br>
                                    (DESCRIPCION DE SINTOMAS INTENSIDAD, HORARIO DE APARICION QUE LO EXACERBA)<br>
                                    EN LA ACTUALIDAD SINTOMAS SE INTESIFICAN POR LO QUE ACUDE A CONSULTA.';
                                }
                            }
                        }

                        $input_hc_evolucion = [
                            'hc_id_procedimiento' => $id_hc_procedimiento,
                            'hcid'                => $agenda->hcid,
                            'secuencia'           => '0',
                            'cuadro_clinico'      => $xcuadro_clinico,
                            'fecha_ingreso'       => ' ',
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariomod'       => $idusuario,
                            'id_usuariocrea'      => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'created_at'          => date('Y-m-d H:i:s'),
                            'updated_at'          => date('Y-m-d H:i:s'),
                        ];
                        $id_evolucion = Hc_evolucion::insertGetId($input_hc_evolucion);

                        $evolucion = DB::table('hc_evolucion as e')->where('e.id', $id_evolucion)->leftjoin('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();
                        //return $evolucion;
                        //dd($evolucion);
                    }
                }

                $child_pugh    = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                $examen_fisico = null;
                if ($agenda->espid == '4') {

                    $examen_fisico = 'ESTADO CABEZA Y CUELLO:
ESTADO TORAX:
ESTADO ABDOMEN:
ESTADO MIEMBROS SUPERIORES:
ESTADO MIEMBROS INFERIORES:
OTROS:
';

                    if (!is_null($xseguro)) {
                        if ($xseguro->tipo == '0') {
                            $examen_fisico = 'REVISION ACTUAL ORGANOS Y SISTEMAS
PIEL: TEXTURA NORMAL, HIDRATADA, SIN LESIONES
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO MAREO
CARA: SIN ALTERACIONES
CUELLO: MOVIL, CENTRAL, SIN ADENOPATIAS
TORAX: SIMETRICO, SIN LESIONES
CSPS: VENTILADOS.
RSCS: RITMICOS

ABDOMEN:BANDO DEPRESIBLE SIN DOLOR A LA PALPACION SUPERFICIAL Y PROFUNDA.
RSHS(+), PUNTOS URETERALES(-),
COLUMNA VERTEBRAL: CENTRAL, SIN DESVIACION, NI LESIONES, PUÑOPERCUSION: (-)
EXTREMIDADES: SIN ALTERACIONES, SIMETRICOS

EXAMEN FISICO REGIONAL
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO, CEFALEA
EXTREMIDADES: SIN LESIONES NI ALTERACIONES';
                        }
                    }
                }

                if (is_null($child_pugh)) {
                    if ($agenda->estado_cita == '4') {
                        $input_child_pugh = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'id_usuariocrea'  => $idusuario,
                            'examen_fisico'   => $examen_fisico,
                            'ip_creacion'     => $ip_cliente,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        hc_child_pugh::insert($input_child_pugh);

                        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                    }
                }
                $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                if (is_null($hc_proc)) {
                    $input_hc_procedimiento = [
                        'id_hc'                     => $agenda->hcid,
                        'id_seguro'                 => $agenda->h_idseguro,
                        'id_doctor_examinador'      => $agenda->id_doctor1,
                        'id_procedimiento_completo' => '40',
                        'ip_modificacion'           => $ip_cliente,
                        'id_usuariocrea'            => $idusuario,
                        'id_usuariomod'             => $idusuario,
                        'ip_creacion'               => $ip_cliente,
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];
                    $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                } else {
                    $id_hc_procedimiento    = $hc_proc->id;
                    $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                        //return $request->all();
                        $procedimiento_generico->update($inputx2);
                    }

                    $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                }
                $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                //return $child_pugh;
            }
            if ($agenda->proc_consul == '1') {
                //PROCEDIMIENTOS

                $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

                //dd($protocolo->id);

                if (!is_null($protocolo)) {

                    $evoluciones_proc = DB::table('hc_evolucion as e')->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)->get();
                    //dd($evoluciones_proc);

                    $evoluciones_proc = DB::table('hc_evolucion as e')->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)->get();
                    //dd($evoluciones_proc);

                }

                $protocolos_dia = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'nombre_general')->get();

                if (!is_null($protocolo)) {

                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();

                    $imagenes               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->get();
                    $documentos             = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '2')->get();
                    $estudios               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get();
                    $biopsias               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '4')->get();
                    $hc_proc                = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    $procedimiento_generico = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                        //return $request->all();
                        $procedimiento_generico->update($inputx2);
                        $procedimiento_generico = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    }
                    $hc_proc = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                } else {

                    $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                    if (is_null($hc_proc)) {
                        $input_hc_procedimiento = [
                            'id_hc'                => $agenda->hcid,
                            'id_seguro'            => $agenda->h_idseguro,
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                            'created_at'           => date('Y-m-d H:i:s'),
                            'updated_at'           => date('Y-m-d H:i:s'),
                        ];
                        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                    } else {
                        $id_hc_procedimiento    = $hc_proc->id;
                        $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                        if (is_null($procedimiento_generico->id_doctor_examinador)) {
                            $inputx2 = [
                                'id_doctor_examinador' => $agenda->id_doctor1,
                                'ip_modificacion'      => $ip_cliente,
                                'id_usuariomod'        => $idusuario,
                            ];
                            //return $request->all();
                            $procedimiento_generico->update($inputx2);
                        }
                        $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                    }

                    $input_hc_protocolo = [
                        'id_hc_procedimientos' => $id_hc_procedimiento,
                        'hora_inicio'          => date('H:i:s'),
                        'hora_fin'             => date('H:i:s'),
                        'estado_final'         => ' ',
                        'ip_modificacion'      => $ip_cliente,
                        'hcid'                 => $agenda->hcid,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_creacion'          => $ip_cliente,
                        'created_at'           => date('Y-m-d H:i:s'),
                        'updated_at'           => date('Y-m-d H:i:s'),
                    ];
                    hc_protocolo::insert($input_hc_protocolo);

                    $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();

                    $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->get();
                    $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '2')->get();
                    $estudios   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get();
                    $biopsias   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '4')->get();
                    $hc_proc    = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                }
            }

            //la receta
            $hc_rec = hc_receta::where('id_hc', $agenda->hcid)->first();
            if (is_null($hc_rec)) {
                $input_hc_receta = [
                    'id_hc'           => $agenda->hcid,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                if ($agenda->proc_consul == '0') {
                    hc_receta::insert($input_hc_receta);
                }
            }

            $hc_rec = hc_receta::where('id_hc', $agenda->hcid)->first();
        }

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $mail = '';

        if ($agenda->pparentesco == 'Principal') {
            //$mail = User::find($agenda->id_paciente)->email;
            $mail = null;
            $u1   = User::find($agenda->id_paciente);
            if (!is_null($u1)) {
                $mail = $u1->email;
            }
        } else {
            $mail = User::find($agenda->id_usuario)->email;
        }

        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();

        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
        // dd($alergiasxpac->principio_activo);

        $laboratorio_externo = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '1')->get();
        $biopsias_1          = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        //$biopsias = hc_imagenes_protocolo::where('id_hc_protocolo',$protocolo->id)->where('estado', '4')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $agenda->id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();
        //return $biopsias_2;

        //dd($evoluciones);
        //Nueva funcionalidas Historial de Recetas
        //$xpaciente = Paciente::find($agenda->id_paciente);

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre')
            ->get();

        //Fin nueva Funcionalidad historial de Recetas
        $orden_biopsias = Hc4_Biopsias::where('id_paciente', $agenda->id_paciente)->groupBy("hc_id_procedimiento")->get();

        $paciente_observacion = Paciente_Observaciones::where('id_paciente', $agenda->id_paciente)->first();

        return view('hc_admision/historia/historiaclinica', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'evolucion' => $evolucion, 'evoluciones' => $evoluciones, 'seguros' => $seguros, 'subseguros' => $subseguros, 'mail' => $mail, 'protocolos' => $protocolos, 'protocolo' => $protocolo, 'procedimientos_pentax' => $procedimientos_pentax, 'proc_completo' => $proc_completo, 'doctores' => $doctores, 'anestesiologos' => $anestesiologos, 'enfermeros' => $enfermeros, 'imagenes' => $imagenes, 'documentos' => $documentos, 'estudios' => $estudios, 'biopsias' => $biopsias, 'hc_receta' => $hc_rec, 'alergiasxpac' => $alergiasxpac, 'protocolos_dia' => $protocolos_dia, 'evoluciones_proc' => $evoluciones_proc, 'child_pugh' => $child_pugh, 'hc_proc' => $hc_proc, 'laboratorio_externo' => $laboratorio_externo, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'hist_recetas' => $hist_recetas, 'orden_biopsias' => $orden_biopsias, 'paciente_observacion' => $paciente_observacion]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update2(Request $request, $id, $url_doctor)
    {
        

        $ruta = $request->ruta;
        //dd($request->all());
        $fecha  = date('Y-m-d H:i');
        $agenda = Agenda::findOrFail($id);
        //dd($agenda)
        $obs_anterior         = $agenda->observaciones;
        $estado_cita_anterior = $agenda->estado_cita;
        $fechaini_anterior    = $agenda->fechaini;
        $fechafin_anterior    = $agenda->fechafin;
        $estado_anterior      = $agenda->estado;
        $cortesia_anterior    = $agenda->cortesia;
        $id_doctor1_anterior  = $agenda->id_doctor1;
        $id_doctor2_anterior  = $agenda->id_doctor2;
        $id_doctor3_anterior  = $agenda->id_doctor3;
        $id_sala_anterior     = $agenda->id_sala;
        $paciente             = Paciente::find($agenda->id_paciente);
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $descripcion  = "";
        $descripcion2 = "";
        $descripcion3 = "CAMBIO: ";
        $est_cita     = $request['estado_cita'];
        $est          = '1';
        $bandera      = false;
        $cambio       = false;
        $flag2        = false;
        $proc         = $request['proc'];
        $aux_ant      = "";
        $aux          = "";
        $agproc       = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();

        $idPaciente = $request['id_paciente'];
        $post_procedimiento = 0;
        if ($request['post_procedimiento'] == "on") {
            $post_procedimiento = 1;
        }
        // $obser_admin="";
        if ($request['observaciones_admin'] != "" || $request['observaciones_admin'] != null) {
            $obser_admin = Paciente_Observaciones::where('id_paciente', $idPaciente)->first();
            $obser       = [
                'id_paciente'     => $idPaciente,
                'observacion'     => $request['observaciones_admin'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            if (!is_null($obser_admin)) {
                //actualiza
                Paciente_Observaciones::where('id_paciente', $idPaciente)->update($obser);
            } else {
                //crea
                Paciente_Observaciones::create($obser);
            }
        }

        if ($request->est_amb_hos == '1') {
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        } else {
            $omni = null;
        }
        $this->validateEdit($request);
        $this->validateMax1_2($request, $id);
        $this->validateInput4($request);

        if ($id_doctor1_anterior != '4444444444') {
            $permiso = Agenda_Permiso::where('id_usuario', $idusuario)->where('proc_consul', '1')->where('estado', '2')->first();

            if (is_null($permiso)) {
                $this->validatereagendar($request, $agenda);
            }
        }

        if ($request->id_seguro == '2') {

            $val_arr = [
                'id_empresa' => 'required',
            ];
            $msn_arr = [
                'id_empresa.required' => 'Seleccione la Empresa',
            ];
            $this->validate($request, $val_arr, $msn_arr);
        }

        if ($agenda->estado_cita != '4') {

            //cambio en campo
            if ($agenda->espid != $request['espid']) {
                $descripcion3 = $descripcion3 . " ESPECIALIZACIÓN,";
                $cambio       = true;
            }
            if ($agenda->id_seguro != $request['id_seguro']) {
                $descripcion3 = $descripcion3 . " SEGURO,";
                $cambio       = true;
            }

            if ($agenda->tipo_cita != $request['tipo_cita']) {
                $descripcion3 = $descripcion3 . " TIPO_CITA,";
                $cambio       = true;
            }

            if ($agenda->tipo_cita != $request['tipo_cita']) {
                $descripcion3 = $descripcion3 . " TIPO_CITA,";
                $cambio       = true;
            }

            if ($agenda->id_empresa != $request['id_empresa']) {
                $descripcion3 = $descripcion3 . " EMPRESA,";
                $cambio       = true;
            }
            if ($agenda->procedencia != $request['procedencia']) {
                $descripcion3 = $descripcion3 . " PROCEDENCIA,";
                $cambio       = true;
            }

            if (date('Y/m/d', strtotime($paciente->fecha_nacimiento)) != $request['fecha_nacimiento']) {
                //12/1/2018
                $descripcion3 = $descripcion3 . " NACIMIENTO,";
                $cambio       = true;
            }

            if ($agenda->supervisa_robles != $request['supervisa_robles']) {
                $descripcion3 = $descripcion3 . " SUPERVISA DR. ROBLES,";
                $cambio       = true;
            }

            if ($agenda->paciente_dr != $request['paciente_dr']) {
                $descripcion3 = $descripcion3 . " PACIENTE_DR,";
                $cambio       = true;
            }

            if ($agenda->solo_robles != $request['solo_robles']) {
                $descripcion3 = $descripcion3 . " SOLO LO PUEDE REALIZAR EL DR. ROBLES,";
                $cambio       = true;
            }

            if ($agenda->id_sala != $request['id_sala']) {
                $descripcion3 = $descripcion3 . " SALA,";
                $cambio       = true;
            }

            if (!is_null($request['id_ag_artxt'])) {

                $agenda_archivotxt = Agenda_archivo::find($request['id_ag_artxt']);
                if ($agenda_archivotxt->texto != $request['hc']) {
                    $descripcion3 = $descripcion3 . " AGENDA_ARCHIVO_TXT,";
                    $cambio       = true;
                }
            } else {
                if (!is_null($request['hc'])) {
                    $descripcion3 = $descripcion3 . " AGENDA_ARCHIVO_TXT,";
                    $cambio       = true;
                }
            }

            if ($agenda->proc_consul == '1') {
                //cambio procedimiento
                //cambio el primero
                if ($proc[0] != $agenda->id_procedimiento) {
                    $flag2 = true;
                } else {
                    if (count($proc) - 1 != $agproc->count()) {
                        $flag2 = true;
                    }
                    for ($x = 1; $x < count($proc); $x++) {
                        if ($x <= $agproc->count()) {
                            if ($proc[$x] != $agproc[$x - 1]->id_procedimiento) {
                                $flag2 = true;
                            }
                        }
                    }
                }
                if ($flag2) {
                    $descripcion3 = $descripcion3 . " PROCEDIMIENTO";
                }
            }
        }

        if ($agenda->teleconsulta != $request['teleconsulta']) {
            $descripcion3 = $descripcion3 . " TELECONSULTA,";
            $cambio       = true;
        }

        if ($agenda->tc != $request['tc']) {
            $descripcion3 = $descripcion3 . " TC,";
            $cambio       = true;
        }

        if ($agenda->est_amb_hos != $request['est_amb_hos']) {
            $descripcion3 = $descripcion3 . " INGRESO,";
            $cambio       = true;
        }
        if ($agenda->fecha_val != $request['fecha_val']) {
            $descripcion3 = $descripcion3 . " FECHA DE VALIDACION,";
            $cambio       = true;
        }
        if ($agenda->cod_val != $request['cod_val']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION,";
            $cambio       = true;
        }
        if ($agenda->validacion_cv_msp != $request['validacion_cv_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;
        }
        if ($agenda->validacion_nc_msp != $request['validacion_nc_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;
        }
        if ($agenda->validacion_sec_msp != $request['validacion_sec_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;
        }
        if ($agenda->adelantado != $request['adelantado']) {
            $descripcion3 = $descripcion3 . " ADELANTADO,";
            $cambio       = true;
        }

        //CORTESIA/OCUPACION/REFERIDO
        if ($agenda->cortesia != $request['cortesia']) {
            $descripcion3 = $descripcion3 . " CORTESIA,";
            $cambio       = true;
        }

        if ($agenda->vip != $request['vip']) {
            $descripcion3 = $descripcion3 . " VIP,";
            $cambio       = true;
        }

        if ($agenda->omni != $omni) {
            $descripcion3 = $descripcion3 . " OMNI,";
            $cambio       = true;
            //return "ok";
        }

        if ($paciente->ocupacion != $request['ocupacion']) {

            $descripcion3 = $descripcion3 . " OCUPACION,";
            $cambio       = true;
        }

        if ($paciente->referido != $request['referido']) {
            $descripcion3 = $descripcion3 . " REFERIDO,";
            $cambio       = true;
        }
        if($agenda->post_proc !== $post_procedimiento){
            $descripcion3 = $descripcion3 . " POST PROCEDIMIENTO,";
            $cambio       = true;
        }

        if ($agenda->observaciones != $request['observaciones']) {
            ///1408
            $descripcion3 = $descripcion3 . " OBSERVACION,";
            $cambio       = true;
        }

        if ($request['estado_cita'] == '0' && $url_doctor != '0') //Por Confirmar
        {
            

            if (!$cambio && !$flag2 && $request['archivo'] == null) {
                
                return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $request['unix']]);
            }
        }


        if ($request['estado_cita'] == '1') //confirmar
        {

            if ($agenda->estado_cita == '1' && $url_doctor != '0') {
                //validacion que debe valer solo par aparticular
                if ($request['id_seguro'] != 1 && $request['proc_consul'] != 0) {
                    if (!$cambio && !$flag2 && $request['archivo'] == null) {
                        return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $request['unix']]);
                    }
                }
            } else {
                //validacionde confirmacion particular
                if ($request['id_seguro'] != 1 && $request['proc_consul'] != 0) {
                    $this->validateInput3_3($request, $id, $agenda->fechaini, $agenda->fechafin);
                }
                $descripcion = "CONFIRMO LA CITA";
                $bandera     = true;
                $cambio      = true;

                $input = [
                    'estado'             => '1',
                    'estado_cita'        => $request['estado_cita'],
                    'observaciones'      => $request['observaciones'],
                    'id_usuarioconfirma' => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }

            //dd("entra44");
        }
        if ($request['estado_cita'] == '2') //reagendar
        {

            $bandera      = true;
            $est_cita     = '0';
            $descripcion  = "RE-AGENDO LA CITA";
            $nro_reagenda = $agenda->nro_reagenda + 1;

            //12/1/2018 validacion de sala ocupada
            $idhospital = Sala::find($request['id_sala'])->id_hospital;
            if ($idhospital == '2') {

                //$this->valida_salaPentax($request,'1',$id);
            } //--
            // 13/01/2018   Solo cuenta reagenda para procedimientos si:
            //cambia de hospital, fecha de inicio y fin a otro día
            if ($request['proc_consul'] == '1') {
                $flag_act     = '0';
                $flag_reag    = '0';
                $descripcion2 = "";

                $nro_reagenda = $agenda->nro_reagenda;
                if ($request['id_doctor1'] != $agenda->id_doctor1) {
                    $flag_act = '1';

                    $descripcion2 = $descripcion2 . " DOCTOR";
                }
                if ($request['id_doctor2'] != $agenda->id_doctor2) {
                    $flag_act = '1';

                    $descripcion2 = $descripcion2 . " ASISTENTE";
                }
                if ($request['id_doctor3'] != $agenda->id_doctor3) {
                    $flag_act = '1';

                    $descripcion2 = $descripcion2 . " ASISTENTE";
                }
                $req_idhos = Sala::find($request['id_sala'])->id_hospital;
                $ag_idhos  = "";
                $vid_sala  = "";
                if (!is_null($agenda->id_sala)) {
                    $ag_idhos = Sala::find($agenda->id_sala)->id_hospital;
                    $vid_sala = $agenda->id_sala;
                }

                if ($request['id_sala'] != $vid_sala) {

                    if ($req_idhos != $ag_idhos) {
                        $flag_reag = '1';

                        $descripcion2 = $descripcion2 . " HOSPITAL";
                        $nro_reagenda = $agenda->nro_reagenda + 1;
                    } else {
                        $flag_act = '1';

                        $descripcion2 = $descripcion2 . " SALA";
                    }
                }

                if ($request['inicio'] != null) {
                    if (date('Y-m-d H:i:s', strtotime($request['inicio'])) != $agenda->fechaini) {
                        $req_ini = substr($request['inicio'], 0, 10);
                        $new_ini = substr($agenda->fechaini, 0, 10);
                        $req_ini = date('Y/m/d', strtotime($req_ini));
                        $new_ini = date('Y/m/d', strtotime($new_ini));
                        if ($new_ini != $req_ini) {
                            $flag_reag = '1';

                            $descripcion2 = $descripcion2 . " FECHA";
                            $nro_reagenda = $agenda->nro_reagenda + 1;
                        } else {
                            $flag_act = '1';

                            $descripcion2 = $descripcion2 . " HORA";
                        }
                    }
                }
                if ($flag_reag == '1') {
                    $descripcion = "RE-AGENDO LA CITA";
                } elseif ($flag_act == '1') {
                    $descripcion = "ACTUALIZA";
                }
            } //-------

            if ($request['id_seguro'] != 1 && $request['proc_consul'] != 0) {
                $this->validateInput3_2($request, $id);
            }

            //valida horario del doctor
            $horariocontroller = new HorarioController();
            if ($request['id_seguro'] != 1 && $request['proc_consul'] != 0) {
                $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);
            }

            $input = [
                'estado'          => '1',
                'nro_reagenda'    => $nro_reagenda,
                'estado_cita'     => '0',
                'observaciones'   => $request['observaciones'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'id_doctor1'      => $request['id_doctor1'],
                'id_sala'         => $request['id_sala'],

            ];

            if ($request['proc_consul'] == '1') {
                $input2 = [
                    'id_doctor2' => $request['id_doctor2'],
                    'id_doctor3' => $request['id_doctor3'],
                ];
                $input = array_merge($input, $input2);
                if ($request['id_doctor2'] != '' || $request['id_doctor3'] != '') {
                    $this->validateInput5_2($request, $id);
                }
                if ($request['id_doctor2'] != '' && $request['id_doctor3'] != '') {

                    $this->validateDoctores($request);
                    $this->validateDoctores2($request);
                }

                if ($request['id_doctor2'] != '') {
                    $this->validateMax2_2($request, $id);
                }
                if ($request['id_doctor3'] != '') {
                    $this->validateMax3_2($request, $id);
                }
            }

            $agenda_datos = DB::table('agenda')->where('id', '=', $id)->get();
            $id_paciente  = $agenda_datos[0]->id_paciente;

            $tipo                = $agenda_datos[0]->proc_consul;
            $especialidad        = DB::table('especialidad')->where('id', '=', $agenda_datos[0]->espid)->get();
            $especialidad_nombre = $especialidad[0]->nombre;
            $paciente2           = DB::table('paciente')->where('id', '=', $id_paciente)->get();
            $usuario             = DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
            $correo              = $usuario[0]->email;
            $nombre_paciente     = $paciente2[0]->nombre1 . " ";
            if ($paciente2[0]->nombre2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente2[0]->nombre2 . " ";
            }
            $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido1 . " ";
            if ($paciente2[0]->apellido2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido2 . " ";
            }

            $doctor        = DB::table('users')->where('id', '=', $request['id_doctor1'])->get();
            $nombre_doctor = $doctor[0]->nombre1 . " ";
            if ($doctor[0]->nombre2 != '(N/A)') {
                $nombre_doctor = $nombre_doctor . $doctor[0]->nombre2 . " ";
            }
            $nombre_doctor = $nombre_doctor . $doctor[0]->apellido1 . " ";
            if ($doctor[0]->apellido2 != '(N/A)') {
                $nombre_doctor = $nombre_doctor . $doctor[0]->apellido2 . " ";
            }
            $sala     = DB::table('sala')->where('id', '=', $request['id_sala'])->get();
            $cnombre  = $sala[0]->nombre_sala;
            $hospital = DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
            $hnombre  = $hospital[0]->nombre_hospital;

            $hdireccion = $hospital[0]->direccion;
        }

        if ($request['estado_cita'] == '3' || $request['estado_cita'] == '-1') {
            //suspender
            $bandera = true;
            $est     = '0';
            if ($request['estado_cita'] == '3') {
                $descripcion = "SUSPENDIO LA CITA";
            }
            if ($request['estado_cita'] == '-1') {
                $descripcion = "NO ASISTE A LA CITA";
            }

            $input = [
                'estado_cita'     => $request['estado_cita'],
                'observaciones'   => $request['observaciones'],
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,

            ];

            $mensajes    = ['observaciones.required' => 'Ingresa una Observación.'];
            $constraints = ['observaciones' => 'required'];
            $this->validate($request, $constraints, $mensajes);

            //si existe en pentax debe dejar suspendido el paciente

            $s_pentax = Pentax::where('id_agenda', $agenda->id)->first();

            if (!is_null($s_pentax)) {

                //log
                $input_log_p = [
                    'id_pentax'       => $s_pentax->id,
                    'tipo_cambio'     => "ESTADO",
                    'descripcion'     => "SUSPENDIDO",
                    'estado_pentax'   => '5',
                    'id_seguro'       => $s_pentax->id_seguro,
                    'id_subseguro'    => $s_pentax->id_subseguro,
                    'procedimientos'  => '',
                    'id_doctor1'      => $s_pentax->id_doctor1,
                    'id_doctor2'      => $s_pentax->id_doctor2,
                    'id_doctor3'      => $s_pentax->id_doctor3,
                    'id_sala'         => $s_pentax->id_sala,
                    'observacion'     => 'Suspendido desde Recepción: ' . $request['observaciones'],
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ];

                Pentax_log::create($input_log_p);

                //suspender
                $input_px = [
                    'estado_pentax'   => '5',
                    'id_sala'         => $s_pentax->id_sala,
                    'id_seguro'       => $s_pentax->id_seguro,
                    'id_subseguro'    => $s_pentax->id_subseguro,
                    'id_doctor1'      => $s_pentax->id_doctor1,
                    'id_doctor2'      => $s_pentax->id_doctor2,
                    'id_doctor3'      => $s_pentax->id_doctor3,
                    'observacion'     => 'Suspendido desde Recepción: ' . $request['observaciones'],
                    'ingresa_prepa'   => $s_pentax->ingresa_prepa,
                    'ingresa_proc'    => $s_pentax->ingresa_proc,
                    'ingresa_rec'     => $s_pentax->ingresa_rec,
                    'ingresa_alt'     => $s_pentax->ingresa_alt,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];

                $s_pentax->update($input_px);
            }

            //pentax
        }

        if ($request['estado_cita'] == '4' && $agenda->estado_cita != '4') //ASISTIÓ
        {
            if ($agenda->espid == '10' && $agenda->id_doctor1 == '4444444444') {
                $empresaid = $agenda->id_empresa;
                if ($agenda->id_empresa == null) {
                    $empresaid = '0993075000001';
                }

                $agenda->update([
                    'estado_cita'     => 4,
                    'id_empresa'      => $empresaid,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ]);

                Log_agenda::create([
                    'id_agenda'          => $agenda->id,
                    'estado_cita_ant'    => $estado_cita_anterior,
                    'fechaini_ant'       => $fechaini_anterior,
                    'fechafin_ant'       => $fechafin_anterior,
                    'estado_ant'         => $estado_anterior,
                    'cortesia_ant'       => $cortesia_anterior,
                    'observaciones_ant'  => $obs_anterior,
                    'id_doctor1_ant'     => $id_doctor1_anterior,
                    'id_doctor2_ant'     => $id_doctor2_anterior,
                    'id_doctor3_ant'     => $id_doctor3_anterior,
                    'id_sala_ant'        => $id_sala_anterior,

                    'estado_cita'        => $est_cita,
                    'fechaini'           => $agenda->fechaini,
                    'fechafin'           => $agenda->fechafin,
                    'estado'             => $agenda->estado,
                    'cortesia'           => $request->cortesia,
                    'observaciones'      => $request['observaciones'],
                    'id_doctor1'         => $request['id_doctor1'],
                    'id_doctor2'         => $request['id_doctor2'],
                    'id_doctor3'         => $request['id_doctor3'],
                    'id_sala'            => $request['id_sala'],

                    'descripcion'        => $descripcion,
                    'descripcion2'       => $descripcion2,
                    'descripcion3'       => $descripcion3,
                    'campos_ant'         => "ESP:" . $agenda->espid . " SEG:" . $agenda->id_seguro . " ING:" . $agenda->est_amb_hos . " PRO:" . $agenda->id_procedimiento . ";" . $aux_ant . " PEN:" . $agenda->procedencia . " PDR:" . $agenda->paciente_dr, //12/1/2018
                    'campos'             => "ESP:" . $request['espid'] . " SEG:" . $request['id_seguro'] . " ING:" . $request['est_amb_hos'] . " PRO:" . $aux . " PEN:" . $request['procedencia'] . " PDR:" . $request['paciente_dr'] . " FNA:" . $request['fecha_nacimiento'],
                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);

                if ($url_doctor != '0') {
                    return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $request['unix']]);
                } else {
                    if ($ruta == 'tsalas') {

                        return redirect()->route('salas_todas.cargar', ['id' => $agenda->id]);
                    }
                    return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']]);
                }
            }
            //return "ok";
            $est_cita = $agenda->estado_cita;
            if (!$cambio && !$flag2 && $request['archivo'] == null) {

                return redirect()->route('admisiones.admision', ['id' => $request['id_paciente'], 'cita' => $id, 'ruta' => $url_doctor, 'unix' => $request['unix'], 'i' => $paciente->id_seguro]);
            }
        }

        if ($request['inicio'] == '') {
            $ini = $agenda->fechaini;
        } else {
            $ini = $request['inicio'];
        }
        if ($request['fin'] == '') {
            $fin = $agenda->fechafin;
        } else {
            $fin = $request['fin'];
        }

        

        /*if($agenda->cortesia!=$request['cortesia']){//solo cambio la cortesia
        $descripcion2="CAMBIO CORTESIA";
        $input_cortesia = [

        'cortesia' => $request['cortesia'],
        'id_usuariomod' => $idusuario,
        'ip_modificacion' => $ip_cliente,

        ];
        }*/

        //paciente del  Doctor
        $paciente_dr = 0;
        if ($request->id_seguro != '2' && $request->id_seguro != '3' && $request->id_seguro != '5' && $request->id_seguro != '6') {
            if (is_null($paciente->paciente_doctor)) {
                if ($request->paciente_dr == '1') {

                    $arr_pac_doc = [
                        'id_paciente'     => $paciente->id,
                        'id_usuario'      => $request['id_doctor1'],
                        'id_usuariocrea'  => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,

                    ];
                    Paciente_Doctor::create($arr_pac_doc);
                    $paciente_dr = 1;
                }
            } else {

                if ($paciente->paciente_doctor->id_usuario == $request->id_doctor1) {

                    $paciente_dr = 1;
                }
            }
        }

        $espid = $request->espid;

        if ($request->espid == null) {
            $espid = $agenda->espid;
        }
        
        //return $omni;
        if ($cambio) {

            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')
            //dd();

            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')
            
           
            if ($agenda->id_doctor1 != '4444444444' && $agenda->id_doctor1 != '444444444A' && $agenda->id_doctor1 != '444444444B') {
                
                if ($request['id_seguro'] == '3') {
                    // dd($request['id_seguro']);
                    if ($request['adelantado'] == 1) {
                        //dd($request['adelantado']);
                        $rules_observacionissfa = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionissfa = [
                        'observaciones.required' => 'Ingrese una observacion',
                    ];
                    $this->validate($request, $rules_observacionissfa, $mensajes_observacionissfa);
                }

                $rules_issfa = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];

                $mensajes_issfa = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',
                ];

                $this->validate($request, $rules_issfa, $mensajes_issfa);
            } elseif ($request['id_seguro'] == '5') {
                 
                if ($request['adelantado'] == 1) {
                    $rules_observacionmsp = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionmsp = [
                        'observaciones.required' => 'Ingrese la fecha de validación',
                    ];
                   // $this->validate($request, $rules_observacionmsp, $mensajes_observacionmsp);
                }

                

                $rules_msp = [
                    'fecha_val'          => 'required',
                    'validacion_cv_msp'  => 'required',
                    'validacion_nc_msp'  => 'required',
                    'validacion_sec_msp' => 'required',

                ];
                $mensajes_msp = [
                    'fecha_val.required'          => 'Ingrese la fecha de validación',
                    'validacion_cv_msp.required'  => 'codigo',
                    'validacion_nc_msp.required'  => 'numero',
                    'validacion_sec_msp.required' => 'secuencia',

                ];

                

               //$this->validate($request, $rules_msp, $mensajes_msp);

              

            } elseif ($request['id_seguro'] == '6') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionisspol = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacionisspol = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacionisspol, $mensajes_observacionisspol);
                }

                $rules_isspol = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_isspol = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                $this->validate($request, $rules_isspol, $mensajes_isspol);
            } elseif ($request['id_seguro'] == '2') {

                if ($request['adelantado'] == 1) {
                    $rules_observacioniess = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacioniess = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    //$this->validate($request, $rules_observacioniess, $mensajes_observacioniess);
                }

                $rules_iess = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_iess = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                //$this->validate($request, $rules_iess, $mensajes_iess);
            }

                //cambia especialidad, seguro, ingreso o empresa
            }
                $cv                    = $request['validacion_cv_msp'];
                $nc                    = $request['validacion_nc_msp'];
                $sec                   = $request['validacion_sec_msp'];
                $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;
                //dd($request->adelantado);
                $input_cambios = [
                    'espid'                 => $espid,
                    'id_seguro'             => $request['id_seguro'],
                    'est_amb_hos'           => $request['est_amb_hos'],
                    'tipo_cita'             => $request['tipo_cita'],
                    'id_empresa'            => $request['id_empresa'],
                    'procedencia'           => $request['procedencia'],
                    //'paciente_dr' => $request['paciente_dr'],
                    'paciente_dr'           => $paciente_dr,
                    'vip'                   => $request['vip'],
                    'id_usuariomod'         => $idusuario,
                    'ip_modificacion'       => $ip_cliente,
                    'id_sala'               => $request['id_sala'],
                    'cortesia'              => $request['cortesia'],
                    'observaciones'         => $request['observaciones'],
                    'supervisa_robles'      => $request['supervisa_robles'],
                    'solo_robles'           => $request['solo_robles'],
                    'teleconsulta'          => $request['teleconsulta'],
                    'tc'                    => $request['tc'],
                    'omni'                  => $omni,
                    'fecha_val'             => $request['fecha_val'],
                    'cod_val'               => $request['cod_val'],
                    'validacion_cv_msp'     => $request['validacion_cv_msp'],
                    'validacion_nc_msp'     => $request['validacion_nc_msp'],
                    'validacion_sec_msp'    => $request['validacion_sec_msp'],
                    'codigo_validacion_msp' => $codigo_validacion_msp,
                    'adelantado'            => $request['adelantado'],

            ];
        }
        
        if ($flag2) {
            //procedimientos
            $cv                    = $request['validacion_cv_msp'];
            $nc                    = $request['validacion_nc_msp'];
            $sec                   = $request['validacion_sec_msp'];
            $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

            $input_proc = [
                'id_procedimiento'      => $proc[0],
                'id_usuariomod'         => $idusuario,
                'ip_modificacion'       => $ip_cliente,
                'fecha_val'             => $request['fecha_val'],
                'cod_val'               => $request['cod_val'],
                'validacion_cv_msp'     => $request['validacion_cv_msp'],
                'validacion_nc_msp'     => $request['validacion_nc_msp'],
                'validacion_sec_msp'    => $request['validacion_sec_msp'],
                'codigo_validacion_msp' => $codigo_validacion_msp,
                'post_proc'             => $post_procedimiento,

            ];
        }

        

        if ($agenda->proc_consul == '1') {
            foreach ($agproc as $a1) {
                $aux_ant = $aux_ant . $a1->id_procedimiento . ";";
            }
            if ($agenda->estado_cita != '4') {
                foreach ($proc as $a2) {
                    $aux = $aux . $a2 . ";";
                }
            }
        }
        $fecha_ant = "";
        if (!is_null($paciente->fecha_nacimiento)) {
            $fecha_ant = date('Y/m/d', strtotime($paciente->fecha_nacimiento));
        }

        if ($bandera) {
            $agenda->update($input);
        }
        
        if ($agenda->estado_cita != '4') {
            if ($cambio) {
                
                 
                //cambia especialidad, seguro, ingreso o empresa
                Log_agenda::create([
                    'id_agenda'          => $agenda->id,
                    'estado_cita_ant'    => $estado_cita_anterior,
                    'fechaini_ant'       => $fechaini_anterior,
                    'fechafin_ant'       => $fechafin_anterior,
                    'estado_ant'         => $estado_anterior,
                    'cortesia_ant'       => $cortesia_anterior,
                    'observaciones_ant'  => $obs_anterior,
                    'id_doctor1_ant'     => $id_doctor1_anterior,
                    'id_doctor2_ant'     => $id_doctor2_anterior,
                    'id_doctor3_ant'     => $id_doctor3_anterior,
                    'id_sala_ant'        => $id_sala_anterior,

                    'estado_cita'        => $est_cita,
                    'fechaini'           => $ini,
                    'fechafin'           => $fin,
                    'estado'             => $est,
                    'cortesia'           => $request->cortesia,
                    'observaciones'      => $request['observaciones'],
                    'id_doctor1'         => $request['id_doctor1'],
                    'id_doctor2'         => $request['id_doctor2'],
                    'id_doctor3'         => $request['id_doctor3'],
                    'id_sala'            => $request['id_sala'],

                    'descripcion'        => $descripcion,
                    'descripcion2'       => $descripcion2,
                    'descripcion3'       => $descripcion3,
                    'campos_ant'         => "ESP:" . $agenda->espid . " SEG:" . $agenda->id_seguro . " ING:" . $agenda->est_amb_hos . " PRO:" . $agenda->id_procedimiento . ";" . $aux_ant . " PEN:" . $agenda->procedencia . " PDR:" . $agenda->paciente_dr . " FNA:" . $fecha_ant, //12/1/2018
                    'campos'             => "ESP:" . $request['espid'] . " SEG:" . $request['id_seguro'] . " ING:" . $request['est_amb_hos'] . " PRO:" . $aux . " PEN:" . $request['procedencia'] . " PDR:" . $request['paciente_dr'] . " FNA:" . $request['fecha_nacimiento'],
                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);
                $agenda->update($input_cambios);
                $input_paciente = [
                    'id_seguro'        => $request['id_seguro'],
                    'id_subseguro'     => null,
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'ocupacion'        => $request['ocupacion'],
                    'referido'         => $request['referido'],
                ];
                $paciente->update($input_paciente);
            }
            if ($agenda->proc_consul == '1') {
                if ($flag2) {
                    //procedimientos
                    foreach ($agproc as $ad) {
                        $ad->delete();
                    }
                    $agenda->update($input_proc);
                    foreach ($proc as $value) {
                        if ($proc[0] != $value) {
                            AgendaProcedimiento::create([
                                'id_agenda'        => $id,
                                'id_procedimiento' => $value,

                                'ip_creacion'      => $ip_cliente,
                                'ip_modificacion'  => $ip_cliente,
                                'id_usuariocrea'   => $idusuario,
                                'id_usuariomod'    => $idusuario,
                            ]);
                        }
                    }
                }
            }

            if (is_null($request['id_ag_artxt']) && !is_null($request['hc'])) {

                Agenda_archivo::create([
                    'id_agenda'       => $id,
                    'tipo_documento'  => 'txt',
                    'texto'           => $request['hc'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }

            if (!is_null($request['id_ag_artxt'])) {
                if ($agenda_archivotxt->texto != $request['hc']) {
                    $input_hc_txt = [
                        'texto'           => $request['hc'],
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ];
                    $agenda_archivotxt->update($input_hc_txt);
                }
            }
            if ($request['archivo'] != null) {

                if (!is_null($request['id_ag_ar'])) {

                    Agenda_archivo::find($request['id_ag_ar'])->delete();
                }

                $input_archivo = [
                    'id_agenda'       => $id,
                    'tipo_documento'  => "HCAGENDA",
                    'descripcion'     => "Historia Clinica creada de la agenda",
                    'ruta'            => "/hc_agenda/",
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];

                $id_archivo = Agenda_archivo::insertGetId($input_archivo);

                $this->subir_archivo_validacion($request, $id, $id_archivo);
            }
        } else {
            if ($cambio) {
                //cambia especialidad, seguro, ingreso o empresa

                Log_agenda::create([
                    'id_agenda'          => $agenda->id,
                    'estado_cita_ant'    => $agenda->estado_cita,
                    'fechaini_ant'       => $agenda->fechaini,
                    'fechafin_ant'       => $agenda->fechafin,
                    'estado_ant'         => $agenda->estado,
                    'cortesia_ant'       => $agenda->cortesia,
                    'observaciones_ant'  => $obs_anterior,
                    'id_doctor1_ant'     => $agenda->id_doctor1,
                    'id_doctor2_ant'     => $agenda->id_doctor2,
                    'id_doctor3_ant'     => $agenda->id_doctor3,
                    'id_sala_ant'        => $agenda->id_sala,

                    'estado_cita'        => $est_cita,
                    'fechaini'           => $ini,
                    'fechafin'           => $fin,
                    'estado'             => $est,
                    'cortesia'           => $request->cortesia,
                    'observaciones'      => $request['observaciones'],
                    'id_doctor1'         => $request['id_doctor1'],
                    'id_doctor2'         => $request['id_doctor2'],
                    'id_doctor3'         => $request['id_doctor3'],
                    'id_sala'            => $request['id_sala'],

                    'descripcion'        => $descripcion,
                    'descripcion2'       => $descripcion2,
                    'descripcion3'       => $descripcion3,
                    'campos_ant'         => "ESP:" . $agenda->espid . " SEG:" . $agenda->id_seguro . " ING:" . $agenda->est_amb_hos . " PRO:" . $agenda->id_procedimiento . ";" . $aux_ant . " PEN:" . $agenda->procedencia . " PDR:" . $agenda->paciente_dr . " FNA:" . $fecha_ant, //12/1/2018
                    'campos'             => "ESP:" . $request['espid'] . " SEG:" . $request['id_seguro'] . " ING:" . $request['est_amb_hos'] . " PRO:" . $aux . " PEN:" . $request['procedencia'] . " PDR:" . $request['paciente_dr'] . " FNA:" . $request['fecha_nacimiento'],
                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);

                $input_cambios2 = [

                    'est_amb_hos'           => $request['est_amb_hos'],
                    'id_usuariomod'         => $idusuario,
                    'ip_modificacion'       => $ip_cliente,
                    'cortesia'              => $request['cortesia'],
                    'observaciones'         => $request['observaciones'],
                    'omni'                  => $omni,
                    'teleconsulta'          => $request['teleconsulta'],
                    'tc'                    => $request['tc'],

                    'fecha_val'             => $request['fecha_val'],
                    'cod_val'               => $request['cod_val'],
                    'validacion_cv_msp'     => $request['validacion_cv_msp'],
                    'validacion_nc_msp'     => $request['validacion_nc_msp'],
                    'validacion_sec_msp'    => $request['validacion_sec_msp'],
                    'codigo_validacion_msp' => $codigo_validacion_msp,
                    'adelantado'            => $request['adelantado'],
                    'post_proc'             => $post_procedimiento,

                ];

                $agenda->update($input_cambios2);
                $input_paciente2 = [
                    'id_seguro'        => $request['id_seguro'],
                    'id_subseguro'     => null,
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'ocupacion'        => $request['ocupacion'],
                    'referido'         => $request['referido'],
                ];
                $paciente->update($input_paciente2);
            }
        }

        if ($bandera) {
            //$agenda->update($input);
            //envio de correos electronicos
            if ($agenda->id_doctor1 != '4444444444') {
                if ($request['estado_cita'] == '1') //confirmar
                {
                    $agenda = Agenda::findOrFail($id);
                    $inicio = $agenda->fechaini;
                    $tipo   = $agenda->proc_consul;

                    $id_paciente = $agenda->id_paciente;

                    $especialidad        = DB::table('especialidad')->where('id', '=', $agenda->espid)->get();
                    $especialidad_nombre = $especialidad[0]->nombre;
                    $paciente2           = DB::table('paciente')->where('id', '=', $id_paciente)->get();
                    $usuario             = DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
                    $correo              = $usuario[0]->email;
                    $nombre_paciente     = $paciente2[0]->nombre1 . " ";
                    if ($paciente2[0]->nombre2 != '(N/A)') {
                        $nombre_paciente = $nombre_paciente . $paciente2[0]->nombre2 . " ";
                    }
                    $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido1 . " ";
                    if ($paciente2[0]->apellido2 != '(N/A)') {
                        $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido2 . " ";
                    }

                    $doctor        = DB::table('users')->where('id', '=', $request['id_doctor1'])->get();
                    $nombre_doctor = $doctor[0]->nombre1 . " ";
                    if ($doctor[0]->nombre2 != '(N/A)') {
                        $nombre_doctor = $nombre_doctor . $doctor[0]->nombre2 . " ";
                    }
                    $nombre_doctor = $nombre_doctor . $doctor[0]->apellido1 . " ";
                    if ($doctor[0]->apellido2 != '(N/A)') {
                        $nombre_doctor = $nombre_doctor . $doctor[0]->apellido2 . " ";
                    }
                    $sala     = DB::table('sala')->where('id', '=', $request['id_sala'])->get();
                    $cnombre  = $sala[0]->nombre_sala;
                    $hospital = DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
                    $hnombre  = $hospital[0]->nombre_hospital;

                    $hdireccion = $hospital[0]->direccion;
                    if ($tipo == 1) {

                        $procedimiento_enviar = null;

                        $procedimiento_de_agenda = $agenda->id_procedimiento;
                        $procedimiento_a         = DB::table('procedimiento')->where('id', '=', $procedimiento_de_agenda)->get();
                        $procedimiento_enviar    = $procedimiento_a[0]->nombre . '+' . $procedimiento_enviar;

                        $procedimientos = DB::table('agenda_procedimiento')->where('id_agenda', '=', $id)->get();
                        foreach ($procedimientos as $value) {
                            $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value->id_procedimiento)->get();

                            $procedimiento_enviar = $procedimiento_a[0]->nombre . '+' . $procedimiento_enviar;
                        }

                        $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);

                        $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $agenda->fechaini, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);
                        Mail::send('mails.procedimiento', $avanza, function ($msj) use ($correo) {
                            $msj->subject('Reservación de procedimiento médico IECED');
                            $msj->to($correo);
                            $msj->bcc('torbi10@hotmail.com');
                        });
                    }
                    if ($tipo == 0) {

                        $avanza = array("nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $inicio, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);

                        Mail::send('mails.consulta', $avanza, function ($msj) use ($correo) {
                            $msj->subject('Reservacion de cita médica IECED');
                            $msj->to($correo);
                            $msj->bcc('torbi10@hotmail.com');
                        });
                    }
                }
            }
        }

        if ($request['estado_cita'] == '4') {
            return redirect()->route('admisiones.admision', ['id' => $request['id_paciente'], 'cita' => $id, 'ruta' => $url_doctor, 'unix' => $request['unix'], 'i' => $paciente->id_seguro]);
        } elseif ($url_doctor != '0') {
            return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $request['unix']]);
        } else {
            if ($ruta == 'tsalas') {

                return redirect()->route('salas_todas.cargar', ['id' => $agenda->id]);
            }
            return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']]);
        }
    }

    public function updatereunion(Request $request, $id)
    {

        $agenda     = Agenda::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $descripcion = "SUSPENDIO LA CITA";

        $input = [
            'estado_cita'     => '3',
            'observaciones'   => $request['observaciones'],
            'estado'          => '0',
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        Log_agenda::create([
            'id_agenda'          => $agenda->id,
            'estado_cita_ant'    => $agenda->estado_cita,
            'fechaini_ant'       => $agenda->fechaini,
            'fechafin_ant'       => $agenda->fechafin,
            'estado_ant'         => $agenda->estado,
            'cortesia_ant'       => $agenda->cortesia,
            'observaciones_ant'  => $agenda->observaciones,
            'id_doctor1_ant'     => $agenda->id_doctor1,
            'id_doctor2_ant'     => $agenda->id_doctor2,
            'id_doctor3_ant'     => $agenda->id_doctor3,
            'id_sala_ant'        => $agenda->id_sala,

            'estado_cita'        => '3',
            'fechaini'           => $agenda->fechaini,
            'fechafin'           => $agenda->fechafin,
            'estado'             => '0',
            'cortesia'           => $agenda->cortesia,
            'observaciones'      => $request['observaciones'],
            'id_doctor1'         => $agenda->id_doctor1,
            'id_doctor2'         => $agenda->id_doctor2,
            'id_doctor3'         => $agenda->id_doctor3,
            'id_sala'            => $agenda->id_sala,

            'descripcion'        => $descripcion,
            'descripcion2'       => '',
            'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,

        ]);

        $agenda::where('id', $id)
            ->update($input);

        return redirect()->route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => 0]);
    }

    public function reunionsearch($id)
    {

        $agenda = Agenda::find($id);

        return view('agenda/editreunion', ['id' => $id, 'agenda' => $agenda]);
    }

    public function reunionedit($id)
    {

        $agenda = Agenda::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        //agendas
        $id_doc  = $agenda->id_doctor1;
        $cagenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->get();

        $cagenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->get();

        $cagenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where(function ($query) use ($id_doc) {
                $query->where([['agenda.id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->get();
        //agendas

        return view('agenda/editreunion2', ['id' => $id, 'agenda' => $agenda, 'salas' => $salas, 'cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3]);
    }

    public function updatereunion2(Request $request, $id)
    {

        $agenda     = Agenda::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['estado_cita'] == 3) {

            $descripcion = "SUSPENDIO LA CITA";
            $fechaini    = $agenda->fechaini;
            $fechafin    = $agenda->fechafin;
            $estado_cita = '3';
            $estado      = '0';
            $input       = [
                'estado_cita'     => '3',
                'observaciones'   => $request['observaciones'],
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
        }

        if ($request['estado_cita'] == '2') //reagendar
        {
            $estado_cita = '1';
            $estado      = '1';
            $fechaini    = $request['inicio'];
            $fechafin    = $request['fin'];
            $descripcion = "RE-AGENDO LA CITA";
            $this->validateInput3_2($request, $id);
            $input = [
                'nro_reagenda'    => $agenda->nro_reagenda + 1,
                'estado_cita'     => '1',
                'observaciones'   => $request['observaciones'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'id_doctor1'      => $request['id_doctor1'],
                'id_sala'         => $request['id_sala'],
            ];
        }

        if ($request['estado_cita'] == '2' || $request['estado_cita'] == '3') {
            $this->validateInput4($request);

            Log_agenda::create([
                'id_agenda'          => $agenda->id,
                'estado_cita_ant'    => $agenda->estado_cita,
                'fechaini_ant'       => $agenda->fechaini,
                'fechafin_ant'       => $agenda->fechafin,
                'estado_ant'         => $agenda->estado,
                'cortesia_ant'       => $agenda->cortesia,
                'observaciones_ant'  => $agenda->observaciones,
                'id_doctor1_ant'     => $agenda->id_doctor1,
                'id_doctor2_ant'     => $agenda->id_doctor2,
                'id_doctor3_ant'     => $agenda->id_doctor3,
                'id_sala_ant'        => $agenda->id_sala,

                'estado_cita'        => $estado_cita,
                'fechaini'           => $fechaini,
                'fechafin'           => $fechafin,
                'estado'             => $estado,
                'cortesia'           => $agenda->cortesia,
                'observaciones'      => $request['observaciones'],
                'id_doctor1'         => $agenda->id_doctor1,
                'id_doctor2'         => $agenda->id_doctor2,
                'id_doctor3'         => $agenda->id_doctor3,
                'id_sala'            => $request['id_sala'],

                'descripcion'        => $descripcion,
                'descripcion2'       => '',
                'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,

            ]);

            $agenda::where('id', $id)
                ->update($input);
        }

        return redirect()->route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => 0]);
    }

    public function archivo567(Request $request)
    {

        $path       = public_path() . '/app/hc/';
        $files      = $request->file('foto');
        $id_agenda  = $request['id'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $i          = 1;
        foreach ($files as $file) {

            $input_archivo = [
                'id_agenda'       => $id_agenda,
                'tipo_documento'  => "HCAGENDA",
                'descripcion'     => "Historia Clinica creada de la agenda",
                'ruta'            => "/hc_agenda/",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            $id_archivo = Agenda_archivo::insertGetId($input_archivo);

            $extension = strtolower($file->getClientOriginalExtension());
            //$nuevo_nombre="hc_ESCPRO_".$id_agenda."_".$id_archivo.".".$extension;
            $nuevo_nombre = $file->getClientOriginalName();
            $r1           = Storage::disk('hc_agenda')->put($nuevo_nombre, \File::get($file));
            if ($r1) {

                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $idusuario  = Auth::user()->id;
                date_default_timezone_set('America/Guayaquil');

                $archivo_historico                  = Agenda_archivo::find($id_archivo);
                $archivo_historico->archivo         = $nuevo_nombre;
                $archivo_historico->ip_modificacion = $ip_cliente;
                $archivo_historico->id_usuariomod   = $idusuario;
                $r2                                 = $archivo_historico->save();

                $i = $i + 1;
            }
        }
    }

    public function suspendidas($id, $i)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = User::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')
            ->get();

        $paciente = paciente::find($i);

        //SI NO SE ENCUENTRA EL PACIENTE
        if ($paciente == array() && $i != '0') {

            return redirect()->route('agenda.paciente', ['id' => $id, 'i' => $i]);
        }

        $user      = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get(); //3=DOCTORES;
        $enfermero = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get(); //6=ENFERMEROS;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->where('proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->where(function ($query) use ($id) {
                $query->where([['agenda.id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['agenda.id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['agenda.id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $procedimiento = Procedimiento::all();
        $empresa       = Empresa::all();
        $seguro        = Seguro::all();
        $especialidad  = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();
        return view('agenda/calendario', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'procedimiento2' => $procedimiento, 'i' => $i, 'agenda' => $agenda, 'agenda2' => $agenda2, 'agenda3' => $agenda3, 'especialidad' => $especialidad, 'empresa' => $empresa, 'enfermero' => $enfermero, 'seguro' => $seguro, 'versuspendidas' => '1']);
    }

    public function foto567(Request $request)
    {

        $id    = $request['id'];
        $fotos = DB::table('agenda_archivo')->where('id', '=', $id)->get();
        return view('agenda/foto', ['hcagenda' => $fotos]);
    }

    public function eliminarfoto($id)
    {

        $archivo   = DB::table('agenda_archivo')->where('id', '=', $id)->get();
        $id_agenda = $archivo[0]->id_agenda;
        $agenda    = DB::table('agenda')->where('id', '=', $id_agenda)->get();
        $id_doctor = $agenda[0]->id_doctor1;
        $foto      = $archivo[0]->archivo;

        $r1 = Storage::disk('hc_agenda')->delete($foto);
        if ($r1) {
            agenda_archivo::destroy($id);
        }
        return redirect()->route('agenda.edit2', ['agenda' => $id_agenda, 'doctor' => $id_doctor]);
    }

    public function consulta_ag($id_doc, $fecha_cons)
    {

        //$usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','=',1)->get(); //3=DOCTORES;
        $usuarios      = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        $fecha_cons    = date('Y-m-d', $fecha_cons);
        $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($fecha_cons)));
        $fecha_despues = Date('Y-m-d', strtotime('+1 month', strtotime($fecha_cons)));

        //agendas
        $cagenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('agenda.fechaini', [$fecha_antes, $fecha_despues])
            ->get();

        $cagenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('agenda.fechaini', [$fecha_antes, $fecha_despues])
            ->get();

        $cagenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where(function ($query) use ($id_doc) {
                $query->where([['agenda.id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('agenda.fechaini', [$fecha_antes, $fecha_despues])
            ->get();

        //$fecha_cons = date('Y-m-d', $fecha_cons);
        $horario = DB::table('horario_doctor')
            ->where('id_doctor', '=', $id_doc)->orderBy('ndia')
            ->orderBy('hora_ini')
            ->get();
        //horas extras aceptadas
        $aceptadas_extra = Excepcion_Horario::where('id_doctor1', '=', $id_doc)->get();

        return view('agenda/cons_agenda', ['cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3, 'id_doc' => $id_doc, 'fecha_cons' => $fecha_cons, 'usuarios' => $usuarios, 'horario' => $horario, 'extra' => $aceptadas_extra]);
    }

    public function cambiarhorario($id, $start, $end)
    {

        $new_val = $this->Movimientos_Permitidos($id, $start, $end);
        //return "hola";
        if ($new_val != "OK") {
            return $new_val;
        }
        //date_default_timezone_set('America/Guayaquil');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $agendamiento = Agenda::find($id);
        $id_doctor1   = $agendamiento->id_doctor1;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2       = date('Y-m-d H:i', $start);
        $end2         = date('Y-m-d H:i', $end);
        $terminar     = strtotime('-1 minute', strtotime($end2));
        $terminar     = date('Y-m-d H:i', $terminar);
        $nro_reagenda = $agendamiento->nro_reagenda;
        //return "entra";
        $agenda_nueva = DB::select("SELECT *
            FROM agenda
            WHERE id_doctor1 = '" . $id_doctor1 . "' AND  id != " . $id . " AND (fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id . "  AND  estado > 0 AND id !=  0 ORDER BY fechaini ASC;");
        //return $agenda_nueva;
        $id_doctor = $agendamiento->id_doctor1;
        date_default_timezone_set('America/Guayaquil');
        $input = [
            'nro_reagenda'    => $nro_reagenda,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'fechaini'        => $start2,
            'fechafin'        => $end2,
        ];
        Agenda::where('id', $id)
            ->update($input);
        //falta log de agenda
        Log_agenda::create([
            'id_agenda'          => $agendamiento->id,
            'estado_cita_ant'    => $agendamiento->estado_cita,
            'fechaini_ant'       => $agendamiento->fechaini,
            'fechafin_ant'       => $agendamiento->fechafin,
            'estado_ant'         => $agendamiento->estado,
            'cortesia_ant'       => $agendamiento->cortesia,
            'observaciones_ant'  => $agendamiento->observaciones,
            'id_doctor1_ant'     => $agendamiento->id_doctor1,
            'id_doctor2_ant'     => $agendamiento->id_doctor2,
            'id_doctor3_ant'     => $agendamiento->id_doctor3,
            'id_sala_ant'        => $agendamiento->id_sala,

            'estado_cita'        => $agendamiento->estado_cita,
            'fechaini'           => $start2,
            'fechafin'           => $end2,
            'estado'             => $agendamiento->estado,
            'cortesia'           => $agendamiento->cortesia,
            'observaciones'      => "DESPLAZAMIENTO RÁPIDO DOCTOR",
            'id_doctor1'         => $agendamiento->id_doctor1,
            'id_doctor2'         => $agendamiento->id_doctor2,
            'id_doctor3'         => $agendamiento->id_doctor3,
            'id_sala'            => $agendamiento->id_sala,

            'descripcion'        => "DESPLAZAMIENTO RÁPIDO DOCTOR",
            'descripcion2'       => "",
            'descripcion3'       => "",
            'campos_ant'         => "",
            'campos'             => "",
            'id_usuarioconfirma' => $agendamiento->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        $cuenta = count($agenda_nueva);
        if ($cuenta != 0) {
            do {
                $tfin     = strtotime($agenda_nueva[0]->fechafin);
                $tinicio  = strtotime($agenda_nueva[0]->fechaini);
                $tiempo   = $tfin - $tinicio;
                $tiempo   = $tiempo;
                $start2   = $end2;
                $end2     = strtotime('+' . $tiempo . ' seconds', strtotime($end2));
                $end2     = date('Y-m-d H:i', $end2);
                $terminar = strtotime('-1 minute', strtotime($end2));
                $terminar = date('Y-m-d H:i', $terminar);

                $id_nuevo = $agenda_nueva[0]->id;

                $id_doctor = $agenda_nueva[0]->id_doctor1;

                $input = [
                    'nro_reagenda'    => $nro_reagenda,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'fechaini'        => $start2,
                    'fechafin'        => $end2,
                ];
                Agenda::where('id', $id_nuevo)
                    ->update($input);

                //falta log de agenda
                Log_agenda::create([
                    'id_agenda'          => $agenda_nueva[0]->id,
                    'estado_cita_ant'    => $agenda_nueva[0]->estado_cita,
                    'fechaini_ant'       => $agenda_nueva[0]->fechaini,
                    'fechafin_ant'       => $agenda_nueva[0]->fechafin,
                    'estado_ant'         => $agenda_nueva[0]->estado,
                    'cortesia_ant'       => $agenda_nueva[0]->cortesia,
                    'observaciones_ant'  => $agenda_nueva[0]->observaciones,
                    'id_doctor1_ant'     => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2_ant'     => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3_ant'     => $agenda_nueva[0]->id_doctor3,
                    'id_sala_ant'        => $agenda_nueva[0]->id_sala,

                    'estado_cita'        => $agenda_nueva[0]->estado_cita,
                    'fechaini'           => $start2,
                    'fechafin'           => $end2,
                    'estado'             => $agenda_nueva[0]->estado,
                    'cortesia'           => $agenda_nueva[0]->cortesia,
                    'observaciones'      => "DESPLAZAMIENTO RÁPIDO DOCTOR",
                    'id_doctor1'         => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2'         => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3'         => $agenda_nueva[0]->id_doctor3,
                    'id_sala'            => $agenda_nueva[0]->id_sala,

                    'descripcion'        => "DESPLAZAMIENTO RÁPIDO DOCTOR",
                    'descripcion2'       => "",
                    'descripcion3'       => "",
                    'campos_ant'         => "",
                    'campos'             => "",
                    'id_usuarioconfirma' => $agenda_nueva[0]->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);
                $agenda_nueva = DB::select("SELECT *
                FROM agenda
                WHERE id_doctor1 = '" . $id_doctor1 . "'  AND id !=  '" . $id_nuevo . "'  AND (fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "')  AND  estado > 0 AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta = count($agenda_nueva);
            } while ($cuenta != 0);
        }
        return "Proceso completado correctamente";
    }

    public function Movimientos_Permitidos($id, $start, $end)
    {

        $agendamiento = Agenda::find($id);
        $id_sala      = $agendamiento->id_sala;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2       = date('Y-m-d H:i', $start);
        $end2         = date('Y-m-d H:i', $end);
        $terminar     = strtotime('-1 minute', strtotime($end2));
        $terminar     = date('Y-m-d H:i', $terminar);
        $start3       = strtotime('+1 minute', strtotime($start2));
        $start3       = date('Y-m-d H:i', $start3);
        $nro_reagenda = $agendamiento->nro_reagenda;
        $id_doctor    = $agendamiento->id_doctor1;

        //-------------------------------
        if ($id_doctor != null) {
            // VH: 10102018 VALIDA SI TIENE UNA PROCEDIMIENTO
            $cant_proc = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($start3, $terminar, $id_doctor) {
                return $query->where('id_doctor1', '=', $id_doctor)
                    ->orWhere('id_doctor2', '=', $id_doctor)
                    ->orWhere('id_doctor3', '=', $id_doctor);
            })
                ->where(function ($query) use ($start3, $terminar, $id_doctor) {
                    return $query->whereRaw("(('" . $start3 . "' BETWEEN fechaini and fechafin)")
                        ->orWhere(
                            function ($query) use ($start3, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");
                            }
                        )
                        ->orWhere(function ($query) use ($start3, $terminar) {
                            $query->whereRaw("(fechaini BETWEEN '" . $start3 . "' and '" . $terminar . "'");
                        })
                        ->orWhere(function ($query) use ($start3, $terminar) {
                            $query->whereRaw("fechafin BETWEEN '" . $start3 . "' and '" . $terminar . "')");
                        });
                })
                ->where(function ($query) {
                    return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                })
                ->count();

            if ($cant_proc > 0) {
                return "Doctor posee " . $cant_proc . " procedimiento(s)";
            }

            // VH: 10102018 VALIDA SI TIENE UNA REUNION
            $cant_reuniones = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($start3, $terminar, $id_doctor) {
                return $query->where('id_doctor1', '=', $id_doctor)
                    ->orWhere('id_doctor2', '=', $id_doctor)
                    ->orWhere('id_doctor3', '=', $id_doctor);
            })
                ->where(function ($query) use ($start3, $terminar, $id_doctor) {
                    return $query->whereRaw("(('" . $start3 . "' BETWEEN fechaini and fechafin)")
                        ->orWhere(
                            function ($query) use ($start3, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");
                            }
                        )
                        ->orWhere(function ($query) use ($start3, $terminar) {
                            $query->whereRaw("(fechaini BETWEEN '" . $start3 . "' and '" . $terminar . "'");
                        })
                        ->orWhere(function ($query) use ($start3, $terminar) {
                            $query->whereRaw("fechafin BETWEEN '" . $start3 . "' and '" . $terminar . "')");
                        });
                })
                ->where(function ($query) {
                    return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                })
                ->count();

            if ($cant_reuniones > 0) {
                return "Doctor posee " . $cant_reuniones . " reunion(es)";
            }

            // HORARIO LABORABLE
            $horariocontroller = new HorarioController();
            $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $start2, $terminar);

            if ($horario == "INI") {
                return "Fecha de inicio fuera del Horario Laborable del Doctor";
            }

            if ($horario == "FIN") {
                return "Fecha de fin fuera del Horario Laborable del Doctor";
            }
        }
        //return "ok";
        //-------------------------------
        $agenda_nueva = DB::select("SELECT *
            FROM agenda
            WHERE id_doctor1 = '" . $id_doctor . "' AND  id != " . $id . " AND (fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id . "  AND  estado > 0 AND id !=  0 ORDER BY fechaini ASC;");

        date_default_timezone_set('America/Guayaquil');

        $cuenta = count($agenda_nueva);
        //return $cuenta;
        if ($cuenta != 0) {
            do {

                $tfin     = strtotime($agenda_nueva[0]->fechafin);
                $tinicio  = strtotime($agenda_nueva[0]->fechaini);
                $tiempo   = $tfin - $tinicio;
                $tiempo   = $tiempo;
                $start2   = $end2;
                $end2     = strtotime('+' . $tiempo . ' seconds', strtotime($end2));
                $end2     = date('Y-m-d H:i', $end2);
                $terminar = strtotime('-1 minute', strtotime($end2));
                $terminar = date('Y-m-d H:i', $terminar);

                $id_nuevo = $agenda_nueva[0]->id;

                $id_doctor = $agenda_nueva[0]->id_doctor1;

                $id_sala = $agenda_nueva[0]->id_sala;

                if ($id_doctor != null) {
                    // VH: 10102018 VALIDA SI TIENE UNA PROCEDIMIENTO
                    $cant_proc = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                        return $query->where('id_doctor1', '=', $id_doctor)
                            ->orWhere('id_doctor2', '=', $id_doctor)
                            ->orWhere('id_doctor3', '=', $id_doctor);
                    })
                        ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                            return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                                ->orWhere(
                                    function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");
                                    }
                                )
                                ->orWhere(function ($query) use ($start2, $terminar) {
                                    $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                                })
                                ->orWhere(function ($query) use ($start2, $terminar) {
                                    $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                                });
                        })
                        ->where(function ($query) {
                            return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                        })
                        ->count();

                    if ($cant_proc > 0) {
                        return "Una de las Agendas a desplazar tiene " . $cant_proc . " procedimiento(s)";
                    }

                    // VH: 10102018 VALIDA SI TIENE UNA REUNION
                    $cant_reuniones = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                        return $query->where('id_doctor1', '=', $id_doctor)
                            ->orWhere('id_doctor2', '=', $id_doctor)
                            ->orWhere('id_doctor3', '=', $id_doctor);
                    })
                        ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                            return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                                ->orWhere(
                                    function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");
                                    }
                                )
                                ->orWhere(function ($query) use ($start2, $terminar) {
                                    $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                                })
                                ->orWhere(function ($query) use ($start2, $terminar) {
                                    $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                                });
                        })
                        ->where(function ($query) {
                            return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                        })
                        ->count();

                    if ($cant_reuniones > 0) {
                        return "Una de las Agendas a desplazar tiene " . $cant_reuniones . " reunion(es)";
                    }

                    // HORARIO LABORABLE
                    $horariocontroller = new HorarioController();
                    $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $start2, $terminar);

                    if ($horario == "INI") {
                        return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                    }

                    if ($horario == "FIN") {
                        return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                    }
                }

                $agenda_nueva = DB::select("SELECT *
            FROM agenda
            WHERE id_doctor1 = '" . $id_doctor . "' AND  id != " . $id . " AND (fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id . "  AND  estado > 0 AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta = count($agenda_nueva);
            } while ($cuenta != 0);
        }
        return "OK";
    }

    public function nuevo_reunion($id, $fecha, $i)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $user = User::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = $user;
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')->orderBy('hospital.nombre_hospital')
            ->get();

        $especialidad = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();

        date_default_timezone_set('UTC');

        $fecha  = substr($fecha, 0, 10);
        $fecha2 = date('Y/m/d H:i', $fecha);
        $n_dia  = date('N', $fecha);
        $hora   = date('H:i', $fecha);
        $hora   = date('H:i', strtotime('+1 minute', strtotime($hora)));

        return view('agenda/nuevo_reunion', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'i' => $i, 'especialidad' => $especialidad, 'hora' => $fecha2, 'unix' => $fecha]);
    }

    public function nuevo_reunion_guardar(Request $request)
    {

        //return $request->all();
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $this->validateInput3($request);

        $this->validateInput4($request);

        Agenda::create([
            'fechaini'        => $request['inicio'],
            'fechafin'        => $request['fin'],
            'procedencia'     => $request['clase'],
            'id_doctor1'      => $request['id_doctor1'],
            'proc_consul'     => '2',
            'id_sala'         => $request['id_sala'],
            'estado_cita'     => 1,
            'observaciones'   => $request['observaciones'],
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->route('agenda.fecha', ['id' => $request['id_doctor1'], 'fecha' => $request['unix']]);
    }
    public function guardarCie10_isspol(Request $request)
    {

        $agenda = Agenda::find($request->idAgenda);
        $arrayAg = [
            'cie10' => $request->cie10,
        ];
        $agenda->update($arrayAg);
    }

    public function triaje_telefonico(Request $request)
    {


        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $id = Triaje_Telefonico::where('id_agenda',$request->id)->whereBetween('created_at', [date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59']);
            if(!is_null($id)){
                Triaje_Telefonico::create([
                    'id_agenda' => $request->id_agenda,
                    'mensaje'  => 'ok',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);

            }
            return ['status' => true, 'msj' => 'ok']; 
        } catch (\Throwable $th) {

            return ['status' => false, 'msj' => $th->getMessage()];
        }
    }
    public function guardar_otro_seguro(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_agenda = $request->id_agenda;
        $seguro_ids = $request->id_seguro_multiple ?? [];

        SegurosPaciente::where('id_agenda', $id_agenda)
            ->whereNotIn('id_seguro', $seguro_ids)
            ->delete();

        foreach ($seguro_ids as $id_seguro) {
            $existe = SegurosPaciente::where('id_seguro', $id_seguro)
                ->where('id_agenda', $id_agenda)
                ->exists();

            if (!$existe) {
                $arreglo = [
                    'id_seguro'            => $id_seguro,
                    'id_agenda'            => $id_agenda,
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_modificacion'      => $ip_cliente,
                    'ip_creacion'          => $ip_cliente,
                ];
                SegurosPaciente::insertGetId($arreglo);
            }
        }
    }
                // Inserta el nuevo registro si no existe
}
