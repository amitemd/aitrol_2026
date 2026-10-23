<?php

namespace Sis_medico\Http\Controllers\trabajo_campo;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Creacion_Campo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Excel;

class TrabajoCampoController extends Controller
{

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24, 4, 8)) == false) {
            return true;
        }
    }

    public function index()
    {

        $idusuario  = Auth::user()->id;
        $datos      = [];
        $date = date('Y-m-d');
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($rolUsuario == 1 || $rolUsuario == 8 || $rolUsuario == 20) {
            $datos = Creacion_Campo::whereBetween('fecha_desde', [$date . ' 00:00:00', $date . ' 23:59:59'])->orderBy('created_at', 'ASC')->paginate(20);
        } else {
            $datos = Creacion_Campo::where('user', $idusuario)->whereBetween('created_at', [$date . ' 00:00:00', $date . ' 23:59:59'])->orderBy('created_at', 'DESC')->paginate(20);
        }
        $fechaDesde = date('Y-m-d');
        $fechaHasta = date('Y-m-d');
        return view('trabajo_campo.index', ['datos' => $datos,'fechaDesde'=>$fechaDesde,'fechaHasta'=>$fechaHasta]);
    }

    public function create()
    {
        return view('trabajo_campo.create');
    }

    public function save(Request $request)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        try {
            Creacion_Campo::create([
                'user'            => $idusuario,
                'fecha_desde'     => $request['fecha_desde'],
                'fecha_hasta'     => $request['fecha_hasta'],
                'lugar'           => $request['lugar'],
                'observaciones'   => $request['obs'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
        } catch (\Throwable $th) {
            return json_encode($th);
        }

        return json_encode('ok');
    }

    public function editar($id)
    {

        $datos = Creacion_Campo::where('id', $id)->first();
        return view('trabajo_campo.edit', ['datos' => $datos]);
    }

    public function edit_form(Request $request)
    {

        $update = Creacion_Campo::find($request['id']);
        try {
            $update->fecha_hasta   = $request['fecha_hasta'];
            $update->observaciones = $request['obs'];
            $update->save();
        } catch (\Throwable $th) {
            return json_encode($th);
        }

        return json_encode('ok');
    }

    public function buscador(Request $request)
    {
        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];
        if ($request['excel'] == 0) {
        $control     = Creacion_Campo::query();
        if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {
                $control = $control->whereBetween('fecha_desde', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($request['usuarios'])) {
            $control = $control->where('user', $request['usuarios']);
        }
        $control = $control->paginate(10);
        return view('trabajo_campo.index', ['datos' => $control,'fechaDesde'=>$fecha_desde,'fechaHasta'=>$fecha_hasta]);
        } else {
            $titulos = array("FECHA INICIO", "FECHA FIN", "CEDULA", "NOMBRES", "LUGAR", "OBSERVACIÓN");
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            $control     = Creacion_Campo::query();
            if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {
                $control = $control->whereBetween('fecha_desde', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59']);
            }
            if (!is_null($request['usuarios'])) {
                $control = $control->where('user', $request['usuarios']);
            }
            $control = $control->get();
            Excel::create('TRABAJO DE CAMPO', function ($excel) use ($titulos, $posicion, $control) {
                $excel->sheet('Solicitud de Permiso', function ($sheet) use ($titulos, $posicion, $control) {
                    $sheet->mergeCells('A1:F1');
                    $sheet->cell('A1', function ($cell) {
                        $cell->setValue('TRABAJO DE CAMPO');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo = 2;
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#DAF7A6');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    foreach ($control as $val) {
                        $datos_excel = array();
                        array_push($datos_excel, $val->fecha_desde, $val->fecha_hasta, $val->user, $val->usuario->nombre1 . ' ' . $val->usuario->apellido1, $val->lugar, $val->observaciones);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                    $sheet->setWidth(array(
                        'A' => 17,
                        'B' => 17,
                        'C' => 17,
                        'D' => 17,
                        'E' => 17,
                        'F' => 45,
                        'G' => 17,
                        'H' => 17,
                        'I' => 17,
                        'J' => 17,
                        'K' => 17,
                    ));
                });
                $excel->getActiveSheet()->getStyle('F1:F' . $excel->getActiveSheet()->getHighestRow())
                    ->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('D1:D' . $excel->getActiveSheet()->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            })->export('xlsx');
        }
    }

    public function usuarios(Request $request)
    {
        $campo      = strtoupper($request['term']);
        $valid_tags = [];
        $usuarios   = User::where('estado', '1')
            ->where('id_tipo_usuario', '<>', 2);
        $usuarios = $usuarios->where(function ($jq1) use ($campo) {
            $jq1->whereRaw('CONCAT(nombre1," ",apellido1) LIKE ?', ['%' . $campo . '%']);
        });
        $usuarios = $usuarios->get();
        foreach ($usuarios as $id => $users) {
            $edad         = Carbon::parse($users->fecha_nacimiento)->age;
            $valid_tags[] = ['id' => $users->id, 'nombreappe' => $users->nombre1 . ' ' . $users->apellido1, 'edad' => $edad];
        }

        return response()->json($valid_tags);
    }
}
