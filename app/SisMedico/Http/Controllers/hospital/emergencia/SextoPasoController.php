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
use Sis_medico\Services\ClinicalEmergencyVitalSignsService;

class SextoPasoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
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
        $vitalSigns = app(ClinicalEmergencyVitalSignsService::class);

        return view('hospital.emergencia.sextopaso',[
            'solicitud' => $solicitud,
            'ocular' => $ocular,
            'verbal' => $verbal,
            'motora' => $motora,
            'triaje' => $vitalSigns->firstTriage($solicitud),
            'vitalValues' => $vitalSigns->values($solicitud),
        ]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        //dd("a");
        $id_solicitud    = $request->solicitud_id;  
        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $form008    = $solicitud->form008->first();
        $hc         = $form008->agenda->historia_clinica; 

        $oxygen = $request->satura_oxigeno;
        if ($oxygen === null || $oxygen === '') {
            $oxygen = $request->pulsioximetria;
        }
        $temperature = $request->temp_bucal;
        if ($temperature === null || $temperature === '') {
            $temperature = $request->temp_axilar;
        }
        $glasgowTotal = $request->total_glas;
        if ($glasgowTotal === null || $glasgowTotal === '') {
            $glasgowTotal = (int) $request->ocular + (int) $request->verbal + (int) $request->motora;
        }

        $form008->update([
            'motora'            => $request->motora,
            'verbal'            => $request->verbal,
            'ocular'            => $request->ocular,
            'total_glas'        => $glasgowTotal,
            'glasgow_inicial'   => $glasgowTotal,
            'satura_oxigeno'    => $oxygen,
            'reac_pupila_der'   => $request->pupila_der,
            'reac_pupila_izq'   => $request->pupila_izq,
            't_llenado_capilar' => $request->llenado_capilar,
            'temp_bucal'        => $request->temp_bucal,
            'temp_axilar'       => $request->temp_axilar,
            'frec_respiratoria' => $request->frec_respiratoria,
            'sin_constantes'    => $request->sin_constantes,
            'pulsioximetria'    => $oxygen,
            'perimetro_cefalico' => $request->perimetro_cefalico,
            'glicemia_capilar'   => $request->glicemia_capilar,
        ]);

        $hc->update([
            'presion'       => $request->presion_arterial,
            'pulso'         => $request->frec_cardiaca,
            'frecuencia_respiratoria' => $request->frec_respiratoria,
            'temperatura'    => $temperature,
            'o2'             => $oxygen,
            'peso'          => $request->peso,
            'altura'        => $request->talla,
            'glasgow'       => $glasgowTotal,
            'ocular'        => $request->ocular,
            'verbal'        => $request->verbal,
            'motora'        => $request->motora,
            'reaccion_pupila_der' => $request->pupila_der,
            'reaccion_pupila_izq' => $request->pupila_izq,
            't_llenado_capilar' => $request->llenado_capilar,
        ]);

        $ocular = Ho_Glasgow::where('tipo','1')->where('estado','1')->get();
        $verbal = Ho_Glasgow::where('tipo','2')->where('estado','1')->get();
        $motora = Ho_Glasgow::where('tipo','3')->where('estado','1')->get();
        $form008->refresh();
        $hc->refresh();

        $vitalSigns = app(ClinicalEmergencyVitalSignsService::class);
        $viewData = [
            'solicitud' => $solicitud,
            'ocular' => $ocular,
            'verbal' => $verbal,
            'motora' => $motora,
            'triaje' => $vitalSigns->firstTriage($solicitud),
            'vitalValues' => $vitalSigns->values($solicitud, $form008, $hc),
        ];

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'html' => view('hospital.emergencia.sextopaso', $viewData)->render(),
                'vitals' => $vitalSigns->summary($solicitud, $form008, $hc),
            ]);
        }

        return view('hospital.emergencia.sextopaso', $viewData);
    }
}
