<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalReadinessEvidence;

class ClinicalOperationsEvidenceService
{
    const MONITOR = 'OPERATIONS_MONITOR';
    const ALERT = 'OPERATIONS_ALERT';
    const AUDIT = 'CLINICAL_DAILY_AUDIT';
    const RESTORE = 'RESTORE_DRILL';
    const PILOT = 'CLINICAL_PILOT';
    const TRANSPORT_RENEWAL = 'TRANSPORT_RENEWAL';

    public function available()
    {
        try {
            return Schema::hasTable('clinical_readiness_evidence');
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function recordMonitor()
    {
        if (!$this->available()) {
            throw new \RuntimeException('Falta aplicar la migración de evidencia operativa clínica.');
        }

        $started = microtime(true);
        $issues = array();
        try {
            DB::select('select 1');
        } catch (\Exception $exception) {
            $issues[] = 'database_unavailable';
        }

        foreach (array('clinical_encounters', 'clinical_orders', 'clinical_access_events') as $table) {
            if (!Schema::hasTable($table)) $issues[] = 'missing_table:' . $table;
        }

        $free = @disk_free_space(storage_path());
        $total = @disk_total_space(storage_path());
        $freePercent = ($free !== false && $total) ? round(($free / $total) * 100, 2) : null;
        if (!is_null($freePercent) && $freePercent < 10) $issues[] = 'disk_below_10_percent';

        $responsible = trim((string) config('clinical.operations_responsible'));
        if ($responsible === '') $issues[] = 'operations_responsible_unconfigured';
        if (Schema::hasTable('failed_jobs')) {
            $failedJobs = DB::table('failed_jobs')->where('failed_at', '>=', date('Y-m-d H:i:s', time() - 600))->count();
            if ($failedJobs > 0) $issues[] = 'recent_failed_jobs:' . $failedJobs;
        }
        $recentLogErrors = $this->recentLogErrorCount(storage_path('logs/laravel.log'), 600);
        if ($recentLogErrors > 0) $issues[] = 'recent_log_errors:' . $recentLogErrors;

        $now = date('Y-m-d H:i:s');
        $evidence = DB::transaction(function () use ($issues, $freePercent, $recentLogErrors, $responsible, $now, $started) {
            $alert = count($issues) ? $this->openAlert($issues, $responsible, $now) : null;
            return ClinicalReadinessEvidence::create(array(
                'evidence_type' => self::MONITOR,
                'status' => count($issues) ? 'FAILED' : 'PASSED',
                'started_at' => $now,
                'completed_at' => $now,
                'duration_seconds' => max(0, (int) round(microtime(true) - $started)),
                'metrics' => json_encode(array('issues' => $issues, 'disk_free_percent' => $freePercent,
                    'recent_log_errors' => $recentLogErrors, 'alert_id' => $alert ? $alert->id : null)),
                'recorded_by' => 'scheduler',
                'notes' => count($issues) ? implode(', ', $issues) : 'Comprobación operativa satisfactoria.',
            ));
        }, 3);

        ClinicalReadinessEvidence::where('evidence_type', self::MONITOR)
            ->where('completed_at', '<', date('Y-m-d H:i:s', time() - (30 * 86400)))->delete();

        return $evidence;
    }

    public function recordCertification($type, array $data)
    {
        if (!$this->available()) throw new \RuntimeException('Falta aplicar la migración de evidencia operativa clínica.');
        if (!in_array($type, array(self::RESTORE, self::PILOT, self::TRANSPORT_RENEWAL), true)) throw new \InvalidArgumentException('Tipo de evidencia no permitido.');

        $path = realpath($data['evidence_path']);
        if ($path === false || !is_file($path)) throw new \InvalidArgumentException('El archivo de evidencia no existe.');
        if (empty($data['approved_by']) || empty($data['recorded_by'])) throw new \InvalidArgumentException('Registre responsable y aprobador independiente.');
        if ((string) $data['approved_by'] === (string) $data['recorded_by']) throw new \InvalidArgumentException('El aprobador debe ser distinto de quien registra la evidencia.');
        if (strlen(trim((string) $data['notes'])) < 20) throw new \InvalidArgumentException('Las conclusiones deben tener al menos 20 caracteres.');

        $started = strtotime($data['started_at']);
        $completed = strtotime($data['completed_at']);
        if (!$started || !$completed || $completed <= $started) throw new \InvalidArgumentException('El período de ejecución de la evidencia es inválido.');

        $metrics = isset($data['metrics']) ? $data['metrics'] : array();
        if ($type === self::RESTORE) {
            foreach (array('measured_rpo_minutes', 'measured_rto_minutes') as $metric) {
                if (!isset($metrics[$metric]) || !is_numeric($metrics[$metric]) || (float) $metrics[$metric] < 0) {
                    throw new \InvalidArgumentException('Registre RPO y RTO medidos con valores válidos.');
                }
            }
            if ((float) $metrics['measured_rto_minutes'] <= 0) throw new \InvalidArgumentException('El RTO medido debe ser mayor que cero.');
            if (empty($metrics['target_server'])) throw new \InvalidArgumentException('Identifique el servidor alterno donde se restauró el respaldo.');
            if (strtoupper(trim((string) (isset($metrics['integrity_check']) ? $metrics['integrity_check'] : ''))) !== 'PASSED') {
                throw new \InvalidArgumentException('La verificación de integridad de la restauración debe estar aprobada.');
            }
        }
        if ($type === self::PILOT) {
            $required = array('ADMISION','ENFERMERIA','MEDICO','ESPECIALIDAD','LABORATORIO','IMAGENES','FARMACIA','AUDITORIA','SISTEMAS');
            $participants = array_map('strtoupper', isset($metrics['participants']) ? $metrics['participants'] : array());
            foreach ($required as $role) if (!in_array($role, $participants, true)) throw new \InvalidArgumentException('Falta participante del área ' . $role . '.');
            if (!isset($metrics['critical_defects']) || (int) $metrics['critical_defects'] !== 0) throw new \InvalidArgumentException('El piloto no puede aprobarse con defectos críticos.');
            if (strtoupper(trim((string) (isset($metrics['decision']) ? $metrics['decision'] : ''))) !== 'GO') throw new \InvalidArgumentException('El piloto requiere una decisión explícita GO.');
            if (empty($metrics['training_completed'])) throw new \InvalidArgumentException('La capacitación del piloto debe constar como completada.');
            $openIncidents = isset($metrics['open_incidents']) ? (int) $metrics['open_incidents'] : 0;
            if ($openIncidents < 0) throw new \InvalidArgumentException('La cantidad de incidencias abiertas es inválida.');
            if ($openIncidents > 0 && empty($metrics['incident_owners'])) throw new \InvalidArgumentException('Asigne responsables a las incidencias abiertas.');
        }
        if ($type === self::TRANSPORT_RENEWAL) {
            if (empty($metrics['domain']) || empty($metrics['renewal_method'])) throw new \InvalidArgumentException('Registre dominio y método de renovación del certificado.');
            $testedAt = isset($metrics['renewal_tested_at']) ? strtotime($metrics['renewal_tested_at']) : false;
            $expiresAt = isset($metrics['expires_at']) ? strtotime($metrics['expires_at']) : false;
            if (!$testedAt || !$expiresAt || $expiresAt <= $testedAt) throw new \InvalidArgumentException('Las fechas de prueba y expiración del certificado son inválidas.');
            if (strtoupper(trim((string) (isset($metrics['automation_test']) ? $metrics['automation_test'] : ''))) !== 'PASSED') {
                throw new \InvalidArgumentException('El ensayo automático de renovación debe terminar en PASSED.');
            }
            if (!isset($metrics['dry_run_exit_code']) || !is_numeric($metrics['dry_run_exit_code']) || (int) $metrics['dry_run_exit_code'] !== 0) {
                throw new \InvalidArgumentException('Registre el código de salida 0 del ensayo de renovación.');
            }
            if (empty($metrics['renewal_command']) || empty($metrics['certificate_issuer'])) {
                throw new \InvalidArgumentException('Registre el comando controlado y el emisor del certificado observado.');
            }
            foreach (array('fingerprint_before', 'fingerprint_after') as $fingerprintKey) {
                $fingerprint = preg_replace('/[^a-f0-9]/i', '', (string) (isset($metrics[$fingerprintKey]) ? $metrics[$fingerprintKey] : ''));
                if (strlen($fingerprint) !== 64) throw new \InvalidArgumentException('La huella SHA-256 del certificado es inválida.');
                $metrics[$fingerprintKey] = strtolower($fingerprint);
            }
        }

        return ClinicalReadinessEvidence::create(array(
            'evidence_type' => $type,
            'status' => 'APPROVED',
            'started_at' => date('Y-m-d H:i:s', $started),
            'completed_at' => date('Y-m-d H:i:s', $completed),
            'duration_seconds' => $completed - $started,
            'metrics' => json_encode($metrics),
            'evidence_path' => $path,
            'evidence_hash' => hash_file('sha256', $path),
            'recorded_by' => $data['recorded_by'],
            'approved_by' => $data['approved_by'],
            'approved_at' => date('Y-m-d H:i:s'),
            'notes' => trim($data['notes']),
        ));
    }

    public function status()
    {
        $empty = array('available' => false, 'monitor_72h' => false, 'monitor_hours' => 0,
            'monitor_failures' => null, 'restore_approved' => false, 'pilot_approved' => false,
            'transport_renewal_approved' => false, 'daily_audit_fresh' => false,
            'operations_responsible_configured' => false, 'open_alerts' => null);
        if (!$this->available()) return $empty;

        $since = date('Y-m-d H:i:s', time() - (73 * 3600));
        $runs = ClinicalReadinessEvidence::where('evidence_type', self::MONITOR)
            ->where('completed_at', '>=', $since)->orderBy('completed_at')->get();
        $passed = $runs->filter(function ($row) { return $row->status === 'PASSED'; })->values();
        $failures = $runs->filter(function ($row) { return $row->status !== 'PASSED'; })->count();
        $hours = 0;
        $maxGap = null;
        if ($passed->count()) {
            $first = strtotime($passed->first()->completed_at);
            $last = strtotime($passed->last()->completed_at);
            $hours = round(max(0, $last - $first) / 3600, 2);
            $previous = null;
            foreach ($passed as $run) {
                $current = strtotime($run->completed_at);
                if (!is_null($previous)) $maxGap = max((int) $maxGap, $current - $previous);
                $previous = $current;
            }
        }
        $latestFresh = $passed->count() && (time() - strtotime($passed->last()->completed_at)) <= 600;
        $monitorReady = $hours >= 72 && $failures === 0 && $latestFresh && !is_null($maxGap) && $maxGap <= 600;
        $latestAudit = ClinicalReadinessEvidence::where('evidence_type', self::AUDIT)->orderBy('completed_at', 'desc')->first();
        $dailyAuditFresh = $latestAudit && $latestAudit->status === 'PASSED'
            && (time() - strtotime($latestAudit->completed_at)) <= (26 * 3600);
        $openAlerts = ClinicalReadinessEvidence::where('evidence_type', self::ALERT)->where('status', 'OPEN')->count();
        $responsibleConfigured = trim((string) config('clinical.operations_responsible')) !== '';

        return array(
            'available' => true,
            'monitor_72h' => $monitorReady,
            'monitor_hours' => $hours,
            'monitor_failures' => $failures,
            'monitor_max_gap_seconds' => $maxGap,
            'daily_audit_fresh' => (bool) $dailyAuditFresh,
            'operations_responsible_configured' => $responsibleConfigured,
            'open_alerts' => $openAlerts,
            'restore_approved' => $this->approvedCertificationReady(self::RESTORE),
            'pilot_approved' => $this->approvedCertificationReady(self::PILOT),
            'transport_renewal_approved' => $this->approvedCertificationReady(self::TRANSPORT_RENEWAL),
        );
    }

    public function recordDailyAudit($exitCode, array $metrics)
    {
        if (!$this->available()) return null;
        $now = date('Y-m-d H:i:s');
        $issues = $exitCode === 0 ? array() : array('clinical_cycle_exit:' . (int) $exitCode);
        return DB::transaction(function () use ($exitCode, $metrics, $issues, $now) {
            $responsible = trim((string) config('clinical.operations_responsible'));
            $alert = count($issues) ? $this->openAlert($issues, $responsible, $now) : null;
            $metrics['alert_id'] = $alert ? $alert->id : null;
            return ClinicalReadinessEvidence::create(array(
                'evidence_type' => self::AUDIT, 'status' => $exitCode === 0 ? 'PASSED' : 'FAILED',
                'started_at' => $now, 'completed_at' => $now, 'duration_seconds' => 0,
                'metrics' => json_encode($metrics), 'recorded_by' => 'scheduler',
                'notes' => $exitCode === 0 ? 'Auditoría clínica diaria satisfactoria.' : implode(', ', $issues),
            ));
        }, 3);
    }

    public function acknowledgeAlert($id, $acknowledgedBy, $notes)
    {
        if (trim((string) $acknowledgedBy) === '') throw new \InvalidArgumentException('Identifique a quien atendió la alerta.');
        if (strlen(trim((string) $notes)) < 10) throw new \InvalidArgumentException('Describa la atención de la alerta.');
        return DB::transaction(function () use ($id, $acknowledgedBy, $notes) {
            $alert = ClinicalReadinessEvidence::where('evidence_type', self::ALERT)->where('id', $id)->lockForUpdate()->first();
            if (!$alert) throw new \InvalidArgumentException('La alerta operativa no existe.');
            if ($alert->status === 'ACKNOWLEDGED') return $alert;
            $now = date('Y-m-d H:i:s');
            $metrics = json_decode((string) $alert->metrics, true) ?: array();
            $metrics['response_minutes'] = round(max(0, time() - strtotime($alert->started_at)) / 60, 2);
            $metrics['acknowledged_by'] = trim((string) $acknowledgedBy);
            $alert->status = 'ACKNOWLEDGED';
            $alert->completed_at = $now;
            $alert->duration_seconds = max(0, time() - strtotime($alert->started_at));
            $alert->metrics = json_encode($metrics);
            $alert->approved_by = trim((string) $acknowledgedBy);
            $alert->approved_at = $now;
            $alert->notes = trim((string) $notes);
            $alert->save();
            return $alert;
        }, 3);
    }

    private function openAlert(array $issues, $responsible, $now)
    {
        sort($issues);
        $signature = hash('sha256', implode('|', $issues));
        $open = ClinicalReadinessEvidence::where('evidence_type', self::ALERT)->where('status', 'OPEN')->lockForUpdate()->get();
        foreach ($open as $alert) {
            $metrics = json_decode((string) $alert->metrics, true) ?: array();
            if (isset($metrics['signature']) && hash_equals($metrics['signature'], $signature)) return $alert;
        }
        return ClinicalReadinessEvidence::create(array(
            'evidence_type' => self::ALERT, 'status' => 'OPEN', 'started_at' => $now,
            'metrics' => json_encode(array('issues' => $issues, 'signature' => $signature,
                'assigned_to' => $responsible !== '' ? $responsible : null, 'response_minutes' => null)),
            'recorded_by' => 'scheduler', 'notes' => implode(', ', $issues),
        ));
    }

    private function recentLogErrorCount($path, $seconds)
    {
        if (!is_file($path) || !is_readable($path)) return 0;
        $size = filesize($path);
        $handle = fopen($path, 'rb');
        if (!$handle) return 0;
        if ($size > 1048576) fseek($handle, -1048576, SEEK_END);
        $contents = stream_get_contents($handle);
        fclose($handle);
        if ($contents === false || $contents === '') return 0;
        $count = 0;
        if (preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(?:ERROR|CRITICAL|ALERT|EMERGENCY)\b/i', $contents, $matches)) {
            foreach ($matches[1] as $date) if (($timestamp = strtotime($date)) && time() - $timestamp <= $seconds) $count++;
        }
        return $count;
    }

    private function approvedCertificationReady($type)
    {
        $evidence = ClinicalReadinessEvidence::where('evidence_type', $type)->where('status', 'APPROVED')
            ->orderBy('approved_at', 'desc')->first();
        if (!$evidence || !$evidence->evidence_path || !is_file($evidence->evidence_path)) return false;
        if (!$evidence->evidence_hash || !hash_equals($evidence->evidence_hash, hash_file('sha256', $evidence->evidence_path))) return false;
        if (!$evidence->recorded_by || !$evidence->approved_by || (string) $evidence->recorded_by === (string) $evidence->approved_by) return false;
        $metrics = json_decode((string) $evidence->metrics, true) ?: array();
        if ($type === self::RESTORE) {
            return isset($metrics['measured_rpo_minutes'], $metrics['measured_rto_minutes'])
                && is_numeric($metrics['measured_rpo_minutes']) && (float) $metrics['measured_rpo_minutes'] >= 0
                && is_numeric($metrics['measured_rto_minutes']) && (float) $metrics['measured_rto_minutes'] > 0
                && !empty($metrics['target_server']) && strtoupper((string) $metrics['integrity_check']) === 'PASSED';
        }
        if ($type === self::PILOT) {
            $required = array('ADMISION','ENFERMERIA','MEDICO','ESPECIALIDAD','LABORATORIO','IMAGENES','FARMACIA','AUDITORIA','SISTEMAS');
            $participants = array_map('strtoupper', isset($metrics['participants']) ? $metrics['participants'] : array());
            return count(array_diff($required, $participants)) === 0
                && isset($metrics['critical_defects']) && (int) $metrics['critical_defects'] === 0
                && strtoupper((string) (isset($metrics['decision']) ? $metrics['decision'] : '')) === 'GO'
                && !empty($metrics['training_completed'])
                && ((int) (isset($metrics['open_incidents']) ? $metrics['open_incidents'] : 0) === 0 || !empty($metrics['incident_owners']));
        }
        if ($type === self::TRANSPORT_RENEWAL) {
            return !empty($metrics['domain']) && !empty($metrics['renewal_method'])
                && !empty($metrics['renewal_tested_at']) && !empty($metrics['expires_at'])
                && strtotime($metrics['expires_at']) > strtotime($metrics['renewal_tested_at'])
                && strtoupper((string) (isset($metrics['automation_test']) ? $metrics['automation_test'] : '')) === 'PASSED'
                && isset($metrics['dry_run_exit_code']) && (int) $metrics['dry_run_exit_code'] === 0
                && !empty($metrics['renewal_command']) && !empty($metrics['certificate_issuer'])
                && strlen(preg_replace('/[^a-f0-9]/i', '', (string) (isset($metrics['fingerprint_before']) ? $metrics['fingerprint_before'] : ''))) === 64
                && strlen(preg_replace('/[^a-f0-9]/i', '', (string) (isset($metrics['fingerprint_after']) ? $metrics['fingerprint_after'] : ''))) === 64;
        }
        return false;
    }
}
