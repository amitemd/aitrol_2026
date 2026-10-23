<?php

namespace Sis_medico\Http\Controllers\mantenimiento_nomina;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Nivel_Academico;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class NivelAcademicoController extends Controller
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
        if (in_array($rolUsuario, array(1, 8,  19, 20, 21, 22)) == false) {
            return true;
        }
    }



    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $niveles_academicos = Ct_Rh_Nivel_Academico::where('estado', '!=', null)->orderby('id', 'desc')->paginate(10);
        
        return view('mantenimiento_nomina/nivel_academico/index', ['niveles_academicos' => $niveles_academicos]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        return view('mantenimiento_nomina/nivel_academico/create');
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Ct_Rh_Nivel_Academico::create([

            'descripcion' => $request['descripcion'],
            'estado'     => $request['estado_nv'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return json_encode("ok");
    }
    public function edit($id)
    {
        $niveles_academicos = Ct_Rh_Nivel_Academico::where('id', $id)->first();

        return view('mantenimiento_nomina/nivel_academico/edit', ['niveles_academicos' => $niveles_academicos, 'id' => $id]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $niveles_academicos = Ct_Rh_Nivel_Academico::find($request['id']);
        $niveles_academicos->descripcion          = $request['descripcion'];
        $niveles_academicos->estado               = $request['estado_nv'];
        $niveles_academicos->ip_creacion          = $ip_cliente;
        $niveles_academicos->ip_modificacion      = $ip_cliente;
        $niveles_academicos->id_usuariocrea       = $idusuario;
        $niveles_academicos->id_usuariomod        = $idusuario;
        $niveles_academicos->save();

        return json_encode('ok');
    }
    
    
}