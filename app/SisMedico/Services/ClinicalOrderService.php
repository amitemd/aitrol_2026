<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalCriticalResultNotification;
use Sis_medico\ClinicalCriticalResultEvent;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalOrder;
use Sis_medico\ClinicalOrderEvent;
use Sis_medico\ClinicalResult;
use Sis_medico\Exceptions\ClinicalOrderTransitionException;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Resultado;
use Sis_medico\Ho_Orden_Examenes;
use Sis_medico\Ho_Ordenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\Orden;

class ClinicalOrderService
{
    const DRAFT = 'DRAFT';
    const ORDERED = 'ORDERED';
    const ACKNOWLEDGED = 'ACKNOWLEDGED';
    const IN_PROGRESS = 'IN_PROGRESS';
    const COMPLETED = 'COMPLETED';
    const CANCELLED = 'CANCELLED';

    public function available()
    {
        return Schema::hasTable('clinical_orders') && Schema::hasTable('clinical_order_events')
            && Schema::hasTable('clinical_results') && Schema::hasTable('clinical_critical_result_notifications')
            && Schema::hasTable('clinical_critical_result_events');
    }

    public function backfill()
    {
        $stats = array('orders' => 0, 'results' => 0);
        if (!$this->available()) return $stats;
        Ho_Solicitud::whereNotNull('encounter_id')->orderBy('id')->chunk(100, function ($rows) use (&$stats) {
            foreach ($rows as $solicitud) $stats['orders'] += count($this->syncSolicitud($solicitud->id));
        });
        Examen_Orden::orderBy('id')->chunk(250, function ($rows) use (&$stats) {
            foreach ($rows as $order) if ($this->syncStandaloneOrder($order, 'LABORATORY')) $stats['orders']++;
        });
        Orden::orderBy('id')->chunk(250, function ($rows) use (&$stats) {
            foreach ($rows as $order) if ($this->syncStandaloneOrder($order, $this->categoryFromOrder($order))) $stats['orders']++;
        });
        hc_protocolo::orderBy('id')->chunk(100, function ($rows) use (&$stats) {
            foreach ($rows as $protocol) if ($this->syncProcedureProtocol($protocol)) $stats['results']++;
        });
        Examen_Resultado::orderBy('id')->chunk(250, function ($rows) use (&$stats) {
            foreach ($rows as $result) if ($this->syncLegacyLabResult($result)) $stats['results']++;
        });
        return $stats;
    }

    public function syncSolicitud($solicitudId, array $context = array())
    {
        if (!$this->available()) return array();
        $solicitud = Ho_Solicitud::find($solicitudId);
        if (!$solicitud || !$solicitud->encounter_id) return array();
        $synced = array();

        foreach (Ho_Orden_Examenes::where('id_ho_solicitud', $solicitudId)->get() as $link) {
            $legacy = Examen_Orden::find($link->id_examen_orden);
            if ($legacy) $synced[] = $this->syncLegacyOrder($solicitud, 'LABORATORY', $legacy, $context);
        }
        foreach (Ho_Ordenes::where('id_ho_solicitud', $solicitudId)->where('tipo', '<>', 'INTERCONSULTA')->get() as $link) {
            $legacy = Orden::find($link->clave_tipo);
            if ($legacy) {
                $category = $this->category($link->tipo);
                if ($category === 'PROCEDURE') $category = $this->categoryFromOrder($legacy);
                $synced[] = $this->syncLegacyOrder($solicitud, $category, $legacy, $context);
            }
        }
        return array_values(array_filter($synced));
    }

    public function syncStandaloneOrder($legacy, $category = null, array $context = array())
    {
        if (!$this->available() || !$legacy) return null;
        $sourceType = $legacy instanceof Examen_Orden ? 'examen_orden' : 'orden';
        $existing = ClinicalOrder::where('source_type', $sourceType)->where('source_id', (string) $legacy->id)->first();
        if ($existing) {
            $source = (object) array('encounter_id' => $existing->encounter_id, 'id_paciente' => $existing->patient_id);
            return $this->syncLegacyOrder($source, $category ?: $existing->category, $legacy, $context);
        }

        $encounter = $this->resolveEncounter($legacy);
        if (!$encounter) return null;
        $source = (object) array('encounter_id' => $encounter->id, 'id_paciente' => $legacy->id_paciente);
        return $this->syncLegacyOrder($source, $category ?: ($sourceType === 'examen_orden' ? 'LABORATORY' : $this->categoryFromOrder($legacy)), $legacy, $context);
    }

    public function syncLegacyLabResult(Examen_Resultado $legacyResult, array $context = array())
    {
        if (!$this->available()) return null;
        $order = ClinicalOrder::where('source_type', 'examen_orden')->where('source_id', (string) $legacyResult->id_orden)->first();
        if (!$order) {
            $link = Ho_Orden_Examenes::where('id_examen_orden', $legacyResult->id_orden)->first();
            if ($link) {
                $this->syncSolicitud($link->id_ho_solicitud, $context);
                $order = ClinicalOrder::where('source_type', 'examen_orden')->where('source_id', (string) $legacyResult->id_orden)->first();
            }
        }
        if (!$order) return null;

        $summary = isset($legacyResult->valor) ? $legacyResult->valor
            : (isset($legacyResult->resultado) ? $legacyResult->resultado : 'Resultado de laboratorio registrado');

        // Una orden completada es inmutable: cualquier dato que llegue desde el
        // circuito operativo queda como adenda, nunca como preliminar. La primera
        // sincronizacion conserva el id legado para la auditoria; las correcciones
        // posteriores generan una version estable sin sobrescribir la anterior.
        $baseSourceId = (string) $legacyResult->id;
        $baseResultExists = ClinicalResult::where('source_type', 'examen_resultado')
            ->where('source_id', $baseSourceId)->exists();
        $version = $legacyResult->updated_at ?: $legacyResult->created_at;
        $sourceId = $baseResultExists
            ? $baseSourceId . '-' . ($version ? $version->format('YmdHis') : '1')
            : $baseSourceId;
        $resultStatus = $order->status === self::COMPLETED ? 'AMENDED' : 'PRELIMINARY';

        return $this->recordResult($order, array(
            'source_type' => 'examen_resultado',
            'source_id' => $sourceId,
            'status' => $resultStatus,
            'summary' => (string) $summary,
            'occurred_at' => $legacyResult->created_at ?: Carbon::now(),
            'is_critical' => false,
        ), $context);
    }

    public function syncProcedureProtocol(hc_protocolo $protocol, array $context = array())
    {
        if (!$this->available()) return null;
        $procedure = hc_procedimientos::find($protocol->id_hc_procedimientos);
        if (!$procedure) return null;
        $history = Historiaclinica::find($procedure->id_hc);
        if (!$history || !$history->encounter_id) return null;

        $order = ClinicalOrder::firstOrNew(array('source_type' => 'hc_procedimientos', 'source_id' => (string) $procedure->id));
        if (!$order->exists) {
            $description = 'Procedimiento clínico #' . $procedure->id;
            if ($procedure->procedimiento_completo && $procedure->procedimiento_completo->nombre_general) {
                $description = $procedure->procedimiento_completo->nombre_general;
            }
            $order->fill(array(
                'encounter_id' => $history->encounter_id, 'patient_id' => $history->id_paciente,
                'category' => 'PROCEDURE', 'description' => $description, 'priority' => 'ROUTINE',
                'status' => self::IN_PROGRESS, 'ordered_by' => $procedure->id_doctor_examinador,
                'ordered_at' => $procedure->created_at ?: $protocol->created_at,
            ));
            $order->save();
            $this->event($order, 'PROCEDURE_IMPORTED', null, self::IN_PROGRESS,
                'Procedimiento vinculado desde la historia clínica.', $context);
        }

        $hallazgos = $this->protocolText($protocol->hallazgos);
        $conclusion = $this->protocolText($protocol->conclusion);
        $complicaciones = $this->protocolText($protocol->complicaciones);
        $estadoFinal = $this->protocolText($protocol->estado_final);
        $parts = array_filter(array(
            $hallazgos !== '' ? $hallazgos : null,
            $conclusion !== '' ? 'Conclusión: ' . $conclusion : null,
            $complicaciones !== '' ? 'Complicaciones: ' . $complicaciones : null,
            $estadoFinal !== '' ? 'Estado final: ' . $estadoFinal : null,
        ));
        if (!count($parts)) return $order;

        $version = $protocol->updated_at ?: $protocol->created_at;
        $sourceId = $protocol->id . '-' . ($version ? strtotime($version) : '1');
        $hasPrevious = ClinicalResult::where('clinical_order_id', $order->id)->where('source_type', 'hc_protocolo')->exists();
        return $this->recordResult($order, array(
            'source_type' => 'hc_protocolo', 'source_id' => $sourceId,
            'status' => $hasPrevious ? 'AMENDED' : 'FINAL', 'summary' => implode("\n", $parts),
            'occurred_at' => $protocol->fecha ?: $protocol->updated_at, 'is_critical' => false,
            'verified_by' => $procedure->id_doctor_examinador ?: ($order->ordered_by ?: 'SYSTEM_ORDER_AUDIT'),
            'verified_at' => $protocol->updated_at ?: $protocol->created_at,
        ), $context);
    }

    public function transition(ClinicalOrder $order, $toStatus, $reason, array $context = array())
    {
        return app(ClinicalOrderGuard::class)->run(function () use ($order, $toStatus, $reason, $context) {
            return DB::transaction(function () use ($order, $toStatus, $reason, $context) {
            $order = ClinicalOrder::lockForUpdate()->find($order->id);
            if (!$this->isAllowed($order->status, $toStatus)) {
                throw new ClinicalOrderTransitionException('Transición de orden no permitida: ' . $order->status . ' → ' . $toStatus . '.');
            }
            if ($toStatus === self::CANCELLED && strlen(trim((string) $reason)) < 5) {
                throw new ClinicalOrderTransitionException('La cancelación de una orden requiere un motivo.');
            }
            $this->assertLegacyOrderReady($order, $toStatus);
            $from = $order->status;
            $order->status = $toStatus;
            if ($toStatus === self::COMPLETED) $order->completed_at = Carbon::now();
            if ($toStatus === self::CANCELLED) $order->cancel_reason = trim($reason);
            $order->save();
            $this->syncLegacyTransition($order, $toStatus, $context);
            $this->event($order, 'STATUS_CHANGED', $from, $toStatus, $reason, $context);
            return $order;
            });
        });
    }

    private function assertLegacyOrderReady(ClinicalOrder $order, $toStatus)
    {
        if ($toStatus !== self::ORDERED || $order->source_type !== 'examen_orden') return;
        $legacy = Examen_Orden::lockForUpdate()->find($order->source_id);
        if (!$legacy) {
            throw new ClinicalOrderTransitionException('La orden de laboratorio de origen no existe.');
        }
        if ($legacy->detalles()->count() === 0) {
            throw new ClinicalOrderTransitionException('No se puede emitir una orden de laboratorio sin exámenes.');
        }
    }

    private function syncLegacyTransition(ClinicalOrder $order, $toStatus, array $context)
    {
        if ($order->source_type !== 'examen_orden') return;

        $legacyStatus = null;
        if ($toStatus === self::CANCELLED) {
            $legacyStatus = '0';
        } elseif (in_array($toStatus, array(self::ORDERED, self::ACKNOWLEDGED, self::IN_PROGRESS, self::COMPLETED), true)) {
            $legacyStatus = '1';
        }
        if (is_null($legacyStatus)) return;

        $updates = array(
            'estado' => $legacyStatus,
            'realizado' => $legacyStatus === '1' ? '1' : '0',
            'ip_modificacion' => $this->ip($context),
            'updated_at' => Carbon::now(),
        );
        $actorId = $this->actorId($context);
        if (!is_null($actorId) && $actorId !== '') $updates['id_usuariomod'] = $actorId;
        DB::table('examen_orden')->where('id', $order->source_id)->update($updates);
    }

    public function recordResult(ClinicalOrder $order, array $data, array $context = array())
    {
        if (!$this->available()) throw new ClinicalOrderTransitionException('La migración del ciclo de órdenes y resultados no está aplicada.');
        return app(ClinicalOrderGuard::class)->run(function () use ($order, $data, $context) {
            return DB::transaction(function () use ($order, $data, $context) {
            $order = ClinicalOrder::lockForUpdate()->find($order->id);
            if (!$order || $order->status === self::CANCELLED) {
                throw new ClinicalOrderTransitionException('No se puede registrar un resultado sobre una orden cancelada.');
            }
            $status = isset($data['status']) ? $data['status'] : 'PRELIMINARY';
            if (!in_array($status, array('PRELIMINARY', 'FINAL', 'AMENDED'), true)) {
                throw new ClinicalOrderTransitionException('Estado de resultado no permitido.');
            }
            if ($order->status === self::COMPLETED && $status !== 'AMENDED') {
                throw new ClinicalOrderTransitionException('Una orden completada solo admite una adenda al resultado previo.');
            }
            $critical = !empty($data['is_critical']);
            if ($critical && (!isset($data['critical_reason']) || strlen(trim($data['critical_reason'])) < 5)) {
                throw new ClinicalOrderTransitionException('Un resultado crítico requiere documentar el criterio clínico.');
            }
            $result = ClinicalResult::firstOrNew(array(
                'source_type' => $data['source_type'], 'source_id' => (string) $data['source_id'],
            ));
            $result->fill(array(
                'clinical_order_id' => $order->id,
                'status' => $status,
                'summary' => isset($data['summary']) ? trim($data['summary']) : null,
                'payload_json' => isset($data['payload']) ? json_encode($data['payload']) : null,
                'is_critical' => $critical,
                'critical_reason' => $critical ? trim($data['critical_reason']) : null,
                'verified_by' => isset($data['verified_by']) && $data['verified_by'] !== '' ? $data['verified_by']
                    : ($this->actorId($context) ?: ($order->ordered_by ?: 'SYSTEM_ORDER_AUDIT')),
                'verified_at' => in_array($status, array('FINAL', 'AMENDED'), true)
                    ? (isset($data['verified_at']) && $data['verified_at'] ? $data['verified_at'] : Carbon::now()) : null,
                'occurred_at' => isset($data['occurred_at']) ? $data['occurred_at'] : Carbon::now(),
            ));
            $result->save();

            if ($critical) $this->ensureCriticalAlert($result, $context);
            if (in_array($status, array('FINAL', 'AMENDED'), true) && $order->status !== self::COMPLETED) {
                $this->completeFromResult($order, $context);
            }
            return $result;
            });
        });
    }

    public function notifyCritical($notificationId, array $data, array $context = array())
    {
        return DB::transaction(function () use ($notificationId, $data, $context) {
            $notification = ClinicalCriticalResultNotification::lockForUpdate()->find($notificationId);
            if (!$notification || $notification->status === 'ACKNOWLEDGED') throw new ClinicalOrderTransitionException('La alerta crítica no está disponible.');
            if (empty($data['recipient_id']) && empty($data['recipient_name'])) throw new ClinicalOrderTransitionException('Identifique al receptor del resultado crítico.');
            if (empty($data['recipient_role'])) throw new ClinicalOrderTransitionException('Identifique el cargo o servicio receptor del resultado crítico.');
            $notification->recipient_id = isset($data['recipient_id']) ? $data['recipient_id'] : null;
            $notification->recipient_name = isset($data['recipient_name']) ? trim($data['recipient_name']) : null;
            $notification->recipient_role = isset($data['recipient_role']) ? trim($data['recipient_role']) : null;
            $notification->notified_at = Carbon::now();
            $notification->notified_by = $this->actorId($context);
            $notification->attempts = $notification->attempts + 1;
            $notification->notes = isset($data['notes']) ? trim($data['notes']) : null;
            $notification->status = 'NOTIFIED';
            $notification->save();
            $this->criticalEvent($notification, 'NOTIFIED', $this->actorId($context), $context);
            return $notification;
        });
    }

    public function acknowledgeCritical($notificationId, array $context = array())
    {
        return DB::transaction(function () use ($notificationId, $context) {
            $notification = ClinicalCriticalResultNotification::lockForUpdate()->find($notificationId);
            if (!$notification || !in_array($notification->status, array('NOTIFIED', 'ESCALATED'), true)) {
                throw new ClinicalOrderTransitionException('El resultado crítico debe notificarse antes del acuse.');
            }
            $notification->status = 'ACKNOWLEDGED';
            $notification->acknowledged_at = Carbon::now();
            $notification->acknowledged_by = $this->actorId($context);
            $notification->save();
            $this->criticalEvent($notification, 'ACKNOWLEDGED', $this->actorId($context), $context);
            return $notification;
        });
    }

    public function escalateOverdue($recipientId = null)
    {
        if (is_null($recipientId) || trim((string) $recipientId) === '') {
            throw new ClinicalOrderTransitionException('Configure CLINICAL_CRITICAL_ESCALATION_RECIPIENT antes de escalar alertas críticas.');
        }
        $count = 0;
        foreach (ClinicalCriticalResultNotification::whereIn('status', array('PENDING', 'NOTIFIED'))->where('due_at', '<=', Carbon::now())->get() as $notification) {
            $notification->status = 'ESCALATED';
            $notification->escalated_at = Carbon::now();
            $notification->escalated_to = $recipientId;
            $notification->save();
            $this->criticalEvent($notification, 'ESCALATED', 'SYSTEM_ESCALATION', array('ip' => '127.0.0.1'));
            $count++;
        }
        return $count;
    }

    public function ensureCriticalAlert(ClinicalResult $result, array $context = array())
    {
        $notification = ClinicalCriticalResultNotification::where('clinical_result_id', $result->id)->first();
        if ($notification) return $notification;
        $notification = ClinicalCriticalResultNotification::create(array(
            'clinical_result_id' => $result->id, 'status' => 'PENDING',
            'due_at' => Carbon::now()->addMinutes((int) config('clinical.critical_result_ack_minutes', 15)),
            'attempts' => 0, 'ip_address' => $this->ip($context),
        ));
        $this->criticalEvent($notification, 'ALERT_CREATED', $this->actorId($context), $context);
        return $notification;
    }

    private function syncLegacyOrder($solicitud, $category, $legacy, array $context)
    {
        $sourceType = $legacy instanceof Examen_Orden ? 'examen_orden' : 'orden';
        $legacyStatus = (string) $legacy->estado;
        $status = $legacyStatus === '0' ? self::CANCELLED : ($legacyStatus === '-1' ? self::DRAFT : self::ORDERED);
        $order = ClinicalOrder::firstOrNew(array('source_type' => $sourceType, 'source_id' => (string) $legacy->id));
        if (!$order->exists) {
            $order->fill(array(
                'encounter_id' => $solicitud->encounter_id,
                'patient_id' => $solicitud->id_paciente,
                'category' => $category,
                'description' => $category . ' #' . $legacy->id,
                'priority' => 'ROUTINE',
                'status' => $status,
                'ordered_by' => isset($legacy->id_doctor_ieced) ? $legacy->id_doctor_ieced : (isset($legacy->id_doctor) ? $legacy->id_doctor : null),
                'ordered_at' => isset($legacy->fecha_orden) ? $legacy->fecha_orden : $legacy->created_at,
            ));
            $order->save();
            $this->event($order, 'ORDER_IMPORTED', null, $status, 'Orden clínica sincronizada desde ' . $sourceType . '.', $context);
        } elseif ($order->category !== $category && (string) $order->encounter_id === (string) $solicitud->encounter_id) {
            $previousCategory = $order->category;
            app(ClinicalOrderGuard::class)->run(function () use ($order, $category) {
                $order->category = $category;
                $order->save();
            });
            $this->event($order, 'ORDER_CATEGORY_CORRECTED', $previousCategory, $category,
                'Clasificación corregida desde la orden de Emergencia.', $context);
        }
        if ($order->exists && $status === self::CANCELLED && $order->status !== self::CANCELLED) {
            app(ClinicalOrderGuard::class)->run(function () use ($order) {
                $order->status = self::CANCELLED;
                $order->cancel_reason = 'Cancelación sincronizada desde el módulo de origen.';
                $order->save();
            });
            $this->event($order, 'LEGACY_CANCELLATION_SYNCED', null, self::CANCELLED, $order->cancel_reason, $context);
        }
        return $order;
    }

    private function completeFromResult($order, array $context)
    {
        $order = ClinicalOrder::lockForUpdate()->find($order->id);
        $from = $order->status;
        $order->status = self::COMPLETED;
        $order->completed_at = Carbon::now();
        $order->save();
        $this->event($order, 'COMPLETED_BY_RESULT', $from, self::COMPLETED, 'Resultado final verificado.', $context);
    }

    private function isAllowed($from, $to)
    {
        $map = array(
            self::DRAFT => array(self::ORDERED, self::CANCELLED),
            self::ORDERED => array(self::ACKNOWLEDGED, self::IN_PROGRESS, self::COMPLETED, self::CANCELLED),
            self::ACKNOWLEDGED => array(self::IN_PROGRESS, self::COMPLETED, self::CANCELLED),
            self::IN_PROGRESS => array(self::COMPLETED, self::CANCELLED),
            self::COMPLETED => array(), self::CANCELLED => array(),
        );
        return isset($map[$from]) && in_array($to, $map[$from], true);
    }

    private function category($legacyType)
    {
        $type = strtoupper(trim((string) $legacyType));
        if (strpos($type, 'IMAGEN') !== false) return 'IMAGING';
        if (strpos($type, 'LAB') !== false) return 'LABORATORY';
        return 'PROCEDURE';
    }

    private function categoryFromOrder($order)
    {
        return isset($order->tipo_procedimiento) && (string) $order->tipo_procedimiento === '2'
            ? 'IMAGING' : 'PROCEDURE';
    }

    private function resolveEncounter($legacy)
    {
        if (isset($legacy->encounter_id) && $legacy->encounter_id) return ClinicalEncounter::find($legacy->encounter_id);

        if ($legacy instanceof Examen_Orden && isset($legacy->id_agenda) && $legacy->id_agenda
            && Schema::hasColumn('agenda', 'encounter_id')) {
            $encounterId = DB::table('agenda')->where('id', $legacy->id_agenda)->value('encounter_id');
            if ($encounterId) return ClinicalEncounter::find($encounterId);
        }
        if ($legacy instanceof Orden && $legacy->id_evolucion) {
            $evolution = Hc_Evolucion::find($legacy->id_evolucion);
            if ($evolution && $evolution->historiaclinica && $evolution->historiaclinica->encounter_id) {
                return ClinicalEncounter::find($evolution->historiaclinica->encounter_id);
            }
        }

        $orderedAt = isset($legacy->fecha_orden) && $legacy->fecha_orden ? $legacy->fecha_orden : $legacy->created_at;
        if (!$orderedAt || empty($legacy->id_paciente)) return null;
        $matches = ClinicalEncounter::where('id_paciente', $legacy->id_paciente)
            ->where('started_at', '<=', $orderedAt)
            ->where(function ($query) use ($orderedAt) {
                $query->whereNull('ended_at')->orWhere('ended_at', '>=', $orderedAt);
            })->orderBy('started_at', 'desc')->limit(2)->get();
        return $matches->count() === 1 ? $matches->first() : null;
    }

    private function event($order, $type, $from, $to, $reason, array $context)
    {
        ClinicalOrderEvent::create(array('clinical_order_id' => $order->id, 'event_type' => $type,
            'from_status' => $from, 'to_status' => $to, 'reason' => $reason,
            'actor_id' => $this->actorId($context), 'ip_address' => $this->ip($context)));
    }

    private function protocolText($value)
    {
        return trim(str_replace('&nbsp;', ' ', strip_tags((string) $value)));
    }

    private function criticalEvent($notification, $type, $actor, array $context)
    {
        ClinicalCriticalResultEvent::create(array(
            'notification_id' => $notification->id, 'event_type' => $type,
            'recipient_id' => $notification->recipient_id, 'recipient_name' => $notification->recipient_name,
            'recipient_role' => $notification->recipient_role, 'actor_id' => $actor,
            'occurred_at' => Carbon::now(), 'notes' => $notification->notes,
            'ip_address' => $this->ip($context),
        ));
    }

    private function actorId(array $context) { return isset($context['actor_id']) ? $context['actor_id'] : (Auth::check() ? Auth::user()->id : null); }
    private function ip(array $context) { return isset($context['ip']) ? $context['ip'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null); }
}
