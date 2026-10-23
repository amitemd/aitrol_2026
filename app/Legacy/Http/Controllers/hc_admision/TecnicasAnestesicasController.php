<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\TecnicasAnestesicas;

class TecnicasAnestesicasController extends Controller
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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 4, 5, 6,11,7)) == false){
          return true;
        }
    }

    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $TecnicasAnestesicas = TecnicasAnestesicas::paginate(25);
      

        return view('hc_admision/tecnicas_anestesicas/index', ['TecnicasAnestesicas' => $TecnicasAnestesicas]);
    }
}
