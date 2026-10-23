<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use IlluminateFilesystem\FilesystemManager;
use Response;
use Excel;
use Sis_medico\Examen;
use Sis_medico\Labs_Tipo_Tubo;
use  Sis_medico\Http\Controllers\laboratorio\OrdenController;


class MantenimientoController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }


    public function index()
    {
        $examenes = Examen::paginate(10);
        $tipo = Labs_Tipo_Tubo::all();
        return view('servicios_generales.examenes_mantenimiento.index', ['examenes' => $examenes,'tipo'=>$tipo]);
    }

    public function actualizar(Request $request)
    {
        $examenes = Examen::find($request['id']);
        $tipo = Labs_Tipo_Tubo::all();
        return view('servicios_generales.examenes_mantenimiento.update', ['tipo'=>$tipo ,'examenes' => $examenes]);
    }
    public function update(Request $request)
    {
        //dd($request->all());
        $examenes = Examen::find($request['id']);

        try {
            $examenes->cantidad_tubos = $request['cantidad'];
            $examenes->indice_tubos  = $request['indice'];
            $examenes->id_labs_tubo  = $request['id_labs'];
            $examenes->save();

            return json_encode('ok');
        } catch (\Throwable $th) {
            //dd($th);
            return json_encode('error');
        }
    }

    public function buscador(Request $request)
    {

        $examenes = Examen::where('nombre','like','%'.$request['nombre'].'%')->paginate(10);
        return view('servicios_generales.examenes_mantenimiento.index', ['examenes' => $examenes]);
    }

    public function excel_tubos (){

       Excel::filter('chunk')->load('public/exceltubos.xlsx')->chunk(250, function ($results) {
       //dd($results);
       $idusuario  = Auth::user()->id;
       $ip_cliente = $_SERVER["REMOTE_ADDR"];

           foreach($results as $row)
           {

               $agrup = '';
               $examen = Examen::where('id',intval($row['id']))->first();
               if($row['unicoagrupado'] == 'AGRUPADO' ){
                 $agrup = 'G';
               }else{
                   $agrup = 'U';
               }
               $tubo = Labs_Tipo_Tubo::where('nombre',$row['tipo_tubo'])->first();
               if(!is_null($tubo)){
                 $examen->cantidad_tubos = $row['cantidad'];
                 $examen->indice_tubos = $agrup;
                 $examen->id_labs_tubo = $tubo->id;
                 $examen->save();
               }


           }
   });
   }

   public function imprimir_barra ($id){

      $service = new OrdenController;
      $examen_orden = $service->imprimirCod($id);
      if(empty($examen_orden)){
        $examen_orden = [];
      }

     $view = \View::make('laboratorio.orden.pdf_codigo_barra', compact('examen_orden'))->render();
    return $view;

   }
}
