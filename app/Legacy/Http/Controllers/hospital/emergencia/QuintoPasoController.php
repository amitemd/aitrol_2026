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

class QuintoPasoController extends Controller
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
    public function quintopaso($id_sol)
    {
        $solicitud = Ho_Solicitud::find($id_sol);

        return view('hospital.emergencia.quintopaso',['solicitud' => $solicitud]);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $id_solicitud    = $request->solicitud_id;  

        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $form008    = $solicitud->form008->first();

     
        $form008->update([
            'aerea_libre'            => $request->aerea_libre,
            'aerea_obstruida'        => $request->aerea_obstruida,
            'condicion_estable'      => $request->condicion_estable,
            'condicion_inestable'    => $request->condicion_inestable,
            'observacion_quintop'     => $request->observacion_quintop,
        ]);

       /*$solicitud->update([
            'fecha_ingreso' => $fecha,
        ]);*/

        return view('hospital.emergencia.quintopaso',['solicitud' => $solicitud]);
    }
}
