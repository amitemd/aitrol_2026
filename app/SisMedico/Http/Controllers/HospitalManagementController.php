<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Sis_medico\Hospital;

class HospitalManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $search = trim((string) $request->input('nombreunidad'));

        $hospitals = Hospital::query()
            ->forCompany($companyId)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nombre_hospital', 'like', '%'.$search.'%');
            })
            ->orderBy('nombre_hospital')
            ->paginate(10)
            ->withQueryString();

        $unassignedHospitals = collect();
        if ($this->isSystemsUser()) {
            $unassignedHospitals = Hospital::query()
                ->where(function ($query): void {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where('nombre_hospital', 'like', '%'.$search.'%');
                })
                ->orderBy('nombre_hospital')
                ->limit(50)
                ->get();
        }

        return view('hospital-mgmt.index', [
            'hospitales' => $hospitals,
            'unassignedHospitals' => $unassignedHospitals,
            'defaultHospitalId' => $this->defaultHospitalId($companyId),
            'searchingVals' => ['nombre_hospital' => $search],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeManagement();
        $this->companyId($request);

        return view('hospital-mgmt.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $data = $this->validatedHospital($request, $companyId);
        $actorId = (string) Auth::id();

        $hospital = DB::transaction(function () use ($request, $companyId, $data, $actorId): Hospital {
            $hospital = Hospital::query()->create([
                'id_empresa' => $companyId,
                'nombre_hospital' => mb_strtoupper($data['nombre_hospital'], 'UTF-8'),
                'ciudad' => mb_strtoupper($data['ciudad'], 'UTF-8'),
                'direccion' => mb_strtoupper($data['direccion'], 'UTF-8'),
                'telefono1' => $data['telefono1'],
                'estado' => 1,
                'ip_creacion' => $request->ip(),
                'ip_modificacion' => $request->ip(),
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
            ]);

            $makeDefault = $request->boolean('is_default') || $this->defaultHospitalId($companyId) === null;
            $this->syncHospitalContext($companyId, $hospital, $makeDefault);

            return $hospital;
        });

        return redirect()
            ->route('hospital-management.edit', ['hospital_management' => $hospital->id])
            ->with('success', 'La unidad se creó para la empresa activa.');
    }

    public function edit(Request $request, int $hospital_management): View
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospital = $this->hospitalForCompany($hospital_management, $companyId);

        return view('hospital-mgmt.edit', [
            'hospitales' => $hospital,
            'isDefault' => $this->defaultHospitalId($companyId) === (int) $hospital->id,
        ]);
    }

    public function update(Request $request, int $hospital_management): RedirectResponse
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospital = $this->hospitalForCompany($hospital_management, $companyId);
        $data = $this->validatedHospital($request, $companyId, $hospital);

        DB::transaction(function () use ($request, $companyId, $hospital, $data): void {
            $hospital->update([
                'nombre_hospital' => mb_strtoupper($data['nombre_hospital'], 'UTF-8'),
                'ciudad' => mb_strtoupper($data['ciudad'], 'UTF-8'),
                'direccion' => mb_strtoupper($data['direccion'], 'UTF-8'),
                'telefono1' => $data['telefono1'],
                'estado' => (int) $data['estado'],
                'ip_modificacion' => $request->ip(),
                'id_usuariomod' => (string) Auth::id(),
            ]);

            $this->syncHospitalContext($companyId, $hospital, false);
        });

        return redirect()
            ->route('hospital-management.index')
            ->with('success', 'La unidad de la empresa activa se actualizó correctamente.');
    }

    public function search(Request $request): RedirectResponse
    {
        $this->authorizeManagement();
        $this->companyId($request);

        return redirect()->route('hospital-management.index', [
            'nombreunidad' => trim((string) $request->input('nombreunidad')),
        ]);
    }

    public function setDefault(Request $request, int $hospital): RedirectResponse
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospitalModel = $this->hospitalForCompany($hospital, $companyId);

        abort_unless((int) $hospitalModel->estado === 1, 409, 'La unidad debe estar activa para usarla como hospital predeterminado.');

        DB::transaction(function () use ($companyId, $hospitalModel): void {
            $this->syncHospitalContext($companyId, $hospitalModel, true);
        });

        $request->session()->put('id_hospital', (int) $hospitalModel->id);
        $request->session()->put('hospital_company_id', $companyId);

        return back()->with('success', 'El hospital predeterminado de la empresa fue actualizado.');
    }

    public function assign(Request $request, int $hospital): RedirectResponse
    {
        abort_unless($this->isSystemsUser(), 404);
        $companyId = $this->companyId($request);

        DB::transaction(function () use ($companyId, $hospital): void {
            $hospitalModel = Hospital::query()
                ->where('id', $hospital)
                ->where(function ($query): void {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })
                ->lockForUpdate()
                ->firstOrFail();

            $hospitalModel->update(['id_empresa' => $companyId]);
            DB::table('sala')
                ->where('id_hospital', $hospitalModel->id)
                ->where(function ($query): void {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })
                ->update(['id_empresa' => $companyId]);
            DB::table('company_hospital_contexts')
                ->where('hospital_id', $hospitalModel->id)
                ->where('company_id', '<>', $companyId)
                ->update(['active' => 0, 'is_default' => 0, 'updated_at' => now()]);

            $this->syncHospitalContext(
                $companyId,
                $hospitalModel,
                $this->defaultHospitalId($companyId) === null
            );
        });

        return back()->with('success', 'La unidad heredada y sus salas se asignaron a la empresa activa.');
    }

    /**
     * @return array{nombre_hospital: string, ciudad: string, direccion: string, telefono1: string, estado?: int}
     */
    private function validatedHospital(Request $request, string $companyId, ?Hospital $hospital = null): array
    {
        $nameRule = Rule::unique('hospital', 'nombre_hospital')
            ->where(fn ($query) => $query->where('id_empresa', $companyId));

        if ($hospital !== null) {
            $nameRule->ignore($hospital->id);
        }

        $rules = [
            'nombre_hospital' => ['required', 'string', 'max:100', $nameRule],
            'ciudad' => ['required', 'string', 'max:60'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono1' => ['required', 'digits_between:7,10'],
        ];

        if ($hospital !== null) {
            $rules['estado'] = ['required', 'integer', 'in:0,1'];
        }

        return $request->validate($rules, [
            'nombre_hospital.required' => 'Agrega el nombre del hospital.',
            'nombre_hospital.max' => 'El nombre del hospital no puede ser mayor a :max caracteres.',
            'nombre_hospital.unique' => 'Esta empresa ya tiene una unidad con ese nombre.',
            'ciudad.required' => 'Agrega la ciudad.',
            'direccion.required' => 'Agrega la dirección.',
            'telefono1.required' => 'Agrega el teléfono.',
            'telefono1.digits_between' => 'El teléfono debe tener entre 7 y 10 dígitos.',
            'estado.required' => 'Agrega el estado.',
        ]);
    }

    private function hospitalForCompany(int $hospitalId, string $companyId): Hospital
    {
        return Hospital::query()
            ->forCompany($companyId)
            ->whereKey($hospitalId)
            ->firstOrFail();
    }

    private function syncHospitalContext(string $companyId, Hospital $hospital, bool $makeDefault): void
    {
        $isActive = (int) $hospital->estado === 1;
        $wasDefault = DB::table('company_hospital_contexts')
            ->where('company_id', $companyId)
            ->where('hospital_id', $hospital->id)
            ->where('is_default', 1)
            ->exists();

        if ($makeDefault && $isActive) {
            DB::table('company_hospital_contexts')
                ->where('company_id', $companyId)
                ->update(['is_default' => 0, 'updated_at' => now()]);
        }

        $hasDefault = DB::table('company_hospital_contexts')
            ->where('company_id', $companyId)
            ->where('active', 1)
            ->where('is_default', 1)
            ->where('hospital_id', '<>', $hospital->id)
            ->exists();

        $this->upsertHospitalContext($companyId, (int) $hospital->id, [
            'active' => $isActive,
            'is_default' => $isActive && ($makeDefault || ! $hasDefault),
        ]);

        if (! $isActive && $wasDefault) {
            $replacement = Hospital::query()
                ->forCompany($companyId)
                ->where('estado', 1)
                ->where('id', '<>', $hospital->id)
                ->orderBy('nombre_hospital')
                ->first();

            if ($replacement !== null) {
                $this->upsertHospitalContext($companyId, (int) $replacement->id, [
                    'active' => true,
                    'is_default' => true,
                ]);
            }
        }
    }

    /** @param array{active: bool, is_default: bool} $values */
    private function upsertHospitalContext(string $companyId, int $hospitalId, array $values): void
    {
        $query = DB::table('company_hospital_contexts')
            ->where('company_id', $companyId)
            ->where('hospital_id', $hospitalId);
        $attributes = [
            'active' => $values['active'],
            'is_default' => $values['is_default'],
            'updated_at' => now(),
        ];

        if ($query->exists()) {
            $query->update($attributes);

            return;
        }

        DB::table('company_hospital_contexts')->insert([
            'company_id' => $companyId,
            'hospital_id' => $hospitalId,
            ...$attributes,
            'created_at' => now(),
        ]);
    }

    private function defaultHospitalId(string $companyId): ?int
    {
        $hospitalId = DB::table('company_hospital_contexts')
            ->where('company_id', $companyId)
            ->where('active', 1)
            ->where('is_default', 1)
            ->value('hospital_id');

        return $hospitalId === null ? null : (int) $hospitalId;
    }

    private function companyId(Request $request): string
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        abort_if($companyId === '', 403, 'Selecciona una empresa antes de administrar hospitales y ambulatorios.');

        return $companyId;
    }

    private function authorizeManagement(): void
    {
        abort_unless(in_array((int) Auth::user()->id_tipo_usuario, [1, 4], true), 404);
    }

    private function isSystemsUser(): bool
    {
        return (int) Auth::user()->id_tipo_usuario === 1;
    }
}
