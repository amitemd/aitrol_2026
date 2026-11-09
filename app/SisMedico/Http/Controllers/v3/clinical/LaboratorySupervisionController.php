<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3LaboratorySupervisionService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratorySupervisionController extends Controller
{
    private $supervision;

    public function __construct(V3LaboratorySupervisionService $supervision)
    {
        $this->middleware('auth');
        $this->supervision = $supervision;
    }

    public function index(Request $request)
    {
        $filters = $this->supervision->filters($request);
        $orders = $this->supervision->paginate($filters, $this->companyId($request));
        return $this->page(array(
            'filters' => $filters, 'orders' => $orders, 'insurances' => $this->supervision->insurances(),
            'summary' => $this->supervision->summary($orders),
        ));
    }

    public function export(Request $request, $type)
    {
        if (!in_array($type, array('resumen', 'estadistico', 'detalle', 'pendientes'), true)) abort(404);
        if ($type === 'resumen' && !$this->mayExportSummary()) abort(403);
        if ($type === 'estadistico' && !$this->mayExportStatistics()) abort(403);
        $filters = $this->supervision->filters($request);
        $rows = $this->supervision->all($filters, $this->companyId($request));
        if (in_array($type, array('resumen', 'estadistico'), true)) {
            $rows = $rows->filter(function ($row) { return (int) $row->realizado === 1; })->values();
        }
        if ($type === 'pendientes') {
            $rows = $rows->filter(function ($row) { return (int) $row->parameter_count > (int) $row->certified_count; })->values();
        }
        if ($type === 'estadistico') return $this->statisticsCsv($rows);
        return $this->ordersCsv($rows, $type);
    }

    public function coupons() { return redirect()->route('orden_labs.cuponera_index'); }
    public function commissions() { return redirect()->route('labscomisiones.comisiones'); }
    public function results($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->imprimir_resultado($id); }
    public function document($id)
    {
        $order = $this->owned($id);
        if ((int) $order->insurance_type !== 0) return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->cotizador_imprimir($id);
        return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->detalle($id, 'sup');
    }
    public function values($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->detalle_valor($id); }

    private function ordersCsv($rows, $type)
    {
        return $this->csv('supervision-laboratorio-'.$type, function ($out) use ($rows, $type) {
            $headers = array('ORDEN','FECHA','PACIENTE','SEGURO','TIPO','CANTIDAD','SUBTOTAL','DESCUENTO','RECARGO','TOTAL','PARAMETROS','CERTIFICADOS');
            if ($type === 'detalle') $headers[] = 'EXAMENES';
            fputcsv($out, $headers, ';');
            foreach ($rows as $row) {
                $data = array($row->id,$row->fecha_orden,$this->patient($row),$row->insurance_name,$row->pre_post,$row->exam_count,$row->valor,$row->descuento_valor,$row->recargo_valor,$row->total_valor,$row->parameter_count,$row->certified_count);
                if ($type === 'detalle') $data[] = $row->exam_names;
                fputcsv($out, array_map(array($this, 'safeCsv'), $data), ';');
            }
        });
    }

    private function statisticsCsv($rows)
    {
        $statistics = array();
        foreach ($rows as $row) {
            $key = substr($row->fecha_orden, 0, 7);
            if (!isset($statistics[$key])) $statistics[$key] = array('orders'=>0,'exams'=>0,'subtotal'=>0,'total'=>0);
            $statistics[$key]['orders']++; $statistics[$key]['exams'] += (int) $row->exam_count;
            $statistics[$key]['subtotal'] += (float) $row->valor; $statistics[$key]['total'] += (float) $row->total_valor;
        }
        return $this->csv('estadistico-examenes', function ($out) use ($statistics) {
            fputcsv($out, array('PERIODO','ORDENES','EXAMENES','SUBTOTAL','TOTAL'), ';');
            foreach ($statistics as $period => $values) fputcsv($out, array($period,$values['orders'],$values['exams'],$values['subtotal'],$values['total']), ';');
        });
    }

    private function csv($name, $callback)
    {
        $response = new StreamedResponse(function () use ($callback) { $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF"); $callback($out); fclose($out); });
        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename="'.$name.'-'.date('Ymd-His').'.csv"');
        return $response;
    }

    public function safeCsv($value) { $value=(string)$value; return preg_match('/^[=+\-@]/',ltrim($value)) ? "'".$value : $value; }
    private function patient($row) { return trim($row->apellido1.' '.$row->apellido2.' '.$row->nombre1.' '.$row->nombre2); }
    private function mayExportSummary() { return in_array((string)\Auth::user()->id_tipo_usuario,array('1','11','12','22'),true)||(string)\Auth::user()->id==='1307189140'; }
    private function mayExportStatistics() { return in_array((string)\Auth::user()->id_tipo_usuario,array('1','12','16','22'),true)||(string)\Auth::user()->id==='1307189140'; }
    private function owned($id)
    {
        $row=\DB::table('examen_orden as eo')->leftJoin('seguros as s','s.id','=','eo.id_seguro')->where('eo.id',(int)$id)->where('eo.id_empresa',session()->get('id_empresa'))->select('eo.*','s.tipo as insurance_type')->first();
        if(!$row) abort(404); return $row;
    }
    private function companyId(Request $request) { $id=trim((string)$request->session()->get('id_empresa')); if($id==='') abort(403,'Seleccione una empresa activa.'); return $id; }
    private function page(array $payload)
    {
        $view='v3.clinical.laboratory_supervision.index';
        $data=array('controlador'=>'supervision-laboratorio','carpeta'=>'clinical','submodulo'=>'index','submodulo_'=>'index','menu_id'=>673,'titulo'=>'Supervisión de laboratorio','title'=>'Supervisión de laboratorio | Aitrol Hospitalario','vista'=>$view,'obseraudi'=>'Supervisión operativa y económica de órdenes de laboratorio V3','contenido'=>view($view,$payload));
        config(array('data'=>array_merge(config('data'),$data))); return view('v3.layouts.view_child');
    }
}
