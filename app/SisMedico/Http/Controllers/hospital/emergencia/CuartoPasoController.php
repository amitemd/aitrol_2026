<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Ho_Solicitud;

class CuartoPasoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    public function index($id)
    {
        $solicitud = Ho_Solicitud::find($id);

        $alergias = $solicitud->paciente->a_alergias; $txt_al = '';$cont = 0;
        foreach($alergias as $alergia){ 
            if($cont==0){ $txt_al = $alergia->principio_activo->nombre; }
            else{ $txt_al = $txt_al.' + '.$alergia->principio_activo->nombre; }
            $cont++;
        }  

        return view('hospital.emergencia.cuartopaso',['solicitud' => $solicitud, 'txt_al' => $txt_al, 'alergias' => $alergias]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $clinico            = $request->clinicos;
        $ginecologico       = $request->ginecologico;
        $traumatologico     = $request->traumatologicos;
        $antecedentes_quir  = $request->antecedentes_quir;
        $farmacologico      = $request->farmacologico;
        $psiquiatrico       = $request->psiquiatrico;
        $antecedentes_fam   = $request->antecedentes_fam;
        $id_solicitud       = $request->id_solicitud;  

        $solicitud      = Ho_Solicitud::find($id_solicitud);
        $paciente       = $solicitud->paciente;
        $datos_paciente = $paciente->ho_datos_paciente;
        
        $datos_paciente->update([
            'clinico'        => $clinico,
            'ginecologico'   => $ginecologico,
            'traumatologico' => $traumatologico,
            'farmacologico'  => $farmacologico,
            'psiquiatrico'   => $psiquiatrico,
            'antecedentes_pediatricos' => $request->antecedentes_pediatricos,
            'otros_antecedente' => $request->otros_antecedente,
            'habitos'        => $request->habitos,

        ]);

        $paciente->update([ 
            'antecedentes_quir' => $antecedentes_quir,
            'antecedentes_fam'  => $antecedentes_fam,
            
        ]);

        $alergias = $solicitud->paciente->a_alergias; $txt_al = '';$cont = 0;
        foreach($alergias as $alergia){ 
            if($cont==0){ $txt_al = $alergia->principio_activo->nombre; }
            else{ $txt_al = $txt_al.' + '.$alergia->principio_activo->nombre; }
            $cont++;
        } 


        return view('hospital.emergencia.cuartopaso',['solicitud' => $solicitud, 'txt_al' => $txt_al, 'alergias' => $alergias]);
    }
}
