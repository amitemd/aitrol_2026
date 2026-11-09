<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3PendingBiopsyService;

class PendingBiopsyController extends Controller
{
    private $biopsies;

    public function __construct(V3PendingBiopsyService $biopsies)
    {
        $this->middleware('auth');
        $this->biopsies = $biopsies;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('buscar', ''));
        return $this->page('v3.clinical.pending_biopsies.index', array(
            'search' => $search,
            'biopsies' => $this->biopsies->paginate($search, $this->companyId($request)),
        ), 'Biopsias faltantes');
    }

    public function create()
    {
        return $this->page('v3.clinical.pending_biopsies.create', array(), 'Carga de biopsias');
    }

    public function store(Request $request)
    {
        try {
            $files = $request->file('archivos', array());
            if ($files && !is_array($files)) $files = array($files);
            $result = $this->biopsies->upload($files, $request->input('id_paciente'), $this->companyId($request), auth()->user()->id, $request->ip());
            $redirect = redirect()->route('v3.pending-biopsies.create');
            if ($result['saved']) $redirect->with('success', $result['saved'].' archivo(s) cargado(s) correctamente.');
            if ($result['errors']) $redirect->with('upload_errors', $result['errors']);
            return $redirect;
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->withErrors(array($exception->getMessage()));
        }
    }

    public function download(Request $request, $id)
    {
        return $this->biopsies->download($id, $this->companyId($request));
    }

    public function discard(Request $request, $id)
    {
        $this->biopsies->discard($id, $this->companyId($request), auth()->user()->id, $request->ip());
        return redirect()->back()->with('success', 'La biopsia fue retirada del listado sin eliminar el archivo de auditoría.');
    }

    public function legacyIndex()
    {
        return redirect()->route('v3.pending-biopsies.index');
    }

    public function legacyCreate()
    {
        return redirect()->route('v3.pending-biopsies.create');
    }

    public function legacySearch(Request $request)
    {
        return redirect()->route('v3.pending-biopsies.index', array('buscar' => $request->input('nombre')));
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
            'controlador' => 'biopsias-faltantes', 'carpeta' => 'clinical',
            'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title,
            'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
