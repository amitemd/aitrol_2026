<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Illuminate\Support\Facades\Session;

class CajaBancoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    /************************************************
     ******************CAJA Y BANCO*******************
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $caja_banco = Ct_caja_banco::where('estado', '=', 1)->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);

        return view('contable.caja_banco.index', ['caja_banco' => $caja_banco, 'empresa' => $empresa]);
    }

    /*************************************************
     *****************CREAR CAJA Y BANCO***************
    /*************************************************/
    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        //$plan_cuenta = Plan_cuentas::where('estado','1')->get();
        $cuenta_caja_cj = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_CAJA'); 
        $cuenta_banco_cj = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_BANCOS'); 
        $cuenta_obfinlo = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_BANCOSLOCALES'); 
       //dd($cuenta_obfinlo);

        $plan_cuenta = Plan_Cuentas::orWhere('id', $cuenta_caja_cj->cuenta_guardar)
            ->orWhere('id', $cuenta_banco_cj->cuenta_guardar)
            ->orWhere('id', $cuenta_obfinlo->cuenta_guardar)->get();

        $divisas = Ct_Divisas::where('estado', 1)->get();

        $form_pago = Ct_Tipo_Pago::where('estado', 1)->get();

        return view('contable.caja_banco.create', ['empresa' => $empresa, 'plan_cuenta' => $plan_cuenta, 'divisas' => $divisas, 'sucursales' => $sucursales, 'form_pago' => $form_pago]);

    }

    /*************************************************
     ********OBTENER DETALLE GRUPO PLAN DE CUENTA******
    /*************************************************/
    public function obtener_detalle_grupo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $codigo = $request['opcion'];

        if (!is_null($codigo)) {
            $id_empresa = Session::get('id_empresa');
/*             if($id_empresa == "1391707460001"){
                $id_padre = Plan_Cuentas::where('id', "1.01.01.2.02")->get();
            }else{
                $id_padre = Plan_Cuentas::where('id_padre', $codigo)->get();
            } */
            $id_padre= Plan_Cuentas::where('p.estado','>',0)->join('plan_cuentas_empresa as p','p.id_plan','plan_cuentas.id')->where('p.id_empresa',$id_empresa)->where('p.id_padre',$codigo)->select('plan_cuentas.id as id','p.nombre as nombre')->get();
            

            return $id_padre;
        }

        return 'no';
    }

    /*************************************************
     ****************GUARDA CAJA BANCO*****************
    /*************************************************/

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');

        Ct_caja_banco::create([

            'codigo'          => $request['cod_caja_banco'],
            'nombre'          => $request['nombre_caja_banco'],
            'numero_cuenta'   => $request['numero_cuenta'],
            'clase'           => $request['clase'],
            'grupo'           => $request['grupo_plan_cuenta'],
            'cuenta_mayor'    => $request['detalle_grupo'],
            'sucursal'        => $request['nomb_sucursal'],
            'divisa'          => $request['divisa'],
            'formas_pago'     => $request['forma_pago'],
            'ultimo_cheque'   => $request['ultimo_cheque'],
            'comentarios'     => $request['comentario'],
            'estado'          => $request['estado_caj_banco'],
            'id_empresa'      => $id_empresa,
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ]);

        return redirect()->route('caja_banco.index');
    }

    /*************************************************
     ***************EDITAR CAJA BANCO******************
    /*************************************************/

    public function editar($id, $id_empresa)
    {
        $caja_banco = Ct_Caja_Banco::findorfail($id);
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        $divisas = Ct_Divisas::where('estado', 1)->get();

        $form_pago = Ct_Tipo_Pago::where('estado', 1)->get();
        $cuenta_caja_cj = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_CAJA'); 
        $cuenta_banco_cj = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_BANCOS'); 
        $cuenta_obfinlo = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJA_BAN_BANCOSLOCALES'); 
       
       //dd($cuenta_banco_cj);   

        $plan_cuenta = Plan_Cuentas::orWhere('id', $cuenta_caja_cj->cuenta_guardar)
            ->orWhere('id', $cuenta_banco_cj->cuenta_guardar)
            ->orWhere('id', $cuenta_obfinlo->cuenta_guardar)->get();

        
     
    
        $cuentas_padres = Plan_Cuentas::where('id_padre', $caja_banco->grupo)->get();

        //dd($plan_cuenta);
        return view('contable.caja_banco.edit', ['caja_banco' => $caja_banco, 'empresa' => $empresa, 'plan_cuenta' => $plan_cuenta, 'sucursales' => $sucursales, 'divisas' => $divisas, 'form_pago' => $form_pago, 'cuentas_padres' => $cuentas_padres]);
    }

    /*************************************************
     *************ACTUALIZA CAJA Y BANCO***************
    /*************************************************/
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id         = $request['id_caja_banco'];
        $caja_banco = Ct_Caja_Banco::findOrFail($id);

        $id_empresa = $request->session()->get('id_empresa');

        $input = [

            'codigo'          => $request['cod_caja_banco'],
            'nombre'          => $request['nombre_caja_banco'],
            'numero_cuenta'   => $request['numero_cuenta'],
            'clase'           => $request['clase'],
            'grupo'           => $request['grupo_plan_cuenta'],
            'cuenta_mayor'    => $request['detalle_grupo'],
            'sucursal'        => $request['nomb_sucursal'],
            'divisa'          => $request['divisa'],
            'formas_pago'     => $request['forma_pago'],
            'ultimo_cheque'   => $request['ultimo_cheque'],
            'comentarios'     => $request['comentario'],
            'estado'          => $request['estado_caj_banco'],
            'id_empresa'      => $id_empresa,
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ];

        $caja_banco->update($input);

        return redirect()->route('caja_banco.index');

    }

    /*************************************************
     *****************BUSCAR CAJA Y BANCO**************
    /*************************************************/
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'codigo'     => $request['buscar_codigo'],
            'nombre'     => $request['buscar_nombre'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,
        ];
        $caja_banco = $this->doSearchingQuery($constraints);
        return view('contable.caja_banco.index', ['request' => $request, 'caja_banco' => $caja_banco, 'searchingVals' => $constraints, 'empresa' => $empresa]);

    }

    /*************************************************
     *********CONSULTA QUERY (CAJA Y BANCO)************
    /*************************************************/
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Caja_Banco::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

}
