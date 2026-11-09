<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\HospitalServiceMenuService;

class HospitalServiceConfigurationController extends Controller
{
    private $services;

    public function __construct(HospitalServiceMenuService $services)
    {
        $this->middleware('auth');
        $this->services = $services;
    }

    public function index()
    {
        $this->authorizeConfiguration();
        $company = $this->selectedCompany();

        return view('hospital.configuration.services', array(
            'company' => $company,
            'services' => $this->services->forCompany($company->id),
            'configurationInstalled' => $this->services->isInstalled(),
        ));
    }

    public function update(Request $request)
    {
        $this->authorizeConfiguration();
        $company = $this->selectedCompany();
        $enabled = $request->input('enabled', array());
        if (!is_array($enabled)) abort(422, 'La selección de servicios no es válida.');

        $this->services->saveForCompany(
            $company->id,
            array_values(array_unique(array_map('strval', $enabled))),
            Auth::id(),
            $request->ip()
        );

        return redirect()->route('hospital.services.configuration')
            ->with('success', 'Los servicios visibles de la empresa se actualizaron correctamente.');
    }

    private function authorizeConfiguration()
    {
        abort_unless(Auth::user() && Auth::user()->isSystemUser(), 403,
            'Solo el usuario de Sistemas puede configurar los servicios hospitalarios por empresa.');
    }

    private function selectedCompany()
    {
        $companyId = $this->services->companyId();
        abort_if($companyId === '', 422, 'Seleccione una empresa antes de configurar los servicios hospitalarios.');

        $company = Empresa::find($companyId);
        abort_if(is_null($company), 404, 'La empresa seleccionada ya no existe.');
        return $company;
    }
}
