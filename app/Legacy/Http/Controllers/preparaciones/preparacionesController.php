<?php

namespace Sis_medico\Http\Controllers\preparaciones;

use DateTime;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\User;
use Excel;
use PDF;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Preparaciones;


class preparacionesController extends Controller
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

    public function index(Request   $request){
     $preparaciones = Preparaciones::where('estado','1')->get();
     $imagen = Preparaciones::where('estado','1')->get();

     return view('preparaciones/index',['preparaciones'=> $preparaciones , 'imagen' => $imagen]);
    }  
    
    public function crear(){
    $imagen = preparaciones::where('estado','1')->get();
    return view('preparaciones/crear', ['imagen' => $imagen]);
    }


   public function guardar_preparaciones (Request $request)
  {
    //dd($request->all());

    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    date_default_timezone_set('America/Guayaquil');
    $fecha=date("Y-m-d");
    
    $nombre_original = $request['archivo_preparaciones']->getClientOriginalName();

        $extension       = $request['archivo_preparaciones']->getClientOriginalExtension();
        
        $tiempo = time();

        $nuevo_nombre = "preparaciones_{$tiempo}.{$extension}";

        //$nuevo_nombre    = "preparaciones".$fecha.'_'.".".$extension;

        //dd($nuevo_nombre);

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo_preparaciones']));

        $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
       //dd($rutadelaimagen);

    preparaciones::create([
    'nombre_preparaciones'=> $request['nombre_preparaciones'],
    'archivo_preparaciones'=> $nuevo_nombre,
    //'archivo_preparaciones'=> $request['archivo_preparaciones'],
    'estado'=> 1,
    'ip_creacion'=> $ip_cliente,
    'id_usuariocrea'=> $idusuario,
    'ip_modificacion'=> $ip_cliente,
    'id_usuariomod'=> $idusuario, 
   ]);    
     return redirect(route('preparaciones.index'));
  }

     public function mostrar_pdf(Request $request)
    {

        $preparacion= Preparaciones::find($request->preparacion);
        $path1 = storage_path() . "/app/avatars/" . $preparacion->archivo_preparaciones;
        return response()->file($path1);
    }

}