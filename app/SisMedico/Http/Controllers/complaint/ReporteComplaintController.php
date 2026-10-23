<?php

namespace Sis_medico\Http\Controllers\complaint;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Sis_medico\Archivo_historico;
use Sis_medico\complaint;
use Sis_medico\user;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Sis_medico\Empresa;
use Sis_medico\Notificacion;

class ReporteComplaintController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {

        //dd($request->all());
        $rolUser = Auth::user()->id_tipo_usuario;
        $idUser = Auth::user()->id;
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $data = complaint::where('estado',1)->groupBy('id_usuariocrea')->get();
        //dd($data);

        if ($rolUser == 17) {
            $complaint = complaint::where('estado','1')->where('id_usuariocrea',$idUser)->orderBy('id', 'desc')->paginate(10);
        } else {
            if($rolUser == 1 || $rolUser == 7 || $rolUser == 20){
                
                $complaint = complaint::where('estado','1')->orderBy('id', 'desc')->paginate(10);
                //dd($complaint);
            }
        }

        return view('contable/complaint/index', ["rolUser" => $rolUser,"data" => $data,"request" => $request,"complaint" => $complaint,"fecha_desde" => $fechaDesde, "fecha_hasta" => $fechaHasta]);
    }

    public function fromulario_complaint()
    {
        return view('contable/complaint/formulario_complaint');
    }

    public function guardar(Request $request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;

            // Handle file upload
            $fileName = '';

            if ($request->hasFile('files')) {
                // $nombre_original = $request->file('files')->getClientOriginalName();
                // $extension = $request->file('files')->getClientOriginalExtension();
                // $nuevo_nombre = date('YmdHis'). "_complaint.".$extension;
                // $ruta = base_path() . '/storage/app/logo/' . $nuevo_nombre;
                // $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request->file('files')));
                // $r1; 
                $file = $request->file('files');
                $destino = 'storage/';
                $fileName = time() . " - " . $file->getClientOriginalName();
                $upload = $request->file('files')->move($destino, $fileName);

                $upload;
            }

            // Handle other form fields
            $complaint = [

                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'estado'               => 1,
                'nombre' => $request['nombre'],
                'medico' => $request['medico'],
                'especialista' => $request['especialista'],
                'procedimiento' => $request['procedimiento'],
                'descripcion' => $request['descripcion'],
                'observacion' => $request['observacion'],
                'codigo' => $request['codigo'],
                'numero_lote' => $request['numero_lote'],
                'cantidad' => $request['cantidad'],
                'condicion' => $request['condicion'],
                'fecha_vencimiento' => $request['fecha_vencimiento'],
                'dispositivo' => $request['dispositivo'],
                'resolucion' => $request['resolucion'],
                'files' => $fileName,
                'descripcion_suceso' => $request['descripcion_suceso'],
                'fecha_reporte'     => $request['fecha_reporte'],
                'fecha_danio' => $request['fecha_danio'],
                'momento_dano' => $request['momento_dano'],
            ];
            //dd($complaint);
            $id = complaint::insertGetId($complaint);
            DB::commit();
            return ['msj' => 'Guardado con éxito', "status" => "success"];
        } catch (\Exception $th) {
            DB::rollback();
            return ['status' => "error", "msj" => $th->getMessage()];
        }
    }

    public function notificacion(Request $request)
    {
        $data = Notificacion::notificacion();
        return $data;
    }

    public function show($id)
    {
        $data = complaint::find($id);
        $data->notificacion = 0;
        $data->save();
        return view('contable/complaint/show', compact('data', 'id'));
    }

    public function pdf_complaint($id)
    {
        $data = complaint::find($id);
        $empresa = Empresa::where('prioridad', 1)->first();
        // return view('contable.complaint.pdf_complaint', compact('data', 'empresa'));
        $view     = \View::make('contable.complaint.pdf_complaint', compact('data', 'empresa'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portait');

        return $pdf->stream('complaint-' . $id . '.pdf');
    }


    public function buscador(Request $request)
    {
        //dd($request->all());
       
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        // $data = complaint::where('estado',1)->where('id_usuariocrea',$request->id_vendedor)->get();
    
        $rolUser = Auth::user()->id_tipo_usuario;

        $data = complaint::query();
       
        if ($fechaDesde != null && $fechaHasta != null && $request->id_vendedor != '') {
            // Si vienen ambas fechas y un vendedor, busca entre las fechas con ese vendedor
            $data = $data->whereBetween('created_at', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59'])
                         ->where("id_usuariocrea", $request->id_vendedor);
        } elseif ($fechaDesde != null && $fechaHasta != null) {
            // Si vienen ambas fechas pero no un vendedor, busca entre las fechas
            $data = $data->whereBetween('created_at', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
        } elseif ($fechaDesde != null && $request->id_vendedor != '') {
            // Si solo viene fecha desde y un vendedor, busca desde esa fecha con ese vendedor
            $data = $data->where('created_at', '>=', $fechaDesde . ' 00:00:00')
                         ->where("id_usuariocrea", $request->id_vendedor);
        } elseif ($fechaHasta != null && $request->id_vendedor != '') {
            //dd('entra');
            // Si solo viene fecha hasta y un vendedor, busca hasta esa fecha con ese vendedor
            $data = $data->where('created_at', '<=', $fechaHasta . ' 23:59:59')
                         ->where("id_usuariocrea", $request->id_vendedor);
        } elseif ($fechaDesde != null) {
            // Si solo viene fecha desde, busca desde esa fecha
            $data = $data->where('created_at', '>=', $fechaDesde . ' 00:00:00');
        } elseif ($fechaHasta != null) {
            //dd('nuevo');
            // Si viene solo la fecha hasta, trae todos los resultados hasta esa fecha
            $data = $data->where('created_at', '<=', $fechaHasta . ' 23:59:59');
        } elseif ($request->id_vendedor != '') {
            // Si solo viene un vendedor, busca por ese vendedor
            $data = $data->where("id_usuariocrea", $request->id_vendedor);
        }
        

        $data = $data->where('estado',1)->orderBy('id', 'desc')->paginate(10);
        //dd($data);
        $complaint = complaint::where('estado','1')->groupBy('id_usuariocrea')->paginate(10);
        

        return view('contable/complaint/index', ["rolUser" => $rolUser, "data" => $complaint,"request" => $request,"complaint" => $data, "fecha_desde" => $fechaDesde, "fecha_hasta" => $fechaHasta]);
    }
}
