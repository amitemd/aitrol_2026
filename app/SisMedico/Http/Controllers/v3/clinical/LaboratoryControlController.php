<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3LaboratoryControlService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratoryControlController extends Controller
{
    private $control;
    public function __construct(V3LaboratoryControlService $control) { $this->middleware('auth'); $this->control = $control; }

    public function index(Request $request)
    {
        $filters = $this->control->filters($request); $orders = $this->control->paginate($filters, $this->companyId($request));
        return $this->page(array('filters'=>$filters, 'orders'=>$orders, 'insurances'=>$this->control->insurances(), 'summary'=>$this->control->summary($orders)));
    }

    public function export(Request $request, $type)
    {
        if (!in_array($type, array('general','pendientes','covid'), true)) abort(404);
        $filters = $this->control->filters($request); $companyId = $this->companyId($request);
        $rows = $type === 'covid' ? $this->control->covid($filters, $companyId) : $this->control->all($filters, $companyId);
        if ($type === 'pendientes') $rows = $rows->filter(function ($row) { return (int) $row->parameter_count > (int) $row->certified_count; })->values();
        $response = new StreamedResponse(function () use ($rows, $type) {
            $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF");
            if ($type === 'covid') {
                fputcsv($out,array('ORDEN','FECHA','IDENTIFICACION','PACIENTE','SEGURO','EXAMEN','PARAMETRO','RESULTADO','CERTIFICADO'),';');
                foreach($rows as $row) fputcsv($out,array_map(array($this,'safeCsv'),array($row->id,$row->fecha_orden,$row->patient_id,$this->patient($row),$row->insurance_name,$row->exam_name,$row->parameter_name,$row->valor,$row->certificado)), ';');
            } else {
                fputcsv($out,array('ORDEN','FECHA','PACIENTE','SEGURO','TIPO','EXAMENES','PARAMETROS','CERTIFICADOS','BIOMETRIA','BIOQUIMICA','ESPECIALES'),';');
                foreach($rows as $row) fputcsv($out,array_map(array($this,'safeCsv'),array($row->id,$row->fecha_orden,$this->patient($row),$row->insurance_name,$row->pre_post,$row->exam_count,$row->parameter_count,$row->certified_count,$row->hematology_count,$row->biochemistry_count,$row->special_count)), ';');
            }
            fclose($out);
        });
        $response->headers->set('Content-Type','text/csv; charset=UTF-8'); $response->headers->set('Content-Disposition','attachment; filename="control-laboratorio-'.$type.'-'.date('Ymd-His').'.csv"'); return $response;
    }

    public function area(Request $request, $id, $machine) { if (!in_array((string)$machine,array('0','1','2'),true)) abort(404); $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->realizar($request,$id,$machine); }
    public function special($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->resultado_especial($id); }
    public function barcode($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->codigo_barras($id); }
    public function edit($id) { $order=$this->owned($id); return (int)$order->insurance_type===0 ? app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->edit1_c($id) : app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->cotizador_editar($id); }
    public function order($id) { $order=$this->owned($id); return (int)$order->insurance_type===0 ? app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->detalle($id,'CON') : app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->cotizador_orden_imprimir($id); }
    public function consent(Request $request, $id) { $this->owned($id); $request->merge(array('id_orden'=>$id)); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->modallabs($request); }
    public function safeCsv($value){$value=(string)$value;return preg_match('/^[=+\-@]/',ltrim($value))?"'".$value:$value;}
    private function patient($row){return trim($row->apellido1.' '.$row->apellido2.' '.$row->nombre1.' '.$row->nombre2);}
    private function owned($id){$row=\DB::table('examen_orden as eo')->leftJoin('seguros as s','s.id','=','eo.id_seguro')->where('eo.id',(int)$id)->where('eo.id_empresa',session()->get('id_empresa'))->select('eo.*','s.tipo as insurance_type')->first();if(!$row)abort(404);return $row;}
    private function companyId(Request $request){$id=trim((string)$request->session()->get('id_empresa'));if($id==='')abort(403,'Seleccione una empresa activa.');return $id;}
    private function page(array $payload){$view='v3.clinical.laboratory_control.index';$data=array('controlador'=>'control-laboratorio','carpeta'=>'clinical','submodulo'=>'index','submodulo_'=>'index','menu_id'=>672,'titulo'=>'Control de laboratorio','title'=>'Control de laboratorio | Aitrol Hospitalario','vista'=>$view,'obseraudi'=>'Control técnico de resultados de laboratorio V3','contenido'=>view($view,$payload));config(array('data'=>array_merge(config('data'),$data)));return view('v3.layouts.view_child');}
}
