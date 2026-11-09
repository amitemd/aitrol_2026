<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use Sis_medico\ClinicalTransferOrderItem;
use Sis_medico\ClinicalTransferReconciliation;
use Sis_medico\ClinicalOrder;
use Sis_medico\Examen_Orden;
use Sis_medico\Ho_Orden_Examenes;
use Sis_medico\Ho_Ordenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Orden;

class ClinicalTransferReconciliationService
{
    const PRESERVE = 'PRESERVE';

    public function available()
    {
        return Schema::hasTable('clinical_transfer_reconciliations')
            && Schema::hasTable('clinical_transfer_order_items')
            && app(ClinicalStateTransitionService::class)->available();
    }

    public function previewActiveOrders($solicitudId)
    {
        $orders = array();
        $solicitud = Ho_Solicitud::find($solicitudId);

        if ($solicitud && $solicitud->encounter_id && app(ClinicalOrderService::class)->available()) {
            app(ClinicalOrderService::class)->syncSolicitud($solicitudId);
            foreach (ClinicalOrder::where('encounter_id', $solicitud->encounter_id)
                ->whereNotIn('status', array(ClinicalOrderService::COMPLETED, ClinicalOrderService::CANCELLED))->get() as $order) {
                $orders[] = array(
                    'type' => $order->category,
                    'id' => $order->id,
                    'legacy_id' => $order->source_id,
                    'source_type' => $order->source_type,
                    'description' => $order->description,
                    'status' => $order->status,
                );
            }
            return $orders;
        }

        if (Schema::hasTable('ho_orden_examenes') && Schema::hasTable('examen_orden')) {
            foreach (Ho_Orden_Examenes::where('id_ho_solicitud', $solicitudId)->get() as $link) {
                $order = Examen_Orden::find($link->id_examen_orden);
                if ($order && (string) $order->estado !== '0') {
                    $orders[] = array('type' => 'LABORATORY', 'id' => $order->id,
                        'description' => 'Orden de laboratorio #' . $order->id, 'status' => (string) $order->estado);
                }
            }
        }

        if (Schema::hasTable('ho_ordenes') && Schema::hasTable('orden')) {
            foreach (Ho_Ordenes::where('id_ho_solicitud', $solicitudId)->where('tipo', '<>', 'INTERCONSULTA')->get() as $link) {
                $order = Orden::find($link->clave_tipo);
                if ($order && (string) $order->estado !== '0') {
                    $type = $link->tipo ? strtoupper((string) $link->tipo) : 'PROCEDURE';
                    if (strpos($type, 'IMAGEN') !== false || (string) $order->tipo_procedimiento === '2') {
                        $type = 'IMAGING';
                    }
                    $orders[] = array('type' => $type, 'id' => $order->id,
                        'description' => 'Orden de ' . strtolower($type) . ' #' . $order->id, 'status' => (string) $order->estado);
                }
            }
        }

        return $orders;
    }

    public function prepare($solicitudId, $targetStep, $decision, array $context = array())
    {
        if ($decision !== self::PRESERVE) {
            throw new InvalidArgumentException('R-07 exige confirmar la conservación de las órdenes activas antes del traslado.');
        }
        if (!$this->available()) throw new \RuntimeException('La conciliación clínica de traslados no está disponible.');

        $solicitud = Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->first();
        if (!$solicitud) throw new InvalidArgumentException('No se encontró la solicitud clínica a trasladar.');
        if ((int) $solicitud->estado_paso === (int) $targetStep) return null;

        $encounterService = app(ClinicalEncounterService::class);
        $encounter = $encounterService->findBySource('ho_solicitud', $solicitud->id);
        if (!$encounter) {
            $encounter = $encounterService->openOrReuse($solicitud->id_paciente,
                $encounterService->settingFromLegacyStep($solicitud->estado_paso), $context);
            $encounterService->link($encounter, array('ho_solicitud' => $solicitud->id, 'agenda' => $solicitud->id_agenda));
        }

        $states = app(ClinicalStateTransitionService::class);
        $encounter = $states->initialize($encounter, $solicitud->estado_paso, $context);
        $fromState = $encounter->clinical_state;
        $toState = $states->stateForLegacyStep($targetStep);
        $orders = $this->previewActiveOrders($solicitud->id);

        $reconciliation = ClinicalTransferReconciliation::create(array(
            'encounter_id' => $encounter->id,
            'source_type' => 'ho_solicitud',
            'source_id' => (string) $solicitud->id,
            'from_state' => $fromState,
            'to_state' => $toState,
            'decision' => self::PRESERVE,
            'active_orders_count' => count($orders),
            'reason' => isset($context['reason']) ? trim((string) $context['reason']) : 'Traslado asistencial; se conservan las órdenes activas.',
            'status' => 'PENDING',
            'actor_id' => $this->actorId($context),
            'ip_address' => $this->ip($context),
        ));

        foreach ($orders as $order) {
            ClinicalTransferOrderItem::create(array(
                'reconciliation_id' => $reconciliation->id,
                'order_type' => $order['type'],
                'order_id' => (string) $order['id'],
                'description' => $order['description'],
                'previous_status' => $order['status'],
                'decision' => self::PRESERVE,
                'responsible_id' => $this->actorId($context),
            ));
        }

        $states->transition($encounter, ClinicalStateTransitionService::TRANSFER_PENDING,
            'Traslado iniciado con ' . count($orders) . ' orden(es) activa(s) preservada(s).', $context);
        return $reconciliation;
    }

    public function complete($reconciliation, array $context = array())
    {
        if (is_null($reconciliation)) return;
        if (!$this->available()) throw new \RuntimeException('La conciliación clínica de traslados no está disponible.');
        $states = app(ClinicalStateTransitionService::class);
        $encounter = $reconciliation->fresh()->encounter_id;
        $encounter = \Sis_medico\ClinicalEncounter::find($encounter);
        $states->transition($encounter, $reconciliation->to_state,
            'Traslado completado; órdenes activas conservadas y trazables.', $context);
        $reconciliation->status = 'COMPLETED';
        $reconciliation->completed_at = date('Y-m-d H:i:s');
        $reconciliation->save();
    }

    public function integrity()
    {
        if (!$this->available()) return array('available'=>false,'pending'=>null);
        $pending=DB::table('clinical_transfer_reconciliations')->where('status','PENDING')
            ->where('created_at','<',date('Y-m-d H:i:s',time()-900))->count();
        $invalid=DB::table('clinical_transfer_reconciliations as r')
            ->leftJoin('clinical_encounters as e','e.id','=','r.encounter_id')
            ->where(function($query){$query->where('r.decision','<>',self::PRESERVE)->orWhereNull('r.actor_id')->orWhereNull('r.reason')->orWhere('r.reason','')
                ->orWhere(function($completed){$completed->where('r.status','COMPLETED')->whereNull('r.completed_at');})
                ->orWhereNull('e.id');})->count();
        $itemMismatch=0;
        $eventMismatch=0;
        $sourceMismatch=0;
        foreach(ClinicalTransferReconciliation::where('status','COMPLETED')->get() as $reconciliation){
            $items=ClinicalTransferOrderItem::where('reconciliation_id',$reconciliation->id)->count();
            if((int)$reconciliation->active_orders_count!==$items)$itemMismatch++;
            $invalidItems=ClinicalTransferOrderItem::where('reconciliation_id',$reconciliation->id)->where(function($q){
                $q->where('decision','<>',self::PRESERVE)->orWhereNull('responsible_id');
            })->count();
            if($invalidItems)$itemMismatch++;
            $events=DB::table('clinical_encounter_events')->where('encounter_id',$reconciliation->encounter_id)
                ->where('event_type','CLINICAL_STATE_CHANGED')->whereBetween('created_at',array($reconciliation->created_at,$reconciliation->completed_at))
                ->pluck('to_value')->all();
            if(!in_array(ClinicalStateTransitionService::TRANSFER_PENDING,$events,true)||!in_array($reconciliation->to_state,$events,true))$eventMismatch++;
            $source=Ho_Solicitud::where('id',$reconciliation->source_id)->first();
            if($reconciliation->source_type!=='ho_solicitud'||!$source||$source->encounter_id!==$reconciliation->encounter_id)$sourceMismatch++;
        }
        $duplicatePending=DB::table('clinical_transfer_reconciliations')->where('status','PENDING')->groupBy('encounter_id')
            ->havingRaw('COUNT(*) > 1')->get()->count();
        return array('available'=>true,'stale_pending'=>$pending,'invalid'=>$invalid,
            'item_mismatch'=>$itemMismatch,'event_mismatch'=>$eventMismatch,'source_mismatch'=>$sourceMismatch,
            'duplicate_pending'=>$duplicatePending,'pending'=>$pending+$invalid+$itemMismatch+$eventMismatch+$sourceMismatch+$duplicatePending);
    }

    private function actorId(array $context)
    {
        if (isset($context['actor_id'])) return $context['actor_id'];
        return Auth::check() ? Auth::user()->id : null;
    }

    private function ip(array $context)
    {
        if (isset($context['ip'])) return $context['ip'];
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }
}
