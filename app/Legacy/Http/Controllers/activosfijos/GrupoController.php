<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Plan_Cuentas;

use Sis_medico\AfTipo;
use Sis_medico\AfSubTipo;
use Sis_medico\AfGrupo;

class GrupoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        $grupos = AfGrupo::where('estado','!=',0)->paginate('5');
        $arbol = $this->arbol();
        $tipos      =   AfTipo::all();
        return view('activosfijos/mantenimientos/grupo/index', ['grupos' => $grupos, 'arbol'=>$arbol, 'tipos'=>$tipos]);
    }

    public function arbol()
    {
        $grupos     =   AfGrupo::where('id_padre','=','0')->where('estado','!=',0)->get();
        $arbol      =   "";
        $arbol      .=  "<ul data-widget=\"tree\" >";
        $arbol      .=  "<li >";
        $arbol      .=  "<a class=\"treeview caret-open\" id=\"1\"><i class=\"fa fa-folder-open-o elemento\" aria-hidden=\"true\"></i> <i class=\"fa fa-folder-o oculto elemento2\" aria-hidden=\"true\"></i> ACTIVO</a>";
        $arbol      .=  "<ul class=\"treeview-menu active\">";
        foreach($grupos as $grupo){
            $tipos  =   AfTipo::where('grupo_id','=',$grupo->id)->get();
            if(count($tipos)==0){
                $arbol .= '<li><a id="' . $grupo->id . '" >' . $grupo->nombre . '</a></li>';
            }else{
                $arbol .= '<li><a id="' . $grupo->id . '" class="treeview caret-open"><i class="fa fa-folder-open-o elemento" aria-hidden="true"></i> <i class="fa fa-folder-o oculto elemento2" aria-hidden="true"></i> ' . $grupo->nombre . '</a><ul class="caret-open-menu active">';
                foreach($tipos as $tipo){
                    $arbol      .= '<li><a id="' . $tipo->id . '" class="treeview caret-open"><i class="fa fa-folder-open-o elemento" aria-hidden="true"></i> <i class="fa fa-folder-o oculto elemento2" aria-hidden="true"></i> ' . $tipo->nombre . '</a><ul class="treeview-menu active">';
                    $subtipos   =   AfSubTipo::where('tipo_id','=',$tipo->id)->get();
                    foreach($subtipos as $sub){
                        $arbol .= '<li> <i class="fa fa-circle-o elemento " aria-hidden="true"></i> <a onclick="llamado(this)" id="' . $sub->id . '">' . $sub->nombre . '</a></li>';
                    }
                    $arbol .= '</ul></li>';
                }
                $arbol .= '</ul></li>';
            }
        }
        $arbol      .=  "</ul>";
        $arbol      .=  "</li>";
        $arbol      .=  "</ul>";

        return $arbol;
    }

    public function info(Request $request)
    {
        $id_sub     =   $request['id'];
        $subt       =   AfSubTipo::find($id_sub);
        $tipos      =   AfTipo::all();
        return view('activosfijos/mantenimientos/grupo/informacion', ['subt' => $subt, 'tipos' => $tipos]);
    }

    public function reload()
    {
        echo $this->arbol();
    }

    public function nuevo()
    { 
        $tipos      =   AfTipo::all();
        return view('activosfijos/mantenimientos/grupo/create', ['tipos' => $tipos]);
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        $tipo       =   AfTipo::where('id', '=', $id)->first();
        $plan       =   Plan_Cuentas::all();
        //dd($proveedor);
        // Redirect to user list if updating user wasn't existed
        if ($tipo == null || count($tipo) == 0) {
            return redirect()->intended('/dashboard');
        }
        return view('activosfijos/mantenimientos/grupo/edit', ['tipo' => $tipo, 'plan' => $plan]);
    }

    public function create__(){
    	if($this->rol()){
            return response()->view('errors.404');
        }
        $plan       = Plan_Cuentas::all();
        $codigo     = ""; 
        $codigo     = (AfTipo::max('codigo')+1);
        $codigo     = str_pad($codigo, 2, "0", STR_PAD_LEFT);
        return view('activosfijos/mantenimientos/grupo/create', ['plan' => $plan, 'codigo' => $codigo]);

    }

    private function validateInput($request) {
	    $this->validate($request,[]);
	}

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $sub_id    = null;
        date_default_timezone_set('America/Guayaquil');
        
        if(isset($request['id']))
            $sub_id    = AfSubTipo::where('id', $request['id'])->first();
        
        if(is_null($sub_id)){ 
            $num    =   AfGrupo::max('id'); 
            $num    =   str_pad($num, 2, "0", STR_PAD_LEFT);    
            $tipo   =   str_pad($request['tipo_id'], 2, "0", STR_PAD_LEFT); 
            $codigo =   "$tipo-$num";
            $input = [
                'codigo'                =>  $codigo,
                'nombre'                =>  strtoupper($request['nombre']),
                'tipo_id'               =>  $request['tipo_id'],
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
            ];
            AfSubTipo::create($input);
            $input['id']    =   $num + 1;       
        }else{
            $input = [ 
                'nombre'                =>  strtoupper($request['nombre']),
                'tipo_id'               =>  $request['tipo_id'],
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
                ];

                AfSubTipo::where('id', $request['id'])->update($input);
        }
        //echo "ok";
        return response()->json($input);
    }


    public function update(Request $request, $id)
    {
       
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
 
        date_default_timezone_set('America/Guayaquil');

        $sub_id    = AfSubTipo::where('id', $request['id'])->first();
        
        if(is_null($sub_id)){
            
            AfGrupo::create([
                'codigo'                =>  $request['codigo'],
                'nombre'                =>  strtoupper($request['nombre']),
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
            ]);
            
        }else{
            $input = [
                'codigo'                =>  $request['codigo'],
                'nombre'                =>  strtoupper($request['nombre']),
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
                ];

                AfGrupo::where('id', $request['id'])->update($input);
        }

    }
    
}