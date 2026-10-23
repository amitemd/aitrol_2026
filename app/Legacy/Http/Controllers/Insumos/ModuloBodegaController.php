<?php

namespace Sis_medico\Http\Controllers\Insumos;


use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Modulos;
use Sis_medico\ModuloBodega;

class ModuloBodegaController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $modulos = Modulos::where('estado', 1)->get();
        $bodegas    = Bodega::where('estado', 1)->where('id_empresa', $id_empresa)->get();


        return view('insumos/modulo_bodega/index', ['modulos' => $modulos, 'empresa' => $empresa, 'bodegas' => $bodegas]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        DB::beginTransaction();
        try {
            $modulo_bodega =  ModuloBodega::find($request->id_modulobodega);
            //dd($request->all());    
            if (!is_null($modulo_bodega)) {
                $arr = [
                    'id_bodega' => $request->id_bodega,
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
                $modulo_bodega->update($arr);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con exito', 'titulos' => 'Success'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function store_bodega_usuario(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        DB::beginTransaction();
        try {
            $id_bodega = $request->id_bodega;
            $id_modulo = $request->id_modulo;

            $bodega = Bodega::find($id_bodega);

            $modulo_bodega =  ModuloBodega::where('estado', 1)->where('id_modulo', $id_modulo)->where('id_usuario', $idusuario)->where('id_empresa', $id_empresa)->first();

            if (is_null($modulo_bodega)) {
                $arr = [
                    'id_modulo'         => $id_modulo,
                    'id_bodega'         => $id_bodega,
                    'id_empresa'        => $id_empresa,
                    'id_usuario'        => $idusuario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];

                ModuloBodega::create($arr);
            } else {
                $arr_up = [
                    'id_bodega'         => $id_bodega,
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $modulo_bodega->update($arr_up);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Success', 'nombre_bodega' => $bodega->nombre];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];

        }
    }

    public static function selectedBodega($module){
        $id_empresa = Session::get('id_empresa');

        $consult = ModuloBodega::where('id_empresa', $id_empresa)->where('id_modulo', $module)->first();

        if(!is_null($consult)){
            if(!is_null($consult->id_bodega)){
                return $consult->id_bodega;
            }else{
                return "";
            }
        }else{
            return "";
        }

    }

}
