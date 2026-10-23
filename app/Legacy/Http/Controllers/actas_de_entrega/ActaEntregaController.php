<?php

namespace Sis_medico\Http\Controllers\actas_de_entrega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Acta_De_Entrega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Tipo_Acta_De_Entrega;
use Carbon\Carbon;

class ActaEntregaController extends Controller 
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        
        $tipo_actas = DB::table('tipo_acta_de_entrega')->get();
        $actas = Acta_De_Entrega::where('estado', 1)->select('acta_de_entrega.*')->paginate(30);
        $fecha = DB::table('acta_de_entrega')->min('fecha');
        $fecha_actual = Carbon::now()->format('Y-m-d');
        //dd($fecha);

        return view('actas_de_entrega/index', [ 'actas' => $actas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_actual, 'acta_tipo' => '0', 'nombres_search' => ''] );
    }

    public function crearActa(Request $request){
      
      $tipo_actas = DB::table('tipo_acta_de_entrega')->get();
      //$paciente = null;

      return view('actas_de_entrega/create', ['tipo_actas' => $tipo_actas]);

    }

    public function userNombre(Request $request)
    {

        $nombre_pac   = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND estado = 1 LIMIT 50";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    
    public function userNombre2(Request $request)
    {
        if ($request['usuario_recibe'] == null) {
        $nombre_pac   = $request['usuario_entrega'];
        } else {
        $nombre_pac   = $request['usuario_recibe'];
        }
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = '%'.$seteo . $value. '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND estado = 1";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data) > 0) {
            return $data[0];
            
        } else {
            return '0';
        }

    }
    
    public function editarActa($id){
      
      $edit_acta = DB::table('acta_de_entrega')->find($id);
      $tipo_actas = DB::table('tipo_acta_de_entrega')->get();
      $nombres_ent = DB::table('users')->join('acta_de_entrega', 'users.id', '=', 'acta_de_entrega.id_usuario_entrega')->where('acta_de_entrega.id', $id)->select(DB::raw('CONCAT_WS(" ", users.nombre1, users.nombre2, users.apellido1, users.apellido2) as nombres_completos_ent'))->first();
      $nombres_rec = DB::table('users')->join('acta_de_entrega', 'users.id', '=', 'acta_de_entrega.id_usuario_recibe')->where('acta_de_entrega.id', $id)->select(DB::raw('CONCAT_WS(" ", users.nombre1, users.nombre2, users.apellido1, users.apellido2) as nombres_completos_rec'))->first();
      //dd($nombres_rec);

      return view ('actas_de_entrega/editar', ['edit_acta' => $edit_acta, 'id' => $id, 'tipo_actas' => $tipo_actas, 'nombres_ent' => $nombres_ent, 'nombres_rec' => $nombres_rec]);
    }


    public function storeActa(Request $request){

      //dd($request);
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario  = Auth::user()->id;
      $id_empresa  = $request->session()->get('id_empresa');
      $fecha = Carbon::now()->format('Y-m-d');
      //dd($id_empresa);


      $arr_actas = [
        'id_empresa' => $id_empresa,
        'fecha' => $fecha,
        'id_tipo' => $request['select_tipo'],
        'descripcion' => strtoupper($request['descripcion']),
        'imei' => $request['imei'],
        'numero_cel' => $request['numero_cel'],
        'codigo_tarjeta' => $request['codigo'],
        'modelo_computador' => strtoupper($request['modelo']),
        'info_adicional' => strtoupper($request['info_adicional']),
        'id_usuario_entrega' => $request['id_usuario_entrega'],
        'id_usuario_recibe' => $request['id_usuario_recibe'],
        'estado' => 1,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente
      ];
      //dd($arr_actas);

      Acta_De_Entrega::create($arr_actas);
      return redirect(route('acta.entrega_index'));

    }

    public function updateActa(Request $request){

      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario  = Auth::user()->id;
      $id_empresa  = $request->session()->get('id_empresa');
      $id = $request->id;
      $upd_acta = Acta_De_Entrega::find($id);
      //dd($request);

      $arr_actas = [
        'id_empresa' => $id_empresa,
        'id_tipo' => $request['select_tipo'],
        'descripcion' => strtoupper($request['descripcion']),
        'imei' => $request['imei'],
        'numero_cel' => $request['numero_cel'],
        'codigo_tarjeta' => $request['codigo'],
        'modelo_computador' => strtoupper($request['modelo']),
        'info_adicional' => strtoupper($request['info_adicional']),
        'id_usuario_entrega' => $request['id_usuario_entrega'],
        'id_usuario_recibe' => $request['id_usuario_recibe'],
        'estado' => 1,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente
      ];
      //dd($arr_actas);

      $upd_acta->update($arr_actas);
      return redirect(route('acta.entrega_index'));

    }

    public function visualizar_pdf(Request $request){

      $nombre_acta_firmada = Acta_De_Entrega::where('id', $request['id'])->value('archivo_acta');
      $nombre_archivo = $nombre_acta_firmada;

      if($nombre_acta_firmada == null) {

        $id_empresa  = $request->session()->get('id_empresa');
        $nombre_empresa = DB::table('empresa')->join('acta_de_entrega as act', 'empresa.id', '=', 'act.id_empresa')->value('empresa.nombrecomercial');
        $datos = DB::table('acta_de_entrega')->where('id', $request['id'])->select('acta_de_entrega.*')->first();
        $usuario_ent = DB::table('users')->leftjoin('ct_nomina as ctd', 'users.id', '=', 'ctd.id_user')->where('users.id', $datos->id_usuario_entrega)->select('users.nombre1', 'users.nombre2', 'users.apellido1', 'users.apellido2', 'ctd.cargo')->first();
        $usuario_rec = DB::table('users')->leftjoin('ct_nomina as ctd', 'users.id', '=', 'ctd.id_user')->where('users.id', $datos->id_usuario_recibe)->select('nombre1', 'nombre2', 'apellido1', 'apellido2', 'ctd.cargo')->first();
        $logo_empresa = DB::table('empresa')->where('id', $id_empresa)->value('logo');
        $logo_receta = DB::table('empresa')->where('id', $id_empresa)->value('logo_receta');
        //dd($nombre_empresa);

        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        $vistaurl = "actas_de_entrega.actas_pdf";
        $view     = \View::make($vistaurl, compact('datos', 'usuario_ent', 'usuario_rec', 'meses', 'logo_empresa', 'logo_receta', 'nombre_empresa', 'id_empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landform');
        return $pdf->stream('Acta_Entrega.pdf');
       
      } else {
        $file = Storage::disk('public')->get($nombre_archivo);
        return response($file)->header('Content-Type', 'application/pdf');
        //$file = storage_path() . "/app/avatars/" . $nombre_archivo;
        //return response($file)->header('Content-Type', 'application/pdf');
      }
    }

    public function subirActa(Request $request){
      $id = $request->id;
      return view ('actas_de_entrega/subir', ['id' => $id]);
    }

    public function guardarActa(Request $request){

      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario = Auth::user()->id;
      $id = $request->id;
      date_default_timezone_set('America/Guayaquil');
      $nombre_original = $request['archivo_actas']->getClientOriginalName();
      $extension = $request['archivo_actas']->getClientOriginalExtension();
      $tiempo_unix = time();
      $nuevo_nombre = "actas_{$tiempo_unix}.{$extension}";

      // Guarda 'archivo_actas' con un nuevo nombre en el disco public
      $store_acta = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo_actas']));
      // Almacena la ruta del archivo guardado
      $ruta_acta = base_path() . '/storage/app/avatars' . $nuevo_nombre;
      
      DB::table('acta_de_entrega')->where('id', $id)->update([
        'archivo_acta' => $nuevo_nombre,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente
      ]);

      return redirect(route('acta.entrega_index'));
    }

    public function searchActa(Request $request) {
      //dd($request);
      $fecha_desde = $request['fecha'];
      $fecha_hasta = $request['fecha_hasta'];  
      $acta_tipo = $request['acta_tipo'];
      $nombres_search = $request['nombres_search'];

      $actas = DB::table('acta_de_entrega as act')->join('users as us', 'us.id', '=', 'act.id_usuario_recibe')->where('act.estado', 1)->whereBetween('act.fecha', [$fecha_desde, $fecha_hasta]);

      if ($acta_tipo != 0){
        $actas->where('id_tipo', $acta_tipo)->get();
      }

      if ($nombres_search != null) {
        // query para busqueda por nombres
        $actas = $actas->where(function ($q1) use ($nombres_search){
          $q1->orwhereraw('CONCAT_WS(" ", us.nombre1, us.nombre2, us.apellido1, us.apellido2 ) LIKE ?', ['%' . $nombres_search . '%'])
          ->orwhereraw('CONCAT_WS(" ", us.nombre1, us.apellido1, us.apellido2 ) LIKE ?', ['%' . $nombres_search . '%']);
        });
      }
      
      //$actas = $actas->select('act.*')->get();
      $actas = $actas->select('act.*')->paginate(30);
      //dd($actas);

      return view('actas_de_entrega/index', ['actas' => $actas, 'fecha' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'acta_tipo' => $acta_tipo, 'nombres_search' => $nombres_search] );
    }
}