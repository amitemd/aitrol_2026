<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\HoEstadoPaso;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Hc_Receta_Evolucion;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\User_espe;
use Sis_medico\hc_child_pugh;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\procedimiento_completo;

class ClinicalHospitalModuleService
{
    /**
     * Resolve la especialidad tecnica requerida por la agenda hospitalaria.
     *
     * El usuario Sistemas puede registrar una admision sin tener una
     * especialidad personal. En ese caso se respeta primero la especialidad
     * enviada por el formulario y, si no existe, se utiliza una especialidad
     * activa del catalogo. Nunca se vuelve a depender de un identificador fijo.
     */
    private function resolveAdmissionSpecialty($doctorId, Request $request)
    {
        $linkedSpecialty = User_espe::join('especialidad as e', 'e.id', '=', 'user_espe.espid')
            ->where('user_espe.usuid', $doctorId)
            ->where('e.estado', 1)
            ->orderBy('user_espe.id')
            ->select('e.id')
            ->first();

        if (!is_null($linkedSpecialty)) {
            return $linkedSpecialty->id;
        }

        $requestedSpecialtyId = $request->input('id_especialidad', $request->input('espid'));
        if (Auth::user()->isSystemUser() && !empty($requestedSpecialtyId)) {
            $requestedSpecialty = Especialidad::where('id', $requestedSpecialtyId)
                ->where('estado', 1)
                ->first();

            if (!is_null($requestedSpecialty)) {
                return $requestedSpecialty->id;
            }
        }

        if (Auth::user()->isSystemUser()) {
            $generalSpecialty = Especialidad::where('estado', 1)
                ->where('nombre', 'like', '%MEDICINA GENERAL%')
                ->orderBy('id')
                ->first();

            if (!is_null($generalSpecialty)) {
                return $generalSpecialty->id;
            }

            $activeSpecialty = Especialidad::where('estado', 1)->orderBy('id')->first();
            return is_null($activeSpecialty) ? null : $activeSpecialty->id;
        }

        return null;
    }

    public function buscador_vista(Request $request)
    {
        $from = $request->input('fecha_desde');
        $to = $request->input('fecha_hasta');
        $room = $request->input('sala');
        $names = $request->input('paciente');
        $patientId = $request->input('cedula');
        $stepId = $request->input('id_estado_paso');

        $requests = Ho_Solicitud::leftjoin('ho_estado_paso as paso', 'ho_solicitud.estado_paso', 'paso.id')
            ->where('ho_solicitud.estado_paso', $stepId)
            ->join('agenda as a', 'ho_solicitud.id_agenda', 'a.id')
            ->join('paciente as p', 'a.id_paciente', 'p.id');

        if (!is_null($room)) $requests->where('a.id_sala', $room);
        if (!is_null($from) && !is_null($to)) $requests->whereBetween('ho_solicitud.created_at', array($from, $to));
        if (!is_null($patientId)) $requests->where('ho_solicitud.id_paciente', $patientId);
        if (!is_null($names)) {
            $requests->where(function ($query) use ($names) {
                $query->whereRaw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', array('%'.$names.'%'))
                    ->orWhereRaw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', array('%'.$names.'%'))
                    ->orWhereRaw('CONCAT(p.apellido1," ",p.apellido2) LIKE ?', array('%'.$names.'%'));
            });
        }

        return view('hospital/habitacion/vista_buscador', array(
            'salas' => Sala::where('estado', '1')->get(),
            'solicitudes' => $requests->get(),
            'ho_estado_paso' => $stepId,
        ));
    }

    private function hospitalModuleCatalog()
    {
        return [
            2 => [
                'nombre_paso' => 'EMERGENCIA',
                'descripcion' => 'EMERGENCIA',
                'ruta' => 'hospital.ingreso008',
            ],
            3 => [
                'nombre_paso' => 'HOSPITALIZACION',
                'descripcion' => 'HOSPITALIZACION',
                'ruta' => 'formulario005.index_005',
            ],
            4 => [
                'nombre_paso' => 'IMAGENES',
                'descripcion' => 'IMAGENES',
                'ruta' => 'quirofano.quirofano_paciente',
            ],
            5 => [
                'nombre_paso' => 'CIRUGIA',
                'descripcion' => 'CIRUGIA',
                'ruta' => 'quirofano.quirofano_paciente',
            ],
            6 => [
                'nombre_paso' => 'UCI',
                'descripcion' => 'UCI',
                'ruta' => 'uci.index_uci',
            ],
            7 => [
                'nombre_paso' => 'UCIN',
                'descripcion' => 'UCIN',
                'ruta' => 'uci.index_uci',
            ],
        ];
    }

    /**
     * Obtiene un modulo y restaura su configuracion cuando falta en la base.
     *
     * @param  int|string  $idPaso
     * @return \Sis_medico\HoEstadoPaso|null
     */
    public function resolveHospitalModule($idPaso)
    {
        $idPaso = (int) $idPaso;
        $catalog = $this->hospitalModuleCatalog();

        if (!isset($catalog[$idPaso])) {
            return null;
        }

        $module = HoEstadoPaso::find($idPaso);
        if ($module) {
            return $module;
        }

        return HoEstadoPaso::create(array_merge($catalog[$idPaso], [
            'id' => $idPaso,
            'estado' => 1,
            'ip_creacion' => request()->ip(),
            'ip_modificacion' => request()->ip(),
        ]));
    }

    /**
     * Recupera el registro clinico base usado por los ingresos hospitalarios.
     *
     * La aplicacion historica lo identificaba con el ID 40. En instalaciones
     * nuevas ese dato puede no existir aunque toda la estructura ya haya sido
     * migrada, por lo que se restaura sin obligar al usuario a configurarlo.
     *
     * @param  string  $userId
     * @param  string  $ip
     * @return \Sis_medico\procedimiento_completo
     */
    private function resolveHospitalClinicalProcedure($userId, $ip)
    {
        $procedure = procedimiento_completo::find(40);

        if (!is_null($procedure)) {
            return $procedure;
        }

        return procedimiento_completo::create([
            'id' => 40,
            'nombre_general' => 'EVOLUCION HOSPITALARIA',
            'nombre_completo' => 'EVOLUCION HOSPITALARIA',
            'id_grupo_procedimiento' => null,
            'tipo_documentacion' => 1,
            'estado_anestesia' => 0,
            'estado' => 1,
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    private function hasReceivedInpatientBed(Ho_Solicitud $solicitud)
    {
        $stays = Ho_Hospitalizacion::join('ho_traspaso_sala008 as received_transfer',
                'received_transfer.id', '=', 'ho_hospitalizacion.id_traspaso')
            ->where('received_transfer.id_solicitud', $solicitud->id)
            ->where('received_transfer.id_paciente', $solicitud->id_paciente)
            ->where('received_transfer.paso', 3)->where('received_transfer.estado', 2)
            ->where('ho_hospitalizacion.id_paciente', $solicitud->id_paciente)
            ->where('ho_hospitalizacion.estado', 1)->select('ho_hospitalizacion.*')
            ->lockForUpdate()->limit(2)->get();

        return $stays->count() === 1;
    }

    private function activeAdmissionsForCompany($companyId)
    {
        return Ho_Solicitud::whereHas('encounter', function ($encounter) use ($companyId) {
            $encounter->where('id_empresa', $companyId)
                ->where('status', ClinicalEncounterService::ACTIVE);
        });
    }

    public function ingreso_modulos(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && (int) $user->estado === 1
            && ($user->isSystemUser() || $user->isDoctor() || $user->isNursingUser()), 403);
        $requestedStep = (int) $request->input('id_paso');
        if (!in_array($requestedStep, [2, 3], true)
            || strtolower((string) $request->input('opcion')) === 'traslado') {
            return ['respuesta' => 'error', 'msj' => 'Seleccione un ingreso válido de Emergencia u Hospitalización.'];
        }
        if (!$request->filled('id_doctor') && Auth::user()->isDoctor()) {
            $request->merge(['id_doctor' => Auth::id()]);
        }
        $doctorRoleId = \Sis_medico\User::roleIdForName(array('DOCTORES', 'DOCTOR'));
        abort_unless($doctorRoleId !== null, 422, 'El tipo de usuario DOCTORES no está configurado.');
        $companyId = app(ClinicalHospitalDestinationService::class)->companyId();
        $request->validate([
            'cedula' => 'required|string|max:45', 'id_sala' => 'required|integer|min:1',
            'id_seguro' => ['required', app(CompanyCatalogService::class)->existsRule('seguros')->where('inactivo', 1)],
            'id_doctor' => ['required', \Illuminate\Validation\Rule::exists('users', 'id')
                ->where('id_tipo_usuario', $doctorRoleId)->where('estado', 1)
                ->where(function ($doctor) use ($companyId) {
                    $doctor->whereExists(function ($membership) use ($companyId) {
                        $membership->select(DB::raw(1))->from('usuario_empresa')
                            ->whereColumn('usuario_empresa.id_usuario', 'users.id')
                            ->where('usuario_empresa.id_empresa', $companyId);
                    });
                })],
        ]);
        DB::beginTransaction();
        try {
            $idPaciente = (string) $request->input('cedula');
            $requestedId = (int) $request->input('idsolicitud', 0);
            $existing = $requestedId
                ? $this->activeAdmissionsForCompany($companyId)->where('id', $requestedId)->lockForUpdate()->first()
                : null;
            if ($requestedId && !$existing) {
                throw new \InvalidArgumentException('La solicitud no existe en la empresa activa.');
            }
            if ($existing && (string) $existing->id_paciente !== $idPaciente) {
                throw new \InvalidArgumentException('La solicitud no corresponde al paciente.');
            }
            if (!$existing) {
                $existing = $this->activeAdmissionsForCompany($companyId)
                    ->where('id_paciente', $idPaciente)->where('estado', 1)
                    ->whereIn('estado_paso', [1, 2, 3, 5, 6, 7])->lockForUpdate()->first();
            }
            if ($existing) {
                app(ClinicalHospitalTransferService::class)->authorize($existing, 'hospital.admission', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']);
                if ($existing->id_agenda) {
                    $linkedHistory = Historiaclinica::where('id_agenda', $existing->id_agenda)
                        ->where('id_paciente', $idPaciente)->first();
                    if (!$linkedHistory) {
                        throw new \InvalidArgumentException('El ingreso tiene agenda, pero falta su historia clínica. Revise este episodio antes de continuar.');
                    }
                    $linkedEncounter = \Sis_medico\ClinicalEncounter::findOrFail($existing->encounter_id);
                    if ($linkedHistory->encounter_id && (string) $linkedHistory->encounter_id !== (string) $linkedEncounter->id) {
                        throw new \InvalidArgumentException('La historia clínica está vinculada a otro episodio. No se puede reutilizar.');
                    }
                    app(ClinicalEncounterService::class)->link($linkedEncounter, ['agenda' => $existing->id_agenda,
                        'historiaclinica' => $linkedHistory->hcid]);
                    DB::commit();
                    return ['respuesta' => 'success', 'msj' => 'El paciente ya tiene un ingreso activo.',
                        'id_solicitud' => $existing->id, 'paso' => $existing->estado_paso,
                        'url' => (int) $existing->estado_paso === 3
                            ? route('hospital.documents.index', ['id_solicitud' => $existing->id])
                            : route('hospital.detallep', ['id' => $existing->id])];
                }
                $receivedInpatient = (int) $existing->estado_paso === 3
                    && $requestedId === (int) $existing->id;
                if ((int) $existing->estado_paso === 3 && !$receivedInpatient) {
                    throw new \InvalidArgumentException('Para vincular la Historia 008, abra el episodio hospitalario del paciente.');
                }
                if ($receivedInpatient && !$this->hasReceivedInpatientBed($existing)) {
                    throw new \InvalidArgumentException('La hospitalización no tiene una cama activa vinculada a este episodio. Revise la recepción antes de continuar.');
                }
                if (!in_array((int) $existing->estado_paso, [1, 2, 3], true)) {
                    throw new \InvalidArgumentException('La solicitud no es un ingreso pendiente de Emergencia.');
                }
                $request->request->set('idsolicitud', $existing->id);
                $request->request->set('opcion', 'completar');
            } else {
                $receivedInpatient = false;
            }
            $ip_cliente   = $request->ip();
            $idusuario    = Auth::user()->id;
            if (!is_null($request->id_doctor)) {
                $id_doctor = $request->id_doctor;
            } else {
                $id_doctor    = Auth::user()->id;
            }
            $id_paciente = $request['cedula'];

            // Si el paciente ya existe, serializamos sus ingresos para que dos
            // solicitudes simultaneas no superen juntas la validacion. Si aun
            // no existe, el mismo formulario lo creara dentro de esta transaccion.
            $paciente = Paciente::where('id', $id_paciente)->lockForUpdate()->first();

            $esTraslado = strtolower(trim((string) $request->input('opcion'))) === 'traslado';

            // Un ingreso nuevo no puede crear una segunda estancia para un paciente
            // que ya esta hospitalizado o que ya espera asignacion de cama.
            if (!$esTraslado && !$receivedInpatient) {
                $hospitalizacionActiva = Ho_Hospitalizacion::join('ho_traspaso_sala008 as active_transfer',
                        'active_transfer.id', '=', 'ho_hospitalizacion.id_traspaso')
                    ->join('ho_solicitud as active_request', 'active_request.id', '=', 'active_transfer.id_solicitud')
                    ->join('clinical_encounters as active_encounter', 'active_encounter.id', '=', 'active_request.encounter_id')
                    ->where('ho_hospitalizacion.id_paciente', $id_paciente)
                    ->where('ho_hospitalizacion.estado', 1)
                    ->where('active_encounter.id_empresa', $companyId)
                    ->select('ho_hospitalizacion.*')
                    ->first();
                if (!is_null($hospitalizacionActiva)) {
                    DB::rollBack();
                    return [
                        'respuesta' => 'error',
                        'msj' => 'El paciente ya se encuentra hospitalizado y tiene una cama activa. Debes trasladarlo o liberar su cama actual antes de registrar un nuevo ingreso.',
                        'codigo' => 'paciente_ya_hospitalizado',
                    ];
                }

                $ingresoPendiente = Ho_Traspaso_Sala008::join('ho_solicitud as pending_request',
                        'pending_request.id', '=', 'ho_traspaso_sala008.id_solicitud')
                    ->join('clinical_encounters as pending_encounter', 'pending_encounter.id', '=', 'pending_request.encounter_id')
                    ->where('ho_traspaso_sala008.id_paciente', $id_paciente)
                    ->where('ho_traspaso_sala008.estado', 1)
                    ->where('pending_encounter.id_empresa', $companyId)
                    ->select('ho_traspaso_sala008.*')
                    ->first();
                if (!is_null($ingresoPendiente) && (!$existing || $existing->id_agenda
                    || (int) $ingresoPendiente->id_solicitud !== (int) $existing->id)) {
                    DB::rollBack();
                    return [
                        'respuesta' => 'error',
                        'msj' => 'El paciente ya tiene un ingreso pendiente de asignación de cama. Continúa ese ingreso desde Gestión de camas.',
                        'codigo' => 'paciente_con_ingreso_pendiente',
                    ];
                }

            }
            $user = User::find($id_paciente);
            $datos_paciente = Ho_Datos_Paciente::Where('id_paciente', $id_paciente)->first();
            $id_paso = $request['id_paso'];
            $estado_paso = $this->resolveHospitalModule($id_paso);
            if (is_null($estado_paso)) {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'El módulo solicitado no está configurado. Comunícate con el área de Sistemas.'];
            }

            $sala = app(ClinicalHospitalDestinationService::class)
                ->assertSala($request->input('id_sala'), $id_paso, $companyId);
            $id_sala = $sala->id;
            $empresa = Empresa::where('id', $companyId)->where('estado', 1)->firstOrFail();

            $input_pac = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'telefono_llamar'    => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'referido'           => $request['referido'],
                'tipo_documento'     => $request['tipo_doc'],
                'id_seguro'          => $request['id_seguro'],
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'lugar_nacimiento'   => $request['lugarNacimiento']
            ];

            $input_pac_upd = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentescofamiliar' => $request['parentesco'],
                'telefono3'          => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'referido'           => $request['referido'],
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
                'lugar_nacimiento'   => $request['lugarNacimiento']
            ];

            $ran = rand(1, 10);
            $input_usu_c = [
                'id'               => $id_paciente,
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_tipo_usuario'  => $user ? $user->id_tipo_usuario : app(\Sis_medico\Services\v3\PatientUserType::class)->id(),
                'email'            => $request['id'] . $ran . '@mail.com',
                'password'         => bcrypt($request['id']),
                'tipo_documento'   => $request['tipo_doc'],
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ];

            $arr_ho_pac = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        => $request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'ip_creacion'            => $ip_cliente,
                'ip_modificacion'        => $ip_cliente,
                'id_usuariocrea'         => $idusuario,
                'id_usuariomod'          => $idusuario,
                'parentesco_afinidad'    => $request['parentesco'],
                'limite_credito'         => $request['limite_credito'],
                'grupo_prioritario'      => $request['grupoPrioritario'],
                'especifique_grupo'      => $request['especifiqueGrupo'],
                'autoidentificacion_etnica' => $request['autoIdentificacionEtnica'],
                'nacionalidad_etnica'    => $request['nacionalidadEtnica'],
                'pueblos'                => $request['pueblos'],
                'nivel_educacion'        => $request['nivelEducacion'],
                'estado_nivel_educacion' => $request['estadoNivelEducacion'],
                'calle_principal'        => $request['callePrincipal'],
                'calle_secundaria'       => $request['calleSecundaria'],
                'referencia'             => $request['referencia'],
                'id_subseguro'           => $request['id_subseguro'],
            ];

            $arr_ho_pac_up = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        => $request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'parentesco_afinidad'    => $request['parentesco'],
                'ip_modificacion'        => $ip_cliente,
                'id_usuariomod'          => $idusuario,
                'limite_credito'         => $request['limite_credito'],
                'grupo_prioritario'      => $request['grupoPrioritario'],
                'especifique_grupo'      => $request['especifiqueGrupo'],
                'autoidentificacion_etnica' => $request['autoIdentificacionEtnica'],
                'nacionalidad_etnica'    => $request['nacionalidadEtnica'],
                'pueblos'                => $request['pueblos'],
                'nivel_educacion'        => $request['nivelEducacion'],
                'estado_nivel_educacion' => $request['estadoNivelEducacion'],
                'calle_principal'        => $request['callePrincipal'],
                'calle_secundaria'       => $request['calleSecundaria'],
                'referencia'             => $request['referencia'],
                'id_subseguro'           => $request['id_subseguro'],
            ];


            if (is_null($paciente)) {

                if (is_null($user)) {
                    User::create($input_usu_c);
                }

                paciente::create($input_pac);

                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CREA NUEVO PACIENTE",
                    'dato_ant1'   => $id_paciente,
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato2'       => 'Hospital',
                ];

                Log_usuario::create($input_log);
            } else {
                $paciente->update($input_pac_upd);
                if (!is_null($datos_paciente)) {
                    $datos_paciente->update($arr_ho_pac_up);
                }
            }

            if (is_null($datos_paciente)) {
                Ho_Datos_Paciente::create($arr_ho_pac);
            } else {
                $datos_paciente->update($arr_ho_pac_up);
            }

            $espid = $this->resolveAdmissionSpecialty($id_doctor, $request);
            if (is_null($espid)) {
                DB::rollBack();
                return [
                    'respuesta' => 'error',
                    'codigo' => 'especialidad_admision_no_configurada',
                    'msj' => Auth::user()->isSystemUser()
                        ? 'No existe ninguna especialidad activa. Configura el catalogo de especialidades antes de registrar el ingreso.'
                        : 'El profesional responsable no tiene una especialidad activa asignada.',
                ];
            }

            // El episodio se abre despues de garantizar la existencia del
            // paciente. Esto permite que Admision cree paciente e ingreso en
            // una sola operacion y mantiene todas las escrituras atomicas.
            $encounterService = app(ClinicalEncounterService::class);
            $encounter = $receivedInpatient
                ? \Sis_medico\ClinicalEncounter::where('id', $existing->encounter_id)
                    ->where('id_paciente', $id_paciente)->where('id_empresa', $empresa->id)
                    ->where('status', 'ACTIVE')->where('care_setting', ClinicalEncounterService::INPATIENT)
                    ->lockForUpdate()->firstOrFail()
                : $encounterService->openOrReuse(
                $id_paciente,
                $encounterService->settingFromLegacyStep($id_paso),
                array(
                    'id_empresa' => $empresa->id,
                    'id_hospital' => $sala->id_hospital,
                    'actor_id' => $idusuario,
                    'ip' => $ip_cliente,
                    'legacy_step' => $id_paso,
                    'override' => $request->input('clinical_override'),
                    'override_reason' => $request->input('clinical_override_reason'),
                )
            );



            if (!is_null($request['id_procedimiento'])) {
                $id_procedimiento = $request['id_procedimiento'];
            } else {
                $id_procedimiento = null;
            }


            $input_agenda = [
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s', (strtotime("+15 Minute"))),
                'id_paciente'     => $id_paciente,
                'id_doctor1'      => $id_doctor,
                'proc_consul'     => '1',
                'estado_cita'     => '4',
                'id_empresa'      => $empresa->id,
                'espid'           => $espid,
                'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
                'id_seguro'       => $request['id_seguro'],
                'estado'          => '1',
                'id_sala'         => $id_sala,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_doctor,
                'id_usuariomod'   => $id_doctor,
                'id_procedimiento' => $id_procedimiento,
            ];

            $id_agenda = Agenda::insertGetId($input_agenda);

            if ((int) $id_paso === 4 || (int) $id_paso === 5) {
                $arr_agendap = [
                    'id_agenda' => $id_agenda,
                    'id_procedimiento' => $request->procedimiento,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_doctor,
                    'id_usuariomod'   => $id_doctor,
                ];

                AgendaProcedimiento::create($arr_agendap);
            }



            $consulta_crear_new = [
                'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($consulta_crear_new);

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '1',
                'estado_cita'     => '1',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_doctor1'      => $id_doctor,
                'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];

            Log_Agenda::create($input_log);

            $input_historia = [

                'parentesco'      => $request['parentesco'],
                'id_usuario'      => $id_paciente,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_seguro'       => $request['id_seguro'],
                'id_doctor1'      => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $procedimiento_completo = $this->resolveHospitalClinicalProcedure($idusuario, $ip_cliente);
            $id_procedimiento_completo = $procedimiento_completo->id;

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_hc_procedimiento = [
                'id_hc'                     => $id_historia,
                'id_doctor_examinador'      => $id_doctor,
                'id_doctor_examinador2'     => $id_doctor,
                'id_seguro'                 => $request['id_seguro'],
                'id_procedimiento_completo' => $id_procedimiento_completo,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $procedimiento = Procedimiento::where('nombre', 'like', '%EVOLUCIONES HOSPITAL%')->first();
            if (!is_null($procedimiento)) {
                $input_proc_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $procedimiento->id,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];
                Hc_Procedimiento_Final::insert($input_proc_final);
            }

            $input_hc_protocolo = [
                'fecha'                => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio'          => date('H:i:s'),
                'hora_fin'             => date('H:i:s'),
                'estado_final'         => ' ',
                'ip_modificacion'      => $ip_cliente,
                'hcid'                 => $id_historia,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'tipo_procedimiento'   => 0,
            ];
            hc_protocolo::insert($input_hc_protocolo);


            $input_hc_evolucion = [
                'hc_id_procedimiento' => $id_hc_procedimiento,
                'hcid'                => $id_historia,
                'secuencia'           => '0',
                'fecha_ingreso'       => ' ',
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
                'id_usuariocrea'      => $idusuario,
                'ip_creacion'         => $ip_cliente,

            ];
            $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);

            $input_child_pugh = [
                'id_hc_evolucion'       => $id_evolucion,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
            ];

            $id_child = hc_child_pugh::create($input_child_pugh);

            $input_hc_receta = [
                'id_hc'           => $id_historia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,

            ];
            $id_receta = hc_receta::insertGetId($input_hc_receta);

            $arr_receta_evolucion = [
                'id_receta'             => $id_receta,
                'id_evolucion'          => $id_evolucion,
                'estado'                => 1,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $receta_evolucion = Ho_Hc_Receta_Evolucion::create($arr_receta_evolucion);

            if (is_null($request['opcion'])) {
                $arr_solicitud = [
                    'id_paciente'           => $request['cedula'],
                    'id_agenda'             => $id_agenda,
                    'id_seguro'             => $request['id_seguro'],
                    'fecha_ingreso'         => date('Y-m-d H:i:s'),
                    'estado_paso'           => $id_paso,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];

                $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);
            } else {
                $ho_solicitud = $request['idsolicitud'];
            }

            $sol_log = [
                'id_ho_solicitud'       => $ho_solicitud,
                'estado_paso'           => $id_paso,
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];

            if ($receivedInpatient) {
                $emergencyLog = Ho_Log_Solicitud::where('id_ho_solicitud', $ho_solicitud)
                    ->where('estado_paso', 2)->orderByDesc('id')->lockForUpdate()->first();
                if (!$emergencyLog) {
                    throw new \InvalidArgumentException('El ingreso de Emergencia no tiene un registro de origen para vincular la Historia 008.');
                }
                $emergencyLog->update(['id_agenda' => $id_agenda,
                    'id_usuariomod' => $idusuario, 'ip_modificacion' => $ip_cliente]);
            } else {
                Ho_Log_Solicitud::create($sol_log);
            }
            if ($estado_paso->id == 2) {
                $arr_form = [
                    'fecha_creacion'        => date('Y-m-d'),
                    'id_agenda'             => $id_agenda,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];

                $form008 = app(ClinicalEmergencyFormService::class)
                    ->ensure(Ho_Solicitud::findOrFail($ho_solicitud), $arr_form);
                $id_form = $form008->id;
            }


            $solicitudIngreso = Ho_Solicitud::findOrFail($ho_solicitud);
            $solicitudIngreso->update(['id_agenda' => $id_agenda, 'estado_paso' => $receivedInpatient ? 3 : $id_paso,
                'id_seguro' => $request->input('id_seguro'), 'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente]);

            $encounterService->link($encounter, array(
                'agenda' => $id_agenda,
                'historiaclinica' => $id_historia,
                'ho_solicitud' => $ho_solicitud,
            ));
            if (!Historiaclinica::where('hcid', $id_historia)->where('id_agenda', $id_agenda)
                ->where('id_paciente', $id_paciente)->where('encounter_id', $encounter->id)->exists()) {
                throw new \RuntimeException('No se pudo vincular la historia clínica al episodio; el ingreso no se guardó.');
            }

            $directHospitalAdmission = !$receivedInpatient && (int) $id_paso === 3;
            if ($directHospitalAdmission) {
                Ho_Traspaso_Sala008::create([
                    'id_solicitud' => $ho_solicitud,
                    'id_paciente' => $id_paciente,
                    'id_sala' => $id_sala,
                    'paso' => 3,
                    'seccion' => 4,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_doctor' => $id_doctor,
                    'observaciones' => 'Ingreso directo a Hospitalización desde Admisión.',
                    'estado' => 1,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
            if (!$receivedInpatient && (int) $id_paso === 2) {
                app(ClinicalEmergencyFlowService::class)->ensure(Ho_Solicitud::findOrFail($ho_solicitud), $request);
            }
            DB::commit();
            $requiresBedAssignment = $directHospitalAdmission;

            return [
                'respuesta' => 'success',
                'msj' => $receivedInpatient ? 'Historia 008 vinculada al episodio hospitalario.' : ($directHospitalAdmission
                    ? 'Ingreso registrado en Hospitalización. El paciente queda pendiente de asignación de cama.'
                    : 'Guardado con Exito'),
                'id_agenda' => $id_agenda,
                'id_solicitud' => $ho_solicitud,
                'url' => $receivedInpatient
                    ? route('hospital.documents.index', ['id_solicitud' => $ho_solicitud])
                    : ($directHospitalAdmission
                        ? route('hospital.index_modulos', ['id_paso' => 3, 'cedula' => $id_paciente])
                        : route('hospital.emergencia', ['solicitud' => $ho_solicitud])),
                'paso' => $receivedInpatient ? 3 : $id_paso,
                'requiere_asignacion_cama' => $requiresBedAssignment,
                'asignacion_cama_url' => $requiresBedAssignment ? route('hospital.gcuartos') : null,
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return array('respuesta' => 'error', 'msj' => implode(' ', \Illuminate\Support\Arr::flatten($e->errors())));
        } catch (ClinicalEncounterConflictException $e) {
            DB::rollBack();
            return array(
                'respuesta' => 'conflicto_clinico',
                'codigo' => 'R-01',
                'msj' => $e->getMessage(),
                'episodio_activo' => $e->conflictingEncounterId(),
                'requiere_autorizacion' => true,
                'puede_autorizar' => Auth::user()->isSystemUser() || Auth::user()->isDoctor(),
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }


    //Funcion del index donde se encuentra el gestor de documentos donde esta el formulario008

    public function guardar_traspaso(Request $request)
    {
        if ($request->input('preserve_active_orders') !== ClinicalTransferReconciliationService::PRESERVE) {
            return ['respuesta' => 'error', 'msj' => 'Confirme la conservacion de las ordenes activas.'];
        }
        try {
            $transfer = app(ClinicalHospitalTransferService::class)->requestTransfer(
                $request->input('id_solicitud'), $request->input('id_paso'),
                $request->input('id_sala'), $request->input('transfer_reason')
            );
            return ['respuesta' => 'success', 'msj' => 'Traslado solicitado. El area cambiara al confirmar la recepcion.',
                'paso' => $transfer->paso, 'traspaso_id' => $transfer->id];
        } catch (\InvalidArgumentException $exception) {
            return ['respuesta' => 'error', 'msj' => $exception->getMessage()];
        }
    }
}
