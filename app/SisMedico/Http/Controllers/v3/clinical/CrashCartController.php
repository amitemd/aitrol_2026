<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3AuthorizationService;
use Sis_medico\Services\V3CrashCartService;
use Sis_medico\Services\V3CrashCartPdf;

class CrashCartController extends Controller
{
    private $carts;

    public function __construct(V3CrashCartService $carts) { $this->middleware('auth'); $this->carts = $carts; }

    public function index(Request $request)
    {
       
        $ready = $this->ready();
        $filters = $this->carts->filters($request->only('mes', 'bodega', 'tipo', 'responsable', 'servicio'));
        $company = $this->company($request);
        return $this->page('index', array(
            'ready' => $ready, 'filters' => $filters, 'records' => $ready ? $this->carts->records($company, $filters) : null,
            'warehouses' => $this->carts->warehouses($company), 'users' => $this->carts->responsibleUsers($company),
            'permissions' => $this->permissions($request),
        ), 'Carro de paro');
    }

    public function create(Request $request)
    {
        $this->requireReady();
        $this->validate($request, array('id_bodega' => 'nullable|integer|min:1', 'tipo' => 'nullable|in:1,2',
            'fecha' => 'nullable|date_format:Y-m-d|before_or_equal:today', 'servicio' => 'nullable|string|max:100'));
        $company = $this->company($request);
        $record = (object) array('id' => null, 'id_bodega' => $request->input('id_bodega', ''),
            'tipo' => $request->input('tipo') ?: 1, 'fecha' => $request->input('fecha') ?: date('Y-m-d'),
            'servicio' => mb_strtoupper(trim($request->input('servicio') ?: 'GASTROENTEROLOGÍA'), 'UTF-8'),
            'responsable_id' => Auth::id(), 'observacion' => '', 'version' => 1, 'estado' => 1);
        $historical = $record->fecha < date('Y-m-d');
        $details = collect();
        if ($record->id_bodega) {
            $existing = $this->carts->existing($company, (array) $record);
            if ($existing) return redirect()->route('v3.crash-cart.detail', array('control' => $existing));
            $details = $this->carts->inventory($company, $record->id_bodega, $record->tipo, $historical);
        }
        return $this->page('form', array('record' => $record, 'details' => $details,
            'warehouses' => $this->carts->warehouses($company), 'users' => $this->carts->responsibleUsers($company),
            'historical' => $historical, 'carts' => $this->carts, 'permissions' => $this->permissions($request)), 'Registrar control de carro de paro');
    }

    public function detail(Request $request, $control)
    {
        $this->requireReady();
        $company = $this->company($request);
        $payload = $this->carts->detail($company, $control);
        $payload['events'] = $this->carts->events($company, $control);
        $payload['permissions'] = $this->permissions($request);
        $payload['carts'] = $this->carts;
        return $this->page('detail', $payload, 'Control de carro de paro #'.$control);
    }

    public function edit(Request $request, $control)
    {
        $this->requireReady();
        $company = $this->company($request);
        $payload = $this->carts->detail($company, $control);
        abort_if((int) $payload['record']->estado !== 1, 409, 'El control está anulado.');
        $payload['historical'] = $payload['record']->reference_basis === 'declared';
        $payload['warehouses'] = $this->carts->warehouses($company);
        $payload['users'] = $this->carts->responsibleUsers($company);
        $payload['carts'] = $this->carts;
        $payload['permissions'] = $this->permissions($request);
        return $this->page('form', $payload, 'Corregir control de carro de paro #'.$control);
    }

    public function store(Request $request) { return $this->save($request); }
    public function update(Request $request, $control) { return $this->save($request, $control); }

    private function save(Request $request, $control = null)
    {
        $this->requireReady();
        $id = $this->carts->save($this->company($request), Auth::id(), $request->ip(), $request->all(), $control);
        $url = route('v3.crash-cart.detail', array('control' => $id));
        if ($request->expectsJson()) return response()->json(array('message' => 'Control guardado correctamente.', 'redirect' => $url), $control === null ? 201 : 200);
        return redirect($url)->with('success', 'Control guardado correctamente.');
    }

    public function cancel(Request $request, $control)
    {
        $this->requireReady();
        $this->carts->cancel($this->company($request), $control, Auth::id(), $request->ip(), $request->all());
        return redirect()->route('v3.crash-cart.detail', array('control' => $control))->with('success', 'Control anulado. Su historial se conserva.');
    }

    public function report(Request $request) { return $this->page('report', $this->reportData($request), 'Reporte mensual de carro de paro'); }

    public function pdf(Request $request)
    {
        $data = $this->reportData($request);
        return response(app(V3CrashCartPdf::class)->render($data), 200, array('Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="carro-paro-'.$data['filters']['mes'].'.pdf"'));
    }

    public function excel(Request $request)
    {
        $data = $this->reportData($request);
        $book = new \PHPExcel();
        $sheet = $book->getActiveSheet()->setTitle('Control mensual');
        $headers = array('Bodega', 'Servicio', 'Tipo', 'Producto', 'Lote', 'Serie', 'Caducidad');
        for ($day = 1; $day <= $data['daysInMonth']; $day++) $headers[] = (string) $day;
        $this->excelRow($sheet, 1, $headers);
        $row = 2;
        foreach ($data['matrix'] as $entry) {
            $values = array($entry['record']->bodega_nombre, $entry['record']->servicio, $entry['record']->tipo == 1 ? 'Medicamentos' : 'Insumos',
                $entry['line']->producto_nombre, $entry['line']->lote, $entry['line']->serie, $entry['line']->fecha_caducidad ?: 'Sin fecha');
            for ($day = 1; $day <= $data['daysInMonth']; $day++) $values[] = isset($entry['days'][$day]) ? $entry['days'][$day]['count'] : '';
            $this->excelRow($sheet, $row++, $values);
        }
        $detailSheet = $book->createSheet()->setTitle('Detalle y diferencias');
        $this->excelRow($detailSheet, 1, array('Fecha', 'Bodega', 'Servicio', 'Tipo', 'Responsable', 'Producto', 'Lote', 'Serie', 'Presentación', 'Concentración', 'Vencimiento', 'Referencia', 'Conteo', 'Diferencia', 'Observación', 'Origen referencia'));
        $row = 2;
        foreach ($data['records'] as $record) {
            foreach ($data['reportDetails']->get($record->id, collect()) as $line) {
                $this->excelRow($detailSheet, $row++, array($record->fecha, $record->bodega_nombre, $record->servicio, $record->tipo == 1 ? 'Medicamentos' : 'Insumos',
                    $record->responsable_nombre, $line->producto_nombre, $line->lote, $line->serie, $line->presentacion, $line->concentracion, $line->fecha_caducidad,
                    $line->referencia_cantidad, $line->stock, $line->referencia_cantidad - $line->stock, $line->observacion, $record->reference_basis === 'declared' ? 'Declarada' : 'Inventario'));
            }
        }
        foreach ($book->getAllSheets() as $page) {
            $page->freezePane('G2');
            $page->getStyle('A1:'.$page->getHighestColumn().'1')->getFont()->setBold(true);
            for ($col = 0; $col < 6; $col++) $page->getColumnDimensionByColumn($col)->setWidth($col === 3 ? 36 : 22);
        }
        $sheet->freezePane('H2');
        $sheet->getColumnDimension('G')->setWidth(14);
        return response()->streamDownload(function () use ($book) {
            \PHPExcel_IOFactory::createWriter($book, 'Excel2007')->save('php://output');
            $book->disconnectWorksheets();
        }, 'carro-paro-'.$data['filters']['mes'].'.xlsx', array('Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
    }

    private function excelRow($sheet, $row, array $values)
    {
        foreach ($values as $col => $value) $sheet->setCellValueExplicitByColumnAndRow($col, $row, (string) $value, \PHPExcel_Cell_DataType::TYPE_STRING);
    }

    private function reportData(Request $request)
    {
        $this->requireReady();
        $filters = $this->carts->filters($request->only('mes', 'bodega', 'tipo', 'responsable', 'servicio'));
        $data = $this->carts->monthlyReport($this->company($request), $filters);
        $ids = $data['records']->keys()->all();
        $data['reportDetails'] = $ids ? DB::table(V3CrashCartService::DETAILS)->whereIn('control_id', $ids)->orderBy('producto_nombre')->get()->groupBy('control_id') : collect();
        $data['permissions'] = $this->permissions($request);
        $data['companyName'] = DB::table('empresa')->where('id', $this->company($request))->value('nombrecomercial');
        $data['signatures'] = $this->signatures($data['records']->pluck('responsable_id')->unique()->all());
        return $data;
    }

    private function signatures(array $userIds)
    {
        $result = array();
        if (!$userIds || !Schema::hasTable('firma_usuario')) return $result;
        $root = realpath(storage_path('app/avatars'));
        if (!$root) return $result;
        foreach (DB::table('firma_usuario')->whereIn('id_usuario', $userIds)->orderBy('id')->get(array('id_usuario', 'nombre')) as $signature) {
            $name = (string) $signature->nombre;
            if ($name === '' || basename(str_replace('\\', '/', $name)) !== $name) continue;
            $path = realpath($root.DIRECTORY_SEPARATOR.$name);
            if (!$path || strpos($path, $root.DIRECTORY_SEPARATOR) !== 0 || !is_file($path) || filesize($path) > 2 * 1024 * 1024) continue;
            $mime = mime_content_type($path);
            if (!in_array($mime, array('image/png', 'image/jpeg', 'image/gif'), true)) continue;
            $result[$signature->id_usuario] = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
        }
        return $result;
    }

    private function permissions(Request $request)
    {
        $result = array();
        foreach (array('create' => array('POST', 'guardar'), 'update' => array('PATCH', 'actualizar/1'), 'delete' => array('POST', 'anular/1'), 'export' => array('GET', 'pdf')) as $action => $probe) {
            $check = Request::create('/v3/carro-paro/'.$probe[1], $probe[0]);
            $check->setLaravelSession($request->session());
            $check->attributes->add($request->attributes->all());
            $result[$action] = app(V3AuthorizationService::class)->authorize($check, Auth::user());
        }
        return $result;
    }

    private function ready() { return Schema::hasTable(V3CrashCartService::CONTROLS) && Schema::hasTable(V3CrashCartService::DETAILS) && Schema::hasTable(V3CrashCartService::EVENTS); }
    private function requireReady() { abort_unless($this->ready(), 503, 'El módulo de carro de paro está pendiente de instalación. Contacte a Sistemas.'); }
    private function company(Request $request) { $id = trim((string) $request->session()->get('id_empresa')); abort_if($id === '', 403, 'Seleccione una empresa activa.'); return $id; }
    private function page($view, array $payload, $title)
    {
        $view = 'v3.clinical.crash_cart.'.$view;
        $data = array('controlador' => 'carro-paro', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload));
        config(array('data' => array_merge((array) config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
