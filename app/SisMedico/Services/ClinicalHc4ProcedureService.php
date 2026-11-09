<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Empresa;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_agenda;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Sala;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;

class ClinicalHc4ProcedureService
{
    public function crear_procedimiento(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->paciente);
        $sala           = \Sis_medico\Sala::where('proc_consul_sala', 1)->first();

        $empresa = Empresa::where('prioridad','1')->first();
        //dd($empresa);

        $procedimiento_crear_new = [
            'anterior'        => 'PROCEDIMIENTOS -> El Dr. creó un nuevo procedimiento',
            'nuevo'           => 'PROCEDIMIENTOS -> El Dr. creó un nuevo procedimiento',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini'         => Date('Y-m-d H:i:s'),
                'fechafin'         => Date('Y-m-d H:i:s'),
                'id_paciente'      => $paciente->id,
                'id_doctor1'       => $id_doctor,
                'proc_consul'      => '4',
                'estado_cita'      => '4',
                'id_empresa'       => $empresa->id,
                'espid'            => '4',
                'observaciones'    => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_seguro'        => $paciente->id_seguro,
                'estado'           => '4',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $id_doctor,
                'id_usuariomod'    => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala'          => $sala->id,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);
            //return $id_agenda;

            $txt_pro = '';
            foreach ($procedimientos as $value) {

                if ($procedimientop != $value) {
                    $txt_pro = $txt_pro . '+' . $value;
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $id_doctor,
                        'id_usuariomod'    => $id_doctor,
                    ]);
                }
            }

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_doctor1'      => $id_doctor,
                'descripcion'     => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'campos_ant'      => 'PRO: ' . $procedimientop . $txt_pro,

                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];

            $idusuario = $id_doctor;

            Log_agenda::create($input_log);

            $input_historia = [

                'parentesco'      => $paciente->parentesco,
                'id_usuario'      => $paciente->id_usuario,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $paciente->id,
                'id_seguro'       => $paciente->id_seguro,

                'id_doctor1'      => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_pentax = [
                'id_agenda'       => $id_agenda,
                'hcid'            => $id_historia,
                'id_sala'         => $sala->id,
                'id_doctor1'      => $idusuario,
                'id_seguro'       => $paciente->id_seguro,
                'observacion'     => "PROCEDIMIENTO CREADO POR EL DOCTOR",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];

            $id_pentax = Pentax::insertGetId($input_pentax);

            $list_proc = '';
            foreach ($procedimientos as $value) {
                $input_pentax_pro2 = [
                    'id_pentax'        => $id_pentax,
                    'id_procedimiento' => $value,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro2);
                $list_proc = $list_proc . "+" . $value;
            }

            $input_log_px = [
                'id_pentax'       => $id_pentax,
                'tipo_cambio'     => "CREADO POR EL DOCTOR",
                'descripcion'     => "EN ESPERA",
                'estado_pentax'   => '0',
                'procedimientos'  => $list_proc,
                'id_doctor1'      => $idusuario,
                'observacion'     => "CREADO POR EL DOCTOR",
                'id_sala'         => $sala->id,
                'id_seguro'       => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc'                 => $id_historia,
                'id_seguro'             => $paciente->id_seguro,
                'id_doctor_examinador'  => $idusuario,
                'id_doctor_examinador2' => $idusuario,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

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
                'tipo_procedimiento'   => $request->tipo_procedimiento,
            ];
            hc_protocolo::insert($input_hc_protocolo);

            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }

            return "ok";
        }

        return "Ingrese el Procedimiento";

    }

    //CREAR PACIENTE
}
