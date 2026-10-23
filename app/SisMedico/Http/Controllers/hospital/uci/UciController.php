<?php

namespace Sis_medico\Http\Controllers\hospital\uci;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Examen_Orden;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Paciente;
use Sis_medico\Sala;

class UciController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $salas = Sala::where('estado','1')->where('id_hospital','5')->get();
        

        return view('hospital/uci/index',['salas' =>$salas]);;
    }

    public function index_uci($id_solicitud){

        $solicitud = Ho_solicitud::find($id_solicitud);
        $paciente = Paciente::find($solicitud->id_paciente);

        $log = $solicitud->log->last();

        $historia = $solicitud->agenda->historia_clinica;
        //dd($historia);

        $evolucion = $historia->evoluciones->last();

        $child_pugh = $evolucion->child_pug;
        $examenes = Examen_Orden::where('id_paciente', $solicitud->id_paciente)->latest('created_at')->first();
        return view('hospital/uci/index_uci',['examenes' => $examenes, 'paciente' => $paciente, 'solicitud' => $solicitud, 'log' => $log, 'evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'historia' => $historia]);
    }
}
