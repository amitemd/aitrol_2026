<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Movimiento;	
use DNS1D;
use DNS2D;

class DespachoController extends Controller
{
    protected $redirectTo = '/dashboard';

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
        if(in_array($rolUsuario, array(1, 7)) == false){
          return true;
        }
    }
    public function index()
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return redirect()->intended('/');
        }
        $bodegas =  Bodega::all();
        $proveedores = Proveedor::all();
        //DNS1D::getBarcodeSVG("4445645656", "C39",3,33);

        return view('insumos/despacho/index', ['bodegas' => $bodegas, 'proveedores' => $proveedores]);
    }
}
