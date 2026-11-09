<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3ClinicalAttentionService;

class ClinicalAttentionController extends Controller
{
    private $attention;

    public function __construct(V3ClinicalAttentionService $attention)
    {
        $this->middleware('auth');
        $this->attention = $attention;
    }

    public function show(Request $request, $agenda)
    {
        $payload = $this->attention->detail((int) $agenda, $this->companyId($request));
        return $this->page('v3.clinical.attention.show', $payload, 'Atención clínica');
    }

    public function update(Request $request, $patient, $agenda)
    {
        $this->validate($request, array(
            'cortesia' => 'required|in:SI,NO',
            'sexo' => 'required|integer|in:1,2',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'estadocivil' => 'nullable|integer|between:1,6',
            'ocupacion' => 'nullable|string|max:100', 'trabajo' => 'nullable|string|max:150',
            'ciudad' => 'nullable|string|max:150', 'lugar_nacimiento' => 'nullable|string|max:150',
            'direccion' => 'nullable|string|max:255', 'telefono1' => 'required|string|max:60',
            'telefono2' => 'nullable|string|max:60', 'mail_opcional' => 'nullable|email|max:191',
            'referido' => 'nullable|string|max:150', 'alcohol' => 'nullable|string|max:150',
            'gruposanguineo' => 'nullable|string|max:20', 'transfusion' => 'required|in:SI,NO',
            'observacion' => 'nullable|string|max:1000', 'observacion_admin' => 'nullable|string|max:3000',
            'vacuna' => 'nullable|string|max:1000', 'antecedentes_pat' => 'nullable|string|max:3000',
            'antecedentes_fam' => 'nullable|string|max:3000', 'antecedentes_quir' => 'nullable|string|max:3000',
            'ale_list' => 'nullable|array', 'ale_list.*' => 'integer|exists:principio_activo,id',
        ));
        $this->attention->update($request, (int) $agenda, $this->companyId($request), (string) $patient);
        return redirect()->route('v3.clinical-attention.show', array('agenda' => (int) $agenda))
            ->with('clinical_attention_success', 'La filiación clínica se actualizó correctamente.');
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function page($view, array $payload, $title)
    {
        $data = array(
            'controlador' => 'pacientes-dia', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a atención clínica V3', 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data') ?: array(), $data)));
        return view('v3.layouts.view_child');
    }
}
