<?php

namespace Sis_medico\Http\Controllers\hospital;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Sis_medico\BloodBankRequest;
use Sis_medico\BloodBankRequestUnit;
use Sis_medico\BloodBankUnit;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;

class BloodBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!$this->available()) {
            return redirect()->route('hospital.index')->with('error', 'Aplique la migración del módulo de Banco de sangre.');
        }
        if ($this->companyId() === null) {
            return redirect()->route('hospital.index')->with('error', 'Seleccione una empresa antes de ingresar al Banco de sangre.');
        }

        $this->companyUnits()->where('status', 'AVAILABLE')->where('expires_at', '<', Carbon::now())
            ->update(array('status' => 'EXPIRED', 'updated_by' => auth()->user()->id, 'updated_at' => Carbon::now()));

        $unitQuery = $this->companyUnits()->with(array(
            'allocations' => function ($query) { $query->orderBy('id', 'desc'); },
            'allocations.request',
        ))->orderBy('expires_at')->orderBy('id', 'desc');
        if ($request->filled('blood_type')) $unitQuery->where('blood_type', $request->blood_type);
        if ($request->filled('component')) $unitQuery->where('component', $request->component);
        if ($request->filled('unit_status')) $unitQuery->where('status', $request->unit_status);
        $units = $unitQuery->paginate(12, array('*'), 'units_page');

        $requestQuery = $this->companyRequests()->with(array('units' => function ($query) {
            $query->orderBy('expires_at');
        }))->orderByRaw("CASE priority WHEN 'EMERGENCY' THEN 1 WHEN 'URGENT' THEN 2 ELSE 3 END")
            ->orderBy('due_at')->orderBy('id', 'desc');
        if ($request->filled('request_status')) $requestQuery->where('status', $request->request_status);
        if ($request->filled('patient')) {
            $term = trim($request->patient);
            $requestQuery->where(function ($query) use ($term) {
                $query->where('patient_id', 'like', '%' . $term . '%')->orWhere('patient_name', 'like', '%' . $term . '%');
            });
        }
        $requests = $requestQuery->paginate(10, array('*'), 'requests_page');

        $stats = array(
            'available' => $this->companyUnits()->where('status', 'AVAILABLE')->count(),
            'expiring' => $this->companyUnits()->where('status', 'AVAILABLE')->whereBetween('expires_at', array(Carbon::now(), Carbon::now()->addDays(7)))->count(),
            'pending' => $this->companyRequests()->whereIn('status', array('PENDING', 'COMPATIBILITY'))->count(),
            'ready' => $this->companyRequests()->where('status', 'READY')->count(),
        );
        $stock = $this->companyUnits()->where('status', 'AVAILABLE')->select('blood_type', DB::raw('count(*) as total'))
            ->groupBy('blood_type')->pluck('total', 'blood_type');
        $availableUnits = $this->companyUnits()->where('status', 'AVAILABLE')->where('expires_at', '>', Carbon::now())
            ->orderBy('expires_at')->get();

        return view('hospital.blood_bank.index', compact('units', 'requests', 'stats', 'stock', 'availableUnits'));
    }

    public function searchPatients(Request $request)
    {
        $companyId = $this->requireCompanyId();
        $this->validate($request, array('term' => 'nullable|string|max:100'));
        $term = trim((string) $request->input('term'));
        $query = Paciente::query()->select(array(
            'paciente.id', 'paciente.nombre1', 'paciente.nombre2',
            'paciente.apellido1', 'paciente.apellido2', 'paciente.gruposanguineo',
        ));
        if ($term !== '') {
            $query->where(function ($patients) use ($term) {
                $like = '%' . $term . '%';
                $patients->where('paciente.id', 'like', $like)
                    ->orWhere('paciente.nombre1', 'like', $like)
                    ->orWhere('paciente.nombre2', 'like', $like)
                    ->orWhere('paciente.apellido1', 'like', $like)
                    ->orWhere('paciente.apellido2', 'like', $like);
            });
        }
        $patients = $query->orderBy('paciente.apellido1')->orderBy('paciente.nombre1')->limit(20)->get();

        $encounters = collect();
        if ($patients->isNotEmpty() && Schema::hasTable('clinical_encounters')) {
            $encounters = DB::table('clinical_encounters')
                ->whereIn('id_paciente', $patients->pluck('id')->all())
                ->where('status', 'ACTIVE')
                ->where('id_empresa', $companyId)
                ->orderBy('started_at', 'desc')->get()->groupBy('id_paciente');
        }

        return response()->json($patients->map(function ($patient) use ($encounters) {
            $name = $this->patientName($patient);
            $activeEncounter = $encounters->has((string) $patient->id)
                ? $encounters->get((string) $patient->id)->first()
                : null;
            return array(
                'id' => (string) $patient->id,
                'text' => $name . ' · ' . $patient->id,
                'name' => $name,
                'blood_type' => in_array($patient->gruposanguineo, $this->bloodTypes(), true)
                    ? $patient->gruposanguineo : null,
                'encounter_id' => $activeEncounter ? (string) $activeEncounter->id : null,
            );
        })->values());
    }

    public function storeUnit(Request $request)
    {
        $companyId = $this->requireCompanyId();
        $request->merge(array(
            'code' => trim((string) $request->code),
            'donor_code' => trim((string) $request->donor_code),
            'location' => trim((string) $request->location),
        ));
        $this->validate($request, array(
            'code' => 'required|string|max:40', 'blood_type' => 'required|in:O-,O+,A-,A+,B-,B+,AB-,AB+',
            'component' => 'required|in:RED_CELLS,PLASMA,PLATELETS,CRYOPRECIPITATE',
            'volume_ml' => 'required|integer|min:20|max:1000', 'donor_code' => 'nullable|string|max:40',
            'location' => 'nullable|string|max:120', 'collected_at' => 'required|date',
            'expires_at' => 'required|date|after:collected_at',
        ));
        if ($this->companyUnits()->where('code', trim($request->code))->exists()) {
            return back()->withInput()->with('error', 'Ya existe una unidad con ese código en la empresa activa.');
        }
        BloodBankUnit::create(array_merge($request->only(
            'code', 'blood_type', 'component', 'volume_ml', 'donor_code', 'location', 'collected_at', 'expires_at'
        ), array('id_empresa' => $companyId, 'status' => 'AVAILABLE', 'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id)));
        return back()->with('success', 'Unidad registrada y disponible en el inventario.');
    }

    public function storeRequest(Request $request)
    {
        $companyId = $this->requireCompanyId();
        $this->validate($request, array(
            'patient_id' => 'required|string|max:20',
            'encounter_id' => 'nullable|string|max:36', 'blood_type' => 'required|in:O-,O+,A-,A+,B-,B+,AB-,AB+',
            'component' => 'required|in:RED_CELLS,PLASMA,PLATELETS,CRYOPRECIPITATE',
            'units_required' => 'required|integer|min:1|max:20', 'priority' => 'required|in:ROUTINE,URGENT,EMERGENCY',
            'indication' => 'required|string|min:10|max:3000', 'notes' => 'nullable|string|max:3000',
        ));
        $patient = $this->patientForCompany($request->patient_id);
        $patientName = $this->patientName($patient);
        if ($request->filled('encounter_id') && Schema::hasTable('clinical_encounters')) {
            $validEncounter = DB::table('clinical_encounters')->where('id', $request->encounter_id)
                ->where('id_paciente', $patient->id)->where('id_empresa', $companyId)
                ->where('status', 'ACTIVE')->exists();
            if (!$validEncounter) {
                throw ValidationException::withMessages(array(
                    'encounter_id' => 'El episodio no corresponde al paciente y a la empresa activa.',
                ));
            }
        }
        $minutes = array('ROUTINE' => 1440, 'URGENT' => 240, 'EMERGENCY' => 30);
        BloodBankRequest::create(array_merge($request->only(
            'patient_id', 'encounter_id', 'blood_type', 'component', 'units_required', 'priority', 'indication', 'notes'
        ), array(
            'patient_name' => $patientName,
            'id_empresa' => $companyId, 'status' => 'PENDING', 'requested_by' => auth()->user()->id,
            'requested_at' => Carbon::now(), 'due_at' => Carbon::now()->addMinutes($minutes[$request->priority]),
            'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id,
        )));
        return back()->with('success', 'Solicitud transfusional creada y priorizada.');
    }

    public function updateUnit(Request $request, $id)
    {
        $this->requireCompanyId();
        $request->merge(array(
            'code' => trim((string) $request->code),
            'donor_code' => trim((string) $request->donor_code),
            'location' => trim((string) $request->location),
        ));
        $this->validate($request, array(
            'code' => 'required|string|max:40',
            'blood_type' => 'required|in:O-,O+,A-,A+,B-,B+,AB-,AB+',
            'component' => 'required|in:RED_CELLS,PLASMA,PLATELETS,CRYOPRECIPITATE',
            'volume_ml' => 'required|integer|min:20|max:1000',
            'donor_code' => 'nullable|string|max:40',
            'location' => 'nullable|string|max:120',
            'collected_at' => 'required|date',
            'expires_at' => 'required|date|after:collected_at',
        ));

        $unit = $this->companyUnits()->findOrFail($id);
        if (in_array($unit->status, array('RESERVED', 'USED'), true)) {
            return back()->with('error', $unit->status === 'RESERVED'
                ? 'Libere la unidad de la solicitud antes de editar sus datos.'
                : 'Una unidad utilizada no puede editarse porque forma parte del registro transfusional.');
        }
        if ($this->companyUnits()->where('code', $request->code)->where('id', '<>', $unit->id)->exists()) {
            return back()->withInput()->with('error', 'Ya existe otra unidad con ese código en la empresa activa.');
        }

        $expiresAt = Carbon::parse($request->expires_at);
        $status = $unit->status;
        $reactivated = $status === 'EXPIRED' && $expiresAt->isFuture();
        if ($status === 'AVAILABLE' && $expiresAt->isPast()) {
            $status = 'EXPIRED';
        } elseif ($reactivated) {
            $status = 'AVAILABLE';
        }

        $unit->update(array_merge($request->only(
            'code', 'blood_type', 'component', 'volume_ml', 'donor_code', 'location', 'collected_at', 'expires_at'
        ), array(
            'status' => $status,
            'updated_by' => auth()->user()->id,
        )));

        return back()->with('success', $reactivated
            ? 'Unidad actualizada y disponible para asignación.'
            : 'Datos de la unidad actualizados correctamente.');
    }

    public function autoAssign($id)
    {
        try {
            $assigned = DB::transaction(function () use ($id) {
                $bloodRequest = $this->companyRequests()->lockForUpdate()->findOrFail($id);
                if (!in_array($bloodRequest->status, array('PENDING', 'COMPATIBILITY'), true)) {
                    throw new \RuntimeException('La solicitud ya no admite nuevas asignaciones.');
                }
                $reserved = $bloodRequest->allocations()->where('status', 'RESERVED')->count();
                $needed = max(0, (int) $bloodRequest->units_required - $reserved);
                if ($needed === 0) throw new \RuntimeException('La solicitud ya tiene todas las unidades requeridas.');

                $units = $this->companyUnits()->where('status', 'AVAILABLE')
                    ->where('component', $bloodRequest->component)
                    ->whereIn('blood_type', $this->compatibleDonorTypes($bloodRequest->blood_type, $bloodRequest->component))
                    ->where('expires_at', '>', Carbon::now())
                    ->orderBy('expires_at')->orderBy('id')->lockForUpdate()->limit($needed)->get();
                foreach ($units as $unit) {
                    $this->reserve($bloodRequest, $unit);
                }
                $newCount = $reserved + $units->count();
                $bloodRequest->update(array(
                    'status' => $newCount >= $bloodRequest->units_required ? 'READY' : 'COMPATIBILITY',
                    'crossmatch_result' => $newCount > 0 ? 'COMPATIBLE' : null,
                    'updated_by' => auth()->user()->id,
                ));
                return $units->count();
            });
            if ($assigned === 0) return back()->with('error', 'No hay unidades compatibles disponibles para esta solicitud.');
            return back()->with('success', $assigned . ' unidad(es) compatible(s) asignada(s) por vencimiento más próximo.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function reserveUnit(Request $request, $id)
    {
        $this->validate($request, array('unit_id' => 'required|integer'));
        try {
            DB::transaction(function () use ($request, $id) {
                $bloodRequest = $this->companyRequests()->lockForUpdate()->findOrFail($id);
                if (!in_array($bloodRequest->status, array('PENDING', 'COMPATIBILITY', 'READY'), true)) {
                    throw new \RuntimeException('La solicitud ya no admite reservas.');
                }
                $reserved = $bloodRequest->allocations()->where('status', 'RESERVED')->count();
                if ($reserved >= $bloodRequest->units_required) throw new \RuntimeException('La solicitud ya tiene todas las unidades requeridas.');

                $unit = $this->companyUnits()->lockForUpdate()->findOrFail($request->unit_id);
                if ($unit->status !== 'AVAILABLE' || $unit->expires_at->isPast()) throw new \RuntimeException('La unidad seleccionada no está disponible.');
                if ($unit->component !== $bloodRequest->component || !self::isCompatible($unit->blood_type, $bloodRequest->blood_type, $unit->component)) {
                    throw new \RuntimeException('La unidad no es compatible con el hemocomponente o grupo del receptor.');
                }

                $this->reserve($bloodRequest, $unit);
                $newCount = $reserved + 1;
                $bloodRequest->update(array(
                    'status' => $newCount >= $bloodRequest->units_required ? 'READY' : 'COMPATIBILITY',
                    'crossmatch_result' => 'COMPATIBLE', 'updated_by' => auth()->user()->id,
                ));
            });
            return back()->with('success', 'Unidad compatible reservada para la solicitud.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function releaseUnit($id, $unitId)
    {
        try {
            DB::transaction(function () use ($id, $unitId) {
                $bloodRequest = $this->companyRequests()->lockForUpdate()->findOrFail($id);
                if (!in_array($bloodRequest->status, array('COMPATIBILITY', 'READY'), true)) {
                    throw new \RuntimeException('La solicitud ya no admite liberación de unidades.');
                }
                $unit = $this->companyUnits()->lockForUpdate()->findOrFail($unitId);
                $allocation = $bloodRequest->allocations()->where('blood_bank_unit_id', $unit->id)
                    ->where('status', 'RESERVED')->lockForUpdate()->first();
                if (!$allocation) throw new \RuntimeException('La unidad no está reservada para esta solicitud.');

                $allocation->update(array('status' => 'RELEASED', 'actor_id' => auth()->user()->id));
                $unit->update(array(
                    'status' => $unit->expires_at->isPast() ? 'EXPIRED' : 'AVAILABLE',
                    'updated_by' => auth()->user()->id,
                ));
                $remaining = $bloodRequest->allocations()->where('status', 'RESERVED')->count();
                $bloodRequest->update(array(
                    'status' => $remaining > 0 ? 'COMPATIBILITY' : 'PENDING',
                    'crossmatch_result' => $remaining > 0 ? 'COMPATIBLE' : null,
                    'updated_by' => auth()->user()->id,
                ));
            });
            return back()->with('success', 'Unidad liberada; volvió al inventario disponible.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function transitionRequest(Request $request, $id)
    {
        $this->validate($request, array('status' => 'required|in:DELIVERED,CANCELLED', 'notes' => 'nullable|string|max:2000'));
        try {
            DB::transaction(function () use ($request, $id) {
                $bloodRequest = $this->companyRequests()->lockForUpdate()->findOrFail($id);
                if ($request->status === 'DELIVERED') {
                    if ($bloodRequest->status !== 'READY') throw new \RuntimeException('Solo una solicitud lista puede entregarse.');
                    $links = $bloodRequest->allocations()->where('status', 'RESERVED')->lockForUpdate()->get();
                    if ($links->count() < $bloodRequest->units_required) throw new \RuntimeException('Faltan unidades compatibles para completar la entrega.');
                    foreach ($links as $link) {
                        $unit = $this->companyUnits()->lockForUpdate()->findOrFail($link->blood_bank_unit_id);
                        if ($unit->status !== 'RESERVED') throw new \RuntimeException('Una unidad asignada ya no se encuentra reservada.');
                        $unit->update(array('status' => 'USED', 'updated_by' => auth()->user()->id));
                        $link->update(array('status' => 'DELIVERED', 'delivered_at' => Carbon::now(), 'actor_id' => auth()->user()->id));
                    }
                    $bloodRequest->update(array('status' => 'DELIVERED', 'delivered_at' => Carbon::now(), 'notes' => $request->notes ?: $bloodRequest->notes, 'updated_by' => auth()->user()->id));
                } else {
                    if ($bloodRequest->status === 'DELIVERED') throw new \RuntimeException('Una entrega confirmada no puede cancelarse.');
                    foreach ($bloodRequest->allocations()->where('status', 'RESERVED')->lockForUpdate()->get() as $link) {
                        $unit = $this->companyUnits()->lockForUpdate()->findOrFail($link->blood_bank_unit_id);
                        $unit->update(array(
                            'status' => $unit->expires_at->isPast() ? 'EXPIRED' : 'AVAILABLE',
                            'updated_by' => auth()->user()->id,
                        ));
                        $link->update(array('status' => 'RELEASED', 'actor_id' => auth()->user()->id));
                    }
                    $bloodRequest->update(array('status' => 'CANCELLED', 'notes' => $request->notes ?: $bloodRequest->notes, 'updated_by' => auth()->user()->id));
                }
            });
            return back()->with('success', $request->status === 'DELIVERED' ? 'Entrega confirmada y existencias actualizadas.' : 'Solicitud cancelada; las unidades fueron liberadas.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function updateUnitStatus(Request $request, $id)
    {
        $this->validate($request, array('status' => 'required|in:AVAILABLE,QUARANTINED,DISCARDED'));
        $unit = $this->companyUnits()->findOrFail($id);
        if (in_array($unit->status, array('RESERVED', 'USED', 'DISCARDED', 'EXPIRED'), true)) return back()->with('error', 'Esta unidad se encuentra en un estado final o bajo reserva y no puede cambiarse manualmente.');
        if ($request->status === 'AVAILABLE' && $unit->expires_at->isPast()) return back()->with('error', 'Una unidad vencida no puede volver a disponibilidad.');
        $unit->update(array('status' => $request->status, 'updated_by' => auth()->user()->id));
        return back()->with('success', 'Estado de la unidad actualizado.');
    }

    public static function isCompatible($donor, $recipient, $component)
    {
        if (in_array($component, array('PLATELETS', 'CRYOPRECIPITATE'), true)) return true;
        $redCells = array(
            'O-' => array('O-'), 'O+' => array('O-', 'O+'), 'A-' => array('O-', 'A-'),
            'A+' => array('O-', 'O+', 'A-', 'A+'), 'B-' => array('O-', 'B-'),
            'B+' => array('O-', 'O+', 'B-', 'B+'), 'AB-' => array('O-', 'A-', 'B-', 'AB-'),
            'AB+' => array('O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'),
        );
        $plasma = array(
            'O-' => array('O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'),
            'O+' => array('O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'),
            'A-' => array('A-', 'A+', 'AB-', 'AB+'), 'A+' => array('A-', 'A+', 'AB-', 'AB+'),
            'B-' => array('B-', 'B+', 'AB-', 'AB+'), 'B+' => array('B-', 'B+', 'AB-', 'AB+'),
            'AB-' => array('AB-', 'AB+'), 'AB+' => array('AB-', 'AB+'),
        );
        $map = $component === 'PLASMA' ? $plasma : $redCells;
        return isset($map[$recipient]) && in_array($donor, $map[$recipient], true);
    }

    private function reserve(BloodBankRequest $bloodRequest, BloodBankUnit $unit)
    {
        BloodBankRequestUnit::create(array(
            'blood_bank_request_id' => $bloodRequest->id, 'blood_bank_unit_id' => $unit->id,
            'status' => 'RESERVED', 'reserved_at' => Carbon::now(), 'actor_id' => auth()->user()->id,
        ));
        $unit->update(array('status' => 'RESERVED', 'updated_by' => auth()->user()->id));
    }

    private function compatibleDonorTypes($recipient, $component)
    {
        return array_values(array_filter($this->bloodTypes(), function ($donor) use ($recipient, $component) {
            return self::isCompatible($donor, $recipient, $component);
        }));
    }

    private function bloodTypes()
    {
        return array('O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+');
    }

    private function patientForCompany($id)
    {
        $patient = Paciente::query()->where('paciente.id', trim((string) $id))->first();
        if (!$patient) {
            throw ValidationException::withMessages(array(
                'patient_id' => 'Seleccione un paciente registrado en la empresa activa.',
            ));
        }
        return $patient;
    }

    private function patientName($patient)
    {
        return trim(implode(' ', array_filter(array(
            $patient->apellido1, $patient->apellido2, $patient->nombre1, $patient->nombre2,
        ), function ($value) { return trim((string) $value) !== ''; })));
    }

    private function available()
    {
        return Schema::hasTable('blood_bank_units') && Schema::hasTable('blood_bank_requests') && Schema::hasTable('blood_bank_request_units');
    }

    private function companyId()
    {
        $companyId = trim((string) session('id_empresa'));
        return $companyId !== '' ? $companyId : null;
    }

    private function requireCompanyId()
    {
        $companyId = $this->companyId();
        if ($companyId === null || $companyId === '') {
            throw ValidationException::withMessages(array(
                'id_empresa' => 'Seleccione una empresa antes de administrar el Banco de sangre.',
            ));
        }
        return $companyId;
    }

    private function companyUnits()
    {
        $query = BloodBankUnit::query();
        return $this->companyId() === null ? $query->whereRaw('1 = 0') : $query->where('id_empresa', $this->companyId());
    }

    private function companyRequests()
    {
        $query = BloodBankRequest::query();
        return $this->companyId() === null ? $query->whereRaw('1 = 0') : $query->where('id_empresa', $this->companyId());
    }
}
