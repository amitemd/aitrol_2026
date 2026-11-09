<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Auth;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nivel;
use Sis_medico\Services\V3LaboratoryExamCostService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratoryExamCostController extends Controller
{
    private $costs;

    public function __construct(V3LaboratoryExamCostService $costs)
    {
        $this->middleware('auth');
        $this->costs = $costs;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('buscar', ''));
        list($exams, $prices) = $this->costs->listing($this->companyId(), $search);
        return $this->page('index', array('exams' => $exams, 'prices' => $prices,
            'levels' => Nivel::orderBy('id')->get(), 'search' => $search), 'Costos de exámenes');
    }

    public function edit($exam)
    {
        $model = $this->costs->find($this->companyId(), $exam);
        if (!$model) abort(404);
        $levels = Nivel::orderBy('id')->get();
        $prices = array();
        foreach (\DB::table('examen_nivel')->where('id_examen', $exam)->get() as $price) $prices[$price->nivel] = $price->valor1;
        return $this->page('edit', compact('model', 'levels', 'prices'), 'Editar costo del examen');
    }

    public function update(Request $request, $exam)
    {
        if (!$this->costs->find($this->companyId(), $exam)) abort(404);
        $this->validate($request, array('valor_reactivo' => 'required|numeric|min:0|max:999999999.99',
            'valor_implementos' => 'required|numeric|min:0|max:999999999.99'));
        $this->costs->save($this->companyId(), $exam, round((float)$request->valor_reactivo, 2),
            round((float)$request->valor_implementos, 2), Auth::id(), $request->ip());
        return redirect()->route('v3.laboratory-exam-costs.edit', array('exam' => $exam))
            ->with('success', 'Los costos se actualizaron únicamente para la empresa activa.');
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->get('buscar', ''));
        list($exams, $prices) = $this->costs->all($this->companyId(), $search);
        $levels = Nivel::orderBy('id')->get();
        $response = new StreamedResponse(function () use ($exams, $prices, $levels) {
            $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
            $header = array('EXAMEN', 'AGRUPADOR', 'PARTICULAR', 'REACTIVOS', 'IMPLEMENTOS', 'COSTO TOTAL');
            foreach ($levels as $level) { $header[] = $level->nombre; $header[] = 'MARGEN '.$level->nombre; $header[] = '% '.$level->nombre; }
            $header[] = 'MARGEN PARTICULAR'; $header[] = '% PARTICULAR'; fputcsv($out, $header, ';');
            foreach ($exams as $exam) {
                $cost = (float)$exam->valor_reactivo + (float)$exam->valor_implementos;
                $row = array($this->safeCsv($exam->descripcion), $this->safeCsv($exam->agrupador_nombre), $exam->valor,
                    $exam->valor_reactivo, $exam->valor_implementos, $cost);
                foreach ($levels as $level) { $sale = isset($prices[$exam->id][$level->id]) ? $prices[$exam->id][$level->id] : 0; $margin=$sale-$cost;
                    $row[]=$sale; $row[]=$margin; $row[]=$cost>0?round($margin/$cost*100,2):0; }
                $margin=(float)$exam->valor-$cost; $row[]=$margin; $row[]=$cost>0?round($margin/$cost*100,2):0; fputcsv($out,$row,';');
            }
            fclose($out);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="costos-examenes-'.date('Ymd-His').'.csv"');
        return $response;
    }

    private function safeCsv($value) { $value=(string)$value; return preg_match('/^[=+\-@]/',ltrim($value)) ? "'".$value : $value; }
    private function companyId() { $id=(string)session('id_empresa'); if($id==='') abort(422,'Seleccione una empresa activa.'); return $id; }
    private function page($name, array $payload, $title)
    {
        $view='v3.clinical.laboratory_exam_costs.'.$name;
        $data=array('controlador'=>'costos-examenes','carpeta'=>'clinical','submodulo'=>'index','submodulo_'=>'index','menu_id'=>675,
            'titulo'=>$title,'title'=>$title.' | Aitrol Hospitalario','vista'=>$view,
            'obseraudi'=>'Costos de laboratorio aislados por empresa y autorizados por rol','contenido'=>view($view,$payload));
        config(array('data'=>array_merge(config('data'),$data))); return view('v3.layouts.view_child');
    }
}
