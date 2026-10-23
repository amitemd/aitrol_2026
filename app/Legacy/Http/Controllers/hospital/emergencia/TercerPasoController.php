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

class TercerPasoController extends Controller
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
    public function index($id_sol)
    {
        $solicitud = Ho_Solicitud::find($id_sol);

        return view('hospital.emergencia.tercerpaso',['solicitud' => $solicitud]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $id_solicitud    = $request->solicitud_id;       

        $custodia_policial          = 0;
        $accidente_transito         = 0;
        $mordedura                  = 0;
        $violencia_armcp            = 0;
        $caida                      = 0;
        $ahogamiento                = 0;
        $violencia_rina             = 0;
        $quemadura                  = 0;
        $violencia_armf             = 0;
        $violencia_familiar         = 0;
        $abuso_psicologico          = 0;
        $intoxicacion_alcoholica    = 0;
        $anafilaxia                 = 0;
        $intoxicacion_gases         = 0;
        $intoxicacion_alimentaria   = 0;
        $envenenamiento             = 0;
        $intoxicacion_drogas        = 0;
        $picadura                   = 0;
        $abuso_fisico               = 0;
        $abuso_sexual               = 0;
        $aliento_etilico            = 0;
        $acc_notificacion =0;


        if(isset($request->custodia)){
            $custodia_policial = $request->custodia;    
        }
        if(isset($request->accidente_transito)){
            $accidente_transito = $request->accidente_transito;
        }    
        
        if(isset($request->mordedura)){
            $mordedura    = $request->mordedura;
        }    
                
        if(isset($request->arma_punzante)){
            $violencia_armcp = $request->arma_punzante;    
        }    
                    
        if(isset($request->caida)){
            $caida = $request->caida;    
        }    
                        
        if(isset($request->ahogamiento)){
            $ahogamiento = $request->ahogamiento;    
        }    
                            
        if(isset($request->rina)){
            $violencia_rina = $request->rina;
        }  
        if(isset($request->quemadura)){
            $quemadura = $request->quemadura;    
        }
        if(isset($request->arma_fuego)){
            $violencia_armf = $request->arma_fuego;
        }    
        
        if(isset($request->violencia_familiar)){
            $violencia_familiar    = $request->violencia_familiar;
        }    
                
        if(isset($request->abuso_psicologico)){
            $abuso_psicologico = $request->abuso_psicologico;    
        }    
                    
        if(isset($request->intoxicacion_alcoholica)){
            $intoxicacion_alcoholica = $request->intoxicacion_alcoholica;    
        }    
                        
        if(isset($request->anafilaxia)){
            $anafilaxia = $request->anafilaxia;    
        }    
                            
        if(isset($request->intoxicacion_gases)){
            $intoxicacion_gases = $request->intoxicacion_gases;
        }   
        if(isset($request->intoxicacion_alimentaria)){
            $intoxicacion_alimentaria = $request->intoxicacion_alimentaria;    
        }
        if(isset($request->envenenamiento)){
            $envenenamiento = $request->envenenamiento;
        }    
        
        if(isset($request->intoxicacion_drogas)){
            $intoxicacion_drogas    = $request->intoxicacion_drogas;
        }    
                
        if(isset($request->picadura)){
            $picadura = $request->picadura;    
        }    
                    
        if(isset($request->abuso_fisico)){
            $abuso_fisico = $request->abuso_fisico;    
        }    
                        
        if(isset($request->abuso_sexual)){
            $abuso_sexual = $request->abuso_sexual;    
        }    
                            
        if(isset($request->aliento_alcohol)){
            $aliento_etilico = $request->aliento_alcohol;
        }

        if(isset($request->acc_notificacion)){
            $acc_notificacion = $request->acc_notificacion;
        }

        $solicitud  = Ho_Solicitud::find($id_solicitud);
       // $paciente   = $solicitud->paciente;
        $form008    = $solicitud->form008->first();
       /* $paciente->update([
            //'fecha_nacimiento' => $f_nacimiento,
            'gruposanguineo'  => $grupo_sanguineo, 
        ]);*/
        $form008->update([
            'custodia_policial' => $custodia_policial,  
            'accidente_transito' => $accidente_transito,
            'mordedura' => $mordedura,
            'violencia_armcp' => $violencia_armcp,
            'caida' => $caida,
            'ahogamiento' => $ahogamiento,
            'violencia_rina' => $violencia_rina,
            'quemadura' => $quemadura,
            'violencia_armf' => $violencia_armf,
            'violencia_familiar' => $violencia_familiar,
            'abuso_psicologico' => $abuso_psicologico,
            'intoxicacion_alcoholica' => $intoxicacion_alcoholica,
            'anafilaxia' => $anafilaxia,
            'intoxicacion_gases' => $intoxicacion_gases,
            'intoxicacion_alimentaria' => $intoxicacion_alimentaria,
            'envenenamiento' => $envenenamiento,
            'intoxicacion_drogas' => $intoxicacion_drogas,
            'picadura' => $picadura,
            'abuso_fisico' => $abuso_fisico,
            'abuso_sexual' => $abuso_sexual,
            'aliento_etilico' => $aliento_etilico,
            'lugar_evento'  => $request->lugar_evento,
            'direccion_evento' => $request->direccion_evento,
            'valor_alcocheck' => $request->valor_alcohol,
            'observacion_p3'  => $request->observacion3,
            'acc_notificacion' => $acc_notificacion,
            'cuerpo_extrano' => $request->cuerpo_extrano,
            'aplastamiento' => $request->aplastamiento,
            'otro_accidente' => $request->otro_accidente,
            'otra_intoxicacion' => $request->otra_intoxicacion,
        ]);
        
        
        return view('hospital.emergencia.tercerpaso',['solicitud' => $solicitud]);
    }
}