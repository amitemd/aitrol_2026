<?php

namespace Sis_medico\Services\v3;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentLaboratoryOrderService
{
    public function create(Request $request, $agenda, $userId)
    {
        $protocol = DB::table('protocolo')->where('id', $request->input('id_protocolo'))
            ->where('estado', '1')->first();
        if (!$protocol) {
            throw ValidationException::withMessages(array('id_protocolo' => 'El protocolo seleccionado no está disponible.'));
        }

        $insurance = \Sis_medico\Seguro::query()->where('id', $agenda->id_seguro)->where('inactivo', '1')->first();
        if (!$insurance || (string) $insurance->tipo !== '0') {
            throw ValidationException::withMessages(array('id_seguro' => 'El seguro de la agenda no está habilitado para esta orden.'));
        }

        $convention = DB::table('convenio')->where('id_seguro', $agenda->id_seguro)
            ->where('id_empresa', $agenda->id_empresa)->first();
        if (!$convention) {
            throw ValidationException::withMessages(array('id_empresa' => 'La empresa activa no tiene un convenio configurado para este seguro.'));
        }

        $exams = DB::table('examen')->where('estado', '1')->where('publico_privado', '0')
            ->select('id', 'nombre', 'valor')->orderBy('id')->get()->filter(function ($exam) use ($request) {
                return $request->filled('ch'.$exam->id);
            })->values();
        if (!$exams->count()) {
            throw ValidationException::withMessages(array('examenes' => 'Selecciona al menos un examen disponible.'));
        }

        $level = isset($convention->id_nivel) ? $convention->id_nivel : 0;
        $prices = array();
        $total = 0;
        foreach ($exams as $exam) {
            $price = (float) $exam->valor;
            $levelPrice = DB::table('examen_nivel')->where('id_examen', $exam->id)->where('nivel', $level)->value('valor1');
            if (!is_null($levelPrice)) $price = (float) $levelPrice;
            $prices[$exam->id] = $price;
            $total += $price;
        }

        $ip = $request->ip();
        $now = date('Y-m-d H:i:s');
        return DB::transaction(function () use ($request, $agenda, $userId, $protocol, $exams, $prices, $total, $level, $ip, $now) {
            $historyId = DB::table('historiaclinica')->where('id_agenda', $agenda->id)->value('hcid');
            $orderId = DB::table('examen_orden')->insertGetId(array(
                'id_paciente' => $agenda->id_paciente,
                'anio' => date('Y'),
                'mes' => date('m'),
                'id_protocolo' => $protocol->id,
                'id_seguro' => $agenda->id_seguro,
                'id_nivel' => $level,
                'est_amb_hos' => $request->input('est_amb_hos'),
                'id_doctor_ieced' => $agenda->id_doctor1,
                'doctor_txt' => trim((string) $request->input('doctor_txt')),
                'observacion' => trim((string) $request->input('observacion')),
                'id_empresa' => $agenda->id_empresa,
                'hcid' => $historyId,
                'id_agenda' => $agenda->id,
                'cantidad' => $exams->count(),
                'valor' => $total,
                'total_valor' => $total,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'fecha_orden' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ));

            $examNames = array();
            foreach ($exams as $exam) {
                DB::table('examen_detalle')->insert(array(
                    'id_examen_orden' => $orderId,
                    'id_examen' => $exam->id,
                    'valor' => $prices[$exam->id],
                    'p_comision' => 0,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'id_usuariocrea' => $userId,
                    'id_usuariomod' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                $examNames[] = $exam->nombre;
            }

            $patient = DB::table('paciente')->where('id', $agenda->id_paciente)->first();
            if (!$patient) throw ValidationException::withMessages(array('id' => 'El paciente de la agenda ya no está disponible.'));
            $patientUpdate = array();
            if (empty($patient->fecha_nacimiento)) $patientUpdate['fecha_nacimiento'] = $request->input('fecha_nacimiento');
            if (empty($patient->sexo)) $patientUpdate['sexo'] = $request->input('sexo');
            if ($patientUpdate) {
                $patientUpdate['ip_modificacion'] = $ip;
                $patientUpdate['id_usuariomod'] = $userId;
                $patientUpdate['updated_at'] = $now;
                DB::table('paciente')->where('id', $agenda->id_paciente)->update($patientUpdate);
            }

            DB::table('log_usuario')->insert(array(
                'id_usuario' => $userId,
                'ip_usuario' => $ip,
                'descripcion' => 'GENERA ORDEN EXAMEN V3',
                'dato_ant1' => $agenda->id_paciente,
                'dato1' => trim($patient->nombre1.' '.$patient->nombre2.' '.$patient->apellido1.' '.$patient->apellido2),
                'dato_ant4' => implode(' + ', $examNames),
                'created_at' => $now,
                'updated_at' => $now,
            ));

            return $orderId;
        });
    }
}
