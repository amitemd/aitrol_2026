<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Hospital;
use Sis_medico\Sala;

class SalaManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listasalas(Request $request, int $hospital): View
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospitalModel = $this->hospitalForCompany($hospital, $companyId);
        $rooms = Sala::query()
            ->forHospitalCompany($hospitalModel->id, $companyId)
            ->orderBy('nombre_sala')
            ->paginate(10);

        return view('sala-mgmt.listasalas', [
            'salas' => $rooms,
            'hospital' => $hospitalModel,
        ]);
    }

    public function crear(Request $request, int $hospital): View
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);

        return view('sala-mgmt.crearsala', [
            'hospital' => $this->hospitalForCompany($hospital, $companyId),
        ]);
    }

    public function grabar(Request $request, int $hospital): RedirectResponse
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospitalModel = $this->hospitalForCompany($hospital, $companyId);
        $data = $this->validatedRoom($request);

        Sala::query()->create([
            'id_empresa' => $companyId,
            'nombre_sala' => mb_strtoupper($data['nombre_sala'], 'UTF-8'),
            'id_hospital' => $hospitalModel->id,
            'estado' => 1,
            'ip_creacion' => $request->ip(),
            'ip_modificacion' => $request->ip(),
            'id_usuariocrea' => (string) Auth::id(),
            'id_usuariomod' => (string) Auth::id(),
        ]);

        return redirect()
            ->route('sala-management.listasalas', ['hospital' => $hospitalModel->id])
            ->with('success', 'La sala se creó para la empresa activa.');
    }

    public function editar(Request $request, int $hospital, int $sala): View
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospitalModel = $this->hospitalForCompany($hospital, $companyId);

        return view('sala-mgmt.editar', [
            'hospital' => $hospitalModel,
            'salas' => $this->roomForCompany($sala, $hospitalModel->id, $companyId),
        ]);
    }

    public function actualizar(Request $request, int $hospital, int $sala): RedirectResponse
    {
        $this->authorizeManagement();
        $companyId = $this->companyId($request);
        $hospitalModel = $this->hospitalForCompany($hospital, $companyId);
        $room = $this->roomForCompany($sala, $hospitalModel->id, $companyId);
        $data = $this->validatedRoom($request, true);

        $room->update([
            'nombre_sala' => mb_strtoupper($data['nombre_sala'], 'UTF-8'),
            'estado' => (int) $data['estado'],
            'ip_modificacion' => $request->ip(),
            'id_usuariomod' => (string) Auth::id(),
        ]);

        return redirect()
            ->route('sala-management.listasalas', ['hospital' => $hospitalModel->id])
            ->with('success', 'La sala de la empresa activa se actualizó correctamente.');
    }

    /** @return array{nombre_sala: string, estado?: int} */
    private function validatedRoom(Request $request, bool $updating = false): array
    {
        $rules = ['nombre_sala' => ['required', 'string', 'max:80']];
        if ($updating) {
            $rules['estado'] = ['required', 'integer', 'in:0,1'];
        }

        return $request->validate($rules, [
            'nombre_sala.required' => 'Agrega el nombre de la sala.',
            'nombre_sala.max' => 'El nombre de la sala no puede ser mayor a :max caracteres.',
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

    private function roomForCompany(int $roomId, int $hospitalId, string $companyId): Sala
    {
        return Sala::query()
            ->forHospitalCompany($hospitalId, $companyId)
            ->whereKey($roomId)
            ->firstOrFail();
    }

    private function companyId(Request $request): string
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        abort_if($companyId === '', 403, 'Selecciona una empresa antes de administrar sus salas.');

        return $companyId;
    }

    private function authorizeManagement(): void
    {
        abort_unless(in_array((int) Auth::user()->id_tipo_usuario, [1, 4], true), 404);
    }
}
