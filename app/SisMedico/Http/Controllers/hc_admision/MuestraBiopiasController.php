<?php

namespace Sis_medico\Http\Controllers\hc_admision;

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
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Paciente;


class MuestraBiopiasController extends Controller
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

    public function index_muestras(Request $request){
       
      

        $biopsias = $request->all();
        $fecha     = $request['fecha'];
        $fechafin  = $request['fechafin'];

        //return $request;
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }

        $nombres           = $request['nombres'];
        $cedula            = $request['cedula'];
        $biopsias          = Hc4_Biopsias:: where('estado', '1')->whereBetween('hc4_biopsias.created_at', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59']);
        //->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2');
                     
        //dd($biopsias->get());
         
        
        if (!is_null($cedula)) {
            $biopsias->where('id_paciente', $cedula);
        }

          //dd($biopsias->get());
        
        $biopsias   = $biopsias->select('hc4_biopsias.*')->paginate(15);
        //dd($biopsias);

     return view ('hc_admision/biopsias/index', ['fecha' => $fecha , 'fechafin' => $fechafin , 'nombres' => $nombres , 'cedula' => $cedula , 'biopsias' => $biopsias]);
    }

    public function update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());

        $id = $request->id;
        //dd($request->all());
        
        $biopsias = Hc4_Biopsias::find($id);
        $biopsias->muestra_biopsia = $request['estado'];
        $biopsias->id_usuariomod  = $idusuario;
        $biopsias->ip_modificacion = $ip_cliente;

        $biopsias->save();

        // $hc_id_procedimiento = $request['hc_id_procedimiento'.$id];
        // $hcid                = $request['hcid'.$id];
        // $id_paciente         = $request['id_paciente'.$id];
        // $id_doctor           = $request['id_doctor'.$id];
        // $descripcion_frasco  = $request['descripcion_frasco'.$id];
        // $numero_frasco       = $request['numero_frasco'.$id];
        // $muestra_biopsia      = 0;
        // if (($muestra_biopsia == 'SI')){
        //     $muestra_biopsia = 1;
        // }    

        // $arr = [
        //   'hc_id_procedimiento' => $hc_id_procedimiento,
        //   'hcid'                => $hcid,
        //   'id_paciente'         => $id_paciente,
        //   'id_doctor'           => $id_doctor,
        //   'descripcion_frasco'  => $descripcion_frasco,
        //   'muestra_biopsia'      => $muestra_biopsia,  
        // ];  
        // $muestras->update($arr);

         return  ['msj'=> 'guardado correctamente'];
    }
}