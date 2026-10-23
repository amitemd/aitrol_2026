<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;

class PorcentajeRetencionController extends Controller
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

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $porc_reten = Ct_Porcentaje_Retenciones::where('estado', '=', 1)->orderby('codigo', 'asc')->paginate(20);

        return view('contable.porcentaje_retencion.index', ['porc_reten' => $porc_reten]);
    }
    public function create()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tipo = Plan_Cuentas::all();

        return view('contable.porcentaje_retencion.create', ['tipo' => $tipo]);
    }

    public function find_cta_retencion(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $buscador = $request['search'];
        $cuentas  = [];
        if ($buscador != null) {
            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
                ->where('pe.id_empresa', $id_empresa)
                ->where('pe.estado', '2');

            $cuentas = $cuentas->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('pe.nombre', "LIKE", "%{$buscador}%")
                    ->orwhere('pe.plan', 'LIKE', "%{$buscador}%");
            });

            $cuentas = $cuentas->select('pe.id_plan as id', DB::raw('CONCAT(pe.plan," | ",pe.nombre) as text'))->get();
        }


        return response()->json($cuentas);
    }



    public function guardar_porcentaje_retencion(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Ct_Porcentaje_Retenciones::create([
            'codigo'          => $request['codigo'],
            'codigo_interno'  => $request['codigo_interno'],
            'nombre'          => $request['nombre'],
            'tipo'            => $request['tipo'],
            'valor'           => $request['valor'],
            //'cuenta_acreedores'     => $request['cta_acreedores'],
            'cuenta_clientes' => $request['cuenta_clientes'],
            'nota'            => $request['nota'],
            'estado'          => $request['estados'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ]);

        return redirect()->intended('/contable/porcentajeretencion');
    }

    public function contable_index()
    {
        $contable = Ct_Porcentaje_Retenciones::all();
        return view('contable.porcentaje_retencion.index', ['contable' => $contable]);
    }

    public function edit($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $retenciones = Ct_Porcentaje_Retenciones::find($id);
        $tipo        = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')
            ->where('pe.id_empresa', $id_empresa)
            ->where('pe.estado', '2')
            ->select('pe.id_plan as id', 'pe.nombre as nombre', 'pe.plan')
            ->get();
        return view('contable.porcentaje_retencion.edit', ['retenciones' => $retenciones, 'tipo' => $tipo]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        Ct_Porcentaje_Retenciones::create([
            'codigo'            => $request['codigo'],
            'codigo_interno'  => $request['codigo_interno'],
            'nombre'            => $request['nombre'],
            'tipo'              => $request['tipo'],
            'valor'             => $request['valor'],
            'cuenta_acreedores' => $request['cuenta_acreedores'],
            'cuenta_deudora'    => $request['cuenta_deudora'],
            'cuenta_clientes'   => $request['cuenta_clientes'],
            'nota'              => $request['nota'],
            'estado'            => $request['estados'],
            'id_usuariomod'     => $idusuario,
            'id_usuariocrea'    => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'ip_creacion'       => $ip_cliente,

        ]);

        return redirect()->intended('/contable/porcentaje_retencion');
        //return Redirect::to('formulario.registro')->withInput();

    }
    public function update(Request $request, $id)
    {
        //return $request->all();

        $retenciones = Ct_Porcentaje_Retenciones::findOrFail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $input = [
            'codigo'            => $request['codigo'],
            'codigo_interno'  => $request['codigo_interno'],
            'nombre'            => $request['nombre'],
            'tipo'              => $request['tipo'],
            'valor'             => $request['valor'],
            'cuenta_acreedores' => $request['cuenta_acreedores'],
            'cuenta_clientes'   => $request['cuenta_clientes'],
            'cuenta_deudora'    => $request['cuenta_deudora'],
            'nota'              => $request['nota'],
            'estado'            => $request['estados'],
            'id_usuariomod'     => $idusuario,
            'id_usuariocrea'    => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'ip_creacion'       => $ip_cliente,

        ];

        $retenciones->update($input);

        return redirect()->intended('contable/porcentaje_retencion');
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo' => $request['buscar_codigo'],
            'nombre' => $request['buscar_nombre'],
            'estado' => 1,
        ];

        $porc_reten = $this->doSearchingQuery($constraints);
        return view('contable.porcentaje_retencion.index', ['request' => $request, 'porc_reten' => $porc_reten, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Porcentaje_Retenciones::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }
}
