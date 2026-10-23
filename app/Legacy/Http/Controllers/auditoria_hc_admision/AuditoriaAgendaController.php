<?php
namespace Sis_medico\Http\Controllers\auditoria_hc_admision;
use Carbon\Carbon;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use DateTime;
use Sis_medico\Agenda;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Hc_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Historiaclinica;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\User;
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Child_Pugh;
use Sis_medico\Pentax;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\Aud_Hc_Epicrisis;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Cardiologia;
use Sis_medico\Aud_Hc_Cpre_Eco;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Aud_Hc_Procedimiento_Final;
use Sis_medico\Examen_Orden;
use Sis_medico\Orden_Doctor;
use Sis_medico\Tipo_Procedimiento;
use Sis_medico\Tipo_Detalle_Orden;
use Sis_medico\Orden_Doctor_Detalle;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Subseguro;

date_default_timezone_set('America/Guayaquil');

class AuditoriaAgendaController extends Controller
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
    
    public function detalle_auditoria($id, Request $request)
    {
       
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol_dr()) {
            return response()->view('errors.404');
        }
        //dd($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $agenda = Agenda::find($id);
        //dd($agenda);
        $historia = $agenda->historia_clinica;
        //dd($historia);
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
        //dd($agenda);

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
        
        $evoluciones = DB::table('hc_evolucion as e')
            ->join('historiaclinica as h', 'e.hcid', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->orderBy('a.fechaini', 'desc')
            ->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid', 'a.id as agendaid')
            ->orderBy('e.id', 'desc')
            ->where('a.estado_cita', '<>', '3')
            ->get();

        $evolucion_car = DB::table('hc_evolucion as e')
            ->join('historiaclinica as h', 'e.hcid', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->orderBy('a.fechaini', 'desc')
            ->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')
            ->orderBy('e.id', 'desc')
            ->where('a.estado_cita', '<>', '3')
            ->first();
      

        $evoluciones_aud = Aud_Hc_Evolucion::join('historiaclinica as h', 'aud_hc_evolucion.hcid', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->orderBy('a.fechaini', 'desc')
            ->select('aud_hc_evolucion.*', 'a.fechaini', 'a.proc_consul', 'a.espid')
            ->orderBy('aud_hc_evolucion.id', 'desc')
            ->where('a.estado_cita', '<>', '3')
            ->get();
        //dd($evoluciones_aud);
      
        
        $protocolos = DB::table('hc_protocolo as p')->join('historiaclinica as h', 'h.hcid', 'p.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('hc_procedimientos as hc', 'hc.id', 'p.id_hc_procedimientos')->join('agenda as a', 'a.id', 'h.id_agenda')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')->select('p.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'p.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'a.id as agendaid')->orderBy('a.fechaini', 'desc')->orderBy('p.created_at', 'desc')->where('a.espid', '<>', '10')->get();
        //dd($protocolos);

         $aud_protocolos = Aud_Hc_Protocolo::join('historiaclinica as h', 'h.hcid', 'aud_hc_protocolo.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('aud_hc_procedimientos as hc', 'hc.id_procedimientos_org', 'aud_hc_protocolo.id_hc_procedimientos')->join('agenda as a', 'a.id', 'h.id_agenda')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')->select('aud_hc_protocolo.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'aud_hc_protocolo.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')->orderBy('a.fechaini', 'desc')->orderBy('aud_hc_protocolo.created_at', 'desc')->where('a.espid', '<>', '10')->get();
        
        
        $protocolos_dia   = [];
        $evoluciones_proc = [];
        $child_pugh       = null;
        $hc_proc          = null;

        if (!is_null($agenda->hcid)) {

            if ($agenda->proc_consul == '0' || ($agenda->proc_consul == '4' && $agenda->observacion == 'EVOLUCION CREADA POR EL DOCTOR') ){

                //CONSULTAS O EVOLUCIONES CREADAS POR EL DOCTOR
                $xseguro   = Seguro::find($agenda->h_idseguro);
                $evolucion = DB::table('aud_hc_evolucion as e')->where('e.hcid', $agenda->hcid)->join('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();

                $evolucion_p = DB::table('hc_evolucion as e')->where('e.hcid', $agenda->hcid)->join('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();
                //dd($evolucion_p,$evolucion);
                    
                

                if (is_null($evolucion)) {

                    if ($agenda->estado_cita == '4') {

                        $hc_proc_p = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                        $hc_proc = Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
                        

                        if (is_null($hc_proc)) {
                            $input_hc_procedimiento_p = [
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
                            $id_hc_procedimiento_p = hc_procedimientos::insertGetId($input_hc_procedimiento_p);

                            
                            $procedimiento = hc_procedimientos::find($id_hc_procedimiento_p);
                            $procedimiento_id = $procedimiento->id;

                          
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
                                'id_procedimientos_org'     => $procedimiento_id,
                            ];
                            $id_hc_procedimiento = Aud_Hc_Procedimientos::insertGetId($input_hc_procedimiento);

                        } else {
                            $input_hc_procedimiento    = $hc_proc->id;
                            $procedimiento_generico = Aud_Hc_Procedimientos::find($id_hc_procedimiento);
                            if (is_null($procedimiento_generico->id_doctor_examinador)) {
                                $inputx2 = [
                                    'id_doctor_examinador' => $agenda->id_doctor1,
                                    'ip_modificacion'      => $ip_cliente,
                                    'id_usuariomod'        => $idusuario,
                                ];
                                //return $request->all();
                                $procedimiento_generico->update($inputx2);
                            }
                            $procedimiento_generico = Aud_Hc_Procedimientos::find($id_hc_procedimiento);

                        }
                        $hc_proc = Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
                        //dd($hc_proc);
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

                        $input_hc_evolucion_p = [
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
                        $id_evolucion_p = Hc_evolucion::insertGetId($input_hc_evolucion_p);

                        $evolucion_p = DB::table('hc_evolucion as e')->where('e.id', $id_evolucion_p)->leftjoin('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();

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
                            'id_evolucion_org'    => $evolucion_p->id,
                        ];
                        $id_evolucion = Aud_Hc_Evolucion::insertGetId($input_hc_evolucion);

                        $evolucion = DB::table('aud_hc_evolucion as e')
                        ->where('e.id', $id_evolucion)
                        ->leftjoin('hc_receta as r', 'r.id_hc', 'e.hcid')
                        ->select('e.*', 'r.rp')
                        ->first();
                        
                        //dd($evolucion);
                    }
                }

                $child_pugh    = Aud_Hc_Child_Pugh::where('id_hc_evolucion', $evolucion_p->id)->first();
                //dd($child_pugh);
                
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
                        $input_child_pugh_org = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'id_usuariocrea'  => $idusuario,
                            'examen_fisico'   => $examen_fisico,
                            'ip_creacion'     => $ip_cliente,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        hc_child_pugh::insert($input_child_pugh_org);

                        $child_pugh_org = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

                        $input_child_pugh = [
                            'id_hc_evolucion'   => $evolucion_p->id,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariomod'     => $idusuario,
                            'id_usuariocrea'    => $idusuario,
                            'examen_fisico'     => $examen_fisico,
                            'ip_creacion'       => $ip_cliente,
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => date('Y-m-d H:i:s'),
                            'id_child_pugh_org' => $child_pugh_org->id,
                        ];
                        Aud_Hc_Child_Pugh::insert($input_child_pugh);

                        $child_pugh    = Aud_Hc_Child_Pugh::where('id_hc_evolucion', $evolucion_p->id)->first();
                        //dd($child_pugh);
                     
                    }
                }

                 $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                 $hc_proc_aud = Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();

                if (is_null($hc_proc_aud)) {
                   $input_hc_procedimiento_org = [
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
                    $id_hc_procedimiento_org = hc_procedimientos::insertGetId($input_hc_procedimiento_org);

                    $hc_proc_org = hc_procedimientos::where('id_hc', $agenda->hcid)->first();

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
                        'id_procedimientos_org'     => $hc_proc_org->id,
                    ];
                    $id_hc_procedimiento = Aud_Hc_Procedimientos::insertGetId($input_hc_procedimiento);
                    $hc_proc_aud = Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
                } else {
                    $id_hc_procedimiento    = $hc_proc_aud->id;
                    
                    $procedimiento_generico = Aud_Hc_Procedimientos::find($id_hc_procedimiento);
                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                       
                        $procedimiento_generico->update($inputx2);
                    }

                    $procedimiento_generico = Aud_Hc_Procedimientos::find($id_hc_procedimiento);
                }
                $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                $hc_proc_aud = Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
            }
           
            if ($agenda->proc_consul == '1' || ($agenda->proc_consul == '4' && $agenda->observacion == 'PROCEDIMIENTO CREADO POR EL DOCTOR') ){
                //PROCEDIMIENTOS
                 
                $protocolo = DB::table('aud_hc_protocolo as p')
                ->where('p.hcid', $agenda->hcid)
                ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
                ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
                ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')
                ->orderBy('p.created_at', 'desc')
                ->first();

             
                if(is_null($protocolo))
                {
                   
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

                    $input_hc_protocolo_aud = [
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
                        'id_protocolo_org'     => $protocolo->id,
                    ];
                    Aud_Hc_Protocolo::insert($input_hc_protocolo_aud);

                    $protocolo_aud = DB::table('aud_hc_protocolo as p')
                            ->where('p.hcid', $agenda->hcid)
                            ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
                            ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
                            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                            ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')
                            ->orderBy('p.created_at', 'desc')
                            ->first();
                }

                if (!is_null($protocolo)) {

                    $evoluciones_proc = DB::table('aud_hc_evolucion as e')
                    ->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)
                    ->get();

                    $evoluciones_proc = DB::table('aud_hc_evolucion as e')
                    ->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)
                    ->get();

                }

                $protocolos_dia = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'nombre_general')->get();
                //dd($protocolo);
                if (!is_null($protocolo)) {

                    if ($idusuario == '9999966654') {
                   //dd($protocolos->first()->id); 
                }
                    
                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();

                    $imagenes               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolos->first()->id)->where('estado', '1')->get();
                    $documentos             = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolos->first()->id)->where('estado', '2')->get();
                    $estudios               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolos->first()->id)->where('estado', '3')->get();
                    $biopsias               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolos->first()->id)->where('estado', '4')->get();
                    $hc_proc_org            = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    $hc_proc                = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();
                    
                    //dd($hc_proc);
                    $procedimiento_generico_org = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    $procedimiento_generico     = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();

                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                      
                        $procedimiento_generico->update($inputx2);
                        $procedimiento_generico     = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();
                    }
                    $hc_proc = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    $hc_proc_aud = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
                    
                } else {

                    $hc_proc_org = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                    $hc_proc = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
                    if (is_null($hc_proc_aud)) {

                        $input_hc_procedimiento_org = [
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
                        $id_hc_procedimiento_org = hc_procedimientos::insertGetId($input_hc_procedimiento_org);


                        $hc_proc_org = hc_procedimientos::where('id_hc', $agenda->hcid)->first();

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
                            'id_procedimientos_org'     => $hc_proc_org->id,
                        ];

                        $id_hc_procedimiento = Aud_Hc_Procedimientos::insertGetId($input_hc_procedimiento);
                        $hc_proc_aud = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();

                    } else {
                        $id_hc_procedimiento    = $hc_proc_aud->id;
                        $procedimiento_generico = Aud_Hc_Procedimientos::find($id_hc_procedimiento);
                        if (is_null($procedimiento_generico->id_doctor_examinador)) {
                            $inputx2 = [
                                'id_doctor_examinador' => $agenda->id_doctor1,
                                'ip_modificacion'      => $ip_cliente,
                                'id_usuariomod'        => $idusuario,
                            ];
                            $procedimiento_generico->update($inputx2);
                        }
                        $procedimiento_generico     = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();
                    }
                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();
                    $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->get();
                    $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '2')->get();
                    $estudios   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get();
                    $biopsias   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '4')->get();
                    $hc_proc     = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_proc_org->id)->first();

                       
                }
            }
            $hc_rec = hc_receta::where('id_hc', $agenda->hcid)->first();
            //dd($hc_rec);
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
            $mail = null;
            $u1   = User::find($agenda->id_paciente);
            if (!is_null($u1)) {
                $mail = $u1->email;
            }
        } else {
            $mail = User::find($agenda->id_usuario)->email;
        }

        $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();
        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();
        $hc_proc_audi = Aud_Hc_Procedimientos::where('id_hc', $hc_proc->id_hc)->first();
        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
        $laboratorio_externo = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '1')->get();
        $biopsias_1          = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $agenda->id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();
        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->where('h.id_agenda', $agenda->id)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre')
            ->get();
        $orden_biopsias = Hc4_Biopsias::where('id_paciente', $agenda->id_paciente)->groupBy("hc_id_procedimiento")->get();
        $paciente_observacion = Paciente_Observaciones::where('id_paciente', $agenda->id_paciente)->first();    
            foreach ($protocolos as $value) {
              
                $hc_proc_aud = Aud_Hc_Procedimientos::where('id_procedimientos_org', $value->id_hc_procedimientos)->first();
              
            }
        
        return view('auditoria_hc_admision/historia/historiaclinica', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'evolucion' => $evolucion, 'evoluciones' => $evoluciones, 'seguros' => $seguros, 'subseguros' => $subseguros, 'mail' => $mail, 'protocolos' => $protocolos, 'protocolo' => $protocolo, 'procedimientos_pentax' => $procedimientos_pentax, 'proc_completo' => $proc_completo, 'doctores' => $doctores, 'anestesiologos' => $anestesiologos, 'enfermeros' => $enfermeros, 'imagenes' => $imagenes, 'documentos' => $documentos, 'estudios' => $estudios, 'biopsias' => $biopsias, 'hc_receta' => $hc_rec, 'alergiasxpac' => $alergiasxpac, 'protocolos_dia' => $protocolos_dia, 'evoluciones_proc' => $evoluciones_proc, 'child_pugh' => $child_pugh, 'hc_proc' => $hc_proc, 'laboratorio_externo' => $laboratorio_externo, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'hist_recetas' => $hist_recetas, 'orden_biopsias' => $orden_biopsias, 'paciente_observacion' => $paciente_observacion, 'evoluciones_aud' => $evoluciones_aud, 'aud_protocolos' =>  $aud_protocolos, 'hc_proc_audi' => $hc_proc_audi, 'evolucion_car' => $evolucion_car]);

    }

     public function actualizacortesia($id,$c)
    {
        //dd($id,$c);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $agenda = Agenda::findOrFail($id);
        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/agenda');
        }
        
        
        if($c == 0){$cortesia="NO";}
        elseif($c == 1){$cortesia="SI";}
        $input=[
                'cortesia' => $cortesia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario

            ];
          
        $agenda->update($input); 

        $cortesia_paciente=Cortesia_paciente::find($agenda->id_paciente);
        
        if(is_null($cortesia_paciente)){
            $input_cortesia=[
                    'id' => $agenda->id_paciente,
                    'cortesia' => $cortesia,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario
                ];
            Cortesia_paciente::create($input_cortesia);    
        }
        else{
            $input_cortesia=[
                    'id' => $agenda->id_paciente,
                    'cortesia' => $cortesia,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
            $cortesia_paciente->update($input_cortesia);    
        }

        
          
        return  redirect()->route("auditoria_agenda.detalle", ['id' => $agenda->id]);
        
    }

    public function auditoria_foto567(Request $request)
    {
        $id    = $request['id'];
        $fotos = DB::table('agenda_archivo')->where('id', '=', $id)->get();
        return view('auditoria_hc_admision/agenda/foto', ['hcagenda' => $fotos]);
    }

    public function auditoria_mostrar_lab_externo($id)
    {
        $imagen = paciente_biopsia::find($id);
        return view('auditoria_hc_admision/video/modal_externo', ['imagen' => $imagen]);
    }

    public function auditoria_mostrar_biopsias($id)
    {
         $imagen = hc_imagenes_protocolo::find($id);
        //dd($imagen);
        return view('auditoria_hc_admision/video/modal_biopsias', ['imagen' => $imagen]);
    }
    public function mostrar_foto($id)
    {
        $imagen = hc_imagenes_protocolo::find($id);
        //dd($imagen);
        return view('auditoria_hc_admision/video/modal', ['imagen' => $imagen]);
    }
    public function auditoria_mostrar($id_protocolo, $agenda_ori, $ruta)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        //dd($protocolo);
        $paciente  = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda    = Agenda::find($protocolo->historiaclinica->id_agenda);

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->get();
        //$imagenes2 = hc_imagenes_protocolo::all();
        $imagenes2 = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  hc_protocolo hc_proto,  historiaclinica hc, paciente p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $protocolo->historiaclinica->id_paciente . "'
                ORDER BY id desc;");
        $cvideo = hc_imagenes_protocolo::where('nombre', 'LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        $cimagenes = hc_imagenes_protocolo::where('nombre', 'NOT LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        return view('auditoria_hc_admision/video/video', ['protocolo' => $protocolo, 'paciente' => $paciente, 'imagenes' => $imagenes, 'agenda' => $agenda, 'id' => $id_protocolo, 'imagenes2' => $imagenes2, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta, 'cvideo' => $cvideo, 'cimagenes' => $cimagenes]);
    }

    public function ingreso_actualiza_visita($id,$id_agenda)
    {
        //dd($id);
        $examen_fisico = null;
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        $idusuario = Auth::user()->id;
        //dd($idusuario);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        //$hc_evolucion = hc_receta::where('id_hc', '12686')->first(); 
        //dd($hc_evolucion);

        /*$protocolo = hc_protocolo::find($id_protocolo);
        $paciente = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $receta = hc_receta::where('id_hc', $protocolo->historiaclinica->hcid)->first();

        return view('hc_admision/visita/visita_crea_actualiza', ['paciente' => $paciente, 'protocolo' => $protocolo, 'agenda' => $agenda, 'receta' => $receta]);*/

        $evolucion = DB::table('hc_evolucion as e')
        ->where('e.id',$id)
        ->leftjoin('hc_receta as r','r.id_hc','e.hcid')
        ->select('e.*','r.rp')
        ->first(); 
        //dd($evolucion);

        $aud_evolucion = DB::table('aud_hc_evolucion as e')
        ->where('e.id_evolucion_org',$id)
        ->leftjoin('hc_receta as r','r.id_hc','e.hcid')
        ->select('e.*','r.rp')
        ->first(); 
        //dd($aud_evolucion);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->join('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid','paciente.referido','paciente.id_usuario','paciente.trabajo','paciente.observacion','paciente.alcohol','hs.nombre as hsnombre','h.presion','h.pulso','h.temperatura','h.o2','h.altura','h.peso','h.perimetro','h.examenes_realizar', 'h.id_seguro')
            ->where('h.hcid', '=', $evolucion->hcid)
            ->first();
           //dd($agenda);

        $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));
        
        $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

        $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();

        $hc_receta = hc_receta::where('id_hc',$agenda->hcid)->first();
        if(is_null($hc_receta)){
            $input_hc_receta = [
                'id_hc' => $agenda->hcid,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]; 
            hc_receta::insert($input_hc_receta);     
        }
        //dd($hc_receta);
        $historia = Historiaclinica::find($agenda->hcid);
        //dd($historia);
        //return $historia;
        $hc_receta = hc_receta::where('id_hc',$agenda->hcid)->first();
        $alergiasxpac = Paciente_Alergia::where('id_paciente',$agenda->id_paciente)->get();
        //dd($alergiasxpac);
        $child_pugh = null;
        $child_pugh = Aud_Hc_Child_Pugh::where('id_hc_evolucion', $aud_evolucion->id_evolucion_org)->first();
        //dd($child_pugh);
        //dd($evolucion);
        //Nuevo 

        if($agenda->espid=='4'){

            $examen_fisico = 'ESTADO CABEZA Y CUELLO:
ESTADO TORAX:
ESTADO ABDOMEN:
ESTADO MIEMBROS SUPERIORES:
ESTADO MIEMBROS INFERIORES:     
OTROS:
';
            if(!is_null($historia->seguro)){
                if($historia->seguro->tipo=='0'){
                    $examen_fisico ='REVISION ACTUAL ORGANOS Y SISTEMAS
PIEL: TEXTURA NORMAL, HIDRATADA, SIN LESIONES
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO MAREO
CARA: SIN ALTERACIONES
CUELLO: MOVIL, CENTRAL, SIN ADENOPATIAS
TORAX: SIMETRICO, SIN LESIONES
CSPS: VENTILADOS.
RSCS: RITMICOS

ABDOMEN:BANDO DEPRESIBLE SIN DOLOR A LA PALPACION SUPERFICIAL Y PROFUNDA.
RSHS(+), PUNTOS URETERALES(-),
COLUMNA VERTEBRAL: CENTRAL, SIN DESVIACION, NI LESIONES, PUÑOPERCUSION:(-) 
EXTREMIDADES: SIN ALTERACIONES, SIMETRICOS

EXAMEN FISICO REGIONAL
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO, CEFALEA
EXTREMIDADES: SIN LESIONES NI ALTERACIONES';
                }
            }
        }

                if(is_null($child_pugh)){
                    if($agenda->estado_cita=='4'){
                        $input_child_pugh_p = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod' => $idusuario,                    
                            'id_usuariocrea' => $idusuario,
                            'examen_fisico' => $examen_fisico,
                            'ip_creacion' => $ip_cliente,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]; 
                        hc_child_pugh::insert($input_child_pugh_p);
                        
                        $child_pugh_p = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                        //dd($child_pugh_p);

                        $input_child_pugh = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod' => $idusuario,                    
                            'id_usuariocrea' => $idusuario,
                            'examen_fisico' => $examen_fisico,
                            'ip_creacion' => $ip_cliente,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'id_child_pugh_org' => $child_pugh_p->id

                        ]; 
                        Aud_Hc_Child_Pugh::insert($input_child_pugh);
                        
                        $child_pugh = Aud_Hc_Child_Pugh::where('id_hc_evolucion', $aud_evolucion->id_evolucion_org)->first();

                        //dd($child_pugh_p,$child_pugh);
                    }
                }
        $hc_procedimiento = null;

        $hc_procedimiento =  hc_procedimientos::find($evolucion->hc_id_procedimiento);
        
        $hc_procedimiento_aud = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();

           if(is_null($hc_procedimiento_aud)){
                    if($agenda->estado_cita=='4'){
                        $input_hc_procedimiento_aud = [
                            'id_hc'                         => $hc_procedimiento->id_hc,
                            'id_seguro'                     => $hc_procedimiento->id_seguro,
                            'id_subseguro'                  => $hc_procedimiento->id_subseguro,
                            'id_empresa'                    => $hc_procedimiento->id_empresa,
                            'id_procedimiento_completo'     => $hc_procedimiento->id_procedimiento_completo,
                            'fecha'                         => $hc_procedimiento->fecha,
                            'hora_inicio'                   => $hc_procedimiento->hora_inicio,
                            'estado'                        => $hc_procedimiento->estado,
                            'hora_fin'                      => $hc_procedimiento->hora_fin,
                            'id_doctor_examinador'          => $hc_procedimiento->id_doctor_examinador,
                            'id_doctor_examinador2'         => $hc_procedimiento->id_doctor_examinador2,
                            'id_doctor_responsable'         => $hc_procedimiento->id_doctor_responsable,
                            'id_doctor_ayudante_con'        => $hc_procedimiento->id_doctor_ayudante_con,
                            'observaciones'                 => $hc_procedimiento->observaciones,
                            'estado_pago'                   => $hc_procedimiento->estado_pago,
                            'tipo_procedimiento'            => $hc_procedimiento->tipo_procedimiento,
                            'estimado_minimo'               => $hc_procedimiento->estimado_minimo,
                            'copago'                        => $hc_procedimiento->copago,
                            'pagado'                        => $hc_procedimiento->pagado,
                            'pago_copago'                   => $hc_procedimiento->pago_copago,
                            'cuadro_clinico_bp'             => $hc_procedimiento->cuadro_clinico_bp,
                            'diagnosticos_bp'               => $hc_procedimiento->diagnosticos_bp,
                            'id_procedimientos_org'         => $hc_procedimiento->id,
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                       
                        ]; 
                        Aud_Hc_Procedimientos::insert($input_hc_procedimiento_aud);
                        
                        //$hc_procedimiento_aud = Aud_Hc_Procedimientos::where('id_hc_evolucion', $evolucion->id)->first();
                        $hc_procedimiento_aud = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();
                        //dd($hc_procedimiento_aud);
                           
                    }
                }

        if($hc_procedimiento_aud->id_seguro == null){
            $input_procedimiento = [
                'id_seguro' => $agenda->id_seguro,
            ]; 
            Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)
            ->update($input_procedimiento);
            $hc_procedimiento_aud =  Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
        }
        if($hc_procedimiento_aud->id_doctor_examinador == null){
            $input_procedimiento = [
                'id_doctor_examinador' => $historia->id_doctor1,
            ];        
            Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->update($input_procedimiento);
            $hc_procedimiento_aud =  Aud_Hc_Procedimientos::where('id_hc', $agenda->hcid)->first();
        }

        $seguros = Seguro::where('inactivo', '1')->get();
        $doctores = User::join('user_espe','user_espe.usuid','=','users.id')->where('users.id_tipo_usuario', '3')
            ->where('users.estado', '1')->where('user_espe.espid',$agenda->espid)->select('users.*')->get();
        $examenes_externos = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->get();
        $biopsias_1 = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')->where('id_paciente', $agenda->id_paciente)->where('hc_imagenes_protocolo.estado', '4')->get();
                                
        return view('auditoria_hc_admision/visita/visita_crea_actualiza', ['evolucion' => $evolucion, 'agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'id_agenda' => $id_agenda, 'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'child_pugh' => $child_pugh, 'hc_procedimiento' => $hc_procedimiento, 'seguros' => $seguros, 'doctores' => $doctores, 'laboratorio_externo' => $examenes_externos, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2]);  
    }

     public function actualizar(Request $request)
    {   
      //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        //dd($ip_cliente);
        $idusuario = Auth::user()->id;
        //dd($idusuario);
        date_default_timezone_set('America/Guayaquil');

        $id= $request['hcid'];
         
        $input1 = [
            'presion' => $request["presion"],
            'id_seguro' => $request["id_seguro"],                                        
            'pulso' => $request["pulso"],
            'temperatura' => $request["temperatura"],                                       
            'o2' => $request["o2"],                                      
            'altura' => $request["estatura"],
            'peso' => $request["peso"],                                       
            'perimetro' => $request["perimetro"],
            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        Historiaclinica::where('hcid', $id)
        ->update($input1);

        $input_evo = [
            'motivo' => $request["motivo"], 
            'cuadro_clinico' => $request["historia_clinica"], 
            'resultado' => $request["resultado_ev"],                 
            'fecha_doctor' => $request["fecha_doctor"],
            'indicaciones' => $request["indicaciones"], //este campo no encontre el name
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $id_evolucion = $request['id_evolucion'];
        //return $input_evo;
        Aud_Hc_Evolucion::where('id_evolucion_org', $id_evolucion)
        ->update($input_evo);  

        $input_hc_procedimiento= [
            'id_doctor_examinador' => $request["id_doctor_examinador"], 
            'id_seguro' => $request["id_seguro"], 
            'id_empresa' => $request["id_empresa"],     
            'observaciones' => $request["observaciones"],  
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'id_procedimientos_org'        => $request["id_hc_procedimiento_org"], 
        ];

        $id_hc_procedimiento = $request['id'];
       //return $id_hc_procedimiento;

        Aud_Hc_Procedimientos::where('id', $id_hc_procedimiento)
        ->update($input_hc_procedimiento);  
            
        $hc_procedimiento_org = hc_procedimientos::find($request["id_hc_procedimiento_org"]);
        if(!is_null($hc_procedimiento_org)){
            $hc_procedimiento_org->update([
                'id_seguro' => $request["id_seguro"], 
                'id_empresa' => $request["id_empresa"]
            ]);
        }


        $input_child= [
            'ascitis' => $request["ascitis"], 
            'encefalopatia' => $request["encefalopatia"],                  
            'albumina' => $request["albumina"],
            'bilirrubina' => $request["bilirrubina"],                
            'inr' => $request["inr"],
            'examen_fisico' => $request["examen_fisico"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $id_child = $request['id_child_pugh'];

        //return $id_child;

        Aud_Hc_Child_Pugh::where('id_child_pugh_org', $id_child)
        ->update($input_child);  

        
        return "ok";   
    }
   
    public function cargar_empresa2($id_proc, $id_agenda, $id_seguro){
        $agenda = Agenda::find($id_agenda);
        $seguro = Seguro::find($id_seguro);
        if($seguro->tipo==0){
            $empresas = DB::table('convenio as c')->join('empresa as e','e.id','c.id_empresa')->select('e.*')->where('c.id_seguro',$seguro->id)->get();
        }else{
            $empresas = DB::table('empresa as e')->where('e.estado','1')->where('e.id','<>','9999999999')->get();
        }
        $procedimiento = hc_procedimientos::find($id_proc);
        //dd($procedimiento);
        $id_empresa = $procedimiento->id_empresa;
        
        if($procedimiento->id_empresa==null){
            $procedimiento->update(['id_empresa' => $agenda->id_empresa]);
            $id_empresa = $agenda->id_empresa;
        }
        

        return view('auditoria_hc_admision/admision/empresas2',['empresas'=>$empresas, 'id_empresa' => $id_empresa, 'procedimiento'=>$procedimiento]);
    }

    public function mostrar_epicrisis($hcid, $proc)
    {

        //dd($hcid, $proc);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $usuarios       = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;
        $enfermeros     = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado', '1')->get(); //9=ANESTESIOLOGO;
        $salas          = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $hc_procedimiento = Aud_Hc_Procedimientos::find($proc);
        //dd($hc_procedimiento);
        $historia = Historiaclinica::find($hcid);
        //dd($historia);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);

        $seguro = Seguro::find($historia->id_seguro);

        $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $proc)->get();

        $evolucion_p   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        
        $evolucion   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
        //dd($evolucion);
        $evolucion_1 = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        //dd($evolucion_1);
        $procedimientos_completo = procedimiento_completo::where('estado', '1')->get();

        //$epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        //dd($epicrisis);
        $epicrisis_org = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        //dd($aud_epicrisis);
        $protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $proc)->first();
        //dd($epicrisis_org);
        $epicrisis = Aud_Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
        //dd($epicrisis,$epicrisis_org);
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
            //dd($evolucion_1);
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
                    'id_evolucion_org'    => $evolucion_1->id,
                ];
            } else {
                $input = [
                   /* 'hc_id_procedimiento' => $proc,
                    'hcid'                => $hcid,
                    'secuencia'           => 1,
                    'cuadro_clinico'      => $epicrisis_org->favorable_des,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,*/
                ];
            }

            $id_evolucion  = Aud_Hc_Evolucion::insertGetId($input);
            $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
            //dd($evolucion_1,$evolucion_2);
            //$favorable_des = $evolucion_1->cuadro_clinico;
        }
        $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        $evolucion_2   = Aud_Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
        //dd($evolucion_1);
        //dd($evolucion_1,$evolucion_2);
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
                //$procedimientos       = Aud_Hc_Procedimientos::find($proc);
                $procedimientos       = hc_procedimientos::find($proc);
                $nombre_procedimiento = "";
                //return $procedimientos;
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
            //dd($favorable_des);
            //$protocolo = hc_protocolo::where('id_hc_procedimientos',$proc)->first();
            //dd($protocolo);
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

            //$id_epi = Aud_Hc_Epicrisis::insertGetId($input1);
            //$epicrisis = Aud_Hc_Epicrisis::find($id_epi);
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
            //dd($favorable_des);
            //$protocolo = hc_protocolo::where('id_hc_procedimientos',$proc)->first();
            //dd($protocolo);
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
                'hallazgo' => $protocolo->hallazgos,
                //'favorable_des'   => $favorable_des,
                //'ip_modificacion' => $ip_cliente,
                //'id_usuariomod'   => $idusuario,
            ];
            $epicrisis->update($input1a_aud);

        }
        //dd($protocolo,$epicrisis);

        return view('auditoria_hc_admision/epicrisis/epicrisis', ['agenda' => $agenda, 'paciente' => $paciente, 'hca' => $historia, 'seguro' => $seguro, 'epicrisis' => $epicrisis, 'hc_cie10' => $hc_cie10, 'procedimientos_completo' => $procedimientos_completo, 'hc_procedimiento' => $hc_procedimiento, 'id' => $hcid, 'protocolo' => $protocolo]);

    }
    public function actualiza_epicrisis(Request $request)
    {
        //return "hola";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //return $request->all();
        $epicrisis = Aud_Hc_Epicrisis::find($request->epicrisis);
        //dd($epicrisis);
        $protocolo = Aud_Hc_Protocolo::find($request->protocolo_id);
        //dd($protocolo);
        if (!is_null($epicrisis)) {

            $input1a = [

                'cuadro_clinico'       => $request['cuadro'],
                'favorable_des'        => $request['favorable_des'],
                'complicacion'         => $request['complicacion'],
                //'hallazgo' => $request['hallazgos'],
                'resumen'              => $request['resumen'],
                'condicion'            => $request['condicion'],
                'pronostico'           => $request['pronostico'],
                'alta'                 => $request['alta'],
                'discapacidad'         => $request['discapacidad'],
                'retiro'               => $request['retiro'],
                'defuncion'            => $request['defuncion'],
                'dias_estadia'         => $request['dias_estadia'],
                'dias_incapacidad'     => $request['dias_incapacidad'],
                'fecha_imprime'        => $request['fecha_imprime'],
                'ep_resumen_evolucion' => $request['ep_resumen_evolucion'],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
                'receta'               => $request['receta'],
                'indicaciones_alta'    => $request['indicaciones_alta'],
                'conclusion'           => $request['conclusion'],
            ];
            $input1b = [
                //'hallazgos' => $request['hallazgos'],
                'conclusion'      => $request['conclusion'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $epicrisis->update($input1a);

            //return $request->all();
            $protocolo->update($input1b);

            $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('secuencia', '0')->first();
            if (!is_null($evolucion)) {
                $input1c = [

                    'cuadro_clinico'  => $request['cuadro'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $evolucion->update($input1c);
            }

            $evolucion_1 = Aud_Hc_Evolucion::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('secuencia', '1')->first();
            if (!is_null($evolucion_1)) {
                $input1d = [

                    'cuadro_clinico'  => $request['favorable_des'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $evolucion_1->update($input1d);
            }

        }

        return "ok";

    }

    public function imprimir_epicrisis($id)
    {
        //dd($id);
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $epicrisis = Aud_Hc_Epicrisis::find($id);
        //dd($epicrisis);
        $protocolo = Aud_Hc_Protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        //dd($protocolo);
        //return $protocolo;
        //return $protocolo;
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
        //dd($cie10_in_pre,$cie10_in_def,$cie10_eg_pre,$cie10_eg_def);
        $cie10_3 = Cie_10_3::all();
        $cie10_4 = Cie_10_4::all();
        if (!is_null($protocolo)) {
            
            $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
            //dd($procedimiento);
            //$procedimiento = Aud_Hc_Procedimientos::find($protocolo->id_hc_procedimientos);
            
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
        } else {
            $procedimiento = "";
            $firma         = "";
        }
      
        $tiene_receta = 'NO';
        $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
        //dd($receta);

        if (!is_null($receta)) {
            $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
            if ($receta_det > 0 || $receta->prescripcion != null) {
                $tiene_receta = 'SI';
            }
        }

        $tiene_receta = $epicrisis->receta;

        //dd($receta);
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
        //dd($epicrisis);

        $data = $historiaclinica;
        $receta = hc_receta::where('id_hc',$data->hcid)->first();
        //dd($receta);
        $view = \View::make('auditoria_hc_admision.formato.epicrisis', compact('receta','data', 'evolucion_1', 'epicrisis', 'cie10_in_pre', 'cie10_in_def', 'cie10_3', 'cie10_4', 'cie10_eg_pre', 'cie10_eg_def', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $vt_name = '';
        if($tipo_usuario == 11 ){
            $vt_name = 'HCU_006.pdf';
            return $pdf->stream($vt_name); 
        }
        return $pdf->stream('epicrisis-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    }


    public function Crear_Editar($agenda, $cant_cortesias, $proc_completo, $protocolo, $doctores, $anestesiologos, $enfermeros, $seguros, $procedimientos_pentax, $imagenes, $id_agenda, $documentos, $estudios, $biopsias, $hc_receta,$historiaclinica)
    {
        //dd($historiaclinica);
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();

        $protocolo_id = DB::table('aud_hc_protocolo as p')
        ->where('p.id_protocolo_org',$protocolo->id)->first();
        //dd($protocolo_id);
        //dd($protocolo_id->id_hc_procedimientos);

        $evoluciones_proc    = DB::table('hc_evolucion as e')
        ->join('historiaclinica as h', 'e.hcid', 'h.hcid')
        ->join('agenda as a', 'a.id', 'h.id_agenda')
        ->orderBy('a.fechaini', 'desc')
        ->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')
        ->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)
        ->get();

   

        $aud_evoluciones_proc    = DB::table('aud_hc_evolucion as e')
        ->join('historiaclinica as h', 'e.hcid', 'h.hcid')
        ->join('agenda as a', 'a.id', 'h.id_agenda')
        ->orderBy('a.fechaini', 'desc')
        ->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')
        ->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)
        ->get();

         //dd($aud_evoluciones_proc);
        $id_hc_procedimiento = $protocolo->id_hc_procedimientos;

        //dd($id_hc_procedimiento);
        $hc_procedimiento    = null;
        //dd($protocolo);
        
        $historia         = Historiaclinica::find($agenda->hcid);

        $hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)->first();
        //dd($hc_procedimiento);
        if ($hc_procedimiento->id_seguro == null) {
            $input_procedimiento = [
                'id_seguro' => $agenda->id_seguro,
            ];
            Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)->update($input_procedimiento);
            $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
            //dd($hc_procedimiento);
        }
        if ($hc_procedimiento->id_doctor_examinador == null) {
            $input_procedimiento = [
                'id_doctor_examinador' => $historia->id_doctor1,
            ];
            Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)->update($input_procedimiento);
            $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
        }
        //dd($hc_procedimiento);
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if (is_null($hc_receta)) {
            $input_hc_receta = [
                'id_hc'           => $agenda->hcid,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            $id_receta = hc_receta::insertGetId($input_hc_receta);
            $hc_receta = hc_receta::find($id_receta);

        }

        $examenes_externos = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->get();
        $biopsias_1        = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        //$biopsias = hc_imagenes_protocolo::where('id_hc_protocolo',$protocolo->id)->where('estado', '4')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $agenda->id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();

        //Historial de Recetas Nueva Funcionalidad creada 18-6-2020
        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre')
            ->get();
        ////dd($protocolo);
        return view('auditoria_hc_admision/procedimientos/procedimiento', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'proc_completo' => $proc_completo, 'protocolo' => $protocolo, 'doctores' => $doctores, 'anestesiologos' => $anestesiologos, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'procedimientos_pentax' => $procedimientos_pentax, 'imagenes' => $imagenes, 'documentos' => $documentos, 'biopsias' => $biopsias, 'estudios' => $estudios, 'id_agenda' => $id_agenda, 'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'evoluciones_proc' => $evoluciones_proc, 'hc_procedimiento' => $hc_procedimiento, 'laboratorio_externo' => $examenes_externos, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'hist_recetas' => $hist_recetas, 'aud_evoluciones_proc' => $aud_evoluciones_proc,'historiaclinica' => $historiaclinica]);
    }
    
    public function crear_editar_orden_procedimiento($hcid)
    {
        //dd($hcid);
        //$evolucion = Hc_Evolucion::where('hcid', $hcid)->first();
      
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $historiaclinica = Historiaclinica::find($hcid);

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
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = Orden_Doctor::where('hcid', $hcid)->first();

        $seguros = Seguro::where('inactivo', '1')->get();

        $tipos = Tipo_Procedimiento::where('estado', '1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado', '1')->orderBy('orden', 'asc')->get();
        $evolucion = Aud_Hc_Evolucion::where('hcid', $hcid)->first();
                //dd($hcid,$evolucion,$historiaclinica);
        $detalle_orden = null;
        if (!is_null($orden)) {

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->get();
        } else {
            if ($agenda->proc_consul == 0) {
                $evolucion = Aud_Hc_Evolucion::where('hcid', $hcid)->first();
                //dd($hcid,$evolucion);
                $input     = [

                    'hcid'            => $hcid,
                    'id_paciente'     => $historiaclinica->id_paciente,
                    'motivo'          => $evolucion->motivo,
                    'id_seguro'       => $historiaclinica->id_seguro,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];
                Orden_Doctor::create($input);
            } else {
                $evolucion = Aud_Hc_Protocolo::where('hcid', $hcid)->first();
                 $input     = [

                    'hcid'            => $hcid,
                    'id_paciente'     => $historiaclinica->id_paciente,
                    'motivo'          => $evolucion->motivo,
                    'id_seguro'       => $historiaclinica->id_seguro,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Orden_Doctor::create($input);
            }
        }

        //dd(Date('d/m/Y',strtotime($orden->fecha_examen)));
        return view('auditoria_hc_admision/orden_proc/orden_proc', ['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'historiaclinica' => $historiaclinica]);
    }

    public function imprimir_orden_procedimiento($hcid)
    {

        $historiaclinica = Historiaclinica::find($hcid);
        //dd($historiaclinica);
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
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = DB::table('orden_doctor as od')->where('od.hcid', $hcid)->join('users as u', 'u.id', 'od.id_usuariocrea')->select('od.*', 'u.nombre1', 'u.apellido1')->first();

        $seguros = Seguro::where('inactivo', '1')->get();

        $tipos = Tipo_Procedimiento::where('estado', '1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado', '1')->orderBy('orden', 'asc')->get();

        $detalle_orden = null;
        if (!is_null($orden)) {

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->get();
        }
        $age = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;

        //return view('hc_admision.orden_proc.orden_pdf', ['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'age' => $age]);

        $view = \View::make('hc_admision.orden_proc.orden_pdf', compact('agenda', 'seguros', 'orden', 'tipos', 'detalles', 'detalle_orden', 'age'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        return $pdf->download('orden_doctor_' . $agenda->id_paciente . '.pdf');

        /*
$agenda = Agenda::find($id);
$historia = Historiaclinica::where('id_agenda', $id)->first();

//$historia = Historiaclinica::find($historia[0]->hcid);
if(!is_null($historia)){
$seguro = Seguro::find($historia->id_seguro);
$paciente = Paciente::find($historia->id_paciente);
$doctor = User::find($historia->id_doctor1);
$responsable = User::find($historia->id_usuariocrea);
if($agenda->proc_consul=='1'){
$pentax = Pentax::where('id_agenda',$agenda->id)->first();
$procedimientos = PentaxProc::where('id_pentax',$pentax->id)->get();
$procedimientos_txt = '';
foreach ($procedimientos as $value) {
if($procedimientos_txt==''){
$procedimientos_txt = procedimiento::find($value->id_procedimiento)->nombre;
}else{
$procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;
}
}
}else{
$procedimientos_txt = 'CONSULTA';
}
$ControlDocController = new ControlDocController;
$documentos = $ControlDocController->carga_documentos_union($historia->hcid, $agenda->proc_consul, $seguro->tipo);

$data = $historia;
$date = $historia->created_at;

}else{
$seguro = Seguro::find($agenda->id_seguro);
$paciente = Paciente::find($agenda->id_paciente);
$doctor = User::find($agenda->id_doctor1);
$responsable = User::find($agenda->id_usuariocrea);
if($agenda->proc_consul=='1'){
$procedimientos_txt = procedimiento::find($agenda->id_procedimiento)->nombre;
$procedimientos = AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
foreach ($procedimientos as $value) {

$procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;

}
}else{
$procedimientos_txt = 'CONSULTA';
}

$documentos = null;

$data = $agenda;
$date = $agenda->created_at;

}

$empresa = Empresa::where('id',$agenda->id_empresa)->first();
if(is_null($empresa)){
$empresa = Empresa::find('1391707460001');
}

//$empresaxdoc = Empresa::find($agenda->id_empresa);

//nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
//$procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento',$agenda->id_procedimiento)->first();

//dd($empresa);

$paper_size = array(0,0,595,920);

//return view('hc_admision/formato/'.$documento->formato);
$view =  \View::make('hc_admision.formato.resumen', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'seguro', 'responsable','procedimientos_txt','documentos'))->render();
$pdf = \App::make('dompdf.wrapper');

$pdf->loadHTML($view)/*->setPaper($paper_size, 'portrait');

//return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
if(!is_null($historia)){
return $pdf->download($historia->id_paciente.'_resumen_'.$historia->hcid.'.pdf');
}else{
return $pdf->download($agenda->id_paciente.'_resumenAG_'.$agenda->id.'.pdf');
} */
    }

    public function crear_detalle_orden_procedimiento($hcid, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //return $orden->id;
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //return $detalle
            if (is_null($detalle)) {
                $input = [
                    'id_tipo_detalle_orden' => $id,
                    'id_orden_doctor'       => $orden->id,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariomod'         => $idusuario,
                    'id_usuariocrea'        => $idusuario,
                ];

                Orden_Doctor_Detalle::create($input);
            }
        }
        return 'ok';
    }

    public function guardar_orden_procedimiento(Request $request, $hcid)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        //dd($request->all());

        $historiaclinica = DB::table('historiaclinica')->where('hcid', $hcid)->first();

        //return $historiaclinica->id_paciente;

        //return $request['hcid'];

        $orden_d = Orden_Doctor::where('hcid', $hcid)->first();

        if (!is_null($orden_d)) {

            $input = [

                'id_seguro'              => $request['id_seguro'],
                'motivo'                 => $request['motivo'],
                'fecha_examen'           => $request['fecha_examen'],
                'observacion'            => $request['observacion'],
                'endoscopia_urgencia'    => $request['endoscopia_urgencia'],
                'endoscopia_terapeutica' => $request['endoscopia_terapeutica'],
                'eco_doppler'            => $request['eco_doppler'],
                'ecografia'              => $request['ecografia'],
                'prueba_func'            => $request['prueba_func'],
                'campo1'                 => $request['campo1'],
                'campo2'                 => $request['campo2'],
                'campo3'                 => $request['campo3'],
                'campo4'                 => $request['campo4'],
                'campo5'                 => $request['campo5'],
                'campo6'                 => $request['campo6'],
                'campo7'                 => $request['campo7'],
                'campo8'                 => $request['campo8'],
                'id_usuariomod'          => $idusuario,
                'ip_modificacion'        => $ip_cliente,

            ];

            $orden_d->update($input);

            return "ok";
        }
    }
    public function existe_orden_procedimiento($hcid, $id)
    {
        //dd($hcid);
        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //dd($orden);
        //return $orden->id;
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //dd($detalle,$orden);
            //return $detalle
            if (is_null($detalle)) {
                return '0';
            }
        }
        return '1';
    }

    public function eliminar_orden_procedimiento($hcid, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //return $orden->id;
        //dd($orden);
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //dd($detalle);
            //return $detalle
            if (!is_null($detalle)) {
                $detalle->delete();
            }
        }
        return 'ok';
    }

    public function seleccion_descargar($id_protocolo)
    {   
        //dd($id_protocolo);
        return view('auditoria_hc_admision/video/modal_seleccion', ['id_protocolo' => $id_protocolo]);
    }
    //para descargar el resumen de procedimiento

    public function descarga_resumen($id_protocolo, $tipo)
    {
        //dd($id_protocolo,$tipo);
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
       // $examenes_externos = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->get();
        $protocolo = Aud_Hc_Protocolo::where('id_protocolo_org', $id_protocolo)->first();
        //$protocolo = Aud_Hc_Protocolo::find($id_protocolo);
        //dd($protocolo);
        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('seleccionado', '1')->where('estado', '1')->orderBy('id', 'desc')->get();
        //dd($imagenes);
        //dd($protocolo->historiaclinica->id_paciente);
        $paciente = paciente::find($protocolo->historiaclinica->id_paciente);
        //dd($paciente);
        //$seguro   = Seguro::find($protocolo->procedimiento->id_seguro);
        $seguro   = Seguro::find($protocolo->auditoria_procedimiento->id_seguro);
        
        if (!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)) {
            $firma = Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->doctor_firma->id)->get();
            if (!is_null($seguro)) {
                if ($seguro->tipo == '0') {
                    if ($protocolo->auditoria_procedimiento->id_empresa == '0992704152001') {
                        if ($protocolo->auditoria_procedimiento->id_doctor_examinador2 == '0924611882') {
                            $firma = Firma_Usuario::where('id_usuario', '094346835')->get();
                        }
                    }
                }
            }
        } else {
            $firma = null;
        }
        //dd($firma);
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->auditoria_procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);

        //$elasto = $protocolo->procedimiento->hc_procedimiento_final->where('id_procedimiento','26')->first();
        $elasto = Aud_Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->auditoria_procedimiento->id_procedimientos_org)->where('id_procedimiento', '26')->first();
        //dd($elasto);
        if (!is_null($elasto)) {
            $view = \View::make('auditoria_hc_admision.formato.resumen_elasto', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'elasto'))->render();
        } else {
            if ($tipo == 0) {
                $view = \View::make('auditoria_hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 1) {
                //return "asdsad12312";
                $view = \View::make('auditoria_hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 2) {
                $view = \View::make('auditoria_hc_admision.formato.resumen_2_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 3) {
                $view = \View::make('auditoria_hc_admision.formato.resumen_3_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 4) {
                $view = \View::make('auditoria_hc_admision.formato.resumen_4_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 5) {
                $view = \View::make('auditoria_hc_admision.formato.resumen_5_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 6) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('auditoria_hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 7) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('auditoria_hc_admision.formato.resumen_7_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 8) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('auditoria_hc_admision.formato.resumen_8_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            } elseif ($tipo == 9) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('auditoria_hc_admision.formato.resumen_9_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            }
             elseif ($tipo == 10) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('auditoria_hc_admision.formato.resumen_10_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
            }
        }

        //return view('auditoria_hc_admision.formato.resumen_procedimiento', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'edad'=> $edad, 'paciente' => $paciente, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');

        $pdf->loadHTML($view);
        $agenda = $historia->agenda;
        $txt_fecha = substr($agenda->fechaini,8,2).'_'.substr($agenda->fechaini,5,2).'_'.substr($agenda->fechaini,0,4);//dd($txt_fecha);
        
        return $pdf->stream('Estudio_' . $paciente->id . '_' . $paciente->apellido1 . '_' . $paciente->nombre1 .'_' . $txt_fecha . '.pdf');
    }
     public function estudio_editar($id, $id_agenda)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //dd($id); id del protocolo original
        $protocolo_id = DB::table('hc_protocolo as p')
        ->where('p.id', $id)
        ->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')
        ->join('pentax as px', 'px.hcid', 'p.hcid')
        ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
        ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'px.id_agenda')
        ->orderBy('p.created_at', 'desc')
        ->first();
       //dd($protocolo_id);

       // $aud_protocolo = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
       // dd($aud_protocolo);

        $protocolo = DB::table('aud_hc_protocolo as p')
        ->where('id_procedimientos_org', $protocolo_id->id_hc_procedimientos)
        ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
        ->join('pentax as px', 'px.hcid', 'p.hcid')
        ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
        ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'px.id_agenda')
        ->orderBy('p.created_at', 'desc')
        ->first();
        //dd($protocolo);

         $protocolo_id_users = DB::table('aud_hc_protocolo as p')
        ->where('id_procedimientos_org', $protocolo_id->id_hc_procedimientos)
        ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
        ->join('users as uc', 'uc.id', 'p.id_anestesiologo')
        ->select('p.*','uc.nombre1','uc.nombre2')
        ->orderBy('p.created_at', 'desc')
        ->first();
        //dd($protocolo_id_users);

       
        
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'p.estado_pentax', 'paciente.observacion as pobservacion', 'p.id as pxid')
            ->where('agenda.id', '=', $protocolo->id_agenda)
            ->first();
            //dd($agenda);

            $historiaclinica = Historiaclinica::where('hcid', $agenda->hcid)
            ->leftjoin('users as d1', 'historiaclinica.id_doctor1', 'd1.id')
            ->leftjoin('users as d2', 'historiaclinica.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'historiaclinica.id_doctor3', 'd3.id')
            ->select('historiaclinica.*', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')
            ->first();
            //dd($historiaclinica);

            /* $protocolos = Aud_Hc_Protocolo::join('historiaclinica as h', 'h.hcid', 'aud_hc_protocolo.hcid')
            //->where('h.id_paciente', $agenda->id_paciente)
            //->where('h.id_agenda', $agenda->id)
            //->join('aud_hc_procedimientos as hc', 'hc.id_procedimientos_org', 'aud_hc_protocolo.id_hc_procedimientos')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')
            ->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')
            ->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')
            ->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')
            ->select('aud_hc_protocolo.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'aud_hc_protocolo.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')
            ->orderBy('a.fechaini', 'desc')
            ->orderBy('aud_hc_protocolo.created_at', 'desc')
            ->where('a.espid', '<>', '10')
            ->get();
            dd($protocolos);*/

        $hc_receta = hc_receta::where('id_hc', $agenda->hcid)->first();

        if (is_null($hc_receta)) {
            $input_hc_receta = [
                'id_hc'           => $id,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            hc_receta::insert($input_hc_receta);
        }
        $hc_receta = hc_receta::where('id_hc', $agenda->hcid)->first();

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();

        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $agenda->pxid)->get();

        $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo_org)->orderBy('id', 'desc')->where('estado', '1')->get();
        $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo_org)->orderBy('id', 'desc')->where('estado', '2')->get();

        $estudios = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo_org)->orderBy('id', 'desc')->where('estado', '3')->get();
        $biopsias = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo_org)->orderBy('id', 'desc')->where('estado', '4')->get();

        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();

        $seguros = Seguro::where('inactivo', '1')->get();

        if($agenda->pxid!=null){
            $pentax = Pentax::find($agenda->pxid); 
            if(!is_null($pentax)){
                if($protocolo->id_anestesiologo==null){
                    $vhprot = Aud_Hc_Protocolo::find($protocolo->id);
                    //dd($vhprot);
                    $vhprot->update(['id_anestesiologo' => $pentax->id_anestesiologo]);
                    $protocolo = DB::table('aud_hc_protocolo as p')
                    ->where('id_procedimientos_org', $protocolo_id->id_hc_procedimientos)
                    ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
                    ->join('pentax as px', 'px.hcid', 'p.hcid')
                    ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                    ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'px.id_agenda')
                    ->orderBy('p.created_at', 'desc')->first();
                }
                //dd($prot);
            }   
        }

        return $this->Crear_Editar($agenda, $cant_cortesias, $proc_completo, $protocolo, $doctores, $anestesiologos, $enfermeros, $seguros, $procedimientos_pentax, $imagenes, $id_agenda, $documentos, $estudios, $biopsias, $hc_receta,$historiaclinica);

    }

    public function actualiza_paciente(Request $request)
    {
        
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id                   = $request["id_paciente"];
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $hcid                 = $request['hcid'];
        $protocolo            = $request['protocolo'];

        $proc_com = procedimiento_completo::find($request['proc_com']);
        //dd($proc_com);
        $input1 = [
            'alergias'        => $request["alergias"],
            'observacion'     => $request["observacion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        Paciente::where('id', $id)->update($input1);

      /*  $input2 = [
            'id_procedimiento_completo' => $request['proc_com'],
            'ip_modificacion'           => $ip_cliente,
            'id_usuariomod'             => $idusuario,
        ];
        Aud_Hc_Procedimientos::find($id_hc_procedimientos)->update($input2); aqui revisar porque no recupera el procedimiento completo*/

        $input3 = [
            'id_doctor1'      => $request['id_doctor1'],
            'id_doctor2'      => $request['id_doctor2'],
            'id_doctor3'      => $request['id_doctor3'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        //Historiaclinica::find($hcid)->update($input3);
        //Pentax::where('hcid',$hcid)->update($input3);

        $input4 = [
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
            'id_anestesiologo'   => $request['id_anestesiologo'],
            'motivo'             => $request['motivo'],
            'conclusion'         => $request['conclusion'],
            'hallazgos'          => $request['hallazgos'],
            'complicacion'       => $request['complicacion'],
            'estado_paciente'    => $request['estado_paciente'],
            'plan'               => $request['plan'],
            'estudio_patologico' => $request['estudio_patologico'],
            'id_protocolo_org'   => $request['protocolo2'],
            'ntxt_procedimiento' => $request['ntxt_procedimiento'],
            'realizado'          => $request['realizado']
        ];

        Aud_Hc_Protocolo::find($protocolo)->update($input4);

        $hc_anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', $id_hc_procedimientos)->first();

        if (!is_null($proc_com)) {

            $an_var = $proc_com->estado_anestesia;

        } else {

            $an_var = '1';

        }

        $input_an_crea = [
            'id_hc'                => $hcid,
            'id_hc_procedimientos' => $id_hc_procedimientos,
            'id_anestesiologo'     => $request['id_anestesiologo'],
            'estado'               => $an_var,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
        ];

        $input_an_mod = [

            'id_anestesiologo' => $request['id_anestesiologo'],
            'estado'           => $an_var,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,

        ];

        if (!is_null($hc_anestesiologia)) {

            $hc_anestesiologia->update($input_an_mod);

        } else {

            if (!is_null($proc_com)) {

                if ($proc_com->estado_anestesia == '1') {

                    Hc_Anestesiologia::create($input_an_crea);

                }

            }

        }

        return "ok";

    }

    public function actualiza_paciente_aud(Request $request)
    {
        //return $request->all();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id                   = $request["id_paciente"];
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $hcid                 = $request['hcid'];
        $protocolo            = $request['protocolo'];

        $proc_com = procedimiento_completo::find($request['proc_com']);
        //dd($proc_com);
        
        $input1 = [
            'alergias'        => $request["alergias"],
            'observacion'     => $request["observacion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        Paciente::where('id', $id)->update($input1);

        /*$input2 = [
            'id_procedimiento_completo' => $request['proc_com'],
            'ip_modificacion'           => $ip_cliente,
            'id_usuariomod'             => $idusuario,
        ];
        Aud_Hc_Procedimientos::find($id_hc_procedimientos)->update($input2);*/

        $input3 = [
            'id_doctor1'      => $request['id_doctor1'],
            'id_doctor2'      => $request['id_doctor2'],
            'id_doctor3'      => $request['id_doctor3'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        //Historiaclinica::find($hcid)->update($input3);
        //Pentax::where('hcid',$hcid)->update($input3);

        $input4 = [
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
            'id_anestesiologo'   => $request['id_anestesiologo'],
            'motivo'             => $request['motivo'],
            'conclusion'         => $request['conclusion'],
            'hallazgos'          => $request['hallazgos'],
            'complicacion'       => $request['complicacion'],
            'estado_paciente'    => $request['estado_paciente'],
            'plan'               => $request['plan'],
            'estudio_patologico' => $request['estudio_patologico'],
            'id_protocolo_org'   => $request['protocolo_org'],
            'ntxt_procedimiento' => $request['ntxt_procedimiento'],
        ];

        Aud_Hc_Protocolo::find($protocolo)->update($input4);

        $hc_anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', $id_hc_procedimientos)->first();
        //dd($hc_anestesiologia);
        if (!is_null($proc_com)) {

            $an_var = $proc_com->estado_anestesia;

        } else {

            $an_var = '1';

        }

        $input_an_crea = [
            'id_hc'                => $hcid,
            'id_hc_procedimientos' => $id_hc_procedimientos,
            'id_anestesiologo'     => $request['id_anestesiologo'],
            'estado'               => $an_var,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
        ];

        $input_an_mod = [

            'id_anestesiologo' => $request['id_anestesiologo'],
            'estado'           => $an_var,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,

        ];

        if (!is_null($hc_anestesiologia)) {

            $hc_anestesiologia->update($input_an_mod);

        } else {

            if (!is_null($proc_com)) {

                if ($proc_com->estado_anestesia == '1') {

                    Hc_Anestesiologia::create($input_an_crea);

                }

            }

        }

        return "ok";

    }

     public function actualizar_doctor_seguro(Request $request)
    {
        //return $request->all();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $input_hc_procedimiento = [
            'id_doctor_examinador'         => $request["id_doctor_examinador"],
            'id_seguro'                    => $request["id_seguro"],
            'id_empresa'                   => $request["id_empresa"],
            'observaciones'                => $request["observaciones"],
            'ip_modificacion'              => $ip_cliente,
            'id_usuariomod'                => $idusuario,
            'id_procedimientos_org'        => $request["id_hc_procedimiento_org"],
        ];

        $id_hc_procedimiento = $request['id'];
        //dd($id_hc_procedimiento);
        //return $input_evo;
        Aud_Hc_Procedimientos::where('id', $id_hc_procedimiento)
            ->update($input_hc_procedimiento);
        return "ok";
    }

    public function cargar_empresa($id_proc, $id_agenda, $id_seguro)
    {
        //dd($id_proc);
        $agenda = Agenda::find($id_agenda);
        $seguro = Seguro::find($id_seguro);
        if ($seguro->tipo == 0) {
            $empresas = DB::table('convenio as c')->join('empresa as e', 'e.id', 'c.id_empresa')->select('e.*')->where('c.id_seguro', $seguro->id)->get();
        } else {
            $empresas = DB::table('empresa as e')->where('e.estado', '1')->where('e.id', '<>', '9999999999')->get();
        }
        $procedimiento = Aud_Hc_Procedimientos::find($id_proc);
        //dd($procedimiento);
        $id_empresa    = $procedimiento->id_empresa;

        if ($procedimiento->id_empresa == null) {
            $procedimiento->update(['id_empresa' => $agenda->id_empresa]);
            $id_empresa = $agenda->id_empresa;
        }

        return view('auditoria_hc_admision.empresas', ['empresas' => $empresas, 'id_empresa' => $id_empresa]);
    }


     public function agregar_cie10(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //dd($ip_cliente);
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }
        

        $id_cie0 = $request['hc_id_procedimiento'];
        //dd($id_cie0);

        $cie10_tabla = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $id_cie0)->get();
        //dd($cie10_tabla);

       
        /*  $cie10_tabla = DB::table('aud_hc_cie10')->where('hc_cie10.hc_id_procedimiento', $id)->get();

          if (!is_null($cie10_tabla)) {

            foreach ($cie10_tabla as $value) {

                $id_cie0aud =  $value->id_cie10_org;
                 //dd($value);
                $input2 = [
                    'hc_id_procedimiento'   => $request['hc_id_procedimiento'],
                    'hcid'                  => $request['hcid'],
                    'cie10'                 => $request['codigo'],
                    'ingreso_egreso'        => $request['in_eg'],
                    'presuntivo_definitivo' => $request['pre_def'],
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    //'id_cie10_org'        => $id_cie0aud,
                ];

                $id = Aud_Hc_Cie10::insertGetId($input2);
                $count = Aud_Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();
                //dd($count);
                $cie10 = Aud_Hc_Cie10::find($id);
            }

        }*/

        $input2 = [
            'hcid'                  => $request['hcid'],
            'cie10'                 => $request['codigo'],
            'hc_id_procedimiento'   => $request['hc_id_procedimiento'],
            'ingreso_egreso'        => $request['in_eg'],
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        
        $id = Aud_Hc_Cie10::insertGetId($input2);
        //dd($id);
        $count = Aud_Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        $cie10 = Aud_Hc_Cie10::find($id);
        

        

        $c3 = Cie_10_3::find($cie10->cie10);

        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }
       

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }


    public function cargar_cie10_aud($id)
    {
        //dd($id);
        $c     = [];
        $x     = 0;
        $cie10 = DB::table('aud_hc_cie10')->where('aud_hc_cie10.hc_id_procedimiento', $id)->get();
        //dd($cie10);
        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {
                //dd($value);
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $c[$x] = ['cie10' => $value->cie10, 'id' => $value->id, 'descripcion' => $c3->descripcion, 'pre_def' => $value->presuntivo_definitivo, 'ingreso_egreso' => $value->ingreso_egreso];
                }
                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $c[$x] = ['cie10' => $value->cie10, 'id' => $value->id, 'descripcion' => $c4->descripcion, 'pre_def' => $value->presuntivo_definitivo, 'ingreso_egreso' => $value->ingreso_egreso];
                }
                $x++;
            }

            return $c;

        } else {
            return "no";
        }

    }
    public function eliminar($id)
    {
        //dd($id);
        $cie10 = DB::table('aud_hc_cie10')->where('id', $id)->delete();
        return "ok";

    }

    public function formato($id)
    {

        $evolucion = Hc_Evolucion::find($id);

        $cardio = $evolucion->historiaclinica->cardio;
        //dd($cardio);
        $paciente = $evolucion->historiaclinica->paciente;

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')
            ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'hp.id')
            ->join('users as u', 'u.id', 'hp.id_doctor_examinador')
            ->join('agenda as a', 'a.id', 'hc.id_agenda')
            ->join('especialidad as e', 'e.id', 'a.espid')
            ->select('he.*', 'u.apellido1', 'u.apellido2', 'u.nombre1', 'e.nombre', 'a.proc_consul','he.secuencia','e.nombre as especialidad')
            ->where('hc.id_paciente', $paciente->id)
            //->where('a.proc_consul','0')
            //->where('he.secuencia','0')
            ->orderBy('he.created_at', 'desc')->limit(15)->get();
        $doctores = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //dd($historiaclinica);
        return view('hc_admision/evolucion/cardiologia', ['doctores'=>$doctores,'evolucion' => $evolucion, 'historiaclinica' => $historiaclinica, 'cardio' => $cardio]);

    }
    public function cie10_nombre(Request $request)
    {
        $nombre_cie10 = $request['term'];
        $data         = null;

        $seteo = '%' . $nombre_cie10 . '%';

        $query1 = "SELECT id, descripcion
                  FROM cie_10_3
                  WHERE descripcion like '" . $seteo . "' or id like '" . $seteo . "'  LIMIT 100
                  ";

        $query2 = "SELECT id, descripcion
                  FROM cie_10_4
                  WHERE descripcion like '" . $seteo . "' or id like '" . $seteo . "'  LIMIT 100
                  ";

        $cie10_3 = DB::select($query1);
        $cie10_4 = DB::select($query2);

        foreach ($cie10_3 as $value) {
            $data[] = array('value' => '(' . $value->id . ') ' . $value->descripcion, 'id' => $value->id);
        }
        foreach ($cie10_4 as $value) {
            $data[] = array('value' => '(' . $value->id . ') ' . $value->descripcion, 'id' => $value->id);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

     public function cie10_nombre2(Request $request)
    {

        $nombre_cie10 = $request['cie10'];
        //dd($nombre_cie10);
        $data = null;

        $porciones = explode(")", $nombre_cie10);
        if (count($porciones) > 1) {
            $nombre_cie10 = substr($porciones[0], 1);

        } else {
            return '0';
        }

        $cie10_3 = Cie_10_3::where('id', $nombre_cie10)->get();
        $cie10_4 = Cie_10_4::where('id', $nombre_cie10)->get();
        //dd($cie10_3);

        foreach ($cie10_3 as $value) {
            $data[] = array('value' => $value->descripcion, 'id' => $value->id);
        }
        foreach ($cie10_4 as $value) {
            $data[] = array('value' => $value->descripcion, 'id' => $value->id);
        }
        if (count($data) > 0) {
            return $data[0];
        } else {
            return '0';
        }
    }
    public function descarga_seleccion($id_protocolo, $agenda_ori, $ruta)
    {
        //dd($id_protocolo);
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo              = Aud_Hc_Protocolo::find($id_protocolo);
        //dd($protocolo->id_protocolo_org);
        $imagenes               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id_protocolo_org)->where('estado', '1')->orderBy('id', 'desc')->get();
        //dd($imagenes);
        $agenda                 = Agenda::find($protocolo->historiaclinica->id_agenda);
        //dd($agenda);
        $paciente               = paciente::find($protocolo->historiaclinica->id_paciente);
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->auditoria_procedimiento->id_procedimiento_completo);
       
        //dd($procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);
        $imagenes2              = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  aud_hc_protocolo hc_proto,  historiaclinica hc, paciente p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id_protocolo_org AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $protocolo->historiaclinica->id_paciente . "'
                ORDER BY id desc;");
        //dd($imagenes2);
        if (is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)) {
            $input = [
                'id_doctor_examinador2' => $protocolo->auditoria_procedimiento->id_doctor_examinador,
            ];
            Aud_Hc_Procedimientos::where('id', $protocolo->auditoria_procedimiento->id)->update($input);

            $protocolo = Aud_Hc_Protocolo::find($id_protocolo);
        }
        $doctores = User::where('id_tipo_usuario', 3)->orderBy('apellido1')->get();
        //return $doctores;
        //return $protocolo->procedimiento->id_doctor_examinador2;
        //dd($imagenes2);
        return view('auditoria_hc_admision/video/seleccion', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'imagenes2' => $imagenes2, 'paciente' => $paciente, 'edad' => $edad, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia, 'agenda' => $agenda, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta, 'id' => $id_protocolo, 'doctores' => $doctores]);
    }

    public function fecha_convenios(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id        = $request['id'];

        $protocolo = Aud_Hc_Protocolo::find($id);
        //dd($protocolo);
        $input_ex1 = [
            'fecha'           => $request['fecha'],
            'referido_por'    => $request['referido'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        //$procedimiento = Aud_Hc_Procedimientos::find($protocolo->id_hc_procedimientos);
         $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();

        //dd($procedimiento);
        $input_ex2 = [
            'id_doctor_examinador2' => $request['id_doctor_examinador2'],
            'id_doctor_responsable' => $request['id_doctor_responsable'],
        ];

        $procedimiento->update($input_ex2);
        $protocolo->update($input_ex1);
        return "ok";
    }

    public function pr_modal($id)
    {
        //dd($id);
        $protocolo = Aud_Hc_Protocolo::find($id);
        //dd($protocolo);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        //dd($protocolo,$id_agenda);
        $agenda    = Agenda::find($id_agenda);
        //dd($protocolo,$id_agenda,$agenda);
        if ($protocolo->hora_inicio == null) {
            $hora_inicio = substr($agenda->fechaini, 11, 5);
        } else {
            $hora_inicio = substr($protocolo->hora_inicio, 0, 5);

        }

        $fecha_operacion = $protocolo->fecha_operacion;
        if ($fecha_operacion == null) {
            $fecha_operacion = substr($agenda->fechaini, 0, 10);
        }

        $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador2;
        //dd($protocolo->auditoria_procedimiento);
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

        $id_doctor_ayudante_con = $protocolo->auditoria_procedimiento->id_doctor_ayudante_con;

        if ($id_doctor_ayudante_con == null) {
            if ($protocolo->historiaclinica->doctor_2 != null) {
                $id_doctor_ayudante_con = $protocolo->historiaclinica->doctor_2->id;
            }

        }

        $cardiologia = Agenda::where('agenda.id_paciente', $agenda->id_paciente)
        ->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')
        ->join('hc_cardio as c', 'c.hcid', 'h.hcid')
        ->where('espid', '8')
        ->select('agenda.*', 'h.hcid', 'c.resumen', 'c.id as id_cardio')
        ->orderBy('fechaini', 'desc')
        ->first();
        //return $cardiologia;
        //dd($id_doctor);
        //dd($protocolo->tipo_anestesia);
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();
        //dd($protocolo);
        return view('auditoria_hc_admision/evolucion/pr_modal', ['protocolo' => $protocolo, 'hora_inicio' => $hora_inicio, 'id_doctor_firma' => $id_doctor_firma, 'doctores' => $doctores, 'fecha_operacion' => $fecha_operacion, 'id_doctor_ayudante_con' => $id_doctor_ayudante_con, 'cardiologia' => $cardiologia]);

    }

    public function guardar_op(Request $request)
    {
        //dd($request->all());
        $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
        //dd($protocolo->auditoria_procedimiento);
        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);

        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];
        $protocolo->auditoria_procedimiento->update($input2);

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();
        //dd($firma);
        $id = $protocolo->auditoria_procedimiento->id_procedimientos_org;
        //dd($id);
        $id_proc = $protocolo->auditoria_procedimiento->id;
        //dd($id_proc);
        $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();
        //dd($evolucion);
        $indicaciones = [];
        foreach ($evolucion as $value) {
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();
            //dd($indicaciones);
        }

        $procedimiento = Aud_Hc_Procedimientos::find($id_proc);
        //dd($procedimiento);
        $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_procedimientos_org)->get();
        //dd($proc_finales);
        $id_principal  = '';

         if(Auth::user()->id == '0951561075'){
            //dd($procedimiento->id_procedimientos_org);
        }

        foreach ($proc_finales as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $id_principal = $px->id_procedimiento;
                break;
            }
        }

        //dd($procedimiento);

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
        //dd($historiaclinica);
        $data            = $historiaclinica;
        $fecha_operacion = $request->fecha_operacion;
        $hora_ini        = $request->hora_ini;

        $cardiologia = null;
        if ($request->cardio == '1') {

            if ($request->id_cardio != null) {

                $cardiologia = Cardiologia::find($request->id_cardio);
                //dd($cardiologia);
            }

        }





        if (count($evolucion) <= '1') {
            $view = \View::make('auditoria_hc_admision.evolucion.evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
            $pdf  = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
       // hasta aqui llega el request cuando pondo desde tipo anestecia 8 a 12
       // los otros si cogen la vista dd($request->all());
        if ($id_principal != '') {

            // ENTRR CON LO NUEVOS OPTION DEL EXCEL
            //PH 24H
            //MANOMETRIA ESOFAGICA 2H Y 30 MIN
            //MANOMETRIA ANORECTAL 2H Y 30 MIN
            //CAPSULA ENDOSCOPICA 24H
            //EDA + PH METRIA 24 H
            //dd($request->all());
            if ($request->tipo_anestesia == '8' || $request->tipo_anestesia == '9' || $request->tipo_anestesia == '10' || $request->tipo_anestesia == '11' || $request->tipo_anestesia == '12') {

           
                
                $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $request->tipo_anestesia)->where('estado', '1')->first();

                $minutos = $hor_f->minutos;

                //dd($minutos);
                

                if ($minutos >= 1440) {
                    $fecha_final = strtotime("+$minutos minute", strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    //dd($hora_fin);
                    $view = \View::make('auditoria_hc_admision.evolucion.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();


                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('auditoria_hc_admision.evolucion.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }

            } else {
                    
                //7: FIBROSCAN 20: MANOMETRIA ANORECTAL 2: MANOMETRÍA ESOFAGICA
                if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {



                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('auditoria_hc_admision.evolucion.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

                }
                // 3: CÁPSULA ENDOSCÓPICA 1: PH-METRÍA
                if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                    // add PH-METRIA Y EDA 16 Nov
                    $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    $view        = \View::make('auditoria_hc_admision.evolucion.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

                }
            }


        } else {

        }
        

        if ($request->tipo_anestesia == 'GENERAL') {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);

            //new changes times with new tables

            $view = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        
        if ($request->tipo_anestesia == 'GENERAL90') {
            $hora_media = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL120') {
            $hora_media = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_fin);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL150') {
            $hora_media = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL180') {
            $hora_media = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+240 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media,$hora_fin);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL210') {
            $hora_media = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+270 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'SEDACION') {
            $hora_media = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($hora_media,$pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        //hasta aqui revisado

        //change proced with new tables
        //dd($procedimiento);
        if (($procedimiento->id_procedimiento_completo == '38') || ($procedimiento->id_procedimiento_completo == '12') || ($procedimiento->id_procedimiento_completo == '27')) {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if (($procedimiento->id_procedimiento_completo == '13') || ($procedimiento->id_procedimiento_completo == '14')) {
            $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
            $fecha_final = date('d/m/Y', $fecha_final);
            $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
            $hora_fin    = date('H:i', $hora_fin);
            $view        = \View::make('hc_admision.formato.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }

        //return "hola";
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

    }
    public function nuevo_guardar_op(Request $request)
    {
        //dd($request->all());
        $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
        //dd($protocolo->auditoria_procedimiento);

        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);

        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];
        $protocolo->auditoria_procedimiento->update($input2);

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();
        //dd($firma);
        $id = $protocolo->auditoria_procedimiento->id_procedimientos_org;
        //dd($id);
        $id_proc = $protocolo->auditoria_procedimiento->id;
        //dd($id_proc);
        $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();
        if(Auth::user()->id == '9999966654'){
            //dd($evolucion);
        }
        //dd($evolucion);
        $indicaciones = [];
        foreach ($evolucion as $value) {
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();
            //dd($indicaciones);
        }

        $procedimiento = Aud_Hc_Procedimientos::find($id_proc);
        //dd($procedimiento);
        $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_procedimientos_org)->get();
        //dd($proc_finales);
        if(Auth::user()->id == '9999966654'){
            //dd($proc_finales,$procedimiento);
        }
        $id_principal  = '';

         if(Auth::user()->id == '0951561075'){
            //dd($procedimiento->id_procedimientos_org);
        }

        foreach ($proc_finales as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $id_principal = $px->id_procedimiento;
                break;
            }
        }

        //dd($procedimiento);

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
        //dd($historiaclinica);
        $data            = $historiaclinica;
        $fecha_operacion = $request->fecha_operacion;
        $hora_ini        = $request->hora_ini;

        $cardiologia = null;
        if ($request->cardio == '1') {

            if ($request->id_cardio != null) {

                $cardiologia = Cardiologia::find($request->id_cardio);
                //dd($cardiologia);
            }

        }





        if (count($evolucion) <= '1') {
            $view = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
            $pdf  = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
       // hasta aqui llega el request cuando pondo desde tipo anestecia 8 a 12
       // los otros si cogen la vista dd($request->all());
        if ($id_principal != '') {

            // ENTRR CON LO NUEVOS OPTION DEL EXCEL
            //PH 24H
            //MANOMETRIA ESOFAGICA 2H Y 30 MIN
            //MANOMETRIA ANORECTAL 2H Y 30 MIN
            //CAPSULA ENDOSCOPICA 24H
            //EDA + PH METRIA 24 H
            //dd($request->all());
            if ($request->tipo_anestesia == '8' || $request->tipo_anestesia == '9' || $request->tipo_anestesia == '10' || $request->tipo_anestesia == '11' || $request->tipo_anestesia == '12') {

           
                
                $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $request->tipo_anestesia)->where('estado', '1')->first();

                $minutos = $hor_f->minutos;

                //dd($minutos);
                

                if ($minutos >= 1440) {
                    $fecha_final = strtotime("+$minutos minute", strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    //dd($hora_fin);
                    $view = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();


                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }

            } else {
                    
                //7: FIBROSCAN 20: MANOMETRIA ANORECTAL 2: MANOMETRÍA ESOFAGICA
                if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {



                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

                }
                // 3: CÁPSULA ENDOSCÓPICA 1: PH-METRÍA
                if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                    // add PH-METRIA Y EDA 16 Nov
                    $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    $view        = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

                }
            }


        } else {

        }
        

        if ($request->tipo_anestesia == 'GENERAL') {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);

            //new changes times with new tables

            $view = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        
        if ($request->tipo_anestesia == 'GENERAL90') {
            $hora_media = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL120') {
            $hora_media = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_fin);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL150') {
            $hora_media = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL180') {
            $hora_media = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+240 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media,$hora_fin);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'GENERAL210') {
            $hora_media = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+270 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($pdf,$hora_media);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if ($request->tipo_anestesia == 'SEDACION') {
            $hora_media = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('auditoria_hc_admision.evolucion.nuevo_evolucionformato', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //dd($hora_media,$pdf);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        //hasta aqui revisado

        //change proced with new tables
        //dd($procedimiento);
        /*if (($procedimiento->id_procedimiento_completo == '38') || ($procedimiento->id_procedimiento_completo == '12') || ($procedimiento->id_procedimiento_completo == '27')) {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if (($procedimiento->id_procedimiento_completo == '13') || ($procedimiento->id_procedimiento_completo == '14')) {
            $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
            $fecha_final = date('d/m/Y', $fecha_final);
            $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
            $hora_fin    = date('H:i', $hora_fin);
            $view        = \View::make('hc_admision.formato.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }*/

        //return "hola";
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

    }

    public function guardar_op_2(Request $request)
    {
        //dd($request->all());
        $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
        //dd($protocolo);
        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);

        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];

        //dd($protocolo->auditoria_procedimiento);

        $protocolo->auditoria_procedimiento->update($input2);

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('auditoria_hc_admision.protocolo.prot_operatico', compact('protocolo', 'age', 'agenda', 'firma'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('HCU_017.pdf');
        //return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');

    }

     public function guardar_op_cpre_eco(Request $request)
    {
        //dd($request->all());
        $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
        //dd($protocolo);
        //$cpre_eco  = Hc_cpre_eco::where('hcid', $protocolo->hcid)->first();
        $cpre_eco  = Aud_Hc_Cpre_Eco::where('hcid', $protocolo->hcid)->first();
        //dd($protocolo,$cpre_eco);
        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);

        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
            'id_doctor1'          => $request->id_doctor_examinador2,
            'id_doctor2'          => $request->id_doctor_ayudante_con,
        ];

        if (!is_null($cpre_eco)) {
            $cpre_eco->update($input);
        }

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();
        //dd($firma,$request->all());
        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $age    = Carbon::createFromDate(substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($protocolo->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;
  //dd($protocolo->usuario_anestesiologo);
        $view = \View::make('auditoria_hc_admision.protocolo.prot_operatorio_cpre_eco', compact('protocolo', 'age', 'agenda', 'firma', 'cpre_eco'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('HCU_017.pdf');
        //return $pdf->stream($protocolo->historiaclinica->id_paciente . '_PR_' . $protocolo->id . '.pdf');
    }

    public function pr_modal_protocolo($id)
    {
        //dd($id);
        $protocolo = Aud_Hc_Protocolo::find($id);
        //dd($protocolo);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);
        //dd($agenda);

        //$hora_inicio = substr($agenda->fechaini,11,5);

        if ($protocolo->hora_inicio == null) {
            $hora_inicio = substr($agenda->fechaini, 11, 5);
        } else {
            $hora_inicio = substr($protocolo->hora_inicio, 0, 5);
        }

        $fecha_operacion = $protocolo->fecha_operacion;
        if ($fecha_operacion == null) {
            $fecha_operacion = substr($agenda->fechaini, 0, 10);
        }

        $id_doctor_firma = $protocolo->auditoria_procedimiento->id_doctor_examinador2;
        //dd($protocolo->auditoria_procedimiento);
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

        $id_doctor_ayudante_con = $protocolo->auditoria_procedimiento->id_doctor_ayudante_con;

        if ($id_doctor_ayudante_con == null) {
            if ($protocolo->historiaclinica->doctor_2 != null) {
                $id_doctor_ayudante_con = $protocolo->historiaclinica->doctor_2->id;
            }

        }

        $cpre_eco = Hc_cpre_eco::where('hcid', $protocolo->hcid)->first();

        //dd($id_doctor_firma);
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        return view('auditoria_hc_admision/protocolo/pr_modal', ['protocolo' => $protocolo, 'hora_inicio' => $hora_inicio, 'id_doctor_firma' => $id_doctor_firma, 'doctores' => $doctores, 'fecha_operacion' => $fecha_operacion, 'id_doctor_ayudante_con' => $id_doctor_ayudante_con, 'cpre_eco' => $cpre_eco,'anestesiologos' => $anestesiologos]);

    }


    public function modal_cpre_eco($hcid)
    {
        //dd($hcid);
        $cpre_eco = Aud_Hc_Cpre_Eco::where('hcid', $hcid)->first();
        //dd($cpre_eco->hallazgos);
        $proc     = Aud_Hc_Protocolo::where('hcid', $hcid)->get();
        //dd($proc);
        $cpre_eco_org   = Hc_cpre_eco::where('hcid', $hcid)->first();
        //dd($cpre_eco_org);
        $texto    = '';
        $texto1    = '';
        foreach ($proc as $p) {

            $texto = $texto . $p->hallazgos . '<br>';
            $texto1 = $texto1 . $p->conclusion . '<br>';
        }

        //dd($texto1);
        //dd($cpre_eco);

        return view('auditoria_hc_admision/protocolo/cpre_eco_modal', ['cpre_eco' => $cpre_eco, 'hcid' => $hcid, 'texto' => $texto, 'texto1' => $texto1]);

    }

     public function modal_crear_editar(Request $request)
    {
        //dd($request -> all());
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $hcid       = $request->hcid;
        $cpre_eco_org   = Hc_cpre_eco::where('hcid', $hcid)->first();
        //dd($cpre_eco_org); 
        if (is_null($cpre_eco_org)) {
            $input = [

                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'hcid'            => $request["hcid"],
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Hc_cpre_eco::create($input);  
        }   
        $cpre_eco   = Aud_Hc_Cpre_Eco::where('hcid', $hcid)->first();
        //dd($cpre_eco);
        $cpre_eco_org   = Hc_cpre_eco::where('hcid', $hcid)->first();

        
        if (is_null($cpre_eco)) {     

            $input = [
                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'hcid'            => $request["hcid"],
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_cpre_eco_org'   => $cpre_eco_org->id,
            ];
            Aud_Hc_Cpre_Eco::insertGetId($input);
            //$id = Aud_Hc_Cie10::insertGetId($input2);
            return "Procedimiento Auditoria CPRE + ECO Guardado";
        } else {
            $input = [
                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $cpre_eco->update($input);
            return "Procedimiento CPRE + ECO Auditoria Actualizado";
        }

    }

    private function carga_hc($hcid)
    {

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'agenda.id_sala as idsala')->first();

        return $historiaclinica;
    }

    public function carga_documentos_union($hcid, $proc_consul, $tipo)
    {

        $historia = Historiaclinica::find($hcid);
        $fecha    = date('Y-m-d', strtotime($historia->created_at)) . ' 00:00:00';

        // <TIPO> <PROC_CONSUL> <HCID> <SEGURO> <SUBSEGURO>

        $documentos1 = $this->carga_documentos($tipo, 2, $hcid, null, null);
        //dd($documentos1->get());

        $documentos2 = $this->carga_documentos($tipo, $proc_consul, $hcid, null, null);
        //dd($documentos2->get());

        $documentos3 = $this->carga_documentos($tipo, 2, $hcid, $historia->id_seguro, $historia->id_subseguro);
        //dd($documentos3->get());

        $documentos4 = $this->carga_documentos($tipo, $proc_consul, $hcid, $historia->id_seguro, $historia->id_subseguro);
        //dd($documentos4->get());

        $documentos5 = $this->carga_documentos(null, $proc_consul, $hcid, null, null);
        //dd($documentos5->get());

        $documentos6 = $this->carga_documentos($tipo, $proc_consul, $hcid, $historia->id_seguro, null);
        //dd($documentos6->get());

        $documentos = $documentos1->union($documentos2)->union($documentos3)->union($documentos4)->union($documentos5)->union($documentos6)->orderby('secuencia')->get();

        //dd($documentos);
        return $documentos;
    }
     private function carga_documentos($tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro)
    {

        $historia = Historiaclinica::find($hcid);
        $paciente = $historia->paciente;
        $age      = 0;
        if ($paciente->fecha_nacimiento != null) {
            $age = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $principal = '1';
        if ($historia->id_subseguro != null) {
            $principal = Subseguro::find($historia->id_subseguro)->principal;
        }

        $fecha = date('Y-m-d', strtotime($historia->created_at)) . ' 00:00:00';

        if ($tipo === null) {
            $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', null)->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre')->where('principal', null)->orwhere('principal', $principal);

            //dd("IN",$tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro,$documentos->get());

        }

        if ($id_seguro === null) {

            if ($age >= '45') {
                $documentos = DB::table('documento')
                    ->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                    ->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)->whereNull('principal')
                    ->orWhere(function ($query) use ($proc_consul, $tipo, $fecha, $principal) {
                        $query->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                            ->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)->where('principal', $principal);
                    })
                    ->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')
                    ->select('documento.*', 'tipousuario.nombre as tnombre');

                //dd("IN",$tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro,$documentos->get());
            } else {
                $documentos = DB::table('documento')
                    ->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                    ->where('tipo_documento', '<>', 'CARDIO')->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)
                    ->whereNull('principal')
                    ->orWhere(function ($query) use ($proc_consul, $tipo, $fecha, $principal) {
                        $query->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)
                            ->where('tipo_documento', '<>', 'CARDIO')->whereNull('documento.id_seguro')->whereDate('documento.created_at', '<=', $fecha)
                            ->where('principal', $principal);
                    })
                    ->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')
                    ->select('documento.*', 'tipousuario.nombre as tnombre');

                //dd("IN", $tipo, $proc_consul, $hcid, $id_seguro, $id_subseguro, $principal, $documentos->get());
            }
        } else {

            if ($id_subseguro != null) {
                $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', $id_seguro)->where('documento.id_subseguro', $id_subseguro)->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre');
            } else {
                $documentos = DB::table('documento')->where('documento.estado', '1')->where('documento.proc_consul', $proc_consul)->where('tipo_seguro', $tipo)->where('documento.id_seguro', $id_seguro)->whereNull('documento.id_subseguro')->join('tipousuario', 'tipousuario.id', 'documento.id_tipo_usuario')->whereDate('documento.created_at', '<=', $fecha)->select('documento.*', 'tipousuario.nombre as tnombre');
                if ($proc_consul == '1') {
                    //dd($proc_consul, $tipo, $hcid, $id_seguro, $id_subseguro,$documentos->get());
                }
            }

            //dd($documentos->get());

        }

        //dd($documentos->get());

        return $documentos;
    }
    
    public function control_doc(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['hcid'])) {
            $hcid = $request['hcid'];
        } else {
            $hcid = Historiaclinica::where('id_agenda', $request['cita'])->first()->hcid;
        }

        $url_doctor = $request['url_doctor'];
        //dd($url_doctor);
        $unix       = $request['unix'];
        $protocolo  = $request['protocolo'];
        //$historia        = $this->carga_hc($archivo_historico->id_historia);
        $historiaclinica = $this->carga_hc($hcid);
        //dd($historia_clinica);
        $procs = [];
        if ($historiaclinica->proc_consul == '1') {

            $procs = Procedimiento::find($historiaclinica->id_procedimiento)->observacion;
            //dd($procs);
            $procs_ag = AgendaProcedimiento::where('id_agenda', $historiaclinica->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }
        }

        $hcid        = $historiaclinica->hcid;
        $proc_consul = $historiaclinica->proc_consul;
        $tipo        = $historiaclinica->tipo;
        $id_doctor1  = $historiaclinica->id_doctor1;

        //$hc_protocolo = hc_protocolo::where('id',$protocolo)->first();

        $protocolo = DB::table('aud_hc_protocolo as p')
        ->where('p.hcid', $hcid)
        ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos')
        ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
        ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
        ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'hp.id as id_hcproc', 'hp.id_seguro', 'hp.id_empresa')
        ->orderBy('p.created_at', 'desc')
        ->first();
        //dd($protocolo);

        $empresas = Empresa::where('estado', '1')->get();
        $seguros  = Seguro::where('inactivo', '1')->get();

        $documentos = $this->carga_documentos_union($hcid, $proc_consul, $tipo);

        return view('auditoria_hc_admision/admision/index', ['historia' => $historiaclinica, 'procs' => $procs, 'hcid' => $hcid, 'proc_consul' => $proc_consul, 'tipo' => $tipo, 'url_doctor' => $url_doctor, 'unix' => $unix, 'id_doctor1' => $id_doctor1, 'documentos' => $documentos, 'protocolo' => $protocolo, 'empresas' => $empresas, 'seguros' => $seguros]);
    }

    public function fecha_convenios_documentos(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id        = $request['id'];

        $protocolo = Aud_Hc_Protocolo::find($id);
        //dd($protocolo);

        $input_ex1 = [
            'fecha'           => $request['fecha'],
            'referido_por'    => $request['referido'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        //$procedimiento = Aud_Hc_Procedimientos::find($protocolo->id_hc_procedimientos);
         $procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $protocolo->id_hc_procedimientos)->first();
        //dd($procedimiento);
        
        $input_ex2 = [
            'id_doctor_examinador2' => $request['id_doctor_examinador2'],
            'id_doctor_responsable' => $request['id_doctor_responsable'],
        ];

        $procedimiento->update($input_ex2);
        $protocolo->update($input_ex1);
        return "ok";
    }
    public function seguro_empresa(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id_hcproc = $request['id_hcproc'];

        $procedimiento = Aud_Hc_Procedimientos::find($id_hcproc);
        //dd($procedimiento);
        $input_ex2 = [
            'id_empresa' => $request['id_empresa'],
            'id_seguro'  => $request['id_seguro'],
        ];

        $procedimiento->update($input_ex2);
        return "ok";
    }

    public function reporte_cardiologia ($id,$id_agenda,Request $request){
       

        $agenda = Agenda::find($id_agenda);
        $id_empresa       = $request->session()->get('id_empresa');
        $paciente  =    $agenda->paciente;
        $age = $edad = (new DateTime(date("Y-m-d")))->diff(new DateTime($agenda->paciente->fecha_nacimiento))->y;
        $general       = historiaclinica::where('id_agenda', $agenda->id)->first();
        $view          = \View::make('paciente.reporte_cardiologia', compact('paciente', 'empresa', 'age', 'general'))->render();
        $pdf           = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portraid');
        return $pdf->stream('historia Cardiologia' . $paciente->apellido1 . '_' . $paciente->nombre1 . '.pdf');
    }

}
