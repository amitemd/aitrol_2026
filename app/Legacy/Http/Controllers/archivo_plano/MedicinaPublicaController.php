<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\ApProcedimiento;


class MedicinaPublicaController extends Controller
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


  public function index()
  {

    //dd($codigofecha.$random);
    $medicinas = ApProcedimiento::where('tipo', 'M')->where('estado', '1')->paginate(15);
    $totalmedicinas = ApProcedimiento::where('tipo', 'M')->where('estado', '1')->get();
    //dd($medicinas);

    return view('archivo_plano/medicinapublica/index', ['medicinas' => $medicinas, 'totalmedicinas' => $totalmedicinas]);
    /*$listado = ApProcedimiento::where('estado', '1')->get();
    return view('archivo_plano/mantenimientomedicamentos/index', ['listado' => $listado]);*/
  }

  public function buscar(Request $request)
  {
      $constraints = [
          'codigo'       => $request['codigo'],
          'descripcion'  => $request['descripcion'],
      ];
      $fields = array_keys($constraints);
      $medicinas = array();
      $totalmedicinas = 0;
      for ($i = 0; $i < count($constraints); $i++) {
          if (!is_null($constraints[$fields[$i]])) {
              $medicinas = ApProcedimiento::where($fields[$i], "LIKE", "%" . $constraints[$fields[$i]] . "%")
              ->where('tipo', 'M')->where('estado', '1')->paginate(10);
              $totalmedicinas = ApProcedimiento::where($fields[$i], "LIKE", "%" . $constraints[$fields[$i]] . "%")
              ->where('tipo', 'M')->where('estado', '1')->get();
              break;
          }
      }
      //$totalmedicinas = ApProcedimiento::where('tipo', 'M')->where('estado', '1')->get();
      if (count($medicinas) <= 0) {
        $medicinas = ApProcedimiento::where('tipo', 'M')->where('estado', '1')->paginate(10);
        $totalmedicinas = ApProcedimiento::where('tipo', 'M')->where('estado', '1')->get();
      }
      return view('archivo_plano/medicinapublica/index', ['medicinas' => $medicinas, 'totalmedicinas' => $totalmedicinas]);
  }

  public function crear()
  {
    $medicinas = ApProcedimiento::where('tipo', 'M');
    return view('archivo_plano/medicinapublica/crear', ['medicinas' => $medicinas]);
  }

  public function store(Request $request)
  {
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    //$codigofecha = date("ymdHis");
    //$random = rand(100,999);
    $codigo = date("ymdHis") . rand(100, 999);
    //$codigo = DB::table('ap_procedimiento')->where('tipo','M',)->max('codigo');
    //dd($codigo);

    $arr_medicamento = [
      'descripcion'          => $request['descripcion'],
      'tipo'                 => 'M',
      'codigo_medicina'      => $request['codigo_medicina'],
      'valor'                => $request['valor'],
      'estado'               => 1,
      'tipo_i'               => 'F',
      'codigo'               => $codigo,
      'ip_creacion'          => $ip_cliente,
      'ip_modificacion'      => $ip_cliente,
      'id_usuariocrea'       => $idusuario,
      'id_usuariomod'        => $idusuario,
    ];
    //dd($arr_medicamento);

    ApProcedimiento::create($arr_medicamento);

    return redirect(route('procedimientos.index'));
  }

  public function editar($id)
  {
    $medicina = ApProcedimiento::find($id);
    //dd($medicina);
    return view('archivo_plano/medicinapublica/editar', ['medicina' => $medicina, 'id' => $medicina->id]);
  }

  public function update(Request $request)
  {

    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;

    $id = $request->id;
    //dd($request);
    $medicinas = ApProcedimiento::find($id);

    $arr_medicamento = [
      'descripcion'  => $request['descripcion'],
      'valor'        => $request['valor'],
      'codigo_medicina'      => $request['codigo_medicina'],
      'id_usuariocrea'      => $idusuario,
      'id_usuariomod'       => $idusuario,
      'ip_creacion'         => $ip_cliente,
      'ip_modificacion'     => $ip_cliente,
    ];
    //dd($arr_medicamento,$medicinas);
    $medicinas->update($arr_medicamento);
    return redirect(route('procedimientos.index'));
  }

  public function delete($id)
  {

    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;

    //$id = $request->id;
    //dd($request);
    $medicinas = ApProcedimiento::find($id);

    $arr_medicamento = [
      //'descripcion'  => $request['descripcion'],
      'estado'              => 0,
      'id_usuariocrea'      => $idusuario,
      'id_usuariomod'       => $idusuario,
      'ip_creacion'         => $ip_cliente,
      'ip_modificacion'     => $ip_cliente,
    ];
    //dd($arr_medicamento,$medicinas);
    $medicinas->update($arr_medicamento);
    if (isset($medicinas->estado) && $medicinas->estado == 0) {
      return json_encode(['respuesta' => 'ok']);
    } else {
      return json_encode(['respuesta' => 'no']);
    }
    //return redirect(route('procedimientos.index'));
  }
}
