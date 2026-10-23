<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Porcentajes_Retencion_Iva;
use Sis_medico\Ct_Porcentajes_Retencion_Fuente;
use Sis_medico\Ct_Porcentaje_Retencion_Venta;
use laravel\laravel;
use Carbon\Carbon;


class RetencionVentasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }


    /*************************************
    *************OBTENER MODAL************
    /*************************************/

    public function obtener_modal(Request $request)
    {
      
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_client = $request['id_cl'];
        $sec_Fact = $request['secuencia'];
        $id_vent = $request['id_venta'];


        $cliente = Ct_Clientes::where('identificacion', $id_client)
                   ->where('estado', '1')->first();

        $ct_vent =  Ct_ventas::where('id', $id_vent)
                   ->where('estado', '1')->first(); 

        //Obtenemos los % de Retenciones al Iva y la Fuente
        $rete_iva = Ct_Porcentajes_Retencion_Iva::where('estado', '1')->get(); 
        $rete_fuente = Ct_Porcentajes_Retencion_Fuente::where('estado', '1')->get();




        return view('contable/retencion_ventas/modal_retencion_ventas',['cliente' => $cliente,'ct_vent' => $ct_vent,'rete_iva' => $rete_iva,'rete_fuente' => $rete_fuente]);
    }

    

    /*************************************
    ****OBTENER PORCENTAJE IVA FUENTE*****
    /*************************************/
    public function obtener_porcent_iva_fuente(Request $request)
    {
        
        if($this->rol()){
            return response()->view('errors.404');
        }

        $id_porcent = $request['id_por'];


        if(!is_null($id_porcent)){

            $iva_fuente = Ct_Porcentaje_Retencion_Venta::where('tipo', $id_porcent)->get();
                       
            return $iva_fuente;
        }                
        

        return 'no';

    }


    /*************************************
    *******OBTENER CODIGO PORCENTAJE******
    /*************************************/
    public function obtener_codigo(Request $request)
    {
      
        if($this->rol()){
            return response()->view('errors.404');
        }


        $cod = $request['codig_ret'];

        if(!is_null($cod)){

            $codigo_porcent = Ct_Porcentaje_Retencion_Venta::where('id', $cod)->where('estado', '1')->first();

            $codigo = $codigo_porcent->codigo; 

            return $codigo;

        }

        return 'no';
    }


}
