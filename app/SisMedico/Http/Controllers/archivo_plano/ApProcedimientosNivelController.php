<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApProcedimientoNivel;


class ApProcedimientosNivelController extends Controller
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
        $procedimientos = DB::table('ap_procedimiento')
            ->select('tipo','codigo','nombre','descripcion')
            ->join('posts', 'ap_procedimiento.id', '=', 'ap_procedimiento_nivel.id_procedimiento')
            ->get();
        $procedimientos = ApProcedimiento::paginate(5);
        return view('archivo_plano/procedimientos/index',['procedimientos' =>$procedimientos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
}
