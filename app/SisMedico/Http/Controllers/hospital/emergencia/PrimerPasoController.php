<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class PrimerPasoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    public function index($id)
    {

        $solicitud = Ho_Solicitud::find($id);

        return view('hospital.emergencia.primerpaso', ['solicitud' => $solicitud]);
    }
}
