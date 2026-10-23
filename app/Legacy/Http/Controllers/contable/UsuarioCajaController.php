<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Empresa;
use Sis_medico\Ct_Ciudad;


class UsuarioCajaController extends Controller
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

    
    /************************************************
    ********LISTADO DE USUARIO PUNTO EMISION ********
    /************************************************/
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        //$sucur = Ct_Sucursales::where('estado', '!=', null)->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);

           
        return view('contable.usuario_caja.index', ['empresa' => $empresa]); 
    }
    

    /*************************************************
    ****CREAR NUEVO ESTABLECIMIENTOS (SUSCURSALES)****
    /*************************************************/
    /*public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $empresas = Empresa::where('estado', '1')->get();
        $ciudad = Ct_Ciudad::where('estado', '1')->get();


        return view('contable.sucursales.create', ['empresas' => $empresas,'ciudad' => $ciudad,'empresa' => $empresa]); 
    }*/

    /*************************************************
    ********GUARDA ESTABLECIMIENTOS (SUSCURSALES)*****
    /*************************************************/    
    /*public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Sucursales::create([
            
            'codigo_sucursal'           => $request['codigo'],
            'nombre_sucursal'           => $request['nombre'],
            'id_ciudad'                 => $request['id_ciudad'],
            'direccion_sucursal'        => $request['direccion'],
            'email_sucursal'            => $request['email'],
            'telefono_sucursal'         => $request['telefono'],
            'estado'                    => $request['estado'],
            'id_empresa'                => $request['id_empresa'],
            'id_usuariomod'             => $idusuario,
            'id_usuariocrea'            => $idusuario,
            'ip_modificacion'           => $ip_cliente,
            'ip_creacion'               => $ip_cliente,
           
        ]); 

        return redirect()->route('establecimiento.index');
    }*/

    /*************************************************
    ********EDITAR ESTABLECIMIENTOS (SUSCURSALES)*****
    /*************************************************/
   /* public function editar($id,$id_empresa)
    {
        
        $sucursal = Ct_Sucursales::findorfail($id);
        $empresa  = Empresa::where('id', $id_empresa)->first();
        $empresas = Empresa::where('estado', '1')->get();
        $ciudad = Ct_Ciudad::where('estado', '1')->get();

        return view('contable.sucursales.edit',['sucursal' => $sucursal, 'empresas'=> $empresas,'ciudad'=> $ciudad,'empresa' => $empresa]);
    }*/

    /*************************************************
    ******ACTUALIZA ESTABLECIMIENTOS (SUSCURSALES)****
    /*************************************************/    
    /*public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['id_estab'];
        $establecimiento = Ct_Sucursales::findOrFail($id);
        
        $input = [

            'codigo_sucursal'           => $request['codigo'],
            'nombre_sucursal'           => $request['nombre'],
            'id_ciudad'                 => $request['ciud'],
            'direccion_sucursal'        => $request['direccion'],
            'email_sucursal'            => $request['email'],
            'telefono_sucursal'         => $request['telefono'],
            'estado'                    => $request['estado'],
            'id_empresa'                => $request['id_empresa'],
            'id_usuariomod'             => $idusuario,
            'id_usuariocrea'            => $idusuario,
            'ip_modificacion'           => $ip_cliente,
            'ip_creacion'               => $ip_cliente,
            
        ]; 


        $establecimiento->update($input); 
        
        return redirect()->route('establecimiento.index');
    }*/

  
    /*************************************************
    ************BUSCAR CODIGO PUNTO EMISION***********
    /*************************************************/  
    /*public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo_sucursal'  => $request['buscar_codigo'],
            'nombre_sucursal'  => $request['buscar_nombre'],
        ];
        $sucur = $this->doSearchingQuery($constraints);
        return view('contable.sucursales.index', ['request' => $request, 'sucur' => $sucur, 'searchingVals' => $constraints]);
    
    }*/

    /*************************************************
    ******************CONSULTA QUERY******************
    /*************************************************/  
    /*private function doSearchingQuery($constraints)
    {

        $query  = Ct_Sucursales::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }*/




}
