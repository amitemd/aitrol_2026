<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Sis_medico\Services\ClinicalEncounterService;

class LocalClinicalFullFlowSeeder extends Seeder
{
    private $actor = '0000000001';

    private $doctor = '0000000003';

    private $patient = 'QA00000000002';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('LocalClinicalFullFlowSeeder solo puede ejecutarse en local o testing.');
        }

        $company = (string) DB::table('empresa')->where('estado', 1)->value('id');
        $encounter = app(ClinicalEncounterService::class)->openOrReuse(
            $this->patient,
            ClinicalEncounterService::INPATIENT,
            [
                'actor_id' => $this->actor,
                'id_empresa' => $company,
                'id_hospital' => 1,
                'ip' => '127.0.0.1',
                'started_at' => now()->subHours(6),
            ]
        );

        DB::transaction(function () use ($encounter) {
            $routineOrder = $this->order($encounter->id, 'QA-FLOW-LAB-001', 'LABORATORY',
                'Hemograma completo QA', 'COMPLETED', 'ROUTINE');
            $this->result($routineOrder, 'QA-FLOW-RESULT-001', 'FINAL',
                'Hemoglobina 14.2 g/dL; leucocitos 7.100/mm3. Resultado ficticio normal.', false);

            $criticalOrder = $this->order($encounter->id, 'QA-FLOW-LAB-CRITICAL-001', 'LABORATORY',
                'Potasio sérico QA', 'COMPLETED', 'STAT');
            $criticalResult = $this->result($criticalOrder, 'QA-FLOW-RESULT-CRITICAL-001', 'FINAL',
                'Potasio 6.8 mmol/L. RESULTADO FICTICIO PARA PRUEBA DE ALERTA.', true);
            $this->criticalNotification($criticalResult);

            $this->medication($encounter->id);
            $this->safetyEvent($encounter->id);
            $this->accessControl($encounter->id);
        });

        $this->command?->info('Flujo clínico QA creado: hospitalización, órdenes, resultados, alerta crítica, eMAR, seguridad y acceso excepcional.');
    }

    private function order(string $encounterId, string $sourceId, string $category, string $description,
        string $status, string $priority): int
    {
        $id = DB::table('clinical_orders')->where('source_type', 'qa_local')->where('source_id', $sourceId)->value('id');
        $values = [
            'encounter_id' => $encounterId, 'patient_id' => $this->patient, 'category' => $category,
            'description' => $description, 'priority' => $priority, 'status' => $status,
            'ordered_by' => $this->doctor, 'ordered_at' => now()->subHours(5),
            'responsible_id' => $this->doctor, 'due_at' => now()->subHours(4),
            'completed_at' => $status === 'COMPLETED' ? now()->subHours(3) : null, 'updated_at' => now(),
        ];
        if ($id) {
            DB::table('clinical_orders')->where('id', $id)->update($values);
        } else {
            $values += ['source_type' => 'qa_local', 'source_id' => $sourceId, 'created_at' => now()->subHours(5)];
            $id = DB::table('clinical_orders')->insertGetId($values);
        }
        DB::table('clinical_order_events')->updateOrInsert(
            ['clinical_order_id' => $id, 'event_type' => 'QA_FLOW_COMPLETED'],
            ['from_status' => 'ORDERED', 'to_status' => $status, 'reason' => 'Escenario local de certificación.',
                'actor_id' => $this->doctor, 'ip_address' => '127.0.0.1', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) $id;
    }

    private function result(int $orderId, string $sourceId, string $status, string $summary, bool $critical): int
    {
        DB::table('clinical_results')->updateOrInsert(
            ['source_type' => 'qa_local', 'source_id' => $sourceId],
            ['clinical_order_id' => $orderId, 'status' => $status, 'summary' => $summary,
                'payload_json' => json_encode(['qa' => true, 'environment' => 'local']),
                'is_critical' => $critical, 'critical_reason' => $critical ? 'Valor crítico ficticio QA' : null,
                'verified_by' => $this->doctor, 'verified_at' => now()->subHours(3),
                'occurred_at' => now()->subHours(3), 'created_at' => now()->subHours(3), 'updated_at' => now()]
        );

        return (int) DB::table('clinical_results')->where('source_type', 'qa_local')->where('source_id', $sourceId)->value('id');
    }

    private function criticalNotification(int $resultId): void
    {
        $id = DB::table('clinical_critical_result_notifications')->where('clinical_result_id', $resultId)->value('id');
        $values = ['status' => 'ACKNOWLEDGED', 'recipient_id' => $this->doctor,
            'recipient_name' => 'MEDICO QA', 'recipient_role' => 'MEDICO TRATANTE', 'due_at' => now()->subHours(2),
            'notified_at' => now()->subHours(2)->subMinutes(15), 'notified_by' => $this->actor,
            'acknowledged_at' => now()->subHours(2), 'acknowledged_by' => $this->doctor,
            'attempts' => 1, 'notes' => 'Alerta ficticia notificada y acusada en el piloto local.',
            'ip_address' => '127.0.0.1', 'updated_at' => now()];
        if ($id) {
            DB::table('clinical_critical_result_notifications')->where('id', $id)->update($values);
        } else {
            $values['clinical_result_id'] = $resultId;
            $values['created_at'] = now()->subHours(2);
            $id = DB::table('clinical_critical_result_notifications')->insertGetId($values);
        }
        foreach (['ALERT_CREATED', 'NOTIFIED', 'ACKNOWLEDGED'] as $type) {
            DB::table('clinical_critical_result_events')->updateOrInsert(
                ['notification_id' => $id, 'event_type' => $type],
                ['recipient_id' => $this->doctor, 'recipient_name' => 'MEDICO QA',
                    'recipient_role' => 'MEDICO TRATANTE', 'actor_id' => $this->actor,
                    'occurred_at' => now()->subHours(2), 'notes' => 'Trazabilidad QA '.$type,
                    'ip_address' => '127.0.0.1', 'created_at' => now()->subHours(2), 'updated_at' => now()]
            );
        }
    }

    private function medication(string $encounterId): void
    {
        DB::table('clinical_medication_orders')->updateOrInsert(
            ['source_type' => 'qa_local', 'source_id' => 'QA-FLOW-MED-001'],
            ['encounter_id' => $encounterId, 'patient_id' => $this->patient,
                'medication_name' => 'AMOXICILINA QA 500 MG', 'ordered_dose' => '500 mg',
                'ordered_route' => 'ORAL', 'status' => 'ACTIVE', 'ordered_by' => $this->doctor,
                'ordered_at' => now()->subHours(4), 'created_at' => now()->subHours(4), 'updated_at' => now()]
        );
        $orderId = DB::table('clinical_medication_orders')->where('source_type', 'qa_local')->where('source_id', 'QA-FLOW-MED-001')->value('id');
        $scheduled = now()->startOfMinute()->subHours(2);
        DB::table('clinical_medication_administrations')->updateOrInsert(
            ['medication_order_id' => $orderId, 'scheduled_at' => $scheduled],
            ['encounter_id' => $encounterId, 'patient_id' => $this->patient,
                'medication_name' => 'AMOXICILINA QA 500 MG', 'dose' => '500 mg', 'route' => 'ORAL',
                'administered_at' => $scheduled->copy()->addMinutes(3), 'status' => 'ADMINISTERED',
                'clinical_result' => 'Administración tolerada; registro ficticio QA.', 'inventory_series' => 'QA-LOTE-001',
                'patient_verified' => 1, 'medication_verified' => 1, 'dose_verified' => 1,
                'route_verified' => 1, 'time_verified' => 1, 'administered_by' => $this->actor,
                'ip_address' => '127.0.0.1', 'created_at' => now()->subHours(2), 'updated_at' => now()]
        );
    }

    private function safetyEvent(string $encounterId): void
    {
        $id = DB::table('clinical_safety_events')->where('encounter_id', $encounterId)->where('subtype', 'QA_CAIDA_SIMULADA')->value('id');
        $values = ['patient_id' => $this->patient, 'category' => 'PATIENT_SAFETY', 'subtype' => 'QA_CAIDA_SIMULADA',
            'severity' => 'LOW', 'occurred_at' => now()->subHours(3), 'detected_at' => now()->subHours(3),
            'location' => 'HAB-QA-01', 'description' => 'Evento ficticio sin daño para probar el circuito.',
            'immediate_actions' => 'Valoración ficticia, barreras preventivas y notificación.', 'status' => 'CLOSED',
            'assigned_to' => $this->doctor, 'notification_required' => 0, 'review_due_at' => now()->addDay(),
            'root_cause' => 'Escenario controlado de certificación.', 'corrective_actions' => 'Revisión de barreras y educación QA.',
            'closed_at' => now()->subHour(), 'closed_by' => $this->doctor, 'created_by' => $this->actor,
            'ip_address' => '127.0.0.1', 'updated_at' => now()];
        if ($id) {
            DB::table('clinical_safety_events')->where('id', $id)->update($values);
        } else {
            $values['encounter_id'] = $encounterId;
            $values['created_at'] = now()->subHours(3);
            $id = DB::table('clinical_safety_events')->insertGetId($values);
        }
        DB::table('clinical_safety_event_logs')->updateOrInsert(
            ['clinical_safety_event_id' => $id, 'event_type' => 'STATUS_CHANGED'],
            ['from_status' => 'OPEN', 'to_status' => 'CLOSED', 'detail' => 'Cierre controlado del escenario QA.',
                'actor_id' => $this->doctor, 'ip_address' => '127.0.0.1', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function accessControl(string $encounterId): void
    {
        DB::table('clinical_access_scopes')->updateOrInsert(
            ['user_id' => $this->doctor, 'encounter_id' => $encounterId, 'action' => 'orders.view'],
            ['care_setting' => 'INPATIENT', 'service_code' => 'QA', 'valid_from' => now()->subDay(),
                'valid_until' => now()->addDays(7), 'active' => 1, 'granted_by' => $this->actor,
                'reason' => 'Permiso contextual ficticio para certificación local.', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('clinical_break_glass_accesses')->updateOrInsert(
            ['encounter_id' => $encounterId, 'user_id' => $this->doctor, 'action' => 'results.view'],
            ['reason' => 'Urgencia clínica simulada con justificación suficiente para la prueba local.',
                'status' => 'REVIEWED', 'activated_at' => now()->subHours(4), 'expires_at' => now()->subHours(3),
                'review_due_at' => now()->subHours(2), 'reviewed_by' => $this->actor, 'reviewed_at' => now()->subHours(2),
                'review_decision' => 'APPROVED', 'review_notes' => 'Uso ficticio validado durante el piloto.',
                'ip_address' => '127.0.0.1', 'created_at' => now()->subHours(4), 'updated_at' => now()]
        );
        $accessId = DB::table('clinical_break_glass_accesses')->where('encounter_id', $encounterId)
            ->where('user_id', $this->doctor)->where('action', 'results.view')->value('id');
        $eventKey = ['encounter_id' => $encounterId, 'user_id' => $this->doctor,
            'action' => 'results.view', 'decision' => 'ACTIVATED'];
        if (Schema::hasColumn('clinical_access_events', 'break_glass_access_id')) {
            $eventKey = ['break_glass_access_id' => $accessId, 'decision' => 'ACTIVATED'];
        }
        DB::table('clinical_access_events')->updateOrInsert($eventKey, [
            'encounter_id' => $encounterId, 'user_id' => $this->doctor, 'action' => 'results.view',
            'decision' => 'ACTIVATED', 'source' => 'BREAK_GLASS',
            'detail' => 'Activación ficticia con revisión independiente completada.',
            'ip_address' => '127.0.0.1', 'created_at' => now()->subHours(4), 'updated_at' => now(),
        ]);
    }
}
