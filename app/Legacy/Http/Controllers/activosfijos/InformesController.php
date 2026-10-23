<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input; 

use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\AfFacturaActivoDetalle;
use Sis_medico\AfGrupo;
use Sis_medico\AfTipo;
use Sis_medico\AfActivo;
use Sis_medico\User;
use Sis_medico\Producto;
use Sis_medico\Marca;
use Sis_medico\Empresa;
use Sis_medico\Proveedor;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\AfDepreciacionCabecera;
use Sis_medico\AfDepreciacionDetalle;

class InformesController extends Controller
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

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        // if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
        //     $tipo = $request['tipo'];
        // } else {
        //     $tipo = null;
        // }
        
        $id_empresa = Session::get('id_empresa'); 
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $generados  = $this->getGenerados($fecha, $id_empresa);
        // $activos    = $this->getActivos($tipo, $id_empresa);
        $tipos      = AfTipo::where('estado','!=', 0)->get();
        return view('activosfijos/informes/saldos/index', [ 'empresa' => $empresa, 'fecha' => $fecha,
        'generados' => $generados, 'tipos' => $tipos]);

    }

    public function getGenerados($fecha, $id_empresa)
    {
        $fecha              = str_replace('/', '-', $fecha);
        $timestamp          = \Carbon\Carbon::parse($fecha)->timestamp;
        $fecha              = date('Y-m-d', $timestamp);

        $datos = '[]';
        if ($fecha != null) {
            $datos = AfDepreciacionCabecera::where('fecha', $fecha)
                ->where('estado', 1)
                ->where('id_empresa', $id_empresa)
                ->get();
        }
        // dd($facturas);
        return $datos;
    }

    public function getDetalleGenerados($id)
    {

        $datos = '[]';
        $datos = AfDepreciacionDetalle::where('depreciacion_cabecera_id', $id)
            ->where('estado', 1)
            ->get();
        // dd($facturas);
        return $datos;
    }


    public function getActivos($tipo,$id_empresa)
    {
        $activo = AfActivo::where('estado',1)->where('empresa',$id_empresa);
        if($tipo!=null){
            $activo = $activo->where('tipo_id', $tipo);
        }
        $activo = $activo->get();

        return $activo;
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = null;
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $generados  = $this->getGenerados($fecha, $id_empresa);
        $activos    = $this->getActivos($tipo, $id_empresa);
        $tipos      = AfTipo::where('estado','!=', 0)->get();
        return view('activosfijos/informes/saldos/index', [ 'empresa' => $empresa, 'fecha' => $fecha,
        'generados' => $generados, 'tipos' => $tipos, 'activos'=>$activos]);

    }

    public function show($id)
    {
        $id_empresa = Session::get('id_empresa'); 
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $detalles   = $this->getDetalleGenerados($id);

        $cabecera   = AfDepreciacionCabecera::where('id', $id)->first();

        return view('activosfijos/informes/saldos/show', [ 'empresa' => $empresa, 'detalles' => $detalles]);
    }

    public function codigo_activo($id){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $data = $id;

        $activo = AfActivo::find($id);
        
        $view = \View::make('activosfijos.mantenimientos.activofijo.codigo', compact('data', 'activo'))->render();
        //$pdf = \App::make('dompdf.wrapper');
        //$pdf->loadHTML($view);
        return $view;
    }

}