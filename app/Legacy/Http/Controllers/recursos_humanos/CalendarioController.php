<?php

namespace Sis_medico\Http\Controllers\recursos_humanos;

use Google\Service\CloudSearch\Id;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use DateTime;
use PHPExcel_Shared_Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Sis_medico\Vacaciones;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\UsuarioEmpresa;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Sucursales;
use Excel;
use Sis_medico\Log_Vacaciones;

class CalendarioController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  private function rol()
  {
    $rolUsuario = Auth::user()->id_tipo_usuario;
    if (in_array($rolUsuario, array(1, 8, 19, 20)) == false) {
      return true;
    }
  }

  public function crear()
  {
    if ($this->rol()) {
      return response()->view('errors.404');
    }
    // $id_usuario = Auth::user()->id;
    // $user = User::find($id_usuario);

    $id_empresa = Session::get('id_empresa');
    $empresa = Empresa::find($id_empresa);
    $usuarios = Ct_Nomina::where('id_empresa', $empresa->id)->where('estado', 1)->get();
    return view('calendario_vacaciones/mantenimiento_vacaciones/crear', ['usuarios' => $usuarios, 'empresa' => $empresa]);
  }

  public function guardar(Request $request)
  {

    //dd($request->all());
    $id_usuario = Auth::user()->id;

    $id_empresa = Session::get('id_empresa');
    $empresa = Empresa::find($id_empresa);
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $inicio = new DateTime($request['fecha_inicio']);
    $fin = new DateTime($request['fecha_fin']);
    $diferencia = $inicio->diff($fin);
    $dias = $diferencia->days;
    $usuario = Ct_Nomina::where('estado', 1)->first();



    Vacaciones::create([
      'id_usuario' => $request['id_empleado'],
      'id_contingente' => $request['id_usuario'],
      'observacion' => $request['observacion'],
      'anio' => $request['anio'],
      'id_empresa' => $empresa->id,
      'estado' => 1,
      'inicio' => $request['fecha_inicio'],
      'fin' => $request['fecha_fin'],
      'id_usuariocrea' => $id_usuario,
      'id_usuariomod' => $id_usuario,
      'ip_creacion' => $ip_cliente,
      'ip_modificacion' => $ip_cliente,
      'cantidad_dias' => $dias,
      'id_sucursales' => $usuario->id_sucursal,

    ]);
    // dd($request->all());
    return redirect(route('recursos_humanos.Calendario.index_mantenimiento_vacaciones'));
    //$insumos = ApProcedimiento::where('tipo','IV')->get();
    //$insu = ::where('tipo','IV')->count()+1;
    //return view('archivo_plano/mantenimientoinsumos/crear', ['insu'=> $insu]);
  }

  public function index_calendario_vacaciones()
  {
    $listado_vacaciones = Vacaciones::where('estado', 1)->get();
    //dd($listado_vacaciones);

    return view("calendario_vacaciones/index_calendario", ['listado_vacaciones' => $listado_vacaciones]);
  }
  public function index_mantenimiento_vacaciones(Request $request)
  {
    //dd($request->all());
    if ($this->rol()) {
      return response()->view('errors.404');
    }
    $id_empresa = $request->session()->get('id_empresa');
    $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();
    $anio       = $request['anio'];
    $usuario       = $request['usuario'];
    $estado       = $request['estado'];
    $estado_vacaciones       = $request['estado_vacaciones'];
    $fecha        = $request['fecha'];
    $fecha_hasta  = $request['fecha_hasta'];
    if (is_null($estado)) {
      $estado = 1;
    }
    if(is_null($fecha_hasta)){
      $fecha_hasta = date('Y-m-d');
    }
    $sucursal       = $request['sucursal'];

    /*$listado_vacaciones = DB::table('vacaciones as v')
      ->join('ct_nomina as ct', 'ct.id_user', '=', 'v.id_usuario')
      ->join('empresa as e', 'e.id', '=', 'v.id_empresa')
      ->join('users as u', 'u.id', '=', 'ct.id_user')
      ->leftjoin('ct_sucursales as s', 's.id','n.id_sucursal')
      ->where('v.estado', 1)
      ->where('ct.estado', 1)
      ->where('ct.id_empresa',$id_empresa)
      ->select('v.*', 'ct.id_user', 'ct.estado as estado_nomina', 'u.id as user_id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'e.nombrecomercial','s.nombre_sucursal')
      ->get();*/
    //dd($listado_vacaciones);
    //$listado_vacaciones = $this->buscar_vacaciones($request)->get();
    //dd($listado_vacaciones);
    $listado_vacaciones = $this->buscar_vacaciones($request)->paginate(15);
    return view("calendario_vacaciones/mantenimiento_vacaciones/index_vacaciones", ['resultados' => $listado_vacaciones, 'anio' => $anio, 'usuario' => $usuario, 'estado' => $estado, 'sucursal' => $sucursal, 'sucursales' => $sucursales, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta,'estado_vacaciones' => $estado_vacaciones]);
  }

  public function editar_vacaciones($id)
  {
    $vacacion = Vacaciones::find($id);
    $id_empresa = Session::get('id_empresa');
    $empresa = Empresa::find($id_empresa);
    $contingente = Ct_Nomina::where('id_empresa', $empresa->id)->where('estado', 1)->get();


    return view("calendario_vacaciones/mantenimiento_vacaciones/editar", ['vacacion' => $vacacion, 'id' => $id, 'contingente' => $contingente]);
  }

  public function actualizar(Request $request)
  {
    //dd($request->all());
    $ip_cliente = $_SERVER["REMOTE_ADDR"];

    $idusuario  = Auth::user()->id;
    $id = $request->id;
    $vacacion = Vacaciones::find($id);
    $inicio = new DateTime($request['fecha_inicio']);
    $fin = new DateTime($request['fecha_fin']);
    $diferencia = $inicio->diff($fin);
    $dias = $diferencia->days;

    // Log Vacaciones

    Log_Vacaciones::create([

      'id_usuario' => $vacacion->id_usuario,
      'id_vacacion' => $vacacion->id,
      'campo_anterior_conti' => $vacacion->id_contingente,
      'campo_actual_conti' => $request['contingente'],
      'campo_anterior_observacion' =>  $vacacion->observacion,
      'campo_actual_observacion' =>  $request['observacion'],
      'campo_anterior_anio' =>  $vacacion->anio,
      'campo_actual_anio' =>  $request['anio'],
      'campo_anterior_fecha_inicio' =>   $vacacion->inicio,
      'campo_acual_fecha_inicio' =>  $request['fecha_inicio'],
      'campo_anterior_fecha_fin' =>  $vacacion->fin,
      'campo_acual_fecha_fin' =>  $request['fecha_fin'],
      'id_usuariomod' => $idusuario,
      'ip_modificacion' => $ip_cliente,

    ]);

    $arr_vacacion = [
      'id_usuario'           => $vacacion->id_usuario,
      'id_empresa'           => $vacacion->id_empresa,
      'id_contingente'       => $request['contingente'],
      'observacion'          => $request['observacion'],
      'anio'                 => $request['anio'],
      'estado'               => 1,
      'inicio'               => $request['fecha_inicio'],
      'fin'                  => $request['fecha_fin'],
      'id_usuariocrea'       => $idusuario,
      'id_usuariomod'        => $idusuario,
      'ip_creacion'          => $ip_cliente,
      'ip_modificacion'      => $ip_cliente,
      'cantidad_dias' => $dias,

    ];
    $vacacion->update($arr_vacacion);
    //return redirect(route('laboratorio.Calendario.index_calendario_vacaciones'));
    return redirect(route('recursos_humanos.Calendario.index_mantenimiento_vacaciones'));
  }

  public function index_vacaciones()
  {
    $listado_vacaciones = Vacaciones::where('estado', 1)->get();
    // dd($listado_vacaciones);
    return view("calendario_vacaciones/mantenimiento_vacaciones/index_vacaciones", ['listado_vacaciones' => $listado_vacaciones]);
  }
  public function index_calendario_editable()
  {
    $listado_vacaciones = Vacaciones::where('estado', 1)->get();
    // dd($listado_vacaciones);

    return view("calendario_vacaciones/rrhh_vacaciones/index_calendario_editable", ['listado_vacaciones' => $listado_vacaciones]);
  }

  public function eliminar_vacaciones($id)
  {
    $ip_cliente = $_SERVER["REMOTE_ADDR"];

    $idusuario  = Auth::user()->id;
    try {

      $vacacion = Vacaciones::find($id);
      $arr_vacacion = [
        'estado'               => 0,
        //'id_usuariocrea'       => $idusuario,
        'id_usuariomod'        => $idusuario,
        //'ip_creacion'          => $ip_cliente,
        'ip_modificacion'      => $ip_cliente,
      ];
      $vacacion->update($arr_vacacion);
      return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
    } catch (\Exception $e) {
      return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
    }
  }

  public function descargar_vacaciones(Request $request)
  {
    //dd($request->all());
    $id_empresa = $request->session()->get('id_empresa');

    $rolUsuario = Auth::user()->id_tipo_usuario;
    if (in_array($rolUsuario, array(1, 20, 8, 19)) == true) {


      $listado_vacaciones = $this->buscar_vacaciones($request)->get();

      $fecha_d = date('d-m-Y');
      Excel::create('Reporte Vacaciones' . $fecha_d, function ($excel) use ($listado_vacaciones, $id_empresa) {

        $excel->sheet('Reporte Vacaciones', function ($sheet) use ($listado_vacaciones, $id_empresa) {
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
          $sheet->mergeCells('A1:J2');
          $sheet->cell('A1', function ($cell) {
            $cell->setValue('Reporte Vacaciones');
            $cell->setFontColor('#010101');
            $cell->setBackground('#D1E9F2');
            $cell->setFontWeight('bold');
            $cell->setFontSize('20');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
          });
          $i = 3;

          $arrTitulos = ["Id", "Año",  "Empresa", "Sucursal" ,"Cedula Empleado", "Nombre Empleado", "Estado empleado", "Fecha Ingreso", "Fecha Inicio", "Fecha Fin"];
          $comienzo = 3;
          for ($i = 0; $i < count($arrTitulos); $i++) {
            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
              // manipulate the cel
              $cell->setValue($arrTitulos[$i]);
              $cell->setBackground('#D1E9F2');
              $cell->setFontWeight('bold');
              $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
          }
          $comienzo++;
          //dd($biopsias->get());
          $fecha_hoy = date('d-m-Y');
          foreach ($listado_vacaciones as $value) {
            $nomina = Ct_Nomina::where("id_user", $value->id_usuario)->where('estado', 1)->where('id_empresa', $id_empresa)->first();
            //dd($nomina);
            $fecha_ingreso = '';
            if (!is_null($nomina)) {
              if (!is_null($nomina->fecha_ant)) {
                $fecha_ingreso = ($nomina->fecha_ant);
              } else {
                $fecha_ingreso = ($nomina->fecha_ingreso);
              }
            }



            $arrValue = [$value->id, $value->anio, $value->nombrecomercial,$value->nombre_sucursal, $value->id_usuario, "{$value->apellido1} {$value->apellido2} {$value->nombre1} {$value->nombre2} ", $value->estado_nomina == 1 ? 'Activo' : 'Inactivo', "{$fecha_ingreso}", date('d-m-Y', strtotime($value->inicio)), date('d-m-Y', strtotime($value->fin))];
            for ($i = 0; $i < count($arrValue); $i++) {
              $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                // manipulate the cel
                $cell->setValue($arrValue[$i]);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('left');
              });
            }
            $comienzo++;
          }
        });
      })->export('xlsx');
    }
  }



  public function buscar_usuario(Request $request)
  {
    //dd($request->all());
    $id_empresa = $request->session()->get('id_empresa');
    $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();
    $resultados = DB::table('vacaciones as v')
      ->join('ct_nomina as ct', 'ct.id_user', '=', 'v.id_usuario')
      ->join('empresa as e', 'e.id', '=', 'ct.id_empresa')
      ->join('users as u', 'u.id', '=', 'ct.id_user')
      ->where('v.estado', '=', 1)
      ->where('e.id', $id_empresa)
      ->where(function ($query) use ($request) {
        $query->where('ct.nombres', '=', '%' . $request->paciente . '%')
          ->orWhere('u.nombre1', 'like', '%' . $request->paciente . '%')
          ->orWhere('u.nombre2', 'like', '%' . $request->paciente . '%')
          ->orWhere('u.apellido1', 'like', '%' . $request->paciente . '%')
          ->orWhere('u.apellido2', 'like', '%' . $request->paciente . '%');
      })->select(
        'v.*',
        'ct.id_user',
        'ct.estado as estado_nomina',
        'u.id as user_id',
        'u.nombre1',
        'u.nombre2',
        'u.apellido1',
        'u.apellido2',
        'e.nombrecomercial'
      );
    if (count($request->all()) > 0) {
      if (!is_null($request->estado)) {
        $resultados = $resultados->where("ct.estado", $request->estado);
      }
      if (!is_null($request->anio)) {
        $resultados = $resultados->where("v.anio", $request->anio);
      }
    }
    $resultados = $resultados->get();


    return view('calendario_vacaciones/mantenimiento_vacaciones/index_vacaciones', ['resultados' => $resultados, 'request' => $request, 'sucursales' => $sucursales]);
  }

  public function excel_masivo_vacaciones($nombre,Request $request)
  {
    $id_empresa = $request->session()->get('id_empresa');
    try {

      DB::beginTransaction();

      Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(800, function ($reader) use ($id_empresa) {

        foreach ($reader as$book) {
          

          //$id_usuario = str_pad($book->id_usuario, 10, "0", STR_PAD_LEFT);

          
          if($book->inicio == null && $book->fin == null){
            $fecha_inicio = null;
            $fecha_fin = null;
            $diferencia = 0;

          }else{
            $fecha_inicio = date('Y-m-d', strtotime('+1 day', PHPExcel_Shared_Date::ExcelToPHP($book->inicio)));
            $fecha_fin = date('Y-m-d', strtotime('+1 day', PHPExcel_Shared_Date::ExcelToPHP($book->fin)));
            $fechaInicioObj = new DateTime($fecha_inicio);
            $fechaFinObj = new DateTime($fecha_fin);
            $diferencia = $fechaInicioObj->diff($fechaFinObj)->days;
          }
          

          //$vacaciones = Vacaciones::where('id_usuario', $id_usuario)->where('estado', 1)->where('anio')->first();
          $nomina = Ct_Nomina::where('id_user', $book->cedula)->where('id_empresa',$id_empresa)->where('estado','1')->first();
          //dd($vacaciones->usuario->id_sucursal);
          //dd($book->cedula,$nomina);
          $ip_cliente = $_SERVER["REMOTE_ADDR"];
          if (!is_null($nomina)) {
            $input = [
              'inicio' => $fecha_inicio,
              'fin' => $fecha_fin,
              'id_usuario' => $book->cedula,
              'id_empresa' => $id_empresa,
              'cantidad_dias' => $diferencia,
              'id_sucursales' => $nomina->id_sucursal,
              'anio' => $book->anio,
              'observacion' => $book->observacion,
              'id_usuariocrea' => $book->id_usuariocrea,
              'id_usuariomod' => $book->id_usuariocrea,
              'ip_creacion' => 'Masivo_Cre_Vac'.date('Y-m-d'),
              'ip_modificacion' => $ip_cliente,

            ];
            Vacaciones::create($input);
          }
        }
      });
      DB::commit();
      return 'Registros procesados con exito';
    } catch (\Exception $e) {
      DB::rollBack();
      return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
    }
  }
  public static function buscar_vacaciones(Request $request)
  {
    //dd($request->all());
    $id_empresa = $request->session()->get('id_empresa');
    $fecha_hasta = $request->fecha_hasta;
    $estado_vacaciones = $request->estado_vacaciones;
    $estado = $request->estado;
    if($request->fecha_hasta == null){
      $fecha_hasta = date('Y-m-d');
    }
    if($estado_vacaciones == null){
      $estado_vacaciones = '1';
    }
    if($estado == null){
      $estado = '1';
    }
    //dd($estado,$estado_vacaciones);
    $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();
    $resultados = DB::table('vacaciones as v')
      ->join('ct_nomina as ct', 'ct.id_user', 'v.id_usuario')
      ->join('empresa as e', 'e.id', 'ct.id_empresa')
      ->join('users as u', 'u.id', 'ct.id_user')
      ->leftjoin('ct_sucursales as s', 's.id', 'ct.id_sucursal')
      //->where('v.estado', '1')
      ->where('e.id', $id_empresa)
      ->where('v.id_empresa', $id_empresa)
      //->where('ct.estado','1')
      /*->where(function ($query) use ($request) {
        $query->where('ct.nombres', '=', '%' . $request->usuario . '%')
          ->orWhere('u.nombre1', 'like', '%' . $request->usuario . '%')
          ->orWhere('u.nombre2', 'like', '%' . $request->usuario . '%')
          ->orWhere('u.apellido1', 'like', '%' . $request->usuario . '%')
          ->orWhere('u.apellido2', 'like', '%' . $request->usuario . '%');
      })*/
      ->orderBy('v.id', 'DESC')
      ->select(
        'v.id',
        'v.estado',
        'v.anio',
        'v.id_sucursales',
        'v.id_usuario',
        'v.inicio',
        'v.fin',
        'v.observacion',
        'ct.id_user',
        'ct.estado as estado_nomina',
        'u.id as user_id',
        'u.nombre1',
        'u.nombre2',
        'u.apellido1',
        'u.apellido2',
        'e.nombrecomercial',
        's.id as id_sucursal',
        's.nombre_sucursal'

      );
    if (count($request->all()) > 0) {
      if (!is_null($estado)) {
        $resultados = $resultados->where("ct.estado", $request->estado);
      }
      if (!is_null($estado_vacaciones)) {
        $resultados = $resultados->where("v.estado", $estado_vacaciones);
      }
      if (!is_null($request->anio)) {
        $resultados = $resultados->where("v.anio", $request->anio);
      }
      if (!is_null($request->sucursal)) {
        $resultados = $resultados->where("s.id", $request->sucursal);
      }
      if (!is_null($request->usuario)) {
        $eq = $request->usuario;
        $resultados = $resultados->where(function ($jq1) use ($eq) {
          $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
            ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
            ->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1) LIKE ?', ['%' . $eq . '%'])
            ->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1," ",u.nombre2) LIKE ?', ['%' . $eq . '%']);
        });
      }
      if (!is_null($request->fecha)) {
        $resultados = $resultados->whereBetween('inicio', [$request->fecha . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
        //$vacaciones = $vacaciones->where('inicio', '>=', $fecha_desde . ' 00:00:00');
      } else {
        $resultados = $resultados->where('fin', '<=', $fecha_hasta);
      }
    } else {
      $resultados = $resultados->where("ct.estado", $estado);
      $resultados = $resultados->where("v.estado", $estado_vacaciones);
    }
    //dd($resultados);
    return $resultados;


    //return view('calendario_vacaciones/mantenimiento_vacaciones/index_vacaciones', ['resultados' => $resultados, 'request' => $request,'sucursales'=>$sucursales]);
  }
  public function descargar_plantilla_vacaciones(Request $request)
  {
    //dd($request->all());
    $id_empresa = $request->session()->get('id_empresa');

    $rolUsuario = Auth::user()->id_tipo_usuario;
    if (in_array($rolUsuario, array(1, 20, 8, 19)) == true) {

      $fecha_d = date('d-m-Y');
      $nomina = Ct_Nomina::where('estado', '1')->where('id_empresa', $id_empresa)->get();
      Excel::create('Plantilla_Vacaciones_' . $fecha_d, function ($excel) use ($id_empresa, $nomina) {

        $excel->sheet('Plantilla Vacaciones', function ($sheet) use ($id_empresa, $nomina) {
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
          $i = 1;

          $arrTitulos = ["cedula", "nombres",  "empresa","sucursal" ,"anio", "inicio", "fin","observacion"];
          $comienzo = 1;
          for ($i = 0; $i < count($arrTitulos); $i++) {
            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
              // manipulate the cel
              $cell->setValue($arrTitulos[$i]);
              $cell->setBackground('#D1E9F2');
              $cell->setFontWeight('bold');
              $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
          }
          $comienzo++;
          //dd($biopsias->get());
          $fecha_hoy = date('d-m-Y');
          $anio = date('Y');
          foreach ($nomina as $value) {
            //dd($nomina);
            $fecha_ingreso = '';
            if (!is_null($value)) {
              if (!is_null($value->fecha_ant)) {
                $fecha_ingreso = ($value->fecha_ant);
              } else {
                $fecha_ingreso = ($value->fecha_ingreso);
              }
            }
            $sucursal = "";
            if(!is_null($value->sucursales)) {
               $sucursal = $value->sucursales->nombre_sucursal;
            }
            $arrValue = [$value->id_user, " {$value->user->apellido1} {$value->user->apellido2} {$value->user->nombre1} {$value->user->nombre2}", $value->empresa->nombrecomercial,$sucursal, $anio, "", "",""];
            for ($i = 0; $i < count($arrValue); $i++) {
              $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                // manipulate the cel
                $cell->setValue($arrValue[$i]);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('left');
              });
            }
            $comienzo++;
          }
        });
      })->export('xlsx');
    }
  }
}