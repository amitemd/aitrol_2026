<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Sis_medico\Services\PatientTenantService;

class OdontologiaTestDataSeeder extends Seeder
{
    private const USER_IP = '127.0.0.1';

    private const DENTAL_PATIENT = 'QAODONTO00001';

    private const PERIO_PATIENT = 'QAPERIO000001';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'pruebas'])) {
            throw new RuntimeException('OdontologiaTestDataSeeder solo puede ejecutarse en local, testing o pruebas.');
        }

        foreach (['cie_10_3', 'cie_10_4', 'odont_formulario033', 'odont_procedimientos', 'odont_paciente_procedimientos', 'odont_cotizaciones', 'perio_periodontogramas'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('Falta la tabla '.$table.'. Ejecute primero las migraciones.');
            }
        }

        DB::transaction(function () {
            $now = now();
            $actor = (string) DB::table('users')->where('estado', 1)->orderBy('id')->value('id');
            $doctor = (string) (DB::table('users')->where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('id')->value('id') ?: $actor);
            $company = (string) DB::table('empresa')->where('estado', 1)->orderBy('id')->value('id');
            $insurance = DB::table('seguros')->where('inactivo', 1)->orderBy('id')->value('id');
            $room = DB::table('sala')->where('estado', 1)->orderBy('id')->value('id');

            if ($actor === '' || $doctor === '' || $company === '' || ! $insurance) {
                throw new RuntimeException('Ejecute los seeders base: se requiere usuario, doctor, empresa y seguro activos.');
            }

            $dentalSpecialty = DB::table('especialidad')->where('id', 3)->where('estado', 1)->first();
            if (! $dentalSpecialty) {
                throw new RuntimeException('No existe la especialidad odontologica activa con ID 3.');
            }
            $periodontalSpecialty = DB::table('especialidad')
                ->where('estado', 1)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%periodon%'])
                ->orderBy('id')->first();
            if (! $periodontalSpecialty) {
                $periodontalId = DB::table('especialidad')->insertGetId($this->onlyColumns('especialidad', [
                    'nombre' => 'PERIODONTOLOGIA QA',
                    'descripcion' => 'Especialidad ficticia para validacion local',
                    'estado' => 1,
                    'id_usuariocrea' => $actor,
                    'id_usuariomod' => $actor,
                    'ip_creacion' => self::USER_IP,
                    'ip_modificacion' => self::USER_IP,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            } else {
                $periodontalId = $periodontalSpecialty->id;
            }

            $this->upsertQaDiagnosis($actor, $now);

            $this->upsertPatient(self::DENTAL_PATIENT, 'DANIELA', 'ODONTOLOGIA', $insurance, $actor, $now);
            $this->upsertPatient(self::PERIO_PATIENT, 'PABLO', 'PERIODONCIA', $insurance, $actor, $now);
            if (Schema::hasTable('paciente_empresa')) {
                app(PatientTenantService::class)->associate(self::DENTAL_PATIENT, $company, $actor, self::USER_IP);
                app(PatientTenantService::class)->associate(self::PERIO_PATIENT, $company, $actor, self::USER_IP);
            }

            $dentalProcedure = $this->upsertCatalogProcedure(
                'QA PROFILAXIS Y CONTROL ODONTOLOGICO', 'Promocion y prevencion QA', 3, '35.00', $actor, $now
            );
            $perioProcedure = $this->upsertCatalogProcedure(
                'QA SONDAJE PERIODONTAL COMPLETO', 'Periodoncia QA', $periodontalId, '45.00', $actor, $now
            );

            $dentalAgenda = $this->upsertAgenda(
                self::DENTAL_PATIENT, $doctor, $company, $insurance, $room, 3,
                $dentalProcedure, 'QA ODONTOLOGIA - NO USAR EN PRODUCCION', '2026-09-22 09:00:00', $actor, $now
            );
            $perioAgenda = $this->upsertAgenda(
                self::PERIO_PATIENT, $doctor, $company, $insurance, $room, $periodontalId,
                $perioProcedure, 'QA PERIODONTOLOGIA - NO USAR EN PRODUCCION', '2026-09-22 10:00:00', $actor, $now
            );
            $dentalHistory = $this->upsertHistory($dentalAgenda, self::DENTAL_PATIENT, $insurance, $doctor, $actor, $now);
            $perioHistory = $this->upsertHistory($perioAgenda, self::PERIO_PATIENT, $insurance, $doctor, $actor, $now);

            $dentalPayload = $this->dentalPayload($dentalHistory, $dentalAgenda, $dentalProcedure, $doctor);
            DB::table('odont_formulario033')->updateOrInsert(
                ['hcid' => $dentalHistory],
                [
                    'id_agenda' => $dentalAgenda,
                    'id_paciente' => self::DENTAL_PATIENT,
                    'id_doctor' => $doctor,
                    'version' => 1,
                    'fecha_atencion' => '2026-09-22',
                    'hora_atencion' => '09:00:00',
                    'tipo_atencion' => 'Primera',
                    'nro_hoja' => 1,
                    'motivo_consulta' => 'Control preventivo ficticio QA',
                    'presion_arterial' => '120/80',
                    'frecuencia_cardiaca' => 72,
                    'temperatura' => 36.5,
                    'frecuencia_respiratoria' => 16,
                    'snapshot_json' => json_encode($dentalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'fecha_apertura' => '2026-09-22 09:00:00',
                    'vigente_hasta' => '2027-09-22',
                    'estado' => 1,
                    'id_usuariocrea' => $actor,
                    'id_usuariomod' => $actor,
                    'ip_creacion' => self::USER_IP,
                    'ip_modificacion' => self::USER_IP,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $formId = DB::table('odont_formulario033')->where('hcid', $dentalHistory)->value('id');
            $odontProcedure = DB::table('odont_procedimientos')->where('hcid', $dentalHistory)->where('codigo', 'QA-ODO-001')->first();
            $odontValues = [
                'hcid' => $dentalHistory,
                'id_agenda' => $dentalAgenda,
                'id_paciente' => self::DENTAL_PATIENT,
                'codigo' => 'QA-ODO-001',
                'nombre' => 'PROFILAXIS QA',
                'descripcion' => 'Registro ficticio para comprobar el flujo odontologico',
                'estado' => 1,
                'id_usuariocrea' => $actor,
                'id_usuariomod' => $actor,
                'ip_creacion' => self::USER_IP,
                'ip_modificacion' => self::USER_IP,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($odontProcedure) {
                DB::table('odont_procedimientos')->where('id', $odontProcedure->id)->update($odontValues);
                $odontProcedureId = $odontProcedure->id;
            } else {
                $odontProcedureId = DB::table('odont_procedimientos')->insertGetId($odontValues);
            }
            DB::table('odont_paciente_procedimientos')->updateOrInsert(
                ['id_odont_procedimiento' => $odontProcedureId, 'id_procedimiento_catalogo' => $dentalProcedure, 'pieza_fdi' => '11'],
                [
                    'hcid' => $dentalHistory,
                    'id_agenda' => $dentalAgenda,
                    'id_paciente' => self::DENTAL_PATIENT,
                    'id_formulario033' => $formId,
                    'fecha' => '2026-09-22',
                    'cara_dental' => 'V',
                    'diagnostico' => 'QA00 - control preventivo ficticio',
                    'procedimiento' => 'QA PROFILAXIS Y CONTROL ODONTOLOGICO',
                    'observaciones' => 'DATO FICTICIO QA',
                    'estado_procedimiento' => 'REALIZADO',
                    'id_doctor' => $doctor,
                    'estado' => 1,
                    'id_usuariocrea' => $actor,
                    'id_usuariomod' => $actor,
                    'ip_creacion' => self::USER_IP,
                    'ip_modificacion' => self::USER_IP,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $quoteDetail = [[
                'clave' => $dentalHistory.':11:2026-09-22:'.$dentalProcedure,
                'fecha' => '2026-09-22', 'pieza' => '11', 'caras' => 'V',
                'diagnosticos' => 'QA00 - control preventivo ficticio',
                'id_procedimiento' => (string) $dentalProcedure,
                'procedimiento' => 'QA PROFILAXIS Y CONTROL ODONTOLOGICO', 'precio' => '35.00',
            ]];
            DB::table('odont_cotizaciones')->updateOrInsert(
                ['id_paciente' => self::DENTAL_PATIENT, 'hcid' => $dentalHistory, 'estado' => 'BORRADOR'],
                ['version' => 1, 'total' => 35, 'detalle_json' => json_encode($quoteDetail, JSON_UNESCAPED_UNICODE),
                    'id_usuariocrea' => $actor, 'id_usuariomod' => $actor, 'created_at' => $now, 'updated_at' => $now]
            );

            $periodontalPayload = $this->periodontalPayload();
            DB::table('perio_periodontogramas')->updateOrInsert(
                ['id_agenda' => $perioAgenda],
                ['hcid' => $perioHistory, 'id_paciente' => self::PERIO_PATIENT, 'id_doctor' => $doctor,
                    'payload_json' => json_encode($periodontalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'estado' => 1, 'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                    'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
                    'created_at' => $now, 'updated_at' => $now]
            );
        });

        if ($this->command) {
            $this->command->info('Datos ficticios de Odontologia y Periodontologia creados sin reemplazar registros reales.');
        }
    }

    private function upsertPatient(string $id, string $firstName, string $lastName, $insurance, string $actor, $now): void
    {
        $existing = DB::table('paciente')->where('id', $id)->first();
        if ($existing && (($existing->apellido2 ?? null) !== 'PRUEBA' || ($existing->nombre2 ?? null) !== 'QA')) {
            throw new RuntimeException('El identificador '.$id.' ya pertenece a un paciente que no es de QA. No se modificó ese registro.');
        }

        DB::table('paciente')->updateOrInsert(['id' => $id], $this->onlyColumns('paciente', [
            'nombre1' => $firstName, 'nombre2' => 'QA', 'apellido1' => $lastName, 'apellido2' => 'PRUEBA',
            'tipo_documento' => 1, 'ciudad' => 'GUAYAQUIL', 'direccion' => 'DIRECCION FICTICIA QA',
            'telefono1' => '0990000000', 'id_seguro' => $insurance, 'imagen_url' => ' ',
            'fecha_nacimiento' => '1990-01-15', 'sexo' => 2, 'id_usuario' => $actor,
            'menoredad' => 0, 'alergias' => 'NINGUNA - DATO FICTICIO QA', 'deleted_at' => null,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]));
    }

    private function upsertQaDiagnosis(string $actor, $now): void
    {
        $parent = DB::table('cie_10_3')->where('id', 'QA0')->first();
        $child = DB::table('cie_10_4')->where('id', 'QA00')->first();
        if (($parent && ($parent->descripcion ?? '') !== 'DIAGNOSTICO ODONTOLOGICO FICTICIO QA')
            || ($child && ($child->descripcion ?? '') !== 'CONTROL PREVENTIVO FICTICIO QA')) {
            throw new RuntimeException('Los codigos QA0/QA00 ya existen y no pertenecen a los datos de prueba.');
        }

        DB::table('cie_10_3')->updateOrInsert(['id' => 'QA0'], $this->onlyColumns('cie_10_3', [
            'descripcion' => 'DIAGNOSTICO ODONTOLOGICO FICTICIO QA', 'estado' => 1, 'id_especialidad' => 3,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]));
        DB::table('cie_10_4')->updateOrInsert(['id' => 'QA00'], $this->onlyColumns('cie_10_4', [
            'descripcion' => 'CONTROL PREVENTIVO FICTICIO QA', 'id_cie_10_3' => 'QA0', 'estado' => 1,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]));
    }

    private function upsertCatalogProcedure(string $name, string $category, $specialty, string $price, string $actor, $now): int
    {
        $procedure = DB::table('procedimiento')->where('nombre', $name)->first();
        $values = $this->onlyColumns('procedimiento', [
            'nombre' => $name, 'observacion' => $category, 'estado' => 1, 'cardiologia' => 0,
            'id_especialidad' => $specialty, 'precio' => $price, 'deleted_at' => null,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        if ($procedure) {
            DB::table('procedimiento')->where('id', $procedure->id)->update($values);

            return (int) $procedure->id;
        }

        return (int) DB::table('procedimiento')->insertGetId($values);
    }

    private function upsertAgenda(string $patient, string $doctor, string $company, $insurance, $room, $specialty, int $procedure, string $marker, string $start, string $actor, $now): int
    {
        $agenda = DB::table('agenda')->where('observaciones', $marker)->first();
        $values = $this->onlyColumns('agenda', [
            'fechaini' => $start, 'fechafin' => date('Y-m-d H:i:s', strtotime($start.' +45 minutes')),
            'id_paciente' => $patient, 'id_doctor1' => $doctor, 'proc_consul' => 0,
            'id_sala' => $room ?: null, 'id_procedimiento' => $procedure, 'tipo_cita' => 0,
            'estado_cita' => 4, 'id_empresa' => $company, 'cortesia' => 'NO',
            'observaciones' => $marker, 'est_amb_hos' => 0, 'espid' => $specialty,
            'id_seguro' => $insurance, 'estado' => 1,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        if ($agenda) {
            DB::table('agenda')->where('id', $agenda->id)->update($values);

            return (int) $agenda->id;
        }

        return (int) DB::table('agenda')->insertGetId($values);
    }

    private function upsertHistory(int $agenda, string $patient, $insurance, string $doctor, string $actor, $now): int
    {
        $history = DB::table('historiaclinica')->where('id_agenda', $agenda)->first();
        $values = $this->onlyColumns('historiaclinica', [
            'id_agenda' => $agenda, 'id_paciente' => $patient, 'id_seguro' => $insurance,
            'id_doctor1' => $doctor, 'estado' => 1, 'deleted_at' => null,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        if ($history) {
            DB::table('historiaclinica')->where('hcid', $history->hcid)->update($values);

            return (int) $history->hcid;
        }

        return (int) DB::table('historiaclinica')->insertGetId($values);
    }

    private function dentalPayload(int $history, int $agenda, int $procedure, string $doctor): array
    {
        return [
            'metadata' => ['sistema' => 'AITROL BORJA', 'formulario' => 'SNS-MSP/HCU-form.033/2021',
                'hcid' => $history, 'id_agenda' => $agenda, 'id_paciente' => self::DENTAL_PATIENT],
            'bloques' => ['motivo_consulta' => 'Control preventivo ficticio QA', 'tipo_atencion' => 'Primera'],
            'antecedentes' => ['personales' => [], 'familiares' => []],
            'signos_vitales' => ['presion_arterial' => '120/80', 'frecuencia_cardiaca' => '72',
                'temperatura' => '36.5', 'frecuencia_respiratoria' => '16', 'alerta' => 0],
            'examen_estomatognatico' => [],
            'odontograma' => ['piezas' => ['11' => [
                'superficies' => ['V' => ['condicion' => 'obturado', 'color' => 'azul'],
                    'L' => null, 'M' => null, 'D' => null, 'O' => null],
                'marca' => '', 'color' => '', 'movilidad' => '', 'recesion' => '',
                'detalles' => [['fecha' => '2026-09-22', 'cara' => 'V', 'nombre_cara' => 'Vestibular',
                    'id_procedimiento' => (string) $procedure,
                    'diagnosticos' => [['cie10' => 'QA00', 'descripcion' => 'Control preventivo ficticio',
                        'id_procedimiento' => (string) $procedure]]]]]]],
            'indicadores' => ['ihos' => ['piezas' => [], 'totales' => []],
                'periodontal' => '', 'oclusion' => '', 'fluorosis' => ''],
            'indices' => ['cpod' => ['c' => 0, 'p' => 0, 'o' => 1, 'total' => 1],
                'ceod' => ['c' => 0, 'e' => 0, 'o' => 0, 'total' => 0]],
            'examenes_complementarios' => [], 'diagnosticos' => [], 'evoluciones' => [],
            'profesional' => ['id_profesional' => $doctor],
        ];
    }

    private function periodontalPayload(): array
    {
        $site = ['probing_depth' => '4', 'gingival_margin' => '-1', 'bop' => true,
            'plaque' => true, 'furcation' => '', 'implant' => false];
        $normal = ['probing_depth' => '2', 'gingival_margin' => '0', 'bop' => false,
            'plaque' => false, 'furcation' => '', 'implant' => false];

        return ['piezas' => ['16' => [
            'movilidad' => '1', 'pronostico' => 'Reservado',
            'V' => ['M' => $site, 'C' => $normal, 'D' => $normal],
            'L' => ['M' => $normal, 'C' => $normal, 'D' => $normal],
        ]]];
    }

    private function onlyColumns(string $table, array $values): array
    {
        $columns = array_flip(Schema::getColumnListing($table));

        return array_intersect_key($values, $columns);
    }
}
