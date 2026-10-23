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
use Sis_medico\Medicina;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;

class TreceavoPasoController extends Controller
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
    public function treceavopaso($id_sol)
    {
        $solicitud = Ho_Solicitud::find($id_sol);

        $historia = $solicitud->agenda->historia_clinica;

        $receta = hc_receta::where('id_hc',$historia->hcid)->first();

        return view('hospital.emergencia.treceavopaso',['solicitud' => $solicitud, 'detalles' => $receta->detalles, 'receta' => $receta]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $id_solicitud    = $request->solicitud_id;  

        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $form008    = $solicitud->form008->first();

     
        $form008->update([
            'via_aerea_obs'   => $request->via_aerea_obs,
            'cabeza'          => $request->cabeza,
            'cuello'          => $request->cuello,
            'torax'           => $request->torax,
            'abdomen'         => $request->abdomen,
            'columna'         => $request->columna,
            'pelvis'          => $request->pelvis,
            'extremidades'    => $request->extremidades,
        ]);

       /*$solicitud->update([
            'fecha_ingreso' => $fecha,
        ]);*/

        return view('hospital.emergencia.treceavopaso',['solicitud' => $solicitud]);
    }

    public function guardar_medicina($id_solic, Request $request)
    {
       
        /*$opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }*/

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_generico = $request['id_generico'];
        $solicitud = Ho_Solicitud::find($id_solic);

        $historia = $solicitud->agenda->historia_clinica;

        $receta = hc_receta::where('id_hc',$historia->hcid)->first();
        
        $medicina = Medicina::find($id_generico);

        if (is_null($medicina)) {
            return "0";
        }
        
        if (!is_null($receta->detalles)) {
            $receta_detalle = $receta->detalles->where('id_medicina',$medicina->id)->first();

            if(is_null($receta_detalle)){

                hc_receta_detalle::create([
                    'id_hc_receta' => $receta->id,
                    'id_medicina'  => $medicina->id,
                    'nombre'       => $medicina->nombre,     
                    'cantidad'     => 1,
                    'dosis'        => $medicina->dosis,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario    

                ]);

            }
        }

        

        $receta2 = hc_receta::find($receta->id);

        return view('hospital.emergencia.treceavopaso.receta_detalle',['receta' => $receta, 'detalles' => $receta2->detalles ]);
        
    }

    public function actualizar_doctor($id_receta, Request $request){
        //dd($request->all());
        $receta = hc_receta::find($id_receta);
        if($receta->id_doctor_examinador == null){
            $receta->update([
                'id_doctor_examinador' => $request->id_doctor,
            ]);
        }
        dd($id_receta);

    }
}
