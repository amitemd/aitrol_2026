<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Empresa;
use Sis_medico\Hosp_dato_paciente;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Hosp_atencion_fomulario008;
use Sis_medico\Hosp_revision_formulario008;
use Sis_medico\Hosp_accidente_formulario008;
use Sis_medico\Hosp_antecedentes_formulario008;
use Sis_medico\Hosp_signos_vitales_formulario008;
use Sis_medico\Hosp_obstetrica_formulario008;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hosp_tratamiento_formulario008;
use Sis_medico\Hosp_formulario008_alta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Seguro;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\User;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Agenda;
use Sis_medico\Sala;
use Sis_medico\Hc_Log;
use Sis_medico\Log_Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta;
use Sis_medico\Ho_Form008;
use Sis_medico\hc_child_pugh;
use Sis_medico\Ho_Glasgow;
use Sis_medico\Ho_Lesiones008;
use Sis_medico\Ho_Traspaso_Sala008;
use PDF;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Log_usuario;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
use Sis_medico\Services\ClinicalEncounterService;
use Sis_medico\Services\ClinicalEmergencyTriageSyncService;
use Sis_medico\Services\ClinicalEmergencyFormService;
use Sis_medico\Services\HospitalEpisodeEmergencySource;
use Sis_medico\Services\ClinicalElectronicSignatureQr;
use Sis_medico\Services\ClinicalRecordSignatureService;
use Sis_medico\Services\DoctorSignatureService;

class Formulario008Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    // 
    public function emergencialista(Request $request)
    {
        /* $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }*/

        $ho_solicitud = Ho_solicitud::where('estado', '1')
            ->where('estado_paso', '2')
            ->join('paciente as p', 'p.id', 'ho_solicitud.id_paciente')
            ->select('p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'ho_solicitud.fecha_ingreso', 'ho_solicitud.id_paciente', 'ho_solicitud.id')->get();

        $ho_triaje = Ho_Triaje_Manchester::join('ho_solicitud as ho_s', 'ho_s.id', 'ho_triaje_manchester.id_ho_solicitud')
            ->where('ho_s.estado_paso', '1')
            ->join('paciente as p', 'p.id', 'ho_s.id_paciente')
            ->join('ho_tipo_emergencia as ho_t_e', 'ho_t_e.id', 'ho_triaje_manchester.tipo_emergencia')
            ->join('ho_prioridad_emergencia as ho_prio', 'ho_prio.id', 'ho_triaje_manchester.prioridad')
            ->select('ho_triaje_manchester.*', 'ho_s.id_paciente', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'ho_prio.nombre as nombre_prioridad', 'ho_t_e.nombre as nombre_emergencia', 'ho_triaje_manchester.prioridad', 'ho_triaje_manchester.tipo_emergencia', 'ho_s.id as id_solicitud', 'ho_s.fecha_ingreso')
            ->get();

        //dd($ho_triaje);

        return view('hospital/emergencia/emergencia', ['ho_solicitud' => $ho_solicitud, 'ho_triaje' => $ho_triaje]);
    }

    public function ingreso008(Request $request, $id_solicitud)
    {

        $fecha = date('Y-m-d');
        $seguros = Seguro::where('inactivo', '1')->get();
        //$seguro_privado = Seguro::where('inactivo','1')->where('tipo','!=','0')->get();
        //dd($id_solicitud);
        $ho_solicitud = null;
        if ($id_solicitud != '0') {
            $ho_solicitud = Ho_Solicitud::findOrFail($id_solicitud);
            app(\Sis_medico\Services\ClinicalHospitalTransferService::class)
                ->authorize($ho_solicitud, 'hospital.admission', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA'], false);
            if ($ho_solicitud->id_agenda) {
                return redirect()->route('hospital.detallep', ['id' => $ho_solicitud->id]);
            }
        }

        $salas = app(\Sis_medico\Services\ClinicalHospitalDestinationService::class)->salas([2]);
        $doctores = User::where('estado', 1)->where('id_tipo_usuario', User::roleIdForName(array('DOCTORES', 'DOCTOR')))->orderBy('apellido1')->get();
        $patientData = $ho_solicitud ? $this->admissionPatientData($ho_solicitud->id_paciente) : [];
        return view('hospital/emergencia/ingreso008', ['seguros' => $seguros, 'fecha' => $fecha,
            'ho_solicitud' => $ho_solicitud, 'id_solicitud' => $id_solicitud, 'salas' => $salas,
            'doctores' => $doctores, 'patientData' => $patientData]);
    }

    //Ingreso a emergencia tabla ingrenso_emer_008
    public function guardar2(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ingreso_emergecia = new Ingreso_emer_008();
        $ingreso_emergecia->id_paciente     = $request->cedula;
        $ingreso_emergecia->fecha_ingreso   = $request->f_admision;
        $ingreso_emergecia->save();

        $ingreso = new Hosp_dato_paciente();
        $ingreso->id_paciente             = $request->cedula;
        $ingreso->barrio                  = $request->barrio;
        $ingreso->parroquia               = $request->parroquia;
        $ingreso->canto                   = $request->canton;
        $ingreso->provincia               = $request->provincia;
        $ingreso->zona_ur                 = $request->zona;
        $ingreso->grupo_cultural          = $request->grupo_cultural;
        $ingreso->instruccion             = $request->instruccion;
        $ingreso->edad                    = $request->edad;
        $ingreso->direccion_familiar      = $request->direccion_familiar;
        $ingreso->forma_llegada           = $request->forma_llegada;
        $ingreso->fuente_informacion      = $request->fuente_informacion;
        $ingreso->telefono_inst_per_paci  = $request->telefono_inst_per_paci;

        $ingreso->save();

        return back()->with('message', 'Ha ingresado a Emergencia con Excito !');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre1' => 'required|string|max:60', 'apellido1' => 'required|string|max:60',
            'nombre2' => 'nullable|string|max:60', 'apellido2' => 'nullable|string|max:60',
            'f_nacimiento' => 'nullable|date_format:Y-m-d|before_or_equal:today',
        ], [], ['nombre1' => 'primer nombre', 'apellido1' => 'primer apellido',
            'f_nacimiento' => 'fecha de nacimiento']);
        $request->request->set('id_paso', 2);
        $result = app(\Sis_medico\Services\ClinicalHospitalModuleService::class)->ingreso_modulos($request);
        return response()->json($result, isset($result['respuesta']) && $result['respuesta'] === 'success' ? 200 : 422);
    }

    public function formulario08(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        $adicional = Hosp_dato_paciente::where('id_paciente', '=', $id_paciente)->get();
        //dd($adicional);
        $datos_paciente = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select(
                'paciente.id',
                'paciente.nombre1',
                'paciente.nombre2',
                'paciente.apellido1',
                'paciente.apellido2',
                'paciente.ciudad',
                'paciente.telefono1',
                'paciente.telefono2',
                'paciente.direccion',
                'paciente.fecha_nacimiento',
                'paciente.lugar_nacimiento',
                'paciente.id_pais',
                'paciente.sexo',
                'paciente.estadocivil',
                'paciente.ocupacion',
                'paciente.id_seguro',
                'paciente.referido',
                'paciente.telefono3',
                'paciente.telefono_llamar',
                'paciente.nombre1familiar',
                'paciente.nombre2familiar',
                'paciente.apellido1familiar',
                'paciente.apellido2familiar',
                'paciente.parentesco',
                'ingreso_emer_008.created_at'
            )
            ->get();
        dd($datos_paciente);
        return view('hospital/emergencia/formulario008', ['id_paciente' => $id_paciente, 'datos_paciente' => $datos_paciente, 'adicional' => $adicional]);
    }

    public function buscar_paciente2(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $productos;
    }

    public function buscar_paciente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();
        //dd($nombre);
        $pacientes = Paciente::orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', ['%' . $nombre . '%'])
            ->leftjoin('ho_datos_paciente as hopac', 'hopac.id_paciente', 'paciente.id')
            ->select('paciente.id as idpaciente', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.ciudad', 'paciente.telefono1', 'paciente.telefono2', 'paciente.direccion', 'paciente.estadocivil', 'paciente.ocupacion', 'paciente.referido', 'paciente.apellido1familiar', 'paciente.apellido2familiar', 'paciente.nombre1familiar', 'paciente.nombre2familiar', 'paciente.parentescofamiliar', 'paciente.sexo', 'paciente.fecha_nacimiento', 'paciente.telefono_llamar', 'paciente.telefono3', 'hopac.*')
            ->get();

        //dd($pacientes);
        foreach ($pacientes as $paciente) {
            $data[] = array(
                'value'         => $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2,
                'id'            => $paciente->idpaciente,
                'nombre1'       => $paciente->nombre1,
                'nombre2'       => $paciente->nombre2,
                'apellido1'     => $paciente->apellido1,
                'apellido2'     => $paciente->apellido2,
                'ciudad'        => $paciente->ciudad,
                'telefono1'     => $paciente->telefono1,
                'telefono2'     => $paciente->telefono2,
                'telefono3'     => $paciente->telefono3,
                'direccion'     => $paciente->direccion,
                'barrio'        => $paciente->barrio,
                'parroquia'     => $paciente->parroquia,
                'canton'        => $paciente->canton,
                'provincia'     => $paciente->provincia,
                'zona_ur'       => $paciente->zona_ur,
                'grupo_cultural' => $paciente->grupo_cultural,
                'edad'          => $paciente->edad,
                'sexo'          => $paciente->sexo,
                'estadocivil'   => $paciente->estadocivil,
                'instruccion'   => $paciente->instruccion,
                'ocupacion'     => $paciente->ocupacion,
                'empresa_trabajo' => $paciente->empresa_trabajo,
                'id_seguro'     => $paciente->id_seguro,
                'referido'      => $paciente->referido,
                'llamar_a'      => $paciente->apellido1familiar . ' ' . $paciente->apellido2familiar . ' ' . $paciente->nombre1familiar . ' ' . $paciente->nombre2familiar,
                'parentesco'    => $paciente->parentesco_afinidad,
                'telefono_llamar' => $paciente->telefono3,
                'direccion_familiar' => $paciente->direccion_familiar,
                'forma_llegada' => $paciente->forma_llegada,
                'fuente_informacion' => $paciente->fuente_informacion,
                'telefono_inst_per_paci' => $paciente->telefono_inst_per_paci,
                'fecha_nacimiento'  => $paciente->fecha_nacimiento,

            );
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function obtener_informacion(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado   = $request['nombre'];
        $data               = null;
        $nuevo_nombre       = explode(' ', $nombre_encargado);
        $seteo              = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) 
                  as completo, telefono1, telefono2,
                     id_seguro, id_pais, sexo, id, estadocivil,
                     cedulafamiliar, religion, fecha_nacimiento,
                     trabajo, lugar_nacimiento, alergias, ciudad,
                     gruposanguineo, direccion, antecedentes_pat,
                     antecedentes_fam, ocupacion, telefono_llamar
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";


        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $jquery = \Sis_medico\Seguro::query()->where('id', $nombre->id_seguro)->first();
            $pais = DB::table('pais')->where('id', $nombre->id_pais)->first();
            $data[] = array(
                'value' =>  $nombre->completo,
                'fecha'             =>  $nombre->fecha_nacimiento,
                'telefono1'         =>  $nombre->telefono1,
                'telefono2'         =>  $nombre->telefono2,
                'id_pais'           =>  $nombre->id_pais,
                'ocupacion'         =>  $nombre->ocupacion,
                'seguro'            =>  $nombre->id_seguro,
                'sexo'              =>  $nombre->sexo,
                'id'                =>  $nombre->id,
                'estadoc'           =>  $nombre->estadocivil,
                'cedula'            =>  $nombre->cedulafamiliar,
                'religion'          =>  $nombre->religion,
                'alergia'           =>  $nombre->alergias,
                'lugar_nacimiento'  =>  $nombre->lugar_nacimiento,
                'ciudad'            =>  $nombre->ciudad,
                'grupos'            =>  $nombre->gruposanguineo,
                'direccion'         =>  $nombre->direccion,
                'antp'              =>  $nombre->antecedentes_pat,
                'antf'              =>  $nombre->antecedentes_fam,
                'tipo_seguro'       =>  $jquery->nombre,
                'id_pais'           =>  $pais->nombre,
                'telefono_llamar'   =>  $nombre->telefono_llamar
            );
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function busca_paciente($id)
    {
        return $this->admissionPatientData($id) ?: 'no';
    }

    private function admissionPatientData($id)
    {
        $patient = Paciente::find($id);
        if (!$patient) return [];
        $details = Ho_Datos_Paciente::where('id_paciente', $patient->id)->first();
        // Keep the patient's identity: the administrative record has its own id.
        $data = array_merge($details ? $details->getAttributes() : [], $patient->getAttributes());
        $data['llamar_a'] = ($details->llamar_a ?? null) ?: trim(implode(' ', array_filter([
            $patient->apellido1familiar, $patient->apellido2familiar,
            $patient->nombre1familiar, $patient->nombre2familiar,
        ])));
        return $data;
    }

    public function guardar_atencio_motivo(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $atencio_motivo = new Hosp_atencion_fomulario008();
        $atencio_motivo->id_emer                = $request->id_paciente;
        $atencio_motivo->hora                   = $request->hora;
        $atencio_motivo->causa                  = $request->causa;
        $atencio_motivo->grupo_sanguineo        = $request->sanguineo_factor;
        $atencio_motivo->notificacion_policia   = $request->notificacion_policial;
        $atencio_motivo->save();

        return back()->with('message_atencio', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_enferm_actual_revsion(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $enferm_actu_revi = new Hosp_revision_formulario008();
        $enferm_actu_revi->id_emer              = $request->id_paciente;
        $enferm_actu_revi->via_area             = $request->via_area;
        $enferm_actu_revi->condicion_sistemas   = $request->condicion_sistemas;
        $enferm_actu_revi->save();

        return back()->with('message_enfer_actu_revi', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_accd_viol_intx(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $viol_intox = new Hosp_accidente_formulario008();
        $viol_intox->id_emer            = $request->id_paciente;
        //$viol_intox->fecha_hora         = $request->fecha_hora;
        $viol_intox->lugar_evento       = $request->lugar_evento;
        $viol_intox->direccion_evento   = $request->direccion_evento;
        $viol_intox->custodia_policial  = $request->custodia_policial;
        $viol_intox->observacion        = $request->observacion;
        $viol_intox->aliento_etilico    = $request->aliento_etilico;
        $viol_intox->valor_alcocheck    = $request->valor_alcocheck;

        $viol_intox->save();

        return back()->with('message_viol_intox', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_ante_pers_familiar(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $antecedentes = new Hosp_antecedentes_formulario008();
        $antecedentes->id_emer  = $request->id_paciente;
        $antecedentes->clinico  = $request->clinico;

        $antecedentes->save();
        return back()->with('message_antecedentes', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_signos_vitales(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $signos_vitales = new Hosp_signos_vitales_formulario008();
        dd($signos_vitales);
        $signos_vitales->id_emer            = $request->id_paciente;
        $signos_vitales->presion_arterial   = $request->presion_arterial;
        $signos_vitales->cardiaca           = $request->cardiaca;
        $signos_vitales->respiratoria       = $request->respiratoria;
        $signos_vitales->temp_bucal         = $request->temp_bucal;
        $signos_vitales->temp_axilar        = $request->temp_axilar;
        $signos_vitales->peso_kg            = $request->peso_kg;
        $signos_vitales->talla              = $request->talla;
        $signos_vitales->ocultar            = $request->ocultar;
        $signos_vitales->verbal             = $request->verbal;
        $signos_vitales->motora             = $request->motora;
        $signos_vitales->total              = $request->total;
        $signos_vitales->reaccion_pupilar_d = $request->reaccion_pupilar_d;
        $signos_vitales->reaccion_pupilar_i = $request->reaccion_pupilar_i;
        $signos_vitales->llenado_capilar    = $request->llenado_capilar;
        $signos_vitales->satura_oxigeno     = $request->satura_oxigeno;

        $signos_vitales->save();

        return back()->with('message_signos_vitales', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_emer_obstetrica(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $obstetrica = new Hosp_obstetrica_formulario008();

        $obstetrica->id_emer            = $request->id_paciente;
        $obstetrica->gestas             = $request->gestas;
        $obstetrica->partos             = $request->partos;
        $obstetrica->abortos            = $request->abortos;
        $obstetrica->cesareas           = $request->cesareas;
        $obstetrica->fec_mestruacion    = $request->fec_mestruacion;
        $obstetrica->fec_parto          = $request->fec_parto;
        $obstetrica->nivel_riesgo       = $request->nivel_riesgo;
        $obstetrica->semana_gestacion   = $request->semana_gestacion;
        $obstetrica->movimiento_fetal   = $request->movimiento_fetal;
        $obstetrica->frec_fetal         = $request->frec_fetal;
        $obstetrica->membranas_rotas    = $request->membranas_rotas;
        $obstetrica->tiempo_ruptura     = $request->tiempo_ruptura;
        $obstetrica->altura_uterina     = $request->altura_uterina;
        $obstetrica->presentacion       = $request->presentacion;
        $obstetrica->dilatacion         = $request->dilatacion;
        $obstetrica->borramiento        = $request->borramiento;
        $obstetrica->plano              = $request->plano;
        $obstetrica->pelvis_util        = $request->pelvis_util;
        $obstetrica->sangramiento       = $request->sangramiento;
        $obstetrica->contracciones      = $request->contracciones;
        $obstetrica->save();

        return back()->with('message_obstetrica', 'Se ha guardado correctamente la informacion !');
    }

    public function modal_tratamiento(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/emergencia/modalTratamiento');
    }

    public function guardar_tratamiento(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $tratamiento = new Hosp_tratamiento_formulario008();

        $tratamiento->id_emer                   = $request->id_paciente;
        $tratamiento->nombre                    = $request->nombre;
        $tratamiento->presentacion              = $request->presentacion;
        $tratamiento->cantidad                  = $request->cantidad;
        $tratamiento->concentracion             = $request->concentracion;
        $tratamiento->dosis                     = $request->dosis;
        $tratamiento->unidad                    = $request->unidad;
        $tratamiento->via                       = $request->via;
        $tratamiento->frecuencia                = $request->frecuencia;
        $tratamiento->duracion                  = $request->duracion;
        $tratamiento->indicaciones_medicinas    = $request->indicaciones_medicinas;
        $tratamiento->save();

        return back()->with('message_tratamiento', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_formulario008_alta(Request $request)
    {
        $this->validate($request, ['id_solicitud' => 'required|integer']);
        $solicitud = Ho_Solicitud::findOrFail($request->id_solicitud);
        app(\Sis_medico\Services\ClinicalHospitalTransferService::class)
            ->authorize($solicitud, 'encounter.close', ['SISTEMAS', 'DOCTORES', 'DOCTOR'], false);
        return response()->json(['estado' => 'continuar', 'msj' => 'Complete y firme el alta del episodio.',
            'url' => route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud->id])]);
    }

    public function historial_atencion(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_atencion_fomulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'ingreso_emer_008.id_paciente', 'paciente.created_at')
            ->get();

        //dd($nombre);
        return view('hospital/emergencia/historial_atencion008', ['id_paciente' => $id_paciente, 'dato_paciente' => $dato_paciente, 'nombre' => $nombre]);
    }

    public function historial_revision(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_revision_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_revision008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_accidente(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_accidente_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_accidente008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_antecendentes(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_antecedentes_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_antecendentes008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_signos_vitales(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_signos_vitales_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_signos_vitales008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_obstetrica(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_obstetrica_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_obstetrica008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_tratamiento(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_tratamiento_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_tratamiento008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_alta(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_formulario008_alta::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_alta008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }
    public function formulario008_pdf($id, Request $request)
    {
        $solicitudPdf = Ho_Solicitud::findOrFail($id);
        if (in_array((int) $solicitudPdf->estado_paso, [1, 2, 3, 5, 6, 7], true)) {
            app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->authorize(
                $solicitudPdf, 'hospital.view', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA', 'ANESTESIÓLOGO', 'ANESTESIOLOGO'], false);
        }
        abort_unless(
            app(\Sis_medico\Services\ClinicalHospitalFormAvailabilityService::class)
                ->isAvailable($id, \Sis_medico\Services\ClinicalHospitalFormAvailabilityService::FORM_008),
            404,
            'El Formulario 008 todavía no tiene información clínica registrada.'
        );

        $log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud', $id)->where('estado_paso', '2')->whereNotNull('id_agenda')->first();
        abort_unless($log_solicitud && $log_solicitud->agenda && $log_solicitud->agenda->historia_clinica,
            422, 'Complete primero el ingreso administrativo.');
        //dd($log_solicitud);
        $solicitudemer = Ho_Solicitud::find($id);
        $empresa = Empresa::find($request->session()->get('id_empresa'));
        if (is_null($empresa) && !is_null($solicitudemer) && !is_null($solicitudemer->agenda)) {
            $empresa = Empresa::find($solicitudemer->agenda->id_empresa);
        }
        //dd($solicitudemer);
        $historia = $log_solicitud->agenda->historia_clinica;
        abort_unless(((string) $historia->encounter_id === (string) $solicitudemer->encounter_id
                || (!$historia->encounter_id && (string) $historia->id_agenda === (string) $log_solicitud->id_agenda))
            && (string) $historia->id_paciente === (string) $solicitudemer->id_paciente, 404);
        $verificar = Ho_Lesiones008::where('id_008', $id)->first();
        $verificacion = Ho_Traspaso_Sala008::where('id_solicitud', $solicitudemer->id)->first();
        $form008    = $solicitudemer->form008->first();
        abort_unless($form008 && $form008->agenda && $form008->agenda->historia_clinica, 404);
        $hc            = $form008->agenda->historia_clinica;
        abort_unless(((string) $hc->encounter_id === (string) $solicitudemer->encounter_id
                || (!$hc->encounter_id && (string) $hc->id_agenda === (string) $log_solicitud->id_agenda))
            && (string) $hc->id_paciente === (string) $solicitudemer->id_paciente, 404);
        $alergias = $solicitudemer->paciente->a_alergias;
        $txt_al = '';
        $cont = 0;
        $pasos = Ho_Solicitud::join('historiaclinica  as hc', 'hc.id_agenda', 'ho_solicitud.id_agenda')
            ->join('hc_cie10 as hi', 'hi.hcid', 'hc.hcid')->where('ho_solicitud.id', $id)->select('hi.*')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();
        $receta = hc_receta::where('id_hc', $historia->hcid)->first();

        // return view('hospital.emergencia.formulario008_pdf', ['pasos'=>$pasos,'solicitudemer' => $solicitudemer, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $receta->detalles, 'receta' => $receta, 'empresa' => $empresa]);

        $emergencySource = app(HospitalEpisodeEmergencySource::class)->forForm($solicitudemer->id, '008');
        $view = \View::make('hospital.emergencia.formulario008_pdf', ['pasos' => $pasos, 'solicitudemer' => $solicitudemer, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $receta ? $receta->detalles : collect(), 'receta' => $receta, 'empresa' => $empresa, 'emergencySource' => $emergencySource])->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['chroot'  => base_path('/')]);
        return $pdf->stream('formulario008_pdf.pdf');
    }
    public function pdf_formulario008_nuevo($id, Request $request) //
    {
        $solicitudPdf = Ho_Solicitud::findOrFail($id);
        $isHospital = in_array((int) $solicitudPdf->estado_paso, [1, 2, 3, 5, 6, 7], true);
        if ($isHospital) {
            app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->authorize(
                $solicitudPdf, 'hospital.view', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA', 'ANESTESIÓLOGO', 'ANESTESIOLOGO'], false);
        }
        abort_unless(
            app(\Sis_medico\Services\ClinicalHospitalFormAvailabilityService::class)
                ->isAvailable($id, \Sis_medico\Services\ClinicalHospitalFormAvailabilityService::FORM_008),
            404,
            'El Formulario 008 todavía no tiene información clínica registrada.'
        );

        $log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud', $id)->where('estado_paso', '2')->whereNotNull('id_agenda')->first();
        //SOLO PARA AMBULATORIO 
        if(is_null($log_solicitud)){
            $log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud', $id)->where('estado_paso', '0')->first();    
        }
        //dd($log_solicitud);
        $solicitudemer = Ho_Solicitud::find($id);
        $empresa = Empresa::find($request->session()->get('id_empresa'));
        if (is_null($empresa) && !is_null($solicitudemer) && !is_null($solicitudemer->agenda)) {
            $empresa = Empresa::find($solicitudemer->agenda->id_empresa);
        }
        //dd($solicitudemer);
        $historia = $log_solicitud->agenda->historia_clinica;
        $verificar = Ho_Lesiones008::where('id_008', $id)->first();
        $verificacion = Ho_Traspaso_Sala008::where('id_solicitud', $solicitudemer->id)->first();

        $form008    = $solicitudemer->form008->first();
        if (!$isHospital) $this->precarga_formulario($form008);
        $hc       = $solicitudemer->agenda->historia_clinica;
        $hc_cie10_ingreso = Hc_Cie10::where('hc_cie10.hc_id_procedimiento', $hc->hc_procedimientos->id)->get();
        $alergias = $solicitudemer->paciente->a_alergias;
        $txt_al = '';
        $cont = 0;
        $paciente = $solicitudemer->paciente;
        $glasgow = $form008->glasgow;
        $signos_vitales = $form008->consantes_vitales;
        $datos_pac = $solicitudemer->paciente->ho_datos_paciente;
        $examen_fisico = ($form008->examen_fisico);
        $user_crea = User::where('id', $solicitudemer->id_usuariocrea)->first();
        $usuario = User::where('id', $form008->id_usuariocrea)->first();
        $pasos = Ho_Solicitud::join('historiaclinica  as hc', 'hc.id_agenda', 'ho_solicitud.id_agenda')
            ->join('hc_cie10 as hi', 'hi.hcid', 'hc.hcid')->where('ho_solicitud.id', $id)->select('hi.*')->get();

        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();
        $receta = hc_receta::where('id_hc', $historia->hcid)->first();
        $detalles = [];
        if(!is_null($receta)){
            $detalles = $receta->detalles;
        }
        $formSignature = $this->form008Signature($form008, $usuario);
        //dd($receta);

        $view = \View::make('hospital/emergencia/formulario008_nuevo', ['hc_cie10_ingreso' => $hc_cie10_ingreso, 'datos_pac' => $datos_pac, 'paciente' => $paciente, 'examen_fisico' => $examen_fisico, 'signos_vitales' => $signos_vitales, 'glasgow' => $glasgow, 'pasos' => $pasos, 'user_crea' => $user_crea, 'solicitudemer' => $solicitudemer, 'usuario' => $usuario, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $detalles, 'receta' => $receta, 'empresa' => $empresa, 'formSignature' => $formSignature])->render();
        $pdf  = \App::make('dompdf.wrapper');


        $pdf->loadHTML($view);
        return $pdf->stream('formulario008_nuevo.pdf');
    }

    private function form008Signature(Ho_Form008 $form008, $author = null)
    {
        $authorId = (string) $form008->id_usuariocrea;
        $author = $author ?: User::withoutGlobalScopes()->find($authorId);
        $name = $author ? trim(implode(' ', array_filter([
            $author->nombre1, $author->nombre2, $author->apellido1, $author->apellido2,
        ], function ($part) {
            $value = strtoupper(trim((string) $part));
            return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
        }))) : '';
        $specialty = $author
            ? DB::table('user_espe as ue')->join('especialidad as e', 'e.id', '=', 'ue.espid')
                ->where('ue.usuid', $author->id)->orderBy('ue.id')->pluck('e.nombre')->unique()->implode(', ')
            : '';
        $stored = Firma_Usuario::where('id_usuario', $authorId)->orderBy('id')->first();
        $images = app(DoctorSignatureService::class);
        $proof = app(ClinicalRecordSignatureService::class)->issueForm008($form008);

        return [
            'name' => $name !== '' ? $name : 'PROFESIONAL NO IDENTIFICADO',
            'specialty' => $specialty !== '' ? $specialty : 'Especialidad no registrada',
            'register' => $author ? trim((string) $author->registro_doctor) : '',
            'status' => 'VALIDADO POR AITROL',
            'fingerprint' => $proof['fingerprint'],
            'validation_url' => $proof['validation_url'],
            'qr_source' => app(ClinicalElectronicSignatureQr::class)->dataUri($proof['validation_url']),
            'signature_image' => $stored ? $images->dataUriForFilename($stored->firma) : null,
            'seal_image' => $stored ? $images->dataUriForFilename($stored->nombre) : null,
        ];
    }

    public function precarga_formulario($form008){

        $agenda = $form008->agenda;
        if(!is_null($agenda)){
            $hc = Historiaclinica::where('id_agenda',$agenda->id)->first();//dd($hc);
            if(!is_null($hc)){
                $evo = $hc->evoluciones->where('secuencia','0')->first();
                if(!is_null($evo)){
                    //dd($evo,$form008);
                  
                    $form008->update([
                        'motivo'                => $evo->motivo,
                        'observacion_quintop'   => $evo->cuadro_clinico,
                        'frec_respiratoria'     => $hc->frecuencia_respiratoria,
                        'pulsioximetria'        => $hc->o2,
                        'perimetro_cefalico'    => $hc->perimetro,
                        'glicemia_capilar'      => $hc->glicemia_capilar,
                        'glasgow_inicial'       => $hc->glasgow,
                        'ocular'                => $hc->ocular,
                        'verbal'                => $hc->verbal,
                        'motora'                => $hc->motora,
                        'reac_pupila_der'       => $hc->reaccion_pupila_izq,
                        'reac_pupila_izq'       => $hc->reaccion_pupila_izq,
                        't_llenado_capilar'     => $hc->t_llenado_capilar,        
                    ]);
                  

                }

            }
        }
        

        

    }
}
