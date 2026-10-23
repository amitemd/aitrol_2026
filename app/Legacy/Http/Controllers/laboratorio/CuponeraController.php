<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;
use Sis_medico\Examen_Obligatorio;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Convenio;
use Sis_medico\Cupones;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;


class CuponeraController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 12, 11)) == false ){
          return true;
        }
        

    }
    
    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $cupones = Cupones::all();
        $arr=[];

        foreach ($cupones as $value) {
            
            $ordenes = Examen_Orden::whereBetween('ticket',[$value->inferior, $value->superior])->where('estado','1')->get();
            $arr[$value->id] = $ordenes->count();
            if($value->id=='8'){
                //dd($ordenes,$arr);
            }
        }

        $total = 0;
        foreach ($arr as $x) {
            $total += $x;
        }

        return view('laboratorio/cuponera/index',['cupones' => $cupones, 'arr' => $arr, 'total' => $total]);
    }

    

}    


    

    