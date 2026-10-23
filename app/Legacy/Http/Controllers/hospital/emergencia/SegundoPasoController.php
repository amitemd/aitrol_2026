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

class SegundoPasoController extends Controller
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

        return view('hospital.emergencia.segundopaso',['solicitud' => $solicitud]);
    }
    public function store(Request $request)
    {
        //dd($request->all());
        $id_solicitud    = $request->solicitud_id;
        $fecha           = $request->fecha;
        $grupo_sanguineo = $request->grupo_sanguineo;
        $motivo          = $request->motivo;

        $trauma          = 0;
        $c_clinica       = 0;
        $c_obstetrica    = 0;
        $c_quirurgica    = 0;
        $not_policia     = 0;
        $otros           = 0;

        if(isset($request->trauma)){
            $trauma = $request->trauma;    
        }
        if(isset($request->c_clinica)){
            $c_clinica = $request->c_clinica;
        }    
        
        if(isset($request->c_obstetrica)){
            $c_obstetrica    = $request->c_obstetrica;
        }    
                
        if(isset($request->c_quirurgica)){
            $c_quirurgica = $request->c_quirurgica;    
        }    
                    
        if(isset($request->not_policia)){
            $not_policia = $request->not_policia;    
        }    
                        
        if(isset($request->otros)){
            $otros = $request->otros;    
        }    
                            
        if(isset($request->motivo)){
            $motivo = $request->motivo;
        }    
        

        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $paciente   = $solicitud->paciente;
        $form008    = $solicitud->form008->first();
        //$paciente->update([
            //'fecha_nacimiento' => $f_nacimiento,
            //'gruposanguineo'  => $grupo_sanguineo, 
        //]);
        $form008->update([
            'motivo'        => $motivo,
            'condicion'     => $request['condicion_llegada'],
        ]);
        $solicitud->update([
            'fecha_ingreso' => $fecha,
        ]);


        return view('hospital.emergencia.segundopaso',['solicitud' => $solicitud]);
    }
}
