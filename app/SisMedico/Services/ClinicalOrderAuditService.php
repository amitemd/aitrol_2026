<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalOrderAuditService
{
    public function available()
    {
        return app(ClinicalOrderService::class)->available();
    }

    public function inspect()
    {
        if (!$this->available()) {
            return array('available' => false, 'pending' => null, 'checks' => array());
        }

        $checks = array(
            'laboratory_orders_without_cycle' => $this->missingHospitalOrders('ho_orden_examenes', 'id_examen_orden', 'examen_orden'),
            'procedure_orders_without_cycle' => $this->missingHospitalOrders('ho_ordenes', 'clave_tipo', 'orden'),
            'ambulatory_laboratory_orders_without_cycle' => $this->missingAmbulatoryLaboratoryOrders(),
            'ambulatory_procedure_orders_without_cycle' => $this->missingAmbulatoryProcedureOrders(),
            'laboratory_results_without_cycle' => $this->missingLaboratoryResults(),
            'procedure_protocols_without_cycle' => $this->missingProcedureProtocols(),
            'orders_without_valid_encounter' => $this->ordersWithoutEncounter(),
            'orders_with_patient_mismatch' => $this->ordersWithPatientMismatch(),
            'orders_without_audit_event' => $this->ordersWithoutAuditEvent(),
            'orders_with_invalid_state' => $this->ordersWithInvalidState(),
            'results_without_valid_order' => $this->resultsWithoutValidOrder(),
            'final_results_without_completed_order' => $this->finalResultsWithoutCompletedOrder(),
            'invalid_verified_results' => $this->invalidVerifiedResults(),
            'critical_results_without_alert' => $this->criticalResultsWithoutAlert(),
            'invalid_critical_notifications' => $this->invalidCriticalNotifications(),
            'critical_alerts_overdue_without_escalation' => $this->overdueCriticalNotifications(),
            'critical_notifications_without_events' => $this->criticalNotificationsWithoutEvents(),
            'critical_escalation_recipient_unconfigured' => trim((string) config('clinical.critical_result_escalation_recipient')) === '' ? 1 : 0,
        );

        return array('available' => true, 'pending' => array_sum($checks), 'checks' => $checks);
    }

    public function repair()
    {
        if (!$this->available()) return $this->inspect();
        app(ClinicalOrderService::class)->backfill();
        $this->repairVerifiedResults();
        $this->repairCriticalAlerts();
        return $this->inspect();
    }

    private function missingHospitalOrders($linkTable, $legacyKey, $sourceType)
    {
        if (!Schema::hasTable($linkTable) || !Schema::hasTable('ho_solicitud')) return 0;
        return DB::table($linkTable . ' as link')
            ->join('ho_solicitud as hs', 'hs.id', '=', 'link.id_ho_solicitud')
            ->whereNotNull('hs.encounter_id')
            ->whereNotExists(function ($query) use ($legacyKey, $sourceType) {
                $query->select(DB::raw(1))->from('clinical_orders as co')
                    ->where('co.source_type', $sourceType)
                    ->whereRaw('co.source_id = CAST(link.' . $legacyKey . ' AS CHAR)');
            })->count();
    }

    private function missingLaboratoryResults()
    {
        if (!Schema::hasTable('examen_resultado')) return 0;
        return DB::table('examen_resultado as er')
            ->join('clinical_orders as co', function ($join) {
                $join->on('co.source_id', '=', DB::raw('CAST(er.id_orden AS CHAR)'))
                    ->where('co.source_type', '=', 'examen_orden');
            })->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('clinical_results as cr')
                    ->where('cr.source_type', 'examen_resultado')
                    ->whereRaw('cr.source_id = CAST(er.id AS CHAR)');
            })->count();
    }

    private function missingAmbulatoryLaboratoryOrders()
    {
        if (!Schema::hasTable('examen_orden') || !Schema::hasColumn('examen_orden', 'id_agenda')
            || !Schema::hasColumn('agenda', 'encounter_id')) return 0;
        return DB::table('examen_orden as eo')->join('agenda as a', 'a.id', '=', 'eo.id_agenda')
            ->whereNotNull('a.encounter_id')->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('clinical_orders as co')
                    ->where('co.source_type', 'examen_orden')->whereRaw('co.source_id = CAST(eo.id AS CHAR)');
            })->count();
    }

    private function missingAmbulatoryProcedureOrders()
    {
        if (!Schema::hasTable('orden') || !Schema::hasTable('hc_evolucion')
            || !Schema::hasColumn('historiaclinica', 'encounter_id')) return 0;
        return DB::table('orden as o')->join('hc_evolucion as he', 'he.id', '=', 'o.id_evolucion')
            ->join('historiaclinica as hc', 'hc.hcid', '=', 'he.hcid')->whereNotNull('hc.encounter_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('clinical_orders as co')
                    ->where('co.source_type', 'orden')->whereRaw('co.source_id = CAST(o.id AS CHAR)');
            })->count();
    }

    private function ordersWithoutEncounter()
    {
        if (!Schema::hasTable('clinical_encounters')) return DB::table('clinical_orders')->count();
        return DB::table('clinical_orders as co')->leftJoin('clinical_encounters as ce', 'ce.id', '=', 'co.encounter_id')
            ->whereNull('ce.id')->count();
    }

    private function missingProcedureProtocols()
    {
        if (!Schema::hasTable('hc_protocolo') || !Schema::hasTable('hc_procedimientos')
            || !Schema::hasColumn('historiaclinica', 'encounter_id')) return 0;
        $protocols = DB::table('hc_protocolo as hp')->join('hc_procedimientos as proc', 'proc.id', '=', 'hp.id_hc_procedimientos')
            ->join('historiaclinica as hc', 'hc.hcid', '=', 'proc.id_hc')->whereNotNull('hc.encounter_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('clinical_results as cr')
                    ->where('cr.source_type', 'hc_protocolo')
                    ->whereRaw("cr.source_id LIKE CONCAT(CAST(hp.id AS CHAR), '-%')");
            })->select('hp.id', 'hp.hallazgos', 'hp.conclusion', 'hp.complicaciones', 'hp.estado_final')->get();

        $pending = 0;
        foreach ($protocols as $protocol) {
            foreach (array('hallazgos', 'conclusion', 'complicaciones', 'estado_final') as $column) {
                $value = trim(str_replace('&nbsp;', ' ', strip_tags((string) $protocol->{$column})));
                if ($value !== '') {
                    $pending++;
                    break;
                }
            }
        }
        return $pending;
    }

    private function criticalResultsWithoutAlert()
    {
        return DB::table('clinical_results as cr')
            ->leftJoin('clinical_critical_result_notifications as cn', 'cn.clinical_result_id', '=', 'cr.id')
            ->where('cr.is_critical', 1)->whereNull('cn.id')->count();
    }

    private function ordersWithPatientMismatch()
    {
        return DB::table('clinical_orders as co')->join('clinical_encounters as ce', 'ce.id', '=', 'co.encounter_id')
            ->whereRaw('co.patient_id <> ce.id_paciente')->count();
    }

    private function ordersWithoutAuditEvent()
    {
        return DB::table('clinical_orders as co')->leftJoin('clinical_order_events as oe', 'oe.clinical_order_id', '=', 'co.id')
            ->whereNull('oe.id')->count();
    }

    private function ordersWithInvalidState()
    {
        return DB::table('clinical_orders')->whereNotIn('status', array('DRAFT','ORDERED','ACKNOWLEDGED','IN_PROGRESS','COMPLETED','CANCELLED'))
            ->orWhere(function ($query) {
                $query->where('status', 'COMPLETED')->whereNull('completed_at');
            })->orWhere(function ($query) {
                $query->where('status', 'CANCELLED')->where(function ($cancelled) {
                    $cancelled->whereNull('cancel_reason')->orWhere('cancel_reason', '');
                });
            })->count();
    }

    private function resultsWithoutValidOrder()
    {
        return DB::table('clinical_results as cr')->leftJoin('clinical_orders as co', 'co.id', '=', 'cr.clinical_order_id')
            ->whereNull('co.id')->count();
    }

    private function finalResultsWithoutCompletedOrder()
    {
        return DB::table('clinical_results as cr')->join('clinical_orders as co', 'co.id', '=', 'cr.clinical_order_id')
            ->whereIn('cr.status', array('FINAL','AMENDED'))->where('co.status', '<>', 'COMPLETED')->count();
    }

    private function invalidVerifiedResults()
    {
        return DB::table('clinical_results')->where(function ($query) {
            $query->whereNull('summary')->orWhere('summary', '')->orWhereNull('occurred_at')
                ->orWhereNotIn('status', array('PRELIMINARY','FINAL','AMENDED'));
        })->orWhere(function ($query) {
            $query->whereIn('status', array('FINAL','AMENDED'))->where(function ($verified) {
                $verified->whereNull('verified_by')->orWhereNull('verified_at');
            });
        })->orWhere(function ($query) {
            $query->where('is_critical', 1)->where(function ($critical) {
                $critical->whereNull('critical_reason')->orWhereRaw('CHAR_LENGTH(TRIM(critical_reason)) < 5');
            });
        })->count();
    }

    private function repairVerifiedResults()
    {
        $results = DB::table('clinical_results as cr')
            ->join('clinical_orders as co', 'co.id', '=', 'cr.clinical_order_id')
            ->whereIn('cr.status', array('FINAL', 'AMENDED'))
            ->where(function ($query) {
                $query->whereNull('cr.verified_by')->orWhereNull('cr.verified_at');
            })->select('cr.id', 'cr.verified_by', 'cr.verified_at', 'cr.occurred_at', 'cr.updated_at', 'cr.created_at', 'co.ordered_by')
            ->orderBy('cr.id')->get();

        foreach ($results as $result) {
            DB::table('clinical_results')->where('id', $result->id)->update(array(
                'verified_by' => $result->verified_by ?: 'SYSTEM_ORDER_AUDIT',
                'verified_at' => $result->verified_at ?: ($result->occurred_at ?: ($result->updated_at ?: ($result->created_at ?: date('Y-m-d H:i:s')))),
            ));
        }
    }

    private function invalidCriticalNotifications()
    {
        return DB::table('clinical_critical_result_notifications as cn')
            ->leftJoin('clinical_results as cr', 'cr.id', '=', 'cn.clinical_result_id')
            ->where(function ($query) {
                $query->whereNull('cr.id')->orWhere('cr.is_critical', '<>', 1)->orWhereNull('cn.due_at')
                    ->orWhereNotIn('cn.status', array('PENDING','NOTIFIED','ESCALATED','ACKNOWLEDGED'))
                    ->orWhere(function ($notified) {
                        $notified->whereIn('cn.status', array('NOTIFIED','ACKNOWLEDGED'))
                            ->where(function ($fields) {
                                $fields->whereNull('cn.recipient_name')->orWhere('cn.recipient_name', '')
                                    ->orWhereNull('cn.recipient_role')->orWhere('cn.recipient_role', '')
                                    ->orWhereNull('cn.notified_at')->orWhereNull('cn.notified_by');
                            });
                    })->orWhere(function ($ack) {
                        $ack->where('cn.status', 'ACKNOWLEDGED')->where(function ($fields) {
                            $fields->whereNull('cn.acknowledged_at')->orWhereNull('cn.acknowledged_by');
                        });
                    })->orWhere(function ($escalated) {
                        $escalated->where('cn.status', 'ESCALATED')->where(function ($fields) {
                            $fields->whereNull('cn.escalated_at')->orWhereNull('cn.escalated_to');
                        });
                    });
            })->count();
    }

    private function overdueCriticalNotifications()
    {
        return DB::table('clinical_critical_result_notifications')->whereIn('status', array('PENDING','NOTIFIED'))
            ->where('due_at', '<=', date('Y-m-d H:i:s'))->count();
    }

    private function criticalNotificationsWithoutEvents()
    {
        $pending = 0;
        $notifications = DB::table('clinical_critical_result_notifications')->get();
        foreach ($notifications as $notification) {
            $types = DB::table('clinical_critical_result_events')->where('notification_id', $notification->id)
                ->pluck('event_type')->all();
            if (!in_array('ALERT_CREATED', $types, true)) { $pending++; continue; }
            if (in_array($notification->status, array('NOTIFIED','ACKNOWLEDGED'), true) && !in_array('NOTIFIED', $types, true)) { $pending++; continue; }
            if ($notification->status === 'ESCALATED' && !in_array('ESCALATED', $types, true)) { $pending++; continue; }
            if ($notification->status === 'ACKNOWLEDGED' && !in_array('ACKNOWLEDGED', $types, true)) $pending++;
        }
        return $pending;
    }

    private function repairCriticalAlerts()
    {
        $ids = DB::table('clinical_results as cr')
            ->leftJoin('clinical_critical_result_notifications as cn', 'cn.clinical_result_id', '=', 'cr.id')
            ->where('cr.is_critical', 1)->whereNull('cn.id')->pluck('cr.id')->all();
        foreach ($ids as $id) {
            $result = \Sis_medico\ClinicalResult::find($id);
            if ($result) app(ClinicalOrderService::class)->ensureCriticalAlert($result, array('actor_id' => 'SYSTEM_ORDER_AUDIT', 'ip' => '127.0.0.1'));
        }
    }
}
