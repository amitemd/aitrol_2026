<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Sis_medico\ClinicalCriticalResultNotification;
use Sis_medico\ClinicalOrder;
use Sis_medico\Exceptions\ClinicalOrderTransitionException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalOrderService;
use Sis_medico\Services\ClinicalAccessControlService;
use Sis_medico\Services\ClinicalEncounterSelectorService;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;

class ClinicalOrderController extends Controller
{
    private $service;

    public function __construct(ClinicalOrderService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if (!$this->service->available()) {
            return redirect()->route('hospital.index')->with('error', 'Aplique la migración del ciclo clínico de órdenes y resultados.');
        }

        $encounterId = trim((string) $request->input('encounter_id', ''));
        if ($encounterId === '') {
            $encounters = app(ClinicalEncounterSelectorService::class)->paginateForAction('ORDER_VIEW', $request);
            if (is_null($encounters)) return redirect()->route('hospital.index')->with('error', 'La selección segura de episodios no está disponible.');
            return view('hospital.clinical_encounters.select', array(
                'encounters' => $encounters,
                'targetRoute' => 'clinical_orders.index',
                'title' => 'Órdenes y resultados',
                'description' => 'Selecciona el episodio antes de consultar órdenes, resultados y alertas críticas.',
                'icon' => 'fa fa-clipboard',
            ));
        }
        try {
            app(ClinicalAccessControlService::class)->assertAllowed($encounterId, 'ORDER_VIEW', $this->accessContext($request));
        } catch (ClinicalAccessDeniedException $exception) {
            return redirect()->route('hospital.index')->with('error', $exception->getMessage());
        }
        $query = ClinicalOrder::with(array('results' => function ($query) {
            $query->orderBy('occurred_at', 'desc');
        }))->orderBy('ordered_at', 'desc')->orderBy('id', 'desc');

        $status = trim((string) $request->input('status', ''));
        $category = trim((string) $request->input('category', ''));
        $patientId = trim((string) $request->input('patient_id', ''));
        if ($status !== '') $query->where('status', $status);
        if ($category !== '') $query->where('category', $category);
        if ($patientId !== '') $query->where('patient_id', 'like', '%' . $patientId . '%');
        $query->where('encounter_id', $encounterId);

        $orders = $query->paginate(20);
        $criticalQuery = ClinicalCriticalResultNotification::with('result.order')
            ->whereIn('status', array('PENDING', 'NOTIFIED', 'ESCALATED'))
            ->orderBy('due_at');
        $criticalQuery->whereHas('result.order', function ($query) use ($encounterId) {
            $query->where('encounter_id', $encounterId);
        });
        $criticalAlerts = $criticalQuery->get();

        return view('hospital.clinical_orders.index', compact('orders', 'criticalAlerts', 'encounterId'));
    }

    public function transition(Request $request, $id)
    {
        $this->validate($request, array(
            'status' => 'required|in:ORDERED,ACKNOWLEDGED,IN_PROGRESS,COMPLETED,CANCELLED',
            'reason' => 'nullable|string|max:1000',
        ));

        try {
            $order = ClinicalOrder::findOrFail($id);
            app(ClinicalAccessControlService::class)->assertAllowed($order->encounter_id, 'ORDER_TRANSITION', $this->accessContext($request));
            $this->service->transition($order, $request->status, $request->reason, $this->context($request));
            return back()->with('success', 'El estado de la orden clínica fue actualizado.');
        } catch (ClinicalOrderTransitionException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (ClinicalAccessDeniedException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function storeResult(Request $request, $id)
    {
        $this->validate($request, array(
            'status' => 'required|in:PRELIMINARY,FINAL,AMENDED',
            'summary' => 'required|string|max:5000',
            'is_critical' => 'nullable|boolean',
            'critical_reason' => 'nullable|string|max:2000',
        ));

        try {
            $order = ClinicalOrder::findOrFail($id);
            app(ClinicalAccessControlService::class)->assertAllowed($order->encounter_id, 'RESULT_CREATE', $this->accessContext($request));
            $this->service->recordResult($order, array(
                'source_type' => 'clinical_manual',
                'source_id' => $order->id . '-' . str_replace('.', '', microtime(true)),
                'status' => $request->status,
                'summary' => $request->summary,
                'is_critical' => $request->input('is_critical') == '1',
                'critical_reason' => $request->critical_reason,
            ), $this->context($request));
            return back()->with('success', 'El resultado fue registrado y vinculado al episodio.');
        } catch (ClinicalOrderTransitionException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (ClinicalAccessDeniedException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function notifyCritical(Request $request, $id)
    {
        $this->validate($request, array(
            'recipient_name' => 'required|string|max:191',
            'recipient_role' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ));

        try {
            $notification = ClinicalCriticalResultNotification::with('result.order')->findOrFail($id);
            app(ClinicalAccessControlService::class)->assertAllowed($notification->result->order->encounter_id, 'CRITICAL_RESULT_NOTIFY', $this->accessContext($request));
            $this->service->notifyCritical($id, $request->only('recipient_name', 'recipient_role', 'notes'), $this->context($request));
            return back()->with('success', 'La notificación del resultado crítico quedó registrada.');
        } catch (ClinicalOrderTransitionException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (ClinicalAccessDeniedException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function acknowledgeCritical(Request $request, $id)
    {
        try {
            $notification = ClinicalCriticalResultNotification::with('result.order')->findOrFail($id);
            app(ClinicalAccessControlService::class)->assertAllowed($notification->result->order->encounter_id, 'CRITICAL_RESULT_ACK', $this->accessContext($request));
            $this->service->acknowledgeCritical($id, $this->context($request));
            return back()->with('success', 'Acuse registrado. El circuito del resultado crítico quedó cerrado.');
        } catch (ClinicalOrderTransitionException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (ClinicalAccessDeniedException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    private function context(Request $request)
    {
        return array('actor_id' => auth()->user()->id, 'ip' => $request->ip());
    }

    private function accessContext(Request $request)
    {
        return array('user' => auth()->user(), 'ip' => $request->ip());
    }
}
