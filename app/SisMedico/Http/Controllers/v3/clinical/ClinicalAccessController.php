<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\ClinicalBreakGlassAccess;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalAccessControlService;

class ClinicalAccessController extends Controller
{
    private $service;

    public function __construct(ClinicalAccessControlService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if (! $this->service->available()) {
            return redirect()->route('new-system.dashboard')->with('error', 'Aplique la migración de acceso clínico.');
        }
        $user = auth()->user();
        $isReviewer = in_array((int) $user->id_tipo_usuario, [1, 3], true);
        $encounterId = trim((string) $request->input('encounter_id'));
        $query = ClinicalBreakGlassAccess::orderBy('id', 'desc');
        if (! $isReviewer) {
            $query->where('user_id', $user->id);
        }
        if ($encounterId !== '') {
            $query->where('encounter_id', $encounterId);
        }

        return $this->page([
            'accesses' => $query->paginate(30),
            'encounterId' => $encounterId,
            'isReviewer' => $isReviewer,
        ]);
    }

    private function page(array $payload)
    {
        $view = 'v3.clinical.access.index';
        $data = [
            'controlador' => 'accesos-clinicos', 'carpeta' => 'clinical',
            'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => 'Accesos clínicos excepcionales',
            'title' => 'Accesos clínicos excepcionales | Aitrol Hospitalario',
            'vista' => $view, 'obseraudi' => 'Ingreso a accesos clínicos excepcionales',
            'contenido' => view($view, $payload),
        ];
        config(['data' => array_merge(config('data') ?: [], $data)]);

        return view('v3.layouts.view_child');
    }
}
