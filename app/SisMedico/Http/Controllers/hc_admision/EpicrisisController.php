<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Archivo_historico;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\ClinicalDocument;
use Sis_medico\Documento;
use Sis_medico\Empresa;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Hc_Epicrisis_Final_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Exceptions\ClinicalClosureBlockedException;
use Sis_medico\Services\ClinicalEncounterClosureService;
use Sis_medico\Services\ClinicalElectronicSignatureQr;
use Sis_medico\Services\ClinicalRecordSignatureService;
use Sis_medico\Services\DoctorSignatureService;
use Sis_medico\ClinicalEncounterClosure;
use Sis_medico\Support\RequestSchema as Schema;

class EpicrisisController extends Controller
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
        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyUserTypeName(array(
            'SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA',
            'ANESTESIÓLOGO', 'ANESTESIOLOGO', 'RECEPCIÓN', 'RECEPCION',
            'CONTABILIDAD', 'FARMACIA', 'PACIENTE'
        ))) {
            return true;
        }
    }

    public function mostrar($hcid, $proc)
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

        $hc_procedimiento = Hc_procedimientos::find($proc);
        //dd($hc_procedimiento);
        $historia = Historiaclinica::find($hcid);
        //dd($historia);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->leftJoin('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);

        $seguro = Seguro::find($historia->id_seguro);

        $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $proc)->get();

        $evolucion   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();//dd($evolucion);
        $evolucion_1 = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();

        $procedimientos_completo = procedimiento_completo::where('estado', '1')->get();

        $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();

        $protocolo = hc_protocolo::where('id_hc_procedimientos', $proc)->first();

        $favorable_des = null;
        if (!is_null($evolucion_1)) {

            $favorable_des = $evolucion_1->cuadro_clinico;
        } else {
            if (is_null($epicrisis)) {
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
                    'cuadro_clinico'      => $epicrisis->favorable_des,
                    'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            }
            $evolucion_1 = null;
            $favorable_des = ' ';

            if($agenda->proc_consul != 0){
                $id_evolucion  = Hc_Evolucion::insertGetId($input);//NO LO DEBE HACER SI SE TRATA DE UNA CONSULTA 
                $evolucion_1   = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '1')->first();
            }
            
            if(!is_null($evolucion_1)){
                $favorable_des = $evolucion_1->cuadro_clinico;
            }            
            
        }

        $c_clinico = null;
        if (!is_null($evolucion)) {
            $c_clinico = $evolucion->cuadro_clinico;//dd($c_clinico);
        } else {
            if (is_null($epicrisis)) {
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
                ];
            }

            $id_evolucion = Hc_Evolucion::insertGetId($input);
            $evolucion    = Hc_Evolucion::where('hc_id_procedimiento', $proc)->where('secuencia', '0')->first();
            $c_clinico    = $evolucion->cuadro_clinico;
        }

        $complicacion = '';
        if(!is_null($protocolo)){
            $complicacion = $protocolo->complicaciones;
        }
         
        //dd($c_clinico,$epicrisis);

        if (is_null($epicrisis)) {

            //$protocolo = hc_protocolo::where('id_hc_procedimientos',$proc)->first();
            //dd($protocolo);
            //CREAR EPICRISIS
            $input1 = [
                'cuadro_clinico'      => $c_clinico,
                'hc_id_procedimiento' => $proc,
                'hcid'                => $hcid,
                'favorable_des'       => $favorable_des,
                'complicacion'        => $complicacion,
                //'hallazgo' => $protocolo->hallazgos,

                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_epi = Hc_Epicrisis::insertGetId($input1);

            $epicrisis = Hc_Epicrisis::find($id_epi);

        } else {

            $input1a = [
                'cuadro_clinico'  => $c_clinico,
                //'hallazgo' => $protocolo->hallazgos,
                'favorable_des'   => $favorable_des,
                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,
            ];
            //$epicrisis->update($input1a);

        }


        return view('hc_admision/epicrisis/epicrisis', ['agenda' => $agenda, 'paciente' => $paciente, 'hca' => $historia, 'seguro' => $seguro, 'epicrisis' => $epicrisis, 'hc_cie10' => $hc_cie10, 'procedimientos_completo' => $procedimientos_completo, 'hc_procedimiento' => $hc_procedimiento, 'id' => $hcid, 'protocolo' => $protocolo]);
    }

    public function mostrar_final($id_solicitud)
    {
        //dd($hcid, $proc);
        $solicitud = Ho_Solicitud::findOrFail($id_solicitud);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $closureService = app(ClinicalEncounterClosureService::class);
        if ($closureService->isHospital($solicitud)) {
            try {
                $closureService->assertHospitalActor(
                    app(\Sis_medico\Services\ClinicalEncounterService::class)->findBySource('ho_solicitud', $solicitud->id),
                    array('SISTEMAS', 'DOCTORES', 'DOCTOR'));
            } catch (ClinicalClosureBlockedException $exception) {
                abort(403, implode(' ', $exception->blockers()));
            }
        }

        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $solicitud->id_agenda)
            ->first();

        $paciente = Paciente::find($solicitud->id_paciente);
        if (is_null($agenda)) {
            $agenda = (object) array(
                'id_paciente' => $solicitud->id_paciente,
                'papellido1' => $paciente ? $paciente->apellido1 : '',
                'papellido2' => $paciente ? $paciente->apellido2 : '',
                'pnombre1' => $paciente ? $paciente->nombre1 : '',
                'pnombre2' => $paciente ? $paciente->nombre2 : '',
            );
        }
        $epicrisis_final = Hc_Epicrisis_Final::where('id_ho_solicitud', $id_solicitud)->first();
        if (is_null($epicrisis_final)) {
            return view('hc_admision/epicrisis_final/inicializar', compact('solicitud', 'paciente'));
        }
        $closurePreview = app(ClinicalEncounterClosureService::class)->preview($id_solicitud, $idusuario);
        $documentCompleteness = $epicrisis_final->encounter_id
            ? app(\Sis_medico\Services\ClinicalDocumentCompletenessService::class)->summary($epicrisis_final->encounter_id)
            : array();
        $closureDoctors = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->get();
        return view('hc_admision/epicrisis_final/epicrisis', ['agenda' => $agenda, 'paciente' => $paciente,
            'solicitud' => $solicitud, 'epicrisis_final' => $epicrisis_final, 'closurePreview' => $closurePreview,
            'closureDoctors' => $closureDoctors, 'documentCompleteness' => $documentCompleteness]);
    }

    public function inicializar_final($id_solicitud, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        try {
        return app(ClinicalEncounterClosureService::class)->editEpicrisis($id_solicitud, function () use ($id_solicitud, $request) {
        $idusuario = Auth::user()->id;
        $solicitud = Ho_Solicitud::findOrFail($id_solicitud);
        $historia = $solicitud->agenda ? $solicitud->agenda->historia_clinica : null;
        if (is_null($historia)) {
            $historia = Historiaclinica::where('id_paciente', $solicitud->id_paciente)
                ->orderBy('created_at', 'desc')->first();
        }
        $procedimiento = $historia ? hc_procedimientos::where('id_hc', $historia->hcid)
            ->orderBy('id', 'desc')->first() : null;
        $evolucion = $procedimiento ? Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)
            ->orderBy('created_at', 'desc')->first() : null;
        $protocolo = $procedimiento ? hc_protocolo::where('id_hc_procedimientos', $procedimiento->id)
            ->orderBy('created_at', 'desc')->first() : null;

        Hc_Epicrisis_Final::firstOrCreate(
            array('id_ho_solicitud' => $id_solicitud),
            array(
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $request->ip(),
                'ip_modificacion' => $request->ip(),
                'cuadro_clinico' => $evolucion ? $evolucion->cuadro_clinico : null,
                'conclusion' => $protocolo ? $protocolo->conclusion : null,
                'resumen' => null,
                'ep_resumen_evolucion' => null,
                'complicacion' => null,
                'condicion' => null,
                'pronostico' => null,
            )
        );

        return redirect()->route('epicrisis.mostrar_final', array_filter(array(
            'id_solicitud' => $id_solicitud,
            'embedded' => $request->boolean('embedded') ? 1 : null,
        )));
        }, array('actor_id' => Auth::user()->id, 'ip' => $request->ip()));
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array('estado' => 'blocked', 'bloqueos' => $exception->blockers()), 423);
        }
    }

    public function pdf_final($id_solicitud)
    {
        $solicitud = Ho_Solicitud::findOrFail($id_solicitud);
        $epicrisis = Hc_Epicrisis_Final::where('id_ho_solicitud', $id_solicitud)->firstOrFail();
        $paciente = Paciente::findOrFail($solicitud->id_paciente);
        $agenda = $solicitud->agenda;
        $historia = $agenda ? Historiaclinica::where('id_agenda', $agenda->id)
            ->where('encounter_id', $solicitud->encounter_id)
            ->where('id_paciente', $paciente->id)->first() : null;
        if (!$historia) {
            $historia = Historiaclinica::where('encounter_id', $solicitud->encounter_id)
                ->where('id_paciente', $paciente->id)->orderBy('created_at', 'desc')->first();
        }
        $procedimiento = $historia && $solicitud->id_hcproc
            ? hc_procedimientos::where('id', $solicitud->id_hcproc)->where('id_hc', $historia->hcid)->first()
            : null;
        if (!$procedimiento && $historia) {
            $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->orderBy('id', 'desc')->first();
        }
        $evolucion = $procedimiento ? Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)
            ->orderBy('created_at', 'desc')->first() : null;
        $protocolo = $procedimiento ? hc_protocolo::where('id_hc_procedimientos', $procedimiento->id)
            ->orderBy('created_at', 'desc')->first() : null;
        if (!$protocolo) {
            // Permite generar el F-006 aun cuando el protocolo quirurgico no exista.
            $protocolo = (object) array(
                'hallazgos' => '',
                'conclusion' => $epicrisis->conclusion ?: '',
                'created_at' => $epicrisis->updated_at ?: $epicrisis->created_at,
                'id_hc_procedimientos' => $procedimiento ? $procedimiento->id : null,
            );
        }
        $diagnosticos = Hc_Epicrisis_Final_Cie10::where('id_hc_epicrisis_final', $epicrisis->id)->get();
        $doctor = $procedimiento && $procedimiento->id_doctor_responsable
            ? User::find($procedimiento->id_doctor_responsable)
            : ($procedimiento && $procedimiento->id_doctor_examinador2 ? User::find($procedimiento->id_doctor_examinador2) : null);
        if (!$doctor && $epicrisis->id_usuariomod) {
            $doctor = User::find($epicrisis->id_usuariomod);
        }
        $empresa = $agenda ? Empresa::find($agenda->id_empresa) : null;
        $especialidad = $agenda && $agenda->espid ? \Sis_medico\Especialidad::find($agenda->espid) : null;
        // Alias compatible para plantillas F-006 antiguas: conserva el origen hospitalario.
        $data = (object) array_merge($paciente->toArray(), array(
            'id_paciente' => $paciente->id,
            'id_agenda' => $agenda ? $agenda->id : ($historia ? $historia->id_agenda : null),
            'hcid' => $historia ? $historia->hcid : null,
        ));
        // La Epicrisis hospitalaria no tiene documento ambulatorio original.
        $epicrisis_original = null;
        // Compatibilidad con las variables del formato F-006 anterior.
        $cie10 = collect();
        $cie10e = $diagnosticos;
        $cie10ca = collect();
        $firma = $doctor ? Firma_Usuario::where('id_usuario', $doctor->id)->first() : null;
        $epicrisisSignature = $this->epicrisisSignature($epicrisis, $doctor, $especialidad);

        $view = \View::make('pdf_hospital.formulario006_hospital', compact(
            'solicitud', 'epicrisis', 'paciente', 'agenda', 'historia', 'procedimiento',
            'diagnosticos', 'doctor', 'empresa', 'especialidad', 'evolucion', 'protocolo', 'data', 'epicrisis_original',
            'cie10', 'cie10e', 'cie10ca', 'firma', 'epicrisisSignature'
        ))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(array('chroot' => base_path('/')));
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('epicrisis-hospital-' . $solicitud->id . '.pdf');
    }

    private function epicrisisSignature(Hc_Epicrisis_Final $epicrisis, $fallbackDoctor = null, $fallbackSpecialty = null)
    {
        $document = null;
        if (Schema::hasTable('clinical_documents')
            && Schema::hasColumn('clinical_documents', 'signed_at')
            && Schema::hasColumn('clinical_documents', 'signed_by')
            && Schema::hasColumn('clinical_documents', 'content_hash')) {
            $document = ClinicalDocument::where('source_type', 'hc_epicrisis_final')
                ->where('source_id', (string) $epicrisis->id)
                ->whereNotNull('signed_at')->whereNotNull('signed_by')->first();
        }

        $professional = $document
            ? User::withoutGlobalScopes()->find($document->signed_by)
            : $fallbackDoctor;
        $professionalId = $professional ? (string) $professional->id : '';
        $stored = $professionalId !== ''
            ? Firma_Usuario::where('id_usuario', $professionalId)->orderBy('id')->first()
            : null;
        $images = app(DoctorSignatureService::class);
        $proof = $document
            ? app(ClinicalRecordSignatureService::class)->issueForm006($epicrisis, $document)
            : null;

        $name = $professional ? trim(implode(' ', array_filter(array(
            $professional->nombre1, $professional->nombre2, $professional->apellido1, $professional->apellido2,
        ), function ($part) {
            $value = strtoupper(trim((string) $part));
            return $value !== '' && $value !== 'N/A' && $value !== '(N/A)';
        }))) : '';
        $specialty = $professional
            ? DB::table('user_espe as ue')->join('especialidad as e', 'e.id', '=', 'ue.espid')
                ->where('ue.usuid', $professional->id)->orderBy('ue.id')->pluck('e.nombre')->unique()->implode(', ')
            : '';
        if ($specialty === '' && $fallbackSpecialty) $specialty = trim((string) $fallbackSpecialty->nombre);
        $signedAt = $document && $document->signed_at ? $document->signed_at : $epicrisis->updated_at;
        if ($signedAt instanceof \DateTimeInterface) $signedAt = $signedAt->format('Y-m-d H:i:s');

        return array(
            'electronic' => !is_null($proof),
            'professional' => $professional,
            'name' => $name !== '' ? $name : 'PROFESIONAL NO IDENTIFICADO',
            'specialty' => $specialty !== '' ? $specialty : 'Especialidad no registrada',
            'register' => $professional ? trim((string) $professional->registro_doctor) : '',
            'signed_at' => trim((string) $signedAt),
            'status' => 'VALIDADO POR AITROL',
            'fingerprint' => $proof ? $proof['fingerprint'] : null,
            'validation_url' => $proof ? $proof['validation_url'] : null,
            'qr_source' => $proof ? app(ClinicalElectronicSignatureQr::class)->dataUri($proof['validation_url']) : null,
            'signature_image' => !$proof && $stored ? $images->dataUriForFilename($stored->firma) : null,
            'seal_image' => $stored ? $images->dataUriForFilename($stored->nombre) : null,
        );
    }

    public function cerrar_final(Request $request)
    {
        if ($this->rol()) return response()->json(array('estado' => 'error', 'msj' => 'No autorizado.'), 403);
        try {
            $closure = app(ClinicalEncounterClosureService::class)->close(
                $request->input('id_solicitud'),
                $request->only(array('closure_type', 'medication_reconciliation', 'pending_orders_decision',
                    'pending_orders_reason', 'follow_up_responsible_id', 'death_occurred_at',
                    'death_pronounced_by', 'death_pronounced_at', 'death_location', 'death_probable_cause',
                    'death_manner', 'death_certificate_number', 'death_identity_verified',
                    'death_body_identified', 'death_family_contact', 'death_family_notified_at',
                    'death_morgue_notified_at', 'death_notes')),
                array('actor_id' => Auth::user()->id, 'ip' => $request->ip())
            );
            return response()->json(array(
                'estado' => 'success',
                'msj' => $closure->status === ClinicalEncounterClosureService::SIGNED
                    ? 'Alta medica firmada. Pendiente confirmar salida fisica; la cama sigue ocupada.'
                    : 'Episodio cerrado correctamente con checklist y firma.',
                'closure_id' => $closure->id,
                'closure_status' => $closure->status,
            ));
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array(
                'estado' => 'blocked',
                'codigo' => 'F-02',
                'msj' => $exception->getMessage(),
                'bloqueos' => $exception->blockers(),
            ), 422);
        } catch (\Exception $exception) {
            return response()->json(array('estado' => 'error', 'msj' => $exception->getMessage()), 500);
        }
    }
    public function confirmar_salida_final(Request $request)
    {
        $request->validate(array('id_solicitud' => 'required|integer|min:1',
            'closure_id' => 'required|integer|min:1', 'confirm_exit' => 'required|accepted'));
        try {
            $closure = app(ClinicalEncounterClosureService::class)->confirmPhysicalExit(
                $request->input('id_solicitud'), $request->input('closure_id'),
                array('actor_id' => Auth::user()->id, 'ip' => $request->ip()));
            return response()->json(array('estado' => 'success', 'closure_id' => $closure->id,
                'closure_status' => $closure->status, 'msj' => 'Salida fisica confirmada.'));
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array('estado' => 'blocked', 'msj' => $exception->getMessage(),
                'bloqueos' => $exception->blockers()), 422);
        }
    }

    public function diagnostico($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $usuarios       = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;
        $enfermeros     = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado', '1')->get(); //9=ANESTESIOLOGO;
        $salas          = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $hc_procedimiento = Hc_procedimientos::find($id);
        $historia         = Historiaclinica::find($hc_procedimiento->id_hc);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);

        $seguro = Seguro::find($historia->id_seguro);

        $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $id)->first();

        $hc_cie10 = Hc_Cie10::where('hc_id_procedimiento', $id)->get();

        if (!is_null($hc_cie10)) {
            $c10_arr = [];
            foreach ($hc_cie10 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr[$c10->id] = $c3->descripcion;
                }
                if ($c4 != null) {
                    $c10_arr[$c10->id] = $c4->descripcion;
                }

            }
        }

        $procedimientos_completo = procedimiento_completo::all();

        return view('hc_admision/epicrisis/diagnostico', ['agenda' => $agenda, 'paciente' => $paciente, 'hca' => $historia, 'seguro' => $seguro, 'epicrisis' => $epicrisis, 'hc_cie10' => $hc_cie10, 'procedimientos_completo' => $procedimientos_completo, 'hc_procedimiento' => $hc_procedimiento, 'id' => $id, 'c10_arr' => $c10_arr]);
    }

    public function actualiza(Request $request)
    {
        //return "hola";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        //dd($request->all());

        $epicrisis = Hc_Epicrisis::find($request->epicrisis);
        $protocolo = hc_protocolo::find($request->protocolo_id);

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

            DB::beginTransaction();
            try {
                $epicrisis->update($input1a);
                if (!is_null($protocolo)) {
                    $protocolo->update($input1b);
               

                    $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('secuencia', '0')->first();
                    if (!is_null($evolucion)) {
                    $input1c = [

                        'cuadro_clinico'  => $request['cuadro'],

                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                        $evolucion->update($input1c);
                    }

                    $evolucion_1 = Hc_Evolucion::where('hc_id_procedimiento', $protocolo->id_hc_procedimientos)->where('secuencia', '1')->first();
                    if (!is_null($evolucion_1)) {
                    $input1d = [

                        'cuadro_clinico'  => $request['favorable_des'],

                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                        $evolucion_1->update($input1d);
                    }
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                report($exception);
                return response('No se pudo actualizar la epicrisis.', 500);
            }
        }

        return "ok";

    }

    public function show($id)
    {
        //
    }

    public function imprimirpdf($ahid)
    {
        $archivo_historico = Archivo_historico::find($ahid);
        $documento         = Documento::find($archivo_historico->id_documento);
        $historia          = $this->carga_hc($archivo_historico->id_historia);
        $agenda            = Agenda::find($historia->id_agenda);
        $seguro            = Seguro::find($historia->id_seguro);
        $empresa           = Empresa::where('id', $agenda->id_empresa)->first();
        $paciente          = Paciente::find($historia->id_paciente);
        $empresaxdoc       = Empresa::find($documento->id_empresa);
        $doctor            = User::find($historia->id_doctor1);

        $age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $paper_size = array(0, 0, 595, 920);
        $data       = $historia;
        $date       = $historia->created_at;

        //return view('hc_admision/formato/'.$documento->formato);
        $view = \View::make('hc_admision.formato.' . $documento->formato, compact('data', 'date', 'empresa', 'age', 'empresaxdoc', 'paciente', 'agenda', 'doctor'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        if ($documento->formato == 'contrareferencia') {

            $pdf->setOptions(['dpi' => 96]);
            $paper_size = array(0, 0, 1100, 1650);
            $pdf->setpaper($paper_size);
            $pdf->loadHTML($view);

        } else {
            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
        }

        //return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
        return $pdf->stream($historia->id_paciente . '_' . $documento->tipo_documento . '_' . $archivo_historico->id_historia . '_' . $archivo_historico->id . '.pdf');
    }

    public function imprimir($id)
    {
        //dd($id);
        $procedimiento = null;
        $firma         = null;

        $epicrisis = Hc_Epicrisis::find($id);
        $protocolo = hc_protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        //return $protocolo;
        //return $protocolo;
        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'agenda.id_empresa')->first();
        $id_empresa = $historiaclinica->id_empresa; 
        $cie10_in_pre = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();

        $cie10_in_def = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();

        $cie10_eg_pre = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();

        $cie10_eg_def = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();

        $cie10_3 = Cie_10_3::all();
        $cie10_4 = Cie_10_4::all();
        if (!is_null($protocolo)) {
            $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
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
        }
        //dd($procedimiento);
        $tiene_receta = 'NO';
        $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
        if (!is_null($receta)) {
            $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
            if ($receta_det > 0 || $receta->prescripcion != null) {
                $tiene_receta = 'SI';
            }
        }
        $tiene_receta = $epicrisis->receta;

        //dd($firma);
        $nombre_doc = "";
        if (!is_null($firma)) {
            $nombre_doc = User::find($firma->id_usuario);
        }

        $doctor = null;
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
        // dd($doctor);

        $data = $historiaclinica;
        $receta = hc_receta::where('id_hc',$data->hcid)->first();
        //dd($receta);
        $view = \View::make('hc_admision.formato.epicrisis', compact('receta','data', 'evolucion_1', 'epicrisis', 'cie10_in_pre', 'cie10_in_def', 'cie10_3', 'cie10_4', 'cie10_eg_pre', 'cie10_eg_def', 'tiene_receta', 'protocolo', 'firma', 'nombre_doc', 'doctor', 'id_empresa', 'firma', 'procedimiento'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        return $pdf->stream('HCU_006.pdf');
    }

    public function imprimir_stream($id)
    { 
        $procedimiento = "";
        $doctor = "";
        $firma         = "";
        $epicrisis = Hc_Epicrisis::find($id);
        $protocolo = hc_protocolo::where('hcid', $epicrisis->hcid)->where('id_hc_procedimientos', $epicrisis->hc_id_procedimiento)->first();
        //return $protocolo;
        //return $protocolo;
        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $epicrisis->hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo')->first();

        $cie10_in_pre = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();

        $cie10_in_def = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('hc_cie10.ingreso_egreso', 'INGRESO')->get();

        $cie10_eg_pre = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'PRESUNTIVO')->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();

        $cie10_eg_def = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $epicrisis->hc_id_procedimiento)->where('hc_cie10.presuntivo_definitivo', 'DEFINITIVO')->where('hc_cie10.ingreso_egreso', 'EGRESO')->get();

        $cie10_3 = Cie_10_3::all();
        $cie10_4 = Cie_10_4::all();

        $tiene_receta = 'NO';
        $receta       = hc_receta::where('id_hc', $historiaclinica->hcid)->first();
        if (!is_null($receta)) {
            $receta_det = hc_receta_detalle::where('id_hc_receta', $receta->id)->count();
            if ($receta_det > 0) {
                $tiene_receta = 'SI';
            }

        }

        //dd('hol');
      
        $data = $historiaclinica;
        $receta = hc_receta::where('id_hc',$data->hcid)->first();
        $view = \View::make('hc_admision.formato.epicrisis', compact('data', 'evolucion_1', 'epicrisis', 'cie10_in_pre', 'cie10_in_def', 'cie10_3', 'cie10_4', 'cie10_eg_pre', 'cie10_eg_def', 'tiene_receta', 'protocolo', 'firma', 'receta', 'procedimiento', 'doctor'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        //return $pdf->download('epicrisis-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('epicrisis-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    }

    public function seleccion()
    {

        return response()->json([
            'name'  => 'Abigail',
            'state' => 'CA',
        ]);

    }

    public function cie10_nombre(Request $request)
    {
        $nombre_cie10 = trim((string) $request['term']);
        $data = array();
        if (strlen($nombre_cie10) < 2) {
            return $data;
        }

        $seteo = '%' . $nombre_cie10 . '%';
        $cie10_3 = Cie_10_3::where(function ($query) use ($seteo) {
            $query->where('descripcion', 'like', $seteo)->orWhere('id', 'like', $seteo);
        })->orderBy('id')->limit(100)->get(array('id', 'descripcion'));
        $cie10_4 = Cie_10_4::where(function ($query) use ($seteo) {
            $query->where('descripcion', 'like', $seteo)->orWhere('id', 'like', $seteo);
        })->orderBy('id')->limit(100)->get(array('id', 'descripcion'));

        foreach ($cie10_3 as $value) {
            $data[] = array('value' => '(' . $value->id . ') ' . $value->descripcion, 'id' => $value->id);
        }
        foreach ($cie10_4 as $value) {
            $data[] = array('value' => '(' . $value->id . ') ' . $value->descripcion, 'id' => $value->id);
        }
        return count($data) > 0 ? $data : array(array('value' => 'No se encontraron resultados', 'id' => ''));

    }

    public function cie10_nombre2(Request $request)
    {

        $nombre_cie10 = $request['cie10'];
        //dd($nombre_cie10);
        $data = array();

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
    //aquii proc_agregar_cie10
    public function agregar_cie10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }
        /*if($request['pre_def']==null){
        return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }
        if($request['in_eg']==null){
        return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }*/
        //return $request->all();

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
        $id = Hc_Cie10::insertGetId($input2);

        $count = Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        $cie10 = Hc_Cie10::find($id);

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

    public function cargar($id)
    {

        $c     = [];
        $x     = 0;
        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $id)->get();
        //dd($cie10);
        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {
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

        $cie10 = DB::table('hc_cie10')->where('id', $id)->delete();
        //dd($cie10);
        return "ok";
    }

    public function actualiza_final(Request $request)
    {
        try {
        return app(ClinicalEncounterClosureService::class)->editEpicrisis($request->id_solicitud, function () use ($request) {
        //return "hola";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if (Schema::hasTable('clinical_encounter_closures')
            && ClinicalEncounterClosure::where('solicitud_id', $request->id_solicitud)->where('status', 'COMPLETED')->exists()) {
            return response()->json(array(
                'estado' => 'blocked',
                'msj' => 'La epicrisis está firmada y el episodio está cerrado. Las correcciones deben realizarse mediante una adenda.',
            ), 423);
        }

        $epicrisis_final = Hc_Epicrisis_Final::where('id_ho_solicitud',$request->id_solicitud)->first();

        if (!is_null($epicrisis_final)) {

            $input1a = [
                'id_ho_solicitud'      => $request['id_solicitud'],
                'cuadro_clinico'       => $request['cuadro'],
                'favorable_des'        => $request['favorable_des'],
                'complicacion'         => $request['complicacion'],
                'hallazgo'             => $request['hallazgo'],
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

            $epicrisis_final->update($input1a);
        }

        return "ok";
        }, array('actor_id' => Auth::user()->id, 'ip' => $request->ip()));
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array('estado' => 'blocked', 'bloqueos' => $exception->blockers()), 423);
        }
    }

    public function agregar_cie10_final(Request $request)
    {
        $this->validate($request, [
            'id_epicrisis_final' => 'required|integer',
            'id_ho_solicitud' => 'required|integer',
            'codigo' => 'required',
            'pre_def' => 'required|in:PRESUNTIVO,DEFINITIVO',
            'in_eg' => 'required|in:INGRESO,EGRESO',
        ]);
        $epicrisis = Hc_Epicrisis_Final::findOrFail($request->id_epicrisis_final);
        try {
        return app(ClinicalEncounterClosureService::class)->editEpicrisis($epicrisis->id_ho_solicitud,
            function ($solicitud) use ($request, $epicrisis) {
        if (app(ClinicalEncounterClosureService::class)->isHospital($solicitud)) {
            abort_unless((string) $request->id_ho_solicitud === (string) $solicitud->id, 422,
                'El diagnostico no corresponde a la solicitud.');
        }
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input2 = [
            'id_ho_solicitud'       => $request['id_ho_solicitud'],
            'id_hc_epicrisis_final' => $request['id_epicrisis_final'],
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

        $id = Hc_Epicrisis_Final_Cie10::insertGetId($input2);

        $count = Hc_Epicrisis_Final_Cie10::where('id_hc_epicrisis_final', $request['id_epicrisis_final'])->get()->count();

        $cie10 = Hc_Epicrisis_Final_Cie10::find($id);

        $descripcion = '';
        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return response()->json(['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']]);
        }, array('actor_id' => Auth::user()->id, 'ip' => $request->ip()));
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array('estado' => 'blocked', 'bloqueos' => $exception->blockers()), 423);
        }
    }

    public function eliminar_final($id)
    {
        $cie10 = Hc_Epicrisis_Final_Cie10::findOrFail($id);
        $epicrisis = Hc_Epicrisis_Final::findOrFail($cie10->id_hc_epicrisis_final);
        try {
            return app(ClinicalEncounterClosureService::class)->editEpicrisis($epicrisis->id_ho_solicitud,
                function () use ($id) {
                    $cie10 = Hc_Epicrisis_Final_Cie10::where('id', $id)->lockForUpdate()->firstOrFail();
                    $cie10->delete();
                    return $cie10;
                });
        } catch (ClinicalClosureBlockedException $exception) {
            return response()->json(array('estado' => 'blocked', 'bloqueos' => $exception->blockers()), 423);
        }
    }

    public function cargar_final($id)
    {
        $c     = [];
        $x     = 0;
        $cie10 = Hc_Epicrisis_Final_Cie10::where('id_hc_epicrisis_final', $id)->get();
        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {
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
}
