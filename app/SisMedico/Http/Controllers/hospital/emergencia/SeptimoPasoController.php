<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Services\HospitalEpisodePhysicalExam;

class SeptimoPasoController extends Controller
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

    public function septimopaso($id_sol)
    {
        $solicitud = Ho_Solicitud::find($id_sol);

        return view('hospital.emergencia.septimopaso', ['solicitud' => $solicitud]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $id_solicitud = $request->solicitud_id;

        $solicitud = Ho_Solicitud::find($id_solicitud);
        $form008 = $solicitud->form008->first();

        $examFields = array_column(
            array_merge(...array_values(HospitalEpisodePhysicalExam::fieldGroups())),
            'field'
        );
        $form008->update($request->only($examFields));

        /*$solicitud->update([
             'fecha_ingreso' => $fecha,
         ]);*/

        return view('hospital.emergencia.septimopaso', ['solicitud' => $solicitud]);
    }
}
