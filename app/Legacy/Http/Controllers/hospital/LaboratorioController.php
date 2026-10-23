<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Examen_Orden;
use Sis_medico\Seguro;

class LaboratorioController extends Controller
{
public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request ){
        
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        //dd($request->all());
        $nombres = $request['nombres'];

        if ($request['fecha'] == null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = $request['fecha'];
        }
        if ($request['fecha_hasta'] == null) {
            $fecha_hasta = date('Y-m-d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];

        $ordenes = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->leftjoin('empresa as em', 'em.id', 'examen_orden.id_empresa')->leftjoin('nivel as n', 'n.id', 'examen_orden.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'examen_orden.id_protocolo')->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')->join('users as cu', 'cu.id', 'examen_orden.id_usuariocrea')->join('users as mu', 'mu.id', 'examen_orden.id_usuariomod')->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo');

        if ($fecha != null) {

            $ordenes = $ordenes->whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });

            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }

        }

        
        $ordenes = $ordenes->where('examen_orden.estado', '1')->paginate(30);

        //dd($ordenes); 
        $seguros = Seguro::where('inactivo', '1')->get();
        //Cookie::queue('fecha_desde', $fecha, '1000');
        //Cookie::queue('fecha_hasta', $fecha_hasta, '1000');

        return view('hospital/laboratorio/index', [ 'ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'facturadas' => null]);

    }
     
  
}