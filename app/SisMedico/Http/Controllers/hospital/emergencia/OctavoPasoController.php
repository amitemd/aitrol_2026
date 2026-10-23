<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ho_Colores008;
use Sis_medico\Ho_Lesiones008;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class OctavoPasoController extends Controller
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
    public function __index(Request $request)
    {
        //
        $colores = Ho_Colores008::where('estado', '1')->get();
        $ho = Ho_Lesiones008::where('id_008', $request['ep'])->first();
        return view('hospital.octavopaso', ['colores' => $colores, 'ho' => $ho]);
    }

    
    public function ___save(Request  $request)
    {
        $id = $request['id'];
        $verificar = Ho_Lesiones008::where('id_008', $id)->first();
        $extension = 'ho_' . $id . '_' . date('Ymds') . '_' . $request['my-file']->getClientOriginalName();
        if (is_null($verificar)) {
            Ho_Lesiones008::create([
                'id_008' => $id,
                'url_imagen' => $extension,
                'nombre' => $extension
            ]);
        } else {
            $verificar->url_imagen = $extension;
            $verificar->save();
        }
        $fileName =  $extension;
        //ingresar la foto
        Storage::disk('hc_ima')->put($fileName, \File::get($request['my-file']));
        return response()->json(['success' => '1']);
    }

    public function index($id_sol)
    {
        
        $solicitud = Ho_Solicitud::find($id_sol);

        return view('hospital.emergencia.octavopasonuevo', ['solicitud' => $solicitud]);
    }
    public function store_octavo(Request  $request)
    {
        $id_solicitud    = $request->solicitud_id;

        $solicitud  = Ho_Solicitud::find($id_solicitud);
        $form008    = $solicitud->form008->first();


        $form008->update([
            'examen_fisico_obs'     => $request->examen_fisico_obs,
        ]);

        return view('hospital.emergencia.octavopasonuevo', ['solicitud' => $solicitud]);
    }
}
