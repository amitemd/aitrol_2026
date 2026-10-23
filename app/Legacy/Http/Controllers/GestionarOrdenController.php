<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Response;
use Storage;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\User;
use Carbon\Carbon;


class GestionarOrdenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index(Request $request)
    {
        //dd($request->all());  
        $desde = $request->desde;
        $hasta = $request->hasta;
        $tipo  = $request->tipo;
        $nombre_paciente = $request->id;

        if($desde == null){
            $desde = date('Y-m-d');
        }
        if($hasta == null){
            $hasta = date('Y-m-d');
        }
        

        $ordenes = DB::table('orden as o', 'orden_tipo')
            ->join('paciente as p','p.id','o.id_paciente')
            ->wherebetween('o.fecha_orden', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
        //la funcion de groupby esta a prueba     
        //dd($ordenes->get());
        if (!is_null($tipo)) {
            $ordenes = $ordenes->where('o.tipo_procedimiento', $request['tipo']);
        }

        if($nombre_paciente!=null)
        {
            $nombres2 = explode(" ", $nombre_paciente); 
            $cantidad = count($nombres2);
            $nombres_sql="";
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }

            $nombres_sql= $nombres_sql.'%';
            
            if($cantidad > '1'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
            }
            else{
                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 
            }
        }    
       
        $ordenes = $ordenes->select('o.*','p.apellido1','p.apellido2','p.nombre1','p.nombre2')->orderBy('o.fecha_orden', 'desc')->paginate(50);
        return view('ordenes_gestion/index', ['ordenes' => $ordenes, 'tipo' => $tipo,'desde'=>$desde,'hasta'=>$hasta,'nombre_paciente'=>$nombre_paciente]);
    }

    public function cantidad()
     {

         $fecha = date('Y-m-d');
         $observaciones = DB::table('orden')->join('orden_tipo as ot', 'orden.id', 'ot.id_orden')->whereBetween('fecha_orden', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->get();
         return $observaciones->count();
    }
}
