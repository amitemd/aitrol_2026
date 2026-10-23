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
use Sis_medico\Mantenimientos_Dotacion;

class Mantenimientos_DotacionController extends Controller
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


        $mantenimientos_d = Mantenimientos_Dotacion::paginate(5);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('servicios_generales/mantenimientos_dotacion/index', ['mantenimientos_d' => $mantenimientos_d]);
    }
    
    public function crear()
    {


        return view('servicios_generales/mantenimientos_dotacion/create');
    }

    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Mantenimientos_Dotacion::create([

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
        $mantenimientos_d = Mantenimientos_Dotacion::where('id', $id)->first();

        return view('servicios_generales/mantenimientos_dotacion/edit', ['mantenimientos_d' => $mantenimientos_d, 'id' => $id]);
    }

    public function actualizar(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $mantenimientos_d = Mantenimientos_Dotacion::where('id', $request['id'])->first();
        $mantenimientos_d->nombre               = $request['nombre'];
        $mantenimientos_d->descripcion          = $request['descripcion'];
        $mantenimientos_d->estado               = $request['estado'];
        $mantenimientos_d->ip_creacion          = $ip_cliente;
        $mantenimientos_d->ip_modificacion      = $ip_cliente;
        $mantenimientos_d->id_usuariocrea       = $idusuario;
        $mantenimientos_d->id_usuariomod        = $idusuario;
        $mantenimientos_d->save();

        return json_encode('ok');
    }
  
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
