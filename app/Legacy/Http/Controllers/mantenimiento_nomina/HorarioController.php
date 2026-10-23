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
use Sis_medico\Ct_Rh_Area;
use Sis_medico\Ct_Rh_Estado_Civil;
use Sis_medico\Ct_Rh_Horario;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class HorarioController extends Controller
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
      //  dd("holis");
        $mantenimientos_horarios = Ct_Rh_Horario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(10);
      //dd($mantenimientos_horarios);
        
        return view('mantenimiento_nomina/horario/index', ['mantenimientos_horarios' => $mantenimientos_horarios]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }

      
        return view('mantenimiento_nomina/horario/create');
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Ct_Rh_Horario::create([

            'horario' => $request['horario'],
            'estado'     => $request['estado_horario'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return json_encode("ok");
    }
    public function edit($id)
    {
        $mantenimientos_horarios = Ct_Rh_Horario::where('id', $id)->first();

        return view('mantenimiento_nomina/horario/edit', ['mantenimientos_horarios' => $mantenimientos_horarios, 'id' => $id]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $mantenimientos_horarios = Ct_Rh_Horario::find( $request['id']) ;
        $mantenimientos_horarios->horario          = $request['horario'];
        $mantenimientos_horarios->estado               = $request['estado_horario'];
        $mantenimientos_horarios->ip_creacion          = $ip_cliente;
        $mantenimientos_horarios->ip_modificacion      = $ip_cliente;
        $mantenimientos_horarios->id_usuariocrea       = $idusuario;
        $mantenimientos_horarios->id_usuariomod        = $idusuario;
        $mantenimientos_horarios->save();

        return json_encode('ok');
    }
    
    
}