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

class NovenoPasoController extends Controller
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
    public function novenopaso(Request $request, $id)
    {
        $solicitud = Ho_Solicitud::find($id);
        return view('hospital.emergencia.novenopaso',['solicitud' => $solicitud]);
    }
    public function store(Request $request)
    {
        //dd($request->solicitud_id);
        $id_solicitud    = $request->solicitud_id;
        $fecha           = $request->fecha;
        $grupo_sanguineo = $request->grupo_sanguineo;
        $motivo          = $request->motivo;

        $gestas            = 0;    
        $abortos           = 0;
        $partos            = 0;
        $cesareas          = 0;
        $ultima_mens       = 0;
        $semanas_gest      = 0;
        $mov_fetal         = 0;
        $frecuencia_fetal  = 0;
        $membranas_rotas   = 0;
        $motivo            = 0;
        $tiempo            = 0;
        $altura_uterina    = 0;
        $presentacion      = 0;
        $dilatacion        = 0;
        $borramiento       = 0;
        $plano             = 0;
        $pelvis_util       = 0;
        $sangrado_vaginal  = 0;
        $contracciones     = 0;
        
        if(isset($request->trauma)){
            $trauma = $request->trauma;    
        }
        if(isset($request->abortos)){
            $abortos = $request->abortos;
        }    
        
        if(isset($request->partos)){
            $partos    = $request->partos;
        }    
                
        if(isset($request->cesareas)){
            $cesareas = $request->cesareas;    
        }    
                    
        if(isset($request->ultima_mens)){
            $ultima_mens = $request->ultima_mens;    
        }    
                        
        if(isset($request->semanas_gest)){
            $semanas_gest = $request->semanas_gest;    
        }    
                            
        if(isset($request->mov_fetal)){
            $mov_fetal = $request->mov_fetal;
        }  
        if(isset($request->membranas_rotas)){
            $membranas_rotas = $request->membranas_rotas;
        }  
        if(isset($request->motivo)){
            $motivo = $request->motivo;
        }  
        if(isset($request->tiempo)){
            $tiempo = $request->tiempo;
        }    
        if(isset($request->altura_uterina)){
            $altura_uterina = $request->altura_uterina;
        }   
        if(isset($request->presentacion)){
            $presentacion = $request->presentacion;
        }   
        if(isset($request->dilatacion)){
            $dilatacion = $request->dilatacion;
        }   
        if(isset($request->borramiento)){
            $borramiento = $request->borramiento;
        } 
        if(isset($request->plano)){
            $plano = $request->plano;
        } 
        if(isset($request->pelvis_util)){
            $pelvis_util = $request->pelvis_util;
        }
        if(isset($request->sangrado_vaginal)){
            $sangrado_vaginal = $request->sangrado_vaginal;
        }
        if(isset($request->contracciones)){
            $contracciones = $request->contracciones;
        }

        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $paciente   = $solicitud->paciente;
        $form008    = $solicitud->form008->first();
        //dd($form008);
        $paciente->update([
            //'fecha_nacimiento' => $f_nacimiento,
            'gruposanguineo'  => $grupo_sanguineo, 
        ]);
        $form008->update([
            'gestas'        => $gestas,
            'partos'        => $partos,
            'abortos'       => $abortos,
            'cesareas'      => $cesareas,
            'ultima_menstruacion'     => $ultima_mens,
            'posible_parto'           => $partos,
            'semanas_gestacion'       => $semanas_gest,
            'movimiento_fetal'        => $mov_fetal,
            'frecuencia_fetal'        => $frecuencia_fetal,
            'membranas_rotas'         => $membranas_rotas,
            'altura_uterina'          => $motivo,
            'tiempo_ruptura'          => $tiempo,
            'altura_uterina'          => $altura_uterina,
            'presentacion'            => $presentacion,
            'dilatacion'              => $dilatacion,
            'borramiento'             => $borramiento,
            'plano'                   => $plano,
            'pelvis_util'             => $pelvis_util,
            'sangrado_vaginal'        => $sangrado_vaginal,
            'contracciones'           => $contracciones,
            'score_mama'              => $request->score_mama,   


        ]);
        $solicitud->update([
            'fecha_ingreso' => $fecha,
        ]);


        return view('hospital.emergencia.novenopaso',['solicitud' => $solicitud]);
    }
}