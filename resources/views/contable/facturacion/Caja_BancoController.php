<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Plan_cuentas;
use Sis_medico\Ct_Sucursal_Banco;
use Sis_medico\Http\Controllers\Controller;

class Caja_BancoController extends Controller
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

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $caj_ban = Ct_caja_banco::paginate(10);


        return view('contable.caja_banco.index', ['caj_ban' => $caj_ban]); //*Aqui va la ruta de la carpeta*
    }
    public function crear()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $divisas = Ct_Divisas::where('estado', 1)->get();
        $plan = Plan_Cuentas::where('id_padre', '1.01.01')->get();
        $sucursal = Ct_Sucursal_Banco::all();



        return view('contable.caja_banco.create', ['divisas' => $divisas, 'plan' => $plan, 'sucursal' => $sucursal]); //*Aqui va la ruta de la carpeta*
    }

    public function guardar_caja_banco(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Ct_caja_banco::create([
            'codigo'        => $request['codigo'],
            'nombre'       => $request['nombre'],
            'numero_de_cuenta' => $request['numero'],
            'tipo'           => $request['tipos'],
            'clase'         => $request['clases'],
            'grupo'         => $request['grupos'],
            'cta_mayor'     => $request['ctamayor'],
            'sucursal'      => $request['sucursales'],
            'divisa'        => $request['divisas'],
            'www'           => $request['web'],
            'formas_de_pago' => $request['formasdepago'],
            'comentarios'    => $request['comen'],
            'estado'        => $request['estados'],
            'id_usuariomod'         => $idusuario,
            'id_usuariocrea'        => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'ip_creacion'           => $ip_cliente,

        ]);

        return redirect()->intended('contable/caja_banco');
        //return Redirect::to('formulario.registro')->withInput();

    }


    public function contable_index()
    {
        $contable = Ct_caja_banco::all();
        return view('contable.caja_banco.index', ['contable' => $contable]);
    }

    public function query_cuentas(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];

        if (!is_null($codigo)) {
            $id_padre = Plan_Cuentas::where('id_padre', $codigo)->get();

            return $id_padre;
        }
        return 'no';
    }
    public function edit($id)
    {
        $caja = Ct_caja_banco::find($id);
        $divisas = Ct_Divisas::where('estado', 1)->get();
        $plan = Plan_Cuentas::where('id_padre', '1.01.01')->get();
        $sucursal = Ct_Sucursal_Banco::all();

        //dd($plan);

        return view('contable.caja_banco.edit', ['caja' => $caja, 'divisas' => $divisas, 'plan' => $plan, 'sucursal' => $sucursal]);
    }
    public function update(Request $request, $id)
    {
        //return $request->all();

        $caja = Ct_caja_banco::findOrFail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $input = [
            'codigo'        => $request['codigo'],
            'nombre'       => $request['nombre'],
            'numero_de_cuenta' => $request['numero'],
            'tipo'           => $request['tipos'],
            'clase'         => $request['clases'],
            'grupo'         => $request['grupos'],
            'cta_mayor'     => $request['ctamayor'],
            'sucursal'      => $request['sucursales'],
            'divisa'        => $request['divisas'],
            'www'           => $request['web'],
            'formas_de_pago' => $request['formasdepago'],
            'comentarios'    => $request['comen'],
            'estado'        => $request['estados'],
            'id_usuariomod'         => $idusuario,
            'id_usuariocrea'        => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'ip_creacion'           => $ip_cliente,

        ];


        $caja->update($input);


        return redirect()->intended('contable/caja_banco');
    }
}
