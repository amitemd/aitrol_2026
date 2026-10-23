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
use Sis_medico\Ho_Glasgow;

class SextoPasoController extends Controller
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
    public function sextopaso($id_sol)
    {
        $solicitud = Ho_Solicitud::find($id_sol);
        $ocular = Ho_Glasgow::where('tipo','1')->where('estado','1')->get();
        $verbal = Ho_Glasgow::where('tipo','2')->where('estado','1')->get();
        $motora = Ho_Glasgow::where('tipo','3')->where('estado','1')->get();
        

        return view('hospital.emergencia.sextopaso',['solicitud' => $solicitud, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        //dd("a");
        $id_solicitud    = $request->solicitud_id;  
        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $form008    = $solicitud->form008->first();
        $hc         = $form008->agenda->historia_clinica; 

        $form008->update([
            'motora'            => $request->motora,
            'verbal'            => $request->verbal,
            'ocular'            => $request->ocular,
            'total_glas'        => $request->total_glas,
            'satura_oxigeno'    => $request->satura_oxigeno,
            'reac_pupila_der'   => $request->pupila_der,
            'reac_pupila_izq'   => $request->pupila_izq,
            't_llenado_capilar' => $request->llenado_capilar,
            'temp_bucal'        => $request->temp_bucal,
            'temp_axilar'       => $request->temp_axilar,
            'frec_respiratoria' => $request->frec_respiratoria,
            'sin_constantes'    => $request->sin_constantes,
            'pulsioximetria'    => $request->pulsioximetria,
            'perimetro_cefalico' => $request->perimetro_cefalico,
            'glicemia_capilar'   => $request->glicemia_capilar,
        ]);

        $hc->update([
            'presion'       => $request->presion_arterial,
            'pulso'         => $request->frec_cardiaca,
            'peso'          => $request->peso,
            'altura'        => $request->talla,
        ]);

        $ocular = Ho_Glasgow::where('tipo','1')->where('estado','1')->get();
        $verbal = Ho_Glasgow::where('tipo','2')->where('estado','1')->get();
        $motora = Ho_Glasgow::where('tipo','3')->where('estado','1')->get();

        return view('hospital.emergencia.sextopaso',['solicitud' => $solicitud, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }
}