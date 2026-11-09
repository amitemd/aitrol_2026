<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\HospitalDocumentService;
use Sis_medico\Services\HospitalEpisodeLaboratoryService;

class HospitalEpisodeLaboratoryController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request, $id_solicitud)
    {
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $episode->loadMissing('paciente');
        $encounter = ClinicalEncounter::findOrFail($episode->encounter_id);
        $laboratory = app(HospitalEpisodeLaboratoryService::class);
        $reports = $laboratory->orders($episode, $encounter)->map(function ($order) use ($episode, $laboratory) {
            return $laboratory->report($episode, $order);
        });
        $open = HospitalDocumentService::writable($episode, $encounter, true);
        return view('hospital.laboratorio.episodio', [
            'solicitud' => $episode, 'reports' => $reports, 'focus' => $request->query('form', '010A'),
            'canRequest' => HospitalDocumentService::writable($episode, $encounter) && $this->requestRole(),
            // Same role set used by OrdenController::rol_control().
            'canRecord' => $open && in_array((int) Auth::user()->id_tipo_usuario, [1, 10, 11, 16], true),
        ]);
    }

    public function requests($id_solicitud)
    {
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $encounter = app(HospitalDocumentService::class)->authorize($episode, 'hospital.documents.write');
        abort_unless($this->requestRole() && HospitalDocumentService::writable($episode, $encounter), 403,
            'Se requiere un episodio abierto y un usuario autorizado para solicitar exámenes.');
        $native = app(emergencia\DecimoPasoController::class)->laboratorio($episode->id);
        return view('hospital.laboratorio.solicitudes-episodio', ['solicitud' => $episode, 'nativeData' => $native->getData()]);
    }

    public function results(Request $request, $id_solicitud, $order, $machine)
    {
        abort_unless(in_array((string) $machine, ['0', '1', '2', 'special'], true), 404);
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $encounter = app(HospitalDocumentService::class)->authorize($episode, 'hospital.documents.write');
        abort_unless(HospitalDocumentService::writable($episode, $encounter, true), 403, 'El episodio no admite edición de resultados.');
        abort_unless(in_array((int) Auth::user()->id_tipo_usuario, [1, 10, 11, 16], true), 403);
        $laboratory = app(HospitalEpisodeLaboratoryService::class);
        $labOrder = $laboratory->order($episode, $order);
        abort_unless((int) $labOrder->estado === 1, 422, 'Confirme la solicitud antes de ingresar resultados.');
        $report = $laboratory->report($episode, $labOrder);
        abort_unless($report['exams']->contains(function ($exam) use ($machine) {
            return (string) ($exam->maquina ?? '') === ($machine === 'special' ? '0' : (string) $machine);
        }), 404);
        $controller = app(\Sis_medico\Http\Controllers\laboratorio\OrdenController::class);
        $view = $machine === 'special' ? $controller->resultado_especial($order) : $controller->realizar($request, $order, $machine);
        return $view instanceof \Illuminate\View\View ? $view->with('hospitalEpisode', $episode) : $view;
    }

    public function catalog(Request $request, $id_solicitud, $order)
    {
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $labOrder = app(HospitalEpisodeLaboratoryService::class)->order($episode, $order);
        return view('hospital.emergencia.orden_laboratorio.listado',
            app(\Sis_medico\Services\HospitalLaboratoryOrderCatalog::class)->data($labOrder, $request->only('buscador', 'buscador2', 'seleccionados')));
    }

    public function selection(Request $request, $id_solicitud, $order)
    {
        $data = $request->validate([
            'action' => 'required|in:add,remove,profile,clear',
            'exam_id' => 'required_if:action,add,remove|nullable|integer',
            'profile_id' => 'required_if:action,profile|nullable|integer',
        ]);
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $encounter = app(HospitalDocumentService::class)->authorize($episode, 'hospital.documents.write');
        abort_unless($this->requestRole() && HospitalDocumentService::writable($episode, $encounter), 403,
            'El episodio no admite cambios en la solicitud.');
        app(HospitalEpisodeLaboratoryService::class)->order($episode, $order);
        return \DB::transaction(function () use ($request, $data, $episode, $order) {
            $lockedEpisode = \Sis_medico\Ho_Solicitud::whereKey($episode->id)->lockForUpdate()->firstOrFail();
            $lockedEncounter = ClinicalEncounter::whereKey($episode->encounter_id)->lockForUpdate()->firstOrFail();
            abort_unless(HospitalDocumentService::writable($lockedEpisode, $lockedEncounter), 403);
            $labOrder = \Sis_medico\Examen_Orden::whereKey($order)->lockForUpdate()->firstOrFail();
            abort_unless((int) $labOrder->estado === -1, 422, 'La solicitud ya fue confirmada o anulada. Actualice la pantalla.');
            abort_unless($labOrder->seguro && (int) $labOrder->seguro->tipo !== 0, 422);
            $current = $labOrder->detalles->pluck('id_examen')->unique()->all();
            $desired = $current;
            if ($data['action'] === 'add') $desired[] = $data['exam_id'];
            elseif ($data['action'] === 'remove') $desired = array_diff($current, [$data['exam_id']]);
            elseif ($data['action'] === 'clear') $desired = [];
            else {
                $profile = \Sis_medico\Protocolo::whereKey($data['profile_id'])->where('estado', 2)
                    ->where('id_usuariocrea', Auth::id())->firstOrFail();
                $desired = \DB::table('examen_protocolo')->where('id_protocolo', $profile->id)->pluck('id_examen')->unique()->all();
                abort_if(empty($desired), 422, 'El perfil no tiene exámenes configurados.');
            }
            $add = array_diff(array_unique($desired), $current);
            if ($add) {
                $active = \Sis_medico\Examen::whereIn('id', $add)->where('estado', 1)->count();
                abort_unless($active === count($add), 422, 'Hay exámenes inactivos o no disponibles en esta selección.');
            }
            // Retain existing pricing, coverage, membership and audit rules.
            $native = app(\Sis_medico\Http\Controllers\laboratorio\OrdenController::class);
            foreach (array_diff($current, $desired) as $examId) $native->cotizador_delete($order, $examId);
            foreach ($add as $examId) $native->cotizador_update($order, $examId);
            $labOrder->refresh();
            $labOrder->id_protocolo = $data['action'] === 'profile' ? $data['profile_id'] : null;
            $labOrder->id_usuariomod = Auth::id();
            $labOrder->ip_modificacion = $request->ip();
            $labOrder->save();
            $selected = $labOrder->detalles()->pluck('id_examen')->unique()->values();
            return response()->json(['selected' => $selected, 'count' => $selected->count(), 'message' => 'Selección guardada.']);
        });
    }

    public function confirm(Request $request, $id_solicitud, $order)
    {
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $encounter = app(HospitalDocumentService::class)->authorize($episode, 'ORDER_TRANSITION');
        abort_unless($this->requestRole() && HospitalDocumentService::writable($episode, $encounter), 403,
            'El episodio no admite nuevas solicitudes.');
        $labOrder = app(HospitalEpisodeLaboratoryService::class)->order($episode, $order);
        $return = route('hospital.laboratory.index', ['id_solicitud' => $episode->id]
            + ($request->boolean('embedded') ? ['embedded' => 1] : []));
        if ((int) $labOrder->estado === 1) return redirect($return);
        $cycle = app(\Sis_medico\Services\ClinicalOrderService::class);
        abort_unless($cycle->available(), 422, 'El ciclo de órdenes clínicas no está disponible.');
        try {
            \DB::transaction(function () use ($cycle, $episode, $labOrder, $request) {
                $lockedEpisode = \Sis_medico\Ho_Solicitud::whereKey($episode->id)->lockForUpdate()->firstOrFail();
                $lockedEncounter = ClinicalEncounter::whereKey($episode->encounter_id)->lockForUpdate()->firstOrFail();
                abort_unless(HospitalDocumentService::writable($lockedEpisode, $lockedEncounter), 403);
                $cycle->syncSolicitud($episode->id);
                $clinicalOrder = \Sis_medico\ClinicalOrder::where('source_type', 'examen_orden')
                    ->where('source_id', (string) $labOrder->id)->where('encounter_id', $episode->encounter_id)->firstOrFail();
                $cycle->transition($clinicalOrder, \Sis_medico\Services\ClinicalOrderService::ORDERED,
                    'Solicitud de laboratorio confirmada desde el episodio.', ['actor_id' => Auth::id(), 'ip' => $request->ip()]);
            });
            return redirect($return)->with('success', 'Solicitud confirmada. El laboratorio ya puede ingresar resultados.');
        } catch (\Sis_medico\Exceptions\ClinicalOrderTransitionException $exception) {
            return redirect($return)->with('error', $exception->getMessage());
        }
    }

    public function pdf($id_solicitud, $order, $code)
    {
        abort_unless(in_array($code, ['010A', '010B'], true), 404);
        $episode = app(HospitalDocumentService::class)->obtain($id_solicitud);
        $episode->loadMissing('paciente');
        $laboratory = app(HospitalEpisodeLaboratoryService::class);
        $labOrder = $laboratory->order($episode, $order);
        $report = $laboratory->report($episode, $labOrder);
        $labOrder->loadMissing('empresa', 'doctor');
        $pdf = app('dompdf.wrapper');
        $pdf->setOptions(['isRemoteEnabled' => false]);
        return $pdf->loadView('hospital.laboratorio.formulario-pdf', [
            'solicitud' => $episode, 'report' => $report, 'code' => $code,
        ])->setPaper('a4', 'portrait')->stream('formulario-'.$code.'-orden-'.$labOrder->id.'.pdf')
            ->header('Cache-Control', 'no-store, private');
    }

    private function requestRole()
    {
        return Auth::user()->isSystemUser() || Auth::user()->hasAnyUserTypeName(['DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);
    }
}
