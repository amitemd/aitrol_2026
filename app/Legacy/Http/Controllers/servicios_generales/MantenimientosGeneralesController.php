<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\User;
use Sis_medico\Limpieza;
use Sis_medico\Sala;
use Sis_medico\Agenda;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\Limpieza_Banos;
use Sis_medico\Mantenimientos_Generales;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Inv_Carga_Inventario;


class MantenimientosGeneralesController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }


    public function index()
    {


        $mantenimientos_g = Mantenimientos_Generales::paginate(5);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('servicios_generales/mantenimientos/index', ['mantenimientos_g' => $mantenimientos_g]);
    }
    public function crear()
    {


        return view('servicios_generales/mantenimientos/create');
    }

    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Mantenimientos_Generales::create([

            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return json_encode("ok");
    }
    public function editar($id)
    {
        $mantenimientos_g = Mantenimientos_Generales::where('id', $id)->first();

        return view('servicios_generales/mantenimientos/edit', ['mantenimientos_g' => $mantenimientos_g, 'id' => $id]);
    }

    public function actualizar(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $mantenimientos_g = Mantenimientos_Generales::where('id', $request['id'])->first();
        $mantenimientos_g->nombre               = $request['nombre'];
        $mantenimientos_g->descripcion          = $request['descripcion'];
        $mantenimientos_g->estado               = $request['estado'];
        $mantenimientos_g->ip_creacion          = $ip_cliente;
        $mantenimientos_g->ip_modificacion      = $ip_cliente;
        $mantenimientos_g->id_usuariocrea       = $idusuario;
        $mantenimientos_g->id_usuariomod        = $idusuario;
        $mantenimientos_g->save();

        return json_encode('ok');
    }
    /* public function buscar_piso(Request $request) {
         $control_piso = $request['control_piso'];
        return view('servicios_generales/mantenimientos/index', ['control_piso' => $control_piso]);*/


   /* public function actualizar_inventario()
    {
        $actualizar_campo = Inv_Carga_Inventario::get();


        foreach ($actualizar_campo as $val) {


            if ($val->bodega == 2) {


                $arr = [

                    'lugar' => 'COMPRAS',
                ];


                Inv_Carga_Inventario::where('id', $val->id)->update($arr);
            }
        }
    }*/
    public function actualizar_bodega()
    {
        $actualizar_b = Inv_Carga_Inventario::get();


        foreach ($actualizar_b as $val) {


            if (is_null($val->bodega) && $val->bodega != 2) {


                $arr = [

                    'bodega' => '1',
                ];


                Inv_Carga_Inventario::where('id', $val->id)->update($arr);
            }
        }
    }
}
