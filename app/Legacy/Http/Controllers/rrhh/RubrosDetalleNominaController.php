<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\CtRhRubrosDetalleNomina;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;

class RubrosDetalleNominaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }

    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $rubrosDetalles = CtRhRubrosDetalleNomina::paginate(25);
        return view('rrhh/rubros_detalles/index', ['rubrosDetalles' => $rubrosDetalles]);
    }

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('rrhh/rubros_detalles/create');
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        //dd($id_empresa);
        //$this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        //dd($request->all());
        CtRhRubrosDetalleNomina::create([
            'descripcion' => $request['descripcion'],
            'descripcion_cuenta' => $request['descripcion_cuenta'],
            'ingreso_egreso' => $request['ingreso_egreso'],
            'debe_haber' => $request['debe_haber'],
            'editable' => $request['editable'],
            'marca' => $request['marca'],
            'id_empresa' => $id_empresa,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);
        return ['status' => 'success', 'msj' => 'Guardado con exito'];
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $rubrosDetalleNomina = CtRhRubrosDetalleNomina::find($id);
        return view('rrhh/rubros_detalles/edit', ['rubrosDetalleNomina' => $rubrosDetalleNomina]);
    }

    public function update(Request $request)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $rubrosDetalleNomina = CtRhRubrosDetalleNomina::findOrFail($request['id']);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        date_default_timezone_set('America/Guayaquil');
        $input = [
            'descripcion' => $request['descripcion'],
            'descripcion_cuenta' => $request['descripcion_cuenta'],
            'ingreso_egreso' => $request['ingreso_egreso'],
            'debe_haber' => $request['debe_haber'],
            'editable' => $request['editable'],
            'marca' => $request['marca'],
            'id_empresa' => $id_empresa,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ];
        $rubrosDetalleNomina->update($input);
        return ['status' => 'success', 'msj' => 'Actualizado con exito'];
    }

    public function asignarCuenta($id)
    {
        $id_empresa = session('id_empresa');
        if($this->rol()){
            return response()->view('errors.404');
        }
        $rubrosDetalleNomina = CtRhRubrosDetalleNomina::find($id);
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
        ->where('pe.id_empresa', $id_empresa)
        ->where('plan_cuentas.estado', '=', 2)
        ->select('plan_cuentas.*')
        ->get();
        return view('rrhh/rubros_detalles/asignar_cuenta', ['rubrosDetalleNomina' => $rubrosDetalleNomina, 'cuentas' => $cuentas]);
    }

    public function storeCuenta(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        if($this->rol()){
            return response()->view('errors.404');
        }
        $rubrosDetalleNomina = CtRhRubrosDetalleNomina::find($request['id']);
        $cuenta_empresa = Plan_Cuentas_Empresa::where('id_plan',$request['cuentas'])->where('id_empresa',$id_empresa)->first();
        $plan_cuentas = Plan_Cuentas::find($request['cuentas']);
        // dd($rubrosDetalleNomina,$cuenta,$request->all(),$plan_cuentas);
        $id_configuracion = Ct_Configuraciones::insertGetId([
            'id_plan' => $cuenta_empresa->id_plan,
            'nombre' => $plan_cuentas->nombre,
            'id_empresa' => $id_empresa,
            'tipo' => $rubrosDetalleNomina->descripcion_cuenta,
            'modulo' => $rubrosDetalleNomina->descripcion_cuenta,
            'cuenta_ant' => $cuenta_empresa->id_plan,
            'iva' => null,
            'ice' => null,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);
        $rubrosDetalleNomina->update(['id_ct_confirguraciones' => $id_configuracion]);
        return ['status' => 'success', 'msj' => 'Cuenta asignada con exito'];
    }

}
 