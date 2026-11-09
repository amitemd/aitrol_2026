<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Services\AmbulatoryAdmissionService;

class LocalClinicalPilotSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('LocalClinicalPilotSeeder solo puede ejecutarse en local o testing.');
        }

        $userId = '0000000001';
        $doctorId = '0000000003';
        $now = now();
        $audit = [
            'id_usuariocrea' => $userId,
            'id_usuariomod' => $userId,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'updated_at' => $now,
        ];

        $admissionAgendaId = DB::transaction(function () use ($audit, $doctorId, $now, $userId) {
            $companyId = (string) DB::table('empresa')->where('estado', 1)->value('id');
            $insuranceId = (int) DB::table('seguros')->where('inactivo', 1)->orderBy('id')->value('id');
            $specialtyId = (int) (DB::table('user_espe')->where('usuid', $doctorId)->value('espid')
                ?: DB::table('especialidad')->where('estado', 1)->orderBy('id')->value('id'));
            $roomId = (int) DB::table('sala')->where('estado', 1)->orderBy('id')->value('id');
            $procedureId = (int) DB::table('procedimiento')->where('estado', 1)->orderBy('id')->value('id');

            DB::table('hospital')->where('id', 1)->update(['estado' => 1, 'deleted_at' => null]);
            DB::table('sala')->where('id', 1)->update(['estado' => 1, 'deleted_at' => null]);
            $hospitalRoomId = DB::table('sala')->where('nombre_sala', 'HOSPITALIZACION QA')->value('id');
            if (! $hospitalRoomId) {
                $hospitalRoomId = DB::table('sala')->insertGetId(array_merge($audit, [
                    'nombre_sala' => 'HOSPITALIZACION QA', 'id_hospital' => 1,
                    'proc_consul_sala' => 0, 'estado' => 1, 'created_at' => $now,
                ]));
            }

            $floorId = DB::table('piso')->where('nombre_piso', 'PISO QA')->value('id');
            if (! $floorId) {
                $floorId = DB::table('piso')->insertGetId([
                    'nombre_piso' => 'PISO QA', 'nombrepiso' => 'PISO QA', 'estado' => 1,
                    'estadopiso' => 1, 'habitacion_total' => 1, 'id_usuario' => $userId,
                    'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                    'ip_creacion' => '127.0.0.1', 'ip_modificacion' => 1,
                ]);
            }
            $roomTypeId = DB::table('tipo_habitacion')->where('nombre', 'TIPO QA')->value('id');
            if (! $roomTypeId) {
                $roomTypeId = DB::table('tipo_habitacion')->insertGetId(array_merge($audit, [
                    'nombre' => 'TIPO QA', 'descripcion' => 'PRUEBA LOCAL', 'cantidad_camas' => 2,
                    'estado' => 1, 'id_usuario' => $userId, 'created_at' => $now,
                ]));
            }
            $bedroomId = DB::table('habitacion')->where('codigo', 'HAB-QA-01')->value('id');
            if (! $bedroomId) {
                $bedroomId = DB::table('habitacion')->insertGetId(array_merge($audit, [
                    'id_tipo' => $roomTypeId, 'id_piso' => $floorId, 'estado' => 1,
                    'descripcion' => 'HABITACION QA', 'codigo' => 'HAB-QA-01',
                    'id_usuario' => $userId, 'created_at' => $now,
                ]));
            }
            foreach (['CAMA-QA-01', 'CAMA-QA-02'] as $bedCode) {
                DB::table('cama')->updateOrInsert(
                    ['codigo' => $bedCode],
                    array_merge($audit, ['estado' => 1, 'id_habitacion' => $bedroomId,
                        'id_usuario' => $userId, 'created_at' => $now])
                );
            }

            foreach ([
                ['QA00000000001', 'ANA', 'AMBULATORIA', 2, '1990-01-15'],
                ['QA00000000002', 'BRUNO', 'HOSPITALARIO', 1, '1985-06-20'],
                ['QA00000000003', 'CARLA', 'LABORATORIO', 2, '2000-11-05'],
            ] as $patient) {
                DB::table('paciente')->updateOrInsert(
                    ['id' => $patient[0]],
                    array_merge($audit, [
                        'nombre1' => $patient[1],
                        'nombre2' => 'QA',
                        'apellido1' => $patient[2],
                        'apellido2' => 'PRUEBA',
                        'tipo_documento' => 1,
                        'ciudad' => 'GUAYAQUIL',
                        'direccion' => 'DIRECCION FICTICIA QA',
                        'telefono1' => '0990000000',
                        'id_seguro' => $insuranceId,
                        'imagen_url' => ' ',
                        'fecha_nacimiento' => $patient[4],
                        'sexo' => $patient[3],
                        'id_usuario' => $userId,
                        'menoredad' => 0,
                        'alergias' => 'DATOS FICTICIOS QA',
                        'deleted_at' => null,
                    ])
                );
            }

            $appointments = [
                ['QA00000000001', '2026-09-07 09:00:00', '2026-09-07 09:30:00', 'QA AMBULATORIO - ADMITIR'],
                ['QA00000000003', '2026-09-07 10:00:00', '2026-09-07 10:30:00', 'QA LABORATORIO'],
            ];
            $admissionId = null;
            foreach ($appointments as $appointment) {
                $existing = DB::table('agenda')->where('observaciones', $appointment[3])->first();
                $values = array_merge($audit, [
                    'fechaini' => $appointment[1],
                    'fechafin' => $appointment[2],
                    'id_paciente' => $appointment[0],
                    'id_doctor1' => $doctorId,
                    'proc_consul' => 0,
                    'id_sala' => $roomId ?: null,
                    'id_procedimiento' => $procedureId ?: null,
                    'tipo_cita' => 0,
                    'estado_cita' => 1,
                    'id_empresa' => $companyId,
                    'est_amb_hos' => 0,
                    'espid' => $specialtyId,
                    'id_seguro' => $insuranceId,
                    'estado' => 1,
                    'observaciones' => $appointment[3],
                ]);
                if ($existing) {
                    DB::table('agenda')->where('id', $existing->id)->update($values);
                    $agendaId = $existing->id;
                } else {
                    $values['created_at'] = $now;
                    $agendaId = DB::table('agenda')->insertGetId($values);
                }
                if ($appointment[0] === 'QA00000000001') {
                    $admissionId = $agendaId;
                }
            }

            $group = DB::table('examen_agrupador')->where('nombre', 'QA LABORATORIO')->first();
            if (! $group) {
                $groupId = DB::table('examen_agrupador')->insertGetId(array_merge($audit, [
                    'nombre' => 'QA LABORATORIO',
                    'estado' => 1,
                    'created_at' => $now,
                ]));
            } else {
                $groupId = $group->id;
            }

            foreach ([
                ['codigo' => 'QA-HEM', 'nombre' => 'HEMOGRAMA QA', 'valor' => 10.00],
                ['codigo' => 'QA-GLU', 'nombre' => 'GLUCOSA QA', 'valor' => 5.00],
            ] as $exam) {
                DB::table('examen')->updateOrInsert(
                    ['codigo_inter' => $exam['codigo']],
                    array_merge($audit, [
                        'nombre' => $exam['nombre'],
                        'descripcion' => $exam['nombre'].' - DATO FICTICIO PARA CERTIFICACION LOCAL',
                        'valor' => $exam['valor'],
                        'id_agrupador' => $groupId,
                        'tarifario' => $exam['codigo'],
                        'publico_privado' => 0,
                        'estado' => 1,
                    ])
                );
            }

            foreach ([
                [1, 'LUN'], [2, 'MAR'], [3, 'MIE'],
                [4, 'JUE'], [5, 'VIE'],
            ] as $day) {
                DB::table('horario_doctor')->updateOrInsert(
                    ['id_doctor' => $doctorId, 'ndia' => $day[0], 'tipo' => 0],
                    array_merge($audit, [
                        'dia' => $day[1],
                        'hora_ini' => '08:00:00',
                        'hora_fin' => '17:00:00',
                        'estado' => 1,
                    ])
                );
            }

            return $admissionId;
        });

        if ($admissionAgendaId) {
            app(AmbulatoryAdmissionService::class)
                ->admit($admissionAgendaId, $userId, '127.0.0.1');
        }

        $this->command?->info('Catálogos clínicos ficticios para el piloto local creados correctamente.');
    }
}
